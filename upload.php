<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Si se ha subido un archivo
if(isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])){
  $tmpname = time()."_".$_FILES['video']['name']; // Nombre con fecha para evitar duplicados
  $tempvideo = "videos/tmp/".$tmpname; // Ruta temporal para el vídeo subido
  move_uploaded_file($_FILES['video']['tmp_name'], $tempvideo); // Mover el vídeo a la ruta temporal
  $fps = checkfps();
  $videoID = generateRandomString(); // Generar ID random
  shell_exec("bash ".$_SERVER['DOCUMENT_ROOT']."/sh/codificar360p.sh \"".$tempvideo."\" \"".$videoID."\" \"".$fps."\""); // Codificar en 360p con nombre ID.mp4
  shell_exec("bash ".$_SERVER['DOCUMENT_ROOT']."/sh/codificar720p.sh \"".$tempvideo."\" \"".$videoID."\" \"".$fps."\""); // Codificar en 720p con nombre ID.mp4
  unlink($tempvideo); // Borrar vídeo subido al acabar el procesado
  echo "Procesado<br><a href='/ver?video=$videoID'>Enlace al vídeo</a>";
} else { // Si no se ha subido un archivo
  formulario(); // Se muestra el formulario
}

function formulario(){ // Formulario para subir un archivo ?>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="video"><br>
    <input type="submit">
  </form>
<?php } 

function generateRandomString($length = 8) { // Generados de IDs aleatorios
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-'; // Caracteres válidos para el ID
  $charactersLength = strlen($characters); // Longitud del ID
  $randomString = '';
  for ($i = 0; $i < $length; $i++) { // Por cada caracter que se quiere generar
    $randomString .= $characters[rand(0, $charactersLength - 1)]; // Elige uno al azar y lo añade al ID
  }
  return $randomString; // Devuelde el ID
} 

function checkFPS() { // Comprobador de FPS. Si se pasa de 30, se divide por las veces que se pasa
  global $tempvideo;
  $fps = shell_exec("bash ".$_SERVER['DOCUMENT_ROOT']."sh/checkfps.sh \"".$tempvideo."\"");
  $res = eval("return ".$fps.";");
  if ($res > 30)
    $fps = 30;
  return $fps;
} ?>