<!-- Upload Data -->
<div class="modal fade" id="modal-upload-orders" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadOrdersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="uploadOrdersModalLabel">Upload Orders</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mtm-checkbox-filter">
                <form class="mb-0" action="#" id="form-upload" method="POST" enctype="multipart/form-data">
                    <input class="form-control mb-3" type="file" name="excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required id="formFile">
                    <button class="form-control btn btn-success" id="upload-btn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="upload-loading"></span>
                        <span id="upload-text">Upload</span>
                    </button>
                </form>

                <hr><div class="mt-3">
                    <div class="fs-0-8"><i class="bi bi-clock me-1"></i>Upload logs</div>
                    <div id="upload-logs"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal add parameter -->
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
                                <option value="8">Option value</option>
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
                            <label class="form-label" for="parameter-all">All varieties</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="parameter-all" id="parameter-all">
                                <label class="form-check-label" for="parameter-all"></label>
                            </div>
                        </div>

                        <div class="col-md-4"">
                            <label class="form-label" for="parameter-required">Required</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="parameter-required" id="parameter-required">
                                <label class="form-check-label" for="parameter-required"></label>
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
                            <div class="mt-4"><label class="form-label">
                                Options
                                <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" type="button" style="width:20px; height:20px;" onclick="addOptionParameter()">
                                    <i class="bi bi-plus fw-bold"></i>
                                </button></label>
                            </div>
                            <hr class="mt-1">
                            <div class="row overflow-auto py-1" id="parameter-options" style="max-height:15vh;">
                            </div>
                        </div>
                        
                        <div class="mt-4"><p class="form-label">Assigned crops</p></div>
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

<!-- Modal add user -->
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
                            <input type="text" class="form-control" name="user-name" id="user-name" placeholder="@user" autocomplete="off" required>
                        </div>
                        <div class="col-md-2">
                            <label for="user-new-password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="user-new-password" autocomplete="off" id="user-new-password">
                        </div>
                        <div class="d-none">
                            <input type="password" class="form-control" name="user-password" id="user-password" autocomplete="off" readonly required>
                        </div>

                        <div class="col-md-2">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" autocomplete="true" required>
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
                        
                        <div class="mt-4"><p class="form-label">Assigned sec cust</p></div>
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

<!-- Modal add customer -->
<div class="modal fade" id="modalAddCustomer" tabindex="-1" aria-labelledby="modalAddCustomerLabel" aria-hidden="true">
    <form action="#" id="form-customer" type="POST">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAddCustomerLabel">Add customer</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="d-none">
                            <input type="number" id="customer-id" name="customer-id" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="customer-file">Logo</label>
                            <div class="w-100 img-thumbnail d-flex justify-content-center align-items-center" style="height: 150px; background-size: cover; background-position: center;" id="customer-image">
                                <i class="bi bi-card-image display-1"></i>
                            </div>
                            <input class="form-control mt-1" type="file" name="customer-file" id="customer-file">
                            <input type="text" class="d-none" name="customer-file-path" id="customer-file-path" value="">
                        </div>

                        <div class="col-md-9 ps-md-5">
                            <div class="row">
                                
                                <div class="col-md-4 mb-3">
                                    <label for="customer-number" class="form-label">Customer Number</label>
                                    <input type="number" class="form-control" name="customer-number" id="customer-number" min="1" required>
                                </div>
                                
                                <div>
                                    <label for="customer-name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="customer-name" id="customer-name" required>
                                </div>

                            </div>
                            
                        </div>

                        <div class="mt-5">
                            <label class="form-label">Sec customers
                                <button class="btn btn-sm btn-primary rounded-circle p-0 ms-1" type="button" style="width:20px; height:20px;" onclick="addSecCustomer()">
                                    <i class="bi bi-plus fw-bold"></i>
                                </button>
                            </label>
                        </div>
                        <hr class="mt-1">
                        <div class="row overflow-auto m-0 py-1" id="customer-sec-cust" style="max-height:15vh;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="customer-btn">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal add event -->
