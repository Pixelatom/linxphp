<?php

/**
 * extension of Mapper for supporting MySQL specific stuff like pagination
 * and autoincrement and that kind of things
 *
 * @author JaViS
 */
class MySQLMapperDriver extends SQLMapperDriver {

    protected $escape = '`';

    protected function _insert($object) {
        $return = parent::_insert($object);
        if ($return > 0) {
            $d = ModelDescriptor::describe($object);
            foreach ($d['properties'] as $property => $property_attributes) {
                if ((isset($property_attributes['attributes']['auto_increment'])
                        and $property_attributes['attributes']['auto_increment'] == true)) {
                    $object->$property = db::get_last_insert_id($property);
                }
            }
        }
        return $return;
    }

    protected function get_sql_table_schema($object_or_classname) {
        $obj_schema = ModelDescriptor::describe($object_or_classname);
        
        $schema = parent::get_sql_table_schema($object_or_classname);

        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {

            if ((isset($property_attributes['attributes']['auto_increment'])
                    and $property_attributes['attributes']['auto_increment'] == true)) {

                if (array_search($property_name, $schema['primary_key']) === false)
                    $schema['primary_key'][] = $property_name;
                $schema['fields'][$property_name]['primary_key'] = true;
                $schema['fields'][$property_name]['auto_increment'] = true;
            }
        }

        return $schema;
    }

    /**
     * @todo soporte para autoincrement
     * @param <type> $object 
     */
    protected function create_table($object) {
        if(is_object($object))
           $sql_schema = $this->get_sql_table_schema(get_class($object));
       else
           $sql_schema = $this->get_sql_table_schema($object);

        # field declarations
        $fields_declaration = "";
        foreach ($sql_schema['fields'] as $field => $attributes) {

            $declaration = "{$this->escape}$field{$this->escape} {$attributes['data_type']}";

            if (isset($attributes['auto_increment']) and $attributes['auto_increment'] == true) {
                $declaration .= ' AUTO_INCREMENT';
            }

            if (isset($attributes['default'])) {
                $declaration .= ' NOT NULL DEFAULT  \'' . addslashes($attributes['default']) . '\'';
            }

            if (((isset($attributes['auto_increment']) and $attributes['auto_increment'] == true)
                    OR
                    (isset($attributes['primary_key']) and $attributes['primary_key'] == true)
                    ) and count($sql_schema['primary_key']) == 1) {

                $declaration .= ' PRIMARY KEY';
            }



            if (!empty($fields_declaration))
                $fields_declaration .= ", ";

            $fields_declaration .= "\n" . $declaration;
        }

        # composite primary key
        if (isset($sql_schema['primary_key']) and count($sql_schema['primary_key']) > 1) {
            $fields_declaration .= ", PRIMARY KEY ({$this->escape}" . implode("{$this->escape},{$this->escape}", $sql_schema['primary_key']) . "{$this->escape})";
        }

        $sql = "CREATE TABLE {$sql_schema['table_name']}
        ({$fields_declaration}
        )";

        //echo $sql;

        db::execute($sql);
    }

    protected function build_select_query($classname, $conditions=null, $order_by=null, $limit = null, $offset = 0) {
        $sql = parent::build_select_query($classname, $conditions, $order_by);

        if (!is_null($limit)) {
            $sql .= " LIMIT $offset, $limit";
        }

        //echo $sql;
        return $sql;
    }

    public function get($classname, $conditions=null, $order_by=null, $limit = null, $offset = 0) {


        $sql = $this->build_select_query($classname, $conditions, $order_by, $limit, $offset);

        $return = db::query($sql, $fields_values = array(), $bind_params = array(), $classname);

        foreach ($return as &$object) {
            $this->fill_relationship($object);            
        }

        return $return;
    }

}
?>
