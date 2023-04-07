var parameterCrops = Array();
var stateOptions = false;
var parameterProcess = 1;

$(document).ready(function() {
    //TODO Parameters
    countOptionsParameter();

    $("#parameter-type").change(function(){
        if (this.value == 1) $(".parameter-more").removeClass('d-none');
        else $(".parameter-more").addClass('d-none');

        if (this.value == 1 || this.value == 2) $("#parameter-all-cont").removeClass('d-none');
        else $("#parameter-all-cont").addClass('d-none');

        if (this.value == 5 || this.value == 6 || this.value == 7) {
            $("#parameter-options-cont").removeClass('d-none');
            stateOptions = true;
        }else{
            $("#parameter-options-cont").addClass('d-none'); 
            $("#parameter-options").html(null);
            countOptionsParameter();
            stateOptions = false;
        }
    });

    $("#form-parameter").on("submit", function(e){
        e.preventDefault();

        let options = Array();
        let empty = false;
        $('#parameter-options input[type="text"]').each(function() {

            if (!this.value && parameterProcess == 2) {
                alert('empty option value!');
                empty = true;
                return;
            }

            if (this.value) options.push(this.value);
        });

        if (empty) return;

        if (stateOptions && !options.length) {
            alert('Add options!');
            return;
        }

        if (hasDuplicates(options)) {
            alert('Duplicate options!');
            return;
        }

        if (parameterProcess == 2) {
            options = Array();
            $('#parameter-options input[type="text"]').each(function() {
                options.push(`${this.getAttribute('data-id')},${this.value},${this.getAttribute('data-enabled')}`);
            });
        }

        if (!parameterCrops.length) {
            alert('Assigned crops!');
            return;
        }

        let formData = new FormData(document.getElementById("form-parameter"));
        formData.append('crops', JSON.stringify(parameterCrops));
        formData.append('options', JSON.stringify(options));
        formData.append('process', parameterProcess);

        $.ajax({
            url: base_url+"/Dashboard/setParameter",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#parameter-btn').html('Loading...');
                $('#form-parameter :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#form-parameter :input').attr('disabled', false);
                $('#parameter-btn').html('Save changes');
            },
        })
        .done(function(response){
            console.log(response);
            try {
                var objData = JSON.parse(response);

                if(objData.status == true){
                    alert(objData.res);

                    toogleModal('modalAddParamter', false);
                    loadParameters();
                }else{
                    alert(objData.res);
                } 
            } catch (error) {
                alert(error);   
            }
            

            $('#parameter-btn').html('Save changes');
            $('#form-parameter :input').attr('disabled', false);
        });
        
    });
});

//TODO Parameters
async function loadParameters() {

    return new Promise(resolve => {
        stateParameters = true;

        $.ajax({
            url: base_url+"dashboard/loadParameters",
            cache: false,

            beforeSend: function() {
                $('#parameters-loading').removeClass('d-none');
            },
    
            error:  function(xhr) {
                alert(xhr);
                $('#parameters-loading').addClass('d-none');
            },

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

                $('#parameters-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });

}

async function loadCrops() {

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"dashboard/loadCrops",
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                $('#parameter-crops').html('Error');
            },

            success:  function(response) {
                if (!response.includes('DOCTYPE')) {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#parameter-crops').html(objData.res);
                    }else{
                        alert(objData.res);
                    }   
                }else{
                    $('#parameter-crops').html(response);
                }

                resolve('resolved');
            }
        })
    });

}

function openModalParameter() {
    if (parameterProcess == 2) {
        resetParameterForm();
    }

    document.getElementById('parameter-state-cont').classList.add('d-none');
    document.getElementById('modalAddParamterLabel').innerText = 'Add parameter';
    parameterProcess = 1;
    
}

function selectCrops($this) {

    if ($this.value == 17) {

        if($this.checked) parameterCrops = [17];
        else {
            for (let index = 0; index < parameterCrops.length; index++) {
                if (parameterCrops[index] == $this.value){
                    parameterCrops.splice(index, 1);
                    break;
                }
            }
        }

        $('#parameter-crops input[type="checkbox"]' ).prop('checked', $this.checked);

        return;
    }else if($('#parameter-crops input[type="checkbox"]' ).first().prop('checked')){

        $('#parameter-crops input[type="checkbox"]' ).first().prop('checked', false);

        parameterCrops = [];

        $('#parameter-crops input[type="checkbox"]:checked').each(function() {
            parameterCrops.push(this.value);
         });

        return;

    }

    if($this.checked) parameterCrops.push($this.value);
    else {
        for (let index = 0; index < parameterCrops.length; index++) {
            if (parameterCrops[index] == $this.value){
                parameterCrops.splice(index, 1);
                break;
            }
        }
    }

    console.log(parameterCrops);
}

