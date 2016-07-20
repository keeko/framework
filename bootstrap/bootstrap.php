<?php
use keeko\framework\config\DatabaseConfiguration;
use keeko\framework\config\DevelopmentConfiguration;
use keeko\framework\service\PuliService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

define('KEEKO_PRODUCTION', 'production');
define('KEEKO_DEVELOPMENT', 'development');

// puli
$puli = new PuliService();
$repo = $puli->getResourceRepository();

// load config
$locator = new FileLocator($repo->get('/config')->getFilesystemPath());
$devConfig = new DevelopmentConfiguration($locator);
$dbConfig = new DatabaseConfiguration($locator);

$loader = new DelegatingLoader(new LoaderResolver([$devConfig, $dbConfig]));
$loader->load('development.yaml');
$loader->load('database.yaml');

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

return $puli;
