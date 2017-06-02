<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Subir vídeo"); ?>

<h2 class="text-center">Subir un vídeo</h2>

<?php
// Incluir formulario de subida si se está logueado
if(isset($_SESSION['isLoged']) && $_SESSION['isLoged'])
  require_once($_SERVER['DOCUMENT_ROOT']."/upload/upload.html");
else {?>
  <div class="text-center">
    <div style="display: inline-block" class="alert alert-danger">
      <p style="display: inline-block" class='text-center'>
        Debe estar registrado y logueado para poder subir vídeos
        en esta la plataforma
      </p>
    </div>
  </div>
<?php } ?>

  </body>
</html>