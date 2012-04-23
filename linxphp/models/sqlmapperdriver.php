<?php

class SQLMapperDriver implements IMapperDriver {

    protected $escape = '';

    

    // a little cache for storing ongoing processing in recursive functions
    protected $cache = null;

    protected function get_sql_table_schema($model) {
        // check if this is the first call of this recursive function and 
        // starts the cache
        $first_call = false;
        if ($this->cache === null) {
            $first_call = true;
            $this->cache = array();
        }

        if (is_object($model)) {
            $class_name = get_class($model);
        } else {
            $class_name = $model;
        }

        if (is_object($model)) {
            $cache_id = spl_object_hash($model);
        } else {
            $cache_id = $class_name;
        }

        // uses the recursive cache to avoid entering in a loop
        if (isset($this->cache[$cache_id])) {
            $schema = $this->cache[$cache_id];
            return $schema;
        } else {
            $this->cache[$cache_id] = array();
            $schema = &$this->cache[$cache_id];
        }

        $obj_schema = ModelDescriptor::describe($model);
        

        $schema['table_name'] = $obj_schema['type'];

        if (isset($obj_schema['attributes']['table']))
            $schema['table_name'] = $obj_schema['attributes']['table'];

        $schema['fields'] = array();

        $schema['primary_key'] = array();

        // we'll add all properties as fields in the SQL table description
        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {
            
            if (!$property_attributes['attributes']['is_relationship']) {
                // scalar value property

                $field = array();

                $field['name'] = $property_name;
                
                $field['default'] = $property_attributes['default_value'];

                $field['value'] = $property_attributes['value'];

                $length = (int) ((isset($property_attributes['attributes']['length'])) ? $property_attributes['attributes']['length'] : '');

                $type = 'VARCHAR';
                if (!empty($length))
                    $type .= "($length)";
                else
                    $type .= "(255)";

                $pdo_bind_params = array('data_type' => PDO::PARAM_STR, 'length' => (!empty($length)) ? $length : 255);

                if (isset($property_attributes['attributes']['type'])) {
                    switch ($property_attributes['attributes']['type']) {
                        case 'string':
                        case 'varchar':
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
                        case 'smallint':
                        case 'tinyint':
                        case 'float':
                        //numeric(p,s)
                        case 'char':
                        case 'real':
                        case 'datetime':
                            $type = strtoupper($property_attributes['attributes']['type']);
                            break;

                        default:
                            # unrecognized type!
                            # run an event to proccess the unrecognized type
                            $type = $property_attributes['attributes']['type'];
                            Event::run('mapper.data_type_declaration', $type, $pdo_bind_params);                            
                            break;
                    }
                } else {
                    # default type if it's not set
                    $type = 'VARCHAR(255)';
                }

                if ($field['value'] === null)
                    $pdo_bind_params = array('data_type' => PDO::PARAM_NULL);

                $field['pdo_bind_params'] = $pdo_bind_params;
                $field['data_type'] = $type;

                if (isset($property_attributes['attributes']['primary_key'])
                        and $property_attributes['attributes']['primary_key'] == true) {
                    $schema['primary_key'][] = $property_name;
                    $field['primary_key'] = true;
                }

                $schema['fields'][$property_name] = $field;
            }
            else{
                // relationship property
                
                $type_classname = $property_attributes['attributes']['type'];
                $type_schema = ModelDescriptor::describe($type_classname);

                # we're going to define fore keys for this relationship
                if (!isset($property_attributes['attributes']['relationship']['type'])) {
                    # relationship must be defined in comments!
                    throw new Exception("relationship attribute must be defined for field $property_name in model {$obj_schema['type']} ");
                }
                //die(    $property_attributes['attributes']['relationship']['type']);
                switch ($property_attributes['attributes']['relationship']['type']) {
                    case 'childs':
                        // parents doesnt need a sql field for their childs
                        break;
                    case 'parent':
                        // childs must define the relationship to a parent in SQL
                        if (is_object($property_attributes['value']))
                            $type_sql_schema = $this->get_sql_table_schema($property_attributes['value']); // if the relationship is not empty we'll get the values for the fields
                        else
                            $type_sql_schema = $this->get_sql_table_schema($type_classname);// if the relationship is empty we'll get the schema from the class name

                        // for each pk on the relationship object we'll create a forekey field on our table
                        foreach ($type_sql_schema['primary_key'] as $type_primary_key) {
                            // we're going to copy the declaration of the primary keys
                            // to build the fore keys
                            $forekey = $property_name . '_' . $type_primary_key;

                            $field = array(); 
                            ///echo '<pre>';
                            //var_dump($type_sql_schema);
                            //$field['data_type'] = 'VARCHAR';
                            //$field['pdo_bind_params'] = array('data_type' => PDO::PARAM_STR, 'length' => 255);

                            if (!isset($type_sql_schema['fields'][$type_primary_key]['pdo_bind_params'])){
                                throw new Exception("Foreign Key Declaration Failure: Field {$type_classname}::{$type_primary_key} must be declared before {$classname}::{$property_name}");
                            }                    
                            $field['pdo_bind_params'] = $type_sql_schema['fields'][$type_primary_key]['pdo_bind_params'];
                            //if (isset($type_sql_schema['fields'][$type_primary_key]['data_type']))
                            $field['data_type'] = $type_sql_schema['fields'][$type_primary_key]['data_type'];
                            
                            $field['default'] = $type_sql_schema['fields'][$type_primary_key]['default'];

                            // if it's a lazy_load property and we have the temporal id value we'll use it                            
                            if (is_object($model) and isset($model->$forekey)) {                                
                                $field['value'] = $model->$forekey;
                            } elseif (is_object($model) and !isset($model->$forekey) and isset($model->$property_name->$type_primary_key)) {                                
                                $field['value'] = $model->$property_name->$type_primary_key;// si es una instancia con la relación asignada usamos el valor que tenemos en memoria o forzamos la carga
                            } else {
                                $field['value'] = $type_sql_schema['fields'][$type_primary_key]['value'];
                            }

                            // check if this forekey is pk at the same time
                            if (isset($property_attributes['attributes']['primary_key'])
                                    and $property_attributes['attributes']['primary_key'] == true) {
                                $schema['primary_key'][] = $forekey;
                                $field['primary_key'] = true;
                            }

                            // add the real SQL field as forekey
                            $schema['fields'][$forekey] = $field;
                        }
                        break;
                }
            }
        }

        // if this is the first call of this recursive function
        // we'll reset the cache
        if ($first_call == true) {
            $this->cache = null;
        }


        return $schema;
    }

