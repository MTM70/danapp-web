let eventProcess = 1;
let imgEvent;

let idEventActual = 0;
let yearActual = 0;

let uploadMap;
let eventoChange;
let mapIssues = 0;
let mapOptionsSelected = [];
let uploadMapNameHist = '';
let isEditMap = false;

$(document).ready(function() {

    $("#form-event").on("submit", function(e){
        e.preventDefault();

        let formData = new FormData(document.getElementById("form-event"));
        formData.append('process', eventProcess);

        $.ajax({
            url: base_url+"/Dashboard/setEvent",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#event-btn').html('Loading...');
                $('#form-event :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#form-event :input').attr('disabled', false);
                $('#event-btn').html('Save changes');
            },
        })
        .done(function(response){
            try {
                const objData = JSON.parse(response);

                if(objData.status == true){
                    alert(objData.res);

                    toogleModal('modalAddEvent', false);
                    loadEvents();
                }else{
                    alert(objData.res);
                } 
            } catch (error) {
                alert(error);   
            }
            

            $('#event-btn').html('Save changes');
            $('#form-event :input').attr('disabled', false);
        });
        
    });

    $("#form-event-add-year").on("submit", function(e){
        e.preventDefault();

        let formData = new FormData(document.getElementById("form-event-add-year"));

        $.ajax({
            url: base_url+"/Dashboard/setEventYear",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#event-add-year-btn').html('Loading...');
                $('#form-event-add-year :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#form-event-add-year :input').attr('disabled', false);
                $('#event-add-year-btn').html('Add year');
            },
        })
        .done(function(response){
            try {
                const objData = JSON.parse(response);

                if(objData.status == true){
                    alert(objData.res);

                    openModalViewEvent(document.querySelector('#event-add-year-id').value, false);
                }else{
                    alert(objData.res);
                } 
            } catch (error) {
                alert(error);   
            }
            

            $('#event-add-year-btn').html('Add year');
            $('#form-event-add-year :input').attr('disabled', false);
        });
        
    });

    uploadMap = document.getElementById("upload-map");
    eventoChange = new Event("change", { bubbles: true });

    document.querySelector('#upload-map').addEventListener('change', function(event) {

        const file = event.target.files[0];

        if (uploadMapNameHist != file.name) {
            mapOptionsSelected = [];
            uploadMapNameHist = file.name;
        }

        const formData = new FormData();
        formData.append('excel', file);
        formData.append('selected', JSON.stringify(mapOptionsSelected));

        $.ajax({
            url: base_url+"/Dashboard/uploadFileMap",
            type: "POST",
            //dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                document.querySelector('#upload-map').classList.add('disabled');
                $('#event-year-map').html('<div class="h-75 d-flex justify-content-center align-items-center"><i class="spinner-border spinner-border-sm"></i></div>');
            },

            error:  function(xhr) {
                alert(xhr.response);
                console.log(xhr);
                document.querySelector('#upload-map').classList.remove('disabled');
            },
        })
        .done(function(response){
            const objData = JSON.parse(response);

            if(objData.status == true){

                $('#event-year-map').html(objData.res);
                mapOptionsSelected = objData.selected;

                mapIssues = objData.issues;
                if (objData.issues > 0) document.querySelector('#map-issues').innerHTML = `<span class="alert alert-danger p-2">${objData.issues} issues!</span>`;
                else document.querySelector('#map-issues').innerHTML = `<button class="btn btn-success" onclick="setEventMap(this)">Upload<i class="spinner-grow spinner-grow-sm ms-2"></i></button>`;

                if (!document.querySelector('#table-upload-map')) return;

                const table = new DataTable('#table-upload-map', {
                    "processing": true,
                    "scrollY": false,
                    "scrollX": (isMobile) ? true : false,
                    "iDisplayLength": 100,
                    "stateSave": true,
                    scrollY: '62vh',
                }).page();

                $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                    window.scrollTo(0, 0);
                });

            }else{
                alert(objData.res);
                $('#event-year-map').html(null);
            }

            document.querySelector('#event-year-map').classList.remove('disabled');
        });

    });

    imgEvent = document.querySelector('#event-image');

    document.querySelector('#event-file').addEventListener('change', function() {

        if (this.files && this.files[0]) {

            imgEvent.onload = () => {
                URL.revokeObjectURL(imgEvent.style.backgroundImage);  // no longer needed, free memory
            }

            imgEvent.querySelector('i').classList.add('d-none');

            imgEvent.style.backgroundImage = `url(${URL.createObjectURL(this.files[0])})`; // set src to blob url

        }else {
            imgEvent.style.backgroundImage = null;
            imgEvent.querySelector('i').classList.remove('d-none');
        }
    });

    document.querySelector('#editModeMap').addEventListener('change', function() {

        isEditMap = this.checked;
        this.disabled = true;
        openModalViewEventMap();
        
    });

});

