<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias
 * Licensed under MIT License
 */

// detect base app dir
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
        'classes'=>'application/classes',
        'hooks'=>'application/hooks',
        'modules'=>'application/modules',
    ),
    'errors'=>array(
        'useexceptions'=>false,
    ),
);

include_once 'classes/configuration.php';
include_once 'classes/application.php';

/*
 complete the basic configuration with the config found in the ini file
*/
if (file_exists("$application_directory/config.ini")) {
    $app_config = parse_ini_file("$application_directory/config.ini", true);
}
Configuration::set_values($app_config);

// Add System Classes
Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$system_directory.'/classes');

/*
 * PHP autoload behavior is configured here, so it make use of the Application class configuration.
 */
function __autoload($class_name)
{
    $file = '';
    $filename = '';

    foreach (Application::get_classes_paths() as $params) {
        if (preg_match($params['name_patern'], $class_name)) {
            $filename = preg_replace($params['name_patern'], $params['filename'], $class_name);

            $file = $params['directory'].'/'.$filename;
            if (file_exists($file) == true) break;
        }
    }

    if (file_exists($file) == false) return false;

    include($file);
}

if (Configuration::get('errors','useexeptions'))
include_once 'includes/errorexeption.php';

/**
 * Register App classes path
 */
$path_array = Application::get_site_path().Configuration::get('paths','classes','application/classes');
$path_array = explode(',', $path_array);
foreach ($path_array as $path) {
    $path = trim($path);
    if (file_exists(realpath($path.'/')))
    Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$path);

    // adds subfolders inside class folder as well
    $path = $path.'/';
    foreach (glob($path . '*', GLOB_ONLYDIR) as $dir) {
        Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$dir);
    }
}

/*
 * Loads hooks here, at the start of the application
 */
$hookdirs=array();

# application hooks
if (file_exists(realpath(Application::get_site_path().Configuration::get('paths','hooks','application/hooks').'/'))) {
    $hookdirs[] = new DirectoryIterator(realpath(Application::get_site_path().Configuration::get('paths','hooks').'/'));
}

# include all hook files.
foreach ($hookdirs as $dir) {
    foreach ($dir as $file) {
        if (!$file->isDot() && !$file->isDir() && preg_match("/\.php$/",$file->getFilename())) {
            /*@var $dir DirectoryIterator */
            include_once($dir->getPath().'/'.$file->getFilename());
        }
    }
}

/*
 * load all modules
 */
$path_array = array(
    realpath($system_directory.'/core/'), //core modules
    realpath($system_directory.'/modules/'), // extra framework's modules
);
$app_modules_path = Application::get_site_path().Configuration::get('paths','modules','application/modules'); // application modules paths
$app_modules_path = explode(',', $app_modules_path);
$path_array = array_merge($path_array,$app_modules_path);

foreach ($path_array as $path) {
    if($path == false) continue;
    $path = trim($path);
    if (file_exists(realpath($path.'/'))) {
        # read all modules folders
        $moduledir = new DirectoryIterator(realpath($path.'/'));
        foreach ($moduledir as $file) {
            if (!$file->isDot() && $file->isDir()) {
                # extracts module folder
                $module = ($moduledir->getPath().'/'.$file->getFilename());

                # module classes
                if (file_exists(realpath($module.'/classes/'))) {
                    Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$module.'/classes');
                }

                # module hooks
                if (file_exists(realpath($module.'/hooks/'))) {
                    $dir = new DirectoryIterator(realpath($module.'/hooks/'));
                    foreach ($dir as $file) {
                        if (!$file->isDot() && !$file->isDir() && preg_match("/\.php$/",$file->getFilename())) {

                            /*@var $dir DirectoryIterator */
                            include_once($dir->getPath().'/'.$file->getFilename());
                        }
                    }
                }

                // tells the system the module is loaded
                $module_name = strtolower(basename($module));
                Event::run('module.load',$module_name,$module);
            }
        }
    }
}

# execute hooks
Event::run('system.ready');
