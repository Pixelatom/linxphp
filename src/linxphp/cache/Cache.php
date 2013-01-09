<?php
namespace linxphp\cache;

/**
 * Cache is a class that wraps up different PHP cache drivers though one simple class 
 * that allows you to encapsulate the caching layer of your application.
 */
class Cache {

    protected $_driver = null;
    // default configuration of the cache class
    protected $_config = array(
        'driver' => 'File',
        'servers' => array('localhost'),
        'prefix' => '',
        'compress' => false,
    );
    
    public function __construct($settings = array()) {
        $this->config($settings);
        $this->init();
    }

    /**
     * The following keys are used in core cache drivers:
     * - `driver`
     * - `prefix` Prefix appended to all entries. Good for when you need to share a keyspace
     *    with either another cache config or annother application.     
     * - `servers' Used by memcache. Give the address of the memcached servers to use.
     * - `path` Used by FileCache.  Path to where cachefiles should be saved.
     * @param <array> $settings the settings in array form
     */
    public function config($settings = array()) {
        $this->_config = array_merge($this->_config, $settings);
        $this->_driver = null;
    }

    /**
     * internal setup function
     */
    protected function init() {
        
        $class_name = $this->_config['driver'];
        
        if (in_array($class_name, array('APC','File','Memcache'))){
            $class_name = __NAMESPACE__ . '\\Drivers\\' . $class_name;
        }

        if (!class_exists($class_name))
            throw new Exception($message = "Cache Driver Class '$class_name' not found");

        if (!is_object($this->_driver) or (get_class($this->_driver) != $class_name)) {            
            $this->setDriverObject(new $class_name($this->_config));
        }
    }

    protected function setDriverObject(\Cache\Driver $driver) {
        $this->_driver = $driver;
    }

    /**
     * retrieves the value stored under the key name
     * or false if the cache doesn't exists or if it's expired
     * @param <string> $key
     * @return <mixed> the value stored or false
     */
    public function fetch($key) {
        self::init();
        return $this->_driver->fetch($key);
    }

    /**
     * Stores a value in the cache under a key name
     * @param <string> $key the name of the key
     * @param <mixed> $data the value to be stored
     * @param <integer> $ttl expiration time in seconds
     * @return <boolean> returns true if the value was stored
     */
    public function store($key, $data, $ttl = 0) {
        self::init();
        return $this->_driver->store($key, $data, $ttl);
    }

    /**
     * deletes a value from the cache driver
     * @param <string> $key the name of the key to be deleted
     * @return <boolean> true if the key was
     */
    public function delete($key) {
        self::init();
        return $this->_driver->delete($key);
    }

}