<a href="#" class="back-to-top d-flex align-items-center justify-content-center" style="z-index: 999;"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

<script src="<?= media() ?>/js/jquery-3.5.1.min.js"></script>

<?php
    foreach ($data['page_js'] as $key => $value) {
        echo '<script src="'.media().'/js/'.$value.'?ver='.JS.'"></script>';
    }
?>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>