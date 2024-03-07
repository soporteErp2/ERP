<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sql   = "SELECT id,id_empresa,id_tercero,consecutivo FROM comprobante_egreso WHERE debug=1 AND activo=1 AND consecutivo>0";
	$query = mysql_query($sql,$link);

	$arrayTercero = array();
	while ($row = mysql_fetch_array($query)) {

		$idCe        = $row['id'];
		$consecutivo = $row['consecutivo'];
		$id_empresa  = $row['id_empresa'];
		$id_tercero  = $row['id_tercero'];

		$arrayTercero[$id_empresa][$idCe] = $id_tercero;

		$sqlUpdate = "UPDATE asientos_colgaap
					SET id_tercero=$id_tercero
					WHERE debug=1
						AND id_empresa='$id_empresa'
						AND id_documento='$idCe'
						AND consecutivo_documento='$consecutivo'
						AND tipo_documento='CE'
						AND id_tercero=0";
		$queryupdate = mysql_query($sqlUpdate,$link);

		$sqlUpdateNiif = "UPDATE asientos_niif
					SET id_tercero=$id_tercero
					WHERE debug=1
						AND id_empresa='$id_empresa'
						AND id_documento='$idCe'
						AND consecutivo_documento='$consecutivo'
						AND tipo_documento='CE'
						AND id_tercero=0";
		$queryupdateNiif = mysql_query($sqlUpdateNiif,$link);

		echo $sqlUpdate.'<br>';
	}


?>