    protected function get_unique_identifier($model){
        //for model instances we add a new property describing the unique identifier
        $unique = array();
        $schema = ModelDescriptor::describe($model);
        foreach ($schema['properties'] as $property_name => $property_attributes) {
            if (array_key_exists('primary_key', $property_attributes['attributes'])
                    and $property_attributes['attributes']['primary_key'] == true) {

                // for non relationship properties
                if (!$property_attributes['attributes']['is_relationship'] and !is_null($schema['properties'][$property_name]['value']))
                    $unique[$property_name] = $schema['properties'][$property_name]['value'];

                // relation properties
                if ($property_attributes['attributes']['is_relationship'] and
                        $property_attributes['attributes']['relationship']['type'] == 'parent') {

                    $type_classname = $property_attributes['attributes']['type'];
                    $type_schema = ModelDescriptor::describe($type_classname);

                    foreach ($type_schema['primary_key'] as $type_primary_key) {
                        $forekey = $property_name . '_' . $type_primary_key;

                        // if it's a lazy_load property and we have the temporal id value we'll use it
                        if (is_object($model) and isset($model->$forekey)) {
                            $unique[$forekey] = $model->$forekey;
                        } elseif (is_object($model) and !isset($model->$forekey) and isset($model->$property_name->$type_primary_key)) {
                            $unique[$forekey] = $model->$property_name->$type_primary_key; // si es una instancia con la relación asignada usamos el valor que tenemos en memoria o forzamos la carga
                        } else {
                            $unique[$forekey] = $type_schema['properties'][$type_primary_key]['value'];
                        }
                    }
                }
            }
        }
        return $unique;
    }

    /*
     * Stupid function
     */

