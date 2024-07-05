<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$MSucursales = user_permisos(1);


	if($MSucursales == 'true' ){$filtroS = '';}
	if($MSucursales == 'false'){$filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}

	if($MSucursales == 'false' && $_SESSION["SUCURSALORIGEN"] != $_SESSION['SUCURSAL']){
		$filtroS = 'AND id = 0';
		echo '<Script>//Ext.getCmp("btnAgregaEmpleado").disable()</script>';
	}

	$SQL = "SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = ".$_SESSION['EMPRESA']." $filtroS";
	$consulS = mysql_query($SQL,$link);
?>
<div style="float:left; margin: 5px 0 0 10px">
    <div style="float:left; width:50px; padding:3px 0 0 0">Sucursal</div>
    <div id="recibidor_filtro_empresa_<?php echo $opc; ?>" style="float:left; width:190px">
	    <select class="myfield" name="filtro_sucursal_<?php echo $opc; ?>" id="filtro_sucursal_<?php echo $opc; ?>" style="width:190px" onChange="cambia_ubicacion<?php echo $opc; ?>()">
	        <?php
				while($rowS=mysql_fetch_array($consulS)){
					if ($rowS['id'] == $_SESSION['SUCURSAL']){ $select = 'selected'; }
					else { $select = ''; }
				 	echo '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
				}
	        ?>
	    </select>
	</div>
</div>
<div style="float:left; margin: 5px 0 0 10px">
    <div style="float:left; width:50px; padding:3px 0 0 0">
        Bodega
    </div>
    <div id="recibidor_filtro_bodega_<?php echo $opc; ?>" style="float:left; width:190px"></div>
</div>

<script>

	function cambia_ubicacion<?php echo $opc; ?>(){
		filtro_empresa  = <?php echo $_SESSION['EMPRESA']; ?>;
		filtro_sucursal = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value;

		Ext.get('recibidor_filtro_bodega_<?php echo $opc; ?>').load({
			url     : "filtros/filtro_bodega.php",
			scripts : true,
			nocache : true,
			params  :
			{
				opc             : "<?php echo $opc; ?>",
				filtro_empresa  : filtro_empresa,
				filtro_sucursal : filtro_sucursal
			}
		});
	}
	cambia_ubicacion<?php echo $opc; ?>();

</script>
