<?php

namespace linxphp\implementation;

use linxphp\common\Configuration;

class Module{
    public $config;
    
    public function __construct(){
        $this->config = new Configuration();
    }
    
    public static function load($path){
        $module = new Module();
        
    }
}