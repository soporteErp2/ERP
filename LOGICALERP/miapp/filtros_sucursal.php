<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$consulS = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = ".$_SESSION['EMPRESA']."",$link);
?>

    <select class="myfield" name="filtro_sucursal_dir" id="filtro_sucursal_dir" style="width:190px" onChange="">
			<option value="%" >TODAS</option>
        <?php
			while($rowS=mysql_fetch_array($consulS)){
		?>
			<option value="<?php echo $rowS['id']?>" <?php if($rowS['id']==$_SESSION['SUCURSAL']){echo 'selected';}?>><?php echo $rowS['nombre']?></option>
        <?php
			}
        ?>
    </select>
