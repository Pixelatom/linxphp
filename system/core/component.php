<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under GNU General Public License.
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