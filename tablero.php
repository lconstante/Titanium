<?php
class tablero
{
    private $titulo;
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function mostrar()
    {
        $claseMenu = new claseMenu();
        $menu = $claseMenu->mostrar();
        unset($claseMenu);
        $fotoUsuario = encriptar("fotoUsuario");
        $miPerfil = encriptar("miPerfil");
        $logActividad = encriptar("logActividad");
        $salirSistema = encriptar("salirSistema");
        $nombreUsuario = encriptar("nombreUsuario");

        $sql = "select nombre from seusuario a inner join gepersona b on (a.idpersona = b.id) where usuario=\"" . desencriptar($_SESSION["usuario"]) . "\"";
        $parametro_sql = [];
        $parametro_sql["s_sql"] = "$sql";
        $mensaje = "";
        $parametro["db_procedimiento"] = "sp_getQuery";
        $parametro["parametro_sql"] = $parametro_sql;
        $rows = readCall($parametro, $mensaje, false);
        unset($db);

        $this->titulo = "";
        if ($mensaje == "[OK]") {
            if (count($rows) > 0) {
                $nombreUsuario = $rows[0]["nombre"];
            }
        }

        $presentar = <<<HEREDOCS
        <!DOCTYPE html>
            <html lang="es">
                <head>    				
                    <meta charset="ISO-8859-1">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <meta name="description" content="">
                    <meta name="author" content="">
                    <title>{$this->getTitulo()}</title>
                                    
                    <!--assets-->
                    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
                    <link rel="stylesheet" href="css/base.min.css">
                    <link rel="stylesheet" href="css/custom.css">
                    <!----->
                    <link rel="stylesheet" type="text/css" href="css/style-tablero.css">   
                    <script type='text/javascript' src='js/funciones.js'></script>
                </head>
                <body onload="load();">
                    <div id="contenedor-principal">
                        <header>                        
                            <div class="barra-menu">
                                <nav class="menu">
                                    {$menu}
                                </nav>
                            </div>
                            <div class="dato-sesion">
                                <div class="foto-usuario">
                                    <img src="defecto.php?accion={$fotoUsuario}">
                                </div>
                                <nav class="menu-sesion">
                                    <ul>
                                        <li>
                                            <a href="#" class="link-inactivo">
                                                {$nombreUsuario}
                                            </a>
                                        </li>
                                        <li><a href="#" 
                                                onclick="
                                                    setCookie(\"accion\",\"{$miPerfil}\");
                                                    var object = document.getElementById(\"contenedor\");
                                                    object.data = \"defecto.php\";
                                                "
                                            >Mi Perfil</a></li>
                                        <li><a href="#" 
                                                onclick="
                                                    setCookie(\"accion\",\"{$logActividad}\");
                                                    var object = document.getElementById(\"contenedor\");
                                                    object.data = \"defecto.php\";
                                                "
                                            >Log de Actividad</a></li>
                                        <div class="dropdown-divider"></div>
                                        <li><a href="#" onclick="
                                                            setCookie('accion','{$salirSistema}');
                                                            var ventana = window.open('defecto.php?','_self','status=no','toolbar=no','menubar=no');
                                                            ventana.focus();
                                                            window.close()
                                                        ">Salir del Sistema</a></li>
                                    </ul>
                                </label>
                            </div>
                        </header>
                        <object class="cuerpo" id="contenedor" width="100%" height="800" data=""></object>
                        <div id="mensaje1"></div>
                        <footer>
                            <div class="footer-data">
                                <h4>Contacto</h4>
                                <span>
                                    <strong>Correo electrónico:</strong>
                                    <a href="mailto:lconstante@itsgg.edu.ec">lconstante@itsgg.edu.ec</a>
                                </span>
                                <span>
                                    <strong>Teléfono:</strong> (593) 994 826 783
                                </span>
                            </div>
                            <div class="footer-social-media">
                                <a href="#" id="facebook">Facebook</a>
                                <a href="#" id="youtube">Youtube</a>
                                <a href="#" id="twitter">Twitter</a>
                            </div>
                        </footer>
                    </div>
                    <script type='text/javascript' src='js/menu.js'></script>
                </body>
            </html>
        HEREDOCS;

        return $presentar;
    }
}
