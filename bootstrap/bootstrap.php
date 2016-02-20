<?php
use keeko\framework\config\DatabaseConfiguration;
use keeko\framework\config\DevelopmentConfiguration;
use keeko\framework\config\GeneralConfiguration;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

define('KEEKO_PRODUCTION', 'production');
define('KEEKO_DEVELOPMENT', 'development');

define('KEEKO_PATH_CONFIG', KEEKO_PATH . DIRECTORY_SEPARATOR . 'config');
define('KEEKO_PATH_PACKAGES', KEEKO_PATH . DIRECTORY_SEPARATOR . 'packages');

// load config
$locator = new FileLocator(KEEKO_PATH_CONFIG);
$devConfig = new DevelopmentConfiguration($locator);
$dbConfig = new DatabaseConfiguration($locator);
$generalConfig = new GeneralConfiguration($locator);
$loader = new DelegatingLoader(new LoaderResolver([$devConfig, $dbConfig, $generalConfig]));

try {
	$loader->load(KEEKO_PATH_CONFIG . '/development.yaml');
	$loader->load(KEEKO_PATH_CONFIG . '/database.yaml');
	$loader->load(KEEKO_PATH_CONFIG . '/general.yaml');
} catch (FileLoaderLoadException $e) {}


// development config
define('KEEKO_ENVIRONMENT', $devConfig->isLoaded() ? KEEKO_DEVELOPMENT : KEEKO_PRODUCTION);

if (KEEKO_ENVIRONMENT == KEEKO_DEVELOPMENT) {
	error_reporting(E_ALL | E_STRICT);
}


// database config
define('KEEKO_DATABASE_LOADED', $dbConfig->isLoaded());
if ($dbConfig->isLoaded()) {
	$serviceContainer = Propel::getServiceContainer();
	$serviceContainer->setAdapterClass('keeko', 'mysql');
	$manager = new ConnectionManagerSingle();
	$manager->setConfiguration([
		'dsn'      => 'mysql:host=' . $dbConfig->getHost() . ';dbname=' . $dbConfig->getDatabase(),
		'user'     => $dbConfig->getUser(),
		'password' => $dbConfig->getPassword()
	]);
	$manager->setName('keeko');
	$serviceContainer->setConnectionManager('keeko', $manager);
	$serviceContainer->setDefaultDatasource('keeko');
	
	// set utf-8
	$con = Propel::getWriteConnection('keeko');
	$con->exec('SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci;');
// 	$con->exec('SET SQL_SAFE_UPDATES=0;');
	
	if (KEEKO_ENVIRONMENT == KEEKO_DEVELOPMENT) {
		$con->useDebug(true);
		$logger = new Logger('defaultLogger');
		
		if ($devConfig->getPropelLogging() == 'stderr') {
			$logger->pushHandler(new StreamHandler('php://stderr'));
		}
		$serviceContainer->setLogger('defaultLogger', $logger);
	}
}
unset($dbConfig);

// general config
define('KEEKO_PATH_FILES', KEEKO_PATH . DIRECTORY_SEPARATOR . $generalConfig->getPathsFiles());
