 <?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");
	require("functions_body_article.php");
	// error_reporting(E_ALL);

	$cuentaPago = 0;
	$cuentaPagoNiif = 0;
	$saldoGlobalFactura = 0;
	$saldoGlobalFacturaSinAbono = 0;

	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($idFactura)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if ($opc<>'guardarFechaFactura') {
			verificaCierre($idFactura,'compras_facturas',$id_empresa,$link);
		}
	}

	switch ($opc) {

		case 'updateTerceroHead':
			updateTerceroHead($idFactura,$codProveedor,$id_empresa,'ProveedorFactura',$inputId,$evt,$link);
			break;

		case 'cargarCampoOrdenCompra':
			cargarCampoOrdenCompra();
			break;

		case 'buscarOrdenCompra':
			buscarOrdenCompra($idOrdenCompra,$id_sucursal,$filtro_bodega,$id_empresa,$link,$confirm,$opcCargar);
			break;

		case 'cargaHeadInsertUnidades':
			cargaHeadInsertUnidades('echo',1);
			break;

		case 'cargaDivsInsertUnidades':
			cargaDivsInsertUnidades('echo',$cont);
			break;

		case 'ventanaDescripcionArticulofactura':
			ventanaDescripcionArticulofactura($cont,$idArticulo,$idFactura,$id_empresa,$idInsert,$filtro_bodega,$link);
			break;

		case 'guardarDescripcionArticuloFactura':
			guardarDescripcionArticuloFactura($cont,$idCentroCostos,$idImpuesto,$observacion,$idInventario,$idInsert,$idFactura,$id_empresa,$filtro_bodega,$link);
			break;

		case 'checkboxRetenciones':
			checkboxRetenciones($idFactura,$idRetencion,$accion,$link);
			break;

		case 'UpdateFormaPago':
			UpdateFormaPago($id,$idFormaPago,$link);
			break;

		case 'UpdateCuentaPago':
			UpdateCuentaPago($id,$idCuentaPago,$link);
			break;

		case 'UpdateIdPlantilla':
			UpdateIdPlantilla($id,$idPlantilla,$link);
			break;

		case 'nuevaFacturaCompra':
			nuevaFacturaCompra();
			break;

		//==========================// ITEMS //==========================//
		case 'buscarArticuloFactura':
			buscarArticuloFactura($valorArticulo,$contArticulo,$id_empresa,$id_sucursal,$idBodega,$link);
			break;

		case 'guardarArticuloFactura':
			guardarArticuloFactura($consecutivoFactura,$idFactura,$contFactura,$idInventarioFactura,$cantArticuloFactura,$tipoDesc,$descuentoArticuloFactura,$costoArticuloFactura,$checkOpcionContable,$link);
			break;

		case 'actualizaArticuloFactura':
			actualizaArticuloFactura($idFactura,$idInsertArticulo,$contFactura,$idInventarioFactura,$cantArticuloFactura,$tipoDesc,$descuentoArticuloFactura,$costoArticuloFactura,$iva,$checkOpcionContable,$link);
			break;

		case 'deleteArticuloFactura':
			deleteArticuloFactura($contFactura,$idFactura,$idArticuloFactura,$link);
			break;

		case 'retrocederArticuloFactura':
			retrocederArticuloFactura($idFactura,$idArticulo,$cont,$id_empresa,$link);
			break;

		//=================== IMPUESTOS RETENCIONES ====================//
		// case 'buscarImpuestoArticuloFacturaCompra':
		// 	buscarImpuestoArticuloFacturaCompra($id_inventario,$link);
		// 	break;


		//======================== FACTURA ===========================//
		case 'guardarObservacionFacturaCompra':
			guardarObservacionFacturaCompra($observacion,$idFactura,$link);
			break;

		case 'guardarFechaFactura':
			guardarFechaFactura($idInputDate,$idFactura,$valInputDate,$link);
			break;

		case 'terminarFacturaCompra':
			require("contabilizar_bd.php");
			require("contabilizar_niif_bd.php");

			terminarFacturaCompra($idPlantilla,$idProveedor,$nitProveedor,$idFactura,$prefijoFactura,$numeroFactura,$id_empresa,$id_sucursal,$idBodega,$observacion,$link);
			break;

		case 'AgregarOrdenCompra':
			AgregarOrdenCompra($idProveedorFactura,$codDocAgregar,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$link,$confirmar,$opcCargar);
			break;

		case 'eliminaDocReferenciaFactura':
			eliminaDocReferenciaFactura($id_referencia,$docReferencia,$idFactura,$id_empresa,$id_sucursal,$filtro_bodega,$link);
			break;

		case 'cancelarFacturaCompra':
			cancelarFacturaCompra($idFactura,$id_empresa,$id_sucursal,$idBodega,$link);
			break;

		case 'restaurarFacturaCompra':
			restaurarFacturaCompra($idFactura,$id_sucursal,$id_empresa,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idFactura,$id_empresa,$id_sucursal,$id_bodega,$link);
			break;

		case 'abrirVentanaUpdateValores':
			abrirVentanaUpdateValores($idFactura,$subtotal,$iva,$total,$id_empresa,$link);
			break;

		case 'guardarEmpleadoRecibeAlmacen':
			guardarEmpleadoRecibeAlmacen($id,$idFactura,$id_empresa,$link);
			break;

		case 'validateCcos':
			validateCcos($codigoCcos,$id_empresa,$link);
			break;

		case 'updateCentroCostos':
			updateCentroCostos($contFila,$idDepartamento,$id_empresa,$link);
			break;

		case 'cancelarValoresFacturaCompra':
			cancelarValoresFacturaCompra($idFactura,$id_empresa,$link);
			break;

		case 'guardarValoresFacturaCompra':
			guardarValoresFacturaCompra($idFactura,$subtotal,$iva,$total,$id_centro_costos,$id_cuenta_subtotal,$id_cuenta_niif_subtotal,$id_cuenta_iva,$id_cuenta_niif_iva,$id_cuenta_total,$id_cuenta_niif_total,$id_empresa,$link);
			break;

		case 'sincronizarCuentaNiif':
			sincronizarCuentaNiif($id_cuenta_colgaap,$campoIdNiif,$campoNiif,$id_empresa,$link);
			break;

		case 'validarNumeroFactura':
			validarNumeroFactura($prefijoFactura,$numeroFactura,$idFactura,$nitProveedor,$id_empresa,$link);
			break;

		case 'ventanaValorAnticipo':
			ventanaValorAnticipo($idFactura,$valor_Factura,$id_cuenta_anticipo,$id_anticipo,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'guardarAnticipo':
			guardarAnticipo($contFila,$idFactura,$valor_anticipo,$id_cuenta_anticipo,$id_anticipo,$opcGrillaContable,$id_empresa,$link);
			break;

		case 'cancelarAnticipoFactura':
			cancelarAnticipoFactura($idFactura,$id_empresa,$link);
			break;

		case 'filtro_anticipo':
			filtro_anticipo($idFactura,$idProveedor,$opcGrilla,$id_empresa,$link);
			break;

		case 'cargarNewDocumento':
			cargarNewDocumento($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar);
			break;

		case 'agregarDocumento':
			agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
			break;

		case 'reloadBodyAgregarDocumento':
			reloadBodyAgregarDocumento($opcGrillaContable,$id_documento,$id_sucursal,$id_empresa,'compras_facturas','compras_facturas_inventario','id_factura_compra',$link);
			break;
		case 'eliminaDocReferencia':
			// verificaEstadoDocumento($id_factura,$opcGrillaContable,$tablaPrincipal,$link);
			eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,'compras_facturas','compras_facturas_inventario','id_factura_compra',$link);
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

		case 'updateSupportDocumentResolution':
			updateSupportDocumentResolution($idFactura,$resolution_id,$link);
			break;
		case 'updateDocumentType':
			updateDocumentType($idFactura,$document_type,$link);
			break;
		case 'enviarFacturaDIAN':
			enviarFacturaDIAN($id_factura,$opcGrillaContable,$id_empresa,$mysql);
			break;
		case 'updateRows':
			updateRows($row,$value,$idFactura,$id_empresa,$mysql,$link);
			break;
		case 'filtro_tipo_doc':
			filtro_tipo_doc($filtro_bodega,$id_empresa,$link);
			break;


	}

	//=================================// FUNCION PARA BUSCAR UN CLIENTE //=================================//
	//******************************************************************************************************//
	function updateTerceroHead($idFactura,$codProveedor,$id_empresa,$opcGrillaContable,$inputId,$evt,$link){
		//PERMISO PARA AGREGAR UN PROVEEDOR A LA FACTURA
		if(user_permisos(60,'true') == 'true'){
			echo'<script>alert("Aviso\nUsted no posee permisos para facturar sin orden de compra\nSolo puede facturar cargando una orden de compra");</script>';
			exit;
		}

		//CONSULTA EL RECIBO DE CAJA
		$sqlFC   = "SELECT COUNT(id) AS contFC,id_proveedor,cod_proveedor,nit,proveedor,observacion,estado FROM compras_facturas WHERE id='$idFactura'";
		$queryFC = mysql_query($sqlFC,$link);

		$contFC        = mysql_result($queryFC, 0, 'contFC');
		$id_terceroFC  = mysql_result($queryFC, 0, 'id_proveedor');
		$codigoFC      = mysql_result($queryFC, 0, 'cod_proveedor');
		$nitFC         = mysql_result($queryFC, 0, 'nit');
		$nombreFC      = mysql_result($queryFC, 0, 'proveedor');
		$observacionFC = mysql_result($queryFC, 0, 'observacion');
		$estadoFC      = mysql_result($queryFC, 0, 'estado');

		if($contFC == 0 || is_nan($contFC)){ echo '<script>alert("Aviso,\nNo se encontro informacion sobre la factura de compra!")</script>'; exit; }

		//CONSULTA LA INFORMACION DE NUEVO TEFCERO
		$campo   = ($inputId=='nit'.$opcGrillaContable)? "numero_identificacion": "codigo";
		$mensaje = ($inputId=='nit'.$opcGrillaContable)? 'alert("NIT de tercero no establecido");' : 'alert("codigo de tercero no establecido");';

		$sql   = "SELECT COUNT(id) AS contTercero,id,numero_identificacion,tipo_identificacion,codigo,nombre
					FROM terceros
					WHERE $campo='$codProveedor'
						AND activo=1
						AND tercero = 1
						AND id_empresa='$id_empresa'
						AND tipo_proveedor='Si'
					LIMIT 0,1";
		$query = mysql_query($sql,$link);

		$id_tercero  = mysql_result($query,0,'id');
		$contTercero = mysql_result($query,0,'contTercero');
		$nit         = mysql_result($query,0,'numero_identificacion');
		$codigo      = mysql_result($query,0,'codigo');
		$nombre      = mysql_result($query,0,'nombre');

		//SI EL NUEVO TERCERO EXISTE Y ES DIFERENTE DEL ANTERIOR
		if($id_tercero > 0 && $id_terceroFC != $id_tercero){
			$sqlUpdate   = "UPDATE compras_facturas SET id_proveedor = '$id_tercero' WHERE id='$idFactura' AND id_empresa='$id_empresa'";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			// $sqlDeleteInventario   = "DELETE FROM compras_facturas_inventario WHERE id_factura_compra = '$idFactura'";
			// $queryDeleteInventario = mysql_query($sqlDeleteInventario,$link);

			// $sqlDeleteRetenciones   = "DELETE FROM compras_facturas_retenciones  WHERE id_factura_compra = '$idFactura'";
			// $queryDeleteRetenciones = mysql_query($sqlDeleteRetenciones,$link);

			// CHECKBOX RETENCIONES CHECKED
			$arrayRetenciones    = '';
			$sqlArrayRetenciones = "SELECT R.id,
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
		                                    AND TR.id_proveedor = '$id_tercero'
		                                    OR R.factura_auto = 'true'
		                                )
		                                AND TR.activo = 1
		                                AND TR.id_empresa = '$id_empresa'
		                            )
		                            WHERE
		                                R.id_empresa = '$id_empresa'
		                            AND R.activo = 1
		                            AND R.cuenta > 0
		                            AND R.modulo = 'Compra'
		                            AND R.id NOT IN (
											SELECT
												id_retencion AS id
											FROM
												compras_facturas_retenciones
											WHERE id_factura_compra = $idFactura
										)
		                            GROUP BY
		                                R.id";
			$queryArrayRetenciones = mysql_query($sqlArrayRetenciones,$link);

			$retenciones .= 'var contenedor=document.getElementById(\'contenedorCheckboxFacturaCompra\'); contenedor.innerHTML="";';
			// CREAR ARRAY DE LAS RETENCIONES
			while ($row=mysql_fetch_array($queryArrayRetenciones)) {
				$whereIdRetenciones .= ' AND id<>'.$row['id'] ;
				$arrayRetenciones[$row['id']] = array(
														'tipo_retencion' => $row['tipo_retencion'],
														'cuenta'         => $row['cuenta'],
														'retencion'      => $row['retencion'],
														'valor'          => $row['valor'],
														'base'           => $row['base'],
													);
			}

			$sqlArrayRetenciones = "SELECT R.id,
		                                R.tipo_retencion,
		                                R.cuenta,
		                                R.retencion,
		                                R.valor,
		                                R.base
		                            FROM
		                                retenciones AS R
		                            WHERE
		                                R.id_empresa = '$id_empresa'
		                            AND R.activo = 1
		                            AND R.cuenta > 0
		                            AND R.modulo = 'Compra'
		                            AND R.factura_auto = 'true'
		                            $whereIdRetenciones
		                            AND R.id NOT IN (
											SELECT
												id_retencion AS id
											FROM
												compras_facturas_retenciones
											WHERE id_factura_compra = $idFactura
										)
		                            GROUP BY
		                                R.id";
			$queryArrayRetenciones = mysql_query($sqlArrayRetenciones,$link);
			while ($row=mysql_fetch_array($queryArrayRetenciones)) {
				$whereIdRetenciones .= ' AND id<>'.$row['id'] ;
				$arrayRetenciones[$row['id']] = array(
														'tipo_retencion' => $row['tipo_retencion'],
														'cuenta'         => $row['cuenta'],
														'retencion'      => $row['retencion'],
														'valor'          => $row['valor'],
														'base'           => $row['base'],
													);

			}

			$retenciones .= 'var contenedor=document.getElementById(\'contenedorCheckboxFacturaCompra\'); contenedor.innerHTML="";';
			// CREAR ARRAY DE LAS RETENCIONES
			while ($row=mysql_fetch_array($queryArrayRetenciones)) {
				$arrayRetenciones[$row['id']] = array(
														'tipo_retencion' => $row['tipo_retencion'],
														'cuenta'         => $row['cuenta'],
														'retencion'      => $row['retencion'],
														'valor'          => $row['valor'],
														'base'           => $row['base'],
													);

			}
			foreach ($arrayRetenciones as $id => $arrayResult) {
				$retenciones .= 'var contenedor=document.getElementById(\'contenedorCheckboxFacturaCompra\');
										contenedor.innerHTML=contenedor.innerHTML+\'<div class="campoCheck" title="'.$arrayResult['tipo_retencion'].'" id="contenedorRetencionesFacturaCompra_'.$id.'">\'
	                					                        				    +\'<div id="cargarCheckboxFacturaCompra_'.$id.'" class="renderCheck"></div>\'
	                					                        				    +\'<input type="hidden" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFacturaCompra_'.$id.'" name="checkboxFacturaCompra" value="'.$arrayResult['valor'].'"  />\'
	                					                        				    +\'<label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$arrayResult['retencion'].'">\'
	                					                        				        +\'<div class="labelNombreRetencion">'.$arrayResult['retencion'].'</div>\'
	                					                        				        +\'<div class="labelValorRetencion">('.$arrayResult['valor'].'%)</div>\'
	                					                        				    +\'</label>\'
	                					                        				+\'</div>\';';


				$retenciones .= 'arrayRetencionesFacturaCompra['.$id.']='.$id.';';

		    	$retenciones .= 'objectRetenciones_FacturaCompra['.$id.'] = {'
	                                                                                	    .'tipo_retencion : "'.$arrayResult['tipo_retencion'].'",'
	                                                                                	    .'base           : "'.$arrayResult['base'].'",'
	                                                                                	    .'valor          : "'.$arrayResult['valor'].'",'
	                                                                                	    .'cuenta         : "'.$arrayResult['cuenta_venta'].'",'
	                                                                                	    .'estado         : "0"'
	                                                                                	.'};';

				// $arrayRetenciones     .='if(document.getElementById("checkboxRetencionesFactura_'.$row['id_retencion'].'")){
											// document.getElementById("checkboxRetencionesFactura_'.$row['id_retencion'].'").checked=true;
										// }';

				$sqlInsertRetencion   = "INSERT INTO compras_facturas_retenciones (id_factura_compra,id_retencion) VALUES ('$idFactura','".$id."')";
				$queryInsertRetencion = mysql_query($sqlInsertRetencion);

				echo'<script>retefuenteFacturaCompra+='.$arrayResult['valor'].';</script>';
			}

			// CARGAR LAS RETENCIONES AUTOMATICAS


			echo'<script>
					iva'.$opcGrillaContable.'        = 0.00;
					total'.$opcGrillaContable.'      = 0.00;
					subtotal'.$opcGrillaContable.'   = 0.00;
					retefuente'.$opcGrillaContable.' = 0.00;

					document.getElementById("cod'.$opcGrillaContable.'").value    = "'.$codigo.'";
					document.getElementById("nit'.$opcGrillaContable.'").value    = "'.$nit.'";
					document.getElementById("nombre'.$opcGrillaContable.'").value = "'.$nombre.'";

					objectRetenciones_FacturaCompra = [];
					arrayRetencionesFacturaCompra   = [];
					'.$retenciones.'

					contArticulosFactura = '.(($evt=='insert')? '1' : 'contArticulosFactura' ).';
					id_proveedor_factura = "'.$id_tercero.'";

					nit'.$opcGrillaContable.'    = "'.$nit.'";
					nombre'.$opcGrillaContable.' = "'.$nombre.'";
					codigo'.$opcGrillaContable.' = "'.$codigo.'";
				</script>'.(($evt=='insert')? cargaHeadInsertUnidades('return',1,$opcGrillaContable) : '' );
		}
		else{
			echo'<script>
					iva'.$opcGrillaContable.'        = 0.00;
					total'.$opcGrillaContable.'      = 0.00;
					subtotal'.$opcGrillaContable.'   = 0.00;
					retefuente'.$opcGrillaContable.' = 0.00;

					document.getElementById("cod'.$opcGrillaContable.'").value    = "'.$codigoFC.'";
					document.getElementById("nit'.$opcGrillaContable.'").value    = "'.$nitFC.'";
					document.getElementById("nombre'.$opcGrillaContable.'").value = "'.$nombreFC.'";

					id_proveedor_factura = "'.$id_terceroFC.'";

					nit'.$opcGrillaContable.'    = "'.$nitFC.'";
					nombre'.$opcGrillaContable.' = "'.$nombreFC.'";
					codigo'.$opcGrillaContable.' = "'.$codigoFC.'";

					'.$mensaje.'
				</script>';
		}
	}

	//funcion para mostrar el campo nit en la factura/orden de compra
	function cargarCampoOrdenCompra(){

		echo'<div style="float:left; text-align:center; width:100%; " title="cargar una orden de compra" id="divContenedorCargarDesdeFacturaCompra"  onclick="cambiarCargaFacturaFacturaCompra()">
				<div class="div_hover" style="width:10%;" id="imgFacturarDesdeFacturaCompra" ><img src="img/pedido.png" id="imgCargarDesdeFacturaCompra" width"20px" height="20px" /></div>
				<div class="div_hover" style="width:40%;" id="textoFacturardesdeFacturaCompra" ><b>Orden de Compra</b> </div>
				<div class="div_hover" style="width:10%;"><img src="img/flecha_abajo.png" /></div>
			</div>

			<div style="float:left; margin: 7px 0 0 5px">
			    <div style="float:left; width:120px; height:22px;">
				    <input  placeholder="Numero..." class="myFieldGrilla" id="ordenCompra" onKeyup="buscarOrdenCompraFactura(event,this)" style="padding-left: 5px;"/>
				</div>
				<div class="iconBuscarProveedor" title="Buscar" onclick="ventanaBuscarDocumentoCruceFacturaCompra();" style="margin-top:2px; margin-left:-23;">
				    <img src="img/buscar20.png"/>
				</div>
				<div title="Cargar Documento en nueva Factura" onclick="cargarOrdenCompraFactura();" class="btnCargarAgregarOrdenCompra" style="display:none;">
				   <img src="img/page.png"/>
				</div>
				<div title="Agregar Documento" onclick="agregarOrdenCompraFactura(\'false\');" class="btnCargarAgregarOrdenCompra">
				   <img src="img/add16.png"/>
				</div>
			    <div style="float:left; max-width:20px; max-height:20px; overflow:hidden; float:left; margin-left:-23px" id="renderCargaCotizacionPedidoFacturaCompra"></div>
			</div>
			<script>
				document.getElementById("ordenCompra").focus();
				var cambioCargaFacturaCompra = 1;
				function cambiarCargaFacturaFacturaCompra(){
					if (cambioCargaFacturaCompra==0) {
						cambioCargaFacturaCompra++;

						document.getElementById("imgCargarDesdeFacturaCompra").setAttribute("src","img/pedido.png");
						document.getElementById("imgCargarDesdeFacturaCompra").setAttribute("width","20px");
						document.getElementById("imgCargarDesdeFacturaCompra").setAttribute("height","20px");

						document.getElementById("textoFacturardesdeFacturaCompra").innerHTML="<b>Orden de Compra</b>";
						document.getElementById("divContenedorCargarDesdeFacturaCompra").setAttribute("title","Haga Click para cambiar a facturar desde una Entrada de Almacen");

						document.getElementById("ordenCompra").focus();

					}else if (cambioCargaFacturaCompra==1) {
						cambioCargaFacturaCompra=0;

						document.getElementById("imgCargarDesdeFacturaCompra").setAttribute("src","../ventas/img/remisiones.png");
						document.getElementById("imgCargarDesdeFacturaCompra").setAttribute("width","20px");
						document.getElementById("imgCargarDesdeFacturaCompra").setAttribute("height","20px");

						document.getElementById("textoFacturardesdeFacturaCompra").innerHTML="<b>Entrada de Almacen</b>";
						document.getElementById("divContenedorCargarDesdeFacturaCompra").setAttribute("title","Haga Click para cambiar a facturar desde una Orden");

						document.getElementById("ordenCompra").focus();

					}
				}
			</script>';
	}

	//=========================== CARGAR LA CABECERA DE LA GRILLA DE LOS ARTICULOS ==============================================//
	function cargaHeadInsertUnidades($formaConsulta,$cont){

		$contenidoEditarValores='';
		if (user_permisos(89,'false') == 'true') {
			$contenidoEditarValores='<div style="float:left;margin-top:1px;margin: 1px 0 0 -21px;cursor:pointer;" onclick="abrirVentanaUpdateValoresFacturaCompra()" id="imgAjusteFactura" title="Editar Valores Totales">
                    				   <img src="img/config16.png" style="margin: 2px 0 0 2px;"/>
                    				</div>';
		}

		$head ='<div class="contenedorGrilla">
					<div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
					<div class="contenedorHeadArticulos">
						<div class="headArticulos" id="headFacturaCompra">
							<div class="label" style="width:40px !important; border-left:none; padding-left:2px;"></div>
							<div class="label" title="Codigo/EAN">Codigo/EAN</div>
							<div class="labelNombreArticulo">Articulo</div>
							<div class="label" title="Unidad">Unidad</div>
							<div class="label" title="Cantidad">Cantidad</div>
							<div class="label" title="Descuento">Descuento</div>
							<div class="label" title="Precio Unitario">Precio Unitario</div>
							<div class="label" title="Precio Total">Precio Total</div>
							<div class="labelCheck" title="Activo Fijo">A.F.</div>
							<div class="labelCheck" title="Costo">C.</div>
							<div class="labelCheck" title="Gasto de Venta" style="border-right: 1px solid #d4d4d4">G.V.</div>
							<div style="float:right; min-width:80px;"></div>
						</div>
					</div>
					<div class="DivArticulos" id="DivArticulosFactura" onscroll="resizeHeadMyGrilla(this,\'headFacturaCompra\')">
						<div class="bodyDivArticulosFactura" id="bodyDivArticulosFactura_'.$cont.'">
							'.cargaDivsInsertUnidades('return',$cont).'
						</div>
					</div>
				</div>

				<div class="contenedor_totales" id="contenedor_totales_facturas_compras">
					<div class="contenedorObservacionGeneral">
						<div style="padding:2px 0 0 3px;" id="labelObservacionFactura"><b>OBSERVACIONES</b></div>
						<textarea id="observacionFacturaCompra" onKeydown="inputObservacionFacturaCompra(event,this)"></textarea>
					</div>
					<div class="contenedorDetalleTotales">
						<div class="renglon">
							<div class="label" style="width:170px !important; padding-left:5px;">Subtotal</div>
							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="subtotalFacturaCompra"  style="width:140px;">0</div>
						</div>
						<div class="renglon" style="overflow:visible; height:auto;">
							<div class="label" style="height:auto; width:172px !important; padding-left:3px; font-weight:bold; overflow:visible;" id="labelIvaFacturaCompra">Iva</div>
							<div class="labelSimbolo" id="simboloIvaFacturaCompra">$</div>
							<div class="labelTotal" id="ivaFacturaCompra" >0</div>
						</div>
						<div class="renglon" style="display:none; overflow:visible; height:auto;" id="divRetencionesFacturaCompra" >
							<div class="label" style="height:auto; width:170px !important; padding-left:5px; font-weight:bold; overflow:visible;" id="idretencionFacturaCompra"></div>
							<div class="labelSimbolo" id="simboloRetencionFacturaCompra"></div>
							<div class="labelTotal" style="height:auto; overflow:visible;" id="retefuenteFacturaCompra"></div>
						</div>
						<div class="renglon renglonTotal">

							<div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL</div>

							'.$contenidoEditarValores.'

							<div class="labelSimbolo">$</div>
							<div class="labelTotal" id="totalFacturaCompra">0</div>
						</div>
					</div>
				</div>

				<script>

					//mostramos los valores de las variables de los calculos del total de la factura
					document.getElementById("subtotalFacturaCompra").innerHTML = parseFloat(subtotalFacturaCompra).toFixed(2);
					document.getElementById("ivaFacturaCompra").innerHTML      = parseFloat(ivaFacturaCompra).toFixed(2);
					document.getElementById("totalFacturaCompra").innerHTML    = parseFloat(totalFacturaCompra).toFixed(2);

					document.getElementById("eanArticuloFactura_'.$cont.'").focus();
				</script>';

		if($formaConsulta=='return'){ return $head; }
		else{ echo $head; }
	}

	//=========================== CARGAR LA GRILLA CON LOS ARTICULOS ==============================================//
	function cargaDivsInsertUnidades($formaConsulta,$cont){
		$body ='<div class="campo" style="width:40px !important; border-left:none; padding-left:2px; overflow:hidden;">
					<div style="float:left; margin-top:3px;">'.$cont.'</div>
					<div style="float:right; width:18px; overflow:hidden;" id="renderArticuloFactura_'.$cont.'"></div>
				</div>

				<div class="campo">
					<input type="text" id="eanArticuloFactura_'.$cont.'" onKeyup="buscarArticuloFactura(event,this);" />
				</div>

				<div class="campoNombreArticulo"><input type="text" id="nombreArticuloFactura_'.$cont.'" style="text-align:left;" readonly/></div>
				<div onclick="ventanaBuscarArticuloFactura('.$cont.');" title="Buscar Articulo" class="iconBuscarArticulo">
					<img src="img/buscar20.png"/>
				</div>

				<div class="campo"><input type="text" id="unidadesFactura_'.$cont.'" style="text-align:left;" readonly /></div>
				<div class="campo"><input type="text" id="cantArticuloFactura_'.$cont.'"/></div>

				<div class="campo campoDescuento">
					<div onclick="tipoDescuentoArticulo('.$cont.')" id="tipoDescuentoArticulo_'.$cont.'" title="En porcentaje">
						<img src="img/porcentaje.png" id="imgDescuentoArticulo_'.$cont.'"/>
					</div>
					<input type="text" id="descuentoArticuloFactura_'.$cont.'"/>
				</div>

				<div class="campo"><input type="text" id="costoArticuloFactura_'.$cont.'" onKeyup="guardarAutoFactura(event,this,'.$cont.');" value="0"/></div>
				<div class="campo"><input type="text" id="costoTotalArticuloFactura_'.$cont.'"  readonly/></div>

				<div class="campoOptionCheck" id="div_check_factura_activo_fijo_'.$cont.'"></div>
				<div class="campoOptionCheck" id="div_check_factura_costo_'.$cont.'"></div>
				<div class="campoOptionCheck" id="div_check_factura_gasto_'.$cont.'" style="border-right: 1px solid #d4d4d4;"></div>

				<div style="float:right; min-width:80px;">
					<div onclick="guardarNewArticuloFactura('.$cont.');" id="divImageSaveFactura_'.$cont.'" title="Guardar Articulo" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="imgSaveFactura_'.$cont.'"/></div>
					<div onclick="retrocederArticuloFactura('.$cont.')" id="divImageDeshacer_'.$cont.'" title="Deshacer Cambios" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none"><img src="img/deshacer.png" id="imgDeshacerArticulo_'.$cont.'"></div>
					<div onclick="ventanaDescripcionArticuloFactura('.$cont.');" id="descripcionArticuloFactura_'.$cont.'" title="Agregar Observacion" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/edit.png"/></div>
					<div onclick="deleteArticuloFactura('.$cont.');" id="deleteArticuloFactura_'.$cont.'" title="Eliminar Articulo" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png" /></div>
				</div>

				<input type="hidden" id="idArticuloFactura_'.$cont.'" value="0" />
				<input type="hidden" class="classInputInsertArticuloFactura" id="idInsertArticuloFactura_'.$cont.'" value="0" />
				<input type="hidden" id="ivaArticuloFacturaCompra_'.$cont.'" value="0" >

				<script>
					document.getElementById("cantArticuloFactura_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloFactura(event,this,"",'.$cont.'); };
					document.getElementById("descuentoArticuloFactura_'.$cont.'").onkeyup = function(event){ return validarNumberArticuloFactura(event,this,"",'.$cont.'); };
				</script>';

		if($formaConsulta == 'return'){ return $body; }
		else{ echo $body; }
	}

	//=========================== FUNCION PARA MOSTRAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ==============================================//
	function ventanaDescripcionArticulofactura($cont,$idArticulo,$idFactura,$id_empresa,$idInsert,$filtro_bodega,$link){
		validaEstadoFactura($idFactura,$link);
		// $styleHidden = 'display:none;';
		$codigoCCos  = '';
		$nombreCCos  = '';

		$sqlItem   = "SELECT observaciones,
							id_impuesto,
							impuesto,
							id_centro_costos,
							check_opcion_contable
						FROM compras_facturas_inventario
						WHERE id_inventario='$idArticulo'
							AND id_factura_compra='$idFactura'
							AND id_empresa='$id_empresa'
							AND id_bodega='$filtro_bodega'
							AND id='$idInsert'
						LIMIT 0,1";
		$queryItem = mysql_query($sqlItem,$link);

		$observacion    = mysql_result($queryItem,0,'observaciones');
		$idDepartamento = mysql_result($queryItem,0,'id_departamento');
		$impuesto       = mysql_result($queryItem,0,'impuesto');
		$idImpuesto     = mysql_result($queryItem,0,'id_impuesto');
		$idCentroCostos = mysql_result($queryItem,0,'id_centro_costos');
		$checkOpcion    = mysql_result($queryItem,0,'check_opcion_contable');

		$sqlCentroCostos   = "SELECT id, codigo, nombre FROM centro_costos WHERE id='$idCentroCostos' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
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
			$selected = ($rowImpuesto['id'] == $idImpuesto)? 'selected': '';
			$optionImpuesto .= '<option value="'.$rowImpuesto['id'].'" '.$selected.'>'.$rowImpuesto['impuesto'].'</option>';
		}

		echo'<div style="margin: 0 10px">
				<div id="renderizaGuardarObservacionFactura_'.$cont.'" style="width:20px; height:20px; overflow:hidden;"></div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:5px; '.$styleHidden.'">
					<div style="float:left; width:24%;">Impuesto</div>
					<div style="width:75%; float:left; height:23px;">
						<select id="id_impuestoItem_fc" style="width:99%;">'.$optionImpuesto.'</select>
					</div>
				</div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:5px; '.$styleHidden.'">
					<div style="float:left; width:24%;">Centro de Costo</div>
					<div style="width:75%; float:left; height:23px;">
						<input type="text" id="id_ccos_fc" value="'.$idCentroCostos.'" style="display:none;"/>
						<input type="text" id="codigo_ccos_fc" onclick="ventana_centros_costos_fc()" value="'.$codigoCCos.'" style="width:29%; float:left; margin-right:1%" class="myfield" readonly/>
						<input type="text" id="nombre_ccos_fc" onclick="ventana_centros_costos_fc()" value="'.$nombreCCos.'" style="width:70%; float:left;" class="myfield" readonly/>
					</div>
				</div>
				<div style="width:100%; overflow:hidden; margin-top:5px;">
					<div>Observacion</div>
					<textarea id="observacionArticuloFactura_'.$cont.'" style="height:130px; width:99%; margin-bottom:5px;" class="myfield">'.$observacion.'</textarea>
				</div>
			</div>';
	}

	//=========================== FUNCION PARA GUARADAR DESCRIPCION O OBSERVACION AL ARTICULO DE FORMA INDIVIDUAL ==============================================//
	function guardarDescripcionArticuloFactura($cont,$idCentroCostos,$idImpuesto,$observacion,$idInventario,$idInsert,$idFactura,$id_empresa,$filtro_bodega,$link){
		validaEstadoFactura($idFactura,$link);

		//CONSULTA ITEM
		$sqlItemFactura = "SELECT id_impuesto,cantidad,tipo_descuento,descuento,costo_unitario
							FROM compras_facturas_inventario
							WHERE id_inventario = '$idInventario' AND id_factura_compra='$idFactura' AND id='$idInsert' AND id_empresa='$id_empresa' AND id_bodega='$filtro_bodega'
							LIMIT 0,1";
		$queryItemFactura = mysql_query($sqlItemFactura,$link);

		$idImpuestoBd   = mysql_result($queryItemFactura,0,'id_impuesto');
		$cantidad       = mysql_result($queryItemFactura,0,'cantidad');
		$tipo_descuento = mysql_result($queryItemFactura,0,'tipo_descuento');
		$descuento      = mysql_result($queryItemFactura,0,'descuento');
		$costo_unitario = mysql_result($queryItemFactura,0,'costo_unitario');

		$impuesto             = '';
		$valor_impuesto       = 0;
		$cuenta_impuesto      = '';
		$cuenta_impuesto_niif = '';

		if($idImpuesto > 0){
			$sqlImpuesto   = "SELECT cuenta_compra,cuenta_compra_niif,valor,impuesto FROM impuestos WHERE id='$idImpuesto' AND id_empresa='$id_empresa' AND activo=1 LIMIT 0,1";
			$queryImpuesto = mysql_query($sqlImpuesto,$link);

			$impuesto             = mysql_result($queryImpuesto, 0, 'impuesto');
			$valor_impuesto       = mysql_result($queryImpuesto, 0, 'valor');
			$cuenta_impuesto      = mysql_result($queryImpuesto, 0, 'cuenta_compra');
			$cuenta_impuesto_niif = mysql_result($queryImpuesto, 0, 'cuenta_compra_niif');
		}

		//UPDATE ITEM
		$sqlUpdateObservacion   = "UPDATE compras_facturas_inventario
									SET observaciones = '$observacion',
										impuesto = '$impuesto',
										id_impuesto = '$idImpuesto',
										valor_impuesto = '$valor_impuesto',
										cuenta_impuesto = '$cuenta_impuesto',
										cuenta_impuesto_niif = '$cuenta_impuesto_niif',
										id_centro_costos = '$idCentroCostos'
									WHERE id_inventario = '$idInventario' AND id_factura_compra='$idFactura' AND id='$idInsert' AND id_empresa='$id_empresa' AND id_bodega='$filtro_bodega'";
		$queryUpdateObservacion = mysql_query($sqlUpdateObservacion,$link);

		if($queryUpdateObservacion){
			if($idImpuestoBd != $idImpuesto){

				echo'<script>
						calcularValoresFactura('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$idImpuestoBd.'",'.$cont.');

						if(arrayIvaFacturaCompra['.$idImpuesto.'] == undefined){
							arrayIvaFacturaCompra['.$idImpuesto.'] = {
								nombre : "'.$impuesto.'",
								saldo  : 0,
								valor  : "'.$valor_impuesto.'"
							}
						}

						calcularValoresFactura('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"agregar","'.$tipo_descuento.'","'.$idImpuesto.'",'.$cont.');
						document.getElementById("ivaArticuloFacturaCompra_'.$cont.'").value="'.$idImpuesto.'";
					</script>';
			}

			echo'<script>Win_Ventana_descripcion_Articulo_factura.close(id);</script>';
		}
		else{ echo '<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
	}

	//=========================== FUNCION PARA BUSCAR UN ARTICULO ==============================================//
	function buscarArticuloFactura($valorArticulo,$contArticulo,$id_empresa,$id_sucursal,$idBodega,$link){
		$sqlArticulo = "SELECT
							I.id,
							I.codigo,
							I.code_bar,
							I.costos,
							I.nombre_equipo,
							I.numero_piezas,
							I.id_impuesto,
							I.estado_compra,
							I.opcion_costo,
							I.opcion_gasto,
							I.opcion_activo_fijo,
							I.unidad_medida,
							I.cantidad_unidades,
							IT.cantidad_maxima_stock,
							IT.cantidad
						FROM
							items AS I,
							inventario_totales AS IT
						WHERE I.activo=1
							AND I.id_empresa=$id_empresa
							AND (I.code_bar = '$valorArticulo' OR I.codigo = '$valorArticulo')
							AND IT.id_item=I.id
							AND IT.id_sucursal='$id_sucursal'
							AND IT.id_ubicacion='$idBodega'
							AND IT.id_empresa='$id_empresa'
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
		$cantidad_maxima_stock = mysql_result($query,0,'cantidad_maxima_stock');
		$cantidad_stock        = mysql_result($query,0,'cantidad');
		$estadoCompra          = mysql_result($query,0,'estado_compra');
		$opcionCosto           = mysql_result($query,0,'opcion_costo');
		$opcionGasto           = mysql_result($query,0,'opcion_gasto');
		$opcionActivoFijo      = mysql_result($query,0,'opcion_activo_fijo');

		//consultamos el valor del impuesto para asignarlo al campo oculto,
		$sqlImpuesto   = "SELECT impuesto,valor FROM impuestos WHERE id='$id_impuesto'";
		$queryImpuesto = mysql_query($sqlImpuesto,$link);
		$valorImpuesto = mysql_result($queryImpuesto,0,'valor');
		$impuesto = mysql_result($queryImpuesto,0,'impuesto');

		if ($impuesto!="" && $valorImpuesto!="") {
			$script = 'if (typeof(arrayIvaFacturaCompra['.$id_impuesto.'])=="undefined") {
							arrayIvaFacturaCompra['.$id_impuesto.']={nombre:"'.$impuesto.'",valor:"'.$valorImpuesto.'"};
						}';
		}

		if(!$query || !$queryImpuesto){
			echo'<script>
					document.getElementById("eanArticuloFactura_'.$contArticulo.'").blur()
					alert("Aviso,\nHa ocurrido un error en la consulta, si el problema persiste comuniquese con el administrador del sistema.");
					setTimeout(function(){ document.getElementById("eanArticuloFactura_'.$contArticulo.'").focus(); },100);
				</script>';
			exit;
		}

		if($id > 0 && $estadoCompra == "true"){
			if ($cantidad_stock==$cantidad_maxima_stock && $cantidad_maxima_stock>0) { echo '<script>alert("Aviso!\nEste articulo esta en su cantidad maxima de stock");</script>'; }
			else if ($cantidad_stock>$cantidad_maxima_stock && $cantidad_maxima_stock>0) { echo '<script>alert("Aviso!\nEste articulo supero la cantidad maxima de stock");</script>'; }
			echo'<script>
					document.getElementById("unidadesFactura_'.$contArticulo.'").value          ="'.$nombre_unidad.' x '.$numero_unidad.'";
					document.getElementById("idArticuloFactura_'.$contArticulo.'").value        ="'.$id.'";
					document.getElementById("eanArticuloFactura_'.$contArticulo.'").value       ="'.$codigo.'";
					document.getElementById("costoArticuloFactura_'.$contArticulo.'").value     ="'.$costos.'";
					document.getElementById("nombreArticuloFactura_'.$contArticulo.'").value    ="'.addslashes($nombreArticulo).'";
					document.getElementById("ivaArticuloFacturaCompra_'.$contArticulo.'").value ="'.$id_impuesto.'";
					'.$script.'
					setTimeout(function(){ document.getElementById("cantArticuloFactura_'.$contArticulo.'").focus(); },100);
				</script>';

			if($opcionCosto == 'true'){
				echo'<script>
						document.getElementById("div_check_factura_costo_'.$contArticulo.'").innerHTML =\'<input type="checkbox" id="check_factura_costo_'.$contArticulo.'" class="optionCheckContable_'.$contArticulo.'" onchange="changeCheckOptionContable('.$contArticulo.', this);"/>\';
					</script>';
			}
			if($opcionGasto == 'true'){
				echo'<script>
						document.getElementById("div_check_factura_gasto_'.$contArticulo.'").innerHTML =\'<input type="checkbox" id="check_factura_gasto_'.$contArticulo.'" class="optionCheckContable_'.$contArticulo.'" onchange="changeCheckOptionContable('.$contArticulo.', this);"/>\';
					</script>';
			}
			if($opcionActivoFijo == 'true'){
				echo'<script>
						document.getElementById("div_check_factura_activo_fijo_'.$contArticulo.'").innerHTML =\'<input type="checkbox" id="check_factura_activo_fijo_'.$contArticulo.'" class="optionCheckContable_'.$contArticulo.'" onchange="changeCheckOptionContable('.$contArticulo.', this);"/>\';
					</script>';
			}
		}
		else if($estadoCompra == 'false'){
			echo'<script>
					document.getElementById("eanArticuloFactura_'.$contArticulo.'").blur()
					alert("Codigo '.$valorArticulo.' No esta disponible en el modulo compras");
					setTimeout(function(){ document.getElementById("eanArticuloFactura_'.$contArticulo.'").focus(); },100);
				</script>';
		}
		else{
			echo'<script>
					document.getElementById("eanArticuloFactura_'.$contArticulo.'").blur();
					alert("El codigo '.$valorArticulo.' No se encuentra asignado en el inventario");
					setTimeout(function(){ document.getElementById("eanArticuloFactura_'.$contArticulo.'").focus(); },100);

					document.getElementById("idArticuloFactura_'.$contArticulo.'").value     ="0";
					document.getElementById("unidadesFactura_'.$contArticulo.'").value       ="";
					document.getElementById("costoArticuloFactura_'.$contArticulo.'").value  ="";
					document.getElementById("nombreArticuloFactura_'.$contArticulo.'").value ="";
				</script>';
		}
	}

	//=========================== FUNCION PARA DESHACER LOS CAMBIOS DE UN ARTICULO QUE SE MODIFICA==============================================//
	function retrocederArticuloFactura($idFactura,$idRegistro,$cont,$id_empresa,$link){
		$sql           = "SELECT id_inventario FROM compras_facturas_inventario WHERE id=$idRegistro";
		$queryRegistro = mysql_query($sql,$link);
		$idArticulo    = mysql_result($queryRegistro,0,'id_inventario');

		$sqlArticulo = "SELECT
											I.id,
											I.codigo,
											I.code_bar,
											I.cantidad_unidades,
											CF.costo_unitario,
											CF.nombre,
											CF.nombre_unidad_medida,
											CF.cantidad,
											CF.tipo_descuento,
											CF.descuento,
											CF.check_opcion_contable,
											CF.opcion_costo,
											CF.opcion_gasto,
											CF.opcion_activo_fijo,
											CF.id_impuesto,
											CF.id_consecutivo_referencia
										FROM items AS I,
											compras_facturas_inventario AS CF
										WHERE
											I.activo = 1
										AND
											I.id_empresa = $id_empresa
										AND
											I.id = $idArticulo
										AND
											CF.id_factura_compra = $idFactura
										AND
											CF.id = $idRegistro
										LIMIT
											0,1";

		$query 							       = mysql_query($sqlArticulo,$link);
		$id                        = mysql_result($query,0,'id');
		$codigo                    = mysql_result($query,0,'codigo');
		$costos                    = mysql_result($query,0,'costo_unitario');
		$codigoBarras              = mysql_result($query,0,'code_bar');
		$nombre_unidad             = mysql_result($query,0,'nombre_unidad_medida');
		$nombreArticulo            = mysql_result($query,0,'nombre');
		$numeroPiezas              = mysql_result($query,0,'cantidad_unidades');
		$cantidad_articulo         = mysql_result($query,0,'cantidad');
		$tipoDesc                  = mysql_result($query,0,'tipo_descuento');
		$descuento_articulo        = mysql_result($query,0,'descuento');
		$checkOpcionContable       = mysql_result($query,0,'check_opcion_contable');
		$opcionCosto               = mysql_result($query,0,'opcion_costo');
		$opcionGasto               = mysql_result($query,0,'opcion_gasto');
		$opcionActivoFijo          = mysql_result($query,0,'opcion_activo_fijo');
		$id_impuesto               = mysql_result($query,0,'id_impuesto');
		$id_consecutivo_referencia = mysql_result($query,0,'id_consecutivo_referencia');

		if ($tipoDesc=='porcentaje') {
			$imgDescuento    ='img/porcentaje.png';
			$tituloDescuento ='En porcentaje';
		}
		else{
			$imgDescuento    ='img/pesos.png';
			$tituloDescuento ='En pesos';
		}

		echo'<script>';
		if($opcionCosto == 'true'){
			echo'document.getElementById("div_check_factura_costo_'.$cont.'").innerHTML =\'<input type="checkbox" id="check_factura_costo_'.$cont.'" class="optionCheckContable_'.$cont.'" onchange="changeCheckOptionContable('.$cont.', this);"/>\';';
		}
		if($opcionGasto == 'true'){
			echo'document.getElementById("div_check_factura_gasto_'.$cont.'").innerHTML =\'<input type="checkbox" id="check_factura_gasto_'.$cont.'" class="optionCheckContable_'.$cont.'" onchange="changeCheckOptionContable('.$cont.', this);"/>\';';
		}
		if($opcionActivoFijo == 'true'){
			echo'document.getElementById("div_check_factura_activo_fijo_'.$cont.'").innerHTML =\'<input type="checkbox" id="check_factura_activo_fijo_'.$cont.'" class="optionCheckContable_'.$cont.'" onchange="changeCheckOptionContable('.$cont.', this);"/>\';';
		}
		if($checkOpcionContable != ''){ echo 'document.getElementById("check_factura_'.$checkOpcionContable.'_'.$cont.'").checked ="true";'; }
		if($id_consecutivo_referencia != ''){ echo 'document.getElementById("check_factura_'.$checkOpcionContable.'_'.$cont.'").disabled ="true";';}
		echo' document.getElementById("unidadesFactura_'.$cont.'").value            =  "'.$nombre_unidad.' x '.$numeroPiezas.'";
				  document.getElementById("idArticuloFactura_'.$cont.'").value         	=  "'.$id.'";
				  document.getElementById("eanArticuloFactura_'.$cont.'").value        	=  "'.$codigo.'";
				  document.getElementById("costoArticuloFactura_'.$cont.'").value      	=  "'.$costos.'";
				  document.getElementById("costoTotalArticuloFactura_'.$cont.'").value 	=  "'.$costos*$cantidad_articulo.'";
				  document.getElementById("nombreArticuloFactura_'.$cont.'").value     	=  "'.$nombreArticulo.'";
				  document.getElementById("cantArticuloFactura_'.$cont.'").value       	=  "'.$cantidad_articulo.'";
				  document.getElementById("descuentoArticuloFactura_'.$cont.'").value  	=  "'.$descuento_articulo.'";
				  document.getElementById("ivaArticuloFacturaCompra_'.$cont.'").value  	=  "'.$id_impuesto.'";

				  document.getElementById("tipoDescuentoArticulo_'.$cont.'").setAttribute("src","img/reload.png");
				  document.getElementById("imgDescuentoArticulo_'.$cont.'").setAttribute("src","'.$imgDescuento.'");
				  document.getElementById("imgDescuentoArticulo_'.$cont.'").setAttribute("title","'.$tituloDescuento.'");

				  document.getElementById("divImageSaveFactura_'.$cont.'").style.display  = "none";
				  document.getElementById("divImageDeshacer_'.$cont.'").style.display     = "none";
			  </script>';
	}

	//=========================== FUNCION PARA ELIMINAR UN ARTICULO Y LA FILA EN QUE SE ENCUENTRA ==============================================//
	function deleteArticuloFactura($cont,$idFactura,$idArticulo,$link){
		validaEstadoFactura($idFactura,$link);
		$sqlDelete   = "DELETE FROM compras_facturas_inventario WHERE id_factura_compra='$idFactura' AND id='$idArticulo'";
		$queryDelete = mysql_query($sqlDelete,$link);
		if(!$queryDelete){
			echo '<script>alert("No se puede eliminar el articulo, si el problema persiste favor comuniquese con el administrador del sistema");</script>';
		}
		else{ echo '<script>(document.getElementById("bodyDivArticulosFactura_'.$cont.'")).parentNode.removeChild(document.getElementById("bodyDivArticulosFactura_'.$cont.'"));</script>'; }
	}

	//=========================== FUNCION PARA AGREGAR O QUITAR RETENCIONES A LA FACTURA ==============================================//
	function checkboxRetenciones($idFactura,$idRetencion,$accion,$link){
		validaEstadoFactura($idFactura,$link);
		$sqlRetencion           = "";
		$sqlValorRetencion      = "SELECT valor FROM retenciones WHERE id='$idRetencion'";
		$querySqlValorRetencion = mysql_query($sqlValorRetencion,$link);
		$arraySqlValorRetencion = mysql_fetch_array($querySqlValorRetencion);

		if ($accion=="eliminar") {
			$sqlRetencion="DELETE FROM compras_facturas_retenciones WHERE id_factura_compra=$idFactura AND id_retencion=$idRetencion";
			echo'<script>
					retefuenteFacturaCompra=parseFloat(retefuenteFacturaCompra)-parseFloat('.$arraySqlValorRetencion['valor'].');
				</script>';
		}
		else if ($accion=="insertar") {
			$sqlRetencion="INSERT INTO compras_facturas_retenciones (id_factura_compra,id_retencion) VALUES ('$idFactura','$idRetencion')";
			echo'<script>
					retefuenteFacturaCompra=parseFloat(retefuenteFacturaCompra)+parseFloat('.$arraySqlValorRetencion['valor'].');
				</script>';
		}

		$queryRetencion = mysql_query($sqlRetencion,$link);
		if(!$queryRetencion){
			echo'<script>alert("No se logro '.$accion.' la retencion");</script>';
		}
	}

	//=========================== FUNCION PARA ACTUALIZAR LA FORMA DE PAGO ==============================================//
	function UpdateFormaPago($id,$idFormaPago,$link){
		validaEstadoFactura($idFactura,$link);
		//Actualizamos el id de la forma de pago en la tabla principal
		$sql   = "UPDATE compras_facturas SET id_forma_pago='$idFormaPago' WHERE id='$id'";
		$query = mysql_query($sql,$link);

		if ($query){ $return='<script>calcFechaLargaPagoFacturaCompra();</script>'; }
		else{
        	$return='<script>
        				document.getElementById("selectFormaPagoCompra").value = idFechaSavePagoFactura;
	        			alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");
	        		</script>';
		}
		echo $return;
	}

	//=========================== FUNCION PARA ACTUALIZAR LA CUENTA DE PAGO ==============================================//
	function UpdateCuentaPago($id,$idCuentaPago,$link){
		validaEstadoFactura($idFactura,$link);
		//Actualizamos el id de la forma de pago en la tabla principal
		$sql   = "UPDATE compras_facturas SET id_configuracion_cuenta_pago='$idCuentaPago' WHERE id='$id'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
	}

	//=========================== FUNCION PARA ACTUALIZAR LA PLANTILLA ==============================================//
	function UpdateIdPlantilla($id,$idPlantilla,$link){
		validaEstadoFactura($idFactura,$link);
		//Actualizamos el id de la forma de pago en la tabla principal
		$sql   = "UPDATE compras_facturas SET plantillas_id='$idPlantilla' WHERE id='$id'";
		$query = mysql_query($sql,$link);

		if (!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }
	}

	//=========================== FUNCION PARA GENERAR UNA NUEVA FACTURA ==============================================//
	function nuevaFacturaCompra(){
		echo'<script>
			 	Ext.getCmp("Btn_cancelar_FacturaCompra").disable();

				Ext.get("contenedor_facturacion_compras").load({
					url     : "facturacion/facturacion_compras.php",
					scripts : true,
					nocache : true,
					params  : { }
				});

			</script>';
	}

	function guardarArticuloFactura($consecutivo,$idFactura,$cont,$idInventario,$cantArticulo,$tipoDesc,$descuentoArticulo,$costo_unitario,$checkOpcionContable,$link){
		validaEstadoFactura($idFactura,$link);
		$sqlInsert = "INSERT INTO compras_facturas_inventario(
							id_factura_compra,
							id_inventario,
							cantidad,
							tipo_descuento,
							descuento,
							costo_unitario,
							check_opcion_contable)
						VALUES('$idFactura',
							'$idInventario',
							'$cantArticulo',
							'$tipoDesc',
							'$descuentoArticulo',
							'$costo_unitario',
							'$checkOpcionContable')";

		$queryInsert = mysql_query($sqlInsert,$link);

		$sqlLastId = "SELECT LAST_INSERT_ID()";
		$lastId    = mysql_result(mysql_query($sqlLastId,$link),0,0);

		if($lastId > 0){
			echo'<script>
					Ext.getCmp("Btn_cancelar_FacturaCompra").enable();

					document.getElementById("eanArticuloFactura_'.$consecutivo.'").focus();
					document.getElementById("divImageSaveFactura_'.$cont.'").setAttribute("title","Actualizar Articulo");
					document.getElementById("imgSaveFactura_'.$cont.'").setAttribute("src","img/reload.png");

					document.getElementById("idInsertArticuloFactura_'.$cont.'").value     = '.$lastId.';
					document.getElementById("divImageSaveFactura_'.$cont.'").style.display = "none";
					document.getElementById("divImageDeshacer_'.$cont.'").style.display    = "none";

					document.getElementById("descripcionArticuloFactura_'.$cont.'").style.display = "block";
					document.getElementById("deleteArticuloFactura_'.$cont.'").style.display      = "block";

				</script>'.cargaDivsInsertUnidades('echo',$consecutivo);
		}
		else{
			echo "Error, no se ha almacenado el articulo en esta factura, si el problema persiste favor comuniquese con la administracion del sistema ".$sqlInsert;
		}
	}

	function actualizaArticuloFactura($idFactura,$id,$cont,$idInventario,$cantArticulo,$tipoDescuento,$descuentoArticulo,$costoArticulo,$ivaArticulo,$checkOpcionContable,$link){
		validaEstadoFactura($idFactura,$link);
		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo
		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura

		$sqlArticuloAnterior   = "SELECT cantidad,tipo_descuento,descuento,costo_unitario,id_impuesto AS valor_impuesto FROM compras_facturas_inventario WHERE id='$id'";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);

		$cantidad       = mysql_result($queryArticuloAnterior,0,'cantidad');
		$tipo_descuento = mysql_result($queryArticuloAnterior,0,'tipo_descuento');
		$descuento      = mysql_result($queryArticuloAnterior,0,'descuento');
		$costo_unitario = mysql_result($queryArticuloAnterior,0,'costo_unitario');
		$valor_impuesto = mysql_result($queryArticuloAnterior,0,'valor_impuesto');

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		echo'<script>calcularValoresFactura('.$cantidad.',"'.$descuento.'",'.$costo_unitario.',"eliminar","'.$tipo_descuento.'","'.$valor_impuesto.'",'.$cont.');</script>';

		$sqlUpdateArticulo   = "UPDATE compras_facturas_inventario
								SET id_inventario ='$idInventario',
									cantidad ='$cantArticulo',
									tipo_descuento ='$tipoDescuento',
									descuento ='$descuentoArticulo',
									costo_unitario ='$costoArticulo',
									check_opcion_contable ='$checkOpcionContable'
								WHERE id_factura_compra=$idFactura AND id=$id";

		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);

		if ($queryUpdateArticulo) {
			echo'<script>
					document.getElementById("divImageSaveFactura_'.$cont.'").style.display = "none";
					document.getElementById("divImageDeshacer_'.$cont.'").style.display    = "none";

					calcularValoresFactura('.$cantArticulo.',"'.$descuentoArticulo.'",'.$costoArticulo.',"agregar","'.$tipoDescuento.'","'.$ivaArticulo.'",'.$cont.');
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo el articulo"); </script>'; }
	}

	function guardarObservacionFacturaCompra($observacion,$idFactura,$link){
		validaEstadoFactura($idFactura,$link);
		$observacion                = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlUpdateComprasFacturas   = "UPDATE compras_facturas SET  observacion='$observacion' WHERE id='$idFactura'";
		$queryUpdateComprasFacturas = mysql_query($sqlUpdateComprasFacturas,$link);

		if($queryUpdateComprasFacturas){ echo 'true'; }
		else{ echo'false'; }
	}

	function guardarFechaFactura($idInputDate,$idFactura,$valInputDate,$link){
		validaEstadoFactura($idFactura,$link);
		if($idInputDate=='fechaFactura'){ $sqlUpdateFecha = "UPDATE compras_facturas SET  fecha_inicio='$valInputDate' WHERE id='$idFactura'"; }
		else if($idInputDate=='fechaFinalFactura'){ $sqlUpdateFecha = "UPDATE compras_facturas SET  fecha_final='$valInputDate' WHERE id='$idFactura'"; }

		$queryUpdateFecha = mysql_query($sqlUpdateFecha,$link);
		if($queryUpdateFecha){ echo 'true'; }
		else{ echo'false'; }
	}

	//=========================== FUNCION PARA TERMINAR LA FACTURA Y CARGAR UNA NUEVA ==============================================//
	function terminarFacturaCompra($idPlantilla,$idProveedor,$nitProveedor,$idFactura,$prefijoFactura,$numeroFactura,$id_empresa,$id_sucursal,$idBodega,$observacion,$link){
		validaEstadoFactura($idFactura,$link);

		if(validarNumeroFactura($prefijoFactura,$numeroFactura,$idFactura,$nitProveedor,$id_empresa,$link)){
			echo '<script>
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
			exit;
		}  
		
		// VERIFICAR SI ES UN DOCUMENTO SOPORTE  

		global $saldoGlobalFactura, $saldoGlobalFacturaSinAbono, $cuentaPago, $cuentaPagoNiif;

		$sqlSaldoRemision   = "UPDATE compras_facturas_inventario SET saldo_cantidad=cantidad WHERE id_factura_compra='$idFactura' AND id_empresa='$id_empresa'";
		$querySaldoRemision = mysql_query($sqlSaldoRemision,$link);

		//===================================== CREACION DE ARRAY DOCUMENTOS DE REFERENCIA =============================================//
		/********************************************************************************************************************************/
		$arraySindDoc        = '';
		$arrayOrdenCompra    = '';
		$arrayEntradaAlmacen = '';

		$contOrdenCompra    = 0;
		$contEntradaAlmacen = 0;
		$contSinDoc         = 0;

		$acumIdOrdenCompra    = '';		//CONDICIONAL GLOBAL WHERE SQL IDS ORDEN DE COMPRA
		$acumIdEntradaAlmacen = '';		//CONDICIONAL GLOBAL WHERE SQL IDS ENTRADA DE ALMACEN

		$sqlDocumentoAdjunto = "SELECT id_consecutivo_referencia AS id_referencia, nombre_consecutivo_referencia AS nombre_referencia
								FROM compras_facturas_inventario
								WHERE id_factura_compra='$idFactura' AND activo=1
								GROUP BY id_consecutivo_referencia, nombre_consecutivo_referencia";

		$queryDocumentoAdjunto = mysql_query($sqlDocumentoAdjunto,$link);
		while($rowDoc = mysql_fetch_array($queryDocumentoAdjunto)){

			$id_referencia     = $rowDoc['id_referencia'];
			$nombre_referencia = $rowDoc['nombre_referencia'];
			$arrayResult       = array('id_referencia' => $id_referencia, 'nombre_referencia' => $nombre_referencia);

			if($id_referencia > 0){																								//CON DOCUMENTO DE REFERENCIA
				if($nombre_referencia == 'Orden de Compra'){ $contOrdenCompra++; $arrayOrdenCompra[$contOrdenCompra] = $arrayResult; }
				else if($nombre_referencia == 'Entrada de Almacen'){ $contEntradaAlmacen++; $arrayEntradaAlmacen[$contEntradaAlmacen] = $arrayResult; }
			}
			else{ $contSinDoc++; $arraySindDoc[$contSinDoc][$id_referencia] = $nombre_referencia; }								//SIN DOCUMENTO DE REFERENCIA
		}

		// VALIDACION SI LA FACTURA CONTIENE UNIDADES MAYORES A LA ENTRADA DE ALMACEN ==>
		if($contEntradaAlmacen>0){
			$sql = " SELECT COUNT(TI.id) AS cont_validate_saldo
						FROM compras_facturas_inventario AS TI, compras_entrada_almacen_inventario AS TS
						WHERE
							TI.id_factura_compra='$idFactura'
						AND TI.activo = 1
						AND TS.activo = 1
						AND TI.nombre_consecutivo_referencia='Entrada de Almacen'
						AND TS.id=TI.id_tabla_referencia
						AND TI.cantidad > TS.saldo_cantidad
						GROUP BY TI.id";
			$query = mysql_query($sql,$link);
			$cont_validate_saldo = mysql_result($query,0,'cont_validate_saldo');
			if($cont_validate_saldo > 0){
				echo '<script>
						alert("Aviso!\nExisten cantidad de unidades mayores a las relacionadas en la entrada de almacen que se adjunto en la presente factura");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
					exit;
			}
		}

		//====================== VALIDACIONES DOCUMENTOS REFERENCIA ======================//
		/**********************************************************************************/
		if($contOrdenCompra>0){																									//VALIDACION ORDEN DE COMPRA
			for($cont=1; $cont<=$contOrdenCompra; $cont++) {
				$acumIdOrdenCompra .= ($acumIdOrdenCompra=='')? "id=":" OR id=";
				$acumIdOrdenCompra .= $arrayOrdenCompra[$cont]['id_referencia'];
			}

			$sqlEstadoCotizacion   = "SELECT consecutivo,estado,activo FROM compras_ordenes_inventario WHERE id_empresa=$id_empresa AND ($acumIdOrdenCompra)";
			$queryEstadoCotizacion = mysql_query($sqlEstadoCotizacion);
			while ($rowEstadoCotizacion = mysql_fetch_array($queryEstadoCotizacion)) {
				if($rowEstadoCotizacion['estado']==3){
					echo '<script>
							alert("Error!\nLa Orden codigo '.$rowEstadoCotizacion['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>'; exit;
				}
				if($rowEstadoCotizacion['estado']==0){
					echo '<script>
							alert("Error!\nLa Orden codigo '.$rowEstadoCotizacion['consecutivo'].' esta editada\ngenere el documento o elimine los articulos relacionados a esta para continuar.")
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>'; exit;
				}
			}

			actualizarSaldoCantidadDocumentoCargado($idFactura,'validar',$id_empresa,$link);				//VALIDAR EL CAMPO SALDO CANTIDAD DEL DOCUMENTO CARGADO
		}

		if($contEntradaAlmacen>0){																									//VALIDACION ORDEN DE COMPRA
			for($cont=1; $cont<=$contEntradaAlmacen; $cont++) {
				$acumIdEntradaAlmacen .= ($acumIdEntradaAlmacen=='')? "id=":" OR id=";
				$acumIdEntradaAlmacen .= $arrayEntradaAlmacen[$cont]['id_referencia'];
			}

			$sqlEstadoCotizacion   = "SELECT consecutivo,estado,activo FROM compras_entrada_almacen WHERE id_empresa=$id_empresa AND ($acumIdEntradaAlmacen)";
			$queryEstadoCotizacion = mysql_query($sqlEstadoCotizacion);
			while ($rowEstadoCotizacion = mysql_fetch_array($queryEstadoCotizacion)) {
				if($rowEstadoCotizacion['estado']==3){
					echo '<script>
							alert("Error!\nLa Entrada de Almacen codigo '.$rowEstadoCotizacion['consecutivo'].' esta Cancelada\nrestaure el documento o elimine los articulos relacionados a esta para continuar.")
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>'; exit;
				}
				if($rowEstadoCotizacion['estado']==0){
					echo '<script>
							alert("Error!\nLa Entrada de Almacen codigo '.$rowEstadoCotizacion['consecutivo'].' esta Editada\nguarde el documento o elimine los articulos relacionados a esta para continuar.")
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>'; exit;
				}
			}

			actualizarSaldoCantidadDocumentoCargado($idFactura,'validar',$id_empresa,$link);				//VALIDAR EL CAMPO SALDO CANTIDAD DEL DOCUMENTO CARGADO
		}

		$sqlGeneraFact   = "UPDATE compras_facturas
							SET id_sucursal = '$id_sucursal',
								id_bodega = '$idBodega',
								cuenta_pago = '$cuentaPago',
								cuenta_pago_niif = '$cuentaPagoNiif',
								observacion = '$observacion',
								estado = '1',
								prefijo_factura = '$prefijoFactura',
								numero_factura = '$numeroFactura',
								plantillas_id = '$idPlantilla'
							WHERE id='$idFactura'";
		$queryGeneraFact = mysql_query($sqlGeneraFact,$link);
		
		$sqlFacturaCompra   = "SELECT
									COUNT(id) AS cont_factura,
									fecha_inicio,
									estado,
									activo,
									id_configuracion_cuenta_pago,
									cuenta_pago,
									cuenta_pago_niif,
									contabilidad_manual,
									consecutivo,
									tipo_documento
								FROM compras_facturas
								WHERE id_empresa=$id_empresa AND id='$idFactura'";
		$queryfacturaCompra = mysql_query($sqlFacturaCompra,$link);

		// $numeroFactura  = mysql_result($queryfacturaCompra,0,'consecutivo');
		$consecutivoFactura  = mysql_result($queryfacturaCompra,0,'consecutivo');
		$tipo_documento      = mysql_result($queryfacturaCompra,0,'tipo_documento');
		$contFactura         = mysql_result($queryfacturaCompra,0,'cont_factura');
		$estadoFactura       = mysql_result($queryfacturaCompra,0,'estado');
		$activoFactura       = mysql_result($queryfacturaCompra,0,'activo');
		$fechaInicioFactura  = mysql_result($queryfacturaCompra,0,'fecha_inicio');
		$contabilidad_manual = mysql_result($queryfacturaCompra,0,'contabilidad_manual');
		$consecutivoFactura  = ($tipo_documento=="05")? "$prefijoFactura $consecutivoFactura" : $consecutivoFactura;

		$idCuentaPago   = mysql_result($queryfacturaCompra,0,'id_configuracion_cuenta_pago');
		$cuentaPago     = mysql_result($queryfacturaCompra,0,'cuenta_pago');
		$cuentaPagoNiif = mysql_result($queryfacturaCompra,0,'cuenta_pago_niif');

		if(!$queryfacturaCompra){
			echo'<script>
					alert("Error!\nNo se ha establecido la comunicacion con el servidor.");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		else if($contFactura == 0 || $activoFactura == 0){
			echo'<script>
					alert("Aviso!\nLa factura se encuentra cancelada");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}
		// else if($estadoFactura > 0){
		// 	echo'<script>
		// 			alert("Aviso!\nLa factura no se ncuentra disponible para generar");
		// 			document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
		// 		</script>';
		// 	return;
		// }

		//CUENTA DE PAGO ESTADO (credito-contado)
		$sqlEstadoCuentaPago   = "SELECT estado FROM configuracion_cuentas_pago WHERE id='$idCuentaPago' AND id_empresa='$id_empresa' AND tipo='Compra'";
		$queryEstadoCuentaPago = mysql_query($sqlEstadoCuentaPago,$link);
		$estadoCuentaPago      = mysql_result($queryEstadoCuentaPago, 0, 'estado');

		$arrayCuentaPago = array('cuentaColgaap' => $cuentaPago, 'cuentaNiif' => $cuentaPagoNiif, 'estado' => $estadoCuentaPago);

		//ANTICIPOS
		$arrayAnticipo = array('total'=>0);
		$sqlAnticipo   = "SELECT id,id_cuenta_anticipo,id_documento_anticipo,tipo_documento_anticipo,consecutivo_documento_anticipo,cuenta_colgaap,cuenta_niif,id_tercero,nit_tercero,tercero,valor
							FROM anticipos
							WHERE id_documento='$idFactura'
								AND tipo_documento='FC'
								AND id_empresa='$id_empresa'
								AND activo=1
								AND valor>0";
		$queryAnticipo =  mysql_query($sqlAnticipo,$link);
		while ($rowAnticipo = mysql_fetch_assoc($queryAnticipo)) {

			$idAnticipo  = $rowAnticipo['id'];

			$arrayAnticipo['total'] += $rowAnticipo['valor']*1;

			$arrayAnticipo['anticipos'][$idAnticipo]['valor']          = $rowAnticipo['valor'];
			$arrayAnticipo['anticipos'][$idAnticipo]['id_tercero']     = $rowAnticipo['id_tercero'];
			$arrayAnticipo['anticipos'][$idAnticipo]['cuenta_niif']    = $rowAnticipo['cuenta_niif'];
			$arrayAnticipo['anticipos'][$idAnticipo]['cuenta_colgaap'] = $rowAnticipo['cuenta_colgaap'];
			$arrayAnticipo['anticipos'][$idAnticipo]['consecutivo']    = $rowAnticipo['consecutivo_documento_anticipo'];
			$arrayAnticipo['anticipos'][$idAnticipo]['id_documento']   = $rowAnticipo['id_documento_anticipo'];
			$arrayAnticipo['anticipos'][$idAnticipo]['tipo_documento'] = $rowAnticipo['tipo_documento_anticipo'];
		}

		if($arrayAnticipo['total'] > 0){

			$sqlValidaAnticipos = "SELECT COUNT(A.id) AS contAnticipo
									FROM comprobante_egreso_cuentas AS C, anticipos AS A
									WHERE C.id=A.id_cuenta_anticipo
										AND C.activo=1
										AND A.activo=1
										AND A.id_documento='$idFactura'
										AND A.tipo_documento='FC'
										AND C.saldo_pendiente < A.valor";
			$queryValidaAnticipos = mysql_query($sqlValidaAnticipos,$link);
			$conFailAnticipo = mysql_result($queryValidaAnticipos, 0, 'contAnticipo');

			if(!$queryValidaAnticipos || $conFailAnticipo>0){
				echo '<script>
								alert("Aviso,\nHay anticipos que superan el valor registrado, por favor realice las correcciones respectivas.");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
							exit;
			}
		}

		//=================================== UPDATE ======================================//
		/***********************************************************************************/
		$prefijoFactura = str_replace(" ", "", $prefijoFactura);  // QUITAMOS LOS ESPACIOS VACIOS DEL PREFIJO
		
		if($tipo_documento=="05"){
			$consecutivoDocReferencia = $numeroFactura;
		}
		else{
			$consecutivoDocReferencia = (strlen($prefijoFactura) > 0)? $prefijoFactura.' '.$numeroFactura: $numeroFactura; // COMBINACION DE PREFIJO + NUMERO FACTURA
		}


		// CONTABILIZACION FACTURA
		if($idPlantilla > 0){ 					//CONTABILIZACION SIN PLANTILLA
			contabilizarConPlantilla($fechaInicioFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$idPlantilla,$idFactura,$idProveedor,$link);
			contabilizarConPlantillaNiif($fechaInicioFactura,$consecutivoFactura,$idBodega,$id_sucursal,$id_empresa,$idPlantilla,$idFactura,$idProveedor,$link);
		}
		else {
			if ($contabilidad_manual=='true') {
				contabilizarSinPlantillaManual($arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$consecutivoDocReferencia,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link); //COLGAAP
				contabilizarSinPlantillaManualNiif($arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$consecutivoDocReferencia,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link); //NIIF
			}
			else{								//CONTABILIZACION CON PLANTILLA
				contabilizarSinPlantilla($arrayAnticipo,$arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$consecutivoDocReferencia,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link);
				contabilizarSinPlantillaNiif($arrayAnticipo,$arrayCuentaPago,$fechaInicioFactura,$consecutivoFactura,$consecutivoDocReferencia,$idBodega,$id_sucursal,$id_empresa,$idFactura,$idProveedor,$link);
			}
		}

		//ACTUALIZAMOS LA FACTURA PARA DAR POR TERMINADA
		$sqlGeneraFact   = "UPDATE compras_facturas
							SET total_factura = '$saldoGlobalFactura',
								total_factura_sin_abono = '$saldoGlobalFacturaSinAbono'
							WHERE id='$idFactura'";
		$queryGeneraFact = mysql_query($sqlGeneraFact,$link);

		// $sqlConsecutivo = "SELECT consecutivo FROM compras_facturas WHERE id='$idFactura'";
  //   $queryConsecutivo   = mysql_query($sqlConsecutivo,$link);
  //   $consecutivoFactura = mysql_result($queryConsecutivo, 0, 'consecutivo');

    // $sqlAsientosColgaap =  "UPDATE asientos_colgaap
    //                         SET
	// 							consecutivo_documento='$consecutivoFactura',
    //                           	numero_documento_cruce=IF(numero_documento_cruce > 0, numero_documento_cruce, '$consecutivoDocReferencia')
    //                         WHERE id_documento='$idFactura'
    //                         AND tipo_documento='FC'";
    // $queryAsientosColgaap = mysql_query($sqlAsientosColgaap,$link);

    // $sqlAsientosNiif = "UPDATE asientos_niif
    //                     SET
	// 						consecutivo_documento='$consecutivoFactura',
    //                       	numero_documento_cruce= IF(numero_documento_cruce > 0, numero_documento_cruce, '$consecutivoDocReferencia')
    //                     WHERE id_documento='$idFactura'
    //                     AND tipo_documento='FC'";
    // $queryAsientosNiif = mysql_query($sqlAsientosNiif,$link);

		if($arrayAnticipo['total'] > 0){
			$sqlUpdate = "UPDATE anticipos SET consecutivo_documento='$consecutivoFactura' WHERE id_empresa='$id_empresa' AND id_documento='$idFactura' AND tipo_documento='FC'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
		}

		if ($queryGeneraFact){
			actualizarSaldoCantidadDocumentoCargado($idFactura,'eliminar',$id_empresa,$link);				//DESCONTAR LOS EL CAMPO SALDO CANTIDAD DEL DOCUMENTO CARGADO
			actualizarCantidadArticulos($idFactura,'ingreso',"Generar");		//ACTUALIZAR LA CANTIDAD DE ARTICULOS
			actualizaCantidadActivosFijos($idFactura,$id_sucursal,'agregar',$id_empresa,$link); //ACTUALIZAR LA CANTIDAD DE ACTIVOS
		}
		else{
			echo'<script>
					alert("Error!,\nNo se finalizo la factura\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>'; return;
		}

		if($contOrdenCompra>0){																							//UPDATE ORDEN DE COMPRA
			$sqlUpdateEstado = "UPDATE compras_ordenes SET estado=2 WHERE id_empresa=$id_empresa AND ($acumIdOrdenCompra)";
			mysql_query($sqlUpdateEstado,$link);
		}

		if($contEntradaAlmacen>0){																							//UPDATE ORDEN DE COMPRA
			$sqlUpdateEstado = "UPDATE compras_entrada_almacen SET estado=2 WHERE id_empresa=$id_empresa AND ($acumIdEntradaAlmacen)";
			mysql_query($sqlUpdateEstado,$link);
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					     VALUES($idFactura,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','FC','Factura de Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

		echo '<script>
						document.getElementById("titleDocuementoFacturaCompra").innerHTML="Consecutivo<br>N. '.$consecutivoFactura.'";
						Ext.get("contenedor_facturacion_compras").load({
							url     : "facturacion/facturacion_compras_bloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								id_factura_compra : "'.$idFactura.'",
								filtro_bodega     : "'.$idBodega.'"
							}
						});
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	}

	//=========================== FUNCION PARA RESTAR O AGREGAR EL SALDO CANTIDAD DE LOS DOCUMENTOS RELACIONADOS =============================//
	function actualizarSaldoCantidadDocumentoCargado($idFactura,$accion,$id_empresa,$link){

		//AUMENTAR LA CANTIDAD
		if ($accion=='agregar') {
			$sql  = "UPDATE compras_ordenes_inventario AS IT,
						(
							SELECT
								id_tabla_referencia,
								id_inventario,
								saldo_cantidad
							FROM
								compras_facturas_inventario
							WHERE
								activo = 1
							AND id_factura_compra = $idFactura
							AND nombre_consecutivo_referencia <> ''
							AND id_empresa = $id_empresa
							AND nombre_consecutivo_referencia = 'Orden de Compra'
						) AS CFI
					SET IT.saldo_cantidad = IT.saldo_cantidad + CFI.saldo_cantidad
					WHERE
						IT.id            = CFI.id_tabla_referencia
					AND IT.id_inventario = CFI.id_inventario
					AND IT.activo        = 1";
			$query = mysql_query($sql,$link);
			if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron los saldos del(os) documento(s) Relacionado(s)");</script>'; }

			$sql  = "UPDATE compras_entrada_almacen_inventario AS IT,
						(
							SELECT
								id_tabla_referencia,
								id_inventario,
								saldo_cantidad
							FROM
								compras_facturas_inventario
							WHERE
								activo = 1
							AND id_factura_compra = $idFactura
							AND nombre_consecutivo_referencia <> ''
							AND id_empresa = $id_empresa
							AND nombre_consecutivo_referencia = 'Entrada de Almacen'
						) AS CFI
					SET IT.saldo_cantidad = IT.saldo_cantidad + CFI.saldo_cantidad
					WHERE
						IT.id            = CFI.id_tabla_referencia
					AND IT.id_inventario = CFI.id_inventario
					AND IT.activo        = 1";
			$query = mysql_query($sql,$link);
			if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron los saldos del(os) documento(s) Relacionado(s)");</script>'; }

		}
		//RESTAR CANTIDAD
		else if ($accion=='eliminar') {
			$sql  = "UPDATE compras_ordenes_inventario AS IT,
						(
							SELECT
								id_tabla_referencia,
								id_inventario,
								saldo_cantidad
							FROM
								compras_facturas_inventario
							WHERE
								activo = 1
							AND id_factura_compra = $idFactura
							AND nombre_consecutivo_referencia <> ''
							AND nombre_consecutivo_referencia = 'Orden de Compra'
							AND id_empresa = $id_empresa
						) AS CFI
					SET IT.saldo_cantidad = IT.saldo_cantidad - CFI.saldo_cantidad
					WHERE
						IT.id            = CFI.id_tabla_referencia
					AND IT.id_inventario = CFI.id_inventario
					AND IT.activo        = 1";
			$query = mysql_query($sql,$link);
			if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron los saldos del las ordenes de compra");</script>'; }

			$sql  = "UPDATE compras_entrada_almacen_inventario AS IT,
						(
							SELECT
								id_tabla_referencia,
								id_inventario,
								saldo_cantidad
							FROM
								compras_facturas_inventario
							WHERE
								activo = 1
							AND id_factura_compra = $idFactura
							AND nombre_consecutivo_referencia <> ''
							AND nombre_consecutivo_referencia = 'Entrada de Almacen'
							AND id_empresa = $id_empresa
						) AS CFI
					SET IT.saldo_cantidad = IT.saldo_cantidad - CFI.saldo_cantidad
					WHERE
						IT.id            = CFI.id_tabla_referencia
					AND IT.id_inventario = CFI.id_inventario
					AND IT.activo        = 1";
			$query = mysql_query($sql,$link);
			if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron las entradas de almacen)");</script>'; }

		}
		else if ($accion=='validar') {

			//QUERY PARA COMPARAR LAS CANTIDADES DE LOS ARTICULOS CARGADOS Y RELACIONADOS
			$sqlArticulos = "SELECT
								CFI.cantidad,
								CFI.nombre,
								CFI.id_tabla_referencia,
								CFI.consecutivo_referencia,
								COI.saldo_cantidad
							FROM
								compras_facturas_inventario AS CFI
							INNER JOIN compras_ordenes_inventario AS COI ON CFI.id_tabla_referencia = COI.id
							WHERE
								CFI.id_factura_compra = $idFactura
							AND CFI.id_tabla_referencia <> ''
							AND COI.nombre_consecutivo_referencia = 'Orden de Compra'";
			$queryArticulos = mysql_query($sqlArticulos,$link);
			while ($row=mysql_fetch_array($queryArticulos)) {

				if ($row['cantidad']>$row['saldo_cantidad']) {
					echo '<script>
							alert("Error!\nel articulo '.$row['nombre'].' cargado de la orden de compra '.$row['consecutivo_referencia'].'\nExede la cantidad disponible, solo quedan '.$row['saldo_cantidad'].'\nVerifiquelo e intentelo de nuevo");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			//QUERY PARA COMPARAR LAS CANTIDADES DE LOS ARTICULOS CARGADOS Y RELACIONADOS
			$sqlArticulos = "SELECT
								CFI.cantidad,
								CFI.nombre,
								CFI.id_tabla_referencia,
								CFI.consecutivo_referencia,
								COI.saldo_cantidad
							FROM
								compras_facturas_inventario AS CFI
							INNER JOIN compras_entrada_almacen_inventario AS COI ON CFI.id_tabla_referencia = COI.id
							WHERE
								CFI.id_factura_compra = $idFactura
							AND CFI.id_tabla_referencia <> ''
							AND COI.nombre_consecutivo_referencia = 'Entrada de Almacen'";
			$queryArticulos = mysql_query($sqlArticulos,$link);
			while ($row=mysql_fetch_array($queryArticulos)) {

				if ($row['cantidad']>$row['saldo_cantidad']) {
					echo '<script>
							alert("Error!\nel articulo '.$row['nombre'].' cargado de la entrada de almacen '.$row['consecutivo_referencia'].'\nExede la cantidad disponible, solo quedan '.$row['saldo_cantidad'].'\nVerifiquelo e intentelo de nuevo");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
					exit;
				}
			}

			return true;
		}



		// if (!$query) { echo '<script>alert("Error!\nNo se Actualizaron los saldos del(os) documento(s) Relacionado(s)");</script>'; }
		// else{
			//ACTUALIZAR EL CAMPO pendiente_facturar de la tabla de cabecera
			$sqlUpdatePendientes = "UPDATE compras_ordenes,
									 (
										SELECT
											SUM(CO.saldo_cantidad) AS saldo,
											CO.id_orden_compra,
											CFI.consecutivo_referencia
										FROM
											compras_ordenes_inventario AS CO
										INNER JOIN compras_facturas_inventario AS CFI ON CO.id = CFI.id_tabla_referencia
										WHERE
											CFI.id_factura_compra = $idFactura
										GROUP BY
											CFI.consecutivo_referencia
									) AS CF
									SET pendientes_facturar = CF.saldo
									WHERE
										consecutivo = CF.consecutivo_referencia
									AND id_empresa = $id_empresa";
			$query2 = mysql_query($sqlUpdatePendientes,$link);

			if (!$query2) { echo '<script>alert("Error!\nNo se Actualizaron el saldo pendiente de la orden de compra");</script>'; }

			$sqlUpdatePendientes = "UPDATE compras_entrada_almacen,
									 (
										SELECT
											SUM(CO.saldo_cantidad) AS saldo,
											CO.id_entrada_almacen,
											CFI.consecutivo_referencia
										FROM
											compras_entrada_almacen_inventario AS CO
										INNER JOIN compras_facturas_inventario AS CFI ON CO.id = CFI.id_tabla_referencia
										WHERE
											CFI.id_factura_compra = $idFactura
										GROUP BY
											CFI.consecutivo_referencia
									) AS CF
									SET pendientes_facturar = CF.saldo
									WHERE
										consecutivo = CF.consecutivo_referencia
									AND id_empresa = $id_empresa";
			$query2 = mysql_query($sqlUpdatePendientes,$link);
			if (!$query2) { echo '<script>alert("Error!\nNo se Actualizaron el saldo pendiente de la entrada de almacen");</script>'; }
		// }
	}

	//=========================== FUNCION PARA INSERTAR EL TOTAL DE CANTIDADES DE ARTICULOS CON ACTIVOS FIJO  ===================================//
	function actualizaCantidadActivosFijos($id,$id_sucursal,$accion,$id_empresa,$link){
		// CONSULTAR EL CONSECUTIVO DE LA FACTURA DE COMPRA
		$sqlConsecutivo     = "SELECT consecutivo FROM compras_facturas WHERE id='$id'";
		$queryConsecutivo   = mysql_query($sqlConsecutivo,$link);
		$consecutivoFactura = mysql_result($queryConsecutivo, 0, 'consecutivo');

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
		  	// print_r($arrayClasificacion);
			$sql = "SELECT
							CFI.codigo AS code_bar,
							CF.id_bodega,
							CFI.nombre AS nombre_equipo,
							CF.id_proveedor,
							CF.nit AS nit_proveedor,
							CF.proveedor,
							CF.fecha_inicio AS fecha_compra,
							CFI.nombre_unidad_medida AS unidad,
							CFI.costo_unitario AS costo,
							CFI.tipo_descuento,
							CFI.descuento,
							CFI.valor_impuesto,
							CFI.id_inventario AS id_item,
							CF.consecutivo AS documento_referencia_consecutivo,
							CFI.id AS id_documento_referencia_inventario,
							CFI.cantidad,
							CFI.id_centro_costos,
							(SELECT id_subgrupo FROM items WHERE id=CFI.id_inventario) AS id_subgrupo
						FROM
							compras_facturas_inventario AS CFI
						LEFT JOIN
							compras_facturas AS CF ON CF.id = CFI.id_factura_compra
						WHERE
							CFI.id_factura_compra = $id
						AND
							CFI.activo = 1
						AND
							CFI.check_opcion_contable = 'activo_fijo'
						AND CFI.nombre_consecutivo_referencia <> 'Entrada de Almacen'
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

				// echo $arrayClasificacion[$row['id_item']];

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
									'FC',
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
		  				AND documento_referencia = 'FC'
		  				AND id_documento_referencia = '$id'
		  				AND documento_referencia_consecutivo = '$consecutivoFactura'
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
	  				AND documento_referencia = 'FC'
	  				AND id_documento_referencia = '$id'
	  				AND documento_referencia_consecutivo = '$consecutivoFactura'
	  				";
  			$query=mysql_query($sql,$link);
  			while ($row=mysql_fetch_array($query)) {
  				$whereActivo .=($whereActivo=='')? " id_activo=$row[id] " : " OR id_activo=$row[id] ";
			}

			// ELIMINAR LAS CUENTAS INSERTADAS POR DEFECTO
			$sql="DELETE FROM activos_fijos_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND ($whereActivo) ";
			$query=mysql_query($sql,$link);

	  		$sql="UPDATE activos_fijos SET activo=0 WHERE activo=1 AND id_empresa=$id_empresa AND documento_referencia='FC' AND id_documento_referencia='$id' ";
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
		$sql="SELECT COUNT(id) AS cont FROM activos_fijos WHERE activo=1 AND id_empresa=$id_empresa AND documento_referencia='FC' AND id_documento_referencia='$id' AND estado='1' ";
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

	//=========================== FUNCION PARA AGREGAR O ELIMINAR LOS ARTICULOS DEL INVENTARIO ======================================//
	function actualizarCantidadArticulos($id_factura,$accion_inventario,$accion_documento){
		
		global $mysql;
		// consultar la informacion del documento
		$sql = "SELECT id_sucursal,sucursal,id_bodega,bodega,id_empresa,consecutivo,fecha_inicio
				FROM compras_facturas WHERE id=$id_factura";
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
					FROM compras_facturas_inventario 
					WHERE id_factura_compra=$id_factura
					AND activo=1 
					AND nombre_consecutivo_referencia<>'Entrada de Almacen'
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
			"documento_id"          => $id_factura,
			"documento_tipo"        => "FC",
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

	//================================= AGREGAR ORDEN DE COMPRA ================================//
	function AgregarOrdenCompra($idProveedorFactura,$codDocAgregar,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$link,$confirmar,$opcCargar){
		validaEstadoFactura($idFactura,$link);

		//VERIFICAMOS CUAL TABLA SE VA A CARGAR CON LA VARIABLE OPCCARGAR
		if ($opcCargar=='orden_compra') {
			$tablaCarga            ='compras_ordenes';
			$idTablaCarga          ='id_orden_compra';
			$tablaCargaInventario  ='compras_ordenes_inventario';
			$nombreReferencia      ='Orden de Compra';
			$referenciaInput       = 'OC';
			$campoSelectValidacion = ",autorizado";
		}
		else if ($opcCargar=='compras_entrada_almacen') {
			$tablaCarga           ='compras_entrada_almacen';
			$idTablaCarga         ='id_entrada_almacen';
			$tablaCargaInventario ='compras_entrada_almacen_inventario';
			$nombreReferencia     ='Entrada de Almacen';
			$referenciaInput      = 'EA';

		}

		//VALIDACION ESTADO DEL DOCUMENTO A CRUZAR
		$idProveedorDocAgregar  = '';
		$estadoDocAgregar       = '';
		$sqlValidateDocumento   = "SELECT id_proveedor,estado,id,pendientes_facturar $campoSelectValidacion FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa'";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);

		$idProveedorDocAgregar = mysql_result($queryValidateDocumento,0,'id_proveedor');
		$idDocumentoAgregar    = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar      = mysql_result($queryValidateDocumento,0,'estado');
		$pendientes_facturar   = mysql_result($queryValidateDocumento,0,'pendientes_facturar');

		// VERIFICAR SI ES ORDEN DE COMPRA, QUE ESTE AUTORIZADO SI DA A LUGAR
		if ($opcCargar=='orden_compra') {
			$autorizado = mysql_result($queryValidateDocumento,0,'autorizado');

			// CONSULTAR SI TIENE AUTORIZACION POR PRECIO
			$sql="SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
			$aut_precio = mysql_result($query,0,'aut_precio');

			// CONSULTAR SI TIENE AUTORIZACION POR AREA
			$sql="SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
			$aut_area = mysql_result($query,0,'aut_area');

			if ( ( $aut_precio>0 || $aut_area>0 ) && $autorizado=='false' ) {
				echo '<script>alert("La Orden de compra no esta autorizada!");</script>';
			  	exit;
			}
		}


		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$nombreReferencia.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$nombreReferencia.' esta cancelado");</script>'; return; }
		else if($pendientes_facturar <1){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$nombreReferencia.' no tiene unidades disponibles a facturar");</script>'.cargaArticulosFacturaCompraSave($id_factura,'',0,$link); return; }
		// else if($idProveedorDocAgregar <> $idProveedorFactura && $idProveedorFactura > 0){ echo '-'.$idProveedorFactura.'<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$nombreReferencia.' pertenece a un cliente diferente.");</script>'; }


		//CONSULTA SI LA ORDEN DE COMPRA YA HA SIDO AGREGADA
		$selectNoRepetido = "SELECT COUNT(id) AS cont FROM compras_facturas_inventario
							WHERE id_empresa='$id_empresa'
								AND consecutivo_referencia='$codDocAgregar'
								AND nombre_consecutivo_referencia='$nombreReferencia'
								AND id_factura_compra='$id_factura'
								AND id_bodega='$filtro_bodega'
								AND activo=1";
		$cont = mysql_result(mysql_query($selectNoRepetido,$link),0,'cont');
		if($cont > 0){
			echo'<script>
					document.getElementById("ordenCompra").blur();
        			alert("La orden de compra codigo '.$codDocAgregar.' ya fue agregada en la presente factura.");
        			document.getElementById("ordenCompra").value="";
        			setTimeout(function(){ document.getElementById("ordenCompra").focus(); },80);
        		</script>'.cargaArticulosFacturaCompraSave($id_factura,'',0,$link);
        	exit;
		}

		if ($opcCargar=='ordenCompra') {
			$sql   = "SELECT COUNT(id) AS cont FROM compras_ordenes_inventario WHERE id_orden_compra=$idDocumentoAgregar AND saldo_cantidad>0";
			$query = mysql_query($sql,$link);
			$cont  = mysql_result($query,0,'cont');

    		if ($cont<1) {
    			echo'<script>
	    				document.getElementById("ordenCompra").blur();
	        			alert("El documento numero '.$idOrdenCompra.' no tiene articulos con saldo disponible!");
	    				setTimeout(function(){ document.getElementById("ordenCompra").focus(); },100);
	        			document.getElementById("ordenCompra").value="";
	        		</script>';
    			return;
    		}
    	}

		//QUERY ESTADO DE LA ORDEN DE COMPRA
		$sql = "SELECT id as id_documento_referencia,id_proveedor, cod_proveedor, proveedor,estado,nit,observacion
				FROM $tablaCarga
				WHERE consecutivo='$codDocAgregar'
					AND  activo = 1
					AND id_sucursal= '$id_sucursal'
					AND id_bodega= '$filtro_bodega'
					AND id_empresa='$id_empresa'
				LIMIT 0,1";
		$query = mysql_query($sql,$link);

		$nit              = mysql_result($query,0,'nit');
		$idDocReferencia  = mysql_result($query,0,'id_documento_referencia');
		$oc_estado        = mysql_result($query,0,'estado');
		$oc_nit           = mysql_result($query,0,'nit');
		$oc_id_proveedor  = mysql_result($query,0,'id_proveedor');
		$oc_cod_proveedor = mysql_result($query,0,'cod_proveedor');
		$oc_proveedor     = mysql_result($query,0,'proveedor');
		$oc_observacion   = mysql_result($query,0,'observacion');

		//CAMPOS DE LA FACTURA DE COMPRA
		$sqlFactura     = "SELECT id_proveedor,COUNT(id) AS contF,id_bodega,estado,observacion FROM compras_facturas WHERE id='$id_factura' AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryFactura   = mysql_query($sqlFactura,$link);
		$f_id_proveedor = mysql_result($queryFactura,0,'id_proveedor');
		$f_id_bodega    = mysql_result($queryFactura,0,'id_bodega');
		$f_estado       = mysql_result($queryFactura,0,'estado');
		$f_observacion  = mysql_result($queryFactura,0,'observacion');

        if ($idDocReferencia > 0) {
	        // ORDEN DE COMPRA GENERADA
	        if($oc_estado==1 || $oc_estado==2 && $confirmar=='true'){
				if($queryFactura){
					if($f_estado == 1){												//IF SI LOS PORVEEDORES SON DIFERENTES
						echo'<script>
			        			alert("La factura Se encuetra cerrada");
			        			document.getElementById("ordenCompra").focus();
			        		</script>';
					}
					else if($f_id_proveedor <> $oc_id_proveedor && $f_id_proveedor!=0){												//IF SI LOS PORVEEDORES SON DIFERENTES
						echo'<script>
			        			alert("No se permite cargar ordenes de compra de diferentes proveedores");
			        			document.getElementById("ordenCompra").focus();
			        		</script>'.cargaArticulosFacturaCompraSave($id_factura,'',0,$link);
		        		return;
					}
					else if($f_id_bodega <> $filtro_bodega){												//IF SI LA UBICACION ES DIFERENTE
						echo'<script>
			        			alert("No se pueden cargar ordenes de compra de diferentes Bodegas");
			        			document.getElementById("ordenCompra").focus();
			        		</script>'.cargaArticulosFacturaCompraSave($id_factura,'',0,$link);
		        		return;
					}
					else{
						if($f_id_proveedor == 0 || $f_id_proveedor==''){
							$sqlUpdateProveedor   = "UPDATE compras_facturas
													SET id_proveedor='$oc_id_proveedor',
														observacion = IF(
															observacion<>'',
															CONCAT(observacion, ' ', '$referenciaInput ', '$codDocAgregar', ': ', '$oc_observacion'),
															CONCAT('$referenciaInput ', '$codDocAgregar', ': ', '$oc_observacion')
														)
													WHERE id='$id_factura' AND id_empresa='$id_empresa'";
							$queryUpdateProveedor = mysql_query($sqlUpdateProveedor,$link);
							if($queryUpdateProveedor){

								// CHECKBOX RETENCIONES ALMACENADAS
								$retencionesChecked = '';
						        $sqlRetenciones   = "SELECT TR.id_retencion AS id,
						                                    R.retencion AS retencion,
						                                    R.valor AS valor
						                            FROM terceros_retenciones AS TR, retenciones AS R
						                            WHERE TR.activo=1
														AND TR.id_proveedor = '$oc_id_proveedor'
														AND TR.id_empresa   = '$id_empresa'
														AND TR.id_retencion = R.id
														AND R.modulo = 'Compra'
						                            GROUP BY TR.id_retencion";
						        $queryRetenciones = mysql_query($sqlRetenciones,$link);
						        while ($rowRetenciones=mysql_fetch_array($queryRetenciones)){
						            $sqlInsertRetenciones   = "INSERT INTO compras_facturas_retenciones (id_factura_compra,id_retencion) VALUES ('$id_factura','".$rowRetenciones['id']."')";
						            $queryInsertRetenciones = mysql_query($sqlInsertRetenciones,$link);

						            $retencionesChecked .= 'if(document.getElementById("checkboxRetencionesFactura_'.$rowRetenciones['id'].'")){ document.getElementById("checkboxRetencionesFactura_'.$rowRetenciones['id'].'").checked=true; }';
						        }

								echo '<script>
										'.$retencionesChecked.'
					        			document.getElementById("ordenCompra").value="";

										id_proveedor_factura   = "'.$oc_id_proveedor.'";
										nitProveedorFactura    = "'.$oc_nit.'";
										codigoProveedorFactura = "'.$oc_cod_proveedor.'";
										nombreProveedorFactura = "'.$oc_proveedor.'";

					        			document.getElementById("nitProveedorFactura").value    = nitProveedorFactura;
						                document.getElementById("codProveedorFactura").value    = codigoProveedorFactura;
						                document.getElementById("nombreProveedorFactura").value = nombreProveedorFactura;
					        		</script>';
							}
							else{
								echo'<script>
					        			alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");
					        			document.getElementById("ordenCompra").focus();
					        		</script>';
		        				exit;
							}
						}
						else{
							$sqlUpdateProveedor   = "UPDATE compras_facturas
													SET observacion = IF(
															observacion<>'',
															CONCAT(observacion, ' ', '$referenciaInput ', '$codDocAgregar', ': ', '$oc_observacion'),
															CONCAT('$referenciaInput ', '$codDocAgregar', ': ', '$oc_observacion')
														)
													WHERE id='$id_factura' AND id_empresa='$id_empresa'";
							$queryUpdateProveedor = mysql_query($sqlUpdateProveedor,$link);
						}

						consultaInventarioOrdenesCompra($idDocReferencia,$codDocAgregar,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$link,$tablaCarga,$idTablaCarga,$tablaCargaInventario,$nombreReferencia);
						echo cargaArticulosFacturaCompraSave($id_factura,'',0,$link);
						return;
					}
				}
				//SI NO SE REALIZO LA CONSULTA
				else{
					echo'<script>
		        			alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");
		        			document.getElementById("ordenCompra").focus();
		        		</script>';
		        		exit;
				}
	        }
	        else if($oc_estado==2){
	        	echo '<script>
	        			if(confirm("El documento numero '.$codDocAgregar.' se ha relacionado con una factura de compra.\nDesea asignarla nuevamente?")){
	        				setTimeout(function(){ agregarOrdenCompraFactura("true"); },100);
	        			};
	        		</script>';
	        }
	        else if($oc_estado==3){
	        	echo '<script>
	        			document.getElementById("ordenCompra").value="";
	        			alert("El documento se ha cancelado");
	        			document.getElementById("ordenCompra").focus();
	        		</script>';
	        }

	        if($f_id_proveedor > 0 && ($oc_estado==2 || $oc_estado==3)){ echo cargaArticulosFacturaCompraSave($id_factura,'',0,$link); return; }
        }
        //ORDEN NO REGISTRADA A UN PROVEEDOR YA ESTABLECIDO
        else if($f_id_proveedor > 0){
        	echo'<script>
        			document.getElementById("ordenCompra").blur();
        			alert("Error, documento numero '.$codDocAgregar.' no registrada");
        			setTimeout(function(){ document.getElementById("ordenCompra").focus(); },80);
        		</script>';
        		echo cargaArticulosFacturaCompraSave($id_factura,'',0,$link);
        }
         //AGREGAR EN NUEVO ORDEN A UN PROVEEDOR YA ESTABLECIDO
        else{
        	echo'<script>
        			document.getElementById("ordenCompra").blur();
        			alert("Error, Documento numero '.$codDocAgregar.' no registrada");
        			setTimeout(function(){ document.getElementById("ordenCompra").focus(); },80);
        		</script>';
        }
	}

	function consultaInventarioOrdenesCompra($idDocReferencia,$consecutivoCarga,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$link,$tablaCarga,$idTablaCarga,$tablaCargaInventario,$nombreReferencia){
		$campoSelectItems = ($tablaCargaInventario=='compras_entrada_almacen_inventario')?",COI.check_opcion_contable" : '' ;

		$sql   = "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.saldo_cantidad AS cantidad,COI.costo_unitario,COI.tipo_descuento,COI.descuento,COI.id_centro_costos,
					COI.id_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida $campoSelectItems
                    FROM $tablaCargaInventario AS COI
                    	INNER JOIN  $tablaCarga AS CO ON ( COI.$idTablaCarga = CO.id )
                    WHERE CO.consecutivo='$consecutivoCarga'
                    	AND COI.$idTablaCarga = '$idDocReferencia'
                    	AND COI.activo = 1
                    	AND CO.id_sucursal='$id_sucursal'
                    	AND CO.id_bodega='$filtro_bodega'";
        $query = mysql_query($sql,$link);




        //RECORREMOS TODOS LOS ARTICULOS DEL DOCUMENTO A CARGAR Y GENERAMOS UNA CADENA PARA DESPUES HACER EL INSERT
        $cadenaInsert = '';
        while ($rowArt=mysql_fetch_array($query)) {
            $cadenaInsert .="('$id_factura',
            					'$id_empresa',
            					'$id_sucursal',
            					'$filtro_bodega',
            					'".$rowArt['id']."',
            					'$consecutivoCarga',
            					'$idDocReferencia',
            					'$nombreReferencia',
            					'".$rowArt['id_inventario']."',
            					'".$rowArt['cantidad']."',
            					'".$rowArt['costo_unitario']."',
            					'".$rowArt['tipo_descuento']."',
            					'".$rowArt['descuento']."',
            					'".$rowArt['id_centro_costos']."',
            					'".$rowArt['id_impuesto']."',
            					'".$rowArt['observaciones']."'
            					".(($tablaCargaInventario=='compras_entrada_almacen_inventario')? ",'".$rowArt['check_opcion_contable']."'" : '') ."
            					),";
        }

		$cadenaInsert      = substr($cadenaInsert,0,-1);
		$sqlInsertArticulo = "INSERT INTO compras_facturas_inventario (
								id_factura_compra,
								id_empresa,
								id_sucursal,
								id_bodega,
								id_tabla_referencia,
								consecutivo_referencia,
								id_consecutivo_referencia,
								nombre_consecutivo_referencia,
								id_inventario,
								cantidad,
								costo_unitario,
								tipo_descuento,
								descuento,
								id_centro_costos,
								id_impuesto,
								observaciones
								".(($tablaCargaInventario=='compras_entrada_almacen_inventario')? ',check_opcion_contable' : '') ."
								)
							VALUES $cadenaInsert";
        $queryInsertArticulo = mysql_query($sqlInsertArticulo,$link);

        //VERIFICAMOS SI SE EJECUTO EL QUERY CORRECTAMENTE
        if (!$queryInsertArticulo) {
            echo '<script>
                    document.getElementById("ordenCompra").blur();
                    alert("Error!\nSe produjo un error al cargar los articulos del documento\nIntentelo nuevamente\nSi el problema persiste comuniquese con el administrador del sistema");
                    setTimeout(function(){ document.getElementById("ordenCompra").focus();},100);
                </script>';

            exit;
        }
		else{

			$title = 'Eliminar items de la '.$nombreReferencia;
			// INCLUDE CARGA BODY GRILLA
    		// $divOrdenCompraCompra  ='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;" id="divOrdenCompraFactura_'.$idDocReferencia.'">'
		    // 						    .'<div class="contenedorInputOrdenCompraFactura">'
		    // 						        .'<input type="text" class="inputOrdenCompraFactura" value="'.$referenciaInput.' '.$consecutivoCarga.'" readonly/>'
		    // 						    .'</div>'
		    // 						    .'<div style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;" onclick="eliminaDocReferenciaFactura(\\\''.$idDocReferencia.'\\\',\\\''.$nombreReferencia.'\\\',\\\''.$id_factura.'\\\')">'
		    // 						        .'<img src="img/delete.png" style="margin: 1px 0 0 1px;"/>'
		    // 						    .'</div>'
		    // 						.'</div>';

		    $divOrdenCompraCompra .='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;height: 22px;" id="divDocReferenciaFactura_'.substr($nombreReferencia, 0, 1).'_'.$idDocReferencia.'">'
                            		    .'<div class="contenedorInputDocReferenciaFactura">'
                            		       .'<input type="text" class="inputDocReferenciaFactura" value="'.substr($nombreReferencia, 0, 1).' '.$consecutivoCarga.'" readonly style="border-bottom: 1px solid #d4d4d4;" />'
                            		    .'</div>'
                            		    .'<div title="'.$title.' # '.$consecutivoCarga.' en la presente factura" onclick="eliminaDocReferenciaFacturaCompra(\\\''.$idDocReferencia.'\\\',\\\''.$nombreReferencia.'\\\',\\\''.$id_factura.'\\\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">'
                            		        .'<div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btnFacturaCompra_'.substr($nombreReferencia, 0, 1).'_'.$idDocReferencia.'">'
                            		            .'<div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>'
                            		        .'</div>'
                            		    .'</div>'
                            		.'</div>';

			echo'<script>
					contenedorOredenesCompra = document.getElementById("contenedorOrdenCompraFactura").innerHTML;
					document.getElementById("contenedorOrdenCompraFactura").innerHTML =contenedorOredenesCompra+\''.$divOrdenCompraCompra.'\';
	    			document.getElementById("ordenCompra").value="";
	    		</script>';
		}
	}

	function eliminaDocReferenciaFactura($id_referencia,$docReferencia,$idFactura,$filtro_empresa,$id_sucursal,$filtro_bodega,$link){
		validaEstadoFactura($idFactura,$link);
		$sql   = "DELETE FROM compras_facturas_inventario WHERE id_factura_compra='$idFactura' AND id_consecutivo_referencia='$id_referencia' AND nombre_consecutivo_referencia='$docReferencia' AND id_empresa='$filtro_empresa' AND id_bodega='$filtro_bodega' AND id_sucursal='$id_sucursal'";
		$query = mysql_query($sql,$link);

		if($query){
			echo'<script>
					document.getElementById("contenedorOrdenCompraFactura").removeChild(document.getElementById("divDocReferenciaFactura_O_'.$id_referencia.'"));
				</script>'.cargaArticulosFacturaCompraSave($idFactura,'',0,$link);
		}
		else{ echo $sql.'<script>alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
	}

	//=============================== MODIFICAR FACTURA GENERADA ===================================//
	function modificarDocumentoGenerado($idDocumento,$id_empresa,$id_sucursal,$id_bodega,$link){
		// VALIDAR SI TIENE ACTIVOS REGISTRADOS PARA CANCELAR LA EDICION
		validaEstadoActivosFijos($idDocumento,$id_empresa,$link);

		//VALIDAR QUE NO TENGA NOTAS CONTABLES RELACIONADAS PARA EDITARLO
		validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$link);

		//DESCONTABILIZAMOS LA FACTURA DE COMPRA
		descontabilizarDocumento($idDocumento,$id_sucursal,$id_bodega,$link);

		//LLAMAMOS LA FUNCION PARA ACTUALIZAR LA CANTIDAD DE LOS ARTICULOS DEL INVENTARIO
		actualizarCantidadArticulos($idDocumento,'reversar ingreso',"Editar");
		// actualizarCantidadArticulos($idDocumento,$id_bodega,'eliminar',$link);
		// ACTUALIZAR LA CANTIDAD DE ACTIVOS FIJOS
		actualizaCantidadActivosFijos($idDocumento,$id_sucursal,'eliminar',$id_empresa,$link);

		//AGREGAR LOS EL CAMPO SALDO CANTIDAD DEL DOCUMENTO CARGADO
		actualizarSaldoCantidadDocumentoCargado($idDocumento,'agregar',$id_empresa,$link);

		// ACTUALIZAR EL ESTADO DEL DOCUMENTO ADJUNTADO
		cambiaEstadoDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$id_bodega,$link);

		//ACTUALIZAMOS LA FACTURA DE COMPRA A ESTADO 0 'SIN GUARDAR'
		$sql   = "UPDATE compras_facturas SET estado=0 WHERE id='$idDocumento' AND id_sucursal='$id_sucursal' AND id_bodega='$id_bodega' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query) {
			echo '<script>
					alert("Error!\nNo se modifico el documento para editarlo\nSi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			return;
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','FC','Factura de Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

		echo'<script>
			 	Ext.get("contenedor_facturacion_compras").load({
					url     : "facturacion/facturacion_compras.php",
					scripts : true,
					nocache : true,
					params  :
					{
						filtro_bodega     : "'.$id_bodega.'",
						id_factura_compra : "'.$idDocumento.'"
					}
				});

				Ext.getCmp("Btn_cancelar_FacturaCompra").disable();
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
	}

	//=============================== CANCELAR FACTURA DE COMPRA =======================================//
	function cancelarFacturaCompra($idFactura,$id_empresa,$id_sucursal,$id_bodega,$link){
		// VALIDAR SI TIENE ACTIVOS REGISTRADOS PARA CANCELAR LA EDICION
		validaEstadoActivosFijos($idFactura,$id_empresa,$link);

		//VALIDAR QUE NO TENGA NOTAS CONTABLES RELACIONADAS PARA EDITARLO
		validaDocumentoCruce($idFactura,$id_empresa,$id_sucursal,$link);

		//VERIFICAR SI LA FACTURA SE GENERO, SI HASI FUE, ENTONCES SE PROCEDE A DESCONTABILIZARLA Y SACAR LAS UNIDADES DEL INVENTARIO
		$sqlFactura   = "SELECT estado,consecutivo FROM compras_facturas WHERE id='$idFactura' AND activo=1 AND id_empresa='$id_empresa'";
		$queryFactura = mysql_query($sqlFactura,$link);
		$estado       = mysql_result($queryFactura,0,'estado');
		$consecutivo  = mysql_result($queryFactura,0,'consecutivo');

		$sqlUpdate = "UPDATE compras_facturas
						SET estado=3
						WHERE estado<>2 AND id='$idFactura'";

		//SI SE GENERO LA FACTURA, ENTONCES SE DEBE DESCONTABILIZAR Y DAR DE BAJA LOS ARTICULOS QUE INGRESARON CON ESA FACTURA.
		if ($estado=='1') {

			//DESCONTABILIZAMOS LA FACTURA DE COMPRA
			descontabilizarDocumento($idFactura,$id_sucursal,$id_bodega,$link);

			//LLAMAMOS LA FUNCION PARA ACTUALIZAR LA CANTIDAD DE LOS ARTICULOS DEL INVENTARIO
			actualizarCantidadArticulos($idFactura,'reversar ingreso',"Cancelar");

			// actualizarCantidadArticulos($idFactura,$id_bodega,'eliminar',$link);
			// ACTUALIZAR LA CANTIDAD DE ACTIVOS FIJOS
			actualizaCantidadActivosFijos($idFactura,$id_sucursal,'eliminar',$id_empresa,$link);

			//AGREGAR LOS EL CAMPO SALDO CANTIDAD DEL DOCUMENTO CARGADO
			actualizarSaldoCantidadDocumentoCargado($idFactura,'agregar',$id_empresa,$link);

			// ACTUALIZAR EL ESTADO DEL DOCUMENTO ADJUNTADO
			cambiaEstadoDocumentoCruce($idFactura,$id_empresa,$id_sucursal,$id_bodega,$link);

		}
		else if ($consecutivo=='0') {
			$sqlUpdate = "UPDATE compras_facturas
						SET activo=0
						WHERE estado<>2 AND id='$idFactura'";
		}
		else if ($estado=='3') {
			echo '<script>
					alert("Error!\nLa factura ya se encuentra cancelada!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				return;
		}


		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($queryUpdate){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idFactura,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','FC','Factura de Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					nuevaFacturaCompra();
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// Ext.get("contenedor_facturacion_compras").load({
					// 	url     : "facturacion/facturacion_compras_bloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_factura_compra : "'.$idFactura.'",
					// 		filtro_bodega     : document.getElementById("filtro_ubicacion_facturacion_compras").value
					// 	}
					// });
				</script>';
		}
		else{
			echo'<script>
					alert("No hay conexion con la base de datos,\nsi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>'; }
	}

	// ============================== ACTUALIZAR EL ESTADO DE LOS DOCUMENTOS CRUZADOS ==========================//
	function cambiaEstadoDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$id_bodega,$link){
		$sql  = "SELECT id,id_consecutivo_referencia,consecutivo_referencia,nombre_consecutivo_referencia
					FROM compras_facturas_inventario
					WHERE activo=1
						AND id_factura_compra=$idDocumento
					GROUP BY id_consecutivo_referencia,nombre_consecutivo_referencia";
		$query = mysql_query($sql,$link);

		$contFacturas=0;
		while ($row=mysql_fetch_array($query)) {
			$sqlfacturas   = "SELECT COUNT(CFI.id) AS cont,CFI.id_factura_compra,CF.estado
								FROM
									compras_facturas_inventario AS CFI,
									compras_facturas AS CF
								WHERE CFI.activo = 1
									AND CFI.id_consecutivo_referencia     = '$row[id_consecutivo_referencia]'
									AND CFI.consecutivo_referencia        = '$row[consecutivo_referencia]'
									AND CFI.nombre_consecutivo_referencia = '$row[nombre_consecutivo_referencia]'
									AND CF.id = CFI.id_factura_compra
									AND CF.id <> $idDocumento
									AND CF.estado = 1";

			$queryFacturas = mysql_query($sqlfacturas,$link);
			$contFacturas  = mysql_result($queryFacturas,0,'cont');

			if ($contFacturas==0 && $row['nombre_consecutivo_referencia']=='Orden de Compra') {
				$sqlFactura   = "UPDATE compras_ordenes SET estado=1
									WHERE activo=1
										AND id_empresa=$id_empresa
										AND id_sucursal=$id_sucursal
										AND id_bodega=$id_bodega
										AND id=$row[id_consecutivo_referencia]";
				$queryFactura = mysql_query($sqlFactura,$link);
			}
			else if ($contFacturas==0 && $row['nombre_consecutivo_referencia']=='Entrada de Almacen') {
				$sqlFactura   = "UPDATE compras_entrada_almacen SET estado=1
									WHERE activo=1
										AND id_empresa=$id_empresa
										AND id_sucursal=$id_sucursal
										AND id_bodega=$id_bodega
										AND id=$row[id_consecutivo_referencia]";
				$queryFactura = mysql_query($sqlFactura,$link);
			}

			$contFacturas==0;
		}
	}

	function validaDocumentoCruce($idDocumento,$id_empresa,$id_sucursal,$link){
		$sqlNota    = "SELECT consecutivo_documento, tipo_documento
						FROM asientos_colgaap
						WHERE activo=1
							AND id_documento_cruce = '$idDocumento'
							AND tipo_documento_cruce='FC'
							AND id_documento<>'$idDocumento'
							AND tipo_documento<>'FC'
							AND id_empresa = '$id_empresa'
							/*AND id_sucursal = '$id_sucursal'*/
						GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';
		while ($row=mysql_fetch_array($queryNota)) {
			$doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento'];
		}
		if ($doc_cruces != '') {
			echo '<script>
				  	   alert("Aviso!\nEsta Factura de compra tiene relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nCancele los documentos cruce para editar.");
				  	   if(document.getElementById("modal")){
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					   }
				  </script>';
			exit;
		}

	}

	//=============================== FUNCION PARA DESCONTABILIZAR LA FACTURA DE COMPRA ==============================//
	function descontabilizarDocumento($idDocumento,$id_sucursal,$idBodega,$link){
		//ELIMINA CONTABILIZACION COLGAAP
		$sqlColgaap   = "DELETE FROM asientos_colgaap  WHERE id_documento='$idDocumento' AND tipo_documento='FC' AND id_sucursal='$id_sucursal' ";
		$queryColgaap = mysql_query($sqlColgaap,$link);

		$sqlColgaapContabilidad   = "DELETE FROM contabilizacion_compra_venta WHERE id_documento='$idDocumento' AND tipo_documento='FC' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryColgaapContabilidad = mysql_query($sqlColgaapContabilidad,$link);

		//ELIMINA CONTABILIZACION NIIF
		$sqlNiif   = "DELETE FROM asientos_niif  WHERE id_documento='$idDocumento' AND tipo_documento='FC' AND id_sucursal='$id_sucursal' ";
		$queryNiif = mysql_query($sqlNiif,$link);

		$sqlNiifContabilidad   = "DELETE FROM contabilizacion_compra_venta_niif	 WHERE id_documento='$idDocumento' AND tipo_documento='FC' AND id_sucursal='$id_sucursal' AND id_bodega='$idBodega' AND id_empresa=".$_SESSION['EMPRESA'];
		$queryNiifContabilidad = mysql_query($sqlNiifContabilidad,$link);

		if (!$queryColgaap || !$queryColgaapContabilidad || !$queryNiif || !$queryNiifContabilidad) {
			echo '<script>alert("Error!\nNo se descontabilizo la factura de compra\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			exit;
		}

		$sqlAnticipo = "UPDATE comprobante_egreso_cuentas AS C, anticipos AS A
						SET C.saldo_pendiente=C.saldo_pendiente+A.valor
						WHERE C.id=A.id_cuenta_anticipo
							AND C.activo=1
							AND A.activo=1
							AND A.id_documento='$idDocumento'
							AND A.tipo_documento='FC'";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);
	}

	function restaurarFacturaCompra($idFactura,$id_sucursal,$id_empresa,$link){
		//SE RESTAURA EL DOCUMENTO PARA QUE SU ESTADO QUEDE CON CERO

		$sqlUpdate = "UPDATE compras_facturas
						SET estado='0'
						WHERE id='$idFactura'
							AND id_empresa='$id_empresa'
							AND id_sucursal='$id_sucursal'";
		$queryUpdate = mysql_query($sqlUpdate,$link);


		if($queryUpdate){

			$sqlConsulDoc="SELECT consecutivo FROM compras_facturas WHERE id='$idFactura' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";

			//VERIFICAR SI FUE GENERADO ANTES DE CANCELAR
			$queryConsulDoc = mysql_query($sqlConsulDoc,$link);
			$consecutivo    = mysql_result($queryConsulDoc,0,'consecutivo');

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idFactura,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','FC','Factura de Compra',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					Ext.get("contenedor_facturacion_compras").load({
						url     : "facturacion/facturacion_compras.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_compra : "'.$idFactura.'",
							filtro_bodega     : document.getElementById("filtro_ubicacion_facturacion_compras").value
						}
					});
					document.getElementById("titleDocuementoFacturaCompra").innerHTML = "Consecutivo<br>N. "+"'.$consecutivo.'";
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
		}
		else{
			echo'<script>
					alert("No hay conexion con la base de datos,\nsi el problema persiste comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>'; }
	}

	//============================== FUNCION PARA ACTUALIZAR EL USUARIO QUE RECIBE EN EL ALMACEN =======================//
	function guardarEmpleadoRecibeAlmacen($id,$idFactura,$id_empresa,$link){
		validaEstadoFactura($idFactura,$link);
		$sql   = "UPDATE compras_facturas SET id_usuario_recibe_en_almacen=$id WHERE id=$idFactura AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query) {
			echo'<script>
					alert("Error!\nNo se guardo el empleado que recibio!");
					document.getElementById("nombreEmpleadoRecibioAlmacen").value="";
				</script>';
		}
		else{
			echo'<script>
					document.getElementById("nombreEmpleadoRecibioAlmacen").value=document.getElementById("div_empleadoFactura_nombre_'.$id.'").innerHTML;
					Win_VentanaProveedor_Factura.close();
				</script>';
		}
	}

	function validateCcos($codigoCcos,$id_empresa,$link){
		$sqlCcos   = "SELECT COUNT(id) AS contCcos FROM centro_costos WHERE codigo LIKE '$codigoCcos%' AND id_empresa='$id_empresa' AND activo=1 GROUP BY activo";
		$queryCcos = mysql_query($sqlCcos,$link);
		echo $contCcos = mysql_result($queryCcos, 0, 'contCcos');
	}

	// function updateCentroCostos($contFila,$idDepartamento,$id_empresa,$link){
	// $sqlCentroCostos   = "SELECT id, codigo, nombre FROM entro_costos WHERE activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
	// $queryCentroCostos = mysql_query($sqlCentroCostos,$link);
	// $codigo = mysql_result($queryCentroCostos, 0, 'codigo');
	// $nombre = mysql_result($queryCentroCostos, 0, 'nombre');

	// 	while ($rowCentroCostos = mysql_fetch_array($queryCentroCostos)) {
	// 		$selectedCentroCostos = '';
	// 		if($rowCentroCostos['id'] == $idCentroCostos){ $selectedCentroCostos = 'selected'; }
	// 		$optCentroCostos .= '<option value="'.$rowCentroCostos['id'].'" '.$selectedCentroCostos.'>'.$rowCentroCostos['codigo'].' '.$rowCentroCostos['nombre'].'</option>';
	// 	}
	// 	echo'<select id="centro_costo_fc_'.$contFila.'" style="width:100%;">'.$optCentroCostos.'</select>';
	// }

	function abrirVentanaUpdateValores($idFactura,$subtotal,$iva,$total,$id_empresa,$link){
		validaEstadoFactura($idFactura,$link);

		//CONSULTAR SI LA FACTURA YA ESTA CON LOS TOTALES MANUALES
		$sql   = "SELECT contabilidad_manual,cuenta_pago,cuenta_pago_niif FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$idFactura";
		$query = mysql_query($sql,$link);

		$contabilidad_manual = mysql_result($query,0,'contabilidad_manual');
		$cuenta_pago         = mysql_result($query,0,'cuenta_pago');
		$cuenta_pago_niif    = mysql_result($query,0,'cuenta_pago_niif');

		$script = '';
		if ($contabilidad_manual=='true') {
			$sql="SELECT subtotal_manual,
						iva_manual,
						total_manual,
						id_centro_costos,
						codigo_centro_costos,
						nombre_centro_costos,
						id_cuenta_subtotal,
						cuenta_subtotal,
						descripcion_cuenta_subtotal,
						id_cuenta_niif_subtotal,
						cuenta_niif_subtotal,
						descripcion_niif_subtotal,
						id_cuenta_iva,
						cuenta_iva,
						descripcion_cuenta_iva,
						id_cuenta_niif_iva,
						cuenta_niif_iva,
						descripcion_niif_iva,
						id_cuenta_total,
						cuenta_total,
						descripcion_cuenta_total,
						id_cuenta_niif_total,
						cuenta_niif_total,
						descripcion_niif_total
				 FROM compras_facturas_contabilidad_manual WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$idFactura";
			$query=mysql_query($sql,$link);
			$subtotal_manual         = mysql_result($query,0,'subtotal_manual');
			$iva_manual              = mysql_result($query,0,'iva_manual');
			$total_manual            = mysql_result($query,0,'total_manual');
			$id_centro_costos        = mysql_result($query,0,'id_centro_costos');
			$codigo_centro_costos    = mysql_result($query,0,'codigo_centro_costos');
			$nombre_centro_costos    = mysql_result($query,0,'nombre_centro_costos');

			$id_cuenta_subtotal      = mysql_result($query,0,'id_cuenta_subtotal');
			$cuenta_subtotal         = mysql_result($query,0,'cuenta_subtotal');
			$id_cuenta_niif_subtotal = mysql_result($query,0,'id_cuenta_niif_subtotal');
			$cuenta_niif_subtotal    = mysql_result($query,0,'cuenta_niif_subtotal');
			$id_cuenta_iva           = mysql_result($query,0,'id_cuenta_iva');
			$cuenta_iva              = mysql_result($query,0,'cuenta_iva');
			$id_cuenta_niif_iva      = mysql_result($query,0,'id_cuenta_niif_iva');
			$cuenta_niif_iva         = mysql_result($query,0,'cuenta_niif_iva');
			$id_cuenta_total         = mysql_result($query,0,'id_cuenta_total');
			$cuenta_total            = mysql_result($query,0,'cuenta_total');
			$id_cuenta_niif_total    = mysql_result($query,0,'id_cuenta_niif_total');
			$cuenta_niif_total       = mysql_result($query,0,'cuenta_niif_total');

			$subtotal = $subtotal_manual;
			$iva      = $iva_manual;
			$total    = $total_manual;
			$script = 'Ext.getCmp("btn_cancelar_contabilidad_manual").show();
					   Ext.getCmp("btn_guardar_contabilidad_manual").setText("Actualizar");';
		}

		echo'<div style="margin:0 10px; overflow:visible;">
				<div style="width:100%; float:left; height:16px; overflow:hidden;" id="loadValidaUpdatefecha"></div>

				<div style="float:left;width:100%;background-color:#ECECEC;border:1px solid #d4d4d4;border-radius: 3;">

					<div style="width:100%;float:left;border-bottom:1px solid #d4d4d4;">
						<div style="width:80px; float:left; "  class="labelNombreArticulo"></div>
						<div style="width:120px; float:left;text-indent: 3;" class="labelNombreArticulo">Valor($)</div>
						<div style="width:164px; float:left;text-indent: 3;" class="labelNombreArticulo">Cuenta Colgaap</div>
						<div style="width:120px; float:left;border-right:0px;text-indent: 3;" class="labelNombreArticulo">Cuenta Niif</div>
					</div>

					<div style="width:100%;float:left;border-bottom:1px solid #d4d4d4;">
						<div style="width:80px;  float:left; text-indent: 3;font-weight: bold;white-space: nowrap;">Subtotal</div>
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;"><input type="text" id="subtotalEditado" style="border:0px;width:100%;height:22px;text-indent: 3;" onKeyup="validarCamposEditados(this)" value="'.$subtotal.'" /></div>
						<input type="hidden" id="idCuentaSubtotalEditado" value="'.$id_cuenta_subtotal.'">
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;background-color:#FFF;height:22px;text-indent: 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;" id="cuentaSubtotalEditado">
							'.$cuenta_subtotal.'
						</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/buscar20.png" title="Buscar Cuenta para el Subtotal" id="img_buscar_centro_costos" onclick="ventana_buscar_cuenta(\'colgaap\',\'idCuentaSubtotalEditado\',\'cuentaSubtotalEditado\')" style="width: 18px;height: 18px;">
						</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/refresh.png" title="Sincronizar Cuenta Niif" id="img_buscar_centro_costos" onclick="sincronizar_cuenta_niif(\'idCuentaSubtotalEditado\',\'idCuentaNiifSubtotalEditado\',\'cuentaNiifSubtotalEditado\')" style="width: 16px;height: 16px;margin: 2 0 0 2;">
						</div>
						<input type="hidden" id="idCuentaNiifSubtotalEditado" value="'.$id_cuenta_niif_subtotal.'">
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;background-color:#FFF;height:22px;text-indent: 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;" id="cuentaNiifSubtotalEditado">
							'.$cuenta_niif_subtotal.'
						</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/buscar20.png" title="Buscar Cuenta Niif para el Subtotal" id="img_buscar_centro_costos" onclick="ventana_buscar_cuenta(\'niif\',\'idCuentaNiifSubtotalEditado\',\'cuentaNiifSubtotalEditado\')" style="width: 18px;height: 18px;">
						</div>
					</div>

					<div style="width:100%;float:left;border-bottom:1px solid #d4d4d4;">
						<div style="width:80px;  float:left; text-indent: 3;font-weight: bold;white-space: nowrap;">Iva</div>
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;"><input type="text" id="ivaEditado" style="border:0px;width:100%;height:22px;text-indent: 3;" onKeyup="validarCamposEditados(this)" value="'.$iva.'" /></div>
						<input type="hidden" id="idCuentaIvaEditado" value="'.$id_cuenta_iva.'">
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;background-color:#FFF;height:22px;text-indent: 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;" id="cuentaIvaEditado">
							'.$cuenta_iva.'
						</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/buscar20.png" title="Buscar Cuenta para el Iva" id="img_buscar_centro_costos" onclick="ventana_buscar_cuenta(\'colgaap\',\'idCuentaIvaEditado\',\'cuentaIvaEditado\')" style="width: 18px;height: 18px;">
						</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/refresh.png" title="Sincronizar Cuenta Niif" id="img_buscar_centro_costos" onclick="sincronizar_cuenta_niif(\'idCuentaIvaEditado\',\'idCuentaNiifIvaEditado\',\'cuentaNiifIvaEditado\')" style="width: 16px;height: 16px;margin: 2 0 0 2;">
						</div>
						<input type="hidden" id="idCuentaNiifIvaEditado" value="'.$id_cuenta_niif_iva.'">
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;background-color:#FFF;height:22px;text-indent: 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;" id="cuentaNiifIvaEditado">
							'.$cuenta_niif_iva.'
						</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/buscar20.png" title="Buscar Cuenta Niif para el Iva" id="img_buscar_centro_costos" onclick="ventana_buscar_cuenta(\'niif\',\'idCuentaNiifIvaEditado\',\'cuentaNiifIvaEditado\')" style="width: 18px;height: 18px;">
						</div>
					</div>

					<div style="width:100%;float:left;">
						<div style="width:80px;  float:left; text-indent: 3;font-weight: bold;white-space: nowrap;">Total</div>
						<div style="width:120px; float:left; border-left:1px solid #d4d4d4;"><input type="text" id="totalEditado" style="border:0px;width:100%;height:22px;text-indent: 3;" onKeyup="validarCamposEditados(this)" value="'.$total.'" /></div>

						<div style="width:164px; float:left; border-left:1px solid #d4d4d4;background-color:#FFF;height:22px;text-indent: 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;" id="cuentaTotalEditado">
							'.$cuenta_pago.'
						</div>

						<div style="width:143px; float:left; border-left:1px solid #d4d4d4;background-color:#FFF;height:22px;text-indent: 3;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;" id="cuentaNiifTotalEditado">
							'.$cuenta_pago.'
						</div>
					</div>

				</div>

				<div style="float:left;width:100%;background-color:#ECECEC;border:1px solid #d4d4d4;border-radius: 3;margin-top:15px;">

					<div style="width:100%;float:left;border-bottom:1px solid #d4d4d4;">
						<div style="width:calc(100% - 23px); float:left; text-align:center;border:0px;"  class="labelNombreArticulo">Centro de Costos</div>
						<div style="background-color: #F3F3F3;width:21px;float: left;cursor: pointer;height: 22;border-left:1px solid #d4d4d4;" >
							<img src="img/buscar20.png" title="Buscar Centro de Costos" id="img_buscar_centro_costos" onclick="ventana_centros_costos_fc()" style="width: 18px;height: 18px;">
						</div>
					</div>
					<div style="width:100%;float:left;">
							<input type="hidden" id="id_ccos_fc" value="'.$id_centro_costos.'" >
							<div style="float:left;width:80px;text-indent:6px;font-weight: bold;white-space: nowrap;">Codigo</div>
							<div style="float:left;width:120px;border-left:1px solid #d4d4d4;">
								<input type="text" readonly id="codigo_ccos_fc" value="'.$codigo_centro_costos.'" style="width:100%;border:0px;height:22px;text-indent: 3;" />
							</div>
							<div style="float:left;width:80px;text-indent:6px;font-weight: bold;white-space: nowrap;border-left:1px solid #d4d4d4;height:22px;">Nombre</div>
							<div style="float:left;width:228px;border-left:1px solid #d4d4d4;">
								<input type="text" readonly id="nombre_ccos_fc" value="'.$nombre_centro_costos.'" style="width:100%;border:0px;height:22px;text-indent: 3;"  />
							</div>
					</div>
				<div>

			</div>
			<script>
				'.$script.'
				function validarCamposEditados(input){
					patron = /[^\d.]/g;
        			if(patron.test(input.value)){
						input.value = input.value.replace(patron,\'\');
						input.value = input.value;
        			}
				}
			</script>';
	}

	//CANCELAR LOS VALORES MANUALES DE LA FACTURA
	function cancelarValoresFacturaCompra($idFactura,$id_empresa,$link){
		validaEstadoFactura($idFactura,$link);

		$sql   = "UPDATE compras_facturas SET contabilidad_manual='false' WHERE activo=1 AND id_empresa=$id_empresa AND id=$idFactura";
		$query = mysql_query($sql,$link);
		if ($query) {

			$sql   = "DELETE FROM compras_facturas_contabilidad_manual WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$idFactura ";
			$query = mysql_query($sql,$link);
			if ($query) {
				echo '<script>
						Win_Ventana_update_valores_FacturaCompra.close();
						contabilidad_manual = "";
						subtotal_manual     = 0;
						iva_manual          = 0;
						total_manual        = 0;
						//recalculamos los valores de la factura
        				calcularValoresFactura(0,0,0,"","",0);
				 </script>';
			}else{
				echo '<script>alert("Erro!\nNo se eliminaron los valores manuales\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
			}

		}else{
			echo '<script>alert("Error!\nNo se actualizo el campo contabilidad manual\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	//GUARDAR LOS VALORES MANUELAES DE LA FACTURA
	function guardarValoresFacturaCompra($idFactura,$subtotal,$iva,$total,$id_centro_costos,$id_cuenta_subtotal,$id_cuenta_niif_subtotal,$id_cuenta_iva,$id_cuenta_niif_iva,$id_cuenta_total,$id_cuenta_niif_total,$id_empresa,$link){
		validaEstadoFactura($idFactura,$link);
		$iva = ($iva=='')? 0 : $iva ;

		$sql   = "UPDATE compras_facturas SET contabilidad_manual='true' WHERE activo=1 AND id_empresa=$id_empresa AND id=$idFactura";
		$query = mysql_query($sql,$link);
		if ($query) {

			$sql   = "SELECT COUNT(id) AS cont FROM compras_facturas_contabilidad_manual WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$idFactura";
			$query = mysql_query($sql,$link);
			$cont  = mysql_result($query,0,'cont');

			if ($cont>0) {
				$sql="UPDATE compras_facturas_contabilidad_manual SET
						subtotal_manual         = '$subtotal',
						iva_manual              = '$iva',
						total_manual            = '$total',
						id_centro_costos        = '$id_centro_costos',
						id_cuenta_subtotal      = '$id_cuenta_subtotal',
						id_cuenta_niif_subtotal = '$id_cuenta_niif_subtotal',
						id_cuenta_iva           = '$id_cuenta_iva',
						id_cuenta_niif_iva      = '$id_cuenta_niif_iva',
						id_cuenta_total         = '$id_cuenta_total',
						id_cuenta_niif_total    = '$id_cuenta_niif_total'
						WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$idFactura";
				$query=mysql_query($sql,$link);
				if ($query) {
					echo '<script>
							Win_Ventana_update_valores_FacturaCompra.close();
							contabilidad_manual = "true";
							subtotal_manual     = '.$subtotal.';
							iva_manual          = '.$iva.';
							total_manual        = '.$total.';
							//recalculamos los valores de la factura
        					calcularValoresFactura(0,0,0,"","",0);
					 	</script>';
				}
				else{
					echo '<script>alert("Error!\nNo se actualizaron los valores manuales\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
				}
			}
			else{

				$sql="INSERT INTO compras_facturas_contabilidad_manual
							(	id_factura_compra,
								subtotal_manual,
								iva_manual,
								total_manual,
								id_centro_costos,
								id_cuenta_subtotal,
								id_cuenta_niif_subtotal,
								id_cuenta_iva,
								id_cuenta_niif_iva,
								id_cuenta_total,
								id_cuenta_niif_total,
								id_empresa
							)
					VALUES (	'$idFactura',
								'$subtotal',
								'$iva',
								'$total',
								'$id_centro_costos',
								'$id_cuenta_subtotal',
								'$id_cuenta_niif_subtotal',
								'$id_cuenta_iva',
								'$id_cuenta_niif_iva',
								'$id_cuenta_total',
								'$id_cuenta_niif_total',
								'$id_empresa'
							)";

				$query=mysql_query($sql,$link);
				if ($query) {
					echo '<script>
							Win_Ventana_update_valores_FacturaCompra.close();
							contabilidad_manual = "true";
							subtotal_manual     = '.$subtotal.';
							iva_manual          = '.$iva.';
							total_manual        = '.$total.';
							//recalculamos los valores de la factura
        					calcularValoresFactura(0,0,0,"","",0);
					 	</script>';
				}else{
					echo '<script>alert("Error!\nNo se inertaron los valores manuales\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
				}
			}
		}
		else{
			echo '<script>alert("Error!\nNo se actualizo el campo contabilidad manual\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	//BUSCAR LA CUENTA NIIF EQUIVALENTE A LA COLGAAP
	function sincronizarCuentaNiif($id_cuenta_colgaap,$campoIdNiif,$campoNiif,$id_empresa,$link){
		$sql="SELECT
					puc.id,
					puc.cuenta,
					puc_niif.id AS id_niif,
					puc_niif.cuenta AS cuenta_niif

				FROM
					puc,
					puc_niif
				WHERE
					puc.activo = 1
				AND puc.id_empresa = $id_empresa
				AND puc_niif.cuenta = puc.cuenta_niif
				AND puc.id = $id_cuenta_colgaap
				AND puc_niif.id_empresa = $id_empresa
				AND puc_niif.activo = 1
				LIMIT 0,1";
		$query       = mysql_query($sql,$link);
		$cuenta      = mysql_result($query,0,'cuenta');
		$id_niif     = mysql_result($query,0,'id_niif');
		$cuenta_niif = mysql_result($query,0,'cuenta_niif');

		if ($id_niif>0) {
			echo '<script>
					document.getElementById("'.$campoIdNiif.'").value='.$id_niif.';
					document.getElementById("'.$campoNiif.'").innerHTML="'.$cuenta_niif.'";
				</script>';
		}
		else{
			echo'<script>alert("Error!\nNo hay ninguna cuenta niif configurada para la cuenta colgaap \nPuede configurarla desde el panel de control");</script>';
		}
	}

	//FUNCION PARA VALIDAR EL ESTADO DE LA FACTURA CUANDO SE GUARDA,ACTUALIZA,ELIMINA UN ITEM
	function validaEstadoFactura($idFactura,$link){
		$sql   = "SELECT estado,id_bodega FROM compras_facturas WHERE id=$idFactura";
		$query = mysql_query($sql,$link);
		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');

		if ($estado==1) {
			echo '<script>

						alert("Error!\nEl documento ya ha sido generado\nNo se puede realizar mas acciones sobre el");

						if(document.getElementById("Win_Ventana_update_valores_FacturaCompra")){
							Win_Ventana_update_valores_FacturaCompra.close();
						}
						if(document.getElementById("Win_Ventana_descripcion_Articulo_factura")){
							Win_Ventana_descripcion_Articulo_factura.close();
						}

						Ext.get("contenedor_facturacion_compras").load({
							url     : "facturacion/facturacion_compras_bloqueada.php",
							scripts : true,
							nocache : true,
							params  :
							{
								id_factura_compra : "'.$idFactura.'",
								filtro_bodega     : "'.$id_bodega.'"
							}
						});

						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		else if ($estado==3) {
			echo'<script>

					alert("Error!\nEl documento a sido cancelado\nNo se puede realizar mas acciones sobre el");

					if(document.getElementById("Win_Ventana_update_valores_FacturaCompra")){
						Win_Ventana_update_valores_FacturaCompra.close();
					}
					if(document.getElementById("Win_Ventana_descripcion_Articulo_factura")){
						Win_Ventana_descripcion_Articulo_factura.close();
					}

					Ext.get("contenedor_facturacion_compras").load({
						url     : "facturacion/facturacion_compras_bloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_factura_compra : "'.$idFactura.'",
							filtro_bodega     : "'.$id_bodega.'"
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
	}

	function validarNumeroFactura($prefijoFactura,$numeroFactura,$idFactura,$nitProveedor,$id_empresa,$link){
		$sqlFactura  = "SELECT prefijo_factura,numero_factura,fecha_generacion,hora_generacion,proveedor
									  FROM compras_facturas
										WHERE activo = 1
										AND id_empresa = '$id_empresa'
										AND id <> '$idFactura'
										AND (estado != 0 OR estado != 3)
										AND activo = 1
										AND prefijo_factura = '$prefijoFactura'
										AND numero_factura = '$numeroFactura'
										AND nit = '$nitProveedor'";
		$queryFactura = mysql_query($sqlFactura, $link);

		$facturasRepetidas = "";
		while ($row = mysql_fetch_assoc($queryFactura)){
			$numero_factura    = ($row['prefijo_factura'] != '')? $row['prefijo_factura'].'-'.$row['numero_factura'] : $row['numero_factura'];
			$facturasRepetidas = '\n\n* FACTURA: '.$numero_factura.' \nPROVEEDOR: '.$row['proveedor'].' \nFECHA Y HORA: '.fecha_larga($row['fecha_generacion']).' '.$row['hora_generacion'];
		}
		if($facturasRepetidas != ""){
			echo '<script>
							alert("Aviso,\nSe encontraron las siguientes facturas con una numeracion igual a la ingresada: '.$facturasRepetidas.'");
						</script>';
			return true;
		}
		else{
			return false;
		}
	}

	function ventanaValorAnticipo($idFactura,$valor_Factura,$id_cuenta_anticipo,$id_anticipo,$opcGrilla,$id_empresa,$link){
		//CUENTA DE ANTICIPO EN EL COMPROBANTE DE EGRESO
		$sqlAnticipo = "SELECT C.saldo_pendiente AS saldo_cuenta
					FROM comprobante_egreso AS E,
						comprobante_egreso_cuentas AS C
					WHERE E.id_empresa='$id_empresa'
						AND E.activo=1
						AND E.id=C.id_comprobante_egreso
						AND (E.estado=1 || E.estado=2)
						AND C.activo=1
						AND C.id='$id_cuenta_anticipo'";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);
		$saldo_cuenta = mysql_result($queryAnticipo, 0, 'saldo_cuenta');
		if($saldo_cuenta == 0 OR $saldo_cuenta==''){
			echo'<script>
					Win_Ventana_valor_anticipo.close(id);
					alert("Aviso,\nEl Anticipo ya no existe dentro del sistema!")
				</script>';
			exit;
		}
		$saldo_cuenta *= 1;

		//ANTICIPOS EN LA FACTURA DE COMPRA
		$sqlSaldoAnticipo = "SELECT id,valor
							FROM anticipos
							WHERE id_empresa='$id_empresa'
								AND id_documento='$idFactura'
								AND tipo_documento='FC'
								AND activo=1";
		$querySaldoAnticipo = mysql_query($sqlSaldoAnticipo,$link);

		$valorAnticipo  = 0;
		$saldoAnticipos = 0;
		while ($rowSaldoAnticipo = mysql_fetch_assoc($querySaldoAnticipo)) {
			if($rowSaldoAnticipo['id'] == $id_anticipo){ $valorAnticipo = $rowSaldoAnticipo['valor']; continue; }
			$saldoAnticipos += $rowSaldoAnticipo['valor'];
		}
		$valorAnticipo  *= 1;
		$saldoAnticipos *= 1;

		?>
			<div id="load_save_<?php echo $opcGrilla; ?>" style="overflow:hidden; position:fixed; width:18px; height:18px;"></div>
			<div style="margin:2%; overflow:hidden; width:96%;" class="contenedor_ventana_<?php echo $opcGrilla; ?>">
				<div style="overflow:hidden; width:100%; margin-top:15px;">
					<div style="float:left; width:50%; margin-right:1%;">Saldo Actual de factura</div>
					<div style="float:left; width:49%;"><input type="text" class="myfield" style="width:100%;" value="<?php echo $valor_Factura; ?>" readonly/></div>
				</div>
				<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:50%; margin-right:1%;">Saldo Otros Anticipos</div>
					<div style="float:left; width:49%;"><input type="text" class="myfield" style="width:100%;" value="<?php echo $saldoAnticipos; ?>" readonly/></div>
				</div>
				<div style="overflow:hidden; width:100%;" class="EmpSeparador">ANTICIPO HA REALIZAR</div>
				<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:50%; margin-right:1%;">Valor Maximo de Anticipo</div>
					<div style="float:left; width:49%;"><input id="saldo_anticipo" type="text" class="myfield" style="width:100%;" value="<?php echo $saldo_cuenta; ?>" readonly/></div>
				</div>
				<div style="overflow:hidden; width:100%;">
					<div style="float:left; width:50%; margin-right:1%;">valor Anticipo</div>
					<div style="float:left; width:49%;"><input id="valor_anticipo" type="text" class="myfield" style="width:100%;" value="<?php echo $valorAnticipo; ?>"/></div>
				</div>
			</div>
			<style>
				.contenedor_ventana_<?php echo $opcGrilla; ?>{ margin-top:10px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> > div{ margin-bottom:5px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> > div > div{ height:23px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> input{ text-align:right; padding-right:5px; }
				.contenedor_ventana_<?php echo $opcGrilla; ?> input[readonly]{ background-color:#ededed; }
			</style>
		<?php
	}

	function guardarAnticipo($contFila,$idFactura,$valor_anticipo,$id_cuenta_anticipo,$id_anticipo,$opcGrilla,$id_empresa,$link){
		//VALIDA SI EL ANTICIPO EXISTE
		$sqlAnticipo = "SELECT
							E.id AS id_documento_anticipo,
							E.consecutivo,
							if(C.id_tercero>0,C.id_tercero,E.id_tercero) AS id_tercero,
							if(C.id_tercero>0,C.nit_tercero,E.nit_tercero) AS nit_tercero,
							if(C.id_tercero>0,C.tercero,E.tercero) AS tercero,
							C.saldo_pendiente AS saldo_cuenta,
							C.cuenta AS cuenta_colgaap,
							C.cuenta_niif
						FROM comprobante_egreso AS E,
							comprobante_egreso_cuentas AS C
						WHERE E.id_empresa='$id_empresa'
							AND E.activo=1
							AND E.id=C.id_comprobante_egreso
							AND (E.estado=1 || E.estado=2)
							AND C.activo=1
							AND C.id='$id_cuenta_anticipo'";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);

		$saldo_cuenta   = mysql_result($queryAnticipo, 0, 'saldo_cuenta');
		$cuenta_niif    = mysql_result($queryAnticipo, 0, 'cuenta_niif');
		$cuenta_colgaap = mysql_result($queryAnticipo, 0, 'cuenta_colgaap');

		$consecutivoAnticipo = mysql_result($queryAnticipo, 0, 'consecutivo');
		$idDocumentoAnticipo = mysql_result($queryAnticipo, 0, 'id_documento_anticipo');
		$idTercero           = mysql_result($queryAnticipo, 0, 'id_tercero');
		$nitTercero          = mysql_result($queryAnticipo, 0, 'nit_tercero');
		$tercero             = mysql_result($queryAnticipo, 0, 'tercero');

		if($saldo_cuenta == 0 OR $saldo_cuenta==''){
			echo'<script>
					Win_Ventana_valor_anticipo.close(id);
					alert("Aviso,\nEl Anticipo no existe dentro del sistema!")
				</script>';
			exit;
		}
		else if($saldo_cuenta < $valor_anticipo){ echo '<script>alert("Aviso,\nEl valor del anticipo no puede exceder el saldo registrado!")</script>'; exit; }

		//ANTICIPOS DUPLICADOS Y SALDO DE ANTICIPO
		$sqlAnticipo = "SELECT id,id_cuenta_anticipo,valor
							FROM anticipos
							WHERE id_empresa='$id_empresa'
								AND id_documento='$idFactura'
								AND tipo_documento='FC'
								AND activo=1";
		$queryAnticipo = mysql_query($sqlAnticipo,$link);

		$saldoAnticipos     = 0;
		$contCuentaAnticipo = 0;
		while ($rowAnticipo = mysql_fetch_assoc($queryAnticipo)) {
			if($rowAnticipo['id_cuenta_anticipo'] == $id_cuenta_anticipo){
				$contCuentaAnticipo++;
				$id_Anticipo_BD = $rowAnticipo['id'];
			}
			if($rowAnticipo['id'] == $id_anticipo){ continue; }

			$saldoAnticipos += $rowAnticipo['valor'];
		}
		if($contCuentaAnticipo > 2){ echo '<script>alert("Aviso,\nExisten mas de un anticipo registrado con la misma cuenta en el comprobante de egreso!")</script>'; exit; }
		else if($id_anticipo == '' && $id_Anticipo_BD > 0){ $id_anticipo = $id_Anticipo_BD; }

		$totalAnticipo = $saldoAnticipos + $valor_anticipo;

		//DATOS DE FACTURA
		$sqlfactura   = "SELECT id_proveedor,proveedor,nit FROM compras_facturas WHERE id='$idFactura' LIMIT 0,1";
		$queryFactura = mysql_query($sqlfactura,$link);

		if(!$queryFactura){ echo '<script>alert("Aviso,\nNo se encontro la factura de compra!")</script>'; exit; }

		//INSERT OR UPDATE
		if($id_anticipo == ''){
			$sqlAnticipo = "INSERT INTO anticipos (id_cuenta_anticipo,
													id_empresa,
													id_documento,
													tipo_documento,
													valor,
													cuenta_niif,
													cuenta_colgaap,
													id_tercero,
													nit_tercero,
													tercero,
													id_documento_anticipo,
													tipo_documento_anticipo,
													consecutivo_documento_anticipo)
							VALUES ('$id_cuenta_anticipo',
									'$id_empresa',
									'$idFactura',
									'FC',
									'$valor_anticipo',
									'$cuenta_niif',
									'$cuenta_colgaap',
									'$idTercero',
									'$nitTercero',
									'$tercero',
									'$idDocumentoAnticipo',
									'CE',
									'$consecutivoAnticipo')";
			$queryAnticipo = mysql_query($sqlAnticipo,$link);
			if(!$queryAnticipo){ echo '<script>alert("Aviso,\nNo se guardo el anticipo registrado!")</script>'; exit; }

			$sqlAnticipo = "SELECT LAST_INSERT_ID()";
			$id_anticipo = mysql_result(mysql_query($sqlAnticipo,$link),0,0);
		}
		else{
			$sqlAnticipo = "UPDATE anticipos
							SET valor='$valor_anticipo',
								cuenta_niif='$cuenta_niif',
								cuenta_colgaap='$cuenta_colgaap',
								id_tercero='$idTercero',
								nit_tercero='$nitTercero',
								tercero='$tercero',
								id_documento_anticipo='$idDocumentoAnticipo',
								tipo_documento_anticipo='CE'
							WHERE id='$id_anticipo'
								AND id_cuenta_anticipo='$id_cuenta_anticipo'
								AND id_empresa='$id_empresa'
								AND id_documento='$idFactura'
								AND tipo_documento='FC'";
			$queryAnticipo = mysql_query($sqlAnticipo,$link);
			if(!$queryAnticipo){ echo '<script>alert("Aviso,\nNo se guardo el anticipo registrado!")</script>'; exit; }
		}

		?>
			<script type="text/javascript">
				document.getElementById("fila_<?php echo $opcGrilla; ?>_<?php echo $contFila; ?>").setAttribute("ondblclick","ventanaValorAnticipo('<?php echo $contFila; ?>','<?php echo $id_anticipo; ?>','<?php echo $id_cuenta_anticipo; ?>')");
				document.getElementById("valor_<?php echo $opcGrilla; ?>_<?php echo $contFila; ?>").innerHTML='<?php echo ROUND($valor_anticipo,$_SESSION['DECIMALESMONEDA']); ?>';
				document.getElementById("total_<?php echo $opcGrilla; ?>").innerHTML='$ <?php echo ROUND($totalAnticipo,$_SESSION['DECIMALESMONEDA']); ?>';

				//CAMPO CABECERA FACTURA DE COMPRA
				document.getElementById("<?php echo $opcGrilla; ?>").value = '$ <?php echo ROUND($totalAnticipo,$_SESSION['DECIMALESMONEDA']); ?>';

				Win_Ventana_valor_anticipo.close();
			</script>

		<?php
	}

	function cancelarAnticipoFactura($idFactura,$id_empresa,$link){
		$sqlDelete   = "UPDATE anticipos SET activo=0 WHERE id_empresa='$id_empresa' AND id_documento='$idFactura' AND tipo_documento='FC'";
		$queryDelete = mysql_query($sqlDelete,$link);

		if(!$queryDelete){ echo '<script>alert("Aviso,\nNo se eliminaron los anticipos agregados!")</script>'; exit; }
		?>
			<script>
				document.getElementById("anticipo_FacturaCompra").value = "$ 0";
				Win_Ventana_cuenta_anticipo_FacturaCompra.close();
			</script>

		<?php
	}

	function filtro_anticipo($idFactura,$idProveedor,$opcGrilla,$id_empresa,$link){
		?>
			<div style="margin-top:5px;">
				<select onchange="load_anticipos_compras(this.value)" class="myfield" style="width:145px;">
					<option value="">Todos</option>
					<option value="tercero">Tercero Factura</option>
				<select>
			</div>
			<script type="text/javascript">

				load_anticipos_compras("");

				function load_anticipos_compras(opcAnticipo){

					var terceroAnticipo = (opcAnticipo == '')? '': '<?php echo $idProveedor ?>';

					Ext.get('contenedor_cuenta_<?php echo $opcGrilla; ?>').load({
						url     : 'facturacion/grilla_anticipo_factura.php',
						scripts : true,
						nocache : true,
						params  :
						{
							opcGrilla       : '<?php echo $opcGrilla ?>',
							idFactura       : '<?php echo $idFactura ?>',
							terceroAnticipo : terceroAnticipo,
						}
					});
				}

			</script>
		<?php
	}

	function filtro_tipo_doc($filtro_bodega,$id_empresa,$link){
		?>
			<div style="margin-top:5px;">
				<select onchange="load_facturas(this.value)" class="myfield" style="width:145px;">
					<option value="">Todos</option>
					<option value="FC">Facturas</option>
					<option value="DSE">Documentos soporte</option>

				<select>
			</div>
			<script type="text/javascript">

				function load_facturas(tipo_doc){

					Ext.get('ContenedorPrincipal_facturaCompra').load({
						url     : 'facturacion/grilla_buscar_factura_compra.php',
						scripts : true,
						nocache : true,
						params  :
						{	filtro_bodega : '<?php echo $filtro_bodega ?>', 
							tipo_doc : tipo_doc
						}
					});
				}

			</script>
		<?php
	}

	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT fecha_inicio,fecha_final FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		$fecha_inicio      = mysql_result($query,0,'fecha_inicio');
		$fecha_final = mysql_result($query,0,'fecha_final');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha_inicio)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha_inicio)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha_final)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha_final)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND'$fecha_documento' BETWEEN fecha_inicio AND fecha_final";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont FROM nota_cierre WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_inicio' BETWEEN fecha_inicio AND fecha_final ";
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

	function agregarDocumento($typeDoc,$codDocAgregar,$id_factura,$filtro_bodega,$id_sucursal,$id_empresa,$opcGrillaContable,$tablaInventario='compras_facturas_inventario',$idTablaPrincipal='id_factura_compra',$link){

		$campoSelect = "";
		switch($typeDoc){
			case 'compras_entrada_almacen':
				$campoCantidad          = "cantidad";
				$title                  = 'Eliminar los Articulos de la Entrada de Almacen';
				$referencia_input       = "E";
				$referencia_consecutivo = "Entrada de Almacen";
				$tablaCarga             = "compras_entrada_almacen";
				$idTablaCargar          = "id_entrada_almacen";
				$tablaCargaInventario   = "compras_entrada_almacen_inventario";
				$docReferencia          = 'E'; //ESTA VARIABLE LLEGA AL condicional de eliminaDocReferencia
				$tablaBuscar            = "compras_entrada_almacen";
				$campoSelectItems       = ",COI.check_opcion_contable";
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
				$campoSelectValidacion  = ",autorizado";
				$tablaBuscar            = "compras_ordenes";
				break;
		}

	 	$sqlFactura   = "SELECT id_proveedor, estado FROM compras_facturas WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$queryFactura = mysql_query($sqlFactura,$link);
		$idProveedor  = mysql_result($queryFactura,0,'id_proveedor');
		$estado       = mysql_result($queryFactura,0,'estado');

		if($estado == 1){
			echo '<script>alert("Error!,\nLa presente factura ha sido generada.");</script>';
			return;
		}
		if($estado == 3){
			echo '<script>alert("Error!,\nLa presente factura ha sido cancelada.");</script>';
			return;
		}
		else if($idProveedor == '' || $idProveedor == 0){
			cargarNewDocumento($codDocAgregar,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,'facturacion/', $opcGrillaContable, '', $idTablaPrincipal, $tablaInventario, '',$typeDoc);
			return;
		}
		// else if(($idProveedor == '' || $idProveedor == 0) /*&& $typeDoc == 'compras_entrada_almacen'*/){
		// 	echo '<script>

		//VALIDACION ESTADO DE LA FACTURA
		$sqlValidateDocumento   = "SELECT estado,id,observacion,id_proveedor$campoSelectValidacion FROM $tablaCarga WHERE consecutivo='$codDocAgregar' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' ";
		$queryValidateDocumento = mysql_query($sqlValidateDocumento,$link);
		$idProveedorDocAgregar  = mysql_result($queryValidateDocumento,0,'id_proveedor');
		$idDocumentoAgregar     = mysql_result($queryValidateDocumento,0,'id');
		$estadoDocAgregar       = mysql_result($queryValidateDocumento,0,'estado');
		$observacion            = mysql_result($queryValidateDocumento,0,'observacion');

		// VERIFICAR SI ES ORDEN DE COMPRA, QUE ESTE AUTORIZADO SI DA A LUGAR
		if ($typeDoc=='orden_compra') {
			$autorizado = mysql_result($queryValidateDocumento,0,'autorizado');

			// CONSULTAR SI TIENE AUTORIZACION POR PRECIO
			$sql="SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
			$aut_precio = mysql_result($query,0,'aut_precio');

			// CONSULTAR SI TIENE AUTORIZACION POR AREA
			$sql="SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
			$aut_area = mysql_result($query,0,'aut_area');

			if ( ( $aut_precio>0 || $aut_area>0 ) && $autorizado=='false' ) {
				echo '<script>alert("La Orden de compra no esta autorizada!");</script>';
			  	exit;
			}
		}

		if($estadoDocAgregar == ''){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' no esta registrado");</script>'; return; }
		else if($estadoDocAgregar == 3){ echo '<script>alert("Error!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' esta cancelado");</script>'; return; }
		// else if($idProveedorDocAgregar <> $idProveedor){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' pertenece a un proveedor diferente.");</script>'; return; }

		//VALIDACION QUE EL DOCUMENTO NO HAYA SIDO INGRESADO
		$sqlValidateRepetido = "SELECT COUNT(id) AS contDocRepetido
								FROM compras_facturas_inventario
								WHERE activo=1 AND id_consecutivo_referencia='$idDocumentoAgregar'
									AND nombre_consecutivo_referencia='$referencia_consecutivo'
									AND id_factura_compra='$id_factura'
								GROUP BY id_tabla_referencia LIMIT 0,1";
		$docRepetido = mysql_result(mysql_query($sqlValidateRepetido,$link),0,'contDocRepetido');
		if($docRepetido > 0){ echo '<script>alert("Aviso!,\nEl consecutivo '.$codDocAgregar.' de '.$referencia_consecutivo.' ya ha sido agregado en la presente entrada de almacen");</script>'; return; }

		if($observacion <> ''){
			$sqlObservacion = "UPDATE compras_facturas
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

			$sqlSelect = "SELECT observacion FROM compras_facturas WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
            $querySelect = mysql_query($sqlSelect,$link);

            $observacion = mysql_result($querySelect,0,'observacion');

            $arrayReplaceString = array("\n","\r","<br>");
        	$observacion        = str_replace($arrayReplaceString,"\\n",$observacion);

            echo '<script>document.getElementById("observacion'.$opcGrillaContable.'").value="'.$observacion.'";</script>';

		}

		//GENERA CICLO PARA INSERTAR ARTICULOS DEL DOCUMENTO REFERENCIA A TABLA INVENTARIOS FACTURAS
		$sqlConsultaInventario = "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.$campoCantidad AS cantidad,COI.costo_unitario,
                                        COI.tipo_descuento,COI.descuento,COI.id_centro_costos,
                                        COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                        CO.id AS id_documento,CO.consecutivo AS consecutivo_documento $campoSelectItems
                                	FROM $tablaCargaInventario AS COI
                                	INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                	WHERE CO.consecutivo     ='$codDocAgregar'
                                	    AND COI.activo       = 1
                                	    AND CO.id_sucursal   ='$id_sucursal'
                                	    AND CO.id_bodega     ='$filtro_bodega'
                                	    AND CO.id_empresa    ='$id_empresa'
                                    ";
    	$queryConsultaInventario = mysql_query($sqlConsultaInventario,$link);

	    $contInsert = 0;
	    while($row = mysql_fetch_array($queryConsultaInventario)){
	    	$contInsert++;
	    	$idDocCruce = $row['id_documento'];
	      $sqlInsertArticulos =  "INSERT INTO
																	compras_facturas_inventario(
																		id_factura_compra,
	                                  id_inventario,
	                                  cantidad,
	                                  saldo_cantidad,
	                                  costo_unitario,
	                                  tipo_descuento,
	                                  descuento,
	                                  id_centro_costos,
	                                  observaciones,
	                                  id_tabla_referencia,
	                                  id_consecutivo_referencia,
	                                  consecutivo_referencia,
	                                  nombre_consecutivo_referencia
	                                  " . ( ($typeDoc == 'compras_entrada_almacen')? ',check_opcion_contable' : '') . "
																	)
	                            		VALUES(
																		'$id_factura',
	                                  '".$row['id_inventario']."',
	                                  '".$row['cantidad']."',
	                                  '".$row['cantidad']."',
	                                  '".$row['costo_unitario']."',
	                                  '".$row['tipo_descuento']."',
	                                  '".$row['descuento']."',
	                                  '".$row['id_centro_costos']."',
	                                  '".$row['observaciones']."',
	                                  '".$row['id']."',
	                                  '".$row['id_documento']."',
	                                  '".$row['consecutivo_documento']."',
	                                  '$referencia_consecutivo'
	                                  ".(($typeDoc=='compras_entrada_almacen')? ",'".$row['check_opcion_contable']."'" : '') ."
																	)";
	      $queryInsertArticulos = mysql_query($sqlInsertArticulos,$link);
	    }

	    if($contInsert > 0){
	  		$newDocReferencia  ='<div style="width:136px; margin-left:5px; float:left; overflow:hidden;height: 22px;" id="divDocReferenciaFactura_'.$docReferencia.'_'.$idDocumentoAgregar.'">'
							       .'<div class="contenedorInputDocReferenciaFactura">'
							           .'<input type="text" class="inputDocReferenciaEntradaAlmacen" value="'.$referencia_input.' '.$codDocAgregar.'" readonly style="border-bottom: 1px solid #d4d4d4;"/>'
							       .'</div>'
							       .'<div title="'.$title.' # '.$codDocAgregar.' en la presente entrada de almacen" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\\\''.$idDocumentoAgregar.'\\\',\\\''.$referencia_consecutivo.'\\\',\\\''.$id_factura.'\\\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">'
							           .'<div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$docReferencia.'_'.$idDocCruce.'">'
	                                        .'<div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>'
	                                    .'</div>'
							       .'</div>'
							    .'</div>';

				echo '<script>
								divDocsReferenciaFactura = document.getElementById("contenedorOrdenCompraFactura").innerHTML;
								document.getElementById("contenedorOrdenCompraFactura").innerHTML =divDocsReferenciaFactura+\''.$newDocReferencia.'\';
			    			document.getElementById("ordenCompra").value="";

	    					Ext.get("renderizaNewArticuloFactura").load({
			            url     : "facturacion/bd/bd.php",
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
	    			document.getElementById("ordenCompra").blur();
	    			alert("Numero invalido!\nDocumento no terminado o ya asignado");
	    			setTimeout(function(){ document.getElementById("ordenCompra").focus();}, 100);
	    		</script>';
			}
	}

	// AGREGAR EL DOCUMENTO
	function cargarNewDocumento($id,$id_sucursal,$filtro_bodega,$id_empresa,$tablaBuscar,$link,$carpeta, $opcGrillaContable, $tablaPrincipal, $idTablaPrincipal, $tablaInventario, $tablaRetenciones,$opcCargar){
		$tablaBuscar = ($opcCargar == 'orden_compra')? 'compras_ordenes' : 'compras_entrada_almacen';
		$sql = "SELECT COUNT(id_proveedor) AS cont, id_proveedor, nit, proveedor
						FROM $tablaBuscar
						WHERE consecutivo='$id'
						AND activo = 1
						AND (estado = 1 OR estado=2)
						AND id_sucursal= '$id_sucursal'
						AND id_bodega= '$filtro_bodega'
						AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);
		$resu  = mysql_result($query,0,'cont');

		$mensaje = '<script>
			       			document.getElementById("ordenCompra").blur();
			       			alert("Aviso!\nEl documento ya expiro\nO no existe.");
			       			setTimeout(function(){ document.getElementById("ordenCompra").focus(); },80);
								</script>';


	    if($resu > 0){
	    	echo '<script>
	        			Ext.get("contenedorFacturaCompra").load({
									url     : "facturacion/facturacion_compras.php",
									text    : "Cargando Documento...",
									scripts : true,
									nocache : true,
									params  :
									{
										opcCargar         : "'.$opcCargar.'",
										opcGrillaContable : "'.$opcGrillaContable.'",
										filtro_bodega     : document.getElementById("filtro_ubicacion_facturacion_compras").value,
										consecutivoCarga  : '.$id.',
									}
								});

								if(document.getElementById("Win_Ventana_buscar_documento_cruceFacturaCompra")){
									Win_Ventana_buscar_documento_cruceFacturaCompra.close();
								}
	        		</script>';
	    }
	    else{
			echo $mensaje;
		}
	}

	function reloadBodyAgregarDocumento($opcGrillaContable,$id_documento,$id_sucursal,$id_empresa,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){
		echo cargaArticulosFacturaCompraSave($id_documento,'',0,$link);
	    echo '<script>
	                if (document.getElementById("Win_Ventana_buscar_documento_cruceFacturaCompra")) {
						Win_Ventana_buscar_documento_cruceFacturaCompra.close();
					}
        	  </script>';
	}

	function eliminaDocReferencia($opcGrillaContable,$id_factura,$id_sucursal,$filtro_bodega,$id_empresa,$id_doc_referencia,$docReferencia,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link){

		// $campoDocReferencia = '';

		// if($docReferencia=='E'){ $campoDocReferencia = 'Entrada de Almacen'; }
		// else if($docReferencia=='O'){ $campoDocReferencia = 'Orden de Compra'; }

		$sql   ="DELETE FROM $tablaInventario WHERE $idTablaPrincipal=$id_factura AND id_consecutivo_referencia=$id_doc_referencia  AND nombre_consecutivo_referencia='$docReferencia'";
		$query = mysql_query($sql,$link);

		echo cargaArticulosFacturaCompraSave($id_factura,'',0,$link);

		if($query){
			$reference = substr($docReferencia, 0,1);
			echo'<script>
					document.getElementById("divDocReferenciaFactura_'.$reference.'_'.$id_doc_referencia.'").parentNode.removeChild(document.getElementById("divDocReferenciaFactura_'.$reference.'_'.$id_doc_referencia.'"));
				</script>';
		}
		else{ echo'<script>alert("Error de conexion.\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }
	}

	function ventanaVerImagenDocumentoTerceros($nombreImage,$nombreDocumento,$type,$id_host){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas/'.$nombreImage)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($type != 'pdf' && $type != 'PDF'){
			echo '<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
    					<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
    						<a href="'.$url.'" download="'.$nombreDocumento.'">
    							<img src="'.$url.'" style="">
    						</a>
    					</div>
    				</div>';
		}
		else{
			echo '<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
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
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas/'.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas/'.$nombre;
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
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas/'.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/compras/facturas/'.$nombre;
		}
		else{
			$url = '';
		}

		if ( unlink($url) ) {
			$sql="DELETE FROM compras_facturas_archivos_adjuntos WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
						var element = document.getElementById("archivo_adjunto_'.$id.'");
						element.parentNode.removeChild(element);
						MyLoading2("off",{texto:"Registro Eliminado"});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error! No se elimino el registro en base de datos",duracion:2500});
						// alert("Error!\nSe elimino el archivo, pero no el registro en base de datos");
					</script>';
			}
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error! no se elimino el archivo adjunto",duracion:2500});
					// alert("Error!\nNo se Elimino el Archivo Adjunto");
				</script>';
		}
	}

	function mostrarAlmacenamiento(){

		if($_SERVER['SERVER_NAME'] != 'erp.plataforma.co'){
			$size       = getFolderSize($_SESSION['ID_HOST'],'../../../../');
			$porcentaje = $size*100/$_SESSION['ALMACENAMIENTO'];
			$proporcion = 400*$porcentaje/100;
		}
		else{ $proporcion = 0; }

		$title = "INFORMACION DE ALMACENAMIENTO";

		if ($size >= $_SESSION['ALMACENAMIENTO'] ) {
			$title = "NO HAY ESPACIO DE ALMACENAMIENTO";
		}

		echo '<div class="content-sin-espacio">
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

	/**
	 * rollback deshacer los cambios realizados en caso de error
	 * @param  Array $param Parametros necesarios para realizar el rollback
	 * @param  int $param[id_documento] Id del documento a realizar rollback
	 * @param  boolean $param[modal] Si se debe eliminar la ventana modal
	 * @param  String $param[message] Mensage de error a retornar
	 * @param  String $param[debug] Variable con mensaje a mostrar en consola para debug
	 * @param  Object $param[mysql] Variable de conexion
	 *
	 */
	function rollback($params){
		$sql   = "UPDATE compras_facturas SET estado=0 WHERE activo=1 AND id=$params[id_documento]";
		$query = mysql_query($sql,$params['mysql']);
		$params['debug'] = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $params['debug']);
		$params['debug'] = ($params['debug']<>'')? 'console.log("'.$params['debug'].'");': "" ;
		if ($params['message']<>''){
			if ($params['modal']) {
				echo'<script>
						try{
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							alert("'.$params['message'].'");
							'.$params['debug'].'

						}
						catch(error) {
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							alert("'.$params['message'].'");
							'.$params['debug'].'
						  	console.error(error);
						}
					</script>';
			}
			else{
				echo '<script>
						alert("'.$params['message'].'");
						"'.$params['debug'].'"
					</script>';
			}
		}

		exit;
	}

	function updateSupportDocumentResolution($idFactura,$resolution_id,$link){
		$sql   = "UPDATE compras_facturas SET id_resolucion='".$resolution_id."' WHERE activo=1 AND id=$idFactura";
		$query = mysql_query($sql,$link);

		$sqlFechaVencimiento = "SELECT fecha_final_resolucion FROM resolucion_documento_soporte WHERE id=$resolution_id AND activo = 1";
		$queryFechaVencimiento = mysql_query($sqlFechaVencimiento,$link);
		$fecha_vencimiento_res = mysql_result($queryFechaVencimiento,0, 'fecha_final_resolucion');

		$current_timestamp = time();
        $fecha_res_timestamp = strtotime($fecha_vencimiento_res);
        // Calculate the difference in seconds
        $seconds_difference = abs($current_timestamp - $fecha_res_timestamp);

        // Calculate the difference in days
        $days_difference = floor($seconds_difference / (60 * 60 * 24));
    
    	if($days_difference < 7){
    	echo "<script>if(document.querySelector('#titleResFC') !==null){ document.querySelector('#titleResFC').innerHTML='<b>Fecha de vencimeinto resolucion</b><br>$fecha_vencimiento_res';}</script>";
		}
	}
	function updateDocumentType($idFactura,$document_type,$link){
		$sql   = "UPDATE compras_facturas SET tipo_documento='".$document_type."' WHERE activo=1 AND id=$idFactura";
		$query = mysql_query($sql,$link);
	}

		//======================= ENVIAR DOCUMENTO SOPORTE =======================//
	function enviarFacturaDIAN($id_factura,$opcGrillaContable,$id_empresa,$mysql){

		require("ClassSupportDocument.php");
					
		$documentoJson = new ClassSupportDocument($id_factura,$mysql);
		$data = $documentoJson->sendInvoice();
		
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
		$sqlDocumentoSoporte = "UPDATE
									compras_facturas
								SET
									fecha_DS = '$fecha_actual',
									hora_DS = '$hora_actual',
									response_DS = '$response_DS',
									UUID = 'esta pendiente',
									cufe = 'esta pendiente',
									id_usuario_DS = '$_SESSION[IDUSUARIO]',
									nombre_usuario_DS = '$_SESSION[NOMBREFUNCIONARIO]',
									cedula_usuario_DS = '$_SESSION[CEDULAFUNCIONARIO]'
								WHERE
									id = $id_factura";

		$queryDocumentoSoporte = mysql_query($sqlDocumentoSoporte,$mysql->link);
	}

	function updateRows($row,$value,$id_factura,$id_empresa,$mysql,$link){
		$sql   =  "UPDATE compras_facturas SET $row = '$value' WHERE id = $id_factura";
		$query = mysql_query($sql,$link);
	}

?>
