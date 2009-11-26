<?php
class GoogleTranslator implements ITranslationProvider{
    
    /**
     * Translate a string using google translator
     *
     * @access public
     * @param string $from
     * @param string $to
     * @param string $string
     * @return string
     */
    public function translate($from, $to, $string) {
        

        $url 		= 'http://translate.google.com/translate_a/t?client=t&text=%s&sl=%s&tl=%s';
        $request 	= sprintf($url, urlencode($string), $from, $to);
        try{
            $response 	= file_get_contents($request);
        }
        catch(Exception $e){
            return false;
        }

        $parts 		= explode(",[", $response);
        $clean		= substr($parts[0],1, -1);
        if(substr($clean,0, 1) == '"') {
            $clean = substr($clean, 1, (strlen($clean)-1));
        }

        if(strrpos($clean, '",') > 0) {
            $clean = substr($clean, 0, strrpos($clean, '",'));
        }

        // Return translation
        return mb_convert_encoding(stripslashes($clean),"UTF-8");

    }
}

?>
