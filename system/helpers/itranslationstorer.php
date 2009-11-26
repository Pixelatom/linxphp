<?php
interface ITranslationStorer{
    public function set_language($languaje);
    public function get($string);
    public function set($string,$translation);    
}