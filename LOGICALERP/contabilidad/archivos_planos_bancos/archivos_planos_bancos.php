<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
?>
<style>
	.contenedor{
		width  : 100%;
		height : 100%;
	}
	.renglon_export{
		width  : 93%;
		height : 25px;
		float  : left;
		margin : 10px 0px 0px 10px;
	}
	.label_export{
		width : 90px;
		float : left;
	}
	.campo_export{
		width : 143px;
		float : left;
	}
</style>
<div class="contenedor">
	<div class="renglon_export">
		<div class="label_export">Formato Banco</div>
		<div class="campo_export">
			<select id="banco">
				<option value="">Seleccione...</option>
				<option value="1051">Banco Davivienda SA</option>
				<option value="1007">Bancolombia</option>
			</select>
		</div>
	</div>
	<div class="renglon_export">
		<div class="label_export">Fecha Inicial</div>
		<div class="campo_export"><input type="text" id="fecha_inicial"></div>
	</div>
	<div class="renglon_export">
		<div class="label_export">Fecha Final</div>
		<div class="campo_export"><input type="text" id="fecha_final"></div>
	</div>
</div>
<script>
	new Ext.form.DateField({
    emptyText  : 'Seleccione...',  //PLACEHOLDER
    format     : 'Y-m-d',          //FORMATO
    width      : 140,              //ANCHO
    allowBlank : false,
    showToday  : false,
    applyTo    : 'fecha_inicial',
    editable   : false,            //EDITABLE
    listeners  : { select: function() {} }
	});

	new Ext.form.DateField({
    emptyText  : 'Seleccione...',  //PLACEHOLDER
    format     : 'Y-m-d',          //FORMATO
    width      : 140,              //ANCHO
    allowBlank : false,
    showToday  : false,
    applyTo    : 'fecha_final',
    editable   : false,            //EDITABLE
    listeners  : { select: function() {} }
	});

	function genera_archivo_plano(regenera){
		var banco       = document.getElementById('banco').value
		,	fecha_inicial = document.getElementById('fecha_inicial').value
		,	fecha_final   = document.getElementById('fecha_final').value;

		if(banco == ""){
			alert('Debe seleccionar un banco.');
			return;
		}

		if(fecha_inicial == 'Seleccione...' || fecha_inicial == '' || fecha_final == 'Seleccione...' || fecha_final == ''){
			alert('Debe seleccionar la fecha inicial y final.');
			return;
		}

		if(regenera == "true"){
			if(!confirm('\u00BFEsta seguro que desea regenerar el archivo plano?')){
				return;
			}
		}

		window.open("archivos_planos_bancos/bd/descargarArchivoPlano.php?regenera="+regenera+"&banco="+banco+"&fecha_inicial="+fecha_inicial+"&fecha_final="+fecha_final);
	}
</script>
