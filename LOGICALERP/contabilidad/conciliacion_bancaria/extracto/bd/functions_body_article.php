<?php
	//================================== FUNCIONES DE LA INTERFAZ CON EL TERCERO ==================================//
	function cargaArticulosSave($saldo_extracto,$tablaPrincipal,$id_documento,$observacion,$estado,$opcGrillaContable,$idTablaPrincipal,$id_empresa,$link){
		$cont = 0;
		$sql = "SELECT
							id,
	         		tipo,
							numero_documento,
							fecha,
							valor
            FROM
							extractos_detalle
            WHERE
							id_extracto = '$id_documento'";
	  $query = mysql_query($sql,$link);

		//CABECERA DEL DOCUMENTO
    $body =  '<div class="contenedorGrilla">
								<div class="contenedorHeadArticulos">
									<div class="headArticulos" id="head'.$opcGrillaContable.'">
										<div class="label" style="width:40px !important;"></div>
										<div class="label" style="width:130px;">Tipo Documento</div>
										<div class="label" style="width:130px;">Numero Documento</div>
										<div class="label" style="width:130px;">Fecha</div>
										<div class="label" style="width:130px;">Valor</div>
										<div style="float:right; min-width:130px;"></div>
									</div>
								</div>
								<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">';

		//ESTADO DEL DOCUMENTO EN BORRADOR
		if($estado == 0){
			while($row = mysql_fetch_array($query)){
				$cont++;
				$body .= '<div class="bodyDivArticulos" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
										'.cargaDivsInsertDetalles($deshabilita,'return',$cont,$row['id'],$row['tipo'],$row['numero_documento'],$row['fecha'],$row['valor'],$opcGrillaContable).'
									</div>';
			}
			$cont++;
			$body .= '<div class="bodyDivArticulos" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
									'.cargaDivsInsertDetalles($deshabilita,'return',$cont,$row['id'],$row['tipo'],$row['numero_documento'],$row['fecha'],$row['valor'],$opcGrillaContable).'
								</div>';
		}
		else{
			$total_detalles = 0;
			while($row = mysql_fetch_array($query)){
				$cont++;
				$total_detalles += $row['valor'];
				$body .= '<div class="bodyDivArticulos" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
										'.cargaDivsInsertDetalles_lock($deshabilita,'return',$cont,$row['id'],$row['tipo'],$row['numero_documento'],$row['fecha'],$row['valor'],$opcGrillaContable).'
									</div>';
			}
		}

		//PIE DE PAGINA DEL DOCUMENTO
		$body .= '</div>
							</div>
							<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'" >
								<div class="contenedorObservacionGeneral">
									<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
									<textarea id="observacion'.$opcGrillaContable.'" '.$deshabilita.' onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
								</div>
								<div class="contenedorDetalleTotales">
									<div class="renglon">
										<div class="label" style="width:170px !important; padding-left:5px;">Total Extracto</div>
										<div class="labelSimbolo">$</div>
										<div class="labelTotal" id="subtotal'.$opcGrillaContable.'">0</div>
									</div>
									<div class="renglon">
										<div class="label" style="width:170px !important; padding-left:5px;">Total Detalle</div>
										<div class="labelSimbolo">$</div>
										<div class="labelTotal" id="subtotalDetalle'.$opcGrillaContable.'">0</div>
									</div>
									<div class="renglon renglonTotal" >
										<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">DIFERENCIA EXTRACTO</div>
										<div class="labelSimbolo">$</div>
										<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
									</div>
								</div>
							</div>';
		return $body;
	}
	//==================================== CARGAR ARTICULOS EN FACTURA ABIERTA ==================================================================//
	function cargaDivsInsertDetalles($deshabilita,$formaConsulta,$cont,$idDetalle,$tipo,$numero_documento,$fecha,$valor,$opcGrillaContable){

		$valor = ($valor == '')? 0 : $valor ;

		$body =  '<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderDetalle'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" value="'.$tipo.'" id="tipo'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'tipo'.$opcGrillaContable.'_'.$cont.'\','.$cont.')"/>
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" value="'.$numero_documento.'"  id="numeroDocumento'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'fecha'.$opcGrillaContable.'_'.$cont.'\','.$cont.')" />
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" value="'.$fecha.'"  id="fecha'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'valor'.$opcGrillaContable.'_'.$cont.'\','.$cont.')" />
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" value="'.$valor.'"  placeholder="ingrese valor" id="valor'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'guardar\','.$cont.')" />
							</div>
							<div style="float:right; min-width:130px;">
								<div onclick="deleteDetalle'.$opcGrillaContable.'('.$cont.')" id="deleteDetalle'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/delete.png"/></div>
								<div onclick="retrocederRegistro'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="guardarNewRegistro'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Registro" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:inline;"><img src="img/save_true.png" id="imgSaveDetalle'.$opcGrillaContable.'_'.$cont.'"/></div>
							</div>
							<input type="hidden" id="idDetalle'.$opcGrillaContable.'_'.$cont.'" value="'.$idDetalle.'">';
		if($valor == 0){
			$body .= '<input type="hidden" id="idRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />
								<input type="hidden" id="idInsertRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />';
		} else{
			$body .= '<input type="hidden" id="idRegistro'.$opcGrillaContable.'_'.$cont.'" value="'.$cont.'" />
								<input type="hidden" id="idInsertRegistro'.$opcGrillaContable.'_'.$cont.'" value="'.$cont.'" />
								<script>
									document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style = "width:20px; float:left; margin-top:3px;cursor:pointer;display:none;";
									document.getElementById("deleteDetalle'.$opcGrillaContable.'_'.$cont.'").style = "width:20px; float:left; margin-top:3px;cursor:pointer;display:inline;";
								</script>';
		}
			$body .= '<script>
									new Ext.form.ComboBox({
										typeAhead     : true,
										triggerAction : "all",
										lazyRender    : true,
										mode          : "local",
										applyTo       : "tipo'.$opcGrillaContable.'_'.$cont.'",
										width         : 130,
										store         : new Ext.data.ArrayStore({
											id     				: 0,
											fields 				:
												[
													"myId",
													"displayText"
												],
											data   				:
												[
													[1, "Cheque"],
													[2, "Consignacion"],
													[3, "Nota Debito"],
													[4, "Nota Credito"]
												]
									  }),
										valueField   : "myId",
										displayField : "displayText",
										listeners  : {
											select: function(){
												changeInput(event,this,\'tipo'.$opcGrillaContable.'_'.$cont.'\','.$cont.')
											}
										}
									});

									new Ext.form.DateField({
										emptyText  : "A\u00f1o-Mes-Dia",
	                  format     : "Y-m-d",
	                  width      : 130,
	                  allowBlank : false,
	                  showToday  : false,
	                  applyTo    : "fecha'.$opcGrillaContable.'_'.$cont.'",
	                  editable   : true,
										listeners  : {
											select: function(){
												changeInput(event,this,\'fecha'.$opcGrillaContable.'_'.$cont.'\','.$cont.')
											}
										}
					        });
									calcTotalExtrac("sumar",'.$valor.',0);
								</script>';
		if($formaConsulta == 'return'){
			return $body;
		}
		else{
			echo $body;
		}
	}
	//==================================== CARGAR ARTICULOS EN FACTURA CERRADA ==================================================================//
	function cargaDivsInsertDetalles_lock($deshabilita,$formaConsulta,$cont,$id,$tipo,$numero_documento,$fecha,$valor,$opcGrillaContable){
		$body =  '<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderDetalle'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>
							<div class="campo" style="width:130px;">
								<input '.$deshabilita.' type="text" value="'.$tipo.'" id="tipo'.$opcGrillaContable.'_'.$cont.'"/>
							</div>
							<div class="campo" style="width:130px;">
								<input '.$deshabilita.' type="text" value="'.$numero_documento.'"  id="numeroDocumento'.$opcGrillaContable.'_'.$cont.'"  />
							</div>
							<div class="campo" style="width:130px;">
								<input '.$deshabilita.' type="text" value="'.$fecha.'"  id="fecha'.$opcGrillaContable.'_'.$cont.'"  />
							</div>
							<div class="campo" style="width:130px;">
								<input '.$deshabilita.' type="text" value="'.$valor.'"  placeholder="ingrese valor" id="valor'.$opcGrillaContable.'_'.$cont.'" />
							</div>
							<div style="float:right; min-width:80px;">
						  </div>
							<script>
								calcTotalExtrac("sumar",'.$valor.',0);
							</script>';

		if($formaConsulta == 'return'){
			return $body;
		}
		else{
			echo $body;
		}
	}
?>
