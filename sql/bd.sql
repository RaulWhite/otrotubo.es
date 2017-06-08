CREATE DATABASE IF NOT EXISTS `otrotubo`;

USE `otrotubo`;

CREATE TABLE IF NOT EXISTS `otrotubo`.`usuarios` (
  `nick` VARCHAR(30) NOT NULL,
  `pass` VARCHAR(100) NOT NULL,
  `email` VARCHAR(80) NOT NULL,
  `nombre` VARCHAR(80) NULL,
  `avatar` MEDIUMBLOB NULL,
  `bio` TEXT NULL,
  `fechaRegistro` DATE NOT NULL,
  PRIMARY KEY (`nick`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `otrotubo`.`videos` (
  `idVideo` VARCHAR(8) NOT NULL,
  `titulo` VARCHAR(100) NULL,
  `descripcion` TEXT NULL,
  `estado` ENUM('queued','encoding','ready','error','deleted') NULL,
  `isHD` BOOLEAN NULL,
  `public` BOOLEAN NOT NULL DEFAULT TRUE,
  `fechaSubida` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuarios_nick` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`idVideo`, `usuarios_nick`),
  INDEX `fk_videos_usuarios_idx` (`usuarios_nick` ASC),
  CONSTRAINT `fk_videos_usuarios`
    FOREIGN KEY (`usuarios_nick`)
    REFERENCES `otrotubo`.`usuarios` (`nick`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `otrotubo`.`comentarios` (
  `idComentario` INT NOT NULL,
  `texto` TEXT NOT NULL,
  `fechaComentario` DATETIME NOT NULL,
  `usuarios_nick` VARCHAR(30) NOT NULL,
  `videos_idVideo` VARCHAR(8) NOT NULL,
  PRIMARY KEY (`idComentario`),
  INDEX `fk_comentarios_usuarios1_idx` (`usuarios_nick` ASC),
  INDEX `fk_comentarios_videos1_idx` (`videos_idVideo` ASC),
  CONSTRAINT `fk_comentarios_usuarios1`
    FOREIGN KEY (`usuarios_nick`)
    REFERENCES `otrotubo`.`usuarios` (`nick`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentarios_videos1`
    FOREIGN KEY (`videos_idVideo`)
    REFERENCES `otrotubo`.`videos` (`idVideo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `otrotubo`.`likes` (
  `usuarios_nick` VARCHAR(30) NOT NULL,
  `videos_idVideo` VARCHAR(8) NOT NULL,
  `gusto` BOOLEAN NOT NULL,
  PRIMARY KEY (`usuarios_nick`, `videos_idVideo`),
  INDEX `fk_usuarios_has_videos_videos1_idx` (`videos_idVideo` ASC),
  INDEX `fk_usuarios_has_videos_usuarios1_idx` (`usuarios_nick` ASC),
  CONSTRAINT `fk_usuarios_has_videos_usuarios1`
    FOREIGN KEY (`usuarios_nick`)
    REFERENCES `otrotubo`.`usuarios` (`nick`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_has_videos_videos1`
    FOREIGN KEY (`videos_idVideo`)
    REFERENCES `otrotubo`.`videos` (`idVideo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;