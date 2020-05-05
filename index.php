<?php
// [START gae_php_app_bootstrap]

// Use the composer autoloader to load dependencies.
require_once __DIR__ . '/vendor/autoload.php';

//  Load the application code.
/** @var Slim\App $app */
$app = require __DIR__ . '/src/app.php';
require __DIR__ . '/src/controllers.php';

// Bootstrap the slim framework to handle the request.
$app->run();

// [END gae_php_app_bootstrap]