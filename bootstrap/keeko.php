<?php
use keeko\framework\kernel\WebKernel;

$puli = require_once __DIR__ . '/bootstrap.php';

if (!KEEKO_DATABASE_LOADED) {
	echo 'No database loaded. Please run install.';
	exit;
}

$kernel = new WebKernel($puli);
$response = $kernel->process();
$response->send();
