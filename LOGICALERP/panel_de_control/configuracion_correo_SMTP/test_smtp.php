<?php
	include('../../../configuracion/conectar.php');
	include('../../../configuracion/define_variables.php');
?>

<?php

	if(isset($CORRALO)){
	 	include('../../../configuracion/define_variables_debug.php');

		/////////////////DEFINO VARIABLES DEL MENSAJE
		$From             = $CORREO;
		$FromName         = 'LOGICALERP '.$_SESSION["NOMBRE_PROPIEDAD"];
		$correo_personal  = $CORREO;
		$Host             = $SERVIDOR;
		$Username         = $USUARIO;
		$Password         = $PASSWORD;
		$Port             = $PUERTO;
		$SMTPSecure       = $SEGURIDAD;
		$Autenticacion    = $AUTENTICACION;
		$SMTPAuth         = ($Autenticacion == 'si')? true: false;
		$Debug            = true;
		$Subject          = 'Test de Configuracion SMTP';
		$body             = 'Este mensaje es generado automaticamente por el Software '.$_SESSION["VERSION"].' de '.$_SESSION["NOMBRE_PROPIEDAD"].'<br />
							para verificar la configuracion SMTP<br /><br /><br />
							Test generado por : '.$_SESSION["NOMBREFUNCIONARIO"];

		require('../../../misc/phpmailer/PHPMailerAutoload.php');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 2;
		$mail->Debugoutput = 'html';

		$mail->MsgHTML($body);
		$mail->SMTPAuth = $SMTPAuth;
		$mail->Port     = $Port;

		if($SMTPSecure != 'Ninguna'){ $mail->SMTPSecure = $SMTPSecure; }

		$mail->Host      		= $Host;
		$mail->Username  		= $Username;
		$mail->Password   		= $Password;
		$mail->From       		= $From;
		$mail->FromName   		= $FromName;
		$mail->ConfirmReadingTo = $correo_personal;
		$mail->Subject    		= $Subject;
		$mail->AltBody    		= 'Para poder ver este mensaje utilize un cliente de correo compatible con contenido HTML!';

		// $mail->AddAddress('sistemas@plataforma.com.co');
		$mail->AddAddress($CORRALO);

		if(!$mail->Send()) { echo'<br /><br /><br />Error enviando e-mail<br />'.$mail->ErrorInfo.'<br /><br />'; }
		else { echo'<br /><br /><br />e-mail enviado Correctamente!<br /><br />Esta Configuracion es valida.<br /><br />'; }
		/*
		$eol = "\r\n";
		$host = $SERVIDOR;
		$port = $PUERTO;
		$fname = $_SESSION["VERSION"].' '.$_SESSION["NOMBRE_PROPIEDAD"];
		$from = $CORREO;
		$pass = $PASSWORD;
		$subj = 'Test de Configuracion SMTP';
		$tname = 'Equipo de Desarrollo ASISTE';
		$to   = 'sistemas@plataforma.com.co';
		$error = '';

		$data  = 'Date: ' . date ( 'r', time () ) . $eol;
		$data .= 'From: "' . $fname . '" <' . $from . '>' . $eol;
		$data .= 'Subject: ' . $subj . $eol;
		$data .= 'To: "' . $tname . '" <' . $to . '>' . $eol;
		$data .= 'X-Priority: 1 (High)' . $eol;
		$data .= 'X-Mailer: <Google Mail Server>' . $eol;
		$data .= 'MIME-Version: 1.0' . $eol;
		$data .= 'Content-Type: text/plain; charset="ISO-8859-1"' . $eol;
		$data .= 'Content-Transfer-Encoding: 8bit' . $eol . $eol;
		$data .= 'Este mesaje es generado automaticamente por el Software '.$_SESSION["VERSION"].' de '.$_SESSION["NOMBRE_PROPIEDAD"] . $eol;
		$data .= 'para verificar la configuracion SMTP'. $eol;


		if ( ( $smtp = fsockopen ($host, $port, $errno, $errstr, 5 ) ) )
		{
			fputs ( $smtp, 'EHLO ' . $host . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(1)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, 'AUTH LOGIN' . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(2)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, base64_encode ($from) . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(3)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, base64_encode ($pass) . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(4)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, 'MAIL From: <' . $from . '>' . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(5)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, 'RCPT To: <' . $to . '>' . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(6)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, 'DATA' . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(7)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, $data . $eol . '.' . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(8)'.$error.'<br /><br />';
				exit ();
			}
			fputs ( $smtp, 'QUIT' . $eol );
			if ( ! test_return ( $smtp, $error ) )
			{
				echo '(9)'.$error.'<br /><br />';
				exit ();
			}
			fclose ( $smtp );
		}

		function test_return ( $res, &$error )
		{
			$out = fread ( $res, 1 );
			$len = socket_get_status ( $res );

			if ( $len > 0 )
			{
				$out .= fread ( $res, $len['unread_bytes'] );
			}

			echo $out . "<br />";
			if ( preg_match ( "/^5/", $out ) )
			{
				$error = $out;
				return false;
			}
			return true;
		}
		*/
	}
	else{ ?>

		<div style="color:#00FF00; margin:10px">
			Por favor escriba una direccion de correo valida para realizar la prueba de envio de e-mail<br />
			<br />
			erp@server$\ <span id="CORRALO2"></span><input name="CORRALO_SMTP" id="CORRALO_SMTP" type="text" onkeypress="validar(event)" style="background-color:#000000; border:none; color:#00FF00; width:330px;">
		</div>
	    <div id="RECIBE_TEST_SMTP" style="color:#00FF00; margin:10px">
	    </div>


		<script type="text/javascript" src="../../misc/lib.js?v2.5.9.0831"></script>
	    <script language="javascript1.2">
		    document.getElementById('CORRALO_SMTP').focus();

		    function validar(e) {
				tecla = (document.all) ? e.keyCode : e.which;
				if (tecla==13) enviar();
		    }

		    function enviar(){

		        var VARGET = 'CORREO=<?php echo $CORREO ?>';
		        VARGET += '&SERVIDOR=<?php echo $SERVIDOR ?>';
		        VARGET += '&USUARIO=<?php echo $USUARIO ?>';
		        VARGET += '&PASSWORD=<?php echo $PASSWORD ?>';
		        VARGET += '&SEGURIDAD=<?php echo $SEGURIDAD ?>';
		        VARGET += '&PUERTO=<?php echo $PUERTO ?>';
		        VARGET += '&AUTENTICACION=<?php echo $AUTENTICACION ?>';
		        VARGET += '&CORRALO='+document.getElementById('CORRALO_SMTP').value;

		        FAjax('configuracion_correo_SMTP/test_smtp.php','RECIBE_TEST_SMTP',VARGET,'post','loading3');
		        document.getElementById('CORRALO2').innerHTML = document.getElementById('CORRALO_SMTP').value;
		        document.getElementById('CORRALO_SMTP').style.visibility = "hidden";
		    }
		</script>

<?php
}
?>