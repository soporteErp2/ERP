<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	//======================// CONSULTA CUENTAS SIN TERCEROS EN RECIVOS DE CAJA //======================//
	// $sqlColgaap = "SELECT id_documento
	// 				FROM asientos_colgaap
	// 				WHERE debug=1
	// 					AND tipo_documento='RC'
	// 					AND id_tercero=0";
	// $queryColgaap = mysql_query($sqlColgaap,$link);

	// $where = "";
	// while ($row = mysql_fetch_assoc($queryColgaap)) {
	// 	$where .= "OR id=$row[id_documento] ";
	// }
	// echo $sqlRc = "SELECT * FROM recibo_caja WHERE activo=1 AND consecutivo>0 AND ($where)";

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sql   = "SELECT id,id_empresa,id_tercero,consecutivo FROM recibo_caja WHERE debug=1 AND activo=1 AND consecutivo>0";
	$query = mysql_query($sql,$link);

	$arrayTercero = array();
	while ($row = mysql_fetch_array($query)) {

		$idRc        = $row['id'];
		$consecutivo = $row['consecutivo'];
		$id_empresa  = $row['id_empresa'];
		$id_tercero  = $row['id_tercero'];

		// $sqlUpdate = "UPDATE asientos_colgaap
		// 			SET id_tercero=$id_tercero
		// 			WHERE debug=1
		// 				AND id_empresa='$id_empresa'
		// 				AND id_documento='$idRc'
		// 				AND consecutivo_documento='$consecutivo'
		// 				AND tipo_documento='RC'
		// 				AND id_tercero=0";
		// $queryupdate = mysql_query($sqlUpdate,$link);

		$sqlUpdateNiif = "UPDATE asientos_niif
					SET id_tercero=$id_tercero
					WHERE debug=1
						AND id_empresa='$id_empresa'
						AND id_documento='$idRc'
						AND consecutivo_documento='$consecutivo'
						AND tipo_documento='RC'
						AND id_tercero=0";
		$queryupdateNiif = mysql_query($sqlUpdateNiif,$link);

		echo $sqlUpdateNiif.'<br>';
	}


?>