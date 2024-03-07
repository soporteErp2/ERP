<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'ventana_centro_costo':
			ventana_centro_costo($id_empresa, $id, $link);
			break;

		case 'save_update_centro_costo':
			save_update_centro_costo($id_empresa, $id, $codigo, $nombre, $link);
			break;

		case 'eliminar_centro_costo':
			eliminar_centro_costo($id_empresa, $id,$link);
			break;
	}

	function ventana_centro_costo($id_empresa, $id, $link){
		$nombre = "";
		$codigo = "";
		if($id > 0){
			$sql    = "SELECT nombre,codigo FROM centro_costos WHERE id='$id' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$query  = mysql_query($sql,$link);
			$nombre = mysql_result($query, 0, 'nombre');
			$codigo = mysql_result($query, 0, 'codigo');
		}
		echo'<div style="overflow:hidden; margin:15px 5px;">
				<div id="render_centro_costo" style="overflow:hidden; width:20px; height:20px; position:fixed; float:left;"></div>
				<div style="width:100%;height:25px; overflow:hidden;">
					<div style="float:left; width:20%">Codigo</div>
					<div style="float:left; width:80%"><input type="text" id="codigo_cuenta_costo" onkeyup="validateNumberInt(this);" value="'.$codigo.'" class="myfield" style="width:99%;"/></div>
				</div>
				<div style="width:100%; height:25px; overflow:hidden; margin-top:7px;">
					<div style="float:left; width:20%">Nombre</div>
					<div style="float:left; width:80%"><input type="text" id="nombre_cuenta_costo" value="'.$nombre.'" class="myfield" style="width:99%;"/></div>
				</div>
			<div>';
	}

	function save_update_centro_costo($id_empresa, $id, $codigo, $nombre, $link){

		$where = $id > 0? "AND id<>'$id'": "";

		$sqlValidate   = "SELECT COUNT(id) AS cont FROM centro_costos WHERE codigo='$codigo' AND activo=1 AND id_empresa='$id_empresa' $where LIMIT 0,1";
		$queryValidate = mysql_query($sqlValidate, $link);
		$cont = mysql_result($queryValidate, 0, 'cont');

		if($cont > 0){ echo'<script>alert("Aviso.\nEl codigo '.$codigo.' centro de costo ha sido ingresado!")</script>'; exit; }

		if(strlen($codigo) > 2){
			$cifras = strlen($codigo) - 2;

			$sqlValidate   = "SELECT COUNT(id) AS cont FROM centro_costos WHERE codigo = left('$codigo',$cifras) $where AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$queryValidate = mysql_query($sqlValidate, $link);
			$cont = mysql_result($queryValidate, 0, 'cont');

			if($cont == 0){ echo'<script>alert("Aviso.\nEl codigo '.$codigo.' centro de costo no pertenece a ninguna clasificacion de '.$cifras.' cifras!")</script>'; exit; }
		}

		if($id > 0){
			$sqlUpdate = "UPDATE centro_costos SET codigo='$codigo', nombre='$nombre' WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
			$querySql  = mysql_query($sqlUpdate,$link);
			echo'<script>Actualiza_Div_centroCostos('.$id.');</script>';
		}
		else{
			$sqlUpdate = "INSERT INTO centro_costos (codigo, nombre, id_empresa) VALUES ('$codigo', '$nombre', '$id_empresa')";
			$querySql  = mysql_query($sqlUpdate,$link);

			$sqlIdCcos = "SELECT LAST_INSERT_ID()";
			$idCcos  = mysql_result(mysql_query($sqlIdCcos,$link),0,0);

			echo'<script>Inserta_Div_centroCostos('.$idCcos.');</script>';
		}

		if(!$querySql){ echo'<script>alert("Aviso.\nNo se ha podido establecer conexion con la base de datos!")</script>'; exit; }
		echo'<script>Win_Ventana_centro_costo.close();</script>';
	}

	function eliminar_centro_costo($id_empresa, $id,$link){
		//CONSULTAR EL CODIGO DEL CENTRO DE COSTOS
		$sql    = "SELECT codigo FROM centro_costos WHERE id='$id' AND id_empresa='$id_empresa'";
		$query  = mysql_query($sql,$link);
		$codigo = mysql_result($query,0,'codigo');

		//ELIMINAR LOS CENTROS DE COSTOS QUE INICIEN CON EL CODIGO CONSULTADO
		$sql   = "UPDATE centro_costos SET activo=0 WHERE codigo LIKE '$codigo%' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		echo"<script>
				MyBusquedacentroCostos();
				Win_Ventana_centro_costo.close();
			</script>";
	}

?>