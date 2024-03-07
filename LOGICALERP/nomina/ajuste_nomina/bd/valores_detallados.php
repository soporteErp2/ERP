<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];

	$whereConceptos = ($opc<>'global')? 'AND NPEC.id_empleado = '.$id_empleado : '' ;

	// $sql   = "SELECT id_empleado,documento_empleado,nombre_empleado
	// 			FROM nomina_planillas_ajuste_empleados
	// 			WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_planilla=$id_planilla";
	// $query = $mysql->query($sql,$mysql->link);
	// while ($row=$mysql->fetch_array($query)) {
	// 	if ($opc=='global') {
	// 		# code...
	// 	}
	// 	else{
	// 		$array_empleados[$row['id_empleado']] = array('documento_empleado' => $row['documento_empleado'], 'nombre_empleado'=>$row['nombre_empleado']);
	// 	}
	// }

	$sql   = "SELECT
					NPEC.codigo_concepto,
					NPEC.concepto,
					NPEC.caracter,
					NPEC.cuenta_colgaap,
					NPEC.caracter_contrapartida,
					NPEC.caracter_contrapartida,
					NPEC.cuenta_contrapartida_colgaap,
					NPEC.id_tercero,
					NPEC.id_tercero_contrapartida,
					NPEC.valor_concepto_ajustado
				FROM nomina_planillas_ajuste_empleados_conceptos AS NPEC,
					nomina_conceptos AS NC
				WHERE NPEC.activo=1
				AND NPEC.id_empresa = $id_empresa
				AND NPEC.id_planilla = $id_planilla
				$whereConceptos
				AND NC.concepto_ajustable = 'true'
				AND NC.id=NPEC.id_concepto

				";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdTercero[$row['id_tercero']]=$row['id_tercero'];
		$whereIdTercero[$row['id_tercero_contrapartida']]=$row['id_tercero_contrapartida'];

		if ($row['caracter']=='credito') {
			$array_cuentas[$row['id_tercero']][$row['cuenta_colgaap']]+=$row['valor_concepto_ajustado'];
		}
		if ($row['caracter_contrapartida']=='credito') {
			$array_cuentas[$row['id_tercero_contrapartida']][$row['cuenta_contrapartida_colgaap']]+=$row['valor_concepto_ajustado'];
		}
	}

	foreach ($whereIdTercero as $id_tercero => $valor) {
		$whereIdTerceros .= ($whereIdTerceros=='')? 'id='.$id_tercero : ' OR id='.$id_tercero ;
	}

	// CONSULTAR LOS TERCEROS
	$sql   = "SELECT id,nombre_comercial FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdTerceros) ";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayTerceros[$row['id']]=$row['nombre_comercial'];
	}

	$style = 'color';
	foreach ($array_cuentas as $id_tercero => $array_cuentas_resul) {
		foreach ($array_cuentas_resul as $cuenta => $valor) {
			$style =($style=='')? 'background-color:#EEE;' : '';
			$body .='<div class="filaDivs" style="width:90px;'.$style.'">'.$cuenta.'</div>
						<div class="filaDivs" title="'.$arrayTerceros[$id_tercero].'" style="width:calc(100% - 107px - 94px );'.$style.'">'.$arrayTerceros[$id_tercero].'</div>
						<div class="filaDivs" title="" style="width:100px;border-right:none;text-align:right;padding :  5 3 5 0;'.$style.'">'.number_format($valor,$_SESSION['DECIMALESMONEDA']).'</div>';
		}
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
		margin-left      : 20px;
		border           : 1px solid #D4D4D4;
		/*max-height       : 200px;*/
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

<div id="toolbar_ventana_cuentas_transito" style="height:85px"></div>


<div style="width:100%;">
	<div class="titulos_ventana">VALORES DETALLADOS</div>

	<div style="float:left;width:100%;max-height:180px;overflow:auto;">
		<div class="contenedor_tablas_cuentas">
			<div class="headDivs" style="width:90px;">CUENTA</div>
			<div class="headDivs" style="width:calc(100% - 107px - 94px);">TERCERO</div>
			<div class="headDivs" style="width:100px;border-right:none;">VALOR</div>

			<!-- <div class="filaDivs" style="width:90px;">DEBITO</div>
			<div class="filaDivs" id="cuenta_colgaap_debito" style="width:calc(100% - 107px - 94px );">&nbsp;</div>
			<div class="filaDivs" id="descripcion_cuenta_colgaap_debito" style="width:100px;border-right:none;">&nbsp;</div> -->
			<?php echo $body; ?>
		</div>
	</div>
</div>


<script>


	new Ext.Panel
	(
		{
			renderTo	:'toolbar_ventana_cuentas_transito',
			frame		:false,
			border		:false,
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					columns	: 3,
					title	: 'Opciones',
					items	:
					[
						// {
						// 	xtype		: 'button',
						// 	//id			: 'btn2',
						// 	text		: 'Guadar',
						// 	scale		: 'large',
						// 	iconCls		: 'guardar',
						// 	iconAlign	: 'top',
						// 	handler 	: function(){BloqBtn(this); actualiza_info_empresa();}
						// },
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'top',
							handler 	: function(){Win_Ventana_detalle_valores_planilla.close();}
						}
					]
				}
			]
		}
	);


</script>