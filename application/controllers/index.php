<?php


class IndexController extends Controller {
    function index(){
      require (Application::get_site_path().'application/models/item.php');


      $object = new Item();

      $object->id = 1;

      $object->title = 'this is a test';

      $object->uri = 'lol';


      Mapper::insert($object);


      




    }
    function language() {

        $user_lang=Language::UserLanguage();


        if(isset($_GET['ln'])) {
            $_SESSION['language'] = $_GET['ln'];
        }
        elseif (!isset($_SESSION['language']) and in_array($user_lang, array('es'))){
            $_SESSION['language'] = $user_lang;
        }
        
        Language::Set($_SESSION['language']);
        Language::SetAuto(true);
        # showing up a template
        Template::factory('index')->show();
    }
}
?>