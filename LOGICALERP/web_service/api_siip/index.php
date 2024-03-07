<?php
 
	//Cargamos el framework
	require_once 'vendor/autoload.php';

	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;
	 
	//$app = new \Slim\Slim();

	//=============================================Creamos la conexión a la base de datos con MySQLi====================================================//

	function connect($idEmpresaErp){
		if($idEmpresaErp == '' || $idEmpresaErp == 0 || is_nan($idEmpresaErp)){ return array("estado" => "error", "msj" => "Error,\\nNo existe la configuracion id ERP"); }

		if($_SERVER['SERVER_NAME'] == 'logicalerp.localhost'){
			$host       = '192.168.8.202';
			$usuario    = 'root';
			$password   = 'serverchkdsk';
			$nameDb     = 'logicalsofterp';
			$nameDbHost = 'erp_bd';
		}
		else{
			$host       = 'localhost';
			$usuario    = 'root';
			$password   = 'serverchkdsk';
			$nameDb     = 'erp';
			$nameDbHost = 'erp_acceso';
		}

		$conexion = new mysqli($host, $usuario, $password, $nameDbHost) OR die("Error al conectar a la base de datos");

		if ($conexion->connect_error) {
    		die('Error de Conexión (' . $conexion->connect_errno . ') ' . $conexion->connect_error);
		}

		//mysql_select_db($nameDbHost,$conexion) OR die("Error al conectar a la base de datos");

		$sqlNameDb = $conexion->query("SELECT COUNT(id) AS contDb, bd FROM host WHERE activo=1 AND id='$idEmpresaErp'");		
		$rowNameDb = $sqlNameDb->fetch_assoc();

		//print_r($rowNameDb);

		//$queryNameDb = mysql_query($sqlNameDb,$conexion);

		$contDb = $rowNameDb['contDb'];
		$nameDb = $rowNameDb['bd'];

		if($contDb == 0){ return array("estado" => "error", "msj" => "No se ha encontrado la Db de la empresa"); }

		$conexion = new mysqli($host, $usuario, $password, $nameDb);

		if ($conexion->connect_error) {
    		die('Error de Conexión (' . $conexion->connect_errno . ') ' . $conexion->connect_error);
		}

		//mysql_select_db($nameDb,$conexion) OR die("Error al conectar a la base de datos");
		return array("estado"=>"true","conexion"=>$conexion);
	}

	//$db = new mysqli($host, $usuario, $password, );

	$app = new \Slim\App;

	//==================================================OBTENER CONTRATOS PROXIMOS A VENCER=====================================================//

	$app->get('/getContratosVencimientos/{id_empresa}', function (Request $request, Response $response, array $args) {	

		$id_empresa = $args['id_empresa'];

		$idEmpresaErp = $id_empresa;

		$fecha = date('Y-m-d');

		$inicio_notificacion = date("Y-m-d", strtotime('+31 days',strtotime($fecha)));//LA PRIMERA ALERTA
		$final_notificacion = date("Y-m-d", strtotime('+33 days',strtotime($fecha)));//LA ULTIMA ALERTA

		$arrayConect = connect($idEmpresaErp);
		if($arrayConect['estado'] == 'error'){ 
			$arrayReturn = array("estado" => 'error', "msj" => $arrayConect['msj']);
			return json_encode($arrayReturn); 
		}
		
		$conexion = $arrayConect['conexion'];

		$sql = "SELECT
					EC.id,
					EC.id_empleado, 
					EC.documento_empleado,
					EC.fecha_fin_contrato,
					EMP.documento AS nit_empresa
				FROM empleados_contratos AS EC 
				INNER JOIN empleados AS E ON (E.id = EC.id_empleado AND E.activo = 1)
				INNER JOIN empresas AS EMP ON (EMP.id = E.id_empresa)
				WHERE EC.activo=1 
				    AND EC.estado <> 1
					AND vencimiento_firmado = 'false'
					AND EC.fecha_fin_contrato BETWEEN '$inicio_notificacion' AND '$final_notificacion'
				ORDER BY EC.id_empresa ASC";

		$sqlFuncionarios = $conexion->query($sql);

		$funcionarios=array();

		//$total_num_rows = $sqlFuncionarios->num_rows;

		//if ($total_num_rows > 0) {
    	while($fila=$sqlFuncionarios->fetch_assoc()){
    	    $funcionarios[]=$fila;
    	}   
    	//}     
 
        return json_encode(array("estado" => "true","arrayDatos" => $funcionarios));

		//return json_encode($arrayConect);

	});

	$app->get('/getContratoVencimientoPorFecha/{id_empresa}/{cedula}/{fecha_vencimiento}', function (Request $request, Response $response, array $args) {	

		$id_empresa = $args['id_empresa'];
		$cedula     = $args['cedula'];
		$fecha_vencimiento = $args['fecha_vencimiento'];

		$idEmpresaErp = $id_empresa;
		
		$arrayConect = connect($idEmpresaErp);
		if($arrayConect['estado'] == 'error'){ 
			$arrayReturn = array("estado" => 'error', "msj" => $arrayConect['msj']);
			return json_encode($arrayReturn); 
		}
		
		$conexion = $arrayConect['conexion'];

		$sql = "SELECT
					EC.id,
					EC.id_empleado, 
					EC.documento_empleado,
					EC.fecha_fin_contrato,
					EMP.documento AS nit_empresa					
				FROM empleados_contratos AS EC 
				INNER JOIN empleados AS E ON (E.id = EC.id_empleado AND E.activo = 1)
				INNER JOIN empresas AS EMP ON (EMP.id = E.id_empresa)			
				WHERE EC.activo=1 
					AND EC.documento_empleado = '$cedula'
					AND EC.vencimiento_firmado = 'false'
					AND EC.fecha_fin_contrato = '$fecha_vencimiento'";

		$sqlFuncionarios = $conexion->query($sql);

		$funcionarios=array();
		
    	while($fila=$sqlFuncionarios->fetch_assoc()){
    	    $funcionarios[]=$fila;
    	} 
    	     
        return json_encode(array("estado" => "true","arrayDatos" => $funcionarios));		

	});

	$app->run();

?>