<?php
// Si no se limpia y están todo slos datos
if(!isset($_POST["clean"]) 
    && isset($_POST["tempVideo"]) 
    && isset($_POST["resolution"]) 
    && isset($_POST["videoID"])){
  $tempVideo = $_POST["tempVideo"];
  $resolution = $_POST["resolution"];
  $videoID = $_POST["videoID"];
  $videoFPS = getFPS($tempVideo);
  $logFile = $_SERVER['DOCUMENT_ROOT']."/videos/log/$videoID.log";
  if(is_file($logFile))
    unlink($logFile);
  exec($_SERVER['DOCUMENT_ROOT']."/sh/codificar".$resolution."p.sh"
    ." \"".$tempVideo."\" \"".$videoID."\" \"".$videoFPS."\""
    ." \"".$_SERVER['DOCUMENT_ROOT']."\"");
}

// Si se limpia
if(isset($_POST["clean"])
    && $_POST["clean"]
    && isset($_POST["tempVideo"])
    && isset($_POST["videoID"])){
  $tempVideo = $_POST["tempVideo"];
  $videoID = $_POST["videoID"];
  $logFile = $_SERVER['DOCUMENT_ROOT']."/videos/log/$videoID.log";
  unlink($tempVideo);
  unlink($logFile);
}

// Comprobador de FPS. Si se pasa de 30, se divide por las veces que se pasa
function getFPS($videoFile) { 
  $output = shell_exec("bash ".$_SERVER['DOCUMENT_ROOT']."/sh/checkfps.sh"
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
?>