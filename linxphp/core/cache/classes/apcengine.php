<?php
class APCEngine implements iCacheEngine{
    protected $prefix = '';
    public function __construct($settings){
        if (!empty($settings['prefix']))
            $this->prefix = $settings['prefix'];
    }

    function fetch($key) {
        return apc_fetch($this->prefix.$key);
    }

    function store($key,$data,$ttl) {
        return apc_store($this->prefix.$key,$data,$ttl);
    }

    function delete($key) {
        return apc_delete($this->prefix.$key);
    }

}