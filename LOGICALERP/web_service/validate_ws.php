<?php

	// VALIDACION API VERSION
	if(!is_dir('./V'.$arrayWs['apiVersion'])) echo json_encode(array("estado"=>"error","msj" => "No se reconoce la version de la api!"));

	// VALIDACION CONEXION
	if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
		$host     = '192.168.8.202';
		$usuario  = 'root';
		$password = 'serverchkdsk';
		$nameDb   = 'erp_bd';
	}
	else if($_SERVER['SERVER_NAME'] == 'erp.plataforma.co' || $_SERVER['SERVER_NAME'] == 'www.erp.plataforma.co'){
		$host     = 'localhost';
		$usuario  = 'root';
		$password = 'simipyme';
		$nameDb   = 'erp';
	}
	else if($_SERVER['SERVER_NAME'] == 'www.logicalsoft-erp.com' || $_SERVER['SERVER_NAME'] == 'logicalsoft-erp.com'){
		$host     = 'localhost';
		$usuario  = 'root';
		$password = 'serverchkdsk';
		$nameDb   = 'erp_acceso';
	}
	else{ echo json_encode(array("estado"=>"error", "msj"=>"ErrorHostWs")); }

	//===========================// VALIDACION CONECTAR GLOBAL //===========================//
	//**************************************************************************************//
	$nit_empresa = mysql_real_escape_string($arrayWs['nit_empresa']);

	$acceso = mysql_connect($host,$usuario,$password);
	if(!$acceso){ return array("estado"=>"error","msj" => "No se puede conectar a la base de datos mysql!"); };
	mysql_select_db($nameDb,$acceso);
	if(!@mysql_select_db($nameDb,$acceso)){ return array("estado"=>"error","msj" => "No se puede conectar a la base de datos!"); }

	$sqlBd   = "SELECT * FROM host WHERE activo=1 AND nit='$nit_empresa' LIMIT 0,1";
	$queryBd = mysql_query($sqlBd,$acceso);

	if(mysql_num_rows($queryBd)){		//SI LA EMPRESA SI EXISTE
		$nameDb = mysql_result($queryBd, 0, 'bd');
		$host   = mysql_result($queryBd, 0, 'servidor');

		mysql_close($acceso);
	}
	else{ echo json_encode(array("estado"=>"error","msj" => "No se puede conectar a la base de datos en este momentos!")); }


	//===========================// CONECTAR //===========================//
	//********************************************************************//
	$conexion = mysql_connect($host, $usuario, $password);
	$selectDb = mysql_select_db($nameDb,$conexion);

	if(!$selectDb) echo json_encode(array("estado"=>"error","msj" => "$nameDb No se puede conectar a la base de datos en este momentos!"));

	//VALIDACION USUARIO
	$passwordMd5 = md5($arrayWs['password']);
	$sqlUser = "SELECT COUNT(E.id) AS contEmpleado,
						E.id AS id_empleado,
						E.nombre AS nombre_empleado,
						E.documento AS documento_empleado,
						E.id_empresa,
						A.grupo_empresarial,
						A.id_pais
				FROM empleados AS E,
					empresas AS A
				WHERE E.username='$arrayWs[username]'
					AND E.password='$passwordMd5'
					AND E.id_empresa = A.id
					AND A.documento='$arrayWs[nit_empresa]'
				GROUP BY E.id
				LIMIT 0,1";
	$queryUser = mysql_query($sqlUser,$conexion);
	$arrayEmpleado = mysql_fetch_assoc($queryUser);

	if(!$queryUser){ echo json_encode(array("estado"=>"error","msj" => "No se puede validar el usuario en base de datos en este momento!")); exit; }
	else if($arrayEmpleado['contEmpleado'] > 0){ $arrayWs = array_merge($arrayWs, $arrayEmpleado); }
	else{ echo json_encode(array("estado"=>"error","msj" => "Error de autenticacion en credenciales de usuario y empresa!")); exit; }

	// print_r($arrayWs); echo "<br><br><br><br><br><br>";
	// return json_encode($arrayWs);
	return include('V'.$arrayWs['apiVersion'].'/'.$nameArchivo.'.php');
?>