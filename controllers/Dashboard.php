<?php
    require_once "vendor/autoload.php";
    require_once('controllers/Security.php');
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $data['page_js'] = array("dashboard.js");

            if ($_SESSION['idRol'] == 1) array_push($data["page_js"], "parameters.js", "users.js");

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

                $orderNoHist = null;    //Guardar ultima orden

                for ($i = 1; $i < count($data); $i++) { //Por cada dato del arreglo de datos

                    if (trim($data[$i][0]) != $orderNoHist) {   //Si la orden actual es indiferente de la orden anterior

                        $order = $this->model->getOrderByNo(trim($data[$i][0]));    //Comprobar si la orden ya existe

                        if (!$order) {  //Si no existe

                            $cust = $this->model->getCustByNo(trim($data[$i][7]));  //Comprobar si el cust existe

                            if (!$cust) {   //Si no existe se crea
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

                            $secCust = $this->model->getSecCustByNo($cust["id"], trim($data[$i][9]));   //Comprobar si el secCust existe

                            if (!$secCust) {    //Si no existe se crea
                                $data[$i][10] = (trim($data[$i][10]) != "") ? trim($data[$i][10]) : $cust["cust"];
                                $insert = $this->model->setSecCustomer($cust["id"], trim($data[$i][9]), trim($data[$i][10]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create sec cust fail!'.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else {
                                    $secCust = $this->model->getSecCustByNo($cust["id"], trim($data[$i][9]));
                                }
                            }

                            $orderType = $this->model->getOrderTypeByType(trim($data[$i][22])); //Comprobar si el tipo de orden existe

                            if (!$orderType) {  //Si no existe se crea
                                $insert = $this->model->setOrderType(trim($data[$i][22]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create order type fail!'.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $orderType = $this->model->getOrderTypeByType(trim($data[$i][22]));
                                }
                            }

                            $product = $this->model->getProductByProduct(trim($data[$i][16]));  //Comprobar si el producto existe

                            if (!$product) {    //Si no existe se crea
                                $insert = $this->model->setProduct(trim($data[$i][16]));
                                if (!$insert) {
                                    $this->arrResponse = array(
                                        'status' => false, 
                                        'res' => 'Create product fail!'.$insert
                                    );
        
                                    exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                                }else{
                                    $product = $this->model->getProductByProduct(trim($data[$i][16]));
                                }
                            }

                            //Registramos la orden
                            $insert = $this->model->setOrder(trim($data[$i][0]), $secCust["id"], $orderType["id"], $product["id"], trim($data[$i][3]), trim($data[$i][4]), trim($data[$i][14]), trim($data[$i][34]));
                            if (!$insert) {
                                $this->arrResponse = array(
                                    'status' => false, 
                                    'res' => 'Create order fail!'.$insert
                                );
    
                                exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                            }else{
                                $order = $this->model->getOrderByNo(trim($data[$i][0]));
                            }
                            
                        }
                    }
                    


                    //TODO Orden detail ----------------------------------------

                    $crop = $this->model->getCropByNo(trim($data[$i][25])); //Comprobar si el cultivo existe

                    if (!$crop) { //Si no existe se crea

                        $cropGeneral = (trim($data[$i][25]) == 20) ? 2 : 3 ;

                        $insert = $this->model->setCrop($cropGeneral, trim($data[$i][25]), trim($data[$i][26]));
                        if (!$insert) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Create crop fail!'.$insert
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }else{
                            $crop = $this->model->getCropByNo(trim($data[$i][25]));
                        }
                    }

                    $variety = $this->model->getVarietyByNo(trim($data[$i][27]));   //Comprobar si la variedad existe

                    if (!$variety) {    //Si no existe se crea
                        $insert = $this->model->setVariety($crop["id"], trim($data[$i][27]), trim($data[$i][28]));
                        if (!$insert) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Create variety fail!'.$insert
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }else{
                            $variety = $this->model->getVarietyByNo(trim($data[$i][27]));
                        }
                    }

                    $orderDetail = $this->model->getOrderDetail($order["id"], $variety["id"]);  //Comprobar si la orden detalle existe

                    if (!$orderDetail) {    //Si no existe se crea
                        $insert = $this->model->setOrderDetail($order["id"], $variety["id"], trim($data[$i][20]), floatval(str_replace(",", ".", trim($data[$i][21]))));
                        if (!$insert) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Create detail order fail!'.$insert
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                    }else{  //Si existe se actualiza la cantidad y el total precio
                        $update = $this->model->updateOrderDetail($orderDetail["id"], trim($data[$i][20]), floatval(str_replace(",", ".", trim($data[$i][21]))));
                        if (!$update) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'update detail order fail!'.$update
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                    }

                    $orderNoHist = $order["order_no"];  //Guardamos el hist de la orden
                }

                $update = $this->model->updateOrderDetailState(); //Actualizamos state a 1, para no modificar si subimos de nuevo el archivo

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

                $parameters = $this->model->getParameters();
                $data = $this->model->getDataBetweenWeeks2($year1, $week1, $year2, $week2);
                $response = $this->model->getOrdersParametersBetweenWeeks($year1, $week1, $year2, $week2);

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', 'Order number');
                $sheet->setCellValue('B1', 'Secondary customer name');
                $sheet->setCellValue('C1', 'Variety name');
                $sheet->setCellValue('D1', 'Year');
                $sheet->setCellValue('E1', 'Week');
                $sheet->setCellValue('F1', 'Customer number');
                $sheet->setCellValue('G1', 'Customer name');
                $sheet->setCellValue('H1', 'Secondary customer number');
                $sheet->setCellValue('I1', 'Order type');
                $sheet->setCellValue('J1', 'Product');
                $sheet->setCellValue('K1', 'Destination');
                $sheet->setCellValue('L1', 'Variety number');

                $c = 13;
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
                        $sheet->setCellValue('B'.$f, $k["sec_cust"]);
                        $sheet->setCellValue('C'.$f, $k["variety"]);
                        $sheet->setCellValue('D'.$f, $k["year"]);
                        $sheet->setCellValue('E'.$f, $k["week"]);
                        $sheet->setCellValue('F'.$f, $k["cust_no"]);
                        $sheet->setCellValue('G'.$f, $k["cust"]);
                        $sheet->setCellValue('H'.$f, $k["sec_cust_no"]);
                        $sheet->setCellValue('I'.$f, $k["order_type"]);
                        $sheet->setCellValue('J'.$f, $k["product"]);
                        $sheet->setCellValue('K'.$f, $k["destination"]);
                        $sheet->setCellValue('L'.$f, $k["variety_code"]);

                        $c = 13;
                        foreach ($parameters as $p) {
                            

                            foreach ($data as $d) {
                                if ($d["id_order"] == $k["id_order"] AND $d["id_variety"] == $k["id_variety"] AND $d["id_parameter"] == $p["id"]) {

                                    if ($p["type"] != 4) {
                                        $sheet->setCellValue([$c, $f], ($d["obs"] != "") ? $d["value"]." (".$d["obs"].")" : $d["value"]);
                                    }else{
                                        $sheet->getStyle([$c, $f])->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                                        $sheet->getCell([$c, $f])->getStyle()->getFont()->setUnderline(true);
                                        $sheet->setCellValue([$c, $f], ($d["obs"] != "") ? $d["value"]." (".$d["obs"].")" : $d["value"]);
                                        $sheet->getCell([$c, $f])->getHyperlink()->setUrl("https://danapp.co/uploads/".$d["value"]);
                                    }
                                    
                                    break; 
                                }
                            }

                            $c++;
                        }

                        $f++;
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(10.14);
                $sheet->getRowDimension(1)->setRowHeight(54.75);
                $sheet->getColumnDimension('B')->setWidth(21.57);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(5.57);
                $sheet->getColumnDimension('E')->setWidth(6.14);
                $sheet->getColumnDimension('F')->setWidth(9.50);
                $sheet->getColumnDimension('G')->setWidth(20.43);
                $sheet->getColumnDimension('H')->setWidth(13.2);
                //$sheet->getColumnDimension('I')->setWidth();
                $sheet->getColumnDimension('J')->setWidth(8.57);
                $sheet->getColumnDimension('K')->setWidth(12);
                $sheet->getColumnDimension('L')->setWidth(9);
                

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
                

                $spreadsheet->getActiveSheet()->setAutoFilter('A1:BA20');
                $spreadsheet->getActiveSheet()->freezePane('D2');

                
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

        //TODO Parameters --------------------------------------------------------------------
        public function loadParameters()
        {
            $response = $this->model->getParameters();

            if (!empty($response)) {

                $this->html.='
                    <table class="table table-hover" id="table-parameters" style="font-size:14px;">
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

                    $type = array("Yes/No", "Number", "Date", "Text", "Image", "Select", "Select Radio", "Switch");
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

                        if ($this->model->getParameterByNameAndType(str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Parameter exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $id = $this->model->setParameter(str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"], $_POST["parameter-category"], str_ucfirst($_POST["parameter-label"]), $_POST["parameter-position"], str_ucfirst($_POST["parameter-remark"]), $all);

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
                        AND $_POST["parameter-name"] AND $_POST["parameter-type"] AND $_POST["parameter-category"] AND $_POST["crops"] AND $_POST["parameter-position"] AND $_POST["options"]) {

                        $all = (isset($_POST["parameter-all"])) ? 1 : 0 ;

                        if ($this->model->getParameterByNameAndType(str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"], $_POST["parameter-id"])) {
                            $this->arrResponse = array(
                                'status' => false, 
                                'res' => 'Parameter exist!'
                            );

                            exit(json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $update = $this->model->updateParameter($_POST["parameter-id"], str_ucfirst($_POST["parameter-name"]), $_POST["parameter-type"], $_POST["parameter-category"], str_ucfirst($_POST["parameter-label"]), $_POST["parameter-position"], str_ucfirst($_POST["parameter-remark"]), $all, $_POST["parameter-state"]);

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
                    <table class="table table-hover" id="table-users" style="font-size:14px;">
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

    }