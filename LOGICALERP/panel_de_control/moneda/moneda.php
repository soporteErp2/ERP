<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	$sqlMonedaDb       = "SELECT id_moneda,decimales_moneda FROM empresas WHERE activo=1 AND id='$idEmpresa' LIMIT 0,1";
	$idMonedaDb        = mysql_result(mysql_query($sqlMonedaDb,$link),0,'id_moneda');
	$decimalesMonedaDb = mysql_result(mysql_query($sqlMonedaDb,$link),0,'decimales_moneda');

	$sqlConfigMonedas   = "SELECT id,descripcion,simbolo,decimales FROM configuracion_moneda WHERE activo=1";
	$queryConfigMonedas = mysql_query($sqlConfigMonedas,$link);

	$optionMoneda = "<option</option>";
	while ($rowConfigMonedas = mysql_fetch_array($queryConfigMonedas)) {
		$selected = ($idMonedaDb == $rowConfigMonedas['id'])? 'selected': '';
		$optionMoneda .= '<option value="'.$rowConfigMonedas['id'].'" '.$selected.'>'.$simbolo.' '.$rowConfigMonedas['descripcion'].'</option>';
	}

?>
<div id="barraBotonesMonedas"></div>
<div style="margin: 10px; overflow:hidden;">
	<div id="loadSaveConfigMoneda" style="width:100%; height:15px; overflow:hidden;"></div>
	<div style="float:left; width:35%;">Moneda</div>
	<div style="float:left; width:60%;">
		<select class="myfield" style="width:100%" id="id_moneda"/><?php echo $optionMoneda; ?></select>
	</div>

	<div style="float:left; width:35%; margin-top:10px;">Decimales</div>
	<div style="float:left; width:60%; margin-top:10px;">
		<input class="myfield" style="width:100%" id="decimales_moneda" value="<?php echo $decimalesMonedaDb; ?>" onKeyup="validaNumero(this)"/>
	<div style="float:left;width:20px;height:23px;" id="cargarInsertDias"></div>
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
				handler		: function(){ guardarConfiguracionMoneda(); }
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

	function validaNumero(Input){ setTimeout(function(){ Input.value = (Input.value).replace(/[^0-9]/g,''); },10); }

	function guardarConfiguracionMoneda(){
		var decimalesMoneda = document.getElementById('decimales_moneda').value
		,	idMoneda = document.getElementById('id_moneda').value;

		if(isNaN(decimalesMoneda)){ alert("Aviso,\nFavor ingrese la cantidad de decimales."); return; }

		Ext.get('loadSaveConfigMoneda').load({
			url     : 'moneda/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op              : 'guardarConfiguracionMoneda',
				idMoneda        : idMoneda,
				decimalesMoneda : decimalesMoneda
			}
		});

	}
</script>