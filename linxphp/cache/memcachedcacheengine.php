<?php
class MemcachedCacheEngine implements iCacheEngine{

    // Memcache object
    public $connection;

    function __construct() {

        $this->connection = new MemCache;

    }

    function store($key, $data, $ttl) {

        return $this->connection->set($key,$data,0,$ttl);

    }

    function fetch($key) {

        return $this->connection->get($key);

    }

    function delete($key) {

        return $this->connection->delete($key);

    }

    function addServer($host,$port = 11211, $weight = 10) {

        $this->connection->addServer($host,$port,true,$weight);

    }

}