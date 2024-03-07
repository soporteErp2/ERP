<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$MSucursales = user_permisos(4);
	$MEmpresas   = user_permisos(3);
	$empresa     = $_SESSION['EMPRESA'];


	if($MSucursales == 'true'  && $MEmpresas == 'true' ){$filtroS = '';}
	if($MSucursales == 'true'  && $MEmpresas == 'false'){$filtroS = '';}
	if($MSucursales == 'false' && $MEmpresas == 'false'){$filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}
	if($MSucursales == 'false' && $MEmpresas == 'true' ){$filtroS = 'AND id = '.$_SESSION['SUCURSAL'];}

	if($MSucursales == 'false' && $_SESSION["SUCURSALORIGEN"] != $_SESSION['SUCURSAL']){
		$filtroS = 'AND id = 0';
		echo '<Script>Ext.getCmp("btnAgregaEmpleado").disable()</script>';
	}

	$consulS = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = $empresa $filtroS",$link);
?>
<div style="float:left; margin: 15px 0 0 10px">
    <div style="float:left; width:50px; padding:3px 0 0 0">
        Sucursal
    </div>
    <div style="float:left; width:190px">
    <select class="myfield" name="filtro_sucursal" id="filtro_sucursal" style="width:190px" onChange="BusquedaEmpleados()">
        <?php while($rowS=mysql_fetch_array($consulS)){ ?>
			<option value="<?php echo $rowS['id']?>" <?php if($rowS['id']==$_SESSION['SUCURSAL']){echo 'selected';}?>><?php echo $rowS['nombre']?></option>
        <?php } ?>
    </select>
    </div>
</div>

<script> BusquedaEmpleados(); </script>
