<?php
class CombinedTemplate extends Template{
    protected function clousure($path,&$vars){
		
        $code=file_get_contents($path);
      
        preg_match_all('/\\[template=(?P<name>.+?)\\](?P<code>.*?)\\[\/template\\]/si', $code, $results, PREG_SET_ORDER);
        
        
        
        
        for ($i = 0; $i < count($results); $i++) {
            var_dump($results[$i]['name']);
            $vars[$results[$i]['name']]=new DynamicTemplate($results[$i]['code']);
        }
        
      
	}
}
?>