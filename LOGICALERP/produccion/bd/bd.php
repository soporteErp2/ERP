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
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarCliente($id,$codCliente,$inputId,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

    	case 'guardarVendedor':
    		verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
    	 	guardarVendedor($id, $tablaPrincipal,$documento,$nombre,$link);
    		break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaDescripcionArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'buscarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			buscarArticulo($campo,$valorArticulo,$idArticulo,$id_empresa,$idCliente,$opcGrillaContable,$whereBodega,$exentoIva,$link);
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
		 	retrocederArticulo($exento_iva,$id,$idArticulo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'checkboxRetenciones':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			checkboxRetenciones($id,$idRetencion,$accion,$opcGrillaContable,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

		case 'UpdateFormaPago':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			UpdateFormaPago($id,$idFormaPago,$tablaPrincipal,$opcGrillaContable,$link,$fechaVencimiento);
			break;

		case 'terminarGenerar':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			terminarGenerar($id,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$tablaRetenciones,$opcGrillaContable,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($id_inventario,$cont,$opcGrillaContable,$unidadMedida,$idArticulo,$codigo,$costo,$nombreArticulo,$link);
			break;

		case 'guardarArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'actualizaArticulo':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
			actualizaArticulo($id,$idInsertArticulo,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaPrincipal, $tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'guardarObservacion':
			verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
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

		case 'updateSucursalCliente':
			updateSucursalCliente($id_factura,$id_scl_cliente,$nombre_scl_cliente,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'eliminarFacturaWs':
			eliminarFacturaWs($id_factura,$id_empresa,$link);
			break;

		case 'detalleDashboard':
			detalleDashboard($bodega,$sucursal,$table,$tabOpcion);
			break;

	}

	//============================= FUNCION PARA MOSTRAR EL CAMPO DE CARGAR DESDE ===============================================================//
	function cargarCampoCotizacionPedido($opcGrillaContable){

		if ($opcGrillaContable=='PedidoVenta') {
			echo'
				<div style="float:left; margin: 12px 0 0 5px">
				<div style="width:18px; height:18px; overflow:hidden; float:left;margin-right: -20;position: absolute;" id="renderCargaCotizacionPedido'.$opcGrillaContable.'"></div>

				    <div style="float:left; width:120px; height:22px;">
					    <input placeholder="Numero..." class="myFieldGrilla" id="cotizacionPedido'.$opcGrillaContable.'" onKeyup="buscarCotizacionPedido'.$opcGrillaContable.'(event,this)" style="padding-left: 5px;"/>
					</div>
					<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarCotizacionPedido'.$opcGrillaContable.'();" style="margin-top:2px; margin-left:-23;">
					   <img src="img/buscar20.png"/>
					</div>
					<div title="Agregar Documento" onclick="agregarDocumento'.$opcGrillaContable.'(\'\');" class="btnCargarAgregarDocumento">
					   <img src="img/add16.png"/>
					</div>
				</div>';
		}
		else if ($opcGrillaContable=='RemisionesVenta'){

			echo'<div style="width: 120px; display:table; margin-left:5px;" id="divContenedorCargarDesde'.$opcGrillaContable.'" title="Haga Click para cambiar a facturar desde un pedido" onclick="cambiarCargaFactura()">
					<div class="div_hover" id="imgFacturarDesde'.$opcGrillaContable.'"><img src="img/cotizacion.png" id="imgCargarDesde'.$opcGrillaContable.'" width"20px" height="20px"/></div>
					<div class="div_hover" id="textoFacturardesde'.$opcGrillaContable.'"> <b>Cotizacion</b> </div>
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
					<div title="Agregar Documento" onclick="agregarDocumento'.$opcGrillaContable.'(\'\');" class="btnCargarAgregarDocumento">
					   <img src="img/add16.png"/>
					</div>
				</div>
				<script>
					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

					function cambiarCargaFactura(){
						if (document.getElementById("imgCargarDesde'.$opcGrillaContable.'").getAttribute("src")=="img/pedido.png") {

							document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/cotizacion.png");
							document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
							document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

							document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Cotizacion</b>";
							document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Haga Click para cambiar a facturar desde un Pedido");

							document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

						}else{

							document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/pedido.png");
							document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
							document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");

							document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Pedido</b>";
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

					if (document.getElementById("Win_Ventana_buscar_cotizacionPedido'.$opcGrillaContable.'")) {
						Win_Ventana_buscar_cotizacionPedido'.$opcGrillaContable.'.close();
					}

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

		// CHECKBOX RETENCIONES CHECKED SI opcGrillaContable ES IGUAL A FacturaVenta
		if ($opcGrillaContable=='FacturaVenta') {

			$arrayRetenciones    = '';
			$sqlArrayRetenciones = "SELECT
		                                R.id,
		                                R.tipo_retencion,
		                                R.cuenta,
		                                R.retencion,
		                                R.valor,
		                                R.base
		                            FROM
		                                retenciones AS R
		                            RIGHT JOIN terceros_retenciones AS TR ON (
		                                (
		                                    TR.id_retencion = R.id
		                                    AND TR.id_proveedor = '$id'
		                                    OR R.factura_auto = 'true'
		                                )
		                                AND TR.activo = 1
		                                AND TR.id_empresa = '$id_empresa'
		                            )
		                            WHERE
		                                R.id_empresa = '$id_empresa'
		                            AND R.activo = 1
		                            AND R.cuenta > 0
		                            AND R.modulo = 'Venta'
		                            AND R.id NOT IN (
											SELECT
												id_retencion AS id
											FROM
												ventas_facturas_retenciones
											WHERE id_factura_venta = $idRegistro
										)
		                            GROUP BY
		                                R.id";


			$queryArrayRetenciones = mysql_query($sqlArrayRetenciones,$link);

		    while ($row=mysql_fetch_array($queryArrayRetenciones)) {

		    	$arrayRetenciones .= '
		    						var contenedor=document.getElementById(\'contenedorCheckbox'.$opcGrillaContable.'\');
									contenedor.innerHTML=contenedor.innerHTML+\'<div class="campoCheck" title="'.$row['tipo_retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">\'
                					                        				    +\'<div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>\'
                					                        				    +\'<input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'"  />\'
                					                        				    +\'<label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">\'
                					                        				        +\'<div class="labelNombreRetencion">'.$row['retencion'].'</div>\'
                					                        				        +\'<div class="labelValorRetencion">('.$row['valor'].'%)</div>\'
                					                        				    +\'</label>\'
                					                        				+\'</div>\';';


				$arrayRetenciones.='arrayRetenciones'.$opcGrillaContable.'['.$row['id'].']='.$row['id'].';';

		    	$arrayRetenciones.= 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                        .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                        .'base           : "'.$row['base'].'",'
                                                                                        .'valor          : "'.$row['valor'].'",'
                                                                                        .'cuenta         : "'.$row['cuenta_venta'].'",'
                                                                                        .'estado         : "0"'
                                                                                    .'};';

				// $arrayRetenciones     .='if(document.getElementById("checkboxRetenciones'.$opcGrillaContable.'_'.$row['id_retencion'].'")){
					// document.getElementById("checkboxRetenciones'.$opcGrillaContable.'_'.$row['id_retencion'].'").checked=true;
				// }';
				$sqlInsertRetencion   = "INSERT INTO $tablaRetenciones ($idTablaPrincipal,id_retencion) VALUES ('$idRegistro','".$row['id']."')";
				$queryInsertRetencion = mysql_query($sqlInsertRetencion);

				//consultamos el valor de cada retencion asignada al proveedor y las sumamos en la variable retefuenteFacturaCompra
				$sqlValorRetencion      = "SELECT valor FROM retenciones WHERE id=".$row['id'];
				$querySqlValorRetencion = mysql_query($sqlValorRetencion,$link);
				$arraySqlValorRetencion = mysql_fetch_array($querySqlValorRetencion);

			}
		}


		$sqlUpdate = "UPDATE $tablaPrincipal
						SET id_cliente  = '$id',
							cod_cliente = '$codCliente'
						WHERE id='$idRegistro'
							AND id_empresa = '$id_empresa'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($id > 0){

			$sqlSucursales     = "SELECT id,nombre FROM terceros_direcciones WHERE id_tercero=$id AND activo=1";
						$querySucursales   = mysql_query($sqlSucursales,$link);
						$sucursalesCliente = '<option value=\'0\'>Seleccione...</option>';
				        while ($row=mysql_fetch_array($querySucursales)) {
				            $sucursalesCliente .= '<option value=\''.$row['id'].'\'>'.$row['nombre'].'</option>';
				        }

			if ($opcGrillaContable=='FacturaVenta' || $opcGrillaContable=='RemisionesVenta') {
				$script_forma_pago='';
				if ($id_forma_pago>0 && $opcGrillaContable=='FacturaVenta') {
					$script_forma_pago='document.getElementById("formasDePago'.$opcGrillaContable.'").value="'.$id_forma_pago.'";
										UpdateFormaPago'.$opcGrillaContable.'("'.$id_forma_pago.'") ';
				}
				echo'<script>
						document.getElementById("sucursalCliente'.$opcGrillaContable.'").innerHTML = "'.$sucursalesCliente.'";
						'.$script_forma_pago.'
					</script>';
			}



			echo'<script>
					'.$arrayRetenciones.'

					document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
					document.getElementById("codCliente'.$opcGrillaContable.'").value    = "'.$codigo.'";
					document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombre.'";

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
						<div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
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
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'"  onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo campoDescuento">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" '.$readonly_descuento.' onKeyup="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" '.$readonly_precio.' onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

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
		$selectObservacion = "SELECT observaciones FROM $tablaInventario WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
		$observacion       = mysql_result(mysql_query($selectObservacion,$link),0,'observaciones');

		echo'<div style="margin: 10px">
				<div id="renderizaGuardarObservacion'.$opcGrillaContable.'_'.$cont.'"></div>
				<textarea id="observacionArticulo'.$opcGrillaContable.'_'.$cont.'" style="width:300px; height:150px;">'.$observacion.'</textarea>
			</div>';
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ===============================//
	function guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link){
		$sqlUpdateObservacion   = "UPDATE $tablaInventario SET observaciones='$observacion' WHERE id='$idArticulo' AND $idTablaPrincipal='$id'";
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
							AND I.estado_venta='true'
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

		if($exentoIva != 'Si'){
			//consultamos el valor del impuesto para asignarlo al campo oculto,
			$sqlImpuesto   = "SELECT impuesto,valor FROM impuestos WHERE id=$id_impuesto";
			$queryImpuesto = mysql_query($sqlImpuesto,$link);
			$valorImpuesto = mysql_result($queryImpuesto,0,'valor');
			$impuesto      = mysql_result($queryImpuesto,0,'impuesto');

			if ($valorImpuesto!="" && $impuesto!="") {
				$script = 'if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
								arrayIva'.$opcGrillaContable.'['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valorImpuesto.'"};
						 	}';
			}
		}
		else{ $id_impuesto = 0; }


		if($id > 0){
			if ($cantidad>0 ) {
			//VERIFICAR LA CANTIDAD MINIMA DE STOCK CON LA OPCGRILLACONTABLE PARA FILTRAR DE QUE SOLO SEA PARA LA REMISION
				if ($cantidad <= $cantidad_minima_stock && ($opcGrillaContable=='RemisionesVenta' || $opcGrillaContable=='FacturaVenta')) {
					$texto = ($cantidad == $cantidad_minima_stock)? "es igual": "es menor";
					echo '<script>alert("Aviso!\nEste articulo '.$texto.' a la cantidad minima de '.$cantidad_minima_stock.' '.$nombre_unidad.' en stock\nSolo restan '.$cantidad.' '.$nombre_unidad.' del articulo");</script>';
				}

				//SI LA CANTIDAD DEL ARTICULO ES MAYOR A CERO EN LA BODEGA, SE PERMITE REALIZAR LA VENTA DEL ARTICULO
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

		$sqlUpdateProveedor     = "UPDATE $tablaPrincipal SET id_cliente = 0, id_sucursal_cliente=0, nombre_sucursal_cliente='', exento_iva='' WHERE id = '$id'";
		$queryUpdateProveedor   = mysql_query($sqlUpdateProveedor,$link);

		$script = '';
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

		$sqlArticulo = "SELECT id_inventario,codigo,costo_unitario,cantidad_unidad_medida,nombre_unidad_medida,nombre,cantidad,tipo_descuento,descuento,id_impuesto
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
				document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value           = "'.$nombre_unidad.' x '.$numeroPiezas.'";
				document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value         = "'.$id_inventario.'";
				document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "'.$codigo.'";
				document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value      = "'.$costo.'";
				document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$nombreArticulo.'";
				document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value       = "'.$cantidad_articulo.'";
				document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value  = "'.$descuento_articulo.'";
				document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "'.$id_impuesto.'";
				document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.($cantidad_articulo*$costo).'";

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
		//==================================== GENERA COTIZACION =====================================//
		/**********************************************************************************************/
		if ($opcGrillaContable=='CotizacionVenta') {

			$titulo = 'Cotizacion de Venta';
			$sql    = "UPDATE $tablaPrincipal
						SET estado='1',
							total_unidades=(
								SELECT SUM(saldo_cantidad)
								FROM ventas_cotizaciones_inventario
								WHERE id_cotizacion_venta= '$id')
						WHERE id='$id' ";
			$query  = mysql_query($sql,$link);

			$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'";
			$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

			if ($query) {
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','CV','Cotizacion de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
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
								id_factura_venta  : "'.$id.'"
							}
						});

						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
					</script>';
			}
			else{ echo'<script> alert("No se guardo la '.$titulo.',\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema"); </script>'; }
		}
		//====================================== GENERA PEDIDO =======================================//
		/**********************************************************************************************/
		else if ($opcGrillaContable=='PedidoVenta') {

			//UPDATE SALDO UNIDADES TABLA PEDIDO INVENTARIO
			$sqlSaldoRemision   = "UPDATE ventas_pedidos_inventario SET saldo_cantidad=cantidad WHERE id_pedido_venta='$id'";
			$querySaldoRemision = mysql_query($sqlSaldoRemision,$link);

			$titulo            = 'Pedido de Venta';
			$sqlSelect         = "SELECT consecutivo_carga FROM $tablaPrincipal WHERE id='$id'";			//consulta cotizacion asociada
			$consecutivo_carga = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo_carga');

			//si carga cotizacion
			if ($consecutivo_carga>0 || $consecutivo_carga!=''){
				//CONSULTAMOS ESA COTIZACION PARA DETERMINAR SI ESTA DISPONIBLE Y PODER ASOCIAR EL PEDIDO CON ELLA
				$sqlConsulCotizacionPedido="SELECT COUNT(cliente) as cont,activo,estado FROM ventas_cotizaciones
											WHERE consecutivo='$consecutivo_carga'  AND id_sucursal= '$id_sucursal' AND id_bodega= '$idBodega'
											AND  ( '".date('Y-m-d')."' BETWEEN date_format(fecha_inicio,'%Y-%m-%d') AND date_format(fecha_finalizacion,'%Y-%m-%d') ) AND id_empresa=".$_SESSION['EMPRESA'];

				$queryConsulCotizacionPedido = mysql_query($sqlConsulCotizacionPedido,$link);
				$resulCotizacionPedido       = mysql_result($queryConsulCotizacionPedido,0,'cont');
				$activo                      = mysql_result($queryConsulCotizacionPedido,0,'activo');
				$estado                      = mysql_result($queryConsulCotizacionPedido,0,'estado');

				//INICIAMOS LA VERIFICACION DEL DOCUMENTO SI ESTA DISPONIBLE

				if ($activo=='0') { echo '<script>alert("Error!\nLa cotizacion a cargar se encuentra eliminada!");</script>'; return; }		//VALIDACION SI EXISTE LA COTIZACION
				else if ($estado!='1') { echo '<script>alert("Error!\nLa cotizacion a cargar no ha sido generada! '.$estado.' ");</script>'; return; } // VALIDACION COTIZACION CANCELADA
				else{

					//PRIMERO ACTUALIZAMOS EL PEDIDO A ESTADO 1 PARA QUE CUANDO SE ELIMINEN LOS REPETIDOS NO SE ELIMINE EL QUE SE ESTA CARGANDO Y QUE SE DESEA GENERAR
					$sqlUpdatePedido   = "UPDATE $tablaPrincipal SET estado='1',
												observacion='$observacion'
												unidades_pendientes=(
													SELECT SUM(saldo_cantidad)
													FROM ventas_pedidos_inventario
													WHERE id_pedido_venta= '$id')
										 	WHERE id='$id'";
					$queryUpdatePedido = mysql_query($sqlUpdatePedido,$link);
					$consecutivo       = mysql_result(mysql_query("SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'"),0,'consecutivo');	//consecutivo pedido

					//VALIDAMOS QUE SE HAYA ACTUALIZADO EL PEDIDO A ESTADO 1, DE LO CONTRARIO NO SE CONTINUA CON EL PROCESO
					if (!$queryUpdatePedido) { echo'<script>alert("Error!\nSe produjo un error y no se Genero el Pedido\nSi el problema persiste comuniquese con el administrador del sistema"); </script>'; exit(); }
				}
			}
			//SI NO. EL PEDIDO SE CREO DESDE CERO, POR LO TANTO SOLO SE GENERA SIN AFECTAR MAS TABLAS
			else{
				//ACTUALIZAR LA TABLA PARA PROCEDER A GENERARLA
				$sql   = "UPDATE $tablaPrincipal SET estado='1',
								observacion='$observacion',
								unidades_pendientes=(
									SELECT SUM(saldo_cantidad)
									FROM ventas_pedidos_inventario
									WHERE id_pedido_venta= '$id')
							WHERE id='$id' ";
				$query = mysql_query($sql,$link);

				//CONSULTAMOS EL CONSECUTIVO AUTOINCREMENTAL AUTOMATICO QUE SE GENERA POR TRIGGER AL ACTUALIZAR EL ESTADO DEL PEDIDO
				$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'";
				$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

				//SI SE ACTUALIZO CONTINUAMOS
				if (!$query) { echo'<script> alert("No se guardo la '.$titulo.',\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema"); </script>'; exit(); }
			}

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//log
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','PV','Pedido de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "bd/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : "'.$idBodega.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_factura_venta  : "'.$id.'"
						}
					});

					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$titulo.'<br>N. '.$consecutivo.'";
				</script>';
		}
		//====================================== GENERA REMISION =======================================//
		/***********************************************************************************************/
		else if ($opcGrillaContable=='RemisionesVenta') {

			//UPDATE SALDO UNIDADES TABLA REMISIONES INVENTARIO
			$sqlSaldoRemision   = "UPDATE ventas_remisiones_inventario SET saldo_cantidad=cantidad WHERE id_remision_venta='$id'";
			$querySaldoRemision = mysql_query($sqlSaldoRemision,$link);

			$titulo = 'Remision de Venta';

			//VALIDAR EL ESTADO DE LA REMISION
			$sqlRemision     = "SELECT estado FROM $tablaPrincipal WHERE id=$id LIMIT 0,1";
			$queryRemision   = mysql_query($sqlRemision,$link);
			$estado_remision = mysql_result($queryRemision,0,'estado');

			if(!$queryRemision){ echo '<script>alert("Aviso! No 1,\nSin conexion con la base de datos!");</script>'; exit; }			// ERROR QUERY
			else if ($estado_remision == 1){ echo '<script>alert("Aviso! No 3,\nLa remision ya ha sido generada");</script>'; exit; }	// REMISION GENERADA
			else if($estado_remision == 3){ echo '<script>alert("Aviso! No 2,\nLa remision ha sido cancelada");</script>'; exit; }		// REMISION CANCELADA

			//LLAMAMOS LA FUNCION validaCantidadArticulos QUE NOS RETORNARA SI UN ARTICULO EXECEDE EL STOCK
			$arrayRetorno = validaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$link);

			//VALIDAMOS LO QUE NOS RETORNO LA FUNCION
			if ($arrayRetorno[0]=='false') {
				echo '<script>alert("Error!\nEl Articulo '.$arrayRetorno[1].' tiene una cantidad mayor a la del inventario!\nCorrija el problema y intentelo nuevamente");</script>';
				return;
			}

			//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
			/********************************************************************************************************************************/
			$arraySindDoc      = '';
			$arrayRemisiones   = '';
			$arrayPedidos      = '';
			$arrayCotizaciones = '';

			$contRemisiones = 0;
			$contPedido     = 0;
			$contCotizacion = 0;
			$contSinDoc     = 0;

			$acumIdCotizacion = '';		//CONDICIONAL GLOBAL WHERE SQL IDS COTIZACION
			$acumIdPedido     = '';		//CONDICIONAL GLOBAL WHERE SQL IDS PEDIDO
			$acumIdRemisiones = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

			$sqlDocumentoAdjunto = "SELECT id_consecutivo_referencia AS id_referencia, nombre_consecutivo_referencia AS nombre_referencia
									FROM $tablaInventario
									WHERE $idTablaPrincipal='$id' AND activo=1
									GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";
			$queryDocumentoAdjunto = mysql_query($sqlDocumentoAdjunto,$link);
			while($rowDoc = mysql_fetch_array($queryDocumentoAdjunto)){

				$id_referencia     = $rowDoc['id_referencia'];
				$nombre_referencia = $rowDoc['nombre_referencia'];
				$arrayResult       = Array ( 'id_referencia' => $id_referencia, 'nombre_referencia' => $nombre_referencia);

				if($id_referencia > 0){																								//CON DOCUMENTO DE REFERENCIA
					if($nombre_referencia == 'Pedido'){ $contPedido++; $arrayPedidos[$contPedido] = $arrayResult; }
					else if($nombre_referencia == 'Cotizacion'){ $contCotizacion++; $arrayCotizaciones[$contCotizacion] = $arrayResult; }
				}
				else{ $contSinDoc++; $arraySindDoc[$contSinDoc][$id_referencia] = $nombre_referencia; }								//SIN DOCUMENTO DE REFERENCIA
			}

			//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
			/**********************************************************************************/
			if($contCotizacion>0){																									//VALIDACION COTIZACION
				for($cont=1; $cont<=$contCotizacion; $cont++) {
					$acumIdCotizacion .= ($acumIdCotizacion=='')? "id=":" OR id=";
					$acumIdCotizacion .= $arrayCotizaciones[$cont]['id_referencia'];
				}

				$sqlEstadoCotizacion   = "SELECT consecutivo,estado,activo FROM ventas_cotizaciones WHERE id_empresa=$id_empresa AND ($acumIdCotizacion)";
				$queryEstadoCotizacion = mysql_query($sqlEstadoCotizacion);
				while ($rowEstadoCotizacion = mysql_fetch_array($queryEstadoCotizacion)) {
					if($rowEstadoCotizacion['estado']==3){
						echo '<script>alert("Error!\nLa cotizacion codigo '.$rowEstadoCotizacion['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")</script>'; exit;
					}
				}
			}

			if($contPedido>0){ 																									// VALIDACION PEDIDO
				for($cont=1; $cont<=$contPedido; $cont++){
					$acumIdPedido .= ($acumIdPedido=='')? "id=":" OR id=";
					$acumIdPedido .= $arrayPedidos[$cont]['id_referencia'];
				}

				$sqlEstadoPedido   = "SELECT consecutivo,estado,activo FROM ventas_pedidos WHERE id_empresa=$id_empresa AND ($acumIdPedido)";
				$queryEstadoPedido = mysql_query($sqlEstadoPedido);
				while ($rowEstadoPedido = mysql_fetch_array($queryEstadoPedido)) {
					if($rowEstadoPedido['estado']==3){
						echo '<script>alert("Error!\nEl pedido codigo '.$rowEstadoPedido['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")</script>'; exit;
					}
				}


				// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES AL PEDIDO ==>
				$sqlValidaSaldo = " SELECT COUNT(TI.id) AS cont_validate_saldo
									FROM ventas_remisiones_inventario AS TI, ventas_pedidos_inventario AS TS
									WHERE TI.id_remision_venta='$id' AND TI.activo = 1 AND TS.activo = 1 AND TI.nombre_consecutivo_referencia='Pedido' AND TS.id=TI.id_tabla_inventario_referencia
										AND TI.cantidad > TS.saldo_cantidad
									GROUP BY TI.id";
				$contValidateSaldo = mysql_result(mysql_query($sqlValidaSaldo,$link),0,'cont_validate_saldo');
				if($contValidateSaldo > 0){ echo '<script>alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en el pedido que se adjunto en la presente Remision")</script>'; return; }

			}

			//PRIMERO ACTUALIZAMOS LA REMISION A ESTADO 1 PARA QUE CUANDO SE ELIMINEN LOS REPETIDOS NO SE ELIMINE EL QUE SE ESTA CARGANDO
			$sqlUpdateFactura = "UPDATE $tablaPrincipal
								SET estado='1',
									observacion='$observacion',
									pendientes_facturar=(
										SELECT SUM(saldo_cantidad)
										FROM ventas_remisiones_inventario
										WHERE id_remision_venta= '$id')
								WHERE id='$id'";
			$queryUpdateFactura=mysql_query($sqlUpdateFactura,$link);

			//VALIDAMOS QUE SE HAYA ACTUALIZADO LA REMISION A ESTADO 1, DE LO CONTRARIO NO SE CONTINUA CON EL PROCESO
			if ($queryUpdateFactura) {

				//EN ESTE PUNTO DAMOS DE BAJA LOS ARTICULOS DEL INVENTARIO
				$resul=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);

				if($resul==0){
					//GENERAMOS EL MOVIMIENTO DE LAS CUENTAS PARA LA REMISION
					moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'contabilizar',$link);

				}
				else{ echo '<script>alert("Error!\nNo se dieron de baja todos los articulos\nSi el problema persite contacte el administrador del sistema");</script>'; return; }

			}
			//SINO SE ACTUALIZO, MOSTRAMOS UNA ALERTA Y NO CONTINUAMOS GENERANDO MAS CAMBIOS  EN NINGUNA TABLA
			else{ echo'<script> alert("Error!\nSe produjo un error y no se Cerro la Remision\nSi el problema persiste comuniquese con el administrador del sistema"); </script>'; return; }

			//====================== ACTUALIZAR LOS DOCUMENTOS RELACIONADOS ===========================================//

			if($contCotizacion>0){																				//UPDATE COTIZACIONES
				$sqlUpdateEstado = "UPDATE ventas_cotizaciones SET estado=2 WHERE id_empresa=$id_empresa AND ($acumIdCotizacion)";
				mysql_query($sqlUpdateEstado,$link);
			}

			if($contPedido>0){																				//UPDATE PEDIDOS
				$sqlUpdateSaldos = "UPDATE ventas_pedidos_inventario AS VPI
									INNER JOIN ventas_remisiones_inventario AS VRI ON VPI.id=VRI.id_tabla_inventario_referencia
									SET VPI.saldo_cantidad= (VPI.saldo_cantidad-VRI.cantidad)
									WHERE VRI.id_remision_venta='$id' AND VRI.nombre_consecutivo_referencia='Pedido' AND VRI.id_tabla_inventario_referencia=VPI.id AND VPI.activo = 1  AND VRI.activo = 1";
				mysql_query($sqlUpdateSaldos,$link);

				//UPDATE TOTAL ARTICULOS PENDIENTES FACTURAR EN REMISION
				for($cont=1; $cont<=$contPedido; $cont++) {
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

			$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'";
			$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','RV','Remision de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);
			echo'<script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "bd/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_venta  : "'.$id.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : "'.$idBodega.'"
						}
					});
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Remision de Venta<br>N. '.$consecutivo.'";
				</script>';

			// }
			//====================================== NUEVA REMISION =======================================//
			// else{

			// 	//LLAMAMOS LA FUNCION validaCantidadArticulos QUE NOS RETORNARA SI UN ARTICULO EXECEDE EL STOCK
			// 	$arrayRetorno = validaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$link);

			// 	//VALIDAMOS LO QUE NOS RETORNO LA FUNCION
			// 	if ($arrayRetorno[0]=='false') {
			// 		echo '<script>alert("Error!\nEl Articulo '.$arrayRetorno[1].' tiene una cantidad mayor a la del inventario!\nCorrija el problema y intentelo nuevamente");</script>';
			// 		return;
			// 	}

			// 	//ACTUALIZAMOS LA REMISION PARA DARLA POR TERMINADA
			// 	$sqlTerminar   = "UPDATE $tablaPrincipal SET id_sucursal='$id_sucursal', id_bodega='$idBodega',  observacion='$observacion', estado='1' WHERE id='$id'";
			// 	$queryTerminar = mysql_query($sqlTerminar,$link);
			// 	if ($queryTerminar) {
			// 		//SI SE EJECUTO EL QUERY, SE ACTUALIZO Y SE GENERO EL NUMERO DEL CONSECUTIVO
			// 		//EN ESTE PUNTO DAMOS DE BAJA LOS ARTICULOS DEL INVENTARIO
			// 		$resul=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);

			// 		if($resul==0){
			// 			//GENERAMOS EL MOVIMIENTO DE LAS CUENTAS PARA LA REMISION
			// 			moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'contabilizar',$link);

			// 			$sqlSelect   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id'";
			// 			$consecutivo = mysql_result(mysql_query($sqlSelect,$link),0,'consecutivo');

			//      $fecha_actual = date('Y-m-d');
			//      $hora_actual  = date('H:i:s');

			// 			INSERTAR EL LOG DE EVENTOS
			// 			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
			// 						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','RV','Remision de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			// 			$queryLog = mysql_query($sqlLog,$link);

			// 			echo'<script>
			// 					Ext.get("contenedor_'.$opcGrillaContable.'").load({
			// 						url     : "bd/grillaContableBloqueada.php",
			// 						scripts : true,
			// 						nocache : true,
			// 						params  :
			// 						{
			// 							id_factura_venta  : "'.$id.'",
			// 							opcGrillaContable : "'.$opcGrillaContable.'",
			// 							filtro_bodega     : "'.$idBodega.'"
			// 						}
			// 					});
			// 					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Remision de Venta<br>N. '.$consecutivo.'";
			// 				</script>';
			// 		}
			// 		else{ echo '<script>alert("Error!\nNo se dieron de baja todos los articulos\nSi el problema persite contacte el administrador del sistema");</script>'; return; }

			// 	}
			// 	else{ echo '<script>alert("Se produjo un error y no se Genero la Remision\nSi el problema continua comuniquese con el administrador del sistema");</script>'; return; }

			// }
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
							WHERE TI.activo = 1
								AND TI.$idTablaPrincipal = '$id'
								AND TI.id_inventario = '$id_inventario'
								AND TI.inventariable = 'true'
								AND TIT.id_item='$id_inventario'
								AND TIT.id_sucursal='$id_sucursal'
								AND TIT.id_ubicacion='$idBodega'";

			$queryArticulo       = mysql_query($sqlArticulo,$link);
			$cantidad_documento  = mysql_result($queryArticulo,0,'cantidad_total');
			$cantidad_inventario = mysql_result($queryArticulo,0,'cantidad');

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
		$cont  = 0;
		$where = '';
		if ($tablaInventario=='ventas_facturas_inventario') { $where = "AND VFI.nombre_consecutivo_referencia<> 'Remision'"; }

		//CONSULTAMOS TODOS LOS ARTICULOS DE LA FACTURA
		$sqlArticulos="SELECT VFI.id_inventario,IT.nombre_equipo,VFI.cantidad AS cantidad_factura,IT.id_item,IT.cantidad AS cantidad_inventario ,VFI.costo_inventario AS costos
						FROM $tablaInventario AS VFI, inventario_totales AS IT
						WHERE VFI.activo=1
							AND VFI.$idTablaPrincipal='$id'
							AND IT.id_item=VFI.id_inventario
							AND IT.id_sucursal='$id_sucursal'
							AND VFI.inventariable='true'
							AND IT.id_ubicacion='$idBodega'
							$where
							AND IT.id_empresa=$_SESSION[EMPRESA]";
		$queryArticulos=mysql_query($sqlArticulos,$link);

		//VERIFICAMOS SI SE VAN A DAR DE BAJA LOS ARTICULOS O SE VAN A RETORNAR AL INVENTARIO

		if ($opc=='eliminar') {

			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {

				$sqlActuliza    = "UPDATE inventario_totales
									SET cantidad=cantidad-$rowArticulos[cantidad_factura]
									WHERE id_item=$rowArticulos[id_item]
										AND id_sucursal='$id_sucursal'
										AND id_ubicacion='$idBodega'
										AND id_empresa=$_SESSION[EMPRESA]";
				$queryActualiza = mysql_query($sqlActuliza,$link);
				if (!$queryActualiza) { $cont++; }
			}
		}
		else if ($opc=='agregar') {

			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {

				$sqlActuliza    = "UPDATE inventario_totales
									SET cantidad=cantidad+$rowArticulos[cantidad_factura]
									WHERE id_item=$rowArticulos[id_item]
										AND id_sucursal='$id_sucursal'
										AND id_ubicacion='$idBodega'
										AND id_empresa=$_SESSION[EMPRESA]";
				$queryActualiza = mysql_query($sqlActuliza,$link);
				if (!$queryActualiza){ $cont++; }
			}
		}
		else if ($opc=='costosArticulosVenta') {
			//RECORREMOS EL RESULTADO Y SUMAMOS LOS COSTOS DE LOS ARTICULOS QUE ESTAN EN EL DOCUMENTO
			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
				$cont+=($rowArticulos['costos']*$rowArticulos['cantidad_factura']);
			}
		}

		return $cont;
	}

	//=========================== FUNCION MOVER LAS CUENTAS CUANDO SE VAN A GENERER UNA FACTURA O REMISON =============================================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$accion,$link){

		$idEmpresa = $_SESSION['EMPRESA'];
		if ($opcGrillaContable=='FacturaVenta' && $accion=='descontabilizar') {
			//SE ELIMINA LA CONTABILIDAD COLGAAP
			$sqlColgaap   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND tipo_documento='FV' AND id_sucursal='$id_sucursal' AND id_empresa='$idEmpresa'";
			$queryColgaap = mysql_query($sqlColgaap,$link);

			$sqlColgaap   = "DELETE FROM contabilizacion_compra_venta WHERE id_documento='$idDocumento' AND tipo_documento='FV' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa=".$_SESSION['EMPRESA'];
			$queryColgaap = mysql_query($sqlColgaap,$link);

			//SE ELIMINA LA CONTABILIDAD NIIF
			$sqlNiif   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND tipo_documento='FV' AND id_sucursal='$id_sucursal' AND id_empresa='$idEmpresa'";
			$queryNiif = mysql_query($sqlNiif,$link);

			$sqlNiif   = "DELETE FROM contabilizacion_compra_venta_niif WHERE id_documento='$idDocumento' AND tipo_documento='FV' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa=".$_SESSION['EMPRESA'];
			$queryNiif = mysql_query($sqlNiif,$link);

			if (!$queryColgaap || !$queryNiif) {
				echo '<script>alert("Error!\nNo se descontabilizo el documento!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit;
			}
		}
		//EL DOCUMENTO ES UNA REMISION
		else if ($opcGrillaContable=='RemisionesVenta') {
			//LA REMISION MUEVE LAS CUENTAS  1435 -> CREDITO Y 6135 -> DEBITO

			//CONTABILIZAR DOCUMENTO
			if ($accion=='contabilizar') {

				//VALOR DEL MOVIMIENTO QUE ES IGUAL AL COSTO DE COMPRA DE TODOS LOS ARTICULOS DEL DOCUMENTO
				$costoArticulos = actualizaCantidadArticulos($idDocumento,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'costosArticulosVenta',$link);

				// VALIDACION QUE TODOS LOS ARTICULOS INVENTARIABLES TENGAN CONFIGURADO LA CUENTA INVENTARIO Y COSTOS
				$contNoContabilizacion = 0;
				$consultaCuentas = "SELECT COUNT(VRI.id) AS cont
									FROM ventas_remisiones_inventario AS VRI, items AS I
									WHERE VRI.activo = 1
										AND VRI.id_remision_venta = '$idDocumento'
										AND VRI.id_inventario= I.id
										AND I.inventariable= 'true'
										AND id_inventario NOT IN (
												SELECT id_items
												FROM items_cuentas
												WHERE activo=1
													AND id_empresa='$idEmpresa'
													AND estado='venta'
													AND (descripcion='costo' OR descripcion='contraPartida_costo')
											)
									GROUP BY VRI.activo=1";

				$contNoContabilizacion = mysql_result(mysql_query($consultaCuentas,$link),0,'cont');
				if($contNoContabilizacion > 0){ echo'<script>alert("Aviso!\n Hay articulos inventariables que no tiene configuracion contable.");</script>'; exit; }

				$sqlConsecutivo      = "SELECT consecutivo,id_cliente FROM ventas_remisiones WHERE activo=1 AND id='$idDocumento' LIMIT 0,1";
				$queryConsecutivo    = mysql_query($sqlConsecutivo,$link);
				$idCliente           = mysql_result($queryConsecutivo,0,'id_cliente');
				$consecutivoRemision = mysql_result($queryConsecutivo,0,'consecutivo');

				//================================ CONTABILIZACION CUENTAS COLGAAP ================================//
				/***************************************************************************************************/
				$consultaCuentasItems = "SELECT VRI.id,VRI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, VRI.costo_inventario AS costo, VRI.cantidad, IC.descripcion
										FROM ventas_remisiones_inventario AS VRI, items_cuentas AS IC
										WHERE VRI.activo = 1
											AND VRI.id_remision_venta = '$idDocumento'
											AND VRI.id_inventario = IC.id_items
											AND IC.activo         = 1
											AND IC.estado         = 'venta'
											AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
				$queryCuentasItems = mysql_query($consultaCuentasItems,$link);
				$valueInsertContabilizacion = '';
				while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
					$cuenta          = $rowCuentaItems['puc'];
					$id_item         = $rowCuentaItems['id_inventario'];
					$idDocInventario = $rowCuentaItems['id'];
					$id_puc          = $rowCuentaItems['id_puc'];
					$estado          = $rowCuentaItems['estado'];
					$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

					$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

					if(is_nan($arrayAsiento[$cuenta][$estadoAsiento])){ $arrayAsiento[$cuenta][$estadoAsiento] = 0; }
					$arrayAsiento[$cuenta][$estadoAsiento] += $costo;

					$arrayCuenta['debito']  = 0;
					$arrayCuenta['credito'] = 0;

					$valueInsertContabilizacion .= "('$id_item',
													'$id_puc',
													'$cuenta',
													'".$rowCuentaItems['estado']."',
													'".$rowCuentaItems['descripcion']."',
													'$idDocumento',
													'RV',
													'$idEmpresa',
													'$id_sucursal',
													'$idBodega'),";
				}
				$contAsientos  = 0;
				$globalDebito  = 0;
				$globalCredito = 0;
				$valueInsertAsientos = '';
				foreach ($arrayAsiento as $cuenta => $arrayCuenta) {
					$contAsientos++;
					$globalDebito  += $arrayCuenta['debe'];
					$globalCredito += $arrayCuenta['haber'];

					$valueInsertAsientos .= "('$idDocumento',
												'$consecutivoRemision',
												'RV',
												'Remision de Venta',
												'$idDocumento',
												'$consecutivoRemision',
												'RV',
												NOW(),
												'".$arrayCuenta['debe']."',
												'".$arrayCuenta['haber']."',
												'$cuenta',
												'$idCliente',
												'$id_sucursal',
												'$idEmpresa'
											),";
				}
				if($contAsientos == 0){ return; }
				else if($globalDebito != $globalCredito){ echo '<script>alert("Aviso.\nNo se cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

				//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
				$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
				echo $sqlInsertColgaap   = "INSERT INTO asientos_colgaap (
											id_documento,
											consecutivo_documento,
											tipo_documento,
											tipo_documento_extendido,
											id_documento_cruce,
											numero_documento_cruce,
											tipo_documento_cruce,
											fecha,
											debe,
											haber,
											codigo_cuenta,
											id_tercero,
											id_sucursal,
											id_empresa)
										VALUES $valueInsertAsientos";
				$queryInsertColgaap = mysql_query($sqlInsertColgaap,$link);
				if(!$queryInsertColgaap){ echo'<script>alert("Error!\nSin conexion con la base de datos. Si el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }


				$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
				$sqlContabilizar     = "INSERT INTO contabilizacion_compra_venta (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega) VALUES $valueInsertContabilizacion";
				$queryContabilizar   = mysql_query($sqlContabilizar,$link);


				//================================ CONTABILIZACION CUENTAS NIIF ================================//
				/************************************************************************************************/
				$consultaCuentasItems = "SELECT VRI.id,VRI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, VRI.costo_inventario AS costo, VRI.cantidad, IC.descripcion
										FROM ventas_remisiones_inventario AS VRI, items_cuentas_niif AS IC
										WHERE VRI.activo = 1
											AND VRI.id_remision_venta = '$idDocumento'
											AND VRI.id_inventario = IC.id_items
											AND IC.activo         = 1
											AND IC.estado         = 'venta'
											AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
				$queryCuentasItems = mysql_query($consultaCuentasItems,$link);
				$valueInsertContabilizacion = '';
				while ($rowCuentaItems = mysql_fetch_array($queryCuentasItems)) {
					$cuenta          = $rowCuentaItems['puc'];
					$id_item         = $rowCuentaItems['id_inventario'];
					$idDocInventario = $rowCuentaItems['id'];
					$id_puc          = $rowCuentaItems['id_puc'];
					$estado          = $rowCuentaItems['estado'];
					$costo           = $rowCuentaItems['costo'] * $rowCuentaItems['cantidad'];

					$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

					if(is_nan($arrayAsientoNiif[$cuenta][$estadoAsiento])){ $arrayAsientoNiif[$cuenta][$estadoAsiento] = 0; }
					$arrayAsientoNiif[$cuenta][$estadoAsiento] += $costo;

					$arrayCuenta['debito']  = 0;
					$arrayCuenta['credito'] = 0;

					$valueInsertContabilizacion .= "('$id_item',
													'$id_puc',
													'$cuenta',
													'".$rowCuentaItems['estado']."',
													'".$rowCuentaItems['descripcion']."',
													'$idDocumento',
													'RV',
													'$idEmpresa',
													'$id_sucursal',
													'$idBodega'),";
				}

				$contAsientos  = 0;
				$globalDebito  = 0;
				$globalCredito = 0;
				$valueInsertAsientos = '';
				foreach ($arrayAsientoNiif as $cuenta => $arrayCuenta) {
					$contAsientos++;
					$globalDebito  += $arrayCuenta['debe'];
					$globalCredito += $arrayCuenta['haber'];

					$valueInsertAsientos .= "('$idDocumento',
												'$consecutivoRemision',
												'RV',
												'Remision de Venta',
												'$idDocumento',
												'$consecutivoRemision',
												'RV',
												NOW(),
												'".$arrayCuenta['debe']."',
												'".$arrayCuenta['haber']."',
												'$cuenta',
												'$idCliente',
												'$id_sucursal',
												'$idEmpresa'
											),";
				}

				if($contAsientos == 0){ echo'<script>alert("Aviso!\nLos articulos no tienen una configuracion contable.");</script>'; exit; }
				else if($globalDebito != $globalCredito){ echo '<script>alert("Aviso.\nNo se cumple doble partida, Confirme su configuracion en el modulo panel de control.")</script>'; exit; }

				//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
				$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
				$sqlInsertColgaap   = "INSERT INTO asientos_niif (
											id_documento,
											consecutivo_documento,
											tipo_documento,
											tipo_documento_extendido,
											id_documento_cruce,
											numero_documento_cruce,
											tipo_documento_cruce,
											fecha,
											debe,
											haber,
											codigo_cuenta,
											id_tercero,
											id_sucursal,
											id_empresa)
										VALUES $valueInsertAsientos";
				$queryInsertColgaap = mysql_query($sqlInsertColgaap,$link);
				if(!$queryInsertColgaap){ echo'<script>alert("Error!\nSin conexion con la base de datos. Si el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }

				$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
				$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta_niif (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega)
										VALUES $valueInsertContabilizacion";
				$queryContabilizar = mysql_query($sqlContabilizar,$link);

			}
			//DESCONTABILIZAR DOCUMENTO
			else if ($accion=='descontabilizar') {
				//SE ELIMINAN TODOS LOS REGISTROS CONTABLES DE ESE DOCUMENTO
				$sqlAsiento   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_empresa='$idEmpresa'";
				$queryAsiento = mysql_query($sqlAsiento,$link);

				$sqlContabilidad   = "DELETE FROM contabilizacion_compra_venta WHERE id_documento='$idDocumento' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$idEmpresa'";
				$queryContabilidad = mysql_query($sqlContabilidad,$link);

				$sqlAsiento   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_empresa='$idEmpresa'";
				$queryAsiento = mysql_query($sqlAsiento,$link);

				$sqlContabilidad   = "DELETE FROM contabilizacion_compra_venta_niif WHERE id_documento='$idDocumento' AND tipo_documento='RV' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$idEmpresa'";
				$queryContabilidad = mysql_query($sqlContabilidad,$link);

				if (!$queryAsiento || !$queryContabilidad) {
					echo '<script>alert("Error!\nNo se descontabilizo el documento!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return;
				}
			}
		}
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA

	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$carpeta,$link){
		//===================================== EDITAR FACTURA DE VENTA =============================================//
		/*************************************************************************************************************/
		if ($opcGrillaContable=="FacturaVenta") {

			//VALIDAR QUE NO TENGA DOCUMENTOS RELACIONADOS
			validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link);

			//ACTUALIZAMOS LA FACTURA A ESTADO 0 'SIN GUARDAR'
			$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa='$id_empresa'";
			$query = mysql_query($sql,$link);

			if (!$query) { echo '<script>alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }

			//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
			/**********************************************************************************/
			sumarSaldoDocumentoCruce($idDocumento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link); 		// VALIDACION REMISION

			moverCuentasDocumento($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','FV','Factura de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}
		//==================================== EDITAR REMISIONES DE VENTA ===========================================//
		/*************************************************************************************************************/
		else if($opcGrillaContable == 'RemisionesVenta'){

			//VALIDAR QUE NO TENGA DOCUMENTOS RELACIONADOS
			validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link);

			//CONSULTAMOS LA TABLA PARA SABER SI SE CARGO A PARTIR DE UN DOCUMENTO, Y SI SE CARGO, ENTONCES HABILITAR EL DOCUMENTO CON QUE SE CARGO
			$sqlDocumentoCargado    = "SELECT consecutivo_carga, referencia_consecutivo_carga,estado FROM $tablaPrincipal WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryDocumentoCargado  = mysql_query($sqlDocumentoCargado,$link);
			$consecutivo            = mysql_result($queryDocumentoCargado,0,'consecutivo_carga');
			$referencia_consecutivo = mysql_result($queryDocumentoCargado,0,'referencia_consecutivo_carga');
			$estado                 = mysql_result($queryDocumentoCargado,0,'estado');

			if ($estado==2) { echo '<script>alert("Error!\nNo se puede editar esta remision!\nPor que ya ha sido Facturada");</script>'; return; }

			//SI CONSECUTIVO Y REFERENCIA TIENE VALORES, ENTONCES FILTRAMOS LAS REFERENCIAS PARA ACTUALIZAR LAS TABLAS
			if ($consecutivo!='' && $referencia_consecutivo!='') {
				if ($referencia_consecutivo=='Pedido') {
					//ESTO INDICA QUE LA REMISION SE CARGO CON UN PEDIDO, ENTONCES DEVOLVEMOS EL ESTADO DEL PEDIDO A 1
					$sqlPedido   = "UPDATE ventas_pedidos SET estado=1 WHERE activo=1 AND consecutivo='$consecutivo' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND id_empresa='$id_empresa'";
					$queryPedido = mysql_query($sqlPedido,$link);
					if (!$queryPedido) { echo '<script>alert("Error!\nNo se habilito el Pedido con el que se cargo la Remision!");</script>'; return; }
				}

			}

			//ACTUALIZAMOS LA REMISION A ESTADO 0 'SIN GUARDAR'
			$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
			$query = mysql_query($sql,$link);

			if (!$query) { echo '<script>alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }

			//DESCONTABILIZAMOS LA REMNISION
			moverCuentasDocumento($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);

			//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
			/**********************************************************************************/
			sumarSaldoDocumentoCruce($idDocumento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link); 		// VALIDACION REMISION

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','RV','Remision de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}

		if ($opcGrillaContable == "FacturaVenta" || $opcGrillaContable == 'RemisionesVenta'){
			//LLAMAMOS LA FUNCION Y LE ENVIAMOS COMO OPC LA CADENA 'agregar' PARA AGREGAR LOS ARTICULOS AL INVENTARIO NUEVAMENTE
			$resul = actualizaCantidadArticulos($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);

			if($resul==0){
				$queryLog=mysql_query($sqlLog,$link);
				cambiaEstadoDocumentoCruce($idDocumento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$id_bodega,$id_sucursal,$id_empresa,$link);
				echo'<script>
					 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
							url     : "'.$carpeta.'/grillaContable.php",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
								opcGrillaContable : "'.$opcGrillaContable.'",
								id_factura_venta  : "'.$idDocumento.'"
							}
						});
						Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
						document.getElementById("cotizacionPedido'.$opcGrillaContable.'").innerHTML="";
					</script>';
			}
			else{ echo '<script>alert("Error!\nNo se insertaron los articulos nuevamente al inventario!");</script>'; }
			return;
		}

		else if ($opcGrillaContable == "CotizacionVenta" || $opcGrillaContable == 'PedidoVenta'){
			//ACTUALIZAMOS LA FACTURA DE COMPRA A ESTADO 0 'SIN GUARDAR'
			$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1";
			$query = mysql_query($sql,$link);

			if (!$query) { echo '<script>alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }

			$typeDocumento = ($opcGrillaContable == "CotizacionVenta")? 'CV': 'PV';
			$descDocumento = ($opcGrillaContable == "CotizacionVenta")? 'Cotizacion de Venta': 'Pedido de Venta';

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','$typeDocumento','$descDocumento','$id_sucursal','$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : "'.$id_bodega.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_factura_venta  : "'.$idDocumento.'"
						}
					});

					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
				</script>';
		}
	}

	// ========================== FUNCION PARA CAMBIAR EL ESTADO DE UN DOCUMENTO CRUZADO =====================================================//
	function cambiaEstadoDocumentoCruce($idDocumento,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$id_bodega,$id_sucursal,$id_empresa,$link){
		$sqlCruce   = "SELECT id,id_consecutivo_referencia,consecutivo_referencia,nombre_consecutivo_referencia
						FROM $tablaInventario
						WHERE activo=1 AND $idTablaPrincipal=$idDocumento
						GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";
		$queryCruce = mysql_query($sqlCruce,$link);

		// RECORRER LOS DOCUMENTOS CRUZADOS EN ESTE DOCUMENTO
		$whereRemisiones = ($tablaInventario=='ventas_remisiones_inventario')? 'AND VR.id<>'.$idDocumento : '';
		$whereFacturas   = ($tablaInventario=='ventas_facturas_inventario')? 'AND VF.id<>'.$idDocumento  : '';

		while ($row = mysql_fetch_assoc($queryCruce)) {
			$contRemisiones = 0;
			$contFacturas   = 0;

			//VALIDACION QUE EL PEDIDO NO ESTE CRUZADO CON OTRO DOCUMENTO
			if ($row['nombre_consecutivo_referencia']=='Pedido') {
				$sql   = "SELECT COUNT(VRI.id) AS cont,VRI.id_remision_venta,VR.cliente,VR.estado
							FROM ventas_remisiones_inventario AS VRI,
								ventas_remisiones AS VR
							WHERE VRI.activo = 1
								AND VRI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
								AND VRI.consecutivo_referencia        = '$row[consecutivo_referencia]'
								AND VRI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
								AND VR.id=VRI.id_remision_venta
								$whereRemisiones
								AND VR.estado=1";
				$query = mysql_query($sql,$link);

				$contRemisiones = mysql_result($query,0,'cont');

				$sql   = "SELECT COUNT(VFI.id) AS cont,VFI.id_factura_venta,VF.cliente,VF.estado
							FROM ventas_facturas_inventario AS VFI,
								ventas_facturas AS VF
							WHERE VFI.activo = 1
								AND VFI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
								AND VFI.consecutivo_referencia        = '$row[consecutivo_referencia]'
								AND VFI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
								AND VF.id=VFI.id_factura_venta
								$whereFacturas
								AND VF.estado=1";

				$query        = mysql_query($sql,$link);
				$contFacturas = mysql_result($query,0,'cont');

				// SI LOS CONTADORES SON MAYORES A 0 ENTONCES ESE DOCUMENTO ESTA CRUZADO EN OTRA PARTE, SI NO, ENTONCES RETORNAMOS EL ESTADO DEL DOCUMENTO
				if ($contRemisiones==0 && $contFacturas==0) {
					$sql  = "UPDATE ventas_pedidos SET estado=1
							WHERE activo=1
								AND id_empresa=$id_empresa
								AND id_sucursal=$id_sucursal
								AND id_bodega=$id_bodega
								AND id=$row[id_consecutivo_referencia]";
					$query = mysql_query($sql,$link);
				}
			}
			//VALIDACION QUE LA REMISION NO ESTE CRUZADO CON OTRO DOCUMENTO
			else if($row['nombre_consecutivo_referencia']=='Remision') {
				$sql   = "SELECT COUNT(VFI.id) AS cont,VFI.id_factura_venta,VF.cliente,VF.estado
							FROM ventas_facturas_inventario AS VFI,
								ventas_facturas AS VF
							WHERE VFI.activo = 1
								AND VFI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
								AND VFI.consecutivo_referencia        = '$row[consecutivo_referencia]'
								AND VFI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
								AND VF.id=VFI.id_factura_venta
								$whereFacturas
								AND VF.estado=1";
				$query        = mysql_query($sql,$link);
				$contRemision = mysql_result($query,0,'cont');

				if ($contRemision==0) {
					$sql   = "UPDATE ventas_remisiones
								SET estado=1
								WHERE activo=1
									AND id_empresa=$id_empresa
									AND id_sucursal=$id_sucursal
									AND id_bodega=$id_bodega
									AND id=$row[id_consecutivo_referencia]";
					$query = mysql_query($sql,$link);
				}
			}
		}
	}

	//=========================== FUNCION PARA VALIDAR SI SE CRUZO EL DOCUMENTO ==============================================================//
	function validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$opcGrillaContable,$link){
		$tipo_documento_cruce = ($opcGrillaContable=='FacturaVenta')? 'FV' : 'RV';
		$texto = ($opcGrillaContable=='FacturaVenta')? 'Factura de venta' : 'Remision';

		$sqlNota    = "SELECT consecutivo_documento, tipo_documento
						FROM asientos_colgaap
						WHERE activo=1
							AND id_documento_cruce = '$idDocumento'
							AND tipo_documento_cruce='$tipo_documento_cruce'
							AND id_documento<>'$idDocumento'
							AND tipo_documento<>'$tipo_documento_cruce'
							AND id_empresa = '$id_empresa'
							AND id_sucursal = '$id_sucursal'
						GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';

		while ($row=mysql_fetch_array($queryNota)) { $doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento']; }
		if ($doc_cruces != '') { echo '<script>alert("Aviso!\nEsta '.$texto.' tiene relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nCancele los documentos cruce para editar.");</script>'; exit; }
	}

	//=========================== FUNCION PARA RESTAR O AGREGAR EL SALDO CANTIDAD DE LOS DOCUMENTOS RELACIONADOS =============================//
	function sumarSaldoDocumentoCruce($idFactura,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link){
		//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
		/********************************************************************************************************************************/
		$arrayPedidos  = Array();
		$contPedidos   = 0;
		$acumIdPedidos = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

		$arrayRemisiones  = Array();
		$contRemisiones   = 0;
		$acumIdRemisiones = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

		//PEDIDOS CRUCE
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
			$arrayPedidos[$contPedidos] = Array ('id_referencia' => $rowDoc['id_referencia']);
		}

		//REMISION CRUCE
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
			$arrayRemisiones[$contRemisiones] = Array ('id_referencia' => $rowDoc['id_referencia']);
		}


		//SI HAY PEDIDO RELACIONADOS AUMENTAR LA CANTIDAD
		if ($contPedidos>0) {

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
											WHERE id_pedido_venta= '$id_pedido')
										WHERE id=$id_pedido";
				mysql_query($sqlUpdatePendientes,$link);
			}
		}

		//SI HAY REMISIONES RELACIONADAS AUMENTAR LA CANTIDAD
		if ($contRemisiones>0) {

			$sql  = "UPDATE ventas_remisiones_inventario AS VRI
					INNER JOIN $tablaInventario AS VFI ON VRI.id=VFI.id_tabla_inventario_referencia
					SET VRI.saldo_cantidad = (VRI.saldo_cantidad + VFI.saldo_cantidad)
					WHERE VFI.$idTablaPrincipal='$idFactura'
						AND VRI.id_inventario = VFI.id_inventario
						AND VRI.activo = 1
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
											WHERE id_remision_venta= '$id_remision')
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
		else{ echo '<script>console.log("error al buscar iva");</script>'; }
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($consecutivo,$id,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$iva,$exento_iva,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		if ($exento_iva=='Si') { $iva = 0; }

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
		else{ echo "Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema "; }
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

		if ($exento_iva=='Si') { $valor_impuesto=0; }

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		echo'<script>calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'",'.$cont.');</script>';
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

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','CV','Cotizacion de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}
		else if ($opcGrillaContable=='PedidoVenta') {

			$sqlVerificar   ="SELECT estado FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryVerificar = mysql_query($sqlVerificar,$link);
			$estado         = mysql_result($queryVerificar,0,'estado');

			if ($estado==2){ echo '<script>alert("Error!\nNo se puede cancelar este pedido!\nPor que ya ha sido Remisionado o Facturado");</script>'; return; }
			else if ($estado==3) { echo '<script>alert("Error!\nEste Pedido ya esta Cancelado!");</script>'; return; }
			else{ $sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'"; }

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','PV','Pedido de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}
		else if ($opcGrillaContable=='RemisionesVenta') {

			//VALIDAR QUE NO TENGA DOCUMENTOS RELACIONADOS EN CONTABILIDAD
			validaDocumentoCruce($id,$id_empresa,$id_sucursal,$opcGrillaContable,$link);

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
			$sqlVerificar = "SELECT estado
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
				// if ($consecutivo_carga>0 && $referencia_consecutivo_carga=='Pedido') {
				// 	//ACTUALIZAMOS EL PEDIDO A ESTADO = 1 QUE ES CUANDO SE ENCUENTRA GENERADO Y LISTO PARA SER CARGADO
				// 	$sqlUpdatePedido   = "UPDATE ventas_pedidos SET estado=1 WHERE activo=1 AND consecutivo='$consecutivo_carga' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";
				// 	$queryUpdatePedido = mysql_query($sqlUpdatePedido,$link);

				// 	if (!$queryUpdatePedido) { echo '<script>alert("Error!\nEl pedido asignado a esta remision no se habilito!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; return; }
				// }

				// PROCEDEMOS A LA DESCONTABILIZACION DE LA MISMA
				moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);

				//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
				/**********************************************************************************/
				sumarSaldoDocumentoCruce($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link);		// VALIDACION REMISION

				cambiaEstadoDocumentoCruce($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$idBodega,$id_sucursal,$id_empresa,$link);

				// DEVOLVEMOS LOS ARTICULOS AL INVENTARIO
				$resul=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);
				if ($resul>0) { echo 'Error!\nNo se devolvieron algunos articulos al inventario!\nSi el problema persiste comuniquese con el administrador del sistema'; }
			}
			else if ($estado==2) { echo '<script>alert("Error!\nNo se puede cancelar esta remision!\nPor que ya ha sido Facturada");</script>'; return; }
			else if ($estado==3) { echo '<script>alert("Error!\nEsta remision ya esta Cancelada!");</script>'; return; }

			//SINO ES POSIBLE QUE SE HALLA CREADO A PARTIR DE UNA COTIZACION O SIN CARGAR NINGUN DOCUMENTO, EN CUALQUIERA DE LOS DOS CASOS SOLO SE EJECUTA LA ACTUALIZACION DE LA REMISION, POR QUE LA COTIZACION SE BLOQUEA POR TIEMPO
			$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','RV','Remision de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}
		else if ($opcGrillaContable=='FacturaVenta') {

			//VALIDAR QUE NO TENGA DOCUMENTOS RELACIONADOS EN CONTABILIDAD
			validaDocumentoCruce($id,$id_empresa,$id_sucursal,$opcGrillaContable,$link);

			//ESTADO DOCUMENTO
			$sqlVerificar   = "SELECT estado, numero_factura FROM $tablaPrincipal WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryVerificar = mysql_query($sqlVerificar,$link);
			$estado         = mysql_result($queryVerificar,0,'estado');
			$numeroFactura  = mysql_result($queryVerificar,0,'numero_factura');

			if ($estado==1  || $numeroFactura>0) {

				$res = actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);
				if($res > 0){ echo '<script>alert("Aviso!\nNo se actualizo el inventario\nSi el problema persite comuniquese con el administrador del sistema");</script>'; return; }

				moverCuentasDocumento($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,'descontabilizar',$link);

				//ACTUALIZAR EL DOCUMENTO A ESTADO 3 CANCELADO
				$sqlUpdate = "UPDATE $tablaPrincipal
								SET estado=3
								WHERE id='$id'
									AND activo=1
									AND id_sucursal='$id_sucursal'
									AND id_bodega='$idBodega'
									AND id_empresa='$id_empresa'";

				//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
				/**********************************************************************************/
				sumarSaldoDocumentoCruce($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$link);		// VALIDACION REMISION

				cambiaEstadoDocumentoCruce($id,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcGrillaContable,$idBodega,$id_sucursal,$id_empresa,$link);
			}
			else if ($estado==3) { echo '<script>alert("Error!\nEsta factura ya esta Cancelada!");</script>'; return; }
			else if ($estado==0 && ($numeroFactura=='' || $numeroFactura==0)) { $sqlUpdate = "UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'"; }

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','FV','Factura de Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
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

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','FV','Factura de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
 		}
 		else{
 			if ($opcGrillaContable=='CotizacionVenta') { 			//INSERTAR EL LOG DE EVENTOS
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');
				
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','CV','Cotizacion de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
 			}
 			elseif ($opcGrillaContable=='PedidoVenta') { 			//INSERTAR EL LOG DE EVENTOS
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');
				
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','PV','Pedido de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
 			}
 			elseif ($opcGrillaContable=='RemisionesVenta') { 		//INSERTAR EL LOG DE EVENTOS
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');
				
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','RV','Remision de Venta',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
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

 		switch ($typeDoc) {
			case 'cotizacion':
				$campoCantidad          = "cantidad";
				$title                  = 'Eliminar los Articulos de la Cotizacion';
				$referencia_input       = "C";
				$referencia_consecutivo = "Cotizacion";
				$tablaCarga             = "ventas_cotizaciones";
				$idTablaCargar          = "id_cotizacion_venta";
				$tablaCargaInventario   = "ventas_cotizaciones_inventario";

				$opcCargar = 'cotizacionRemision';
				$tablaBuscar = 'ventas_cotizaciones';

				break;

			case 'pedido':
				$campoCantidad          = "saldo_cantidad";
				$title                  = 'Eliminar los Articulos del Pedido';
				$referencia_input       = "P";
				$referencia_consecutivo = "Pedido";
				$tablaCarga             = "ventas_pedidos";
				$idTablaCargar          = "id_pedido_venta";
				$tablaCargaInventario   = "ventas_pedidos_inventario";

				$opcCargar = 'pedidoRemision';
				$tablaBuscar = 'ventas_pedidos';

				break;
		}

		$carpeta=($opcGrillaContable=='RemisionesVenta')? 'remisiones' : 'pedido' ;

		$sqlDocumento       = "SELECT id_cliente, estado FROM $tablaPrincipal WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$queryDocumento     = mysql_query($sqlDocumento,$link);

		$idClienteDocumento= mysql_result($queryDocumento,0,'id_cliente');
		$estadoDocumento    = mysql_result($queryDocumento,0,'estado');

		if($estadoDocumento == 1){ echo '<script>alert("Error!,\nEl documento actual ha sido generado.");</script>'; return; }
		if($estadoDocumento == 3){ echo '<script>alert("Error!,\nEl documento actual ha sido cancelado.");</script>'; return; }
		else if($idClienteDocumento== '' || $idClienteDocumento== 0){
			buscarCotizacionPedido($codDocAgregar,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, '',$opcCargar);
			return;
			// echo '<script>alert("Aviso!,\nSeleccione un cliente para la factura.");</script>'; return;
		}

		$whereDocumentoCarga = ($typeDoc == 'pedido')? "AND CO.unidades_pendientes > 0": "";

		//VALIDACION ESTADO DE LA FACTURA
		$idClienteDocAgregar    = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT id_cliente,estado,id,id_bodega
									FROM $tablaCarga
									WHERE consecutivo='$codDocAgregar'
										AND id_bodega='$filtro_bodega'
										AND id_empresa='$id_empresa'
									LIMIT 0,1";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idClienteDocAgregar = mysql_result($queryValidateDocumento,0,'id_cliente');
		$idDocumentoAgregar  = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar    = mysql_result($queryValidateDocumento,0,'estado');

		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		else if($idClienteDocAgregar <> $idClienteDocumento){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenece a un cliente diferente.");</script>'; return; }

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

	function updateSucursalCliente($id_factura,$id_scl_cliente,$nombre_scl_cliente,$opcGrillaContable,$id_empresa,$link){
		$sql   = "UPDATE ventas_remisiones SET id_sucursal_cliente='$id_scl_cliente',sucursal_cliente='$nombre_scl_cliente' WHERE id='$id_factura' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);
		// echo '<script>console.log("'.$sql.'");;</script>';
	}


	function eliminarFacturaWs($id_factura,$id_empresa,$link){
		// ELIMINAR LOS ASIENTOS CONTABLES
		$sql="DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_factura AND tipo_documento='FV' ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se puedo eliminar los asientos colgaap, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			// echo $sql;
			return;
		}

		$sql="DELETE FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_factura AND tipo_documento='FV' ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se puedo eliminar los asientos niif, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			// echo $sql;
			return;
		}

		$sql="DELETE FROM ventas_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_factura";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se puedo eliminar la factura, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			// echo $sql;
			return;
		}

		echo '<script>
				Win_Ventana_factura_web_service.close();
				document.getElementById("item_FacturaVenta_'.$id_factura.'").parentNode.removeChild(document.getElementById("item_FacturaVenta_'.$id_factura.'"));
			</script>';
	}
	function detalleDashboard($bodega,$sucursal,$table,$tabOpcion){

		  //visualizador de datos del dashboard

		  echo 'bodega: '.$bodega.'\n Sucursal: '.$sucursal.'\n tabla: '.$table.' pesta&ntilde;a: '.$tabOpcion;

          $SQL1 = mysql_query("SELECT * FROM ".$tabla."WHERE estado = 1 AND activo = 1 AND id_bodega = '".$filtro_bodega."' AND id_sucursal = '".$filtro_sucursal."'".$agrupa_por,$link);
	      $IND1 = mysql_num_rows($SQL1);
	      //if($IND1 == 0){$IMG1 = 'ok';}else{$IMG1 = 'alert';}

	      switch($tabOpcion){

	      	   case 'Global':
	      	       echo '<div><br>grafico '.$tabOpcion.'<div>';
	      	       break;
	      	   case 'Cliente':
	      	       echo '<div><br>grafico '.$tabOpcion.'<div>';
	      	       break;
	      	   case 'Vendedor':
	      	       echo '<div><br>grafico '.$tabOpcion.'<div>';
	      	       break;
	      	   case 'Centro costo':
	      	       echo '<div><br>grafico '.$tabOpcion.'<div>';
	      	       break;
	      	   case 'Pendientes por facturar':
	      	       echo '<div><br>grafico '.$tabOpcion.'<div>';
	      	       break;
	      	   case 'Facturadas':
	      	       echo '<div><br>grafico '.$tabOpcion.'<div>';
	      	       break;
	      }



	}
?>
