<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Inicio");

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

$resu = $con->query("SELECT * FROM `videos` WHERE public = TRUE
  AND estado = 'ready' ORDER BY fechaSubida DESC LIMIT 10");
if(!$resu || $resu->num_rows == 0){ ?>
  <div class="text-center alertVideoWrapper">
  <?php if(!$resu) { ?>
    <div class="alert alert-danger" style="display: inline-block">
      <h3>Error en la base de datos</h3>
    </div>
  <?php } else if($resu->num_rows == 0){ ?>
    <div class="alert alert-warning" style="display: inline-block">
      <h3>No hay vídeos públicos en la plataforma 😕</h3>
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
<h2 class="text-center">Últimos vídeos subidos</h2>
<div class="container homeList">
  <?php
  while ($video = $resu->fetch_assoc()){ ?>
    <div class="row homeItem">
      <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 text-center">
        <div class="videoThumbs"
        style=<?php echo "'background-image:url("
          ."\"/videos/thumbs/".$video["idVideo"].".jpg\")'"
        ?>>
        </div>
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