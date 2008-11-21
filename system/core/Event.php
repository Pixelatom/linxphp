<?php
class Event{
    protected static $_listeners=array();   
    public static function add($eventName, $function){
        if (!is_callable($function,true)) throw new InvalidArgumentException("Second Argument must be callable");
        self::$_listeners[strtolower($eventName)][]=$function;
    }
    public static function clear($eventName){
        unset(self::$_listeners[strtolower($eventName)]);
    }
    public static function remove($eventName,$function){
        if (!isset(self::$_listeners[strtolower($eventName)])) return false;
        $index=array_search($function,self::$_listeners[strtolower($eventName)],true);
        if ($index!==false){
            unset(self::$_listeners[strtolower($eventName)][$index]);
            return true;
        }
        return false;
    }
    
    public static function replace($eventName, $oldfunction, $newfunction){
        if (!isset(self::$_listeners[strtolower($eventName)])) return false;
        $index=array_search($oldfunction,self::$_listeners[strtolower($eventName)],true);
        if ($index!==false){
            self::$_listeners[strtolower($eventName)][$index]=$newfunction;
            return true;
        }
        return false;
    }
    
    public static function run($eventName,&$arg=null){
        if (!isset(self::$_listeners[strtolower($eventName)])) return false;
        foreach (self::$_listeners[strtolower($eventName)] as $function){
            if (is_callable($function))            
            return call_user_func_array($function,array(&$arg));
        }
    }
    
    
}
?>