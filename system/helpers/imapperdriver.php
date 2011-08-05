<?php

/**
 *
 * @author JaViS
 */
interface IMapperDriver {
    public function save($object);
    public function insert($object);
    public function update($object);
    public function delete($object, $delete_childs=true);
    public function count($classname, $conditions=null);
    public function get_by_id($classname, $id);
    public function get($classname, $conditions=null, $order_by=null);
    public function _load_relationship($object, $property_name);

}
?>
