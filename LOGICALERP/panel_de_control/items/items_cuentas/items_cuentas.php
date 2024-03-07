<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../configuracion/naturaleza_cuentas.php");

	$id_pais        = $_SESSION['PAIS'];
	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	$divItemsCuenta      = '';
	$acumArrayJavaScript = '';

	$arraySaveCampos['compra_gasto']                = array();
	$arraySaveCampos['compra_precio']               = array();
	$arraySaveCampos['compra_impuesto']             = array();
	$arraySaveCampos['compra_activo_fijo']          = array();
	$arraySaveCampos['compra_contraPartida_precio'] = array();

	$arraySaveCampos['venta_costo']                = array();
	$arraySaveCampos['venta_precio']               = array();
	$arraySaveCampos['venta_impuesto']             = array();
	$arraySaveCampos['venta_contraPartida_costo']  = array();
	$arraySaveCampos['venta_contraPartida_precio'] = array();

	//ACTUALIZA SI YA CREARON LA CUENTA PUC
	$sqlCuentas   = "UPDATE items_cuentas SET cuenta='' WHERE activo=1 AND id_empresa='$id_empresa' AND id_puc='' AND puc>0 AND id_items='$idItems'";
	$queryCuentas = mysql_query($sqlCuentas,$link);

	$sqlCuentasItems = "SELECT descripcion,id_puc,puc,cuenta,estado,tipo,id
						FROM items_cuentas
						WHERE id_items='$idItems'
							AND activo=1
							AND id_empresa='$id_empresa'
							AND (descripcion<>'gasto' OR descripcion<>'contraPartida_gasto')";
	$queryCuentasItems = mysql_query($sqlCuentasItems,$link);

	while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {
		$texto    = $rowCuentasItems['estado'].'_'.$rowCuentasItems['descripcion'];
		$textIcon = ($rowCuentasItems['tipo'] != 'debito')? 'C': 'D';

		$arraySaveCampos[$texto]['cuenta'] = ($rowCuentasItems['cuenta']=='' && $rowCuentasItems['id_puc']=='')? '<span style="color:red;">NO EXISTE LA CUENTA PUC RELACIONADA</span>': $rowCuentasItems['cuenta'];
		$arraySaveCampos[$texto]['puc']    = $rowCuentasItems['puc'];
		$arraySaveCampos[$texto]['estado'] = '<div id="contenedorBtns_'.$texto.'" class="contenedorBtns" style=" width:35px !important;">
													<div title="'.$rowCuentasItems['tipo'].'" id="btnTipoAsiento_'.$texto.'" class="btnItemsCuentasEstado">
														'.$textIcon.'
													</div>
												</div>';

		$btnSinc = '<div id="reload_'.$texto.'" title="Homologar cuenta en niif" style="height:20px; width:20px; float:left; overflow:hidden; margin:0 10px 0 5px;">
						<div class="btnItemsCuentas" style="margin:0;" onclick="homologarCuentaEnNiif(\''.$texto.'\')"><img src="items/images/refresh.png" /></div>
					</div>';
		if($arrayNaturaleza[$id_pais]['items_'.$texto]['btnSinc'] != 'si'){
			$btnSinc = '<div style="height:20px; width:20px; float:left; overflow:hidden; margin:0 10px 0 5px;">&nbsp;</div>';
		}

		$arraySaveCampos[$texto]['btns']   = '<div id="contenedorBtns_'.$texto.'" class="contenedorBtns">
													<div title="'.$rowCuentasItems['tipo'].'" id="btnTipoAsiento_'.$texto.'" class="btnItemsCuentasEstado">
														'.$textIcon.'
													</div>
													'.$btnSinc.'
												</div>';

		$acumArrayJavaScript .= 'arrayIdItemsCuenta["'.$texto.'"]="'.$rowCuentasItems['id'].'";';
	}

	$sqlItems   = "SELECT inventariable,id_impuesto,opcion_gasto,opcion_activo_fijo,opcion_costo,estado_compra,estado_venta
					FROM items
					WHERE id='$idItems'
						AND activo=1
					LIMIT 0,1";
	$queryItems = mysql_query($sqlItems,$link);

	$inventariable      = mysql_result($queryItems,0,'inventariable');
	$id_impuesto        = mysql_result($queryItems,0,'id_impuesto');
	$opcion_gasto       = mysql_result($queryItems,0,'opcion_gasto');
	$opcion_costo       = mysql_result($queryItems,0,'opcion_costo');
	$opcion_activo_fijo = mysql_result($queryItems,0,'opcion_activo_fijo');
	$estadoCompra       = mysql_result($queryItems,0,'estado_compra');
	$estadoVenta        = mysql_result($queryItems,0,'estado_venta');

	if($estadoCompra != 'true' && $estadoVenta != 'true'){ echo 'EL PRESENTE ITEM NO TIENE CONFIGURACION EN COMPRA Y VENTA'; exit; }

	for($i=1; $i<=2; $i++){

		$estado = ($i==1)? 'compra': 'venta';
		if($estado == 'compra' && $estadoCompra != 'true' || $estado == 'venta' && $estadoVenta != 'true'){ continue; }

		$divItemsCuenta .= ($i==1)?
							'<div class="item_cuenta_left">
								<div class="titleItemsCuenta"><b>COMPRA</b></div>'
							:'<div class="item_cuenta_right">
								<div class="titleItemsCuenta"><b>VENTA</b></div>';

		$divItemsCuenta .= 		'<div class="filaCuentasItems" id="contenedor_'.$estado.'_precio">
									<div style="float:left; width:145px;">SubTotal</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="'.$estado.'_precio" onclick="buscarPuc(this)" value="'.$arraySaveCampos[$estado.'_precio']['puc'].'" readonly/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_precio"></div>
									'.$arraySaveCampos[$estado.'_precio']['btns'].'
									<div class="cuentaPuc" id="cuenta_'.$estado.'_precio">'.$arraySaveCampos[$estado.'_precio']['cuenta'].'</div>
								</div>';

		if ($id_impuesto > 0 && 1==2){
			$divItemsCuenta .= '<div class="filaCuentasItems" id="contenedor_'.$estado.'_impuesto">
									<div style="float:left; width:145px;">Impuesto</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="'.$estado.'_impuesto" value="'.$arraySaveCampos[$estado.'_impuesto']['puc'].'" style="cursor:default;" readonly/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_impuesto"></div>
									'.$arraySaveCampos[$estado.'_impuesto']['estado'].'
									<div class="cuentaPuc" id="cuenta_'.$estado.'_impuesto">'.$arraySaveCampos[$estado.'_impuesto']['cuenta'].'</div>
								</div>';
		}

		//CONTRA PARTIDA YA SE TRABAJA CON LA FORMA DE PAGO
		// $textContraPartida = ($id_impuesto > 0)? 'Total con Iva': 'Total sin Iva';

		// $divItemsCuenta .= 		'<div class="filaCuentasItems" id="contenedor_'.$estado.'_contraPartida_precio">
		// 							<div style="float:left; width:145px;">'.$textContraPartida.'</div>
		// 							<div style="float:left; width:90px; margin-left:5px;">
		// 								<input type="text" class="myfield" id="'.$estado.'_contraPartida_precio" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_contraPartida_precio']['puc'].'"/>
		// 							</div>
		// 							<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_contraPartida_precio"></div>
		// 							'.$arraySaveCampos[$estado.'_contraPartida_precio']['btns'].'
		// 							<div class="cuentaPuc" id="cuenta_'.$estado.'_contraPartida_precio">'.$arraySaveCampos[$estado.'_contraPartida_precio']['cuenta'].'</div>
		// 						</div>';

		//ACTIVO FIJO - GASTO
		if($estado == 'compra' ){
			if ($opcion_activo_fijo == 'true') {
				$texto ='SubTotal (Opcional - Activo Fijo)';
				$divItemsCuenta .= 	'<div class="filaCuentasItems" id="contenedor_'.$estado.'_activo_fijo">
										<div style="float:left; width:145px;">'.$texto.'</div>
										<div style="float:left; width:90px; margin-left:5px;">
											<input type="text" class="myfield" id="'.$estado.'_activo_fijo" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_activo_fijo']['puc'].'"/>
										</div>
										<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_activo_fijo"></div>
										'.$arraySaveCampos[$estado.'_activo_fijo']['btns'].'
										<div class="cuentaPuc" id="cuenta_'.$estado.'_activo_fijo">'.$arraySaveCampos[$estado.'_activo_fijo']['cuenta'].'</div>
									</div>';
			}
			if ($opcion_gasto == 'true') {
				$texto ='Subtotal (Opcional - Gasto Venta)';
				$divItemsCuenta .= 	'<div class="filaCuentasItems" id="contenedor_'.$estado.'_gasto">
										<div style="float:left; width:145px;">'.$texto.'</div>
										<div style="float:left; width:90px; margin-left:5px;">
											<input type="text" class="myfield" id="'.$estado.'_gasto" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_gasto']['puc'].'"/>
										</div>
										<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_gasto"></div>
										'.$arraySaveCampos[$estado.'_gasto']['btns'].'
										<div class="cuentaPuc" id="cuenta_'.$estado.'_gasto">'.$arraySaveCampos[$estado.'_gasto']['cuenta'].'</div>
									</div>';
			}
			if($opcion_costo=='true'){
				$texto = 'Subtotal (Opcional - Costo)';
				$divItemsCuenta .= 	'<div class="filaCuentasItems" id="contenedor_'.$estado.'_costo">
										<div style="float:left; width:145px;">'.$texto.'</div>
										<div style="float:left; width:90px; margin-left:5px;">
											<input type="text" class="myfield" id="'.$estado.'_costo" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_costo']['puc'].'"/>
										</div>
										<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_costo"></div>
										'.$arraySaveCampos[$estado.'_costo']['btns'].'
										<div class="cuentaPuc" id="cuenta_'.$estado.'_costo">'.$arraySaveCampos[$estado.'_costo']['cuenta'].'</div>
									</div>';
			}

			// $texto = ($opcion_activo_fijo == 'true')? 'SubTotal (Opcional - Activo Fijo)': 'Subtotal (Opcional - Gasto)';

		}

		//ENTRADA DE ALMACEN - PRESTAMO DE PRODUCTOS
		// $divItemsCuenta .= ($inventariable=='true' && $estado == 'compra')?
		// 						'<div class="filaCuentasItems" id="contenedor_'.$estado.'_prestamo">
		// 							<div style="float:left; width:145px;">Prestamo Inventario -entrada de almacen</div>
		// 							<div style="float:left; width:90px; margin-left:5px;">
		// 								<input type="text" class="myfield" id="'.$estado.'_prestamo" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_prestamo']['puc'].'"/>
		// 							</div>
		// 							<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_prestamo"></div>
		// 							'.$arraySaveCampos[$estado.'_prestamo']['btns'].'
		// 						</div>' : '';


		if($inventariable=='true' && $estado == 'venta'){
			$divItemsCuenta .=	'<div class="filaCuentasItems" id="contenedor_'.$estado.'_costo">
									<div style="float:left; width:145px;">Inventario</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="'.$estado.'_costo" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_costo']['puc'].'"/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_costo"></div>
									'.$arraySaveCampos[$estado.'_costo']['btns'].'
									<div class="cuentaPuc" id="cuenta_'.$estado.'_costo">'.$arraySaveCampos[$estado.'_costo']['cuenta'].'</div>
								</div>
								<div class="filaCuentasItems" id="contenedor_'.$estado.'_contraPartida_costo">
									<div style="float:left; width:145px;">Costo</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="'.$estado.'_contraPartida_costo" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$estado.'_contraPartida_costo']['puc'].'"/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_contraPartida_costo"></div>
									'.$arraySaveCampos[$estado.'_contraPartida_costo']['btns'].'
									<div class="cuentaPuc" id="cuenta_'.$estado.'_contraPartida_costo">'.$arraySaveCampos[$estado.'_contraPartida_costo']['cuenta'].'</div>
								</div>';
		}

		if($estado == 'venta'){
			$divItemsCuenta .= 	'<div class="filaCuentasItems" id="contenedor_'.$estado.'_devprecio">
									<div style="float:left; width:145px;">SubTotal en Devolucion</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="'.$estado.'_devprecio" onclick="buscarPuc(this)" value="'.$arraySaveCampos[$estado.'_devprecio']['puc'].'" readonly/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$estado.'_devprecio"></div>
									'.$arraySaveCampos[$estado.'_devprecio']['btns'].'
									<div class="cuentaPuc" id="cuenta_'.$estado.'_devprecio">'.$arraySaveCampos[$estado.'_devprecio']['cuenta'].'</div>
								</div>';
		}

		$divItemsCuenta .=	'</div>';
	}


