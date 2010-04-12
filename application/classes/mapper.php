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
    /*
    echo '<PRE>';
    var_dump($schema);
    die();
     *
     */

    return $schema;

  }

  static protected function get_sql_table_schema($object){
    $obj_schema = self::get_object_schema($object);

    $schema = array();

    $schema['table_name'] = $obj_schema['type'];

    if (isset($schema['attributes']['table']))
    $schema['table_name'] = $schema['attributes']['table'];

    $schema['fields'] = array();

    foreach ($obj_schema['properties'] as $property_name => $property_attributes){
      $fields=array();

      $field['name'] = $property_name;

      $field['value'] = $property_attributes['value'];

      $field['data_type'] = 'VARCHAR(255)';
      $type = 'string';
      
      if (isset($schema['properties'][$property_name]['attributes']['table'])){

      }

      $schema['fields'] = $field;

    }



    return $schema;
  }

  static public function insert($object){
    $sql_schema = self::get_sql_table_schema($object);




  }

}
?>
