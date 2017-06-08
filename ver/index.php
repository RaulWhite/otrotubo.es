<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php");

$idVideo = $_GET["video"];

if (!isset($idVideo)){ ?>
  <script>window.location.replace("/");</script>
  <?php die();
}

// Crear conexión con la BD
require_once($_SERVER['DOCUMENT_ROOT']."/mysqlicon.php");
$con = dbCon();

$resu = $con->query("SELECT v.*, u.avatar, u.email FROM videos v JOIN usuarios u "
  ."ON (v.usuarios_nick = u.nick) WHERE idVideo = '"
  .$con->real_escape_string($idVideo)."'");

if(!$resu)
  videoNotAvailable("Error en la base de datos", true);

if($resu->num_rows == 0)
  videoNotAvailable("No se ha encontrado el vídeo", true);

$infoVideo = $resu->fetch_assoc();

// Si se ha pedido editar vídeo y el usuario logueado es el propietario
if(isset($_POST["editConfirm"])
&& $_SESSION["logedUser"]->getNick() == $infoVideo["usuarios_nick"]){
  if(!isset($_POST["newTitle"]) || trim($_POST["newTitle"]) == ""){
    videoNotAvailable("Debe indicar un título al editar el vídeo");
  }
  $newTitle = $_POST["newTitle"];
  $newDesc = (isset($_POST["newDesc"]) && $_POST["newDesc"] != ""
    ?$_POST["newDesc"]:NULL);
  $newPublic = isset($_POST["newPublic"])?"TRUE":"FALSE";

  $updateResu = $con->query("UPDATE `videos` SET "
    ."titulo = '".$con->real_escape_String($newTitle)."', "
    ."descripcion = '".$con->real_escape_String($newDesc)."', "
    ."public = ".$con->real_escape_String($newPublic)." "
    ."WHERE idVideo = '".$con->real_escape_string($infoVideo["idVideo"])."'");

  // Si la modificación ha salido bien, se recarga para evitar reenvío de formulario
  if($updateResu){
    header("Refresh:0");
  // Si no, se indica error en la base de datos
  } else {
    videoNotAvailable("Error en la base de datos", true);
  }
}

// Si se ha pedido borrar vídeo y el usuario logueado es el propietario
if(isset($_POST["eraseConfirm"])
&& $_SESSION["logedUser"]->getNick() == $infoVideo["usuarios_nick"]){
  // Se indica como borrado en la base de datos
  $eraseResu = $con->query("UPDATE `videos` SET estado = 'deleted' "
    ."WHERE idVideo = '".$infoVideo["idVideo"]."'");
  
  // Si la modificación de la BD ha salido bien, se elimina el vídeo
  if($eraseResu){
    $videosFolder = $_SERVER["DOCUMENT_ROOT"]."/videos";
    // Borrar vídeo 360p, si existe
    if(is_file($videosFolder."/360/".$infoVideo["idVideo"].".mp4"))
      unlink($videosFolder."/360/".$infoVideo["idVideo"].".mp4");
    // Borrar vídeo 720p, si existe
    if(is_file($videosFolder."/720/".$infoVideo["idVideo"].".mp4"))
      unlink($videosFolder."/720/".$infoVideo["idVideo"].".mp4");
    // Borrar archivo miniaturas, si existe
    if(is_file($videosFolder."/thumbs/".$infoVideo["idVideo"].".jpg"))
      unlink($videosFolder."/thumbs/".$infoVideo["idVideo"].".jpg");
    // Indicar actual como borrado para imprimir mensaje de vídeo borrado
    $infoVideo["estado"] = "deleted";
  // Si no, se indica error en BD
  } else {
    videoNotAvailable("Error en la base de datos", true);
  }
}

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

getHeader(htmlentities($infoVideo["titulo"]));

