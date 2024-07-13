<?php
    require_once "vendor/autoload.php";
    require_once('controllers/Security.php');
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    //use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

    /* error_reporting(E_ALL);
    ini_set('display_errors', 'On'); */

    class Dashboard extends Controllers{

        public $views;
        public $arrResponse = array();
        public $html = '';

        public function __construct()
        {
            new Security();
            parent::__construct();
        }

        public function dashboard()
        {
            $data['page_title'] = "Dashboard";
            $data['page_js'] = array("dashboard.js", "calendar.js");

            if ($_SESSION['idRol'] == 1) array_push($data["page_js"], "parameters.js", "users.js", "events.js", "customers.js", "varieties.js");

            $this->views->getView($this,"dashboard", $data);
        }

        public function uploadFile()
        {
            if (isset($_FILES['excel']['tmp_name'])) {      //Si existe file
                
                $file = $_FILES['excel']['tmp_name'];

                $documento = IOFactory::load($file);        //Cargamos en libreria

                $hojaActual = $documento->getSheet(0);      //Hoja 0

                //Nombre columnas
                $columns = array("order_number", "Rooting Year", "Rooting Week", "delivery year", "delivery Week", "ship_number", "exit_day", "customer_number", "customer_name", "secondary_customer_number", "secondary_customer_name", "supply_source_number", "supply_source_name", "Stock", "destination", "customer_order", "product", "Base_Unit_Price", "Unit_Additions", "Total_Unit_Price", "total_quantity", "total_price", "order_type", "created_on", "Special Quality", "crop_number", "crop_name", "variety_number", "variety_name", "Catalog_ID", "Order_Property", "Breeder Status", "Production Status", "Currency", "Remarks");
                $data = array(); //Array donde guardaremos los datos de cada fila

                # Iterar filas
                $f = 0;

                foreach ($hojaActual->getRowIterator() as $fila) {  //Por cada fila

                    # Iterar filas
                    $c = 0;

                    $data[$f] = array(); //Array con posicion de la fila para insertar columnas

                    foreach ($fila->getCellIterator() as $celda) {  //Por cada columna

                        $celdaValue = trim($celda->getValue());

                        if (!$f) {  //Si fila es igual a 0 (encabezado)
                            if ($celdaValue != $columns[$c]) {
                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Column does not match! -> '.$columns[$c]
                                );
    
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }
                        }else{
                            $data[$f][$c] = $celdaValue;
                        }

                        $c++;

                    }

                    $f++;
                }

                $orderNoHist = null;                                                                                                        //* Guardar ID ultima orden

                for ($i = 1; $i < count($data); $i++) {                                                                                     //* Por cada dato del arreglo de datos

                    $isCreatedOrder = false;                                                                                                //* Saber si la orden se creo

                    if (trim($data[$i][0]) != $orderNoHist) {                                                                               //* Si la orden actual es indiferente de la orden anterior

                        if (trim($data[$i][7]) == 0 || trim($data[$i][27]) == 0) continue;                                                                              //* Si la orden es igual a 0 pase al siguiente registro

                        $order = $this->model->getOrderByNo(trim($data[$i][0]));                                                            //* Comprobar si la orden ya existe

                        if (empty($order)) {                                                                                                //* Si no existe la orden

                            $cust = $this->model->getCustByNo(trim($data[$i][7]));                                                          //* Comprobar si el cust existe

                            if (empty($cust)) {                                                                                             //* Si no existe el cust se crea
                                $insert = $this->model->setCustomer(trim($data[$i][7]), trim($data[$i][8]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create cust fail!'.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $cust = $this->model->getCustByNo(trim($data[$i][7]));
                                }
                            }

                            $secCust = $this->model->getSecCustByNo($cust["id"], trim($data[$i][9]));                                       //* Comprobar si el secCust existe

                            if (empty($secCust)) {                                                                                          //* Si no existe el secCust se crea
                                $data[$i][10] = (trim($data[$i][10]) != "") ? trim($data[$i][10]) : $cust["cust"];
                                $insert = $this->model->setSecCustomer($cust["id"], trim($data[$i][9]), trim($data[$i][10]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create sec cust fail! '.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else {
                                    $secCust = $this->model->getSecCustByNo($cust["id"], trim($data[$i][9]));
                                }
                            }

                            $orderType = $this->model->getOrderTypeByType(trim($data[$i][22]));                                             //* Comprobar si el tipo de orden existe

                            if (empty($orderType)) {                                                                                        //* Si no existe el tipo de orden se crea
                                $insert = $this->model->setOrderType(trim($data[$i][22]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create order type fail! '.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $orderType = $this->model->getOrderTypeByType(trim($data[$i][22]));
                                }
                            }

                            $product = $this->model->getProductByProduct(trim($data[$i][16]));                                              //* Comprobar si el producto existe

                            if (empty($product)) {                                                                                          //* Si no existe se crea
                                $insert = $this->model->setProduct(trim($data[$i][16]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create product fail! '.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $product = $this->model->getProductByProduct(trim($data[$i][16]));
                                }
                            }
                            
                            //* GET DATE VISIT--------------------------------------------------------------------------

                            $year = $data[$i][3];
                            $week = $data[$i][4];

                            //Obtener semanas del ano
                            $date = new DateTime;
                            $date->setISODate($year, 53);
                            $weeks = $date->format("W") === "53" ? 53 : 52;
                            //////////////////////////////////////////////////

                            $cycle = (trim($data[$i][14]) == "BOG") ? 14 : 12 ;

                            $week = $week + $cycle;
                            if ($week > $weeks) {
                                $week = $week - $weeks;
                                $year++;
                            }

                            $week = ($week > 9) ? $week : "0".$week ;

                            $lunes = date('Y-m-d', strtotime("Y".$year."W".$week."1"));

                            //* GET DATE VISIT--------------------------------------------------------------------------

                            //* Registramos la orden
                            $insert = $this->model->setOrder(trim($data[$i][0]), $secCust["id"], $orderType["id"], $product["id"], trim($data[$i][3]), trim($data[$i][4]), trim($data[$i][14]), trim($data[$i][34]), $lunes);
                            if (!$insert) {
                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Create order fail! '.$insert
                                );
    
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }else{
                                $order = $this->model->getOrderByNo(trim($data[$i][0]));
                                $isCreatedOrder = true;
                            }
                            
                        }
                    }
                    


                    //TODO Orden detail ----------------------------------------

                    $crop = $this->model->getCropByNo(trim($data[$i][25]));                                                                 //* Comprobar si el cultivo existe

                    if (empty($crop)) {                                                                                                     //* Si no existe se crea

                        $cropGeneral = (trim($data[$i][25]) == 20) ? 2 : 3 ;

                        $insert = $this->model->setCrop($cropGeneral, trim($data[$i][25]), trim($data[$i][26]));
                        if (!$insert) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Create crop fail! '.$insert
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }else{
                            $crop = $this->model->getCropByNo(trim($data[$i][25]));
                        }
                    }

                    $variety = $this->model->getVarietyByNo($crop["id"], trim($data[$i][27]));                                              //* Comprobar si la variedad existe

                    if (empty($variety)) {                                                                                                  //* Si no existe la variedad se crea
                        $insert = $this->model->setVariety($crop["id"], trim($data[$i][27]), trim($data[$i][28]));
                        if (!$insert) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Create variety fail! '.$insert
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }else{
                            $variety = $this->model->getVarietyByNo($crop["id"], trim($data[$i][27]));
                        }
                    }else{
                        //TODO debo revisar primero si ya existe el nombre con codigo en 0 y producto =
                        $insert = $this->model->updateVariety($variety["id"], trim($data[$i][28]));
                    }

                    $orderDetail = $this->model->getOrderDetail($order["id"], $variety["id"]);                                              //* Comprobar si la orden detalle existe

                    if (empty($orderDetail)) {                                                                                              //* Si no existe la orden detalle se crea
                        $insert = $this->model->setOrderDetail($order["id"], $variety["id"], trim($data[$i][20]), floatval(str_replace(",", ".", trim($data[$i][21]))));
                        if (!$insert) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Create detail order fail! '.$insert
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }else if(!$isCreatedOrder && trim($data[$i][0]) != $orderNoHist){                                                   //* Actualizamos date upload
                            if (!$this->model->updateDateUploadByOrder($order["id"])) {
                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'update date upload order fail! '.$order["order_no"]
                                );
                            }
                        }
                    }else{                                                                                                                  //* Si existe se actualiza la cantidad y el total precio
                        $update = $this->model->updateOrderDetail($orderDetail["id"], trim($data[$i][20]), floatval(str_replace(",", ".", trim($data[$i][21]))));
                        if (!$update) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'update detail order fail! '.$update
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                    }

                    $orderNoHist = $order["order_no"];  //Guardamos el hist de la orden
                }

                $update = $this->model->updateOrderDetailState();                                                                           //* Actualizamos state a 1, para no modificar si subimos de nuevo el archivo

                if (!$update) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'update state order fail! -> '.$update
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => 'Upload success'
                    );
                }
                
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No file!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);

        }

        public function downloadData($params)
        {

            if ($params) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="DanApp-db.xlsx"');
                header('Cache-Control: max-age=0');

                $params = explode(",", $params);
                $data1 = $params[0];
                $data2 = $params[1];

                $year1 = substr($data1, 0, 4);
                $week1 = substr($data1, 6, 8);
                $year2 = substr($data2, 0, 4);
                $week2 = substr($data2, 6, 8);

                $response = $this->model->getDataBetweenWeeks($year1, $week1, $year2, $week2);

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', 'Order number');
                $sheet->setCellValue('B1', 'Year');
                $sheet->setCellValue('C1', 'Week');
                $sheet->setCellValue('D1', 'Customer number');
                $sheet->setCellValue('E1', 'Customer name');
                $sheet->setCellValue('F1', 'Secondary customer number');
                $sheet->setCellValue('G1', 'Secondary customer name');
                $sheet->setCellValue('H1', 'Order type');
                $sheet->setCellValue('I1', 'Product');
                $sheet->setCellValue('J1', 'Destination');
                $sheet->setCellValue('K1', 'Variety number');
                $sheet->setCellValue('L1', 'Variety name');
                $sheet->setCellValue('M1', 'Parameter');
                $sheet->setCellValue('N1', 'Parameter value');
                $sheet->setCellValue('O1', 'Parameter obs');


                if (isset($response)) {

                    $f = 2;

                    foreach ($response as $k) {

                        $sheet->setCellValue('A'.$f, $k["order_no"]);
                        $sheet->setCellValue('B'.$f, $k["year"]);
                        $sheet->setCellValue('C'.$f, $k["week"]);
                        $sheet->setCellValue('D'.$f, $k["cust_no"]);
                        $sheet->setCellValue('E'.$f, $k["cust"]);
                        $sheet->setCellValue('F'.$f, $k["sec_cust_no"]);
                        $sheet->setCellValue('G'.$f, $k["sec_cust"]);
                        $sheet->setCellValue('H'.$f, $k["order_type"]);
                        $sheet->setCellValue('I'.$f, $k["product"]);
                        $sheet->setCellValue('J'.$f, $k["destination"]);
                        $sheet->setCellValue('K'.$f, $k["variety_code"]);
                        $sheet->setCellValue('L'.$f, $k["variety"]);
                        $sheet->setCellValue('M'.$f, $k["parameter"]);

                        if ($k["type"] != 4) {
                            $sheet->setCellValue('N'.$f, $k["value"]);
                        }else{
                            $sheet->getStyle('N'.$f)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                            $sheet->getCell('N'.$f)->getStyle()->getFont()->setUnderline(true);
                            $sheet->setCellValue('N'.$f, "View image");
                            $sheet->getCell('N'.$f)->getHyperlink()->setUrl("https://danapp.co/uploads/".$k["value"]);
                        }

                        $sheet->setCellValue('O'.$f, $k["obs"]);

                        $f++;
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(10.14);
                $sheet->getRowDimension(1)->setRowHeight(54.75);
                $sheet->getColumnDimension('B')->setWidth(5.57);
                $sheet->getColumnDimension('C')->setWidth(6.14);
                $sheet->getColumnDimension('D')->setWidth(9.50);
                $sheet->getColumnDimension('E')->setWidth(20.43);
                $sheet->getColumnDimension('F')->setWidth(13.2);
                $sheet->getColumnDimension('G')->setWidth(21.57);
                //$sheet->getColumnDimension('H')->setWidth();
                $sheet->getColumnDimension('I')->setWidth(8.57);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(9);
                $sheet->getColumnDimension('L')->setWidth(18);
                $sheet->getColumnDimension('M')->setWidth(32.57);
                $sheet->getColumnDimension('N')->setWidth(45);
                $sheet->getStyle('N')->getAlignment()->setHorizontal('left');
                $sheet->getColumnDimension('O')->setAutoSize(true);

                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('C1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('D1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('F1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('G1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('I1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('J1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('K1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('K1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('L1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('M1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('M1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('N1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('O1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('O1')->getAlignment()->setWrapText(true);

                $spreadsheet->getActiveSheet()->setAutoFilter('A1:O20');
                $spreadsheet->getActiveSheet()->freezePane('B2');

                
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                //$writer->save('hello world.xlsx');

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'Download data success'
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No filter'
                );
            }

            //echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function downloadData2($params)
        {

            if ($params) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="DanApp-db.xlsx"');
                header('Cache-Control: max-age=0');

                $params = explode(",", $params);
                $data1 = $params[0];
                $data2 = $params[1];

                $year1 = substr($data1, 0, 4);
                $week1 = substr($data1, 6, 8);
                $year2 = substr($data2, 0, 4);
                $week2 = substr($data2, 6, 8);

                $weekFrom = str_replace('-W', '', $params[0]);
                $weekTo = str_replace('-W', '', $params[1]);

                $parameters = $this->model->getParameters();
                $data = $this->model->getDataBetweenWeeks2($weekFrom, $weekTo);
                $response = $this->model->getOrdersParametersBetweenWeeks($weekFrom, $weekTo);

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', 'Order number');
                $sheet->setCellValue('B1', 'User');
                $sheet->setCellValue('C1', 'Secondary customer name');
                $sheet->setCellValue('D1', 'Crop name');
                $sheet->setCellValue('E1', 'Variety name');
                $sheet->setCellValue('F1', 'Year');
                $sheet->setCellValue('G1', 'Week');
                $sheet->setCellValue('H1', 'Date');
                $sheet->setCellValue('I1', 'Customer number');
                $sheet->setCellValue('J1', 'Customer name');
                $sheet->setCellValue('K1', 'Secondary customer number');
                $sheet->setCellValue('L1', 'Order type');
                $sheet->setCellValue('M1', 'Product');
                $sheet->setCellValue('N1', 'Destination');
                $sheet->setCellValue('O1', 'Variety number');

                $c = 16;
                foreach ($parameters as $p) {
                    $sheet->setCellValue([$c, 1], $p["parameter"]);
                    $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);

                    $sheet->getStyle([$c,1])->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle([$c,1])->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle([$c,1])->getAlignment()->setWrapText(true);

                    $c++;
                }

                if (isset($response)) {

                    $f = 2;

                    foreach ($response as $k) {

                        $sheet->setCellValue('A'.$f, $k["order_no"]);
                        $sheet->setCellValue('B'.$f, $k["name"]." ".$k["last_name"]);
                        $sheet->setCellValue('C'.$f, $k["sec_cust"]);
                        $sheet->setCellValue('D'.$f, $k["crop"]);
                        $sheet->setCellValue('E'.$f, $k["variety"]);
                        $sheet->setCellValue('F'.$f, $k["year"]);
                        $sheet->setCellValue('G'.$f, $k["week"]);
                        $sheet->setCellValue('H'.$f, $k["date"]);
                        $sheet->setCellValue('I'.$f, $k["cust_no"]);
                        $sheet->setCellValue('J'.$f, $k["cust"]);
                        $sheet->setCellValue('K'.$f, $k["sec_cust_no"]);
                        $sheet->setCellValue('L'.$f, $k["order_type"]);
                        $sheet->setCellValue('M'.$f, $k["product"]);
                        $sheet->setCellValue('N'.$f, $k["destination"]);
                        $sheet->setCellValue('O'.$f, $k["variety_code"]);

                        $c = 16;
                        foreach ($parameters as $p) {
                            
                            $isset = false;

                            foreach ($data as $d) {
                                if ($d["id_user"] == $k["id_user"] AND $d["id_order"] == $k["id_order"] AND $d["id_variety"] == $k["id_variety"] AND $d["id_parameter"] == $p["id"]) {

                                    if ($p["type"] != 4) {
                                        $sheet->setCellValue([$c, $f], ($d["obs"] != "") ? $d["value"]." (".$d["obs"].")" : $d["value"]);
                                    }else{
                                        $sheet->getStyle([$c, $f])->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                                        $sheet->getCell([$c, $f])->getStyle()->getFont()->setUnderline(true);
                                        $sheet->setCellValue([$c, $f], ($d["obs"] != "") ? $d["value"]." (".$d["obs"].")" : $d["value"]);
                                        $sheet->getCell([$c, $f])->getHyperlink()->setUrl("https://danapp.co/uploads/".$d["value"]);
                                    }
                                    
                                    $isset = true;
                                    break; 
                                }
                            }

                            if (!$isset) $sheet->setCellValue([$c, $f], "");

                            $c++;
                        }

                        $f++;
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(10.14);
                $sheet->getRowDimension(1)->setRowHeight(54.75);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(21.57);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(5.57);
                $sheet->getColumnDimension('G')->setWidth(6.14);
                $sheet->getColumnDimension('H')->setWidth(11);
                $sheet->getColumnDimension('I')->setWidth(9.50);
                $sheet->getColumnDimension('J')->setWidth(20.43);
                $sheet->getColumnDimension('K')->setWidth(13.2);
                //$sheet->getColumnDimension('L')->setWidth();
                $sheet->getColumnDimension('M')->setWidth(8.57);
                $sheet->getColumnDimension('N')->setWidth(12);
                $sheet->getColumnDimension('O')->setWidth(9);
                

                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('C1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('D1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('F1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('G1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('I1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('J1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('K1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('K1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('L1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('M1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('M1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('N1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('O1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('O1')->getAlignment()->setWrapText(true);
                

                $spreadsheet->getActiveSheet()->setAutoFilter('A1:BQ20');
                $spreadsheet->getActiveSheet()->freezePane('F2');

                
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                //$writer->save('hello world.xlsx');

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'Download data success'
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No filter'
                );
            }

            //echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public  function loadUploadLogs()
        {
            $response = $this->model->getUploadLogs();

            if (!empty($response)) {

                $this->html .= '
                    <table class="table table-hover fs-0-8">
                        <tr>
                            <th>Date</th>
                            <th>Delivery week min</th>
                            <th>Delivery week max</th>
                            <th>Orders</th>
                            <th>Rows</th>
                        </tr>
                ';

                foreach ($response as $k) {

                    $this->html .= '
                        <tr>
                            <td>'.$k["date_upload"].'</td>
                            <td>'.$k["week_min"].'</td>
                            <td>'.$k["week_max"].'</td>
                            <td>'.number_format($k["orders"]).'</td>
                            <td>'.number_format($k["rowsData"]).'</td>
                        </tr>
                    ';
                }

                $this->html .= '
                    </table>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadCharts()
        {
            if (isset($_GET["from"]) AND isset($_GET["to"]) AND isset($_GET["type"]) AND isset($_GET["filters"]) AND $_GET["from"] AND $_GET["to"]) {

                $week1 = explode("-W", $_GET["from"])[1];
                $year1 = explode("-W", $_GET["from"])[0];

                $week2 = explode("-W", $_GET["to"])[1];
                $year2 = explode("-W", $_GET["to"])[0];

                $weekFrom = str_replace('-W', '', $_GET["from"]);
                $weekTo = str_replace('-W', '', $_GET["to"]);

                $filters = json_decode($_GET["filters"], true);

                $filterUploads = "";
                $filterCompleted = "";

                if (count($filters["users"]) OR count($filters["customers"]) OR count($filters["destinations"]) OR count($filters["types"]) 
                    OR count($filters["products"]) OR count($filters["crops"]) OR count($filters["varieties"])) {
                    
                    $and = "";
                    $andComplete = "";

                    if (count($filters["users"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["users"]);

                        $andComplete .= "AND oc.id_user IN (".implode(',', $array).") ";
                    }

                    if (count($filters["customers"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["customers"]);

                        $and .= "AND sc.id_cust IN (".implode(',', $array).") ";
                        $andComplete .= "AND sc.id_cust IN (".implode(',', $array).") ";
                    }

                    if (count($filters["destinations"])) {

                        $array = array_map(function($element) {
                            return "'" . explode(",",$element)[0] . "'";
                        }, $filters["destinations"]);

                        $and .= "AND o.destination IN (".implode(',', $array).") ";
                        $andComplete .= "AND o.destination IN (".implode(',', $array).") ";
                    }

                    if (count($filters["types"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["types"]);

                        $and .= "AND o.id_type IN (".implode(',', $array).") ";
                        $andComplete .= "AND o.id_type IN (".implode(',', $array).") ";
                    }

                    if (count($filters["products"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["products"]);

                        $and .= "AND o.id_product IN (".implode(',', $array).") ";
                        $andComplete .= "AND o.id_product IN (".implode(',', $array).") ";
                    }

                    if (count($filters["crops"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["crops"]);

                        $and .= "AND c.id IN (".implode(',', $array).") ";
                        $andComplete .= "AND c.id IN (".implode(',', $array).") ";
                    }

                    if (count($filters["varieties"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["varieties"]);

                        $and .= "AND v.id IN (".implode(',', $array).") ";
                        $andComplete .= "AND v.id IN (".implode(',', $array).") ";
                    }

                    /* if (count($filters["types"])) $and .= "AND o.id_type IN (".implode(',', $filters["types"]).") ";
                    if (count($filters["products"])) $and .= "AND o.id_product IN (".implode(',', $filters["products"]).") ";
                    if (count($filters["crops"])) $and .= "AND c.id IN (".implode(',', $filters["crops"]).") ";
                    if (count($filters["varieties"])) $and .= "AND v.id IN (".implode(',', $filters["varieties"]).") "; */
                    
                    $filterUploads = "
                        INNER JOIN (
                            SELECT DISTINCT od.id_order 
                            FROM orders_details AS od 
                            INNER JOIN orders AS o ON o.id = od.id_order 
                            INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                            INNER JOIN varieties AS v ON v.id = od.id_variety 
                            INNER JOIN crops AS c ON c.id = v.id_crop 
                            WHERE 
                                YEARWEEK(visit_day) BETWEEN :value0 AND :value1 
                                /*(YEAR(visit_day) BETWEEN :value0 AND :value2) AND (WEEK(visit_day) BETWEEN :value1 AND :value3)*/ 
                                $and 
                        ) AS filter ON filter.id_order = o.id 
                    ";

                    $filterCompleted = "
                        INNER JOIN (
                            SELECT DISTINCT op.id_order 
                            FROM orders_parameters AS op 
                            INNER JOIN orders AS o ON o.id = op.id_order 
                            INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order 
                            INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                            INNER JOIN varieties AS v ON v.id = op.id_variety 
                            INNER JOIN crops AS c ON c.id = v.id_crop 
                            WHERE 
                                YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                                /*(YEAR(oc.date) BETWEEN :value0 AND :value2) AND (WEEK(oc.date) BETWEEN :value1 AND :value3)*/ 
                                $andComplete 
                        ) AS filter ON filter.id_order = o.id 
                    ";

                }

                /* $this->arrResponse = array(
                    'status' => true, 
                    'res' => $filterUploads
                );

                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE)); */

                $response = null;

                switch ($_GET["type"]) {
                    case '0':
                        $response = $this->model->getDataCountBetweenWeeksGroupCust($weekFrom, $weekTo, $filterUploads, $filterCompleted);
                        break;

                    case '1':
                        $response = $this->model->getDataCountBetweenWeeksGroupUser($weekFrom, $weekTo, $filterCompleted);
                        break;

                    case '2':
                        $response = $this->model->getDataCountBetweenWeeksGroupCrop($weekFrom, $weekTo, $filterUploads, $filterCompleted);
                        break;

                    case '3':
                        $response = $this->model->getDataCountBetweenWeeksGroupVariety($weekFrom, $weekTo, $filterUploads, $filterCompleted);
                        break;
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $response
                );

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadFiltersChart()
        {
            if (isset($_GET["from"]) AND isset($_GET["to"]) AND isset($_GET["checkType"]) AND $_GET["from"] AND $_GET["to"]) {

                $week1 = explode("-W", $_GET["from"])[1];
                $year1 = explode("-W", $_GET["from"])[0];

                $week2 = explode("-W", $_GET["to"])[1];
                $year2 = explode("-W", $_GET["to"])[0];

                $weekFrom = str_replace('-W', '', $_GET["from"]);
                $weekTo = str_replace('-W', '', $_GET["to"]);

                $users = $this->model->getUsersBetweenWeeks($weekFrom, $weekTo);
                $customers = $this->model->getCustomersBetweenWeeks($weekFrom, $weekTo);
                $destinations = $this->model->getDestinationsBetweenWeeks($weekFrom, $weekTo);
                $orderTypes = $this->model->getOrderTypesBetweenWeeks($weekFrom, $weekTo);
                $products = $this->model->getProductsBetweenWeeks($weekFrom, $weekTo);
                $crops = $this->model->getDataCountBetweenWeeksGroupCrop($weekFrom, $weekTo);
                $varieties = $this->model->getDataCountBetweenWeeksGroupVariety($weekFrom, $weekTo);

                $this->html .= '
                    <div class="col-12 col-md-4 mt-4 mt-md-0" id="filters-chart-users">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 150px;">
                            
                            <p class="position-absolute bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-people-fill me-1"></i>Users</p>

                            <div>';

                                if (!empty($users)) {

                                    $top = "mt-2";

                                    foreach ($users as $k) {
                                        $this->html .= '
                                            <div class="p-1 '.$top.'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-destination" type="checkbox" value="'.$k["id"].','.$k["user"].'" id="checkChartUser'.$k["id"].'">
                                                    <label class="form-check-label cursor-select" for="checkChartUser'.$k["id"].'">
                                                        '.$k["user"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }else $this->html .= '<div class="text-center">No data!</div>';

                            $this->html .= '
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8 mt-4 mt-md-0" id="filters-chart-customers">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 150px;">
                            
                            <p class="position-absolute bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-briefcase-fill me-1"></i>Customers</p>

                            <div>';

                                if (!empty($customers)) {

                                    $top = "mt-2";

                                    foreach ($customers as $k) {
                                        $this->html .= '
                                            <div class="p-1 '.$top.'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-destination" type="checkbox" value="'.$k["id"].','.$k["cust"].'" id="checkChartDest'.$k["id"].'">
                                                    <label class="form-check-label cursor-select" for="checkChartDest'.$k["id"].'">
                                                        '.$k["cust"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }

                            $this->html .= '
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mt-4" id="filters-chart-destinations">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 150px;">
                            
                            <p class="position-absolute bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-airplane-fill me-1"></i>Destinations</p>

                            <div>';

                                if (!empty($destinations)) {

                                    $top = "mt-2";

                                    foreach ($destinations as $k) {
                                        $this->html .= '
                                            <div class="p-1 '.$top.'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-destination" type="checkbox" value="'.$k["destination"].','.$k["destination"].'" id="checkChartDest'.$k["destination"].'">
                                                    <label class="form-check-label cursor-select" for="checkChartDest'.$k["destination"].'">
                                                        '.$k["destination"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }

                            $this->html .= '
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 mt-4" id="filters-chart-types">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 150px;">
                            
                            <p class="position-absolute px-2 bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-building-fill-check me-1"></i>Order types</p>

                            <div>';

                                if (!empty($orderTypes)) {

                                    $top = "mt-2";

                                    foreach ($orderTypes as $k) {
                                        $this->html .= '
                                            <div class="p-1 '.$top.'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-type" type="checkbox" value="'.$k["id"].','.$k["categorie"].'" id="checkChartType'.$k["id"].'" onchange="checkTypeOrder = false;" '.(($k["id"] == 1 AND $_GET["checkType"] == "true") ? 'false' : '' ).'>
                                                    <label class="form-check-label cursor-select" for="checkChartType'.$k["id"].'">
                                                        '.$k["categorie"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }

                            $this->html .= '
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-4 mt-4" id="filters-chart-products">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 150px;">
                            
                            <p class="position-absolute px-2 bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-bookmark-fill me-1"></i>Products</p>

                            <div>';

                                if (!empty($products)) {

                                    $top = "mt-2";

                                    foreach ($products as $k) {
                                        $this->html .= '
                                            <div class="p-1 '.$top.'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-product" type="checkbox" value="'.$k["id"].','.$k["categorie"].'" id="checkChartProd'.$k["id"].'">
                                                    <label class="form-check-label cursor-select" for="checkChartProd'.$k["id"].'">
                                                        '.$k["categorie"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }

                            $this->html .= '
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-4" id="filters-chart-crops">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 250px;">
                            
                            <p class="position-absolute px-2 bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-flower2 me-1"></i>Crops</p>

                            <div>';

                                if (!empty($crops)) {

                                    $top = "mt-2";

                                    foreach ($crops as $k) {
                                        $this->html .= '
                                            <div class="p-1 '.$top.'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-crop" type="checkbox" value="'.$k["id"].','.$k["categorie"].'" id="checkChartCrop'.$k["id"].'">
                                                    <label class="form-check-label cursor-select" for="checkChartCrop'.$k["id"].'">
                                                        '.$k["categorie"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }

                            $this->html .= '
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-4" id="filters-chart-varieties">
                        <div class="border border-dark border-opacity-10 bg-white p-2 rounded-3 overflow-auto" style="max-height: 250px;">
                            
                            <p class="position-absolute px-2 bg-white fw-semibold" style="margin-top: -18px; margin-left: 2px;"><i class="bi bi-flower3 me-1"></i>Varieties</p>

                            <div class="row m-0 '.(!empty($varieties) ? 'mt-5' : '').'">';

                                if (!empty($varieties)) {

                                    //$top = "mt-5";

                                    $this->html .= '
                                        <div class="col-5 position-absolute d-flex" style="margin-top:-33px;">
                                            <input type="text" class="form-control form-control-sm me-2" onkeyup="searchFilter(this);" placeholder="Search...">
                                            <button class="btn btn-light rounded-circle p-2 h-auto w-auto ms-1 position-relative" type="button" onclick="unselectFilter(this);" style="width: 60px; height: 60px;">
                                                <i class="bi bi-square"></i>
                                            </button>
                                        </div>
                                    ';

                                    foreach ($varieties as $k) {
                                        $this->html .= '
                                            <div class="col-12 p-1 '.$top.' filter-item">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select filter-variety" type="checkbox" value="'.$k["id"].','.$k["categorie"].'" id="checkChartVar'.$k["id"].'" onclick="moveUpFilter(this);">
                                                    <label class="form-check-label cursor-select" for="checkChartVar'.$k["id"].'">
                                                        '.$k["categorie"].'
                                                    </label>
                                                </div>
                                            </div>
                                        ';

                                        $top = null;
                                    }
                                }else $this->html .= '<div class="text-center">No data!</div>';

                            $this->html .= '
                            </div>
                        </div>
                    </div>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );

            }else
            $this->arrResponse = array(
                'status' => false, 
                'res' => 'Parameter fail!'
            );

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public  function loadVarietiesCompare()
        {
            if (isset($_GET["from"]) AND isset($_GET["to"]) AND $_GET["from"] AND $_GET["to"]) {

                $week1 = explode("-W", $_GET["from"])[1];
                $year1 = explode("-W", $_GET["from"])[0];

                $week2 = explode("-W", $_GET["to"])[1];
                $year2 = explode("-W", $_GET["to"])[0];

                $weekFrom = str_replace('-W', '', $_GET["from"]);
                $weekTo = str_replace('-W', '', $_GET["to"]);

                $response = $this->model->getVarietiesBetweenWeeks($weekFrom, $weekTo);

                $crop = null;

                if (!empty($response)) {

                    foreach ($response as $k) {

                        if ($crop != $k["id_crop"]) $this->html .= '<div class="w-100 mt-2">'.$k["crop"].'</div>';

                        $this->html .= '
                            <div class="p-1">
                                <input id="checkCompareVariety'.$k["id"].'" type="checkbox" value="'.$k["id"].','.$k["variety"].'" checked>
                                <label class="rounded-3" for="checkCompareVariety'.$k["id"].'">'.$k["variety"].'</label>
                            </div>
                        ';

                        $crop = $k["id_crop"];
                    }

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public  function loadParametersCompare()
        {
            if (isset($_GET["from"]) AND isset($_GET["to"]) AND $_GET["from"] AND $_GET["to"]) {

                $week1 = explode("-W", $_GET["from"])[1];
                $year1 = explode("-W", $_GET["from"])[0];

                $week2 = explode("-W", $_GET["to"])[1];
                $year2 = explode("-W", $_GET["to"])[0];

                $weekFrom = str_replace('-W', '', $_GET["from"]);
                $weekTo = str_replace('-W', '', $_GET["to"]);

                $response = $this->model->getParametersBetweenWeeks($weekFrom, $weekTo);

                if (!empty($response)) {

                    $this->html .= '<div class="col-12 mb-1">Client</div>';

                    foreach ($response as $k) {

                        if ($k["category"] == 2) continue;

                        $this->html .= '
                            <div class="p-1">
                                <input id="checkCompareParameter'.$k["id"].'_1" type="checkbox" value="'.$k["id"].','.$k["parameter"].','.$k["type"].'" '.( $k["id"] == 28 ? 'checked' : '' ).'>
                                <label class="rounded-3" for="checkCompareParameter'.$k["id"].'_1">'.$k["parameter"].'</label>
                            </div>
                        ';

                    }

                    $this->html .= '<hr class="col-12"><div class="col-12 mb-1">Technical</div>';

                    foreach ($response as $k) {

                        if ($k["category"] == 1) continue;

                        $this->html .= '
                            <div class="p-1">
                                <input id="checkCompareParameter'.$k["id"].'_2" type="checkbox" value="'.$k["id"].','.$k["parameter"].','.$k["type"].'">
                                <label class="rounded-3" for="checkCompareParameter'.$k["id"].'_2">'.$k["parameter"].'</label>
                            </div>
                        ';

                    }

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public  function loadTableCompare()
        {
            if (isset($_POST["from"]) AND isset($_POST["to"]) AND $_POST["from"] AND $_POST["to"] AND isset($_POST["varieties"]) AND isset($_POST["parameters"]) AND isset($_POST["filters"]) AND $_POST["varieties"] AND $_POST["parameters"]) {

                $week1 = explode("-W", $_POST["from"])[1];
                $year1 = explode("-W", $_POST["from"])[0];

                $week2 = explode("-W", $_POST["to"])[1];
                $year2 = explode("-W", $_POST["to"])[0];

                $weekFrom = str_replace('-W', '', $_POST["from"]);
                $weekTo = str_replace('-W', '', $_POST["to"]);

                $varieties = json_decode($_POST["varieties"], true);
                $parameters = json_decode($_POST["parameters"], true);

                $varietiesSearch = array();
                $parametersSearch = array();

                foreach ($varieties as $variety) {
                    $varietiesSearch[] = explode(",", $variety)[0]; // Agrega el número al array de números
                }

                foreach ($parameters as $parameter) {
                    $parametersSearch[] = explode(",", $parameter)[0]; // Agrega el número al array de números
                }

                $varietiesSearch = implode(",", $varietiesSearch);
                $parametersSearch = implode(",", $parametersSearch);

                //*===================Filters============================================================

                $filters = json_decode($_POST["filters"], true);

                $filterCompleted = "";

                if (count($filters["users"]) OR count($filters["customers"]) OR count($filters["destinations"]) OR count($filters["types"]) 
                    OR count($filters["products"]) OR count($filters["crops"]) OR count($filters["varieties"])) {
                    
                    $and = "";

                    if (count($filters["users"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["users"]);

                        $and .= "AND oc.id_user IN (".implode(',', $array).") ";
                    }

                    if (count($filters["customers"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["customers"]);

                        $and .= "AND sc.id_cust IN (".implode(',', $array).") ";
                    }

                    if (count($filters["destinations"])) {

                        $array = array_map(function($element) {
                            return "'" . explode(",",$element)[0] . "'";
                        }, $filters["destinations"]);

                        $and .= "AND o.destination IN (".implode(',', $array).") ";
                    }

                    if (count($filters["types"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["types"]);

                        $and .= "AND o.id_type IN (".implode(',', $array).") ";
                    }

                    if (count($filters["products"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["products"]);

                        $and .= "AND o.id_product IN (".implode(',', $array).") ";
                    }

                    if (count($filters["crops"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["crops"]);

                        $and .= "AND c.id IN (".implode(',', $array).") ";
                    }

                    if (count($filters["varieties"])) {

                        $array = array_map(function($element) {
                            return explode(",",$element)[0];
                        }, $filters["varieties"]);

                        $and .= "AND v.id IN (".implode(',', $array).") ";
                    }

                    $filterCompleted = "
                        INNER JOIN (
                            SELECT DISTINCT op.id_order 
                            FROM orders_parameters AS op 
                            INNER JOIN orders AS o ON o.id = op.id_order 
                            INNER JOIN orders_closed AS oc ON oc.id_order = op.id_order 
                            INNER JOIN sec_customers AS sc ON sc.id = o.id_sec_cust 
                            INNER JOIN varieties AS v ON v.id = op.id_variety 
                            INNER JOIN crops AS c ON c.id = v.id_crop 
                            WHERE 
                                YEARWEEK(oc.date) BETWEEN :value0 AND :value1 
                                /*(YEAR(oc.date) BETWEEN :value0 AND :value2) AND (WEEK(oc.date) BETWEEN :value1 AND :value3)*/ 
                                $and 
                        ) AS filter ON filter.id_order = o.id 
                    ";

                }

                //*===================Filters============================================================

                $response = $this->model->getVarietiesBetweenWeeksDistinctOrders($weekFrom, $weekTo, $varietiesSearch, $filterCompleted);
                $data = $this->model->getDataCompareBetweenWeeksOrders($weekFrom, $weekTo, $varietiesSearch, $parametersSearch);

                $this->html .= '
                    <table class="table table-bordered table-hover text-center w-100 fs-0-8" id="table-compare">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle">Variety</th>
                                <th rowspan="2" class="text-center align-middle">Order No</th>
                                <th rowspan="2" class="text-center align-middle">Sec Cust</th>
                                <th rowspan="2" class="text-center align-middle">User</th>
                                <!--<th class="text-center align-middle" rowspan="2">Week</th>-->
                                <!--<th class="text-center align-middle" rowspan="2">Date</th>-->
                                <th rowspan="2">idCust</th>
                                <th rowspan="2">idUser</th>
                                <th rowspan="2">idCrop</th>
                                <th rowspan="2">idVariety</th>
                                <th colspan="'.count($parameters).'" class="text-center">Parameters</th>
                            </tr>
                            <tr>';
                                
                                foreach ($parameters as $parameter) {
                                    $parameter = explode(',', $parameter);

                                    $this->html .= '<td class="text-center align-middle">'.$parameter[1].'</td>';
                                }
                            
                            $this->html .= '</tr>
                        </thead>

                        <tbody>';

                            if (!empty($response)) {
                                foreach ($response as $k) {

                                    $stateFilters = false;

                                    if ($filters["users"]) {
                                        foreach ($filters["users"] as $value) {
                                            if (explode(",",$value)[0] == $k["id_user"]) {
                                                $stateFilters = true;
                                                break;
                                            }
                                        }
                                    }elseif ($filters["varieties"]) {
                                        foreach ($filters["varieties"] as $value) {
                                            if (explode(",",$value)[0] == $k["id_variety"]) {
                                                $stateFilters = true;
                                                break;
                                            }
                                        }
                                    }elseif ($filters["crops"]) {
                                        foreach ($filters["crops"] as $value) {
                                            if (explode(",",$value)[0] == $k["id_crop"]) {
                                                $stateFilters = true;
                                                break;
                                            }
                                        }
                                    }elseif (!$stateFilters) $stateFilters = true;


                                    if (!$stateFilters) continue;

                                    $this->html .= '
                                        <tr>
                                            <th class="align-middle">'.$k["variety"].'</th>
                                            <td class="align-middle">'.$k["order_no"].'</td>
                                            <td class="align-middle">'.$k["sec_cust"].'</td>
                                            <td class="align-middle">'.$k["user"].'</td>
                                            <!--<td class="text-center align-middle">W'.$k["week"].' '.$k["year"].'</td>-->
                                            <!--<td class="text-center align-middle">'.$k["date"].'</td>-->
                                            <td>'.$k["id_cust"].'</td>
                                            <td>'.$k["id_user"].'</td>
                                            <td>'.$k["id_crop"].'</td>
                                            <td>'.$k["id_variety"].'</td>';

                                            $indexImg = 0;

                                            foreach ($parameters as $parameter) {
                                                $parameter = explode(',', $parameter);
                                                
                                                $isset = false;

                                                if (isset($data)) {

                                                    foreach ($data as $d) {
                                                        if ($d["id_order"] == $k["id_order"] AND $d["id_user"] == $k["id_user"] AND $d["id_variety"] == $k["id_variety"] AND $d["id_parameter"] == $parameter[0]) {

                                                            if ($parameter[2] == 4 AND strpos($d["value"], 'jpg') !== false) {
                                                                //'.base_url().'

                                                                $route = base_url()."/uploads/".$d["value"];

                                                                $this->html .= '
                                                                    <td class="text-center align-middle">
                                                                        <img 
                                                                            class="cursor-select" 
                                                                            src="' . base_url()."/uploads/optimized/".$d["value"] . '" 
                                                                            alt="Imagen" 
                                                                            height="100px"
                                                                            data-url="'.$route.'"
                                                                            data-index="'.$indexImg.'" 
                                                                            data-parameter="'.$parameter[1].'" 
                                                                            data-obs="'.$d["obs"].'" 
                                                                            onclick="viewImage(this)"
                                                                            data-bs-toggle="modal" data-bs-target="#modalImage"
                                                                        >
                                                                        '.($d["obs"] ? '<div class="mt-1">'.$d["obs"].'</div>' : '').'
                                                                    </td>
                                                                ';

                                                                $indexImg++;
                                                            }else if ($parameter[2] == 8 AND strpos($d["value"], '=') !== false) {

                                                                $dataB9 = explode(',', $d['value']);
                                                                
                                                                $this->html .= '
                                                                    <td class="text-center align-middle">
                                                                        <table class="table table-bordered text-center bg-light mb-0 fs-0-7">
                                                                            <tr>
                                                                                <th class="bg-secondary bg-opacity-50 text-white align-middle" rowspan="2" width="40px">B-9 ppm</th>';

                                                                                foreach ($dataB9 as $value) {
                                                                                    $value = explode('=', $value);

                                                                                    $this->html .= '
                                                                                        <th class="p-1">'.$value[0].'</th>
                                                                                    ';
                                                                                }

                                                                            $this->html .= '
                                                                            </tr>
                                                                            <tr>';

                                                                                foreach ($dataB9 as $value) {
                                                                                    $value = explode('=', $value);

                                                                                    $this->html .= '
                                                                                        <td class="p-1">'.$value[1].'</td>
                                                                                    ';
                                                                                }
                                                                                
                                                                            $this->html .= '
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                ';
                                                            }
                                                            else $this->html .= '<td class="text-center align-middle">'.$d["value"].($d["obs"] ? '<div class="mt-1 text-muted fs-0-8"><i>('.$d["obs"].')</i></div>' : '').'</td>';

                                                            $isset = true;
                                                        }
                                                    }
                                                }

                                                if(!$isset) $this->html .= '<td></td>';
                                            }

                                        $this->html .= '</tr>
                                    ';
                                }
                            }

                        $this->html .= '</tbody>
                    </table>
                ';

                $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s','/<!--(.|\s)*?-->/');
                $replace = array('>', '<', '\\1');
        
                $this->html = preg_replace($search, $replace, $this->html);

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        //TODO Events --------------------------------------------------------------------
        public function loadEvents()
        {
            $response = $this->model->getEvents();

            if (!empty($response)) {

                $this->html .= '
                    <div class="card-group card-group-scroll">
                ';

                foreach ($response as $k) {

                    $state = ($k["state"]) ? '' : 'bg-danger bg-opacity-10 text-decoration-line-through' ;
                    $stateImg = ($k["state"]) ? '' : 'opacity: 0.3;' ;

                    $this->html .= '
                        <div class="card cursor-select shadow-none border border-light me-2 '.$state.'" style="height:65vh; max-width:41vh;" onclick="openModalViewEvent('.$k["id"].', '."'".$k["name"]."'".')">
                            <div class="img-hover-zoom">
                                <div class="card-img-top" style="background-image: url('.base_url().'/uploads/events/'.$k["image"].'); background-position-y: center; '.$stateImg.'"></div>
                            </div>
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">'.$k["name"].'</h5>
                                    <button class="btn btn-light" onclick="loadEventEdit('.$k["id"].', event);" data-bs-toggle="modal" data-bs-target="#modalAddEvent"><i class="bi bi-pencil" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="More"></i></button>
                                </div>
                                <p class="card-text">'.$k["description"].'</p>
                                <p class="card-text"><small class="text-success"><i class="bi bi-calendar-range me-1"></i>Week '.$k["start_week"].' to Week '.$k["end_week"].'</small></p>
                            </div>
                            <div class="card-footer">
                                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                            </div>
                        </div>
                    ';
                }

                $this->html .= '
                    </div>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function setEvent()
        {
            if ($_POST AND isset($_POST["process"]) AND $_POST["process"]) {

                if ($_POST["process"] == 1) {
                    if (isset($_POST["event-name"]) AND isset($_POST["event-start"]) AND isset($_POST["event-end"]) AND isset($_POST["event-description"]) 
                        AND isset($_FILES["event-file"]) AND $_POST["event-name"] AND $_POST["event-start"] AND $_POST["event-end"] AND $_POST["event-description"]) {

                        if ($this->model->getEventByNameAndWeek(str_ucfirst($_POST["event-name"]), $_POST["event-start"], $_POST["event-end"], 0)) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Event exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        //*Upload image----------------------
                        if ($_FILES["event-file"]) {
                            $filename = str_replace(" ", "", $_POST["event-name"]) . "." . pathinfo($_FILES["event-file"]["name"], PATHINFO_EXTENSION);
                            $tempname = $_FILES["event-file"]["tmp_name"];

                            if (!move_uploaded_file($tempname, __DIR__ . "/../uploads/events/" . $filename)) {
                                /* $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Failed to upload image!' . $_FILES["event-file"]["error"]
                                );

                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE)); */
                                $filename = "event-img.png";
                            }
                        }else{
                            $filename = "event-img.png";
                        }
                        //*----------------------------------
                        
                        $id = $this->model->setEvent(str_ucfirst($_POST["event-name"]), $_POST["event-start"], $_POST["event-end"], str_ucfirst($_POST["event-description"]), $filename);

                        if (!$id) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Add event fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Add event success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }else {
                    if (isset($_POST["event-id"]) AND isset($_POST["event-name"]) AND isset($_POST["event-start"]) AND isset($_POST["event-end"]) AND isset($_POST["event-description"]) AND isset($_POST["event-state"]) 
                        AND isset($_FILES["event-file"]) AND $_POST["event-id"] AND $_POST["event-name"] AND $_POST["event-start"] AND $_POST["event-end"] AND $_POST["event-description"]) {

                        if ($this->model->getEventByNameAndWeek(str_ucfirst($_POST["event-name"]), $_POST["event-start"], $_POST["event-end"], $_POST["event-id"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Event exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        //*Upload image----------------------
                        if ($_FILES["event-file"]) {
                            $filename = str_replace(" ", "", $_POST["event-name"]) .date('H_i_s'). "." . pathinfo($_FILES["event-file"]["name"], PATHINFO_EXTENSION);
                            $tempname = $_FILES["event-file"]["tmp_name"];

                            if (!move_uploaded_file($tempname, __DIR__ . "/../uploads/events/" . $filename)) {
                                /* $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Failed to upload image!' . $_FILES["event-file"]["error"]
                                );

                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE)); */
                                $filename = $_POST["event-file-path"];
                            }
                        }else{
                            $filename = $_POST["event-file-path"];
                        }
                        //*----------------------------------
                        
                        $update = $this->model->updateEvent($_POST["event-id"], str_ucfirst($_POST["event-name"]), $_POST["event-start"], $_POST["event-end"], str_ucfirst($_POST["event-description"]), $filename, $_POST["event-state"]);

                        if (!$update) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update event fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update event success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function setEventYear()
        {
            if (isset($_POST["event-add-year-id"]) AND isset($_POST["event-add-year-year"]) AND $_POST["event-add-year-id"] AND $_POST["event-add-year-year"]) {
                
                $id = $this->model->setEventYear(str_ucfirst($_POST["event-add-year-id"]), $_POST["event-add-year-year"]);

                if (!$id) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Add event year fail!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'Add event year success',
                    'id' => $id
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function setEventMap()
        {
            if (isset($_POST["idEvent"]) AND isset($_POST["year"]) AND isset($_POST["data"]) AND $_POST["idEvent"] AND $_POST["year"] AND $_POST["data"]) {

                $data = json_decode($_POST["data"], true);

                if (empty($data)) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data selected!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                if ($_POST["year"] < date('Y')) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Year not available!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                if(!empty($this->model->getIsStartedEvent($_POST["idEvent"], $_POST["year"]))){
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Event started!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                if(!$this->model->deleteEventMapByYear($_POST["idEvent"], $_POST["year"])){
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Error deleting data!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                foreach ($data as $k) {
                    $id = $this->model->setEventMap($_POST["idEvent"], $_POST["year"], $k["value"], trim($k["greenhouse"]), trim($k["position"]), trim($k["management"]));

                    if (!$id) {
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'Add event map fail!'
                        );

                        exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                    }
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'Add event map success ('.number_format(count($data)).' rows)'
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadEventEdit()
        {
            if (isset($_GET["id"]) AND $_GET["id"]) {
                $response = $this->model->getEvent($_GET["id"]);

                if (!empty($response)) {

                    $this->html = json_encode($response);

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data!'
                    );
                }
            }else{

            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadEventYears()
        {
            if (isset($_GET["id"]) AND $_GET["id"]) {
                $response = $this->model->getEventYears($_GET["id"]);

                if (!empty($response)) {

                    $this->html.='
                    <table class="table table-hover text-center fs-0-8 w-100">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Map data</th>
                                <th>Number of requests</th>
                                <!--<th></th>-->
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';

                    foreach ($response as $k) {

                        $this->html .= '
                            <tr>
                                <td class="align-middle">'.$k["year"].'</td>
                                <td class="align-middle"><i class="'.($k["map_data"] ? 'bi bi-check-circle-fill text-success' : 'bi bi-x-circle-fill text-danger' ).'"></i></td>
                                <td class="align-middle">'.($k["orders"] ? $k["orders"] : '-').'</td>
                                <!--<td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Download
                                        </button>
                                        <ul class="dropdown-menu position-fixed">
                                            <li><a class="dropdown-item" href="#" onclick="downloadDataEventByYear('.$k["year"].', '.$_GET["id"].')">Company</a></li>';

                                            /* if ($k["ordersBck"] > 0) $this->html .= '<li><a class="dropdown-item" href="#">Customers ('.$k["ordersBck"].')</a></li>'; */
                                            $this->html .= '
                                        </ul>
                                    </div>
                                </td>-->
                                <td>
                                    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="More">
                                        <button class="btn btn-light rounded-pill p-2 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu position-fixed">
                                            <li><button class="dropdown-item" type="button" '.($k["orders"] ? 'enabled' : 'disabled').' onclick="downloadDataEventByYear('.$k["year"].', '.$_GET["id"].')"><i class="bi bi-file-earmark-arrow-down me-2"></i>Download</button></li>
                                            <li><button class="dropdown-item" type="button" data-bs-target="#modalViewEventMap" data-bs-toggle="modal" onclick="isEditMap = false; document.querySelector('."'"."#editModeMap"."'".').checked = false; openModalViewEventMap('.$_GET["id"].', '.$k["year"].')"><i class="bi bi-border-style me-2"></i>Map data</button></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        ';
                    }

                    $this->html .= '
                            </tbody>
                        </table>
                    ';

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => 'No data!'
                    );
                }
            }else{

            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function downloadDataEventByYear($params)
        {

            if ($params) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="DanApp-event.xlsx"');
                header('Cache-Control: max-age=0');

                $params = explode(",", $params);
                $year = $params[0];
                $idEvent = $params[1];

                $data = $this->model->getDataEventByYear($year, $idEvent);

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', 'Date');
                /* $sheet->setCellValue('B1', 'Delivery Year');
                $sheet->setCellValue('C1', 'Delivery Week'); */
                $sheet->setCellValue('B1', 'User'); //D
                $sheet->setCellValue('C1', 'Event'); //E
                //$sheet->setCellValue('F1', 'Customer number');
                $sheet->setCellValue('D1', 'Customer name'); //G
                //$sheet->setCellValue('H1', 'Secondary customer number');
                $sheet->setCellValue('E1', 'Secondary customer name'); //I
                $sheet->setCellValue('F1', 'Client name'); //J
                $sheet->setCellValue('G1', 'Email'); //K
                //$sheet->setCellValue('L1', 'Email name');
                $sheet->setCellValue('H1', 'Crop name'); //M
                $sheet->setCellValue('I1', 'Order type'); //N
                $sheet->setCellValue('J1', 'Product'); //O
                //$sheet->setCellValue('P1', 'Variety number');
                $sheet->setCellValue('K1', 'Variety name'); //Q
                $sheet->setCellValue('L1', 'Tot. quantity'); //R
                $sheet->setCellValue('M1', 'replicas'); //S
                //$sheet->setCellValue('T1', 'Greenhouse');
                //$sheet->setCellValue('U1', 'Position');
                $sheet->setCellValue('N1', 'Remark'); //V

                if (!empty($data)) {

                    $f = 2;

                    foreach ($data as $k) {

                        $sheet->setCellValue('A'.$f, $k["date_first"]);
                        //$sheet->setCellValue('B'.$f, $k["year"]);
                        //$sheet->setCellValue('C'.$f, $k["week"]);
                        $sheet->setCellValue('B'.$f, $k["name_user"]." ".$k["last_name"]);
                        $sheet->setCellValue('C'.$f, $k["event"]);
                        //$sheet->setCellValue('F'.$f, $k["cust_no"]);
                        $sheet->setCellValue('D'.$f, $k["cust"]);
                        //$sheet->setCellValue('H'.$f, $k["sec_cust_no"]);
                        $sheet->setCellValue('E'.$f, $k["sec_cust"]);
                        $sheet->setCellValue('F'.$f, $k["name"]);
                        $sheet->setCellValue('G'.$f, $k["email"]);
                        //$sheet->setCellValue('L'.$f, $k["email_name"]);
                        
                        $sheet->setCellValue('H'.$f, $k["crop"]);
                        $sheet->setCellValue('I'.$f, $k["order_type"]);
                        $sheet->setCellValue('J'.$f, $k["product"]);
                        //$sheet->setCellValue('P'.$f, $k["variety_code"]);
                        $sheet->setCellValue('K'.$f, $k["variety"]);

                        $sheet->setCellValue('L'.$f, $k["tot_quantity"]);
                        $sheet->setCellValue('M'.$f, $k["replicas"]);
                        //$sheet->setCellValue('T'.$f, $k["greenhouse"]);
                        //$sheet->setCellValue('U'.$f, $k["position"]);
                        $sheet->setCellValue('N'.$f, $k["remark"]);

                        //$sheet->getStyle("A" . $f)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
                        

                        $f++;
                    }

                    /* $dateColumn = "A";
                    $lastRow = count($data);

                    $sheet->getStyle($dateColumn . "2:" . $dateColumn . ($lastRow + 1))->getNumberFormat()->setFormatCode('yyyy-mm-dd'); */
                }

                $sheet->getColumnDimension('A')->setWidth(10.14);
                $sheet->getRowDimension(1)->setRowHeight(54.75);
                //$sheet->getColumnDimension('B')->setWidth(11);
                //$sheet->getColumnDimension('C')->setWidth(11);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(18);
                //$sheet->getColumnDimension('F')->setWidth(11);
                $sheet->getColumnDimension('D')->setWidth(25);
                //$sheet->getColumnDimension('H')->setWidth(11);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(16);
                $sheet->getColumnDimension('G')->setWidth(25);
                //$sheet->getColumnDimension('L')->setWidth(16);
                $sheet->getColumnDimension('H')->setWidth(16);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(12);
                //$sheet->getColumnDimension('P')->setWidth(9);
                $sheet->getColumnDimension('K')->setWidth(25);
                //$sheet->getColumnDimension('T')->setWidth(20);
                //$sheet->getColumnDimension('U')->setWidth(10);
                $sheet->getColumnDimension('L')->setAutoSize(true);
                $sheet->getColumnDimension('N')->setAutoSize(true);
                

                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('C1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('D1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('E1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('F1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('G1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('H1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('I1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('J1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('K1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('K1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('L1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('M1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('M1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('N1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('N1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('O1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('O1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('P1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('P1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('P1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('Q1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('Q1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('Q1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('R1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('R1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('S1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('S1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('S1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('T1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('T1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('T1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('U1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('U1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('U1')->getAlignment()->setWrapText(true);
                $sheet->getStyle('V1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('V1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('V1')->getAlignment()->setWrapText(true);
                

                $spreadsheet->getActiveSheet()->setAutoFilter('A1:N20');
                //$spreadsheet->getActiveSheet()->freezePane('F2');

                
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save('php://output');
                //$writer->save('hello world.xlsx');

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'Download data success'
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No filter'
                );
            }

            //echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function uploadFileMap()
        {
            if (isset($_FILES['excel']['tmp_name']) AND isset($_POST["selected"])) {      //Si existe file
                
                $file = $_FILES['excel']['tmp_name'];
                $selected = json_decode($_POST['selected'], true);

                /* $this->arrResponse = array(
                    'status' => true, 
                    'res' => json_encode($selected),
                    'issues' => 0
                );

                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE)); */

                $crops = $this->model->getCrops();
                $optionsCrop = null;
                if (!empty($crops)) {
                    foreach ($crops as $crop) {
                        $optionsCrop .= '<option value="'.$crop["id"].'">'. $crop['crop'] .'</option>';
                    }
                }

                $documento = IOFactory::load($file);        //Cargamos en libreria

                $hojaActual = $documento->getSheet(0);      //Hoja 0

                //Nombre columnas
                $columns = array("Greenhouse", "Position", "Variety name", 'Management');
                $data = array(); //Array donde guardaremos los datos de cada fila
                $issues = 0;

                # Iterar filas
                $f = 0;

                $this->html .= '
                    <table class="table table-bordered table-hover fs-0-8 w-100" id="table-upload-map">
                        <thead>
                            <tr>
                                <th class="text-center">Row</th>';

                foreach ($hojaActual->getRowIterator() as $fila) {  //Por cada fila

                    # Iterar filas
                    $c = 0;

                    $data[$f] = array(); //Array con posicion de la fila para insertar columnas

                    foreach ($fila->getCellIterator() as $celda) {  //Por cada columna

                        $celdaValue = trim($celda->getValue());

                        if (!$f) {  //Si fila es igual a 0 (encabezado)
                            if ($celdaValue != $columns[$c]) {
                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Column does not match! -> '.$columns[$c]
                                );
    
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }

                            $this->html .= '
                                <th>'.$columns[$c].'</th>
                            ';
                        }else{
                            $data[$f][$c] = $celdaValue;
                        }

                        $c++;

                    }

                    $f++;
                }

                $this->html .= '
                            <th class="text-center">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';

                for ($i = 1; $i < count($data); $i++) { //Por cada dato del arreglo de datos

                    $exist = $this->model->getVarietyByName($data[$i][2]);

                    $options = false;
                    $optionsRadio = null;
                    $isChecked = false;

                    if (!$exist) {
                        $options = $this->model->getVarietyByNameAdvance($data[$i][2]);

                        if (!empty($options)) {

                            $optionsRadio .= '<p class="d-none d-md-block">Variety not found, check the name in the file or select option:</p>';

                            foreach ($options as $k) {

                                $checked = null;

                                if ($selected) {
                                    foreach ($selected as $s) {
                                        if ($s["value"] == $k["id"] AND $s["row"] == $i) {
                                            $checked = "checked";
                                            $isChecked = true;
                                        }
                                    }
                                }

                                $optionsRadio .= '
                                    <div class="col-md-6 p-1">
                                        <div class="form-check">
                                            <input class="form-check-input cursor-select" type="radio" name="optionVariety'.$i.'" value="'.$k["id"].'" id="checkOptionVariety'.$i.'_'.$k["id"].'" onchange="selectedOptionMap(this, false, '.$i.')" '.$checked.'>
                                            <label class="form-check-label cursor-select" for="checkOptionVariety'.$i.'_'.$k["id"].'">
                                                <b>'.$k["variety"].'</b> ('.$k["variety_code"].' - '.substr($k["crop"], 0, 5).')
                                            </label>
                                        </div>
                                    </div>
                                ';
                            }
                        }else{
                            $optionsRadio .= '<p class="d-none d-md-block text-danger fw-bold">Variety not found, check the name in the file or add variety:</p>';
                        }

                        $optionsRadio .= '
                            <div class="row m-0 align-items-center border border-white py-2 rounded-2">
                                <div class="col-12 col-md-auto pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input cursor-select" type="radio" name="optionVariety'.$i.'" value="add" id="checkOptionVariety'.$i.'_add" '.(!$options ? 'checked' : '').'  onchange="selectedOptionMap(this, true, '.$i.')">
                                    </div>
                                </div>

                                <div class="col-12 col-md">
                                    <form class="row m-0 p-0" action="#" method="POST" id="formAddVariety'.$i.'">
                                        <div class="col-6 col-md">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Crop</span>
                                                <select name="add-variety-map-crop" class="form-select form-select-sm" '.($options ? 'disabled' : '').'>
                                                    <option value="">Choose</option>
                                                    '.$optionsCrop.'
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Code</span>
                                                <input class="form-control form-control-sm" type="number" value="0" name="add-variety-map-code" required '.($options ? 'disabled' : '').'>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-4 mt-1 mt-md-0">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Name</span>
                                                <input class="form-control form-control-sm" type="text" value="'.$data[$i][2].'" name="add-variety-map-name"  required '.($options ? 'disabled' : '').'>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-auto mt-1 mt-md-0">
                                            <button class="form-control btn btn-sm btn-light"  type="button" '.($options ? 'disabled' : '').' onclick="setVarietyFromMap('."'".'formAddVariety'.$i."'".')">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        ';

                        if(!$isChecked) $issues++;

                    }else{
                        $optionsRadio .= '
                            <div class="col-md-6 p-1">
                                <div class="form-check">
                                    <input class="form-check-input cursor-select" type="radio" name="optionVariety'.$i.'" value="'.$exist["id"].'" checked id="checkOptionVariety'.$i.'_'.$exist["id"].'">
                                    <label class="form-check-label cursor-select" for="checkOptionVariety'.$i.'_'.$exist["id"].'">
                                        <b>'.$exist["variety"].'</b> ('.$exist["variety_code"].' - '.substr($exist["crop"], 0, 5).')
                                    </label>
                                </div>
                            </div>
                        ';

                        $isset = false;
                        if ($selected)
                        foreach ($selected as $s) {
                            if($i == $s["row"]){
                                $s["greenhouse"] = $data[$i][0];
                                $s["position"] = $data[$i][1];
                                $s["value"] = $exist["id"];
                                $s["management"] = $data[$i][3];
                                $isset = true;
                            }
                        }

                        if(!$isset) array_push($selected, array("row" => $i, "greenhouse" => $data[$i][0], "position" => $data[$i][1], "value" => $exist["id"], "management" => $data[$i][3], "isForm" => false));
                    }

                    $this->html .= '
                        <tr class="'.((!$exist AND !$isChecked) ? !$options ? 'bg-danger bg-opacity-25' : 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10').'">
                            <td class="align-middle">'.$i.'</td>
                            <td class="align-middle">'.$data[$i][0].'</td>
                            <td class="align-middle">'.$data[$i][1].'</td>
                            <th class="align-middle">'.$data[$i][2].'</th>
                            <td class="align-middle">'.$data[$i][3].'</td>
                            <td class="text-center align-middle">'.(!$exist ? !$options ? '<i class="bi bi-x-circle-fill text-danger"> 3</i>' : '<i class="bi bi-info-circle-fill text-warning"> 2</i>' : '<i class="bi bi-check-circle-fill text-success"> 1</i>').'</td>
                            <td class="align-middle" width="50%"><div class="row m-0 px-4 py-0 overflow-auto" style="max-height:120px;">'.$optionsRadio.'</div></td>
                        </tr>
                    ';
                }

                $this->html .= '
                        </tbody>
                    </table>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html,
                    'selected' => $selected,
                    'issues' => $issues
                );
                
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No file!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);

        }

        public function loadEventMap()
        {
            if (isset($_GET["id_event"]) AND isset($_GET["year"]) AND isset($_GET["isEditMap"]) AND $_GET["id_event"] AND $_GET["year"] AND $_GET["isEditMap"]) {

                $response = $this->model->getEventMapByYear($_GET["id_event"], $_GET["year"]);

                if (!empty($response)) {

                    $varieties = $this->model->getVarieties();
                    $options = null;

                    if (!empty($varieties)) {
                        foreach($varieties as $v) {
                            $options .= '<option value="'.$v["id"].'" data-crop="'.$v["crop"].'">'.$v["variety"].' ('.$v["variety_code"].')</option>';
                        }
                    }

                    $this->html.='
                        <table class="table table-hover w-100 fs-0-8" id="table-event-map">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Greenhouse</th>
                                    <th scope="col">Position</th>
                                    <th scope="col">Crop</th>
                                    <th scope="col">Variety</th>
                                    <th scope="col">Management</th>
                                </tr>
                            </thead>
                            <tbody>';

                    foreach ($response as $k) {

                        $this->html .= '
                            <tr>
                                <td class="text-muted">'.$k["id"].'</td>
                                <td>'.$k["greenhouse"].'</td>
                                <th>'.$k["position"].'</th>
                                <td>'.$k["crop"].'</td>
                                <td>
                                    <select class="form-select form-select-sm w-auto" onchange="updateVarietyMap(this, '.$k["id"].')" '.($_GET["isEditMap"] == "false" ? 'disabled' : '').'>
                                        <option value="'.$k["id_variety"].'" data-crop="'.$v["crop"].'">'.$k["variety"].' ('.$k["variety_code"].')</option>
                                        '.($_GET["isEditMap"] == "true" ? $options : '').'
                                    </select>
                                </td>
                                <td>'.$k["management"].'</td>
                            </tr>
                        ';
                    }

                    $this->html .= '
                            </tbody>
                        </table>
                    ';

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html 
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function setVarietyFromMap()
        {
            if (isset($_POST["add-variety-map-crop"]) AND isset($_POST["add-variety-map-code"]) AND isset($_POST["add-variety-map-name"]) AND $_POST["add-variety-map-crop"] AND $_POST["add-variety-map-name"]) {

                if ($this->model->getVarietyByNo($_POST["add-variety-map-crop"], $_POST["add-variety-map-code"])) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'variety exist!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }
                
                $id = $this->model->setVariety($_POST["add-variety-map-crop"], $_POST["add-variety-map-code"], trim($_POST["add-variety-map-name"]));

                if (!$id) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Add variety fail!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => 'Add variety success'
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function updateVarietyMap()
        {   
            if (isset($_POST["idEvent"]) AND isset($_POST["year"]) AND isset($_POST["idMap"]) AND isset($_POST["idVariety"]) AND $_POST["idEvent"] AND $_POST["year"] AND $_POST["idMap"] AND $_POST["idVariety"]) {

                if ($_POST["year"] < date('Y')) {
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Year not available!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }

                if(!empty($this->model->getIsStartedEvent($_POST["idEvent"], $_POST["year"]))){
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Event started!'
                    );

                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                }
                
                if ($this->model->updateVarietyMap($_POST["idMap"], $_POST["idVariety"])) {
                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => 'Update successfull'
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'Update variety failed!'
                    );
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        //TODO Events --------------------------------------------------------------------


        //TODO Parameters --------------------------------------------------------------------
        public function loadParameters()
        {
            $response = $this->model->getParameters();

            if (!empty($response)) {

                $this->html.='
                    <table class="table table-hover w-100 fs-0-8" id="table-parameters">
                        <thead>
                            <tr>
                                <th scope="col">Parameter</th>
                                <th scope="col">Type</th>
                                <th scope="col">Category</th>
                                <th scope="col">Label</th>
                                <th scope="col">Position</th>
                                <th scope="col">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($response as $k) {

                    $type = array("Yes/No", "Number", "Date", "Text", "Image", "Select", "Select Radio", "Switch", "Option Value");
                    $position = array("Top", "Middle", "Bottom");
                    $category = array("Client", "Technical", "All");

                    $state = ($k["state"]) ? '' : 'bg-danger bg-opacity-10 text-decoration-line-through' ;

                    $this->html .= '
                        <tr class="cursor-select '.$state.'" onclick="loadParameterEdit('.$k["id"].')" data-bs-toggle="modal" data-bs-target="#modalAddParamter">
                            <td scope="row">'.$k["parameter"].'</td>
                            <td>'.$type[$k["type"]].'</td>
                            <td>'.$category[$k["category"]-1].'</td>
                            <td>'.$k["label"].'</td>
                            <td>'.$position[$k["position"]-1].'</td>
                            <td>'.$k["remark"].'</td>
                        </tr>
                    ';
                }

                $this->html .= '
                        </tbody>
                    </table>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        public function loadCrops()
        {
            $response = $this->model->getCrops();

            if (!empty($response)) {

                $this->html.='
                    <div class="col-md-3 p-1">
                        <div class="form-check">
                            <input class="form-check-input cursor-select" type="checkbox" value="17" id="flexCheckChecked" onclick="selectCrops(this)">
                            <label class="form-check-label cursor-select" for="flexCheckChecked">
                                All
                            </label>
                        </div>
                    </div>
                ';

                foreach ($response as $k) {
                    $this->html .= '
                        <div class="col-md-3 p-1">
                            <div class="form-check">
                                <input class="form-check-input cursor-select" type="checkbox" value="'.$k["id"].'" id="flexCheckChecked'.$k["id"].'" onclick="selectCrops(this)">
                                <label class="form-check-label cursor-select" for="flexCheckChecked'.$k["id"].'">
                                    '.$k["crop"].' <i class="text-muted">('.$k["crop_no"].')</i>
                                </label>
                            </div>
                        </div>
                    ';
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadParameterEdit()
        {
            if (isset($_GET["id"]) AND $_GET["id"]) {
                $response = $this->model->getParameter($_GET["id"]);

                if (!empty($response)) {

                    $this->html = json_encode($response);

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data!'
                    );
                }
            }else{

            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function setParameter()
        {
            if ($_POST AND isset($_POST["process"]) AND $_POST["process"]) {

                if ($_POST["process"] == 1) {
                    if (isset($_POST["parameter-name"]) AND isset($_POST["parameter-type"]) AND isset($_POST["parameter-category"]) AND isset($_POST["parameter-label"]) 
                        AND isset($_POST["parameter-remark"]) AND isset($_POST["crops"]) AND isset($_POST["parameter-position"]) AND isset($_POST["options"]) AND $_POST["parameter-name"] 
                        AND $_POST["parameter-category"] AND $_POST["crops"] AND $_POST["parameter-position"] AND $_POST["options"]) {

                        $all = (isset($_POST["parameter-all"])) ? 1 : 0 ;
                        $required = (isset($_POST["parameter-required"])) ? 1 : 0 ;

                        if ($this->model->getParameterByNameAndType(str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Parameter exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $id = $this->model->setParameter(str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"], $_POST["parameter-category"], str_ucfirst($_POST["parameter-label"]), $_POST["parameter-position"], str_ucfirst($_POST["parameter-remark"]), $all, $required);

                        if (!$id) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Add parameter fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        foreach (json_decode(stripslashes($_POST["options"])) as $key => $value) {
                            if (!$this->model->setParameterOption($id, str_ucfirst($value))) {

                                $this->model->deleteParameter($id);
                                $this->model->deleteParametersOptions($id);

                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Add parameter option fail!'
                                );
        
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        foreach (json_decode(stripslashes($_POST["crops"])) as $key => $value) {
                            if (!$this->model->setParameterCrop($id, $value)) {
                                
                                $this->model->deleteParameter($id);
                                $this->model->deleteParametersOptions($id);
                                $this->model->deleteParametersCrops($id);

                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Add parameter crop fail!'
                                );
        
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Add parameter success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }else {
                    if (isset($_POST["parameter-id"]) AND isset($_POST["parameter-name"]) AND isset($_POST["parameter-type"]) AND isset($_POST["parameter-category"]) AND isset($_POST["parameter-label"]) 
                        AND isset($_POST["parameter-remark"]) AND isset($_POST["crops"]) AND isset($_POST["parameter-position"]) AND isset($_POST["options"]) AND $_POST["parameter-id"] 
                        AND $_POST["parameter-name"] AND $_POST["parameter-category"] AND $_POST["crops"] AND $_POST["parameter-position"] AND $_POST["options"]) {

                        $all = (isset($_POST["parameter-all"])) ? 1 : 0 ;
                        $required = (isset($_POST["parameter-required"])) ? 1 : 0 ;

                        if ($this->model->getParameterByNameAndType(str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"], $_POST["parameter-id"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Parameter exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $update = $this->model->updateParameter($_POST["parameter-id"], str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"], $_POST["parameter-category"], str_ucfirst($_POST["parameter-label"]), $_POST["parameter-position"], str_ucfirst($_POST["parameter-remark"]), $all, $required, $_POST["parameter-state"]);

                        if (!$update) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update parameter fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        foreach (json_decode(stripslashes($_POST["options"])) as $key => $value) {

                            $data = explode(',', $value);

                            if (!$data[0]) {
                                if(!$this->model->setParameterOption($_POST["parameter-id"], $data[1])){
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Add parameter option fail!'
                                    );
            
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }
                            }else{

                                if (!$this->model->updateParameterOption($data[0], str_ucfirst($data[1]), $data[2])) {

                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Update parameter option fail!'
                                    );
            
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }
                            }
                        }

                        $this->model->deleteParametersCrops($_POST["parameter-id"]);

                        foreach (json_decode(stripslashes($_POST["crops"])) as $key => $value) {
                            if (!$this->model->setParameterCrop($_POST["parameter-id"], $value)) {

                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Add parameter crop fail!'
                                );
        
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update parameter success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function updateParameter()
        {
            if ($_POST) {
                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $_POST
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        //TODO Parameters --------------------------------------------------------------------

        //TODO Users --------------------------------------------------------------------
        public function loadUsers()
        {
            $response = $this->model->getUsers();

            if (!empty($response)) {

                $this->html.='
                    <table class="table table-hover w-100 fs-0-8" id="table-users">
                        <thead>
                            <tr>
                                <th scope="col">User</th>
                                <th scope="col">Name</th>
                                <th scope="col">Last name</th>
                                <th scope="col">Rol</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($response as $k) {

                    $state = ($k["state"]) ? '' : 'bg-danger bg-opacity-10 text-decoration-line-through' ;

                    $this->html .= '
                        <tr class="cursor-select '.$state.'" onclick="loadUserEdit('.$k["id"].')" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                            <td scope="row">'.$k["user"].'</td>
                            <td>'.$k["name"].'</td>
                            <td>'.$k["last_name"].'</td>
                            <td>'.$k["rol"].'</td>
                        </tr>
                    ';
                }

                $this->html .= '
                        </tbody>
                    </table>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadUserEdit()
        {
            if (isset($_GET["id"]) AND $_GET["id"]) {
                $response = $this->model->getUser($_GET["id"]);

                if (!empty($response)) {

                    $this->html = json_encode($response);

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        public function setUser()
        {
            if ($_POST AND isset($_POST["process"]) AND $_POST["process"]) {

                if ($_POST["process"] == 1) {
                    if (isset($_POST["user-name"]) AND isset($_POST["user-new-password"]) AND isset($_POST["name"]) AND isset($_POST["user-last-name"]) 
                        AND isset($_POST["user-rol"]) AND $_POST["user-name"] AND $_POST["user-new-password"] 
                        AND $_POST["name"] AND $_POST["user-last-name"] AND $_POST["user-rol"] AND $_POST["secCusts"]) {

                        if ($this->model->getUserByUser($_POST["user-name"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'User exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $id = $this->model->setUser($_POST["user-rol"], $_POST["user-name"], md5($_POST["user-new-password"]));

                        if (!$id) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Add user fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        if (!$this->model->setUserDetail($id, str_ucfirst($_POST["name"]), str_ucfirst($_POST["user-last-name"]))) {

                            $this->model->deleteUser($id);

                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Add user details fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        foreach (json_decode(stripslashes($_POST["secCusts"])) as $key => $value) {
                            if (!$this->model->setUserSecCustomer($id, $value)) {
                                
                                $this->model->deleteUser($id);
                                $this->model->deleteUserDetail($id);
                                $this->model->deleteUserSecCustomer($id);

                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Add user sec cust fail!'
                                );
        
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Add user success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }else{
                    if (isset($_POST["user-id"]) AND isset($_POST["user-name"]) AND isset($_POST["user-password"]) AND isset($_POST["name"]) AND isset($_POST["user-last-name"]) 
                        AND isset($_POST["user-rol"]) AND $_POST["user-id"] AND $_POST["user-name"] AND $_POST["user-password"] 
                        AND $_POST["name"] AND $_POST["user-last-name"] AND $_POST["user-rol"] AND $_POST["secCusts"]) {

                        if ($this->model->getUserByUser($_POST["user-name"], $_POST["user-id"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'User exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $password = $_POST["user-password"];
                        if (isset($_POST["user-new-password"]) AND $_POST["user-new-password"]) {
                            $password = md5($_POST["user-new-password"]);
                        }
                        
                        if (!$this->model->updateUser($_POST["user-id"], $_POST["user-rol"], $_POST["user-name"], $password, $_POST["user-state"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update user fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        if (!$this->model->updateUserDetail($_POST["user-id"], str_ucfirst($_POST["name"]), str_ucfirst($_POST["user-last-name"]))) {

                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update user details fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        $this->model->deleteUserSecCustomer($_POST["user-id"]);

                        foreach (json_decode(stripslashes($_POST["secCusts"])) as $key => $value) {
                            if (!$this->model->setUserSecCustomer($_POST["user-id"], $value)) {

                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Add user sec cust fail!'
                                );
        
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update user success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadSecCusts()
        {
            $response = $this->model->getSecCusts();

            if (!empty($response)) {

                $idCust = null;

                foreach ($response as $k) {

                    if ($idCust != $k["id_cust"]) {

                        if ($idCust) {
                            $this->html .= '
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ';
                        }

                        $this->html .= '
                            <div class="col-md-3 mb-2">
                                <div class="accordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-heading'.$k["id_cust"].'">
                                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse'.$k["id_cust"].'" aria-expanded="true" aria-controls="panelsStayOpen-collapse'.$k["id_cust"].'">
                                                <div class="form-check">
                                                    <input class="form-check-input cursor-select" type="checkbox" id="checkCust'.$k["id_cust"].'" onclick="checkCust(this, '."'".'panelsStayOpen-collapse'.$k["id_cust"]."'".');">
                                                </div>'.$k["cust"].'<i class="text-muted mx-2">('.$k["cust_no"].')</i>
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapse'.$k["id_cust"].'" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingOne">
                                            <div class="accordion-body">
                        ';
                    }

                    $secCustNo = ($k["sec_cust_no"] != 0) ? '<i class="text-muted">('.$k["sec_cust_no"].')</i>' : '' ;

                    $this->html .= '
                        <div class="form-check">
                            <input class="form-check-input cursor-select" type="checkbox" value="'.$k["id"].'" id="checkSecCust'.$k["id"].'" onclick="checkSecCust(this, '."'".'panelsStayOpen-collapse'.$k["id_cust"].''."'".', '."'".'checkCust'.$k["id_cust"]."'".');">
                            <label class="form-check-label cursor-select" for="checkSecCust'.$k["id"].'">
                                '.$k["sec_cust"].' '.$secCustNo.'
                            </label>
                        </div>
                    ';

                    $idCust = $k["id_cust"];
                }

                $this->html .= '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadRoles()
        {
            $response = $this->model->getRoles();

            if (!empty($response)) {

                $this->html.='
                    <option value="">Choose...</option>
                ';

                foreach ($response as $k) {
                    $this->html .= '
                        <option value="'.$k["id"].'">'.$k["rol"].'</option>
                    ';
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        //TODO Users --------------------------------------------------------------------

        //TODO Customers --------------------------------------------------------------------
        public function loadCustomers()
        {
            $response = $this->model->getCustomers();

            if (!empty($response)) {

                $this->html.='
                    <table class="table table-hover w-100 fs-0-8" id="table-customers">
                        <thead>
                            <tr>
                                <th scope="col">Cust No</th>
                                <th scope="col">Name</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($response as $k) {

                    //$state = ($k["state"]) ? '' : 'bg-danger bg-opacity-10 text-decoration-line-through' ;

                    $this->html .= '
                        <tr class="cursor-select" onclick="loadCustomerEdit('.$k["id"].')" data-bs-toggle="modal" data-bs-target="#modalAddCustomer">
                            <td class="align-middle">'.$k["cust_no"].'</td>
                            <td class="d-flex align-items-center"><div class="rounded-circle bg-success bg-opacity-10 me-3" style="width:30px; height:30px; background-image: url('."'".media()."/img/customers/".$k["logo"]."'".'); background-size: cover;"></div> '.$k["cust"].'</td>
                        </tr>
                    ';
                }

                $this->html .= '
                        </tbody>
                    </table>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadCustomerEdit()
        {
            if (isset($_GET["id"]) AND $_GET["id"]) {
                $response = $this->model->getCustomer($_GET["id"]);

                if (!empty($response)) {

                    $this->html = json_encode($response);

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        public function setCustomer()
        {
            if ($_POST AND isset($_POST["process"]) AND $_POST["process"]) {

                $filename = "";

                if ($_POST["process"] == 1) {
                    if (isset($_POST["customer-number"]) AND isset($_POST["customer-name"]) AND isset($_FILES["customer-file"])  AND isset($_POST["sec-customers"]) AND $_POST["customer-name"]) {

                        $_POST["customer-number"] = $this->model->getNewCustNo()["cust_no"];
                        if ($this->model->getCustomerByNumber($_POST["customer-number"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Customer exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        //*Upload image----------------------
                        if ($_FILES["customer-file"]) {
                            $filename = $_POST["customer-number"] . "." . pathinfo($_FILES["customer-file"]["name"], PATHINFO_EXTENSION);
                            $tempname = $_FILES["customer-file"]["tmp_name"];

                            if (!move_uploaded_file($tempname, __DIR__ . "/../assets/img/customers/" . $filename)) {
                                /* $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Failed to upload image!' . $_FILES["event-file"]["error"]
                                );

                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE)); */
                                $filename = "danziger.png";
                            }
                        }else{
                            $filename = "danziger.png";
                        }
                        //*----------------------------------
                        
                        $id = $this->model->setCustomer($_POST["customer-number"], $_POST["customer-name"], $filename);

                        if (!$id) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Add customer fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        foreach (json_decode(stripslashes($_POST["sec-customers"])) as $key => $value) {

                            $data = explode(',', $value);

                            if (!$this->model->getSecCustByNo($id, $data[0])) {
                                if (!$this->model->setSecCustomer($id, $data[0], $data[1])) {
    
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Add sec cust fail!'
                                    );
            
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }
                            }

                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Add customer success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }else{
                    if (isset($_POST["customer-id"]) AND isset($_POST["customer-number"]) AND isset($_POST["customer-name"]) AND isset($_FILES["customer-file"])  AND isset($_POST["sec-customers"]) AND $_POST["customer-id"] AND $_POST["customer-number"] AND $_POST["customer-name"]) {
                        
                        //*Upload image----------------------
                        if ($_FILES["customer-file"]) {
                            $filename = $_POST["customer-number"] .date('H_i_s'). "." . pathinfo($_FILES["customer-file"]["name"], PATHINFO_EXTENSION);
                            $tempname = $_FILES["customer-file"]["tmp_name"];

                            if (!move_uploaded_file($tempname, __DIR__ . "/../assets/img/customers/" . $filename)) {
                                $filename = $_POST["customer-file-path"];
                            }
                        }else{
                            $filename = $_POST["customer-file-path"];
                        }
                        //*----------------------------------

                        if (!$this->model->updateSecCustomer($_POST["customer-id"], $_POST["customer-name"], $filename)) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update customer fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        foreach (json_decode(stripslashes($_POST["sec-customers"])) as $key => $value) {

                            $data = explode(',', $value);

                            if (!$this->model->getSecCustByNo($_POST["customer-id"], $data[0])) {
                                if (!$this->model->setSecCustomer($_POST["customer-id"], $data[0], $data[1])) {
    
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Add sec cust fail!'
                                    );
            
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }
                            }

                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update cust success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        //TODO Customers --------------------------------------------------------------------


        //TODO Varieties --------------------------------------------------------------------
        public function loadVarieties()
        {
            $response = $this->model->getVarieties();

            if (!empty($response)) {

                $this->html.='
                    <table class="table table-hover w-100 fs-0-8" id="table-varieties">
                        <thead>
                            <tr>
                                <th scope="col">Crop</th>
                                <th scope="col">Variety Code</th>
                                <th scope="col">Variety</th>
                                <th scope="col">Image</th>
                            </tr>
                        </thead>
                        <tbody>';

                foreach ($response as $k) {

                    //$state = ($k["state"]) ? '' : 'bg-danger bg-opacity-10 text-decoration-line-through' ;

                    $this->html .= '
                        <tr class="cursor-select" onclick="loadVarietyEdit('.$k["id"].')" data-bs-toggle="modal" data-bs-target="#modalAddVariety">
                            <td class="align-middle">'.$k["crop"].'</td>
                            <td class="">'.$k["variety_code"].'</td>
                            <td class="">'.$k["variety"].'</td>
                            <td class="">'.($k["img"] ? '<img src="'.media()."/img/varieties/".$k["img"].'" height="100">' : '').'</td>
                        </tr>
                    ';
                }

                $this->html .= '
                        </tbody>
                    </table>
                ';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadVarietyEdit()
        {
            if (isset($_GET["id"]) AND $_GET["id"]) {
                $response = $this->model->getVarietyById($_GET["id"]);

                if (!empty($response)) {

                    $this->html = json_encode($response);

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }
        public function setVariety()
        {
            if ($_POST AND isset($_POST["process"]) AND $_POST["process"]) {

                $filename = "";

                if ($_POST["process"] == 1) {
                    /* if (isset($_POST["customer-number"]) AND isset($_POST["customer-name"]) AND isset($_FILES["customer-file"])  AND isset($_POST["sec-customers"]) AND $_POST["customer-name"]) {

                        $_POST["customer-number"] = $this->model->getNewCustNo()["cust_no"];
                        if ($this->model->getCustomerByNumber($_POST["customer-number"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Customer exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        //Upload image----------------------
                        if ($_FILES["customer-file"]) {
                            $filename = $_POST["customer-number"] . "." . pathinfo($_FILES["customer-file"]["name"], PATHINFO_EXTENSION);
                            $tempname = $_FILES["customer-file"]["tmp_name"];

                            if (!move_uploaded_file($tempname, __DIR__ . "/../assets/img/customers/" . $filename)) {
                                /* $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Failed to upload image!' . $_FILES["event-file"]["error"]
                                );

                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE)); *
                                //$filename = "danziger.png";
                            }
                        }else{
                            //$filename = "danziger.png";
                        }
                        //----------------------------------
                        
                        $id = $this->model->setCustomer($_POST["customer-number"], $_POST["customer-name"], $filename);

                        if (!$id) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Add customer fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        foreach (json_decode(stripslashes($_POST["sec-customers"])) as $key => $value) {

                            $data = explode(',', $value);

                            if (!$this->model->getSecCustByNo($id, $data[0])) {
                                if (!$this->model->setSecCustomer($id, $data[0], $data[1])) {
    
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Add sec cust fail!'
                                    );
            
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }
                            }

                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Add customer success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    } */
                }else{
                    if (isset($_POST["variety-id"]) AND isset($_POST["variety-number"]) AND isset($_POST["variety-name"]) AND isset($_FILES["variety-file"]) AND $_POST["variety-id"] AND $_POST["variety-number"] AND $_POST["variety-name"]) {
                        
                        $variety = $this->model->getVarietyById($_POST["variety-id"]);

                        if (empty($variety)) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Variety not exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        //*Upload image----------------------
                        if ($_FILES["variety-file"]) {
                            $filename = $_POST["variety-number"] .date('H_i_s'). "." . pathinfo($_FILES["variety-file"]["name"], PATHINFO_EXTENSION);
                            
                            $tempname = $_FILES["variety-file"]["tmp_name"];

                            if (!move_uploaded_file($tempname, __DIR__ . "/../assets/img/varieties/" . $filename)) {
                                $filename = $_POST["variety-file-path"];
                            }else{
                                //TODO Save img optimized======================================
                                $image = imageQuality("assets/img/varieties/" . $filename, 1);
                                // Guardar la imagen en el servidor
                                file_put_contents('assets/img/varieties/'.$filename, base64_decode($image));
                                //TODO Save img optimized======================================
                            }
                        }else{
                            $filename = $_POST["variety-file-path"];
                        }
                        //*----------------------------------

                        if (!$this->model->updateVarietyImg($_POST["variety-id"], $filename)) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Update variety fail!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }

                        if($variety["img"]){
                            $oldImageDir = __DIR__ . "/../assets/img/varieties/" . $variety["img"];

                            // Verificar si el archivo existe antes de intentar eliminarlo
                            if (file_exists($oldImageDir)) {
                                !unlink($oldImageDir);
                            }
                        }

                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update variety success'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'No data!'
                        );
                    }
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        //TODO Varieties --------------------------------------------------------------------


        //TODO Calendar --------------------------------------------------------------------

        public function loadCalendarFilters()
        {
            if (isset($_GET["week"])) {

                $year = substr($_GET["week"], 0, 4);
                $week = substr($_GET["week"], 6, 2);

                $ordersTypes = $this->model->getOrdersTypesByWeekById($year, $week);
                $destinations = $this->model->getOrdersDestinationsByWeekById($year, $week);
                $crops = $this->model->getOrdersCropsByWeekById($year, $week);

                if (!empty($ordersTypes)) {
                    
                    $this->html .= '
                        <h6><i class="bi bi-building-fill-check me-1"></i>Orders types</h6>
                        <div class="d-flex flex-wrap mtm-checkbox-filter" id="calendar-filters-types">
                    ';

                    foreach ($ordersTypes as $k) {
                        $this->html .= '
                            <div class="p-1">
                                <input id="checkT'.$k["id_type"].'" type="checkbox" value="'.$k["id_type"].'" onclick="calendarFilter()">
                                <label class="rounded-3" for="checkT'.$k["id_type"].'">'.$k["type"].'</label>
                            </div>
                        ';
                    }

                    $this->html .= '</div><hr>';
                }

                if (!empty($destinations)) {
                    
                    $this->html .= '
                        <h6><i class="bi bi-airplane-fill me-1"></i>Destinations</h6>
                        <div class="d-flex flex-wrap mtm-checkbox-filter" id="calendar-filters-destinations">
                    ';

                    foreach ($destinations as $k) {
                        $this->html .= '
                            <div class="p-1">
                                <input id="checkD'.$k["destination"].'" type="checkbox" value="'.$k["destination"].'" onclick="calendarFilter()">
                                <label class="rounded-3" for="checkD'.$k["destination"].'">'.$k["destination"].'</label>
                            </div>
                        ';
                    }

                    $this->html .= '</div><hr>';
                }

                if (!empty($crops)) {
                    
                    $this->html .= '
                        <h6><i class="bi bi-flower2 me-1"></i>Crops</h6>
                        <div class="d-flex flex-wrap mtm-checkbox-filter" id="calendar-filters-crops">
                    ';

                    foreach ($crops as $k) {
                        $this->html .= '
                            <div class="p-1">
                                <input id="checkC'.$k["id"].'" type="checkbox" value="'.$k["id"].'" onclick="calendarFilter()">
                                <label class="rounded-3" for="checkC'.$k["id"].'">'.$k["crop"].'</label>
                            </div>
                        ';
                    }

                    $this->html .= '</div>';
                }

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function loadCalendarWeek()
        {
            if (isset($_GET["week"])) {

                $year = substr($_GET["week"], 0, 4);
                $week = substr($_GET["week"], 6, 2);

                $response = $this->model->getOrdersByWeekById($year, $week);

                if (!empty($response)) {

                    $days = array();

                    for ($d=1; $d <= 5 ; $d++) { 
                        
                        $day = date('Y-m-d', strtotime("Y".$year."W".$week.$d));
                        array_push($days, $day);

                    }

                    $this->html = '
                        <table class="table table-bordered text-center" id="table-calendar" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th>M<!--<br><i class="text-muted fw-light">('.$days[0].')</i>--></th>
                                    <th>T<!--<br><i class="text-muted fw-light">('.$days[1].')</i>--></th>
                                    <th>W<!--<br><i class="text-muted fw-light">('.$days[2].')</i>--></th>
                                    <th>T<!--<br><i class="text-muted fw-light">('.$days[3].')</i>--></th>
                                    <th>F<!--<br><i class="text-muted fw-light">('.$days[4].')</i>--></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>';

                                    foreach ($days as $key => $day) {

                                        $this->html .= '<td class="p-3" ondragover="dragover(event)" ondragleave="dragleave(event)" ondrop="drop(event)" data-date="'.$day.'">';

                                        foreach ($response as $o) {

                                            if (substr($o["visit_day"], 0, 10) != $day) continue;

                                            $visitDay = new DateTime($o["visit_day"]);

                                            $this->html .= '
                                                <div 
                                                    class="card shadow-sm border mb-3" 
                                                    style="cursor:move;" 
                                                    draggable="true" 
                                                    ondragstart="dragstart(event, 0);" 
                                                    ondragend="dragEnd(event)" 
                                                    data-id="'.$o["id_order"].'" 
                                                    data-time="'.substr($o["visit_day"], 11, 5).'"
                                                    data-type="'.$o["id_type"].'"
                                                    data-destination="'.$o["destination"].'"
                                                    data-crop="'.$o["id_crop"].'"
                                                    data-notify="'.$o["notify"].'"
                                                >
                                                    <div class="card-header d-flex justify-content-between align-items-center bg-primary bg-opacity-10 p-1">
                                                        <div class="ps-2">
                                                            <h6 class="card-text text-secondary"><i class="bi bi-flower2 me-1"></i>'.$o["crop"].'</h6>
                                                        </div>
                                                        <div>
                                                            <h6 class="card-text text-secondary">'.substr($o["type"], 0, 20).'</h6>
                                                            <div>
                                                                <h6 class="card-text fs-0-8"><i class="bi bi-bookmark me-1"></i>'.$o["product"].' | <i class="bi bi-airplane me-1"></i>'.$o["destination"].'</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body accordion accordion-flush p-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-inboxes-fill me-3"></i>
                                                            <div class="text-start">
                                                                <h6 class="card-title my-0 py-0">No.'.$o["order_no"].'</h6>
                                                                <h6 class="card-text my-0">Week '.$o["week"].' - '.$o["year"].'</h6>
                                                            </div>
                                                            <div class="flex-grow-1 text-end">
                                                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapse'.$o["id_order"].'" aria-expanded="false" aria-controls="flush-collapse'.$o["id_order"].'"></button>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3 accordion-collapse collapse" id="flush-collapse'.$o["id_order"].'">';

                                                            $varieties = explode(',', $o["varieties"]);

                                                            foreach ($varieties as $key => $value) {
                                                                $this->html .= '<div class="badge m-1 bg-warning bg-opacity-25 text-dark">'.$value.'</div>';
                                                            }

                                                        $this->html .= '</div>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                                        <div class="text-start pe-3">
                                                            <h6 class="mb-0 fs-0-7 fw-semibold"><i class="bi bi-briefcase-fill me-1"></i>'.substr($o["sec_cust"], 0, 50).'</h6>
                                                        </div>
                                                        <div class="dropdown dropend">
                                                            <button type="button" class="btn btn-sm '.($visitDay->format("H:i") != "00:00" ? "btn-success" : "btn-outline-success").' dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                                                <i class="bi bi-clock me-1"></i>'.$visitDay->format("H:i").'
                                                            </button>
                                                            <form class="dropdown-menu p-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Date</label>
                                                                    <input type="date" class="form-control" value="'.$day.'">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Time</label>
                                                                    <input type="time" class="form-control" value="'.$visitDay->format("H:i").'" type="button">
                                                                </div>
                                                                <div class="mb-3 d-none">
                                                                    <label class="form-label">Notify</label><br>
                                                                    <div class="d-flex align-items-end">
                                                                        <input type="number" class="form-control" min="1" step="1" max="100" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="'.$o["notify"].'" style="width: 60px!important;">
                                                                        <span class="fs-0-8 ms-2">hours before</span>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-primary" onclick="updateOrderVisitDayForm(this, '.$o["id_order"].')">Update</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            ';
                                        }

                                        $this->html .= '</td>';

                                    }

                                $this->html .= '</tr>
                            </tbody>
                        </table>
                    ';

                    $this->arrResponse = array(
                        'status' => true, 
                        'res' => $this->html
                    );
                }else{
                    $this->arrResponse = array(
                        'status' => false, 
                        'res' => 'No data!'
                    );
                }
            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'Parameter fail!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        public function updateOrderVisitDay()
        {
            if ($_POST AND isset($_POST["idOrder"]) AND isset($_POST["date"]) AND isset($_POST["time"]) AND isset($_POST["notify"]) AND $_POST["idOrder"] AND $_POST["date"] AND $_POST["time"] AND $_POST["notify"]) {

                $exist = $this->model->getOrderVisitDay($_POST["idOrder"]);

                $newDate = $_POST["date"]." ".$_POST["time"];

                if (empty($exist)) {

                    if ($this->model->setOrderVisitDay($_POST["idOrder"], $newDate, $_POST["notify"])) {
                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update successfull'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'Update date failed!'
                        );
                    }

                }else{

                    if ($this->model->updateOrderVisitDay($exist["id"], $newDate, $_POST["notify"])) {
                        $this->arrResponse = array(
                            'status' => true, 
                            'res' => 'Update successfull'
                        );
                    }else{
                        $this->arrResponse = array(
                            'status' => false, 
                            'res' => 'Update date failed!'
                        );
                    }
                }

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data!'
                );
            }

            echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);
        }

        /* public function updateVisitDays()
        {

            $response = $this->model->getAllOrders();

            //dep($response);

            if (!empty($response)) {
                foreach ($response as $k) {
                    //* GET DATE VISIT--------------------------------------------------------------------------

                    $year = $k["year"];
                    $week = $k["week"];

                    //Obtener semanas del ano
                    $date = new DateTime;
                    $date->setISODate($year, 53);
                    $weeks = $date->format("W") === "53" ? 53 : 52;
                    //////////////////////////////////////////////////

                    $cycle = ($k["destination"] == "BOG") ? 14 : 12 ;

                    $week = $week + $cycle;
                    if ($week > $weeks) {
                        $week = $week - $weeks;
                        $year++;
                    }

                    $week = ($week > 9) ? $week : "0".$week ;

                    $lunes = date('Y-m-d', strtotime("Y".$year."W".$week."1"));

                    $this->model->updateOrderVisitDay2($k["id"], $lunes);

                    //* GET DATE VISIT--------------------------------------------------------------------------
                }
            }

        } */

        //TODO Calendar --------------------------------------------------------------------

        //TODO Test optimized images
        public function optimizedImages($params){
            
            $params = explode(',', $params);

            $week1 = explode('-W', $params[0]);
            $week2 = explode('-W', $params[1]);

            $data = $this->model->getEvaluatedImages($week1[0], $week1[1], $week2[0], $week2[1]);

            if (!empty($data)) {

                foreach ($data as $k) {
                    $image = imageQuality('uploads/'.$k["value"]);

                    // Guardar la imagen en el servidor
                    file_put_contents('uploads/optimized/'.$k["value"], base64_decode($image));

                    /*echo '
                        <img src="'.base_url().'/uploads/optimized/'.$k["value"].'" height="100" alt="'.$k["value"].'">
                    ';*/
                }

            }

        }

    }