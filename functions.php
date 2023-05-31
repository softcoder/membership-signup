<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================

if ( defined('INCLUSION_PERMITTED') === false ||
    (defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false)) { 
	die( 'This file must not be invoked directly.' ); 
}

require_once 'logging.php';

function getFirstActiveConfig($list) {
    global $log;
	foreach ($list as &$site) {
		if($site->ENABLED == true) {
			return $site;
		}
		else {
		    $log->trace("In getFirstActiveConfig skipping: ".$site->toString());
		}
	}
	return null;
}

function getRootURLFromRequest($request_url, $sites, $use_site=null) {
	global $log;
	
    if ($use_site !== null || safe_count($sites) === 1) {
        if ($use_site !== null) {
            if ($log !== null) $log->trace("#1 Looking for website root URL req [$request_url] root [" . $use_site->WEBSITE->WEBSITE_ROOT_URL . "]");
            return rtrim($use_site->WEBSITE->WEBSITE_ROOT_URL, '/');
        } 
        else {
            if ($log !== null) $log->trace("#1 Looking for website root URL req [$request_url] root [" . $sites[0]->WEBSITE->WEBSITE_ROOT_URL . "]");
            return rtrim($sites[0]->WEBSITE->WEBSITE_ROOT_URL, '/');
        }
	}
	else {
		if(isset($request_url) === false && isset($_SERVER['REQUEST_URI']) === true) {
			$request_url = htmlspecialchars($_SERVER['REQUEST_URI']);
		}
		foreach ($sites as &$site) {
			if($log !== null) $log->trace("#2 Looking for website root URL req [$request_url] root [" . $site->WEBSITE->WEBSITE_ROOT_URL . "]");
			
			if($site->ENABLED == true && 
					strpos($request_url ?? '', $site->WEBSITE->WEBSITE_ROOT_URL) === 0) {
				return rtrim($site->WEBSITE->WEBSITE_ROOT_URL, '/');
			}
		}
		
		$url_parts = explode('/', $request_url);
		if(isset($url_parts)  === true && safe_count($url_parts) > 0) {
			$url_parts_count = safe_count($url_parts);
			
			foreach ($sites as &$site) {
				if($log !== null) $log->trace("#3 Looking for website root URL req [$request_url] root [" . $site->WEBSITE->WEBSITE_ROOT_URL . "]");
				
				$fh_parts = explode('/', $site->WEBSITE->WEBSITE_ROOT_URL);
				if(isset($fh_parts)  === true && safe_count($fh_parts) > 0) {
					$fh_parts_count = safe_count($fh_parts);
					
					for($index_fh = 0; $index_fh < $fh_parts_count; $index_fh++) {
						for($index = 0; $index < $url_parts_count; $index++) {
							if($log !== null) $log->trace("#3 fhpart [" .  $fh_parts[$index_fh] . "] url part [" . $url_parts[$index] . "]");
							
							if($fh_parts[$index_fh] !== '' && $url_parts[$index] !== '' &&
								$fh_parts[$index_fh] === $url_parts[$index]) {

                                    if($log !== null) $log->trace("#3 website matched!");
								return rtrim($site->WEBSITE->WEBSITE_ROOT_URL, '/');
							}
						}
					}
				}
			}
		}
	}
	return '';
}

if (!function_exists('is_countable')) {
    function is_countable($var) { 
        return is_array($var) 
            || $var instanceof Countable 
            || $var instanceof ResourceBundle 
            || $var instanceof SimpleXmlElement; 
    }
}

function safe_count($var) {
    if(is_countable($var)) {
        return count($var);
    }
    return 0;
}

function getSafeCookieValue($key, $cookie_variables=null) {
    if($cookie_variables !== null && array_key_exists($key, $cookie_variables) === true) {
        return htmlspecialchars($cookie_variables[$key]);
    }
    if($_COOKIE !== null && array_key_exists($key, $_COOKIE) === true) {
        return htmlspecialchars($_COOKIE[$key]);
    }
    return null;
}

function getSafeRequestValue($key, $request_variables=null) {
    if($request_variables !== null && array_key_exists($key, $request_variables) === true) {
        return htmlspecialchars($request_variables[$key]);
    }
    $request_list = array_merge($_GET, $_POST);
    if($request_list !== null && array_key_exists($key, $request_list) === true) {
        return htmlspecialchars($request_list[$key]);
    }
    return null;
}

function getServerVar($key, $server_variables=null) {
    if($server_variables !== null && array_key_exists($key, $server_variables) === true) {
        return htmlspecialchars($server_variables[$key]);
    }
    if($_SERVER !== null && array_key_exists($key, $_SERVER) === true) {
        return htmlspecialchars($_SERVER[$key]);
    }
    return null;
}

function get_query_param($param_name) {
    return getSafeRequestValue($param_name);
}	

