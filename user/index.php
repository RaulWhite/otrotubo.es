<?php
// Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 

if(isset($_GET["user"])){
  $userNick = htmlentities($_GET["user"]);
} else { ?>
  <script>window.location.replace("/");</script>
  <?php die();
}

// Crear conexión con la BD
require_once($_SERVER['DOCUMENT_ROOT']."/mysqlicon.php");
$con = dbCon();
$resu = $con->query("SELECT u.nick, u.email, u.nombre, u.avatar, u.bio, u.fechaRegistro "
  ."FROM usuarios u WHERE nick = '".$con->real_escape_string($userNick)."'");

// Si la query no ha devuelto nada (no hay usuario con ese nick)
if(!($fila = $resu->fetch_row())){
  getHeader("El usuario no existe"); ?>
  <div class="text-center">
    <div class="alert alert-danger text-center" style="display:inline-block">
      <h2>El usuario indicado no existe</h2>
    </div>
  </div>
  <?php exit();
} else {
  $userReq = new Usuario();
  $userReq->setNick($fila[0]);
  $userReq->setEmail($fila[1]);
  $userReq->setNombre($fila[2]);
  $userReq->setAvatar($fila[3]);
  $userReq->setBio($fila[4]);
  $userReq->setFechaReg($fila[5]);
}

// Pedir cabecera con título de página como nombre del usuario (o nick)
getHeader(is_null($userReq->getNombre())?$userReq->getNick():$userReq->getNombre());

// Si se ha pedido modificar el perfil
if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]
&& $_SESSION["logedUser"]->getNick() == $userReq->getNick()
&& isset($_POST["editConfirm"])){
  $editProfileResu = $con->query("UPDATE usuarios SET "
    ."nombre = ".((isset($_POST["newName"])&&($_POST["newName"]!=""))
      ?("'".$con->real_escape_string($_POST["newName"])."'"):"NULL").", "
    ."bio = ".((isset($_POST["newBio"])&&($_POST["newBio"]!=""))
      ?("'".$con->real_escape_string($_POST["newBio"])."'"):"NULL").", "
    ."avatar = ".((isset($_POST["avatarBase64"])&&($_POST["avatarBase64"]!=""))
      ?("'".$con->real_escape_string(base64_decode($_POST["avatarBase64"]))
      ."'"):"NULL")." "
    ."WHERE nick = '".$con->real_escape_string($_SESSION["logedUser"]->getNick())."'");

  // Si todo ha salido bien, recargar la información de la sesión de usuario
  if($editProfileResu){
    $nickParsed = $con->real_escape_string($_SESSION["logedUser"]->getNick());
    $loginResu = $con->query(
      "SELECT `nick`, `email`, `nombre`, `avatar`, `bio`, `fechaRegistro`, `tipo`
      FROM `usuarios`
      WHERE `nick` = '$nickParsed' OR email = '$nickParsed'"
    );
    if($fila = $loginResu->fetch_row()){
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

  // Borrar variable y recargar la página para evitar reenvío de formulario
  unset($_POST["editConfirm"]); ?>
  <script>
    var url = window.location.href;
    window.location.replace(url);
  </script>
<?php }

// Nick de usuario logueado
if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]){
  $logedUserNick = $_SESSION["logedUser"]->getNick();
} else {
  $logedUserNick = null;
}

// Query de vídeos del usuario
$videosResu = $con->query("SELECT * FROM videos WHERE usuarios_nick = '"
  .$con->real_escape_string($userReq->getNick())."' "
  .(($userReq->getNick())===($logedUserNick)?"":"AND estado = 'ready' AND public = true ")
  ."ORDER BY fechaSubida DESC");

