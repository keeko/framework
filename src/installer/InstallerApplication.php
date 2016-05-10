<?php
namespace keeko\framework\installer;

use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use keeko\core\model\Application;
use keeko\core\model\ApplicationQuery;
use keeko\core\model\ApplicationUri;
use keeko\core\model\Group;
use keeko\core\model\LanguageQuery;
use keeko\core\model\Localization;
use keeko\core\model\LocalizationQuery;
use keeko\core\model\Map\UserTableMap;
use keeko\core\model\Preference;
use keeko\core\model\User;
use keeko\core\model\UserQuery;
use keeko\framework\foundation\AbstractApplication;
use keeko\framework\preferences\SystemPreferences;
use keeko\framework\service\ServiceContainer;
use phootwork\lang\Text;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\Request;

class InstallerApplication extends AbstractApplication {
	
	const DEFAULT_LOCALE = 'en';

	/** @var IOInterface */
	private $io;
	
	/** @var PackageManager */
	private $packageManager;
	
	/** @var AppInstaller */
	private $appInstaller;
	
	/** @var ModuleInstaller */
	private $moduleInstaller;
	
	/** @var ModuleManager */
	private $moduleManager;

	public function __construct(Application $model, ServiceContainer $service, IOInterface $io = null) {
		parent::__construct($model, $service);
		$this->io = $io ?: new NullIO();
	}

	private function initialize() {
		$this->packageManager = $this->service->getPackageManager();
		$this->appInstaller = new AppInstaller($this->service);
		$this->moduleInstaller = new ModuleInstaller($this->service);
		$this->moduleManager = $this->service->getModuleManager();
	}

	public function install($rootUrl, $locale = self::DEFAULT_LOCALE) {
		if (!KEEKO_DATABASE_LOADED) {
			throw new \Exception('Cannot install keeko - no database defined');
		}
		
		$root = new Text($rootUrl);
		if ($root->endsWith('/')) {
			$rootUrl = $root->substring(0, -1);
		}

		$this->io->write('Install Log:');

		$this->installDatabase();
		$this->initialize();
		$this->installGroupsAndUsers();
		$this->installKeeko($rootUrl, $locale);
	}
	
	/**
	 * Runs the main setup routine
	 */
	public function run(Request $request) {
		$uri = $request->getUri();
		
		$this->install($uri, $request->getLocale());
	}
	
	/**
	 * Writes the database config
	 *
	 * @param string $host
	 * @param string $database
	 * @param string $user
	 * @param string $password
	 */
	public function writeConfig($host, $database, $user, $password) {
		
	}

	/**
	 *
	 * @param string $locale
	 * @return Localization
	 */
	private function getLocale($locale) {
		$langTag = \Locale::getPrimaryLanguage($locale);
		$regionTag = \Locale::getRegion($locale);
		
		$lang = LanguageQuery::create()->findOneBySubtag($langTag);
		$query = LocalizationQuery::create()->filterByLanguage($lang);
		
		if (!empty($regionTag)) {
			$query->filterByRegion($regionTag);
		}
		
		$local = $query->findOne();
		
		// if no locale found -> create one
		if ($local === null) {
			$local = new Localization();
			$local->setLanguage($lang);
			if (!empty($regionTag)) {
				$local->setRegion($regionTag);
			}
			$local->setIsDefault(true);
			$local->save();
		}

		return $local;
	}
	
	private function installGroupsAndUsers() {
		$guestGroup = new Group();
		$guestGroup->setName('Guest');
		$guestGroup->setIsGuest(true);
		$guestGroup->save();
		
		$userGroup = new Group();
		$userGroup->setName('Users');
		$userGroup->setIsDefault(true);
		$userGroup->save();
		
		$adminGroup = new Group();
		$adminGroup->setName('Administrators');
		$adminGroup->save();

		
		$con = Propel::getConnection();
		$adapter = Propel::getAdapter();
		
		// guest
		$guest = new User();
		$guest->setDisplayName('Guest');
		$guest->save();
		
		$stmt = $con->prepare(sprintf('UPDATE %s SET id = -1 WHERE ID = 1', $adapter->quoteIdentifierTable(UserTableMap::TABLE_NAME)));
		$stmt->execute();
		
		// root
		$root = new User();
		$root->setDisplayName('root');
		$root->setUserName('root');
		$root->setPassword(password_hash('root', PASSWORD_BCRYPT));
		$root->save();

		$stmt = $con->prepare(sprintf('UPDATE %s SET id = 0 WHERE ID = 2', $adapter->quoteIdentifierTable(UserTableMap::TABLE_NAME)));
		$stmt->execute();
		
		$root = UserQuery::create()->findOneById(0);
		$root->addGroup($userGroup);
		$root->addGroup($adminGroup);
		$root->save();
		
		// @TODO: Cross-SQL-Server routine wanted!!
		$stmt = $con->prepare(sprintf('ALTER TABLE %s AUTO_INCREMENT = 1', $adapter->quoteIdentifierTable(UserTableMap::TABLE_NAME)));
		$stmt->execute();
		
	}

