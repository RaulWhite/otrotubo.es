<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Registro");

// Si ya está logueado, volver al inicio
if(isset($_SESSION['isLoged']) && $_SESSION['isLoged']){ ?>
  <script>window.location.replace("/");</script>
  <noscript>
    Usted ya está registrado y logueado.
    <a href="/">Volver al inicio</a>
  </noscript>
  <?php die();
} ?>

<script>
  $(document).ready(function(){
    // Cada vez que se selecciona un archivo en el formulario
    $("#avatar").change(function(){
      var formData = new FormData();
      // Archivo del input "avatar"
      $(".alert").remove();
      $(".tmpAvatar").attr("src", "");
      $(".tmpAvatar").hide();
      $.each($("#avatar")[0].files, function(i, file){
        formData.append("file"+i, file);
      });
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
            console.log(json);
            $(".tmpAvatar").attr("src", json.tmpImgPath);
            $(".tmpAvatar").show();
            $("#registerForm").append(
              "<input type='hidden' name='tmpAvatarPath' id='tmpAvatarPath'" +
              "value=" + json.tmpImgPath);
          // Sino, se muestra el mensaje de error como alert de bootstrap
          } else {
            $("input#avatar").closest(".form-group").append(
              "<div class='alert alert-danger' style='display:none'>" +
                "<strong>Error </strong>" + json.message +
              "</div>"
            );
            $(".alert").slideDown();
            setTimeout(function(){
              $(".alert").slideUp();
            }, 5000);
          }
        }
      });
    })
  });
</script>

<div class="container">
  <div class="row">
    <div class="col-xs-12">
      <h2 class="registerTitle text-center">
        Regístrate en <span class="label label-danger">otrotubo.es</span>
      </h2>
    </div>
  </div>
</div>
<hr>
<form method="POST" enctype="multipart/form-data"
name="registerForm" id="registerForm">
  <div class="container">
    <div class="row">
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="nick">Nick:</label>
          <input type="text" class="form-control"
          id="nick" name="nick" placeholder="Nick">
        </div>
      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control"
          id="email" name="email" placeholder="Email">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="pass">Contraseña:</label>
          <input type="password" class="form-control"
          id="pass" name="pass" placeholder="Contraseña">
        </div>
      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="passRepeat">Repetir contraseña:</label>
          <input type="password" class="form-control"
          id="passRepeat" name="passRepeat" placeholder="Repetir contraseña">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="avatar">Avatar:</label>
          <label class="btn btn-default btn-sm">
            Seleccionar
            <input type="file" id="avatar" name="avatar" style="display:none">
          </label>
          <br><br>
          <img class="text-center tmpAvatar img-responsive" style="display:none">
        </div>
      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="bio">Descripción:</label>
          <textarea class="form-control" rows="5"
          id="bio" name="bio" placeholder="Descripción"></textarea>
        </div>
        <br>
        <div class="text-right">
          <button type="submit" class="btn btn-default btn-danger btn-lg">
            Registrarse
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

  </body>
</html>