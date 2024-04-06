<?php

	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

		$scriptRemision          = ''; 						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision = '';
		$idRemision            = '';

		//CONSULTAR SI EL TERCERO ES EXCENTO DE IVA
		$sql   = "SELECT T.id,DV.exento_iva,DV.observacion FROM terceros AS T, $tablaPrincipal AS DV WHERE T.id=DV.id_cliente AND DV.id=$id";
		$query = mysql_query($sql,$link);

		$exento_iva = mysql_result($query,0,'exento_iva');
		if($observacion == ''){ $observacion = mysql_result($query,0,'observacion'); }

		$arrayReplaceString = array("\n", "\r","<br>");
		$observacionFactura = str_replace($arrayReplaceString, " ", $observacion);

		if ($opcGrillaContable=="FacturaVenta" || $opcGrillaContable=='RemisionesVenta' || $opcGrillaContable=='PedidoVenta'){
			$arrayColor = array('','#2b0af7','#000','#eb0af7','#faa96a','#889588','#330300','#54048a','#140af7','#0af7be','#737603','#2a0245','#f0f70a','#048080','#83048a','#0ccc00','#f7710a','#05024f','#033300','#ab39f8','#0af7f7','#510500','#8370ff','#060033','#c400cc','#f97aff','#996900','#008480','#b7fefc','#ffb25b','#a7a9a9');

			$orderBy = 'ORDER BY id ASC';

			if($opcGrillaContable=="FacturaVenta"){
				$orderBy = 'ORDER BY NCR ASC, ICR ASC';
				// CARGAR LOS GRUPOS DE ITEMS
			}

			// CONDICION DE LOS ITEMS DE LAS RECETAS PARA REMISIONES Y FACTURAS
			if ($opcGrillaContable=="FacturaVenta" || $opcGrillaContable=="RemisionesVenta") {
				$whereItems = " AND id_fila_item_receta = 0";
			}

			$sql   = "SELECT id,
						id_inventario,
						codigo,
						nombre,
						cantidad,
						costo_unitario,
						tipo_descuento,
						descuento,
						id_impuesto,
						impuesto,
						valor_impuesto,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						id_consecutivo_referencia AS ICR,
						consecutivo_referencia AS CR,
						nombre_consecutivo_referencia AS NCR
					FROM $tablaInventario
					WHERE $idTablaPrincipal='$id' AND activo = 1 $whereItems
					$orderBy";
			$query = mysql_query($sql,$link);
			
			$sqlConfirmSaldo = "SELECT TI.id,TS.saldo_cantidad
								FROM $tablaInventario AS TI, ventas_pedidos_inventario AS TS
								WHERE TI.$idTablaPrincipal='$id' AND TI.activo = 1 AND TI.nombre_consecutivo_referencia='Pedido' AND TS.id=TI.id_tabla_inventario_referencia
								GROUP BY TI.id";
			$queryConfirmSaldo = mysql_query($sqlConfirmSaldo,$link);
			while($rowConfirmSaldo = mysql_fetch_array($queryConfirmSaldo)){ $scriptRemision .= 'objDocumentosCruce'.$opcGrillaContable.'["'.$rowConfirmSaldo['id'].'"] ={typeDoc:"Pedido",saldo_cantidad:"'.$rowConfirmSaldo['saldo_cantidad'].'"};'; }

			if ($opcGrillaContable=='FacturaVenta') {
				$sqlConfirmSaldo = "SELECT TI.id,TS.saldo_cantidad
								FROM $tablaInventario AS TI, ventas_remisiones_inventario AS TS
								WHERE TI.$idTablaPrincipal='$id' AND TI.activo = 1 AND TI.nombre_consecutivo_referencia='Remision' AND TS.id=TI.id_tabla_inventario_referencia
								GROUP BY TI.id";
				$queryConfirmSaldo = mysql_query($sqlConfirmSaldo,$link);
				while($rowConfirmSaldo = mysql_fetch_array($queryConfirmSaldo)){ $scriptRemision .= 'objDocumentosCruce'.$opcGrillaContable.'["'.$rowConfirmSaldo['id'].'"] ={typeDoc:"Remision",saldo_cantidad: "'.$rowConfirmSaldo['saldo_cantidad'].'"};'; }
			}

		}
		else{
			$sql   = "SELECT id,id_inventario,codigo,nombre,cantidad,costo_unitario,tipo_descuento,descuento,id_impuesto,impuesto,valor_impuesto,nombre_unidad_medida,cantidad_unidad_medida
					FROM $tablaInventario
					WHERE $idTablaPrincipal='$id' AND activo = 1";
			$query = mysql_query($sql,$link);
		}

		$body = '<script>
					subtotalAcumulado'.$opcGrillaContable.'          = 0;
					descuentoAcumulado'.$opcGrillaContable.'         = 0;
					descuento'.$opcGrillaContable.'                  = 0;
					acumuladodescuentoArticulo'.$opcGrillaContable.' = 0;
					ivaAcumulado'.$opcGrillaContable.'               = 0;
					total'.$opcGrillaContable.'                      = 0;
					contArticulos'.$opcGrillaContable.'              = 0;
					arrayIva'.$opcGrillaContable.'                   = [];
					objDocumentosCruce'.$opcGrillaContable.'         = [];
				</script>

				<div class="contenedorGrilla">
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
				<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		$readonly  = '';
		$cont      = 0;
		$contColor = 0;
		$docCruce  = '';

		if ($opcGrillaContable=="FacturaVenta" || $opcGrillaContable=="RemisionesVenta" || $opcGrillaContable=='PedidoVenta') {
			if ($estado == 0) {
				while($row = mysql_fetch_array($query)){
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'];
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'];
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'];

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
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesSave($cont,$opcGrillaContable,$color, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'], $row['id_impuesto'],$row['impuesto'],$row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],$row['NCR'],$row['ICR'],$row['CR']).'
							</div>';
				}
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSave($cont,$opcGrillaContable).'
						</div>';
				$mostarBoton='enable()';
				$deshabilita='';
				$eventoObservacionGrilla="onKeydown=inputObservacion".$opcGrillaContable."(event,this)";

			}
			else{
				while($row = mysql_fetch_array($query)){
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'];
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'];
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'];

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
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'], $row['id_impuesto'],$row['impuesto'],$row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],$row['NCR'],$row['ICR'],$row['CR'],$color).'
							</div>';
				}
				$mostarBoton='disable()';
				$deshabilita='readonly';
			}
		}
		else{
			// SI LA COTIZACION NO HA SIDO CEERRADA POR UNA FACTURA CARGA INPUTS READONLY
			if ($estado < 1) {
				while($row = mysql_fetch_array($query)){
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'];
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'];
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'];

					$row['cantidad']       = $row['cantidad'] * 1;
					$row['descuento']      = $row['descuento'] * 1;
					$row['costo_unitario'] = $row['costo_unitario'] * 1;

					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesSave($cont,$opcGrillaContable,'', $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'], $row['id_impuesto'],$row['impuesto'],$row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],'','','').'
							</div>';
				}
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSave($cont,$opcGrillaContable).'
						</div>';

				$deshabilita             = '';
				$mostarBoton             = ($estado=='1')? 'disable()': 'enable()';
				$eventoObservacionGrilla ="onKeydown=inputObservacion".$opcGrillaContable."(event,this)";


			}
			else{

				while($row = mysql_fetch_array($query)){
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'];
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'];
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'];

					$row['cantidad']       = $row['cantidad'] * 1;
					$row['descuento']      = $row['descuento'] * 1;
					$row['costo_unitario'] = $row['costo_unitario'] * 1;

					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'],$row['id_impuesto'],$row['impuesto'], $row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida']).'
							</div>';
				}
				$mostarBoton='disable()';
				$deshabilita='readonly';
			}
		}

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
				$acumScript = cargaDivsGruposItems($id,'ventas_facturas_grupos',$opcGrillaContable,$estado,$_SESSION['EMPRESA'],$link);
				break;

			default:
				$typeDocument = '';
				break;
		}

		$body .='	</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'" '.$eventoObservacionGrilla.' '.$deshabilita.'></textarea>
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
							<div class="labelTotal" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'">
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"> </div>
							<div class="labelSimbolo"  id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelTotal" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
						</div>

						<div class="renglon renglonTotal">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL '.$typeDocument.'</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');

					'.$scriptRemision.'
					'.$acumScript.'
					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacionFactura.'";
					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
				</script>';

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$color='',$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento='',$descuento = 0, $id_impuesto=0,$impuesto='',$valor_impuesto=0, $estado='',$nombre_unidad='',$cantidad_unidad='',$NCR='',$ICR='',$CR=''){
		//LA VARIABLE NCR ES EL nombre_consecutivo_referencia, SI ES Remision ENTONCES NO MOSTRAMOS EL BOTON BUSCAR ARTICULO, Y QUITAMOS EL EVENTO DE BUSCAR ARTICULO DEL CAMPO EAN
		$eventoEan         = 'onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);"';
		$btnBuscarArticulo = '<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo" style="'.$mostrarImagen.'">
								<img src="img/buscar20.png"/>
							</div>';

		if ($NCR=='Remision' || $NCR=='Pedido') {
			$eventoEan         = '';
			$btnBuscarArticulo = '';
		}

		$readonly_ean='';

		if ($NCR=='Remision' || $NCR=='Pedido' || $NCR == 'Cotizacion') {
			$readonly_ean='readonly';
		}

		$readonly_precio='';
		if(user_permisos(61,'false') == 'false'){
			$readonly_precio='readonly';
		}
		$readonly_descuento='';
		if(user_permisos(76,'false') == 'false'){
			$readonly_descuento='readonly';
		}

		$readonly_precio_remision_adjunta='';
		if(user_permisos(112,'false') == 'false' && $NCR=='Remision'){
			$readonly_precio_remision_adjunta='readonly';
		}
		$readonly_descuento_remision_adjunta='';
		if(user_permisos(113,'false') == 'false' && $NCR=='Remision'){
			$readonly_descuento_remision_adjunta='readonly';
		}

		$scriptColor = '';
		if($color == ''){
			$color       = '#b70c00';
			$scriptColor = '';
		}
		else{
			$titleBtnDelete = '('.substr($NCR, 0, 1).' '.$CR.')';
			$scriptColor    = 'try{
									document.getElementById("btn'.$opcGrillaContable.'_'.substr($NCR, 0, 1).'_'.$ICR.'").style.backgroundColor="'.$color.'"
								}
								catch(error) {
									console.warn(error)
								}';
		}

		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		$srcImgDescuento  = 'img/porcentaje.png';
		$titleDescuento	  = 'En porcentaje';
		$descuento2 	  = 0;
		//verificar si la factura esta cerrada para deshabiltar los campos y ocultar las imagenes

		$mostrarImagen     ='';
		$deshabiltar       ='';
		$eventoTipoDesc    =' onclick="tipoDescuentoArticulo('.$cont.')"';
		$idArticuloFactura =$id_inventario;

		$id_impuesto=($id_impuesto=='')? 0 : $id_impuesto ;
		$idInsertArticuloFactura  =$id;
		$ivaArticuloFacturaCompra =$id_impuesto;

		//calcular subtotal del articulo
		$subtotal=$cantidad*$costo_unitario;
		// var_dump((int)$costo_unitario != $costo_unitario);
		if($id_inventario == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}


		//verificar que descuento tiene el articulo si en pesos o en porcentaje
		if ($tipoDescuento=='pesos') {
			$srcImgDescuento  = 'img/pesos.png';
			$titleDescuento	  = 'En  pesos';
		}
		else{ $descuento2 = $descuento; }

		if ($nombre_unidad != '' && $cantidad_unidad!='') {
			$mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad;
		}

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' '.$eventoEan.'  value="'.$codigo.'" '.$readonly_ean.'/>
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.' value="'.$nombre.'" /></div>
				'.$btnBuscarArticulo.'

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="'.$mostrarUnidad.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$cantidad.'" '.$deshabiltar.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo campoDescuento">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$descuento.'" '.$readonly_descuento.' '.$readonly_descuento_remision_adjunta.' '.$deshabiltar.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' '.$readonly_precio.'  '.$readonly_precio_remision_adjunta.' value="'.$costo_unitario.' '.is_float($costo_unitario+0).' " onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.'   readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="img/edit.png"/></div>

					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo '.$titleBtnDelete.'" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;">
						<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; background-color:'.$color.'; margin:1px;">
                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                        </div>
					</div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idArticuloFactura.'" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idInsertArticuloFactura.'" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id_impuesto.'">

				<script>
					// console.log("i: '.$impuesto.' -  val: '.$valor_impuesto.' id: '.$id_impuesto.'");
					if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
						arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}
					//llamamos la funcion para generar los calculos de la factura
					calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');
					'.$scriptColor.'
					'.showIngredients($idInsertArticuloFactura,$cont).'
				</script>';

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento='',$descuento = 0, $id_impuesto=0,$impuesto='',$valor_impuesto=0,$estado='',$nombre_unidad='',$cantidad_unidad='',$NCR='',$ICR='',$CR='',$color=''){
		$srcImgDescuento  = 'img/pesos.png';

		if ($tipoDescuento=='porcentaje') {
			$srcImgDescuento  = 'img/porcentaje.png';
			$titleDescuento	  = 'En porcentaje';
		}
		//mostrar las unidades

		$scriptColor = '';
		if($color == ''){
			$color       = '#b70c00';
			$scriptColor = '';
		}
		else{
			//echo '<script>console.log("'.$color.'");</script>';
			$titleBtnDelete = '('.substr($NCR, 0, 1).' '.$CR.')';
			//doc_'.$row['string_cruce'].' '.$row['numero_cruce']
			if($opcGrillaContable == 'FacturaVenta'){

				$scriptColor   .=  '
									document.getElementById("label'.$opcGrillaContable.'_'.substr($NCR, 0, 1).'_'.$CR.'").style.color="'.$color.'";
									document.getElementById("label'.$opcGrillaContable.'_'.substr($NCR, 0, 1).'_'.$CR.'").style.fontWeight ="bold";
							   		document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").style.backgroundColor="'.$color.'";
							   		document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").title = "'.$titleBtnDelete.'";';

		   	}

		}

		$columnaCruzados = '';

		if($opcGrillaContable == 'FacturaVenta' && $color != '#b70c00'){
			$columnaCruzados = '<div class="campoDocumentoCruzado"  id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" title=""></div>';
		}

		//echo '<script>console.log("'.$color.' '.$NCR.' '.$CR.'");</script>';

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input readonly type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'"   value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly  value="'.$nombre.'" /></div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$nombre_unidad.' x '.$cantidad_unidad.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$cantidad.'"  readonly /></div>

				<div class="campo campoDescuento">
					<div id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$descuento.'" readonly/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'"  value="'.$costo_unitario.'" readonly  value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"    readonly/></div>
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />
				'.$columnaCruzados.'

				<div style="float:right; min-width:80px;display:none;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="img/edit.png"/></div>

					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo '.$titleBtnDelete.'" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;">
						<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; background-color:'.$color.'; margin:1px;">
                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                        </div>
					</div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idInsertArticuloFactura.'" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id_impuesto.'">

				<script>
					'.$scriptColor.'
					// console.log("i: '.$impuesto.' -  val: '.$valor_impuesto.' id: '.$id_impuesto.'");
					if (typeof(arrayIva'.$opcGrillaContable.'["'.$id_impuesto.'"])=="undefined") {
						arrayIva'.$opcGrillaContable.'["'.$id_impuesto.'"]={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}
					//llamamos la funcion para generar los calculos de la factura
					calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');

					'.(($opcGrillaContable == 'FacturaVenta')? showIngredients($id,$cont,1) : "" ).'
				</script>';
		return $body;
	}

	function showIngredients($id_fila_item_receta,$cont,$estado=0) {
		if ($id_fila_item_receta==0) { return; }
		global $mysql,$tablaInventario,$idTablaPrincipal,$id_factura_venta,$opcGrillaContable;
		$sql   = "SELECT
					id,
					id_inventario,
					codigo,
					nombre,
					cantidad,
					costo_unitario,
					tipo_descuento,
					descuento,
					id_impuesto,
					impuesto,
					valor_impuesto,
					nombre_unidad_medida,
					cantidad_unidad_medida
					FROM $tablaInventario WHERE /*$idTablaPrincipal=$id AND*/ id_fila_item_receta=$id_fila_item_receta";
		$query = $mysql->query($sql,$mysql->link);
		$num_ing = $mysql->num_rows($query);
		if ($num_ing>0) {
			$contIng = $cont+0.01;
			while ($row=$mysql->fetch_array($query)) {
				$id_row                 = $row['id'];
				$id_inventario          = $row['id_inventario'];
				$codigo                 = $row['codigo'];
				$nombre                 = $row['nombre'];
				$cantidad               = $row['cantidad'];
				$costo_unitario         = $row['costo_unitario'];
				$tipo_descuento         = $row['tipo_descuento'];
				$descuento              = $row['descuento'];
				$id_impuesto            = $row['id_impuesto'];
				$impuesto               = $row['impuesto'];
				$valor_impuesto         = $row['valor_impuesto'];
				$nombre_unidad_medida   = $row['nombre_unidad_medida'];
				$cantidad_unidad_medida = $row['cantidad_unidad_medida'];
				$unidad_medida          = "$nombre_unidad_medida x $cantidad_unidad_medida";
				$total_ingrediente      = $cantidad*$costo_unitario;

				if ($estado==1) {
					$readonly = "readonly";
				}
				else{
					$btnBuscar = "<div onclick='ventanaBuscarArticulo$opcGrillaContable($contIng);' title='Buscar Articulo' class='iconBuscarArticulo'>
										<img src='img/buscar20.png'>
									</div>";
					$btnsRows = "<div style='float:right; min-width:80px;'>
										<div onclick='guardarNewArticulo$opcGrillaContable($contIng)' id='divImageSave$opcGrillaContable"."_$contIng' title='Actualizar Articulo' style='width: 20px; float: left; margin-top: 3px; cursor: pointer; display: none;'><img src='img/reload.png' id='imgSaveArticulo$opcGrillaContable"."_$contIng'></div>
										<div onclick='retrocederArticulo$opcGrillaContable($contIng)' id='divImageDeshacer$opcGrillaContable"."_$contIng' title='Deshacer Cambios' style='width:20px; float:left; margin-top:3px;cursor:pointer;display:none'><img src='img/deshacer.png' id='imgDeshacerArticulo$opcGrillaContable"."_$contIng'></div>
										<div onclick='ventanaDescripcionArticulo$opcGrillaContable($contIng)' id='descripcionArticulo$opcGrillaContable"."_$contIng' title='Agregar Observacion' style='width: 20px; float: left; margin-top: 3px; display: block; cursor: pointer;'><img src='img/edit.png'></div>
										<div onclick='deleteArticulo$opcGrillaContable($contIng)' id='deleteArticulo$opcGrillaContable"."_$contIng' title='Eliminar Articulo' style='width: 20px; float: left; margin-top: 3px; display: block; cursor: pointer;'><img src='img/delete.png'></div>
									</div>";
					$eventCodigo   = "onkeyup=\"buscarArticulo$opcGrillaContable(event,this)\"";
					$eventCantidad = "onkeyup=\"validarNumberArticulo$opcGrillaContable(event,this,'double',$contIng)\"";
					$eventTipoDesc = "onclick=\"tipoDescuentoArticulo$opcGrillaContable($contIng)\"";
					$eventDesc     = "onkeyup=\"validarNumberArticulo$opcGrillaContable(event,this,'double',$contIng)\"";
					$eventCosto    = "onkeyup=\"guardarAuto$opcGrillaContable(event,this,$contIng)\"";
				}

				$ingredientes .= "<div class='bodyDivArticulos$opcGrillaContable' id='bodyDivArticulos$opcGrillaContable"."_$contIng' >
									<div class='campo' style='width:40px !important; overflow:hidden;'>
									<div style='float:left; margin:3px 0 0 2px;'>$contIng</div>
									<div style='float:left; width:18px; overflow:hidden;' id='renderArticulo$opcGrillaContable"."_$contIng'></div>
									</div>
									<div class='campo' style='width:12%;'>
										<input type='text' $readonly id='eanArticulo$opcGrillaContable"."_$contIng' value='$codigo' $eventCodigo >
									</div>
									<div class='campoNombreArticulo'><input type='text' $readonly value='$nombre' id='nombreArticulo$opcGrillaContable"."_$contIng' style='text-align:left;' readonly=''></div>
									$btnBuscar
									<div class='campo'><input type='text' $readonly id='unidades$opcGrillaContable"."_$contIng' style='text-align:left;' readonly='' value='$unidad_medida'></div>
									<div class='campo'><input type='text' $readonly id='cantArticulo$opcGrillaContable"."_$contIng' value='$cantidad' $eventCantidad  ></div>
									<div class='campo campoDescuento'>
										<div $eventTipoDesc id='tipoDescuentoArticulo$opcGrillaContable"."_$contIng' title='En porcentaje'>
											<img src='img/porcentaje.png' id='imgDescuentoArticulo$opcGrillaContable"."_$contIng'>
										</div>
										<input type='text' $readonly id='descuentoArticulo$opcGrillaContable"."_$contIng' value='0' readonly $eventDesc >
									</div>
									<div class='campo'><input type='text' $readonly id='costoArticulo$opcGrillaContable"."_$contIng'  value='$costo_unitario' $eventCosto ></div>
									<div class='campo'><input type='text' $readonly id='costoTotalArticulo$opcGrillaContable"."_$contIng' readonly='' value='$total_ingrediente'></div>
									$btnsRows
									<input type='hidden' id='idRecetaArticulo$opcGrillaContable"."_$contIng' value='$id_fila_item_receta'>
									<input type='hidden' id='idArticulo$opcGrillaContable"."_$contIng' value='$id_inventario'>
									<input type='hidden' id='idInsertArticulo$opcGrillaContable"."_$contIng' value='$id_row'>
									<input type='hidden' id='ivaArticulo$opcGrillaContable"."_$contIng' value='$id_impuesto'>
								</div>";
				$contIng += 0.01;
			}

			$script = "var div_content_ing = document.createElement('div')
						div_content_ing.setAttribute('id','divIngredientes$opcGrillaContable"."_$cont');
						div_content_ing.setAttribute('class','divIngredientes');
						// div_content_ing.setAttribute('style','display:none');
						div_content_ing.innerHTML = `$ingredientes`;
						document.getElementById('bodyDivArticulos$opcGrillaContable"."_$cont').after(div_content_ing)
						var divRender = document.getElementById('renderArticulo$opcGrillaContable"."_$cont')
						divRender.parentNode.innerHTML = `<img style='float:left;cursor:pointer;' onclick='showHiddenIngredients(\"divIngredientes$opcGrillaContable"."_$cont\")' title='Ingredientes' src='img/list.png'> <div style='float:left; width:18px; overflow:hidden;' id='renderArticulo$opcGrillaContable"."_$contIng'></div>
															<div style='float:left; width:20px' id='renderArticulo$opcGrillaContable"."_$cont'></div>`;";
			return $script;
		}

	}

	// CARGAR LOS GRUPOS DE LOS ITEMS
	function cargaDivsGruposItems($id_documento,$tablaInventarioGrupos,$opcGrillaContable,$estado,$id_empresa,$link){
		//ventas_facturas_grupos
		$sql="SELECT * FROM $tablaInventarioGrupos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_venta=$id_documento";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)){
			$id_row         = $row['id'];
			$codigo         = $row['codigo'];
			$nombre         = $row['nombre'];
			$cantidad       = $row['cantidad'];
			$costo_unitario = $row['costo_unitario'];
			$observaciones  = $row['observaciones'];
			$descuento      = $row['descuento'];
			$valor_impuesto = $row['valor_impuesto'];
			$total          = $costo_unitario-$descuento;
			$btns = ($estado<>0)? '' : '<div style="float:right; min-width:80px;">
														<div onclick="ventanaActualizaAgrupacionItems('.$id_row.')" title="Modificar Grupo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
														<div onclick="deleteGrupoFacturaVenta('.$id_row.')" id="" title="Eliminar Grupo" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
													</div>';
			$scriptRow .= 'var addRow'.$id_row.' = `<div class="bodyDivArticulosFacturaVenta" id="bodyDivGruposFacturaVenta_'.$id_row.'" data-id="'.$id_row.'">
													<div class="campo" style="width:40px !important; overflow:hidden;text-align:center;">
													<img src="img/grupos.png" style="margin-top: 3px;cursor:hand;" title="Grupo de Items" onclick="showHiddenItems('.$id_row.')">
													<div style="float:left; width:18px; overflow:hidden;" id="renderGrupoFacturaVenta_'.$id_row.'" ></div>
													</div>
													<div class="campo" style="width:12%;"><input type="text" id="codigoGrupoFacturaVenta_'.$id_row.'" readonly value="'.$codigo.'"></div>
													<div class="campoNombreArticulo"><input type="text" id="nombreGrupoFacturaVenta_'.$id_row.'" style="text-align:left;" readonly value="'.$nombre.'"></div>
													<div class="campo"><input type="text" id="unidadGrupoFacturaVenta_'.$id_row.'" readonly value="Unidad"></div>
													<div class="campo"><input type="text" id="cantGrupoFacturaVenta_'.$id_row.'" readonly value="'.$cantidad.'" ></div>
													<div class="campo"><input type="text" id="descuentoArticuloFacturaVenta_'.$id_row.'" readonly value="'.$descuento.'"></div>
													<div class="campo"><input type="text" id="costoGrupoFacturaVenta_'.$id_row.'" readonly value="'.$costo_unitario.'"></div>
													<div class="campo"><input type="text" id="costoTotalGrupoFacturaVenta_'.$id_row.'" readonly value="'.$total.'"></div>
													'.$btns.'
												</div>
												<div id="content-group-'.$id_row.'" style="display:none;border-bottom: 1px dashed #819cba;">
												</div>`;
							$("#DivArticulos'.$opcGrillaContable.'").prepend(addRow'.$id_row.');';
		}

		// CONSULTAR LOS ITEMS DE ESE GRUPO PARA MOVERLOS AL DIV DEL GRUPO
		$sql="SELECT id_grupo_factura_venta,id_inventario_factura_venta FROM ventas_facturas_inventario_grupos WHERE id_factura_venta=$id_documento";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id_grupo      = $row['id_grupo_factura_venta'];
			$id_inventario = $row['id_inventario_factura_venta'];
			$scriptRow .="if($(\"[value='$id_inventario']\")[0]){
              $('#bodyDivArticulosFacturaVenta_'+$(\"[value='$id_inventario']\")[0].id.split('_')[1]).appendTo('#content-group-$id_grupo');
              }";
		}

		return $scriptRow;
	}


?>
