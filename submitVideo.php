<?php
// Si se ha subido un archivo
if(isset($_FILES['video']) && is_uploaded_file($_FILES['video']['tmp_name'])){
  if(strpos($_FILES['video']['type'], "video/") === false){
    echo "El archivo subido no es un vídeo";
  } else {
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
  var tempVideo = <?php echo "\"$tempVideo\"" ?>;
  var videoID =  <?php echo "\"$videoID\"" ?>;
</script>
<script src="processVideoCode.js"></script>
<?php }}

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