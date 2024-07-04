<?php

	//==================================// FUNCIONES DE LA INTERFAZ CON EL TERCERO //==================================//
	//*****************************************************************************************************************//

	function loadItemsSave($id_documento,$observacion,$estado,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$link){
		$cont                    = 0;						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision   = '';
		$idRemision              = '';

		echo $sql   = "SELECT A.consecutivo_documento,
						A.tipo_documento,
						A.tipo_documento_extendido,
						A.tipo_documento_cruce,
						A.numero_documento_cruce,
						A.fecha,
						SUM(A.debe - A.haber) AS saldo,
						A.nit_tercero,
						A.tercero
					FROM conciliacion_bancos_items AS C INNER JOIN asientos_colgaap AS A ON(
						A.activo=1
						AND C.id_asiento=A.id
						AND A.id_empresa='$id_empresa'
						)
					WHERE C.id='$id_documento'
					ORDER BY cuenta_puc ASC";
		$query = mysql_query($sql,$link);

		$body = '<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrilla.'">
							<div class="labelNotaGeneral" style="width:40px !important;"></div>
							<div class="labelNotaGeneral" style="width:95px;">Cuenta</div>
							<div class="labelNotaGeneral campoDescripcion">Descripcion</div>
							<div class="labelNotaGeneral campoDescripcion">Tercero</div>
							<div class="labelNotaGeneral opcionalCruce">Doc. Cruce</div>
							<div class="labelNotaGeneral opcionalCruce">N.Doc.Cruce</div>
							<div class="labelNotaGeneral">Debito</div>
							<div class="labelNotaGeneral">Credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrilla.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrilla.'\')">';

			// SI LA COTIZACION NO HA SIDO CEERRADA POR UNA FACTURA CARGA INPUTS READONLY
			if ($estado < 1) {
				while($row = mysql_fetch_array($query)){
					$cont++;
					$row['numero_documento_cruce'] = ($row['numero_documento_cruce'] > 0)? $row['numero_documento_cruce']: '';
					$body .='<div class="bodyDivArticulos'.$opcGrilla.'" id="bodyDivArticulos'.$opcGrilla.'_'.$cont.'">
								'.cargaDivsUnidadesSaveConTercero($cont,$opcGrilla, $row['id'], $row['id_puc'], $row['cuenta_puc'], $row['descripcion_puc'], $row['debe'], $row['haber'],$row['id_tercero'],$row['tercero'], $row['tipo_documento_cruce'],$row['id_documento_cruce'], $row['prefijo_documento_cruce'], $row['numero_documento_cruce']).'
							</div>';
				}
				$cont++;
				$body .='<div class="bodyDivArticulos'.$opcGrilla.'" id="bodyDivArticulos'.$opcGrilla.'_'.$cont.'">
							'.cargaDivsUnidadesSaveConTercero($cont,$opcGrilla).'
						</div>';
				($estado=='1') ? $mostarBoton='disable()':$mostarBoton='enable()';
				$eventoObservacionGrilla="onKeydown=inputObservacion".$opcGrilla."(event,this)";
				$deshabilita='';

			}
			else{
				while($row = mysql_fetch_array($query)){
					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrilla.'" id="bodyDivArticulos'.$opcGrilla.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrilla, $row['id'], $row['id_puc'], $row['cuenta_puc'], $row['descripcion_puc'], $row['debe'], $row['haber'],$row['id_tercero'],$row['tercero'],$row['tipo_documento_cruce'], $row['prefijo_documento_cruce'], $row['numero_documento_cruce']).'
							</div>';
				}
				$mostarBoton='disable()';
				$deshabilita='readonly';
			}

			$opciones=($estado==1)? 'readonly' : 'onKeydown="inputObservacion'.$opcGrilla.'(event,this)"';

		$body .='	</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrilla.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrilla.'"  '.$opciones.'></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="debitoAcumulado'.$opcGrilla.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal" id="creditoAcumulado'.$opcGrilla.'">0</div>
						</div>

						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL (DEBITO - CREDITO)</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrilla.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrilla.'"),\'head'.$opcGrilla.'\');
					contItems'.$opcGrilla.'= '.$cont.';

					document.getElementById("observacion'.$opcGrilla.'").value="'.$observacion.'";
					Ext.getCmp("Btn_nueva_'.$opcGrilla.'").enable();
        			Ext.getCmp("Btn_guardar_'.$opcGrilla.'").'.$mostarBoton.';
				</script>';

		return $body;

	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSaveConTercero($cont,$opcGrilla,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$tercero,$tipo_documento_cruce,$id_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce){
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
			$imagenBoton = 'eliminar';
			$eventoBoton = 'eliminaTercero';
		}
		if ($numero_documento_cruce!='') {
			$imagenBotonDocumentoCruce = 'eliminar';
			$eventoBotonDocumentoCruce = 'eliminaDocumentoCruce';
		}

		$script = '';
		if ($cuenta!=""){ $script='arrayCuentaPago['.$cont.']='.$cuenta.';'; }

		$body ='<div class="campoNotaGeneral" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrilla.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrilla.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrilla.'(event,this);"  value="'.$cuenta.'" />
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="descripcion'.$opcGrilla.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" /></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrilla.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="tercero'.$opcGrilla.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>
				<div class="iconBuscarArticulo">
					<img src="img/'.$imagenBoton.'.png" id="imgBuscarTercero'.$opcGrilla.'_'.$cont.'" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" onclick="'.$eventoBoton.''.$opcGrilla.'('.$cont.')" title="Buscar Tercero" />
				</div>

				<div class="campoNotaGeneral opcionalCruce">
				 	<input type="text" id="documentoCruce'.$opcGrilla.'_'.$cont.'" readonly style="text-align:left;" value="'.$tipo_documento_cruce.'">
				 </div>
				 <div class="iconBuscarArticulo opcionalCruce">
					<img onclick="'.$eventoBotonDocumentoCruce.$opcGrilla.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/'.$imagenBotonDocumentoCruce.'.png" />
				</div>

				<div class="campoNotaGeneral opcionalCruce">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrilla.'_'.$cont.'" class="inputPrefijoNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'mayuscula\',\''.$cont.'\');"  value="'.$prefijo_documento_cruce.'"/>
					-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrilla.'_'.$cont.'" class="inputNumeroNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'\',\''.$cont.'\');" value="'.$numero_documento_cruce.'" />
				</div>

				<div class="campoNotaGeneral"><input type="text" id="debito'.$opcGrilla.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'double\',\''.$cont.'\');" value="'.$debe.'" /></div>
				<div class="campoNotaGeneral"><input type="text" id="credito'.$opcGrilla.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrilla.'(event,this,'.$cont.');" value="'.$haber.'" /></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewCuenta'.$opcGrilla.'('.$cont.')" id="divImageSave'.$opcGrilla.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtnReload.';'.$mostrarImagen.'"><img src="'.$srcImg.'" id="imgSaveArticulo'.$opcGrilla.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrilla.'('.$cont.')" id="divImageDeshacer'.$opcGrilla.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrilla.'_'.$cont.'"></div>
					<div onclick="deleteCuenta'.$opcGrilla.'('.$cont.')" id="deleteCuenta'.$opcGrilla.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; '.$displayBtns.'cursor:pointer;"><img src="img/delete.png" /></div>
					<div onclick="cambiaCuentaNiif'.$opcGrilla.'('.$cont.')" id="configurarCuenta'.$opcGrilla.'_'.$cont.'" title="Configurar Cuenta Niif" style="width:20px; float:left; margin-top:3px;cursor:pointer;'.$displayBtns.'"><img src="img/config16.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrilla.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrilla.'_'.$cont.'" value="'.$id.'" />
				<input type="hidden" id="idTercero'.$opcGrilla.'_'.$cont.'" value="'.$id_tercero.'" />
				<input type="hidden" id="idDocumentoCruce'.$opcGrilla.'_'.$cont.'" value="'.$id_documento_cruce.'" />

				<script>
					document.getElementById("documentoCruce'.$opcGrilla.'_'.$cont.'").value = "'.$tipo_documento_cruce.'";
					'.$script.'
					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrilla.'('.$debe.','.$haber.',"agregar");
				</script>';

		return $body;
	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrilla,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$tercero,$tipo_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce){

		$body ='<div class="campoNotaGeneral" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrilla.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrilla.'_'.$cont.'" value="'.$cuenta.'" readonly/>
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="descripcion'.$opcGrilla.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" readonly/></div>
				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="tercero'.$opcGrilla.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>

				<div class="campoNotaGeneral opcionalCruce"><input type="text" id="tipodocumentoCruce'.$opcGrilla.'_'.$cont.'" style="text-align:left;" value="'.$tipo_documento_cruce.'" readonly/></div>

				<div class="campoNotaGeneral opcionalCruce">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrilla.'_'.$cont.'" class="inputPrefijoNotaContableCruce" value="'.$prefijo_documento_cruce.'" readonly/>
					-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrilla.'_'.$cont.'" class="inputNumeroNotaContableCruce" value="'.$numero_documento_cruce.'" readonly/>
				</div>

				<div class="campoNotaGeneral"><input type="text" id="debito'.$opcGrilla.'_'.$cont.'" value="'.$debe.'" readonly/></div>
				<div class="campoNotaGeneral"><input type="text" id="credito'.$opcGrilla.'_'.$cont.'" value="'.$haber.'" readonly/></div>

				<input type="hidden" id="idCuenta'.$opcGrilla.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrilla.'_'.$cont.'" value="'.$id.'" />

				<script>
					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrilla.'('.$debe.','.$haber.',"agregar");
				</script>';

		return $body;
	}


?>