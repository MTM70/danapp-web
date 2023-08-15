<?php

class Qr extends Controllers
{

    public $html = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function event($params)
    {

        if ($params != "74e4bc032c5f9ea4d0130ef131c4e802") {
            exit("Token invalid!");
        }

        $response = $this->model->getData();

        if (!empty($response)) {
            
            $this->html .= '
                <link href="'.media().'/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
                <link href="'.media().'/css/style.css?ver='.STYLES.'" rel="stylesheet">

                <div class="container-fluid">

                    <div class="d-flex justify-content-between align-items-center p-4">
                        <div class="p-4">
                            <p class="badge fs-0-9 bg-success bg-opacity-10 m-0 text-success">Open house 2023</p>
                        </div>
                        <div class="">
                            <img src="'.media().'/img/logo.png" alt="" width="100">
                        </div>
                    </div>

                    <div class="row">
            ';

            $cg = null;

            foreach ($response as $k) {

                if ($cg != $k["crop_general"]) $this->html .= '<div class="col-12 display-6 fw-bold bg-primary bg-opacity-25 text-primary mt-5 mb-2 p-3 text-center">'.$k["crop_general"].'</div>';

                $this->html .= '
                    <div class="col-2 border text-center p-1">
                        <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl='.$k["id_variety"].','.$k["id"].','.$k["position"].'&choe=UTF-8" alt="">
                        <h6 class="mt-1"><b>'.$k["variety"].'</b> - ('.$k["greenhouse"].' | <b class="text-danger">#'.$k["position"].'</b>)</h6>
                    </div>
                ';

                $cg= $k["crop_general"];
            }

            $this->html .= '</div></div>';

            echo $this->html;

        }

    }

}