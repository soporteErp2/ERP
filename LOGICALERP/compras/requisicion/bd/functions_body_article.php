<?php

    $styleCamposEliminados = 'display:none';//EN CASO QUE SE REQUIERA VOLVER A MOSTRAR LOS CAMPOS ELIMINADOS

	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

        global $styleCamposEliminados;

		$scriptRemision          = ''; 						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision = '';
		$idRemision            = '';

		$arrayReplaceString       = array("\n", "\r","<br>");
		$observacionFactura = str_replace($arrayReplaceString, " ", $observacion);

		//CONSULTAR SI EL TERCERO ES EXCENTO DE IVA
		$sql   = "SELECT T.id,T.exento_iva,DV.observacion FROM terceros AS T, $tablaPrincipal AS DV WHERE T.id=DV.id_cliente AND DV.id=$id";
		$query = mysql_query($sql,$link);

		$exento_iva  = mysql_result($query,0,'exento_iva');
		$observacion = mysql_result($query,0,'observacion');

		// echo $exento_iva;
		if ($opcGrillaContable=="FacturaVenta" || $opcGrillaContable=='EntradaAlmacen' || $opcGrillaContable=='PedidoVenta'){
			$arrayColor = array('','#2b0af7','#000','#eb0af7','#faa96a','#889588','#330300','#54048a','#140af7','#0af7be','#737603','#2a0245','#f0f70a','#048080','#83048a','#0ccc00','#f7710a','#05024f','#033300','#ab39f8','#0af7f7','#510500','#8370ff','#060033','#c400cc','#f97aff','#996900','#008480','#b7fefc','#ffb25b','#a7a9a9');

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
					WHERE $idTablaPrincipal='$id' AND activo = 1
					ORDER BY id ASC";
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
				<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		$readonly  = '';
		$cont      = 0;
		$contColor = 0;
		$docCruce  = '';

		if ($opcGrillaContable=="FacturaVenta" || $opcGrillaContable=="RemisionesVenta" || $opcGrillaContable=='PedidoVenta') {
			if ($estado == 0) {
				while($row = mysql_fetch_array($query)){
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'] ;
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'] ;
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'] ;

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
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'] ;
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'] ;
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'] ;

					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'], $row['id_impuesto'],$row['impuesto'],$row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida']).'
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
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'] ;
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'] ;
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'] ;

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
					$row['id_impuesto']    =($exento_iva=='Si')? 0 : $row['id_impuesto'] ;
					$row['impuesto']       =($exento_iva=='Si')? '': $row['impuesto'] ;
					$row['valor_impuesto'] =($exento_iva=='Si')? '': $row['valor_impuesto'] ;

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
					<div class="contenedorDetalleTotales" style="'.$styleCamposEliminados.'">
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
					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacionFactura.'";
					if(Ext.getCmp("btnNueva'.$opcGrillaContable.'")){
						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					}

				</script>';

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$color='',$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento='',$descuento = 0, $id_impuesto=0,$impuesto='',$valor_impuesto=0, $estado='',$nombre_unidad='',$cantidad_unidad='',$NCR='',$ICR='',$CR=''){
		//LA VARIABLE NCR ES EL nombre_consecutivo_referencia, SI ES Remision ENTONCES NO MOSTRAMOS EL BOTON BUSCAR ARTICULO, Y QUITAMOS EL EVENTO DE BUSCAR ARTICULO DEL CAMPO EAN

		global $styleCamposEliminados;

		$eventoEan         = 'onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);"';
		$btnBuscarArticulo = '<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo" style="'.$mostrarImagen.'">
								<img src="img/buscar20.png"/>
							</div>';

		if ($NCR=='Remision' || $NCR=='Pedido') {
			$eventoEan         = '';
			$btnBuscarArticulo = '';
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
			$scriptColor    = 'document.getElementById("btn'.$opcGrillaContable.'_'.substr($NCR, 0, 1).'_'.$ICR.'").style.backgroundColor="'.$color.'"';
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

				<div class="campo" >
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' '.$eventoEan.'  value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.' value="'.$nombre.'" /></div>
				'.$btnBuscarArticulo.'

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="'.$mostrarUnidad.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.($cantidad * 1).'" '.$deshabiltar.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/></div>

				<div class="campo campoDescuento" style="'.$styleCamposEliminados.'">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$descuento.'" '.$readonly_descuento.' '.$readonly_descuento_remision_adjunta.' '.$deshabiltar.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' '.$readonly_precio.'  '.$readonly_precio_remision_adjunta.' value="'.$costo_unitario.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.'   readonly/></div>

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
				<input type="hidden" class="classInputInsertArticulo" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idInsertArticuloFactura.'" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id_impuesto.'">

				<script>
					//console.log("i: '.$impuesto.' -  val: '.$valor_impuesto.' id: '.$id_impuesto.'");
					if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
						arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}
					//llamamos la funcion para generar los calculos de la factura
					//calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');
					'.$scriptColor.'
				</script>';

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento='',$descuento = 0, $id_impuesto=0,$impuesto='',$valor_impuesto=0,$estado='',$nombre_unidad='',$cantidad_unidad=''){

		global $styleCamposEliminados;

		$srcImgDescuento  = 'img/pesos.png';

		if ($tipoDescuento=='porcentaje') {
			$srcImgDescuento  = 'img/porcentaje.png';
			$titleDescuento	  = 'En porcentaje';
		}
		//mostrar las unidades

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo">
					<input readonly type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'"   value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly  value="'.$nombre.'" /></div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$nombre_unidad.' x '.$cantidad_unidad.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.($cantidad * 1).'"  readonly /></div>

				<div class="campo campoDescuento" style="'.$styleCamposEliminados.'">
					<div id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$descuento.'" readonly/>
				</div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'"  value="'.$costo_unitario.'" readonly  value="0"/></div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"    readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Configuracion Adicional" style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="img/config16.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />

				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');
					if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
						arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}
					//llamamos la funcion para generar los calculos de la factura
					//calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');

				</script>';

		return $body;
	}


?>