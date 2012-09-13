<?php
// includes SplClassLoader implementation
require_once '../src/linxphp/common/ClassLoader.php';

// register linxphp classes path for autoloading
$classLoader = new linxphp\common\ClassLoader('linxphp', "../src");
$classLoader->register();

// Load the application at the current directory
linxphp\implementation\Application::load(dirname(__FILE__));

$request = new linxphp\rest\Request();
echo '<pre>';
var_dump($request);
