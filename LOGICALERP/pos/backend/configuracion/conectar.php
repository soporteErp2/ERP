<?php
	set_time_limit(0);
	date_default_timezone_set("America/Bogota");
	// $serverName = $_SERVER['SERVER_NAME'];
	// if ($serverName=='logicalsoftpos.localhost' || $serverName=='logicalerp.localhost' ) {
	// 	$datosConexion  = array(
	// 						'servidor' => "localhost",
	// 						'usuario'  => "root",
	// 						'password' => "serverchkdsk",
	// 						'bd'       => "erp_bd",
	// 					);
	// }
	// else{
	// 	$datosConexion  = array(
	// 						'servidor' => "localhost",
	// 						'usuario'  => "root",
	// 						'password' => "serverchkdsk",
	// 						'bd'       => "erp_acceso",
	// 					);
	// }
	
	include_once('../../../../configuracion/xml2array.php');

	$DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']);

   	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml')){
		$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE LOCAL O EN CARPETA /SIIP
	}
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion.xml')){
		$fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE UN DOMINIO
	}

	$array = xml2array($fichero);

	$datosConexion  = array(
		'servidor' => $array['configuracion']['database']['servidor'],
		'usuario'  => $array['configuracion']['database']['usuario'],
		'password' => $array['configuracion']['database']['password'],
		'bd'       => $array['configuracion']['database']['bd'],
	);

	include_once('../../../../misc/ConnectDb/class.ConnectDb.php');
	global $mysql;

	// CONSULTAR LA BASE DE DATOS DE LA EMPRESA CON EL NIT
	$objConectDB = new ConnectDb(
						"MySql",			// API SQL A UTILIZAR  MySql, MySqli
						$datosConexion['servidor'],			// SERVIDOR
						$datosConexion['usuario'], 			// USUARIO DATA BASE
						$datosConexion['password'], 			// PASSWORD DATA BASE
						$datosConexion['bd'] 				// NOMBRE DATA BASE
					);

	$mysql = $objConectDB->getApi();
	$link  = $mysql->conectar();

	$sql="SELECT id,servidor,bd FROM host WHERE nit=$nit ";
	$query=$mysql->query($sql);
	$id_host  = $mysql->result($query,0,'id');
	$servidor = $mysql->result($query,0,'servidor');
	$bd       = $mysql->result($query,0,'bd');
	// $id_host     = 22;
	// $mysql->close($mysql->link);

	// CONECTARSE A LA BASE DE DATOS DE ESE NIT
	$objConectDB = new ConnectDb(
						"MySql",			// API SQL A UTILIZAR  MySql, MySqli
						$servidor,			// SERVIDOR
						$datosConexion['usuario'], 			// USUARIO DATA BASE
						$datosConexion['password'], 			// PASSWORD DATA BASE
						$bd 				// NOMBRE DATA BASE
					);
	$mysql = $objConectDB->getApi();
	$link  = $mysql->conectar();

	// CONSULTAR EL ID EMPRESA
	$sql        = "SELECT id FROM empresas WHERE documento=$nit ";
	$query      = $mysql->query($sql);
	$id_empresa = $mysql->result($query,0,'id');


?>
