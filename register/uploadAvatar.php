<?php
$image = $_FILES['file0'];
// Si se ha subido un archivo, y es una imagen (no SVG) de 2MB o menos
if(is_uploaded_file($image['tmp_name'])
  && (strstr(mime_content_type($image['tmp_name']), "image/"))
  && !(strstr(mime_content_type($image['tmp_name']), "svg"))
  && filesize($image['tmp_name']) <= 2097152){
  // Ruta de imagen temporal con nombre original y número aleatorio
  $newTempImg = $_SERVER["DOCUMENT_ROOT"]
    ."/avatars/tmp/".rand(0, 99999)."_".$image['name'];
  // Si no existe la carpeta de avatares temporales, se crea
  if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/avatars/tmp"))
    mkdir($_SERVER["DOCUMENT_ROOT"]."/avatars/tmp", 0755);
  // Se mueve el archivo al directorio temporal
  move_uploaded_file($image['tmp_name'], $newTempImg);
  // Tamaño de la imagen
  $imgSize = getimagesize($newTempImg);
  // Redimensionar a 500x500 si es más grande
  $original = new \Imagick(realpath($newTempImg));
  $imagick = new \Imagick();
  // Si no es gif, escala la imagen
  if(!isImageAnimated($newTempImg)){
    $imagick->newImage($imgSize[0], $imgSize[1], "white");
    $imagick->compositeImage($original, Imagick::COMPOSITE_OVER, 0, 0);
    $imagick->setImageFormat("jpg");
    if($imgSize[0] > 500 || $imgSize[1] > 500)
      $imagick->resizeImage(500,500,25,1,true);
    $imagick->setImageCompression(Imagick::COMPRESSION_JPEG); 
    $imagick->setImageCompressionQuality(55);
    unlink($newTempImg);
    $newTempImg.=".jpg";
    $imagick->writeImage($newTempImg);
  // Si es gif, escala los cuadros por separado y los vuelve a unir
  } else {
    if($imgSize[0] > 500 || $imgSize[1] > 500){
      $imagick = $original->coalesceImages();
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
// Si se ha subido un archivo de más de 2MB
} else if((($imageSize = filesize($image['tmp_name'])) > 2097152)
  || (isset($_POST["tooLarge"]) && $_POST["tooLarge"] 
    && $imageSize = $_POST["imageSize"])){
  echo json_encode(array(
    "checkSuccess" => false,
    "message" => "El archivo subido es de más de 2MB "
      ."(".rtrim(human_filesize($imageSize), "B")."B)"
  ));
// Si se ha subido un archivo, y es una imagen SVG
} else if(strstr(mime_content_type($image['tmp_name']), "svg")){
  echo json_encode(array(
    "checkSuccess" => false,
    "message" => "Lo sentimos. Archivos de tipo SVG no están soportados"
  ));
// Si se ha subido un archivo, pero no es una imagen
} else if((is_uploaded_file($image['tmp_name'])
    && !strstr(mime_content_type($image['tmp_name']), "image/"))
  || (isset($_POST["notImage"]) && $_POST["notImage"])){
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

// Función para pasar el tamaño del archivo a "humano"
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
?>