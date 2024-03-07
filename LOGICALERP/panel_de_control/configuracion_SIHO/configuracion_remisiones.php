<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];
	$sql    = "SELECT valor FROM ventas_remisiones_configuracion WHERE activo=1 AND id_empresa=$id_empresa";
	$query  = $mysql->query($sql,$mysql->link);
	$tipo = $mysql->result($query,0,'valor');
	if ($tipo<>'') {
		$script = "document.getElementById('tipo_valor').value='$tipo';";
	}

?>

<style>
	.titulos_ventana{
		color       : #15428B;
		font-weight : bold;
		font-size   : 13px;
		font-family : tahoma,arial,verdana,sans-serif;
		text-align  : center;
		margin-top  : 15px;
		float       : left;
		width       : 100%;
	}

	.contenedor_tablas_cuentas{
		float            : left;
		width            : 90%;
		background-color : #FFF;
		margin-top       : 10px;
		margin-left      : 10px;
		border           : 1px solid #D4D4D4;
	}

	.headDivs{
		float            : left;
		background-color : #F3F3F3;
		padding          : 5 0 5 3;
		font-size        : 11px;
		font-weight      : bold;
		border-right     : 1px solid #D4D4D4;
		border-bottom    : 1px solid #D4D4D4;
	}

	.filaDivs{
		float         : left;
		border-right  : 1px solid #D4D4D4;
		padding       :  5 0 5 3;
		overflow      : hidden;
		white-space   : nowrap;
		text-overflow : ellipsis;
	}

	.divIcono{
		float            : left;
		width            : 20px;
		height           : 16px;
		padding          : 3 0 4 5;
		background-color : #F3F3F3;
		overflow         : hidden;
	}

	.divIcono>img{
		cursor : pointer;
		width  : 16px;
		height : 16px;
	}

</style>

<!-- <div id="toolbar_ventana_cuentas_transito" style="height:85px"></div> -->


<div style="width:100%;">
	<div class="titulos_ventana">VALOR ITEMS EN REMISIONES</div>

	<div class="contenedor_tablas_cuentas">
		<div class="headDivs" style="width:calc(100% - 3px);border-right:none;text-align:center;">Valor a tomar de los items</div>
		<div class="filaDivs" style="width:calc(100% - 3px);text-align:center;" id="tercero">
			<select onchange="guardaTipoValor(this.value)" id='tipo_valor'>
				<option value="PV">Precio de Venta</option>
				<option value="CI">Costo en Inventario</option>
			</select>
		</div>

	</div>

</div>
<div id="loadForm" style="display:none;"></div>

<script>
	<?php echo $script; ?>
	function guardaTipoValor(tipo) {

		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'configuracion_SIHO/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc     : 'guardaTipoValor',
				tipo      : tipo,
			}
		});
	}

</script>