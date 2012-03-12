<?php

interface iCacheEngine {
    public function __construct($settings);
    public function fetch($key);
    public function store($key,$data,$ttl);
    public function delete($key);

}