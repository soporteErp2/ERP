<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
	$id_empresa = $_SESSION['EMPRESA'];

	// CONSULTAR LOS RESTAURANTES CREADOS EN LA OPCION SECCIONES
	$sql="SELECT id,nombre FROM ventas_pos_secciones WHERE activo=1 AND id_empresa=$id_empresa AND restaurante='Si' ";
	$query=$mysql->query($sql,$mysql->link);
	$num_rows = $mysql->num_rows($query);
	if ($num_rows<=0) {
		$options = '<option value="">Sin restaurantes</option>';
		$textoDiv = "<p>
						No hay ninguna seccion creada como restaurante, por favor dirijase al panel de control, en la configuracion
						del Pos en secciones cree una seccion y asignela como restaurante para que pueda configurar mesas
					</p>
					";
		$script = 'Ext.getCmp("btn_nueva_mesa").disable();';
	}
	else{
		while ($row = $mysql->fetch_array($query)){
			$id_seccion = ($id_seccion=='')? $row['id'] : $id_seccion ;
			$restaurante    = ($restaurante=='')? $row['id'] : $restaurante ;
			$options .= "<option value='$row[id]'>$row[nombre]</option>";
		}
	}

	$selectSeccion = "`<select id='seccion' class='myfield' style='height: 25px;width: 155px;font-size: 11px;margin-top: 12px;' onchange='cargarGrilla()' >
							$options
						</select>`";


?>
<style>
	.content p{
		padding   : 15px;
		font-size : 12px;
	}
</style>
<div class="content">
	<div id="toolBar"></div>
	<div id="contentGrilla">
		<?php echo $textoDiv; ?>
	</div>
</div>
<script>
	// BARRA DE BOTONES DE LA VENTANA
	// var htmlSelectSeccion = `<select>`;
	var tb = new Ext.Toolbar();
	tb.render('toolBar');
	tb.add({
		xtype   : 'buttongroup',
		columns : 3,
		items   :
		[
			{
				xtype     : 'panel',
				border    : false,
				width     : 160,
				height    : 56,
				bodyStyle : 'background-color:rgba(255,255,255,0)',
				html      : <?php echo $selectSeccion ?>,
			},
			{
				text      : 'Nueva',
				scale     : 'large',
				id        : 'btn_nueva_mesa',
				width     : 80,
				height    : 60,
				iconCls   : 'mesa',
				iconAlign : 'top',
				handler   : function(){ Agregar_ventas_pos_mesas(); }
			},
			{
				text		: 'Regresar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'regresar',
				iconAlign	: 'top',
				handler		: function(){Win_Panel_Global.close();}
			}
		]
	});
	tb.doLayout();
	<?php echo $script; ?>

	// CARGAR LA GRILLA CON LA SECCION SELECCIONADA
	var cargarGrilla = () => {
		var select  = document.getElementById('seccion')
		,	seccion = select.options[select.selectedIndex].innerText
		Ext.get('contentGrilla').load({
			url     : 'configuracion_mesas_pos/grillaMesas.php',
			scripts : true,
			nocache : true,
			params  :
			{
				id_seccion : select.value,
				seccion    : seccion,
			}
		});
	}

	cargarGrilla();

</script>