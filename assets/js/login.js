$(document).ready(function() {
    //Registrar ppc
    $("#form-login").on("submit", function(e){
        e.preventDefault();

        var formData = new FormData(document.getElementById("form-login"));

        $.ajax({
            url: base_url+"/Login/signIn",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            beforeSend: function() {
                $('#login-btn').addClass('disabled');
                $('#login-text').html('checking...');
                $('#login-loading').removeClass('d-none');
            },

            error:  function(xhr) {
                alert(xhr.response);
                $('#login-btn').removeClass('disabled');
                $('#login-text').html('Login');
                $('#login-loading').addClass('d-none');
            },

            success:  function(response) {
                var objData = JSON.parse(response);

                if(objData.status == true){
                    location.href= base_url+"/dashboard";
                }else{
                    alert(objData.res);
                }

                $('#login-btn').removeClass('disabled');
                $('#login-text').html('Login');
                $('#login-loading').addClass('d-none');
            }
        });
    });
});