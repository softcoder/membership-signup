<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;

ini_set('display_errors', 'On');
error_reporting(E_ALL);

//
// This file manages routing of requests
//
if(defined('INCLUSION_PERMITTED') === false) {
    define( 'INCLUSION_PERMITTED', true);
}

require_once 'config_constants.php';
require_once 'functions.php';
try {
    if (!file_exists('config.php' )) {
        throw new \Exception('Config script does not exist!');
    }
    else {
        require_once 'config.php';
    }
}
catch(\Exception $e) {
    echo '<!DOCTYPE html>'.PHP_EOL.
    '<html>'.PHP_EOL.
    '<head>'.PHP_EOL.
    '<meta charset="UTF-8">'.PHP_EOL.
    '<title>Error Detected</title>'.PHP_EOL.
    '<link rel="stylesheet" href="styles/main.css" />'.PHP_EOL.
    '</head>'.PHP_EOL.
    '<body>'.PHP_EOL.
    '<p style="font-size:40px; color: white">'.
    '<hr></p>'.PHP_EOL.
    '<p style="font-size:35px; color: red">'.PHP_EOL.
    'Error detected, message : ' . $e->getMessage().', '.'Code : ' . $e->getCode().PHP_EOL.
    'trace : ' . $e-> getTraceAsString().PHP_EOL.
    '<br><span style="font-size:35px; color: black">Please create a config.php script in the root installation path</span>'.PHP_EOL.
    '</p><hr>'.PHP_EOL.
    '</body>'.PHP_EOL.
    '</html>';

    return;
}

require_once __RIPRUNNER_ROOT__ . '/functions.php';
require __DIR__ . '/vendor/autoload.php';


// The main index URL
\Flight::route('GET|POST /', function () {
    global $SITECONFIGS;
    global $log;

    $root_url = getRootURLFromRequest(\Flight::request()->url, $SITECONFIGS);
    
    $log->trace("Route got / request url [".\Flight::request()->url . "] root url [".$root_url."]");
    \Flight::redirect($root_url .'/controllers/membership-controller.php');
});

// The membership URL
\Flight::route('GET|POST /membership-route', function () {
    global $SITECONFIGS;
    global $log;

    $root_url = getRootURLFromRequest(\Flight::request()->url, $SITECONFIGS);
    
    $log->trace("Route got /membership-route request url [".\Flight::request()->url . "] root url [".$root_url."]");
    \Flight::redirect($root_url .'/controllers/membership-controller.php?route_action=membership');
});

// The membership waiver URL
\Flight::route('GET|POST /waiver-route', function () {
    global $SITECONFIGS;
    global $log;

    $root_url = getRootURLFromRequest(\Flight::request()->url, $SITECONFIGS);
    
    $log->trace("Route got /waiver-route request url [".\Flight::request()->url . "] root url [".$root_url."]");
    \Flight::redirect($root_url .'/controllers/membership-controller.php?route_action=waiver');
});


\Flight::route('GET|POST /test/(@params)', function ($params) {
    global $log;
    $log->trace("Route got TEST message: ".$params);
}); 
	
\Flight::map('notFound', function () {
	// Handle not found
	echo "route NOT FOUND!" . PHP_EOL;
});
		
\Flight::set('flight.log_errors', true);	
\Flight::start();
