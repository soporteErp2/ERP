<?php
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");

	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=informe_ajuste_nomina_".date("Y_m_d").".xls");
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
	NPEC.id AS id_fila,
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
    NPEC.id_tercero,
	(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero) AS documento_tercero,
	(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero) AS tercero,
	NPEC.cuenta_contrapartida_colgaap,
    NPEC.caracter_contrapartida,
    NPEC.id_tercero_contrapartida,
	(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_contrapartida) AS documento_tercero_contrapartida,
	(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_contrapartida) AS tercero_contrapartida,
	NPEC.id_tercero_ajuste,
	(SELECT numero_identificacion FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_ajuste) AS documento_tercero_ajuste,
	(SELECT nombre FROM terceros WHERE activo=1 AND id=NPEC.id_tercero_ajuste) AS tercero_ajuste,
	NPEC.id_centro_costos,
	NPEC.codigo_centro_costos,
	NPEC.id_centro_costos_contrapartida,
	NPEC.codigo_centro_costos_contrapartida,
	NPEC.centro_costos_contrapartida,
	NPEC.id_centro_costos_ajuste,
	NPEC.codigo_centro_costos_ajuste,
   'LE' AS tipo_planilla,
   'Planilla de Liquidacion' AS descripcion_tipo_planilla,
    NPEC.valor_concepto_ajustado,
	SUM(NPEC.valor_concepto-NPEC.valor_concepto_ajustado) AS diferencia_ajuste,
    NPEC.cuenta_colgaap_ajuste,
	NPEC.descripcion_cuenta_colgaap_ajuste
