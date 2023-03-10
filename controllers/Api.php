<?php

class Api extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function signIn()
    {
        if (isset($_POST["user"]) and isset($_POST["pass"])) {

            $res = $this->model->getUser($_POST["user"], md5($_POST["pass"]));

            if (!empty($res)) {

                $apiKey = token();

                $res["API_KEY"] = $apiKey;

                echo json_encode($res);
            } else {
                echo json_encode(array("error" => "Usuario no encontrado"));
            }

            //$array = array("error" => false, "datos" => $res);

        } else {
            echo json_encode(array("error" => "Al conectarse al server"));
        }
    }

    public function getCrops()
    {
        $res = $this->model->getCrops();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron cultivos!"));
        }
    }


    public function getVarieties()
    {
        $res = $this->model->getVarieties();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron cultivos!"));
        }
    }


    public function getProducts()
    {
        $res = $this->model->getProducts();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron cultivos!"));
        }
    }

    public function getSecCustomers()
    {
        $res = $this->model->getSecCustomers();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron fincas, para este usuario!"));
        }
    }

    public function getOrders()
    {
        $res = $this->model->getOrders();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron ordenes, para este usuario!"));
        }
    }

    public function getOrders2()
    {
        $res = $this->model->getOrders2();

        if (!empty($res)) {

            foreach ($res as $key => $values) {

                $year = $values["year"];
                $week = $values["week"];

                //Obtener semanas del ano
                $date = new DateTime;
                $date->setISODate($year, 53);
                $weeks = $date->format("W") === "53" ? 53 : 52;
                //////////////////////////////////////////////////
                
                $cycle = ($values["destination"] == "BOG") ? 14 : 12 ;

                $week = $week + ($cycle - 1);
                if ($week > $weeks) {
                    $week = $week - $weeks;
                    $year++;
                }

                $week = ($week > 9) ? $week : "0".$week ;

                $lunes = date('Y-m-d', strtotime("Y".$year."W".$week."1"));
                $viernes = date('Y-m-d', strtotime("Y".$year."W".$week."5"));

                $res[$key]["notify"] = $year.",".$week.",".$lunes." 07:00:00,".$viernes." 07:00:00";
                //$res[$key]["notify"] = "2023-02-27 21:18:00,2023-02-27 21:18:00";
            }

            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron ordenes, para este usuario!"));
        }
    }

    public function getOrdersTypes()
    {
        $res = $this->model->getOrdersTypes();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron ordenes, para este usuario!"));
        }
    }

    public function getParameters()
    {
        $res = $this->model->getParameters();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron parametros!"));
        }
    }

    public function getParametersOptions()
    {
        $res = $this->model->getParametersOptions();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron parametros!"));
        }
    }

    public function getParametersCrops()
    {
        $res = $this->model->getParametersCrops();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No se encontraron parametros cultivos!"));
        }
    }

    public function sync()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);

            foreach ($data as $k) {
                $res = $this->model->getOrderParameter($k["id_order"], $k["id_variety"], $k["id_parameter"]);

                if ($res) {
                    $res = $this->model->updateDataSync($res["id"], $k["value"], $k["obs"]);
                    if (!$res) {
                        echo json_encode(array("error" => "Al actualizar datos!"));
                        exit();
                    }
                }else{
                    $res = $this->model->setDataSync($k["id_order"], $k["id_variety"], $k["id_parameter"], $k["value"], $k["obs"], DATE("Y"), DATE("W"));
                    if (!$res) {
                        echo json_encode(array("error" => "Al registrar datos!"));
                        exit();
                    }
                }
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Al recebir datos!"));
        }
    }

    public function syncImages()
    {
        if (isset($_POST['file']) AND isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);
            
            $base64_string = $_POST['file'];
            $name = $data["id_order"]."_".$data["id_variety"]."_".$data["id_parameter"].".jpg";
            $outputfile = "uploads/$name";
            $filehandler = fopen($outputfile, 'wb');

            if(fwrite($filehandler, base64_decode($base64_string))){

                fclose($filehandler);

                $res = $this->model->getOrderParameter($data["id_order"], $data["id_variety"], $data["id_parameter"]);

                if ($res) {
                    $res = $this->model->updateDataSync($res["id"], $name, $data["obs"]);
                    if (!$res) {
                        echo json_encode(array("error" => "Al actualizar datos!"));
                        exit();
                    }
                }else{
                    $res = $this->model->setDataSync($data["id_order"], $data["id_variety"], $data["id_parameter"], $name, $data["obs"], DATE("Y"), DATE("W"));
                    if (!$res) {
                        echo json_encode(array("error" => "Al registrar datos!"));
                        exit();
                    }
                }
                
            }else{
                echo json_encode(array("error" => "Al guardar imagen!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Al recebir datos!"));
        }
    }

    public function updateOrderState()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);
            
            $res = $this->model->updateOrderState($data["idOrder"]);
            if (!$res) {
                echo json_encode(array("error" => "Al actualizar estado!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Al recibir datos!"));
        }
    }

    public function deleteImage()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);

            $path = "uploads/".$data["idOrder"]."_".$data["idVariety"]."_".$data["idParameter"].".jpg";

            if (!unlink($path)) {
                echo json_encode(array("error" => "Error deleting image on server, please try again!"));
                exit();
            }
            
            $res = $this->model->deleteOrderParameter($data["idOrder"], $data["idVariety"], $data["idParameter"]);
            if (!$res) {
                echo json_encode(array("error" => "Error deleting server record, please try again!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Al recibir datos!"));
        }
    }
}