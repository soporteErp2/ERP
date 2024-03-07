<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($opc) {
		case 'filtro_fecha_interface':
			filtro_fecha_interface();
			break;
	}

	function filtro_fecha_interface(){
		echo '<div style="margin:5px;"><input type="text" id="input_fecha_interface"/></div>
			<script>
				new Ext.form.DateField({
				    format     : "Y-m-d",               //FORMATO
				    width      : 130,                   //ANCHO
				    allowBlank : false,
				    showToday  : true,
				    applyTo    : "input_fecha_interface",
				    minValue   : "2015-01-01",          //MINIMO
				    editable   : false,                 //EDITABLE
				    value      : new Date(),             //VALOR POR DEFECTO
				});
			</script>';
	}

?>