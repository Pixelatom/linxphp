<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias
 * Licensed under MIT License
 */
namespace linxphp\Core;

require_once 'core/classes/classloader.php';

if (Configuration::get('errors','useexeptions',true))
include_once 'includes/errorexeption.php';

$linxphp_path = dirname(__FILE__);

$classLoader = new ClassLoader('linxphp\Core', $linxphp_path. '/core/classes');
$classLoader->register();