	private function installKeeko($rootUrl, $locale = self::DEFAULT_LOCALE) {
		// 1) apps
		
		// api
		$apiUrl = $rootUrl . '/api/';
		$this->installApp('keeko/api-app');
		$this->setupApp('keeko/api-app', $apiUrl, $locale);
		
		// developer
		$this->installApp('keeko/developer-app');
		$this->setupApp('keeko/developer-app', $rootUrl . '/developer/', $locale);
		
		// account
		$accountUrl = $rootUrl . '/account/';
		$this->installApp('keeko/account-app');
		$this->setupApp('keeko/account-app', $accountUrl, $locale);

		
		// 2) preferences
		$core = $this->service->getPackageManager()->getComposerPackage('keeko/core');
		
		$this->setPreference(SystemPreferences::PREF_VERSION, $core->getPrettyVersion());
		$this->setPreference(SystemPreferences::PREF_PLATTFORM_NAME, 'Keeko');
		$this->setPreference(SystemPreferences::PREF_API_URL, $apiUrl);
		$this->setPreference(SystemPreferences::PREF_API_VERSION, '1');
		$this->setPreference(SystemPreferences::PREF_ACCOUNT_URL, $accountUrl);
		
		// user prefs
		$this->setPreference(SystemPreferences::PREF_USER_LOGIN, SystemPreferences::LOGIN_USERNAME);
		$this->setPreference(SystemPreferences::PREF_USER_EMAIL, true);
		$this->setPreference(SystemPreferences::PREF_USER_NAMES, SystemPreferences::VALUE_OPTIONAL);
		$this->setPreference(SystemPreferences::PREF_USER_BIRTH, SystemPreferences::VALUE_OPTIONAL);
		$this->setPreference(SystemPreferences::PREF_USER_SEX, SystemPreferences::VALUE_OPTIONAL);

		// 3) modules
		$this->installModule('keeko/core');
		$this->activateModule('keeko/core');
		
		$this->installModule('keeko/auth');
		$this->activateModule('keeko/auth');
		
		$this->installModule('keeko/account');
		$this->activateModule('keeko/account');
	}
	
	private function setPreference($key, $value) {
		$pref = new Preference();
		$pref->setKey($key);
		$pref->setValue($value);
		$pref->save();
	}

	public function installApp($packageName) {
		return $this->appInstaller->install($this->io, $packageName);
	}
	
	public function setupApp($packageName, $uri, $locale = self::DEFAULT_LOCALE) {
		$this->io->write(sprintf('[Keeko] Setup App %s at %s', $packageName, $uri));
		$app = ApplicationQuery::create()->findOneByName($packageName);
		
		if ($app === null) {
			throw new \Exception(sprintf('Application (%s) not found', $packageName));
		}
		
		$comps = parse_url($uri);
		
		$uri = new ApplicationUri();
		$uri->setApplication($app);
		$uri->setLocalization($this->getLocale($locale));
		$uri->setHttphost($comps['host']);
		$uri->setBasepath($comps['path']);
		$uri->setSecure($comps['scheme'] == 'https');
		$uri->save();
	}
	
	public function installModule($packageName) {
		$this->moduleInstaller->install($this->io, $packageName);
	}

	public function activateModule($packageName) {
		$this->moduleInstaller->activate($this->io, $packageName);
	}

	private function installDatabase() {
		$files = [
			'sql/keeko.sql',
			'data/static-data.sql'
		];
		$con = Propel::getConnection();
		
		foreach ($files as $file) {
			$path = KEEKO_PATH . '/packages/keeko/core/database/' . $file;
			
			if (file_exists($path)) {
				$sql = file_get_contents($path);
				
				try {
					$stmt = $con->prepare($sql);
					$stmt->execute();
				} catch (\Exception $e) {
					echo $e->getMessage();
				}
			}
		}
	}
}