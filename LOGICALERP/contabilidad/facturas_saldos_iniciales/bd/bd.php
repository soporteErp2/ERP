<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_empresa  = $_SESSION['EMPRESA'];

	// if (isset($id) || isset($idDocumento)) {
	// 	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
	// 	if ($opc<>'actualizarFechaNota') {
	// 		verificaCierre($id,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link);
	// 	}
	// }

	switch ($opc) {
		case 'guardarDocumento':
			verificaCierre($id_saldo_inicial,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link,$cont);
			guardarDocumento($opcGrillaContable,$consecutivo,$cont,$idInsertDoc,$prefijoFactura,$numeroFactura,$detalle,$fechaFactura,$valorFactura,$id_saldo_inicial,$id_tercero,$idCuentaPago,$tipo_factura,$filtro_sucursal,$id_empresa,$link);
			break;

		case 'actualizaDocumento':
			verificaCierre($id_saldo_inicial,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link,$cont);
			actualizaDocumento($opcGrillaContable,$consecutivo,$cont,$idInsertDoc,$prefijoFactura,$numeroFactura,$detalle,$fechaFactura,$valorFactura,$id_saldo_inicial,$id_tercero,$tipo_factura,$filtro_sucursal,$id_empresa,$link);
			break;

		case 'eliminaDocumento':
			verificaCierre($id_saldo_inicial,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link,$cont);
			eliminaDocumento($opcGrillaContable,$idDocumento,$tipo_factura,$cont,$id_saldo_inicial,$id_sucursal,$id_empresa,$link);
			break;

		case 'retrocederDocumento':
			verificaCierre($id_saldo_inicial,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link,$cont);
			retrocederDocumento($cont,$opcGrillaContable,$idDocumento,$id_saldo_inicial,$tipo_factura,$id_sucursal,$id_empresa,$link);
			break;

		case 'reloadBody':
			reloadBody($id_empresa, $id_sucursal, $id_saldo_inicial, $tipo_factura, $opcGrillaContable, $link);
			break;

		// case 'verificaCabecera':
		// 	verificaCabecera($id,$tipo_factura,$id_sucursal,$id_empresa,$link);
		// 	break;

		case 'ventanaEditarEncabezado':
			ventanaEditarEncabezado($id,$estado,$tipo_factura,$filtro_sucursal,$id_empresa,$link);
			break;

		case 'ventanaAgregarEncabezado':
			ventanaAgregarEncabezado($id_sucursal,$id_empresa,$link);
			break;

		case 'generar_saldo_inicial':
			generar_saldo_inicial($id_empresa, $idSaldoInicial, $opcGrillaContable, $link);
			break;

		case 'editar_saldo_inicial':
			editar_saldo_inicial($id_empresa, $idSaldoInicial, $opcGrillaContable, $link);
			break;

		case 'guardarEncabezado':
			verificaCierre(0,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link,$cont,$fecha_factura);
			guardarEncabezado($contrapartida_cuenta_pago_colgaap,$contrapartida_cuenta_pago_niif,$tipo_factura,$fecha_factura,$id_cuenta_pago,$filtro_sucursal,$id_empresa,$link);
			break;

		case 'actualizaEncabezado':
			verificaCierre($id,'fecha_factura','facturas_saldos_iniciales',$id_empresa,$link,$cont);
			actualizaEncabezado($contrapartida_cuenta_pago_colgaap,$contrapartida_cuenta_pago_niif,$id,$tipo_factura,$fecha_factura,$id_cuenta_pago,$filtro_sucursal,$id_empresa,$link);
			break;

		case 'eliminaEncabezado':
			eliminaEncabezado($tipo_factura,$id,$id_sucursal,$id_empresa,$link);
			break;

		case 'sincronizarCuentaNiif':
			sincronizarCuentaNiif($cuenta,$id_empresa,$link);
			break;

	}

	//FUNCION PARA GUARDAR LOS DOCUMENTOS
	function guardarDocumento($opcGrillaContable,$consecutivo,$cont,$idInsertDoc,$prefijoFactura,$numeroFactura,$detalle,$fechaFactura,$valorFactura,$id_saldo_inicial,$id_tercero,$idCuentaPago,$tipo_factura,$id_sucursal,$id_empresa,$link){
		//SI ES UNA FACTURA DE COMPRA
		if ($tipo_factura=='FC') {
			$sql   = "INSERT INTO compras_facturas (prefijo_factura,numero_factura,observacion,fecha_final,total_factura,total_factura_sin_abono,id_saldo_inicial,id_proveedor,id_configuracion_cuenta_pago,id_sucursal,id_empresa)
						VALUES ('$prefijoFactura','$numeroFactura','$detalle','$fechaFactura','$valorFactura','$valorFactura','$id_saldo_inicial','$id_tercero','$idCuentaPago','$id_sucursal','$id_empresa')";
			$query = mysql_query($sql,$link);

			$sqlLastId = "SELECT LAST_INSERT_ID()";
			$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

			if ($lastId > 0) {
				echo '<div class="bodyDivArticulosNotaGeneral" id="bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'">
						<div class="campo" style="width:40px !important; overflow:hidden;">
							<div style="float:left; margin:3px 0 0 2px;">'.$consecutivo.'</div>
							<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$consecutivo.'"></div>
						</div>

						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="nit_tercero_'.$opcGrillaContable.'_'.$consecutivo.'" readonly />
						</div>

						<div class="campoNotaGeneral" style="width:150px;">
							<input type="text" style="padding-right: 25px;" id="tercero_'.$opcGrillaContable.'_'.$consecutivo.'" readonly />
						</div>
						<div class="iconBuscarProveedor" onclick="ventanaBusquedaTercero('.$consecutivo.')" id="imgBuscarProveedor" title="Buscar Proveedor">
	                       <img src="img/buscar20.png"/>
	                    </div>
						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="prefijoFactura'.$opcGrillaContable.'_'.$consecutivo.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$consecutivo.')" />
						</div>

						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="numeroFactura'.$opcGrillaContable.'_'.$consecutivo.'"  onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$consecutivo.')" />
						</div>

						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="detalle'.$opcGrillaContable.'_'.$consecutivo.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$consecutivo.')" />
						</div>

						<div class="campoNotaGeneral" style="width:90px;">
							<input type="text" id="fechaFactura'.$opcGrillaContable.'_'.$consecutivo.'"  />
						</div>

						<div class="campoNotaGeneral" style="width:85px;">
							<input type="text" id="valorFactura'.$opcGrillaContable.'_'.$consecutivo.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$consecutivo.');"  />
						</div>

						<div style="float:left; min-width:80px;padding-left:5px;">
							<div onclick="guardarNewFactura'.$opcGrillaContable.'('.$consecutivo.')" id="divImageSave'.$opcGrillaContable.'_'.$consecutivo.'" title="Guardar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$consecutivo.'"/></div>
							<div onclick="retrocederDocumento'.$opcGrillaContable.'('.$consecutivo.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$consecutivo.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$consecutivo.'"></div>
							<div onclick="deleteFactura'.$opcGrillaContable.'('.$consecutivo.')" id="deleteFactura'.$opcGrillaContable.'_'.$consecutivo.'" title="Eliminar Documento" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
						</div>

						<input type="hidden" id="idTecero'.$opcGrillaContable.'_'.$consecutivo.'" " />
						<input type="hidden" id="idInsertDoc'.$opcGrillaContable.'_'.$consecutivo.'" value="0" />
					</div>
					<script>
						new Ext.form.DateField({
						    format     : "Y-m-d",
						    width      : 90,
						    allowBlank : false,
						    showToday  : false,
						    applyTo    : "fechaFactura'.$opcGrillaContable.'_'.$consecutivo.'",
						    editable   : false,
						    listeners  : { select: function() { validarNumberDocumentoFecha'.$opcGrillaContable.'(document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$consecutivo.'"),'.$consecutivo.');  } }
						});

						document.getElementById("idInsertDoc'.$opcGrillaContable.'_'.$cont.'").value = '.$lastId.'

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Documento");
						document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
						document.getElementById("deleteFactura'.$opcGrillaContable.'_'.$cont.'").style.display    = "block";

					</script>';

			}
			else{
				echo '<script>
						alert("Error\nNo se ha guardo la factura de compra, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
						var elemento=document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'");
						elemento.parentNode.removeChild(elemento);
				  	</script>  ';
			}
		}
		//SI ES UNA FACTURA DE VENTA
		else{
			$numero_factura_completo=($prefijoFactura!="")? $prefijoFactura.' '.$numeroFactura : $numeroFactura;
			$sql   = "INSERT INTO ventas_facturas (prefijo,numero_factura,numero_factura_completo,observacion,fecha_inicio,fecha_vencimiento,total_factura,total_factura_sin_abono,id_saldo_inicial,id_cliente,id_configuracion_cuenta_pago,id_sucursal,id_empresa)
						VALUES ('$prefijoFactura','$numeroFactura','$numero_factura_completo','$detalle','$fechaFactura','$fechaFactura','$valorFactura','$valorFactura','$id_saldo_inicial','$id_tercero','$idCuentaPago','$id_sucursal','$id_empresa')";
			$query = mysql_query($sql,$link);

			$sqlLastId = "SELECT LAST_INSERT_ID()";
			$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

			if ($lastId > 0) {
				echo'<div class="bodyDivArticulosNotaGeneral" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
						<div class="campo" style="width:40px !important; overflow:hidden;">
							<div style="float:left; margin:3px 0 0 2px;">'.$consecutivo.'</div>
							<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$consecutivo.'"></div>
						</div>

						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="nit_tercero_'.$opcGrillaContable.'_'.$consecutivo.'" readonly />
						</div>

						<div class="campoNotaGeneral" style="width:150px;">
							<input type="text" style="padding-right: 25px;" id="tercero_'.$opcGrillaContable.'_'.$consecutivo.'" readonly />
						</div>
						<div class="iconBuscarProveedor" onclick="ventanaBusquedaTercero('.$consecutivo.')" id="imgBuscarProveedor" title="Buscar Proveedor">
	                       <img src="img/buscar20.png"/>
	                    </div>
						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="prefijoFactura'.$opcGrillaContable.'_'.$consecutivo.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$consecutivo.')" />
						</div>

						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="numeroFactura'.$opcGrillaContable.'_'.$consecutivo.'"  onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$consecutivo.')" />
						</div>

						<div class="campoNotaGeneral" style="width:95px;">
							<input type="text" id="detalle'.$opcGrillaContable.'_'.$consecutivo.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$consecutivo.')" />
						</div>

						<div class="campoNotaGeneral" style="width:90px;">
							<input type="text" id="fechaFactura'.$opcGrillaContable.'_'.$consecutivo.'"  />
						</div>

						<div class="campoNotaGeneral" style="width:85px;">
							<input type="text" id="valorFactura'.$opcGrillaContable.'_'.$consecutivo.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$consecutivo.');"  />
						</div>

						<div style="float:left; min-width:80px;padding-left:5px;">
							<div onclick="guardarNewFactura'.$opcGrillaContable.'('.$consecutivo.')" id="divImageSave'.$opcGrillaContable.'_'.$consecutivo.'" title="Guardar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$consecutivo.'"/></div>
							<div onclick="retrocederDocumento'.$opcGrillaContable.'('.$consecutivo.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$consecutivo.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$consecutivo.'"></div>
							<div onclick="deleteFactura'.$opcGrillaContable.'('.$consecutivo.')" id="deleteFactura'.$opcGrillaContable.'_'.$consecutivo.'" title="Eliminar Documento" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
						</div>

						<input type="hidden" id="idTecero'.$opcGrillaContable.'_'.$consecutivo.'" " />
						<input type="hidden" id="idInsertDoc'.$opcGrillaContable.'_'.$consecutivo.'" value="0" />
					</div>
					<script>
						new Ext.form.DateField({
						    format     : "Y-m-d",
						    width      : 90,
						    allowBlank : false,
						    showToday  : false,
						    applyTo    : "fechaFactura'.$opcGrillaContable.'_'.$consecutivo.'",
						    editable   : false,
						    listeners  : { select: function() { validarNumberDocumentoFecha'.$opcGrillaContable.'(document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$consecutivo.'"),'.$consecutivo.');  } }
						});

						document.getElementById("idInsertDoc'.$opcGrillaContable.'_'.$cont.'").value = '.$lastId.'

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Documento");
						document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
						document.getElementById("deleteFactura'.$opcGrillaContable.'_'.$cont.'").style.display    = "block";

					</script>';
			}
			else{
				echo'<script>
						alert("Error\nNo se ha guardo la factura de compra, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
						var elemento=document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'");
						elemento.parentNode.removeChild(elemento);
				  	</script>  ';
			}
		}
	}

	//FUNCION PARA ACTUALIZAR LOS DOCUMENTO
	function actualizaDocumento($opcGrillaContable,$consecutivo,$cont,$idInsertDoc,$prefijoFactura,$numeroFactura,$detalle,$fechaFactura,$valorFactura,$id_saldo_inicial,$id_tercero,$tipo_factura,$id_sucursal,$id_empresa,$link){
		//SI ES UNA FACTURA DE COMPRA
		if ($tipo_factura=='FC') {
			$sql   = "UPDATE compras_facturas SET
							prefijo_factura='$prefijoFactura',
							numero_factura='$numeroFactura',
							observacion='$detalle',
							fecha_final='$fechaFactura',
							total_factura='$valorFactura',
							total_factura_sin_abono='$valorFactura',
							id_saldo_inicial='$id_saldo_inicial',
							id_proveedor='$id_tercero',
							id_sucursal='$id_sucursal',
							id_empresa='$id_empresa'
						WHERE activo=1
							AND id_empresa=$id_empresa
							AND id_sucursal=$id_sucursal
							AND id=$idInsertDoc";
			$query = mysql_query($sql,$link);

			if ($query) {
				echo '<script>
						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					</script>';
			}
			else{
				echo '<script>
						alert("Error\nNo se ha actualizo la factura de compra, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
				  	</script>  ';
			}
		}
		//SI ES UNA FACTURA DE VENTA
		else{
			$numero_factura_completo=($prefijoFactura!="")? $prefijoFactura.' '.$numeroFactura : $numeroFactura ;
			$sql   = "UPDATE ventas_facturas SET
							prefijo='$prefijoFactura',
							numero_factura='$numeroFactura',
							numero_factura_completo='$numero_factura_completo',
							observacion='$detalle',
							fecha_vencimiento='$fechaFactura',
							total_factura='$valorFactura',
							total_factura_sin_abono='$valorFactura',
							id_saldo_inicial='$id_saldo_inicial',
							id_cliente='$id_tercero',
							id_sucursal='$id_sucursal',
							id_empresa='$id_empresa'
						WHERE activo=1
							AND id_empresa=$id_empresa
							AND id_sucursal=$id_sucursal
							AND id=$idInsertDoc";
			$query = mysql_query($sql,$link);

			if ($query) {
				echo '<script>
						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					</script>';
			}
			else{
				echo '<script>
						alert("Error\nNo se ha guardo la factura de venta, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
				  	</script>  ';
			}
		}
	}

	//FUNCION PARA ELIMINAR UN DOCUMENTO
	function eliminaDocumento($opcGrillaContable,$idDocumento,$tipo_factura,$cont,$id_saldo_inicial,$id_sucursal,$id_empresa,$link){
		if ($tipo_factura=='FC') {
			$sqlDelete   = "DELETE FROM compras_facturas WHERE id='$idDocumento' AND id_saldo_inicial='$id_saldo_inicial' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";
		}
		else{
			$sqlDelete   = "DELETE FROM ventas_facturas WHERE id='$idDocumento' AND id_saldo_inicial='$id_saldo_inicial' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";
		}

		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar el documento, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo'<script>
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
				</script>';
		}
	}

	function retrocederDocumento($cont,$opcGrillaContable,$idDocumento,$id_saldo_inicial,$tipo_factura,$id_sucursal,$id_empresa,$link){
		if ($tipo_factura=='FC') {
			$sql = "SELECT id,prefijo_factura,numero_factura,observacion,fecha_final AS fecha_vencimiento,total_factura FROM compras_facturas
					WHERE activo=1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND id_saldo_inicial=$id_saldo_inicial AND id=$idDocumento";
		}
		//SI ES UNA FACTURA DE VENTA
		else{
			$sql = "SELECT id,prefijo AS prefijo_factura,numero_factura,observacion,fecha_vencimiento,total_factura FROM ventas_facturas
					WHERE activo=1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND id_saldo_inicial=$id_saldo_inicial AND id=$idDocumento";
		}

		$query = mysql_query($sql,$link);

		$prefijo_factura   = mysql_result($query,0,'prefijo_factura');
		$numero_factura    = mysql_result($query,0,'numero_factura');
		$observacion       = mysql_result($query,0,'observacion');
		$fecha_vencimiento = mysql_result($query,0,'fecha_vencimiento');
		$total_factura     = mysql_result($query,0,'total_factura');

		echo'<script>
				document.getElementById("prefijoFactura'.$opcGrillaContable.'_'.$cont.'").value           = "'.$prefijo_factura.'";
				document.getElementById("numeroFactura'.$opcGrillaContable.'_'.$cont.'").value            = "'.$numero_factura.'";
				document.getElementById("detalle'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$observacion.'";
				document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$cont.'").value             = "'.$fecha_vencimiento.'";
				document.getElementById("valorFactura'.$opcGrillaContable.'_'.$cont.'").value             = "'.$total_factura.'";

				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
			</script>';
	}

	function reloadBody($id_empresa, $id_sucursal, $id_saldo_inicial, $tipo_factura, $opcGrillaContable, $link){

		if ($tipo_factura == 'FC') {
			$sql = "SELECT id,prefijo_factura,numero_factura,observacion,fecha_final AS fecha_vencimiento,total_factura,nit,id_proveedor AS id_tercero, proveedor AS tercero
					FROM compras_facturas
					WHERE activo=1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND id_saldo_inicial=$id_saldo_inicial";

		}
		//SI ES UNA FACTURA DE VENTA
		else{
			$sql = "SELECT id,prefijo AS prefijo_factura,numero_factura,observacion,fecha_vencimiento,total_factura,nit,id_cliente AS id_tercero, cliente AS tercero
					FROM ventas_facturas
					WHERE activo=1 AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa AND id_saldo_inicial=$id_saldo_inicial";
		}

		$cont  = 1;
		$query = mysql_query($sql,$link);

		while ($row=mysql_fetch_array($query)) {
			$bodyArticle .= '<div class="bodyDivArticulosNotaGeneral" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
								<div class="campo" style="width:40px !important; overflow:hidden;">
									<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
									<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								</div>

								<div class="campoNotaGeneral" style="width:95px;">
									<input type="text" id="nit_tercero_'.$opcGrillaContable.'_'.$cont.'" value="'.$row['nit'].'" readonly />
								</div>

								<div class="campoNotaGeneral" style="width:150px;">
									<input type="text" style="padding-right: 25px;" id="tercero_'.$opcGrillaContable.'_'.$cont.'" value="'.$row['tercero'].'" readonly />
								</div>
								<div class="iconBuscarProveedor" onclick="ventanaBusquedaTercero('.$cont.')" id="imgBuscarProveedor" title="Buscar Proveedor">
			                       <img src="img/buscar20.png"/>
			                    </div>
								<div class="campoNotaGeneral" style="width:95px;">
									<input type="text" id="prefijoFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['prefijo_factura'].'" '.$eventoInput.' />
								</div>

								<div class="campoNotaGeneral" style="width:95px;">
									<input type="text" id="numeroFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['numero_factura'].'" '.$eventoInput.' />
								</div>

								<div class="campoNotaGeneral" style="width:95px;">
									<input type="text" id="detalle'.$opcGrillaContable.'_'.$cont.'" value="'.$row['observacion'].'" '.$eventoInput.' />
								</div>

								<div class="campoNotaGeneral" style="width:90px;">
									<input type="text" id="fechaFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['fecha_vencimiento'].'" />
								</div>

								<div class="campoNotaGeneral" style="width:85px;">
									<input type="text" id="valorFactura'.$opcGrillaContable.'_'.$cont.'" value="'.$row['total_factura'].'" '.$eventSave.'  />
								</div>

								<div style="float:left; min-width:80px;padding-left:5px;">
									<div onclick="guardarNewFactura'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/reload.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
									<div onclick="retrocederDocumento'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
									<div onclick="deleteFactura'.$opcGrillaContable.'('.$cont.')" id="deleteFactura'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Documento" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png" /></div>
								</div>

								<input type="hidden" id="idTecero'.$opcGrillaContable.'_'.$cont.'" value="'.$row['id_tercero'].'" />
								<input type="hidden" id="idInsertDoc'.$opcGrillaContable.'_'.$cont.'" value="'.$row['id'].'" />
							</div>
							<script>
								new Ext.form.DateField({
								    format     : "Y-m-d",
								    width      : 90,
								    allowBlank : false,
								    showToday  : false,
								    applyTo    : "fechaFactura'.$opcGrillaContable.'_'.$cont.'",
								    editable   : false,
								    listeners  : { select: function() { validarNumberDocumentoFecha'.$opcGrillaContable.'(document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$cont.'"),'.$cont.');  } }
								});
							</script>';
			$cont++;
		}

		$bodyArticle .= '<div class="bodyDivArticulosNotaGeneral" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="nit_tercero_'.$opcGrillaContable.'_'.$cont.'" readonly />
							</div>

							<div class="campoNotaGeneral" style="width:150px;">
								<input type="text" style="padding-right: 25px;" id="tercero_'.$opcGrillaContable.'_'.$cont.'" readonly />
							</div>
							<div class="iconBuscarProveedor" onclick="ventanaBusquedaTercero('.$cont.')" id="imgBuscarProveedor" title="Buscar Proveedor">
		                       <img src="img/buscar20.png"/>
		                    </div>
							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="prefijoFactura'.$opcGrillaContable.'_'.$cont.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')" />
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="numeroFactura'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')" />
							</div>

							<div class="campoNotaGeneral" style="width:95px;">
								<input type="text" id="detalle'.$opcGrillaContable.'_'.$cont.'" onKeyup="validarNumberDocumento'.$opcGrillaContable.'(event,this,'.$cont.')" />
							</div>

							<div class="campoNotaGeneral" style="width:90px;">
								<input type="text" id="fechaFactura'.$opcGrillaContable.'_'.$cont.'"  />
							</div>

							<div class="campoNotaGeneral" style="width:85px;">
								<input type="text" id="valorFactura'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"  />
							</div>

							<div style="float:left; min-width:80px;padding-left:5px;">
								<div onclick="guardarNewFactura'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Documento" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
								<div onclick="retrocederDocumento'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="deleteFactura'.$opcGrillaContable.'('.$cont.')" id="deleteFactura'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Documento" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
							</div>

							<input type="hidden" id="idTecero'.$opcGrillaContable.'_'.$cont.'" " />
							<input type="hidden" id="idInsertDoc'.$opcGrillaContable.'_'.$cont.'" value="0" />
						</div>
						<script>
							new Ext.form.DateField({
							    format     : "Y-m-d",
							    width      : 90,
							    allowBlank : false,
							    showToday  : false,
							    applyTo    : "fechaFactura'.$opcGrillaContable.'_'.$cont.'",
							    editable   : false,
							    listeners  : { select: function() {  validarNumberDocumentoFecha'.$opcGrillaContable.'(document.getElementById("fechaFactura'.$opcGrillaContable.'_'.$cont.'"),'.$cont.'); } }
							});
							contArticulos'.$opcGrillaContable.' = '.$cont.';
						</script>';

		echo $bodyArticle;
	}

	//FUNCION PARA VERIFICAR SI SE PUEDE EDITAR LA CABECERA
	// function verificaCabecera($id,$tipo_factura,$id_sucursal,$id_empresa,$link){
	// 	$tabla = ($tipo_factura=='FV')? 'ventas_facturas' : 'compras_facturas';

	// 	$sql   = "SELECT COUNT(id) AS cont FROM $tabla WHERE id_saldo_inicial=$id ";
	// 	$query = mysql_query($sql,$link);
	// 	$cont  = mysql_result($query,0,'cont');

	// 	if ($cont>0) { echo 'false'; }
	// 	else{ echo "true"; }
	// }

	//VENTANA EDITAR REGISTRO DE CABECERA
	function ventanaEditarEncabezado($id,$estado,$tipo_factura,$id_sucursal,$id_empresa,$link){

		$tabla = ($tipo_factura=='FV')? 'ventas_facturas' : 'compras_facturas';

		$sql   = "SELECT COUNT(id) AS cont FROM $tabla WHERE id_saldo_inicial=$id";
		$query = mysql_query($sql,$link);
		$cont  = mysql_result($query,0,'cont');

		// $visible  = ($cont > 0)? 'none': 'block';
		$estado1 = ($estado == 'bloqueado')? 'none': 'block';

		$sql   = "SELECT * FROM facturas_saldos_iniciales WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";
		$query = mysql_query($sql,$link);

		$tipo_factura   = mysql_result($query,0,'tipo_factura');
		$fecha_factura  = mysql_result($query,0,'fecha_factura');
		$id_cuenta_pago = mysql_result($query,0,'id_cuenta_pago');
		$cuenta_pago    = mysql_result($query,0,'cuenta_pago');
		$cuenta_colgaap = mysql_result($query,0,'cuenta_colgaap');
		$cuenta_niif    = mysql_result($query,0,'cuenta_niif');
		$cuenta_contrapartida_colgaap = mysql_result($query,0,'contrapartida_colgaap');
		$cuenta_contrapartida_niif    = mysql_result($query,0,'contrapartida_niif');

		$tipo_factura=($tipo_factura=='FV')? 'document.getElementById("tipo_factura").value="FV";' : 'document.getElementById("tipo_factura").value="FC";' ;

		$MSucursales = user_permisos(1);

		if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
		if($MSucursales == 'true'){ $filtroS = ""; }

		$SQL     = "SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = '$id_empresa' $filtroS";
		$consulS = mysql_query($SQL,$link);
		$selectSucursal='';

		while($rowS=mysql_fetch_array($consulS)){
			$selected = $rowS['id'] == $id_sucursal? 'selected': '';
		 	$selectSucursal.= '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
		}

		//-------------------------//
		// VALIDAR EL CIERRE ANUAL //
		//-------------------------//

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar = date("Y", strtotime($fecha_factura)).'-01-01';
		$fecha_fin_buscar    = date("Y", strtotime($fecha_factura)).'-12-31';

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND fecha_nota>='$fecha_inicio_buscar' AND fecha_nota<='$fecha_fin_buscar' ";
		$query=mysql_query($sql,$link);
		$cont = mysql_result($query,0,'cont');

		// SI EXISTEN NOTAS DE CIERRE DE ESE PERIODO
		if ($cont>0) {
			$script='Ext.getCmp("bnt_actualizar").disable();Ext.getCmp("bnt_eiminar").disable();';
			$divInfo='<div class="avisoBloqueo">DOCUMENTO BLOQUEADO POR CIERRE ANUAL</div>';
		}

		echo'<style>
				.EmpSeparador{
					float                 : left;
					width                 : 90%;
					color                 : #333;
					padding               : 2px 0 3px 5px;
					margin                : 10px 0 8px 10px;
					font-weight           : bold;
					-moz-border-radius    : 3px;
					-webkit-border-radius : 3px;
					-webkit-box-shadow    : 1px 1px 3px #666;
					-moz-box-shadow       : 1px 1px 2px #666;
					background            : -webkit-linear-gradient(#DFE8F6, #CDDBF0);
					background            : -moz-linear-gradient(#DFE8F6, #CDDBF0);
					background            : -o-linear-gradient(#DFE8F6, #CDDBF0);
					background            : linear-gradient(#DFE8F6, #CDDBF0);
				}

				.avisoBloqueo{
					text-align  :center;
					float       :left;
					color       : rgba(255, 6, 6, 0.64);
					width       : 100%;
					font-weight : bold;
					margin      : 10px 0px 0px 0px;

				}

			</style>
			<div style="width:100%;height:100%;">
				<div id="divLoad" style="position: absolute;width: auto;height: 20px;"></div>

				'.$divInfo.'

				<div class="EmpSeparador" style="display:'.$visible.';">GENERAL</div>
				<div style="margin-top:9px; width:300px;margin-left: 50px;float:left; display:'.$visible.';">
					<div style="width: 110px;float: left;font-weight:bold;">Sucursal:</div>
					<div style="width: calc(100% - 110px);font-weight:bold;float:left;">
						<select id="filtro_sucursal" >
							'.$selectSucursal.'
						</select>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left; display:'.$visible.';">
					<div style="width: 110px;float: left;font-weight:bold;">Fecha:</div>
					<div style="width: calc(100% - 110px);font-weight:bold;float:left;">
						<input type="text" class="myfield" id="fecha_factura" style="width:100%;float:left;" value="'.$fecha_factura.'" readonly>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left; display:'.$visible.';">
					<div style="width: 110px;;float: left;font-weight:bold;">Tipo Factura:</div>
					<div style="width: calc(100% - 110px);font-weight:bold;float:left;">
						<select id="tipo_factura" onchange="changeTipoFactura()">
							<option value="FV">Venta</option>
							<option value="FC">Compra</option>
						</select>
					</div>
				</div>

				<div class="EmpSeparador">CONFIGURACION CONTABLE</div>
				<div style="margin-top:9px; width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;;float: left;font-weight:bold;">Cuenta Pago:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="hidden" id="id_cuenta_pago" value="'.$id_cuenta_pago.'">
						<input type="text" class="myfield" id="cuenta_pago" style="width:calc(100% - 20px);float:left;" value="'.$cuenta_pago.'" readonly/>
						<div style="float:right;width:18px;height:18px;cursor:pointer; border:1px solid #d4d4d4; background-color: #F3F3F3; display:'.$estado1.';" onclick="ventanaBusquedaCuentaPago()">
							<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Cuenta Colgaap:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="cuenta_pago_colgaap" style="width:100%;float:left;" value="'.$cuenta_colgaap.'" readonly/>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Cuenta Niif:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="cuenta_pago_niif" style="width:100%;float:left;" value="'.$cuenta_niif.'" readonly/>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Contrapartida Cuenta Colgaap:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="contrapartida_cuenta_pago_colgaap" readonly style="width:calc(100% - 40px);float:left;" value="'.$cuenta_contrapartida_colgaap.'" />
						<div style="float:left;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3; display:'.$estado1.';" title="Sincronizar cuenta niif" onclick="sincronizarCuentaNiif()">
							<img src="img/refresh.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>

						<div style="float:left;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3; display:'.$estado1.';" title="Buscar" onclick="ventanaBuscarCuenta(\'puc\')">
							<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Contrapartida Cuenta Niif:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="contrapartida_cuenta_pago_niif" readonly style="width:calc(100% - 20px);float:left;" value="'.$cuenta_contrapartida_niif.'" />
						<div style="float:right;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3; display:'.$estado1.';" onclick="ventanaBuscarCuenta(\'niif\')">
							<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>
					</div>
				</div>

			</div>

			<script>
				new Ext.form.DateField({
				    format     : "Y-m-d",               //FORMATO
				    width      : 130,                   //ANCHO
				    allowBlank : false,
				    showToday  : false,
				    applyTo    : "fecha_factura",
				    editable   : false,                 //EDITABLE
				    value      : "'.$fecha_factura.'",             //VALOR POR DEFECTO
				    listeners  : { select: function() {   } }
				});
				'.$tipo_factura.'
				'.$script.'
			</script>';

	}

	//VENTANA AGREGAR ENCABEZADO
	function ventanaAgregarEncabezado($id_sucursal,$id_empresa,$link){

		$MSucursales = user_permisos(1);

		if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
		if($MSucursales == 'true'){ $filtroS = ""; }

		$SQL     = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$id_empresa' $filtroS";
		$consulS = mysql_query($SQL,$link);
		$selectSucursal='';

		while($rowS=mysql_fetch_array($consulS)){
			$selected = $rowS['id'] == $id_sucursal? 'selected': '';
		 	$selectSucursal.= '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
		}


		echo'<style>
				.EmpSeparador{
					float: left;
					width: 90%;
					color: #333;
					padding: 2px 0 3px 5px;
					margin: 10px 0 8px 10px;
					font-weight: bold;
					-moz-border-radius: 3px;
					-webkit-border-radius: 3px;
					-webkit-box-shadow: 1px 1px 3px #666;
					-moz-box-shadow: 1px 1px 2px #666;
					background: -webkit-linear-gradient(#DFE8F6, #CDDBF0);
					background: -moz-linear-gradient(#DFE8F6, #CDDBF0);
					background: -o-linear-gradient(#DFE8F6, #CDDBF0);
					background: linear-gradient(#DFE8F6, #CDDBF0);
				}
			</style>

			<div style="width:100%;height:100%;">
				<div id="divLoad" style="position: absolute;width: auto;height: 20px;"></div>
				<div class="EmpSeparador">GENERAL</div>
				<div style="margin-top: 10px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Sucursal:</div>
					<div style="width: calc(100% - 110px);font-weight:bold;float:left;">
						<select id="filtro_sucursal" >
							'.$selectSucursal.'
						</select>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Fecha:</div>
					<div style="width: calc(100% - 110px);font-weight:bold;float:left;">
						<input type="text" class="myfield" id="fecha_factura" style="width:100%;float:left;" readonly>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Tipo Factura:</div>
					<div style="width: calc(100% - 110px);font-weight:bold;float:left;">
						<select id="tipo_factura" onchange="changeTipoFactura()">
							<option value="FV">Venta</option>
							<option value="FC">Compra</option>
						</select>
					</div>
				</div>

				<div class="EmpSeparador">CONFIGURACION CONTABLE</div>
				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Cuenta Pago:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="hidden" id="id_cuenta_pago" value="0">
						<input type="text" class="myfield" id="cuenta_pago" readonly style="width:calc(100% - 20px);float:left;"  onclick="ventanaBusquedaCuentaPago()">
						<div style="float:right;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3;" onclick="ventanaBusquedaCuentaPago()">
							<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Cuenta Colgaap:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="cuenta_pago_colgaap" readonly style="width:100%;float:left;" >
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Cuenta Niif:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="cuenta_pago_niif" readonly style="width:100%;float:left;" >
					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Contrapartida Cuenta Colgaap:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="contrapartida_cuenta_pago_colgaap" readonly style="width:calc(100% - 40px);float:left;" onclick="ventanaBuscarCuenta(\'puc\')" >

						<div style="float:left;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3;" title="Sincronizar cuenta niif" onclick="sincronizarCuentaNiif()">
							<img src="img/refresh.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>

						<div style="float:left;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3;" title="Buscar" onclick="ventanaBuscarCuenta(\'puc\')">
							<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>

					</div>
				</div>

				<div style="margin-top: 9px;width: 300px;margin-left: 50px;float:left;">
					<div style="width: 110px;float: left;font-weight:bold;">Contrapartida Cuenta Niif:</div>
					<div style="width: calc(100% - 110px);float: left;">
						<input type="text" class="myfield" id="contrapartida_cuenta_pago_niif" readonly style="width:calc(100% - 20px);float:left;" onclick="ventanaBuscarCuenta(\'niif\')" >
						<div style="float:right;width:18px;height:18px;cursor:pointer;border: 1px solid #d4d4d4;background-color: #F3F3F3;" onclick="ventanaBuscarCuenta(\'niif\')">
							<img src="img/buscar20.png" style="width:16px;height:16px;padding-top: 1;padding-left: 1;">
						</div>
					</div>
				</div>

			</div>
			<script>
				new Ext.form.DateField({
				    format     : "Y-m-d",               //FORMATO
				    width      : 130,                   //ANCHO
				    allowBlank : false,
				    showToday  : false,
				    applyTo    : "fecha_factura",
				    editable   : false,                 //EDITABLE
				    value      : new Date(),             //VALOR POR DEFECTO
				    listeners  : { select: function() {   } }
				});
			</script>';
	}

	function generar_saldo_inicial($id_empresa, $idSaldoInicial, $opcGrillaContable, $link){
		$sqlEstado   = "SELECT COUNT(id) as cont,
							cuenta_colgaap,
							cuenta_niif,
							contrapartida_colgaap,
							contrapartida_niif,
							tipo_factura,
							id_sucursal,
							estado,
							fecha_factura
						FROM facturas_saldos_iniciales
						WHERE id='$idSaldoInicial'
							AND id_empresa='$id_empresa'
							AND activo=1
						LIMIT 0,1";
		$queryEstado = mysql_query($sqlEstado,$link);
		// echo $sqlEstado;
		$cont        = mysql_result($queryEstado, 0, 'cont');
		$estado      = mysql_result($queryEstado, 0, 'estado');
		$idSucursal  = mysql_result($queryEstado, 0, 'id_sucursal');
		$tipoFactura = mysql_result($queryEstado, 0, 'tipo_factura');
		$fecha       = mysql_result($queryEstado, 0, 'fecha_factura');

		$partidaColgaap       = mysql_result($queryEstado, 0, 'cuenta_colgaap');
		$partidaNiif          = mysql_result($queryEstado, 0, 'cuenta_niif');
		$contraPartidaColgaap = mysql_result($queryEstado, 0, 'contrapartida_colgaap');
		$contraPartidaNiif    = mysql_result($queryEstado, 0, 'contrapartida_niif');

		if(!$queryEstado){ echo '<script>alert("Aviso\nA ocurrido un problema con la conexion a la base de datos!")</script>'; exit; }
		else if($cont == 0 || is_nan($cont)){ echo '<script>alert("Aviso\nNo se encontro el documento de saldos iniciales!")</script>'; exit; }
		else if($estado == 1){ echo '<script>alert("Aviso\nEl presente documento ya ha sido generado!")</script>'; exit; }


		if($tipoFactura == 'FV'){
			$tablaBd       = "ventas_facturas";
			$tipoDocumento = 'Factura de Venta';
			$sqlFacturas   = "SELECT id, CONCAT(prefijo, ' ', numero_factura) AS numero_factura, total_factura,nit,id_cliente AS id_tercero,fecha_inicio
								FROM ventas_facturas
								WHERE id_saldo_inicial='$idSaldoInicial'
									AND activo=1";
		}
		else{
			$tablaBd       = "compras_facturas";
			$tipoDocumento = 'Factura de Compra';
			$sqlFacturas   = "SELECT id, CONCAT(prefijo_factura, ' ', numero_factura) AS numero_factura, total_factura,nit,id_proveedor AS id_tercero,fecha_inicio
								FROM compras_facturas
								WHERE id_saldo_inicial='$idSaldoInicial'
									AND activo=1";
		}

		$queryFacturas = mysql_query($sqlFacturas,$link);

		$insertColgaap = "";
		$insertNiif    = "";
		$deleteError   = "";
		while ($row = mysql_fetch_assoc($queryFacturas)) {
			if ($row['nit']=='') { echo '<script>alert("Error!\nAlgunas facturas no tienen el tercero, verifiquelas e intentelo de nuevo");</script>'; exit; }

			$debito  = ($tipoFactura == 'FV')? $row['total_factura']: 0;
			$credito = ($tipoFactura == 'FV')? 0: $row['total_factura'];

			$insertColgaap .= "('$row[id]',
								'$row[numero_factura]',
								'$tipoFactura',
								'$tipoDocumento',
								'$row[id]',
								'$tipoFactura',
								'$row[numero_factura]',
								'$fecha',
								$debito,
								$credito,
								$partidaColgaap,
								'$row[id_tercero]',
								$idSucursal,
								$id_empresa)
								,
								('$row[id]',
								'$row[numero_factura]',
								'$tipoFactura',
								'$tipoDocumento',
								'$row[id]',
								'$tipoFactura',
								'$row[numero_factura]',
								'$fecha',
								$credito,
								$debito,
								$contraPartidaColgaap,
								'$row[id_tercero]',
								$idSucursal,
								$id_empresa),";

			$insertNiif .= "('$row[id]',
								'$row[numero_factura]',
								'$tipoFactura',
								'$tipoDocumento',
								'$row[id]',
								'$tipoFactura',
								'$row[numero_factura]',
								'$fecha',
								$debito,
								$credito,
								$partidaNiif,
								'$row[id_tercero]',
								$idSucursal,
								$id_empresa)
								,
								('$row[id]',
								'$row[numero_factura]',
								'$tipoFactura',
								'$tipoDocumento',
								'$row[id]',
								'$tipoFactura',
								'$row[numero_factura]',
								'$fecha',
								$credito,
								$debito,
								$contraPartidaNiif,
								'$row[id_tercero]',
								$idSucursal,
								$id_empresa),";

			$deleteError .= "(id_documento='$row[id]' AND tipo_documento='$tipoFactura' AND id_empresa='$id_empresa') OR ";
		}

		//==============================================// INSERT CONTABILIDAD //==============================================//
		/***********************************************************************************************************************/
		$arrayError['id']       = $idSaldoInicial;
		$arrayError['tablaDoc'] = $tablaBd;
		$arrayError['asientos'] = substr($deleteError, 0, -4);

		$insertColgaap = substr($insertColgaap, 0, -1);
		$insertNiif    = substr($insertNiif, 0, -1);

		$sqlColgaap = "INSERT INTO asientos_colgaap (
							id_documento,
							consecutivo_documento,
							tipo_documento,
							tipo_documento_extendido,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							fecha,
							debe,
							haber,
							codigo_cuenta,
							id_tercero,
							id_sucursal,
							id_empresa)
						VALUES $insertColgaap";
		$queryColgaap = mysql_query($sqlColgaap,$link);
		if(!$queryColgaap){ deleteError(0, "No se almaceno la contabilidad Colgaap en la base de datos", $arrayError, $link); }

		$sqlNiif = "INSERT INTO asientos_niif (
							id_documento,
							consecutivo_documento,
							tipo_documento,
							tipo_documento_extendido,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							fecha,
							debe,
							haber,
							codigo_cuenta,
							id_tercero,
							id_sucursal,
							id_empresa)
						VALUES $insertNiif";
		$queryNiif = mysql_query($sqlNiif,$link);
		if(!$queryNiif){ deleteError(1, "No se almaceno la contabilidad Niif en la base de datos", $arrayError, $link); }


		//===============================================// UPDATE DOCUMENTOS //===============================================//
		/***********************************************************************************************************************/
		$sqlUpdate  = "UPDATE $tablaBd SET estado = 1, total_factura_sin_abono=total_factura WHERE id_saldo_inicial='$idSaldoInicial' AND activo=1";
		$queryUdate = mysql_query($sqlUpdate, $link);
		if(!$queryUdate){ deleteError(2, "No se almaceno el nuevo estado de los documentos en la base de datos", $arrayError, $link); }

		//==============================================// UPDATE TABLA SALDOS //==============================================//
		/***********************************************************************************************************************/
		$sqlUpdate  = "UPDATE facturas_saldos_iniciales SET estado = 1, fecha_generacion = NOW() WHERE id='$arrayError[id]' AND activo=1";
		$queryUdate = mysql_query($sqlUpdate, $link);
		if(!$queryUdate){ deleteError(3, "No se almaceno el nuevo estado del saldo inicial en la base de datos", $arrayError, $link); }

		echo"<script>
				Actualiza_Div_encabezadoFacturasSaldosIniciales($idSaldoInicial);
				Win_Ventana_encabezado.close();
			</script>";

	}

	function deleteError($numberError, $msj, $arrayError, $link){
		if($msj != "") echo '<script>alert("Aviso,\n'.$msj.'")</script>';

		if($numberError == 0) exit;

		$sql   = "DELETE FROM asientos_colgaap WHERE $arrayError[asientos]";
		$query = mysql_query($sql,$link);
		if($numberError == 1) exit;

		$sql   = "DELETE FROM asientos_niif WHERE $arrayError[asientos]";
		$query = mysql_query($sql,$link);
		if($numberError == 2) exit;

		$sqlUpdate  = "UPDATE $arrayError[tablaDoc] SET estado = 0 WHERE id_saldo_inicial='$arrayError[id]' AND activo=1";
		$queryUdate = mysql_query($sqlUpdate, $link);
		if($numberError == 3) exit;

		$sqlUpdate  = "UPDATE facturas_saldos_iniciales SET estado = 0 WHERE id='$arrayError[id]' AND activo=1";
		$queryUdate = mysql_query($sqlUpdate, $link);
		if($numberError == 4) exit;
	}

	function editar_saldo_inicial($id_empresa, $idSaldoInicial, $opcGrillaContable, $link){

		$sqlEstado   = "SELECT COUNT(id) as cont, tipo_factura, estado, id_sucursal FROM facturas_saldos_iniciales WHERE id='$idSaldoInicial' AND id_empresa='$id_empresa' AND activo=1 LIMIT 0,1";
		$queryEstado = mysql_query($sqlEstado,$link);

		$cont        = mysql_result($queryEstado, 0, 'cont');
		$estado      = mysql_result($queryEstado, 0, 'estado');
		$tipoFactura = mysql_result($queryEstado, 0, 'tipo_factura');
		$id_sucursal = mysql_result($queryEstado, 0, 'id_sucursal');
		$tablaBd     = ($tipoFactura=='FV')? 'ventas_facturas' : 'compras_facturas';

		//=====================// BOQUEA BOTON SI HAY FACTURAS CRUZADAS //=====================//
		//*************************************************************************************//
		$sqlContFactura = "SELECT COUNT(F.id) AS contFactura
							FROM $tablaBd AS F INNER JOIN asientos_colgaap AS A ON (
									A.tipo_documento_cruce = '$tipoFactura'
									AND A.id_documento_cruce = F.id
									AND A.id_documento_cruce <> A.id_documento
									AND A.tipo_documento_cruce <> A.tipo_documento
								)
							WHERE F.activo=1
								AND F.id_sucursal=$id_sucursal
								AND F.id_empresa=$id_empresa
								AND F.id_saldo_inicial=$idSaldoInicial
								AND F.estado=1";
		$queryContFactura  = mysql_query($sqlContFactura,$link);
		$contFacturasCruce = mysql_result($queryContFactura, 0, 'contFactura');
		if($contFacturasCruce > 0){ echo '<script>alert("Aviso,\nNo se pueden editar los presentes saldos iniciales de facturacion, existen '.$contFacturasCruce.' documentos cruzados!")</script>'; exit; }


		if(!$queryEstado){ echo '<script>alert("Aviso\nA ocurrido un problema con la conexion a la base de datos!")</script>'; exit; }
		else if($cont == 0 || is_nan($cont)){ echo '<script>alert("Aviso\nNo se encontro el documento de saldos iniciales!")</script>'; exit; }
		else if($estado == 0){ echo '<script>alert("Aviso\nEl presente documento ya ha sido editado!")</script>'; exit; }

		if($tipoFactura == 'FV'){
			$sqlFacturas = "SELECT id, CONCAT(prefijo, ' ', numero_factura) AS numero_factura, total_factura FROM ventas_facturas WHERE id_saldo_inicial='$idSaldoInicial' AND activo=1";
		}
		else{
			$sqlFacturas = "SELECT id, CONCAT(prefijo_factura, ' ', numero_factura) AS numero_factura, total_factura FROM compras_facturas WHERE id_saldo_inicial='$idSaldoInicial' AND activo=1";
		}

		// echo $sqlFacturas;
		$queryFacturas = mysql_query($sqlFacturas,$link);

		$insertColgaap = "";
		$insertNiif    = "";
		$deleteEdit    = "";
		while ($row = mysql_fetch_array($queryFacturas)) { $deleteEdit .= "(id_documento='$row[id]' AND tipo_documento='$tipoFactura' AND id_empresa='$id_empresa') OR "; }

		$deleteEdit = substr($deleteEdit, 0, -4);

		if ($deleteEdit=='') { echo '<script>alert("Aviso!\nNo hay nada que editar!");</script>'; exit; }

		$sql   = "DELETE FROM asientos_colgaap WHERE $deleteEdit";
		$query = mysql_query($sql,$link);

		$sql   = "DELETE FROM asientos_niif WHERE $deleteEdit";
		$query = mysql_query($sql,$link);

		$sqlUpdate  = "UPDATE $tablaBd SET estado = 0 WHERE id_saldo_inicial='$idSaldoInicial' AND activo=1";
		$queryUdate = mysql_query($sqlUpdate, $link);

		$sqlUpdate  = "UPDATE facturas_saldos_iniciales SET estado = 0 WHERE id='$idSaldoInicial' AND activo=1";
		$queryUdate = mysql_query($sqlUpdate, $link);

		echo"<script>
				Actualiza_Div_encabezadoFacturasSaldosIniciales($idSaldoInicial);
				Win_Ventana_encabezado.close();
			</script>";

	}


	//FUNCION PARA GUARDAR EL ENCABEZADO DEL DOCUMENTO
	function guardarEncabezado($contrapartida_cuenta_pago_colgaap,$contrapartida_cuenta_pago_niif,$tipo_factura,$fecha_factura,$id_cuenta_pago,$filtro_sucursal,$id_empresa,$link){
		$sql   = "INSERT INTO facturas_saldos_iniciales (id_sucursal,tipo_factura,fecha,fecha_factura,id_cuenta_pago,id_empresa,contrapartida_colgaap,contrapartida_niif)
					VALUES ('$filtro_sucursal','$tipo_factura',NOW(),'$fecha_factura','$id_cuenta_pago','$id_empresa','$contrapartida_cuenta_pago_colgaap','$contrapartida_cuenta_pago_niif')";
		$query = mysql_query($sql,$link);
		if ($query) {
			$sql   = "SELECT LAST_INSERT_ID() AS id";
			$query = mysql_query($sql,$link);
			$id    = mysql_result($query,0,'id');

			echo '<script>
					Inserta_Div_encabezadoFacturasSaldosIniciales('.$id.');
					Win_Ventana_agregar_grilla.close();
				</script>';
		}
		else{ echo '<script>alert("Error\nNo se guardo el registro, Intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	//FUNCION PARA ACTUALIZAR EL ENCABEZADO DEL DOCUMENTO
	function actualizaEncabezado($contrapartida_cuenta_pago_colgaap,$contrapartida_cuenta_pago_niif,$id,$tipo_factura,$fecha_factura,$id_cuenta_pago,$filtro_sucursal,$id_empresa,$link){
		$sql  = "UPDATE facturas_saldos_iniciales
					SET id_sucursal='$filtro_sucursal',
						tipo_factura='$tipo_factura',
						fecha_factura='$fecha_factura',
						id_cuenta_pago='$id_cuenta_pago',
						id_empresa='$id_empresa',
						contrapartida_colgaap='$contrapartida_cuenta_pago_colgaap',
						contrapartida_niif='$contrapartida_cuenta_pago_niif'
					WHERE id=$id";
		$query = mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					Actualiza_Div_encabezadoFacturasSaldosIniciales('.$id.');
					Win_Ventana_editar_grilla.close();
				</script>';
		}
		else{ echo '<script>alert("Error\nNo se actualizo el registro, Intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	//FUNCION PARA ELIMINAR LA CABECERA
	function eliminaEncabezado($tipo_factura,$id,$id_sucursal,$id_empresa,$link){
		$sql   = "UPDATE facturas_saldos_iniciales SET activo=0 WHERE id=$id AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					Elimina_Div_encabezadoFacturasSaldosIniciales('.$id.');
					Win_Ventana_editar_grilla.close();
				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se elimino el registro, Intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	//SINCRONIZAR LA CUENTA COLGAAP DE CONTRAPARTIDA PARA NIIF
	function sincronizarCuentaNiif($cuenta,$id_empresa,$link){
		$sql         = "SELECT cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND cuenta='$cuenta' ";
		$query       = mysql_query($sql,$link);
		$cuenta_niif = mysql_result($query,0,'cuenta_niif');

		if ($cuenta_niif > 0) { echo'<script>document.getElementById("contrapartida_cuenta_pago_niif").value="'.$cuenta_niif.'";</script>'; }
		else{ echo'<script>alert("No existe una cuenta niif configurada a la cuenta colgaap actual");</script>'; }
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$campoFecha,$tablaPrincipal,$id_empresa,$link,$cont=0,$fecha=''){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT $campoFecha AS fecha FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);
		$fecha_documento =($fecha=='')? mysql_result($query,0,'fecha') : $fecha ;

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar = date("Y", strtotime($fecha_documento)).'-01-01';
		$fecha_fin_buscar    = date("Y", strtotime($fecha_documento)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final ";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND fecha_nota>='$fecha_inicio_buscar' AND fecha_nota<='$fecha_fin_buscar' ";
		$query=mysql_query($sql,$link);
		$cont2 = mysql_result($query,0,'cont');

		if ($cont1>0 || $cont2>0) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
					if (document.getElementById("bodyDivArticulos_'.$cont.'")) {
						console.log(document.getElementById("bodyDivArticulos_'.$cont.'").parentNode);
						document.getElementById("bodyDivArticulos_'.$cont.'").parentNode.removeChild(document.getElementById("bodyDivArticulos_'.$cont.'").nextSibling);

					}
				</script>';
			exit;
		}

	}

?>
