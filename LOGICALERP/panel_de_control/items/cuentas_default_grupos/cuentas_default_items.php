<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../configuracion/naturaleza_cuentas.php");

	$idEmpresa      = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];
	$divItemsCuenta = '';

	$sqlCuentasItems = "SELECT id,descripcion,estado,cuenta,detalle_cuenta
						FROM asientos_colgaap_default_grupos
						WHERE id_empresa='$idEmpresa' AND id_grupo=$id_grupo";
	$queryCuentasItems = mysql_query($sqlCuentasItems,$link);

	while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {

		$texto     = $rowCuentasItems['descripcion'];
		$textIcon  = ($rowCuentasItems['estado'] != 'debito')? 'C': 'D';
		$titleIcon = ($rowCuentasItems['estado'] != 'debito')? 'Credito': 'Debito';

		$arraySaveCampos[$texto]['cuenta']      = $rowCuentasItems['cuenta'];
		$arraySaveCampos[$texto]['descripcion'] = " - ".$rowCuentasItems['detalle_cuenta'];
	}

	//=================// COMPRA //=================//
	// $arrayCuentasCompras[]= array('texto' => 'items_compra_impuesto', 'label' => 'Impuesto');
	$arrayCuentasCompras[] = array('texto' => 'items_compra_precio', 		'label' => 'SubTotal',							'styleBtnSinc'=>'');
	$arrayCuentasCompras[] = array('texto' => 'items_compra_activo_fijo', 	'label' => 'SubTotal (Opcional - Activo Fijo)',	'styleBtnSinc'=>'');
	$arrayCuentasCompras[] = array('texto' => 'items_compra_gasto', 		'label' => 'SubTotal (Opcional - Gasto)',		'styleBtnSinc'=>'');
	$arrayCuentasCompras[] = array('texto' => 'items_compra_costo', 		'label' => 'SubTotal (Opcional - Costo)',		'styleBtnSinc'=>'');

	foreach ($arrayCuentasCompras as $key => $arrayResult) {
		$naturaleza = $arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza'];
		$tableBodyCompras .="<div class='row' id='row_tercero_14'>
	                           	<div class='cell' data-col='1'></div>
            					<div class='cell' data-col='2'>$arrayResult[label]</div>
            					<div class='cell' data-col='3' id='estado_$arrayResult[texto]'>".$arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza']."</div>
            					<div class='cell' data-col='4' id='$arrayResult[texto]' data-cuenta='".$arraySaveCampos[$arrayResult['texto']]['cuenta']."' title='".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."'>".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."</div>
	                           	<div class='cell' data-col='1' data-icon='search' onclick='buscarPuc(\"$arrayResult[texto]\")' title='Buscar Cuenta'></div>
	                           	<div class='cell' data-col='1' data-icon='sinc' onclick='homologarCuentaEnNiif(\"$arrayResult[texto]\",\"$naturaleza\")' title='Sincronizar Niif'></div>
	                        </div>";
	}

	//=================// VENTA //=================//
	// $arrayCuentasVentas[]= array('texto' => 'items_venta_impuesto', 'label' => 'Impuesto');
	$arrayCuentasVentas[]= array('texto' => 'items_venta_precio', 				'label' => 'SubTotal',				'styleBtnSinc'=>'');
	$arrayCuentasVentas[]= array('texto' => 'items_venta_costo', 				'label' => 'Inventario',			'styleBtnSinc'=>'');
	$arrayCuentasVentas[]= array('texto' => 'items_venta_contraPartida_costo', 	'label' => 'Costo',					'styleBtnSinc'=>'');
	$arrayCuentasVentas[]= array('texto' => 'items_venta_devprecio', 			'label' => 'SubTotal en Devolucion','styleBtnSinc'=>"style='display:none;'");

	foreach ($arrayCuentasVentas as $key => $arrayResult) {
		$naturaleza = $arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza'];
		$tableBodyVentas .="<div class='row' id='row_tercero_14'>
	                           	<div class='cell' data-col='1'></div>
            					<div class='cell' data-col='2'>$arrayResult[label]</div>
            					<div class='cell' data-col='3' id='estado_$arrayResult[texto]' >".$arrayNaturaleza[$_SESSION['PAIS']][$arrayResult['texto']]['naturaleza']."</div>
            					<div class='cell' data-col='4' id='$arrayResult[texto]' data-cuenta='".$arraySaveCampos[$arrayResult['texto']]['cuenta']."' title='".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."'>".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."</div>
	                           	<div class='cell' data-col='1' data-icon='search' onclick='buscarPuc(\"$arrayResult[texto]\")' title='Buscar Cuenta'></div>
	                           	<div class='cell' data-col='1' data-icon='sinc' onclick='homologarCuentaEnNiif(\"$arrayResult[texto]\",\"$naturaleza\")' title='Sincronizar Niif'  $arrayResult[styleBtnSinc]></div>
	                        </div>";
	}

	$arrayCuentasActivosFijos[] = array('texto' => 'items_activo_fijo_depreciacion_debito',  'label'=>'Cuenta debito (Gasto)', 'naturaleza'=>'debito');
	$arrayCuentasActivosFijos[] = array('texto' => 'items_activo_fijo_depreciacion_credito','label'=>'Cuenta Credito (Activo)', 'naturaleza'=>'credito');

	foreach ($arrayCuentasActivosFijos as $key => $arrayResult) {
		$tableBodyActivos .= "<div class='row' id='row_tercero_14'>
	                           	<div class='cell' data-col='1'></div>
            					<div class='cell' data-col='2'>$arrayResult[label]</div>
            					<div class='cell' data-col='3' id='estado_$arrayResult[texto]'>$arrayResult[naturaleza]</div>
            					<div class='cell' data-col='4' id='$arrayResult[texto]' data-cuenta='".$arraySaveCampos[$arrayResult['texto']]['cuenta']."' title='".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."'>".$arraySaveCampos[$arrayResult['texto']]['cuenta']." ".$arraySaveCampos[$arrayResult['texto']]['descripcion']."</div>
	                           	<div class='cell' data-col='1' data-icon='search' onclick='buscarPuc(\"$arrayResult[texto]\")' title='Buscar Cuenta'></div>
	                           	<div class='cell' data-col='1' data-icon='sinc' onclick='homologarCuentaEnNiif(\"$arrayResult[texto]\",\"$arrayResult[naturaleza]\")' title='Sincronizar Niif'></div>
	                        </div>";
	}

