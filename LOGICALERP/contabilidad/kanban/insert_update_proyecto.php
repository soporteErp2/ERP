<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");





?>

<div style="margin:5px;">
	<div id="loadSaveProyecto" style="overflow:hidden; width:20px; height:20px;"></div>
	<div style="overflow:hidden; height:30px;">
		<div style="float:left; width:45%;">Proyecto</div>
		<div style="float:left; width:55%; height:20px;"><input type="text" id="proyecto" class="myfield"/></div>
	</div>
	<div style="overflow:hidden; height:30px;">
		<div style="float:left; width:45%;">Responsable</div>
		<div style="float:left; width:55%; height:20px;"><input type="text" id="responsable" class="myfield"/></div>
	</div>
	<div style="overflow:hidden; height:30px;">
		<div style="float:left; width:45%;">Fecha Inicio desarrollo</div>
		<div style="float:left; width:55%; height:20px;"><input type="text" id="fecha_inicio" class="myfield"/></div>
	</div>
	<div style="overflow:hidden; height:30px;">
		<div style="float:left; width:45%;">Fecha Final desarrollo</div>
		<div style="float:left; width:55%; height:20px;"><input type="text" id="fecha_desarrollo" class="myfield"/></div>
	</div>
	<div style="overflow:hidden; height:30px;">
		<div style="float:left; width:45%;">Fecha Final soporte</div>
		<div style="float:left; width:55%; height:20px;"><input type="text" id="fecha_soporte" class="myfield"/></div>
	</div>
	<div style="overflow:hidden; height:30px;">
		<div style="float:left; width:45%;">Observaciones</div>
		<div style="float:left; width:55%; height:20px;"><input type="text" id="observaciones" class="myfield"/></div>
	</div>

</div>

<script type="text/javascript">
	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 130,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_inicio',
	    editable   : false,                 //EDITABLE
	    value      : new Date(),             //VALOR POR DEFECTO
	    listeners  : { select: function() {   } }
	});

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 130,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_desarrollo',
	    editable   : false,                 //EDITABLE
	    value      : new Date(),             //VALOR POR DEFECTO
	    listeners  : { select: function() {   } }
	});

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 130,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_soporte',
	    editable   : false,                 //EDITABLE
	    value      : new Date(),             //VALOR POR DEFECTO
	    listeners  : { select: function() {   } }
	});



</script>