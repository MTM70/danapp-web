let stateCalendar = false;
let stateParameters = false;
let stateEvents = false;
let stateUsers = false;
let stateCustomers = false;

//* Charts
let stateChartCusts = false;
let stateChartUsers = false;
let stateChartCrops = false;
let stateChartVarieties = false;

$(document).ready(async function() {

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    //TODO upload data
    $("#form-upload").on("submit", function(e){
        e.preventDefault();

        var formData = new FormData(document.getElementById("form-upload"));

        $.ajax({
            url: base_url+"/Dashboard/uploadFile",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#upload-btn').addClass('disabled');
                $('#upload-text').html('Loading...');
                $('#upload-loading').removeClass('d-none');
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#upload-btn').removeClass('disabled');
                $('#upload-text').html('Upload');
                $('#upload-loading').addClass('d-none');
            },
        })
        .done(function(response){
            //console.log(response);
            const objData = JSON.parse(response);

            if(objData.status == true){
                alert(objData.res);
            }else{
                alert(objData.res);
            }

            $('#upload-btn').removeClass('disabled');
            $('#upload-text').html('Upload');
            $('#upload-loading').addClass('d-none');
        });
    });

    document.querySelector('#week-from').addEventListener('change', async function() {
        setStateChart(0, false);

        await loadCharts();
        await loadVarietiesCompare();
        await loadParametersCompare();
        await loadTableCompare(false);
        await loadFiltersChart();
    });

    document.querySelector('#week-to').addEventListener('change', async function() {
        setStateChart(0, false);

        await loadCharts();
        await loadVarietiesCompare();
        await loadParametersCompare();
        await loadTableCompare(false);
        await loadFiltersChart();
    });

    document.querySelector('#filters-chart-btn').addEventListener('click', function() {
        setStateChart(0, false);

        // Obtén una referencia al elemento del dropdown que deseas cerrar
        const dropdown = document.getElementById("miDropdown"); // Reemplaza "miDropdown" con el ID de tu dropdown
        // Cierra el dropdown
        new bootstrap.Dropdown(dropdown).hide();

        loadCharts();
    });

    document.querySelector('#varieties-compare-btn').addEventListener('click', function() {
        toogleModal('modal-compare-varieties', true)
    });

    document.querySelector('#parameters-compare-btn').addEventListener('click', function() {
        toogleModal('modal-compare-parameters', true)
    });

    document.querySelector('#varieties-compare-apply').addEventListener('click', function() {
        showSelectionCompare('compare-varieties', 'compare-varieties-selected');
    });

    document.querySelector('#parameters-compare-apply').addEventListener('click', function() {
        showSelectionCompare('compare-parameters', 'compare-parameters-selected');
    });

    document.querySelector('.toggle-sidebar-btn').addEventListener('click', function() {
        resizeTable();
        setStateChart(0, false);
    });


    //TODO download data
    $("#form-download").on("submit", function(e){
        e.preventDefault();

        if (document.querySelector('#week-from').value > document.querySelector('#week-to').value) {
            alert('start date greater than end date');
            return;
        }

        window.open(base_url+'/Dashboard/downloadData2/'+$('#week-from').val()+'/'+$('#week-to').val(), '_blank');
    });

    window.addEventListener('resize', () => {
        setStateChart(0, false);
        resizeTable();
    });

    await loadCharts();
    await loadVarietiesCompare();
    await loadParametersCompare();
    await loadFiltersChart();
});

