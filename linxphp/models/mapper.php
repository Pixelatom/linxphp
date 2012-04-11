<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mapper
 *
 * @author JaViS
 */
class Mapper {

    /* functions for configure the driver */ 
    static protected $driver = null;

    static protected $registered_drivers = array(
        'mysql' => 'MySQLMapperDriver',
    );

    static public function register_driver($drivername, $classname){
        self::$registered_drivers[$drivername] = $classname;
    }

    static public function set_driver(IMapperDriver $driver) {
        self::$driver = $driver;
    }

    static protected function setup() {
        if (self::$driver == null) {
            $drivername = DB::get_pdolink()->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if (array_key_exists($drivername, self::$registered_drivers)){
                $classname = self::$registered_drivers[$drivername];
                self::set_driver(new $classname());    
            }
            else{
                self::set_driver(new SQLMapperDriver());    
            }
        }
    }

    
    static protected $config = array(
        'use_cache' => false,
        'cache_ttl' => 86400,
        'cache_registry_ttl' => 186400,
    );

    /**
     * the following keys are used to configure mapper:
     * `use_cache` sets if the Mapper should cache sql results
     * `cache_ttl` time of cache expiration in seconds
     * `cache_registry_ttl` time of cache registry expiration in seconds (must be bigger than cache_ttl)
     * @param <type> $settings
     */
    static public function config($settings = array()) {
        self::$config = array_merge(self::$config, $settings);        
    }
       

    /*
     * instances functions
     * every object is stored in instances so it's possible to use always the
     * same instance of an object
     */
    static protected function add_to_instances($object) {
        $classname = get_class($object);
        $id = ModelDescriptor::get_id($object);
        $key = md5(strtolower($classname) . json_encode($id));
        Registry::set($key, $object);
    }

    protected function is_object_in_instances($object) {
        $classname = get_class($object);
        $id = ModelDescriptor::get_id($object);
        return self::is_in_instances($classname, $id);
    }

    protected function is_in_instances($classname, $id) {
        $key = md5(strtolower($classname) . json_encode($id));
        return Registry::exists($key);
    }

    protected function get_object_from_instances($object) {
        $classname = get_class($object);
        $id = ModelDescriptor::get_id($object);
        $key = md5(strtolower($classname) . json_encode($id));
        return Registry::get($key);
    }

    protected function get_from_instances($classname, $id) {
        $key = md5(strtolower($classname) . json_encode($id));
        return Registry::get($key);
    }

    /*
     * End instances functions
     */

    /* cache functions */
    static protected function get_cache_registry(){
        
        $registry = SimpleCache::fetch('mapper_cache_registry');
        if (!is_array($registry)){
            $registry = array();
        }        
        return $registry;
    }

    static protected function cache_fetch($class_name,$conditions){
        $key = strtolower("{$class_name}_{$conditions}");
        return SimpleCache::fetch($key);
    }

    static protected function cache_store($data,$class_name,$conditions){
        $class_name = strtolower($class_name);
        $key = strtolower("{$class_name}_{$conditions}");
        $ttl = self::$config['cache_ttl'];

        SimpleCache::store($key, $data, $ttl);       

        $registry = self::get_cache_registry();
        $registry[$class_name][$key] = json_encode(array('class_name'=>$class_name,'conditions'=>$conditions));
        SimpleCache::store('mapper_cache_registry', $registry, self::$config['cache_registry_ttl']);
    }

    static protected function cache_delete($class_name,$conditions){
        $class_name = strtolower($class_name);
        $key = strtolower("{$class_name}_{$conditions}");

        SimpleCache::delete($key);
        
        $registry = self::get_cache_registry();
        if (isset($registry[$class_name][$key]))
        unset($registry[$class_name][$key]);

        SimpleCache::store('mapper_cache_registry', $registry, self::$config['cache_registry_ttl']);
    }
    
    static protected function cache_delete_section($class_name){
        $class_name = strtolower($class_name);
        $registry = self::get_cache_registry();
        if (isset($registry[$class_name])){
            foreach($registry[$class_name] as $key=>$value){
                SimpleCache::delete($key);
            }
        }
        // delete all cache refered to this model class name        
        $registry = self::get_cache_registry();

        if (isset($registry[$class_name]))
        unset($registry[$class_name]);

        SimpleCache::store('mapper_cache_registry', $registry, self::$config['cache_registry_ttl']);
    }


    static public function save($object) {
        self::setup();
        $return = self::$driver->save($object);
        return $return;
    }

