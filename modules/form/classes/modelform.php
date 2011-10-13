<?php

class ModelForm extends FormTemplate {

    protected $model;

    public function __construct($model) {
        

        $form = $this->build_fields($model);

        // add submit button
        $form['submit'] = array(
            'type' => 'submit',
            'label' => 'Guardar',
        );
   
        $schema = ModelDescriptor::describe($model);
        parent::__construct($form, $schema['type'] . 'form', null);
    }

    protected function build_fields($model) {

        $form = array();

        $schema = ModelDescriptor::describe($model);


        foreach ($schema['properties'] as $property_name => $property_attributes) {

            // property form == false ignores field
            if (!isset($property_attributes['attributes']['form']) or
                    $property_attributes['attributes']['form'] == false)
                continue;

            // default field properties
            $field = array(
                'type' => 'text',
                'label' => $property_name,
                'id' => $property_name,
                'default' => $property_attributes['value'],
                );

            if (isset($property_attributes['attributes']['form'])) {

                $field = array_merge($field, $property_attributes['attributes']['form']);


                // check if property is a relation to another model
                if (($property_attributes['attributes']['is_relationship'])) {
                    // reset to null
                    $field['default'] = null;
                    // fill values depending on the type of input field
                    switch ($field['type']) {
                        case 'select':
                            $field['options'] = array();
                            if (isset($field['value_property'])
                                    and isset($field['display_property'])) {

                                $option_models = Mapper::get($property_attributes['attributes']['type']);

                                $field['options'] = array();

                                foreach ($option_models as $option) {
                                    $field['options'][$option->$field['value_property']] = $option->$field['display_property'];
                                }
                            }
                        default:
                            // fill value
                            // if the model is an instance
                            if (is_object($model)) {
                                if (isset($field['value_property'])
                                    and is_object($property_attributes['value'])) // and the property as well
                                    $field['default'] = $property_attributes['value']->$field['value_property'];//
                            }
                    }
                    
                    /*@todo: throw event to give the posibility to extend the switch when the type of widget is not recognized*/
                    
                    // specific ModelForm properties, not to be rendered.
                    unset($field['value_property']);
                    unset($field['display_property']);
                }
                else {
                    switch ($field['type']) {
                        case 'checkbox':
                            /* it's checked if the property value is equal to the value of the checkbox*/
                            $field['checked'] = $property_attributes['value'] == $field['value'];                            
                            break;
                    }
                }
            }

            $form[$property_name] = $field;
        }
        return $form;
    }

    public function submit($model) {
        // first check id the submit is validated
        if (!$this->is_submitted() or !$this->is_valid())
            return;
        
        $description = ModelDescriptor::describe($model);

        foreach ($this->fields as $name => $properties) {

            // if the model doesn't have this field we'll continue with the next one
            if (!isset($description['properties'][$name]))
                continue;

            $property_name = $name;
            $property_attributes = $description['properties'][$name];

            // we don't want to change ID properties
            if (isset($property_attributes['attributes']['primary_key'])
                    and $property_attributes['attributes']['primary_key'] == true)
                continue;

            $field = $this->widget($name); // obtain the widget instance

            // will assign the value only if it's submited, of course!
            if (!$field->is_submitted()) continue;

            if (!$property_attributes['attributes']['is_relationship']) {
                $model->$name = $field->value; // if it's not relationship we'll just assign the value
            } else {
                if ($property_attributes['attributes']['relationship']['type'] == 'parent') {
                    if (is_null($field->value)) {
                        $model->$property_name = null;
                    } else {
                        $model->$property_name = Mapper::get_by_id($property_attributes['attributes']['type'], $field->value);
                    }
                } elseif ($property_attributes['attributes']['relationship']['type'] == 'childs') {
                    $model->$property_name = array();
                    if (is_array($field->value)) {
                        foreach ($field->value as $id_value) {
                            $model->{$property_name}[] = Mapper::get_by_id($property_attributes['attributes']['type'], $id_value);
                        }
                    }
                }
            }            
        }
    }

}