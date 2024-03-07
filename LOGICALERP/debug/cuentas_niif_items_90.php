<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	exit; // BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	// $sqlCuentasGrupo   = "SELECT C.id_grupo,
	// 						C.grupo,
	// 						C.descripcion,
	// 						C.cuenta AS cuenta_colgaap,
	// 						N.cuenta AS cuenta_niif,
	// 						C.estado,
	// 						C.id_empresa
	// 					FROM
	// 						asientos_colgaap_default_grupos AS C
	// 					LEFT JOIN asientos_niif_default_grupos AS N ON (
	// 						C.descripcion = N.descripcion
	// 						AND C.estado = N.estado
	// 						AND C.id_grupo = N.id_grupo
	// 						AND C.id_empresa = N.id_empresa
	// 						AND C.activo = N.activo
	// 					)
	// 					WHERE
	// 						C.activo = 1";


	// // CONSULTA CUENTAS POR ITEMS COLGAAP Y NIIF
	// "SELECT C.id_empresa,
	// 	C.descripcion,
	// 	C.id_items,
	// 	C.estado,
	// 	C.puc AS puc_colgaap,
	// 	N.puc AS puc_niif
	// 	FROM items_cuentas AS C
	// 	LEFT JOIN items_cuentas_niif AS N ON(
	// 		C.id_empresa = N.id_empresa
	// 		AND C.descripcion = N.descripcion
	// 		AND C.id_items = N.id_items
	// 		AND C.estado = N.estado
	// 		AND N.activo=1
	// 	)
	// 	WHERE C.activo=1
	// 		AND C.descripcion <> 'contraPartida_precio'
	// 	ORDER BY C.id_empresa ASC, C.id_items";

	// $sqlCuentasGrupo   = "SELECT id_grupo,
	// 						grupo,
	// 						descripcion,
	// 						cuenta AS cuenta_niif,
	// 						estado,
	// 						id_empresa
	// 					FROM asientos_niif_default_grupos
	// 					WHERE activo = 1 AND cuenta>9999999 ORDER BY id ASC";
	// $queryCuentasGrupo = mysql_query($sqlCuentasGrupo,$link);

	// $arrayCuentasGrupo = array();
	// while ($row = mysql_fetch_assoc($queryCuentasGrupo)) {
	// 	$id_empresa = $row['id_empresa'];
	// 	$id_grupo   = $row['id_grupo'];

	// 	$arrayExplode = explode('_', $row['descripcion']);
	// 	$estado       = $arrayExplode[1];
	// 	$descripcion  = $arrayExplode[2];

	// 	$arrayCuentasGrupo[$id_empresa][$id_grupo][$descripcion][$estado] = $row['cuenta_niif'];
	// }

	// $sqlItems   = "SELECT id, id_grupo, id_empresa FROM items WHERE activo=1 ORDER BY id ASC";
	// $queryItems = mysql_query($sqlItems,$link);

	// while ($row = mysql_fetch_assoc($queryItems)) {

	// 	$id_grupo   = $row['id_grupo'];
	// 	$id_empresa = $row['id_empresa'];

	// 	foreach ($arrayCuentasGrupo[$id_empresa][$id_grupo] as $descripcion => $arrayDescripcion) {
	// 		foreach ($arrayDescripcion as $estado => $cuenta_niif) {
	// 			$sqlUpdate = "UPDATE items_cuentas_niif SET puc='$cuenta_niif' WHERE activo=1 AND id_items='$row[id]' AND estado='$estado' AND descripcion='$descripcion' AND id_empresa='$id_empresa'";
	// 			mysql_query($sqlUpdate,$link);

	// 			echo $sqlUpdate.'<br>';
	// 		}
	// 	}
	// }
?>