<?php


class IndexController extends Controller {
    function index() {

       
        if(isset($_GET['ln'])) $_SESSION['language'] = $_GET['ln'];
        Language::Set($_SESSION['language']);
        Language::SetAuto(true);
        # showing up a template
        Template::factory('index')->show();
    }
}
?>