?>
<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: 100%; height: 500px; }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: 160px;}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 180px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 65px; text-transform: capitalize;}
    .content-grilla-filtro .cell[data-col="4"]{width: 211px;}
    .sub-content [data-width="input"]{width: 120px;}

</style>

<div class="main-content" style="height: 409px;overflow-y: auto;overflow-x: hidden;">
    <div class="sub-content" data-position="right">
        <div class="title">CUENTAS EN COMPRA</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Tipo</div>
                <div class="cell" data-col="3">Naturaleza</div>
                <div class="cell" data-col="4">Cuenta</div>
                <!-- <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div> -->
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
                <!-- <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div> -->
            </div>
            <div class="body" id="body_grilla_filtro">
            	<?php echo $tableBodyVentas; ?>
            </div>
        </div>

        <div class="title">CUENTAS DE ACTIVO FIJO</div>
        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Tipo</div>
                <div class="cell" data-col="3">Naturaleza</div>
                <div class="cell" data-col="4">Cuenta</div>
                <!-- <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div> -->
            </div>
            <div class="body" id="body_grilla_filtro">
            	<?php echo $tableBodyActivos; ?>
            </div>
        </div>

    </div>

<div id="loadForm" style="display: none;"></div>
</div>

<!-- <div class="contenedor_items_cuentas"><?php echo $divItemsCuenta; ?></div> -->

<script>

	function buscarPuc(idInput){
		// var idInput = inputCuenta.id;

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
		var texto  = idInput
		,	estado = document.getElementById('estado_'+texto).innerHTML
		,	puc    = document.getElementById('div_busquedaPucItems_cuenta_'+idPuc).innerHTML
		,	nombre = document.getElementById('div_busquedaPucItems_descripcion_'+idPuc).innerHTML;

		// estado = (estado == 'D')? 'debito': 'credito';
		if(puc.length > 4){ Win_VentanaBuscarPucItems.close(); }
		else{ alert("Aviso,\nSeleccione una subcuenta o una cuenta auxiliar."); return; }

		MyLoading2('on');

		Ext.get('loadForm').load({
		    url		: 'items/cuentas_default_grupos/bd/bd.php',
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
				id_grupo     : '<?php echo $id_grupo ?>',
		    }
		});
	}

	function homologarCuentaEnNiif(texto,estado){

		var cuenta = document.getElementById(texto).dataset.cuenta
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'items/cuentas_default_grupos/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op       : 'updateCuentaNiif',
				cuenta   : cuenta,
				texto    : texto,
				id_grupo : '<?php echo $id_grupo ?>',
				estado   : estado,
			}
		});
	}

</script>
