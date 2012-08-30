<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under MIT License.
 */
namespace linxphp\common;
/**
 * A static class instance holding all of the configuration settings. 
 * Default values are taken form the config.ini file located at the root of the program.
 */
class Configuration{
    static private $config=array();

    /**
     * with this function you can set a default value for a configuration key when it is not set on the ini file.
     */
    public static function setDefault($section,$key,$value){
        if (!isset(self::$config[strtolower($section)]) or !isset(self::$config[strtolower($section)][strtolower($key)]))
        self::set($section,$key,$value);
    }

    /**
     * this set or replace a value to a configuration key
     */
    public static function set($section,$key = null,$value = null ){		
        if (is_array($section)){
            self::$config = array_merge(self::$config,$array);
        }
        else{
            self::$config[strtolower($section)][strtolower($key)]=$value;
        }
    }

    /**
     * this function load the values from a ini file
     */
    public static function load($filename){		
        if (file_exists($filename)){
            $array = parse_ini_file($filename, true);

            self::set($array);
        }
    }
    

    /**
     * this function allows you to get the value of a configuration key.
     */
    public static function get($section,$key=false,$default=false){
        if(isset(self::$config[strtolower($section)]) and isset(self::$config[strtolower($section)]) and !$key)
            return self::$config[strtolower($section)];
        elseif(isset(self::$config[strtolower($section)]) and isset(self::$config[strtolower($section)][strtolower($key)]))
            return self::$config[strtolower($section)][strtolower($key)];
        else 
            return $default;
    }
}