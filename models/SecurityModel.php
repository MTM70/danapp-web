<?php

    class SecurityModel extends Mysql
    {
        private $token;

        public function __construct()
        {
            parent::__construct();
        }

        public function loginUserToken(String $token)
        {
            $this->token = $token;

            $sql = "SELECT 
                        u.id, u.user, u.pass, 
                        name, last_name, 
                        id_rol, rol 
                    FROM users AS u 
                    INNER JOIN users_details AS ud ON ud.id_user = u.id 
                    INNER JOIN roles AS r ON r.id = u.id_rol 
                    WHERE u.token = '$this->token' AND state = 1 
                    LIMIT 1";

            return $this->selectOne($sql);
        }

    }