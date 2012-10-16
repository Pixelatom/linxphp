<?php

namespace linxphp\implementation;

use linxphp\common\Configuration;
use linxphp\common\Event;
use linxphp\common\ClassLoader;

class Module{
    protected $config;    
    protected $path;
    
    protected $name;
    
    public function name(){
        return $this->name;
    }
    
    public function path(){
        return $this->path;
    }
        
    private function __construct(){    
        $this->config = new Configuration();
    }
    
    /**
     * Factory method to create Module instances
     * @param string $path the path to the module to be loaded
     * @return \linxphp\implementation\Module 
     */
    public static function load($path){
        
        if (!file_exists($path)) throw new \Exception("Module path '$path' does not exists");
        
        
        $module = new static();
        
        // adds slash at the end of the path
        if (substr($path, -1) !== "/" and substr($path, -1) !== "\\") {
            $path .= DIRECTORY_SEPARATOR;
        }                 
        $module->path = $path;
        
        # load application configuration
        if (file_exists($path.'config.ini')){
            $module->config->load($path.'config.ini');
        }
        
        # assign a module name that can be also configured
        $module->name = $module->config->get('module','name',basename($path));
        
        # configure app classes autoloading
        $app_classes = $module->config->get('paths','classes','classes');        
        
        if (file_exists($path.$app_classes)){
            $class_loader = new ClassLoader(null,($path.$app_classes));
            $class_loader->register();    
        }
        
        # autoinclude hook files
        $app_hooks = $module->config->get('paths','hooks','hooks');
        if (file_exists($path.$app_hooks)){
            $dir = new \DirectoryIterator(($path.$app_hooks));
            foreach ($dir as $file){
                if(!$file->isDot() && !$file->isDir() && preg_match("/\.php$/",$file->getFilename())) {                
                    include_once($dir->getPath().'/'.$file->getFilename());
                }
            }
        }

        # Application setup ready
        Event::run('module.load',$module);
        
        return $module;
    }
}