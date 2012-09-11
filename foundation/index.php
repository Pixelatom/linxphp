<?php
// includes SplClassLoader implementation
require_once '../src/linxphp/common/ClassLoader.php';

// register linxphp classes path for autoloading
$classLoader = new linxphp\common\ClassLoader('linxphp', "../src");
$classLoader->register();

use linxphp\common\Env;
use linxphp\implementation\Application;

// Load the application
Application::load(Env::path());