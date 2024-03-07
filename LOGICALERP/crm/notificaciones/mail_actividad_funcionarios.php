<?php

    //***************************CODIGO PARA ENVIAR UN CORREO NOTIFICANDO QUE SE HAN GENERADO ORDENES DE COMPRA******************//
    //===========================================================================================================================//

	
	$path = '../../../';
	include_once($path.'configuracion/conectar.php');
	include_once($path.'configuracion/define_variables.php');
	include_once($path.'misc/phpmailer/PHPMailerAutoload.php');

	$id_usuario  = $_SESSION['IDUSUARIO'];
    $id_empresa  = $_SESSION['EMPRESA'];	
	
	//echo '<script>console.log("'.var_dump($row_consulta).'")</script>';
	$fecha = date('Y-m-d h:i:s A');


	// //CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular FROM empresas WHERE id='$id_empresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ciudad     = mysql_result($queryEmpresa,0,'ciudad');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos 			   = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	$actividad_economica   = mysql_result($queryEmpresa,0,'actividad_economica');

    //CORREO DEL USUARIO DEL SISTEMA
	$sqlUsuario     = "SELECT nombre,email_empresa AS email FROM empleados WHERE id= '$id_usuario' AND activo = 1 LIMIT 0,1";
	$queryUsuario   = $mysql->query($sqlUsuario,$link);
	$email_usuario  = $mysql->result($queryUsuario, 0, 'email');
	$nombre_usuario = $mysql->result($queryUsuario, 0, 'nombre');

    /*//NOMBRE DEL CLIENTE
    $sqlTercero     = "SELECT nombre_comercial FROM terceros WHERE id= '$id_cliente' AND activo = 1 LIMIT 0,1";
	$queryTercero   = $mysql->query($sqlTercero,$link);
    $nombre_tercero = $mysql->result($queryTercero, 0, 'nombre_comercial');*/

	//NOMBRE DE LA ACTIVIDAD

	if($finalizo == 'true'){
		$id = $id_actividad;
	}

	$sqlActividad     = "SELECT objetivo,tema,fechaf,horaf,observacion,cliente,id_asignado,id_usuario FROM crm_objetivos_actividades WHERE id= '$id' AND activo = 1 LIMIT 0,1";
	$queryActividad   = $mysql->query($sqlActividad,$link);

	$row = $mysql->fetch_array($queryActividad);

	if($finalizo == 'true'){
		$id_asignado    = $row['id_asignado'];
		$id_responsable = $row['id_usuario'];
	}

	//CORREO DEL USUARIO ASIGNADO A LA ACTIVIDAD
	$sqlAsignado     = "SELECT nombre,email_empresa AS email FROM empleados WHERE id= '$id_asignado' AND activo = 1 LIMIT 0,1";
	$queryAsignado   = $mysql->query($sqlAsignado,$link);
	$email_asignado  = $mysql->result($queryAsignado, 0, 'email');
	$nombre_asignado = $mysql->result($queryAsignado, 0, 'nombre');

	$objetivo = '';

	if(isset($row['objetivo']) && $row['objetivo'] != ''){
		$objetivo = '<tr>
    	                 <td><b>Proyecto:&nbsp;</b></td><td>'.$row['objetivo'].'</td>
    	             </tr>';
    }

    $observacion = '';

    if(isset($row['observacion']) && $row['observacion'] != ''){
    	$observacion = '<tr>
		                     <td><b>Observaciones:&nbsp;</b></td><td>'.$row['observacion'].'</td>
		                </tr>';
    }

    $title = 'Asignada';
    $fecha_vencimiento = '<tr>
		                 	 <td><b>Fecha de Vencimiento:&nbsp;</b></td><td>'.fecha_larga($row['fechaf']).' '.$row['horaf'].'</td>
		              	  </tr>';

	$fila_usuario = '<tr>
                        <td><b>Funcionario que Asigna:&nbsp;</b></td><td>'.$nombre_usuario.'</td>
                    </tr>';

    if($finalizo == 'true'){

    	//CORREO DEL USUARIO QUE CREO LA ACTIVIDAD
		$sqlR        = "SELECT nombre,email_empresa AS email FROM empleados WHERE id= '$id_responsable' AND activo = 1 LIMIT 0,1";
		$queryR      = $mysql->query($sqlR,$link);
		$email_resp  = $mysql->result($queryR, 0, 'email');
		$nombre_resp = $mysql->result($queryR, 0, 'nombre');

    	//CUANDO FINALIZO ACTIVIDAD DEBO ENVIARLE A LOS ADICIONALES EN LA ACTIVIDAD
    	$sql   = "SELECT id_asignado FROM crm_objetivos_actividades_personas WHERE id_actividad = $id_actividad";
    	$query = $mysql->query($sql);

    	$funcionarios = array();

    	$i=0;
    	while($rowS = $mysql->fetch_array($query)){
    		$funcionarios[$i] = $rowS['id_asignado'];
    		$i++;
    	}

    	$title = 'Finalizada';

    	$fecha_vencimiento = '';

    	$observacion  = '<tr>
		                     <td><b>Observaciones:&nbsp;</b></td><td>'.$accion.'</td>
		                </tr>';

        $fila_usuario = '<tr>
                        	<td><b>Finalizo:&nbsp;</b></td><td>'.$nombre_usuario.'</td>
                   		 </tr>';

    }
    //============================// BODY EMAIL //=============================//
	/****************************************************************************/
	$mail  = new PHPMailer();
	
	$body .= '  <font color="black">	              
	                <div style="width:445px; margin-left:10px;">
						<table>
							<tr><td><b>'.$razon_social.'</b></td><td></td></tr>
							<tr><td><b>'.$tipo_regimen.'</b></td><td></td></tr>
							<tr><td><b>'.$tipo_documento_nombre.':</b></td><td>'.$documento_empresa.'</td></tr>
							<tr><td><b>Direccion: </b></td><td>'.$direccion_empresa.'</td></tr>
							<tr><td><b>Tels:</b></td><td>'.$telefonos.'</td></tr>
							<tr><td><b>'.$ciudad.'</b></td><td></td></tr>
						</table>
				    </div>
				    <br>
				    <b><div style="font-size:16px;padding-bottom:16px;padding-left:70px">Notificacion de Actividad '.$title.'</div></b>
				    <div style="width:445px; margin-left:10px;">
						<table>
	              	  		<tr>
		              		    <td><b>Cliente: &nbsp;</b></td><td>'.$row['cliente'].'</td>
		              		</tr>
		              		'.$objetivo.'
		              		<tr>
		              		    <td><b>Actividad:&nbsp;</b></td><td>'.$row['tema'].'</td>
		              		</tr>
		              		'.$fecha_vencimiento.'
		              		'.$fila_usuario.'
		              		'.$observacion.'
	             		</table>
					</div>	              
	            </font>';
    /*
	if($emailAdministrador != ""){
		$body .= ' [ <a HREF="mailto:'.$emailAdministrador.'" style="text-decoration:none"> &lt;'.$emailAdministrador.'&gt; </a> ]';
	}*/

	$body .= '<br>Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.</font>'.'<br>';
    
	$mail  = new PHPMailer();
	
	$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
	$queryConexion = mysql_query ($sqlConexion,$link);
	if($row_consulta= mysql_fetch_array($queryConexion)){
		$seguridad     = $row_consulta['seguridad_smtp'];
		// $pass          = $row_consulta['password'];
		$pass          = $row_consulta['password'];
		// $user          = $row_consulta['user_name'];
		$user          = $row_consulta['correo'];
		$puerto        = $row_consulta['puerto'];
		// $servidor      = $row_consulta['servidor_SMTP'];
		$servidor      = $row_consulta['servidor'];
		// $from          = $row_consulta['from'];
		$from          = $row_consulta['correo'];
		$autenticacion = $row_consulta['autenticacion'];
	}

	if ($user=='') {		
		
	}

	$mail->IsSMTP();
	// $mail->SMTPDebug = true;

	$mail->SMTPAuth   = true;                  				// enable SMTP authentication
	$mail->SMTPSecure = $seguridad;                         // sets the prefix to the servier
	$mail->Host       = $servidor;      				    // sets GMAIL as the SMTP server
	$mail->Port       = $puerto;                            // set the SMTP port

	$mail->Username   = $user; // GMAIL username
	$mail->Password   = $pass; // GMAIL password

	$mail->From       = $from;
	$mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
	$mail->Subject    = "Modulo CRM: Notificacion de Actividad ".$title;
	$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
	$mail->WordWrap   = 50; // set word wrap

	//echo '<script>console.log("wfsdgsdffffg'.$email.$id_empresa.$id_usuario.'")</script>';

	$mail->MsgHTML($body);
	//$mail->AddAddress($email_responsable);

	//if($id_empresa != 8){

	for($i=0;$i<count($funcionarios);$i++){
		$queryFuncionario = $GLOBALS['mysql']->query("SELECT email_empresa AS email FROM empleados WHERE id= '$funcionarios[$i]' AND activo = 1 LIMIT 0,1",$link);

    	$email_funcionario  = $mysql->result($queryFuncionario, 0, 'email');
    	//$nombre_responsable = $mysql->result($queryUsuario, 0, 'nombre');
		$mail->AddAddress($email_funcionario);//A LOS ADICIONALES

	}

	//$mail->AddAddress("hector.morales@logicalsoft.co");
	$mail->AddAddress($email_usuario);//AL USUARIO QUE ESTA EN EL SISTEMA
	$mail->AddAddress($email_asignado);//AL ASIGNADO PRINCIPAL DE LA ACTIVIDAD
	$mail->AddAddress($email_resp);//AL QUE PROGRAMO LA ACTIVIDAD


	$mail->IsHTML(true); // send as HTML

	if(!$mail->Send()) {
				
    }


?>