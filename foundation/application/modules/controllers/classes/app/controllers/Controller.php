<?php
namespace app\controllers;
/**
 * Base class for controllers.
 */
abstract class Controller{	
	abstract function index();
        
        /**
         * return the class name of a controller based on where it's located
         * @param string $controller_path 
         * @return string class name
         */
        public static function mapController($controller_path){
            // build path to the controllers folder
            $controllers_dir = \linxphp\implementation\Application::instance()->path();    
            $controllers_dir .= \linxphp\implementation\Application::instance()->configuration()->get('paths','controllers','controllers');   
            $controllers_dir = realpath($controllers_dir);
                        
            $namespace = dirname($controller_path);            
            $namespace = str_replace($controllers_dir, __NAMESPACE__, $namespace);            
            $namespace = str_replace('/', '\\',$namespace);
            $namespace = strtolower($namespace);
            
            $classname = $namespace . '\\' . ucfirst(basename($controller_path));
            
            return $classname;
        }
}