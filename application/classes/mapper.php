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

    $theme['table'] = $class_name;

    $function = new ReflectionClass($class_name);

    // get table name from class name
    $theme['table'] = $function->getShortName();


    if (self::$_convert_to_lowercase){
      $theme['table'] = strtolower($theme['table']);
    }

    $properties = $function->getDefaultProperties();

    foreach ($properties as $property=>$value){

    }


  }
  static public function insert($object){
    $schema = self::get_object_schema($object);
  }

}
?>
