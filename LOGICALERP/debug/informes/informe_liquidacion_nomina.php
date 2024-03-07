<?php
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");

	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=informe_liquidacion_nomina_".date("Y_m_d").".xls");
	header("Pragma: no-cache");
	header("Expires: 0");


foreach ($arrayId as $id_planilla => $value) {
	$whereIdPlanilla.=($whereIdPlanilla=='')? 'id_planilla='.$id_planilla
											: ' OR id_planilla='.$id_planilla ;
}

$sql="SELECT valor_deducir,cuenta_colgaap,id_empleado,id_concepto,id_concepto_deducir
		FROM nomina_planillas_liquidacion_conceptos_deducir
		WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPlanilla)";
$query=mysql_query($sql,$link);

$sql="SELECT
NP.id,
NPEC.id_empleado,
NPEC.id_concepto,
	NP.consecutivo,
	NP.sucursal,
	NP.fecha_documento,
	NP.fecha_inicio,
	NP.fecha_final,
  (SELECT documento FROM empleados WHERE activo=1 AND id=NPEC.id_empleado) AS documento_empleado,
  (SELECT nombre FROM empleados WHERE activo=1 AND id=NPEC.id_empleado) AS nombre_empleado,
	NPEC.codigo_concepto,
	NPEC.concepto,
	NPEC.naturaleza,
	NPEC.valor_campo_texto,
	NPEC.valor_concepto,

	NPEC.cuenta_colgaap,
  NPEC.caracter,
	(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero) AS documento_tercero,
	(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero) AS tercero,
	NPEC.cuenta_contrapartida_colgaap,
  NPEC.caracter_contrapartida,
	(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_contrapartida) AS documento_tercero_contrapartida,
	(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_contrapartida) AS tercero_contrapartida,
	NPEC.codigo_centro_costos,
	NPEC.codigo_centro_costos_contrapartida,
	NPEC.centro_costos_contrapartida,
  'LE' AS tipo_planilla,
  'Planilla de Liquidacion' AS descripcion_tipo_planilla,
  NPEC.valor_concepto_ajustado,
	SUM(NPEC.valor_concepto-NPEC.valor_concepto_ajustado) AS diferencia_ajuste,
  NPEC.cuenta_colgaap_liquidacion,
  NPEC.descripcion_cuenta_colgaap_liquidacion

FROM
	nomina_planillas_liquidacion AS NP,
	nomina_planillas_liquidacion_empleados_conceptos AS NPEC
WHERE
	NP.id_empresa = 1
AND NPEC.id_empresa = 1
AND NP.activo = 1
AND NPEC.activo = 1
AND NPEC.id_planilla = NP.id
GROUP BY NPEC.id
#LIMIT 0,100";
$query=mysql_query($sql,$link);

