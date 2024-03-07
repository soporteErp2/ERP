<?php

	function cargaArticulosOrdenCompraSave($idOrdenCompra,$observacionOrdenCompra,$estadoOrdenCompra,$link){

		$cont  = 0;
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
				  FROM compras_ordenes_inventario
				  WHERE id_orden_compra='$idOrdenCompra' AND activo = 1";

		$query = mysql_query($sql,$link);

		$body = '<script>
					contArticulosOrdenCompra = 0;
					subtotalOrdenCompra      = 0.00;
					ivaOrdenCompra           = 0.00;
					totalOrdenCompra         = 0.00;

					arrayIvaOrdenCompra=[];
				</script>
				<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS ORDEN DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="headOrdenesCompra">
							<div class="label" style="width:40px !important; border-left:none; padding-left:2px;"></div>
							<div class="label">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label">Descuento</div>
							<div class="label" style="border-right: 1px solid #d4d4d4">Costo Unitario</div>
							<div class="label" title="Costo Total">Costo Total</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos" onscroll="resizeHeadMyGrilla(this,\'headOrdenesCompra\')">';
		$eventoInputObservacion=($estadoOrdenCompra==0)?'onKeydown="inputObservacionOrdenCompra(event,this)"' : '';

		if($estadoOrdenCompra == 2){		// INPUT READONLY

			while($row = mysql_fetch_array($query)){
				$row['cantidad']       = $row['cantidad'] * 1;
				$row['descuento']      = $row['descuento'] * 1;
				$row['costo_unitario'] = $row['costo_unitario'] * 1;

				$cont++;
				$body .='<div class="bodyDivArticulos" id="bodyDivArticulos_'.$cont.'">
							'.cargaDivsUnidadesBloqueadas($cont,$row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'],$row['id_impuesto'],$row['impuesto'],  $row['valor_impuesto'], $row['nombre_unidad_medida'], $row['cantidad_unidad_medida']).'
						</div>';
			}
		}

		else{								// ORDEN DE COMPRA HABIL
			while($row = mysql_fetch_array($query)){

				$row['cantidad']       = $row['cantidad'] * 1;
				$row['descuento']      = $row['descuento'] * 1;
				$row['costo_unitario'] = $row['costo_unitario'] * 1;

				$cont++;

				//CONTROL DEL COLOR DE LOS BOTONES
				if($row['ICR'] > 0 && $row['NCR'] != '' && $docCruce !=  $row['NCR'].'_'.$row['ICR']){
					$contColor++;
					if($contColor == 29) $contColor = 1;

					$docCruce =  $row['NCR'].'_'.$row['ICR'];
					$color    = $arrayColor[$contColor];
				}
				else if($row['ICR'] == 0 || $row['ICR'] == ''){ $color = ''; }

				$body .='<div class="bodyDivArticulos" id="bodyDivArticulos_'.$cont.'">
							'.cargaDivsUnidadesSave($cont,$color,$row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['costo_unitario'], $row['tipo_descuento'], $row['descuento'],$row['id_impuesto'],$row['impuesto'], $row['valor_impuesto'], $row['nombre_unidad_medida'], $row['cantidad_unidad_medida'],$row['NCR'],$row['ICR'] ,$row['CR'] ).'
						</div>';
			}

			$cont++;
			$body .='<div class="bodyDivArticulos" id="bodyDivArticulos_'.$cont.'">
						'.cargaDivsUnidadesSave($cont).'
					</div>';
		}


		$body .='	</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_ordenes_compras">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacionOrdenCompra"><b>OBSERVACIONES</b></div>
						<textarea id="observacionOrdenCompra" '.$eventoInputObservacion.'>'.$observacionOrdenCompra.'</textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalOrdenCompra"></div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetenciones" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px;font-weight:bold; overflow:visible;" id="idretencionOrdenCompra"></div>
							<div class="labelSimbolo" id="simboloRetencion"></div>
							<div class="labelTotal" style="height:auto; overflow:visible;" id="retencionOrdenCompra"></div>
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
					resizeHeadMyGrilla(document.getElementById("DivArticulos"),\'headOrdenesCompra\');
					document.getElementById("observacionOrdenCompra").value="'.$observacionOrdenCompra.'";
					contArticulosOrdenCompra = '.$cont.';
					Ext.getCmp("Btn_nueva_orden_compra").enable();
				</script>';

		return $body;

	}

	function cargaDivsUnidadesSave($cont,$color='',$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = '',$costo_unitario = 0,$tipoDescuento = 'porcentaje',$descuento = '0.00' ,$id_impuesto=0,$impuesto='',$iva=0, $unidad_medida='',$cantidad_medida='',$NCR='',$ICR='',$CR=''){
		$srcImg            = 'img/reload.png';
		$displayBtnReload  = 'display:none';
		$displayBtns       = 'display:block';
		$inputUnidadMedida = '';

		if($unidad_medida != ''){ $inputUnidadMedida = $unidad_medida.' x '.$cantidad_medida; }

		if($id_inventario == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none';
		}
        //CONTROL DEL BOTON BUSCAR ARTICULO

        $btnBuscarArticulo = '<div onclick="ventanaBuscarArticuloOrdenCompra('.$cont.');" class="iconBuscarArticulo"><img src="img/buscar20.png"/></div>';
		if ($NCR=='Requisicion') {
			$eventoEan         = '';
			$btnBuscarArticulo = '';
			$readonlyCampo     = 'readonly';
		}
        //CONTROL DEL COLOR DE LOS BOTONES
		$scriptColor = '';
		if($color == ''){
			$color       = '#b70c00';
			$scriptColor = '';
		}
		else{
			$titleBtnDelete = '('.substr($NCR, 0, 1).' '.$CR.')';
			$scriptColor    = 'document.getElementById("btnOrdenCompra_'.substr($NCR, 0, 1).'_'.$ICR.'").style.backgroundColor="'.$color.'"';
		}

		if ($iva =='' ) { $iva = 0; }

		$body ='<div class="campo" style="width:40px !important; border-left:none; padding-left:2px; overflow:hidden;">
					<div style="float:left; margin-top:3px;">'.$cont.'</div>
					<div style="float:right; width:18px; overflow:hidden;" id="renderArticuloOrdenCompra_'.$cont.'"></div>
				</div>
				<div class="campo">
					<input type="text" id="eanArticulo_'.$cont.'" style="float:left" value="'.$codigo.'" onKeyup="buscarArticuloOrdenCompra(event,this);" '.$readonlyCampo.'/>
				</div>
				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo_'.$cont.'" style="text-align:left;" value="'.$nombre.'" readonly/></div>
                '.$btnBuscarArticulo.'
				<div class="campo"><input type="text" style="text-align:left;" id="unidades_'.$cont.'" value="'.$inputUnidadMedida.'" readonly/></div>

				<div class="campo"><input type="text" id="cantArticulo_'.$cont.'" value="'.($cantidad * 1).'"/></div>
				<div class="campo campoDescuento">
					<div onclick="tipoDescuentoArticuloOrdenCompra('.$cont.',this)" id="divTipoDescuentoArticuloOrdenCompra_'.$cont.'" title="En '.$tipoDescuento.'">
						<img src="img/'.$tipoDescuento.'.png" id="imgDescuentoArticuloOrdenCompra_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo_'.$cont.'" value="'.$descuento.'"/>
				</div>

				<div class="campo" style="border-right: 1px solid #d4d4d4"><input type="text" id="costoArticulo_'.$cont.'" value="'.$costo_unitario.'" /></div>

				<div class="campo" style="border-right: 1px solid #d4d4d4;"><input type="text" id="costoTotalArticuloOrdenCompra_'.$cont.'" value='.($costo_unitario*$cantidad).' readonly value="0"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticuloOrdenCompra('.$cont.');" id="divGuardarNewArticuloOrdenCompra_'.$cont.'" title="Guardar" class="icono" style="'.$displayBtnReload.'">
						<img src="'.$srcImg.'" id="imgSaveOrdenCompra_'.$cont.'"/>
					</div>
					<div onclick="noUpdateArticuloOrdenCompra('.$cont.')" id="divImageNoUpdateArticuloOrdenCompra_'.$cont.'" title="Deshacer Cambios" style="display:none" class="icono">
						<img src="img/deshacer.png" id="imgNoUpdateArticuloOrdenCompra_'.$cont.'">
					</div>
					<div onclick="ventanaDescripcionArticuloOrdenCompra('.$cont.');" id="descripcionArticuloOrdenCompra_'.$cont.'" title="Observaciones" style="'.$displayBtns.'" class="icono">
						<img src="img/edit.png"/>
					</div>
					<div onclick="deleteArticuloOrdenCompra('.$cont.');" id="deleteArticuloOrdenCompra_'.$cont.'"  title="Eliminar Articulo '.$titleBtnDelete.'" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;">
						<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; background-color:'.$color.'; margin:1px;">
                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                        </div>
					</div>
				</div>

				<input type="hidden" id="idArticulo_'.$cont.'" value="'.$id_inventario.'"/>
				<input type="hidden" class="classInputInsertArticulo" id="idInsertArticulo_'.$cont.'" value="'.$id.'"/>
				<input type="hidden" id="ivaArticuloOrdenCompra_'.$cont.'" value="'.$id_impuesto.'" />

				<script>
					if (typeof(arrayIvaOrdenCompra['.$id_impuesto.'])=="undefined") {
						arrayIvaOrdenCompra['.$id_impuesto.'] = { nombre:"'.$impuesto.'", valor:"'.$iva.'" };
					}
                    //alert("'.$color.'");
					document.getElementById("cantArticulo_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloOrdenCompra(event,this); };
					document.getElementById("descuentoArticulo_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloOrdenCompra(event,this); };
					document.getElementById("costoArticulo_'.$cont.'").onkeyup = function(event){ return guardarAutoOrdenCompra(event,this,'.$cont.'); };
					// console.log("'.$id_impuesto.'");
					calcularValoresOrdenCompra("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'","'.$cont.'");
				    '.$scriptColor.'
				</script>';

		return $body;
	}


	function cargaDivsUnidadesBloqueadas($cont,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = '',$costo_unitario = 0,$tipoDescuento = 'porcentaje',$descuento,$id_impuesto=0,$impuesto='',$iva=0, $unidad_medida='',$cantidad_medida=''){
		$inputUnidadMedida = '';
		if($unidad_medida != ''){ $inputUnidadMedida = $unidad_medida.' x '.$cantidad_medida; }

		$body ='<div class="campo" style="width:40px !important; border-left:none; padding-left:2px; overflow:hidden;">
					<div style="float:left; margin-top:3px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticuloOrdenCompra_'.$cont.'"></div>
				</div>
				<div class="campo">
					<input type="text" id="eanArticulo_'.$cont.'" style="float:left" value="'.$codigo.'" readonly/>
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo_'.$cont.'" style="text-align:left;" value="'.$nombre.'" readonly/></div>
				<div class="campo"><input type="text" style="text-align:left;" id="unidades_'.$cont.'" readonly value="'.$inputUnidadMedida.'"/></div>

				<div class="campo"><input type="text" id="cantArticulo_'.$cont.'" value="'.($cantidad * 1).'" readonly/></div>
				<div class="campo campoDescuento">
					<div id="divTipoDescuentoArticuloOrdenCompra_'.$cont.'" title="En '.$tipoDescuento.'">
						<img src="img/'.$tipoDescuento.'.png" id="imgDescuentoArticuloOrdenCompra_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo_'.$cont.'" value="'.$descuento.'" readonly/>
				</div>

				<div class="campo" style="border-right: 1px solid #d4d4d4"><input type="text" id="costoArticulo_'.$cont.'" value="'.$costo_unitario.'" readonly/></div>

				<div class="campo" style="border-right: 1px solid #d4d4d4;"><input type="text" id="costoTotalArticuloOrdenCompra_'.$cont.'" value='.($costo_unitario*$cantidad).' readonly value="0"/></div>

				<div style="float:right; min-width:80px;"></div>
				<input type="hidden" id="ivaArticuloOrdenCompra_'.$cont.'" value="'.$iva.'" />

				<script>
					if (typeof(arrayIvaOrdenCompra['.$id_impuesto.'])=="undefined") {
						arrayIvaOrdenCompra['.$id_impuesto.'] = { nombre:"'.$impuesto.'", valor:"'.$iva.'" };
					}

					calcularValoresOrdenCompra("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'","'.$cont.'");
					document.getElementById("observacionOrdenCompra").setAttribute("readonly","readonly");
				</script>';

		return $body;
	}


?>