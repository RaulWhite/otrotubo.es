// Codificar vídeo
  function encode(resolution){ 
    $.post("encode.php", {
      "tempVideo": tempVideo,
      "resolution": resolution,
      "videoID": videoID});
    setTimeout(function(){checkProgress(resolution)}, 2000);
  }

  // Borrar archivos temporales
  function clean(){ 
    $.post("encode.php", {
      "clean": true,
      "tempVideo": tempVideo,
      "videoID": videoID});
  }

  // Comprobar progreso
  function checkProgress(resolution){ 
    $.post("encodeProgress.php", {"videoID": videoID}, 
      function(data){
      $("#encode"+resolution+"Progress")
        .html("Codificando "+ resolution +"p: " + data.progress + "%");
      // Si ha fallado
      if(data.failed){
        clean();
        $("#encode"+resolution+"Progress").html("");
        $("#videoLink").html("Fallo en la conversión");
        return;
      }
      // Si se ha acabado
      if(data.finished){
        // Si se ha acabado con el 360p, se inicia el proceso para 720p
        // (si el vídeo es HD) y se muestra el enlace
        if(resolution == "360"){ 
          $.post("encode.php", {"checkIfHD": true, "tempVideo": tempVideo},
            function(data){
              if(data.isHD)
                setTimeout(function(){encode("720")}, 2000);
              else
                clean();
            }
          );
          $("#videoLink")
            .html("Procesado<br>"
              + "<a href='/ver?video="+videoID+"'>"
              + "Enlace al vídeo</a>");
        }
        // Si se ha acabado con el 720p
        if(resolution == "720") 
          // Se limpia
          clean(); 
      // Si no se ha acabado (Se comprueba explícitamente 
      // si es false por si el php no devuelve respuesta)
      }else if (data.finished == false){ 
        // Volver a ejecutar esta función para ir actualizanco el porcentaje
        setTimeout(function(){checkProgress(resolution)}, 2000); 
      }
    }, "json");
  };

  // Empezar con el vídeo 360p
  setTimeout(function(){encode("360")}, 200); 