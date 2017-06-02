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
  // Obtener FPS para codificar el vídeo
  $videoFPS = getFPS($tmpVideo);
  $output = array();
  $return_var = -1;
  exec($webServerRoot."/sh/codificar"
    .$resolution."p.sh"." \"".$tmpVideo."\" \"".$idVideo."\""
    ." \"".$videoFPS."\" \"".$webServerRoot."\"",
    $output, $return_var);

  return $return_var;
}

// Comprobador de resolución HD
function checkIfHD($videoFile){
  global $webServerRoot;
  $output = shell_exec("bash ".$webServerRoot."/sh/getResolution.sh"
    ." \"".$videoFile."\"");
  $videoRes = explode("\n", $output);
  return ($videoRes[0] >= 1280 || $videoRes[1] >= 720);
}
?>