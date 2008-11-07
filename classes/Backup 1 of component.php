<?php
/*
 * PHP Mini Framework
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
 
/**
 * un objeto de vista independiente con codigo controlador propio
 * puede ser considerado como una union de controlador/vista en un mismo objeto
 * las caracteristicas son
 * - es independiente del controlador en el q se use
 * - es reutilizable
 * - se renderiza con echo o mediante la llamada a render, por lo que puede ser utilizado en templates
 */
abstract class Component{
	public function __construct(){
		
	}
	public function show(){
		try{
			$this->render();	
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
		
	}
	
	public function __toString(){
		ob_start();
		$this->show();
		$return=ob_get_contents();
		ob_end_clean();
		return $return;
	}
	abstract protected function render();
}
?>