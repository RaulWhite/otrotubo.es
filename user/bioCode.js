$(document).ready(function(){
  checkBioHeight();

  // Al desplegar la bio
  $('.readMoreBio').click(function(){
    $(".bio p").toggleClass("collapsed");
    $(this).text(function(i, text){
        return text === "Leer más" ? "Leer menos" : "Leer más";
    })
  })
});

$(window).resize(function(){checkBioHeight()});

// Función para comprobar si es necesario mostrar el botón de "Leer más"
function checkBioHeight(){
  var bio = $(".bio p.collapsed");
  if(bio.length > 0){
    if (bio[0].offsetHeight < bio[0].scrollHeight)
      $(".readMoreBio").show();
    else
      $(".readMoreBio").hide();
  }
}