<?php
abstract class FormWidget extends Template {
    // field name
    public $name;
    // default value
    public $default;
    // current value
    public $value;
    // HTML element attributes
    public $attributes = array();
    // input label
    public $label;
    // validation error
    public $error;
    // validation rules
    public $rules;
    // input type
    public $type;

    protected $properties;

    // reference to the parent form
    /*@todo: when set, POST must be an multidimensional array where parent index is form name*/
    public $parentform=null;

    public $ignore_submit = false; // when true, it won't be considered submitted

    /**
     * returns true if the field comes on the POST
     * @return <type>
     */
    public function is_submitted(){
        if ($this->ignore_submit)
            return false;

        if (is_null($this->parentform))
            return  isset($_POST[$this->name]);
        else
            return  isset($_POST[$this->parentform->name][$this->name]);
    }

    public function  __construct($name,$properties) {
        $this->set_default_template('form/widgets/textfield');

        $this->properties = $properties;
        
        
        $this->name = $name;

        $this->id = (!isset($properties['id']))? $name : $properties['id'];

        $this->type = (!isset($properties['type']))? 'text' : $properties['type'];

        $this->label = (!isset($properties['label']))?  $properties['name'] : $properties['label'];

        $this->default = (!isset($properties['default']))?  '' : $properties['default'];

        $this->rules = (!isset($properties['rules']))?  '' : $properties['rules'];

        if (!isset($properties['attributes']['class']))
            $properties['attributes']['class'] = '';

        if (!isset($properties['attributes']['id']))
            $properties['attributes']['id'] = $name;

        $this->attributes = $properties['attributes'];

        $this->value = $this->get_value();
    }

    /**
     * get field value
     * @return <type>
     */
    protected function get_value(){
        if (is_null($this->parentform))
            return ( isset($_POST[$this->name]) ? $_POST[$this->name] : $this->default );
        else
            return ( isset($_POST[$this->parentform->name][$this->name]) ? $_POST[$this->parentform->name][$this->name] : $this->default );
    }

    public function validate(){
        if (!isset($this->rules) || empty($this->rules) || $this->is_submitted())
            return true;

        if (is_string($this->rules))
            $rules = explode(',', str_replace(' ', '', $this->rules));

        foreach ($rules as $rule) {
            if (preg_match("/\[([^\]]+)\]/", $rule, $matches)) {
                $rule = str_replace($matches[0], '', $rule);
                $args = array_merge($args, explode(',', str_replace(' ', '', $matches[1])));
            }
            switch ($rule) {
                case "required":
                    if (empty($this->value))
                    $this->error = "The {$this->name} field is required.";
                    break;
                case "email":
                    if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $this->value))
                    $this->error = "The {$this->name} field must contain a valid email address.";
                    break;
                case "min_length" :
                    if (!strlen($this->value) <  $args[0])
                    $this->error = "The {$this->name} field must contain at least {$this->name} characters.";
                    break;
                case "max_length" :
                    if (strlen($this->value) > $args[0])
                    $this->error = "The {$this->name} field can not exceed {$this->name} characters in length.";
                    break;
                case "exact_length" :
                    if (!strlen($this->value == $args[0]))
                    $this->error = "The {$this->name} field must be exactly {$this->name} characters in length.";
                    break;
                case "alpha" :
                    if (!preg_match("/^([a-z])+$/i", $this->value)){
                        $this->error = "The {$this->name} field may only contain alphabetical characters.";
                    }
                    break;
                case "alpha_numeric" :
                    if (!preg_match("/^([a-z0-9])+$/i", $this->value))
                    $this->error = "The {$this->name} field may only contain alpha-numeric characters.";
                    break;
                case "alpha_dash" :
                    if (!preg_match("/^([-a-z0-9_-])+$/i", $this->value))
                    $this->error = "The {$this->name} field may only contain alpha-numeric characters, underscores, and dashes.";
                    break;
                case "numeric" :
                    if (!is_numeric($this->value))
                    $this->error = "The {$this->name} field must contain a number.";
                    break;
                case "integer" :
                    if (!preg_match('/^[\-+]?[0-9]+$/', $this->value))
                    $this->error = "The {$this->name} field must contain an integer.";
                    break;
                case "matches" :
                    if (isset($_POST[$args[0]])) {
                        $compareValue = $_POST[$args[0]];
                        if ($this->value !== $args[0])
                            $this->error = "The {$this->name} field does not match the {$args[0]} field.";
                    }
                    break;
            }

            if (!empty($this->error)) return false;
        }
        return true;
    }

    public function show($name = null) {

        $this->set('type', $this->type);
        $this->set('default', $this->default);
        $this->set('value', $this->value);
        $this->set('label', $this->label);
        $this->set('name', $this->name);
        $this->set('attributes', $this->attributes);

        if (!empty($this->error))
        $this->set('error', $this->error);

        // the rest of the properties set on the constructor will be passed as vars to the template
        foreach ($this->properties as $varname=>$varvalue){
            if (!$this->key_exists($varname)){
                $this->set($varname, $varvalue);
            }
        }

        parent::show($name);
    }


    function remove_rule($name, $rules) {
        $currentRules = explode(',', str_replace(' ', '', $this->rules));

        if (is_string($rules))
            $rules = explode(',', str_replace(' ', '', $rules));

        $currentRules = array_diff($currentRules, $rules);

        $this->rules = implode(', ', $currentRules);
    }

}