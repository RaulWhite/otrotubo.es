<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Inicio");

// Incluir formulario de subida si se está logueado
if(isset($_SESSION['isLoged']) && $_SESSION['isLoged'])
  require_once($_SERVER['DOCUMENT_ROOT']."/upload/upload.html");
?>

  </body>
</html>