<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");
	 //echo"<script>console.log('$observacion');</script>";
	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		verificaCierre($id,$tablaPrincipal,$id_empresa,$link);
	}

	switch ($opc) {
		case 'updateTipoRequisicion':
			updateTipoRequisicion($id_documento,$id_tipo,$nombre,$mysql);
			break;

		case 'cargarCampoCotizacionPedido':
			cargarCampoCotizacionPedido($opcGrillaContable);
			break;

		case 'guardarFechaOrden':
			guardarFechaOrden($idInputDate,$idRequisicion,$valInputDate,$link);
			break;

		case 'buscarCotizacionPedido':
			buscarCotizacionPedido($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar);
			break;

		case 'buscarCliente':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarCliente($id,$codCliente,$inputId,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

    	case 'guardarSolicitante':
    		// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
    	 	guardarSolicitante($id,$tablaPrincipal,$id_solicitante,$documento_solicitante,$nombre_solicitante,$link);
    		break;

    	case 'guardarAreaSolicitante':
    		// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
    	 	guardarAreaSolicitante($id,$tablaPrincipal,$id_area_solicitante,$codigo_area_solicitante,$departamento_area_solicitante,$link);
    		break;

    	 // case 'updateCcos':
    	 // 	updateCcos($id_centro_costos,$nombre,$codigo,$opcGrillaContable,$id,$id_empresa,$link);
    	 // 	break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;
        //SE OCULTA EL IMPUESTO Y EL CENTRO DE COSTOS EN LA REQUISICION
		case 'ventanaDescripcionArticulo':
			ventanaDescripcionArticulo($cont,$idArticulo,$idInsertArticulo,$observacionArt,$id,$id_centro_costos,$codigo_centro_costo,$centro_costo,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$id_empresa,$link);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($id_impuesto,$idCentroCostos,$observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$id_ccos,$id_impuesto,$link);
			break;


		case 'guardarObservacionArt':
			guardarObservacionArt($tablaInventario,$observacion,$idInsertArticulo,$opcGrillaContable,$cont,$mysql);
			break;

		case 'actualizarCcos':
			actualizarCcos($idInsertArticulo,$tablaInventario,$opcGrillaContable,$id_centro_costos,$codigo_centro_costo,$centro_costo,$id_empresa,$link);
			break;

		case 'buscarArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($campo,$valorArticulo,$idArticulo,$id_empresa,$idCliente,$opcGrillaContable,$whereBodega,$exentoIva,$link);
			break;

		case 'cambiaCliente':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			cambiaCliente($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$link,$tablaPrincipal);
			break;

		case 'deleteArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			deleteArticulo($cont,$id,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'retrocederArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
		 	retrocederArticulo($exento_iva,$id,$idArticulo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'checkboxRetenciones':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			checkboxRetenciones($id,$idRetencion,$accion,$opcGrillaContable,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

		case 'UpdateFormaPago':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			UpdateFormaPago($id,$idFormaPago,$tablaPrincipal,$opcGrillaContable,$link,$fechaVencimiento);
			break;

		case 'terminarGenerar':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$mysql);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$eanArticulo,$link);
			break;

		case 'guardarArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'actualizaArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'guardarObservacion':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'verificaCantidadArticulo':
			verificaCantidadArticulo($opcGrillaContable,$id,$id_sucursal,$filtro_bodega,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'ventanaAutorizaDocumento':
			ventanaAutorizaDocumento($id,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$mysql);
			break;

		case 'autorizarRequisicionCompra':
			autorizarRequisicionCompra($id_documento,$opcGrillaContable,$id_sucursal,$idBodega,$id_empresa,$tipo_autorizacion,$id_area,$orden,$mysql);
			break;

		case 'agregarDocumento':
			// verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'eliminaDocReferencia':
			// verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;
		case 'updateSucursalCliente':
			updateSucursalCliente($id_factura,$id_scl_cliente,$nombre_scl_cliente,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'eliminarFacturaWs':
			eliminarFacturaWs($id_factura,$id_empresa,$link);
			break;

		case 'loadArticulos':
			loadArticulos($opcGrillaContable);
			break;

		case 'deleteDocumentoRequisicion':
		  deleteDocumentoRequisicion($id_host,$idDocumento,$nombre,$ext,$link);
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
	function updateTipoRequisicion($id_documento,$id_tipo,$nombre,$mysql){

		$sql   = "UPDATE compras_requisicion SET id_tipo = $id_tipo,tipo_nombre = '$nombre' WHERE id = $id_documento";
		$query = $mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error\no se actualizo el tipo de requisicion, intentelo de nuevo");</script>';
		}

	}

	//============================= FUNCION PARA MOSTRAR EL CAMPO DE CARGAR DESDE ===============================================================//
	function cargarCampoCotizacionPedido($opcGrillaContable){

			if ($opcGrillaContable=='EntradaAlmacen'){

				echo'<div style="width: 120px; display:table; margin-left:5px;" id="divContenedorCargarDesde'.$opcGrillaContable.'" title="Haga Click para cambiar a facturar desde un pedido" onclick="cambiarCargaFactura()">
						<div class="div_hover" id="imgFacturarDesde'.$opcGrillaContable.'"><img src="img/cotizacion.png" id="imgCargarDesde'.$opcGrillaContable.'" width"20px" height="20px"/></div>
						<div class="div_hover" id="textoFacturardesde'.$opcGrillaContable.'"> <b>Requisicion</b> </div>
					    <div class="div_hover" style="width:18px; height:18px; overflow:hidden; float:left;" id="renderCargaCotizacionPedido'.$opcGrillaContable.'"></div>
						<div class="div_hover"><img src="img/flecha_abajo.png"/></div>
					</div>

					<div style="float:left; margin: 7px 0 0 5px">
					    <div style="float:left; width:120px; height:22px;">
						    <input placeholder="Numero..." class="myFieldGrilla" id="cotizacionPedido'.$opcGrillaContable.'" onKeyup="buscarCotizacionPedido'.$opcGrillaContable.'(event,this)" style="padding-left: 5px;"/>
						</div>
						<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarCotizacionPedido'.$opcGrillaContable.'();" style="margin-top:2px; margin-left:-23;">
						   <img src="img/buscar20.png"/>
						</div>
						<!--<div title="Agregar Documento" onclick="agregarDocumento'.$opcGrillaContable.'(\'\');" class="btnCargarAgregarDocumento">
						   <img src="img/add16.png"/>
						</div>-->
					</div>
					<script>
						document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

						function cambiarCargaFactura(){
							if (document.getElementById("imgCargarDesde'.$opcGrillaContable.'").getAttribute("src")=="img/pedido.png") {

								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/cotizacion.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Requisicion</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para cambiar a facturar desde un Pedido");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

							}else{

								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/pedido.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b> Orden Compra</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para cambiar a facturar desde una Cotizacion");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
							}
						}
				    </script>';
			}
			//FACTURA DE VENTA
			else{
				echo'<div style="width: 120px; display:table; margin-left:5px;" id="divContenedorCargarDesde'.$opcGrillaContable.'" title="Haga Click para cambiar a facturar desde un pedido" onclick="cambiarCargaFactura'.$opcGrillaContable.'()">
						<div class="div_hover" id="imgFacturarDesde'.$opcGrillaContable.'"><img src="img/cotizacion.png" id="imgCargarDesde'.$opcGrillaContable.'" width"20px" height="20px"/></div>
						<div class="div_hover" id="textoFacturardesde'.$opcGrillaContable.'"><b>Cotizacion</b></div>
						<div class="div_hover" style="width:18px; height:18px; overflow:hidden; float:left;" id="renderCargaCotizacionPedido'.$opcGrillaContable.'"></div>
						<div class="div_hover"><img src="img/flecha_abajo.png"/></div>
					</div>

					<div style="float:left; margin-top:7px; margin-left:5px;">
					    <div style="float:left; width:120px; height:22px;">
						    <input placeholder="Numero..." class="myFieldGrilla" id="cotizacionPedido'.$opcGrillaContable.'" onKeyup="buscarCotizacionPedido'.$opcGrillaContable.'(event,this)" style="padding-left: 5px;" />
						</div>
						<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarCotizacionPedido'.$opcGrillaContable.'();" style="margin-top:2px; margin-left:-23;">
						    <img src="img/buscar20.png"/>
						</div>
						<div title="Cargar Documento en nueva Factura" onclick="cargarDocumento'.$opcGrillaContable.'();" class="btnCargarAgregarDocumento" style="display:none;">
						   <img src="img/page.png"/>
						</div>
						<div title="Agregar Documento" onclick="agregarDocumento'.$opcGrillaContable.'(\'\');" class="btnCargarAgregarDocumento">
						   <img src="img/add16.png"/>
						</div>
					</div>
					<script>
						document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
						var cambioCarga'.$opcGrillaContable.'=0;
						function cambiarCargaFactura'.$opcGrillaContable.'(){

							if (cambioCarga'.$opcGrillaContable.'==0) {
								cambioCarga'.$opcGrillaContable.'++;

								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/pedido.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Pedido</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para cambiar a facturar desde una Remision");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

							}else if (cambioCarga'.$opcGrillaContable.'==1) {
								cambioCarga'.$opcGrillaContable.'++;

								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/remisiones.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Remision</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para cambiar a facturar desde un Cotizacion");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

							}else if (cambioCarga'.$opcGrillaContable.'==2) {
								cambioCarga'.$opcGrillaContable.'=0;

								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/cotizacion.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Cotizacion</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para cambiar a facturar desde un Pedido");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
							}
						}
				    </script>';
			}
	}

	//============================ FUNCION PARA BUSCAR Y ASIGNAR UNA COTIZACION/PEDIO A UNA FACTURA/PEDIDO ======================================//
	function buscarCotizacionPedido($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar){

		//SI SE VA A CARGAR UNA COTIZACION VALIDAR QUE NO ESTE VENCIDA
		if ($opcCargar=='cotizacion' || $opcCargar=='cotizacionRemision' || $opcCargar=='cotizacionApedido') {
			$sql   = "SELECT COUNT(cliente) as cont, nit, id_cliente, cliente
					  FROM ventas_cotizaciones
					  WHERE consecutivo='$id' AND  activo = 1 AND (estado = 1 OR estado=2)  AND id_sucursal= '$id_sucursal' AND id_bodega= '$filtro_bodega' AND id_empresa='$id_empresa' AND
					  ('".date('Y-m-d')."' BETWEEN date_format(fecha_inicio,'%Y-%m-%d') AND date_format(fecha_finalizacion,'%Y-%m-%d') )";

			$query   = mysql_query($sql,$link);
			$mensaje = '<script>
		        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
		        			alert("Aviso!\nLa Cotizacion ya expiro\nO no existe  ");
		        			setTimeout(function(){
		        				document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
		        			},80);
		        		</script>';
		}
		else{
			$sql     = "SELECT COUNT(cliente) as cont, nit, id_cliente, cliente FROM $tablaBuscar WHERE consecutivo='$id' AND  activo = 1 AND (estado = 1 OR estado=2) AND id_sucursal= '$id_sucursal' AND id_bodega= '$filtro_bodega' AND id_empresa='$id_empresa'";
			$query   = mysql_query($sql,$link);
			$mensaje = '<script>
			    			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
			    			alert("Numero invalido!\nDocumento no terminado o ya asignado");
			    			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();}, 100);
			    		</script>';
		}

        $resu = mysql_result($query,0,'cont');
        if ($resu > 0) {
        	echo'<script>
        			Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcCargar         : "'.$opcCargar.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
							idConsecutivoCotizacionPedido : '.$id.'
						}
					});

					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = "";
        		</script>';
        }
        else{ echo $mensaje; }
	}

	//========================================== GUARDAR FECHA DE VENCIMIENTO =========================================//

	function guardarFechaOrden($idInputDate,$idRequisicion,$valInputDate,$link){
		if($idInputDate=='fechaRequisicionCompra'){ $sqlUpdateFecha = "UPDATE compras_requisicion SET  fecha_inicio='$valInputDate' WHERE id='$idRequisicion'"; }
		else if($idInputDate=='fechaFinalRequisicionCompra'){ $sqlUpdateFecha = "UPDATE compras_requisicion SET  fecha_vencimiento='$valInputDate' WHERE id='$idRequisicion'"; }

		$queryUpdateFecha = mysql_query($sqlUpdateFecha,$link);
		if($queryUpdateFecha){ echo 'true'; }
		else{ echo 'false'; }
	}

	//=========================== FUNCION PARA BUSCAR UN CLIENTE ===============================================================================//
	function buscarCliente($idRegistro,$codCliente,$inputId,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link){


		//limpiar los valores del documento
		echo '	<script>
					subtotalAcumulado'.$opcGrillaContable.'  = 0.00;
					descuentoAcumulado'.$opcGrillaContable.' = 0.00;
					descuento'.$opcGrillaContable.'          = 0.00;
					acumuladodescuentoArticulo               = 0.00;
					ivaAcumulado'.$opcGrillaContable.'       = 0.00;
					total'.$opcGrillaContable.'              = 0.00;
				</script>';

		$campo = '';
		$focus = '';
		if($inputId == 'codCliente'.$opcGrillaContable.''){
			$campo     = 'codigo';
			$textAlert = 'Codigo';
			$focus     = 'setTimeout(function(){ document.getElementById("codCliente'.$opcGrillaContable.'").focus(); },100);';
		}
		else if($inputId == 'nitCliente'.$opcGrillaContable.''){
			$campo     = 'numero_identificacion';
			$textAlert = 'Nit';
			$focus     = 'setTimeout(function(){ document.getElementById("nitCliente'.$opcGrillaContable.'").focus(); },100);';
		}
		else if($inputId == 'idCliente'.$opcGrillaContable.''){
			$campo     = 'id';
			$textAlert = 'Codigo';
		}

		$SQL   = "SELECT id,numero_identificacion,nombre,codigo AS cod_cliente,exento_iva,id_forma_pago FROM terceros WHERE $campo='$codCliente' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' AND tipo_cliente='Si' LIMIT 0,1";
		$query = mysql_query($SQL,$link);

		$id            = mysql_result($query,0,'id');
		$nit           = mysql_result($query,0,'numero_identificacion');
		$codigo        = mysql_result($query,0,'cod_cliente');
		$nombre        = mysql_result($query,0,'nombre');
		$exento_iva    = mysql_result($query,0,'exento_iva');
		$id_forma_pago = mysql_result($query,0,'id_forma_pago');

		$sqlUpdate = "UPDATE $tablaPrincipal
						SET id_proveedor  = '$id',
							cod_proveedor = '$codCliente'
						WHERE id='$idRegistro'
							AND id_empresa = '$id_empresa'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($id > 0){

			echo'<script>
					'.$arrayRetenciones.'

					document.getElementById("nitCliente'.$opcGrillaContable.'").value          = "'.$nit.'";
					document.getElementById("codCliente'.$opcGrillaContable.'").value          = "'.$codigo.'";
					document.getElementById("nombreCliente'.$opcGrillaContable.'").value       = "'.$nombre.'";

					id_cliente_'.$opcGrillaContable.'   = "'.$id.'";
					codigoCliente'.$opcGrillaContable.' = "'.$codigo.'";
					nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
					nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";
					exento_iva_'.$opcGrillaContable.'   = "'.$exento_iva.'";
				</script>'.cargaHeadInsertUnidades('return',1,$opcGrillaContable);
		}
		else{
			echo'<script>
					alert("'.$textAlert.' de cliente no establecido!");
					'.$focus.'
				</script>';
		}


	}

	function loadArticulos($opcGrillaContable){
		cargaHeadInsertUnidades('return',1,$opcGrillaContable);
	}

	//=========================== GUARDAR VENDEDOR DE LA FACTURA ===============================================================================//
	function guardarSolicitante($id_documento, $tablaPrincipal,$id_solicitante,$documento_solicitante,$nombre_solicitante,$link){
		$sql   = "UPDATE $tablaPrincipal SET id_solicitante='$id_solicitante',documento_solicitante=$documento_solicitante,nombre_solicitante='$nombre_solicitante' WHERE id=$id_documento ";
		$query = mysql_query($sql,$link);

		if (!$query) { echo '<script>alert("Error!\nNo se guardo el solicitante, intentelo de nuevo");</script>'; }
	}

	//=========================== GUARDAR VENDEDOR DE LA FACTURA ===============================================================================//
	function guardarAreaSolicitante($id_documento,$tablaPrincipal,$id_area_solicitante,$codigo_area_solicitante,$departamento_area_solicitante,$link){
		$sql   = "UPDATE $tablaPrincipal SET id_area_solicitante='$id_area_solicitante',codigo_area_solicitante=$codigo_area_solicitante,area_solicitante='$departamento_area_solicitante' WHERE id=$id_documento ";
		$query = mysql_query($sql,$link);

		if (!$query) { echo '<script>alert("Error!\nNo se guardo el solicitante, intentelo de nuevo");</script>'; }
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

		$styleCamposEliminados = 'display:none'; // EN CASO QUE SE REQUIERA VOLVER A MOSTRAR LOS CAMPOS ELIMINADOS

		switch ($opcGrillaContable) {
			case 'CotizacionVenta':
				$typeDocument = 'COTIZACION';
				break;

			case 'PedidoVenta':
				$typeDocument = 'PEDIDO';
				break;

			case 'EntradaAlmacen':
				$typeDocument = 'ENTRADA DE ALMACEN';
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
							<div class="label" >Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label" style="'.$styleCamposEliminados.'">Descuento</div>
							<div class="label" style="'.$styleCamposEliminados.'">Precio</div>
							<div class="label" style="'.$styleCamposEliminados.'">Total</div>
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
					<div class="contenedorDetalleTotales" style="'.$styleCamposEliminados.'">
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
					//document.getElementById("subtotalAcumulado'.$opcGrillaContable.'").innerHTML = parseFloat(subtotalAcumulado'.$opcGrillaContable.').toFixed(2);
					//document.getElementById("ivaAcumulado'.$opcGrillaContable.'").innerHTML      = parseFloat(ivaAcumulado'.$opcGrillaContable.').toFixed(2);
					//document.getElementById("totalAcumulado'.$opcGrillaContable.'").innerHTML    = parseFloat(total'.$opcGrillaContable.').toFixed(2);

					document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		echo $head;
	}

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){
		$readonly='';
		if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		$styleCamposEliminados = 'display:none';//EN CASO QUE SE REQUIERA VOLVER A MOSTRAR LOS CAMPOS ELIMINADOS

		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" >
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campoNombreArticulo">
					<input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/>
				</div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo">
					<input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/>
				</div>
				<div class="campo">
					<input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'"  onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');"/>
				</div>

				<div class="campo campoDescuento" style="'.$styleCamposEliminados.'">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" '.$readonly_descuento.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo" style="'.$styleCamposEliminados.'"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/edit.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" class="classInputInsertArticulo" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" >

				<script>document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();</script>';

		if($formaConsulta == 'return'){
			return $body;
		}
		else{
			echo $body;
		}
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaDescripcionArticulo($cont,$idArticulo,$idInsertArticulo,$observacionArt,$id,$id_centro_costos,$codigo_centro_costo,$centro_costo,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$id_empresa,$link){
		$id_empresa  = $_SESSION['EMPRESA'];

		// CONSULTAR EL ESTADO DEL DOCUMENTO
		$sql    ="SELECT estado FROM compras_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query  = mysql_query($sql,$link);
		$estado = mysql_result($query,0,'estado');
		$block  = ($estado<>0)? 'true' : '' ;

		$displayCamposOcultos = "display:none";//SE OCULTAN EL IMPUESTO Y EL CENTRO DE COSTOS YA QUE NO SE REQUIEREN EN LA REQUISICION

		$selectObservacion = "SELECT observaciones,id_centro_costos,codigo_centro_costo,centro_costo,id_impuesto FROM compras_requisicion_inventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryObservacion = mysql_query($selectObservacion,$link);
		$observacionArt   = mysql_result($queryObservacion ,0,'observaciones');
		$id_centro_costos = mysql_result($queryObservacion ,0,'id_centro_costos');
		$id_impuesto      = mysql_result($queryObservacion ,0,'id_impuesto');
		$nombreCcos       = mysql_result($queryObservacion,0,'centro_costo');
		$codigoCcos       = mysql_result($queryObservacion,0,'codigo_centro_costo');


		$sqlCentroCostos   = "SELECT id, codigo, nombre FROM centro_costos WHERE activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryCentroCostos = mysql_query($sqlCentroCostos,$link);
		// $codigoCCos = mysql_result($queryCentroCostos, 0, 'codigo');
		// $nombreCCos = mysql_result($queryCentroCostos, 0, 'nombre');

		$optionImpuesto = '<option value="0">SIN IMPUESTO</option>';
		$sqlImpuesto    = "SELECT id,impuesto FROM impuestos
							WHERE id_empresa='$id_empresa'
								AND activo=1
								AND cuenta_compra>0
								AND cuenta_compra_niif>0";
		$queryImpuesto  = mysql_query($sqlImpuesto,$link);
		while($rowImpuesto = mysql_fetch_assoc($queryImpuesto)){
			$selected = ($rowImpuesto['id'] == $id_impuesto)? 'selected': '';
			$optionImpuesto .= '<option value="'.$rowImpuesto['id'].'" '.$selected.'>'.$rowImpuesto['impuesto'].'</option>';
		}
		$btnCcos = ($block=='true')? '' : '<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCentroCostos_'.$opcGrillaContable.'('.$cont.')"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>' ;
		$btnObs = ($block=='true')? '' : '<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5px; border-left: 1px solid #D4D4D4;" onclick="guardarObservacionArt('.$cont.')"><img src="img/save_true.png" style="cursor:pointer;width:16px;height:16px;" title="Guardar Observacion"></div>' ;
		$txtAreaBloq = ($block=='true')? 'readonly="true"' : '';

		echo '<div style="color: #15428b;font-weight: bold;font-size: 13px;font-family: tahoma,arial,verdana,sans-serif;text-align:center;margin-top:20px;float: left;width:100%;">CENTRO DE COSTOS</div>
		 				<div style="float:left;width:90%; background-color:#FFF; margin-top:10px; margin-left:20px; border:1px solid #D4D4D4;">
		 					<div style="float:left;width:100px;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">CODIGO</div><div style="float:left;width:calc(100% - 107px);background-color:#F3F3F3;padding: 5px 0 5px 3px;font-weight: bold;font-size: 11px;">CENTRO DE COSTOS</div>
	 						'.$btnCcos.'
		 					<div id="codigoCcos_'.$opcGrillaContable.'" style="float:left; height:13px; width:100px; border-right:1px solid #D4D4D4; padding: 5px 0 5px 3px;">'.$codigoCcos.'</div>
		 					<div id="nombreCcos_'.$opcGrillaContable.'" style="float:left; height:13px; width:calc(100% - 110px); padding: 5px 0 5px 3px; overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">'.$nombreCcos.'</div>
		 				</div>
		 	  <div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
 					<!--<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>-->
 					<div style="float:left;width:99%;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">OBSERVACION</div>
 					'.$btnObs.'
 					<textarea id="observacionArt'.$opcGrillaContable.'" '.$txtAreaBloq.' >'.$observacionArt.'</textarea>
 				</div>';

	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($id_impuesto,$id_centro_costos,$observacionArt,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$id_ccos,$id_impuesto,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaInventario SET observaciones='$observacion',id_impuesto='$id_impuesto',id_centro_costos='$id_centro_costos' WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){ echo '<script>Win_Ventana_descripcion_Articulo_factura.close(id);</script>'; }
		else{ echo '<script>alert("La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema");</script>'; }
	}

	function guardarObservacionArt($tablaInventario,$observacionArt,$idInsertArticulo,$opcGrillaContable,$cont,$mysql){
		echo$sql="UPDATE $tablaInventario SET observaciones='$observacionArt' WHERE id='$idInsertArticulo' AND activo=1";
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

	function actualizarCcos($idInsertArticulo,$tablaInventario,$opcGrillaContable,$id_centro_costos,$codigo_centro_costo,$centro_costo,$id_empresa,$link){
		$sqlCentroCostos = "SELECT COUNT(id) AS contCcos
						FROM centro_costos
						WHERE codigo<>'$codigo_centro_costo'
							AND codigo LIKE '$codigo_centro_costo%',
							nombre<>'$centro_costo'
							AND nombre LIKE '$centro_costo'";

		$queryCentroCostos = mysql_query($sqlCentroCostos,$link);
		$contCcos  = mysql_result($queryCentroCostos, 0, 'contCcos');
		if($contCcos > 0){ echo'padre'; exit; }

 		$sql  = "UPDATE $tablaInventario
		 		 SET    id_centro_costos='$id_centro_costos',
			 	    	codigo_centro_costo='$codigo_centro_costo',
			 			centro_costo='$centro_costo'

		 			  WHERE  id='$idInsertArticulo'
		 			  AND activo=1";

 		$query = mysql_query($sql,$link);

 		if ($query){ echo 'true'; }
 		else{ echo 'false'; }
 	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO =============================observacionCuentaRequisicionCompra===========================================//
	function buscarArticulo($campo,$valorArticulo,$idArticulo,$id_empresa,$idCliente,$opcGrillaContable,$whereBodega,$exentoIva,$link){

		$sqlArticulo = "SELECT I.id,
							I.codigo,
							I.code_bar,
							I.precio_venta,
							I.nombre_equipo,
							I.numero_piezas,
							I.inventariable,
							IT.cantidad_minima_stock,
							I.unidad_medida,
							I.cantidad_unidades,
							I.id_impuesto,
							IT.cantidad
						FROM items AS I,
							inventario_totales AS IT
						WHERE I.activo=1
							AND I.id_empresa=$id_empresa
							AND IT.id_item=I.id
							AND I.estado_compra='true'
							$whereBodega
							AND (I.code_bar = '$valorArticulo' OR I.codigo = '$valorArticulo')
						LIMIT 0,1";

		$query = mysql_query($sqlArticulo,$link);

		$id                    = mysql_result($query,0,'id');
		$codigo                = mysql_result($query,0,'codigo');
		$precio_venta          = mysql_result($query,0,'precio_venta');
		$codigoBarras          = mysql_result($query,0,'code_bar');
		$nombre_unidad         = mysql_result($query,0,'unidad_medida');
		$numero_unidad         = mysql_result($query,0,'cantidad_unidades');
		$nombreArticulo        = mysql_result($query,0,'nombre_equipo');
		$id_impuesto           = mysql_result($query,0,'id_impuesto');
		$cantidad              = mysql_result($query,0,'cantidad');
		$cantidad_minima_stock = mysql_result($query,0,'cantidad_minima_stock');
		$inventariable         = mysql_result($query,0,'inventariable');

		$valorImpuesto = 0;
		$impuesto      = '';
		$script        = '';

		// if($exentoIva != 'Si'){
			//consultamos el valor del impuesto para asignarlo al campo oculto,
			$sqlImpuesto   = "SELECT impuesto,valor FROM impuestos WHERE id=$id_impuesto";
			$queryImpuesto = mysql_query($sqlImpuesto,$link);
			$valorImpuesto = mysql_result($queryImpuesto,0,'valor');
			$impuesto = mysql_result($queryImpuesto,0,'impuesto');

			if ($valorImpuesto!="" && $impuesto!="") {
				$script='if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
							arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valorImpuesto.'"};
						 }';
			}
			else{
				$script='';
			}
		// }
		// else{
		// 	$id_impuesto=0;
		// }


		if($id > 0){
			if ($cantidad>0 ) {
			//VERIFICAR LA CANTIDAD MINIMA DE STOCK CON LA OPCGRILLACONTABLE PARA FILTRAR DE QUE SOLO SEA PARA LA REMISION
				if ($cantidad <= $cantidad_minima_stock && ($opcGrillaContable=='RemisionesVenta' || $opcGrillaContable=='FacturaVenta')) {
					$texto = ($cantidad == $cantidad_minima_stock)? "es igual": "es menor";
					echo '<script>alert("Aviso!\nEste articulo '.$texto.' a la cantidad minima de '.$cantidad_minima_stock.' '.$nombre_unidad.' en stock\nSolo restan '.$cantidad.' '.$nombre_unidad.' del articulo");</script>';
				}

				//si la cantidad del articulo es mayor a cero en la bodega, se permite realizar la venta del articulo
				echo'<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       = "'.$nombre_unidad.' x '.$numero_unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "'.$id.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value    = "'.$codigo.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value  = "'.$precio_venta.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "'.$nombreArticulo.'";
						document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value    = "'.$id_impuesto.'";

						setTimeout(function(){ document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },50);
						'.$script.'
					</script>';
			}
			else if($inventariable=='true' && ($opcGrillaContable == 'RemisionesVenta' || $opcGrillaContable == 'FacturaVenta')){
				echo'<script>
						alert("El articulo se agoto en esta bodega!");
						setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "0";
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       = "";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value  = "";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "";
					</script>';
			}
			else{
				echo'<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       = "'.$nombre_unidad.' x '.$numero_unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     = "'.$id.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value    = "'.$codigo.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value  = "'.$precio_venta.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value = "'.$nombreArticulo.'";
						document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value    = "'.$id_impuesto.'";

						setTimeout(function(){ document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },50);
						'.$script.'
					</script>';
			}
		}
		else{
			echo'<script>
					alert("El codigo '.$valorArticulo.' No se encuentra asignado en el inventario");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$idArticulo.'").focus(); },100);
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value     ="0";
					document.getElementById("unidades'.$opcGrillaContable.'_'.$idArticulo.'").value       ="";
					document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value  ="";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$idArticulo.'").value ="";
				</script>';
		}
	}

	//=========================== FUNCION PARA CAMBIAR EL PROVEEDOR DE LA FACTURA ===============================================================//
	function cambiaCliente($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$link,$tablaPrincipal){
		$sqlDeleteInventario    = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal = '$id'";
		$queryDeleteInventario  = mysql_query($sqlDeleteInventario,$link);

		$sqlDeleteRetenciones   = "DELETE FROM $tablaRetenciones  WHERE $idTablaPrincipal = '$id'";
		$queryDeleteRetenciones = mysql_query($sqlDeleteRetenciones,$link);

		$sqlUpdateProveedor     = "UPDATE $tablaPrincipal SET id_proveedor = 0, exento_iva='' WHERE id = '$id'";
		$queryUpdateProveedor   = mysql_query($sqlUpdateProveedor,$link);
		$script='';
		if ($opcGrillaContable=='FacturaVenta') {
			$script='document.getElementById("contenedorCheckbox'.$opcGrillaContable.'").innerHTML="";';
		}

		echo'<script>
				id_cliente_'.$opcGrillaContable.'   = 0;
				contArticulos'.$opcGrillaContable.' = 1;
				nitCliente'.$opcGrillaContable.'    = 0;
				codigoCliente'.$opcGrillaContable.' = 0;
				nombreCliente'.$opcGrillaContable.' = "";
				exento_iva_'.$opcGrillaContable.'   = "";
				document.getElementById("codCliente'.$opcGrillaContable.'").focus();
				'.$script.'
			</script>';
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederArticulo($exento_iva,$id,$idRegistro,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$sqlArticulo ="SELECT id_inventario,codigo,costo_unitario,cantidad_unidad_medida,nombre_unidad_medida,nombre,cantidad,tipo_descuento,descuento,id_impuesto
						FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal='$id' AND id='$idRegistro' LIMIT 0,1; ";

		$query = mysql_query($sqlArticulo,$link);

		$id_inventario      = mysql_result($query,0,'id_inventario');
		$codigo             = mysql_result($query,0,'codigo');
		$costo              = mysql_result($query,0,'costo_unitario');
		$nombre_unidad      = mysql_result($query,0,'nombre_unidad_medida');
		$nombreArticulo     = mysql_result($query,0,'nombre');
		$numeroPiezas       = mysql_result($query,0,'cantidad_unidad_medida');
		$cantidad_articulo  = mysql_result($query,0,'cantidad');
		$tipoDesc           = mysql_result($query,0,'tipo_descuento');
		$descuento_articulo = mysql_result($query,0,'descuento');
		$id_impuesto        = ($exento_iva=='Si')? 0 : mysql_result($query,0,'id_impuesto');

		if ($tipoDesc=='porcentaje') {
			$imgDescuento    = 'img/porcentaje.png';
			$tituloDescuento = 'En porcentaje';
		}
		else{
			$imgDescuento    = 'img/pesos.png';
			$tituloDescuento = 'En pesos';
		}

		echo'<script>
				document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value                 = "'.$nombre_unidad.' x '.$numeroPiezas.'";
				document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value               = "'.$id_inventario.'";
				document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value              = "'.$codigo.'";
				document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value            = "'.$costo.'";
				document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value           = "'.$nombreArticulo.'";
				document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.($cantidad_articulo * 1).'";
				document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "'.$descuento_articulo.'";
				document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value              = "'.$id_impuesto.'";
				document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'").value       = "'.($cantidad_articulo*$costo).'";

				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

				document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","'.$imgDescuento.'");
				document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","'.$tituloDescuento.'");

				//document.getElementById("tipoDescuentoArticulo_'.$cont.'").setAttribute("src","img/reload.png");
			</script>';
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteArticulo($cont,$id,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){
		$sqlDelete   = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND id='$idArticulo'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{ echo '<script>(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")); </script>'; }
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
	function UpdateFormaPago($id,$idFormaPago,$tablaPrincipal,$opcGrillaContable,$link,$fechaVencimiento){
		//si es una factura se actualiza el id de la forma de pago
		if ($opcGrillaContable=='FacturaVenta') {
			$sql   = "UPDATE $tablaPrincipal SET id_forma_pago='$idFormaPago',fecha_vencimiento='$fechaVencimiento' WHERE id='$id'";
			$query = mysql_query($sql,$link);

			if ($query){ echo'<script>calculaPlazo'.$opcGrillaContable.'(); </script>'; }
			else{ echo '<script>alert("Error!\nNo se actualizo la forma de pago");</script>'; exit;}
		}
		//sino se actualiza directamente la fecha final en cotizacion, pedido, remision
		else{
			$sql   = "UPDATE $tablaPrincipal SET fecha_finalizacion='$idFormaPago' WHERE id='$id'";
			$query = mysql_query($sql,$link);

			if (!$query){ echo '<script>alert("Error!\nNo se actualizo la fecha de vencimiento");</script>'; }
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/****************************************************************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA FACTURA, COTIZACION, PEDIDO Y CARGAR UNA NUEVA ==============================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$mysql){
		global $id_empresa,$id_sucursal;
		// VALIDAR QUE CONTENGA TODOS LOS CENTROS DE COSTO LOS ITEMS
		validaCentroCostosItems($id,$mysql);
		// VALIDAR QUE TENGA AREA SOLICITANTE Y PERSONA SOLICITANTE
		$sql="SELECT id_solicitante,id_area_solicitante FROM compras_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=$mysql->query($sql,$mysql->link);
		$id_solicitante      = $mysql->result($query,0,'id_solicitante');
		$id_area_solicitante = $mysql->result($query,0,'id_area_solicitante');
		if ($id_solicitante==0 || $id_solicitante=='') {
			echo '<script>
					alert("El campo solicitante es obligatorio!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		if ($id_area_solicitante==0 || $id_area_solicitante=='') {
			echo '<script>
					alert("El campo area solicitante es obligatorio!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$titulo = 'Requisicion de Compra';

		// VERIFICAR LOS AUTORIZADORES, SI NO TIENE, ENTONCES DEJAR LA REQUISICION AUTORIZADA
		$sql="SELECT COUNT(id) AS autorizardores FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area_solicitante";
		$query=$mysql->query($sql,$mysql->link);
		$autorizardores = $mysql->result($query,0,'autorizardores');
		if ($autorizardores==0 || $autorizardores=='') {
			$campoUpdate = "autorizado='true',";
		}

		$sql    = "UPDATE $tablaPrincipal
					SET estado='1',
						observacion='$observacion',
						$campoUpdate
						pendientes_facturar=(
						 SELECT SUM(saldo_cantidad)
						 FROM compras_requisicion_inventario
						 WHERE id_requisicion_compra = '$id') WHERE id='$id'";
		$query  = $mysql->query($sql,$mysql->link);

		$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'";
		$consecutivo = $mysql->result($mysql->query($sqlSelect,$mysql->link),0,'consecutivo');

		if ($query) {

			if ($campoUpdate=='') {
				// EVNIAR EMAIL A LOS ENCARGADOS DE AUTORIZAR LAS REQUISICIONES
				include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
				$mail  = new PHPMailer();

				$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
				$queryConexion = $mysql->query ($sqlConexion,$mysql->link);
				if($row_consulta= $mysql->fetch_array($queryConexion)){
					$seguridad     = $row_consulta['seguridad_smtp'];
					$pass          = $row_consulta['password'];
					$user          = $row_consulta['correo'];
					$puerto        = $row_consulta['puerto'];
					$servidor      = $row_consulta['servidor'];
					$from          = $row_consulta['correo'];
					$autenticacion = $row_consulta['autenticacion'];
				}

				if ($user=='') {
					echo '<script>
							alert("No exite ninguna configuracion de correo SMTP!\nConfigure el correo desde el panel de control en el boton configuracion SMTP\nPara que se puedan enviar las notificaciones a las personas encargadas de autorizar el documento");
						</script>';
				}

				//CONSULTAR LA INFORMACION DE LA EMPRESA
				$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,nit_completo,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular
								FROM empresas
								WHERE id='$id_empresa'
								LIMIT 0,1";
				$queryEmpresa = $mysql->query($sqlEmpresa,$mysql->link);

				$nombre_empresa        = $mysql->result($queryEmpresa,0,'nombre');
				$tipo_documento_nombre = $mysql->result($queryEmpresa,0,'tipo_documento_nombre');
				$documento_empresa     = $mysql->result($queryEmpresa,0,'nit_completo');
				$ciudad                = $mysql->result($queryEmpresa,0,'ciudad');
				$direccion_empresa     = $mysql->result($queryEmpresa,0,'direccion');
				$razon_social          = $mysql->result($queryEmpresa,0,'razon_social');
				$tipo_regimen          = $mysql->result($queryEmpresa,0,'tipo_regimen');
				$telefonos             = $mysql->result($queryEmpresa,0,'telefono').' - '.$mysql->result($queryEmpresa,0,'celular');
				$actividad_economica   = $mysql->result($queryEmpresa,0,'actividad_economica');

				// CONSULTAR LA INFORMACION DEL DOCUMENTO
				$sql="SELECT sucursal,
								bodega,
								fecha_inicio,
								consecutivo,
								codigo_centro_costo,
								centro_costo,
								documento_solicitante,
								nombre_solicitante,
								codigo_area_solicitante,
								area_solicitante,
								tipo_nombre,
								observacion,
								documento_usuario,
								usuario,
								id_solicitante,
								id_area_solicitante
						FROM compras_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
				$query=$mysql->query($sql,$mysql->link);

				$sucursal                = $mysql->result($query,0,'sucursal');
				$bodega                  = $mysql->result($query,0,'bodega');
				$consecutivo             = $mysql->result($query,0,'consecutivo');
				$fecha_inicio            = $mysql->result($query,0,'fecha_inicio');
				$codigo_centro_costo     = $mysql->result($query,0,'codigo_centro_costo');
				$centro_costo            = $mysql->result($query,0,'centro_costo');
				$documento_solicitante   = $mysql->result($query,0,'documento_solicitante');
				$nombre_solicitante      = $mysql->result($query,0,'nombre_solicitante');
				$codigo_area_solicitante = $mysql->result($query,0,'codigo_area_solicitante');
				$area_solicitante        = $mysql->result($query,0,'area_solicitante');
				$tipo_nombre             = $mysql->result($query,0,'tipo_nombre');
				$observacion             = $mysql->result($query,0,'observacion');
				$documento_usuario       = $mysql->result($query,0,'documento_usuario');
				$usuario                 = $mysql->result($query,0,'usuario');
				$id_solicitante          = $mysql->result($query,0,'id_solicitante');
				$id_area_solicitante     = $mysql->result($query,0,'id_area_solicitante');


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
				$mail->Subject    = "Notificacion: Nueva Requisicion creada. Pendiente su Autorizacion tipo: $tipo_nombre  Consecutivo: $consecutivo ";
				$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
				$mail->WordWrap   = 50; // set word wrap

				$body  = '<font color="black">
						<br>
						<b>'.$razon_social.'</b><br>
						<b>'.$tipo_regimen.'</b><br>
						<b>'.$tipo_documento_nombre.': </b>'.$documento_empresa.'<br>
						<b>Direccion: </b>'.$direccion_empresa.' - <b>'.$ciudad.' </b><br>
						<b>Telefono: </b>'.$telefonos.'<br>

						<br>

						<table>
							<tr>
								<td>Asunto:</td>
								<td>Nueva Requisicion a la espera de su Autorizacion</td>
							</tr>
							<tr>
								<td>Consecutivo</td>
								<td style="font-size:24px;font-weight:bold;">'.$consecutivo.'</td>
							</tr>
							<tr>
								<td>Bodega: </td>
								<td> '.$bodega.'</td>
							</tr>
							<tr>
								<td>Sucursal: </td>
								<td>'.$sucursal.'</td>
							</tr>
							<tr>
								<td>Persona Solicitante: </td>
								<td>'.$documento_solicitante.' - '.$nombre_solicitante.' </td>
							</tr>
							<tr>
								<td>Tipo</td>
								<td>'.$tipo_nombre.'</td>
							</tr>
							<tr>
								<td>Area Solicitante</td>
								<td>'.$area_solicitante.'</td>
							</tr>
							<tr>
								<td>Usuario Creador</td>
								<td>'.$documento_usuario.' - '.$usuario.' </td>
							</tr>
						</table>

						<!--El usuario '.$documento_usuario.' - '.$usuario.' creo la Requisicion No. <span style="font-size:18px;font-weight:bold;">'.$consecutivo.'</span> en la bodega <b>'.$bodega.'</b> de la sucursal <b>'.$sucursal.'</b> solicitado por '.$documento_solicitante.' - '.$nombre_solicitante.' del area de '.$area_solicitante.'  <br>
						Ingrese a la aplicacion y dirijase al modulo de compras, en la pesta&ntilde;a requisicion, busque el documento y oprima el boton autorizar y seleccione la opcion de autorizacion que desee<br><br>-->
						<br>
						<br>
						<br>
						Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.
					</font><br>';

				$mail->Body = $body;

				$mail->MsgHTML($body);

				// CONSULTAR LAS DIRECCIONES DE EMAIL DE LOS ENCARGADOS DE AUTORIZAR EL DOCUMENTO
				$sql="SELECT email FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area_solicitante AND orden=1 LIMIT 0,1";
				$query=$mysql->query($sql,$mysql->link);
				while ($row=$mysql->fetch_array($query)) {
					$mail->AddAddress($row['email']);
				}

				$mail->IsHTML(true); // send as HTML

				if(!$mail->Send()){ echo $mail->ErrorInfo.'<script>alert("Se genero el documento pero no se pudo enviar por email las notificaciones a los encargados de autorizar el documento\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
				$mail->ClearAddresses();

			}

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES
						($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Requisicion de Compra',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','RQ')";
			$queryLog = $mysql->query($sqlLog,$mysql->link);

			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "requisicion/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : "'.$idBodega.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_documento  : "'.$id.'"
						}
					});

					Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").enable();
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
		else{
			rollback($id,'compras_requisicion',"No se guardo la $titulo,\\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema",$id_empresa,$mysql);
		}
	}

	// FUNCION PARA VALIDAR QUE TODOS LOS ITEMS TENGAN EL CENTRO DE COSTOS
	function validaCentroCostosItems($id_requisicion,$mysql){
		$id_empresa=$_SESSION['EMPRESA'];
		if ($id_empresa==1 || $id_empresa==47) {
			$sql="SELECT codigo,nombre,id_centro_costos
			 		FROM compras_requisicion_inventario WHERE activo=1 AND id_requisicion_compra=$id_requisicion";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)){
				$id_centro_costos = $row['id_centro_costos'];
				$codigo = $row['codigo'];
				$nombre = $row['nombre'];
				if ($id_centro_costos<=0 || $id_centro_costos=='' || is_null($id_centro_costos)) {
					rollback($id_requisicion,'compras_requisicion',"El item $codigo - $nombre",$id_empresa,$mysql);
				}
			}
		}

	}

	//=========================== FUNCION PARA VALIDAR LA CANTIDAD DE ARTICULOS A DAR DE BAJA =====================================================//
	//VALIDA QUE LA CANTIDAD DE LOS DOCUMENTOS INSERTADOS EN EL DOCUMETNO SEA CORRECTA, ES DECIR QUE NO SEA MAYOR A LA EXISTENTE
	function validaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$link){
		//CREAMOS UN ARRAY PARA GUARDAR LOS VALORES CON LAS RESPUESTAS Y LUEGO RETORNARLO
		//EN LA PRIMERA POCISION SE GUARDARA LO QUE RETORNO Y EN LA SEGUNDA EL NOMBRE DEL ARTICULO SI LA PRIMERA POCISION ES FALSE
		$arrayRespuesta;

		//CONSULTAMOS TODOS LOS ARTICULOS DEL DOCUMENTO
		$sqlArticulosDocumento="SELECT id_inventario,cantidad,nombre
									FROM $tablaInventario
									WHERE activo = 1
									AND $idTablaPrincipal = '$id'";
		$queryArticulosDocumento=mysql_query($sqlArticulosDocumento,$link);

		while ($rowArticulosDocumento=mysql_fetch_array($queryArticulosDocumento)) {
			$id_inventario = $rowArticulosDocumento['id_inventario'];
			//CONSULTAMOS SI HAY ARTICULOS REPETIDOS, SI LOS HAY, LOS AGRUPAMOS SACANDO LA SUMA DE SUS CANTIDADES
			$sqlArticulo="SELECT
								TI.id_inventario,
								Sum(TI.cantidad) AS cantidad_total,
								TI.nombre,
								TIT.id_item,
								TIT.cantidad
							FROM
								$tablaInventario AS TI,
								inventario_totales AS TIT
							WHERE
								TI.activo = 1
							AND TI.$idTablaPrincipal = '$id'
							AND TI.id_inventario = '$id_inventario'
							AND TI.inventariable = 'true'
							AND TIT.id_item='$id_inventario'
							AND TIT.id_sucursal='$id_sucursal'
							AND TIT.id_ubicacion='$idBodega'";

			$queryArticulo=mysql_query($sqlArticulo,$link);
			$cantidad_documento=mysql_result($queryArticulo,0,'cantidad_total');
			$cantidad_inventario=mysql_result($queryArticulo,0,'cantidad');

			if ($cantidad_documento>$cantidad_inventario) {
				$arrayRespuesta[0]='false';
				$arrayRespuesta[1]=$rowArticulosDocumento['nombre'];
				return $arrayRespuesta;
			}
		}
		$arrayRespuesta[0]='true';
		return $arrayRespuesta;
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link){
		//CONSULTAMOS EL ESTADO DEL DOCUMENTO
		$sql   = "SELECT estado,autorizado,id_area_solicitante FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento";
		$query = mysql_query($sql,$link);
		$estado              = mysql_result($query,0,'estado');
		$autorizado          = mysql_result($query,0,'autorizado');
		$id_area_solicitante = mysql_result($query,0,'id_area_solicitante');

		// CONSULTAR SI EL DOCUMENTO TIENE AUTORIZACIONES
		$sql="SELECT COUNT(id) AS autorizardores FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area_solicitante";
		$query=mysql_query($sql,$link);
		$autorizardores = mysql_result($query,0,'autorizardores');

		if ($autorizado=='true' && $autorizado>0) {
			echo '<script>
						alert("La Requisicion esta autorizada! por lo tanto no se puede alterar, deben quitar las autorizaciones para poder modificarla");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}

		if($estado == 2){

			$sql = "SELECT consecutivo_cruce AS consecutivo,tipo_documento_cruce AS tipo FROM compras_requisicion_doc_cruce WHERE id_requisicion = '$idDocumento' AND activo=1";
            $query = mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
				$contenido .= $row['tipo'].' '.$row['consecutivo'].'\n';
			}
			echo '<script>
						alert("La Requisicion esta cruzada con los siguientes documentos:\n'.$contenido.'\nDebe eliminar el cruce o cancelar los documentos!");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}

		//moverCuentasDocumento($idDocumento,$id_sucursal,0,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);
		// ACTUALIZAR EL ESTADO DEL DOCUMENTO
		$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento";
		$query=mysql_query($sql,$link);
		if ($query) {
			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
					VALUES ($idDocumento,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Entrada de Almacen',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','EA')";
			$queryLog=mysql_query($sqlLog,$link);

			echo '<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "requisicion/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_documento  : "'.$idDocumento.'"
						}
					});
					Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").enable();
					if(document.getElementById("modal")){
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
		}
		else{
			echo '<script>
					alert("Error!\nNo se actualizo el documento, intente de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					if(document.getElementById("modal")){
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
		}

	}

	//=========================== FUNCION PARA BUSCAR EL VALOR DEL IVA DE UN ARTICULO ===========================================================//
	function buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$eanArticulo,$link){

		$sql      = "SELECT impuestos.id,impuestos.impuesto,impuestos.valor FROM items, impuestos WHERE items.id='$id_inventario' AND items.id_impuesto = impuestos.id";
		$query    = mysql_query($sql,$link);
		$id       = mysql_result($query,0,'id');
		$impuesto = mysql_result($query,0,'impuesto');
		$valor    = mysql_result($query,0,'valor');

		if ($query) {
			echo '<script>
										if(document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value > 0){                   //mostrar la imagen deshacer y actualizar
                        document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "inline";
                        document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "inline";
                    }
                    else{
												document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
										}

                    document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       = "'.$unidadMedida.'";
                    document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$idArticulo.'";
                    document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$eanArticulo.'";
                    document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "'.$costo.'";
                    document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$nombreArticulo.'";
                    document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$id.'";

                    Win_Ventana_buscar_Articulo_factura.close();

                    if (typeof(arrayIva'.$opcGrillaContable.'["'.$id.'"])=="undefined") {
                       arrayIva'.$opcGrillaContable.'["'.$id.'"]={nombre:"'.$impuesto.'",valor:"'.$valor.'"};
                    }

				</script>';
		}
		else{
			echo '<script>console.log("error al buscar iva");</script>';
		}

		// if (!$query) { echo 'false'; }
		// else { echo $valorImpuesto; }
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		if ($exento_iva=='Si') {
			$iva=0;
		}

		// if ($opcGrillaContable=='RemisionesVenta' || $opcGrillaContable=='PedidoVenta') {

			$sqlInsert = "INSERT INTO $tablaInventario(
						  	$idTablaPrincipal,
								id_inventario,
								cantidad,
								saldo_cantidad,
								tipo_descuento,
								descuento,
								costo_unitario,
								valor_impuesto)
						VALUES(
								'$id',
								'$idInventario',
								'$cantArticulo',
								'$cantArticulo',
								'$tipoDesc',
								'$descuentoArticulo',
								'$costoArticulo',
								'$iva')";
		// }
		// else{
			// $sqlInsert = "INSERT INTO $tablaInventario (
			// 					$idTablaPrincipal,
			// 					id_inventario,
			// 					cantidad,
			// 					tipo_descuento,
			// 					descuento,
			// 					costo_unitario
			// 					)
			// 			VALUES( '$id',
			// 					'$idInventario',
			// 					'$cantArticulo',
			// 					'$tipoDesc',
			// 					'$descuentoArticulo',
			// 					'$costoArticulo')";
		// }


		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

		if($lastId > 0){

			echo'<script>
					document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value            = '.$lastId.'

					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
					document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display        = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display    = "none";

					document.getElementById("descripcionArticulo'.$opcGrillaContable.'_'.$cont.'").style.display = "block";
					document.getElementById("deleteArticulo'.$opcGrillaContable.'_'.$cont.'").style.display      = "block";

				</script>'.cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);

		}
		else{
			$cont++;
			echo '<script>
					  	alert("Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema.");
							var body = document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'");
							body.parentNode.removeChild(body);
							contArticulos'.$opcGrillaContable.'--;
						</script>';

		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo
		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura

		$sqlArticuloAnterior   = "SELECT cantidad,tipo_descuento,descuento,costo_unitario,id_impuesto AS valor_impuesto FROM $tablaInventario WHERE id='$idInsertArticulo' AND $idTablaPrincipal='$id' ";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);

		$cantidad       = mysql_result($queryArticuloAnterior,0,'cantidad');
		$tipo_descuento = mysql_result($queryArticuloAnterior,0,'tipo_descuento');
		$descuento      = mysql_result($queryArticuloAnterior,0,'descuento');
		$costo_unitario = mysql_result($queryArticuloAnterior,0,'costo_unitario');
		$valor_impuesto = mysql_result($queryArticuloAnterior,0,'valor_impuesto');

		if ($exento_iva=='Si') {
			$valor_impuesto=0;
		}

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		//echo'<script>calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'",'.$cont.');</script>';
		// if ($opcGrillaContable=='RemisionesVenta') {
			$sqlUpdateArticulo   = "UPDATE $tablaInventario
									SET id_inventario='$idInventario',
										cantidad       ='$cantArticulo',
										saldo_cantidad ='$cantArticulo',
										tipo_descuento ='$tipoDesc',
										descuento      ='$descuentoArticulo',
										costo_unitario ='$costoArticulo',
										valor_impuesto = '$iva'
									WHERE $idTablaPrincipal=$id
										AND id=$idInsertArticulo";
		// }
		// else{
		// 	$sqlUpdateArticulo   = "UPDATE $tablaInventario
		// 							SET id_inventario='$idInventario',
		// 								cantidad       ='$cantArticulo',
		// 								tipo_descuento ='$tipoDesc',
		// 								descuento      ='$descuentoArticulo',
		// 								costo_unitario ='$costoArticulo',
		// 								valor_impuesto = '$iva'
		// 							WHERE $idTablaPrincipal=$id
		// 								AND id=$idInsertArticulo";
		// }

		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);

		if ($queryUpdateArticulo) {
			echo'<script>
					document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
					//llamamos la funcion para recalcular el costo de la factura
					//calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantArticulo.',"'.$descuentoArticulo.'",'.$costoArticulo.',"agregar","'.$tipoDesc.'","'.$iva.'",'.$cont.');
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION INDIVIDUAL POR ARTICULO =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
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
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$link){

		//CONSULTAMOS EL ESTADO DEL DOCUMENTO
		$sql    = "SELECT estado,consecutivo,autorizado,id_area_solicitante FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query  = mysql_query($sql,$link);
		$estado              = mysql_result($query,0,'estado');
		$consecutivo         = mysql_result($query,0,'consecutivo');
		$autorizado          = mysql_result($query,0,'autorizado');
		$id_area_solicitante = mysql_result($query,0,'id_area_solicitante');

		// CONSULTAR SI EL DOCUMENTO TIENE AUTORIZACIONES
		$sql="SELECT COUNT(id) AS autorizardores FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area_solicitante";
		$query=mysql_query($sql,$link);
		$autorizardores = mysql_result($query,0,'autorizardores');

		if ($autorizado=='true' && $autorizardores>0) {
			echo '<script>
						alert("La Requisicion esta autorizada! por lo tanto no se puede alterar, deben quitar las autorizaciones para poder modificarla");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}

		if($estado == 2){


			$sql = "SELECT consecutivo_cruce AS consecutivo,tipo_documento_cruce AS tipo FROM compras_requisicion_doc_cruce WHERE id_requisicion = '$id' AND activo=1";
            $query = mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
				$contenido .= $row['tipo'].' '.$row['consecutivo'].'\n';
			}
			echo '<script>
						alert("La Requisicion esta cruzada con los siguientes documentos:\n'.$contenido.'\nDebe eliminar el cruce o cancelar los documentos!");
						if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						}
				  </script>';

		  	exit;
		}

		//IDENTIFICAMOS EL DOCUMENTO QUE SE VA A CANCELAR
        if($consecutivo != ''){
			$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
		}
		else{
			$sqlUpdate="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";
		}
		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
					VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Cotizacion de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','CE')";


		$queryUpdate = mysql_query($sqlUpdate,$link);				//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		if (!$queryUpdate) { echo '<script>alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
		else{
			//INSERTAR EL LOG DE EVENTOS
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
					nueva'.$opcGrillaContable.'();
					// Ext.get("contenedor_'.$opcGrillaContable.'").load({
					// 	url     : "requisicion/grillaContableBloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_documento      : "'.$id.'",
					// 		opcGrillaContable : "'.$opcGrillaContable.'",
					// 		filtro_bodega     : "'.$idBodega.'"
					// 	}
					// });
					if(document.getElementById("modal")){
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
		}
	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	AL RESTAURAR TENER EN CUENTA:																			//
		//																											//
		//	-> SI FUE GENERADO, ENTONCES, SE ACTUALIZA CON ESTADO IGUAL A 1 PARA DEJAR EL DOCUMENTO COMO ESTABA 	//
		//	-> SI NO SE GENERO ANTES DE CANCELARLO ENTONCES SU ESTADO QUE DA IGUAL A 0 'NO GUARDADO'				//
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $sqlConsulDoc="SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
		//VERIFICAR SI FUE GENERADO ANTES DE CANCELAR
		$queryConsulDoc = mysql_query($sqlConsulDoc,$link);
		$consecutivo    = mysql_result($queryConsulDoc,0,'consecutivo');

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Remision de Venta',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','CE')";


		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			//INSERTAR EL LOG DE EVENTOS
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
			        titulo="Requisicion de Compra";
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "requisicion/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_documento      : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : "'.$idBodega.'"
						}
					});
                    document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = titulo+"<br>N."+"'.$consecutivo.'";
				</script>';
		}
		else{ echo '<script>
			            alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");
			            document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
		            </script>'; return; }
 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function ventanaAutorizaDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$mysql){
 		$id_empleado = $_SESSION['IDUSUARIO'];
 		// CONSULTAR EL AREA DEL DOCUMENTO
 		$sql   = "SELECT id_area_solicitante,estado FROM compras_requisicion WHERE activo=1 ANd id_empresa=$id_empresa AND id=$idDocumento";
 		$query = $mysql->query($sql,$mysql->link);
		$id_area = $mysql->result($query,0,'id_area_solicitante');
		$estado  = $mysql->result($query,0,'estado');

 		$sql="SELECT id,id_empleado,documento_empleado,nombre_empleado,cargo,tipo_autorizacion,orden
 			FROM autorizacion_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_requisicion=$idDocumento AND id_area=$id_area";
 		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)){
			// $arrayTipoAutorizacion[$row['id_empleado']]              = $row['tipo_autorizacion'];
			$arrayAutorizaciones[$row['orden']][$row['id_empleado']] = array('id' => $row['id'], 'tipo_autorizacion' => $row['tipo_autorizacion'] );
		}

 		$sql="SELECT id,id_empleado,documento_empleado,nombre_empleado,cargo,orden FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area ORDER BY orden ASC";
 		$query=$mysql->query($sql,$mysql->link);
 		while ($row=$mysql->fetch_array($query)) {
			$selectAutorizacion = '';
			$contentAut         = '';

			// SI NO ES EL USUARIO O SI EL DOCUMENTO NO ESTA GENERADO O ESTA CRUZADO O ELIMINADO, NO MOSTRAR LOS SELECT SI NO SOLO TEXTO
			if ($id_empleado<>$row['id_empleado'] || $estado<>1){
 				$contentAut = ($arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']<>'')? '<img style="height:11px;" src="img/'.$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion'].'.png" > '.$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']
 																				: '<span style="color:#A8A8A8;font-style:italic;">Sin Autorizacion</span>' ;
				$padding = '';
 			}
 			// SI ES EL USUARIO Y EL DOCUMENTO ESTA HABILITADO, MOSTRAR LOS SELECT PARA QUE PUEDAN REALIZAR EL PROCESO DE AUTORIZACION
 			else if ($id_empleado==$row['id_empleado']) {
 				$selectAutorizacion = "<select id='tipo_autorizacion_$row[id]' style='border:none;' onchange='autorizar$opcGrillaContable($row[id],$id_area,$row[orden])'>
											<option value=''>Sin Autorizacion</option>
											<option value='Autorizada'>Autorizada</option>
											<option value='Aplazada'>Aplazada</option>
											<option value='Rechazada'>Rechazada</option>
										</select>";
				$padding = 'padding:1.5px;';
				if ($arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']<>'') {
					$script .= "document.getElementById('tipo_autorizacion_$row[id]').value='".$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']."';";
				}

				if (!empty($arrayAutorizaciones[$row['orden']+1])) {
					$script .= "document.getElementById('tipo_autorizacion_$row[id]').disabled=true;";
				}

 			}




 			$bodyUsuarios.='<div class="filaDivs" style="width:90px;">'.$row['documento_empleado'].'</div>
							<div class="filaDivs" style="width:200px;">'.$row['nombre_empleado'].'</div>
							<div class="filaDivs" style="width:145px;">'.$row['cargo'].'</div>
							<div class="filaDivs" style="width:130px;border-right: none;text-align:center;'.$padding.'" id="div_img_autorizacion_'.$row['id_empleado'].'">

							'.$selectAutorizacion.'
							'.$contentAut.'
							</div>';

 		}

 		$sql="SELECT documento,nombre,cargo FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
 		$query=$mysql->query($sql,$mysql->link);
		$documento_empleado = $mysql->result($query,0,'documento');
		$nombre_empleado    = $mysql->result($query,0,'nombre');
		$cargo              = $mysql->result($query,0,'cargo');

		?>
 			<style>
				.titulos_ventana{
					color       : #15428B;
					font-weight : bold;
					font-size   : 13px;
					font-family : tahoma,arial,verdana,sans-serif;
					text-align  : center;
					float       : left;
					width       : 100%;
					border-top  : 1px solid #8DB2E3;
					padding-top : 15px;
				}

				.contenedor_tablas_cuentas{
					float            : left;
					width            : 597px;
					height 			 : 180px;
					background-color : #FFF;
					margin-top       : 10px;
					margin-left      : 20px;
					border           : 1px solid #D4D4D4;
				    overflow-y: scroll;
				}

				.headDivs{
					float            : left;
					background-color : #F3F3F3;
					padding          : 5 0 5 3;
					font-size        : 11px;
					font-weight      : bold;
					border-right     : 1px solid #D4D4D4;
					border-bottom    : 1px solid #D4D4D4;
				}

				.filaDivs{
					float         : left;
					border-right  : 1px solid #D4D4D4;
					border-bottom  : 1px solid #D4D4D4;
					padding       :  5 0 5 3;
					overflow      : hidden;
					white-space   : nowrap;
					text-overflow : ellipsis;
				}

				.filaDivs img{
					cursor : pointer;
				}

				.divIcono{
					float            : left;
					width            : 20px;
					height           : 16px;
					padding          : 3 0 4 5;
					background-color : #F3F3F3;
					overflow         : hidden;
				}

				.divIcono>img{
					cursor : pointer;
					width  : 16px;
					height : 16px;
				}
			</style>

			<div style="width:100%;">
				<div class="titulos_ventana">AUTORIZACIONES REQUERIDAS</div>

				<div class="contenedor_tablas_cuentas">
					<div class="headDivs" style="width:90px;">DOCUMENTO</div>
					<div class="headDivs" style="width:200px;">USUARIO</div>
					<div class="headDivs" style="width:145px;">CARGO</div>
					<div class="headDivs" style="width:130px;border-right: none;">AUTORIZACION</div>

						<?php echo $bodyUsuarios; ?>

				</div>
				<div id="loadAut" style='display:none;'></div>
			</div>
			<script>
				<?php echo $script; ?>
			</script>
		<?php
 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function autorizarRequisicionCompra($id_documento,$opcGrillaContable,$id_sucursal,$idBodega,$id_empresa,$tipo_autorizacion,$id_area,$orden,$mysql){
		$id_empleado = $_SESSION['IDUSUARIO'];

 		// CONSULTAR LA INFORMACION DEL EMPLEADO
 		$sql="SELECT documento,nombre,id_cargo,cargo,email_empresa FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado ";
 		$query=$mysql->query($sql,$mysql->link);

		$documento_empleado = $mysql->result($query,0,'documento');
		$nombre_empleado    = $mysql->result($query,0,'nombre');
		$email              = $mysql->result($query,0,'email_empresa');
		$id_cargo           = $mysql->result($query,0,'id_cargo');
		$cargo              = $mysql->result($query,0,'cargo');

 		// CONSULTAR QUIEN AUTORIZA Y EL ORDEN EN QUE LO HACEN
 		$sql="SELECT * FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_area=$id_area ORDER BY orden ASC";
 		$query=$mysql->query($sql,$mysql->link);
 		while ($row=$mysql->fetch_array($query)) {
 			$orden_arr=$row['orden'];

 			$arrayAutorizadores[$orden_arr][$row['id_empleado']] = array(
																	'id_empleado'        => $row['id_empleado'],
																	'orden'              => $row['orden'],
																	'documento_empleado' => $row['documento_empleado'],
																	'nombre_empleado'    => $row['nombre_empleado'],
																	'id_cargo'           => $row['id_cargo'],
																	'cargo'              => $row['cargo'],
																	'email'              => $row['email'],
																	'id_area'            => $row['id_area'],
				 												);
 		}
 		// print_r($arrayAutorizadores);
 		// CONSULTAR SI EL DOCUMENTO YA TIENE UNA AUTORIZACION POR PARTE DEL USURAIO
 		$sql="SELECT id FROM autorizacion_requisicion
 				WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_requisicion=$id_documento AND id_area=$id_area AND orden=$orden";
 		$query=$mysql->query($sql,$mysql->link);
 		$id_row = $mysql->result($query,0,'id');

 		// SI ES UNA ACTUALIZACION A LA AUTORIZACION
 		if ($id_row>0) {
 			$sql="UPDATE autorizacion_requisicion SET tipo_autorizacion='$tipo_autorizacion'
 					WHERE activo=1 ANd id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_requisicion=$id_documento AND orden=$orden AND id_area=$id_area";
 			$query=$mysql->query($sql,$mysql->link);

 		}
 		// SI SE INSERTA LA NUEVA AUTORIZACION
 		else{
 			$sql="INSERT INTO autorizacion_requisicion (orden,id_empleado,documento_empleado,nombre_empleado,id_cargo,cargo,email,tipo_autorizacion,id_requisicion,id_area,fecha,hora,id_empresa)
 					VALUES($orden,'$id_empleado','$documento_empleado','$nombre_empleado','$id_cargo','$cargo','$email','$tipo_autorizacion','$id_documento','$id_area',NOW(),NOW(),'$id_empresa') ";
 			$query=$mysql->query($sql,$mysql->link);
 		}

 		// SI SE REALIZO LA AUTORIZACION, ENTONCES
 		if ($query) {

 			// SI SE AUTORIZO EL DOCUMENTO, ENTONCES ENVIAR EMAIL AL SIGUIENTE EMPLEADO ENCARGADO PARA QUE REALICE SU RESPECTIVA AUTORIZACION
 			if ($tipo_autorizacion=='Autorizada') {
 				$orden++;
 				$id_empleado_sigt = 0;
 				foreach ($arrayAutorizadores[$orden] as $id_empleado_orden => $datosEmp) {
 					$id_empleado_sigt = $id_empleado_orden;
 				}

 				// SI NO HAY MAS EMPLEADOS PARA AUTORIZAR, Y ESTA TODO AUTORIZADO, ENTONCES ACTUALIZAR EL DOCUMENTO COMO AUTORIZA
 				if ($id_empleado_sigt==0) {
					$sql   = "UPDATE compras_requisicion SET autorizado='true' WHERE id='$id_documento'";
					$query = $mysql->query($sql,$mysql->link);

					$Subject       = "Requisicion Autorizada";
					$mensaje_email = "La requisicion que solicito ha sido autorizada";
 					enviaEmailAutorizacion($id_documento,'solicitante',$id_empresa,$Subject,$mensaje_email,$mysql);
 				}
 				// SI AUN FALTA UN EMPLEADO EN AUTORIZAR, ENTONCES ENVIAR LA NOTIFICACION POR CORREO ELECTRONICO
 				else{
					$Subject       = "Requisicion Pendiente por Autorizacion";
					$mensaje_email = "Requisicion a la espera de su Autorizacion";
 					enviaEmailAutorizacion($id_documento,$id_empleado_sigt,$id_empresa,$Subject,$mensaje_email,$mysql);
 				}

 			}
 			// SI EL DOCUMENTO NO FUE AUTORIZADO ENTONCES ENVIAR UNA NOTIFICACION AL SOLICITANTE INDICANDO QUE LA REQUISICION NO FUE APROBADA
 			else if ($tipo_autorizacion=='Rechazada') {
 				$sql   = "UPDATE compras_requisicion SET autorizado='false' WHERE id='$id_documento'";
				$query = $mysql->query($sql,$mysql->link);

 				$Subject       = "Requisicion Rechazada";
				$mensaje_email = "La requisicion que solicito ha sido rechazada, comuniquese con el departamento de compras";
				enviaEmailAutorizacion($id_documento,'solicitante',$id_empresa,$Subject,$mensaje_email,$mysql);
 			}
 			// SI ESTA APLAZADA, NO AUTORIZADA, NO REALIZAR NINGUNA ACCION
 			else {
 				$sql   = "UPDATE compras_requisicion SET autorizado='false' WHERE id='$id_documento'";
				$query = $mysql->query($sql,$mysql->link);
 				echo '<script>MyLoading2("off")</script>';
 			}

 		}
 		else{
			echo '<script>MyLoading2("off",{icono:"fail",texto:"Error al autorizar intentelo de nuevo"})</script>';
 		}
 	}

 	//=========================== ENVIAR UN EMAIL CUANDO SE AUTORIZA O RECHAZA UNA REQUICISION  DE COMPRA ========================================== //
 	function enviaEmailAutorizacion($id_documento,$id_empleado,$id_empresa,$Subject,$mensaje_email,$mysql){
 		// EVNIAR EMAIL A LOS ENCARGADOS DE AUTORIZAR LAS REQUISICIONES
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		$mail  = new PHPMailer();
		// echo $mail;
		$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
		$queryConexion = $mysql->query ($sqlConexion,$mysql->link);
		if($row_consulta= $mysql->fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_smtp'];
			$pass          = $row_consulta['password'];
			$user          = $row_consulta['correo'];
			$puerto        = $row_consulta['puerto'];
			$servidor      = $row_consulta['servidor'];
			$from          = $row_consulta['correo'];
			$autenticacion = $row_consulta['autenticacion'];
		}

		if ($user=='') {
			echo '<script>
					alert("No exite ninguna configuracion de correo SMTP!\nConfigure el correo desde el panel de control en el boton configuracion SMTP\nPara que se puedan enviar las notificaciones a las personas encargadas de autorizar el documento");
				</script>';
		}

		//CONSULTAR LA INFORMACION DE LA EMPRESA
		$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,nit_completo,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular
						FROM empresas
						WHERE id='$id_empresa'
						LIMIT 0,1";
		$queryEmpresa = $mysql->query($sqlEmpresa,$mysql->link);

		$nombre_empresa        = $mysql->result($queryEmpresa,0,'nombre');
		$tipo_documento_nombre = $mysql->result($queryEmpresa,0,'tipo_documento_nombre');
		$documento_empresa     = $mysql->result($queryEmpresa,0,'nit_completo');
		$ciudad                = $mysql->result($queryEmpresa,0,'ciudad');
		$direccion_empresa     = $mysql->result($queryEmpresa,0,'direccion');
		$razon_social          = $mysql->result($queryEmpresa,0,'razon_social');
		$tipo_regimen          = $mysql->result($queryEmpresa,0,'tipo_regimen');
		$telefonos             = $mysql->result($queryEmpresa,0,'telefono').' - '.$mysql->result($queryEmpresa,0,'celular');
		$actividad_economica   = $mysql->result($queryEmpresa,0,'actividad_economica');

		// CONSULTAR LA INFORMACION DEL DOCUMENTO
		$sql="SELECT sucursal,
					bodega,
					fecha_inicio,
					consecutivo,
					codigo_centro_costo,
					centro_costo,
					documento_solicitante,
					nombre_solicitante,
					codigo_area_solicitante,
					area_solicitante,
					tipo_nombre,
					observacion,
					documento_usuario,
					usuario,
					id_solicitante
				FROM compras_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$mysql->link);

		$sucursal                = $mysql->result($query,0,'sucursal');
		$bodega                  = $mysql->result($query,0,'bodega');
		$consecutivo             = $mysql->result($query,0,'consecutivo');
		$fecha_inicio            = $mysql->result($query,0,'fecha_inicio');
		$codigo_centro_costo     = $mysql->result($query,0,'codigo_centro_costo');
		$centro_costo            = $mysql->result($query,0,'centro_costo');
		$documento_solicitante   = $mysql->result($query,0,'documento_solicitante');
		$nombre_solicitante      = $mysql->result($query,0,'nombre_solicitante');
		$codigo_area_solicitante = $mysql->result($query,0,'codigo_area_solicitante');
		$area_solicitante        = $mysql->result($query,0,'area_solicitante');
		$tipo_nombre             = $mysql->result($query,0,'tipo_nombre');
		$observacion             = $mysql->result($query,0,'observacion');
		$documento_usuario       = $mysql->result($query,0,'documento_usuario');
		$usuario                 = $mysql->result($query,0,'usuario');


		// SI SE DEBE ENVIAR LA NOTIFICACION A QUIEN SOLICITO LA REQUISICIONES
		if ($id_empleado=='solicitante') {
			$id_empleado = $mysql->result($query,0,'id_solicitante');
			// CONSULTAR SI ESTA CRUZADA EN UNA ORDEN DE COMPRA
			$sql="SELECT
						CO.consecutivo
					FROM
						compras_ordenes_inventario AS COI, compras_ordenes AS CO
					WHERE
						COI.activo = 1
					AND COI.id_consecutivo_referencia = $id_documento
					AND CO.id=COI.id_consecutivo_referencia
					AND CO.activo=1
					AND CO.consecutivo>0 ";
			$query=$mysql->query($sql,$mysql->link);
			$consecutivo_OC = $mysql->result($query,0,'consecutivo');
			$mensaje_email .= ($consecutivo_OC>0)? ", esta requisicion se encuentra en proceso en la orden de compra N. $consecutivo_OC" : "";
		}

		// CONSULTAR EL CUERPO DEL DOCUMENTO
		$sql="SELECT codigo,nombre,nombre_unidad_medida,cantidad_unidad_medida,cantidad,observaciones
				FROM compras_requisicion_inventario WHERE activo=1 AND id_requisicion_compra = $id_documento";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$bodyTable .="<tr>
							<td style='border:1px solid #D4D4D4;padding:3px;' >$row[codigo]</td>
							<td style='border:1px solid #D4D4D4;padding:3px;' >$row[nombre]</td>
							<td style='border:1px solid #D4D4D4;padding:3px;' >$row[nombre_unidad_medida] x $row[unidad_medida]</td>
							<td style='border:1px solid #D4D4D4;padding:3px;' >$row[cantidad]</td>
						</tr>
						<tr>
							<td style='border:1px solid #D4D4D4;padding:3px;font-size:10px;' colspan='4'><i>$row[observaciones]</i></td>
						</tr>";
		}

		// $id_empleado = ($id_empleado=='solicitante')? $mysql->result($query,0,'id_solicitante') : $id_empleado ;

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
		$mail->Subject    = "Notificacion: $Subject tipo: $tipo_nombre Consecutivo: $consecutivo ";
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap

		$body  = '
				<br>
				<b>'.$razon_social.'</b><br>
				<b>'.$tipo_regimen.'</b><br>
				<b>'.$tipo_documento_nombre.': </b>'.$documento_empresa.'<br>
				<b>Direccion: </b>'.$direccion_empresa.' - <b>'.$ciudad.' </b><br>
				<b>Telefono: </b>'.$telefonos.'<br>

				<br>
				<table>
					<tr>
						<td> <b>Asunto:</b></td>
						<td>'.$mensaje_email.'</td>
					</tr>
					<tr>
						<td> <b>Consecutivo:</b></td>
						<td style="font-size:24px;font-weight:bold;">'.$consecutivo.'</td>
					</tr>
					<tr>
						<td> <b>Bodega: </b></td>
						<td> '.$bodega.'</td>
					</tr>
					<tr>
						<td> <b>Sucursal: </b></td>
						<td>'.$sucursal.'</td>
					</tr>
					<tr>
						<td> <b>Persona Solicitante: </b></td>
						<td>'.$documento_solicitante.' - '.$nombre_solicitante.' </td>
					</tr>
					<tr>
						<td>Tipo</td>
						<td>'.$tipo_nombre.'</td>
					</tr>
					<tr>
						<td> <b>Area Solicitante:</b></td>
						<td>'.$area_solicitante.'</td>
					</tr>
					<tr>
						<td> <b>Usuario Creador:</b></td>
						<td>'.$documento_usuario.' - '.$usuario.' </td>
					</tr>
					<tr>
						<td> <b>Observaciones:</b></td>
						<td><i>'.$observacion.'</i> </td>
					</tr>
				</table>
				<br>
				<table style="border-collapse: collapse;width:100%;">
					<tr>
						<td style="font-weight:bold;border:1px solid #D4D4D4;padding:7px;background-color: #F3F3F3;">CODIGO</td>
						<td style="font-weight:bold;border:1px solid #D4D4D4;padding:7px;background-color: #F3F3F3;">ITEM</td>
						<td style="font-weight:bold;border:1px solid #D4D4D4;padding:7px;background-color: #F3F3F3;">UNIDAD MEDIDA</td>
						<td style="font-weight:bold;border:1px solid #D4D4D4;padding:7px;background-color: #F3F3F3;">CANTIDAD</td>
					</tr>
					'.$bodyTable.'

				</table>

				<br>
				<br>
				<br>
				Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.
			<br>';

		$mail->Body = $body;

		$mail->MsgHTML($body);

		// CONSULTAR LAS DIRECCIONES DE EMAIL DE LOS ENCARGADOS DE AUTORIZAR EL DOCUMENTO
		echo$sql="SELECT email_empresa FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		$query=$mysql->query($sql,$mysql->link);
		$email = $mysql->result($query,0,'email_empresa');
		if ($email<>''){
			$mail->AddAddress($email);
			$mail->IsHTML(true); // send as HTML
			if(!$mail->Send()){
				// echo $mail->ErrorInfo.'<script>alert("Se genero la autorizacion pero no se pudo enviar por email las notificaciones a los encargados de autorizar el documento\nSi el problema continua comuniquese con el administrador del sistema");</script>';

				echo '<script>MyLoading2("off",{icono:"fail",texto:"Se genero la autorizacion pero no se envio el email! verifique que todos tengan un email configurado"})</script>';

			}
			$mail->ClearAddresses();

			echo '<script>MyLoading2("off")</script>';
		}
		else{
			echo '<script>MyLoading2("off",{icono:"fail",texto:"El usuario no tiene el email configurado"})</script>';
		}

		// echo '<script>MyLoading2("off")</script>';
		// echo $mail;
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
							url     : "requisicion/grillaContableBloqueada.php",
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

	// FUNCION PARA RETROCEDER EL PROCESO DEL DOCUMENTO
	function rollback($id_documento,$tablaPrincipal,$mensaje,$id_empresa,$mysql){
		// CAMBIAR EL ESTADO DEL DOCUMENTO AL NUMERO 0
		$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=$mysql->query($sql,$mysql->link);


		echo '<script>
				alert("'.$mensaje.'");
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
		exit;
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT fecha_inicio,fecha_vencimiento FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		$fecha_inicio      = mysql_result($query,0,'fecha_inicio');
		$fecha_vencimiento = mysql_result($query,0,'fecha_vencimiento');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha_inicio)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha_inicio)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha_vencimiento)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha_vencimiento)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final";
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

	function deleteDocumentoRequisicion($id_host,$idDocumento,$nombre,$ext,$link){
		$nombreImage = md5($nombre).'_'.$idDocumento.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nombreImage;
		} else{
			$url = '';
		}

		// VALIDAR QUE QUIEN ELIMINA LA IMAGEN SEA EL MISMO USUARIO QUE LA CARGO, DE OTRO MODO NO PERMITIR QUE SE BORRE
		$sql="SELECT id_usuario FROM compras_requisicion_documentos WHERE activo=1 AND id=$idDocumento";
		$query=mysql_query($sql,$link);
		$id_usuario = mysql_result($query,0,'id_usuario');
		if ($id_usuario<>$_SESSION['IDUSUARIO']) {
			echo '<script>
						alert("El archivo solo puede ser eliminado por el usuario que lo adjunto!");
					</script>';
			exit;
		}

		$sqlDelete   = "UPDATE compras_requisicion_documentos SET activo = 0 WHERE id = $idDocumento";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){
			echo '<script>
							alert("No se puede eliminar el archivo, si el problema persiste favor comuniquese con el administrador del sistema");
						</script>';
			exit;
		} else{
			unlink($url);
			echo "<script>
							Elimina_Div_requisicionCompraDocumentos($idDocumento);
						</script>";
			exit;
		}
	}

	function downloadFile($nameFile,$ext,$id,$id_empresa,$id_host){
		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nombreImage)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nombreImage;
		}	else{
			$url = '';
		}

		if (file_exists($url)) {
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
		}	else{
			echo "Error, el archivo no se encuentra almacenado ";
		}
		exit;
	}

	function consultaSizeDocumento($nameFile,$ext,$id,$id_host){
		$nameFile = md5($nameFile).'_'.$id.'.'.$ext;

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nameFile)){
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nameFile;
		}	else{
			$url = '';
		}

		list($size['ancho'], $size['alto'], $tipo, $atributos) = getimagesize($url);
		echo json_encode($size);
	}

	function ventanaViewDocumento($nameFile,$ext,$id,$id_host){
		$nombreImage = md5($nameFile).'_'.$id.'.'.$ext;

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nombreImage)){
			$url = '../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/requisicion/'.$nombreImage;
		}	else{
			$url = '';
		}

		if($ext != 'pdf'){
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
		}	else{
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

?>
