<?php
/**
 * Default listener for 404 error event
 * you should need to know the name of this function if you want to change
 * the default behavior on a 404 not found error
 */
function system_404(){
    if (!headers_sent()){
        header(' ', true, 404);
        header('Status: 404 Not Found');
        header('HTTP/1.0 404 Not Found');	
    }
}
Event::add('system.404','system_404');
?>