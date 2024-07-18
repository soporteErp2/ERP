<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$id_usuario = $_SESSION['IDUSUARIO'];

	$select     = "SELECT color_menu, color_fondo FROM empleados WHERE id=$id_usuario LIMIT 0,1";
	$query      = mysql_query($select,$link);
	$colorMenu  = mysql_result($query,0,'color_menu');
	$colorFondo = mysql_result($query,0,'color_fondo');

?>
<style>
	#colorFondo,#colorMenu{
		width       :20px;
		float       :left;
		text-indent :-9999px;
		border      :2px outset #c4c4c4;
	}

</style>
<div style="margin:10px 10px 10px 30px; Width:95%; height :100%">
	<div id="renderizaGuardarStyle"></div>
	<div style="overflow:hidden; width:100%">
		<div style="margin-right:5px; float:left; width:50%"><b>Color de fondo</b></div>
		<div style="float:left; width:20%">
			<input type="text" id="colorFondo" value="<?php echo $colorFondo; ?>" readonly="readonly" style="background:rgb(<?php echo $colorFondo; ?>);"/>
		</div>
	</div>

	<div style="overflow:hidden; width:100%; margin-top:5px;">
		<div style="margin-right:5px; float:left; width:50%"><b>Color de menu</b></div>
		<div style="float:left; width:20%">
			<input type="text" id="colorMenu" value="<?php echo $colorMenu; ?>"  readonly="readonly" style="background:rgb(<?php echo $colorMenu; ?>);"/>
		</div>
	</div>
</div>

<script>
	var colorFondoDefault='<?php echo $colorFondo; ?>';
	var colorMenuDefault='<?php echo $colorMenu; ?>';

	$("#colorFondo, #colorMenu").ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(rgb.r+","+rgb.g+","+rgb.b);
			//$(el).val(hex);
			$(el).ColorPickerHide();
			el.style.background = 'rgba('+rgb.r+","+rgb.g+","+rgb.b+',.80)';
		},
		onBeforeShow: function() {
			array=(this.value).split(',');
			colArray={};
			colArray.r=array[0];
			colArray.g=array[1];
			colArray.b=array[2];
			$(this).ColorPickerSetColor(colArray);
		}
	})

	function GuardarStyleColor(){

		var colorFondo = document.getElementById('colorFondo').value;
		var colorMenu  = document.getElementById('colorMenu').value;

		window.parent.cambia_color({
			colorFondo : colorFondo,
			colorMenu  : colorMenu
		});

		var idEmpleado = <?php echo $_SESSION['IDUSUARIO']; ?>;
		Ext.get('renderizaGuardarStyle').load({
			url		: 'bd/bd.php',
			timeout : 180000,
			scripts	: true,
			nocache	: true,
			params	:
					{
						op         : 'guardarStyleColorUsuario',
						idEmpleado : idEmpleado,
						colorFondo : colorFondo,
						colorMenu  : colorMenu
					}
		});
	}

	function cerrarWinStyleColor(){
		window.parent.cambia_color({
			colorFondo : colorFondoDefault,
			colorMenu  : colorMenuDefault
		});

		Win_Panel_Global.close();
	}

	function vistaPreviaStyleColor(){
		var colorFondo = document.getElementById('colorFondo').value;
		var colorMenu  = document.getElementById('colorMenu').value;

		window.parent.cambia_color({
			colorFondo : colorFondo,
			colorMenu  : colorMenu
		});


	}

</script>

