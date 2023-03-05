<?php
session_start();
include_once('configuracion.php');
include_once('acceso.php');

$presentar = "";
$accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : "";
$accion = $accion == "" ? "acceso" : desencriptar($accion);

switch ($accion) {
    case "acceso":
        $sesionId = iniciarSesion();
        $acceso = new claseAcceso();
        $presentar = $acceso->mostrar($sesionId);
        unset($acceso);
        break;
    case "validar":
        if (!isset($_SESSION["usuario"])) {
            $acceso = new claseAcceso();
            $presentar = $acceso->validar();
            unset($acceso);

            if ($presentar == "[OK]") {
                #$_SESSION["usuario"] = $_REQUEST["usuario"];
            }
        } 
        break;
}
echo $presentar;