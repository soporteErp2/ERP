<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");
	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if ($opc<>'actualizaFechaDocumento') {
			verificaCierre($id,'fecha_inicio',$tablaPrincipal,$id_empresa,$link);
		}
	}

	switch ($opc) {
		case 'buscarCliente':
			buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaConfiguracionArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			ventanaConfiguracionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'buscarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($id,$campo,$valorArticulo,$idArticulo,$id_empresa,$opcGrillaContable,$whereBodega,$link);
			break;

		case 'cambiaCliente':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			cambiaCliente($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$link,$tablaPrincipal);
			break;

		case 'deleteArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			deleteArticulo($cont,$id,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'retrocederArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
		 	retrocederArticulo($id,$idArticulo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'actualizaFechaDocumento':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaFechaDocumento($id,$fecha,$tablaPrincipal,$opcGrillaContable,$link);
			break;

		case 'validaNota':
			validaNota($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$opcGrillaContable,$mysql);
			break;

		case 'terminarGenerar':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			terminarGenerar($id,$id_sucursal,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$mysql);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$mysql);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$id_empresa,$mysql);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$carpeta,$id_sucursal,$id_sucursal,$id_empresa,$tablaPrincipal,$mysql);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link);
			break;

		case 'guardarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantArticulo,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$depreciacionAcumulada,$valorDepreciacion,$id_sucursal_item,$mysql);
			break;

		case 'actualizaArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$costoArticulo,$exento_iva,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$depreciacionAcumulada,$valorDepreciacion,$link);
			break;

		case 'guardarObservacion':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'verificaCantidadArticulo':
			verificaCantidadArticulo($opcGrillaContable,$id,$id_sucursal,$filtro_bodega,$link);
			break;

		case 'sincronizarCuentaNiif':
			sincronizarCuentaNiif($id,$campoId,$campoText,$id_empresa,$link);
			break;

		case 'updateCuentasConcepto':
			updateCuentasConcepto($cuenta_colgaap,$cuenta_niif,$cuenta_contrapartida_colgaap,$cuenta_contrapartida_niif,$idArticulo,$id,$id_empresa,$link);
			break;

		case 'calculaValorDepreciacion':
			calculaValorDepreciacion($opcGrillaContable,$id_activo,$accion,$cont,$id_empresa,$link);
			break;

		case 'cambiaSyncNota':
			cambiaSyncNota($tipo,$id,$opcGrillaContable,$id_empresa,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'cargarActivosFijosSucursal':
			cargarActivosFijosSucursal($id_documento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$link);
			break;
	}

	//=========================== FUNCION PARA BUSCAR UN CLIENTE ===============================================================================//
	function buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link){

		if ($inputId=='nitTercero'.$opcGrillaContable) {
			$where   ='numero_identificacion="'.$codCliente.'" AND tipo_identificacion="'.$tipoDocumento.'"';
			$mensaje = 'alert("'.$tipoDocumento.' de tercero no establecido");';
		}
		else if ($inputId=='codigoTercero'.$opcGrillaContable) {
			$where   = 'codigo= "'.$codCliente.'"';
			$mensaje = 'alert("codigo de tercero no establecido");';
		}

		$sqlTercero   = "SELECT id, numero_identificacion, tipo_identificacion, codigo, nombre, COUNT(id) AS contTercero FROM terceros WHERE $where  AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryTercero = mysql_query($sqlTercero,$link);

		$contTercero = mysql_result($queryTercero,0,'contTercero');
		$idTercero   = mysql_result($queryTercero,0,'id');
		$nit         = mysql_result($queryTercero,0,'numero_identificacion');
		$tipoNit     = mysql_result($queryTercero,0,'tipo_identificacion');
		$codigo      = mysql_result($queryTercero,0,'codigo');
		$nombre      = mysql_result($queryTercero,0,'nombre');

		//GENERAMOS LA VARIABLE PARA HACER EL UPDATE DE LA TABLA PRINCIPAL
		if ($contTercero == 0) {
			$sqlDocumento   = "SELECT codigo_tercero, numero_identificacion_tercero, tipo_identificacion_tercero FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryDocumento = mysql_query($sqlDocumento,$link);

			$codigoTercero  = mysql_result($queryDocumento,0,'codigo_tercero');
			$nitTercero     = mysql_result($queryDocumento,0,'numero_identificacion_tercero');
			$tipoNitTercero = mysql_result($queryDocumento,0,'tipo_identificacion_tercero');

			echo'<script>
					'.$mensaje.'
					document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigoTercero.'";
					document.getElementById("tipoDocumento'.$opcGrillaContable.'").value = "'.$tipoNitTercero.'";
					document.getElementById("nitTercero'.$opcGrillaContable.'").value    = "'.$nitTercero.'";
				</script>';
			exit;
		}
		else if ($inputId=='nitTercero'.$opcGrillaContable) {
			$camposInsert = "codigo_tercero = '$codigo ',
							numero_identificacion_tercero = '$codCliente',
							tipo_identificacion_tercero = '$tipoDocumento'";
		}
		else if ($inputId=='codigoTercero'.$opcGrillaContable) {
			$camposInsert = "codigo_tercero = '$codigo ',
							numero_identificacion_tercero = '$nit',
							tipo_identificacion_tercero = '$tipoNit'";
		}

		$sqlUpdate = " UPDATE $tablaPrincipal
						SET id_tercero = '$idTercero',
							tercero = '$nombre',
							$camposInsert
						WHERE id='$id'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		echo'<script>
				document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigo.'";
				document.getElementById("tipoDocumento'.$opcGrillaContable.'").value = "'.$tipoNit.'";
				document.getElementById("nitTercero'.$opcGrillaContable.'").value    = "'.$nit.'";
				document.getElementById("nombreTercero'.$opcGrillaContable.'").value = "'.$nombre.'";

				id_cliente_'.$opcGrillaContable.'   = "'.$idTercero.'";
				nitTercero'.$opcGrillaContable.'    = "'.$nit.'";
				nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";

				//id_tipo_nota = document.getElementById("selectTipoNota").value;
				//if(arrayTipoNota[id_tipo_nota] == "Si"){ document.getElementById("contenedorNotaContable").setAttribute("class","contenedorNotaContableCruce"); }
				//else{ document.getElementById("contenedorNotaContable").setAttribute("class","contenedorNotaContable"); }
			</script>';
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		switch ($opcGrillaContable) {
			case 'CotizacionVenta':
				$typeDocument = 'COTIZACION';
				break;

			case 'PedidoVenta':
				$typeDocument = 'PEDIDO';
				break;

			case 'RemisionesVenta':
				$typeDocument = 'REMISION';
				break;

			case 'FacturaVenta':
				$typeDocument = 'FACTURA';
				break;

			default:
				$typeDocument = '';
				break;
		}

		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label" style="width:40px !important;"></div>
							<div class="label" style="width:12%">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label">Descuento</div>
							<div class="label">Precio</div>
							<div class="label">Total</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsInsertUnidades('return',$cont,$opcGrillaContable).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="overflow:visible; height:auto;">
							<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIva'.$opcGrillaContable.'">Iva</div>
							<div class="labelSimbolo" id="simboloIva'.$opcGrillaContable.'">$</div>
							<div class="labelTotal" style="height:auto !important;" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"> </div>
							<div class="labelSimbolo"  id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelTotal" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
						</div>
						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL '.$typeDocument.'</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotalAcumulado'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalAcumulado'.$opcGrillaContable.').toFixed(2);
					document.getElementById("ivaAcumulado'.$opcGrillaContable.'").innerHTML      = parseFloat(ivaAcumulado'.$opcGrillaContable.').toFixed(2);
					document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML    = parseFloat(total'.$opcGrillaContable.').toFixed(2);

					document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){
		$readonly = '';
		if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		$body =  '<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>
							<div class="campo" style="width:12%;">
								<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);">
							</div>
							<div class="campoNombreArticulo">
								<input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly>
							</div>
							<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
								<img src="images/buscar20.png">
							</div>
							<div class="campo">
								<input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly>
							</div>
							<div class="campo">
								<input type="text" id="depreciacionAcumulada'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="0">
							</div>
							<div class="campo">
								<input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" readonly value="1">
							</div>
							<div class="campo">
								<input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' readonly value="0">
							</div>
							<div class="campo">
								<input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');" value="0">
							</div>
							<div style="float:right; min-width:80px;">
								<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="images/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/config16.png"></div>
								<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/delete.png"></div>
							</div>
							<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0">
							<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0">
							<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="0">
							<input type="hidden" id="valorSalvamento'.$opcGrillaContable.'_'.$cont.'" value="0">
							<script>
								document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
							</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaConfiguracionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link){

		// CONSULTAR EL TIPO DE CONTABILIDAD DEL DOCUMENTO DEPRECIACION
		$sql="SELECT sinc_nota FROM activos_fijos_depreciaciones WHERE activo=1 AND id=$id";
		$query=mysql_query($sql,$link);
		$sinc_nota=mysql_result($query,0,'sinc_nota');
		$display='';
		if ($sinc_nota=='colgaap') {
			$display='display:none;';
		}

		$sql = "SELECT cuenta_depreciacion,contrapartida_depreciacion,cuenta_depreciacion_niif,contrapartida_depreciacion_niif
								FROM $tablaInventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$query=mysql_query($sql,$link);

		$cuenta_depreciacion             = mysql_result($query,0,'cuenta_depreciacion');
		$contrapartida_depreciacion      = mysql_result($query,0,'contrapartida_depreciacion');
		$cuenta_depreciacion_niif        = mysql_result($query,0,'cuenta_depreciacion_niif');
		$contrapartida_depreciacion_niif = mysql_result($query,0,'contrapartida_depreciacion_niif');

		echo '	<style>
					.campoConfigLabel{
						width:40%;
						float:left;
						margin-top:10px;
						text-align:left;
						margin-left: 25px;
						height:20px;
						padding-top: 2px;
					}
					.campoConfig{
						width:120px;
						float:left;
						margin-top:10px;
						background-color:#FFF;
						height:20px;
						padding-top: 2px;
					}
					.iconBuscarProveedor{
						width:22px;
						height:22px;
					}
				</style>
				<div style="width:100%;height:100%;background-color:#dfe8f6;">
					<div style="margin:auto;width:450px;text-align:center;padding-top: 20px;">
						<div id="divLoadConfigCuentas" style="width: 50px;"></div>
						<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;"></div>
						<div class="campoConfigLabel">Cuenta Colgaap</div><div class="campoConfig" id="cuenta_colgaap">'.$cuenta_depreciacion.'</div>
						<div id="id_cuenta_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'cuenta_colgaap\',\'id_cuenta_niif\',\'cuenta_niif\')" title="Sincronizar Niif" class="iconBuscarProveedor" style="display:none;overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
                		    <img src="images/refresh.png">
                		</div>
                		<div onclick="ventanaBuscarCuentasArticulo(\'colgaap\',\'id_cuenta_colgaap\',\'cuenta_colgaap\')" title="Buscar" class="iconBuscarProveedor" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="images/buscar20.png">
                		</div>
						<div class="campoConfigLabel" style="'.$display.'">Cuenta Niif</div><div class="campoConfig" id="cuenta_niif" style="'.$display.'">'.$cuenta_depreciacion_niif.'</div>
						<div onclick="ventanaBuscarCuentasArticulo(\'niif\',\'id_cuenta_niif\',\'cuenta_niif\')" title="Buscar" class="iconBuscarProveedor" style="'.$display.'margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="images/buscar20.png">
                		</div>

						<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;float:left;"></div>
						<div class="campoConfigLabel">Cuenta Contrapartida Colgaap</div><div class="campoConfig" id="cuenta_contrapartida_colgaap">'.$contrapartida_depreciacion.'</div>
						<div id="id_cuenta_contrapartida_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'cuenta_contrapartida_colgaap\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Sincronizar Niif" class="iconBuscarProveedor" style="display:none;overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
                		    <img src="images/refresh.png">
                		</div>
						<div onclick="ventanaBuscarCuentasArticulo(\'colgaap\',\'id_cuenta_contrapartida_colgaap\',\'cuenta_contrapartida_colgaap\')" title="Buscar" class="iconBuscarProveedor" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="images/buscar20.png">
                		</div>
						<div class="campoConfigLabel" style="'.$display.'">Cuenta Contrapartida Niif</div><div class="campoConfig" id="cuenta_contrapartida_niif" style="'.$display.'">'.$contrapartida_depreciacion_niif.'</div>
						<div onclick="ventanaBuscarCuentasArticulo(\'niif\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Buscar" class="iconBuscarProveedor" style="'.$display.'margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="images/buscar20.png">
                		</div>

					</div>

				</div>
			';
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaInventario SET observaciones='$observacion' WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){ echo '<script>Win_Ventana_descripcion_Articulo_factura.close(id);</script>'; }
		else{ echo '<script>alert("La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema");</script>'; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
	function buscarArticulo($id,$campo,$valorArticulo,$idArticulo,$id_empresa,$opcGrillaContable,$whereBodega,$link){
		// CONSULTAR QUE EL ACTIVO NO ESTE YA AGREGADO EN LA PRESENTE NIOTA
		$sql="SELECT id_activo_fijo,nombre FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_empresa=$id_empresa AND id_depreciacion=$id";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$arrayActivos[$row['id_activo_fijo']]=$row['nombre'];
		}

		$sqlArticulo = "SELECT id,codigo_activo,nombre_equipo,unidad,costo,depreciacion_acumulada,valor_salvamento
							FROM activos_fijos
						WHERE  activo = 1
							AND estado=1 AND depreciable='Si'
							$whereBodega
							AND codigo_activo = '$valorArticulo'
						LIMIT 0,1";

		$query = mysql_query($sqlArticulo,$link);

		$id                     = mysql_result($query,0,'id');
		$codigo_activo          = mysql_result($query,0,'codigo_activo');
		$nombre_equipo          = mysql_result($query,0,'nombre_equipo');
		$unidad                 = mysql_result($query,0,'unidad');
		$costo                  = mysql_result($query,0,'costo');
		$depreciacion_acumulada = mysql_result($query,0,'depreciacion_acumulada');
		$valor_salvamento       = mysql_result($query,0,'valor_salvamento');

		if ($id==0 || $id=='') {
			echo'<script>
					alert("El codigo '.$valorArticulo.' No se encuentra asignado en los activos fijos");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     ="0";
					document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       ="";
					document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value  ="";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value ="";
					document.getElementById("depreciacionAcumulada'.$opcGrillaContable.'_'.$idArticulo.'").value = "";
					document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "";
					document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "";
				</script>';
		}
		else if(array_key_exists($id, $arrayActivos)){
			echo '<script>
					alert("El activo Fijo buscado ya esta agregado en el documento!");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
				</script>';
		}
		else if(($costo-$valor_salvamento)<=$depreciacion_acumulada){
			echo '<script>
					alert("El activo Fijo buscado ya no se puede depreciar mas!");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
				</script>';
		}
		else if($id > 0){
			$valorDepreciar=calculaValorDepreciacion($opcGrillaContable,$id,'',0,$id_empresa,$link);
			// 	//si la cantidad del articulo es mayor a cero en la bodega, se permite realizar la venta del articulo
				echo'<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value           = "'.$unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value         = "'.$id.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value        = "'.$codigo_activo.'";
						document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "'.$valorDepreciar.'";
						document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "1";

						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value      = "'.$costo.'";
						document.getElementById("depreciacionAcumulada'.$opcGrillaContable.'_'.$idArticulo.'").value      = "'.$depreciacion_acumulada.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "'.$nombre_equipo.'";

						setTimeout(function(){ document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },50);
						'.$script.'
					</script>';

		}

	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederArticulo($id,$idRegistro,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$sqlArticulo ="SELECT id_activo_fijo,codigo_activo,nombre,unidad,costo,dias_depreciar,valor
						FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal='$id' AND id='$idRegistro' LIMIT 0,1; ";

		$query = mysql_query($sqlArticulo,$link);

		$id_activo_fijo = mysql_result($query,0,'id_activo_fijo');
		$codigo         = mysql_result($query,0,'codigo_activo');
		$nombre         = mysql_result($query,0,'nombre');
		$unidad         = mysql_result($query,0,'unidad');
		$costo          = mysql_result($query,0,'costo');
		$dias_depreciar = mysql_result($query,0,'dias_depreciar');
		$valor					= mysql_result($query,0,'valor');

		echo '<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value                 = "'.$unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value               = "'.$id_activo_fijo.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value              = "'.$codigo.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value            = "'.$costo.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value           = "'.$nombre.'";
						document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.$dias_depreciar.'";
						document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'").value       = "'.$valor.'";

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					</script>';
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteArticulo($cont,$id,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){
		$sql="SELECT valor FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND id='$idArticulo'";
		$query=mysql_query($sql,$link);
		$valor=mysql_result($query,0,'valor');

		$sqlDelete   = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND id='$idArticulo'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{

			echo '<script>
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
					calculaValorTotalesDocumento("eliminar",'.$valor.');
				</script>';
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR LA FORMA DE PAGO ======================================================================//
	function actualizaFechaDocumento($id,$fecha,$tablaPrincipal,$opcGrillaContable,$link){

		$sql   = "UPDATE $tablaPrincipal SET fecha_inicio='$fecha' WHERE id='$id'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo '<script>alert("\u00A1Error!\nNo se actualizo la fecha");</script>'; }
	}

	//=========================== FUNCION PARA TERMINAR 'GENERAR' EL DOCUMENTO ==============================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_sucursal,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$mysql){
		global $id_empresa;

		//VALIDAR SI EL DOCUMENTO TIENE UN TERCERO ASIGNADO
		$sql   	 = "SELECT tercero FROM $tablaPrincipal WHERE activo = 1 AND id = '$id' AND id_sucursal = '$id_sucursal' AND id_empresa = '$id_empresa'";
		$query	 = $mysql->query($sql,$mysql->link);
		$tercero = $mysql->result($query,0,'tercero');
		if($tercero == ''){
			echo '<script>
							alert("El documento no tiene tercero seleccionado");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}

		//CONSULTAR SI LA DEPRECIACION DE LOS ACTIVOS NO SUPERA EL VALOR DE SALVAMENTO
		$sqlActivosDepreciados = "SELECT
																AFDI.nombre,
																AFDI.codigo_activo,
																AFDI.costo,
																AF.depreciacion_acumulada,
																AF.valor_salvamento,
																AFDI.valor
															FROM
																activos_fijos_depreciaciones_inventario AS AFDI
															LEFT JOIN
																activos_fijos AS AF
															ON
																AF.id = AFDI.id_activo_fijo
															WHERE
																AFDI.activo = 1
															AND
																AFDI.id_empresa = $id_empresa
															AND
															  AFDI.id_depreciacion = $id";
    $queryActivosDepreciados = $mysql->query($sqlActivosDepreciados,$mysql->link);
		while($row = $mysql->fetch_array($queryActivosDepreciados)){
			if(($row['costo'] - $row['depreciacion_acumulada'] - $row['valor_salvamento']) < $row['valor']){
				$activosError .= $row['nombre'] . ' - ' . $row['codigo_activo'] . '\n';
			}
		}
		if($activosError != ""){
			echo '<script>
							alert("\u00A1Error!\nLos siguientes activos tienen problemas:' . '\n' . $activosError . 'Por favor eliminarlos y volverlos a cargar.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}

		//ACTUALIZAR EL ESTADO DEL DOCUMENTO
		$sql   = "UPDATE activos_fijos_depreciaciones SET estado = 1 WHERE activo = 1 AND id_empresa = $id_empresa AND id = $id";
		$query = $mysql->query($sql,$mysql->link);
		if($query){
			// CONSULTAR EL CONSECUTIVO
			$sql="SELECT consecutivo,fecha_inicio,id_sucursal FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=$mysql->query($sql,$mysql->link);
			$consecutivo = $mysql->result($query,0,'consecutivo');
			$fecha       = $mysql->result($query,0,'fecha_inicio');
			$id_sucursal = $mysql->result($query,0,'id_sucursal');
			if ($consecutivo>0) {
				// MOVER LOS ASIENTOS CONTABLES DE LOS ACTIVOS PARA LA DEPRECIACION
				moverCuentasDocumento($id,$consecutivo,$fecha,'contabilizar',$id_sucursal,$id_empresa,$mysql);

				// ACTUALIZAR EL VALOR DE LA DEPRECIACION ACUMULADA DE LOS ACTIVOS DEL DOCUENTO
				actualizaDepreciacionAcumuladaActivos($id,'agregar',$id_empresa,$mysql);

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','DA','Depreciacion Activos Fijos','".$_SESSION['SUCURSAL']."','".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$query = $mysql->query($sql,$mysql->link);
				echo '<script>
							 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
									url     : "depreciaciones/bd/grillaContableBloqueada.php",
									scripts : true,
									nocache : true,
									params  :
									{
										filtro_sucursal   : "'.$id_sucursal.'",
										opcGrillaContable : "'.$opcGrillaContable.'",
										id_depreciacion   : "'.$id.'"
									}
								});
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
			}
			else{
				$sql = "UPDATE activos_fijos_depreciaciones SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
				$query = $mysql->query($sql,$mysql->link);
				echo '<script>
								alert("No se genero consecutivo del documento\nIntentelo de nuevo");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
				exit;
			}
		}
	}

	//======================== VALIDA NOTA Y EJECUTA LA FUNCION TERMINAR ========================//
	function validaNota($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$opcGrillaContable,$mysql){
		// VALIDACION DE LAS CUENTAS DE LOS ACTIVOS FIJOS CARGADOS
		$sql   = "SELECT
						codigo,
						nombre,
						cuenta_depreciacion,
						contrapartida_depreciacion,
						cuenta_depreciacion_niif,
						contrapartida_depreciacion_niif
					FROM $tablaInventario
					WHERE
						activo            = 1
					AND $idTablaPrincipal = '$id'
					AND id_sucursal       = '$id_sucursal'
					AND id_empresa        = '$id_empresa'";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$codigo                          = $row['codigo'];
			$nombre                          = $row['nombre'];
			$cuenta_depreciacion             = $row['cuenta_depreciacion'];
			$contrapartida_depreciacion      = $row['contrapartida_depreciacion'];
			$cuenta_depreciacion_niif        = $row['cuenta_depreciacion_niif'];
			$contrapartida_depreciacion_niif = $row['contrapartida_depreciacion_niif'];

			if ($cuenta_depreciacion == ''
				|| $contrapartida_depreciacion == ''
				|| $cuenta_depreciacion_niif == ''
				|| $contrapartida_depreciacion_niif == ''){
				$activos_error .= '\n'.$codigo.' - '.$nombre;
			}
		}

		if($activos_error <> '') {
			echo '<script>
					alert("Aviso,\nLos siguientes activos no tienen cuentas! '.$activos_error.'")
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}

		$sql   = "SELECT tercero,fecha_inicio FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$query=$mysql->query($sql,$mysql->link);
		$tercero      = $mysql->result($queryNota,0,'tercero');
		$fecha_inicio = $mysql->result($queryNota,0,'fecha_inicio');

		$mes_fecha_nota  = date("m",strtotime($fecha_inicio));
		$anio_fecha_nota = date("y",strtotime($fecha_inicio));

		if ($mes_fecha_nota=='12') {
			$anio_fecha_nota++;
			$mes_fecha_nota='01';
		}
		else{ $mes_fecha_nota++; }

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_buscar=$anio_fecha_nota.'/'.$mes_fecha_nota.'/01';

		if($tercero==''){
			echo '<script>
					alert("Debe Seleccionar el tercero!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$sqlNotaGenerada   = "SELECT COUNT(id) AS cont FROM $tablaPrincipal WHERE fecha_inicio>='$fecha_buscar' AND activo=1 AND estado=1 AND id_sucursal='$id_sucursal' AND id_empresa=$id_empresa";
		$queryNotaGenerada = mysql_query($sqlNotaGenerada,$link);
		$contNotaGenerada  = mysql_result($queryNotaGenerada,0,'cont');
		//SI CONT ES MAYOR A CERO, HAY NOTAS GENERADAS EN EL MES SIGUIENTE, ASI QUE SE ADVERTIRA AL USUARIO
		if ($cont>0) {
			echo '<script>
					if (confirm("Aviso!\nExiten '.$cont.' notas creadas del mes siguiente a la fecha de la nota!\nSi continua no coincidara el consecutivo con el mes\nDesea continuar de todos modos?")) {
						validarNotaGeneral("terminar");
					}
				</script>';
			return;
		}
		else{
			terminarGenerar($id,$id_sucursal,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$link);
		}
	}

	// FUNCION PARA GENERAR LOS ASIENTOS DE LOS ACTIVOS, O ELIMINAR LOS ASIENTOS GENERADOS
	function moverCuentasDocumento($id_documento,$consecutivo,$fecha,$accion,$id_sucursal,$id_empresa,$mysql){
		if ($accion=='contabilizar') {
			// CONSULTAR EL TERCERO DEL DOCUMENTO
			$sql   = "SELECT id_tercero FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id_documento";
			$query = $mysql->query($sql,$mysql->link);
			$id_tercero = $mysql->result($query,0,'id_tercero' );

			// CONSULTAR TODOS LOS ACTIVOS DEL DOCUMENTO
			$sql="SELECT
						id_activo_fijo,
						code_bar,
						codigo_activo,
						nombre,
						valor,
						(SELECT id_centro_costos FROM activos_fijos WHERE activo=1 AND id=id_activo_fijo ) AS id_centro_costos
					FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_depreciacion=$id_documento AND id_empresa=$id_empresa";
			$query=$mysql->query($sql,$mysql->link);
			// ACUMULAR LAS CUENTAS CON SUS VALORES
			while ($row=$mysql->fetch_array($query)){
				$id_activo = $row['id_activo_fijo'];
				$arrayActivo[$id_activo]['datos'] = array(
														'code_bar'         => $row['code_bar'],
														'codigo_activo'    => $row['codigo_activo'],
														'nombre'           => $row['nombre'],
														'valor'            => $row['valor'],
														'id_centro_costos' => $row['id_centro_costos']
														);

				$whereIdActivos .= ($whereIdActivos=='')? " id_activo=$id_activo " : " OR id_activo=$id_activo " ;

			}

			// CONSULTAR LAS CUENTAS DE LOS ACTIVOS
			$sql="SELECT
						id_activo,
						descripcion,
						estado,
						id_cuenta,
						cuenta,
						descripcion_cuenta
					FROM activos_fijos_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdActivos) AND contabilidad='colgaap' AND descripcion='depreciacion' ";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$id_activo          = $row['id_activo'];
				$whereCuentas .= ($whereCuentas=='')? " cuenta='$row[cuenta]' " : " OR cuenta='$row[cuenta]' " ;

				$arrayActivo[$id_activo]['cuentas'][] = array(
																'estado'             => $row['estado'],
																'id_cuenta'          => $row['id_cuenta'],
																'cuenta'             => $row['cuenta'],
																'descripcion_cuenta' => $row['descripcion_cuenta'],
															);
			}

			// CONSULTAR SI LAS CUENTAS TIENEN CENTROS DE COSTOS
			$sql = "SELECT cuenta,cuenta_niif,centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND ( $whereCuentas ) ";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$arrayCuentasCcosColgaap[$row['cuenta']]   = $row['centro_costo'];
			}

			// VERIFICAR QUE TODOS LOS ACTIVOS TENGAN LAS CUENTAS
			foreach ($arrayActivo as $id_activo => $arrayActivos) {
				foreach ($arrayActivos['cuentas'] as $key => $arrayResult) {
					// VALIDAR QUE EL ACTIVO TENGA UNA CUENTA
					if ($arrayResult['cuenta']=='' || $arrayResult['cuenta']==0) {
						$sql="UPDATE activos_fijos_depreciaciones SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
						$query=$mysql->query($sql,$mysql->link);
						echo '<script>
								alert("\u00A1Error!\nEl activo '.$arrayActivos['datos']['codigo_activo'].' - '.$arrayActivos['datos']['nombre'].' no tiene cuentas contables configuradas!");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						exit;
					}
					// VALIDAR QUE LA CUENTA TENGA CENTRO DE COSTOS SI ES OBLIGATORIO
					if($arrayCuentasCcosColgaap[$arrayResult['cuenta']] == 'Si' && ($arrayActivos['datos']['id_centro_costos'] == 0 || $arrayActivos['datos']['id_centro_costos'] == '')){
						$sql = "UPDATE activos_fijos_depreciaciones SET estado = 0 WHERE activo = 1 AND id_empresa = $id_empresa AND id = $id_documento";
						$query = $mysql->query($sql,$mysql->link);
						echo '<script>
										alert("\u00A1Error!\nEl activo '.$arrayActivos['datos']['codigo_activo'].' - '.$arrayActivos['datos']['nombre'].' no tiene centro de costos y la cuenta '.$arrayResult['cuenta'].' esta configurada para causar centro de costos!");
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
									</script>';
						exit;
					}

					$arrayAsientos[$arrayActivos['datos']['id_centro_costos']][$arrayResult['cuenta']][$arrayResult['estado']] += $arrayActivos['datos']['valor'];
				}
			}

			// ASIENTOS COLGAAP
			foreach ($arrayAsientos as $id_centro_costos => $arrayAsientosCuentas) {
				foreach ($arrayAsientosCuentas as $cuenta => $arrayAsientosResul) {
					foreach ($arrayAsientosResul as $caracter => $saldo) {
						$id_centro_costos_insert = '';
						if ($caracter=='debito') {
							$debito  = $saldo;
							$credito = 0;
						}
						else{
							$debito  = 0;
							$credito = $saldo;
						}

						$acumDebito  += $debito;
						$acumCredito += $credito;

						$id_centro_costos_insert =($arrayCuentasCcosColgaap[$cuenta]=='Si')? $id_centro_costos : 0 ;

						$valueInsertAsientos .= "('$id_documento',
													'$consecutivo',
													'DA',
													'$id_documento',
													'$consecutivo',
													'DA',
													'Depreciacion Activos Fijos',
													'".$fecha."',
													'".$debito."',
													'".$credito."',
													'".$cuenta."',
													'$id_tercero',
													'$id_centro_costos_insert',
													'$id_sucursal',
													'$id_empresa'),";
					}
				}
			}

			if($acumDebito <> $acumCredito){
				echo '<script>
								alert("\u00A1Error!\nDiferencia en saldo debito y credito de '.($acumDebito - $acumCredit).' ");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
				$sql   = "UPDATE activos_fijos_depreciaciones SET estado = 0 WHERE id = '$id_documento' AND id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND activo = 1";
				$query = mysql_query($sql,$link);
				exit;
			}

			$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
			// INSERTAR LOS ASIENTOS
			$sql = "INSERT INTO
								asientos_colgaap(
									id_documento,
									consecutivo_documento,
									tipo_documento,
									id_documento_cruce,
									numero_documento_cruce,
									tipo_documento_cruce,
									tipo_documento_extendido,
									fecha,
									debe,
									haber,
									codigo_cuenta,
									id_tercero,
									id_centro_costos,
									id_sucursal,
									id_empresa
								)
							VALUES
								$valueInsertAsientos";
			$query = $mysql->query($sql,$mysql->link);

			if(!$query){
				// ACTUALIZAR EL ESTADO DEL DOCUMENTO
				$sql="UPDATE activos_fijos_depreciaciones SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
				$query=$mysql->query($sql,$mysql->link);
				echo '<script>
								alert("\u00A1Error!\nNo se insertaron los asientos colgaap\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
				exit;
			}

			// CUENTAS SIMULTANEAS
			contabilizacionSimultanea($id_documento,'DA',$id_sucursal,$idEmpresa,$mysql);

			// CONSULTAR EL TIPO DE NOTA, SI ES SOLO COLGAAP NO SE DEBE INSERTAR LOS ASIENTOS NIIF
			$sql="SELECT sinc_nota FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			$query=$mysql->query($sql,$mysql->link);
			$sinc_nota=$mysql->result($query,0,'sinc_nota');

			if ($sinc_nota=='colgaap') {
				return;
			}

		}
		else if($accion=='descontabilizar'){
			// BORRAR ASIENTOS COLGAAP
			$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_documento=$id_documento AND tipo_documento='DA' ";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				echo '<script>
						alert("\u00A1Error!\nNo se eliminaron los asientos colgaap\nIntentelo de nuevo");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

		}
	}

	function actualizaDepreciacionAcumuladaActivos($id_documento,$accion,$id_empresa,$mysql){
		if ($accion=='agregar') {
			$sql="UPDATE activos_fijos AS AF,
						 (
							SELECT
								AFD.*
							FROM
								activos_fijos_depreciaciones_inventario AS AFD
							WHERE
								AFD.id_depreciacion =  $id_documento
							AND AFD.activo=1
							AND AFD.id_empresa = '$id_empresa'
						) AS AFD
						SET AF.depreciacion_acumulada = AF.depreciacion_acumulada + AFD.valor
						WHERE
							AF.id = AFD.id_activo_fijo
						AND AF.activo=1
						AND AF.id_empresa = $id_empresa";
		}
		else if ($accion=='eliminar') {
			$sql="UPDATE activos_fijos AS AF,
						 (
							SELECT
								AFD.*
							FROM
								activos_fijos_depreciaciones_inventario AS AFD
							WHERE
								AFD.id_depreciacion =  $id_documento
							AND AFD.activo=1
							AND AFD.id_empresa = '$id_empresa'
						) AS AFD
						SET AF.depreciacion_acumulada = AF.depreciacion_acumulada - AFD.valor
						WHERE
							AF.id = AFD.id_activo_fijo
						AND AF.activo=1
						AND AF.id_empresa = $id_empresa";
		}

		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {

			$sql="UPDATE activos_fijos_depreciaciones SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			$query=$mysql->query($sql,$mysql->link);

			// CONSULTAR LA SUCURSAL DEL DOCUMENTO+
			$sql="SELECT id_sucursal FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			$query=$mysql->query($sql,$mysql->link);
			$id_sucursal = $mysql->result($query,0,'id_sucursal');

			moverCuentasDocumento($id_documento,0,0,'descontabilizar',$id_sucursal,$id_empresa,$mysql);

			echo '<script>
					alert("\u00A1Error!\nNo se Actualizo el valor de la depreciacion acumulada de los activos");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
	}

	//=============================================== FUNCION EDITAR UN DOCUMENTO YA GENERADA ===============================================================//
	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$mysql){
		// DEVOLVER EL VALOR ACUMULADOD DE LOS ACTIVOS
		actualizaDepreciacionAcumuladaActivos($idDocumento,'eliminar',$id_empresa,$mysql);

		// DESCONTABILIZAR EL DOCUMENTO
		moverCuentasDocumento($idDocumento,0,0,'descontabilizar',$id_sucursal,$id_empresa,$mysql);

		//ACTUALIZAMOS LA FACTURA DE COMPRA A ESTADO 0 'SIN GUARDAR'
		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND activo=1";
		$query = $mysql->query($sql,$mysql->link);

		if (!$query) {
			$sql   = "UPDATE $tablaPrincipal SET estado=1 WHERE id='$idDocumento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND activo=1";
			$query = $mysql->query($sql,$mysql->link);
			echo '<script>
							alert("\u00A1Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			return;
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sql   = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','DA','Depreciacion Activos Fijos','$id_sucursal','$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$query = $mysql->query($sql,$mysql->link);

		echo '<script>
					 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "depreciaciones/grilla/grillaContable.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_sucursal   : "'.$id_sucursal.'",
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_depreciacion   : "'.$idDocumento.'",
							}
						});

						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					</script>';
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$id_empresa,$mysql){
		// DEVELVER EL VALOR ACUMULADOD DE LOS ACTIVOS
		actualizaDepreciacionAcumuladaActivos($id,'eliminar',$id_empresa,$mysql);
		// DESCONTABILIZAR EL DOCUMENTO
		moverCuentasDocumento($id,0,0,'descontabilizar',$id_sucursal,$id_empresa,$mysql);

		$sql   			 = "SELECT consecutivo,estado FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";
		$query       = $mysql->query($sql,$mysql->link);
		$estado      = $mysql->result($query,0,'estado');
		$consecutivo = $mysql->result($query,0,'consecutivo');

		$sql="UPDATE $tablaPrincipal SET estado=3 WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";

		if($estado == 3){
			echo '<script>
					alert("El documento ya esta cancelado!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if($estado == 0 && $consecutivo == ''){
			$sql="UPDATE $tablaPrincipal SET activo=0 WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";
		}

		$query=$mysql->query($sql,$mysql->link);			  //EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		if (!$query) {
			echo '<script>
					alert("\u00A1Error!\nNo se logro cancelar el documento");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else{

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','DA','Depreciacion Activos Fijos',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$query=$mysql->query($sql,$mysql->link);
			echo'<script>
					nueva'.$opcGrillaContable.'();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$id_sucursal,$id_empresa,$tablaPrincipal,$mysql){

		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";
		$query = $mysql->query($sql,$mysql->link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if($query){

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sql = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','DA','Depreciacion Activos Fijos',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$query = $mysql->query($sql,$mysql->link);

			echo '<script>
							Ext.get("contenedor_'.$opcGrillaContable.'").load({
								url     : "depreciaciones/grilla/grillaContable.php",
								scripts : true,
								nocache : true,
								params  :
								{
									id_depreciacion   : "'.$idDocumento.'",
									opcGrillaContable : "'.$opcGrillaContable.'",
									id_sucursal       : "'.$id_sucursal.'"
								}
							});
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
		}
		else{
			echo '<script>
							alert("\u00A1Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			return;
		}
 	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantArticulo,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$depreciacionAcumulada,$valorDepreciacion,$id_sucursal_item,$mysql){
		global $id_empresa;

		$sql = "SELECT
							id
						FROM
							activos_fijos_cuentas
						WHERE
							id_activo = '$idInventario'";
	  $query = $mysql->query($sql,$mysql->link);
		$contFilas = $mysql->num_rows($query);

		if($contFilas < 8){
			echo '<script>
							alert("\u00A1Error!\n No se ha almacenado el articulo en el documento, porque no tiene las cuentas configuradas.");
							document.getElementById("bodyDivArticulosDepreciaciones_"+contArticulos'.$opcGrillaContable.').parentNode.removeChild(document.getElementById("bodyDivArticulosDepreciaciones_"+contArticulos'.$opcGrillaContable.'));
						</script>';
		}
		else{
			$sql = "INSERT INTO
								$tablaInventario(
							  	$idTablaPrincipal,
							  	id_activo_fijo,
									dias_depreciar,
									costo,
									valor,
									depreciacion_acumulada,
									id_empresa,
									id_sucursal
								)
							VALUES(
								'$id',
								'$idInventario',
								'$cantArticulo',
								'$costoArticulo',
								'$valorDepreciacion',
								'$depreciacionAcumulada',
								'$id_empresa',
								'$id_sucursal_item'
							)";
			$query = $mysql->query($sql,$mysql->link);
			if($query){
				$lastId = $mysql->insert_id();
				echo '<script>
								document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value            = '.$lastId.'
								document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
								document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","images/reload.png");
								document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display        = "none";
								document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display    = "none";
								document.getElementById("descripcionArticulo'.$opcGrillaContable.'_'.$cont.'").style.display = "block";
								document.getElementById("deleteArticulo'.$opcGrillaContable.'_'.$cont.'").style.display      = "block";
							</script>'.cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);
			}
			else{
				echo '<script>
								alert("\u00A1Error!\n No se ha almacenado el articulo en el documento, si el problema persiste favor comuniquese con la administracion del sistema");
								document.getElementById("bodyDivArticulosDepreciaciones_"+contArticulos'.$opcGrillaContable.').parentNode.removeChild(document.getElementById("bodyDivArticulosDepreciaciones_"+contArticulos'.$opcGrillaContable.'));
							</script>';
			}
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$costoArticulo,$exento_iva,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$depreciacionAcumulada,$valorDepreciacion,$link){

		$sqlArticuloAnterior   = "SELECT dias_depreciar,costo,valor,depreciacion_acumulada FROM $tablaInventario WHERE id='$idInsertArticulo' AND $idTablaPrincipal='$id' ";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);

		$valor = mysql_result($queryArticuloAnterior,0,'valor');
		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		echo '<script>
						calculaValorTotalesDocumento("eliminar",'.$valor.');
					</script>';

		$sqlUpdateArticulo = "UPDATE $tablaInventario
													SET
														id_activo_fijo 		     = '$idInventario',
														dias_depreciar         = '$cantArticulo',
														costo                  = '$costoArticulo',
														valor                  = '$valorDepreciacion',
														depreciacion_acumulada = '$depreciacionAcumulada'
													WHERE $idTablaPrincipal = $id
													AND id = $idInsertArticulo";

		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);

		if($queryUpdateArticulo){
			echo '<script>
							document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
							document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
							//llamamos la funcion para recalcular el costo de la factura
							calculaValorTotalesDocumento("agregar",'.$valorDepreciacion.');
						</script>';
		}
		else{
			echo '<script>
							alert("Error, no se actualizo el articulo");
						</script>';
		}
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION INDIVIDUAL POR ARTICULO =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sql   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sql,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//=========================== FUNCION PARA VERIFICAR LA CANTIDAD EXISTENTE DEL ARTICULO  ======================================================//
	function verificaCantidadArticulo($opcGrillaContable,$id,$id_sucursal,$filtro_bodega,$link){
		$sql           = "SELECT cantidad,inventariable,estado_venta FROM inventario_totales WHERE id_item='$id' AND id_sucursal='$id_sucursal' AND id_ubicacion='$filtro_bodega' AND id_empresa=".$_SESSION['EMPRESA']." LIMIT 0,1";
		$query         = mysql_query($sql,$link);
		$inventariable = mysql_result($query,0,'inventariable');
		$estado_venta  = mysql_result($query,0,'estado_venta');
		$cantidad      = mysql_result($query,0,'cantidad');

		$cantidad = ($inventariable=='false' && $estado_venta=='true')? 9999 : $cantidad;

		if (!$query) { echo 'false'; }
		else { echo $cantidad;  }
	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$link){

		$campoConsec=($tablaPrincipal=='ventas_facturas')? ' numero_factura_completo AS consecutivo ' : ' consecutivo ' ;

		$sql="SELECT estado,id_bodega,$campoConsec FROM $tablaPrincipal WHERE id=$id_documento";
		$query=mysql_query($sql,$link);

		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');
		$consecutivo = mysql_result($query,0,'consecutivo');
		if ($estado==1) {
			$mensaje='\u00A1Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='\u00A1Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='\u00A1Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el';
		}

		if ($opcGrillaContable=='CotizacionVenta'){
			$titulo='Cotizacion de Venta';
		}
		else if ($opcGrillaContable=='PedidoVenta'){
			$titulo='Pedido de Venta';
		}
		else if ($opcGrillaContable=='RemisionesVenta'){
			$titulo='Remision de Venta';
		}
		else{
			$titulo='Factura de Venta';
		}

		if ($estado>0) {
			echo'<script>
						alert("'.$mensaje.'");
						if (document.getElementById("Win_Ventana_descripcion_Articulo_factura")) {
							Win_Ventana_descripcion_Articulo_factura.close();
						}
						if (document.getElementById("Win_Ventana_update_fecha_FacturaVenta")) {
							Win_Ventana_update_fecha_FacturaVenta.close();
						}
						if (document.getElementById("Win_Ventana_configRetenciones_FacturaVenta")) {
							Win_Ventana_configRetenciones_FacturaVenta.close();
						}

						Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "bd/grillaContableBloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega     : "'.$id_bodega.'",
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_factura_venta  : "'.$id_documento.'"
							}
						});

						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
					</script>';
			exit;
		}
	}

	// SINCRONIZAR LA CUENTA NIIF
	function sincronizarCuentaNiif($id,$campoId,$campoText,$id_empresa,$link){
		$sqlNiif   = "SELECT cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND cuenta=$id";
		$queryNiif = mysql_query($sqlNiif,$link);

		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');

		if($cuentaNiif == 0 || $cuentaNiif==''){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$id.'");</script>'; }
		else{ echo '<script>
						document.getElementById("'.$campoText.'").innerHTML = "'.$cuentaNiif.'";
					</script>'; }

		echo'<img src="images/refresh.png" />';
	}

	function updateCuentasConcepto($cuenta_colgaap,$cuenta_niif,$cuenta_contrapartida_colgaap,$cuenta_contrapartida_niif,$idArticulo,$id,$id_empresa,$link){
		$sql = "UPDATE activos_fijos_depreciaciones_inventario
				SET cuenta_depreciacion='$cuenta_colgaap',
					contrapartida_depreciacion='$cuenta_contrapartida_colgaap',
					cuenta_depreciacion_niif='$cuenta_niif',
					contrapartida_depreciacion_niif='$cuenta_contrapartida_niif'
				WHERE activo=1 AND id_empresa=$id_empresa AND id_depreciacion='$id' AND id='$idArticulo'";
		$query = mysql_query($sql,$link);

		if ($query) { echo '<script>ventanaDescripcionArticulo.close();</script>'; }
		else{ echo '<script>alert("\u00A1Error!\nNo se actualizaron las cuentas!\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	// CALCULAR EL VALOR DE LA DEPRECIACION DE UN ACTIVO
	function calculaValorDepreciacion($opcGrillaContable,$id_activo,$accion,$cont,$id_empresa,$link){
		$sql = "SELECT
							metodo_depreciacion_colgaap,
							valor_salvamento,
							costo_sin_depreciar_anual,
							fecha_inicio_depreciacion,
							vida_util,
							costo,
							depreciacion_acumulada
						FROM
							activos_fijos
						WHERE
							activo = 1
						AND
							id_empresa = $id_empresa
						AND
							id = $id_activo";
		$query = mysql_query($sql,$link);

		$metodo_depreciacion_colgaap = mysql_result($query,0,'metodo_depreciacion_colgaap');
		$valor_salvamento            = mysql_result($query,0,'valor_salvamento');
		$costo_sin_depreciar_anual   = mysql_result($query,0,'costo_sin_depreciar_anual');
		$fecha_inicio_depreciacion   = mysql_result($query,0,'fecha_inicio_depreciacion');
		$vida_util                   = mysql_result($query,0,'vida_util');
		$costo                       = mysql_result($query,0,'costo');
		$depreciacion_acumulada      = mysql_result($query,0,'depreciacion_acumulada');
		$fecha                       = date("Y-m-d");

		$costo = $costo - $valor_salvamento;

		if($metodo_depreciacion_colgaap == 'linea_recta'){													// DEPRECIACION LINEA RECTA
			$depreciacionMes = round((($costo / $vida_util) / 12),$_SESSION['DECIMALESMONEDA']);
		}
		else if($metodo_depreciacion_colgaap == 'reduccion_saldos'){								// DEPRECIACION REDUCCION DE SALDOS
			$tasaDepreciacion = 1 - (POW(($valor_salvamento / $costo),(1 / $vida_util)));
			$depreciacionMes 	= round(($costo_sin_depreciar_anual * $tasaDepreciacion) / 12,$_SESSION['DECIMALESMONEDA']);
		}
		else if($metodo_depreciacion_colgaap == 'suma_digitos_year'){ 							// DEPRECIACION SUMA DE DIGITOS DEL AÑO
			$fecha1          = new DateTime($fecha." 24:00:00");
			$fecha2          = new DateTime($fecha_inicio_depreciacion." 24:00:00");
			$diferenciaFecha = $fecha1->diff($fecha2);

			if($mes == $mesDb){
				$diferenciaFecha->y = $diferenciaFecha->y - 1;
			}

			$sumaDigitos     = round(($vida_util * (($vida_util + 1) / 2)),$_SESSION['DECIMALESMONEDA']);
			$factor          = ($vida_util - $diferenciaFecha->y) / $sumaDigitos;
			$depreciacionMes = round(($costo * $factor) / 12,$_SESSION['DECIMALESMONEDA']);
		}

		$depreciacionMinima = $costo - $depreciacion_acumulada;

		if($depreciacionMinima < $depreciacionMes){
			$depreciacionMes = $depreciacionMinima;
		}

		if($accion == 'mostrar'){
			echo '<script>
							document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'").value="'.round($depreciacionMes,$_SESSION['DECIMALESMONEDA']).'";
						</script>';
		}
		else{
			return $depreciacionMes;
		}
	}

	// CAMBIAR LAMSINCRONIZACION DE LOS ASIENTOS CONTABLES, SI SE INSERTA EN COLGAAP Y NIIF O SOLO COLGAAP
	function cambiaSyncNota($tipo,$id,$opcGrillaContable,$id_empresa,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sql   = "UPDATE $tablaPrincipal SET sinc_nota='$tipo' WHERE activo=1 AND id_empresa='$id_empresa' AND id='$id'";
		$query = mysql_query($sql,$link);
 		if ($query) {

 			if ($tipo=='colgaap') {
 				echo '<script>
 						Ext.getCmp("GroupBtnSync").show();
						Ext.getCmp("GroupBtnNoSync").hide();
						sinc_nota_'.$opcGrillaContable.' = "'.$tipo.'";
					</script>';
 			}
 			else{
 				echo '<script>
 						Ext.getCmp("GroupBtnSync").hide();
						Ext.getCmp("GroupBtnNoSync").show();
						sinc_nota_'.$opcGrillaContable.' = "'.$tipo.'";
 					</script>';
 			}

 		}else{
 			echo '<script>
 					alert("Aviso!\nNo se genero el cambio en la nota!\nIntentelo de Nuevo, si el problema persiste comuniquese con el administrador del sistema");
 				</script>';
 		}
 	}

 	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$campoFecha,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT $campoFecha AS fecha FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);
		$fecha_documento = mysql_result($query,0,'fecha');

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
					if ( document.getElementById("modal") ) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}
	}

	// FUNCION PARA CRAGAR TODOS LOS ACTIVOS FIJOS DE UNA SUCURSAL AL CREAR EL DOCUMENTO PARA DEPRECIAR
	function cargarActivosFijosSucursal($id_documento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$link){
		// CONSULTAR LOS ACTIVOS QUE YA ESTAN GUARDADOS EN EL DOCUMENTO PARA NO CARGARLOS DE NUEVO
		$sql="SELECT id_activo_fijo	FROM $tablaInventario WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_depreciacion=$id_documento";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereIdActivos .= ($whereIdActivos=='')? 'id<>'.$row['id_activo_fijo'] : ' OR id<>'.$row['id_activo_fijo'] ;
		}

		$whereIdActivos = ($whereIdActivos<>'')? ' AND ('.$whereIdActivos.')' : '';

		$sql="SELECT id,
					metodo_depreciacion_colgaap,
					valor_salvamento,
					costo_sin_depreciar_anual,
					fecha_inicio_depreciacion,
					vida_util,
					costo,
					depreciacion_acumulada,
					depreciacion_acumulada_niif,
					deterioro_acumulado,
					vida_util_restante,
					vida_util_niif_restante
				FROM activos_fijos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND estado=1 $whereIdActivos";
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

			$valor=calculaValorDepreciacionActivosCargados($metodo_depreciacion_colgaap,$valor_salvamento,$costo_sin_depreciar_anual,$fecha_inicio_depreciacion,$vida_util,$costo,$depreciacion_acumulada,$deterioro_colgaap);

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

		echo '<script>
				Ext.get("contenedor_Depreciaciones").load({
				url     : "depreciaciones/grilla/grillaContable.php",
				scripts : true,
				nocache : true,
				params  :
					{
						id_depreciacion   : '.$id_documento.',
						opcGrillaContable : "Depreciaciones",
						filtro_sucursal   : "'.$id_sucursal.'",
					}
				});
			</script>';
	}

	// CALCULAR EL VALOR DE LA DEPRECIACION DE UN ACTIVO
	function calculaValorDepreciacionActivosCargados($metodo_depreciacion_colgaap,$valor_salvamento,$costo_sin_depreciar_anual,$fecha_inicio_depreciacion,$vida_util,$costo,$depreciacion_acumulada,$deterioro_colgaap){
		$fecha = date("Y-m-d");

		if($metodo_depreciacion_colgaap == 'linea_recta'){													// DEPRECIACION LINEA RECTA
			$depreciacionMes = ROUND((($costo / $vida_util) / 12),2);
		}
		else if($metodo_depreciacion_colgaap == 'reduccion_saldos'){								// DEPRECIACION REDUCCION DE SALDOS
			$tasaDepreciacion = 1 - (POW(($valor_salvamento / $costo),(1 / $vida_util)));

			$depreciacionMes = ROUND(($costo_sin_depreciar_anual * $tasaDepreciacion) / 12,2);
		}
		else if($metodo_depreciacion_colgaap == 'suma_digitos_year'){ 							// DEPRECIACION SUMA DE DIGITOS DEL AÑO
			$fecha1          = new DateTime($fecha." 24:00:00");
			$fecha2          = new DateTime($fecha_inicio_depreciacion." 24:00:00");
			$diferenciaFecha = $fecha1->diff($fecha2);

			if($mes == $mesDb){ $diferenciaFecha->y = $diferenciaFecha->y - 1; }

			$sumaDigitos     = ROUND(($vida_util * (($vida_util + 1) / 2)),2);
			$factor          = ($vida_util - $diferenciaFecha->y) / $sumaDigitos;
			$depreciacionMes = ROUND(($costo * $factor) / 12,2);
		}

		return $depreciacionMes;
	}
?>
