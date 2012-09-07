<?php

namespace linxphp\implementation;

use linxphp\common\Configuration;
use linxphp\common\Event;

class Module{
    protected $config;    
    protected $path;
    
    private function __construct(){    
        $this->config = new Configuration();
    }
    
    public static function load($path){
        $module = new get_called_class();
        
        // adds slash at the end of the path
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }                 
        $module->path = $path;
        
        # load application configuration
        if (file_exists($path.'config.ini')){
            $module->config->load($path.'config.ini');
        }
        
        # configure app classes autoloading
        $app_classes = $config->get('paths','classes',$module->path.'classes');
        $class_loader = new ClassLoader(null,$app_classes);
        $class_loader->register();
        
        # autoinclude hook files
        $app_hooks = $config->get('paths','hooks',$module->path.'hooks');
        $dir = new \DirectoryIterator($app_hooks);
        foreach ($dir as $file){
            if(!$file->isDot() && !$file->isDir() && preg_match("/\.php$/",$file->getFilename())) {                
                include_once($dir->getPath().'/'.$file->getFilename());
            }
        }

        # Application setup ready
        Event::run('module.ready',$module);
        
        return $module;
    }
}