FROM
	nomina_planillas_ajuste AS NP,
	nomina_planillas_ajuste_empleados_conceptos AS NPEC
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

	$id_empleado                  = $row['id_empleado'];
	$id_concepto                  = $row['id_concepto'];
	$id_tercero                   = $row['id_tercero'];
	$id_tercero_contrapartida     = $row['id_tercero_contrapartida'];
	$id_empleado_cruce            = $row['id_empleado_cruce'];
	$cuenta_colgaap               = $row['cuenta_colgaap'];
	$cuenta_contrapartida_colgaap = $row['cuenta_contrapartida_colgaap'];
	$cuenta_niif                  = $row['cuenta_niif'];
	$cuenta_contrapartida_niif    = $row['cuenta_contrapartida_niif'];
	$id_tercero_ajuste            = $row['id_tercero_ajuste'];
	$cuenta_colgaap_ajuste        = $row['cuenta_colgaap_ajuste'];
	$cuenta_niif_ajuste           = $row['cuenta_niif_ajuste'];
	$valor_concepto               = $row['valor_concepto'];
	$valor_concepto_ajustado      = $row['valor_concepto_ajustado'];

	$centro_costos                = $row['id_centro_costos'];
	$centro_costos_contrapartida  = $row['id_centro_costos_contrapartida'];
	$centro_costos_ajuste         = $row['id_centro_costos_ajuste'];

	$caracter                     = $row['caracter'];
	$caracter_contrapartida       = $row['caracter_contrapartida'];
	$naturaleza                   = $row['naturaleza'];

	$id_prestamo                  = $row['id_prestamo'];
	$tercero                      = $row['tercero'];
	$tercero_cruce                = $row['tercero_cruce'];
	$concepto                     = $row['concepto'];
	//$centro_costos                = $row['centro_costos'];
	//$centro_costos_contrapartida  = $row['centro_costos_contrapartida'];
	$id_planilla                  = $row['id'];
	$id_fila                      = $row['id_fila'];


	// SI EL CONCEPTO ES UNA APROPIACION
	if ($naturaleza=='Apropiacion') {
		$ccos_debito            = ($caracter=='debito')? $centro_costos : $centro_costos_contrapartida ;
		$tercero_debito         = ($caracter=='debito')? $id_tercero : $id_tercero_contrapartida ;
		$cuenta_colgaap_debito  = ($caracter=='debito')? $cuenta_colgaap : $cuenta_contrapartida_colgaap ;
		$cuenta_niif_debito     = ($caracter=='debito')? $cuenta_niif : $cuenta_contrapartida_niif ;

		$ccos_credito           = ($caracter=='credito')? $centro_costos : $centro_costos_contrapartida ;
		$tercero_credito        = ($caracter=='credito')? $id_tercero : $id_tercero_contrapartida ;
		$cuenta_colgaap_credito = ($caracter=='credito')? $cuenta_colgaap : $cuenta_contrapartida_colgaap ;
		$cuenta_niif_credito    = ($caracter=='credito')? $cuenta_niif : $cuenta_contrapartida_niif ;

	}
	// SI EL CONCEPTO NO ES UNA APROPIACION TOMAR LA CUENTA DE AJUSTE CONFIGURADA EN EL CONCEPTO
	else{
		$ccos_debito           = $centro_costos_ajuste;
		$tercero_debito        = $id_tercero_ajuste;
		$cuenta_colgaap_debito = $cuenta_colgaap_ajuste;
		$cuenta_niif_debito    = $cuenta_niif_ajuste;

		$ccos_credito           = ($caracter=='credito')? $centro_costos : $centro_costos_contrapartida;
		$tercero_credito        = ($caracter=='credito')? $id_tercero : $id_tercero_contrapartida;
		$cuenta_colgaap_credito = ($caracter=='credito')? $cuenta_colgaap : $cuenta_contrapartida_colgaap;
		$cuenta_niif_credito    = ($caracter=='credito')? $cuenta_niif : $cuenta_contrapartida_niif;

	}

	$diferencia=$valor_concepto-$valor_concepto_ajustado;
	$ajuste=abs($diferencia);

	// SI LA DIFERENCIA ES MENOR, SACAR DE LA CXP Y DEVOLVER AL GASTO
	if ($diferencia<0) {
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['id_empleado']     =  $id_empleado;

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['codigo_concepto'] =  $row['codigo_concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['concepto']        =  $row['concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['naturaleza']      =  $row['naturaleza'];

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['ccos']            =  $ccos_debito;
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_credito][$cuenta_colgaap_credito]['ccos']          =  $ccos_credito;

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['id_empleado']     =  $id_empleado;
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['codigo_concepto'] =  $row['codigo_concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['concepto']        =  $row['concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['naturaleza']      =  $row['naturaleza'];

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['debito']          += $ajuste;
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_credito][$cuenta_colgaap_credito]['credito']       += $ajuste;

	}
	// SI LA DIFERENCIA ES MAYOR, SACAR DE GASTO A CXP
	else if($diferencia>0){
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['id_empleado']     =  $id_empleado;

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['codigo_concepto'] =  $row['codigo_concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['concepto']        =  $row['concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['naturaleza']      =  $row['naturaleza'];

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['ccos']            =  $ccos_debito;
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_credito][$cuenta_colgaap_credito]['ccos']          =  $ccos_credito;

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['id_empleado']     =  $id_empleado;
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['codigo_concepto'] =  $row['codigo_concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['concepto']        =  $row['concepto'];
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_credito]['naturaleza']      =  $row['naturaleza'];

		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_debito][$cuenta_colgaap_debito]['credito']         += $ajuste;
		$arrayAsientosColgaap[$id_planilla][$id_fila][$tercero_credito][$cuenta_colgaap_credito]['debito']        += $ajuste;

	}



	// ARRAY CON LA INFORMACION DEL CONCEPTO
	$arrayInfoConcepto[$tercero_debito][$cuenta_colgaap_debito]= array(
																		'concepto'           => $row['concepto'],
																		'naturaleza'         => $row['naturaleza'],
																		'concepto_ajustable' => $row['concepto_ajustable'],
																		'cuenta_niif'        => $cuenta_niif_credito,
																		'id_concepto'        => $row['id_concepto']
																		);

	$arrayInfoConcepto[$tercero_credito][$cuenta_colgaap_credito]= array(
																		'concepto'           => $row['concepto'],
																		'naturaleza'         => $row['naturaleza'],
																		'concepto_ajustable' => $row['concepto_ajustable'],
																		'cuenta_niif'        => $cuenta_niif_credito,
																		'id_concepto'        => $row['id_concepto']
																		);

	$arrayInfoPlanilla[$row['id']] = array(
											'consecutivo'     => $row['consecutivo'],
											'sucursal'     => $row['sucursal'],
											'fecha_documento' => $row['fecha_documento'],
											'fecha_inicio'    => $row['fecha_inicio'],
											'fecha_final'     => $row['fecha_final'],
										);

	$arrayInfoEmpleado[$row['id_empleado']]  = array('documento_empleado' => $row['documento_empleado'] ,
														'nombre_empleado' => $row['nombre_empleado']  );



	$arrayInfotercero[$row['id_tercero']]  = array('documento_tercero' => $row['documento_tercero'], 'tercero' => $row['tercero']);
	$arrayInfotercero[$row['id_empleado']]  = array('documento_tercero' => $row['documento_empleado'], 'tercero' => $row['nombre_empleado']);
	$arrayInfotercero[$row['id_tercero_ajuste']]  = array('documento_tercero' => $row['documento_tercero_ajuste'], 'tercero' => $row['tercero_ajuste']);
	$arrayInfotercero[$row['id_tercero_contrapartida']]  = array('documento_tercero' => $row['documento_tercero_contrapartida'],
																'tercero' => $row['tercero_contrapartida']);
	$arrayCcos[$row['id_centro_costos']]               = $row['codigo_centro_costos'];
	$arrayCcos[$row['id_centro_costos_contrapartida']] = $row['codigo_centro_costos_contrapartida'];
	$arrayCcos[$row['id_centro_costos_ajuste']]        = $row['codigo_centro_costos_ajuste'];


}

