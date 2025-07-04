<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");

	//echo"<script>console.log('$observacion');</script>";
	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		verificaCierre($id,$tablaPrincipal,$id_empresa,$link);
	}

	switch ($opc) {

		case 'guardarFecha':
			guardarFecha($tablaPrincipal,$opcGrillaContable,$id_documento,$fecha,$mysql);
			break;

		case 'actualizaDestinoTraslado':
			actualizaDestinoTraslado($tablaPrincipal,$opcGrillaContable,$id_documento,$id_destino,$nombre_destino,$destino,$mysql);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;
        //SE OCULTA EL IMPUESTO Y EL CENTRO DE COSTOS EN LA REQUISICION
		case 'ventanaDescripcionArticulo':
			ventanaDescripcionArticulo($cont,$idArticulo,$idInsertArticulo,$observacionArt,$id,$id_centro_costos,$codigo_centro_costo,$centro_costo,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$tablaPrincipal,$id_empresa,$mysql);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($id_impuesto,$idCentroCostos,$observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$id_ccos,$id_impuesto,$link);
			break;


		case 'guardarObservacionArt':
			guardarObservacionArt($tablaInventario,$observacion,$idInsertArticulo,$opcGrillaContable,$cont,$mysql);
			break;

		case 'buscarArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($campo,$valorArticulo,$idArticulo,$id_empresa,$opcGrillaContable,$whereBodega,$mysql);
			break;

		case 'deleteArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			deleteArticulo($cont,$id,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'retrocederArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
		 	retrocederArticulo($id,$idArticulo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'terminarGenerar':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			terminarGenerar($id,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$mysql);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id_documento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'guardarArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($opcGrillaContable,$consecutivo,$cont,$idInsertArticulo,$idInventario,$cantArticulo,$id_documento,$tablaInventario,$idTablaPrincipal,$id_sucursal,$id_ubicacion,$mysql);
			break;

		case 'actualizaArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($id_documento,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$id_sucursal,$id_ubicacion,$mysql);
			break;

		case 'guardarObservacion':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'verificaCantidadArticulo':
			verificaCantidadArticulo($opcGrillaContable,$id,$id_sucursal,$filtro_bodega,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql);
			break;

		case 'loadArticulos':
			loadArticulos($opcGrillaContable);
			break;

	}

	//========================================== GUARDAR FECHA DE DOCUMENTO =========================================//
	function guardarFecha($tablaPrincipal,$opcGrillaContable,$id_documento,$fecha,$mysql){
		$sql="UPDATE $tablaPrincipal SET fecha_documento='$fecha' WHERE activo=1 AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo "<script>alert('Se produjo un error al actualzar la fecha, intentelo de nuevo');</script>";
		}
	}

	//======================== ACTUALIZAR DESTINO DEL TRASLADO =======================//
	function actualizaDestinoTraslado($tablaPrincipal,$opcGrillaContable,$id_documento,$id_destino,$nombre_destino,$destino,$mysql){
		if ($destino=='sucursal'){ $camposUpdate = "id_sucursal_traslado='$id_destino',sucursal_traslado='$nombre_destino'"; }
		else{ $camposUpdate = "id_bodega_traslado='$id_destino',bodega_traslado='$nombre_destino' "; }

		$sql="UPDATE $tablaPrincipal SET $camposUpdate WHERE activo=1 AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo "<script>alert('Se produjo un error al actualizar el destino del inventario');</script>";
		}
	}

	function loadArticulos($opcGrillaContable){
		cargaHeadInsertUnidades('return',1,$opcGrillaContable);
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$styleCamposEliminados = 'display:none'; // EN CASO QUE SE REQUIERA VOLVER A MOSTRAR LOS CAMPOS ELIMINADOS

		switch ($opcGrillaContable) {
			case 'CotizacionVenta':
				$typeDocument = 'COTIZACION';
				break;

			case 'PedidoVenta':
				$typeDocument = 'PEDIDO';
				break;

			case 'EntradaAlmacen':
				$typeDocument = 'ENTRADA DE ALMACEN';
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
							<div class="label" >Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label" style="'.$styleCamposEliminados.'">Descuento</div>
							<div class="label" style="'.$styleCamposEliminados.'">Precio</div>
							<div class="label" style="'.$styleCamposEliminados.'">Total</div>
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
					<div class="contenedorDetalleTotales" style="'.$styleCamposEliminados.'">
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
					//document.getElementById("subtotalAcumulado'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalAcumulado'.$opcGrillaContable.').toFixed(2);
					//document.getElementById("ivaAcumulado'.$opcGrillaContable.'").innerHTML      = parseFloat(ivaAcumulado'.$opcGrillaContable.').toFixed(2);
					//document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML    = parseFloat(total'.$opcGrillaContable.').toFixed(2);

					document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){
		$readonly='';
		// if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		// if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		$styleCamposEliminados = 'display:none';//EN CASO QUE SE REQUIERA VOLVER A MOSTRAR LOS CAMPOS ELIMINADOS

		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" >
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campoNombreArticulo">
					<input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/>
				</div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo">
					<input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/>
				</div>
				<div class="campo">
					<input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'"  onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/>
				</div>

				<div class="campo campoDescuento" style="'.$styleCamposEliminados.'">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" '.$readonly_descuento.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/edit.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" class="classInputInsertArticulo" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" >

				<script>document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();</script>';

		if($formaConsulta == 'return'){
			return $body;
		}
		else{
			echo $body;
		}
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaDescripcionArticulo($cont,$idArticulo,$idInsertArticulo,$observacionArt,$id,$id_centro_costos,$codigo_centro_costo,$centro_costo,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$tablaPrincipal,$id_empresa,$mysql){

		// CONSULTAR EL ESTADO DEL DOCUMENTO
		$sql    ="SELECT estado FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query  = $mysql->query($sql,$mysql->link);
		$estado = $mysql->result($query,0,'estado');
		$block  = ($estado<>0)? 'true' : '' ;

		$sql   = "SELECT observaciones FROM $tablaInventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$query = $mysql->query($sql,$link);
		$observacionArt   = $mysql->result($query ,0,'observaciones');

		$btnObs      = ($block=='true')? '' : '<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5px; border-left: 1px solid #D4D4D4;" onclick="guardarObservacionArt('.$cont.')"><img src="img/save_true.png" style="cursor:pointer;width:16px;height:16px;" title="Guardar Observacion"></div>' ;
		$txtAreaBloq = ($block=='true')? 'readonly="true"' : '';

		echo '<div style="float:left;width:88%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;height:180px;">
 					<div style="float:left;width:99%;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">OBSERVACION</div>
 					'.$btnObs.'
 					<textarea id="observacionArt'.$opcGrillaContable.'" '.$txtAreaBloq.' >'.$observacionArt.'</textarea>
 				</div>';

	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($id_impuesto,$id_centro_costos,$observacionArt,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$id_ccos,$id_impuesto,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaInventario SET observaciones='$observacion',id_impuesto='$id_impuesto',id_centro_costos='$id_centro_costos' WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){ echo '<script>Win_Ventana_descripcion_Articulo_factura.close(id);</script>'; }
		else{ echo '<script>alert("La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema");</script>'; }
	}

	function guardarObservacionArt($tablaInventario,$observacionArt,$idInsertArticulo,$opcGrillaContable,$cont,$mysql){
		$sql="UPDATE $tablaInventario SET observaciones='$observacionArt' WHERE id='$idInsertArticulo' AND activo=1";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					MyLoading2("off");
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"No se guardo la Observacion",duracion:2000});
				</script>';
		}
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO =============================observacionCuentaRequisicionCompra===========================================//
	function buscarArticulo($campo,$valorArticulo,$idArticulo,$id_empresa,$opcGrillaContable,$whereBodega,$mysql){

		$sqlArticulo = "SELECT I.id,
							I.codigo,
							I.code_bar,
							I.precio_venta,
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
							AND I.estado_compra='true'
							$whereBodega
							AND (I.code_bar = '$valorArticulo' OR I.codigo = '$valorArticulo')
						LIMIT 0,1";

		$query = $mysql->query($sqlArticulo,$mysql->link);

		$id                    = $mysql->result($query,0,'id');
		$codigo                = $mysql->result($query,0,'codigo');
		$precio_venta          = $mysql->result($query,0,'precio_venta');
		$codigoBarras          = $mysql->result($query,0,'code_bar');
		$nombre_unidad         = $mysql->result($query,0,'unidad_medida');
		$numero_unidad         = $mysql->result($query,0,'cantidad_unidades');
		$nombreArticulo        = $mysql->result($query,0,'nombre_equipo');
		$id_impuesto           = $mysql->result($query,0,'id_impuesto');
		$cantidad              = $mysql->result($query,0,'cantidad');
		$cantidad_minima_stock = $mysql->result($query,0,'cantidad_minima_stock');
		$inventariable         = $mysql->result($query,0,'inventariable');

		if($id > 0){
			if ($cantidad>0 ) {
				echo'<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       = "'.$nombre_unidad.' x '.$numero_unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "'.$id.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value    = "'.$codigo.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "'.$nombreArticulo.'";

						setTimeout(function(){ document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },50);
					</script>';
			}
			else{
				echo'<script>
						alert("El articulo se agoto en esta bodega!");
						setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "0";
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       = "";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "";
					</script>';
			}
		}
		else{
			echo'<script>
					alert("El codigo '.$valorArticulo.' No se encuentra asignado en el inventario");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     ="0";
					document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       ="";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value ="";
				</script>';
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederArticulo($id,$idRegistro,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql){

		$sqlArticulo ="SELECT
						id_inventario,
						codigo,
						id_unidad_medida,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad,
						costo_unitario,
						observaciones
						FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal='$id' AND id='$idRegistro' LIMIT 0,1; ";

		$query = $mysql->query($sqlArticulo,$mysql->link);

		$id_inventario          = $mysql->result($query,0,'id_inventario');
		$codigo                 = $mysql->result($query,0,'codigo');
		$id_unidad_medida       = $mysql->result($query,0,'id_unidad_medida');
		$nombre_unidad_medida   = $mysql->result($query,0,'nombre_unidad_medida');
		$cantidad_unidad_medida = $mysql->result($query,0,'cantidad_unidad_medida');
		$nombre                 = $mysql->result($query,0,'nombre');
		$cantidad               = $mysql->result($query,0,'cantidad');
		$costo_unitario         = $mysql->result($query,0,'costo_unitario');
		$observaciones          = $mysql->result($query,0,'observaciones');

		echo "<script>
				document.getElementById('idArticulo$opcGrillaContable"."_$cont').value               = '$id_inventario';
				document.getElementById('eanArticulo$opcGrillaContable"."_$cont').value              = '$codigo';
				document.getElementById('nombreArticulo$opcGrillaContable"."_$cont').value           = '$nombre';
				document.getElementById('unidades$opcGrillaContable"."_$cont').value                 = '$nombre_unidad_medida x $cantidad_unidad_medida'
				document.getElementById('cantArticulo$opcGrillaContable"."_$cont').value             = ($cantidad * 1);
				document.getElementById('divImageSave$opcGrillaContable"."_$cont').style.display     = 'none';
				document.getElementById('divImageDeshacer$opcGrillaContable"."_$cont').style.display = 'none';
			</script>";

	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteArticulo($cont,$id,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql){
		$sql   = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND id='$idArticulo'";
		$query = $mysql->query($sql,$mysql->link);
		if(!$query){ echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{ echo '<script>(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")); </script>'; }
	}

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA FACTURA, COTIZACION, PEDIDO Y CARGAR UNA NUEVA ==============================//
	function terminarGenerar($id,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$mysql){
		// VALIDAR EXISTENCIAS DEL INVENTARIO A TRASLADAR
		validarCantidadInventarioTraslado($id,'trasladar',$mysql);

		$sql    = "UPDATE $tablaPrincipal SET estado='1'
					WHERE id='$id'";
		$queryU  = $mysql->query($sql,$mysql->link);

		//TRASLADAR INVENTARIO ENTRE LAS BODEGAS
		trasladarInventario($id,'trasladar',"Generar");

		$sql   = "SELECT consecutivo,id_sucursal,id_bodega FROM $tablaPrincipal WHERE id='$id'";
		$query = $mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo');
		$id_sucursal = $mysql->result($query,0,'id_sucursal');
		$id_bodega   = $mysql->result($query,0,'id_bodega');

		if ($queryU){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','TDI','Traslado de Inventario',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = $mysql->query($sqlLog,$mysql->link);

			echo"<script>
					Ext.get('contenedor_$opcGrillaContable').load({
						url     : 'traslados/grillaContableBloqueada.php',
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable : '$opcGrillaContable',
							id_documento      : '$id',
							filtro_sucursal   : '$id_sucursal',
							filtro_bodega     : '$id_bodega',
						}
					});

					Ext.getCmp('btnNueva$opcGrillaContable').enable();
					document.getElementById('titleDocumento$opcGrillaContable').innerHTML='Traslado de Inventario<br>N. $consecutivo';
					document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
				</script>";
		}
		else{
			echo "<script>
					alert('Se produjo un error al generar el documento');
					document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
				</script>";
		}
	}

	function trasladarInventario($id_documento,$accion_inventario,$accion_documento){

		global $mysql;
		// consultar la informacion del documento
		$sql = "SELECT 
					id_sucursal,
					sucursal,
					id_bodega,
					bodega,
					id_sucursal_traslado,
					sucursal_traslado,
					id_bodega_traslado,
					bodega_traslado,
					id_empresa,
					consecutivo,
					fecha_documento
				FROM inventario_traslados WHERE id=$id_documento";
		$query = $mysql->query($sql);
		$id_empresa   = $mysql->result($query,0,"id_empresa");
		$id_sucursal  = $mysql->result($query,0,"id_sucursal");
		$sucursal     = $mysql->result($query,0,"sucursal");
		$id_bodega    = $mysql->result($query,0,"id_bodega");
		$bodega       = $mysql->result($query,0,"bodega");
		$consecutivo  = $mysql->result($query,0,"consecutivo");
		$fecha_inicio = $mysql->result($query,0,"fecha_documento");

		$id_sucursal_traslado  = $mysql->result($query,0,"id_sucursal_traslado");
		$sucursal_traslado     = $mysql->result($query,0,"sucursal_traslado");
		$id_bodega_traslado    = $mysql->result($query,0,"id_bodega_traslado");
		$bodega_traslado       = $mysql->result($query,0,"bodega_traslado");

		// consultar los items de ese documento pero solo los que generan movimiento de inventario
		$sql = "SELECT 
						id_inventario AS id,
						codigo,
						nombre,
						nombre_unidad_medida AS unidad_medida,
						cantidad_unidad_medida AS cantidad_unidades,
						costo_unitario AS costo,
						cantidad
					FROM inventario_traslados_unidades 
					WHERE id_traslado=$id_documento
					AND activo=1  ";
		$query = $mysql->query($sql);
		$index = 0;
		$items_1 = array();
		$items_2 = array();
		while ($row = $mysql->fetch_assoc($query)) {
			$items_1[$index]                = $row;
			$items_1[$index]["empresa_id"]  = $id_empresa;
			$items_1[$index]["empresa"]     = NULL;
			$items_1[$index]["sucursal_id"] = $id_sucursal_traslado;
			$items_1[$index]["sucursal"]    = $sucursal_traslado;
			$items_1[$index]["bodega_id"]   = $id_bodega_traslado;
			$items_1[$index]["bodega"]      = $bodega_traslado;

			$items_2[$index]                = $row;
			$items_2[$index]["empresa_id"]  = $id_empresa;
			$items_2[$index]["empresa"]     = NULL;
			$items_2[$index]["sucursal_id"] = $id_sucursal;
			$items_2[$index]["sucursal"]    = $sucursal;
			$items_2[$index]["bodega_id"]   = $id_bodega;
			$items_2[$index]["bodega"]      = $bodega;
			
			$index++;
		}
		// GENERAR EL MOVIMIENTO DE INVENTARIO
		include '../../Clases/Inventory.php';
		$obj = new Inventario_pp();

		if ($accion_inventario=="trasladar") {
			$params[0] = [ 
				"documento_id"          => $id_documento,
				"documento_tipo"        => "TDI",
				"documento_consecutivo" => $consecutivo,
				"fecha"                 => $fecha_inicio,
				"accion_inventario"     => "traslado ingreso",
				"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
				"items"                 => $items_1,
				"mysql"                 => $mysql
			];
			$params[1] = [ 
				"documento_id"          => $id_documento,
				"documento_tipo"        => "TDI",
				"documento_consecutivo" => $consecutivo,
				"fecha"                 => $fecha_inicio,
				"accion_inventario"     => "traslado salida",
				"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
				"items"                 => $items_2,
				"mysql"                 => $mysql
			];
		}
		else if($accion_inventario=="reversar"){
			$params[0] = [ 
				"documento_id"          => $id_documento,
				"documento_tipo"        => "TDI",
				"documento_consecutivo" => $consecutivo,
				"fecha"                 => $fecha_inicio,
				"accion_inventario"     => "reversar traslado ingreso",
				"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
				"items"                 => $items_1,
				"mysql"                 => $mysql
			];
			$params[1] = [ 
				"documento_id"          => $id_documento,
				"documento_tipo"        => "TDI",
				"documento_consecutivo" => $consecutivo,
				"fecha"                 => $fecha_inicio,
				"accion_inventario"     => "reversar traslado salida",
				"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
				"items"                 => $items_2,
				"mysql"                 => $mysql
			];
		}

		
		$process_1 = $obj->UpdateInventory($params[0]);
		$process_2 = $obj->UpdateInventory($params[1]);
		// echo " -- --- $id_documento,$accion_inventario,$accion_documento ----";
		// var_dump($params);
		// var_dump($params[1]);
		// var_dump($process_2);
		return;

		$sql="SELECT id_sucursal,id_bodega,id_sucursal_traslado,id_bodega_traslado
				FROM inventario_traslados WHERE activo=1 AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		$id_sucursal          = $mysql->result($query,0,'id_sucursal');
		$id_bodega            = $mysql->result($query,0,'id_bodega');
		$id_sucursal_traslado = $mysql->result($query,0,'id_sucursal_traslado');
		$id_bodega_traslado   = $mysql->result($query,0,'id_bodega_traslado');
		$arrayDatos = array(
							"campo_fecha"             => "fecha_documento",
							"tablaPrincipal"          => "inventario_traslados",
							"id_documento"            => "$id_documento",
							"campos_tabla_inventario" => " id_inventario AS id_item ",
							"tablaInventario"         => "inventario_traslados_unidades",
							"idTablaPrincipal"        => "id_traslado",
							"documento"               => "TI",
							"descripcion_documento"   => "Traslado de inventario"
							);

		if ($accion=="trasladar") {
			$sql1 = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS cantidad_traslado,costo_unitario,
															id_inventario AS id_item
														FROM
															inventario_traslados_unidades
														WHERE
															id_traslado = '$id_documento'
														AND
															activo = 1
														GROUP BY
															id_inventario) AS CFI
										SET
											IT.cantidad = IT.cantidad + CFI.cantidad_traslado,
											IT.costos=CFI.costo_unitario,
											IT.id_documento_update          = '$id_documento',
											IT.tipo_documento_update        = 'Traslado de inventario'
										WHERE IT.id_item = CFI.id_item
					 					AND IT.activo = 1
										AND IT.id_sucursal  = '$id_sucursal_traslado'
										AND IT.id_ubicacion = '$id_bodega_traslado' ";
			$sql2 = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS cantidad_traslado,costo_unitario,
															id_inventario AS id_item
														FROM
															inventario_traslados_unidades
														WHERE
															id_traslado = '$id_documento'
														AND
															activo = 1
														GROUP BY
															id_inventario) AS CFI
										SET
											IT.cantidad = IT.cantidad - CFI.cantidad_traslado,
											IT.costos=CFI.costo_unitario,
											IT.id_documento_update          = '$id_documento',
											IT.tipo_documento_update        = 'Traslado de inventario'
										WHERE IT.id_item = CFI.id_item
					 					AND IT.activo = 1
										AND IT.id_sucursal  = '$id_sucursal'
										AND IT.id_ubicacion = '$id_bodega' ";
		}
		else if($accion=="reversar"){
			$sql1 = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS cantidad_traslado,costo_unitario,
															id_inventario AS id_item
														FROM
															inventario_traslados_unidades
														WHERE
															id_traslado = '$id_documento'
														AND
															activo = 1
														GROUP BY
															id_inventario) AS CFI
					SET
						IT.cantidad = IT.cantidad - CFI.cantidad_traslado,
						IT.costos=CFI.costo_unitario,
						IT.id_documento_update          = '$id_documento',
						IT.tipo_documento_update        = 'Traslado de inventario',
						IT.consecutivo_documento_update = ''
					WHERE IT.id_item = CFI.id_item
 					AND IT.activo = 1
					AND IT.id_sucursal  = '$id_sucursal_traslado'
					AND IT.id_ubicacion = '$id_bodega_traslado' ";
			$sql2 = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS cantidad_traslado,costo_unitario,
															id_inventario AS id_item
														FROM
															inventario_traslados_unidades
														WHERE
															id_traslado = '$id_documento'
														AND
															activo = 1
														GROUP BY
															id_inventario) AS CFI
										SET
											IT.cantidad = IT.cantidad + CFI.cantidad_traslado,
											IT.costos=CFI.costo_unitario,
											IT.id_documento_update          = '$id_documento',
											IT.tipo_documento_update        = 'Traslado de inventario',
											IT.consecutivo_documento_update = ''
										WHERE IT.id_item = CFI.id_item
					 					AND IT.activo = 1
										AND IT.id_sucursal  = '$id_sucursal'
										AND IT.id_ubicacion = '$id_bodega' ";
		}
		// echo $sql;
		$query1=$mysql->query($sql1,$mysql->link);
		$query2=$mysql->query($sql2,$mysql->link);
		echo $sql1;
		echo "<br>";
		echo $sql1;
		if (!$query1 || !$query2) {
			rollback($id_documento,'Error al actualizar las unidades de inventario',$mysql);
		}
		// MOVER EL LOG DE INVENTARIO
		else{
			// ORIGEN
			logInventario($arrayDatos,$mysql);
			// DESTINO
			$arrayDatos["id_sucursal"] = "$id_sucursal_traslado AS id_sucursal";
			$arrayDatos["id_bodega"]   = "$id_bodega_traslado AS id_bodega";
			logInventario($arrayDatos,$mysql);
		}
	}

	function validarCantidadInventarioTraslado($id_documento,$accion,$mysql){
		$sql="SELECT id_sucursal,id_bodega,id_sucursal_traslado,id_bodega_traslado
				FROM inventario_traslados WHERE activo=1 AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		$id_sucursal          = $mysql->result($query,0,'id_sucursal');
		$id_bodega            = $mysql->result($query,0,'id_bodega');
		$id_sucursal_traslado = $mysql->result($query,0,'id_sucursal_traslado');
		$id_bodega_traslado   = $mysql->result($query,0,'id_bodega_traslado');
		if (
				($id_sucursal          =='' || $id_sucursal==0) ||
				($id_bodega            =='' || $id_bodega==0) ||
				($id_sucursal_traslado =='' || $id_sucursal_traslado==0) ||
				($id_bodega_traslado   =='' || $id_bodega_traslado==0 )
			) {
			echo "<script>
					alert('Debe seleccionar la sucursal y bodega de traslado');
					document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
				</script>";
			exit;
		}
		if ($accion=='trasladar'){
			$id_sucursal_validar = $id_sucursal;
			$id_bodega_validar   = $id_bodega;
		}
		else if ($accion=='reversar'){
			$id_sucursal_validar = $id_sucursal_traslado;
			$id_bodega_validar   = $id_bodega_traslado;
		}


		$sql="SELECT SUM(cantidad) AS cantidad_traslado,id_inventario
				FROM inventario_traslados_unidades
				WHERE id_traslado = '$id_documento' AND activo = 1 GROUP BY id_inventario";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_assoc($query)) {
			$id       = $row['id_inventario'];
			$cantidad = $row['cantidad_traslado'];
			$arrayInventario[$id] = $cantidad;
			$whereItems .= ($whereItems=='')? "id_item = $id " : " OR id_item = $id "  ;
		}

		$sql="SELECT id_item,cantidad,costos,nombre_equipo
				FROM inventario_totales
				WHERE activo=1 AND ($whereItems) AND id_sucursal=$id_sucursal_validar AND id_ubicacion=$id_bodega_validar ";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_assoc($query)) {
			if ($arrayInventario[$row['id_item']]>$row['cantidad'] && $accion !== "reversar") {
				echo "<script>
						alert('El item $row[nombre_equipo] supera la cantidad a trasladar');
						document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
					</script>";
				exit;
			}
		}
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$mysql){
		// VALIDAR EXISTENCIAS DEL INVENTARIO A TRASLADAR
		validarCantidadInventarioTraslado($idDocumento,'reversar',$mysql);
		// ACTUALIZAR EL ESTADO DEL DOCUMENTO
		$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			trasladarInventario($idDocumento,'reversar',"Editar");

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					       VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','TDI','Traslado de Inventario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog=$mysql->query($sqlLog,$mysql->link);

			echo "<script>
					Ext.get('contenedor_$opcGrillaContable').load({
						url     : 'traslados/grillaContable.php',
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable : '$opcGrillaContable',
							id_documento      : '$idDocumento'
						}
					});
					Ext.getCmp('btnNueva$opcGrillaContable').enable();
					document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
				</script>";
		}
		else{
			echo '<script>
					alert("Error!\nNo se actualizo el documento, intente de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($opcGrillaContable,$consecutivo,$cont,$idInsertArticulo,$idInventario,$cantArticulo,$id_documento,$tablaInventario,$idTablaPrincipal,$id_sucursal,$id_ubicacion,$mysql){
		// VALIDAR LA CANTIDAD DE ESE ARTICULO EN EL INVENTARIO
		$sql="SELECT cantidad,costos FROM inventario_totales WHERE activo=1 AND id_item=$idInventario AND id_sucursal=$id_sucursal AND id_ubicacion=$id_ubicacion ";
		$query=$mysql->query($sql,$mysql->link);
		$cantidadInv = $mysql->result($query,0,'cantidad');
		$costos      = $mysql->result($query,0,'costos');
		if ($cantArticulo>$cantidadInv) {
			echo "<script>
					alert('La cantidad ingresada es mayor a la cantidad de inventario, cantidad de inventario: $cantidadInv');
					document.getElementById('cantArticuloTraslados_$cont').focus();
					var body = document.getElementById('bodyDivArticulos".$opcGrillaContable."_".($cont+1)."');
					body.parentNode.removeChild(body);
					contArticulos$opcGrillaContable--;
				</script>";
			return;
		}

		$sql = "INSERT INTO $tablaInventario
						(
					  		$idTablaPrincipal,
							id_inventario,
							cantidad,
							costo_unitario
						)
					VALUES
						(
							'$id_documento',
							'$idInventario',
							'$cantArticulo',
							'$costos'
						)";
		$queryInsert = $mysql->query($sql,$mysql->link);

		$lastId = $mysql->insert_id();

		if($lastId > 0){
			echo'<script>
					document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value            = '.$lastId.'

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
					document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display        = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display    = "none";

					document.getElementById("descripcionArticulo'.$opcGrillaContable.'_'.$cont.'").style.display = "block";
					document.getElementById("deleteArticulo'.$opcGrillaContable.'_'.$cont.'").style.display      = "block";

				</script>'.cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);
		}
		else{
			$cont++;
			echo '<script>
					  	alert("Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema.");
							var body = document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'");
							body.parentNode.removeChild(body);
							contArticulos'.$opcGrillaContable.'--;
						</script>';

		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaArticulo($id_documento,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$id_sucursal,$id_ubicacion,$mysql){
		// VALIDAR LA CANTIDAD DE ESE ARTICULO EN EL INVENTARIO
		$sql="SELECT cantidad,costos FROM inventario_totales WHERE activo=1 AND id_item=$idInventario AND id_sucursal=$id_sucursal AND id_ubicacion=$id_ubicacion ";
		$query=$mysql->query($sql,$mysql->link);
		$cantidadInv = $mysql->result($query,0,'cantidad');
		$costos      = $mysql->result($query,0,'costos');

		if ($cantArticulo>$cantidadInv) {
			echo "<script>
					alert('La cantidad ingresada es mayor a la cantidad de inventario, cantidad de inventario: $cantidadInv');
					document.getElementById('cantArticuloTraslados_$cont').focus();
				</script>";
			return;
		}

		$sql   = "UPDATE $tablaInventario
								SET id_inventario 	  = '$idInventario',
									cantidad          = '$cantArticulo',
									costo_unitario    = '$costos'
								WHERE $idTablaPrincipal=$id_documento
									AND id=$idInsertArticulo";
		$query = $mysql->query($sql,$mysql->link);

		if ($query) {
			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					//llamamos la funcion para recalcular el costo de la factura
					//calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantArticulo.',"'.$descuentoArticulo.'",'.$costoArticulo.',"agregar","'.$tipoDesc.'","'.$iva.'",'.$cont.');
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION INDIVIDUAL POR ARTICULO =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//=========================== FUNCION PARA VERIFICAR LA CANTIDAD EXISTENTE DEL ARTICULO  ======================================================//
	function verificaCantidadArticulo($opcGrillaContable,$id,$id_sucursal,$filtro_bodega,$link){
		$sql           = "SELECT cantidad,inventariable,estado_venta FROM inventario_totales WHERE id_item='$id' AND id_sucursal='$id_sucursal' AND id_ubicacion='$filtro_bodega' AND id_empresa=".$_SESSION['EMPRESA']." LIMIT 0,1";
		$query         = mysql_query($sql,$link);
		$inventariable = mysql_result($query,0,'inventariable');
		$estado_venta  = mysql_result($query,0,'estado_venta');
		$cantidad      = mysql_result($query,0,'cantidad');

		$cantidad = ($inventariable=='false' && $estado_venta=='true')? 9999 : $cantidad;

		if (!$query) { echo 'false'; }
		else { echo $cantidad;  }
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql){
		//CONSULTAMOS EL ESTADO DEL DOCUMENTO
		$sql    = "SELECT estado,consecutivo FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id;";
		$query  = $mysql->query($sql,$mysql->link);
		$estado              = $mysql->result($query,0,'estado');
		$consecutivo         = $mysql->result($query,0,'consecutivo');

		//IDENTIFICAMOS EL DOCUMENTO QUE SE VA A CANCELAR
        if($consecutivo > 0){
			$sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa' ";
		}
		else{
			$sql="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_empresa='$id_empresa'";
		}

		if ($estado==1) {
			// VALIDAR EXISTENCIAS DEL INVENTARIO A TRASLADAR
			validarCantidadInventarioTraslado($id,'reversar',$mysql);
			// TRASLADAR LAS UNIDADES DE INVENTARIO
			trasladarInventario($id,'reversar',"Cancelar");
		}
		// echo "$sql";
		$query = $mysql->query($sql,$link);				//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		if (!$query) { echo '<script>alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
		else{
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			////INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					       VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','TDI','Traslado de Inventario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = $mysql->query($sqlLog,$link);

			echo'<script>
					nueva'.$opcGrillaContable.'();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$id_empresa,$tablaPrincipal,$mysql){
		$sql="SELECT consecutivo FROM $tablaPrincipal WHERE activo=1 AND id='$idDocumento' AND id_empresa='$id_empresa'";
		$query=$mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo');
		$tituloDoc = ($consecutivo>0)? "Traslado de inventario<br>N. $consecutivo" : "" ;

		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_empresa='$id_empresa' ";
		$query = $mysql->query($sql,$mysql->link);
		if ($query) {
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							   VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','TDI','Traslado de Inventario',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = $mysql->query($sqlLog,$mysql->link);
			echo"<script>
					Ext.get('contenedor_$opcGrillaContable').load({
						url     : 'traslados/grillaContable.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_documento      : '$idDocumento',
							opcGrillaContable : '$opcGrillaContable',
						}
					});
                    document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
                    document.getElementById('titleDocumento$opcGrillaContable').innerHTML = '$tituloDoc';
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
							url     : "requisicion/grillaContableBloqueada.php",
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

	// FUNCION PARA RETROCEDER EL PROCESO DEL DOCUMENTO
	function rollback($id_documento,$mensaje='',$mysql){
		// CAMBIAR EL ESTADO DEL DOCUMENTO AL NUMERO 0
		$sql="UPDATE inventario_traslados SET estado=0 WHERE activo=1 AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		echo '<script>
				alert("'.$mensaje.'");
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
		exit;
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT fecha_inicio,fecha_vencimiento FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		$fecha_inicio      = mysql_result($query,0,'fecha_inicio');
		$fecha_vencimiento = mysql_result($query,0,'fecha_vencimiento');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha_inicio)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha_inicio)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha_vencimiento)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha_vencimiento)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ((fecha_nota>='$fecha_inicio_buscar_1' AND fecha_nota<='$fecha_fin_buscar_1') OR (fecha_nota>='$fecha_inicio_buscar_2' AND fecha_nota<='$fecha_fin_buscar_2')  ) ";
		$query=mysql_query($sql,$link);
		$cont2 = mysql_result($query,0,'cont');

		if ($cont1>0 || $cont2>0) {
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
