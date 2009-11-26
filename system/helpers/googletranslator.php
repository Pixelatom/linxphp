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
        $request 	= sprintf($url, rawurlencode($string), $from, $to);
        
        try{
            $response 	= file_get_contents($request);
        }
        catch(Exception $e){
            return false;
        }
        //var_dump($response);
        $response = json_decode(mb_convert_encoding($response,"UTF-8"));
        //var_dump($response);
        
        if (!empty($response)){
            // Return translation
            return ($response->sentences[0]->trans);
        }
        return false;

    }
}