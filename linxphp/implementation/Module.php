<?php

namespace linxphp\implementation;

use linxphp\common\Configuration;
use linxphp\common\Event;

class Module{
    public $config;    
    public $path;
    
    private function __construct(){    
        $this->config = new Configuration();
    }
    
    public static function load($path){
        $module = new Module();
                
        $module->path = $path;
        # load application configuration

        if (file_exists($path.'config.ini')){
            $module->config->load($path.'config.ini');
        }
        
        # configure app classes autoloading
        $app_classes = $config->get('paths','classes',$module->path.'classes');
        $class_loader = new ClassLoader(null,$app_classes);
        $class_loader->register();

        # Application setup ready
        Event::run('module.ready',$module);
        
        return $module;
    }
}