<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		if ($opc<>'UpdateFormaPago') {
			verificaCierre($id,$opcGrillaContable,$tablaPrincipal,$id_empresa,$link);
		}
	}

	switch ($opc) {
		case 'UpdateFechaDocumento':
			UpdateFechaDocumento($id,$fecha,$tablaPrincipal,$id_empresa,$mysql);
			break;

		case 'buscarTercero':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarTercero($id,$codTercero,$inputId,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql);
			break;

		case 'updateCcos':
			verificaEstadoDocumento($id,$opcGrillaContable,$link);
			updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id,$id_empresa,$mysql);
			break;

		case 'cargaHeadInsertUnidades' :
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaDescripcionArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'buscarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($opcGrillaContable,$valor_campo,$cont,$id,$id_bodega,$id_sucursal,$id_empresa,$mysql);
			break;

		case 'deleteArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			deleteArticulo($cont,$id,$idInsertArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$cantInvArticulo,$costoArticulo,$cantidad,$mysql);
			break;

		case 'retrocederArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
		 	retrocederArticulo($exento_iva,$id,$idArticulo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'terminarGenerar':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			terminarGenerar($id,$id_sucursal,$idBodega,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$id_empresa,$mysql);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$mysql);
			break;

		case 'guardarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantInvArticulo,$cantidad,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'actualizaArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($consecutivo,$id,$idInsertArticulo,$cont,$idInventario,$cantInvArticulo,$cantidad,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'guardarObservacion':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$id_sucursal,$id_empresa,$mysql);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$id_sucursal,$id_empresa,$mysql);
			break;

	}

	//=========================== FUNCION PARA ACTUALIZAR LA FECHA DEL DOCUMENTO ==========================================================================//
	function UpdateFechaDocumento($id,$fecha,$tablaPrincipal,$id_empresa,$mysql){
		$sql="UPDATE $tablaPrincipal SET fecha_documento='$fecha'  WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se actualizo la fechan del documento, si el problema persiste comuniquese con el administrador del sistema");</script>';
		}
	}

	//=========================== FUNCION PARA BUSCAR E INSERTAR UN TERCERO ===============================================================================//
	function buscarTercero($id,$codTercero,$inputId,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql){



		$campo = '';
		$focus = '';
		if($inputId == 'codTercero'.$opcGrillaContable.''){
			$campo     = 'codigo';
			$textAlert = 'Codigo';
			$focus     = 'setTimeout(function(){ document.getElementById("codTercero'.$opcGrillaContable.'").focus(); },100);';
		}
		else if($inputId == 'nitTercero'.$opcGrillaContable.''){
			$campo     = 'numero_identificacion';
			$textAlert = 'Nit';
			$focus     = 'setTimeout(function(){ document.getElementById("nitTercero'.$opcGrillaContable.'").focus(); },100);';
		}
		else if($inputId == 'idCliente'.$opcGrillaContable.''){
			$campo     = 'id';
			$textAlert = 'Codigo';
		}

		$sql   = "SELECT id,numero_identificacion,nombre,codigo FROM terceros WHERE $campo='$codTercero' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$query=$mysql->query($sql,$mysql->link);


		$id_tercero = $mysql->result($query,0,'id');
		$nit        = $mysql->result($query,0,'numero_identificacion');
		$codigo     = $mysql->result($query,0,'codigo');
		$nombre     = $mysql->result($query,0,'nombre');

		$sql = "UPDATE $tablaPrincipal
				SET id_tercero  = '$id_tercero'
				WHERE id='$id'
				AND id_empresa = '$id_empresa'";
		$query=$mysql->query($sql,$mysql->link);

		if($id_tercero > 0){
			echo"<script>
					document.getElementById('nitTercero$opcGrillaContable').value    = '$nit';
					document.getElementById('codTercero$opcGrillaContable').value    = '$codigo';
					document.getElementById('nombreTercero$opcGrillaContable').value = '$nombre';

					id_tercero_$opcGrillaContable    = '$id_tercero';
					codigoTercero$opcGrillaContable  = '$codigo';
					nitTercero$opcGrillaContable     = '$nit';
					nombreTercero$opcGrillaContable = '$nombre';
				</script>";
		}
		else{
			echo"<script>
					document.getElementById('nitTercero$opcGrillaContable').value    = nitTercero$opcGrillaContable
					document.getElementById('codTercero$opcGrillaContable').value    = codigoTercero$opcGrillaContable
					document.getElementById('nombreTercero$opcGrillaContable').value = nombreTercero$opcGrillaContable

					alert('$textAlert de cliente no establecido!');
					$focus
				</script>";
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR EL CENTRO DE COSTOS DEL DOCUMENTO ===============================================================//
	function updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id,$id_empresa,$mysql){
		$sql   = "UPDATE inventario_ajuste SET id_centro_costo='$idCcos' WHERE id='$id' AND id_empresa='$id_empresa' AND activo=1";
		$query = $mysql->query($sql,$mysql->link);
		if(!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; exit; }

		echo'<script>
				document.getElementById("cCos_'.$opcGrillaContable.'").value = "'.$codigo.' '.$nombre.'";
				Win_Ventana_Ccos_'.$opcGrillaContable.'.close();
			</script>';
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		switch ($opcGrillaContable) {
			case 'CotizacionVenta':
				$typeDocument = 'COTIZACION';
				break;

			case 'PedidoVenta':
				$typeDocument = 'PEDIDO';
				break;

			case 'RemisionesVenta':
				$typeDocument = 'REMISION';
				break;

			case 'FacturaVenta':
				$typeDocument = 'FACTURA';
				break;

			default:
				$typeDocument = '';
				break;
		}

		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label" style="width:40px !important;"></div>
							<div class="label" style="width:12%">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label">Descuento</div>
							<div class="label">Precio</div>
							<div class="label">Total</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
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
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="overflow:visible; height:auto;">
							<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIva'.$opcGrillaContable.'">Iva</div>
							<div class="labelSimbolo" id="simboloIva'.$opcGrillaContable.'">$</div>
							<div class="labelTotal" style="height:auto !important;" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"> </div>
							<div class="labelSimbolo"  id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelTotal" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
						</div>
						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL '.$typeDocument.'</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotalAcumulado'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalAcumulado'.$opcGrillaContable.').toFixed(2);
					document.getElementById("ivaAcumulado'.$opcGrillaContable.'").innerHTML      = parseFloat(ivaAcumulado'.$opcGrillaContable.').toFixed(2);
					document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML    = parseFloat(total'.$opcGrillaContable.').toFixed(2);

					document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="campo"><input type="text" id="cantInvArticulo'.$opcGrillaContable.'_'.$cont.'" readonly ></div>

				<div class="campo ">
					<input type="text" id="cantidad'.$opcGrillaContable.'_'.$cont.'" value="0" '.$readonly_descuento.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo"><input type="text" id="ajuste'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/edit.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />

				<script>document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link){
		$selectObservacion = "SELECT observaciones FROM $tablaInventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$observacion       = mysql_result(mysql_query($selectObservacion,$link),0,'observaciones');

		echo'<div style="margin: 10px">
				<div id="renderizaGuardarObservacion'.$opcGrillaContable.'_'.$cont.'"></div>
				<textarea id="observacionArticulo'.$opcGrillaContable.'_'.$cont.'" style="width:300px; height:150px;">'.$observacion.'</textarea>
			</div>';
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaInventario SET observaciones='$observacion' WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){ echo '<script>Win_Ventana_descripcion_Articulo_factura.close(id);</script>'; }
		else{ echo '<script>alert("La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema");</script>'; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
	function buscarArticulo($opcGrillaContable,$valor_campo,$cont,$id,$id_bodega,$id_sucursal,$id_empresa,$mysql){

		$sql = "SELECT I.id,
						I.codigo,
						I.code_bar,
						I.precio_venta,
						IT.costos,
						I.nombre_equipo,
						I.numero_piezas,
						I.inventariable,
						IT.cantidad_minima_stock,
						I.unidad_medida,
						I.cantidad_unidades,
						I.id_impuesto,
						IT.cantidad
					FROM items AS I,
						inventario_totales AS IT
					WHERE I.activo=1
						AND I.id_empresa=$id_empresa
						AND IT.id_item=I.id
						AND I.estado_venta='true'
						AND IT.id_sucursal=$id_sucursal
						AND IT.id_ubicacion= $id_bodega
						AND (I.code_bar = '$valor_campo' OR I.codigo = '$valor_campo')
						AND I.inventariable = 'true'
					LIMIT 0,1";
		$query=$mysql->query($sql,$mysql->link);

		$id                    = $mysql->result($query,0,'id');
		$codigo                = $mysql->result($query,0,'codigo');
		$precio_venta          = $mysql->result($query,0,'precio_venta');
		$costos                = $mysql->result($query,0,'costos');
		$codigoBarras          = $mysql->result($query,0,'code_bar');
		$nombre_unidad         = $mysql->result($query,0,'unidad_medida');
		$numero_unidad         = $mysql->result($query,0,'cantidad_unidades');
		$nombreArticulo        = $mysql->result($query,0,'nombre_equipo');
		$id_impuesto           = $mysql->result($query,0,'id_impuesto');
		$cantidad              = $mysql->result($query,0,'cantidad');
		$cantidad_minima_stock = $mysql->result($query,0,'cantidad_minima_stock');
		$inventariable         = $mysql->result($query,0,'inventariable');

		if($id > 0){

			echo"<script>
					document.getElementById('idArticulo".$opcGrillaContable."_$cont').value      = '$id';
					document.getElementById('nombreArticulo".$opcGrillaContable."_$cont').value  = '$nombreArticulo';
					document.getElementById('unidades".$opcGrillaContable."_$cont').value        = '$nombre_unidad x $numero_unidad';
					document.getElementById('cantInvArticulo".$opcGrillaContable."_$cont').value = '$cantidad';
					document.getElementById('costoArticulo".$opcGrillaContable."_$cont').value   = '$costos';

					setTimeout(function(){ document.getElementById('cantidad".$opcGrillaContable."_$cont').focus(); },50);
				</script>";

		}
		else{
			echo"<script>
					alert('El codigo $valor_campo No se encuentra asignado en el inventario');
					setTimeout(function(){ document.getElementById('eanArticulo".$opcGrillaContable."_$cont').focus(); },100);
					document.getElementById('idArticulo".$opcGrillaContable."_$cont').value      = '0';
					document.getElementById('nombreArticulo".$opcGrillaContable."_$cont').value  = '';
					document.getElementById('unidades".$opcGrillaContable."_$cont').value        = '';
					document.getElementById('cantInvArticulo".$opcGrillaContable."_$cont').value = '';
					document.getElementById('costoArticulo".$opcGrillaContable."_$cont').value   = '';
				</script>";
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederArticulo($exento_iva,$id,$idRegistro,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$sqlArticulo = "SELECT id_inventario,codigo,costo_unitario,cantidad_unidad_medida,nombre_unidad_medida,nombre,cantidad,tipo_descuento,descuento,id_impuesto
						FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal='$id' AND id='$idRegistro' LIMIT 0,1; ";

		$query = mysql_query($sqlArticulo,$link);

		$id_inventario      = mysql_result($query,0,'id_inventario');
		$codigo             = mysql_result($query,0,'codigo');
		$costo              = mysql_result($query,0,'costo_unitario');
		$nombre_unidad      = mysql_result($query,0,'nombre_unidad_medida');
		$nombreArticulo     = mysql_result($query,0,'nombre');
		$numeroPiezas       = mysql_result($query,0,'cantidad_unidad_medida');
		$cantidad_articulo  = mysql_result($query,0,'cantidad');
		$tipoDesc           = mysql_result($query,0,'tipo_descuento');
		$descuento_articulo = mysql_result($query,0,'descuento');
		$id_impuesto        = ($exento_iva=='Si')? 0 : mysql_result($query,0,'id_impuesto');

		if ($tipoDesc=='porcentaje') {
			$imgDescuento    = 'img/porcentaje.png';
			$tituloDescuento = 'En porcentaje';
		}
		else{
			$imgDescuento    = 'img/pesos.png';
			$tituloDescuento = 'En pesos';
		}

		echo'<script>
				document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value           = "'.$nombre_unidad.' x '.$numeroPiezas.'";
				document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value         = "'.$id_inventario.'";
				document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "'.$codigo.'";
				document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value      = "'.$costo.'";
				document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$nombreArticulo.'";
				document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value       = "'.$cantidad_articulo.'";
				document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "'.$descuento_articulo.'";
				document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "'.$id_impuesto.'";
				document.getElementById("ajuste'.$opcGrillaContable.'_'.$cont.'").value = "'.($cantidad_articulo*$costo).'";

				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

				document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","'.$imgDescuento.'");
				document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","'.$tituloDescuento.'");

				//document.getElementById("tipoDescuentoArticulo_'.$cont.'").setAttribute("src","img/reload.png");
			</script>';
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//============================================== FUNCION PARA TERMINAR 'GENERAR' EL DOCUMENTO ===============================================//
	function terminarGenerar($id,$id_sucursal,$idBodega,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$id_empresa,$mysql){
		// PROCESAR EL DOCUMENTO DE AJUSTE
		$sql="UPDATE inventario_ajuste SET estado=1 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id ";
		$query=$mysql->query($sql,$mysql->link);

		$sql   ="SELECT consecutivo,estado FROM inventario_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query =$mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo');

		// CLASE PADRE CON LAS FUNCIONES DE DOCUMENTOS
		include 'Class.DocumentFunctions.php';

		// PROCESAR LAS ENTRADAS Y LAS SALIDAS DEL INVENTARIO
		include 'Class.Remision.php';
		$objectRemision = new ClassRemision($id,$id_empresa,$mysql);
		$objectRemision->generate();

		include 'Class.EntradaAlmacen.php';
		$objectEntrada = new ClassEntradaAlmacen($id,$id_empresa,$mysql);
		$objectEntrada->generate();

		// LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Ajuste Inventario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','AI')";
		$query=$mysql->query($sql,$mysql->link);

		echo'<script>
				Ext.get("contenedor_'.$opcGrillaContable.'").load({
					url     : "ajuste_inventario/bd/grillaContableBloqueada.php",
					scripts : true,
					nocache : true,
					params  :
					{
						id_documento      : "'.$id.'",
						opcGrillaContable : "'.$opcGrillaContable.'",
					}
				});
                document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Ajuste de Inventario<br>N. '.$consecutivo.'";
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';

	}

	//=============================================== FUNCION PARA MODIFICAR UN DOCUMENTO YA GENERADO ===============================================================//
	function modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$mysql){
		$sql   ="SELECT consecutivo,estado FROM inventario_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query =$mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo');
		$estado      = $mysql->result($query,0,'estado');

		if ($estado==3) {
			echo '<script>alert("El documento esta cancelado!");</script>';
			return;
		}
		if ($estado==0) {
			echo '<script>alert("El documento ya esta editado!");</script>';
			return;
		}
		else if ($estado==1) {
			// CLASE PADRE CON LAS FUNCIONES DE DOCUMENTOS
			include 'Class.DocumentFunctions.php';

			// PROCESAR LAS ENTRADAS Y LAS SALIDAS DEL INVENTARIO
			include 'Class.Remision.php';
			$objectRemision = new ClassRemision($id,$id_empresa,$mysql);
			$objectRemision->editCancel();

			include 'Class.EntradaAlmacen.php';
			$objectEntrada = new ClassEntradaAlmacen($id,$id_empresa,$mysql);
			$objectEntrada->editCancel();
		}

		$sql = "UPDATE inventario_ajuste SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=$mysql->query($sql,$mysql->link);

		// LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Ajuste Inventario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','AI')";
		$query=$mysql->query($sql,$mysql->link);

		echo'<script>
					 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "ajuste_inventario/grillaContable.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_documento      : "'.$id.'"
							}
						});
						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';

	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantInvArticulo,$cantidad,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql){

		$sql = "INSERT INTO $tablaInventario(
							  	$idTablaPrincipal,
								id_inventario,
								cantidad_inventario,
								cantidad,
								costo_unitario
								)
						VALUES( '$id',
								'$idInventario',
								'$cantInvArticulo',
								'$cantidad',
								'$costoArticulo')";
		$query=$mysql->query($sql,$mysql->link);

		$sql="SELECT LAST_INSERT_ID()";
		$query=$mysql->query($sql,$mysql->link);
		$lastId = mysql_result($query,0,0);
		$ajuste = $cantidad-$cantInvArticulo;
		if ($ajuste<>0) {
			$ajuste = ($ajuste>0)? '+'.abs($ajuste) : '-'.abs($ajuste);
			$color = ($ajuste>0)? '#28B463' : '#E74C3C ';
			$tipo   = ($ajuste>0)? "ingreso" : "salida";
		}

		if($lastId > 0){

			echo"<script>
					document.getElementById('idInsertArticulo".$opcGrillaContable."_$cont').value = '$lastId'
					document.getElementById('ajuste".$opcGrillaContable."_$cont').value           = '$ajuste'
					document.getElementById('ajuste".$opcGrillaContable."_$cont').style.color           = '$color'
					document.getElementById('divImageSave".$opcGrillaContable."_$cont').setAttribute('title','Actualizar Articulo');
					document.getElementById('imgSaveArticulo".$opcGrillaContable."_$cont').setAttribute('src','img/reload.png');
					document.getElementById('divImageSave".$opcGrillaContable."_$cont').style.display        = 'none';
					document.getElementById('divImageDeshacer".$opcGrillaContable."_$cont').style.display    = 'none';

					document.getElementById('descripcionArticulo".$opcGrillaContable."_$cont').style.display = 'block';
					document.getElementById('deleteArticulo".$opcGrillaContable."_$cont').style.display      = 'block';

					calcTotalDoc".$opcGrillaContable."($cantInvArticulo,$cantidad,$costoArticulo,'agregar','$tipo');
				</script>".cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);

		}
		else{ echo "Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema "; }
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaArticulo($consecutivo,$id,$idInsertArticulo,$cont,$idInventario,$cantInvArticulo,$cantidad,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql){
		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo
		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura

		$sql   = "SELECT cantidad,costo_unitario,cantidad_inventario FROM $tablaInventario WHERE id='$idInsertArticulo' AND $idTablaPrincipal='$id' ";
		$query = $mysql->query($sql,$mysql->link);

		$cantidad_inventario_anterior = $mysql->result($query,0,'cantidad_inventario');
		$cantidad_anterior            = $mysql->result($query,0,'cantidad');
		$costo_unitario_anterior      = $mysql->result($query,0,'costo_unitario');

		$ajuste = $cantidad_inventario_anterior-$cantidad_anterior;
		if ($ajuste<>0) {
			// $ajuste = ($ajuste>0)? '-'.abs($ajuste) : '+'.abs($ajuste);
			$tipo   = ($ajuste<0)? "ingreso" : "salida";
		}

		$script ="calcTotalDoc".$opcGrillaContable."($cantidad_inventario_anterior,$cantidad_anterior,$costo_unitario_anterior,'eliminar','$tipo');";

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		// echo'<script>calcTotalDoc'.$opcGrillaContable.'('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'",'.$cont.');</script>';
		$sql   = "UPDATE $tablaInventario
						SET id_inventario='$idInventario',
							cantidad       ='$cantidad',
							costo_unitario ='$costoArticulo'
						WHERE $idTablaPrincipal=$id
						AND id=$idInsertArticulo";
		$query = $mysql->query($sql,$mysql->link);
		$cantInvArticulo= abs($cantInvArticulo);
		$ajuste = $cantInvArticulo-$cantidad;
		if ($ajuste<>0) {
			$ajuste = ($ajuste>0)? '-'.abs($ajuste) : '+'.abs($ajuste);
			$tipo   = ($ajuste>0)? "ingreso" : "salida";
		}

		if ($query) {
			echo"<script>
					document.getElementById('ajuste".$opcGrillaContable."_$cont').value                   = '$ajuste'
					document.getElementById('divImageSave".$opcGrillaContable."_$cont').style.display     = 'none';
					document.getElementById('divImageDeshacer".$opcGrillaContable."_$cont').style.display = 'none';
					$script
					//llamamos la funcion para recalcular el costo de la factura
					calcTotalDoc".$opcGrillaContable."($cantInvArticulo,$cantidad,$costoArticulo,'agregar','$tipo');
				</script>";
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteArticulo($cont,$id,$idInsertArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$cantInvArticulo,$costoArticulo,$cantidad,$mysql){
		$ajuste = $cantInvArticulo-$cantidad;
		if ($ajuste<>0) {
			// $ajuste = ($ajuste>0)? '-'.abs($ajuste) : '+'.abs($ajuste);
			$tipo   = ($ajuste<0)? "ingreso" : "salida";
		}
		$sql   = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND id='$idInsertArticulo'";
		$query = $mysql->query($sql,$mysql->link);
		if(!$query){ echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo '<script>
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
					calcTotalDoc'.$opcGrillaContable.'('.$cantInvArticulo.','.$cantidad.','.$costoArticulo.',"eliminar","'.$tipo.'");
				</script>';
		}
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
	function cancelarDocumento($id,$opcGrillaContable,$id_sucursal,$id_empresa,$mysql){

		$sql   ="SELECT consecutivo,estado FROM inventario_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query =$mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo');
		$estado      = $mysql->result($query,0,'estado');

		// POR DEFECTO SE ACTUALIZA EL ESTADO DEL DOCUMENTO A 3
		$sql = "UPDATE inventario_ajuste SET estado=3 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";

		if ($estado==3) {
			echo '<script>alert("El documento ya esta cancelado!");</script>';
			return;
		}
		else if ($estado==1) {
			// CLASE PADRE CON LAS FUNCIONES DE DOCUMENTOS
			include 'Class.DocumentFunctions.php';

			// PROCESAR LAS ENTRADAS Y LAS SALIDAS DEL INVENTARIO
			include 'Class.Remision.php';
			$objectRemision = new ClassRemision($id,$id_empresa,$mysql);
			$objectRemision->editCancel();

			include 'Class.EntradaAlmacen.php';
			$objectEntrada = new ClassEntradaAlmacen($id,$id_empresa,$mysql);
			$objectEntrada->editCancel();
		}
		else if ($estado==0 && ($consecutivo=='' OR $consecutivo==0 OR is_null($consecutivo) ) ){
			$sql = "UPDATE inventario_ajuste SET activo=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		}

		$query=$mysql->query($sql,$mysql->link);

		// LOG DE EVENTOS
		$sql = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Ajuste Inventario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','AI')";
		$query=$mysql->query($sql,$mysql->link);

		echo'<script>
				nueva'.$opcGrillaContable.'();
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';

	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($id,$opcGrillaContable,$id_sucursal,$id_empresa,$mysql){

 		$sql="SELECT consecutivo FROM inventario_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
 		$query=$mysql->query($sql,$mysql->link);
 		$consecutivo = $mysql->result($query,0,'consecutivo');

 		$sql="UPDATE inventario_ajuste SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
 		$query=$mysql->query($sql,$mysql->link);

 		$sql = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Ajuste Inventario',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','AI')";
 		$query=$mysql->query($sql,$mysql->link);

		echo'<script>
				Ext.get("contenedor_'.$opcGrillaContable.'").load({
					url     : "ajuste_inventario/grillaContable.php",
					scripts : true,
					nocache : true,
					params  :
					{
						id_documento      : "'.$id.'",
						opcGrillaContable : "'.$opcGrillaContable.'",
					}
				});
                document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Ajuste de Inventario<br>N. '.$consecutivo.'";
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';

 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$link){


		$sql="SELECT estado,id_bodega,consecutivo FROM $tablaPrincipal WHERE id=$id_documento";
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
								id_documento      : "'.$id_documento.'"
							}
						});

						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Ajuste de inventario<br>N. '.$consecutivo.'";
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			exit;
		}

	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$opcGrillaContable,$tablaPrincipal,$id_empresa,$link){
		$camposFecha = 'fecha_documento AS fecha1,fecha_documento AS fecha2';

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