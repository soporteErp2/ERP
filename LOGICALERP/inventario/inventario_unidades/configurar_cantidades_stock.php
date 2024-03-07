<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$sql   = "SELECT cantidad_minima_stock,cantidad_maxima_stock FROM inventario_totales WHERE activo=1 AND id='$id' AND id_empresa=".$_SESSION['EMPRESA']." LIMIT 0,1";
	$query = mysql_query($sql,$link);

	$cantidad_minima = mysql_result($query,0,'cantidad_minima_stock');
	$cantidad_maxima = mysql_result($query,0,'cantidad_maxima_stock');
	if ($dias==''){ $dias='30'; }

?>

<div style="float:left;font-size:12px; font-weight:normal; margin-bottom:20px; overflow:hidden; margin: 5px 10px;">
	<div style="float:left; width:95%; height:23px; overflow:hidden;">
		<div style="float:left; width:23px; height:23px; overflow:hidden;" id="cargarValoresCantidadStock"></div>
	</div>
	<div style="margin:0 auto 0 auto;width:260px; overflow:hidden;">
		<div style="float:left; width: 110px;">Stock minimo</div>
		<div style="float:left; width: 150px;"><input type="text" class="myField" id="minimoStock" style="width:140px; height:25px" onKeyup="validaNumero(this)" value="<?php echo $cantidad_minima; ?>" /></div>
	</div>
	<div style="margin:10px auto 0 auto;width:260px; overflow:hidden;">
		<div style="float:left; width: 110px;">Stock maximo</div>
		<div style="float:left; width: 150px;"><input type="text" class="myField" id="maximoStock" style="width:140px; height:25px" onKeyup="validaNumero(this)" value="<?php echo $cantidad_maxima; ?>" /></div>
	</div>
</div>		

<script>
	op='<?php echo $op; ?>';
	function validaNumero(Input){
		setTimeout(function(){ Input.value = (Input.value).replace(/[^0-9]/g,''); },10);
	}

	function guardarValoresCantidadStock(){
		var minimo=document.getElementById("minimoStock")
		,   maximo=document.getElementById("maximoStock");

		if (minimo.value==0) {	alert("Error!\nDebe Ingresar un valor minimo para este articulo en el stock"); minimo.focus(); return;}
		else if (maximo.value==0) {	alert("Error!\nDebe ingresar un valor maximo para este articulo en el stock"); maximo.focus(); return;}
		if ((minimo.value*1) > (maximo.value*1)) {	alert("Error!\nEl valor minimo del stock debe ser menor al valor maximo!"); return;};

		Ext.get("cargarValoresCantidadStock").load({
				url		: 'inventario_unidades/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op              : 'guardarValoresCantidadStock',
					cantidad_minima : minimo.value,
					cantidad_maxima : maximo.value,
					id              : '<?php echo $id; ?>',
					idBodega        : document.getElementById("filtro_ubicacion_inventario").value
				}
			});
	}

</script>