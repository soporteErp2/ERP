<?php
	require_once("../../../../configuracion/conectar.php");
	require_once("../../../../configuracion/define_variables.php");
	include 'ClassExternalApis.php';

	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_empresa  = $_SESSION['EMPRESA'];

	$objectApi = new ClassExternalApis($id_sucursal,$id_empresa,$mysql);
 	switch ($method) {
 		case 'setCausacion':
 			$objectApi->setCausacion($id_api,$fecha_api,$tipo);
 			break;

 		default:
 			echo json_encode( array('success' => "consumo api default, enviar metodo", ));
 			break;
 	}


 ?>