<?php
/**
 * Description of textwidget
 *
 * @author JaViS
 */
class FileWidget extends FormWidget{
    public function validate(){
        return empty($this->error);
    }

    public function is_submitted(){
        if ($this->ignore_submit)
            return false;

        if (is_null($this->parentform))
            return  (isset($_FILES[$this->name]) and !empty($_FILES[$this->name]["name"]));
        else
            return  (isset($_FILES[$this->parentform->name][$this->name]) and !empty($_FILES[$this->parentform->name][$this->name]["name"]));
    }

    protected function get_value(){
        if ($this->is_submitted()){
            
            $foo = new Upload($_FILES[$this->name]);
            /*$handle->allowed = array('image/*');*/
            if ($foo->uploaded) {
                // save uploaded image with no changes
                $foo->Process(Application::get_site_path() . $this->config['path'] );
                if ($foo->processed) {
                    // if it was not empty, we'll remove previous file
                    if (!empty($this->default)){
                        @ unlink(Application::get_site_path() . $this->config['path'].$this->default);
                    }
                    return $foo->file_dst_name;
                }
                else {
                    //AdminController::set_message('error uploading file: ' . $foo->error, AdminController::MSG_TYPE_ERROR);
                    $this->error = 'Error uploading file: ' . $foo->error;
                    return '';
                }
            }
        }
    }
    
    public $config = array(
        'path' => 'upload/',
        'multi' => false,
    );
    
    public function  __construct($name, $properties) {
        $this->set_default_template('form/widgets/file');

        $this->properties = $properties;
        $this->name = $name;
        $this->id = (!isset($properties['id']))? $name : $properties['id'];
        $this->type = (!isset($properties['type']))? 'text' : $properties['type'];
        $this->label = (!isset($properties['label']))?  $name : $properties['label'];
        $this->default = (!isset($properties['default']))?  '' : $properties['default'];
        $this->rules = (!isset($properties['rules']))?  array() : $properties['rules'];

        if (!isset($properties['attributes']['class']))
            $properties['attributes']['class'] = '';

        if (!isset($properties['attributes']['id']))
            $properties['attributes']['id'] = $name;

        $this->attributes = $properties['attributes'];
        
        if (isset($this->properties['upload']))
        $this->config = array_merge($this->config,$this->properties['upload']);
        
        $this->value = $this->get_value();
    }
}