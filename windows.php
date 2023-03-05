<?php
define('p_pagSize', 4);
define('p_bloque', 5);
include "db/baseDato.php";
class windows
{
    private string $titulo = "";
    private string $tabla = "";
    private array $campos = array();
    private array $camposMostrar = array();
    private int $numeroPagina = 1;
    private int $totalPagina = 1;
    private array $detalleTabla = array();

    private function getTitulo()
    {
        return $this->titulo;
    }
    public function __construct($titulo, $tabla, $campos, $camposMostrar)
    {
        $this->titulo = $titulo;
        $this->tabla = $tabla;
        $this->campos = $campos;
        $this->camposMostrar = $camposMostrar;
    }
    private function tableHead()
    {
        $properties = [];
        $properties["class"] = "text-primary";
        $thead = new Control("thead", $properties);
        $tr = new Control("tr");
        foreach ($this->camposMostrar as $index => $field) {
            $alias = $this->campos[$field]["alias"];
            $th = new Control("th", array(), ucfirst($alias));
            $tr->addChild($th);
        }
        $properties = [];
        $properties["class"] = "td-actions text-right";
        $th = new Control("th", $properties, "Acción");
        $tr->addChild($th);

        $thead->addChild($tr);
        return $thead;
    }

    private function tableBody()
    {
        $tbody = new Control("tbody");

        foreach ($this->detalleTabla as $fila) {
            $tr = new Control("tr");
            foreach ($this->camposMostrar as $key => $field) {
                $value = $fila[$field];
                $td = new Control("td", array(), $value);
                $tr->addChild($td);
            }

            $tr->addChild($this->windowAction());
            $tbody->addChild($tr);
        }
        $tr = new Control("tr");
        $properties=[];
        $properties["colspan"]=count($this->camposMostrar)+1;
        $properties["align"]="center";
        $td = new Control("td", $properties, $this->getPaginacion()->mostrar());
        $tr->addChild($td);
        $tbody->addChild($tr);

        return $tbody;
    }
    private function tableFoot()
    {
        $tfoot = new Control("tfoot");
        $tfoot->addChild($this->getPaginacion());
        return $tfoot;
    }
    private function getTable()
    {
        $this->cargaDetalleTabla();

        $properties = [];
        $properties["class"] = "table";

        $table = new Control("table", $properties);
        $table->addChild($this->tableHead());
        $table->addChild($this->tableBody());
        //$table->addChild($this->tableFoot());

        return $table;
    }
    private function windowAction()
    {
        $properties = [];
        $properties["class"] = "td-actions text-right";
        $td = new Control("td", $properties);

        $properties = [];
        $properties["class"] = "btn btn-primary btn-link btn-sm";
        $properties["type"] = "button";
        $properties["rel"] = "tooltip";
        $properties["title"] = "";
        $properties["data-original-title"] = "Search Task";

        // $properties["id"] = "searchWA[]";
        $a = new Control("button", $properties);

        $properties = [];
        $properties["class"] = "material-icons";
        $i = new Control("i", $properties, "search");

        $a->addChild($i);
        $a->addChild("<div class=\"ripple-container\">");
        $td->addChild($a);

        $properties = [];
        $properties["class"] = "btn btn-primary btn-link btn-sm";
        $properties["type"] = "button";
        $properties["rel"] = "tooltip";
        $properties["title"] = "";
        $properties["data-original-title"] = "Edit Task";
        //$properties["id"] = "editWA[]";
        $a = new Control("button", $properties);

        $properties = [];
        $properties["class"] = "material-icons";
        $i = new Control("i", $properties, "edit");

        $a->addChild($i);
        $a->addChild("<div class=\"ripple-container\">");
        $td->addChild($a);

        $properties = [];
        $properties["class"] = "btn btn-danger btn-link btn-sm";
        $properties["type"] = "button";
        $properties["rel"] = "tooltip";
        $properties["title"] = "";
        $properties["data-original-title"] = "Remove";

        //$properties["id"] = "deleteWA[]";
        $a = new Control("button", $properties);

        $properties = [];
        $properties["class"] = "material-icons";
        $i = new Control("i", $properties, "close");

        $a->addChild($i);
        $a->addChild("<div class=\"ripple-container\">");
        $td->addChild($a);

        return $td;
    }
    private function buscaFk($campo, &$join)
    {
        $campoMostrar = $this->tabla . "." . $campo;
        if ($this->campos[$campo]["claveForanea"] == "S") {
            $campoMostrar = $this->campos[$campo]["tablaForanea"] . "." . $this->campos[$campo]["campoMostrarTablaForanea"];
            if ($join != "")
                $join .= "\n";

            $join .= " inner join " . $this->campos[$campo]["tablaForanea"] . " on (" . $this->tabla . "." . $campo . " = " . $this->campos[$campo]["tablaForanea"] . "." . $this->campos[$campo]["campoTablaForanea"] . ")";
        } elseif ($this->campos[$campo]["claveForanea"] == "F") {
            $campoMostrar = $this->campos[$campo]["tablaForanea"] . "." . $this->campos[$campo]["campoMostrarTablaForanea"];
            if ($join != "")
                $join .= "\n";

            $join .= " inner join " . $this->campos[$campo]["tablaForanea"] . " on (" . $this->campos[$campo]["tablaForanea"] . ".tabla = '" . $this->campos[$campo]["aliasTablaForanea"] . "' and " . $this->tabla . "." . $campo . " = " . $this->campos[$campo]["tablaForanea"] . "." . $this->campos[$campo]["campoTablaForanea"] . ")";
        }

        $campoMostrar .= " " . $campo;

        return $campoMostrar;
    }

