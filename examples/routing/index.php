<?php
// includes ClassLoader implementation from the framework
require_once '../../src/linxphp/common/ClassLoader.php';

// Autoloading configuration. Path needs to point to linxphp src folder
$classLoader = new linxphp\common\ClassLoader('linxphp', "../../src");
$classLoader->register();

// enabled to stop at any error
linxphp\common\ErrorHandler::register();

use linxphp\rest\Router;

Router::register('GET', '/lol/lala', function(){
    echo ':D';
});
    
Router::route();