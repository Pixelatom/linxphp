<?php
class MemcacheEngine implements iCacheEngine{

    // Memcache object
    protected $_connection;

    public function __construct($settings) {

        $this->_connection = new MemCache;
        foreach ($settings['servers'] as $host){
            $this->addServer($host);
        }
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