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
			url		:	'wizard_carnet/paso1.php',
			scripts	:true,
			nocache	:true,
			params	:
				{
					id			:	<?php echo $id ?>,
					id_empresa	:	<?php echo $id_empresa ?>,
					id_sucursal	:	<?php echo $id_sucursal ?>
				}
		}
	);
</script>
