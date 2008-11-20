<?php
class Replacer extends PostScript{
    protected $_regexp='';
    protected $_replacement='';
    function __construct($regexp,$replacement){
        $this->_regexp=$regexp;
        $this->_replacement=$replacement;
    }
    public function process(&$output){        
        $output = preg_replace($this->_regexp, $this->_replacement, $output);
    }
}
?>