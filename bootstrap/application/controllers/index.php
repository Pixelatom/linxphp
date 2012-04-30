<?php
class IndexController extends AppController {
    protected $login_required = true;
    function index() {

        $this->view->content = '';
        $this->show();

    }
}