function openModalEvent() {
    if (eventProcess == 2) {
        resetEventForm();
    }

    document.getElementById('modalAddEventLabel').innerText = 'Add event';
    eventProcess = 1;
    
}

//TODO Events
async function loadEvents() {

    return new Promise(resolve => {
        stateEvents = true;

        $.ajax({
            url: base_url+"/dashboard/loadEvents",
            cache: false,

            beforeSend: function() {
                $('#events-loading').removeClass('d-none');
            },
    
            error:  function(xhr) {
                alert(xhr);
                $('#events-loading').addClass('d-none');
            },

            success:  function(response) {
                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#events').html(objData.res);
                        /* let table = new DataTable('#table-events', {
                            "processing": true,
                            "scrollY": false,
                            "scrollX": (isMobile) ? true : false,
                            "iDisplayLength": 10,
                            "stateSave": true,
                        }).page();

                        $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                            window.scrollTo(0, 0);
                        });

                        document.querySelectorAll('#table-events_wrapper .row')[2].classList.add('mt-3'); */

                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                $('#events-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });

}

async function loadEventEdit(id, event) {

    event.preventDefault();
    event.stopPropagation();

    eventProcess = 2;
    document.getElementById('modalAddEventLabel').innerText = 'Update event';

    resetEventForm();

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/dashboard/loadEventEdit",
            type: 'GET',
            data: {'id': id},
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                //$('#parameter-crops').html('Error');
            },

            success:  function(response) {
                try {
                    const objData = JSON.parse(response);

                    if(objData.status == true){
                        let data = JSON.parse(objData.res);

                        document.getElementById('event-id').value = data['id'];
                        document.getElementById('event-name').value = data['name'];
                        document.getElementById('event-start').value = data['start_week'];
                        document.getElementById('event-end').value = data['end_week'];
                        document.getElementById('event-description').value = data['description'];
                        document.getElementById('event-state').value = data['state'];

                        if (data['image']) {
                            imgEvent.querySelector('i').classList.add('d-none');
                            imgEvent.style.backgroundImage = `url(${base_url}/uploads/events/${data['image']})`;
                            document.getElementById('event-file-path').value = data['image'];
                        }else {
                            imgEvent.style.backgroundImage = null;
                            imgEvent.querySelector('i').classList.remove('d-none');
                            document.getElementById('event-file-path').value = '';
                        }
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    console.log(response);
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });
}

function openModalViewEvent(id, title) {

    if (title) {
        document.getElementById('modalViewEventLabel').innerText = title;
        document.getElementById('modalViewEventMapLabel').innerText = title;
    }
    document.querySelector('#event-add-year-id').value = id;
    if (title) toogleModal('modalViewEvent', true);
    $('#event-years').html(null);

    $.ajax({
        url: base_url+"/dashboard/loadEventYears",
        type: 'GET',
        data: {'id': id},
        cache: false,

        beforeSend: function() {
            
        },

        error:  function(xhr) {
            //$('#parameter-crops').html('Error');
        },

        success:  function(response) {
            try {
                const objData = JSON.parse(response);

                if(objData.status == true){
                    $('#event-years').html(objData.res);

                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
                }else{
                    alert(objData.res);
                }

                setYearsEvent();
            } catch (error) {
                console.log(response);
                alert(error);
            }
        }
    });
    
}

function openModalViewEventMap(idEvent = idEventActual, year = yearActual) {

    idEventActual = idEvent;
    yearActual = year;

    const title = document.getElementById('modalViewEventMapLabel').innerText.split(' ')[0];

    document.getElementById('modalViewEventMapLabel').innerText = `${title} ${year}`;
    document.querySelector('#generate-qr-btn').href = `${base_url}/Qr/qr/74e4bc032c5f9ea4d0130ef131c4e802/${idEvent}/${year}/${document.querySelector('#modalViewEventMapLabel').innerText.replace(' ', '-')}`;

    document.querySelector('#upload-map').value = null;
    document.querySelector('#map-issues').innerHTML = null;

    $.ajax({
        url: base_url+"/dashboard/loadEventMap",
        type: 'GET',
        data: { 'id_event': idEvent, 'year' : year, 'isEditMap' : isEditMap },
        cache: false,

        beforeSend: function() {
            $('#event-year-map').html('<div class="h-75 d-flex justify-content-center align-items-center"><i class="spinner-border spinner-border-sm"></i></div>');
        },

        error:  function(xhr) {
            //$('#parameter-crops').html('Error');
        },

        success:  function(response) {
            try {
                const objData = JSON.parse(response);

                if(objData.status == true){
                    setTimeout(() => {
                        $('#event-year-map').html(objData.res);

                        if (!document.querySelector('#table-event-map')) return;

                        const table = new DataTable('#table-event-map', {
                            "processing": true,
                            /* "ordering": false, */
                            "scrollY": false,
                            "scrollX": (isMobile) ? true : false,
                            "iDisplayLength": 50,
                            "stateSave": true,
                            scrollY: '60vh',
                        }).page();
    
                        $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                            window.scrollTo(0, 0);
                        });
    
                        document.querySelectorAll('#table-event-map_wrapper .row')[2].classList.add('mt-3');
                    }, 500);
                }else{
                    alert(objData.res);
                }
            } catch (error) {
                console.log(response);
                alert(error);
            }

            document.querySelector('#editModeMap').disabled = false;
        }
    })
    
}

const updateVarietyMap = async (item, idMap) => {

    // Obtiene el elemento option seleccionado
    const selectedOption = item.options[item.selectedIndex];

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/updateVarietyMap",
            type: "POST",
            data: { 'idEvent': idEventActual, 'year' : yearActual, 'idMap' : idMap, 'idVariety' : item.value },
            cache: false,

            beforeSend: function() {
                //$('#event-add-year-btn').html('Loading...');
                //$('#form-event-add-year :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                //$('#form-event-add-year :input').attr('disabled', false);
                //$('#event-add-year-btn').html('Add year');
                resolve(false);
            },
        })
        .done(function(response){
            try {
                const objData = JSON.parse(response);

                if(objData.status == true){
                    alert(objData.res);

                    item.parentNode.parentNode.querySelectorAll('th, td')[3].innerHTML = selectedOption.getAttribute('data-crop');

                    resolve('true');
                }else{
                    alert(objData.res);
                    resolve(false);
                } 
            } catch (error) {
                alert(error);   
                resolve(false);
            }
        });
    });

}

