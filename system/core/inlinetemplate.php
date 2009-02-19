<?php
/**
 *@package template system
 */
class InlineTemplate extends MergedTemplate{
    protected $_inline_name = null;
    
    
    public function set_inline_name($inline_name){
        $this->_inline_name=$inline_name;
        return $this;
    }
    
    function __construct($inline_name=null,$default_template=null,$custom_path=null){
        $this->set_inline_name($inline_name);
		$this->set_default_template($default_template);
		$this->set_custom_path($custom_path);
	}
    
    function show($inline_name=null,$template_name=null){
		
        if (!empty($inline_name)){
            $this->_inline_name=$inline_name;    
        }
        
		parent::show($template_name);
        
        if (!empty($inline_name)){
            $inline_name=$this->_inline_name;    
        }
        
        return $this;
	}
    
    protected function get_template_code($name){
        $code = parent::get_template_code($name);
        
        # extract the part of the template that belongs to the inline template we are searching for.
        //if (preg_match('/\\[template='.$this->_inline_name.'\\](?P<code>.*?)\\[\/template='.$this->_inline_name.'\\]/si', $code, $result)) {
        if (preg_match('/<!--\\s*template\\s*=\\s*'.$this->_inline_name.'\\s*-->(?P<code>.*?)<!--\\s*\/template\\s*=\\s*'.$this->_inline_name.'\\s*-->/si', $code, $result)) {
            $code=$result['code'];    
        }
        
        return $code;
    }
    
    
}
?>