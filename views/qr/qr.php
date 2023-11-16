<script>
    const base_url = "<?= base_url(); ?>";
    const idEvent = "<?= explode('/', $_GET['url'])[3]; ?>";
    const year = "<?= explode('/', $_GET['url'])[4]; ?>";
</script>

<!DOCTYPE html>
<html lang="en">

    <?= get_view("header", $data) ?>

    <body>

        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center p-4">
                <div class="row">
                    <!-- <div class="col-auto">
                        <label for="">Event</label>
                        <select class="form-select" name="" id="">
                            <option value="">Choose...</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <label for="">Year</label>
                        <select class="form-select" name="" id="">
                            <option value="">Choose...</option>
                        </select>
                    </div> -->
                    <p class="display-6 fs-1-3 fw-semibold m-0"><i class="bi bi-calendar-event text-success"></i> <?= explode('/', $_GET['url'])[5]; ?></p>
                </div>
                <div class="">
                    <img src="<?= media() ?>/img/logo.png" alt="" width="100">
                </div>
            </div>

            <div id="cont-qr">

            </div>

        </div>
    </body>

    <?= get_view("footer", $data) ?>

</html>