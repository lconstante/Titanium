<?php
class claseUsuario
{
    public function mostrar()
    {
        $presentar="CLase usuario";
        
        return $presentar;
    }
    public function getDatosUsuario($user)
    {
        $presentar="";
        $mensaje="";
        $db = new baseDato();
        $sql = "select nombre from seusuario a inner join gepersona b on (a.idpersona = b.id) where usuario='".$user."'";
        $json=$db->read("sp_getQuery($sql,array(),$mensaje);");
        $rows = json_decode($json);
        $contador = count($rows);
        if($mensaje =="[OK]" && $contador > 0)
        {
            $presentar = $rows[0]->nombre;
        }
        return $presentar;
        
    }
    public function getFotoUsuario($user)
    {
        $presentar="";
        $mensaje="";
        $query="select foto from seusuario where usuario='".$user."'";
        
        $parametro_sql=array();
        $parametro_sql["s_sql"]=$query;
        $mensaje = "";
        $parametro["db_procedimiento"]="sp_getQuery";
        $parametro["parametro_sql"]=$parametro_sql;
        $rows = readCall($parametro,$mensaje,false);
        if($mensaje =="[OK]")
        {    
            if (count($rows)>0)
            {
                unset($db);
                $foto = $rows[0]["foto"];

                $imagen = new claseImagen(); 
                $imagen->setSize(40,40);               
                $file=$imagen->getImageName();            
                $imagen->setImageFile($file,$foto);            

                $presentar = $imagen->getReduceImage($file);
                unset($imagen);
                unset($foto);

                header("Content-type:image/jpeg");
            }
        }
        return $presentar;
    }
}
?>