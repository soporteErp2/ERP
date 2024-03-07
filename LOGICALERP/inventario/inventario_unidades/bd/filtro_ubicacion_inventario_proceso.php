<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	
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
	
	$consulS = mysql_query("SELECT id,nombre FROM empresas_sucursales_bodegas WHERE id_empresa = $filtro_empresa AND id_sucursal = $filtro_sucursal $filtroS",$link);

?>

    <select class="myfield" name="filtro_ubicacion_<?php echo $opc; ?>" id="filtro_ubicacion_<?php echo $opc; ?>" style="width:190px" onChange="carga_<?php echo $opc; ?>()">
        <?php
			while($rowS=mysql_fetch_array($consulS)){
		?>
			<option value="<?php echo $rowS['id']?>" <?php if($rowS['id']==$_SESSION['SUCURSAL']){echo 'selected';}?>><?php echo $rowS['nombre']?></option>
        <?php
			}
        ?>
    </select>

<script>

function carga_<?php echo $opc; ?>(){
	var filtro_empresa_<?php echo $opc; ?> = <?php echo $filtro_empresa; ?>;
	var filtro_sucursal_<?php echo $opc; ?> = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value;
	var filtro_ubicacion_<?php echo $opc; ?> = document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').value;
	
	ubicacion_inventario_proceso=filtro_ubicacion_<?php echo $opc; ?>;
	/*
	var prueba=filtro_sucursal_<?php echo $opc; ?>+"%"+filtro_ubicacion_<?php echo $opc; ?>;
	alert(prueba);
	*/
	Ext.get('contenedor_traslado_<?php echo $opc; ?>').load(
		{	
			
			url	: "inventario_traslado_global.php",
			scripts:true,
			nocache:true,
			params:{
				filtro_ubicacion_traslado 	: filtro_ubicacion_<?php echo $opc; ?>,
				filtro_sucursal_traslado		: filtro_sucursal_<?php echo $opc; ?>,
			}
		}
	);
}
carga_<?php echo $opc; ?>();	

</script>	
