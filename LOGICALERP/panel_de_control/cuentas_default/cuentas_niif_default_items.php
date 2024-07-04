<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$idEmpresa      = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];
	$divItemsCuenta = '';

	$sqlCuentasItems = "SELECT descripcion,estado,cuenta,detalle_cuenta
						FROM asientos_niif_default
						WHERE id_empresa='$idEmpresa'";
	$queryCuentasItems = mysql_query($sqlCuentasItems,$link);

	while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {

		$texto     = $rowCuentasItems['descripcion'];
		$textIcon  = ($rowCuentasItems['estado'] != 'debito')? 'C': 'D';
		$titleIcon = ($rowCuentasItems['estado'] != 'debito')? 'Credito': 'Debito';

		$arraySaveCampos[$texto]['cuenta']         = $rowCuentasItems['cuenta'];
		$arraySaveCampos[$texto]['detalle_cuenta'] = $rowCuentasItems['detalle_cuenta'];
		$arraySaveCampos[$texto]['btns']           = '<div id="contenedorBtns_'.$texto.'" class="contenedorBtns">
														<div id="estado_'.$texto.'_niif" title="'.$titleIcon.'" class="btnItemsCuentasEstado">'.$textIcon.'</div>
													</div>';
	}


	//================= COMPRA ================//
	$arrayCuentasCompras[1]= array('texto' => 'items_compra_precio', 'label' => 'SubTotal');
	$arrayCuentasCompras[2]= array('texto' => 'items_compra_impuesto', 'label' => 'Impuesto');
	$arrayCuentasCompras[3]= array('texto' => 'items_compra_activo_fijo', 'label' => 'SubTotal (Opcional - Activo Fijo)');
	$arrayCuentasCompras[4]= array('texto' => 'items_compra_gasto', 'label' => 'SubTotal (Opcional - Gasto)');
	$arrayCuentasCompras[5]= array('texto' => 'items_compra_costo', 'label' => 'SubTotal (Opcional - Costo)');

	$divItemsCuenta .= '<div class="item_cuenta_left">
							<div class="titleItemsCuenta"><b>COMPRA</b></div>';

	for ($i=1; $i <= 5 ; $i++) {
		$label = $arrayCuentasCompras[$i]['label'];
		$texto = $arrayCuentasCompras[$i]['texto'];

		$divItemsCuenta .= 		'<div class="filaCuentasItems">
									<div style="float:left; width:180px;">'.$label.'</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="input_'.$texto.'_niif" onclick="buscarPucNiif(this)" readonly value="'.$arraySaveCampos[$texto]['cuenta'].'"/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$texto.'_niif"></div>
									'.$arraySaveCampos[$texto]['btns'].'
									<div class="cuentaPuc" id="detalle_'.$texto.'_niif">'.$arraySaveCampos[$texto]['detalle_cuenta'].'</div>
								</div>';
	}

	$divItemsCuenta .=	'</div>';

	//================= VENTA ================//
	$arrayCuentasVentas[1]= array('texto' => 'items_venta_precio', 'label' => 'SubTotal');
	$arrayCuentasVentas[2]= array('texto' => 'items_venta_impuesto', 'label' => 'Impuesto');
	$arrayCuentasVentas[3]= array('texto' => 'items_venta_costo', 'label' => 'inventario');
	$arrayCuentasVentas[4]= array('texto' => 'items_venta_contraPartida_costo', 'label' => 'Costo');

	$divItemsCuenta .= '<div class="item_cuenta_right">
							<div class="titleItemsCuenta"><b>VENTA</b></div>';

	for ($i=1; $i <= 4 ; $i++) {
		$label = $arrayCuentasVentas[$i]['label'];
		$texto = $arrayCuentasVentas[$i]['texto'];

		$divItemsCuenta .= 		'<div class="filaCuentasItems">
									<div style="float:left; width:180px;">'.$label.'</div>
									<div style="float:left; width:90px; margin-left:5px;">
										<input type="text" class="myfield" id="input_'.$texto.'_niif" onclick="buscarPucNiif(this)" readonly value="'.$arraySaveCampos[$texto]['cuenta'].'"/>
									</div>
									<div style="float:left; width:18px; overflow:hidden; margin-left:-18px;" id="render_'.$texto.'_niif"></div>
									'.$arraySaveCampos[$texto]['btns'].'
									<div class="cuentaPuc" id="detalle_'.$texto.'_niif">'.$arraySaveCampos[$texto]['detalle_cuenta'].'</div>
								</div>';
	}

	$divItemsCuenta .=	'</div>';

?>

<div class="contenedor_items_cuentas"><?php echo $divItemsCuenta; ?></div>

<script>

	function buscarPucNiif(inputCuenta){
		var idInput = inputCuenta.id;

		Win_VentanaBuscarPucNiifItems = new Ext.Window({
            width       : 500,
            height      : 500,
            id          : 'Win_VentanaBuscarPucNiifItems',
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
                params  : { ejecutaFuncion : 'responseVentanaPucItemsNiif(id,"'+idInput+'")', type_puc : 'niif' }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_VentanaBuscarPucNiifItems.close(); }
                }
            ]
        }).show();

	}

	function responseVentanaPucItemsNiif(idPuc,idInput){
		var texto  = idInput.replace('input_','');
		var estado = document.getElementById('estado_'+texto).innerHTML
		,	puc    = document.getElementById('div_busquedaPucItems_cuenta_'+idPuc).innerHTML
		,	nombre = document.getElementById('div_busquedaPucItems_descripcion_'+idPuc).innerHTML;

		estado = (estado == 'D')? 'debito': 'credito';
		if(puc.length > 4){ Win_VentanaBuscarPucNiifItems.close(); }
		else{ alert("Aviso,\nSeleccione una subcuenta o una cuenta auxiliar."); return; }

		Ext.get('render_'+texto).load({
		    url		: 'cuentas_default/bd/bd.php',
		    timeout : 180000,
		    scripts	: true,
		    nocache	: true,
		    params	:
		    {
				op           : 'saveCuenta',
				typeResponse : 'niif',
				puc          : puc,
				nombre       : nombre,
				estado       : estado,
				texto        : texto
		    }
		});
	}

</script>