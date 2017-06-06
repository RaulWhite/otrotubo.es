<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php"); 
getHeader("Inicio");

// Crear conexión con la BD
require_once($_SERVER['DOCUMENT_ROOT']."/mysqlicon.php");
$con = dbCon();

// Variable de paginación
if(isset($_GET["pag"]) && is_numeric($_GET["pag"])){
  $pagina = floor($_GET["pag"]);
  if($pagina <= 0)
    $pagina = 1;
} else
  $pagina = 1;

// Si se ha pedido búsqueda
if(isset($_GET["buscar"])){
  // Comprobar que no haya espacios vacíos
  $_GET["buscar"] = trim($_GET["buscar"]);
  // Si solo eran espacios, se cancela la búsqueda
  if(empty($_GET["buscar"]))
    unset($_GET["buscar"]);
  $query = "SELECT * FROM `videos` WHERE public = TRUE AND titulo LIKE "
    ."'%".$con->real_escape_string($_GET["buscar"])."%' "
    ."AND estado = 'ready' ORDER BY fechaSubida DESC";
} else
  $query = "SELECT * FROM `videos` WHERE public = TRUE
  AND estado = 'ready' ORDER BY fechaSubida DESC";

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
    <?php } else if(isset($pagina)) { ?>
      <h3>No ha más vídeos que mostrar</h3>
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
  // Se empieza por -1 para que al primer pase sea 0
  $i = -1;
  // Si la página solicitada es mayor que el número de páginas posibles,
  // no entra en el bucle
  if(!((($pagina-1)*5)+1 > $resu->num_rows)){
    while ($video = $resu->fetch_assoc()){
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
  }
  // Mostrar módulo de paginación si hay para más de una página
  if($resu->num_rows > 5){
    if(isset($_GET["buscar"]))
      $getSearch = "buscar=".urlencode($_GET["buscar"])."&";
  ?>
    <div class="row paginacion">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        <?php
        // Mostrar botón de página anterior si no se está en la primera página
        if(isset($pagina) && $pagina > 1){
          echo "<a href='/?".(isset($getSearch)?$getSearch:"")
            ."pag=".($pagina-1)."'>
              <button class='btn btn-default btn-danger'>&lt;- Anterior</button>
            </a>";
        }
        ?>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
        <?php
        // Mostrar botón página siguiente si no se está en la última página posible
        if(isset($pagina) && $pagina < (($resu->num_rows)/5)){
          echo "<a href='/?".(isset($getSearch)?$getSearch:"")
            ."pag=".($pagina+1)."'>
              <button class='btn btn-default btn-danger'>Siguiente -&gt;</button>
            </a>";
        }
        ?>
      </div>
    </div>
  <?php } ?>
</div>

  </body>
</html>