<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Mapper{

  /* options */
  static protected $_convert_to_lowercase = true;

  static protected function get_attributes($comments_string){
    $attributes = preg_replace('%/\*\*|^\s*?\*/\s*|^\s*?\*(?:\s?$| ){0,1}%sm', '', $comments_string);
    return Spyc::YAMLLoadString($attributes);
  }

  static protected function get_object_schema($object){
    $schema = array();


    $class_name = get_class($object);

    $schema['type'] = $class_name;

    $function = new ReflectionClass($class_name);

    // get type name from class name
    if (method_exists('ReflectionClass', 'getShortName'))
    $schema['type'] = $function->getShortName();


    $schema['attributes'] = self::get_attributes($function->getDocComment());



    if (self::$_convert_to_lowercase){
      $schema['type'] = strtolower($schema['type']);
    }

    $properties = $function->getDefaultProperties();

    $schema['properties'] = array();
    foreach ($properties as $property=>$value){
      $prop = array();
      $prop['value'] = $value;

      $method=new ReflectionProperty($class_name,$property);

      // obtenemos los comentarios de la propiedad
      $prop['attributes'] = self::get_attributes($method->getDocComment());


      $schema['properties'][$property] = $prop;
    }


    return $schema;

  }
/**
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
  static protected function get_sql_table_schema($object){
    $obj_schema = self::get_object_schema($object);

    $schema = array();

    $schema['table_name'] = $obj_schema['type'];

    if (isset($schema['attributes']['table']))
    $schema['table_name'] = $schema['attributes']['table'];

    $schema['fields'] = array();

    $schema['primary_key'] = array();


    foreach ($obj_schema['properties'] as $property_name => $property_attributes){


      $field = array();

      //$field['name'] = $property_name;

      $field['value'] = $property_attributes['value'];

      $length = (int) (isset($property_attributes['attributes']['length']))?$property_attributes['attributes']['length']:'';

      if (isset($property_attributes['attributes']['primary_key'])
        and $property_attributes['attributes']['primary_key']==true){
        $schema['primary_key'][] = $property_name;
        $field['primary_key'] = true;
      }
      $type = 'VARCHAR(255)';
      if (isset($property_attributes['attributes']['type'])){
        switch ($property_attributes['attributes']['type']){
          case 'string':
            $type = 'VARCHAR';
            if (!empty($length))
            $type .= "($length)";
            else
            $type .= "(255)";
            break;
          case 'integer':
            $type = 'INTEGER';
            break;
          case 'date':
            $type = 'DATE';
            break;
          case 'datetime':
            $type = 'DATETIME';
            break;
          case 'float':
            $type = 'FLOAT';
            break;
          default:
            $type = 'VARCHAR(255)';
            break;
        }
      }

      $field['data_type'] = $type;

      $schema['fields'][$property_name] = $field;

    }



    return $schema;
  }

  static public function insert($object){
    $sql_schema = self::get_sql_table_schema($object);

    if (!db::table_exists($sql_schema['table_name'])){
      self::create_table($object);
    }

    $fields_names = array();
    $fields_values = array();

    foreach ($sql_schema['fields'] as $field=>$attributes){

      $fields_names[]=$field;

      if (!is_scalar($attributes['value']))
      throw new Exception('Field Values must be scalars!');

      $fields_values[':'.$field] = $attributes['value'];

    }

    $fields = implode(',', $fields_names);

    $params = implode(',',array_keys($fields_values));


    $sql = "INSERT INTO {$sql_schema['table_name']} ($fields) VALUES ($params)";

    db::execute($sql, $fields_values);

  }

  static public function update($object){
    $sql_schema = self::get_sql_table_schema($object);

    if (!db::table_exists($sql_schema['table_name'])){
      self::create_table($object);
      self::insert($object);
      return;
    }

    $field_updates = '';
    $fields_values = array();
    foreach ($sql_schema['fields'] as $field=>$attributes){
      if (!empty($field_updates))
      $field_updates .= ", ";

      $field_updates .= "$field = :$field";

      $fields_values[':'.$field] = $attributes['value'];
    }

    $where_id = '';

    foreach ($sql_schema['primary_key'] as $key){
      if (!empty($where_id))
      $where_id .= " AND ";

      $where_id .= "$key = :$key";
    }


    $sql = "UPDATE {$sql_schema['table_name']}
    SET $field_updates
    WHERE $where_id";
    
    
    db::execute($sql, $fields_values);

  }

  static protected function create_table($object){
    $sql_schema = self::get_sql_table_schema($object);

    # field declarations
    $fields_declaration = "";
    foreach ($sql_schema['fields'] as $field=>$attributes){

      $declaration = "$field {$attributes['data_type']}";

      if (isset($attributes['primary_key']) and $attributes['primary_key']==true
        and count($sql_schema['primary_key'])==1){

        $declaration .= ' PRIMARY KEY';
      }

      if (!empty($fields_declaration))
      $fields_declaration .= ", ";

      $fields_declaration .= "\n".$declaration;

    }

    # composite primary key
    if (isset($sql_schema['primary_key']) and count($sql_schema['primary_key'])>1){
      $fields_declaration .= ", PRIMARY KEY (".implode(',', $sql_schema['primary_key']).")";
    }

    $sql = "CREATE TABLE {$sql_schema['table_name']}
    ({$fields_declaration}
    )";

    db::execute($sql);

  }



}
?>
