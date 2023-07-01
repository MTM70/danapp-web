$(document).ready(function() {

    $("#calendar-week").change(function(){
        loadCalendar();
        loadCalendarFilters();
    });

});


//TODO Calendar
const loadCalendar = async () => {

    return new Promise(resolve => {
        stateCalendar = true;

        $.ajax({
            url: base_url+"dashboard/loadCalendarWeek",
            type: 'GET',
            data: {'week': document.querySelector('#calendar-week').value},
            cache: false,

            beforeSend: function() {
                $('#calendar-loading').removeClass('d-none');
            },

            error:  function(jqXHR, textStatus, errorThrown) {
                console.log({jqXHR, textStatus, errorThrown});
                //alert(xhr.responseJSON);
                //console.log(xhr.responseJSON);
                $('#calendar-loading').addClass('d-none');
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#calendar').html(objData.res);

                        calendarSearch();
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    console.log(response);
                    alert(error);
                }

                $('#calendar-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });
}

const loadCalendarFilters = async () => {

    return new Promise(resolve => {
        stateCalendar = true;

        $.ajax({
            url: base_url+"dashboard/loadCalendarFilters",
            type: 'GET',
            data: {'week': document.querySelector('#calendar-week').value},
            cache: false,

            beforeSend: function() {
                $('#calendar-loading').removeClass('d-none');
            },

            error:  function(jqXHR, textStatus, errorThrown) {
                console.log({jqXHR, textStatus, errorThrown});
                //alert(xhr.responseJSON);
                //console.log(xhr.responseJSON);
                $('#calendar-loading').addClass('d-none');
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        $('#calendar-filters').html(objData.res);
                    }else{
                        alert(objData.res);
                    }
                } catch (error) {
                    console.log(response);
                    alert(error);
                }

                $('#calendar-loading').addClass('d-none');
                resolve('resolved');
            }
        })
    });
}

const calendarFilter = () => {
    let ordersTypes = document.querySelectorAll('#calendar-filters-types input:checked');
    let destinations = document.querySelectorAll('#calendar-filters-destinations input:checked');;
    let crops = document.querySelectorAll('#calendar-filters-crops input:checked');;

    const orders = document.querySelectorAll('#calendar .card');

    if (!ordersTypes.length && !destinations.length && !crops.length) {
        
        orders.forEach(element => element.classList.remove('d-none'));
        document.querySelector('#calendar-filters-btn').classList.add('d-none');
        document.querySelector('#calendar-filters-notify').classList.add('d-none');

        return;
    }

    orders.forEach(element => element.classList.add('d-none'));
    document.querySelector('#calendar-filters-btn').classList.remove('d-none');
    document.querySelector('#calendar-filters-notify').classList.remove('d-none');

    if (ordersTypes.length) {
        
        if (destinations.length) {

            if (crops.length) {

                ordersTypes.forEach(type => {
                    destinations.forEach(destination => {
                        crops.forEach(crop => {
                            orders.forEach(order => (order.dataset.type == type.value && order.dataset.destination == destination.value && order.dataset.crop == crop.value) ? order.classList.remove('d-none') : null);
                        })
                    })
                });

                return;
            }

            ordersTypes.forEach(type => {
                destinations.forEach(destination => {
                    orders.forEach(order => (order.dataset.type == type.value && order.dataset.destination == destination.value) ? order.classList.remove('d-none') : null);
                })
            });

            return;
            
        }else if (crops.length) {

            ordersTypes.forEach(type => {
                crops.forEach(crop => {
                    orders.forEach(order => (order.dataset.type == type.value && order.dataset.crop == crop.value) ? order.classList.remove('d-none') : null);
                })
            });
            
            return;
        }

        ordersTypes.forEach(type => orders.forEach(order => (order.dataset.type == type.value) ? order.classList.remove('d-none') : null));

    }else if (destinations.length) {

        if (crops.length) {

            destinations.forEach(destination => {
                crops.forEach(crop => {
                    orders.forEach(order => (order.dataset.destination == destination.value && order.dataset.crop == crop.value) ? order.classList.remove('d-none') : null);
                })
            });
            
            return;
        }

        destinations.forEach(destination => orders.forEach(order => (order.dataset.destination == destination.value) ? order.classList.remove('d-none') : null));
        
    }else if (crops.length) {

        crops.forEach(crop => orders.forEach(order => (order.dataset.crop == crop.value) ? order.classList.remove('d-none') : null));
        
    };
}

const calendarClearFilter = () => {
    document.querySelectorAll('#calendar-filters-types input:checked').forEach(element => element.checked = false);
    document.querySelectorAll('#calendar-filters-destinations input:checked').forEach(element => element.checked = false);
    document.querySelectorAll('#calendar-filters-crops input:checked').forEach(element => element.checked = false);

    let openedCanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRight'));
    openedCanvas.hide()

    calendarFilter();
};

const updateOrderVisitDayForm = (element, idOrder) => {
    let inputs = element.parentNode.querySelectorAll('input');

    updateOrderVisitDay(idOrder, inputs[0].value, inputs[1].value, inputs[2].value);
}

const updateOrderVisitDay = async (idOrder, date, time, notify) => {

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"dashboard/updateOrderVisitDay",
            type: 'POST',
            data: {'idOrder': idOrder, 'date':date, 'time':time, 'notify':notify},
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                console.log(xhr);
                //$('#parameter-crops').html('Error');
            },

            success:  function(response) {
                console.log(response);
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        loadCalendar();
                        //$('#parameter-crops').html(objData.res);
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

let idDrag = 0;
let timeDrag = 0;
let notifyDrag = 0;

function dragstart(e) { //Al arrastrar
    //typeDrag = type;

    //e.dataTransfer.setData('id', e.target.parentNode.id);
    idDrag = e.target.dataset.id;
    timeDrag = e.target.dataset.time;
    notifyDrag = e.target.dataset.notify;

    document.querySelectorAll('#calendar td').forEach(element => element.classList.add('pt-5', 'pb-5'));
}

function dragover(e) {
    //id = e.target.id; //id del bloque y dia a mover
    //id2 = idDrag; //id del bloque y dia a jalar
    //bloque = id.split('_');
    //bloque2 = id2.split('_');

    if (e.target.tagName == 'TD') {
        e.preventDefault();
        e.target.classList.add('calendar-hover'); 
    }
    
}

function dragEnd(e) {
    document.querySelectorAll('#calendar td').forEach(element => element.classList.remove('pt-5', 'pb-5'));
}

function dragleave(e) {
    e.target.classList.remove('calendar-hover'); 
}

function drop(e){
    e.target.classList.remove('calendar-hover');

    updateOrderVisitDay(idDrag, e.target.dataset.date, timeDrag, notifyDrag);
}



// JavaScript code
function calendarSearch() {
	let input = document.getElementById('calendar-search').value

    if (!input.length) {
        calendarFilter();
        return;
    }

	input = input.toLowerCase();

	let x = document.querySelectorAll('#calendar .card');
	
	for (i = 0; i < x.length; i++) {
		if (!x[i].innerHTML.toLowerCase().includes(input)) {
			x[i].classList.add('d-none');
		}
		else {
			x[i].classList.remove('d-none');			
		}
	}
}


function exportPDF() {
    document.querySelector('body').classList.remove('toggle-sidebar');
    window.print();
    document.querySelector('body').classList.add('toggle-sidebar');
}