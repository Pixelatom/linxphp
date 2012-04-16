<?php

abstract class AppLoginController extends AppController {

    public function __construct() {
        $this->view = new Template('login');
    }

    public function index() {
        if (isset($_POST) and !empty($_POST)) {
            $user = false;
            $user = Mapper::get('User', '(email = "' . addslashes($_POST['email']) . '" OR username = "' . addslashes($_POST['email']) . '") and password = MD5("' . $_POST['password'] . '")');
            if (isset($user[0])) {

                Authorization::user_log_in($user[0]->id); // logueamos al usuario
                $currentUrl = new Url();

                Application::$request_url = $currentUrl;

                $redirect = null;

                if (Url::factory()->get_param('route') == 'login') {
                    $redirect = 'index';
                }

                Application::route($redirect, true);
                die();
            } else {
                $this->show_message('Invalid login. Try again.');
            }
        }
        $this->show();
    }

}