<?php
// Si se ha subido un archivo
if(isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])){
  // Nombre con fecha para evitar duplicados
  $uploadedVideoTmpName = time()."_".$_FILES['video']['name']; 
  // Ruta temporal para el vídeo subido
  $tempVideo = $_SERVER['DOCUMENT_ROOT']."/videos/tmp/".$uploadedVideoTmpName; 
  // Mover el vídeo a la ruta temporal
  move_uploaded_file($_FILES['video']['tmp_name'], $tempVideo); 
  // Generar ID random
  $videoID = generateRandomString(); 
?>
<div id="encode360Progress"></div>
<div id="encode720Progress"></div>
<div id="videoLink"></div>
<script>
  // Codificar vídeo
  function encode(resolution){ 
    $.post("encode.php", {
      "tempVideo": <?php echo "\"$tempVideo\"" ?>,
      "resolution": resolution,
      "videoID": <?php echo "\"$videoID\"" ?>});
    setTimeout(function(){checkProgress(resolution)}, 2000);
  }

  // Borrar archivos temporales
  function clean(){ 
    $.post("encode.php", {
      "clean": true,
      "tempVideo": <?php echo "\"$tempVideo\"" ?>,
      "videoID": <?php echo "\"$videoID\"" ?>});
  }

  // Comprobar progreso
  function checkProgress(resolution){ 
    $.post("encodeProgress.php", {"videoID": <?php echo "\"$videoID\"" ?>}, 
      function(data){
      $("#encode"+resolution+"Progress")
        .html("Codificando "+ resolution +"p: " + data.progress + "%");
      // Si se ha acabado
      if(data.finished){
        // Si se ha acabado con el 360p, se inicia 
        // el proceso para 720p y se muestra el enlace
        if(resolution == "360"){ 
          setTimeout(function(){encode("720")}, 2000);
          $("#videoLink")
            .html("Procesado<br>"
              + "<a href='/ver?video="+<?php echo "\"$videoID\"" ?>+"'>"
              + "Enlace al vídeo</a>");
        }
        // Si se ha acabaco con el 720p
        if(resolution == "720") 
          // Se limpia
          clean(); 
      // Si no se ha acabado (Se comprueba explícitamente 
      // si es false por si el php no devuelve respuesta)
      }else if (data.finished == false){ 
        // Volver a ejecutar esta función para ir actualizanco el porcentaje
        setTimeout(function(){checkProgress(resolution)}, 2000); 
      }
    }, "json");
  };

  // Empezar con el vídeo 360p
  setTimeout(function(){encode("360")}, 200); 
</script>
<?php } 

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

?>