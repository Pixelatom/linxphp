<?php

/*
  ANSI data type	Oracle        MySql           PostGreSQL            Most Portable
  integer         NUMBER(38)    integer(11)     integer               integer
  smallint        NUMBER(38)    smallint(6)     smallint              smallint
  tinyint         *	tinyint(4)  *               numeric(4,0)
  numeric(p,s)    NUMBER(p,s)   decimal(p,s)    numeric(p,s)          numeric(p,s)
  varchar(n)      VARCHAR2(n)   varchar(n)      character varying(n)	varchar(n)
  char(n)         CHAR(n)       varchar(n)      character(n)          char(n)
  datetime        DATE          datetime        timestamp no timezone have to autodetect
  float           FLOAT(126)    float           double precision      float
  real            FLOAT(63)     double          real                  real
 */

class Mapper {
    

    

    

     protected function get_class_schema($class_name) {
        return ModelDescriptor::describe($class_name);
    }

     protected function get_object_schema($object) {
        return ModelDescriptor::describe($object);
    }

     // a little cache for storing ongoing processing in recursive functions
     protected $cache = null;

    
     protected function get_sql_table_schema($model) {
        // check if this is the first call of this recursive function and 
        // starts the cache
        $first_call = false;
        if ($this->cache === null){
            $first_call = true;
            $this->cache = array();
        }
       
        if (is_object($model)){
            $class_name = get_class($model);
        }
        else{
            $class_name = $model;
        }

        if (is_object($model)){
            $cache_id = spl_object_hash($model);
        }
        else{
            $cache_id = $class_name;
        }

        // uses the recursive cache to avoid entering in a loop
        if (isset($this->cache[$cache_id])){
            $schema = $this->cache[$cache_id];
            return $schema;
        }
        else{
            $this->cache[$cache_id] = array();
            $schema = &$this->cache[$cache_id];
        }
        
        $obj_schema = $this->get_class_schema($model);

        $schema['table_name'] = $obj_schema['type'];

        if (isset($obj_schema['attributes']['table']))
            $schema['table_name'] = $obj_schema['attributes']['table'];

        $schema['fields'] = array();

        $schema['primary_key'] = array();


        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {
            //echo 'property: '.$property_name.'<br/>';

            $field = array();

            $field['name'] = $property_name;

            $field['value'] = $property_attributes['value'];

            $length = (int) (isset($property_attributes['attributes']['length'])) ? $property_attributes['attributes']['length'] : '';

            if (isset($property_attributes['attributes']['primary_key'])
                    and $property_attributes['attributes']['primary_key'] == true) {
                $schema['primary_key'][] = $property_name;
                $field['primary_key'] = true;
            }
            $type = 'VARCHAR(255)';

            $pdo_bind_params = array('data_type' => PDO::PARAM_STR, 'length' => (!empty($length)) ? $length : 255);

            if (isset($property_attributes['attributes']['type'])) {
                switch ($property_attributes['attributes']['type']) {
                    case 'string':


                        $type = 'VARCHAR';
                        if (!empty($length))
                            $type .= "($length)";
                        else
                            $type .= "(255)";

                        $pdo_bind_params = array('data_type' => PDO::PARAM_STR, 'length' => (!empty($length)) ? $length : 255);

                        break;
                    case 'integer':

                        $pdo_bind_params = array('data_type' => PDO::PARAM_INT);
                        $type = 'INTEGER';
                        break;
                    /*
                      case 'float':
                      $type = 'FLOAT';
                      break;
                      case 'date':
                      $type = 'DATE';
                      break;
                      case 'datetime':
                      $type = 'DATETIME';
                      break;
                     */
                    default:
                        # unrecognized type! let's see if it's a class name

                        if (class_exists($property_attributes['attributes']['type'])) {

                            $type_classname = $property_attributes['attributes']['type'];
                            $type_schema = $this->get_class_schema($type_classname);

                            # we're going to define fore keys for this relationship
                            if (!isset($property_attributes['attributes']['relationship']['type'])) {
                                # relationship must be deffined in comments!
                                throw new Exception("relationship attribute must be deffined for field $property_name in mode {$obj_schema['type']} ");
                            }
                            //die(    $property_attributes['attributes']['relationship']['type']);
                            switch ($property_attributes['attributes']['relationship']['type']) {
                                case 'childs':
                                    // parents doesnt need a sql property for their childs
                                    break;
                                case 'parent':
                                    // childs must define the relationship to a parent in SQL
                                    // we will add fore keys to this table
                                    // force loading just in case it's lazy load
                                    // TODO: remove force loading from here.
                                    //if (is_object($object_or_classname))
                                    //$property_attributes['value'] = $object_or_classname->$property_name;

                                    if (is_object($property_attributes['value']))
                                    // if the relationship is not empty we'll get the values for the fields
                                        $type_sql_schema = $this->get_sql_table_schema($property_attributes['value']);
                                    else
                                    // if the relationship is empty we'll get the schema from the class name
                                        $type_sql_schema = $this->get_sql_table_schema($type_classname);

                                    foreach ($type_sql_schema['primary_key'] as $type_primary_key) {
                                        // we're going to copy the declaration of the primary keys
                                        // to build the fore keys
                                        $field = array();
                                        $field['value'] = $type_sql_schema['fields'][$type_primary_key]['value'];
                                        $field['pdo_bind_params'] = $type_sql_schema['fields'][$type_primary_key]['pdo_bind_params'];
                                        $field['data_type'] = $type_sql_schema['fields'][$type_primary_key]['data_type'];

                                        $schema['fields'][$property_name . '_' . $type_primary_key] = $field;
                                    }

                                    break;
                            }
                            // continue with next property
                            continue 2; // 2 because the sentence SWITCH is considered a loop structure :O
                        }

                        # run an event to proccess the unrecognized type
                        $type = Event::run('mapper.data_type_declaration', $property_attributes['attributes']['type'], $pdo_bind_params);
                        break;
                }
            } else {
                # default type if it's not set
                $type = 'VARCHAR(255)';
            }

            if ($field['value'] == null)
                $pdo_bind_params = array('data_type' => PDO::PARAM_NULL);

            $field['pdo_bind_params'] = $pdo_bind_params;
            $field['data_type'] = $type;

            $schema['fields'][$property_name] = $field;
        }

        // if this is the first call of this recursive function
        // we'll reset the cache
        if ($first_call == true){
            $this->cache = null;
        }


        return $schema;
    }

