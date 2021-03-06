<?php
class MemcacheEngine implements iCacheEngine{

    // Memcache object
    protected $connection;
    protected $compress = 0;
    protected $prefix = '';

    public function __construct($settings) {
        if (!empty($settings['prefix']))
            $this->prefix = $settings['prefix'];
        
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
        return $this->connection->set($this->prefix.$key,$data,$this->compress,$ttl);
    }

    function fetch($key) {
        return $this->connection->get($this->prefix.$key);
    }

    function delete($key) {
        
        return $this->connection->delete($this->prefix.$key);
    }

    function addServer($host,$port = 11211, $weight = 10) {
        $this->connection->addServer($host,$port,true,$weight);
    }

}