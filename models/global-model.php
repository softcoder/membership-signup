<?php 
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;

require_once __RIPRUNNER_ROOT__ . '/config.php';
require_once __RIPRUNNER_ROOT__ . '/functions.php';

// Model array of variables to be used for view
if(isset($view_template_vars) === false) {
    $view_template_vars = array();
}
if(isset($global_vm) === false && isset($SITECONFIGS) == true) {
	$global_vm = new GlobalViewModel($SITECONFIGS);
	$view_template_vars['gvm'] = $global_vm;
}

// The model class handling variable requests dynamically
class GlobalViewModel {
	
	private $detect_browser;
	private $sites;
	
	public function __construct($sites) { 
		$this->sites = $sites;
	}
	
	public function __destruct() { 

	}
	
	public function __get($name) {
		global $log;

		if('isMobile' === $name) {
			//return $this->getDetectBrowser()->isMobile();
		}
		if('isTablet' === $name) {
			//return $this->getDetectBrowser()->isTablet();
		}
		if('RR_DOC_ROOT' === $name) {
			return getRootURLFromRequest(null, $this->sites);
		}
		if('RR_DB_CONN' === $name) {
			return $this->getDBConnection();
		}

		if('site' === $name) {
			return $this->getSite();
		}
		if('phpinfo' === $name) {
			return $this->getPhpInfo();
		}
		
		// throw some kind of error
		throw new \Exception("Invalid var reference [$name].");
	}

	public function __isset($name) {
		if(in_array($name,
			array('isMobile','isTablet','RR_DOC_ROOT','RR_DB_CONN',
				  'site', 'phpinfo'
			)) === true) {
			return true;
		}
		return false;
	}
	
	private function getPhpInfo() {
		ob_start();
		phpinfo();
		return ob_get_clean();
	}
	
	// Lazy init as much as possible
	private function getDetectBrowser() {
		if(isset($this->detect_browser) === false) {
//			$this->detect_browser = MobileDetect_Factory::create('browser_type');
		}
		return $this->detect_browser;
	}
	
	private function getSite() {
		return getFirstActiveConfig($this->sites);;
	}
	
}
