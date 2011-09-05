<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias
 * Licensed under MIT License
 */


if (isset($_SERVER['REDIRECT_SUBDOMAIN_DOCUMENT_ROOT']))
$application_directory = dirname(realpath($_SERVER['REDIRECT_SUBDOMAIN_DOCUMENT_ROOT'].$_SERVER['PHP_SELF']));
else
$application_directory = dirname(realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']));

$system_directory = dirname(__FILE__);


/*
 *  basic application configuration
 */
$app_config=array(
    'paths'=>array(
        'templates'=>'application/templates',
        'controllers'=>'application/controllers',
        'classes'=>'application/classes',
        'hooks'=>'application/hooks',
        'models'=>'application/models',
    ),
    'errors'=>array(
        'useexceptions'=>false,
    ),
);


include_once('helpers/configuration.php');
include_once('core/application.php');

/*
 complete the basic configuration with the config found in the ini file
*/
if (file_exists("$application_directory/config.ini")){
    $app_config = parse_ini_file("$application_directory/config.ini", true);
}
Configuration::set_values($app_config);


// general classes autoinclude paths

// system classes
Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$system_directory.'/core');
Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$system_directory.'/helpers');

// controllers path
Application::add_class_path('/\\A([A-Z]\\w+)Controller\\z/e',"strtolower('\\1').'.php'",Application::get_site_path().Configuration::get('paths','controllers'));    

// application classes
$path_array = Application::get_site_path().Configuration::get('paths','classes');
$path_array = explode(',', $path_array);
foreach ($path_array as $path){
    $path = trim($path);
    if (file_exists(realpath($path.'/')))
    Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$path);
}


// models classes
$path_array = Application::get_site_path().Configuration::get('paths','models');
$path_array = explode(',', $path_array);
foreach ($path_array as $path){
    $path = trim($path);
    if (file_exists(realpath($path.'/')))
    Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$path);
}

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

if (Configuration::get('errors','useexeptions'))
include_once('includes/errorexeption.php');

/*
 * Loads hooks here, at the start of the application
 */


$hookdirs=array();
# loads system hooks
$hookdirs[] = new DirectoryIterator(realpath(dirname(__FILE__).'/hooks/'));

# application hooks
if (file_exists(realpath(Application::get_site_path().Configuration::get('paths','hooks').'/'))){
    $hookdirs[] = new DirectoryIterator(realpath(Application::get_site_path().Configuration::get('paths','hooks').'/'));
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