    private function getPaginacion()
    {
        if ($this->totalPagina > 1) {
            $nav = new Control("nav");

            $pi = $this->numeroPagina - 2;
            $pf = $this->numeroPagina + 2;
            if ($pi < 1) {
                $pf += $pi * -1 + 1;
                $pi = 1;
            }

            if ($pf > $this->totalPagina) {
                $pi = $pi - ($pf - $this->totalPagina);
                $pf = $this->totalPagina;
                if ($pi < 1) {
                    $pi = 1;
                }
            }

            $bf = $pf + p_bloque - 2;
            $bi = $pi - p_bloque;

            if ($bi < 1) {
                $bi = 1;
            }
            if ($bf > $this->totalPagina) {
                $bf = $this->totalPagina;
            }

            $pb = $this->numeroPagina - 1;
            $pa = $this->numeroPagina + 1;

            if ($pb < 1)
                $pb = 1;

            if ($pa > $this->totalPagina)
                $pa = $this->totalPagina;

            $disable = "disable-control";
            if ($this->numeroPagina > 1) {
                $disable = "";
            }

            $properties = [];
            $properties["class"] = "btn btn-sm";
            $properties["id"] = "firstPage";
            if ($disable != "")
                $properties[$disable] = "";

            $a = new Control("a", $properties);

            $properties = [];
            $span = new Control("span", $properties, "|<");

            $a->addChild($span);
            $nav->addChild($a);

            $disable = "disable-control";
            if ($pi > 1 && $this->numeroPagina > (p_bloque / 2) && $this->totalPagina > p_bloque) {
                $disable = "";
            }
            $properties = [];
            $properties["class"] = "btn btn-sm";
            $properties["id"] = "backBloq";
            if ($disable != "")
                $properties[$disable] = "";

            $a = new Control("a", $properties);

            $properties = [];
            $span = new Control("span", $properties, "<<");

            $a->addChild($span);
            $nav->addChild($a);

            $disable = "disable-control";
            if ($this->numeroPagina > 1) {
                $disable = "";
            }
            $properties = [];
            $properties["class"] = "btn btn-sm";
            $properties["id"] = "back";
            if ($disable != "")
                $properties[$disable] = "";

            $a = new Control("a", $properties);

            $properties = [];
            $span = new Control("span", $properties, "<");

            $a->addChild($span);
            $nav->addChild($a);

            for ($i = $pi; $i <= $pf && $i <= $this->totalPagina; $i++) {
                $class = "btn btn-sm";
                if ($i == $this->numeroPagina) {
                    $class = "btn btn-sm active";
                }

                $properties = [];
                $properties["class"] = $class;
                $properties["id"] = "p$i";
                if ($disable != "")
                    $properties[$disable] = "";

                $a = new Control("a", $properties);

                $properties = [];
                $span = new Control("span", $properties, $i);

                $a->addChild($span);
                $nav->addChild($a);
            }


            $disable = "disable-control";
            if ($this->numeroPagina < $this->totalPagina) {
                $disable = "";
            }
            $properties = [];
            $properties["class"] = "btn btn-sm";
            $properties["id"] = "next";
            if ($disable != "")
                $properties[$disable] = "";

            $a = new Control("a", $properties);

            $properties = [];
            $span = new Control("span", $properties, ">");

            $a->addChild($span);
            $nav->addChild($a);

            $disable = "disable-control";
            if ($pf < $this->totalPagina && $this->numeroPagina < ($this->totalPagina - (p_bloque / 2)) && $this->totalPagina > p_bloque) {
                $disable = "";
            }

            $properties = [];
            $properties["class"] = "btn btn-sm";
            $properties["id"] = "nextBloq";
            if ($disable != "")
                $properties[$disable] = "";

            $a = new Control("a", $properties);

            $properties = [];
            $span = new Control("span", $properties, ">>");

            $a->addChild($span);
            $nav->addChild($a);

            $disable = "disable-control";
            if ($this->numeroPagina < $this->totalPagina) {
                $disable = "";
            }
            $properties = [];
            $properties["class"] = "btn btn-sm";
            $properties["id"] = "lastPage";
            if ($disable != "")
                $properties[$disable] = "";

            $a = new Control("a", $properties);

            $properties = [];
            $span = new Control("span", $properties, ">|");

            $a->addChild($span);
            $nav->addChild($a);
            return $nav;
        }
    }

