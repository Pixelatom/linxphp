<?php

interface iCacheEngine {

    public function fetch($key);
    public function store($key,$data,$ttl);
    public function delete($key);

}