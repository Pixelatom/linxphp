<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
/*
 * necesario para la correcta ejecucion del sitio
 */
include_once('../linxphp/linxphp/linx.php');
if (!session_id()) 
session_start();

/*
 * reescribe las url automaticamente para que sean amigable
 * (necesita que este activado el mod rewirte del apache)
 */
Url::set_default_url_rewriter(new Regexpurlrewriter());


/* 
 * incluye el controlador indicado en la URL y ejecuta la accion que corresponde
 */
Application::route();