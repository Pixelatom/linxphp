<?php

namespace linxphp;

class Loader{    
    /**
     * list of directories and regexp patterns used to locate a class on autoload
     * @var array 
     */
    static protected $_classes_paths = array();
    
    /**
     * PHP has functionality to automatically load files if a certain class has not been loaded yet. Linx employs this functionality.
     * this function adds a new direcotry where to find classes to auto load.
     *@param string $name_patern a regexp pattern for the class name. it must have a captouring group defined.
     *@param string $filename it will set the name of the file which will be included. it should use the capturing group set in the previous parameter
     *@param string $directory the directory where the file to include is located.
     */
    static public function add_class_path($name_patern='/(.+)/',$filename='$1.php',$directory=''){
        self::$_classes_paths[]=array('name_patern'=>$name_patern,'filename'=>$filename,'directory'=>$directory);
    }
    
    /**
     * Get an array containing all the directories where the framework should search for classes
     */
    static public function get_classes_paths(){
        return self::$_classes_paths;
    }
    
    static public function autoload($class_name){
        $file = '';
        $filename = '';

        
        foreach (\linxphp\Loader::get_classes_paths() as $params){

            // we assume the class AAA\BBB\CCC is placed in /AAA/BBB/CCC.php
            $f_class_name = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class_name);
            

            if (preg_match($params['name_patern'], $f_class_name)){

                $filename = preg_replace($params['name_patern'], $params['filename'], $f_class_name);

                $file = $params['directory'].'/'.$filename;
                
                if (file_exists($file) == true) break;
            }
        }

        if (file_exists($file) == false) return false;

        include($file);
        
        # dispatch event telling the class was loaded wuuuhuu!
        if (class_exists ('\linxphp\Event',false))
        \linxphp\Event::run('loader.autoload',$class_name);
    }
}