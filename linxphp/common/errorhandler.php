<?php

namespace linxphp\common;

class ErrorHandler{
    protected static $registered = false;
    protected static $old_error_reporting = null;
    protected static $old_display_error = null;
    
    static public function errorHandler($code, $string, $file, $line){
        switch ($code) {
            case E_DEPRECATED: // ignores new DEPRECATED error to allow developers to use third party libraries
                return;
            case E_WARNING:
                throw new ErrorException($string, $code, $code,$file,$line);
            default:
                throw new ErrorException($string, $code, $code,$file,$line);
        }
    }
    static public function register(){
        if (!self::$registered){
            // saves old error reporting
            self::$old_error_reporting = error_reporting();
            self::$old_display_error = ini_get('display_errors');
            
            // set new error reporting configuration
            ini_set('display_errors','1');
            error_reporting(E_ALL);

            // set error handling
            set_error_handler(array('linxphp\common\ErrorHandler','errorHandler'), E_ALL);
            self::$registered = true;
        }
        
    }
    static public function unregister(){
        if (self::$registered){
            // restore old configuration values
            ini_set('display_errors',self::$old_display_error);
            error_reporting(self::$old_error_reporting);
            
            restore_error_handler();
            self::$registered = false;
        }
    }
}