const loadCharts = async () => {

    let buttons = Array.from(document.querySelectorAll('#nav-tab button'));
    let btnActive = buttons.find(button => button.classList.contains('active'));

    let index = buttons.indexOf(btnActive);
    let id = btnActive.getAttribute('data-bs-target');

    if (getStateChart(index)) return;

    const filters = await getFiltersChartSelected();

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadCharts",
            type: 'GET',
            data: { 'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value, 'type' : index, 'filters' : JSON.stringify(filters) },
            cache: false,

            beforeSend: function() {
                document.querySelector(id).innerHTML = `
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="spinner spinner-border"></i>
                    </div>
                `;
            },
    
            error:  function(xhr) {
                alert(xhr.status);
                resolve(false);
            },

            success:  function(response) {

                //console.log(response);

                document.querySelector(id).innerHTML = null;

                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){

                        /* const categories = objData.res.map(item => (item.categorie.length > 20) ? `${item.categorie.substring(0, 20)}...` : item.categorie);
                        const data = objData.res.map(item => item.count); */

                        const data = [];

                        objData.res.forEach(element => {

                            if (index == 2 && filters.crops.length){

                                let isFilter = false;

                                filters.crops.forEach(crop => {
                                    if (crop.split(',')[1] == element.categorie) {
                                        isFilter = true;
                                    }
                                });

                                if (!isFilter) return;
                                
                            }

                            if (index == 3 && filters.varieties.length){

                                let isFilter = false;

                                filters.varieties.forEach(variety => {
                                    if (variety.split(',')[1] == element.categorie) {
                                        isFilter = true;
                                    }
                                });

                                if (!isFilter) return;
                                
                            }

                            let dataPoint = {
                                x: element.categorie,
                                y: element.count,
                                goals: []
                            };
                        
                            if (element.goal !== undefined) {
                                dataPoint.goals.push({
                                    name: 'Uploads',
                                    value: element.goal,
                                    strokeHeight: 5,
                                    strokeColor: '#775DD0'
                                });
                            }
                        
                            data.push(dataPoint);
                        });

                        let options = {
                            title: {
                                text: `Quantity of orders evaluated - ${capitalizarPrimeraLetra(id.split('-')[1])}`,  // Aquí defines el título que desees
                                align: 'center',
                                margin: 10,
                                offsetX: 0,
                                offsetY: 10,
                                floating: false,
                                style: {
                                  fontSize: '14px',
                                  color: '#333'  // Puedes ajustar el color y otros estilos del título
                                }
                            },
                            series: [{
                                name: 'Evaluated',
                                data: data
                            }
                            ],
                            chart: {
                                height: '100%',
                                type: 'bar',
                                events: {
                                    click: function(chart, w, e) {
                                        // console.log(chart, w, e)
                                    }
                                }
                            },
                            plotOptions: {
                                bar: {
                                    columnWidth: '45%',
                                    //distributed: true
                                }
                            },
                            dataLabels: {
                                enabled: (!isMobile()) ? objData.res.length <= 30 : objData.res.length <= 10 ,
                                formatter: function (value, { seriesIndex, dataPointIndex, w }) {
                                    if (value === 0 && w.config.series[seriesIndex].data[dataPointIndex].goal !== 0) {
                                        return w.config.series[seriesIndex].data[dataPointIndex].goal;
                                    } else {
                                        return value;
                                    }
                                }
                            },
                            tooltip: {
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function (val) {
                                        return val.toString();
                                    }
                                }
                            },
                            legend: {
                                show: true
                            },
                            xaxis: {
                                //categories: categories,
                                labels: {
                                    style: {
                                        fontSize: '12px'
                                    },
                                    formatter: (value) => {
                                        const len = value.length;
                                        return len > 20 ? `${value.substring(0, 20)}...` : value;
                                    }
                                }
                            }
                        };

                        let chart = new ApexCharts(document.querySelector(id), options);
                        chart.render();

                        setStateChart(index, true);
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });

}