    /*
     * Stupid function
     */

     public function save($object) {
        $update = $this->update($object);
        if ($update == 0) {
            try {
                return $this->insert($object);
            } catch (PDOException $e) {
                // if there where an update == 0 because the object wasn't modified
                // then the insert will give an duplicated id error
                // we chatch here
            }
        }
        return $update;
    }

    /*
     * saves childs properties
     * @TODO: avoid force loading of lazy load properties
     * instead do not save childs elements never loaded
     */
     protected function save_relationships($object) {
        $count = 0;
        // force loading of lazy load properties
        $this->fill_relationship($object, true);

        $obj_schema = $this->get_object_schema($object);

        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {


            if (isset($property_attributes['attributes']['relationship']['type']) and $property_attributes['attributes']['relationship']['type'] == 'childs') {
                // save child objects using inverse property name
                if (is_object($object->$property_name)) {
                    if ($property_attributes['attributes']['type'] != get_class($object->$property_name)) {
                        throw new Exception("Wrong class type in child object");
                    }

                    if (isset($property_attributes['attributes']['relationship']['inverse_property'])) {
                        $inverse = $property_attributes['attributes']['relationship']['inverse_property'];

                        $object->$property_name->$inverse = $object;
                    }

                    $count += $this->save($object->$property_name);
                } elseif (is_array($object->$property_name)) {

                    foreach ($object->$property_name as $child) {

                        if (isset($property_attributes['attributes']['relationship']['inverse_property'])) {

                            $inverse = $property_attributes['attributes']['relationship']['inverse_property'];

                            $child->{$inverse} = $object;
                        }
                        $count += $this->save($child);
                    }
                }
            }
        }
        return $count;
    }

    /*
     * CRUD functions
     */

