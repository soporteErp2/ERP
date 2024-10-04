<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	// include("../../../funciones_globales/funciones_javascript/totalesNotaContable.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");
	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if(isset($id)){
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if($opc<>'actualizarFechaNota'){
			verificaCierre($id,'fecha_comprobante',$tablaPrincipal,$id_empresa,$link);
		}

		if($opc!='cancelarDocumento' && $opc!='restaurarDocumento' && $opc!='modificarDocumentoGenerado' && $opc!='updateCuentaPago' && $opc!='updateFlujoEfectivo' && $opc!='updateDisponibleArchivoPlano' && $opc!='ventanaObservacionCuenta' && $opc!='eliminarArchivoAdjunto'){
			verificaEstadoDocumento($id,$link);
		}
	}

	switch($opc){
		case 'updateTerceroHead':
			updateTerceroHead($id,$codTercero,$id_empresa,$opcGrillaContable,$inputId,$link);
			break;

		case 'cargaHeadInsertUnidadesConTercero':
			cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable);
			break;

		case 'buscarCuenta':
			buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$idCliente,$opcGrillaContable,$link);
			break;

		case 'deleteCuenta':
			deleteCuenta($cont,$id,$idCuenta,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'ventanaObservacionCuenta':
			ventanaObservacionCuenta($cont,$idCuenta,$id,$opcGrillaContable,$idTablaPrincipal,$tablaCuentasNota,$readonly,$id_empresa,$link);
			break;

		case 'guardarDescripcionCuenta':
			guardarDescripcionCuenta($observacion,$idCuenta,$id,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'actualizarCcos':
			actualizarCcos($idRow,$id_centro_costos,$codigo_centro_costos,$id_empresa,$mysql);
			break;

		case 'retrocederCuenta':
		 	retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'validaNota':
			validaNota($id_empresa,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_tercero,$cuenta,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id_empresa,$id_sucursal,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$id_tercero,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id_empresa,$id_sucursal,$id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'guardarCuenta':
			guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero_general,$id_tercero,$terceroGeneral,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$id_tabla_referencia,$link);
			break;

		case 'actualizaCuenta':
			actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$id_tercero_general,$cuenta,$id_tabla_referencia,$link);
			break;

		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$id_sucursal,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'actualizarFechaNota':
			actualizarFechaNota($id,$fecha,$tablaPrincipal,$link);
			break;

		case 'ventana_buscar_documento_cruce':
			ventana_buscar_documento_cruce($id_comprobante_egreso,$cont,$opcGrillaContable,$id_empresa,$id_sucursal,$link);
			break;

		case 'ventana_buscar_sucursal':
			ventana_buscar_sucursal($cont,$id_comprobante_egreso,$opcGrillaContable,$id_sucursal,$link);
			break;

		case 'ventana_buscar_terceros':
			ventana_buscar_terceros($cont,$id_comprobante_egreso,$id_sucursal,$opcGrillaContable);
			break;

		case 'guardarNumeroCheque':
			guardarNumeroCheque($numeroCheque,$id,$link);
			break;

		case 'updateCuentaPago':
			verificaEstadoDocumento($id,$link);
			updateCuentaPago($idConfiguracion,$id,$id_empresa,$link);
			break;

		case 'updateFlujoEfectivo':
			verificaEstadoDocumento($id,$link);
			updateFlujoEfectivo($idFlujoEfectivo,$flujo_efectivo,$id,$id_empresa,$link);
			break;

		case 'updateDisponibleArchivoPlano':
			verificaEstadoDocumento($id,$link);
			updateDisponibleArchivoPlano($disponible,$id,$id_empresa,$mysql);
			break;

		case 'validarCuenta':
			validarCuenta($cuenta,$id_empresa,$link);
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

		case 'mostrarAlmacenamiento':
			mostrarAlmacenamiento();
			break;
	}

	//=================================// FUNCION PARA BUSCAR UN CLIENTE //=================================//
	function updateTerceroHead($id,$codTercero,$id_empresa,$opcGrillaContable,$inputId,$link){

		//CONSULTA EL COMPROBANTE DE EGRESO
		$sqlCe   = "SELECT COUNT(id) AS contCe,id_tercero,codigo_tercero,nit_tercero,tercero,observacion,estado FROM comprobante_egreso WHERE id='$id'";
		$queryCe = mysql_query($sqlCe,$link);

		$contCe        = mysql_result($queryCe, 0, 'contCe');
		$id_terceroCe  = mysql_result($queryCe, 0, 'id_tercero');
		$codigoCe      = mysql_result($queryCe, 0, 'codigo_tercero');
		$nitCe         = mysql_result($queryCe, 0, 'nit_tercero');
		$nombreCe      = mysql_result($queryCe, 0, 'tercero');
		$observacionCe = mysql_result($queryCe, 0, 'observacion');
		$estadoCe      = mysql_result($queryCe, 0, 'estado');

		if($contCe == 0 || is_nan($contCe)){ echo '<script>alert("Aviso,\nNo se encontro informacion sobre el comprobante de egreso!")</script>'; exit; }

		//CONSULTA LA INFORMACION DE NUEVO TERCERO
		$campo   = ($inputId=='nitCliente'.$opcGrillaContable)? "numero_identificacion": "codigo";
		$mensaje = ($inputId=='nitCliente'.$opcGrillaContable)? 'alert("NIT de tercero no establecido");' : 'alert("codigo de tercero no establecido");';

		$sql   = "SELECT COUNT(id) AS contTercero,id,numero_identificacion,tipo_identificacion,codigo,nombre FROM terceros WHERE $campo='$codTercero' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);

		$id_tercero  = mysql_result($query,0,'id');
		$contTercero = mysql_result($query,0,'contTercero');
		$nit         = mysql_result($query,0,'numero_identificacion');
		$codigo      = mysql_result($query,0,'codigo');
		$nombre      = mysql_result($query,0,'nombre');

		//SI EL NUEVO TERCERO EXISTE Y ES DIFERENTE DEL ANTERIOR
		if($id_tercero > 0 && $id_terceroCe != $id_tercero){
			$sqlUpdate = "UPDATE comprobante_egreso
						SET id_tercero     = '$id_tercero',
							tercero        = '$nombre',
							codigo_tercero = '$codigo',
							nit_tercero    = '$nit'
						WHERE id='$id'";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			echo "<script>
					id_cliente_ComprobanteEgreso = $id_tercero;
					document.getElementById('codigoTercero".$opcGrillaContable."').value = '$codigo';
					document.getElementById('nitCliente".$opcGrillaContable."').value    = '$nit';
					document.getElementById('nombreCliente".$opcGrillaContable."').value = '$nombre';
				</script>";

		}
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$head = '<div class="contenedorGrilla">
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
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsInsertUnidadesConTercero('return',$cont,$opcGrillaContable).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'" onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
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
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotalDebito'.$opcGrillaContable.'").innerHTML = parseFloat(creditoAcumulado'.$opcGrillaContable.').toFixed('.$_SESSION['DECIMALESMONEDA'].');
					document.getElementById("subtotalCredito'.$opcGrillaContable.'").innerHTML = parseFloat(creditoAcumulado'.$opcGrillaContable.').toFixed('.$_SESSION['DECIMALESMONEDA'].');
					document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML    = parseFloat(total'.$opcGrillaContable.').toFixed('.$_SESSION['DECIMALESMONEDA'].');
				</script>';

		if($formaConsulta=='return'){ return $head; }
		else{ echo $head; }
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;" id="label_cont_'.$cont.'">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoGrilla" style="width:95px;">
					<input type="text" style="text-align:left;" id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);" />
				</div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" />
				</div>

				<div class="campoGrilla" style="width:30%;"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="iconBuscarArticulo">
					<img src="img/buscar20.png" title="Buscar Tercero" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" id="imgBuscarTercero_'.$cont.'"  onclick="buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')"/>
				</div>

				<div class="campoGrilla" style="width:5%">
					<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly/>
				</div>

				<div class="campoGrilla">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputPrefijoFacturaComprobanteEgreso" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'mayuscula\',\''.$cont.'\');" readonly/>
					-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroFacturaComprobanteEgreso" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');" readonly/>
				</div>
				<div class="iconBuscarArticulo">
					<img onclick="ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" />
				</div>

				<div class="campoGrilla"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'"  onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/></div>
				<div class="campoGrilla"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'"  onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaObservacionCuenta'.$opcGrillaContable.'('.$cont.')" id="descripcionCuenta'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion/Detalle" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/config16.png"/></div>
					<div onclick="deleteCuenta'.$opcGrillaContable.'('.$cont.')" id="deleteCuenta'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idTercero'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idTablaReferencia'.$opcGrillaContable.'_'.$cont.'" value="0" />

				<script>
					document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
	function buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$idCliente,$opcGrillaContable,$link){
		/*
		SE DEBE VALIDAR LAS CUENTAS DE LA SIGUIENTE MANERA:
			- SI SE INSERTO UN ASIENTO CON UNA CUENTA CON MENOR CANTIDAD DE DIGITOS A LA QUE SE VA A BUSCAR,
			ES DECIR, SI BUSCAMOS LA CUENTA 110505 (UN NIVEL INFERIOR) PERO YA SE INSERTARON DATOS EN LA 110505 EN ADELANTE
			NO PODREMOS USAR ESA CUENTA, Y SI BUSCAMOS LA CUENTA 11050501 Y SE INSERTO UN ASIENTO EN LA CUENTA
			110505 (UN NIVEL SUPERIOR), TAMPOCO SE PODRA UTILIZAR LA CUENTA DE 8 DIGITOS

		*/

		//IDENTIFICAMOS LA LONGITUD DE LA CUENTA
		if (strlen ($cuenta)<5) {
			echo '<script>
					alert("Ingrese una cuenta de minimo 6 digitos!");
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
				</script>';
			exit;
		}
		else if (strlen ($cuenta)>5 AND strlen ($cuenta)<7) {
			$where   = ' LEFT(codigo_cuenta,6) = \''.$cuenta.'\'';
			$mensaje = 'Existe(n) una(s)   cuenta(s) de esta, con 8 digitos, no se puede utilizar esta de 6\nDigite apartir de 8 digitos para continuar';
		}
		else if (strlen ($cuenta)>7 && strlen ($cuenta)<9) {
			$where   = ' codigo_cuenta =  LEFT(\''.$cuenta.'\',6) ';
			$mensaje = 'Existe(n) una(s)   cuenta(s) de esta, con 6 digitos, no se puede utilizar esta de 8\nDigite solo de 6 digitos para continuar';
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
					document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value = "0";
					document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").value   = "";
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
		$sqlCuentaAsientos  = "SELECT id_cuenta,cuenta FROM asientos_colgaap WHERE codigo_cuenta = '$cuenta' AND id_empresa=$id_empresa";
		$queryCuentaAsiento = mysql_query($sqlCuentaAsientos,$link);

		$id_cuenta          = mysql_result($queryCuentaAsiento,0, 'id_cuenta');
		$descripcion_cuenta = mysql_result($queryCuentaAsiento,0, 'cuenta');

		if ($descripcion_cuenta!='') {
			echo'<script>
					document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value     = "'.$id_cuenta.'";
					setTimeout(function(){ document.getElementById("debito'.$opcGrillaContable.'_'.$contFila.'").focus();},100);
				</script>';
		}
		//SI LA CUENTA NO EXISTE ENTONCES SE VA A VALIDAR ANTES DE CONTINUAR
		else{

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

			$sql         = "SELECT id,descripcion FROM puc WHERE cuenta='$cuenta' AND (LENGTH(cuenta)>5) AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$query       = mysql_query($sql,$link);
			$id          = mysql_result($query,0,'id');
			$descripcion = mysql_result($query,0,'descripcion');

			if ($descripcion!='') {
				echo '<script>
						document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value     = "'.$id.'";
						setTimeout(function(){ document.getElementById("debito'.$opcGrillaContable.'_'.$contFila.'").focus();},100);
					 </script>';
			}
			else{
				echo'<script>
						alert("El Numero de cuenta No se encuentra asignado en el PUC de la empresa\nO el Numero tiene menos de 6 digitos");
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$contFila.'").focus(); },100);
						document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value ="0";
						document.getElementById("debito'.$opcGrillaContable.'_'.$contFila.'").value   ="";
					</script>';
			}
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sql   = "SELECT id_puc,cuenta,debito,credito,id_documento_cruce,prefijo_documento_cruce,numero_documento_cruce,tipo_documento_cruce,tercero,id_tabla_referencia
					FROM $tablaCuentasNota
					WHERE $idTablaPrincipal='$id' AND id='$idCuentaInsert' ";
		$query = mysql_query($sql,$link);

		$idCuenta                = mysql_result($query,0,'id_puc');
		$cuenta                  = mysql_result($query,0,'cuenta');
		$descripcion             = mysql_result($query,0,'descripcion');
		$debe                    = mysql_result($query,0,'debito');
		$haber                   = mysql_result($query,0,'credito');
		$tercero                 = mysql_result($query,0,'tercero');
		$id_documento_cruce      = mysql_result($query,0,'id_documento_cruce');
		$tipo_documento_cruce    = mysql_result($query,0,'tipo_documento_cruce');
		$prefijo_documento_cruce = mysql_result($query,0,'prefijo_documento_cruce');
		$numero_documento_cruce  = (mysql_result($query,0,'numero_documento_cruce')==0)? '' : mysql_result($query,0,'numero_documento_cruce') ;
		$id_tabla_referencia     = mysql_result($query,0,'id_tabla_referencia');

		echo'<script>
				if ("'.$tercero.'"!="") {
					document.getElementById("imgBuscarTercero_'.$cont.'").setAttribute("src","img/eliminar.png");
        			document.getElementById("imgBuscarTercero_'.$cont.'").setAttribute("title","Eliminar Tercero");
        			document.getElementById("imgBuscarTercero_'.$cont.'").setAttribute("onclick"," eliminaTercero'.$opcGrillaContable.'('.$cont.')");
				}else if("'.$tercero.'"==""){
					document.getElementById("imgBuscarTercero_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarTercero_'.$cont.'").setAttribute("title","Buscar Tercero");
        			document.getElementById("imgBuscarTercero_'.$cont.'").setAttribute("onclick","buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')");
				}

				if ("'.$numero_documento_cruce.'"!="") {
					document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("src","img/eliminar.png");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("title","Eliminar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("onclick"," eliminaDocumentoCruce'.$opcGrillaContable.'('.$cont.')");
				}else if("'.$numero_documento_cruce.'"==""){
					document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("title","Buscar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("onclick","ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.')");
				}

				document.getElementById("idCuenta'.$opcGrillaContable.'_'.$cont.'").value                 = "'.$idCuenta.'";
				document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$cuenta.'";
				document.getElementById("tercero'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$tercero.'";
				document.getElementById("idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value         = "'.$id_documento_cruce.'";
				document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value           = "'.$tipo_documento_cruce.'";
				document.getElementById("prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value    = "'.$prefijo_documento_cruce.'";
				document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value     = "'.$numero_documento_cruce.'";
				document.getElementById("debito'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$debe.'";
				document.getElementById("credito'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$haber.'";
				document.getElementById("idTablaReferencia'.$opcGrillaContable.'_'.$cont.'").value        = "'.$id_tabla_referencia.'";
				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
			</script>';

	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteCuenta($cont,$id,$idCuenta,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sqlDelete   = "DELETE FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuenta'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar la cuenta, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo'<script>
					document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'").parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
				</script>';
		}
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaObservacionCuenta($cont,$idCuenta,$id,$opcGrillaContable,$idTablaPrincipal,$tablaCuentasNota,$readonly,$id_empresa,$link){

		$placeHolder          = ($readonly == '')? "Escriba aqui la observacion...": "";
		$sqlDB                = "SELECT id_puc,observaciones,id_centro_costos,codigo_centro_costos,centro_costos FROM $tablaCuentasNota WHERE id='$idCuenta' AND $idTablaPrincipal='$id' AND activo = 1 LIMIT 0,1";
		$queryDB              = mysql_query($sqlDB,$link);
		$observacion          = mysql_result($queryDB,0,'observaciones');
		$idPuc                = mysql_result($queryDB,0,'id_puc');
		$id_centro_costos     = mysql_result($queryDB,0,'id_centro_costos');
		$codigo_centro_costos = mysql_result($queryDB,0,'codigo_centro_costos');
		$centro_costos        = mysql_result($queryDB,0,'centro_costos');

		// CONSULTAR SI LA CUENTA REQUIERE CENTRO DE COSTO, PARA ASIGNARLO
		$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$idPuc";
		$query=mysql_query($sql,$link);

		if (mysql_result($query,0,'centro_costo')=='Si') {
			if ($readonly=='') {
				$btn_ccos=($id_centro_costos>0)? '<img src="../inventario/img/false_inv.png" style="cursor:pointer;width:16px;height:16px;" title="Eliminar Centro Costos" onclick="eliminarCentroCostos'.$opcGrillaContable.'('.$cont.','.$idCuenta.')" id="imgCentroCostos">'
												: '<img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta" onclick="ventanaBuscarCentroCostos'.$opcGrillaContable.'('.$cont.','.$idCuenta.')" id="imgCentroCostos">' ;
			}


			echo '<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float: left;width:100px;background-color:#F3F3F3;padding: 5 0 5 3;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CODIGO</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5 0 5 3;font-weight: bold;font-size: 11px;">CENTRO DE COSTOS</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" >'.$btn_ccos.'</div>

 					<div id="codigo_centro_costos" style="float:left;width:100px;border-right:1px solid #D4D4D4;padding: 5 0 5 3;">&nbsp;'.$codigo_centro_costos.'</div>
 					<div id="nombre_centro_costos" style="float:left;width:calc(100% - 110px);padding: 5 0 5 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.$centro_costos.'</div>

 				</div>

				<div id="renderizaGuardarObservacion'.$opcGrillaContable.'_'.$cont.'" style="position:fixed; width:18px; overflow:hidden; margin-left:7px;"></div>
				<div style="font-size:12px;float:left;margin-top:10px;margin-left: 20px;">
					<textarea id="observacionArticulo'.$opcGrillaContable.'_'.$cont.'" style="width:286px;height:80px;border:1px solid #d4d4d4; padding-left:5px;" '.$readonly.' placeholder="'.$placeHolder.'">'.$observacion.'</textarea>
				</div>
 				';
		}
		else{
			echo'<div id="renderizaGuardarObservacion'.$opcGrillaContable.'_'.$cont.'" style="position:fixed; width:18px; overflow:hidden; margin-left:7px;"></div>
				<div style="font-size:12px;">
					<textarea id="observacionArticulo'.$opcGrillaContable.'_'.$cont.'" style="height:100%; border:1px solid #d4d4d4; padding-left:5px;" '.$readonly.' placeholder="'.$placeHolder.'">'.$observacion.'</textarea>
				</div>';
		}
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionCuenta($observacion,$idCuenta,$id,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaCuentasNota SET observaciones='$observacion' WHERE id='$idCuenta' AND $idTablaPrincipal='$id'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){ echo '<script>Win_Ventana_descripcion_cuenta.close(id);</script>'; }
		else{ echo 'La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema'; }
	}

	// ======================== FUNCION PARA GUARDAR O ELIMINAR EL CENTRO DE COSTOS DE UNA CUENTA ==============================================//
	function actualizarCcos($idRow,$id_centro_costos='',$codigo_centro_costos='',$id_empresa,$mysql){
		// VALIDAR CENTRO COSTOS HIJO
		if ($codigo_centro_costos<>'') {
			$sql="SELECT COUNT(id) AS cont FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND codigo LIKE '$codigo_centro_costos%' AND codigo<>'$codigo_centro_costos' ";
			$query=$mysql->query($sql,$mysql->link);
			$res = $mysql->result($query,0,'cont');
			if ($res>0) {
				echo 'padre';
				return;
			}
		}

		$sql="UPDATE comprobante_egreso_cuentas SET id_centro_costos='$id_centro_costos'
				WHERE activo=1 AND id=$idRow";
		$query=$mysql->query($sql,$mysql->link);

		if ($query) {
			echo 'true';
		}
		else{
			echo 'false';
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA NOTA ==========================================================================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id_empresa,$id_sucursal,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$id_tercero,$link){
		// VALIDAR QUE EL COMPROBANTE DE EGRESO TENGA UNA CUENTA CRUZE DE CABECERA SELECCIONADA
		$sql="SELECT id_configuracion_cuenta FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id ";
		$query=mysql_query($sql,$link);
		$id_row=mysql_result($query,0,'id_configuracion_cuenta');
		if ($id_row==0 || $id_row=='') {
			echo '<script>
					alert("Debe seleccionar la cuenta de cabecera del comprobante!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$fecha = date("Y-m-d");
		$sql   = "UPDATE $tablaPrincipal SET estado=1, fecha_generado='$fecha' WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);
		if (!$query){
			echo '<script>
					alert("Error!\nNo se pudo actualizar el documento, vuelva a intentarlo\nSi el problema persite comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}

	 	//MOVEMOS LAS CUENTAS
		moverCuentasDocumento($id_empresa,$id_sucursal,$id,$tablaCuentasNota,$idTablaPrincipal,'agregar',$id_tercero,$link);

		//RESTAMOS SALDOS DE LOS DOCUMENTOS
		moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'eliminar',$link);

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip)
					     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','CE','Comprobante de Egreso',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."')";
		$queryLog = mysql_query($sqlLog,$link);

		//CONSULTAR EL CONSECUTIVO PARA MOSTRARLO DtablaPrincipalE TITULO EN LA VENTANA
		$sql         = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa'";
		$consecutivo = mysql_result(mysql_query($sql,$link),0,'consecutivo');

	   	echo'<script>
   				Ext.get("contenedor_ComprobanteEgreso").load({
 		            url     : "comprobante_egreso/bd/grillaContableBloqueada.php",
 		            scripts : true,
 		            nocache : true,
 		            params  :
 		            {
						opcGrillaContable     : "ComprobanteEgreso",
						id_comprobante_egreso : '.$id.'
 		            }
 		        });
				document.getElementById("titleDocuementoComprobanteEgreso").innerHTML="Comprobante de Egreso<br>N. '.$consecutivo.'";
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
	}

	//============================ FUNCION PARA VALIDAR EL COMPROBANTE Y LLAMAR LA FUNCION TERMINAR PARA GENERARLA =========================================================================//
	function validaNota($id_empresa,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_tercero,$cuenta,$link){
		// VALIDAR LAS PLANILLAS QUE SE ESTAN CRUZANDO
		validaCuentasNomina($id,$id_empresa,$link);
		$sqlConsulNota   = "SELECT id_tercero,tercero,fecha_comprobante FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$queryConsulNota = mysql_query($sqlConsulNota,$link);

		$id_tercero_general = mysql_result($queryConsulNota,0,'id_tercero');
		$tercero            = mysql_result($queryConsulNota,0,'tercero');
		$fecha_nota         = mysql_result($queryConsulNota,0,'fecha_comprobante');

		$mes_fecha_nota  = date("m",strtotime($fecha_nota));
		$anio_fecha_nota = date("y",strtotime($fecha_nota));

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

		// VALIDAR QUE LAS CUENTAS QUE REQUIEREN CENTRO DE COSTOS TENGAN UNO ASIGNADO
		$sql = "SELECT id_puc FROM $tablaCuentasNota WHERE activo=1 AND id_comprobante_egreso=$id AND (id_centro_costos='' OR ISNULL(id_centro_costos) ) ";
		$query = mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereCuentas.=($whereCuentas=='')? 'id='.$row['id_puc'] : ' OR id='.$row['id_puc'] ;
		}

		$sql="SELECT cuenta,descripcion FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND centro_costo='Si' AND ($whereCuentas)";
		$query=mysql_query($sql,$link);
		$cont_cuentas = 0;
		while ($row=mysql_fetch_array($query)) {
			$cont_cuentas++;
			$cuentas.=$row['cuenta'].'\n';
		}

		if($cont_cuentas>0){
			echo '<script>
					alert("Las siguientes cuentas requieren centro de costo:\n'.$cuentas.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//UNA VEZ QUE RECORRIMOS LAS CUENTAS Y SE VALIDO QUE ESTUVIERAN CORRECTAMENTE INSERTADAS, Y SE TIENE EL ACUMULADO DEL DEBITO Y DEL CREDITO, VERIFICAMOS QUE ESTE BALACEADA LA NOTA, ES DECIR QUE LA DIFERENCIA
		//ENTRE EL DEBITO-CREDITO SEA IGUAL A CERO, SI NO ES IGUAL A CERO ENTONCES NO ESTA BALANCEADA LA NOTA Y NO SE PUEDE GENERAR
		$sqlConsulNotaGenerada   = "SELECT COUNT(id) AS cont FROM $tablaPrincipal WHERE fecha_comprobante>='$fecha_buscar' AND activo=1 AND estado=1 AND id_sucursal='$id_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryConsulNotaGenerada = mysql_query($sqlConsulNotaGenerada,$link);
		$cont = mysql_result($queryConsulNotaGenerada,0,'cont');
		//SI CONT ES MAYOR A CERO, HAY NOTAS GENERADAS EN EL MES SIGUIENTE, ASI QUE SE ADVERTIRA AL USUARIO
		if ($cont>0) {
			echo '<script>
					if (confirm("Aviso!\nExiten '.$cont.' notas creadas del mes siguiente a la fecha de la nota!\nSi continua no coincidara el consecutivo con el mes\nDesea continuar de todos modos?")) {
						generarComprobanteEgreso();
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
					else{
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			return;
		}
		else{
			echo '<script>
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					cargando_documentos("Generando Documento...","");
				</script>';
			moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'validar',$link);
			terminarGenerar($id_empresa,$id_sucursal,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$id_tercero,$link);
		}
	}

	// ====================== FUNCION PARA VALIDAR LOS DOCUMENTOS DE NOMINA CRUZADOS EN EL COMPROBANTE ===============================//
	function validaCuentasNomina($id_comprobante,$id_empresa,$link){
		$sql="SELECT
				tipo_documento_cruce,
				numero_documento_cruce,
				tercero
			FROM
				comprobante_egreso_cuentas
			WHERE
				activo = 1
			AND id_comprobante_egreso = $id_comprobante
			AND (tipo_documento_cruce='LN' OR tipo_documento_cruce='LE' OR tipo_documento_cruce='PA' OR tipo_documento_cruce='PCP')
			AND id_tabla_referencia NOT IN (
				SELECT
					id
				FROM
					nomina_planillas_empleados_contabilizacion
				WHERE
					activo =1
				AND id = id_tabla_referencia
			)";
		$query=mysql_query($sql,$link);
		$filas_erradas='';
		$cont=0;
		while ($row=mysql_fetch_array($query)) {
			$filas_erradas.=($row['numero_documento_cruce']>0)?'-> '.$row['tipo_documento_cruce'].' - '.$row['numero_documento_cruce'].' : '.$row['tercero'].'\n' : '';
			$cont+=($row['numero_documento_cruce']>0)? 1 : 0 ;
		}

		if ($cont>0) {
			echo '<script>
					alert("Los siguientes documentos cruce no existen! elimine la fila y busque de nuevo el documento\n\n'.$filas_erradas.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($id_empresa,$id_sucursal,$idDocumento,$tablaCuentasNota,$idTablaPrincipal,$accion,$id_tercero,$link){

		if ($accion=='agregar') {
			$sqlNotaGeneral  = "SELECT consecutivo,tercero,fecha_comprobante,id_tercero,cuenta,cuenta_niif,id_flujo_efectivo,flujo_efectivo
								FROM comprobante_egreso
								WHERE activo=1
									AND id = '$idDocumento'";
			$queryNotaGeneral = mysql_query($sqlNotaGeneral,$link);

			$consecutivoNota  = mysql_result($queryNotaGeneral,0,'consecutivo');
			$id_tercero       = mysql_result($queryNotaGeneral,0,'id_tercero');
			$tercero          = mysql_result($queryNotaGeneral,0,'tercero');
			$cuenta           = mysql_result($queryNotaGeneral,0,'cuenta');
			$cuentaNiif       = mysql_result($queryNotaGeneral,0,'cuenta_niif');
			$fechaComprobante = mysql_result($queryNotaGeneral,0,'fecha_comprobante');
			$idFlujoEfectivo  = mysql_result($queryNotaGeneral,0,'id_flujo_efectivo');
			$flujoEfectivo    = mysql_result($queryNotaGeneral,0,'flujo_efectivo');

			$sql   = "SELECT id,
						debito,
						credito,
						cuenta AS cuenta_colgaap,
						cuenta_niif,
						id_tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						id_centro_costos,
						observaciones
					FROM $tablaCuentasNota
					WHERE $idTablaPrincipal='$idDocumento' AND activo=1";
			$query = mysql_query($sql,$link);

			//RECORREMOS EL RESULTADO DEL QUERY Y LLENAMOS EL ARRAY PARA LOS ASIENTOS
			$saldoDebito         = 0;
			$saldoCredito        = 0;
			$valuesInsertNiif    = "";
			$valuesInsertColgaap = "";
			while ($row=mysql_fetch_array($query)){
				$documento_cruce   = ($row['prefijo_documento_cruce'] != '')? $row['prefijo_documento_cruce'].' '.$row['numero_documento_cruce']: $row['numero_documento_cruce'];
				$documento_cruce   = ($documento_cruce=='' || $documento_cruce=='0')? $consecutivoNota : $documento_cruce ;
				$row['id_tercero'] = ($row['id_tercero']=='0' || $row['id_tercero']=='')? $id_tercero : $row['id_tercero'];

				$saldoDebito  += (is_nan($row['debito']) || $row['debito'] == '')? 0: $row['debito'];
				$saldoCredito += (is_nan($row['credito']) || $row['credito'] == '')? 0: $row['credito'];

				$row['id_documento_cruce'] = ($row['id_documento_cruce'] =='' || $row['id_documento_cruce'] ==0)? $idDocumento : $row['id_documento_cruce'] ;
				$row['tipo_documento_cruce'] = ($row['tipo_documento_cruce']=='' )? 'CE' : $row['tipo_documento_cruce'];

				$sucursalCuenta = $id_sucursal;
				// if($row['id_documento_cruce'] > 0 && $row['tipo_documento_cruce']== 'FC'){
				// 	$sqlSucursalFv   = "SELECT COUNT(id) AS contSucursal, id_sucursal FROM compras_facturas WHERE id_empresa='$id_empresa' AND activo=1 AND id='$row[id_documento_cruce]' GROUP BY id LIMIT 0,1";
				// 	$querySucursalFv = mysql_query($sqlSucursalFv,$link);

				// 	$contSucursal = mysql_result($querySucursalFv, 0, 'contSucursal');

				// 	if($contSucursal>0) $sucursalCuenta = mysql_result($querySucursalFv, 0, 'id_sucursal');
				// }

				//============================== PARTIDA ================================//
	            $valuesInsertColgaap .= "($idDocumento,
										$consecutivoNota,
										'CE',
										'Comprobante de Egreso',
										'".$row['id_documento_cruce']."',
										'".$row['tipo_documento_cruce']."',
										'$documento_cruce',
										'".$row['debito']."',
										'".$row['credito']."',
										'".$row['cuenta_colgaap']."',
										$sucursalCuenta,
										$id_empresa,
										'".$row['id_tercero']."',
										'$fechaComprobante',
										'".$row['id_centro_costos']."',
										'0',
										'',
										'$row[observaciones]'),";

				$valuesInsertNiif .= "($idDocumento,
										$consecutivoNota,
										'CE',
										'Comprobante de Egreso',
										'".$row['id_documento_cruce']."',
										'".$row['tipo_documento_cruce']."',
										'$documento_cruce',
										'".$row['debito']."',
										'".$row['credito']."',
										'".$row['cuenta_niif']."',
										$sucursalCuenta,
										$id_empresa,
										'".$row['id_tercero']."',
										'$fechaComprobante',
										'".$row['id_centro_costos']."',
										'0',
										'',
										'$row[observaciones]'),";
			}
			
			if(ROUND($saldoDebito,$_SESSION['DECIMALESMONEDA']) < ROUND($saldoCredito,$_SESSION['DECIMALESMONEDA'])){
				echo '<script>
								alert("Aviso\nEl saldo credito no puede ser mayor al debito.\nDebito: '.$saldoDebito.' Credito: '.$saldoCredito.'");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
				rollbackEstado($idDocumento,$id_empresa,$link);
				exit;
			}
			$saldoCuentaCruce =  $saldoDebito - $saldoCredito;

			//============================= CONTRA PARTIDA =============================//
			$valuesInsertColgaap .= "($idDocumento,
									$consecutivoNota,
									'CE',
									'Comprobante de Egreso',
									'$idDocumento',
									'CE',
									'$consecutivoNota',
									0,
									'$saldoCuentaCruce',
									'$cuenta',
									$id_sucursal,
									$id_empresa,
									'$id_tercero',
									'$fechaComprobante',
									'".$row['id_centro_costos']."',
									'$idFlujoEfectivo',
									'$flujoEfectivo',
									''),";

			$valuesInsertNiif .= "($idDocumento,
									$consecutivoNota,
									'CE',
									'Comprobante de Egreso',
									'$idDocumento',
									'CE',
									'$consecutivoNota',
									0,
									'$saldoCuentaCruce',
									'$cuentaNiif',
									$id_sucursal,
									$id_empresa,
									'$id_tercero',
									'$fechaComprobante',
									'".$row['id_centro_costos']."',
									'$idFlujoEfectivo',
									'$flujoEfectivo',
									''),";

			$valuesInsertNiif    = substr($valuesInsertNiif, 0, -1);
			$valuesInsertColgaap = substr($valuesInsertColgaap, 0, -1);

			if($valuesInsertColgaap == ""){
				echo '<script>
								alert("Aviso!\nNo hay abonos guardados");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
				rollbackEstado($idDocumento,$id_empresa,$link);
				exit;
			}

			//INSERT ASIENTO COLGAAP
			$sql = "INSERT INTO asientos_colgaap (
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce,
						debe,
						haber,
						codigo_cuenta,
						id_sucursal,
						id_empresa,
						id_tercero,
						fecha,
						id_centro_costos,
						id_flujo_efectivo,
						flujo_efectivo,
						observacion)
					VALUES $valuesInsertColgaap";
			$queryInsert = mysql_query($sql,$link);
			if (!$queryInsert) {
				echo '<script>
						alert("Error!\nNo se genero el asiento contable, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				rollbackEstado($idDocumento,$id_empresa,$link);
				exit;
			}

			// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
			contabilizacionSimultanea($idDocumento,'CE',$id_sucursal,$id_empresa,$link);

			//INSERT ASIENTO NIIF
			$sql = "INSERT INTO asientos_niif (
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce,
						debe,
						haber,
						codigo_cuenta,
						id_sucursal,
						id_empresa,
						id_tercero,
						fecha,
						id_centro_costos,
						id_flujo_efectivo,
						flujo_efectivo,
						observacion)
					VALUES $valuesInsertNiif";
			$queryInsert = mysql_query($sql,$link);

			// ACTUALIZAR EL VALOR DE LA CUENTA POR PAGAR
			// $sql   = "UPDATE comprobante_egreso SET total_factura=$saldoCuentaCruce,total_factura_sin_abono=$saldoCuentaCruce WHERE activo=1 AND id_empresa='$id_empresa' AND id='$idDocumento'";
			// $query = mysql_query($sql,$link);

			if (!$query) {
				$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$id_empresa' AND tipo_documento='CE'";
				$queryDelete = mysql_query($sqlDelete,$link);
				$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$id_empresa' AND tipo_documento='CE'";
				$queryDelete = mysql_query($sqlDelete,$link);

				$sql   = "UPDATE comprobante_egreso SET estado=0 WHERE id='$idDocumento' AND activo=1 AND id_empresa='$id_empresa'";
				$query = mysql_query($sql,$link);


				echo '<script>
						alert("Error!\nNo se actualizo el valor para el campo total comprobante\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
		}
		else if ($accion=='eliminar') {
			$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$id_empresa' AND tipo_documento='CE' ";
			$queryDelete = mysql_query($sqlDelete,$link);

			if (!$queryDelete){
				echo '<script>
						alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$id_empresa' AND tipo_documento='CE' ";
			$queryDelete = mysql_query($sqlDelete,$link);
		}
	}

	function rollbackEstado($idDocumento,$id_empresa,$link){
		$sql   = "UPDATE comprobante_egreso SET estado = 0 WHERE id = '$idDocumento' AND activo = 1 AND id_empresa = '$id_empresa'";
		$query = mysql_query($sql,$link);
	}

	//=============================================== 	MODIFICAR DOCUMENTO YA GENERADO  ===============================================================//
	function modificarDocumentoGenerado($id_empresa,$id_sucursal,$idDocumento,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){

		// VALIDAR QUE EL COMPROBANTE NO ESTE CRUZADO
		validaComprobanteCruzado($idDocumento,$id_empresa,$link);

		$sql   = "SELECT consecutivo,id_tercero FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='".$_SESSION['EMPRESA']."' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo        = mysql_result($query,0 ,'consecutivo');
		$id_tercero_general = mysql_result($query,0 ,'id_tercero');

		//PARA MODIFICAR EL DOCUMENTO PRIMERO DEBEMOS DESCONTABILIZARLO Y LUEGO REGRESARLO A ESTADO=0
		moverCuentasDocumento($id_empresa,$id_sucursal,$idDocumento,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);

		//REGRESAMOS LOS SALDOS DE LOS DOCUMENTOS
		moverSaldoDocumentosRelacionados($idDocumento,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'agregar',$link);

		//YA QUE SE DIERON DE BAJA LOS ASIENTOS GENERADOS POR LA NOTA, PROCEDEMOS A ACTUALIZAR SU ESTADO A CERO
		$sql          = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
		$query_update = mysql_query($sql,$link);

		if($query_update){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','CE','Comprobante de Egreso',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "comprobante_egreso/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable     : "'.$opcGrillaContable.'",
							id_comprobante_egreso : "'.$idDocumento.'"
						}
					});

					Ext.getCmp("btnExportar'.$opcGrillaContable.'").disable();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
		else{
			echo '<script>
					alert("Error!\nNo se modifico el documento, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>'; }
	}

	//============================================ FUNCION PARA MOVER LOS SALDOS DE LOS DOCUMENTO RELACIONADOS EN LAS CUENTAS ======================================================//
	function moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$accion,$link){
		global $id_empresa;
		// VALIDAR SALDOS GENERAR COMPROBANTE EGRESO
		if ($accion != 'agregar') {
			// VALIDAR LAS FACTURAS DE COMPRA
			$whereIdFactura = '';
			$whereIdLiquidacionProvision = '';

			$sql   = "SELECT id,SUM(debito - credito) AS saldoDebitoabono,id_documento_cruce,tipo_documento_cruce,cuenta
						FROM $tablaCuentasNota
						WHERE activo=1
							AND $idTablaPrincipal='$id'
							AND tipo_documento_cruce<>''
							AND id_documento_cruce > 0
						GROUP BY id_documento_cruce ";
			$query = mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)){

				if ($row['tipo_documento_cruce'] == 'FC') {
					$idFactura = $row['id_documento_cruce'];
					$arraySaldoFacturas[$idFactura] += $row['saldoDebitoabono'];
					$whereIdFactura .= ($whereIdFactura != '')? ' OR id = '.$idFactura : 'id = '.$idFactura;
				}
				else if ($row['tipo_documento_cruce'] == 'LP') {
					$idLiquidacionProvision = $row['id_documento_cruce'];
					$arraySaldoLiquidacion[$idLiquidacionProvision] += $row['saldoDebitoabono'];
					$whereIdLiquidacionProvision .= ($whereIdLiquidacionProvision != '')? ' OR id = '.$idLiquidacionProvision : 'id = '.$idLiquidacionProvision;
				}
			}
			// CONSULTAMOS PARA VALIDAR QUE NO SE EXEDAN EN LOS SALDOS DE LAS FACTURAS DE COMPRA
			$sql   = "SELECT id, id_proveedor, prefijo_factura, numero_factura, total_factura_sin_abono AS saldo FROM compras_facturas WHERE activo=1 AND ( $whereIdFactura ) AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$idFactura = $row['id'];
				if($row['saldo'] < $arraySaldoFacturas[$idFactura]){
					$diferencia       = number_format($arraySaldoFacturas[$idFactura] - $row['saldo'],2);
					$numero_documento = ($row['prefijo_factura'] != '')? $row['prefijo_factura'].' '.$row['numero_factura']: $row['numero_factura'];
					echo '<script>
							alert("Error!\nLa Factura de compra numero '.$numero_documento.'\nExcede el saldo del documento relacionado con '.$diferencia.' de diferencia.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			// CONSULTAMOS PARA VALIDAR QUE NO SE EXEDAN EN LOS SALDOS DE LAS LIQUIDACIONES PROVISIONES
			$sql   = "SELECT id, id_tercero, consecutivo, total_sin_abono AS saldo FROM nomina_liquidacion_provision WHERE activo=1 AND ( $whereIdLiquidacionProvision ) AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$idLiquidacionProvision = $row['id'];
				if($row['saldo'] < $arraySaldoLiquidacion[$idLiquidacionProvision]){
					$diferencia       = number_format($arraySaldoLiquidacion[$idLiquidacionProvision] - $row['saldo'],2);
					$numero_documento = $row['consecutivo'];
					echo '<script>
							alert("Error!\nLa liquidacion de provision numero '.$numero_documento.'\nExcede el saldo del documento relacionado con '.$diferencia.' de diferencia.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			// VALIDAR LAS LIQUIDACION DE NOMINA Y LA LIQUIDACION DE EMPLEADOS
			$sql   = "SELECT id,SUM(debito - credito) AS saldoDebitoabono,id_documento_cruce,tipo_documento_cruce,cuenta
						FROM $tablaCuentasNota
						WHERE activo=1
							AND $idTablaPrincipal='$id'
							AND tipo_documento_cruce<>''
							AND id_documento_cruce > 0
						GROUP BY id_documento_cruce,cuenta ";
			while ($row=mysql_fetch_array($query)){

				if ($row['tipo_documento_cruce'] == 'LN' || $row['tipo_documento_cruce'] == 'LE' || $row['tipo_documento_cruce'] == 'PA') {
					$idPlanilla = $row['id_documento_cruce'];
					$cuenta     = $row['cuenta'];
					$tipo       = $row['tipo_documento_cruce'];

					$arraySaldoPlanilla[$idPlanilla][$tipo][$cuenta] += $row['saldoDebitoabono'];

					$whereIdPlanilla .= ($whereIdPlanilla != '')? ' OR ( id_planilla = '.$idPlanilla." AND tipo_planilla='".$row['tipo_documento_cruce']."' )"
																	: '(id_planilla = '.$idPlanilla." AND tipo_planilla='".$row['tipo_documento_cruce']."' )";
				}
			}

			//CONSULTAMOS PARA VALIDAR QUE NO SE EXEDAN EN LOS SALDOS DE LOS CONCEPTOS A PAGAR DE LOS EMPLEADOS
			$sql   = "SELECT id,id_planilla,tipo_planilla,consecutivo_planilla, total_sin_abono AS saldo,cuenta_colgaap
						FROM nomina_planillas_empleados_contabilizacion WHERE activo=1 AND ( $whereIdPlanilla ) AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$idPlanilla = $row['id_planilla'];
				$cuenta     = $row['cuenta_colgaap'];
				$tipo       = $row['tipo_planilla'];

				if ($tipo=='LN') {
					$nombre_documento = "Nomina";
				}
				else if ($tipo=='LE') {
					$nombre_documento = "Liquidacion";
				}
				else if ($tipo=='PA') {
					$nombre_documento = "Ajuste de Nomina";
				}
				else if ($tipo=='PCP') {
					$nombre_documento = "Consolidacion de Provision";
				}

				// $nombre_documento = ($tipo=='LE')? 'Liquidacion' : 'Nomina' ;

				if($row['saldo'] < $arraySaldoPlanilla[$idPlanilla][$tipo][$cuenta]){
					$diferencia       = number_format($arraySaldoPlanilla[$idPlanilla][$tipo][$cuenta] - $row['saldo'],2);
					echo '<script>
							alert("Error!\nLa cuenta de la planilla de '.$nombre_documento.' '.$row['consecutivo_planilla'].'\nExcede el saldo del documento relacionado con '.$diferencia.' de diferencia.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}


		}


		if ($accion=='agregar') {
			//ACTUALIZAR LAS FACTURAS DE COMPRA RELACIONADAS AL DOCUMENTO
		  	$sql = "UPDATE compras_facturas AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'FC'
													GROUP BY id_documento_cruce
												) AS CE
					SET CF.total_factura_sin_abono= IF((CF.total_factura_sin_abono+CE.abono)>CF.total_factura,CF.total_factura,CF.total_factura_sin_abono+CE.abono)
					WHERE CF.id=CE.id_documento_cruce
						AND CF.cuenta_pago=CE.cuenta
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			//ACTUALIZAR LAS LIQUIDACIONES DE PROVISION RELACIONADAS AL DOCUMENTO
		  	$sql = "UPDATE nomina_liquidacion_provision AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'LP'
													GROUP BY id_documento_cruce
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono+CE.abono
					WHERE CF.id=CE.id_documento_cruce
						AND CF.cuenta_colgaap_cruce=CE.cuenta
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			//ACTUALIZAR LAS PLANILLAS DE NOMINA RELACIONADAS AL DOCUMENTO
			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF,
						(SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
							FROM comprobante_egreso_cuentas
							WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'LN'
							GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
						) AS CE
					SET CF.total_sin_abono = CF.total_sin_abono+CE.abono
					WHERE
					CF.id_planilla        = CE.id_documento_cruce
					AND CF.id             = CE.id_tabla_referencia
					AND tipo_planilla     = CE.tipo_documento_cruce
					AND CF.id_empresa     = $id_empresa";
			$query = mysql_query($sql,$link);

			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'LE'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono+CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'PA'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono+CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'PCP'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono+CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

		}
		else if ($accion=='eliminar') {
			//ACTUALIZAR LAS FACTURAS DE COMPRA RELACIONADAS AL DOCUMENTO
			$sql = "UPDATE compras_facturas AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'FC'
													GROUP BY id_documento_cruce
												) AS CE
					SET CF.total_factura_sin_abono=CF.total_factura_sin_abono-CE.abono
					WHERE CF.id=CE.id_documento_cruce
						AND CF.cuenta_pago=CE.cuenta
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			//ACTUALIZAR LAS LIQUIDACIONES DE PROVISION RELACIONADAS AL DOCUMENTO
		  	$sql = "UPDATE nomina_liquidacion_provision AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'LP'
													GROUP BY id_documento_cruce
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono-CE.abono
					WHERE CF.id=CE.id_documento_cruce
						AND CF.cuenta_colgaap_cruce=CE.cuenta

						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			//ACTUALIZAR LAS PLANILLAS DE NOMINA RELACIONADAS EN EL COMPROBANTE DE EGRESO
			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'LN'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono-CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			//ACTUALIZAR LAS PLANILLAS DE NOMINA RELACIONADAS EN EL COMPROBANTE DE EGRESO
			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'LE'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono-CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'PA'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono-CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

			$sql = "UPDATE nomina_planillas_empleados_contabilizacion AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta,id_tabla_referencia,tipo_documento_cruce
													FROM comprobante_egreso_cuentas
													WHERE activo=1 AND id_comprobante_egreso='$id' AND id_documento_cruce > 0 AND tipo_documento_cruce = 'PCP'
													GROUP BY id_documento_cruce,cuenta,id_tabla_referencia
												) AS CE
					SET CF.total_sin_abono=CF.total_sin_abono-CE.abono
					WHERE CF.id_planilla=CE.id_documento_cruce
						AND CF.id = CE.id_tabla_referencia
						AND tipo_planilla     = CE.tipo_documento_cruce
						AND CF.id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);

		}
		// echo $sql;
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero_general,$id_tercero,$terceroGeneral,$id_documento_cruce='0',$documento_cruce='',$prefijo_documento_cruce='',$numero_documento_cruce='',$id_tabla_referencia,$link){
		$debe  = (is_nan($debe) || $debe == '')? 0: $debe;
		$haber = (is_nan($haber) || $haber == '')? 0: $haber;
		$saldo_pendiente = ($debe>$haber)? $debe : $haber;

		//VERIFICAR NUEVAMENTE EL MONTO EN DEBE Y HABER PARA VALIDAR QUE CONTENGA UN VALOR PERO QUE SOLO SEA UNO
		if ($debe==0 && $haber == 0) { echo'<script>alert("Aviso\nDebe ingresar un valor numerico debito o credito");</script>'; return; }

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($documento_cruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$opcGrillaContable,$cont,$id_tercero_general,$id_tercero,$cuenta,'guardar',$debe,$haber,$link); }
		else if ($numero_documento_cruce!='') { $numero_documento_cruce=''; }

		$sqlInsert = "INSERT INTO $tablaCuentasNota(
						$idTablaPrincipal,
						id_puc,
						id_documento_cruce,
						tipo_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						id_tabla_referencia,
						debito,
						credito,
						saldo_pendiente,
						id_tercero)
					VALUES('$id',
						'$idPuc',
						'$id_documento_cruce',
						'$documento_cruce',
						'$prefijo_documento_cruce',
						'$numero_documento_cruce',
						'$id_tabla_referencia',
						'$debe',
						'$haber',
						'$saldo_pendiente',
						'$id_tercero')";
		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

		if($lastId > 0){
			// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
			$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$idPuc ";
			$query=mysql_query($sql,$link);
			$centro_costo = mysql_result($query,0,'centro_costo');


			// if ($centro_costo=='Si') {
				// $script = ($cont=='1')? 'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.nextElementSibling.childNodes[1].innerHTML="<img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'>";'
				// 						: 'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.firstChild.nextElementSibling.innerHTML="<img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'>";' ;

			$script =($centro_costo=='Si')? 'document.getElementById("label_cont_'.$cont.'").innerHTML="<div style=\'float:right;\'>'.$cont.'</div><div style=\'float:left;\'><img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'></div>";' : '';
			// }

			$body  = cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrillaContable);

			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
					document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");

					document.getElementById("idInsertCuenta'.$opcGrillaContable.'_'.$cont.'").value            = '.$lastId.'
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display      = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display  = "none";
					document.getElementById("deleteCuenta'.$opcGrillaContable.'_'.$cont.'").style.display      = "block";
					document.getElementById("descripcionCuenta'.$opcGrillaContable.'_'.$cont.'").style.display = "block";

					//llamamos a la funcion para calcular los totales de la nota
					calcTotal'.$opcGrillaContable.'("'.$debe.'","'.$haber.'","agregar");

					//habilitar el boton terminar y nuevo
					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();

					if (typeof(cont)!="undefined") { cont = '.$consecutivo.'; }

					'.$script.'

				</script>'.$body/*.$script*/;

		}
		else{
			echo'<script>
					alert("Error, no se ha almacenado la cuenta en el documento\n si el problema persiste favor comuniquese con la administracion del sistema");
					document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'").parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'"));
				</script>';
		}
	}

	function validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce='',$numero_documento_cruce,$opcGrillaContable,$cont,$id_tercero_general,$id_tercero,$cuenta,$evento,$debe,$haber,$link){

		$script = '';
		$cont2  = $cont;

		//CON LA VARIABLE EVENTO IDENTIFICAMOS SI SE ESTA GUARDANDO O ACTUALIZANDO UNA CUENTA, PARA ASI MOSTRAR O NO UN BLOQUE DE CODIGO Y PARA EL CONTADOR
		if ($evento=='guardar') {
			$script = '(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.($cont++).'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.($cont++).'"));
						contArticulos'.$opcGrillaContable.'--;';
			$cont2  = ($cont-2);
		}

		//VALIDAR QUE TENGA UN NUMERO DE DOCUMENTO
		if ($numero_documento_cruce=='' || $numero_documento_cruce=='0') {
			echo '<script>
					alert("Si relaciona una '.$documento_cruce.', debe ingresar el numero!");
					document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
			exit;
		}

		//VALIDAR QUE EL NUMERO DE LA CUENTA QUE SE ESTA RELACIONANDO CON EL DOCUMENTO EXISTA EN LOS ASIENTOS RELACIONADOS AL DOCUMENTO CRUCE
		$sqlAsientos   = "SELECT id,SUM(debe - haber) AS saldoCuenta
							FROM asientos_colgaap
							WHERE
								id_documento_cruce       = '$id_documento_cruce'
								AND tipo_documento_cruce = '$documento_cruce'
								AND (id_documento=id_documento_cruce
									AND tipo_documento=tipo_documento_cruce
									AND haber>0
									OR id_documento<>id_documento_cruce
									OR tipo_documento<>tipo_documento_cruce)
								AND codigo_cuenta        = '$cuenta'
								AND id_empresa           = '$id_empresa'
								AND activo               = 1
							GROUP BY id_documento_cruce";
		$queryAsientos = mysql_query($sqlAsientos,$link);
		$idAsiento     = mysql_result($queryAsientos,0,'id');
		$saldoCuentaDb = mysql_result($queryAsientos,0,'saldoCuenta');
		$saldoCuenta   = $debe-$haber;

		$absCuenta   = abs($saldoCuenta);
		$absCuentaDb = abs($saldoCuentaDb);

		//SINO EXISTE EL ASIENTO DE ESE DOCUMENTO RELACIONADO
		if ($idAsiento=='') {
			if ($documento_cruce!='PA' && $documento_cruce!='PCP') {
				echo '<script>
						alert(" La cuenta '.$cuenta.' de la '.$documento_cruce.' relacionada no existe en los asientos de ese documento\nPor favor digite una cuenta que genero el documento relacionado");
						'.$script.'
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
					</script>';
				exit;
			}
		}
		else if($absCuentaDb <= 0 && $absCuenta <= 0){
			echo '<script>
					alert("El saldo debito o credito debe ser mayor a 0.");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
		else if($saldoCuentaDb > 0 && $saldoCuenta > 0){
			// SI ES DIFERENTE A UNA LIQUIDACION DE EMPLEADO SE REALIZA LA VALIDACION
			if ($documento_cruce!='LE' && $documento_cruce!='PA' && $documento_cruce!='PCP') {
				echo '<script>
						alert("No se permite debitar y acreditar el mismo registro.");
						'.$script.'
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
					</script>';
				exit;
			}
		}
		else if($absCuenta > $absCuentaDb){
			// SI ES DIFERENTE A UNA LIQUIDACION DE EMPLEADO SE REALIZA LA VALIDACION
			if ($documento_cruce!='LE' && $documento_cruce!='PA' && $documento_cruce!='PCP') {
				echo '<script>
					alert("El valor a cruzar es superior al saldo registrado en el documento ('.$absCuentaDb.').");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
				</script>';
				exit;
			}

		}

		// VALIDAR EL VALOR DEL DOCUMENTO DE NOMINA
		if ($documento_cruce=='LE' || $documento_cruce=='PA' || $documento_cruce=='PCP') {
			$sql = "SELECT id,SUM(total_sin_abono) AS saldoCuenta
					FROM nomina_planillas_empleados_contabilizacion
					WHERE
					id_planilla        = '$id_documento_cruce'
					AND tipo_planilla  = '$documento_cruce'
					AND cuenta_colgaap = '$cuenta'
					AND id_empresa     = '$id_empresa'
					AND activo         = 1
					GROUP BY id_planilla";
			$query         = mysql_query($sql,$link);
			$idAsiento     = mysql_result($query,0,'id');
			$saldoCuentaDb = mysql_result($query,0,'saldoCuenta');
			$saldoCuenta   = $debe-$haber;

			$absCuenta     = $saldoCuenta;
			$absCuentaDb   = $saldoCuentaDb;

			if($absCuentaDb <= 0 && $absCuenta <= 0){
				echo '<script>
						alert("El saldo debito o credito debe ser mayor a 0.");
						'.$script.'
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
					</script>';
				exit;
			}
			else if($absCuenta > $absCuentaDb){
				// SI ES DIFERENTE A UNA LIQUIDACION DE EMPLEADO SE REALIZA LA VALIDACION
					echo '<script>
						alert("El valor a cruzar es superior al saldo registrado en el documento ('.$absCuentaDb.').");
						'.$script.'
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
					</script>';
					exit;

			}
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce='0',$documento_cruce='',$prefijo_documento_cruce='',$numero_documento_cruce='',$id_tercero_general,$cuenta,$id_tabla_referencia,$link){
		$debe  = (is_nan($debe) || $debe == '')? 0: $debe;
		$haber = (is_nan($haber) || $haber == '')? 0: $haber;
		$saldo_pendiente = ($debe>$haber)? $debe : $haber;

		if ($documento_cruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$opcGrillaContable,$cont,$id_tercero_general,$id_tercero,$cuenta,'actualizar',$debe,$haber,$link); }

		$numero_documento_cruce=($documento_cruce!='')? $numero_documento_cruce : '';

		$sqlArticuloAnterior   = "SELECT debito,credito FROM $tablaCuentasNota WHERE id='$idInsertCuenta' AND $idTablaPrincipal='$id'";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);
		$debeAnterior          = mysql_result($queryArticuloAnterior,0,'debito');
		$haberAnterior         = mysql_result($queryArticuloAnterior,0,'credito');

		$sqlUpdateArticulo   = "UPDATE $tablaCuentasNota
								SET id_puc					='$idPuc',
									debito                  ='$debe',
									credito                 ='$haber',
									saldo_pendiente         ='$saldo_pendiente',
									id_tercero              ='$id_tercero',
									id_documento_cruce      ='$id_documento_cruce',
									tipo_documento_cruce    ='$documento_cruce',
									prefijo_documento_cruce ='$prefijo_documento_cruce',
									numero_documento_cruce  ='$numero_documento_cruce',
									id_tabla_referencia     ='$id_tabla_referencia'
								WHERE $idTablaPrincipal=$id AND id=$idInsertCuenta;";
		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);

		if ($queryUpdateArticulo) {
			// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
			$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$idPuc ";
			$query=mysql_query($sql,$link);
			$centro_costo = mysql_result($query,0,'centro_costo');

			$sql = "SELECT id_centro_costos FROM comprobante_egreso_cuentas WHERE activo=1 AND id=$idInsertCuenta;";
			$query = mysql_query($sql,$link);
			$id_centro_costos = mysql_result($query,0,'id_centro_costos');

			// if ($centro_costo=='Si') {
			// 	$script = ($cont==1)? 'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.nextElementSibling.childNodes[1].innerHTML="<img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'>";' :
			// 					'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.firstChild.nextElementSibling.innerHTML="<img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'>";' ;
			// 	$script = 'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.nextElementSibling.childNodes[1].innerHTML="<img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'>";';
			// }
			// else{
			// 	$script = ($cont==1)? 'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.nextElementSibling.childNodes[1].innerHTML="'.$cont.'"' :
			// 						'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.firstChild.nextElementSibling.innerHTML="'.$cont.'"';
			// 	$script = 'document.getElementById("bodyDivArticulosComprobanteEgreso_'.$cont.'").firstChild.nextElementSibling.childNodes[1].innerHTML="'.$cont.'"';
			// }
			$script =($centro_costo=='Si' && ($id_centro_costos=='' || $id_centro_costos==0) )? 'document.getElementById("label_cont_'.$cont.'").innerHTML="<div style=\'float:right;\'>'.$cont.'</div><div style=\'float:left;\'><img src=\'img/warning.png\' title=\'Requiere Centro de Costos\'></div>";' :
																								'document.getElementById("label_cont_'.$cont.'").innerHTML="'.$cont.'";'  ;



			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					//llamamos la funcion para recalcular el costo de la factura
					calcTotal'.$opcGrillaContable.'('.$debeAnterior.','.$haberAnterior.',"eliminar");
					calcTotal'.$opcGrillaContable.'('.$debe.','.$haber.',"agregar");
					'.$script.'
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo la cuenta");  </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION DE LA NOTA =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link){
		//CONSULTAMOS EL DOCUMENTO PARA SABER SI ESTA GENERADO
		$sql   = "SELECT estado,consecutivo,id_tercero FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$estado             = mysql_result($query,0 ,'estado');
		$consecutivo        = mysql_result($query,0 ,'consecutivo');
		$id_tercero_general = mysql_result($query,0 ,'id_tercero');

		if($estado=='3'){
			echo '<script>
					alert("Error!\nEsta nota ya esta cancelada!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else if ($estado=='0' && $consecutivo>0) { $sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";}
		else if ($estado=='0' && ($consecutivo<=0 || $consecutivo=='' )) {
			$sqlUpdate="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";
		}
		else if ($estado=='1') {
			// VALIDAR QUE NO ESTE CRUZADO
			validaComprobanteCruzado($id,$id_empresa,$link);

			//DESCONTABILIZAMOS LA NOTA, ELIMINADO LOS ASIENTOS QUE SE GENERARON A PARTIR DE ELLA
			moverCuentasDocumento($id_empresa,$id_sucursal,$id,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);

			//REGRESAMOS LOS SALDOS DE LOS DOCUMENTOS
			moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'agregar',$link);

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
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','CE','Comprobante de Egreso',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					nueva'.$opcGrillaContable.'();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$id_sucursal,$id_empresa,$tablaPrincipal,$link){

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal'  AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			//CONSULTAR EL CONSECUTIVO PARA MOSTRARLO DtablaPrincipalE TITULO EN LA VENTANA
			$sql         = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal'  AND id_empresa='$id_empresa' ";
			$consecutivo = mysql_result(mysql_query($sql,$link),0,'consecutivo');
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','CE','Comprobante de Egreso',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "comprobante_egreso/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_comprobante_egreso : "'.$idDocumento.'",
							opcGrillaContable     : "'.$opcGrillaContable.'"
						}
					});
					document.getElementById("titleDocuemento'.$opcGrillaContable.'").innerHTML = "Comprobante de Egreso<br>N. '.$consecutivo.'";
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

 	//=========================== FUNCION PARA ACTUALIZAR LA FECHA DE LA NOTA =========================================================================//
 	function actualizarFechaNota($id,$fecha,$tablaPrincipal,$link){
		$sql   = "UPDATE $tablaPrincipal SET fecha_comprobante='$fecha' WHERE id='$id' ";
		$query = mysql_query($sql,$link);

 		if (!$query) { echo '<script>alert("Error!\nNo se actualizo la fecha, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
 	}

 	function ventana_buscar_documento_cruce($id_comprobante_egreso,$cont,$opcGrillaContable,$id_empresa,$id_sucursal,$link){
 		echo'<select class="myfield" name="filtro_tipo_documento" id="filtro_tipo_documento" style="width:180px; margin: 8px 0px 0px 4px;" onChange="carga_filtro_tipo_documento(this.value)">
        		<option value="FC">FC - Factura de Compra</option>
        		<option value="LN">LN - Liquidacion de Nomina</option>
        		<option value="LE">LE - Liquidacion Empleado</option>
        		<option value="PA">PA - Planilla Ajuste de Nomina</option>
        		<option value="PCP">PCP - Planilla consolidacion Provision</option>
    		</select>
    		<script>
				function carga_filtro_tipo_documento(tipo_documento_cruce){

					idTercero = "";

					if(document.getElementById("filtro_terceros")){	filtroTercero  = document.getElementById("filtro_terceros").value; }
					else{ filtroTercero  = "principal"; }

					if(filtroTercero == "principal"){
						idTercero = id_cliente_'.$opcGrillaContable.';
					}

					if (document.getElementById("filtro_tipo_documento")) { tipo_documento_cruce=document.getElementById("filtro_tipo_documento").value; }
					else{ tipo_documento_cruce="FC"; }

					if (document.getElementById("filtro_sucursal_ComprobanteEgreso")) {	filtro_sucursal=document.getElementById("filtro_sucursal_ComprobanteEgreso").value;	}
					else{ filtro_sucursal="'.$id_sucursal.'"; }

					Ext.get("contenedor_buscar_documento_cruce_'.$opcGrillaContable.'").load({
						url     : "comprobante_egreso/bd/grillaDocumentoCruce.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opc                   : "'.$opc.'",
							filtro_sucursal       : filtro_sucursal,
							tipo_documento_cruce  : tipo_documento_cruce,
							cont                  : "'.$cont.'",
							opcGrillaContable     : "'.$opcGrillaContable.'",
							carpeta               : "comprobante_egreso",
							tablaPrincipal        : "nota_contable_general",
							idTablaPrincipal      : "id_nota_general",
							tablaCuentasNota      : "nota_contable_general_cuentas",
							id_comprobante_egreso : "'.$id_comprobante_egreso.'",
							idTercero             : idTercero,
						}
					});
				}
				// carga_filtro_tipo_documento();
			</script>';
 	}

 	function ventana_buscar_sucursal($cont,$id_comprobante_egreso,$opcGrillaContable,$id_sucursal,$link){

 		$MSucursales = user_permisos(1);
		$id_empresa  = $_SESSION['EMPRESA'];
		$id_sucursal = $_SESSION['SUCURSAL'];

		if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
		if($MSucursales == 'true'){ $filtroS = ""; }

		$SQL     = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$id_empresa' $filtroS";
		$consulS = mysql_query($SQL,$link);

 		?>
 		<select name="filtro_sucursal_<?php echo $opcGrillaContable; ?>" id="filtro_sucursal_<?php echo $opcGrillaContable; ?>" style="width:200px;margin: 7px 0px 0px 5px;" onChange="cambia_filtro_<?php echo $opcGrillaContable; ?>()">
	        <?php
				while($rowS=mysql_fetch_array($consulS)){
					$selected = ($rowS['id'] == $id_sucursal)? 'selected': '';
				 	echo '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
				}
	        ?>
	    </select>

	    <script>

	    	setTimeout(function(){ cambia_filtro_<?php echo $opcGrillaContable; ?>() },200);

			//varSelected   = ''	// Cambia el selected de la bodega
			//imprimeVarPhp = ''; 	// Imprime variables a enviar al renderizar

			function cambia_filtro_<?php echo $opcGrillaContable; ?>(){

				idTercero = "";

				if(document.getElementById("filtro_terceros")){	filtroTercero  = document.getElementById("filtro_terceros").value; }
				else{ filtroTercero  = 'principal'; }

				if(filtroTercero == "principal"){
					idTercero = id_cliente_<?php echo $opcGrillaContable; ?>;
				}
                //FILTRO TIPO DOCUMENTO
				if (document.getElementById('filtro_tipo_documento')) { tipo_documento_cruce=document.getElementById('filtro_tipo_documento').value; }
				else{ tipo_documento_cruce='FC'; }

				//FILTRO SUCURSAL
				if (document.getElementById("filtro_sucursal_ComprobanteEgreso")) {	filtro_sucursal=document.getElementById("filtro_sucursal_ComprobanteEgreso").value;	}
				else{ filtro_sucursal="<?php echo $id_sucursal; ?>"; }
				// var tipo_documento_cruce=(typeof(document.getElementById('filtro_tipo_documento').value)=="undefined")? "<?php echo $documento_cruce; ?>" : document.getElementById('filtro_tipo_documento').value ;

				Ext.get('contenedor_buscar_documento_cruce_<?php echo $opcGrillaContable; ?>').load({
					url     : "comprobante_egreso/bd/grillaDocumentoCruce.php",
					scripts : true,
					nocache : true,
					params  :
					{
						cont                  : "<?php echo $cont; ?>",
						opcGrillaContable     : "<?php echo $opcGrillaContable; ?>",
						idTercero             : idTercero,
						filtro_sucursal       : filtro_sucursal,
						tipo_documento_cruce  : tipo_documento_cruce,
						id_comprobante_egreso : "<?php echo $id_comprobante_egreso; ?>"

					}
				});
			}

			//cambia_filtro_<?php echo $opcGrillaContable; ?>();

		</script>

 		<?php
 	}

 	function ventana_buscar_terceros($cont,$id_comprobante_egreso,$id_sucursal,$opcGrillaContable){
 		echo'<select name="filtro_terceros" id="filtro_terceros" style="width:100px; margin: 7px 0px 0px 5px;height:25px" onChange="carga_filtro_tercero()">
        		<option value="principal" selected>Principal</option>
        		<option value="todos">Todos</option>
    		</select>
    		<script>
    		    setTimeout(function(){ carga_filtro_tercero() },200);
				function carga_filtro_tercero(){

					idTercero = "";

					if(document.getElementById("filtro_terceros")){	filtroTercero  = document.getElementById("filtro_terceros").value; }
					else{ filtroTercero  = "principal"; }

					if(filtroTercero == "principal"){
						idTercero = id_cliente_'.$opcGrillaContable.';
					}

					if (document.getElementById("filtro_tipo_documento")) { tipo_documento_cruce=document.getElementById("filtro_tipo_documento").value; }
					else{ tipo_documento_cruce="FC"; }

					if (document.getElementById("filtro_sucursal_ComprobanteEgreso")) {	filtro_sucursal=document.getElementById("filtro_sucursal_ComprobanteEgreso").value;	}
					else{ filtro_sucursal="'.$id_sucursal.'"; }

					Ext.get("contenedor_buscar_documento_cruce_'.$opcGrillaContable.'").load({
					 	url     : "comprobante_egreso/bd/grillaDocumentoCruce.php",
					 	scripts : true,
					 	nocache : true,
						params  :
						{
							cont                  : "'.$cont.'",
							tipo_documento_cruce  : tipo_documento_cruce,
							opcGrillaContable     : "'.$opcGrillaContable.'",
							filtro_sucursal       : filtro_sucursal,
							idTercero             : idTercero,
							id_comprobante_egreso : "'.$id_comprobante_egreso.'"
						}
					});
				}
				//carga_filtro_tercero();
			</script>';
 	}

 	//=========================== FUNCION PARA GUARDAR EL NUMERO DE CHEQUE DEL COMPROBANTE ===========================================================//
	function guardarNumeroCheque($numeroCheque,$id,$link){
		$sql   = "UPDATE comprobante_egreso SET numero_cheque='$numeroCheque' WHERE id=$id AND id_empresa='$_SESSION[EMPRESA]'";
		$query = mysql_query($sql,$link);

		if (!$query) { echo '<script>alert("Error!\nNo se guardo el numero del cheque, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
	}

	function updateCuentaPago($idConfiguracion,$id,$id_empresa,$link){
 		$sql   = "UPDATE comprobante_egreso SET id_configuracion_cuenta='$idConfiguracion' WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
 	}

 	function updateFlujoEfectivo($idFlujoEfectivo,$flujo_efectivo,$id,$id_empresa,$link){
 		$sql   = "UPDATE comprobante_egreso SET flujo_efectivo='$flujo_efectivo', id_flujo_efectivo='$idFlujoEfectivo' WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
 	}

	function updateDisponibleArchivoPlano($disponible,$id,$id_empresa,$mysql){
 		$sql   = "UPDATE comprobante_egreso SET disponible_archivo_plano='$disponible' WHERE id='$id' AND activo = 1 AND id_empresa = '$id_empresa'";
		$query = $mysql->query($sql,$mysql->link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$link){
		$sql   = "SELECT estado,consecutivo FROM comprobante_egreso WHERE id=$id_documento";
		$query = mysql_query($sql,$link);

		$estado      = mysql_result($query,0,'estado');
		$consecutivo = mysql_result($query,0,'consecutivo');

		if ($estado==1) { $mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el'; }
		else if ($estado==2) { $mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el'; }
		else if ($estado==3) { $mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el'; }

		if ($estado>0) {
			echo'<script>

						alert("'.$mensaje.'");

						if (document.getElementById("Win_Ventana_descripcion_cuenta")) {
							Win_Ventana_descripcion_cuenta.close();
						}

						Ext.get("contenedor_ComprobanteEgreso").load({
 		        		    url     : "comprobante_egreso/bd/grillaContableBloqueada.php",
 		        		    scripts : true,
 		        		    nocache : true,
 		        		    params  :
 		        		    {
								opcGrillaContable     : "ComprobanteEgreso",
								id_comprobante_egreso : '.$id_documento.',
 		        		    }
 		        		});

						document.getElementById("titleDocuementoComprobanteEgreso").innerHTML="Comprobante de Egreso<br>N. '.$consecutivo.'";

					</script>';
			exit;
		}
	}

	//FUNCION PARA VALIDAR QUE EL COMPROBANTE NO ESTE CRUZADO
	function validaComprobanteCruzado($id_documento,$id_empresa,$link){
		$sql     = "SELECT id FROM comprobante_egreso_cuentas WHERE activo=1 AND id_comprobante_egreso=$id_documento";
		$query   = mysql_query($sql,$link);
		$whereId = '';

		while ($row=mysql_fetch_array($query)) {
			$whereId=($whereId=='')? ' CFC.id_tabla_referencia='.$row['id'] : ' OR CFC.id_tabla_referencia='.$row['id'] ;
		}

		$sql="SELECT
					CFC.id,
					CF.consecutivo,
					CFC.id_factura_compra
				FROM
					compras_facturas AS CF,
					compras_facturas_cuentas AS CFC
				WHERE
					CFC.activo = 1
					AND ($whereId)
					AND CF.id=CFC.id_factura_compra
					AND CF.estado=1
				GROUP BY CF.id";
		$query = mysql_query($sql,$link);
		$cont  = 0;
		while ($row=mysql_fetch_array($query)) {
			$cont++;
			$mensaje.='-> FC - '.$row['consecutivo'].' \n';
		}

		if ($cont>0) {
			echo '<script>
					alert("No se puede modificar el comprobante por que esta cruzado en las siguientes facturas:\n'.$mensaje.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
	}

	function validarCuenta($cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM puc WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$campoFecha,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql = "SELECT $campoFecha AS fecha FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query = mysql_query($sql,$link);
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
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}
	}

	function ventanaVerImagenDocumentoTerceros($nombreImage,$nombreDocumento,$type,$id_host){
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/comprobante_egreso/'.$nombreImage)){
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/comprobante_egreso/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($type != 'pdf' && $type != 'PDF'){
			echo   '<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
						<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
							<a href="'.$url.'" download="'.$nombreDocumento.'">
								<img src="'.$url.'" style="">
							</a>
						</div>
					</div>';
		}
		else{
			echo   '<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
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
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/comprobante_egreso/'.$nombre)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/comprobante_egreso/'.$nombre;
		}
		else{
			$url = '';
		}

		list($ancho, $alto, $tipo, $atributos) = getimagesize($url);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;
		echo json_encode($size);
	}

	function eliminarArchivoAdjunto($id,$nombre,$id_host,$mysql){
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/comprobante_egreso/'.$nombre)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/comprobante_egreso/'.$nombre;
		}
		else{
			$url = '';
		}

		if(unlink($url)){
			$sql = "DELETE FROM comprobante_egreso_archivos_adjuntos WHERE activo = 1 AND id = $id";
			$query = $mysql->query($sql,$mysql->link);
			if($query){
				echo   '<script>
							var element = document.getElementById("archivo_adjunto_'.$id.'");
							element.parentNode.removeChild(element);
							MyLoading2("off",{texto:"Registro Eliminado"});
						</script>';
			}
			else{
				echo   '<script>
							MyLoading2("off",{icono:"fail",texto:"Error! No se elimino el registro en base de datos",duracion:2500});
							// alert("Error!\nSe elimino el archivo, pero no el registro en base de datos");
						</script>';
			}
		}
		else{
			echo   '<script>
						MyLoading2("off",{icono:"fail",texto:"Error! no se elimino el archivo adjunto",duracion:2500});
						// alert("Error!\nNo se Elimino el Archivo Adjunto");
					</script>';
		}
	}

	function mostrarAlmacenamiento(){
		if($_SERVER['SERVER_NAME'] != 'erp.plataforma.co'){
			$size       = getFolderSize($_SESSION['ID_HOST'],'../../../../');
			$porcentaje = $size * 100 / $_SESSION['ALMACENAMIENTO'];
			$proporcion = 400 * $porcentaje / 100;
		}
		else{
			$proporcion = 0;
		}

		$title = "INFORMACION DE ALMACENAMIENTO";

		if($size >= $_SESSION['ALMACENAMIENTO']){
			$title = "NO HAY ESPACIO DE ALMACENAMIENTO";
		}

		echo   '<div class="content-sin-espacio">
				  	<div class="title-sin-espacio" id="label_almacenamiento">'.$title.'</div>
				  	<div class="espacio-disponible">
				  	  	<div class="espacio-no-disponible" style="width:'.$proporcion.'">
				  	  	</div>
				  	</div>
				  	<div class="content-label">
				  	  	<table class="table-espace">
				  	  		<tr>
				  	  			<td data-color="asignado">&nbsp;</td><td>&nbsp;&nbsp;Espacio Asignado</td><td>'.number_format($_SESSION['ALMACENAMIENTO']).'MB</td>
				  	  		</tr>
				  	  		<tr>
				  	  			<td data-color="ocupado">&nbsp;</td><td>&nbsp;&nbsp;Espacio Ocupado</td><td>'.number_format($size,2).'MB</td>
				  	  		</tr>
				  	  		<tr>
				  	  			<td data-color="disponible">&nbsp;</td><td>&nbsp;&nbsp;Espacio Disponible</td><td>'.number_format( ($_SESSION['ALMACENAMIENTO']-$size),2).'MB</td>
				  	  		</tr>
				  	  	</table>
				  	</div>
			    </div>';
	}

?>
