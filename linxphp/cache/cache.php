<?php
class Cache{
    static protected $_engine = null;

    static protected $_config = array(
        'engine' => 'FileCache',
    );

    /**
     * The following keys are used in core cache engines:
     * - `engine`
     * - `prefix` Prefix appended to all entries. Good for when you need to share a keyspace
     *    with either another cache config or annother application.     
     * - `servers' Used by memcache. Give the address of the memcached servers to use.
     * - `path` Used by FileCache.  Path to where cachefiles should be saved.
     */
    static public function config($settings = array()) {
        self::$_config = array_merge(self::$_config, $settings);
        self::$_engine = null;
    }

    static protected function init(){
        if (!is_object(self::$_engine)){
            $class_name = self::$_config['engine'] . 'Engine';
            if (!class_exists($class_name))
                throw new Exception ($message= "Cache Class '$class_name' not found");
            self::$_engine = new $class_name(self::$_config);
        }
    }

    static public function fetch($key){
        self::init();
        return self::$_engine->fetch($key);
    }
    static public function store($key,$data,$ttl){
        self::init();
        return self::$_engine->store($key,$data,$ttl);
    }
    static public function delete($key){
        self::init();
        return self::$_engine->delete($key);
    }


}