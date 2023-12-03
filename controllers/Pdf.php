<?php 

    require_once "vendor/autoload.php";

    class Pdf{

        public $html = "";

        public function report(){
            ini_set('pcre.backtrack_limit', 10000000);
            // Recibir los datos enviados desde el cliente
            $data = json_decode(file_get_contents('php://input'), true);

            $week1 = explode('-W', $data['week1']);
            $week2 = explode('-W', $data['week2']);

            // Generar el PDF
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->SetHeader('Danapp');
            $mpdf->SetFooter('{DATE j-m-Y}||{PAGENO}');

            //$mpdf->AddPage('L');
            $this->html .= '
                <link rel="stylesheet" href="'.media().'/css/bootstrap.css">
                <link rel="stylesheet" href="'.media().'/css/style.css">';

            if (!empty($data['filters'])) {
                $this->html .= '
                    <table class="table table-bordered text-center fs-0-8">
                        <tr><th class="text-center" colspan="'.($data['filters']['search'] ? '6' : '5').'" style="background: #01633b; color:white;">Filters</th></tr>
                        <tr><th class="text-center" colspan="'.($data['filters']['search'] ? '6' : '5').'">Week '.$week1[1].'/'.$week1[0].' to '.$week2[1].'/'.$week2[0].'</th></tr>
                        <tr>
                            '.($data['filters']['search'] ? '<th class="text-center"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg></th>' : '').'
                            <th class="text-center">Destinations</th>
                            <th class="text-center">Order types</th>
                            <th class="text-center">Products</th>
                            <th class="text-center">Crops</th>
                            <th class="text-center">Varieties</th>
                        </tr>
                        <tr>
                            '.($data['filters']['search'] ? '<td>'.$data['filters']['search'].'</td>' : '').'
                            <td>'.($data['filters']['destinations'] ? $data['filters']['destinations'] : 'All').'</td>
                            <td>'.($data['filters']['types'] ? $data['filters']['types'] : 'All').'</td>
                            <td>'.($data['filters']['products'] ? $data['filters']['products'] : 'All').'</td>
                            <td>'.($data['filters']['crops'] ? $data['filters']['crops'] : 'All').'</td>
                            <td>'.($data['filters']['varieties'] ? $data['filters']['varieties'] : 'All').'</td>
                        </tr>
                    </table>
                ';
            }

            $this->html .= '
                <div><img src="'.$data["image"].'"/></div><br>';

                if (!empty($data['dataChart'])) {

                    $row = 0;
                    $float = array("left", "right");
                    $floatIndex = 0;
                    $maxRow = 24;
                    $next = 0;
                    $totalTable = array(0, 0);
                    
                    foreach ($data['dataChart'] as $values) {
                        
                        if (!$row OR $row >= $maxRow) {

                            if ($row){
                                $this->html .= '
                                            </tbody>
                                        </table>
                                    </div>
                                ';

                                $floatIndex = ($floatIndex) ? 0 : 1 ;
                            }

                            if ($next < 2) $next++;
                            else {
                                $maxRow = 45;

                                $next = 1;

                                $mpdf->WriteHTML($this->html);
                                $this->html = '';
                                $mpdf->AddPage();
                                
                            }

                            $this->html .= '
                                <div style="width:'.( count($data['dataChart']) <= 30 ? '100%' : '48%' ).'; float:'.$float[$floatIndex].';">
                                    <table class="table table-bordered text-center fs-0-7">
                                        <thead>
                                            <tr>
                                                <th>Categorie</th>
                                                <th style="background: #40a0fc;"></th>
                                                <th style="background: #03e396;"></th>
                                                <th class="text-center">%</th>
                                            </tr>
                                        </thead>
                
                                        <tbody>
                            ';
                            
                            $row = 0;

                        }

                        $this->html .= '<tr>';

                        for ($k = 1; $k < count($values); $k++) { 
                            $this->html .= '
                                <td class="'.($k == 1 ? 'text-left' : '').'" style="padding-top:3px; padding-bottom:3px;">'.($k == 1 ? strlen($values[$k]) >= 33 ? substr($values[$k], 0, 33).'...' : $values[$k] : $values[$k]).'</td>
                            ';
                        }

                        $totalTable[0] += $values[2];
                        $totalTable[1] += $values[3];

                        $this->html .= '</tr>';

                        $row++;
                        
                    }

                    $this->html .= '
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center">'.$totalTable[0].'</th>
                                        <th class="text-center">'.$totalTable[1].'</th>
                                        <th class="text-center">'.($totalTable[0] != "0" ? round(($totalTable[1]/$totalTable[0]) * 100, 1) : '0').'%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    ';

                }

                //*Resume=======================================================

                if (!empty($data['dataCompareResume'])) {

                    $mpdf->WriteHTML($this->html);
                    $this->html = '';
                    //$mpdf->AddPage();

                    $this->html .= '
                        <div style="width:100%;">
                            <h4>Evaluated Resume</h4>
                            <table class="table table-bordered text-center fs-0-8">
                                <thead>
                                    <tr>
                                        <th class="text-center">Parameter</th>
                                        <th class="text-center">Selections</th>
                                    </tr>
                                </thead>
        
                                <tbody>
                    ';
                    
                    foreach ($data['dataCompareResume'] as $values) {

                        $this->html .= '<tr>';

                        for ($k = 0; $k < count($values); $k++) { 

                            $this->html .= '
                                <td style="padding-top:3px; padding-bottom:3px; vertical-align: middle;">'.$values[$k].'</td>
                            ';

                        }

                        $this->html .= '</tr>';

                    }

                    $this->html .= '
                                </tbody>
                            </table>
                        </div>
                    ';
                    
                }

                //*Resume=======================================================

                if (!empty($data['dataCompare'])) {

                    $mpdf->WriteHTML($this->html);
                    $this->html = '';
                    $mpdf->AddPage('L');

                    $this->html .= '
                        <div>
                            <h4>Evaluated varieties</h4>
                            <table class="table table-bordered fs-0-7" style="background:#01633b;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle;">Variety</th>
                                        <th class="text-center" style="vertical-align: middle;">Sec Cust</th>
                                        <th class="text-center" style="vertical-align: middle;">User</th>';

                                        if (!empty($data["parameters"])) {
                                            foreach ($data["parameters"] as $value) {

                                                $parameter = explode(",", $value);
                                                $this->html .= '<th class="text-center" style="vertical-align: middle;">'.$parameter[1].'</th>';

                                            }
                                        }

                                    $this->html .= '
                                    </tr>
                                </thead>
        
                                <tbody>
                    ';
                    
                    foreach ($data['dataCompare'] as $values) {

                        $this->html .= '<tr>';

                        for ($k = 0; $k < count($values); $k++) { 

                            $this->html .= '
                                <td class="text-center" style="padding:3px; vertical-align: middle;">'.$values[$k].'</td>
                            ';

                            if ($k == 2) $k = 6;
                        }

                        $this->html .= '</tr>';

                    }

                    $this->html .= '
                                </tbody>
                            </table>
                        </div>
                    ';
                    
                }

            $mpdf->WriteHTML($this->html);

            // Agregar la imagen al PDF
            //$mpdf->WriteBase64($datos['imagen']);

            // Devolver el PDF al cliente
            $mpdf->Output('temp/reporte.pdf');
        }

    }