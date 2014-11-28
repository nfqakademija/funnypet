$(document).ready(function(){
    $(".upload").click(function(event){
        event.preventDefault();
    });

    $(".file_name").fileupload({
        dataType: 'json',
        Default: true,
        singleFileUploads: true,
        success: function (data) {
            NProgress.done();
           if (data.response == "success") {
               var successBlock = "<div>"+data.message+"</div>";
               $("#success").html(successBlock).removeClass('hide');
           } else {
               var errorBlock = "";
               for (var key in data.global) {
                   errorBlock += "<div><strong>Klaida!</strong> "+data.global[key]+"</div>";
               }

               for (var key in data.fields) {
                   errorBlock += "<div><strong>Klaida!</strong> "+data.fields[key]+"</div>";
               }

               $("#error").html(errorBlock).removeClass('hide');
           }
        },
        add: function (e, data) {
            $(".upload").prop("disabled", false);
            $("#selected_photo_name").text(data.fileInput.val());
            $(".upload").off('click').on('click', function (event) {
                $("#error").addClass('hide');
                $("#success").addClass('hide');
                event.preventDefault();
                NProgress.start();
                data.submit();
            });
        },
        fail: function () {
            alert("Can not upload photo please contact administrator.");
        }
    });
});