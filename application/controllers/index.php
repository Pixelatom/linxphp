<?php


class IndexController extends Controller {
	function index(){
		
		$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
		
		$array = new PDOQueryIterator($db,'select * from country',PDO::FETCH_ASSOC);
		
		$grid = new GridComponent($array);
		
		$grid->set_items_per_page(20);
		
		$view = new CombinedTemplate('index');
		$view->grid = $grid;
		$view->show();
	}	
}
?>