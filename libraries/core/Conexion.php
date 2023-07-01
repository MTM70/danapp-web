<?php

    Class Conexion extends PDO{
        
        private $connect;

        public function __construct(){
            $conectionString = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;

            try {
                $this->connect = new PDO($conectionString, DB_USER, DB_PASS);
                
                $this->connect->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(Exception $e){ 
                echo "ERROR:{$e->getMessage()}";
            }
        }

        public function connect():object {
            return $this->connect;
        }

    }