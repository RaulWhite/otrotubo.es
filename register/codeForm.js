$(document).ready(function(){
  // Declaración global para tiempo de espera de ocultación de errores
  var alertHideTOut;
  // Cada vez que se selecciona un archivo en el formulario
  $("#avatar").change(function(){
    // Se ocultan el alert y la imagen si se estaban mostrando
    $(".tmpAvatar").hide();
    clearTimeout(alertHideTOut);
    $("#alertIMG").slideUp();
    // Se muestra el nombre de la imagen o que no hay nada seleccionado
    $("#tmpFileName").val($(this).val() ?
      $(this).val().replace(/\\/g, '/').replace(/.*\//, '')
      : "Ningún archivo seleccionado");
    var formData = new FormData();
    // Archivo del input "avatar"
    $(".tmpAvatar").hide();
    $(".tmpAvatar").attr("src", "");
    // Si se ha quitado el archivo subido (se ha vaciado el input),
    // se intenta usar Gravatar
    if(!$("#avatar").get(0).files.length)
      $("#email").change();
    // Si se ha subido archivo
    if($("#avatar").get(0).files.length){
      $.each($("#avatar")[0].files, function(i, file){
        formData.append("file"+i, file);
      });
      // Mostrar animación de carga
      $(".imgProcessing").slideDown();
      $.ajax({
        url: "/register/uploadAvatar.php",
        type: "post",
        dataType: "json",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(json){
          // Si ha pasado los tests en el PHP, se muestra la imagen
          if(json.checkSuccess){
            $(".tmpAvatar").attr("src", json.tmpImgPath);
            $(".tmpAvatar").show();
          // Sino, se muestra el mensaje de error como alert de bootstrap
          } else {
            $("#avatar").val("");
            $("#alertIMG").html("<strong>ERROR: </strong>" + json.message);
            $("#alertIMG").removeClass("alert-info");
            $("#alertIMG").addClass("alert-danger");
            $("#alertIMG").slideDown();
            alertHideTOut = setTimeout(function(){
              $("#alertIMG").slideUp();
            }, 5000);
          }
        },
        error: function(){
          $("#avatar").val("");
          $("#alertIMG").html("<strong>Error desconocido</strong>");
          $("#alertIMG").removeClass("alert-info");
          $("#alertIMG").addClass("alert-danger");
          $("#alertIMG").slideDown();
          alertHideTOut = setTimeout(function(){
            $("#alertIMG").slideUp();
          }, 5000);
        },
        complete: function(){
          $(".imgProcessing").slideUp();
        }
      });
    }
  })

  // Cada vez que cambia el correo en el input
  $("#email").change(function(){
    var email = $("#email").val();
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    // Si se ha subido imagen, y el correo es válido,
    // se muestra el botón de usar gravatar
    if($("#avatar").val() && regex.test(email))
      $("#useGravatar").slideDown();
    // Si no se ha subido pero el correo es válido, se usa gravatar directamente
    else if (!($("#avatar").val()) && regex.test(email))
      useGravatar();
    // Si se ha subido pero el correo no es válido, se oculta el botón
    else if ($("#avatar").val() && (!regex.test(email)))
      $("#useGravatar").slideUp();
    // Si no se ha subido y el correo no es válido, se oculta todo
    else {
      $("#useGravatar").slideUp();
      $("#alertIMG").slideUp();
      $("#avatar").val("");
      $("#avatar").change();
    }
  });

  // Cuando se pulsa en el botón, se usa Gravatar
  $("#useGravatar").click(function(e){
    e.preventDefault();
    $("#avatar").val("");
    $("#avatar").change();
    useGravatar();
  });

  function useGravatar(){
    $("#useGravatar").slideUp();
    var email = $("#email").val();
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(regex.test(email)){
      // Se ocultan el alert y la imagen si se estaban mostrando otros
        $(".tmpAvatar").hide();
        if($("#alertIMG").html().indexOf("Gravatar") === -1){
          clearTimeout(alertHideTOut);
          $("#alertIMG").slideUp();
        }
      // Se calcula el md5 del correo
      emailMD5 = $.md5(email.trim().toLowerCase());
      $(".tmpAvatar").attr(
        "src", "https://gravatar.com/avatar/"+emailMD5+"?d=retro&s=256"
      );
      $(".tmpAvatar").show();
      // Se muestra alert como que la imagen está sacada de Gravatar
      $("#alertIMG").html("Powered by " +
          "<strong><a href='\/\/gravatar.com'>Gravatar</a></strong>");
      $("#alertIMG").removeClass("alert-danger");
      $("#alertIMG").addClass("alert-info");
      $("#alertIMG").slideDown();
    }
  };
});