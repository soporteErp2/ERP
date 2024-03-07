<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	$sql = "SELECT tope FROM ventas_pos_tope_facturacion WHERE id_empresa='$idEmpresa' LIMIT 0,1";
	$tope   = mysql_result(mysql_query($sql,$link),0,'tope');

?>
<div id="barraBotonesMonedas"></div>
<div style="overflow: hidden;height: 100%;background-color: #FFF;padding: 10; display:flex;flex-direction: column;align-items: center;">
	<span style="margin-bottom:25px;">
		Aqui se establece el tope en dinero (se debe convertir las UVT a valor en pesos) 
		limite para facturas pos, despues de ese limite se generara una factura electronica 
	</span>
	<div style="display:flex;justify-content: center;width:100%;align-items: center;gap:20px;" >
		<div ><b><i>Tope en pesos</div>
		<div >
			<input 
				style="padding:7px;width:120px;border: 1px solid #ccc;"
				type="text"
				id="tope"
				value="<?=$tope?>"
			>
		</div>
	</div>
	
	<div id="loadSave" style="width:100%; height:15px; overflow:hidden;"></div>
</div>



<script>

	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotonesMonedas');
	tb.add({
		xtype   : 'buttongroup',
		columns : 2,
		items   :
		[
			{
				text		: 'Guardar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'guardar',
				iconAlign	: 'top',
				handler		: function(){ saveConfig(); }
			},
			{
				text		: 'Regresar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'regresar',
				iconAlign	: 'top',
				handler		: function(){ Win_Panel_Global.close(); }
			}
		]
	});
	tb.doLayout();

	function saveConfig(){
		var tope = document.getElementById('tope').value

		Ext.get('loadSave').load({
			url     : 'pos_tope_facturacion/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op    : 'saveConfig',
				tope  : tope,
			}
		});

	}
</script>