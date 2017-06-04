<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Inicio");

// Crear conexión con la BD
require_once($_SERVER['DOCUMENT_ROOT']."/mysqlicon.php");
$con = dbCon();

// Si se ha pedido búsqueda
if(isset($_GET["buscar"])){
  // Comprobar que no haya espacios vacíos
  $_GET["buscar"] = trim($_GET["buscar"]);
  // Si solo eran espacios, se cancela la búsqueda
  if(empty($_GET["buscar"]))
    unset($_GET["buscar"]);
  $query = "SELECT * FROM `videos` WHERE public = TRUE AND titulo LIKE "
    ."'%".$con->real_escape_string($_GET["buscar"])."%' "
    ."AND estado = 'ready' ORDER BY fechaSubida DESC LIMIT 10";
} else
  $query = "SELECT * FROM `videos` WHERE public = TRUE
  AND estado = 'ready' ORDER BY fechaSubida DESC LIMIT 10";

$resu = $con->query($query);
if(!$resu || $resu->num_rows == 0){ ?>
  <div class="text-center alertVideoWrapper">
  <?php if(!$resu) { ?>
    <div class="alert alert-danger" style="display: inline-block">
      <h3>Error en la base de datos</h3>
    </div>
  <?php } else if($resu->num_rows == 0){ ?>
    <div class="alert alert-warning" style="display: inline-block">
    <?php if(isset($_GET["buscar"])){ ?>
      <h3>No se han encontrado vídeos con la búsqueda</h3>
    <?php } else { ?>
      <h3>No hay vídeos públicos en la plataforma 😕</h3>
    <?php } ?>
    </div>
  </div>
    </body>
  </html>
  <?php }
  $con->close();
  exit();
}

?>
<script src="/thumbsCode.js"></script>
<?php if(isset($_GET["buscar"])){ ?>
  <h2 class="text-center">
    Resultados de búsqueda: "<?php echo htmlentities($_GET["buscar"]) ?>"
  </h2>
<?php } else { ?>
  <h2 class="text-center">Últimos vídeos subidos</h2>
<?php } ?>
<div class="container videoList">
  <?php
  while ($video = $resu->fetch_assoc()){ ?>
    <div class="row videoListItem">
      <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 text-center">
        <a href=<?php echo "'/ver?video=".$video["idVideo"]."'" ?>>
          <div class="videoThumbs"
          style=<?php echo "'background-image:url("
            ."\"/videos/thumbs/".$video["idVideo"].".jpg\")'"
          ?>>
          </div>
        </a>
      </div>
      <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
        <a href=<?php echo "'/ver?video=".$video["idVideo"]."'" ?>>
          <h3 class="text-justify">
            <?php echo htmlentities($video["titulo"]) ?>
          </h3>
          <h5><?php echo htmlentities($video["usuarios_nick"]) ?></h5>
          <p class="text-justify">
            <?php echo htmlentities($video["descripcion"]) ?>
          </p>
        </a>
      </div>
    </div>
  <?php }
  ?>
</div>

  </body>
</html>