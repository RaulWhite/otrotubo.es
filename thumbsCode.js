// Declaración global de timeout
var scrollTOut;

$(document).ready(function(){
  $(".videoThumbs").mouseover(function(){
    scrollTOut = setTimeout(scrollThumb, 750, 0, $(this));
  });
  $(".videoThumbs").mouseout(function(){
    clearTimeout(scrollTOut);
    $(this).css("background-position-x", "675px");
  });
});

function scrollThumb(i, element) {
  if(i == 0)
    i = 12;
  element.css("background-position-x", (225*i)+"px");
  scrollTOut = setTimeout(scrollThumb, 750, i - 1, element);
}