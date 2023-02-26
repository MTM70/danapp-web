<?php

    require_once("config/config.php");
    require_once("helpers/helpers.php");

    $url = !empty($_GET["url"]) ? $_GET["url"] : "Login/login";
    $arrUrl = explode("/", $url);

    $controller = $arrUrl[0];
    $method = $arrUrl[0];
    $params = "";

    if (isset($arrUrl[1]) AND $arrUrl[1]) {
        $method = $arrUrl[1];

        if (isset($arrUrl[2]) AND $arrUrl[2]) {
            for ($i=2; $i < count($arrUrl); $i++) { 
                $params .= $arrUrl[$i].",";
            }

            $params = trim($params, ",");
        }
    }

    require_once("libraries/core/autoload.php");
    require_once("libraries/core/load.php");