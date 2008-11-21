<?php
/*
 * Linx PHP Framework
 * Copyright (C) 2008  Javier Arias
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/.
 */
 
error_reporting(E_ALL);
/*
 * necesario para la correcta ejecucion del sitio
 */
include_once('system/linx.php');
session_start();

/*
 * reescribe las url automaticamente para que sean amigable
 * (necesita que este activado el mod rewirte del apache)
 */
Url::set_default_url_rewriter(new Regexpurlrewriter());

function append(&$output){
    $output.=" this is done with events";
}


Event::add('template.show','append');



/* 
 * incluye el controlador indicado en la URL y ejecuta la accion que corresponde
 */
Application::route();
?>