<div class="modal fade" id="modalAddEvent" tabindex="-1" aria-labelledby="modalAddEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form action="#" id="form-event" type="POST" accept="image/*" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalAddEventLabel">Add event</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="d-none">
                            <input type="number" id="event-id" name="event-id" readonly>
                        </div>

                        <div class="col-md-4">
                            <label for="event-name" class="form-label">Image</label>
                            <div class="w-100 img-thumbnail d-flex justify-content-center align-items-center" style="height: 200px; background-size: cover; background-position: center;" id="event-image"> <!-- background-image: url(<?= base_url() ?>/uploads/events/imagen.png); -->
                                <i class="bi bi-card-image display-1"></i>
                            </div>
                            <input class="form-control mt-1" type="file" name="event-file" id="event-file">
                            <input type="text" class="d-none" name="event-file-path" id="event-file-path" value="">
                        </div>

                        <div class="col-md-8 ps-md-5">
                            <div class="row">

                                <div class="mb-3">
                                    <label for="event-name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="event-name" id="event-name" maxlength="30" required>
                                </div>

                                <div class="col-auto mb-3">
                                    <label for="event-start" class="form-label">Start week</label>
                                    <select class="form-select w-auto" name="event-start" id="event-start">
                                        <option value="">Choose</option>
                                        <?php 
                                            for ($w=1; $w <= 53 ; $w++) { 
                                                echo '
                                                    <option value="'.$w.'">'.$w.'</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-auto mb-3">
                                    <label for="event-end" class="form-label">End week</label>
                                    <select class="form-select w-auto" name="event-end" id="event-end">
                                        <option value="">Choose</option>
                                        <?php 
                                            for ($w=1; $w <= 53 ; $w++) { 
                                                echo '
                                                    <option value="'.$w.'">'.$w.'</option>
                                                ';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="event-state" class="form-label">State</label>
                                    <select class="form-select w-50" name="event-state" id="event-state" required>
                                        <option value="1">Enabled</option>
                                        <option value="0">Disabled</option>
                                    </select>
                                </div>

                            </div>
                            
                        </div>

                        <div class="col-md-12 pt-md-5">
                            <label for="event-description" class="form-label">Description</label>
                            <textarea class="form-control" name="event-description" id="event-description" cols="30" rows="5" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="event-btn">Save changes</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Modal view event -->
<div class="modal fade" id="modalViewEvent" tabindex="-1" aria-labelledby="modalViewEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalViewEventLabel">Event title</h1>

                <form action="#" class="m-0" id="form-event-add-year">
                    <input class="d-none" type="number" name="event-add-year-id" id="event-add-year-id" required readonly>
                    <div class="input-group w-auto ms-3">
                        <select class="form-select form-select-sm" name="event-add-year-year" id="event-add-year-year"></select>
                        <button class="btn btn-primary" id="event-add-year-btn">Add year</button>
                    </div>
                </form>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" id="event-years">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal view event map -->
<div class="modal fade" id="modalViewEventMap" tabindex="-1" aria-labelledby="modalViewEventMapLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen p-2 modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-2">
            
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalViewEventMapLabel">Event title</h1>

                <button type="button" class="btn-close" data-bs-target="#modalViewEvent" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-1 rounded-3">
                            <input type="file" class="form-control" id="upload-map">
                        </div>
                        <div class="ms-2" id="map-issues"></div>
                    </div>

                    <div class="dropdown" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="More">
                        <button class="btn btn-light rounded-pill p-2 shadow-sm" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu position-fixed">
                            <li>
                                <div class="dropdown-item-text">
                                    <div class="form-check">
                                        <input class="form-check-input cursor-select" type="checkbox" name="editModeMap" id="editModeMap">
                                        <label class="form-check-label cursor-select" for="editModeMap">
                                            Edit mode
                                        </label>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-2"></li>
                            <li><a class="dropdown-item" href="http://macbook-pro.local/danapp-web/Format-event.xlsx" target="_blank"><i class="bi bi-file-earmark-excel fs-1-3"></i>Download format</a></li>
                            <li><a id="generate-qr-btn" class="dropdown-item" href="" target="_blank"><i class="bi bi-qr-code fs-1-3"></i>Generate Qr Codes</a></li>
                        </ul>
                    </div>

                </div>

                <div class="table-responsive mt-3 p-1" id="event-year-map">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-compare-varieties" tabindex="-1" aria-labelledby="compareVarietiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="compareVarietiesModalLabel">Varieties</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- <div class="d-none">
                    <input type="text" placeholder="Buscar...">
                </div> -->
               <!--  <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="compare-varieties-check-all" checked>
                        <label class="form-check-label" for="compare-varieties-check-all">
                            All
                        </label>
                    </div>
                </div> -->
                <div class="d-flex flex-wrap mtm-checkbox-filter" id="compare-varieties"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="varieties-compare-apply" data-bs-dismiss="modal">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-compare-parameters" tabindex="-1" aria-labelledby="compareParametersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="compareParametersModalLabel">Parameters</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="compare-parameters-check-all">
                        <label class="form-check-label" for="compare-parameters-check-all">
                            All
                        </label>
                    </div>
                </div> -->
                <div class="d-flex flex-wrap mtm-checkbox-filter" id="compare-parameters"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="parameters-compare-apply" data-bs-dismiss="modal">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Images full -->
<div class="modal fade" id="modalImage" tabindex="-1" aria-labelledby="modalImageLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content bg-dark">
            <div class="modal-header text-white">
                <h1 class="modal-title fs-5" id="modalImageLabel">Modal title</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="carouselExampleCaptions" class="carousel slide">

                    <div class="carousel-indicators" id="images-compare-indicators"></div>
                    <div class="carousel-inner" id="images-compare">
                        
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>