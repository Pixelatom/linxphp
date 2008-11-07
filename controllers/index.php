<?php
/*
 * Linx PHP Framework
 * Copyright (C) 2008  Javier Arias
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
include "classes/postscripts/urlcompleter.php";

class IndexController extends Controller {
	
	
	function index(){		
		$t = new Template('urltest');
		$t->register_postscript(new UrlCompleter());
		
		$t->show();
	}
	
	function grid(){
		$pdo=new PDO('mysql:dbname=test;host=127.0.0.1','root','');
		$pdo->setAttribute(	PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$g = new GridComponent(new PDOQueryIterator($pdo,"select * from country limit 9"));
		//$g->get_paginator()->listings_per_page=5;
		$t = new Template('gridtest');
		
		$t->set('grid',$g);
		$t->show();
	}
	
	
}
?>