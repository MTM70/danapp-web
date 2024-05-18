<?php

    class Security extends Controllers{

        public function __construct()
        {
            session_start();
            parent::__construct();

            if (!isset($_SESSION['login'])) {
                if (!isset($_COOKIE['token'])) {
                    header('Location: '.BASE_URL);
                }else{
                    $this->reloadSession();
                }
            }
        }

        public function reloadSession()
        {
            $response = $this->model->loginUserToken(md5($_COOKIE['token']));
            
            if (!empty($response))
            {
                //setcookie('token', md5($_COOKIE['token']), time() + 86400, '/');
                $_SESSION['id'] = $response['id'];
                $_SESSION['user'] = $response['user'];
                $_SESSION['name'] = $response['name'];
                $_SESSION['last_name'] = $response['last_name'];
                $_SESSION['idRol'] = $response['id_rol'];
                $_SESSION['rol'] = $response['rol'];
                $_SESSION['id_country'] = $response['id_country'];
                $_SESSION['country'] = $response['country'];
                $_SESSION['country_img'] = $response['img'];
                //$_SESSION['cust'] = $response['cust'];
                $_SESSION['login'] = true;
            }else{
                header('Location: '.BASE_URL);
            }
        }
    }