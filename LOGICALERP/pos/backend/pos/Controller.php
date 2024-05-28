<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Allow-Headers: *");

	 // : "GET,POST,PUT,DELETE,OPTIONS",
	include '../../../../configuracion/define_variables.php';
	include '../configuracion/conectar.php';
	include '../configuracion/configuration.php';
	include 'ClassGlobalFunctions.php';
	include 'PosClassFunctions.php';
	include 'PosClass.php';
	include 'ClassCajas.php';
	include 'ClassMesas.php';


	if (!isset($id_sucursal)){
		// $id_sucursal = $_SESSION['SUCURSAL'];
		// $arrayResult = array('status' => 'failed', 'message'=>'No se envio la variable sucursal' );
		// echo json_encode($arrayResult);
		// exit;
	}

	// $id_host     = 22;
	// $id_empresa  = 2;
	// $id_sucursal = 2;

	// $id_host     = $_SESSION['ID_HOST'];
	// $id_empresa  = $_SESSION['EMPRESA'];
	// $id_sucursal = $_SESSION['SUCURSAL'];

	$objectPos   = new Pos($id_sucursal,$id_empresa,$id_host,$mysql);
	$objectCajas = new Cajas($id_sucursal,$id_empresa,$mysql);
	$objectMesas = new Mesas($id_sucursal,$id_empresa,$mysql);

	switch ($method) {
		case 'validateToken':
			$objectPos->validateToken($token,$id_sucursal);
			break;
			case 'validatePin':
			$objectPos->validatePin($pin);
			break;
		case 'getPosHuesped':
			$objectPos->getPosHuesped($url,$nit,$like);
			break;
		case 'getRestaurantes':
			$objectPos->getRestaurantes();
			break;
		case 'getCashRegister':
			$objectCajas->getCashRegister($id_restaurante);
			break;
		case 'getCashRegisterState':
			$objectCajas->getCashRegisterState($id_caja);
			break;
		case 'getPagosCaja':
			$objectCajas->getPagosCaja($id_caja,$id_usuario);
			break;
		case 'openCashRegister':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectCajas->openCashRegister($params);
			break;
		case 'cerrarCaja':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectCajas->cerrarCaja($params);
			break;

		case 'getEstadoMesas':
			$objectMesas->getEstadoMesas();
			break;
		case 'getMesas':
			$objectMesas->getMesas($id_restaurante);
			break;
		case 'getEstadoMesa':
			$objectMesas->getEstadoMesa($id_restaurante,$id_caja,$id_mesa);
			break;
		case 'openTableAccount':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectMesas->openTableAccount($params);
			break;
		case 'saveItem':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectMesas->saveItem($params);
			break;
		case 'deleteItem':
			$objectMesas->deleteItem($id_cuenta,$id_row,$id_item);
			break;
		case 'solicitarPedido':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectMesas->solicitarPedido($params);
			break;
		case 'printComanda':
			$objectMesas->printComanda($id_comanda);
			break;
		case 'printPrecuenta':
			$objectMesas->printPrecuenta($id_cuenta);
			break;
		case 'changeCuentaMesa':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectMesas->changeCuentaMesa($params);
			break;
		case 'closeMesa':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectMesas->closeMesa($params);
			break;
		case 'generateTiquet':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectPos->generateTiquet($params);
			break;
		case 'printTiquet':
			$objectPos->printTiquet($id_documento);
			break;
		case 'get_tope_facturacion':
			$objectPos->get_tope_facturacion();
			break;
		case 'get_clients':
			if (!$value){ return json_encode(["error"=>"param value is required"]); }
			$objectPos->get_clients($value);
			break;			
		case 'savePayPos':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectPos->savePayPos($params);
			break;
		case 'logOutToken':
			$json   = file_get_contents('php://input');
			$params = json_decode($json,true);
			if ($params==''){ return; }
			$objectPos->logOutToken($params);
			break;

		default:
			echo json_encode(array('status' => 'success', 'mensaje' => 'Consumo api default, debe enviar el metodo' ));
			break;
	}

?>