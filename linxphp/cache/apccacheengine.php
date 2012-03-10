<?php
class APCCacheEngine implements iCacheEngine{

    function fetch($key) {
        return apc_fetch($key);
    }

    function store($key,$data,$ttl) {
        return apc_store($key,$data,$ttl);
    }

    function delete($key) {
        return apc_delete($key);
    }

}