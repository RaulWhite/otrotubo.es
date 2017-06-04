<?php
// Archivo con funciones de ayuda para la codificación
require_once("encodeFunctions.php");

// Ruta del servidor web, ya que este es un script que se ejecuta por terminal.
$webServerRoot = "/var/www/html/otrotubo";

// Archivo ini con las credenciales para acceder a la BD.
// Se encuentra en la carpeta superior a la raíz de la página.
$bdCred = parse_ini_file(dirname($webServerRoot)."/mysqlcon.ini");
$con = new mysqli(
  "localhost",
  $bdCred['dbuser'],
  $bdCred['dbpass'],
  $bdCred['db']
);
$con->set_charset("utf8");

// Si hay un vídeo procesandose en otra instancia del código
$resu = $con->query("SELECT idVideo FROM `videos` WHERE estado = 'encoding'");
if(!$resu || $resu->num_rows > 0)
  endScript(false);

// Consulta de próximo vídeo pendiente en la cosa
$resu = $con->query("SELECT idVideo FROM `videos` WHERE estado = 'queued' 
  ORDER BY fechaSubida ASC LIMIT 1");

// Si no hay vídeos pendientes en la cola, se termina el proceso
if(!$resu || $resu->num_rows == 0)
  endScript(false);

// ID del vídeo a procesar
$idVideo = $resu->fetch_assoc()["idVideo"];
// Archivo original subido
$tmpVideo = glob($webServerRoot."/videos/tmp/$idVideo.*")[0];

// Si no existe el archivo subido
if(!is_file($tmpVideo)){
  writeStateToBD("error");
  endScript(true);
}

// Indicar vídeo ocupado en cola
writeStateToBD("encoding");

// Codificar en 360p
$return_var = convert(360, $tmpVideo, $idVideo);

// Si no ha habido errores, y el vídeo original es HD, codificar en 720p
$videoRes = getResolution($tmpVideo);
if($return_var === 0){
  if ($videoRes[0] >= 1280 || $videoRes[1] >= 720){
    $return_var = convert(720, $tmpVideo, $idVideo);
    $isHD = true;
  } else 
    $isHD = false;
}

// Borrar archivo original
unlink($tmpVideo);

// Si ha habido errores en los procesos de conversión
if($return_var !== 0){
  // Borrar todos los vídeos relacionados
  $tmp360 = $webServerRoot."/videos/360/$idVideo.mp4";
  $tmp720 = $webServerRoot."/videos/360/$idVideo.mp4";
  if(is_file($tmp360)){unlink($tmp360);}
  if(is_file($tmp720)){unlink($tmp720);}
  // Indicar error en la BD
  writeStateToBD("error");
} else {
  // Si todo ha ido bien, se crean las miniaturas y se etiqueta el vídeo
  // como listo para visualizarse
  createThumbnails($idVideo);
  writeStateToBD("ready");
}

endScript(true);

// Función para indicar el nuevo estado de un vídeo en la BD
function writeStateToBD($estado){
  global $con;
  global $isHD;
  global $idVideo;
  $con->query("UPDATE `videos` SET estado = '$estado'"
    .((isset($isHD))?", isHD = ".($isHD?"true":"false"):"")
    ." WHERE idVideo = '".$con->real_escape_string($idVideo)."'");
}

// Función para volver a ejecutar este archivo y terminar el proceso actual
function endScript($rerun = false){
  if(isset($con))
    $con->close();
  global $webServerRoot;
  if($rerun)
    pclose(popen("php ".$webServerRoot."/upload/encode.php &","r"));
  exit();
}
?>