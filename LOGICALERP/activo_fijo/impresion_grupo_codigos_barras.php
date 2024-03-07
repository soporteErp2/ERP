<?php

	echo '
			<div style="margin:20px 5px 5px 5px">
				<div>
					<input type="radio" name="opcion_imprimir_barras" id="opcion_imprimir_barras" value="buscar_rango_barras" onclick="cargar_opcion_a()" checked/>&nbsp;Por Codigo
					<div style="margin:10px 0px 20px 15px">
						Desde &nbsp;<input type="number" id="desde" value="0101000001" style="margin-right:15px">Hasta &nbsp;<input type="number" id="hasta">
					</div>
				</div>
				
				<div>
					<input type="radio" name="opcion_imprimir_barras" id="opcion_imprimir_barras" value="buscar_fecha_barras" onclick="cargar_opcion_b()"/>&nbsp;Por Fecha
					
					<div style="margin:10px 0px 10px 10px; overflow: hidden; padding-left:5px">
						<div style="float:left;">
							<div style="float:left; width:38px;">Desde</div>
							<div style="float:left; width:150px"><input type="text" id="desde_fecha"></div>
							<div style="float:left; width:36px; padding-left:15px">Hasta</div>
							<div style="float:left; width:150px;"><input type="text" id="hasta_fecha"></div>
						</div>	
					</div>
				</div>
			</div>
		';
		
	echo '
			<script>
			function cargar_opcion_a(){
			document.getElementById("opcion_imprimir_barras").value = "buscar_rango_barras";
			};
			
			function cargar_opcion_b(){
			document.getElementById("opcion_imprimir_barras").value = "buscar_fecha_barras";
			};
			</script>
		';	
?>

<script>
	new Ext.form.DateField(
			{
				format 		:	'Y-m-d', 
				width		:	155,  
				allowBlank	:	false, 
				showToday	:	false, 
				applyTo		:	'desde_fecha',
				editable	:	false
			}
		);
		
	new Ext.form.DateField(
			{
				format 		:	'Y-m-d', 
				width		:	155,  
				allowBlank	:	false, 
				showToday	:	false, 
				applyTo		:	'hasta_fecha',
				editable	:	false
			}
		);	
</script>