    static public function insert($object) {
        self::setup();
        
        $object->_before_insert();
        $return = self::$driver->insert($object);
        $object->_after_insert();

        // removes all the cache generated for this class
        if (self::$config['use_cache']){
            self::cache_delete_section(get_class($object));
        }

        return $return;
    }

    static public function update($object) {
        self::setup();
        
        $object->_before_update();
        $return = self::$driver->update($object);
        $object->_after_update();

        // removes all the cache generated for this class
        if (self::$config['use_cache']){
            self::cache_delete_section(get_class($object));
        }

        return $return;
    }

    static public function delete($object, $delete_childs=true) {
        self::setup();
        $object->_before_delete();
        $return = self::$driver->delete($object, $delete_childs);
        $object->_after_delete();

        // removes all the cache generated for this class
        if (self::$config['use_cache']){
            self::cache_delete_section(get_class($object));
        }

        return $return;
    }

    static public function count($classname, $conditions=null) {
        self::setup();
        if (self::$config['use_cache']){
            $cached = self::cache_fetch($classname, 'count:'.$conditions);
            if ($cached !== false) return $cached;
        }
        $return = self::$driver->count($classname, $conditions);        
        if (self::$config['use_cache']){
            self::cache_store($return, $classname, 'count:'.$conditions);
        }
        return $return;
    }

    static public function get_by_id($classname, $id) {
        self::setup();

        /* normalizar parametro $id para poder identificarlo en instances y cache */
        $d = ModelDescriptor::describe($classname);
        $nid = array();
        if (count($d['primary_key']) != count($id)) throw new Exception('Incorrect number of values for primary key');
        
        foreach ($d['primary_key'] as $key) {
            if (!is_array($id))
                $value = $id;
            else {
                if (!isset($id[$key])) throw new Exception("Missing key '$key' in primary keys argument");
                $value = $id[$key];
            }
            $nid[$key] = $value;
        }
        asort($nid);
        $id = $nid; // normalized id array

        // first check in current instances
        if (self::is_in_instances($classname, $id))
            return self::get_from_instances($classname,$id);
        
        $return = false;

        // then check in cache
        if (self::$config['use_cache']){
            $return = self::cache_fetch($classname, json_encode($id));            
        }
        // then check in database
        if ($return === false){
            $return = self::$driver->get_by_id($classname, $id);            
        }

        if (!empty($return)){
            if (self::$config['use_cache']){
                self::cache_store($return, $classname, json_encode($id));
            }

            $return->_after_load();
            self::add_to_instances($return);
        }

        return $return;
    }

    static public function get($classname, $conditions=null, $order_by=null, $limit = null, $offset = 0) {
        self::setup();
        $args = func_get_args();
        
        $return = false;

        // try cache
        if (self::$config['use_cache'])
        $return = self::cache_fetch($classname, json_encode($args));

        // get from DB
        if ($return === false){
            $return = call_user_func_array(array(self::$driver, "get"), $args);            
        }
        
        // save on cache
        if (self::$config['use_cache']){
            self::cache_store($return, $classname, json_encode($args));
        }

        // add to instances and executes events
        foreach ($return as &$object) {
            // revisa si cada uno de los objetos retornados esta en cache,
            // y si no es asi los guardamos, si ya estan guardados retornamos la instancia que ya existe
            if (self::is_object_in_instances($object)) {
                // objects in cache are supposed to be already filled
                $object = self::get_object_from_instances($object);
            } else {
                $object->_after_load();
                self::add_to_instances($object);
            }
        }

        return $return;
    }

    /**
     *
     * @param <type> $object
     * @param <type> $property_name
     * @param <type> $child_conditions
     * @param <type> $order_by
     * @return <type>
     */
    static public function get_relationship($object, $property_name ,$child_conditions=null, $order_by=null){
        self::setup();
        return self::$driver->get_relationship($object, $property_name ,$child_conditions, $order_by);
    }

    // these following methods are used internally by the models objects
    /**
     * load all relationships in an object
     */
    static public function _fill_relationship($object) {
        self::setup();
        return self::$driver->_fill_relationship($object);
    }
    /**
     * load one relationship property
     */
    static public function _load_relationship($object, $property_name) {
        self::setup();
        return self::$driver->_load_relationship($object, $property_name);
    }
    /**
     * returns true if the relationship property is already loaded
     */
    static public function _is_relationship_loaded($object, $property_name) {
        self::setup();
        return self::$driver->_is_relationship_loaded($object, $property_name);
    }

}
?>
