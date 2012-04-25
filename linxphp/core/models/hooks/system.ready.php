<?php
function _application_models_folder(){
    // application models classes
    $path_array = Application::get_site_path().Configuration::get('paths','models','application/models');
    $path_array = explode(',', $path_array);
    foreach ($path_array as $path){
        $path = trim($path);
        if (file_exists(realpath($path.'/')))
        Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$path);    
    }
}
Event::add('system.ready','_application_models_folder');