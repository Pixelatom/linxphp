<?php
/**
 * This file gives support to components objetcs to the framework
 */

# add the autoinclude path
function components_autoinclude(){
    
    $path=Application::get_site_path().'components';
    
    if (Configuration::get('paths','components',false)){
        $path=Application::get_site_path().Configuration::get('paths','components');
    }
    
    Application::add_class_path('/\\A([A-Z]\\w+)Component\\z/','$1.php',$path);    
}
Event::add('system.ready','components_autoinclude');

# support for passing a Component as a Template parameter
function components_template_show($output,$name){
    if (!empty($name) and is_object($name) and is_subclass_of($name,'Component')){
        $output=$name->__toString();
    }
}
Event::add('template.show','components_template_show');
?>