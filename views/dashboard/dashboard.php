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
                                            <input type="week" name="from" id="week-from" min="2023-W01" max="<?= date('Y').'-W'.date('W'); ?>" value="<?= date('Y').'-W'.date('W'); ?>" required class="form-control">
                                        </div>
                                        <div class="col">
                                            <label for="week-end">To</label>
                                            <input type="week" name="to" id="week-to" required class="form-control" min="2023-W01" max="<?= date('Y').'-W'.date('W'); ?>" value="<?= date('Y').'-W'.date('W'); ?>">
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

        <main id="main-events" class="main" style="display:none;">

            <div class="pagetitle d-flex justify-content-between align-items-center pe-3">
                <div>
                    <h1>Events
                        <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" style="width:20px; height:20px;" data-bs-toggle="modal" data-bs-target="#modalAddEvent" onclick="openModalEvent()">
                            <i class="bi bi-plus fw-bold"></i>
                        </button>
                    </h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Events</li>
                        </ol>
                    </nav>
                </div>
                <div class="spinner-border spinner-border-sm text-success d-none" role="status" id="events-loading">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col">

                        <div class="card">
                            <div class="card-body p-5" id="events"></div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main events -->

        <main id="main-parameters" class="main" style="display:none;">

            <div class="pagetitle d-flex justify-content-between align-items-center pe-3">
                <div>
                    <h1>Parameters
                        <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" style="width:20px; height:20px;" data-bs-toggle="modal" data-bs-target="#modalAddParamter" onclick="openModalParameter()">
                            <i class="bi bi-plus fw-bold"></i>
                        </button>
                    </h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Parameters</li>
                        </ol>
                    </nav>
                </div>
                <div class="spinner-border spinner-border-sm text-success d-none" role="status" id="parameters-loading">
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
                    <h1>Users
                        <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" style="width:20px; height:20px;" data-bs-toggle="modal" data-bs-target="#modalAddUser" onclick="openModalUser()">
                            <i class="bi bi-plus fw-bold"></i>
                        </button>
                    </h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>
                    </nav>
                </div>
                <div class="spinner-border spinner-border-sm text-success d-none" role="status" id="users-loading">
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

        <main id="main-calendar" class="main" style="display:none;">

            <div class="pagetitle d-flex justify-content-between align-items-center pe-3">
                <div>
                    <h1>Calendar</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Calendar</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center">
                    <div>
                        <div class="spinner-border spinner-border-sm text-success me-3 d-none" role="status" id="calendar-loading">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <input type="week" id="calendar-week" class="form-control" value="<?= date('Y').'-W'.date('W'); ?>">

                    <button class="btn btn-light rounded-circle p-2 h-auto w-auto ms-3 position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" style="width: 60px; height: 60px;">
                        <i class="bi bi-funnel"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" id="calendar-filters-notify" style="margin-top: 7; margin-left: -7;">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </button>
                    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="offcanvasRightLabel"><i class="bi bi-funnel-fill me-1"></i>Filters</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body" id="calendar-filters">
                        </div>
                        <div class="offcanvas-bottom p-2">
                            <button class="btn btn-warning form-control" id="calendar-filters-btn" onclick="calendarClearFilter()">Clear filters</button>
                        </div>
                    </div>

                    <button class="btn btn-light rounded-circle p-2 h-auto w-auto ms-3" style="width: 60px; height: 60px;" id="export" onclick="exportPDF('calendar')"><i class="bi bi-file-earmark-pdf"></i></button>
                </div>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col">

                        <div class="card">
                            <div class="card-body p-2" id="calendar"></div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main -->

        <?= get_view("modals", $data) ?>

    </body>

</html>