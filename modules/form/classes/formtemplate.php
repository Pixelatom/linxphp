<?php

/**
 * Description of formtemplate
 *
 * @author JaViS
 */
class FormTemplate extends Template {
    // array definition for each field
    protected $fields = array();
    // error messages for each field after validation
    protected $errors = array();
    // array of FormWidgets objects for each field
    protected $_widgets = array();
    // name of the form
    public $name;
    // action url of the form
    public $action;

    /**
     * form constructor
     * @param array $fields array definition for all the fields
     * @param string $name
     * @param string $action
     */
    public function __construct($fields,$name = 'form', $action = null) {
        parent::__construct();

        $this->fields = $fields;
        $this->name = $name;
        $this->action = $action;

        $this->set_default_template('form/form');
    }

    /**
     * return true if the form is submitted.
     * @return boolean
     */
    public function is_submitted(){
        return isset($_POST[$this->name]);
    }

    /**
     * returns a FormWidget class for a specific field
     * @param <type> $name
     * @return <type>
     */
    public function widget($name) {
        if (!isset($this->fields[$name])) return false;

        if (isset($this->_widgets[$name])) return $this->_widgets[$name];

        extract($this->fields[$name], EXTR_SKIP);

        if (!isset($type)) $type = 'text';

        $widgetclass = ucfirst($type) . 'Widget';
        if (!class_exists($widgetclass)){
            throw new Exception("Class $widgetclass not found");
        }

        $class = new ReflectionClass($widgetclass);
        if ($class->isAbstract())
            throw new Exception("Class $widgetclass can't be instantiated");


        $this->_widgets[$name] = new $widgetclass($name, $this->fields[$name]);
        return $this->_widgets[$name];
    }
    
    /**
     * Renders the form HTML
     */
    public function show() {
        $action = $this->action;        
        // if not action was defined we'll define the currect url as action
        if (empty ($action))
            $action = new Url();

        // set some form variables
        $this->set('id', $this->name);
        $this->set('name', $this->name);
        $this->set('action', $action);

        // creates form widgets classes and validates the form
        $fields = $this->build_form();

        $this->set('fields', $fields);

        parent::show();
    }

    /**
     * creates all the FormWidget classes for each form field
     * build the errors array from previous validations
     * @return <type>
     */
    protected function build_form(){        
        
        $fields = array();
        $this->errors = array();
        
        // build the list of widget in the same order as the form definition
        foreach ($this->fields as $name => $properties) {
            $field = $this->widget($name);
            if (!empty($field->error))
            $this->errors[] = $field->error;

            $fields[$name] = $field;
        }
        
        return $fields;
    }

    /**
     * returns true if the valdation went ok.
     * if not set the errors array and updates the form widgets
     * clear and rebuild the errors array and widgets errors 
     * @return <type>
     */
    public function is_valid() {
        if (!$this->is_submitted()) return true;
        
        $fields = $this->build_form();

        $this->errors = array();

        foreach ($fields as $field) {            
            if (!$field->validate()){
                $this->errors[] = $field->error;
            }            
        }
        //var_dump($this->errors);
        return count($this->errors)==0;
    }

    /* functions to add and remove fields */

    /**
     * set all the fields at once
     * @param array $fields array definition for all the fields
     */
    public function set_fields($fields) {
        $this->fields = $fields;
    }

    /**
     * add one field definition
     * @param string $name
     * @param array $properties array definition of the field
     * @param string $sibling name of the sibling field
     * @param string $position 'before' or 'after'
     */
    public function add_field($name, $properties, $sibling, $position = 'before') {
        if (!is_array($properties)) {
            $properties = array('text' => $properties, 'type' => 'text');
        }
        $properties['name'] = $name;
        $keys = array_keys($this->fields);
        $values = array_values($this->fields);

        $siblingPos = array_search($sibling, $keys);

        if ($position == 'after')
            $siblingPos++;

        array_splice($keys, $siblingPos, 0, $name);
        array_splice($values, $siblingPos, 0, array($properties));
        $this->fields = array_combine($keys, $values);
    }
    public function add_field_before($name, $properties, $sibling) {
        $this->add_field($name, $properties, $sibling);
    }
    public function add_field_after($name, $properties, $sibling) {
        $this->add_field($name, $properties, $sibling, 'after');
    }
    public function override_field($name, $properties) {
        $this->fields[$name] = array_merge($this->fields[$name], $properties);

        // force reset existing widget
        if (isset($this->_widgets[$name]))
        unset($this->_widgets[$name]);
    }
    public function remove_field($name) {
        unset($this->fields[$name]);

        // remove widget too
        if (isset($this->_widgets[$name]))
        unset($this->_widgets[$name]);
    }
    public function &get_field($name){
        if (isset($this->fields[$name])){            
            return $this->fields[$name];
        }
        else{
            throw new Exception ("Field `$name` not found in Form's field list");
        }
            
    }

}