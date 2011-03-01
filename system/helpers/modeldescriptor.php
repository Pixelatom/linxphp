<?php

class ModelDescriptor {
    /* options */

    static protected $cache = array();

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
            throw new Exception("Class $model doesn't exists");
        }


        // will return the description stored in cache
        if (isset(self::$cache[$class_name])){
            
            $schema = self::$cache[$class_name];
            
            if (is_object($model)){
                // set the value for the cached properties
                foreach ($schema['properties'] as $property => &$prop) {
                    // we'll ask this condition to avoid force loading of lazy loading properties
                    if ((!isset($prop['attributes']['relationship']['lazy_load'])
                            or $prop['attributes']['relationship']['lazy_load'] == false)
                            or isset($model->$property))
                        $prop['value'] = $model->$property;
                }
                //for model instances we add a new property describing the unique identifier
                $unique = array();
                foreach ($schema['properties'] as $property_name => $property_attributes) {
                    if (isset($property_attributes['attributes']['primary_key'])
                            and $property_attributes['attributes']['primary_key'] == true) {
                        if (!is_null($schema['properties'][$property_name]['value']))
                         $unique[$property_name] = $schema['properties'][$property_name]['value'];
                    }
                }
                if (!empty($unique))
                $schema['unique'] = $unique;
            }
            
            return $schema;
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
            $reflection = new ReflectionProperty($class_name, $property);
            if ($reflection->isPublic() ){
                $prop = array();
                $prop['default_value'] = $value;
                $prop['value'] = $value;
                //$prop['value'] = $object->$property;

                $method = new ReflectionProperty($class_name, $property);

                // obtenemos los comentarios de la propiedad

                $prop['attributes'] = self::get_attributes($method->getDocComment());

                $prop['attributes']['is_relationship'] = false;

                if (isset($prop['attributes']['type']) and class_exists($prop['attributes']['type'])){
                    $prop['attributes']['is_relationship'] = true;
                }

		if(!isset($prop['attributes']['model']) or $prop['attributes']['model']!='ignore')
		    $schema['properties'][$property] = $prop;
            }
            
        }

        $schema['primary_key'] = array();
        foreach ($schema['properties'] as $property_name => $property_attributes) {
            if (isset($property_attributes['attributes']['primary_key'])
                    and $property_attributes['attributes']['primary_key'] == true) {
                $schema['primary_key'][] = $property_name;
            }
        }

        


        

        if (!isset(self::$cache[$class_name]))
            self::$cache[$class_name] = $schema;

        return $schema;
    }
}
