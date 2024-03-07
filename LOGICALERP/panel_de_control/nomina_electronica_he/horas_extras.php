<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	$sql = "SELECT tipo_hora FROM nomina_configuracion_hora_extra WHERE activo=1 AND id_empresa='$idEmpresa' LIMIT 0,1";
	$tipo_hora   = mysql_result(mysql_query($sql,$link),0,'tipo_hora');

?>
<div id="barraBotonesMonedas"></div>
<div style="margin: 10px; overflow:hidden;">
	<span>
		Al enviar la nomina electronica la dian recibe las horas extras en horas, si usted maneja
		las horas extras en minutos o segundos, seleccione a acontinuacion como lo hace, esto con 
		el fin de que el sistema calcule automaticamente la cantida de horas a reportar a la dian, por
		ejemplo, si maneja la hora extra en minutos y registra 120 en la nomina, entonces el sistema
		calculara y enviara a la dian 2 y no 120
	</span>
	<div id="loadSave" style="width:100%; height:15px; overflow:hidden;"></div>
	<div style="float:left; width:60%;"><b><i>Como ingresa las horas extras?</div>
	<div style="float:left; width:40%;">
		<select class="myfield" style="width:100%" id="tipo">
			<option <?php if($tipo_hora=="hora"){ echo "selected"; } ?> value="hora">Horas</option>
			<option <?php if($tipo_hora=="minutos"){ echo "selected"; } ?> value="minutos">Minutos</option>
			<option <?php if($tipo_hora=="segundos"){ echo "selected"; } ?> value="segundos">Segundos</option>
		</select>
	</div>
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
		var tipo = document.getElementById('tipo').value

		Ext.get('loadSave').load({
			url     : 'nomina_electronica_he/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op    : 'saveConfig',
				tipo  : tipo,
			}
		});

	}
</script>