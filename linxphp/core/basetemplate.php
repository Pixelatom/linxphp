<?php
/**
 *@package template system
 */

abstract class BaseTemplate{
    protected $_vars=array();
    
    /**
	 * This method is static. Parameters are the same as creating a new instance.
	 * It creates a View instance and immediately returns it so method chaining is possible.
	 */
	static public function factory($default_template=null,$custom_path=null){
		$class = get_class($this);
		return new $class($default_template,$custom_path);
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
	
    public function __toString(){
        ob_start();
        try{
            $this->show();
        }
        catch(Exception $e){ 
        	ob_end_clean();
        	throw $e;
        }

        $return=ob_get_contents();
        ob_end_clean();
        return $return;

	}
    
    abstract function show();
}
?>