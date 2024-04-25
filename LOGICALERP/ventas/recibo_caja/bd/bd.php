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
		if ($opc<>'actualizarFechaNota') {
			verificaCierre($id,'fecha_recibo',$tablaPrincipal,$id_empresa,$link);
		}
		if ($opc<>'cancelarDocumento'&& $opc<>'restaurarDocumento' && $opc<>'modificarDocumentoGenerado'&& $opc<>'updatecuentaPago'&& $opc<>'updateFlujoEfectivo' && $opc<>'ventanaObservacionCuenta' && $opc<>'ventanaViewDocumento') {
			verificaEstadoDocumento($id,$link);
		}

	}

	switch ($opc) {
		case 'updateTerceroHead':
			updateTerceroHead($id,$codTercero,$id_empresa,$opcGrillaContable,$inputId,$link);
			break;

		case 'buscarCuenta':
			buscarCuenta($campo,$cuenta,$idCuenta,$id_empresa,$idCliente,$opcGrillaContable,$link);
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
			validaNota($id_empresa,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_tercero,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id_empresa,$id_sucursal,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$id_tercero,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id_empresa,$id_sucursal,$id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'guardarCuenta':
			guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero_general,$id_tercero,$terceroGeneral,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$link);
			break;

		case 'actualizaCuenta':
			actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$id_tercero_general,$cuenta,$link);
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
			ventana_buscar_documento_cruce($cont,$opcGrillaContable,$id_recibo_caja);
			break;

		case 'ventana_buscar_terceros':
			ventana_buscar_terceros($cont,$opcGrillaContable,$id_recibo_caja);
			break;

		case 'ventana_buscar_sucursal':
			ventana_buscar_sucursal($cont,$opcGrillaContable,$id_recibo_caja,$link);
			break;

		case 'ventana_add_delete_documentos_cruce':
			ventana_add_delete_documentos_cruce($contArticulos,$opcGrilla,$id_documento);
			break;

		case 'updatecuentaPago':
			verificaEstadoDocumento($idReciboCaja,$link);
			updatecuentaPago($idConfiguracion,$idReciboCaja,$id_empresa,$link);
			break;

		case 'updateFlujoEfectivo':
			verificaEstadoDocumento($idReciboCaja,$link);
			updateFlujoEfectivo($idFlujoEfectivo,$flujo_efectivo,$idReciboCaja,$id_empresa,$link);
			break;

		case 'validarCuenta':
			validarCuenta($cuenta,$id_empresa,$link);
			break;

		case 'deleteDocumentoReciboCaja':
		  deleteDocumentoReciboCaja($id_host,$idDocumento,$nombre,$ext,$link);
		  break;

		case "downloadFile":
			downloadFile($nameFile,$ext,$id,$id_empresa,$id_host);
			break;

		case "consultaSizeDocumento":
			consultaSizeDocumento($nameFile,$ext,$id,$id_host);
			break;

		case "ventanaViewDocumento":
			ventanaViewDocumento($nameFile,$ext,$id,$id_host);
			break;
	}

	//=================================// FUNCION PARA BUSCAR UN CLIENTE //=================================//
	//******************************************************************************************************//
	function updateTerceroHead($id,$codTercero,$id_empresa,$opcGrillaContable,$inputId,$link){

		//CONSULTA EL RECIBO DE CAJA
		$sqlRC   = "SELECT COUNT(id) AS contRC,id_tercero,codigo_tercero,nit_tercero,tercero,observacion,estado FROM recibo_caja WHERE id='$id'";
		$queryRC = mysql_query($sqlRC,$link);

		$contRC        = mysql_result($queryRC, 0, 'contRC');
		$id_terceroRC  = mysql_result($queryRC, 0, 'id_tercero');
		$codigoRC      = mysql_result($queryRC, 0, 'codigo_tercero');
		$nitRC         = mysql_result($queryRC, 0, 'nit_tercero');
		$nombreRC      = mysql_result($queryRC, 0, 'tercero');
		$observacionRC = mysql_result($queryRC, 0, 'observacion');
		$estadoRC      = mysql_result($queryRC, 0, 'estado');

		if($contRC == 0 || is_nan($contRC)){ echo '<script>alert("Aviso,\nNo se encontro informacion sobre el recibo de caja!")</script>'; exit; }

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
		if($id_tercero > 0 && $id_terceroRC != $id_tercero){
			$sqlUpdate = "UPDATE recibo_caja
						SET id_tercero     = '$id_tercero',
							tercero        = '$nombre',
							codigo_tercero = '$codigo',
							nit_tercero    = '$nit'
						WHERE id='$id'";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			$sqlDeleteInventario    = "DELETE FROM recibo_caja_cuentas WHERE id_recibo_caja = '$id'";
			$queryDeleteInventario  = mysql_query($sqlDeleteInventario,$link);

			echo '<script>
					debitoAcumulado'.$opcGrillaContable.'  = 0.00;
					creditoAcumulado'.$opcGrillaContable.' = 0.00;
					total'.$opcGrillaContable.'            = 0.00;

					subtotalDebito'.$opcGrillaContable.'  = 0.00;
					subtotalCredito'.$opcGrillaContable.' = 0.00;

					document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigo.'";
					document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
					document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombre.'";

					id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
					nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
					nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";
					codigoCliente'.$opcGrillaContable.' = "'.$codigo.'";
					contArticulos'.$opcGrillaContable.' = 1;
				</script>'.cargaHeadInsertUnidadesConTercero('return',1,$opcGrillaContable);
		}
		else{
			include("functions_body_article.php");

			echo'<script>
					debitoAcumulado'.$opcGrillaContable.'  = 0.00;
					creditoAcumulado'.$opcGrillaContable.' = 0.00;
					total'.$opcGrillaContable.'            = 0.00;

					subtotalDebito'.$opcGrillaContable.'  = 0.00;
					subtotalCredito'.$opcGrillaContable.' = 0.00;

					document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigoRC.'";
					document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nitRC.'";
					document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombreRC.'";

					id_cliente_'.$opcGrillaContable.'   = "'.$id_terceroRC.'";
					nitCliente'.$opcGrillaContable.'    = "'.$nitRC.'";
					nombreCliente'.$opcGrillaContable.' = "'.$nombreRC.'";
					codigoCliente'.$opcGrillaContable.' = "'.$codigoRC.'";

					'.$mensaje.'
				</script>'.cargaArticulosSaveConTercero($id,$observacionRC,$estadoRC,$opcGrillaContable,'recibo_caja_cuentas','id_recibo_caja',$link);
		}
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="labelGrilla" style="width:40px !important;"></div>
							<div class="labelGrilla" style="width:95px">Cuenta</div>
							<div class="labelGrilla" style="width:30%">Tercero</div>
							<div class="labelGrillaDocCruce">Documento Cruce</div>
							<div class="labelGrilla">Debito</div>
							<div class="labelGrilla">Credito</div>
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
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
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
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL CUENTA COBRO</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotalDebito'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalDebito'.$opcGrillaContable.').toFixed('.$_SESSION['DECIMALESMONEDA'].');
					document.getElementById("subtotalCredito'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalCredito'.$opcGrillaContable.').toFixed('.$_SESSION['DECIMALESMONEDA'].');
					document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML    = parseFloat(total'.$opcGrillaContable.').toFixed('.$_SESSION['DECIMALESMONEDA'].');
				</script>';

		if($formaConsulta=='return'){ return $head; }
		else{ echo $head; }
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;" id="label_cont_'.$opcGrillaContable.'_'.$cont.'">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campoGrilla" style="width:95px;">
					<input type="text" style="text-align:left;" id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);"/>
				</div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoGrilla" style="width:30%;"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" title="Buscar Tercero" id="imgBuscarTercero_'.$cont.'"  onclick="buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')"/>
				</div>

				<div class="campoGrilla" style="width:5%">
					<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly/>
				 </div>

				<div class="campoGrilla">
					<input title="Prefijo" type="text" id="prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly class="inputPrefijoFacturaReciboCaja"  style="text-align:left;width:41%;" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'mayuscula\',\''.$cont.'\');"  />
					-
					<input title="Numero" type="text" id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly class="inputNumeroFacturaReciboCaja" style="text-align:left;width:48%;" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');" />
				</div>
				<div class="iconBuscarArticulo">
					<img onclick="ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoGrilla"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');"/></div>
				<div class="campoGrilla"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/></div>

				<div style="float:right; min-width:80px;" class="btnsGrilla">
					<div onclick="guardarNewCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Cuenta"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaObservacionCuenta'.$opcGrillaContable.'('.$cont.')" id="descripcionCuenta'.$opcGrillaContable.'_'.$cont.'" title="observaciones." style="display:none;"><img src="img/config16.png"/></div>
					<div onclick="deleteCuenta'.$opcGrillaContable.'('.$cont.')" id="deleteCuenta'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Cuenta" style=" display:none;"><img src="img/delete.png"/></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrillaContable.'_'.$cont.'" value="0"/>
				<input type="hidden" id="idInsertCuenta'.$opcGrillaContable.'_'.$cont.'" value="0"/>
				<input type="hidden" id="idTercero'.$opcGrillaContable.'_'.$cont.'" value="0"/>
				<input type="hidden" id="idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" value="0"/>

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
			$mensaje = 'Exite(n) una(s)   cuenta(s) de esta, con 8 digitos, no se puede utilizar esta de 6\nDigite apartir de 8 digitos para continuar';
		}
		else if (strlen ($cuenta)>7 && strlen ($cuenta)<9) {
			$where   = ' codigo_cuenta =  LEFT(\''.$cuenta.'\',6) ';
			$mensaje = 'Exite(n) una(s) cuenta(s) de esta, con 6 digitos, no se puede utilizar esta de 8\nDigite solo de 6 digitos para continuar';
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
		$sql         = "SELECT id_cuenta,cuenta FROM asientos_colgaap WHERE codigo_cuenta = '$cuenta' AND id_empresa=$id_empresa";
		$query       = mysql_query($sql,$link);
		$id_cuenta   = mysql_result($query,0, 'id_cuenta');
		$descripcion = mysql_result($query,0, 'cuenta');

		if ($descripcion != '') {
			echo '<script>
						document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value = "'.$id_cuenta.'";
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


			$sqlArticulo = "SELECT id,descripcion FROM puc WHERE cuenta='$cuenta' AND (LENGTH(cuenta)>5) AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$query       = mysql_query($sqlArticulo,$link);
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
						document.getElementById("idCuenta'.$opcGrillaContable.'_'.$contFila.'").value  ="0";
						document.getElementById("debito'.$opcGrillaContable.'_'.$contFila.'").value      ="";
					</script>';
			}
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sql   = "SELECT id_puc,cuenta,debito,prefijo_documento_cruce,numero_documento_cruce,tipo_documento_cruce,tercero FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuentaInsert' ";
		$query = mysql_query($sql,$link);

		$idCuenta    = mysql_result($query,0,'id_puc');
		$cuenta      = mysql_result($query,0,'cuenta');
		$descripcion = mysql_result($query,0,'descripcion');
		$debe        = mysql_result($query,0,'debito');
		$tercero     = mysql_result($query,0,'tercero');
		$tipo_documento_cruce    = mysql_result($query,0,'tipo_documento_cruce');
		$prefijo_documento_cruce = mysql_result($query,0,'prefijo_documento_cruce');
		$numero_documento_cruce  = (mysql_result($query,0,'numero_documento_cruce')==0)? '' : mysql_result($query,0,'numero_documento_cruce') ;

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
				document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value           = "'.$tipo_documento_cruce.'";
				document.getElementById("prefijoDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value     = "'.$prefijo_documento_cruce.'";
				document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value     = "'.$numero_documento_cruce.'";
				document.getElementById("debito'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$debe.'";
				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
			</script>';

	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteCuenta($cont,$id,$idCuenta,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sqlCuenta   = "SELECT debito,credito FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuenta'";
		$queryCuenta = mysql_query($sqlCuenta,$link);
		$debe  = mysql_result($queryCuenta, 0, 'debito');
		$haber = mysql_result($queryCuenta, 0, 'credito');

		$sqlDelete   = "DELETE FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuenta'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar la cuenta, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo'<script>
					calcTotal'.$opcGrillaContable.'("'.$debe.'","'.$haber.'","eliminar");
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
				</script>';
		}
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaObservacionCuenta($cont,$idCuenta,$id,$opcGrillaContable,$idTablaPrincipal,$tablaCuentasNota,$readonly,$id_empresa,$link){
		$placeHolder = ($readonly == '')? "Escriba aqui...": "";
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
				$btn_ccos=($id_centro_costos>0)? '<img src="../inventario/img/false_inv.png" style="cursor:pointer;width:16px;height:16px;" title="Eliminar Centro Costos" onclick="eliminarCentroCostos'.$opcGrillaContable.'('.$idCuenta.','.$cont.')" id="imgCentroCostos">'
												: '<img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta" onclick="ventanaBuscarCentroCostos'.$opcGrillaContable.'('.$idCuenta.','.$cont.')" id="imgCentroCostos">' ;
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

		$sql="UPDATE recibo_caja_cuentas SET id_centro_costos='$id_centro_costos'
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

	//=======================================// FUNCION PARA TERMINAR 'GENERAR' LA NOTA //=======================================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id_empresa,$id_sucursal,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$id_tercero,$link){
		// VALIDAR QUE EL RECIBO DE CAJA TENGA UNA CUENTA CRUZE DE CABECERA SELECCIONADA
		$sql="SELECT id_configuracion_cuenta FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id=$id ";
		$query=mysql_query($sql,$link);
		$id_row=mysql_result($query,0,'id_configuracion_cuenta');
		if ($id_row==0 || $id_row=='') {
			echo '<script>
					alert("Debe seleccionar la cuenta de cabecera del documento!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$fecha = date("Y-m-d");
		$sql   = "UPDATE $tablaPrincipal SET estado=1,fecha_generado='$fecha' WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);
		if (!$query){
			echo'<script>
					alert("Error!\nNo se pudo actualizar la nota, vuelva a intentarlo\nSi el problema persite comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//RESTAMOS SALDOS DE LOS DOCUMENTOS
		moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'eliminar',$link);

	 	//MOVEMOS LAS CUENTAS
		moverCuentasDocumento($id_empresa,$id_sucursal,$id,$tablaCuentasNota,$idTablaPrincipal,'agregar',$id_tercero,$link);

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','RC','Recibo de Caja',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

		//CONSULTAR EL CONSECUTIVO PARA MOSTRARLO DtablaPrincipalE TITULO EN LA VENTANA
		$sql         = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa'";
		$consecutivo = mysql_result(mysql_query($sql,$link),0,'consecutivo');

	   	echo'<script>
   				Ext.get("contenedor_ReciboCaja").load({
 		            url     : "recibo_caja/bd/grillaContableBloqueada.php",
 		            scripts : true,
 		            nocache : true,
 		            params  :
 		            {
						opcGrillaContable : "ReciboCaja",
						id_recibo_caja    : '.$id.'
 		            }
 		        });

				document.getElementById("titleDocumentoReciboCaja").innerHTML="Recibo de Caja<br>N. '.$consecutivo.'";
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
	}

	//============================ FUNCION PARA VALIDAR LA NOTA Y LLAMAR LA FUNCION TERMINAR PARA GENERARLA =========================================================================//
	function validaNota($id_empresa,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_tercero1,$link){
		$sqlConsulNota   = "SELECT id_tercero,tercero,fecha_recibo,cuenta,cuenta_niif FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_sucursal='$id_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryConsulNota = mysql_query($sqlConsulNota,$link);

		$id_tercero2 = mysql_result($queryConsulNota,0,'id_tercero');
		$tercero     = mysql_result($queryConsulNota,0,'tercero');
		$fecha_nota  = mysql_result($queryConsulNota,0,'fecha_recibo');

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
		// VALIDAR QUE SI TIENE CUENTAS CON CENTRO DE COSTOS ESTE TENGA YA UNO
		$sql="SELECT RC.id_puc,RC.cuenta,RC.id_centro_costos,RC.centro_costos,puc.centro_costo FROM recibo_caja_cuentas AS RC INNER JOIN puc ON puc.id=RC.id_puc WHERE RC.activo = 1 AND RC.id_recibo_caja = $id ";
		$query = mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$centro_costo     = $row['centro_costo'];
			$cuenta           = $row['cuenta'];
			$id_centro_costos = $row['id_centro_costos'];

			if ($centro_costo=='Si' && ($id_centro_costos=='' || $id_centro_costos=='0')) {
				echo '<script>
						alert("La cuenta '.$cuenta.' no tiene centro de costo!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
		}



		//UNA VEZ QUE RECORRIMOS LAS CUENTAS Y SE VALIDO QUE ESTUVIERAN CORRECTAMENTE INSERTADAS, Y SE TIENE EL ACUMULADO DEL DEBITO Y DEL CREDITO, VERIFICAMOS QUE ESTE BALACEADA LA NOTA, ES DECIR QUE LA DIFERENCIA
		//ENTRE EL DEBITO-CREDITO SEA IGUAL A CERO, SI NO ES IGUAL A CERO ENTONCES NO ESTA BALANCEADA LA NOTA Y NO SE PUEDE GENERAR
		$sqlConsulNotaGenerada   = "SELECT COUNT(id) AS cont
									FROM $tablaPrincipal
									WHERE fecha_generado>='$fecha_buscar' AND activo=1 AND estado=1 AND id_sucursal='$id_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryConsulNotaGenerada = mysql_query($sqlConsulNotaGenerada,$link);

		$cont=mysql_result($queryConsulNotaGenerada,0,'cont');
		//SI CONT ES MAYOR A CERO, HAY NOTAS GENERADAS EN EL MES SIGUIENTE, ASI QUE SE ADVERTIRA AL USUARIO
		if ($cont>0) {
			echo'<script>
					if (confirm("Aviso!\nExiten '.$cont.' notas creadas del mes siguiente a la fecha de la nota!\nSi continua no coincidara el consecutivo con el mes\nDesea continuar de todos modos?")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						generarReciboCaja();
					}
				</script>';
			return;
		}
		else{
			echo '<script>
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					cargando_documentos("Generando Documento...","");
				</script>';
			moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero2,'validar',$link);
			terminarGenerar($id_empresa,$id_sucursal,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero2,$id_tercero1,$link);
		}
	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($id_empresa,$id_sucursal,$idDocumento,$tablaCuentasNota,$idTablaPrincipal,$accion,$id_tercero,$link){

		if ($accion=='agregar') {
			$sqlNotaGeneral   = "SELECT consecutivo,tercero,fecha_recibo,id_tercero,cuenta,cuenta_niif,id_flujo_efectivo,flujo_efectivo FROM recibo_caja WHERE activo=1 AND id='$idDocumento'";
			$queryNotaGeneral = mysql_query($sqlNotaGeneral,$link);

			$consecutivoNota  = mysql_result($queryNotaGeneral,0,'consecutivo');
			$id_tercero       = mysql_result($queryNotaGeneral,0,'id_tercero');
			$tercero          = mysql_result($queryNotaGeneral,0,'tercero');
			$cuenta           = mysql_result($queryNotaGeneral,0,'cuenta');
			$cuentaNiif       = mysql_result($queryNotaGeneral,0,'cuenta_niif');
			$fechaComprobante = mysql_result($queryNotaGeneral,0,'fecha_recibo');
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
				$documento_cruce   = ($documento_cruce=='' || $documento_cruce=='0')? $consecutivoNota : $documento_cruce;
				$row['id_tercero'] = ($row['id_tercero']=='0' || $row['id_tercero']=='')? $id_tercero : $row['id_tercero'];

				$saldoDebito  += (is_nan($row['debito']) || $row['debito'] == '')? 0: $row['debito'];
				$saldoCredito += (is_nan($row['credito']) || $row['credito'] == '')? 0: $row['credito'];

				$row['id_documento_cruce'] = ($row['id_documento_cruce'] =='' || $row['id_documento_cruce'] ==0)? $idDocumento : $row['id_documento_cruce'];
				$row['tipo_documento_cruce'] = ($row['tipo_documento_cruce']=='')? 'RC' : $row['tipo_documento_cruce'];

				$sucursalCuenta = $id_sucursal;
				// if($row['id_documento_cruce'] > 0 && $row['tipo_documento_cruce']== 'FV'){
				// 	$sqlSucursalFv   = "SELECT COUNT(id) AS contSucursal, id_sucursal FROM ventas_facturas WHERE id_empresa='$id_empresa' AND activo=1 AND id='$row[id_documento_cruce]' GROUP BY id LIMIT 0,1";
				// 	$querySucursalFv = mysql_query($sqlSucursalFv,$link);

				// 	$contSucursal = mysql_result($querySucursalFv, 0, 'contSucursal');

				// 	if($contSucursal>0) $sucursalCuenta = mysql_result($querySucursalFv, 0, 'id_sucursal');
				// }

				// echo $sucursalCuenta; exit;
				//============================== PARTIDA ================================//
				$valuesInsertColgaap .= "($idDocumento,
										$consecutivoNota,
										'RC',
										'Recibo de Caja',
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
										0,
										'',
										'$row[observaciones]'),";

				$valuesInsertNiif .= "($idDocumento,
										$consecutivoNota,
										'RC',
										'Recibo de Caja',
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
										0,
										'',
										'$row[observaciones]'),";

			}
			$saldoDebito = round($saldoDebito,$_SESSION['DECIMALESMONEDA']);
			$saldoCredito = round($saldoCredito,$_SESSION['DECIMALESMONEDA']);
			if($saldoDebito > $saldoCredito){
				echo '<script>
						alert("Aviso\nEl saldo debito no puede ser mayor al credito!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			$saldoCuentaCruce = $saldoCredito - $saldoDebito;

			//============================= CONTRA PARTIDA =============================//
			$valuesInsertColgaap .= "($idDocumento,
									$consecutivoNota,
									'RC',
									'Recibo de Caja',
									'$idDocumento',
									'RC',
									'$consecutivoNota',
									'$saldoCuentaCruce',
									0,
									'$cuenta',
									$id_sucursal,
									$id_empresa,
									'$id_tercero',
									'$fechaComprobante',
									'',
									'$idFlujoEfectivo',
									'$flujoEfectivo',
									''),";

			$valuesInsertNiif .= "($idDocumento,
									$consecutivoNota,
									'RC',
									'Recibo de Caja',
									'$idDocumento',
									'RC',
									'$consecutivoNota',
									'$saldoCuentaCruce',
									0,
									'$cuentaNiif',
									$id_sucursal,
									$id_empresa,
									'$id_tercero',
									'$fechaComprobante',
									'',
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
				exit;
			}

			contabilizacionSimultanea($idDocumento,'RC',$id_sucursal,$id_empresa,$link);

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
			if (!$queryInsert) {
				echo $sql.'<script>
							alert("Error!\nNo se genero el asiento contable, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
				exit;
			}
		}
		else if ($accion=='eliminar') {
			$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$id_empresa' AND tipo_documento='RC' ";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){
				echo '<script>
						alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$id_empresa' AND tipo_documento='RC' ";
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

	//=============================================== 	MODIFICAR DOCUMENTO YA GENERADO  ===============================================================//
	function modificarDocumentoGenerado($id_empresa,$id_sucursal,$idDocumento,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){

		$sql   = "SELECT consecutivo,id_tercero FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='".$_SESSION['EMPRESA']."' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo        = mysql_result($query,0 ,'consecutivo');
		$id_tercero_general = mysql_result($query,0 ,'id_tercero');

		//REGRESAMOS LOS SALDOS DE LOS DOCUMENTOS
		moverSaldoDocumentosRelacionados($idDocumento,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'agregar',$link);

		//PARA MODIFICAR EL DOCUMENTO PRIMERO DEBEMOS DESCONTABILIZARLO Y LUEGO REGRESARLO A ESTADO=0
		moverCuentasDocumento($id_empresa,$id_sucursal,$idDocumento,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);

		//YA QUE SE DIERON DE BAJA LOS ASIENTOS GENERADOS POR LA NOTA, PROCEDEMOS A ACTUALIZAR SU ESTADO A CERO
		$sql          = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
		$query_update = mysql_query($sql,$link);

		if($query_update){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								 VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','RC','Recibo de Caja',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "recibo_caja/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_recibo_caja    : "'.$idDocumento.'"
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
				</script>';
		}
	}

	//============================================ FUNCION PARA MOVER LOS SALDOS DE LOS DOCUMENTO RELACIONADOS EN LAS CUENTAS ======================================================//
	function moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,$accion,$link){

		//VALIDAR SALDOS GENERAR RECIVO DE CAJA
		if ($accion=='validar') {
			$whereIdFactura = '';

			$sql   = "SELECT id,SUM(credito) AS abono,id_documento_cruce,tipo_documento_cruce
						FROM $tablaCuentasNota
						WHERE activo=1
							AND $idTablaPrincipal='$id'
							AND tipo_documento_cruce<>''
						GROUP BY id_documento_cruce,tipo_documento_cruce";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)){

				if ($row['tipo_documento_cruce'] == 'FV') {
					$idFactura = $row['id_documento_cruce'];
					$arraySaldoFacturas[$idFactura] += $row['abono'];
					$whereIdFactura .= ($whereIdFactura != '')? ' OR id = '.$idFactura : 'id = '.$idFactura;
				}
			}

			//CONSULTAMOS PARA VALIDAR QUE NO SE EXEDAN EN LOS SALDOS
			$sql   = "SELECT id, id_cliente, prefijo, numero_factura, total_factura_sin_abono AS saldo FROM ventas_facturas WHERE activo=1 AND ( $whereIdFactura ) AND id_empresa=".$_SESSION['EMPRESA'];
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$idFactura = $row['id'];
				if($row['saldo'] < $arraySaldoFacturas[$idFactura] && (ABS($row['saldo']-$arraySaldoFacturas[$idFactura] > 1))){
					$diferencia       = number_format($arraySaldoFacturas[$idFactura] - $row['saldo'],2);
					$numero_documento = ($row['prefijo'] != '')? $row['prefijo'].' '.$row['numero_factura']: $row['numero_factura'];
					echo '<script>
							alert("Error!\nLa Factura de Venta numero '.$numero_documento.'\nExcede el saldo del documento relacionado con '.$diferencia.' de diferencia.");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}
		}

		if($accion == 'agregar'){
		  	$sql = "UPDATE ventas_facturas AS CF, (SELECT SUM(debito - credito) AS abono,id_documento_cruce,cuenta
									FROM recibo_caja_cuentas
									WHERE activo = 1 AND id_recibo_caja = '$id' AND tipo_documento_cruce = 'FV'
									GROUP BY id_documento_cruce
								) AS CE
								SET CF.total_factura_sin_abono =  ROUND((CF.total_factura_sin_abono - CE.abono),$_SESSION[DECIMALESMONEDA])
								WHERE CF.id = CE.id_documento_cruce
								AND CF.cuenta_pago = CE.cuenta
								AND CF.id_empresa = " . $_SESSION['EMPRESA'];
			$query = mysql_query($sql,$link);
		}
		else if($accion == 'eliminar'){
			$sql = "UPDATE ventas_facturas AS CF, (SELECT SUM(debito-credito) AS abono,id_documento_cruce,cuenta
								FROM recibo_caja_cuentas
								WHERE activo = 1 AND id_recibo_caja = '$id' AND tipo_documento_cruce = 'FV'
								GROUP BY id_documento_cruce
							) AS CE
							SET CF.total_factura_sin_abono = ROUND((CF.total_factura_sin_abono + CE.abono),$_SESSION[DECIMALESMONEDA])
							WHERE CF.id = CE.id_documento_cruce
							AND CF.cuenta_pago = CE.cuenta
							AND CF.id_empresa = " . $_SESSION['EMPRESA'];
			$query = mysql_query($sql,$link);
		}
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*******************************************************************************************************************************************************************************/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//==========================================// FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA //==========================================//
	function guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero_general,$id_tercero,$terceroGeneral,$id_documento_cruce='0',$documento_cruce='',$prefijo_documento_cruce='',$numero_documento_cruce='',$link){
		$debe  = (is_nan($debe) || $debe == '')? 0: $debe;
		$haber = (is_nan($haber) || $haber == '')? 0: $haber;
		$saldo_pendiente = ($debe>$haber)? $debe : $haber;

		//VERIFICAR NUEVAMENTE EL MONTO EN DEBE Y HABER PARA VALIDAR QUE CONTENGA UN VALOR PERO QUE SOLO SEA UNO
		if ($debe == 0 && $haber == 0) { echo '<script>alert("Aviso\nDebe ingresar un valor numerico debito o credito");</script>'; return; }

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
						debito,
						credito,
						saldo_pendiente,
						id_tercero)
					VALUES(
						'$id',
						'$idPuc',
						'$id_documento_cruce',
						'$documento_cruce',
						'$prefijo_documento_cruce',
						'$numero_documento_cruce',
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

			$script =($centro_costo=='Si')? 'document.getElementById("label_cont_'.$opcGrillaContable.'_'.$cont.'").innerHTML="<div style=\'float:right;\'>'.$cont.'</div><div style=\'float:left;\'><img src=\'../compras/img/warning.png\' title=\'Requiere Centro de Costos\'></div>";' : '';


			$body=cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrillaContable);

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
					Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();
					'.$script.'
					actualiza_fila_ventana_busqueda_doc_cruce('.$id_documento_cruce.');

				</script>'.$body;

		}
		else{ echo '<script>
						alert("Aviso,\nNo se ha almacenado la cuenta\nSi el problema persiste favor comuniquese con la administracion del sistema ");
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
			echo'<script>
					alert(" La cuenta '.$cuenta.' de la '.$documento_cruce.' relacionada no existe en los asientos de ese documento\nPor favor digite una cuenta que genero el documento relacionado");
					'.$script.'
					actualiza_fila_ventana_busqueda_doc_cruce('.$id_documento_cruce.',"fail");
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
					alert("El valor a cruzar es superior al saldo registrado en el documento ('.$absCuentaDb.').");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce='0',$documento_cruce='',$prefijo_documento_cruce='',$numero_documento_cruce='',$id_tercero_general,$cuenta,$link){
		$debe  = (is_nan($debe) || $debe == '')? 0: $debe;
		$haber = (is_nan($haber) || $haber == '')? 0: $haber;
		$saldo_pendiente = ($debe>$haber)? $debe: $haber;

		if ($documento_cruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce,$numero_documento_cruce,$opcGrillaContable,$cont,$id_tercero_general,$id_tercero,$cuenta,'actualizar',$debe,$haber,$link); }

		$numero_documento_cruce=($documento_cruce!='')? $numero_documento_cruce : '';

		$sqlArticuloAnterior   = "SELECT debito,credito FROM $tablaCuentasNota WHERE id='$idInsertCuenta' AND $idTablaPrincipal='$id'";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);
		$debeAnterior          = mysql_result($queryArticuloAnterior,0,'debito');
		$haberAnterior         = mysql_result($queryArticuloAnterior,0,'credito');

		// CONSULTAR LA CUENTA PARA SABER SI ES OBLIGATORIO EL CENTRO DE COSTOS
		$sql="SELECT centro_costo FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$idPuc ";
		$query=mysql_query($sql,$link);
		$centro_costo = mysql_result($query,0,'centro_costo');
		$campoUpdate = ($centro_costo<>'Si')? ",id_centro_costos='',codigo_centro_costos='',centro_costos=''" : "" ;

		$sqlUpdateArticulo   = "UPDATE $tablaCuentasNota
								SET id_puc='$idPuc',
									debito='$debe',
									credito='$haber',
									saldo_pendiente='$saldo_pendiente',
									id_tercero='$id_tercero',
									id_documento_cruce='$id_documento_cruce',
									tipo_documento_cruce='$documento_cruce',
									prefijo_documento_cruce='$prefijo_documento_cruce',
									numero_documento_cruce='$numero_documento_cruce'
									$campoUpdate
								WHERE $idTablaPrincipal=$id AND id=$idInsertCuenta;";
		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);

		if ($queryUpdateArticulo) {



			$sql = "SELECT id_centro_costos FROM $tablaCuentasNota WHERE activo=1 AND id=$idInsertCuenta;";
			$query = mysql_query($sql,$link);
			$id_centro_costos = mysql_result($query,0,'id_centro_costos');

			$script =($centro_costo=='Si' && ($id_centro_costos=='' || $id_centro_costos==0) )? 'document.getElementById("label_cont_'.$opcGrillaContable.'_'.$cont.'").innerHTML="<div style=\'float:right;\'>'.$cont.'</div><div style=\'float:left;\'><img src=\'../compras/img/warning.png\' title=\'Requiere Centro de Costos\'></div>";' :
																								'document.getElementById("label_cont_'.$opcGrillaContable.'_'.$cont.'").innerHTML="'.$cont.'";'  ;

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
		$sql   = "SELECT estado,consecutivo,id_tercero,tipo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$estado             = mysql_result($query,0 ,'estado');
		$consecutivo        = mysql_result($query,0 ,'consecutivo');
		$id_tercero_general = mysql_result($query,0 ,'id_tercero');
		$tipo               = mysql_result($query,0 ,'tipo');

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
			//REGRESAMOS LOS SALDOS DE LOS DOCUMENTOS
			moverSaldoDocumentosRelacionados($id,$tablaCuentasNota,$idTablaPrincipal,$id_tercero_general,'agregar',$link);

			//DESCONTABILIZAMOS LA NOTA, ELIMINADO LOS ASIENTOS QUE SE GENERARON A PARTIR DE ELLA
			moverCuentasDocumento($id_empresa,$id_sucursal,$id,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
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
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','RC','Recibo de Caja',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
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

			$sqlConsulDoc="SELECT consecutivo,tipo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";
			$queryConsulDoc = mysql_query($sqlConsulDoc,$link);
			$consecutivo = mysql_result($queryConsulDoc,0,'consecutivo');
			$tipo        = mysql_result($queryConsulDoc,0,'tipo');

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','RC','Recibo de Caja',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);
			$title_siho = ($tipo=='Ws')? '<br>Sincronizado de SIHO' : '' ;
			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "recibo_caja/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_recibo_caja    : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'"
						}
					});
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Recibo de Caja<br>N. '.$consecutivo.' '.$title_siho.'";
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
		$sql   = "UPDATE $tablaPrincipal SET fecha_recibo='$fecha' WHERE id='$id' ";
		$query = mysql_query($sql,$link);

 		if (!$query) { echo '<script>alert("Error!\nNo se actualizo la fecha, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
 	}

 	function ventana_buscar_documento_cruce($cont,$opcGrillaContable,$id_recibo_caja){
 		echo'<select name="filtro_tipo_documento" id="filtro_tipo_documento" style="width:100px; margin: 7px 0px 0px 5px;height:25px" onChange="carga_filtro_tipo_documento()">
        		<option value="FV">FV</option>
    		</select>
    		<script>
				function carga_filtro_tipo_documento(){
					// var filtroTipoDocumento = document.getElementById("filtro_tipo_documento").value;
					// Ext.get("contenedor_buscar_documento_cruce_'.$opcGrillaContable.'").load({
					// 	url     : "recibo_caja/bd/grillaDocumentoCruce.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		cont                 : "'.$cont.'",
					// 		tipo_documento_cruce : filtroTipoDocumento,
					// 		opcGrillaContable    : "'.$opcGrillaContable.'"
					// 	}
					// });
				}
				// carga_filtro_tipo_documento();
			</script>';

 	}

 	function ventana_buscar_terceros($cont,$opcGrillaContable,$id_recibo_caja){
 		echo'<select name="filtro_terceros" id="filtro_terceros" style="width:100px; margin: 7px 0px 0px 5px;height:25px" onChange="carga_filtro_tercero()">
        		<option value="principal" selected>Principal</option>
        		<option value="todos">Todos</option>
    		</select>
    		<script>
    		    setTimeout(function(){ carga_filtro_tercero() },200);
				function carga_filtro_tercero(){
					var filtroTercero         = document.getElementById("filtro_terceros").value
					,	filtroSucursal        = document.getElementById("filtro_sucursal_'.$opcGrillaContable.'").value
					,   tipo_documento_cruce  = document.getElementById("filtro_tipo_documento").value;

					whereTercero = "";

					if(filtroTercero == "principal"){
						whereTercero = "AND id_cliente="+id_cliente_'.$opcGrillaContable.';
					}
					Ext.get("contenedor_buscar_documento_cruce_'.$opcGrillaContable.'").load({
					 	url     : "recibo_caja/bd/grillaDocumentoCruce.php",
					 	scripts : true,
					 	nocache : true,
						params  :
						{
							cont                 : "'.$cont.'",
							tipo_documento_cruce : tipo_documento_cruce,
							opcGrillaContable    : "'.$opcGrillaContable.'",
							filtro_sucursal      : filtroSucursal,
							whereTercero         : whereTercero,
							id_recibo_caja       : "'.$id_recibo_caja.'",
						}
					});
				}
				//carga_filtro_tercero();
			</script>';

 	}

 	function ventana_buscar_sucursal($cont,$opcGrillaContable,$id_recibo_caja,$link){

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

				var filtro_sucursal      = document.getElementById('filtro_sucursal_<?php echo $opcGrillaContable; ?>').value
				,	filtroTercero        = document.getElementById("filtro_terceros").value
				,	tipo_documento_cruce = document.getElementById("filtro_tipo_documento").value;

				whereTercero = "";

				if(filtroTercero == "principal"){
					whereTercero = "AND id_cliente="+id_cliente_<?php echo $opcGrillaContable; ?>;
				}

				if (document.getElementById('filtro_tipo_documento')) { tipo_documento_cruce=document.getElementById('filtro_tipo_documento').value; }
				else{ tipo_documento_cruce='<?php echo $documento_cruce; ?>'; }
				// var tipo_documento_cruce=(typeof(document.getElementById('filtro_tipo_documento').value)=="undefined")? "<?php echo $documento_cruce; ?>" : document.getElementById('filtro_tipo_documento').value ;

				Ext.get('contenedor_buscar_documento_cruce_<?php echo $opcGrillaContable; ?>').load({
					url     : "recibo_caja/bd/grillaDocumentoCruce.php",
					scripts : true,
					nocache : true,
					params  :
					{
						cont                 : "<?php echo $cont; ?>",
						opcGrillaContable    : "<?php echo $opcGrillaContable; ?>",
						whereTercero         : whereTercero,
						filtro_sucursal      : filtro_sucursal,
						tipo_documento_cruce : tipo_documento_cruce,
						id_recibo_caja       : '<?php echo $id_recibo_caja; ?>'

					}
				});
			}

			//cambia_filtro_<?php echo $opcGrillaContable; ?>();

		</script>

 <?php

 	}

 	function ventana_add_delete_documentos_cruce($contArticulos,$opcGrilla,$id_documento){
 		echo'<select id="filtroDocCruce_'.$opcGrilla.'" style="width:100px; margin: 7px 0px 0px 5px;height:25px" onChange="reload_docs_cruce_'.$opcGrilla.'(this.value)">
        		<option value="FV">FV</option>
        		<option value="FV">FV</option>
    		</select>
    		<script>
				function reload_docs_cruce_'.$opcGrilla.'(typeDoc){
					Ext.get("contenedor_grillaAddDeleteDocsCruce_'.$opcGrilla.'").load({
						url     : "recibo_caja/bd/grilla_add_delete_documentos_cruce.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrilla     : "'.$opcGrilla.'",
							id_documento  : "'.$id_documento.'",
							contArticulos : "'.$contArticulos.'",
							typeDocCruce  : typeDoc
						}
					});
				}
				reload_docs_cruce_'.$opcGrilla.'(document.getElementById("filtroDocCruce_'.$opcGrilla.'").value)
			</script>';
	}

 	function updatecuentaPago($idConfiguracion,$idReciboCaja,$id_empresa,$link){
 		$sql   = "UPDATE recibo_caja SET id_configuracion_cuenta='$idConfiguracion' WHERE id='$idReciboCaja' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
 	}

 	function updateFlujoEfectivo($idFlujoEfectivo,$flujo_efectivo,$idReciboCaja,$id_empresa,$link){
 		$sql   = "UPDATE recibo_caja SET flujo_efectivo='$flujo_efectivo', id_flujo_efectivo='$idFlujoEfectivo' WHERE id='$idReciboCaja' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$link){
		$sql   = "SELECT estado,consecutivo FROM recibo_caja WHERE id=$id_documento";
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

					Ext.get("contenedor_ReciboCaja").load({
		        		    url     : "recibo_caja/bd/grillaContableBloqueada.php",
		        		    scripts : true,
		        		    nocache : true,
		        		    params  :
		        		    {
							opcGrillaContable : "ReciboCaja",
							id_recibo_caja    : '.$id_documento.'
		        		    }
		        		});

					document.getElementById("titleDocumentoReciboCaja").innerHTML="Recibo de Caja<br>N. '.$consecutivo.'";
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
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}

	}

	//=========================== FUNCION PARA BORRAR UN DOCUMENTO ANEXO ===========================//
	function deleteDocumentoReciboCaja($id_host,$idDocumento,$nombre,$ext,$link){
		$nombreImage = md5($nombre).'_'.$idDocumento.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nombreImage;
		} else{
			$url = '';
		}

		$sqlDelete   = "UPDATE ventas_recibo_caja_documentos SET activo = 0 WHERE id = $idDocumento";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){
			echo '<script>
							alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");
						</script>';
			exit;
		} else{
			unlink($url);
			echo "<script>
							Elimina_Div_reciboCajaDocumentos($idDocumento);
						</script>";
			exit;
		}
	}

	//========================== FUNCION PARA DESCARGAR UN DOCUMENTO ANEXO =========================//
	function downloadFile($nameFile,$ext,$id,$id_empresa,$id_host){
		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nombreImage;
		}	else{
			$url = '';
		}

		if(file_exists($url)){
			header('Content-Disposition: attachment; filename='.$nameFile.'.'.$ext);
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($url));
			ob_clean();
			flush();
			readfile($url);
		} else{
			echo "Error, el archivo no se encuentra almacenado ";
		}

		exit;
	}

	//========================= FUNCION PARA VER LISTA DE DOCUMENTOS ANEXOS ========================//
	function ventanaViewDocumento($nameFile,$ext,$id,$id_host){
		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nombreImage)){
			$url = '../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($ext != 'pdf' && $type != 'PDF'){
			echo'<div style="margin:0px; width:100%; height:100%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<img src="'.$url.'" id="imagenItems">
					</div>
					<a class="btn_guardar_anexo" href="'.$url.'" download="'.$nameFile.'" title="Click para descargar">
						<img src="../../temas/clasico/images/BotonesTabs/guardar.png" style="margin-top: 3px;"/>
						<div style="color:#000; text-align: center;">Descargar</div>
					</a>
				</div>
				<script>
					document.getElementById("imagenItems").oncontextmenu = function(){ return false; }
				</script>
				<style>

					.btn_guardar_anexo{
						opacity     : 0.4;
						position    : absolute;
						width       : 68px;
						height      : 55px;
						top         : 5;
						left        : 10;
						overflow    : hidden;
						text-align  : center;
						color       : #333;
						padding     : 0px;
						margin      : 0px;
						font-weight : bold;
						border      : 3px solid #000;
						background-color      : #FFF;
						-moz-border-radius    : 1px;
						-webkit-border-radius : 1px;
						background : -webkit-linear-gradient(#FFF, #CECECE);
						background : -moz-linear-gradient(#FFF, #CECECE);
						background : -o-linear-gradient(#FFF, #CECECE);
						background : linear-gradient(#FFF, #CECECE);

						-webkit-box-shadow: 4px 7px 45px 2px rgba(255,255,255,1);
						-moz-box-shadow: 4px 7px 45px 2px rgba(255,255,255,1);
						box-shadow: 4px 7px 45px 2px rgba(255,255,255,1);
					}

					.btn_guardar_anexo:hover{
						opacity : 1;
						-webkit-animation : cssAnimation 1s 1 ease-in;
						-moz-animation    : cssAnimation 1s 1 ease-in;
						-o-animation      : cssAnimation 1s 1 ease-in;
						animation-iteration-count : 1;
					}

					@-webkit-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
					@-moz-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
					@-o-keyframes cssAnimation {
						from { opacity:0.7; }
						to { opacity:1; }
					}
				</style>';
		}
		else{
			echo'<div style="margin:0px; width:100%; height:100%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<iframe src="'.$url.'" id="iframeViewDocumentItems"></iframe>
					</div>
				</div>
				<script>
					cambiaViewPdf();
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeViewDocumentItems");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function consultaSizeDocumento($nameFile,$ext,$id,$id_host){
		$nameFile = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nameFile)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/ventas/recibo_caja/'.$nameFile;
		}
		else{
			$url = '';
		}

		list($size['ancho'], $size['alto'], $tipo, $atributos) = getimagesize($url);
		echo json_encode($size);
	}
?>
