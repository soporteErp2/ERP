<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	//include("../bd/functions_bd.php");

	$body = urldecode($body);
	//cargaIdFormatoDocumento($id_intercambio,$typeDocument);

	//echo '<script>alert("'.$typeDocument.' '.$id_intercambio.'");</script>';



	$textoDocumentoMail = 'Documento';

	//ESTOS CONDICIONALES CONTROLARAN DINAMICAMENTE DEPENDIENDO DEL DOCUMENTO A ENVIAR

	if($typeDocument == 'CotizacionVenta'){
		$textoDocumentoMail = 'Cotizacion';
		$tablaBuscar        = 'ventas_cotizaciones';
		$dir_adjunto        = 'cotizaciones'; //EL directorio donde se guardan los documentos
		$nombre_archivo     = 'Cotizacion';
	}
	if($typeDocument == 'PedidoVenta'){
		$textoDocumentoMail = 'Pedido';
		$tablaBuscar        = 'ventas_pedidos';
		$dir_adjunto        = 'pedidos';
		$nombre_archivo     = 'Pedido';
	}
	if($typeDocument == 'FacturaVenta'){
		$textoDocumentoMail = 'Factura de Venta';
		$tablaBuscar        = 'ventas_facturas';
		$dir_adjunto    	= 'facturas_venta';
		$nombre_archivo     = 'factura_venta';
	}
	if($typeDocument == 'RemisionesVenta'){
		$textoDocumentoMail = 'Remision';
		$tablaBuscar        = 'ventas_remisiones';
		$dir_adjunto        = 'remisiones';
		$nombre_archivo     = 'Remision';
	}



	$PDF_GUARDA      = 'F'; //GUARDA EL PDF;
	$correo_personal = $_SESSION["EMAIL"];
	$documento       = $textoDocumentoMail.' No.'.$pedido.' para '.$cliente;
	$textEmail       = $textoDocumentoMail.' No. ';

	include_once('envio_mail1.php');

	$fecha = date("Y-m-d");
	$hora  = date("H:i:s");
	$QUIEN = $_SESSION['NOMBREFUNCIONARIO'].' ['.$correo_personal.']';

	if($como_ale == "true"){
		//$ale = str_replace('"', '\"' , $ale);
		//$ale = nl2br ($ale);
		//mysql_query("INSERT INTO email (id_usuario, usuario, documento, contenido, destinatarios, adjuntos, fecha, hora, enviado, mensaje_enviado) VALUES ('$_SESSION[IDASISTE]','$QUIEN', '$documento', '$body','$destinatarios','$arc_adjuntos','$fecha','$hora','$como_ale','$ale')",$link);

		echo 'REGISTRO DE ENVIO CREADO......OK<BR />';
	}
?>
<script>
	//parent.window.frames['frame_mail'].document.body.innerHTML = "<?php echo $ale; ?><BR />";
	parent.document.getElementById('finaliza_email').value = '<?php echo $ale; ?>';
</script>
