<?php
require_once "vendor/autoload.php";

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

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
                echo json_encode(array("error" => "User not found!"));
            }

            //$array = array("error" => false, "datos" => $res);

        } else {
            echo json_encode(array("error" => "Parameter error!"));
        }
    }

    public function getUsersDetails()
    {
        $res = $this->model->getUsersDetails();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => true, "datos" => "No data users!."));
        }
    }

    public function getCrops()
    {
        $res = $this->model->getCrops();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data Crops!"));
        }
    }


    public function getVarieties()
    {
        $res = $this->model->getVarieties();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data Varieties!"));
        }
    }
    
    public function getVarieties2()
    {
        $res = $this->model->getVarieties2();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data Varieties!"));
        }
    }


    public function getProducts()
    {
        $res = $this->model->getProducts();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data Products!"));
        }
    }

    public function getCustomers()
    {
        $res = $this->model->getCustomers();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data sec_customers!"));
        }
    }
    
    public function getSecCustomers()
    {
        $res = $this->model->getSecCustomers();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data sec_customers!"));
        }
    }

    /* public function getOrders()
    {
        $res = $this->model->getOrders();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data orders!"));
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
            echo json_encode(array("error" => "No data orders!"));
        }
    }

    public function getOrders3()
    {
        if (!isset($_POST["id"]) OR !$_POST["id"]) {
            exit(json_encode(array("error" => "No data orders!")));
        }

        $res = $this->model->getOrders3($_POST["id"]);

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
            echo json_encode(array("error" => "No data orders!"));
        }
    } */

    public function getOrders4()
    {
        if (!isset($_POST["id"]) OR !$_POST["id"]) {
            exit(json_encode(array("error" => "No data orders!")));
        }

        $res = $this->model->getOrders4($_POST["id"]);

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
            echo json_encode(array("error" => "No data orders!"));
        }
    }

    public function getOrders5()
    {
        if (!isset($_POST["id"]) OR !$_POST["id"]) {
            exit(json_encode(array("error" => "No data orders!")));
        }

        $res = $this->model->getOrders5($_POST["id"]);

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
            echo json_encode(array("error" => "No data orders!"));
        }
    }

    public function getOrders6()
    {
        if (!isset($_POST["id"]) OR !$_POST["id"] OR !isset($_POST["dateSync"])) {
            exit(json_encode(array("error" => "No data orders (Parameters)!")));
        }

        if ($_POST["dateSync"] == "") $_POST["dateSync"] = "2000-01-01 19:36:23";

        $res = $this->model->getOrders6($_POST["id"], $_POST["dateSync"]);

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
            echo json_encode(array("error" => "No data orders!"));
        }
    }

    public function getOrdersParameters()
    {
        if (!isset($_POST["id"]) OR !$_POST["id"]) {
            exit(json_encode(array("error" => "Parameter error!")));
        }

        $res = $this->model->getOrdersParameters($_POST["id"]);

        echo json_encode(array("error" => false, "datos" => $res));
    }

    public function getOrdersTypes()
    {
        $res = $this->model->getOrdersTypes();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data orders types!"));
        }
    }

    public function getParameters()
    {
        $res = $this->model->getParameters();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data parameters!"));
        }
    }

    public function getParameters2()
    {
        $res = $this->model->getParameters2();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data parameters!"));
        }
    }

    public function getParameters3()
    {
        $res = $this->model->getParameters3();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data parameters!"));
        }
    }

    public function getParametersOptions()
    {
        $res = $this->model->getParametersOptions();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data parameters options!"));
        }
    }

    public function getParametersCrops()
    {
        $res = $this->model->getParametersCrops();

        if (!empty($res)) {
            echo json_encode(array("error" => false, "datos" => $res));
        } else {
            echo json_encode(array("error" => "No data Parameters crops!"));
        }
    }

    public function sync()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);

            foreach ($data as $k) {
                $res = $this->model->getOrderParameter($k["id_user"], $k["id_order"], $k["id_variety"], $k["id_parameter"]);

                if ($res) {
                    $res = $this->model->updateDataSync($res["id"], $k["value"], $k["obs"]);
                    if (!$res) {
                        echo json_encode(array("error" => "Error updating data!"));
                        exit();
                    }
                }else{
                    $res = $this->model->setDataSync($k["id_user"], $k["id_order"], $k["id_variety"], $k["id_parameter"], $k["value"], $k["obs"], DATE("Y"), DATE("W"));
                    if (!$res) {
                        echo json_encode(array("error" => "Failed to record data!"));
                        exit();
                    }
                }
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
        }
    }

    public function syncImages()
    {
        if (isset($_POST['file']) AND isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);
            
            $base64_string = $_POST['file'];
            $name = $data["id_user"]."_".$data["id_order"]."_".$data["id_variety"]."_".$data["id_parameter"].".jpg";
            $outputfile = "uploads/$name";

            if (!$filehandler = fopen($outputfile, 'wb')) {
                echo json_encode(array("error" => "Failed to save image!"));
                exit();
            }

            if(fwrite($filehandler, base64_decode($base64_string))){

                fclose($filehandler);

                //TODO Save img optimized======================================
                $image = imageQuality('uploads/'.$name);
                // Guardar la imagen en el servidor
                file_put_contents('uploads/optimized/'.$name, base64_decode($image));
                //TODO Save img optimized======================================

                $res = $this->model->getOrderParameter($data["id_user"], $data["id_order"], $data["id_variety"], $data["id_parameter"]);

                if ($res) {
                    $res = $this->model->updateDataSync($res["id"], $name, $data["obs"]);
                    if (!$res) {
                        echo json_encode(array("error" => "Error updating data!"));
                        exit();
                    }
                }else{
                    $res = $this->model->setDataSync($data["id_user"], $data["id_order"], $data["id_variety"], $data["id_parameter"], $name, $data["obs"], DATE("Y"), DATE("W"));
                    if (!$res) {
                        echo json_encode(array("error" => "Failed to record data!"));
                        exit();
                    }
                }

                // Subir la imagen a Google Drive
                /* try {
                    $client = new Google_Client();
                    $client->setAuthConfig('path/to/credentials.json'); // Asegúrate de que el path es correcto
                    $client->addScope(Google_Service_Drive::DRIVE_FILE);

                    // Obtener el token de acceso usando la cuenta de servicio
                    $client->fetchAccessTokenWithAssertion();

                    $service = new Google_Service_Drive($client);

                    $fileMetadata = new Google_Service_Drive_DriveFile([
                        'name' => $name,
                        'parents' => ['your-folder-id'] // Reemplaza 'your-folder-id' con el ID de la carpeta en Google Drive
                    ]);

                    $content = file_get_contents('uploads/optimized/'.$name);

                    $file = $service->files->create($fileMetadata, [
                        'data' => $content,
                        'mimeType' => 'image/jpeg',
                        'uploadType' => 'multipart',
                        'fields' => 'id'
                    ]);

                    $fileId = $file->id;
                    echo json_encode(array("error" => false, "fileId" => $fileId));

                } catch (Exception $e) {
                    echo json_encode(array("error" => "Error uploading to Google Drive: " . $e->getMessage()));
                    exit();
                } */
                
            }else{
                echo json_encode(array("error" => "Failed to save image!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
        }
    }

    /* public function drive()
    {
        // Configura la variable de entorno para las credenciales de Google
        putenv('GOOGLE_APPLICATION_CREDENTIALS=/Applications/XAMPP/xamppfiles/htdocs/danapp-web/config/danapp-424818-721bfc75e128.json');

        //echo realpath('config/client_secret_106315567045-4vtrunacklqi7ilsmi4u3703rf3f5rr0.apps.googleusercontent.com.json');
        // Subir la imagen a Google Drive
        try {
            $client = new Google_Client();

            // Usa las credenciales de la cuenta de servicio por defecto
            $client->useApplicationDefaultCredentials();
            $client->setScopes([Google_Service_Drive::DRIVE_FILE]);

            $service = new Google_Service_Drive($client);

            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => 'qr3.png',
                'parents' => ['1iRES8aqr_3kdOrEvYUgAafw_wSCm14sX'] // Reemplaza 'your-folder-id' con el ID de la carpeta en Google Drive
            ]);

            $content = file_get_contents('/Applications/XAMPP/xamppfiles/htdocs/danapp-web/uploads/optimized/qr.png');

            $file = $service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'image/jpeg',
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            $fileId = $file->id;
            //echo json_encode(array("error" => false, "fileId" => $fileId));

            // Nombre de la imagen que quieres mostrar
            $imageName = 'qr.png';

            // Busca el archivo de imagen por nombre en Google Drive
            $files = $service->files->listFiles([
                'q' => "name='$imageName'",
                'fields' => 'files(id, webViewLink)'
            ]);

            // Verifica si se encontró la imagen
            if (count($files->getFiles()) > 0) {
                // Obtener el ID del archivo
                $fileId = $files->getFiles()[0]->getId();

                // Construir la URL directa de la imagen
                $imageUrl = "https://drive.google.com/uc?id=$fileId";

                // Incrusta la imagen en tu página web
                echo "<img src='$imageUrl' alt='Imagen desde Google Drive'>";
            } else {
                // Si la imagen no se encontró, muestra un mensaje de error o una imagen de reemplazo
                echo "La imagen no se encontró.";
            }

        } catch (Exception $e) {
            echo json_encode(array("error" => "Error uploading to Google Drive: " . $e->getMessage()));
            exit();
        }
    } */

    public function syncImagesValues()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);
            
            $name = $data["id_user"]."_".$data["id_order"]."_".$data["id_variety"]."_".$data["id_parameter"].".jpg";

            $res = $this->model->getOrderParameter($data["id_user"], $data["id_order"], $data["id_variety"], $data["id_parameter"]);

            if (empty($res)) {
                $res = $this->model->setDataSync($data["id_user"], $data["id_order"], $data["id_variety"], $data["id_parameter"], $name, $data["obs"], DATE("Y"), DATE("W"));
                if (!$res) {
                    echo json_encode(array("error" => "Failed to record data!"));
                    exit();
                }
                //echo json_encode(array("error" => "Error updating data!"));
                //exit();
            }

            $res = $this->model->updateDataSync($res["id"], $name, $data["obs"]);
            if (!$res) {
                echo json_encode(array("error" => "Error updating data!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
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
            echo json_encode(array("error" => "parameter error!"));
        }
    }

    public function setOrderClosed()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);
            
            $res = $this->model->setOrderClosed($data["idOrder"], $data["idUser"]);
            if (!$res) {
                echo json_encode(array("error" => "Failed to close order!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
        }
    }

    public function deleteImage()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);

            $path = "uploads/".$data["idUser"]."_".$data["idOrder"]."_".$data["idVariety"]."_".$data["idParameter"].".jpg";
            $path2 = "uploads/optimized/".$data["idUser"]."_".$data["idOrder"]."_".$data["idVariety"]."_".$data["idParameter"].".jpg";

            if (!unlink($path) OR !unlink($path2)) {
                echo json_encode(array("error" => "Error deleting image on server, please try again!"));
                exit();
            }
            
            $res = $this->model->deleteOrderParameter($data["idUser"], $data["idOrder"], $data["idVariety"], $data["idParameter"]);
            if (!$res) {
                echo json_encode(array("error" => "Error deleting server record, please try again!"));
                exit();
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Parameter error!"));
        }
    }

    public function addOrder()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);

            $date = new DateTime($data["date"]);
            $year = $date -> format("Y");
            $week = $date -> format("W");

            $orderNo = $this->model->getIdAddOrder();

            if (empty($orderNo)) {
                echo json_encode(array("error" => "Failed to assign No.!"));
                exit();
            }


            //*Obtener semanas del ano
            $date = new DateTime;
            $date->setISODate($year, 53);
            $weeks = $date->format("W") === "53" ? 53 : 52;
            //////////////////////////////////////////////////

            $cycle = ($data["destination"] == "BOG") ? 14 : 12 ;

            $year2 = $date -> format("Y");
            $week2 = $week + $cycle;
            if ($week2 > $weeks) {
                $week2 = $week2 - $weeks;
                $year2++;
            }

            $week2 = ($week2 > 9) ? $week2 : "0".$week2 ;

            $lunes = date('Y-m-d', strtotime("Y".$year2."W".$week2."1"));
            //*Obtener semanas del ano

            
            $res = $this->model->setOrder($orderNo["order_no"], $data["idSecCust"], $data["idOrderType"], $data["idProduct"], $year, $week, $data["destination"], $lunes);
            if (!$res) {
                echo json_encode(array("error" => "Add order error!"));
                exit();
            }else{
                foreach (explode(",", $data["varieties"]) as $key => $value) {
                    $insert = $this->model->setOrderDetail($res, $value);

                    if (!$insert) {

                        $this->model->deleteOrder($res);
                        $this->model->deleteOrdersDetails($res);

                        echo json_encode(array("error" => "Add order detail error!"));
                        exit();
                    }
                }
            }
            
            echo json_encode(array("error" => false, "id_order" => $res, "order_no" => $orderNo["order_no"], "year" => $year, "week" => $week, "visit_day" => $lunes));
        }else{
            echo json_encode(array("error" => "Parameter error!"));
        }
    }

    public function addVarietyOrder()
    {
        if (isset($_POST['data'])) {

            $data = json_decode($_POST["data"], true);
            
            foreach (explode(",", $data["varieties"]) as $key => $value) {
                $insert = $this->model->setOrderDetail($data["idOrder"], $value);

                if (!$insert) {
                    echo json_encode(array("error" => "Add variety error!"));
                    exit();
                }
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Parameter error!"));
        }
    }

    public function updateOrderVisitDay()
    {
        if (isset($_POST['id']) AND isset($_POST['idOrder']) AND isset($_POST['visitDay'])) {

            $exist = $this->model->getOrderVisitDay($_POST["id"], $_POST["idOrder"]);

            if (empty($exist)) {

                if (!$this->model->setOrderVisitDay($_POST["id"], $_POST["idOrder"], $_POST['visitDay'])) {
                    echo json_encode(array("error" => "Visit day update fail!"));
                    exit();
                }

            }else{
                if (!$this->model->updateOrderVisitDay($exist["id"], $_POST['visitDay'])) {
                    echo json_encode(array("error" => "Visit day update fail!"));
                    exit();
                }
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "Parameter error!"));
        }
    }

    public function getVisitDaysByUser()
    {
        if (isset($_GET["id"]) AND $_GET["id"]) {
            $res = $this->model->getVisitDaysByUser($_GET["id"]);

            if (!empty($res)) {
                echo json_encode(array("error" => false, "datos" => $res));
            } else {
                echo json_encode(array("error" => "No data!"));
            }
        }
    }

    //*EVENTS--------------------------------------------------------------
    public function getEvents()
    {
        if (isset($_GET["id"]) AND $_GET["id"]) {
            $res = $this->model->getEvents($_GET["id"]);

            if (!empty($res)) {
                echo json_encode(array("error" => false, "datos" => $res));
            } else {
                echo json_encode(array("error" => "No data!"));
            }
        }
    }

    public function getEventsCustomers()
    {
        if (isset($_GET["id"]) AND $_GET["id"]) {
            $res = $this->model->getEventsCustomers($_GET["id"]);

            if (!empty($res)) {
                echo json_encode(array("error" => false, "datos" => $res));
            } else {
                echo json_encode(array("error" => "No data!"));
            }
        }
    }

    public function getEventsCustomersOrders()
    {
        if (isset($_GET["id"]) AND $_GET["id"]) {
            $res = $this->model->getEventsCustomersOrders($_GET["id"]);

            if (!empty($res)) {
                echo json_encode(array("error" => false, "datos" => $res));
            } else {
                echo json_encode(array("error" => "No data!"));
            }
        }
    }

    public function syncEventSecCusts()
    {
        if (isset($_POST['data']) AND isset($_POST['idRol'])) {

            $data = json_decode($_POST["data"], true);
            $table = ($_POST['idRol'] != 3) ? 'events_sec_customers' : 'events_sec_customers_bck' ;

            foreach ($data as $k) {

                $k["number_phone"] = (isset($k["number_phone"])) ? $k["number_phone"] : 0 ;

                $res = $this->model->getEventSecCustByEventBySecCust($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["name"], $table);

                if ($res) {
                    $res = $this->model->updateDataEventSecCustSync($res["id"], $k["name"], $k["number_phone"], $k["email"], $k["email_name"], $table);
                    if (!$res) {
                        echo json_encode(array("error" => "Error updating data!"));
                        exit();
                    }
                }else{
                    $res = $this->model->setDataEventSecCustSync($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["name"], $k["number_phone"], $k["email"], $k["email_name"], $k["date"], $table);
                    if (!$res) {
                        echo json_encode(array("error" => "Failed to record data!"));
                        exit();
                    }
                }
            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
        }
    }

    public function syncEventSecCustsVarieties()
    {
        if (isset($_POST['data']) AND isset($_POST['idRol'])) {

            $data = json_decode($_POST["data"], true);
            $table = ($_POST['idRol'] != 3) ? 'events_sec_customers_orders' : 'events_sec_customers_orders_bck' ;

            foreach ($data as $k) {

                if ($k["state"] != 2) {
                    $res = $this->model->getEventVarietyByEventBySecCustByVariety($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["id_variety"], $k["name"], $k["id_event_map"], $table);
                    
                    if (!empty($res)) {
                        $res = $this->model->updateDataEventVarietySync($res["id"], $k["year"], $k["week"], $k["tot_quantity"], $k["replicas"], $k["remark"], $table);
                        if (!$res) {
                            echo json_encode(array("error" => "Error updating data!"));
                            exit();
                        }
                    }else{
                        $res = $this->model->setDataEventVarietySync($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["name"], $k["id_type"], $k["id_product"], $k["id_variety"], $k["id_event_map"], $k["year"], $k["week"], $k["tot_quantity"], $k["replicas"], $k["remark"], $k["date"], $table);
                        if (!$res) {
                            echo json_encode(array("error" => "Failed to record data!"));
                            exit();
                        }
                    }
                }else{

                    $res = $this->model->deleteEventVarietyByEventBySecCustByVariety($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["id_variety"], $k["name"], $k["id_event_map"], $table);

                    if (!$res) {
                        echo json_encode(array("error" => "Failed to delete data!"));
                        exit();
                    }

                }

            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
        }
    }

    public function syncEventSecCustsVarieties2()
    {
        if (isset($_POST['data']) AND isset($_POST['idRol'])) {

            $data = json_decode($_POST["data"], true);
            $table = ($_POST['idRol'] != 3) ? 'events_sec_customers_orders' : 'events_sec_customers_orders_bck' ;

            foreach ($data as $k) {

                if ($k["state"] != 2) {
                    $res = $this->model->getEventVarietyByEventBySecCustByVariety($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["id_variety"], $k["name"], $k["id_event_map"], $table);
                    
                    if (!empty($res)) {
                        $res = $this->model->updateDataEventVarietySync2($res["id"], $k["year"], $k["week"], $k["tot_quantity"], $k["replicas"], $k["remark"], $k["confirm"], $table);
                        if (!$res) {
                            echo json_encode(array("error" => "Error updating data!"));
                            exit();
                        }
                    }else{
                        $res = $this->model->setDataEventVarietySync2($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["name"], $k["id_type"], $k["id_product"], $k["id_variety"], $k["id_event_map"], $k["year"], $k["week"], $k["tot_quantity"], $k["replicas"], $k["remark"], $k["confirm"], $k["date"], $table);
                        if (!$res) {
                            echo json_encode(array("error" => "Failed to record data!"));
                            exit();
                        }
                    }
                }else{

                    $res = $this->model->deleteEventVarietyByEventBySecCustByVariety($k["id_user"], $k["id_event"], $k["id_sec_cust"], $k["id_variety"], $k["name"], $k["id_event_map"], $table);

                    if (!$res) {
                        echo json_encode(array("error" => "Failed to delete data!"));
                        exit();
                    }

                }

            }
            
            echo json_encode(array("error" => false));
        }else{
            echo json_encode(array("error" => "parameter error!"));
        }
    }
}