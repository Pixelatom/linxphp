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
		'includes'=>'includes',
	),
	'errors'=>array(
		'useexceptions'=>false,
	),
);


if (file_exists('config.ini')){
	$app_config = parse_ini_file("config.ini", true);
}

include_once($app_config['paths']['classes'].'/configuration.php');
include_once($app_config['paths']['classes'].'/application.php');

Configuration::set_values($app_config);

function __autoload($class_name){
	
	if (preg_match('/\\A[A-Z]\\w+Controller\\z/', $class_name)){ 
		$filename = str_replace('controller','',strtolower($class_name)).'.php';
		$file = Application::get_site_path().Configuration::get('paths','controllers').'/'.$filename;	
	}	
	elseif (preg_match('/\\A[A-Z]\\w+Component\\z/', $class_name)){ 
		$filename = str_replace('component','',strtolower($class_name)).'.php';
		$file = Application::get_site_path().Configuration::get('paths','components').'/'.$filename;	
	}	
	else{
		$filename = strtolower($class_name).'.php';
		$file = Application::get_site_path().Configuration::get('paths','classes').'/'.$filename;
	}
	
	if (file_exists($file) == false){		
		foreach (Application::get_classes_paths() as $params){
			if (preg_match($params['name_patern'], $class_name)){
				$filename = preg_replace($params['name_patern'], $params['filename'], $class_name);
				
				$file = $params['directory'].'/'.$filename;
				if (file_exists($file) == false) break;
			}
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
include_once(Configuration::get('paths','includes').'/errorexeption.php');
?>