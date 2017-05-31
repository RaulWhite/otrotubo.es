<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Registro");

// Si ya está logueado, volver al inicio. Redirección por JS
if(isset($_SESSION['isLoged']) && $_SESSION['isLoged']){ ?>
  <script>window.location.replace("/");</script>
  <noscript>
    Usted ya está registrado y logueado.
    <a href="/">Volver al inicio</a>
  </noscript>
  <?php die();
} 

// Si hay una petición POST del reCaptcha, cargar la clave secreta
if(isset($_POST["g-recaptcha-response"])){
  $gReSecret =
    parse_ini_file(
      dirname($_SERVER['DOCUMENT_ROOT'])."/g-recaptcha-secret.ini")['secret'];
  require_once($_SERVER['DOCUMENT_ROOT'].'/lib/recaptcha/src/autoload.php');
  $recaptcha = new \ReCaptcha\ReCaptcha($gReSecret);
  $gReResp =
    $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

  // Respuesta de comprobación del Captcha
  $passedCaptcha = $gReResp->isSuccess();
}

// Si se ha enviado el formulario para registrarse, hacer el registro
if(isset($_POST["registrarse"]) && $passedCaptcha){
  require_once($_SERVER['DOCUMENT_ROOT']."/login/claseUsuario.php");

  $tmpUser = new Usuario();
  $tmpUser->setNick(trim($_POST["nick"]));
  $tmpUser->setNombre(trim($_POST["name"]));
  $tmpUser->setEmail(trim($_POST["email"]));
  $tmpUser->setBio((trim($_POST["bio"]) != "")?trim($_POST["bio"]):null); 
  $tmpUser->setFechaReg(date("Y-m-d"));
  $tmpUser->setTipo("registered");

  if(isset($_POST["avatarBase64"])
  && isset($_POST["usingGravatar"])
  && $_POST["usingGravatar"] == "false"){
    $tmpUser->setAvatar(base64_decode($_POST["avatarBase64"]));
  }

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
  $con->query("INSERT INTO `usuarios`"
    ."(`nick`, `pass`, `email`, `nombre`, `avatar`, `bio`, `fechaRegistro`, `tipo`)"
    ."VALUES('"
    .$con->real_escape_string($tmpUser->getNick())."', '"
    .$con->real_escape_string(password_hash($_POST['pass'], PASSWORD_DEFAULT))."', '"
    .$con->real_escape_string($tmpUser->getEmail())."', "
    .($tmpUser->getNombre()!=""?("'".$con->real_escape_string($tmpUser->getNombre())."'"):"NULL").", "
    .($tmpUser->getAvatar()!=""?("'".$con->real_escape_string($tmpUser->getAvatar())."'"):"NULL").", "
    .($tmpUser->getBio()!=""?("'".$con->real_escape_string($tmpUser->getBio())."'"):"NULL").", '"
    .$con->real_escape_string($tmpUser->getFechaReg())."', '"
    .$con->real_escape_string($tmpUser->getTipo())."')");
  $con->close();
}

printForm();

// Imprimir formulario de registro, con librerías JS
function printForm(){
  require_once("registerForm.html");
}
?>