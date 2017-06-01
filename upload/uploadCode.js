// Declaración global para el timeout del check
var checkTimeout;
$(document).ready(function() {
  var percent = $('.percent');
  var respuesta = $('#respuesta');

  $("#video").bind("change", function(){
    $("#progresoSubida").hide();
    percent.html("0%");
    // Si el archivo pesa más de 2GB (valor en bytes; 1024 bytes por KB, etc)
    if(this.files[0] !== undefined && this.files[0].size > 2147483648){
      // Se avisa y se deshabilita el botón
      respuesta.html("El archivo es demasiado grande (Máximo admitido: 2GB)");
      $("#submit").disable();
    } else { // Sino
      // Se vacía el mensaje de aviso y se habilita el botón
      respuesta.empty(); 
      $("#submit").enable();
    }
  });

  $('#submitVideoForm').ajaxForm({
    // Antes de subir
    beforeSend: function() { 
      // Porcentaje a 0
      var percentVal = '0%'; 
      percent.html(percentVal);
      // Mostrar proceso de subida
      $("#progresoSubida").show(); 
    },
    // Mientras se sube
    uploadProgress: function(event, position, total, percentComplete) {
      // Actualizar porcentaje
      var percentVal = percentComplete + '%'; 
      percent.html(percentVal);
      if(percentComplete == 100)
        $(".barraCodificando").slideDown();
    },
    complete: function(data){
      respuesta.html(data.responseText);
      $(".barraCodificando").slideUp();
    }
  });
});