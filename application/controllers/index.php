<?php
namespace app\controllers;

class Index extends \app\controllers\Controller{
    public function index(){
        return new \linxphp\http\Response("Yeah!");
    }
}
