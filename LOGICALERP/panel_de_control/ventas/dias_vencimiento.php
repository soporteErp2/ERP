<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");


	$id_empresa = $_SESSION['EMPRESA'];

	$arrayEstado['pedido']     = 'false';
	$arrayEstado['remision']   = 'false';
	$arrayEstado['cotizacion'] = 'false';

	$arrayDocumentos['pedido']     = 30;
	$arrayDocumentos['remision']   = 30;
	$arrayDocumentos['cotizacion'] = 30;

	$sql   = "SELECT dias_vencimiento, documento FROM configuracion_vencimiento_documentos
				WHERE activo=1
				AND ( documento = 'cotizacion'
					OR documento = 'pedido'
					OR documento = 'remision')
				AND id_empresa='$id_empresa'";
	$query = mysql_query($sql,$link);

	while ($row = mysql_fetch_array($query)) {
		$documento = $row['documento'];
		$arrayEstado[$documento]     = 'true';
		$arrayDocumentos[$documento] = $row['dias_vencimiento'];

	}
?>

<div id="barraBotonesVentasVencimiento"></div>
<div style="width:192px; height:25px; float:left; overflow:hidden;">
	<div id="renderSaveVentasVencimiento" style="float:left; overlow:hidden; width:20px;"></div>
	<img id="imgQuestion" src="../../temas/clasico/images/BotonesTabs/help.png"/>
	<div id="titleQuestion">
		Ingrese el numero de dias en los que vence un documento, Esta es una configuracion predeterminada que puede ser modificada al momento de crear el documento.
	</div>
</div>

<?php
	foreach ($arrayDocumentos as $typeDocumento => $valor) {
		echo'<div style="margin:10px; float:left; width:220px;">
				<div style="float:left; width:80px;">'.$typeDocumento.'</div>
				<div style="float:left"><input type="text" class="myField" id="'.$typeDocumento.'" style="width:100px; height:25px" onKeyup="validaNumero(this)" value="'.$valor.'" /></div>
			</div>';
	}


?>

<style type="text/css">

	#imgQuestion{
		float  : right;
		width  : 20px;
		height : 20px;
		margin : 2px 0 0 -20px;
	}

	#imgQuestion:hover ~ #titleQuestion{
		height: 75px;
		width:200px;
		background: #EEE;
		padding: 5px;
		border: 1px solid #000;
	}

	#titleQuestion {
		width              : 0px;
		height             : 0px;
		float              : left;
		position           : fixed;
		margin             : 15px 200px ;
		background         : #EEE;
		overflow           : hidden;
		-webkit-transition :all 0.5s ease;
		-moz-transition    :all 0.5s ease;
		-o-transition      :all 0.5s ease;
		transition         :all 0.5s ease;
	}

</style>

<script type="text/javascript">


	function validaNumero(inputDocumento){
		inputDocumento.value = (inputDocumento.value).replace(/[^0-9]/g,'');
		return true;
	}

	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotonesVentasVencimiento');
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
				handler		: function(){ guardarConfiguracionVentasVencimiento(); }
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

	function guardarConfiguracionVentasVencimiento(){
		var pedido     = document.getElementById('pedido').value
		,	cotizacion = document.getElementById('cotizacion').value
		,	remision   = document.getElementById('remision').value;

		var json = '{"pedido":{'
								+'"estadoDb": "<?php echo $arrayEstado['pedido'] ?>",'
								+'"valor": "'+pedido+'"'
							+'},'
					+'"cotizacion":{'
								+'"estadoDb": "<?php echo $arrayEstado['cotizacion'] ?>",'
								+'"valor": "'+cotizacion+'"'
							+'},'
					+'"remision":{'
								+'"estadoDb": "<?php echo $arrayEstado['remision'] ?>",'
								+'"valor": "'+remision+'"'
							+'}'
					+'}';


		if(isNaN(pedido) || isNaN(cotizacion) || isNaN(remision)){ alert("Todos los campos deben ser numericos!"); return; }

        Ext.get('renderSaveVentasVencimiento').load({
        url     : 'ventas/bd/bd.php',
        timeout : 180000,
        scripts : true,
        nocache : true,
        params  :
            {
				op   : 'guardarDiasDocumentos',
				json : json
            }
        });
	}

</script>