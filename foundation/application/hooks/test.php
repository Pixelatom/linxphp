<?php
use linxphp\common\Event;

Event::add('system.ready',function(){
    //echo '<p>System Ready</p>';
    $request = linxphp\rest\Request::fromRoute('lol');
    
    echo $request->url().'<br>';
    echo linxphp\common\URL::current();
});

