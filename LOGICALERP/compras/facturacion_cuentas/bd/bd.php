<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if ($opc<>'guardarFechaFactura') {
			verificaCierre($id,$tablaPrincipal,$id_empresa,$link);
		}

		if ($opc<>'cancelarDocumento' && $opc<>'restaurarDocumento' && $opc<>'modificarDocumentoGenerado' && $opc<>'eliminarArticuloRelacionado') {
			verificaEstadoDocumento($id,$link);
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

		case 'buscarTerceroCuenta':
			buscarTerceroCuenta($nit,$contFila,$id_empresa,$opcGrillaContable,$mysql);
			break;

		case 'buscarCuenta':
			buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$id_sucursal,$idCliente,$opcGrillaContable,$link);
			break;

		case 'deleteCuenta':
			deleteCuenta($cont,$id,$idCuenta,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'retrocederCuenta':
		 	retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$nitProveedor,$prefijo_factura,$numero_factura,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'guardarCuenta':
			guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$idTablaReferencia,$link);
			break;

		case 'actualizaCuenta':
			actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$idTablaReferencia,$link);
			break;

		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'actualizarTipoNota':
			actualizarTipoNota($id,$idTipoNota,$notaCruce,$tablaPrincipal,$id_empresa,$link);
			break;

		case 'guardarFechaFactura':
			guardarFechaFactura($idInputDate,$idFactura,$valInputDate,$link);
			break;

		case 'eliminarArticuloRelacionado':
			eliminarArticuloRelacionado($id,$tipo,$accion,$link);
			break;

		case 'ventana_buscar_documento_cruce':
			ventana_buscar_documento_cruce($cont,$opcGrillaContable,$carpeta,$id_empresa,$id_sucursal,$link);
			break;

		case 'cargaConfiguracionCuenta':
			cargaConfiguracionCuenta($idInsertCuenta,$idCuenta,$id_empresa,$opcGrillaContable,$cont,$link);
			break;

		case 'actualizarNiif':
			actualizarNiif($id_niif,$cuenta,$idInsertCuenta,$idCuenta,$opcGrillaContable,$cont,$id_empresa,$link);
			break;

		case 'cargaHeadInsertUnidadesConTercero':
			cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable);
			break;

		case 'actualizarCcos':
			actualizarCcos($idInsertCuenta,$idCuenta,$opcGrillaContable,$nombre_centro_costos,$codigo_centro_costos,$id_centro_costos,$id_empresa,$link);
			break;

		case 'validarCuenta':
			validarCuenta($cuenta,$id_empresa,$link);
			break;

		case 'eliminarCentroCostos':
			eliminarCentroCostos($cont,$idInsertCuenta,$id_empresa,$link);
			break;

		case 'validar_centro_costo':
			validar_centro_costo($cont,$id_ccos,$codigo_centro_costos,$centro_costos,$id_empresa,$link);
			break;

		case 'ventanaVerImagenDocumentoTerceros':
			ventanaVerImagenDocumentoTerceros($nombreImage,$nombreDocumento,$type,$id_host);
			break;

		case 'consultaSizeImageDocumentTerceros':
			consultaSizeImageDocumentTerceros($id_host,$nombre);
			break;

		case 'eliminarArchivoAdjunto':
			eliminarArchivoAdjunto($id,$nombre,$id_host,$mysql);
			break;

		case 'guardarObservacionCuenta':
			guardarObservacionCuenta($id_documento,$id,$observacion,$id_empresa,$mysql);
			break;

		case 'validarNumeroFactura':
			validarNumeroFactura($prefijoFactura,$numeroFactura,$idFactura,$nitProveedor,$id_empresa,$link);
			break;
	}

	//=========================== FUNCION PARA BUSCAR UN CLIENTE ===============================================================================//
	function buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link){

		if ($inputId=='nitCliente'.$opcGrillaContable) {
			$where   ='numero_identificacion="'.$codCliente.'"  ';
			$mensaje = 'alert("Numero de identificaion de tercero no establecido");';
		}
		else if ($inputId=='codigoTercero'.$opcGrillaContable) {
			$where   = 'codigo= "'.$codCliente.'"';
			$mensaje = 'alert("codigo de tercero no establecido");';
		}

		$sqlTercero   = "SELECT id, numero_identificacion, tipo_identificacion, codigo, nombre, COUNT(id) AS contTercero FROM terceros
						WHERE $where  AND activo=1 AND tercero = 1 AND tipo_proveedor='Si' AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryTercero = mysql_query($sqlTercero,$link);

		$contTercero = mysql_result($queryTercero,0,'contTercero');
		$idTercero   = mysql_result($queryTercero,0,'id');
		$nit         = mysql_result($queryTercero,0,'numero_identificacion');
		$tipoNit     = mysql_result($queryTercero,0,'tipo_identificacion');
		$codigo      = mysql_result($queryTercero,0,'codigo');
		$nombre      = mysql_result($queryTercero,0,'nombre');

		//GENERAMOS LA VARIABLE PARA HACER EL UPDATE DE LA TABLA PRINCIPAL
		if ($contTercero == 0) {
			// $sqlDocumento   = "SELECT codigo_tercero, numero_identificacion_tercero, tipo_identificacion_tercero FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			// $queryDocumento = mysql_query($sqlDocumento,$link);

			// $codigoTercero  = mysql_result($queryDocumento,0,'codigo_tercero');
			// $nitTercero     = mysql_result($queryDocumento,0,'numero_identificacion_tercero');
			// $tipoNitTercero = mysql_result($queryDocumento,0,'tipo_identificacion_tercero');

			echo'<script>
					'.$mensaje.'
					document.getElementById("nitCliente'.$opcGrillaContable.'").focus();
					document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "";
					document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "";
					document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "";
				</script>';
			exit;
		}
		else if ($inputId=='nitCliente'.$opcGrillaContable) {
			$camposInsert = "codigo_tercero = '$codigo ',
							numero_identificacion_tercero = '$codCliente',
							tipo_identificacion_tercero = '$tipoDocumento'";
		}
		else if ($inputId=='codigoTercero'.$opcGrillaContable) {
			$camposInsert = "codigo_tercero = '$codigo ',
							numero_identificacion_tercero = '$nit',
							tipo_identificacion_tercero = '$tipoNit'";
		}

		//$sqlUpdate = " UPDATE $tablaPrincipal
		//				SET id_tercero = '$idTercero',
		//					tercero = '$nombre',
		//					$camposInsert
		//				WHERE id='$id'";
		//$queryUpdate = mysql_query($sqlUpdate,$link);

		$sqlUpdateComprasFacturas = "UPDATE compras_facturas
										SET id_empresa = '$id_empresa',
											id_proveedor = '$idTercero'
										WHERE id='$id'";
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);

		echo'<script>
				document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigo.'";
				document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
				document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombre.'";

				id_cliente_'.$opcGrillaContable.'   = "'.$idTercero.'";
				nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
				nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";

				// id_tipo_nota = document.getElementById("selectTipoNota").value;
				// if(arrayTipoNota[id_tipo_nota] == "Si"){ document.getElementById("contenedorNotaContable").setAttribute("class","contenedorNotaContableCruce"); }
				// else{ document.getElementById("contenedorNotaContable").setAttribute("class","contenedorNotaContable"); }
			</script>';
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos">
							<div class="labelNotaGeneral" style="width:40px !important;"></div>
							<div class="labelNotaGeneral" style="width:95px;">Cuenta</div>
							<div class="labelNotaGeneral ">Descripcion</div>
							<div class="labelNotaGeneral">Nit</div>
							<div class="labelNotaGeneral ">Tercero</div>
							<div class="labelNotaGeneral " >Doc. Cruce</div>
							<div class="labelNotaGeneral " >N.Doc.Cruce</div>
							<div class="labelNotaGeneral" >Debito</div>
							<div class="labelNotaGeneral" >Credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsInsertUnidadesConTercero('return',$cont,$opcGrillaContable).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacionFacturaCuentas"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal" id="debitoAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="creditoAcumulado'.$opcGrillaContable.'">0</div>
						</div>

						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL CUENTA POR PAGAR</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("debitoAcumulado'.$opcGrillaContable.'").innerHTML  = parseFloat(debitoAcumulado'.$opcGrillaContable.').toFixed(2);
					document.getElementById("creditoAcumulado'.$opcGrillaContable.'").innerHTML = parseFloat(creditoAcumulado'.$opcGrillaContable.').toFixed(2);
					document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML   = parseFloat(total'.$opcGrillaContable.').toFixed(2);
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;" id="label_cont_'.$opcGrillaContable.'_'.$cont.'">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campoNotaGeneral "><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoNotaGeneral" ><input type="text" id="nit'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" onkeyup="buscarTerceroCuenta'.$opcGrillaContable.'(event,this);" /></div>
				<div class="campoNotaGeneral "><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="iconBuscarArticulo" >
					<img src="img/buscar20.png" id="imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" onclick="buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')" title="Buscar Tercero" />
				</div>

				<div class="campoNotaGeneral ">
				 	<!--<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly style="text-align:left;" >-->
				 	<select id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" style="width:calc(100% - 20px);border:none;" onChange="cambiaDocumentoCruce(this.value,'.$cont.',\'\')">
				 		<option value="" title="">Seleccione</option>
				 		<option value="CE" title="Comprobante de Egreso">CE</option>
				 		<option value="FC" title="Factura de Compra">FC</option>
				 	</select>
				 </div>
				 <div class="iconBuscarArticulo" id="iconBuscarArticulo_'.$cont.'">
					<img id="imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" title="Buscar Documento Cruce" src="img/buscar20.png" />
				</div>

				<div class="campoNotaGeneral ">
					<input title="Prefijo" type="text" readonly id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputPrefijoNotaContableCruce" onKeyup="this.value=this.value.toUpperCase();"   />-
					<input title="Numero" type="text" readonly id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');"  />
				</div>

				<div class="campoNotaGeneral" ><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"  /></div>
				<div class="campoNotaGeneral" ><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/></div>

				<div style="float:left; min-width:80px;margin-left:5px;">
					<div onclick="guardarNewCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="deleteCuenta'.$opcGrillaContable.'('.$cont.')" id="deleteCuenta'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
					<div onclick="cambiaCuentaNiif'.$opcGrillaContable.'('.$cont.')" id="configurarCuenta'.$opcGrillaContable.'_'.$cont.'" title="Configurar Cuenta Niif" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/config16.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idTercero'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idTablaReferencia'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<script>
					cambiaDocumentoCruce("",'.$cont.',"false");
					let tipoDocumento = document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value;
        			document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick","ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.',tipoDocumento)");
					// console.log("in");
					document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>
				';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	// ===================== BUSCAR EL TERCERO PARA LAS CEUNTAS =========================== //
	function buscarTerceroCuenta($nit,$contFila,$id_empresa,$opcGrillaContable,$mysql){
		$sql="SELECT id,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND numero_identificacion='$nit'";
		$query=$mysql->query($sql,$mysql->link);
		$id_tercero     = $mysql->result($query,0,'id');
		$nombre_tercero = $mysql->result($query,0,'nombre');
		if ($id_tercero=='') {
			echo "<script>
					alert('No Existe un tercero con ese numero de identificacion');
					document.getElementById('nit".$opcGrillaContable."_".$contFila."').focus();
					document.getElementById('nit".$opcGrillaContable."_".$contFila."').value='';
				</script>";
			return;
		}
		echo "<script>
				document.getElementById('idTercero".$opcGrillaContable."_".$contFila."').value=$id_tercero;
				document.getElementById('tercero".$opcGrillaContable."_".$contFila."').value='$nombre_tercero';
				document.getElementById('debito".$opcGrillaContable."_".$contFila."').focus();
			</script>";

	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
	function buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$id_sucursal,$idCliente,$opcGrillaContable,$link){
		/* VALIDACION:
			- SI SE INSERTO UN ASIENTO CON UNA CUENTA CON MENOR CANTIDAD DE DIGITOS A LA QUE SE VA A BUSCAR,
			ES DECIR, SI BUSCAMOS LA CUENTA 110505 (UN NIVEL INFERIOR) PERO YA SE INSERTARON DATOS EN LA 110505 EN ADELANTE
			NO PODREMOS USAR ESA CUENTA, Y SI BUSCAMOS LA CUENTA 11050501 Y SE INSERTO UN ASIENTO EN LA CUENTA
			110505 (UN NIVEL SUPERIOR), TAMPOCO SE PODRA UTILIZAR LA CUENTA DE 8 DIGITOS
		*/

		//IDENTIFICAMOS LA LONGITUD DE LA CUENTA
		if (strlen ($cuenta)<5) {
			echo'<script>
					alert("Ingrese una cuenta de minimo 6 digitos!");
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
				</script>';
			exit;
		}
		else if (strlen ($cuenta)>5 AND strlen ($cuenta)<7) {
			$where   = ' LEFT(codigo_cuenta,6) = \''.$cuenta.'\'';
			$mensaje = 'Existe(n) una(s) cuenta(s) de esta, con 8 digitos, no se puede utilizar esta de 6\nDigite apartir de 8 digitos para continuar';
		}
		else if (strlen ($cuenta)>7 && strlen ($cuenta)<9) {
			$where   = ' codigo_cuenta =  LEFT(\''.$cuenta.'\',6) ';
			$mensaje = 'Existe(n) una(s) cuenta(s) de esta, con 6 digitos, no se puede utilizar esta de 8\nDigite solo de 6 digitos para continuar';
		}
		else {
			$where   = '(LENGTH(cuenta)>5)';
			$mensaje = 'Ditige 6 o 8 digitos!';
		}

		//VALIDATE SUCURSAL CUENTA
		$sqlValidateSucursal   = "SELECT COUNT(id) AS cont_sucursal, id_sucursal FROM puc WHERE cuenta='$cuenta' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryValidateSucursal = mysql_query($sqlValidateSucursal,$link);

		$contSucursal = mysql_result($queryValidateSucursal,0,'cont_sucursal');
		$idSucursalDb = mysql_result($queryValidateSucursal,0,'id_sucursal');

		if($contSucursal == 0 || $idSucursalDb != 0 && $idSucursalDb != $id_sucursal){
			$textAlert = ($contSucursal == 0)? "no se encuentra asignado en el PUC de la empresa": "no esta disponible en la presente sucursal!";
			echo'<script>
					alert("El Numero de cuenta '.$cuenta.' '.$textAlert.'");
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
					document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value    = "0";
					document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").value      = "";
					document.getElementById("descripcion'.$opcGrillaContable.'_'.$contFila.'").value = "";
				</script>';
				exit;
		}

		//====================// VALIDACION CUENTAS PADRE //====================//
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM puc WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%' AND cuenta<>'$cuenta'";
		$queryCuenta = mysql_query($sqlCuenta, $link);
		$contCuenta  = mysql_result($queryCuenta, 0, 'contCuenta');

		if($contCuenta > 0){
			echo'<script>
					alert("Aviso.\nEl numero de cuenta '.$cuenta.' pertenece a una cuenta padre!");
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
					document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value = "0";
					document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").value   = "";
				</script>';
				exit;
		}

		//BUSCAR SI LA CUENTA YA FUE INSERTADA EN LOS ASIENTOS, Y SI ES ASI, SE CONTINUA NORMALMENTE
		$sqlCuentaAsientos  = "SELECT id_cuenta,cuenta FROM asientos_colgaap WHERE codigo_cuenta = '$cuenta' AND id_empresa='$id_empresa'";
		$queryCuentaAsiento = mysql_query($sqlCuentaAsientos,$link);

		$id_cuenta          = mysql_result($queryCuentaAsiento,0, 'id_cuenta');
		$descripcion_cuenta = mysql_result($queryCuentaAsiento,0, 'cuenta');

		if ($descripcion_cuenta!='') {
			echo'<script>
					arrayCuentaPago['.$contFila.']='.$cuenta.';

					document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value    = "'.$id_cuenta.'";
					document.getElementById("descripcion'.$opcGrillaContable.'_'.$contFila.'").value = "'.$descripcion_cuenta.'";

					document.getElementById("descripcion'.$opcGrillaContable.'_'.$contFila.'").blur();
					setTimeout(function(){ document.getElementById("nit'.$opcGrillaContable.'_'.$contFila.'").focus(); }, 100);
			  	</script>';
		}
		//SI LA CUENTA NO EXISTE ENTONCES SE VA A VALIDAR ANTES DE CONTINUAR
		else {

			//CONSULTAMOS EN LOS ASIENTOS PARA HACER LA VALIDACION, SI ES DE 6 QUE NO HALLAN DE 8 Y VICEVERSA
			$sqlValidaAsiento   = "SELECT COUNT(id) AS cont FROM asientos_colgaap WHERE $where AND id_empresa=$id_empresa";
			$queryValidaAsiento = mysql_query($sqlValidaAsiento,$link);
			$cont = mysql_result($queryValidaAsiento,0,'cont');

			if ($cont>=1) {
				echo '<script>
						alert("Error!\n'.$mensaje.'");
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
					</script>';
				exit;
			}

			$sqlArticulo = "SELECT id,descripcion FROM puc WHERE cuenta='$cuenta' AND (LENGTH(cuenta)>5) AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$query       = mysql_query($sqlArticulo,$link);
			$id          = mysql_result($query,0,'id');
			$descripcion = mysql_result($query,0,'descripcion');

			if ($descripcion!='') {
				echo'<script>
						arrayCuentaPago['.$contFila.']='.$cuenta.';

						document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value    = "'.$id.'";
						document.getElementById("descripcion'.$opcGrillaContable.'_'.$contFila.'").value = "'.$descripcion.'";

						document.getElementById("descripcion'.$opcGrillaContable.'_'.$contFila.'").blur();
						setTimeout(function(){ document.getElementById("nit'.$opcGrillaContable.'_'.$contFila.'").focus();},100);
					</script>';
			}
			else{
				echo'<script>
						alert("El Numero de cuenta '.$cuenta.' no se encuentra asignado en el PUC de la empresa");
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
						document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value    ="0";
						document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").value      ="";
						document.getElementById("descripcion'.$opcGrillaContable.'_'.$contFila.'").value ="";
					</script>';
			}
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sql   = "SELECT id_puc,cuenta_puc,descripcion_puc,debe,haber,id_documento_cruce,numero_documento_cruce,tercero,tipo_documento_cruce,prefijo_documento_cruce FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuentaInsert' ";
		$query = mysql_query($sql,$link);

		$idCuenta                = mysql_result($query,0,'id_puc');
		$cuenta                  = mysql_result($query,0,'cuenta_puc');
		$descripcion             = mysql_result($query,0,'descripcion_puc');
		$debe                    = mysql_result($query,0,'debe');
		$haber                   = mysql_result($query,0,'haber');
		$tercero                 = mysql_result($query,0,'tercero');
		$id_documento_cruce      = mysql_result($query,0,'id_documento_cruce');
		$tipo_documento_cruce    = mysql_result($query,0,'tipo_documento_cruce');
		$prefijo_documento_cruce = mysql_result($query,0,'prefijo_documento_cruce');
		$numero_documento_cruce  = (mysql_result($query,0,'numero_documento_cruce')==0)? '' : mysql_result($query,0,'numero_documento_cruce') ;


		echo'<script>';

		echo 	($tercero != "")?
					'document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/eliminar.png");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Eliminar Tercero");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick"," eliminaTercero'.$opcGrillaContable.'('.$cont.')");'

				:	'document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Buscar Tercero");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick","buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')");';

		echo 	($numero_documento_cruce != "")?
					'document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/eliminar.png");
        			document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Eliminar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick"," eliminaDocumentoCruce'.$opcGrillaContable.'('.$cont.')");'
				:
					'document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Buscar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick","ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.')");';

		echo'	document.getElementById("idCuenta'.$opcGrillaContable.'_'.$cont.'").value                 = "'.$idCuenta.'";
				document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$cuenta.'";
				document.getElementById("descripcion'.$opcGrillaContable.'_'.$cont.'").value              = "'.$descripcion.'";
				document.getElementById("tercero'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$tercero.'";
				document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value           = "'.$tipo_documento_cruce.'";
				document.getElementById("prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value    = "'.$prefijo_documento_cruce.'";
				document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value     = "'.$numero_documento_cruce.'";
				document.getElementById("idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value      	  = "'.$id_documento_cruce.'";
				document.getElementById("debito'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$debe.'";
				document.getElementById("credito'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$haber.'";
				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

				cambiaDocumentoCruce("'.$tipo_documento_cruce.'",'.$cont.',"false");

			</script>';
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteCuenta($cont,$id,$idCuenta,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sqlDelete   = "DELETE FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuenta'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar la cuenta, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo'<script>
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
				</script>';
		}
	}

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA NOTA ==========================================================================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$nitProveedor,$prefijo_factura,$numero_factura,$link){
		if(validarNumeroFactura($prefijo_factura,$numero_factura,$id,$nitProveedor,$id_empresa,$link)){
			echo '<script>
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}

		// VALIDAR QUE LA FACTURA TENGA UN TERCERO
		$sql   = "SELECT proveedor,id_proveedor FROM $tablaPrincipal WHERE activo=1 AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND id='$id'";
		$query = mysql_query($sql,$link);

		if (mysql_result($query,0,'proveedor')=='') {
			echo'<script>
					alert("Aviso\nDebe seleccionar el proveedor!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
		$id_tercero = mysql_result($query,0,'id_proveedor');

		// VALIDAR QUE LAS CUENTAS QUE TENGAN CENTRO DE COSTOS TENGAN SELECCIONADO UN CENTRO DE COSTOS, SINO DETENER EL PROCESO
		$sql="SELECT id_puc,id_centro_costos,codigo_centro_costos FROM compras_facturas_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$id";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$arrayCuentas[$row['id_puc']][]=$row['id_centro_costos'];
			$whereIdPuc.=($whereIdPuc=='')? 'id='.$row['id_puc'] : ' OR id='.$row['id_puc'] ;
		}

		// CONSULTAR SI LAS CUENTAS DEL DOCUMENTO SON CON CENTRO DE COSTO OBLIGATORIO
		$mensajeValidacion='';
		$sql="SELECT id,cuenta,descripcion,centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPuc)";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
				foreach ($arrayCuentas[$row['id']] as $id_puc => $arrayResul) {
					if ($row['centro_costo']=='Si' && ($arrayResul==0 || $arrayResul=='') ) {
						$mensajeValidacion.=($mensajeValidacion=='')? $row['cuenta'].' - '.$row['descripcion'] : '\n'.$row['cuenta'].' - '.$row['descripcion'] ;
					}

				}

		}
		if ($mensajeValidacion<>'') {
			echo '<script>
					alert("Error!\nHay cuentas que requieren un centro de costo y no lo tienen asignado\nVerifique las siguientes cuentas:\n'.$mensajeValidacion.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		// SI NO TIENE UN NUMERO DE FACTURA ENTONCES SE BORRA EL PREFIJO
		$prefijo_factura = ($numero_factura=='')? '' : $prefijo_factura;
	 	//MOVEMOS LAS CUENTAS
		moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'agregar',$id_tercero,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$id,'eliminar',$link);

		$fecha = date("Y-m-d");
		$sql   = "UPDATE $tablaPrincipal SET estado=1,prefijo_factura='$prefijo_factura',numero_factura='$numero_factura' WHERE id='$id' AND activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if (!$query){
			echo '<script>
					alert("Error!\nNo se pudo actualizar la factura, vuelva a intentarlo\nSi el problema persite comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			moverDocumentosSaldos($id_empresa,$id,'agregar',$link);
			moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',$id_tercero,$link);
		}
		else{
			$sqlConsecutivo = "SELECT consecutivo FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
			$queryConsecutivo=mysql_query($sqlConsecutivo,$link);
			$consecutivo_documento = mysql_result($queryConsecutivo,0,'consecutivo');

			$sqlUpdate = "UPDATE asientos_colgaap SET consecutivo_documento='$consecutivo_documento' WHERE id_documento='$id' AND tipo_documento='FC' AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);

			$sqlUpdate = "UPDATE asientos_niif SET consecutivo_documento='$consecutivo_documento' WHERE id_documento='$id' AND tipo_documento='FC' AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);

		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','FC','Factura de Compra por Cuentas',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

	   	echo'<script>
   				Ext.get("contenedor_FacturaCompraCuentas").load({
 		            url     : "facturacion_cuentas/bd/grillaContableBloqueada.php",
 		            scripts : true,
 		            nocache : true,
 		            params  :
 		            {
						id_factura_compra : '.$id.',
						opcGrillaContable : "FacturaCompraCuentas",
 		            }
 		        });
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
	}

	//============================= FUNCION PARA MOVER LOS SALDOS (ABONOS) DE LOS DOCUMENTOS RELACIONADOS EN FC Y FV ================================//
	function moverDocumentosSaldos($id_empresa,$id_nota,$accion,$link){

		if ($accion=='agregar') {
			// ACTUALIZAR EL SALDO DE LOS COMPROBANTES DE EGRESO RELACIONADO EN LA FACTURA POR CUENTAS
			$sql="UPDATE comprobante_egreso_cuentas AS CEC,
						 (
							SELECT
								CFC.*
							FROM
								compras_facturas_cuentas AS CFC
							WHERE
								CFC.id_factura_compra = $id_nota
							AND CFC.tipo_documento_cruce = 'CE'
							AND CFC.id_tabla_referencia >0
						) AS CFC
						SET CEC.saldo_pendiente = CEC.saldo_pendiente + (CFC.debe + CFC.haber)
						WHERE
						CEC.activo=1
						AND	CEC.id = CFC.id_tabla_referencia
						AND CEC.cuenta = CFC.cuenta_puc";

		}
		else if ($accion=='eliminar') {
			// ACTUALIZAR EL SALDO DE LAS CUENTAS DEL COMPROBANTE DE EGRESO
			$sql="UPDATE comprobante_egreso_cuentas AS CEC,
						 (
							SELECT
								CFC.*
							FROM
								compras_facturas_cuentas AS CFC
							WHERE
								CFC.id_factura_compra = $id_nota
							AND CFC.tipo_documento_cruce = 'CE'
							AND CFC.id_tabla_referencia >0
						) AS CFC
						SET CEC.saldo_pendiente = CEC.saldo_pendiente - (CFC.debe + CFC.haber)
						WHERE
						CEC.activo=1
						AND	CEC.id = CFC.id_tabla_referencia
						AND CEC.cuenta = CFC.cuenta_puc";
		}

		// //EJECUTAR LOS QUERY
		$query = mysql_query($sql,$link);
		if (!$query) { echo '<script>alert("Error!\nNo se actualizo el saldo de los documentos CE");</script>'; }
	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A

	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$idEmpresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,$accion,$id_tercero,$link){

		if ($accion=='agregar') {
			$sqlNotaGeneral   = "SELECT consecutivo,proveedor,fecha_inicio,id_configuracion_cuenta_pago,cuenta_pago,cuenta_pago_niif,prefijo_factura,numero_factura
								FROM compras_facturas
								WHERE activo=1
									AND id='$idDocumento'
									AND id_empresa='$idEmpresa'";
			$queryNotaGeneral = mysql_query($sqlNotaGeneral,$link);

			$prefijo_factura              = mysql_result($queryNotaGeneral,0,'prefijo_factura');
			$numero_factura               = mysql_result($queryNotaGeneral,0,'numero_factura');
			$consecutivo                  = mysql_result($queryNotaGeneral,0,'consecutivo');
			$proveedor                    = mysql_result($queryNotaGeneral,0,'proveedor');
			$fecha_inicio                 = mysql_result($queryNotaGeneral,0,'fecha_inicio');
			$cuenta_pago                  = mysql_result($queryNotaGeneral,0,'cuenta_pago');
			$cuenta_pago_niif             = mysql_result($queryNotaGeneral,0,'cuenta_pago_niif');
			$id_configuracion_cuenta_pago = mysql_result($queryNotaGeneral,0,'id_configuracion_cuenta_pago');

			//CONSULTAR SI LA FORMA DE PAGO ES DE CONTADO O A CREDITO
			$sql   = "SELECT estado FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=$idEmpresa AND id=$id_configuracion_cuenta_pago ";
			$query = mysql_query($sql,$link);
			$forma_pago = mysql_result($query,0,'estado');

			$sql   = "SELECT debe,haber,cuenta_puc,cuenta_niif,id_tercero,id_documento_cruce,tipo_documento_cruce,prefijo_documento_cruce,numero_documento_cruce,id_centro_costos,observacion
						FROM $tablaCuentasNota WHERE $idTablaPrincipal='$idDocumento' AND activo=1";
			$query = mysql_query($sql,$link);

			$cuentaVaciaColgaap  = 0;
			$cuentaVaciaNiif     = 0;
			$saldoDebitoColgaap  = 0;
			$saldoCreditoColgaap = 0;
			$saldoDebitoNiif     = 0;
			$saldoCreditoNiif    = 0;

			$valueInsertCuentasColgaap = "";
			$valueInsertCuentasNiif    = "";

			while ($row=mysql_fetch_assoc($query)){
				$documento_cruce = 0;

				if($row['debe'] == ''){ $row['debe'] = 0;}
				if($row['haber'] == ''){ $row['haber'] = 0;}

				//VALIDACION SI HAY REGISTROS SIN CUENTAS COLGAAP
				if($row['cuenta_puc'] == 0 || $row['cuenta_puc']==''){ $cuentaVaciaColgaap++; }

				//VALIDACION SI HAY REGISTROS SIN CUENTAS NIIF
				if($row['cuenta_niif'] == 0 || $row['cuenta_niif']==''){ $cuentaVaciaNiif++; }

				//VALIDACION DOBLE PARTIDA CUENTAS COLGAAP-NIIF
				if($row['cuenta_puc'] > 0){
					$saldoDebitoColgaap  += $row['debe'];
					$saldoCreditoColgaap += $row['haber'];
				}

				if($row['cuenta_niif'] > 0){
					$saldoDebitoNiif  += $row['debe'];
					$saldoCreditoNiif += $row['haber'];
				}

				// VALIDAR EL DOCUMENTO CRUCE
				// $row['id_documento_cruce'] = ($row['id_documento_cruce'] =='' || $row['id_documento_cruce'] ==0)? $idDocumento : $row['id_documento_cruce'] ;
				// $row['tipo_documento_cruce'] = ($row['tipo_documento_cruce']=='' )? 'FC' : $row['tipo_documento_cruce'];

				$id_tercero_nota = ($row['id_tercero']=='0' || $row['id_tercero']=='')? $id_tercero : $row['id_tercero'];

				if ($row['tipo_documento_cruce']=='' || $row['tipo_documento_cruce'] == 'FC') {
					$row['tipo_documento_cruce'] = 'FC';
					$row['id_documento_cruce']   = $idDocumento;
					$documento_cruce = ($prefijo_factura != '')? $prefijo_factura.' '.$numero_factura: $numero_factura;
				}
				else{
					$documento_cruce = ($row['prefijo_documento_cruce'] != '')? $row['prefijo_documento_cruce'].' '.$row['numero_documento_cruce']: $row['numero_documento_cruce'];
				}

				// SI EL DEBITO Y CREDITO ES IGUAL A 0 SALTAR LA CUENTA
				if ($row['debe']==0 && $row['haber']==0 ) { continue; }

				$valueInsertCuentasColgaap .= "('$idDocumento',
												'$consecutivo',
												'FC',
												'Factura de Compra por Cuentas',
												'".$row['debe']."',
												'".$row['haber']."',
												'".$row['cuenta_puc']."',
												'$id_sucursal',
												'$id_tercero_nota',
												'".$_SESSION['NITEMPRESA']."',
												'',
												'$idEmpresa',
												'$fecha_inicio',
												'".$row['id_documento_cruce']."',
												'".$row['tipo_documento_cruce']."',
												'$documento_cruce',
												'".$row['id_centro_costos']."',
												'".$row['observacion']."'
												),";

				$valueInsertCuentasNiif .= "('$idDocumento',
											'$consecutivo',
											'FC',
											'Factura de Compra por Cuentas',
											'".$row['debe']."',
											'".$row['haber']."',
											'".$row['cuenta_niif']."',
											'$id_sucursal',
											'$id_tercero_nota',
											'".$_SESSION['NITEMPRESA']."',
											'',
											'$idEmpresa',
											'$fecha_inicio',
											'".$row['id_documento_cruce']."',
											'".$row['tipo_documento_cruce']."',
											'$documento_cruce',
											'".$row['id_centro_costos']."',
											'".$row['observacion']."'
											),";
			}

			// VALIDAR EL SALDO PARA LA CUENTA DE PAGO
			if($saldoDebitoColgaap < $saldoCreditoColgaap){
				echo '<script>
						alert("Aviso!\nEl credito de la factura es mayor que el debito, verifique el documento e intentelo de nuevo");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			$saldoCuentaPorPagar = abs($saldoDebitoColgaap - $saldoCreditoColgaap);
			$saldoCreditoColgaap +=$saldoCuentaPorPagar;
			$saldoCreditoNiif    +=$saldoCreditoColgaap;
			// SI ES NEGATIVO PASAR A POSITIVO
			// $saldoCuentaPorPagar=($saldoCuentaPorPagar<0)? $saldoCuentaPorPagar*-1 : $saldoCuentaPorPagar ;

			// AGREGAR LA CUENTA POR PAGAR AL INSERT SI TIENE SALDO
			if ($saldoCuentaPorPagar>0) {
				$document_cruce_head = ($prefijo_factura<>'')? "$prefijo_factura $numero_factura" : "$numero_factura" ;


				$valueInsertCuentasColgaap .= "('$idDocumento',
													'$consecutivo',
													'FC',
													'Factura de Compra por Cuentas',
													'0',
													'$saldoCuentaPorPagar',
													'$cuenta_pago',
													'$id_sucursal',
													'$id_tercero',
													'".$_SESSION['NITEMPRESA']."',
													'',
													'$idEmpresa',
													'$fecha_inicio',
													'$idDocumento',
													'FC',
													'$document_cruce_head',
													'0',
													''
													),";

				$valueInsertCuentasNiif .= "('$idDocumento',
											'$consecutivo',
											'FC',
											'Factura de Compra por Cuentas',
											'0',
											'$saldoCuentaPorPagar',
											'$cuenta_pago_niif',
											'$id_sucursal',
											'$id_tercero',
											'".$_SESSION['NITEMPRESA']."',
											'',
											'$idEmpresa',
											'$fecha_inicio',
											'$idDocumento',
											'FC',
											'$document_cruce_head',
											'0',
											''
											),";
			}


			//VALIDACIONES CONTABILIDAD COLGAAP
			if($cuentaVaciaColgaap > 0){
				echo '<script>
						alert("Aviso!\nExisten '.$cuentaVaciaColgaap.' registros sin cuentas en la contabilidad colgaap!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}
			else if($saldoDebitoColgaap == 0 || $saldoCreditoColgaap == 0){
				echo '<script>
						alert("Aviso!\nLos saldos deben ser mayores a cero en la contabilidad Colgaap!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			// else if($saldoDebitoColgaap != $saldoCreditoColgaap){ echo '<script>alert("Aviso!\nLos saldos Debitos ('.$saldoDebitoColgaap.') y Creditos ('.$saldoCreditoColgaap.') en la contabilidad colgaap son diferentes!");</script>'; exit; }

			if($cuentaVaciaNiif > 0){
				echo '<script>
						alert("Aviso!\nExisten '.$cuentaVaciaNiif.' registros sin cuentas en la contabilidad niif!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}
			else if($saldoDebitoNiif == 0 || $saldoCreditoNiif == 0){
				echo '<script>
						alert("Aviso!\nLos saldos deben ser mayores a cero en la contabilidad niif!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}
			// else if($saldoDebitoNiif != $saldoCreditoNiif){ echo '<script>alert("Aviso!\nLos saldos Debitos ('.$saldoDebitoNiif.') y Creditos ('.$saldoCreditoNiif.')  en la contabilidad Niif son diferentes!");</script>'; exit; }

			$valueInsertCuentasColgaap = substr($valueInsertCuentasColgaap, 0, -1);
			$valueInsertCuentasNiif    = substr($valueInsertCuentasNiif, 0, -1);

			$sqlInsert   = "INSERT INTO asientos_colgaap(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								debe,
								haber,
								codigo_cuenta,
								id_sucursal,
								id_tercero,
								nit_tercero,
								tercero,
								id_empresa,
								fecha,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								id_centro_costos,
								observacion)
							VALUES $valueInsertCuentasColgaap";
			$queryInsert = mysql_query($sqlInsert,$link);
			if (!$queryInsert) {
				echo '<script>
						alert("Error!\nNo se genero el asiento contable Colgaap, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
			contabilizacionSimultanea($idDocumento,'FC',$id_sucursal,$idEmpresa,$link);

			$sqlInsert   = "INSERT INTO asientos_niif(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								debe,
								haber,
								codigo_cuenta,
								id_sucursal,
								id_tercero,
								nit_tercero,
								tercero,
								id_empresa,
								fecha,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								id_centro_costos,
								observacion)
							VALUES $valueInsertCuentasNiif";
			$queryInsert = mysql_query($sqlInsert,$link);
			if (!$queryInsert) {
				$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='FC'";
				$queryDelete = mysql_query($sqlDelete,$link);

				echo'<script>
						alert("Error!\nNo se genero el asiento contable Niif, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// ACTUALIZAR EL VALOR DE LA CUENTA POR PAGAR
			if ($forma_pago!='Contado') {
				$sql="UPDATE compras_facturas SET total_factura=$saldoCuentaPorPagar,total_factura_sin_abono=$saldoCuentaPorPagar WHERE activo=1 AND id_empresa='$idEmpresa' AND id='$idDocumento' ";
				$query=mysql_query($sql,$link);
				if (!$query) {
					$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='FC'";
					$queryDelete = mysql_query($sqlDelete,$link);
					$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='FC'";
					$queryDelete = mysql_query($sqlDelete,$link);

					echo '<script>
							alert("Error!\nNo se actualizo el valor para el campo total factura\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}
		}

		else if ($accion=='eliminar') {
			$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='FC'";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){
				echo '<script>
						alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}

			$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='FC'";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){
				echo '<script>
						alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}
		}
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){
		validaDocumentoPrincipalCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link);

		$sql   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo=mysql_result($query,0 ,'consecutivo');

		//VALIDAMOS QUE NO TENGA ARTICULOS RELACIONADOS, SE DEBE REVERSAR EL MOVIMIENTO QUE HICIERON LOS ARTICULOS
		// $sqlValidaArticulos   = "SELECT COUNT(id) AS cont FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
		// $queryValidaArticulos = mysql_query($sqlValidaArticulos,$link);
		// $cont = mysql_result($queryValidaArticulos,0,'cont');

		// if ($cont>0) {

		// 	//SI TIENE ARTICULOS SE DEBE REVERSAR EL MOVIMIENTO DEL MISMO, LLAMAMOS LA FUNCION PARA REALIZAR
		// 	$sqlArticulos   = "SELECT id,tipo FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa'";
		// 	$queryArticulos = mysql_query($sqlArticulos,$link);

		// 	$resul=0;

		// 	//RECORREMOS TODOS LOS ARTICULOS Y LLAMAMOS LA FUNCION PARA REVERSAR EL PROCESO DE CADA UNO
		// 	while ($rowArticulos=mysql_fetch_array($queryArticulos)) { $resul=eliminarArticuloRelacionado($rowArticulos['id'],$rowArticulos['tipo'],'return',$link); }

		// 	if ($resul>0) { echo '<script>alert("Erro!\nNo se actualizo el inventario\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
		// }

		//PARA MODIFICAR EL DOCUMENTO PRIMERO DEBEMOS DESCONTABILIZARLO Y LUEGO REGRESARLO A ESTADO=0
		moverCuentasDocumento($idDocumento,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
		//YA QUE SE DIERON DE BAJA LOS ASIENTOS GENERADOS POR LA NOTA, PROCEDEMOS A ACTUALIZAR SU ESTADO A CERO
		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$idDocumento,'agregar',$link);

		if($query){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','FC','Factura de Compra por Cuentas',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "facturacion_cuentas/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_compra : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'"
						}
					});

					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se insertaron los articulos nuevamente al inventario!");</script>'; }
	}

	//=========================== FUNCION PARA GUARDAR CUENTA DE LA GRILLA ==================================================================//
	function guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$idTablaReferencia,$link){
		//VALIDACIONES
		if ($debe==0 && $haber==0) { echo '<script>alert("Error\nDebe ingresar el monto  del debito o del credito");</script>'; return; }
		elseif ($debe>0 && $haber>0) { echo '<script>alert("Error\nDebe ingresar el monto solo para el debito o el credito");</script>'; return; }

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$prefijoDocumentoCruce,$numeroDocumentoCruce,$opcGrillaContable,$cont,$terceroGeneral,$id_tercero,$cuenta,'guardar',$debe,$haber,$link); }

		$sqlInsert = "INSERT INTO $tablaCuentasNota(
						$idTablaPrincipal,
						id_puc,
						debe,
						haber,
						id_tercero,
						tipo_documento_cruce,
						id_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						id_tabla_referencia,
						id_empresa)
					VALUES(
						'$id',
						'$idPuc',
						'$debe',
						'$haber',
						'$id_tercero',
						'$tipoDocumentoCruce',
						'$id_documento_cruce',
						'$prefijoDocumentoCruce',
						'$numeroDocumentoCruce',
						'$idTablaReferencia',
						'$id_empresa')";
		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

		if($lastId > 0){
			$debe  = (is_nan($debe))? 0  : $debe;
			$haber = (is_nan($haber))? 0 : $haber;

			// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
			$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$idPuc ";
			$query=mysql_query($sql,$link);
			$centro_costo = mysql_result($query,0,'centro_costo');

			$script =($centro_costo=='Si')? 'document.getElementById("label_cont_'.$opcGrillaContable.'_'.$cont.'").innerHTML="<div style=\'float:right;\'>'.$cont.'</div><div style=\'float:left;\'><img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'></div>";' : '';


			$body=cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrillaContable);

			echo'<script>
					document.getElementById("idInsertCuenta'.$opcGrillaContable.'_'.$cont.'").value = '.$lastId.'

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Cuenta");
					document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					document.getElementById("deleteCuenta'.$opcGrillaContable.'_'.$cont.'").style.display     = "block";
					document.getElementById("configurarCuenta'.$opcGrillaContable.'_'.$cont.'").style.display = "block";
					// document.getElementById("adjuntar'.$opcGrillaContable.'_'.$cont.'").style.display = "block";

					// llamamos a la funcion para calcular los totales de la nota
					calcTotal'.$opcGrillaContable.'("'.$debe.'","'.$haber.'","agregar");

					'.$script.'

				</script>'.$body;

		}
		else{
			echo'<script>
					alert("Error\nNo se ha guardo la cuenta en la nota, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
					var elemento=document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'");
					//elemento.parentNode.removeChild(elemento);
			  	</script>';
		}
	}

	//=========================== FUNCION PARA VALIDAR SI SE CRUZO EL DOCUMENTO ==============================================================//
	function validaDocumentoPrincipalCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link){

		$sqlNota    = "SELECT consecutivo_documento, tipo_documento FROM asientos_colgaap WHERE id_documento_cruce = '$idDocumento' AND tipo_documento_cruce='FC' AND tipo_documento<>'FC' AND activo=1 AND id_empresa = '$id_empresa' /*AND id_sucursal = '$id_sucursal'*/ GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';

		while ($row=mysql_fetch_array($queryNota)) { $doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento']; }
		if ($doc_cruces != '') {
			echo '<script>
					alert("Error!\nEste documento tiene relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nPor favor anule los documentos para poder modificarlo");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
	}

	function validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce='',$numero_documento_cruce,$opcGrillaContable,$cont,$id_tercero_general,$id_tercero,$cuenta,$evento,$debe,$haber,$link){
		if ($documento_cruce=='FC') { return true; }

		$script = '';
		$cont2  = $cont;

		//CON LA VARIABLE EVENTO IDENTIFICAMOS SI SE ESTA GUARDANDO O ACTUALIZANDO UNA CUENTA, PARA ASI MOSTRAR O NO UN BLOQUE DE CODIGO Y PARA EL CONTADOR
		if ($evento=='guardar') {
			$script = 'document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.($cont++).'").parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.($cont++).'"));
						contArticulos'.$opcGrillaContable.'--;';
			$cont2  = ($cont-2);
		}

		//VALIDAR QUE TENGA UN NUMERO DE DOCUMENTO
		if ($numero_documento_cruce=='' || $numero_documento_cruce=='0') {
			echo'<script>
					alert("Si relaciona una '.$documento_cruce.', debe seleccionar el documento!");
					'.$script.'
					document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont2.'").focus();
				</script>';
			exit;
		}

		//VALIDAR QUE EL NUMERO DE LA CUENTA QUE SE ESTA RELACIONANDO CON EL DOCUMENTO EXISTA EN LOS ASIENTOS RELACIONADOS AL DOCUMENTO CRUCE
		$sqlAsientos   = "SELECT id,SUM(debe - haber) AS saldoCuenta
							FROM asientos_colgaap
							WHERE id_documento_cruce='$id_documento_cruce'
								AND tipo_documento_cruce='$documento_cruce'
								AND codigo_cuenta='$cuenta'
								AND id_empresa='$id_empresa'
								AND activo=1
							GROUP BY id_documento_cruce";
		$queryAsientos = mysql_query($sqlAsientos,$link);
		$idAsiento     = mysql_result($queryAsientos,0,'id');
		$saldoCuentaDb = mysql_result($queryAsientos,0,'saldoCuenta');
		$saldoCuenta   = $debe-$haber;

		$absCuenta   = abs($saldoCuenta);
		$absCuentaDb = abs($saldoCuentaDb);

		//SINO EXISTE EL ASIENTO DE ESE DOCUMENTO RELACIONADO
		if ($idAsiento=='') {
			echo '<script>
					alert(" La cuenta '.$cuenta.' de la '.$documento_cruce.' relacionada no existe en los asientos de ese documento\nPor favor digite una cuenta que genero el documento relacionado");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
		else if(($saldoCuentaDb < 0 && $saldoCuenta < 0) || ($saldoCuentaDb > 0 && $saldoCuenta > 0)){
			echo '<script>
					alert("No se permite debitar o acreditar la misma cuenta en mas de una ocacion.");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
		else if($absCuenta > $absCuentaDb){
			echo '<script>
					alert("El valor a cruzar es superior al registrado en el documento ('.$absCuentaDb.').");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$idTablaReferencia,$link){

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$prefijoDocumentoCruce,$numeroDocumentoCruce,$opcGrillaContable,$cont,$terceroGeneral,$id_tercero,$cuenta,'actualizar',$debe,$haber,$link); }

		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo
		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura
		$sqlArticuloAnterior   = "SELECT debe,haber FROM $tablaCuentasNota WHERE id='$idInsertCuenta' AND $idTablaPrincipal='$id' ";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);

		$debeAnterior  = mysql_result($queryArticuloAnterior,0,'debe');
		$haberAnterior = mysql_result($queryArticuloAnterior,0,'haber');

		$numeroDocumentoCruce = ($numeroDocumentoCruce > 0)? $numeroDocumentoCruce: 'NULL';

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		$sqlUpdateArticulo   = "UPDATE $tablaCuentasNota
								SET id_puc='$idPuc',
								debe                    = '$debe',
								haber                   = '$haber',
								id_tercero              = '$id_tercero',
								id_documento_cruce      = '$id_documento_cruce',
								tipo_documento_cruce    = '$tipoDocumentoCruce',
								prefijo_documento_cruce = '$prefijoDocumentoCruce',
								numero_documento_cruce  = '$numeroDocumentoCruce',
								id_tabla_referencia     = '$idTablaReferencia'
								WHERE $idTablaPrincipal=$id AND id=$idInsertCuenta";
		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);


		if ($queryUpdateArticulo) {

			$debe  = ($debe=='')? 0: $debe;
			$haber = ($haber=='')? 0: $haber;

			// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
			$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$idPuc ";
			$query=mysql_query($sql,$link);
			$centro_costo = mysql_result($query,0,'centro_costo');

			$sql = "SELECT id_centro_costos FROM $tablaCuentasNota WHERE activo=1 AND id=$idInsertCuenta;";
			$query = mysql_query($sql,$link);
			$id_centro_costos = mysql_result($query,0,'id_centro_costos');

			$script =($centro_costo=='Si' && ($id_centro_costos=='' || $id_centro_costos==0) )? 'document.getElementById("label_cont_'.$opcGrillaContable.'_'.$cont.'").innerHTML="<div style=\'float:right;\'>'.$cont.'</div><div style=\'float:left;\'><img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'></div>";' :
																								'document.getElementById("label_cont_'.$opcGrillaContable.'_'.$cont.'").innerHTML="'.$cont.'";'  ;

			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

					//llamamos la funcion para recalcular el costo de la nota
					calcTotal'.$opcGrillaContable.'("'.$debeAnterior.'","'.$haberAnterior.'","eliminar");
					calcTotal'.$opcGrillaContable.'("'.$debe.'","'.$haber.'","agregar");

					'.$script.'

				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo la cuenta"); </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION DE LA NOTA =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){
		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link){
		//CONSULTAMOS EL DOCUMENTO PARA SABER SI ESTA GENERADO
		$sql   = "SELECT estado,consecutivo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$estado      = mysql_result($query,0 ,'estado');
		$consecutivo = mysql_result($query,0 ,'consecutivo');

		if($estado=='3'){
			echo '<script>
					alert("Error!\nEsta nota ya esta cancelada!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				return;
		}
		else if ($estado=='0' && $consecutivo=='') { $sqlUpdate="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";	}
		else if($estado=='0' && $consecutivo!=''){$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";	}
		else if ($estado=='1') {
			//VALIDAMOS QUE NO TENGA ARTICULOS RELACIONADOS, SE DEBE REVERSAR EL MOVIMIENTO QUE HICIERON LOS ARTICULOS
			$sqlValidaArticulos   ="SELECT COUNT(id) AS cont FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
			$queryValidaArticulos =mysql_query($sqlValidaArticulos,$link);
			$cont=mysql_result($queryValidaArticulos,0,'cont');

			if ($cont>0) {

				//SI TIENE ARTICULOS SE DEBE REVERSAR EL MOVIMIENTO DEL MISMO, LLAMAMOS LA FUNCION PARA REALIZAR
				$sqlArticulos   = "SELECT id,tipo FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa'";
				$queryArticulos = mysql_query($sqlArticulos,$link);

				$resul=0;

				//RECORREMOS TODOS LOS ARTICULOS Y LLAMAMOS LA FUNCION PARA REVERSAR EL PROCESO DE CADA UNO
				while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
					$resul=eliminarArticuloRelacionado($rowArticulos['id'],$rowArticulos['tipo'],'return',$link);
				}

				if ($resul>0) {
					echo '<script>
							alert("Erro!\nNo se actualizo el inventario\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			//DESCONTABILIZAMOS LA NOTA, ELIMINADO LOS ASIENTOS QUE SE GENERARON A PARTIR DE ELLA
			//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
			moverDocumentosSaldos($id_empresa,$id,'agregar',$link);

			moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
			//CADENA PARA ACTUALIZAR LA NOTA
			$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";

		}

		//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		$queryUpdate = mysql_query($sqlUpdate,$link);


		if (!$queryUpdate) {
			echo '<script>
					alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else{
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','FC','Factura de Compra por Cuentas',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo '<script>
					nueva'.$opcGrillaContable.'();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}

	}

 	//=========================== FUNCION PARA ELIMINAR LOS ARTICULOS RELACIONADOS A LA NOTA ==========================================================//
 	function eliminarArticuloRelacionado($id,$tipo,$opc='',$link){
 		//PRIMERO REVERSAMOS EL PROCESO QUE SE EJECUTO CUANDO SE GENERO EL MOVIMIENTO, ES DECIR, SI SALIERON ARTICULOS ENTONCES VUELVEN A INGRESAR, Y VICEVERSA

 		$cont=0;
 		//CONSULTA DEL ARTICULO Y LAS CANTIDADES INGRESADAS PARA PROCEDER A AGREGAR O ELIMINAR DEL INVENTARIO
		$sqlConsul   = "SELECT id_item,cantidad,id_bodega,id_sucursal,costo FROM inventario_movimiento_notas WHERE id='$id' ";
		$queryConsul = mysql_query($sqlConsul,$link);

		$id_item     = mysql_result($queryConsul,0,'id_item');
		$costo       = mysql_result($queryConsul,0,'costo');
		$cantidad    = mysql_result($queryConsul,0,'cantidad');
		$id_bodega   = mysql_result($queryConsul,0,'id_bodega');
		$id_sucursal = mysql_result($queryConsul,0,'id_sucursal');
		$costo_total = $cantidad*$costo;

 		if ($tipo=='entrada') {
 			//SI SE ENTRARON ARTICULOS CON LA NOTA, AL ELIMINAR EL REGISTRO, ENTONCES SE DEBEN SACAR
			// $sqlInventario   = "UPDATE inventario_totales SET cantidad=cantidad-$cantidad WHERE id_item='$id_item' AND id_sucursal='$id_sucursal' AND id_ubicacion='$id_bodega' ";
			// $queryInventario = mysql_query($sqlInventario,$link);
 			// if (!$queryInventario) { $cont++; }
 			// MOVER EL KARDEX
 			$sql   = "UPDATE inventario_totales AS IT
						SET IT.costos=IF(IT.cantidad-$cantidad = 0, 0, ((IT.cantidad * IT.costos) - ($costo_total))/(IT.cantidad-$cantidad)),
							IT.cantidad=IT.cantidad-$cantidad
						WHERE IT.id_item=$id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$id_bodega'";

			$query = mysql_query($sql,$link);
 		}
 		else if ($tipo=='salida') {
			//SI SE SACARON ARTICULOS CON LA NOTA, AL ELIMINAR EL REGISTRO, ENTONCES SE DEBEN INGRESAR
			// echo$sqlInventario   = "UPDATE inventario_totales SET cantidad=cantidad+$cantidad WHERE id_item='$id_item' AND id_sucursal='$id_sucursal' AND id_ubicacion='$id_bodega' ";
			// $queryInventario = mysql_query($sqlInventario,$link);
			// if (!$queryInventario) { $cont++; }
			// MOVER EL KARDEX
 			$sql   = "UPDATE inventario_totales AS IT
						SET IT.costos=((IT.cantidad * IT.costos)+($costo_total))/(IT.cantidad+$cantidad),
							IT.cantidad=IT.cantidad+$cantidad
						WHERE IT.id_item=$id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$id_bodega'";

			$query = mysql_query($sql,$link);
 		}
 		else if ($tipo=='baja_activo_fijo'){
 			// ACTUALIZAR DE NUEVO EL ACTIVO FIJO
 			$sql="UPDATE activos_fijos SET activo=1 WHERE id=$id_item";
 			$query=mysql_query($sql,$link);
 		}

		$sql   = "DELETE FROM inventario_movimiento_notas WHERE id='$id'";
		$query = mysql_query($sql,$link);
 		if (!$query) { $cont++; }

 		if ($opc=='return') { return $cont; }
 		else if ($cont==0 && $opc=='') { echo "true"; }
 		else if ($cont>0 && $opc=='') { echo "false"; }

 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal'  AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {

			$sqlConsulDoc="SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";

			//VERIFICAR SI FUE GENERADO ANTES DE CANCELAR
			$queryConsulDoc = mysql_query($sqlConsulDoc,$link);
			$consecutivo    = mysql_result($queryConsulDoc,0,'consecutivo');

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','FC','Factura de Compra por Cuentas',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "facturacion_cuentas/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_compra : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'"
						}
					});
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = "Consecutivo<br>N. "+"'.$consecutivo.'";
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
		else{
			echo '<script>alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			return;
		}
 	}

 	//=========================== FUNCION PARA ACTUALIZARLE TIPO DE NOTA CONTABLE ====================================================================//
 	function actualizarTipoNota($id,$idTipoNota,$notaCruce,$tablaPrincipal,$id_empresa,$link){
		$sql   = "UPDATE $tablaPrincipal SET id_tipo_nota='$idTipoNota' WHERE id='$id' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

 		if (!$query) {
 			echo'<script>
 					alert("Error!\nNo se actualizo el tipo de nota\nSi el problema persiste comuniquese con el administrador del sistema");
 					document.getElementById("selectTipoNota").focus();
 				</script>';
 			exit;
 		}

 		$classNota = ($notaCruce == "Si")? 'contenedorNotaContableCruce': 'contenedorNotaContable';

 		echo'<script>document.getElementById("contenedorNotaContable").setAttribute("class","'.$classNota.'");</script>';
 	}

 	//=========================== FUNCION PARA ACTUALIZAR LA FECHA DE LA NOTA =========================================================================//
	function guardarFechaFactura($idInputDate,$idFactura,$valInputDate,$link){
		// validaEstadoFactura($idFactura,$link);
		if($idInputDate=='fechaFacturaCuentas'){ $sqlUpdateFecha = "UPDATE compras_facturas SET  fecha_inicio='$valInputDate' WHERE id='$idFactura'"; }
		else if($idInputDate=='fechaFinalFacturaCuentas'){ $sqlUpdateFecha = "UPDATE compras_facturas SET  fecha_final='$valInputDate' WHERE id='$idFactura'"; }

		$queryUpdateFecha = mysql_query($sqlUpdateFecha,$link);
		if($queryUpdateFecha){ echo 'true'; }
		else{ echo'false'; }
	}

 	//REDERIZA FILTRO TIPO DE DOCUMENTO
 	function ventana_buscar_documento_cruce($cont,$opcGrillaContable,$carpeta,$id_empresa,$id_sucursal,$link){
 		echo'<select class="myfield" name="filtro_tipo_documento" id="filtro_tipo_documento" style="width:100px; margin: 2px 0px 0px 4px;" onChange="carga_filtro_tipo_documento(this.value)">
        		<option value="FC">FC</option>
        		<option value="FV">FV</option>
    		</select>
    		<script>
				function carga_filtro_tipo_documento(tipo_documento_cruce){
					var filtroTipoDocumento = document.getElementById("filtro_tipo_documento").value;
					Ext.get("contenedor_buscar_documento_cruce_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'bd/grillaDocumentoCruce.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opc                  : "'.$opc.'",
							filtro_sucursal      : '.$id_sucursal.',
							tipo_documento_cruce : tipo_documento_cruce,
							cont                 : "'.$cont.'",
							opcGrillaContable    : "'.$opcGrillaContable.'",
							carpeta              : "'.$carpeta.'",
							tablaPrincipal       : "nota_contable_general",
							idTablaPrincipal     : "id_nota_general",
							tablaCuentasNota     : "nota_contable_general_cuentas",
						}
					});
				}
				// carga_filtro_tipo_documento();
			</script>';
 	}

 	//========================== FUNCION PARA CARGAR LA CUENTA NIIF ==============================================================//
 	function cargaConfiguracionCuenta($idInsertCuenta,$idCuenta,$id_empresa,$opcGrillaContable,$cont,$link){
 		$sql   = "SELECT cuenta_niif,descripcion_niif,cuenta_puc,id_centro_costos,codigo_centro_costos,centro_costos,observacion
 					FROM compras_facturas_cuentas
 					WHERE id='$idInsertCuenta'
 						AND id_puc='$idCuenta'
 						AND id_empresa='$id_empresa'
 						AND activo=1";
 		$query = mysql_query($sql,$link);

 		$btn_ccos=(mysql_result($query,0,'codigo_centro_costos')>0)? '<img src="../inventario/img/false_inv.png" style="cursor:pointer;width:16px;height:16px;" title="Eliminar Centro Costos" onclick="eliminarCentroCostos('.$cont.','.$idInsertCuenta.')" id="imgCentroCostos">'
 																	: '<img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta" onclick="ventanaBuscarCentroCostos('.$cont.')" id="imgCentroCostos">' ;

 		echo '<div style="width:100%;padding-top:10px;">
 				<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;">CUENTA NIIF DE '.mysql_result($query,0,'cuenta_puc').' COLGAAP</div>

 				<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5 0 5 3;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CUENTA</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5 0 5 3;font-weight: bold;font-size: 11px;">DESCRIPCION</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.',\'niif\')"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>

 					<div id="cuenta_niif" style="float:left;width:100px;border-right:1px solid #D4D4D4;padding: 5 0 5 3;">'.mysql_result($query,0,'cuenta_niif').'</div>
 					<div id="descripcion_niif" style="float:left;width:calc(100% - 110px);padding: 5 0 5 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.mysql_result($query,0,'descripcion_niif').'</div>
 				</div>

 				<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;margin-top:20px;float: left;width:100%;">CENTRO DE COSTOS</div>

 				<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5 0 5 3;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CODIGO</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5 0 5 3;font-weight: bold;font-size: 11px;">CENTRO DE COSTOS</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" >'.$btn_ccos.'</div>

 					<div id="codigo_centro_costos" style="float:left;width:100px;border-right:1px solid #D4D4D4;padding: 5 0 5 3;">'.mysql_result($query,0,'codigo_centro_costos').'</div>
 					<div id="nombre_centro_costos" style="float:left;width:calc(100% - 110px);padding: 5 0 5 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.mysql_result($query,0,'centro_costos').'</div>

 				</div>

 				<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float:left;width:99%;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">OBSERVACION</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5px; border-left: 1px solid #D4D4D4;" onclick="guardarObservacionCuenta('.$cont.')"><img src="img/save_true.png" style="cursor:pointer;width:16px;height:16px;" title="Guardar Observacion"></div>
 					<textarea id="observacionCuentaFacturaCompraCuentas">'.mysql_result($query,0,'observacion').'</textarea>
 				</div>

			</div>';
 	}

 	//=========================== FUNCION PARA ACTUALIZAR LA CUENTA NIIF DE UNA COLGAAP ====================================//
 	function actualizarNiif($id_niif,$cuenta,$idInsertCuenta,$idCuenta,$opcGrillaContable,$cont,$id_empresa,$link){
		$sql   = "UPDATE compras_facturas_cuentas SET id_niif='$id_niif' WHERE  id='$idInsertCuenta' AND id_puc='$idCuenta' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);

 		if ($query){ echo 'true'; }
 		else{ echo 'false'; }
 	}

 	// ========================== FUNCION PARA ACTUALIZAR EL CENTRO DE COSTOS ==============================================//
 	function actualizarCcos($idInsertCuenta,$idCuenta,$opcGrillaContable,$nombre_centro_costos,$codigo_centro_costos,$id_centro_costos,$id_empresa,$link){
 		$sql   = "UPDATE compras_facturas_cuentas
	 				SET id_centro_costos='$id_centro_costos',
	 					codigo_centro_costos='$codigo_centro_costos',
	 					centro_costos='$nombre_centro_costos'
	 				WHERE  id='$idInsertCuenta'
	 					AND id_puc='$idCuenta'
	 					AND id_empresa='$id_empresa'
	 					AND activo=1";
 		$query = mysql_query($sql,$link);

 		if ($query){ echo 'true'; }
 		else{ echo 'false'; }
 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$link){
		$sql   = "SELECT estado,consecutivo FROM compras_facturas_cuentas WHERE id=$id_documento";
		$query = mysql_query($sql,$link);

		$estado    = mysql_result($query,0,'estado');
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

		if ($estado>0) {
			echo'<script>

					alert("'.$mensaje.'");

					if (document.getElementById("Win_Ventana_cambiar_cuenta_niif")) {
						Win_Ventana_cambiar_cuenta_niif.close();
					}

					Ext.get("contenedor_NotaGeneral").load({
		        		    url     : "facturacion_cuentas/bd/grillaContableBloqueada.php",
		        		    scripts : true,
		        		    nocache : true,
		        		    params  :
		        		    {
							id_factura_compra : '.$id_documento.',
							opcGrillaContable : "FacturaCompraCuentas",
		        		    }
		        		});

				</script>';
			exit;
		}

	}

	function validarCuenta($cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM puc WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

	function eliminarCentroCostos($cont,$idInsertCuenta,$id_empresa,$link){
		$sql   = "UPDATE compras_facturas_cuentas SET id_centro_costos='',codigo_centro_costos='',centro_costos='' WHERE activo=1 AND id_empresa=$id_empresa AND id='$idInsertCuenta'";
		$query = mysql_query($sql,$link);

		if ($query) { echo 'true{.}'; }
		else{ echo 'false{.}'; }
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT fecha_inicio,fecha_final FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		$fecha_inicio      = mysql_result($query,0,'fecha_inicio');
		$fecha_final = mysql_result($query,0,'fecha_final');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha_inicio)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha_inicio)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha_final)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha_final)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final ";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ((fecha_nota>='$fecha_inicio_buscar_1' AND fecha_nota<='$fecha_fin_buscar_1') OR (fecha_nota>='$fecha_inicio_buscar_2' AND fecha_nota<='$fecha_fin_buscar_2')  ) ";
		$query=mysql_query($sql,$link);
		$cont2 = mysql_result($query,0,'cont');

		if ($cont1>0 || $cont2>0) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}
	}

	function validar_centro_costo($cont,$id_ccos,$codigo_centro_costos,$centro_costos,$id_empresa,$link){
		$sql="SELECT COUNT(id) AS cont FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND codigo LIKE '$codigo_centro_costos%' AND codigo<>'$codigo_centro_costos' ";
		$query=mysql_query($sql,$link);
		$res = mysql_result($query,0,'cont');
		if ($res>0) {
			echo '<script>
					alert("Debe seleccionar un centro de costos hijo!");
				</script>';
		}
		else{
			echo "<script>
					renderSelectedCcos_FacturaCompraCuentas(".$id_ccos.",".$cont.");
				</script>";
		}
	}

	function ventanaVerImagenDocumentoTerceros($nombreImage,$nombreDocumento,$type,$id_host){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas_cuentas/'.$nombreImage)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas_cuentas/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($type!='pdf'){
			echo'<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
						<a href="'.$url.'" download="'.$nombreDocumento.'">
							<img src="'.$url.'" style="">
						</a>
					</div>
				</div>';
		}
		else{
			echo'<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<iframe src="'.$url.'" id="iframeViewDocumentTerceros"></iframe>
					</div>
				</div>
				<script>
					cambiaViewPdf();
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeViewDocumentTerceros");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function consultaSizeImageDocumentTerceros($id_host,$nombre){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas_cuentas/'.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas_cuentas/'.$nombre;
		}
		else{
			$url = '';
		}

		list($ancho, $alto, $tipo, $atributos) = getimagesize($url);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;
		$size['url']  = $url;
		echo json_encode($size);
	}

	function eliminarArchivoAdjunto($id,$nombre,$id_host,$mysql){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas_cuentas/'.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas_cuentas/'.$nombre;
		}
		else{
			$url = '';
		}

		if ( unlink($url) ) {
			$sql="DELETE FROM compras_facturas_archivos_adjuntos WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
						var element = document.getElementById("archivo_adjunto_'.$id.'");
						element.parentNode.removeChild(element);
						MyLoading2("off",{texto:"Registro Eliminado"});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error! No se elimino el registro en base de datos",duracion:2500});
						// alert("Error!\nSe elimino el archivo, pero no el registro en base de datos");
					</script>';
			}
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error! no se elimino el archivo adjunto",duracion:2500});
					// alert("Error!\nNo se Elimino el Archivo Adjunto");
				</script>';
		}

	}

	function guardarObservacionCuenta($id_documento,$id,$observacion,$id_empresa,$mysql){
		$sql="UPDATE compras_facturas_cuentas SET observacion = '$observacion' WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$id_documento AND id=$id";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					MyLoading2("off");
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"No se guardo la Observacion",duracion:2000});
				</script>';
		}
	}

	function validarNumeroFactura($prefijoFactura,$numeroFactura,$idFactura,$nitProveedor,$id_empresa,$link){
		$sqlFactura  = "SELECT prefijo_factura,numero_factura,fecha_generacion,hora_generacion,proveedor
										FROM compras_facturas
										WHERE activo = 1
										AND id_empresa = '$id_empresa'
										AND id <> '$idFactura'
										AND estado > 0
										AND activo = 1
										AND prefijo_factura = '$prefijoFactura'
										AND numero_factura = '$numeroFactura'
										AND nit = $nitProveedor";
		$queryFactura = mysql_query($sqlFactura,$link);

		$facturasRepetidas = "";
		while($row = mysql_fetch_assoc($queryFactura)){
			$numero_factura    = ($row['prefijo_factura'] != '')? $row['prefijo_factura'].'-'.$row['numero_factura']: $row['numero_factura'];
			$facturasRepetidas = '\n\n* FACTURA: '.$numero_factura.' \nPROVEEDOR: '.$row['proveedor'].' \nFECHA Y HORA: '.fecha_larga($row['fecha_generacion']).' '.$row['hora_generacion'];
		}

		if($facturasRepetidas != ""){
			echo '<script>
							alert("Aviso,\nSe encontraron las siguientes facturas con una numeracion igual a la ingresada: ' . $facturasRepetidas . '");
						</script>';
						return true;
			exit;
		}
		else{
			return false;
		}
	}
?>
