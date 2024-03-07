<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include 'PosAdminClass.php';
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	$obj = new PosAdminClass($id_sucursal,$id_empresa,$mysql);

	switch ($method) {
		case 'getToken':
			$obj->getToken($_SESSION['IDUSUARIO']);
			break;
		case 'anularComanda':
			$obj->anularComanda($id_comanda,$observacion);
			break;
		case 'anularFactura':
			$obj->anularFactura($id_documento,$observacion);
			break;
		case 'cerrarCaja':
			$obj->cerrarCaja($id_row);
			break;
		case 'generarPrecierre':
			$obj->generarPrecierre($fecha);
			break;
		case 'generarCierre':
			$obj->generarCierre($fecha);
			break;
		case 'setPin':
			$obj->setPin($pin);
			break;

		default:
			echo json_encode(array('success' => "api default, se debe enviar el metodo", ));
			break;
	}


?>