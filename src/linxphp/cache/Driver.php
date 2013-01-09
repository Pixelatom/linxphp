<?php
namespace linxphp\cache;

interface Driver{
    public function __construct($settings);
    public function fetch($key);
    public function store($key,$data,$ttl = 0);
    public function delete($key);
}