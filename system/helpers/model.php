<?php
abstract class Model{
    // protected methods accesible only for 'friends' classes
    protected function _before_insert(){
    }
    protected function _before_update(){
    }
    protected function _before_delete(){
    }
    // simulation of a Friend Class methods
    public function __call($name, $arguments) {

        $trace = debug_backtrace();

        
        if(isset($trace[2]['class']) && ($trace[2]['class'] == 'Mapper' or in_array('Mapper', class_parents($trace[2]['class'])))) {
            switch ($name){
                case '_before_insert':
                    return $this->_before_insert($arguments);
                    break;
                case '_before_update':
                    return $this->_before_update($arguments);
                    break;
                case '_before_delete':
                    return $this->_before_delete($arguments);
                    break;
            }
        }
        // normal __set() code here
        trigger_error('Cannot access private method ' . __CLASS__ . '::$' . $name, E_USER_ERROR);
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