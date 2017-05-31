<?php
if(isset($_POST["check"]) && (isset($_POST["nick"]) || isset($_POST["email"]))){
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
}
?>