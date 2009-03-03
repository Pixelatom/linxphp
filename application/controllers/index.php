<?php


class IndexController extends Controller {
	function index(){
		# showing up a template
		Template::factory('index')->show();
	}	
}
?>