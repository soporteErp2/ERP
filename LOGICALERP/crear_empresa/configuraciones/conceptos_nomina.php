<?php

// INSERTAR LOS GRUPOS DE CONCEPTOS DE NOMINA POR DEFECTO
		$sqlGruposConceptos = "INSERT INTO nomina_grupos_conceptos (descripcion,id_empresa) VALUES
								('GENERAL',$id_empresa),
								('SEGURIDAD SOCIAL',$id_empresa),
								('PARAFISCALES',$id_empresa),
								('CARGA PRESTACIONAL',$id_empresa)";
		$queryGruposConceptos = mysql_query($sqlGruposConceptos,$link);
		if (!$queryGruposConceptos) { deleteInfoEmpresa($link,$id_empresa,"<br>NO SE INSERTARON LOS GRUPOS DE CONCEPTOS PARA LA NOMINA<br/>",38); }

		// INSERTAR LOS CONCEPTOS
		// CONSULTAR LOS ID DE LOS GRUPOS DE LOS CONCEPTOS
		$sql   = "SELECT id,descripcion FROM nomina_grupos_conceptos WHERE activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id_grupo          = $row['id'];
			$descripcion_grupo = $row['descripcion'];
			$arrayGruposConcepto[$descripcion_grupo]=$id_grupo;
		}

		$arrayConceptos['GENERAL']['AT'] 	 = array(
													'descripcion'                  => 'AUXILIO DE TRANSPORTE',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51052701',
													'cuenta_niif'                  => '51052701',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'true',
													'nivel_formula'                => 4,
													'formula'                      => '((83140/30)*({DL}-|>DS<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>AP<|-|>LM<|-|>PNR<| ))',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false',
													);

		$arrayConceptos['GENERAL']['CM'] 	 = array(
													'descripcion'                  => 'COMISIONES',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '52051801',
													'cuenta_niif'                  => '52051801',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false',
													);

		$arrayConceptos['GENERAL']['MT']	  = array(
													'descripcion'                  => 'MEDIOS DE TRANSPORTE',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '52054801',
													'cuenta_niif'                  => '52054801',
													'caracter'                     => 'debito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'true',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'true',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['SB']	  = array(
													'descripcion'                  => 'SALARIO BASICO',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51050601',
													'cuenta_niif'                  => '51050601',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'true',
													'nivel_formula'                => 2,
													'formula'                      => '({SC} /30)*({DL}-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>LM<|)',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['HDFCD']	  = array(
													'descripcion'                  => 'HORA DOMINICAL O FESTIVO CON DESCANSO',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '{SC} /240*{CT} *1.75',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['IM']	  = array(
													'descripcion'                  => 'INCAPACIDAD MEDICA EMPRESA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51052401',
													'cuenta_niif'                  => '51052401',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '({SC} /30)*{CT}',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													 );
		$arrayConceptos['GENERAL']['IMEPS90']	  = array(
													'descripcion'                  => 'INCAPACIDAD MEDICA EPS HASTA 90 DIAS',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51052401',
													'cuenta_niif'                  => '51052401',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '((({SC} /30)*66.66)/100)*{CT}',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													 );
		$arrayConceptos['GENERAL']['IMEPS180']	  = array(
													'descripcion'                  => 'INCAPACIDAD MEDICA EPS DE 91 HASTA 180 DIAS',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51052401',
													'cuenta_niif'                  => '51052401',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '((({SC} /30)*50)/100)*{CT}',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													 );
		$arrayConceptos['GENERAL']['IMARL']	  = array(
														'descripcion'                  => 'INCAPACIDAD MEDICA ARL',
														'naturaleza'                   => 'Devengo',
														'cuenta_colgaap'               => '51052401',
														'cuenta_niif'                  => '51052401',
														'caracter'                     => 'debito',
														'centro_costos'                => 'true',
														'cuenta_contrapartida_colgaap' => '25050101',
														'cuenta_contrapartida_niif'    => '25050101',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'true',
														'carga_automatica'             => 'false',
														'nivel_formula'                => 1,
														'formula'                      => '({SC} /30)*{CT}',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Empleado',
														'tercero_cruce'                => 'Empleado',
														'resta_dias'                   => 'false'
														 );
		$arrayConceptos['GENERAL']['IMAFP']	  = array(
														'descripcion'                  => 'INCAPACIDAD MEDICA AFP',
														'naturaleza'                   => 'Devengo',
														'cuenta_colgaap'               => '51052401',
														'cuenta_niif'                  => '51052401',
														'caracter'                     => 'debito',
														'centro_costos'                => 'true',
														'cuenta_contrapartida_colgaap' => '25050101',
														'cuenta_contrapartida_niif'    => '25050101',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'true',
														'carga_automatica'             => 'false',
														'nivel_formula'                => 1,
														'formula'                      => '((({SC} /30)*50)/100)*{CT}',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Empleado',
														'tercero_cruce'                => 'Empleado',
														'resta_dias'                   => 'false'
														 );


		$arrayConceptos['GENERAL']['LM']	  = array(
													'descripcion'                  => 'LICENCIA DE MATERNIDAD / LEY MARIA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51052401',
													'cuenta_niif'                  => '51052401',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '({SC} /30)*{CT}',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													 );
		$arrayConceptos['GENERAL']['PNR']	  = array(
													'descripcion'                  => 'PERMISOS NO REMUNERADOS',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '51050601',
													'cuenta_niif'                  => '51050601',
													'caracter'                     => 'credito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '({SC} /30)*{CT}',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'true'
													 );
		$arrayConceptos['GENERAL']['RF 383']  = array(
													'descripcion'                  => 'RETEFUENTE TABLA 383',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23650501',
													'cuenta_niif'                  => '23650501',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['RST']	  = array(
													'descripcion'                  => 'RESIDENTE',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '11050501',
													'cuenta_niif'                  => '11050501',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['SF']	  = array(
													'descripcion'                  => 'SERVICIOS FUNERARIOS',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23809505',
													'cuenta_niif'                  => '23809505',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Entidad',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['PYA']	  = array(
													'descripcion'                  => 'PRESTAMOS Y ANTICIPOS',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '13652501',
													'cuenta_niif'                  => '13652501',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['AFC']	  = array(
													'descripcion'                  => 'APORTES CUENTAS AFC',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23704501',
													'cuenta_niif'                  => '23704501',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['DL']	  = array(
													'descripcion'                  => 'DESCUENTO DE LIBRANZA',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23703001',
													'cuenta_niif'                  => '23703001',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Entidad',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['DS']	  = array(
													'descripcion'                  => 'SUSPENSION',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '51050601',
													'cuenta_niif'                  => '51050601',
													'caracter'                     => 'credito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '({SC} /30)*{CT}',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'true'
													 );
		$arrayConceptos['GENERAL']['RF 384']  = array(
													'descripcion'                  => 'RETEFUENTE TABLA 384',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23650503',
													'cuenta_niif'                  => '23650503',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['BN']	  = array(
													'descripcion'                  => 'BONIFICACIONES',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51054801',
													'cuenta_niif'                  => '51054801',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['RF']	  = array(
													'descripcion'                  => 'REALIZACION DE FIESTA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['IR']	  = array(
													'descripcion'                  => 'INCENTIVO DE RODAMIENTO',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51054502',
													'cuenta_niif'                  => '51054502',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['AM']	  = array(
													'descripcion'                  => 'AUXILIO DE MOVILIZACION',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51054501',
													'cuenta_niif'                  => '51054501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['HEDO']	  = array(
													'descripcion'                  => 'HORA EXTRA DIURNA ORDINARIA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '(({SC} /14400)*{CT} )*1.25',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['HENO']	  = array(
													'descripcion'                  => 'HORA EXTRA NOCTURNA ORDINARIA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '{SC} /14400*{CT} *1.75',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['HEDF']	  = array(
													'descripcion'                  => 'HORA EXTRA DIURNA FESTIVA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '{SC} /14400*{CT} *2',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['HENF']	  = array(
													'descripcion'                  => 'HORA EXTRA NOCTURNA FESTIVA',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '{SC} /14400*{CT} *2.5',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['HDFSD']	  = array(
													'descripcion'                  => 'HORA DOMINICAL O FESTIVO SIN DESCANSO',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '{SC} /240*{CT} *2.75',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['RN']	  = array(
													'descripcion'                  => 'RECARGO NOCTURNO',
													'naturaleza'                   => 'Devengo',
													'cuenta_colgaap'               => '51051501',
													'cuenta_niif'                  => '51051501',
													'caracter'                     => 'debito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'credito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '{SC} /14400*{CT} *0.35',
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['DT']	  = array(
													'descripcion'                  => 'DESCUENTO TELEFONO',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23809506',
													'cuenta_niif'                  => '23809506',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Entidad',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['AP']	  = array(
													'descripcion'                  => 'AUSENCIA',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '51050601',
													'cuenta_niif'                  => '51050601',
													'caracter'                     => 'credito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['EJ']	  = array(
													'descripcion'                  => 'EMBARGOS JUDICIALES',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '23702501',
													'cuenta_niif'                  => '23702501',
													'caracter'                     => 'credito',
													'centro_costos'                => 'false',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '',
													'tipo_concepto'                => 'Personal',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);
		$arrayConceptos['GENERAL']['PNRH']	  = array(
													'descripcion'                  => 'PERMISOS NO REMUNERADOS HORAS',
													'naturaleza'                   => 'Deduccion',
													'cuenta_colgaap'               => '51050601',
													'cuenta_niif'                  => '51050601',
													'caracter'                     => 'credito',
													'centro_costos'                => 'true',
													'cuenta_contrapartida_colgaap' => '25050101',
													'cuenta_contrapartida_niif'    => '25050101',
													'caracter_contrapartida'       => 'debito',
													'centro_costos_contrapartida'  => 'false',
													'imprimir_volante'             => 'true',
													'carga_automatica'             => 'false',
													'nivel_formula'                => 1,
													'formula'                      => '({SC} /14400)*{CT}' ,
													'tipo_concepto'                => 'General',
													'tercero'                      => 'Empleado',
													'tercero_cruce'                => 'Empleado',
													'resta_dias'                   => 'false'
													);


		$arrayConceptos['SEGURIDAD SOCIAL']['EPST']	= array(
														'descripcion'                  => 'EPS EMPLEADO',
														'naturaleza'                   => 'Deduccion',
														'cuenta_colgaap'               => '25050101',
														'cuenta_niif'                  => '25050101',
														'caracter'                     => 'debito',
														'centro_costos'                => 'false',
														'cuenta_contrapartida_colgaap' => '23700501',
														'cuenta_contrapartida_niif'    => '23700501',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'true',
														'carga_automatica'             => 'true',
														'nivel_formula'                => 2,
														'formula'                      => '(((({SC} /30)*({DL}-|>DS<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>AP<|-|>LM<|-|>PNR<|-|>PNRH<|))+[HDFCD] +[IM]+[IMEPS90] +[IMEPS180] +[IMARL] +[IMAFP] +[LM]+ [HENF] +[HDFSD] +[RN] +[HEDO] +[HENO] +[HEDF]+ [CM]  ) )*0.04',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Empleado',
														'tercero_cruce'                => 'Entidad',
														'resta_dias'                   => 'false'
														);
		$arrayConceptos['SEGURIDAD SOCIAL']['EPSE']	= array(
														'descripcion'                  => 'EPS EMPLEADOR',
														'naturaleza'                   => 'Apropiacion',
														'cuenta_colgaap'               => '51056901',
														'cuenta_niif'                  => '51056901',
														'caracter'                     => 'debito',
														'centro_costos'                => 'true',
														'cuenta_contrapartida_colgaap' => '23700501',
														'cuenta_contrapartida_niif'    => '23700501',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'false',
														'carga_automatica'             => 'false',
														'nivel_formula'                => 2,
														'formula'                      => '((({SC} /30)*({DL}-|>DS<|-|>AP<|-|>PNR<|))+[HDFCD] + [HENF] +[HDFSD] +[RN] +[HEDO] +[HENO] +[HEDF]+ [CM] )*0.085',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Entidad',
														'tercero_cruce'                => 'Entidad',
														'resta_dias'                   => 'false'
														);
		$arrayConceptos['SEGURIDAD SOCIAL']['PT']	= array(
														'descripcion'                  => 'PENSION EMPLEADO',
														'naturaleza'                   => 'Deduccion',
														'cuenta_colgaap'               => '25050101',
														'cuenta_niif'                  => '25050101',
														'caracter'                     => 'debito',
														'centro_costos'                => 'false',
														'cuenta_contrapartida_colgaap' => '23803001',
														'cuenta_contrapartida_niif'    => '23803001',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'true',
														'carga_automatica'             => 'true',
														'nivel_formula'                => 2,
														'formula'                      => '(((({SC} /30)*({DL}-|>DS<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>AP<|-|>LM<|-|>PNR<|-|>PNRH<|))+[HDFCD] +[IM] +[IMEPS90] +[IMEPS180] +[IMARL] +[IMAFP]  +[LM]+ [HENF] +[HDFSD] +[RN] +[HEDO] +[HENO] +[HEDF]+ [CM]  ))*0.04',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Empleado',
														'tercero_cruce'                => 'Entidad',
														'resta_dias'                   => 'false'
														);
		$arrayConceptos['SEGURIDAD SOCIAL']['PE']	= array(
														'descripcion'                  => 'PENSION EMPLEADOR',
														'naturaleza'                   => 'Apropiacion',
														'cuenta_colgaap'               => '51057001',
														'cuenta_niif'                  => '51057001',
														'caracter'                     => 'debito',
														'centro_costos'                => 'true',
														'cuenta_contrapartida_colgaap' => '23803001',
														'cuenta_contrapartida_niif'    => '23803001',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'false',
														'carga_automatica'             => 'true',
														'nivel_formula'                => 2,
														'formula'                      => '((({SC} /30)*({DL}-|>DS<|-|>AP<|-|>PNR<|))+[HDFCD] + [HENF] +[HDFSD] +[RN] +[HEDO] +[HENO] +[HEDF]+ [CM] )*0.12',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Entidad',
														'tercero_cruce'                => 'Entidad',
														'resta_dias'                   => 'false'
														);
		$arrayConceptos['SEGURIDAD SOCIAL']['ARL']	= array(
														'descripcion'                  => 'ARL',
														'naturaleza'                   => 'Apropiacion',
														'cuenta_colgaap'               => '51056801',
														'cuenta_niif'                  => '51056801',
														'caracter'                     => 'debito',
														'centro_costos'                => 'true',
														'cuenta_contrapartida_colgaap' => '23700601',
														'cuenta_contrapartida_niif'    => '23700601',
														'caracter_contrapartida'       => 'credito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'false',
														'carga_automatica'             => 'true',
														'nivel_formula'                => 4,
														'formula'                      => '((({SC} /30)*({DL}-|>DS<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>AP<|-|>LM<|-|>PNR<|))+[HDFCD] +[HENF] +[HDFSD] +[RN] +[HEDO] +[HENO] +[HEDF] +[CM])  *{NRL} /100',
														'tipo_concepto'                => 'General',
														'tercero'                      => 'Entidad',
														'tercero_cruce'                => 'Entidad',
														'resta_dias'                   => 'false'
														);
		$arrayConceptos['SEGURIDAD SOCIAL']['FSP']	= array(
														'descripcion'                  => 'FONDO DE SOLIDARIDAD PENSIONAL',
														'naturaleza'                   => 'Deduccion',
														'cuenta_colgaap'               => '23803001',
														'cuenta_niif'                  => '23803001',
														'caracter'                     => 'credito',
														'centro_costos'                => 'false',
														'cuenta_contrapartida_colgaap' => '25050101',
														'cuenta_contrapartida_niif'    => '25050101',
														'caracter_contrapartida'       => 'debito',
														'centro_costos_contrapartida'  => 'false',
														'imprimir_volante'             => 'true',
														'carga_automatica'             => 'false',
														'nivel_formula'                => 1,
														'formula'                      => '',
														'tipo_concepto'                => 'Personal',
														'tercero'                      => 'Entidad',
														'tercero_cruce'                => 'Empleado',
														'resta_dias'                   => 'false'
														);

		$arrayConceptos['PARAFISCALES']['SENA']	= array(
																	'descripcion'                  => 'SENA',
																	'naturaleza'                   => 'Apropiacion',
																	'cuenta_colgaap'               => '51057801',
																	'cuenta_niif'                  => '51057801',
																	'caracter'                     => 'debito',
																	'centro_costos'                => 'true',
																	'cuenta_contrapartida_colgaap' => '23701001',
																	'cuenta_contrapartida_niif'    => '23701001',
																	'caracter_contrapartida'       => 'credito',
																	'centro_costos_contrapartida'  => 'false',
																	'imprimir_volante'             => 'false',
																	'carga_automatica'             => 'false',
																	'nivel_formula'                => 1,
																	'formula'                      => '(({SC} /30)*({DL}-|>DS<|-|>AP<|-|>LM<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>PNR<|)+[HENF] +[HDFSD] +[HDFCD] +[RN] +[HEDO] +[HENO] +[HEDF] + [CM]) *0.02',
																	'tipo_concepto'                => 'General',
																	'tercero'                      => 'Entidad',
																	'tercero_cruce'                => 'Entidad',
																	'resta_dias'                   => 'false'
																	);
		$arrayConceptos['PARAFISCALES']['ICBF']	= array(
																	'descripcion'                  => 'ICBF',
																	'naturaleza'                   => 'Apropiacion',
																	'cuenta_colgaap'               => '51057501',
																	'cuenta_niif'                  => '51057501',
																	'caracter'                     => 'debito',
																	'centro_costos'                => 'true',
																	'cuenta_contrapartida_colgaap' => '23701001',
																	'cuenta_contrapartida_niif'    => '23701001',
																	'caracter_contrapartida'       => 'credito',
																	'centro_costos_contrapartida'  => 'false',
																	'imprimir_volante'             => 'false',
																	'carga_automatica'             => 'false',
																	'nivel_formula'                => 1,
																	'formula'                      => '(({SC} /30)*({DL}-|>DS<|-|>AP<|-|>LM<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>PNR<|)+[HENF] +[HDFSD] +[HDFCD] +[RN] +[HEDO] +[HENO] +[HEDF] + [CM]) *0.03',
																	'tipo_concepto'                => 'General',
																	'tercero'                      => 'Entidad',
																	'tercero_cruce'                => 'Entidad',
																	'resta_dias'                   => 'false'
																	);
		$arrayConceptos['PARAFISCALES']['CDCF']	= array(
																	'descripcion'                  => 'CAJA DE COMPENSACION',
																	'naturaleza'                   => 'Apropiacion',
																	'cuenta_colgaap'               => '51057201',
																	'cuenta_niif'                  => '51057201',
																	'caracter'                     => 'debito',
																	'centro_costos'                => 'false',
																	'cuenta_contrapartida_colgaap' => '23701001',
																	'cuenta_contrapartida_niif'    => '23701001',
																	'caracter_contrapartida'       => 'credito',
																	'centro_costos_contrapartida'  => 'false',
																	'imprimir_volante'             => 'false',
																	'carga_automatica'             => 'true',
																	'nivel_formula'                => 1,
																	'formula'                      => '(({SC} /30)*({DL}-|>DS<|-|>AP<|-|>LM<|-|>IM<|-|>IMEPS90<|-|>IMEPS180<|-|>IMARL<|-|>IMAFP<|-|>PNR<|)+[HENF] +[HDFSD] +[HDFCD] +[RN] +[HEDO] +[HENO] +[HEDF] + [CM]) *0.04',
																	'tipo_concepto'                => 'General',
																	'tercero'                      => 'Entidad',
																	'tercero_cruce'                => 'Entidad',
																	'resta_dias'                   => 'false'
																	);

		$arrayConceptos['CARGA PRESTACIONAL']['CS']		= array(
															'descripcion'                  => 'CESANTIAS',
															'naturaleza'                   => 'Provision',
															'cuenta_colgaap'               => '51053001',
															'cuenta_niif'                  => '51053001',
															'caracter'                     => 'debito',
															'centro_costos'                => 'true',
															'cuenta_contrapartida_colgaap' => '25101001',
															'cuenta_contrapartida_niif'    => '25101001',
															'cuenta_colgaap_liquidacion'   => '25101001',
															'cuenta_niif_liquidacion'      => '25101001',
															'tercero_cruce_liquidacion'    => 'Empleado',
															'caracter_contrapartida'       => 'credito',
															'centro_costos_contrapartida'  => 'false',
															'imprimir_volante'             => 'false',
															'carga_automatica'             => 'true',
															'nivel_formula'                => 5,
															'formula'                      => '((({SC}+83140)*( {DL}-|>DS<|-|>AP<|-|>PNR<|) )+(([HDFCD]+[HENF]+[HDFSD]+[RN]+[CM]+[HEDO]+[HENO]+[HEDF])*30) )/360 ',
															'nivel_formula_liquidacion'    => '1',
															'formula_liquidacion'          => '{BL}*{DL}/360',
															'tipo_concepto'                => 'General',
															'tercero'                      => 'Empleado',
															'tercero_cruce'                => 'Empleado',
															'resta_dias'                   => 'false'
															 );
		$arrayConceptos['CARGA PRESTACIONAL']['PS']		= array(
															'descripcion'                  => 'PRIMA DE SERVICIOS',
															'naturaleza'                   => 'Provision',
															'cuenta_colgaap'               => '51053601',
															'cuenta_niif'                  => '51053601',
															'caracter'                     => 'debito',
															'centro_costos'                => 'true',
															'cuenta_contrapartida_colgaap' => '25200101',
															'cuenta_contrapartida_niif'    => '25200101',
															'caracter_contrapartida'       => 'credito',
															'cuenta_colgaap_liquidacion'   => '25101001',
															'cuenta_niif_liquidacion'      => '25101001',
															'tercero_cruce_liquidacion'    => 'Empleado',
															'centro_costos_contrapartida'  => 'false',
															'imprimir_volante'             => 'false',
															'carga_automatica'             => 'true',
															'nivel_formula'                => 5,
															'formula'                      => '((({SC}+83140)*( {DL}-|>DS<|-|>AP<|-|>PNR<|) )+(([HDFCD]+[HENF]+[HDFSD]+[RN]+[CM]+[HEDO]+[HENO]+[HEDF])*30) )/360',
															'nivel_formula_liquidacion'    => '1',
															'formula_liquidacion'          => '{BL}*{DL}/360',
															'tipo_concepto'                => 'General',
															'tercero'                      => 'Empleado',
															'tercero_cruce'                => 'Empleado',
															'resta_dias'                   => 'false'
															 );
		$arrayConceptos['CARGA PRESTACIONAL']['ISC']	= array(
															'descripcion'                  => 'INTERESES SOBRE CESANTIAS',
															'naturaleza'                   => 'Provision',
															'cuenta_colgaap'               => '51053301',
															'cuenta_niif'                  => '51053301',
															'caracter'                     => 'debito',
															'centro_costos'                => 'true',
															'cuenta_contrapartida_colgaap' => '25150101',
															'cuenta_contrapartida_niif'    => '25150101',
															'caracter_contrapartida'       => 'credito',
															'centro_costos_contrapartida'  => 'false',
															'cuenta_colgaap_liquidacion'   => '25101001',
															'cuenta_niif_liquidacion'      => '25101001',
															'tercero_cruce_liquidacion'    => 'Empleado',
															'imprimir_volante'             => 'false',
															'carga_automatica'             => 'true',
															'nivel_formula'                => 6,
															'formula'                      => '[CS] *0.12',
															'nivel_formula_liquidacion'    => '2',
															'formula_liquidacion'          => '({BL} *{DL} )/3000',
															'tipo_concepto'                => 'General',
															'tercero'                      => 'Empleado',
															'tercero_cruce'                => 'Empleado',
															'resta_dias'                   => 'false'
															);
		$arrayConceptos['CARGA PRESTACIONAL']['VC']		= array(
															'descripcion'                  => 'VACACIONES',
															'naturaleza'                   => 'Provision',
															'cuenta_colgaap'               => '51053901',
															'cuenta_niif'                  => '51053901',
															'caracter'                     => 'debito',
															'centro_costos'                => 'true',
															'cuenta_contrapartida_colgaap' => '25250101',
															'cuenta_contrapartida_niif'    => '25250101',
															'caracter_contrapartida'       => 'credito',
															'cuenta_colgaap_liquidacion'   => '25101001',
															'cuenta_niif_liquidacion'      => '25101001',
															'tercero_cruce_liquidacion'    => 'Empleado',
															'centro_costos_contrapartida'  => 'false',
															'imprimir_volante'             => 'false',
															'carga_automatica'             => 'true',
															'nivel_formula'                => 3,
															'formula'                      => '({SC}*({DL}-|>DS<|-|>AP<|-|>PNR<| )+(([RN]+[CM])*30) )/720',
															'nivel_formula_liquidacion'    => '1',
															'formula_liquidacion'          => '{BL}*{DL}/720',
															'tipo_concepto'                => 'General',
															'tercero'                      => 'Empleado',
															'tercero_cruce'                => 'Empleado',
															'resta_dias'                   => 'false'
															);


		// ARRAY CON LOS CONCEPTOS
		// $arrayConceptos['GENERAL']['AT']            = array('descripcion' => 'AUXILIO DE TRANSPORTE', 		'naturaleza' => 'Devengo',  	'imprimir_volante' => 'true', 	'carga_automatica' => 'true',  	'nivel_formula' => '4', 'formula' => '((83140/30)*({DL}-(30*[DS] /{SC})-(30*[IM]  /{SC})-(30* [AP] /{SC})-(30*[LM]  /{SC})-(30*[PNR] /{SC})))',  				 				'tipo_concepto' => 'General',	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);
		// $arrayConceptos['GENERAL']['CM']            = array('descripcion' => 'COMISIONES', 					'naturaleza' => 'Devengo',  	'imprimir_volante' => 'true', 	'carga_automatica' => 'false', 	'nivel_formula' => '1', 'formula' => '',  				 				'tipo_concepto' => 'Personal',	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);
		// $arrayConceptos['GENERAL']['SB']            = array('descripcion' => 'SALARIO BASICO', 				'naturaleza' => 'Devengo',  	'imprimir_volante' => 'true', 	'carga_automatica' => 'true',  	'nivel_formula' => '1', 'formula' => '({SC} /30)*{DL}', 				'tipo_concepto' => 'Personal',	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);
		// $arrayConceptos['GENERAL']['PG']            = array('descripcion' => 'PRESTAMO GENERAL', 			'naturaleza' => 'Deduccion',	'imprimir_volante' => 'true', 	'carga_automatica' => 'false', 	'nivel_formula' => '1', 'formula' => '', 				 				'tipo_concepto' => 'General',	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);

		// $arrayConceptos['SEGURIDAD SOCIAL']['EPST'] = array('descripcion' => 'EPS EMPLEADO', 				'naturaleza' => 'Deduccion',   	'imprimir_volante' => 'true',  	'carga_automatica' => 'true',  	'nivel_formula' => '2', 'formula' => '(({SC} /30)*{DL})) *0.04',  		'tipo_concepto' => 'General', 	'tercero' => 'Empleado', 	'tercero_cruce' => 'Entidad',	);
		// $arrayConceptos['SEGURIDAD SOCIAL']['EPSE'] = array('descripcion' => 'EPS EMPLEADOR', 				'naturaleza' => 'Apropiacion', 	'imprimir_volante' => 'false', 	'carga_automatica' => 'true',  	'nivel_formula' => '2', 'formula' => '(({SC} /30)*{DL})) *0.085',  		'tipo_concepto' => 'General', 	'tercero' => 'Entidad',  	'tercero_cruce' => 'Entidad',	);
		// $arrayConceptos['SEGURIDAD SOCIAL']['PT']   = array('descripcion' => 'PENSION EMPLEADO',			'naturaleza' => 'Deduccion',   	'imprimir_volante' => 'true',  	'carga_automatica' => 'true',  	'nivel_formula' => '2', 'formula' => '(({SC} /30)*{DL})) *0.04',  		'tipo_concepto' => 'General', 	'tercero' => 'Empleado', 	'tercero_cruce' => 'Entidad',	);
		// $arrayConceptos['SEGURIDAD SOCIAL']['PE']   = array('descripcion' => 'PENSION EMPLEADOR',			'naturaleza' => 'Apropiacion', 	'imprimir_volante' => 'false', 	'carga_automatica' => 'true',  	'nivel_formula' => '2', 'formula' => '(({SC} /30)*{DL})) *0.12',  		'tipo_concepto' => 'General', 	'tercero' => 'Entidad',  	'tercero_cruce' => 'Entidad',	);
		// $arrayConceptos['SEGURIDAD SOCIAL']['ARL']  = array('descripcion' => 'ARL', 						'naturaleza' => 'Apropiacion', 	'imprimir_volante' => 'false', 	'carga_automatica' => 'true', 	'nivel_formula' => '1', 'formula' => '((({SC} /30)*{DL}))*{NRL})/100',  'tipo_concepto' => 'General', 	'tercero' => 'Entidad',  	'tercero_cruce' => 'Entidad',	);

		// $arrayConceptos['PARAFISCALES']['SENA']     = array('descripcion' => 'SENA',						'naturaleza' => 'Apropiacion',  'imprimir_volante' => 'false', 	'carga_automatica' => 'false', 	'nivel_formula' => '1', 'formula' => '',  								'tipo_concepto' => 'general',	'tercero' => 'Entidad', 	'tercero_cruce' => 'Entidad',	);
		// $arrayConceptos['PARAFISCALES']['ICBF']     = array('descripcion' => 'ICBF',						'naturaleza' => 'Apropiacion',  'imprimir_volante' => 'false', 	'carga_automatica' => 'false', 	'nivel_formula' => '1', 'formula' => '',  								'tipo_concepto' => 'general',	'tercero' => 'Entidad', 	'tercero_cruce' => 'Entidad',	);
		// $arrayConceptos['PARAFISCALES']['CDCF']     = array('descripcion' => 'CAJA DE COMPENSACION',		'naturaleza' => 'Apropiacion',  'imprimir_volante' => 'false', 	'carga_automatica' => 'true', 	'nivel_formula' => '1', 'formula' => '(({SC} /30)*{DL}))*0.04',  		'tipo_concepto' => 'general',	'tercero' => 'Entidad', 	'tercero_cruce' => 'Entidad',	);

		// $arrayConceptos['CARGA PRESTACIONAL']['CS']  = array('descripcion' => 'CESANTIAS',					'naturaleza' => 'Provision',  	'imprimir_volante' => 'false', 	'carga_automatica' => 'true', 	'nivel_formula' => '2', 'formula' => '(({SC}  +83140 )*{DL} )/360',  	'tipo_concepto' => 'General', 	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);
		// $arrayConceptos['CARGA PRESTACIONAL']['PS']  = array('descripcion' => 'PRIMA DE SERVICIOS',			'naturaleza' => 'Provision',  	'imprimir_volante' => 'false', 	'carga_automatica' => 'true', 	'nivel_formula' => '2', 'formula' => '(({SC} +83140)*{DL} )/360',  		'tipo_concepto' => 'General', 	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);
		// $arrayConceptos['CARGA PRESTACIONAL']['ISC'] = array('descripcion' => 'INTERESES SOBRE CESANTIAS',	'naturaleza' => 'Provision',  	'imprimir_volante' => 'false', 	'carga_automatica' => 'true', 	'nivel_formula' => '3', 'formula' => '([CS] *{DL} *0.12)/360',  		'tipo_concepto' => 'General', 	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);
		// $arrayConceptos['CARGA PRESTACIONAL']['VC']  = array('descripcion' => 'VACACIONES',					'naturaleza' => 'Provision',  	'imprimir_volante' => 'false', 	'carga_automatica' => 'true', 	'nivel_formula' => '2', 'formula' => '({SC}  *{DL} )/720',  			'tipo_concepto' => 'General', 	'tercero' => 'Empleado', 	'tercero_cruce' => 'Empleado',	);

		$valueInsert = '';

		// REOCORRER EL ARRAY PARA ARMAR EL INSERT DE LOS CONCEPTOS
		foreach ($arrayGruposConcepto as $descripcion_grupo => $id_grupo) {
			foreach ($arrayConceptos[$descripcion_grupo] as $codigo_concepto => $arrayResul) {
				$valueInsert .="(
									'$id_grupo',
									'$descripcion_grupo',
									'$codigo_concepto',
									'$arrayResul[descripcion]',
									'$arrayResul[naturaleza]',
									'$arrayResul[cuenta_colgaap]',
									'$arrayResul[cuenta_niif]',
									'$arrayResul[caracter]',
									'$arrayResul[centro_costos]',
									'$arrayResul[cuenta_contrapartida_colgaap]',
									'$arrayResul[cuenta_contrapartida_niif]',
									'$arrayResul[caracter_contrapartida]',
									'$arrayResul[centro_costos_contrapartida]',
									'$arrayResul[cuenta_colgaap_liquidacion]',
									'$arrayResul[cuenta_niif_liquidacion]',
									'$arrayResul[tercero_cruce_liquidacion]',
									'$arrayResul[imprimir_volante]',
									'$arrayResul[carga_automatica]',
									'$arrayResul[nivel_formula]',
									'$arrayResul[formula]',
									'$arrayResul[nivel_formula_liquidacion]',
									'$arrayResul[formula_liquidacion]',
									'$arrayResul[tipo_concepto]',
									'$arrayResul[tercero]',
									'$arrayResul[tercero_cruce]',
									'$arrayResul[resta_dias]',
									'$id_empresa'
								),";
			}

		}

		$valueInsert = substr($valueInsert, 0,-1);
		$sqlInsertConceptos="INSERT INTO nomina_conceptos (
															id_grupo,
															grupo,
															codigo,
															descripcion,
															naturaleza,
															cuenta_colgaap,
															cuenta_niif,
															caracter,
															centro_costos,
															cuenta_contrapartida_colgaap,
															cuenta_contrapartida_niif,
															caracter_contrapartida,
															centro_costos_contrapartida,
															cuenta_colgaap_liquidacion,
															cuenta_niif_liquidacion,
															tercero_cruce_liquidacion,
															imprimir_volante,
															carga_automatica,
															nivel_formula,
															formula,
															nivel_formula_liquidacion,
															formula_liquidacion,
															tipo_concepto,
															tercero,
															tercero_cruce,
															resta_dias,
															id_empresa
															)
							VALUES $valueInsert";
		$queryConceptos=mysql_query($sqlInsertConceptos,$link);

		if (!$queryConceptos) { deleteInfoEmpresa($link,$id_empresa,"<br>NO SE INSERTARON LOS DE CONCEPTOS PARA LA NOMINA<br/>".$sqlInsertConceptos,39); }

		// INSERTAR LOS CONCEPTOS PARA LAS BASES DE LIQUIDACION
		$sql="SELECT id,codigo
				FROM nomina_conceptos
				WHERE
				activo=1
				AND id_empresa=$id_empresa
				AND (codigo='AT'
					OR codigo='SB'
					OR codigo='HDFCD'
					OR codigo='HEDO'
					OR codigo='HENO'
					OR codigo='HEDF'
					OR codigo='HENF'
					OR codigo='HDFSD'
					OR codigo='RN'
					OR codigo='CM'
					OR codigo='CS'
					OR codigo='ISC'
					OR codigo='PS'
					OR codigo='VC'
					)";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)){
			$arrayConceptosId[$row['codigo']]=$row['id'];
		}

		$sql="INSERT INTO nomina_conceptos_base_liquidacion (id_concepto,id_concepto_base,id_empresa)
				VALUES
				($arrayConceptosId[CS],$arrayConceptosId[AT],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[SB],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[HDFCD],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[HEDO],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[HENO],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[HEDF],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[HENF],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[HDFSD],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[RN],$id_empresa),
				($arrayConceptosId[CS],$arrayConceptosId[CM],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[AT],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[SB],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[HDFCD],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[HEDO],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[HENO],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[HEDF],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[HENF],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[HDFSD],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[RN],$id_empresa),
				($arrayConceptosId[ISC],$arrayConceptosId[CM],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[AT],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[SB],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[HDFCD],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[HEDO],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[HENO],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[HEDF],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[HENF],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[HDFSD],$id_empresa),
				($arrayConceptosId[PS],$arrayConceptosId[RN],$id_empresa),
				($arrayConceptosId[VC],$arrayConceptosId[SB],$id_empresa)
				";
		$query=mysql_query($sql,$link);

		if (!$queryConceptos) { deleteInfoEmpresa($link,$id_empresa,"<br>NO SE INSERTARON LOS DE CONCEPTOS PARA LA BASE DE LA LIQUIDACION<br/>".$sqlInsertConceptos,43); }


 ?>