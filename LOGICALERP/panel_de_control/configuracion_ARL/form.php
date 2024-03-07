<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];
	$sql="SELECT codigo_arl,codigo_ccf FROM configuracion_arl WHERE activo=1 AND id_sucursal=$filtro_sucursal AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	$codigo_arl = $mysql->result($query,0,'codigo_arl');
	$codigo_ccf = $mysql->result($query,0,'codigo_ccf');
?>

<link rel="stylesheet" type="text/css" href="configuracion_ARL/form.css">
<div class="content" >
	<div id="tbar_config_arl"></div>
	<table class="table-form" style="width:90%;" >
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="3">CONFIGURACION DE LOS CODIGO ARL</td>
		</tr>
		<tr style="background-color: #FFF;">
			<td>Codigo ARL</td>
			<td><input type="text" style="width:190px;" value="<?php echo $codigo_arl; ?>" id="codigo_arl" maxlength="6"></td>
		</tr>

	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	new Ext.Panel
	(
		{
			renderTo	:'tbar_config_arl',
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
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Guadar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'top',
							handler 	: function(){guardar_configuracion(); BloqBtn(this); }
						},
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'top',
							handler 	: function(){Win_Panel_Global.close();}
						}
					]
				}
			]
		}
	);

	function guardar_configuracion(){
		var codigo_arl = document.getElementById('codigo_arl').value

		if (codigo_arl=='') { alert('El campo Codigo de Arl no puede estar vacio'); return; };
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'configuracion_ARL/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc             : 'guardar_configuracion_arl',
				codigo_arl      : codigo_arl,
				filtro_sucursal : '<?php echo $filtro_sucursal; ?>',
			}
		});

	}

</script>