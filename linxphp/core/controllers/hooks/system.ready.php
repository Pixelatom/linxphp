<?php
function _application_controllers_folder(){
    // controllers path
    Application::add_class_path('/\\A([A-Z]\\w+)Controller\\z/e',"strtolower('\\1').'.php'",Application::get_site_path().Configuration::get('paths','controllers','application/controllers'));    
}
Event::add('system.ready','_application_controllers_folder');