<?php

	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	$id_usuario  = $_SESSION['IDUSUARIO'];
    //$id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];


	$id_empresa  = $_SESSION['EMPRESA'];
	$sucursales =  $_SESSION['NOMBRESUCURSAL'];

	//CONSULTAMOS LA INFORMACION DE LA EMPRESA

	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento FROM empresas WHERE id = '$id_empresa' AND activo = 1 LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$empresa      = mysql_result($queryEmpresa, 0, 'nombre');


	//PARAMETROS DE CONFIGURACION DEL CORREO

	$sql="SELECT * FROM empresas_config_correo WHERE id_empresa='".$_SESSION["EMPRESA"]."'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result)){

		$Host          = mysql_result($result,0,"servidor");
		$Username      = mysql_result($result,0,"correo");
		$Password      = mysql_result($result,0,"password");
		$Port          = mysql_result($result,0,"puerto");
		$SMTPSecure    = mysql_result($result,0,"seguridad_smtp");
		$Autenticacion = mysql_result($result,0,"autenticacion");
		if($Autenticacion   == 'si'){$SMTPAuth	= true;}else{$SMTPAuth	= false;}

		//echo "SERVIDOR:$SERVIDOR</br>CORREO:$CORREO</br>PASSWORD:$PASSWORD</br>PUERTO:$PUERTO</br>SEGURIDAD:$SEGURIDAD</br>AUTENTICACION:$AUTENTICACION</br>";
	}

	else{ $Host = "false"; }

	//CORREO DEL USUARIO QUE ENVIA LA ORDEN DE COMPRA
    $sqlUsuario   = "SELECT nombre,email_empresa AS email,celular_empresa AS telefono FROM empleados WHERE id= '$id_usuario' AND activo = 1 LIMIT 0,1";
	$queryUsuario = mysql_query($sqlUsuario,$link);


	$correo_personal  = mysql_result($queryUsuario, 0, 'email');
	$nombre           = mysql_result($queryUsuario, 0, 'nombre');
	$telefono_usuario = mysql_result($queryUsuario, 0, 'telefono');

	$From     = $correo_personal;
	$FromName = $_SESSION["NOMBREEMPRESA"];
	$Subject  = $textEmail;
	$Debug    = true;

	if($correo_personal!=''){
		$tdE = '<tr>
        		  <td><b>Email:</b></td><td>'.$correo_personal.'</td>
        	  </tr>';
	}
	if($telefono_usuario!='' && $telefono_usuario > 0){
		$tdT = '<tr>
            	  <td><b>Telefono:</b></td><td>'.$telefono_usuario.'</td>
               </tr>';
	}



	$body = '<div style="font-size:16px;padding-bottom:16px;font-weight:bold;padding-left:5px">'.$empresa.'</div>
		        <table style="width:350px">
		        	<tr>
		        		<td><b>Sucursal:</b></td><td>'.$sucursales.'</td>
		        	</tr>
		        	<tr>
		        		<td><b>&nbsp;</b></td><td>&nbsp;</td>
		        	</tr>
		        	<tr>
		        		<td><b>Usuario:</b></td><td>'.$nombre.'</td>
		        	</tr>'.$tdT.$tdE.'
		        </table>
		        <br>
	            <br><br>'.$body.'
	            <br><br>Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.'.'<br>';



	//echo '<script>console.log("'.$From.$FromName.$Host.$Subject.'\n\n'.$Username.$Password.$Port.$SMTPSecure.$Autenticacion.'")</script>';
	//$body     = str_replace("../../../ARCHIVOS_PROPIOS", "../../ARCHIVOS_PROPIOS", $body);
    //$body = "hola es una prueba";
	if($Host!="false"){
			//echo "	Host:$Host</br>Port:$Port</br>From:$correo_personal</br>Username:$Username</br>Password:$Password</br>SMTPSecure:$SMTPSecure</br>Autenticacion:$Autenticacion</br>body:$body</br>";
        //echo '<script>console.log("holaaaa")</script>';
		include('../../../../../misc/phpmailer/PHPMailerAutoload.php');

		$mail = new PHPMailer();
		$mail->IsSMTP();
		//$mail->SMTPDebug = 2;
		$mail->Debugoutput = 'html';
		$mail->SMTPAuth   		= $SMTPAuth;
		$mail->Port       		= $Port;
		if($SMTPSecure != 'Ninguna'){$mail->SMTPSecure   = $SMTPSecure;}
		$mail->Host      		= $Host;
		$mail->Username  		= $Username;

		$mail->Password   		= $Password;

		//$mail->AddReplyTo($correo_personal,$_SESSION["NOMBREFUNCIONARIO"]);

		$mail->From       		= $From;
		$mail->FromName   		= $FromName;
		$mail->ConfirmReadingTo = $correo_personal;
		$mail->Subject    		= $Subject;
		$mail->AltBody    		= 'Para poder ver este mensaje utilize un cliente de correo compatible con contenido HTML!';
		$mail->MsgHTML($body);

		//
		$mail->AddAddress($correo_personal);

		//$mail->AddAddress("hector.morales@logicalsoft.co");
		$array_destinatarios = explode(',',$destinatarios);
		for($i=0;$i<count($array_destinatarios);$i++){
			if($array_destinatarios[$i] != ""){
				$mail->AddAddress($array_destinatarios[$i]);
			}
		}

		$serv = $_SERVER['DOCUMENT_ROOT']."/";
		$url  = $serv.'ARCHIVOS_PROPIOS/empresa_'.$_SESSION['ID_HOST'].'/terceros/adjuntos_terceros/';

// echo '<script>console.log("'.$url.$nombre_archivo.'_'.$id_intercambio.'.pdf");</script>';
		// /////////////////////////////////////// ADJUNTOS ARCHIVOS PREDETERMINADOS ////////////////////////////////////////////
		if(isset($adjuntos1)){
			$array_adjuntos1 = explode(',',$adjuntos1);
			for($i=0;$i<count($array_adjuntos1);$i++){
				if($array_adjuntos1[$i] != ""){
					$mail->AddAttachment($url.$array_adjuntos1[$i], $array_adjuntos1[$i]);
					echo $url.$array_adjuntos1[$i]."<br /><br />";
				}
			}
		}
		// //////////////////////////////////////////// ADJUNTO OTROS ARCHIVOS////////////////////////////////////////////////////
		if(isset($adjuntos2)){
			$array_adjuntos2 = explode(',',$adjuntos2);
			for($i=0;$i<count($array_adjuntos2);$i++){
				if($array_adjuntos2[$i] != ""){
					$mail->AddAttachment($url.$array_adjuntos2[$i], $array_adjuntos2[$i]);
					echo $url.$array_adjuntos2[$i]."<br /><br />";
				}
			}
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//$destinatarios = str_replace(",", "<br />", $destinatarios);

		if(!$mail->Send()) {
		  $ale ='Error enviando e-mail a los siguientes destinatarios : '.$destinatarios.',error.png';
		  $como_ale = 'false';
		  echo '<script>console.log("No envia");</script>';
		} else {
		  $ale = 'E-mail enviado a : '.$destinatarios.',correcto.gif';
		  $como_ale = 'true';
		  echo '<script>console.log("envia");</script>';
		}
		echo 'envio_mail1.php  --> OK.<br />';

		// $ale = 'E-mail enviado a : '.$destinatarios.',correcto.gif';
		// $como_ale = 'true';

	} else {
		 $ale = "LA CONFIGURACION DEL SERVIDOR DE CORREO NO HA SIDO PARAMETRIZADO!!!";
		 echo '<script>console.log("'.$ale.'");</script>';
		 $como_ale = 'false';
	}


?>