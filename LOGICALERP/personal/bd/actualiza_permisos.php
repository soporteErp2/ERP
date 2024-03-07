<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	
	$SQL = "UPDATE empleados_roles SET nombre='$nombre', valor='$rolnivel' WHERE id = $id AND id_empresa=".$_SESSION['EMPRESA'];
	mysql_query($SQL,$link);
	
	$ArrayPermisos = explode(',',$permisos);
	
	mysql_query("DELETE FROM empleados_roles_permisos WHERE id_rol = $id AND id_empresa=".$_SESSION['EMPRESA'],$link);
	
	$LOG = 'ACTUALIZACION DE PERMISOS DE ROL ->'.$SQL;
	
	for($i=0;$i<count($ArrayPermisos);$i++){
		$SQL2 = "INSERT INTO empleados_roles_permisos (id_permiso,id_rol) VALUES ($ArrayPermisos[$i],$id)";
		mysql_query($SQL2,$link);
		$LOG .= ' '.$SQL2.' ';
	}
	
	mylog($LOG,17,$link);
	
	
	echo 'true{.}'.$id;
?>