<?php
namespace linxphp\implementation;

use linxphp\common\ErrorHandler;

class Application extends Module{
    static protected $_instance = NULL;
    public static function load($path) {
        // if the application was already initialized we''l trow an error
        if (self::$_instance) throw new \Exception('Application already loaded');
        
        // loads application configuration and init class autoloading 
        self::$_instance = parent::load($path);
        
        // sets error handler
        if (self::$_instance->config->get('error_handler','convert_to_exceptions',true)){
            ErrorHandler::register();
        }
        return self::$_instance;
    }
    
    public function __clone() {
        throw new \Exception('Cloning '.__CLASS__.' is not allowed.');
    }
    
    public function __wakeup() {
        throw new \Exception('Unserializing '.__CLASS__.' is not allowed.');
    }
}