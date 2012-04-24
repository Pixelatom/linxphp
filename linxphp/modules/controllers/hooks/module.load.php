<?php
function _modules_controllers_folder(&$module,$path){
    
    // configures default application router
    if ($module == 'controllers'){
        Application::$request_url = new Url();
        Application::set_application_router(new ApplicationRouter());                
    }
    # module controllers (I'm not quite sure it's a good idea, let's try)                
    if (file_exists(realpath($path.'/controllers/'))){                
        Application::add_class_path('/\\A([A-Z]\\w+)Controller\\z/e',"strtolower('\\1').'.php'",$path.'/controllers');   
    }
}
Event::add('module.load','_modules_controllers_folder');