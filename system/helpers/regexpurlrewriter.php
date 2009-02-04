<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under GNU General Public License.
 */

/**
 * This rewriter will change a traditional url and give the standard friendly url format
 */
class Regexpurlrewriter implements IUrlRewriter {
	public function rewrite($url){
		/*http://(.+?)/([^\/]+?\.php){0,1}(?:\?route=(.+?))*(&.*)*\z*/
		$url = preg_replace('/http:\/\/(.+?)\/([^\\/]+?\\.php){0,1}(?:\\?route=(.+?))*(&.*)*\\z/i', 'http://\\1/\\3\\4', $url);
		return $url;
	}
}
?>