<?php
include_once("configuracion.php");

include_once('menu.php');
include_once('tablero.php');
include_once('usuario.php');

$presentar = "";

if (!cargarSesion()) {
    header("location: index.php");
    return;
}
if (isset($_COOKIE["accion"]) || isset($_REQUEST["accion"])) {
    $accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : $_COOKIE["accion"];
    $accion = desencriptar($accion);
    switch ($accion) {
        case "home":
            
        case "tablero":
            $tablero = new tablero();
            $tablero->setTitulo("Sistema de Programación de Aplicaciones WEB 4D");
            $presentar = $tablero->mostrar();
            break;
        case "menu":
            $menu = new claseMenu();
            $presentar = $menu->mostrar();
            break;
        case "opcion":
            try {
                $parameter = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
                $datos = explode("&", desencriptar($parameter));
                $opcion = getDato($datos, "opcion");
                if (!getBoolean($opcion)) {
                    $parametro_sql = array();
                    $parametro_sql["s_sql"] = "select clase, nombre from seopcion where id=" . $opcion;
                    $parametro["db_procedimiento"] = "sp_getQuery";
                    $parametro["parametro_sql"] = $parametro_sql;
                    $rows = readCall($parametro, $mensaje, false);
                    if (count($rows) > 0) {
                        $clase = $rows[0]["clase"];

                        if (class_exists($clase, false)) {
                            $opcion = new $clase();
                            if (method_exists($opcion, "mostrar")) {
                                $presentar = $opcion->mostrar();
                            } else {
                                $presenta = "[ERROR] Método invocado en la clase no existe.";
                            }
                        } else {
                            $presentar = "[ERROR] La clase no existe.";
                        }
                    } else {
                        $presentar = "La <strong>opción</strong> no tiene asociado una pantalla.";
                    }
                } else {
                    $presentar = "La <strong>opción</strong> no tiene asociado una pantalla.";
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $presentar = "[ERROR]";
            } catch (Error $e) {
                error_log($e->getMessage());
                $presentar = "[ERROR] Al cargar la opción ";
            }

            $cadena_de_texto = "$presentar";
            $cadena_buscada   = "[ERROR]";
            $posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);

            //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
            if ($posicion_coincidencia === false) {
                // echo "NO se ha encontrado la palabra deseada!!!!";
            } else {
                $presentar = <<<heredocs
                <!DOCTYPE html>
                <html lang='es'>
                    <head>    				
                        <meta charset='ISO-8859-1'>
                        <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                        <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no' />
                        <meta name='description' content='' />
                        <meta name='author' content='' />
                        <title>[ERROR]</title>
                        <!--assets-->
                        <link rel='stylesheet' href='css/fontawesome/css/all.min.css'>
                        <link rel='stylesheet' href='css/base.min.css'>
                        <link rel='stylesheet' href='css/custom.css'>
                        <!----->
                        <link rel='stylesheet' type='text/css' href='style-opcion.css'>
                        <script type='text/javascript' src='js/funciones.js'></script>
                    </head>
                    <body>
                        <script>
                            mostrarMensaje('$presentar');
                        </script>
                    </body>
                </html>
                heredocs;
                //setcookie("accion", encriptar("tablero"));
            }
            break;
        case "fotoUsuario":
            $usuario = new claseUsuario();
            $presentar = $usuario->getFotoUsuario(desencriptar($_SESSION["usuario"]));
            break;
        case "salirSistema":
            session_destroy();
            unset($_COOKIE);
            header("Location: http://localhost/web/index.php");
            break;
        case "buscaDato":
            //sleep(3);
        case "datos":
        case "pagina":
            $parameter = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
            $datos = explode("&", secured_decrypt($parameter));
            $clase = getDato($datos, "clase");

            if (!getBoolean($clase)) {
                $opcion = new $clase();
                $presentar = $opcion->mostrar();
            } else {
                $presentar = "[ERROR] Al cargar datos de patalla";
            }
            break;
    }
}

echo $presentar;
/*
if (isset($_COOKIE["accion"]))
    unset($_COOKIE["accion"]);

if (isset($_COOKIE["parametro"]))
    unset($_COOKIE["parametro"]);
*/