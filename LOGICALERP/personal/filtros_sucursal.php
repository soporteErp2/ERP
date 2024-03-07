<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$MSucursales = user_permisos(1);

	if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
	if($MSucursales == 'true'){ $filtroS = ""; }

	if($MSucursales == 'false' && $_SESSION["SUCURSALORIGEN"] != $_SESSION['SUCURSAL']){
		$filtroS = 'AND id = 0';
		echo '<Script>Ext.getCmp("btnAgregaEmpleado").disable()</script>';
	}

	$consulS = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa=$empresa AND activo=1 $filtroS",$link);
?>

    <select class="myfield" name="filtro_sucursal" id="filtro_sucursal" style="width:190px" onChange="cambia_filtro_sucursal_empleado()">
        <?php
			while($rowS=mysql_fetch_array($consulS)){
		?>
			<option value="<?php echo $rowS['id']?>" <?php if($rowS['id']==$_SESSION['SUCURSAL']){echo 'selected';}?>><?php echo $rowS['nombre']?></option>
        <?php
			}
        ?>
    </select>


<script>
// document.getElementById('panel_sucursal').style.borderLeft='border-left:1px solid #8DB2E3;';
var estilo=document.getElementById('panel_sucursal').getAttribute('style');

document.getElementById('panel_sucursal').setAttribute('style',estilo+'border-left:1px solid #8DB2E3;');
// console.log("in");
	cambia_filtro_sucursal_empleado();
	function cambia_filtro_sucursal_empleado() {
		var filtro_sucursal=document.getElementById('filtro_sucursal').value;
		Ext.get('contenedor_Empleados').load(
			{
				url		: 'empleados.php',
				scripts	:true,
				nocache	:true,
				params	:
					{
						// filtro 			: 	filtro,
						// filtro_empresa	:	filtro_empresa,
						filtro_sucursal	:	filtro_sucursal
					}
			}
		);
	}
</script>
