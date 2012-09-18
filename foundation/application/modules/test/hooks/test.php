<?php
use linxphp\common\Event;

Event::add('module.load',function($module){
    //echo "<p>Module '{$module->name()}' loaded</p>";
});