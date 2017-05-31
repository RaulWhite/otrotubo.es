<?php // Incluir librerías y header
require_once($_SERVER['DOCUMENT_ROOT']."/header.php");
getHeader("Vídeo");

$idVideo = $_GET["video"];

if (!isset($idVideo)){
  header('Location: /');
}

?>
<script>
  $(document).ready(function(){
    $('#player')[0].play();
    $('#qSelector').click(function(){
      tiempo = $('#player')[0].currentTime;
      pausado = $('#player').get(0).paused;
      $('source', '#player').eq(1).prependTo('#player');
      $('#player')[0].load();
      $('#player')[0].currentTime = tiempo;
      if(!pausado)
        $('#player')[0].play();
      $(this).text(function(i, text){
        return text === "Cambiar a 720p" ? "Cambiar a 360p" : "Cambiar a 720p";
      })
      $(this).toggleClass("btn-danger btn-primary")
    })
    $('#wSelector').click(function(){
      $('#playerWrapper').toggleClass("wideVideo normalVideo");
      $(this).text(function(i, text){
          return text === "Modo cine" ? "Modo normal" : "Modo cine";
      })
      $(this).toggleClass("btn-danger btn-primary")
    })
  })
</script>
<button id="qSelector" class="btn btn-danger">Cambiar a 720p</button>
<button id="wSelector" class="btn btn-danger">Modo cine</button>
<br>
<div id="playerWrapper" class="normalVideo">
  <video id="player" controls>
    <source label="360p"
      src=<?php echo "\"/videos/360/".$idVideo.".mp4\"" ?>
      type="video/mp4">
    <source label="720p"
      src=<?php echo "\"/videos/720/".$idVideo.".mp4\"" ?>
      type="video/mp4">
    Su navegador no soporta vídeo HTML5.
  </video>
</div>
  </body>
</html>