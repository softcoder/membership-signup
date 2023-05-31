<?php
/*
    ==============================================================
	Copyright (C) 2014 Mark Vejvoda
	Under GNU GPL v3.0
    ==============================================================

	Class to handle application configuration
*/
namespace riprunner;

if(defined('__RIPRUNNER_ROOT__') === false) {
    define('__RIPRUNNER_ROOT__', dirname(dirname(__FILE__)));
}

if ( defined('INCLUSION_PERMITTED') === false ||
( defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false ) ) {
	die( 'This file must not be invoked directly.' );
}

require_once __RIPRUNNER_ROOT__ . '/cache/cache-proxy.php';
require_once __RIPRUNNER_ROOT__ . '/logging.php';

class ConfigManager {

    static private $DEFAULT_CONFIG_FILE = 'config-defaults.ini';
    
    private $sites = null;
    private $default_config = null;
    private $site_configs = array();

    private $default_config_file_path = null;
    private $enable_cache = true;
    
    /*
    	Constructor
    	@param $sites the sites configured for the application
    */
    public function __construct($sites=null,$default_config_file_path=null) {
        $this->sites = $sites;
        $this->default_config_file_path = $default_config_file_path;
    }

    public function setEnableCache($caching) {
        $this->enable_cache = $caching;
    }
    
    public function getSystemConfigValue($key) {
        $result = $this->findConfigValueInDefaultConfig($key);
        if($result === null) {
            $result = $this->findConfigValueInConstants($key);
        }
        return $result;
    }
    
    public function reset_default_config_file() {
        $filename = $this->getDefaultConfigFileFullPath();
        
        $cache_key_lookup = "RIPRUNNER_DEFAULT_CONFIG_FILE_" . $filename;
        if($this->enable_cache === true) {
            $this->getCache()->deleteItem($cache_key_lookup);
        }
        unlink($filename);
    }

    public function get_default_config() {
        $this->loadConfigValuesForDefault();
        if($this->default_config !== null) {
            return $this->default_config;
        }
        return null;
    }
    
    public function write_default_config_file($assoc_arr) {
        $filename = $this->getDefaultConfigFileFullPath();

        $cache_key_lookup = "RIPRUNNER_DEFAULT_CONFIG_FILE_" . $filename;
        if($this->enable_cache === true) {
            $this->getCache()->deleteItem($cache_key_lookup);
        }
            
        $this->write_ini_file($assoc_arr, $filename);
    }
    
    private function findConfigValueInConfigs($key,$site_id) {
        if($this->site_configs !== null && array_key_exists($site_id, 
                $this->site_configs) === true) {
            $site = $this->site_configs[$site_id];
            if(array_key_exists($key, $site) === true) {
                return $site[$key];
            }
        }
        return null;
    }
    
    private function loadConfigValuesForDefault() {
        //global $log;
        if($this->default_config === null) {
            $filename = $this->getDefaultConfigFileFullPath();
            if(file_exists($filename) === true) {
                $this->default_config = $this->getFileContents($filename);
            }                
        }
    }
    
    private function findConfigValueInDefaultConfig($key) {
        $this->loadConfigValuesForDefault();
        if($this->default_config !== null && array_key_exists($key,
                $this->default_config) === true) {
            return $this->default_config[$key];
        }
        return null;
    }
    private function findConfigValueInObject($key_parts, $index, $lookup_object) {
        if($lookup_object !== null) {
            if($lookup_object !== null && property_exists($lookup_object, $key_parts[$index]) === true) {
                $new_lookup_object = $lookup_object->{$key_parts[$index]};
                $index++;
                if($index < safe_count($key_parts)) {
                    return $this->findConfigValueInObject($key_parts, $index, $new_lookup_object);
                }
                else {
                    return $new_lookup_object;
                }
            }
        }
        return null;
    }
    
    private function findConfigValueInConstants($key) {
        if(defined($key) === true) {
            return constant($key);
        }
        return null;
    }

    private function getDefaultConfigFileFullPath() {
        return $this->getRootPath().'/'.$this->getDefaultConfigFilePath().
                                                $this->getDefaultConfigFile();
    }
    private function getDefaultConfigFile() {
        return self::$DEFAULT_CONFIG_FILE;
    }
    
    private function getRootPath() {
        return __RIPRUNNER_ROOT__;
    }

    private function getDefaultConfigFilePath() {
        if($this->default_config_file_path !== null) {
            return $this->default_config_file_path;
        }
        return '';
    }
    
    
    private function getFileContents($filename) {
        global $log;
        $cache_key_lookup = "RIPRUNNER_DEFAULT_CONFIG_FILE_" . $filename;
        if($this->enable_cache === true) {
            if ($this->getCache()->hasItem($cache_key_lookup) === true) {
                if($log !== null) $log->trace("DEFAULT CONFIG file found in CACHE: ".$filename);
                return $this->getCache()->getItem($cache_key_lookup);
            }
            else {
                if($log !== null) $log->trace("DEFAULT CONFIG file NOT FOUND in CACHE: ".$filename);
            }
        }
        $sql_array = parse_ini_file($filename, true);
        if($this->enable_cache === true) {
            $this->getCache()->setItem($cache_key_lookup, $sql_array);
        }
        return $sql_array;
    }
    
    private function write_ini_file($assoc_arr, $path, $has_sections=false) {
        $content = "";
        if ($has_sections === true) {
            foreach ($assoc_arr as $key => $elem) {
                $content .= "[".$key."]\n";
                foreach ($elem as $key2 => $elem2) {
                    if(is_array($elem2) === true) {
                        $elem2_count = safe_count($elem2);
                        for($i = 0; $i < $elem2_count; $i++) {
                            if(is_bool($elem2[$i]) === true) {
                                $content .= $key2."[] = \"". (($elem2[$i] === true) ? 'true' : 'false')."\"\n";
                            }
                            else {
                                $content .= $key2."[] = \"".$elem2[$i]."\"\n";
                            }
                        }
                    }
                    else if($elem2 == "") {
                        $content .= $key2." = \n";
                    }
                    else {
                        if(is_bool($elem2) === true) {
                            $content .= $key2." = \"". (($elem2 === true) ? 'true' : 'false')."\"\n";
                        }
                        else {
                            $content .= $key2." = \"".$elem2."\"\n";
                        }
                    }
                }
            }
        }
        else {
            foreach ($assoc_arr as $key => $elem) {
                if(is_array($elem) === true) {
                    $elem_count = safe_count($elem);
                    for($i = 0; $i < $elem_count; $i++) {
                        $content .= $key."[] = \"".$elem[$i]."\"\n";
                    }
                }
                else if($elem == "") {
                    $content .= $key." = \n";
                }
                else {
                    if(is_bool($elem) === true) {
                        $content .= $key." = \"". (($elem === true) ? 'true' : 'false')."\"\n";
                    }
                    else {
                        $content .= $key." = \"".$elem."\"\n";
                    }
                }
            }
        }
        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        $success = fwrite($handle, $content);
        fclose($handle);
        return $success;
    }
    
    private function getCache() {
        return CacheProxy::getInstance();
    }
    
}
