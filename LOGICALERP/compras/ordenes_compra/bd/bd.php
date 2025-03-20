<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("functions_body_article.php");
	include("../../config_var_global.php");

	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	//VALIDAR EL ESTADO DEL DOCUMENTO ANTES DE REALIZAR CUALQUIER ACCION
	if(isset($idOrdenCompra)){
		// VALIDAR EL CIERRE ANUAL
		if($opc <> 'guardarFechaOrden'){
			verificaCierre($idOrdenCompra,'compras_ordenes',$id_empresa,$link);
		}

		if($opc <> 'restaurarOrdenCompra' && $opc <> 'modificarDocumentoGenerado' && $opc <> 'btnEliminarOrdenCompra'){
			verificaEstadoOrden($idOrdenCompra,$link);
		}
	}

	switch($opc){
		case 'buscarProveedor':
			buscarProveedor($codProveedor,$inputId,$id_empresa,$idOrdenCompra,$evt,$link);
			break;

		case 'cambiaProveedorOrdenCompra':
			cambiaProveedorOrdenCompra($idOrdenCompra,$link);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaDescripcionArticulo':
			ventanaDescripcionArticulo($cont,$idArticulo,$idOrdenCompra,$link);
			break;

		case 'guardarDescripcionArticuloOrdenCompra':
			guardarDescripcionArticuloOrdenCompra($cont,$id_impuesto,$idCentroCostos,$observacion,$idInventario,$idInsert,$idOrdenCompra,$id_empresa,$filtro_bodega,$link);
			break;

		case 'buscarArticuloOrdenCompra':
			buscarArticuloOrdenCompra($valorArticulo,$contArticulo,$id_empresa,$link);
			break;

		case 'guardarArticuloOrdenCompra':
			guardarArticuloOrdenCompra($tipoDescuento,$consecutivo,$idOrdenCompra,$cont,$idInventario,$cantArticulo,$descuentoArticulo,$costoArticulo,$link);
			break;

		case 'updateArticuloOrdenCompra':
			updateArticuloOrdenCompra($tipoDescuento,$idInsertArticulo,$consecutivo,$idOrdenCompra,$cont,$idInventario,$cantArticulo,$descuentoArticulo,$costoArticulo,$iva,$link);
			break;

		case 'deleteArticuloOrdenCompra':
			deleteArticuloOrdenCompra($cont,$idOrdenCompra,$idArticulo,$link);
			break;

		case 'btnTerminarOrdenCompra':
			btnTerminarOrdenCompra($idOrdenCompra,$link,$mysql);
			break;

		case 'noUpdateArticuloOrdenCompra':
			noUpdateArticuloOrdenCompra($idOrdenCompra,$idArticulo,$cont,$link);
			break;

		case 'guardarObservacionOrdenCompra':
			guardarObservacionOrdenCompra($observacion,$idOrdenCompra,$link);
			break;

		case 'btnEliminarOrdenCompra':
			btnEliminarOrdenCompra($idOrdenCompra,$id_sucursal,$filtro_bodega,$id_empresa,$link);
			break;

		case 'cargarIvaArticuloOrdenCompra':
			cargarIvaArticuloOrdenCompra($id_inventario,$id_empresa,$cont,$link);
			break;

		case 'restaurarOrdenCompra':
			restaurarOrdenCompra($idOrdenCompra,$id_empresa,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idOrdenCompra,$id_empresa,$id_sucursal,$id_bodega,$link);
			break;

		case 'guardarFechaOrden':
			guardarFechaOrden($idInputDate,$idOrdenCompra,$valInputDate,$link);
			break;

		case 'guardarAreaSolicitante':
  	 	guardarAreaSolicitante($id,$id_area_solicitante,$codigo_area_solicitante,$departamento_area_solicitante,$link);
  		break;

		case 'validarOrdenCompra':
			validarOrdenCompra($consecutivo,$id_empresa,$link);
			break;

		case 'btnValidarOrdenCompra':
			btnValidarOrdenCompra($consecutivo,$id_empresa,$link);
			break;

		case 'deleteDocumentoOrden':
		    deleteDocumentoOrden($id_host,$idDocumento,$nombre,$ext,$link);
		    break;

		case "downloadFile":
			downloadFile($nameFile,$ext,$id,$id_empresa,$id_host);
			break;

		case "consultaSizeDocumento":
			consultaSizeDocumento($nameFile,$ext,$id,$id_host);
			break;

		case "ventanaViewDocumento":
			ventanaViewDocumento($nameFile,$ext,$id,$id_host);
			break;

		case "UpdateTipoOrden":
		    updateTipoOrden($id_orden,$id_tipo,$link);
		    break;

		case 'agregarDocumento':
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_documento,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'eliminaDocReferencia':
			eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'controladorAutorizaciones':
			controladorAutorizaciones($id_orden_compra,$id_sucursal,$id_empresa,$mysql);
			break;

		case 'ventanaAutorizaDocumento':
			ventanaAutorizaDocumento($idDocumento,$id_empresa,$mysql);
			break;
		case 'autorizarOrdenCompra':
			autorizarOrdenCompra($id_orden_compra,$tipo_autorizacion,$id_empresa,$mysql);
			break;
		case 'UpdateFormaPago':
			UpdateFormaPago($id,$idFormaPago,$link);
			break;
		case 'autorizarOrdenCompraArea':
			autorizarOrdenCompraArea($id_documento,$opcGrillaContable,$id_empresa,$tipo_autorizacion,$id_area,$orden,$mysql);
			break;
	}

	//funcion para mostrar el campo nit en la factura/orden de compra
	function buscarProveedor($codProveedor,$inputId,$id_empresa,$idOrdenCompra,$evt,$link){

		$campo = '';
		$focus = '';
		if($inputId == 'codProveedor'){
			$campo     = 'codigo';
			$textAlert = 'Codigo';
			$focus     = 'setTimeout(function(){ document.getElementById("codProveedor").focus(); },100);';
		}
		else if($inputId == 'nitProveedor'){
			$campo     = 'numero_identificacion';
			$textAlert = 'Nit';
			$focus     = 'setTimeout(function(){ document.getElementById("nitProveedor").focus(); },100);';
		}
		else if($inputId == 'idProveedor'){
			$campo     = 'id';
			$textAlert = 'Id';
		}

		$SQL   = "SELECT id,numero_identificacion,nombre,codigo AS cod_proveedor FROM terceros WHERE $campo='$codProveedor' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' AND tipo_proveedor='Si' LIMIT 0,1";
		$query = mysql_query($SQL,$link);

		$id     = mysql_result($query,0,'id');
		$nit    = mysql_result($query,0,'numero_identificacion');
		$codigo = mysql_result($query,0,'cod_proveedor');
		$nombre = mysql_result($query,0,'nombre');

		if($nombre!=''){
			$sqlUpdateComprasOrdenes = "UPDATE compras_ordenes
										SET id_empresa = '$id_empresa',
											id_proveedor = '$id'
										WHERE id='$idOrdenCompra'";
			$queryUpdateComprasOrdenes = mysql_query($sqlUpdateComprasOrdenes,$link);

			echo'<script>
					id_proveedor_orden_compra                        = "'.$id.'";
					document.getElementById("nitProveedor").value    = "'.$nit.'";
					document.getElementById("codProveedor").value    = "'.$codigo.'";
					document.getElementById("nombreProveedor").value = "'.$nombre.'";

					codigoProveedor = "'.$codigo.'";
					nitProveedor    = "'.$nit.'";
					nombreProveedor = "'.$nombre.'";
				</script>'.(($evt=='insert')? cargaHeadInsertUnidades('return',1) : '' );
		}
		else{
			echo'<script>
					alert("'.$textAlert.' de proveedor no establecido!");
					'.$focus.'
				</script>';
		}
	}

	function cargaHeadInsertUnidades($formaConsulta,$cont){
		$body = '<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS ORDEN DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="headOrdenesCompra">
							<div class="label" style="width:40px !important; border-left:none; padding-left:2px;"></div>
							<div class="label" title="Codigo/EAN">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label" title="Unidad">Unidad</div>
							<div class="label" title="Cantidad">Cantidad</div>
							<div class="label" title="Descuento">Descuento</div>
							<div class="label" title="Costo Unitario">Costo Unitario</div>
							<div class="label" title="Costo Total">Costo Total</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos" onscroll="resizeHeadMyGrilla(this,\'headOrdenesCompra\');">
						<div class="bodyDivArticulos" id="bodyDivArticulos_'.$cont.'">
							'.cargaDivsInsertUnidades('return',$cont).'
						</div>
					</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_ordenes_compras">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacionOrdenCompra"><b>OBSERVACIONES</b></div>
						<textarea id="observacionOrdenCompra" onKeydown="inputObservacionOrdenCompra(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalOrdenCompra"></div>
						</div>
						<div class="renglon" style="overflow:visible; height:auto;">
							<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIvaOrdenCompra">Iva</div>
							<div class="labelSimbolo" id="simboloIvaOrdenCompra">$</div>
							<div class="labelTotal" id="ivaOrdenCompra" >0</div>
						</div>
						<div class="renglon renglonTotal">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalOrdenCompra"></div>
						</div>
					</div>
				</div>
				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotalOrdenCompra").innerHTML = parseFloat(subtotalOrdenCompra).toFixed(2);
					document.getElementById("ivaOrdenCompra").innerHTML      = parseFloat(ivaOrdenCompra).toFixed(2);
					document.getElementById("totalOrdenCompra").innerHTML    = parseFloat(totalOrdenCompra).toFixed(2);

					document.getElementById("eanArticulo_'.$cont.'").focus();
				</script>';

		if($formaConsulta=='return'){ return $body; }
		else{ echo $body; }
	}

	function cargaDivsInsertUnidades($formaConsulta,$cont){
		$body ='<div class="campo" style="width:40px !important; border-left:none; padding-left:2px; overflow:hidden;">
					<div style="float:left; margin-top:3px;">'.$cont.'</div>
					<div style="float:right; width:18px; overflow:hidden;" id="renderArticuloOrdenCompra_'.$cont.'"></div>
				</div>
				<div class="campo">
					<input type="text" id="eanArticulo_'.$cont.'" onKeyup="buscarArticuloOrdenCompra(event,this);"/>
				</div>
				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo_'.$cont.'" style="text-align:left; readonly"/></div>
				<div onclick="ventanaBuscarArticuloOrdenCompra('.$cont.');" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidades_'.$cont.'"  style="text-align:left;" readonly /></div>
				<div class="campo"><input type="text" id="cantArticulo_'.$cont.'"/></div>

				<div class="campo campoDescuento">
					<div onclick="tipoDescuentoArticuloOrdenCompra('.$cont.',this)" id="divTipoDescuentoArticuloOrdenCompra_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticuloOrdenCompra_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo_'.$cont.'" value="0.00"/>
				</div>

				<div class="campo" style="border-right: 1px solid #d4d4d4;"><input type="text" id="costoArticulo_'.$cont.'" onKeyup="guardarAutoOrdenCompra(event,this,'.$cont.');" value="0"/></div>

				<div class="campo" style="border-right: 1px solid #d4d4d4;"><input type="text" id="costoTotalArticuloOrdenCompra_'.$cont.'" readonly value="0"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticuloOrdenCompra('.$cont.');" id="divGuardarNewArticuloOrdenCompra_'.$cont.'" title="Guardar" class="icono">
						<img src="img/save_true.png" id="imgSaveOrdenCompra_'.$cont.'"/>
					</div>
					<div onclick="noUpdateArticuloOrdenCompra('.$cont.')" id="divImageNoUpdateArticuloOrdenCompra_'.$cont.'" title="Deshacer Cambios" style="display:none" class="icono">
						<img src="img/deshacer.png" id="imgNoUpdateArticuloOrdenCompra_'.$cont.'">
					</div>
					<div onclick="ventanaDescripcionArticuloOrdenCompra('.$cont.');" id="descripcionArticuloOrdenCompra_'.$cont.'" title="Observaciones" style="display:none;" class="icono">
						<img src="img/edit.png"/>
					</div>
					<div onclick="deleteArticuloOrdenCompra('.$cont.');" id="deleteArticuloOrdenCompra_'.$cont.'"  title="Eliminar" style="display:none;" class="icono">
						<img src="img/delete.png" />
					</div>
				</div>
				<input type="hidden" id="idArticulo_'.$cont.'" value="0"/>
				<input type="hidden" class="classInputInsertArticulo" id="idInsertArticulo_'.$cont.'" value="0" />
				<input type="hidden" id="ivaArticuloOrdenCompra_'.$cont.'" value="0" />
				<script>
					document.getElementById("cantArticulo_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloOrdenCompra(event,this); };
					document.getElementById("descuentoArticulo_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloOrdenCompra(event,this); };
				</script>';

		if($formaConsulta=='return'){ return $body; }
		else{ echo $body; }
	}

	// DESCRIPCION POR ARTICULO EN ORDEN DE COMPRA
	function ventanaDescripcionArticulo($cont,$idArticulo,$idOrdenCompra,$link){
		global $id_empresa;

		$selectObservacion = "SELECT observaciones,id_centro_costos,id_impuesto FROM compras_ordenes_inventario WHERE id='$idArticulo' AND id_orden_compra='$idOrdenCompra'";
		$observacion       = mysql_result(mysql_query($selectObservacion,$link),0,'observaciones');
		$idCentroCostos    = mysql_result(mysql_query($selectObservacion,$link),0,'id_centro_costos');
		$idImpuesto        = mysql_result(mysql_query($selectObservacion,$link),0,'id_impuesto');

		$sqlCentroCostos   = "SELECT id, codigo, nombre FROM centro_costos WHERE id='$idCentroCostos' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryCentroCostos = mysql_query($sqlCentroCostos,$link);
		$codigoCCos = mysql_result($queryCentroCostos, 0, 'codigo');
		$nombreCCos = mysql_result($queryCentroCostos, 0, 'nombre');

		$optionImpuesto = '<option value="0">SIN IMPUESTO</option>';
		$sqlImpuesto    = "SELECT id,impuesto FROM impuestos
							WHERE id_empresa='$id_empresa'
								AND activo=1
								AND cuenta_compra>0
								AND cuenta_compra_niif>0";
		$queryImpuesto  = mysql_query($sqlImpuesto,$link);
		while($rowImpuesto = mysql_fetch_assoc($queryImpuesto)){
			$selected = ($rowImpuesto['id'] == $idImpuesto)? 'selected': '';
			$optionImpuesto .= '<option value="'.$rowImpuesto['id'].'" '.$selected.'>'.$rowImpuesto['impuesto'].'</option>';
		}

		echo'<div style="margin: 0 10px">
				<div id="renderizaGuardarObservacionOrden_'.$cont.'" style="width:20px; height:20px; overflow:hidden;"></div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:5px;">
					<div style="float:left; width:24%;">Impuesto</div>
					<div style="width:75%; float:left; height:23px;">
						<select id="id_impuestoItem_oc" style="width:99%;">'.$optionImpuesto.'</select>
					</div>
				</div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:5px; ">
					<div style="float:left; width:24%;">Centro de Costo</div>
					<div style="width:75%; float:left; height:23px;">
						<input type="text" id="id_ccos_oc" value="'.$idCentroCostos.'" style="display:none;"/>
						<input type="text" id="codigo_ccos_oc" onclick="ventana_centros_costos_oc()" value="'.$codigoCCos.'" style="width:29%; float:left; margin-right:1%;" class="myfield" readonly/>
						<input type="text" id="nombre_ccos_oc" onclick="ventana_centros_costos_oc()" value="'.$nombreCCos.'" style="width:70%; float:left;" class="myfield" readonly/>
					</div>
				</div>
				<div style="margin: 10px">
					<div id="renderizaGuardarObservacion_'.$cont.'"></div>
					<textarea id="observacionArticuloOrdenCompra_'.$cont.'" style="width:300px; height:130px;">'.$observacion.'</textarea>
				</div>
			</div>';
	}

	function guardarDescripcionArticuloOrdenCompra($cont,$id_impuesto,$idCentroCostos,$observacion,$idInventario,$idInsert,$idOrdenCompra,$id_empresa,$filtro_bodega,$link){

		$sqlOrdenBd    = "SELECT id_impuesto,cantidad,tipo_descuento,descuento,costo_unitario
							FROM compras_ordenes_inventario
							WHERE id_inventario='$idInventario' AND id_orden_compra='$idOrdenCompra' AND id='$idInsert' LIMIT 0,1";
		$queryOrdenBd  = mysql_query($sqlOrdenBd,$link);

		$id_impuestoBd  = mysql_result($queryOrdenBd, 0, 'id_impuesto');
		$cantidad       = mysql_result($queryOrdenBd, 0, 'cantidad');
		$tipo_descuento = mysql_result($queryOrdenBd, 0, 'tipo_descuento');
		$descuento      = mysql_result($queryOrdenBd, 0, 'descuento');
		$costo_unitario = mysql_result($queryOrdenBd, 0, 'costo_unitario');

		$sqlImpuesto   = "SELECT impuesto,valor FROM impuestos WHERE id='$id_impuesto'";
		$queryImpuesto = mysql_query($sqlImpuesto,$link);

		$impuesto       = mysql_result($queryImpuesto,0,'impuesto');
		$valor_impuesto = mysql_result($queryImpuesto,0,'valor');

		$sqlUpdateObservacion   = "UPDATE compras_ordenes_inventario
									SET observaciones='$observacion',
									    id_impuesto ='$id_impuesto',
									    impuesto = '$impuesto',
									    valor_impuesto = '$valor_impuesto',
										id_centro_costos = '$idCentroCostos'
									WHERE id_inventario='$idInventario' AND id_orden_compra='$idOrdenCompra' AND id='$idInsert'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){

			echo '<script>Win_Ventana_descripcion_Articulo_orden_compra.close(id);</script>';

			if($id_impuestoBd != $id_impuesto){
				echo'<script>
						calcularValoresOrdenCompra('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$id_impuestoBd.'",'.$cont.');

						if(arrayIvaOrdenCompra['.$id_impuesto.'] == undefined){
							arrayIvaOrdenCompra['.$id_impuesto.'] = {
								nombre : "'.$impuesto.'",
								saldo  : 0,
								valor  : "'.$valor_impuesto.'"
							}
						}

						calcularValoresOrdenCompra('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipo_descuento.'","'.$id_impuesto.'",'.$cont.');
						document.getElementById("ivaArticuloOrdenCompra_'.$cont.'").value="'.$id_impuesto.'";
					</script>';
			}
		}
		else{ echo '<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }

	}

	function buscarArticuloOrdenCompra($valorArticulo,$contArticulo,$id_empresa,$link){

		$sqlArticulo ="SELECT I.id,
							I.codigo,
							I.code_bar,
							I.costos,
							I.nombre_equipo,
							I.id_impuesto,
							I.estado_compra,
							I.unidad_medida,
							I.cantidad_unidades
						FROM items AS I
						WHERE I.activo=1
							AND I.id_empresa=$id_empresa
							AND (I.code_bar = '$valorArticulo' OR I.codigo = '$valorArticulo')
						LIMIT 0,1";

		$query = mysql_query($sqlArticulo,$link);

		$id             = mysql_result($query,0,'id');
		$codigo         = mysql_result($query,0,'codigo');
		$costos         = mysql_result($query,0,'costos');
		$codigoBarras   = mysql_result($query,0,'code_bar');
		$nombre_unidad  = mysql_result($query,0,'unidad_medida');
		$numero_unidad  = mysql_result($query,0,'cantidad_unidades');
		$nombreArticulo = mysql_result($query,0,'nombre_equipo');
		$id_impuesto    = mysql_result($query,0,'id_impuesto');
		$estadoCompra   = mysql_result($query,0,'estado_compra');

		//consultamos el valor del impuesto para asignarlo al campo oculto,
		$sqlImpuesto   = "SELECT impuesto,valor FROM impuestos WHERE id='$id_impuesto'";
		$queryImpuesto = mysql_query($sqlImpuesto,$link);
		$valorImpuesto = mysql_result($queryImpuesto,0,'valor');
		$impuesto      = mysql_result($queryImpuesto,0,'impuesto');

		if ($impuesto!="" && $valorImpuesto!="") {
			$script='if (typeof(arrayIvaOrdenCompra['.$id_impuesto.'])=="undefined") {
							arrayIvaOrdenCompra['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valorImpuesto.'"};
						 }';
		}

		//RESPONSE ERROR
		if(!$query || !$queryImpuesto){
			echo'<script>
					document.getElementById("eanArticulo_'.$contArticulo.'").blur()
					alert("Aviso,\nHa ocurrido un error en la consulta, si el problema persiste comuniquese con el administrador del sistema.");
					setTimeout(function(){ document.getElementById("eanArticulo_'.$contArticulo.'").focus(); },100);
				</script>';
			exit;
		}

		//RESPONSE RESULTADO CONSULTA
		if($id > 0 && $estadoCompra == "true"){
			echo'<script>
					document.getElementById("unidades_'.$contArticulo.'").value                ="'.$nombre_unidad.' x '.$numero_unidad.'";
					document.getElementById("idArticulo_'.$contArticulo.'").value              ="'.$id.'";
					document.getElementById("eanArticulo_'.$contArticulo.'").value             ="'.$codigo.'";
					document.getElementById("costoArticulo_'.$contArticulo.'").value           ="'.$costos.'";
					document.getElementById("nombreArticulo_'.$contArticulo.'").value          ="'.$nombreArticulo.'";
					document.getElementById("ivaArticuloOrdenCompra_'.$contArticulo.'").value  ="'.$id_impuesto.'";

					'.$script.'

					setTimeout(function(){ document.getElementById("cantArticulo_'.$contArticulo.'").focus(); },50);
				</script>';
		}
		else if($estadoCompra == 'false'){
			echo'<script>
					document.getElementById("eanArticulo_'.$contArticulo.'").blur()
					alert("Codigo '.$valorArticulo.' No esta disponible en el modulo compras");
					setTimeout(function(){ document.getElementById("eanArticulo_'.$contArticulo.'").focus(); },100);
				</script>';
		}
		else{
			echo'<script>
					document.getElementById("eanArticulo_'.$contArticulo.'").blur()
					alert("Codigo '.$valorArticulo.' No esta asociado en el inventario");
					setTimeout(function(){ document.getElementById("eanArticulo_'.$contArticulo.'").focus(); },100);
				</script>';
		}
	}

	function guardarArticuloOrdenCompra($tipoDescuento,$consecutivo,$idOrdenCompra,$cont,$idInventario,$cantArticulo,$descuentoArticulo,$costoArticulo,$link){
		// VALIDAR QUE TENGA EL PERMISO PARA GENERAR ORDENES SIN REQUISICIONES
		if (user_permisos(224,'false') == 'false') {
			echo '<script>
					alert("Aviso\nNo tiene permisos para hacer ordenes sin Requisicion!");
					document.getElementById("bodyDivArticulos_"+contArticulosOrdenCompra).parentNode.removeChild(document.getElementById("bodyDivArticulos_"+contArticulosOrdenCompra));
				</script>';

			exit;
		}
		// $permiso_orden_compra = (user_permisos(224,'false') == 'true')? 'false' : 'true';

		$sqlInsert = "INSERT INTO compras_ordenes_inventario
									  ( id_orden_compra,
										id_inventario,
										cantidad,
										saldo_cantidad,
										tipo_descuento,
										descuento,
										costo_unitario)
								VALUES( '$idOrdenCompra',
										'$idInventario',
										'$cantArticulo',
										'$cantArticulo',
										'$tipoDescuento',
										'$descuentoArticulo',
										'$costoArticulo')";
		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);
		if($lastId > 0){
			echo'<script>
					Ext.getCmp("Btn_nueva_orden_compra").enable();

					document.getElementById("idInsertArticulo_'.$cont.'").value='.$lastId.'
					document.getElementById("eanArticulo_'.$consecutivo.'").focus();

					document.getElementById("imgSaveOrdenCompra_'.$cont.'").setAttribute("src","img/reload.png");
					document.getElementById("divGuardarNewArticuloOrdenCompra_'.$cont.'").style.display    = "none";
					document.getElementById("divImageNoUpdateArticuloOrdenCompra_'.$cont.'").style.display = "none";

					document.getElementById("descripcionArticuloOrdenCompra_'.$cont.'").style.display = "block";
					document.getElementById("deleteArticuloOrdenCompra_'.$cont.'").style.display      = "block";
				</script>'.cargaDivsInsertUnidades('echo',$consecutivo);
		}
		else{ echo "Error, no se ha almacenado el articulo en la presente orden de compra, si el problema persiste favor comuniquese con la administracion del sistema"; }
	}

	function updateArticuloOrdenCompra($tipoDescuento,$idInsertArticulo,$consecutivo,$idOrdenCompra,$cont,$idInventario,$cantArticulo,$descuentoArticulo,$costoArticulo,$ivaArticulo,$link){
		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo

		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura

		$sqlArticuloAnterior="SELECT cantidad,tipo_descuento,descuento,costo_unitario, id_impuesto AS valor_impuesto FROM compras_ordenes_inventario WHERE id='$idInsertArticulo'";
		$queryArticuloAnterior=mysql_query($sqlArticuloAnterior,$link);

		$cantidad 			= mysql_result($queryArticuloAnterior,0,'cantidad');
		$tipo_descuento 	= mysql_result($queryArticuloAnterior,0,'tipo_descuento');
		$descuento 			= mysql_result($queryArticuloAnterior,0,'descuento');
		$costo_unitario 	= mysql_result($queryArticuloAnterior,0,'costo_unitario');
		$valor_impuesto 	= mysql_result($queryArticuloAnterior,0,'valor_impuesto');

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		echo'<script> calcularValoresOrdenCompra('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'","'.$cont.'"); </script>';

		$sqlUpdate= "UPDATE compras_ordenes_inventario
					SET id_inventario='$idInventario',
						cantidad        ='$cantArticulo',
						saldo_cantidad  ='$cantArticulo',
						tipo_descuento  ='$tipoDescuento',
						descuento       ='$descuentoArticulo',
						costo_unitario  ='$costoArticulo'
					WHERE id='$idInsertArticulo'";
		$queryUpdate= mysql_query($sqlUpdate,$link);

		//despues de actualizarlo, recalculamos nuevamente los valores de la factura, pero para agregar los costos del articulo

		if($queryUpdate){

			echo'<script>
					document.getElementById("divImageNoUpdateArticuloOrdenCompra_'.$cont.'").style.display="none";
					document.getElementById("divGuardarNewArticuloOrdenCompra_'.$cont.'").style.display = "none";

					calcularValoresOrdenCompra('.$cantArticulo.',"'.$descuentoArticulo.'",'.$costoArticulo.',"agregar","'.$tipoDescuento.'","'.$ivaArticulo.'","'.$cont.'");
				</script>';
		}
		else{ echo '<script>alert("No se pudo actualizar el campo, Por favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema")</script>'; }
	}

	function deleteArticuloOrdenCompra($cont,$idOrdenCompra,$idArticulo,$link){
		$sqlDelete   = "DELETE FROM compras_ordenes_inventario WHERE id_orden_compra='$idOrdenCompra' AND id='$idArticulo'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){
			echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>';
		}
		else{ echo '<script>(document.getElementById("bodyDivArticulos_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos_'.$cont.'"));</script>'; }
	}

	function btnTerminarOrdenCompra($idOrdenCompra,$link,$mysql){
		$id_sucursal = $_SESSION['SUCURSAL'];
		$id_empresa  = $_SESSION['EMPRESA'];

		// CONSULTAR SI TIENE AUTORIZACION POR PRECIO
		$sql = "SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo = 1 AND id_empresa = $id_empresa";
		$query = mysql_query($sql,$link);
		$aut_precio = mysql_result($query,0,'aut_precio');

		// CONSULTAR SI TIENE AUTORIZACION POR AREA
		$sql = "SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo = 1 AND id_empresa = $id_empresa";
		$query = mysql_query($sql,$link);
		$aut_area = mysql_result($query,0,'aut_area');

		if($aut_precio > 0 && $aut_area > 0){
			echo '<script>
							alert("Error en la configuracion de autorizaciones, solo puede estar configurado una opcion, dirijase al panel de control y corrija la configuracion");
							if(document.getElementById("modal")){
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							}
						</script>';
			exit;
		}
		// SI NO TIENE NINGUN TIPO DE AUTORTIZACION CONFIGURADA, ENTONCES DEJAR LA ORDEN COMO AUTORIZADA
		else if($aut_precio == 0 && $aut_area == 0){
			$campoUpdate = ",autorizado = 'true' ";
		}

		actualizaInventarioDocumentoCruce('disminuir',$idOrdenCompra,$link);

		$sqlUpdateComprasOrdenes   = "UPDATE compras_ordenes SET estado = 1 $campoUpdate WHERE id = '$idOrdenCompra'";
		$queryUpdateComprasOrdenes = mysql_query($sqlUpdateComprasOrdenes,$link);

		$sql = "SELECT
							consecutivo,
							sucursal,
							bodega,
							fecha_inicio,
							consecutivo,
							nit,
							proveedor,
							observacion,
							documento_usuario,
							usuario,
							id_area_solicitante
						FROM
							compras_ordenes
						WHERE
							id = '$idOrdenCompra'";
		$query = mysql_query($sql,$link);
		$consecutivo         = mysql_result($query,0,'consecutivo');
		$sucursal            = mysql_result($query,0,'sucursal');
		$bodega              = mysql_result($query,0,'bodega');
		$fecha_inicio        = mysql_result($query,0,'fecha_inicio');
		$consecutivo         = mysql_result($query,0,'consecutivo');
		$nit                 = mysql_result($query,0,'nit');
		$proveedor           = mysql_result($query,0,'proveedor');
		$observacion         = mysql_result($query,0,'observacion');
		$documento_usuario   = mysql_result($query,0,'documento_usuario');
		$usuario             = mysql_result($query,0,'usuario');
		$id_area_solicitante = mysql_result($query,0,'id_area_solicitante');

		if($queryUpdateComprasOrdenes){
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog =  "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
									VALUES
									($idOrdenCompra,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Orden de Compra',".$id_sucursal.",".$id_empresa.",'".$_SERVER['REMOTE_ADDR']."','OC')";
			$queryLog = mysql_query($sqlLog,$link);

			//PASAMOS LAS REQUISICIONES QUE ESTAN CRUZADAS A ESTE DOCUMENTO A ESTADO 2 O DE BLOQUEO
			//PARA QUE NO SE PUEDAN EDITAR HASTA QUE TODAS LOS DOCUMENTOS DONDE ESTAN CARGADAS SEAN CANCELADOS O EDITADOS

			$sqlDocReferencia  = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM compras_ordenes_inventario
                            WHERE id_consecutivo_referencia>0 AND id_orden_compra='$idOrdenCompra' AND activo=1
                            ORDER BY id ASC";
  		$queryDocReferencia = mysql_query($sqlDocReferencia,$link);

  		while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){
				// CREAR ARRAY CON LAS REQUISICIONES
				$id_requisicion = $rowDocReferencia['id_referencia'];
  			$consecutivo_requisicion = $rowDocReferencia['consecutivo_referencia'];

  			/*	PROCESO PARA BLOQUEAR REQUISICIONES CRUZADAS AL DOCUMENTO

    			SI NO SE HABIA CRUZADO ANTES LA REQUISICION GUARDAMOS EN LA TABLA DE DOCUMENTOS
    			CRUZADOS LAS REQUISICIONES QUE ESTAN CARGADAS EN EL DOCUMENTO
    		*/

  			//PRIMERO VERIFICAMOS QUE SE HAYA CARGADO ANTERIORMENTE A LA ORDEN DE COMPRA

  			$sqlCheck = "SELECT id FROM compras_requisicion_doc_cruce WHERE id_requisicion = '$rowDocReferencia[id_referencia]' AND  id_documento_cruce = '$idOrdenCompra' AND tipo_documento_cruce = 'OC'";

				$queryCheck = mysql_query($sqlCheck,$link);
				$rows       = mysql_num_rows($queryCheck);

				if($rows < 1){
					//NO ESTABA CRUZADA ANTERIORMENTE LA REQUISICION
					$sql = "INSERT INTO compras_requisicion_doc_cruce (id_requisicion,id_documento_cruce,consecutivo_cruce,tipo_documento_cruce)
								  VALUES ('$rowDocReferencia[id_referencia]',$idOrdenCompra,'$consecutivo','OC')";

				}
				else{
					$id = mysql_result($queryCheck,0,'id');
					//SI YA HABIA ESTADO CRUZADA A ESTE DOCUMENTO
					$sql = "UPDATE compras_requisicion_doc_cruce SET activo = 1 WHERE id='$id'";

				}

				$query = mysql_query($sql,$link);

				if(!$query){
					echo '<script>
									alert("Ha ocurrido un error en el cruce de requisiciones,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema");
									if(document.getElementById("modal")){
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
									}
								</script>';
				}
  		}

  		//============================================================================//
  		// ENVIAR UN EMAIL A LAS PERSONAS ENCARGADAS DE AUTORIZAR LA ORDEN DE COMPRA  //
  		//============================================================================//
		$mensajeEmail = "Nueva Orden de Compra: a la espera de su Autorizacion";
		$subjectEmail ="Notificacion: ".$mensajeEmail;

		if($aut_precio > 0){
			// CONSULTAR LOS TOPES DE VERIFICACION
			$sql   = "SELECT id,rango_inicial,rango_final FROM rango_autorizaciones_ordenes_compra WHERE activo = 1 AND id_empresa = $id_empresa ORDER BY rango_inicial ASC LIMIT 0,1";
			$query = mysql_query($sql,$link);
			while ($row = mysql_fetch_array($query)) {
				if ($costo_total>=$row["rango_inicial"] && $costo_total<=$row["rango_final"] || ($row["rango_inicial"]==0 && $row["rango_final"]==0) ) {
					$whereIdRango= 'id_rango='.$row['id'];
					break;
				}
			}
			// SI ES AUTORIZACION POR RANGO DE PRECIOS Y TIENE REALIZADA ALGUNA CONFIGURACION
			if($whereIdRango <> ''){
				// CONSULTAR EL ROL
				$sql   = "SELECT id_rol FROM costo_autorizadores_ordenes_compra WHERE activo = 1 AND id_empresa = $id_empresa AND $whereIdRango ORDER BY orden ASC LIMIT 0,1";
    			$query = mysql_query($sql,$link);
    			$id_rol = mysql_result($query,0,'id_rol');
    			// SI TIENE UN ROL CONFIGURADO
    			if($id_rol <> ''){    					// CONSULTAR EL PRIMER EMPLEADO DE ESE ROL
    				$sql = "SELECT email_empresa, id FROM empleados WHERE activo=1 AND id_empresa = $id_empresa AND id_rol = $id_rol ORDER BY id ASC LIMIT 0,1";
    				$query = mysql_query($sql,$link);
    				$email_empleado = mysql_result($query,0,'email_empresa');
    				$id_empleado = mysql_result($query,0,'id');
    				// SI EXISTE EL EMPLEADO CON ESE ROL
    				if($email_empleado <> ''){
						enviaEmailAutorizacion($idOrdenCompra,$id_empleado,$id_empresa,$subjectEmail,$mensajeEmail,$mysql);
    				}
    			}
			}
		
		}else if($aut_area > 0){
			$sql = "SELECT id_empleado FROM costo_autorizadores_ordenes_compra_area WHERE activo = 1 AND id_empresa = $id_empresa AND id_area = $id_area_solicitante AND orden = 1";
			$query = mysql_query($sql,$link);
			while($row=mysql_fetch_array($query)){
				$id_empleados[] = $row['id_empleado'];
			}
			enviaEmailAutorizacion($idOrdenCompra,$id_empleados,$id_empresa,$subjectEmail,$mensajeEmail,$mysql);
		}

			echo '<script>
							Ext.getCmp("Btn_nueva_orden_compra").enable();
							Ext.getCmp("Btn_upload_orden_compra").enable();
							document.getElementById("titleDocuementoOrdenCompra").innerHTML="Orden de compra<br>N. '.$consecutivo.'";

							Ext.get("contenedor_ordenes_compra").load({
								url     : "ordenes_compra/ordenes_compra_bloqueada.php",
								scripts : true,
								nocache : true,
								params  :	{
														filtro_bodega   : document.getElementById("filtro_ubicacion_ordenes_compra").value,
														id_orden_compra : "'.$idOrdenCompra.'"
													}
							});

							if(document.getElementById("modal")){
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							}
						</script>';
		}
		else{
			echo '<script>
							alert("No se guardo la orden de compra,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema");
							if(document.getElementById("modal")){
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							}
						</script>';
		}
	}

	function noUpdateArticuloOrdenCompra($idOrdenCompra,$idArticulo,$cont,$link){
		$sql = "SELECT COI.id_inventario AS id_inventario,
						COI.codigo AS codigo,
						COI.costo_unitario AS costo,
						COI.nombre AS nombre_articulo,
						COI.cantidad AS catidad,
						COI.tipo_descuento AS tipo_descuento,
						COI.descuento AS descuento,
						IU.unidades AS unidades_empaque,
						IU.nombre AS nombre_empaque
				FROM items AS I,
					inventario_unidades AS IU,
					compras_ordenes_inventario AS COI
				WHERE COI.id = '$idArticulo'
				AND COI.id_orden_compra = '$idOrdenCompra'
				AND COI.id_inventario = I.id
				AND I.id_unidad_medida = IU.id
				LIMIT 0,1";
		$query = mysql_query($sql,$link);

		$id                   = mysql_result($query,0,'id_inventario');
		$codigo               = mysql_result($query,0,'codigo');
		$costos               = mysql_result($query,0,'costo');
		$nombre_unidad        = mysql_result($query,0,'nombre_empaque');
		$nombreArticulo       = mysql_result($query,0,'nombre_articulo');
		$numeroPiezas         = mysql_result($query,0,'unidades_empaque');
		$cantidad_articulo    = mysql_result($query,0,'catidad');
		$tipo_descuento       = mysql_result($query,0,'tipo_descuento');
		$descuento_articulo   = mysql_result($query,0,'descuento');

		if($query){
			echo'<script>
					document.getElementById("unidades_'.$cont.'").value                      ="'.$nombre_unidad.' x '.$numeroPiezas.'";
					document.getElementById("idArticulo_'.$cont.'").value                    ="'.$id.'";
					document.getElementById("eanArticulo_'.$cont.'").value                   ="'.$codigo.'";
					document.getElementById("costoArticulo_'.$cont.'").value                 ="'.$costos.'";
					document.getElementById("nombreArticulo_'.$cont.'").value                ="'.$nombreArticulo.'";
					document.getElementById("cantArticulo_'.$cont.'").value                  ="'.$cantidad_articulo.'";
					document.getElementById("descuentoArticulo_'.$cont.'").value             ="'.$descuento_articulo.'";
					document.getElementById("costoTotalArticuloOrdenCompra_'.$cont.'").value ="'.($costos*$cantidad_articulo).'";

					document.getElementById("imgDescuentoArticuloOrdenCompra_'.$cont.'").setAttribute("src","img/'.$tipo_descuento.'.png")
					document.getElementById("divTipoDescuentoArticuloOrdenCompra_'.$cont.'").setAttribute("title","En '.$tipo_descuento.'")

					setTimeout(function(){ document.getElementById("eanArticulo_'.($cont+1).'").focus(); },50);
					document.getElementById("divGuardarNewArticuloOrdenCompra_'.$cont.'").style.display="none";
					document.getElementById("divImageNoUpdateArticuloOrdenCompra_'.$cont.'").style.display = "none";
				</script>';
		}
		else{ echo'<script> alert("No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema"); </script>'; }
	}

	function guardarObservacionOrdenCompra($observacion,$idOrdenCompra,$link){
		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasOrdenes   = "UPDATE compras_ordenes SET  observacion='$observacion' WHERE id='$idOrdenCompra'";
		$queryUpdateComprasOrdenes = mysql_query($sqlUpdateComprasOrdenes,$link);
		if($queryUpdateComprasOrdenes){ echo 'true'; }
		else{ echo'false'; }
	}

	function btnEliminarOrdenCompra($idOrdenCompra,$id_sucursal,$filtro_bodega,$id_empresa,$link){
		// CONSULTAR SI TIENE AUTORIZACION POR PRECIO
		$sql="SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		$aut_precio = mysql_result($query,0,'aut_precio');

		// CONSULTAR SI TIENE AUTORIZACION POR AREA
		$sql="SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		$aut_area = mysql_result($query,0,'aut_area');

		//CONSULTAMOS EL ESTADO DEL DOCUMENTO
		$sqlCheck   = "SELECT estado,autorizado FROM compras_ordenes WHERE activo=1 AND id_empresa=$id_empresa AND id=$idOrdenCompra";
		$queryCheck = mysql_query($sqlCheck,$link);
		$estado     = mysql_result($queryCheck,0,'estado');
		$autorizado = mysql_result($queryCheck,0,'autorizado');

		if ( ( $aut_precio>0 || $aut_area>0 ) && $autorizado=='true' ) {
			echo '<script>
						alert("La Orden de compra esta autorizada! por lo tanto no se puede alterar, deben quitar las autorizaciones para poder modificarla");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}

		if($estado == 2){


			$sql = "SELECT consecutivo_cruce AS consecutivo,tipo_documento_cruce AS tipo FROM compras_ordenes_doc_cruce WHERE id_orden = '$idOrdenCompra' AND activo=1";
            $query = mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
				$contenido .= $row['tipo'].' '.$row['consecutivo'].'\n';
			}
			echo '<script>
						alert("La Orden de Compra esta cruzada con los siguientes documentos:\n'.$contenido.'\nDebe eliminar el cruce o cancelar los documentos!");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}


		$sql="SELECT consecutivo FROM compras_ordenes WHERE id='$idOrdenCompra'";
		$query=mysql_query($sql,$link);
		$consecutivo=mysql_result($query,0,'consecutivo');

		$sqlUpdate ="UPDATE compras_ordenes
					SET estado=3
					WHERE estado<>2 AND id='$idOrdenCompra'";

		if ($consecutivo=='0' || $consecutivo=='') {
			$sqlUpdate ="UPDATE compras_ordenes
					SET activo=0
					WHERE estado<>2 AND id='$idOrdenCompra'";
		}


		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($queryUpdate){
		    /*=============================	PROCESO PARA LIBERAR REQUISICIONES CRUZADAS =================================*/

			// CONSULTAMOS LAS REQUISICIONES QUE TIENE CARGADAS EL DOCUMENTO

			$sqlDocReferencia  = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
				                    FROM compras_ordenes_inventario
				                    WHERE id_consecutivo_referencia>0 AND id_orden_compra='$idOrdenCompra' AND activo=1
				                    ORDER BY id ASC";
			$queryDocReferencia = mysql_query($sqlDocReferencia,$link);

			while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

				//PRIMERO PONEMOS EN ACTIVO 0 EL CRUCE CON LA ORDEN DE COMPRA ACTUAL

				$sqlCruce   = "UPDATE compras_requisicion_doc_cruce SET activo = 0 WHERE id_requisicion = '$rowDocReferencia[id_referencia]' AND id_documento_cruce='$idOrdenCompra' AND tipo_documento_cruce = 'OC'";
				$queryCruce = mysql_query($sqlCruce,$link);

				//VERIFICAMOS QUE LA REQUISICION NO ESTE CRUZADA A NINGUN OTRO DOCUMENTO, DE SER ASI LA LIBERAMOS PARA EDICION

				$sqlCheck = "SELECT id FROM compras_requisicion_doc_cruce WHERE id_requisicion = '$rowDocReferencia[id_referencia]' AND activo = 1";

				$queryCheck = mysql_query($sqlCheck,$link);
				$rows       = mysql_num_rows($queryCheck);

	            if($rows < 1){
	            	//NO ESTA CRUZADA A NINGUN DOCUMENTO
					$sqlUpdate = "UPDATE compras_requisicion SET estado=1 WHERE id='$rowDocReferencia[id_referencia]' AND id_sucursal='$id_sucursal' AND id_bodega='$filtro_bodega' AND activo=1 AND id_empresa='$id_empresa'";
					$query     = mysql_query($sqlUpdate,$link);
	            }

	    	}
			if($consecutivo!='0' || $consecutivo!=''){//Validar que la OC este generada

				// Si la OC ha sido generada se actualiza el inventario del doc cruce
				actualizaInventarioDocumentoCruce('aumentar',$idOrdenCompra,$link);
			}

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog="INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
					VALUES
					($idOrdenCompra,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Orden de Compra',".$_SESSION['SUCURSAL'].",".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','OC')";
			$queryLog=mysql_query($sqlLog,$link);

			echo'<script>
					nuevaOrdenCompra();
					// Ext.get("contenedor_ordenes_compra").load({
					// 	url     : "ordenes_compra/ordenes_compra_bloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_orden_compra : "'.$idOrdenCompra.'",
					// 		filtro_bodega : document.getElementById("filtro_ubicacion_ordenes_compra").value
					// 	}
					// });
					if(document.getElementById("modal")){
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
		}
		else{ echo'<script>
					alert("No hay conexion con la base de datos,\nsi el problema persiste comuniquese con el administrador del sistema")
					if(document.getElementById("modal")){
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>'; }
	}

	function cargarIvaArticuloOrdenCompra($id_inventario,$id_empresa,$cont,$link){
		$valorImpuesto = 0;

		$sql      = "SELECT impuestos.id,impuestos.impuesto,impuestos.valor FROM items, impuestos WHERE items.id='$id_inventario' AND items.id_impuesto = impuestos.id";
		$query    = mysql_query($sql,$link);
		$id       = mysql_result($query,0,'id');
		$impuesto = mysql_result($query,0,'impuesto');
		$valor    = mysql_result($query,0,'valor');

		if ($query) {
			echo'<script>
					document.getElementById("ivaArticuloOrdenCompra_'.$cont.'").value ="'.$id.'";

                    if (typeof(arrayIvaOrdenCompra["'.$id.'"])=="undefined") {
                       arrayIvaOrdenCompra["'.$id.'"]={nombre:"'.$impuesto.'",valor:"'.$valor.'"};
                    }
				</script>';
		}
		else{ echo '<script>console.log("error al buscar iva");</script>'; }
	}

	function restaurarOrdenCompra($idOrdenCompra,$id_empresa,$link){
		$script      = '';
		$estado      = '';
		$select      = "SELECT consecutivo FROM compras_ordenes WHERE id='$idOrdenCompra' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query       = mysql_query($select,$link);
		$consecutivo = mysql_result($query,0,'consecutivo');

		if(!$query){ echo$select.'<script>
									alert("No hay conexion con la base de datos,\nsi el problema persiste comuniquese con el administrador del sistema")
									if(document.getElementById("modal")){
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
									}
								</script>'; return; }

		if($consecutivo > 0){ $estado = 0;  $script = 'document.getElementById("titleDocuementoOrdenCompra").innerHTML="Orden de compra"+"<br>N. "+"'.$consecutivo.'";'; }
		else{ $estado = 0; $script = 'document.getElementById("titleDocuementoOrdenCompra").innerHTML=""; Ext.getCmp("Btn_nueva_orden_compra").enable();'; }

		$sqlUpdate ="UPDATE compras_ordenes
					SET estado='$estado'
					WHERE id='$idOrdenCompra'
						AND id_empresa='$id_empresa'";
		$queryUpdate = mysql_query($sqlUpdate,$link);


		if($queryUpdate){
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog="INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
					VALUES
					($idOrdenCompra,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Orden de Compra',".$_SESSION['SUCURSAL'].",".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','OC')";
			$queryLog=mysql_query($sqlLog,$link);

			echo'<script>
					'.$script.'
					Ext.get("contenedor_ordenes_compra").load({
						url     : "ordenes_compra/ordenes_compra.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_orden_compra : "'.$idOrdenCompra.'",
							filtro_bodega   : document.getElementById("filtro_ubicacion_ordenes_compra").value
						}
					});
					if(document.getElementById("modal")){
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
		}
		else{ echo'<script>
						alert("No hay conexion con la base de datos,\nsi el problema persiste comuniquese con el administrador del sistema")
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
					</script>'; }
	}

	function modificarDocumentoGenerado($idOrdenCompra,$id_empresa,$id_sucursal,$id_bodega,$link){

		// CONSULTAR SI TIENE AUTORIZACION POR PRECIO
		$sql="SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		$aut_precio = mysql_result($query,0,'aut_precio');

		// CONSULTAR SI TIENE AUTORIZACION POR AREA
		$sql="SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		$aut_area = mysql_result($query,0,'aut_area');

		//CONSULTAMOS EL ESTADO DEL DOCUMENTO
		$sqlCheck   = "SELECT estado,autorizado FROM compras_ordenes WHERE activo=1 AND id_empresa=$id_empresa AND id=$idOrdenCompra";
		$queryCheck = mysql_query($sqlCheck,$link);
		$estado     = mysql_result($queryCheck,0,'estado');
		$autorizado = mysql_result($queryCheck,0,'autorizado');

		if ( ( $aut_precio>0 || $aut_area>0 ) && $autorizado=='true' ) {
			echo '<script>
						alert("La Orden de compra esta autorizada! por lo tanto no se puede alterar, deben quitar las autorizaciones para poder modificarla");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}


		if($estado == 2){

			$sql = "SELECT consecutivo_cruce AS consecutivo,tipo_documento_cruce AS tipo FROM compras_ordenes_doc_cruce WHERE id_orden = '$idOrdenCompra' AND activo=1";
            $query = mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
				$contenido .= $row['tipo'].' '.$row['consecutivo'].'\n';
			}
			echo '<script>
						alert("La Orden de Compra esta cruzada con los siguientes documentos:\n'.$contenido.'\nDebe eliminar el cruce o cancelar los documentos!");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}

		//ACTUALIZAMOS LA FACTURA DE COMPRA A ESTADO 0 'SIN GUARDAR'
		$sql   = "UPDATE compras_ordenes SET estado=0 WHERE id='$idOrdenCompra' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		actualizaInventarioDocumentoCruce('aumentar',$idOrdenCompra,$link);

		if (!$query) {
			echo '<script>
					alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
					if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			return;
		}

		/*=============================	PROCESO PARA LIBERAR REQUISICIONES CRUZADAS =================================*/

		// CONSULTAMOS LAS REQUISICIONES QUE TIENE CARGADAS EL DOCUMENTO

		$sqlDocReferencia  = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
			                    FROM compras_ordenes_inventario
			                    WHERE id_consecutivo_referencia>0 AND id_orden_compra='$idOrdenCompra' AND activo=1
			                    ORDER BY id ASC";
		$queryDocReferencia = mysql_query($sqlDocReferencia,$link);

		while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

			//PRIMERO PONEMOS EN ACTIVO 0 EL CRUCE CON LA ORDEN DE COMPRA ACTUAL

			$sqlCruce   = "UPDATE compras_requisicion_doc_cruce SET activo = 0 WHERE id_requisicion = '$rowDocReferencia[id_referencia]' AND id_documento_cruce='$idOrdenCompra' AND tipo_documento_cruce = 'OC'";
			$queryCruce = mysql_query($sqlCruce,$link);

			//VERIFICAMOS QUE LA REQUISICION NO ESTE CRUZADA A NINGUN OTRO DOCUMENTO, DE SER ASI LA LIBERAMOS PARA EDICION

			$sqlCheck = "SELECT id FROM compras_requisicion_doc_cruce WHERE id_requisicion = '$rowDocReferencia[id_referencia]' AND activo = 1";

			$queryCheck = mysql_query($sqlCheck,$link);
			$rows       = mysql_num_rows($queryCheck);

            if($rows < 1){
            	//NO ESTA CRUZADA A NINGUN DOCUMENTO
				$sqlUpdate = "UPDATE compras_requisicion SET estado=1 WHERE id='$rowDocReferencia[id_referencia]' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa='$id_empresa'";
				$query     = mysql_query($sqlUpdate,$link);
            }


    	}

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog	  = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
					VALUES ($idOrdenCompra,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Orden de Compra',$id_sucursal,".$id_empresa.",'".$_SERVER['REMOTE_ADDR']."','OC')";
		$queryLog = mysql_query($sqlLog,$link);

		echo'<script>
			 	Ext.get("contenedor_ordenes_compra").load({
					url     : "ordenes_compra/ordenes_compra.php",
					scripts : true,
					nocache : true,
					params  :
					{
						filtro_bodega   : "'.$id_bodega.'",
						id_orden_compra : "'.$idOrdenCompra.'"
					}
				});
				if(document.getElementById("modal")){
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				}

				Ext.getCmp("Btn_nueva_orden_compra").enable();
			</script>';
	}

	// FUNCION PARA  REVERSAR EL INVENTARIO DE LAS REQUISICIONES
	function actualizaInventarioDocumentoCruce($accion,$id_orden_compra,$link){
		// echo $accion;
		if ($accion=='aumentar') {
			$sql = "UPDATE compras_requisicion_inventario AS CRI
						INNER JOIN compras_ordenes_inventario AS COI ON CRI.id=COI.id_tabla_inventario_referencia
						SET CRI.saldo_cantidad= (CRI.saldo_cantidad+COI.cantidad)
						WHERE
							COI.id_orden_compra='$id_orden_compra'
						AND COI.nombre_consecutivo_referencia='Requisicion'
						AND COI.id_tabla_inventario_referencia=CRI.id
						AND CRI.activo = 1
						AND COI.activo = 1";
			mysql_query($sql,$link);
		}
		else if ($accion=='disminuir') {
			// SI SE VA A DISMINUIR DE INVENTARIO, VERIFICAR QUE ANTES LA CANTIDAD DE UNIDADES DISPONIBLES
			// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES A LA REMISION ==>
			$sql = " SELECT COUNT(COI.id) AS cont_validate_saldo
								FROM compras_ordenes_inventario AS COI, compras_requisicion_inventario AS CRI
								WHERE COI.id_orden_compra='$id_orden_compra' AND COI.activo = 1 AND CRI.activo = 1 AND COI.nombre_consecutivo_referencia='Requisicion' AND CRI.id=COI.id_tabla_inventario_referencia
									AND COI.cantidad > CRI.saldo_cantidad
								GROUP BY COI.id";
			$contValidateSaldo = mysql_result(mysql_query($sql,$link),0,'cont_validate_saldo');
			if($contValidateSaldo > 0){
				echo '<script>
						alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en la requisicion que se adjunto en la presente orden");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}

			$sql = "UPDATE compras_requisicion_inventario AS CRI
						INNER JOIN compras_ordenes_inventario AS COI ON CRI.id=COI.id_tabla_inventario_referencia
						SET CRI.saldo_cantidad= (CRI.saldo_cantidad-COI.cantidad)
						WHERE
							COI.id_orden_compra='$id_orden_compra'
						AND COI.nombre_consecutivo_referencia='Requisicion'
						AND COI.id_tabla_inventario_referencia=CRI.id
						AND CRI.activo = 1
						AND COI.activo = 1";
			mysql_query($sql,$link);
		}

		// ACTUALIZAR LAS CABECERAS DE LAS REQUISCIONES PARA QUE SE ACTUALICE EL SALDO PENDIENTE
		$sql="UPDATE compras_requisicion AS CR
				INNER JOIN compras_ordenes_inventario AS COI ON CR.id=COI.id_consecutivo_referencia
				SET CR.activo=1
				WHERE
					COI.id_orden_compra='$id_orden_compra'
				AND COI.nombre_consecutivo_referencia='Requisicion'
				AND COI.id_consecutivo_referencia=CR.id
				AND CR.activo = 1
				AND COI.activo = 1";
		$query=mysql_query($sql,$link);
	}

	function guardarFechaOrden($idInputDate,$idOrdenCompra,$valInputDate,$link){
		if($idInputDate=='fechaOrdenCompra'){ $sqlUpdateFecha = "UPDATE compras_ordenes SET  fecha_inicio='$valInputDate' WHERE id='$idOrdenCompra'"; }
		else if($idInputDate=='fechaVencimientoOrdenCompra'){ $sqlUpdateFecha = "UPDATE compras_ordenes SET  fecha_vencimiento='$valInputDate' WHERE id='$idOrdenCompra'"; }

		$queryUpdateFecha = mysql_query($sqlUpdateFecha,$link);
		if($queryUpdateFecha){ echo 'true'; }
		else{ echo 'false'; }
	}

	// GUARDAR EL AREA QUE SOLICITA LA ORDEN
	function guardarAreaSolicitante($id_documento,$id_area_solicitante,$codigo_area_solicitante,$departamento_area_solicitante,$link){
		$sql   = "UPDATE compras_ordenes SET id_area_solicitante='$id_area_solicitante',codigo_area_solicitante=$codigo_area_solicitante,area_solicitante='$departamento_area_solicitante' WHERE id=$id_documento ";
		$query = mysql_query($sql,$link);

		if (!$query) { echo '<script>alert("Error!\nNo se guardo el solicitante, intentelo de nuevo");</script>'; }
	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoOrden($idOrdenCompra,$link){
		$sql         = "SELECT estado,id_bodega,consecutivo FROM compras_ordenes WHERE id=$idOrdenCompra";
		$query       = mysql_query($sql,$link);

		$estado      = mysql_result($query,0,'estado');
		$id_bodega   = mysql_result($query,0,'id_bodega');
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
			echo '<script>

						alert("'.$mensaje.'");

						if(document.getElementById("Win_Ventana_descripcion_Articulo_orden_compra")){
							Win_Ventana_descripcion_Articulo_orden_compra.close();
						}

						document.getElementById("titleDocuementoOrdenCompra").innerHTML="Orden de compra<br>N. '.$consecutivo.'";

						Ext.get("contenedor_ordenes_compra").load({
							url     : "ordenes_compra/ordenes_compra_bloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega   : "'.$id_bodega.'",
								id_orden_compra : "'.$idOrdenCompra.'"
							}
						});

				</script>';
			exit;
		}
	}

	function validarOrdenCompra($consecutivo,$id_empresa,$link){

		$id_usuario = $_SESSION['IDUSUARIO'];
		$usuario    = $_SESSION['NOMBREUSUARIO'];

		$sql        = "UPDATE compras_ordenes SET validacion = 'true',id_usuario_validacion = '$id_usuario',usuario_validacion = '$usuario' WHERE consecutivo='$consecutivo' AND activo=1 AND id_empresa='$id_empresa'";
		$query      = mysql_query($sql,$link);
        if($query){ echo 'true'; }
        else{ echo 'false'; }
    }

    function btnValidarOrdenCompra($consecutivo,$id_empresa,$link){

		$sql1     = "SELECT validacion,estado FROM compras_ordenes WHERE consecutivo='$consecutivo' AND activo=1 AND id_empresa='$id_empresa'";
		$query    = mysql_query($sql1,$link);
		$estado   = mysql_result($query,0,'estado');
		$validate = mysql_result($query,0,'validacion');

		echo json_encode(array("estado"=>$estado, "validate"=>$validate));
    }

    function deleteDocumentoOrden($id_host,$idDocumento,$nombre,$ext,$link){
    	$nombreImage = md5($nombre).'_'.$idDocumento.'.'.$ext;

    	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nombreImage)){
				$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nombreImage;
			} else{
				$url = '';
			}

    	$sqlDelete   = "UPDATE compras_ordenes_documentos SET activo = 0 WHERE id = $idDocumento";
			$queryDelete = mysql_query($sqlDelete,$link);
			if(!$queryDelete){
				echo '<script>
								alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");
							</script>';
				exit;
			} else{
				unlink($url);
				echo "<script>
							Elimina_Div_ordenesCompraDocumentos($idDocumento);
						</script>";
				exit;
			}
    }

    function downloadFile($nameFile,$ext,$id,$id_empresa,$id_host){
		// $nameMd5  = md5($nameFile).'_'.$id.'.'.$ext;
		// $enlace   = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/documentos_ordenes_compra/empresa_'.$id_empresa.'/'.$nameMd5;
		// $nameFile = str_replace(' ', '_', $nameFile).'.'.$ext;

		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if (file_exists($url)) {
			//header('Content-Disposition: attachment; filename='.basename($nameFile));
			header('Content-Disposition: attachment; filename='.$nameFile.'.'.$ext);
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: '.filesize($url));
		    ob_clean();
		    flush();
		    readfile($url);
	    }
	    else{ echo "Error, el archivo no se encuentra almacenado "; }
	    exit;
		}

	function consultaSizeDocumento($nameFile,$ext,$id,$id_host){
		$nameFile = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nameFile)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nameFile;
		}
		else{
			$url = '';
		}

		// $url      = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/documentos_ordenes_compra/empresa_'.$id_host.'/'.$nameFile;

		list($size['ancho'], $size['alto'], $tipo, $atributos) = getimagesize($url);
		echo json_encode($size);
	}

	function ventanaViewDocumento($nameFile,$ext,$id,$id_host){
		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nombreImage)){
			$url = '../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/ordenes_compra/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($ext != 'pdf'){
			echo'<div style="margin:0px; width:100%; height:100%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<img src="'.$url.'" id="imagenItems">
					</div>
					<a class="btn_guardar_anexo" href="'.$url.'" download="'.$nameFile.'" title="Click para descargar">
						<img src="../../temas/clasico/images/BotonesTabs/guardar.png" style="margin-top: 3px;"/>
						<div style="color:#000; text-align: center;">Descargar</div>
					</a>
				</div>
				<script>
					document.getElementById("imagenItems").oncontextmenu = function(){ return false; }
				</script>
				<style>

					.btn_guardar_anexo{
						opacity     : 0.4;
						position    : absolute;
						width       : 68px;
						height      : 55px;
						top         : 5;
						left        : 10;
						overflow    : hidden;
						text-align  : center;
						color       : #333;
						padding     : 0px;
						margin      : 0px;
						font-weight : bold;
						border      : 3px solid #000;
						background-color      : #FFF;
						-moz-border-radius    : 1px;
						-webkit-border-radius : 1px;
						background : -webkit-linear-gradient(#FFF, #CECECE);
						background : -moz-linear-gradient(#FFF, #CECECE);
						background : -o-linear-gradient(#FFF, #CECECE);
						background : linear-gradient(#FFF, #CECECE);

						-webkit-box-shadow: 4px 7px 45px 2px rgba(255,255,255,1);
						-moz-box-shadow: 4px 7px 45px 2px rgba(255,255,255,1);
						box-shadow: 4px 7px 45px 2px rgba(255,255,255,1);
					}

					.btn_guardar_anexo:hover{
						opacity : 1;
						-webkit-animation : cssAnimation 1s 1 ease-in;
						-moz-animation    : cssAnimation 1s 1 ease-in;
						-o-animation      : cssAnimation 1s 1 ease-in;
						animation-iteration-count : 1;
					}

					@-webkit-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
					@-moz-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
					@-o-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
				</style>';
		}
		else{
			echo'<div style="margin:0px; width:100%; height:100%; background-color:#FFF; display:table">
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

	function updateTipoOrden($id_orden,$id_tipo,$link){

		$sqlOrden    = "SELECT nombre FROM compras_ordenes_tipos WHERE id='$id_tipo'";
		$queryOrden  = mysql_query($sqlOrden,$link);
		$nombre      = mysql_result($queryOrden,0,'nombre');


		$sqlUpdate   = "UPDATE compras_ordenes SET id_tipo = $id_tipo,tipo_nombre = '$nombre' WHERE id = $id_orden";
		$queryUpdate = mysql_query($sqlUpdate,$link);
		/*if(!$queryUpdate){
			echo '<script>alert("No se puede guardar el tipo de orden, si el problema persiste favor comuniquese con el administrador del sistema");</script>';
		}*/
		//echo $sqlUpdate;
	}

	function agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$campoCantidad          = "saldo_cantidad";
		$title                  = 'Eliminar los Articulos de la Requisicion';
		$referencia_input       = "R";
		$referencia_consecutivo = "Requisicion";
		$tablaCarga             = "compras_requisicion";
		$idTablaCargar          = "id_requisicion_compra";
		$tablaCargaInventario   = "compras_requisicion_inventario";
		$docReferencia          = 'R'; //ESTA VARIABLE LLEGA AL condicional de eliminaDocReferencia
		$tablaBuscar            ="compras_requisicion";

		$sqlFactura   = "SELECT id_proveedor, estado,id_area_solicitante,tipo_nombre FROM compras_ordenes WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$queryFactura = mysql_query($sqlFactura,$link);

		$idProveedor = mysql_result($queryFactura,0,'id_proveedor');
		$estado      = mysql_result($queryFactura,0,'estado');
		$area_orden  = mysql_result($queryFactura,0,'id_area_solicitante');
		$tipo_nombre = mysql_result($queryFactura,0,'tipo_nombre');

		// CONSULTAR LOS TIPOS DE ORDENES DE COMPRA
		$sql="SELECT id,nombre FROM compras_ordenes_tipos WHERE activo=1 AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id_tipo     = $row['id'];
			$nombre_tipo = $row['nombre'];
			$arrayTipoOc[$nombre_tipo] = $id_tipo;
		}

		// VALIDAR QUE LA REQUISICION ESTE AUTORIZADA
		$sql="SELECT autorizado,id_area_solicitante,codigo_area_solicitante,area_solicitante,tipo_nombre
				FROM compras_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND consecutivo=$codDocAgregar";
		$query=mysql_query($sql,$link);
		$autorizado              = mysql_result($query,0,'autorizado');
		$id_area_requisicion     = mysql_result($query,0,'id_area_solicitante');
		$codigo_area_requisicion = mysql_result($query,0,'codigo_area_solicitante');
		$area_requisicion        = mysql_result($query,0,'area_solicitante');
		$tipo_nombre_requisicion = mysql_result($query,0,'tipo_nombre');

		if ($autorizado == 'false' ) {
			echo '<script>alert("Error!,\nLa requisicion no ha sido autorizada!");</script>';
			return;
		}
		else if ($area_orden>0 && $area_orden<>$id_area_requisicion) {
			echo '<script>alert("Aviso!,\nLa requisicion tiene un area diferente al area de la orden, en ambos documentos debe ser la misma!");</script>';
			return;
		}

		if($estado == 1){ echo '<script>alert("Error!,\nLa presente Orden de Compra ha sido generada.");</script>'; return; }
		if($estado == 3){ echo '<script>alert("Error!,\nLa presente Orden de Compra ha sido cancelada.");</script>'; return; }
		else if(($idProveedor == '' || $idProveedor == 0) && $typeDoc != 'requisicion'){

			cargarNewDocumento($codDocAgregar,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,'ordenes_compra/', $opcGrillaContable, '', $idTablaPrincipal, $tablaInventario, '',$typeDoc);
			return;
		}
		else if(($idProveedor == '' || $idProveedor == 0) && $typeDoc == 'requisicion'){
			echo '<script>
        	     	alert("Aviso!,\nPor favor asigne un Proveedor al documento para cargar una requisicion.");
        	     	if (document.getElementById("Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'.close();
					}
        		  </script>';
        	return;
		}

		// $whereRemision = ($typeDoc == 'remision')? "AND CO.pendientes_facturar > 0 AND COI.saldo_cantidad > 0": "";

		//VALIDACION ESTADO DE LA FACTURA
		$idProveedorDocAgregar  = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT estado,id,observacion $campoSelect FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' ";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idProveedorDocAgregar = mysql_result($queryValidateDocumento,0,'id_proveedor');
		$idDocumentoAgregar    = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar      = mysql_result($queryValidateDocumento,0,'estado');
		$observacion           = mysql_result($queryValidateDocumento,0,'observacion');

		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		// else if(($idProveedorDocAgregar <> $idProveedor) && $typeDoc != 'requisicion'){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenece a un proveedor diferente.");</script>'; return; }

		//VALIDACION QUE EL DOCUMENTO NO HAYA SIDO INGRESADO
		$sqlValidateRepetido = "SELECT COUNT(id) AS contDocRepetido
								FROM $tablaInventario
								WHERE activo=1 AND id_consecutivo_referencia='$idDocumentoAgregar'
									AND nombre_consecutivo_referencia='$referencia_consecutivo'
									AND id_orden_compra='$id_factura'
								GROUP BY id_tabla_inventario_referencia LIMIT 0,1";

		// $sqlValidateRepetido = "SELECT COUNT(id) AS contDocRepetido
		// 						FROM $tablaInventario
		// 						WHERE activo=1 AND id_empresa='$id_empresa' AND id_bodega='$filtro_bodega' AND id_consecutivo_referencia='$idDocumentoAgregar'
		// 							AND nombre_consecutivo_referencia='$referencia_consecutivo'
		// 							AND id_orden_compra='$id_factura'
		// 						GROUP BY id_tabla_inventario_referencia LIMIT 0,1";

		$docRepetido = mysql_result(mysql_query($sqlValidateRepetido,$link),0,'contDocRepetido');
		if($docRepetido > 0){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' ya ha sido agregado en la presente orden de compra");</script>'; return; }

		// ACTUALIZAR LA CABECERA DE LA ORDEN DE COMPRA
		if ($observacion <> '') {
			$arrayReplaceString = array("\n", "\r","<br>");
			$observacion        = str_replace($arrayReplaceString, "\\n", $observacion);
			$camposUpdate[] ="observacion = IF(
											observacion<>'',
											CONCAT(observacion, '<br>', '$referencia_input ', '$codDocAgregar', ': ', '$observacion'),
											CONCAT('$referencia_input ', '$codDocAgregar', ': ', '$observacion')
										)";
			$acumScript .="var observacionOC = document.getElementById('observacion$opcGrillaContable')
							observacionOC.value=observacionOC.value+'<br> $referencia_input $codDocAgregar: $observacion'; ";
		}
		if ($area_orden<=0 || is_null($area_orden)){
			$camposUpdate[] ="id_area_solicitante 	= '$id_area_requisicion',
							codigo_area_solicitante = '$codigo_area_requisicion',
							area_solicitante        = '$area_requisicion'";
			$acumScript .="document.getElementById('areaSolcitante').value='$area_requisicion';";
		}
		$search_array = array('first' => 1, 'second' => 4);
		if (array_key_exists($tipo_nombre_requisicion, $arrayTipoOc)) {
		    $camposUpdate[] = "id_tipo=$arrayTipoOc[$tipo_nombre_requisicion],
		    				tipo_nombre='$tipo_nombre_requisicion' ";
			$acumScript .="document.getElementById('selectTipoOrdenCompra').value='$arrayTipoOc[$tipo_nombre_requisicion]';";
		}

		// SI HAY CAMPOS A ALMACENAR ENTONCES ACTUALIZAR LA CABECERA DEL DOCUMENTO
		if (!empty($camposUpdate)) {
			$sql="UPDATE compras_ordenes SET ".implode(", ",$camposUpdate)." WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
			$query=mysql_query($sql,$link);
		}
		// if($observacion <> ''){
		// 	$sqlObservacion = "UPDATE compras_ordenes
		// 						SET observacion = IF(
		// 									observacion<>'',
		// 									CONCAT(observacion, '<br>', '$referencia_input ', '$codDocAgregar', ': ', '$observacion'),
		// 									CONCAT('$referencia_input ', '$codDocAgregar', ': ', '$observacion')
		// 								)
		// 						WHERE id='$id_factura'
		// 							AND id_empresa='$id_empresa'
		// 							AND activo=1";
		// 	$queryObservacion = mysql_query($sqlObservacion,$link);

		// 	// ACTUALIZAMOS EL CONTENEDOR DE LA OBSERVACION

		// 	$sqlSelect = "SELECT observacion FROM compras_ordenes WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
	  //           $querySelect = mysql_query($sqlSelect,$link);

	  //           $observacion = mysql_result($querySelect,0,'observacion');

	  //           $arrayReplaceString = array("\n","\r","<br>");
	  //       	$observacion        = str_replace($arrayReplaceString,"\\n",$observacion);

	  //           echo '<script>document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";</script>';

		// }

		// ACTUALIZAR EL AREA DE LA ORDEN
		// if ($area_orden<=0 || is_null($area_orden)){
		// 	$sql="UPDATE compras_ordenes
		// 			SET id_area_solicitante 	= '$id_area_requisicion',
		// 				codigo_area_solicitante = '$codigo_area_requisicion',
		// 				area_solicitante        = '$area_requisicion'
		// 			WHERE id='$id_factura'
		// 				AND id_empresa='$id_empresa'
		// 				AND activo=1
		// 			";
		// 	$query=mysql_query($sql,$link);
		// 	// MOSTRAR EN DOM EL AREA DE LA ORDEN
		// 	echo "<script> document.getElementById('areaSolcitante').value='$area_requisicion'; </script>";
		// }

		//GENERA CICLO PARA INSERTAR ARTICULOS DEL DOCUMENTO REFERENCIA A TABLA INVENTARIOS FACTURAS
		$sqlConsultaInventario= "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.$campoCantidad AS cantidad,COI.costo_unitario,
                                        COI.tipo_descuento,COI.descuento,
                                        COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                        CO.id AS id_documento,CO.consecutivo AS consecutivo_documento,COI.id_centro_costos
                                FROM $tablaCargaInventario AS COI
                                INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                WHERE CO.consecutivo     ='$codDocAgregar'
                                    AND COI.activo       = 1
                                    AND CO.id_sucursal   ='$id_sucursal'
                                    AND CO.id_bodega     ='$filtro_bodega'
                                    AND CO.id_empresa    ='$id_empresa'
                                    AND CO.pendientes_facturar > 0 AND COI.saldo_cantidad > 0
                                   ";
        $queryConsultaInventario=mysql_query($sqlConsultaInventario,$link);

        $contInsert=0;
        while ($row = mysql_fetch_array($queryConsultaInventario)) {
        	$contInsert++;
    		$idDocCruce = $row['id_documento'];
        	$valueInsert .= "(
	        					'$id_factura',
	                            '".$row['id_inventario']."',
	                            '".$row['cantidad']."',
	                            '".$row['cantidad']."',
	                            '".$row['costo_unitario']."',
	                            '".$row['tipo_descuento']."',
	                            '".$row['descuento']."',
	                            '".$row['observaciones']."',
	                            '".$row['id']."',
	                            '".$row['id_documento']."',
	                            '".$row['consecutivo_documento']."',
	                            '$referencia_consecutivo',
	                            '".$row['id_centro_costos']."'
                        	),";
        }

        $valueInsert = substr($valueInsert,0,-1);
        $sqlInsertArticulos="INSERT INTO $tablaInventario
                                        ($idTablaPrincipal,
                                        id_inventario,
                                        cantidad,
                                        saldo_cantidad,
                                        costo_unitario,
                                        tipo_descuento,
                                        descuento,
                                        observaciones,
                                        id_tabla_inventario_referencia,
                                        id_consecutivo_referencia,
                                        consecutivo_referencia,
                                        nombre_consecutivo_referencia,
                                        id_centro_costos)
                            VALUES $valueInsert";
        $queryInsertArticulos=mysql_query($sqlInsertArticulos,$link);

        if($contInsert > 0){
    		$newDocReferencia  ='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;height: 22px;" id="divDocReferencia'.$opcGrillaContable.'_'.$docReferencia.'_'.$idDocumentoAgregar.'">'
							       .'<div class="contenedorInputDocReferenciaFactura">'
							           .'<input type="text" class="inputDocReferenciaEntradaAlmacen" value="'.$referencia_input.' '.$codDocAgregar.'" readonly style="border-bottom: 1px solid #d4d4d4;"/>'
							       .'</div>'
							       .'<div title="'.$title.' # '.$codDocAgregar.' en la presente orden de compra" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\\\''.$idDocumentoAgregar.'\\\',\\\''.$docReferencia.'\\\',\\\''.$id_factura.'\\\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">'
							           .'<div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$docReferencia.'_'.$idDocCruce.'">'
	                                        .'<div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>'
	                                    .'</div>'
							       .'</div>'
							    .'</div>';

			echo"<script>
					$acumScript
					divDocsReferenciaFactura = document.getElementById('contenedorDocsReferencia$opcGrillaContable').innerHTML;
					document.getElementById('contenedorDocsReferencia$opcGrillaContable').innerHTML =divDocsReferenciaFactura+'$newDocReferencia';
	    			document.getElementById('cotizacionPedido$opcGrillaContable').value='';

	    			Ext.get('renderizaNewArticulo$opcGrillaContable').load({
			            url     : 'ordenes_compra/bd/bd.php',
			            scripts : true,
			            nocache : true,
			            params  :
			            {
							opc               : 'reloadBodyAgregarDocumento',
							opcGrillaContable : '$opcGrillaContable',
							id_documento      : '$id_factura',
			            }
			        });

					actualiza_fila_ventana_busqueda_doc_cruce($idDocumentoAgregar);
        		</script>";
        }
        else{
        	echo'<script>
        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
        			alert("Numero invalido!\nDocumento no terminado o ya asignado");
        			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();}, 100);
        		</script>';
		}
	}

	function reloadBodyAgregarDocumento($opcGrillaContable,$id_documento,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

		$sql   = "SELECT observacion FROM $tablaPrincipal WHERE id=$id_documento AND activo = 1";

		$query = mysql_query($sql,$link);

		$observacion = mysql_result($query,0,'observacion');

	    $arrayReplaceString = array("\n","\r","<br>");
        $observacion        = str_replace($arrayReplaceString,"\\n",$observacion);

		echo cargaArticulosOrdenCompraSave($id_documento,$observacion,0,$link);
            // cargaArticulosOrdenCompraSave($idOrdenCompra,$observacionOrdenCompra,$estadoOrdenCompra,$link)
	    echo '<script>
	                if (document.getElementById("Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'.close();
					}
        	  </script>';
	}

	function eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

        //QUERY PARA CONSULTAR LA OBSERVACION PARA CARGARLA EN EL CONTENEDOR

        $sqlO   = "SELECT observacion FROM $tablaPrincipal WHERE id=$id_factura AND activo = 1";

		$queryO = mysql_query($sqlO,$link);

		$observacion = mysql_result($queryO,0,'observacion');

	    $arrayReplaceString = array("\n","\r","<br>");
        $observacion        = str_replace($arrayReplaceString,"\\n",$observacion);


		$campoDocReferencia = '';

		if($docReferencia=='R'){ $campoDocReferencia = 'Requisicion'; }
		else if($docReferencia=='O'){ $campoDocReferencia = 'Orden de Compra'; }

		$sql   ="DELETE FROM $tablaInventario WHERE $idTablaPrincipal=$id_factura AND id_consecutivo_referencia=$id_doc_referencia  AND nombre_consecutivo_referencia='$campoDocReferencia'";
		$query = mysql_query($sql,$link);

		echo cargaArticulosOrdenCompraSave($id_factura,$observacion,0,$link);

		if($query){
			echo'<script>
					document.getElementById("divDocReferencia'.$opcGrillaContable.'_'.$docReferencia.'_'.$id_doc_referencia.'").parentNode.removeChild(document.getElementById("divDocReferencia'.$opcGrillaContable.'_'.$docReferencia.'_'.$id_doc_referencia.'"));
				</script>';
		}
		else{ echo'<script>alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
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
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final ";
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

	// REDIRECCIONAR AL PROCESO DE AUTORIZACION QUE SE HUBIESE CONFIGURADO
	function controladorAutorizaciones($idDocumento,$id_sucursal,$id_empresa,$mysql){
		// CONSULTAR SI TIENE AUTORIZACION POR PRECIO
		$sql = "SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo = 1 AND id_empresa = $id_empresa";
		$query = $mysql->query($sql,$mysql->link);
		$aut_precio = $mysql->result($query,0,'aut_precio');

		// CONSULTAR SI TIENE AUTORIZACION POR AREA
		$sql = "SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo = 1 AND id_empresa = $id_empresa";
		$query = $mysql->query($sql,$mysql->link);
		$aut_area = $mysql->result($query,0,'aut_area');

		// SI NO TIENE NINGUNA CONFIGURACION DE AUTORIZACION
		if($aut_precio == 0 && $aut_area == 0){
			if($whereIdRango == ''){
				echo '<style>
			 					.contenedor{
									width      : 100%;
									height     : 100%;
									text-align : center;
			 					}
			 					.title{
									width       : 100%;
									padding     : 10px 0px 10px 0px;
									font-weight : bold;
									font-size   : 14px;
									color       : #000;
			 					}
								.body{
									width     : 100%;
									font-size : 14px;
									color     : #000;
									padding   : 10px 0px 10px 0px;
								}
			 				</style>
			 				<div class="contenedor">
			 					<div class="title">INFORMACI&Oacute;N DE CONFIGURACI&Oacute;N</div>
			 					<div class="body">No existe ninguna configuracion creada para autorizaciones de las ordenes de compra en el panel de control  </div>
			 				</div>';
			}
		}
		if($aut_precio > 0 && $aut_area > 0){
			echo '<style>
		 					.contenedor{
								width      : 100%;
								height     : 100%;
								text-align : center;
		 					}
		 					.title{
								width       : 100%;
								padding     : 10px 0px 10px 0px;
								font-weight : bold;
								font-size   : 14px;
								color       : #000;
		 					}
							.body{
								width     : 100%;
								font-size : 14px;
								color     : #000;
								padding   : 10px 0px 10px 0px;
							}
		 				</style>
		 				<div class="contenedor">
		 					<div class="title">INFORMACI&Oacute;N DE CONFIGURACI&Oacute;N</div>
		 					<div class="body">
		 						Hay un error en la configuracion de autorizaciones, actualmente tiene configuradas dos opciones,
		 						por rango de precio y por area, pero solo es posible aplicar una opcion, dirijase al panel de control
		 						y quite la opcion por la que no se debe autorizar, e intentelo de nuevo
							</div>
		 				</div>';
		}
		else if($aut_precio > 0){
			ventanaAutorizaDocumento($idDocumento,$id_empresa,$mysql);
		}
		else if($aut_area > 0){
			ventanaAutorizaDocumentoArea($idDocumento,'',$id_sucursal,$id_empresa,$mysql);
		}
	}

	// VENTANA DE AUTORIZACION POR PRECIO
	function ventanaAutorizaDocumento($idDocumento,$id_empresa,$mysql){
 		$id_empleado = $_SESSION['IDUSUARIO'];
 		// CONSULTAR EL TOTAL DEL DOCUMENTO
		$sqlItems = "SELECT cantidad,costo_unitario,valor_impuesto,tipo_descuento,descuento FROM compras_ordenes_inventario WHERE activo=1 AND id_orden_compra=$idDocumento";
		$query   = $mysql->query($sqlItems,$mysql->link);
		while ($array = $mysql->fetch_array($query)) {
			$tipoDesc = $array["tipo_descuento"];

			//variables para los calculos
			$subtotal         = 0;
			$valorIva         = 0;
			$descuentoTotal   = 0;
			$descuentoMostrar = 0;

			//calculamos el subtotal por articulo
			$subtotal= $array["cantidad"]*$array["costo_unitario"];

			if ($tipoDesc=='porcentaje') {
					$valorDescuento   = (($subtotal*$array["descuento"])/100);
					$descuentoMostrar = $array["descuento"];
					$tipodesart       = '%';
			}
			else if($tipoDesc=='pesos'){
					$valorDescuento   = $array["descuento"];
					$descuentoMostrar = $valorDescuento;
					$tipodesart       = '$';
			}
			$iva = $subtotal*$array['valor_impuesto']/100;
			$costo_total += $subtotal-$valorDescuento+$iva;
		}

		// CONSULTAR LOS TOPES DE VERIFICACION
		$sql   = "SELECT id,rango_inicial,rango_final FROM rango_autorizaciones_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa";
		$query = $mysql->query($sql,$mysql->link);
		while ($row = $mysql->fetch_array($query)) {

			if ($costo_total>=$row["rango_inicial"] && $costo_total<=$row["rango_final"] || ($row["rango_inicial"]==0 && $row["rango_final"]==0) ) {
				$whereIdRango= 'id_rango='.$row['id'];
				break;
			}
		}

		if ($whereIdRango=='') {
			echo '<style>
 					.contenedor{
						width      : 100%;
						height     : 100%;
						text-align : center;
 					}
 					.title{
						width       : 100%;
						padding     : 10px 0px 10px 0px;
						font-weight : bold;
						font-size   : 14px;
						color       : #000;
 					}
					.body{
						width     : 100%;
						font-size : 14px;
						color     : #000;
						padding   : 10px 0px 10px 0px;
					}
 				</style>
 				<div class="contenedor">
 					<div class="title">INFORMACI&Oacute;N DE CONFIGURACI&Oacute;N</div>
 					<div class="body">No existe ninguna configuracion creada para autorizaciones de las ordenes de compra en el panel de control  </div>
 				</div>

					<script>
 					// alert("Su usuario no tiene privilegio para autorizar el documento");
 					// Win_Ventana_autoriza_documento.close(id);
 				</script>';
 			exit;
		}

		// CONSULTAR LOS ROLES QUE CORRESPONDEN A ESOS RANGOS
		$sql    = "SELECT id_rol,orden,codigo_rol,rol FROM  costo_autorizadores_ordenes_compra WHERE activo=1 /*AND id_empresa=$id_empresa*/ AND($whereIdRango) ORDER BY orden ASC";
		$query  = $mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$whereIdRol.=($whereIdRol=='')? 'id_rol='.$row['id_rol'] : ' OR id_rol='.$row['id_rol'] ;
		}
		$id_rol = $mysql->result($query,0,'id_rol');

		// CONSULTAR LOS EMPLEADOS DEL ROL, CON SUS RESPECTIVOS EMAIL
		$sql   = "SELECT id,documento,nombre,email_empresa,cargo FROM empleados WHERE activo=1 AND /*id_empresa=$id_empresa AND*/ ($whereIdRol) ORDER BY id";
		$query = $mysql->query($sql,$mysql->link);
		$validate_user='false';
		while ($row=$mysql->fetch_array($query)) {
			$arrayEmpleados[$row['id']] = array('documento_empleado' => $row['documento'],'nombre_empleado'=>$row['nombre'],'email_empresa'=>$row['email_empresa'],'cargo'=>$row['cargo'] );
			$whereIdEmpleados.=($whereIdEmpleados=='')? 'id_empleado='.$row['id'] : ' OR id_empleado='.$row['id'] ;
			if ($validate_user=='false') {
				if ($id_empleado==$row['id']) {
					$validate_user = 'true';
				}
			}
		}

 		if ($validate_user=='false') {
 			echo '<style>
 					.contenedor{
						width      : 100%;
						height     : 100%;
						text-align : center;
 					}
 					.title{
						width       : 100%;
						padding     : 10px 0px 10px 0px;
						font-weight : bold;
						font-size   : 14px;
						color       : #000;
 					}
					.body{
						width     : 100%;
						font-size : 14px;
						color     : #000;
						padding   : 10px 0px 10px 0px;
					}
 				</style>
 				<div class="contenedor">
 					<div class="title">INFORMACI&Oacute;N DE SEGURIDAD</div>
 					<div class="body">Su rol de usuario no esta configurado para autorizar este documento </div>
 				</div>

					<script>
 					// alert("Su usuario no tiene privilegio para autorizar el documento");
 					// Win_Ventana_autoriza_documento.close(id);
 				</script>';
 			exit;
 		}

 		$sql="SELECT id,id_empleado,documento_empleado,nombre_empleado,tipo_autorizacion
 			FROM compras_ordenes_autorizaciones WHERE activo=1 AND id_empresa=$id_empresa AND id_orden_compra=$idDocumento AND ($whereIdEmpleados) AND ($whereIdRol) ";
 		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)){
			$arrayTipoAutorizacion[$row['id_empleado']] = $row['tipo_autorizacion'];
		}

		foreach ($arrayEmpleados as $id_empleado_array => $arrayEmpleadosResul) {
			$contentAut = ($arrayTipoAutorizacion[$id_empleado_array]<>'')? '<img style="height:11px;" src="img/'.$arrayTipoAutorizacion[$id_empleado_array].'.png" > '.$arrayTipoAutorizacion[$id_empleado_array]
 						: '<span style="color:#A8A8A8;font-style:italic;">Sin Autorizacion</span>' ;

 			$bodyUsuarios.='<div class="filaDivs" style="width:90px;">'.$arrayEmpleadosResul['documento_empleado'].'&nbsp;</div>
							<div class="filaDivs" style="width:200px;">'.$arrayEmpleadosResul['nombre_empleado'].'&nbsp;</div>
							<div class="filaDivs" style="width:145px;">'.$arrayEmpleadosResul['cargo'].'&nbsp;</div>
							<div class="filaDivs" style="width:100px;border-right: none;text-align:center;" id="div_img_autorizacion_'.$id_empleado_array.'"> '.$contentAut.' </div>';
		}

 		$sql = "SELECT documento,nombre,cargo FROM empleados WHERE activo=1 AND id_empresa = $id_empresa AND id = $id_empleado";
 		$query = $mysql->query($sql,$mysql->link);
		$documento_empleado = $mysql->result($query,0,'documento');
		$nombre_empleado    = $mysql->result($query,0,'nombre');
		$cargo              = $mysql->result($query,0,'cargo');

 		echo '<style>
						.titulos_ventana{
							color       : #15428B;
							font-weight : bold;
							font-size   : 13px;
							font-family : tahoma,arial,verdana,sans-serif;
							text-align  : center;
							float       : left;
							width       : 100%;
							border-top  : 1px solid #8DB2E3;
							padding-top : 15px;
						}
						.contenedor_tablas_cuentas{
							float            : left;
							width            : 550px;
							height           : auto;
							background-color : #FFF;
							margin-top       : 10px;
							margin-left      : 20px;
							border           : 1px solid #D4D4D4;
							border-bottom    : none;
						}
						.headDivs{
							float            : left;
							background-color : #F3F3F3;
							padding          : 5 0 5 3;
							font-size        : 11px;
							font-weight      : bold;
							border-right     : 1px solid #D4D4D4;
							border-bottom    : 1px solid #D4D4D4;
						}
						.filaDivs{
							float         : left;
							border-right  : 1px solid #D4D4D4;
							border-bottom  : 1px solid #D4D4D4;
							padding       :  5 0 5 3;
							overflow      : hidden;
							white-space   : nowrap;
							text-overflow : ellipsis;
						}
						.filaDivs img{
							cursor : pointer;
						}
						.divIcono{
							float            : left;
							width            : 20px;
							height           : 16px;
							padding          : 3 0 4 5;
							background-color : #F3F3F3;
							overflow         : hidden;
						}
						.divIcono>img{
							cursor : pointer;
							width  : 16px;
							height : 16px;
						}
						.contenedor{
							width: 589px;
							height : 150px;
							overflow : auto;
							float: left;
						}
					</style>
					<div style="width:100%;">
						<div class="titulos_ventana">AUTORIZACIONES REQUERIDAS</div>
						<div class="contenedor">
							<div class="contenedor_tablas_cuentas">
								<div class="headDivs" style="width:90px;">DOCUMENTO</div>
								<div class="headDivs" style="width:200px;">USUARIO</div>
								<div class="headDivs" style="width:145px;">CARGO</div>
								<div class="headDivs" style="width:100px;border-right: none;">AUTORIZACION</div>
								'.$bodyUsuarios.'
							</div>
						</div>
						<div class="titulos_ventana" style="border:none;padding-top:5px;">AUTORIZAR</div>
							<div class="contenedor_tablas_cuentas" style="height:50px;overflow:hidden;">
								<div class="headDivs" style="width:90px;">DOCUMENTO</div>
								<div class="headDivs" style="width:177px;">USUARIO</div>
								<div class="headDivs" style="width:145px;">CARGO</div>
								<div class="headDivs" style="width:123px; border-right: none;">AUTORIZACION</div>
								<div class="filaDivs" style="height:22px; width:90px;">'.$arrayEmpleados[$id_empleado]['documento_empleado'].'</div>
								<div class="filaDivs" style="height:22px; width:177px;">'.$arrayEmpleados[$id_empleado]['nombre_empleado'].'</div>
								<div class="filaDivs" style="height:22px; width:145px;">'.$arrayEmpleados[$id_empleado]['cargo'].'</div>
								<div class="filaDivs" style="width:100px; padding: 0px;">
									<select id="tipo_autorizacion" style="border:none;">
										<option value="">Seleccione...</option>
										<option value="Autorizada">Autorizada</option>
										<option value="Aplazada">Aplazada</option>
										<option value="Rechazada">Rechazada</option>
									</select>
								</div>
								<div class="filaDivs" style="height:22px; width:22px;border-right: none;" id="divLoadAutorizacion">
									<img src="img/save_true.png" title="Guardar Autorizacion" onclick="autorizarOrdenCompra()">
								</div>
							</div>
						</div>
						<script>
							document.getElementById("tipo_autorizacion").value="'.$arrayTipoAutorizacion[$id_empleado].'";
						</script>';
 	}

 	// AUTORIZAR LA ORDEN DE COMPRA POR PRECIO
 	function autorizarOrdenCompra($id_orden_compra,$tipo_autorizacion,$id_empresa,$mysql){
 		$id_empleado = $_SESSION['IDUSUARIO'];

 		// CONSULTAR LA INFORMACION DEL EMPLEADO
 		$sql = "SELECT documento,nombre,id_cargo,cargo,email_empresa,id_rol FROM empleados WHERE activo = 1 AND id = $id_empleado";
 		$query = $mysql->query($sql,$mysql->link);

		$documento_empleado = $mysql->result($query,0,'documento');
		$nombre_empleado    = $mysql->result($query,0,'nombre');
		$email              = $mysql->result($query,0,'email_empresa');
		$id_cargo           = $mysql->result($query,0,'id_cargo');
		$cargo              = $mysql->result($query,0,'cargo');
		$id_rol             = $mysql->result($query,0,'id_rol');

 		// CONSULTAR SI EL DOCUMENTO YA TIENE UNA AUTORIZACION POR PARTE DEL USUARIO
 		$sql = "SELECT id FROM compras_ordenes_autorizaciones WHERE activo = 1 AND id_empleado = $id_empleado AND id_orden_compra = $id_orden_compra";
 		$query = $mysql->query($sql,$mysql->link);
 		$id_registro = $mysql->result($query,0,'id');

 		// SI EXISTE UN REGISTRO SE DEBE ACTUALIZAR
 		if($id_registro > 0){
 			$sql = "UPDATE compras_ordenes_autorizaciones SET tipo_autorizacion = '$tipo_autorizacion'
 							WHERE activo = 1 AND id_empresa = $id_empresa AND id_empleado = $id_empleado AND id_orden_compra = $id_orden_compra AND id_rol = $id_rol";
 			$query = $mysql->query($sql,$mysql->link);
 		}
 		// SI NO EXISTE UN REGISTRO DE DEBE INSERTAR LA FILA
 		else{
 			$sql = "INSERT INTO compras_ordenes_autorizaciones (id_empleado,documento_empleado,nombre_empleado,id_cargo,cargo,id_rol,tipo_autorizacion,id_orden_compra,id_empresa)
							VALUES('$id_empleado','$documento_empleado','$nombre_empleado','$id_cargo','$cargo','$id_rol','$tipo_autorizacion','$id_orden_compra','$id_empresa')";
 			$query = $mysql->query($sql,$mysql->link);
 		}

 		if(!$query){
 			echo '<script>alert("Error\nNo se logro generar la autorizacion");</script>';
 		}
 		else{
			// CONSULTAR EL TOTAL DEL DOCUMENTO
			$sqlItems = "SELECT cantidad,costo_unitario,valor_impuesto,tipo_descuento,descuento FROM compras_ordenes_inventario WHERE activo = 1 AND id_orden_compra = $id_orden_compra";
			$query = $mysql->query($sqlItems,$mysql->link);
			$costo_total = 0;
			while($array = $mysql->fetch_array($query)){
				$subtotal = $array["cantidad"] * $array["costo_unitario"];
				$iva = $subtotal * $array['valor_impuesto'] / 100;
				$valorDescuento = ($array["tipo_descuento"] == 'porcentaje')?	(($subtotal * $array["descuento"]) / 100) : $array["descuento"];
																
				$costo_total += $subtotal - $valorDescuento + $iva;
			}

			// CONSULTAR LOS TOPES DE VERIFICACION
			$sql   = "SELECT id,rango_inicial,rango_final FROM rango_autorizaciones_ordenes_compra WHERE activo = 1 AND id_empresa = $id_empresa";
			$query = $mysql->query($sql,$mysql->link);
			while($row = $mysql->fetch_array($query)){

				if ($costo_total>=$row["rango_inicial"] && $costo_total<=$row["rango_final"] || ($row["rango_inicial"]==0 && $row["rango_final"]==0) ) {
					$whereIdRango= 'id_rango='.$row['id'];
					break;
				}
			}

			//////////////////////////////////////////////
			// VALIDAR SI SE AUTORIZO TODO EL DOCUMENTO //
			//////////////////////////////////////////////
			// CONSULTAR LOS ROLES QUE CORRESPONDEN A ESOS RANGOS
			$sql    = "SELECT id_rol FROM  costo_autorizadores_ordenes_compra WHERE activo = 1 AND($whereIdRango) ORDER BY orden ASC";
			$query  = $mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$whereIdRol.=($whereIdRol=='')? 'id_rol='.$row['id_rol'] : ' OR id_rol='.$row['id_rol'] ;
			}

			// CONSULTAR LOS EMPLEADOS DEL ROL
			$sql   = "SELECT id FROM empleados WHERE activo = 1 AND ($whereIdRol) ORDER BY id";
			$query = $mysql->query($sql,$mysql->link);
			$validate_user = 'false';
			while($row = $mysql->fetch_array($query)){
				$whereIdEmpleados .= ($whereIdEmpleados == '')? 'id_empleado=' . $row['id'] : ' OR id_empleado=' . $row['id'];
				$contAutorizadores++;
			}

			$sql = "SELECT tipo_autorizacion
 							FROM compras_ordenes_autorizaciones
							WHERE activo = 1 AND id_empresa = $id_empresa AND id_orden_compra = $id_orden_compra AND ($whereIdEmpleados) AND ($whereIdRol)";
	 		$query = $mysql->query($sql,$mysql->link);
			while($row = $mysql->fetch_array($query)){
				if($row['tipo_autorizacion'] == 'Autorizada'){
					$contAutorizaciones++;
				}
			}

			$autorizado = ($contAutorizadores == $contAutorizaciones)? 'true' : 'false';

			if($autorizado == 'true'){
				$Subject       = "Orden de Compra Autorizada";
				$mensaje_email = "La orden de compra que solicito ha sido autorizada";
				enviaEmailAutorizacion($id_documento,'solicitante',$id_empresa,$Subject,$mensaje_email,$mysql);
			}
			$sql = "UPDATE compras_ordenes SET autorizado = '$autorizado' WHERE id = '$id_orden_compra'";
			$query = $mysql->query($sql,$mysql->link);

 			echo '<script>
	 						document.getElementById("div_img_autorizacion_'.$id_empleado.'").innerHTML="<img style=\'height:11px;\' src=\'img/'.$tipo_autorizacion.'.png\' > '.$tipo_autorizacion.' ";
	 					</script>
	 					<img src="img/save_true.png" title="Guardar Autorizacion" onclick="autorizarOrdenCompra()">';
 		}
 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function ventanaAutorizaDocumentoArea($idDocumento,$opcGrillaContable,$id_sucursal,$id_empresa,$mysql){
 		$id_empleado = $_SESSION['IDUSUARIO'];
		$disabledSelect = '';

 		// CONSULTAR EL AREA DEL DOCUMENTO
 		$sql   = "SELECT id_area_solicitante,estado FROM compras_ordenes WHERE activo=1 ANd id_empresa=$id_empresa AND id=$idDocumento";
 		$query = $mysql->query($sql,$mysql->link);
		$id_area = $mysql->result($query,0,'id_area_solicitante');
		$estado  = $mysql->result($query,0,'estado');

		//Consultar las autorizaciones del documento
 		$sql="SELECT id,tipo_autorizacion,orden,id_empleado
 			FROM autorizacion_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa AND id_orden_compra=$idDocumento AND id_area=$id_area";
		$query = $mysql->query($sql, $mysql->link);
		while ($row = $mysql->fetch_array($query)) {
			$arrayAutorizaciones[$row['id_empleado']] = $row;
		}
		//Consultar los autorizadores del area
		$sql="SELECT id,id_empleado,documento_empleado,nombre_empleado,cargo,orden FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area ORDER BY orden ASC";
		$query=$mysql->query($sql,$mysql->link);
		while ($row = $mysql->fetch_array($query)) {
			$arrayAutorizadores[$row['id_empleado']] = $row;
		}

		//Consultar la autorizacion anterior
		$tipoAutorizacionUsuarioAnterior = '';
		foreach ($arrayAutorizaciones as $autorizacion) {
			if ($autorizacion['orden'] == ($arrayAutorizadores[$id_empleado]['orden'] - 1)) {
				$tipoAutorizacionUsuarioAnterior = $autorizacion['tipo_autorizacion'];
				break;
			}
		}

		foreach($arrayAutorizadores as $row){ //Para cada autorizador
			$selectAutorizacion = '';
			$contentAut         = '';
			$tipoAutorizacionEmpleado = $arrayAutorizaciones[$row['id_empleado']]['tipo_autorizacion'];
			$disabledSelect = ($tipoAutorizacionUsuarioAnterior <> 'Autorizada' && $row['orden']<>1)? 'disabled' : '';

			// SI NO ES EL USUARIO O SI EL DOCUMENTO NO ESTA GENERADO O ESTA CRUZADO O ELIMINADO, NO MOSTRAR LOS SELECT SI NO SOLO TEXTO
			if ($id_empleado<>$row['id_empleado'] || $estado<>1){
 				$contentAut = ($tipoAutorizacionEmpleado<>'')? '<img style="height:11px;" src="img/'.$tipoAutorizacionEmpleado.'.png" > '.$tipoAutorizacionEmpleado
 																				: '<span style="color:#A8A8A8;font-style:italic;">Sin Autorizacion</span>' ;
				$padding = '';
 			
			}else if ($id_empleado==$row['id_empleado']) {
				// SI ES EL USUARIO Y EL DOCUMENTO ESTA HABILITADO  MOSTRAR LOS SELECT PARA QUE PUEDAN REALIZAR EL PROCESO DE AUTORIZACION

 				$selectAutorizacion = "<select id='tipo_autorizacion_$row[id]' style='border:none;' onchange='autorizarOrdenCompraArea($row[id],$id_area,$row[orden])' $disabledSelect>
											<option value=''>Sin Autorizacion</option>
											<option value='Autorizada'>Autorizada</option>
											<option value='Aplazada'>Aplazada</option>
											<option value='Rechazada'>Rechazada</option>
										</select>";
				$padding = 'padding:1.5px;';
				if ($tipoAutorizacionEmpleado<>'') {
					$script .= "document.getElementById('tipo_autorizacion_$row[id]').value='".$tipoAutorizacionEmpleado."';";
				}
				$script .="console.log('".$tipoAutorizacionEmpleado."');";
 			}

 			$bodyUsuarios .="<div class='row' id='row_centro_costo_$row[documento_empleado]'>
	                           	<div class='cell' data-col='1'>$row[documento_empleado]</div>
        						<div class='cell' data-col='2'>$row[nombre_empleado]</div>
        						<div class='cell' data-col='3'>$row[cargo]</div>
        						<div class='cell' data-col='4'>$selectAutorizacion $contentAut</div>
	                        </div>";

 		}

 		$sql="SELECT documento,nombre,cargo FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
 		$query=$mysql->query($sql,$mysql->link);
		$documento_empleado = $mysql->result($query,0,'documento');
		$nombre_empleado    = $mysql->result($query,0,'nombre');
		$cargo              = $mysql->result($query,0,'cargo');

		?>

		<style>
		  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
		  .sub-content[data-position="right"]{width: 100%; }
		  .content-grilla-filtro { height: 175px; }
		  .content-grilla-filtro .cell[data-col="1"]{width: 90px;}
		  .content-grilla-filtro .cell[data-col="2"]{width: 200px;}
		  .content-grilla-filtro .cell[data-col="3"]{width: 145px;}
		  .content-grilla-filtro .cell[data-col="4"]{width: 115px;border-right: none;}
		  .sub-content [data-width="input"]{width: 150px;}
		  .content-grilla-filtro .row {height:30px;}
		</style>

		<div class="main-content">
		  <div class="sub-content" data-position="right">

		    <div class="title">AUTORIZACIONES REQUERIDAS</div>
		    <div class="content-grilla-filtro">
		      <div class="head">
		        <div class="cell" data-col="1">Documento</div>
		        <div class="cell" data-col="2">Usuario</div>
		        <div class="cell" data-col="3">Cargo</div>
		        <div class="cell" data-col="4">Autorizacion</div>
		      </div>
		      <div class="body" id="body_grilla_filtro_tercero">
		      	<?php echo $bodyUsuarios; ?>
		      </div>
		    </div>

		  </div>
		</div>
		<div id="loadAut" style="display: none;"></div>
			<script>
				<?php echo $script; ?>
			</script>
		<?php
 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function autorizarOrdenCompraArea($id_documento,$opcGrillaContable,$id_empresa,$tipo_autorizacion,$id_area,$orden,$mysql){
		$id_empleado = $_SESSION['IDUSUARIO'];

 		// CONSULTAR LA INFORMACION DEL EMPLEADO
 		$sql="SELECT documento,nombre,id_cargo,cargo,email_empresa FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado ";
 		$query=$mysql->query($sql,$mysql->link);

		$documento_empleado = $mysql->result($query,0,'documento');
		$nombre_empleado    = $mysql->result($query,0,'nombre');
		$email              = $mysql->result($query,0,'email_empresa');
		$id_cargo           = $mysql->result($query,0,'id_cargo');
		$cargo              = $mysql->result($query,0,'cargo');

 		// CONSULTAR QUIEN AUTORIZA Y EL ORDEN EN QUE LO HACEN
		$sql="SELECT id_empleado,orden FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area ORDER BY orden ASC";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) { $arrayAutorizadores[$row['orden']] = $row['id_empleado'];}
		print_r($arrayAutorizadores);
 		// CONSULTAR SI EL DOCUMENTO YA TIENE UNA AUTORIZACION POR PARTE DEL USURAIO
 		$sql="SELECT id FROM autorizacion_ordenes_compra_area
 				WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_orden_compra=$id_documento AND id_area=$id_area AND orden=$orden";
 		$query=$mysql->query($sql,$mysql->link);
 		$id_row = $mysql->result($query,0,'id');

 		// SI ES UNA ACTUALIZACION A LA AUTORIZACION
 		if ($id_row>0) {
 			$sql="UPDATE autorizacion_ordenes_compra_area SET tipo_autorizacion='$tipo_autorizacion'
 					WHERE activo=1 ANd id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_orden_compra=$id_documento AND orden=$orden AND id_area=$id_area";
 			$query=$mysql->query($sql,$mysql->link);
 		}
 		// SI SE INSERTA LA NUEVA AUTORIZACION
 		else{
 			$sql="INSERT INTO autorizacion_ordenes_compra_area (orden,id_empleado,documento_empleado,nombre_empleado,id_cargo,cargo,email,tipo_autorizacion,id_orden_compra,id_area,fecha,hora,id_empresa)
 					VALUES($orden,'$id_empleado','$documento_empleado','$nombre_empleado','$id_cargo','$cargo','$email','$tipo_autorizacion','$id_documento','$id_area',NOW(),NOW(),'$id_empresa') ";
 			$query=$mysql->query($sql,$mysql->link);
 		}

 		// SI SE REALIZO LA AUTORIZACION, ENTONCES
 		if ($query) {

 			// SI SE AUTORIZO EL DOCUMENTO, ENTONCES ENVIAR EMAIL AL SIGUIENTE EMPLEADO ENCARGADO PARA QUE REALICE SU RESPECTIVA AUTORIZACION
 			if ($tipo_autorizacion=='Autorizada') {
 				$id_empleado_sigt = 0;
 				if(array_key_exists($orden+1,$arrayAutorizadores)){
					$id_empleado_sigt = $arrayAutorizadores[$orden+1];
				}

 				// SI NO HAY MAS EMPLEADOS PARA AUTORIZAR, Y ESTA TODO AUTORIZADO, ENTONCES ACTUALIZAR EL DOCUMENTO COMO AUTORIZA
 				if ($id_empleado_sigt==0) {
					$sql   = "UPDATE compras_ordenes SET autorizado='true' WHERE id='$id_documento'";
					$query = $mysql->query($sql,$mysql->link);

					$Subject       = "Orden de Compra Autorizada";
					$mensaje_email = "La orden de compra que solicito ha sido autorizada";
 					enviaEmailAutorizacion($id_documento,'solicitante',$id_empresa,$Subject,$mensaje_email,$mysql);
				} else{
					$Subject       = "Orden de Compra Pendiente por Autorizacion";
					$mensaje_email = "Orden de compra a la espera de su Autorizacion";
 					enviaEmailAutorizacion($id_documento,$id_empleado_sigt,$id_empresa,$Subject,$mensaje_email,$mysql);
 				}
 			}
 			// SI EL DOCUMENTO NO FUE AUTORIZADO ENTONCES ENVIAR UNA NOTIFICACION AL SOLICITANTE INDICANDO QUE LA REQUISICION NO FUE APROBADA
 			else if ($tipo_autorizacion=='Rechazada') {
 				$sql   = "UPDATE compras_ordenes SET autorizado='false' WHERE id='$id_documento'";
				$query = $mysql->query($sql,$mysql->link);

 				$Subject       = "Orden de Compra Rechazada";
				$mensaje_email = "La orden de compra que solicito ha sido rechazada, comuniquese con el departamento de compras";
				enviaEmailAutorizacion($id_documento,'solicitante',$id_empresa,$Subject,$mensaje_email,$mysql);
 			}
 			// SI ESTA APLAZADA, NO AUTORIZADA, NO REALIZAR NINGUNA ACCION
 			else {
 				$sql   = "UPDATE compras_ordenes SET autorizado='false' WHERE id='$id_documento'";
				$query = $mysql->query($sql,$mysql->link);
				$Subject       = "Orden de Compra Aplazada";
				$mensaje_email = "La orden de compra que solicito ha sido aplazada, comuniquese con el departamento de compras";
				enviaEmailAutorizacion($id_documento,'solicitante',$id_empresa,$Subject,$mensaje_email,$mysql);
				echo '<script>MyLoading2("off")</script>';

 			}
 		}
 		else{
			echo '<script>MyLoading2("off",{icono:"fail",texto:"Error al autorizar intentelo de nuevo"})</script>';
 		}
 	}

 	//=========================== ENVIAR UN EMAIL CUANDO SE AUTORIZA O RECHAZA UNA ORDEN  DE COMPRA ========================================== //
 	function enviaEmailAutorizacion($id_documento,$id_empleado,$id_empresa,$Subject,$mensaje_email,$mysql){
 		// EVNIAR EMAIL A LOS ENCARGADOS DE AUTORIZAR LAS REQUISICIONES
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		$mail  = new PHPMailer();
		// echo $mail;
		$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
		$queryConexion = $mysql->query ($sqlConexion,$mysql->link);
		if($row_consulta = $mysql->fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_smtp'];
			$pass          = $row_consulta['password'];
			$user          = $row_consulta['correo'];
			$puerto        = $row_consulta['puerto'];
			$servidor      = $row_consulta['servidor'];
			$from          = $row_consulta['correo'];
			$autenticacion = $row_consulta['autenticacion'];
		}

		if ($user=='') {
			echo '<script>
					alert("No exite ninguna configuracion de correo SMTP!\nConfigure el correo desde el panel de control en el boton configuracion SMTP\nPara que se puedan enviar las notificaciones a las personas encargadas de autorizar el documento");
				</script>';
		}

		//CONSULTAR LA INFORMACION DE LA EMPRESA
		$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento,nit_completo,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular
						FROM empresas
						WHERE id='$id_empresa'
						LIMIT 0,1";
		$queryEmpresa = $mysql->query($sqlEmpresa,$mysql->link);

		$nombre_empresa        = $mysql->result($queryEmpresa,0,'nombre');
		$tipo_documento_nombre = $mysql->result($queryEmpresa,0,'tipo_documento_nombre');
		$nit_empresa		   = $mysql->result($queryEmpresa,0,'documento');
		$documento_empresa     = $mysql->result($queryEmpresa,0,'nit_completo');
		$ciudad                = $mysql->result($queryEmpresa,0,'ciudad');
		$direccion_empresa     = $mysql->result($queryEmpresa,0,'direccion');
		$razon_social          = $mysql->result($queryEmpresa,0,'razon_social');
		$telefonos             = $mysql->result($queryEmpresa,0,'telefono').' - '.$mysql->result($queryEmpresa,0,'celular');
		$actividad_economica   = $mysql->result($queryEmpresa,0,'actividad_economica');

		// CONSULTAR LA INFORMACION DEL DOCUMENTO
		$sql   = "SELECT
					consecutivo,
					sucursal,
					bodega,
					fecha_inicio,
					consecutivo,
					nit,
					proveedor,
					observacion,
					documento_usuario,
					usuario,
					id_area_solicitante,
					id_usuario
				FROM compras_ordenes WHERE id='$id_documento'";
		$query = $mysql->query($sql,$mysql->link);
		$consecutivo         = $mysql->result($query,0,'consecutivo');
		$sucursal            = $mysql->result($query,0,'sucursal');
		$bodega              = $mysql->result($query,0,'bodega');
		$fecha_inicio        = $mysql->result($query,0,'fecha_inicio');
		$consecutivo         = $mysql->result($query,0,'consecutivo');
		$nit                 = $mysql->result($query,0,'nit');
		$proveedor           = $mysql->result($query,0,'proveedor');
		$observacion         = $mysql->result($query,0,'observacion');
		$documento_usuario   = $mysql->result($query,0,'documento_usuario');
		$usuario             = $mysql->result($query,0,'usuario');
		$id_area_solicitante = $mysql->result($query,0,'id_area_solicitante');
		$id_usuario          = $mysql->result($query,0,'id_usuario');

		$mail->IsSMTP();
		$mail->SMTPAuth   = true;                  				// enable SMTP authentication
		$mail->SMTPSecure = $seguridad;                         // sets the prefix to the servier
		$mail->Host       = $servidor;      				    // sets GMAIL as the SMTP server
		$mail->Port       = $puerto;                            // set the SMTP port
		$mail->Username   = $user; // GMAIL username
		$mail->Password   = $pass; // GMAIL password
		$mail->From       = $from;
		$mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
		$mail->Subject    = $Subject;
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap
		$datos = base64_encode($id_documento .'|'. 
							   $consecutivo  .'|'. 
							   $sucursal 	 .'|'. 
							   $nit_empresa  .'|'. 
							   $id_empresa   .'|'.
							   $_SESSION['BD']
							);
		
		// SI SE DEBE ENVIAR LA NOTIFICACION A QUIEN SOLICITO LA REQUISICIONES
	    $serverRoot = ($_SERVER['SERVER_NAME'] == 'localhost')? "http://localhost/ERP":$_SERVER['SERVER_NAME'];
		if ($id_empleado=='solicitante') {
			$id_empleado = $id_usuario;
			$tableAutorizarOC = '';
		}else{
			$tableAutorizarOC = '<table>
									<tr>
										<td  font-family:tahoma,arial,verdana,sans-serif; font-size:32px; font-weight:bold; ">
											  <a href="'.$serverRoot.'/LOGICALERP/compras/ordenes_compra/bd/autorizacion_ordenes_correo/autorizar_ordenes_correo.php?data='.$datos.'" target="_blank" style="font-family:tahoma,arial,verdana,sans-serif; font-size:32px; font-weight:bold; ">Click aqui para autorizar</a>
										</td>
									</tr>
								</table>';
		}

		$body  = '<font color="black">
				<br>
				<b>'.$razon_social.'</b><br>
				<b>'.$tipo_documento_nombre.': </b>'.$documento_empresa.'<br>
				<b>Direccion: </b>'.$direccion_empresa.' - <b>'.$ciudad.' </b><br>
				<b>Telefono: </b>'.$telefonos.'<br>

				<br>

				<table>
					<tr>
						<td>Asunto: </td>
						<td>'.$mensaje_email.'</td>
					</tr>
					<tr>
						<td>Consecutivo</td>
						<td style="font-size:24px;font-weight:bold;">'.$consecutivo.'</td>
					</tr>
					<tr>
						<td>Bodega: </td>
						<td> '.$bodega.'</td>
					</tr>
					<tr>
						<td>Sucursal: </td>
						<td>'.$sucursal.'</td>
					</tr>
					<tr>
						<td>Proveedor: </td>
						<td>'.$nit.' - '.$proveedor.' </td>
					</tr>
					<tr>
						<td>Usuario Creador</td>
						<td>'.$documento_usuario.' - '.$usuario.' </td>
					</tr>
				</table>
				'.$tableAutorizarOC.'
				<br>
				<br>
				Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.
			</font><br>';

		$mail->Body = $body;
		$mail->MsgHTML($body);

		// CONSULTAR LAS DIRECCIONES DE EMAIL DE LOS ENCARGADOS DE AUTORIZAR EL DOCUMENTO
		$id_list =  (is_array($id_empleado))? "(".implode(',', $id_empleado).")" : "(".$id_empleado.")";
		$sql = "SELECT email_empresa FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id IN $id_list";
		$query=$mysql->query($sql,$mysql->link);

		if ($mysql->num_rows($query) <> 0){
			while($row = $mysql->fetch_array($query)){
				$mail->AddAddress($row['email_empresa']);
			}
			//Obtener el pdf
			include("imprimir_orden_compra.php");
									
			$pdfString = generarPDF($id_documento, $mysql);
			//var_dump($pdfString);
			$mail->addStringAttachment($pdfString,"orden_$consecutivo.pdf");
							
			$mail->IsHTML(true); // send as HTML
			if(!$mail->Send()){
				// echo $mail->ErrorInfo.'<script>alert("Se genero la autorizacion pero no se pudo enviar por email las notificaciones a los encargados de autorizar el documento\nSi el problema continua comuniquese con el administrador del sistema");</script>';

				echo '<script>MyLoading2("off",{icono:"fail",texto:"Se genero la autorizacion pero no se envio el email! verifique que todos tengan un email configurado"})</script>';

			}
			$mail->ClearAddresses();

			echo '<script>MyLoading2("off")</script>';
		}
		else{
			echo '<script>MyLoading2("off",{icono:"fail",texto:"El usuario no tiene el email configurado"})</script>';
		}
 	}

 	function UpdateFormaPago($id,$idFormaPago,$link){
		$sql   = "UPDATE compras_ordenes SET id_forma_pago = '$idFormaPago' WHERE id = '$id'";
		$query = mysql_query($sql,$link);

		if(!$query){
      $return =  '<script>
			        			alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");
			        		</script>';
		}
		echo $return;
	}
?>