    public function save($object) {
        $unique = $this->get_unique_identifier($object);
        
        /* TODO mysql update if exists */
        /* TODO count how many recrds with the same id */
        // check if the object was loaded or if it's new
        if (!empty($unique) and $this->exists(get_class($object), $unique)) {
            return Mapper::update($object);
        } else {
            return Mapper::insert($object);
        }
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

        $obj_schema = ModelDescriptor::describe($object);

        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {

            // saves only CHILD properties
            if (isset($property_attributes['attributes']['relationship']['type']) and $property_attributes['attributes']['relationship']['type'] == 'childs') {
                // save child objects using inverse property name
                if (is_object($object->$property_name)) {
                    if ($property_attributes['attributes']['type'] != get_class($object->$property_name)) {
                        throw new Exception("Wrong class type in child object");
                    }

                    # we're going to define fore keys for this relationship
                    if (!isset($property_attributes['attributes']['relationship']['inverse_property'])) {
                        # relationship must be defined in comments!
                        throw new Exception("inverse_property attribute must be defined for field $property_name in model {$obj_schema['type']} ");
                    }

                    if (isset($property_attributes['attributes']['relationship']['inverse_property'])) {
                        $inverse = $property_attributes['attributes']['relationship']['inverse_property'];

                        $object->$property_name->$inverse = $object;
                    }

                    $count += Mapper::save($object->$property_name);
                } elseif (is_array($object->$property_name)) {

                    foreach ($object->$property_name as $child) {

                        if (isset($property_attributes['attributes']['relationship']['inverse_property'])) {

                            $inverse = $property_attributes['attributes']['relationship']['inverse_property'];

                            $child->{$inverse} = $object;
                        }
                        $count += Mapper::save($child);
                    }
                }
            }
        }
        return $count;
    }

    protected static $table_exists_cache = array();
    protected function table_exists($tableName) {

        if (!isset(self::$table_exists_cache[$tableName])){
            // Other RDBMS.  Graceful degradation
            $exists = true;
            try{
                $cmdOthers = "select 1 from {$this->escape}" . $tableName . "{$this->escape} where 1 = 0";
                db::query($cmdOthers);
            }
            catch (Exception $e){
                $exists = false;
            }
            self::$table_exists_cache[$tableName] = $exists;
        }
        else{
            $exists = self::$table_exists_cache[$tableName];
        }

        return $exists;
    }

    /*
     * CRUD functions
     */

    /**
     * build and execute the insert SQL statement for a single model
     * @param <type> $object
     * @return <type>
     */
    protected function _insert($object) {
        // quantity of entities saved (to be returned by the function)
        $count = 0;

        $sql_schema = $this->get_sql_table_schema($object);

        $fields_names = array();
        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['fields'] as $field => $attributes) {

            $fields_names[] = $field;

            $value = $attributes['value'];

            if (!is_scalar($value) and !$value == null)
                throw new Exception('Incorrect value type for field: '.$field.' (It must be scalar, '.gettype($value) .' given)');


            # proccess value with hooks (just in case it needs to be processed)
            Event::run('mapper.process_field_value', $field, $attributes, $value);
            $fields_values[':' . $field] = $value;
            $bind_params[':' . $field] = $attributes['pdo_bind_params'];
        }

        $fields = $this->escape.implode("{$this->escape},{$this->escape}", $fields_names).$this->escape;

        $params = implode(',', array_keys($fields_values));


        $sql = "INSERT INTO {$this->escape}{$sql_schema['table_name']}{$this->escape} ($fields) VALUES ($params)";

        //throw new Exception(print_r($fields_values,true));

        $count += db::execute($sql, $fields_values, $bind_params);

