# otrotubo.es

otrotubo.es es una página de compartición de vídeos (al estilo de YouTube), pero más simple y hecho desde cero por mi cuenta.
Está basado en HTML5, PHP y JS.

Las únicas librerías que de momento he usado son:
  - Para el diseño
    - JQuery
    - Bootstrap
    - Less

  - Para funcionamiento de la página
    - https://github.com/jquery-form/form
      (Usado para mostrar porcentaje del proceso de subida)

### Funcionamiento
Esta aplicación web requiere de un servidor (a poder ser un linux basado en Debian), con el programa ffmpeg instalado para poder realizar la conversión de los vídeos.

Los vídeos cargados a la plataforma se convierten en resolución 360p, y 720p si el vídeo es de alta definición.