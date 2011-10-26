<?php
/**
 * Description of textwidget
 *
 * @author JaViS
 */
class TextareaWidget extends FormWidget{
    public function  __construct($name, $properties) {
        parent::__construct($name, $properties);
        $this->set_default_template('form/widgets/textarea');
    }
}
?>
