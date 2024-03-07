<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$sql   = "SELECT dias_vencimiento FROM configuracion_vencimiento_documentos WHERE activo=1 AND documento='orden de compra' AND id_empresa=".$_SESSION['EMPRESA']." LIMIT 0,1";
	$query = mysql_query($sql,$link);
	$dias  = mysql_result($query,0,'dias_vencimiento');
	if ($dias==''){ $dias='30'; }
?>
<div id="barraBotones"></div>
<div style="margin:10px; float:left;">
	<div style="float:left; width:100px;">Numero de Dias</div>
	<div style="float:left"><input type="text" class="myField" id="diasFechaOrden" style="width:100px; height:25px" onKeyup="validaNumero(this)" value="<?php echo $dias; ?>" /></div>
	<div id="divimgQuestion">
		<img id="imgQuestion" src="../../temas/clasico/images/BotonesTabs/help.png"/>
	</div>
	<div id="divTitle">Digite el numero de dias en que vence una Orden, Esta es una configuracion predeterminada que puede ser modificada al momento de crear el documento.</div>
	<div style="float:left;width:20px;height:23px;" id="cargarInsertDias"></div>
</div>
<style type="text/css">

	#divimgQuestion{
		float  : left;
		width  : 20px;
		height : 20px;
		margin : 2px 0 0 -20px;
	}

	#imgQuestion{
		width  : 100%;
		height : 100%;
		float  : left;
	}


	#divimgQuestion:hover ~ #divTitle{
		height     : 75px;
		width      : 200px;
		background : #EEE;
		padding    : 5px;
		border     : 1px solid #000;
	}

	#divTitle{
		width              : 0px;
		height             : 0px;
		float              : left;
		position           : fixed;
		margin             : 15px 200px;
		background         : #EEE;
		overflow           : hidden;
		-webkit-transition :all 0.5s ease;
		-moz-transition    :all 0.5s ease;
		-o-transition      :all 0.5s ease;
		transition         :all 0.5s ease;
	}


</style>
<script>

	function guardarDiasOrdenCompra(){
		var dias=document.getElementById('diasFechaOrden');

		if (dias.value=='' || dias.value ==0) { alert('Error!\nDebe digitar un valor'); dias.focus(); return;};

		Ext.get('cargarInsertDias').load({
			url     : 'compras/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op   : 'guardarDiasOrdenes',
				opc  : 'orden de compra',
				dias : dias.value
			}

		});
	}

	function validaNumero(Input){
		var patron= /[^0-9]/g
		if (patron.test(Input.value)) { Input.value = (Input.value).replace(/[^0-9]/g,''); }
		return true;
	}

	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotones');
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
				handler		: function(){guardarDiasOrdenCompra();}
			},
			{
				text		: 'Regresar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'regresar',
				iconAlign	: 'top',
				handler		: function(){Win_Panel_Global.close();}
			}
		]
	});
	tb.doLayout();


</script>
