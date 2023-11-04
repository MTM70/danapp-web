<script>
    const base_url = "<?= base_url(); ?>";
</script>

<?php 
    $year = date("Y");
    $week = date("W") - 1;

    $date = new DateTime("{$year}-12-31");
    $weeks = $date->format("W");

    if ($week == 0) {
        $year--;

        $date = new DateTime("{$year}-12-31");
        $weeks = $date->format("W");

        $week = $weeks;
    }

    $week = $year.'-W'.$week;
?>

<!DOCTYPE html>
<html lang="en">

    <?= get_view("header", $data) ?>

    <body>

        <?= get_view("top_bar") ?>

        <?= get_view("nav") ?>

        <main id="main" class="main">

            <div class="pagetitle d-flex justify-content-between align-items-center">
                <div>
                    <h1>Dashboard</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </nav>
                </div>

                <div class="me-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-upload-orders"><i class="bi bi-cloud-arrow-up-fill"></i></button>
                </div>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row" style="height: 75vh;">
                    <div class="col-lg-6 d-none">

                        <div class="card">
                            <div class="card-body">

                                <!-- <h5 class="card-title">Upload Orders</h5><hr>
                                <form class="mb-0" action="#" id="form-upload" method="POST" enctype="multipart/form-data">
                                    <input class="form-control mb-3" type="file" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required id="formFile">
                                    <button class="form-control btn btn-success" id="upload-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="upload-loading"></span>
                                        <span id="upload-text">Upload</span>
                                    </button>
                                </form> -->
                                
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-12">

                        <div class="card">
                            <div class="card-header">
                                <form class="mb-0" action="#" id="form-download" method="POST">
                                    <div class="row mb-3 align-items-end">
                                        <div class="col">
                                            <label for="week-in">From</label>
                                            <input type="week" name="from" id="week-from" min="2023-W01" max="<?= date('Y').'-W'.date('W'); ?>" value="<?= $week; ?>" required class="form-control">
                                        </div>
                                        <div class="col">
                                            <label for="week-end">To</label>
                                            <input type="week" name="to" id="week-to" required class="form-control" min="2023-W01" max="<?= date('Y').'-W'.date('W'); ?>" value="<?= date('Y').'-W'.date('W'); ?>">
                                        </div>
                                        <div class="col-12 col-md-auto mt-2 mt-md-0">
                                            <button class="form-control btn btn-success" id="download-btn">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="download-loading"></span>
                                                <span id="download-text">Download</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">

                                <div class="mt-2">
                                    <nav class="d-flex align-items-center">
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button onclick="loadCharts()" class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-customers" type="button" role="tab" aria-controls="nav-customers" aria-selected="true">Customers</button>
                                            <button onclick="loadCharts()" class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-users" type="button" role="tab" aria-controls="nav-users" aria-selected="false">Users</button>
                                            <button onclick="loadCharts()" class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-crops" type="button" role="tab" aria-controls="nav-crops" aria-selected="false">Crops</button>
                                            <button onclick="loadCharts()" class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-varieties" type="button" role="tab" aria-controls="nav-varieties" aria-selected="false">Varieties</button>
                                        </div>
                                        <div>
                                            <div class="btn-group dropdown">
                                                <button class="btn btn-light rounded-circle p-2 h-auto w-auto ms-3 position-relative" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-funnel"></i>
                                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none" id="chart-filters-notify" style="margin-top: 7; margin-left: -7;">
                                                        <span class="visually-hidden">New alerts</span>
                                                    </span>
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md-start rounded-3 px-2 py-4" id="miDropdown" style="width: 600px;">
                                                    <div class="row" id="filters-chart">
                                                        <div class="col-4">
                                                            <div class="border border-dark border-opacity-10 rounded-3" style="height: 150px;">
                                                                <div>
                                                                    <p class="placeholder-glow m-2 mt-3">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="border border-dark border-opacity-10 rounded-3 overflow-auto" style="height: 150px;">
                                                                <div>
                                                                    <p class="placeholder-glow m-2 mt-3">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="border border-dark border-opacity-10 rounded-3 overflow-auto" style="height: 150px;">
                                                                <div>
                                                                    <p class="placeholder-glow m-2 mt-3">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 mt-4">
                                                            <div class="border border-dark border-opacity-10 rounded-3 overflow-auto" style="height: 150px;">
                                                                <div>
                                                                    <p class="placeholder-glow m-2 mt-3">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-8 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 mt-4">
                                                            <div class="border border-dark border-opacity-10 rounded-3 overflow-auto" style="height: 150px;">
                                                                <div>
                                                                    <p class="placeholder-glow m-2 mt-3">
                                                                        <span class="placeholder rounded-2 col-12 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-12 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-12 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-12 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-12 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                    <p class="placeholder-glow m-2">
                                                                        <span class="placeholder rounded-2 col-12 bg-dark bg-opacity-25"></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><hr>
                                                    <div class="text-center">
                                                        <button class="btn btn-primary" id="filters-chart-btn">Apply</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <i class="bi bi-funnel"></i>
                                            <i class="bi bi-airplane-fill me-2"></i>
                                            <select class="form-select form-select-sm" name="" id="">
                                                <option value="">All</option>
                                                <option value="">Bog</option>
                                                <option value="">Med</option>
                                            </select> -->
                                        </div>
                                    </nav>
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active text-center" id="nav-customers" style="height: 50vh;" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                                            <div class="d-flex justify-content-center align-items-center h-100">
                                                <i class="spinner spinner-border"></i>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="nav-users" style="height: 50vh;" role="tabpanel" aria-labelledby="nav-users-tab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="nav-crops" style="height: 50vh;" role="tabpanel" aria-labelledby="nav-crops-tab" tabindex="0">...</div>
                                        <div class="tab-pane fade" id="nav-varieties" style="height: 50vh;" role="tabpanel" aria-labelledby="nav-varieties-tab" tabindex="0">...</div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <hr>
                                    <h6 class="p-2 text-success">Compare varieties<i class="bi bi-caret-down-fill ms-1"></i></h6>
                                    <hr>

                                    <div class="row mt-4">
                                        <div class="col-6 btn btn-light m-2 border overflow-auto" id="varieties-compare-btn" style="height: 80px;">
                                            <p class="position-absolute bg-white bg-opacity-50 rounded-3 px-2" style="margin-top: -18px;">Varieties</p>
                                            <div class="py-3 h-100 d-flex flex-wrap justify-content-center align-items-center mtm-checkbox-filter" id="compare-varieties-selected">
                                                <h6>Clic here.</h6>
                                            </div>
                                        </div>

                                        <div class="col btn btn-light m-2 border overflow-auto" id="parameters-compare-btn" style="height: 80px;">
                                            <p class="position-absolute bg-white bg-opacity-50 rounded-3 px-2" style="margin-top: -18px;">Parameters</p>
                                            <div class="py-3 h-100 d-flex flex-wrap justify-content-center align-items-center mtm-checkbox-filter" id="compare-parameters-selected">
                                                <h6>Clic here.</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3" style="min-height: 57vh;">
                                        <div class="pt-1" id="compare-table">
                                            
                                            <div class="text-center mt-5">
                                                <i class="bi bi-exclamation-circle display-6"></i>
                                                <p class="mt-3">Select the items to compare!</p>
                                            </div>

                                        </div>
                                    </div>
                                    
                                </div>
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
                            <div class="card-body p-2">
                                <div class="table-responsive" id="calendar"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main -->

        <main id="main-customers" class="main" style="display:none;">

            <div class="pagetitle d-flex justify-content-between align-items-center pe-3">
                <div>
                    <h1>Customers
                        <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" style="width:20px; height:20px;" data-bs-toggle="modal" data-bs-target="#modalAddCustomer" onclick="openModalCustomer()">
                            <i class="bi bi-plus fw-bold"></i>
                        </button>
                    </h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>dashboard">Home</a></li>
                            <li class="breadcrumb-item active">Customers</li>
                        </ol>
                    </nav>
                </div>
                <div class="spinner-border spinner-border-sm text-success d-none" role="status" id="customers-loading">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div><!-- End Page Title -->

            <section class="section">
                <div class="row">
                    <div class="col">

                        <div class="card">
                            <div class="card-body p-2" id="customers"></div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main -->

        <?= get_view("modals", $data) ?>

    </body>

</html>