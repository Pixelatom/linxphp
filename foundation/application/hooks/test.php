<?php
use linxphp\common\Event;

Event::add('system.ready',function(){
    echo '<p>System Ready</p>';
});

