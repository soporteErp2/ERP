<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		if ($opc!='cancelarDocumento' && $opc!='restaurarDocumento' && $opc!='modificarDocumentoGenerado') {
			// verificaEstadoDocumento($id,$link);
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

		case 'buscarCuenta':
			buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$id_sucursal,$idCliente,$opcGrillaContable,$link);
			break;

		case 'deleteCuenta':
			deleteCuenta($cont,$id,$idCuenta,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'retrocederCuenta':
		 	retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'validaNota':
			validaNota($id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$id_sucursal,$id_tercero,$notaCruce,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'guardarCuenta':
			guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_tabla_referencia,$id_documento_cruce,$numeroDocumentoCruce,$tipoDocumentoCruce,$link);
			break;

		case 'actualizaCuenta':
			actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_tabla_referencia,$id_documento_cruce,$numeroDocumentoCruce,$tipoDocumentoCruce,$link);
			break;

		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'actualizarTipoNota':
			actualizarTipoNota($id,$id_concepto,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$link);
			break;

		case 'actualizarFechaNota':
			actualizarFechaNota($id,$fecha,$campo,$tablaPrincipal,$link);
			break;

		case 'eliminarArticuloRelacionado':
			eliminarArticuloRelacionado($id,$tipo,$accion,$link);
			break;

		case 'ventana_buscar_documento_cruce':
			ventana_buscar_documento_cruce($cont,$opcGrillaContable,$carpeta,$id_empresa,$id_sucursal,$link);
			break;

		case 'cargaCuentaNiif':
			cargaCuentaNiif($idInsertCuenta,$idCuenta,$id_empresa,$opcGrillaContable,$cont,$link);
			break;

		case 'actualizarNiif':
			actualizarNiif($id_niif,$cuenta,$idInsertCuenta,$idCuenta,$opcGrillaContable,$cont,$id_empresa,$link);
			break;

		case 'cargaHeadInsertUnidadesConTercero':
			cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable);
			break;

		case 'actualizarCuentaCruce':
			actualizarCuentaCruce($opcGrillaContable,$id,$id_cuenta,$id_empresa,$id_sucursal,$link);
			break;

		case 'actualizarCuentaCruceNiif':
			actualizarCuentaCruceNiif($opcGrillaContable,$id,$cuenta,$id_empresa,$id_sucursal,$link);
			break;
		case 'cargaNiifCruce':
			cargaNiifCruce($id,$id_empresa,$opcGrillaContable,$link);
			break;

		case 'ventana_enviar_email' :
			ventana_enviar_email($opcGrillaContable,$id_nota,$consecutivo,$fecha_inicio,$fecha_final,$concepto,$id_empresa,$id_sucursal,$link);
			break;

		case 'enviarVolanteUnicoEmpleado':
			enviarVolanteUnicoEmpleado($id_tercero,$consecutivo,$fecha_inicio,$fecha_final,$concepto,$saldo,$id_empresa,$link);
			break;
		case 'cargarTodasProvisiones':
			cargarTodasProvisiones($id,$id_concepto,$fecha_inicial,$fecha_final,$id_empresa,$sucursal,$MyFiltroBusqueda,$link);
			break;

	}

	//=========================== FUNCION PARA BUSCAR UN CLIENTE ===============================================================================//
	function buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link){

		if ($inputId=='nitCliente'.$opcGrillaContable) {
			$where   ='numero_identificacion="'.$codCliente.'" AND tipo_identificacion="'.$tipoDocumento.'"';
			$mensaje = 'alert("'.$tipoDocumento.' de tercero no establecido");';
		}
		else if ($inputId=='codigoTercero'.$opcGrillaContable) {
			$where   = 'codigo= "'.$codCliente.'"';
			$mensaje = 'alert("codigo de tercero no establecido");';
		}

		$sqlTercero   = "SELECT id, numero_identificacion, tipo_identificacion, codigo, nombre, COUNT(id) AS contTercero FROM terceros WHERE $where  AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
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
					document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nitTercero.'";
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

		$sqlUpdate = " UPDATE $tablaPrincipal
						SET id_tercero = '$idTercero',
							tercero = '$nombre',
							$camposInsert
						WHERE id='$id'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		echo'<script>
				document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigo.'";
				document.getElementById("tipoDocumento'.$opcGrillaContable.'").value = "'.$tipoNit.'";
				document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
				document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombre.'";

				id_cliente_'.$opcGrillaContable.'   = "'.$idTercero.'";
				nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
				nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";

			</script>';
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrillaContable){
		$head ='<div class="contenedorGrilla">
					<div class="contenedorHeadArticulos">
						<div class="headArticulos">
							<div class="label'.$opcGrillaContable.'" style="width:40px !important;"></div>
							<div class="label'.$opcGrillaContable.'" style="width:95px;">Cuenta</div>
							<div class="label'.$opcGrillaContable.' campoDescripcion">Descripcion</div>
							<div class="label'.$opcGrillaContable.' campoDescripcion">Tercero</div>
							<div class="label'.$opcGrillaContable.' " >Doc. Cruce</div>
							<div class="label'.$opcGrillaContable.' " >N.Doc.Cruce</div>
							<div class="label'.$opcGrillaContable.'" >Debito</div>
							<div class="label'.$opcGrillaContable.'" >Credito</div>
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
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
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
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL (DEBITO - CREDITO)</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrillaContable.'">0</div>
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
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo'.$opcGrillaContable.'" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campo'.$opcGrillaContable.' campoDescripcion"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campo'.$opcGrillaContable.' campoDescripcion"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="iconBuscarArticulo" >
					<img src="img/buscar20.png" id="imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" onclick="buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')" title="Buscar Tercero" />
				</div>

				<div class="campo'.$opcGrillaContable.' ">
				 	<input type="text" id="documentoCruce'.$opcGrillaContable.'_'.$cont.'" readonly style="text-align:left;" >
				 </div>
				 <div class="iconBuscarArticulo ">
					<img onclick="ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/buscar20.png" />
				</div>

				<div class="campo'.$opcGrillaContable.' ">
					<input title="Numero" type="text" readonly id="numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'" class="inputNumeroNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'\',\''.$cont.'\');"  />
				</div>

				<div class="campo'.$opcGrillaContable.'" ><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"  /></div>
				<div class="campo'.$opcGrillaContable.'" ><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/></div>

				<div style="float:right; min-width:80px;">
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
					document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>
				';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
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
			echo '<script>
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
					setTimeout(function(){ document.getElementById("debito'.$opcGrillaContable.'_'.$contFila.'").focus(); }, 100);
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
						setTimeout(function(){ document.getElementById("debito'.$opcGrillaContable.'_'.$contFila.'").focus();},100);
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
		$sql   = "SELECT id_puc,cuenta_puc,descripcion_puc,debe,haber,id_documento_cruce,numero_documento_cruce,tercero,tipo_documento_cruce,id_tabla_referencia FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuentaInsert' ";
		$query = mysql_query($sql,$link);

		$idCuenta               = mysql_result($query,0,'id_puc');
		$cuenta                 = mysql_result($query,0,'cuenta_puc');
		$descripcion            = mysql_result($query,0,'descripcion_puc');
		$debe                   = mysql_result($query,0,'debe');
		$haber                  = mysql_result($query,0,'haber');
		$tercero                = mysql_result($query,0,'tercero');
		$id_documento_cruce     = mysql_result($query,0,'id_documento_cruce');
		$tipo_documento_cruce   = mysql_result($query,0,'tipo_documento_cruce');
		$numero_documento_cruce = (mysql_result($query,0,'numero_documento_cruce')==0)? '' : mysql_result($query,0,'numero_documento_cruce') ;
		$id_tabla_referencia    = mysql_result($query,0,'id_tabla_referencia');


		echo'<script>';

		echo 	($tercero != "")?
					'document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/delete.png");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Eliminar Tercero");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick"," eliminaTercero'.$opcGrillaContable.'('.$cont.')");'

				:	'document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Buscar Tercero");
        			document.getElementById("imgBuscarTercero'.$opcGrillaContable.'_'.$cont.'").setAttribute("onclick","buscarVentanaTercero'.$opcGrillaContable.'('.$cont.')");';

		echo 	($numero_documento_cruce != "")?
					'document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("src","img/delete.png");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("title","Eliminar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("onclick"," eliminaDocumentoCruce'.$opcGrillaContable.'('.$cont.')");'
				:
					'document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("title","Buscar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("onclick","ventanaBuscarDocumentoCruce'.$opcGrillaContable.'('.$cont.')");';

				echo'	document.getElementById("idCuenta'.$opcGrillaContable.'_'.$cont.'").value         = "'.$idCuenta.'";
				document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$cuenta.'";
				document.getElementById("descripcion'.$opcGrillaContable.'_'.$cont.'").value              = "'.$descripcion.'";
				document.getElementById("tercero'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$tercero.'";
				document.getElementById("documentoCruce'.$opcGrillaContable.'_'.$cont.'").value           = "'.$tipo_documento_cruce.'";
				document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value     = "'.$numero_documento_cruce.'";
				document.getElementById("idDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").value         = "'.$id_documento_cruce.'";
				document.getElementById("idTablaReferencia'.$opcGrillaContable.'_'.$cont.'").value        = "'.$id_tabla_referencia.'";
				document.getElementById("debito'.$opcGrillaContable.'_'.$cont.'").value                   = "'.$debe.'";
				document.getElementById("credito'.$opcGrillaContable.'_'.$cont.'").value                  = "'.$haber.'";
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
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
				</script>';
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA NOTA ==========================================================================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_empresa,$id_sucursal_login,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link){
		// CONSULTAR LA SUCURSAL DEL DOCUMENTO
		$sql="SELECT id_sucursal,id_tercero,id_cuenta_colgaap_cruce AS cuenta FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=mysql_query($sql,$link);
		$id_sucursal = mysql_result($query,0,'id_sucursal');
		$id_tercero  = mysql_result($query,0,'id_tercero');
		$cuenta      = mysql_result($query,0,'cuenta');

		if ($id_tercero=='') {
			echo '<script>alert("Aviso\nDebe seleccionar un tercero para el documento");</script>';
			exit;
		}
		if ($cuenta=='') {
			echo '<script>alert("Aviso\nDebe seleccionar la cuenta de consolidado para el documento");</script>';
			exit;
		}

	 	//MOVEMOS LAS CUENTAS
		moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'agregar',$id_tercero,$link);

		// //MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$id,'eliminar',$link);
		// exit;
		$fecha = date("Y-m-d");
		$sql   = "UPDATE $tablaPrincipal SET estado=1,fecha_finalizacion='$fecha' WHERE id='$id' AND activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if (!$query){
			echo '<script>alert("Error!\nNo se pudo actualizar la nota, vuelva a intentarlo\nSi el problema persite comuniquese con el administrador del sistema");</script>';
			moverDocumentosSaldos($id_empresa,$id,'agregar',$link);
			moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',$id_tercero,$link);
		}
		else{
			$sqlConsecutivo = "SELECT consecutivo FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
			$queryConsecutivo=mysql_query($sqlConsecutivo,$link);
			$consecutivo_documento = mysql_result($queryConsecutivo,0,'consecutivo');

			$sqlUpdate = "UPDATE asientos_colgaap SET consecutivo_documento='$consecutivo_documento' WHERE id_documento='$id' AND tipo_documento='LP' AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);

			$sqlUpdate = "UPDATE asientos_niif SET consecutivo_documento='$consecutivo_documento' WHERE id_documento='$id' AND tipo_documento='LP' AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);

		}

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
					VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Liquidacion Provision',$id_sucursal_login,'$id_empresa')";
		$queryLog = mysql_query($sqlLog,$link);

	   	echo'<script>
   				Ext.get("contenedor_LiquidacionProvision").load({
 		            url     : "liquidacion_provision/bd/grillaContableBloqueada.php",
 		            scripts : true,
 		            nocache : true,
 		            params  :
 		            {
						id_nota           : '.$id.',
						opcGrillaContable : "LiquidacionProvision",
 		            }
 		        });
			</script>';
	}

	//============================= FUNCION PARA MOVER LOS SALDOS (ABONOS) DE LOS DOCUMENTOS RELACIONADOS EN FC Y FV ================================//
	function moverDocumentosSaldos($id_empresa,$id_nota,$accion,$link){
		// echo $accion;
		//VALIDAR SALDOS GENERAR EL DOCUMENTO
		if ($accion != 'agregar') {

			$sql   = "SELECT id,SUM(debito - credito) AS saldoDebitoabono,id_documento_cruce,tipo_documento_cruce,cuenta
						FROM nomina_consolidacion_provision_cuentas
						WHERE activo=1
							AND id_consolidacion_provision='$id_nota'
							AND tipo_documento_cruce<>''
							AND id_documento_cruce > 0
						GROUP BY id_documento_cruce,cuenta ";
			while ($row=mysql_fetch_array($query)){

				if ($row['tipo_documento_cruce'] == 'LN') {
					$idPlanilla = $row['id_documento_cruce'];
					$cuenta = $row['cuenta'];
					$arraySaldoPlanilla[$idPlanilla][$cuenta] += $row['saldoDebitoabono'];
					$whereIdPlanilla .= ($whereIdPlanilla != '')? ' OR id_planilla = '.$idPlanilla : 'id_planilla = '.$idPlanilla;
				}
			}

			//CONSULTAMOS PARA VALIDAR QUE NO SE EXEDAN EN LOS SALDOS DE LAS FACTURAS DE COMPRA
			$sql   = "SELECT id,id_planilla,consecutivo_planilla, total_sin_abono_provision AS saldo,cuenta_colgaap FROM nomina_planillas_empleados_contabilizacion WHERE activo=1 AND ( $whereIdPlanilla ) AND id_empresa=".$_SESSION['EMPRESA'];
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$idPlanilla = $row['id_planilla'];
				if($row['saldo'] < $arraySaldoPlanilla[$idPlanilla][$row['cuenta_colgaap']]){
					$diferencia       = number_format($arraySaldoPlanilla[$idPlanilla][$row['cuenta_colgaap']] - $row['saldo'],2);
					echo '<script>alert("Error!\nLa cuenta de la planilla de nomina '.$row['consecutivo_planilla'].'\nExcede el saldo del documento relacionado con '.$diferencia.' de diferencia.");</script>';
					exit;
				}
			}

		}

		if ($accion=='agregar') {
			$sql="UPDATE nomina_planillas_empleados_contabilizacion AS CF,
						 (
							SELECT
								NC.*
							FROM
								nomina_consolidacion_provision_cuentas AS NC
							WHERE
								NC.id_consolidacion_provision = $id_nota
							AND NC.tipo_documento_cruce = 'LN'
							AND NC.id_documento_cruce <> ''
						) AS NC
						SET CF.total_sin_abono_provision = CF.total_sin_abono_provision + (NC.debe + NC.haber)
						WHERE
							CF.id_planilla = NC.id_documento_cruce
						AND CF.cuenta_colgaap = NC.cuenta_puc
						AND CF.id = NC.id_tabla_referencia
						AND CF.id_empresa = $id_empresa";

		}
		else if ($accion=='eliminar') {
			$sql="UPDATE nomina_planillas_empleados_contabilizacion AS CF,
						 (
							SELECT
								NC.*
							FROM
								nomina_consolidacion_provision_cuentas AS NC
							WHERE
								NC.id_consolidacion_provision = $id_nota
							AND NC.tipo_documento_cruce = 'LN'
							AND NC.id_documento_cruce <> ''
						) AS NC
						SET CF.total_sin_abono_provision = CF.total_sin_abono_provision - (NC.debe + NC.haber)
						WHERE
							CF.id_planilla = NC.id_documento_cruce
						AND CF.cuenta_colgaap = NC.cuenta_puc
						AND CF.id = NC.id_tabla_referencia
						AND CF.id_empresa = $id_empresa";

		}

		//EJECUTAR LOS QUERY
		$query = mysql_query($sql,$link);

		if (!$query) { echo '<script>alert("Error!\nNo se actualizo el saldo de los documentos LN");</script>'; }

	}

	//============================ FUNCION PARA VALIDAR LA NOTA Y LLAMAR LA FUNCION TERMINAR PARA GENERARLA =========================================================================//
	// function validaNota($id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$id_sucursal,$id_tercero,$notaCruce,$link){

	// 	$sqlNota   = "SELECT tercero,cuenta_colgaap_cruce FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
	// 	$queryNota = mysql_query($sqlNota,$link);

	// 	$cuenta_colgaap_cruce = mysql_result($queryNota,0,'cuenta_colgaap_cruce');
	// 	$tercero              = mysql_result($queryNota,0,'tercero');

	// 	if($cuenta_colgaap_cruce==''){ echo '<script>alert("Debe Seleccionar la cuenta Cruce!");</script>'; exit; }
	// 	if($tercero==''){ echo '<script>alert("Debe Seleccionar el tercero!");</script>'; exit; }

	// 	// VALIDAR LAS CUENTAS DE LOS DOCUMENTOS CRUCE
	// 	moverDocumentosSaldos($id_empresa,$id,'validar',$link);

	// 	terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link);

	// }

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$idEmpresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,$accion,$id_tercero,$link){

		if ($accion=='agregar') {
			$sqlNotaGeneral   = "SELECT consecutivo,tercero,fecha_nota,cuenta_colgaap_cruce,cuenta_niif_cruce FROM nomina_consolidacion_provision WHERE activo=1 AND id='$idDocumento' AND id_empresa='$idEmpresa'";
			$queryNotaGeneral = mysql_query($sqlNotaGeneral,$link);

			$consecutivoNota      = mysql_result($queryNotaGeneral,0,'consecutivo');
			$tercero              = mysql_result($queryNotaGeneral,0,'tercero');
			$fechaNota            = mysql_result($queryNotaGeneral,0,'fecha_nota');
			$cuenta_colgaap_cruce = mysql_result($queryNotaGeneral,0,'cuenta_colgaap_cruce');
			$cuenta_niif_cruce    = mysql_result($queryNotaGeneral,0,'cuenta_niif_cruce');

			$sql   = "SELECT debe,haber,cuenta_puc,cuenta_niif,id_tercero,id_documento_cruce,tipo_documento_cruce,numero_documento_cruce
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

				if($row['debe'] == '') $row['debe'] = 0;
				if($row['haber'] == '') $row['haber'] = 0;

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

				$id_tercero_nota = ($row['id_tercero']=='0' || $row['id_tercero']=='')? $id_tercero : $row['id_tercero'];
				$documento_cruce = $row['numero_documento_cruce'];
				$valueInsertCuentasColgaap .= "('$idDocumento',
												'$consecutivoNota',
												'LP',
												'Liquidacion Provision',
												'".$row['debe']."',
												'".$row['haber']."',
												'".$row['cuenta_puc']."',
												'$id_sucursal',
												'$id_tercero_nota',
												'".$_SESSION['NITEMPRESA']."',
												'',
												'$idEmpresa',
												'$fechaNota',
												'".$row['id_documento_cruce']."',
												'".$row['tipo_documento_cruce']."',
												'$documento_cruce'
												),";

				$valueInsertCuentasNiif .= "('$idDocumento',
											'$consecutivoNota',
											'LP',
											'Liquidacion Provision',
											'".$row['debe']."',
											'".$row['haber']."',
											'".$row['cuenta_niif']."',
											'$id_sucursal',
											'$id_tercero_nota',
											'".$_SESSION['NITEMPRESA']."',
											'',
											'$idEmpresa',
											'$fechaNota',
											'".$row['id_documento_cruce']."',
											'".$row['tipo_documento_cruce']."',
											'$documento_cruce'
											),";
			}

			$diferencia=$saldoDebitoColgaap-$saldoCreditoColgaap;
			$debitoCruce  = ($saldoDebitoColgaap<$saldoCreditoColgaap)? $diferencia : 0 ;
			$creditoCruce = ($saldoCreditoColgaap<$saldoDebitoColgaap)? $diferencia : 0 ;

			//CUENTAS CONTRAPARTIDA CABECERA
			$valueInsertCuentasColgaap .= "('$idDocumento',
												'$consecutivoNota',
												'LP',
												'Liquidacion Provision',
												'".$debitoCruce."',
												'".$creditoCruce."',
												'".$cuenta_colgaap_cruce."',
												'$id_sucursal',
												'$id_tercero_nota',
												'".$_SESSION['NITEMPRESA']."',
												'',
												'$idEmpresa',
												'$fechaNota',
												'$idDocumento',
												'LP',
												'$documento_cruce'
												),";

				$valueInsertCuentasNiif .= "('$idDocumento',
											'$consecutivoNota',
											'LP',
											'Liquidacion Provision',
											'".$debitoCruce."',
											'".$creditoCruce."',
											'".$cuenta_niif_cruce."',
											'$id_sucursal',
											'$id_tercero_nota',
											'".$_SESSION['NITEMPRESA']."',
											'',
											'$idEmpresa',
											'$fechaNota',
											'$idDocumento',
											'LP',
											'$documento_cruce'
											),";

			//VALIDACIONES CONTABILIDAD COLGAAP
			// if($cuentaVaciaColgaap > 0){ echo '<script>alert("Aviso!\nExisten '.$cuentaVaciaColgaap.' registros sin cuentas en la contabilidad colgaap!");</script>'; exit; }
			// else if($saldoDebitoColgaap == 0 || $saldoCreditoColgaap == 0){ echo '<script>alert("Aviso!\nLos saldos deben ser mayores a cero en la contabilidad Colgaap!");</script>'; exit; }
			// else if($saldoDebitoColgaap != $saldoCreditoColgaap){ echo '<script>alert("Aviso!\nLos saldos Debitos ('.$saldoDebitoColgaap.') y Creditos ('.$saldoCreditoColgaap.') en la contabilidad colgaap son diferentes!");</script>'; exit; }

			// //VALIDACIONES CONTABILIDAD NIIF
			// if ($sinc_nota=='colgaap_niif') {
			// 	if($cuentaVaciaNiif > 0){ echo '<script>alert("Aviso!\nExisten '.$cuentaVaciaNiif.' registros sin cuentas en la contabilidad niif!");</script>'; exit; }
			// 	else if($saldoDebitoNiif == 0 || $saldoCreditoNiif == 0){ echo '<script>alert("Aviso!\nLos saldos deben ser mayores a cero en la contabilidad niif!");</script>'; exit; }
			// 	else if($saldoDebitoNiif != $saldoCreditoNiif){ echo '<script>alert("Aviso!\nLos saldos Debitos ('.$saldoDebitoNiif.') y Creditos ('.$saldoCreditoNiif.')  en la contabilidad Niif son diferentes!");</script>'; exit; }
			// }

			// ACTUALIZAR EL SALDO A PAGAR DEL DOCUMENTO
			$sql="UPDATE nomina_consolidacion_provision SET total='$diferencia',total_sin_abono='$diferencia' WHERE activo=1 AND id_empresa=$idEmpresa AND id=$idDocumento";
			$query=mysql_query($sql,$link);

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
								numero_documento_cruce)
							VALUES $valueInsertCuentasColgaap";

			$queryInsert = mysql_query($sqlInsert,$link);
			if (!$queryInsert) { echo '<script>alert("Error!\nNo se genero el asiento contable Colgaap, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }

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
								numero_documento_cruce)
							VALUES $valueInsertCuentasNiif";
			$queryInsert = mysql_query($sqlInsert,$link);
			if (!$queryInsert) {
				$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='LP'";
				$queryDelete = mysql_query($sqlDelete,$link);

				echo'<script>alert("Error!\nNo se genero el asiento contable Niif, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit;
			}

		}

		else if ($accion=='eliminar') {
			$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='LP'";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){ echo '<script>alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }

			$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='LP'";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){ echo '<script>alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
		}
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){
		// VALIDAR QUE NO TENGA DOCUMENTOS CRUCE
		validaDocumentoCruce($idDocumento,$id_empresa,$link);

		$sql   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo=mysql_result($query,0 ,'consecutivo');

		//PARA MODIFICAR EL DOCUMENTO PRIMERO DEBEMOS DESCONTABILIZARLO Y LUEGO REGRESARLO A ESTADO=0
		moverCuentasDocumento($idDocumento,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
		//YA QUE SE DIERON DE BAJA LOS ASIENTOS GENERADOS POR LA NOTA, PROCEDEMOS A ACTUALIZAR SU ESTADO A CERO
		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$idDocumento,'agregar',$link);

		if($query){
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
						VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Liquidacion Provision',$id_sucursal,'$id_empresa')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "liquidacion_provision/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota           : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'"
						}
					});

				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se actualizo el estado del documento!");</script>'; }
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*******************************************************************************************************************************************************************************/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




	//=========================== FUNCION PARA GUARDAR CUENTA DE LA GRILLA ==================================================================//
	function guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_tabla_referencia,$id_documento_cruce,$numeroDocumentoCruce,$tipoDocumentoCruce,$link){
		//VALIDACIONES
		if ($debe==0 && $haber==0) { echo '<script>alert("Error\nDebe ingresar el monto  del debito o del credito");</script>'; return; }
		elseif ($debe>0 && $haber>0) { echo '<script>alert("Error\nDebe ingresar el monto solo para el debito o el credito");</script>'; return; }

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$numeroDocumentoCruce,$opcGrillaContable,$cont,$terceroGeneral,$id_tercero,$cuenta,'guardar',$debe,$haber,$link); }

		$sqlInsert = "INSERT INTO $tablaCuentasNota(
						$idTablaPrincipal,
						id_puc,
						debe,
						haber,
						id_tercero,
						id_tabla_referencia,
						tipo_documento_cruce,
						id_documento_cruce,
						numero_documento_cruce,
						id_empresa)
					VALUES(
						'$id',
						'$idPuc',
						'$debe',
						'$haber',
						'$id_tercero',
						'$id_tabla_referencia',
						'$tipoDocumentoCruce',
						'$id_documento_cruce',
						'$numeroDocumentoCruce',
						'$id_empresa')";
		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

		if($lastId > 0){
			$debe  = (is_nan($debe))? 0  : $debe;
			$haber = (is_nan($haber))? 0 : $haber;

			// if($terceroGeneral!=''){ $body=cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrillaContable); }
			// else{
				$body=cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrillaContable);
			// }

			echo'<script>
					document.getElementById("idInsertCuenta'.$opcGrillaContable.'_'.$cont.'").value = '.$lastId.'

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Cuenta");
					document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					document.getElementById("deleteCuenta'.$opcGrillaContable.'_'.$cont.'").style.display     = "block";
					document.getElementById("configurarCuenta'.$opcGrillaContable.'_'.$cont.'").style.display = "block";

					//llamamos a la funcion para calcular los totales de la nota
					calcTotal'.$opcGrillaContable.'("'.$debe.'","'.$haber.'","agregar");

					//habilitar el boton terminar y nuevo
					Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();
					Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").enable();

				</script>'.$body;

		}
		else{
				echo'<script>
						alert("Error\nNo se ha guardo la cuenta en la nota, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
						var elemento=document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'");
						elemento.parentNode.removeChild(elemento);
				  	</script>  ';
			}
	}


	// function validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$numero_documento_cruce,$opcGrillaContable,$cont,$id_tercero_general,$id_tercero,$cuenta,$evento,$debe,$haber,$link){

	// 	$script = '';
	// 	$cont2  = $cont;

	// 	//CON LA VARIABLE EVENTO IDENTIFICAMOS SI SE ESTA GUARDANDO O ACTUALIZANDO UNA CUENTA, PARA ASI MOSTRAR O NO UN BLOQUE DE CODIGO Y PARA EL CONTADOR
	// 	if ($evento=='guardar') {
	// 		$script = 'document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.($cont++).'").parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.($cont++).'"));
	// 					contArticulos'.$opcGrillaContable.'--;';
	// 		$cont2  = ($cont-2);
	// 	}

	// 	//VALIDAR QUE TENGA UN NUMERO DE DOCUMENTO
	// 	if ($numero_documento_cruce=='' || $numero_documento_cruce=='0') {
	// 		echo'<script>
	// 				alert("Si relaciona una '.$documento_cruce.', debe ingresar el numero!");
	// 				document.getElementById("numeroDocumentoCruce'.$opcGrillaContable.'_'.$cont.'").focus();
	// 			</script>';
	// 		exit;
	// 	}

	// 	//VALIDAR QUE EL NUMERO DE LA CUENTA QUE SE ESTA RELACIONANDO CON EL DOCUMENTO EXISTA EN LOS ASIENTOS RELACIONADOS AL DOCUMENTO CRUCE
	// 	$sqlAsientos   = "SELECT id,SUM(debe - haber) AS saldoCuenta
	// 						FROM asientos_colgaap
	// 						WHERE id_documento_cruce='$id_documento_cruce'
	// 							AND tipo_documento_cruce='$documento_cruce'
	// 							AND codigo_cuenta='$cuenta'
	// 							AND id_empresa='$id_empresa'
	// 							AND activo=1
	// 						GROUP BY id_documento_cruce";
	// 	$queryAsientos = mysql_query($sqlAsientos,$link);
	// 	$idAsiento     = mysql_result($queryAsientos,0,'id');
	// 	$saldoCuentaDb = mysql_result($queryAsientos,0,'saldoCuenta');
	// 	$saldoCuenta   = $debe-$haber;

	// 	$absCuenta   = abs($saldoCuenta);
	// 	$absCuentaDb = abs($saldoCuentaDb);

	// 	//SINO EXISTE EL ASIENTO DE ESE DOCUMENTO RELACIONADO
	// 	if ($idAsiento=='') {
	// 		echo '<script>
	// 				alert(" La cuenta '.$cuenta.' de la '.$documento_cruce.' relacionada no existe en los asientos de ese documento\nPor favor digite una cuenta que genero el documento relacionado");
	// 				'.$script.'
	// 				setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
	// 			</script>';
	// 		exit;
	// 	}
	// 	else if(($saldoCuentaDb < 0 && $saldoCuenta < 0) || ($saldoCuentaDb > 0 && $saldoCuenta > 0)){
	// 		echo '<script>
	// 				alert("No se permite debitar o acreditar la misma cuenta en mas de una ocacion.");
	// 				'.$script.'
	// 				setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
	// 			</script>';
	// 		exit;
	// 	}
	// 	else if($absCuenta > $absCuentaDb){
	// 		echo '<script>
	// 				alert("El valor a cruzar es superior al registrado en el documento ('.$absCuentaDb.').");
	// 				'.$script.'
	// 				setTimeout(function(){ document.getElementById("cuenta'.$opcGrillaContable.'_'.$cont2.'").focus(); },100);
	// 			</script>';
	// 		exit;
	// 	}
	// }

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_tabla_referencia,$id_documento_cruce,$numeroDocumentoCruce,$tipoDocumentoCruce,$link){

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$numeroDocumentoCruce,$opcGrillaContable,$cont,$terceroGeneral,$id_tercero,$cuenta,'actualizar',$debe,$haber,$link); }

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
								debe='$debe',
								haber='$haber',
								id_tercero='$id_tercero',
								id_tabla_referencia='$id_tabla_referencia',
								id_documento_cruce='$id_documento_cruce',
								tipo_documento_cruce = '$tipoDocumentoCruce',
								numero_documento_cruce = $numeroDocumentoCruce
								WHERE $idTablaPrincipal=$id AND id=$idInsertCuenta";
		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);


		if ($queryUpdateArticulo) {

			$debe  = ($debe=='')? 0: $debe;
			$haber = ($haber=='')? 0: $haber;

			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

					//llamamos la funcion para recalcular el costo de la nota
					calcTotal'.$opcGrillaContable.'("'.$debeAnterior.'","'.$haberAnterior.'","eliminar");
					calcTotal'.$opcGrillaContable.'("'.$debe.'","'.$haber.'","agregar");
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo la cuenta"); </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION DE LA NOTA =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){
		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true{.}'; }
		else{ echo'false'; }
	}


	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($idDocumento,$opcGrillaContable,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link){
		// VALIDAR QUE NO TENGA DOCUMENTOS CRUCE
		validaDocumentoCruce($idDocumento,$id_empresa,$link);

		$sql   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo=mysql_result($query,0 ,'consecutivo');

		//PARA MODIFICAR EL DOCUMENTO PRIMERO DEBEMOS DESCONTABILIZARLO Y LUEGO REGRESARLO A ESTADO=0
		moverCuentasDocumento($idDocumento,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
		//YA QUE SE DIERON DE BAJA LOS ASIENTOS GENERADOS POR LA NOTA, PROCEDEMOS A ACTUALIZAR SU ESTADO A CERO
		if ($consecutivo>0) {
			$sql = "UPDATE $tablaPrincipal SET estado=3 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		}
		else{
			$sql = "UPDATE $tablaPrincipal SET activo=1 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		}

		$query = mysql_query($sql,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$idDocumento,'agregar',$link);

		if($query){
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
						 VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Liquidacion Provision',$id_sucursal,'$id_empresa')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "liquidacion_provision/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable : "'.$opcGrillaContable.'"
						}
					});

				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se actualizo el estado del documento!");</script>'; }

	}

 	//=========================== FUNCION PARA ELIMINAR LOS ARTICULOS RELACIONADOS A LA NOTA ==========================================================//
 	function eliminarArticuloRelacionado($id,$tipo,$opc='',$link){
 		//PRIMERO REVERSAMOS EL PROCESO QUE SE EJECUTO CUANDO SE GENERO EL MOVIMIENTO, ES DECIR, SI SALIERON ARTICULOS ENTONCES VUELVEN A INGRESAR, Y VICEVERSA

 		$cont=0;
 		//CONSULTA DEL ARTICULO Y LAS CANTIDADES INGRESADAS PARA PROCEDER A AGREGAR O ELIMINAR DEL INVENTARIO
		$sqlConsul   = "SELECT id_item,cantidad,id_bodega,id_sucursal FROM inventario_movimiento_notas WHERE id='$id' ";
		$queryConsul = mysql_query($sqlConsul,$link);

		$id_item     = mysql_result($queryConsul,0,'id_item');
		$cantidad    = mysql_result($queryConsul,0,'cantidad');
		$id_bodega   = mysql_result($queryConsul,0,'id_bodega');
		$id_sucursal = mysql_result($queryConsul,0,'id_sucursal');

 		if ($tipo=='entrada') {
 			//SI SE ENTRARON ARTICULOS CON LA NOTA, AL ELIMINAR EL REGISTRO, ENTONCES SE DEBEN SACAR
			$sqlInventario   = "UPDATE inventario_totales SET cantidad=cantidad-$cantidad WHERE id_item='$id_item' AND id_sucursal='$id_sucursal' AND id_ubicacion='$id_bodega' ";
			$queryInventario = mysql_query($sqlInventario,$link);
 			if (!$queryInventario) { $cont++; }

 		}
 		else if ($tipo=='salida') {
			//SI SE SACARON ARTICULOS CON LA NOTA, AL ELIMINAR EL REGISTRO, ENTONCES SE DEBEN INGRESAR
			$sqlInventario   = "UPDATE inventario_totales SET cantidad=cantidad+$cantidad WHERE id_item='$id_item' AND id_sucursal='$id_sucursal' AND id_ubicacion='$id_bodega' ";
			$queryInventario = mysql_query($sqlInventario,$link);
			if (!$queryInventario) { $cont++; }
 		}

		$sql   = "DELETE FROM inventario_movimiento_notas WHERE id='$id'";
		$query = mysql_query($sql,$link);
 		if (!$query) { $cont++; }

 		if ($opc=='return') { return $cont; }
 		else if ($cont==0 && $opc=='') { echo "true"; }
 		else if ($cont>0 && $opc=='') { echo "false"; }

 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$id_empresa,$tablaPrincipal,$link){

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal'  AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
						VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Liquidacion Provision',$id_sucursal,".$_SESSION['EMPRESA'].")";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota           : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'"
						}
					});
				</script>';
		}
		else{
			echo '<script>alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			return;
		}
 	}

 	//=========================== FUNCION PARA ACTUALIZARLE TIPO DE NOTA CONTABLE ====================================================================//
 	function actualizarTipoNota($id,$id_concepto,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$link){
 		// ELIMINAR  LAS CUENTAS RELACIONADAS
 		$sql="DELETE FROM $tablaCuentasNota WHERE $idTablaPrincipal=$id";
 		$query=mysql_query($sql,$link);

		$sql   = "UPDATE $tablaPrincipal SET id_concepto='$id_concepto' WHERE id='$id' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

 		if (!$query) {
 			echo '<script>
 					alert("Error!\nNo se actualizo el tipo de nota\nSi el problema persiste comuniquese con el administrador del sistema");
 					document.getElementById("selectConcepto").focus();
 				  </script>';
 			exit;
 		}
 		else{
 			echo '<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_1">';
 				cargaHeadInsertUnidadesConTercero('',1,'LiquidacionProvision');
 			echo '</div>';
 		}
 	}

 	//=========================== FUNCION PARA ACTUALIZAR LA FECHA DE LA NOTA =========================================================================//
 	function actualizarFechaNota($id,$fecha,$campo,$tablaPrincipal,$link){
		$sql   = "UPDATE $tablaPrincipal SET $campo='$fecha' WHERE id='$id' ";
		$query = mysql_query($sql,$link);

 		if (!$query) { echo '<script>alert("Error!\nNo se actualizo la fecha, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
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
 	function cargaCuentaNiif($idInsertCuenta,$idCuenta,$id_empresa,$opcGrillaContable,$cont,$link){
 		$sql="SELECT cuenta_niif,descripcion_niif,cuenta_puc FROM nomina_consolidacion_provision_cuentas WHERE id='$idInsertCuenta' AND id_puc='$idCuenta' AND id_empresa='$id_empresa' AND activo=1";
 		$query=mysql_query($sql,$link);
 		echo '<div style="width:100%;padding-top:10px;">
 				<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;">CUENTA NIIF DE '.mysql_result($query,0,'cuenta_puc').' COLGAAP</div>

 				<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5 0 5 3;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CUENTA</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5 0 5 3;font-weight: bold;font-size: 11px;">DESCRIPCION</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCuenta'.$opcGrillaContable.'('.$cont.',\'niif\')"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>

 					<div style="float:left;width:100px;border-right:1px solid #D4D4D4;padding: 5 0 5 3;">'.mysql_result($query,0,'cuenta_niif').'</div>
 					<div style="float:left;width:calc(100% - 110px);padding: 5 0 5 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.mysql_result($query,0,'descripcion_niif').'</div>

 				</div>
			</div>';
 	}

 	//=========================== FUNCION PARA ACTUALIZAR LA CUENTA NIIF DE UNA COLGAAP ====================================//
 	function actualizarNiif($id_niif,$cuenta,$idInsertCuenta,$idCuenta,$opcGrillaContable,$cont,$id_empresa,$link){
 		$sql="UPDATE nomina_consolidacion_provision_cuentas SET id_niif='$id_niif' WHERE  id='$idInsertCuenta' AND id_puc='$idCuenta' AND id_empresa='$id_empresa' AND activo=1";
 		$query=mysql_query($sql,$link);
 		if ($query){
 			echo 'true';
 		}
 		else{
 			echo 'false';
 		}
 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$link){
		$sql="SELECT estado,consecutivo FROM nota_contable_general WHERE id=$id_documento";
		$query=mysql_query($sql,$link);

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
 		        		    url     : "nota_general/bd/grillaContableBloqueada.php",
 		        		    scripts : true,
 		        		    nocache : true,
 		        		    params  :
 		        		    {
								id_nota           : '.$id_documento.',
								opcGrillaContable : "NotaGeneral",
 		        		    }
 		        		});

					</script>';
			exit;
		}
	}

 	//========================== FUNCION PARA CARGAR LA CUENTA NIIF CRUCE ==============================================================//
 	function cargaNiifCruce($id,$id_empresa,$opcGrillaContable,$link){
 		$sql="SELECT cuenta_colgaap_cruce,cuenta_niif_cruce,descripcion_cuenta_niif_cruce  FROM nomina_consolidacion_provision WHERE id='$id' AND id_empresa='$id_empresa' AND activo=1";
 		$query=mysql_query($sql,$link);
 		echo '<div style="width:100%;padding-top:10px;">
 				<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;">CUENTA NIIF DE '.mysql_result($query,0,'cuenta_colgaap_cruce').' COLGAAP</div>

 				<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5 0 5 3;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CUENTA</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5 0 5 3;font-weight: bold;font-size: 11px;">DESCRIPCION</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCuentaCruceNiif()"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>

 					<div style="float:left;width:100px;border-right:1px solid #D4D4D4;padding: 5 0 5 3;">'.mysql_result($query,0,'cuenta_niif_cruce').'</div>
 					<div style="float:left;width:calc(100% - 110px);padding: 5 0 5 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.mysql_result($query,0,'descripcion_cuenta_niif_cruce').'</div>

 				</div>
			</div>';
 	}

	//===================== FUNCION PARA ACTUALIZAR LA CUENTA DE CRUCE DEL DOCUMENTO ===================================//
	function actualizarCuentaCruce($opcGrillaContable,$id,$id_cuenta,$id_empresa,$id_sucursal,$link){
		//CONSULTAR LOS DATOS DE LA CUENTA COLGAAP Y VERIFICAR SI TIENE CONFIGURADA UNA CUENTA NIIF
		$sql   = "SELECT cuenta,descripcion,cuenta_niif FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_cuenta";
		$query = mysql_query($sql,$link);

		$cuenta      = mysql_result($query,0,'cuenta');
		$descripcion = mysql_result($query,0,'descripcion');
		$cuenta_niif = mysql_result($query,0,'cuenta_niif');

		if ($cuenta>0) {
			$sql="UPDATE nomina_consolidacion_provision SET id_cuenta_colgaap_cruce='$id_cuenta',cuenta_colgaap_cruce='$cuenta',descripcion_cuenta_colgaap_cruce='$descripcion',cuenta_niif_cruce='$cuenta_niif'
					WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query=mysql_query($sql,$link);
			if (!$query) {
				echo '<script>
						alert("Se produjo un error y no se guardo la cuenta, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
						document.getElementById("cuenta_cruce'.$opcGrillaContable.'").value="";
					</script>';
			}
			else if ($cuenta_niif==0 || $cuenta_niif=='') {
				echo '<script>
						alert("La cuenta colgaap no tiene cuenta niif configurada, configurela manualmente");
					</script>';
			}
			else{
				echo '<script>
						document.getElementById("cuenta_cruce'.$opcGrillaContable.'").value="'.$cuenta.'";
					</script>';
			}

		}
		else{
			echo '<script>alert("No existe la cuenta colgaap");</script>';
		}
	}

	function actualizarCuentaCruceNiif($opcGrillaContable,$id,$cuenta,$id_empresa,$id_sucursal,$link){
		$sql="UPDATE nomina_consolidacion_provision SET cuenta_niif_cruce='$cuenta'
				WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>
					alert("Se produjo un error y no se guardo la cuenta, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>';
		}
		else{
			echo '<script>
					Win_Ventana_cambiar_cuenta_niif.close();
					Win_Ventana_buscar_cuenta.close();
				</script>';
		}
	}

	function ventana_enviar_email($opcGrillaContable,$id_nota,$consecutivo,$fecha_inicio,$fecha_final,$concepto,$id_empresa,$id_sucursal,$link){
		//SELECCIONAR LOS EMPLEADOS RELACIONADOS EN LA LIQUIDACION DE LA PROVISION
		$sql="SELECT *,SUM(debe-haber) AS saldo FROM nomina_consolidacion_provision_cuentas
					WHERE activo=1 AND
					id_consolidacion_provision = $id_nota AND
					id_empresa               = $id_empresa AND
					id_documento_cruce       > 0 AND
					tipo_documento_cruce     = 'LN'
					GROUP BY id_documento_cruce,id_tercero";

		$query=mysql_query($sql,$link);

		echo '<div style="width:450px;margin:auto;overflow:hidden;height: 370px;border:1px solid #d4d4d4;">
				<div class="contenedorHeadArticulos">
						<div class="headArticulos">
							<div class="label'.$opcGrillaContable.'" style="width:150px !important;">Empleado</div>
							<div class="label'.$opcGrillaContable.'" style="width:80px;">Consecutivo</div>
							<div class="label'.$opcGrillaContable.' " style="width:150px;">Concepto</div>

						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">

						';

		while ($row=mysql_fetch_array($query)) {
			echo '<div class="campo" style="width:150px !important; overflow:hidden;background-color:#FFF;">
					<div style="float:left; margin:3px 0 0 2px;width: 100%;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.$row['tercero'].'</div>
				</div>
				<div class="campo" style="width:80px !important; overflow:hidden;background-color:#FFF;">
					<div style="float:left; margin:3px 0 0 2px;">'.$row['numero_documento_cruce'].'</div>
				</div>
				<div class="campo" style="width:150px !important; overflow:hidden;background-color:#FFF;">
					<div style="float:left; margin:3px 0 0 2px;width: 100%;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.$concepto.'</div>
				</div>
				<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;"><img src="img/enviaremail_true.png" onclick="enviarVolanteUnicoEmpleadoLiquidacion(\''.$row['id_tercero'].'\',\''.$consecutivo.'\',\''.$concepto.'\',\''.$fecha_inicio.'\',\''.$fecha_final.'\',\''.$row['tercero'].'\',\''.$row['saldo'].'\')"></div>
				</div>';
		}

		echo '</div>
			</div>
			</div>';
	}

	//==========================  FUNCION PARA ENVIAR EL VOLANTE A UN SOLO EMPLEADO =================================//
	function enviarVolanteUnicoEmpleado($id_tercero,$consecutivo,$fecha_inicio,$fecha_final,$concepto,$saldo,$id_empresa,$link){
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		include("../../../../misc/MPDF54/mpdf.php");
		include('enviar_volante_email.php');

		$mail  = new PHPMailer();

		$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
		$queryConexion = mysql_query ($sqlConexion,$link);
		if($row_consulta= mysql_fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_smtp'];
			// $pass          = $row_consulta['password'];
			$pass          = $row_consulta['password'];
			// $user          = $row_consulta['user_name'];
			$user          = $row_consulta['correo'];
			$puerto        = $row_consulta['puerto'];
			// $servidor      = $row_consulta['servidor_SMTP'];
			$servidor      = $row_consulta['servidor'];
			// $from          = $row_consulta['from'];
			$from          = $row_consulta['correo'];
			$autenticacion = $row_consulta['autenticacion'];
		}

		if ($user=='') {
			echo '<script>
					alert("No exite ninguna configuracion de correo SMTP!\nConfigure el correo desde el panel de control en el boton configuracion SMTP");
					document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
				</script>';
			exit;
		}

		$mail->IsSMTP();
		// $mail->SMTPDebug = true;

		$mail->SMTPAuth   = true;                  				// enable SMTP authentication
		$mail->SMTPSecure = $seguridad;                         // sets the prefix to the servier
		$mail->Host       = $servidor;      				    // sets GMAIL as the SMTP server
		$mail->Port       = $puerto;                            // set the SMTP port

		$mail->Username   = $user; // GMAIL username
		$mail->Password   = $pass; // GMAIL password

		$mail->From       = $from;
		$mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
		$mail->Subject    = "Volante de Pago de Nomina";
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap

		if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
		if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
		if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
		if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
		else{ $MS= 60  ; $MD = 10;$MI = 15;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
		if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}




		// echo "string";
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		$fun=imprimirEnviaVolante($id_tercero,$consecutivo,$fecha_inicio,$fecha_final,$concepto,$saldo,$mail,$mpdf,$link);
		if ($fun=='true') {
			$sql="UPDATE nomina_planillas_empleados SET email_enviado='true' WHERE activo=1 AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND id_empleado=$id_empleado AND id_empresa=$id_empresa";
			$query=mysql_query($sql,$link);
			echo '<script>
				// document.getElementById("imgEmail_'.$id_contrato.'").setAttribute("src","img/enviaremail_true.png");
				// document.getElementById("imgEmail_'.$id_contrato.'").setAttribute("title","Reenviar Volante por email");
				document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
			</script>';
		}
		else{
			echo '	'.$id_tercero.' - '.$consecutivo.' - '.$fecha_inicio.' - '.$fecha_final.' - '.$concepto.' - '.$saldo.' -
					$seguridad = "'.$seguridad.'"-
					$pass = "'.$pass.'"
					$user = "'.$user.'"
					$puerto = "'.$puerto.'"
					$servidor = "'.$servidor.'"
					$from = "'.$from.'"
					$autenticacion = "'.$autenticacion.'"
				<script>
					alert("Error\nNo se envio el email, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
				</script>';
		}
	}

	// FUNCION PARA VALIDAR QUE NO TENGA UN DOCUMENTO CRUCE RELACIONADO
	function validaDocumentoCruce($idDocumento,$id_empresa,$link){
		$id_sucursal=$_SESSION['SUCURSAL'];
		$sqlNota    = "SELECT consecutivo_documento, tipo_documento FROM asientos_colgaap WHERE id_documento_cruce = '$idDocumento' AND tipo_documento_cruce='LP' AND tipo_documento<>'LP' AND activo=1 AND id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';
		while ($row=mysql_fetch_array($queryNota)) {
			$doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento'];
		}
		if ($doc_cruces != '') { echo '<script>alert("Error!\nEste documento tienen relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nPor favor anule los documentos para poder modificar el documento");</script>'; exit; }
	}

	// CARGAR TODO LO PROVISIONADO DE UNA PROVISION EN UN RANGO DE TIEMPO
	function cargarTodasProvisiones($id,$id_concepto,$fecha_inicial,$fecha_final,$id_empresa,$sucursal,$MyFiltroBusqueda,$link){
		// CONSULTAR LOS REGISTROS QUE YA SE HAN INSERTADO
		$sql="SELECT id_tabla_referencia FROM nomina_consolidacion_provision_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND id_consolidacion_provision=$id";
		$query=mysql_query($sql,$link);
		$whereId='';
		while ($row=mysql_fetch_array($query)) {
			$whereId.=' AND id<>'.$row['id_tabla_referencia'];
		}

		// FILTRO DE LA GRILLA
		$whereFiltro='';
		if ($MyFiltroBusqueda!='') {
			$whereFiltro=" AND(cuenta_colgaap LIKE '%$MyFiltroBusqueda%'
							 OR cuenta_niif LIKE '%$MyFiltroBusqueda%'
							 OR tercero LIKE '%$MyFiltroBusqueda%'
							 OR empleado_cruce LIKE '%$MyFiltroBusqueda%'
							 OR debito LIKE '%$MyFiltroBusqueda%'
							 OR credito LIKE '%$MyFiltroBusqueda%'
							 OR total_sin_abono_provision LIKE '%$MyFiltroBusqueda%'
							 OR documento_tercero LIKE '%$MyFiltroBusqueda%'
							 OR documento_empleado_cruce LIKE '%$MyFiltroBusqueda%') ";
		}

		$sql="SELECT * FROM nomina_planillas_empleados_contabilizacion
				WHERE activo=1
				AND id_empresa=$id_empresa
				AND fecha_inicio_planilla>='$fecha_inicial'
				AND fecha_final_planilla<='$fecha_final'
				AND id_concepto=$id_concepto
				AND total_sin_abono_provision>0
				$whereId $whereFiltro";
		$query=mysql_query($sql,$link);
		$valueInsert='';
		while ($row=mysql_fetch_array($query)) {
			$debito  = ($row['credito']>0)? $row['total_sin_abono_provision'] : 0 ;
			$credito = ($row['debito']>0)? $row['total_sin_abono_provision'] : 0 ;
			$valueInsert.="(
							'$id',
							'$row[id_cuenta_colgaap]',
							'$debito',
							'$credito',
							'$row[id_tercero]',
							'$row[id]',
							'LP',
							'$row[id_planilla]',
							'$row[consecutivo_planilla]',
							'$id_empresa'
							),";
		}

		$valueInsert = substr($valueInsert, 0, -1);

		$sql="INSERT INTO nomina_consolidacion_provision_cuentas(
						id_consolidacion_provision,
						id_puc,
						debe,
						haber,
						id_tercero,
						id_tabla_referencia,
						tipo_documento_cruce,
						id_documento_cruce,
						numero_documento_cruce,
						id_empresa)
						VALUES
			  			$valueInsert";
		$query=mysql_query($sql,$link);

		include ('functions_body_article.php');

		echo cargaArticulosSaveConTercero($id,'',0,'LiquidacionProvision','nomina_consolidacion_provision_cuentas','id_consolidacion_provision',$link);
	}

?>

