<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../configuracion/naturaleza_cuentas.php");

	$id_pais        = $_SESSION['PAIS'];
	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	$divItemsCuenta = '';

	//VALIDACION CUENTA DEVOLUCION VENTA
	$sqlDevVenta   = "SELECT COUNT(id) AS contDevVenta FROM asientos_colgaap_default WHERE id_empresa='$id_empresa' AND descripcion='items_venta_devprecio' AND activo=1";
	$queryDevVenta = mysql_query($sqlDevVenta,$link);
	$contDevVenta  = mysql_result($queryDevVenta, 0, 'contDevVenta');

	if($contDevVenta == 0){
		$textIcon = $arrayNaturaleza[$id_pais]['items_venta_devprecio']['prefijo'];
		$estado   = $arrayNaturaleza[$id_pais]['items_venta_devprecio']['naturaleza'];

		$sqlInsert   = "INSERT INTO asientos_colgaap_default (descripcion,estado,cuenta,id_empresa) VALUES('items_venta_devprecio','$estado','41750101','$id_empresa')";
		$queryInsert = mysql_query($sqlInsert,$link);
	}

	$sqlCuentasItems = "SELECT id,descripcion,estado,cuenta,detalle_cuenta
						FROM asientos_colgaap_default
						WHERE id_empresa='$id_empresa'";
	$queryCuentasItems = mysql_query($sqlCuentasItems,$link);

	while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {
		$reload    = '';
		$texto     = $rowCuentasItems['descripcion'];
		$textIcon  = ($rowCuentasItems['estado'] != 'debito')? 'C': 'D';
		$titleIcon = ($rowCuentasItems['estado'] != 'debito')? 'Credito': 'Debito';
		$btnReload = $arrayNaturaleza[$id_pais][$texto]['btnSinc'];

		if($btnReload != 'no'){
			$reload = '<div id="reload_'.$texto.'" title="Homologar cuenta en niif" style="height:20px; width:20px; float:left; overflow:hidden; margin:0 10px 0 5px;">
							<div class="btnItemsCuentas" style="margin:0;" onclick="homologarCuentaEnNiif(\''.$texto.'\')"><img src="cuentas_default/img/refresh.png" /></div>
						</div>';
		}
		else{ $reload = '<div style="height:20px; width:20px; float:left; overflow:hidden; margin:0 10px 0 5px;"></div>'; }


		$arraySaveCampos[$texto]['cuenta']         = $rowCuentasItems['cuenta'];
		$arraySaveCampos[$texto]['detalle_cuenta'] = $rowCuentasItems['detalle_cuenta'];
		$arraySaveCampos[$texto]['btns']           = '<div id="contenedorBtns_'.$texto.'" class="contenedorBtns">
														<div id="estado_'.$texto.'" title="'.$titleIcon.'" class="btnItemsCuentasEstado">'.$textIcon.'</div>
														'.$reload.'
													</div>';
	}

	//================= COMPRA ================//
	$arrayCuentasCompras[1]= array('texto' => 'items_compra_precio', 'label' => 'SubTotal');
	$arrayCuentasCompras[2]= array('texto' => 'items_compra_impuesto', 'label' => 'Impuesto');
	$arrayCuentasCompras[3]= array('texto' => 'items_compra_activo_fijo', 'label' => 'SubTotal (Opcional - Activo Fijo)');
	$arrayCuentasCompras[4]= array('texto' => 'items_compra_gasto', 'label' => 'SubTotal (Opcional - Gasto)');
	$arrayCuentasCompras[5]= array('texto' => 'items_compra_costo', 'label' => 'SubTotal (Opcional - costo)');

	$divItemsCuenta .= '<div class="item_cuenta_left">
							<div class="titleItemsCuenta"><b>COMPRA</b></div>';

	for ($i=1; $i <= 5 ; $i++) {
		$label = $arrayCuentasCompras[$i]['label'];
		$texto = $arrayCuentasCompras[$i]['texto'];

		$divItemsCuenta .= 	'<div class="filaCuentasItems">
								<div style="float:left; width:180px;">'.$label.'</div>
								<div style="float:left; width:90px; margin-left:5px;">
									<input type="text" class="myfield" id="input_'.$texto.'" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$texto]['cuenta'].'"/>
								</div>
								<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$texto.'"></div>
								'.$arraySaveCampos[$texto]['btns'].'
								<div class="cuentaPuc" id="detalle_'.$texto.'">'.$arraySaveCampos[$texto]['detalle_cuenta'].'</div>
							</div>';
	}
	$divItemsCuenta .=	'</div>';

	//================= VENTA ================//
	$arrayCuentasVentas[1]= array('texto' => 'items_venta_precio', 'label' => 'SubTotal');
	$arrayCuentasVentas[2]= array('texto' => 'items_venta_impuesto', 'label' => 'Impuesto');
	$arrayCuentasVentas[3]= array('texto' => 'items_venta_costo', 'label' => 'Costo');
	$arrayCuentasVentas[4]= array('texto' => 'items_venta_contraPartida_costo', 'label' => 'inventario');
	$arrayCuentasVentas[5]= array('texto' => 'items_venta_devprecio', 'label' => 'SubTotal en Devolucion');

	$divItemsCuenta .= '<div class="item_cuenta_right">
							<div class="titleItemsCuenta"><b>VENTA</b></div>';

	for ($i=1; $i <= 5 ; $i++) {
		$label = $arrayCuentasVentas[$i]['label'];
		$texto = $arrayCuentasVentas[$i]['texto'];

		$divItemsCuenta .= 	'<div class="filaCuentasItems">
								<div style="float:left; width:180px;">'.$label.'</div>
								<div style="float:left; width:90px; margin-left:5px;">
									<input type="text" class="myfield" id="input_'.$texto.'" onclick="buscarPuc(this)" readonly value="'.$arraySaveCampos[$texto]['cuenta'].'"/>
								</div>
								<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$texto.'"></div>
								'.$arraySaveCampos[$texto]['btns'].'
								<div class="cuentaPuc" id="detalle_'.$texto.'">'.$arraySaveCampos[$texto]['detalle_cuenta'].'</div>
							</div>';
	}
	$divItemsCuenta .=	'</div>';