?>

<div class="contenedor_items_cuentas"><?php echo $divItemsCuenta; ?></div>

<script>

	var arrayIdItemsCuenta = new Array();
	<?php echo $acumArrayJavaScript; ?>

		function buscarPuc(inputCuenta,idItemsCuenta){
		var idInput = inputCuenta.id;

		Win_VentanaBuscarPucItems = new Ext.Window({
            width       : 500,
            height      : 500,
            id          : 'Win_VentanaBuscarPucItems',
            title       : 'Cuentas PUC',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'items/items_cuentas/busqueda_puc_items.php',
                scripts : true,
                nocache : true,
                params  : { ejecutaFuncion : 'responseVentanaPucItems(id,"'+idInput+'","'+idItemsCuenta+'")' }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_VentanaBuscarPucItems.close(); }
                }
            ]
        }).show();
	}

	function responseVentanaPucItems(idPuc,idInput,idItemsCuenta){
		var estado      = idInput.split('_')[0]
		,	descripcion = idInput.replace(estado+'_','')
		,	puc         = document.getElementById('div_busquedaPucItems_cuenta_'+idPuc).innerHTML
		,	nombre      = document.getElementById('div_busquedaPucItems_descripcion_'+idPuc).innerHTML;

		if(puc.length > 4){ Win_VentanaBuscarPucItems.close(); }
		else{ alert("Aviso,\nSeleccione una subcuenta o una cuenta auxiliar."); return; }

		Ext.get('render_'+idInput).load({
		    url		: 'items/items_cuentas/bd/bd.php',
		    timeout : 180000,
		    scripts	: true,
		    nocache	: true,
		    params	:
		    {
				op           : 'saveCuenta',
				typeResponse : 'colgaap',
				// idItemCuenta : arrayIdItemsCuenta[idInput],
				idItemCuenta : idPuc,
				puc          : puc,
				nombre       : nombre,
				estado       : estado,
				descripcion  : descripcion,
				idItems      : '<?php echo $idItems; ?>'
		    }
		});
	}

	function homologarCuentaEnNiif(texto){
		var cuenta = document.getElementById(texto).value;

		Ext.get('reload_'+texto).load({
			url     : 'items/items_cuentas/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op      : 'updateCuentaNiif',
				cuenta  : cuenta,
				texto   : texto,
				idItems : '<?php echo $idItems; ?>'
			}
		});
	}

</script>