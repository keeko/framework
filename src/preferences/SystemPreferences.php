<?php
namespace keeko\framework\preferences;

class SystemPreferences extends Preferences {

	const PREF_VERSION = 'version';
	const PREF_PLATTFORM_NAME = 'plattform_name';
	const PREF_ROOT_URL = 'root_url';
	const PREF_API_URL = 'api_url';
	const PREF_API_VERSION = 'api_version';
	const PREF_ACCOUNT_URL = 'root_url';
	
	const PREF_USER_LOGIN = 'login';
	const PREF_USER_NAMES = 'user_names';
	const PREF_USER_NICKNAME = 'user_nickname';
	const PREF_USER_BIRTH = 'user_birth';
	const PREF_USER_SEX = 'user_sex';
	const PREF_USER_DISPLAY_NAME = 'user_display_name';
	
	const PREF_USER_DISPLAY_OPT_USERNAME = 'user_display_opt_username';
	const PREF_USER_DISPLAY_OPT_NICKNAME = 'user_display_opt_nickname';
	const PREF_USER_DISPLAY_OPT_GIVENFAMILYNAME = 'user_display_opt_givenfamilyname';
	const PREF_USER_DISPLAY_OPT_FAMILYGIVENNAME = 'user_display_opt_familygivenname';
	
	// misc stuff
	const PREF_PAGINATION_SIZE = 'pagination_size';
	
	
	// Values
	
	/**
	 * Default pagination size
	 * 
	 * @var int
	 */
	const PAGINATION_SIZE_DEFAULT = 50;

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
	 * Email or username login
	 * 
	 * @var string
	 */
	const LOGIN_USERNAME_EMAIL = 'username_email';
	
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
	 * Value for required
	 * 
	 * @var string
	 */
	const VALUE_REQUIRED = 'required';
	
	/**
	 * Values for optional
	 * @var string
	 */
	const VALUE_OPTIONAL = 'optional';
	
	/**
	 * Values for not used at all
	 * @var string
	 */
	const VALUE_NONE = 'none';
	
	/**
	 * Returns the plattforms name
	 *
	 * @return string
	 */
	public function getPlattformName() {
		return $this->get(self::PREF_PLATTFORM_NAME);
	}
	
	/**
	 * Returns the url to the public API
	 *
	 * @return string
	 */
	public function getApiUrl() {
		return $this->get(self::PREF_API_URL);
	}
	
	/**
	 * Returns the API version
	 *
	 * @return string
	 */
	public function getApiVersion() {
		return $this->get(self::PREF_API_VERSION);
	}
	
	/**
	 * Returns the plattform version (keeko/core)
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->get(self::PREF_VERSION);
	}
	
	/**
	 * Returns the root url for the installed plattform
	 *
	 * @return string
	 */
	public function getRootUrl() {
		return $this->get(self::PREF_ROOT_URL);
	}
	
	/**
	 * Returns the accout url for the installed plattform
	 * 
	 * @return string
	 */
	public function getAccountUrl() {
		return $this->get(self::PREF_ACCOUNT_URL);
	}
	
	/**
	 * Returns whether users birth is required, optional or not used at all
	 * 
	 * @see SystemPreferences::VALUE_NONE
	 * @see SystemPreferences::VALUE_OPTIONAL
	 * @see SystemPreferences::VALUE_REQUIRED
	 * 
	 * @return string
	 */
	public function getUserBirth() {
		return $this->get(self::PREF_USER_BIRTH);
	}
	
	/**
	 * Returns whether users sex is required, optional or not used at all
	 * 
	 * @see SystemPreferences::VALUE_NONE
	 * @see SystemPreferences::VALUE_OPTIONAL
	 * @see SystemPreferences::VALUE_REQUIRED
	 * 
	 * @return string
	 */
	public function getUserSex() {
		return $this->get(self::PREF_USER_SEX);
	}
	
	/**
	 * Returns whether users names (given and family) are required, optional or not used at all
	 * 
	 * @see SystemPreferences::VALUE_NONE
	 * @see SystemPreferences::VALUE_OPTIONAL
	 * @see SystemPreferences::VALUE_REQUIRED
	 * 
	 * @return string
	 */
	public function getUserNames() {
		return $this->get(self::PREF_USER_NAMES);
	}
	
	/**
	 * Returns whether username or email is used as login
	 * 
	 * @see SystemPreferences::LOGIN_USERNAME
	 * @see SystemPreferences::LOGIN_EMAIL
	 * @see SystemPreferences::LOGIN_USERNAME_EMAIL
	 * 
	 * @return string
	 */
	public function getUserLogin() {
		return $this->get(self::PREF_USER_LOGIN, self::LOGIN_USERNAME);
	}
	
	/**
	 * Returns whether user can select a nickname or not
	 * 
	 * @return boolean
	 */
	public function getUserNickname() {
		return $this->getBool(self::PREF_USER_NICKNAME);
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
		return $this->get(self::PREF_USER_DISPLAY_NAME, self::DISPLAY_USERNAME);
	}
	
	/**
	 * Returns whether username is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionUsername() {
		return $this->getBool(self::PREF_USER_DISPLAY_OPT_FAMILYGIVENNAME);
	}
	
	/**
	 * Returns whether nickname is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionNickname() {
		return $this->getBool(self::PREF_USER_DISPLAY_OPT_NICKNAME);
	}
	
	/**
	 * Returns whether <Given> <Family> name is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionGivenFamilyName() {
		return $this->getBool(self::PREF_USER_DISPLAY_OPT_GIVENFAMILYNAME);
	}
	
	/**
	 * Returns whether <Family> <Given> name is an option for a user
	 * 
	 * @return boolean
	 */
	public function getUserDisplayOptionFamilyGivenName() {
		return $this->getBool(self::PREF_USER_DISPLAY_OPT_FAMILYGIVENNAME);
	}
	
	/**
	 * Returns the pagination size
	 * 
	 * @return int
	 */
	public function getPaginationSize() {
		return $this->getInt(self::PREF_PAGINATION_SIZE, self::PAGINATION_SIZE_DEFAULT);
	}
}
