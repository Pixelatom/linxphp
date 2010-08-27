<?php
abstract class Model{
    function  __isset($name) {
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        return (array_key_exists($name,$properties));
    }
    function  __get($name) {
       
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();
        
        if (array_key_exists($name,$properties)){
            
            Mapper::_load_relationship($this,$name);
            return $this->$name;
        }
        else{
            $trace = debug_backtrace();
            trigger_error(
            'Undefined property '.$class_name.'::' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
            return null;
        }
    }
}