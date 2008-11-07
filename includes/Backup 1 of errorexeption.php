<?php
/*
 * PHP Mini Framework
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
 
class WarningException extends ErrorException {}
class FatalErrorException extends ErrorException {}

function exceptionsHandler($code, $string, $file, $line) {
	switch ($code){
		case E_WARNING:
			throw new WarningException($string, $code, $code,$file,$line);
		default:
			throw new ErrorException($string, $code, $code,$file,$line);
	}
}
set_error_handler('exceptionsHandler', E_ALL);
?>