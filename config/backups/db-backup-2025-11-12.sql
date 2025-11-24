

CREATE TABLE `academia` (
  `id_academia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `siglas` varchar(45) NOT NULL,
  PRIMARY KEY (`id_academia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `aplicacioninstrumento` (
  `id_aplicacion` int(11) NOT NULL AUTO_INCREMENT,
  `puntaje` decimal(5,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `id_instrumento` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_periodo` int(11) DEFAULT NULL,
  `id_evaluador` int(11) DEFAULT NULL COMMENT 'ID del usuario (profesor.id_profesor) que realiza la evaluación',
  `estado` varchar(45) NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (`id_aplicacion`),
  KEY `id_instrumento` (`id_instrumento`),
  KEY `id_profesor` (`id_profesor`),
  KEY `id_periodo` (`id_periodo`),
  KEY `id_evaluador` (`id_evaluador`),
  CONSTRAINT `aplicacioninstrumento_ibfk_1` FOREIGN KEY (`id_instrumento`) REFERENCES `instrumento` (`id_instrumento`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `aplicacioninstrumento_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `aplicacioninstrumento_ibfk_3` FOREIGN KEY (`id_periodo`) REFERENCES `periodo` (`id_periodo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `aplicacioninstrumento_ibfk_4` FOREIGN KEY (`id_evaluador`) REFERENCES `profesor` (`id_profesor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `asignacionmateria` (
  `nombre_materia` int(11) NOT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  KEY `id_materia` (`id_materia`),
  KEY `id_profesor` (`id_profesor`),
  CONSTRAINT `asignacionmateria_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `asignacionmateria_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `instrumento` (
  `id_instrumento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_instrumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `materia` (
  `id_materia` int(11) NOT NULL,
  `nombre_materia` varchar(45) NOT NULL,
  `clave` varchar(45) NOT NULL,
  `creditos` int(11) NOT NULL,
  `id_academia` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_materia`),
  KEY `id_academia` (`id_academia`),
  CONSTRAINT `materia_ibfk_1` FOREIGN KEY (`id_academia`) REFERENCES `academia` (`id_academia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `periodo` (
  `id_periodo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id_periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `postulante` (
  `id_aplicacion` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido_pa` varchar(45) NOT NULL,
  `apellido_ma` varchar(45) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `rol` varchar(45) NOT NULL,
  `grado_academico` varchar(45) NOT NULL,
  PRIMARY KEY (`id_aplicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido_pa` varchar(45) NOT NULL,
  `apellido_ma` varchar(45) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `matricula` varchar(45) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `rol` varchar(45) NOT NULL,
  `grado_academico` varchar(45) NOT NULL,
  PRIMARY KEY (`id_profesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO profesor VALUES("1","Sandra Elizabeth","León","Sosa","F","admin1","$2y$10$C9Sgaxil2bfZit6z13qA2ee8OMCchCaWdAN3pEmeimg0SIKuKcaju","Administrador","Profesora de Tiempo Completo");
INSERT INTO profesor VALUES("2","Alexa","Morales","menez","F","coordinador1","$2y$10$mqoyTkrwyOsMlCAQUDJa3unb8HNRlJjS6bt5T6jOVIvJLxK0zDSe2","Profesor","Profesor");





CREATE TABLE `respuesta` (
  `id_respuesta` int(11) NOT NULL AUTO_INCREMENT,
  `id_aplicacion` int(11) NOT NULL,
  `id_rubro` int(11) NOT NULL,
  `puntaje_obtenido` decimal(5,2) DEFAULT NULL,
  `comentario_adicional` text DEFAULT NULL,
  PRIMARY KEY (`id_respuesta`),
  KEY `id_aplicacion` (`id_aplicacion`),
  KEY `id_rubro` (`id_rubro`),
  CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`id_aplicacion`) REFERENCES `aplicacioninstrumento` (`id_aplicacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `respuesta_ibfk_2` FOREIGN KEY (`id_rubro`) REFERENCES `rubro` (`id_rubro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE `rubro` (
  `id_rubro` int(11) NOT NULL AUTO_INCREMENT,
  `id_instrumento` int(11) NOT NULL,
  `texto_aspecto` text NOT NULL,
  `orden` int(11) NOT NULL,
  PRIMARY KEY (`id_rubro`),
  KEY `id_instrumento` (`id_instrumento`),
  CONSTRAINT `rubro_ibfk_1` FOREIGN KEY (`id_instrumento`) REFERENCES `instrumento` (`id_instrumento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




