<?php
class claseMenu
{
    public function mostrar()
    {
        $mensaje="";
       
        $db = new baseDato();
        $query="call sp_getOpcion('".desencriptar($_SESSION['usuario'])."',0);";
        $json = $db->read($query,array(),$mensaje);
        $rows = json_decode($json);
        $presentar ="<ul>";
        
        $accion=encriptar("home");
        $inicio="parametro=''";
        $inicio=encriptar($inicio);
        $presentar .="<li><a class='menu-item' href='#'><i class='fas fa-home'></i>  Inicio</a>";
        
        for ($i=0; $i<count($rows);$i++)
        {
            $query="call sp_getOpcion('".desencriptar($_SESSION['usuario'])."',".$rows[$i]->id.");";
            $json = $db->read($query,array(),$mensaje);
            $row = json_decode($json);
            if ($mensaje !="[OK]")
            {
                return $mensaje;
            }
            if (count($row)>0)
            {
                $cntsm=1;
                #$presentar .="<li id=".$rows[$i]->id."><p class='menu-item'>".$rows[$i]->nombre."</p>";
                $presentar .="<li id=".$rows[$i]->id."><a href='#'>".$rows[$i]->nombre."</a>";
                $presentar .= $this->submenu($rows[$i]->id,$cntsm);
                
            }
            else 
            {
                $accion=encriptar("opcion");
                $parametro="opcion=".$rows[$i]->id."&clase_php=".get_class();
                $parametro=encriptar($parametro);
                $presentar .="<li id=".$rows[$i]->id." ><a class='menu-item' href='#' >".$rows[$i]->nombre."</a>";
            }
            $presentar .="</li>";
        }
        $presentar .="</ul>";
        
        
        return $presentar;
    }
    private function submenu($padre,$cntsm)
    {
        
        $presentar="";
        $db = new baseDato();
        $query="call sp_getOpcion('".desencriptar($_SESSION['usuario'])."',".$padre.");";
        $json = $db->read($query,array(),$mensaje);
        $row = json_decode($json);
        $count = count($row);
        if ($count >0)
        {
            $presentar .="<ul>";
            for ($i=0; $i < $count; $i++)
            {
                $query="call sp_getOpcion('".desencriptar($_SESSION['usuario'])."',".$row[$i]->id.");";
                $json = $db->read($query,array(),$mensaje);
                $rows = json_decode($json);
                if (count($rows)>0)
                {      
                    $cntsm++;
                    $presentar .="<li id=".$row[$i]->id."><a href='#'>".$row[$i]->nombre."</a>";
                    $presentar .= $this->submenu($row[$i]->id,$cntsm);
                    $presentar .="</li>";
                }
                else {
                    $presentar .="<li>";
                    $accion=encriptar("opcion");
                    $parametro="opcion=".$row[$i]->id."&clase_php=".get_class();;
                    $parametro=encriptar($parametro);
                    $presentar .="<a id=".$row[$i]->id." href='#' class='menu-item'>";
                    $presentar .=$row[$i]->nombre;
                    $presentar .="</a>";
                    $presentar .="</li>";
                }
                unset($rows);
            }
            unset($row);
            $presentar .="</ul>";
        }
        return $presentar;
    }
}

?>