// Avatar del usuario
if(is_null($userReq->getAvatar())){
  $emailMD5 = md5($userReq->getEmail());
  $avatar = "https://gravatar.com/avatar/$emailMD5?d=retro&s=500";
} else {
  $blob = $userReq->getAvatar();
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

// Variable de paginación
if(isset($_GET["pag"]) && is_numeric($_GET["pag"])){
  $pagina = floor($_GET["pag"]);
  if($pagina <= 0)
    $pagina = 1;
} else
  $pagina = 1;

// Cargar código JS para las miniaturas si hay vídeos que mostrar en la lista
if($videosResu->num_rows > 0){ ?>
  <script src="/thumbsCode.js"></script>
<?php } ?>
<script src="/user/bioCode.js"></script>

<div class="container userPage">
  <div class="row">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 leftUser">
      <img class="userAvatar img-responsive" src=<?php echo "'$avatar'" ?>>
      <h3><?php
        echo htmlentities((is_null($userReq->getNombre()))
          ?($userReq->getNick()):($userReq->getNombre()))
      ?></h3>
      <?php
      if(!is_null($userReq->getNombre())){ ?>
        <h4><?php echo $userReq->getNick() ?></h4>
      <?php } ?>
      <h6>Se unió el <?php echo date('d/m/Y',strtotime($userReq->getFechaReg())) ?></h6>
      <?php if(!is_null($userReq->getBio())){ ?>
        <div class="bio">
          <p class="collapsed text-justify">
            <?php echo htmlentities($userReq->getBio()) ?>
          </p>
          <button class="readMoreBio btn btn-default btn-sm btn-info btn-block"
          style="display:none">Leer más</button>
        </div>
      <?php }
      if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]
      && $userReq->getNick() == $_SESSION["logedUser"]->getNick()){ ?>
        <button id="editProfileButton" class="btn btn-default btn-block btn-danger"
        data-toggle="modal" data-target="#editProfileModal">Editar perfil</button>
      <?php } ?>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 rightUser">
      <div clas="container-fluid videoList">
        <h2 class="text-center">Vídeos de <?php echo htmlentities(
          (is_null($userReq->getNombre()))?($userReq->getNick()):($userReq->getNombre())
        ) ?></h2>
        <?php // Si la query de vídeo no ha devuelto resultados
        if($videosResu->num_rows <= 0){ ?>
          <div class="alert alert-warning text-center">
          <?php // Si es el usuario logueado
          if(($userReq->getNick())===($logedUserNick)){ ?>
            <h3>Todavía no has subido vídeos</h3>
          <?php } else { ?>
            <h3>Este usuario todavía no ha publicado vídeos</h3>
          <?php } ?>
          </div>
        <?php } else if ($videosResu->num_rows > 0){
          // Se empieza por -1 para que al primer pase sea 0
          $i = -1;
          // Si la página solicitada es mayor que el número de páginas posibles,
          // no entra en el bucle
          if(!((($pagina-1)*5)+1 > $videosResu->num_rows)){
            while ($video = $videosResu->fetch_assoc()){
              // Contar página
              $i++;
              // Si la página solicitada no contiene todavía este listado, continue
              if($i < (($pagina-1)*5))
                continue;
              // Si la página ya tiene los listados solicitados, break
              else if($i >= ($pagina*5))
                break;
              ?>
              <div class="row videoListItem">
                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
                  <a href=<?php echo "'/ver?video=".$video["idVideo"]."'" ?>>
                    <div class="videoThumbs"
                      <?php if(is_file($_SERVER["DOCUMENT_ROOT"]
                        ."/videos/thumbs/".$video["idVideo"].".jpg")){
                        echo "style='background-image:url("
                          ."\"/videos/thumbs/".$video["idVideo"].".jpg\")'";
                      } ?>
                    ></div>
                  </a>
                </div>
                <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
                  <a href=<?php echo "'/ver?video=".$video["idVideo"]."'" ?>>
                    <h3 class="text-justify">
                      <?php echo htmlentities($video["titulo"]) ?>
                      <?php if($video["estado"] == "ready" && $video["public"] == 0){ ?>
                        <small>Vídeo oculto</small>
                      <?php } else if($video["estado"] == "error") { ?>
                        <small style="color: #a94442">Error de codificiación</small>
                      <?php } else if($video["estado"] == "queued") { ?>
                        <small style="color: #8a6d3b">En cola de proceso</small>
                      <?php } else if($video["estado"] == "encoding") { ?>
                        <small style="color: #8a6d3b">Procesando</small>
                      <?php } else if($video["estado"] == "deleted") { ?>
                        <small style="color: #a94442">Eliminado</small>
                      <?php } ?>
                    </h3>
                  </a>
                  <h5><?php echo htmlentities($video["usuarios_nick"]) ?></h5>
                  <p class="text-justify">
                    <?php echo htmlentities($video["descripcion"]) ?>
                  </p>
                </div>
              </div>
            <?php }
          }
        }
        // Mostrar módulo de paginación si hay para más de una página
        if($videosResu->num_rows > 5){ ?>
          <div class="row paginacion">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
              <?php
              // Mostrar botón de página anterior si no se está en la primera página
              if(isset($pagina) && $pagina > 1){
                echo "<a href='/u/".$userReq->getNick()."/".($pagina-1)."'>
                    <button class='btn btn-default btn-danger'>&lt;- Anterior</button>
                  </a>";
              }
              ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
              <?php
              // Mostrar botón página siguiente si no se está en la última página posible
              if(isset($pagina) && $pagina < (($videosResu->num_rows)/5)){
                echo "<a href='/u/".$userReq->getNick()."/".($pagina+1)."'>
                    <button class='btn btn-default btn-danger'>Siguiente -&gt;</button>
                  </a>";
              }
              ?>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php
// Modal de edición de perfil
if(isset($_SESSION["isLoged"]) && $_SESSION["isLoged"]
&& $userReq->getNick() == $_SESSION["logedUser"]->getNick()){ ?>
  <div id="editProfileModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar perfil</h4>
        </div>
        <form id="editProfile" method="POST" class="form">
          <div class="modal-body">
            <div class="form-group">
              <label>Nombre: </label>
              <input type="text" id="newName" name="newName" class="form-control"
              value=<?php echo "'".$_SESSION["logedUser"]->getNombre()."'" ?>>
            </div>
            <div class="form-group">
              <label>Descripción: </label>
              <textarea type="text" id="newBio" name="newBio" rows="5" style="resize: vertical"
              class="form-control"><?php echo $_SESSION["logedUser"]->getBio() ?></textarea>
            </div>
            <div class="imgBlock">
              <div class="form-group">
                <label for="avatar">
                  Avatar: <span class="text-danger">(Máximo 2MB)</span>
                </label>
                <div class="input-group">
                  <label class="input-group-btn">
                    <span class="btn btn-primary">
                      Seleccionar
                      <input type="file" accept="image/*"
                      id="avatar" name="avatar" style="display:none">
                    </span>
                  </label>
                  <input type="text" class="form-control"
                  id="tmpFileName" name="tmpFileName"
                  value="Ningún archivo seleccionado" disabled>
                </div>
                <div class="alert" id="alertIMG" style="display:none"></div>
                <button id="useGravatar"
                class="btn btn-default btn-info" style="display:none">
                  Usar avatar de <strong>Gravatar</strong>
                </button>
                <div class="imgProcessing" style="display: none">
                  <i class="fa fa-refresh fa-5x imgProcessing"></i>
                </div>
                <img class="text-center tmpAvatar img-responsive">
                <script>
                  var usingGravatar = 
                    <?php echo is_null($_SESSION["logedUser"]->getAvatar())?"true":"false" ?>;
                  var emailMD5 =
                    <?php echo "'".md5($_SESSION["logedUser"]->getEmail())."'" ?>
                </script>
                <script src="/user/editAvatarCode.js"></script>
              </div>
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
<?php $con->close() ?>

  </body>
</html>