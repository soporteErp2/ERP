<?php
	// ini_set('display_errors', '0');  //ACTIVA REPORTES DE ERRORES
	error_reporting(0);  //DESACTIVA REPORTES DE ERRORES
	header('Content-Type: text/html; charset=utf8');

    require_once('../nuSoap/nusoap.php');
    require_once('../../../misc/ConnectDb/class.ConnectDb.php');

    function getItems($nit,$usuario,$password,$id_bodega){

		// $nit       = 900467785;
		// $usuario   = "jhon.marroquin";
		// $password  = "jhon3rick";
		// $id_bodega = 141;


	    //VALIDACION CONEXION
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
			$arrayItem[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, No se encontro la empresa!");
			return $arrayItem;
		}
		$id_empresa = $mysql->result($queryEmpresa, 0, 'id');

		//==================// VALIDACION USUARIO //==================//
		$password = md5($password);
		$sqlEmpleado = "SELECT COUNT(id) AS contEmpleado, id FROM empleados WHERE username='$usuario' AND password='$password' AND activo=1";
		$queryEmpleado = $mysql->query($sqlEmpleado,$mysql->link);
		if($mysql->result($queryEmpleado, 0, 'contEmpleado') == 0){
			$arrayItem[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, La informacion de usuario no esta registrada!");
			return $arrayItem;
		}

		$id_empleado = $mysql->result($queryEmpleado, 0, 'id');

		//==================// VALIDACION BODEGA //==================//
		$sqlBodega   = "SELECT COUNT(id) AS contBodega FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa='$id_empresa' AND id='$id_bodega'";
		$queryBodega = $mysql->query($sqlBodega,$mysql->link);
		if($mysql->result($queryBodega, 0, 'contBodega') == 0){
			$arrayItem[] = array("id"=>"", "nombre"=>"", "costo"=>"", "opc"=>"Error, el codigo de bodega no existe!");
			return $arrayItem;
		}

		$arrayItem = array();
		$sqlItems   = "SELECT id, nombre_equipo AS nombre, costos AS costo
						FROM inventario_totales
						WHERE id_empresa='$id_empresa' AND id_ubicacion='$id_bodega' AND inventariable='true' AND estado_venta='true'";
		$queryItems = $mysql->query($sqlItems,$mysql->link);
		while ($row = $mysql->fetch_assoc($queryItems)) {
			$arrayItem[] = array("id"=>$row['id'], "nombre"=>$row['nombre'], "costo"=>$row['costo'], "opc"=>"");
		}
		return $arrayItem;
	}

	//====================// SALIDA //====================//
	//****************************************************//
	$ns = 'http://logicalerp.localhost/LOGICALERP/web_service/V1/items.php';
	$server = new soap_server();
	$server->configureWSDL('ApplicationServices', "urn:".$_SERVER['SCRIPT_URI']);

	$server->wsdl->addComplexType('arrayItems',
									'complexType',
									'struct',
									'all',
									'',
									array('id' => array('id' => 'id', 'type' => 'xsd:string'),
										'nombre' => array('name' => 'nombre', 'type' => 'xsd:string'),
										'costo' => array('name' => 'costo', 'type' => 'xsd:string'),
										'opc' => array('name' => 'opc', 'type' => 'xsd:string')
									)
								);

	$server->wsdl->addComplexType('items',
									'complexType',
									'array',
									'all',
									'SOAP-ENC:Array',
									array(),
									array(
										array('ref'=>'SOAP-ENC:arrayType',
										'wsdl:arrayType'=>'tns:arrayItems[]')
									),
									'tns:arrayItems'
								);

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
			'bodega' => array('name' => 'bodega', 'type' => 'xsd:string')
		)
	);

	$arrayEntrada = array('nit' => 'xsd:string',
						'usuario' => 'xsd:string',
						'password' => 'xsd:string',
						'bodega' => 'xsd:string'
					);

	//====================// REGISTRO //====================//
	//******************************************************//
	$server->register("getItems",
		$arrayEntrada, // ENTRADA
		// array('user' => 'tns:user'), // ENTRADA
		array("return" => "tns:items"),	// SALIDA
		"urn:".$_SERVER['SCRIPT_URI'],
		"urn:".$_SERVER['SCRIPT_URI']."#getItems",
		"rpc",
		"encoded",
		"Costo de Items por Bodega"
	);

	if(!isset($HTTP_RAW_POST_DATA)){ $HTTP_RAW_POST_DATA = ''; }
	$server->service($HTTP_RAW_POST_DATA);

?>