<?php


class IndexController extends Controller {
    function index(){
      require (Application::get_site_path().'application/models/item.php');


      $object = new Item();

      Mapper::insert($object);


      $method=new ReflectionProperty('Item','title');
      // obtenemos los comentarios de la propiedad title
      $attributes = $method->getDocComment();
      // eliminamos los caracteres de comentarios
      $attributes = preg_replace('%\A/\*\*$|^\s*?\*/\s*|^\s*?\*(?:\s?$| ){0,1}%sm', '', $attributes);
      var_dump(Spyc::YAMLLoadString($attributes));




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