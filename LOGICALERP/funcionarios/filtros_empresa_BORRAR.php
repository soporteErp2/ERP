<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	
	$MSucursales = user_permisos(4);
	$MEmpresas   = user_permisos(3);
	
	if($MSucursales == 'true' && $MEmpresas == 'true')	{$filtroE = '';}
	if($MSucursales == 'true' && $MEmpresas == 'false')	{$filtroE = 'WHERE id = '.$_SESSION['EMPRESA'];}
	if($MSucursales == 'false' && $MEmpresas == 'false'){$filtroE = 'WHERE id = '.$_SESSION['EMPRESA'];}
	if($MSucursales == 'false' && $MEmpresas == 'true')	{$filtroE = '';}
	
	if($MEmpresas == 'false' && $_SESSION["EMPRESAORIGEN"] != $_SESSION['EMPRESA']){
		$filtroE = 'WHERE id = 0';
		echo '<script>Ext.getCmp("btnAgregaEmpleado").disable()</script>';
	}
	
	$consulE = mysql_query("SELECT id,nombre FROM empresas $filtroE",$link);

?>
<div style="float:left; margin: 5px 0 0 10px">
    <div style="float:left; width:50px; padding:3px 0 0 0">
        Empresa
    </div>
    <div style="float:left; width:190px">
    <select class="myfield" name="filtro_empresa" id="filtro_empresa" style="width:190px" onChange="CambiaEmpresa();">
        <?php
			while($rowE=mysql_fetch_array($consulE)){
		?>
			<option value="<?php echo $rowE['id']?>" <?php if($rowE['id']==$_SESSION['EMPRESA']){echo 'selected';}?>><?php echo $rowE['nombre']?></option>
        <?php
			}
        ?>
    </select>
    </div>
</div> 
<div style="float:left; margin: 5px 0 0 10px">
    <div style="float:left; width:50px; padding:3px 0 0 0">
        Sucursal
    </div>
    <div id="recibidor_filtro_empresa" style="float:left; width:190px">
    <!--<select class="myfield" name="filtro_sucursal" id="filtro_sucursal" style="width:190px">
        <?php
			while($rowS=mysql_fetch_array($consulS)){
		?>
			<option value="<?php echo $rowS['id']?>" <?php if($rowS['id']==$_SESSION['SUCURSAL']){echo 'selected';}?>><?php echo $rowS['nombre']?></option>
        <?php
			}
        ?>
    </select>-->
    </div>
</div> 

<script>

	function CambiaEmpresa(){
		var empresa = document.getElementById('filtro_empresa').value;
		//alert(empresa);
		Ext.get('recibidor_filtro_empresa').load(
			{
				url		:	'../funcionarios/filtros_sucursal.php',
				scripts	:	true,
				nocache	:	true,
				params	:
					{
						empresa	:	empresa	
					}
			}
		);
	}
	
	CambiaEmpresa();
	
</script>
