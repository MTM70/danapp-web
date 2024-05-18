<?php

    class Login extends Controllers{

        public $views;
        public $arrResponse = array();

        public function __construct()
        {
            parent::__construct();
        }

        public function login()
        {
            $data['page_title'] = "DanApp - login";
            $data['page_js'] = array("login.js");

            $this->views->getView($this,"login", $data);
        }

        public function signIn()
        {
            if (isset($_POST['user']) AND isset($_POST['password'])) 
            {
                if ($_POST['user'] AND $_POST['password']) 
                {
                    $response = $this->model->getUser($_POST['user'], md5($_POST['password']));
                    
                    if (!empty($response))
                    {
                        $token = token();
        
                        $updateToken = $this->model->setToken($response['id'], md5($token));
    
                        if ($updateToken) 
                        {
                            session_start();
                            setcookie('token', $token, time() + 86400, '/');
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
    
                            $this->arrResponse = array(
                                'status' => true
                            );
                        }else{
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update token fail!'
                            );
                        }
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'Incorrect user or password!'
                        );
                    }
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Empty fields!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Failed to verify user!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function logout()
        {
            setcookie("token", "", time() - 3600, '/');
            session_start();
            session_unset();
            session_destroy();

            header("Location: ".base_url());
            die();
        }

    }