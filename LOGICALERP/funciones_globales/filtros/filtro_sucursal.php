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

	$SQL     = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$_SESSION[EMPRESA]' $filtroS";
	$consulS = mysql_query($SQL,$link);
?>
<div style="float:left; margin: 5px 0 0 10px">
    <div style="float:left; width:50px; padding:3px 0 0 0">Sucursal</div>
    <div id="recibidor_filtro_empresa_<?php echo $opc; ?>" style="float:left; width:190px">
	    <select class="myfield" name="filtro_sucursal_<?php echo $opc; ?>" id="filtro_sucursal_<?php echo $opc; ?>" style="width:190px" onChange="cambia_filtro_bodega_<?php echo $opc; ?>()">
	        <?php
				while($rowS=mysql_fetch_array($consulS)){
					$selected = ($rowS['id'] == $_SESSION['SUCURSAL'])? 'selected': '';
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

	//varSelected   = ''		// Cambia el selected de la bodega
	//imprimeVarPhp = ''; 	// Imprime variables a enviar al renderizar

	function cambia_filtro_bodega_<?php echo $opc; ?>(varSelected,imprimeVarPhpExt,newUrlRender){
		var filtro_empresa  = "<?php echo $_SESSION['EMPRESA']; ?>"
		,	filtro_sucursal = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value;

		Ext.get('recibidor_filtro_bodega_<?php echo $opc; ?>').load({
			url     : "<?php echo $url_filtro_bodega; ?>",
			scripts : true,
			nocache : true,
			params  :
			{
				opc                          : "<?php echo $opc; ?>",
				renderizaBody                : "<?php echo $renderizaBody; ?>",
				newUrlRender                 : newUrlRender,
				url_render                   : "<?php echo $url_render; ?>",
				filtro_empresa               : filtro_empresa,
				filtro_sucursal              : filtro_sucursal,
				varSelected                  : varSelected,
				opcGrillaContable            : "<?php echo $opcGrillaContable; ?>",
				tablaPrincipal               : '<?php echo $tablaPrincipal; ?>',
				idTablaPrincipal             : '<?php echo $idTablaPrincipal; ?>',
				tablaInventario              : '<?php echo $tablaInventario; ?>',
				tablaRetenciones             : '<?php echo $tablaRetenciones; ?>',
				carpeta                      : '<?php echo $carpeta; ?>',
				tablaCotizacionPedido        : '<?php echo $tablaCotizacionPedido; ?>',
				nombreGrillaCotizacionPedido : '<?php echo $nombreGrillaCotizacionPedido; ?>',
				opcCargar                    : '<?php echo $opcCargar; ?>',
				imprimeVarPhp                : imprimeVarPhpExt
			}
		});
	}
	cambia_filtro_bodega_<?php echo $opc; ?>();

</script>