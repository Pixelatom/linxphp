<?php
abstract class Model{
    
    function  __construct() {
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        // removes variables when there are getters and setters in the format __get_variable
        foreach ($properties as $name=>$value){
            
            $methods_name = array('__get_'.$name,'__set_'.$name);
            foreach($methods_name as $method_name){
                
                if (method_exists($this, $method_name)){

                    unset($this->$name);
                    //die('accessible false');
                    //$prop = $function->getProperty($name);
                    //$prop->setAccessible(false); // we are making this property private here.. I think
                    
                }
            }
        }
    }
    public function __wakeup()
    {
        Mapper::_fill_relationship($this);
    }
    /*
    function  __set($name, $value) {
        // we'll check if the unset variable is part of the model
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        if (array_key_exists($name,$properties)){
            $reflection = new ReflectionProperty($class_name, $name);
            if ($reflection->isPublic() ){
                // is part of the model!!

                // we check if there is a getter existing
                $methodname = '__set_'.$name;
                if (method_exists($this, $methodname)){
                    return $this->$methodname($value);
                }

                // set the non accesible internal property
                $this->$name = $value;
            }
        }
    }
    */
    
    function  __get($name) {
        // when checking for unset variables
        // we'll check if the unset variable is part of the model
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        if (array_key_exists($name,$properties)){
            $reflection = new ReflectionProperty($class_name, $name);
            if ($reflection->isPublic() ){
                // is part of the model!!

                // we check if there is a getter existing
                $methodname = '__get_'.$name;
                //if (method_exists($this, $methodname)){
                //    return $this->$methodname();
                //}

                // then we try to return a lazy_load property
                Mapper::_load_relationship($this,$name);
                return $this->$name;
            }
        }

        $trace = debug_backtrace();
        trigger_error(
        'Undefined property '.$class_name.'::' . $name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
        return null;

    }
    // protected methods accesible only for 'friends' classes
    public function _before_insert(){
    }
    public function _before_update(){
    }
    public function _before_delete(){
    }
    public function _after_insert(){
    }
    public function _after_update(){
    }
    public function _after_delete(){
    }
    public function _after_load(){
    }
    
    
}