<?php

	function cargaArticulosSave($id_documento,$id_empresa,$opcGrillaContable,$mysql){
		$sql="SELECT
				id,
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
				id_cuenta_credito,
				cuenta_credito,
				descripcion_cuenta_credito,
				valor
			FROM amortizaciones_diferidos WHERE activo=1 AND id_empresa=$id_empresa AND id_amortizacion=$id_documento";

		$query=$mysql->query($sql,$mysql->link);
		$cont           = 1;
		$totalAcumulado = 0;
		$body ='<div class="contenedorGrilla" style="height:400px;">
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
						';

						while ($row=$mysql->fetch_array($query)) {
							$body .='<div class="bodyDivArticulos" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">';
							$body .= cargaDivsUnidadesSave($cont,$opcGrillaContable,$row['tipo_documento'],$row['consecutivo_documento'],$row['fecha_inicio'],$row['documento_tercero'],$row['tercero'],$row['valor'] );
							$body .='</div>';
							$cont++;
							$totalAcumulado+=$row['valor'];
						}

		$body .='
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<!--<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Total Extracto</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotal'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Total Detalle</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalDetalle'.$opcGrillaContable.'">0</div>
						</div>-->

						<div class="renglon renglonTotal" style="border-top:none;">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL AMORTIZACION</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">'.number_format($totalAcumulado,$_SESSION['DECIMALESMONEDA']).'</div>
						</div>
					</div>
				</div>

				<script>
				</script>';
		// echo $head;

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$tipo_documento,$consecutivo_documento,$fecha_inicio,$documento_tercero,$tercero,$valor){

		$body ='<div class="campo" style="width:40px !important;">'.$cont.'</div>
				<div class="campo" style="width:80px;"><input type="text"  readonly style="text-align:center;" value="'.$tipo_documento.'"></div>
				<div class="campo" style="width:80px;"><input type="text"  readonly style="text-align:left;" value="'.$consecutivo_documento.'"></div>
				<div class="campo" style="width:90px;"><input type="text"  readonly style="text-align:left;" value="'.$fecha_inicio.'"></div>
				<div class="campo" style="width:90px;"><input type="text"  readonly style="text-align:left;" value="'.$documento_tercero.'"></div>
				<div class="campo" style="width:250px;"><input type="text" readonly style="text-align:left;" value="'.$tercero.'"></div>
				<div class="campo" style="width:100px;"><input type="text" readonly  value="'.$valor.'"></div>

				<div id="guardar_registro" style="float:right; border:solid min-width:80px;">

				</div>

				<input type="hidden" id="idRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />

				<script>
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

				$scriptColor   .=  'document.getElementById("label'.$opcGrillaContable.'_'.substr($NCR, 0, 1).'_'.$CR.'").style.color="'.$color.'";
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
				'.$columnaCruzados.'
				<script>
					'.$scriptColor.'

					if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
						arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					}
					//llamamos la funcion para generar los calculos de la factura
					calcTotalDocCompraVenta'.$opcGrillaContable.'("'.$cantidad.'","'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipoDescuento.'","'.$id_impuesto.'",'.$cont.');

				</script>';

		return $body;
	}


?>