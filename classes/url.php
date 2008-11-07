<?php
/*
 * Linx PHP Framework
 * Copyright (C) 2008  Javier Arias
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
 
/*
TIP:
codigo para emular la reescritura que hace el htaccess.

$helper=new Url();	
if (preg_match('¿'.($helper->get_application_path()).'/(?!index\\.php\\?route=)(.*)¿i', $_SERVER['HTTP_REFERER'])) {	
	$referer_url=new Url(preg_replace('¿'.($helper->get_application_path()).'/(.*)¿i', $helper->get_application_path().'/index.php?route=$1', ($_SERVER['HTTP_REFERER'])));				
}
*/

class Url{
	private $_https=false;
	private $_server_name=false;
	private $_server_port=false;
	private $_request_uri=false;
	private $_params=array();
	
	private static $_default_rewriter=null;
	
	public static function set_default_url_rewriter(IUrlRewriter $rewriter){
		self::$_default_rewriter=$rewriter;
	}

	public $use_rewriter=true;
	
	public function get_application_path(){
		$current_url='http';
		$current_url.=($this->_https)?'s':'';
		$current_url.='://'. $this->_server_name;
		if (!empty($this->_server_port) and $this->_server_port!=80)
		$current_url.=":".$this->_server_port;
		$current_url.=$this->_request_uri;
		
		$current_url=explode('/',$current_url);
		array_pop($current_url);
		$current_url=implode('/',$current_url);
		return $current_url;
	}
	
	function __construct($url=null){
		if (empty($url)){
			$this->_https=(isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on')?true:false;
			$this->_server_name=$_SERVER['SERVER_NAME'];
			$this->_server_port=$_SERVER['SERVER_PORT'];
			$this->_request_uri=$_SERVER['SCRIPT_NAME'];
			
			foreach ($_GET as $name=>$value){				
				$this->_params[$name]=$value;				
			}
		}
		else{			
			if (preg_match('·(?:(?<protocol>https?|ftp)://(?<domain>[-A-Z0-9.]+))?(?<file>/?[-A-Z0-9+&@#/%=~_|!:,.;]*)?(?<parameters>\?[-A-Z0-9+&@#/%=~_|!:,.;]*)?·i', $url, $result)) {
				
				if (!empty($result['protocol']))
				$this->_https=($result['protocol']=='https')?true:false;
				else
				$this->_https=(isset($_SERVER['HTTPS']) and $_SERVER['HTTPS']=='on')?true:false;
				
				$this->_server_port=isset($result['port'])?$result['port']:$this->_server_port=$_SERVER['SERVER_PORT'];
				
				if (!empty($result['domain']) and !empty($result['file'])){
					$this->_server_name=$result['domain'];
					$this->_request_uri=$result['file'];
				}
				elseif(empty($result['domain']) and !empty($result['file'])){
					$this->_server_name=$_SERVER['SERVER_NAME'];
					
					$filepath = dirname($_SERVER['SCRIPT_NAME']);
					
					
					$this->_request_uri=str_replace('//','/',$filepath.'/'.$result['file']);
				}
				else{
					$this->_server_name=$_SERVER['SERVER_NAME'];		
					$this->_request_uri=$_SERVER['SCRIPT_NAME'];
				}
				
				
				$this->_request_uri.=(isset($result['parameters']))?$result['parameters']:'';
				if (isset($result['parameters'])){
				$parse_request=explode('?',$this->_request_uri);		
				$this->_request_uri=array_shift($parse_request);
				
				$parse_request=implode($parse_request);
				$parse_request=explode("&",$parse_request);
				
				foreach ($parse_request as $param){
					$param=explode("=",$param);
					$this->_params[$param[0]]='';
					if (isset($param[1]))
					$this->_params[$param[0]]=$param[1];
				}
				}
			}
		}
	}
	public function set_server_name($name){
		$this->_server_name=$name;
		return $this;
	}
	public function get_server_name(){
		return $this->_server_name;
	}
	
	public function get_url(){
		$current_url='http';
		$current_url.=($this->_https)?'s':'';
		$current_url.='://'. $this->_server_name;
		if (!empty($this->_server_port) and $this->_server_port!=80)
		$current_url.=":".$this->_server_port;
		$current_url.=$this->_request_uri;
		
		if (count($this->_params)>0){
			$request='';
			foreach ($this->_params as $name=>$value){
				if ($request=='')
				$request.='?';
				else 
				$request.='&';
				
				$request .= $name.'='.$value;
			}
			$current_url.= $request;
		}
		
		
		
		if (!empty(self::$_default_rewriter) and is_object(self::$_default_rewriter) and $this->use_rewriter){
			$current_url=self::$_default_rewriter->rewrite($current_url);
		}
		
		return $current_url;
	}
	
	public function set_param($name,$value){
		$this->_params[$name]=$value;
		return $this;
	}
	public function get_param($name,$default_value=null){
		if (!array_key_exists($name,$this->_params)) return $default_value;
		return $this->_params[$name];
	}
	public function clear_params(){
		$this->_params=array();
		return $this;
	}
	public function remove_param($remove){
		$new_params=array();
		foreach ($this->_params as $name=>$value){
			if ($name<>$remove)
			$new_params[$name]=$value;
		}
		$this->_params=$new_params;
		return $this;
	}
	public function param_exists($name){
		return array_key_exists($name,$this->_params);
	}
	public function __toString(){
		return $this->get_url();
	}
}
?>