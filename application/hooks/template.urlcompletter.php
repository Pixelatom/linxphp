<?php
class UrlCompleter{
    public function process(&$output){
        
        preg_match_all('/(<[a-z]+[^>]*?(?:src|href)\\s*=\\s*")((?!(?:https?|ftp):\/\/(?:[-A-Z0-9.]+))(?:[-A-Z0-9+&@#\/%=~_|!:,.;]*)?(?:\\?[-A-Z0-9+&@#\/%=~_|!:,.;]*)?)("[^>]*>)/i', $output, $matches, PREG_PATTERN_ORDER);        /*echo count ($matches);*/
        for ($i = 0; $i < count($matches[0]); $i++) {
            // $matches[0][$i];
            $u = new Url($matches[2][$i]);
            
            $output=str_replace($matches[0][$i],$matches[1][$i].$u->get_url().$matches[3][$i],$output);
        }
        
        
    }
}
Event::add('template.show',array('UrlCompleter','process'));
?>