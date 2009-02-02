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
 *  basic application configuration
 */
$app_config=array(
	'paths'=>array(
		'templates'=>'application/templates',
		'controllers'=>'application/controllers',		
		'classes'=>'application/classes',
		'hooks'=>'application/hooks',	
	),
	'errors'=>array(
		'useexceptions'=>false,
	),
);


include_once('core/configuration.php');
include_once('core/application.php');

/*
 complete the basic configuration with the config found in the ini file
*/
if (file_exists('config.ini')){
	$app_config = parse_ini_file("config.ini", true);
}
Configuration::set_values($app_config);


// general classes autoinclude paths

// system classes
Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",Application::get_site_path().'/system/core');
Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",Application::get_site_path().'/system/helpers');

// controllers path
Application::add_class_path('/\\A([A-Z]\\w+)Controller\\z/e',"strtolower('\\1').'.php'",Application::get_site_path().Configuration::get('paths','controllers'));    

// application classes
Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",Application::get_site_path().Configuration::get('paths','classes'));

/*
 * PHP autoload behavior is configured here, so it make use of the Application class configuration.
 */
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
 * Application object is initialized
 */
new Application();

if (Configuration::get('errors','useexceptions'))
include_once('includes/errorexeption.php');

/*
 * Loads hooks here, at the start of the application
 */


$hookdirs=array();
# loads system hooks
$hookdirs[] = new DirectoryIterator(realpath(dirname(__FILE__).'/hooks/'));
# application hooks
$hookdirs[] = new DirectoryIterator(realpath(Application::get_site_path().Configuration::get('paths','hooks').'/'));
/*
 now we will search for component hooks
 they should be placed on [componentsfolder]/[componentname]/hooks
*/
$dir = new DirectoryIterator(Application::get_site_path().Configuration::get('paths','components').'/' );
foreach($dir as $file )
{
  if ($file->getType()=='file' and preg_match('/(.+?)\\.php/i', $file->getFilename(), $result)){    
        $param = $result[1];
        /*echo $param;*/
        if (file_exists(Application::get_site_path().Configuration::get('paths','components').'/'.$param.'/hooks')
            and is_dir(Application::get_site_path().Configuration::get('paths','components').'/'.$param.'/hooks')){
            # we found a new hooks directory!
            $hookdirs[] = new DirectoryIterator(Application::get_site_path().Configuration::get('paths','components').'/'.$param.'/hooks/');
        }
  }  
}

foreach ($hookdirs as $dir){
	foreach ($dir as $file){
		if(!$file->isDot() && !$file->isDir() && preg_match("/\.php$/",$file->getFilename())) {		
			/*@var $dir DirectoryIterator */
            
            include_once($dir->getPath().'/'.$file->getFilename());
            
		}
	}
}

Event::run('system.ready');

?>