<?php
use keeko\framework\kernel\AppKernel;

require_once __DIR__ . '/bootstrap.php';

if (!KEEKO_DATABASE_LOADED) {
	echo 'No database loaded. Please run install.';
	exit;
}

$kernel = new AppKernel();
$response = $kernel->process();
$response->send();
