<?php
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

  // Crear conexión con la BD
  require_once($_SERVER['DOCUMENT_ROOT']."/mysqlicon.php");
  $con = dbCon();

  $nickParsed = $con->real_escape_string(trim($nick));
  $resu = $con->query(
    "SELECT `nick`, `pass` FROM `usuarios`
    WHERE `nick` = '$nickParsed' OR `email` = '$nickParsed'"
  );

  // Si la query no ha devuelto nada (no hay usuario con ese nick o email)
  if(!($fila = $resu->fetch_row())){
    $_SESSION['errorLogin'] = "<p>Usuario no encontrado</p>";
  // Si las contraseñas codificadas no coinciden
  } else if (!(password_verify($pass, $fila[1]))){
    $_SESSION['errorLogin'] = "<p>Contraseña incorrecta</p>";
  // Sino, el login es válido
  } else {
    $_SESSION['errorLogin'] = null;
    $_SESSION['isLoged'] = true;
    setLogedUserObject($con, $nickParsed);
  }
  $con->close();
}


function setLogedUserObject($con, $nickParsed){ // Función para crear el objeto de usuario
  if(!isset($_SESSION['logedUser'])){
    $resu = $con->query(
      "SELECT `nick`, `email`, `nombre`, `avatar`, `bio`, `fechaRegistro`, `tipo`
      FROM `usuarios`
      WHERE `nick` = '$nickParsed' OR email = '$nickParsed'"
    );
    if($fila = $resu->fetch_row()){
      $actual = new Usuario();
      $actual->setNick(htmlentities($fila[0]));
      $actual->setEmail($fila[1]);
      $actual->setNombre(htmlentities($fila[2]));
      $actual->setAvatar($fila[3]);
      $actual->setBio(htmlentities($fila[4]));
      $actual->setFechaReg($fila[5]);
      $actual->setTipo($fila[6]);
      $_SESSION['logedUser'] = $actual;
    }
  }
}
?>
