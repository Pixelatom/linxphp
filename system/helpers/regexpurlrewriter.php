<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under GNU General Public License.
 */

/**
 * This rewriter will change a traditional url and give the standard friendly url format
 * This class is used in conjunction with Url helper, it'll change the way the url are retourned.
 */
class Regexpurlrewriter implements IUrlRewriter {
	public function rewrite($url){
                //return $url;
		/*http://(.+?)/([^\/]+?\.php){0,1}(?:\?route=(.+?))*(&.*)*\z*/
                $pattern = '/http(s)*:\/\/(.+?)\/([^\/]+?\.php){0,1}((?:(?![&?]route=)[&?][^&?]+)*)(?:[&?]route=([^&?]+))*((?:(?![&?]route=)[&?][^&?]+)*)\z/i';
                if (preg_match($pattern, $url)){
                    
                    $url = preg_replace($pattern, 'http\1://\2/\5\4\6', $url);
                }	
                
		return $url;
	}
}
?>