    private function cargaDetalleTabla()
    {
        $campos = "";
        $join = "";
        foreach ($this->camposMostrar as $key => $value) {
            if ($campos != "") {
                $campos .= ", ";
            }
            $campos .= $this->buscaFk($value, $join);
        }
        try {
            $parametro_sql = [];
            $parametro_sql["tabla"] = $this->tabla . " " . $join;
            $parametro_sql["p_columns"] = $campos;
            $parametro_sql["p_where"] = "";
            $parametro_sql["p_order"] = "";
            $parametro_sql["p_pagNum"] = $this->numeroPagina;
            $parametro_sql["p_pagSize"] = p_pagSize;
            $db = new baseDato();
            $filas = $db->readCall("sp_getPage", $parametro_sql, $mensaje, false);
            unset($db);
            if ($mensaje == "[OK]") {
                $this->detalleTabla = $filas;
                $this->totalPagina = $filas[0]["TOTALPAGE"];
            } else {
                $this->detalleTabla = array();
                $this->totalPagina = 1;
            }
        } catch (Throwable $e) {
            $mensaje = "[ERROR] " . $e->getMessage();
        }
    }

    private function datos($parametro)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        switch ($metodo) {
            case "select":
                $controles = "<button id='cancelar' onclick='cancelar();'>Cancelar</button>";
                $disabled = "disabled";
                break;
            case "update":
                $controles = "<button id='grabar' onclick='grabar();'>Grabar</button>";
                $controles .= "<button id='cancelar' onclick='cancelar();'>Cancelar</button>";
                $disabled = "";
                break;
            case "delete":
                $controles = "<button id='grabar' onclick='grabar();'>Grabar</button>";
                $controles .= "<button id='cancelar' onclick='cancelar();'>Cancelar</button>";
                $disabled = "disabled";
                break;
        }
        $presentar = "";
        $campos = "";
        $join = "";
        foreach ($this->campos as $key => $value) {
            if ($campos != "") {
                $campos .= ", ";
            }
            $campos .= $this->buscaFk($key, $join);
        }

        $sql = "select " . $campos . " \n from " . $this->tabla . " " . $join;
        $sql .= " \n where " . $this->tabla . "." . "id=" . $id;

        $mensaje = "";
        $db = new baseDato();
        $rows = $db->readCall("sp_getQuery", array("s_sql" => $sql), $mensaje, false);

