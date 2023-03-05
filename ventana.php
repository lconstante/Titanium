<?php
class claseVentana
{
    private function getDetalle($parametro)
    {
        $accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : $_COOKIE["accion"];
        $accion = desencriptar($accion);

        if (!(isset($_REQUEST["parametro"]) || isset($_COOKIE["parametro"]))) {
            $presentar = "[ERROR] No se se recibieron los parámetros en este programa.";
            return $presentar;
        } else {
            $parametro_sesion = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
            if ($accion == "opcion")
                $datos = explode("&", desencriptar($parametro_sesion)); //Menu ejecuta opción a tarves del OBJECT HTML 
            else
                $datos = explode("&", secured_decrypt($parametro_sesion));

            foreach ($datos as $key => $dato) {
                $variable = explode("=", $dato);
                $key = $variable[0];
                $$key = $variable[1];
            }
        }

        $mensaje = "";

        $presentar = $this->getDetalleMostrar($parametro, $mensaje, false);
        if ($mensaje != "[OK]")
            return $mensaje;

        return $presentar;
    }

    private function getPantalla($parametro)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }

        if (!(isset($_REQUEST["parametro"]) || isset($_COOKIE["parametro"]))) {
            $presentar = "[ERROR] No se se recibieron los parámetros en este programa.";
            return $presentar;
        } else {
            $parametro_sesion = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
            $datos = explode("&", desencriptar($parametro_sesion));
            foreach ($datos as $key => $dato) {
                $variable = explode("=", $dato);
                $key = $variable[0];
                $$key = $variable[1];
            }
        }

        $mensaje = "";
        $detalle = $this->getDetalleMostrar($parametro, $mensaje, false);
        $busqueda = $this->getCamposFiltrar($parametro);
        if ($mensaje != "[OK]") {
            return $mensaje;
        }

        $dato = "objeto=detalle&clase=" . $parametro["clase_php"] . "&opcion=$opcion&metodo=nuevo";
        $dato = secured_encrypt($dato);
        $accion = encriptar("datos");

        $cabecera = "
                            <ul class='nav nav-tabs'>
                                <li class='nav-item'>
                                    <a class='nav-link active' id='pantalla-tab' data-toggle='tab' href='#pantalla' role='tab' aria-controls='pantalla' aria-selected='true'>
                                        <i class='fas fa-tag'></i>
                                        " . $this->titulo . "
                                    </a>
                                </li class='nav-item'>
                                <li>
                                    <a class='nav-link' id='crear-tab' href='#' onclick=\"cargarObjeto('datos','$accion','$dato');\">
                                        <i class='fas fa-plus'></i>
                                        Crear
                                    </a>
                                </li>
                            </ul>
        ";

        $presentar = "
            <!DOCTYPE html>
            <html>
                <head>
                <meta charset='utf-8'/>
                <meta name='mobile-web-app-capable' content='yes'/>
                <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no' />
                <meta name='description' content='' />
                <meta name='author' content='' />
                <!--assets-->
                <link rel='stylesheet' href='css/fontawesome/css/all.min.css'>
                <link rel='stylesheet' href='css/base.min.css'>
                <link rel='stylesheet' href='css/custom.css'>
                <!----->
                <link rel='stylesheet' type='text/css' href='style-opcion.css'>
                <script type='text/javascript' src='function.js'></script>
                </head>
                <body onload='load();'>
                    <div id='contenedor-principal'>
                        <div id='pantalla-listado'>                           
                            <div id='cabecera'>
                            $cabecera
                            </div>
                            <div id='busqueda'>
                            $busqueda
                            </div>
                            <div id='detalle'>
                                $detalle
                            </div>
                        </div>
                    </div>
                    <div id='datos'>
                    </div>
                    
                </body>
            </html>
        ";



        return $presentar;
    }
    private function getNuevo($parametro, &$mensaje)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        if (!(isset($_REQUEST["parametro"]) || isset($_COOKIE["parametro"]))) {
            $presentar = "[ERROR] No se se recibieron los parámetros en este programa.";
            return $presentar;
        } else {
            $parametro_sesion = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
            $datos = explode("&", secured_decrypt($parametro_sesion));
            foreach ($datos as $key => $dato) {
                $variable = explode("=", $dato);
                $key = $variable[0];
                $$key = $variable[1];
            }
        }

        $cabecera = $this->titulo . " - " . ucfirst($metodo);
        $pk = secured_encrypt($camposClavePrimaria);
        $cabecera = "";
        $controles = "<input type='hidden' name='camposClavePrimaria' value=$pk>";

        $cabecera = $this->titulo . " - " . ucfirst($metodo);
        $campos = json_decode($campos);
        foreach ($campos as $key => $campo) {
            if ($campo->editable == "N")
                $controlInactivo = "disabled";
            else
                $controlInactivo = "";

            if ($campo->clavePrimaria != "S") {
                $controles .= "
                    <div class='form-group'>
                        <label class='label' >" . ucfirst($campo->alias) . "</label>
                        <input class='form-control' type='text' placeholder='$campo->alias' value='' $controlInactivo>
                    </div>
                ";
            }
        }

        $accion = encriptar("metodo");
        $controles .= "
                    <button class='btn btn-success' 
                        onclick='
                            var forma = document.getElementById(\"forma\");
                            guardar(forma,\"$accion\");
                        '>Guardar
                    </button>
                    <a class='btn btn-primary' href='#' onclick='cerrarControl(\"datos\");'>Volver al listado</a>
        ";


        $presentar = "
                    <div id='pantalla-datos'>
                        <div id='cabecera-datos'>
                            <h1>$cabecera</h1>
                        </div>
                        <div class='contenedor-forma'>
                            <form class='forma-principal' id='forma'>
                                $controles
                            </form>
                        </div>
                    </div>
        ";
        return $presentar;
    }
    private function getDatos($parametro, $mensaje)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        if (!(isset($_REQUEST["parametro"]) || isset($_COOKIE["parametro"]))) {
            $presentar = "[ERROR] No se se recibieron los parámetros en este programa.";
            return $presentar;
        } else {
            $parametro_sesion = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
            $datos = explode("&", secured_decrypt($parametro_sesion));
            foreach ($datos as $key => $dato) {
                $variable = explode("=", $dato);
                $key = $variable[0];
                $$key = $variable[1];
            }
        }

        if ($metodo == "consultar" || $metodo == "eliminar")
            $inactivo = "disabled";
        else
            $inactivo = "";

        $cabecera = "";
        $controles = "";
        if (!getBoolean($metodo)) {
            $cabecera = $this->titulo . " - " . ucfirst($metodo);

            $filas = $this->getDatoRegistro($parametro, $mensaje);

            $campos = $parametro["campos"];
            if ($mensaje == "[OK]") {
                if (count($filas) > 0) {
                    $controles = "";

                    foreach ($filas as $i => $fila) {
                        foreach ($fila as $nombreCampo => $datoCampo) {
                            $campo = $this->getCampo($campos, $nombreCampo);
                            if ($campo->editable == "N")
                                $controlInactivo = "disabled";
                            else
                                $controlInactivo = "";

                            $alias = $campo->alias;

                            $desactivar = $inactivo != "" ? $inactivo : $controlInactivo;

                            $controles .= "
                                <div class='form-group'>
                                    <label class='label' >" . $alias . "</label>
                                    <input class='form-control' type='text' placeholder='$nombreCampo' value='$datoCampo' $desactivar />
                                </div>
                            ";
                        }
                    }

                    $accion = encriptar("metodo");
                    if ($metodo != "consultar") {
                        $controles .= "
                                <button class='btn btn-success' 
                                    onclick='
                                        var forma = document.getElementById(\"forma\");
                                        guardar(forma,\"$accion\");
                                    '>Guardar
                                </button>
                        ";
                    }
                    $controles .= "
                                <a class='btn btn-primary' href='#' onclick='cerrarControl(\"datos\");'>Volver al listado</a>
                    ";
                }
            }
        }

        $presentar = "
                    <div id='pantalla-datos'>
                        <div id='cabecera-datos'>
                            <h1>$cabecera</h1>
                        </div>
                        <div class='contenedor-forma'>
                            <form class='forma-principal' id='forma'>
                                $controles
                            </form>
                        </div>
                    </div>
        ";
        return $presentar;
    }
    public function mostrar()
    {
        $accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : $_COOKIE["accion"];
        $accion = desencriptar($accion);
        $opcion = 0;

        if (!(isset($_REQUEST["parametro"]) || isset($_COOKIE["parametro"]))) {
            $presentar = "[ERROR] No se se recibieron los parámetros en este programa.";
            return $presentar;
        } else {
            $parametro_sesion = isset($_REQUEST["parametro"]) ? $_REQUEST["parametro"] : $_COOKIE["parametro"];
            if ($accion == "opcion")
                $datos = explode("&", desencriptar($parametro_sesion)); //Menu ejecuta opción a tarves del OBJECT HTML 
            else
                $datos = explode("&", secured_decrypt($parametro_sesion));

            foreach ($datos as $key => $dato) {
                $variable = explode("=", $dato);
                $key = $variable[0];
                $$key = $variable[1];
                unset($variable);
            }
            unset($key);
            unset($dato);
            unset($datos);
        }
        try {
            $mensaje = "";
            $parametro["opcion"] = $opcion;
            $parametro["clase_php"] = get_class();
            $parametro["p_pagNum"] = isset($p_pagNum) ? $p_pagNum : 1;

            $parametro_forma = $this->getParametroForma($parametro, $mensaje, false);

            $presentar = "";
            if ($mensaje == "[OK]") {
                $this->titulo = $parametro_forma["titulo"];
                $presentar = "";
                switch ($accion) {
                    case "opcion":
                        $presentar = $this->getPantalla($parametro_forma);
                        if ($mensaje != "[OK]")
                            $presentar = $mensaje;
                        break;
                    case "datos":
                        if ($metodo == "nuevo") {
                            $presentar = $this->getNuevo($parametro_forma, $mensaje);
                            if ($mensaje != "[OK]")
                                $presentar = $mensaje;
                        } else {
                            $presentar = $this->getDatos($parametro_forma, $mensaje);
                            if ($mensaje != "[OK]")
                                $presentar = $mensaje;
                        }
                        break;
                    case "buscaDato":
                        $p_where = $this->getCondicionDetalle($parametro_forma, $mensaje);
                        $parametro_forma["parametro_sql"]["p_where"] = $p_where;
                    case "pagina": //en case buscaDato no hay break para que continue en esta opcion
                        $presentar = $this->getDetalleMostrar($parametro_forma, $mensaje, false);
                        if ($mensaje != "[OK]")
                            $presentar = $mensaje;
                        break;
                }
            }
        } catch (Throwable $e) {
            $mensaje = "[ERROR]" . $e->getMessage();
        }
        if ($mensaje != "[OK]")
            $presentar = $mensaje;
        return $presentar;
    }

    function getDetalleMostrar($parametro, &$mensaje, $json_return)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }

        $filas = readCall($parametro, $mensaje, $json_return);
        if ($mensaje != "[OK]")
            return $mensaje;

        if (count($filas) == 0)
            return "[ERROR] No hay datos ...";

        $campos = json_decode($campos);
        $camposMostrar = $this->getOrden(($camposMostrar));
        $presentar = "
		<div class='table-responsive'>
			<form id='forma-detalle' class='forma-detalle'>
			<table class='table table-bordered'>
				<thead>
	";
        $presentar .= "	
					<tr>
	";
        foreach ($camposMostrar as $i => $campoMostrar) {
            foreach ($campos as $j => $campo) {
                if ($campoMostrar->campo == $campo->campo) {
                    $alias = $campo->alias;
                    break;
                }
            }

            $presentar .= "	
					<th>
						$alias
					</th>
	";
        }
        $presentar .= "
						<th class='td-control'>
							Acción
						</th>
					</tr>
				</thead>
				<tbody>
	";

        $contador = 0;
        $totalPage = $filas[0]['TOTALPAGE'];
        $accion = encriptar("datos");

        foreach ($filas as $key => $fila) {
            $presentar .= "
					<tr class='tr-$contador'>
				
		";
            foreach ($camposMostrar as $i => $campos) {
                $presentar .= "
						<td>" . $fila[$campos->campo] . "
							<input type='hidden' name='" . $campos->campo . "[]' value='" . $fila[$campos->campo] . "'>
						</td>
			";
            }
            $dato = "objeto=detalle&clase=" . $clase_php . "&opcion=$opcion&metodo=consultar&indice=$key";
            $dato = secured_encrypt($dato);
            $consultar = "<a title ='Consultar' href='#' class='btn btn-warning' onclick=\"cargarDetalle('datos','$accion','$dato','forma-detalle');\"><span><i class='fa fa-search'></i></span></a>";

            $dato = "objeto=detalle&clase=" . $clase_php . "&opcion=$opcion&metodo=modificar&indice=$key";
            $dato = secured_encrypt($dato);
            $modificar = "<a title ='Modificar' href='#' class='btn btn-warning' onclick=\"cargarDetalle('datos','$accion','$dato','forma-detalle');\"><span><i class='fa fa-edit'></i></span></a>";

            $dato = "objeto=detalle&clase=" . $clase_php . "&opcion=$opcion&metodo=eliminar&indice=$key";
            $dato = secured_encrypt($dato);
            $eliminar = "<a title ='Eliminar' href='#' class='btn btn-danger' onclick=\"cargarDetalle('datos','$accion','$dato','forma-detalle');\"><span><i class='fa fa-trash'></i></span></a>";

            $presentar .= "
						<td class='td-control'>
								$consultar 
								$modificar 
								$eliminar
						</td>
					</tr>
		";

            $contador = $contador == 0 ? 1 : 0;
        }

        if (p_pagSize > count($filas)) {
            for ($i = count($filas); $i < p_pagSize; $i++) {
                $presentar .= "
					<tr class='tr-transparente'>
			";
                foreach ($camposMostrar as $key => $campos) {
                    $presentar .= "
						<td class='td-transparente'><div class='div-transparente'</div></td>
					";
                }
                $presentar .= "
						<td class='td-control td-transparente'><div class='div-transparente'</div></td>
					</tr>
			";
            }
        }
        $presentar .= "
				</tbody>
			</table>
		</form>
		</div>
	";
        if ($totalPage > 1) {


            $parametro_paginacion = array();
            $parametro_paginacion["p_pagNum"] = $p_pagNum;
            $parametro_paginacion["totalPage"] = $totalPage;
            $parametro_paginacion["opcion"] = $opcion;
            $parametro_paginacion["clase_php"] = $clase_php;

            $paginacion = getPaginacion($parametro_paginacion);

            $presentar .= "
			<div id='paginacion'>
				$paginacion
			</div>
		";
        }

        return $presentar;
    }

    function getParametroForma($parametro, &$mensaje, $json_return)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }

        $sql = "select a.titulo, a.tabla, a.campos, a.camposClavePrimaria, a.camposMostrar, 
					a.camposFiltrar, a.camposOrdenar
                from gcforma a 
                    inner join seopcion b on (a.id = b.idforma) 
                where b.id=$opcion and a.estado=1 and b.estado=1";
        $parametro = array();
        $parametro["db_procedimiento"] = "sp_getQuery";
        $parametro["parametro_sql"] = array("s_sql" => $sql);
        $mensaje = "";
        try {
            $parametro_forma = array();

            $filas = readCall($parametro, $mensaje, $json_return);
            if ($mensaje != "[OK]") {
                $filas = array();
                $filas[0] = $mensaje;
            } else {
                if (count($filas) > 0) {
                    $tabla = $filas[0]["tabla"];
                    $parametro_forma["tabla"] = $tabla;
                    $parametro_forma["titulo"] = $filas[0]["titulo"];
                    $parametro_forma["campos"] = $filas[0]["campos"];
                    $parametro_forma["camposClavePrimaria"] = $filas[0]["camposClavePrimaria"];
                    $parametro_forma["camposMostrar"] = $filas[0]["camposMostrar"];
                    $parametro_forma["camposFiltrar"] = $filas[0]["camposFiltrar"];
                    $parametro_forma["camposOrdenar"] = $filas[0]["camposOrdenar"];

                    $parametro_forma["opcion"] = $opcion;
                    $parametro_forma["p_pagNum"] = $p_pagNum;
                    $parametro_forma["clase_php"] = $clase_php;
                    $parametro_forma["db_procedimiento"] = "sp_getPage";

                    $tablasJoin = "";
                    $camposQuery = $this->getCamposMostrar($filas[0]["campos"], $filas[0]["camposMostrar"], $filas[0]["tabla"], $tablasJoin);
                    $parametro_sql = [];
                    if ($tablasJoin == "")
                        $parametro_sql["tabla"] = $filas[0]["tabla"];
                    else
                        $parametro_sql["tabla"] = $tablasJoin;

                    $parametro_sql["p_columns"] = $camposQuery;
                    $parametro_sql["p_where"] = "";
                    $camposOrdenar = $this->getCamposOrdenar($filas[0]["campos"], $filas[0]["camposOrdenar"], $filas[0]["tabla"]);
                    $parametro_sql["p_order"] = $camposOrdenar;
                    $parametro_sql["p_pagNum"] = $p_pagNum;
                    $parametro_sql["p_pagSize"] = p_pagSize;
                    $parametro_forma["parametro_sql"] = $parametro_sql;
                } else {
                    $mensaje = "[ERROR] No existe configuración para esta forma, pongase en contacto con el administrador del sistema.";
                    return $parametro_forma;
                }
            }
        } catch (Throwable $e) {
            $mensaje = "[ERROR] " . $e->getMessage();
        }

        return $parametro_forma;
    }
    function getCondicionDetalle($parametro, &$mensaje)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        $presentar = "";
        $campo = isset($_REQUEST["campo"]) ? $_REQUEST["campo"] : "";
        if ($campo == "") {
            $mensaje = "[ERROR] No se ha seleccionado un criterio de busqueda...";
        } else {
            $dato = isset($_REQUEST["dato"]) ? $_REQUEST["dato"] : "";
            if ($dato != "") {
                $campoForaneo = isset($_REQUEST["campoForaneo"]) ? $_REQUEST["campoForaneo"] : "";
                if ($campoForaneo[$campo] != "") {
                    $tablaForanea = isset($_REQUEST["tablaForanea"]) ? $_REQUEST["tablaForanea"] : "";
                    $tablaForaneaAlias = isset($_REQUEST["tablaForaneaAlias"]) ? $_REQUEST["tablaForaneaAlias"] : "";
                    $campoForaneoPK = isset($_REQUEST["campoForaneoPK"]) ? $_REQUEST["campoForaneoPK"] : "";
                    $tablasJoin = $tabla . "  inner join " . $tablaForanea[$campo] . "  on($tabla.$campo = " . $tablaForanea[$campo] . "." . $campoForaneoPK[$campo] . ") ";
                    $presentar = $tablaForaneaAlias[$campo] == "" ? $tablaForanea[$campo] : $tablaForaneaAlias[$campo];
                    $presentar .= "." . $campoForaneo[$campo] . " like '" . $dato . "'";
                } else {
                    $presentar = "$tabla.$campo like '$dato'";
                }
            }
        }
        return $presentar;
    }

    function getOrden($campos)
    {
        $campos = json_decode($campos);
        $orden = [];
        foreach ($campos as $key => $value) {
            $orden[$key]["orden"] = $value->orden;
        }
        asort($orden);
        $array = [];
        $i = 0;
        foreach ($orden as $key => $value) {
            $array[$i] = $campos[$key];
            $array[$i]->indice_original = $key;
            $i++;
        }
        return $array;
    }
    function getCampos($campos, $tabla, &$tablaJoin)
    {
        $campos = json_decode($campos);
        $presentar = "";
        $tablaJoin = $tabla;
        foreach ($campos as $i => $campo) {
            if ($presentar != "")
                $presentar .= ", ";
            $campoForaneo = "";
            if ($campo->claveForanea == "I" || $campo->claveForanea == "L") {
                $tablaForanea = $campo->tablaForaneaAlias == "" ? $campo->tablaForanea : $campo->tablaForaneaAlias;
                $campoForaneo = $tablaForanea . "." . $campo->campoForaneo;
                $join = $campo->tablaForanea . " " . $campo->tablaForaneaAlias . " on ( " . $tabla . "." . $campo->campo . " = " . $tablaForanea . "." . $campo->campoForaneoPK . " )";
                switch ($campo->claveForanea) {
                    case "I":
                        $tablaJoin .= " inner join " . $join;
                        break;
                    case "L":
                        $tablaJoin .= " left  join " . $join;
                        break;
                }
            } elseif ($campo->claveForanea == "F") {
                $tablaForanea = $campo->tablaForanea;
                $campoForaneo = "f_getDescripcion('" . $tablaForanea . "'," . $tabla . "." . $campo->campo . ") ";
            }

            if ($campoForaneo != "")
                $presentar .= $campoForaneo . " " . $campo->campo;
            else
                $presentar .= $tabla . "." . $campo->campo;
        }
        return $presentar;
    }
    function getCamposMostrar($campos, $camposMostrar, $tabla, &$tablaJoin)
    {
        $orden = $this->getOrden($camposMostrar);
        $campos = json_decode($campos);
        $presentar = "";
        $tablaJoin = $tabla;
        foreach ($orden as $i => $value) {
            if ($presentar != "")
                $presentar .= ", ";
            $campoForaneo = "";
            foreach ($campos as $j => $campo) {
                if ($value->campo == $campo->campo && ($campo->claveForanea == "I" || $campo->claveForanea == "L")) {
                    $tablaForanea = $campo->tablaForaneaAlias == "" ? $campo->tablaForanea : $campo->tablaForaneaAlias;
                    $campoForaneo = $tablaForanea . "." . $campo->campoForaneo;
                    $join = $campo->tablaForanea . " " . $campo->tablaForaneaAlias . " on ( " . $tabla . "." . $campo->campo . " = " . $tablaForanea . "." . $campo->campoForaneoPK . " )";
                    switch ($campo->claveForanea) {
                        case "I":
                            $tablaJoin .= " inner join " . $join;
                            break;
                        case "L":
                            $tablaJoin .= " left  join " . $join;
                            break;
                    }
                    break;
                } elseif ($value->campo == $campo->campo && $campo->claveForanea == "F") {
                    $tablaForanea = $campo->tablaForanea;
                    $campoForaneo = "f_getDescripcion('" . $tablaForanea . "'," . $tabla . "." . $campo->campo . ") ";
                    break;
                }
            }
            if ($campoForaneo != "")
                $presentar .= $campoForaneo . " " . $campo->campo;
            else
                $presentar .= $tabla . "." . $orden[$i]->campo;
        }
        return $presentar;
    }
    function getCamposOrdenar($campos, $camposOrdenar, $tabla)
    {
        $orden = $this->getOrden($camposOrdenar);
        $campos = json_decode($campos);
        $presentar = "";
        foreach ($orden as $i => $value) {
            if ($presentar != "")
                $presentar .= ", ";
            $campoForaneo = "";
            foreach ($campos as $j => $campo) {
                if ($value->campo == $campo->campo && ($campo->claveForanea == "I" || $campo->claveForanea == "L")) {
                    $tablaForanea = $campo->tablaForaneaAlias == "" ? $campo->tablaForanea : $campo->tablaForaneaAlias;
                    $campoForaneo = $tablaForanea . "." . $campo->campoForaneo;
                    break;
                }
            }
            if ($campoForaneo != "")
                $presentar .= $campoForaneo;
            else
                $presentar .= $tabla . "." . $orden[$i]->campo;
        }
        return $presentar;
    }
    function getCampo($campos, $nombreCampo)
    {
        $presentar = false;
        $campos = json_decode($campos);

        foreach ($campos as $key => $campo) {
            if ($campo->campo == $nombreCampo)
                $presentar = $campo;
        }

        return $presentar;
    }
    function getCamposFiltrar($parametro)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        $orden = $this->getOrden($camposFiltrar);
        $campos = json_decode($campos);
        $formulario = "";
        $accion = encriptar("buscaCampo");
        $dato = "opcion=$opcion&clase=$clase_php";
        $dato = secured_encrypt($dato);
        $presentar = "
		<form id='forma-busqueda'>
				<div class='content-select'>
					<select name='campo' 
						onclick='
							cargarDetalle(\"detalle-busqueda\",\"$accion\",$dato,\"forma-busqueda\");
						'
					>
	";
        foreach ($orden as $i => $value) {

            $campoForaneo = "";
            $tablaForanea = "";
            $campoForaneoFK = "";
            $tablaForaneaAlias = "";
            foreach ($campos as $j => $campo) {
                if ($value->campo == $campo->campo && ($campo->claveForanea == "I" || $campo->claveForanea == "L")) {
                    $tablaForanea = $campo->tablaForanea;
                    $tablaForaneaAlias = $campo->tablaForaneaAlias;
                    $campoForaneo = $campo->campoForaneo;
                    $campoForaneoFK = $campo->campoForaneoPK;
                    $alias = $campo->alias;
                    break;
                } elseif ($value->campo == $campo->campo) {
                    $alias = $campo->alias;
                }
            }
            $formulario .= "
						<input type='hidden' name='tablaForanea[" . $value->campo . "]' value='" . $tablaForanea . "'>";
            $formulario .= "
						<input type='hidden' name='tablaForaneaAlias[" . $value->campo . "]' value='" . $tablaForaneaAlias . "'>";
            $formulario .= "
						<input type='hidden' name='campoForaneo[" . $value->campo . "]' value='" . $campoForaneo . "'>";
            $formulario .= "
						<input type='hidden' name='campoForaneoPK[" . $value->campo . "]' value='" . $campoForaneoFK . "'>";


            $presentar .= "
							<option value='" . $value->campo . "'>" . $alias . "</option>
					";
        }
        $accion = encriptar("buscaDato");
        $dato = "opcion=$opcion&clase=$clase_php";
        $dato = secured_encrypt($dato);
        $presentar .= "
				</select>
				<i></i>
				</div>
				<div id='detalle-busqueda'>
					<input type='text' name='dato' placeholder='Escriba el dato correspondiente al campo seleccionado...'>
				</div>
				<div class='boton-busqueda'>
					<a ref='#' class='btn btn-warning' 
						onclick='
							cargarDetalle(\"detalle\",\"$accion\",\"$dato\",\"forma-busqueda\");
						'
					>
						<span>
						<i class='fas fa-search'></i>
						Buscar
						</span>
					</a>
				</div>

			$formulario
		</form>";
        return $presentar;
    }
    function getCondicionForma($parametro, $indice)
    {
        $parametro_cabecera = $_REQUEST["parametro"];
        $datos = explode("&", secured_decrypt($parametro_cabecera));
        $indice=-1;
        foreach ($datos as $key => $dato) {
            $variable = explode("=", $dato);
            $key = $variable[0];
            $$key = $variable[1];
            unset($variable);
        }

        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        

        $camposClavePrimaria = json_decode($parametro["camposClavePrimaria"]);
        $presentar = "";
        foreach ($camposClavePrimaria as $i => $fila) {
            foreach ($fila as $key => $valor) {
                if ($key == "campo") {
                    if ($presentar != "")
                        $presentar .= " and ";
                    $arreglo = $valor . "->" . $i;
                    $arreglo = $valor . "[" . $i . "]";
                    $dato = $_REQUEST["$valor"];

                    $dato = $dato[$indice];
                    $presentar .= $tabla . "." . $valor . "=" . $dato;
                }
            }
        }
        return $presentar;
    }
    function getDatoRegistro($parametro, &$mensaje)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }

        $p_where = $this->getCondicionForma($parametro, $mensaje);

        $parametro["db_procedimiento"] = "sp_getCall";
        unset($parametro["parametro_sql"]);

        $tablaJoin = "";
        $campos = $this->getCampos($campos, $tabla, $tablaJoin);

        if ($tablaJoin == "")
            $parametro["parametro_sql"]["p_tabla"] = $filas[0]["tabla"];
        else
            $parametro["parametro_sql"]["p_tabla"] = $tablaJoin;

        $parametro["parametro_sql"]["p_columns"] = $campos;
        $parametro["parametro_sql"]["p_where"] = $p_where;
        $parametro["parametro_sql"]["p_order"] = ""; //Revisar orden

        $filas =  readCall($parametro, $mensaje, false);
        if (count($filas) > 0 && $mensaje == "[OK]") {
            return $filas;
        } else {
            if ($mensaje != "[OK]")
                $mensaje = "[ERROR] No hay datos";
            return array();
        }
    }
}
