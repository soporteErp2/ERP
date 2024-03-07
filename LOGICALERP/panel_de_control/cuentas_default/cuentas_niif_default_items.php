<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../configuracion/naturaleza_cuentas.php");

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
		$arraySaveCampos[$texto]['cuenta']      = $rowCuentasItems['cuenta'];
		$arraySaveCampos[$texto]['descripcion'] = " - ".$rowCuentasItems['detalle_cuenta'];
	}

	//================= COMPRA ================//
	$arrayCuentasCompras[]= array('texto' => 'items_compra_precio', 'label' => 'SubTotal');
	$arrayCuentasCompras[]= array('texto' => 'items_compra_impuesto', 'label' => 'Impuesto');
	$arrayCuentasCompras[]= array('texto' => 'items_compra_activo_fijo', 'label' => 'SubTotal (Opcional - Activo Fijo)');
	$arrayCuentasCompras[]= array('texto' => 'items_compra_gasto', 'label' => 'SubTotal (Opcional - Gasto)');
	$arrayCuentasCompras[]= array('texto' => 'items_compra_costo', 'label' => 'SubTotal (Opcional - Costo)');
	foreach ($arrayCuentasCompras as $key => $arrayResult) {
		if ($arraySaveCampos[$arrayResult['texto']]['descripcion']==' - ') {
			$arraySaveCampos[$arrayResult['texto']]['cuenta']      = '';
			$arraySaveCampos[$arrayResult['texto']]['descripcion'] = "<i>Sin cuenta asignada</i>";
		}
		$tableBodyCompras .="<div class='row' id='row_tercero_14'>
	                           	<div class='cell' data-col='1'></div>
            					<div class='cell' data-col='2'>$arrayResult[label]</div>
            					<div class='cell' data-col='3' id='' data-value='".$arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza']."'>".$arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza']."</div>
            					<div class='cell' data-col='4' id='cuenta_$arrayResult[texto]_niif' title='".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."'>".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."</div>
	                           	<div class='cell' data-col='1' data-icon='search' onclick='buscarPucNiif(\"$arrayResult[texto]\")' title='Buscar Cuenta'></div>
	                        </div>";
	}

	//================= VENTA ================//
	$arrayCuentasVentas[] = array('texto' => 'items_venta_precio', 'label' => 'SubTotal');
	$arrayCuentasVentas[] = array('texto' => 'items_venta_impuesto', 'label' => 'Impuesto');
	$arrayCuentasVentas[] = array('texto' => 'items_venta_costo', 'label' => 'inventario');
	$arrayCuentasVentas[] = array('texto' => 'items_venta_contraPartida_costo', 'label' => 'Costo');
	foreach ($arrayCuentasVentas as $key => $arrayResult) {
		if ($arraySaveCampos[$arrayResult['texto']]['descripcion']==' - ') {
			$arraySaveCampos[$arrayResult['texto']]['cuenta']      = '';
			$arraySaveCampos[$arrayResult['texto']]['descripcion'] = "<i>Sin cuenta asignada</i>";
		}
		$tableBodyVentas .="<div class='row' id='row_tercero_14'>
	                           	<div class='cell' data-col='1'></div>
            					<div class='cell' data-col='2'>$arrayResult[label]</div>
            					<div class='cell' data-col='3' id='' data-value='".$arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza']."' >".$arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza']."</div>
            					<div class='cell' data-col='4' id='cuenta_$arrayResult[texto]_niif' title='".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."'>".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."</div>
	                           	<div class='cell' data-col='1' data-icon='search' onclick='buscarPucNiif(\"$arrayResult[texto]\")' title='Buscar Cuenta'></div>
	                        </div>";
	}

?>

<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: 100%; height: 420px; }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: 165px; width: 98%; }
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 180px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 65px; text-transform: capitalize;}
    .content-grilla-filtro .cell[data-col="4"]{width: 211px;}
    .sub-content [data-width="input"]{width: 120px;}

</style>

<div class="main-content" style="height: 420px;overflow-y: auto;overflow-x: hidden;">
    <div class="sub-content" data-position="right">
        <div class="title">CUENTAS EN COMPRA</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Tipo</div>
                <div class="cell" data-col="3">Naturaleza</div>
                <div class="cell" data-col="4">Cuenta</div>
            </div>
            <div class="body" id="body_grilla_filtro">
            	<?php echo $tableBodyCompras; ?>
            </div>
        </div>

    	<div class="title">CUENTAS EN VENTA</div>
        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Tipo</div>
                <div class="cell" data-col="3">Naturaleza</div>
                <div class="cell" data-col="4">Cuenta</div>
            </div>
            <div class="body" id="body_grilla_filtro">
            	<?php echo $tableBodyVentas; ?>
            </div>
        </div>

    </div>

<div id="loadForm" style="display: none;"></div>
</div>

<script>

	function buscarPucNiif(inputCuenta){
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
                params  : { ejecutaFuncion : 'responseVentanaPucItemsNiif(id,"'+inputCuenta+'")', type_puc : 'niif' }
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
		// console.log('estado_'+texto);
		var estado = document.getElementById('estado_'+idInput).dataset.value
		,	puc    = document.getElementById('div_busquedaPucItems_cuenta_'+idPuc).innerHTML
		,	nombre = document.getElementById('div_busquedaPucItems_descripcion_'+idPuc).innerHTML;

		estado = (estado == 'D')? 'debito': 'credito';
		if(puc.length > 4){ Win_VentanaBuscarPucNiifItems.close(); }
		else{ alert("Aviso,\nSeleccione una subcuenta o una cuenta auxiliar."); return; }

		Ext.get('loadForm').load({
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
				texto        : idInput
		    }
		});
	}

</script>