<?php
	include_once("../../../../../configuracion/conectar.php");
	include_once("../../../../../configuracion/define_variables.php");
	include_once("../../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");

	include_once("contabilizar_bd.php");
	include_once("contabilizar_niif_bd.php");
	include_once("../config_var_global.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

    $saldoGlobalNotaSinAbono = 0;								//VARIABLE GLOBAL TOTAL CUENTA CLIENTES O PROVEEDORES

    if (isset($id)) {
    	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if ($opc<>'actualizarFechaNota') {
			if($opcCargar=='facturaVenta'){verificaCierre($id,'fecha_inicio',$tablaPrincipal,$id_empresa,$link,$opcCargar);}
			else{verificaCierre($id,'fecha_registro',$tablaPrincipal,$id_empresa,$link,$opcCargar);}
		}

  	if ($opc!='cancelarDocumento' && $opc!='restaurarDocumento' && $opc!='modificarDocumentoGenerado' && $opc!='buscarCotizacionPedido' && $opc!='actualizaArticulo') {
  		verificaEstadoDocumento($id,$opcGrillaContable,$tablaPrincipal,$link);
  	}

    }

	switch ($opc) {
		case 'cargarCampoFacturaCompra':
			cargarCampoFacturaCompra($opcGrillaContable);
			break;

		case 'buscarCotizacionPedido':
			buscarCotizacionPedido($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar,$idDocumento);
			break;

		case 'buscarCliente':
			buscarCliente($id,$codCliente,$inputId,$id_empresa,$opcGrillaContable,$tablaPrincipal,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaDescripcionArticulo':
			ventanaDescripcionArticulo($cont,$idArticulo,$id,$opcGrillaContable,$idTablaPrincipal,$tablaInventario,$link);
			break;

		case 'guardarDescripcionArticulo':
			guardarDescripcionArticulo($observacion,$idArticulo,$id,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'buscarArticulo':
			buscarArticulo($id,$idNota,$opcCargar,$campo,$eanArticulo,$cont,$id_empresa,$idCliente,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$exentoIva,$link);
			break;

		case 'cambiaCliente':
			cambiaCliente($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$tablaPrincipal,$link);
			break;

		case 'deleteArticulo':
			deleteArticulo($cont,$id,$id_doc_cruce,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$idInsertFilaCargada,$opcCargar,$mysql);
			break;

		case 'retrocederArticulo':
		 	retrocederArticulo($id,$tablaCargaInventario,$idArticulo,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'checkboxRetenciones':
			checkboxRetenciones($id,$idRetencion,$accion,$opcGrillaContable,$tablaRetenciones,$idTablaPrincipal,$link);
			break;

		case 'UpdateFormaPago':
			UpdateFormaPago($id,$idFormaPago,$tablaPrincipal,$opcGrillaContable,$link,$fechaVencimiento);
			break;

		case 'terminarGenerar':
			terminarGenerar($id,$idDocumentoCarga,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$opcCargar,$id_empresa,$fecha,$numero_documento_cruce,$link,$id_motivo_dian);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($id,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcCargar,$carpeta,$link,$idDocumentoCarga);
			break;

		case 'buscarImpuestoArticulo':
			buscarImpuestoArticulo($idInsert,$idFactura,$opcGrillaContable,$cont,$unidadMedida,$idArticulo,$codigo,$cantidad,$descuento,$costo,$nombreArticulo,$id_empresa,$link);
			break;

		case 'guardarArticulo':
			guardarArticulo($consecutivo,$id,$idNota,$cont,$idInventario,$codigo,$cantArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$accion,$opcCargar,$mysql);
			break;

		case 'actualizaArticulo':
			actualizaArticulo($id,$idNota,$codigo,$idInsertArticulo,$idInsertFilaCargada,$idInsertNewFilaCargada,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$opcCargar,$mysql);
			break;

		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$tablaPrincipal,$id_empresa,$link);
			break;

		case 'verificaCantidadArticulo':
			verificaCantidadArticulo($id,$id_empresa,$id_sucursal,$filtro_bodega,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$opcCargar,$link,$idDocumentoCarga);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($id,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'actualizarFechaNota':
			actualizarFechaNota($id,$fecha,$tablaPrincipal,$link);
			break;

		case 'enviarDevolucionDIAN':
			enviarDevolucionDIAN($id_devolucion,$opcGrillaContable,$id_empresa,$id_sucursal,$mysql,$link);
			break;
	}

	//============================= FUNCION PARA MOSTRAR EL CAMPO DE CARGAR DESDE ===============================================================//
	function cargarCampoFacturaCompra($opcGrillaContable){
		if ($opcGrillaContable!='DevolucionCompra') {
			echo'<div style="float:left; width:120px; display:table-cell; vertical-align:middle; margin-left:8px;" id="divContenedorCargarDesde'.$opcGrillaContable.'" title="Click para cargar una Factura" onclick="cambiarCargaFactura()">
					<div class="div_hover" style="width:80px;" id="imgFacturarDesde'.$opcGrillaContable.'" ><img src="img/remisiones.png" id="imgCargarDesde'.$opcGrillaContable.'" width"20px" height="20px"/></div>
					<div class="div_hover" style="width:90px" id="textoFacturardesde'.$opcGrillaContable.'" ><b>Remision</b> </div>
					<div class="div_hover" style="width:10px;"><img src="img/flecha_abajo.png"/></div>
				</div>

				<div style="float:left; margin: 5px 0 0 5px">
				    <div style="float:left; width:120px; height:22px;">
					    <input placeholder="Numero..." class="myFieldGrilla" id="cotizacionPedido'.$opcGrillaContable.'" onKeyup="buscarCotizacionPedido'.$opcGrillaContable.'(event,this)" style="padding-left:5px;"/>
					</div>
					<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarCotizacionPedido'.$opcGrillaContable.'();">
					   <img src="../../temas/clasico/images/BotonesTabs/buscar20.png"/>
					</div>
				    <div style="float:left; max-width:20px; max-height:20px; overflow:hidden; float:left; margin-left:-23px" id="renderCargaCotizacionPedido'.$opcGrillaContable.'"></div>
				</div>
				<script>
					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();

					function cambiarCargaFactura(){
						//SE LLAMAN ESTA FUNCION, Y ESTA FUNCION ESTA EN grillaContable.php Y default.php EN LAS ULTIMAS LINEAS DE CODIGO
						limpiarGrillaContable("'.$opcGrillaContable.'",document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value);
					}
			    </script>';
		}
		else{
			echo'<div style="float:left; margin: 13px 0 0 5px">
				    <div style="float:left; width:120px; height:22px;">
					    <input id="cotizacionPedido'.$opcGrillaContable.'" placeholder="Numero..." class="myFieldGrilla" onKeyup="buscarCotizacionPedido'.$opcGrillaContable.'(event,this)" style="padding-left: 5px;"/>
					</div>
					<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarCotizacionPedido'.$opcGrillaContable.'();">
					    <img src="../../temas/clasico/images/BotonesTabs/buscar20.png"/>
					</div>
				    <div style="float:left; max-width:20px; max-height:20px; overflow:hidden; float:left; margin-left:-23px" id="renderCargaCotizacionPedido'.$opcGrillaContable.'"></div>
				</div>
				<script>
					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
			    </script>';
	    }
	}

	//============================ FUNCION PARA BUSCAR Y ASIGNAR UNA COTIZACION/PEDIO A UNA FACTURA/PEDIDO ======================================//
	function buscarCotizacionPedido($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar,$idDocumento){
		$docError = 'Factura de Compra';
		if ($opcCargar=='facturaCompra') {

			if($idDocumento > 0){ $where='id='.$idDocumento; }
			else{
				$arrayFactura = explode(' ', $id);
				$contFactura  = count($arrayFactura);
				if($contFactura == 1){ $where='numero_factura = '.$id; }
				else if($contFactura == 2){ $where='prefijo_factura = \''.$arrayFactura[0].'\' AND numero_factura='.$arrayFactura[1]; }
				else{
					echo'<script>
		        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
		    				alert("Aviso!\nNumero de factura con separadores no permitidos.");
		    				setTimeout(function(){
		    					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
		    				},100);
		    			</script>';
		    			exit;
				}
			}

			$sql = "SELECT id,COUNT(proveedor) AS cont, estado
					FROM compras_facturas
					WHERE $where
						AND id_sucursal = '$id_sucursal'
						AND id_bodega = '$filtro_bodega'
						AND id_empresa = '$id_empresa'
						AND activo = 1
						AND (estado = 1 OR estado = 2)";

			$query = mysql_query($sql,$link);

			$id_documento_cruce     = mysql_result($query,0,'id');
			$estado_documento_cruce = mysql_result($query,0,'estado');

			$sqlSaldos   = "SELECT COUNT(id) AS saldo FROM compras_facturas_inventario WHERE id_factura_compra='$id_documento_cruce' AND saldo_cantidad > 0";
			$querySaldos = mysql_query($sqlSaldos,$link);
			$saldo_documento_cruce = mysql_result($querySaldos,0,'saldo');

		}
		else if($opcCargar=='remisionVenta') {
			$docError = 'Remision de venta';
			$where    = ($idDocumento>0)? 'id='.$idDocumento : "consecutivo = '$id'" ;

			$sql = "SELECT id,COUNT(cliente) AS cont, estado
					FROM ventas_remisiones
					WHERE $where
						AND id_sucursal = '$id_sucursal'
						AND id_bodega = '$filtro_bodega'
						AND id_empresa = '$id_empresa'
						AND activo = 1
						AND estado > 0";
			$query = mysql_query($sql,$link);

			$id_documento_cruce     = mysql_result($query,0,'id');
			$estado_documento_cruce = mysql_result($query,0,'estado');

			$sqlSaldos             = "SELECT COUNT(id) AS saldo FROM ventas_remisiones_inventario WHERE id_remision_venta='$id_documento_cruce' AND saldo_cantidad > 0";
			$querySaldos           = mysql_query($sqlSaldos,$link);
			$saldo_documento_cruce = mysql_result($querySaldos,0,'saldo');
		}
		else if($opcCargar=='facturaVenta') {
			$docError = 'Factura de venta';
			$where = ($idDocumento!='')? 'id='.$idDocumento : "numero_factura_completo = '".$id."'" ;

			$sql = "SELECT id,estado,total_factura_sin_abono,email_fe
							FROM ventas_facturas
							WHERE $where
							AND id_sucursal = '$id_sucursal'
							AND id_bodega = '$filtro_bodega'
							AND id_empresa = '$id_empresa'
							AND activo = 1
							AND estado > 0";
			$query = mysql_query($sql,$link);

			$id_documento_cruce     = mysql_result($query,0,'id');
			$estado_documento_cruce = mysql_result($query,0,'estado');
			$total_factura_sin_abono_documento_cruce = mysql_result($query,0,'total_factura_sin_abono');
			$email_fe_documento_cruce = mysql_result($query,0,'email_fe');

			if($email_fe_documento_cruce != "" || $email_fe_documento_cruce != null){
				$total_factura_sin_abono_documento_cruce = 1;
			}

			$sqlSaldos             = "SELECT COUNT(id) AS saldo FROM ventas_facturas_inventario WHERE id_factura_venta='$id_documento_cruce' AND saldo_cantidad > 0";
			$querySaldos           = mysql_query($sqlSaldos,$link);
			$saldo_documento_cruce = mysql_result($querySaldos,0,'saldo');
		}
		
		if ($id_documento_cruce == "") {				//NO EXISTE LA FACTURA
			echo'<script>
        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
    				alert("Aviso!\nLa '.$docError.' no esta registrada en el sistema.");
    				setTimeout(function(){
    					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
    				},100);
    			</script>';
		}
		// else if ($total_factura_sin_abono_documento_cruce == 0) {				//NO TIENE SALDO EN LA CABECERA
		// 	echo'<script>
		  //       			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
		  //   				alert("Aviso!\nLa '.$docError.' no posee saldo disponible.");
		  //   				setTimeout(function(){
		  //   					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
		  //   				},100);
		  //   			</script>';
				// }
		else if($estado_documento_cruce != 1 && $estado_documento_cruce != 2){ 		//FACTURA EN ESTADO 1
        	echo'<script>
        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
        			alert("Aviso!\nLa '.$docError.' cambio de estado.\nEs posible que no este generada o no Exista.");
        			setTimeout(function(){
        				document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
        			},100);
        		</script>';
        }
		else if ($saldo_documento_cruce == 0) {						//FACTURA CON UNIDADES SIN NOTA
			echo'<script>
        			document.getElementById("cotizacionPedido'.$opcGrillaContable.'").blur();
    				alert("Aviso!\nLa '.$docError.' no tiene articulos disponibles para realizar devolucion.");
    				setTimeout(function(){
    					document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
    				},100);
    			</script>';
		}
        else if ($id_documento_cruce > 0 && $saldo_documento_cruce > 0) {		//OK
        	echo'<script>
        			document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";

        			Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_documento      : "'.$id_documento_cruce.'",
							opcCargar         : "'.$opcCargar.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : "'.$filtro_bodega.'"
						}
					});
        		</script>';
        }
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
			$campo     = 'cod_cliente';
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
			$textAlert = 'Id';
		}

		$SQL   = "SELECT id,numero_identificacion,nombre,cod_cliente FROM terceros WHERE $campo='$codCliente' AND activo=1 AND tercero = 1 AND id_empresa='$id_empresa' AND tipo_cliente='Si' LIMIT 0,1";
		$query = mysql_query($SQL,$link);
		$id     = mysql_result($query,0,'id');
		$nit    = mysql_result($query,0,'numero_identificacion');
		$codigo = mysql_result($query,0,'cod_cliente');
		$nombre = mysql_result($query,0,'nombre');

		// CHECKBOX RETENCIONES CHECKED SI opcGrillaContable ES IGUAL A FacturaVenta
		if ($opcGrillaContable=='FacturaVenta') {

			$arrayRetenciones      = '';
			$sqlArrayRetenciones   = "SELECT id_retencion FROM terceros_retenciones WHERE activo=1 AND id_empresa='$id_empresa' AND id_cliente='$id'";
			$queryArrayRetenciones = mysql_query($sqlArrayRetenciones,$link);
		    while ($row=mysql_fetch_array($queryArrayRetenciones)) {

				$arrayRetenciones     .='if(document.getElementById("checkboxRetenciones'.$opcGrillaContable.'_'.$row['id_retencion'].'")){
												document.getElementById("checkboxRetenciones'.$opcGrillaContable.'_'.$row['id_retencion'].'").checked=true;
											}';
				$sqlInsertRetencion   = "INSERT INTO $tablaRetenciones ($idTablaPrincipal,id_retencion) VALUES ('$idRegistro','".$row['id_retencion']."')";
				$queryInsertRetencion = mysql_query($sqlInsertRetencion);

				//consultamos el valor de cada retencion asignada al proveedor y las sumamos en la variable retefuenteFacturaCompra
				$sqlValorRetencion      = "SELECT valor FROM retenciones WHERE id=".$row['id_retencion'];
				$querySqlValorRetencion = mysql_query($sqlValorRetencion,$link);
				$arraySqlValorRetencion = mysql_fetch_array($querySqlValorRetencion);
			}
		}
		$sqlUpdate = "UPDATE $tablaPrincipal
						SET id_empresa = '$id_empresa',
						id_cliente     = '$id',
						cod_cliente    = '$codCliente'
						WHERE id='$idRegistro'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($nombre!=''){
			echo'<script>
					'.$arrayRetenciones.'

					id_cliente_'.$opcGrillaContable.'                                    = "'.$id.'";
					document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
					document.getElementById("codCliente'.$opcGrillaContable.'").value    = "'.$codigo.'";
					document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombre.'";
					codigoCliente'.$opcGrillaContable.' = "'.$codigo.'";
					nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
					nombreCliente'.$opcGrillaContable.' = "'.$nombre.'";

				</script>'.cargaHeadInsertUnidades('return',1,$opcGrillaContable);
		}
		else{
			echo'<script>
					alert("'.$textAlert.' de cliente no establecido");
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
							<div class="label" style="width:12%">Codigo/EAN</div>
							<div class="labelNombreArticulo" style="width:30%">Articulo</div>
							<div class="label">Unidad</div>
							<div class="label">Cantidad</div>
							<div class="label">Descuento</div>
							<div class="label">Precio</div>
							<div class="label" style="border-right: 1px solid #d4d4d4">Costo Total</div>
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
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Iva</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="ivaAcumulado'.$opcGrillaContable.'">0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesAcumulado'.$opcGrillaContable.'" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionAcumulado'.$opcGrillaContable.'"> </div>
							<div class="labelSimbolo" id="simboloRetencionAcumulado'.$opcGrillaContable.'"></div>
							<div class="labelTotal" style="height:auto; overflow:visible;" id="retefuenteAcumulado'.$opcGrillaContable.'"></div>
						</div>
						<div class="renglon renglonTotal" >
							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL</div>
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
		$body ='<div class="campo" style="width:40px !important; overflow:hidden;">
					<div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
					<div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
				</div>

				<div class="campo" style="width:12%;">
					<input type="text" id="eanArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="buscarArticulo'.$opcGrillaContable.'(event,this);"/>
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticulo'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarArticulo'.$opcGrillaContable.'('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidades'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly/></div>
				<div class="campo"><input type="text" id="cantArticulo'.$opcGrillaContable.'_'.$cont.'" onKeydown="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\''.$cont.'\');"/></div>

				<div class="campo campoDescuento">
					<div onclick="tipoDescuentoArticulo'.$opcGrillaContable.'('.$cont.')" id="tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" onKeydown="validarNumberArticulo'.$opcGrillaContable.'(event,this,\'double\',\'\');"/>
				</div>

				<div class="campo"><input type="text" id="costoArticulo'.$opcGrillaContable.'_'.$cont.'" onKeyup="guardarAuto'.$opcGrillaContable.'(event,this,'.$cont.');" value="0"/></div>

				<div class="campo"><input type="text" id="costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'"  readonly/></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageSave'.$opcGrillaContable.'_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'"/></div>
					<div onclick="retrocederArticulo'.$opcGrillaContable.'('.$cont.')" id="divImageDeshacer'.$opcGrillaContable.'_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticulo'.$opcGrillaContable.'('.$cont.')" id="descripcionArticulo'.$opcGrillaContable.'_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/edit.png"/></div>
					<div onclick="deleteArticulo'.$opcGrillaContable.'('.$cont.')" id="deleteArticulo'.$opcGrillaContable.'_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"/></div>
				</div>

				<input type="hidden" id="idArticulo'.$opcGrillaContable.'_'.$cont.'" value="0"/>
				<input type="hidden" id="idInsertArticulo'.$opcGrillaContable.'_'.$cont.'" value="0"/>
				<input type="hidden" id="ivaArticulo'.$opcGrillaContable.'_'.$cont.'" value="0" >

				<!-- id de la fila insertada del documento -->
				<input type="hidden" id="idInsertFilaCargada'.$opcGrillaContable.'_'.$cont.'" value="'.$id_fila_cargada.'"/>
				<!-- id de la fila nueva a  insertar en la nota -->
				<input type="hidden" id="idInsertNewFilaCargada'.$opcGrillaContable.'_'.$cont.'" value=""/>

				<script>
					document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';

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
		else{ echo 'La Observacion no se ha almacenado, favor intente gurdar nuevamente. Si el problema persiste favor comuniquese al administrador del sistema'; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ===============================================================================//
	function buscarArticulo($id,$idNota,$opcCargar,$campo,$eanArticulo,$cont,$id_empresa,$idCliente,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$exentoIva,$link){
		if($campo == 'eanArticulo'.$opcGrillaContable.''){ $where = 'AND (codigo = '.$eanArticulo.')'; }
		else if($campo == 'codigoProveedorArticulo'.$opcGrillaContable.''){ $where = 'AND codigo_proveedor = '.$eanArticulo; }

		//VERIFICAR QUE DOCUMENTO SE CARGO
		if ($opcCargar=='facturaCompra') {
			$tablaCarga   = 'compras_facturas_inventario';
			$idTablaCarga = 'id_factura_compra';
		}
		//SE VA A CARGAR UNA REMISION DE VENTA PARA LA DEVOLUCION
		else if ($opcCargar=='remisionVenta') {
			$tablaCarga   = 'ventas_remisiones_inventario';
			$idTablaCarga = 'id_remision_venta';
		}
		//SE VA A CARGAR UNA FACTURA DE VENTA PARA LA DEVOLUCION
		else if ($opcCargar=='facturaVenta') {
			$tablaCarga   = 'ventas_facturas_inventario';
			$idTablaCarga = 'id_factura_venta';
		}
		$sqlArticulo ="SELECT
							id,
							id_inventario,
							codigo,
							nombre,
							nombre_unidad_medida,
							cantidad_unidad_medida,
							tipo_descuento,
							descuento,
							costo_unitario,
							id_impuesto,
							impuesto,
							valor_impuesto,
							saldo_cantidad  AS cantidad
						FROM $tablaCarga
						WHERE activo = 1
							AND  $idTablaCarga = '$id'
							$where
							AND id NOT IN (
									SELECT id_fila_cargada
									FROM $tablaInventario
									WHERE $idTablaPrincipal='$idNota'
										AND id_fila_cargada=$tablaCarga.id
								)
							AND saldo_cantidad>0
						LIMIT 0,1";

		$query = mysql_query($sqlArticulo,$link);

		$id_fila        = mysql_result($query,0,'id');
		$id_inventario  = mysql_result($query,0,'id_inventario');
		$codigo         = mysql_result($query,0,'codigo');
		$nombre_unidad  = mysql_result($query,0,'nombre_unidad_medida');
		$nombreArticulo = mysql_result($query,0,'nombre');
		$numeroPiezas   = mysql_result($query,0,'cantidad_unidad_medida');
		$id_impuesto    = mysql_result($query,0,'id_impuesto');
		$impuesto       = mysql_result($query,0,'impuesto');
		$valor_impuesto = mysql_result($query,0,'valor_impuesto');
		$cantidad       = mysql_result($query,0,'cantidad');
		$descuento      = mysql_result($query,0,'descuento');
		$tipo_descuento = mysql_result($query,0,'tipo_descuento');
		$costo_unitario = mysql_result($query,0,'costo_unitario');

		if($exentoIva != 'Si'){
			//consultamos el valor del impuesto para asignarlo al campo oculto,
			$sqlImpuesto   = "SELECT impuesto,valor FROM impuestos WHERE id=$id_impuesto";
			$queryImpuesto = mysql_query($sqlImpuesto,$link);
			$valorImpuesto = mysql_result($queryImpuesto,0,'valor');
			$impuesto      = mysql_result($queryImpuesto,0,'impuesto');

			if ($valorImpuesto!="" && $impuesto!="") {
				$script = 'if (typeof(arrayIva'.$opcGrillaContable.'['.$id_impuesto.'])=="undefined") {
								arrayIva'.$opcGrillaContable.'['.$id_impuesto.'] = { nombre:"'.$impuesto.'", valor:"'.$valorImpuesto.'" };
						 	}';
			}
		}
		else{ $id_impuesto = 0; }

		($tipo_descuento=='porcentaje')? $titulo='En porcentaje' : $titulo='En pesos';

		if($id_inventario > 0){

			echo'<script>
					document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value       = "'.$nombre_unidad.' x '.$numeroPiezas.'";
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$id_inventario.'";
					document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "'.$codigo.'";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$nombreArticulo.'";
					document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value   = "'.$cantidad.'";

					document.getElementById("idInsertNewFilaCargada'.$opcGrillaContable.'_'.$cont.'").value = "'.$id_fila.'";

					document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/'.$tipo_descuento.'.png");
                	document.getElementById("tipoDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","'.$titulo.'");

					document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$descuento.'";
					document.getElementById("ivaArticulo'.$opcGrillaContable.'_'.$cont.'").value       = "'.$id_impuesto.'";
					document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$costo_unitario.'";

					setTimeout(function(){ document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").focus(); },50);

					//LE ASIGNAMOS EL VALOR DE LA CANTIDAD AL ARRAY PARA VALIDAR LAS CANTIDADES POR ARTICULO
					cantidadesArticulos'.$opcGrillaContable.'["'.$cont.'"]='.$cantidad.';

					//ARRAY PARA DISCRIMINAR EL IVA
					'.$script.'
				</script>';
		}
		else{
			echo'<script>
					alert("El articulo No esta en Disponible!");
					setTimeout(function(){ document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").focus(); },100);
					document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value        = "0";
					document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value          = "";
					document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "";
					document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value    = "";
					document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value      = "'.$cantidad.'";
					document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value = "'.$descuento.'";

					cantidadesArticulos'.$opcGrillaContable.'["'.$cont.'"]="";
				</script>';
		}
	}

	//=========================== FUNCION PARA CAMBIAR EL PROVEEDOR DE LA FACTURA ===============================================================//
	function cambiaCliente($id,$tablaInventario,$tablaRetenciones,$idTablaPrincipal,$opcGrillaContable,$tablaPrincipal,$link){
		$sqlDeleteInventario    = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal = '$id'";
		$queryDeleteInventario  = mysql_query($sqlDeleteInventario,$link);

		$sqlDeleteRetenciones   = "DELETE FROM $tablaRetenciones  WHERE $idTablaPrincipal = '$id'";
		$queryDeleteRetenciones = mysql_query($sqlDeleteRetenciones,$link);

		$sqlUpdateProveedor     = "UPDATE $tablaPrincipal SET id_cliente = 0 WHERE id = '$id'";
		$queryUpdateProveedor   = mysql_query($sqlUpdateProveedor,$link);

		echo'<script>
				id_cliente_'.$opcGrillaContable.'   = 0;
				contArticulos'.$opcGrillaContable.' = 1;
				nitCliente'.$opcGrillaContable.'    = 0;
				codigoCliente'.$opcGrillaContable.' = 0;
				nombreCliente'.$opcGrillaContable.' = "";
			</script>';
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA ==============================================//
	function retrocederArticulo($id,$tablaCargaInventario,$idRegistro,$cont,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link){

		$sqlArticulo   = "SELECT id_fila_cargada,id_inventario,codigo,costo_unitario,cantidad_unidad_medida,nombre_unidad_medida,nombre,cantidad,tipo_descuento,descuento
							FROM $tablaInventario WHERE activo=1 AND $idTablaPrincipal='$id' AND id='$idRegistro' LIMIT 0,1;";
		$queryArticulo = mysql_query($sqlArticulo,$link);
		if(!$queryArticulo){ echo '<script>alert("Error,\nNo hay conexion con el servidor, Si el problema persiste favor comuniquese con el administrador del sistema.")</script>'; }

		$id_fila            = mysql_result($queryArticulo,0,'id_fila_cargada');
		$id_inventario      = mysql_result($queryArticulo,0,'id_inventario');
		$codigo             = mysql_result($queryArticulo,0,'codigo');
		$costos             = mysql_result($queryArticulo,0,'costo_unitario');
		$nombre_unidad      = mysql_result($queryArticulo,0,'nombre_unidad_medida');
		$nombreArticulo     = mysql_result($queryArticulo,0,'nombre');
		$numeroPiezas       = mysql_result($queryArticulo,0,'cantidad_unidad_medida');
		$cantidad_articulo  = mysql_result($queryArticulo,0,'cantidad');
		$tipoDesc           = mysql_result($queryArticulo,0,'tipo_descuento');
		$descuento_articulo = mysql_result($queryArticulo,0,'descuento');

		$imgDescuento    = '';
		$tituloDescuento = '';

		if ($tipoDesc == 'porcentaje') {
			$imgDescuento    = 'img/porcentaje.png';
			$tituloDescuento = 'En porcentaje';
		}
		else{
			$imgDescuento    = 'img/pesos.png';
			$tituloDescuento = 'En pesos';
		}

		$sqlSaldoCantidad = "SELECT saldo_cantidad FROM ".$tablaCargaInventario."_inventario WHERE id='$id_fila' AND activo=1 LIMIT 0,1";
		$saldoCantidad    = mysql_result(mysql_query($sqlSaldoCantidad,$link),0,'saldo_cantidad');

		$totalCostoArticulo = $cantidad_articulo * $costos;

		echo'<script>
				cantidadesArticulos'.$opcGrillaContable.'["'.$cont.'"] = "'.$saldoCantidad.'";

				document.getElementById("unidades'.$opcGrillaContable.'_'.$cont.'").value               = "'.$nombre_unidad.' x '.$numeroPiezas.'";
				document.getElementById("idArticulo'.$opcGrillaContable.'_'.$cont.'").value             = "'.$id_inventario.'";
				document.getElementById("eanArticulo'.$opcGrillaContable.'_'.$cont.'").value            = "'.$codigo.'";
				document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").value           = "'.$cantidad_articulo.'";
				document.getElementById("costoArticulo'.$opcGrillaContable.'_'.$cont.'").value          = "'.$costos.'";
				document.getElementById("nombreArticulo'.$opcGrillaContable.'_'.$cont.'").value         = "'.$nombreArticulo.'";
				document.getElementById("descuentoArticulo'.$opcGrillaContable.'_'.$cont.'").value      = "'.$descuento_articulo.'";
				document.getElementById("costoTotalArticulo'.$opcGrillaContable.'_'.$cont.'").value     = "'.$totalCostoArticulo.'";
				document.getElementById("idInsertNewFilaCargada'.$opcGrillaContable.'_'.$cont.'").value = "'.$id_fila.'";

				document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
				document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

				document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","'.$imgDescuento.'");
				document.getElementById("imgDescuentoArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","'.$tituloDescuento.'");
			</script>';
	}

	//=========================== FUNCION PARA AGREGAR O QUITAR RETENCIONES A LA FACTURA ========================================================//
	function checkboxRetenciones($id,$idRetencion,$accion,$opcGrillaContable,$tablaRetenciones,$idTablaPrincipal,$link){
		//cadena con el query para insertar o eliminar una retencion de la factura
		$sqlRetencion="";

		//cadena para consultar el valor de la retencion y agregar o restar el valor a la variable retefuenteCompra de la factura de compra
		$sqlValorRetencion      = "SELECT valor FROM retenciones WHERE id=".$idRetencion;
		$querySqlValorRetencion = mysql_query($sqlValorRetencion,$link);
		$arraySqlValorRetencion = mysql_fetch_array($querySqlValorRetencion);

		if ($accion=="eliminar"){ $sqlRetencion="DELETE FROM $tablaRetenciones WHERE $idTablaPrincipal=$id AND id_retencion=$idRetencion"; }			//eliminar una retencion insertada
		else if ($accion=="insertar"){ $sqlRetencion="INSERT INTO $tablaRetenciones ($idTablaPrincipal,id_retencion) VALUES ('$id','$idRetencion')"; }	//insertar una nueva retencion a la factura

		$queryRetencion=mysql_query($sqlRetencion,$link);

		if(!$queryRetencion){ echo'<script>alert("No se logro '.$accion.' la retencion");</script>'; }
	}

	//=========================== FUNCION PARA ACTUALIZAR LA FORMA DE PAGO ======================================================================//
	function UpdateFormaPago($id,$idFormaPago,$tablaPrincipal,$opcGrillaContable,$link,$fechaVencimiento){
		//si es una factura se actualiza el id de la forma de pago
		if ($opcGrillaContable=='FacturaVenta') {
			$sql   = "UPDATE $tablaPrincipal SET id_forma_pago='$idFormaPago',fecha_vencimiento='$fechaVencimiento' WHERE id='$id'";
			$query = mysql_query($sql,$link);

			if ($query){ $mensaje='<script>calculaPlazo'.$opcGrillaContable.'(); </script>'; }
			else{
	        	$mensaje = '<script>
			        			alert("Error!\nNo se actualizo la forma de pago");
			        		</script>';
			}
			echo $mensaje;
		}
		//sino se actualiza directamente la fecha final en cotizacion, pedido, remision
		else{
			$sql   = "UPDATE $tablaPrincipal SET fecha_finalizacion='$idFormaPago' WHERE id='$id'";
			$query = mysql_query($sql,$link);

			if (!$query){  echo $mensaje = '<script>alert("Error!\nNo se actualizo la fecha de vencimiento");</script>'; }
		}
	}

	//===========================// NOTA DE DEVOLUCION DE VENTA O DE COMPRA  //===========================//
	function terminarGenerar($id,$idDocumentoCarga,$id_sucursal,$idBodega,$observacion,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$opcGrillaContable,$opcCargar,$id_empresa,$fecha,$numero_documento_cruce,$link,$id_motivo_dian){
		global $saldoGlobalNotaSinAbono;
		$labelConsecutivo = "";

		//VALIDAMOS EL ULTIMO CONSECUTIVO SEGUN EL MODULO
		$moduloConsecutivo = ($opcGrillaContable == "DevolucionCompra")? "compra" : "venta";

		//CONSULTAMOS EL ULTIMO CONSECUTIVO
		$sqlConsecutivo =  "SELECT consecutivo
												FROM configuracion_consecutivos_documentos
												WHERE activo = 1
												AND id_empresa = $id_empresa
												AND id_sucursal = $id_sucursal
												AND modulo = '$moduloConsecutivo'
												AND documento = 'devolucion'";
		$queryConsecutivo = mysql_query($sqlConsecutivo,$link);

		$consecutivoBD = mysql_result($queryConsecutivo,0,'consecutivo');

		if($consecutivoBD == 0){
			echo "<script>
							alert('Consecutivo para la devolucion esta en cero, elija cual es el consecutivo siguiente en la devolucion. Este proceso solo debera realizarlo una vez. Dirijase al Panel De Control -> Configuracion Sucursal -> Seccion Parametrizaciones Modulo de Venta o Compra -> Configuracion Consecutivos.');
							document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
						</script>";
			exit;
		}
		else if($consecutivoBD == "" || $consecutivoBD == null){
			echo "<script>
							alert('Consecutivo de devolucion no existe. Por favor Comunicarse con el administrador del sistema.');
							document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
						</script>";
			exit;
		}

		//===================// NOTA DE DEVOLUCION DE COMPRA //===================//
		//************************************************************************//
		if ($opcGrillaContable == 'DevolucionCompra') {

			$sqlFactura   = "SELECT total_factura_sin_abono,id_configuracion_cuenta_pago FROM compras_facturas WHERE id='$idDocumentoCarga' AND id_empresa='$id_empresa'";
			$queryFactura = mysql_query($sqlFactura,$link);
			$totalFactura = mysql_result($queryFactura,0,'total_factura_sin_abono');
			$idCuentaPago = mysql_result($queryFactura,0,'id_configuracion_cuenta_pago');
			if($totalFactura == 0){
				echo '<script>
						alert("Aviso! No 1,\nLa factura no tiene saldo suficiente por descontar!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}		// FACTURA ELIMINADA

			$labelConsecutivo     = "Devolucion de Compra <br>N.";
			$sqlDocumentoCompra   = "SELECT count(id) AS cont,consecutivo,id_documento_compra,estado,plantillas_id AS plantilla,id_proveedor
															 FROM devoluciones_compra
															 WHERE activo=1
															 AND id='$id'
															 LIMIT 0,1";
			$queryDocumentoCompra = mysql_query($sqlDocumentoCompra,$link);

			$idFactura         = mysql_result($queryDocumentoCompra,0,'id_documento_compra');
			$consecutivoActual = mysql_result($queryDocumentoCompra,0,'consecutivo');
			$estado            = mysql_result($queryDocumentoCompra,0,'estado');
			$idPlantilla       = mysql_result($queryDocumentoCompra,0,'plantilla');
			$idProveedor       = mysql_result($queryDocumentoCompra,0,'id_proveedor');
			$cont              = mysql_result($queryDocumentoCompra,0,'cont');
			$cont              = mysql_result($queryDocumentoCompra,0,'cont');

			if($cont == 0){
				echo '<script>
						alert("Error!\nLa presente nota no se encuentra activa!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			else if($estado != 0){
				echo '<script>
						alert("Error!\nLa presente nota ha cambiado de estado!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			verificarDocumento('compras_facturas','id_factura_compra',$idDocumentoCarga,$id_empresa,$link);

			//CUENTA DE PAGO ESTADO (credito-contado)
			$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Compra'";
			$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
			$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

			if($idPlantilla > 0){
				contabilizarNotaFacturaCompraConPlantilla($id,$idBodega,$id_sucursal,$id_empresa,$idPlantilla,$idFactura,$idProveedor,$link,$fecha,$numero_documento_cruce,$totalFactura);
				contabilizarNotaFacturaCompraConPlantillaNiif($id,$idBodega,$id_sucursal,$id_empresa,$idPlantilla,$idFactura,$idProveedor,$link,$fecha,$numero_documento_cruce,$totalFactura);
			}
			else{
				contabilizarNotaFacturaCompraSinPlantilla($estadoCuentaPago,$id,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link,$fecha,$numero_documento_cruce,$totalFactura);
				contabilizarNotaFacturaCompraSinPlantillaNiif($estadoCuentaPago,$id,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link,$fecha,$numero_documento_cruce,$totalFactura);
			}

			if($saldoGlobalNotaSinAbono > 0){
				$updateSaldoFactura = "UPDATE compras_facturas SET total_factura_sin_abono=(total_factura_sin_abono - $saldoGlobalNotaSinAbono)
										WHERE activo=1 AND id_empresa='$id_empresa' AND id='$idFactura'";
				$querySaldoFactura  = mysql_query($updateSaldoFactura,$link);

				if(!$querySaldoFactura){
					echo '<script>
							alert("Error No 2.\nNo se ha establecido conexion con el servidor si el problema persiste consulte el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			//ACTUALIZAR LOS ARTICULOS O ACTIVOS FIJOS
			actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link,$idDocumentoCarga);

			//ACTUALIZAR LOS SALDOS DEL DOCUMENTO CARGADO
			actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,'compras_facturas_inventario','eliminar',$link);

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','NDFC','Nota Devolucion Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);
		}
		// NDRV
		// NDFV
		// NDFC
		//===================// NOTA DE DEVOLUCION DE VENTA //===================//
		//***********************************************************************//
		else if ($opcGrillaContable=='DevolucionVenta') {

			$labelConsecutivo = "Devolucion de Venta <br>N.";

			$sqlDocumentoVenta =  "SELECT count(id) AS cont,consecutivo,id_documento_venta,estado,plantillas_id,id_cliente,documento_venta
															FROM devoluciones_venta
															WHERE activo=1 AND id='$id' LIMIT 0,1";
			$queryDocumentoVenta = mysql_query($sqlDocumentoVenta,$link);

			$idDocumentoVenta  = mysql_result($queryDocumentoVenta,0,'id_documento_venta');
			$consecutivoActual = mysql_result($queryDocumentoVenta,0,'consecutivo');
			$documento_venta   = mysql_result($queryDocumentoVenta,0,'documento_venta');
			$estado            = mysql_result($queryDocumentoVenta,0,'estado');
			$idPlantilla       = mysql_result($queryDocumentoVenta,0,'plantilla');
			$idCliente         = mysql_result($queryDocumentoVenta,0,'id_cliente');
			$cont              = mysql_result($queryDocumentoVenta,0,'cont');

			if($cont == 0){
				echo '<script>
						alert("Error!\nLa presente nota no se encuentra activa!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			else if($estado != 0){
				echo '<script>
						alert("Error!\nLa presente nota ha cambiado de estado!");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			//============// REMISION DE VENTA //============//
			if ($documento_venta == 'Remision') {

				verificarDocumento('ventas_remisiones','id_remision_venta',$idDocumentoCarga,$id_empresa,$link);

				//ACTUALIZAR LAS CANTIDADES DE LOS ARTICULOS EN INVENTARIO TOTALES
				$res = actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link);
				if ($res>0) {
					echo '<script>
							alert("Error!\nAlgunos articulos no regresaron al inventario!\nSi el problema persite comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				$res1=actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,'ventas_remisiones_inventario','eliminar',$link);
				if ($res1>0) {
					echo '<script>
							alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				contabilizarNotaRemisionVenta($id,$idBodega,$id_sucursal,$id_empresa,$idDocumentoVenta,$idCliente,$link,$fecha,$numero_documento_cruce);
				contabilizarNotaRemisionVentaNiif($id,$idBodega,$id_sucursal,$id_empresa,$idDocumentoVenta,$idCliente,$link,$fecha,$numero_documento_cruce);

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','NDRV','Nota Devolucion Remision venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);

			}
			//============// FACTURA DE VENTA //============//
			else if ($documento_venta == 'Factura') {

				verificarDocumento('ventas_facturas','id_factura_venta',$idDocumentoCarga,$id_empresa,$link);

				$sqlFactura   = "SELECT plantillas_id, estado, activo, id_cliente, id_configuracion_cuenta_pago, cuenta_pago, cuenta_pago_niif, id_centro_costo, exento_iva, total_factura_sin_abono, email_fe
								FROM ventas_facturas
								WHERE id='$idDocumentoVenta' AND id_empresa='$id_empresa'";
				$queryFactura = mysql_query($sqlFactura,$link);

				$estado      = mysql_result($queryFactura,0,'estado');
				$activo      = mysql_result($queryFactura,0,'activo');
				$idPlantilla = mysql_result($queryFactura,0,'plantillas_id');
				$idCcos      = mysql_result($queryFactura,0,'id_centro_costo');
				$exento_iva  = mysql_result($queryFactura,0,'exento_iva');

				$totalFactura   = mysql_result($queryFactura,0,'total_factura_sin_abono');
				$email_fe       = mysql_result($queryFactura,0,'email_fe');
				$idCuentaPago   = mysql_result($queryFactura,0,'id_configuracion_cuenta_pago');
				$cuentaPago     =  mysql_result($queryFactura,0,'cuenta_pago');
				$cuentaPagoNiif = mysql_result($queryFactura,0,'cuenta_pago_niif');

				//CUENTA DE PAGO ESTADO (credito-contado)
				$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Venta'";
				$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
				$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

				$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

				if(!$queryFactura){ echo '<script>alert("Aviso! No 1,\nSin conexion con la base de datos!");document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);</script>'; exit; }			// ERROR QUERY
				else if($estado == 3){ echo '<script>alert("Aviso! No 2,\nLa factura ha sido cancelada!");document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);</script>'; exit; }		// FACTURA CANCELADA
				else if($activo == 0){ echo '<script>alert("Aviso! No 3,\nLa factura ha sido eliminada!");document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);</script>'; exit; }		// FACTURA ELIMINADA
				else if($totalFactura == 0 && $estadoCuentaPago<>'Contado'){
					if($email_fe == ""){
						echo '<script>alert("Aviso! No 4,\nLa factura no tiene saldo suficiente por descontar!");document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);</script>';
						exit;
					}
				}		// FACTURA ELIMINADA

				if($estado == 1 || $estado == 2){
					if($idPlantilla > 0){
						contabilizarNotaFacturaVentaConPlantilla($id,$id_sucursal,$id_empresa,$idPlantilla,$idDocumentoVenta,$idCliente,$exento_iva,$link,$fecha,$numero_documento_cruce,$totalFactura);
						contabilizarNotaFacturaVentaConPlantillaNiif($id,$id_sucursal,$id_empresa,$idPlantilla,$idDocumentoVenta,$idCliente,$exento_iva,$link,$fecha,$numero_documento_cruce,$totalFactura);
					}
					else{
						contabilizarNotaFacturaVentaSinPlantilla($arrayCuentaPago,$idCcos,$id,$idBodega,$id_sucursal,$id_empresa,$idDocumentoVenta,$idCliente,$exento_iva,$link,$fecha,$numero_documento_cruce,$totalFactura);
						contabilizarNotaFacturaVentaSinPlantillaNiif($arrayCuentaPago,$idCcos,$id,$idBodega,$id_sucursal,$id_empresa,$idDocumentoVenta,$idCliente,$exento_iva,$link,$fecha,$numero_documento_cruce,$totalFactura);
					}

					// ACTUALIZAR LOS ARTICULOS O ACTIVOS FIJOS
					actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link,$idDocumentoCarga);

					//ACTUALIZAR LOS SALDOS DEL DOCUMENTO CARGADO
					actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,'ventas_facturas_inventario','eliminar',$link);

					$fecha_actual = date('Y-m-d');
					$hora_actual  = date('H:i:s');

					//INSERTAR EL LOG DE EVENTOS
					$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','NDFV','Nota Devolucion Factura Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
					$queryLog = mysql_query($sqlLog,$link);
				}
			}

		}

		//ELEGIMOS EL MOTIVO DE LA DEVOLUCION DE LA FACTURA
		switch ($id_motivo_dian){
			case 1:
				$descripcion_motivo_dian = "No aceptacion o devolucion de parte de los bienes";
				break;
			case 2:
				$descripcion_motivo_dian = "Anulacion de la factura electronica";
				break;
			case 3:
				$descripcion_motivo_dian = "Rebaja total aplicada";
				break;
			case 4:
				$descripcion_motivo_dian = "Descuento total aplicado";
				break;
			case 5:
				$descripcion_motivo_dian = "Rescision";
				break;
			case 6:
				$descripcion_motivo_dian = "Otros";
				break;
		}

		$camposDian =($documento_venta == 'Factura')?",id_motivo_dian = '$id_motivo_dian',descripcion_motivo_dian = '$descripcion_motivo_dian'": "";

		//CONSULTAMOS SI EL DOCUMENTO YA TIENE O NO EL CONSECUTIVO
		if($consecutivoActual == "" || $consecutivoActual == null){
			$consecutivoDV = $consecutivoBD;
		}
		else{
			$consecutivoDV = $consecutivoActual;
		}

		//DESPUES DE QUE SE HA FILTRADO Y REALIZADO EL PROCEDIMIENTO PARA CADA CASO, ACTUALIZAMOS LA NOTA A ESTADO 1 PARA DARLA POR TERMINADA Y BLOQUEADA
		$sqlUpdateNota = "UPDATE
												$tablaPrincipal
											SET
												consecutivo = $consecutivoDV,
												estado = 1,
												total_nota_sin_abono = '$saldoGlobalNotaSinAbono',
												fecha_finalizacion = NOW(),
												hora_finalizacion = NOW()
												$camposDian
											WHERE
												id = '$id'
											AND
												id_empresa = '$id_empresa'";

		$queryUpdateNota = mysql_query($sqlUpdateNota,$link);
		if (!$queryUpdateNota) {
			echo '<script>
					alert("Error!\nSe genero todo el proceso pero no se pudo cerrar la nota!\nSi el problema persite comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else{
			if($opcGrillaContable=='DevolucionVenta' && $documento_venta == 'Remision'){ $tipoDocumento = 'NDRV'; }
			else if($opcGrillaContable=='DevolucionVenta' && $documento_venta == 'Factura'){ $tipoDocumento = 'NDFV'; }
			else{ $tipoDocumento = 'NDFC'; }

			$sqlUpdate   = "UPDATE asientos_colgaap SET consecutivo_documento='$consecutivoDV' WHERE id_documento='$id' AND tipo_documento='$tipoDocumento' AND id_empresa='$id_empresa'";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			$sqlUpdate   = "UPDATE asientos_niif SET consecutivo_documento='$consecutivoDV' WHERE id_documento='$id' AND tipo_documento='$tipoDocumento' AND id_empresa='$id_empresa'";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			echo'<script>
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$labelConsecutivo.$consecutivoDV.'";

					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "notas_inventario/notas_devolucion/bd/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_nota 		  : "'.$id.'",
							filtro_bodega     : "'.$idBodega.'"
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR LA CANTIDAD DE LOS SALDOS TOTALES =======================================================//
	function actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,$tablaCargaInventario,$accion,$link){

		$cont=0;
		//CONSULTAMOS LOS ARTICULOS DE LA NOTA, PARA RECORRERLOS Y RESTARLOS DE LOS SALDOS DEL DOCUMENTO DE CARGA
		$sql   = "SELECT id_fila_cargada,cantidad FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND activo=1 ";
		$query = mysql_query($sql,$link);

		if ($accion=='agregar') {
			while ($row=mysql_fetch_array($query)) {
				$updateSaldos      = "UPDATE $tablaCargaInventario SET saldo_cantidad= saldo_cantidad + ".$row['cantidad']." WHERE id='".$row['id_fila_cargada']."'";
				$queryUpdateSaldos = mysql_query($updateSaldos,$link);

				if (!$queryUpdateSaldos) { $cont++; }
			}
		}
		else if ($accion=='eliminar'){
			while ($row=mysql_fetch_array($query)) {
				$updateSaldos      = "UPDATE $tablaCargaInventario SET saldo_cantidad= saldo_cantidad - ".$row['cantidad']." WHERE id='".$row['id_fila_cargada']."'";
				$queryUpdateSaldos = mysql_query($updateSaldos,$link);

				if (!$queryUpdateSaldos) { $cont++; }
			}
		}
		return $cont;
	}

	//=========================== FUNCION PARA VALIDAR LA DISPONIBILIDAD DEL DOCUMENTO ============================================================//
	//VALIDA EN QUE ESTADO SE ENCUENTRA EL DOCUMENTO QUE SE VA A CARGAR, SI ESTA DISPONIBLE  Y ESTA GENERADO
	function verificarDocumento($tablaCarga,$idTablaCarga,$idDocumentoCarga,$id_empresa,$link){
		$sqlDocumentoCargado   = "SELECT estado FROM $tablaCarga WHERE id='$idDocumentoCarga' AND activo=1 AND  id_empresa='$id_empresa'";
		$queryDocumentoCargado = mysql_query($sqlDocumentoCargado,$link);
		$estado                = mysql_result($queryDocumentoCargado,0,'estado');

		if ($estado=='0') { echo '<script>alert("Error!\nEl documento cargado no esta generado!\nVerifiquelo y vuelva a intentarlo");</script>'; exit; }
		elseif ($estado=='3') { echo '<script>alert("Error!\nEl documento cargado ha sido eliminado!\nVerifiquelo y vuelva a intentarlo");</script>'; exit; }

		//VALIDAR LOS SALDOS ANTES DE GENERAR LA NOTA
		$sqlSaldos    = "SELECT COUNT(id) AS saldo FROM ".$tablaCarga."_inventario WHERE $idTablaCarga='".$idDocumentoCarga."' AND saldo_cantidad>0";
		$querySaldos  = mysql_query($sqlSaldos,$link);
		$mensajeSaldo =  '<script>
												alert("Aviso!\nNo se puede Generar la nota, esto se debe a que.\nYa no tiene articulos disponibles para realizar una devolucion");
												document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
											</script>';

		$saldo=mysql_result($querySaldos,0,'saldo');
		if ($saldo<1) { echo $mensajeSaldo; exit; }
	}

	//=========================== MUEVE CUENTAS AL GENERAR UNA NOTA EN COMPRA O VENTAS =======================================//
	//ESTA FUNCION MUEVE LAS CUENTAS DE LOS DOCUMENTO, SI LA VARIABLE ACCION = AGREGAR ENTONCES SE VA A CONTABILIZAR UN DOCUMENTO, SI ES = A ELIMINAR, ENTONCES SE VA A
	//DESCONTABILIZAR UN DOCUMENTO.
	function moverCuentasDocumento($idDocumento,$id_sucursal,$opcGrillaContable,$accion,$opcCargar,$tablaInventario,$idTablaPrincipal,$link,$idDocumentoCarga=0){
		$id_empresa = $_SESSION['EMPRESA'];

		if ($opcGrillaContable == 'DevolucionCompra' && $accion == 'descontabilizar') {
			$sqlDescontabilizar   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND tipo_documento='NDFC' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
			$queryDescontabilizar = mysql_query($sqlDescontabilizar,$link);

			$sqlDescontabilizar   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND tipo_documento='NDFC' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
			$queryDescontabilizar = mysql_query($sqlDescontabilizar,$link);

			if (!$queryDescontabilizar) { echo '<script>alert("Error!\nNo se descontabilizo el documento!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }

		}
		//EL DOCUMENTO ES UNA REMISION
		else if ($opcGrillaContable=='DevolucionVenta' && $accion=='descontabilizar') {
			$tipoDocumento        = ($opcCargar=='remisionVenta')? 'NDRV': 'NDFV';
			$sqlDescontabilizar   = "DELETE FROM asientos_colgaap WHERE id_documento='$idDocumento' AND tipo_documento='$tipoDocumento' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
			$queryDescontabilizar = mysql_query($sqlDescontabilizar,$link);

			$sqlDescontabilizar   = "DELETE FROM asientos_niif WHERE id_documento='$idDocumento' AND tipo_documento='$tipoDocumento' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
			$queryDescontabilizar = mysql_query($sqlDescontabilizar,$link);

			if (!$queryDescontabilizar) { echo '<script>alert("Error!\nNo se descontabilizo el documento!\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }

		}
	}

	//=========================== FUNCION ACTUALIZAR LA CANTIDAD DE ARTICULOS ==============================================================================//
	function actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,$opc,$link,$idDocumentoCarga=''){

		$arrayActivosFijos;

		$cont                    = 0;
		$opcActivoFijoEliminar   = 0;
		$opcActivoFijoAgregar    = 0;
		$valueInsertActivosFijos = "";
		$id_empresa              = $_SESSION['EMPRESA'];

		//CAMPOS DE LA CONSULTA DEPENDIENDO DE LA TABLA
		$campo_consulta = "$tablaInventario.opcion_contable,";
		$campo_costo    = "costo_inventario";

		if ($tablaInventario=='devoluciones_compra_inventario') {
			$campo_consulta = "";
			$campo_costo    = "costo_unitario";
		}

		//CONSULTAMOS TODOS LOS ARTICULOS DE LA FACTURA

		$sqlArticulos = "SELECT
							items.nombre_equipo,
							$tablaInventario.$campo_costo AS costos,
							$tablaInventario.cantidad,
							$tablaInventario.valor_impuesto,
							$tablaInventario.id_inventario,
							$campo_consulta
							$tablaInventario.check_opcion_contable,
							$tablaInventario.inventariable,
							$tablaInventario.id_fila_cargada
						FROM items INNER JOIN $tablaInventario ON items.id=$tablaInventario.id_inventario
						WHERE $tablaInventario.$idTablaPrincipal = '$id'";

		$queryArticulos=mysql_query($sqlArticulos,$link);

		//SI ES UNA FACTURA DE COMPRA CONSULTAMOS EL NUMERO DE FACTURA Y EL ID DEL PROVEEDOR, POR SI SE FACTURARON ACTIVOS FIJOS
		if ($tablaInventario=='devoluciones_compra_inventario') {
			$sqlCompra      = "SELECT numero_factura,id_proveedor FROM compras_facturas WHERE id='$idDocumentoCarga'";
			$queryCompra    = mysql_query($sqlCompra);
			$numero_factura = mysql_result($queryCompra,0,'numero_factura');
			$id_proveedor   = mysql_result($queryCompra,0,'id_proveedor');
		}

		//VERIFICAMOS SI SE VAN A DAR DE BAJA LOS ARTICULOS O SE VAN A RETORNAR AL INVENTARIO

		if ($opc=='eliminar') {

			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
				$costo_total = ($rowArticulos['cantidad'] * $rowArticulos['costos']);

				if ($rowArticulos['inventariable']=='true' && $rowArticulos['check_opcion_contable'] == '') {
					$sqlActuliza    = "UPDATE inventario_totales
										SET costos = IF(cantidad - $rowArticulos[cantidad] = 0, 0,((costos * cantidad) - $costo_total) / (cantidad - $rowArticulos[cantidad])),
											cantidad=cantidad-$rowArticulos[cantidad]
										WHERE id_item=$rowArticulos[id_inventario]
										AND id_sucursal='$id_sucursal'
										AND id_ubicacion='$idBodega'
										AND id_empresa='$id_empresa'
										AND inventariable='true'";
					$queryActualiza = mysql_query($sqlActuliza,$link);
					if (!$queryActualiza) {$cont++; }
				}
				//SI ES ACTIVO FIJO, SE ELIMINAN LOS ACTIVOS FIJO RELACIONADOS CON EL DOCUMENTO
				else if ($rowArticulos['check_opcion_contable']=='activo_fijo') {
					$opcActivoFijoEliminar++;
					$arrayActivosFijos[$rowArticulos['id_inventario']]+=$rowArticulos['cantidad'];
				}
			}
		}
		else if ($opc=='agregar') {

			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
				$costo_total = ($rowArticulos['cantidad'] * $rowArticulos['costos']);

				if ($rowArticulos['inventariable']=='true' && $rowArticulos['check_opcion_contable'] == '') {
					$sqlActuliza    = "UPDATE inventario_totales
										SET costos = ((costos * cantidad) + $costo_total) / (cantidad + $rowArticulos[cantidad]),
											cantidad = cantidad+$rowArticulos[cantidad]
										WHERE id_item=$rowArticulos[id_inventario]
											AND id_sucursal='$id_sucursal'
											AND id_ubicacion='$idBodega'
											AND id_empresa='$id_empresa'
											AND inventariable='true'";

					$queryActualiza = mysql_query($sqlActuliza,$link);
					if (!$queryActualiza){ $cont++; }
				}
				//SI ES ACTIVO FIJO, SE ELIMINAN LOS ACTIVOS FIJO RELACIONADOS CON EL DOCUMENTO
				else if ($rowArticulos['check_opcion_contable']=='activo_fijo') {
					$opcActivoFijoAgregar++;
					$numero      = $rowArticulos['cantidad'];
					$costoActivo = ($rowArticulos['valor_impuesto'] > 0)? $rowArticulos['costos'] + ($rowArticulos['costos'] * $rowArticulos['valor_impuesto'] /100): $rowArticulos['costos'];

					while ($numero>0) {
						$valueInsertActivosFijos.="('$id_empresa',
												'$id_sucursal',
												'$idBodega',
												'".$rowArticulos['id_inventario']."',
												'".$rowArticulos['nombre_equipo']."',
												'$idDocumentoCarga',
												'".$rowArticulos['id_fila_cargada']."',
												'FC',
												'$numero_factura',
												'".$costoActivo."',
												'$id_proveedor'),";
						$numero--;
					}
				}
			}
		}
		else if ($opc=='costosArticulosVenta') {
			while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
				$cont+=($rowArticulos['costos']*$rowArticulos['cantidad']);
			}
		}

		$whereActivosFijos = "";

		//ELIMINAR LOS ACTIVOS FIJOS SI LOS TIENE DEL DOCUMENTO CARGADO
		if ($opcActivoFijoEliminar>0) {

			$sqlActivosFijos  = "SELECT id,id_item FROM activos_fijos WHERE id_documento_referencia='$idDocumentoCarga' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ORDER BY estado ASC";
			$queryActivosFijo = mysql_query($sqlActivosFijos,$link);
			while ($rowActivosFijos=mysql_fetch_array($queryActivosFijo)) {

				if ($arrayActivosFijos[$rowActivosFijos['id_item']]>0) {

					$whereActivosFijos .= ($whereActivosFijos != '')? ' OR ' : '' ;
					$whereActivosFijos .= "id=".$rowActivosFijos['id'];

					echo '<br>id: '.$rowActivosFijos['id'];

					$arrayActivosFijos[$rowActivosFijos['id_item']]--;
				}
			}

			$sql   = "DELETE FROM activos_fijos WHERE id_documento_referencia='$idDocumentoCarga' AND ($whereActivosFijos) ";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se eliminaron los activos fijos asociados a este documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			}

		}
		else if ($opcActivoFijoAgregar>0) {

			$valueInsertActivosFijos = substr($valueInsertActivosFijos,0,-1);
			$sqlInsertActivoFijo     = "INSERT INTO activos_fijos (
											id_empresa,
											id_sucursal,
											id_bodega,
											id_item,
											nombre_equipo,
											id_documento_referencia,
											id_documento_referencia_inventario,
											documento_referencia,
											documento_referencia_consecutivo,
											costo,
											id_proveedor)
										VALUES $valueInsertActivosFijos";
			$queryInsertActivoFijo = mysql_query($sqlInsertActivoFijo,$link);
		}

		return $cont;
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	// AL EDITAR UNA FACTURA - REMISION YA GENERADA, SE DESCONTABILIZA, ES DECIR SE ELIMINAN LOS REGISTROS CONTABLES QUE GENERO (SOLO LA FACTURA), Y DEVOLVEMOS LOS ARTICULOS
	// AL INVENTARIO, ADEMAS CAMBIAMOS SU ESTADO A CERO, QUEDANDO LA FACTURA COMO SI SE HUBIERA CREADO PERO NO SE HUBIERA TERMINADO
	// ESTO SOLO SE CUMPLE SI LA FACTURA NO ESTA DENTRO DE UN CIERRE, SI ES ASI NO SE PODRA MODIFICAR EN NINGUNA MANERA
	function modificarDocumentoGenerado($idDocumento,$opcGrillaContable,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$opcCargar,$carpeta,$link,$idDocumentoCarga=''){
		//VERIFICAMOS QUE DOCUMENTO SE VA A EDITAR PARA GENERAR TODOS LOS PROCEDIMIENTOS NECESARIOS
		/************************** DEVOLCUION DE COMPRA **************************/
		if($opcGrillaContable == 'DevolucionCompra'){
			//ACTUALIZAMOS LA CANTIDAD DE LOS ARTICULOS, LOS SACAMOS DEL INVENTARIO
				$res=actualizaCantidadArticulos($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,'agregar',$link,$idDocumentoCarga);
				if ($res>0) {
					//SI NO SE ACTUALIZARON LOS ARTICULOS, CANCELAMOS LOS DEMAS PROCESOS
					echo '<script>
							alert("Error!\nNo se actulizo la cantidad de los articulos del inventario\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					return;
				}
				//ACTUALIZAMOS LAS CANTIDADES EN SALDOS TOTALES DE LA REMISION
				$res1=actualizaSaldoArticulos($idDocumento,$tablaInventario,$idTablaPrincipal,'compras_facturas_inventario','agregar',$link);
				if ($res1>0) {
					echo '<script>
							alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
				//ELIMINAMOS LOS ASIENTOS CONTABLES QUE SE GENERARON A PARTIR DE LA NOTA
				moverCuentasDocumento($idDocumento,$id_sucursal,$opcGrillaContable,'descontabilizar',$opcCargar,$tablaInventario,$idTablaPrincipal,$link);

				//AGREGAR VALOR SALDO SIN ABONO A LA FACTURA CUANDO SE CANCELA LA NOTA
				$updateTotalAbonoFactura = "UPDATE compras_facturas AS CF, devoluciones_compra AS DC
											SET CF.total_factura_sin_abono = (CF.total_factura_sin_abono + DC.total_nota_sin_abono)
											WHERE CF.activo=1
												AND CF.id = DC.id_documento_compra
												AND CF.id_empresa ='$id_empresa'
												AND DC.id = '$idDocumento'
												AND DC.id_empresa ='$id_empresa'";
				$queryTotalAbonoFactura = mysql_query($updateTotalAbonoFactura,$link);

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						       VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','NDFC','Nota Devolucion Factura Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);

				//LUEGO DE APLICAR LOS CAMBIOS CON LAS CONDICIONES, PROCEDEMOS A ACTUALIZAR EL ESTADO DE LA NOTA A CERO, PARA QUE QUEDE COMO SI SE HUBIERA CARGADO PERO NO GENERADO
				$sqlUpdateNota   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_empresa='$id_empresa'";
				$queryUpdateNota = mysql_query($sqlUpdateNota,$link);
				if($queryUpdateNota){
						echo'<script>
							 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
									url     : "'.$carpeta.'/grillaContable.php",
									scripts : true,
									nocache : true,
									params  :
									{
										filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
										opcGrillaContable : "'.$opcGrillaContable.'",
										id_nota           : "'.$idDocumento.'"
									}
								});

								Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
					}
					else{
						echo '<script>
								alert("Error!\nSe proceso la nota pero no se actualizo!\nPor favor elimine esta nota y vuelva a crearla\nSi el problema persiste comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						return;
					}

		}
		/*************************** DEVOLUCION DE VENTA **************************/
		else if($opcGrillaContable == 'DevolucionVenta'){
			//EDITAR UNA NOTA TIPO REMISION
			if ($opcCargar == 'remisionVenta'){

				$res = actualizaCantidadArticulos($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
				if ($res > 0) {
					echo '<script>
							alert("Error!\nNo se actulizo la cantidad de los articulos del inventario\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					return;
				}

				$res = actualizaSaldoArticulos($idDocumento,$tablaInventario,$idTablaPrincipal,'ventas_remisiones_inventario','agregar',$link);
				if ($res > 0) {
					echo '<script>
							alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				moverCuentasDocumento($idDocumento,$id_sucursal,$opcGrillaContable,'descontabilizar',$opcCargar,$tablaInventario,$idTablaPrincipal,$link);

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','NDRV','Nota Devolucion Remision Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);

			}
			//EDITAR UNA NOTA TIPO FACTURA
			else if($opcCargar == 'facturaVenta'){
				//SI LA DEVOLUCION YA SE ENVIO A LA DIAN NO SERA POSIBLE EDITARLA
				$sqlEstadoDevolucionVenta 	= "SELECT response_DE FROM devoluciones_venta WHERE id = $idDocumento AND id_empresa = $id_empresa";
				$queryEstadoDevolucionVenta = mysql_query($sqlEstadoDevolucionVenta,$link);
				$response_DE 								= mysql_result($queryEstadoDevolucionVenta,0,'response_DE');

				if($response_DE != "Ejemplar recibido exitosamente pasara a verificacion"){
					$res = actualizaCantidadArticulos($idDocumento,$id_sucursal,$id_bodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
					if($res > 0) {
						echo '<script>
								alert("Error!\nNo se actulizo la cantidad de los articulos del inventario\nSi el problema persiste comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						return;
					}

					$res = actualizaSaldoArticulos($idDocumento,$tablaInventario,$idTablaPrincipal,'ventas_facturas_inventario','agregar',$link);
					if ($res > 0) {
						echo '<script>
								alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						exit;
					}

					//ELIMINAMOS LOS ASIENTOS CONTABLES QUE SE GENERARON A PARTIR DE LA NOTA
					moverCuentasDocumento($idDocumento,$id_sucursal,$opcGrillaContable,'descontabilizar',$opcCargar,$tablaInventario,$idTablaPrincipal,$link);

					//AGREGAR VALOR SALDO SIN ABONO A LA FACTURA CUANDO SE CANCELA LA NOTA
					$updateTotalAbonoFactura = "UPDATE ventas_facturas AS VF, devoluciones_venta AS DV
												SET VF.total_factura_sin_abono = (VF.total_factura_sin_abono + DV.total_nota_sin_abono)
												WHERE VF.activo=1
													AND VF.id = DV.id_documento_venta
													AND VF.id_empresa ='$id_empresa'
													AND DV.id = '$idDocumento'
													AND DV.documento_venta = 'Factura'
													AND DV.id_empresa ='$id_empresa'";
					$queryTotalAbonoFactura = mysql_query($updateTotalAbonoFactura,$link);

					$fecha_actual = date('Y-m-d');
					$hora_actual  = date('H:i:s');

					//INSERTAR EL LOG DE EVENTOS
					$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','NDFV','Nota Devolucion Factura Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
					$queryLog = mysql_query($sqlLog,$link);

					//LUEGO DE APLICAR LOS CAMBIOS CON LAS CONDICIONES, PROCEDEMOS A ACTUALIZAR EL ESTADO DE LA NOTA A CERO, PARA QUE QUEDE COMO SI SE HUBIERA CARGADO PERO NO GENERADO
					$sqlUpdateNota   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND id_empresa='$id_empresa'";
					$queryUpdateNota = mysql_query($sqlUpdateNota,$link);
					if($queryUpdateNota){
						echo'<script>
							 	Ext.get("contenedor_'.$opcGrillaContable.'").load({
									url     : "'.$carpeta.'/grillaContable.php",
									scripts : true,
									nocache : true,
									params  :
									{
										filtro_bodega     : document.getElementById("filtro_ubicacion_'.$opcGrillaContable.'").value,
										opcGrillaContable : "'.$opcGrillaContable.'",
										id_nota           : "'.$idDocumento.'"
									}
								});

								Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
					}
					else{
						echo '<script>
								alert("Error!\nSe proceso la nota pero no se actualizo!\nPor favor elimine esta nota y vuelva a crearla\nSi el problema persiste comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						return;
					}
				}
				else if($response_DE == "Ejemplar recibido exitosamente pasara a verificacion"){
					echo "<script>
							alert('No se puede editar el documento porque ya se ha enviado a la DIAN');
							document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
						</script>";
				}
			}
		}
	}

	//===========================// FUNCION PARA BUSCAR EL VALOR DEL IVA DE UN ARTICULO //===========================//
	function buscarImpuestoArticulo($idInsert,$idFactura,$opcGrillaContable,$cont,$unidadMedida,$idArticulo,$codigo,$cantidad,$descuento,$costo,$nombreArticulo,$id_empresa,$link){

		$campo_factura    = ($opcGrillaContable == 'DevolucionVenta')? 'id_factura_venta': 'id_factura_compra';
		$tabla_inventario = ($opcGrillaContable == 'DevolucionVenta')? 'ventas_facturas_inventario': 'compras_facturas_inventario';

		$sqlImpuesto   = "SELECT id_impuesto,impuesto,valor_impuesto,tipo_descuento, saldo_cantidad
							FROM $tabla_inventario
							WHERE $campo_factura='$idFactura'
								AND id = '$idInsert'
							LIMIT 0,1";
		$queryImpuesto = mysql_query($sqlImpuesto,$link);

		$impuesto       = mysql_result($queryImpuesto, 0, 'impuesto');
		$id_impuesto    = mysql_result($queryImpuesto, 0, 'id_impuesto');
		$valor_impuesto = mysql_result($queryImpuesto, 0, 'valor_impuesto');
		$tipo_descuento = mysql_result($queryImpuesto, 0, 'tipo_descuento');
		$saldo_cantidad = mysql_result($queryImpuesto, 0, 'saldo_cantidad');

		$titulo = ($tipo_descuento == 'porcentaje')? 'En porcentaje':'En pesos';
		$script = "if(typeof(arrayIva".$opcGrillaContable."[$id_impuesto])=='undefined') {
						arrayIva".$opcGrillaContable."[$id_impuesto] = { nombre:'$impuesto', valor:'$valor_impuesto' };
				 	}
				 	cantidadesArticulos".$opcGrillaContable."[$cont]='$saldo_cantidad';";

		if ($queryImpuesto) {
			echo "<script>
						if(document.getElementById('idInsertArticulo".$opcGrillaContable."_'+".$cont.").value > 0){
			                document.getElementById('divImageDeshacer".$opcGrillaContable."_'+".$cont.").style.display = 'inline';
			                document.getElementById('divImageSave".$opcGrillaContable."_'+".$cont.").style.display     = 'inline';
			            }
			            else{ document.getElementById('divImageDeshacer".$opcGrillaContable."_'+".$cont.").style.display = 'none'; }

						document.getElementById('unidades".$opcGrillaContable."_'+".$cont.").value       = '$unidadMedida';
						document.getElementById('idArticulo".$opcGrillaContable."_'+".$cont.").value     = '$idArticulo';
						document.getElementById('eanArticulo".$opcGrillaContable."_'+".$cont.").value    = '$codigo';
						document.getElementById('cantArticulo".$opcGrillaContable."_'+".$cont.").value   = '$cantidad';
						document.getElementById('costoArticulo".$opcGrillaContable."_'+".$cont.").value  = '$costo';
						document.getElementById('nombreArticulo".$opcGrillaContable."_'+".$cont.").value = '$nombreArticulo';
						document.getElementById('ivaArticulo".$opcGrillaContable."_'+".$cont.").value    = '$id_impuesto';
						document.getElementById('descuentoArticulo".$opcGrillaContable."_'+".$cont.").value = '$descuento';
			            document.getElementById('idInsertNewFilaCargada".$opcGrillaContable."_'+".$cont.").value = '$idInsert';

			            document.getElementById('imgDescuentoArticulo".$opcGrillaContable."_'+".$cont.").setAttribute('src','img/".$tipo_descuento.".png');
			            document.getElementById('imgDescuentoArticulo".$opcGrillaContable."_'+".$cont.").setAttribute('title','".$titulo."');

			            $script
			       	</script>";
		}
		else { echo '<script>alert("Aviso,\nAviso No se logro consultar la informacion del iva perteneciente al Item")</script>'; exit; }
	}

	//=========================== FUNCION PARA GUARDAR UN ARTICULO DE LA GRILLA ==================================================================//
	function guardarArticulo($consecutivo,$id,$idNota,$cont,$idInventario,$codigo,$cantArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$accion,$opcCargar,$mysql){

		$camposIdNota = "id_devolucion_venta,";

		//VERIFICAMOS DE QUE ARTICULO SE VA A GUARDAR
		if ($opcCargar=='facturaCompra'){
			$camposIdNota         = "id_devolucion_compra,";

			$idTablaCarga         = 'id_factura_compra';
			$tablaCargaInventario = 'compras_facturas_inventario';
		}
		else if ($opcCargar=='remisionVenta') {
			$idTablaCarga         = 'id_remision_venta';
			$tablaCargaInventario = 'ventas_remisiones_inventario';
		}
		else if ($opcCargar=='facturaVenta') {
			$idTablaCarga         = 'id_factura_venta';
			$tablaCargaInventario = 'ventas_facturas_inventario';
		}

		$sqlArticulo = "SELECT *
						FROM $tablaCargaInventario
						WHERE activo = 1
							AND $idTablaCarga = '$id'
							AND codigo='$codigo'
							AND saldo_cantidad>0
							AND id NOT IN (
									SELECT id_fila_cargada
									FROM $tablaInventario
									WHERE $idTablaPrincipal='$idNota'
										AND activo = 1
										AND id_fila_cargada=$tablaCargaInventario.id
								)
						LIMIT 0,1";

		$queryArticulo = $mysql->query($sqlArticulo,$mysql->link);

		$id_registro            = $mysql->result($queryArticulo,0,'id');
		$id_inventario          = $mysql->result($queryArticulo,0,'id_inventario');
		$codigo                 = $mysql->result($queryArticulo,0,'codigo');
		$codigo_proveedor       = $mysql->result($queryArticulo,0,'codigo_proveedor');
		$nombre                 = $mysql->result($queryArticulo,0,'nombre');
		$cantidad               = $mysql->result($queryArticulo,0,'saldo_cantidad');
		$id_unidad_medida       = $mysql->result($queryArticulo,0,'id_unidad_medida');
		$nombre_unidad_medida   = $mysql->result($queryArticulo,0,'nombre_unidad_medida');
		$cantidad_unidad_medida = $mysql->result($queryArticulo,0,'cantidad_unidad_medida');
		$tipo_descuento         = $mysql->result($queryArticulo,0,'tipo_descuento');
		$descuento              = $mysql->result($queryArticulo,0,'descuento');
		$costo_unitario         = $mysql->result($queryArticulo,0,'costo_unitario');
		$id_impuesto            = $mysql->result($queryArticulo,0,'id_impuesto');
		$impuesto               = $mysql->result($queryArticulo,0,'impuesto');
		$valor_impuesto         = $mysql->result($queryArticulo,0,'valor_impuesto');
		$inventariable          = $mysql->result($queryArticulo,0,'inventariable');
		$costo_inventario       = $mysql->result($queryArticulo,0,'costo_inventario');
		$opcion_contable        = $mysql->result($queryArticulo,0,'check_opcion_contable');
		$centro_costo           = $mysql->result($queryArticulo,0,'id_centro_costos');

		$campoVenta = ($opcGrillaContable == 'DevolucionVenta')? "costo_inventario,": "";
		$valueVenta = ($opcGrillaContable == 'DevolucionVenta')? "'$costo_inventario',": "";

		$campoCompra = ($opcCargar=='facturaCompra')? "check_opcion_contable, id_centro_costos,": "";
		$valueCompra = ($opcCargar=='facturaCompra')? "'$opcion_contable', '$centro_costo',": "";

		if ($cantArticulo>$cantidad) {
			echo'<script>
					alert("Error!\nLa cantidad ingresada para este articulo supera la del documento cargado");
					(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$consecutivo.'"));
					document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
				</script>';
		}
		else{
			//INSERTAR EN LA TABLA INVENTARIO DE LA NOTA EL REGISTRO DEL ARTICULO CON TODOS LOS DATOS
        	$sqlInsert = "INSERT INTO $tablaInventario (
								$camposIdNota
				        		id_fila_cargada,
					        	id_inventario,
					        	codigo,
					        	id_unidad_medida,
					        	nombre_unidad_medida,
					        	cantidad_unidad_medida,
					        	nombre,
					        	cantidad,
								costo_unitario,
								tipo_descuento,
								descuento,
								id_impuesto,
								impuesto,
								valor_impuesto,
								$campoVenta
								$campoCompra
								inventariable)
						VALUES ('$idNota',
								'$id_registro',
								'$id_inventario',
								'$codigo',
								'$id_unidad_medida',
								'$nombre_unidad_medida',
								'$cantidad_unidad_medida',
								'$nombre',
								'$cantArticulo',
								'$costo_unitario',
								'$tipo_descuento',
								'$descuento',
								'$id_impuesto',
								'$impuesto',
								'$valor_impuesto',
								$valueVenta
								$valueCompra
								'$inventariable')";

			$queryInsert = $mysql->query($sqlInsert,$mysql->link);
			//$sqlLastId   = "SELECT LAST_INSERT_ID()";
			$lastId  = $mysql->insert_id();
			// $lastId      = mysql_result(mysql_query($sqlLastId,$link),0,0);

			if($lastId > 0){
				// SI EL ITEM A GUARDAR TIENE UN GRUPO, REALIZAR CALCULO DEL GRUPO Y MOVERLO AL DOM DEL GRUPO
				if ($opcCargar=='facturaVenta') { actualizaCamposGrupo('sumar',$idNota,$id,$id_registro,true,$id_empresa,$mysql); }

				echo'<script>
						document.getElementById("idInsertArticulo'.$opcGrillaContable.'_'.$cont.'").value            = '.$lastId.'

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").setAttribute("title","Actualizar Articulo");
						document.getElementById("imgSaveArticulo'.$opcGrillaContable.'_'.$cont.'").setAttribute("src","img/reload.png");

						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display        = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display    = "none";
						document.getElementById("descripcionArticulo'.$opcGrillaContable.'_'.$cont.'").style.display = "block";
						document.getElementById("deleteArticulo'.$opcGrillaContable.'_'.$cont.'").style.display      = "block";

						if ("'.$accion.'"=="agregar") {
           					calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantArticulo.','.$descuento.','.$costo_unitario.',"'.$accion.'","'.$tipo_descuento.'",'.$id_impuesto.','.$cont.');
           				}
           				Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();

					</script>'.cargaDivsInsertUnidades('echo',$consecutivo,$opcGrillaContable);
			}
			else{
				echo'<script>
						alert("Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema");
						(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'"));
					</script>';
			}
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaArticulo($id,$idNota,$codigo,$idInsertArticulo,$idInsertFilaCargada,$idInsertNewFilaCargada,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costoArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$opcCargar,$mysql){

		$camposIdNota = "id_devolucion_venta,";
		if ($opcCargar=='facturaCompra') {
			$camposIdNota         = "id_devolucion_compra,";

			$idTablaCarga         = 'id_factura_compra';
			$tablaCargaInventario = 'compras_facturas_inventario';
		}
		elseif ($opcCargar=='remisionVenta') {
			$idTablaCarga         = 'id_remision_venta';
			$tablaCargaInventario = 'ventas_remisiones_inventario';
		}
		elseif ($opcCargar=='facturaVenta') {
			$idTablaCarga         = 'id_factura_venta';
			$tablaCargaInventario = 'ventas_facturas_inventario';
		}

		//VALIDACION SALDO CANTIDAD
		$sqlArticulo = "SELECT *
						FROM $tablaCargaInventario
						WHERE activo = 1
							AND id='$idInsertNewFilaCargada'
							AND $idTablaCarga = '$id'
							AND codigo = '$codigo'
							AND saldo_cantidad>0
						LIMIT 0,1";

		$queryArticulo = $mysql->query($sqlArticulo,$mysql->link);
		$titulo        = ($tipo_descuento == 'porcentaje')? 'En porcentaje': 'En pesos';


		//INSERTAMOS EN LA GRILLA LOS DATOS DEL NUEVO ARTICULO Y LO ACTUALIZAMOS
		//CAMPOS DEL ARTICULO GUARDADO EN EL DOCUMENTO DE CARGA
		$id_registro            = $mysql->result($queryArticulo,0,'id');
		$id_inventario          = $mysql->result($queryArticulo,0,'id_inventario');
		$codigo                 = $mysql->result($queryArticulo,0,'codigo');
		$nombre                 = $mysql->result($queryArticulo,0,'nombre');
		$cantidad               = $mysql->result($queryArticulo,0,'saldo_cantidad');
		$id_unidad_medida       = $mysql->result($queryArticulo,0,'id_unidad_medida');
		$nombre_unidad_medida   = $mysql->result($queryArticulo,0,'nombre_unidad_medida');
		$cantidad_unidad_medida = $mysql->result($queryArticulo,0,'cantidad_unidad_medida');
		$tipo_descuento         = $mysql->result($queryArticulo,0,'tipo_descuento');
		$descuento              = $mysql->result($queryArticulo,0,'descuento');
		$costo_unitario         = $mysql->result($queryArticulo,0,'costo_unitario');
		$id_impuesto            = $mysql->result($queryArticulo,0,'id_impuesto');
		$impuesto               = $mysql->result($queryArticulo,0,'impuesto');
		$id_impuesto         	= $mysql->result($queryArticulo,0,'id_impuesto');
		$valor_impuesto         = $mysql->result($queryArticulo,0,'valor_impuesto');
		$inventariable          = $mysql->result($queryArticulo,0,'inventariable');
		$costo_inventario       = $mysql->result($queryArticulo,0,'costo_inventario');
		$opcion_contable        = $mysql->result($queryArticulo,0,'check_opcion_contable');
		$centro_costo           = $mysql->result($queryArticulo,0,'id_centro_costos');

		$setVenta  = ($opcGrillaContable == 'DevolucionVenta')? "costo_inventario = '$costo_inventario',": "";
		$setCompra = ($opcCargar=='facturaCompra')? "check_opcion_contable = '$opcion_contable', id_centro_costos = '$centro_costo',": "";

		if ($cantArticulo > $cantidad) { echo '<script>alert("Error!\nLa cantidad ingresada del articulo supera la cantidad disponible en el documento cargado");</script>'; exit; }
		else if($id_inventario > 0){ 		//LA CANTIDAD INGRESADA NO PUEDE SER MAYOR A SALDO CANTIDAD

			if ($cantArticulo>$cantidad) {
				echo '<script>
						alert("Error!\nLa cantidad ingresada es mayor a la disponible\nSolo hay '.$cantidad.' disponible(s)");
						document.getElementById("cantArticulo'.$opcGrillaContable.'_'.$cont.'").focus();
					</script>';
				exit;
			}

			//LUEGO DE LAS CODICIONES, CONSULTAMOS EL ARTICULO ANTERIOR PARA RECALCULAR LOS TOTALES DE LA NOTA
			$sqlArticuloAnterior   = "SELECT cantidad,tipo_descuento,descuento,costo_unitario,id_impuesto, valor_impuesto FROM $tablaInventario WHERE id='$idInsertArticulo' AND $idTablaPrincipal='$idNota'";
			$queryArticuloAnterior = $mysql->query($sqlArticuloAnterior,$mysql->link);

			$cantidadDB       = $mysql->result($queryArticuloAnterior,0,'cantidad');
			$tipo_descuentoDB = $mysql->result($queryArticuloAnterior,0,'tipo_descuento');
			$descuentoDB      = $mysql->result($queryArticuloAnterior,0,'descuento');
			$costo_unitarioDB = $mysql->result($queryArticuloAnterior,0,'costo_unitario');
			$id_impuestoDB 	  = $mysql->result($queryArticuloAnterior,0,'id_impuesto');
			$valor_impuestoDB = $mysql->result($queryArticuloAnterior,0,'valor_impuesto');

			//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
			$calcRestaTotal = 'calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantidadDB.',"'.$descuentoDB.'",'.$costo_unitarioDB.',"eliminar","'.$tipo_descuentoDB.'","'.$id_impuestoDB.'",'.$cont.');';
			if ($opcCargar=='facturaVenta') { actualizaCamposGrupo('restar',$idNota,$id,$id_registro,false,$id_empresa,$mysql); }

			//ACTUALIZAR EN LA TABLA INVENTARIO DE LA NOTA EL REGISTRO DEL ARTICULO CON TODOS LOS DATOS
			$sqlUpdateArticulo= "UPDATE $tablaInventario
								SET id_fila_cargada='$id_registro',
									id_inventario='$id_inventario',
								 	codigo='$codigo',
									id_unidad_medida='$id_unidad_medida',
									nombre_unidad_medida='$nombre_unidad_medida',
									cantidad_unidad_medida='$cantidad_unidad_medida',
									nombre='$nombre',
									cantidad='$cantArticulo',
									costo_unitario='$costoArticulo',
									tipo_descuento='$tipo_descuento',
									descuento='$descuento',
									id_impuesto='$id_impuesto',
									impuesto='$impuesto',
									valor_impuesto='$valor_impuesto',
									$setVenta
									$setCompra
									inventariable= '$inventariable'
								WHERE $idTablaPrincipal=$idNota
									AND id=$idInsertArticulo";
			$queryUpdateArticulo = $mysql->query($sqlUpdateArticulo,$mysql->link);

			if ($queryUpdateArticulo) {
				if ($opcCargar=='facturaVenta') { actualizaCamposGrupo('sumar',$idNota,$id,$id_registro,false,$id_empresa,$mysql); }

				echo'<script>
						'.$calcRestaTotal.'
						document.getElementById("divImageSave'.$opcGrillaContable.'_'.$cont.'").style.display     = "none";
						document.getElementById("divImageDeshacer'.$opcGrillaContable.'_'.$cont.'").style.display = "none";

						calcTotalDocCompraVenta'.$opcGrillaContable.'('.$cantArticulo.',"'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipo_descuento.'","'.$id_impuesto.'",'.$cont.');
					</script>';
			}
			else{ echo '<script> alert("Aviso,\nNo se actualizo el articulo");</script>'; return; }
		}
		else{ echo'<script>alert("Aviso\nEl articulo No esta Disponible!");</script>'; }
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ===============================================//
	function deleteArticulo($cont,$id,$id_doc_cruce,$idArticulo,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$idInsertFilaCargada,$opcCargar,$mysql){
		if ($opcCargar=='facturaVenta') { actualizaCamposGrupo('restar',$id,$id_doc_cruce,$idInsertFilaCargada,false,$_SESSION['EMPRESA'],$mysql); }
		$sqlDelete   = "DELETE FROM $tablaInventario WHERE $idTablaPrincipal='$id' AND id='$idArticulo'";
		$queryDelete = $mysql->query($sqlDelete,$mysql->link);
		if(!$queryDelete){ echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>'; }
		else{
			echo '<script>(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulos'.$opcGrillaContable.'_'.$cont.'")); </script>';
		}
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION INDIVIDUAL POR ARTICULO =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$id_empresa,$link){

		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE $tablaPrincipal SET observacion='$observacion' WHERE id='$id' AND id_empresa='$id_empresa'";
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);
		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	//=========================== FUNCION PARA VERIFICAR LA CANTIDAD EXISTENTE DEL ARTICULO  ======================================================//
	function verificaCantidadArticulo($id,$id_empresa,$id_sucursal,$filtro_bodega,$link){
		$sql   = "SELECT cantidad FROM inventario_totales WHERE id_item='$id' AND id_sucursal='$id_sucursal' AND id_ubicacion='$filtro_bodega' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);

		if (!$query) { echo 'false'; }
		else { echo mysql_result($query,0,'cantidad'); }
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_sucursal,$idBodega,$id_empresa,$opcCargar,$link,$idDocumentoCarga=''){

		$sqlConsecutivo   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryConsecutivo = mysql_query($sqlConsecutivo,$link);
		$consecutivo      = mysql_result($queryConsecutivo, 0, "consecutivo");

		//IDENTIFICAMOS LA NOTA A CANCELAR
		if ($opcGrillaContable=='DevolucionCompra') {
			//VERIFICAR SI EL DOCUMENTO ESTA GENERADO
			if (verificarDocumentoGenerado($tablaPrincipal,$id,$id_empresa,$link)=='true') {
				//SI ESTA GENERADO, ENTONCES SE DEBEN MOVER LAS CUENTAS, Y ALTERAR EL INVENTARIO
				$res=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'agregar',$link,$idDocumentoCarga);
				if ($res>0) {
					//SI NO SE ACTUALIZARON LOS ARTICULOS, CANCELAMOS LOS DEMAS PROCESOS
					echo '<script>
							alert("Error!\nNo se actualizo la cantidad de los articulos del inventario\nSi el problema persiste comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					return;
				}
				//ACTUALIZAMOS LAS CANTIDADES EN SALDOS TOTALES DE LA REMISION
				$res1=actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,'compras_facturas_inventario','agregar',$link);
				if ($res1>0) {
					echo '<script>
							alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}

				//AGREGAR VALOR SALDO SIN ABONO A LA FACTURA CUANDO SE CANCELA LA NOTA
				$updateTotalAbonoFactura = "UPDATE compras_facturas AS CF, devoluciones_compra AS DC
											SET CF.total_factura_sin_abono = (CF.total_factura_sin_abono + DC.total_nota_sin_abono)
											WHERE CF.activo=1
												AND CF.id = DC.id_documento_compra
												AND CF.id_empresa ='$id_empresa'
												AND DC.id = '$id'
												AND DC.id_empresa ='$id_empresa'";
				$queryTotalAbonoFactura = mysql_query($updateTotalAbonoFactura,$link);

				//ELIMINAMOS LOS ASIENTOS CONTABLES QUE SE GENERARON A PARTIR DE LA NOTA
				moverCuentasDocumento($id,$id_sucursal,$opcGrillaContable,'descontabilizar',$opcCargar,$tablaInventario,$idTablaPrincipal,$link);

				$sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa'";
			}
			else if($consecutivo > 0){ $sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa'"; }
			else{ $sql="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_empresa='$id_empresa'"; }

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					       VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','NDFC','Nota Devolucion Factura Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		}
		else if ($opcGrillaContable=='DevolucionVenta') {
			//SI ES UNA NOTA DE REMISION DE VENTA
			if ($opcCargar=='remisionVenta') {
				//VERIFICAR SI EL DOCUMENTO ESTA GENERADO
				if (verificarDocumentoGenerado($tablaPrincipal,$id,$id_empresa,$link)=='true') {
					//SI ESTA GENERADO, ENTONCES SE DEBEN MOVER LAS CUENTAS, Y ALTERAR EL INVENTARIO
					//PRIMERO SE ACTUALIZAN LAS CANTIDADES DE EL INVENTARIO
					$res=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
					if ($res>0) {
						echo '<script>
								alert("Error!\nNo se actualizo el inventario!\nSi el problema persite comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						return;
					}
					//ACTUALIZAMOS LAS CANTIDADES EN SALDOS TOTALES DE LA REMISION
					$res1=actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,'ventas_remisiones_inventario','agregar',$link);
					if ($res1>0) {
						echo '<script>
								alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						exit;
					}
					//ELIMINAMOS LOS ASIENTOS QUE CREO ESA NOTA CUANDO SE GENERO
					moverCuentasDocumento($id,$id_sucursal,$opcGrillaContable,'descontabilizar',$opcCargar,$tablaInventario,$idTablaPrincipal,$link);

					$sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa'";
				}
				else if($consecutivo > 0){ $sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa'"; }

				else{ $sql="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_empresa='$id_empresa'"; }

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','NDRV','Nota Devolucion Remision Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			}
			//SI ES UNA NOTA DE FACTURA DE VENTA
			else if ($opcCargar=='facturaVenta') {
				//SI LA DEVOLUCION YA SE ENVIO A LA DIAN NO SERA POSIBLE CANCELARLA
				$sqlEstadoDevolucionVenta 	= "SELECT response_DE FROM devoluciones_venta WHERE id = $id AND id_empresa = $id_empresa";
				$queryEstadoDevolucionVenta = mysql_query($sqlEstadoDevolucionVenta,$link);
				$response_DE 								= mysql_result($queryEstadoDevolucionVenta,0,'response_DE');

				if($response_DE != "Ejemplar recibido exitosamente pasara a verificacion"){
					$varEstadoDoc = verificarDocumentoGenerado($tablaPrincipal,$id,$id_empresa,$link);

					if ($varEstadoDoc=='true') {
						//SI ESTA GENERADO, ENTONCES SE DEBEN MOVER LAS CUENTAS, Y ALTERAR EL INVENTARIO
						//ACTUALIZAMOS LA CANTIDAD DE LOS ARTICULOS, LOS SACAMOS DEL INVENTARIO
						$res=actualizaCantidadArticulos($id,$id_sucursal,$idBodega,$tablaInventario,$idTablaPrincipal,'eliminar',$link);
						if ($res>0) {
							//SI NO SE ACTUALIZARON LOS ARTICULOS, CANCELAMOS LOS DEMAS PROCESOS
							echo '<script>
									alert("Error!\nNo se actualizo la cantidad de los articulos del inventario\nSi el problema persiste comuniquese con el administrador del sistema");
									document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							return;
						}
						//ACTUALIZAMOS LAS CANTIDADES EN SALDOS TOTALES DE LA REMISION
						$res1=actualizaSaldoArticulos($id,$tablaInventario,$idTablaPrincipal,'ventas_facturas_inventario','agregar',$link);
						if ($res1>0) {
							echo '<script>
									alert("Error!\nAlgunos articulos no regresaron al documento!\nSi el problema persite comuniquese con el administrador del sistema");
									document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
								</script>';
							exit;
						}

						//AGREGAR VALOR SALDO SIN ABONO A LA FACTURA CUANDO SE CANCELA LA NOTA
						$updateTotalAbonoFactura = "UPDATE ventas_facturas AS VF, devoluciones_venta AS DV
													SET VF.total_factura_sin_abono = (VF.total_factura_sin_abono + DV.total_nota_sin_abono)
													WHERE VF.activo=1
														AND VF.id = DV.id_documento_venta
														AND VF.id_empresa ='$id_empresa'
														AND DV.id = '$id'
														AND DV.documento_venta = 'Factura'
														AND DV.id_empresa ='$id_empresa'";
						$queryTotalAbonoFactura = mysql_query($updateTotalAbonoFactura,$link);

						//ELIMINAMOS LOS ASIENTOS CONTABLES QUE SE GENERARON A PARTIR DE LA NOTA
						moverCuentasDocumento($id,$id_sucursal,$opcGrillaContable,'descontabilizar',$opcCargar,$tablaInventario,$idTablaPrincipal,$link);

						$sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa'";
					}

					else if($consecutivo > 0){ $sql="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_empresa='$id_empresa'"; }

					else{ $sql="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_empresa='$id_empresa'"; }

					$fecha_actual = date('Y-m-d');
					$hora_actual  = date('H:i:s');

					//INSERTAR EL LOG DE EVENTOS
					$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							       VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','NDFV','Nota Devolucion Factura Venta',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				}
				else if($response_DE == "Ejemplar recibido exitosamente pasara a verificacion"){
					echo "<script>
							alert('No se puede cancelar el documento porque ya se ha enviado a la DIAN');
							document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
						</script>";
				}
			}
		}

		//UNA VEZ EN ESTE PUNTO ACTUALIZAMNOS EL ACTIVO DE LA NOTA A 0
		$query=mysql_query($sql,$link);

		if ($query) {
			$queryLog=mysql_query($sqlLog,$link);
			echo '<script>
							Ext.get("contenedor_'.$opcGrillaContable.'").load({
                url     : "notas_inventario/notas_devolucion/default.php",
                scripts : true,
                nocache : true,
                params  : {
														filtro_bodega     : "'.$idBodega.'",
														opcGrillaContable : "'.$opcGrillaContable.'"
			                    }
			        });
							// document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
		}
		else{
			echo '<script>alert("Error!\nSe proceso la nota pero no se cancelo!\nSi el problema persite comuniquese con el administrador del sistema");</script>'; return;
		}
	}

 	//============================ FUNCION PARA VERIFICAR SI UNA NOTA ESTA GENERADA O NO ==============================================================//
 	function verificarDocumentoGenerado($tablaPrincipal,$idNota,$id_empresa,$link){
		$sql    = "SELECT estado FROM $tablaPrincipal WHERE id='$idNota' AND id_empresa='$id_empresa'";
		$query  = mysql_query($sql,$link);
		$estado = mysql_result($query,0,'estado');

 		if ($estado=='1') { return 'true'; }
 		else{ return 'false'; }
 	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrillaContable,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){
 		//ACTUALIZAR LA NOTA A ESTADO 0
		$sqlUpdate="UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa'";
		$queryUpdate=mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			$sqlConsulDoc="SELECT consecutivo FROM $tablaPrincipal WHERE activo = 1 AND id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa='$id_empresa' ";

			//VERIFICAR SI FUE GENERADO ANTES DE CANCELAR
			$queryConsulDoc = mysql_query($sqlConsulDoc,$link);
			$consecutivo    = mysql_result($queryConsulDoc,0,'consecutivo');

			echo '
				 <script>
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "'.$carpeta.'/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota  : "'.$idDocumento.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							filtro_bodega     : "'.$idBodega.'"
						}
					});
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = titulo+"<br>N. "+"'.$consecutivo.'";
				</script>';

		}else{
			echo '<script>alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			return;
		}

 	}

	//=========================== FUNCION PARA ACTUALIZAR LA FECHA DE LA NOTA =========================================================================//
 	function actualizarFechaNota($id,$fecha,$tablaPrincipal,$link){
		$sql   = "UPDATE $tablaPrincipal SET fecha_registro='$fecha' WHERE id='$id' ";
		$query = mysql_query($sql,$link);

 		if (!$query) {
			echo '<script>alert("Error!\nNo se actualizo la fecha, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
		}
 	}

	//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$tablaPrincipal,$link){
		$sql   = "SELECT estado,consecutivo,id_bodega FROM $tablaPrincipal WHERE id=$id_documento";
		$query = mysql_query($sql,$link);

		$estado      = mysql_result($query,0,'estado');
		$id_bodega   = mysql_result($query,0,'id_bodega');
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

		if($opcGrillaContable=='DevolucionVenta') {
			$labelConsecutivo = "Devolucion de Venta <br>N.";
		}
		else{
			$labelConsecutivo = "Devolucion de Compra <br>N.";
		}

		if ($estado>0) {
			echo'<script>

					alert("'.$mensaje.'");

					if (document.getElementById("Win_Ventana_descripcion_Articulo_factura")) {
						Win_Ventana_descripcion_Articulo_factura.close();
					}

					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$labelConsecutivo.$consecutivo.'";

					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "notas_inventario/notas_devolucion/bd/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_nota 		  : "'.$id_documento.'",
							filtro_bodega     : "'.$id_bodega.'"
						}
					});

					</script>';
			exit;
		}

	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$campoFecha,$tablaPrincipal,$id_empresa,$link,$opcCargar){
		// CONSULTAR EL DOCUMENTO
		$whereId = ($opcCargar=='facturaVenta') ? "id=$id_documento" : "numero_factura_completo=$id_documento"; 
		$sql= "SELECT $campoFecha AS fecha FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND $whereId";
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

	// ACTUALIZAR EL COSTO,IMPUESTO Y DESCUENTO DE UN GRUPO PARA LA DEVOLUCION
	function actualizaCamposGrupo($accion,$id_documento,$id_factura,$id_inventario,$mueve_fila,$id_empresa,$mysql){
		// CONSULTAR LA INFORMACION DE GRUPO
		$sql="SELECT id_grupo_factura_venta FROM ventas_facturas_inventario_grupos
				WHERE activo=1 AND id_factura_venta=$id_factura AND id_inventario_factura_venta=$id_inventario";
		$query=$mysql->query($sql,$mysql->link);
		$id_grupo = $mysql->result($query,0,'id_grupo_factura_venta');
		if ($id_grupo=='' || $id_grupo==0) { echo "<script>console.log('Item Sin grupo!');</script>"; return; }

		// CONSULTAR LA INFORMACION DEL INVENTARIO
		$sql="SELECT codigo,nombre,cantidad,costo_unitario,tipo_descuento,descuento,valor_impuesto
				FROM devoluciones_venta_inventario WHERE activo=1 AND id_devolucion_venta = $id_documento AND id_fila_cargada=$id_inventario";
		$query=$mysql->query($sql,$mysql->link);
		$codigo         = $mysql->result($query,0,'codigo');
		$nombre         = $mysql->result($query,0,'nombre');
		$cantidad       = $mysql->result($query,0,'cantidad');
		$costo_unitario = $mysql->result($query,0,'costo_unitario');
		$tipo_descuento = $mysql->result($query,0,'tipo_descuento');
		$descuento      = $mysql->result($query,0,'descuento');
		$valor_impuesto = $mysql->result($query,0,'valor_impuesto');

		$subtotal = $cantidad*$costo_unitario;
		if ($descuento>0 && $tipo_descuento=='porcentaje') {
			$descuento = $subtotal*$descuento/100;
		}
		$impuesto = (($subtotal-$descuento)*$valor_impuesto)/100;
		$total = $subtotal-$descuento+$impuesto;

		// AGREGAR VALORES A LOS CAMPOS
		if ($accion=='sumar') {
			$sql="UPDATE devoluciones_venta_grupos SET
						descuento      = descuento+$descuento,
						valor_impuesto = valor_impuesto+$impuesto,
						costo_unitario = costo_unitario+($subtotal)
 					WHERE activo=1 AND id_devolucion_venta=$id_documento AND id_fila_grupo_factura_venta=$id_grupo AND id_empresa=$id_empresa";
			$query=$mysql->query($sql,$mysql->link);
		}

		// RESTAR VALORES A LOS CAMPOS
		else if ($accion=='restar') {
			$sql="UPDATE devoluciones_venta_grupos SET
						descuento      = descuento-$descuento,
						valor_impuesto = valor_impuesto-$impuesto,
						costo_unitario = costo_unitario-($subtotal)
 					WHERE activo=1 AND id_devolucion_venta=$id_documento AND id_fila_grupo_factura_venta=$id_grupo AND id_empresa=$id_empresa";
			$query=$mysql->query($sql,$mysql->link);
		}

		//SI SE ESTA AGREGANDO UN NUVEO ITEM Y ESTE PERTENECE A UN GRUPO, ENTONCES EN EL DOM SE DEBE MOVER AL DOM DEL GRUPO
		if ($mueve_fila==true) {
			echo "<script>
					$('#bodyDivArticulosDevolucionVenta_'+$(\"[value='$id_inventario']\")[0].id.split('_')[1]).appendTo('#content-group-$id_grupo');
				</script>";
		}

		// echo $sql;
		// CONSULTAR LOS NUEVOS DATOS DEL GRUPO
		$sql="SELECT
					cantidad,
					descuento,
					valor_impuesto,
					costo_unitario
				FROM devoluciones_venta_grupos WHERE activo=1 AND id_devolucion_venta=$id_documento AND id_fila_grupo_factura_venta=$id_grupo AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		$cantidad       = $mysql->result($query,0,'cantidad');
		$descuento      = $mysql->result($query,0,'descuento');
		$valor_impuesto = $mysql->result($query,0,'valor_impuesto');
		$costo_unitario = $mysql->result($query,0,'costo_unitario');
		$total_grupo    = $costo_unitario-$descuento;

		// MOSTRAR LOS NUERVOS VALORES
		echo "<script>
				//if ( $('#costo_grupo').length > 0 ) $('#costo_grupo').val('$costo_unitario') ;
				//if ( $('#descuento_grupo').length > 0 ) $('#descuento_grupo').val('$descuento') ;
				//if ( $('#impuesto_grupo').length > 0 ) $('#impuesto_grupo').val('$valor_impuesto') ;

				if ( $('#descuentoArticuloDevolucionVenta_$id_grupo').length > 0 ) $('#descuentoArticuloDevolucionVenta_$id_grupo').val('$descuento') ;
				if ( $('#costoGrupoDevolucionVenta_$id_grupo').length > 0 ) $('#costoGrupoDevolucionVenta_$id_grupo').val('$costo_unitario') ;
				if ( $('#costoTotalGrupoDevolucionVenta_$id_grupo').length > 0 ) $('#costoTotalGrupoDevolucionVenta_$id_grupo').val('$total_grupo') ;

			</script>";

	}

	//====================== ENVIAR DEVOLUCION ELECTRONICA =====================//
	function enviarDevolucionDIAN($id_devolucion,$opcGrillaContable,$id_empresa,$id_sucursal,$mysql,$link){

		if($id_empresa == 0 || $id_empresa == 999999999999999){
			// Se incluye la clase de la factura electronica y la libreria del web service
			include("ClassDevolucionJSON.php");
			include("../../../../web_service/nuSoap/nusoap.php");

			//Instanciamos el objeto y llamamos los metodos para enviar la factura
			$devolucionJSON = new ClassDevolucionJSON($mysql);
		  $devolucionJSON->obtenerDatos($id_devolucion,$id_empresa,$id_sucursal);
		  $devolucionJSON->construirJSON();
			$result 				= $devolucionJSON->enviarJSON();
			$quitar 				= array("'",".");
			$result	 				= str_replace($quitar,"",$result);

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//Se guardan los datos de la respuesta del web web_service
			$sqlDevolucionFacturaDIAN =  "UPDATE
																			devoluciones_venta
																		SET
																			fecha_DE = '$fecha_actual',
																			hora_DE = '$hora_actual',
																			response_DE = '$result[comentario]',
																			UUID = '$result[id_factura]',
																			id_usuario_DE = '$_SESSION[IDUSUARIO]',
																			nombre_usuario_DE = '$_SESSION[NOMBREFUNCIONARIO]',
																			cedula_usuario_DE = '$_SESSION[CEDULAFUNCIONARIO]'
																		WHERE
																			id = $id_devolucion";

			$queryDevolucionFacturaDIAN = mysql_query($sqlDevolucionFacturaDIAN,$link);

			//Se evalua la repsuesta del web service y se cierra la ventana de carga
			if($result["comentario"] == "Ejemplar recibido exitosamente pasara a verificacion"){
				echo "<script>
								alert('Devolucion Enviada');
								document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
								Ext.getCmp('Btn_enviar_devolucion_electronica_$opcGrillaContable').disable();
							</script>";
			}
			else if($result["comentario"] != "Ejemplar recibido exitosamente pasara a verificacion"){
				echo "<script>
								alert('Devolucion No Enviada');
								document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
								Ext.getCmp('Btn_enviar_devolucion_electronica_$opcGrillaContable').enable();
							</script>";
			}
		}
		else{
			if($opcGrillaContable == "DevolucionVenta"){

				include("ClassDevolucionJSON_V2.php");

				$devolucionJSON = new ClassDevolucionJSON_V2($mysql);
				$devolucionJSON->obtenerDatos($id_devolucion,$id_empresa);
				$devolucionJSON->construirJSON();
				$result         = $devolucionJSON->enviarJSON();

				$result['validar'] = str_replace("\'","",$result['validar']);

					if(strpos($result['validar'],"Procesado Correctamente") != false || strpos($result['validar'],"Documento no enviado, Ya cuenta con env") != false || strpos($result['validar'],"procesado anteriormente") != false || strpos($result['validar'],"ha sido autorizada") != false){
						$response_DE = "Ejemplar recibido exitosamente pasara a verificacion";

						$result['validar'] = str_replace("'", "-", $result['validar']);

						echo "<script>
										alert('Devolucion Enviada');
										console.log('$result[validar]');
										document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
										Ext.getCmp('Btn_enviar_devolucion_electronica_$opcGrillaContable').disable();
									</script>";
					}
					else{
						$response_DE = $result['validar'];

						echo "<script>
										alert('Devolucion No Enviada');
										console.log('$result[validar]');
										document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
									</script>";
					}

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//Se guardan los datos de la respuesta del web web_service
				$sqlEnviarDevolucionDIAN = "UPDATE
											devoluciones_venta
											SET
											fecha_DE = '$fecha_actual',
											hora_DE = '$hora_actual',
											response_DE = '$response_DE',
											UUID = '$result[id_devolucion]',
											id_usuario_DE = '$_SESSION[IDUSUARIO]',
											nombre_usuario_DE = '$_SESSION[NOMBREFUNCIONARIO]',
											cedula_usuario_DE = '$_SESSION[CEDULAFUNCIONARIO]'
											WHERE
											id = $id_devolucion";

				$queryEnviarDevolucionDIAN = mysql_query($sqlEnviarDevolucionDIAN,$link);
			}
			else{
				echo "<script>alert('envio de documento soporte');</script>";

				include("ClassSupportDocumentReturn.php");

				$documentoJson = new ClassSupportDocumentReturn($id_factura,$mysql);
				$data = $documentoJson->sendDocumentReturn(true);
				
				if(strpos($data["result"],"Procesado Correctamente") != false || strpos($data["result"],"Documento no enviado, Ya cuenta con env") != false || strpos($data["result"],"procesado anteriormente") != false || strpos($data["result"],"ha sido autorizada") != false){
					
					$response_DS = "Ejemplar recibido exitosamente pasara a verificacion";
					$alert = "Documento Enviado.";
					$boton = "Ext.getCmp('Btn_enviar_factura_electronica_$opcGrillaContable').disable();";

					echo   "<script>
								alert('$alert');
								console.log('".$data["result"]."');
								document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
								$boton
							</script>";
				}
				else{
					$response_DS = $data["result"];

					echo   "<script>
								alert('Documento No Enviado.');
								console.log('".$data["result"]."');
								document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
							</script>";
				}

				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//Se guardan los datos de la respuesta del web web_service
				$sqlEnviarDevolucionDIAN = "UPDATE
												devoluciones_compra
											SET
												fecha_DE = '$fecha_actual',
												hora_DE = '$hora_actual',
												response_DE = '$response_DE',
												UUID = '$result[id_devolucion]',
												id_usuario_DE = '$_SESSION[IDUSUARIO]',
												nombre_usuario_DE = '$_SESSION[NOMBREFUNCIONARIO]',
												cedula_usuario_DE = '$_SESSION[CEDULAFUNCIONARIO]'
											WHERE
												id = $id_devolucion";

				$queryEnviarDevolucionDIAN = mysql_query($sqlEnviarDevolucionDIAN,$link);
			}
	  }
	}

?>
