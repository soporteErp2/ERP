<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

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
			buscarArticulo($id,$idArticulo,$id_empresa,$opcGrillaContable,$codigo_activo,$deterioroAcumulado,$valorActual,$valorDeterioro,$mysql);
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

		case 'checkboxRetenciones':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			checkboxRetenciones($id,$idRetencion,$accion,$opcGrillaContable,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

		case 'actualizaFechaDocumento':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaFechaDocumento($id,$fecha,$tablaPrincipal,$opcGrillaContable,$link);
			break;

		case 'validaNota':
			validaNota($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$opcGrillaContable,$link);
			break;

		case 'terminarGenerar':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			terminarGenerar($id,$id_sucursal,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link);
			break;

		case 'guardarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($consecutivo,$id,$cont,$idInventario,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$deterioroAcumulado,$costoArticulo,$valorActual,$valorDeterioro,$id_empresa,$mysql);
			break;

		case 'actualizaArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($consecutivo,$id,$cont,$idInsertArticulo,$idInventario,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$deterioroAcumulado,$costoArticulo,$valorActual,$valorDeterioro,$id_empresa,$mysql);
			break;

		case 'guardarObservacion':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'verificaCantidadArticulo':
			verificaCantidadArticulo($opcGrillaContable,$id,$id_sucursal,$filtro_bodega,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$carpeta,$id_sucursal,$id_sucursal,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'agregarDocumento':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'eliminaDocReferencia':
			verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'sincronizarCuentaNiif':
			sincronizarCuentaNiif($id,$campoId,$campoText,$id_empresa,$link);
			break;

		case 'calculaValorDepreciacion':
			calculaValorDepreciacion($opcGrillaContable,$id_activo,$accion,$cont,$id_empresa,$link);
			break;

		case 'cargarActivosFijosSucursal':
			cargarActivosFijosSucursal($id_documento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$mysql);
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
		$readonly='';
		if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="images/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="campo"><input type="text" id="deterioroAcumulada'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly '.$deshabiltar.'value="'.$deprecicionAcumulada.'" /></div>
				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" readonly onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo"><input type="text" id="valorActual'.$opcGrillaContable.'_'.$cont.'" onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo"><input type="text" id="valorDeterioro'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="images/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/config16.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/delete.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" >

				<script>document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaConfiguracionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link){

		// CONSULTAR EL TIPO DE CONTABILIDAD DEL DOCUMENTO DEPRECIACION
		$sql="SELECT sinc_nota FROM activos_fijos_deterioro WHERE activo=1 AND id=$id";
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
	function buscarArticulo($id,$idArticulo,$id_empresa,$opcGrillaContable,$codigo_activo,$deterioroAcumulado,$valorActual,$valorDeterioro,$mysql){
		// CONSULTAR QUE EL ACTIVO NO ESTE YA AGREGADO EN LA PRESENTE NIOTA
		$sql   = "SELECT id_activo_fijo,nombre FROM activos_fijos_deterioro_inventario WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro=$id";
		$query = $mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$arrayActivos[$row['id_activo_fijo']]=$row['nombre'];
		}

		$sqlArticulo = "SELECT id,codigo_activo,nombre_equipo,unidad,costo,deterioro_acumulado,valor_salvamento
							FROM activos_fijos
						WHERE  activo = 1
							AND estado=1
							AND codigo_activo = '$codigo_activo'
						LIMIT 0,1";

		$query = $mysql->query($sqlArticulo,$mysql->link);

		$id_activo        = $mysql->result($query,0,'id');
		$codigo_activo         = $mysql->result($query,0,'codigo_activo');
		$nombre_equipo    = $mysql->result($query,0,'nombre_equipo');
		$unidad           = $mysql->result($query,0,'unidad');
		$costo            = $mysql->result($query,0,'costo');
		$deterioro_acumulado   = $mysql->result($query,0,'deterioro_acumulado');
		$valor_salvamento = $mysql->result($query,0,'valor_salvamento');

		if(array_key_exists($id_activo, $arrayActivos)){
			echo '<script>
					alert("El activo Fijo buscado ya esta agregado en el documento!");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
				</script>';
		}
		// else if(($costo-$valor_salvamento)<=$deterioro_acumulado){
		// 	echo '<script>
		// 			alert("El activo Fijo buscado ya no se puede depreciar mas!");
		// 			setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
		// 		</script>';
		// }
		else if($id_activo > 0){
			// $valorDepreciar=calculaValorDepreciacion($opcGrillaContable,$id,'',0,$id_empresa,$link);
			// 	//si la cantidad del articulo es mayor a cero en la bodega, se permite realizar la venta del articulo
				echo'<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value           = "'.$unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value         = "'.$id_activo.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value        = "'.$codigo_activo.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "'.$nombre_equipo.'";
						document.getElementById("deterioroAcumulada'.$opcGrillaContable.'_'.$idArticulo.'").value = "'.$deterioro_acumulado.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value      = "'.$costo.'";
						document.getElementById("valorDeterioro'.$opcGrillaContable.'_'.$idArticulo.'").value     = "0";

						setTimeout(function(){ document.getElementById("valorActual'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },50);
						'.$script.'
					</script>';

		}
		else{
			echo'<script>
					alert("El codigo '.$valorArticulo.' No se encuentra asignado en los activos fijos");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value         ="0";
					document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value           ="";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     ="";
					document.getElementById("deterioroAcumulada'.$opcGrillaContable.'_'.$idArticulo.'").value = "";
					document.getElementById("valorActual'.$opcGrillaContable.'_'.$idArticulo.'").value        = "";
					document.getElementById("valorDeterioro'.$opcGrillaContable.'_'.$idArticulo.'").value     = "";
				</script>';
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederArticulo($id,$idRegistro,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$sqlArticulo ="SELECT id_activo_fijo,codigo,nombre,unidad,costo,dias_depreciar
						FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal='$id' AND id='$idRegistro' LIMIT 0,1; ";

		$query = mysql_query($sqlArticulo,$link);

		$id_activo_fijo = mysql_result($query,0,'id_activo_fijo');
		$codigo         = mysql_result($query,0,'codigo');
		$nombre         = mysql_result($query,0,'nombre');
		$unidad         = mysql_result($query,0,'unidad');
		$costo          = mysql_result($query,0,'costo');
		$dias_depreciar = mysql_result($query,0,'dias_depreciar');

		// $id_inventario      = mysql_result($query,0,'id_inventario');
		// $codigo             = mysql_result($query,0,'codigo');
		// $costo              = mysql_result($query,0,'costo_unitario');
		// $nombre_unidad      = mysql_result($query,0,'nombre_unidad_medida');
		// $nombreArticulo     = mysql_result($query,0,'nombre');
		// $numeroPiezas       = mysql_result($query,0,'cantidad_unidad_medida');
		// $cantidad_articulo  = mysql_result($query,0,'cantidad');
		// $tipoDesc           = mysql_result($query,0,'tipo_descuento');
		// $descuento_articulo = mysql_result($query,0,'descuento');
		// $id_impuesto        = ($exento_iva=='Si')? 0 : mysql_result($query,0,'id_impuesto');

		// if ($tipoDesc=='porcentaje') {
		// 	$imgDescuento    = 'img/porcentaje.png';
		// 	$tituloDescuento = 'En porcentaje';
		// }
		// else{
		// 	$imgDescuento    = 'img/pesos.png';
		// 	$tituloDescuento = 'En pesos';
		// }

		echo'<script>
				document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value                 = "'.$unidad.'";
				document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value               = "'.$id_activo_fijo.'";
				document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value              = "'.$codigo.'";
				document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value            = "'.$costo.'";
				document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value           = "'.$nombre.'";
				document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.$dias_depreciar.'";
				document.getElementById("valorDeterioro'.$opcGrillaContable.'_'.$cont.'").value       = "";

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

	//=========================== FUNCION PARA AGREGAR O QUITAR RETENCIONES A LA FACTURA ========================================================//
	function checkboxRetenciones($id,$idRetencion,$accion,$opcGrillaContable,$tablaRetenciones,$idTablaPrincipal,$link){
		//cadena con el query para insertar o eliminar una retencion de la factura
		$sqlRetencion="";

		//cadena para consultar el valor de la retencion y agregar o restar el valor a la variable retefuenteCompra de la factura de compra
		$sqlValorRetencion      = "SELECT valor FROM retenciones WHERE id=".$idRetencion;
		$querySqlValorRetencion = mysql_query($sqlValorRetencion,$link);
		$arraySqlValorRetencion = mysql_fetch_array($querySqlValorRetencion);


		if ($accion=="eliminar"){ $sqlRetencion="DELETE FROM $tablaRetenciones WHERE $idTablaPrincipal=$id AND id_retencion=$idRetencion"; }
		else if ($accion=="insertar") { $sqlRetencion="INSERT INTO $tablaRetenciones ($idTablaPrincipal,id_retencion) VALUES ('$id','$idRetencion')"; }

		$queryRetencion = mysql_query($sqlRetencion,$link);
		if(!$queryRetencion){ echo'<script>alert("No se logro '.$accion.' la retencion");</script>'; }
	}

	//=========================== FUNCION PARA ACTUALIZAR LA FORMA DE PAGO ======================================================================//
	function actualizaFechaDocumento($id,$fecha,$tablaPrincipal,$opcGrillaContable,$link){

		$sql   = "UPDATE $tablaPrincipal SET fecha_inicio='$fecha' WHERE id='$id'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo '<script>alert("Error!\nNo se actualizo la fecha");</script>'; }
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA FACTURA, COTIZACION, PEDIDO Y CARGAR UNA NUEVA ==============================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_sucursal,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$link){
		global $id_empresa;
		// ACTUALIZAR ELESTADO DEL DOCUMENTO
		$sql="UPDATE activos_fijos_deterioro SET estado=1 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=mysql_query($sql,$link);
		if ($query) {
			// CONSULTAR EL CONSECUTIVO
			$sql="SELECT consecutivo,fecha_inicio,id_sucursal FROM activos_fijos_deterioro WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=mysql_query($sql,$link);
			$consecutivo =mysql_result($query,0,'consecutivo');
			$fecha       =mysql_result($query,0,'fecha_inicio');
			$id_sucursal =mysql_result($query,0,'id_sucursal');
			if ($consecutivo>0) {
				// MOVER LOS ASIENTOS CONTABLES DE LOS ACTIVOS PARA LA DEPRECIACION
				moverCuentasDocumento($id,$consecutivo,$fecha,'contabilizar',$id_sucursal,$id_empresa,$link);

				// ACTUALIZAR EL VALOR DE LA DEPRECIACION ACUMULADA DE LOS ACTIVOS DEL DOCUENTO
				actualizaDepreciacionAcumuladaActivos($id,'agregar',$id_empresa,$link);


				$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Depreciacion Activos Fijos',".$_SESSION['SUCURSAL'].",".$_SESSION['EMPRESA'].")";
				$queryLog = mysql_query($sqlLog,$link);
				echo '<script>
					 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "deterioro/bd/grillaContableBloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_sucursal   : "'.$id_sucursal.'",
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_deterioro   : "'.$id.'"
							}
						});
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			}
			else{
				$sql="UPDATE activos_fijos_deterioro SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
				$query=mysql_query($sql,$link);
				echo '<script>
						alert("No se genero consecutivo del documento\nIntentelo de nuevo");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			}
		}
	}

	//========================// VALIDA NOTA Y EJECUTA LA FUNCION TERMINAR //========================//
	//***********************************************************************************************//
	function validaNota($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$opcGrillaContable,$link){
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
		$query = mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
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

		$sqlNota   = "SELECT tercero,fecha_inicio FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$queryNota = mysql_query($sqlNota,$link);

		$tercero      = mysql_result($queryNota,0,'tercero');
		$fecha_inicio = mysql_result($queryNota,0,'fecha_inicio');

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
	function moverCuentasDocumento($id_documento,$consecutivo,$fecha,$accion,$id_sucursal,$id_empresa,$link){
		if ($accion=='contabilizar') {

			// CONSULTAR EL TERCERO DEL DOCUMENTO
			$sql="SELECT id_tercero FROM activos_fijos_deterioro WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id_documento";
			$query=mysql_query($sql,$link);
			$id_tercero = mysql_result($query,0,'id_tercero' );

			// CONSULTAR TODOS LOS ACTIVOS DEL DOCUMENTO CON LAS CUENTAS Y LOS VALORES DE DEPRECIACION
			echo$sql="SELECT cuenta_deterioro,
						contrapartida_deterioro,
						costo,
						valor,
						id_centro_costos
					FROM activos_fijos_deterioro_inventario WHERE activo=1 AND id_deterioro=$id_documento AND id_empresa=$id_empresa";
			$query=mysql_query($sql,$link);
			// ACUMULAR LAS CUENTAS CON SUS VALORES
			while ($row=mysql_fetch_array($query)){
				$cuenta_deterioro             = $row['cuenta_deterioro'];
				$contrapartida_deterioro      = $row['contrapartida_deterioro'];
				$id_centro_costos                = $row['id_centro_costos'];

				$whereCuentaColgaap .= ($whereCuentaColgaap=='')? " cuenta='$cuenta_deterioro' OR cuenta='$contrapartida_deterioro' " : " OR cuenta='$cuenta_deterioro' OR cuenta='$contrapartida_deterioro' " ;
				$costo = $row['costo'];
				$valor = $row['valor'];
				if (($costo-$valor)<=0 || $valor<=0) { continue; }
				$valor_deterioro = $costo-$valor;

				$arrayAsientosNiif[$id_centro_costos][$cuenta_deterioro]['debito']         += $valor_deterioro;
				$arrayAsientosNiif[$id_centro_costos][$contrapartida_deterioro]['credito'] += $valor_deterioro;
			}

			// CONSULTAR SI LAS CUENTAS TIENEN CENTROS DE COSTOS
			$sql = "SELECT cuenta,cuenta_niif,centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND ( $whereCuentaColgaap ) ";
			$query = mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$arrayCuentasCcosColgaap[$row['cuenta']]   = $row['centro_costo'];
			}

			// print_r($arrayAsientosNiif);

			// ASIENTOS COLGAAP
			foreach ($arrayAsientosNiif as $id_centro_costos => $arrayAsientosNiifCuentas) {
				foreach ($arrayAsientosNiifCuentas as $cuenta => $arrayAsientosNiifResul) {
					foreach ($arrayAsientosNiifResul as $caracter => $saldo) {
						$id_centro_costos_insert = '';
						if ($saldo<=0) { continue; }
						if ($caracter=='debito') {
							$debito  = $saldo;
							$credito = 0;
						}
						else{
							$debito  = 0;
							$credito = $saldo;
						}

						$acumSaldo += $saldo;

						if ($arrayCuentasCcosColgaap[$cuenta]=='Si'){
							if ($id_centro_costos==0 || $id_centro_costos=='') {
								echo '<script>
										alert("Error!\nLa cuenta '.$cuenta.' no tiene centro de costos, revise los activos fijos");
										document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
									</script>';
								exit;
							}
							$id_centro_costos_insert = $id_centro_costos;

						}

						$valueInsertAsientos .= "('$id_documento',
													'$consecutivo',
													'DT',
													'$id_documento',
													'$consecutivo',
													'DT',
													'Deterioro Activos Fijos',
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

			if ($acumSaldo<=0) {
				echo '<script>
						alert("Error!\nNo hay activos con deterioros a contabilizar!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				$sql   = "UPDATE activos_fijos_deterioro SET estado=0 WHERE id='$id_documento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND activo=1";
				$query = mysql_query($sql,$link);
				exit;
			}

			$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
			// INSERTAR LOS ASIENTOS
			$sql="INSERT INTO asientos_niif(
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
										id_empresa)
									VALUES $valueInsertAsientos";
			$query=mysql_query($sql,$link);

			if (!$query) {
				// DEVELVER EL VALOR ACUMULADOD DE LOS ACTIVOS
				actualizaDepreciacionAcumuladaActivos($id_documento,'eliminar',$id_empresa,$link);

				// ACTUALIZAR EL ESTADO DEL DOCUMENTO
				$sql="UPDATE activos_fijos_deterioro SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
				$query=mysql_query($sql,$link);
				echo '<script>
						alert("Error!\nNo se insertaron los asientos Niif\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

		}
		else if($accion=='descontabilizar'){
			// BORRAR ASIENTOS COLGAAP
			$sql="DELETE FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_documento=$id_documento AND tipo_documento='DT' ";
			$query=mysql_query($sql,$link);
			if (!$query) {
				echo '<script>
						alert("Error!\nNo se eliminaron los asientos colgaap\nIntentelo de nuevo");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

		}
	}

	function actualizaDepreciacionAcumuladaActivos($id_documento,$accion,$id_empresa,$link){
		if ($accion=='agregar') {
			echo$sql="UPDATE activos_fijos AS AF,
						 (
							SELECT
								AFD.*
							FROM
								activos_fijos_deterioro_inventario AS AFD
							WHERE
								AFD.id_deterioro =  $id_documento
							AND AFD.activo=1
							AND AFD.id_empresa = '$id_empresa'
						) AS AFD
						SET AF.deterioro_acumulado = AF.deterioro_acumulado + (IF((AFD.costo-AFD.valor)<0,0,AFD.costo-AFD.valor))
						WHERE
							AF.id = AFD.id_activo_fijo
						AND AF.activo=1
						AND AF.id_empresa = $id_empresa";
		}
		else if ($accion=='eliminar') {
			echo$sql="UPDATE activos_fijos AS AF,
						 (
							SELECT
								AFD.*
							FROM
								activos_fijos_deterioro_inventario AS AFD
							WHERE
								AFD.id_deterioro =  $id_documento
							AND AFD.activo=1
							AND AFD.id_empresa = '$id_empresa'
						) AS AFD
						SET AF.deterioro_acumulado = AF.deterioro_acumulado - (IF((AFD.costo-AFD.valor)<0,0,AFD.costo-AFD.valor))
						WHERE
							AF.id = AFD.id_activo_fijo
						AND AF.activo=1
						AND AF.id_empresa = $id_empresa";
		}

		$query=mysql_query($sql,$link);
		if (!$query) {
			// $sql="UPDATE activos_fijos_deterioro SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			// $query=mysql_query($sql,$link);
			echo '<script>
					alert("Error!\nNo se Actualizo el valor de la depreciacion acumulada de los activos");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link){
		// DEVELVER EL VALOR ACUMULADOD DE LOS ACTIVOS
		actualizaDepreciacionAcumuladaActivos($idDocumento,'eliminar',$id_empresa,$link);

		// DESCONTABILIZAR EL DOCUMENTO
		moverCuentasDocumento($idDocumento,0,0,'descontabilizar',$id_sucursal,$id_empresa,$link);

		//ACTUALIZAMOS LA FACTURA DE COMPRA A ESTADO 0 'SIN GUARDAR'
		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND activo=1";
		$query = mysql_query($sql,$link);

		if (!$query) {
			$sql   = "UPDATE $tablaPrincipal SET estado=1 WHERE id='$idDocumento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND activo=1";
			$query = mysql_query($sql,$link);
			echo '<script>
					alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog	  = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
					VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Depreciacion Activos Fijos','$id_sucursal','$id_empresa')";
		$queryLog = mysql_query($sqlLog,$link);

		echo'<script>
			 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
					url     : "deterioro/grilla/grillaContable.php",
					scripts : true,
					nocache : true,
					params  :
					{
						filtro_sucursal   : "'.$id_sucursal.'",
						opcGrillaContable : "'.$opcGrillaContable.'",
						id_deterioro   : "'.$idDocumento.'",
					}
				});
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
			</script>';
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($consecutivo,$id,$cont,$idInventario,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$deterioroAcumulado,$costoArticulo,$valorActual,$valorDeterioro,$id_empresa,$mysql){
		// CONSULTAR LA SUSUCURSAL DEL DOCUMENTO
		$sql="SELECT id_sucursal FROM activos_fijos_deterioro WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=$mysql->query($sql,$mysql->link);
		$id_sucursal = mysql_result($query,0,'id_sucursal');

		$sql = "INSERT INTO $tablaInventario(
						  	$idTablaPrincipal,
						  	id_activo_fijo,
							costo,
							valor,
							deterioro_acumulado,
							id_empresa,
							id_sucursal)
					VALUES( '$id',
							'$idInventario',
							'$costoArticulo',
							'$valorActual',
							'$deterioroAcumulado',
							'$id_empresa ',
							'$id_sucursal')";

		$query=$mysql->query($sql,$mysql->link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = $mysql->result($mysql->query($sqlLastId,$mysql->link),0,0);

		if($lastId > 0){

			echo'<script>
					document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value            = '.$lastId.'

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
					document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","images/reload.png");
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display        = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display    = "none";

					// document.getElementById("descripcionArticulo'.$opcGrillaContable.'_'.$cont.'").style.display = "block";
					document.getElementById("deleteArticulo'.$opcGrillaContable.'_'.$cont.'").style.display      = "block";

				</script>'.cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);

		}
		else{ echo $sqlInsert." Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema "; }
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaArticulo($consecutivo,$id,$cont,$idInsertArticulo,$idInventario,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$deterioroAcumulado,$costoArticulo,$valorActual,$valorDeterioro,$id_empresa,$mysql){
		$sql   = "SELECT costo,valor FROM $tablaInventario WHERE id='$idInsertArticulo' AND $idTablaPrincipal='$id' ";
		$sql   = $mysql->query($sql,$link);
		$costo = $mysql->result($sql,0,'costo');
		$valor = $mysql->result($sql,0,'valor');

		$sql   = "UPDATE $tablaInventario
					SET id_activo_fijo 		   = '$idInventario',
						costo                  = '$costoArticulo',
						valor                  = '$valorActual'
					WHERE $idTablaPrincipal=$id
						AND id=$idInsertArticulo";
		$query = $mysql->query($sql,$link);

		if ($query) {
			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					document.getElementById("valorActual'.$opcGrillaContable.'_'.$cont.'").blur();

					// CALCULAR EL VALOR DEL ACTIVO
					calculaValorTotalesDocumento("eliminar",'.($costo-$valor).');

					//CALCULAR EL VALOR DEL ACTIVO
					calculaValorTotalesDocumento("agregar",'.($costoArticulo-$valorActual).');
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
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


	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$id_empresa,$link){


		$sql   = "SELECT consecutivo,estado FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";
		$query = mysql_query($sql,$link);
		$estado      = mysql_result($query,0,'estado');
		$consecutivo = mysql_result($query,0,'consecutivo');

		$sql="UPDATE $tablaPrincipal SET estado=3 WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";

		if ($estado==3) {
			echo '<script>
					alert("El documento ya esta cancelado!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if ($estado==0 && $consecutivo=='') {
			$sql="UPDATE $tablaPrincipal SET activo=0 WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id";
		}
		else if ($estado==1) {
			// DEVELVER EL VALOR ACUMULADOD DE LOS ACTIVOS
			actualizaDepreciacionAcumuladaActivos($id,'eliminar',$id_empresa,$link);
			// DESCONTABILIZAR EL DOCUMENTO
			moverCuentasDocumento($id,0,0,'descontabilizar',$id_sucursal,$id_empresa,$link);
		}
		// echo $sql;
		$queryUpdate = mysql_query($sql,$link);				//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		if (!$queryUpdate) {
			echo '<script>
					alert("Error!\nNo se logro cancelar el documento");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else{
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Depreciacion Activos Fijos',$id_sucursal,".$_SESSION['EMPRESA'].")";
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
					nueva'.$opcGrillaContable.'();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// Ext.get("contenedor_'.$opcGrillaContable.'").load({
					// 	url     : "bd/grillaContableBloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_factura_venta  : "'.$id.'",
					// 		opcGrillaContable : "'.$opcGrillaContable.'",
					// 		filtro_bodega     : "'.$idBodega.'"
					// 	}
					// });
				</script>';
		}
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$id_sucursal,$id_empresa,$tablaPrincipal,$link){

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
							VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Depreciacion Activos Fijos',$id_sucursal,".$_SESSION['EMPRESA'].")";
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "deterioro/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_deterioro   : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_sucursal       : "'.$id_sucursal.'"
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';

		}
		else{
			echo '<script>
					alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				return;
		}
 	}

 	//============================== FUNCION PARA AGREGAR UN DOCUMENTO ==========================================================================//
 	function agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

		$sqlDocumento       = "SELECT id_cliente, estado FROM $tablaPrincipal WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$queryDocumento     = mysql_query($sqlDocumento,$link);

		$idClienteDocumento= mysql_result($queryDocumento,0,'id_cliente');
		$estadoDocumento    = mysql_result($queryDocumento,0,'estado');

		if($estadoDocumento == 1){ echo '<script>alert("Error!,\nEl documento actual ha sido generada.");</script>'; return; }
		if($estadoDocumento == 3){ echo '<script>alert("Error!,\nEl documento actual ha sido cancelada.");</script>'; return; }
		else if($idClienteDocumento== '' || $idClienteDocumento== 0){ echo '<script>alert("Aviso!,\nSeleccione un cliente para la factura.");</script>'; return; }

		switch ($typeDoc) {
			case 'cotizacion':
				$campoCantidad          = "cantidad";
				$title                  = 'Eliminar los Articulos de la Cotizacion';
				$referencia_input       = "C";
				$referencia_consecutivo = "Cotizacion";
				$tablaCarga             = "ventas_cotizaciones";
				$idTablaCargar          = "id_cotizacion_venta";
				$tablaCargaInventario   = "ventas_cotizaciones_inventario";
				break;

			case 'pedido':
				$campoCantidad          = "saldo_cantidad";
				$title                  = 'Eliminar los Articulos del Pedido';
				$referencia_input       = "P";
				$referencia_consecutivo = "Pedido";
				$tablaCarga             = "ventas_pedidos";
				$idTablaCargar          = "id_pedido_venta";
				$tablaCargaInventario   = "ventas_pedidos_inventario";
				break;
		}

		$whereDocumentoCarga = ($typeDoc == 'pedido')? "AND CO.unidades_pendientes > 0": "";

		//VALIDACION ESTADO DE LA FACTURA
		$idClienteDocAgregar    = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT id_cliente,estado,id FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa'";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idClienteDocAgregar = mysql_result($queryValidateDocumento,0,'id_cliente');
		$idDocumentoAgregar  = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar    = mysql_result($queryValidateDocumento,0,'estado');

		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		else if($idClienteDocAgregar <> $idClienteDocumento){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenese a un cliente diferente.");</script>'; return; }

		//VALIDACION QUE EL DOCUMENTO NO HAYA SIDO INGRESADO
		$sqlValidateRepetido = "SELECT COUNT(id) AS contDocRepetido
								FROM $tablaInventario
								WHERE activo=1  AND id_consecutivo_referencia='$idDocumentoAgregar'
									AND nombre_consecutivo_referencia='$referencia_consecutivo'
									AND $idTablaPrincipal='$id_factura'
								GROUP BY id_tabla_inventario_referencia LIMIT 0,1";
		$docRepetido = mysql_result(mysql_query($sqlValidateRepetido,$link),0,'contDocRepetido');
		if($docRepetido > 0){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' ya ha sido agregado en el presente documento");</script>'; return; }

		//GENERA CICLO PARA INSERTAR ARTICULOS DEL DOCUMENTO REFERENCIA A TABLA INVENTARIOS FACTURAS
		$sqlConsultaInventario= "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.$campoCantidad AS cantidad,COI.costo_unitario,
                                        COI.tipo_descuento,COI.descuento,
                                        COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                        CO.id AS id_documento,CO.consecutivo AS consecutivo_documento
                                FROM $tablaCargaInventario AS COI
                                INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                WHERE CO.consecutivo     ='$codDocAgregar'
                                    AND COI.activo       = 1
                                    AND CO.id_sucursal   ='$id_sucursal'
                                    AND CO.id_bodega     ='$filtro_bodega'
                                    AND CO.id_empresa    ='$id_empresa'
                                    $whereDocumentoCarga";
        $queryConsultaInventario=mysql_query($sqlConsultaInventario,$link);

        $contInsert=0;
        while ($row = mysql_fetch_array($queryConsultaInventario)) {
        	$contInsert++;
        	$idDocCruce = $row['id_documento'];
            $sqlInsertArticulos="INSERT INTO $tablaInventario
                                            ($idTablaPrincipal,
                                            id_inventario,
                                            cantidad,
                                            costo_unitario,
                                            tipo_descuento,
                                            descuento,
                                            observaciones,
                                            id_tabla_inventario_referencia,
                                            id_consecutivo_referencia,
                                            consecutivo_referencia,
                                            nombre_consecutivo_referencia)
                                VALUES ('$id_factura',
                                        '".$row['id_inventario']."',
                                        '".$row['cantidad']."',
                                        '".$row['costo_unitario']."',
                                        '".$row['tipo_descuento']."',
                                        '".$row['descuento']."',
                                        '".$row['observaciones']."',
                                        '".$row['id']."',
                                        '".$row['id_documento']."',
                                        '".$row['consecutivo_documento']."',
                                        '$referencia_consecutivo')";
            $queryInsertArticulos=mysql_query($sqlInsertArticulos,$link);
        }

        if($contInsert > 0){

    		$newDocReferencia  ='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;height: 22px;" id="divDocReferencia'.$opcGrillaContable.'_'.$referencia_input.'_'.$idDocumentoAgregar.'">'
							       .'<div class="contenedorInputDocReferenciaFactura">'
							           .'<input type="text" class="inputDocReferenciaFactura" value="'.$referencia_input.' '.$codDocAgregar.'" style="border-bottom: 1px solid #d4d4d4;" readonly/>'
							       .'</div>'
							       .'<div title="'.$title.' # '.$codDocAgregar.' en la presente factura" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\\\''.$idDocumentoAgregar.'\\\',\\\''.$referencia_input.'\\\',\\\''.$id_factura.'\\\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">'
							           .'<div style="overflow:hidden; border-radius:35px; color:#fff; height:16px; width:16px; margin:1px;" id="btn'.$opcGrillaContable.'_'.$referencia_input.'_'.$idDocCruce.'">'
	                                        .'<div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>'
	                                    .'</div>'
							       .'</div>'
							    .'</div>';

			echo'<script>
					divDocsReferenciaFactura = document.getElementById("contenedorDocsReferencia'.$opcGrillaContable.'").innerHTML;
					document.getElementById("contenedorDocsReferencia'.$opcGrillaContable.'").innerHTML =divDocsReferenciaFactura+\''.$newDocReferencia.'\';
	    			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").value="";

	    			Ext.get("renderizaNewArticulo'.$opcGrillaContable.'").load({
			            url     : "bd/bd.php",
			            scripts : true,
			            nocache : true,
			            params  :
			            {
							opc               : "reloadBodyAgregarDocumento",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_factura        : "'.$id_factura.'",
			            }
			        });
        		</script>';
        }
        else{
        	echo'<script>
        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
        			alert("Numero invalido!\nDocumento no terminado o ya asignado");
        			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();}, 100);
        		</script>';
		}
	}

	function reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		include("functions_body_article.php");
		echo cargaArticulosSave($id_factura,'',0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
	}

	function eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		include("functions_body_article.php");
		$campoDocReferencia = '';
		if($docReferencia=='P'){ $campoDocReferencia = 'Pedido'; }
		else if($docReferencia=='C'){ $campoDocReferencia = 'Cotizacion'; }
		// else if($docReferencia=='R'){ $campoDocReferencia = 'Remision'; }

		$sql   ="DELETE FROM $tablaInventario WHERE $idTablaPrincipal=$id_factura AND id_consecutivo_referencia=$id_doc_referencia  AND nombre_consecutivo_referencia='$campoDocReferencia'";
		$query = mysql_query($sql,$link);

		echo cargaArticulosSave($id_factura,'',0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

		if($query){
			echo'<script>
					document.getElementById("divDocReferencia'.$opcGrillaContable.'_'.$docReferencia.'_'.$id_doc_referencia.'").parentNode.removeChild(document.getElementById("divDocReferencia'.$opcGrillaContable.'_'.$docReferencia.'_'.$id_doc_referencia.'"));
				</script>';
		}
		else{ echo'<script>alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
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
			$mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el';
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
	function cargarActivosFijosSucursal($id_documento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$mysql){

		// CONSULTAR LOS ACTIVOS QUE YA ESTAN GUARDADOS EN EL DOCUMENTO PARA NO CARGARLOS DE NUEVO
		$sql="SELECT id_activo_fijo	FROM $tablaInventario WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_deterioro=$id_documento";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$whereIdActivos .= ' AND id<>'.$row['id_activo_fijo'] ;
		}

		// $whereIdActivos = ($whereIdActivos<>'')? ' AND ('.$whereIdActivos.')' : '';

		$sql="SELECT
					id,
					costo,
					deterioro_acumulado
				FROM activos_fijos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND estado=1 $whereIdActivos";
		$query=$mysql->query($sql,$link);
		while ($row=$mysql->fetch_array($query)) {
			$id                  = $row['id'];
			$costo               = $row['costo'];
			$deterioro_acumulado = $row['deterioro_acumulado'];

			$valueInsert .= "('$id_documento',
								'$id',
								'$costo',
								'0',
								'$deterioro_acumulado',
								'$id_empresa ',
								'$id_sucursal'),";
		}

		$valueInsert = substr($valueInsert, 0, -1);
		$sql = "INSERT INTO $tablaInventario(
						  	$idTablaPrincipal,
						  	id_activo_fijo,
							costo,
							valor,
							deterioro_acumulado,
							id_empresa,
							id_sucursal)
					VALUES $valueInsert";

		$query= $mysql->query($sql,$mysql->link);

		echo '<script>
				Ext.get("contenedor_Deterioro").load({
				url     : "deterioro/grilla/grillaContable.php",
				scripts : true,
				nocache : true,
				params  :
					{
						id_deterioro      : '.$id_documento.',
						opcGrillaContable : "Deterioro",
						filtro_sucursal   : "'.$id_sucursal.'",
					}
				});
			</script>';

	}

?>