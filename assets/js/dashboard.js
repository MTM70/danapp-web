let stateParameters = false;
let stateUsers = false;

$(document).ready(function() {
    //upload data
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


    //download data
    $("#form-download").on("submit", function(e){
        e.preventDefault();

        window.open(base_url+'/Dashboard/downloadData/'+$('#week-from').val()+'/'+$('#week-to').val(), '_blank');
    });
});

function loadParameters() {

    stateParameters = true;

    $.ajax({
        url: base_url+"dashboard/loadParameters",
        cache: false,

        success:  function(response) {
            var objData = JSON.parse(response);

            if(objData.status == true){
                $('#parameters').html(objData.res);
                let table = new DataTable('#table-parameters', {
                    "processing": true,
                    "scrollY": false,
                    "iDisplayLength": 10,
                    "stateSave": true,
                }).page();

                $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                    window.scrollTo(0, 0);
                });
            }else{
                alert(objData.res);
            }
        }
    })
}

function loadUsers() {

    stateUsers = true;

    $.ajax({
        url: base_url+"dashboard/loadUsers",
        cache: false,

        success:  function(response) {
            var objData = JSON.parse(response);

            if(objData.status == true){
                $('#users').html(objData.res);
                let table = new DataTable('#table-users', {
                    "processing": true,
                    "scrollY": false,
                    "iDisplayLength": 10,
                    "stateSave": true,
                }).page();

                $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                    window.scrollTo(0, 0);
                });
            }else{
                alert(objData.res);
            }
        }
    })
}

function showOption($this, container){
    $('.main').fadeOut(0);
    $('.nav-link').addClass('collapsed');

    $($this).removeClass('collapsed');
    $('#'+container).fadeIn(0);

    switch (container) {
        case 'main-parameters':
            
            if (!stateParameters) {
                loadParameters();
            }

            break;

        case 'main-users':
            
            if (!stateUsers) {
                loadUsers();
            }

            break;
    }
}