<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	require("../../config_var_global.php");
	include_once("../../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	// $id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		if ($opc<>'UpdateFormaPago') {
			verificaCierre($id,$opcGrillaContable,$tablaPrincipal,$id_empresa,$link);
		}
	}

	switch ($opc) {
		case 'actualizaSucursal':
			actualizaSucursal($id_documento,$opcGrillaContable,$tablaPrincipal,$id_sucursal,$id_empresa,$mysql);
			break;

		case 'actualizaFecha':
			actualizaFecha($fecha,$campoId,$id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql);
			break;

		case 'cargaHeadInsertUnidades' :
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'terminarGenerar':
			verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$mysql);
			terminarGenerar($id_documento,$opcGrillaContable,$tablaPrincipal,$id_sucursal,$id_empresa,$mysql);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql);
			break;

		case 'guardarObservacion':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql);
			break;

		case 'cargarDiferidos':
			cargarDiferidos($id_documento,$fecha,$id_sucursal,$opcGrillaContable,$id_empresa,$mysql);
			break;

	}

	// ACTUALIZAR LA SUCURSAL DEL DOCUMENTO
	function actualizaSucursal($id_documento,$opcGrillaContable,$tablaPrincipal,$id_sucursal,$id_empresa,$mysql){
		$campoUpdate = ($id_sucursal=='todas')? " ,sucursal='Todas' " : "" ;
		$sql="UPDATE $tablaPrincipal SET id_sucursal='$id_sucursal' $campoUpdate WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo la sucursal");</script>';
		}
	}

	function actualizaFecha($fecha,$campoId,$id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql){
		$sql="UPDATE $tablaPrincipal SET $campoId='$fecha' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo la fecha");</script>';
		}
		// SI SE CAMBIA LA FECHA DE DIFERIDOS SE DEBEN ELIMINAR LOS DIFERIDOS CARGADOS EN EL DOCUMENTO
		if ($campoId=='fecha_diferidos') {
			$sql="DELETE FROM amortizaciones_diferidos WHERE activo=1 AND id_empresa=$id_empresa AND id_amortizacion=$id_documento";
			$query=$mysql->query($sql,$mysql->link);

			if ($query) {
				echo "<script>document.getElementById('renderizaNewArticulo$opcGrillaContable').innerHTML='';</script>";
			}
			else{
				echo '<script>alert("Error\nNo se eliminaron los diferidos!");</script>';
			}

		}
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label" style="width:40px !important;"></div>
							<div class="label" style="width:80px;">Documento</div>
							<div class="label" style="width:80px;">Consecutivo</div>
							<div class="label" style="width:90px;">Fecha</div>
							<div class="label" style="width:90px;">Nit</div>
							<div class="label" style="width:250px;">Tercero</div>
							<div class="label" style="width:100px;">Valor</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsInsertUnidades('return',$cont,$opcGrillaContable).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Total Extracto</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotal'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Total Detalle</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalDetalle'.$opcGrillaContable.'">0</div>
						</div>

						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">DIFERENCIA EXTRACTO</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					//document.getElementById("subtotal'.$opcGrillaContable.'").innerHTML        = parseFloat(subtotal'.$opcGrillaContable.').toFixed(2);
					//document.getElementById("subtotalDetalle'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalDetalle'.$opcGrillaContable.').toFixed(2);
					//document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML  = parseFloat(total'.$opcGrillaContable.').toFixed(2);

					// document.getElementById("tipo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$body ='<div class="campo" style="width:40px !important;">'.$cont.'</div>
				<div class="campo" style="width:80px;"><input type="text"></div>
				<div class="campo" style="width:80px;"><input type="text"></div>
				<div class="campo" style="width:90px;"><input type="text"></div>
				<div class="campo" style="width:90px;"><input type="text"></div>
				<div class="campo" style="width:250px;"><input type="text"></div>
				<div class="campo" style="width:100px;"><input type="text"></div>
				<div id="guardar_registro" style="float:right; border:solid min-width:80px;">
					<div onclick="guardarNewRegistro'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Registro" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveDetalle'.$opcGrillaContable.'_'.$cont.'"/></div>
				</div>

				<input type="hidden" id="idRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />

				<script>
				</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA FACTURA, COTIZACION, PEDIDO Y CARGAR UNA NUEVA ==============================//
	function terminarGenerar($id_documento,$opcGrillaContable,$tablaPrincipal,$id_sucursal,$id_empresa,$mysql){
		// ACTUALIZAR EL DOCUMENTO
		$sql="UPDATE $tablaPrincipal SET estado=1 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento ";
		 $query=$mysql->query($sql,$mysql->link);

		// CONTABILIZAR EL DOCUMENTO
		moverCuentasDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,'contabilizar',$mysql);

		modificarSaldoDiferidos($id_documento,$opcGrillaContable,$id_empresa,'eliminar',$mysql);

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					  VALUES($id_documento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Agregar','AM','Amortizacion','".$_SESSION['SUCURSAL']."','".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$query = $mysql->query($sql,$mysql->link);

		echo "<script>
				Ext.get('contenedor_Amortizacion').load({
		    		url     : 'amortizaciones/amortizacion/bd/grillaContableBloqueada.php',
		    		scripts : true,
		    		nocache : true,
		    		params  :
		    		{
						opcGrillaContable : '$opcGrillaContable',
						id_documento      : $id_documento,
		    		}
		    	});

				document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
			</script>";
	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	function moverCuentasDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,$accion,$mysql){
		if ($accion=='contabilizar') {
			// CONSULTAR LA CABECERA
			$sql="SELECT fecha_documento,consecutivo FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			$query=$mysql->query($sql,$mysql->link);
			$consecutivo     = $mysql->result($query,0,'consecutivo');
			$fecha_documento = $mysql->result($query,0,'fecha_documento');

			// DIFERIDOS DEL DOCUMENTO A AMORTIZAR
			$sql="SELECT
						id_documento,
						tipo_documento,
						consecutivo_documento,
						cuenta_debito,
						cuenta_credito,
						valor,
						id_tercero,
						id_sucursal,
						id_centro_costos,
						descripcion_cuenta_debito,
						centro_costos_debito,
						centro_costos_credito
					FROM amortizaciones_diferidos WHERE activo=1 AND id_empresa=$id_empresa AND id_amortizacion=$id_documento";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				// CUENTA DEBITO
				$id_centro_costos_debito = ( $row['centro_costos_debito'] == 'Si' )? $row['id_centro_costos'] : '' ;
				$valueInsertAsientos .= "(
											'$id_documento',
											'$consecutivo',
											'AM',
											'$row[id_documento]',
											'$row[consecutivo_documento]',
											'$row[tipo_documento]',
											'Amortizacion',
											'$fecha_documento',
											'$row[valor]',
											0,
											'$row[cuenta_debito]',
											'$row[id_tercero]',
											'$id_centro_costos_debito',
											'$row[id_sucursal]',
											'$id_empresa'
										),";

				// CUENTA CREDITO
				$id_centro_costos_credito = ( $row['centro_costos_credito'] == 'Si' )? $row['id_centro_costos'] : '' ;
				$valueInsertAsientos .= "(
											'$id_documento',
											'$consecutivo',
											'AM',
											'$row[id_documento]',
											'$row[consecutivo_documento]',
											'$row[tipo_documento]',
											'Amortizacion',
											'$fecha_documento',
											0,
											$row[valor],
											'$row[cuenta_credito]',
											'$row[id_tercero]',
											'$id_centro_costos_credito',
											'$row[id_sucursal]',
											'$id_empresa'
										),";

				// $arrayCuentas[$cuenta_debito]
			}

			$valueInsertAsientos = substr($valueInsertAsientos,0,-1);
			$sql="INSERT INTO asientos_colgaap(
										id_documento,
										consecutivo_documento,
										tipo_documento,
										id_documento_cruce,
										numero_documento_cruce,
										tipo_documento_cruce,
										tipo_documento_extendido,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_centro_costos,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
			$query=$mysql->query($sql,$mysql->link);
			// CUENTAS SIMULTANEAS
			contabilizacionSimultanea($id_documento,'AM',$id_sucursal,$idEmpresa,$link);
			if (!$query) {
				$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
				$query=$mysql->query($sql,$mysql->link);
				echo '<script>
							alert("Error\nNo se insertaron los asientos Colgaap");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

		}
		else if ($accion=='descontabilizar'){
			$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_documento AND tipo_documento='AM' ";
			$query=$mysql->query($sql,$mysql->link);
		}
	}

	// ========================== MODIFICAR EL SALDO DE LOS DIFERIDOS CARGADOS EN LA AMORTIZACION ===================//
	function modificarSaldoDiferidos($id_documento,$opcGrillaContable,$id_empresa,$accion,$mysql){
		// $sql   = "UPDATE inventario_totales AS IT, (
		// 					SELECT SUM(cantidad) AS total_factura_venta, id_inventario AS id_item
		// 					FROM ventas_facturas_inventario
		// 					WHERE id_factura_venta='$idFactura'
		// 						AND activo=1
		// 						AND inventariable='true'
		// 						AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
		// 					GROUP BY id_inventario) AS VFI
		// 				SET IT.cantidad=IT.cantidad-VFI.total_factura_venta
		// 				WHERE IT.id_item=VFI.id_item
	 	//						AND IT.activo = 1
	 	//						AND IT.id_ubicacion = '$idBodega'";
		if ($accion=='agregar') {
			$sql="UPDATE diferidos AS D, (
									SELECT valor,id_diferido
									FROM amortizaciones_diferidos AS AD
									WHERE activo=1 AND id_empresa=$id_empresa AND id_amortizacion=$id_documento
									) AS AD
						SET D.saldo = D.saldo+AD.valor
						WHERE D.activo=1 AND D.id_empresa=$id_empresa AND D.id=AD.id_diferido
									";
		}
		else if ($accion=='eliminar') {
			$sql="UPDATE diferidos AS D, (
									SELECT valor,id_diferido
									FROM amortizaciones_diferidos AS AD
									WHERE activo=1 AND id_empresa=$id_empresa AND id_amortizacion=$id_documento
									) AS AD
						SET D.saldo = D.saldo-AD.valor
						WHERE D.activo=1 AND D.id_empresa=$id_empresa AND D.id=AD.id_diferido
									";
		}
		echo $sql;
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se desconto la cuota de los diferidos");</script>';
		}
	}

	// function rollback($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,$accion,$mysql){
	// 	moverCuentasDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,'descontabilizar',$mysql);
	// }

	// EDITAR UN DOCUMENTO GENERADO
	function modificarDocumentoGenerado($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql){

		//ACTUALIZAMOS LA REMISION A ESTADO 0 'SIN GUARDAR'
		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$id_documento' AND activo=1 AND id_empresa=$id_empresa";
		$query = $mysql->query($sql,$mysql->link);

		if (!$query) {
			echo '<script>
					alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}

		//DESCONTABILIZAMOS LA REMNISION
		moverCuentasDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,'descontabilizar',$mysql);

	 	// SALDO DE LOSN DIFERIDOS
		modificarSaldoDiferidos($id_documento,$opcGrillaContable,$id_empresa,'agregar',$mysql);

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					  VALUES($id_documento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','AM','Amortizacion','".$_SESSION['SUCURSAL']."','".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$query = $mysql->query($sql,$mysql->link);

		echo "<script>
				Ext.get('contenedor_Amortizacion').load({
		    		url     : 'amortizaciones/amortizacion/grillaContable.php',
		    		scripts : true,
		    		nocache : true,
		    		params  :
		    		{
						opcGrillaContable : '$opcGrillaContable',
						id_documento      : $id_documento,
		    		}
		    	});

				document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
			</script>";
	}

	// ========================== FUNCION PARA CAMBIAR EL ESTADO DE UN DOCUMENTO CRUZADO =====================================================//
	function cambiaEstadoDocumentoCruce($idDocumento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$id_bodega,$id_sucursal,$id_empresa,$link){
		$sqlCruce   = "SELECT id,id_consecutivo_referencia,consecutivo_referencia,nombre_consecutivo_referencia
						FROM $tablaInventario
						WHERE activo=1 AND $idTablaPrincipal=$idDocumento
						GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";
		$queryCruce = mysql_query($sqlCruce,$link);

		// RECORRER LOS DOCUMENTOS CRUZADOS EN ESTE DOCUMENTO
		$whereRemisiones = ($tablaInventario=='ventas_remisiones_inventario')? 'AND VR.id<>'.$idDocumento : '';
		$whereFacturas   = ($tablaInventario=='ventas_facturas_inventario')? 'AND VF.id<>'.$idDocumento  : '';

		while ($row = mysql_fetch_assoc($queryCruce)) {
			$contRemisiones = 0;
			$contFacturas   = 0;

			// VALIDACION QUE EL PEDIDO NO ESTE CRUZADO CON OTRO DOCUMENTO
			if ($row['nombre_consecutivo_referencia']=='Pedido') {
				$sql   = "SELECT COUNT(VRI.id) AS cont,VRI.id_remision_venta,VR.Tercero,VR.estado
							FROM ventas_remisiones_inventario AS VRI,
								ventas_remisiones AS VR
							WHERE VRI.activo = 1
								AND VRI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
								AND VRI.consecutivo_referencia        = '$row[consecutivo_referencia]'
								AND VRI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
								AND VR.id=VRI.id_remision_venta
								$whereRemisiones
								AND VR.estado=1";
				$query = mysql_query($sql,$link);

				$contRemisiones = mysql_result($query,0,'cont');

				$sql   = "SELECT COUNT(VFI.id) AS cont,VFI.id_factura_venta,VF.Tercero,VF.estado
							FROM ventas_facturas_inventario AS VFI,
								ventas_facturas AS VF
							WHERE VFI.activo = 1
								AND VFI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
								AND VFI.consecutivo_referencia        = '$row[consecutivo_referencia]'
								AND VFI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
								AND VF.id=VFI.id_factura_venta
								$whereFacturas
								AND VF.estado=1";

				$query        = mysql_query($sql,$link);
				$contFacturas = mysql_result($query,0,'cont');

				// SI LOS CONTADORES SON MAYORES A 0 ENTONCES ESE DOCUMENTO ESTA CRUZADO EN OTRA PARTE, SI NO, ENTONCES RETORNAMOS EL ESTADO DEL DOCUMENTO
				if ($contRemisiones==0 && $contFacturas==0) {
					$sql  = "UPDATE ventas_pedidos SET estado=1
							WHERE activo=1
								AND id_empresa=$id_empresa
								AND id_sucursal=$id_sucursal
								AND id_bodega=$id_bodega
								AND id=$row[id_consecutivo_referencia]";
					$query = mysql_query($sql,$link);
				}
			}
			//VALIDACION QUE LA REMISION NO ESTE CRUZADO CON OTRO DOCUMENTO
			else if($row['nombre_consecutivo_referencia']=='Remision') {
				$sql   = "SELECT COUNT(VFI.id) AS cont,VFI.id_factura_venta,VF.Tercero,VF.estado
							FROM ventas_facturas_inventario AS VFI,
								ventas_facturas AS VF
							WHERE VFI.activo = 1
								AND VFI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
								AND VFI.consecutivo_referencia        = '$row[consecutivo_referencia]'
								AND VFI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
								AND VF.id=VFI.id_factura_venta
								$whereFacturas
								AND VF.estado=1";
				$query        = mysql_query($sql,$link);
				$contRemision = mysql_result($query,0,'cont');

				if ($contRemision==0) {
					$sql   = "UPDATE ventas_remisiones
								SET estado=1
								WHERE activo=1
									AND id_empresa=$id_empresa
									AND id_sucursal=$id_sucursal
									AND id_bodega=$id_bodega
									AND id=$row[id_consecutivo_referencia]";
					$query = mysql_query($sql,$link);
				}
			}
		}
	}

	//=========================== FUNCION PARA VALIDAR SI SE CRUZO EL DOCUMENTO ==============================================================//
	function validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link){
		$tipo_documento_cruce = ($opcGrillaContable=='FacturaVenta')? 'FV' : 'RV';
		$texto = ($opcGrillaContable=='FacturaVenta')? 'Factura de venta' : 'Remision';

		$sqlNota    = "SELECT consecutivo_documento, tipo_documento
						FROM asientos_colgaap
						WHERE activo=1
							AND id_documento_cruce = '$idDocumento'
							AND tipo_documento_cruce='$tipo_documento_cruce'
							AND id_documento<>'$idDocumento'
							AND tipo_documento<>'$tipo_documento_cruce'
							AND id_empresa = '$id_empresa'
							AND id_sucursal = '$id_sucursal'
						GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';

		while ($row=mysql_fetch_array($queryNota)) { $doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento']; }
		if ($doc_cruces != '') {
			echo '<script>
					alert("Aviso!\nEsta '.$texto.' tiene relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nCancele los documentos cruce para editar.");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>'; exit; }
	}


	//=========================== FUNCION PARA GUARDAR LA OBSERVACION INDIVIDUAL POR ARTICULO =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql){
		// CONSULTAR EL ESTADO Y EL CONSECUTIVO DEL DOCUMENTO
		$sql="SELECT consecutivo,estado FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo');
		$estado      = $mysql->result($query,0,'estado');

		if ($estado==3) {
			echo '<script>
					alert("Error!\nEl documento ya se encuentra cancelado");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else if ($consecutivo>0 && $estado==1) {
			$sql   = "UPDATE $tablaPrincipal SET estado=3 WHERE id='$id_documento' AND activo=1 AND id_empresa=$id_empresa";
			//DESCONTABILIZAMOS LA REMNISION
			moverCuentasDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,'descontabilizar',$mysql);
			// SALDO DE LOSN DIFERIDOS
			modificarSaldoDiferidos($id_documento,$opcGrillaContable,$id_empresa,'agregar',$mysql);
		}
		else if ($consecutivo>0 ) {
			$sql   = "UPDATE $tablaPrincipal SET estado=3 WHERE id='$id_documento' AND activo=1 AND id_empresa=$id_empresa";
		}
		else{
			$sql   = "UPDATE $tablaPrincipal SET activo=0 WHERE id='$id_documento' AND activo=1 AND id_empresa=$id_empresa";
		}

		//ACTUALIZAMOS EL DOCUMENTO A CANCELAR
		$query = $mysql->query($sql,$mysql->link);

		if (!$query) {
			echo '<script>
					alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					  VALUES($id_documento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','AM','Amortizacion','".$_SESSION['SUCURSAL']."','".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$query = $mysql->query($sql,$mysql->link);

		echo "<script>
				nuevaAmortizacion();
				document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
			</script>";
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql){
 		// CONSULTAR LA CABECERA
		$sql="SELECT fecha_documento,consecutivo FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		$consecutivo     = $mysql->result($query,0,'consecutivo');

 		$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
 		$query=$mysql->query($sql,$mysql->link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($query) {
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');
			
			//INSERTAR EL LOG DE EVENTOS
			$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						  VALUES($id_documento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','AM','Amortizacion','".$_SESSION['SUCURSAL']."','".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$query = $mysql->query($sql,$mysql->link);
			echo "<script>
					Ext.get('contenedor_Amortizacion').load({
			    		url     : 'amortizaciones/amortizacion/grillaContable.php',
			    		scripts : true,
			    		nocache : true,
			    		params  :
			    		{
							opcGrillaContable : '$opcGrillaContable',
							id_documento      : $id_documento,
			    		}
			    	});
					document.getElementById('titleDocumento$opcGrillaContable').innerHTML='Amortizacion<br>N $consecutivo';
					document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
				</script>";
		}
		else{
			echo '<script>
					alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$mysql){


		$sql="SELECT estado,consecutivo FROM $tablaPrincipal WHERE id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);

		$estado      = $mysql->result($query,0,'estado');
		$consecutivo = $mysql->result($query,0,'consecutivo');

		if ($estado==1) {
			$mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el';
		}

		if ($estado>0) {
			echo'<script>
						alert("'.$mensaje.'");

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
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Amortizacion<br>N. '.$consecutivo.'";
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			exit;
		}
	}

	// FUNCION PARA CARGAR LOS DIFERIDOS
	function cargarDiferidos($id_documento,$fecha,$id_sucursal,$opcGrillaContable,$id_empresa,$mysql){
		//ELIMINAR LOS DIFERIDOS QUE ESTEN CARGADOS
		$sql="DELETE FROM amortizaciones_diferidos WHERE activo=1 AND id_empresa=$id_empresa AND id_amortizacion=$id_documento";
		$query=$mysql->query($sql,$mysql->link);

		// CONSULTAR LOS DIFERIDOS A AGREGAR
		$whereIdSucursal =($id_sucursal<>'todas')? " AND id_sucursal= $id_sucursal" : "" ;
		$sql="SELECT
				id,
				id_documento,
				tipo_documento,
				consecutivo_documento,
				id_tercero,
				documento_tercero,
				tercero,
				fecha_inicio,
				estado,
				id_cuenta_debito,
				cuenta_debito,
				descripcion_cuenta_debito,
				centro_costos_debito,
				id_cuenta_credito,
				cuenta_credito,
				descripcion_cuenta_credito,
				centro_costos_credito,
				id_centro_costos,
				cod_centro_costos,
				centro_costos,
				valor,
				meses,
				saldo,
				id_sucursal
			FROM diferidos WHERE activo=1 AND id_empresa=$id_empresa AND estado='Activo' AND saldo>0 AND fecha_inicio<='$fecha' $whereIdSucursal";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$valor = $row['valor'];
			$meses = $row['meses'];
			$saldo = $row['saldo'];
			$cuota = round( ($valor / $meses),$_SESSION['DECIMALESMONEDA'] );

			if ($cuota>0){
				$valueInsert .="(
									'$id_documento',
									'$row[id]',
									'$row[id_documento]',
									'$row[tipo_documento]',
									'$row[consecutivo_documento]',
									'$row[id_tercero]',
									'$row[documento_tercero]',
									'$row[tercero]',
									'$row[fecha_inicio]',
									'$row[id_cuenta_debito]',
									'$row[cuenta_debito]',
									'$row[descripcion_cuenta_debito]',
									'$row[centro_costos_debito]',
									'$row[id_cuenta_credito]',
									'$row[cuenta_credito]',
									'$row[descripcion_cuenta_credito]',
									'$row[centro_costos_credito]',
									'$row[id_centro_costos]',
									'$row[cod_centro_costos]',
									'$row[centro_costos]',
									'$cuota',
									'$row[meses]',
									'$row[saldo]',
									'$row[id_sucursal]',
									'$id_empresa'
								),";
			}

		}

		// INSERTAR LOS DIFERIDOS
		if ($valueInsert=='') {
			echo '<script>alert("Aviso\nNo hay diferidos regsitrados en esa fecha!");</script>';
			exit;
		}
		$valueInsert = substr($valueInsert,0,-1);
		$sql="INSERT INTO amortizaciones_diferidos
			(
				id_amortizacion,
				id_diferido,
				id_documento,
				tipo_documento,
				consecutivo_documento,
				id_tercero,
				documento_tercero,
				tercero,
				fecha_inicio,
				id_cuenta_debito,
				cuenta_debito,
				descripcion_cuenta_debito,
				centro_costos_debito,
				id_cuenta_credito,
				cuenta_credito,
				descripcion_cuenta_credito,
				centro_costos_credito,
				id_centro_costos,
				cod_centro_costos,
				centro_costos,
				valor,
				meses,
				saldo,
				id_sucursal,
				id_empresa

			) VALUES $valueInsert ";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			include('functions_body_article.php');

			echo cargaArticulosSave($id_documento,$id_empresa,$opcGrillaContable,$mysql);
		}
		else{
			echo '<script>alert("Error\nNo se insertaron los diferidos");</script>';
		}
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,$link){
		if ($opcGrillaContable=='CotizacionVenta'){
			$camposFecha = 'fecha_inicio AS fecha1,fecha_finalizacion AS fecha2';
		}
		else if ($opcGrillaContable=='PedidoVenta'){
			$camposFecha = 'fecha_inicio AS fecha1,fecha_finalizacion AS fecha2';
		}
		else if ($opcGrillaContable=='RemisionesVenta'){
			$camposFecha = 'fecha_inicio AS fecha1,fecha_finalizacion AS fecha2';
		}
		else if ($opcGrillaContable=='FacturaVenta'){
			$camposFecha = 'fecha_inicio AS fecha1,fecha_vencimiento AS fecha2';
		}

		// CONSULTAR EL DOCUMENTO
		$sql="SELECT $camposFecha FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		// $fecha_documento = mysql_result($query,0,'fecha_documento');
		$fecha1    = mysql_result($query,0,'fecha1');
		$fecha2     = mysql_result($query,0,'fecha2');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha1)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha1)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha2)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha2)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont
				FROM nota_cierre
				WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND
				(
					(fecha_nota>='$fecha_inicio_buscar_1' AND fecha_nota<='$fecha_fin_buscar_1') OR
					(fecha_nota>='$fecha_inicio_buscar_2' AND fecha_nota<='$fecha_fin_buscar_2')
				)";

		$query = mysql_query($sql,$link);
		$cont2  = mysql_result($query,0,'cont');

		if ($cont1>0 || $cont2>0){
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}
	}

?>
