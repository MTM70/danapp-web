let eventProcess = 1;
let imgEvent;

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
            console.log(response);
            try {
                var objData = JSON.parse(response);

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
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#events').html(objData.res);
                        /* let table = new DataTable('#table-events', {
                            "processing": true,
                            "scrollY": false,
                            "scrollX": (isMobile()) ? true : false,
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
                    var objData = JSON.parse(response);

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

    document.getElementById('modalViewEventLabel').innerText = title;
    toogleModal('modalViewEvent', true);
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
                var objData = JSON.parse(response);

                if(objData.status == true){
                    $('#event-years').html(objData.res);
                }else{
                    alert(objData.res);
                }
            } catch (error) {
                console.log(response);
                alert(error);
            }
        }
    })
    
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