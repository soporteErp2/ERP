<?php
	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	$sqlBug = "SELECT
					*
				FROM
					items
				WHERE
					id_impuesto > 0
				AND activo = 1
				AND id NOT IN (
					SELECT
						id_items AS id
					FROM
						items_cuentas
					WHERE
						activo = 1
					AND descripcion = 'impuesto'
				)";
	$queryBug = mysql_query($sqlBug,$link);

	$valueInsert = "";
	while ($rowBug = mysql_fetch_assoc($queryBug)) {
		$id_items = $rowBug['id'];
		$id_empresa = $rowBug['id_empresa'];

		$valueInsert .= "('impuesto','$id_items','debito','$id_empresa','compra'),";
	}

	echo $sqlInsert = "INSERT INTO items_cuentas (descripcion,id_items,tipo,id_empresa,estado) VALUES $valueInsert";
	echo $sqlInsert_niif = "INSERT INTO items_cuentas (descripcion,id_items,tipo,id_empresa,estado) VALUES $valueInsert";



?>