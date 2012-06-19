<?php
class MemcacheEngine implements iCacheEngine{

    // Memcache object
    protected $connection;
    protected $compress = 0;

    public function __construct($settings) {
        $this->connection = new Memcache;
        foreach ($settings['servers'] as $server){
            $parts = explode(':', $server);
            $host = $parts[0];
            $port = 11211;
            if (isset($parts[1])) $port = $parts[1];
            
            $this->addServer($host,$port);
        }
        if ($settings['compress']){
            $this->compress = MEMCACHE_COMPRESSED;
        }
    }

    function store($key, $data, $ttl) {
        $key = md5($key);
        return $this->connection->set($key,$data,$this->compress,$ttl);
    }

    function fetch($key) {
        $key = md5($key);
        return $this->connection->get($key);
    }

    function delete($key) {
        $key = md5($key);
        return $this->connection->delete($key);
    }

    function addServer($host,$port = 11211, $weight = 10) {
        $this->connection->addServer($host,$port,true,$weight);
    }

}