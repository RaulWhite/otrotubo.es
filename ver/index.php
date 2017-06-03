<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php");

$idVideo = $_GET["video"];

if (!isset($idVideo)){ ?>
  <script>window.location.replace("/");</script>
  <?php die();
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

$resu = $con->query("SELECT * FROM videos WHERE idVideo = '"
  .$con->real_escape_string($idVideo)."'");

if(!$resu)
  videoNotAvailable("Error en la base de datos", true);

if($resu->num_rows == 0)
  videoNotAvailable("No se ha encontrado el vídeo", true);

$infoVideo = $resu->fetch_assoc();

switch ($infoVideo["estado"]) {
  case "queued":
    videoNotAvailable("El vídeo solicitado está en cola de proceso");
    break;
  case "encoding":
    videoNotAvailable("El vídeo solicitado se está procesando");
    break;
  case "error":
    videoNotAvailable("El vídeo solicitado ha tenido un error en el proceso de conversión", true);
    break;
  case "deleted":
    videoNotAvailable("El vídeo solicitado se ha eliminado", true);
    break;
  case "ready":
  default:
    continue;
    break;
}

getHeader($infoVideo["titulo"]);
?>

<script src="/ver/playerCode.js"></script>

<div class="container video-container">
  <div class="row">
    <div class="col-lg-7 col-md-9 col-sm-12 col-xs-12 player-col">
      <div id="playerWrapper" class="normalVideo text-center">
        <video id="player" controls>
          <source label="360p"
            src=<?php echo "\"/videos/360/".$idVideo.".mp4\"" ?>
            type="video/mp4">
          Su navegador no soporta vídeo HTML5.
        </video>
      </div>
      <?php if($infoVideo["isHD"]){ ?>
        <button id="qSelector" class="btn btn-danger">Cambiar a 720p</button>
      <?php } ?>
      <button id="wSelector" class="btn btn-danger">Modo cine</button>
    </div>
    <div class="col-lg-5 col-md-3 col-sm-12 col-xs-12 info-col info-narrow">
      <h3><?php echo $infoVideo["titulo"] ?></h3>
      <p class="collapsed text-justify">
        <?php echo $infoVideo["descripcion"] ?>
      </p>
      <button class="readMoreDesc readMoreNarrow btn btn-default btn-sm btn-info btn-block"
      style="display:none">Leer más</button>
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-xs-12 info-col info-wide" style="display:none">
      <h3><?php echo $infoVideo["titulo"] ?></h3>
      <p class="collapsed text-justify">
        <?php echo $infoVideo["descripcion"] ?>
      </p>
      <button class="readMoreDesc readMoreWide btn btn-default btn-sm btn-info btn-block"
      style="display:none">Leer más</button>
    </div>
  </div>
</div>

  </body>
</html>

<?php 
$con->close();

// Si no se puede mostrar el vídeo, se cierra la conexión y se para la ejecución
function videoNotAvailable($message, $severe = false){
  getHeader("Vídeo no disponible");
  $alertClasses = "alert alertVideo alert-".($severe?"danger":"warning");
  ?>
  <div class="text-center alertVideoWrapper">
    <div class=<?php echo "'$alertClasses'" ?> style="display: inline-block">
      <h3><?php echo $message ?></h3>
    </div>
  </div>
  <?php
  echo "</body></html>";
  $con->close();
  exit();
}
?>