const loadFiltersChart = async () => {

    const selecteds = await getFiltersChartSelected();

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadFiltersChart",
            type: 'GET',
            data: {'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value},
            cache: false,

            beforeSend: function() {
                $('#filters-chart').html(`
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
                `);
            },
    
            error:  function(xhr) {
                alert(xhr);
                resolve(false);
            },

            success: async function(response) {
                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){

                        $('#filters-chart').html(objData.res);

                        document.querySelectorAll('#filters-chart input[type="checkbox"]').forEach(element => {
                            element.addEventListener('change', function() {
                                checkFiltersChartSelected();
                            });

                            selecteds.destinations.forEach(value => {
                                if (element.value == value) element.checked = true;
                            });

                            selecteds.types.forEach(value => {
                                if (element.value == value) element.checked = true;
                            });

                            selecteds.products.forEach(value => {
                                if (element.value == value) element.checked = true;
                            });

                            selecteds.crops.forEach(value => {
                                if (element.value == value) element.checked = true;
                            });

                            selecteds.varieties.forEach(value => {
                                if (element.value == value) element.checked = true;
                            });
                        });
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });

}

const checkFiltersChartSelected = () => {

    const btn = document.querySelector('#filters-chart-btn');
    let isChecked = false;

    document.querySelectorAll('#filters-chart input[type="checkbox"]:checked').forEach(element => {
        isChecked = true;
        return;
    });

    //if (isChecked) btn.removeAttribute('disabled');
    //else btn.setAttribute('disabled', true);

}

const getFiltersChartSelected = async () => {

    const mapObject = {};
    let containers = document.querySelectorAll('#filters-chart> div');

    let array = [];
    containers[0].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['destinations'] = array;

    array = [];
    containers[1].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['types'] = array;

    array = [];
    containers[2].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['products'] = array;

    array = [];
    containers[3].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['crops'] = array;

    array = [];
    containers[4].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['varieties'] = array;

    if (!mapObject.destinations.length && !mapObject.types.length && !mapObject.products.length && !mapObject.crops.length && !mapObject.varieties.length)
        document.querySelector('#chart-filters-notify').classList.add('d-none');
    else
        document.querySelector('#chart-filters-notify').classList.remove('d-none');

    return mapObject;

}

const getStateChart = (index) => {

    let value = false;

    switch (index) {
        case 0:
            value = stateChartCusts;
            break;

        case 1:
            value = stateChartUsers;
            break;

        case 2:
            value = stateChartCrops;
            break;

        case 3:
            value = stateChartVarieties;
            break;
    
        default:
            break;
    }

    return value;
    
}

const setStateChart = (index, state) => {
    if (state)
    switch (index) {
        case 0:
            stateChartCusts = true;
            break;

        case 1:
            stateChartUsers = true;
            break;

        case 2:
            stateChartCrops = true;
            break;

        case 3:
            stateChartVarieties = true;
            break;
    
        default:
            break;
    }
    else{
        stateChartCusts = false;
        stateChartUsers = false;
        stateChartCrops = false;
        stateChartVarieties = false;
    }
}

