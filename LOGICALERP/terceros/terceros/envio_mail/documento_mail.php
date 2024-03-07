<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	
	//include("../bd/functions_bd.php");

	$body = urldecode($body);
	//cargaIdFormatoDocumento($id_intercambio,$typeDocument);
	
	$textEmail = 'mensaje de '.$_SESSION['NOMBREEMPRESA'];	

	include_once('envio_mail1.php');

	if($como_ale == "true"){
		
	}
?>
<script>
	//parent.window.frames['frame_mail'].document.body.innerHTML = "<?php echo $ale; ?><BR />";
	parent.document.getElementById('finaliza_email').value = '<?php echo $ale; ?>';
</script>
