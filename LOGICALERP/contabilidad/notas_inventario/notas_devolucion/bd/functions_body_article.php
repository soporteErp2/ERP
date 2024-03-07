<?php
	//========================= CARGAR ARTICULO NUEVO ==========================//
	function cargaArticuloNuevo($opcGrillaContable){
		$body = '<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label" style="width:40px !important;"></div>
							<div class="label" style="width:12%">Codigo/EAN</div>
							<div class="labelNombreArticulo" style="width:30%">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label">Descuento</div>
							<div class="label">Precio</div>
							<div class="label" style="border-right: 1px solid #d4d4d4">Total</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_1">
							 '.cargaDivsUnidadesSave(1,$opcGrillaContable).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Iva</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelSimbolo" id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelTotal" style="height:auto; overflow:visible;" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
						</div>
						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL NOTA</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>

				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');
					document.getElementById("eanArticulo'.$opcGrillaContable.'_1").focus();
				</script>';

				return $body;

	}
	//======================= CARGAR ARTICULOS GUARDADOS =======================//
	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$tablaCarga,$idTablaCarga,$link){
		$cont                    = 0;					//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';
		$tablaCarga             .='_inventario';

		$whereSqlSaldoRemision   = '';					//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$idRemision              = '';

		$sql = "SELECT
							id,
							id_fila_cargada,
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
						FROM
							$tablaInventario
						WHERE
							$idTablaPrincipal = '$id'
						AND
							activo = 1";
		$query = mysql_query($sql,$link);

		$body = '<div class="contenedorGrilla">
							<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
							<div class="contenedorHeadArticulos">
								<div class="headArticulos" id="head'.$opcGrillaContable.'">
									<div class="label" style="width:40px !important;"></div>
									<div class="label" style="width:12%">Codigo/EAN</div>
									<div class="labelNombreArticulo" style="width:30%">Articulo</div>
									<div class="label">Unidad</div>
									<div class="label">Cantidad</div>
									<div class="label">Descuento</div>
									<div class="label">Precio</div>
									<div class="label" style="border-right: 1px solid #d4d4d4">Total</div>
									<div style="float:right; min-width:80px;"></div>
								</div>
							</div>
							<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		if($estado == 0){
			while($row = mysql_fetch_array($query)){
				$cont++;
				//CONSULTAMOS EL SALDO CANTIDAD DE LA TABLA DE DONDE SE CARGA EL ARTICULO PARA LAS VALIDACIONES DE CANTIDAD
				$sqlSaldo = "SELECT saldo_cantidad FROM $tablaCarga WHERE activo = 1 AND id = '".$row['id_fila_cargada']."' AND saldo_cantidad>0 LIMIT 0,1";

				$querySaldo     = mysql_query($sqlSaldo,$link);
				$saldo_cantidad = mysql_result($querySaldo,0,'saldo_cantidad');

				$body .= '<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							    '.cargaDivsUnidadesSave($cont,$opcGrillaContable,$row['id'],$row['id_inventario'],$row['codigo'],$row['nombre'],$row['cantidad'],$row['costo_unitario'],$row['tipo_descuento'],$row['descuento'],$row['id_impuesto'],$row['impuesto'],$row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],$row['id_fila_cargada'],$saldo_cantidad).'
						      </div>';
			}
			$cont++;
			$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
						'.cargaDivsUnidadesSave($cont,$opcGrillaContable).'
					</div>';
			$mostarBoton = 'enable()';
			$deshabilita = '';
			$eventoObservacionGrilla="onKeydown=inputObservacion".$opcGrillaContable."(event,this)";

		}
		else{
			while($row = mysql_fetch_array($query)){
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'],$row['id_impuesto'],$row['impuesto'], $row['valor_impuesto'],$estado,$row['nombre_unidad_medida'],$row['cantidad_unidad_medida'],$row['id_fila_cargada']).'
						</div>';
			}
			$mostarBoton = 'disable()';
			$deshabilita = 'readonly';
		}


		$body .= '</div>
					</div>
					<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
						<div class="contenedorObservacionGeneral">
							<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
							<textarea id="observacion'.$opcGrillaContable.'" '.$eventoObservacionGrilla.' '.$deshabilita.'  ></textarea>
						</div>
						<div class="contenedorDetalleTotales">
							<div class="renglon">
								<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
								<div class="labelSimbolo">$</div>
								<div class="labelTotal" id="subtotalAcumulado'.$opcGrillaContable.'"  style="width:140px;">0</div>
							</div>
							<div class="renglon" style="overflow:visible; height:auto;">
								<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIva'.$opcGrillaContable.'">Iva</div>
								<div class="labelSimbolo" id="simboloIva'.$opcGrillaContable.'">$</div>
								<div class="labelTotal" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
							</div>
							<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'" >
								<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"></div>
								<div class="labelSimbolo" id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
								<div class="labelTotal" style="height:auto; overflow:visible;" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
							</div>

							<div class="renglon renglonTotal" >
								<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL NOTA</div>
								<div class="labelSimbolo">$</div>
								<div class="labelTotal" id="totalAcumulado'.$opcGrillaContable.'"  style="width:140px;">0</div>
							</div>
						</div>
					</div>
					<script>
						resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');
						contArticulos'.$opcGrillaContable.'= '.$cont.';

						document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";
	        			Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").'.$mostarBoton.';
					</script>';

		return $body;

	}
	//=================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$id=0,$id_inventario='',$codigo='',$nombre='',$cantidad=0,$costo_unitario=0,$tipoDescuento='',$descuento=0,$id_impuesto=0,$impuesto='',$valor_impuesto=0,$estado='',$nombre_unidad='',$cantidad_unidad='',$id_fila_cargada,$saldo_cantidad=0){
		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';
		$srcImgDescuento  = 'img/porcentaje.png';
		$titleDescuento	  = 'En porcentaje';
		$descuento2 	  	= 0;

		$mostrarImagen            = '';
		$deshabiltar              = '';
		$eventoTipoDesc           = ' onclick="tipoDescuentoArticulo('.$cont.')"';
		$idArticuloFactura        = $id_inventario;
		$idInsertArticuloFactura  = $id;
		$ivaArticuloFacturaCompra = $valor_impuesto;

		//Calcular subtotal del articulo
		$subtotal            = $cantidad*$costo_unitario;
		$arrayValidaUnidades = 'cantidadesArticulos'.$opcGrillaContable.'["'.$cont.'"]="'.$saldo_cantidad.'";';

		if($id_inventario == ''){
			$srcImg              = 'img/save_true.png';
			$displayBtnReload    = '';
			$displayBtns         = 'display:none;';
			$arrayValidaUnidades = '';
		}

		//verificar que descuento tiene el articulo si en pesos o en porcentaje
		if($tipoDescuento == 'pesos'){
			$srcImgDescuento = 'img/pesos.png';
			$titleDescuento  = 'En  pesos';
		}	else{
			$descuento2 = $descuento;
		}

		if ($nombre_unidad != '' || $cantidad_unidad!='') { $mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad; }

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.' value="'.$nombre.'" /></div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo" style="'.$mostrarImagen.'" >
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="'.$mostrarUnidad.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$cantidad.'" '.$deshabiltar.' onKeydown="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo campoDescuento">
					<div id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$descuento.'" '.$deshabiltar.' readonly onKeydown="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' value="'.$costo_unitario.'" '.$deshabiltar.' onKeydown="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="img/edit.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;"><img src="img/delete.png" /></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idArticuloFactura.'" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idInsertArticuloFactura.'" />

				<!-- id de la fila insertada del documento -->
				<input type="hidden" id="idInsertFilaCargada'.$opcGrillaContable.'_'.$cont.'" value="'.$id_fila_cargada.'" />
				<!-- id de la fila nueva a  insertar en la nota -->
				<input type="hidden" id="idInsertNewFilaCargada'.$opcGrillaContable.'_'.$cont.'" value="'.$id_fila_cargada.'" />

				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id_impuesto.'" >

				<script>
					if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.']) == "undefined"){
						if(exento_iva_'.$opcGrillaContable.' == "Si"){
							arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"0"};
						} else{
							arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
						}
					}

					'.$arrayValidaUnidades.'		//Llenamos el array para validar la cantidad de articulos

					//Llamamos la funcion para generar los calculos de la factura
					calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');
				</script>';

		return $body;
	}
	//============ CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ===========//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0,$costo_unitario = 0,$tipoDescuento='',$descuento = 0,$id_impuesto=0,$impuesto='', $valor_impuesto=0,$estado='',$nombre_unidad='',$cantidad_unidad='',$id_fila_cargada){

		$srcImgDescuento  = 'img/pesos.png';

		if ($tipoDescuento=='porcentaje') {
			$srcImgDescuento = 'img/porcentaje.png';
			$titleDescuento  = 'En porcentaje';
		}

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input readonly type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$nombre.'" /></div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$nombre_unidad.' x '.$cantidad_unidad.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$cantidad.'" readonly /></div>

				<div class="campo campoDescuento">
					<div id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="'.$titleDescuento.'">
						<img src="'.$srcImgDescuento.'" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$descuento.'" readonly/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$costo_unitario.'" readonly value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'" readonly/></div>
				<input type="hidden" id="idInsertFilaCargada'.$opcGrillaContable.'_'.$cont.'" value="'.$id_fila_cargada.'" />

				<script>

				if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.']) == "undefined"){
					if(exento_iva_'.$opcGrillaContable.' == "Si"){
						arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"0"};
					} else{
						arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}
				}

					//llamamos la funcion para generar los calculos de la factura
					calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');
				</script>';

		return $body;
	}
	//===================== CARGAR ARTICULOS DE LOS GRUPOS =====================//
	function cargaDivsGruposItems($id_documento,$id_documento_carga,$tablaInventarioGrupos,$opcGrillaContable,$estado,$id_empresa,$link){
		//ventas_facturas_grupos
		$sql="SELECT * FROM $tablaInventarioGrupos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_devolucion_venta=$id_documento;";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)){
			$id_row         = $row['id_fila_grupo_factura_venta'];
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
											<div onclick="deleteGrupoDevolucionVenta('.$id_row.')" id="" title="Eliminar Grupo" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
										</div>';
			$scriptRow .= ' var addRow'.$id_row.' = `<div class="bodyDivArticulosDevolucionVenta" id="bodyDivArticulosDevolucionVentas_'.$id_row.'" data-id="'.$id_row.'">
														<div class="campo" style="width:40px !important; overflow:hidden;text-align:center;">
														<img src="img/grupos.png" style="margin-top: 3px;cursor:hand;" title="Grupo de Items" onclick="showHiddenItems('.$id_row.')">
														<div style="float:left; width:18px; overflow:hidden;" id="renderGrupoDevolucionVenta_'.$id_row.'" ></div>
														</div>
														<div class="campo" style="width:12%;"><input type="text" id="codigoGrupoDevolucionVenta_'.$id_row.'" readonly value="'.$codigo.'"></div>
														<div class="campoNombreArticulo"><input type="text" id="nombreGrupoDevolucionVenta_'.$id_row.'" style="text-align:left;" readonly value="'.$nombre.'"></div>
														<div class="campo"><input type="text" id="unidadGrupoDevolucionVenta_'.$id_row.'" readonly value="Unidad"></div>
														<div class="campo"><input type="text" id="cantGrupoDevolucionVenta_'.$id_row.'" readonly value="'.$cantidad.'" ></div>
														<div class="campo"><input type="text" id="descuentoArticuloDevolucionVenta_'.$id_row.'" readonly value="'.$descuento.'"></div>
														<div class="campo"><input type="text" id="costoGrupoDevolucionVenta_'.$id_row.'" readonly value="'.$costo_unitario.'"></div>
														<div class="campo"><input type="text" id="costoTotalGrupoDevolucionVenta_'.$id_row.'" readonly value="'.$total.'"></div>
														<!--'.$btns.'-->
														<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$id_row.'" value="'.$id_row.'" />
														<div style="float:right; min-width:80px;display:none;">
															<div id="divImageSave'.$opcGrillaContable.'_'.$id_row.'" style="display:none;" ><img src="img/reload.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$id_row.'"/></div>
														</div>
												</div>
												<div id="content-group-'.$id_row.'" style="display:none;border-bottom: 1px dashed #819cba;">
												</div>`;
							$("#DivArticulos'.$opcGrillaContable.'").prepend(addRow'.$id_row.');';
		}

		// CONSULTAR LOS ITEMS DE ESE GRUPO PARA MOVERLOS AL DIV DEL GRUPO
		$sql="SELECT id_grupo_factura_venta,id_inventario_factura_venta FROM ventas_facturas_inventario_grupos WHERE id_factura_venta=$id_documento_carga";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id_grupo      = $row['id_grupo_factura_venta'];
			$id_inventario = $row['id_inventario_factura_venta'];
			$scriptRow .=" if ($(\"[value='$id_inventario']\").length > 0) $('#bodyDivArticulosDevolucionVenta_'+$(\"[value='$id_inventario']\")[0].id.split('_')[1]).appendTo('#content-group-$id_grupo');";
		}

		return $scriptRow;

	}
?>
