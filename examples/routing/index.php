<?php
// includes ClassLoader implementation from the framework
require_once '../../src/linxphp/common/ClassLoader.php';

// Autoloading configuration. Path needs to point to linxphp src folder
$classLoader = new linxphp\common\ClassLoader('linxphp', "../../src");
$classLoader->register();

// enabled to stop at any error
linxphp\common\ErrorHandler::register();

use linxphp\http\rest\Router;

// optional and required section arguments detection
Router::register('GET', '/users/?/*', function($action,$id=''){
    echo $action;
});

// optional path argument detection
Router::register('GET', '/posts/*+', function($path=''){
    echo $path;
});

// required path argument detection
Router::register('GET', '/pages/?+', function($path){
    echo $path;
});


Router::route();