<?php
use linxphp\common\Event;

Event::add('system.ready',function(){
    echo 'App loaded';
});