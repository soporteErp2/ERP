<?php
	
	// CREACION DE LAS TABLAS INEXISTENTES
	$arrayTables ['nomina_configuracion_consecutivos'] = "CREATE TABLE nomina_configuracion_consecutivos (
																		  id int(11) NOT NULL AUTO_INCREMENT,
																		  prefijo varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
																		  consecutivo int(1) DEFAULT 1,
																		  codigo varchar(255) DEFAULT NULL,
																		  tipo varchar(255) DEFAULT NULL,
																		  id_empresa int(11) DEFAULT NULL,
																		  id_sucursal int(11) DEFAULT NULL,
																		  activo int(1) DEFAULT 1,
																		  PRIMARY KEY (id),
																		  UNIQUE KEY id (id) USING BTREE,
																		  KEY id_empresa (id_empresa) USING BTREE,
																		  KEY is_sucursal (id_sucursal) USING BTREE
																		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_tipo_documentos'] = "CREATE TABLE nomina_configuracion_tipo_documentos (
																		  id int(11) NOT NULL AUTO_INCREMENT,
																		  codigo varchar(255) DEFAULT NULL,
																		  tipo varchar(255) DEFAULT NULL,
																		  detalle varchar(255) DEFAULT NULL,																		  
																		  id_empresa int(11) DEFAULT NULL,
																		  id_sucursal int(11) DEFAULT NULL,
																		  activo int(1) DEFAULT 1,
																		  PRIMARY KEY (id),
																		  UNIQUE KEY id (id) USING BTREE,
																		  KEY id_empresa (id_empresa) USING BTREE,
																		  KEY is_sucursal (id_sucursal) USING BTREE
																		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_tipo_documentos_ajuste'] = "CREATE TABLE nomina_configuracion_tipo_documentos_ajuste (
																		  id int(11) NOT NULL AUTO_INCREMENT,
																		  codigo varchar(255) DEFAULT NULL,
																		  nombre varchar(255) DEFAULT NULL,
																		  id_empresa int(11) DEFAULT NULL,
																		  id_sucursal int(11) DEFAULT NULL,
																		  activo int(1) DEFAULT 1,
																		  PRIMARY KEY (id),
																		  UNIQUE KEY id (id) USING BTREE,
																		  KEY id_empresa (id_empresa) USING BTREE,
																		  KEY is_sucursal (id_sucursal) USING BTREE
																		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	// $arrayTables ['nomina_configuracion_tipo_contratos'] = "CREATE TABLE nomina_configuracion_tipo_contratos (
																		  // id int(11) NOT NULL AUTO_INCREMENT,
																		  // codigo varchar(255) DEFAULT NULL,
																		  // tipo varchar(255) DEFAULT NULL,
																		  // id_empresa int(11) DEFAULT NULL,
																		  // id_sucursal int(11) DEFAULT NULL,
																		  // activo int(1) DEFAULT 1,
																		  // PRIMARY KEY (id),
																		  // UNIQUE KEY id (id) USING BTREE,
																		  // KEY id_empresa (id_empresa) USING BTREE,
																		  // KEY is_sucursal (id_sucursal) USING BTREE
																		// ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_tipo_trabajador'] = "CREATE TABLE nomina_configuracion_tipo_trabajador (
																		  id int(11) NOT NULL AUTO_INCREMENT,
																		  codigo varchar(255) DEFAULT NULL,
																		  tipo varchar(255) DEFAULT NULL,
																		  id_empresa int(11) DEFAULT NULL,
																		  id_sucursal int(11) DEFAULT NULL,
																		  activo int(1) DEFAULT 1,
																		  PRIMARY KEY (id),
																		  UNIQUE KEY id (id) USING BTREE,
																		  KEY id_empresa (id_empresa) USING BTREE,
																		  KEY is_sucursal (id_sucursal) USING BTREE
																		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_subtipo_trabajador'] = "CREATE TABLE nomina_configuracion_subtipo_trabajador (
																			  id int(11) NOT NULL AUTO_INCREMENT,
																			  codigo varchar(255) DEFAULT NULL,
																			  tipo varchar(255) DEFAULT NULL,
																			  id_empresa int(11) DEFAULT NULL,
																			  id_sucursal int(11) DEFAULT NULL,
																			  activo int(1) DEFAULT 1,
																			  PRIMARY KEY (id),
																			  UNIQUE KEY id (id) USING BTREE,
																			  KEY id_empresa (id_empresa) USING BTREE,
																			  KEY is_sucursal (id_sucursal) USING BTREE
																			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_formas_pago'] = "CREATE TABLE nomina_configuracion_formas_pago (
																	  id int(11) NOT NULL AUTO_INCREMENT,
																	  codigo varchar(255) DEFAULT NULL,
																	  nombre varchar(255) DEFAULT NULL,
																	  id_empresa int(11) DEFAULT NULL,
																	  id_sucursal int(11) DEFAULT NULL,
																	  activo int(1) DEFAULT 1,
																	  PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE,
																	  KEY id_empresa (id_empresa) USING BTREE,
																	  KEY is_sucursal (id_sucursal) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_medios_pago'] = "CREATE TABLE nomina_configuracion_medios_pago (
																	  id int(11) NOT NULL AUTO_INCREMENT,
																	  codigo varchar(255) DEFAULT NULL,
																	  nombre varchar(255) DEFAULT NULL,
																	  id_empresa int(11) DEFAULT NULL,
																	  id_sucursal int(11) DEFAULT NULL,
																	  activo int(1) DEFAULT 1,
																	  PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE,
																	  KEY id_empresa (id_empresa) USING BTREE,
																	  KEY is_sucursal (id_sucursal) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_hora_extra_recargo'] = "CREATE TABLE nomina_configuracion_hora_extra_recargo (
																			  id int(11) NOT NULL AUTO_INCREMENT,
																			  codigo varchar(255) DEFAULT NULL,
																			  nombre varchar(255) DEFAULT NULL,
																			  porcentaje varchar(255) DEFAULT NULL,
																			  id_empresa int(11) DEFAULT NULL,
																			  id_sucursal int(11) DEFAULT NULL,
																			  activo int(1) DEFAULT 1,
																			  PRIMARY KEY (id),
																			  UNIQUE KEY id (id) USING BTREE,
																			  KEY id_empresa (id_empresa) USING BTREE,
																			  KEY is_sucursal (id_sucursal) USING BTREE
																			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_idiomas'] = "CREATE TABLE nomina_configuracion_idiomas (
																	  id int(11) NOT NULL AUTO_INCREMENT,
																	  nombre varchar(255) DEFAULT NULL,
																	  ISO_639_1  varchar(255) DEFAULT NULL,
																	  ISO_639_2  varchar(255) DEFAULT NULL,
																	  id_empresa int(11) DEFAULT NULL,
																	  id_sucursal int(11) DEFAULT NULL,
																	  activo int(1) DEFAULT 1,
																	  PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_configuracion_monedas'] = "CREATE TABLE nomina_configuracion_monedas (
																	  id int(11) NOT NULL AUTO_INCREMENT,
																	  codigo varchar(255) DEFAULT NULL,
																	  divisa  varchar(255) DEFAULT NULL,
																	  pais  varchar(255) DEFAULT NULL,
																	  id_empresa int(11) DEFAULT NULL,
																	  id_sucursal int(11) DEFAULT NULL,
																	  activo int(1) DEFAULT 1,
																	  PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ['nomina_wizard_process'] = "CREATE TABLE nomina_wizard_process (
																	  id int(11) NOT NULL AUTO_INCREMENT,
																	  `table` varchar(255) DEFAULT NULL,
																	  process  varchar(255) DEFAULT NULL,
																	  id_empresa int(11) DEFAULT NULL,
																	  activo int(1) DEFAULT 1,
																	  PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";


	$arrayTables ["nomina_planillas_electronica"] = "CREATE TABLE nomina_planillas_electronica (
																		id	int(11) NOT NULL AUTO_INCREMENT,
																		random	varchar(255),
																		fecha_creacion	date,
																		fecha_generacion	date,
																		fecha_documento	date,
																		fecha_inicio	date,
																		fecha_final	date,
																		consecutivo	int(11),
																		id_tipo_liquidacion	int(11),
																		tipo_liquidacion	varchar(255),
																		dias_liquidacion	int(11),
																		codigo_tipo_documento	varchar(255),
																		tipo_documento	varchar(255),
																		id_usuario	int(11),
																		usuario	varchar(255),
																		observacion	longtext,
																		estado	int(11) DEFAULT 0,
																		id_planilla_liquidacion	int(11),
																		consecutivo_planilla_liquidacion	int(11),
																		id_sucursal	int(11),
																		sucursal	varchar(255),
																		id_empresa	int(11),
																		activo	int(11) DEFAULT 1,
																	  PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ["nomina_planillas_electronica_empleados"] = "CREATE TABLE nomina_planillas_electronica_empleados (
																	id	int(11) NOT NULL AUTO_INCREMENT,
																	id_planilla	int(11),
																	id_empleado	int(11),
																	tipo_documento	varchar(255),
																	documento_empleado	varchar(255),
																	nombre_empleado	varchar(255),
																	dias_laborados	int(11),
																	dias_laborados_empleado	int(11),
																	id_contrato	int(11),
																	prefijo	varchar(11),
																	consecutivo	int(11),
																	tiempo_laborado	int(11),
																	codigo_tipo_ajuste	varchar(100),
																	planilla_relacionada_al_ajuste	varchar(100),
																	id_usuario_NE int(11),
																	nombre_usuario_NE varchar(255),
																	cedula_usuario_NE varchar(255),
																	fecha_NE date,
																	hora_NE time,
																	response_NE longtext,
																	UUID varchar(255),
																	verificado	varchar(255),
																	observaciones	longtext,
																	id_sucursal	int(11),
																	id_empresa	int(11),
																	activo	int(11) DEFAULT 1,
																	PRIMARY KEY (id),
																	  UNIQUE KEY id (id) USING BTREE
																	) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ["nomina_electronica_estructura_conceptos"] = "CREATE TABLE nomina_electronica_estructura_conceptos (
																				id int(11) NOT NULL AUTO_INCREMENT,
																				nombre varchar(255),
																				estructura longtext,
																				id_empresa	int(11),
																				activo	int(11) DEFAULT 1,
																				PRIMARY KEY (id),
																				  UNIQUE KEY id (id) USING BTREE
																				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";


	$arrayTables ["nomina_planillas_empleados_conceptos_datos_nomina_electronica"] = "CREATE TABLE nomina_planillas_empleados_conceptos_datos_nomina_electronica (
																				id int(11) NOT NULL AUTO_INCREMENT,
																				id_estructura int(11),
																				tipo_planilla varchar(11),
																				id_planilla int(11),
																				id_empleado int(11),
																				id_concepto int(11),
																				data longtext,
																				id_empresa	int(11),
																				activo	int(11) DEFAULT 1,
																				PRIMARY KEY (id),
																				  UNIQUE KEY id (id) USING BTREE
																				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC;";

	$arrayTables ["nomina_planillas_electronica_empleados_conceptos"] = "CREATE TABLE nomina_planillas_electronica_empleados_conceptos (
																		  id int(11) NOT NULL AUTO_INCREMENT,
																		  id_planilla int(11) DEFAULT NULL,
																		  id_empleado int(11) DEFAULT NULL,
																		  id_contrato int(11) DEFAULT NULL,
																		  id_concepto int(11) DEFAULT NULL,
																		  codigo_concepto varchar(255) DEFAULT NULL,
																		  concepto varchar(255)  DEFAULT NULL,
																		  naturaleza varchar(100)  DEFAULT NULL,
																		  valor_concepto double DEFAULT NULL,
																		  valor_campo_texto double DEFAULT NULL,
																		  data longtext DEFAULT NULL,
																		  id_sucursal int(11) DEFAULT NULL,
																		  id_empresa int(11) DEFAULT NULL,
																		  activo int(11) DEFAULT '1',
																		  debug_nomina varchar(255)  DEFAULT '',
																		  PRIMARY KEY (id),
																		  UNIQUE KEY id (id) USING BTREE,
																		  KEY id_planilla (id_planilla) USING BTREE,
																		  KEY id_empleado (id_empleado) USING BTREE,
																		  KEY id_contrato (id_contrato) USING BTREE,
																		  KEY id_concepto (id_concepto) USING BTREE,
																		  KEY id_empresa (id_empresa) USING BTREE
																		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC";

	$arrayTables ["nomina_planillas_electronica_empleados_fechas_pago"] = "CREATE TABLE nomina_planillas_electronica_empleados_fechas_pago (
																		  id int(11) NOT NULL AUTO_INCREMENT,
																		  id_planilla int(11) DEFAULT NULL,
																		  id_empleado int(11) DEFAULT NULL,
																		  fecha date DEFAULT NULL,
																		  id_empresa int(11) DEFAULT NULL,
																		  activo int(11) DEFAULT '1',
																		  PRIMARY KEY (id),
																		  UNIQUE KEY id (id) USING BTREE,
																		  KEY id_planilla (id_planilla) USING BTREE,
																		  KEY id_empleado (id_empleado) USING BTREE,
																		  KEY id_empresa (id_empresa) USING BTREE
																		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC";




