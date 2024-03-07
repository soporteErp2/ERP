<?php

	//==================================// FUNCIONES DE LA INTERFAZ CON EL TERCERO //==================================//
	//*****************************************************************************************************************//

	function cargaArticulosSaveConTercero($id,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$cont                    = 0;						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision   = '';
		$idRemision              = '';

		$sql   = "SELECT id,id_puc,cuenta_puc,descripcion_puc,debe,haber,id_tercero,tercero,tipo_documento_cruce,id_tabla_referencia,id_documento_cruce,numero_documento_cruce FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' ORDER BY cuenta_puc ASC";
		$query = mysql_query($sql,$link);

		$body = '<script>
					debitoAcumulado'.$opcGrillaContable.'  = 0.00;
					creditoAcumulado'.$opcGrillaContable.' = 0.00;
					total'.$opcGrillaContable.'            = 0.00;
				</script>
				<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label'.$opcGrillaContable.'" style="width:40px !important;"></div>
							<div class="label'.$opcGrillaContable.'" style="width:95px;">Cuenta</div>
							<div class="label'.$opcGrillaContable.' campoDescripcion">Descripcion</div>
							<div class="label'.$opcGrillaContable.' campoDescripcion">Tercero</div>
							<div class="label'.$opcGrillaContable.' opcionalCruce">Doc. Cruce</div>
							<div class="label'.$opcGrillaContable.' opcionalCruce">N.Doc.Cruce</div>
							<div class="label'.$opcGrillaContable.'">Debito</div>
							<div class="label'.$opcGrillaContable.'">Credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

			// SI LA COTIZACION NO HA SIDO CEERRADA POR UNA FACTURA CARGA INPUTS READONLY
			if ($estado < 1) {
				while($row = mysql_fetch_array($query)){
					$cont++;
					$row['numero_documento_cruce'] = ($row['numero_documento_cruce'] > 0)? $row['numero_documento_cruce']: '';
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable, $row['id'], $row['id_puc'], $row['cuenta_puc'], $row['descripcion_puc'], $row['debe'], $row['haber'],$row['id_tercero'],$row['tercero'], $row['tipo_documento_cruce'],$row['id_tabla_referencia'],$row['id_documento_cruce'], $row['prefijo_documento_cruce'], $row['numero_documento_cruce'],$row['id_tabla_referencia']).'
							</div>';
				}
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable).'
						</div>';
				($estado=='1') ? $mostarBoton='disable()':$mostarBoton='enable()';
				$eventoObservacionGrilla="onKeydown=inputObservacion".$opcGrillaContable."(event,this)";
				$deshabilita='';

			}
			else{
				while($row = mysql_fetch_array($query)){
					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrillaContable, $row['id'], $row['id_puc'], $row['cuenta_puc'], $row['descripcion_puc'], $row['debe'], $row['haber'],$row['id_tercero'],$row['tercero'],$row['tipo_documento_cruce'], $row['prefijo_documento_cruce'], $row['numero_documento_cruce']).'
							</div>';
				}
				$mostarBoton='disable()';
				$deshabilita='readonly';
			}


		$body .='	</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="debitoAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal" id="creditoAcumulado'.$opcGrillaContable.'">0</div>
						</div>

						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL (DEBITO - CREDITO)</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');
					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";
					Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").enable();
        			Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").'.$mostarBoton.';
				</script>';

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero=0,$tercero='',$tipo_documento_cruce='',$id_tabla_referencia=0,$id_documento_cruce=0,$prefijo_documento_cruce='',$numero_documento_cruce='',$id_tabla_referencia=0){
		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		if($id == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}

		//ESTABLECER LA IMAGEN Y EL EVENTO DEL BOTON BUSCAR TERCERO
		$imagenBoton = 'buscar20';
		$eventoBoton = 'buscarVentanaTercero';

		$imagenBotonDocumentoCruce = 'buscar20';
		$eventoBotonDocumentoCruce = 'ventanaBuscarDocumentoCruce';

		if ($tercero!='') {
			$imagenBoton = 'delete';
			$eventoBoton = 'eliminaTercero';
		}
		if ($numero_documento_cruce!='') {
			$imagenBotonDocumentoCruce = 'delete';
			$eventoBotonDocumentoCruce = 'eliminaDocumentoCruce';
		}

		$script = '';
		if ($cuenta!=""){ $script='arrayCuentaPago['.$cont.']='.$cuenta.';'; }

		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoLiquidacionProvision" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);"  value="'.$cuenta.'" />
				</div>

				<div class="campoLiquidacionProvision campoDescripcion"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" /></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoLiquidacionProvision campoDescripcion"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>
				<div class="iconBuscarArticulo">
					<img src="img/'.$imagenBoton.'.png" id="imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" onclick="'.$eventoBoton.''.$opcGrillaContable.'('.$cont.')" title="Buscar Tercero" />
				</div>

				<div class="campoLiquidacionProvision opcionalCruce">
				 	<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly style="text-align:left;" value="'.$tipo_documento_cruce.'">
				 </div>
				 <div class="iconBuscarArticulo opcionalCruce">
					<img onclick="'.$eventoBotonDocumentoCruce.$opcGrillaContable.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/'.$imagenBotonDocumentoCruce.'.png" />
				</div>

				<div class="campoLiquidacionProvision opcionalCruce">
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');" value="'.$numero_documento_cruce.'" />
				</div>

				<div class="campoLiquidacionProvision"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');" value="'.$debe.'" /></div>
				<div class="campoLiquidacionProvision"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="'.$haber.'" /></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="deleteCuenta'.$opcGrillaContable.'('.$cont.')" id="deleteCuenta'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;"><img src="img/delete.png" /></div>
					<div onclick="cambiaCuentaNiif'.$opcGrillaContable.'('.$cont.')" id="configurarCuenta'.$opcGrillaContable.'_'.$cont.'" title="Configurar Cuenta Niif" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtns.'"><img src="img/config16.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />
				<input type="hidden" id="idTercero'.$opcGrillaContable.'_'.$cont.'" value="'.$id_tercero.'" />
				<input type="hidden" id="idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" value="'.$id_documento_cruce.'" />
				<input type="hidden" id="idTablaReferencia'.$opcGrillaContable.'_'.$cont.'" value="'.$id_tabla_referencia.'" />

				<script>
					document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value = "'.$tipo_documento_cruce.'";
					'.$script.'
					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");
				</script>';

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrillaContable,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$tercero,$tipo_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce){

		$body ='<div class="campoLiquidacionProvision" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoLiquidacionProvision" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$cuenta.'" readonly/>
				</div>

				<div class="campoLiquidacionProvision campoDescripcion"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" readonly/></div>
				<div class="campoLiquidacionProvision campoDescripcion"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>

				<div class="campoLiquidacionProvision opcionalCruce"><input type="text" id="tipodocumentoCruce'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tipo_documento_cruce.'" readonly/></div>

				<div class="campoLiquidacionProvision opcionalCruce">
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroNotaContableCruce" value="'.$numero_documento_cruce.'" readonly/>
				</div>

				<div class="campoLiquidacionProvision"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" value="'.$debe.'" readonly/></div>
				<div class="campoLiquidacionProvision"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" value="'.$haber.'" readonly/></div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />

				<script>
					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");
				</script>';

		return $body;
	}


?>