     public function insert(&$object) {

        $sql_schema = $this->get_sql_table_schema($object);
        if (!db::table_exists($sql_schema['table_name'])) {
            $this->create_table($object);
        }

        $object->_before_insert();
        
        // LOAD and save childs properties
        $count = $this->save_relationships($object);

        $sql_schema = $this->get_sql_table_schema($object);
        

        

        $fields_names = array();
        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['fields'] as $field => $attributes) {

            $fields_names[] = $field;

            $value = $attributes['value'];

            if (!is_scalar($value) and !$value == null)
                throw new Exception('Field Values must be scalars!');


            # proccess value with hooks (just in case it needs to be processed)
            Event::run('mapper.process_field_value', $field, $attributes, $value);
            $fields_values[':' . $field] = $value;
            $bind_params[':' . $field] = $attributes['pdo_bind_params'];
        }

        $fields = implode(',', $fields_names);

        $params = implode(',', array_keys($fields_values));


        $sql = "INSERT INTO {$sql_schema['table_name']} ($fields) VALUES ($params)";

        $count += db::execute($sql, $fields_values, $bind_params);
        return $count;
    }

     public function update($object) {
        $sql_schema = $this->get_sql_table_schema($object);
        if (!db::table_exists($sql_schema['table_name'])) {
            $this->create_table($object);
            return false;
        }

        $object->_before_update();

        $count = $this->save_relationships($object);
        $sql_schema = $this->get_sql_table_schema($object);
        
        $field_updates = '';
        $fields_values = array();
        $bind_params = array();
        foreach ($sql_schema['fields'] as $field => $attributes) {
            if (!empty($field_updates))
                $field_updates .= ", ";

            $field_updates .= "$field = :$field";

            $value = $attributes['value'];
            //$value = $object->{$attributes['name']};


            if (!is_scalar($value) and !$value == null)
                throw new Exception('Field Values must be scalars!');

            # proccess value with hooks (just in case it needs to be processed)
            Event::run('mapper.process_field_value', $field, $attributes, $value);
            $fields_values[':' . $field] = $value;
            $bind_params[':' . $field] = $attributes['pdo_bind_params'];
        }

        $where_id = '';

        if (count($sql_schema['primary_key']) == 0) {
            throw new Exception('Objects must have a PRIMARY ID property.');
        }

        foreach ($sql_schema['primary_key'] as $key) {
            if (!empty($where_id))
                $where_id .= " AND ";

            $where_id .= "$key = :$key";
        }


        $sql = "UPDATE {$sql_schema['table_name']}
        SET $field_updates
        WHERE $where_id";


        $count += db::execute($sql, $fields_values, $bind_params);
        return $count;
    }

     protected function delete_relationships($object) {
        // force loading of lazy load properties
        $this->fill_relationship($object, true);

        $obj_schema = $this->get_object_schema($object);

        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {

            if (isset($property_attributes['attributes']['relationship']['type']) and $property_attributes['attributes']['relationship']['type'] == 'childs') {

                if (is_object($object->$property_name)) {
                    if ($property_attributes['attributes']['type'] != get_class($object->$property_name)) {
                        throw new Exception("Wrong class type in child object");
                    }
                    $this->delete($object->$property_name);
                } elseif (is_array($object->$property_name)) {
                    foreach ($object->$property_name as $child) {
                        $this->delete($child);
                    }
                }

                $object->$property_name = null;
            }
        }
    }

     public function delete($object, $delete_childs=true) {
        $sql_schema = $this->get_sql_table_schema($object);

        if (!db::table_exists($sql_schema['table_name'])) {
            $this->create_table($object);
            $this->insert($object);
            return;
        }

        $object->_before_delete();
        $sql_schema = $this->get_sql_table_schema($object);

        if ($delete_childs) {
            $this->delete_relationships($object);
        }

        $where_id = '';



        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['primary_key'] as $key) {
            if (!empty($where_id))
                $where_id .= " AND ";

            $where_id .= "$key = :$key";

            $attributes = $sql_schema['fields'][$key];

            $value = $attributes['value'];

            # proccess value with hooks (just in case it needs to be processed)
            Event::run('mapper.process_field_value', $key, $attributes, $value);
            $fields_values[':' . $key] = $value;
            $bind_params[':' . $key] = $attributes['pdo_bind_params'];
        }


        $sql = "DELETE FROM {$sql_schema['table_name']} WHERE $where_id";


        return db::execute($sql, $fields_values, $bind_params);
    }

    /*
     * Misc function
     */

     protected function create_table($object) {
        $sql_schema = $this->get_sql_table_schema($object);
        if (count($sql_schema['primary_key']) == 0) {
            throw new Exception('Objects must have a PRIMARY ID property.');
        }
        # field declarations
        $fields_declaration = "";
        foreach ($sql_schema['fields'] as $field => $attributes) {

            $declaration = "$field {$attributes['data_type']}";

            if (isset($attributes['primary_key']) and $attributes['primary_key'] == true
                    and count($sql_schema['primary_key']) == 1) {

                $declaration .= ' PRIMARY KEY';
            }

            if (!empty($fields_declaration))
                $fields_declaration .= ", ";

            $fields_declaration .= "\n" . $declaration;
        }

        # composite primary key
        if (isset($sql_schema['primary_key']) and count($sql_schema['primary_key']) > 1) {
            $fields_declaration .= ", PRIMARY KEY (" . implode(',', $sql_schema['primary_key']) . ")";
        }

        $sql = "CREATE TABLE {$sql_schema['table_name']}
        ({$fields_declaration}
        )";

        db::execute($sql);
    }

    /*
     * Cache functions
     * every object is stored in cache so it's possible to use always the
     * same instance of an object
     */

     protected function add_to_cache($object) {
        $classname = get_class($object);

        $sql_schema = $this->get_sql_table_schema($object);

        $id = array();

        foreach ($sql_schema['primary_key'] as $key) {

            $value = $sql_schema['fields'][$key]['value'];

            $id[$key] = (string) $value;
        }

        $key = md5($classname . json_encode($id));



        Registry::set($key, $object);
    }

     protected function is_object_in_cache($object) {
        $classname = get_class($object);

        $sql_schema = $this->get_sql_table_schema($object);

        $id = array();

        foreach ($sql_schema['primary_key'] as $key) {

            $value = $sql_schema['fields'][$key]['value'];

            $id[$key] = (string) $value;
        }

        return $this->is_in_cache($classname, $id);
    }

     protected function is_in_cache($classname, $id) {
        if (!is_array($id)) {
            $sql_schema = $this->get_sql_table_schema($classname);
            $id = array($sql_schema['primary_key'][0] => (string) $id);
        }

        $key = md5($classname . json_encode($id));

        return Registry::exists($key);
    }

     protected function get_object_from_cache($object) {
        $classname = get_class($object);

        $sql_schema = $this->get_sql_table_schema($object);

        $id = array();

        foreach ($sql_schema['primary_key'] as $key) {

            $value = $sql_schema['fields'][$key]['value'];

            $id[$key] = (string) $value;
        }

        $key = md5($classname . json_encode($id));
        
        return Registry::get($key);
    }

     protected function get_from_cache($classname, $id) {
        if (!is_array($id)) {
            $sql_schema = $this->get_sql_table_schema($classname);
            $id = array($sql_schema['primary_key'][0] => (string) $id);
        }

        $key = md5($classname . json_encode($id));

        return Registry::get($key);
    }

    /*
     * End Cache functions
     */

    /*
     * utility function
     */

     protected function build_select_query($classname, $conditions=null, $order_by=null) {
        $obj_schema = $this->get_class_schema($classname);
        $sql_schema = $this->get_sql_table_schema($classname);

        if (!db::table_exists($sql_schema['table_name'])) {
            $this->create_table($classname);
            //return;
        }

        $sql = "SELECT distinct {$sql_schema['table_name']}.* FROM {$sql_schema['table_name']}";


        // create relationship properties with left joins
        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {
            if (isset($property_attributes['attributes']['type']) AND class_exists($property_attributes['attributes']['type'])) {

                $type_classname = $property_attributes['attributes']['type'];
                $type_schema = $this->get_class_schema($type_classname);
                $type_sql_schema = $this->get_sql_table_schema($type_classname);


                if (!db::table_exists($type_sql_schema['table_name'])) {
                    $this->create_table($type_classname);
                }

                # we're going to define fore keys for this relationship
                if (!isset($property_attributes['attributes']['relationship']['type'])) {
                    # relationship must be deffined in comments!
                    throw new Exception("relationship attribute must be deffined for field $property_name in mode {$obj_schema['type']} ");
                }
                $join_condition = '';

                switch ($property_attributes['attributes']['relationship']['type']) {
                    case 'childs':
                        foreach ($sql_schema['primary_key'] as $primary_key) {

                            if (!empty($join_condition))
                                $join_condition .= " AND ";
                            $inverse_property = $property_attributes['attributes']['relationship']['inverse_property'];
                            $field = $inverse_property . '_' . $primary_key;

                            $join_condition .= " {$property_name}.$field = {$sql_schema['table_name']}.$primary_key ";
                        }
                        break;
                    case 'parent':

                        foreach ($type_sql_schema['primary_key'] as $primary_key) {

                            if (!empty($join_condition))
                                $join_condition .= " AND ";

                            $field = $property_name . '_' . $primary_key;

                            $join_condition .= " {$sql_schema['table_name']}.$field = {$property_name}.$primary_key ";
                        }
                        break;
                }

                $sql .= " left join {$type_sql_schema['table_name']} {$property_name}  on $join_condition ";
            }
        }


        if (!empty($conditions))
            $sql .= " WHERE $conditions";

        if (!empty($order_by))
            $sql .= " ORDER BY $order_by";

        return $sql;
    }

    /*
     * SQL Functions wrapers
     */

     public function count($classname, $conditions=null) {
        $sql = $this->build_select_query($classname, $conditions);

        $sql = "select count(1) from ($sql) as selectquery";

        return db::query_scalar($sql);
    }

    /**
     * get object by id, but without using cache
     * @param <type> $classname
     * @param <type> $id
     * @return <type>
     */
    protected function _get_by_id($classname, $id) {
        $sql_schema = $this->get_sql_table_schema($classname);

        $where_id = '';

        if (count($sql_schema['primary_key']) != count($id)) {
            throw new Exception('Incorrect number of values for primary key');
        }

        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['primary_key'] as $key) {
            if (!empty($where_id))
                $where_id .= " AND ";

            $where_id .= "$key = :$key";

            $attributes = $sql_schema['fields'][$key];

            if (!is_array($id))
                $value = $id;
            else {
                if (!isset($id[$key]))
                    throw new Exception("Missing key '$key' in primary keys argument");

                $value = $id[$key];
            }

            # proccess value with hooks (just in case it needs to be processed)
            Event::run('mapper.process_field_value', $key, $attributes, $value);

            $fields_values[':' . $key] = $value;
            $bind_params[':' . $key] = $attributes['pdo_bind_params'];
        }


        $sql = "SELECT * FROM {$sql_schema['table_name']}";

        $sql .= " WHERE $where_id";

        if (!empty($order_by))
            $sql .= " ORDER BY $order_by";


        $results = db::query($sql, $fields_values, $bind_params, $classname);

        if (isset($results[0])) {
            $this->add_to_cache($results[0]);
            $this->fill_relationship($results[0]);
            return $results[0];
        }
        else
            return;
    }

    /*
     * Loads one object by id -uses cache
     */

     public function get_by_id($classname, $id) {
        
        if ($this->is_in_cache($classname, $id))
            return $this->get_from_cache($classname, $id);


        return $this->_get_by_id($classname, $id);
    }

     public function get($classname, $conditions=null, $order_by=null) {

        //$sql = call_user_func_array(array(self, 'build_select_query'), func_get_args());
        $sql = $this->build_select_query($classname, $conditions, $order_by);
//        if (empty($sql))
//            die($conditions);

        $return = db::query($sql, $fields_values = array(), $bind_params = array(), $classname);

        foreach ($return as &$object) {

            // revisa si cada uno de los objetos retornados esta en cache,
            // y si no es asi los guardamos, si ya estan guardados retornamos la instancia que ya existe
            if ($this->is_object_in_cache($object)) {
                // objects in cache are supposed to be already filled

                $object = $this->get_object_from_cache($object);
            } else {
                $this->add_to_cache($object);
                $this->fill_relationship($object);
            }
        }

        return $return;
    }

     public function _load_relationship($object, $property_name) {
        
        $obj_schema = $this->get_object_schema($object);
        $sql_schema = $this->get_sql_table_schema($object);

        $property_attributes = $obj_schema['properties'][$property_name];

        if (isset($property_attributes['attributes']['type']) AND class_exists($property_attributes['attributes']['type'])) {

            $type_classname = $property_attributes['attributes']['type'];
            $type_schema = $this->get_class_schema($type_classname);
            $type_sql_schema = $this->get_sql_table_schema($type_classname);

            # we're going to define fore keys for this relationship
            if (!isset($property_attributes['attributes']['relationship']['type'])) {
                # relationship must be deffined in comments!
                throw new Exception("relationship attribute must be deffined for field $property_name in mode {$obj_schema['type']} ");
            }

            switch ($property_attributes['attributes']['relationship']['type']) {
                case 'childs':

                    # we're going to define fore keys for this relationship
                    if (!isset($property_attributes['attributes']['relationship']['inverse_property'])) {
                        # relationship must be deffined in comments!
                        throw new Exception("inverse_property attribute must be deffined for field $property_name in mode {$obj_schema['type']} ");
                    }

                    $conditions = '';


                    
                    foreach ($sql_schema['primary_key'] as $primary_key) {

                        if (!empty($conditions))
                            $conditions .= " AND ";

                        $value = $sql_schema['fields'][$primary_key]['value'];

                       // wrong condition builder, must use inverse property name instead property name
                       // $field = $type_sql_schema['table_name'] . '.' . $primary_key;
                        $field =  $property_attributes['attributes']['relationship']['inverse_property'] . '_' . $primary_key;


                        $conditions .= " $field = '$value' ";
                    }




                    /* TODO: el parametro conditions del get no me gusta mucho porque los valores no se pueden pasar como parametros */
                    // parents doesnt need a sql property for their childs
                    $childs = $this->get($type_classname, $conditions);


                    // asignamos los childs at last

                    $object->$property_name = $childs;

                    break;
                case 'parent':
                    //

                    


                    $fore_keys = array();

                    foreach ($type_sql_schema['primary_key'] as $type_primary_key) {


                        $sql_field = $property_name . '_' . $type_primary_key;


                        // if the id is null then there is not an object related to it
                        if (!isset($object->$sql_field) or $object->$sql_field == NULL) {
                            $object->$property_name = null;
                        } else {
                            $fore_keys[$type_primary_key] = $object->$sql_field;
                        }

                        if (!in_array($sql_field, array_keys($obj_schema['properties'])))
                            unset($object->$sql_field);# remove property because it was not defined in the original class
                    }
                    

                    if (!empty($fore_keys)){

                        $object->$property_name = $this->get_by_id($type_classname, $fore_keys);
                        
                    }




                    break;
            }
        }
    }

    /*
     * Complete relationship properties on load
     */

     protected function fill_relationship($object, $force_loading=false) {

        $obj_schema = $this->get_object_schema($object);


        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {
            
                // check only properties of an object type
                if (isset($property_attributes['attributes']['type']) and class_exists($property_attributes['attributes']['type'])) {
                    // first we check the property doesnt do lazy load
                    if ((!array_key_exists('lazy_load', $property_attributes['attributes']) or $property_attributes['attributes']['relationship']['lazy_load'] === false)) {
                        // only fill null properties
                        if (is_null($object->$property_name)) {
                            Mapper::_load_relationship($object, $property_name);
                        }
                    } elseif (isset($property_attributes['attributes']['relationship']['lazy_load']) and $property_attributes['attributes']['relationship']['lazy_load'] == true) {
                        // rpoperty with lazy load
                        if (!isset($object->$property_name)) {
                            if ($force_loading == true /*and $property_attributes['attributes']['relationship']['type'] == 'parent'*/)
                            //force loading only by referencing the property
                                $object->$property_name;
                            else
                            // if the property is set to be loaded when requested, we'll unset it now
                                unset($object->$property_name);

                        }

                        
                    }
                }
            
        }
    }

}
?>