const loadVarietiesCompare = async () => {

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadVarietiesCompare",
            type: 'GET',
            data: {'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value},
            cache: false,

            beforeSend: function() {

            },
    
            error:  function(xhr) {
                alert(xhr);
                resolve(false);
            },

            success:  function(response) {
                try {

                    const objData = JSON.parse(response);

                    if(objData.status == true){

                        let selecteds = document.querySelectorAll(`#compare-varieties input[type="checkbox"]:checked`);

                        $('#compare-varieties').html(objData.res);

                        selecteds.forEach(selected => {
                            document.querySelectorAll(`#compare-varieties input[type="checkbox"]`).forEach(element => {
                                console.log({ 'selected' : selected.value, 'value' : element.value });
                                if (selected.value == element.value) element.checked = true;
                            });
                        });
                        
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });

}

const loadParametersCompare = async () => {

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadParametersCompare",
            type: 'GET',
            data: {'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value},
            cache: false,

            beforeSend: function() {
                
            },
    
            error:  function(xhr) {
                alert(xhr);
                resolve(false);
            },

            success:  function(response) {
                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){

                        let selecteds = document.querySelectorAll(`#compare-parameters input[type="checkbox"]:checked`);

                        $('#compare-parameters').html(objData.res);

                        selecteds.forEach(selected => {
                            document.querySelectorAll(`#compare-parameters input[type="checkbox"]`).forEach(element => {
                                console.log({ 'selected' : selected.value, 'value' : element.value });
                                if (selected.value == element.value) element.checked = true;
                            });
                        });
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });

}

const showSelectionCompare = (idCheckboxs, idBtn, modal = true) => {
    let cont = document.querySelector(`#${idBtn}`);
    cont.innerHTML = null;
    
    document.querySelectorAll(`#${idCheckboxs} input[type="checkbox"]:checked`).forEach(element => {

        const value = element.value.split(',');

        let div = document.createElement('div');
        div.classList.add('bg-success', 'bg-opacity-25', 'fs-0-7', 'm-1', 'px-2', 'py-1', 'rounded-2');
        div.innerHTML = `${value[1]}<button class="btn btn-sm py-1 px-1 ms-2 rounded-circle" onclick="compareRemoveSelected('${idCheckboxs}', '${idBtn}', '${element.value}');"><i class="bi bi-x"></i></button>`;

        cont.appendChild(div);
    });

    if (!cont.innerHTML) {
        cont.innerHTML = '<h6>Clic here.</h6>';
    }

    loadTableCompare(modal);
}

const compareRemoveSelected = (idCheckboxs, idBtn, value) => {
    event.stopPropagation();

    document.querySelectorAll(`#${idCheckboxs} input[type="checkbox"]:checked`).forEach(element => {
        if (element.value == value) element.checked = false;
    });

    showSelectionCompare(idCheckboxs, idBtn, false);
}

const loadTableCompare = async (modal = true) => {

    let varieties = [];
    let parameters = [];

    document.querySelectorAll(`#compare-varieties input[type="checkbox"]:checked`).forEach(element => {
        varieties.push(element.value);
    });

    document.querySelectorAll(`#compare-parameters input[type="checkbox"]:checked`).forEach(element => {
        parameters.push(element.value);
    });

    if (varieties.length == 0 || parameters.length == 0) {
        $('#compare-table').html(`
            <div class="text-center mt-5">
                <i class="bi bi-exclamation-circle display-6"></i>
                <p class="mt-3">Select the items to compare!</p>
            </div>
        `);

        if (varieties.length == 0 && modal) toogleModal('modal-compare-varieties', true);
        else if (parameters.length == 0 && modal) toogleModal('modal-compare-parameters', true);

        return;
    }

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadTableCompare",
            type: 'GET',
            data: { varieties : JSON.stringify(varieties), parameters : JSON.stringify(parameters), from : document.querySelector('#week-from').value, to : document.querySelector('#week-to').value },
            cache: false,

            beforeSend: function() {
                $('#compare-table').html(`
                    <div class="text-center mt-5">
                        <i class="spiner spinner-border"></i>
                    </div>
                `);
            },
    
            error:  function(xhr) {
                alert(xhr);
                console.log(xhr);
                resolve(false);
            },

            success:  function(response) {
                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#compare-table').html(objData.res);

                        table = new DataTable('#table-compare', {
                            dom: 'Bfrtip',
                            buttons: [
                                'copy', 'csv', 'excel', 'pdf', 'print'
                            ],
                            fixedColumns: {
                                left: (isMobile()) ? 1 : 4
                            },
                            scrollCollapse: true,
                            "processing": true,
                            "scrollX": true,
                            "iDisplayLength": 100,
                            "stateSave": true,
                            scrollY: '57vh',
                        }).page();

                        /* $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                            window.scrollTo(0, 0);
                        }); */
                        if(modal) window.scrollTo(0, 1000);

                        if (!isMobile()) {
                            document.querySelector('body').classList.add('toggle-sidebar');
                            setStateChart(0, false);
                        }
                        resizeTable();

                        //document.querySelectorAll('#table-compare_wrapper .row')[2].classList.add('mt-3');
                    }else{
                        console.log(objData.res);
                        alert(objData.res);
                    }
                } catch (error) {
                    console.log('dsd');
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });

}

