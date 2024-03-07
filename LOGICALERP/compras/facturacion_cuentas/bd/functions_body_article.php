<?php

	//==================================// FUNCIONES DE LA INTERFAZ CON EL TERCERO //==================================//
	//*****************************************************************************************************************//

	function cargaArticulosSaveConTercero($id,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$cont                    = 0;						//VARIABLE GUARDA DATOS DE REMISION CUANDO SE FACTURA
		$eventoObservacionGrilla = '';

		//VARIABLES CONFIRM SALDO CANTIDAD EN FACTURA O REMISION
		$whereSqlSaldoRemision   = '';
		$idRemision              = '';

		$sql   = "SELECT id,id_puc,cuenta_puc,descripcion_puc,debe,haber,id_tercero,nit_tercero,tercero,tipo_documento_cruce,id_documento_cruce,prefijo_documento_cruce,numero_documento_cruce,id_tabla_referencia FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' ORDER BY cuenta_puc ASC";
		$query = mysql_query($sql,$link);

		$body = '<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="labelNotaGeneral" style="width:40px !important;"></div>
							<div class="labelNotaGeneral" style="width:95px;">Cuenta</div>
							<div class="labelNotaGeneral campoDescripcion">Descripcion</div>
							<div class="labelNotaGeneral campoDescripcion">Nit</div>
							<div class="labelNotaGeneral campoDescripcion">Tercero</div>
							<div class="labelNotaGeneral">Doc. Cruce</div>
							<div class="labelNotaGeneral">N.Doc.Cruce</div>
							<div class="labelNotaGeneral">Debito</div>
							<div class="labelNotaGeneral">Credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

			// SI LA COTIZACION NO HA SIDO CEERRADA POR UNA FACTURA CARGA INPUTS READONLY
			if ($estado < 1) {
				while($row = mysql_fetch_array($query)){
					$row['debe']  = $row['debe'] * 1;
					$row['haber'] = $row['haber'] * 1;

					$cont++;
					$row['numero_documento_cruce'] = ($row['numero_documento_cruce'] > 0)? $row['numero_documento_cruce']: '';
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable, $row['id'], $row['id_puc'], $row['cuenta_puc'], $row['descripcion_puc'], $row['debe'], $row['haber'],$row['id_tercero'],$row['nit_tercero'],$row['tercero'], $row['tipo_documento_cruce'],$row['id_documento_cruce'], $row['prefijo_documento_cruce'], $row['numero_documento_cruce'],$row['id_tabla_referencia'],$link).'
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
					$row['debe']  = $row['debe'] * 1;
					$row['haber'] = $row['haber'] * 1;

					$cont++;
					$body .='<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								'.cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrillaContable, $row['id'], $row['id_puc'], $row['cuenta_puc'], $row['descripcion_puc'], $row['debe'], $row['haber'],$row['id_tercero'],$row['nit_tercero'],$row['tercero'],$row['tipo_documento_cruce'], $row['prefijo_documento_cruce'], $row['numero_documento_cruce']).'
							</div>';
				}
				$mostarBoton='disable()';
				$deshabilita='readonly';
			}

			$opciones=($estado==1)? 'readonly' : 'onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"' ;

		$body .='</div>
				</div>
				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacionFacturaCuentas"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  '.$opciones.'></textarea>
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
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL CUENTA POR PAGAR</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>
				<script>
					resizeHeadMyGrilla(document.getElementById("DivArticulos'.$opcGrillaContable.'"),\'head'.$opcGrillaContable.'\');
					contArticulos'.$opcGrillaContable.'= '.$cont.';

					document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";
					// Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").enable();
     //    			Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").'.$mostarBoton.';
				</script>';

		return $body;

	}

	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsUnidadesSaveConTercero($cont,$opcGrillaContable,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$nit,$tercero,$tipo_documento_cruce,$id_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$id_tabla_referencia,$link){
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

		// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
		$id_empresa=$_SESSION['EMPRESA'];
		$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_puc; ";
		$query=mysql_query($sql,$link);
		$centro_costo = mysql_result($query,0,'centro_costo');

		$sql = "SELECT id_centro_costos FROM compras_facturas_cuentas WHERE activo=1 AND id=$id;";
		$query = mysql_query($sql,$link);
		$id_centro_costos = mysql_result($query,0,'id_centro_costos');

		$contenido = ( $centro_costo=='Si' && ($id_centro_costos=='' || $id_centro_costos==0) )? '<div style="float:right;">'.$cont.'</div><div style="float:left;"><img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'></div>' : $cont ;


		$body ='<div class="campoNotaGeneral" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;" id="label_cont_'.$opcGrillaContable.'_'.$cont.'">'.$contenido.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);"  value="'.$cuenta.'" />
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" /></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoNotaGeneral" ><input type="text" id="nit'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$nit.'" onkeyup="buscarTerceroCuenta'.$opcGrillaContable.'(event,this);" /></div>
				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>
				<div class="iconBuscarArticulo">
					<img src="img/'.$imagenBoton.'.png" id="imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" onclick="'.$eventoBoton.''.$opcGrillaContable.'('.$cont.')" title="Buscar Tercero" />
				</div>

				<div class="campoNotaGeneral ">
				 	<!--<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly style="text-align:left;" >-->
				 	<select id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" style="width:calc(100% - 20px);border:none;" onChange="cambiaDocumentoCruce(this.value,'.$cont.',\'\')">
				 		<option value="" title="">Seleccione</option>
				 		<option value="CE" title="Comprobante de Egreso">CE</option>
				 		<option value="FC" title="Factura de Compra">FC</option>
				 	</select>
				 </div>
				 <div class="iconBuscarArticulo " id="iconBuscarArticulo_'.$cont.'">
					<img onclick="'.$eventoBotonDocumentoCruce.$opcGrillaContable.'('.$cont.');" id="imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" title="Buscar Documento Cruce" src="img/'.$imagenBotonDocumentoCruce.'.png" />
				</div>

				<div class="campoNotaGeneral ">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputPrefijoNotaContableCruce" onKeyup="this.value=this.value.toUpperCase()"  value="'.$prefijo_documento_cruce.'"/>-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');" value="'.$numero_documento_cruce.'" />
				</div>

				<div class="campoNotaGeneral"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');" value="'.$debe.'" /></div>
				<div class="campoNotaGeneral"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="'.$haber.'" /></div>

				<div style="float:left; min-width:80px;margin-left: 7px;">
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
					cambiaDocumentoCruce("'.$tipo_documento_cruce.'",'.$cont.',"false");
					document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value = "'.$tipo_documento_cruce.'";

					'.$script.'
					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");
				</script>';

		return $body;

	}

	//==================================== CARGAR ARTICULOS EN FACTURA TERMINADA 'BLOQUEADA' ====================================================//
	function cargaDivsUnidadesBloqueadasConTercero($cont,$opcGrillaContable,$id = 0,$id_puc = 0,$cuenta='',$descripcion='',$debe=0,$haber=0,$id_tercero,$nit,$tercero,$tipo_documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce){

		$body ='<div class="campoNotaGeneral" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$cuenta.'" readonly/>
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" readonly/></div>
				<div class="campoNotaGeneral" ><input type="text" id="nit'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$nit.'" readonly /></div>
				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>

				<div class="campoNotaGeneral "><input type="text" id="tipodocumentoCruce'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tipo_documento_cruce.'" readonly/></div>

				<div class="campoNotaGeneral ">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputPrefijoNotaContableCruce" value="'.$prefijo_documento_cruce.'" readonly/>-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroNotaContableCruce" value="'.$numero_documento_cruce.'" readonly/>
				</div>

				<div class="campoNotaGeneral"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" value="'.$debe.'" readonly/></div>
				<div class="campoNotaGeneral"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" value="'.$haber.'" readonly/></div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id_puc.'" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$id.'" />

				<script>
					//llamamos la funcion para generar los calculos de la factura
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");
				</script>';

		return $body;
	}


?>