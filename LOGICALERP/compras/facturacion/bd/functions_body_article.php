<?php
	function cargaArticulosFacturaCompraSave($idFactura,$f_observacion,$estado,$link){
		$cont  = 0;
		$arrayColor = array('','#2b0af7','#000','#eb0af7','#faa96a','#889588','#330300','#54048a','#140af7','#0af7be','#737603','#2a0245','#f0f70a','#048080','#83048a','#0ccc00','#f7710a','#05024f','#033300','#ab39f8','#0af7f7','#510500','#8370ff','#060033','#c400cc','#f97aff','#996900','#008480','#b7fefc','#ffb25b','#a7a9a9');

		$sqlFactura    = "SELECT observacion FROM compras_facturas WHERE id='$idFactura' LIMIT 0,1";
		$queryFactura  = mysql_query($sqlFactura,$link);
		$f_observacion = mysql_result($queryFactura, 0, 'observacion');

		$sql  = "SELECT F.id,
						F.id_inventario,
						F.codigo,
						F.nombre,
						F.cantidad,
						F.costo_unitario,
						F.tipo_descuento,
						F.descuento,
						F.id_impuesto,
						F.impuesto,
						F.valor_impuesto,
		 				F.nombre_unidad_medida,
						F.cantidad_unidad_medida,
						F.opcion_gasto,
						F.opcion_costo,
						F.opcion_activo_fijo,
						F.check_opcion_contable,
						F.id_consecutivo_referencia AS ICR,
						F.consecutivo_referencia AS CR,
						F.nombre_consecutivo_referencia AS NCR,
						C.codigo AS centro_costo
					FROM compras_facturas_inventario AS F
					LEFT JOIN centro_costos AS C
					ON F.id_centro_costos = C.id
					WHERE F.id_factura_compra='$idFactura' AND F.activo = 1";

		//CREAR ARRAY PARA VALIDAR LAS CANTIDADES DE LAS ORDENES CARGADAS
		$sqlConfirmSaldo = "SELECT
								TI.id,
								TS.saldo_cantidad,
								TI.nombre_consecutivo_referencia
							FROM
								compras_facturas_inventario AS TI,
								compras_ordenes_inventario AS TS
							WHERE
								TI.id_factura_compra = '$idFactura'
							AND TI.activo = 1
							AND TS.id = TI.id_tabla_referencia
							GROUP BY
								TI.id";
		$queryConfirmSaldo = mysql_query($sqlConfirmSaldo,$link);

		while($rowConfirmSaldo = mysql_fetch_array($queryConfirmSaldo)){ $scriptOrdenes .= 'objDocumentosCruceFacturaCompra["'.$rowConfirmSaldo['id'].'"] ={typeDoc:"'.$rowConfirmSaldo['nombre_consecutivo_referencia'].'",saldo_cantidad: "'.$rowConfirmSaldo['saldo_cantidad'].'"};'; }


		$query = mysql_query($sql,$link);

		if($estado > 0){
			$columnaCcos = '<div class="labelCcos" title="C. Costo">C. Costo</div>';
		}

		$body = '<script>
					subtotalFacturaCompra           = 0.00;
					acumuladodescuentoArticulo      = 0.00;
					ivaFacturaCompra                = 0.00;
					retefuenteFacturaCompra         = 0.00;
					totalFacturaCompra              = 0.00;
					contArticulosFactura            = 0;
					arrayIvaFacturaCompra           = [];
					objDocumentosCruceFacturaCompra = [];
				</script>
				<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="headFacturaCompra">
							<div class="label" style="width:40px !important; border-left:none; padding-left:2px;"></div>
							<div class="label" title="Codigo/EAN">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label" title="Unidad">Unidad</div>
							<div class="label" title="Cantidad">Cantidad</div>
							<div class="label" title="Descuento">Descuento</div>
							<div class="label" title="Precio Unitario">Precio Unitario</div>
							<div class="label" title="Precio Total">Precio Total</div>
							<div class="labelCheck" title="Activo Fijo">A.F.</div>
							<div class="labelCheck" title="Costo">C.</div>
							<div class="labelCheck" title="Gasto de Venta" style="border-right: 1px solid #d4d4d4">G.V.</div>
							'.$columnaCcos.'
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulosFactura" onscroll="resizeHeadMyGrilla(this,\'headFacturaCompra\')">';


		if ($estado<'1') {
			while($row = mysql_fetch_array($query)){

				$row['cantidad']       = $row['cantidad'] * 1;
				$row['descuento']      = $row['descuento'] * 1;
				$row['costo_unitario'] = $row['costo_unitario'] * 1;

				$cont++;
				if($row['ICR'] > 0 && $row['NCR'] != '' && $docCruce !=  $row['NCR'].'_'.$row['ICR']){
					$contColor++;
					if($contColor == 29) $contColor = 1;

					$docCruce =  $row['NCR'].'_'.$row['ICR'];
					$color    = $arrayColor[$contColor];
				}
				else if($row['ICR'] == 0 || $row['ICR'] == ''){ $color = ''; }

				$body .= '<div class="bodyDivArticulosFactura" id="bodyDivArticulosFactura_'.$cont.'">
										'.cargaDivsUnidadesSave($cont,$color,$row['id'],$row['id_inventario'],$row['codigo'],$row['nombre'],$row['cantidad'],$row['costo_unitario'],$row['tipo_descuento'], $row['descuento'],$row['id_impuesto'],$row['impuesto'], $row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],$row['check_opcion_contable'],$row['opcion_costo'],$row['opcion_gasto'],$row['opcion_activo_fijo'],$row['NCR'],$row['ICR'],$row['CR']).'
									</div>';
			}
			$cont++;
			if(user_permisos(60,'true') == 'false'){
				$body .='<div class="bodyDivArticulosFactura" id="bodyDivArticulosFactura_'.$cont.'">
						'.cargaDivsUnidadesSave($cont).'
					</div>';
				$mostarBoton = 'enable()';
			}
		}
		else{
			while($row = mysql_fetch_array($query)){
				$cont++;

				if($row['ICR'] > 0 && $row['NCR'] != '' && $docCruce !=  $row['NCR'].'_'.$row['ICR']){
					$contColor++;
					if($contColor == 29) $contColor = 1;

					$docCruce =  $row['NCR'].'_'.$row['ICR'];
					$color    = $arrayColor[$contColor];
				}
				else if($row['ICR'] == 0 || $row['ICR'] == ''){ $color = ''; }

				$row['cantidad']       = $row['cantidad'] * 1;
				$row['descuento']      = $row['descuento'] * 1;
				$row['costo_unitario'] = $row['costo_unitario'] * 1;

				$body .='<div class="bodyDivArticulosFactura" id="bodyDivArticulosFactura_'.$cont.'">
							'.cargaDivsUnidadesBloqueadas($cont, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'],$row['id_impuesto'],$row['impuesto'], $row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],$row['check_opcion_contable'],$row['centro_costo'],$row['NCR'],$row['ICR'],$row['CR'],$color).'
						</div>';
			}
			$mostarBoton='disable()';
		}

		$contenidoEditarValores='';
		if (user_permisos(89,'false') == 'true' && $estado==0) {
			$contenidoEditarValores = '<div style="float:left;margin-top:1px;margin: 1px 0 0 -21px;cursor:pointer;" onclick="abrirVentanaUpdateValoresFacturaCompra()" id="imgAjusteFactura" title="Editar Valores Totales">
	                    				   <img src="img/config16.png" style="margin: 2px 0 0 2px;"/>
	                    				</div>';
		}

		$styleObs = ($estado<1)? 'onKeydown="inputObservacionFacturaCompra(event,this)"': 'readonly';
		$body .='	</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_facturas_compras">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacionFactura"><b>OBSERVACIONES</b></div>
						<textarea id="observacionFacturaCompra" '.$styleObs.'></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalFacturaCompra">0</div>
						</div>
						<div class="renglon" style="overflow:visible; height:auto;">
							<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIvaFacturaCompra">Iva</div>
							<div class="labelSimbolo" id="simboloIvaFacturaCompra">$</div>
							<div class="labelTotal" id="ivaFacturaCompra" >0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesFacturaCompra" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px;font-weight:bold; overflow:visible;" id="idretencionFacturaCompra"> </div>
							<div class="labelSimbolo" id="simboloRetencionFacturaCompra"></div>
							<div class="labelTotal" style="height:auto; overflow:visible;" id="retefuenteFacturaCompra"></div>
						</div>
						<div class="renglon renglonTotal">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL</div>
							'.$contenidoEditarValores.'
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalFacturaCompra">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulosFactura"),\'headFacturaCompra\');

					'.$scriptOrdenes.'
					contArticulosFactura='.$cont.';
					// document.getElementById("observacionFacturaCompra").value="";
					Ext.getCmp("Btn_cancelar_FacturaCompra").enable();
				</script>';

		return $body;
	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$color = '',$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento = '',$descuento = 0,$id_impuesto = 0,$impuesto = '', $valor_impuesto = 0,$estado = '',$nombre_unidad = '',$cantidad_unidad = '',$check_opcion_contable = '',$opcion_costo = '',$opcion_gasto = '',$opcion_activo_fijo = '',$NCR = '',$ICR = '',$CR = ''){
		//LA VARIABLE NCR ES EL nombre_consecutivo_referencia, SI ES orden de compra ENTONCES NO MOSTRAMOS EL BOTON BUSCAR ARTICULO, Y QUITAMOS EL EVENTO DE BUSCAR ARTICULO DEL CAMPO EAN
		$eventoEan         = 'onKeyup="buscarArticuloFactura(event,this);"';
		$btnBuscarArticulo = '<div onclick="ventanaBuscarArticuloFactura('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo" style="'.$mostrarImagen.'">
									<img src="img/buscar20.png"/>
								</div>';

		if ($NCR=='Orden de Compra' || $NCR=='Entrada de Almacen') {
			$eventoEan         = '';
			$btnBuscarArticulo = '';
			$readonlyCampo     = 'readonly';
		}

		$scriptColor = '';
		if($color == ''){
			$color       = '#b70c00';
			$scriptColor = '';
		}
		else{
			$titleBtnDelete = '('.substr($NCR, 0, 1).' '.$CR.')';
			$scriptColor    = 'document.getElementById("btnFacturaCompra_'.substr($NCR, 0, 1).'_'.$ICR.'").style.backgroundColor="'.$color.'"';
		}

		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';
		$srcImgDescuento  = 'img/pesos.png';
		$titleDescuento	  = 'En pesos';
		$descuento2 	  = 0;

		//verificar si la factura esta cerrada para deshabiltar los campos y ocultar las imagenes
		$mostrarImagen            = '';
		$deshabiltar              = '';
		$eventoTipoDesc           = ' onclick="tipoDescuentoArticulo('.$cont.')"';
		$idArticuloFactura        = $id_inventario;
		$idInsertArticuloFactura  = $id;
		$ivaArticuloFacturaCompra = $valor_impuesto;

		$valueUnidadMedida = $nombre_unidad.' x '.$cantidad_unidad;

		//calcular subtotal del articulo
		$subtotal=$cantidad*$costo_unitario;

		if($id_inventario == ''){
			$srcImg            = 'img/save_true.png';
			$displayBtnReload  = '';
			$displayBtns       = 'display:none';
			$valueUnidadMedida = '';
		}
		//verificar que descuento tiene el articulo si en pesos o en porcentaje
		if ($tipoDescuento=='porcentaje') {
			$srcImgDescuento  = 'img/porcentaje.png';
			$titleDescuento	  = 'En porcentaje';
		}
		else{ $descuento2 = $descuento; }

		//OPCION CONTABLE
		$check_contable_activo = '';
		$check_contable_costo  = '';
		$check_contable_gasto  = '';

		if($opcion_activo_fijo == 'true' ){
			// echo "<script>console.log('check opcion contable $check_opcion_contable');</script>";
			$checkedOpcionContable = ($check_opcion_contable == 'activo_fijo')? 'checked': '';
			$check_contable_activo = '<input type="checkbox" id="check_factura_activo_fijo_'.$cont.'" class="optionCheckContable_'.$cont.'" '.$checkedOpcionContable.' onchange="changeCheckOptionContable('.$cont.',this)" '.(($NCR=='Entrada de Almacen')? 'disabled ' : '' ).'/>';
		}
		if($opcion_gasto == 'true' && $NCR<>'Entrada de Almacen'){
			$checkedOpcionContable = ($check_opcion_contable == 'gasto')? 'checked': '';
			$check_contable_gasto  = '<input type="checkbox" id="check_factura_gasto_'.$cont.'" class="optionCheckContable_'.$cont.'" '.$checkedOpcionContable.' onchange="changeCheckOptionContable('.$cont.',this)"/>';
		}
		if($opcion_costo == 'true' && $NCR<>'Entrada de Almacen'){
			$checkedOpcionContable = ($check_opcion_contable == 'costo')? 'checked': '';
			$check_contable_costo  = '<input type="checkbox" id="check_factura_costo_'.$cont.'" class="optionCheckContable_'.$cont.'" '.$checkedOpcionContable.' onchange="changeCheckOptionContable('.$cont.',this)"/>';
		}

		$body ='<div class="label" style="width:40px !important; border-left:none; padding-left:2px; overflow:hidden;">
					<div style="float:left; margin-top:3px;">'.$cont.'</div>
					<div style="float:right; width:18px; overflow:hidden;" id="renderArticuloFactura_'.$cont.'"></div>
				</div>

				<div class="campo">
					<input type="text" id="eanArticuloFactura_'.$cont.'" '.$eventoEan.' style="float:left;" value="'.$codigo.'" '.$readonlyCampo.'/>
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticuloFactura_'.$cont.'" readonly style="text-align:left;" value="'.$nombre.'"/></div>
				'.$btnBuscarArticulo.'

				<div class="campo"><input type="text" id="unidadesFactura_'.$cont.'" style="text-align:left" value="'.$valueUnidadMedida.'" readonly/></div>
				<div class="campo"><input type="text" id="cantArticuloFactura_'.$cont.'" value="'.$cantidad.'"/></div>

				<div class="campo campoDescuento">
					<div '.$eventoTipoDesc.' id="tipoDescuentoArticulo_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo_'.$cont.'" />
					</div>
					<input type="text" id="descuentoArticuloFactura_'.$cont.'" value="'.$descuento.'" />
				</div>

				<div class="campo"><input type="text" id="costoArticuloFactura_'.$cont.'" onKeyup="guardarAutoFactura(event,this,'.$cont.');" value="'.$costo_unitario.'" '.$readonlyCampo.'/></div>
				<div class="campo"><input type="text" id="costoTotalArticuloFactura_'.$cont.'"  readonly/></div>

				<div class="campoOptionCheck" id="div_check_factura_activo_fijo_'.$cont.'">'.$check_contable_activo.'</div>
				<div class="campoOptionCheck" id="div_check_factura_costo_'.$cont.'">'.$check_contable_costo.'</div>
				<div class="campoOptionCheck" id="div_check_factura_gasto_'.$cont.'" style="border-right: 1px solid #d4d4d4;">'.$check_contable_gasto.'</div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticuloFactura('.$cont.');" id="divImageSaveFactura_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveFactura_'.$cont.'"/></div>
					<div onclick="retrocederArticuloFactura('.$cont.')" id="divImageDeshacer_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticuloFactura('.$cont.');" id="descripcionArticuloFactura_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="img/edit.png"/></div>
					<!--
					<div onclick="deleteArticuloFactura('.$cont.');" id="deleteArticuloFactura_'.$cont.'" '.$titleBtnDelete.' style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="img/delete.png" /></div>
					-->
					<div onclick="deleteArticuloFactura('.$cont.')" id="deleteArticuloFactura_'.$cont.'" title="Eliminar Articulo '.$titleBtnDelete.'" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;">
						<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; background-color:'.$color.'; margin:1px;">
                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                        </div>
					</div>

				</div>

				<input type="hidden" id="idArticuloFactura_'.$cont.'" value="'.$idArticuloFactura.'" />
				<input type="hidden" id="idInsertArticuloFactura_'.$cont.'" class="classInputInsertArticuloFactura" value="'.$idInsertArticuloFactura.'" />
				<input type="hidden" id="ivaArticuloFacturaCompra_'.$cont.'" value="'.$id_impuesto.'" />
				<script>

					if (typeof(arrayIvaFacturaCompra['.$id_impuesto.'])=="undefined") {
						arrayIvaFacturaCompra['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}

					document.getElementById("cantArticuloFactura_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloFactura(event,this); };
					document.getElementById("descuentoArticuloFactura_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloFactura(event,this); };

					calcularValoresFactura("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');

					'.$scriptColor.'

				</script>';

		return $body;
	}
	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadas($cont,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento = '',$descuento  =  0,$id_impuesto = 0,$impuesto = '', $valor_impuesto = 0,$estado = '',$nombre_unidad = '',$cantidad_unidad = '',$check_opcion_contable = '',$centro_costo  =  '',$NCR = '',$ICR = '',$CR = '',$color = ''){

		//OPCION CONTABLE
		if($check_opcion_contable == 'activo_fijo'){
			$check_contable_activo = '<input type="checkbox" id="check_factura_activo_fijo_'.$cont.'" class="optionCheckContable_'.$cont.'" checked onchange="this.checked=true;"/>';
		}
		if($check_opcion_contable == 'gasto'){
			$check_contable_gasto = '<input type="checkbox" id="check_factura_gasto_'.$cont.'" class="optionCheckContable_'.$cont.'" checked onchange="this.checked=true;"/>';
		}
		if($check_opcion_contable == 'costo'){
			$check_contable_costo = '<input type="checkbox" id="check_factura_costo_'.$cont.'" class="optionCheckContable_'.$cont.'" checked onchange="this.checked=true;"/>';
		}

		$scriptColor = '';
		if($color == ''){
			$color       = '#b70c00';
			$scriptColor = '';
		}
		else{
			$titleBtnDelete = '('.substr($NCR, 0, 1).' '.$CR.')';
			//doc_'.$row['string_cruce'].' '.$row['numero_cruce']

			$scriptColor   .=  'document.getElementById("OCFacturaCompra_'.$CR.'").style.color="'.$color.'";
								document.getElementById("OCFacturaCompra_'.$CR.'").style.fontWeight ="bold";
						   		document.getElementById("documentoCruceFactura_'.$cont.'").style.backgroundColor="'.$color.'";
						   		document.getElementById("documentoCruceFactura_'.$cont.'").title = "'.$titleBtnDelete.'";';

		    $columnaCruce  = '<div class="campoDocumentoCruzado"  id="documentoCruceFactura_'.$cont.'" title=""></div>';

		}


		$body ='<div class="label" style="width:40px !important; border-left:none; padding-left:2px;">
					<div style="float:left; margin-top:3px;">'.$cont.'</div>
					<div style="float:left; width:18px" id="renderArticuloFactura_'.$cont.'"></div>
				</div>

				<div class="campo">
					<input type="text" id="eanArticuloFactura_'.$cont.'" readonly  style="float:left;" value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticuloFactura_'.$cont.'" readonly style="text-align:left;" readonly value="'.$nombre.'"/></div>

				<div class="campo"><input type="text" id="unidadesFactura_'.$cont.'" style="text-align:left" value="'.$nombre_unidad.' x '.$cantidad_unidad.'" readonly/></div>
				<div class="campo"><input type="text" id="cantArticuloFactura_'.$cont.'" value="'.$cantidad.'" readonly  /></div>

				<div class="campo campoDescuento">
					<div id="tipoDescuentoArticulo_'.$cont.'" title="En '.$tipoDescuento.'">
						<img src="img/'.$tipoDescuento.'.png" id="imgDescuentoArticulo_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticuloFactura_'.$cont.'" value="'.$descuento.'" readonly/>
				</div>

				<div class="campo" ><input type="text" id="costoArticuloFactura_'.$cont.'" readonly  value="'.$costo_unitario.'"/></div>
				<div class="campo"><input type="text" id="costoTotalArticuloFactura_'.$cont.'" readonly/></div>

				<div class="campoOptionCheck" id="div_check_factura_activo_fijo_'.$cont.'">'.$check_contable_activo.'</div>
				<div class="campoOptionCheck" id="div_check_factura_costo_'.$cont.'">'.$check_contable_costo.'</div>
				<div class="campoOptionCheck" id="div_check_factura_gasto_'.$cont.'" style="border-right: 1px solid #d4d4d4;">'.$check_contable_gasto.'</div>
				<div class="campoCcos" ><input type="text" id="centroCostoFactura_'.$cont.'" readonly  value="'.$centro_costo.'"/></div>
				'.$columnaCruce.'
				<input type="hidden" id="idInsertArticuloFactura_'.$cont.'" class="classInputInsertArticuloFactura" value="'.$id_inventario.'" />

				<script>
					'.$scriptColor.'
					if (typeof(arrayIvaFacturaCompra['.$id_impuesto.'])=="undefined") {
						arrayIvaFacturaCompra['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}

					calcularValoresFactura("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');
				</script>';

		return $body;
	}
?>
