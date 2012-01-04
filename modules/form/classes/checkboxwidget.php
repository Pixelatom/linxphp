<?php

/**
 * Description of textwidget
 *
 * @author JaViS
 */
class CheckboxWidget extends FormWidget {

   

    // property to know if the checkbox is checked or not
    public function checked() {
        if ($this->is_submitted())
            $checked = $this->get_value() == $this->properties['value'];
        else
            $checked = (!isset($this->properties['checked'])) ? false : $this->properties['checked'];
        return $checked;
    }

    public function __construct($name, $properties) {

        parent::__construct($name, $properties);

        // we need a value to know if it was checked, if not present, we'll force it
        
        if (!isset($this->properties['value']))
            $this->properties['value'] = true;

        $this->value = ($this->checked()) ? $this->properties['value'] : false;

        $this->set_default_template('form/widgets/checkbox');
    }

    public function show($name = null) {
        if ($this->checked())
            $this->attributes['checked'] = "checked";
        else
            unset($this->attributes['checked']);

        $this->set('type', $this->type);
        $this->set('default', $this->default);
        $this->set('value', $this->properties['value']);
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

        Template::show($name);
    }

}
?>
