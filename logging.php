<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================

if ( defined('INCLUSION_PERMITTED') === false ||
    (defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false)) { 
	die( 'This file must not be invoked directly.' ); 
}

if(defined('__RIPRUNNER_ROOT__') === false) {
    define('__RIPRUNNER_ROOT__', dirname(__FILE__));
}
require __DIR__ . '/vendor/autoload.php';

// Use Monolog's `Logger` namespace:
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = null;

// The model class handling variable requests dynamically
class AppLogger extends Logger {

	public function trace($msg) {
  		parent::info($msg);
	}

	public function getRootLoggerPath() {
		return $this->log->getHandlers()[0]->getUrl();
	}
}

$log = new AppLogger('myLogger');

// Declare a new handler and store it in the $logstream variable
// This handler will be triggered by events of log level INFO and above
$logstream = new StreamHandler(__RIPRUNNER_ROOT__ . '/application.log', Logger::WARNING);

// Push the $logstream handler onto the Logger object
$log->pushHandler($logstream);

function throwExceptionAndLogError($ui_error_msg, $log_error_msg) {
    global $log;
	try {
		throw new \Exception($log_error_msg);
	}
	catch(Exception $ex) {
	    if($log != null) $log->error($ui_error_msg, $ex);
		die($ui_error_msg);
	}
}
