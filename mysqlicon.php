<?php
// Función que devuelve una conexión con la BD
function dbCon(){
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
  return $con;
}
?>