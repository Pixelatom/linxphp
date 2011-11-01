<?php
abstract class Model{
    
    
    /**
     * relation properties filters support :)
     */
    public function __call($name, $arguments) {
        
        // when checking for unset variables
        // we'll check if the unset variable is part of the model
        $class_name = get_class($this);
        $description = ModelDescriptor::describe($this);

        if (array_key_exists($name,$description['properties']) and
            $description['properties'][$name]["attributes"]['is_relationship'] and
            isset($description['properties'][$name]["attributes"]['relationship']['inverse_property'])){
            
            
            array_unshift($arguments,$description['properties'][$name]["attributes"]['type']);
            
            // $arguments[1] are the conditions of the search 
            foreach ($description['primary_key'] as $key ){
                $value = $description['properties'][$key]['value'];
                if (!empty($arguments[1])){
                    $arguments[1] .= ' and ';
                }
                
                $forekey = $description['properties'][$name]["attributes"]['relationship']['inverse_property'] . '_' . $key;
                
                $arguments[1] .= " $forekey = " . $value;
            
            }
            
            return call_user_func_array(array('Mapper','get'),$arguments);            
        }        
        
        $trace = debug_backtrace();
        trigger_error(
        'Undefined method "'. $name . '" on class ' .  get_class($this) .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
        return null;
        
    }
    
    /**
     * needed to support model storage in session
     */
    public function __wakeup()
    {
        Mapper::_fill_relationship($this);
    }
    
    public function __isset($name) {
        // we'll check if the unset variable is part of the model
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        if (array_key_exists($name,$properties)){
            $reflection = new ReflectionProperty($class_name, $name);
            if ($reflection->isPublic() ){
                // is part of the model (but lazy load)
                return true;
            }
        }
        else{
            // not part of the model
            return false;
        }
    }

    function __set($name, $value){
        // we'll check if the unset variable is part of the model
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        if (array_key_exists($name,$properties)){
            $reflection = new ReflectionProperty($class_name, $name);
            if ($reflection->isPublic() ){
                // is part of the model!!
                // force the loading of the current value before setting the new one
                //if (!Mapper::_is_relationship_loaded($this, $name))
                Mapper::_load_relationship($this,$name);

                // set the non accesible internal property
                $this->$name = $value;
            }
        }
        else{
            // if it's not part of the model we'lldirectly set the property
            $this->$name = $value;
        }
    }
    
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
    // kind of events or hooks called by the mappers
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