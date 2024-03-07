<?php
	//====================== CARGAR TITULOS DE LAS COLUMNAS ====================//
	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		$id_empresa = $_SESSION['EMPRESA'];
		$eventoObservacionGrilla = '';

		$sql = "SELECT
							id,
							id_activo_fijo,
							codigo_activo,
							nombre,
							unidad,
							numero_piezas,
							costo,
							deterioro_acumulado,
							depreciacion_acumulada,
							depreciacion_acumulada_niif
						FROM
							$tablaInventario
						WHERE
							$idTablaPrincipal = '$id'
						AND
							activo = 1
						AND
							id_empresa = $id_empresa";

		$query = mysql_query($sql,$link);

		$body =  '<div class="contenedorGrilla">
								<div class="contenedorHeadArticulos">
									<div class="headArticulos" id="head'.$opcGrillaContable.'">
										<div class="label" style="width:40px !important;"></div>
										<div class="label" style="width:calc(12% + 2px)">Codigo/EAN</div>
										<div class="labelNombreArticulo">Articulo</div>
										<div class="label">Unidad</div>
										<div class="label">Costo</div>
										<div class="label" title="Depreciacion Acumulada Colgaap">Dep. Acum. Local</div>
										<div class="label" title="Depreciacion Acumulada Niif">Dep. Acum. Niif</div>
										<div class="label" title="Deterioro Acumulado">Det. Acum.</div>
										<div style="float:right; min-width:80px;"></div>
									</div>
								</div>
								<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		$cont = 0;

		if($estado < 1){
			while($row = mysql_fetch_array($query)){
				$cont++;
				$body .= '<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
										'.cargaDivsUnidadesSave($cont,$opcGrillaContable,$row['id'],$row['id_activo_fijo'],$row['codigo_activo'],$row['nombre'],$row['unidad'],$row['numero_piezas'],$row['costo'],$row['deterioro_acumulado'],$row['depreciacion_acumulada'],$row['depreciacion_acumulada_niif']).'
									</div>';
			}
			$cont++;
			$body .= '<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
									'.cargaDivsUnidadesSave($cont,$opcGrillaContable).'
								</div>';

			$deshabilita             = '';
			$eventoObservacionGrilla = "onKeydown=inputObservacion".$opcGrillaContable."(event,this)";
		}
		else{
			while($row = mysql_fetch_array($query)){
				$cont++;
				$body .= '<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
										'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$row['id'],$row['id_activo_fijo'],$row['codigo_activo'],$row['nombre'],$row['unidad'],$row['numero_piezas'],$row['costo'],$row['deterioro_acumulado'],$row['depreciacion_acumulada'],$row['depreciacion_acumulada_niif']).'
									</div>';
			}

			$deshabilita = 'readonly';
		}

		$body .= '</div>
						</div>
						<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
							<div class="contenedorObservacionGeneral">
								<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
								<textarea id="observacion'.$opcGrillaContable.'" '.$eventoObservacionGrilla.' '.$deshabilita.'></textarea>
							</div>
							<div class="contenedorDetalleTotales" style="display: none;">
								<div class="renglon">
									<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
									<div class="labelSimbolo">$</div>
									<div class="labelTotal" id="subtotalAcumulado'.$opcGrillaContable.'">0</div>
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

							contArticulos'.$opcGrillaContable.' = '.$cont.';

							document.getElementById("observacion'.$opcGrillaContable.'").value = "'.$observacion.'";
							Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						</script>';

		return $body;
	}

	//==================== CARGAR FILAS EN DOCUMENTO EDITADO ===================//
	function cargaDivsUnidadesSave($cont='',$opcGrillaContable='',$id=0,$id_activo_fijo='',$codigo_activo='',$nombre='',$unidad='',$numero_piezas='',$costo=0,$deterioro_acumulado=0,$depreciacion_acumulada=0,$depreciacion_acumulada_niif=0){
		$srcImg                  = 'images/reload.png';
		$displayBtnReload        = 'display:none';
		$displayBtns             = '';
		$idArticuloFactura 			 = $id_activo_fijo;
		$idInsertArticuloFactura = $id;
		$unidades								 = ($unidad != "")? "$unidad x $numero_piezas" : "";

		if($id_activo_fijo == ''){
			$srcImg           = 'images/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}

		$body =  '<div class="campo" style="width:40px !important;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>
							<div class="campo" style="width:12%;">
								<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" value="'.$codigo_activo.'" />
							</div>
							<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly  value="'.$nombre.'" /></div>
							<div id="ventanaBuscarArticulo'.$opcGrillaContable.'_'.$cont.'" onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
								<img src="images/buscar20.png"/>
							</div>
							<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$unidades.'" /></div>
							<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" readonly value="'.$costo.'"  /></div>
							<div class="campo"><input type="text" id="depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'"  readonly value="'.$depreciacion_acumulada.'" /></div>
							<div class="campo"><input type="text" id="depreciacionAcumuladaNiif'.$opcGrillaContable.'_'.$cont.'" readonly value="'.$depreciacion_acumulada_niif.'"/></div>
							<div class="campo"><input type="text" id="deterioroAcumulado'.$opcGrillaContable.'_'.$cont.'" readonly  value="'.$deterioro_acumulado.'"/></div>
							<div style="float:right; min-width:80px;">
								<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px; cursor:pointer;'.$displayBtnReload.';"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
								<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="images/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Configurar Item" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/config16.png"/></div>
								<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo '.$titleBtnDelete.'" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;">
									<img src="images/delete.png"/>
								</div>
							</div>
							<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idArticuloFactura.'" />
							<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="'.$idInsertArticuloFactura.'" />';

		return $body;
	}

	//==================== CARGAR FILAS EN DOCUMENTO GENERADO ==================//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$id,$id_activo_fijo,$codigo_activo,$nombre,$unidad,$numero_piezas,$costo,$deterioro_acumulado,$depreciacion_acumulada,$depreciacion_acumulada_niif){

		$body =  '<div class="campo" style="width:40px !important;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:20px" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>
							<div class="campo" style="width:12%;">
								<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" readonly value="'.$codigo_activo.'">
							</div>
							<div class="campoNombreArticulo">
								<input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" readonly value="'.$nombre.'">
							</div>
							<div class="campo">
								<input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" readonly value="'.$unidad.' x '.$numero_piezas.'">
							</div>
							<div class="campo">
								<input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly value="'.$costo.'">
							</div>
							<div class="campo">
								<input type="text" id="depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly value="'.$depreciacion_acumulada.'">
							</div>
							<div class="campo">
								<input type="text" id="depreciacionAcumuladaNiif'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly value="'.$depreciacion_acumulada_niif.'">
							</div>
							<div class="campo">
								<input type="text" id="deterioroAcumulado'.$opcGrillaContable.'_'.$cont.'" style="text-align:right;" readonly  value="'.$deterioro_acumulado.'">
							</div>
							<div style="float:right; min-width:80px;">
								&nbsp;
							</div>';

		return $body;
	}
?>
