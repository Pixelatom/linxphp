<?php
// includes ClassLoader implementation from the framework
require_once '../../src/linxphp/common/ClassLoader.php';

// Autoloading configuration. Path needs to point to linxphp src folder
$classLoader = new linxphp\common\ClassLoader('linxphp', "../../src");
$classLoader->register();

// enabled to stop at any error
linxphp\common\ErrorHandler::register();

use linxphp\http\rest\Router;
use linxphp\http\Request;
use linxphp\http\Response;

// required path argument detection
Router::register('GET', '/', function($path){
    $request = new Request();
    if (empty($request->auth_user))
        return Response::create($path, 401,array("WWW-Authenticate", "Basic realm=\"myrealm\""));
    else
        echo $request->auth_user . ':' . $request->auth_password;
});

Router::route();