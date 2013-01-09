<?php

namespace linxphp\cache\drivers;

class Redis implements \linxphp\cache\driver {

    protected $connection;
    protected $compress = 0;
    protected $prefix = '';

    public function __construct($settings) {
        if (!empty($settings['prefix']))
            $this->prefix = $settings['prefix'];

        $this->connection = new \Redis;


        $parts = explode(':', $settings['server']);
        $host = $parts[0];
        $port = 6379;
        if (isset($parts[1]))
            $port = $parts[1];

        $this->connection = new \Redis();
        $this->connection->connect($host, $port);

        if (isset($settings['password'])) {
            $this->connection->auth($settings['password']);
        }
    }

    function store($key, $data, $ttl = 0) {
        $return = $this->connection->set($this->prefix . $key, $data);

        if ($ttl > 0)
            $this->connection->expire($this->prefix . $key, $ttl);

        return $return;
    }

    function fetch($key) {
        return $this->connection->get($this->prefix . $key);
    }

    function delete($key) {
        return $this->connection->delete($this->prefix . $key);
    }

}