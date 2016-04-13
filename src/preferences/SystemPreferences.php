<?php
namespace keeko\framework\preferences;

class SystemPreferences extends Preferences {

	const VERSION = 'version';
	const PLATTFORM_NAME = 'plattform_name';
	const ROOT_URL = 'root_url';
	const API_URL = 'api_url';
	const API_VERSION = 'api_version';
	
	const USER_LOGIN = 'login';
	const USER_NAMES_REQUIRED = 'user_names_required';
	const USER_BIRTH_REQUIRED = 'user_birth_required';
	const USER_DISPLAY_NAME = 'user_display_name';
	
	const USER_DISPLAY_OPT_USERNAME = 'user_display_opt_username';
	const USER_DISPLAY_OPT_NICKNAME = 'user_display_opt_nickname';
	const USER_DISPLAY_OPT_GIVENFAMILYNAME = 'user_display_opt_givenfamilyname';
	const USER_DISPLAY_OPT_FAMILYGIVENNAME = 'user_display_opt_familygivenname';
	
	// msic stuff
	const PAGINATION_SIZE = 'pagination_size';
	const PAGINATION_SIZE_DEFAULT = 50;
	
	// Values

	/**
	 * Username login value
	 * 
	 * @var string
	 */
	const LOGIN_USERNAME = 'username';
	
	/**
	 * Email login value
	 * 
	 * @var string
	 */
	const LOGIN_EMAIL = 'email';
	
	/**
	 * Username display value
	 * 
	 * @var string
	 */
	const DISPLAY_USERNAME = 'username';
	
	/**
	 * Nickname display value
	 * 
	 * @var string
	 */
	const DISPLAY_NICKNAME = 'nickname';
	
	/**
	 * <Given> <Family> name display value
	 * 
	 * @var string
	 */
	const DISPLAY_GIVENFAMILYNAME = 'given_family';
	
	/**
	 * <Family> <Given> name display value
	 * 
	 * @var string
	 */
	const DISPLAY_FAMILYGIVENNAME = 'family_given';
	
	/**
	 * Let the user select the display name
	 * 
	 * @var string
	 */
	const DISPLAY_USERSELECT = 'user_select';
	
	
	/**
	 * Returns the plattforms name
	 *
	 * @return string
	 */
	public function getPlattformName() {
		return $this->get(self::PLATTFORM_NAME);
	}
	
	/**
	 * Returns the url to the public API
	 *
	 * @return string
	 */
	public function getApiUrl() {
		return $this->get(self::API_URL);
	}
	
	/**
	 * Returns the API version
	 *
	 * @return string
	 */
	public function getApiVersion() {
		return $this->get(self::API_VERSION);
	}
	
	/**
	 * Returns the plattform version (keeko/core)
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->get(self::VERSION);
	}
	
	/**
	 * Returns the root url for the installed plattform
	 *
	 * @return string
	 */
	public function getRootUrl() {
		return $this->get(self::ROOT_URL);
	}
	
	/**
	 * Returns whether username or email is used as login
	 * 
	 * @see SystemPreferences::LOGIN_USERNAME
	 * @see SystemPreferences::LOGIN_EMAIL
	 * 
	 * @return string
	 */
	public function getUserLogin() {
		return $this->get(self::USER_LOGIN, self::LOGIN_USERNAME);
	}
	
	/**
	 * Returns what kind of name should be displayed for a user
	 * 
	 * @see SystemPreferences::DISPLAY_USERNAME
	 * @see SystemPreferences::DISPLAY_NICKNAME
	 * @see SystemPreferences::DISPLAY_GIVENFAMILYNAME
	 * @see SystemPreferences::DISPLAY_FAMILYGIVENNAME
	 * @see SystemPreferences::DISPLAY_USERSELECT
	 * 
	 * @return string
	 */
	public function getUserDisplayName() {
		return $this->get(self::USER_DISPLAY_NAME, self::DISPLAY_USERNAME);
	}
	
	/**
	 * Returns whether username is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionUsername() {
		return $this->getBool(self::USER_DISPLAY_OPT_FAMILYGIVENNAME);
	}
	
	/**
	 * Returns whether nickname is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionNickname() {
		return $this->getBool(self::USER_DISPLAY_OPT_NICKNAME);
	}
	
	/**
	 * Returns whether <Given> <Family> name is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionGivenFamilyName() {
		return $this->getBool(self::USER_DISPLAY_OPT_GIVENFAMILYNAME);
	}
	
	/**
	 * Returns whether <Family> <Given> name is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionFamilyGivenName() {
		return $this->getBool(self::USER_DISPLAY_OPT_FAMILYGIVENNAME);
	}
	
	/**
	 * Returns the pagination size
	 * 
	 * @return int
	 */
	public function getPaginationSize() {
		return $this->getInt(self::PAGINATION_SIZE, self::PAGINATION_SIZE_DEFAULT);
	}
}
