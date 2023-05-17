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

        <!-- Modal -->
        <div class="modal fade" id="modalAddParamter" tabindex="-1" aria-labelledby="modalAddParamterLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form action="#" id="form-parameter" type="POST">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalAddParamterLabel">Add parameter</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="d-none">
                                    <input type="number" id="parameter-id" name="parameter-id" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="parameter-name" class="form-label">Parameter name</label>
                                    <input type="text" class="form-control" name="parameter-name" id="parameter-name" required>
                                </div>
                                <div class="col-md-4">
                                    <label 
                                        for="parameter-type" 
                                        class="form-label">
                                        Type
                                        <span class="dropdown dropend">
                                            <i class="bi bi-info-circle-fill ms-1 cursor-select" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                            <div class="dropdown-menu position-fixed text-start overflow-auto p-4 h-75">
                                                <h6 class="dropdown-header px-0 text-start">Yes/No</h6>
                                                <img src='<?= media() ?>/img/helps/checkbox.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Number</h6>
                                                <img src='<?= media() ?>/img/helps/number.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Image</h6>
                                                <img src='<?= media() ?>/img/helps/image.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Date</h6>
                                                <img src='<?= media() ?>/img/helps/date.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Text</h6>
                                                <img src='<?= media() ?>/img/helps/text.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Selection</h6>
                                                <img src='<?= media() ?>/img/helps/select.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Selection radio</h6>
                                                <img src='<?= media() ?>/img/helps/radio.png' width='280' alt=''><hr>

                                                <h6 class="dropdown-header px-0 text-start">Switch</h6>
                                                <img src='<?= media() ?>/img/helps/switch.png' width='280' alt=''>
                                            </div>
                                        </span>
                                    </label>
                                    
                                    <select class="form-select" name="parameter-type" id="parameter-type" required>
                                        <option value="">Choose...</option>
                                        <option value="0">Yes/No</option>
                                        <option value="1">Number</option>
                                        <option value="4">Image</option>
                                        <option value="2">Date</option>
                                        <option value="3">Text</option>
                                        <option value="5">Selection</option>
                                        <option value="6">Selection radio</option>
                                        <option value="7">Switch</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="parameter-category" class="form-label">Category</label>
                                    <select class="form-select" name="parameter-category" id="parameter-category" required>
                                        <option value="">Choose...</option>
                                        <option value="1">Client</option>
                                        <option value="2">Technical</option>
                                        <option value="3">All</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-none parameter-more">
                                    <label for="parameter-label" class="form-label">Label</label>
                                    <input type="text" class="form-control" name="parameter-label" id="parameter-label">
                                </div>
                                <div class="col-md-4 d-none parameter-more">
                                    <label for="parameter-remark" class="form-label">Remark</label>
                                    <input type="text" class="form-control" name="parameter-remark" id="parameter-remark">
                                </div>
                                <div class="col-md-4">
                                    <label for="parameter-position" class="form-label">Position</label>
                                    <select class="form-select" name="parameter-position" id="parameter-position" required>
                                        <option value="">Choose...</option>
                                        <option value="1">Top</option>
                                        <option value="2">Middle</option>
                                        <option value="3">bottom</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-none" id="parameter-all-cont">
                                    <label for="inputState" class="form-label">All varieties</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="parameter-all" id="parameter-all">
                                        <label class="form-check-label" for="parameter-all"></label>
                                    </div>
                                </div>

                                <div class="col-md-2" id="parameter-state-cont">
                                    <label for="parameter-state" class="form-label">State</label>
                                    <select class="form-select" name="parameter-state" id="parameter-state" required>
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                </div>

                                <div class="d-none" id="parameter-options-cont">
                                    <div class="mt-4"><label for="" class="form-label">
                                        Options
                                        <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" type="button" style="width:20px; height:20px;" onclick="addOptionParameter()">
                                            <i class="bi bi-plus fw-bold"></i>
                                        </button></label>
                                    </div>
                                    <hr class="mt-1">
                                    <div class="row overflow-auto py-1" id="parameter-options" style="max-height:15vh;">
                                    </div>
                                </div>
                                
                                <div class="mt-4"><label for="" class="form-label">Assigned crops</label></div>
                                <hr class="mt-1">
                                <div class="row overflow-auto px-4" id="parameter-crops" style="max-height:35vh;">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="parameter-btn">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalAddUser" tabindex="-1" aria-labelledby="modalAddUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen modal-dialog-scrollable p-2">
                <form action="#" id="form-user" type="POST">
                    <div class="modal-content rounded-3">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalAddUserLabel">Add user</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="d-none">
                                    <input type="number" id="user-id" name="user-id" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label for="user-name" class="form-label">User</label>
                                    <input type="text" class="form-control" name="user-name" id="user-name" placeholder="@user" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="user-new-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="user-new-password" id="user-new-password">
                                </div>
                                <div class="d-none">
                                    <input type="password" class="form-control" name="user-password" id="user-password" readonly required>
                                </div>

                                <div class="col-md-2">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="user-last-name" class="form-label">Last name</label>
                                    <input type="text" class="form-control" name="user-last-name" id="user-last-name" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="user-rol" class="form-label">Rol</label>
                                    <select class="form-select" name="user-rol" id="user-rol" required>
                                        <option value="">Choose...</option>
                                    </select>
                                </div>

                                <div class="col-md-1" id="user-state-cont">
                                    <label for="user-state" class="form-label">State</label>
                                    <select class="form-select" name="user-state" id="user-state" required>
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                </div>
                                
                                <div class="mt-4"><label for="" class="form-label">Assigned sec cust</label></div>
                                <hr class="mt-1">
                                <div class="row" id="user-sec-custs"></div>
                            </div>
                           
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="user-btn">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </body>

</html>