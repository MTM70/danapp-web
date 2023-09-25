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
            console.log(response);
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


    //TODO download data
    $("#form-download").on("submit", function(e){
        e.preventDefault();

        if (document.querySelector('#week-from').value > document.querySelector('#week-to').value) {
            alert('start date greater than end date');
            return;
        }

        window.open(base_url+'/Dashboard/downloadData2/'+$('#week-from').val()+'/'+$('#week-to').val(), '_blank');
    });
});

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
            

            document.querySelector('body').classList.add('toggle-sidebar');
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