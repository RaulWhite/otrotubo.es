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
} 

// Si hay una petición POST del reCaptcha, cargar la clave secreta
if(isset($_POST["g-recaptcha-response"])){
  $gReSecret =
    parse_ini_file(
      dirname($_SERVER['DOCUMENT_ROOT'])."/g-recaptcha-secret.ini")['secret'];
  require_once($_SERVER['DOCUMENT_ROOT'].'/lib/recaptcha/src/autoload.php');
  $recaptcha = new \ReCaptcha\ReCaptcha($gReSecret);
  $gReResp =
    $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

  $passedCaptcha = $gReResp->isSuccess();
}

?>

<script src="/register/codeForm.js"></script>
<!-- Script para calcular md5 con JS -->
<script
src="/lib/jquery.md5.js">
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
      <div class="col-sm-6 col-xs-12 imgBlock">
        <div class="form-group">
          <label for="avatar">
            Avatar: <span class="text-danger">(Máximo 20MB)</span>
          </label>
          <div class="input-group">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                  Seleccionar
                  <input type="file" accept="image/*"
                  id="avatar" name="avatar" style="display:none">
              </span>
            </label>
            <input type="text" class="form-control"
            id="tmpFileName" name="tmpFileName"
            value="Ningún archivo seleccionado" disabled>
          </div>
          <div class="alert" id="alertIMG" style="display:none"></div>
          <button id="useGravatar"
          class="btn btn-default btn-info" style="display:none">
            Usar avatar de <strong>Gravatar</strong>
          </button>
          <div class="imgProcessing" style="display: none">
            <i class="fa fa-refresh fa-5x imgProcessing"></i>
          </div>
          <img class="text-center tmpAvatar img-responsive" style="display:none">
        </div>
      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="form-group">
          <label for="bio">Descripción:</label>
          <textarea class="form-control" rows="5"
          id="bio" name="bio" placeholder="Descripción"></textarea>
          <br>
          <label for="recaptcha">Captcha:</label>
          <div class="g-recaptcha" id="recaptcha"
          data-sitekey="6LdjTSIUAAAAADZmrDjz1X6UayHpg5-OAikK2WIr">
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
  </div>
</form>

  </body>
</html>