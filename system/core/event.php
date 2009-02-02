<?php
class Event{
    protected static $_listeners=array();
    
    
    /**
     * Used to add a new callback to an event. If the event does not already exist, it will be created.
     */
    public static function add($eventName, $function){
        
        if (!is_callable($function,true)) throw new InvalidArgumentException("Second Argument must be callable");
        
        if (!isset(self::$_listeners[strtolower($eventName)]))
        self::$_listeners[strtolower($eventName)]=array();
        
        self::$_listeners[strtolower($eventName)][]=$function;
        
    }
    /**
     * Clear all callbacks from an event.
     */
    public static function clear($eventName){
        die('NOOOO');
        unset(self::$_listeners[strtolower($eventName)]);
    }
    
    /**
     * Clear one callback from an event.
     */
    public static function remove($eventName,$function){
        if (!isset(self::$_listeners[strtolower($eventName)])) return false;
        $index=array_search($function,self::$_listeners[strtolower($eventName)],true);
        if ($index!==false){
            unset(self::$_listeners[strtolower($eventName)][$index]);
            return true;
        }
        return false;
    }
    
    /**
     * Used to replace a callback with another callback in an event.
     */
    public static function replace($eventName, $oldfunction, $newfunction){
        if (!isset(self::$_listeners[strtolower($eventName)])) return false;
        $index=array_search($oldfunction,self::$_listeners[strtolower($eventName)],true);
        if ($index!==false){
            self::$_listeners[strtolower($eventName)][$index]=$newfunction;
            return true;
        }
        return false;
    }
    
    /**
     * Execute all of the callbacks attached to an event.
     * 
     * 
     *@param string $eventName the name of the event to run
     *@param mixed $referenced is any variable passed by reference that can by modified bu thecallbacks
     */
    public static function run($eventName,&$referenced=null){
        
        $return=null;
        
        if (!isset(self::$_listeners[strtolower($eventName)])) return false;
        
        foreach (self::$_listeners[strtolower($eventName)] as $function){
            $args=func_get_args();
            array_shift($args);
            array_shift($args);
            
            $parameters=array(&$referenced);
            foreach ($args as $param){
                $parameters[]=$param;
            }
            
            if (is_callable($function))
            $return=call_user_func_array($function,$parameters);
        }
        return $return;
    }
    
    
}
?>