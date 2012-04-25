<?php
/**
 * This interface is the key of the abstraction of SimpleCache.
 * It provides a normalized form of classes to use different cache engines in the same way
 */
interface iCacheEngine {
    public function __construct($settings);
    public function fetch($key);
    public function store($key,$data,$ttl);
    public function delete($key);

}