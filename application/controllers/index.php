<?php


class IndexController extends Controller {
    function index() {
        require (Application::get_site_path().'application/models/item.php');
        require (Application::get_site_path().'application/models/header.php');


        // testing loading
        echo '<pre>';
        var_dump(Mapper::get_by_id('Item', 2));
        die();


        // testing removing item 1
        $object = new Item();

        $object->id = 1;

        $object->title = 'is a test LOL';

        $object->uri = 'lol';


        Mapper::save($object);
        Mapper::delete($object);



        $object = new Item();

        $object->id = 2;

        $object->title = 'another object';

        Mapper::save($object);      

        
        // testing cache in item # 2
        /*
        $object = Mapper::get_by_id('Item', 2);
        $object->description = ':(';
        echo '<pre>';
        var_dump(Mapper::get_by_id('Item', 2));
        */

        // testing relationships
        $header = new Header();
        $header->id = 1;
        $header->title = 'test';
        Mapper::save($header);
/*
        echo '<pre>';
        var_dump(Mapper::get_by_id('Header', 1));
*/

        $object->header = $header;
        Mapper::save($object);

    }
    function language() {

        $user_lang=Language::UserLanguage();


        if(isset($_GET['ln'])) {
            $_SESSION['language'] = $_GET['ln'];
        }
        elseif (!isset($_SESSION['language']) and in_array($user_lang, array('es'))) {
            $_SESSION['language'] = $user_lang;
        }

        Language::Set($_SESSION['language']);
        Language::SetAuto(true);
        # showing up a template
        Template::factory('index')->show();
    }
}
?>