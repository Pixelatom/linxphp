<?php

/**
 *  configure cache at the start of the application
 */
function _config_cache(){
    Mapper::config(array('use_cache'=>false));
    
}
Event::add('system.ready','_config_cache');