<?php
date_default_timezone_set("America/GuayAquil");
$rutaLogs =  __DIR__ . "\\logs";
if (!file_exists($rutaLogs)) {
    mkdir($rutaLogs);
}
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", $rutaLogs . "\\" . date("Y-m-d") . ".log");

define('primeraClave', 'Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');
define('segundaClave', 'EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');
define('tamanioPagina', 8);
define('tamanioBloque', 5);
define('urlInicio', 'defecto.php');
define('urlAcceso', 'index.php');
define('miDominio', 'titanium.local');

include_once("nucleo.php");
include_once("db/mariaDB.php");
include_once("db/baseDato.php");
include_once("funcion.php");
include_once("imagen.php");
