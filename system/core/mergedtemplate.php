<?php

class MergedTemplate extends Template{
    
    private $_template_content = null;
    
    public function set_content($content){
        $this->_template_content=$content;
    }
    
    protected function get_template_code($name){
        if (empty($name) and empty($this->_default_template) and !empty($this->_template_content)){            
            $code = $this->_template_content;
            
        }else{
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
        }
        
        return $code;
    }
    
    protected function include_template($name,&$vars){
        
        $code = $this->get_template_code($name);
        
        // [template] tag interpreter.
        preg_match_all('/\\[template=(?P<name>.+?)\\](?P<code>.*?)\\[\/template=\\1\\]/si', $code, $results, PREG_SET_ORDER);
        
        $original_vars = $vars;
        
        $new_code = $code;
        
        for ($i = 0; $i < count($results); $i++) {
            
			# if there is not a template set for that name, we'll create one now.
            if ( ! (isset($vars[$results[$i]['name']]) and is_object($vars[$results[$i]['name']]) and (is_subclass_of($vars[$results[$i]['name']],'BaseTemplate')))){				
                $vars[$results[$i]['name']]=new MergedTemplate();				
				$vars[$results[$i]['name']]->set_content($results[$i]['code']);	
                $vars[$results[$i]['name']]->_vars = array_merge($vars[$results[$i]['name']]->_vars, $original_vars);
            }
            /*
			if (get_class($vars[$results[$i]['name']])=='InlineTemplate'){
				$vars[$results[$i]['name']]->set_content($results[$i]['code']);
				$vars[$results[$i]['name']]->_vars = array_merge($vars[$results[$i]['name']]->_vars, $original_vars);
				$new_code=str_replace($results[$i][0],'',$new_code);
			}
			else{*/
				# prints a template in place of the code.
				$new_code=str_replace($results[$i][0],'<?= $'.$results[$i]['name'].'?>',$new_code);	
			//}
			
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