const setVarietyFromMap = async (idForm) => {

    const formData = new FormData(document.getElementById(idForm));

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/setVarietyFromMap",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,

            beforeSend: function() {
                //$('#event-add-year-btn').html('Loading...');
                //$('#form-event-add-year :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                //$('#form-event-add-year :input').attr('disabled', false);
                //$('#event-add-year-btn').html('Add year');
                resolve(false);
            },
        })
        .done(function(response){
            try {
                const objData = JSON.parse(response);

                if(objData.status == true){
                    alert(objData.res);

                    //openModalViewEvent(document.querySelector('#event-add-year-id').value, false);
                    //openModalViewEventMap();
                    uploadMap.dispatchEvent(eventoChange);

                    resolve('true');
                }else{
                    alert(objData.res);
                    resolve(false);
                } 
            } catch (error) {
                alert(error);   
                resolve(false);
            }
        });
    });
}

const setEventMap = async (item) => {

    const confirmUser = confirm('This action deletes previous upload records for this event and year. Do you want to continue?');

    if (confirmUser === false) return;

    return new Promise(resolve => {
        $.ajax({
            url: base_url+"/Dashboard/setEventMap",
            type: 'POST',
            data: {'idEvent' : idEventActual, 'year' : yearActual, 'data': JSON.stringify(mapOptionsSelected)},
            cache: false,
    
            beforeSend: function() {
                item.disabled = true;
            },
    
            error:  function(xhr) {
                //$('#parameter-crops').html('Error');
                item.disabled = false;
                resolve(false);
            },
    
            success:  function(response) {
                try {
                    const objData = JSON.parse(response);
    
                    if(objData.status == true){
                        alert(objData.res);
    
                        openModalViewEventMap();
                        openModalViewEvent(idEventActual, '');
                    }else{
                        alert(objData.res);
                    }
    
                    resolve('resolve');
                } catch (error) {
                    console.log(response);
                    alert(error);
                    resolve(false);
                }

                item.disabled = false;
            }
        });
    });
}

