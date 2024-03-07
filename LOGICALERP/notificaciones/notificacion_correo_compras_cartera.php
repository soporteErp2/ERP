<?php
	date_default_timezone_set("America/Bogota");
	/**
	*
	*/
	class NotificacionesCorreoCartera
	{
		var $enlace;
		function __construct($link)
		{
			$this->enlace = $link;

		}

		public function getFacturasVentas($id_empresa,$id_sucursal){
			$sql="SELECT
					fecha_inicio,
					hora_inicio,
					fecha_vencimiento,
					numero_factura_completo,
					documento_vendedor,
					nombre_vendedor,
					documento_usuario,
					usuario,
					nit,
					cliente,
					sucursal_cliente,
					exento_iva,
					cuenta_pago,
					forma_pago,
					observacion,
					total_factura_sin_abono
				FROM
					ventas_facturas
				WHERE
					id_empresa=$id_empresa
					AND id_sucursal=$id_sucursal
					AND total_factura_sin_abono>0
				ORDER BY fecha_vencimiento ASC";
			$result=mysql_query($sql,$this->enlace);
			while($data[] = mysql_fetch_array($result, MYSQL_ASSOC));
			return $data;
		}

		public function getUsuariosNotificacion($id_empresa,$id_sucursal){
			$sql    ="SELECT email_empresa FROM empleados WHERE id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND activo=1 AND notificacion_correo_cartera='true'";
			$result =mysql_query($sql,$this->enlace);
			while($data[] = mysql_fetch_array($result, MYSQL_ASSOC));
			return $data;
		}

		public function diferenciaEntreFechas($fecha1,$fecha2){
			$dias = (strtotime($fecha1)-strtotime($fecha2))/86400;
			$dias = abs($dias);
			$dias = floor($dias);
			return $dias;
		}

		public function porcentajeNotificaciones($dias,$porcentaje){
			$diasNotificacion=($dias*$porcentaje)/100;
			return floor($diasNotificacion);
		}

		public function fecha_corta($date1,$idi){
			list($aano,$mmes,$ddia) = explode("-",$date1);
			$ww = date('w', mktime(0,0,0,date($mmes)  ,date($ddia) ,date($aano)));
			switch($idi){
				case "1":
					$dias  = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
					$meses = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
					break;

				case "0":
					$dias  = array("Dom","Lun","Mar","Mier","Jue","Vie","Sab");
					$meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
					break;
			}
			$resultado = $dias[$ww]." ".$ddia." ".$meses[$mmes-1]." ".$aano;
			return $resultado;
		}

		public function generarNotificaciones(){
			// $fechaActual = date("Y-m-d");
			$fechaActual = "2016-08-02";
			$facturas    = $this->getFacturasVentas(48,46);
			array_pop($facturas);

			$header="
			<!DOCTYPE html>
			<html>
			<head>
				<title>Sistemas de notificaciones automaticas</title>
				<style>
					body{
						width            :100%;
						font-family      :sans-serif, Arial, Helvetica;
						font-size        :15px;
						color            :black;
						background-color :#616161;
						padding          :10px;
					}
					div{
						background-color :#FFFFFF;
						padding          :10px;
					}
					h1{
						text-align:center;
					}
					p{
						margin:10px 0px 10px 0px;
					}
					table {
					    border-collapse: collapse;
					}

					table, td, th {
					    border: 1px solid black;
					}
				</style>
			</head>
			<body>
			<div>
				<h1>Modulo de Notificaciones Automaticas</h1>
				<hr />
				<p>El sistema de notificaciones de Logicalsoft-ERP le informa que hoy ".$this->fecha_corta($fechaActual,0).", tiene las siguientes facturas pendientes por cobrar:</p>
			<table cellpadding='5'>
				<thead>
					<tr>
						<th width='80'>No. Factura</th>
						<th width='100'>Fecha Inicio</th>
						<th width='100'>Fecha Vencimiento</th>
						<th width='180'>Usuario</th>
						<th width='180'>Vendedor</th>
						<th width='80'>NIT</th>
						<th width='180'>Cliente</th>
						<th width='180'>Sucursal Cliente</th>
						<th width='50'>Exento IVA</th>
						<th width='100'>Cuenta de Pago</th>
						<th width='180'>Forma de Pago</th>
						<th width='200'>Observacion</th>
						<th width='150'>Total</th>
					</tr>
				</thead>";
			$body="";
			foreach ($facturas as $key => $value){
				$dias              =$this->diferenciaEntreFechas($value["fecha_inicio"],$value["fecha_vencimiento"]);
				$porcentajeDias    =$this->porcentajeNotificaciones($dias,80);
				// $porcentajeDias =$this->porcentajeNotificaciones($dias,90);
				// $porcentajeDias    =$this->porcentajeNotificaciones($dias,100);
				$porcentajeDias    =($porcentajeDias<1)?1:$porcentajeDias;
				$fechaNotificacion =strtotime('+'.$porcentajeDias.' day', strtotime($value["fecha_inicio"]));
				$fechaNotificacion =date("Y-m-d",$fechaNotificacion);

				if($fechaNotificacion==$fechaActual){
					$body.="<tr>
								<td width='80'>".$value["numero_factura_completo"]."</td>
								<td width='100'>".$this->fecha_corta($value["fecha_inicio"],0)."</td>
								<td width='100'>".$this->fecha_corta($value["fecha_vencimiento"],0)."</td>
								<td width='180'>".$value["usuario"]."</td>
								<td width='180'>".$value["nombre_vendedor"]."</td>
								<td width='80'>".$value["nit"]."</td>
								<td width='180'>".$value["cliente"]."</td>
								<td width='180'>".$value["sucursal_cliente"]."</td>
								<td width='50'>".$value["exento_iva"]."</td>
								<td width='100'>".$value["cuenta_pago"]."</td>
								<td width='180'>".$value["forma_pago"]."</td>
								<td width='200'>".$value["observacion"]."</td>
								<td width='150' align='right'>".$value["total_factura_sin_abono"]."</td>
							</tr>";
				}else{
					// echo "<br />[$fechaActual] - [".$fechaNotificacion."]";
				}
				// echo "<br />[$value[fecha_inicio]] - [$value[fecha_vencimiento]] - [$dias] -  [$porcentajeDias] -> La alerta de notificacion debe llegar el : ".$fechaNotificacion;
			}
			$foot="</table></div>
			<div><p>Por favor no responda directamente este correo electronico, mensaje generado por un robot.</p></div></body>
			</html>";
			$msgHtml=$header.$body.$foot;
			return $msgHtml;

		}
	}

	$configSmtp =getConfiguracionSmtp(48,$link);
	$obj        = new NotificacionesCorreoCartera($link);
	$msgHtml    =$obj->generarNotificaciones();
	$usuarios   =$obj->getUsuariosNotificacion(48,46);
	array_pop($usuarios);

	$mail       = new PHPMailer(true);

	try{
		if($configSmtp[0]['autenticacion']=='si'){
			$SMTPAuth = true;
		}else{
			$SMTPAuth = false;
		}

		$mail->IsSMTP();
		$mail->SMTPDebug        = true;
		$mail->SetLanguage('es');
		$mail->MsgHTML($msgHtml);
		$mail->SMTPAuth         = $SMTPAuth;
		$mail->Port             = $configSmtp[0]['puerto'];
		if($configSmtp[0]['seguridad_smtp'] != 'Ninguna'){
			$mail->SMTPSecure = $configSmtp[0]['seguridad_smtp'];
		}

		$mail->Host             = $configSmtp[0]['servidor'];
		$mail->Username         = $configSmtp[0]['correo'];
		$mail->Password         = $configSmtp[0]['password'];
		$mail->From             = $configSmtp[0]['correo'];
		$mail->FromName         = 'Logicalsoft-ERP';
		$mail->ConfirmReadingTo = $configSmtp[0]['correo'];
		$mail->Subject          = 'Sistema de Notificacion Automatica - Logicalsoft-ERP';
		$mail->AltBody          = 'Para poder ver este mensaje utilize un cliente de correo compatible con contenido HTML!';
		$mail->SMTPOptions      = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false,'allow_self_signed' => true));

		$emails="";

		foreach ($usuarios as $key => $value) {
			$emails.=$value['email_empresa'];
			$mail->AddAddress($value['email_empresa']);
		}
		// echo $emails;

		if(!$mail->Send()) {
		  	$ale = '<br />Error enviando e-mail<br />'.$mail->ErrorInfo.'<br /><br />'.$emails;
		  	echo $ale;
		}else{
		  	$ale = '<br />e-mail enviado Correctamente!<br /><br />Esta Configuracion es valida.<br /><br />'.$emails;
		  	echo $ale;
		}
	}catch (phpmailerException $e){
		echo $e->errorMessage();
	}catch (Exception $e){
		echo $e->getMessage();
	}
?>