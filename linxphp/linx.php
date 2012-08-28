<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias
 * Licensed under MIT License
 */
namespace linxphp\common;

// includes SplClassLoader implementation
require_once 'common/classes/classloader.php';

// register common classes
$linxphp_path = dirname(__FILE__);
$classLoader = new ClassLoader('linxphp\common', $linxphp_path. '/common/classes');
$classLoader->register();

if (Configuration::get('errors','useexeptions',true)){
    ErrorHandler::register();
}