// MODIFICACION DE ESTRUCTURA DE LAS TABLAS

	$arrayTables['empresas'] = [
		array("colum_name"=>"primer_apellido","properties"=>"VARCHAR( 255 )"),
		array("colum_name"=>"segundo_apellido","properties"=>"VARCHAR( 255 )"),
		array("colum_name"=>"primer_nombre","properties"=>"VARCHAR( 255 )"),
		array("colum_name"=>"otros_nombres","properties"=>"VARCHAR( 255 )"),
	];

	$arrayTables ['nomina_tipos_liquidacion'] =[
		array("colum_name"=>"codigo","properties"=>"VARCHAR( 255 )"),
	];

	$arrayTables ['empleados_contratos'] = [
		array("colum_name"=>"id_tipo_trabajador","properties"=>"int(11)"),
		array("colum_name"=>"id_subtipo_trabajador","properties"=>"int(11)"),
		array("colum_name"=>"id_forma_pago","properties"=>"int(11)"),
		array("colum_name"=>"id_medio_pago","properties"=>"varchar(255)"),
		array("colum_name"=>"nombre_banco","properties"=>"varchar(255)"),
		array("colum_name"=>"tipo_cuenta_bancaria","properties"=>"varchar(255)"),

	];

	$arrayTables ['nomina_conceptos'] =[
		array("colum_name"=>"clasificacion","properties"=>"VARCHAR( 100 )"),
	];

	$arrayTables ['nomina_tipo_contrato'] =[
		array("colum_name"=>"codigo_dian","properties"=>"VARCHAR( 100 )"),
	];

	// $arrayTables ['empresas'] = "ALTER TABLE empresas ADD primer_apellido VARCHAR( 255 ),
	// 														segundo_apellido VARCHAR( 255 ),
	// 														primer_nombre VARCHAR( 255 ),
	// 														otros_nombres VARCHAR( 255 )";

	// $arrayTables ['nomina_tipos_liquidacion'] = "ALTER TABLE nomina_tipos_liquidacion ADD codigo VARCHAR( 255 )";
	
	// $arrayTables ['empleados_contratos'] = "ALTER TABLE empleados_contratos ADD id_tipo_trabajador int(11),
	// 																			id_subtipo_trabajador int(11),
	// 																			id_forma_pago int(11),
	// 																			id_medio_pago varchar(255),
	// 																			nombre_banco varchar(255),
	// 																			tipo_cuenta_bancaria int(11)";