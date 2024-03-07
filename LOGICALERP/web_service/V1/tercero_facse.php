<?php
	// ini_set('display_errors', '0');  // ACTIVA REPORTES DE ERRORES
	error_reporting(0);  // DESACTIVA REPORTES DE ERRORES
	header('Content-Type: text/html; charset=utf8');

	require_once('../nuSoap/nusoap.php');
	require_once('../../../misc/ConnectDb/class.ConnectDb.php');

	function getTercero($nit,$usuario,$password,$nit_tercero){
		$arrayTercero[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, tercero no existe!");
			return $arrayTercero; 
		// $nit       = 900467785;
		// $usuario   = "jhon.marroquin";
		// $password  = "jhon3rick";
		// $id_bodega = 141;

		// VALIDACION CONEXION
		if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
			$hostDb     = '192.168.8.202';
			$usuarioDb  = 'root';
			$passwordDb = 'serverchkdsk';
			$nameDb     = 'erp_bd';
		}
		else if($_SERVER['SERVER_NAME'] == 'www.logicalsoft-erp.com' || $_SERVER['SERVER_NAME'] == 'logicalsoft-erp.com'){
			$hostDb     = 'localhost';
			$usuarioDb  = 'root';
			$passwordDb = 'serverchkdsk';
			$nameDb     = 'erp_acceso';
		}

		//==========================// CONEXION BD CENTRAL //==========================//
		//*****************************************************************************//
		$connectHost = new ConnectDb(
							"MySql",
							$hostDb,
							$usuarioDb,
							$passwordDb,
							$nameDb
						);

		$mysqlHost = $connectHost->getApi();
		$link      = $mysqlHost->conectar();

		$sqlhost   = "SELECT COUNT(id) AS contHost, servidor, bd FROM host WHERE activo=1 AND nit='$nit' LIMIT 0,1";
		$queryHost = $mysqlHost->query($sqlhost,$link);
		$contHost  = $mysqlHost->result($queryHost, 0, 'contHost');

		if($contHost == 0){ exit; }

		$cliente_host = $mysqlHost->result($queryHost, 0, 'servidor');
		$cliente_bd   = $mysqlHost->result($queryHost, 0, 'bd');

		$mysqlHost->free_result($queryHost);
		unset($link);
		unset($mysqlHost);


		//==========================// CONEXION BD POR CLIENTE //==========================//
		//*********************************************************************************//
		$connect = new ConnectDb(
						"MySql",
						$cliente_host,
						$usuarioDb,
						$passwordDb,
						$cliente_bd
					);
		$mysql = $connect->getApi();
		$link  = $mysql->conectar();

		//==================// VALIDACION EMPRESA //==================//
		$sqlEmpresa   = "SELECT COUNT(id) AS contEmpresa, id FROM empresas WHERE documento='$nit' AND activo=1 LIMIT 0,1";
		$queryEmpresa = $mysql->query($sqlEmpresa, $mysql->link);
		$contEmpresa  = $mysql->result($queryEmpresa, 0, 'contEmpresa');

		if($contEmpresa == 0){
			$arrayTercero[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, No se encontro la empresa!");
			return $arrayTercero;
		}
		$id_empresa = $mysql->result($queryEmpresa, 0, 'id');

		//==================// VALIDACION USUARIO //==================//
		$password = md5($password);
		$sqlEmpleado = "SELECT COUNT(id) AS contEmpleado, id FROM empleados WHERE username='$usuario' AND password='$password' AND activo=1";
		$queryEmpleado = $mysql->query($sqlEmpleado,$mysql->link);
		if($mysql->result($queryEmpleado, 0, 'contEmpleado') == 0){
			$arrayTercero[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, La informacion de usuario no esta registrada!");
			return $arrayTercero;
		}

		$id_empleado = $mysql->result($queryEmpleado, 0, 'id');

		//==================// VALIDACION Y CONSULTA DEL TERCERO //==================//
		$sqlTercero   = "SELECT id,tipo_identificacion,numero_identificacion,ciudad_identificacion, nombre, nombre_comercial,direccion,telefono FROM terceros WHERE activo=1 AND id_empresa='$id_empresa' AND numero_identificacion='$nit_tercero'";
		$queryTercero = $mysql->query($sqlTercero,$mysql->link);

		$id_terccero           = mysql_result($queryTercero,0,'id');
		$tipo_identificacion   = mysql_result($queryTercero,0,'tipo_identificacion');
		$numero_identificacion = mysql_result($queryTercero,0,'numero_identificacion');
		$ciudad_identificacion = mysql_result($queryTercero,0,'ciudad_identificacion');
		$nombre                = mysql_result($queryTercero,0,'nombre');
		$nombre_comercial      = mysql_result($queryTercero,0,'nombre_comercial');
		$direccion             = mysql_result($queryTercero,0,'direccion');
		$telefono              = mysql_result($queryTercero,0,'telefono');

		if($id_terccero == 0 || $id_tercero == ''){
			$arrayTercero[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, tercero no existe!");
			return $arrayTercero;
		}

		
		$arrayTercero[] = array(
								"tipo_identificacion"   => $tipo_identificacion,
								"nit"                   => $numero_identificacion,
								"ciudad_identificacion" => $ciudad_identificacion,
								"razon_social"          => utf8_encode($nombre),
								"nombre_comercial"      => utf8_encode($nombre_comercial),
								"direccion"             => $direccion,
								"telefono"              => $telefono
							);
		
		return json_encode($arrayTercero);
	}

	//====================// SALIDA //====================//
	//****************************************************//
	$ns  = 'http://logicalerp.localhost/LOGICALERP/web_service/V1/tercero_facse.php';
	$server = new soap_server();
	$server->configureWSDL('ApplicationServices', "urn:".$_SERVER['SCRIPT_URI']);

	//====================// ENTRADA //====================//
	//*****************************************************//
	$server->wsdl->addComplexType(
		'user',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'nit' => array('name' => 'nit', 'type' => 'xsd:string'),
			'usuario' => array('name' => 'usuario', 'type' => 'xsd:int'),
			'password' => array('name' => 'password', 'type' => 'xsd:string'),
			'nit_tercero' => array('name' => 'nit_tercero', 'type' => 'xsd:string')
		)
	);

	$arrayEntrada = array('nit' => 'xsd:string',
						'usuario' => 'xsd:string',
						'password' => 'xsd:string',
						'nit_tercero' => 'xsd:string'
					);

	//====================// REGISTRO //====================//
	//******************************************************//
	$server->register("getTercero",
		$arrayEntrada, // ENTRADA
		// array('user' => 'tns:user'), // ENTRADA
		array('return'=>'xsd:string'),	// SALIDA
		"urn:".$_SERVER['SCRIPT_URI'],
		"urn:".$_SERVER['SCRIPT_URI']."#getTercero",
		"rpc",
		"encoded",
		"Datos de tercero por nit"
	);

	$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
	$server->service($POST_DATA);

?>