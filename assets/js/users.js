var userProcess = 1;

$(document).ready(function() {
    //TODO Users
    $("#form-user").on("submit", function(e){
        e.preventDefault();

        let secCusts = Array();
        $('.accordion-body input[type="checkbox"]:checked').each(function() {
           secCusts.push(this.value);
        });

        if (!secCusts.length && document.getElementById('user-rol').value == 2) {
            alert('Add sec cust!');
            return;
        }

        let formData = new FormData(document.getElementById("form-user"));
        formData.append('secCusts', JSON.stringify(secCusts));
        formData.append('process', userProcess);

        $.ajax({
            url: base_url+"/Dashboard/setUser",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#user-btn').html('Loading...');
                $('#form-user :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#form-user :input').attr('disabled', false);
                $('#user-btn').html('Save changes');
            },
        })
        .done(function(response){
            try {
                var objData = JSON.parse(response);
 
                if(objData.status == true){
                    alert(objData.res);

                    toogleModal('modalAddUser', false);
                    loadUsers();
                }else{
                    alert(objData.res);
                } 
            } catch (error) {
                alert(error);   
            }
            

            $('#user-btn').html('Save changes');
            $('#form-user :input').attr('disabled', false);
        });
    });

    $('#user-name').keyup(function(e){
        if (!document.getElementById('user-name').value.includes('@')) {
            $(this).val(`@${this.value}`);
        }
    });
});

//TODO Users
async function loadUsers() {

    return new Promise(resolve => {
        stateUsers = true;

        $.ajax({
            url: base_url+"/Dashboard/loadUsers",
            cache: false,

            beforeSend: function() {
                $('#users-loading').removeClass('d-none');
            },

            error:  function(xhr) {
                alert(xhr);
                $('#users-loading').addClass('d-none');
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#users').html(objData.res);
                        let table = new DataTable('#table-users', {
                            "processing": true,
                            "scrollY": false,
                            "scrollX": (isMobile) ? true : false,
                            "iDisplayLength": 50,
                            "stateSave": true,
                            scrollY: '53vh',
                        }).page();

                        $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                            window.scrollTo(0, 0);
                        });

                        document.querySelectorAll('#table-users_wrapper .row')[2].classList.add('mt-3');
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                $('#users-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });
}

async function openModalUser() {
    if (userProcess == 2) {
        resetUserForm();
        await loadCusts();
    }

    document.getElementById('user-state-cont').classList.add('d-none');
    document.getElementById('modalAddUserLabel').innerText = 'Add user';
    userProcess = 1;
    
}

async function loadUserEdit(id) {

    userProcess = 2;
    document.getElementById('modalAddUserLabel').innerText = 'Update user';
    document.getElementById('user-state-cont').classList.remove('d-none');
    $('#user-sec-custs').html(null);

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/Dashboard/loadUserEdit",
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

                        document.getElementById('user-id').value = data['id'];
                        document.getElementById('user-name').value = data['user'];
                        document.getElementById('user-new-password').value = '';
                        document.getElementById('user-password').value = data['pass'];
                        document.getElementById('name').value = data['name'];
                        document.getElementById('user-last-name').value = data['last_name'];
                        document.getElementById('user-rol').value = data['id_rol'];
                        document.getElementById('user-state').value = data['state'];

                        await loadCusts();

                        if (data['secCusts']) {

                            let cust = 0;

                            data['secCusts'].split(',').forEach(element => {
                                $('#user-sec-custs .accordion-body input[type="checkbox"]').each(function () {
                                    if (this.value == element) {
                                        this.checked = true;

                                        let idCheckCust = this.parentNode.parentNode.parentNode.parentNode.querySelectorAll('input')[0].id;
                                        let idCollapse = this.parentNode.parentNode.parentNode.id;

                                        if (cust != idCollapse) {
                                            var myCollapse = document.getElementById(idCollapse)

                                            let bsCollapse = new bootstrap.Collapse(myCollapse, {
                                                toggle : false
                                            });

                                            bsCollapse.show();
                                        }

                                        checkSecCust(this, idCollapse, idCheckCust);

                                        cust = idCollapse;
                                    }
                                })
                            });   
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

async function loadCusts() {

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/Dashboard/loadSecCusts",
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                alert(xhr);
            },

            success:  function(response) {
                var objData = JSON.parse(response);

                if(objData.status == true){
                    $('#user-sec-custs').html(objData.res);
                }else{
                    alert(objData.res);
                }

                resolve('resolved');
            }
        })
    });
}

async function loadRoles() {

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/Dashboard/loadRoles",
            cache: false,

            beforeSend: function() {
            },

            error:  function(xhr) {
                alert(xhr);
            },

            success:  function(response) {
                var objData = JSON.parse(response);

                if(objData.status == true){
                    $('#user-rol').html(objData.res);
                }else{
                    alert(objData.res);
                }

                resolve('resolved');
            }
        })
    });
}

function checkCust($this, id) {
    let bsCollapse = new bootstrap.Collapse(`#${id}`, {
        toggle: $this.checked
    })

    if (!$this.checked) {
        bsCollapse.hide();
        $this.parentNode.parentNode.classList.add('bg-light');
    }else $this.parentNode.parentNode.classList.remove('bg-light');

    $(`#${id}`).parent().parent().parent().find('.accordion-body input[type="checkbox"]').prop('checked', $this.checked);
}

function checkSecCust($this, id, idCust) {

    let activos = 0;
    let total = $(`#${id}`).parent().parent().parent().find('.accordion-body input[type="checkbox"]').length;

    $(`#${id}`).parent().parent().parent().find('.accordion-body input[type="checkbox"]').each(function() {
        if (this.checked) activos++;
    });

    if (activos < total) {

        if (!activos) new bootstrap.Collapse(`#${id}`);

        $(`#${idCust}`).prop('checked', false);
        $this.parentNode.parentNode.parentNode.parentNode.querySelector('.accordion-button').classList.add('bg-light');

    }else if(activos == total){

        $(`#${idCust}`).prop('checked', true);

        $this.parentNode.parentNode.parentNode.parentNode.querySelector('.accordion-button').classList.remove('bg-light');
    }
}

function resetUserForm(){
    
    document.querySelectorAll("#user-sec-custs option").forEach(opt => {
        opt.disabled = false;
    });

    document.getElementById('user-name').value = '';
    document.getElementById('user-new-password').value = '';
    document.getElementById('user-password').value = '';
    document.getElementById('name').value = '';
    document.getElementById('user-last-name').value = '';
    document.getElementById('user-rol').selectedIndex = 0;
    document.getElementById('user-state').selectedIndex = 0;
}
//TODO Users