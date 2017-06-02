$(document).ready(function() {
  var percentBar = $('#barraProgreso');
  var percentString = $('#barraProgreso h4');
  var aviso = $('#aviso .alert');

  $("#video").change(function(){
    $("#tmpFileName").val(($(this).val() !== "") ?
      $(this).val().replace(/\\/g, '/').replace(/.*\//, '')
      : "Ningún archivo seleccionado");
    $("#progresoSubida").hide();
    percentBar.attr("aria-valuenow", 0);
    percentBar.css({width: "0%"});
    percentBar.removeClass("progress-bar-success progress-bar-danger "
      +"progress-bar-warning");
    percentBar.addClass("progress-bar-striped active");
    percentString.html("0%");
    // Si el archivo pesa más de 2GB (valor en bytes; 1024 bytes por KB, etc)
    if($(this)[0].files[0] !== undefined && $(this)[0].files[0].size > 2147483648){
      // Se avisa y se deshabilita el botón
      aviso.html("El archivo es demasiado grande (Máximo admitido: 2GB)");
      aviso.removeClass("alert-warning");
      aviso.addClass("alert-danger");
      aviso.slideDown();
      $("#submit").enable(false);
      $("#submitInline span.btn").attr("disabled", true);
    } else { // Sino
      // Se vacía el mensaje de aviso
      aviso.slideUp(function(){
        aviso.empty();
        aviso.removeClass("alert-warning");
        aviso.removeClass("alert-danger");
      });
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
      // Cancelar AJAX si no hay archivo
      if($("#video")[0].files[0] === undefined){
        xhr.abort();
      }
      // Mostrar proceso de subida
      $("#progresoSubida").slideDown();
      percentBar.removeClass("progress-bar-success progress-bar-danger "
      +"progress-bar-warning");
      // Deshabilitar botón de subida
      $("#submit").enable(false);
      $("#submitInline span.btn").attr("disabled", true);
      // Ocultar alert de aviso
      aviso.slideUp(function(){
        aviso.empty();
        aviso.removeClass("alert-warning");
        aviso.removeClass("alert-danger");
      });
    },
    // Mientras se sube
    uploadProgress: function(event, position, total, percentComplete) {
      // Actualizar porcentaje
      percentBar.attr("aria-valuenow", percentComplete);
      var percentVal = percentComplete + '%'; 
      percentBar.css({width: percentVal});
      percentString.html(percentVal);
    },
    complete: function(data){
      // Cuando se ha procesado la inclusión a la cola
      json = data.responseJSON;
      // Indicar en la barra de progreso el mensaje devuelto
      percentString.html(json.mensaje);
      percentBar.removeClass("progress-bar-striped active");
      if(json.success){
        // Si ha salido bien
        percentBar.addClass("progress-bar-success");
        // Enlace al vídeo por alert de aviso
        aviso.html("Verifique el progreso de conversión en la página del vídeo"
        +"<br><a href='/ver?video="+json.idVideo+"'>Enlace al vídeo</a>");
        aviso.removeClass("alert-danger");
        aviso.addClass("alert-warning");
        aviso.slideDown();
      } else {
        // Si no ha salido bien
        percentBar.addClass("progress-bar-danger");
      }
    }
  });
});