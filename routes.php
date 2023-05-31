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
    \handle_config_error('', $e);
    return;
}

require_once __RIPRUNNER_ROOT__ . '/functions.php';
require __DIR__ . '/vendor/autoload.php';


// The main membership URL
\Flight::route('GET|POST /', function () {
    global $SITECONFIGS;
    //$query = array();
    //parse_str($params, $query);

    $root_url = getRootURLFromRequest(\Flight::request()->url, $SITECONFIGS);
    \Flight::redirect($root_url .'/controllers/membership-controller.php');
});

// The membership waiver URL
\Flight::route('GET|POST /waiver', function () {
    global $SITECONFIGS;
    //$query = array();
    //parse_str($params, $query);

    $root_url = getRootURLFromRequest(\Flight::request()->url, $SITECONFIGS);
    \Flight::redirect($root_url .'/controllers/membership-waiver-controller.php');
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
