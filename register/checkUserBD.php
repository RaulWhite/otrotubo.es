<?php
if(isset($_POST["check"]) && (isset($_POST["nick"]) || isset($_POST["email"]))){
  // Crear conexión con la BD
  require_once($_SERVER['DOCUMENT_ROOT']."/mysqlicon.php");
  $con = dbCon();
  $typeOfCheck = $_POST["check"];
  $strToCheck = $con->real_escape_string(trim($_POST[$typeOfCheck]));
  // Query buscando el usuario
  // Mysql no distingue mayúsculas de minúsculas con WHERE =
  $resu = $con->query(
    "SELECT $typeOfCheck FROM usuarios WHERE $typeOfCheck = '$strToCheck'"
  );
  // Si la query a devuelto algún resultado, existe
  echo json_encode(array(
    "exists" => ($resu->num_rows > 0)
  ));
  $con->close();
}
?>