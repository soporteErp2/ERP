<?php
	error_reporting(E_ALL);
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");
	exit;
	// header('Content-type: application/vnd.ms-excel');
	// header("Content-Disposition: attachment; filename=nomina.xls");
	// header("Pragma: no-cache");
	// header("Expires: 0");

	$id_empresa = 47;

	$sql="SELECT
				NP.consecutivo,
				NPE.documento_empleado,
				NPE.nombre_empleado,
				NPEC.codigo_concepto,
				NPEC.concepto,
				NPEC.centro_costos,
				NPEC.id_centro_costos,
				NPEC.centro_costos_contrapartida,
				NPEC.id_centro_costos_contrapartida,
				EC.id_centro_costos AS id_ccos_empleado,
				EC.codigo_centro_costos AS codigo_ccos_empleado,
				EC.nombre_centro_costos AS ccos_empleado
			FROM nomina_planillas AS NP INNER JOIN nomina_planillas_empleados AS NPE ON NPE.id_planilla = NP.id
				INNER JOIN nomina_planillas_empleados_conceptos AS NPEC ON NPEC.id_planilla = NP.id INNER JOIN empleados_contratos AS EC ON EC.id=NPE.id_contrato
			WHERE
				NP.activo=1
			AND NP.id_empresa=47
			AND (NP.estado=1 OR NP.estado=2)
			AND NP.fecha_inicio>='2017-01-01'
			AND (NPEC.centro_costos='true' OR NPEC.centro_costos_contrapartida='true')
	 		AND (NPEC.id_centro_costos=0 OR ISNULL(NPEC.id_centro_costos) OR NPEC.centro_costos_contrapartida=0 OR ISNULL(NPEC.centro_costos_contrapartida) )
			ORDER BY NP.consecutivo DESC ";
	$query=$mysql->query($sql,$mysql->link);

	// $sql="SELECT
	// 			NPE.documento_empleado,
	// 			NPE.nombre_empleado,
	// 			NPEC.centro_costos,
	// 			NPEC.id_centro_costos,
	// 			NPEC.centro_costos_contrapartida,
	// 			NPEC.id_centro_costos_contrapartida
	// 		FROM nomina_planillas_empleados_conceptos AS NPEC, nomina_planillas_empleados AS NPE, nomina_planillas AS NP
	// 		WHERE NPE.id_planilla=NPEC.id_planilla AND
	// 		AND NPEC.activo=1
	// 		AND NPEC.id_empresa=$id_empresa
	// 		AND (NPEC.centro_costos='true' OR NPEC.centro_costos_contrapartida='true')
	// 		AND (NPEC.id_centro_costos=0 OR ISNULL(NPEC.id_centro_costos) OR NPEC.centro_costos_contrapartida=0 OR ISNULL(NPEC.centro_costos_contrapartida) )
	// 		";
	// $query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {

	}

?>