<?php
require "../linxphp/linx.php";

# load application configuration
if (file_exists('config.ini')){
    linxphp\common\Configuration::load('config.ini');
}

# sets error handler
if (linxphp\common\Configuration::get('error_handler','convert_to_exceptions',true)){
    linxphp\common\ErrorHandler::register();
}

# configure app classes autoloading
$class_loader = linxphp\common\ClassLoader('\\','app/classes');
$class_loader->register();

# configure vendor classes autoloading