while ($row=mysql_fetch_array($query)) {
	$arrayId[$row['id']]=$row['id'];
	if ($row['caracter']=='credito') {
		$cuenta            =$row['cuenta_colgaap'];
		$cod_ccos          =$row['codigo_centro_costos'];
		$ccos              =$row['centro_costos'];
		$documento_tercero =$row['documento_tercero'];
		$tercero           =$row['tercero'];
	}
	else if ($row['caracter_contrapartida']=='credito') {
		$cuenta            =$row['cuenta_contrapartida_colgaap'];
		$cod_ccos          =$row['codigo_centro_costos_contrapartida'];
		$ccos              =$row['centro_costos_contrapartida'];
		$documento_tercero =$row['documento_tercero_contrapartida'];
		$tercero           =$row['tercero_contrapartida'];

	}

	$tbody.='<tr>
		 		<td>'.$row['consecutivo'].'</td>
				<td>'.$row['sucursal'].'</td>
				<td>'.$row['fecha_documento'].'</td>
				<td>'.$row['fecha_inicio'].'</td>
				<td>'.$row['fecha_final'].'</td>
		 		<td>'.$row['documento_empleado'].'</td>
		 		<td>'.$row['nombre_empleado'].'</td>
		 		<td>'.$row['codigo_concepto'].'</td>
				<td>'.$row['concepto'].'</td>
				<td>'.$row['naturaleza'].'</td>
				<td>0</td>
				<td>'.$row['valor_concepto'].'</td>
				<td>'.$cuenta.'</td>
				<td>'.$documento_tercero.'</td>
				<td>'.$tercero.'</td>
				<td>'.$cod_ccos.'</td>
				<td>'.$ccos.'</td>
		 	</tr>';

 	if ($row['diferencia_ajuste']<>0 && $row['naturaleza']=='Provision') {

		if ($row['caracter']=='debito') {
			$cuenta            =$row['cuenta_colgaap'];
			$cod_ccos          =$row['codigo_centro_costos'];
			$ccos              =$row['centro_costos'];
			$documento_tercero =$row['documento_tercero'];
			$tercero           =$row['tercero'];
		}
		else if ($row['caracter_contrapartida']=='debito') {
			$cuenta            =$row['cuenta_contrapartida_colgaap'];
			$cod_ccos          =$row['codigo_centro_costos_contrapartida'];
			$ccos              =$row['centro_costos_contrapartida'];
			$documento_tercero =$row['documento_tercero_contrapartida'];
			$tercero           =$row['tercero_contrapartida'];

		}

		if ($row['diferencia_ajuste']>0) {
			$debito = abs($row['diferencia_ajuste']);
			$credito = 0;
		}
		else{
			$debito = 0;
			$credito = abs($row['diferencia_ajuste']);
		}

		$tbody.='<tr>
		 		<td>'.$row['consecutivo'].'</td>
				<td>'.$row['sucursal'].'</td>
				<td>'.$row['fecha_documento'].'</td>
				<td>'.$row['fecha_inicio'].'</td>
				<td>'.$row['fecha_final'].'</td>
		 		<td>'.$row['documento_empleado'].'</td>
		 		<td>'.$row['nombre_empleado'].'</td>
		 		<td>'.$row['codigo_concepto'].'</td>
				<td>'.$row['concepto'].'</td>
				<td>'.$row['naturaleza'].'</td>
				<td>'.$debito.'</td>
				<td>'.$credito.'</td>
				<td>'.$cuenta.'</td>
				<td>'.$documento_tercero.'</td>
				<td>'.$tercero.'</td>
				<td>'.$cod_ccos.'</td>
				<td>'.$ccos.'</td>
		 	</tr>';
	}

	if ($row['naturaleza']=='Deduccion') {
		if ($row['caracter']=='debito') {
			$cuenta            =$row['cuenta_colgaap_liquidacion'];
			$cod_ccos          =$row['codigo_centro_costos'];
			$ccos              =$row['centro_costos'];
			$documento_tercero =$row['documento_tercero'];
			$tercero           =$row['tercero'];
		}
		else if ($row['caracter_contrapartida']=='debito') {
			$cuenta            =$row['cuenta_colgaap_liquidacion'];
			$cod_ccos          =$row['codigo_centro_costos_contrapartida'];
			$ccos              =$row['centro_costos_contrapartida'];
			$documento_tercero =$row['documento_tercero_contrapartida'];
			$tercero           =$row['tercero_contrapartida'];

		}
		$sqlCon="SELECT valor_deducir,cuenta_colgaap,id_empleado,id_concepto,id_concepto_deducir
					FROM nomina_planillas_liquidacion_conceptos_deducir
					WHERE activo=1
					AND id_empresa=1
					AND id_planilla=$row[id]
					AND id_empleado=$row[id_empleado]
					AND id_concepto=$row[id_concepto]";
		$queryCon=mysql_query($sqlCon,$link);
		while ($rowCon=mysql_fetch_array($queryCon)) {
			$tbody.='<tr>
			 		<td>'.$row['consecutivo'].'</td>
					<td>'.$row['sucursal'].'</td>
					<td>'.$row['fecha_documento'].'</td>
					<td>'.$row['fecha_inicio'].'</td>
					<td>'.$row['fecha_final'].'</td>
			 		<td>'.$row['documento_empleado'].'</td>
			 		<td>'.$row['nombre_empleado'].'</td>
			 		<td>'.$row['codigo_concepto'].'</td>
					<td>'.$row['concepto'].'</td>
					<td>'.$row['naturaleza'].'</td>
					<td>'.$rowCon['valor_deducir'].'</td>
					<td>0</td>
					<td>'.$rowCon['cuenta_colgaap'].'</td>
					<td>'.$documento_tercero.'</td>
					<td>'.$tercero.'</td>
					<td>'.$cod_ccos.'</td>
					<td>'.$ccos.'</td>
			 	</tr>';
		}

		// $tbody.='<tr>
		// 	 		<td>'.$row['consecutivo'].'</td>
		// 			<td>'.$sqlCon.'</td>
		// 			<td>'.$row['fecha_documento'].'</td>
		// 			<td>'.$row['fecha_inicio'].'</td>
		// 			<td>'.$row['fecha_final'].'</td>
		// 	 		<td>'.$row['documento_empleado'].'</td>
		// 	 		<td>'.$row['nombre_empleado'].'</td>
		// 	 		<td>'.$row['codigo_concepto'].'</td>
		// 			<td>'.$row['concepto'].'</td>
		// 			<td>'.$row['naturaleza'].'</td>
		// 			<td>'.$rowCon['valor_deducir'].'-----</td>
		// 			<td>0</td>
		// 			<td>'.$rowCon['cuenta_colgaap'].'</td>
		// 			<td>'.$documento_tercero.'</td>
		// 			<td>'.$tercero.'</td>
		// 			<td>'.$cod_ccos.'</td>
		// 			<td>'.$ccos.'</td>
		// 	 	</tr>';

	}
	else{

		if ($row['caracter']=='debito') {
			$cuenta            =$row['cuenta_colgaap_liquidacion'];
			$cod_ccos          =$row['codigo_centro_costos'];
			$ccos              =$row['centro_costos'];
			$documento_tercero =$row['documento_tercero'];
			$tercero           =$row['tercero'];
		}
		else if ($row['caracter_contrapartida']=='debito') {
			$cuenta            =$row['cuenta_colgaap_liquidacion'];
			$cod_ccos          =$row['codigo_centro_costos_contrapartida'];
			$ccos              =$row['centro_costos_contrapartida'];
			$documento_tercero =$row['documento_tercero_contrapartida'];
			$tercero           =$row['tercero_contrapartida'];

		}

		$tbody.='<tr>
			 		<td>'.$row['consecutivo'].'</td>
					<td>'.$row['sucursal'].'</td>
					<td>'.$row['fecha_documento'].'</td>
					<td>'.$row['fecha_inicio'].'</td>
					<td>'.$row['fecha_final'].'</td>
			 		<td>'.$row['documento_empleado'].'</td>
			 		<td>'.$row['nombre_empleado'].'</td>
			 		<td>'.$row['codigo_concepto'].'</td>
					<td>'.$row['concepto'].'</td>
					<td>'.$row['naturaleza'].'</td>
					<td>'.$row['valor_concepto_ajustado'].'</td>
					<td>0</td>
					<td>'.$cuenta.'</td>
					<td>'.$documento_tercero.'</td>
					<td>'.$tercero.'</td>
					<td>'.$cod_ccos.'</td>
					<td>'.$ccos.'</td>
			 	</tr>';
	}

}




 ?>
 <html>
 <head>
 	<title></title>
 </head>
 <body>

 <table>
 	<tr>
 		<td>consecutivo</td>
		<td>sucursal</td>
		<td>fecha_documento</td>
		<td>fecha_inicio</td>
		<td>fecha_final</td>
 		<td>documento_empleado</td>
 		<td>nombre_empleado</td>
 		<td>codigo_concepto</td>
		<td>concepto</td>
		<td>naturaleza</td>
		<td>debito</td>
		<td>credito</td>
		<td>cuenta_colgaap</td>
		<td>documento_tercero</td>
		<td>tercero</td>
		<td>codigo_centro_costos</td>
		<td>codigo_centro_costos_contrapartida</td>
 	</tr>
 	<?php echo $tbody; ?>
 </table>

 </body>
 </html>