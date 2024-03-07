<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	
	$estado_rol='false';
	// //generamos una consulta para verificar que no tenga dependencias ese rol, si tiene no se puede eliminar
	  $consul="SELECT id FROM empleados WHERE id_rol=$id AND activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
	  $query_consul=mysql_query($consul,$link);
	//si es vacio se puede eliminar de lo contrario no
	if (!$array_consulta=mysql_fetch_array($query_consul)) {
	$estado_rol='true';	
			$SQL = "UPDATE empleados_roles SET activo=0 WHERE id = $id";
			mysql_query($SQL,$link);
	}else{
	$estado_rol='false';	
	}
	// $LOG = 'ACTUALIZACION DE PERMISOS DE ROL ->'.$consul;	
	//  mylog($LOG,17,$link);
	
	 echo $estado_rol.'{.}'.$id;

// 	 $SQL = "UPDATE empleados_roles SET nombre='$nombre', valor='$rolnivel' WHERE id = $id";
// 	mysql_query($SQL,$link);
	
// 	$ArrayPermisos = explode(',',$permisos);
	
// 	mysql_query("DELETE FROM empleados_roles_permisos WHERE id_rol = $id",$link);
	
// 	$LOG = 'ACTUALIZACION DE PERMISOS DE ROL ->'.$SQL;
	
// 	for($i=0;$i<count($ArrayPermisos);$i++){
// 		$SQL2 = "INSERT INTO empleados_roles_permisos (id_permiso,id_rol) VALUES ($ArrayPermisos[$i],$id)";
// 		mysql_query($SQL2,$link);
// 		$LOG .= ' '.$SQL2.' ';
// 	}
	
// 	mylog($LOG,17,$link);
	
	
// 	echo 'true{.}'.$id;
?>