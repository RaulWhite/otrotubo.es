<?php class Usuario{
  private $nick;
  private $email;
  private $nombre;
  private $avatar;
  private $bio;
  private $fechaReg;
  private $tipo;

  function getNick(){ return $this->nick; }
  function setNick($nick){ $this->nick = $nick; }

  function getEmail(){ return $this->email; }
  function setEmail($email){ $this->email = $email; }

  function getNombre(){ return $this->nombre; }
  function setNombre($nombre){ $this->nombre = $nombre; }
  
  function getAvatar(){ return $this->avatar; }
  function setAvatar($avatar){ $this->avatar = $avatar; }

  function getBio(){ return $this->bio; }
  function setBio($bio){ $this->bio = $bio; }

  function getFechaReg(){ return $this->fechaReg; }
  function setFechaReg($fechaReg){ $this->fechaReg = $fechaReg; }

  function getTipo(){ return $this->tipo; }
  function setTipo($tipo){ $this->tipo = $tipo; }

  function getUser(){
    $usuario = array(
      "nick" => $this->nick,
      "email" => $this->email,
      "nombre" => $this->nombre,
      "avatar" => $this->avatar,
      "bio" => $this->bio,
      "fechaReg" => $this->fechaReg,
      "tipo" => $this->tipo
    );

    return json_encode($usuario);
  }
} ?>