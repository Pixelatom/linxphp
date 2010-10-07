<?php
abstract class Model{
    // protected methods accesible only for 'friends' classes
    protected function _before_insert(){
    }
    protected function _before_update(){
    }
    protected function _before_delete(){
    }
    protected function _after_insert(){
    }
    protected function _after_update(){
    }
    protected function _after_delete(){
    }
    protected function _after_load(){
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
                case '_after_insert':
                    return $this->_after_insert($arguments);
                    break;
                case '_after_update':
                    return $this->_after_update($arguments);
                    break;
                case '_after_delete':
                    return $this->_after_delete($arguments);
                    break;
                case '_after_load':
                    return $this->_after_load($arguments);
                    break;
            }
        }
        $class_name = get_class($this);
        trigger_error(
            'Undefined method '.$class_name.'::' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
    }
    function  __get($name) {
       
        $class_name = get_class($this);
        $function = new ReflectionClass($class_name);
        $properties = $function->getDefaultProperties();

        $mapper = new Mapper();
        
        if (array_key_exists($name,$properties)){
            $reflection = new ReflectionProperty($class_name, $name);
            if ($reflection->isPublic() ){
                $mapper->_load_relationship($this,$name);
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
}