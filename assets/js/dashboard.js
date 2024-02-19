const isMobile = window.innerWidth <= 767;

let stateCalendar = false;
let stateParameters = false;
let stateEvents = false;
let stateUsers = false;
let stateCustomers = false;

//*Uploads
let stateUploadLogs = false;

//* Charts
const chartsId = ['#nav-customers', '#nav-users', '#nav-crops', '#nav-varieties'];
let charts = [];
let chartsStates = [false, false, false, false];
let chartsValues = [];
let checkTypeOrder = true;

//* Table compare
let tableCompareData = null;
let tableCompare = null;
let tableCompareScrollInstance = null;
let tableCompareResize = false;
let temporizador;

$(document).ready(async function() {

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    if (!isMobile) document.querySelector('#miDropdown').parentNode.classList.add('dropend');
    else {
        document.querySelector('#miDropdown').parentNode.classList.add('dropdown');
        document.querySelector('body').classList.remove('toggle-sidebar');
    }

    loadEmptyCharts();

    //TODO upload data
    $("#form-upload").on("submit", function(e){
        e.preventDefault();

        const formData = new FormData(document.getElementById("form-upload"));

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

            loadUploadLogs();

            $('#upload-btn').removeClass('disabled');
            $('#upload-text').html('Upload');
            $('#upload-loading').addClass('d-none');
        });
    });

    document.querySelector('#week-from').addEventListener('change', async function() {
        if (!this.value) return;
        setStateChart(0, false);

        await loadCharts();
        await loadVarietiesCompare();
        await loadParametersCompare();
        await loadTableCompare(false);
        await loadFiltersChart();
    });

    document.querySelector('#week-to').addEventListener('change', async function() {
        if (!this.value) return;
        setStateChart(0, false);

        await loadCharts();
        await loadVarietiesCompare();
        await loadParametersCompare();
        await loadTableCompare(false);
        await loadFiltersChart();
    });

    document.querySelector('#filters-chart-btn').addEventListener('click', async function() {
        setStateChart(0, false);

        // Obtén una referencia al elemento del dropdown que deseas cerrar
        const dropdown = document.getElementById("miDropdown"); // Reemplaza "miDropdown" con el ID de tu dropdown
        // Cierra el dropdown
        new bootstrap.Dropdown(dropdown).hide();

        await loadCharts();
        await loadTableCompare(false);
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
        resizeTableMain();

        resizeChart();
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

    document.getElementById('fullscreenButton').addEventListener('click', () => {
        const chartContainer = document.querySelector(`${getChartIdTab()}-chart`);
      
        if (chartContainer) {
          if (chartContainer.requestFullscreen) {
            chartContainer.requestFullscreen();
          } else if (chartContainer.mozRequestFullScreen) {
            chartContainer.mozRequestFullScreen();
          } else if (chartContainer.webkitRequestFullscreen) {
            chartContainer.webkitRequestFullscreen();
          } else if (chartContainer.msRequestFullscreen) {
            chartContainer.msRequestFullscreen();
          }
        }
    });

    // Ajustar el tamaño del gráfico después de entrar en modo de pantalla completa
    document.addEventListener('fullscreenchange', () => {
        if (document.fullscreenElement) {
            resizeChartHeight(window.innerHeight);
        } else {
            resizeChartHeight('100%');
        }
    });

    window.addEventListener('resize', () => {
        //resizeTable();
        //destroyChart();
    });

    /* $(window).on('scroll', function () {
        if (!tableCompare) return;

        const scrollPosition = $(document).scrollTop();
        const windowHeight = $(window).height();
        const documentHeight = $(document).height();

        // Verificar si el scroll está en la parte inferior del documento
        const stateScroll = (scrollPosition + windowHeight >= documentHeight) ? 'auto' : 'hidden' ;

        tableCompareScrollInstance = !tableCompareScrollInstance ? document.querySelector('#nav-detail .dataTables_scrollBody') : tableCompareScrollInstance;
        
        if (tableCompareScrollInstance.style.overflow == stateScroll) return;

        tableCompareScrollInstance.style.overflow = stateScroll ;
        resizeTable();
    }); */

    await loadFiltersChart();
    await loadCharts();
    await loadVarietiesCompare();
    await loadParametersCompare();
    await loadTableCompare(false);
});

const openModalUploads = () => {

    if (stateUploadLogs) return;

    loadUploadLogs();

}

