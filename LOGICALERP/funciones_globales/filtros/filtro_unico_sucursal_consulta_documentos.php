<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$MSucursales = user_permisos(1);
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
	if($MSucursales == 'true'){ $filtroS = ""; }
	if($MSucursales == 'true'){
		$filtroS     = "";
		$optionInput = '<optgroup label="Todas las Sucursales">
    						<option value="global">Todas las Sucursales</option>
    					</optgroup>';
	}

	$SQL     = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$id_empresa' $filtroS";
	$consulS = mysql_query($SQL,$link);
?>
<div style="float:left; margin: 5px 0 0 10px;">
    <div style="float:left; width:50px; padding:3px 0 0 0;"></div>
    <div id="recibidor_filtro_empresa_<?php echo $opc; ?>" style="float:left; width:150px">
	    <select class="myfield" class="myfield" name="filtro_sucursal_<?php echo $opc; ?>" id="filtro_sucursal_<?php echo $opc; ?>" style="width:150px" onChange="cambia_filtro_<?php echo $opc; ?>()">
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


<script>
	<?php echo $script; ?>
	//varSelected   = ''	// Cambia el selected de la bodega
	//imprimeVarPhp = ''; 	// Imprime variables a enviar al renderizar

	function cambia_filtro_<?php echo $opc; ?>(varSelected,imprimeVarPhpExt,newUrlRender){

		<?php echo $imprimeScriptPhp; ?>

		filtro_sucursal = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value;

		if (document.getElementById('filtro_tipo_documento')) { tipo_documento_cruce=document.getElementById('filtro_tipo_documento').value; }
		else{ tipo_documento_cruce='<?php echo $documento_cruce; ?>'; }

		Ext.get('<?php echo $contenedor; ?>').load({
			url     : "<?php echo $url_render; ?>",
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : "<?php echo $opc; ?>",
				renderizaBody        : "<?php echo $renderizaBody; ?>",
				filtro_sucursal      : filtro_sucursal,
				tipo_documento_cruce : tipo_documento_cruce,
				cont                 : "<?php echo $cont; ?>",
				opcGrillaContable    : "<?php echo $opc; ?>",
				tablaPrincipal       : "<?php echo $tablaPrincipal; ?>",
				idTablaPrincipal     : "<?php echo $idTablaPrincipal; ?>",
				tablaCuentasNota     : "<?php echo $tablaCuentasNota; ?>",
				<?php echo $imprimeVarPhp; ?>
			}
		});

	}

	cambia_filtro_<?php echo $opc; ?>();

</script>
