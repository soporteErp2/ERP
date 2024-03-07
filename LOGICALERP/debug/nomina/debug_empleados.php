<?php

	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sql="SELECT
				id,
				COUNT(numero_identificacion),
				numero_identificacion,
				nombre
			FROM
				terceros
			WHERE
				activo = 1
			AND id_empresa = 47
			AND debug=0
			AND (tipo_identificacion='NIT.' OR ISNULL(tipo_identificacion))
			GROUP BY
				numero_identificacion
			HAVING
				COUNT(numero_identificacion) >= 2
			ORDER BY
				id ASC";
	$query = mysql_query($sql,$link);
	$acumUpdate='';
	while($row=mysql_fetch_array($query)) {
		$acumUpdate.=($acumUpdate=='')? "(numero_identificacion='".$row['numero_identificacion']."' AND ISNULL(tipo_identificacion) )" :
										" OR (numero_identificacion='".$row['numero_identificacion']."' AND ISNULL(tipo_identificacion) )" ;
	}

	$update = "UPDATE terceros SET debug=1 WHERE activo=1 AND ($acumUpdate)";
	echo $update;
	exit;


	$sql="SELECT
				id,
				COUNT(numero_identificacion),
				numero_identificacion,
				nombre
			FROM
				terceros
			WHERE
				activo = 1
			AND id_empresa=1
			GROUP BY
				numero_identificacion
			HAVING
				COUNT(numero_identificacion) >= 2
				ORDER BY id ASC";





	// $sql = "SELECT id,count(id_tercero) AS cont, id_tercero, nombre
	// 		FROM terceros_direcciones
	// 		WHERE activo=1
	// 			AND direccion_principal=1
	// 		GROUP BY id_tercero
	// 			HAVING cont>=2
	// 		ORDER BY id ASC";


	$query = mysql_query($sql,$link);
	$acumUpdate = "{.}";
	while ($row = mysql_fetch_assoc($query)) {
		$acumUpdate .= " OR id='$row[id]'";
	}

	$acumUpdate = str_replace('{.} OR ', '', $acumUpdate);

	$update = "UPDATE terceros SET debug=1 WHERE $acumUpdate";
	echo $update;
	exit;
	//SEGUNDO QUERY
	$query = mysql_query($sql,$link);
	$acumUpdate = "{.}";
	while ($row = mysql_fetch_assoc($query)) {
		$update = "UPDATE terceros_direcciones SET activo=0 WHERE id_tercero='$row[id_tercero]' AND debug<>1 AND direccion_principal=1;";
		echo $update.'<br>';
		$query2  = mysql_query($update,$link);
	}
?>