async function loadParameterEdit(id) {

    parameterProcess = 2;
    document.getElementById('modalAddParamterLabel').innerText = 'Update parameter';
    document.getElementById('parameter-state-cont').classList.remove('d-none');

    resetParameterForm();

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"dashboard/loadParameterEdit",
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

                        switch (data['type']) {
                            case 5: case 6: case 7:
                                document.querySelectorAll("#parameter-type option").forEach(opt => {
                                    if (opt.value < 5) {
                                        opt.disabled = true;
                                    }
                                });
                                break;
                        
                            default:
                                document.querySelectorAll("#parameter-type option").forEach(opt => {
                                    if (opt.value != data['type'] || opt.value == '') {
                                        opt.disabled = true;
                                    }
                                });
                                break;
                        }

                        //document.getElementById('parameter-type').disabled = true;

                        document.getElementById('parameter-id').value = data['id'];
                        document.getElementById('parameter-name').value = data['parameter'];
                        document.getElementById('parameter-type').value = data['type'];
                        document.getElementById('parameter-category').value = data['category'];
                        document.getElementById('parameter-position').value = data['position'];
                        document.getElementById('parameter-label').value = data['label'];
                        document.getElementById('parameter-remark').value = data['remark'];
                        document.getElementById('parameter-all').checked = data['type_all'];
                        document.getElementById('parameter-state').value = data['state'];

                        if (data['options']) {
                            data['options'].split(',').forEach(element => {
                                let option = element.split('^');
    
                                addOptionParameter(option[0], option[1], option[2]);
                            });   
                        }

                        if (data['crops']) {
                            data['crops'].split(',').forEach(element => {
                                if (element == 17) {
                                    parameterCrops = [17]
                                    $('#parameter-crops input[type="checkbox"]').prop('checked', true);
                                }else{
                                    $('#parameter-crops input[type="checkbox"]').each(function () {
                                        if (this.value == element) {
                                            this.checked = true;

                                            parameterCrops.push(element);
                                        }
                                    })
                                }
                            });   
                        }

                        if (data['type'] == 1) $(".parameter-more").removeClass('d-none');
                        else $(".parameter-more").addClass('d-none');

                        if (data['type'] == 1 || data['type'] == 2) $("#parameter-all-cont").removeClass('d-none');
                        else $("#parameter-all-cont").addClass('d-none');

                        if (data['type'] == 5 || data['type'] == 6 || data['type'] == 7) {
                            $("#parameter-options-cont").removeClass('d-none');
                            stateOptions = true;
                        }else{
                            $("#parameter-options-cont").addClass('d-none'); 
                            $("#parameter-options").html(null);
                            countOptionsParameter();
                            stateOptions = false;
                        }
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(response);
                    alert(error);
                }

                resolve('resolved');
            }
        })
    });
}

function addOptionParameter(id = 0, value = '', state = 1){
    
    let itemCount = countOptionsParameter();

    if (!value) {
        value = `Value ${itemCount+1}`;
    }

    checked = (state == 1) ? 'checked' : '';

    let item = 
        `<div class="col-md-3 mb-3 parameter-option-cont">
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control" data-id="${id}" value="${value}" data-enabled="${state}">
                
            `;

    if (id > 0) {
        item = item + 
            `<div class="input-group-text bg-white" id="addon-wrapping">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" ${checked} role="switch" onchange="onchangeOptionParameter(this)">
                </div>
            </div>`;
    }

    item = item + `</div></div>`;


    if(itemCount > 0){
        $("#parameter-options").append(item);
    }else{
        $("#parameter-options").html(item);
    }
}

function onchangeOptionParameter($this) {
    let state = ($this.checked) ? 1 : 0 ;
    $this.parentNode.parentNode.parentNode.querySelector('input').setAttribute('data-enabled', `${state}`);
}

function countOptionsParameter(){
    let options = document.getElementsByClassName('parameter-option-cont');

    if (!options.length) {
        $("#parameter-options").html('<div class="px-4"><div class="alert alert-warning">Add options!</div></div>');
    }

    return options.length;
}

function resetParameterForm(){
    document.getElementById('parameter-options').innerHTML = '';
    
    document.querySelectorAll("#parameter-type option").forEach(opt => {
        opt.disabled = false;
    });

    document.getElementById('parameter-name').value = '';
    document.getElementById('parameter-type').selectedIndex = 0;
    document.getElementById('parameter-category').selectedIndex = 0;
    document.getElementById('parameter-position').selectedIndex = 0;
    document.getElementById('parameter-state').selectedIndex = 0;
    document.getElementById('parameter-label').value = '';
    document.getElementById('parameter-remark').value = '';
    document.getElementById('parameter-all').checked = false;

    $('#parameter-crops input[type="checkbox"]').prop('checked', false);
    parameterCrops = [];

    countOptionsParameter();
    $(".parameter-more").addClass('d-none');
    $("#parameter-options-cont").addClass('d-none');
}
//TODO Parameters