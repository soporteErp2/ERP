<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
<div style="float:left;padding:5px;">Fecha Inicio<input type="text" id="fecha_inicio_<?php echo $opc; ?>"></div>
<div style="float:left;padding:5px;">Fecha Fin<input type="text" id="fecha_fin_<?php echo $opc; ?>"></div>
<script type="text/javascript">
	if(localStorage.fecha_inicio_<?php echo $opc; ?> != null && localStorage.fecha_fin_<?php echo $opc; ?> != null){
		valueInicio = localStorage.fecha_inicio_<?php echo $opc; ?>;
		valueFin    = localStorage.fecha_fin_<?php echo $opc; ?>;
	}
  else{
    valueInicio = '<?php echo date('Y-m-d'); ?>';
    valueFin    = '<?php echo date('Y-m-d'); ?>';
  }

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 100,
    allowBlank : false,
    showToday  : false,
    applyTo    : "fecha_inicio_<?php echo $opc; ?>",
    editable   : false,
		value      : valueInicio,
  });

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 100,
    allowBlank : false,
    showToday  : false,
    applyTo    : "fecha_fin_<?php echo $opc; ?>",
    editable   : false,
		value      : valueFin,
  });
</script>
