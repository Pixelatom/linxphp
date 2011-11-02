<?php

/**
 * Description of textwidget
 *
 * @author JaViS
 */
class SelectWidget extends FormWidget {

    public $options;
    public $selected;
    

    public function __construct($name, $properties) {
        

        parent::__construct($name, $properties);
        
        

        //$this->value = $this->get_value();

        $this->set_default_template('form/widgets/select');

	    
        
        $this->options = (isset($this->properties['options']))?$this->properties['options']:array();
        
    }

    public function show($name = null) {
        
        
        $options = $this->options;
        // pasar al template
        $selected = array();
        
        foreach ($options as $value => $title) {
            if (!is_array($this->get_value())){

                $selected[$value] = ( $this->get_value() == $value ) ? 'selected="selected"' : '';
            }
            elseif(is_array($this->get_value())){
                /*
                echo '<pre>';
                var_dump($this->get_value());
                die();
                */
                $selected[$value] = ( in_array($value,$this->get_value()) ) ? 'selected="selected"' : '';    
            }
            
        }
        
        $this->selected = $selected;

        $this->set('selected', $this->selected);
        $this->set('options', $this->options);
        if (isset($this->properties['attributes']['multiple']))
            $this->name .= "[]";
        parent::show($name);
        $this->name = str_replace("[]", "", $this->name);
    }

}
?>
