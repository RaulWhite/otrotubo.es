<?php
// Clase de usuario
require_once($_SERVER["DOCUMENT_ROOT"]."/login/claseUsuario.php");
// Archivo con funciones de ayuda para la codificación
require_once($_SERVER["DOCUMENT_ROOT"]."/upload/encodeFunctions.php");

session_start();

if(is_uploaded_file($_FILES['video']['tmp_name'])
&& strpos($_FILES['video']['type'], "video/") === false){
  // Si hay archivo subido y no es un vídeo
  echo json_encode(array(
    "mensaje" => "El archivo subido no es un vídeo",
    "success" => false
    )
  );
  exit();
} else if(!is_uploaded_file($_FILES['video']['tmp_name'])){
  // No hay archivo subido (posible error)
  echo json_encode(array(
    "mensaje" => "Error al subir el archivo",
    "success" => false
    )
  );
  exit();
}

// Información del vídeo
$videoTitle = (isset($_POST["videoTitle"]) && $_POST["videoTitle"] != "")
  ?$_POST["videoTitle"]:pathinfo($_FILES["video"]["name"], PATHINFO_FILENAME);

$videoDesc = (isset($_POST["videoDesc"]) && $_POST["videoDesc"] != "")
  ?$_POST["videoDesc"]:NULL;

// Archivo ini con las credenciales para acceder a la BD.
// Se encuentra en la carpeta superior a la raíz de la página.
$bdCred = parse_ini_file(dirname($_SERVER['DOCUMENT_ROOT'])."/mysqlcon.ini");
$con = new mysqli(
  "localhost",
  $bdCred['dbuser'],
  $bdCred['dbpass'],
  $bdCred['db']
);
$con->set_charset("utf8");

// Generar ID random que no exista en la BD
do {
  $idVideo = generateRandomString();
  // Consulta del ID. Si falla la query, se indica error en BD
  if(!($resu = $con->query("SELECT `idVideo` FROM videos WHERE idVideo = '"
  .$con->real_escape_string($idVideo)."'"))){
    echo json_encode(array(
      "mensaje" => "Error en la base de datos",
      "success" => false
      )
    );
    $con->close();
    exit();
  }
// Repetir creación hasta que la BD no devuelva coincidenciasc
} while (($resu->num_rows) > 0);

// Ruta temporal para el vídeo subido
$tempVideo = $_SERVER['DOCUMENT_ROOT']
  ."/videos/tmp/".$idVideo."."
  .pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
// Mover el vídeo a la ruta temporal
move_uploaded_file($_FILES['video']['tmp_name'], $tempVideo);

// Insertar el vídeo en cola en la BD
$resu = $con->query("INSERT INTO `videos` "
  ."(idVideo, titulo, descripcion, estado, public, fechaSubida, usuarios_nick) VALUES('"
  .$con->real_escape_string($idVideo)."', '"
  .$con->real_escape_string($videoTitle)."', "
  .(is_null($videoDesc)?"NULL":"'".$con->real_escape_string($videoDesc)."'").", '"
  .$con->real_escape_string("queued")."', "
  .$con->real_escape_string(
    (isset($_POST["videoPublic"]) && $_POST["videoPublic"] == "on")
    ?"TRUE":"FALSE").", '"
  .$con->real_escape_string(date("Y-m-d H:i:s"))."', '"
  .$con->real_escape_string($_SESSION["logedUser"]->getNick())."')");

if($resu){
  // Si se ha colocado bien el vídeo en la cola
  echo json_encode(array(
    "mensaje" => "Vídeo subido",
    "idVideo" => $idVideo,
    "success" => true
    )
  );
  // Ejecutar codificador en PHP
  pclose(popen("php ".$_SERVER["DOCUMENT_ROOT"]."/upload/encode.php &","r"));
} else {
  echo json_encode(array(
    "mensaje" => "Error en la base de datos",
    "success" => false
    )
  );
}
?>