<?php
/**
 *@package template system
 */

/**
 * Merged Template can handle a template tagged with multyples inline sub-templates
 *  
 */
class MergedTemplate extends Template{
    protected function get_template_code($name){
        
        //va a mostrar template default
        if (empty($name) and !empty($this->_default_template)){            
            $name=$this->_default_template;
        }
        elseif (empty($this->_default_template)) throw new Exception("Empty template");
        
        if (empty($this->_custom_path))
        $path = Application::get_site_path().Configuration::get('paths','templates').'/'.$name.'.php';
        else 
        $path = realpath($this->_custom_path).'/'.$name.'.php'; 
        
        if (!file_exists($path)) throw new Exception('Template `'.$name.'` does not exists');
        
        $code=file_get_contents($path);
        
        
        return $code;
    }
    
    protected function _get_template_name($name){		
		
		# va a mostrar template default
		if (empty($name)){
			if (empty($this->_default_template)) return "";
			$name=$this->_default_template;
		}
			
		return $name;
	}
    
    protected function include_template($name,&$vars){
        
        $code = $this->get_template_code($name);
        
        // <!--template--> tag interpreter.
        preg_match_all('/<!--\\s*template\\s*=\\s*(?P<name>\\w+?)\\s*-->(?P<code>.*?)<!--\\s*\/template\\s*=\\s*\\1\\s*-->/si', $code, $results, PREG_SET_ORDER);
        
        $original_vars = $vars;
        
        $new_code = $code;
        
        for ($i = 0; $i < count($results); $i++) {
            
			# if there is not a template set for that name, we'll create one now.
            if ( ! (isset($vars[$results[$i]['name']]) and is_object($vars[$results[$i]['name']]) and (is_subclass_of($vars[$results[$i]['name']],'BaseTemplate')))){
                # create the new template object
                $vars[$results[$i]['name']]=new InlineTemplate($results[$i]['name'],$this->_get_template_name($name),$this->_custom_path);
                # assign the same variables we have
                $vars[$results[$i]['name']]->_vars = array_merge($vars[$results[$i]['name']]->_vars, $original_vars);
            }
            
            # prints a template in place of the code.
            $new_code=str_replace($results[$i][0],'<?= $'.$results[$i]['name'].'?>',$new_code);	
			
			
        }
        
        // call the template already processed
        $this->clousure($new_code,$vars);      
	}
    
    protected function clousure($code,&$vars){
		extract($vars, EXTR_REFS);
		eval("?>" . $code . "<?"); 
	}
    
}
?>