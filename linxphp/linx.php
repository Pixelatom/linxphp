<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias
 * Licensed under MIT License
 */
namespace linxphp\common;

// includes SplClassLoader implementation
require_once 'common/classloader.php';

// register common classes
$linxphp_path = dirname(__FILE__);
die($linxphp_path);
$classLoader = new ClassLoader('linxphp', $linxphp_path);
$classLoader->register();

Event::run('system.ready'); // the framework is loaded