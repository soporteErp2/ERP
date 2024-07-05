<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($opc) {

		case 'panel_filtro_fechas':
			panel_filtro_fechas();
			break;

		case 'panel_filtro_contabilidad':
			panel_filtro_contabilidad();
			break;

		case 'panel_tipo_cuentas':
			panel_tipo_cuentas();
			break;

	}

	function panel_filtro_fechas(){
		echo'<div style="width:220px; height:65px; margin:0px 10px;">
				<div style="float:left; width:90px; margin-top:5px;">
					Fecha inicial:
				</div>
				<div style="float:left; width:90px; margin-top:5px;">
					<input type="text" id="filtroFechaInicial" />
				</div>
				<div style="float:left;width:90px; margin-top:5px;">
					Fecha final:
				</div>
				<div style="float:left; width:90px; margin-top:5px;">
					<input type="text" id="filtroFechaFinal"/>
				</div>
			</div>
			<script>
				new Ext.form.DateField({
					format     : "Y-m-d",
					width      : 90,
					allowBlank : false,
					showToday  : true,
					applyTo    : "filtroFechaInicial",
					value: new Date(),
					editable   : false
		        });

				new Ext.form.DateField({
					format     : "Y-m-d",
					width      : 90,
					allowBlank : false,
					showToday  : true,
					applyTo    : "filtroFechaFinal",
					value: new Date(),
					editable   : false
				});
			</script>';
	}

	function panel_filtro_contabilidad(){
		echo'<div style="overflow:hidden;">
				<div style="float:left; width:80px; margin-top:5px;">Contabilidad</div>
				<select id="filtro_contabilidad" style="float:left; width:140px;">
					<option value="colgaap">Norma Colgaap</option>
					<option value="niif">Norma Niif</option>
				</select>
			</div>
			<div style="overflow:hidden; margin-top:5px;">
				<div style="float:left; width:80px; margin-top:5px;">Documento</div>
				<select id="filtro_documento" style="float:left; width:140px;">
					<option value="">Todos</option>
					<optgroup></optgroup>

					<optgroup label="VENTAS">
						<option value="RV">Remisiones</option>
						<option value="FV">Facturas de Venta</option>
						<option value="RC">Recibo de Caja</option>
					</optgroup>
					<optgroup></optgroup>

					<optgroup label="COMPRAS">
						<option value="FC">Facturas de Compra</option>
						<option value="CE">Comprobantes de Egreso</option>
					</optgroup>
					<optgroup></optgroup>

					<optgroup label="CONTABILIDAD">
						<option value="NCG">Notas Contables</option>
						<option value="NDFC">Devoluciones en Compra</option>
						<option value="NDFV">Devoluciones en Venta</option>
					</optgroup>
					<optgroup></optgroup>

					<optgroup label="NOMINA">
						<option value="LN">Liquidacion Nomina</option>
						<option value="LE">Liquidacion Empleado</option>
					</optgroup>
					<optgroup></optgroup>
				</select>
			</div>

			<style>
				optgroup{ font-style: italic; }
				option{ font-style: normal !important; }
			</style>';
	}

	function panel_tipo_cuentas(){
		echo'<div style="overflow:hidden; margin-top:8px;">
				<select id="filtro_tipo_cuenta" style="float:left; width:140px;">
					<option value="todas">Todas</option>
					<option value="cuenta_pago">Cuentas de Pago</option>
				</select>
			</div>';
	}

?>