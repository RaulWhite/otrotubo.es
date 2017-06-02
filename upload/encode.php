<?php
// Archivo con funciones de ayuda para la codificación
require_once($_SERVER["DOCUMENT_ROOT"]."/upload/encodeFunctions.php");

// Si se ha subido un archivo
if(isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])){
  // Si no es un vídeo
  if(strpos($_FILES['video']['type'], "video/") === false){
    echo json_encode(array(
      "mensaje" => "El archivo subido no es un vídeo",
      "success" => false
      )
    );
    exit();
  }

  // Nombre con fecha para evitar duplicados
  $uploadedVideoTmpName = time()."_".$_FILES['video']['name']; 
  // Ruta temporal para el vídeo subido
  $tempVideo = $_SERVER['DOCUMENT_ROOT']."/videos/tmp/".$uploadedVideoTmpName; 
  // Mover el vídeo a la ruta temporal
  move_uploaded_file($_FILES['video']['tmp_name'], $tempVideo); 
  // Generar ID random
  $videoID = generateRandomString();
  
  // Codificar en 360p
  $return_var = convert(360);

  if($return_var === 0 && checkIfHD($tempVideo)){
    $return_var = convert(720);
  }

  if($return_var !== 0){
    unlink($tempVideo);
    unlink($_SERVER["DOCUMENT_ROOT"]."/videos/360/$videoID.mp4");
    unlink($_SERVER["DOCUMENT_ROOT"]."/videos/720/$videoID.mp4");
    echo json_encode(array(
      "mensaje" => "Error en la conversión",
      "success" => false
      )
    );
    exit();
  } else {
    unlink($tempVideo);
    echo json_encode(array(
      "mensaje" => "Procesado <a href='/ver?video=$videoID'>Enlace al vídeo</a>",
      "success" => true
      )
    );
  }
}

?>