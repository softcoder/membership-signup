<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
ini_set('display_errors', 'On');
error_reporting(E_ALL);

if ( defined('INCLUSION_PERMITTED') === false ||
     ( defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false ) ) { 
	die( 'This file must not be invoked directly.' ); 
}

	if(defined('__RIPRUNNER_ROOT__') === false) {
	    define('__RIPRUNNER_ROOT__', dirname(__FILE__));
	}

// ==============================================================

define( 'DEBUG_MODE', false);
define( 'LOG_LEVEL', 'WARNING');

