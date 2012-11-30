<?php
namespace app\controllers\foo;

class Bar extends \app\controllers\Controller{
    public function index(){
        return new \linxphp\http\Response("Foo Bar");
    }
}
