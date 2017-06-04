<?php
// Generados de IDs aleatorios
function generateRandomString($length = 8) { 
  // Caracteres válidos para el ID
  $characters = 
    '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-'; 
  // Longitud del ID
  $charactersLength = strlen($characters);
  $randomString = '';
  // Por cada caracter que se quiere generar
  for ($i = 0; $i < $length; $i++) { 
    // Elige uno al azar y lo añade al ID
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  // Devuelde el ID
  return $randomString;
}

// Comprobador de FPS. Si se pasa de 30, se divide por las veces que se pasa
function getFPS($videoFile) { 
  global $webServerRoot;
  $output = shell_exec("bash ".$webServerRoot."/sh/checkfps.sh"
    ." \"".$videoFile."\"");
  $res = eval("return ".$output.";");
  if ($res > 30) 
    return "30/1";
  else
    // Sino, devuelve la división que imprime ffmpeg.
    return $output;
  // Por ejemplo: Si es un valor tipo 23.976, ffmpeg imprime 24000/1001,
  // ya que el resultado es más exacto que un número decimal definido,
  // y así se recomienda en la documentación de ffmpeg.
}

// Convertir
function convert($resolution, $tmpVideo, $idVideo){
  global $webServerRoot;
  // Calcular si se pone de máximo altura o anchura
  $videoRes = getResolution($tmpVideo);
  $heightIsMaximum = ($videoRes[1]/($videoRes[0]/640) > 360);
  if($heightIsMaximum)
    $scale = "-2:$resolution";
  else
    $scale = (($resolution == 360)?"640":"1280").":-2";
  $scale = "scale=".$scale;
  // Obtener FPS para codificar el vídeo
  $videoFPS = getFPS($tmpVideo);
  $output = array();
  $return_var = -1;
  exec($webServerRoot."/sh/codificar"
    .$resolution."p.sh"." \"".$tmpVideo."\" \"".$idVideo."\""
    ." \"".$videoFPS."\" \"".$webServerRoot."\" \"".$scale."\"",
    $output, $return_var);

  return $return_var;
}

// Obtener duración del vídeo
function getDuration($videoFile){
  global $webServerRoot;
  $output = shell_exec("bash ".$webServerRoot."/sh/getDuration.sh"
    ." \"".$videoFile."\"");
  $output = explode("\n", $output);
  foreach ($output as $duration) {
    if($duration != "N/A" && $duration != "")
      break;
  }
  return $duration;
}

// Obtener resolución
function getResolution($videoFile){
  global $webServerRoot;
  $output = shell_exec("bash ".$webServerRoot."/sh/getResolution.sh"
    ." \"".$videoFile."\"");
  $videoRes = explode("\n", $output);
  return $videoRes;
}

// Crear imagen con minuaturas del vídeo
function createThumbnails($idVideo){
  global $webServerRoot;

  if(!is_dir($webServerRoot."/videos/tmp/thumbs"))
    mkdir($webServerRoot."/videos/tmp/thumbs");

  if(!is_dir($webServerRoot."/videos/thumbs"))
    mkdir($webServerRoot."/videos/thumbs");

  $video = $webServerRoot."/videos/360/$idVideo.mp4";

  $output = shell_exec("bash ".$webServerRoot."/sh/getDuration.sh"
    ." \"".$video."\"");
  $output = explode("\n", $output);
  foreach ($output as $duration) {
    if($duration != "N/A" && $duration != "")
      break;
  }

  mkdir($webServerRoot."/videos/tmp/thumbs/$idVideo");
  $stack = new \Imagick();

  for($i = 1; $i <= 12; $i++){
    $seek = ($duration/12)*($i-1);
    exec("bash ".$webServerRoot."/sh/generateThumb.sh"
      ." \"".$video."\" \"".$seek."\""
      ." \"".$idVideo."\" \"".(($i < 10)?("0".$i):$i)."\""
      ." \"".$webServerRoot."\"");
    $stack->addImage(new \Imagick(
      $webServerRoot."/videos/tmp/thumbs/$idVideo/"
      .(($i < 10)?("0".$i):$i).".png")
    );
  }

  $montage = $stack->montageImage(new ImagickDraw(), '12x', '400x225', 0, '0');
  $montage->setImageFormat("jpg");
  $montage->setImageCompression(Imagick::COMPRESSION_JPEG); 
  $montage->setImageCompressionQuality(55);
  $montage->writeImage($webServerRoot."/videos/thumbs/$idVideo.jpg");

  $target = $webServerRoot."/videos/tmp/thumbs/$idVideo";
  for($i = 1; $i <= 12; $i++){
    unlink($target."/".(($i < 10)?("0".$i):$i).".png");
  }
  rmdir($target);
}
?>