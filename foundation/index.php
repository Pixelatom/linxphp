<?php
require "../linxphp/linx.php";

use linxphp\common\Configuration;
use linxphp\common\ErrorHandler;
use linxphp\common\ClassLoader;

# load application configuration
if (file_exists('config.ini')){
    Configuration::load('config.ini');
}

# sets error handler
if (Configuration::get('error_handler','convert_to_exceptions',true)){
    ErrorHandler::register();
}

# configure app classes autoloading
$app_classes = Configuration::get('paths','classes','application/classes');
$class_loader = new ClassLoader(null,$app_classes);
$class_loader->register();

