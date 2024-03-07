<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
<div style="float:left; margin:5px 5px">
    <div style="float:left; width:50px; padding:3px 0 0 0"></div>
    <div id="recibidor_filtro_empresa_<?php echo $opc; ?>" style="float:left; width:150px">
	    <select class="myfield" name="filtro_tipo_contabilidad_<?php echo $opc; ?>" id="filtro_tipo_contabilidad_<?php echo $opc; ?>" style="width:100%" onChange="cambia_filtro_tipo_<?php echo $opc; ?>()">
	        <option value="colgaap">Contabilidad Colgaap</option>
	        <option value="niif">Contabilidad Niif</option>
	    </select>
	</div>
</div>

<script>

	//varSelected   = ''		// Cambia el selected de la bodega
	//imprimeVarPhp = ''; 	// Imprime variables a enviar al renderizar

	function cambia_filtro_tipo_<?php echo $opc; ?>(varSelected,imprimeVarPhpExt,newUrlRender){
		<?php echo $imprimeScriptPhp; ?>

		var filtro_tipo_contabilidad = document.getElementById('filtro_tipo_contabilidad_<?php echo $opc; ?>').value;
		var url_render        = ''
		,	opcGrillaContable = '<?php echo $opcGrillaContable; ?>'
		,	contenedor        = '';

		if (filtro_tipo_contabilidad=='colgaap') { url_render = "<?php echo $url_render_colgaap; ?>"; }
		else{ url_render = "<?php echo $url_render_niif; ?>"; }

		//CONDICION PARA EL CONTENEDOR
		if (document.getElementById('contenedor_<?php echo $opcGrillaContable_colgaap; ?>')) { contenedor='contenedor_<?php echo $opcGrillaContable_colgaap; ?>'; }
		else{ contenedor='contenedor_<?php echo $opcGrillaContable_niif; ?>'; }
		
		Ext.get(contenedor).load({
			url     : url_render,
			scripts : true,
			nocache : true,
			params  :
			{
				opc                      : "<?php echo $opcGrillaContable; ?>",
				renderizaBody            : "<?php echo $renderizaBody; ?>",
				url_render               : url_render,
				filtro_tipo_contabilidad : filtro_tipo_contabilidad,
				opcGrillaContable        : "<?php echo $opcGrillaContable; ?>",
				<?php echo $inmprimeVarphp; ?>
			}
		});
	}

	cambia_filtro_tipo_<?php echo $opc; ?>();

</script>
