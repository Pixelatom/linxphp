<?php
class DynamicTemplate extends BaseTemplate{
    private $_template_content = null;
    
    function __construct($content){
        $this->_template_content=$content;
    }
    
    function show(){
        Event::run('template.show_call',$name);
        # sumamos a las variables seteadas con los metodos comunes, las variables seteadas dinamicamente.
		$vars=array_merge(get_object_vars($this),$this->_vars);
		
		$onbuffer=false;
		$onbuffer=ob_start();
		try{
            
            $this->clousure($this->_template_content,$vars);
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
    
    protected function clousure($code,&$vars){
		extract($vars, EXTR_REFS);
		eval("?>" . $code . "<?"); 
	}
    
}
?>