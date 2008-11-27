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
	
	/**
	 * This method is static. Parameters are the same as creating a new instance.
	 * It creates a View instance and immediately returns it so method chaining is possible.
	 */
	static public function factory($default_template=null,$custom_path=null){
		return new Template($default_template,$custom_path);
	}
	/**
	 * change the path where the class will search for the file it has to show
	 */
	public function set_custom_path($custom_path){
		$this->_custom_path=$custom_path;
		return $this;
	}
	/**
	 * set a default file or object to render when the method show is called without parameters
	 */
	public function set_default_template($default_template){
		$this->_default_template=$default_template;
		return $this;
	}
	
	
	/**
	 * set() can be used to set a variable in a view
	 */
	public function set($varname,$value){
		$this->_vars[$varname]=$value;
		return $this;
	}
	
	/**
	 * bind() is like set only the variable is assigned by reference.
	 */
	public function bind($varname,&$value){
		$this->_vars[$varname] = &$value;
		return $this;
	}
	/**
	 * return true if the var name is set for this template
	 */
	public function key_exists($key){
		return isset($this->_vars[$key]);
		return $this;
	}
	
	/**
	 * get the value set for a template variable
	 */
	public function get($key){
		if (!isset($this->_vars[$key])) throw new Exception("Value '$key' doesn't exists for this template.");
		return $this->_vars[$key];
	}
	
	/**
	 * remove a template variable by name
	 * 
	 */
	public function remove($key){
		if (!isset($this->_vars[$key])) return $this;
		unset($this->_vars[$key]);
		return $this;
	}
	
	/**
	 * remove all the variables set for this template
	 */
	public function clear(){
		$this->_vars=array();
		return $this;
	}
	
	private function clousure($path,&$vars){
		extract($vars, EXTR_REFS);
		include($path);
	}
	
	/**
	 * renders the output of the View.
	 *
	 *@param unknown_type $name: (opcional) si no es especificado
	 * muestra el template default del objeto, 
	 * si es un strig, busca el archivo .php con el mismo nombre y lo usa 
	 * de template.
	 * si es otro objeto template, lo muestra agregandole las variables 
	 * que tiene seteadas este objeto.
	 *
	 *@todo soporte para variables por referencia 
	 * 
	 */
	function show($name=null){
		Event::run('template.show_call',$name);
		
		# sumamos a las variables seteadas con los metodos comunes, las variables seteadas dinamicamente.
		$vars=array_merge(get_object_vars($this),$this->_vars);
		
		$onbuffer=false;
		$onbuffer=ob_start();
		try{
			if (!empty($name) and is_object($name) and (get_class($name)=='Template' or  is_subclass_of($name,'Template'))){
				/*@var $name Template */
				$name = clone $name;
				
				# copiamos todas las variables que tenemos en este template al template que se paso por parametros
				foreach ($vars as $key=>&$value){
					if (!isset($name->_vars[$key])){
						$name->_vars[$key]=&$value;	
					}
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
				
				#buscamos entre todas las variables que tenemos asignadas por un objeto template
				foreach ($vars as $key=>&$value){
					# si es un template le asigna las variables que este template tiene
					if (!empty($value) and is_object($value) and (get_class($value)=='Template' or  is_subclass_of($value,'Template'))){						
						$value = clone $value;				
						
						foreach ($vars as $key1=>&$value1){
							if (!isset($value->_vars[$key1]) and $key != $key1){								
								$value->_vars[$key1] = &$value1;	
							}
						}
						
					}
				}
				
				$this->clousure($path,$vars);
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
		return $this;
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