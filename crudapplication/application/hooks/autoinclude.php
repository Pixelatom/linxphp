<?php
function add_includes(){
  $appath = Application::get_site_path();
  $appath = $appath.'application/includes/';
  $appath = realpath($appath);
  $did = opendir($appath);
  while ($file = readdir($did))
  {

  	if(strpos($file,'.php')!== false)
  	  include_once($appath.'/'.$file);
  }
}
Event::add('system.ready','add_includes');