const selectedOptionMap = (item, isForm, row) => {
    if (!isForm){

        item.parentNode.parentNode.parentNode.parentNode.parentNode.classList.remove('bg-warning');
        item.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('bg-success');

        item.parentNode.parentNode.parentNode.parentNode.querySelectorAll('form input, form select, form button').forEach(element => {
            element.disabled = true;
        });
    }else{

        item.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.classList.remove('bg-success');
        item.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('bg-warning');

        item.parentNode.parentNode.parentNode.parentNode.querySelectorAll('form input, form select, form button').forEach(element => {
            element.disabled = false;
        });

    }

    if (!isForm){

        let stateRow = false;
        mapOptionsSelected.forEach(mapOption => {
            if(mapOption.row == row){
                mapOption.value = item.value;

                if (mapOption.isForm != isForm) 
                    if(!isForm) mapIssues--;
                    else mapIssues++;

                mapOption.isForm = isForm;
                stateRow = true;

                return;
            }
        });

        if (!stateRow) {

            const rowValues = (!isForm) ? item.parentNode.parentNode.parentNode.parentNode.parentNode.querySelectorAll('th, td') : item.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.querySelectorAll('th, td') ;
            
            let map = {};
            map.row = row;
            map.greenhouse = rowValues[1].innerText.trim();
            map.position = rowValues[2].innerText;
            map.management = rowValues[4].innerText.trim();
            map.value = item.value;
            map.isForm = isForm;

            mapOptionsSelected.push(map);
            mapIssues--;   
        }

    }else{
        mapOptionsSelected = mapOptionsSelected.filter(objeto => objeto.row !== row);
    }

    if (mapIssues > 0) document.querySelector('#map-issues').innerHTML = `<span class="alert alert-danger p-2">${mapIssues} issues!</span>`;
    else document.querySelector('#map-issues').innerHTML = `<button class="btn btn-success" onclick="setEventMap(this)">Upload<i class="spinner-grow spinner-grow-sm ms-2"></i></button>`;
}

const setYearsEvent = () => {
    let yearActual = new Date().getFullYear();

    let inputYear = document.querySelector('#event-add-year-year');
    inputYear.innerHTML = null;

    let trs = document.querySelectorAll('#event-years tr');
    if (trs.length) yearActual = parseInt(trs[trs.length - 1].querySelector('td').innerText) + 1;

    for (let year = yearActual; year < (yearActual + 3); year++) {

        let option = document.createElement('option')
        option.innerText = year;
        option.value = year;
        
        inputYear.appendChild(option);
        
    }
}

function downloadDataEventByYear(year, idEvent) {
    window.open(base_url+'/Dashboard/downloadDataEventByYear/'+year+'/'+idEvent, '_blank');
}

function resetEventForm(){
    document.getElementById('event-name').value = '';
    document.getElementById('event-start').selectedIndex = 0;
    document.getElementById('event-end').selectedIndex = 0;
    document.getElementById('event-description').value = '';
    document.getElementById('event-state').selectedIndex = 0;

    imgEvent.style.backgroundImage = null;
    imgEvent.querySelector('i').classList.remove('d-none');
    document.getElementById('event-file-path').value = '';
}