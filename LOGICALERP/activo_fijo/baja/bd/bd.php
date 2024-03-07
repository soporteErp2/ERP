<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	//===================== VARIABLES PRINCIPALES DE SESION ====================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if(isset($id)){
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if($opc <> 'actualizaFechaDocumento'){
			verificaCierre($id,'fecha_inicio',$tablaPrincipal,$id_empresa,$link);
		}
	}

	switch($opc){
		case 'buscarCliente':
			buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'buscarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($id,$idArticulo,$id_empresa,$opcGrillaContable,$codigo_activo,$deterioroAcumulado,$valorActual,$valorDeterioro,$mysql);
			break;

		case 'deleteArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			deleteArticulo($opcGrillaContable,$idArticulo,$cont,$id,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'retrocederArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
		 	retrocederArticulo($opcGrillaContable,$idArticulo,$cont,$id,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'actualizaFechaDocumento':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaFechaDocumento($id,$fecha,$tablaPrincipal,$opcGrillaContable,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($opcGrillaContable,$id,$tablaPrincipal,$tablaInventario,$id_empresa,$idSucursal,$idBodega,$mysql);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($opcGrillaContable,$id,$tablaPrincipal,$tablaInventario,$id_empresa,$id_sucursal,$mysql);
			break;

		case 'guardarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($idBaja,$idArticulo,$idSucursal,$consecutivo,$cont,$opcGrillaContable,$tablaInventario,$id_empresa,$mysql);
			break;

		case 'actualizaArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($idBaja,$idArticulo,$idSucursal,$cont,$opcGrillaContable,$tablaInventario,$idInsertArticulo,$idTablaPrincipal,$id_empresa,$mysql);
			break;

		case 'guardarObservacion':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($opcGrillaContable,$id,$tablaPrincipal,$tablaInventario,$id_empresa,$id_sucursal,$mysql);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$id_sucursal,$id_empresa,$tablaPrincipal,$mysql);
			break;
	}

	//============================= BUSCAR CLIENTE =============================//
	function buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link){

		if($inputId == 'nitTercero' . $opcGrillaContable){
			$where   = 'numero_identificacion = "' . $codCliente . '" AND tipo_identificacion = "' . $tipoDocumento . '"';
			$mensaje = 'alert("'.$tipoDocumento.' de tercero no establecido");';
		}
		else if($inputId == 'codigoTercero' . $opcGrillaContable){
			$where   = 'codigo = "' . $codCliente . '"';
			$mensaje = 'alert("Codigo de tercero no establecido");';
		}

		$sqlTercero  = "SELECT
											id,
											numero_identificacion,
											tipo_identificacion,
											codigo,
											nombre,
											COUNT(id) AS contTercero
										FROM
											terceros
										WHERE
											$where
										AND
											activo = 1
										AND
											tercero = 1
										AND
											id_empresa = '$id_empresa'
										LIMIT 0,1";
		$queryTercero = mysql_query($sqlTercero,$link);
		$contTercero 	= mysql_result($queryTercero,0,'contTercero');
		$idTercero   	= mysql_result($queryTercero,0,'id');
		$nit         	= mysql_result($queryTercero,0,'numero_identificacion');
		$tipoNit     	= mysql_result($queryTercero,0,'tipo_identificacion');
		$codigo      	= mysql_result($queryTercero,0,'codigo');
		$nombre      	= mysql_result($queryTercero,0,'nombre');

		//GENERAMOS LA VARIABLE PARA HACER EL UPDATE DE LA TABLA PRINCIPAL
		if($contTercero == 0){
			$sqlDocumento   = "SELECT codigo_tercero, numero_identificacion_tercero, tipo_identificacion_tercero FROM $tablaPrincipal WHERE id = '$id' AND activo = 1 AND id_empresa = '$id_empresa' LIMIT 0,1";
			$queryDocumento = mysql_query($sqlDocumento,$link);
			$codigoTercero  = mysql_result($queryDocumento,0,'codigo_tercero');
			$nitTercero     = mysql_result($queryDocumento,0,'numero_identificacion_tercero');
			$tipoNitTercero = mysql_result($queryDocumento,0,'tipo_identificacion_tercero');

			echo '<script>
							' . $mensaje . '
							document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigoTercero.'";
							document.getElementById("tipoDocumento'.$opcGrillaContable.'").value = "'.$tipoNitTercero.'";
							document.getElementById("nitTercero'.$opcGrillaContable.'").value    = "'.$nitTercero.'";
						</script>';
			exit;
		}
		else if($inputId == 'nitTercero' . $opcGrillaContable){
			$camposInsert =  "codigo_tercero = '$codigo ',
												numero_identificacion_tercero = '$codCliente',
												tipo_identificacion_tercero = '$tipoDocumento'";
		}
		else if($inputId == 'codigoTercero' . $opcGrillaContable){
			$camposInsert =  "codigo_tercero = '$codigo ',
												numero_identificacion_tercero = '$nit',
												tipo_identificacion_tercero = '$tipoNit'";
		}

		$sqlUpdate = "UPDATE $tablaPrincipal
									SET id_tercero = '$idTercero',tercero = '$nombre',$camposInsert
									WHERE id = '$id'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		echo '<script>
						document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "' . $codigo . '";
						document.getElementById("tipoDocumento'.$opcGrillaContable.'").value = "' . $tipoNit . '";
						document.getElementById("nitTercero'.$opcGrillaContable.'").value    = "' . $nit . '";
						document.getElementById("nombreTercero'.$opcGrillaContable.'").value = "' . $nombre . '";

						id_cliente_'.$opcGrillaContable.'   = "'.$idTercero.'";
						nitTercero'.$opcGrillaContable.'    = "'.$nit.'";
						nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";
					</script>';
	}

	//============================ CARGAR FILA VACIA ===========================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){
		$readonly = '';
		if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		$body =  '<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>

							<div class="campo" style="width:12%;">
								<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" />
							</div>

							<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
							<div id="ventanaBuscarArticulo'.$opcGrillaContable.'_'.$cont.'" onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
								<img src="images/buscar20.png"/>
							</div>

							<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly></div>
							<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly></div>
							<div class="campo"><input type="text" id="depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly></div>
							<div class="campo"><input type="text" id="depreciacionAcumuladaNiif'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly></div>
							<div class="campo"><input type="text" id="deterioroAcumulado'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly></div>

							<div style="float:right; min-width:80px;">
								<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
								<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="images/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/delete.png"/></div>
							</div>

							<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="" />
							<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />

							<script>
								document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
							</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//============================= BUSCAR ARTICULO ============================//
	function buscarArticulo($id,$idArticulo,$id_empresa,$opcGrillaContable,$codigo_activo,$deterioroAcumulado,$valorActual,$valorDeterioro,$mysql){
		//CONSULTAR QUE EL ACTIVO NO ESTE YA AGREGADO EN EL DOCUMENTO
		$sql = "SELECT id_activo_fijo,nombre
						FROM activos_fijos_baja_inventario
						WHERE activo = 1
						AND id_empresa = $id_empresa
						AND id_baja = $id";
		$query = $mysql->query($sql,$mysql->link);
		while($row = $mysql->fetch_array($query)){
			$arrayActivos[$row['id_activo_fijo']] = $row['nombre'];
		}

		$codigo_buscado = $codigo_activo;

		$sqlArticulo = "SELECT id,codigo_activo,nombre_equipo,unidad,numero_piezas,costo,deterioro_acumulado,depreciacion_acumulada,depreciacion_acumulada_niif
										FROM activos_fijos
										WHERE activo = 1
										AND estado = 1
										AND codigo_activo = '$codigo_activo'
										LIMIT 0,1";
		$query = $mysql->query($sqlArticulo,$mysql->link);
		$id_activo                   = $mysql->result($query,0,'id');
		$codigo_activo               = $mysql->result($query,0,'codigo_activo');
		$nombre_equipo               = $mysql->result($query,0,'nombre_equipo');
		$unidad                      = $mysql->result($query,0,'unidad');
		$numero_piezas               = $mysql->result($query,0,'numero_piezas');
		$costo                       = $mysql->result($query,0,'costo');
		$deterioro_acumulado         = $mysql->result($query,0,'deterioro_acumulado');
		$depreciacion_acumulada      = $mysql->result($query,0,'depreciacion_acumulada');
		$depreciacion_acumulada_niif = $mysql->result($query,0,'depreciacion_acumulada_niif');

		if(array_key_exists($id_activo, $arrayActivos)){
			echo '<script>
							alert("El codigo '.$codigo_buscado.' ya esta agregado en el documento");
							setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
						</script>';
		}
		else if($id_activo > 0){
			//Si la cantidad del articulo es mayor a cero en la bodega, se permite realizar la venta del articulo
			echo '<script>
							document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value                = "'.$id_activo.'";
							document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value               = "'.$codigo_activo.'";
							document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value            = "'.$nombre_equipo.'";
							document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value                  = "'.$unidad.' x '.$numero_piezas.'";
							document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value             = "'.$costo.'";
							document.getElementById("depreciacionAcumulada'.$opcGrillaContable.'_'.$idArticulo.'").value     = "'.$depreciacion_acumulada.'";
							document.getElementById("depreciacionAcumuladaNiif'.$opcGrillaContable.'_'.$idArticulo.'").value = "'.$depreciacion_acumulada_niif.'";
							document.getElementById("deterioroAcumulado'.$opcGrillaContable.'_'.$idArticulo.'").value        = "'.$deterioro_acumulado.'";
						</script>';
		}
		else{
			echo '<script>
							alert("El codigo '.$codigo_buscado.' no se encuentra asignado en los activos fijos");
							setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
							document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value                = "0";
							document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value            = "";
							document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value                  = "";
							document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value             = "0";
							document.getElementById("depreciacionAcumulada'.$opcGrillaContable.'_'.$idArticulo.'").value     = "0";
							document.getElementById("depreciacionAcumuladaNiif'.$opcGrillaContable.'_'.$idArticulo.'").value = "0";
							document.getElementById("deterioroAcumulado'.$opcGrillaContable.'_'.$idArticulo.'").value        = "0";
						</script>';
		}
	}

	//===================== RETROCEDER ARTICULO MODIFICADO =====================//
	function retrocederArticulo($opcGrillaContable,$idArticulo,$cont,$id,$tablaInventario,$idTablaPrincipal,$link){
		$sqlArticulo = "SELECT id,id_activo_fijo,codigo_activo,nombre,unidad,numero_piezas,costo,deterioro_acumulado,depreciacion_acumulada,depreciacion_acumulada_niif
										FROM $tablaInventario
										WHERE activo = 1
										AND $idTablaPrincipal = '$id'
										AND id = '$idArticulo'
										LIMIT 0,1";
		$query = mysql_query($sqlArticulo,$link);

		$id_activo_fijo              = mysql_result($query,0,'id_activo_fijo');
		$codigo                      = mysql_result($query,0,'codigo_activo');
		$nombre                      = mysql_result($query,0,'nombre');
		$unidad                      = mysql_result($query,0,'unidad');
		$numero_piezas               = mysql_result($query,0,'numero_piezas');
		$costo                       = mysql_result($query,0,'costo');
		$deterioro_acumulado         = mysql_result($query,0,'deterioro_acumulado');
		$depreciacion_acumulada      = mysql_result($query,0,'depreciacion_acumulada');
		$depreciacion_acumulada_niif = mysql_result($query,0,'depreciacion_acumulada_niif');

		echo '<script>
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value                = "'.$id_activo_fijo.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value               = "'.$codigo.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value            = "'.$nombre.'";
						document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$unidad.' x '.$numero_piezas.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.$costo.'";
						document.getElementById("depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'").value     = "'.$depreciacion_acumulada.'";
						document.getElementById("depreciacionAcumuladaNiif'.$opcGrillaContable.'_'.$cont.'").value = "'.$depreciacion_acumulada_niif.'";
						document.getElementById("deterioroAcumulado'.$opcGrillaContable.'_'.$cont.'").value        = "'.$deterioro_acumulado.'";
						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display			 = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display	 = "none";
					</script>';
	}

	//============================ ELIMINAR ARTICULO ===========================//
	function deleteArticulo($opcGrillaContable,$idArticulo,$cont,$id,$tablaInventario,$idTablaPrincipal,$link){
		$sqlDelete   = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal = '$id' AND id = '$idArticulo'";
		$queryDelete = mysql_query($sqlDelete,$link);

		if(!$queryDelete){
			echo '<script>
							alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");
						</script>';
		}
		else{
			echo '<script>
							(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
						</script>';
		}
	}

	//======================= ACTUALIZAR FECHA DOCUMENTO =======================//
	function actualizaFechaDocumento($id,$fecha,$tablaPrincipal,$opcGrillaContable,$link){
		$sql   = "UPDATE $tablaPrincipal SET fecha_inicio = '$fecha' WHERE id = '$id'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo '<script>alert("Error!\nNo se actualizo la fecha '.$sql.'");</script>'; }
	}

	//============================ GENERAR DOCUMENTO ===========================//
	function terminarGenerar($opcGrillaContable,$id,$tablaPrincipal,$tablaInventario,$id_empresa,$idSucursal,$idBodega,$mysql){
		//ACTUALIZAR EL ESTADO DEL DOCUMENTO
		$sqlActualizaDocumento = "UPDATE $tablaPrincipal
															SET
																estado = 1
															WHERE activo = 1
															AND id_empresa = $id_empresa
															AND id_sucursal = $idSucursal
															AND id_bodega = $idBodega
															AND id = $id";
		$queryActualizaDocumento = $mysql->query($sqlActualizaDocumento,$mysql->link);

		//CONSULTAMOS DATOS DE LA CABECERA DEL DOCUMENTO
		$sql = "SELECT E.documento,AFB.observacion
						FROM $tablaPrincipal AS AFB
						LEFT JOIN empleados AS E
						ON E.id = AFB.id_usuario
						WHERE E.activo = 1
						AND AFB.id_empresa = $id_empresa
						AND AFB.id_sucursal = $idSucursal
						AND AFB.id_bodega = $idBodega
						AND AFB.id = $id";
		$query = $mysql->query($sql,$mysql->link);
		$documentoUsuario = $mysql->result($query,0,'documento');
		$observacion      = $mysql->result($query,0,'observacion');

		//CONSULTAR ACTIVOS A DAR DE BAJA
		$sqlActivos  = "SELECT AFI.id_activo_fijo,AFI.codigo_activo,AF.sincronizar_siip
										FROM $tablaInventario AS AFI
										LEFT JOIN activos_fijos AS AF
										ON AF.id = AFI.id_activo_fijo
										WHERE AFI.activo = 1
										AND AFI.id_baja = $id
										AND AFI.id_empresa = $id_empresa";
    $queryActivos = $mysql->query($sqlActivos,$mysql->link);

		//ACTUALIZAR EL ESTADO DEL ACTIVO FIJO
		while($row = $mysql->fetch_array($queryActivos)){
			$sqlActualizaActivos = "UPDATE activos_fijos
															SET
																estado = 3
															WHERE activo = 1
															AND id_empresa = $id_empresa
															AND id_sucursal = $idSucursal
															AND id_bodega = $idBodega
															AND id = '$row[id_activo_fijo]'";
			$queryActualizaActivos = $mysql->query($sqlActualizaActivos,$mysql->link);

			if($row['sincronizar_siip'] == "si"){
				include_once("../../../external_apis/LOGICALHOTELS/backend/ClassExternalApis.php");

				$datosActivoFijo = array(
					"codigo" => "$row[codigo_activo]",
					"observaciones_eliminacion" => "$observacion",
					"documento_usuario" => "$documentoUsuario"
				);

				$server_name = $_SERVER['SERVER_NAME'];

				if($server_name == "logicalerp.localhost"){
					$url_api = "http://siip.localhost/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}
				else{
					$url_api = "http://siip.plataforma.co/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}

				$params = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = json_encode($datosActivoFijo);

				$nuevoActivo = new ClassExternalApis($idSucursal,$id_empresa,$mysql);
				$respuesta = $nuevoActivo->curlApi($params);

				$respuesta = json_decode($respuesta,true);

				if($respuesta['estado'] == "error"){
					if(strpos($respuesta['msg'],'empleado') == true && $mensajes == ""){
						$mensajes = $respuesta['msg'];
					}
					else if(strpos($respuesta['msg'],'equipo') == true){
						$mensajes .= $respuesta['msg'] . " #$row[codigo_activo]" . "\\n";
					}
				}
			}
		}

		if($mensajes != ""){
			echo "<script>
							alert('$mensajes');
						</script>";
		}

		//SI LOS ACTIVOS SE ACTUALIZAN
		if($queryActualizaActivos){
			//CONSULTAR EL CONSECUTIVO
			$sql = "SELECT consecutivo,fecha_inicio,id_sucursal
							FROM $tablaPrincipal
							WHERE activo = 1
							AND id_empresa = $id_empresa
							AND id = $id";
			$query 			 = $mysql->query($sql,$mysql->link);
			$consecutivo = $mysql->result($query,0,'consecutivo');
			$fecha       = $mysql->result($query,0,'fecha_inicio');
			$id_sucursal = $mysql->result($query,0,'id_sucursal');

			//SI EL DOCUMENTO SE CREO
			if($consecutivo > 0){
				//MOVER LOS ASIENTOS CONTABLES DE LOS ACTIVOS PARA EL BAJA
				moverCuentasDocumento($id,$consecutivo,$fecha,'contabilizar',$id_empresa,$mysql);

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog =  "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
										VALUES($id,'" . $_SESSION['IDUSUARIO'] . "','" . $_SESSION['NOMBREUSUARIO'] . "','Generar','BA','Baja Activos Fijos','" . $_SESSION['SUCURSAL'] . "','" . $_SESSION['EMPRESA'] . "','" . $_SERVER['REMOTE_ADDR'] . "','$fecha_actual','$hora_actual')";
				$queryLog = $mysql->query($sqlLog,$mysql->link);

				echo '<script>
							 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
									url     : "baja/bd/grillaContableBloqueada.php",
									scripts : true,
									nocache : true,
									params  :	{
															filtro_sucursal   : "'.$idSucursal.'",
															opcGrillaContable : "'.$opcGrillaContable.'",
															id_baja   				: "'.$id.'"
														}
								});
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
			}
			else{
				//SI EL DOCUMENTO NO SE CREO
				$sql = "UPDATE activos_fijos_baja
								SET
									estado = 0
								WHERE activo = 1
								AND id_empresa = $id_empresa
								AND id = $id";
				$query = $mysql->query($sql,$mysql->link);

				echo '<script>
								alert("No se genero consecutivo del documento\nIntentelo de nuevo");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
			}
		}
	}

	//============================== MOVER CUENTAS =============================//
	function moverCuentasDocumento($id_documento,$consecutivo,$fecha,$accion,$id_empresa,$mysql){
		if($accion == 'contabilizar'){
			//===================== CONSULTAR DATOS DEL TERCERO ====================//
			$sql = "SELECT
								id_tercero,
								numero_identificacion_tercero,
								tercero,
								id_sucursal,
								sucursal
							FROM
								activos_fijos_baja
							WHERE
								activo = 1
							AND
								id_empresa = $id_empresa
							AND
								id = $id_documento";
			$query 													= $mysql->query($sql,$mysql->link);
			$id_tercero 										= $mysql->result($query,0,'id_tercero');
			$numero_identificacion_tercero 	= $mysql->result($query,0,'numero_identificacion_tercero');
			$tercero 												= $mysql->result($query,0,'tercero');
			$id_sucursal_origen							= $mysql->result($query,0,'id_sucursal');
			$sucursal_origen								= $mysql->result($query,0,'sucursal');

			//============ CONSULTAR CUENTAS DE COMPRA EN NORMA COLGAAP ============//
			$sqlColgaap =  "SELECT
												AFBI.costo,
												AF.id_centro_costos,
												AF.codigo_centro_costos,
												AF.centro_costos,
												IC.id_puc,
												IC.puc,
												IC.cuenta,
												IC.descripcion,
												IC.tipo
											FROM activos_fijos_baja_inventario AS AFBI
											LEFT JOIN items_cuentas AS IC	ON IC.id_items = AFBI.id_item
											LEFT JOIN activos_fijos AS AF ON AF.id = AFBI.id_activo_fijo
											WHERE	AFBI.activo = 1
											AND AFBI.id_baja = $id_documento
											AND AFBI.id_empresa = $id_empresa
											AND IC.descripcion = 'activo_fijo'";
			$queryColgaap = $mysql->query($sqlColgaap,$mysql->link);

			while($row = $mysql->fetch_array($queryColgaap)){
				$arrayActivosColgaap[$row['tipo']][$row['id_centro_costos']][$row['id_puc']]['costo'] += $row['costo'];
				$arrayActivosColgaap[$row['tipo']][$row['id_centro_costos']][$row['id_puc']] += array(
																																																'id_cuenta' 						=> ($row['id_puc'] != "")? $row['id_puc'] : "",
																																																'codigo_cuenta' 				=> ($row['puc'] != "")? $row['puc'] : "",
																																																'cuenta'								=> ($row['cuenta'] != "")? $row['cuenta'] : "",
																																																'id_centro_costos' 		 	=> ($row['id_centro_costos'] != "")? $row['id_centro_costos'] : "",
																																																'codigo_centro_costos' 	=> ($row['codigo_centro_costos'] != "")? $row['codigo_centro_costos'] : "",
																																																'centro_costos' 			 	=> ($row['centro'] != "")? $row['centro'] : ""
																																															);
			}

			foreach($arrayActivosColgaap as $primerIndice => $primerValor){
				foreach($primerValor as $segundoIndice => $segundoValor){
					foreach($segundoValor as $tercerIndice => $tercerValor){
						if($primerIndice == "debito"){
							$valuesColgaap .= "('$id_documento',
															 		'$consecutivo',
															 		'BA',
															 		'Baja Activo',
															 		'$id_documento',
															 		'BA',
															 		'$consecutivo',
															 		'$fecha',
															 		'',
															 		'". $tercerValor['costo'] ."',
															 		'". $tercerValor['id_cuenta'] ."',
															 		'". $tercerValor['codigo_cuenta'] ."',
															 		'". $tercerValor['cuenta'] ."',
															 		'$id_tercero',
															 		'$numero_identificacion_tercero',
															 		'$tercero',
															 		'$id_sucursal_origen',
															 		'$sucursal_origen',
															 		'0',
															 		'$id_empresa',
															 		'". $tercerValor['id_centro_costos'] ."',
															 		'". $tercerValor['codigo_centro_costos'] ."',
															 		'". $tercerValor['centro_costos'] ."',
															 		'',
															 		'',
															 		'',
															 		'',
															 		'1',
															 		'')";
						}
						else if($primerIndice == "credito"){
							$valuesColgaap .= "('$id_documento',
																	'$consecutivo',
																	'BA',
																	'Baja Activo',
																	'$id_documento',
																	'BA',
																	'$consecutivo',
																	'$fecha',
																	'". $tercerValor['costo'] ."',
																	'',
																	'". $tercerValor['id_cuenta'] ."',
																	'". $tercerValor['codigo_cuenta'] ."',
																	'". $tercerValor['cuenta'] ."',
																	'$id_tercero',
																	'$numero_identificacion_tercero',
																	'$tercero',
																	'$id_sucursal_origen',
																	'$sucursal_origen',
																	'0',
																	'$id_empresa',
																	'". $tercerValor['id_centro_costos'] ."',
																	'". $tercerValor['codigo_centro_costos'] ."',
																	'". $tercerValor['centro_costos'] ."',
																	'',
																	'',
																	'',
																	'',
																	'1',
																	'')";
						}
					}

					if($segundoValor != end($primerValor)){
						$valuesColgaap .= ",";
					}
				}
			}

			$sqlInsertColgaap =  "INSERT INTO
										          asientos_colgaap(
										            id_documento,
										            consecutivo_documento,
										            tipo_documento,
										            tipo_documento_extendido,
										            id_documento_cruce,
										            tipo_documento_cruce,
										            numero_documento_cruce,
										            fecha,
										            debe,
										            haber,
										            id_cuenta,
										            codigo_cuenta,
										            cuenta,
										            id_tercero,
										            nit_tercero,
										            tercero,
										            id_sucursal,
										            sucursal,
										            permiso_sucursal,
										            id_empresa,
										            id_centro_costos,
										            codigo_centro_costos,
										            centro_costos,
										            id_flujo_efectivo,
										            flujo_efectivo,
										            id_sucursal_cruce,
										            sucursal_cruce,
										            activo,
										            observacion
										          )
										        VALUES
															$valuesColgaap";
			$queryInsertColgaap = $mysql->query($sqlInsertColgaap,$mysql->link);

			//============== CONSULTAR CUENTAS DE COMPRA EN NORMA NIIF =============//
			$sqlNiif = "SELECT
										AFBI.costo,
										AF.id_centro_costos,
										AF.codigo_centro_costos,
										AF.centro_costos,
										ICN.id_puc,
										ICN.puc,
										ICN.cuenta,
										ICN.descripcion,
										ICN.tipo
									FROM activos_fijos_baja_inventario AS AFBI
									LEFT JOIN items_cuentas_niif AS ICN	ON ICN.id_items = AFBI.id_item
									LEFT JOIN activos_fijos AS AF ON AF.id = AFBI.id_activo_fijo
									WHERE	AFBI.activo = 1
									AND AFBI.id_baja = $id_documento
									AND AFBI.id_empresa = $id_empresa
									AND ICN.descripcion = 'activo_fijo'";
			$queryNiif = $mysql->query($sqlNiif,$mysql->link);

			while($row = $mysql->fetch_array($queryNiif)){
				$arrayActivosNiif[$row['tipo']][$row['id_centro_costos']][$row['id_puc']]['costo'] += $row['costo'];
				$arrayActivosNiif[$row['tipo']][$row['id_centro_costos']][$row['id_puc']] += array(
																																														'id_cuenta' 						=> ($row['id_puc'] != "")? $row['id_puc'] : "",
																																														'codigo_cuenta' 				=> ($row['puc'] != "")? $row['puc'] : "",
																																														'cuenta'								=> ($row['cuenta'] != "")? $row['cuenta'] : "",
																																														'id_centro_costos' 		 	=> ($row['id_centro_costos'] != "")? $row['id_centro_costos'] : "",
																																														'codigo_centro_costos' 	=> ($row['codigo_centro_costos'] != "")? $row['codigo_centro_costos'] : "",
																																														'centro_costos' 			 	=> ($row['centro'] != "")? $row['centro'] : ""
																																													);
			}

			foreach($arrayActivosNiif as $primerIndice => $primerValor){
				foreach($primerValor as $segundoIndice => $segundoValor){
					foreach($segundoValor as $tercerIndice => $tercerValor){
						if($primerIndice == "debito"){
							$valuesNiif .=  "('$id_documento',
														 		'$consecutivo',
														 		'BA',
														 		'Baja Activo',
														 		'$id_documento',
														 		'BA',
														 		'$consecutivo',
														 		'$fecha',
														 		'',
														 		'". $tercerValor['costo'] ."',
														 		'". $tercerValor['id_cuenta'] ."',
														 		'". $tercerValor['codigo_cuenta'] ."',
														 		'". $tercerValor['cuenta'] ."',
														 		'$id_tercero',
														 		'$numero_identificacion_tercero',
														 		'$tercero',
														 		'$id_sucursal_origen',
														 		'$sucursal_origen',
														 		'0',
														 		'$id_empresa',
														 		'". $tercerValor['id_centro_costos'] ."',
														 		'". $tercerValor['codigo_centro_costos'] ."',
														 		'". $tercerValor['centro_costos'] ."',
														 		'',
														 		'',
														 		'',
														 		'',
														 		'1',
														 		'')";
						}
						else if($primerIndice == "credito"){
							$valuesNiif .=  "('$id_documento',
																'$consecutivo',
																'BA',
																'Baja Activo',
																'$id_documento',
																'BA',
																'$consecutivo',
																'$fecha',
																'". $tercerValor['costo'] ."',
																'',
																'". $tercerValor['id_cuenta'] ."',
																'". $tercerValor['codigo_cuenta'] ."',
																'". $tercerValor['cuenta'] ."',
																'$id_tercero',
																'$numero_identificacion_tercero',
																'$tercero',
																'$id_sucursal_origen',
																'$sucursal_origen',
																'0',
																'$id_empresa',
																'". $tercerValor['id_centro_costos'] ."',
																'". $tercerValor['codigo_centro_costos'] ."',
																'". $tercerValor['centro_costos'] ."',
																'',
																'',
																'',
																'',
																'1',
																'')";
						}
					}

					if($segundoValor != end($primerValor)){
						$valuesNiif .= ",";
					}
				}
			}

			$sqlInsertNiif = "INSERT INTO
								          asientos_niif(
								            id_documento,
								            consecutivo_documento,
								            tipo_documento,
								            tipo_documento_extendido,
								            id_documento_cruce,
								            tipo_documento_cruce,
								            numero_documento_cruce,
								            fecha,
								            debe,
								            haber,
								            id_cuenta,
								            codigo_cuenta,
								            cuenta,
								            id_tercero,
								            nit_tercero,
								            tercero,
								            id_sucursal,
								            sucursal,
								            permiso_sucursal,
								            id_empresa,
								            id_centro_costos,
								            codigo_centro_costos,
								            centro_costos,
								            id_flujo_efectivo,
								            flujo_efectivo,
								            id_sucursal_cruce,
								            sucursal_cruce,
								            activo,
								            observacion
								          )
								        VALUES
													$valuesNiif";
			$queryInsertNiif = $mysql->query($sqlInsertNiif,$mysql->link);

			//==================== CONSULTAR CUENTAS DE ACTIVOS ====================//
			$sqlCuentasActivos = "SELECT
			                        AF.id_centro_costos,
			                        AF.codigo_centro_costos,
			                        AF.centro_costos,
			                        AFC.id_cuenta,
			                        AFC.cuenta,
			                        AFC.descripcion_cuenta,
			                        AFC.descripcion,
			                        AFC.contabilidad,
			                        AFBI.deterioro_acumulado,
			                        AFBI.depreciacion_acumulada,
			                        AFBI.depreciacion_acumulada_niif
			                      FROM activos_fijos_baja_inventario AS AFBI
			                      LEFT JOIN activos_fijos_cuentas AS AFC	ON AFC.id_activo = AFBI.id_activo_fijo
			                      LEFT JOIN activos_fijos AS AF ON AF.id = AFBI.id_activo_fijo
			                      WHERE	AFBI.activo = 1
			                      AND AFBI.id_baja = $id_documento
			                      AND AFBI.id_empresa = $id_empresa
			                      AND (
			                            AFBI.deterioro_acumulado > 0
			                            OR
			                            AFBI.depreciacion_acumulada > 0
			                            OR
			                            AFBI.depreciacion_acumulada_niif > 0
			                          )
			                      AND AFC.estado = 'credito'";
			$queryCuentasActivos = $mysql->query($sqlCuentasActivos,$mysql->link);

			while($row = $mysql->fetch_array($queryCuentasActivos)){
				if($row['descripcion'] == "depreciacion" && $row['contabilidad'] == "colgaap"){
					$arrayCuentasActivos[$row['id_centro_costos']][$row['id_cuenta']]['depreciacion_acumulada'] += $row['depreciacion_acumulada'];
				}
				else if($row['descripcion'] == "depreciacion" && $row['contabilidad'] == "niif"){
					$arrayCuentasActivos[$row['id_centro_costos']][$row['id_cuenta']]['depreciacion_acumulada_niif'] += $row['depreciacion_acumulada_niif'];
				}
				else if($row['descripcion'] == "deterioro" && $row['contabilidad'] == "niif"){
					$arrayCuentasActivos[$row['id_centro_costos']][$row['id_cuenta']]['deterioro_acumulado'] += $row['deterioro_acumulado'];
				}

			  $arrayCuentasActivos[$row['id_centro_costos']][$row['id_cuenta']] += array(
			                                                                              'id_cuenta' 						=> ($row['id_cuenta'] != "")? $row['id_cuenta'] : "",
			                                                                              'cuenta'								=> ($row['cuenta'] != "")? $row['cuenta'] : "",
			                                                                              'descripcion_cuenta' 		=> ($row['descripcion_cuenta'] != "")? $row['descripcion_cuenta'] : "",
			                                                                              'id_centro_costos' 		 	=> ($row['id_centro_costos'] != "")? $row['id_centro_costos'] : "",
			                                                                              'codigo_centro_costos' 	=> ($row['codigo_centro_costos'] != "")? $row['codigo_centro_costos'] : "",
			                                                                              'centro_costos' 			 	=> ($row['centro'] != "")? $row['centro'] : "",
			                                                                              'contabilidad'					=> ($row['contabilidad'] != "")? $row['contabilidad'] : ""
			                                                                            );
			}

			foreach($arrayCuentasActivos as $primerIndice => $primerValor){
			  foreach($primerValor as $segundoIndice => $segundoValor){
			    if($segundoValor["depreciacion_acumulada"] != 0 && $segundoValor['contabilidad'] == "colgaap"){
			      $valuesCuentasColgaap .=  "('$id_documento',
			                                  '$consecutivo',
			                                  'BA',
			                                  'Baja Activo',
			                                  '$id_documento',
			                                  'BA',
			                                  '$consecutivo',
			                                  '$fecha',
			                                  '". $segundoValor['depreciacion_acumulada'] ."',
			                                  '',
			                                  '". $segundoValor['id_cuenta'] ."',
			                                  '". $segundoValor['cuenta'] ."',
			                                  '". $segundoValor['descripcion_cuenta'] ."',
			                                  '$id_tercero',
			                                  '$numero_identificacion_tercero',
			                                  '$tercero',
			                                  '$id_sucursal_origen',
			                                  '$sucursal_origen',
			                                  '0',
			                                  '$id_empresa',
			                                  '". $segundoValor['id_centro_costos'] ."',
			                                  '". $segundoValor['codigo_centro_costos'] ."',
			                                  '". $segundoValor['centro_costos'] ."',
			                                  '',
			                                  '',
			                                  '',
			                                  '',
			                                  '1',
			                                  ''),";
			    }
			    else if($segundoValor["depreciacion_acumulada_niif"] != 0 && $segundoValor['contabilidad'] == "niif"){
			      $valuesCuentasNiif .= "('$id_documento',
			                              '$consecutivo',
			                              'BA',
			                              'Baja Activo',
			                              '$id_documento',
			                              'BA',
			                              '$consecutivo',
			                              '$fecha',
			                              '". $segundoValor['depreciacion_acumulada_niif'] ."',
			                              '',
			                              '". $segundoValor['id_cuenta'] ."',
			                              '". $segundoValor['cuenta'] ."',
			                              '". $segundoValor['descripcion_cuenta'] ."',
			                              '$id_tercero',
			                              '$numero_identificacion_tercero',
			                              '$tercero',
			                              '$id_sucursal_origen',
			                              '$sucursal_origen',
			                              '0',
			                              '$id_empresa',
			                              '". $segundoValor['id_centro_costos'] ."',
			                              '". $segundoValor['codigo_centro_costos'] ."',
			                              '". $segundoValor['centro_costos'] ."',
			                              '',
			                              '',
			                              '',
			                              '',
			                              '1',
			                              ''),";
			    }
			    else if($segundoValor["deterioro_acumulado"] != 0 && $segundoValor['contabilidad'] == "niif"){
			      $valuesCuentasNiif .= "('$id_documento',
			                              '$consecutivo',
			                              'BA',
			                              'Baja Activo',
			                              '$id_documento',
			                              'BA',
			                              '$consecutivo',
			                              '$fecha',
			                              '". $segundoValor['deterioro_acumulado'] ."',
			                              '',
			                              '". $segundoValor['id_cuenta'] ."',
			                              '". $segundoValor['cuenta'] ."',
			                              '". $segundoValor['descripcion_cuenta'] ."',
			                              '$id_tercero',
			                              '$numero_identificacion_tercero',
			                              '$tercero',
			                              '$id_sucursal_origen',
			                              '$sucursal_origen',
			                              '0',
			                              '$id_empresa',
			                              '". $segundoValor['id_centro_costos'] ."',
			                              '". $segundoValor['codigo_centro_costos'] ."',
			                              '". $segundoValor['centro_costos'] ."',
			                              '',
			                              '',
			                              '',
			                              '',
			                              '1',
			                              ''),";
			    }
			  }
			}

			$valuesCuentasColgaap = ($valuesCuentasColgaap != "")? substr($valuesCuentasColgaap,0,-1) : "";
			$valuesCuentasNiif 		= ($valuesCuentasNiif != "")? substr($valuesCuentasNiif,0,-1) : "";

			if($valuesCuentasColgaap != ""){
			  $sqlInsertActivosColgaap = "INSERT INTO
			                                asientos_colgaap(
			                                  id_documento,
			                                  consecutivo_documento,
			                                  tipo_documento,
			                                  tipo_documento_extendido,
			                                  id_documento_cruce,
			                                  tipo_documento_cruce,
			                                  numero_documento_cruce,
			                                  fecha,
			                                  debe,
			                                  haber,
			                                  id_cuenta,
			                                  codigo_cuenta,
			                                  cuenta,
			                                  id_tercero,
			                                  nit_tercero,
			                                  tercero,
			                                  id_sucursal,
			                                  sucursal,
			                                  permiso_sucursal,
			                                  id_empresa,
			                                  id_centro_costos,
			                                  codigo_centro_costos,
			                                  centro_costos,
			                                  id_flujo_efectivo,
			                                  flujo_efectivo,
			                                  id_sucursal_cruce,
			                                  sucursal_cruce,
			                                  activo,
			                                  observacion
			                                )
			                              VALUES
			                                $valuesCuentasColgaap";
			  $queryInsertActivosColgaap = $mysql->query($sqlInsertActivosColgaap,$mysql->link);
			}

			if($valuesCuentasNiif != ""){
			  $sqlInsertActivosNiif =  "INSERT INTO
			                              asientos_niif(
			                                id_documento,
			                                consecutivo_documento,
			                                tipo_documento,
			                                tipo_documento_extendido,
			                                id_documento_cruce,
			                                tipo_documento_cruce,
			                                numero_documento_cruce,
			                                fecha,
			                                debe,
			                                haber,
			                                id_cuenta,
			                                codigo_cuenta,
			                                cuenta,
			                                id_tercero,
			                                nit_tercero,
			                                tercero,
			                                id_sucursal,
			                                sucursal,
			                                permiso_sucursal,
			                                id_empresa,
			                                id_centro_costos,
			                                codigo_centro_costos,
			                                centro_costos,
			                                id_flujo_efectivo,
			                                flujo_efectivo,
			                                id_sucursal_cruce,
			                                sucursal_cruce,
			                                activo,
			                                observacion
			                              )
			                            VALUES
			                              $valuesCuentasNiif";
			  $queryInsertActivosNiif = $mysql->query($sqlInsertActivosNiif,$mysql->link);
			}

			//================= CONSULTAR CUENTAS PARA DAR DE BAJA =================//
			$sqlCuentasBaja  = "SELECT
														AF.id_centro_costos,
														AF.codigo_centro_costos,
														AF.centro_costos,
														AFC.id_cuenta,
														AFC.cuenta,
														AFC.descripcion_cuenta,
														AFC.contabilidad,
														(AF.costo - AF.depreciacion_acumulada) AS costo_final_colgaap,
														(AF.costo - AF.depreciacion_acumulada_niif - AF.deterioro_acumulado) AS costo_final_niif
													FROM
														activos_fijos AS AF
													LEFT JOIN activos_fijos_baja_inventario AS AFBI ON AFBI.id_activo_fijo = AF.id
													LEFT JOIN activos_fijos_cuentas AS AFC ON AFC.id_activo = AF.id
													WHERE
														AF.id_empresa = $id_empresa
													AND AF.activo = 1
													AND AFBI.id_baja = $id_documento
													AND AFC.descripcion = 'baja'";
			$queryCuentasBaja = $mysql->query($sqlCuentasBaja,$mysq->link);

			while($row = $mysql->fetch_array($queryCuentasBaja)){
				if($row['contabilidad'] == "colgaap"){
					$arrayCuentasBaja[$row['contabilidad']][$row['id_centro_costos']][$row['id_cuenta']]['costo'] += $row['costo_final_colgaap'];
				}
				else if($row['contabilidad'] == "niif"){
					$arrayCuentasBaja[$row['contabilidad']][$row['id_centro_costos']][$row['id_cuenta']]['costo'] += $row['costo_final_niif'];
				}

			  $arrayCuentasBaja[$row['contabilidad']][$row['id_centro_costos']][$row['id_cuenta']] += array(
													                                                                              'id_cuenta' 						=> ($row['id_cuenta'] != "")? $row['id_cuenta'] : "",
													                                                                              'cuenta'								=> ($row['cuenta'] != "")? $row['cuenta'] : "",
													                                                                              'descripcion_cuenta' 		=> ($row['descripcion_cuenta'] != "")? $row['descripcion_cuenta'] : "",
													                                                                              'id_centro_costos' 		 	=> ($row['id_centro_costos'] != "")? $row['id_centro_costos'] : "",
													                                                                              'codigo_centro_costos' 	=> ($row['codigo_centro_costos'] != "")? $row['codigo_centro_costos'] : "",
													                                                                              'centro_costos' 			 	=> ($row['centro_costos'] != "")? $row['centro_costos'] : ""
														                                                                          );
			}

			foreach($arrayCuentasBaja as $primerIndice => $primerValor){
				foreach($primerValor as $segundoIndice => $segundoValor){
					foreach($segundoValor as $tercerIndice => $tercerValor){
						if($primerIndice == "colgaap"){
				      $valuesCuentasColgaapBaja .=  "('$id_documento',
						                                  '$consecutivo',
						                                  'BA',
						                                  'Baja Activo',
						                                  '$id_documento',
						                                  'BA',
						                                  '$consecutivo',
						                                  '$fecha',
																							'". $tercerValor['costo'] ."',
						                                  '',
						                                  '". $tercerValor['id_cuenta'] ."',
						                                  '". $tercerValor['cuenta'] ."',
						                                  '". $tercerValor['descripcion_cuenta'] ."',
						                                  '$id_tercero',
						                                  '$numero_identificacion_tercero',
						                                  '$tercero',
						                                  '$id_sucursal_origen',
						                                  '$sucursal_origen',
						                                  '0',
						                                  '$id_empresa',
						                                  '". $tercerValor['id_centro_costos'] ."',
						                                  '". $tercerValor['codigo_centro_costos'] ."',
						                                  '". $tercerValor['centro_costos'] ."',
						                                  '',
						                                  '',
						                                  '',
						                                  '',
						                                  '1',
						                                  ''),";
				    }
				    else if($primerIndice == "niif"){
				      $valuesCuentasNiifBaja .= "('$id_documento',
						                              '$consecutivo',
						                              'BA',
						                              'Baja Activo',
						                              '$id_documento',
						                              'BA',
						                              '$consecutivo',
						                              '$fecha',
																					'". $tercerValor['costo'] ."',
						                              '',
						                              '". $tercerValor['id_cuenta'] ."',
						                              '". $tercerValor['cuenta'] ."',
						                              '". $tercerValor['descripcion_cuenta'] ."',
						                              '$id_tercero',
						                              '$numero_identificacion_tercero',
						                              '$tercero',
						                              '$id_sucursal_origen',
						                              '$sucursal_origen',
						                              '0',
						                              '$id_empresa',
						                              '". $tercerValor['id_centro_costos'] ."',
						                              '". $tercerValor['codigo_centro_costos'] ."',
						                              '". $tercerValor['centro_costos'] ."',
						                              '',
						                              '',
						                              '',
						                              '',
						                              '1',
						                              ''),";
				    }
					}
				}
			}

			$valuesCuentasColgaapBaja = ($valuesCuentasColgaapBaja != "")? substr($valuesCuentasColgaapBaja,0,-1) : "";
			$valuesCuentasNiifBaja 		= ($valuesCuentasNiifBaja != "")? substr($valuesCuentasNiifBaja,0,-1) : "";

			if($valuesCuentasColgaapBaja != ""){
			  $sqlInsertActivosColgaapBaja = "INSERT INTO
					                                asientos_colgaap(
					                                  id_documento,
					                                  consecutivo_documento,
					                                  tipo_documento,
					                                  tipo_documento_extendido,
					                                  id_documento_cruce,
					                                  tipo_documento_cruce,
					                                  numero_documento_cruce,
					                                  fecha,
					                                  debe,
					                                  haber,
					                                  id_cuenta,
					                                  codigo_cuenta,
					                                  cuenta,
					                                  id_tercero,
					                                  nit_tercero,
					                                  tercero,
					                                  id_sucursal,
					                                  sucursal,
					                                  permiso_sucursal,
					                                  id_empresa,
					                                  id_centro_costos,
					                                  codigo_centro_costos,
					                                  centro_costos,
					                                  id_flujo_efectivo,
					                                  flujo_efectivo,
					                                  id_sucursal_cruce,
					                                  sucursal_cruce,
					                                  activo,
					                                  observacion
					                                )
					                              VALUES
					                                $valuesCuentasColgaapBaja";
				$queryInsertActivosColgaapBaja = $mysql->query($sqlInsertActivosColgaapBaja,$mysql->link);
			}

			if($valuesCuentasNiifBaja != ""){
			  $sqlInsertActivosNiifBaja =  "INSERT INTO
					                              asientos_niif(
					                                id_documento,
					                                consecutivo_documento,
					                                tipo_documento,
					                                tipo_documento_extendido,
					                                id_documento_cruce,
					                                tipo_documento_cruce,
					                                numero_documento_cruce,
					                                fecha,
					                                debe,
					                                haber,
					                                id_cuenta,
					                                codigo_cuenta,
					                                cuenta,
					                                id_tercero,
					                                nit_tercero,
					                                tercero,
					                                id_sucursal,
					                                sucursal,
					                                permiso_sucursal,
					                                id_empresa,
					                                id_centro_costos,
					                                codigo_centro_costos,
					                                centro_costos,
					                                id_flujo_efectivo,
					                                flujo_efectivo,
					                                id_sucursal_cruce,
					                                sucursal_cruce,
					                                activo,
					                                observacion
					                              )
					                            VALUES
					                              $valuesCuentasNiifBaja";
				$queryInsertActivosNiifBaja = $mysql->query($sqlInsertActivosNiifBaja,$mysql->link);
			}
		}
		else if($accion == 'descontabilizar'){
			//BORRAR ASIENTOS COLGAAP
			$sqlDeleteColgaap =  "DELETE FROM asientos_colgaap
														WHERE activo = 1
														AND id_empresa = $id_empresa
														AND id_documento = $id_documento
														AND tipo_documento = 'BA'";
			$queryDeleteColgaap = $mysql->query($sqlDeleteColgaap,$mysql->link);

			//BORRAR ASIENTOS NIIF
			$sqlDeleteNiif = "DELETE FROM asientos_niif
												WHERE activo = 1
												AND id_empresa = $id_empresa
												AND id_documento = $id_documento
												AND tipo_documento = 'BA'";
			$queryDeleteNiif = $mysql->query($sqlDeleteNiif,$mysql->link);

			if(!$queryDeleteColgaap && !$queryDeleteNiif){
				echo '<script>
								alert("\u00A1Error!\nNo se eliminaron los asientos colgaap y niif.\nIntentelo de nuevo.");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
				exit;
			}
		}
	}

	//============================ EDITAR DOCUMENTO ============================//
	function modificarDocumentoGenerado($opcGrillaContable,$id,$tablaPrincipal,$tablaInventario,$id_empresa,$id_sucursal,$mysql){
		//DESCONTABILIZAR EL DOCUMENTO
		moverCuentasDocumento($id,0,0,'descontabilizar',$id_empresa,$mysql);

		//CONSULTAR ACTIVOS PARA HABILITAR DE NUEVO
		$sqlActivos  = "SELECT AFI.id_activo_fijo,AFI.codigo_activo,AF.sincronizar_siip
										FROM $tablaInventario AS AFI
										LEFT JOIN activos_fijos AS AF
										ON AF.id = AFI.id_activo_fijo
										WHERE AFI.activo = 1
										AND AFI.id_baja = $id
										AND AFI.id_empresa = $id_empresa";
    $queryActivos = $mysql->query($sqlActivos,$mysql->link);

		//ACTUALIZAR EL ESTADO DEL ACTIVO FIJO
		while($row = $mysql->fetch_array($queryActivos)){
			$sqlActualizaActivos = "UPDATE activos_fijos
															SET
																estado = 1
															WHERE activo = 1
															AND id_empresa = $id_empresa
															AND id = $row[id_activo_fijo]";
			$queryActualizaActivos = $mysql->query($sqlActualizaActivos,$mysql->link);

			if($row['sincronizar_siip'] == "si"){
				include_once("../../../external_apis/LOGICALHOTELS/backend/ClassExternalApis.php");

				$datosActivoFijo = array(
					"codigo" => "$row[codigo_activo]"
				);

				$server_name = $_SERVER['SERVER_NAME'];

				if($server_name == "logicalerp.localhost"){
					$url_api = "http://siip.localhost/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}
				else{
					$url_api = "http://siip.plataforma.co/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}

				$params = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = json_encode($datosActivoFijo);

				$nuevoActivo = new ClassExternalApis($idSucursal,$id_empresa,$mysql);
				$respuesta = $nuevoActivo->curlApi($params);

				$respuesta = json_decode($respuesta,true);

				if($respuesta['estado'] == "error"){
					if(strpos($respuesta['msg'],'restaurando') == true){
						$mensajes .= $respuesta['msg'] . "\\n";
					}
					else if(strpos($respuesta['msg'],'encuentra') == true){
						$mensajes .= $respuesta['msg'] . " #$row[codigo_activo]" . "\\n";
					}
				}
			}
		}

		if($mensajes != ""){
			echo "<script>
							alert('$mensajes');
						</script>";
		}

		//ACTUALIZAMOS EL ESTADO DEL DOCUMENTO
		$sqlActualizaDocumento = "UPDATE $tablaPrincipal
															SET estado = 0
															WHERE id = $id
															AND id_empresa = $id_empresa
															AND activo = 1";
		$queryActualizaDocumento = $mysql->query($sqlActualizaDocumento,$mysql->link);

		//SI EXISTE UN ERROR EN LA ACTUALIZACION DEL ESTADO
		if(!$queryActualizaDocumento){
			$sql = "UPDATE $tablaPrincipal
							SET estado = 1
							WHERE id = '$id'
							AND id_empresa = '$id_empresa'
							AND activo = 1";
			$query = $mysql->query($sql,$mysql->link);

			echo '<script>
							alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			return;
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog	 = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','BA','Baja Activos Fijos',$id_sucursal,$id_empresa,'".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = $mysql->query($sqlLog,$mysql->link);

		echo '<script>
					 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "baja/grilla/grillaContable.php",
							scripts : true,
							nocache : true,
							params  : {
													filtro_sucursal   : "'.$id_sucursal_origen.'",
													opcGrillaContable : "'.$opcGrillaContable.'",
													id_baja  	 				: "'.$id.'",
												}
						});
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					</script>';
	}

	//=========================== GUARDAR ACTIVO FIJO ==========================//
	function guardarArticulo($idBaja,$idArticulo,$idSucursal,$consecutivo,$cont,$opcGrillaContable,$tablaInventario,$id_empresa,$mysql){

		//CONSULTAMOS INFORMACION DEL ACTIVO
		$sql = "SELECT
							AF.id_item,
							AF.code_bar,
							AF.codigo_activo,
							AF.nombre_equipo,
							AF.unidad,
							AF.numero_piezas,
							AF.costo,
							AF.deterioro_acumulado,
							AF.depreciacion_acumulada,
							AF.depreciacion_acumulada_niif
						FROM
							activos_fijos AS AF
						WHERE
							AF.activo = 1
						AND
						  AF.id = $idArticulo
						AND
							AF.id_empresa = $id_empresa";
		$query 												= $mysql->query($sql,$mysql->link);
		$id_item 											= $mysql->result($query,0,'id_item');
		$code_bar 										= $mysql->result($query,0,'code_bar');
		$codigo_activo 								= $mysql->result($query,0,'codigo_activo');
		$nombre_equipo 								= $mysql->result($query,0,'nombre_equipo');
		$unidad 											= $mysql->result($query,0,'unidad');
		$numero_piezas								= $mysql->result($query,0,'numero_piezas');
		$costo 												= $mysql->result($query,0,'costo');
		$deterioro_acumulado 					= $mysql->result($query,0,'deterioro_acumulado');
		$depreciacion_acumulada 			= $mysql->result($query,0,'depreciacion_acumulada');
		$depreciacion_acumulada_niif 	= $mysql->result($query,0,'depreciacion_acumulada_niif');

		//INSERTAMOS EL ACTIVO EN EL DETALLE DEL BAJA
		$sql = "INSERT INTO
							$tablaInventario(
						  	id_baja,
						  	id_activo_fijo,
								id_item,
								code_bar,
								codigo_activo,
								nombre,
								unidad,
								numero_piezas,
								costo,
								deterioro_acumulado,
								depreciacion_acumulada,
								depreciacion_acumulada_niif,
								id_empresa,
								id_sucursal,
								activo
							)
						VALUES(
							'$idBaja',
							'$idArticulo',
							'$id_item',
							'$code_bar',
							'$codigo_activo',
							'$nombre_equipo',
							'$unidad',
							'$numero_piezas',
							'$costo',
							'$deterioro_acumulado',
							'$depreciacion_acumulada',
							'$depreciacion_acumulada_niif',
							'$id_empresa',
							'$idSucursal',
							'1'
						)";
		$query = $mysql->query($sql,$mysql->link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = $mysql->result($mysql->query($sqlLastId,$mysql->link),0,0);

		if($lastId > 0){
			echo '<script>
							document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
							document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","images/reload.png");
							document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value            					= '.$lastId.'
							document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display        					= "none";
							document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display    					= "none";
							document.getElementById("deleteArticulo'.$opcGrillaContable.'_'.$cont.'").style.display      					= "block";
							document.getElementById("ventanaBuscarArticulo'.$opcGrillaContable.'_'.$cont.'").disabled      				= true;
							document.getElementById("ventanaBuscarArticulo'.$opcGrillaContable.'_'.$cont.'").style.pointerEvents  = "none";
						</script>' . cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);
		}
		else{
			echo $sql . " Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema.";
		}
	}

	//========================= ACTUALIZAR ACTIVO FIJO =========================//
	function actualizaArticulo($idBaja,$idArticulo,$idSucursal,$cont,$opcGrillaContable,$tablaInventario,$idInsertArticulo,$idTablaPrincipal,$id_empresa,$mysql){
		//CONSULTAMOS INFORMACION DEL ACTIVO
		$sql = "SELECT
							AF.id_item,
							AF.code_bar,
							AF.codigo_activo,
							AF.nombre_equipo,
							AF.unidad,
							AF.numero_piezas,
							AF.costo,
							AF.deterioro_acumulado,
							AF.depreciacion_acumulada,
							AF.depreciacion_acumulada_niif
						FROM
							activos_fijos AS AF
						WHERE
						  AF.id = $idArticulo";
		$query 												= $mysql->query($sql,$mysql->link);
		$id_item 											= $mysql->result($query,0,'id_item');
		$code_bar 										= $mysql->result($query,0,'code_bar');
		$codigo_activo 								= $mysql->result($query,0,'codigo_activo');
		$nombre_equipo 								= $mysql->result($query,0,'nombre_equipo');
		$unidad 											= $mysql->result($query,0,'unidad');
		$numero_piezas								= $mysql->result($query,0,'numero_piezas');
		$costo 												= $mysql->result($query,0,'costo');
		$deterioro_acumulado 					= $mysql->result($query,0,'deterioro_acumulado');
		$depreciacion_acumulada 			= $mysql->result($query,0,'depreciacion_acumulada');
		$depreciacion_acumulada_niif 	= $mysql->result($query,0,'depreciacion_acumulada_niif');

		//ACTUALIZAMOS EL ACTIVO EN EL INVENTARIO DEL BAJA
		$sql = "UPDATE $tablaInventario
						SET
							id_activo_fijo = '$idArticulo',
							id_item = '$id_item',
							code_bar = '$code_bar',
							codigo_activo = '$codigo_activo',
							nombre = '$nombre_equipo',
							unidad = '$unidad',
							numero_piezas = '$numero_piezas',
							costo = '$costo',
							deterioro_acumulado = '$deterioro_acumulado',
							depreciacion_acumulada = '$depreciacion_acumulada',
							depreciacion_acumulada_niif = '$depreciacion_acumulada_niif'
						WHERE $idTablaPrincipal = $idBaja
						AND id = $idInsertArticulo";
		$query = $mysql->query($sql,$mysql->link);

		if($query){
			echo '<script>
							document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
							document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
						</script>';
		}
		else{
			echo '<script>
							alert("Error, no se actualizo el articulo");
						</script>';
		}
	}

	//=========================== GUARDAR OBSERVACION ==========================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){
		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sql = "UPDATE $tablaPrincipal SET  observacion = '$observacion' WHERE id = '$id' AND id_empresa = " . $_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sql,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo 'false'; }
	}

	//=========================== CANCELAR DOCUMENTO ===========================//
	function cancelarDocumento($opcGrillaContable,$id,$tablaPrincipal,$tablaInventario,$id_empresa,$id_sucursal,$mysql){
		//DESCONTABILIZAR EL DOCUMENTO
		moverCuentasDocumento($id,0,0,'descontabilizar',$id_empresa,$mysql);

		//CONSULTAR ACTIVOS PARA HABILITAR DE NUEVO
		$sqlActivos  = "SELECT AFI.id_activo_fijo,AFI.codigo_activo,AF.sincronizar_siip
										FROM $tablaInventario AS AFI
										LEFT JOIN activos_fijos AS AF
										ON AF.id = AFI.id_activo_fijo
										WHERE AFI.activo = 1
										AND AFI.id_baja = $id
										AND AFI.id_empresa = $id_empresa";
	  $queryActivos = $mysql->query($sqlActivos,$mysql->link);

	  //ACTUALIZAR EL ESTADO DEL ACTIVO FIJO
	  while($row = $mysql->fetch_array($queryActivos)){
			$sqlActualizaActivos = "UPDATE activos_fijos
	                            SET
																estado = 1
	                            WHERE activo = 1
	                            AND id_empresa = $id_empresa
	                            AND id = $row[id_activo_fijo]";
	    $queryActualizaActivos = $mysql->query($sqlActualizaActivos,$mysql->link);

			if($row['sincronizar_siip'] == "si"){
				include_once("../../../external_apis/LOGICALHOTELS/backend/ClassExternalApis.php");

				$datosActivoFijo = array(
					"codigo" => "$row[codigo_activo]"
				);

				$server_name = $_SERVER['SERVER_NAME'];

				if($server_name == "logicalerp.localhost"){
					$url_api = "http://siip.localhost/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}
				else{
					$url_api = "http://siip.plataforma.co/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}

				$params = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = json_encode($datosActivoFijo);

				$nuevoActivo = new ClassExternalApis($idSucursal,$id_empresa,$mysql);
				$respuesta = $nuevoActivo->curlApi($params);

				$respuesta = json_decode($respuesta,true);

				if($respuesta['estado'] == "error"){
					if(strpos($respuesta['msg'],'restaurando') == true){
						$mensajes .= $respuesta['msg'] . "\\n";
					}
					else if(strpos($respuesta['msg'],'encuentra') == true){
						$mensajes .= $respuesta['msg'] . " #$row[codigo_activo]" . "\\n";
					}
				}
			}
	  }

		if($mensajes != ""){
			echo "<script>
							alert('$mensajes');
						</script>";
		}

		//CONSULTAR DATOS DEL DOCUMENTO
		$sqlBaja = "SELECT consecutivo,estado
								FROM $tablaPrincipal
								WHERE activo = 1
								AND id_empresa = $id_empresa
								AND id = $id";
		$queryBaja 		= $mysql->query($sqlBaja,$mysql->link);
		$estado      	= $mysql->result($queryBaja,0,'estado');
		$consecutivo 	= $mysql->result($queryBaja,0,'consecutivo');

		//ACTUALIZAMOS EL ESTADO DEL DOCUMENTO
		if($estado == 1){
			$sqlActualizaDocumento = "UPDATE $tablaPrincipal
																SET estado = 3
																WHERE activo = 1
																AND id_empresa = $id_empresa
																AND id = $id";
		}
		else if($estado == 0 && $consecutivo == ''){
			$sqlActualizaDocumento = "UPDATE $tablaPrincipal
																SET activo = 0
																WHERE activo = 1
																AND id_empresa = $id_empresa
																AND id = $id";
		}
		$queryActualizaDocumento = $mysql->query($sqlActualizaDocumento,$mysql->link);

		//SI EXISTE UN ERROR EN LA ACTUALIZACION DEL ESTADO
		if(!$queryActualizaDocumento){
			echo '<script>
							alert("Error!\nNo se logro cancelar el documento");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			return;
		}
		else{

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog =  "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
									VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','BA','Baja Activos Fijos',$id_sucursal,$id_empresa,'".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = $mysql->query($sqlLog,$mysql->link);

			echo '<script>
							nueva'.$opcGrillaContable.'();
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
		}
	}

 	//=========================== RESTAURAR DOCUMENTO ==========================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$id_sucursal,$id_empresa,$tablaPrincipal,$mysql){
		$sqlUpdate = "UPDATE $tablaPrincipal
									SET estado = 0
									WHERE activo = 1
									AND id = '$idDocumento'
									AND id_empresa = '$id_empresa'";
		$queryUpdate = $mysql->query($sqlUpdate,$mysql->link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if($queryUpdate){

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog =  "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
									VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','BA','Baja Activos Fijos',$id_sucursal,$id_empresa,'".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = $mysql->query($sqlLog,$mysql->link);

			echo '<script>
							Ext.get("contenedor_'.$opcGrillaContable.'").load({
								url     : "baja/grilla/grillaContable.php",
								scripts : true,
								nocache : true,
								params  :	{
														id_baja   				: "'.$idDocumento.'",
														opcGrillaContable : "'.$opcGrillaContable.'",
														id_sucursal       : "'.$id_sucursal.'"
													}
							});
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
		}
		else{
			echo '<script>
							alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			return;
		}
 	}

	//===================== VERIFICAR ESTADO DEL DOCUMENTO =====================//
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$link){

		$campoConsec=($tablaPrincipal=='ventas_facturas')? ' numero_factura_completo AS consecutivo ' : ' consecutivo ' ;

		$sql="SELECT estado,id_bodega,$campoConsec FROM $tablaPrincipal WHERE id=$id_documento";
		$query=mysql_query($sql,$link);

		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');
		$consecutivo = mysql_result($query,0,'consecutivo');
		if ($estado==1) {
			$mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el';
		}

		if ($opcGrillaContable=='CotizacionVenta'){
			$titulo='Cotizacion de Venta';
		}
		else if ($opcGrillaContable=='PedidoVenta'){
			$titulo='Pedido de Venta';
		}
		else if ($opcGrillaContable=='RemisionesVenta'){
			$titulo='Remision de Venta';
		}
		else{
			$titulo='Factura de Venta';
		}

		if ($estado>0) {
			echo'<script>
						alert("'.$mensaje.'");
						if (document.getElementById("Win_Ventana_descripcion_Articulo_factura")) {
							Win_Ventana_descripcion_Articulo_factura.close();
						}
						if (document.getElementById("Win_Ventana_update_fecha_FacturaVenta")) {
							Win_Ventana_update_fecha_FacturaVenta.close();
						}
						if (document.getElementById("Win_Ventana_configRetenciones_FacturaVenta")) {
							Win_Ventana_configRetenciones_FacturaVenta.close();
						}

						Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "bd/grillaContableBloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega     : "'.$id_bodega.'",
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_factura_venta  : "'.$id_documento.'"
							}
						});

						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
					</script>';
			exit;
		}
	}

 	//============================ VERIFICAR CIERRE ============================//
	function verificaCierre($id_documento,$campoFecha,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT $campoFecha AS fecha FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);
		$fecha_documento = mysql_result($query,0,'fecha');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar = date("Y", strtotime($fecha_documento)).'-01-01';
		$fecha_fin_buscar    = date("Y", strtotime($fecha_documento)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final ";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND fecha_nota>='$fecha_inicio_buscar' AND fecha_nota<='$fecha_fin_buscar' ";
		$query=mysql_query($sql,$link);
		$cont2 = mysql_result($query,0,'cont');

		if ($cont1>0 || $cont2>0) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if ( document.getElementById("modal") ) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}
	}
?>
