// Declaración global para tiempo de espera de ocultación de errores
var alertHideTOut;
// Declaración global para petición asíncrona
var ajaxAvatar;

$(document).ready(function(){
  $(".tmpAvatar").attr("src",$(".userPage .userAvatar").attr("src"));
  if(usingGravatar){
    useGravatar();
  } else {
    $("#useGravatar").slideDown();
  }

  $("#avatar").change(function(){
    // Se ocultan el alert y la imagen si se estaban mostrando
    $(".tmpAvatar").hide();
    clearTimeout(alertHideTOut);
    $("#alertIMG").slideUp();
    $("#alertIMG").removeClass("alert-danger");
    // Se muestra el nombre de la imagen o que no hay nada seleccionado
    $("#tmpFileName").val(($(this).val() !== "") ?
      $(this).val().replace(/\\/g, '/').replace(/.*\//, '')
      : "Ningún archivo seleccionado");
    var formData = new FormData();
    // Archivo del input "avatar"
    $(".tmpAvatar").hide();
    $(".tmpAvatar").attr("src", "");
    // Si se ha quitado el archivo subido (se ha vaciado el input),
    // se intenta usar Gravatar
    if(!$("#avatar").get(0).files.length){
      useGravatar();
      $("#useGravatar").slideUp();
    }
    // Si hay archivo seleccionado
    if($("#avatar").get(0).files.length){
      // Si es de más de 2 MB
      if ($("#avatar")[0].files[0].size > 2097152){
        formData.append("tooLarge", true);
        formData.append("imageSize", $("#avatar")[0].files[0].size);
      // Si no es una imagen
      } else if($("#avatar")[0].files[0].type.indexOf("image") == -1)
        formData.append("notImage", true);
      // Si es una imagen de 2MB o menos (es válida)
      else{
        formData.append("file0", $("#avatar")[0].files[0]);
        // Se coloca el botón de Gravatar
        $("#useGravatar").slideDown();
      }
      // Mostrar animación de carga
      $(".imgProcessing").slideDown();
      if(typeof(ajaxAvatar) != "undefined")
        ajaxAvatar.abort();
      ajaxAvatar = $.ajax({
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
            usingGravatar = false;
          // Sino, se muestra el mensaje de error como alert de bootstrap
          } else {
            $("#avatar").val("");
            $("#tmpFileName").val("Ningún archivo seleccionado");
            $("#alertIMG").html("<strong>ERROR: </strong>" + json.message);
            $("#alertIMG").removeClass("alert-info");
            $("#alertIMG").addClass("alert-danger");
            $("#alertIMG").slideDown();
            alertHideTOut = setTimeout(function(){
              $("#alertIMG").slideUp();
              $("#alertIMG").removeClass("alert-danger");
            }, 5000);
          }
        },
        // Si ha habido algún error en la petición, se indica error desconocido
        error: function(){
          $("#avatar").val("");
          usingGravatar = true;
          $("#tmpFileName").val("Ningún archivo seleccionado");
          $("#alertIMG").html("<strong>Error desconocido</strong>");
          $("#alertIMG").removeClass("alert-info");
          $("#alertIMG").addClass("alert-danger");
          $("#alertIMG").slideDown();
          alertHideTOut = setTimeout(function(){
            $("#alertIMG").slideUp();
            $("#alertIMG").removeClass("alert-danger");
          }, 5000);
        },
        complete: function(){
          $(".imgProcessing").slideUp();
        }
      });
    }
  })

  $("#useGravatar").click(function(e){
    e.preventDefault();
    $("#avatar").val("");
    $("#avatar").change();
    useGravatar();
  });

  // Al realizar el submit, si se está usando un avatar subido,
  // se pasa la codificación base64 a un input hidden.
  $("form#editProfile").submit(function(){
    if(!usingGravatar){
      var srcBase64 = $(".tmpAvatar").attr("src").slice(
        $(".tmpAvatar").attr("src").indexOf("base64,") + "base64,".length
      );
      $(this).append(
        "<input type='hidden' name='avatarBase64'"
        +"id='avatarBase64' value='"+srcBase64+"'>"
      );
    }
  });

  // Función para usar Gravatar
  function useGravatar(){
    if(typeof(ajaxAvatar) != "undefined")
      ajaxAvatar.abort();
    $("#useGravatar").slideUp();
    // Se ocultan el alert y la imagen si se estaban mostrando otros
    $(".tmpAvatar").hide();
    if($("#alertIMG").html().indexOf("Gravatar") === -1){
      clearTimeout(alertHideTOut);
      $("#alertIMG").slideUp();
      $("#alertIMG").removeClass("alert-danger");
    }
    $(".tmpAvatar").attr(
      "src", "https://gravatar.com/avatar/"+emailMD5+"?d=retro&s=500"
    );
    $(".tmpAvatar").show();
    usingGravatar = true;
    // Se muestra alert como que la imagen está sacada de Gravatar
    $("#alertIMG").html("Powered by " +
        "<strong><a href='\/\/gravatar.com'>Gravatar</a></strong>");
    $("#alertIMG").removeClass("alert-danger");
    $("#alertIMG").addClass("alert-info");
    $("#alertIMG").slideDown();
  };
});