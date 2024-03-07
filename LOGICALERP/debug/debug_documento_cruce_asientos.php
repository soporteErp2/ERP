<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sql="SELECT
				*
			FROM
				asientos_colgaap
			WHERE
				activo = 1
			AND tipo_documento <> tipo_documento_cruce
			AND consecutivo_documento = numero_documento_cruce";
	$query=mysql_query($sql,$link);

	while ($row=mysql_fetch_array($query)) {

		$arrayAsientos[$row['id']][$row['tipo_documento_cruce']][$row['id_documento_cruce']] = array(
																										'consecutivo_documento'  => $row['consecutivo_documento'],
																										'tipo_documento'         => $row['tipo_documento'],
																										'tipo_documento_cruce'   => $row['tipo_documento_cruce'],
																										'numero_documento_cruce' => $row['numero_documento_cruce'],
																									);

		if ($row['tipo_documento_cruce']=='CE') {
			$whereIdCE.=($whereIdCE=='')? 'id='.$row['id_documento_cruce'] : ' OR id='.$row['id_documento_cruce'] ;

		}
		else if ($row['tipo_documento_cruce']=='FC') {
			$whereIdFC.=($whereIdFC=='')? 'id='.$row['id_documento_cruce'] : ' OR id='.$row['id_documento_cruce'] ;
		}
		else if ($row['tipo_documento_cruce']=='FV') {
			$whereIdFV.=($whereIdFV=='')? 'id='.$row['id_documento_cruce'] : ' OR id='.$row['id_documento_cruce'] ;
		}
		// echo 'consecutivo: '.$row['consecutivo_documento'].' tipo: '.$row['tipo_documento'].
		// ' cruce: '.$row['tipo_documento_cruce'].' numero: '.$row['numero_documento_cruce'].'<br>';
	}

	$sql="SELECT * FROM comprobante_egreso where activo=1 AND ($whereIdCE)";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$arrayConsecutivos['CE'][$row['id']]=$row['consecutivo'];
		// echo $row['consecutivo'].' '.$row['sucursal'].'<br>';
	}

	$sql="SELECT * FROM compras_facturas WHERE activo=1 AND ($whereIdFC)";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$consecutivo = ($row['prefijo_factura']=='')? $row['numero_factura'] : $row['prefijo_factura'].' '.$row['numero_factura'] ;
		$arrayConsecutivos['FC'][$row['id']]=$consecutivo;
		// echo $row['prefijo_factura'].'-'.$row['numero_factura'].' '.$row['sucursal'].'<br>';
	}

	$sql="SELECT * FROM ventas_facturas WHERE activo=1 AND ($whereIdFV)";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$arrayConsecutivos['FV'][$row['id']]=$row['numero_factura_completo'];
		// echo $row['numero_factura_completo'].' '.$row['sucursal'].'<br>';
	}

	foreach ($arrayAsientos as $id_asiento => $arrayAsientosArray) {
		foreach ($arrayAsientosArray as $tipo_documento => $arrayAsientosArray1) {
			foreach ($arrayAsientosArray1 as $id_documento_cruce => $arrayAsientosResul) {
				$sql="UPDATE
							asientos_colgaap
						SET
							debug1=1,
							numero_documento_cruce='".$arrayConsecutivos[$tipo_documento][$id_documento_cruce]."'
						WHERE activo=1
						AND id=$id_asiento";
				$query=mysql_query($sql,$link);

				echo 'consecutivo: '.$arrayAsientosResul['consecutivo_documento'].' tipo: '.$arrayAsientosResul['tipo_documento'].
				' cruce: '.$arrayAsientosResul['tipo_documento_cruce'].' numero: '.$arrayAsientosResul['numero_documento_cruce'].
				' -> '.$arrayConsecutivos[$tipo_documento][$id_documento_cruce].'<br>';

			}
		}
	}


?>