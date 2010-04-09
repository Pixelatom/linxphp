<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Mapper{

  /* options */
  static protected $_convert_to_lowercase = true;

  static protected function get_object_schema($object){
    $theme = array();
    
    
    $class_name = get_class($object);

    $theme['type'] = $class_name;

    $function = new ReflectionClass($class_name);

    // get table name from class name
    $theme['type'] = $function->getShortName();


    if (self::$_convert_to_lowercase){
      $theme['type'] = strtolower($theme['type']);
    }

    $properties = $function->getDefaultProperties();

    $theme['properties'] = array();
    foreach ($properties as $property=>$value){
      $prop = array();
      $prop['value'] = $value;

      $method=new ReflectionProperty($class_name,$property);
      // obtenemos los comentarios de la propiedad title
      $attributes = $method->getDocComment();
      // eliminamos los caracteres de comentarios
      $attributes = preg_replace('%\A/\*\*$|^\s*?\*/\s*|^\s*?\*(?:\s?$| ){0,1}%sm', '', $attributes);


      $prop['meta'] = Spyc::YAMLLoadString($attributes);


      $theme['properties'][$property] = $prop;
    }

    var_dump($theme);
die();

  }
  static public function insert($object){
    $schema = self::get_object_schema($object);
  }

}
?>
