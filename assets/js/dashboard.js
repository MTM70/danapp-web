let stateCalendar = false;
let stateParameters = false;
let stateEvents = false;
let stateUsers = false;
let stateCustomers = false;

$(document).ready(function() {

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
            var objData = JSON.parse(response);

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

    document.querySelector('#week-from').addEventListener('change', function() {
        loadChartCountCust();
        loadVarietiesCompare();
    });

    document.querySelector('#week-to').addEventListener('change', function() {
        loadChartCountCust();
        loadVarietiesCompare();
    });

    document.querySelector('#varieties-compare-apply').addEventListener('click', function() {

        showSelectionCompare('compare-varieties', 'compare-varieties-selected');

    });

    document.querySelector('#parameters-compare-apply').addEventListener('click', function() {

        showSelectionCompare('compare-parameters', 'compare-parameters-selected');

    });

    document.querySelector('.toggle-sidebar-btn').addEventListener('click', function() {
        resizeTable();
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

    loadChartCountCust();
    loadVarietiesCompare();
    loadParametersCompare();
});

const loadChartCountCust = async () => {

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadChartCountCust",
            type: 'GET',
            data: {'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value},
            cache: false,

            beforeSend: function() {
                document.querySelector("#chart").innerHTML = '<i class="spinner spinner-border"></i>'
            },
    
            error:  function(xhr) {
                alert(xhr.status);
                resolve(false);
            },

            success:  function(response) {

                document.querySelector("#chart").innerHTML = null;

                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        //$('#chart').html(objData.res);
                        //console.log(objData.res);

                        const categories = objData.res.map(item => (item.cust.length > 20) ? `${item.cust.substring(0, 20)}...` : item.cust);
                        const data = objData.res.map(item => item.count);

                        //console.log({categories, data});

                        let options = {
                            title: {
                                text: 'Quantity of orders evaluated - customer',  // Aquí defines el título que desees
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
                                data: data
                            }],
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
                                enabled: true
                            },
                            legend: {
                                show: true
                            },
                            xaxis: {
                                categories: categories,
                                labels: {
                                    style: {
                                        fontSize: '12px'
                                    }
                                }
                            }
                        };

                        chart = new ApexCharts(document.querySelector("#chart"), options);
                        chart.render();
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
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#compare-varieties').html(objData.res);
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
            cache: false,

            beforeSend: function() {
                
            },
    
            error:  function(xhr) {
                alert(xhr);
                resolve(false);
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#compare-parameters').html(objData.res);
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

const showSelectionCompare = (idCheckboxs, idBtn) => {
    let cont = document.querySelector(`#${idBtn}`);
    cont.innerHTML = null;
    
    document.querySelectorAll(`#${idCheckboxs} input[type="checkbox"]:checked`).forEach(element => {

        const value = element.value.split(',');

        let div = document.createElement('div');
        div.classList.add('bg-success', 'bg-opacity-25', 'fs-0-7', 'm-1', 'px-2', 'py-1', 'rounded-2');
        div.innerHTML = `${value[1]}<button class="btn btn-sm py-1 px-1 ms-2 rounded-circle"><i class="bi bi-x"></i></button>`;

        cont.appendChild(div);
    });

    if (!cont.innerHTML) {
        cont.innerHTML = '<h6>Clic here.</h6>';
    }

    loadTableCompare();
}

const loadTableCompare = async () => {

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

        if (varieties.length == 0) toogleModal('modal-compare-varieties', true);
        else if (parameters.length == 0) toogleModal('modal-compare-parameters', true);

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
                    var objData = JSON.parse(response);

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
                        window.scrollTo(0, 1000);
                        if (!isMobile()) document.querySelector('body').classList.add('toggle-sidebar');
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

    document.querySelector('#modalImageLabel').innerHTML = `${tds[1].innerText} / Order No.${tds[0].innerText} / ${tds[3].innerText}`;

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
    
    if (isMobile()) {
        document.querySelector('body').classList.remove('toggle-sidebar');
    }

    search.classList.add('d-none');

    $('.main').fadeOut(0);
    $('.nav-link').addClass('collapsed');

    $($this).removeClass('collapsed');
    $('#'+container).fadeIn(0);

    switch (container) {

        case 'main-calendar':
            
            if (!isMobile()) document.querySelector('body').classList.add('toggle-sidebar');
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