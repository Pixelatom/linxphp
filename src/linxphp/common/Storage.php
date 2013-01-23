<?php
namespace linxphp\common;
/**
 * Storage is a type of object that can store values as properties, and can be accesed
 * as array, iterated, counted, etc.
 *
 */
class ValueStorage implements Countable , Iterator , Traversable ,  ArrayAccess {
    protected $position = 0;
    protected $array = array();
    
    // properties setters and getters
    public function __set($name, $value)
    {   
        $this->array[$name] = $value;
    }

    public function &__get($name)
    {
        if (array_key_exists($name, $this->array)) {
            return $this->array[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
     
        return null;
    }
    
    public function __isset($name)
    {
        return isset($this->array[$name]);
    }

    public function __unset($name)
    {
        unset($this->array[$name]);
    }
    
    // countable methods
    public function count() 
    { 
        return count($this->array); 
    }
    
    
    // iterable methods
    public function rewind() {
    
        $this->position = 0;
    }

    public function current() {
    
        return $this->array[$this->position];
    }

    public function key() {
    
        return $this->position;
    }

    public function next() {
    
        ++$this->position;
    }

    public function valid() {
    
        return isset($this->array[$this->position]);
    }
    
    // array access methods
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->array[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->array[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }
}
