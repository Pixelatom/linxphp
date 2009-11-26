<?php
interface ITranslationStorer{
    public function load($languaje);
    public function get($string);
    public function set($string,$translation);    
}