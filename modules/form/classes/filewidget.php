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
    protected function get_value(){

        if (isset($_FILES[$this->name])) {

            $foo = new Upload($_FILES[$this->name]);
            /*$handle->allowed = array('image/*');*/
            if ($foo->uploaded) {
                $uploadpath = 'upload/';
                if (isset($property_attributes['attributes']['form']['attributes']['uploadpath']))
                $uploadpath = $property_attributes['attributes']['form']['attributes']['uploadpath'] . '/';

                // save uploaded image with no changes
                $foo->Process(realpath(Application::get_site_path() . $uploadpath) . '/');
                if ($foo->processed) {
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
    public function  __construct($name, $properties) {
        parent::__construct($name, $properties);
        $this->set_default_template('form/widgets/file');
    }
}
?>
