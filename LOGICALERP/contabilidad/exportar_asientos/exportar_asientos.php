<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];

	// CONSULTAR LOS TIPO DE DOCUMENTOS
	$sql   = "SELECT tipo_documento,tipo_documento_extendido FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa GROUP BY tipo_documento ORDER BY tipo_documento ASC";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) { $tipo_documento.='<option value="'.$row['tipo_documento'].'">'.$row['tipo_documento'].' - '.$row['tipo_documento_extendido'].'</option>'; }

	$sql   = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) { $sucursales.='<option value="'.$row['id'].'">'.$row['nombre'].' </option>'; }

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
		width : 160px;
		float : left;
	}

</style>

<div class="contenedor">

	<div class="renglon_export">
		<div class="label_export">Contabilidad</div>
		<div class="campo_export">
			<select id="contabilidad">
				<option value="colgaap">Colgaap</option>
				<option value="niif">Niif</option>
			</select>
		</div>
	</div>

	<div class="renglon_export">
		<div class="label_export">Sucursal</div>
		<div class="campo_export">
			<select id="id_sucursal" style="font-style:italic;">
				<optgroup label="Todas las Sucursales">
			    	<option value="todos">Todas las sucursales</option>
			  	</optgroup>
				<optgroup label="Sucursales">
			    	<?php echo $sucursales ?>
			  	</optgroup>
			</select>
		</div>
	</div>

	<div class="renglon_export">
		<div class="label_export">Tipo Documento</div>
		<div class="campo_export">
			<select id="tipo_documento" style="font-style:italic;">
				<optgroup label="Todos los Documentos">
			    	<option value="todos">Todos los documentos</option>
			  	</optgroup>
				<optgroup label="Documento">
			    	<?php echo $tipo_documento ?>
			  	</optgroup>
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
	    emptyText  : 'Seleccione...',    //PLACEHOLDER
	    format     : 'Y-m-d',               //FORMATO
	    width      : 130,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_inicial',
	    editable   : false,                 //EDITABLE
	    // value      : new Date(),             //VALOR POR DEFECTO
	    listeners  : { select: function() {   } }
	});

	new Ext.form.DateField({
	    emptyText  : 'Seleccione...',    //PLACEHOLDER
	    format     : 'Y-m-d',               //FORMATO
	    width      : 130,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_final',
	    editable   : false,                 //EDITABLE
	    // value      : new Date(),             //VALOR POR DEFECTO
	    listeners  : { select: function() {   } }
	});


	function genera_excel(type){
		var contabilidad   = document.getElementById('contabilidad').value
		,	id_sucursal    = document.getElementById('id_sucursal').value
		,	tipo_documento = document.getElementById('tipo_documento').value
		,	fecha_inicial  = document.getElementById('fecha_inicial').value
		,	fecha_final    = document.getElementById('fecha_final').value;

		if (fecha_inicial=='Seleccione...' || fecha_inicial=='' || fecha_final=='Seleccione...' || fecha_final=='') { alert('Debe seleccionar la fecha inicial y final'); return; }

		window.open("exportar_asientos/bd/bd.php?contabilidad="+contabilidad+"&id_sucursal="+id_sucursal+"&tipo_documento="+tipo_documento+"&fecha_inicial="+fecha_inicial+"&fecha_final="+fecha_final+"&type="+type);

	}

</script>