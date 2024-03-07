<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$sqlConsecutivo =  "SELECT documento, consecutivo, modulo ,digitos
											FROM configuracion_consecutivos_documentos
											WHERE activo = 1
											AND id_empresa = '$id_empresa'
											AND id_sucursal = '$filtro_sucursal'
											AND modulo = 'venta'
											ORDER BY modulo ASC";
	$queryConsecutivo = mysql_query($sqlConsecutivo,$link);

	$body  = '';
	$title = '';

	while($rowConsecutivo = mysql_fetch_array($queryConsecutivo)){
		$label = str_replace("_", " ", $rowConsecutivo['documento']);
		$body .= '<div style="float:left; width:90px; height:23px; margin-top:10px; text-transform:capitalize;">'.$label.'</div>
							<div style="float:left; width:135px; margin-top:10px;">
								<input type="text" class="myField" id="'.$rowConsecutivo['documento'].'" style="width:75%; margin-right:1px;" data-tipo="consecutivo" onKeyup="validaNumero(this)" value="'.$rowConsecutivo['consecutivo'].'"/>
								<input type="text" class="myField" id="_'.$rowConsecutivo['documento'].'" style="width:24%;" onKeyup="validaNumero(this)" value="'.$rowConsecutivo['digitos'].'"/>
							</div>';
	}

	$arrayDocs = array('cotizacion','pedido','remision','factura','recibo_de_caja');
?>
<style type="text/css">
	#contenedorConsecutivos{
		padding    : 10px;
		height     : 270px;
		width      : 100%;
		overflow   : hidden;
		box-sizing : border-box;
		display    : inline-block;
	}
	#contenedorConsecutivos input{
		float      : left;
		height     : 25px;
		padding    : 3px;
		text-align : right;
	}
	#contenedorConsecutivos > div{
		width    : 100%;
		overflow : hidden;
	}
	#contenedorConsecutivos p{
		float    : left;
		overflow : hidden;
	}
	#contenedorConsecutivos *{
		box-sizing : border-box;
	}
	#loadConsecutivosDocumentos{
		position : fixed;
		width    : 20px;
		height   : 20px;
		margin   : 5px;
		overflow : hidden;
	}
</style>
<div id="barraBotonesConsecutivos"></div>
<div id="loadConsecutivosDocumentos"></div>
<div id="contenedorConsecutivos">
	<div>
		<p style="width:90px; margin-right:1%;">&nbsp;</p>
		<p style="width:92px; margin-right:1%;">CONSECUTIVO</p>
		<p style="width:42px;">D&IacuteGITOS</p>
	</div>
	<?php echo $body; ?>
</div>
<script>
	function guardarConsecutivosDocumentos(){
		var objData = {}
		,	inputs = document.querySelectorAll('[data-tipo=consecutivo]')
		,	camposVacios = 0
		,	totalConsecutivos = 0;

		[].forEach.call(inputs,function(element){
			var consecutivo = element.value;
			var digitos     = document.getElementById("_"+element.id).value;

			if(consecutivo && digitos){
				if(consecutivo > 0){
					totalConsecutivos++;
					objData[element.id] = {
						value   : consecutivo,
						digitos : digitos
					}

				}
				else{
					alert("Por favor ingrese n�meros mayores a 0.");
				}
			}
			else{
			  camposVacios++;
			  if(camposVacios == 1){
					alert("Todos los campos son obligatorios.");
				}
			}
		});

		//Se garantiza envio el objeto lleno y con valores mayores a 0.
		if(totalConsecutivos !== inputs.length){ return false; }

		Ext.get('loadConsecutivosDocumentos').load({
			url     : 'ventas/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :	{
									op              : 'guardarConsecutivosDocumentos',
									jsonData        : JSON.stringify(objData),
									filtro_sucursal : '<?php echo $filtro_sucursal; ?>',
								}
		});
	}

	function validaNumero(input){
		var patron = /[^0-9]/g
		if (patron.test(input.value)) { input.value = (input.value).replace(/[^0-9]/g,''); }
		return true;
	}

	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotonesConsecutivos');
	tb.add([
		{
			text		  : 'Guardar',
			scale		  : 'large',
			width     : 80,
			height 		: 60,
			iconCls		: 'guardar',
			iconAlign	: 'top',
			handler		: function(){ guardarConsecutivosDocumentos(); }
		}
	]);
	tb.doLayout();
</script>
