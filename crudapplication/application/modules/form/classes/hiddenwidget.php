<?php
/**
 * Description of textwidget
 *
 * @author JaViS
 */
class HiddenWidget extends FormWidget{
    public function  __construct($name, $properties) {
        parent::__construct($name, $properties);
        $this->set_default_template('form/widgets/hidden');
    }
}
?>
