<?php

    function base_url(){
        return BASE_URL;
    }

    function media()
    {
        return BASE_URL."/assets";
    }

    function get_view(String $name_file, $data = null)
    {
        if (file_exists("views/templates/$name_file.php")) {
            require_once ("views/templates/$name_file.php");
        }
    }

    //Muestra información formateada
	function dep($data)
    {
        $format  = print_r('<pre>');
        $format .= print_r($data);
        $format .= print_r('</pre>');
        return $format;
    }

    //Genera un token
    function token()
    {
        $r1 = bin2hex(random_bytes(10));
        $r2 = bin2hex(random_bytes(10));
        $r3 = bin2hex(random_bytes(10));
        $r4 = bin2hex(random_bytes(10));
        $token = $r1.'-'.$r2.'-'.$r3.'-'.$r4;
        return $token;
    }

    function str_ucfirst(string $value)
    {
        return ucfirst(strtolower($value));
    }

    function imageQuality($rutaImagen, $compressionQuality = 10, $resizeFactor = 0.2, $rotationAngle = 0) {
        // Obtener la extensión del archivo
        $extension = pathinfo($rutaImagen, PATHINFO_EXTENSION);
    
        // Intentar abrir la imagen según la extensión
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $imagenOriginal = @imagecreatefromjpeg($rutaImagen);
                break;
            case 'png':
                $imagenOriginal = @imagecreatefrompng($rutaImagen);
                break;
            default:
                throw new Exception("Formato de imagen no compatible: $extension");
        }
    
        if (!$imagenOriginal) {
            // Manejar el caso en que no se pueda abrir la imagen
            throw new Exception("No se pudo abrir la imagen: $rutaImagen");
        }
    
        // Obtener las dimensiones originales de la imagen
        list($widthOrig, $heightOrig) = getimagesize($rutaImagen);
        $width = $widthOrig;
        $height = $heightOrig;
    
        // Rotar la imagen si es necesario (verificar orientación)
        $metadatos = exif_read_data($rutaImagen);
        $orientacion = isset($metadatos['Orientation']) ? $metadatos['Orientation'] : 0;
    
        switch ($orientacion) {
            case 3:
                $imagenOriginal = imagerotate($imagenOriginal, 180, 0);
                break;
            case 6:
                $imagenOriginal = imagerotate($imagenOriginal, -90, 0);
                break;
            case 8:
                $imagenOriginal = imagerotate($imagenOriginal, 90, 0);
                break;
            default:
                $height = $widthOrig;
                $width = $heightOrig;
                break;
        }
    
        // Redimensionar la imagen
        $imagenOptimizada = imagescale($imagenOriginal, $width * $resizeFactor, $height * $resizeFactor);
    
        // Crear una nueva imagen con la calidad deseada
        ob_start();
    
        if ($extension === 'jpeg' || $extension === 'jpg') {
            imagejpeg($imagenOptimizada, null, $compressionQuality);
        } elseif ($extension === 'png') {
            imagepng($imagenOptimizada, null, round(9 - ($compressionQuality / 10)));
        }
    
        $contenidoImagen = ob_get_clean();
        $enlaceDatos = 'data:image/' . $extension . ';base64,' . base64_encode($contenidoImagen);
    
        // Liberar memoria
        imagedestroy($imagenOriginal);
        imagedestroy($imagenOptimizada);
    
        return $enlaceDatos;
    }
    