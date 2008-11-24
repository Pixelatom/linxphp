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

# support for rendering only an Component 
function components_router_start(){
    Registry::set('_component_output',false);
    # primero nos fijamos si hay que renderizar solamente un componente
    if (Application::$request_url->param_exists('_component')){
        
        Registry::set('_component_class_name',(Application::$request_url->get_param('_component')));
        Application::$request_url->remove_param('_component');			
        
        Registry::set('_component_original_url',clone Application::$request_url);
        Registry::set('_component_output',true);
        ob_start();		
    }
}
Event::add('system.routing','components_router_start');

function component_router_end(){
    if (Registry::get('_component_output')){
        ob_end_clean();
        Application::$request_url=egistry::get('_component_original_url');
        $class=Registry::get('_component_class_name');
        $component=new $class;
        $component->show();
    }
    Registry::remove('_component_output');
    Registry::remove('_component_class_name');
    Registry::remove('_component_original_url');
}
Event::add('system.routing','system.post_routing');

?>