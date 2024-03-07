<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sql = "SELECT id,count(id_tercero) AS cont, id_tercero, nombre
			FROM terceros_direcciones
			WHERE activo=1
				AND direccion_principal=1
			GROUP BY id_tercero
				HAVING cont>=2
			ORDER BY id ASC";


	$query = mysql_query($sql,$link);
	$acumUpdate = "{.}";
	while ($row = mysql_fetch_assoc($query)) {
		$acumUpdate .= " OR id='$row[id]'";
	}

	$acumUpdate = str_replace('{.} OR ', '', $acumUpdate);

	$update = "UPDATE terceros_direcciones SET debug=1 WHERE $acumUpdate";
	echo $update;

	//SEGUNDO QUERY
	$query = mysql_query($sql,$link);
	$acumUpdate = "{.}";
	while ($row = mysql_fetch_assoc($query)) {
		$update = "UPDATE terceros_direcciones SET activo=0 WHERE id_tercero='$row[id_tercero]' AND debug<>1 AND direccion_principal=1;";
		echo $update.'<br>';
		$query  = mysql_query($update,$link);
	}
?>