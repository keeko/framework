<?php
namespace keeko\framework\security;

use keeko\core\model\Session;
use keeko\core\model\SessionQuery;
use keeko\core\model\User;
use keeko\core\model\UserQuery;
use Symfony\Component\HttpFoundation\Request;
use DeviceDetector\DeviceDetector;
use Ivory\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\FreeGeoIp;
use Geocoder\Provider\GeoPlugin;
use Geocoder\ProviderAggregator;
use phootwork\lang\ArrayObject;

class AuthManager {
	
	/**
	 * @var User
	 */
	private $user;
	
	/** @var Session */
	private $session;
	
	private $recognized = false;
	
	private $authenticated = false;
	
	public function __construct() {
		$this->user = $this->getGuest();
		
		$request = Request::createFromGlobals();
		$strategies = ['header', 'basic', 'cookie'];

		foreach ($strategies as $strategy) {
			$method = 'auth' . ucfirst($strategy);
			if ($this->$method($request)) {
				break;
			}
		}
	}
	
	private function getGuest() {
		return UserQuery::create()->findOneById(-1);
	}
	
	private function authCookie(Request $request) {
		if ($request->cookies->has('Bearer')) {
			$bearer = $request->cookies->get('Bearer');
			return $this->authToken($bearer);
		}
		return false;
	}

	private function authHeader(Request $request) {
		if ($request->headers->has('authorization')) {
			$auth = $request->headers->get('authorization');
			if (!empty($auth)) {
				list(, $bearer) = explode(' ', $auth);
				return $this->authToken($bearer);
			}
		}
		return false;
	}
	
	private function authToken($token) {
		$session = SessionQuery::create()->findOneByToken($token);

		if ($session !== null) {
			$this->session = $session;
			$this->user = $session->getUser();
			$this->recognized = true;
			$this->authenticated = true;
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param Request $request
	 */
	private function authBasic(Request $request) {
		$user = $this->findUser($request->getUser());
		if ($user !== null && $this->verifyUser($user, $request->getPassword())) {
			$session = $this->findSession($user);
			if ($session === null) {
				$session = $this->createSession($user);
			}

			$this->session = $session;
			$this->user = $user;
			$this->recognized = true;
			$this->authenticated = true;
			
			return true;
		}
		return false;
	}
	
	/**
	 * TODO: Probably not the best location/method-name and response (throw an exception?)
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	public function login($login, $password) {
		$user = $this->findUser($login);

		if ($user) {
			$this->user = $user;
			$this->recognized = true;
			
			if ($this->verifyUser($user, $password)) {
				$this->authenticated = true;

				$session = $this->findSession($user);
				if ($session === null) {
					$session = $this->createSession($user);
				}
				
				$this->session = $session;
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @param User $user
	 * @return Session
	 */
	private function createSession(User $user) {
		$request = Request::createFromGlobals();
		$ua = $request->headers->get('User-Agent');
		$detector = new DeviceDetector($ua);
		$detector->skipBotDetection(true);
		$detector->parse();

		$session = new Session();
		$session->setToken(self::generateToken());
		$session->setUser($user);
		$session->setBrowser($detector->getClient('name'));
		$session->setOs($detector->getOs('name'));
		$session->setDevice($detector->getDeviceName());
		$session->setLocation($this->getLocation());
		$session->save();
		
		return $session;
	}
	
	/**
	 * 
	 * @param User $user
	 * @return Session|null
	 */
	private function findSession(User $user) {
		$request = Request::createFromGlobals();
		$ua = $request->headers->get('User-Agent');
		$detector = new DeviceDetector($ua);
		$detector->skipBotDetection(true);
		$detector->parse();

		return SessionQuery::create()
			->filterByUserId($user->getId())
			->filterByBrowser($detector->getClient('name'))
			->filterByOs($detector->getOs('name'))
			->findOne();
	}
	
	private function verifyUser(User $user, $password) {
		return password_verify($password, $user->getPassword());
	}
	
	/**
	 * 
	 * @param string $login
	 * @return User|null
	 */
	private function findUser($login) {
		return UserQuery::create()->filterByLoginName($login)->findOne();
	}

	public function logout(User $user = null) {
		if ($user === null) {
			$user = $this->user;
		}
		$session = $this->findSession($user);
		$session->delete();

		$this->user = $this->getGuest();
		$this->recognized = false;
		$this->authenticated = false;
		$this->session = null;
		
		return true;
	}

	public static function generateToken() {
		return md5(uniqid(mt_rand(), true));
	}

	public function getUser() {
		return $this->user;
	}
	
	public function getSession() {
		return $this->session;
	}
	
	public function isRecognized() {
		return $this->recognized;
	}
	
	public function isAuthenticated() {
		return $this->authenticated;
	}
	
	private function getLocation() {
		$request = Request::createFromGlobals();
		$adapter  = new CurlHttpAdapter();
		$geocoder = new ProviderAggregator();
		$geocoder->registerProviders([
			new FreeGeoIp($adapter),
			new GeoPlugin($adapter)
		]);
		
		$location = 'n/a';
		
		try {
			$result = $geocoder->geocode($request->getClientIp());
			if ($result->count()) {
				$address = $result->first();
				$state = $address->getAdminLevels()->first();
				$state = $state !== null ? $state->getName() : '';
				$parts = new ArrayObject([
					$address->getCountry()->getName(),
					$state,
					$address->getLocality()
				]);
				$parts = $parts->filter(function ($elem) {
					return !empty($elem);
				});
				$loc = $parts->join(', ')->trim(' ,')->toString();
				if (!empty($loc)) {
					$location = $loc;
				}
			}
		} catch (\Exception $e) {}
		
		return $location;
	}
}
