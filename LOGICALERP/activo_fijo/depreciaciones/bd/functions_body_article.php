<?php

	// FUNCION PARA CRAGAR TODOS LOS ACTIVOS FIJOS DE UNA SUCURSAL AL CREAR EL DOCUMENTO PARA DEPRECIAR
	function cargarActivosFijosSucursal($id_documento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$link){
		$sql="SELECT id,
					metodo_depreciacion_colgaap,
					valor_salvamento,
					costo_sin_depreciar_anual,
					fecha_inicio_depreciacion,
					vida_util,
					costo,
					depreciacion_acumulada,
					depreciacion_acumulada_niif,
					deterioro_colgaap,
					deterioro_niif,
					vida_util_restante,
					vida_util_niif_restante
				FROM activos_fijos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND estado=1";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id                          = $row['id'];
			$metodo_depreciacion_colgaap = $row['metodo_depreciacion_colgaap'];
			$valor_salvamento            = $row['valor_salvamento'];
			$costo_sin_depreciar_anual   = $row['costo_sin_depreciar_anual'];
			$fecha_inicio_depreciacion   = $row['fecha_inicio_depreciacion'];
			$vida_util                   = $row['vida_util'];
			$costo                       = $row['costo'];
			$depreciacion_acumulada      = $row['depreciacion_acumulada'];
			$depreciacion_acumulada_niif = $row['depreciacion_acumulada_niif'];
			$deterioro_colgaap           = $row['deterioro_colgaap'];
			$deterioro_niif              = $row['deterioro_niif'];
			$vida_util_restante          = $row['vida_util_restante'];
			$vida_util_niif_restante     = $row['vida_util_niif_restante'];

			$valor=calculaValorDepreciacion($metodo_depreciacion_colgaap,$valor_salvamento,$costo_sin_depreciar_anual,$fecha_inicio_depreciacion,$vida_util,$costo,$depreciacion_acumulada,$deterioro_colgaap);

			$valueInsert .= "('$id_documento',
								'$id',
								'1',
								'$costo',
								'$valor',
								'$depreciacion_acumulada',
								'$id_empresa ',
								'$id_sucursal'),";
		}

		$valueInsert = substr($valueInsert, 0, -1);

		$sql="INSERT INTO $tablaInventario(
									  	$idTablaPrincipal,
									  	id_activo_fijo,
										dias_depreciar,
										costo,
										valor,
										depreciacion_acumulada,
										id_empresa,
										id_sucursal)
								VALUES $valueInsert";
		$query=mysql_query($sql,$link);

	}

		// CALCULAR EL VALOR DE LA DEPRECIACION DE UN ACTIVO
	function calculaValorDepreciacion($metodo_depreciacion_colgaap,$valor_salvamento,$costo_sin_depreciar_anual,$fecha_inicio_depreciacion,$vida_util,$costo,$depreciacion_acumulada,$deterioro_colgaap){

		// $metodo_depreciacion_colgaap = mysql_result($query,0,'metodo_depreciacion_colgaap');
		// $valor_salvamento            = mysql_result($query,0,'valor_salvamento');
		// $costo_sin_depreciar_anual   = mysql_result($query,0,'costo_sin_depreciar_anual');
		// $fecha_inicio_depreciacion   = mysql_result($query,0,'fecha_inicio_depreciacion');
		// $vida_util                   = mysql_result($query,0,'vida_util');
		// $costo                       = mysql_result($query,0,'costo');
		$fecha                       = date("Y-m-d");
		// $depreciacion_acumulada      = mysql_result($query,0,'depreciacion_acumulada');
		// $deterioro_colgaap           = mysql_result($query,0,'deterioro_colgaap');

		if($metodo_depreciacion_colgaap == 'linea_recta'){									// DEPRECIACION LINEA RECTA
			$depreciacionMes = ROUND((($costo / $vida_util)/12),2);
		}

		else if($metodo_depreciacion_colgaap == 'reduccion_saldos'){						// DEPRECIACION REDUCCION DE SALDOS
			$tasaDepreciacion = 1-(POW(
										($valor_salvamento/$costo),(1/$vida_util)
									));

			$depreciacionMes = ROUND(($costo_sin_depreciar_anual * $tasaDepreciacion)/12,2);
		}
		else if($metodo_depreciacion_colgaap == 'suma_digitos_year') { 					// DEPRECIACION SUMA DE DIGITOS DEL AÑO
			$fecha1          = new DateTime($fecha." 24:00:00");
			$fecha2          = new DateTime($fecha_inicio_depreciacion." 24:00:00");
			$diferenciaFecha = $fecha1->diff($fecha2);
			//printf('%d años, %d meses, %d días, %d horas, %d minutos', $diferenciaFecha->y, $diferenciaFecha->m, $diferenciaFecha->d, $diferenciaFecha->h, $diferenciaFecha->i);

			//list($yearDb,$mesDb,$diaDb) = explode('-',$rowActivoFijo['fecha_inicio_depreciacion']);
			if($mes == $mesDb){ $diferenciaFecha->y = $diferenciaFecha->y - 1; }

			$sumaDigitos     = ROUND(($vida_util*(($vida_util+1)/2)),2);
			$factor          = ($vida_util - $diferenciaFecha->y) / $sumaDigitos;
			$depreciacionMes = ROUND(($costo * $factor)/12,2);
		}

		return $depreciacionMes;
	}


	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		$id_empresa=$_SESSION['EMPRESA'];
		$scriptRemision          = ''; 						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision = '';
		$idRemision            = '';

		// CONSULTAR LA DEPRECIACION ACUMULADA DE LOS ACTIVOS
		$sql="SELECT deprecicion_acumulada FROM activos_fijos WHERE activo=1 AND id_empresa=";
		$query=mysql_query($sql,$link);

		$sql   = "SELECT id,id_activo_fijo,codigo_activo AS codigo,nombre,unidad,costo,dias_depreciar,depreciacion_acumulada,valor
						FROM $tablaInventario
						WHERE $idTablaPrincipal='$id' AND activo = 1";
		$query = mysql_query($sql,$link);


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
					<div class="titleGrilla"><b><!--ARTICULOS FACTURA DE COMPRA--></b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label" style="width:40px !important;"></div>
							<div class="label" style="width:calc(12% + 2px)">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label" title="Depreciacion Acumulada">Deprec. Acum.</div>
							<div class="label" title="Periodicidad en Meses">Periodicidad</div>
							<div class="label">Costo</div>
							<div class="label">Valor Deprec.</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		$readonly  = '';
		$cont      = 0;
		$contColor = 0;
		$docCruce  = '';

		// SI LA COTIZACION NO HA SIDO CEERRADA POR UNA FACTURA CARGA INPUTS READONLY
		if ($estado < 1) {
			while($row = mysql_fetch_array($query)){

				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSave($cont,$opcGrillaContable, $row['id'], $row['id_activo_fijo'], $row['codigo'], $row['nombre'], $row['unidad'], $row['dias_depreciar'], $row['costo'],$row['depreciacion_acumulada'],$row['valor'], $estado).'
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

				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable, $row['id'], $row['id_activo_fijo'], $row['codigo'], $row['nombre'],$row['unidad'], $row['dias_depreciar'], $row['costo'],$row['depreciacion_acumulada'],$row['valor'], $estado).'
						</div>';
			}
			$mostarBoton='disable()';
			$deshabilita='readonly';
		}

		$body .='	</div>
					<div class="contenedorPaginacion" style="display:none;">
			            <div style="float:right; margin:2 20px 0 0;">
			                <div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de '.$paginas.'</div>
			                <div class="my_first" onclick="paginacionGrilla("first")"></div>
			                <div class="my_prev" onclick="paginacionGrilla("prev")"></div>
			                <div class="my_next" onclick="paginacionGrilla("next")"></div>
			                <div class="my_last" onclick="paginacionGrilla("last")"></div>
			            </div>
			        </div>
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
						<!--
						<div class="renglon" style="overflow:visible; height:auto;">
							<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIva'.$opcGrillaContable.'">Iva</div>
							<div class="labelSimbolo" id="simboloIva'.$opcGrillaContable.'">$</div>
							<div class="labelTotal" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'">
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"> </div>
							<div class="labelSimbolo"  id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelTotal" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
						</div>-->

						<div class="renglon renglonTotal">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL </div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');

					'.$scriptRemision.'
					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";
					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
				</script>';

		return $body;
	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$id = 0,$id_activo_fijo= '',$codigo = '',$nombre = '', $unidad='', $dias_depreciar = '1',$costo = 0,$depreciacion_acumulada=0,$valor=0, $estado=''){
		//LA VARIABLE NCR ES EL nombre_consecutivo_referencia, SI ES Remision ENTONCES NO MOSTRAMOS EL BOTON BUSCAR ARTICULO, Y QUITAMOS EL EVENTO DE BUSCAR ARTICULO DEL CAMPO EAN
		$eventoEan         = 'onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);"';
		$btnBuscarArticulo = '<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo" style="'.$mostrarImagen.'">
								<img src="images/buscar20.png"/>
							</div>';

		$readonly_precio='';
		// if(user_permisos(61,'false') == 'false'){
		// 	$readonly_precio='readonly';
		// }
		// $readonly_descuento='';
		// if(user_permisos(76,'false') == 'false'){
		// 	$readonly_descuento='readonly';
		// }

		$srcImg           = 'images/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		$srcImgDescuento  = 'images/porcentaje.png';
		$titleDescuento	  = 'En porcentaje';
		$descuento2 	  = 0;
		//verificar si la factura esta cerrada para deshabiltar los campos y ocultar las imagenes

		$mostrarImagen     ='';
		$deshabiltar       ='';
		$idArticuloFactura =$id_activo_fijo;

		$idInsertArticuloFactura  =$id;
		$ivaArticuloFacturaCompra =$id_impuesto;

		//calcular subtotal del articulo
		$subtotal=$cantidad*$costo_unitario;

		if($id_activo_fijo == ''){
			$srcImg           = 'images/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}


		//verificar que descuento tiene el articulo si en pesos o en porcentaje
		// if ($tipoDescuento=='pesos') {
		// 	$srcImgDescuento  = 'images/pesos.png';
		// 	$titleDescuento	  = 'En  pesos';
		// }
		// else{ $descuento2 = $descuento; }

		// if ($nombre_unidad != '' && $cantidad_unidad!='') {
		// 	$mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad;
		// }

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' '.$eventoEan.'  value="'.$codigo.'" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.' value="'.$nombre.'" /></div>
				'.$btnBuscarArticulo.'

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="'.$unidad.'" /></div>
				<div class="campo"><input type="text" id="depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="'.$depreciacion_acumulada.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" readonly value="'.$dias_depreciar.'" '.$deshabiltar.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>



				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.' '.$readonly_precio.' value="'.$costo.'" readonly  value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'" '.$deshabiltar.'  value="'.$valor.'" onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="images/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Configurar Item" style="width:20px; float:left; margin-top:3px; '.$displayBtns.';cursor:pointer;'.$mostrarImagen.'"><img src="images/config16.png"/></div>

					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo '.$titleBtnDelete.'" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;">
						<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; background-color:#D94E37; margin:1px;">
                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                        </div>
					</div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idArticuloFactura.'" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idInsertArticuloFactura.'" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$id_impuesto.'">

				<script>
					//console.log("i: '.$impuesto.' -  val: '.$valor_impuesto.' id: '.$id_impuesto.'");
					// if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
					// 	arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					// }
					//llamamos la funcion para generar los calculos de la factura
					calculaValorTotalesDocumento("agregar",'.$valor.');

				</script>';

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$id = 0,$id_activo_fijo= '',$codigo = '',$nombre = '', $unidad='', $dias_depreciar = '1',$costo = 0,$depreciacion_acumulada=0,$valor=0){

		$srcImgDescuento  = 'img/pesos.png';

		if ($tipoDescuento=='porcentaje') {
			$srcImgDescuento  = 'img/porcentaje.png';
			$titleDescuento	  = 'En porcentaje';
		}
		//mostrar las unidades

		//echo eval('$costo=(2>1)? 25 : 1;');

		$body ='<div class="campo" style="width:40px !important;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'"  value="'.$codigo.'" readonly/>
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$nombre.'" readonly/></div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$unidad.'" readonly /></div>
				<div class="campo"><input type="text" id="depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$depreciacion_acumulada.'" /></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$dias_depreciar.'" readonly /></div>



				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' value="'.$costo.'" readonly value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"   readonly  value="'.$valor.'" /></div>


				<script>

					// if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
					// arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valor_impuesto.'"};
					// }

					// //llamamos la funcion para generar los calculos de la factura
					calculaValorTotalesDocumento("agregar",'.$valor.');

				</script>';

		return $body;
	}


?>