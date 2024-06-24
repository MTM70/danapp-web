let customerProcess = 1;
let imgCustomer;

$(document).ready(function() {
    //TODO Customers
    $("#form-customer").on("submit", function(e){
        e.preventDefault();

        let secCustomers = Array();

        document.querySelectorAll('#customer-sec-cust .sec-cust-cont').forEach(element => {
            let inputs = element.querySelectorAll('input');

            if (!inputs || inputs.length != 2) return;

            if (!inputs[0].value || !inputs[1].value) return;

            secCustomers.push(inputs[0].value + ',' + inputs[1].value);
        });

        let formData = new FormData(document.getElementById("form-customer"));
        formData.append('process', customerProcess);
        formData.append('sec-customers', JSON.stringify(secCustomers));

        $.ajax({
            url: base_url+"/Dashboard/setCustomer",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#customer-btn').html('Loading...');
                $('#form-customer :input').attr('disabled', true);
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#form-customer :input').attr('disabled', false);
                $('#customer-btn').html('Save changes');
            },
        })
        .done(function(response){
            try {
                var objData = JSON.parse(response);
 
                if(objData.status == true){
                    alert(objData.res);

                    toogleModal('modalAddCustomer', false);
                    loadCustomers();
                    resetCustomerForm();
                }else{
                    alert(objData.res);
                } 
            } catch (error) {
                alert(error);   
            }
            

            $('#customer-btn').html('Save changes');
            $('#form-customer :input').attr('disabled', false);
        });
    });

    imgCustomer = document.querySelector('#customer-image');

    document.querySelector('#customer-file').addEventListener('change', function() {

        if (this.files && this.files[0]) {

            imgCustomer.onload = () => {
                URL.revokeObjectURL(imgCustomer.style.backgroundImage);  // no longer needed, free memory
            }

            imgCustomer.querySelector('i').classList.add('d-none');

            imgCustomer.style.backgroundImage = `url(${URL.createObjectURL(this.files[0])})`; // set src to blob url

        }else {
            imgCustomer.style.backgroundImage = null;
            imgCustomer.querySelector('i').classList.remove('d-none');
        }
    });
});

//TODO Customers
async function loadCustomers() {

    return new Promise(resolve => {
        stateCustomers = true;

        $.ajax({
            url: base_url+"/Dashboard/loadCustomers",
            cache: false,

            beforeSend: function() {
                $('#customers-loading').removeClass('d-none');
            },

            error:  function(xhr) {
                alert(xhr);
                $('#customers-loading').addClass('d-none');
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#customers').html(objData.res);
                        new DataTable('#table-customers', {
                            "processing": true,
                            "scrollY": false,
                            "scrollX": (isMobile) ? true : false,
                            "iDisplayLength": 50,
                            "stateSave": true,
                            order: [[1, 'asc']],
                            scrollY: '53vh',
                        }).page();

                        $(".dataTables_paginate").bind( "click", '.paginate_button', function() {
                            window.scrollTo(0, 0);
                        });

                        document.querySelectorAll('#table-customers_wrapper .row')[2].classList.add('mt-3');
                        resetCustomerForm();
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    alert(error);
                }

                $('#customers-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });
}

async function openModalCustomer() {
    if (customerProcess == 2) {
        resetCustomerForm();
    }

    document.getElementById('modalAddCustomerLabel').innerText = 'Add customer';
    customerProcess = 1;
    
}

async function loadCustomerEdit(id) {

    customerProcess = 2;
    document.getElementById('modalAddCustomerLabel').innerText = 'Update customer';
    resetCustomerForm();

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/dashboard/loadCustomerEdit",
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

                        document.getElementById('customer-id').value = data['id'];
                        document.getElementById('customer-number').value = data['cust_no'];
                        document.getElementById('customer-number').readOnly = true;
                        document.getElementById('customer-number').parentNode.classList.remove('d-none');
                        document.getElementById('customer-name').value = data['cust'];
                        document.getElementById('customer-file-path').value = data['logo'];

                        if (data['logo']) {
                            imgCustomer.querySelector('i').classList.add('d-none');
                            imgCustomer.style.backgroundImage = `url(${base_url}/assets/img/customers/${data['logo']})`;
                            document.getElementById('customer-file-path').value = data['logo'];
                        }else {
                            imgCustomer.style.backgroundImage = null;
                            imgCustomer.querySelector('i').classList.remove('d-none');
                            document.getElementById('customer-file-path').value = '';
                        }

                        if (data['secCusts']) {

                            document.getElementById('customer-sec-cust').innerHTML = null;

                            data['secCusts'].split(',').forEach(element => {

                                let data = element.split('&');
                                
                                addSecCustomer(data[0], data[1]);
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

function addSecCustomer(no = 0, name = ''){
    
    let itemCount = countSecCustomers();

    let item = 
        `<div class="col-auto sec-cust-cont ${!no ? 'order-first' : ''}">
            <div class="badge p-2 m-1 bg-success bg-opacity-10 text-dark fw-normal border">
                <div class="d-flex">
                    <div class="">`;

                        (!no) ? item += `<input class="form-control form-control-sm bg-warning bg-opacity-10" type="number" placeholder="Sec No" value="${itemCount > 0 ? itemCount + 1 : 0}">` : (no != 0 ? item += `<span class="fs-0-8">(${no})</span>` : '');

                    item += `</div>

                    <div class="${!name ? 'ms-2' : ''}">`;

                        (!no) ? item += `<input class="form-control form-control-sm bg-warning bg-opacity-10" type="text" placeholder="SecCust Name">` : item += `<span class="fs-0-8">${name}</span>`;
                        
                    item += `</div>
                </div>
            </div>
        </div>`;


    if(itemCount > 0){
        $("#customer-sec-cust").append(item);
    }else{
        $("#customer-sec-cust").html(item);
    }
}

function countSecCustomers(){
    let secCustomers = document.getElementsByClassName('sec-cust-cont');

    if (!secCustomers.length) {
        $("#customer-sec-cust").html('<div class="px-4"><div class="alert alert-danger">Add Sec Customers!</div></div>');
    }

    return secCustomers.length;
}

function resetCustomerForm(){
    
    /* document.querySelectorAll("#user-sec-custs option").forEach(opt => {
        opt.disabled = false;
    }); */

    document.getElementById('customer-number').value = '';
    document.getElementById('customer-number').readOnly = false;
    document.getElementById('customer-number').parentNode.classList.add('d-none');
    document.getElementById('customer-name').value = '';
    document.getElementById('customer-file').value = '';
    document.getElementById('customer-sec-cust').innerHTML = null;
    
    imgCustomer.style.backgroundImage = null;
    imgCustomer.querySelector('i').classList.remove('d-none');
    document.getElementById('customer-file-path').value = '';

    countSecCustomers();
}
//TODO Customers