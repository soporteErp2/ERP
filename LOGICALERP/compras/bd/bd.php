<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];



	switch ($opc) {
		case 'cargarCampoCotizacionPedido':
			cargarCampoCotizacionPedido($opcGrillaContable);
			break;

		case 'buscarCotizacionPedido':
			buscarCotizacionPedido($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar);
			break;

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
			guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$id_ccos,$id_impuesto,$link);
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
			terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link);
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
			restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link);
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
		case 'updateInvoceType':
			updateInvoceType($invoice_id,$type,$mysql);
			break;

	}

	//============================= FUNCION PARA MOSTRAR EL CAMPO DE CARGAR DESDE ===============================================================//
	function cargarCampoCotizacionPedido($opcGrillaContable){

		    $displayRequisicion = 'Requisicion';
		    $permisoRequisicion = 'true';

			$img        = 'cotizacion.png';
			$textoLabel = 'Haga Click para crear un documento a partir de una Orden de Compra';


			if ($opcGrillaContable=='EntradaAlmacen'){

				if(user_permisos(182,'false') == 'false'){
					$displayRequisicion = 'Orden Compra';
					$permisoRequisicion = 'false';
					$textoLabel         = '';

  					$img = 'pedido.png';
				}

				echo'<div style="width: 120px; display:table; margin-left:5px;" id="divContenedorCargarDesde'.$opcGrillaContable.'" title="'.$textoLabel.'" onclick="cambiarCargaFactura()">
						<div class="div_hover" id="imgFacturarDesde'.$opcGrillaContable.'"><img src="img/'.$img.'" id="imgCargarDesde'.$opcGrillaContable.'" width"20px" height="20px"/></div>
						<div class="div_hover" id="textoFacturardesde'.$opcGrillaContable.'"> <b>'.$displayRequisicion.'</b> </div>
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
					    var permisoRequisicion = '.$permisoRequisicion.';
						document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

						function cambiarCargaFactura(){
							if (document.getElementById("imgCargarDesde'.$opcGrillaContable.'").getAttribute("src")=="img/pedido.png" && permisoRequisicion == true) {



								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/cotizacion.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Requisicion</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para crear un documento a partir de una Orden de Compra");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

							}else{

								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/pedido.png");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
								document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

								document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b> Orden Compra</b>";
								document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para crear un documento a partir de una Requisicion");

								document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
							}
						}
				    </script>';
			}
			else if ($opcGrillaContable=='OrdenCompra'){

				echo'<div style="width: 120px; display:table; margin-left:5px;" id="divContenedorCargarDesde'.$opcGrillaContable.'">
						<div class="div_hover" id="imgFacturarDesde'.$opcGrillaContable.'"><img src="img/cotizacion.png" id="imgCargarDesde'.$opcGrillaContable.'" width"20px" height="20px"/></div>
						<div class="div_hover" id="textoFacturardesde'.$opcGrillaContable.'"> <b>Requisicion</b> </div>
					    <div class="div_hover" style="width:18px; height:18px; overflow:hidden; float:left;" id="renderCargaCotizacionPedido'.$opcGrillaContable.'"></div>
						<!-- <div class="div_hover"><img src="img/flecha_abajo.png"/></div> -->
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
					    var permisoRequisicion = '.$permisoRequisicion.';
						document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

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

	//=========================== GUARDAR VENDEDOR DE LA FACTURA ===============================================================================//
	function guardarVendedor($id, $tablaPrincipal,$documento,$nombre,$link){
		$sql   = "UPDATE $tablaPrincipal set documento_vendedor=$documento,nombre_vendedor='$nombre' WHERE id=$id ";
		$query = mysql_query($sql,$link);

		if ($query) { echo "true"; }
		else{ echo "false"; }
	}

	//========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================================//
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

	//========================== CARGAR LA GRILLA CON LOS ARTICULOS ============================================================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont,$opcGrillaContable){
		$readonly='';
		if(user_permisos(61,'false') == 'false'){ $readonly_precio='readonly'; }
		if(user_permisos(76,'false') == 'false'){ $readonly_descuento='readonly'; }

		//SI NO TIENE EL PERMISO PARA VER EL PRECIO EN ENTRADA DE ALMACEN OCULTA LOS CAMPOS

		$displayCampos='';
		$clase = 'campo';

		if($opcGrillaContable=='EntradaAlmacen'){

        	if(user_permisos(180,'false') == 'false'){
				$displayCampos='display:none';
				$clase = 'campoEntradaAlmacen';
			}

		}

		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
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
				<div class="'.$clase.'"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo campoDescuento" style="'.$displayCampos.'">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" '.$readonly_descuento.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo" style="'.$displayCampos.'"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo" style="'.$displayCampos.'"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

				<div style="float:right; min-width:80px;">
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

	//========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ================================//
	function ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link){
		$id_empresa  = $_SESSION['EMPRESA'];

		$selectObservacion = "SELECT observaciones,id_centro_costos,id_impuesto FROM $tablaInventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryObservacion = mysql_query($selectObservacion,$link);
		$observacion      = mysql_result($queryObservacion ,0,'observaciones');
		$id_centro_costos = mysql_result($queryObservacion ,0,'id_centro_costos');
		$id_impuesto      = mysql_result($queryObservacion ,0,'id_impuesto');

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
			echo '<script> Win_Ventana_descripcion_Articulo_factura.setHeight(240);</script>';
		}


		echo'<div style="margin: 10px">
				<div id="renderizaGuardarObservacion'.$opcGrillaContable.'_'.$cont.'"></div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:5px;'.$displayCampos.'">
					<div style="float:left; width:24%;">Impuesto</div>
					<div style="width:75%; float:left; height:23px;">
						<select id="id_impuestoItem_oc" style="width:99%;">'.$optionImpuesto.'</select>
					</div>
				</div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:5px;'.$displayCampos.'">
					<div style="float:left; width:24%;">Centro de Costo</div>
					<div style="width:75%; float:left; height:23px;">
						<input type="text" id="id_ccos_oc" value="'.$id_centro_costos.'" style="display:none;"/>
						<input type="text" id="codigo_ccos_oc" onclick="ventana_centros_costos_'.$opcGrillaContable.'()" value="'.$codigoCCos.'" style="width:29%; float:left; margin-right:1%;" class="myfield" readonly/>
						<input type="text" id="nombre_ccos_oc" onclick="ventana_centros_costos_'.$opcGrillaContable.'()" value="'.$nombreCCos.'" style="width:70%; float:left;" class="myfield" readonly/>
					</div>
				</div>
				<textarea id="observacionArticulo'.$opcGrillaContable.'_'.$cont.'" style="width:300px; height:150px;">'.$observacion.'</textarea>
			</div>';
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$id_ccos,$id_impuesto,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaInventario SET observaciones='$observacion',id_impuesto='$id_impuesto',id_centro_costos='$id_ccos' WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);
		if($queryUpdateObservacion){ echo '<script>Win_Ventana_descripcion_Articulo_factura.close(id);</script>'; }
		else{ echo '<script>alert("La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema");</script>'; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
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
				document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.$cantidad_articulo.'";
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
	function terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$link){
		global $id_empresa,$id_sucursal;
		if ($opcGrillaContable=='EntradaAlmacen') {

			$sql    = "UPDATE $tablaPrincipal SET estado='1',
													total_unidades=(
													 SELECT SUM(saldo_cantidad)
													 FROM compras_entrada_almacen_inventario
													 WHERE id_entrada_almacen= '$id') WHERE id='$id' ";
			$query  = mysql_query($sql,$link);
			if (!$query) {
				rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"No se actualizo el estado del documento!",$id_empresa,$link);
			}

			actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);

			moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'contabilizar',$link);

			if ($query) {
				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
							VALUES
							($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Entrada Almacen',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','CE')";
				$queryLog = mysql_query($sqlLog,$link);

				echo'<script>
						Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "bd/grillaContableBloqueada.php",
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

	//=========================== FUNCION DAR DE BAJA O RETORNAR LOS ARTICULOS VENDIDOS ==============================================================================//
	//ACTUALIZA LA CANTIDAD DE ARTICULOS DEL INVENTARIO O TOMA LOS COSTOS DE LOS MISMOS, ES DECIR LOS RESTA LOS ARTICULOS DEL DOCUMENTO AL INVENTARIO, O LOS AGREGA SEGUN SEA LA $opc
	function actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opc,$link){
		if ($opc=='agregar') {
			$sql   = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS cantidad_factura,
															SUM(cantidad*costo_unitario) AS costo_compra,
															id_inventario AS id_item
														FROM $tablaInventario
														WHERE $idTablaPrincipal='$id'
															AND activo=1
															AND inventariable='true'
														GROUP BY id_inventario) AS CFI
						SET IT.costos=((IT.cantidad * IT.costos)+(CFI.costo_compra))/(IT.cantidad+CFI.cantidad_factura),
							IT.cantidad=IT.cantidad+CFI.cantidad_factura
						WHERE IT.id_item=CFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$idBodega'";

			$query = mysql_query($sql,$link);

			if(!$query){
				rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"No se actualizo el inventario!",$id_empresa,$link);
				echo'<script>
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
		}
		else if ($opc=='eliminar') {
			$sql   = "UPDATE inventario_totales AS IT, (
														SELECT SUM(cantidad) AS cantidad_factura,
															SUM(cantidad*costo_unitario) AS costo_compra,
															id_inventario AS id_item
														FROM $tablaInventario
														WHERE $idTablaPrincipal='$id'
															AND activo=1
															AND inventariable='true'
														GROUP BY id_inventario) AS CFI
						SET IT.costos=IF(IT.cantidad-CFI.cantidad_factura = 0, 0, ((IT.cantidad * IT.costos) - (CFI.costo_compra))/(IT.cantidad-CFI.cantidad_factura)),
							IT.cantidad=IT.cantidad-CFI.cantidad_factura
						WHERE IT.id_item = CFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$idBodega'";

			$query = mysql_query($sql,$link);
			if(!$query ){
				echo'<script>
						alert("Aviso,\nNo se contabilizo los articulos al inventario");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
		}

	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$accion,$link){

		$id_empresa = $_SESSION['EMPRESA'];
		if ($accion=='contabilizar') {
			// CONTABILIZAR ENTRADA DE ALMACEN
			if ($opcGrillaContable=='EntradaAlmacen') {
				// CONSULTAR EL CONSECUTIVO DEL DOCUMENTO
				$sql="SELECT consecutivo,id_proveedor,fecha_inicio FROM compras_entrada_almacen WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento";
				$query=mysql_query($sql,$link);
				$consecutivo = mysql_result($query,0,'consecutivo');
				$id_tercero  = mysql_result($query,0,'id_proveedor');
				$fecha       = mysql_result($query,0,'fecha_inicio');

				if ($consecutivo==0 || $consecutivo=='') {
					rollback($idDocumento,$opcGrillaContable,'compras_entrada_almacen',$idTablaPrincipal,$tablaInventario,"No se Genero el consecutivo del documento!".$sql,$id_empresa,$link);
				}

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
				$sql   = "SELECT SUM(cantidad*costo_unitario) AS saldo FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal=$idDocumento ";
				$query = mysql_query($sql,$link);
				$saldo = mysql_result($query,0,'saldo');
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

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link){

		if ($opcGrillaContable=='EntradaAlmacen') {
			// REVERSAR LA CONTABILIDAD
			moverCuentasDocumento($idDocumento,$id_sucursal,0,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);
			// ACTUALIZAR EL INVENTARIO
			actualizaCantidadArticulos($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
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
	}

	//=========================== FUNCION PARA VALIDAR SI SE CRUZO EL DOCUMENTO ==============================================================//
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

	//=========================== FUNCION PARA RESTAR O AGREGAR EL SALDO CANTIDAD DE LOS DOCUMENTOS RELACIONADOS =============================//
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

	//=========================== FUNCION PARA BUSCAR EL VALOR DEL IVA DE UN ARTICULO ===========================================================//
	function buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link){

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
                    else{ document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none"; }

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
						VALUES( '$id',
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
		else{ echo "Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema ".$sqlInsert; }
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
		echo'<script>calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'",'.$cont.');</script>';
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
					calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantArticulo.',"'.$descuentoArticulo.'",'.$costoArticulo.',"agregar","'.$tipoDesc.'","'.$iva.'",'.$cont.');
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

		//IDENTIFICAMOS EL DOCUMENTO QUE SE VA A CANCELAR
		if ($opcGrillaContable=='CotizacionVenta') {
			$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Cotizacion de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','CE')";
		}
		else if ($opcGrillaContable=='PedidoVenta') {

			$sqlVerificar   ="SELECT estado FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryVerificar = mysql_query($sqlVerificar,$link);
			$estado         = mysql_result($queryVerificar,0,'estado');

			if ($estado==2){ echo '<script>alert("Error!\nNo se puede cancelar este pedido!\nPor que ya ha sido Remisionado o Facturado");</script>'; return; }
			else if ($estado==3) { echo '<script>alert("Error!\nEste Pedido ya esta Cancelado!");</script>'; return; }
			else{ $sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'"; }

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Pedido de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','CE')";
		}
		else if ($opcGrillaContable=='RemisionesVenta') {

			//////////////////////////////////////////////////////////////////////////////////////
			//		PARA CANCELAR UNA REMISION, SE DEBE TENER EN CUENTA:						//
			//																					//
			// 		* SI SE GENERO LA REMSION:												 	//
			//			-> SI SE CARGO DESDE UN PEDIDO, SE DEBE HABILITAR NUEVAMENTE EL PEDIDO	//
			//			-> DESCONTABILIZAR EL DOCUMENTO											//
			//			-> DEVOLVER LAS UNIDADES AL INVENTARIO									//
			//			-> CAMBIAR ESTADO A 3 'CANCELADA'										//
			//																					//
			//		* SI NO SE HA GENERO AUN:													//
			//			-> CAMBIAR ESTADO A 3 'CANCELADA'										//
			//																					//
			//////////////////////////////////////////////////////////////////////////////////////

			//SE VERIFICA PRIMERO QUE NO SE HALLAN REALIZADO NOTAS DE LA REMISION, SI SE HAN REALIZADO, ENTONCES NO SE PODRA EDITAR
			$sqlNota   = "SELECT COUNT(id) AS notas FROM devoluciones_venta WHERE documento_venta='Remision' AND id_documento_venta='$id' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega'";
			$queryNota = mysql_query($sqlNota,$link);
			$notas     = mysql_result($queryNota,0,'notas');
			if ($notas>0) { echo '<script>alert("Error!\nEsta remision tienen relacionadas '.$notas.' notas contables\nPor favor anule las notas para poder cancelar la remision");</script>'; exit; }

			//VERIFICAR SI SE CREO EL DOCUMENTO APARTIR DE UN PEDIDO, Y VERIFICAR SI SE GENERO O NO LA REMSION O SOLO SE HA CREADO PERO NO SE HA GENERADO
			$sqlVerificar = "SELECT consecutivo_carga,referencia_consecutivo_carga,estado
							FROM $tablaPrincipal
							WHERE id='$id'
								AND activo=1
								AND id_sucursal='$id_sucursal'
								AND id_bodega='$idBodega'
								AND id_empresa='$id_empresa'
							LIMIT 0,1";
			$queryVerificar               = mysql_query($sqlVerificar,$link);
			$estado                       = mysql_result($queryVerificar,0,'estado');
			$consecutivo_carga            = mysql_result($queryVerificar,0,'consecutivo_carga');
			$referencia_consecutivo_carga = mysql_result($queryVerificar,0,'referencia_consecutivo_carga');

			//SI SE GENERO LA REMISION
			if ($estado==1) {

				// SI SE CREA A PARTIR DE UN PEDIDO
				if ($consecutivo_carga>0 && $referencia_consecutivo_carga=='Pedido') {
					//ACTUALIZAMOS EL PEDIDO A ESTADO = 1 QUE ES CUANDO SE ENCUENTRA GENERADO Y LISTO PARA SER CARGADO
					$sqlUpdatePedido   = "UPDATE ventas_pedidos SET estado=1 WHERE activo=1 AND consecutivo='$consecutivo_carga' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
					$queryUpdatePedido = mysql_query($sqlUpdatePedido,$link);

					if (!$queryUpdatePedido) { echo '<script>alert("Error!\nEl pedido asignado a esta remision no se habilito!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
				}

				// PROCEDEMOS A LA DESCONTABILIZACION DE LA MISMA
				moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);

				//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
				/**********************************************************************************/
				actualizarSaldoCantidadDocumentoCargado($id,'agregar_documento_referencia',$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link);		// VALIDACION REMISION

				// DEVOLVEMOS LOS ARTICULOS AL INVENTARIO
				$resul=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);
				if ($resul>0) { echo 'Error!\nNo se devolvieron algunos articulos al inventario!\nSi el problema persiste comuniquese con el administrador del sistema'; }
			}
			else if ($estado==2) { echo '<script>alert("Error!\nNo se puede cancelar esta remision!\nPor que ya ha sido Facturada");</script>'; return; }
			else if ($estado==3) { echo '<script>alert("Error!\nEsta remision ya esta Cancelada!");</script>'; return; }

			//SINO ES POSIBLE QUE SE HALLA CREADO A PARTIR DE UNA COTIZACION O SIN CARGAR NINGUN DOCUMENTO, EN CUALQUIERA DE LOS DOS CASOS SOLO SE EJECUTA LA ACTUALIZACION DE LA REMISION, POR QUE LA COTIZACION SE BLOQUEA POR TIEMPO
			$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Remision de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','CE')";
		}
		else if ($opcGrillaContable=='FacturaVenta') {

			//VALIDACION SI TIENE NOTAS
			$sqlNota   = "SELECT COUNT(id) AS notas FROM devoluciones_venta WHERE documento_venta='Factura' AND id_documento_venta='$id' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega'";
			$queryNota = mysql_query($sqlNota,$link);
			$notas     = mysql_result($queryNota,0,'notas');

			if ($notas>0) { echo '<script>alert("Error!\nEsta remision tienen relacionadas '.$notas.' notas contables\nPor favor anule las notas para poder modificar la factura");</script>'; exit; }

			//VALIDACION RECIBO DE CAJA
			$sqlReciboCaja   = "SELECT COUNT(RCD.id) AS contRC
								FROM recibo_caja_documentos AS RCD,
									recibo_caja AS RC
								WHERE RCD.id_documento='$id' AND RCD.tipo_documento='FV' AND RCD.activo=1 AND RC.id=RCD.id_recibo_caja AND RC.estado=1
								GROUP BY RC.id";

			$queryReciboCaja = mysql_query($sqlReciboCaja,$link);
			$contRC          = mysql_result($queryReciboCaja,0,'contRC');

			if(!$queryReciboCaja){ echo '<script>alert("Error No. 3!\nNo se ha establecido la conexion con la base de datos.");</script>'; exit; }
			else if($contRC > 0){ echo '<script>alert("Error!\nEsta Factura tienen relacionado '.$contRC.' recibo(s) de caja.\nPor favor cancele los documentos relacionados para editar la factura");</script>'; exit; }

			//ESTADO DOCUMENTO
			$sqlVerificar   = "SELECT estado FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryVerificar = mysql_query($sqlVerificar,$link);
			$estado         = mysql_result($queryVerificar,0,'estado');

			if ($estado==1) {

				$res = actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);
				if($res > 0){ echo '<script>alert("Error!\nNo se actualizo el inventario\nSi el problema persite comuniquese con el administrador del sistema");</script>'; return; }

				moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);

				//ACTUALIZAR EL DOCUMENTO A ESTADO 3 CANCELADO
				$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";



				//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
				/**********************************************************************************/
				actualizarSaldoCantidadDocumentoCargado($id,'agregar_documento_referencia',$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link);		// VALIDACION REMISION
			}
			else if ($estado==3) { echo '<script>alert("Error!\nEsta factura ya esta Cancelada!");</script>'; return; }
			else if ($estado==0) { $sqlUpdate = "UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'"; }

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Factura de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','CE')";
		}

		$queryUpdate = mysql_query($sqlUpdate,$link);				//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		if (!$queryUpdate) { echo '<script>alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
		else{
			//INSERTAR EL LOG DE EVENTOS
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
					nueva'.$opcGrillaContable.'();
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
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	AL RESTAURAR TENER EN CUENTA:																			//
		//																											//
		//	-> SI FUE GENERADO, ENTONCES, SE ACTUALIZA CON ESTADO IGUAL A 1 PARA DEJAR EL DOCUMENTO COMO ESTABA 	//
		//	-> SI NO SE GENERO ANTES DE CANCELARLO ENTONCES SU ESTADO QUE DA IGUAL A 0 'NO GUARDADO'				//
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

 		if ($opcGrillaContable=='FacturaVenta') {
 			$sqlConsulDoc="SELECT numero_factura AS consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
 			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Factura de Venta',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','CE')";
 		}
 		else{
 			if ($opcGrillaContable=='CotizacionVenta') { 			//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Cotizacion de Venta',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','CE')";
 			}
 			elseif ($opcGrillaContable=='PedidoVenta') { 			//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Pedido de Venta',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','CE')";
 			}
 			elseif ($opcGrillaContable=='RemisionesVenta') { 		//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
							VALUES ($id,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Remision de Venta',$id_sucursal,".$_SESSION['EMPRESA'].",'".$_SERVER['REMOTE_ADDR']."','CE')";
 			}

 			$sqlConsulDoc="SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
 		}

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
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_venta  : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : "'.$idBodega.'"
						}
					});
				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
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

	// ACTUALIZAR EL TIPO DE LA FACTURA
	function updateInvoceType($invoice_id,$type,$mysql)
	{
		echo $sql   = "UPDATE compras_facturas SET id_tipo_factura=$type WHERE id=$invoice_id ";
		$query = $mysql->query($sql);
	}



?>