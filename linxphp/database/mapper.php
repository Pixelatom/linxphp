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

       

    /*
     * instances functions
     * every object is stored in instances so it's possible to use always the
     * same instance of an object
     */

    static protected function add_to_instances($object) {
        $classname = get_class($object);

        $id = ModelDescriptor::get_id($object);

        $key = md5($classname . json_encode($id));

        Registry::set($key, $object);
    }

    protected function is_object_in_instances($object) {
        $classname = get_class($object);

        $id = ModelDescriptor::get_id($object);

        return self::is_in_instances($classname, $id);
    }

    protected function is_in_instances($classname, $id) {

        $key = md5($classname . json_encode($id));

        return Registry::exists($key);
    }

    protected function get_object_from_instances($object) {
        $classname = get_class($object);

        $id = ModelDescriptor::get_id($object);

        $key = md5($classname . json_encode($id));

        return Registry::get($key);
    }

    protected function get_from_instances($classname, $id) {
        
        $key = md5($classname . json_encode($id));

        return Registry::get($key);
    }

    /*
     * End instances functions
     */


    static public function save($object) {
        self::setup();
        return self::$driver->save($object);
    }

    static public function insert($object) {
        self::setup();
        
        $object->_before_insert();
        $return = self::$driver->insert($object);
        $object->_after_insert();

        return $return;
    }

    static public function update($object) {
        self::setup();
        
        $object->_before_update();
        $return = self::$driver->update($object);
        $object->_after_update();

        return $return;
    }

    static public function delete($object, $delete_childs=true) {
        self::setup();
        $object->_before_delete();
        $return = self::$driver->delete($object, $delete_childs);
        $object->_after_delete();
        return $return;
    }

    static public function count($classname, $conditions=null) {
        self::setup();
        return self::$driver->count($classname, $conditions );
    }

    static public function get_by_id($classname, $id) {
        self::setup();

        if (self::is_in_instances($classname, $id))
            return self::get_from_instances($classname,$id);
        

        $return = self::$driver->get_by_id($classname, $id);
        
        if (!empty($return)){
            $return->_after_load();
            self::add_to_instances($return);
        }

        return $return;
    }

    static public function get($classname, $conditions=null, $order_by=null, $limit = null, $offset = 0) {
        self::setup();
        $args = func_get_args();
        $return = call_user_func_array(array(self::$driver, "get"), $args);

        foreach ($return as &$object) {

            // revisa si cada uno de los objetos retornados esta en cache,
            // y si no es asi los guardamos, si ya estan guardados retornamos la instancia que ya existe
            if (self::is_object_in_instances($object)) {
                // objects in cache are supposed to be already filled

                $object = self::get_object_from_instances($object);
            } else {
                self::add_to_instances($object);
                $object->_after_load();
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
