<?php
// Si se ha subido un archivo
if(isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])){
  $uploadedVideoTmpName = time()."_".$_FILES['video']['name']; // Nombre con fecha para evitar duplicados
  $tempVideo = $_SERVER['DOCUMENT_ROOT']."/videos/tmp/".$uploadedVideoTmpName; // Ruta temporal para el vídeo subido
  move_uploaded_file($_FILES['video']['tmp_name'], $tempVideo); // Mover el vídeo a la ruta temporal
  $videoID = generateRandomString(); // Generar ID random
?>
<div id="encode360Progress"></div>
<div id="encode720Progress"></div>
<div id="videoLink"></div>
<script>
  function encode(resolution){ // Codificar vídeo
    $.post("encode.php", {
      "tempVideo": <?php echo "\"$tempVideo\"" ?>,
      "resolution": resolution,
      "videoID": <?php echo "\"$videoID\"" ?>});
    setTimeout(function(){checkProgress(resolution)}, 2000);
  }

  function clean(){ // Borrar archivos temporales
    $.post("encode.php", {
      "clean": true,
      "tempVideo": <?php echo "\"$tempVideo\"" ?>,
      "videoID": <?php echo "\"$videoID\"" ?>});
  }

  function checkProgress(resolution){ // Comprobar progreso
    $.post("encodeProgress.php", {"videoID": <?php echo "\"$videoID\"" ?>}, function(data){
      $("#encode"+resolution+"Progress").html("Codificando "+ resolution +"p: " + data.progress + "%");
      if(data.finished){ // Si se ha acabado
        if(resolution == "360"){ // Si se ha acabado con el 360p, se inicia el proceso para 720p y se muestra el enlace
          setTimeout(function(){encode("720")}, 2000);
          $("#videoLink").html("Procesado<br><a href='/ver?video="+<?php echo "\"$videoID\"" ?>+"'>Enlace al vídeo</a>");
        }
        if(resolution == "720") // Sei se ha acabaco con el 720p
          clean(); // Se limpia
      }else if (data.finished == false){ // Si no se ha acabado (Se comprueba explícitamente si es false por si el php no devuelve respuesta)
        setTimeout(function(){checkProgress(resolution)}, 2000); // Volver a ejecutar esta función para ir actualizanco el porcentaje
      }
    }, "json");
  };

  setTimeout(function(){encode("360")}, 200); // Empezar con el vídeo 360p
</script>
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

?>