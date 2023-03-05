<?php
class claseImagen
{
    private $max_ancho;
    private $max_alto;
    public function setSize($max_ancho, $max_alto)
    {
        $this->max_ancho = $max_ancho;
        $this->max_alto = $max_alto;
    }
    private function compressImage($source, $destination, $quality)
    {
        // Obtenemos la información de la imagen
        $imgInfo = getimagesize($source);
        $mime = $imgInfo['mime'];

        // Creamos una imagen
        switch ($mime) {
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
    private function setTransparency($new_image, $image_source)
    {
        $transparencyIndex = imagecolortransparent($image_source);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0) {
            $transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);
        }

        $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($new_image, 0, 0, $transparencyIndex);
        imagecolortransparent($new_image, $transparencyIndex);
    }
    private function resizeImage($file, $rtDestino)
    {
        try {
            //Parámetros optimización, resolución máxima permitida
            $max_ancho = $this->max_ancho;
            $max_alto = $this->max_alto;

            //Redimensionar
            $rtOriginal = $file;
            $fileType = $this->getImageType($file);

            if ($fileType == 'image/jpeg') {
                $original = imagecreatefromjpeg($rtOriginal);
            } else if ($fileType == 'image/png') {
                $original = imagecreatefrompng($rtOriginal);
            } else if ($fileType == 'image/gif') {
                $original = imagecreatefromgif($rtOriginal);
            }

            list($ancho, $alto) = getimagesize($rtOriginal);

            $x_ratio = $max_ancho / $ancho;
            $y_ratio = $max_alto / $alto;


            if (($ancho <= $max_ancho) && ($alto <= $max_alto)) {
                $ancho_final = $ancho;
                $alto_final = $alto;
            } elseif (($x_ratio * $alto) < $max_alto) {
                $alto_final = ceil($x_ratio * $alto);
                $ancho_final = $max_ancho;
            } else {
                $ancho_final = ceil($y_ratio * $ancho);
                $alto_final = $max_alto;
            }

            $lienzo = imagecreatetruecolor($max_ancho, $max_alto);

            $x = $max_ancho - $ancho_final;
            if ($x > 0) {
                $x /= 2;
            } else
                $x = 0;
            $this->setTransparency($lienzo, $original);
            imagecopyresampled($lienzo, $original, $x, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);

            imagedestroy($original);

            $cal = 8;

            if ($fileType == 'image/jpeg') {
                imagejpeg($lienzo, $rtDestino);
            } else if ($fileType == 'image/png') {
                imagepng($lienzo, $rtDestino);
            } else if ($fileType == 'image/gif') {
                imagegif($lienzo, $rtDestino);
            }
        } catch (Throwable $e) {
            $presentar = "[ERROR] " . $e->getMessage();
        }
    }
    private function getImageType($image)
    {
        $fileType = image_type_to_mime_type(exif_imagetype($image));
        return $fileType;
    }
    public function setImageFile($file, $foto)
    {
        $bok = file_put_contents($file, $foto);
        return $bok;
    }
    public function getImageName()
    {
        $imageName = realpath("tmp/") . "/tmp" . rand() . ".jpg";
        return $imageName;
    }
    private function getContent($file)
    {
        $content = file_get_contents($file);
        return $content;
    }
    public function getReduceImage($image)
    {
        //Dimensión de la imagen
        $medidasimagen = getimagesize($image);

        //Si las imagenes tienen una resolución y un peso aceptable se suben tal cual
        if ($medidasimagen[0] > $this->max_ancho) {
            $fileType = $this->getImageType($image);
            $imageTemporal = $this->getImageName();
            $this->resizeImage($image, $imageTemporal);
            $image = $this->getContent($imageTemporal);
        }
        return $image;
    }
}
