<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	//$SQL="SELECT t.id, t.direccion, t.telefono1  FROM terceros AS t, terceros_direcciones AS t_d WHERE ";
	$sqlSelect="	SELECT id, nombre, id_departamento, id_ciudad, direccion, telefono1,tipo
					FROM terceros
					WHERE activo= 1
						AND id not in (
										SELECT id_tercero
										FROM terceros_direcciones
										WHERE activo=1
										AND direccion_principal=1

										)
					GROUP BY id";

	$querySelect=mysql_query($sqlSelect,$link);
	while ($row=mysql_fetch_row($querySelect)) {
		echo "entro";
		//Condicional tipo persona
		if($row[6]=="Persona"){$tipo="Direccion Principal"; }
		else{$tipo='Sucursal Principal'; }

		$sqlInsert="INSERT INTO terceros_direcciones (id_tercero, direccion, id_departamento, id_ciudad, nombre, telefono1, direccion_principal)
					VALUES (".$row[0].",'".$row[4]."',".$row[2].",".$row[3].",'".$tipo."','".$row[5]."',1)";
		echo "<br>".$sqlInsert;
		$queryInsert=mysql_query($sqlInsert, $link);
	}

	echo "<br><br>".$sqlSelect;
?>