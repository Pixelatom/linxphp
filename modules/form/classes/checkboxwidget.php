<?php
/**
 * Description of textwidget
 *
 * @author JaViS
 */
class CheckboxWidget extends FormWidget{
    // property to know if the checkbox is checked or not
    public function checked(){
        if ($this->is_submitted())
            $checked = $this->get_value() == $this->properties['value'];
        else
            $checked = (!isset($this->properties['checked']))? false : $this->properties['checked'];
        return $checked;
    }
    public function  __construct($name, $properties) {

        parent::__construct($name, $properties);

        // we need a value to know if it was checked, if not present, we'll force it
        if (!isset($this->properties['value']))
                $this->properties['value'] = true;

        $this->value = ($this->checked())? $this->properties['value']:null;

        $this->set_default_template('form/widgets/checkbox');
    }

    public function show($name = null) {
        
        if ($this->checked())
            $this->attributes['checked'] = "checked";
        else
            unset($this->attributes['checked']);

        parent::show($name);
    }
}
?>
