<?php
namespace keeko\framework\security;

use DeviceDetector\DeviceDetector;
use Geocoder\Provider\FreeGeoIp;
use Geocoder\Provider\GeoPlugin;
use Geocoder\ProviderAggregator;
use Ivory\HttpAdapter\CurlHttpAdapter;
use keeko\core\model\Session;
use keeko\core\model\SessionQuery;
use keeko\core\model\User;
use keeko\core\model\UserQuery;
use keeko\framework\preferences\SystemPreferences;
use keeko\framework\service\ServiceContainer;
use phootwork\lang\ArrayObject;
use Symfony\Component\HttpFoundation\Request;

class AuthManager {
	
	/**
	 * @var User
	 */
	private $user;
	
	/** @var Session */
	private $session;

	private $recognized = false;
	
	private $authenticated = false;
	
	/** @var SerivceContainer */
	private $service;
	
	public function __construct(ServiceContainer $service) {
		$this->service = $service;
		$this->user = $this->getGuest();
		
		$request = Request::createFromGlobals();
		$strategies = ['header', 'basic', 'cookie'];

		foreach ($strategies as $strategy) {
			$method = 'auth' . ucfirst($strategy);
			$session = $this->$method($request);
			if ($session !== null) {
				$this->session = $session;
				$this->user = $session->getUser();
				$this->recognized = true;
				
				// update session
				$session->setIp(null);
				$session->setIp($request->getClientIp());
				$session->save();			
				break;
			}
		}
	}
	
	private function getGuest() {
		return UserQuery::create()->findOneById(-1);
	}
	
	/**
	 * Authenticates a user by a token in a cookie
	 * 
	 * @param Request $request
	 * @return Session|null
	 */
	private function authCookie(Request $request) {
		if ($request->cookies->has('Bearer')) {
			$bearer = $request->cookies->get('Bearer');
			return $this->authToken($bearer);
		}
		return null;
	}

	/**
	 * Authenticates a user by an authorization header
	 * 
	 * @param Request $request
	 * @return Session|null
	 */
	private function authHeader(Request $request) {
		if ($request->headers->has('authorization')) {
			$auth = $request->headers->get('authorization');
			if (!empty($auth)) {
				list(, $bearer) = explode(' ', $auth);
				return $this->authToken($bearer);
			}
		}
		return null;
	}
	
	/**
	 * Authenticates a user with a token
	 * 
	 * @param string $token
	 * @return Session|null
	 */
	private function authToken($token) {
		return SessionQuery::create()->findOneByToken($token);
	}
	
	/**
	 * Authenticates a user by basic authentication
	 * 
	 * @param Request $request
	 * @return Session|null
	 */
	private function authBasic(Request $request) {
		$user = $this->findUser($request->getUser());
		if ($user !== null && $this->verifyUser($user, $request->getPassword())) {
			$session = $this->findSession($user);
			if ($session === null) {
				$session = $this->createSession($user);
			}
			$this->authenticated = true;
			
			return $session;
		}
		return null;
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
	
	public function verifyUser(User $user, $password) {
		return password_verify($password, $user->getPassword());
	}
	
	public function encryptPassword($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}
	
	/**
	 * 
	 * @param string $login
	 * @return User|null
	 */
	public function findUser($login) {
		$query = UserQuery::create();
		$prefs = $this->service->getPreferenceLoader()->getSystemPreferences();
		$mode = $prefs->getUserLogin();
		
		// login with username
		if ($mode == SystemPreferences::LOGIN_USERNAME) {
			$query = $query->filterByUserName($login);
		}
		
		// login with email
		else if ($mode == SystemPreferences::LOGIN_EMAIL) {
			$query = $query->filterByEmail($login);
		} 
		
		// login with username or email
		else if ($mode == SystemPreferences::LOGIN_USERNAME_EMAIL) {
			$query = $query->filterByEmail($login)->_or()->filterByUserName($login);
		}
		
		// no mode found, return null
		else {
			return null;
		}

		return $query->findOne();
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
