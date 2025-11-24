

DROP TABLE IF EXISTS `academia`;


CREATE TABLE `academia` (
  `id_academia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `siglas` varchar(45) NOT NULL,
  PRIMARY KEY (`id_academia`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO academia VALUES("1","Redes","RED_1234");
INSERT INTO academia VALUES("2","Programación","PR-ITI39");
INSERT INTO academia VALUES("3","Proyectos","PR-EST");





DROP TABLE IF EXISTS `aplicacioninstrumento`;


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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO aplicacioninstrumento VALUES("5","0.00","","1","","","1","pendiente");
INSERT INTO aplicacioninstrumento VALUES("6","14.00","","3","","","1","completado");
INSERT INTO aplicacioninstrumento VALUES("7","11.00","","1","","","1","completado");
INSERT INTO aplicacioninstrumento VALUES("8","5.00","","2","","","1","completado");
INSERT INTO aplicacioninstrumento VALUES("9","0.00","","3","","","1","pendiente");
INSERT INTO aplicacioninstrumento VALUES("10","1.00","","2","6","","1","completado");
INSERT INTO aplicacioninstrumento VALUES("11","20.00","","3","5","4","1","completado");
INSERT INTO aplicacioninstrumento VALUES("12","0.00","","1","","4","1","pendiente");





DROP TABLE IF EXISTS `asignacionmateria`;


CREATE TABLE `asignacionmateria` (
  `nombre_materia` int(11) NOT NULL,
  `id_materia` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  KEY `id_materia` (`id_materia`),
  KEY `id_profesor` (`id_profesor`),
  CONSTRAINT `asignacionmateria_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `asignacionmateria_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO asignacionmateria VALUES("1","1","3");
INSERT INTO asignacionmateria VALUES("1","1","1");
INSERT INTO asignacionmateria VALUES("2","2","");
INSERT INTO asignacionmateria VALUES("3","3","6");
INSERT INTO asignacionmateria VALUES("3","3","5");





DROP TABLE IF EXISTS `instrumento`;


CREATE TABLE `instrumento` (
  `id_instrumento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_instrumento`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO instrumento VALUES("1","Instrumento 1","Evaluación docente");
INSERT INTO instrumento VALUES("2","Instrumento 2","evaluacion");
INSERT INTO instrumento VALUES("3","Instrumento 3","...");





DROP TABLE IF EXISTS `materia`;


CREATE TABLE `materia` (
  `id_materia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_materia` varchar(45) NOT NULL,
  `clave` varchar(45) NOT NULL,
  `creditos` int(11) NOT NULL,
  `id_academia` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_materia`),
  KEY `id_academia` (`id_academia`),
  CONSTRAINT `materia_ibfk_1` FOREIGN KEY (`id_academia`) REFERENCES `academia` (`id_academia`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO materia VALUES("1","Introducción a Redes","INR","6","1");
INSERT INTO materia VALUES("2","Programación Web","PRW","6","2");
INSERT INTO materia VALUES("3","Programación Orientada a Objetos","POO","7","2");





DROP TABLE IF EXISTS `periodo`;


CREATE TABLE `periodo` (
  `id_periodo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id_periodo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO periodo VALUES("4","mayo - agosto 2025","2025-05-06","2025-08-15");





DROP TABLE IF EXISTS `postulante`;


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






DROP TABLE IF EXISTS `profesor`;


CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellido_pa` varchar(45) NOT NULL,
  `apellido_ma` varchar(45) NOT NULL,
  `sexo` varchar(45) NOT NULL,
  `matricula` varchar(45) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `rol` varchar(45) NOT NULL,
  `grado_academico` varchar(45) NOT NULL,
  PRIMARY KEY (`id_profesor`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO profesor VALUES("1","Sandra Elizabeth","León","Sosa","F","admin1","$2y$10$C9Sgaxil2bfZit6z13qA2ee8OMCchCaWdAN3pEmeimg0SIKuKcaju","Administrador","Profesora de Tiempo Completo");
INSERT INTO profesor VALUES("3","José Enrique","Zagal","Solano","M","jzagal101","$2y$10$40XYJx5J90zrciQ7je2fHeyq0aEl9iYrCtUrEmod0mPPFToI9vQVi","Profesor","Ingeniero");
INSERT INTO profesor VALUES("5","Roberto Enrique","López","Díaz","M","rdiaz424","$2y$10$S6U6IkaQK3hc8/btHzHsaeNXMM5il0L0KLBGekfKRMFgteTANP9d.","Profesor","Maestría");
INSERT INTO profesor VALUES("6","Deny LIzbeth","Hernández","Rabadán","F","dhernandez621","$2y$10$kC32WUZY766hp5ixvmEGq.F6U1Vix57TPa/KiNNTbzdnSy/DTUi7i","Profesor","PTC");
INSERT INTO profesor VALUES("11","César Iván","a","Velaaquez","M","alum","$2y$10$OP5GnMIiFUAoeTnaMpddm.bn.bsXjv5H7AUusF58HQ5ThdxIpLnUy","Profesor","Maestría");
INSERT INTO profesor VALUES("13","Coordi","z","a","M","coordinador","$2y$10$HEf.cn/6S3Yil3cI2fpITenMczio03OD8xQJvJWQz/XGa5mZIx.q2","Coordinador","Maestría");





DROP TABLE IF EXISTS `respuesta`;


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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO respuesta VALUES("12","6","7","5.00","");
INSERT INTO respuesta VALUES("13","6","8","5.00","");
INSERT INTO respuesta VALUES("14","6","9","4.00","");
INSERT INTO respuesta VALUES("15","8","6","5.00","");
INSERT INTO respuesta VALUES("16","7","1","2.00","");
INSERT INTO respuesta VALUES("17","7","2","5.00","");
INSERT INTO respuesta VALUES("18","7","3","4.00","");
INSERT INTO respuesta VALUES("19","10","6","1.00","an jkwcd");
INSERT INTO respuesta VALUES("20","11","7","5.00","fvea f");
INSERT INTO respuesta VALUES("21","11","8","5.00","");
INSERT INTO respuesta VALUES("22","11","9","5.00","");
INSERT INTO respuesta VALUES("23","11","10","5.00","");





DROP TABLE IF EXISTS `rubro`;


CREATE TABLE `rubro` (
  `id_rubro` int(11) NOT NULL AUTO_INCREMENT,
  `id_instrumento` int(11) NOT NULL,
  `texto_aspecto` text NOT NULL,
  `orden` int(11) NOT NULL,
  PRIMARY KEY (`id_rubro`),
  KEY `id_instrumento` (`id_instrumento`),
  CONSTRAINT `rubro_ibfk_1` FOREIGN KEY (`id_instrumento`) REFERENCES `instrumento` (`id_instrumento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO rubro VALUES("1","1","ejemplo de rubro 1","1");
INSERT INTO rubro VALUES("2","1","ejemplo de rubro 2","2");
INSERT INTO rubro VALUES("3","1","ejemplo de rubro 3","3");
INSERT INTO rubro VALUES("6","2","ejeplo","1");
INSERT INTO rubro VALUES("7","3","Aspecto 1","1");
INSERT INTO rubro VALUES("8","3","Aspecto 2","2");
INSERT INTO rubro VALUES("9","3","Aspecto 3","3");
INSERT INTO rubro VALUES("10","3","Aspecto 4","4");



