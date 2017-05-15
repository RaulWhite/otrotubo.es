<?php
$image = $_FILES['file0'];
// Si se ha subido un archivo, y es una imagen
if(is_uploaded_file($image['tmp_name'])
  && getimagesize($image['tmp_name']) !== false){
  // Nombre temporal con número aleatorio
  $newTempImg = "/avatars/tmp/".rand(0, 99999).$image['name'];
  // Si no existe la carpeta de avatares temporales, se crea
  if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/avatars/tmp"))
    mkdir($_SERVER["DOCUMENT_ROOT"]."/avatars/tmp", 0755);
  // Se mueve el archivo al directorio temporal
  move_uploaded_file($image['tmp_name'], 
    $_SERVER["DOCUMENT_ROOT"].$newTempImg);
  // Redimensionar a 500x500
  $imagisk = new \Imagick(realpath($_SERVER["DOCUMENT_ROOT"].$newTempImg));
  $imagisk->scaleImage(500,500,true);
  $imagisk->writeImage($_SERVER["DOCUMENT_ROOT"].$newTempImg);
  // Se envía la ruta
  echo json_encode(array(
    "checkSuccess" => true,
    "tmpImgPath" => $newTempImg
  ));
// Si se ha subido un archivo, pero no es una imagen  
} else if(is_uploaded_file($image['tmp_name'])
  && getimagesize($image['tmp_name']) === false){
    echo json_encode(array(
    "checkSuccess" => false,
    "message" => "El archivo subido no es una imagen"
  ));
// Si no se ha subido un archivo
} else if (!is_uploaded_file($image['tmp_name'])){
  echo json_encode(array(
    "checkSuccess" => false,
    "message" => "Error en el proceso de subida"
  ));
}

// Borrar archivos temporales que llevan más de 1 minuto
if(is_dir($_SERVER['DOCUMENT_ROOT']."/avatars/tmp")){
  $dir = $_SERVER['DOCUMENT_ROOT']."/avatars/tmp";
  $directory = opendir($dir);
  // Mientras hay archivos pendientes de revisar en la carpeta
  while(false !== ($actual = readdir($directory))){
    // Si han pasado más de 1 minuto la creación del archivo, se elimina
    if($actual != "." && $actual != ".."
    && time() - filectime($dir."/".$actual) > 60){
      unlink($dir."/".$actual);
    }
  }
}

?>