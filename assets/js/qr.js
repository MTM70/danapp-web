$(document).ready(function() {

    loadQr();

});

const loadQr = async () => {

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/Qr/event",
            type: 'GET',
            data: {'id_event': idEvent},
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                //$('#parameter-crops').html('Error');
                resolve(false);
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        document.querySelector('#cont-qr').innerHTML = objData.res;
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

const convertirADiv = async (contId, id) => {

    // Obtener una referencia al elemento que deseas ocultar
    let elementoParaOcultar = document.querySelector(`#${contId} .btn-download`); // Reemplaza 'elemento_a_ocultar' con el ID o selector adecuado
    // Guardar el estilo actual del elemento
    let estiloOriginal = elementoParaOcultar.style.display;
    // Ocultar el elemento estableciendo su estilo a 'none'
    elementoParaOcultar.style.display = 'none';

    let img = document.querySelector(`#${contId} img`);
    let name = document.querySelector(`#${contId} h6`);

    return new Promise(resolve => {

        $.ajax({
            url: base_url+"/Qr/saveQrToServer",
            type: 'POST',
            data: {'urlApi': img.src, 'id': id},
            cache: false,

            beforeSend: function() {
                
            },

            error:  function(xhr) {
                //$('#parameter-crops').html('Error');
                resolve(false);
            },

            success:  function(response) {
                try {
                    var objData = JSON.parse(response);

                    if(objData.status == true){
                        
                        img.src = objData.res;

                        // Obtener la referencia al contenedor div
                        var contenedor = document.getElementById(contId);

                        // Utilizar html2canvas para capturar el contenido del contenedor
                        html2canvas(contenedor, {
                            scale: 2, // Aumentar la escala a 2x (aumenta la resolución)
                            type: 'jpeg', // Utilizar formato JPEG
                            quality: 1.0, // Calidad máxima (0.0 - 1.0)
                        }).then(function(canvas) {
                            // Convertir el canvas en una URL de datos (data URL)
                            var imagenBase64 = canvas.toDataURL("image/jpeg");

                            // Crear un enlace de descarga y simular un clic en él para descargar la imagen
                            var enlaceDescarga = document.createElement("a");
                            enlaceDescarga.href = imagenBase64;
                            enlaceDescarga.download = name.innerText + ".jpeg";
                            enlaceDescarga.style.display = "none";
                            document.body.appendChild(enlaceDescarga);
                            enlaceDescarga.click();
                            document.body.removeChild(enlaceDescarga);

                            // Restaurar el estilo original del elemento
                            elementoParaOcultar.style.display = estiloOriginal;
                        });

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
    /* html2canvas([document.getElementById(id)], {
		onrendered: function (canvas) {
			var img = canvas.toDataURL('image/png'); //o por 'image/jpeg' 
			//display 64bit imag
            $('#'+id).html('<img src="'+img+'" width="100%" height="300"/>')
			//id.innerHTML = '<img src="'+img+'"/>';		    
		}
	}); */
}

// Función para ejecutar cuando el mouse pasa por encima del elemento
function mouseOver(item) {
    item.querySelector('.btn-download').classList.remove('d-none');
}

// Función para ejecutar cuando el mouse se quita del elemento
function mouseOut(item) {
    item.querySelector('.btn-download').classList.add('d-none');
}