<?php
require "../linxphp/linx.php";

use linxphp\common\Configuration;
use linxphp\common\ErrorHandler;
use linxphp\common\ClassLoader;


# load application configuration
$config = new Configuration();

if (file_exists('config.ini')){
    $config->load('config.ini');
}

# sets error handler
if ($config->get('error_handler','convert_to_exceptions',true)){
    ErrorHandler::register();
}

# configure app classes autoloading
$app_classes = $config->get('paths','classes','application/classes');
$class_loader = new ClassLoader(null,$app_classes);
$class_loader->register();

# Application setup ready
Event::run('application.ready'); 