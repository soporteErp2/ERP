<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
<div id="WizCapaPrincipal">
	<div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha"></div>
</div>

<script>
	Ext.get('WizCapaDerecha').load(
		{
			url		:	'wizard_contrato/paso1.php',
			scripts	:true,
			nocache	:true,
			params	:
				{
					
				}
		}
	);

</script>