?>

<div class="contenedor_items_cuentas"><?php echo $divItemsCuenta; ?></div>

<script>

	function buscarPuc(inputCuenta){
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
                params  :
                {
					type_puc       : 'puc',
					ejecutaFuncion : 'responseVentanaPucItems(id,"'+idInput+'")'
				}
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

	function responseVentanaPucItems(idPuc,idInput){
		var texto  = idInput.replace('input_','')
		,	estado = document.getElementById('estado_'+texto).innerHTML
		,	puc    = document.getElementById('div_busquedaPucItems_cuenta_'+idPuc).innerHTML
		,	nombre = document.getElementById('div_busquedaPucItems_descripcion_'+idPuc).innerHTML;

		estado = (estado == 'D')? 'debito': 'credito';
		if(puc.length > 4){ Win_VentanaBuscarPucItems.close(); }
		else{ alert("Aviso,\nSeleccione una subcuenta o una cuenta auxiliar."); return; }

		Ext.get('render_'+texto).load({
		    url		: 'cuentas_default/bd/bd.php',
		    timeout : 180000,
		    scripts	: true,
		    nocache	: true,
		    params	:
		    {
				op           : 'saveCuenta',
				typeResponse : 'colgaap',
				puc          : puc,
				nombre       : nombre,
				estado       : estado,
				texto        : texto,
		    }
		});
	}

	function homologarCuentaEnNiif(texto){
		var cuenta = document.getElementById('input_'+texto).value;

		Ext.get('reload_'+texto).load({
			url     : 'cuentas_default/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op     : 'updateCuentaNiif',
				cuenta : cuenta,
				texto  : texto
			}
		});
	}

</script>