        if ($mensaje == "[OK]") {
            $presentar .= "<form id='frmdatos'>";
            foreach ($rows[0] as $key => $value) {
                $label = $this->campos[$key]["alias"];

                if ($metodo == "update") {
                    $disabled = $this->campos[$key]["clavePrimaria"] == "S" ? "disabled" : "";
                }
                $control = "";
                if ($this->campos[$key]["claveForanea"] == "S") {
                    $control = "<select name='$key'>";
                    $sql = " select " . $this->campos[$key]["campoTablaForanea"] . ", " . $this->campos[$key]["campoMostrarTablaForanea"];
                    $sql .= " from " . $this->campos[$key]["tablaForanea"];

                    $rowskf = $db->readCall("sp_getQuery", array("s_sql" => $sql), $mensaje, false);
                    foreach ($rowskf as $i => $data) {
                        $control .= "\n";
                        $control .= "<option value=" . $data[$this->campos[$key]["campoTablaForanea"]] . ">" . $data[$this->campos[$key]["campoMostrarTablaForanea"]] . "</option>";
                    }
                    $control .= "</select>";
                    unset($rowskf);
                } elseif ($this->campos[$key]["claveForanea"] == "F") {
                    $control = "<select name='$key'>";
                    $sql = " select  " . $this->campos[$key]["campoTablaForanea"] . ", " . $this->campos[$key]["campoMostrarTablaForanea"];
                    $sql .= " from " . $this->campos[$key]["tablaForanea"];
                    $sql .= " where tabla='" . $this->campos[$key]["aliasTablaForanea"] . "'";

                    $rowskf = $db->readCall("sp_getQuery", array("s_sql" => $sql), $mensaje, false);
                    foreach ($rowskf as $i => $data) {
                        $control .= "\n";
                        $control .= "<option value=" . $data[$this->campos[$key]["campoTablaForanea"]] . ">" . $data[$this->campos[$key]["campoMostrarTablaForanea"]] . "</option>";
                    }
                    $control .= "</select>";
                    unset($rowskf);
                } else {
                    $control = "<input type='text' name='$key' value='$value' $disabled>";
                }
                $presentar .= "
                    <div>
                        <label>$label</label>
                        <br>
                        $control
                    </div>
                    <br>
            ";
            }
            $presentar .= "</form>";
            $presentar .= "<br>";
            $presentar .= $controles;
        }
        return $presentar;
    }

    private function detalle($parametro)
    {
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }

        $this->numeroPagina = $p_numPage;

        $html = new Control("html", array());
        $head = new Control("head", array());

        $head->addChild("<meta charset=\"utf-8\">");
        $head->addChild("<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>");
        $head->addChild("<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">");
        $head->addChild("<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />");

        $head->addChild("<title>" . $this->getTitulo() . "</title>");

        $head->addChild("<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons' />");
        $head->addChild("<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css'>");
        $head->addChild("<link href='assets/css/material-dashboard.css?v=2.1.2' rel='stylesheet' />");
        $head->addChild("<link href='assets/demo/demo.css' rel='stylesheet' />");

        $html->addChild($head);

        $properties = [];
        $properties["class"] = "content";
        $content = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "container-fluid";
        $conteiner = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "row";
        $row = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "col-md-12";
        $colmd12 = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "card";
        $card = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "card-header card-header-primary";
        $cardHeader = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "card-title";
        $cardTitle = new Control("h4", $properties, "Alumnos");

        $properties = [];
        $properties["class"] = "card-category";
        $cardCategory = new Control("p", $properties, "Pantalla de Mantenimiento");

        $cardHeader->addChild($cardTitle);
        $cardHeader->addChild($cardCategory);

        $card->addChild($cardHeader);

        $properties = [];
        $properties["class"] = "card-body";
        $cardBody = new Control("div", $properties);

        $properties = [];
        $properties["class"] = "table-responsive";
        $tableResponsive = new Control("div", $properties);

        $tableResponsive->addChild($this->getTable());

        $cardBody->addChild($tableResponsive);

        $card->addChild($cardBody);

        $colmd12->addChild($card);
        $row->addChild($colmd12);
        $conteiner->addChild($row);
        $content->addChild($conteiner);

        $properties=[];
        $properties["class"]="wrapper";
        $wrapper = new Control("div", $properties);
        $wrapper->addChild($content);

        $properties=[];
        $properties["class"]="";
        $body = new Control("body", $properties);
        $body->addChild($wrapper);


        $body->addChild("<script src='assets/js/core/jquery.min.js' type='text/javascript'></script>");
        $body->addChild("<script src='assets/js/core/popper.min.js' type='text/javascript'></script>");
        $body->addChild("<script src='assets/js/core/bootstrap-material-design.min.js' type='text/javascript'></script>");
        $body->addChild("<script src='assets/js/plugins/perfect-scrollbar.jquery.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/moment.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/sweetalert2.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/jquery.validate.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/jquery.bootstrap-wizard.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/bootstrap-selectpicker.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/bootstrap-datetimepicker.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/jquery.dataTables.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/bootstrap-tagsinput.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/jasny-bootstrap.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/fullcalendar.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/jquery-jvectormap.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/nouislider.min.js'></script>");
        //$body->addChild("<script src='https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/arrive.min.js'></script>");
        // $body->addChild("<script src='https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE'></script>");
        $body->addChild("<script src='../assets/js/plugins/chartist.min.js'></script>");
        $body->addChild("<script src='../assets/js/plugins/bootstrap-notify.js'></script>");
        $body->addChild("<script src='../assets/js/material-dashboard.js?v=2.1.2' type='text/javascript'></script>");

        $body->addChild("<script src='clase14.js'></script>");

        $html->addChild($body);

        $presentar = $html->mostrar();

        return $presentar;
    }

    public function mostrar($parametro)
    {
        $accion = "";
        foreach ($parametro as $key => $value) {
            $$key = $value;
        }
        $presentar = "";
        switch ($accion) {
            case "detalle":
                $presentar = $this->detalle($parametro);
                break;
            case "datos":
                $presentar = $this->datos($parametro);
                break;
            case "update":

                break;
        }

        return $presentar;
    }
}
