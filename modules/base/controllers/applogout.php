<?php

abstract class AppLogoutController extends AppController {

    public function index() {
        Authorization::user_log_out();
        Application::route('login', true);
    }

}