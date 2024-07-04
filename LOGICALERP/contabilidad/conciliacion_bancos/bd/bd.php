<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		if ($opc!='cancelarDocumento' && $opc!='restaurarDocumento' && $opc!='modificarDocumentoGenerado' && $opc!='eliminarArticuloRelacionado') {
			verificaEstadoDocumento($id,$link);
		}
	}

	switch ($opc) {

		case 'buscarCliente':
			buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrilla,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link);
			break;

		case 'guardarTerceroComoNotaInterna':
			guardarTerceroComoNotaInterna($opcGrilla,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$accion,$link);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'buscarCuenta':
			buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$id_sucursal,$idCliente,$opcGrilla,$link);
			break;

		case 'deleteCuenta':
			deleteCuenta($cont,$id,$idCuenta,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'retrocederCuenta':
		 	retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'validaNota':
			validaNota($id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$id_sucursal,$id_tercero,$notaCruce,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idDocumento,$opcGrilla,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'guardarCuenta':
			guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link);
			break;

		case 'actualizaCuenta':
			actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link);
			break;

		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrilla,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($idDocumento,$opcGrilla,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'actualizarTipoNota':
			actualizarTipoNota($id,$idTipoNota,$notaCruce,$tablaPrincipal,$id_empresa,$link);
			break;

		case 'actualizarFechaNota':
			actualizarFechaNota($id,$fecha,$tablaPrincipal,$link);
			break;

		case 'eliminarArticuloRelacionado':
			eliminarArticuloRelacionado($id,$tipo,$accion,$link);
			break;

		case 'ventana_buscar_documento_cruce':
			ventana_buscar_documento_cruce($cont,$opcGrilla,$carpeta,$id_empresa,$id_sucursal,$link);
			break;

		case 'cargaConfiguracionCuenta':
			cargaConfiguracionCuenta($id_documento,$idInsertCuenta,$idCuenta,$id_empresa,$opcGrilla,$cont,$link);
			break;

		case 'actualizarNiif':
			actualizarNiif($id_niif,$cuenta,$idInsertCuenta,$idCuenta,$opcGrilla,$cont,$id_empresa,$link);
			break;

		case 'actualizarCcos':
			actualizarCcos($idInsertCuenta,$idCuenta,$opcGrilla,$id_centro_costos,$codigo_centro_costos,$id_documento,$id_empresa,$link);
			break;

		case 'cargaHeadInsertUnidadesConTercero':
			cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrilla);
			break;

		case 'validarCuenta':
			validarCuenta($cuenta,$id_empresa,$link);
			break;

	}

	//=========================== FUNCION PARA BUSCAR UN CLIENTE ===============================================================================//
	function buscarCliente($id,$codCliente,$tipoDocumento,$id_empresa,$opcGrilla,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$inputId,$link){

		if ($inputId=='nitCliente'.$opcGrilla) {
			$where   ='numero_identificacion="'.$codCliente.'" AND tipo_identificacion="'.$tipoDocumento.'"';
			$mensaje = 'alert("'.$tipoDocumento.' de tercero no establecido");';
		}
		else if ($inputId=='codigoTercero'.$opcGrilla) {
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
					document.getElementById("codigoTercero'.$opcGrilla.'").value = "'.$codigoTercero.'";
					document.getElementById("tipoDocumento'.$opcGrilla.'").value = "'.$tipoNitTercero.'";
					document.getElementById("nitCliente'.$opcGrilla.'").value    = "'.$nitTercero.'";
				</script>';
			exit;
		}
		else if ($inputId=='nitCliente'.$opcGrilla) {
			$camposInsert = "codigo_tercero = '$codigo ',
							numero_identificacion_tercero = '$codCliente',
							tipo_identificacion_tercero = '$tipoDocumento'";
		}
		else if ($inputId=='codigoTercero'.$opcGrilla) {
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
				document.getElementById("codigoTercero'.$opcGrilla.'").value = "'.$codigo.'";
				document.getElementById("tipoDocumento'.$opcGrilla.'").value = "'.$tipoNit.'";
				document.getElementById("nitCliente'.$opcGrilla.'").value    = "'.$nit.'";
				document.getElementById("nombreCliente'.$opcGrilla.'").value = "'.$nombre.'";

				id_cliente_'.$opcGrilla.'   = "'.$idTercero.'";
				nitCliente'.$opcGrilla.'    = "'.$nit.'";
				nombreCliente'.$opcGrilla.' = "'.$nombre.'";

				id_tipo_nota = document.getElementById("selectTipoNota").value;
				if(arrayTipoNota[id_tipo_nota] == "Si"){ document.getElementById("contenedorNotaContable").setAttribute("class","contenedorNotaContableCruce"); }
				else{ document.getElementById("contenedorNotaContable").setAttribute("class","contenedorNotaContable"); }
			</script>';
	}

	//=========================== FUNCION PARA GUARDAR EL TERCERO COMO NOTA INTERNA =============================================================//
	function guardarTerceroComoNotaInterna($opcGrilla,$id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$accion,$link){

		if ($accion=='agregar') {
			$sqlUpdate = "UPDATE $tablaPrincipal
						SET
						id_tercero     = '',
						tercero 	   = 'NOTA INTERNA',
						codigo_tercero = '',
						numero_identificacion_tercero = '',
						tipo_identificacion_tercero   = ''
						WHERE id='$id'";
			$id_cliente    = 'NI';
			$codigoTercero = '';
			$nitCliente    = 'NI';
			$nombreCliente = 'NOTA INTERNA';

			$body= cargaHeadInsertUnidadesConTercero('return',1,$opcGrilla);

		}
		else if ($accion=='eliminar'){
			$sqlUpdate = "UPDATE $tablaPrincipal
							SET id_tercero     = '',
								tercero 	   = '',
								codigo_tercero = '',
								numero_identificacion_tercero = '',
								tipo_identificacion_tercero   = ''
							WHERE id='$id'";
			$id_cliente    = '';
			$codigoTercero = '';
			$nitCliente    = '';
			$nombreCliente = '';

			$body=cargaHeadInsertUnidades('return',1,$opcGrilla);
		}
		//ACTUALIZAMOS EL TERCERO DE LA NOTA
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//ELIMINAMOS LAS CUENTAS QUE SE PUDIERON AGREGAR EN EL PROCESO, PARA DEJAR LA NOTA EN BLANCO
		$sql   = "DELETE FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' ";
		$query = mysql_query($sql,$link);

		if($queryUpdate){
			echo'<script>

					id_cliente_'.$opcGrilla.'                                    = "";
					document.getElementById("codigoTercero'.$opcGrilla.'").value = "";
					document.getElementById("nitCliente'.$opcGrilla.'").value    = "'.$nitCliente.'";
					document.getElementById("nombreCliente'.$opcGrilla.'").value = "'.$nombreCliente.'";
					nitCliente'.$opcGrilla.'                                     = "'.$nitCliente.'";
					nombreCliente'.$opcGrilla.'                                  = "'.$nombreCliente.'";

				</script>'.$body;
		}
		else{ echo '<script>alert("Error!\nNo se actualizo la nota como interna");</script>'; }
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrilla){
		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrilla.'">
							<div class="labelNotaGeneral" style="width:40px !important;"></div>
							<div class="labelNotaGeneral" style="width:95px;">Cuenta</div>
							<div class="labelNotaGeneral" style="width:50%">Descripcion</div>
							<div class="labelNotaGeneral" >Debito</div>
							<div class="labelNotaGeneral" >Credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrilla.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrilla.'\')">
						<div class="bodyDivArticulos'.$opcGrilla.'" id="bodyDivArticulos'.$opcGrilla.'_'.$cont.'">
							'.cargaDivsInsertUnidades('return',$cont,$opcGrilla).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrilla.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrilla.'"  onKeydown="inputObservacion'.$opcGrilla.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
							<div class="label" style="width:8px;">$</div>
							<div class="labelTotal" id="debitoAcumulado'.$opcGrilla.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
							<div class="label" style="width:8px;">$</div>
							<div class="labelTotal" id="creditoAcumulado'.$opcGrilla.'">0</div>
						</div>

						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL (DEBITO - CREDITO)</div>
							<div class="label" style="width:8px;">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrilla.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("debitoAcumulado'.$opcGrilla.'").innerHTML  = parseFloat(debitoAcumulado'.$opcGrilla.').toFixed(2);
					document.getElementById("creditoAcumulado'.$opcGrilla.'").innerHTML = parseFloat(creditoAcumulado'.$opcGrilla.').toFixed(2);
					document.getElementById("totalAcumulado'.$opcGrilla.'").innerHTML   = parseFloat(total'.$opcGrilla.').toFixed(2);
				</script>';
		echo $head;
	}
	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrilla){
		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrilla.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrilla.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrilla.'(event,this);" />
				</div>

				<div class="campoNotaGeneral" style="width:50%;" ><input type="text" id="descripcion'.$opcGrilla.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrilla.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campoNotaGeneral" ><input type="text" id="debito'.$opcGrilla.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'double\',\''.$cont.'\');" style="text-align:left;" /></div>
				<div class="campoNotaGeneral" ><input type="text" id="credito'.$opcGrilla.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrilla.'(event,this,'.$cont.');"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewCuenta'.$opcGrilla.'('.$cont.')" id="divImageSave'.$opcGrilla.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrilla.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrilla.'('.$cont.')" id="divImageDeshacer'.$opcGrilla.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrilla.'_'.$cont.'"></div>
					<div onclick="deleteCuenta'.$opcGrilla.'('.$cont.')" id="deleteCuenta'.$opcGrilla.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrilla.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertCuenta'.$opcGrilla.'_'.$cont.'" value="0" />
				<input type="hidden" id="idTercero'.$opcGrilla.'_'.$cont.'" value="0" />

				<script>
					document.getElementById("cuenta'.$opcGrilla.'_'.$cont.'").focus();
				</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidadesConTercero($formaConsulta,$cont,$opcGrilla){
		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos">
							<div class="labelNotaGeneral" style="width:40px !important;"></div>
							<div class="labelNotaGeneral" style="width:95px;">Cuenta</div>
							<div class="labelNotaGeneral campoDescripcion">Descripcion</div>
							<div class="labelNotaGeneral campoDescripcion">Tercero</div>
							<div class="labelNotaGeneral opcionalCruce" >Doc. Cruce</div>
							<div class="labelNotaGeneral opcionalCruce" >N.Doc.Cruce</div>
							<div class="labelNotaGeneral" >Debito</div>
							<div class="labelNotaGeneral" >Credito</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrilla.'">
						<div class="bodyDivArticulos'.$opcGrilla.'" id="bodyDivArticulos'.$opcGrilla.'_'.$cont.'">
							'.cargaDivsInsertUnidadesConTercero('return',$cont,$opcGrilla).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrilla.'" >
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrilla.'"  onKeydown="inputObservacion'.$opcGrilla.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
							<div class="labelSimbolo" >$</div>
							<div class="labelTotal" id="debitoAcumulado'.$opcGrilla.'">0</div>
						</div>
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="creditoAcumulado'.$opcGrilla.'">0</div>
						</div>

						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL (DEBITO - CREDITO)</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal"  id="totalAcumulado'.$opcGrilla.'">0</div>
						</div>
					</div>
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("debitoAcumulado'.$opcGrilla.'").innerHTML  = parseFloat(debitoAcumulado'.$opcGrilla.').toFixed(2);
					document.getElementById("creditoAcumulado'.$opcGrilla.'").innerHTML = parseFloat(creditoAcumulado'.$opcGrilla.').toFixed(2);
					document.getElementById("totalAcumulado'.$opcGrilla.'").innerHTML   = parseFloat(total'.$opcGrilla.').toFixed(2);
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidadesConTercero($formaConsulta,$cont,$opcGrilla){
		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrilla.'_'.$cont.'"></div>
				</div>

				<div class="campoNotaGeneral" style="width:95px;">
					<input type="text" id="cuenta'.$opcGrilla.'_'.$cont.'" onKeyup="buscarCuenta'.$opcGrilla.'(event,this);" />
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="descripcion'.$opcGrilla.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarCuenta'.$opcGrilla.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
					<img src="img/buscar20.png" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;"/>
				</div>

				<div class="campoNotaGeneral campoDescripcion"><input type="text" id="tercero'.$opcGrilla.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="iconBuscarArticulo" >
					<img src="img/buscar20.png" id="imgBuscarTercero'.$opcGrilla.'_'.$cont.'" style="margin-top: 3px; margin-left: 2px; height: 16px; width: 16px;" onclick="buscarVentanaTercero'.$opcGrilla.'('.$cont.')" title="Buscar Tercero" />
				</div>

				<div class="campoNotaGeneral opcionalCruce">
				 	<input type="text" id="documentoCruce'.$opcGrilla.'_'.$cont.'" readonly style="text-align:left;" >
				 </div>
				 <div class="iconBuscarArticulo opcionalCruce">
					<img onclick="ventanaBuscarDocumentoCruce'.$opcGrilla.'('.$cont.');" id="imgBuscarDocumentoCruce_'.$cont.'" title="Buscar Documento Cruce" src="img/buscar20.png" />
				</div>

				<div class="campoNotaGeneral opcionalCruce">
					<input title="Prefijo" type="text" readonly id="prefijoDocumentoCruce'.$opcGrilla.'_'.$cont.'" class="inputPrefijoNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'mayuscula\',\''.$cont.'\');"   />
					-
					<input title="Numero" type="text" readonly id="numeroDocumentoCruce'.$opcGrilla.'_'.$cont.'" class="inputNumeroNotaContableCruce" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'\',\''.$cont.'\');"  />
				</div>

				<div class="campoNotaGeneral" ><input type="text" id="debito'.$opcGrilla.'_'.$cont.'"  onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'double\',\''.$cont.'\');"  /></div>
				<div class="campoNotaGeneral" ><input type="text" id="credito'.$opcGrilla.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrilla.'(event,this,'.$cont.');"/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewCuenta'.$opcGrilla.'('.$cont.')" id="divImageSave'.$opcGrilla.'_'.$cont.'" title="Guardar Cuenta" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrilla.'_'.$cont.'"/></div>
					<div onclick="retrocederCuenta'.$opcGrilla.'('.$cont.')" id="divImageDeshacer'.$opcGrilla.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrilla.'_'.$cont.'"></div>
					<div onclick="deleteCuenta'.$opcGrilla.'('.$cont.')" id="deleteCuenta'.$opcGrilla.'_'.$cont.'" title="Eliminar Cuenta" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
					<div onclick="cambiaCuentaNiif'.$opcGrilla.'('.$cont.')" id="configurarCuenta'.$opcGrilla.'_'.$cont.'" title="Configurar Cuenta Niif" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/config16.png" /></div>
				</div>

				<input type="hidden" id="idCuenta'.$opcGrilla.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertCuenta'.$opcGrilla.'_'.$cont.'" value="0" />
				<input type="hidden" id="idTercero'.$opcGrilla.'_'.$cont.'" value="0" />
				<input type="hidden" id="idDocumentoCruce'.$opcGrilla.'_'.$cont.'" value="0" />

				<script>
					document.getElementById("cuenta'.$opcGrilla.'_'.$cont.'").focus();
				</script>
				';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
	function buscarCuenta($campo,$cuenta,$contFila,$id_empresa,$id_sucursal,$idCliente,$opcGrilla,$link){
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
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").focus(); },100);
				</script>';
			exit;
		}
		else if (strlen ($cuenta)>5 AND strlen ($cuenta)<7) {
			$where   = ' LEFT(codigo_cuenta,6) = \''.$cuenta.'\'';
			$mensaje = 'Exite(n) una(s) cuenta(s) de esta, con 8 digitos, no se puede utilizar esta de 6\nDigite apartir de 8 digitos para continuar';
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
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").focus(); },100);
					document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value    = "0";
					document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").value      = "";
					document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").value = "";
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
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").focus(); },100);
					document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value = "0";
					document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").value   = "";
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

					document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value    = "'.$id_cuenta.'";
					document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").value = "'.$descripcion_cuenta.'";

					document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").blur();
					setTimeout(function(){ document.getElementById("debito'.$opcGrilla.'_'.$contFila.'").focus(); }, 100);
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
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").focus(); },100);
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

						document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value    = "'.$id.'";
						document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").value = "'.$descripcion.'";

						document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").blur();
						setTimeout(function(){ document.getElementById("debito'.$opcGrilla.'_'.$contFila.'").focus();},100);
					</script>';
			}
			else{
				echo'<script>
						alert("El Numero de cuenta '.$cuenta.' no se encuentra asignado en el PUC de la empresa");
						setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").focus(); },100);
						document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value    ="0";
						document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").value      ="";
						document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").value ="";
					</script>';
			}
		}
	}


	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederCuenta($id,$idCuentaInsert,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$link){
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
					'document.getElementById("imgBuscarTercero'.$opcGrilla.'_'.$cont.'").setAttribute("src","img/eliminar.png");
        			document.getElementById("imgBuscarTercero'.$opcGrilla.'_'.$cont.'").setAttribute("title","Eliminar Tercero");
        			document.getElementById("imgBuscarTercero'.$opcGrilla.'_'.$cont.'").setAttribute("onclick"," eliminaTercero'.$opcGrilla.'('.$cont.')");'

				:	'document.getElementById("imgBuscarTercero'.$opcGrilla.'_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarTercero'.$opcGrilla.'_'.$cont.'").setAttribute("title","Buscar Tercero");
        			document.getElementById("imgBuscarTercero'.$opcGrilla.'_'.$cont.'").setAttribute("onclick","buscarVentanaTercero'.$opcGrilla.'('.$cont.')");';

		echo 	($numero_documento_cruce != "")?
					'document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("src","img/eliminar.png");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("title","Eliminar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("onclick"," eliminaDocumentoCruce'.$opcGrilla.'('.$cont.')");'
				:
					'document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("src","img/buscar20.png");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("title","Buscar Documento Cruce");
        			document.getElementById("imgBuscarDocumentoCruce_'.$cont.'").setAttribute("onclick","ventanaBuscarDocumentoCruce'.$opcGrilla.'('.$cont.')");';

		echo'	document.getElementById("idCuenta'.$opcGrilla.'_'.$cont.'").value                 = "'.$idCuenta.'";
				document.getElementById("cuenta'.$opcGrilla.'_'.$cont.'").value                   = "'.$cuenta.'";
				document.getElementById("descripcion'.$opcGrilla.'_'.$cont.'").value              = "'.$descripcion.'";
				document.getElementById("tercero'.$opcGrilla.'_'.$cont.'").value                  = "'.$tercero.'";
				document.getElementById("documentoCruce'.$opcGrilla.'_'.$cont.'").value           = "'.$tipo_documento_cruce.'";
				document.getElementById("prefijoDocumentoCruce'.$opcGrilla.'_'.$cont.'").value    = "'.$prefijo_documento_cruce.'";
				document.getElementById("numeroDocumentoCruce'.$opcGrilla.'_'.$cont.'").value     = "'.$numero_documento_cruce.'";
				document.getElementById("idDocumentoCruce'.$opcGrilla.'_'.$cont.'").value      	  = "'.$id_documento_cruce.'";
				document.getElementById("debito'.$opcGrilla.'_'.$cont.'").value                   = "'.$debe.'";
				document.getElementById("credito'.$opcGrilla.'_'.$cont.'").value                  = "'.$haber.'";
				document.getElementById("divImageSave'.$opcGrilla.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrilla.'_'.$cont.'").style.display = "none";
			</script>';
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteCuenta($cont,$id,$idCuenta,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$link){
		$sqlDelete   = "DELETE FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id='$idCuenta'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar la cuenta, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo'<script>
					(document.getElementById("bodyDivArticulos'.$opcGrilla.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrilla.'_'.$cont.'"));
				</script>';
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA NOTA ==========================================================================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link){

	 	//MOVEMOS LAS CUENTAS
		moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'agregar',$id_tercero,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$id,'eliminar',$link);

		$fecha = date("Y-m-d");
		$sql   = "UPDATE $tablaPrincipal SET estado=1,fecha_finalizacion='$fecha' WHERE id='$id' AND activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if (!$query){
			echo '<script>alert("Error!\nNo se pudo actualizar la nota, vuelva a intentarlo\nSi el problema persite comuniquese con el administrador del sistema");</script>';
			moverDocumentosSaldos($id_empresa,$id,'agregar',$link);
			moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',$id_tercero,$link);
		}
		else{
			$sqlConsecutivo        = "SELECT consecutivo FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
			$queryConsecutivo      = mysql_query($sqlConsecutivo,$link);
			$consecutivo_documento = mysql_result($queryConsecutivo,0,'consecutivo');

			$sqlUpdate = "UPDATE asientos_colgaap
							SET consecutivo_documento='$consecutivo_documento',
								numero_documento_cruce='$consecutivo_documento'
						 	WHERE id_documento='$id'
						 		AND tipo_documento='NCG'
						 		AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);

			$sqlUpdate = "UPDATE asientos_niif
							SET consecutivo_documento='$consecutivo_documento',
								numero_documento_cruce='$consecutivo_documento'
						 	WHERE id_documento='$id'
						 		AND tipo_documento='NCG'
						 		AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);
		}

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
					VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Nota Contable General',$id_sucursal,'$id_empresa')";
		$queryLog = mysql_query($sqlLog,$link);

	   	echo'<script>
   				Ext.get("contenedor_NotaGeneral").load({
 		            url     : "nota_general/bd/grillaContableBloqueada.php",
 		            scripts : true,
 		            nocache : true,
 		            params  :
 		            {
						id_nota           : '.$id.',
						opcGrilla : "NotaGeneral",
 		            }
 		        });
			</script>';
	}

	//============================= FUNCION PARA MOVER LOS SALDOS (ABONOS) DE LOS DOCUMENTOS RELACIONADOS EN FC Y FV ================================//
	function moverDocumentosSaldos($id_empresa,$id_documento,$accion,$link){

		if ($accion=='agregar') {
			$sqlCompra="UPDATE compras_facturas AS CF,
						 (
							SELECT
								NC.*
							FROM
								nota_contable_general_cuentas AS NC
							WHERE
								NC.id_nota_general = $id_documento
							AND NC.tipo_documento_cruce = 'FC'
							AND NC.id_documento_cruce <> ''
						) AS NC
						SET CF.total_factura_sin_abono = CF.total_factura_sin_abono + (NC.debe + NC.haber)
						WHERE
							CF.id = NC.id_documento_cruce
						AND CF.cuenta_pago = NC.cuenta_puc
						AND CF.id_empresa = $id_empresa";

			$sqlVenta="UPDATE ventas_facturas AS VF,
						 (
							SELECT
								NC.*
							FROM
								nota_contable_general_cuentas AS NC
							WHERE
								NC.id_nota_general =  $id_documento
							AND NC.tipo_documento_cruce = 'FV'
							AND NC.id_documento_cruce <> ''
						) AS NC
						SET VF.total_factura_sin_abono = VF.total_factura_sin_abono + (NC.debe + NC.haber)
						WHERE
							VF.id = NC.id_documento_cruce
						AND VF.cuenta_pago = NC.cuenta_puc
						AND VF.id_empresa = $id_empresa";

		}
		else if ($accion=='eliminar') {

			$sqlCompra="UPDATE compras_facturas AS CF,
						 (
							SELECT
								NC.*
							FROM
								nota_contable_general_cuentas AS NC
							WHERE
								NC.id_nota_general = $id_documento
							AND NC.tipo_documento_cruce = 'FC'
							AND NC.id_documento_cruce <> ''
						) AS NC
						SET CF.total_factura_sin_abono = CF.total_factura_sin_abono - (NC.debe + NC.haber)
						WHERE
							CF.id = NC.id_documento_cruce
						AND CF.cuenta_pago = NC.cuenta_puc
						AND CF.id_empresa = $id_empresa";

			$sqlVenta="UPDATE ventas_facturas AS VF,
						 (
							SELECT
								NC.*
							FROM
								nota_contable_general_cuentas AS NC
							WHERE
								NC.id_nota_general =  $id_documento
							AND NC.tipo_documento_cruce = 'FV'
							AND NC.id_documento_cruce <> ''
						) AS NC
						SET VF.total_factura_sin_abono = VF.total_factura_sin_abono - (NC.debe + NC.haber)
						WHERE
							VF.id = NC.id_documento_cruce
						AND VF.cuenta_pago = NC.cuenta_puc
						AND VF.id_empresa = $id_empresa";

		}

		//EJECUTAR LOS QUERY
		$queryCompra = mysql_query($sqlCompra,$link);
		$queryVenta  = mysql_query($sqlVenta,$link);

		if (!$queryCompra) { echo '<script>alert("Error!\nNo se actualizo el saldo de los documentos FC");</script>'; }
		if (!$queryVenta) { echo '<script>alert("Error!\nNo se actualizo el saldo de los documentos FV");</script>'; }

	}

	//========================// VALIDA NOTA Y EJECUTA LA FUNCION TERMINAR //========================//
	//***********************************************************************************************//
	function validaNota($id,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$id_sucursal,$id_tercero,$notaCruce,$link){

		// VALIDACION DE CCOS
		$errorCcos = '';
		$sqlCcos = "SELECT id, cuenta_puc
					FROM $tablaCuentasNota
					WHERE $idTablaPrincipal='$id'
						AND id_empresa='$id_empresa'
						AND (id_centro_costos='' OR id_centro_costos=0)
						AND tiene_centro_costo='Si'";
		$queryCcos = mysql_query($sqlCcos);
		while ($rowCcos = mysql_fetch_assoc($queryCcos)) {
			// $saldo = ($rowCcos['saldo']>0)? $rowCcos['saldo']: '('.$rowCcos['saldo'].')';
			$errorCcos .= '\n- '.$rowCcos['cuenta_puc'].' ';
		}

		if($errorCcos != '') { echo '<script>alert("Aviso,\nLas siguientes cuentas deben tener centro de costo!\n'.$errorCcos.'")</script>'; exit; }

		$sqlNota   = "SELECT tercero,tipo_nota,fecha_nota FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$queryNota = mysql_query($sqlNota,$link);

		$tercero    = mysql_result($queryNota,0,'tercero');
		$tipo_nota  = mysql_result($queryNota,0,'tipo_nota');
		$fecha_nota = mysql_result($queryNota,0,'fecha_nota');

		$mes_fecha_nota  = date("m",strtotime($fecha_nota));
		$anio_fecha_nota = date("y",strtotime($fecha_nota));

		if ($mes_fecha_nota=='12') {
			$anio_fecha_nota++;
			$mes_fecha_nota='01';
		}
		else{ $mes_fecha_nota++; }

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_buscar=$anio_fecha_nota.'/'.$mes_fecha_nota.'/01';

		if($tercero==''){ echo '<script>alert("Debe Seleccionar el tercero!");</script>'; exit; }

		//VALIDAMOS LAS CUENTAS DE LA NOTA PARA VERIFICAR QUE ESTEN BALANCEADAS Y QUE ESTEN BIEN REGISTRADAS
		$sqlCuentasNota   = "SELECT cuenta_puc,descripcion_puc,debe,haber FROM $tablaCuentasNota WHERE activo=1 AND $idTablaPrincipal='$id'";
		$queryCuentasNota = mysql_query($sqlCuentasNota,$link);

		//VARIABLES PARA ACUMULAR LOS VALORES DEBITO Y CREDITO
		$acumDebito  =0;
		$acumCredito =0;
		$diferencia  =0;
		$contCuentas =0;

		//RECORRER LAS NOTAS
		while ($rowCuentas=mysql_fetch_array($queryCuentasNota)) {
			//VERIFICAR SI SE INSERTO UNA CUENTA CON VALORES EN DEBITO Y CREDITO, YA QUE SOLO SE PUEDE EN UN SOLO CAMPO
			if ($rowCuentas['debe']>0 && $rowCuentas['haber']>0) { echo '<script>alert("Error!\nLa cuenta '.$rowCuentas['cuenta_puc'].' -> '.$rowCuentas['descripcion_puc'].'\nTiene valores en debito y credito, y solo se permite en uno\nverifique la cuenta y vuelva a intentarlo ");</script>';	exit;}
			//VERIFICAR SI LAS DOS FILAS DE DEBITO Y CREDITO ESTAN VACIAS
			if ($rowCuentas['debe']==0 && $rowCuentas['haber']==0) {echo '<script>alert("Error!\nLa cuenta '.$rowCuentas['cuenta_puc'].' -> '.$rowCuentas['descripcion_puc'].'\nTiene valores en debito y credito en cero\nInserte un valor en uno y vuelva a intentarlo");</script>'; exit;}
			//SI PASO LAS VALIDACIONES, ACUMULAMOS EL VALOR DEL DEBITO Y DEL CREDITO
			$acumDebito  += $rowCuentas['debe'];
			$acumCredito += $rowCuentas['haber'];
		}

		//UNA VEZ QUE RECORRIMOS LAS CUENTAS Y SE VALIDO QUE ESTUVIERAN CORRECTAMENTE INSERTADAS, Y SE TIENE EL ACUMULADO DEL DEBITO Y DEL CREDITO, VERIFICAMOS QUE ESTE BALACEADA LA NOTA, ES DECIR QUE LA DIFERENCIA
		//ENTRE EL DEBITO-CREDITO SEA IGUAL A CERO, SI NO ES IGUAL A CERO ENTONCES NO ESTA BALANCEADA LA NOTA Y NO SE PUEDE GENERAR

		$diferencia=round($acumDebito,2) - round($acumCredito,2);
		if ($diferencia!=0) {echo '<script>alert("Error!\nLa nota no esta balanceada! tiene una diferencia de: $ '.$diferencia.'\nVerifiquela y vuelva a intentarlo");</script>';	exit;}

		$sqlNotaGenerada   = "SELECT COUNT(id) AS cont FROM $tablaPrincipal WHERE fecha_nota>='$fecha_buscar' AND activo=1 AND estado=1 AND id_sucursal='$id_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
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
			terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link);
		}

	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$idEmpresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,$accion,$id_tercero,$link){
		$decimalesMoneda = ($_SESSION['DECIMALESMONEDA'] >= 0)? $_SESSION['DECIMALESMONEDA']: 0;

		if ($accion=='agregar') {
			$sqlNotaGeneral   = "SELECT consecutivo,tercero,fecha_nota,sinc_nota FROM nota_contable_general WHERE activo=1 AND id='$idDocumento' AND id_empresa='$idEmpresa'";
			$queryNotaGeneral = mysql_query($sqlNotaGeneral,$link);

			$consecutivoNota = mysql_result($queryNotaGeneral,0,'consecutivo');
			$tercero         = mysql_result($queryNotaGeneral,0,'tercero');
			$fechaNota       = mysql_result($queryNotaGeneral,0,'fecha_nota');
			$sinc_nota       = mysql_result($queryNotaGeneral,0,'sinc_nota');

			$sql   = "SELECT debe,haber,cuenta_puc,cuenta_niif,id_tercero,id_documento_cruce,tipo_documento_cruce,prefijo_documento_cruce,numero_documento_cruce
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

				// VALIDAR EL DOCUMENTO CRUCE
				$row['id_documento_cruce'] = ($row['id_documento_cruce'] =='' || $row['id_documento_cruce'] ==0)? $idDocumento : $row['id_documento_cruce'] ;
				$row['tipo_documento_cruce'] = ($row['tipo_documento_cruce']=='' )? 'NCG' : $row['tipo_documento_cruce'];

				$id_tercero_nota = ($row['id_tercero']=='0' || $row['id_tercero']=='')? $id_tercero : $row['id_tercero'];
				$documento_cruce = ($row['prefijo_documento_cruce'] != '')? $row['prefijo_documento_cruce'].' '.$row['numero_documento_cruce']: $row['numero_documento_cruce'];
				$documento_cruce=($documento_cruce=='' || $documento_cruce==0)? $consecutivoNota : $documento_cruce ;
				$valueInsertCuentasColgaap .= "('$idDocumento',
												'$consecutivoNota',
												'NCG',
												'Nota Contable General',
												'".$row['debe']."',
												'".$row['haber']."',
												'".$row['cuenta_puc']."',
												'$id_sucursal',
												'$id_tercero_nota',
												'".$_SESSION['NITEMPRESA']."',
												'NOTA INTERNA',
												'$idEmpresa',
												'$fechaNota',
												'".$row['id_documento_cruce']."',
												'".$row['tipo_documento_cruce']."',
												'$documento_cruce'
												),";

				$valueInsertCuentasNiif .= "('$idDocumento',
											'$consecutivoNota',
											'NCG',
											'Nota Contable General',
											'".$row['debe']."',
											'".$row['haber']."',
											'".$row['cuenta_niif']."',
											'$id_sucursal',
											'$id_tercero_nota',
											'".$_SESSION['NITEMPRESA']."',
											'NOTA INTERNA',
											'$idEmpresa',
											'$fechaNota',
											'".$row['id_documento_cruce']."',
											'".$row['tipo_documento_cruce']."',
											'$documento_cruce'
											),";
			}

			$saldoDebitoColgaap  = ROUND($saldoDebitoColgaap,$decimalesMoneda);
			$saldoCreditoColgaap = ROUND($saldoCreditoColgaap,$decimalesMoneda);

			$saldoDebitoNiif  = ROUND($saldoDebitoNiif,$decimalesMoneda);
			$saldoCreditoNiif = ROUND($saldoCreditoNiif,$decimalesMoneda);

			//VALIDACIONES CONTABILIDAD COLGAAP
			if($cuentaVaciaColgaap > 0){ echo '<script>alert("Aviso!\nExisten '.$cuentaVaciaColgaap.' registros sin cuentas en la contabilidad colgaap!");</script>'; exit; }
			else if($saldoDebitoColgaap == 0 || $saldoCreditoColgaap == 0){ echo '<script>alert("Aviso!\nLos saldos deben ser mayores a cero en la contabilidad Colgaap!");</script>'; exit; }
			else if($saldoDebitoColgaap != $saldoCreditoColgaap){ echo '<script>alert("Aviso!\nLos saldos Debitos ('.$saldoDebitoColgaap.') y Creditos ('.$saldoCreditoColgaap.') en la contabilidad colgaap son diferentes!");</script>'; exit; }

			//VALIDACIONES CONTABILIDAD NIIF
			if ($sinc_nota=='colgaap_niif') {
				if($cuentaVaciaNiif > 0){ echo '<script>alert("Aviso!\nExisten '.$cuentaVaciaNiif.' registros sin cuentas en la contabilidad niif!");</script>'; exit; }
				else if($saldoDebitoNiif == 0 || $saldoCreditoNiif == 0){ echo '<script>alert("Aviso!\nLos saldos deben ser mayores a cero en la contabilidad niif!");</script>'; exit; }
				else if($saldoDebitoNiif != $saldoCreditoNiif){ echo '<script>alert("Aviso!\nLos saldos Debitos ('.$saldoDebitoNiif.') y Creditos ('.$saldoCreditoNiif.')  en la contabilidad Niif son diferentes!");</script>'; exit; }
			}

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

			if ($sinc_nota=='colgaap_niif') {

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
					$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='NCG'";
					$queryDelete = mysql_query($sqlDelete,$link);

					echo'<script>alert("Error!\nNo se genero el asiento contable Niif, Intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit;
				}
			}
		}

		else if ($accion=='eliminar') {
			$sqlDelete   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='NCG'";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){ echo '<script>alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }

			$sqlDelete   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND id_empresa='$idEmpresa' AND tipo_documento='NCG'";
			$queryDelete = mysql_query($sqlDelete,$link);
			if (!$queryDelete){ echo '<script>alert("Error!\nNo se eliminaron los asientos de la nota\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
		}
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrilla,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){

		$sql   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo=mysql_result($query,0 ,'consecutivo');

		//VALIDAMOS QUE NO TENGA ARTICULOS RELACIONADOS, SE DEBE REVERSAR EL MOVIMIENTO QUE HICIERON LOS ARTICULOS
		$sqlValidaArticulos   = "SELECT COUNT(id) AS cont FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
		$queryValidaArticulos = mysql_query($sqlValidaArticulos,$link);
		$cont = mysql_result($queryValidaArticulos,0,'cont');

		if ($cont>0) {

			//SI TIENE ARTICULOS SE DEBE REVERSAR EL MOVIMIENTO DEL MISMO, LLAMAMOS LA FUNCION PARA REALIZAR
			$sqlArticulos   = "SELECT id,tipo FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa'";
			$queryArticulos = mysql_query($sqlArticulos,$link);

			$resul=0;

			//RECORREMOS TODOS LOS ARTICULOS Y LLAMAMOS LA FUNCION PARA REVERSAR EL PROCESO DE CADA UNO
			while ($rowArticulos=mysql_fetch_array($queryArticulos)) { $resul=eliminarArticuloRelacionado($rowArticulos['id'],$rowArticulos['tipo'],'return',$link); }

			if ($resul>0) { echo '<script>alert("Erro!\nNo se actualizo el inventario\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
		}

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
						VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Nota Contable General',$id_sucursal,'$id_empresa')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrilla.'").load({
						url     : "nota_general/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota           : "'.$idDocumento.'",
							opcGrilla : "'.$opcGrilla.'"
						}
					});

					Ext.getCmp("Btn_exportar_NotaGeneral").disable();
				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se insertaron los articulos nuevamente al inventario!");</script>'; }
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*******************************************************************************************************************************************************************************/
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA GUARDAR CUENTA DE LA GRILLA ==================================================================//
	function guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link){
		//VALIDACIONES
		if ($debe==0 && $haber==0) { echo '<script>alert("Error\nDebe ingresar el monto  del debito o del credito");</script>'; return; }
		elseif ($debe>0 && $haber>0) { echo '<script>alert("Error\nDebe ingresar el monto solo para el debito o el credito");</script>'; return; }

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$prefijoDocumentoCruce,$numeroDocumentoCruce,$opcGrilla,$cont,$terceroGeneral,$id_tercero,$cuenta,'guardar',$debe,$haber,$link); }

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
						'$id_empresa')";
		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

		if($lastId > 0){
			$debe  = (is_nan($debe))? 0  : $debe;
			$haber = (is_nan($haber))? 0 : $haber;

			if($terceroGeneral!='NOTA INTERNA'){ $body=cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrilla); }
			else{ $body=cargaDivsInsertUnidadesConTercero('return',$consecutivo,$opcGrilla); }

			echo'<script>
					document.getElementById("idInsertCuenta'.$opcGrilla.'_'.$cont.'").value = '.$lastId.'

					document.getElementById("divImageSave'.$opcGrilla.'_'.$cont.'").setAttribute("title","Actualizar Cuenta");
					document.getElementById("imgSaveArticulo'.$opcGrilla.'_'.$cont.'").setAttribute("src","img/reload.png");

					document.getElementById("divImageSave'.$opcGrilla.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrilla.'_'.$cont.'").style.display = "none";
					document.getElementById("deleteCuenta'.$opcGrilla.'_'.$cont.'").style.display     = "block";
					document.getElementById("configurarCuenta'.$opcGrilla.'_'.$cont.'").style.display = "block";

					//llamamos a la funcion para calcular los totales de la nota
					calcTotal'.$opcGrilla.'("'.$debe.'","'.$haber.'","agregar");

					//habilitar el boton terminar y nuevo
					Ext.getCmp("Btn_guardar_'.$opcGrilla.'").enable();
					Ext.getCmp("Btn_nueva_'.$opcGrilla.'").enable();

				</script>'.$body;

		}
		else{
				echo'<script>
						alert("Error\nNo se ha guardo la cuenta en la nota, Intentelo de nuevo\nSi el problema persiste favor comuniquese con la administracion del sistema");
						var elemento=document.getElementById("bodyDivArticulos'.$opcGrilla.'_'.$consecutivo.'");
						elemento.parentNode.removeChild(elemento);
				  	</script>  ';
			}
	}


	function validaDocumentoCruce($id_empresa,$id_documento_cruce,$documento_cruce,$prefijo_documento_cruce='',$numero_documento_cruce,$opcGrilla,$cont,$id_tercero_general,$id_tercero,$cuenta,$evento,$debe,$haber,$link){

		$script = '';
		$cont2  = $cont;

		//CON LA VARIABLE EVENTO IDENTIFICAMOS SI SE ESTA GUARDANDO O ACTUALIZANDO UNA CUENTA, PARA ASI MOSTRAR O NO UN BLOQUE DE CODIGO Y PARA EL CONTADOR
		if ($evento=='guardar') {
			$script = 'document.getElementById("bodyDivArticulos'.$opcGrilla.'_'.($cont++).'").parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrilla.'_'.($cont++).'"));
						contItems'.$opcGrilla.'--;';
			$cont2  = ($cont-2);
		}

		//VALIDAR QUE TENGA UN NUMERO DE DOCUMENTO
		if ($numero_documento_cruce=='' || $numero_documento_cruce=='0') {
			echo'<script>
					alert("Si relaciona una '.$documento_cruce.', debe ingresar el numero!");
					document.getElementById("numeroDocumentoCruce'.$opcGrilla.'_'.$cont.'").focus();
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
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
		else if(($saldoCuentaDb < 0 && $saldoCuenta < 0) || ($saldoCuentaDb > 0 && $saldoCuenta > 0)){
			echo '<script>
					alert("No se permite debitar o acreditar la misma cuenta en mas de una ocacion.");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
		else if($absCuenta > $absCuentaDb){
			echo '<script>
					alert("El valor a cruzar es superior al registrado en el documento ('.$absCuentaDb.').");
					'.$script.'
					setTimeout(function(){ document.getElementById("cuenta'.$opcGrilla.'_'.$cont2.'").focus(); },100);
				</script>';
			exit;
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link){

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$prefijoDocumentoCruce,$numeroDocumentoCruce,$opcGrilla,$cont,$terceroGeneral,$id_tercero,$cuenta,'actualizar',$debe,$haber,$link); }

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
								id_documento_cruce='$id_documento_cruce',
								tipo_documento_cruce = '$tipoDocumentoCruce',
								prefijo_documento_cruce = '$prefijoDocumentoCruce',
								numero_documento_cruce = $numeroDocumentoCruce
								WHERE $idTablaPrincipal=$id AND id=$idInsertCuenta";
		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);


		if ($queryUpdateArticulo) {

			$debe  = ($debe=='')? 0: $debe;
			$haber = ($haber=='')? 0: $haber;

			echo'<script>
					document.getElementById("divImageSave'.$opcGrilla.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrilla.'_'.$cont.'").style.display = "none";

					//llamamos la funcion para recalcular el costo de la nota
					calcTotal'.$opcGrilla.'("'.$debeAnterior.'","'.$haberAnterior.'","eliminar");
					calcTotal'.$opcGrilla.'("'.$debe.'","'.$haber.'","agregar");
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo la cuenta"); </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION DE LA NOTA =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){
		$observacion      = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlObservacion   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa='$_SESSION[EMPRESA]'";
		$queryObservacion = mysql_query($sqlObservacion,$link);
		if($queryObservacion){ echo 'true'; }
		else{ echo'false'; }
	}


	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrilla,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link){
		//CONSULTAMOS EL DOCUMENTO PARA SABER SI ESTA GENERADO
		$sql   = "SELECT estado,consecutivo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$estado      = mysql_result($query,0 ,'estado');
		$consecutivo = mysql_result($query,0 ,'consecutivo');

		if($estado=='3'){ echo '<script>alert("Error!\nEsta nota ya esta cancelada!");</script>'; return;}
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

				if ($resul>0) { echo '<script>alert("Erro!\nNo se actualizo el inventario\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
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
			echo '<script>alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			return;
		}
		else{
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Nota Contable General',$id_sucursal,".$_SESSION['EMPRESA'].")";
			$queryLog = mysql_query($sqlLog,$link);

			echo '<script>nueva'.$opcGrilla.'();</script>';
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
			$sql   = "UPDATE activos_fijos SET activo=1 WHERE id=$id_item";
			$query = mysql_query($sql,$link);
 		}

		$sql   = "DELETE FROM inventario_movimiento_notas WHERE id='$id'";
		$query = mysql_query($sql,$link);
 		if (!$query) { $cont++; }

 		if ($opc=='return') { return $cont; }
 		else if ($cont==0 && $opc=='') { echo "true"; }
 		else if ($cont>0 && $opc=='') { echo "false"; }

 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrilla,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal'  AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa)
						VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Nota Contable General',$id_sucursal,".$_SESSION['EMPRESA'].")";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					Ext.get("contenedor_'.$opcGrilla.'").load({
						url     : "nota_general/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota           : "'.$idDocumento.'",
							opcGrilla : "'.$opcGrilla.'"
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
 	function actualizarTipoNota($id,$idTipoNota,$notaCruce,$tablaPrincipal,$id_empresa,$link){
		$sql   = "UPDATE $tablaPrincipal SET id_tipo_nota='$idTipoNota' WHERE id='$id' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

 		if (!$query) {
 			echo '<script>
 					alert("Error!\nNo se actualizo el tipo de nota\nSi el problema persiste comuniquese con el administrador del sistema");
 					document.getElementById("selectTipoNota").focus();
 				  </script>';
 			exit;
 		}

 		$classNota = ($notaCruce == "Si")? 'contenedorNotaContableCruce': 'contenedorNotaContable';

 		echo'<script>document.getElementById("contenedorNotaContable").setAttribute("class","'.$classNota.'");</script>';
 	}


 	//=========================== FUNCION PARA ACTUALIZAR LA FECHA DE LA NOTA =========================================================================//
 	function actualizarFechaNota($id,$fecha,$tablaPrincipal,$link){
		$sql   = "UPDATE $tablaPrincipal SET fecha_nota='$fecha' WHERE id='$id'";
		$query = mysql_query($sql,$link);

 		if (!$query) { echo '<script>alert("Error!\nNo se actualizo la fecha, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
 	}

 	//REDERIZA FILTRO TIPO DE DOCUMENTO
 	function ventana_buscar_documento_cruce($cont,$opcGrilla,$carpeta,$id_empresa,$id_sucursal,$link){
 		echo'<select class="myfield" name="filtro_tipo_documento" id="filtro_tipo_documento" style="width:100px; margin: 2px 0px 0px 4px;" onChange="carga_filtro_tipo_documento(this.value)">
        		<option value="FC">FC</option>
        		<option value="FV">FV</option>
    		</select>
    		<script>
				function carga_filtro_tipo_documento(tipo_documento_cruce){
					var filtroTipoDocumento = document.getElementById("filtro_tipo_documento").value;
					Ext.get("contenedor_buscar_documento_cruce_'.$opcGrilla.'").load({
						url     : "'.$carpeta.'bd/grillaDocumentoCruce.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opc                  : "'.$opc.'",
							filtro_sucursal      : '.$id_sucursal.',
							tipo_documento_cruce : tipo_documento_cruce,
							cont                 : "'.$cont.'",
							opcGrilla    : "'.$opcGrilla.'",
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
 	function cargaConfiguracionCuenta($id_documento,$idInsertCuenta,$idCuenta,$id_empresa,$opcGrilla,$cont,$link){
		$sql   = "SELECT N.cuenta_niif,
						N.descripcion_niif,
						N.cuenta_puc,
						N.centro_costos,
						N.codigo_centro_costos,
						P.centro_costo AS tieneCcos
					FROM nota_contable_general_cuentas AS N LEFT JOIN puc AS P ON(
							P.activo = 1
							AND P.id_empresa = N.id_empresa
							AND P.cuenta = N.cuenta_puc
							AND P.id = N.id_puc
						)
					WHERE N.id = '$idInsertCuenta'
						AND N.id_puc = '$idCuenta'
						AND N.id_empresa = '$id_empresa'
						AND N.activo = 1
						AND id_nota_general='$id_documento'";
		$query = mysql_query($sql,$link);

		$divCccos   = "";
		$tieneCcos  = mysql_result($query, 0, 'tieneCcos');
		$nombreCcos = mysql_result($query, 0, 'centro_costos');
		$codigoCcos = mysql_result($query, 0, 'codigo_centro_costos');

		$cuenta     = mysql_result($query, 0, 'cuenta_puc');
		$cuentaNiif = mysql_result($query, 0, 'cuenta_niif');
		$nombreNiif = mysql_result($query, 0, 'descripcion_niif');

		if($tieneCcos == 'Si'){

			$divCccos = '<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;margin-top:20px;float: left;width:100%;">CENTRO DE COSTOS</div>
		 				<div style="float:left;width:90%; background-color:#FFF; margin-top:10px; margin-left:20px; border:1px solid #D4D4D4;">
		 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CODIGO</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5px 0 5px 3px;font-weight: bold;font-size: 11px;">CENTRO DE COSTOS</div>
		 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCentroCostos_'.$opcGrilla.'('.$cont.')"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>

		 					<div id="codigoCcos_'.$opcGrilla.'" style="float:left; height:13px; width:100px; border-right:1px solid #D4D4D4; padding: 5px 0 5px 3px;">'.$codigoCcos.'</div>
		 					<div id="nombreCcos_'.$opcGrilla.'" style="float:left; height:13px; width:calc(100% - 110px); padding: 5px 0 5px 3px; overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.$nombreCcos.'</div>
		 				</div>';
		}

 		echo'<div style="width:100%; padding-top:10px;">
 				<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;">CUENTA NIIF DE '.$cuenta.' COLGAAP</div>

 				<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CUENTA</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5px 0 5px 3px;font-weight: bold;font-size: 11px;">DESCRIPCION</div>
 					<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5px; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCuenta'.$opcGrilla.'('.$cont.',\'niif\')"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>

 					<div style="float:left; height:13px; width:100px; border-right:1px solid #D4D4D4; padding: 5px 0 5px 3px;">'.$cuentaNiif.'</div>
 					<div style="float:left; height:13px; width:calc(100% - 110px); padding: 5px 0 5px 3px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">'.$nombreNiif.'</div>
 				</div>

 				'.$divCccos.'
			</div>';
 	}

 	//=========================== FUNCION PARA ACTUALIZAR LA CUENTA NIIF DE UNA COLGAAP ====================================//
 	function actualizarNiif($id_niif,$cuenta,$idInsertCuenta,$idCuenta,$opcGrilla,$cont,$id_empresa,$link){
		$sql   = "UPDATE nota_contable_general_cuentas SET id_niif='$id_niif' WHERE id='$idInsertCuenta' AND id_puc='$idCuenta' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);

 		if ($query){ echo 'true'; }
 		else{ echo 'false'; }
 	}

 	//===============================// FUNCION PARA ACTUALIZAR EL CENTRO DE COSTOS //===============================//
 	function actualizarCcos($idInsertCuenta,$idCuenta,$opcGrilla,$id_centro_costos,$codigo_centro_costos,$id_documento,$id_empresa,$link){
		$sqlCcos   = "SELECT COUNT(id) AS contCcos
						FROM centro_costos
						WHERE codigo<>'$codigo_centro_costos'
							AND codigo LIKE '$codigo_centro_costos%'
							AND id_empresa='$id_empresa'";
		$queryCcos = mysql_query($sqlCcos,$link);
		$contCcos  = mysql_result($queryCcos, 0, 'contCcos');
		if($contCcos > 0){ echo'padre'; exit; }

 		$sql   = "UPDATE nota_contable_general_cuentas
	 				SET id_centro_costos='$id_centro_costos'
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
		$sql   = "SELECT estado,consecutivo FROM nota_contable_general WHERE id=$id_documento";
		$query = mysql_query($sql,$link);

		$estado      = mysql_result($query,0,'estado');
		$consecutivo = mysql_result($query,0,'consecutivo');

		if ($estado==1) { $mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el'; }
		else if ($estado==2) { $mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el'; }
		else if ($estado==3) { $mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el'; }

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
							opcGrilla : "NotaGeneral",
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
?>