<?php
require 'vendor/autoload.php';

// Load the application. Path argument needs to point to the 'application' folder  
linxphp\implementation\Application::load(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'application');

// route the application
linxphp\http\rest\Router::route();