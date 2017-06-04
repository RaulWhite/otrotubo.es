<?php
// Clase usuario
require_once($_SERVER["DOCUMENT_ROOT"]."/login/claseUsuario.php");
session_start();

// Si se ha querido iniciar o cerrar sesión, se llama al archivo que procesa el login
if (isset($_POST['loginRequest']) || isset($_POST['logoutRequest']))
  require_once($_SERVER["DOCUMENT_ROOT"]."/login/login.php");

// Función para imprimir head de HTML (con title como parámetro)
// y header de la página
function getHeader($tituloHeader){ ?>
  <!DOCTYPE html>
  <html lang="es">
    <head>
      <!-- Si JS está desactivado, redirigir a página de advertencia -->
      <noscript>
        <meta http-equiv="refresh" content="0;url=/noScript">
      </noscript>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, user-scalable=no">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <meta name="theme-color" content="#DB0000">
      <meta name="msapplication-TileColor" content="#DB0000">

      <!-- http://browser-update.org/ - Código JS para detectar navegadores desactualizados -->
      <script> 
        var $buoop = {
          vs:{i:9,f:46,o:-8,s:7,c:48},
          reminder:0,
          reminderClosed:0,
          mobile:false,
          api:4,
          noclose: false,
          text:"Su navegador está desactualizado. "
            + "Por favor, actualice para poder usar todas las funciones de esta página."
        };
        function $buo_f(){ 
        var e = document.createElement("script"); 
        e.src = "//browser-update.org/update.min.js"; 
        document.body.appendChild(e);
        };
        try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
        catch(e){window.attachEvent("onload", $buo_f)}
      </script>

      <!-- JQuery CDN -->
      <script
      src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

      <!-- JQuery Form -->
      <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.1/jquery.form.min.js"
      integrity="sha384-tIwI8+qJdZBtYYCKwRkjxBGQVZS3gGozr3CtI+5JF/oL1JmPEHzCEnIKbDbLTCer"
      crossorigin="anonymous"></script>
      
      <!-- Bootstrap CDN -->
      <link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
      crossorigin="anonymous">
      <link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
      integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
      crossorigin="anonymous">
      <script
      src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
      integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
      crossorigin="anonymous"></script>
      <script src='https://www.google.com/recaptcha/api.js'></script>
      
      <script>
      // JS de animación para dropdowns del navbar
      $(document).ready(function(){
        $('.navbar-nav li.dropdown').on('show.bs.dropdown', function() {
          $(this).find('.dropdown-menu').first().stop(true, true).slideDown();
        });
        $('.navbar-nav .dropdown').on('hide.bs.dropdown', function(e) {
          e.preventDefault();
          $(this).find('.dropdown-menu').first().stop(true, true).slideUp('default', function(){
            $('.navbar-nav li.dropdown').removeClass('open');
          });
        });

        if (window.innerWidth < 768) {
          $(".navbar-cabecera").removeClass("navbar-fixed-top");
        }
      })

      // JS para quitar el fixed del navbar en resoluciones móviles (< 768px)
      $(window).resize(function() {
        if (window.innerWidth < 768) {
          $(".navbar-cabecera").removeClass("navbar-fixed-top");
        } else {
          $(".navbar-cabecera").addClass("navbar-fixed-top");
        }
      });
      </script>
      
      <!-- Font Awesome CDN -->
      <link rel="stylesheet" 
      href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
      integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
      crossorigin="anonymous">

      <!-- Custom CSSs -->
      <link rel="stylesheet" href="/css/headerStyle.css">
      <link rel="stylesheet" href="/css/style.css">
      
      <!-- Banner de política de Cookies. Generado en cookieconsent.insites.com -->
      <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
      <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
      <script>
      window.addEventListener("load", function(){
      window.cookieconsent.initialise({
        "palette": {
          "popup": {
            "background": "#aa0000",
            "text": "#ffdddd"
          },
          "button": {
            "background": "#ff0000"
          }
        },
        "theme": "edgeless",
        "position": "bottom-right",
        "content": {
          "message": "Esta página web usa cookies para asegurar un correcto funcionamiento de esta.",
          "dismiss": "OK",
          "link": "Más información",
          "href": "http://politicadecookies.com/"
        }
      })});
      </script>

      <!-- Comprobar compatibilidad con h.264 -->
      <script>
        var vid = document.createElement('video');
        compH264 = vid.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"');
        var compatible;
        if(compH264 !== "probably"){
          window.onload = function(){
            document.getElementsByClassName("alertNotComp")[0].style.display = "block";
            var ua = navigator.userAgent.toLowerCase();
            if(ua.indexOf('windows nt 5.1') > 0 || ua.indexOf('windows nt 5.2') > 0)
              document.getElementByClassName("firefoxXP")[0].style.display = "block";
          }
        }
      </script>

      <?php
      // Título de la página proporcionado por el parámetro de la función,
      // seguido del dominio. Ejemplo: Big buck Bunny - otrotubo.es
      echo "<title>"
        .(isset($tituloHeader)?$tituloHeader." | ":"")
        ."otrotubo.es</title>";
      ?>
    </head>
    <body>
      <nav class="navbar navbar-default navbar-fixed-top navbar-cabecera">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button"
            class="navbar-toggle collapsed"
            data-toggle="collapse" data-target="#nav-menu"
            aria-expanded="false">
              <span class="sr-only">Mostrar menú</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
              <span><img alt="otrotubo.es" src="/images/logo.svg"></span>
              <span>otrotubo.es</span>
            </a>
          </div>
          <div class="collapse navbar-collapse" id="nav-menu">
            <form class="navbar-form navbar-left" role="search">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar">
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-default btn-danger">
                    <i class="fa fa-search"></i>
                  </button>
                </span>                
              </div>
            </form>
            <ul class="nav navbar-nav navbar-right">
              <?php // Si hay usuario logueado
              if(isset($_SESSION['isLoged']) && $_SESSION['isLoged']){
                $logedUser = $_SESSION['logedUser'] ?>
                <li class="dropdown loged-dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php if($logedUser->getAvatar() != null) { ?>
                      <img class="userAvatar" 
                      src=<?php echo "\"".$logedUser->getAvatar()."\"" ?>>
                    <?php } ?>
                    <?php echo htmlentities($logedUser->getNick()); ?>
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="/perfil">
                      <button
                      class="btn btn-default btn-danger btn-block profileDropButton">
                        Mi perfil
                      </button>
                    </a></li>
                    <li><a href="/myVideos">
                      <button
                      class="btn btn-default btn-danger btn-block profileDropButton">
                        Mis vídeos
                      </button>
                    </a></li>
                    <li><a>
                      <form method="POST">
                        <button type="submit"
                        class="btn btn-default btn-danger btn-block profileDropButton"
                        name="logoutRequest" id="logoutRequest">
                          Cerrar sesión
                        </button>
                      </form>
                    </a></li>
                  </ul>
                </li>
                <li><a href="/upload">Subir vídeo</a></li>
              <?php } else { // Si no hay usuario logueado ?>
                <li class="dropdown login-dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user-circle-o"></i>
                    &nbsp;Iniciar sesión
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <form method="POST">
                      <div class="form-group">
                        <input type="text" class="form-control"
                        name="nick" id="nick" placeholder="Nick / Email" required>
                      </div>
                      <div class="form-group">
                        <input type="password" class="form-control"
                        name="pass" id="pass" placeholder="Contraseña" required>
                      </div>
                      <button type="submit"
                      class="btn btn-default btn-danger btn-block submitButton"
                      name="loginRequest" id="loginRequest">
                        Iniciar sesión
                      </button>
                    </form>
                  </ul>
                </li>
                <li><a href="/register">Registrarse</a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </nav>
      <div class="alertNotComp" style="display:none">
        <div class="alert alert-danger">
          <p>
            Su navegador no es compatible con vídeos codificados en h.264,
            lo que significa que no podrá visualizar los vídeos de esta plataforma.
            Por favor, actualice su navegador.
            <span class="firefoxXP" style="display:none">
              En Windows XP / Windows Server 2003, Firefox no es compatible con el codec h.264.
              Por favor, use otro navegador como Google Chrome.
            </span>
          </p>
        </div>
      </div>
<?php } ?>