<?php
$image = $_FILES['file0'];
// Si se ha subido un archivo, y es una imagen
if(is_uploaded_file($image['tmp_name'])
  && ($imgSize = getimagesize($image['tmp_name'])) !== false){
  // Ruta de imagen temporal con nombre original y número aleatorio
  $newTempImg = $_SERVER["DOCUMENT_ROOT"]
    ."/avatars/tmp/".rand(0, 99999)."_".$image['name'];
  // Si no existe la carpeta de avatares temporales, se crea
  if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/avatars/tmp"))
    mkdir($_SERVER["DOCUMENT_ROOT"]."/avatars/tmp", 0755);
  // Se mueve el archivo al directorio temporal
  move_uploaded_file($image['tmp_name'], $newTempImg);
  // Redimensionar a 500x500 si es más grande
  if($imgSize[0] > 500 || $imgSize[1] > 500){
    $imagick = new \Imagick(realpath($newTempImg));
    // Si no es gif, escala la imagen
    if(!isImageAnimated($newTempImg)){
      $imagick->scaleImage(500,500,true);
      $imagick->writeImage($newTempImg);
    // Si es gif, escala lso cuadros por separado y los vuelve a unir
    } else {
      $imagick = $imagick->coalesceImages();
      foreach ($imagick as $frame) { 
        $frame->scaleImage(500,500,true);
      }
      $imagick = $imagick->deconstructImages();
      $imagick->writeImages($newTempImg, true);
    }
  }
  // Convertir a base64
  $extension = pathinfo(
    $newTempImg,PATHINFO_EXTENSION);
  $imgData = file_get_contents(
    $newTempImg,PATHINFO_EXTENSION);
  $newTempImgB64 = 'data:image/'.$extension
    .';base64,'.base64_encode($imgData);
  // Borrar imagen temporal
  unlink($newTempImg);
  // Se envía la imagen en base64
  echo json_encode(array(
    "checkSuccess" => true,
    "tmpImgPath" => $newTempImgB64
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
    "message" => "Algo ha ido mal en el proceso de subida"
  ));
}

// Función para comprobar si es una imagen animada (GIF o similar)
function isImageAnimated($file){
  $nb_image_frame = 0;
  $image = new Imagick($file);
  foreach($image->deconstructImages() as $i) {
    $nb_image_frame++;
    if ($nb_image_frame > 1) {
      return true;
    }
  }
  return false;
}
?>