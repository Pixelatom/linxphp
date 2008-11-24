<?php
function system_404(){
    if (!headers_sent()){
        header(' ', true, 404);
        header('Status: 404 Not Found');
        header('HTTP/1.0 404 Not Found');	
    }
}
Event::add('system.404','system_404');
?>