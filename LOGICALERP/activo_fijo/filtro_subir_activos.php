<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
?>
<div style="float:left; margin:0 5px">
    <div style="float:left; width:100%; padding:0; text-align:center; margin:3px 0 7px 0; color:#3e6aaa;">Filtro</div>
    <div style="float:left; width:100%">
	    <select class="myfield" name="filtro_<?php echo $opc; ?>" id="filtro_<?php echo $opc; ?>" style="width:100%" onChange="cambia_<?php echo $opc; ?>()">
	        <option value="">Todos</option>
	        <option value="si">Ingresado</option>
	        <option value="no">Sin Ingresar</option>
	        <option value="repetido">Ya Registrado</option>
	    </select>
	</div>
</div>

<script>

	//imprimeVarPhp = ''; 	// Imprime variables a enviar al renderizar

	function cambia_<?php echo $opc; ?>(){

		var filtro = document.getElementById('filtro_<?php echo $opc; ?>').value;

		Ext.get('contenedor_<?php echo $opc; ?>').load({
			url     : "<?php echo $urlRender; ?>",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro : filtro,
				<?php echo $imprimeVarphp; ?>
			}
		});
	}

	cambia_<?php echo $opc; ?>();

</script>