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

    static protected $driver = array();

    static protected $registered_drivers = array(
        'mysql' => 'MySQLMapperDriver',
    );

    static public function register_driver($drivername, $classname){
        self::$registered_drivers[$drivername] = $classname;
    }

    static public function set_driver(IMapperDriver $driver,$configuration = 'database') {
        self::$driver[$configuration] = $driver;
    }

    static protected function setup($configuration = 'database') {
        if (!isset(self::$driver[$configuration])) {
            $drivername = DB::connect($configuration)->get_pdolink()->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if (array_key_exists($drivername, self::$registered_drivers)){
                $classname = self::$registered_drivers[$drivername];
                self::set_driver(new $classname(),$configuration);
            }
            else{
                self::set_driver(new SQLMapperDriver(),$configuration);
            }
        }
    }

    static public function save($object) {
        $schema = ModelDescriptor::describe($object);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->save($object);
    }

    static public function insert($object) {
        $schema = ModelDescriptor::describe($object);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->insert($object);
    }

    static public function update($object) {
        $schema = ModelDescriptor::describe($object);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->update($object);
    }

    static public function delete($object, $delete_childs=true) {
        $schema = ModelDescriptor::describe($object);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->delete($object, $delete_childs);
    }

    static public function count($classname, $conditions=null) {
        $schema = ModelDescriptor::describe($classname);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->count($classname, $conditions );
    }

    static public function get_by_id($classname, $id) {
        $schema = ModelDescriptor::describe($classname);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->get_by_id($classname, $id);
    }

    static public function get($classname, $conditions=null, $order_by=null, $limit = null, $offset = 0) {
        $schema = ModelDescriptor::describe($classname);
        $configuration = $schema['connection'];
        self::setup($configuration);

        $args = func_get_args();
        return call_user_func_array(array(self::$driver[$configuration], "get"), $args);
        //return self::$driver->get($classname, $conditions , $order_by );
    }

    static public function _load_relationship($object, $property_name) {
        $schema = ModelDescriptor::describe($object);
        $configuration = $schema['connection'];
        self::setup($configuration);

        return self::$driver[$configuration]->_load_relationship($object, $property_name);
    }

}
?>
