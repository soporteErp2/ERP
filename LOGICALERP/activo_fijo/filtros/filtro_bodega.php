<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$MSucursales = user_permisos(1);

	if($MSucursales == 'true' ){$filtroS = '';}
	if($MSucursales == 'false'){$filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}

	if($MSucursales == 'false' && $_SESSION["SUCURSALORIGEN"] != $_SESSION['SUCURSAL']){
		$filtroS = 'AND id = 0';
	}

	$SQL     = "SELECT id,nombre FROM empresas_sucursales_bodegas WHERE id_empresa = ".$_SESSION['EMPRESA']." AND id_sucursal = $filtro_sucursal $filtroS";
	$consulS = mysql_query($SQL,$link);
?>

  <select class="myfield" name="filtro_ubicacion_<?php echo $opc; ?>" id="filtro_ubicacion_<?php echo $opc; ?>" style="width:190px" onChange="carga_<?php echo $opc; ?>()">
    <?php
			while($rowS=mysql_fetch_array($consulS)){
				if($rowS['id'] == $_SESSION['SUCURSAL']){ echo 'selected'; }
				else{ $selected=''; }
				echo '<option value="'.$rowS['id'].' '.$selected.'">'.$rowS['nombre'].'</option>';
			}
    ?>
  </select>

<script>

	function carga_<?php echo $opc; ?>(){
		filtro_empresa   = <?php echo $filtro_empresa; ?>;
		filtro_sucursal  = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value;
		filtro_ubicacion = document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').value;

		Ext.get('contenedor_<?php echo $opc; ?>').load({
			url     : "<?php if($url_render==''){ echo $opc; }else{ echo $url_render;}  ?>.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_ubicacion : filtro_ubicacion,
				filtro_empresa   : filtro_empresa,
				filtro_sucursal  : filtro_sucursal,
				<?php echo $imprimeVarPhp ?>
			}
		});
	}
	carga_<?php echo $opc; ?>();

</script>
