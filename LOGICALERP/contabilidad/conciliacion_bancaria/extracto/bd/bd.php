<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	require("../../config_var_global.php");

	//============================================= FUNCIONES PARA EL DOCUMENTO DE EXTRACTOS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($opc) {
		case 'UpdateFecha':
			UpdateFecha($tablaPrincipal,$id_documento,$fecha,$id_empresa,$mysql);
			break;

		case 'guardarSaldoExtracto':
			guardarSaldoExtracto($tablaPrincipal,$id_documento,$saldo,$id_empresa,$mysql);
			break;

		case 'guardarSucursal':
			guardarSucursal($tablaPrincipal,$id_documento,$codigo_sucursal,$nombre_sucursal,$id_empresa,$mysql);
			break;

		case 'buscarCuenta':
			buscarCuenta($cuenta,$id_documento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$mysql);
			break;

		case 'buscarTercero':
			buscarTercero($id,$codTercero,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'cambiaTercero':
			cambiaTercero($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$link,$tablaPrincipal);
			break;

		case 'deleteDetalle':
			deleteDetalle($idDetalle,$cont,$id,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'retrocederRegistro':
		 	retrocederRegistro($cont,$opcGrillaContable,$idDetalle,$id,$id_empresa,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id_documento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$link);
			break;

		case 'guardarDetalle':
			guardarDetalle($opc,$consecutivo,$id,$cont,$tipo,$numeroDocumento,$fecha,$valor_extracto,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql,$id_empresa,$id_sucursal);
			break;

		case 'actualizaDetalle':
			actualizaDetalle($id,$opcGrillaContable,$cont,$idInsertRegistro,$idDetalle,$tipo,$numeroDocumento,$fecha,$valor_extracto,$tablaInventario,$idTablaPrincipal,$mysql);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id_sucursal,$id,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id_documento,$opcGrillaContable,$consecutivo,$id_sucursal,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'updateSucursalTercero':
			updateSucursalTercero($id_factura,$id_scl_Tercero,$nombre_scl_Tercero,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id,$id_sucursal,$id_empresa,$tablaPrincipal,$opcGrillaContable,$link);
			break;
	}

	function UpdateFecha($tablaPrincipal,$id_documento,$fecha,$id_empresa,$mysql){
		$sql = "UPDATE $tablaPrincipal SET fecha_extracto='$fecha' WHERE activo=1 AND id=$id_documento AND id_empresa=$id_empresa";
		$query = $mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error\nNo se pudo actualizar, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function guardarSaldoExtracto($tablaPrincipal,$id_documento,$saldo,$id_empresa,$mysql){
		//CONSULTAR EL SALDO ANTERIOR
		$sql						= "SELECT saldo_extracto FROM $tablaPrincipal WHERE activo = 1 AND id = $id_documento AND id_empresa = $id_empresa";
		$query					= $mysql->query($sql,$mysql->link);
		$valorAnterior 	= (int) $mysql->result($query,0,'saldo_extracto');
		$saldo        	= (int) $saldo;
		// echo $sql;

		$sql 		= "UPDATE $tablaPrincipal SET saldo_extracto = $saldo WHERE activo = 1 AND id = $id_documento AND id_empresa = $id_empresa";
		$query 	= $mysql->query($sql,$mysql->link);
		if($query){
			echo "<script>
							calcTotalExtrac('restar',0,$valorAnterior);
							calcTotalExtrac('sumar',0,$saldo);
						</script>";
		}
		else{
			echo '<script>alert("Error\nNo se pudo actualizar, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function guardarSucursal($tablaPrincipal,$id_documento,$codigo_sucursal,$nombre_sucursal,$id_empresa,$mysql){
		$sql = "UPDATE $tablaPrincipal SET id_sucursal = $codigo_sucursal, sucursal = '$nombre_sucursal' WHERE activo = 1 AND id = $id_documento AND id_empresa = $id_empresa";
		$query = $mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>
							alert("Error\nNo se pudo actualizar, si el problema continua comuniquese con el administrador del sistema.");
						</script>';
		}
	}

	function buscarCuenta($cuenta,$id_documento,$id_empresa,$opcGrillaContable,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$mysql){
		$opcGrillaContable=='conciliaciones' ?  $tablaPrincipal='conciliaciones':'';

		$sql   = "SELECT id,descripcion FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND cuenta='$cuenta' ";
		$query = $mysql->query($sql,$mysql->link);
		$id          = $mysql->result($query,0,'id');
		$descripcion = $mysql->result($query,0,'descripcion');

		if ($id>0) {
			// ACTUALIZAR LA CUENTA EN EL EXTRACTO
			$sql="UPDATE $tablaPrincipal SET id_cuenta = $id, cuenta='$cuenta',descripcion_cuenta='$descripcion' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
			$mysql->query($sql,$mysql->link);

			echo '<script>
					document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value="'.$descripcion.'";

				</script>';
				if ($opcGrillaContable=='conciliaciones') {
					echo '<script>
					document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value="'.$descripcion.'";
					show_contenedor_'. $opcGrillaContable.'('.$id_documento.');

				</script>';
				}
		}
		else{
			echo '<script>
						alert("Aviso\nLa cuenta digitada no existe");
						document.getElementById("cuenta'.$opcGrillaContable.'").focus();
						document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value="";
				</script>';
		}
	}

	//=========================== FUNCION PARA BUSCAR UN Tercero ===============================================================================//
	function buscarTercero($idRegistro,$codTercero,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link){

		//limpiar los valores del documento
		echo '	<script>
					total'.$opcGrillaContable.'              = 0.00;
				</script>';

		$campo = '';
		$focus = '';
		if($inputId == 'codTercero'.$opcGrillaContable.''){
			$campo     = 'codigo';
			$textAlert = 'Codigo';
			$focus     = 'setTimeout(function(){ document.getElementById("codTercero'.$opcGrillaContable.'").focus(); },100);';
		}
		else if($inputId == 'nitTercero'.$opcGrillaContable.''){
			$campo     = 'numero_identificacion';
			$textAlert = 'Nit';
			$focus     = 'setTimeout(function(){ document.getElementById("nitTercero'.$opcGrillaContable.'").focus(); },100);';
		}
		else if($inputId == 'idTercero'.$opcGrillaContable.''){
			$campo     = 'id';
			$textAlert = 'Codigo';
		}

		$SQL   = "SELECT id,numero_identificacion,nombre,codigo AS cod_Tercero,exento_iva,id_forma_pago FROM terceros WHERE numero_identificacion='$codTercero' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($SQL,$link);

		$id_tercero    = mysql_result($query,0,'id');
		$nit           = mysql_result($query,0,'numero_identificacion');
		$codigo        = mysql_result($query,0,'cod_Tercero');
		$nombre        = mysql_result($query,0,'nombre');
		$exento_iva    = mysql_result($query,0,'exento_iva');
		$id_forma_pago = mysql_result($query,0,'id_forma_pago');

		$sqlUpdate = "UPDATE $tablaPrincipal
						SET id_tercero  		= '$id_tercero',
							documento_tercero 	= '$codTercero',
							tercero 			= '$nombre'
						WHERE id='$idRegistro'
							AND id_empresa = '$id_empresa'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($id_tercero > 0){

			echo'<script>

					document.getElementById("nitTerceroExtractos").value    = "'.$nit.'";
					document.getElementById("nombreTercero'.$opcGrillaContable.'").value = "'.$nombre.'";

					id_Tercero_'.$opcGrillaContable.'   = "'.$id_tercero.'";
					codigoTercero'.$opcGrillaContable.' = "'.$codigo.'";
					nitTercero'.$opcGrillaContable.'    = "'.$nit.'";
					nombreTercero'.$opcGrillaContable.' = "'.$nombre.'";
					exento_iva_'.$opcGrillaContable.'   = "'.$exento_iva.'";
				</script>';
		}
		else{
			echo'<script>
					alert("'.$textAlert.' de Tercero no establecido!");
					'.$focus.'
				</script>';
		}
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="head'.$opcGrillaContable.'">
							<div class="label" style="width:40px !important;"></div>
							<div class="label" style="width:130px;">Tipo Documento</div>
							<div class="label" style="width:130px;">Numero Documento</div>
							<div class="label" style="width:130px;">Fecha</div>
							<div class="label" style="width:130px;">Valor</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
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
				</div>

				<script>
					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotal'.$opcGrillaContable.'").innerHTML        = parseFloat(subtotal'.$opcGrillaContable.').toFixed(2);
					document.getElementById("subtotalDetalle'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalDetalle'.$opcGrillaContable.').toFixed(2);
					document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML  = parseFloat(total'.$opcGrillaContable.').toFixed(2);

					document.getElementById("tipo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$body =  '<div class="campo" style="width:40px !important; overflow:hidden;">
								<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
								<div style="float:left; width:18px; overflow:hidden;" id="renderDetalle'.$opcGrillaContable.'_'.$cont.'"></div>
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" id="tipo'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'numeroDocumento'.$opcGrillaContable.'_'.$cont.'\','.$cont.')"/>
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" id="numeroDocumento'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'fecha'.$opcGrillaContable.'_'.$cont.'\','.$cont.')" />
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" id="fecha'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'valor'.$opcGrillaContable.'_'.$cont.'\','.$cont.')" />
							</div>
							<div class="campo" style="width:130px;">
								<input type="text" placeholder="Ingrese un valor" id="valor'.$opcGrillaContable.'_'.$cont.'" onKeyup="changeInput(event,this,\'guardar\','.$cont.')" />
							</div>
							<div style="float:right; min-width:130px;">
								<div onclick="deleteDetalle'.$opcGrillaContable.'('.$cont.')" id="deleteDetalle'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"/></div>
								<div onclick="retrocederRegistro'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
								<div onclick="guardarNewRegistro'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Registro" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:inline"><img src="img/save_true.png" id="imgSaveDetalle'.$opcGrillaContable.'_'.$cont.'"/></div>
							</div>
							<input type="hidden" id="idDetalle'.$opcGrillaContable.'_'.$cont.'" value="'.$idDetalle.'">
							<input type="hidden" id="idRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />
							<input type="hidden" id="idInsertRegistro'.$opcGrillaContable.'_'.$cont.'" value="0" />

							<script>
								var mes_select= document.getElementById("fecha_formExtractos").value;
								new Ext.form.ComboBox({
									typeAhead     : true,
									triggerAction : "all",
									lazyRender    : true,
									mode          : "local",
									applyTo       : "tipo'.$opcGrillaContable.'_'.$cont.'",
									width         : 130,
									store         : new Ext.data.ArrayStore({
										id     : 0,
										fields :
										[
											"myId",
											"displayText"
										],
										data   :
										[
											[1, "Cheque"],
											[2, "Consignacion"],
											[3, "Nota Debito"],
											[4, "Nota Credito"]
										]
								  }),
									valueField   : "myId",
									displayField : "displayText"
								});

								new Ext.form.DateField({
									emptyText  : "A\u00f1o-Mes-Dia",
                  format     : "Y-m-d",
                  width      : 130,
                  allowBlank : false,
                  showToday  : false,
                  applyTo    : "fecha'.$opcGrillaContable.'_'.$cont.'",
                  minValue   : mes_select+"-01",
									maxValue   : mes_select+"-31",
				          editable   : true,
				        });
							</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarDetalle($opc,$consecutivo,$id,$cont,$tipo,$numeroDocumento,$fecha,$valor_extracto,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql,$id_empresa,$id_sucursal){

		$sql = "INSERT INTO
							$tablaInventario(
							  $idTablaPrincipal,
								tipo,
								numero_documento,
								fecha,
								valor,
								id_sucursal,
								id_empresa
							)
						VALUES(
							'$id',
							'$tipo',
							'$numeroDocumento',
							'$fecha',
							'$valor_extracto',
							'$id_empresa',
							'$id_sucursal'
						)";
		$query = $mysql->query($sql,$mysql->link);
		$lastId = $mysql->insert_id($mysql->link);
		if($cont > 0){
			echo "<script>
							document.getElementById('idDetalle$opcGrillaContable"."_$consecutivo').value						= $lastId;
							document.getElementById('idRegistro$opcGrillaContable"."_$consecutivo').value     			= $consecutivo;
							document.getElementById('idInsertRegistro$opcGrillaContable"."_$consecutivo').value     = $consecutivo;
							document.getElementById('divImageSave$opcGrillaContable"."_$consecutivo').style     		= 'display:none;';
							document.getElementById('divImageDeshacer$opcGrillaContable"."_$consecutivo').style 		= 'display:none;';
							document.getElementById('divImageDeshacer$opcGrillaContable"."_$consecutivo').style 		= 'display:none;';
							document.getElementById('deleteDetalle$opcGrillaContable"."_$consecutivo').style    		= 'width:20px; float:left; margin-top:3px;cursor:pointer;display:inline;';
							document.getElementById('deleteDetalle$opcGrillaContable"."_$consecutivo').setAttribute('title','Eliminar Registro');
							document.getElementById('deleteDetalle$opcGrillaContable"."_$consecutivo').setAttribute('src','img/delete.png');
							calcTotalExtrac('sumar',$valor_extracto,0);
						</script>".cargaDivsInsertUnidades('echo',$cont,$opcGrillaContable);
		}
		else{
			echo "<script>alert('Error, no se ha almacenado el detalle en este extracto, si el problema persiste favor comuniquese con la administracion del sistema');</script> ";
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaDetalle($id,$opcGrillaContable,$cont,$idInsertRegistro,$idDetalle,$tipo,$numeroDocumento,$fecha,$valor_extracto,$tablaInventario,$idTablaPrincipal,$mysql){

		// CONSULTAR EL VALOR ANTERIOR DEL DETALLE
		$sql = "SELECT valor FROM $tablaInventario WHERE id = $idDetalle";
		$query = $mysql->query($sql,$mysql->link);
		$valorAnterior = $mysql->result($query,0,'valor');

		$sql = "UPDATE
							$tablaInventario
						SET
							tipo             = '$tipo',
							numero_documento = '$numeroDocumento',
							fecha            = '$fecha',
							valor            = '$valor_extracto'
						WHERE
							$idTablaPrincipal = $id
						AND
							id = $idDetalle";
		$query = $mysql->query($sql,$mysql->link);

		if($query){
			echo '<script>
							document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
							document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
						</script>';

			//LLAMADO A LA FUNCION PARA RECALCULAR EL VALOR DEL DOCUMENTO
			echo "<script>
							calcTotalExtrac('restar',$valorAnterior,0);
							calcTotalExtrac('sumar',$valor_extracto,0);
						</script>";
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteDetalle($idDetalle,$cont,$id,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$mysql){
		// CONSULTAR EL VALOR DEL DETALLE PARA ELIMINARLO
		$sql = "SELECT id,valor FROM $tablaInventario WHERE id = $idDetalle";
		$query = $mysql->query($sql,$mysql->link);
		$idDetalle			= $mysql->result($query,0,'id');
		$valorAnterior 	= $mysql->result($query,0,'valor');

		if(!$query){
			echo '<script>
							alert("No se puede eliminar el detalle, si el problema persiste favor comuniquese con el administrador del sistema.");
						</script>';
		}
		else{


			$sql = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal = '$id' AND id = '$idDetalle'";
			$query = $mysql->query($sql,$mysql->link);

			echo "<script>
							(document.getElementById('bodyDivArticulos$opcGrillaContable"."_$cont')).parentNode.removeChild(document.getElementById('bodyDivArticulos$opcGrillaContable"."_$cont'));
							calcTotalExtrac('restar',$valorAnterior,0);
						</script>";
		}
	}

	//=========================== FUNCION PARA CAMBIAR EL PROVEEDOR DE LA FACTURA ===============================================================//
	function cambiaTercero($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$link,$tablaPrincipal){
		$sqlDeleteInventario    = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal = '$id'";
		$queryDeleteInventario  = mysql_query($sqlDeleteInventario,$link);

		$sqlDeleteRetenciones   = "DELETE FROM $tablaRetenciones  WHERE $idTablaPrincipal = '$id'";
		$queryDeleteRetenciones = mysql_query($sqlDeleteRetenciones,$link);

		$sqlUpdateProveedor     = "UPDATE $tablaPrincipal SET id_Tercero = 0, id_sucursal_Tercero=0, nombre_sucursal_Tercero='', exento_iva='' WHERE id = '$id'";
		$queryUpdateProveedor   = mysql_query($sqlUpdateProveedor,$link);

		$script = '';
		if ($opcGrillaContable=='FacturaVenta') {
			$script='document.getElementById("contenedorCheckbox'.$opcGrillaContable.'").innerHTML="";';
		}

		echo'<script>
				id_Tercero_'.$opcGrillaContable.'   = 0;
				contArticulos'.$opcGrillaContable.' = 1;
				nitTercero'.$opcGrillaContable.'    = 0;
				codigoTercero'.$opcGrillaContable.' = 0;
				nombreTercero'.$opcGrillaContable.' = "";
				exento_iva_'.$opcGrillaContable.'   = "";
				document.getElementById("codTercero'.$opcGrillaContable.'").focus();
				'.$script.'
			</script>';
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederRegistro($cont,$opcGrillaContable,$idDetalle,$id,$id_empresa,$tablaInventario,$idTablaPrincipal,$link){

		$sql 							= "SELECT tipo,numero_documento,fecha,valor FROM $tablaInventario WHERE activo = 1 AND $idTablaPrincipal = '$id' AND id = '$idDetalle' LIMIT 0,1";
		$query            = mysql_query($sql,$link);
		$tipo             = mysql_result($query,0,'tipo');
		$numero_documento = mysql_result($query,0,'numero_documento');
		$fecha            = mysql_result($query,0,'fecha');
		$valor            = mysql_result($query,0,'valor');

		echo '<script>
						document.getElementById("tipo'.$opcGrillaContable.'_'.$cont.'").value            					= "'.$tipo.'";
						document.getElementById("numeroDocumento'.$opcGrillaContable.'_'.$cont.'").value 					= "'.$numero_documento.'";
						document.getElementById("fecha'.$opcGrillaContable.'_'.$cont.'").value           					= "'.$fecha.'";
						document.getElementById("valor'.$opcGrillaContable.'_'.$cont.'").value           					= "'.$valor.'";
						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					</script>';
	}

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA FACTURA, COTIZACION, PEDIDO Y CARGAR UNA NUEVA ==============================//
	function terminarGenerar($id,$id_sucursal,$id_empresa,$tablaPrincipal,$opcGrillaContable,$link){

		$titulo      = 'Extracto';
		$sql         = "UPDATE $tablaPrincipal SET estado=1 WHERE activo = 1 AND id = $id";
		$query       = mysql_query($sql,$link);

		$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id = '$id'";
		$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

		if($query){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','EB','Extracto Bancario',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			if($opcGrillaContable != 'conciliaciones'){
				echo '<script>
								Ext.get("contenedor'.$opcGrillaContable.'").load({
									url     : "conciliacion_bancaria/extracto/bd/grillaContableBloqueada.php",
									scripts : true,
									nocache : true,
									params  :
									{

										opcGrillaContable : "'.$opcGrillaContable.'",
										id_documento      : "'.$id.'",
										tablaPrincipal    : "'.$tablaPrincipal.'"
									}
								});

								Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
								document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
							</script>';
			}
			else{
				echo '<script>
								Ext.get("contenedor'.$opcGrillaContable.'").load({
									url     : "conciliacion_bancaria/conciliacion_bancos.php",
									scripts : true,
									nocache : true,
									params  :
									{
										bloqueo_inputs    : "true",
										opcGrillaContable : "'.$opcGrillaContable.'",
										id_documento      : "'.$id.'",
										tablaPrincipal    : "'.$tablaPrincipal.'"
									}
								});

								Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
								document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
							</script>';
			}
		}
		else{ echo'<script> alert("No se guardo la '.$titulo.',\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema"); </script>'; }
	}

	//===================================== EDITAR FACTURA DE VENTA =============================================//
	function modificarDocumentoGenerado($id_documento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$link){
		$sql   = "UPDATE $tablaPrincipal SET estado = 0 WHERE activo = 1 AND id_empresa = $id_empresa AND id = $id_documento";
		$query = mysql_query($sql,$link);

		if($query){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								 VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','EB','Extracto Bancario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo '<script>
							Ext.get("contenedor'.$opcGrillaContable.'").load({
								url     : "conciliacion_bancaria/extracto/grillaContable.php",
								scripts : true,
								nocache : true,
								params  :
								{
									id_documento  		: "'.$id_documento.'",
									opcGrillaContable : "'.$opcGrillaContable.'",
									$tablaPrincipal		: "'.$tablaPrincipal.'"
								}
							});
							Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						</script>';
		}
		else{
			echo '<script>
							alert("Error!\nNo se actualizo el documento, intente de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
						</script>';
		}
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id_sucursal,$id,$opcGrillaContable,$tablaPrincipal,$id_empresa,$mysql,$link){

		//IDENTIFICAMOS EL DOCUMENTO QUE SE VA A CANCELAR
		if($opcGrillaContable == 'Extractos'){

			$sql    = "UPDATE extractos SET estado = 3 WHERE activo = 1 AND id = '$id'";
			$query  = mysql_query($sql,$link);

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','EB','Extracto Bancario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}
		else if($opcGrillaContable == 'conciliaciones'){

			$sql   	= "SELECT estado FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' LIMIT 0,1";
			$query  = mysql_query($sql,$link);
			$estado = mysql_result($query,0,'estado');

			if($estado == 3){
				echo '<script>
								alert("Error!\nEste documento ya esta Cancelado!");
							</script>';
				return;
			} else{
				$sql 		= "UPDATE conciliaciones SET estado = '3' WHERE id = '$id' AND activo = '1' AND id_sucursal = '$id_sucursal' AND id_empresa = '$id_empresa'";
				$query 	= mysql_query($sqlUpdate,$link);				//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELAD
			}

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','EB','Extracto Bancario',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}

		if(!$query){
			echo '<script>
							alert("\u00a1Error!\nEl documento no puede ser cancelado.\nSi el problema persiste comuniquese con el administrador del sistema.");
						</script>';
			return;
		}
		else{
			//INSERTAR EL LOG DE EVENTOS
			mysql_query($sqlLog,$link);
			echo '<script>
							nueva'.$opcGrillaContable.'();
						</script>';
		}
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($id_documento,$opcGrillaContable,$consecutivo,$id_sucursal,$id_empresa,$tablaPrincipal,$link){
		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog =  "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								VALUES($id_documento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','EB','Extracto Bancario',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado = 0 WHERE activo = 1 AND id = '$id_documento' AND id_sucursal = '$id_sucursal' AND id_empresa = '$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		$title = 'Extracto';

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if($queryUpdate){
			//INSERTAR EL LOG DE EVENTOS
			$queryLog = mysql_query($sqlLog,$link);
			modificarDocumentoGenerado($id_documento,$opcGrillaContable,$id_empresa,$id_sucursal,$tablaPrincipal,$link);
		}
		else{
			echo '<script>
							alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");
						</script>';
			return;
		}
 	}

	function reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		include("functions_body_article.php");
		echo cargaArticulosSave($id_factura,'',0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
	}

	function updateSucursalTercero($id_factura,$id_scl_Tercero,$nombre_scl_Tercero,$opcGrillaContable,$id_empresa,$link){
		$sql   = "UPDATE ventas_remisiones SET id_sucursal_Tercero='$id_scl_Tercero',sucursal_Tercero='$nombre_scl_Tercero' WHERE id='$id_factura' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);
	}

?>
