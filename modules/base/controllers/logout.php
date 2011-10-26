<?php

class LogoutController extends AppController {

    public function index() {
        Authorization::user_log_out();
        Application::route('login', true);
    }

}