<?php
// includes ClassLoader implementation from the framework
require_once '../../src/linxphp/common/ClassLoader.php';

// Autoloading configuration. Path needs to point to linxphp src folder
$classLoader = new linxphp\common\ClassLoader('linxphp', "../../src");
$classLoader->register();

// Load the application. Path argument needs to point to the 'application' folder  
linxphp\implementation\Application::load(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'application');
