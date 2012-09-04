<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias.
 * Licensed under MIT License.
 */
namespace linxphp\common;
/**
 * A  class instance holding all of the configuration settings. 
 * Default values are taken form the config.ini file located at the root of the program.
 */
class Configuration{
    private $config=array();
    
    /**
     * this function load the values from a ini file
     */
    public function load($filename){		
        $array = parse_ini_file($filename, true);
        $this->set($array);
        return $this;
    }

    /**
     * with this function you can set a default value for a configuration key when it is not set on the ini file.
     */
    public function setDefault($section,$key,$value){
        if (!isset($this->config[strtolower($section)]) or !isset($this->config[strtolower($section)][strtolower($key)]))
        $this->set($section,$key,$value);
        return $this;
    }

    /**
     * this set or replace a value to a configuration key
     */
    public function set($section,$key = null,$value = null ){		
        if (is_array($section)){
            $this->config = array_merge($this->config,$section);
        }
        else{
            $this->config[strtolower($section)][strtolower($key)]=$value;
        }
        return $this;
    }

    /**
     * this function allows you to get the value of a configuration key.
     */
    public function get($section,$key=false,$default=false){
        if(isset($this->config[strtolower($section)]) and isset($this->config[strtolower($section)]) and !$key)
            return $this->config[strtolower($section)];
        elseif(isset($this->config[strtolower($section)]) and isset($this->config[strtolower($section)][strtolower($key)]))
            return $this->config[strtolower($section)][strtolower($key)];
        else 
            return $default;
    }
}