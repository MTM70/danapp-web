<?php

    class Mysql extends Conexion{

        private $conexion;
        private $query;
        private $values;

        function __construct()
        {
            $this->conexion = new Conexion();
            $this->conexion = $this->conexion->connect();
        }

        public function insert(String $query, Array $values = Array()):int
        {
            $this->query = $query;
            $this->values = $values;
            $insert = $this->conexion->prepare($this->query);

            for ($i=0; $i < count($values); $i++) { 
                $insert->bindParam('value'.$i, $values[$i]);
            }
            
            if ($insert->execute()) {
                return $this->conexion->lastInsertId();
            }else return 0;
        }

        public function selectOne(String $query, Array $values = Array())
        {
            $this->query = $query;
            $select = $this->conexion->prepare($this->query);
            for ($i=0; $i < count($values); $i++) { 
                $select->bindParam('value'.$i, $values[$i]);
            }
            
            if ($select->execute()) {
                return $select->fetch(PDO::FETCH_ASSOC);
            }else return 0;
        }

        public function select(String $query, Array $values = Array())
        {
            $this->query = $query;
            $select = $this->conexion->prepare($this->query);
            for ($i=0; $i < count($values); $i++) { 
                $select->bindParam('value'.$i, $values[$i]);
            }
            
            if ($select->execute()) {
                return $select->fetchall(PDO::FETCH_ASSOC);
            }else return 0;
        }
        
        public function update(String $query, Array $values = Array())
        {
            $this->query = $query;
            $this->values = $values;
            $update = $this->conexion->prepare($this->query);

            for ($i=0; $i < count($values); $i++) { 
                $update->bindParam('value'.$i, $values[$i]);
            }

            return $update->execute();
        }
        
        public function delete(String $query, Array $values = Array())
        {
            $this->query = $query;
            $delete = $this->conexion->prepare($this->query);

            for ($i=0; $i < count($values); $i++) { 
                $delete->bindParam('value'.$i, $values[$i]);
            }

            return $delete->execute();
        }

    }