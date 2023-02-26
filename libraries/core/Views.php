<?php

    class Views{

        public function getView($controller, $view, $data = ""){

            $controller = strtolower(get_class($controller));
            $view = "views/".$controller."/".$view.".php";

            if (file_exists($view)) {
                require_once($view);
            }else{
                require_once("controllers/Error.php");
            }

        }

    }