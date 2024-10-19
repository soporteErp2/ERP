<?php
	function cargaArticulosSaveConTercero($id,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$cont                    = 0;						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		$sql   = "SELECT
					id,
					id_puc,
					cuenta,
					descripcion,
					debito,
					credito,
					id_tercero,
					nit_tercero,
					tercero,
					id_documento_cruce,
					tipo_documento_cruce,
					prefijo_documento_cruce,
					numero_documento_cruce,
					id_tabla_referencia
				FROM $tablaCuentasNota
				WHERE $idTablaPrincipal='$id'
				ORDER BY cuenta ASC";
		$query = mysql_query($sql,$link);

		$body = '<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="labelGrilla" style="width:40px !important;"></div>
							<div class="labelGrilla" style="width:95px">Cuenta</div>
							<div class="labelGrilla" style="width:30%">Tercero</div>
							<div class="labelGrillaDocCruce">Documento Cruce</div>
							<div class="labelGrilla">Debito</div>
							<div class="labelGrilla">credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

			// SI LA COTIZACION NO HA SIDO CERRADA POR UNA FACTURA CARGA INPUTS READONLY
			if ($estado < 1) {
				while($row = mysql_fetch_array($query)){
					$row['debito']  = $row['debito'] * 1;
					$row['credito'] = $row['credito'] * 1;

					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable,$row['id'],$row['id_puc'],$row['cuenta'],$row['descripcion'],$row['debito'],$row['credito'],$row['id_tercero'],$row['nit_tercero'],$row['tercero'],$row['id_documento_cruce'],$row['tipo_documento_cruce'],$row['prefijo_documento_cruce'],$row['numero_documento_cruce'],$row['id_tabla_referencia'],$link ).'
							</div>';
				}
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable).'
						</div>';

				$deshabilita = '';
				$mostarBoton = ($estado=='1') ? 'disable()':'enable()';
				$eventoObservacionGrilla="onKeydown=inputObservacion".$opcGrillaContable."(event,this)";

			}
			else{
				while($row = mysql_fetch_array($query)){
					$row['debito']  = $row['debito'] * 1;
					$row['credito'] = $row['credito'] * 1;

					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrillaContable,$row['id'],$row['id_puc'],$row['cuenta'],$row['descripcion'],$row['debito'],$row['credito'],$row['id_tercero'],$row['nit_tercero'],$row['tercero'],$row['id_documento_cruce'],$row['tipo_documento_cruce'],$row['prefijo_documento_cruce'],$row['numero_documento_cruce'],$row['id_tabla_referencia']  ).'
							</div>';
				}
				$mostarBoton='disable()';
				$deshabilita='readonly';
			}

		$styleObs = ($estado<1)? 'onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"': 'readonly';
		$body .='</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'" '.$styleObs.'></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Saldo Debito</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalDebito'.$opcGrillaContable.'">0</div>
						</div>

						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Saldo Credito</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalCredito'.$opcGrillaContable.'">0</div>
						</div>

						<div class="renglon renglonTotal">
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL CUENTA PAGO</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');

					contArticulos'.$opcGrillaContable.'= '.$cont.';
					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";
				</script>';

		return $body;
	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable,$id=0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$nit_tercero,$tercero,$id_documento_cruce,$tipo_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$id_tabla_referencia,$link){

		$numero_documento_cruce=($numero_documento_cruce==0)? '' : $numero_documento_cruce ;

		$srcImg           = 'img/reload.png';
		$displayBtnReload = 'display:none';
		$displayBtns      = '';

		if($id_puc == ''){
			$srcImg           = 'img/save_true.png';
			$displayBtnReload = '';
			$displayBtns      = 'display:none;';
		}

		//ESTABLECER LA IMAGEN Y EL EVENTO DEL BOTON BUSCAR TERCERO
		$imagenBoton='buscar20';
		$eventoBoton='buscarVentanaTercero';

		$imagenBotonDocumentoCruce='buscar20';
		$eventoBotonDocumentoCruce='ventanaBuscarDocumentoCruce';

		if ($tercero!='') {
			$imagenBoton='eliminar';
			$eventoBoton='eliminaTercero';
		}
		if ($numero_documento_cruce!=''){
			$imagenBotonDocumentoCruce='eliminar';
			$eventoBotonDocumentoCruce='eliminaDocumentoCruce';
		}
		$id_empresa=$_SESSION['EMPRESA'];
		// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
		$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_puc; ";
		$query=mysql_query($sql,$link);
		$centro_costo = mysql_result($query,0,'centro_costo');

		$sql = "SELECT id_centro_costos FROM comprobante_egreso_cuentas WHERE activo=1 AND id=$id;";
		$query = mysql_query($sql,$link);
		$id_centro_costos = mysql_result($query,0,'id_centro_costos');

		$contenido = ( $centro_costo=='Si' && ($id_centro_costos=='' || $id_centro_costos==0) )? '<div style="float:right;">'.$cont.'</div><div style="float:left;"><img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'></div>' : $cont ;

		$body ='<div class="campoGrilla" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;" id="label_cont_'.$cont.'">'.$contenido.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoGrilla" style="width:95px;">
					<input type="text" style="text-align:left;"  id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);"  value="'.$cuenta.'" />
				</div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoGrilla flex" style="width:30%;">
					<input type="text" id="documento_tercero_'.$opcGrillaContable.'_'.$cont.'" onkeyup="debounceRequest(this.value, event,'.$cont.')" value="'.$nit_tercero.'" style="border-right:1px solid #CCC;" placeholder="cedula, nit...">
					<input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" title="'.$tercero.'" readonly/>
				</div>
				<div class="iconBuscarArticulo">
					<img src="img/'.$imagenBoton.'.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" title="Buscar Tercero" id="imgBuscarTercero_'.$cont.'" onclick="'.$eventoBoton.''.$opcGrillaContable.'('.$cont.')"/>
				</div>

				<div class="campoGrilla" style="width:5%">
					<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" value="'.$tipo_documento_cruce.'" readonly/>
				</div>

				<div class="campoGrilla">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputPrefijoFacturaComprobanteEgreso" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'mayuscula\',\''.$cont.'\');"  value="'.$prefijo_documento_cruce.'"/>
					-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroFacturaComprobanteEgreso" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');" value="'.$numero_documento_cruce.'" />
				</div>
				<div class="iconBuscarArticulo">
					<img onclick="'.$eventoBotonDocumentoCruce.$opcGrillaContable.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/'.$imagenBotonDocumentoCruce.'.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" />
				</div>

				<div class="campoGrilla"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" style="text-align:left;"  value="'.$debe.'" /></div>
				<div class="campoGrilla"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" style="text-align:left;"  value="'.$haber.'" /></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaObservacionCuenta'.$opcGrillaContable.'('.$cont.')" id="descripcionCuenta'.$opcGrillaContable.'_'.$cont.'" title="Otras Configuraciones" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;"><img src="img/config16.png"/></div>
					<div onclick="deleteCuenta'.$opcGrillaContable.'('.$cont.')" id="deleteCuenta'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;"><img src="img/delete.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />
				<input type="hidden" id="idTercero'.$opcGrillaContable.'_'.$cont.'" value="'.$id_tercero.'" />
				<input type="hidden" id="idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" value="'.$id_documento_cruce.'" />
				<input type="hidden" id="idTablaReferencia'.$opcGrillaContable.'_'.$cont.'" value="'.$id_tabla_referencia.'" />

				<script>
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");		//llamamos la funcion para generar los calculos de la factura
				</script>';
		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrillaContable,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$tercero,$id_documento_cruce,$tipo_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce){

		$numero_documento_cruce=($numero_documento_cruce==0)? '' : $numero_documento_cruce ;

		$body ='<div class="campoGrilla" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoGrilla" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" readonly  value="'.$cuenta.'" />
				</div>

				<div class="campoGrilla" style="width:30%;"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" title="'.$tercero.'" readonly/></div>

				<div class="campoGrilla" style="width:5%"><input type="text" id="tipodocumentoCruce'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;"  value="'.$tipo_documento_cruce.'" readonly/></div>

				<div class="campoGrilla">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputPrefijoFacturaComprobanteEgreso" value="'.$prefijo_documento_cruce.'" readonly/>-
					<input title="Numero" type="text" style="width: calc(60% - 1px) !important;" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroFacturaComprobanteEgreso" value="'.$numero_documento_cruce.'" readonly/>
				</div>

				<div class="campoGrilla"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;"  value="'.$debe.'" readonly/></div>
				<div class="campoGrilla"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;"  value="'.$haber.'" readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="ventanaObservacionCuenta'.$opcGrillaContable.'('.$cont.')" id="descripcionCuenta'.$opcGrillaContable.'_'.$cont.'" title="Descripcion" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;"><img src="img/config16.png"/></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />

				<script>

					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");

				</script>';

		return $body;
	}


?>