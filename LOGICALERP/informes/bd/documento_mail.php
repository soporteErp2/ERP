<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$body = urldecode($body);

	$textoDocumentoMail = 'Documento';

	//ESTOS CONDICIONALES CONTROLARAN DINAMICAMENTE DEPENDIENDO DEL DOCUMENTO A ENVIAR
	if($nombre_informe == 'facturas_radicadas'){
		$textoDocumentoMail = 'Facturas Radicadas';
		$dir_adjunto    	  = 'archivos_temporales';
		$nombre_archivo     = 'Facturas_Radicadas.pdf';
	} else if($nombre_informe == 'estado_cuenta'){
		$textoDocumentoMail = 'Estado De Cuenta';
		$dir_adjunto    	  = 'estado_cuenta';
		$nombre_archivo     = 'Estado_Cuenta.pdf';
	}

	$PDF_GUARDA      	= 'F'; //GUARDA EL PDF;
	$usuarioEmail 	 	= $_SESSION["EMAIL"];
	$documento       	= $textoDocumentoMail . " " . date('d-m-Y');
	$id_usuario  			= $_SESSION['IDUSUARIO'];
  $id_sucursal 			= $_SESSION['SUCURSAL'];
	$id_empresa  			= $_SESSION['EMPRESA'];
	$nombre_sucursal 	= $_SESSION['NOMBRESUCURSAL'];
	$nombre_empresa   = $_SESSION['NOMBREEMPRESA'];

	//PARAMETROS DE CONFIGURACION DEL CORREO
	$sql = "SELECT * FROM empresas_config_correo WHERE id_empresa = '$id_empresa'";
	$result = mysql_query($sql);

	if(mysql_num_rows($result)){
		$Host          = mysql_result($result,0,"servidor");
		$Username      = mysql_result($result,0,"correo");
		$Password      = mysql_result($result,0,"password");
		$Port          = mysql_result($result,0,"puerto");
		$SMTPSecure    = mysql_result($result,0,"seguridad_smtp");
		$Autenticacion = mysql_result($result,0,"autenticacion");
		if($Autenticacion == 'si'){
			$SMTPAuth	= true;
		} else{
			$SMTPAuth	= false;
		}
	}
	else{
		$Host = "false";
	}

	//CORREO DEL USUARIO QUE ENVIA LA ORDEN DE COMPRA
  $sqlUsuario   		= "SELECT nombre,celular_empresa,email_empresa FROM empleados WHERE id = '$id_usuario' AND activo = 1 LIMIT 0,1";
	$queryUsuario 		= mysql_query($sqlUsuario,$link);
	$usuarioNombre   	= mysql_result($queryUsuario,0,'nombre');
	$usuarioTelefono 	= mysql_result($queryUsuario,0,'celular_empresa');
	$usuarioEmail 		= mysql_result($queryUsuario,0,'email_empresa');

	$From     = $usuarioEmail;
	$FromName = $_SESSION["NOMBREEMPRESA"];
	$Subject  = $documento;
	$Debug    = true;

  $body =  '<div style="font-size:16px; padding-bottom:16px; font-weight:bold; padding-left:5px; color:black;">' . $nombre_empresa . '</div>
          	<table style="width:350px; color:black;">
	          	<tr>
	          		<td><b>Sucursal:</b></td>
								<td>' . $nombre_sucursal . '</td>
	          	</tr>
	          	<tr>
	          		<td><b>Usuario:</b></td>
								<td>' . $usuarioNombre . '</td>
	          	</tr>
							<tr>
	        		  <td><b>Email:</b></td>
								<td>' . $usuarioEmail . '</td>
	        	  </tr>
							<tr>
	          	  <td><b>Telefono:</b></td>
								<td>' . $usuarioTelefono . '</td>
	            </tr>
	          </table>
          	<br><br><br>
						' . $body . '
          	<br><br><br>
						<div style="color:black;">Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.</div>'
						. '<br>';

	if($Host != "false"){
		include('../../../misc/phpmailer/PHPMailerAutoload.php');

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Debugoutput	  = 'html';
		$mail->SMTPAuth   		= $SMTPAuth;
		$mail->Port       		= $Port;

		if($SMTPSecure != 'Ninguna'){
			$mail->SMTPSecure   = $SMTPSecure;
		}

		$mail->Host      				= $Host;
		$mail->Username  				= $Username;
		$mail->Password  				= $Password;
		$mail->From       			= $From;
		$mail->FromName   			= $FromName;
		$mail->ConfirmReadingTo = $usuarioEmail;
		$mail->Subject    			= $Subject;
		$mail->AltBody    			= 'Para poder ver este mensaje utilize un cliente de correo compatible con contenido HTML!';
		$mail->MsgHTML($body);
		$mail->AddAddress($usuarioEmail);

		$array_destinatarios = explode(',',$destinatarios);
		for($i = 0; $i < count($array_destinatarios); $i++){
			if($array_destinatarios[$i] != ""){
				$mail->AddAddress($array_destinatarios[$i]);
			}
		}

		$serv = $_SERVER['DOCUMENT_ROOT']."/";
		$url  = $serv.'ARCHIVOS_PROPIOS/empresa_'.$_SESSION['ID_HOST'].'/archivos_temporales/';

		//ADJUNTAR PDF
		if($PDF_GUARDA == 'F'){
			$mail->AddAttachment($url . $nombre_archivo);
		}

		//ADJUNTO OTROS ARCHIVOS
		if(isset($adjuntos2)){
			$array_adjuntos2 = explode(',',$adjuntos2);
			for($i = 0; $i < count($array_adjuntos2); $i++){
				if($array_adjuntos2[$i] != ""){
					$mail->AddAttachment($url . $array_adjuntos2[$i], $array_adjuntos2[$i]);
				}
			}
		}

		if(!$mail->Send()) {
		  $ale ='Error enviando e-mail a los siguientes destinatarios : '.$destinatarios.',error.png';
		  $como_ale = 'false';
		} else {
		  $ale = 'Orden de compra Enviada por e-mail!! a : '.$destinatarios.',correcto.gif';
		  $como_ale = 'true';
		}

		$ale = 'Orden de compra Enviada por e-mail!! a : '.$destinatarios.',correcto.gif';
		$como_ale = 'true';

		//Borrando archivos temporales
		unlink($url . $nombre_archivo);
		for($i = 0; $i < count($array_adjuntos2); $i++){
			if($array_adjuntos2[$i] != ""){
				unlink($url . $array_adjuntos2[$i]);
			}
		}

	}
	else{
		$ale 			= 'LA CONFIGURACION DEL SERVIDOR DE CORREO NO HA SIDO PARAMETRIZADO!!!';
		$como_ale = 'false';
	}


	if($como_ale == "true"){
		echo 'REGISTRO DE ENVIO CREADO......OK<BR />';
	}
?>
<script>
	parent.document.getElementById('finaliza_email').value = '<?php echo $ale; ?>';
</script>
