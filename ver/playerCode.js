$(document).ready(function(){
  // Autoreproducir al cargar la página
  $('#player')[0].play();

  // Al cambiar la calidad
  $('#qSelector').click(function(){
    // Obtener tiempo y estado de reproducción
    tiempo = $('#player')[0].currentTime;
    pausado = $('#player').get(0).paused;
    // Archivo actual
    var actualFile = $('source', '#player').attr("src");
    var actualLabel = $('source', '#player').attr("label");
    // Cambiar a la otra calidad respecto la actual
    if(actualLabel == "360p")
      var newFile = actualFile.replace("360", "720");
    else
      var newFile = actualFile.replace("720", "360");
    // Cambiar archivo en reproductor
    $('source', '#player').attr("src", newFile);
    $('source', '#player').attr("label",((actualLabel == "360p")?"720p":"360p"));
    $('#player')[0].load();
    // Volver al tiempo anterior
    $('#player')[0].currentTime = tiempo;
    // Devolver estado reproducción
    if(!pausado)
      $('#player')[0].play();
    // Cambiar botón
    $(this).text(function(i, text){
      return text === "Cambiar a 720p" ? "Cambiar a 360p" : "Cambiar a 720p";
    })
    $(this).toggleClass("btn-danger btn-primary")
  })

  // Al cambiar tamaño del reproductor
  $('#wSelector').click(function(){
    // Cambiar clases para estilo ancho o estrecho
    $('#playerWrapper').toggleClass("wideVideo normalVideo");
    $('.player-col').toggleClass("col-lg-7 col-md-9 col-lg-12 col-md-12");
    $('.video-container').toggleClass("container container-fluid");
    $('.info-narrow').toggle();
    $('.info-wide').toggle();
    $(this).text(function(i, text){
        return text === "Modo cine" ? "Modo normal" : "Modo cine";
    })
    $(this).toggleClass("btn-danger btn-primary")
  })

  // Comprobar si es necesario mostrar botones de "Leer más"
  checkDescHeight();

  // Al despeglar la descripción
  $('.readMoreDesc').click(function(){
    $(".info-col p").toggleClass("collapsed");
    $(this).text(function(i, text){
        return text === "Leer más" ? "Leer menos" : "Leer más";
    })
  })
})

$(window).resize(function(){checkDescHeight()});

// Función para comprobar si es necesario mostrar el botón de "Leer más"
function checkDescHeight(){
  var infoNarrow = $(".info-narrow p.collapsed");
  if(infoNarrow.length > 0){
    if (infoNarrow[0].offsetHeight < infoNarrow[0].scrollHeight)
      $(".readMoreNarrow").show();
    else
      $(".readMoreNarrow").hide();
  }

  var infoWide = $(".info-wide p.collapsed");
  if(infoWide.length > 0){
    if(infoWide[0].offsetHeight < infoWide[0].scrollHeight)
      $(".readMoreWide").show();
    else
      $(".readMoreWide").hide();
  }
}