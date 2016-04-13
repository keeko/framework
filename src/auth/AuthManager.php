<?php
namespace keeko\framework\auth;

use keeko\core\model\Session;
use keeko\core\model\SessionQuery;
use keeko\core\model\User;
use keeko\core\model\UserQuery;
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
	 * TODO: Creates a new session, every request...
	 * @param Request $request
	 */
	private function authBasic(Request $request) {
		return $this->login($request->getUser(), $request->getPassword());
	}
	
	/**
	 * TODO: Probably not the best location/method-name and response (throw an exception?)
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	public function login($username, $password) {
		$user = UserQuery::create()->filterByLoginName($username)->findOne();

		if ($user) {
			$this->user = $user;
			$this->recognized = true;
			
			if (password_verify($password, $user->getPassword())) {
				$this->authenticated = true;
				
				// delete an old auth-token first ...
				SessionQuery::create()->filterByUserId($user->getId())->delete();
				
				// ... create a new one
				$this->session = new Session();
				$this->session->setToken(self::generateToken());
				$this->session->setUser($user);
				$this->session->save();
				return true;
			}
		}
		
		return false;
	}

	public function logout(User $user = null) {
		if ($user === null) {
			$user = $this->user;
		}
		$success = SessionQuery::create()->filterByUser($user)->delete() > 0;

		if ($success) {
			$this->user = $this->getGuest();
			$this->recognized = false;
			$this->authenticated = false;
			$this->session = null;
		}
		
		return $success;
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
}
