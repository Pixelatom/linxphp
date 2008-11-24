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
 
class Template{
	
	protected $_default_template;
	protected $_custom_path=false;
	protected $_vars=array();
	
	function __construct($default_template=null,$custom_path=null){
		$this->set_default_template($default_template);
		$this->set_custom_path($custom_path);
	}
	
	public function set_custom_path($custom_path){
		$this->_custom_path=$custom_path;
	}
	
	public function set_default_template($default_template){
		$this->_default_template=$default_template;
	}
	
	
	
	public function set($varname,$value){
		$this->_vars[$varname]=$value;
	}
	public function key_exists($key){
		return isset($this->_vars[$key]);		
	}
	public function get($key){
		if (!isset($this->_vars[$key])) throw new Exception("Value '$key' doesn't exists for this template.");
		return $this->_vars[$key];
	}
	
	public function remove($key){
		if (!isset($this->_vars[$key])) return false;
		unset($this->_vars[$key]);
		return true;
	}
	
	public function clear(){
		$this->_vars=array();
	}
	
	
	/**
	 * Muestra el template
	 *
	 * @param unknown_type $name: (opcional) si no es especificado
	 * muestra el template default del objeto, 
	 * si es un strig, busca el archivo .php con el mismo nombre y lo usa 
	 * de template.
	 * si es otro objeto template, lo muestra agregandole las variables 
	 * que tiene seteadas este objeto.
	 */
	function show($name=null){
		Event::run('template.show_call',$name);
		$onbuffer=false;
		$onbuffer=ob_start();
		try{
			if (!empty($name) and is_object($name) and (get_class($name)=='Template' or  is_subclass_of($name,'Template'))){
				/*@var $name Template */
				$name = clone $name;
				
				foreach ($this->_vars as $key=>$value){
					$name->set($key,$value);
				}
				
				$name->show();
			}			
			else{
				
				# va a mostrar template default
				if (empty($name)){
					if (empty($this->_default_template)) throw new Exception("Empty template");
					$name=$this->_default_template;
				}
					
				if (empty($this->_custom_path))
				$path = Application::get_site_path().Configuration::get('paths','templates').'/'.$name.'.php';
				else 
				$path = realpath($this->_custom_path).'/'.$name.'.php'; 
				
				if (!file_exists($path)) throw new Exception('Template `'.$name.'` does not exists');
								
				foreach ($this->_vars as $key=>$value){
					if (isset($$key) == true){ throw new Exception('Unable to set var `'.$key.'`. Already set.');}
					
					# si es un template le asigna las variables que este template tiene
					if (!empty($value) and is_object($value) and (get_class($value)=='Template' or  is_subclass_of($value,'Template'))){
						/*@var $name Template */
						$value = clone $value;				
						foreach ($this->_vars as $newkey=>$newvalue){
							$value->set($newkey,$newvalue);
						}
					}			
					$$key=$value;
				}
				
				include($path);
				
				foreach ($this->_vars as $key=>$value){
					unset($$key);
				}
			}
		}
		catch(Exception $e){
			if ($onbuffer){
				ob_end_flush();
			}
			throw $e;
		}	
		
		if ($onbuffer){
			$output = ob_get_contents();
			ob_end_clean();
			
			
			Event::run('template.show',$output,$name);
			echo $output;
		}
	}
	public function __toString(){
		ob_start();
		$this->show();
		$return=ob_get_contents();
		ob_end_clean();
		return $return;
	}
}
?>