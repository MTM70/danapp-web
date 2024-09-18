let varietyProcess = 1;
let imgVariety, imgVariety2;

$(document).ready(function() {
    //TODO Varieties
    $("#form-variety").on("submit", function(e){
        e.preventDefault();

        let formData = new FormData(document.getElementById("form-variety"));
        formData.append('process', varietyProcess);

        $.ajax({
            url: base_url+"/Dashboard/setVariety",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#variety-btn').html('Loading...');
                $('#form-variety :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#form-variety :input').attr('disabled', false);
                $('#variety-btn').html('Save changes');
            },
        })
        .done(function(response){
            try {
                var objData = JSON.parse(response);
 
                if(objData.status == true){
                    alert(objData.res);

                    toogleModal('modalAddVariety', false);
                    loadVarieties();
                    resetVarietyForm();
                }else{
                    alert(objData.res);
                } 
            } catch (error) {
                alert(error);   
            }
            

            $('#variety-btn').html('Save changes');
            $('#form-variety :input').attr('disabled', false);
        });
    });

    imgVariety = document.querySelector('#variety-image');
    document.querySelector('#variety-file').addEventListener('change', function() {

        if (this.files && this.files[0]) {

            imgVariety.onload = () => {
                URL.revokeObjectURL(imgVariety.style.backgroundImage);  // no longer needed, free memory
            }

            imgVariety.querySelector('i').classList.add('d-none');

            imgVariety.style.backgroundImage = `url(${URL.createObjectURL(this.files[0])})`; // set src to blob url

        }else {
            imgVariety.style.backgroundImage = null;
            imgVariety.querySelector('i').classList.remove('d-none');
        }
    });

    imgVariety2 = document.querySelector('#variety-image-2');
    document.querySelector('#variety-file-2').addEventListener('change', function() {

        if (this.files && this.files[0]) {

            imgVariety2.onload = () => {
                URL.revokeObjectURL(imgVariety2.style.backgroundImage);  // no longer needed, free memory
            }

            imgVariety2.querySelector('i').classList.add('d-none');

            imgVariety2.style.backgroundImage = `url(${URL.createObjectURL(this.files[0])})`; // set src to blob url

        }else {
            imgVariety2.style.backgroundImage = null;
            imgVariety2.querySelector('i').classList.remove('d-none');
        }
    });
});

//TODO Varieties
async function loadVarieties() {

    return new Promise(resolve => {
        stateVarieties = true;

        $.ajax({
            url: base_url+"/Dashboard/loadVarieties",
            cache: false,

            beforeSend: function() {
                $('#varieties-loading').removeClass('d-none');
            },

            error:  function(xhr) {
                alert(xhr);
                $('#varieties-loading').addClass('d-none');
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#varieties').html(objData.res);
                        new DataTable('#table-varieties', {
                            "processing": true,
                            "scrollY": false,
                            "scrollX": (isMobile) ? true : false,
                            "iDisplayLength": 25,
                            "stateSave": true,
                            order: [[2, 'asc']],
                            scrollY: '53vh',
                        }).page();

                        $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                            window.scrollTo(0, 0);
                        });

                        document.querySelectorAll('#table-varieties_wrapper .row')[2].classList.add('mt-3');
                        resetVarietyForm();
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                $('#varieties-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });
}

async function openModalVariety() {
    if (varietyProcess == 2) {
        resetVarietyForm();
    }

    document.getElementById('modalAddVarietyLabel').innerText = 'Add variety';
    varietyProcess = 1;
    
}

async function loadVarietyEdit(id) {

    varietyProcess = 2;
    document.getElementById('modalAddVarietyLabel').innerText = 'Update variety';
    resetVarietyForm();

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/dashboard/loadVarietyEdit",
            type: 'GET',
            data: {'id': id},
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                //$('#parameter-crops').html('Error');
            },

            success:  async function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        let data = JSON.parse(objData.res);

                        document.getElementById('variety-id').value = data['id'];
                        document.getElementById('variety-number').value = data['variety_code'];
                        document.getElementById('variety-name').value = data['variety'];
                        //document.getElementById('variety-file-path').value = data['img'];

                        if (data['img']) {
                            imgVariety.querySelector('i').classList.add('d-none');
                            imgVariety.style.backgroundImage = `url(${base_url}/assets/img/varieties/${data['img']})`;
                            document.getElementById('variety-file-path').value = data['img'];
                        }else {
                            imgVariety.style.backgroundImage = null;
                            imgVariety.querySelector('i').classList.remove('d-none');
                            document.getElementById('variety-file-path').value = '';
                        }

                        if (data['img2']) {
                            imgVariety2.querySelector('i').classList.add('d-none');
                            imgVariety2.style.backgroundImage = `url(${base_url}/assets/img/varieties/${data['img2']})`;
                            document.getElementById('variety-file-path-2').value = data['img2'];
                        }else {
                            imgVariety2.style.backgroundImage = null;
                            imgVariety2.querySelector('i').classList.remove('d-none');
                            document.getElementById('variety-file-path-2').value = '';
                        }
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

function resetVarietyForm(){
    
    /* document.querySelectorAll("#user-sec-custs option").forEach(opt => {
        opt.disabled = false;
    }); */

    document.getElementById('variety-number').value = '';
    document.getElementById('variety-name').value = '';
    document.getElementById('variety-file').value = '';
    document.getElementById('variety-file-2').value = '';
    
    imgVariety.style.backgroundImage = null;
    imgVariety.querySelector('i').classList.remove('d-none');
    document.getElementById('variety-file-path').value = '';

    imgVariety2.style.backgroundImage = null;
    imgVariety2.querySelector('i').classList.remove('d-none');
    document.getElementById('variety-file-path-2').value = '';
}
//TODO Varieties