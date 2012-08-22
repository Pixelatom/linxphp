<?php
/*
 * Linx PHP Framework
 * Author: Javier Arias.
 * Licensed under MIT License.
 */

// convert all errors to exceptions (zero tolerance)
ini_set('display_errors','1');
error_reporting(E_ALL);


set_error_handler(function ($code, $string, $file, $line)
{
    switch ($code) {
        case E_DEPRECATED: // ignores new DEPRECATED error to allow developers to use third party libraries
               return;
        case E_WARNING:
            throw new ErrorException($string, $code, $code,$file,$line);
        default:
            throw new ErrorException($string, $code, $code,$file,$line);
    }
}, E_ALL);
