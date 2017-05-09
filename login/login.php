<?php
// Archivo ini con las credenciales para acceder a la BD.
// Se encuentra en la carpeta superior a la raíz de la página.
$bdCred = parse_ini_file(dirname($_SERVER['DOCUMENT_ROOT'])."/mysqlcon.ini");

if(isset($_POST['loginRequest']) && !isset($_SESSION['isLoged'])){
  login();
}

// Logout
if(isset($_POST['logoutRequest']) && isset($_SESSION['isLoged'])){
  session_destroy();
  $_SESSION['isLoged'] = false;
}

function login(){ // Función para hacer el login
  if(empty($_POST['nick'])){ // nick vacío
    $_SESSION['errorLogin'] = "<p>Por favor, introduzca el usuario</p>";
  } else if (empty($_POST['pass'])){ // Contraseña vacía
    $_SESSION['errorLogin'] = "<p>Por favor, introduzca la contraseña</p>";
    return;
  }

  $nick = $_POST['nick'];
  $pass = $_POST['pass'];
  global $bdCred;
  $con = new mysqli(
    "localhost",
    $bdCred['dbuser'],
    $bdCred['dbpass'],
    $bdCred['db']
  );
  $con->set_charset("utf8");
  $resu = $con->query(
    "SELECT `nick`, `pass` FROM `usuarios`
    WHERE `nick` = '$nick'"
  );

  // Si la query no ha devuelto nada (no hay usuario con ese nick)
  if(!($fila = $resu->fetch_row())){
    $_SESSION['errorLogin'] = "<p>Usuario no encontrado</p>";
  // Si las contraseñas codificadas no coinciden
  } else if (!(password_verify($pass, $fila[1]))){
    $_SESSION['errorLogin'] = "<p>Contraseña incorrecta</p>";
  // Sino, el login es válido
  } else {
    $_SESSION['errorLogin'] = null;
    $_SESSION['isLoged'] = true;
    setLogedUserObject($con);
  }
  $con->close();
}


function setLogedUserObject($con){ // Función para crear el objeto de usuario
  if(!isset($_SESSION['logedUser'])){
    /*global $bdCred;
    $con = new mysqli(
      "localhost",
      $bdCred['dbuser'],
      $bdCred['dbpass'],
      $bdCred['db']
    );*/
    $con->set_charset("utf8");
    $resu = $con->query(
      "SELECT `nick`, `email`, `nombre`, `avatar`, `bio`, `fechaRegistro`, `tipo`
      FROM `usuarios`
      WHERE `nick` = '".$_POST['nick']."'"
    );
    if($fila = $resu->fetch_row()){
      $actual = new Usuario();
      $actual->setNick($fila[0]);
      $actual->setEmail($fila[1]);
      $actual->setNombre($fila[2]);
      $actual->setAvatar($fila[3]);
      $actual->setBio($fila[4]);
      $actual->setFechaReg($fila[5]);
      $actual->setTipo($fila[6]);
      $_SESSION['logedUser'] = $actual;
    }
  }
  //$con->close();
}
?>
