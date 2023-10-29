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