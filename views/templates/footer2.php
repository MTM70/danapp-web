<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="<?= media() ?>/vendor/apexcharts/apexcharts.min.js"></script>
<script src="<?= media() ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= media() ?>/vendor/chart.js/chart.umd.js"></script>
<script src="<?= media() ?>/vendor/echarts/echarts.min.js"></script>
<script src="<?= media() ?>/vendor/quill/quill.min.js"></script>
<script src="<?= media() ?>/vendor/simple-datatables/simple-datatables.js"></script>
<script src="<?= media() ?>/vendor/tinymce/tinymce.min.js"></script>
<script src="<?= media() ?>/vendor/php-email-form/validate.js"></script>

<script src="<?= media() ?>/js/jquery-3.5.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>

<?php
    foreach ($data['page_js'] as $key => $value) {
        echo '<script src="'.media().'/js/'.$value.'?ver='.JS.'"></script>';
    }
?>

<!-- Template Main JS File -->
<script src="<?= media() ?>/js/main.js"></script>