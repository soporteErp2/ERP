<?php include("../../../configuracion/define_variables.php"); ?>

<div style="float:left; margin:10px 0 0 5px;">
    <div id="recibidor_filtro_bodega_<?php echo $opc; ?>" style="float:left; width:140px">
	    <select class="myfield" style="width:140px" id="filtro_asiento_<?php echo $opc; ?>" onChange="carga_<?php echo $opc; ?>('true')" >
	        <option value="asientos_colgaap" >Coolgap</option>
	        <option value="asientos_niif" >Niif</option>
    	</select>
	</div>
</div>
<script>

	function carga_<?php echo $opc; ?>(reloadBody){
		var tabla_asiento = document.getElementById('filtro_asiento_<?php echo $opc; ?>').value;
		<?php
			if($newUrlRender != ''){ $url_render = $newUrlRender; }
			if($renderizaBody == 'true'){
				echo'Ext.get("contenedor_'.$opc.'").load({
						url     : "'.$url_render.'",
						scripts : true,
						nocache : true,
						params  :
						{
							tabla_asiento : tabla_asiento,
							'.$imprimeVarPhp.'
						}
					});';
			}
		?>
	}
	carga_<?php echo $opc; ?>('true');
</script>
