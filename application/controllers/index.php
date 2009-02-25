<?php


class IndexController extends Controller {
	function index(){		
		MergedTemplate::factory('index')->show();
	}	
}
?>