const loadUploadLogs = async () => {

    const container = document.querySelector('#upload-logs');

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadUploadLogs",
            type: 'GET',
            cache: false,

            beforeSend: function() {
            },
    
            error:  function(xhr) {
                alert(xhr.status);
                resolve(false);
            },

            success: async function(response) {
                
                container.innerHTML = null;

                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){

                        container.innerHTML = objData.res;

                        stateUploadLogs = true;

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

const loadEmptyCharts = () => {

    return new Promise(resolve => {

        for (let index = 0; index < chartsId.length; index++) {

            const id = chartsId[index];
            const chartId = `${id}-chart`;
            
            const options = {
                title: {
                    text: `Quantity of orders - ${capitalizarPrimeraLetra(id.split('-')[1])}`,  // Aquí defines el título que desees
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
                noData: {
                    text: 'Loading...'
                },
                series: [],
                chart: {
                    height: '100%',
                    width: "100%",
                    type: 'bar',
                    events: {
                        click: function(event, chartContext, config) {
                            const input = document.querySelector(`${getChartIdTab()}-table tr[data-category-index="${config.dataPointIndex}"] input`);
                            input.checked = !input.checked;
                            
                            filterChartSeries();
                        },

                        dataPointSelection: function (event, chartContext, config) {
                            let dataPointSelected = config.dataPointIndex;
                            let dataSeriesSelected = config.seriesIndex;
                            let clonedMenu = null;

                            const apexGraph = document.querySelector(config.w.globals.chartClass);

                            if (document.querySelector(`.chart-tooltip-copy-${index}`) == null) {
                                let copy = document.getElementsByClassName(
                                    "apexcharts-tooltip"// apexcharts-active
                                );
                        
                                const clone = copy[0].cloneNode(true);
                                
                                clonedMenu = clone.cloneNode(true);
                                clonedMenu.id = `apexcharts-active-dp${dataPointSelected}-series${dataSeriesSelected}-${index}`;

                                const list = clonedMenu.classList;
                                list.add(`chart-tooltip-copy-${index}`, 'start-0', 'top-0');
                            }
                            else clonedMenu = document.querySelector(`.chart-tooltip-copy-${index}`);

                            if(event != null) clonedMenu.style.opacity = 0; 
                            else clonedMenu.style.opacity = 1;

                            if (!document.querySelector(`${getChartIdTab()}-table .bg-secondary`)) return;
                            const tr = document.querySelector(`${getChartIdTab()}-table .bg-secondary`).querySelectorAll('td');

                            clonedMenu.querySelector('.apexcharts-tooltip-title').innerText = tr[1].innerText.length > 20 ? `${tr[1].innerText.substring(0, 20)}...` : tr[1].innerText;
                            clonedMenu.querySelectorAll('.apexcharts-tooltip-series-group')[0].style.display = 'block';
                            clonedMenu.querySelectorAll('.apexcharts-tooltip-series-group')[0].classList.add('d-flex');
                            clonedMenu.querySelectorAll('.apexcharts-tooltip-series-group')[0].innerHTML = `
                                <span class="apexcharts-tooltip-marker" style="background-color: rgb(0, 143, 251); position: relative;"></span>
                                <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Uploads: </span>
                                        <span class="apexcharts-tooltip-text-y-value">${(tr[2].innerText) ? tr[2].innerText : 0}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-goals-group">
                                        <span class="apexcharts-tooltip-text-goals-label"></span>
                                        <span class="apexcharts-tooltip-text-goals-value"></span>
                                    </div>
                                    <div class="apexcharts-tooltip-z-group">
                                        <span class="apexcharts-tooltip-text-z-label"></span>
                                        <span class="apexcharts-tooltip-text-z-value"></span>
                                    </div>
                                </div>
                            `;
                            clonedMenu.querySelectorAll('.apexcharts-tooltip-series-group')[1].style.display = 'block';
                            clonedMenu.querySelectorAll('.apexcharts-tooltip-series-group')[1].classList.add('d-flex');
                            clonedMenu.querySelectorAll('.apexcharts-tooltip-series-group')[1].innerHTML = `
                                <span class="apexcharts-tooltip-marker" style="background-color: rgb(0, 227, 150); position: relative;"></span>
                                <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Evaluated: </span>
                                        <span class="apexcharts-tooltip-text-y-value">${tr[3].innerText}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-goals-group">
                                        <span class="apexcharts-tooltip-text-goals-label"></span>
                                        <span class="apexcharts-tooltip-text-goals-value"></span>
                                    </div>
                                    <div class="apexcharts-tooltip-z-group">
                                        <span class="apexcharts-tooltip-text-z-label"></span>
                                        <span class="apexcharts-tooltip-text-z-value"></span>
                                    </div>
                                </div>
                            `;
                            
                            if (document.querySelector(`.chart-tooltip-copy-${index}`) == null) {
                                apexGraph.appendChild(clonedMenu);
                            }
                        },

                        mouseMove: function(event, chartContext, config) {
                            if (document.querySelector(`.chart-tooltip-copy-${index}`)) {
                                document.querySelector(`.chart-tooltip-copy-${index}`).style.opacity = 0;
                            }

                            const table = document.querySelector(`${getChartIdTab()}-table`).querySelectorAll('tr');
                            let trId = null;

                            table.forEach(tr => tr.classList.remove('bg-secondary', 'bg-opacity-25'));

                            table.forEach(tr => {
                                if (tr.getAttribute('data-category-index') == config.dataPointIndex) {
                                    trId = tr.id;
                                    tr.classList.add('bg-secondary', 'bg-opacity-25');
                                    return;
                                }
                            });

                            scrollToTableRow(`${getChartIdTab()}-table .dataTables_scrollBody`, trId);
                        },

                        mounted: (chart) => {
                            chart.windowResizeHandler();
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            position: 'top', // top, center, bottom
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (value, { seriesIndex, dataPointIndex, w }) {
                        if (value === 0 && w.config.series[seriesIndex].data[dataPointIndex].goal !== 0) {
                            return w.config.series[seriesIndex].data[dataPointIndex].goal;
                        } else {
                            return value;
                        }
                    },
                    offsetY: -20,
                    style: {
                      fontSize: '12px',
                      colors: ["#304758"]
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    fixed: {
                        enabled: false
                    },
                    y: {
                        formatter: function (val) {
                            return (val) ? val.toString() : 0;
                        }
                    }
                },
                legend: {
                    show: true
                },
                xaxis: {
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

            charts[index] = new ApexCharts(document.querySelector(chartId), options);
            charts[index].render();

        }

        resolve('resolved');


    });

}

const loadCharts = async () => {

    const index = getChartIndexTab();
    const id = getChartIdTab();
    const idChart = `${id}-chart`;
    const idTable = `${id}-table`;

    //* Ajustar tabla del tab actual
    if (document.querySelector(`#table-chart-${index}`))
    new DataTable(document.querySelector(`#table-chart-${index}`)).columns.adjust();

    filterTableCompareWithChartTable();

    if (chartsStates[index]) return;

    const filters = await getFiltersChartSelected();
    const filtersChartTable = await getFiltersChartTableSelected();

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadCharts",
            type: 'GET',
            data: { 'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value, 'type' : index, 'filters' : JSON.stringify(filters) },
            cache: false,

            beforeSend: function() {
                charts[index].updateSeries([]);
                charts[index].updateOptions({
                    noData: {
                        text: 'Loading...'
                    },
                });
                document.querySelector(idTable).innerHTML = `
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="spinner spinner-border spinner-border-sm"></i>
                    </div>
                `;
            },
    
            error:  function(xhr) {
                alert(xhr.status);
                resolve(false);
            },

            success: async function(response) {
                
                document.querySelector(idTable).innerHTML = null;

                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){

                        const dataUploads = [];
                        const dataEvaluated = [];

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

                            const dataPointUploads = {
                                x: element.categorie,
                                y: element.goal,
                                id: element.id
                            };

                            const dataPointEvaluated = {
                                x: element.categorie,
                                y: element.count
                            };
                        
                            dataUploads.push(dataPointUploads);
                            dataEvaluated.push(dataPointEvaluated);

                        });

                        chartsValues[index] = [{
                            name: 'Uploads',
                            data: dataUploads
                        },{
                            name: 'Evaluated',
                            data: dataEvaluated
                        }]

                        charts[index].updateSeries(chartsValues[index]);
                        charts[index].updateOptions({
                            dataLabels: {
                                enabled: (!isMobile) ? objData.res.length <= 30 : objData.res.length <= 10 ,
                            },
                            noData: {
                                text: objData.res.length ? 'Loading...' : 'No data'
                            },
                            
                        });

                        setStateChart(index, true);

                        //*Table ==============================================================

                        const table = document.createElement('table');
                        table.classList.add('table', 'table-bordered', 'table-hover', 'fs-0-8');
                        table.style.width = '100%'
                        table.id = `table-chart-${index}`;
                        table.innerHTML = `
                            <thead>
                                <tr>
                                    <th class="text-center" width="15px">
                                        <button class="btn btn-sm btn-light p-0"><i class="bi bi-x-square text-danger"></i></button>
                                    </th>
                                    <th>Categorie</th>
                                    <th class="bg-primary bg-opacity-75 ${(index == 1) ? 'd-none' : ''}"></th>
                                    <th style="background: #03e396"></th>
                                    <th class="text-center ${(index == 1) ? 'd-none' : ''}">%</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot></tfoot>
                        `;

                        const tbody = table.querySelector('tbody');
                        const tfoot = table.querySelector('tfoot');
                        const totalTable = [0,0];

                        let trIndex = 0;
                        dataUploads.forEach(item => {

                            const y1 = (item.y) ? item.y : 0;
                            const y2 = dataEvaluated.find(item2 => item2.x === item.x).y;

                            const value = (y1 > 0) ? ((y2 / y1) * 100) : 0;

                            const checked = filtersChartTable.includes(item.id.toString()) ? 'checked' : '';

                            const row = document.createElement('tr');
                            row.classList.add('cursor-select');
                            row.setAttribute("data-category-index", trIndex);
                            row.id = `${idTable.slice(1)}-tr-${trIndex}`;
                            row.innerHTML += `
                                <td class="align-middle text-center w-auto">
                                    <div class="form-check pt-1 ps-4">
                                        <input class="form-check-input cursor-select" type="checkbox" value="${item.id},${item.x}" id="${`table-chart-input-${index}-${trIndex}`}" ${checked} style="pointer-events:none;">
                                    </div>
                                </td>
                                <td class="align-middle">${item.x}</td>
                                <td class="align-middle text-center ${(index == 1) ? 'd-none' : ''}">${y1}</td>
                                <td class="align-middle text-center">${y2}</td>
                                <td class="align-middle text-center ${(index == 1) ? 'd-none' : ''}">${Number.isInteger(value) ? value : value.toFixed(1)}%</td>
                            `;

                            row.addEventListener("mouseover", (event) => {
                                
                                const categoryIndex = parseInt(event.currentTarget.getAttribute("data-category-index"), 10);
                                const index = getChartIndexTab();

                                event.currentTarget.closest('table').querySelectorAll('tr').forEach(tr => tr.classList.remove('bg-secondary', 'bg-opacity-25'));

                                if (categoryIndex >= 99999) {
                                    if(document.querySelector(`.chart-tooltip-copy-${index}`))
                                    document.querySelector(`.chart-tooltip-copy-${index}`).style.opacity = 0;

                                    return;
                                }

                                //this.classList.add('bg-secondary', 'bg-opacity-25');
                                event.currentTarget.classList.add('bg-secondary', 'bg-opacity-25');

                                charts[index].toggleDataPointSelection(0, categoryIndex);

                            });

                            row.addEventListener("mouseout", (event) => {

                                const categoryIndex = parseInt(event.currentTarget.getAttribute("data-category-index"), 10);

                                if (categoryIndex >= 99999) return;

                                charts[getChartIndexTab()].toggleDataPointSelection(0, categoryIndex);

                            });

                            row.addEventListener("click", (event) => {

                                //const categoryIndex = parseInt(event.currentTarget.getAttribute("data-category-index"), 10);
                                //const index = getChartIndexTab();

                                const isChecked = !event.currentTarget.querySelector('input').checked;
                                event.currentTarget.querySelector('input').checked = isChecked;

                                filterChartSeries();

                            });

                            tbody.appendChild(row);

                            totalTable[0] += +y1;
                            totalTable[1] += +y2;
                            trIndex++;
                        });

                        tfoot.innerHTML = `
                            <tr>
                                <th></th>
                                <th>Total</th>
                                <th class="text-center ${index == 1 ? 'd-none' : ''}">${totalTable[0]}</th>
                                <th class="text-center">${totalTable[1]}</th>
                                <th class="text-center ${index == 1 ? 'd-none' : ''}">${((totalTable[1] / totalTable[0]) * 100).toFixed(1)}%</th>
                            </tr>
                        `;

                        document.querySelector(idTable).appendChild(table);

                        table.querySelector('button').addEventListener('click', async function(event) {
                            const inputs = event.currentTarget.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.querySelectorAll('input[type="checkbox"]:checked');
                            inputs.forEach(input => {
                                input.checked = false;
                            });

                            filterChartSeries();
                        });

                        new DataTable(document.querySelector(`#table-chart-${index}`), {
                            ordering: false,
                            autoWidth: true,
                            scrollCollapse: true,
                            "processing": true,
                            "scrollX": true,
                            "iDisplayLength": 100,
                            "stateSave": true,
                            scrollY: isMobile ? false : '42vh',
                            paging: false,
                            info: false,
                            searching: false
                        }).page();

                        //document.querySelector(idTable).querySelectorAll('table .row')[2].classList.add('mt-3');
                        //console.log();
                        document.querySelector(idTable).querySelectorAll('.dataTables_wrapper .row')[2].classList.add('mt-2')
                        //*Table ==============================================================

                        filterChartSeries();

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

const getChartIndexTab = () => {
    const buttons = Array.from(document.querySelectorAll('#nav-tab button'));
    const btnActive = buttons.find(button => button.classList.contains('active'));
    return buttons.indexOf(btnActive);
}

const getChartIdTab = () => {
    const buttons = Array.from(document.querySelectorAll('#nav-tab button'));
    const btnActive = buttons.find(button => button.classList.contains('active'));
    return btnActive.getAttribute('data-bs-target');;
}

const getTableCompareIndexTab = () => {
    const buttons = Array.from(document.querySelectorAll('#nav-tab-table-compare button'));
    const btnActive = buttons.find(button => button.classList.contains('active'));
    return buttons.indexOf(btnActive);
}

const filterChartSeries = () => {
    const index = getChartIndexTab();
    const idTab = getChartIdTab();
    const inputs = document.querySelector(`${idTab}-table`).querySelectorAll('input[type="checkbox"]:checked');
    const inputsDisabled = document.querySelector(`${idTab}-table`).querySelectorAll('input[type="checkbox"]:not(:checked)');

    const dataUploads = [];
    const dataEvaluated = [];

    let categoryIndex = 0;
    inputs.forEach(element => {
        element.parentNode.parentNode.parentNode.setAttribute("data-category-index", categoryIndex);
        const tds = element.parentNode.parentNode.parentNode.querySelectorAll('td');

        const dataPointUploads = {
            x: tds[1].innerText.length > 20 ? `${tds[1].innerText.substring(0, 20)}...` : `${tds[1].innerText}`,
            y: tds[2].innerText,
        };
    
        const dataPointEvaluated = {
            x: tds[1].innerText.length > 20 ? `${tds[1].innerText.substring(0, 20)}...` : `${tds[1].innerText}`,
            y: tds[3].innerText,
        };

        dataUploads.push(dataPointUploads);
        dataEvaluated.push(dataPointEvaluated);

        categoryIndex++;
    });


    charts[index].updateOptions({
        series: 
            (inputs.length)
            ?[
                {
                    name: 'Uploads',
                    data: dataUploads
                },{
                    name: 'Evaluated',
                    data: dataEvaluated
                }
            ]
            :chartsValues[index],
        dataLabels: {
            enabled: !isMobile 
                        ? (inputs.length 
                            ? inputs.length 
                            : chartsValues[index][0].data.length) <= 30 
                        : (inputs.length 
                            ? inputs.length 
                            : chartsValues[index][0].data.length) <= 10
        }
    });

    if (inputs.length)
        inputsDisabled.forEach(element => {
            element.parentNode.parentNode.parentNode.setAttribute("data-category-index", 99999);
        });
    else{
        let trIndex = 0;
        document.querySelector(`${idTab}-table`).querySelectorAll('input[type="checkbox"]').forEach(element => {

            element.parentNode.parentNode.parentNode.setAttribute("data-category-index", trIndex);
            
            trIndex++;
        });
    }

    filterTableCompareWithChartTable();
}

const loadFiltersChart = async () => {

    const selecteds = await getFiltersChartSelected();

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadFiltersChart",
            type: 'GET',
            data: {'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value, 'checkType' : checkTypeOrder},
            cache: false,

            beforeSend: function() {
                $('#filters-chart').html(`
                    <div class="col-md-4">
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
                    <div class="col-md-8">
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
                    <div class="col-md-3 mt-4">
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
                    <div class="col-md-5 mt-4">
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
                    <div class="col-md-4 mt-4">
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
                    <div class="col-md-6 mt-4">
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
                    <div class="col-md-6 mt-4">
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

                            selecteds.users.forEach(value => {
                                if (element.value == value) element.checked = true;
                            });
                            
                            selecteds.customers.forEach(value => {
                                if (element.value == value) element.checked = true;
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
    mapObject['users'] = array;

    array = [];
    containers[1].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['customers'] = array;

    array = [];
    containers[2].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['destinations'] = array;

    array = [];
    containers[3].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['types'] = array;

    array = [];
    containers[4].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['products'] = array;

    array = [];
    containers[5].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['crops'] = array;

    array = [];
    containers[6].querySelectorAll('input[type="checkbox"]:checked').forEach(element => {
        array.push(element.value);
    });
    mapObject['varieties'] = array;

    if (!mapObject.destinations.length && !mapObject.types.length && !mapObject.products.length && !mapObject.crops.length && !mapObject.varieties.length)
        document.querySelector('#chart-filters-notify').classList.add('d-none');
    else
        document.querySelector('#chart-filters-notify').classList.remove('d-none');

    return mapObject;

}

const getFiltersChartTableSelected = async (name = false) => {

    const array = [];
    const inputs = document.querySelector(`${getChartIdTab()}-table`).querySelectorAll('input[type="checkbox"]:checked');

    inputs.forEach(element => {
        array.push(!name ? element.value.split(',')[0] : element.value.split(',')[1]);
    });
    
    return array;

}

const setStateChart = (index, state) => {
    if (state)
        chartsStates[index] = true;
    else{

        for (let index = 0; index < charts.length; index++) {
            chartsValues[index] = [];
        }

        chartsStates = [false, false, false, false];
    }
}

const resizeChart = () => {
    setTimeout(() => {
        charts[getChartIndexTab()].updateOptions({
            chart: {
                width: "100%",
            }
        });
    }, 500);
}

const resizeChartHeight = (height) => {
    setTimeout(() => {
        charts[getChartIndexTab()].updateOptions({
            chart: {
                height: height,
            }
        });
    }, 300);
}

const loadVarietiesCompare = async () => {

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadVarietiesCompare",
            type: 'GET',
            data: { 'from' : document.querySelector('#week-from').value, 'to' : document.querySelector('#week-to').value },
            cache: false,

            beforeSend: function() {

            },
    
            error:  function(xhr) {
                console.log(xhr);
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
                                if (selected.value == element.value) element.checked = true;
                            });
                        });

                        showSelectionCompare('compare-varieties', 'compare-varieties-selected', false, true);
                        
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    console.log(error);
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

                        const checkboxs = document.querySelectorAll(`#compare-parameters input[type="checkbox"]`);

                        selecteds.forEach(selected => {
                            for (const element of checkboxs) {
                                if (selected.value == element.value) {
                                    element.checked = true; 
                                    break;
                                }
                            }
                        });

                        showSelectionCompare('compare-parameters', 'compare-parameters-selected', false, true);
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

const showSelectionCompare = (idCheckboxs, idBtn, modal = true, onlyShow = false) => {
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

    if(!onlyShow) loadTableCompare(modal);
}

const compareRemoveSelected = (idCheckboxs, idBtn, value) => {
    event.stopPropagation();

    document.querySelectorAll(`#${idCheckboxs} input[type="checkbox"]:checked`).forEach(element => {
        if (element.value == value) element.checked = false;
    });

    showSelectionCompare(idCheckboxs, idBtn, false);
}

const tableCompareChangeTab = () => {
    //* Ajustar tabla del tab actual
    if (!getTableCompareIndexTab()){
        if (document.querySelector(`#table-compare-resume`))
        new DataTable(document.querySelector(`#table-compare-resume`)).columns.adjust();
    }else{
        if (document.querySelector(`#table-compare`))
        new DataTable(document.querySelector(`#table-compare`)).columns.adjust();
    }
}

const loadTableCompare = async (modal = true) => {

    const filters = await getFiltersChartSelected();
    let varieties = [];
    let parameters = await getFiltersParametersSelected();

    document.querySelectorAll(`#compare-varieties input[type="checkbox"]:checked`).forEach(element => {
        varieties.push(element.value);
    });

    if (varieties.length == 0 || parameters.length == 0) {
        $('#nav-detail').html(`
            <div class="d-flex flex-column justify-content-center align-items-center h-100">
                <i class="bi bi-exclamation-circle display-6"></i>
                <p class="mt-3">Select the items to compare!</p>
            </div>
        `);

        $('#nav-resume').html(`
            <div class="d-flex flex-column justify-content-center align-items-center h-100">
                <i class="bi bi-exclamation-circle display-6"></i>
                <p class="mt-3">No data!</p>
            </div>
        `);

        if (varieties.length == 0 && modal) toogleModal('modal-compare-varieties', true);
        else if (parameters.length == 0 && modal) toogleModal('modal-compare-parameters', true);

        return;
    }

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/loadTableCompare",
            type: 'POST',
            data: { varieties : JSON.stringify(varieties), parameters : JSON.stringify(parameters), from : document.querySelector('#week-from').value, to : document.querySelector('#week-to').value, 'filters' : JSON.stringify(filters) },
            cache: false,

            beforeSend: function() {
                $('#nav-detail').html(`
                    <div class="d-flex flex-column justify-content-center align-items-center h-100">
                        <i class="spiner spinner-border"></i>
                    </div>
                `);

                $('#nav-resume').html(`
                    <div class="d-flex flex-column justify-content-center align-items-center h-100">
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
                        $('#nav-detail').html(objData.res);

                        table = new DataTable('#table-compare', {
                            /* dom: 'Bfrtip',
                            buttons: [
                                'excelHtml5',
                                {
                                    extend: 'pdfHtml5',
                                    title: 'Exported Data',
                                    orientation: 'landscape',
                                    pageSize: 'LEGAL'/* 
                                    exportOptions: {
                                        format: {
                                            body: function (data, row, column, node) {
                                                if (column === 2) {
                                                    // Manejar la columna de imágenes aquí
                                                    // Devolver el contenido de la celda de la imagen
                                                }
                                                return data;
                                            }
                                        }
                                    }
                                }
                            ], */
                            fixedColumns: {
                                left: (isMobile) ? 1 : 3
                            },
                            columnDefs: [
                                {
                                    targets: 1, // Índice de la columna que deseas ocultar (columna 2 en este caso)
                                    visible: false, // Ocultar la columna
                                },{
                                    targets: 4, // Índice de la columna que deseas ocultar (columna 2 en este caso)
                                    visible: false, // Ocultar la columna
                                },{
                                    targets: 5, // Índice de la columna que deseas ocultar (columna 2 en este caso)
                                    visible: false, // Ocultar la columna
                                },{
                                    targets: 6, // Índice de la columna que deseas ocultar (columna 2 en este caso)
                                    visible: false, // Ocultar la columna
                                },{
                                    targets: 7, // Índice de la columna que deseas ocultar (columna 2 en este caso)
                                    visible: false, // Ocultar la columna
                                }
                            ],
                            scrollCollapse: true,
                            "processing": true,
                            "scrollX": true,
                            "iDisplayLength": 50,
                            "stateSave": false,
                            scrollY: '53vh',
                        }).page();

                        tableCompare = $('#table-compare').DataTable();
                        tableCompareData = $('#table-compare').DataTable().rows().data();

                        document.querySelector('#table-compare_filter input').addEventListener('keyup', () => {
                            // Limpiar el temporizador anterior si existe
                            clearTimeout(temporizador);

                            // Configurar un nuevo temporizador para esperar a que el usuario deje de escribir
                            temporizador = setTimeout(loadTableCompareResume, 1000); // 500 milisegundos de espera
                        })
                        
                        if(modal) {
                            let elementoBody = document.body;
                            window.scrollTo(0, elementoBody.scrollHeight - window.innerHeight);

                            if (!isMobile) {
                                document.querySelector('body').classList.add('toggle-sidebar');
                                resizeChart();
                            }
                        }

                        resizeTableMain();
                        filterTableCompareWithChartTable();

                        //console.log(document.querySelectorAll('#table-compare_wrapper .row'));
                    }else{
                        console.log(objData.res);
                        alert(objData.res);
                    }
                } catch (error) {
                    console.log(error);
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });

}

const loadTableCompareResume = async () => {
    
    const parameters = await getFiltersParametersSelected();
    const data = $('#table-compare').DataTable().rows({ search: 'applied' }).data().toArray();
    const dataDifferentOrder = new Set();
    const array = {};

    parameters.forEach(parameter => {

        const value = parameter.split(',');

        if(value[2] != 0 && value[2] != 5 && value[2] != 6 && value[2] != 7) return;

        array[value[1]] = {};
    });

    data.forEach(row => {

        dataDifferentOrder.add(row[1]);
        
        for (let i = 0; i < parameters.length; i++) {

            const parameter = parameters[i].split(',');

            if(parameter[2] != 0 && parameter[2] != 5 && parameter[2] != 6 && parameter[2] != 7) continue;

            const valor = row[i + 8] ? row[i + 8] : 'Empty' ;

            // Contar el valor para la columna correspondiente
            if (!array[parameter[1]][valor]) {
                array[parameter[1]][valor] = 1;
            } else {
                array[parameter[1]][valor]++;
            }

        }

    });

    const week1 = document.querySelector('#week-from').value.split('-W');
    const week2 = document.querySelector('#week-to').value.split('-W');

    const container = document.querySelector('#nav-resume');
    container.innerHTML = `
        <div class="text-center my-4">
            <h1><i class="bi bi-bookmark-check-fill text-success me-2"></i>${dataDifferentOrder.size} orders evaluated</h1>
            <h6><i class="bi bi-calendar-range me-2"></i>Week ${week1[1]}/${week1[0]} to ${week2[1]}/${week2[0]}</h6>

            <div class="resume-filters alert alert-primary col-12 col-md-4 d-flex flex-wrap justify-content-center align-items-center" style="margin:0 auto;"></div>
        </div>
    `;

    const filters = await getFiltersAll();

    container.querySelector('.resume-filters').innerHTML += `
        <div class="me-3 text-secondary fw-semibold">Filters:</div>
        <div class="fs-0-8"><i class="bi bi-airplane-fill me-1"></i>${filters.destinations ? filters.destinations : 'All'}</div><i class="bi bi-grip-vertical mx-2"></i>
        <div class="fs-0-8"><i class="bi bi-building-fill-check me-1"></i>${filters.types ? filters.types : 'All'}</div><i class="bi bi-grip-vertical mx-2"></i>
        <div class="fs-0-8"><i class="bi bi-bookmark-fill me-1"></i>${filters.products ? filters.products : 'All'}</div><i class="bi bi-grip-vertical mx-2"></i>
        <div class="fs-0-8"><i class="bi bi-flower2 me-1"></i>${filters.crops ? filters.crops : 'All'}</div><i class="bi bi-grip-vertical mx-2"></i>
        <div class="fs-0-8"><i class="bi bi-flower3 me-1"></i>${filters.varieties ? filters.varieties : 'All'}</div>
        ${filters.search ? `<i class="bi bi-grip-vertical mx-2"></i><div class="fs-0-8"><i class="bi bi-search me-1"></i>${filters.search}</div>` : ''}
    `;

    const table = document.createElement('table');
    table.id = 'table-compare-resume';
    table.classList.add('table', 'table-bordered', 'table-hover', 'fs-0-8', 'text-center', 'w-100');
    table.style.margin = '0 auto';
    table.innerHTML = `
        <thead>
            <tr>
                <th class="text-center">Parameter</th>
                <th class="text-center">Selections</th>
            </tr>
        </thead>
        <tbody></tbody>
    `;

    for (const columna in array) {
        const tr = document.createElement('tr');
        const th = document.createElement('th');
        th.classList.add('align-middle');
        th.innerHTML = `<span class="badge bg-success bg-opacity-10 text-dark fs-0-8 fw-semibold">${columna}</span>`;

        const td = document.createElement('td');
        td.innerHTML = `
            <table class="table table-bordered table-striped-columns fs-0-8 text-center mb-0 w-auto bg-white" style="margin: 0 auto;">
                <tr></tr>
                <tr></tr>
            </table>
        `;

        const values = array[columna];

        for (const value in values) {
            td.querySelectorAll('tr')[0].innerHTML += `<td>${value}</td>`;
            td.querySelectorAll('tr')[1].innerHTML += `<td>${values[value]}</td>`;
        }

        tr.append(th, td);

        table.querySelector('tbody').appendChild(tr);
    }

    container.appendChild(table);
    
    new DataTable(table, {
        fixedColumns: {
            left: 1
        },
        scrollCollapse: true,
        "processing": true,
        "scrollX": true,
        "iDisplayLength": 100,
        "stateSave": false,
        scrollY: '47vh',
        paging: false,
        searching: false,
        info: false,
        ordering: false
    }).page();

}

const getFiltersAll = async () => {
    const destinations = Array.from(document.querySelectorAll('#filters-chart-destinations input[type="checkbox"]:checked')).map(checkbox => checkbox.value.split(',')[1]).join(', ');
    const types = Array.from(document.querySelectorAll('#filters-chart-types input[type="checkbox"]:checked')).map(checkbox => checkbox.value.split(',')[1]).join(', ');
    const products = Array.from(document.querySelectorAll('#filters-chart-products input[type="checkbox"]:checked')).map(checkbox => checkbox.value.split(',')[1]).join(', ');
    let crops = Array.from(document.querySelectorAll('#filters-chart-crops input[type="checkbox"]:checked')).map(checkbox => checkbox.value.split(',')[1]).join(', ');
    let varieties = Array.from(document.querySelectorAll('#filters-chart-varieties input[type="checkbox"]:checked')).map(checkbox => checkbox.value.split(',')[1]).join(', ');

    if (getChartIndexTab() == 2) {
        const filters = await getFiltersChartTableSelected(true);
        if (filters.length)
        crops = Array.from(filters).map(value => value).join(', ');
    }

    if (getChartIndexTab() == 3) {
        const filters = await getFiltersChartTableSelected(true);
        if (filters.length)
        varieties = Array.from(filters).map(value => value).join(', ');
    }

    const map = {
        'destinations' : destinations,
        'types' : types,
        'products' : products,
        'crops' : crops,
        'varieties' : varieties,
        'search' : document.querySelector('#table-compare_filter input').value
    }

    return map;
}

const filterTableCompareWithChartTable = async () => {

    if (!tableCompareData) return;

    const filters = await getFiltersChartTableSelected();

    const table = $('#table-compare').DataTable();
    const data = tableCompareData;

    // Filtrar los datos basados en la condición
    const dataFilter = data.filter(row => {
        // Supongamos que la condición es que el valor en la segunda columna sea mayor a 50
        return filters.includes(row[4 + getChartIndexTab()]);
    });

    // Limpiar la tabla actual
    table.clear();

    // Agregar los datos filtrados a la tabla
    table.rows.add(filters.length ? dataFilter : tableCompareData);

    // Redibujar la tabla
    table.draw();

    loadTableCompareResume();
}

const getFiltersParametersSelected = async () => {

    const array = [];
    const inputs = document.querySelectorAll(`#compare-parameters input[type="checkbox"]:checked`);

    inputs.forEach(element => {
        array.push(element.value);
    });
    
    return array;

}

const viewImage = (element) => {
    let tr = element.parentNode.parentNode;
    let tds = tr.querySelectorAll('td, th');

    let contIndicators = document.querySelector('#images-compare-indicators');
    let contImages = document.querySelector('#images-compare');
    contImages.innerHTML = null;
    contIndicators.innerHTML = null;

    document.querySelector('#modalImageLabel').innerHTML = `${tds[1].innerText} / ${tds[0].innerText}`;

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
                        src="${image.dataset.url}" 
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

async function viewReport() {

    const dataCompare = $('#table-compare').DataTable().rows({ search: 'applied' }).data().toArray();
    const dataCompareResume = $('#table-compare-resume').DataTable().rows().data().toArray();
    //const dataChart = $(`#table-chart-${getChartIndexTab()}`).DataTable().rows().data().toArray();
    const parameters = await getFiltersParametersSelected();

    const tableChart = document.querySelector(`#table-chart-${getChartIndexTab()}`);
    const tableChartRows = tableChart.querySelectorAll('tbody tr');
    const isChartFilter = await getFiltersChartTableSelected();
    const dataChart = [];

    tableChartRows.forEach(function (row) {
        var dataRow = [];
        var celds = row.querySelectorAll('td');

        if (isChartFilter.length)
            if (!celds[0].querySelector('input').checked) return;
      
        celds.forEach(function (celd) {
            dataRow.push(celd.textContent.trim()); // Obtener el texto de la celda
        });
      
        dataChart.push(dataRow);
    });

    const dataCompareResumeModify = dataCompareResume.map(row => [
        dataCompareResumeEdit(row[0]),
        row[1] // Dejamos la segunda columna sin cambios
    ]);

    charts[getChartIndexTab()].dataURI().then(async ({ imgURI, _ }) => {

        const loading = document.querySelector('#full-loading');
        loading.classList.remove('d-none');

        // Crear un objeto XMLHttpRequest
        const xhr = new XMLHttpRequest();

        // Configurar el objeto XMLHttpRequest
        xhr.open('POST', `${base_url}/Pdf/report`, true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        // Enviar los datos al servidor
        xhr.send(JSON.stringify({
            'week1' : document.querySelector('#week-from').value,
            'week2' : document.querySelector('#week-to').value,
            'dataCompare' : dataCompare,
            'dataCompareResume' : dataCompareResumeModify,
            'dataChart' : dataChart,
            'parameters' : parameters,
            'filters' : await getFiltersAll(),
            'image' : imgURI
        }));

        // Escuchar la respuesta del servidor
        xhr.onload = function() {
            //console.log(xhr);
            // Si la respuesta es correcta
            if (xhr.status === 200 && xhr.responseText == '') {
                window.open(base_url + '/temp/reporte.pdf', '_blank');
            }else{
                console.log(xhr.responseText);
                alert(xhr.responseText);
            }

            loading.classList.add('d-none');
        };

    });

}


// Función para eliminar clases específicas de una cadena HTML
function dataCompareResumeEdit(stringHTML) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(stringHTML, 'text/html');
  
    // Obtener el elemento principal (en este caso, el <span>)
    const element = doc.body.firstChild;
  
    // Eliminar clases específicas
    element.classList.remove('badge', 'bg-success', 'bg-opacity-10');
  
    // Devolver la cadena HTML modificada
    return element.outerHTML;
  }

const resizeTable = () => {

    const mains = document.querySelectorAll('.main:not(#main)');

    mains.forEach(main => {
        if (main.style.display !== 'none') {

            let tables = main.querySelectorAll('table.dataTable');

            for (let index = 1; index < tables.length; index = index + 2) {
                setTimeout(() => {
                    let tableCompare = $(tables[index]).DataTable();
                    tableCompare.columns.adjust().draw();
                }, 300);
            }

        }
    });

}

const resizeTableMain = () => {

    //* Ajustar tabla del tab actual
    const index = getChartIndexTab();
    if (document.querySelector(`#table-chart-${index}`))
    setTimeout(() => {
        new DataTable(document.querySelector(`#table-chart-${index}`)).columns.adjust();
    }, 300);

    if (!getTableCompareIndexTab()){
        if (document.querySelector(`#table-compare-resume`))
        setTimeout(() => {
            new DataTable(document.querySelector(`#table-compare-resume`)).columns.adjust();
        }, 300);
    }else{
        if (document.querySelector(`#table-compare`))
        setTimeout(() => {
            new DataTable(document.querySelector(`#table-compare`)).columns.adjust();
        }, 500);
    }

}

function scrollToTableRow(tableId, trId) {

    //const table = document.querySelector(tableId);
    if (!document.querySelector(tableId) || !document.getElementById(trId)) return;

    // Obtiene el contenedor y el elemento deseado
    const contenedor = document.querySelector(tableId);
    const elementoDeseado = document.getElementById(trId);

    const scrollOptions = {
        top: elementoDeseado.offsetTop - contenedor.offsetTop,
        //behavior: 'smooth'
        behavior: 'auto'
    };
    contenedor.scrollTo(scrollOptions);
}

async function showOption($this, container){

    let search = document.querySelector('.search-bar');
    let searchBtn = document.querySelector('.search-bar-toggle');
    
    if (isMobile) {
        document.querySelector('body').classList.remove('toggle-sidebar');
    }

    search.classList.add('d-none');
    searchBtn.classList.add('d-none');

    $('.main').fadeOut(0);
    $('.nav-link').addClass('collapsed');

    $($this).removeClass('collapsed');
    $('#'+container).fadeIn(0);

    switch (container) {

        case 'main':
            
            if (!isMobile) {
                document.querySelector('body').classList.add('toggle-sidebar');
                resizeTableMain();
            }

            break;

        case 'main-calendar':
            
            if (!isMobile) document.querySelector('body').classList.add('toggle-sidebar');
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