<?php

class LoginController extends AppLoginController {
    public function __construct() {
        AppController::__construct();
        unset($this->view->breadcrumb);
        $this->view->content  = new Template('login');
    }

}