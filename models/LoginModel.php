<?php

    class LoginModel extends Mysql{

        private $id, $user, $password, $token;

        public function __construct()
        {
            parent::__construct();
        }

        public function getUser(string $user, string $password)
        {
            $this->user = $user;
            $this->password = $password;

            $sql = "SELECT 
                        u.id, u.user, u.pass, 
                        name, last_name, 
                        rol 
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    INNER JOIN roles AS r ON r.id = u.id_rol 
                    WHERE u.user = '$this->user' AND u.pass = '$this->password' 
                    LIMIT 1";

            return $this->selectOne($sql);
        }

        public function setToken(int $id, string $token)
        {
            $this->id = $id;
            $this->token = $token;

            $sql = "UPDATE users SET token = :value1 WHERE id = :value0";
            $arrData = array($this->id, $this->token);
            return $this->update($sql, $arrData);
        }

    }