<?php
function _application_templates_folder(){
    # finally we add the application template path (we add this at the last path so it's the first to be used)
    Template::add_path(Application::get_site_path() . Configuration::get('paths', 'templates','application/templates'));
}
Event::add('system.ready','_application_templates_folder');