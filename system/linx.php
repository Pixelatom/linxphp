<?php
/*
 * Linx PHP Framework
 * Copyright (C) 2008  Javier Arias
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
 
/*
 * basic application configuration
 */
$app_config=array(
	'paths'=>array(
		'templates'=>'templates',
		'controllers'=>'controllers',
		'components'=>'components',
		'classes'=>'classes',
	
	),
	'errors'=>array(
		'useexceptions'=>false,
	),
);


if (file_exists('config.ini')){
	$app_config = parse_ini_file("config.ini", true);
}

include_once('core/configuration.php');
include_once('core/application.php');

Configuration::set_values($app_config);

// general classes autoinclude paths

// system classes
Application::add_class_path('/(.+)/','$1.php',Application::get_site_path().'/system/core');
Application::add_class_path('/(.+)/','$1.php',Application::get_site_path().'/system/helpers');

// application classes
Application::add_class_path('/(.+)/','$1.php',Application::get_site_path().Configuration::get('paths','classes'));

// miscelaneous classes
Application::add_class_path('/(.+)/','$1.php',Application::get_site_path().'/system/core/postscripts');

// controllers path
Application::add_class_path('/\\A([A-Z]\\w+)Controller\\z/','$1.php',Application::get_site_path().Configuration::get('paths','controllers'));

// components path
Application::add_class_path('/\\A([A-Z]\\w+)Component\\z/','$1.php',Application::get_site_path().Configuration::get('paths','components'));

function __autoload($class_name){
	$file = '';
	$filename = '';
	
	foreach (Application::get_classes_paths() as $params){
		if (preg_match($params['name_patern'], $class_name)){
			$filename = preg_replace($params['name_patern'], $params['filename'], $class_name);
			
			$file = $params['directory'].'/'.$filename;
			if (file_exists($file) == true) break;
		}
	}
	
	if (file_exists($file) == false) return false;
	
	include($file);
}

/*
 * inicializa la variables de la aplicacion. 
 */
new Application();


if (Configuration::get('errors','useexceptions'))
include_once('includes/errorexeption.php');
?>