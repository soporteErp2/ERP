<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include('../../../misc/codeQR/phpqrcode.php');

	$id_empresa = $_SESSION['EMPRESA'];
	$id_host    = $_SESSION['ID_HOST'];

	switch ($op) {
		// HORAS EXTRAS DE PERSONAL /////////////////////////////////////////
		case "cargaHorasExtras":
			cargaHorasExtras($id,$fechai,$fechaf,$link);
			break;

		// API GOOGLE /////////////////////////////////////////
		case "cargaConfigCorreo":
			cargaConfigCorreo($link);
			break;

		case "guardaConfigCorreo":
			guardaConfigCorreo($activo,$email,$password,$dominio,$link);
			break;

		// CORREOS CORPORATIVOS /////////////////////////////////////////
		case "updateCorreo":
			updateCorreo($mail,$id,$link);
			break;

		// ROLES /////////////////////////////////////////
		case "agregarRol": //OK
			agregarRol($rol,$rolnivel,$link);
			break;

		// CONTRATOS ////////////////////////////////////////
		case "importarContrato": //OK
			importarContrato($id,$id_empresa,$id_sucursal,$link);
			break;

		case "agregarContrato": //OK
			agregarContrato($nombre,$id_empresa,$id_sucursal,$contrato,$link);
			break;

		case "actualizarContrato": //OK
			actualizarContrato($nombre,$contrato,$id,$link);
			break;

		case "EliminarContrato": //OK
			EliminarContrato($id,$link);
			break;

		// CARGOS ////////////////////////////////////////
		case "agregarCargo": //OK
			agregarCargo($cargo,$link);
			break;

		case "actualizarCargo": //OK
			actualizaraCargo($cargo,$id,$link);
			break;

		case "EliminarCargo": //OK
			EliminarCargo($id,$link);
			break;

		// EMPLEADOS ///////////////////////////////////////
		case "agregarEmpleado": //OK
			agregarEmpleado($tipo_id1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$username1,$id_empresa,$id_sucursal1,$ID,$acceso_sistema,$link);
			break;

		case "actualizarEmpleado": //OK
			actualizarEmpleado($tipo_id1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$username1,$id_empresa,$id_sucursal1,$ID,$acceso_sistema,$link);
			break;

		case "actualTextoContrato": //OK
			actualTextoContrato($id_contrato,$texto,$link);
			break;

		case "resetPass": //OK
			resetPass($id,$link);
			break;

		case "updateToken": //OK
			updateToken($id,$link);
			break;

		case "checkIdentificacion": //OK
			checkIdentificacion($id1,$id_empresa,$sucursal,$link);
			break;

		case "checkUsername": //OK
			checkUsername($user,$link,$id);
			break;

		//case "uploadFoto": //OK
		//	uploadFoto($id1,$link);
		//	break;

		case "EliminarEmpleado": //OK
			EliminarEmpleado($id,$id_empresa,$sucursal,$link);
			break;

		case "RestaurarEmpleado": //OK
			RestaurarEmpleado($id,$id_empresa,$sucursal,$link);
			break;

		case "agregarDocumento":
			agregarDocumento($cargo,$link);
			break;

		case "actualizarDocumento":
			actualizarDocumento($id,$cargo,$link);
			break;

		// ADJUNTAR INVENTARIO A EMPLEADO JHON-ERICK////////////////////////////////////////
		case "actualizar_inventario_empleado": //OK
			actualizar_inventario_empleado($id_empleado,$id_inventario,$link);
			break;

		case "consultaSizeImageDocumentEmpleado":
			consultaSizeImageDocumentEmpleado($nombre,$id_host);
			break;

		case "ventanaVerImagenDocumentoEmpleado":
			ventanaVerImagenDocumentoEmpleado($nombreImage,$nombreDocumento,$type,$id_host);
			break;

		case "descargarArchivo":
			descargarArchivo($id_host,$nombreDocumento,$nombreRandomico);
			break;

		case "option_departamento":
			option_departamento($id_pais, $id_empresa, $id_empleado, $render, $link);
			break;

		case "option_ciudad":
			option_ciudad($id_departamento, $id_empresa, $id_empleado,$render, $link);
			break;

		case 'select_informacion_parentesco':
			select_informacion_parentesco($parentesco,$id_empleado,$link);
			break;

		case 'select_contacto_principal':
			select_contacto_principal($id_contacto,$id_empleado,$opcion,$link);
			break;

		case 'trasladar_empleado_sucursal':
			trasladar_empleado_sucursal($id_empleado,$id_nueva_sucursal,$fecha_inicio,$fecha_final,$id_empresa,$link);
			break;


	}

	// API GOOGLE (CORREOS) ////////////////////////////////////////////////////////////////////////////////////////
	function cargaConfigCorreo($link){
		$sql   = "SELECT * FROM configuracion_global_api_google";
		$query = mysql_query($sql,$link);

		if(mysql_num_rows($query)){
			$activo   = mysql_result($query,$i,"activo");
			$email    = mysql_result($query,$i,"email");
			$password = mysql_result($query,$i,"password");
			$dominio  = mysql_result($query,$i,"dominio");

			echo  	'true{.}'.$activo.'{.}'.$email.'{.}'.$password.'{.}'.$dominio;
		}
		else{ echo  'false{.}No existe.'; }
	}

	function guardaConfigCorreo($activo,$email,$password,$dominio,$link){
		$sql   = "UPDATE configuracion_global_api_google  SET activo='".$activo."', email='".$email."', password='".$password."', dominio='".$dominio."'";
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}Guardado';
			mylog('ACTUALIZA CONFIG MAIL -> '.$sql,17,$link);
		}
		else{ echo 'false{.}No realizado '.$sql; }
	}

	function updateCorreo($mail,$id,$link){
		$sql   = "UPDATE empleados  SET email_empresa = '".$mail."' WHERE documento = '".$id."'";
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$sql;
			mylog('ACTUALIZA MAIL -> '.$sql,17,$link);
		}
		else{ echo 'false{.}No realizado '.$sql; }
	}

	// ROLES /////////////////////////////////////////////////////////////////////////////////////////

	function agregarRol($rol,$rolnivel,$link){

		$sql   = "INSERT INTO empleados_roles (nombre,id_empresa) VALUES ('".$rol."',".$_SESSION['EMPRESA'].")";
		$query = mysql_query($sql,$link);

		if($query){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR ROL ->'.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	// CONTRATOS /////////////////////////////////////////////////////////////////////////////////////////

	function importarContrato($id,$id_empresa,$id_sucursal,$link){
		$sql      = "SELECT * FROM empleados_contratos WHERE id='$id'";
		$result   = mysql_query($sql,$link);
		$nombre   = mysql_result($result,$i,"nombre");
		$contrato = mysql_result($result,$i,"contrato");

		$sql   = "INSERT INTO empleados_contratos (nombre,id_empresa,id_sucursal,contrato) VALUES ('$nombre','$id_empresa','$id_sucursal','$contrato')";
		$query = mysql_query($sql,$link);

		if($query){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('IMPORTAR CONTRATO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}Error al guardar{.}'.$sql; }
	}

	function agregarContrato($nombre,$id_empresa,$id_sucursal,$contrato,$link){

		$sql   = "INSERT INTO empleados_contratos (nombre,id_empresa,id_sucursal,contrato) VALUES ('$nombre','$id_empresa','$id_sucursal','$contrato')";
		$query = mysql_query($sql,$link);

		if($query){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id.'{.}'.$sql;
			mylog('AGREGAR CONTRATO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function actualizarContrato($nombre,$contrato,$id,$link){
		$sql   = "UPDATE empleados_contratos  SET nombre = '$nombre',contrato = '$contrato' WHERE id = '$id' AND id_empresa='$_SESSION[EMPRESA]'";
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ACTUALIZA CARGO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function actualTextoContrato($id,$texto,$link){
		$sql   = "UPDATE empleados_contratos  SET contrato = '$texto' WHERE id = '$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ACTUALIZA CONTRATO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function EliminarContrato($id,$link){
		$sql   = "UPDATE empleados_contratos SET activo = 0 WHERE id ='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ELIMINAR CARGO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	// CARGOS /////////////////////////////////////////////////////////////////////////////////////////
	function agregarCargo($cargo,$link){

		$sql   = "INSERT INTO empleados_cargos (nombre,id_empresa) VALUES('".$cargo."',".$_SESSION['EMPRESA'].")";
		$query = mysql_query($sql,$link);

		if($query){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR CARGO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function actualizaraCargo($cargo,$id,$link){
		$sql   = "UPDATE empleados_cargos  SET nombre = '$cargo' WHERE id = $id AND id_empresa=".$_SESSION['EMPRESA'];
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ACTUALIZA CARGO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function EliminarCargo($id,$link){
		$sql   = "UPDATE empleados_cargos SET activo = 0 WHERE id ='".$id."' AND id_empresa=".$_SESSION['EMPRESA'];
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ELIMINAR CARGO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function empleados_generaQR($elid,$link){

		$consul          = mysql_query("SELECT * FROM empleados WHERE id = $elid",$link);
		$nombre1         = ucwords(strtolower(mysql_result($consul,0,'nombre1')));
		$nombre2         = ucwords(strtolower(mysql_result($consul,0,'nombre2')));
		$apellido1       = ucwords(strtolower(mysql_result($consul,0,'apellido1')));
		$apellido2       = ucwords(strtolower(mysql_result($consul,0,'apellido2')));
		$nombre          = ucwords(strtolower(mysql_result($consul,0,'nombre')));
		$email_empresa   = mysql_result($consul,0,'email_empresa');
		$celular_empresa = mysql_result($consul,0,'celular_empresa');
		$empresa         = mysql_result($consul,0,'empresa');
		$cargo           = mysql_result($consul,0,'cargo');

		//OJO ESTE CODIGO DEBE IR PEGADO A LA MARGEN, SIN TABULACIONES//////////////////////
		$text = "BEGIN:VCARD
		VERSION:3.0
		FN:".$nombre."
		N:".$apellido1." ".$apellido2.";".$nombre1." ".$nombre2.";;;
		EMAIL;TYPE=INTERNET;TYPE=WORK:".$email_empresa."
		TEL;TYPE=CELL:".$celular_empresa."
		ORG:".$empresa."
		TITLE:".$cargo;
		/////////////////////////////////////////////////////////////////////////////////////

		//$img = "imagenes/qr/codigo.png";
		$bar   = QRcode::png($text,'../QR/'.$elid.'.png',4,2);  // Crea y guarda un png con el código QR
		$fp    = fopen('../QR/'.$elid.'.png', "rb");
		$tfoto = fread($fp, filesize('../QR/'.$elid.'.png'));
		$tfoto = addslashes($tfoto);

		fclose($fp);
		unlink('../QR/'.$elid.'.png');

		mysql_query("UPDATE empleados SET qrcode = '$tfoto' WHERE id = $elid" ,$link);
	}


	// EMPLEADO /////////////////////////////////////////////////////////////////////////////////////////
	function agregarEmpleado($tipo_id1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$username1,$id_empresa,$id_sucursal1,$ID,$acceso_sistema,$link){

		// VALIDAR LA CANTIDAD DE USUAIOS POR PLAN
		if ($acceso_sistema=='true') {
			$sql="SELECT COUNT(id) AS usuarios  FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND acceso_sistema='true' ";
			$query=mysql_query($sql,$link);
			$usuarios=mysql_result($query,0,'usuarios');
			if ($usuarios>$_SESSION['PLAN_USUARIOS']) {
				echo 'false{.}Su plan actual solo le permite tener '.$_SESSION['PLAN_USUARIOS'].' usuarios con acceso al sistema !';
				return;
			}
		}

		$nombre_completo = $nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;

		$password = md5("12345678");
		$sql = "INSERT INTO empleados (
					tipo_documento,
					documento,
					nombre1,
					nombre2,
					apellido1,
					apellido2,
					id_rol,
					id_cargo,
					username,
					password,
					acceso_sistema,
					id_sucursal,
					id_empresa
				)VALUES(
					'$tipo_id1',
					'$id1',
					'$nombre1',
					'$nombre2',
					'$apellido1',
					'$apellido2',
					'$rol',
					'$cargo',
					'$username1',
					'$password',
					'$acceso_sistema',
					'$id_sucursal1',
					'$id_empresa'
				)";

		$connectid = mysql_query($sql,$link);
		$id = mysql_insert_id($link);

		if($connectid){

			//EMPLEADO EN MODULO TERCERO
			$sql         = "SELECT COUNT(id) AS contTercero FROM terceros WHERE numero_identificacion='$id1' AND id_empresa=$id_empresa";
			$query       = mysql_query($sql,$link);
			$contTercero = mysql_result($query,0,'contTercero');

			if ($contTercero>0) {
				$sql = "UPDATE terceros
						SET id_tipo_identificacion = '$tipo_id1',
							nombre1 = '$nombre1',
							nombre2 = '$nombre2',
							apellido1 = '$apellido1',
							apellido2 = '$apellido2',
							nombre = '$nombre_completo',
							nombre_comercial='$nombre_completo',
							tercero_empleado = 'true'
						WHERE numero_identificacion='$id1' AND id_empresa=$id_empresa";
				$query = mysql_query($sql,$link);

				//ACTUALIZAR EL CAMPO SINC
				if ($query) {
					$sql   = "UPDATE empleados SET sinc_tercero='true',id_tercero='$id_tercero' WHERE id=$ID";
					$query = mysql_query($sql,$link);
				}
			}
			else{

				$nombre_completo = $nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;
				$sql = "INSERT INTO terceros (
							id_tipo_identificacion,
							nombre1,
							nombre2,
							apellido1,
							apellido2,
							nombre,
							nombre_comercial,
							numero_identificacion,
							id_empresa,
							tercero_empleado,
							tipo_proveedor,
							tipo_cliente)
						VALUES(
							'$tipo_id1',
							'$nombre1',
							'$nombre2',
							'$apellido1',
							'$apellido2',
							'$nombre_completo',
							'$nombre_completo',
							'$id1',
							'$id_empresa',
							'true',
							'Si',
							'Si')";
				$query      = mysql_query($sql,$link);
				$id_tercero = mysql_insert_id($link);

				//ACTUALIZAR EL CAMPO SINC
				if ($query) {
					$sql   = "UPDATE empleados SET sinc_tercero='true',id_tercero='$id_tercero' WHERE id=$id";
					$query = mysql_query($sql,$link);
				}
			}

			echo 'true{.}'.$id;
			mylog('AGREGAR EMPLEADO -> '.$sql,17,$link);
			mysql_query("INSERT INTO empleados_historial_vinculacion(id_usuario,fecha,motivo,id_empresa,id_sucursal)VALUES($id,now(),'IN','$id_empresa','$id_sucursal1')",$link);
			empleados_generaQR($id,$link);

			// insertarInformacionAdicional($ciudad_trabajo,
			// 							 $fondo_pension,
			// 							 $tipo_sangre,
			// 							 $alergico_medicamento,
			// 							 $cual_alergico_medicamento,
			// 							 $toma_medicamento,
			// 							 $cual_toma_medicamento,
			// 							 $grupo_sanguineo,
			// 							 $factor_rh,$id,$link);
		}
		else{ echo 'false{.}Error Enviando la Solicitud!'; }
	}

	function actualizarEmpleado($tipo_id1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$username1,$id_empresa,$id_sucursal1,$ID,$acceso_sistema,$link){
		
		// VALIDAR SI EL EMPLEADO TIENE SEGUNDO APELLIDO
		$apellido2 =  ($apellido2 == '')? ' ' : $apellido2;

		// VALIDAR LA CANTIDAD DE USUAIOS POR PLAN
		if ($acceso_sistema=='true') {
			$sql="SELECT COUNT(id) AS usuarios FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND acceso_sistema='true' ";
			$query=mysql_query($sql,$link);
			$usuarios=mysql_result($query,0,'usuarios');
			if ($usuarios>$_SESSION['PLAN_USUARIOS']) {
				echo 'false{.}Su plan actual solo le permite tener '.$_SESSION['PLAN_USUARIOS'].' usuarios con acceso al sistema !';
				return;
			}
		}

		$sql = "UPDATE empleados
				SET tipo_documento             = '$tipo_id1',
					documento                  = '$id1',
					nombre1                    = '$nombre1',
					nombre2                    = '$nombre2',
					apellido1                  = '$apellido1',
					apellido2                  = '$apellido2',
					id_rol                     = '$rol',
					id_cargo                   = '$cargo',
					username                   = '$username1',
					acceso_sistema             = '$acceso_sistema'
				WHERE id ='$ID'";
		$connectid = mysql_query($sql,$link);

		if($connectid){
			$nombre_completo = $nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;

			//EMPLEADO EN MODULO TERCERO
			$sqlTercero   = "SELECT COUNT(id) AS contTercero,id FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND numero_identificacion='$id1' LIMIT 0,1";
			$queryTercero = mysql_query($sqlTercero,$link);
			$contTercero  = mysql_result($queryTercero,0,'contTercero');
			$id_tercero   = mysql_result($queryTercero,0,'id');

			if ($contTercero>0) {

				$sql = "UPDATE terceros
						SET id_tipo_identificacion = '$tipo_id1',
							nombre1 = '$nombre1',
							nombre2 = '$nombre2',
							apellido1 = '$apellido1',
							apellido2 = '$apellido2',
							nombre = '$nombre_completo',
							nombre_comercial = '$nombre_completo',
							id_empresa = '$id_empresa',
							tercero_empleado = 'true'
						WHERE numero_identificacion='$id1' AND id_empresa=$id_empresa";
				$query = mysql_query($sql,$link);

				//ACTUALIZAR EL CAMPO SINC
				if ($query) {
					$sql   = "UPDATE empleados SET sinc_tercero='true',id_tercero='$id_tercero' WHERE id=$ID";
					$query = mysql_query($sql,$link);
				}
			}
			else{
				//INSERTAR EL EMPLEADO COMO TERCERO
				$nombre_completo = $nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;
				$sql = "INSERT INTO terceros (
							id_tipo_identificacion,
							nombre1,
							nombre2,
							apellido1,
							apellido2,
							nombre,
							nombre_comercial,
							numero_identificacion,
							id_empresa,
							tercero_empleado,
							tipo_proveedor,
							tipo_cliente)
						VALUES(
							'$tipo_id1',
							'$nombre1',
							'$nombre2',
							'$apellido1',
							'$apellido2',
							'$nombre_completo',
							'$nombre_completo',
							'$id1',
							'$id_empresa',
							'true',
							'Si',
							'Si')";
				$query=mysql_query($sql,$link);

				//ACTUALIZAR EL CAMPO SINC
				if ($query) {
					$id_tercero = mysql_insert_id($link);

					$sql   = "UPDATE empleados SET sinc_tercero='true',id_tercero='$id_tercero' WHERE id=$ID";
					$query = mysql_query($sql,$link);
				}
			}

			echo 'true{.}'.$ID;
			mylog('ACTUALIZAR EMPLEADO -> '.$sql,17,$link);
			empleados_generaQR($ID,$link);

			$sqlCheck = "SELECT id FROM empleados_adicional WHERE id_empleado = '$ID'";
			$rows = $GLOBALS['mysql']->num_rows($GLOBALS['mysql']->query($sqlCheck,$link));

			//SI NO LA TIENE LA INSERTAMOS
			if($rows < 1){

				// insertarInformacionAdicional($ciudad_trabajo,
				// 						 $fondo_pension,
				// 						 $tipo_sangre,
				// 						 $alergico_medicamento,
				// 						 $cual_alergico_medicamento,
				// 						 $toma_medicamento,
				// 						 $cual_toma_medicamento,
				// 						 $grupo_sanguineo,
				// 						 $factor_rh,$ID,$link);


			}
			else{

				//SI YA LA TIENE SOLO ACTUALIZAMOS

				// actualizarInformacionAdicional($ciudad_trabajo,
				// 						   $fondo_pension,
				// 						   $tipo_sangre,
				// 						   $alergico_medicamento,
				// 						   $cual_alergico_medicamento,
				// 						   $toma_medicamento,
				// 						   $cual_toma_medicamento,
				// 						   $grupo_sanguineo,
				// 						   $factor_rh,$ID,$link);

			}
		}
		else{ echo 'false{.}'; }
	}

	function insertarInformacionAdicional($ciudad_trabajo,
										 $fondo_pension,
										 $tipo_sangre,
										 $alergico_medicamento,
										 $cual_alergico_medicamento,
										 $toma_medicamento,
										 $cual_toma_medicamento,
										 $grupo_sanguineo,
										 $factor_rh,$id,$link){

		//INSERTAR LA INFORMACION ADICIONAL

			$sqlAdd="INSERT INTO empleados_adicional (
										id_empleado,
										ciudad_trabajo,
										fondo_pension,
										tipo_sangre,
										alergico_medicamento,
										cual_alergico_medicamento,
										toma_medicamento,
										cual_toma_medicamento,
										grupo_sanguineo,
										factor_rh
									)VALUES(
									    '$id',
										'$ciudad_trabajo',
										'$fondo_pension',
										'$tipo_sangre',
										'$alergico_medicamento',
										'$cual_alergico_medicamento',
										'$toma_medicamento',
										'$cual_toma_medicamento',
										'$grupo_sanguineo',
										'$factor_rh'
									)";
			$GLOBALS['mysql']->query($sqlAdd,$link);


	}

	function actualizarInformacionAdicional($ciudad_trabajo,
										   $fondo_pension,
										   $tipo_sangre,
										   $alergico_medicamento,
										   $cual_alergico_medicamento,
										   $toma_medicamento,
										   $cual_toma_medicamento,
										   $grupo_sanguineo,
										   $factor_rh,$ID,$link){

		$sqlAdd ="	UPDATE
							empleados_adicional
						SET
							ciudad_trabajo            = '$ciudad_trabajo',
							fondo_pension             = '$fondo_pension',
							tipo_sangre               = '$tipo_sangre',
							alergico_medicamento      = '$alergico_medicamento',
							cual_alergico_medicamento = '$cual_alergico_medicamento',
							toma_medicamento          = '$toma_medicamento',
							cual_toma_medicamento     = '$cual_toma_medicamento',
							grupo_sanguineo           = '$grupo_sanguineo',
							factor_rh                 = '$factor_rh'

						WHERE
							id_empleado ='".$ID."'";

		$GLOBALS['mysql']->query($sqlAdd,$link);

	}

	function resetPass($ID,$link){

		$password = md5("12345678");
		$sql      = "UPDATE empleados SET password='".$password."' WHERE id ='".$ID."' AND id_empresa=".$_SESSION['EMPRESA'];
		$query    = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$ID;
			mylog('RESET PASSWORD -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function updateToken($ID,$link){
		$token = password_hash(date("Y-m-d H:i:s")."PASSWORD_DEFAULT", PASSWORD_DEFAULT );
		$sql      = "UPDATE empleados SET token='".$token."' WHERE id ='".$ID."' AND id_empresa=".$_SESSION['EMPRESA'];
		$query    = mysql_query($sql,$link);
		if ($query) {
			echo "<script>
					document.getElementById('token').value = '$token';
					// console.log('$sql');
					MyLoading2('off')
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'Error al generar token',duracion:3000})
				</script>";
		}

		// if($query){
		// 	echo 'true{.}'.$ID;
		// 	mylog('RESET PASSWORD -> '.$sql,17,$link);
		// }
		// else{ echo 'false{.}'; }
	}

	function EliminarEmpleado($id,$id_empresa,$sucursal,$link){
		$sql   = "UPDATE empleados SET activo = 0 WHERE id ='$id' AND id_empresa = '$id_empresa'";
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ELIMINAR EMPLEADO -> '.$sql,17,$link);
			mysql_query("INSERT INTO empleados_historial_vinculacion(id_usuario,fecha,motivo,id_empresa,id_sucursal)VALUES($id,now(),'OUT','".$_SESSION['EMPRESA']."','".$_SESSION['SUCURSAL']."')",$link);
		}
		else{ echo 'false{.}'; }
	}

	function RestaurarEmpleado($id,$id_empresa,$sucursal,$link){
		$sql   = "UPDATE empleados SET activo = 1, id_empresa = $id_empresa, id_sucursal = $sucursal, id_rol = 0, id_cargo = 0 WHERE id ='".$id."'";
		$query = mysql_query($sql,$link);

		if($query){
			echo 'true{.}'.$id;
			mylog('ACTIVAR EMPLEADO -> '.$sql,17,$link);
			mysql_query("INSERT INTO empleados_historial_vinculacion(id_usuario,fecha,motivo,id_empresa,id_sucursal)VALUES($id,now(),'IN','$id_empresa','$sucursal')",$link);
			empleados_generaQR($id,$link);
		}
		else{ echo 'false{.}'; }
	}

	function checkIdentificacion($id1,$id_empresa,$id_sucursal1,$link){

		$sql    = "SELECT id,id_sucursal,id_empresa,activo,nombre,empresa,sucursal FROM empleados WHERE documento='$id1' AND id_empresa = '$id_empresa' AND activo=1 AND id_sucursal=$id_sucursal1";
		$result = mysql_query($sql,$link);

		$Existe        = 'false';
		$EstaActivo    = 'false';
		$MismaEmpresa  = 'false';
		$MismaSucursal = 'false';

		if(mysql_num_rows($result)){
			$Existe      = 'true';
			$id          = mysql_result($result,$i,"id");
			$activo      = mysql_result($result,$i,"activo");
			$nombre      = mysql_result($result,$i,"nombre");
			$id_sucursal = mysql_result($result,$i,"id_sucursal");
			$id_empresa  = mysql_result($result,$i,"id_empresa");
			$Sucursal    = mysql_result($result,$i,"sucursal");
			$Empresa     = mysql_result($result,$i,"empresa");


			if($activo == 1){ $EstaActivo = 'true'; }

			if($id_empresa == $id_empresa){
				$MismaEmpresa = 'true';
				if($id_sucursal == $id_sucursal1){ $MismaSucursal = 'true'; }
			}
		}
		echo  $Existe.'{.}'.$EstaActivo.'{.}'.$MismaEmpresa.'{.}'.$MismaSucursal.'{.}'.$id.'{.}'.$nombre.'{.}'.$Empresa.'{.}'.$Sucursal;
	}

	function checkUsername($user,$link,$id){

		$sql    = "SELECT id,username FROM empleados WHERE username='".$user."' AND activo=1 AND id!=".$id." AND id_empresa=".$_SESSION['EMPRESA'] ;
		$result = mysql_query($sql,$link);

		if($result){
			$num = mysql_numrows($result);

			if($num>=1){
				$id = mysql_result($result,$i,"username");
				echo 'true{.}'.$id;
			}
			else{ echo 'true{.}false'; }
		}
		else{ echo 'false{.}'; }
	}

	function agregarDocumento($cargo,$link){

		$sql       = "INSERT INTO empleados_tipo_documento (nombre,id_empresa) VALUES('".$cargo."',".$_SESSION['EMPRESA'].")";
		$connectid = mysql_query($sql,$link);

		if($connectid){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR TIPO DOCUMENTO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

	function actualizarDocumento($id,$cargo,$link){

		$sql       = "UPDATE empleados_tipo_documento  SET nombre = '$cargo' WHERE id = $id AND id_empresa=".$_SESSION['EMPRESA'];
		$connectid = mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id;
			mylog('ACTUALIZA TIPO DOCUMENTO -> '.$sql,17,$link);
		}
		else{ echo 'false{.}'; }
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////// FUNCIONES HORAS EXTRAS DE PERSONAL /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	function cargaHorasExtras($id,$fechai,$fechaf,$link){

		////	EXTRAE LA INFORMACION DEL EMPLEADO
		$sql="SELECT
					telefono1,
					cargo,
					documento,
					nombre1,
					apellido1,
					empleados_horas_extras.fechai as fechai,
					empleados_horas_extras.fechas as fechas,
					empleados_horas_extras.horai as horai,
					empleados_horas_extras.horas as horas,
					empleados_horas_extras.med as med,
					empleados_horas_extras.men as men,
					empleados_horas_extras.medf as medf,
					empleados_horas_extras.menf as menf,
					empleados_horas_extras.total as total
				FROM
					 empleados_horas_extras
				INNER JOIN
					empleados on empleados_horas_extras.cedula=empleados.documento
				WHERE
					empleados_horas_extras.total!=0 AND
					empleados_horas_extras.cedula=$id AND
					fechai BETWEEN '$fechai' AND '$fechaf'";
		//echo $sql;
		$del = mysql_query($sql);

		if(mysql_num_rows($del)){
			$cow   = mysql_fetch_array($del);
			$reced = $cow['documento'];

			echo 'true{.}<br>
			<table width="720" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
						<tr >
							<td width="10"> </td>
							<td width="103" bgcolor="#CDDBF0">Cedula</td>
							<td width="271" bgcolor="#CDDBF0">Nombres y Apellidos</td>
							<td width="153" bgcolor="#CDDBF0">Cargo</td>
							<td width="132" bgcolor="#CDDBF0">Telefono</td>
						</tr>
						<tr >
							<td width="10"> </td>
							<td width="103" >'.$id.'</td>
							<td width="271" >'.$cow['nombre1'].' '.$cow['apellido1'].'</td>
							<td width="153" >'.$cow['cargo'].'&nbsp;</td>
							<td width="132" >'.$cow['telefono1'].'&nbsp;</td>
						</tr>
				</table><br>';

			echo '<table width="720" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
					  <tr >
						<td width="10">&nbsp;</td>
						<td width="165" bgcolor="#CDDBF0">DIA ENTRADA</td>
						<td width="50" bgcolor="#CDDBF0">HORA</td>
						<td width="180" bgcolor="#CDDBF0">DIA SALIDA</td>
						<td width="50" bgcolor="#CDDBF0">HORA</td>
						<td width="50" bgcolor="#CDDBF0">M.E.D</td>
						<td width="50" bgcolor="#CDDBF0">M.E.N</td>
						<td width="50" bgcolor="#CDDBF0">M.E.D.F</td>
						<td width="50" bgcolor="#CDDBF0">M.E.N.F</td>
					  </tr>';

			$del = mysql_query($sql);
			while($row = mysql_fetch_array($del)){
				//// MUESTRA DETALLES DE HORAS EXTRAS
				echo '<tr >
						<td width="10">&nbsp;</td>
						<td width="165" >'.fecha($row['fechai']).'</td>
						<td width="50" >'.$row['horai'].'</td>
						<td width="180" >'.fecha($row['fechas']).'</td>
						<td width="50" >'.$row['horas'].'</td>
						<td width="50" >'.number_format($row['med']).'</td>
						<td width="50" >'.number_format($row['men']).'</td>
						<td width="50" >'.number_format($row['medf']).'</td>
						<td width="50" >'.number_format($row['menf']).'</td>
					  </tr>';
			}

			echo '</table><br>';
			//// MUESTRA TOTALES DE HORAS EXTRAS
			$sql2="SELECT
						sum(empleados_horas_extras.med) as med,
						sum(empleados_horas_extras.med_valor) as med_valor,
						sum(empleados_horas_extras.men) as men,
						sum(empleados_horas_extras.men_valor) as men_valor,
						sum(empleados_horas_extras.medf) as medf,
						sum(empleados_horas_extras.medf_valor) as medf_valor,
						sum(empleados_horas_extras.menf) as menf,
						sum(empleados_horas_extras.menf_valor) as menf_valor,
						sum(empleados_horas_extras.total) as total
					FROM
						 empleados_horas_extras
					WHERE
						empleados_horas_extras.total!=0 AND
						empleados_horas_extras.cedula=$id AND
						fechai BETWEEN '$fechai' AND '$fechaf'
					GROUP BY cedula";
			//echo $sql2;
			$resultado = mysql_query($sql2);

			while($row = mysql_fetch_array($resultado)){
				echo '
				<table width="500" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
					<tr  >
					<td width="10"> </td>
					<td width="300" bgcolor="#CDDBF0">TOTAL MINUTOS DIURNOS</td>
					<td width="60">'.$row['med'].'&nbsp;</td>
					<td width="100">$'.number_format($row['med_valor']).'&nbsp;</td>
				  </tr>
				  <tr >
					<td width="10"> </td>
					<td  bgcolor="#CDDBF0">TOTAL MINUTOS NOCTURNOS</td>
					<td >'.$row['men'].'&nbsp;</td>
					<td>$'.number_format($row['men_valor']).'&nbsp;</td>
				  </tr>
					<tr >
					<td width="10"> </td>
					<td bgcolor="#CDDBF0">TOTAL MINUTOS DIURNOS FESTIVOS</td>
					<td>'.$row['medf'].'&nbsp;</td>
					<td>$'.number_format($row['medf_valor']).'&nbsp;</td>
				  </tr>
					 <tr >
					 <td width="10"> </td>
					<td bgcolor="#CDDBF0">TOTAL MINUTOS NOCTURNOS FESTIVOS</td>
					<td>'.$row['menf'].'&nbsp;</td>
					<td>$'.number_format($row['menf_valor']).'&nbsp;</td>
				  </tr>
					 <tr >
					 <td width="10"> </td>
					   <td bgcolor="#EEE">TOTAL</td>
					   <td >&nbsp;</td>
					   <td>$'.number_format($row['total']).'</td>
					</tr>
				</table>';
			}
		}
		else{ echo "false{.}Por Favor Verifique Su numero de cedula{.}".$sql1; }
	}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////  !!!!  OLD !!!!!! FUNCIONES HORAS EXTRAS DE PERSONAL ]  !!!!  OLD !!!!!!  ////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		function fecha($valor){
		list($anio,$mes,$dia)=split('-', $valor);
		$man   = date("w",mktime(0,0,0,$mes,$dia,$anio));
		$dias  = array("Dom","Lun","Mar","Mie","Jue","Vie","Sab");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$fec   = $dias[$man].' '.$dia.' de '.$meses[$mes-1].' '.$anio;
		return $fec;
	}

	function fich($valor){
		list($hor,$min,$seg) = split(":", $valor);
		$fer = date("h:i a",mktime($hor,$min,$seg,10,10,2005));
		return $fer;
	}

	function puntos($horae,$fechaE,$hora,$fechaS,$cl){
		list($ano,$mes,$dia) = split('-', $fechaS);
		list($hor,$min,$sec) = split(':',$hora);
		$extra = mktime($hor,$min,$seg,$mes,$dia,$ano); //unix salida
		list($eanio,$emes,$edia) = split('-', $fechaE);

		switch($cl){
			case 1:
				$prin = mktime(19,00,00,$emes,$edia,$eanio); //unix parte
				$ti   = round(($extra - $prin)/60);

				if($ti >=180){ $tot = 180; }
				else if($ti>0){ $tot = $ti; }
				else{ $tot = $ti; }
			break;
			case 2:
				$limite = mktime(22,00,00,$emes,$edia,$eanio); //unix parte
				$mi = round(($extra - $limite)/60);
				if($mi < 0){$tot = 0;}else{$tot = $mi;}
			break;
		}
		return $tot;
	}

	function cargaHorasExtrasOLD($id,$fechai,$fechaf,$link){
		////	REVISA LA TABLA DE DIAS FESTIVOS
		$fes = "SELECT * FROM tfestivo ORDER BY dia ASC ";
		$fet = mysql_query($fes,$link);
		$l   = 1;

		while($fest=mysql_fetch_array($fet)){ //pone matriz de fecha festiva
			$festivo[$l] = $fest[dia];
			$l++;
		}

		////	EXTRAE LA INFORMACION DEL EMPLEADO
		$sql1 = "SELECT * FROM empleados WHERE documento = '$id'";
		$del  = mysql_query($sql1 ,$link);

		if(mysql_num_rows($del)){
			$cow   = mysql_fetch_array($del);
			$reced = $cow['documento'];

			echo 'true{.}<br>
			<table width="720" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
						<tr >
							<td width="10"> </td>
							<td width="103" bgcolor="#CDDBF0">Cedula</td>
							<td width="271" bgcolor="#CDDBF0">Nombres y Apellidos</td>
							<td width="153" bgcolor="#CDDBF0">Cargo</td>
							<td width="132" bgcolor="#CDDBF0">Telefono</td>
						</tr>
						<tr >
							<td width="10"> </td>
							<td width="103">'.$reced.'</td>
							<td width="271">'.$cow['nombre1'].' '.$cow['nombre2'].' '.$cow['apellido1'].' '.$cow['apellido2'].'</td>
							<td width="153">'.$cow['cargo'].'&nbsp;</td>
							<td width="132">'.$cow['telefono1'].'&nbsp;</td>
						</tr>
				</table><br>';
			echo '<table width="720" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
				  <tr >
					<td width="10">&nbsp;</td>
					<td width="165" bgcolor="#CDDBF0">DIA ENTRADA</td>
					<td width="70" bgcolor="#CDDBF0">HORA</td>
					<td width="180" bgcolor="#CDDBF0">DIA SALIDA</td>
					<td width="72" bgcolor="#CDDBF0">HORA</td>
					<td width="50" bgcolor="#CDDBF0">H.E.D</td>
					<td width="50" bgcolor="#CDDBF0">E.E.N</td>
					<td width="50" bgcolor="#CDDBF0">H.E.D.F</td>
					<td width="50" bgcolor="#CDDBF0">E.E.N.F</td>
				  </tr>';

			////	EXTRACCION DE REGISTROS DE LLEGADAS Y SALIDAS
			$sql2 = "SELECT * FROM vista_horas_extras WHERE cedula = $reced AND fechaE BETWEEN '$fechai' AND '$fechaf'";
			$hex  = mysql_query($sql2,$link);
			$fc   = 0;
			$fn   = 0;
			while($row = mysql_fetch_array($hex)){

				list($san,$sme,$sdi) = split('-',$row['fechaE']);
				$vari = array_search($row['fechaE'], $festivo);
				echo ' <tr >
							<td></td>
							<td>'.fecha($row['fechaE']).' </td>
							<td>'.fich($row['horaE']).'</td>
							<td>'.fecha($row['fechaS']).' </td>
							<td>'.fich($row['horaS']).'</td>';

				if($vari){ //Festivo pue
					echo'	<td>0</td>
								<td>0</td>
								<td>'.$do = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],1).'</td>
								<td>'.$lo = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],2).'</td>
							</tr>';
					$fc = $fc + $do;
					$fn = $fn + $lo;

				}else{
					$dm = date("w",mktime(0,0,0,$sme,$sdi,$san));
					if($dm==0){
						echo'	<td>0</td>
								<td>0</td>
								<td>'.$do = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],1).'</td>
								<td>'.$lo = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],2).'</td>
							</tr>';
						$fc = $fc + $do;
						$fn = $fn + $lo;
					}else{
						echo'	<td>'.$do = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],1).'</td>
								<td>'.$lo = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],2).'</td>
								<td>0</td>
								<td>0</td>
							</tr>';
						$to = $to + $lo;
						$no = $no + $do;
					}
				}
			}

			$sql3 		= "SELECT salario_minimo FROM configuracion_global";
			$sal 		= mysql_query($sql3,$link);
			$row1 		= mysql_fetch_array($sal);
			$valor_min 	= $row1['salario_minimo']/30/8/60;

			echo '</table><br>
			<table width="720" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
				<tr  >
					<td width="10"> </td>
					<td  bgcolor="#CDDBF0"> H.E.DIURNA: </td>
					<td  bgcolor="#CDDBF0"> H.E.NOCTURNA:</td>
					<td  bgcolor="#CDDBF0"> H.E.DIURNA FEST: </td>
					<td  bgcolor="#CDDBF0"> H.E.NOCTURNA FE: </td>
				</tr>
				<tr>
					<td width="10"> </td>';
			$sql4 		= "SELECT * FROM configuracion_factor_horas_extras";
			$config		= mysql_query($sql4,$link);
			while ($row2 = mysql_fetch_array($config)){
				switch ($row2['tipo']) {
						case "H.E.DIURNA":
							$factor_diurna 	= $row2['valor'];
							$valor_diurna	= $factor_diurna * $valor_min;
							echo '<td > $'.number_format($valor_diurna,2).'&nbsp;</td>';
							break;

						case "H.E.NOCTURNA":
							$factor_nocturna = $row2['valor'];
							$valor_nocturna	= $factor_nocturna * $valor_min;
							echo '<td > $'.number_format($valor_nocturna,2).'&nbsp;</td>';
							break;

						case "H.E.DIURNA FEST":
							$factor_diurna_fest = $row2['valor'];
							$valor_diurna_fest	= $factor_diurna_fest * $valor_min;
							echo '<td > $'.number_format($valor_diurna_fest,2).'&nbsp;</td>';
							break;

						case "H.E.NOCTURNA FE":
							$factor_nocturna_fest 	= $row2['valor'];
							$valor_nocturna_fest	= $factor_nocturna_fest * $valor_min;
							echo '<td > $'.number_format($valor_nocturna_fest,2).'&nbsp;</td>';
							break;
					}
			}

			echo ' </tr></table><br>
			<table width="500" border="0" cellspacing="3" cellpadding="3" style="font-size:12px">
				<tr  >
				<td width="10"> </td>
				<td width="300" bgcolor="#CDDBF0">TOTAL MINUTOS DIURNOS</td>
				<td width="60">'.$no.'&nbsp;</td>';
				$NZR1 = $no * $valor_diurna; echo'
				<td width="100">$'.number_format($NZR1).'&nbsp;</td>
			  </tr>
			  <tr >
				<td width="10"> </td>
				<td  bgcolor="#CDDBF0">TOTAL MINUTOS NOCTURNOS</td>
				<td >'.$to.'&nbsp;</td>';
				$NZR2 = $to * $valor_nocturna; echo'
				<td>$'.number_format($NZR2).'&nbsp;</td>
			  </tr>
				<tr >
				<td width="10"> </td>
				<td bgcolor="#CDDBF0">TOTAL MINUTOS DIURNOS FESTIVOS</td>
				<td>'.$fc.'&nbsp;</td>';
				$NZR3 = $fc * $valor_diurna_fest; echo'
				<td>$'.number_format($NZR3).'&nbsp;</td>
			  </tr>
				 <tr >
				 <td width="10"> </td>
				<td bgcolor="#CDDBF0">TOTAL MINUTOS NOCTURNOS FESTIVOS</td>
				<td>'.$fn.'&nbsp;</td>';
				$NZR4 = $fn * $valor_nocturna_fest; echo'
				<td>$'.number_format($NZR4).'&nbsp;</td>
			  </tr>
				 <tr >
				 <td width="10"> </td>
				   <td bgcolor="#EEE">TOTAL</td>
				   <td bgcolor="#EEE">&nbsp;</td>
				   <td>$'.number_format($NZR1 + $NZR2 + $NZR3 + $NZR4).'</td>
				</tr>
			</table>';

		}
		else{ echo "false{.}Por Favor Verifique Su numero de cedula{.}".$sql1; }
	}

	// ADJUNTAR INVENTARIO A EMPLEADO JHON-ERICK////////////////////////////////////////
	function actualizar_inventario_empleado($id_empleado,$id_inventario,$link){

		if($id_empleado!=0){
			$sql       = "UPDATE inventarios SET id_usuario_encargado=".$id_empleado.", fecha_asignacion_usuario=now(), hora_asignacion_usuario=now() WHERE id=".$id_inventario;
			$connectid = mysql_query($sql,$link);

			if (!$connectid){die('no Actualizo'.mysql_error()."sentencia = ".$sql);}

			echo'<script>
					//alert("Se ha Asignado un Nuevo Inventario.");
					Inserta_Div_EmpleadoInventario('.$id_inventario.');
					Elimina_Div_EquiposEmpleadoInventarios('.$id_inventario.');
				</script>';
		}
		else{
			$sql       = "UPDATE inventarios SET id_usuario_encargado=".$id_empleado.", fecha_asignacion_usuario=0, hora_asignacion_usuario=0 WHERE id=".$id_inventario;
			$connectid = mysql_query($sql,$link);

			if (!$connectid){die('no Actualizo'.mysql_error()."sentencia = ".$sql);}
			echo'<script>Elimina_Div_EmpleadoInventario('.$id_inventario.');</script>';
		}
	}

	function consultaSizeImageDocumentEmpleado($nombre,$id_host){

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$nombre)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$nombre;
		}
		else{
			$url = '';
		}

		list($ancho, $alto, $tipo, $atributos) = getimagesize($url);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;

		echo json_encode($size);
	}

	function ventanaVerImagenDocumentoEmpleado($nombreImage,$nombreDocumento,$type,$id_host){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$nombreImage)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($type != 'pdf'){
			echo'<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
						<a href="'.$url.'" download="'.$nombreDocumento.'" title="Click para descargar">
							<img src="'.$url.'" id="imagenItems">
						</a>
					</div>
				</div>
				<script>
					document.getElementById("imagenItems").oncontextmenu = function(){ return false; }
				</script>';
		}
		else{
			echo'<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<iframe src="'.$url.'" id="iframeViewDocumentItems"></iframe>
					</div>
				</div>
				<script>

					cambiaViewPdf();
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeViewDocumentItems");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function descargarArchivo($id_host,$nombreDocumento,$nombreRandomico){
		$enlace          = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$nombreRandomico;
		$nombreDocumento = str_replace(' ', '_', $nombreDocumento);

		if (file_exists($enlace)) {
			// header('Content-Disposition: attachment; filename='.basename($nombreDocumento));
			header('Content-Disposition: attachment; filename='.$nombreDocumento);
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: '.filesize($enlace));
		    ob_clean();
		    flush();
		    readfile($enlace);
	    }
	    else{ echo "Error, el archivo no se encuentra almacenado"; }
	    exit;
	}

	function option_departamento($id_pais, $id_empresa, $id_empleado, $render, $link){

		if($render != ''){
			$render = '_'.$render;
		}

		$sqlDepartamentoBd   = "SELECT id_departamento$render FROM empleados WHERE activo=1 AND id='$id_empleado' AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryDepartamentoBd = mysql_query($sqlDepartamentoBd,$link);

		$campoResult   = 'id_departamento'.$render;
		$id_departamentoBd   = mysql_result($queryDepartamentoBd, 0, $campoResult);

		$option = "<select class=\"myfield\" name=\"departamento".$render."\" type=\"text\" id=\"departamento".$render."\" onchange=\"optionCiudad('contenedor_ciudad$render',this.value,$id_empleado)\">
						<option value=\"\">Seleccione...</option>";

		if($id_pais > 0){
			$sqlDepartamento   = "SELECT id, departamento FROM ubicacion_departamento WHERE activo=1 AND id_pais='$id_pais' ORDER BY departamento ASC";
			$queryDepartamento = mysql_query($sqlDepartamento,$link);

			while ($row = mysql_fetch_assoc($queryDepartamento)) {
				$selected = ($row['id'] == $id_departamentoBd)? "selected": "";
				$option  .= "<option value=\"$row[id]\" $selected>$row[departamento]</option>";
			}
		}

		echo 	$option."
			</select>
			<script>optionCiudad('contenedor_ciudad$render','$id_departamentoBd','$id_empleado');</script>";
	}

	function option_ciudad($id_departamento, $id_empresa, $id_empleado,$render,$link){

		if($render != ''){
			$render = '_'.$render;
		}

		$sqlCiudadBd   = "SELECT id_ciudad$render FROM empleados WHERE activo=1 AND id='$id_empleado' AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryCiudadBd = mysql_query($sqlCiudadBd,$link);

		$campoResult   = 'id_ciudad'.$render;
		$id_ciudadBd   = mysql_result($queryCiudadBd, 0, $campoResult);

		$option = "<select class=\"myfield\" name=\"ciudad".$render."\" type=\"text\" id=\"ciudad".$render."\">
						<option value=\"\">Seleccione...</option>";

		if($id_departamento > 0){
			$sqlCiudad   = "SELECT id, ciudad FROM ubicacion_ciudad WHERE activo=1 AND id_departamento='$id_departamento' ORDER BY ciudad ASC";
			$queryCiudad = mysql_query($sqlCiudad,$link);

			while ($row = mysql_fetch_assoc($queryCiudad)) {
				$selected = ($row['id'] == $id_ciudadBd)? "selected": "";
				$option  .= "<option value=\"$row[id]\" $selected>$row[ciudad]</option>";
			}
		}

		echo $option.'</select>';
	}

	function select_informacion_parentesco($parentesco,$id_empleado,$link){

		//CON ESTE ALGORITMO SI EL PARENTESCO ES UNICO POR EJ: MADRE PADRE SOLO PERMITE QUE SE CARGUE EN UN SOLO CONTACTO

		echo '<select onchange="" style="width:150px" id="grillaContactos_id_parentesco" name="grillaContactos_id_parentesco" class="myfield">';
		echo '<option value="">Seleccione...</option>';
			if($id_empleado > 0){

				if(isset($parentesco)){
					//SI ES VENTANA EDITAR
					$sqlP   = "SELECT nombre,unico FROM configuracion_tipos_contacto WHERE id = '$parentesco' AND activo = 1";
					$queryP = mysql_query($sqlP,$link);
					$unico  = mysql_result($queryP,0,'unico');

					$parent_contacto = mysql_result($queryP,0,'nombre');

                    if($unico == 'si'){
                    	//SOLO EL CONTACTO QUE TIENE EL PARENTESCO UNICO LO MUESTRA EN EL SELECT
						$option .= '<option value="'.$parentesco.'" selected>'.$parent_contacto.'</option>';
					}
				}

				$sql   = "SELECT C.id AS id_par,C.nombre,C.unico,E.id
							FROM configuracion_tipos_contacto  AS C LEFT JOIN empleados_informacion_contacto AS E ON(
								C.id = E.id_parentesco
								AND E.id_empleado='$id_empleado'
								AND E.activo=1
								)
							WHERE C.activo=1 GROUP BY C.nombre";
				$query = mysql_query($sql,$link);

				while($row = mysql_fetch_assoc($query)){

					//SI EL PARENTESCO ES UNICO NO SE CARGA EN EL SELECT DE LOS DEMAS CONTACTOS

					if($row['unico'] == 'si' && $row['id']>0){ continue; }

					$selected='';

					if($row['id_par'] == $parentesco){
						$selected = 'selected';
					}

					$option .= '<option value="'.$row['id_par'].'" '.$selected.'>'.$row['nombre'].'</option>';

				}

			}
			else{
				$sql1   = "SELECT id,nombre FROM configuracion_tipos_contacto WHERE activo = 1";
				$query1 = mysql_query($sql1,$link);

				while($row1 = mysql_fetch_assoc($query1)){
					$option .= '<option value="'.$row1['id'].'">'.$row1['nombre'].'</option>';
				}
			}

		echo $option.'</select>';
	}

	function select_contacto_principal($id_contacto,$id_empleado,$opcion,$link){

		//REDIMENSIONAR LA VENTANA SI MUESTRA O NO EL COMBO

		if($opcion == 'Vupdate'){
			$scriptNo = 'Win_Editar_grillaContactos.setHeight(295);';
			$scriptSi = 'Win_Editar_grillaContactos.setHeight(310);';
		}
		else if($opcion == 'Vagregar'){
			$scriptNo = 'Win_Agregar_grillaContactos.setHeight(295);';
			$scriptSi = 'Win_Agregar_grillaContactos.setHeight(310);';
		}

		$sqlP   = "SELECT id,contacto_principal FROM empleados_informacion_contacto WHERE id_empleado = '$id_empleado' AND activo = 1 AND contacto_principal = 'si'";
		$queryP = mysql_query($sqlP,$link);

		$rows = mysql_num_rows($queryP);

		echo '<select onchange="" style="width:150px" id="grillaContactos_contacto_principal" name="grillaContactos_contacto_principal" class="myfield">';

		if($rows > 0){

			//SI YA HAY UN CONTACTO PRINCIPAL

			while($row = mysql_fetch_array($queryP)){

				if($row['id'] == $id_contacto){

					//SI ES EL CONTACTO PRINCIPAL

					echo '	  <option value="">Seleccione...</option>
							  <option value="si" selected>Si</option>
							  <option value="no">No</option>
						  </select>
						  <script>
						  	  '.$scriptSi.'
						  </script>';
				}
				else{

					//SI NO ES EL PRINCIPAL

					echo '	  <option value="no">No</option>
						  </select>
							</select>
						  <script>
						  	  document.getElementById("grillaContactos_contacto_principal").value="no";
						  	  document.getElementById("EmpLabel_grillaContactos_contacto_principal").style.display ="none";
						  	  document.getElementById("DIV_grillaContactos_contacto_principal").style.display ="none";
						  	  '.$scriptNo.'
						  </script>';
				}

			}
			exit;
		}

		$sql2     = "SELECT id,contacto_principal FROM empleados_informacion_contacto WHERE id = '$id_contacto' AND activo = 1";
		$query2   = mysql_query($sql2,$link);
		$contacto = mysql_result($query2,0,'contacto_principal');

		$scriptSelect = '';

		if($opcion == 'Vupdate'){
			$scriptSelect = 'document.getElementById("grillaContactos_contacto_principal").options.namedItem("'.$contacto.'").selected = "true";';
		}


		//SI NO HAY PRINCIPAL

		echo '
			  	 <option value="">Seleccione...</option>
			 	 <option id="si" value="si">Si</option>
			  	 <option id="no" value="no">No</option>
			  </select>
			  <script>
			  	  '.$scriptSelect.'
			  	  '.$scriptSi.'
			  </script>';
	}

	function trasladar_empleado_sucursal($id_empleado,$id_nueva_sucursal,$fecha_inicio,$fecha_final,$id_empresa,$link){
		$id_usuario=$_SESSION['IDUSUARIO'];

		// CONSULTAR EL ID DE LA SUCURSAL ANTERIOR
		$sql="SELECT id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		$query=mysql_query($sql,$link);
		$id_sucursal_old=mysql_result($query,0,'id_sucursal');

		// VALIDAR QUE NO SE TRASLADE A LA MISMA SUCURSAL QUE YA ESTA
		if ($id_sucursal_old == $id_nueva_sucursal) {
			echo '<script>
					MyLoading2("off");
				</script>';
			exit;
		}

		// INSERTAR EL REGISTRO EN LA TABLA DE TRASLADOS DE LA SUCURSAL
		$sql="INSERT INTO empleados_sucursales_traslados(fecha_inicio,fecha_final,id_empleado,id_usuario,id_sucursal,id_empresa)
				VALUES ('$fecha_inicio','$fecha_final','$id_empleado','$id_usuario','$id_sucursal_old','$id_empresa')";
		$query=mysql_query($sql,$link);

		// ACTUALIZAR LA SUCURSAL DEL EMPLEADO Y DEL ULTIMO CONTRATO DISPONIBLE QUE TENGA EL EMPLEADO
		$sql="UPDATE empleados SET id_sucursal = '$id_nueva_sucursal' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado ";
		$query=mysql_query($sql,$link);

		// ACTUALIZAR EL CONTRATO DEL EMPLEADO
		$sql="UPDATE empleados_contratos SET id_sucursal = '$id_nueva_sucursal' WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND estado = 0";
		$query=mysql_query($sql,$link);

		// CONSULTAR LOS TRASLADOS DE SUCURSALES
		$sql="SELECT fecha_inicio,fecha_final,sucursal,usuario FROM empleados_sucursales_traslados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$tbody.='<tr><td class="filaDivs" >'.$row['fecha_inicio'].'</td><td class="filaDivs" >'.$row['fecha_final'].'</td><td class="filaDivs" title="'.$row['sucursal'].'">'.$row['sucursal'].'</td><td class="filaDivs" title="'.$row['usuario'].'">'.$row['usuario'].'</td></tr>';
		}
		$fecha_inicio_campo = date('Y-m-d', strtotime (" $fecha_final +1 day"));
		//MOSTAR LOS RESULTADOS DEL PROCEDIMIENTO
		echo '<script>
				document.getElementById("tbody").innerHTML=\''.$tbody.'\';
				document.getElementById("fecha_inicio").value = "'.$fecha_inicio_campo.'";
				document.getElementById("fecha_final").value  = "'.(date('Y-m-d',strtotime (" $fecha_inicio_campo +1 day")) ).'";
				Win_Ventana_traslado.close();
				MyBusquedaEmpleados();
				Win_Agregar_Empleado.close();
				MyLoading2("off");
			</script>';
	}

?>