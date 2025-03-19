<?php
	require 'ClaseAutorizaOrdenesCorreo.php';
	require '../../../../funciones_globales/Clases/Orm_Controller.php';
	include_once('../../../../../configuracion/conexion.php');
	include_once('../../../../../misc/phpmailer/PHPMailerAutoload.php');


    // Inicializacion de variables
	$userData 			= isset($_GET['data']) ? $_GET['data'] : null;
	$id_documento 		= isset($_GET['id_documento']) ? $_GET['id_documento'] : null;
	$tipo_autorizacion 	= isset($_GET['tipo_autorizacion']) ? $_GET['tipo_autorizacion'] : null;
	list($usuario, $contrasena, $nitEmpresa, $idEmpresa, $nameBd) = explode('|',base64_decode($userData));

    $orm = new Orm_Controller($server->server_name, $server->user, $server->password, $nameBd);
	
	//Inicializar la clase Ordenes
	$objOrdenes = new AutorizaOrdenesCorreo($orm,$idEmpresa,$id_documento,$usuario,$contrasena,$tipo_autorizacion);
	$response = $objOrdenes->getResponse();
	if(!$response['success']){echo json_encode($response); exit;}

	//Autoriza ordenes
    $objOrdenes->autorizarOrdenCompraArea();
	$responseOrden = $objOrdenes->getResponse();
	if(!$responseOrden['success']){echo json_encode($responseOrden); exit;}

	//Envio de email
	$phpMailer = new PHPMailer();
    $objOrdenes->enviarNotificacion($phpMailer);
	$responseEnvioCorreo = $objOrdenes->getResponse();
	
	//respuesta final
    echo json_encode(["success"=>true,"responseEnvioCorreo"=>$responseEnvioCorreo]);

?>