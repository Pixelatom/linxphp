<?php


class IndexController extends Controller {
	function index(){		
		$view = new MergedTemplate('index');		
		$view->echo=0;
		$view->show();
	}	
}
?>