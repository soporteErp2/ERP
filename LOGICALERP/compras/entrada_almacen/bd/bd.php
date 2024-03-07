<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("functions_body_article.php");
	include("../../config_var_global.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		verificaCierre($id,$tablaPrincipal,$id_empresa,$link);

		if ($opc<>'restaurarDocumento' && $opc<>'modificarDocumentoGenerado' && $opc<>'cancelarDocumento') {
    		verificaEstadoDocumento($id,$opcGrillaContable,$link);
    	}

	}

	switch ($opc) {
		case 'cargarCampoCotizacionPedido':
			cargarCampoCotizacionPedido($opcGrillaContable);
			break;

		case 'buscarCotizacionPedido':
			buscarCotizacionPedido($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar);
			break;

		case 'guardarFechaOrden':
			guardarFechaOrden($idInputDate,$idDocumento,$valInputDate,$link);
			break;

		case 'actualizaTipoEntrada':
			actualizaTipoEntrada($tipo,$id,$id_empresa,$mysql);
			break;

		// case 'updateCcos':
		// 	updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id_factura,$id_empresa,$link);
		// 	break;

		case 'buscarCliente':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarCliente($id,$codCliente,$inputId,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

  		case 'guardarVendedor':
  			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
  	 		guardarVendedor($id, $tablaPrincipal,$documento,$nombre,$link);
  		break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaDescripcionArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($opcGrillaContable,$id_documento,$observaciones,$idRow,$mysql);
			break;

		case 'buscarArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($cont,$valorArticulo,$id_empresa,$opcGrillaContable,$whereBodega,$exentoIva,$link);
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
		 	retrocederArticulo($exento_iva,$id,$idArticulo,$opcionActivoFijo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
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
			terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$fecha_inicio,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link);
			break;

		case 'guardarArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($consecutivo,$checkOpcionContable,$id,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'actualizaArticulo':
			// verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$link,$checkOpcionContable);
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

		/*case 'agregarDocumento':
			// verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;*/

		/*case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_factura,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;*/

		case 'eliminaDocReferencia':
			// verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'updateSucursalCliente':
			updateSucursalCliente($id_factura,$id_scl_cliente,$nombre_scl_cliente,$opcGrillaContable,$id_empresa,$link);
			break;

        case 'updateCcos':
			updateCcos($idRow,$idCcos,$nombre,$codigo,$opc,$id_documento,$opcGrillaContable,$id_empresa,$mysql);
			break;

		case 'updateImpuestoItem':
			updateImpuestoItem($idRow,$id_impuesto,$opcGrillaContable,$id_documento,$mysql);
			break;

		case 'cargarNewDocumento':
			cargarNewDocumento($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar);
			break;

		case 'agregarDocumento':
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_documento,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
			break;

	}

	//====================================================== GUARDAR FECHA DE VENCIMIENTO =======================================================//

	function guardarFechaOrden($idInputDate,$idDocumento,$valInputDate,$link){
		if($idInputDate=='fechaEntradaAlmacen'){ $sqlUpdateFecha = "UPDATE compras_entrada_almacen SET  fecha_inicio='$valInputDate' WHERE id='$idDocumento'"; }
		else if($idInputDate=='fechaFinalEntradaAlmacen'){ $sqlUpdateFecha = "UPDATE compras_entrada_almacen SET  fecha_finalizacion='$valInputDate' WHERE id='$idDocumento'"; }

		$queryUpdateFecha = mysql_query($sqlUpdateFecha,$link);
		if($queryUpdateFecha){ echo 'true'; }
		else{ echo 'false'; }
	}

	function actualizaTipoEntrada($tipo,$id,$id_empresa,$mysql){
		$sql="UPDATE compras_entrada_almacen
				SET
					tipo_entrada ='$tipo',
					id_centro_costo     = CASE WHEN tipo_entrada ='EA' THEN '' ELSE id_centro_costo END,
					codigo_centro_costo = CASE WHEN tipo_entrada ='EA' THEN '' ELSE codigo_centro_costo END,
					centro_costo        = CASE WHEN tipo_entrada ='EA' THEN '' ELSE centro_costo END
				WHERE
					activo=1
					AND id_empresa=$id_empresa AND id=$id ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			// $script = ($tipo=='AI')? 'document.getElementById("div_content_ccos").style.display="block";' : 'document.getElementById("div_content_ccos").style.display="none";' ;
			echo '<script>alert("Error\nNo se actualizo el tipo de documnento, intentelo de nuevo");</script>';
		}
		else{
			$script = ($tipo=='AI')? 'document.getElementById("div_content_ccos").style.display="block";' : 'document.getElementById("div_content_ccos").style.display="none";
																											document.getElementById("cCos_EntradaAlmacen").value="";' ;
			echo '<script>'.$script.'</script>';
		}
	}

	// function updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id_factura,$id_empresa,$link){
	// 	$sql   = "UPDATE compras_entrada_almacen SET id_centro_costo='$idCcos' WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
	// 	$query = mysql_query($sql,$link);
	// 	if(!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; exit; }
	// 	echo'<script>
	// 			document.getElementById("cCos_'.$opcGrillaContable.'").value = "'.$codigo.' '.$nombre.'";
	// 			Win_Ventana_Ccos_'.$opcGrillaContable.'.close();
	// 		</script>';
	// }

	//======================================= FUNCION PARA MOSTRAR EL CAMPO DE CARGAR DESDE =====================================================//
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
						    <input placeholder="Numero..." class="myFieldGrilla" id="cotizacionPedido'.$opcGrillaContable.'" onKeyup="buscarDocumentoCruce'.$opcGrillaContable.'(event,this)" style="padding-left: 5px;"/>
						</div>
						<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarDocumentoCruce'.$opcGrillaContable.'();" style="margin-top:2px; margin-left:-23;">
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
						    <input placeholder="Numero..." class="myFieldGrilla" id="cotizacionPedido'.$opcGrillaContable.'" onKeyup="buscarDocumentoCruce'.$opcGrillaContable.'(event,this)" style="padding-left: 5px;" />
						</div>
						<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarDocumentoCruce'.$opcGrillaContable.'();" style="margin-top:2px; margin-left:-23;">
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

	//=========================== FUNCION PARA BUSCAR UN CLIENTE ================================================================================//
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

		$SQL   = "SELECT id,numero_identificacion,nombre,codigo AS cod_cliente,exento_iva,id_forma_pago FROM terceros WHERE $campo='$codCliente' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' AND tipo_proveedor='Si' LIMIT 0,1";
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

	//=========================== GUARDAR VENDEDOR DE LA FACTURA ================================================================================//
	function guardarVendedor($id, $tablaPrincipal,$documento,$nombre,$link){
		$sql   = "UPDATE $tablaPrincipal set documento_vendedor=$documento,nombre_vendedor='$nombre' WHERE id=$id ";
		$query = mysql_query($sql,$link);

		if ($query) { echo "true"; }
		else{ echo "false"; }
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ===============================================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont,$opcGrillaContable){

        //SI NO TIENE EL PERMISO PARA VER EL PRECIO EN ENTRADA DE ALMACEN OCULTA LOS CAMPOS

		$displayCampos ='';
		$clase         ='label';

		switch ($opcGrillaContable) {
			case 'CotizacionVenta':
				$typeDocument = 'COTIZACION';
				break;

			case 'PedidoVenta':
				$typeDocument = 'PEDIDO';
				break;

			case 'EntradaAlmacen':
				$typeDocument = 'ENTRADA DE ALMACEN';
				if(user_permisos(180,'false') == 'false'){
					$displayCampos ='display:none';
					$clase         ='labelEntradaAlmacen';
				}
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
							<div class="'.$clase.'">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="'.$clase.'">Unidad</div>
							<div class="'.$clase.'">Cantidad</div>
							<div class="label" style="'.$displayCampos.'">Descuento</div>
							<div class="label" style="'.$displayCampos.'">Precio</div>
							<div class="label" style="'.$displayCampos.'">Total</div>
							<div class="labelCheck" title="Activo Fijo" style="border-right: 1px solid #d4d4d4">A.F.</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulos'.$opcGrillaContable.'" onscroll="resizeHeadMyGrilla(this,\'head'.$opcGrillaContable.'\')">
						<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'">
							'.cargaDivsInsertUnidades('return',$cont,$opcGrillaContable).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_'.$opcGrillaContable.'">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacion'.$opcGrillaContable.'"><b>OBSERVACIONES</b></div>
						<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales" style="'.$displayCampos.'">
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

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS =============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable,$optionCheckContable){
		$readonly='';
		if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		//SI NO TIENE EL PERMISO PARA VER EL PRECIO EN ENTRADA DE ALMACEN OCULTA LOS CAMPOS

		$displayCampos  ='';
		$clase          = 'campo';
		$eventoCantidad = 'validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');';

		if($opcGrillaContable=='EntradaAlmacen'){

        	if(user_permisos(180,'false') == 'false'){
				$displayCampos  ='display:none';
				$clase          = 'campoEntradaAlmacen';
				$eventoCantidad = 'guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');';
			}

		}

		$body ='<div class="campo" style="width:40px !important; border-left:none; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="'.$clase.'">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="'.$clase.'"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="'.$clase.'"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'"  onKeyup="'.$eventoCantidad.'"/></div>

				<div class="campo campoDescuento" style="'.$displayCampos.'">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" '.$readonly_descuento.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo" style="'.$displayCampos.'"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo" style="'.$displayCampos.'"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

				<div class="campoOptionCheck" id="div_check_entrada_activo_fijo_'.$cont.'" style="width: 21px; border-right: 1px solid #d4d4d4;"></div>

				<div style="float:right; min-width:30px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/edit.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" />
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" >

				<script>document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL =================================//
	function ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link){
		$id_empresa  = $_SESSION['EMPRESA'];

		$selectObservacion = "SELECT observaciones,id_centro_costos,id_impuesto,check_opcion_contable FROM $tablaInventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryObservacion = mysql_query($selectObservacion,$link);
		$observacion      = mysql_result($queryObservacion ,0,'observaciones');
		$id_centro_costos = mysql_result($queryObservacion ,0,'id_centro_costos');
		$id_impuesto      = mysql_result($queryObservacion ,0,'id_impuesto');
		$checkOpcionContable = mysql_result($queryObservacion,0,'check_opcion_contable');

		$sqlCentroCostos   = "SELECT id, codigo, nombre FROM centro_costos WHERE id='$id_centro_costos' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryCentroCostos = mysql_query($sqlCentroCostos,$link);
		$codigoCCos = mysql_result($queryCentroCostos, 0, 'codigo');
		$nombreCCos = mysql_result($queryCentroCostos, 0, 'nombre');

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

		//SE OCULTAN LOS CENTROS DE COSTOS Y EL IVA

		$displayCampos='';

		if($opcGrillaContable=='EntradaAlmacen'){
			$displayCampos='display:none';
			// echo '<script> Win_Ventana_descripcion_Articulo_factura.setHeight(240);</script>';
		}

		?>
			<style>
				.titleWinObs{
					color       : #15428b;
					font-weight : bold;
					font-size   : 13px;
					font-family : tahoma,arial,verdana,sans-serif;
					text-align  : center;
					margin-top  : 10px;
					float       : left;
					width       : 100%;
				}

				.contentGrillaWinObs{
					float            : left;
					width            : 90%;
					background-color : #FFF;
					margin-top       : 10px;
					margin-left      : 20px;
					border           : 1px solid #D4D4D4;
				}

				.titleGrillaWinObs{
					float            : left;
					/*width            : 100px; width:calc(100% - 107px);*/
					background-color : #F3F3F3;
					padding          : 5px 0 5px 3px;
					border-right     : 1px solid #D4D4D4;
					font-weight      : bold;
					font-size        : 11px;
				}

				.titleGrillaWinObs:first-child, .bodyGrillaWinObs:first-child{
					width: 100px;
				}

				.titleGrillaWinObs:nth-child(2), .bodyGrillaWinObs:nth-child(2){
					width:calc(100% - 108px);
				}

				.bodyGrillaWinObs{
					float         : left;
					height        : 13px;
					/*width         : calc(100% - 110px);*/
					padding       : 5px 0 5px 3px;
					overflow      : hidden;
					white-space   : nowrap;
					text-overflow : ellipsis;
				}

			</style>
			<div class="titleWinObs">IMPUESTO</div>
			<div style="float:left;width:90%;background-color:#FFF;margin-top:10px;margin-left:20px;border:1px solid #D4D4D4;">
				<select id="id_impuestoItem_oc" style="width:99%;" onchange="updateImpuestoItem<?php echo	$opcGrillaContable; ?>(<?php echo $idArticulo ?>,this.value)"><?php echo $optionImpuesto ?></select>
			</div>
			<div class="titleWinObs">CENTRO DE COSTOS</div>
			<div class="contentGrillaWinObs">
				<div class="titleGrillaWinObs">CODIGO</div><div class="titleGrillaWinObs">CENTRO DE COSTOS</div>
				<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5; border-left: 1px solid #D4D4D4;" onclick="ventanaBuscarCentroCostos_<?php echo $opcGrillaContable; ?>('<?php echo $cont ?>')"><img src="img/buscar20.png" style="cursor:pointer;width:16px;height:16px;" title="Cambiar Cuenta"></div>
				<div id="codigoCcos_<?php echo	$opcGrillaContable; ?>" style="width: 100px;" class="bodyGrillaWinObs"> <?php echo $codigoCCos ?> </div>
				<div id="nombreCcos_<?php echo	$opcGrillaContable; ?>" style="width:calc(100% - 108px);" class="bodyGrillaWinObs"><?php echo $nombreCCos ?></div>
			</div>
			<div class="contentGrillaWinObs">
				<!--<textarea id="observacion'.$opcGrillaContable.'"  onKeydown="inputObservacion'.$opcGrillaContable.'(event,this)"></textarea>-->
				<div style="float:left;width:99%;background-color:#F3F3F3;padding: 5px 0 5px 3px;border-right:1px solid #D4D4D4;font-weight: bold;font-size: 11px;">OBSERVACION</div>
				<div style="float: left; margin-left: -30px; margin-top: 3px; width: 20px; padding: 0 0 0 5px; border-left: 1px solid #D4D4D4;" onclick="guardarObservacionArt<?php echo $opcGrillaContable; ?>(<?php echo $idArticulo ?>)"><img src="img/save_true.png" style="cursor:pointer;width:16px;height:16px;" title="Guardar Observacion"></div>
				<textarea id="observacionArt<?php echo	$opcGrillaContable; ?>" '.$txtAreaBloq.' ><?php echo $observacion; ?></textarea>
			</div>
			<div style='display:none;' id='loadWinObs'></div>
		<?php

	}

	// ======================== FUNCION PARA ACTUALIZAR EL CENTRO DE COSTOS DE CADA ITEM =======================================================//
	function updateCcos($idRow,$idCcos,$nombre,$codigo,$opc,$id_documento,$opcGrillaContable,$id_empresa,$mysql){

		$sql="UPDATE compras_entrada_almacen_inventario SET id_centro_costos='$idCcos', codigo_centro_costos='$codigo', centro_costos='$nombre'
				WHERE activo=1 AND id_entrada_almacen='$id_documento' AND id='$idRow' ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo "<script> MyLoading2('off',{icono:'fail',texto:'Se produjo un error intentelo de nuevo'}); </script> ";
		}
		else{
			echo "<script>
					MyLoading2('off');
					Win_Ventana_Ccos_$opcGrillaContable.close();
					document.getElementById('codigoCcos_$opcGrillaContable').innerHTML='$codigo';
            		document.getElementById('nombreCcos_$opcGrillaContable').innerHTML='$nombre';
				</script> ";
		}
	}

	// ========================= FUNCION PARA CAMBIAR EL IVA DE UN ARTICULO DE LA ENTRADA ======================================================//
	function updateImpuestoItem($idRow,$id_impuesto,$opcGrillaContable,$id_documento,$mysql){
		echo$sql="UPDATE compras_entrada_almacen_inventario SET id_impuesto='$id_impuesto' WHERE activo=1 AND id_entrada_almacen='$id_documento' AND id=$idRow ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo "<script> MyLoading2('off',{icono:'fail',texto:'Se produjo un error intentelo de nuevo'}); </script> ";
		}
		else{
			echo "<script>MyLoading2('off');</script> ";
		}
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($opcGrillaContable,$id_documento,$observaciones,$idRow,$mysql){
		$sql="UPDATE compras_entrada_almacen_inventario SET observaciones='$observaciones'
				WHERE activo=1 AND id_entrada_almacen='$id_documento' AND id='$idRow' ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo "<script> MyLoading2('off',{icono:'fail',texto:'Se produjo un error intentelo de nuevo'}); </script> ";
		}
		else{
			echo "<script>
					MyLoading2('off');
				</script> ";
		}

	}

	//============================= FUNCION PARA BUSCAR UN ARTICULO CON OPCION ACTIVO FIJO=======================================================//
	function buscarArticulo($cont,$valorArticulo,$id_empresa,$opcGrillaContable,$whereBodega,$exentoIva,$link){

		$sqlArticulo = "SELECT I.id,
							I.codigo,
							I.code_bar,
							IT.costos,
							I.nombre_equipo,
							I.numero_piezas,
							I.inventariable,
							IT.cantidad_minima_stock,
							I.unidad_medida,
							I.cantidad_unidades,
							I.id_impuesto,
							IT.cantidad,
							I.opcion_activo_fijo
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
		$costos                = mysql_result($query,0,'costos');
		$codigoBarras          = mysql_result($query,0,'code_bar');
		$nombre_unidad         = mysql_result($query,0,'unidad_medida');
		$numero_unidad         = mysql_result($query,0,'cantidad_unidades');
		$nombreArticulo        = mysql_result($query,0,'nombre_equipo');
		$id_impuesto           = mysql_result($query,0,'id_impuesto');
		$cantidad              = mysql_result($query,0,'cantidad');
		$cantidad_minima_stock = mysql_result($query,0,'cantidad_minima_stock');
		$inventariable         = mysql_result($query,0,'inventariable');
		$opcionActivoFijo      = mysql_result($query,0,'opcion_activo_fijo');


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
						document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       = "'.$nombre_unidad.' x '.$numero_unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$id.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$codigo.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "'.$costos.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$nombreArticulo.'";
						document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$id_impuesto.'";

						setTimeout(function(){ document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").focus(); },50);
						'.$script.'
					</script>';

				if($opcionActivoFijo == 'true'){
					echo'<script>
							document.getElementById("div_check_entrada_activo_fijo_'.$cont.'").innerHTML =\'<input type="checkbox" id="check_entrada_activo_fijo_'.$cont.'" class="optionCheckContable_'.$cont.'" onchange="changeCheckOptionContable'.$opcGrillaContable.'('.$cont.', this);"/>\';
						</script>';
				}

			}


			else if($inventariable=='true' && ($opcGrillaContable == 'RemisionesVenta' || $opcGrillaContable == 'FacturaVenta')){
				echo'<script>
						alert("El articulo se agoto en esta bodega!");
						setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus(); },100);
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "0";
						document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       = "";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value = "";
					</script>';
			}
			else{
				echo'<script>
						document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       = "'.$nombre_unidad.' x '.$numero_unidad.'";
						document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$id.'";
						document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$codigo.'";
						document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "'.$costos.'";
						document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$nombreArticulo.'";
						document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$id_impuesto.'";

						setTimeout(function(){ document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").focus(); },50);
						'.$script.'
					</script>';
			}
		}
		else{
			echo'<script>
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus(); },50);
					alert("El codigo '.$campo.' No se encuentra asignado en el inventario");
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     ="0";
					document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       ="";
					document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value  ="";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value ="";

					'.$script.'
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
	function retrocederArticulo($exento_iva,$id,$idArticulo,$opcionActivoFijo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$sqlArticulo = "SELECT
								      compras_entrada_almacen_inventario.id_inventario,
      								compras_entrada_almacen_inventario.codigo,
      								compras_entrada_almacen_inventario.costo_unitario,
      								compras_entrada_almacen_inventario.cantidad_unidad_medida,
      								compras_entrada_almacen_inventario.nombre_unidad_medida,
      								compras_entrada_almacen_inventario.nombre,
      								compras_entrada_almacen_inventario.cantidad,
      								compras_entrada_almacen_inventario.tipo_descuento,
      								compras_entrada_almacen_inventario.descuento,
      								compras_entrada_almacen_inventario.id_impuesto,
      								compras_entrada_almacen_inventario.check_opcion_contable,
      								compras_entrada_almacen_inventario.opcion_activo_fijo
      							FROM
      								$tablaInventario
                    LEFT JOIN
                      compras_entrada_almacen ON compras_entrada_almacen.id=compras_entrada_almacen_inventario.id_entrada_almacen
      							WHERE
      								compras_entrada_almacen_inventario.activo=1
                    AND
                      compras_entrada_almacen.id_empresa=$id_empresa
                    AND
                      compras_entrada_almacen_inventario.id=$idArticulo
                    AND
                      compras_entrada_almacen_inventario.id_entrada_almacen=$id
                    ORDER BY
                      compras_entrada_almacen_inventario.id
                    DESC
      							LIMIT
                      0,1;";

		$query = mysql_query($sqlArticulo,$link);

		$id_inventario           = mysql_result($query,0,'id_inventario');
		$codigo                  = mysql_result($query,0,'codigo');
		$costo_unitario          = mysql_result($query,0,'costo_unitario');
		$nombre_unidad_medida    = mysql_result($query,0,'nombre_unidad_medida');
		$cantidad_unidad_medida  = mysql_result($query,0,'cantidad_unidad_medida');
		$cantidad                = mysql_result($query,0,'cantidad');
    	$nombre                  = mysql_result($query,0,'nombre');
		$tipo_descuento          = mysql_result($query,0,'tipo_descuento');
		$descuento               = mysql_result($query,0,'descuento');
		$id_impuesto             = ($exento_iva=='Si')? 0 : mysql_result($query,0,'id_impuesto');
		$check_opcion_contable   = mysql_result($query,0,'check_opcion_contable');

		if($tipo_descuento == "porcentaje") {
			$imgDescuento    = "img/porcentaje.png";
			$tituloDescuento = "En porcentaje";
		}
		else{
			$imgDescuento    = "img/pesos.png";
			$tituloDescuento = "En pesos";
		}
		echo '<script>';
		if($check_opcion_contable == "activo_fijo"){
			echo 'document.getElementById("check_entrada_activo_fijo_'.$cont.'").checked =true;';
		}elseif($check_opcion_contable == ""){
			echo 'document.getElementById("check_entrada_activo_fijo_'.$cont.'").checked =false;';
		}

	  echo 'document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value               = "'.$id_inventario.'";
	        document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value              = "'.$codigo.'";
	        document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value            = "'.$costo_unitario.'";
	        document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value                 = "'.$nombre_unidad_medida.' x '.$cantidad_unidad_medida.'";
	        document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.($cantidad * 1).'";
	        document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value           = "'.$nombre.'";
	        document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'").value       = "'.($cantidad * $costo_unitario).'";
	        document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "'.$descuento.'";
	        document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value              = "'.$id_impuesto.'";
	        document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
	        document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
	        document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","'.$imgDescuento.'");
	        document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","'.$tituloDescuento.'");
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

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*********************************************************************************************************************************************/
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA FACTURA, COTIZACION, PEDIDO Y CARGAR UNA NUEVA =============================//
	//CIERRA Y/O BLOQUEA, Y MUEVE LAS CUENTAS DE LOS DOCUMENTOS
	function terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$fecha_inicio,$link){
		global $id_empresa,$id_sucursal;


			$titulo = 'Entrada de Almacen';

			$sql    = "UPDATE $tablaPrincipal
						SET estado='1',
							observacion='$observacion',
						    fecha_inicio='$fecha_inicio',
					  		total_unidades=(
					 			SELECT SUM(saldo_cantidad)
							 	FROM compras_entrada_almacen_inventario
							 	WHERE id_entrada_almacen= '$id')
						WHERE id='$id' ";
			$query  = mysql_query($sql,$link);

			$sqlAF = "SELECT
									id
								FROM
									compras_entrada_almacen_inventario
								WHERE
									id_entrada_almacen = $id
								AND
									activo = 1
								AND
									check_opcion_contable = 'activo_fijo'
								";
			$queryAF = mysql_query($sqlAF,$link);
			$resultAF = mysql_num_rows($queryAF);


			moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'contabilizar',$link);
			actualizarCantidadArticulos($id,'ingreso',"Generar");		//ACTUALIZAR LA CANTIDAD DE ARTICULOS

			// actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);
			actualizaCantidadActivosFijos($id,$id_sucursal,'agregar',$id_empresa,$link);


			if (!$query) {
				rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"No se actualizo el estado del documento!",$id_empresa,$link);
			}

			if ($query) {

				$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'";
		    	$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
									 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','EA','Entrada Almacen',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);

				/*PASAMOS LOS DOCUMENTOS QUE ESTAN CRUZADAS A ESTE DOCUMENTO A ESTADO 2 O DE BLOQUEO
			  	  PARA QUE NO SE PUEDAN EDITAR HASTA QUE TODAS LOS DOCUMENTOS DONDE ESTAN CARGADAS SEAN CANCELADOS O EDITADOS*/

				$sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
			                         FROM $tablaInventario
			                         WHERE id_consecutivo_referencia>0 AND $idTablaPrincipal='$id' AND activo=1
			                         ORDER BY id ASC";

	    		$queryDocReferencia = mysql_query($sqlDocReferencia,$link);

	    		while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

	    			/*	PROCESO PARA BLOQUEAR DOCUMENTOS CRUZADOS AL DOCUMENTO

		    			SI NO SE HABIA CRUZADO ANTES EL DOCUMENTO GUARDAMOS EN LA TABLA DE DOCUMENTOS
		    			CRUZADOS Los DOCUMENTOS QUE ESTAN CARGADAS EN EL DOCUMENTO
		    		*/

	    			//PRIMERO VERIFICAMOS QUE SE HAYA CARGADO ANTERIORMENTE A LA ORDEN DE COMPRA

		    		$tipoDoc = $rowDocReferencia['doc_referencia'];

		    		if($tipoDoc == 'R'){
						$tablaCruce     = 'compras_requisicion_doc_cruce';
						$id_doc_cruzado = 'id_requisicion';
						$tabla_update   = 'compras_requisicion';
		    		}
		    		else if($tipoDoc == 'O'){
		    			$tablaCruce     			= 'compras_ordenes_doc_cruce';
						$id_doc_cruzado 			= 'id_orden';
						$tabla_update   			= 'compras_ordenes';
						$tabla_update_inventario    = 'compras_ordenes_inventario';
		    		}

	    			$sqlCheck = "SELECT id FROM $tablaCruce WHERE $id_doc_cruzado = '$rowDocReferencia[id_referencia]' AND  id_documento_cruce = '$id' AND tipo_documento_cruce = 'EA'";

					$queryCheck = mysql_query($sqlCheck,$link);
					$rows       = mysql_num_rows($queryCheck);

					if($rows < 1){
						//NO ESTABA CRUZADO ANTERIORMENTE EL DOCUMENTO
						$sql = "INSERT INTO $tablaCruce ($id_doc_cruzado,id_documento_cruce,consecutivo_cruce,tipo_documento_cruce)
									  VALUES ('$rowDocReferencia[id_referencia]',$id,'$consecutivo','EA')";
						

					}
					else{
						$idCruce = mysql_result($queryCheck,0,'id');
						//SI YA HABIA ESTADO CRUZADA A ESTE DOCUMENTO
						$sql = "UPDATE $tablaCruce SET activo = 1 WHERE id='$idCruce'";
						
					}

					$query = mysql_query($sql,$link);

					if($query){
						//Update cantidades saldos OC
						if($tipoDoc == 'O'){
						echo $sqlUpdaterSaldoCantidades = "UPDATE $tabla_update_inventario AS IT,
							( SELECT id_tabla_inventario_referencia, id_inventario, saldo_cantidad 
								FROM compras_entrada_almacen_inventario 
								WHERE activo = 1 
								AND id_consecutivo_referencia = '$rowDocReferencia[id_referencia]'
								AND id_entrada_almacen = $id
							) AS CFI 
							SET IT.saldo_cantidad = IT.saldo_cantidad - CFI.saldo_cantidad 
							WHERE
								IT.id = CFI.id_tabla_inventario_referencia 
								AND IT.id_inventario = CFI.id_inventario 
								AND IT.activo = 1";
						$queryUpdaterSaldoCantidades = mysql_query($sqlUpdaterSaldoCantidades,$link);
						}
						else{
							
						}
	     				//UNA VEZ REALIZADO EL CRUCE SE BLOQUEA EL DOCUMENTO
						$sqlUpdateRequisiciones   = "UPDATE $tabla_update SET estado = 2 WHERE id='$rowDocReferencia[id_referencia]'";
						$queryUpdateRequisiciones = mysql_query($sqlUpdateRequisiciones,$link);

					}
					else{ echo'<script> alert("Ha ocurrido un error en el cruce de documentos,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema"); </script>'; }

	    		}


				echo'<script>
						Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "entrada_almacen/bd/grillaContableBloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega     : "'.$idBodega.'",
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_documento  : "'.$id.'"
							}
						});

						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			}
			else{
				echo'<script>
						alert("No se guardo la Entrada de Almacen,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>'.$sql;
			}


	}

	//=========================== FUNCION PARA INSERTAR EL TOTAL DE CANTIDADES DE ARTICULOS CON ACTIVOS FIJO  ===================================//
	function actualizaCantidadActivosFijos($id,$id_sucursal,$accion,$id_empresa,$link){
		$sqlSelect   = "SELECT consecutivo FROM compras_entrada_almacen WHERE id='$id'";
		$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

		if ($accion=='agregar') {
			// CONSULTAR LA CLASIFICACION DE LOS ITEMS
			$sql="SELECT
					id,
					id_familia,
					cod_familia,
					familia,
					id_grupo,
					cod_grupo,
					grupo,
					codigo,
					nombre
					FROM items_familia_grupo_subgrupo WHERE activo=1 AND id_empresa=$id_empresa";
		  	$query = mysql_query($sql,$link);
		  	while ($row=mysql_fetch_array($query)) {
		  		$id_subgrupo = $row['id'];
		  		$arrayClasificacion[$id_subgrupo] = array(
																'id_familia'  => $row['id_familia'],
																'cod_familia' => $row['cod_familia'],
																'familia'     => $row['familia'],
																'id_grupo'    => $row['id_grupo'],
																'cod_grupo'   => $row['cod_grupo'],
																'grupo'       => $row['grupo'],
																'codigo'      => $row['codigo'],
																'nombre'      => $row['nombre'],
		  													);
		  	}

			$sql = "SELECT
							EAI.codigo AS code_bar,
							EA.sucursal,
							EA.id_bodega,
							EA.bodega,
							EAI.nombre AS nombre_equipo,
							EA.id_proveedor,
							EA.nit AS nit_proveedor,
							EA.proveedor,
							EA.fecha_inicio AS fecha_compra,
							EAI.nombre_unidad_medida AS unidad,
							EAI.costo_unitario AS costo,
							EA.estado,
							EAI.id_inventario AS id_item,
							EA.consecutivo AS documento_referencia_consecutivo,
							EAI.id AS id_documento_referencia_inventario,
							EAI.cantidad,
							EAI.valor_impuesto,
							EAI.tipo_descuento,
							EAI.descuento,
							EAI.id_centro_costos,
							EAI.codigo_centro_costos,
							EAI.centro_costos,
							(SELECT id_subgrupo FROM items WHERE id=EAI.id_inventario) AS id_subgrupo

						FROM
							compras_entrada_almacen_inventario AS EAI
						LEFT JOIN
							compras_entrada_almacen AS EA ON EA.id = EAI.id_entrada_almacen
						WHERE
							EAI.id_entrada_almacen = $id
						AND
							EAI.activo = 1
						AND
							EAI.check_opcion_contable = 'activo_fijo'
												";
		  	$query = mysql_query($sql,$link);
		  	while ($row = mysql_fetch_array($query)) {
		  		$cantidad       = $row['cantidad'];
				$costo          = $row['costo'];
				$tipo_descuento = $row['tipo_descuento'];
				$descuento      = $row['descuento'];
				$valor_impuesto = $row['valor_impuesto'];
				$costoActivo    = $cantidad * $costo;

				if($descuento > 0){
					$costoActivo = ($tipo_descuento == 'porcentaje')? $costoActivo-ROUND(($descuento*$costoActivo)/100, $_SESSION['DECIMALESMONEDA']) : $costoActivo-$descuento;
				}

				$impuesto = ($costoActivo*$valor_impuesto)/100;
				$costoActivo = ($costoActivo+$impuesto)/$cantidad;

		  		// ARMAR EL STRING CON EL INSERT DEPENDIENDO LA CANTIDAD DE ACTIVOS A INGRESAR
		  		for ($i=0; $i < $cantidad; $i++) {
		  			$valueInsert .= "
									('$row[code_bar]',
								 	'$id_empresa',
								 	'$id_sucursal',
								 	'$row[id_bodega]',
								 	'$row[nombre_equipo]',
								 	'$row[id_proveedor]',
								 	'$row[nit_proveedor]',
								 	'$row[proveedor]',
								 	NOW(),
								 	'$row[fecha_compra]',
								 	'$row[unidad]',
								 	'$costoActivo',
								 	'$costoActivo',
								 	'$row[id_item]',
								 	'".$arrayClasificacion[$row['id_subgrupo']]['id_familia']."',
									'".$arrayClasificacion[$row['id_subgrupo']]['cod_familia']."',
									'".$arrayClasificacion[$row['id_subgrupo']]['familia']."',
									'".$arrayClasificacion[$row['id_subgrupo']]['id_grupo']."',
									'".$arrayClasificacion[$row['id_subgrupo']]['cod_grupo']."',
									'".$arrayClasificacion[$row['id_subgrupo']]['grupo']."',
									'$row[id_subgrupo]',
									'".$arrayClasificacion[$row['id_subgrupo']]['codigo']."',
									'".$arrayClasificacion[$row['id_subgrupo']]['nombre']."',
								 	'EA',
								 	'$id',
								 	'$row[documento_referencia_consecutivo]',
								 	'$row[id_documento_referencia_inventario]',
								 	'Si',
								 	'$row[id_centro_costos]'
									),";
		  		}

		  	}

			$valueInsert = substr($valueInsert, 0, -1);
		  	$sql = "INSERT INTO
								activos_fijos
								(
									code_bar,
									id_empresa,
									id_sucursal,
									id_bodega,
									nombre_equipo,
									id_proveedor,
									nit_proveedor,
									proveedor,
									fecha_creacion,
									fecha_compra,
									unidad,
									costo,
									costo_sin_depreciar_anual,
									id_item,
									id_familia,
									codigo_familia,
									familia,
									id_grupo,
									codigo_grupo,
									grupo,
									id_subgrupo,
									codigo_subgrupo,
									subgrupo,
									documento_referencia,
									id_documento_referencia,
									documento_referencia_consecutivo,
									id_documento_referencia_inventario,
									depreciable,
									id_centro_costos
								) VALUES $valueInsert";
		  	$query = mysql_query($sql,$link);
		  	// SI SE INSERTARON LOS ACTIVOS AL MODULO, ASIGNARLE LAS CUENTAS POR DEFECTO DE LOS GRUPOS
		  	if ($query) {
		  		$sql="SELECT id,id_grupo FROM activos_fijos
		  				WHERE activo=1
		  				AND id_empresa=$id_empresa
		  				AND id_sucursal=$id_sucursal
		  				AND documento_referencia = 'EA'
		  				AND id_documento_referencia = '$id'
		  				AND documento_referencia_consecutivo = '$consecutivo'
		  				";
	  			$query=mysql_query($sql,$link);
	  			while ($row=mysql_fetch_array($query)) {
	  				guardarActualizarCuentasActivoDefault($row['id'],$row['id_grupo'],$id_empresa,$link);
	  			}
		  	}
	  	}
	  	else if ($accion=='eliminar') {
	  		// CONSULTAR LOS ACTIVOS PARA ELIMINAR LAS CUENTAS RELACIONADAS
	  		$sql="SELECT id,id_grupo FROM activos_fijos
	  				WHERE activo=1
	  				AND id_empresa=$id_empresa
	  				AND id_sucursal=$id_sucursal
	  				AND documento_referencia = 'EA'
	  				AND id_documento_referencia = '$id'
	  				AND documento_referencia_consecutivo = '$consecutivo'
	  				";
  			$query=mysql_query($sql,$link);
  			while ($row=mysql_fetch_array($query)) {
  				$whereActivo .=($whereActivo=='')? " id_activo=$row[id] " : " OR id_activo=$row[id] ";
			}

			// ELIMINAR LAS CUENTAS INSERTADAS POR DEFECTO
			$sql="DELETE FROM activos_fijos_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND ($whereActivo) ";
			$query=mysql_query($sql,$link);

	  		$sql="UPDATE activos_fijos SET activo=0 WHERE activo=1 AND id_empresa=$id_empresa AND documento_referencia='EA' AND id_documento_referencia='$id' ";
	  		$query=mysql_query($sql,$link);
	  	}

	}

	// GUARDAR O ACTUALIZAR LAS CUENTAS DE UN ACTIVO CON LAS DEL GRUPO QUE RELACIONA
	function guardarActualizarCuentasActivoDefault($id_activo,$id_grupo,$id_empresa,$link){
		// CONSULTAR LAS CUENTAS POR DEFECTO DEL GRUPO PARA INSERTARLAS
		$sql="SELECT
					id_grupo,
					grupo,
					descripcion,
					SUBSTRING(descripcion, 19) AS tipo,
					estado,
					id_cuenta,
					cuenta,
					detalle_cuenta
				FROM asientos_colgaap_default_grupos
				WHERE
					activo=1
				AND id_empresa = $id_empresa
				AND id_grupo   = $id_grupo
				AND descripcion LIKE 'items_activo_fijo_%'
				";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$tipo = explode("_", $row['tipo']);

			$descripcion  = $tipo[0];
			$estado       = $row['estado'];

			// CREAR STRING PARA INSERTAR LAS CUENTAS INEXISTENTES
			$valueInsert .= "(
								'$id_activo',
								'$tipo[0]',
								'$row[estado]',
								'$row[id_cuenta]',
								'$row[cuenta]',
								'$row[detalle_cuenta]',
								'colgaap',
								'$id_empresa'
							),";

		}

		$sql="SELECT
					id_grupo,
					grupo,
					descripcion,
					SUBSTRING(descripcion, 19) AS tipo,
					estado,
					id_cuenta,
					cuenta,
					detalle_cuenta
				FROM asientos_niif_default_grupos
				WHERE
					activo=1
				AND id_empresa = $id_empresa
				AND id_grupo   = $id_grupo
				AND descripcion LIKE 'items_activo_fijo_%'
				";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$tipo = explode("_", $row['tipo']);

			$descripcion  = $tipo[0];
			$estado       = $row['estado'];

			// CREAR STRING PARA INSERTAR LAS CUENTAS INEXISTENTES
			$valueInsert .= "(
								'$id_activo',
								'$tipo[0]',
								'$row[estado]',
								'$row[id_cuenta]',
								'$row[cuenta]',
								'$row[detalle_cuenta]',
								'niif',
								'$id_empresa'
							),";
		}

		// INSERTAR LAS CUENTAS DEL ACTIVO
		if ($valueInsert<>'') {
			$valueInsert = substr($valueInsert,0,-1);
			$sql="INSERT INTO activos_fijos_cuentas
					(
						id_activo,
						descripcion,
						estado,
						id_cuenta,
						cuenta,
						descripcion_cuenta,
						contabilidad,
						id_empresa
					)
					VALUES $valueInsert";
			$query=mysql_query($sql,$link);
		}

	}
	// =========================== FUNCION PARA VALIDAR SI HAY ACTIVOS CON ESTADO 1 ANTES DE EDITAR O ELIMINAR UNA ENTRADA ======================= //
	function validaEstadoActivosFijos($id,$id_empresa,$link){
		$sql="SELECT COUNT(id) AS cont FROM activos_fijos WHERE activo=1 AND id_empresa=$id_empresa AND documento_referencia='EA' AND id_documento_referencia='$id' AND estado='1' ";
		$query=mysql_query($sql,$link);
		$cont = mysql_result($query,0,'cont');
		if ($cont>0) {
			echo '<script>
						alert("Error!\nNo es posible alterar el documento por que tiene '.$cont.' activos que estan ingresados y configurados, debe darlos de baja e intentar de nuevo");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			exit;
		}
	}

	//=========================== FUNCION PARA VALIDAR LA CANTIDAD DE ARTICULOS A DAR DE BAJA ===================================================//
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

	//============================= FUNCION DAR DE BAJA O RETORNAR LOS ARTICULOS VENDIDOS =======================================================//
	//ACTUALIZA LA CANTIDAD DE ARTICULOS DEL INVENTARIO O TOMA LOS COSTOS DE LOS MISMOS, ES DECIR LOS RESTA LOS ARTICULOS DEL DOCUMENTO AL INVENTARIO, O LOS AGREGA SEGUN SEA LA $opc
	function actualizarCantidadArticulos($id_documento,$accion_inventario,$accion_documento){

		global $mysql;
		// consultar la informacion del documento
		$sql = "SELECT id_sucursal,sucursal,id_bodega,bodega,id_empresa,consecutivo,fecha_inicio
				FROM compras_entrada_almacen WHERE id=$id_documento";
		$query = $mysql->query($sql);
		$id_empresa   = $mysql->result($query,0,"id_empresa");
		$id_sucursal  = $mysql->result($query,0,"id_sucursal");
		$sucursal     = $mysql->result($query,0,"sucursal");
		$id_bodega    = $mysql->result($query,0,"id_bodega");
		$bodega       = $mysql->result($query,0,"bodega");
		$consecutivo  = $mysql->result($query,0,"consecutivo");
		$fecha_inicio = $mysql->result($query,0,"fecha_inicio");

		// consultar los items de ese documento pero solo los que generan movimiento de inventario
		$sql = "SELECT 
						id_inventario AS id,
						codigo,
						nombre,
						nombre_unidad_medida AS unidad_medida,
						cantidad_unidad_medida AS cantidad_unidades,
						costo_unitario AS costo,
						cantidad
					FROM compras_entrada_almacen_inventario 
					WHERE id_entrada_almacen=$id_documento
					AND activo=1 
					AND inventariable='true'
					AND (check_opcion_contable='' OR check_opcion_contable IS NULL) ";
		$query = $mysql->query($sql);
		$index = 0;
		$items = array();
		while ($row = $mysql->fetch_assoc($query)) {
			$items[$index]                = $row;
			$items[$index]["empresa_id"]  = $id_empresa;
			$items[$index]["empresa"]     = NULL;
			$items[$index]["sucursal_id"] = $id_sucursal;
			$items[$index]["sucursal"]    = $sucursal;
			$items[$index]["bodega_id"]   = $id_bodega;
			$items[$index]["bodega"]      = $bodega;
			
			$index++;
		}
		// GENERAR EL MOVIMIENTO DE INVENTARIO
		include '../../../inventario/Clases/Inventory.php';

		$params = [ 
			"documento_id"          => $id_documento,
			"documento_tipo"        => "EA",
			"documento_consecutivo" => $consecutivo,
			"fecha"                 => $fecha_inicio,
			"accion_inventario"     => $accion_inventario,
			"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
			"items"                 => $items,
			"mysql"                 => $mysql
		];
		$obj = new Inventario_pp();
		$process = $obj->UpdateInventory($params);

	}

	//============================ FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON ======================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$accion,$link){

		$id_empresa = $_SESSION['EMPRESA'];
		if ($accion=='contabilizar') {
			// CONSULTAR EL CONSECUTIVO DEL DOCUMENTO
			$sql="SELECT consecutivo,id_proveedor,fecha_inicio,tipo_entrada,id_centro_costo FROM compras_entrada_almacen WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento";
			$query=mysql_query($sql,$link);
			$consecutivo        = mysql_result($query,0,'consecutivo');
			$id_tercero         = mysql_result($query,0,'id_proveedor');
			$fecha              = mysql_result($query,0,'fecha_inicio');
			$tipo_entrada       = mysql_result($query,0,'tipo_entrada');
			$id_centro_costo_EA = mysql_result($query,0,'id_centro_costo');

			if ($consecutivo==0 || $consecutivo=='') {
				rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"No se Genero el consecutivo del documento!".$sql,$id_empresa,$link);
			}

			// SI LA ENTRADA DE ALMACEN ES UN AJUSTE DE INVENTARIO
			if ($tipo_entrada=='AI') {
				include ('contabilizacion.php');
				return;
			}
			// SI ES UNA ENTRADA DE ALMACEN NORMAL
			else{

				// CONSULTAR LAS CUENTAS DE TRANSITO DEL PANEL DE CONTROL
				$sql="SELECT cuenta_colgaap_debito,cuenta_niif_debito,cuenta_colgaap_credito,cuenta_niif_credito FROM costo_cuentas_transito WHERE activo=1 AND id_empresa=$id_empresa ";
				$query=mysql_query($sql,$link);

				$cuenta_colgaap_debito  = mysql_result($query,0,'cuenta_colgaap_debito');
				$cuenta_niif_debito     = mysql_result($query,0,'cuenta_niif_debito');
				$cuenta_colgaap_credito = mysql_result($query,0,'cuenta_colgaap_credito');
				$cuenta_niif_credito    = mysql_result($query,0,'cuenta_niif_credito');

				if ($cuenta_colgaap_debito == '' || $cuenta_niif_debito == '' || $cuenta_colgaap_credito == '' || $cuenta_niif_credito == '' ||
					$cuenta_colgaap_debito == '0' || $cuenta_niif_debito == '0' || $cuenta_colgaap_credito =='0' || $cuenta_niif_credito =='0') {
					rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"No hay cuentas de transito configuradas en el panel de control!",$id_empresa,$link);
				}

				//CONSULTAR LOS ITEMS DE LA ENTRADA DE ALMACEN PARA GENERAR LOS ASIENTOS
				$sql   = "SELECT cantidad,costo_unitario,tipo_descuento,descuento FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal=$idDocumento ";
				$query = mysql_query($sql,$link);
				while ($row = mysql_fetch_array($query)) {
					$cantidad       = $row['cantidad'];
					$costo_unitario = $row['costo_unitario'];
					$tipo_descuento = $row['tipo_descuento'];
					$descuento      = $row['descuento'];
					$precio         = $cantidad*$costo_unitario;

					if ($descuento>0) {
						$precio = ($tipo_descuento == 'porcentaje')? $precio-ROUND(($descuento*$precio)/100, $_SESSION['DECIMALESMONEDA']) : $precio-$descuento;
					}

					$saldo += $precio;

				}

				if ($saldo<=0) {
					rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"El documento no tiene items!",$id_empresa,$link);
				}

				// INSERTAMOS LOS ASIENTOS CONTABLES
				$sql="INSERT INTO asientos_colgaap (id_documento,
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
						VALUES ('$idDocumento',
								'$consecutivo',
								'EA',
								'Entrada de Almacen',
								'$idDocumento',
								'EA',
								'$consecutivo',
								'$fecha',
								'$saldo',
								'0',
								'$cuenta_colgaap_debito',
								'$id_tercero',
								'$id_sucursal',
								'$id_empresa'),

								('$idDocumento',
								'$consecutivo',
								'EA',
								'Entrada de Almacen',
								'$idDocumento',
								'EA',
								'$consecutivo',
								'$fecha',
								'0',
								'$saldo',
								'$cuenta_colgaap_credito',
								'$id_tercero',
								'$id_sucursal',
								'$id_empresa')";
				$query=mysql_query($sql,$link);
				if (!$query) {
					rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"El documento no se contabilizo en colgaap!",$id_empresa,$link);
				}
				// INSERTAMOS LOS ASIENTOS CONTABLES
				$sql="INSERT INTO asientos_niif (id_documento,
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
						VALUES ('$idDocumento',
								'$consecutivo',
								'EA',
								'Entrada de Almacen',
								'$idDocumento',
								'EA',
								'$consecutivo',
								'$fecha',
								'$saldo',
								'0',
								'$cuenta_niif_debito',
								'$id_tercero',
								'$id_sucursal',
								'$id_empresa'),

								('$idDocumento',
								'$consecutivo',
								'EA',
								'Entrada de Almacen',
								'$idDocumento',
								'EA',
								'$consecutivo',
								'$fecha',
								'0',
								'$saldo',
								'$cuenta_niif_credito',
								'$id_tercero',
								'$id_sucursal',
								'$id_empresa')";
				$query=mysql_query($sql,$link);
				if (!$query) {
					rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"El documento no se contabilizo en Niif!",$id_empresa,$link);
				}

				// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
				contabilizacionSimultanea($idDocumento,'EA',$id_sucursal,$id_empresa,$link);
			}

		}
		else if ($accion=='descontabilizar') {
			// DESCONTABILIZAR ENTRADA DE ALMACEN
			if ($opcGrillaContable=='EntradaAlmacen') {
				// ELIMINAR LOS ASIENTOS COLGAAP
				$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND tipo_documento='EA' AND id_documento='$idDocumento' ";
				$query=mysql_query($sql,$link);
				// ELIMINAR LOS ASIENTOS NIIF
				$sql="DELETE FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND tipo_documento='EA' AND id_documento='$idDocumento' ";
				$query=mysql_query($sql,$link);
			}
		}

	}

	//============================================= FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===========================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link){

		// VALIDAR QUE NO TENGA ACTIVOS RELACIONADOS Y CONFIGURADOS
		validaEstadoActivosFijos($idDocumento,$id_empresa,$link);

		// REVERSAR LA CONTABILIDAD
		moverCuentasDocumento($idDocumento,$id_sucursal,0,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);
		// ACTUALIZAR EL INVENTARIO
		actualizarCantidadArticulos($idDocumento,'reversar ingreso',"Editar");

		// actualizaCantidadArticulos($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
		// ACTUALIZAR LOS ACTIVOS FIJOS
		actualizaCantidadActivosFijos($idDocumento,$id_sucursal,'eliminar',$id_empresa,$link);

		// ACTUALIZAR EL ESTADO DEL DOCUMENTO
		$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento";
		$query=mysql_query($sql,$link);
		if ($query) {
			
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					       VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','EA','Entrada de Almacen',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog=mysql_query($sqlLog,$link);

			/*=============================	PROCESO PARA LIBERAR DOCUMENTOS CRUZADAS =================================*/

			// CONSULTAMOS LAS REQUISICIONES QUE TIENE CARGADAS EL DOCUMENTO

			$sqlDocReferencia   = "SELECT DISTINCT
																			id_consecutivo_referencia AS id_referencia,
																			consecutivo_referencia AS cod_referencia,
																			LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
			                            	FROM
																			$tablaInventario
			                            	WHERE
																			id_consecutivo_referencia>0
																		AND
																			$idTablaPrincipal='$idDocumento'
																		AND
																			activo=1
			                            	ORDER BY id ASC";

    	$queryDocReferencia = mysql_query($sqlDocReferencia,$link);

		while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

				$tipoDoc = $rowDocReferencia['doc_referencia'];

    		if($tipoDoc == 'R'){
					$tablaCruce     = 'compras_requisicion_doc_cruce';
					$id_doc_cruzado = 'id_requisicion';
					$tabla_update   = 'compras_requisicion';
    		}
    		else if($tipoDoc == 'O'){
    				$tablaCruce			        = 'compras_ordenes_doc_cruce';
					$id_doc_cruzado 			= 'id_orden';
					$tabla_update   			= 'compras_ordenes';
					$tabla_update_inventario    = 'compras_ordenes_inventario';
					
					//Update cantidades pendientes en la OC
					$sqlUpdaterSaldoCantidades = "UPDATE $tabla_update_inventario AS IT,
												( SELECT id_tabla_inventario_referencia, id_inventario, saldo_cantidad 
													FROM compras_entrada_almacen_inventario 
													WHERE activo = 1 
													AND id_consecutivo_referencia = '$rowDocReferencia[id_referencia]'
													AND id_entrada_almacen = $idDocumento
												) AS CFI 
												SET IT.saldo_cantidad = IT.saldo_cantidad + CFI.saldo_cantidad 
												WHERE
													IT.id = CFI.id_tabla_inventario_referencia 
													AND IT.id_inventario = CFI.id_inventario 
													AND IT.activo = 1";

					$queryUpdaterSaldoCantidades = mysql_query($sqlUpdaterSaldoCantidades,$link);
    		}

	    	//PRIMERO PONEMOS EN ACTIVO 0 EL CRUCE CON EL DOCUMENTO ACTUAL

				$sqlCruce   = "UPDATE $tablaCruce SET activo = 0 WHERE $id_doc_cruzado = '$rowDocReferencia[id_referencia]' AND id_documento_cruce='$idDocumento' AND tipo_documento_cruce = 'EA'";
				$queryCruce = mysql_query($sqlCruce,$link);

				//VERIFICAMOS QUE LA REQUISICION NO ESTE CRUZADA A NINGUN OTRO DOCUMENTO, DE SER ASI LA LIBERAMOS PARA EDICION

				$sqlCheck = "SELECT id FROM $tablaCruce WHERE $id_doc_cruzado = '$rowDocReferencia[id_referencia]' AND activo = 1";

				$queryCheck = mysql_query($sqlCheck,$link);
				$rows       = mysql_num_rows($queryCheck);

	      if($rows < 1){
	       	//NO ESTA CRUZADA A NINGUN DOCUMENTO
					$sqlUpdate = "UPDATE $tabla_update SET estado=1 WHERE id='$rowDocReferencia[id_referencia]' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa='$id_empresa'";
					$query     = mysql_query($sqlUpdate,$link);
	      }

    	}

			echo '<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_documento  : "'.$idDocumento.'"
						}
					});
					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").innerHTML="";
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
		else{
			echo '<script>
					alert("Error!\nNo se actualizo el documento, intente de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}

	}

	//================================== FUNCION PARA VALIDAR SI SE CRUZO EL DOCUMENTO ==========================================================//
	function validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link){
		$tipo_documento_cruce = ($opcGrillaContable=='FacturaVenta')? 'FV' : 'RV' ;

		$sqlNota    = "SELECT consecutivo_documento, tipo_documento FROM asientos_colgaap WHERE id_documento_cruce = '$idDocumento' AND tipo_documento_cruce='$tipo_documento_cruce' AND tipo_documento<>'$tipo_documento_cruce' AND activo=1 AND id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';
		while ($row=mysql_fetch_array($queryNota)) {
			$doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento'];
		}
		if ($doc_cruces != '') { echo '<script>alert("Error!\nEste documento tiene relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nPor favor anule los documentos para poder modificarlo");</script>'; exit; }
	}

	//================================ FUNCION PARA RESTAR O AGREGAR EL SALDO CANTIDAD DE LOS DOCUMENTOS RELACIONADOS ===========================//
	function actualizarSaldoCantidadDocumentoCargado($idFactura,$accion,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link){
		//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
		/********************************************************************************************************************************/
		$arrayPedidos  = '';
		$contPedidos   = 0;
		$acumIdPedidos = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

		$arrayRemisiones  = '';
		$contRemisiones   = 0;
		$acumIdRemisiones = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

		$sqlDocumentoAdjunto = "SELECT id_consecutivo_referencia AS id_referencia, nombre_consecutivo_referencia AS nombre_referencia
								FROM $tablaInventario
								WHERE activo=1
									AND $idTablaPrincipal='$idFactura'
									AND id_consecutivo_referencia > 0
									AND nombre_consecutivo_referencia = 'Pedido'
								GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";
		$queryDocumentoAdjunto = mysql_query($sqlDocumentoAdjunto,$link);
		while($rowDoc = mysql_fetch_array($queryDocumentoAdjunto)){
			$contPedidos++;
			$arrayPedidos[$contPedidos] =  Array ('id_referencia' => $rowDoc['id_referencia']);
		}
		// echo $sqlDocumentoAdjunto;exit;

		$sqlDocumentoAdjunto = "SELECT id_consecutivo_referencia AS id_referencia, nombre_consecutivo_referencia AS nombre_referencia
								FROM $tablaInventario
								WHERE activo=1
									AND $idTablaPrincipal='$idFactura'
									AND id_consecutivo_referencia > 0
									AND nombre_consecutivo_referencia = 'Remision'
								GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";
		$queryDocumentoAdjunto = mysql_query($sqlDocumentoAdjunto,$link);
		while($rowDoc = mysql_fetch_array($queryDocumentoAdjunto)){
			$contRemisiones++;
			$arrayRemisiones[$contRemisiones] =  Array ('id_referencia' => $rowDoc['id_referencia']);
		}


		//SI HAY PEDIDO RELACIONADOS
		if ($contPedidos>0) {
			//AUMENTAR LA CANTIDAD
			$sql  = "UPDATE ventas_pedidos_inventario AS VRI
					INNER JOIN $tablaInventario AS VFI ON VRI.id=VFI.id_tabla_inventario_referencia
					SET VRI.saldo_cantidad = (VRI.saldo_cantidad + VFI.saldo_cantidad)
					WHERE VFI.$idTablaPrincipal='$idFactura'
						AND VRI.id_inventario = VFI.id_inventario
						AND VRI.activo  = 1
						AND VFI.activo = 1
						AND VFI.nombre_consecutivo_referencia='Pedido'";

			$query = mysql_query($sql,$link);
			if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron los saldos de los documentos Relacionados");</script>'; exit; }

			//UPDATE TOTAL ARTICULOS PENDIENTES FACTURAR EN REMISION
			for($cont=1; $cont<=$contPedidos; $cont++) {
				$id_pedido = $arrayPedidos[$cont]['id_referencia'];
				$sqlUpdatePendientes = "UPDATE ventas_pedidos
										SET unidades_pendientes=(
												SELECT SUM(saldo_cantidad)
												FROM ventas_pedidos_inventario
												WHERE id_pedido_venta= '$id_pedido'),
											estado=2
										WHERE id=$id_pedido";
				mysql_query($sqlUpdatePendientes,$link);
			}
		}

		//SI HAY REMISIONES RELACIONADAS RELACIONADOS
		if ($contRemisiones>0) {
			//AUMENTAR LA CANTIDAD
			$sql  = "UPDATE ventas_remisiones_inventario AS VRI
					INNER JOIN $tablaInventario AS VFI ON VRI.id=VFI.id_tabla_inventario_referencia
					SET VRI.saldo_cantidad = (VRI.saldo_cantidad + VFI.saldo_cantidad)
					WHERE VFI.$idTablaPrincipal='$idFactura'
						AND VRI.id_inventario = VFI.id_inventario
						AND VRI.activo  = 1
						AND VFI.activo = 1
						AND VFI.nombre_consecutivo_referencia='Remision'";

			$query = mysql_query($sql,$link);
			if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron los saldos de los documentos Relacionados");</script>'; exit; }

			//UPDATE TOTAL ARTICULOS PENDIENTES FACTURAR EN REMISION
			for($cont=1; $cont<=$contRemisiones; $cont++) {
				$id_remision = $arrayRemisiones[$cont]['id_referencia'];
				$sqlUpdatePendientes = "UPDATE ventas_remisiones
										SET pendientes_facturar=(
												SELECT SUM(saldo_cantidad)
												FROM ventas_remisiones_inventario
												WHERE id_remision_venta= '$id_remision'),
											estado=2
										WHERE id=$id_remision";
				mysql_query($sqlUpdatePendientes,$link);
			}
		}
	}

	//=============================== FUNCION PARA BUSCAR EL VALOR DEL IVA DE UN ARTICULO =======================================================//
	function buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link){

		$sql      = "SELECT impuestos.id,impuestos.impuesto,impuestos.valor FROM items, impuestos WHERE items.id='$id_inventario' AND items.id_impuesto = impuestos.id";
		$query    = mysql_query($sql,$link);
		$id       = mysql_result($query,0,'id');
		$impuesto = mysql_result($query,0,'impuesto');
		$valor    = mysql_result($query,0,'valor');

		$sqlAF      = "SELECT opcion_activo_fijo FROM items WHERE id=$id_inventario";
		$queryAF    = mysql_query($sqlAF,$link);
		$activoFijo = mysql_result($queryAF,0,'opcion_activo_fijo');

		if ($query) {
			echo '<script>
							if(document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value > 0){                   //mostrar la imagen deshacer y actualizar
                document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "inline";
                document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "inline";
              }else{
								document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";
							}
							document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       = "'.$unidadMedida.'";
              document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$idArticulo.'";
              document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$codigo.'";
              document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "'.$costo.'";
              document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$nombreArticulo.'";
              document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$id.'";
              Win_Ventana_buscar_Articulo_factura.close();
              if (typeof(arrayIva'.$opcGrillaContable.'["'.$id.'"])=="undefined") {
             		arrayIva'.$opcGrillaContable.'["'.$id.'"]={nombre:"'.$impuesto.'",valor:"'.$valor.'"};
              }
							if("'.$activoFijo.'" == "true"){
								document.getElementById("div_check_entrada_activo_fijo_'.$cont.'").innerHTML=\'<input type="checkbox" id="check_entrada_activo_fijo_'.$cont.'" class="optionCheckContable_'.$cont.'" onchange="changeCheckOptionContable'.$opcGrillaContable.'('.$cont.', this);"/>\';
							}
						</script>';
		}
		else{
			echo '<script>console.log("error al buscar iva");</script>';
		}

		// if (!$query) { echo 'false'; }
		// else { echo $valorImpuesto; }
	}

	//================================ FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ============================================================//
	function guardarArticulo($consecutivo,$checkOpcionContable,$id,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		if($exento_iva=='Si'){
			$iva=0;
		}

		// if ($opcGrillaContable=='RemisionesVenta' || $opcGrillaContable=='PedidoVenta') {

			$sqlInsert = "INSERT INTO
											$tablaInventario(
											  $idTablaPrincipal,
												id_inventario,
												cantidad,
												saldo_cantidad,
												tipo_descuento,
												descuento,
												costo_unitario,
												id_impuesto,
												check_opcion_contable)
										VALUES(
										 '$id',
										 '$idInventario',
										 '$cantArticulo',
										 '$cantArticulo',
										 '$tipoDesc',
					 					 '$descuentoArticulo',
				 						 '$costoArticulo',
			 							 '$iva',
		 								 '$checkOpcionContable')";
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

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ==================================================//
	function actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link,$checkOpcionContable){
		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo
		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura

		$sqlArticuloAnterior   = "SELECT cantidad,tipo_descuento,descuento,costo_unitario,id_impuesto AS valor_impuesto FROM $tablaInventario WHERE id='$idInsertArticulo' AND $idTablaPrincipal='$id' ";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);
		$cantidad              = mysql_result($queryArticuloAnterior,0,'cantidad');
		$tipo_descuento        = mysql_result($queryArticuloAnterior,0,'tipo_descuento');
		$descuento             = mysql_result($queryArticuloAnterior,0,'descuento');
		$costo_unitario        = mysql_result($queryArticuloAnterior,0,'costo_unitario');
		$valor_impuesto        = mysql_result($queryArticuloAnterior,0,'valor_impuesto');

		if ($exento_iva=='Si') {
			$valor_impuesto=0;
		}

		$sqlDeleteAF = "DELETE FROM
											activos_fijos
										WHERE
											id_documento_referencia_inventario = $idInsertArticulo
										AND
											documento_referencia = 'EA'
										AND
											id_empresa = ".$_SESSION['EMPRESA']."
										AND
											activo = 1";
		$queryDeleteAF = mysql_query($sqlDeleteAF,$link);

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		echo'<script>calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'",'.$cont.');</script>';
		// if ($opcGrillaContable=='RemisionesVenta') {
			$sqlUpdateArticulo   = "UPDATE $tablaInventario
									SET id_inventario='$idInventario',
										cantidad       ='$cantArticulo',
										saldo_cantidad ='$cantArticulo',
										tipo_descuento ='$tipoDesc',
										descuento      ='$descuentoArticulo',
										costo_unitario ='$costoArticulo',
										valor_impuesto = '$iva',
										check_opcion_contable = '$checkOpcionContable'
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
					calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantArticulo.',"'.$descuentoArticulo.'",'.$costoArticulo.',"agregar","'.$tipoDesc.'","'.$iva.'",'.$cont.');
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
	}

	//================================ FUNCION PARA GUARDAR LA OBSERVACION INDIVIDUAL POR ARTICULO ==============================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//=========================== FUNCION PARA VERIFICAR LA CANTIDAD EXISTENTE DEL ARTICULO  ====================================================//
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

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION =================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$link){
	  	// VALIDAR QUE NO TENGA ACTIVOS RELACIONADOS Y CONFIGURADOS
		validaEstadoActivosFijos($id,$id_empresa,$link);

		//IDENTIFICAMOS EL DOCUMENTO QUE SE VA A CANCELAR

		$sqlCheck    = "SELECT estado,consecutivo FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$queryCheck  = mysql_query($sqlCheck,$link);
		$estado      = mysql_result($queryCheck,0,'estado');
		$consecutivo = mysql_result($queryCheck,0,'consecutivo');

		if($consecutivo != NULL && $estado == 0){
			$sqlUpdate = "UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
			// echo "<script>console.log('aqui1');</script>";
		}else if($consecutivo == NULL && $estado == 0){
			$sqlDeleteCabecera = "DELETE FROM $tablaPrincipal WHERE id = '$id' AND activo = 1 AND id_sucursal = '$id_sucursal' AND id_bodega = '$idBodega' AND id_empresa = '$id_empresa'";
			//$queryDeleteCabecera = mysql_query($sqlDeleteCabecera,$link);
			// echo "<script>console.log('aqui2');</script>";
		}else if($estado == 1){
			// echo "<script>console.log('aqui3');</script>";
			$sqlUpdate = "UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
			// DESCONTBILIZAR
			moverCuentasDocumento($id,$id_sucursal,0,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);
			// SACAR DE INVENTARIO
			actualizarCantidadArticulos($id,'reversar ingreso',"Cancelar");

			// actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
			// SACAR DE ACTIVOS FIJOS
			actualizaCantidadActivosFijos($id,$id_sucursal,'eliminar',$id_empresa,$link);

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','CV','Cotizacion de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";

			$queryUpdate = mysql_query($sqlUpdate,$link);				//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
			if(!$queryUpdate){
				echo '<script>alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return;
			}
			else{
			/*=============================	PROCESO PARA LIBERAR DOCUMENTOS CRUZADAS =================================*/

			//CONSULTAMOS LAS REQUISICIONES QUE TIENE CARGADAS EL DOCUMENTO

			$sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
			                            FROM $tablaInventario
			                            WHERE id_consecutivo_referencia>0 AND $idTablaPrincipal='$id' AND activo=1
			                            ORDER BY id ASC";

	    	$queryDocReferencia = mysql_query($sqlDocReferencia,$link);

			while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

				$tipoDoc = $rowDocReferencia['doc_referencia'];

		  		if($tipoDoc == 'R'){
					$tablaCruce     = 'compras_requisicion_doc_cruce';
					$id_doc_cruzado = 'id_requisicion';
					$tabla_update   = 'compras_requisicion';
		  		}
		  		else if($tipoDoc == 'O'){
		  			$tablaCruce     = 'compras_ordenes_doc_cruce';
					$id_doc_cruzado = 'id_orden';
					$tabla_update   = 'compras_ordenes';
		  		}

		    	//PRIMERO PONEMOS EN ACTIVO 0 EL CRUCE CON LA ORDEN DE COMPRA ACTUAL
				$sqlCruce   = "UPDATE $tablaCruce SET activo = 0 WHERE $id_doc_cruzado = '$rowDocReferencia[id_referencia]' AND id_documento_cruce='$id' AND tipo_documento_cruce = 'EA'";
				$queryCruce = mysql_query($sqlCruce,$link);

				//VERIFICAMOS QUE LA REQUISICION NO ESTE CRUZADA A NINGUN OTRO DOCUMENTO, DE SER ASI LA LIBERAMOS PARA EDICION

				$sqlCheck = "SELECT id FROM $tablaCruce WHERE $id_doc_cruzado = '$rowDocReferencia[id_referencia]' AND activo = 1";

				$queryCheck = mysql_query($sqlCheck,$link);
				$rows       = mysql_num_rows($queryCheck);

		     	if($rows < 1){
		        //NO ESTA CRUZADA A NINGUN DOCUMENTO
						$sqlUpdate = "UPDATE $tabla_update SET estado=1 WHERE id='$rowDocReferencia[id_referencia]' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND activo=1 AND id_empresa='$id_empresa'";
						$query     = mysql_query($sqlUpdate,$link);
		      	}
	  		}

			//INSERTAR EL LOG DE EVENTOS
			$queryLog = mysql_query($sqlLog,$link);
			}
		}

		echo '<script>
						nueva'.$opcGrillaContable.'();
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						// Ext.get("contenedor_'.$opcGrillaContable.'").load({
						// 	url     : "grillaContableBloqueada.php",
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

 	//=================================== FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ========================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	AL RESTAURAR TENER EN CUENTA:																			//
		//																											//
		//	-> SI FUE GENERADO, ENTONCES, SE ACTUALIZA CON ESTADO IGUAL A 1 PARA DEJAR EL DOCUMENTO COMO ESTABA 	//
		//	-> SI NO SE GENERO ANTES DE CANCELARLO ENTONCES SU ESTADO QUE DA IGUAL A 0 'NO GUARDADO'				//
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if ($opcGrillaContable=='EntradaAlmacen') { 			//INSERTAR EL LOG DE EVENTOS
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					       VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','EA','Entrada de Almacen',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}

		$sqlConsulDoc="SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";


		//VERIFICAR SI FUE GENERADO ANTES DE CANCELAR
		$queryConsulDoc = mysql_query($sqlConsulDoc,$link);
		$consecutivo    = mysql_result($queryConsulDoc,0,'consecutivo');

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			//INSERTAR EL LOG DE EVENTOS
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
			        titulo="Entrada de Almacen";
			        document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "entrada_almacen/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_documento  : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : "'.$idBodega.'"
						}
					});
        			document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = titulo+"<br>N. "+"'.$consecutivo.'";
				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
 	}

	function eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

		$campoDocReferencia = '';

		if($docReferencia=='R'){ $campoDocReferencia = 'Requisicion'; }
		else if($docReferencia=='O'){ $campoDocReferencia = 'Orden de Compra'; }

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
	/*function verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$link){

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
	}*/

	// FUNCION PARA RETROCEDER EL PROCESO DEL DOCUMENTO
	function rollback($id_documento,$opcGrillaContable,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$mensaje,$id_empresa,$link){
		// CAMBIAR EL ESTADO DEL DOCUMENTO AL NUMERO 0
		$sql="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		// SI ES UNA ENTRADA DE ALMACEN SE DEBE REVERSAR EL INGRESO AL INVENTARIO Y LA CONTABILIDAD
		if ($opcGrillaContable=='EntradaAlmacen') {
			// DESHACER LOS MOVIMIENTOS CONTABLES
			$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$idTablaPrincipal AND tipo_documento='EA' ";
			$query=mysql_query($sql,$link);
			$sql="DELETE FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$idTablaPrincipal AND tipo_documento='EA' ";
			$query=mysql_query($sql,$link);

			// SACAR LAS UNIDADES DEL INVENTARIO
		}

		echo '<script>
				alert("'.$mensaje.'");
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
		exit;
	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$link){
		$sql="SELECT estado,id_bodega,consecutivo FROM compras_entrada_almacen WHERE id=$id_documento";
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

		if ($estado>0) {
			echo'<script>
					alert("'.$mensaje.'");
					if (document.getElementById("Win_Ventana_descripcion_Articulo_factura")) {
						Win_Ventana_descripcion_Articulo_factura.close();
					}
					if (document.getElementById("Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'.close();
					}

					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}

					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "entrada_almacen/bd/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : "'.$id_bodega.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_documento      : "'.$id_documento.'"
						}
					});

					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Remision de Venta<br>N. '.$consecutivo.'";
				</script>';
			exit;
		}
	}

	function agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$campoSelect = "";
		switch ($typeDoc) {
			case 'requisicion':
				$campoCantidad          = "cantidad";
				$title                  = 'Eliminar los Articulos de la Requisicion';
				$referencia_input       = "R";
				$referencia_consecutivo = "Requisicion";
				$tablaCarga             = "compras_requisicion";
				$idTablaCargar          = "id_requisicion_compra";
				$tablaCargaInventario   = "compras_requisicion_inventario";
				$docReferencia          = 'R'; //ESTA VARIABLE LLEGA AL condicional de eliminaDocReferencia
				$tablaBuscar            ="compras_requisicion";
				break;

			case 'orden_compra':
				$campoCantidad          = "saldo_cantidad";
				$title                  = 'Eliminar los Articulos de la Orden de Compra';
				$referencia_input       = "OC";
				$referencia_consecutivo = "Orden de Compra";
				$tablaCarga             = "compras_ordenes";
				$idTablaCargar          = "id_orden_compra";
				$tablaCargaInventario   = "compras_ordenes_inventario";
				$docReferencia          = 'O';
				$tablaBuscar ="compras_ordenes";
				break;
		}

		//VALIDACION ESTADO DE LA FACTURA
		$idProveedorDocAgregar  = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT estado,id,observacion,autorizado $campoSelect FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' ";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idDocumentoAgregar = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar   = mysql_result($queryValidateDocumento,0,'estado');
		$observacion        = mysql_result($queryValidateDocumento,0,'observacion');
		$autorizado         = mysql_result($queryValidateDocumento,0,'autorizado');

		if ($autorizado=='false') {
			echo '<script>alert("Aviso!\nEl documento no esta autorizado");</script>';
			return;
		}

		$sqlFactura   = "SELECT id_proveedor, estado FROM compras_entrada_almacen WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$queryFactura = mysql_query($sqlFactura,$link);

		$idProveedor  = mysql_result($queryFactura,0,'id_proveedor');
		$estado       = mysql_result($queryFactura,0,'estado');



		if($estado == 1){ echo '<script>alert("Error!,\nLa presente Entrada de Almacen ha sido generada.");</script>'; return; }
		if($estado == 3){ echo '<script>alert("Error!,\nLa presente Entrada de Almacen ha sido cancelada.");</script>'; return; }
		else if(($idProveedor == '' || $idProveedor == 0) && $typeDoc != 'requisicion'){

			cargarNewDocumento($codDocAgregar,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,'entrada_almacen/', $opcGrillaContable, '', $idTablaPrincipal, $tablaInventario, '',$typeDoc);
			return;
		}
		else if(($idProveedor == '' || $idProveedor == 0) && $typeDoc == 'requisicion'){
			echo '<script>
        	     	alert("Aviso!,\nPor favor asigne un Proveedor al documento para cargar una requisicion.");
        	     	if (document.getElementById("Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'.close();
					}
        		  </script>';
        	return;
		}

		$whereRemision = ($typeDoc == 'remision')? "AND CO.pendientes_facturar > 0 AND COI.saldo_cantidad > 0": "";



		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		// else if(($idProveedorDocAgregar <> $idProveedor) && $typeDoc != 'requisicion'){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenece a un proveedor diferente.");</script>'; return; }

		//VALIDACION QUE EL DOCUMENTO NO HAYA SIDO INGRESADO
		$sqlValidateRepetido = "SELECT COUNT(id) AS contDocRepetido
								FROM $tablaInventario
								WHERE activo=1 AND id_consecutivo_referencia='$idDocumentoAgregar'
									AND nombre_consecutivo_referencia='$referencia_consecutivo'
									AND id_entrada_almacen='$id_factura'
								GROUP BY id_tabla_inventario_referencia LIMIT 0,1";
		$docRepetido = mysql_result(mysql_query($sqlValidateRepetido,$link),0,'contDocRepetido');
		if($docRepetido > 0){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' ya ha sido agregado en la presente entrada de almacen");</script>'; return; }

		// SI ES UNA REQUISICION, VALIDAR QUE TENGA TODAS LAS AUTORIZACIONES
		if ($typeDoc=='requisicion') {
			$sql="SELECT id_empleado,documento_empleado,nombre_empleado,id_cargo,cargo FROM costo_autorizadores_requisicion WHERE activo=1 AND id_empresa=$id_empresa";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$arrayAutorizadoes[$row['id_empleado']] = array('documento_empleado' => $row['documento_empleado'],
																'nombre_empleado' => $row['nombre_empleado'],
																'cargo'           => $row['cargo'],
																'autorizado'      => ''
																);
			}

			$sql="SELECT id_empleado,documento_empleado,nombre_empleado,id_cargo,cargo,tipo_autorizacion FROM autorizacion_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id_requisicion=$idDocumentoAgregar";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$arrayAutorizadoes[$row['id_empleado']]['autorizado']= $row['tipo_autorizacion'];
			}

			$contEmpleados   = 0;
			$contAutorizados = 0;
			$contAplazados   = 0;
			$contRechazados  = 0;

			// RECORRER EL ARRAY PARA IDENTIFICAR LAS AUTORIZACIONES
			foreach ($arrayAutorizadoes as $id_empleado => $arrayResul) {
				$contEmpleados++;
				if ($arrayResul['autorizado']=='Autorizada') {
					$contAutorizados++;
				}
				else if ($arrayResul['autorizado']=='Aplazada') {
					$contAplazados++;
				}
				else if ($arrayResul['autorizado']=='Rechazada') {
					$contRechazados++;
				}

			}

			if ($contEmpleados<>$contAutorizados) {
				$mensaje.=($contAutorizados>0)? '\nAurotizados: '.$contAutorizados : '' ;
				$mensaje.=($contAplazados>0)? 	'\nAplazados: '.$contAplazados : '' ;
				$mensaje.=($contRechazados>0)?  '\nRechazados: '.$contRechazados : '' ;
				$mensaje.=(($contEmpleados-($contAutorizados+$contAplazados+$contRechazados))>0)?  '\nEsperando Autorizacion: '.($contEmpleados-($contAutorizados+$contAplazados+$contRechazados)) : '' ;

				echo '<script>alert("Aviso\nEl documento no esta autorizado totalmente '.$mensaje.'");</script>';
				exit;
			}

		}

		if($observacion <> ''){
			$sqlObservacion = "UPDATE compras_entrada_almacen
								SET observacion = IF(
											observacion<>'',
											CONCAT(observacion, '<br>', '$referencia_input ', '$codDocAgregar', ': ', '$observacion'),
											CONCAT('$referencia_input ', '$codDocAgregar', ': ', '$observacion')
										)
								WHERE id='$id_factura'
									AND id_empresa='$id_empresa'
									AND activo=1";
			$queryObservacion = mysql_query($sqlObservacion,$link);

			// ACTUALIZAMOS EL CONTENEDOR DE LA OBSERVACION

			$sqlSelect = "SELECT observacion FROM compras_entrada_almacen WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
            $querySelect = mysql_query($sqlSelect,$link);

            $observacion = mysql_result($querySelect,0,'observacion');

            $arrayReplaceString = array("\n","\r","<br>");
        	$observacion        = str_replace($arrayReplaceString,"\\n",$observacion);

            echo '<script>document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";</script>';


		}

		//GENERA CICLO PARA INSERTAR ARTICULOS DEL DOCUMENTO REFERENCIA A TABLA INVENTARIOS FACTURAS
		$sqlConsultaInventario= "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.$campoCantidad AS cantidad,COI.costo_unitario,
                                        COI.tipo_descuento,COI.descuento,
                                        COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                        CO.id AS id_documento,CO.consecutivo AS consecutivo_documento,COI.id_centro_costos
                                FROM $tablaCargaInventario AS COI
                                INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                WHERE CO.consecutivo     ='$codDocAgregar'
                                    AND COI.activo       = 1
                                    AND CO.id_sucursal   ='$id_sucursal'
                                    AND CO.id_bodega     ='$filtro_bodega'
                                    AND CO.id_empresa    ='$id_empresa'
                                    $whereRemision";
        $queryConsultaInventario=mysql_query($sqlConsultaInventario,$link);

        $contInsert=0;
        while ($row = mysql_fetch_array($queryConsultaInventario)) {
        	$contInsert++;
        	$idDocCruce = $row['id_documento'];
            $sqlInsertArticulos="INSERT INTO $tablaInventario
                                            ($idTablaPrincipal,
                                            id_inventario,
                                            cantidad,
                                            saldo_cantidad,
                                            costo_unitario,
                                            tipo_descuento,
                                            descuento,
                                            observaciones,
                                            id_tabla_inventario_referencia,
                                            id_consecutivo_referencia,
                                            consecutivo_referencia,
                                            nombre_consecutivo_referencia,
                                            id_centro_costos)
                                VALUES ('$id_factura',
                                        '".$row['id_inventario']."',
                                        '".$row['cantidad']."',
                                        '".$row['cantidad']."',
                                        '".$row['costo_unitario']."',
                                        '".$row['tipo_descuento']."',
                                        '".$row['descuento']."',
                                        '".$row['observaciones']."',
                                        '".$row['id']."',
                                        '".$row['id_documento']."',
                                        '".$row['consecutivo_documento']."',
                                        '$referencia_consecutivo',
                                        '".$row['id_centro_costos']."')";
            $queryInsertArticulos=mysql_query($sqlInsertArticulos,$link);
        }

        if($contInsert > 0){
    		$newDocReferencia  ='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;height: 22px;" id="divDocReferencia'.$opcGrillaContable.'_'.$docReferencia.'_'.$idDocumentoAgregar.'">'
							       .'<div class="contenedorInputDocReferenciaFactura">'
							           .'<input type="text" class="inputDocReferenciaEntradaAlmacen" value="'.$referencia_input.' '.$codDocAgregar.'" readonly style="border-bottom: 1px solid #d4d4d4;"/>'
							       .'</div>'
							       .'<div title="'.$title.' # '.$codDocAgregar.' en la presente entrada de almacen" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\\\''.$idDocumentoAgregar.'\\\',\\\''.$docReferencia.'\\\',\\\''.$id_factura.'\\\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">'
							           .'<div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$docReferencia.'_'.$idDocCruce.'">'
	                                        .'<div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>'
	                                    .'</div>'
							       .'</div>'
							    .'</div>';

			echo'<script>

					divDocsReferenciaFactura = document.getElementById("contenedorDocsReferenciaEntradaAlmacen").innerHTML;
					document.getElementById("contenedorDocsReferenciaEntradaAlmacen").innerHTML =divDocsReferenciaFactura+\''.$newDocReferencia.'\';
	    			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").value="";

	    			Ext.get("renderizaNewArticulo'.$opcGrillaContable.'").load({
			            url     : "entrada_almacen/bd/bd.php",
			            scripts : true,
			            nocache : true,
			            params  :
			            {
							opc               : "reloadBodyAgregarDocumento",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_documento      : "'.$id_factura.'",
			            }
			        });

					actualiza_fila_ventana_busqueda_doc_cruce('.$idDocumentoAgregar.');
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

	// AGREGAR EL DOCUMENTO
	function cargarNewDocumento($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar){
		//SI SE VA A CARGAR UNA COTIZACION VALIDAR QUE NO ESTE VENCIDA
		if ($opcCargar=='requisicion') {
			$sql="SELECT COUNT(id_proveedor) AS cont, id_proveedor, nit, proveedor
					FROM compras_requisicion
					WHERE consecutivo='$id'
						AND activo = 1
						AND (estado = 1 OR estado=2)
						AND id_sucursal= '$id_sucursal'
						AND id_bodega= '$filtro_bodega'
						AND id_empresa='$id_empresa'";
			$query = mysql_query($sql,$link);

			$mensaje = ' <script>
			       			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
			       			alert("Aviso!\nLa Requisicion ya expiro o no existe.");
			       			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus(); },80);
						</script>';
		}
		else{
			$sql="SELECT COUNT(id_proveedor) AS cont, id_proveedor, nit, proveedor
					FROM compras_ordenes
					WHERE consecutivo='$id'
						AND activo = 1
						AND (estado = 1 OR estado=2)
						AND id_sucursal= '$id_sucursal'
						AND id_bodega= '$filtro_bodega'
						AND id_empresa='$id_empresa'";
			$query = mysql_query($sql,$link);

			$mensaje = ' <script>
			       			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
			       			alert("Aviso!\nLa Orden de Compra ya expiro\nO no existe.");
			       			setTimeout(function(){ document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus(); },80);
						</script>';
		}

        $resu = mysql_result($query,0,'cont');

        if ($resu>0) {

        	echo '<script>
        			Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						text : "Cargando Documento...",
						scripts : true,
						nocache : true,
						params  :
						{
							opcCargar                     : "'.$opcCargar.'",
							opcGrillaContable             : "'.$opcGrillaContable.'",
							filtro_bodega                 : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
							idConsecutivoCotizacionPedido : '.$id.',
						}
					});

					if (document.getElementById("Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'.close();
					}

        		</script>';
        }
        else{ echo $mensaje; }
	}

	function reloadBodyAgregarDocumento($opcGrillaContable,$id_documento,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		echo cargaArticulosSave($id_documento,'',0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
	    echo '<script>
	                if (document.getElementById("Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_documento_cruce'.$opcGrillaContable.'.close();
					}
        	  </script>';
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT fecha_inicio,fecha_finalizacion FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		$fecha_inicio      = mysql_result($query,0,'fecha_inicio');
		$fecha_finalizacion = mysql_result($query,0,'fecha_finalizacion');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha_inicio)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha_inicio)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha_finalizacion)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha_finalizacion)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND'$fecha_documento' BETWEEN fecha_inicio AND fecha_final ";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ((fecha_nota>='$fecha_inicio_buscar_1' AND fecha_nota<='$fecha_fin_buscar_1') OR (fecha_nota>='$fecha_inicio_buscar_2' AND fecha_nota<='$fecha_fin_buscar_2')  ) ";
		$query=mysql_query($sql,$link);
		$cont2 = mysql_result($query,0,'cont');

		if ($cont1>0 || $cont2) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}

	}


?>
