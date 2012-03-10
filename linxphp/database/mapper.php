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

    static public function save($object) {
        self::setup();
        return self::$driver->save($object);
    }

    static public function insert($object) {
        self::setup();
        return self::$driver->insert($object);
    }

    static public function update($object) {
        self::setup();
        return self::$driver->update($object);
    }

    static public function delete($object, $delete_childs=true) {
        self::setup();
        return self::$driver->delete($object, $delete_childs);
    }

    static public function count($classname, $conditions=null) {
        self::setup();
        return self::$driver->count($classname, $conditions );
    }

    static public function get_by_id($classname, $id) {
        self::setup();
        return self::$driver->get_by_id($classname, $id);
    }

    static public function get($classname, $conditions=null, $order_by=null, $limit = null, $offset = 0) {
        self::setup();
        $args = func_get_args();
        return call_user_func_array(array(self::$driver, "get"), $args);
        //return self::$driver->get($classname, $conditions , $order_by );
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