const viewImage = (element) => {
    let tr = element.parentNode.parentNode;
    let tds = tr.querySelectorAll('td, th');

    let contIndicators = document.querySelector('#images-compare-indicators');
    let contImages = document.querySelector('#images-compare');
    contImages.innerHTML = null;
    contIndicators.innerHTML = null;

    document.querySelector('#modalImageLabel').innerHTML = `${tds[2].innerText} / Order No.${tds[1].innerText} / ${tds[0].innerText}`;

    tr.querySelectorAll('img').forEach(image => {

        let firstIndicator = '';
        let active = '';

        if (image.dataset.index == element.dataset.index) {
            firstIndicator = 'class="active" aria-current="true"';
            active = 'active';
        }

        contIndicators.innerHTML = contIndicators.innerHTML + `
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="${image.dataset.index}" ${firstIndicator} aria-label="Slide ${image.dataset.index + 1}"></button>
        `;

        contImages.innerHTML = contImages.innerHTML + `
            <div class="carousel-item ${active} border-0">
                <div class="text-center bg-dark" style="height: 80vh;">
                    <img 
                        src="${image.src}" 
                        alt="Imagen" 
                        height="100%;"
                    >
                </div>
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50">
                    <h5>${image.dataset.parameter}</h5>
                    <p>${image.dataset.obs}</p>
                </div>
            </div>
        `;

        firstIndicator = '';
        active = '';
    });

    //console.log(tr);
}

const resizeTable = () => {

    const mains = document.querySelectorAll('.main');

    mains.forEach(main => {
        if (main.style.display !== 'none') {

            let tables = main.querySelectorAll('table.dataTable');

            if (tables.length != 2) return;

            let id = tables[1].id;

            setTimeout(() => {
                let tableCompare = $(`#${id}`).DataTable();
                tableCompare.columns.adjust().draw();
            }, 500);
        }
    });

}

async function showOption($this, container){

    let search = document.querySelector('.search-bar');
    let searchBtn = document.querySelector('.search-bar-toggle');
    
    if (isMobile()) {
        document.querySelector('body').classList.remove('toggle-sidebar');
    }

    search.classList.add('d-none');
    searchBtn.classList.add('d-none');

    $('.main').fadeOut(0);
    $('.nav-link').addClass('collapsed');

    $($this).removeClass('collapsed');
    $('#'+container).fadeIn(0);

    switch (container) {

        case 'main-calendar':
            
            if (!isMobile()) document.querySelector('body').classList.add('toggle-sidebar');
            else searchBtn.classList.remove('d-none');

            search.classList.remove('d-none');
            
            if (!stateCalendar) {
                await loadCalendar();
                loadCalendarFilters();
            }

            break;

        case 'main-events':
            
            if (!stateEvents) {
                await loadEvents();
            }

            break;

        case 'main-parameters':
            
            if (!stateParameters) {
                await loadParameters();
                loadCrops();
            }

            break;

        case 'main-users':
            
            if (!stateUsers) {
                await loadUsers();
                await loadCusts();
                loadRoles();
            }

            break;

        case 'main-customers':
            
            if (!stateCustomers) {
                await loadCustomers();
            }

            break;
    }
}

function isMobile(){
    return (
        (navigator.userAgent.match(/Android/i)) ||
        (navigator.userAgent.match(/webOS/i)) ||
        (navigator.userAgent.match(/iPhone/i)) ||
        (navigator.userAgent.match(/iPod/i)) ||
        (navigator.userAgent.match(/iPad/i)) ||
        (navigator.userAgent.match(/BlackBerry/i))
    );
}

function toogleModal(id, action) {
    if (action) {
        modal = new bootstrap.Modal(document.getElementById(id));
        modal.show();
    } else {
        modal = bootstrap.Modal.getInstance(document.getElementById(id));
        modal.hide();
    }
}

function hasDuplicates(arr) {
    return new Set(arr).size !== arr.length;
}

function capitalizarPrimeraLetra(cadena) {
    return cadena.charAt(0).toUpperCase() + cadena.slice(1);
}