foreach ($arrayAsientosColgaap as $id_planilla => $arrayAsientosColgaap1) {
	foreach ($arrayAsientosColgaap1 as $id_fila => $arrayAsientosColgaap2) {
		foreach ($arrayAsientosColgaap2 as $id_tercero => $arrayResul) {

    		foreach ($arrayResul as $cuenta => $arrayResul1) {
    			$id_empleado=$arrayResul1['id_empleado'];
    			$id_centro_costos=$arrayResul1['ccos'];
				$acumDebito  += $arrayResul1['debito'];
				$acumCredito += $arrayResul1['credito'];

    			if ($arrayResul1['debito']>$arrayResul1['credito']) {
					$debito  = $arrayResul1['debito']-$arrayResul1['credito'];
					$credito = 0;
    			}
    			else{
					$debito  = 0;
					$credito = $arrayResul1['credito']-$arrayResul1['debito'];
    			}

    			if ($debito==0 && $credito==0) { continue; }

				$valueInsertAsientos .= "('$id_planilla',
										'$consecutivo',
										'PA',
										'$id_planilla',
										'$consecutivo',
										'PA',
										'Planilla Ajuste Nomina',
										'".$fecha_documento."',
										'".$debito."',
										'".$credito."',
										'".$cuenta."',
										'".$id_tercero."',
										'$id_centro_costos',
										'$id_sucursal',
										'$id_empresa'),";

				$tbody.='<tr>
			 		<td>'.$arrayInfoPlanilla[$id_planilla]['consecutivo'].'</td>
					<td>'.$arrayInfoPlanilla[$id_planilla]['sucursal'].'</td>
					<td>'.$arrayInfoPlanilla[$id_planilla]['fecha_documento'].'</td>
					<td>'.$arrayInfoPlanilla[$id_planilla]['fecha_inicio'].'</td>
					<td>'.$arrayInfoPlanilla[$id_planilla]['fecha_final'].'</td>
			 		<td>'.$arrayInfoEmpleado[$id_empleado]['documento_empleado'].'</td>
			 		<td>'.$arrayInfoEmpleado[$id_empleado]['nombre_empleado'].'</td>
			 		<td>'.$arrayResul1['codigo_concepto'].'</td>
					<td>'.$arrayResul1['concepto'].'</td>
					<td>'.$arrayResul1['naturaleza'].'</td>
					<td>'.$debito.'</td>
					<td>'.$credito.'</td>
					<td>'.$cuenta.'</td>
					<td>'.$arrayInfotercero[$id_tercero]['documento_tercero'].'</td>
					<td>'.$arrayInfotercero[$id_tercero]['tercero'].'</td>
					<td>'.$arrayCcos[$id_centro_costos].'</td>
			 	</tr>';


    		}
    	}
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
 	</tr>
 	<?php echo $tbody; ?>
 </table>

 </body>
 </html>