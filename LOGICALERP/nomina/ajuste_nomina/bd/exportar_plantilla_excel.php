<?php
	error_reporting(E_ALL);
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	include('../../../../misc/excel/Classes/PHPExcel.php');

	// header('Content-type: application/vnd.ms-excel');
	// header('Content-type: application/x-msexcel');
	// header("Content-Disposition: attachment; filename=plantilla_ajuste_".date("Y_m_d").".xls");
	// header("Pragma: no-cache");
	// header("Expires: 0");
	header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
	header('Content-Disposition: attachment;filename="plantilla_ajuste_'.date("Y_m_d").'.xls"');
	header('Cache-Control: max-age=0');

	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()
		->setCreator("LogicalSoft.com")
		->setLastModifiedBy("LogicalSoft.com")
		->setTitle("Plantilla Ajuste Nomina")
		->setSubject("Formato Excel")
		->setDescription("Plantilla para cargar los ajustes de seguridad social")
		->setKeywords("plantilla ajuste")
		->setCategory("plantilla");

	$objPHPExcel->getActiveSheet()
					->setTitle('Plantilla Ajuste de Nomina')
					->getProtection()->setSheet (true);


	$id_empresa  = $_SESSION['EMPRESA'];

	// CONSULTAR LAS FECHAS DE LA PLANILLA DE AJUSTE
	$sql   = "SELECT fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas_ajuste WHERE activo=1 ANd id_empresa=$id_empresa AND id=$id_planilla";
	$query = $mysql->query($sql,$mysql->link);

	$fecha_inicio = $mysql->result($query,0,'fecha_inicio');
	$fecha_final  = $mysql->result($query,0,'fecha_final');
	$id_sucursal  = $mysql->result($query,0,'id_sucursal');

	// CONSULTAR LAS PLANILLAS DE NOMINA DE ESE PERIODO CON SUS EMPLEADOS
	$sql   = "SELECT
					NP.id,
					NPE.documento_empleado,
					NPE.nombre_empleado,
					NPE.id_empleado
				FROM
					nomina_planillas AS NP,
					nomina_planillas_empleados AS NPE
				WHERE
					NP.activo = 1
				AND (NP.estado=1 OR NP.estado=2)
				AND NP.id_empresa = $id_empresa
				AND NP.id_sucursal = $id_sucursal
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final <= '$fecha_final'
				AND NPE.id_planilla = NP.id
				GROUP BY NP.id,NPE.id_empleado
				ORDER BY NPE.documento_empleado ASC";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$array_id_planillas[$row['id']] = $row['id'];
		$array_empleados[$row['id_empleado']] = array('documento_empleado' => $row['documento_empleado'], 'nombre_empleado'=>$row['nombre_empleado']);
	}

	$whereIdPlanillas='';
	// CREAR WHERE DEL ID PLANILLA
	foreach ($array_id_planillas as $id_planilla => $valor) {
		$whereIdPlanillas .= ($whereIdPlanillas=='')? 'id_planilla='.$id_planilla : ' OR id_planilla='.$id_planilla ;
	}

	$sql="SELECT
			NPEC.id_concepto,
			NPEC.codigo_concepto,
			NPEC.concepto,
			SUM(NPEC.valor_concepto) AS valor_provisionado,
			NPEC.naturaleza,
			NPEC.id_prestamo,
			NC.concepto_ajustable,
			NC.cuenta_colgaap,
			NC.caracter,
			NC.cuenta_contrapartida_colgaap,
			NC.caracter_contrapartida

		FROM
			nomina_planillas_empleados_conceptos AS NPEC,
			nomina_conceptos AS NC
		WHERE
			NPEC.activo = 1
		AND NPEC.id_empresa = $id_empresa
		AND NC.concepto_ajustable = 'true'
		AND NC.id=NPEC.id_concepto
		AND ($whereIdPlanillas)
		GROUP BY
			NPEC.id_concepto";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		// echo $row['caracter'];
		if ($row['caracter']=='credito') {
			$array_cuentas[$row['cuenta_colgaap']]=$row['concepto'];
		}
		if ($row['caracter_contrapartida']=='credito') {
			$array_cuentas[$row['cuenta_contrapartida_colgaap']]=$row['concepto'];
		}

	}

	$arrayAbecedario = array(
							'0' => 'A',
							'1' => 'B',
							'2' => 'C',
							'3' => 'D',
							'4' => 'E',
							'5' => 'F',
							'6' => 'G',
							'7' => 'H',
							'8' => 'I',
							'9' => 'J',
							'10' => 'K',
							'11' => 'L',
							'12' => 'M',
							'13' => 'N',
							'14' => 'O',
							'15' => 'P',
							'16' => 'Q',
							'17' => 'R',
							'18' => 'S',
							'19' => 'T',
							'20' => 'U',
							'21' => 'V',
							'22' => 'W',
							'23' => 'X',
							'24' => 'Y',
							'25' => 'Z', );



	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A1', 'DOCUMENTO');
    $objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('B1', 'EMPLEADO');

	// ANCHO AUTOMATICO DE LA CELDA
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

	$i=2;
	foreach ($array_cuentas as $cuenta => $concepto) {

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue($arrayAbecedario[$i].'1', $concepto.'_'.$cuenta);

		// ANCHO AUTOMATICO DE LA CELDA
		$objPHPExcel->getActiveSheet()->getColumnDimension($arrayAbecedario[$i])->setAutoSize(true);
		$i++;

		// $head.='<td>'.$concepto.'_'.$cuenta.'</td>';
	}

	$i=2;
	$j=2;
	foreach ($array_empleados as $id_empleado => $arrayResul) {
		$documento = '';
		$nombre    = ($array_empleados[$id_empleado]['nombre_empleado']=='' || is_null($array_empleados[$id_empleado]['nombre_empleado']))? 'text' : $array_empleados[$id_empleado]['nombre_empleado'];

		$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A".$i, $array_empleados[$id_empleado]['documento_empleado'])
						->setCellValue("B".$i, utf8_encode($nombre));

			// DESBLOQUEAR LAS CELDAS QUE SWE DEBEN DIGITAR
			foreach ($array_cuentas as $cuenta => $concepto) {
				$objPHPExcel->getActiveSheet()->getStyle($arrayAbecedario[$j].$i)
	    						->getProtection()
	    						->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
				$j++;
			}
		$j=2;
		$i++;

		// $body.='<tr>
		// 			<td>'.$arrayResul['documento_empleado'].'</td>
		// 			<td>'.$arrayResul['nombre_empleado'].'</td>
		// 		</tr>';
	}

	// echo '<table>
	// 		<tr>
	// 			<td>DOCUMENTO</td>
	// 			<td>EMPLEADO</td>
	// 			'.$head.'
	// 		</tr>
	// 		'.$body.'
	// 	</table>';

	$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
	$objWriter->save('php://output');
	exit;

?>