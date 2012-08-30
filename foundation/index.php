<?php

require "../linxphp/linx.php";

if (file_exists('config.ini')){
    linxphp\common\Configuration::load('config.ini');
}

if (Configuration::get('error_handler','convert_to_exceptions',true)){
    ErrorHandler::register();
}