if(is_null($infoVideo["avatar"])){
  $emailMD5 = md5($infoVideo["email"]);
  $avatar = "https://gravatar.com/avatar/$emailMD5?d=retro";
} else {
  $blob = $infoVideo["avatar"];
  $JPEG = "\xFF\xD8\xFF";
  $GIF  = "GIF";
  $PNG  = "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a";
  $BMP  = "BM";
  if(strpos($blob, $JPEG) !== false)
    $dataImage = "data:image/jpeg;base64,";
  else if(strpos($blob, $GIF) !== false)
    $dataImage = "data:image/gif;base64,";
  else if(strpos($blob, $PNG) !== false)
    $dataImage = "data:image/png;base64,";
  else if(strpos($blob, $BMP) !== false)
    $dataImage = "data:image/bmp;base64,";

  if (isset($dataImage))
    $avatar = $dataImage.base64_encode($blob);
}
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
      <div class="container-fluid videoPlayerExtra">
        <div class="row">
          <div class="col-sm-6 col-xs-4">
            <?php if($infoVideo["isHD"]){ ?>
              <button id="qSelector" class="btn btn-danger">Cambiar a 720p</button>
            <?php } ?>
            <button id="wSelector" class="btn btn-danger">Modo cine</button>
          </div>
          <?php if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]
          && $infoVideo["usuarios_nick"] == $_SESSION["logedUser"]->getNick()){ ?>
            <div class="col-sm-6 col-xs-8 text-right">
              <div class="buttonsRightVideo">
                <button id="editInfo" class="btn btn-warning"
                data-toggle="modal" data-target="#editModal">
                  Editar info del vídeo
                </button>
                <button id="eraseVideo" class="btn btn-danger"
                data-toggle="modal" data-target="#eraseModal">Borrar vídeo</button>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-lg-5 col-md-3 col-sm-12 col-xs-12 info-col info-narrow">
      <h3>
        <?php echo htmlentities($infoVideo["titulo"]) ?>
        <?php if($infoVideo["public"] == 0){ ?>
          <br>
          <small>Vídeo oculto</small>
        <?php } ?>
      </h3>
      <h5>
        <a class="userLink" href=<?php echo "'/u/".$infoVideo["usuarios_nick"]."'" ?>>
          <img class="userAvatar" src=<?php echo "'$avatar'" ?>>
        </a>
        <div style="display:inline-block; vertical-align:bottom">
          <a class="userLink" href=<?php echo "'/u/".$infoVideo["usuarios_nick"]."'" ?>>
            <?php echo htmlentities($infoVideo["usuarios_nick"]) ?>
          </a>
          <br><br>Subido el 
          <?php echo htmlentities(
            date('d/m/Y - H:i',strtotime($infoVideo["fechaSubida"]))
          )?>
        </div>
      </h5>
      <p class="collapsed text-justify">
        <?php echo htmlentities($infoVideo["descripcion"]) ?>
      </p>
      <button class="readMoreDesc readMoreNarrow btn btn-default btn-sm btn-info btn-block"
      style="display:none">Leer más</button>
    </div>
  </div>
</div>

<div class="video-container-2 container">
  <div class="row">
    <div class="col-xs-12 info-col info-wide" style="display:none">
      <h3>
        <?php echo htmlentities($infoVideo["titulo"]) ?>
        <?php if($infoVideo["public"] == 0){ ?>
          <br>
          <small>Vídeo oculto</small>
        <?php } ?>
      </h3>
      <h5>
        <a class="userLink" href=<?php echo "'/u/".$infoVideo["usuarios_nick"]."'" ?>>
          <img class="userAvatar" src=<?php echo "'$avatar'" ?>>
        </a>
        <div style="display:inline-block; vertical-align:bottom">
          <a class="userLink" href=<?php echo "'/u/".$infoVideo["usuarios_nick"]."'" ?>>
            <?php echo htmlentities($infoVideo["usuarios_nick"]) ?>
          </a>
          <br><br>Subido el 
          <?php echo date('d/m/Y - H:i',strtotime($infoVideo["fechaSubida"])) ?>
        </div>
      </h5>
      <p class="collapsed text-justify">
        <?php echo htmlentities($infoVideo["descripcion"]) ?>
      </p>
      <button class="readMoreDesc readMoreWide btn btn-default btn-sm btn-info btn-block"
      style="display:none">Leer más</button>
    </div>
  </div>
</div>

<?php // Modal de edición de datos del vídeo
if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]
&& $infoVideo["usuarios_nick"] == $_SESSION["logedUser"]->getNick()){ ?>
  <!-- CSS para switch de vídeo público -->
  <link href="/lib/toggle-switch.css" rel="stylesheet" type="text/css">

  <div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar información del vídeo</h4>
        </div>
        <form id="editVideo" method="POST" class="form">
          <div class="modal-body">
            <div class="form-group">
              <label for="newTitle">Título del vídeo: *</label>
              <input type="text" name="newTitle" id="newTitle" class="form-control"
              value=<?php echo "'".$infoVideo["titulo"]."'" ?> required>
            </div>
            <div class="form-group">
              <div class="container-fluid publicToggle">
                <div class="row">
                  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label for="newPublic">Publicar vídeo </label>
                  </div>
                  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <label class="switch-light" onclick="">
                      <input type="checkbox" id="newPublic" name="newPublic"
                      <?php echo ($infoVideo["public"]?"checked":"") ?>>
                      <span class="progress active">
                        <span>Oculto</span>
                        <span>Público</span>
                        <a class="progress-bar progress-bar-info"></a>
                      </span>
                    </label>
                  </div>
                </div>
                <p>
                  Si se selecciona "Público", el vídeo subido aparecerá en la lista
                  de últimos vídeos subidos y en búsquedas.
                </p>
              </div>
            </div>
            <div class="form-group">
              <label for="newDesc">Descripción del vídeo: </label>
              <textarea class="form-control" rows="5"
              id="newDesc" name="newDesc" style="resize: vertical"
              placeholder="Descripción del vídeo"><?php echo $infoVideo["descripcion"] ?></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default btn-danger pull-left"
            data-dismiss="modal">Cancelar</button>
            <input type="submit" class="btn btn-default btn-primary pull-right"
            id="editConfirm" name="editConfirm" value="Aceptar">
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<?php // Modal de confirmación de borrar vídeo
if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]
&& $infoVideo["usuarios_nick"] == $_SESSION["logedUser"]->getNick()){ ?>
  <div id="eraseModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Borrar vídeo</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger lead">
            ATENCIÓN: Esta acción no se podrá deshacer
          </div>
          <p>¿Está seguro que quiere borrar el vídeo de la plataforma?</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default btn-danger pull-left"
          data-dismiss="modal">Cancelar</button>
          <form id="eraseVideo" method="POST">
            <input type="submit" class="btn btn-default btn-primary pull-right"
            id="eraseConfirm" name="eraseConfirm" value="Aceptar">
          </form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

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
      <?php if(!$severe){ ?>
        <a href="">Recargar la página</a>
      <?php } ?>
    </div>
  </div>
  <?php
  echo "</body></html>";
  global $con;
  $con->close();
  exit();
}
?>