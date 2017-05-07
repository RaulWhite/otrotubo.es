<?php
if(isset($_POST["videoID"])){
  $videoID = $_POST["videoID"];
  if (is_file($_SERVER['DOCUMENT_ROOT']."/videos/log/$videoID.log")){
    // Obtener el archivo log del proceso de ffmpeg
    $logFile = file($_SERVER['DOCUMENT_ROOT']."/videos/log/$videoID.log");
    $logFile = implode("\n", $logFile);

    preg_match_all("/Duration: (.*?), start:/", $logFile, $durationArray);
    // Obtener duración del vídeo (en formato hh:mm:ss.cc)
    $videoLengthString = end($durationArray[1]); 

    // Convertir el tiempo obtenido en segundps (decimal con centésimas)
    $videoLength = parseFfmpegTime($videoLengthString);

    // Obtener estado actual (última línea de tiempo de codificación de vídeo)
    // ffmpeg imprime en todo momento la posición del vídeo por donde va
    preg_match_all("/time=(.*?) bitrate/", $logFile, $actualTimeArray);
    $actualTimeString = end($actualTimeArray[1]);
    $actualTime = parseFfmpegTime($actualTimeString);

    // Progreso actual. Es el cálculo del tiempo actual entre el total
    // (round para poder devolver porcentaje sin decimales)
    $actualProgress = round(($actualTime / $videoLength) * 100);
    $finishedProgress = ($actualProgress == 100);

    // Devolución para llamada de AJAX en la página de subida
    header("Content-Type: application/json");
    echo json_encode(array(
      "progress" => $actualProgress,
      "finished" => $finishedProgress)
      );
  }
}

function parseFfmpegTime($timeString){
  $timeArray = explode(":", $timeString);
  $timeFloat = 
    ($timeArray[2]) + // Segundos y centésimas
    ($timeArray[1] * 60) + // Minutos
    ($timeArray[0] * 60 * 60); // Horas
  return $timeFloat;
}
?>