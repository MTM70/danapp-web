<script>
    const base_url = "<?= base_url(); ?>";
</script>

<!DOCTYPE html>
<html lang="en">

    <?= get_view("header", $data) ?>

    <body>

        <?= get_view("top_bar") ?>

        <?= get_view("nav") ?>

        <main id="main" class="main">

            <div class="pagetitle">
                <h1>Dashboard</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row" style="height: 75vh;">
                    <div class="col-lg-6">

                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title">Upload Orders</h5><hr>
                                <form class="mb-0" action="#" id="form-upload" method="POST" enctype="multipart/form-data">
                                    <input class="form-control mb-3" type="file" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required id="formFile">
                                    <button class="form-control btn btn-success" id="upload-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="upload-loading"></span>
                                        <span id="upload-text">Upload</span>
                                    </button>
                                </form>
                                
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Download data</h5><hr>
                                <form class="mb-0" action="#" id="form-download" method="POST">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="week-in">From</label>
                                            <input type="week" name="from" id="week-from" required class="form-control">
                                        </div>
                                        <div class="col">
                                            <label for="week-end">To</label>
                                            <input type="week" name="to" id="week-to" required class="form-control">
                                        </div>
                                        
                                    </div>
                                    <button class="form-control btn btn-success" id="download-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="download-loading"></span>
                                        <span id="download-text">Download</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <?= get_view("footer2", $data) ?>

        </main><!-- End #main -->

        <main id="main-parameters" class="main" style="display:none;">

            <div class="pagetitle d-flex justify-content-between align-items-center pe-3">
                <div>
                    <h1>Parameters</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Parameters</li>
                        </ol>
                    </nav>
                </div>
                <div class="spinner-border spinner-border-sm text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col">

                        <div class="card">
                            <div class="card-body p-2" id="parameters"></div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main -->

        <main id="main-users" class="main" style="display:none;">

            <div class="pagetitle d-flex justify-content-between align-items-center pe-3">
                <div>
                    <h1>Users</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>
                    </nav>
                </div>
                <div class="spinner-border spinner-border-sm text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col">

                        <div class="card">
                            <div class="card-body p-2" id="users"></div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main -->

    </body>

</html>