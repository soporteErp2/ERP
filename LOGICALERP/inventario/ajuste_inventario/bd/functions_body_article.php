<?php

	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

		$scriptDocumento          = ''; 						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision = '';
		$idRemision            = '';

		if($observacion == ''){ $observacion = mysql_result($query,0,'observacion'); }

		$arrayReplaceString = array("\n", "\r","<br>");
		$observacionFactura = str_replace($arrayReplaceString, " ", $observacion);


		$sql   = "SELECT
						id,
						id_ajuste_inventario,
						id_inventario,
						codigo,
						id_unidad_medida,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad_inventario,
						cantidad,
						costo_unitario,
						observaciones,
						inventariable
					FROM $tablaInventario
					WHERE $idTablaPrincipal='$id' AND activo = 1";
		$query = mysql_query($sql,$link);


		$body = '<script>
					subtotalAcumulado'.$opcGrillaContable.'          = 0;
					total'.$opcGrillaContable.'                      = 0;
					contArticulos'.$opcGrillaContable.'              = 0;
					arrayIva'.$opcGrillaContable.'                   = [];
				</script>

				<div class="contenedorGrilla">
				<div class="contenedorHeadArticulos">
					<div class="headArticulos" id="head'.$opcGrillaContable.'">
						<div class="label" style="width:40px !important;"></div>
						<div class="label" style="width:12%">Codigo/EAN</div>
						<div class="labelNombreArticulo">Articulo</div>
						<div class="label">Unidad</div>
						<div class="label">Cant. Inventario</div>
						<div class="label">Cant. Real</div>
						<div class="label">Costo</div>
						<div class="label">Ajuste</div>
						<div style="float:right; min-width:80px;"></div>
					</div>
				</div>
				<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		$readonly  = '';
		$cont      = 0;
		$contColor = 0;
		$docCruce  = '';


		// SI EL DOCUMENTO NO HA SIDO GENERADO
		if ($estado < 1) {
			while($row = mysql_fetch_array($query)){

				$row['cantidad']       = $row['cantidad'] * 1;
				$row['costo_unitario'] = $row['costo_unitario'] * 1;

				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSave($cont,$opcGrillaContable,$row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad_inventario'], $row['cantidad'], $row['costo_unitario'],$row['nombre_unidad_medida'],$row['cantidad_unidad_medida']).'
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

				$row['cantidad']       = $row['cantidad'] * 1;
				$row['costo_unitario'] = $row['costo_unitario'] * 1;

				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad_inventario'], $row['cantidad'], $row['costo_unitario'],$row['nombre_unidad_medida'],$row['cantidad_unidad_medida']).'
						</div>';
			}
			$mostarBoton='disable()';
			$deshabilita='readonly';
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
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal Ingresos</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalAcumuladoIngresos'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal Salidas</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalAcumuladoSalidas'.$opcGrillaContable.'">0</div>
						</div>

						<div class="renglon renglonTotal">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL </div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');

					'.$scriptDocumento.'
					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacionFactura.'";
					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
				</script>';

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$idInsertArticulo, $idArticulo, $codigo, $nombre, $cantidad_inventario=0, $cantidad=0, $costo_unitario=0,$nombre_unidad,$cantidad_unidad){

		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		$ajuste = $cantidad-$cantidad_inventario;
		if ($ajuste<>0) {
			$ajuste = ($ajuste>0)? '+'.abs($ajuste) : '-'.abs($ajuste);
			$color = ($ajuste>0)? '#28B463' : '#E74C3C ';
		}


		if($idInsertArticulo == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}

		if ($nombre_unidad != '' && $cantidad_unidad!='') {
			$mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad;
		}

		$body ="<div class='campo' style='width:40px !important;'>
					<div style='float:left; margin:3px 0 0 2px;'>$cont</div>
					<div style='float:left; width:20px' id='renderArticulo".$opcGrillaContable."_$cont'></div>
				</div>

				<div class='campo' style='width:12%;'>
					<input type='text' id='eanArticulo".$opcGrillaContable."_$cont'  onKeyup='buscarArticulo$opcGrillaContable(event,this);'  value='$codigo' />
				</div>

				<div class='campoNombreArticulo'><input type='text' id='nombreArticulo".$opcGrillaContable."_$cont' style='text-align:left;' readonly  value='$nombre' /></div>
				<div onclick='ventanaBuscarArticulo$opcGrillaContable($cont);' title='Buscar Articulo' class='iconBuscarArticulo'>
					<img src='img/buscar20.png'/>
				</div>

				<div class='campo'><input type='text' id='unidades".$opcGrillaContable."_$cont' style='text-align:left;' readonly value='$mostrarUnidad' /></div>
				<div class='campo'><input type='text' id='cantInvArticulo".$opcGrillaContable."_$cont' value='$cantidad_inventario' readonly/></div>

				<div class='campo '>
					<input type='text' id='cantidad".$opcGrillaContable."_$cont' value='$cantidad'  onKeyup='validarNumberArticulo$opcGrillaContable(event,this,\"double\",\"\");'/>
				</div>

				<div class='campo'><input type='text' id='costoArticulo".$opcGrillaContable."_$cont' value='$costo_unitario' onKeyup='guardarAuto$opcGrillaContable(event,this,$cont);'/></div>

				<div class='campo'><input type='text' id='ajuste".$opcGrillaContable."_$cont' readonly value='$ajuste' /></div>

				<div style='float:right; min-width:80px;'>
					<div onclick='guardarNewArticulo$opcGrillaContable($cont)' id='divImageSave".$opcGrillaContable."_$cont' title='Guardar Articulo' style='width:20px; float:left; margin-top:3px;cursor:pointer; $displayBtnReload; $mostrarImagen'><img src='$srcImg' id='imgSaveArticulo".$opcGrillaContable."_$cont'/></div>
					<div onclick='retrocederArticulo$opcGrillaContable($cont)' id='divImageDeshacer".$opcGrillaContable."_$cont' title='Deshacer Cambios' style='width:20px; float:left; margin-top:3px;cursor:pointer;display:none'><img src='img/deshacer.png' id='imgDeshacerArticulo".$opcGrillaContable."_$cont'></div>
					<div onclick='ventanaDescripcionArticulo$opcGrillaContable($cont)' id='descripcionArticulo".$opcGrillaContable."_$cont' title='Agregar Observacion' style='width:20px; float:left; margin-top:3px; $displayBtns cursor:pointer;$mostrarImagen'><img src='img/edit.png'/></div>

					<div onclick='deleteArticulo$opcGrillaContable($cont)' id='deleteArticulo".$opcGrillaContable."_$cont' title='Eliminar Articulo' style='width:20px; float:left; margin-top:3px; $displayBtns cursor:pointer;'>
						<img src='img/delete.png'/>
					</div>
				</div>

				<input type='hidden' id='idArticulo".$opcGrillaContable."_$cont' value='$idArticulo' />
				<input type='hidden' id='idInsertArticulo".$opcGrillaContable."_$cont' value='$idInsertArticulo' />

				<script>
					//llamamos la funcion para generar los calculos de la factura
					calcTotalDoc".$opcGrillaContable."($cantidad_inventario,$cantidad,$costo_unitario,'agregar','$tipo');
					document.getElementById('ajuste".$opcGrillaContable."_$cont').style.color           = '$color'
				</script>";

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$idInsertArticulo, $idArticulo, $codigo, $nombre, $cantidad_inventario, $cantidad, $costo_unitario,$nombre_unidad,$cantidad_unidad){

		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		$ajuste = $cantidad-$cantidad_inventario;
		if ($ajuste<>0) {
			$ajuste = ($ajuste>0)? '+'.abs($ajuste) : '-'.abs($ajuste);
			$color = ($ajuste>0)? '#28B463' : '#E74C3C ';
		}

			// if ($ajuste<>0) {
			// 	$ajuste = ($ajuste>0)? '-'.abs($ajuste) : '+'.abs($ajuste);
			// 	$tipo   = ($ajuste>0)? "ingreso" : "salida";
			// }
			// if ($ajuste==0) {
			// 	$ajuste = '-'.abs($cantidad);
			// 	$tipo   = "salida";
			// }



		if($idInsertArticulo == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}

		if ($nombre_unidad != '' && $cantidad_unidad!='') {
			$mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad;
		}

		// if ($tipo=='ingreso') {
		// 	// $script = "console.log('$codigo	$nombre	$cantidad_inventario	$cantidad	$costo_unitario	$ajuste');";
		// 	$arrayReplace = array('-','+' );
		// 	$ajusteShow= str_replace($arrayReplace, '', $ajuste);
		// 	$script = "console.log('$ajusteShow * $costo_unitario = ".($ajusteShow*$costo_unitario)." ');";
		// }

		// $arrayReplace = array('-','+' );
		// $ajusteShow= str_replace($arrayReplace, '', $ajuste);

		$body ="<div class='campo' style='width:40px !important;'>
					<div style='float:left; margin:3px 0 0 2px;'>$cont</div>
					<div style='float:left; width:20px' id='renderArticulo".$opcGrillaContable."_$cont'></div>
				</div>

				<div class='campo' style='width:12%;'>
					<input type='text' id='eanArticulo".$opcGrillaContable."_$cont'  readonly  value='$codigo' />
				</div>

				<div class='campoNombreArticulo'><input type='text' id='nombreArticulo".$opcGrillaContable."_$cont' style='text-align:left;' readonly  value='$nombre' /></div>

				<div class='campo'><input type='text' id='unidades".$opcGrillaContable."_$cont' style='text-align:left;' readonly value='$mostrarUnidad' /></div>
				<div class='campo'><input type='text' id='cantInvArticulo".$opcGrillaContable."_$cont' value='$cantidad_inventario' readonly/></div>

				<div class='campo '>
					<input type='text' id='cantidad".$opcGrillaContable."_$cont' value='$cantidad' readonly />
				</div>

				<div class='campo'><input type='text' id='costoArticulo".$opcGrillaContable."_$cont' value='$costo_unitario' readonly /></div>

				<div class='campo'><input type='text' id='ajuste".$opcGrillaContable."_$cont' readonly value='$ajuste' /></div>

				<!--<div style='float:right; min-width:80px;'>
					<div onclick='guardarNewArticulo$opcGrillaContable($cont)' id='divImageSave".$opcGrillaContable."_$cont' title='Guardar Articulo' style='width:20px; float:left; margin-top:3px;cursor:pointer; $displayBtnReload; $mostrarImagen'><img src='$srcImg' id='imgSaveArticulo".$opcGrillaContable."_$cont'/></div>
					<div onclick='retrocederArticulo$opcGrillaContable($cont)' id='divImageDeshacer".$opcGrillaContable."_$cont' title='Deshacer Cambios' style='width:20px; float:left; margin-top:3px;cursor:pointer;display:none'><img src='img/deshacer.png' id='imgDeshacerArticulo".$opcGrillaContable."_$cont'></div>
					<div onclick='ventanaDescripcionArticulo$opcGrillaContable($cont)' id='descripcionArticulo".$opcGrillaContable."_$cont' title='Agregar Observacion' style='width:20px; float:left; margin-top:3px; $displayBtns cursor:pointer;$mostrarImagen'><img src='img/edit.png'/></div>

					<div onclick='deleteArticulo$opcGrillaContable($cont)' id='deleteArticulo".$opcGrillaContable."_$cont' title='Eliminar Articulo' style='width:20px; float:left; margin-top:3px; $displayBtns cursor:pointer;'>
						<img src='img/delete.png'/>
					</div>
				</div>-->

				<input type='hidden' id='idArticulo".$opcGrillaContable."_$cont' value='$idArticulo' />
				<input type='hidden' id='idInsertArticulo".$opcGrillaContable."_$cont' value='$idInsertArticulo' />

				<script>
					//llamamos la funcion para generar los calculos de la factura
					// $script
					calcTotalDoc".$opcGrillaContable."($cantidad_inventario,$cantidad,$costo_unitario,'agregar','');
					document.getElementById('ajuste".$opcGrillaContable."_$cont').style.color           = '$color'
				</script>";

		return $body;
	}

?>