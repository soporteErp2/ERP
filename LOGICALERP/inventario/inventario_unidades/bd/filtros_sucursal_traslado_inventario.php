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
	
	$consulS = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = $empresa $filtroS",$link);

?>

    <select class="myfield" name="filtro_sucursal_<?php echo $opc; ?>" id="filtro_sucursal_<?php echo $opc; ?>" style="width:190px" onChange="cambia_ubicacion<?php echo $opc; ?>()">
        <?php
			while($rowS=mysql_fetch_array($consulS)){
		?>
			<option value="<?php echo $rowS['id']?>" <?php if($rowS['id']==$_SESSION['SUCURSAL']){echo 'selected';}?>><?php echo $rowS['nombre']?></option>
        <?php
			}
        ?>
    </select>

<script>

function cambia_ubicacion<?php echo $opc; ?>(){
	var filtro_empresa_<?php echo $opc; ?> = <?php echo $empresa; ?>;
	var filtro_sucursal_<?php echo $opc; ?> = document.getElementById('filtro_sucursal_<?php echo $opc; ?>').value;

	sucursal_inventario_proceso=filtro_sucursal_<?php echo $opc; ?>; 

	Ext.get('recibidor_filtro_bodega_<?php echo $opc; ?>').load(
		{	
			url	: "inventario_unidades/bd/filtro_ubicacion_inventario_proceso.php",
			scripts:true,
			nocache:true,
			params:{
				opc		: 	"<?php echo $opc; ?>",
				filtro_empresa : filtro_empresa_<?php echo $opc; ?>,
				filtro_sucursal : filtro_sucursal_<?php echo $opc; ?>
			}
		}
	);
}
cambia_ubicacion<?php echo $opc; ?>();	



</script>	
