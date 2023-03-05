<?php
     include_once('db.php');
    if (!isset($_FILES["images"]))
    {
        $presentar="
            <form enctype='multipart/form-data' action='subir.php' method='POST'>
                <input name='images' type='file' />
                <input type='submit' value='Subir archivo' />
            </form>";
        echo $presentar;
    }
    else
    {
        // Ruta subida
        $patch = "tmp/";

        //Parámetros optimización, resolución máxima permitida
        $max_ancho = 40;
        $max_alto = 40;

        $status = 'error'; 
        if(!empty($_FILES["images"]["name"])) 
        { 
            // File info 
            $fileName = $_FILES["images"]["tmp_name"]; 
            $nombrearchivo = $patch . $fileName;

            //Grabamos la imagen en la DB
            $image=addslashes(file_get_contents($fileName));
            $imageEncode=base64_encode($image);
            $sql = "update login set foto = '".$image."' where user='frivera'";

            //$sql = "update login set foto = :imagen where user='frivera'";
            $parameter = [];
            //$parameter[]['imagen'] = $image;
            $mensaje="";
            $db = new baseDato();
            $db->exec($sql, $parameter, $mensaje);
            if ($mensaje =="[OK]")
            {
                $presentar="[OK]";
                echo $presentar;
            }

            $fileType = strtolower(pathinfo($nombrearchivo, PATHINFO_EXTENSION)); 
            
            // Permitimos solo unas extensiones
            $allowTypes = array('jpg','png','jpeg','gif'); 
            if(in_array($fileType, $allowTypes))
            {                 
                // Image temp source 
                $imageTemp = "tmp/tmp".rand().".".$fileType;

                if (file_exists($imageTemp))
                {
                    unlink($imageTemp);
                }

                $imageTempDes = $_FILES["images"]["tmp_name"];
                move_uploaded_file($imageTempDes, $imageTemp);
                
                //Dimensión de la imagen
                $medidasimagen= getimagesize($imageTemp);
                
                //Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
	            if ($medidasimagen[0] > $max_ancho)
                {
                    $imageTempDes = realpath("tmp/")."/tmp".rand().".".$fileType;
                    while ($medidasimagen[0] > $max_ancho)
                    {
                        
                        resizeImage($imageTemp,$imageTempDes);
                        unlink($imageTemp);

                        $imageTemp=$imageTempDes;
                        $imageTempDes = realpath("tmp/")."/tmp".rand().".".$fileType;

                        //Dimensión de la imagen
                        $medidasimagen= getimagesize($imageTemp);
                    }
                    $nombrearchivo = $imageTemp;
                    $pesoimagen = filesize($imageTemp);
                    $porcentaje = 75;
                    $imageTempDes = realpath("tmp/")."/tmp".rand().".".$fileType;
                    $compressedImage = false;
                    while ($pesoimagen > 100000 && $porcentaje > 10)
                    {

                        if (file_exists($imageTempDes))
                        {
                            unlink($imageTempDes);
                        }
                        // Comprimos el fichero
                        $compressedImage = compressImage($imageTemp, $imageTempDes, $porcentaje); 
                        if($compressedImage)
                        { 
                            $pesoimagen = filesize($imageTempDes);
                            unlink($imageTemp);
                            $imageTemp = $imageTempDes;
                            $imageTempDes = realpath("tmp/")."/tmp".rand().".".$fileType;
                            $porcentaje -=5;

                            $nombrearchivo = $imageTemp;
                        }
                        else
                        { 
                            $statusMsg = "La compresion de la imagen ha fallado"; 
                            $porcentaje=0;
                        }                         
                    }



            }
            else
            { 
                $statusMsg = 'Lo sentimos, solo se permiten imágenes con estas extensiones: JPG, JPEG, PNG, & GIF.'; 
            } 
        }
        else
        { 
            $statusMsg = 'Por favor, selecciona una imagen.'; 
        } 
    }

    function compressImage($source, $destination, $quality) 
    { 
        // Obtenemos la información de la imagen
        $imgInfo = getimagesize($source); 
        $mime = $imgInfo['mime']; 
         
        // Creamos una imagen
        switch($mime){ 
            case 'image/jpeg': 
                $image = imagecreatefromjpeg($source); 
                break; 
            case 'image/png': 
                $image = imagecreatefrompng($source); 
                break; 
            case 'image/gif': 
                $image = imagecreatefromgif($source); 
                break; 
            default: 
                $image = imagecreatefromjpeg($source); 
        } 
         
        // Guardamos la imagen
        imagejpeg($image, $destination, $quality); 
         
        // Devolvemos la imagen comprimida
        return $destination; 
    } 
    function resizeImage($file, $rtDestino)
    {
        //Parámetros optimización, resolución máxima permitida
        $max_ancho = 40;
        $max_alto = 40;

        //Redimensionar
        $rtOriginal=$file;
        $fileType = image_type_to_mime_type(exif_imagetype($file));

        if($fileType=='image/jpeg')
        {
            $original = imagecreatefromjpeg($rtOriginal);
        }
        else if($fileType=='image/png')
        {
            $original = imagecreatefrompng($rtOriginal);
        }
        else if($fileType=='image/gif')
        {
            $original = imagecreatefromgif($rtOriginal);
        }

        list($ancho,$alto)=getimagesize($rtOriginal);

        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;


        if( ($ancho <= $max_ancho) && ($alto <= $max_alto) )
        {
            $ancho_final = $ancho;
            $alto_final = $alto;
        }
        elseif (($x_ratio * $alto) < $max_alto)
        {
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        }
        else
        {
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }

        $lienzo=imagecreatetruecolor($ancho_final,$alto_final); 

        imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
        
        //imagedestroy($original);
        
        $cal=8;

        if($fileType=='image/jpeg')
        {
            imagejpeg($lienzo,$rtDestino);
        }
        else if($fileType=='image/png')
        {
            imagepng($lienzo,$rtDestino);
        }
        else if($fileType=='image/gif')
        {
            imagegif($lienzo,$rtDestino);
        }
    }
}
?>