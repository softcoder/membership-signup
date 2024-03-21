<?php
/*
 ==============================================================
Copyright (C) 2014 Mark Vejvoda
Under GNU GPL v3.0
==============================================================

This is a class factory for Rip Runner class isntances

*/
namespace riprunner;

if ( defined('INCLUSION_PERMITTED') === false ||
    (defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false)) {
	die( 'This file must not be invoked directly.' );
}

require_once 'Mobile_Detect.php';
require_once 'logging.php';

/**
 * Factory class to instantiate Mobile device detection class instances
 */
class MobileDetect_Factory {
	public static function create($type, $param=null) {
		if(isset($type) === false) {
			throwExceptionAndLogError('No mobile type specified.', "Invalid mobile type specified [$type] param [$param]!");
		}

		switch($type) {
			case 'browser_type':
				return new \Mobile_Detect();
			default:
				throwExceptionAndLogError('Invalid mobile type specified.', "Invalid mobile type specified [$type] param [$param]!");
		}
	}
}
