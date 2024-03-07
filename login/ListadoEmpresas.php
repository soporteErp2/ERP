<?php
	$return_conectar_global = true;
    include("../configuracion/conectar_global.php");
    $sql = "SELECT id,nit,nombre FROM host WHERE activo = 1 AND id != 0 ORDER BY nombre";
    $query = mysql_query($sql,$acceso);
	while($row = mysql_fetch_array($query)){
		echo '<div class="ListadoEmpresas" onclick="SelectEmpre(\''.$row['nit'].'\')" title="'.$row['id'].'">'.$row['nombre'].'</div>';
	}

	mysql_close($acceso);
?>
<style>
	.ListadoEmpresas{
		width     : calc(100% - 20px);
		margin    : 10px;
		font-size : 14px;
		cursor    : pointer;
	}
</style>

<script>
	function SelectEmpre(id){
		document.getElementById('empresa').value = id;
		consulta_empresa()
		//document.getElementById('sucursal').focus();
		VBuscaEmpresa.close();
	}
</script>
