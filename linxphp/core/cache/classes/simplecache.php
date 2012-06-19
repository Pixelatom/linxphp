<?php
/**
 * SimpleCache is a class that wraps up different PHP cache engines though one simple class 
 * that allows you to encapsulate the caching layer of your application.
 */
class SimpleCache{
    static protected $_engine = null;

    // default configuration of the cache class
    static protected $_config = array(
        'engine' => 'File',
        'servers' => array('localhost'),
        'prefix' => '',
        'compress' => false,
    );

    /**
     * The following keys are used in core cache engines:
     * - `engine`
     * - `prefix` Prefix appended to all entries. Good for when you need to share a keyspace
     *    with either another cache config or annother application.     
     * - `servers' Used by memcache. Give the address of the memcached servers to use.
     * - `path` Used by FileCache.  Path to where cachefiles should be saved.
     * @param <array> $settings the settings in array form
     */
    static public function config($settings = array()) {
        self::$_config = array_merge(self::$_config, $settings);
        self::$_engine = null;
    }

    /**
     * internal setup function
     */
    static protected function init(){
        if (!is_object(self::$_engine)){
            $class_name = self::$_config['engine'] . 'Engine';
            if (!class_exists($class_name))
                throw new Exception ($message= "Cache Class '$class_name' not found");
            self::$_engine = new $class_name(self::$_config);
        }
    }

    /**
     * retrieves the value stored under the key name
     * or false if the cache doesn't exists or if it's expired
     * @param <string> $key
     * @return <mixed> the value stored or false
     */
    static public function fetch($key){
        self::init();
        return self::$_engine->fetch($key);
    }

    /**
     * Stores a value in the cache under a key name
     * @param <string> $key the name of the key
     * @param <mixed> $data the value to be stored
     * @param <integer> $ttl expiration time in seconds
     * @return <boolean> returns true if the value was stored
     */
    static public function store($key,$data,$ttl){
        self::init();
        return self::$_engine->store($key,$data,$ttl);
    }

    /**
     * deletes a value from the cache engine
     * @param <string> $key the name of the key to be deleted
     * @return <boolean> true if the key was
     */
    static public function delete($key){
        self::init();
        return self::$_engine->delete($key);
    }


}