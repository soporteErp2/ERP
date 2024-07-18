<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$consulE = mysql_query("SELECT id,nombre FROM empresas $filtroE",$link);
?>
<div id="filtros_directorio" style="float:left; height:70px" >
	<div style="float:left; width:280px; ">
		<div style="float:left; margin: 3px 0 0 10px;display:none">
			<div style="float:left; width:70px; padding:3px 0 0 0">
				Empresa
			</div>
			<div style="float:left; width:190px">
				<select class="myfield" name="filtro_empresa_dir" id="filtro_empresa_dir" style="width:190px" onChange="CambiaEmpresaDir();">
						<option value="%" >TODAS</option>
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
		<div style="float:left; margin: 15px 0 0 10px">
			<div style="float:left; width:70px; padding:3px 0 0 0">
				Sucursal
			</div>
			<div id="recibidor_filtro_empresa_dir" style="float:left; width:190px">
				<!--<select class="myfield" name="filtro_sucursal_dir" id="filtro_sucursal_dir" style="width:190px">
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
		<!-- generamos un espacion en blanco para que mejore el diseño -->
		<div style="float:left; margin: 3px 0 0 10px">
			<div style="float:left; width:70px; padding:3px 0 0 0">

			</div>
		</div>


		<div style="float:left; margin: 3px 0 0 10px">
			<div style="float:left; width:70px; padding:3px 0 0 0">
				Busqueda
			</div>
			<div style="float:left; width:190px">
				<input style="float:left; width:190px; height:20px" type="text" id="busca_funcionario" value="" onkeyup="buscaDirectorio(event,this)" />
			</div>
		</div>
	</div>

</div>

<script>

	CambiaEmpresaDir();



</script>