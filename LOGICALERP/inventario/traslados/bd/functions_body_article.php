<?php

    $styleCamposEliminados = 'display:none';//EN CASO QUE SE REQUIERA VOLVER A MOSTRAR LOS CAMPOS ELIMINADOS

	function cargaArticulosSave($id,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$mysql){

        global $styleCamposEliminados;

		$scriptRemision          = ''; 						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		$sql = "SELECT
					id,
					id_traslado,
					id_inventario,
					codigo,
					id_unidad_medida,
					nombre_unidad_medida,
					cantidad_unidad_medida,
					nombre,
					cantidad,
					costo_unitario,
					observaciones
				FROM $tablaInventario
				WHERE $idTablaPrincipal='$id' AND activo = 1";
		$query = $mysql->query($sql,$mysql->link);

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

		if ($estado < 1) {
			while($row = $mysql->fetch_array($query)){
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSave($cont,$opcGrillaContable, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['nombre_unidad_medida'],$row['cantidad_unidad_medida']).'
						</div>';
			}
			$cont++;
			$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
						'.cargaDivsUnidadesSave($cont,$opcGrillaContable).'
					</div>';

			$deshabilita             = '';
			$eventoObservacionGrilla ="onKeydown=inputObservacion".$opcGrillaContable."(event,this)";
		}
		else{

			while($row = $mysql->fetch_array($query)){

				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable, $row['id'], $row['id_inventario'], $row['codigo'], $row['nombre'], $row['cantidad'], $row['nombre_unidad_medida'],$row['cantidad_unidad_medida']).'
						</div>';
			}
			$deshabilita='readonly';
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

					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";
					if(Ext.getCmp("btnNueva'.$opcGrillaContable.'")){
						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					}

				</script>';

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSave($cont,$opcGrillaContable,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0, $nombre_unidad='',$cantidad_unidad=''){
		$eventoEan         = 'onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);"';
		$btnBuscarArticulo = '<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo" style="'.$mostrarImagen.'">
								<img src="img/buscar20.png"/>
							</div>';

		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		$mostrarImagen     ='';
		$deshabiltar       ='';
		$eventoTipoDesc    =' onclick="tipoDescuentoArticulo('.$cont.')"';
		$idArticuloFactura =$id_inventario;


		if($id_inventario == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}

		if ($nombre_unidad != '' && $cantidad_unidad!='') {
			$mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad;
		}

		$body ="<div class='campo' style='width:40px !important; overflow:hidden;'>
					<div style='float:left; margin:3px 0 0 2px;'>$cont</div>
					<div style='float:left; width:18px; overflow:hidden;' id='renderArticulo$opcGrillaContable"."_$cont'></div>
				</div>

				<div class='campo' >
					<input type='text' id='eanArticulo$opcGrillaContable"."_$cont' onKeyup='buscarArticulo$opcGrillaContable(event,this);' value='$codigo' />
				</div>

				<div class='campoNombreArticulo'>
					<input type='text' id='nombreArticulo$opcGrillaContable"."_$cont' style='text-align:left;' readonly value='$nombre'/>
				</div>
				<div onclick='ventanaBuscarArticulo$opcGrillaContable($cont);' title='Buscar Articulo' class='iconBuscarArticulo'>
					<img src='img/buscar20.png'/>
				</div>

				<div class='campo'>
					<input type='text' id='unidades$opcGrillaContable"."_$cont' style='text-align:left;' readonly value='$mostrarUnidad' />
				</div>
				<div class='campo'>
					<input type='text' id='cantArticulo$opcGrillaContable"."_$cont'  onKeyup='guardarAuto$opcGrillaContable(event,this,$cont);' value='$cantidad'/>
				</div>

				<div style='float:right; min-width:80px;'>
					<div onclick='guardarNewArticulo$opcGrillaContable($cont)' id='divImageSave$opcGrillaContable"."_$cont' title='Guardar Articulo' style='width:20px; float:left; margin-top:3px;cursor:pointer;$displayBtnReload'  ><img src='$srcImg' id='imgSaveArticulo$opcGrillaContable"."_$cont'/></div>
					<div onclick='retrocederArticulo$opcGrillaContable($cont)' id='divImageDeshacer$opcGrillaContable"."_$cont' title='Deshacer Cambios' style='width:20px; float:left; margin-top:3px;cursor:pointer;display:none'><img src='img/deshacer.png' id='imgDeshacerArticulo$opcGrillaContable"."_$cont'></div>
					<div onclick='ventanaDescripcionArticulo$opcGrillaContable($cont)' id='descripcionArticulo$opcGrillaContable"."_$cont' title='Agregar Observacion' style='width:20px; float:left; margin-top:3px; $displayBtns cursor:pointer;'><img src='img/edit.png'/></div>
					<div onclick='deleteArticulo$opcGrillaContable($cont)' id='deleteArticulo$opcGrillaContable"."_$cont' title='Eliminar Articulo' style='width:20px; float:left; margin-top:3px;$displayBtns cursor:pointer;'><img src='img/delete.png'/></div>
				</div>

				<input type='hidden' id='idArticulo$opcGrillaContable"."_$cont' value='$id_inventario' />
				<input type='hidden' class='classInputInsertArticulo' id='idInsertArticulo$opcGrillaContable"."_$cont' value='$id' />
				<input type='hidden' id='ivaArticulo$opcGrillaContable"."_$cont' value='0' >";

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadas($cont,$opcGrillaContable,$id = 0,$id_inventario = '',$codigo = '',$nombre = '',$cantidad = 0, $nombre_unidad='',$cantidad_unidad=''){

		if ($nombre_unidad != '' && $cantidad_unidad!='') {
			$mostrarUnidad=$nombre_unidad.' x '.$cantidad_unidad;
		}

		$body ="<div class='campo' style='width:40px !important; overflow:hidden;'>
					<div style='float:left; margin:3px 0 0 2px;'>$cont</div>
					<div style='float:left; width:18px; overflow:hidden;' id='renderArticulo$opcGrillaContable"."_$cont'></div>
				</div>

				<div class='campo' >
					<input type='text' id='eanArticulo$opcGrillaContable"."_$cont' readonly value='$codigo' />
				</div>

				<div class='campoNombreArticulo'>
					<input type='text' id='nombreArticulo$opcGrillaContable"."_$cont' style='text-align:left;' readonly value='$nombre'/>
				</div>

				<div class='campo'>
					<input type='text' id='unidades$opcGrillaContable"."_$cont' style='text-align:left;' readonly value='$mostrarUnidad' />
				</div>
				<div class='campo'>
					<input type='text' id='cantArticulo$opcGrillaContable"."_$cont' readonly value='$cantidad'/>
				</div>
				<div id='divImageSave$opcGrillaContable"."_$cont' title='Guardar Articulo' style='width:20px; float:left; margin-top:3px;cursor:pointer;display:none;'  ><img src='$srcImg' id='imgSaveArticulo$opcGrillaContable"."_$cont'/></div>
				<input type='hidden' id='idArticulo$opcGrillaContable"."_$cont' value='$id' />

				";

		return $body;
	}


?>