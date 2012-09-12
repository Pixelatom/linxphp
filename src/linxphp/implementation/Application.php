<?php
namespace linxphp\implementation;

use linxphp\common\ErrorHandler;
use linxphp\common\Event;

class Application extends Module{
    static protected $_instance = NULL;
    
    /**
     * Singleton method that loads the Application just once
     * @param type $path the path to the application
     * @return type
     * @throws \Exception 
     */
    public static function load($path) {        
        // if the application was already initialized we''l trow an error
        if (self::$_instance) throw new \Exception('Application already loaded');
        
        if (!file_exists($path)) throw new \Exception("Application path '$path' does not exists");
        
        // loads application configuration and init class autoloading 
        self::$_instance = parent::load($path);
        
        // sets error handler
        if (self::$_instance->config->get('error_handler','convert_to_exceptions',true)){
            ErrorHandler::register();
        }

        // Register application modules
        $app_modules = self::$_instance->config->get('paths','modules','modules');
        if (file_exists(self::$_instance->path.$app_modules)){
            $dir = new \DirectoryIterator((self::$_instance->path.$app_modules));
            foreach ($dir as $file){
                if(!$file->isDot() && $file->isDir()) {
                    Module::load(self::$_instance->path.$app_modules.DIRECTORY_SEPARATOR.$file);
                }
            }
        }
        
        # Application setup ready
        Event::run('system.ready');
        
        return self::$_instance;
    }
    
    public function __clone() {
        throw new \Exception('Cloning '.__CLASS__.' is not allowed.');
    }
    
    public function __wakeup() {
        throw new \Exception('Unserializing '.__CLASS__.' is not allowed.');
    }
}