<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$MSucursales = user_permisos(83);
	$MEmpresas   = user_permisos(82);

	if($MSucursales == 'true'  && $MEmpresas == 'true' ){$filtroS = '';}
	if($MSucursales == 'true'  && $MEmpresas == 'false'){$filtroS = '';}
	if($MSucursales == 'false' && $MEmpresas == 'false'){$filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}
	if($MSucursales == 'false' && $MEmpresas == 'true' ){$filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}

	if($MSucursales == 'false' && $_SESSION["SUCURSALORIGEN"] != $_SESSION['SUCURSAL']){
		$filtroS = 'AND id = 0';
		echo '<Script>//Ext.getCmp("btnAgregaEmpleado").disable()</script>';
	}

	$SQL     = "SELECT id,nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa = '$_SESSION[EMPRESA]' AND id_sucursal = '$filtro_sucursal' $filtroS";
	$consulS = mysql_query($SQL,$link);

	$idSelected = '';
	if($varSelected != ''){ $idSelected = $varSelected; }
	else{ $idSelected = $_SESSION['SUCURSAL']; }
?>


    <select class="myfield" name="filtro_ubicacion_<?php echo $opc; ?>" id="filtro_ubicacion_<?php echo $opc; ?>" style="width:190px; height:23px;" onChange="carga_<?php echo $opc; ?>('true')">
        <?php
            echo '<option value="0" selected>TODAS</option>';
			while($rowS=mysql_fetch_array($consulS)){
				$selected = ($rowS['id'] == $idSelected)? 'selected': '';
				echo '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
			}
        ?>
    </select>

<script>

	function carga_<?php echo $opc; ?>(reloadBody){
		var filtro_empresa  = "<?php echo $filtro_empresa; ?>"
		,	filtro_sucursal = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value
		,	filtro_bodega   = document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').value;

		<?php
			if($newUrlRender != ''){ $url_render = $newUrlRender; }
			if($renderizaBody == 'true'){
				echo'Ext.get("contenedor_'.$opc.'").load({
						url     : "'.$url_render.'",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega                : filtro_bodega,
							filtro_sucursal              : filtro_sucursal,
							opcGrillaContable            : "'.$opcGrillaContable.'",
							tablaPrincipal               : "'.$tablaPrincipal.'",
							idTablaPrincipal             : "'.$idTablaPrincipal.'",
							tablaInventario              : "'.$tablaInventario.'",
							tablaRetenciones             : "'.$tablaRetenciones.'",
							carpeta                      : "'.$carpeta.'",
							tablaCotizacionPedido        : "'.$tablaCotizacionPedido.'",
							nombreGrillaCotizacionPedido : "'.$nombreGrillaCotizacionPedido.'",
							opcCargar                    : "'.$opcCargar.'",
							'.$imprimeVarPhp.'
						}
					});';
			}
		?>
	}
	carga_<?php echo $opc; ?>('true');

</script>
