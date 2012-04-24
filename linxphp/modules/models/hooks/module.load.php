<?php
function _modules_models_folder(&$module,$path){
    # module models
    if (file_exists(realpath($path.'/models/'))){
        Application::add_class_path('/(.+)/e',"strtolower('\\1').'.php'",$path.'/models');
    }
}
Event::add('module.load','_modules_models_folder');