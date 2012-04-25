<?php
function _modules_templates_folder(&$module,$path){
    # module templates
    if (file_exists(realpath($path.'/templates/'))){
        Template::add_path(realpath($path.'/templates/'));
    }
}
Event::add('module.load','_modules_templates_folder');