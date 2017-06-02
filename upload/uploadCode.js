$(document).ready(function() {
  var percentBar = $('#barraProgreso');
  var percentString = $('#barraProgreso h4');
  var aviso = $('#aviso');

  $("#video").change(function(){
    $("#tmpFileName").val(($(this).val() !== "") ?
      $(this).val().replace(/\\/g, '/').replace(/.*\//, '')
      : "Ningún archivo seleccionado");
    $("#progresoSubida").hide();
    percentBar.attr("aria-valuenow", 0);
    percentBar.css({width: "0%"});
    percentBar.removeClass("progress-bar-success progress-bar-danger");
    percentBar.addClass("progress-bar-striped active");
    percentString.html("0%");
    // Si el archivo pesa más de 2GB (valor en bytes; 1024 bytes por KB, etc)
    if($(this)[0].files[0] !== undefined && $(this)[0].files[0].size > 2147483648){
      // Se avisa y se deshabilita el botón
      aviso.html("El archivo es demasiado grande (Máximo admitido: 2GB)");
      $("#submit").enable(false);
      $("#submitInline span.btn").attr("disabled", true);
    } else { // Sino
      // Se vacía el mensaje de aviso
      aviso.empty(); 
      // Solo se habilita el botón cuando hay archivo
      $("#submit").enable(($("#video")[0].files[0] !== undefined));
      $("#submitInline span.btn").attr("disabled",
        ($("#video")[0].files[0] === undefined));
    }
  });

  $('#submitVideoForm').ajaxForm({
    dataType: "json",
    // Antes de subir
    beforeSend: function(xhr) {
      // Mostrar proceso de subida
      if($("#video")[0].files[0] === undefined){
        xhr.abort();
      }
      $("#progresoSubida").slideDown();
    },
    // Mientras se sube
    uploadProgress: function(event, position, total, percentComplete) {
      // Actualizar porcentaje
      percentBar.attr("aria-valuenow", percentComplete);
      var percentVal = percentComplete + '%'; 
      percentBar.css({width: percentVal});
      percentString.html(percentVal);
      if(percentComplete == 100){
        percentString.html("Codificando...");
        percentBar.addClass("progress-bar-warning");
      }
    },
    complete: function(data){
      json = data.responseJSON;
      percentString.html(json.mensaje);
      percentBar.removeClass("progress-bar-warning progress-bar-striped active");
      if(json.success){
        percentBar.addClass("progress-bar-success");
      } else {
        percentBar.addClass("progress-bar-danger");
      }
    }
  });
});