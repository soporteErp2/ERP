<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$optionInput = '';
	$MSucursales = user_permisos(1);
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	// CONSULTAR LA BODEGA
	if ($action == 'loadBodega') {

		$sql="SELECT id, nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$filtro_sucursal";
		$query=mysql_query($sql,$link);
		while($row=mysql_fetch_array($query)){
			$optionInput .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
		}

		echo '<div class="text-filter">Bodega</div>
				<select class="myfield" name="filtro_bodega_'.$opc.'" id="filtro_bodega_'.$opc.'" style="width:100%">
					<optgroup label="Todas las Bodegas">
						<option value="global">Todas las Bodegas</option>
					</optgroup>
					<optgroup label="Bodegas">
						'.$optionInput.'
					</optgroup>
				</select>';

		exit;
	}

	if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
	if($MSucursales == 'true'){
		$filtroS     = "";
		$optionInput = '<optgroup label="Todas las Sucursales">
    						<option value="global">Todas las Sucursales</option>
    					</optgroup>';
	}

	$SQL     = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$id_empresa' $filtroS";
	$consulS = mysql_query($SQL,$link);
?>
<style>
	.text-filter {
		font-weight : bold;
		font-size   : 12px;
		font-style  : italic;
		float       : left;
		width: 50px;
	}
</style>
<div style="float:left; margin:3px 3px">
    <div style="float:left; width:50px; padding:3px 0 0 0"></div>
    <div id="recibidor_filtro_sucursal_<?php echo $opc; ?>" style="float:left; width:150px">
    	<div class="text-filter">Sucursal</div>
	    <select class="myfield" name="filtro_sucursal_<?php echo $opc; ?>" id="filtro_sucursal_<?php echo $opc; ?>" style="width:100%" onchange="cambia_filtro_bodega_<?php echo $opc; ?>()">
	        <?php
	        	$optionInput .= '<optgroup label="Sucursales">';
				while($rowS=mysql_fetch_array($consulS)){
					$selected    = ($rowS['id'] == $id_sucursal)? 'selected': '';
					$optionInput .= '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
				}
				$optionInput .= '</optgroup>';

				echo $optionInput;
	        ?>
	    </select>
	</div>
</div>

<div style="float:left; margin:3px 3px">
    <div style="float:left; width:50px; padding:3px 0 0 0"></div>
    <div id="recibidor_filtro_bodega_<?php echo $opc; ?>" style="float:left; width:150px">

	</div>
</div>

<script>
	function cambia_filtro_bodega_<?php echo $opc; ?>(){
		var filtro_sucursal = document.getElementById("filtro_sucursal_<?php echo $opc; ?>").value;
		localStorage.sucursal_items = filtro_sucursal;

		Ext.get("recibidor_filtro_bodega_<?php echo $opc; ?>").load({
			url     : "<?php echo $_SERVER['SCRIPT_NAME']; ?>",
			scripts : true,
			nocache : true,
			params  :
			{
				opc             : '<?php echo $opc; ?>',
				config          : '<?php echo $config; ?>',
				action          : 'loadBodega',
				filtro_sucursal : filtro_sucursal,
			}
		});
	}
	cambia_filtro_bodega_<?php echo $opc; ?>();

</script>