        return $count;
    }

    public function insert($object) {

        $sql_schema = $this->get_sql_table_schema($object);
        if (!$this->table_exists($sql_schema['table_name'])) {
            $this->create_table($object);
        }

        $count = $this->_insert($object);

        if ($count > 0) {
            // LOAD and save childs properties
            $count += $this->save_relationships($object);
        }

        return $count;
    }

    public function update($object) {
        $sql_schema = $this->get_sql_table_schema($object);
        if (!$this->table_exists($sql_schema['table_name'])) {
            $this->create_table($object);
            return false;
        }

        // quantity of entities saved (to be returned by the function)
        $count = 0;

        $sql_schema = $this->get_sql_table_schema($object);

        $field_updates = '';
        $fields_values = array();
        $bind_params = array();
        foreach ($sql_schema['fields'] as $field => $attributes) {
            if (!empty($field_updates))
                $field_updates .= ", ";

            $field_updates .= "{$this->escape}$field{$this->escape} = :$field";

            $value = $attributes['value'];
            //$value = $object->{$attributes['name']};


            if (!is_scalar($value) and !$value == null)
                throw new Exception('Incorrect value type for field: '.$field.' (It must be scalar, '.gettype($value) .' given)');

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

            $where_id .= "{$this->escape}$key{$this->escape} = :$key";
        }


        $sql = "UPDATE {$this->escape}{$sql_schema['table_name']}{$this->escape}
        SET $field_updates
        WHERE $where_id";


        $count += db::execute($sql, $fields_values, $bind_params);

        $count += $this->save_relationships($object);

        return $count;
    }

    protected function delete_relationships($object) {
        $count = 0;
        // force loading of lazy load properties
        $this->fill_relationship($object, true);

        $obj_schema = ModelDescriptor::describe($object);

        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {

            if (isset($property_attributes['attributes']['relationship']['type']) and $property_attributes['attributes']['relationship']['type'] == 'childs') {

                if (is_object($object->$property_name)) {
                    if ($property_attributes['attributes']['type'] != get_class($object->$property_name)) {
                        throw new Exception("Wrong class type in child object");
                    }
                    $count += $this->delete($object->$property_name);
                } elseif (is_array($object->$property_name)) {
                    foreach ($object->$property_name as $child) {
                        $count += Mapper::delete($child);
                    }
                }

                $object->$property_name = null;
            }
        }

        return $count;
    }

    public function delete($object, $delete_childs=true) {
        $sql_schema = $this->get_sql_table_schema($object);

        if (!$this->table_exists($sql_schema['table_name'])) {
            $this->create_table($object);
            $this->insert($object);
            return;
        }

        
        $sql_schema = $this->get_sql_table_schema($object);

        $count = 0;

        if ($delete_childs) {
            $count += $this->delete_relationships($object);
        }

        $where_id = '';



        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['primary_key'] as $key) {
            if (!empty($where_id))
                $where_id .= " AND ";

            $where_id .= "{$this->escape}$key{$this->escape} = :$key";

            $attributes = $sql_schema['fields'][$key];

            $value = $attributes['value'];

            # proccess value with hooks (just in case it needs to be processed)
            Event::run('mapper.process_field_value', $key, $attributes, $value);
            $fields_values[':' . $key] = $value;
            $bind_params[':' . $key] = $attributes['pdo_bind_params'];
        }


        $sql = "DELETE FROM {$this->escape}{$sql_schema['table_name']}{$this->escape} WHERE $where_id";


        $count += db::execute($sql, $fields_values, $bind_params);

        

        return $count;
    }

    /*
     * Misc function
     */

    protected function create_table($object) {
        $sql_schema = $this->get_sql_table_schema($object);
        
        // unset table_exists cache value
        if (isset(self::$table_exists_cache[$sql_schema['table_name']]))
        unset(self::$table_exists_cache[$sql_schema['table_name']]);
        
        if (count($sql_schema['primary_key']) == 0) {
            throw new Exception('Objects must have a PRIMARY ID property.');
        }
        # field declarations
        $fields_declaration = "";
        foreach ($sql_schema['fields'] as $field => $attributes) {

            $declaration = "$field {$attributes['data_type']}";

            if (isset($attributes['default'])){
                $declaration .= ' NOT NULL DEFAULT  \''. addslashes($attributes['default']) .'\'';
            }

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

        $sql = "CREATE TABLE {$this->escape}{$sql_schema['table_name']}{$this->escape}
        ({$fields_declaration}
        )";

        db::execute($sql);
    }

    

    /*
     * utility function
     */

    protected function build_select_query($classname, $conditions=null, $order_by=null) {
        $obj_schema = ModelDescriptor::describe($classname);
        $sql_schema = $this->get_sql_table_schema($classname);

        if (!$this->table_exists($sql_schema['table_name'])) {
            $this->create_table($classname);   
        }

        $sql = "SELECT distinct {$this->escape}{$sql_schema['table_name']}{$this->escape}.* FROM {$this->escape}{$sql_schema['table_name']}{$this->escape}";


        $processed_paths = array();
        if (!empty($conditions) or !empty($order_by)) {

            // we'll search for possible uses of extended fieds that may need joins
            preg_match_all('/[^\'"]{0,1}(?P<joinfield>['.$this->escape.']{0,1}\w+['.$this->escape.']{0,1}(?:\.['.$this->escape.']{0,1}\w+['.$this->escape.']{0,1})+)[^\'"]{0,1}/', ' '.$conditions . '  ' . $order_by . ' ', $result, PREG_PATTERN_ORDER);



            foreach ($result["joinfield"] as $field) {
                $field = str_replace($this->escape, '', $field);
                $tables = explode('.', $field);

                $path = array(
                    $sql_schema['table_name'] => $sql_schema['table_name'],
                );
                $pathstring = $sql_schema['table_name'];
                $realpath = '';
                $prev_pathstring = '';

                $type_schema = $obj_schema;
                $type_sql_schema = $sql_schema;


                foreach ($tables as $property_name) {
                    // non existing property are ignored
                    if (!isset($type_schema['properties'][$property_name]))
                        continue;

                    $property_attributes = $type_schema['properties'][$property_name];

                    // if property is not a relationship we'll stop here
                    if (!$property_attributes['attributes']['is_relationship'])
                        break;

                    $prev_pathstring = $pathstring;

                    if (count($path) > 1) {
                        $pathstring .= '_';
                        $realpath .= '.';
                        $pathstring .= $property_name;
                        $realpath .= $property_name;
                    } else {
                        $pathstring = $property_name;
                        $realpath = $property_name;
                    }

                    $path[$realpath] = $pathstring;

                    //if (count($path) == count($tables)  ) break;
                    //echo $property_name.'<br>';
                    // previous object schema to be used on the joins
                    $prev_sql_schema = $type_sql_schema;
                    $prev_obj_schema = $type_schema;

                    // current part of the path schemas
                    $type_classname = $property_attributes['attributes']['type'];
                    $type_schema = ModelDescriptor::describe($type_classname);
                    $type_sql_schema = $this->get_sql_table_schema($type_classname);

                    if (in_array($pathstring, $processed_paths))
                        continue;
                    $processed_paths[$realpath] = $pathstring;


                    if (!$this->table_exists($type_sql_schema['table_name'])) {
                        $this->create_table($type_classname);
                    }
                    /*
                      // we asume first part of the field is already included
                      if (count($path)==1) continue;
                     */


                    # we're going to define fore keys for this relationship
                    if (!isset($property_attributes['attributes']['relationship']['type'])) {
                        # relationship must be defined in comments!
                        throw new Exception("relationship attribute must be defined for field $property_name in model {$obj_schema['type']} ");
                    }
                    $join_condition = '';

                    switch ($property_attributes['attributes']['relationship']['type']) {
                        case 'childs':
                            foreach ($prev_sql_schema['primary_key'] as $primary_key) {

                                if (!empty($join_condition))
                                    $join_condition .= " AND ";


                                # we're going to define fore keys for this relationship
                                if (!isset($property_attributes['attributes']['relationship']['inverse_property'])) {
                                    # relationship must be defined in comments!
                                    throw new Exception("inverse_property attribute must be defined for field $property_name in model {$prev_obj_schema['type']} ");
                                }

                                $inverse_property = $property_attributes['attributes']['relationship']['inverse_property'];
                                $field = $inverse_property . '_' . $primary_key;

                                $join_condition .= " {$this->escape}{$pathstring}{$this->escape}.{$this->escape}$field{$this->escape} = {$this->escape}{$prev_pathstring}{$this->escape}.{$this->escape}$primary_key{$this->escape} ";
                            }
                            break;
                        case 'parent':

                            foreach ($type_sql_schema['primary_key'] as $primary_key) {

                                if (!empty($join_condition))
                                    $join_condition .= " AND ";

                                $field = $property_name . '_' . $primary_key;

                                $join_condition .= " {$this->escape}{$prev_pathstring}{$this->escape}.{$this->escape}$field{$this->escape} = {$this->escape}{$pathstring}{$this->escape}.{$this->escape}$primary_key{$this->escape} ";
                            }
                            break;
                    }

                    $sql .= " left join {$this->escape}{$type_sql_schema['table_name']}{$this->escape} {$this->escape}{$pathstring}{$this->escape}  on $join_condition ";
                }
            }
        }

        if (!empty($conditions))
            $sql .= " WHERE $conditions";

        if (!empty($order_by))
            $sql .= " ORDER BY $order_by";

        // order values from bigger to smaller strings to replace
        arsort($processed_paths);

        // in case the sql contains extra join we will replace them for their alias
        foreach ($processed_paths as $search => $replace) {
            $search = str_replace($this->escape, '', $search);
            $sql = preg_replace('/([^\'"]{0,1}['.$this->escape.']{0,1})(' . preg_quote($search) . ')(['.$this->escape.']{0,1}[^\'"]{0,1})/', '$1' . $replace . '$3', $sql);
        }
        /*
          if (!empty($processed_paths))
          die($sql);
        */
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
     * return true if the ID passed as argument already exists on database
     * @param <type> $classname
     * @param <type> $id
     * @return <type>
     */
    protected function exists($classname, $id) {
        $sql_schema = $this->get_sql_table_schema($classname);


        if (!$this->table_exists($sql_schema['table_name'])) {
            $this->create_table($classname);
            //return;
        }
        $where_id = '';

        if (count($sql_schema['primary_key']) != count($id)) {
            throw new Exception('Incorrect number of values for primary key');
        }

        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['primary_key'] as $key) {
            if (!empty($where_id))
                $where_id .= " AND ";

            $where_id .= "{$this->escape}$key{$this->escape} = :$key";

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


        $sql = "select count(1)  FROM {$this->escape}{$sql_schema['table_name']}{$this->escape}";

        $sql .= " WHERE $where_id";



        return db::query_scalar($sql, $fields_values, $bind_params) > 0;
    }

    /**
     * get object by id, but without using cache
     * @param <type> $classname
     * @param <type> $id
     * @return <type>
     */
    public function get_by_id($classname, $id) {
        $sql_schema = $this->get_sql_table_schema($classname);


        if (!$this->table_exists($sql_schema['table_name'])) {
            $this->create_table($classname);
        }
        $where_id = '';

        if (count($sql_schema['primary_key']) != count($id)) {
            throw new Exception('Incorrect number of values for primary key');
        }

        $fields_values = array();
        $bind_params = array();

        foreach ($sql_schema['primary_key'] as $key) {
            if (!empty($where_id))
                $where_id .= " AND ";

            $where_id .= "{$this->escape}$key{$this->escape} = :$key";

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


        $sql = "SELECT * FROM {$this->escape}{$sql_schema['table_name']}{$this->escape}";

        $sql .= " WHERE $where_id";

        if (!empty($order_by))
            $sql .= " ORDER BY $order_by";


        $results = db::query($sql, $fields_values, $bind_params, $classname);


        if (isset($results[0])) {
            
            Mapper::_fill_relationship($results[0]);

            $object = $results[0];

            

            return $object;
        }
        else
            return;
    }

    

    
        
    

    public function get($classname, $conditions=null, $order_by=null) {
        
        $sql = $this->build_select_query($classname, $conditions, $order_by);

        $return = db::query($sql, $fields_values = array(), $bind_params = array(), $classname);

        foreach ($return as &$object) {
             Mapper::_fill_relationship($object);
        }

        return $return;
    }


    /**
     *
     * @param <type> $object
     * @param <type> $property_name
     * @param <type> $conditions
     * @param <type> $order_by
     * @return mixed relationship model/s
     */
    public function get_relationship($object, $property_name,$child_conditions=null, $order_by=null) {
        $obj_schema = ModelDescriptor::describe($object);
        $sql_schema = $this->get_sql_table_schema($object);

        $property_attributes = $obj_schema['properties'][$property_name];

        if ($property_attributes['attributes']['is_relationship']) {

            $type_classname = $property_attributes['attributes']['type'];
            $type_schema = ModelDescriptor::describe($type_classname);
            $type_sql_schema = $this->get_sql_table_schema($type_classname);

            # we're going to define fore keys for this relationship

            switch ($property_attributes['attributes']['relationship']['type']) {
                case 'childs':

                    # we're going to define fore keys for this relationship
                    if (!isset($property_attributes['attributes']['relationship']['inverse_property'])) {
                        # relationship must be defined in comments!
                        throw new Exception("inverse_property attribute must be defined for field $property_name in model {$obj_schema['type']} ");
                    }

                    $conditions = '';

                    foreach ($sql_schema['primary_key'] as $primary_key) {

                        if (!empty($conditions))
                            $conditions .= " AND ";

                        $value = $sql_schema['fields'][$primary_key]['value'];

                        // wrong condition builder, must use inverse property name instead property name
                        // $field = $type_sql_schema['table_name'] . '.' . $primary_key;
                        $field = $property_attributes['attributes']['relationship']['inverse_property'] . '_' . $primary_key;


                        $conditions .= " {$this->escape}{$type_sql_schema['table_name']}{$this->escape}.{$this->escape}$field{$this->escape} = '$value' ";
                    }

                    if (!empty($child_conditions)){
                        $conditions = "({$conditions}) AND ({$child_conditions})";
                    }

                    /* TODO: el parametro conditions del get no me gusta mucho porque los valores no se pueden pasar como parametros */
                    // parents doesnt need a sql property for their childs
                    return Mapper::get($type_classname, $conditions,$order_by);


                    break;
                case 'parent':

                    $fore_keys = array();

                    foreach ($type_sql_schema['primary_key'] as $type_primary_key) {


                        $sql_field = $property_name . '_' . $type_primary_key;


                        // if the id is null then there is not an object related to it
                        if (!isset($object->$sql_field) or $object->$sql_field == NULL) {
                            $object->$property_name = null;
                            // shall we break?
                        } else {
                            $fore_keys[$type_primary_key] = $object->$sql_field;
                        }

                        if (!in_array($sql_field, array_keys($obj_schema['properties'])))
                            unset($object->$sql_field); # remove property because it was not defined in the original class
                    }


                    if (!empty($fore_keys)){
                        return Mapper::get_by_id($type_classname, $fore_keys);
                    }

                    break;
            }
        }
    }

    /**
     * Load a relationship property. Used by the lazy_load mechanism.
     * @param <type> $object
     * @param <type> $property_name
     */
    public function _load_relationship($object, $property_name) {
        $obj_schema = ModelDescriptor::describe($object);
        if ($obj_schema['properties'][$property_name]['attributes']['is_relationship']){
            $object->$property_name = Mapper::get_relationship($object, $property_name);
        }
    }

    /**
     * returns true when a relationship property is loaded, false if not loaded yet (when using lazy_load)
     * to determinate when a lazy load property is loaded we use the following logic:
     * - childs: si la propiedad es un array. (no se que es si no esta cargada. me preocupa pensar que puede estar trayendo todo el array )
     * - parent: si estan seteadas las propiedades temporales (forekeys) que hacen referencia al registro padre quiere decir que todavia no se cargó el modelo, pero tenemos un problema, si serializamos el objeto sin cargar el lazy_load, cuando se desserialice quizas perdamos las vars temporales.
     */
    public function _is_relationship_loaded($object, $property_name){
        $obj_schema = ModelDescriptor::describe($object);
        $sql_schema = $this->get_sql_table_schema($object);
        $property_attributes = $obj_schema['properties'][$property_name];
        if ($property_attributes['attributes']['is_relationship']) {

            $type_classname = $property_attributes['attributes']['type'];
            $type_schema = ModelDescriptor::describe($type_classname);
            $type_sql_schema = $this->get_sql_table_schema($type_classname);

            # we're going to define fore keys for this relationship

            switch ($property_attributes['attributes']['relationship']['type']) {
                case 'childs':
                    return isset($object->$property_name) and is_array($object->$property_name);                    
                case 'parent':                    
                    $fore_keys = array();
                    // guess the name of the temporal forekey property on the object
                    foreach ($type_sql_schema['primary_key'] as $type_primary_key) {
                        $sql_field = $property_name . '_' . $type_primary_key;
                        return !isset($object->$sql_field);
                    }
            }
        }
        else 
            return true;
    }

    public function _fill_relationship($object){
        self::fill_relationship($object);
    }
    /*
     * Complete relationship properties on load
     */

    protected function fill_relationship($object, $force_loading=false) {

        $obj_schema = ModelDescriptor::describe($object);


        foreach ($obj_schema['properties'] as $property_name => $property_attributes) {

            // check only properties of an object type
            if (isset($property_attributes['attributes']['relationship']) and isset($property_attributes['attributes']['type']) and class_exists($property_attributes['attributes']['type'])) {
                // first we check the property doesnt do lazy load
                if ($property_attributes['attributes']['relationship']['lazy_load'] == false) {
                    // only fill null properties
                    if (is_null($object->$property_name)) {
                        Mapper::_load_relationship($object, $property_name);
                    }
                } elseif ($property_attributes['attributes']['relationship']['lazy_load'] == true) {
                    // property with lazy load
                    if (!$this->_is_relationship_loaded($object,$property_name)) {
                        if ($force_loading == true /* and $property_attributes['attributes']['relationship']['type'] == 'parent' */)
                        //force loading only by referencing the property
                            $object->$property_name;
                        else
                        // if the property is lazy_load , we'll unset it now, to enable magic methods on Model classes that will load it when requested
                            unset($object->$property_name);
                    }
                }
            }
        }
    }

}

?>
