<?php

class ModelDescriptor {
    /* options */

    static protected $_convert_to_lowercase = true;

    static protected function get_attributes($comments_string) {
        $attributes = preg_replace('%/\*\*|^\s*?\*/\s*|^\s*?\*(?:\s?$| ){0,1}%sm', '', $comments_string);
        return Spyc::YAMLLoadString($attributes);
    }
    static public function describe($model){

        if (is_object($model)){
            $class_name = get_class($model);
        }
        elseif (class_exists($model)){
            if(!in_array('Model', class_parents($model)))
                    throw new Exception("Class $class_name doesn't extends Model");
            $class_name = $model;
        }
        else{
            throw new Exception("Class $class_name doesn't exists");
        }


        $schema = array();

        $schema['type'] = $class_name;

        $function = new ReflectionClass($class_name);

        // get type name from class name
        if (method_exists('ReflectionClass', 'getShortName'))
            $schema['type'] = $function->getShortName();


        $schema['attributes'] = self::get_attributes($function->getDocComment());


        if (self::$_convert_to_lowercase) {
            $schema['type'] = strtolower($schema['type']);
        }

        $properties = $function->getDefaultProperties();

        $schema['properties'] = array();
        foreach ($properties as $property => $value) {
            $prop = array();
            $prop['default_value'] = $value;
            $prop['value'] = $value;
            //$prop['value'] = $object->$property;

            $method = new ReflectionProperty($class_name, $property);

            // obtenemos los comentarios de la propiedad
            $prop['attributes'] = self::get_attributes($method->getDocComment());


            $schema['properties'][$property] = $prop;
        }

        if (is_object($model)){
            foreach ($schema['properties'] as $property => &$prop) {
                // we'll ask this condition to avoid force loading of lazy loading properties
                if ((!isset($prop['attributes']['relationship']['lazy_load'])
                        or $prop['attributes']['relationship']['lazy_load'] == false)
                        or isset($model->$property))
                    $prop['value'] = $model->$property;
            }
        }

        return $schema;
    }
}
