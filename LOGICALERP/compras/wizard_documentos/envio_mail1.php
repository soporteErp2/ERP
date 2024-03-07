<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	$id_usuario  = $_SESSION['IDUSUARIO'];
  $id_sucursal = $_SESSION['NOMBRESUCURSAL'];

	//CONSULTAMOS LOS DATOS DE LA ORDEN DE COMPRA
  if($typeDocument == 'OrdenCompra'){
  	$sqlOrden   = "SELECT consecutivo,proveedor,id_empresa,sucursal,bodega,id_usuario FROM $tablaBuscar WHERE id = '$id_intercambio' AND activo = 1 LIMIT 0,1";
		$queryOrden = mysql_query($sqlOrden,$link);
		$arrayTabla = mysql_fetch_array($queryOrden);
		$nombre_archivo = 'orden_de_compra';
  }
  else if($typeDocument == 'ComprobanteEgreso'){
  	$sqlOrden   = "SELECT consecutivo,tercero AS proveedor,id_empresa,sucursal,id_usuario FROM comprobante_egreso WHERE id = '$id_intercambio' AND activo = 1 LIMIT 0,1";
		$queryOrden = mysql_query($sqlOrden,$link);
		$arrayTabla = mysql_fetch_array($queryOrden);
		$nombre_archivo = 'comprobante_egreso';
  }

	$id_empresa = $arrayTabla['id_empresa'];
	$sucursales = $arrayTabla['sucursal'];

	//CONSULTAMOS LA INFORMACION DE LA EMPRESA
	$sqlEmpresa = "SELECT nombre,tipo_documento_nombre,documento FROM empresas WHERE id = '$id_empresa' AND activo = 1 LIMIT 0,1";
	$queryEmpresa 				 = mysql_query($sqlEmpresa,$link);
	$empresa               = mysql_result($queryEmpresa, 0, 'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa, 0, 'tipo_documento_nombre');
	$documento             = mysql_result($queryEmpresa, 0, 'documento');

	//PARAMETROS DE CONFIGURACION DEL CORREO
	$sql = "SELECT * FROM empresas_config_correo WHERE id_empresa = '".$_SESSION["EMPRESA"]."'";
	$result = mysql_query($sql);

	if(mysql_num_rows($result)){
		$Host          = mysql_result($result,0,"servidor");
		$Username      = mysql_result($result,0,"correo");
		$Password      = mysql_result($result,0,"password");
		$Port          = mysql_result($result,0,"puerto");
		$SMTPSecure    = mysql_result($result,0,"seguridad_smtp");
		$Autenticacion = mysql_result($result,0,"autenticacion");

		if($Autenticacion   == 'si'){
			$SMTPAuth	= true;
		}
		else{
			$SMTPAuth	= false;
		}
	}
	else{
		$Host = "false";
	}

	//CORREO DEL USUARIO QUE ENVIA LA ORDEN DE COMPRA
  $sqlUsuario      = "SELECT nombre,email_empresa AS email FROM empleados WHERE id = '$id_usuario' AND activo = 1 LIMIT 0,1";
	$queryUsuario    = mysql_query($sqlUsuario,$link);
	$correo_personal = mysql_result($queryUsuario, 0, 'email');
	$nombre          = mysql_result($queryUsuario, 0, 'nombre');

	$From     = $correo_personal;
	$FromName = $_SESSION["NOMBREEMPRESA"];
	$Subject  = $textEmail.$arrayTabla['consecutivo'].' de '.$arrayTabla['proveedor'];
	$Debug    = true;

	//CONSULTAMOS LA INFORMACION DEL VENDEDOR
	$sqlCont   = "SELECT nombre,celular_empresa AS telefono,email_empresa AS email FROM empleados WHERE id= '$arrayTabla[id_usuario]' AND activo = 1 LIMIT 0,1";
	$queryCont        = mysql_query($sqlCont,$link);
	$nombre_usuario   = mysql_result($queryCont, 0, 'nombre');
	$telefono_usuario = mysql_result($queryCont, 0, 'telefono');
	$email_usuario    = mysql_result($queryCont, 0, 'email');
	$tdT              = '';

	if($email_usuario != ''){
		$tdE = '<tr>
        		  <td><b>Email:</b></td><td>'.$email_usuario.'</td>
        	  </tr>';
	}

	if($telefono_usuario != ''){
		$tdT = '<tr>
            	<td><b>Telefono:</b></td><td>'.$telefono_usuario.'</td>
            </tr>';
	}

	if($typeDocument == 'OrdenCompra'){
		$body =  '<div style="font-size:16px;padding-bottom:16px;font-weight:bold;padding-left:5px">'.$empresa.'</div>
	            <table style="width:350px">
	            	<tr>
	            		<td><b>Sucursal:</b></td><td>'.$sucursales.'</td>
	            	</tr>
	            	<tr>
	            		<td><b>Bodega:</b></td><td>'.$arrayTabla['bodega'].'</td>
	            	</tr>
	            	<tr>
	            		<td><b>&nbsp;</b></td><td>&nbsp;</td>
	            	</tr>
	            	<tr>
	            		<td><b>Usuario:</b></td><td>'.$nombre_usuario.'</td>
	            	</tr>'.$tdT.$tdE.'
	            </table>
	            <br>
            	<br><br>'.$body.'
            	<br><br><br>Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.'.'<br>';
	}
  else if($typeDocument == 'ComprobanteEgreso'){
		$body =  '<div style="font-size:16px;padding-bottom:16px;font-weight:bold;padding-left:5px">
				    		'.$empresa.'<br>
				    		'.$tipo_documento_nombre.' '.$documento.'
							</div>
	            <table style="width:350px">
	            	<tr>
	            		<td><b>Sucursal:</b></td><td>'.$sucursales.'</td>
	            	</tr>
	            	<tr>
	            		<td><b>Usuario:</b></td><td>'.$nombre_usuario.'</td>
	            	</tr>'.$tdT.$tdE.'
	            </table>
	            <br>
            	<br><br>'.$body.'
            	<br><br><br>Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.<br>';
	}

	if($Host != "false"){
		include('../../../misc/phpmailer/PHPMailerAutoload.php');

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Debugoutput = 'html';
		$mail->SMTPAuth    = $SMTPAuth;
		$mail->Port        = $Port;

		if($SMTPSecure != 'Ninguna'){
			$mail->SMTPSecure = $SMTPSecure;
		}

		$mail->Host      	      = $Host;
		$mail->Username  	      = $Username;
		$mail->Password         = $Password;
		$mail->From       		  = $From;
		$mail->FromName   		  = $FromName;
		$mail->ConfirmReadingTo = $correo_personal;
		$mail->Subject    		  = $Subject;
		$mail->AltBody    		  = 'Para poder ver este mensaje utilize un cliente de correo compatible con contenido HTML!';
		$mail->MsgHTML($body);

		if($typeDocument <> 'ComprobanteEgreso'){
			$mail->AddAddress($correo_personal);
		}

		$array_destinatarios = explode(',',$destinatarios);
		for($i = 0;$i < count($array_destinatarios);$i++){
			if($array_destinatarios[$i] != ""){
				$mail->AddAddress($array_destinatarios[$i]);
			}
		}

		$serv = $_SERVER['DOCUMENT_ROOT']."/";
    $id_host = $_SESSION['ID_HOST'];

    $ruta1 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/';
    if(!file_exists($ruta1)){ mkdir ($ruta1); }

    $ruta2 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras';
    $url  = $ruta2.'/';
    if(!file_exists($ruta2)){ mkdir ($ruta2); }

    $ruta3 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/archivos_temporales';
    $url  = $ruta3.'/';

		/////////////////////////////////////////// ADJUNTO PDF ///////////////////////////////////////////
		if($PDF_GUARDA == 'F'){
			$mail->AddAttachment($url.$nombre_archivo."_".$id_intercambio.".pdf", $nombre_archivo."_".$arrayTabla['consecutivo'].".pdf");
			echo $url.$id_intercambio.".pdf<br /><br />";
		}

		/////////////////////////////////////// ADJUNTOS ARCHIVOS PREDETERMINADOS ///////////////////////////////////////
		if(isset($adjuntos1)){
			$array_adjuntos1 = explode(',',$adjuntos1);
			for($i = 0;$i < count($array_adjuntos1);$i++){
				if($array_adjuntos1[$i] != ""){
					$mail->AddAttachment($url.$array_adjuntos1[$i], $array_adjuntos1[$i]);
					// echo $url.$array_adjuntos1[$i]."<br /><br />";
				}
			}
		}

		//////////////////////////////////////////// ADJUNTO OTROS ARCHIVOS////////////////////////////////////////////
		if(isset($adjuntos2)){
			$array_adjuntos2 = explode(',',$adjuntos2);
			for($i = 0;$i < count($array_adjuntos2);$i++){
				if($array_adjuntos2[$i] != ""){
					$mail->AddAttachment($url.$array_adjuntos2[$i], $array_adjuntos2[$i]);
					// echo $url.$array_adjuntos2[$i]."<br /><br />";
				}
			}
		}

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if(!$mail->Send()){
		  $ale      = 'Error enviando e-mail a los siguientes destinatarios : '.$destinatarios.',error.png';
		  $como_ale = 'false';
		}
		else{
		  $ale      = 'Orden de compra Enviada por e-mail!! a : '.$destinatarios.',correcto.gif';
		  $como_ale = 'true';
		}

		echo 'envio_mail1.php  --> OK.<br />';

		$ale      = 'Orden de compra Enviada por e-mail!! a : '.$destinatarios.',correcto.gif';
		$como_ale = 'true';
	}
	else{
		$ale = "LA CONFIGURACION DEL SERVIDOR DE CORREO NO HA SIDO PARAMETRIZADO!!!";
		$como_ale = 'false';
	}
?>
