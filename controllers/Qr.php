<?php

class Qr extends Controllers
{

    public $html = '';
    public $arrResponse = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function qr($params)
    {
        $params = explode(",", $params);

        if ($params[0] != "74e4bc032c5f9ea4d0130ef131c4e802") {
            exit("Token invalid!");
        }

        $data['page_title'] = "Qr";
        $data['page_js'] = array("qr.js");

        $this->views->getView($this,"qr", $data);
    }

    public function event()
    {
        if (isset($_GET["id_event"]) AND $_GET["id_event"]) {
            
            $response = $this->model->getData($_GET["id_event"]);

            if (!empty($response)) {
                
                $this->html .= '
                    <div class="row justify-content-center mb-5">
                ';

                $cg = null;

                foreach ($response as $k) {

                    if ($k["crop"] == 10) continue;

                    if ($cg != $k["crop_general"]) $this->html .= '<div class="col-12 fs-1-3 fw-semibold bg-primary bg-opacity-10 text-primary mt-2 mb-2 p-3 text-center">'.$k["crop_general"].'</div>';

                    $this->html .= '
                        <div class="border text-center p-1 bg-white" id="cont'.$k["id"].'" onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" style="width:270px; height:300px;">
                            <div class="position-absolute btn-download d-none" style="margin-left: -3px; margin-top: -3px;">
                                <button class="btn btn-primary" onclick="convertirADiv('."'".'cont'.$k["id"]."'".', '.$k["id"].')" title="Download"><i class="bi bi-download"></i></button>
                            </div>
                            <img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl='.$k["id_variety"].','.$k["id"].','.$k["position"].'&choe=UTF-8&chld=L|2" alt="">
                            <h6 class="mt-1"><b>'.$k["variety"].'</b> - ('.$k["greenhouse"].' | <b class="text-danger">#'.$k["position"].'</b>)</h6>
                        </div>
                    ';

                    $cg= $k["crop_general"];
                }

                $cg = null;

                foreach ($response as $k) {

                    if ($k["crop"] != 10) continue;

                    if ($cg != $k["crop_general"]) $this->html .= '<div class="col-12 fs-1-3 fw-semibold bg-primary bg-opacity-10 text-primary mt-2 mb-2 p-3 text-center">Roses</div>';

                    $this->html .= '
                        <div class="border text-center p-1 bg-white" id="cont'.$k["id"].'" onmouseover="mouseOver(this)" onmouseout="mouseOut(this)" style="width:270px; height:300px;">
                            <div class="position-absolute btn-download d-none" style="margin-left: -3px; margin-top: -3px;">
                                <button class="btn btn-primary" onclick="convertirADiv('."'".'cont'.$k["id"]."'".', '.$k["id"].')" title="Download"><i class="bi bi-download"></i></button>
                            </div>
                            <img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl='.$k["id_variety"].','.$k["id"].','.$k["position"].'&choe=UTF-8&chld=L|2" alt="">
                            <h6 class="mt-1"><b>'.$k["variety"].'</b> - ('.$k["greenhouse"].' | <b class="text-danger">#'.$k["position"].'</b>)</h6>
                        </div>
                    ';

                    $cg= $k["crop_general"];
                }

                $this->html .= '</div>';

                $this->arrResponse = array(
                    'status' => true, 
                    'res' => $this->html
                );

            }else{
                $this->arrResponse = array(
                    'status' => false, 
                    'res' => 'No data found!'
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

    public function saveQrToServer()
    {

        if (isset($_POST["urlApi"]) AND $_POST["urlApi"] AND isset($_POST["id"]) AND $_POST["id"]) {

            $directorio = __DIR__ . "/../uploads/qr/";

            // Generar la URL de la imagen del código QR usando la API de Google Charts
            $urlImagenQR = $_POST["urlApi"];

            // Nombre del archivo en el servidor (puedes personalizarlo según tus necesidades)
            $nombreArchivo = $_POST["id"].'.png';

            // Ruta completa del archivo en el servidor
            $rutaArchivo = $directorio . $nombreArchivo;

            // Descargar la imagen y guardarla en el servidor
            file_put_contents($rutaArchivo, file_get_contents($urlImagenQR));

            $this->arrResponse = array(
                'status' => true, 
                'res' => base_url().'/uploads/qr/'.$nombreArchivo
            );

        }else{
            $this->arrResponse = array(
                'status' => false, 
                'res' => 'Url not valid!'
            );
        }

        echo json_encode($this->arrResponse, JSON_UNESCAPED_UNICODE);

    }

}