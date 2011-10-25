<?php

/**
 * Description of textwidget
 *
 * @author JaViS
 */
class SelectWidget extends FormWidget {

    public $options;
    public $selected;
    public $null;

    public function __construct($name, $properties) {
        parent::__construct($name, $properties);

        $this->set_default_template('form/widgets/select');

	    extract($properties, EXTR_SKIP);

        // pasar al template
        $selected = array();
        foreach ($options as $value => $title) {
            if (isset($_POST[$name])):
                $selected[$value] = ( $this->get_value() == $value ) ? 'selected="selected"' : '';
            else:
                $selected[$value] = ($default == $value ) ? 'selected="selected"' : '';
            endif;
        }
    	if(isset($null) and $null == 'true')
    	    $this->null = true;
        
        $this->options = $options;
        $this->selected = $selected;
    }

    public function show($name = null) {
        $this->set('selected', $this->selected);
        $this->set('options', $this->options);
        $this->set('null', $this->null);
        parent::show($name);
    }

}
?>
