<?php
	//error_reporting(E_ALL);
	//ini_set("display_errors", 1);

	include('../../../misc/phpmailer/class.phpmailer.php');
	include('../../../misc/phpmailer/language/phpmailer.lang-es.php') ;

	//cargaConfigCorreo($_SESSION['EMPRESA']);  /// CARGA CONFIGURACION DE MAIL DE LA EMPRESA

		$sql="SELECT * FROM empresas_config_correo WHERE id_empresa='".$_SESSION["EMPRESA"]."'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result)){

			$Host			= mysql_result($result,0,"servidor");
			$Username 		= mysql_result($result,0,"correo");
			$Password		= mysql_result($result,0,"password");
			$Port			= mysql_result($result,0,"puerto");
			$SMTPSecure		= mysql_result($result,0,"seguridad_smtp");
			$Autenticacion	= mysql_result($result,0,"autenticacion");
			if($Autenticacion   =='si'){$SMTPAuth	= true;}else{$SMTPAuth	= false;}

			//echo "SERVIDOR:$SERVIDOR</br>CORREO:$CORREO</br>PASSWORD:$PASSWORD</br>PUERTO:$PUERTO</br>SEGURIDAD:$SEGURIDAD</br>AUTENTICACION:$AUTENTICACION</br>";
		}else{
			$Host			= "false";
		}

		$From     			= $correo_personal;
		$FromName 			= 'RENTAL '.$_SESSION["NOMBREEMPRESA"];
		$Subject			= $documento;
		$Debug				= true;
		$body = str_replace("../../ARCHIVOS_PROPIOS", "../../../ARCHIVOS_PROPIOS", $body);

		if($Host!="false"){
			echo "	Host:$Host</br>Port:$Port</br>From:$correo_personal</br>Username:$Username</br>Password:$Password</br>SMTPSecure:$SMTPSecure</br>Autenticacion:$Autenticacion</br>body:$body</br>";
			$mail = new PHPMailer(true);
			$mail->IsSMTP();
			$mail->SMTPDebug = $Debug;
			$mail->SetLanguage('es');
			$mail->SMTPAuth   		= $SMTPAuth;
			$mail->Port       		= $Port;
			if($SMTPSecure != 'Ninguna'){$mail->SMTPSecure   = $SMTPSecure;}
			$mail->Host      		= $Host;
			$mail->Username  		= $Username;
			$mail->Password   		= $Password;
			$mail->From       		= $From;
			$mail->FromName   		= $FromName;
			$mail->ConfirmReadingTo = $correo_personal;
			$mail->Subject    		= $Subject;
			$mail->AltBody    		= 'Para poder ver este mensaje utilize un cliente de correo compatible con contenido HTML!';
			$mail->MsgHTML($body);
			$mail->AddReplyTo($correo_personal,$_SESSION["NOMBREFUNCIONARIO"]);
			$array_destinatarios = explode(',',$destinatarios);
			for($i=0;$i<count($array_destinatarios);$i++){
				if($array_destinatarios[$i] != ""){
					$mail->AddAddress($array_destinatarios[$i]);
				}
			}

			/////////////////////////////////////////// ADJUNTO PDF ///////////////////////////////////////////////////
			if($PDF_GUARDA == 'F'){
				$mail->AddAttachment("../../../ARCHIVOS_PROPIOS/adjuntos/".$documento.".pdf", $documento.".pdf");
				echo "<br />* ../../../ARCHIVOS_PROPIOS/adjuntos/".$documento.".pdf<br /><br />";
			}

			/////////////////////////////////////// ADJUNTOS ARCHIVOS PREDETERMINADOS ////////////////////////////////////////////
			if(isset($adjuntos1)){
				$array_adjuntos1 = explode(',',$adjuntos1);
				for($i=0;$i<count($array_adjuntos1);$i++){
					if($array_adjuntos1[$i] != ""){
						$mail->AddAttachment("../../../ARCHIVOS_PROPIOS/adjuntos/".$array_adjuntos1[$i], $array_adjuntos1[$i]);
						echo "* ../../../ARCHIVOS_PROPIOS/adjuntos/".$array_adjuntos1[$i]."<br /><br />";
					}
				}
			}
			//////////////////////////////////////////// ADJUNTO OTROS ARCHIVOS////////////////////////////////////////////////////
			if(isset($adjuntos2)){
				$array_adjuntos2 = explode(',',$adjuntos2);
				for($i=0;$i<count($array_adjuntos2);$i++){
					if($array_adjuntos2[$i] != ""){
						$mail->AddAttachment("../../../ARCHIVOS_PROPIOS/adjuntos/".$array_adjuntos2[$i], $array_adjuntos2[$i]);
						echo "* ../../../ARCHIVOS_PROPIOS/adjuntos/".$array_adjuntos2[$i]."<br /><br />";
					}
				}
			}
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$destinatarios = str_replace(",", "<br />", $destinatarios);

			if(!$mail->Send()) {
			  $ale ='Error enviando e-mail a los siguientes destinatarios : '.$destinatarios.',error.png';
			  $como_ale = 'false';
			} else {
			  $ale = 'Cotizacion Enviada por e-mail!! a : '.$destinatarios.',correcto.gif';
			  $como_ale = 'true';
			}
			echo 'envio_mail1.php  --> OK.<br />';

	} else {
		 $ale = "LA CONFIGURACION DEL SERVIDOR DE CORREO NO HA SIDO PARAMETRIZADO!!!";
		 $como_ale = 'false';
	}
?>