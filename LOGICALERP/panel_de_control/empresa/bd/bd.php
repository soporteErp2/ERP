<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];

	switch($opc){
		case 'buscar_ciudad':
			buscar_ciudad($id_pais,$id_departamento,$id_ciudad,$id_empresa,$mysql);
			break;

		case 'actualiza_info_empresa':
			actualiza_info_empresa($nombre_empresa,$razon_social,$regimen,$actividad_economica,$id_departamento,$id_ciudad,$direccion,$telefono,$celular,$id_empresa,$mysql,$email,$tipo_persona_codigo,$tipo_persona_nombre);
			break;
	}

	function buscar_ciudad($id_pais,$id_departamento,$id_ciudad,$id_empresa,$mysql){
		$sql = "SELECT id,ciudad FROM ubicacion_ciudad WHERE activo = 1 AND id_departamento = $id_departamento AND id_pais = $id_pais";
		$query = $mysql->query($sql,$mysql->link);

		while($row = $mysql->fetch_array($query)){
			$selected = ($id_ciudad == $row['id'])? 'selected' : '' ;
			$ciudades .= '<option value="'.$row['id'].'" '.$selected.' >'.$row['ciudad'].'</option>';
		}

		echo "<select style='width:190px;' data-requiere='true' id='id_ciudad' >$ciudades</select>";
	}

	function actualiza_info_empresa($nombre_empresa,$razon_social,$regimen,$actividad_economica,$id_departamento,$id_ciudad,$direccion,$telefono,$celular,$id_empresa,$mysql,$email,$tipo_persona_codigo,$tipo_persona_nombre){
		$sql = "UPDATE
							empresas
						SET
							nombre      				= '$nombre_empresa',
							razon_social        = '$razon_social',
							tipo_regimen        = '$regimen',
							actividad_economica = '$actividad_economica',
							id_departamento     = '$id_departamento',
							id_ciudad           = '$id_ciudad',
							direccion           = '$direccion',
							telefono            = '$telefono',
							celular             = '$celular',
							email 							= '$email',
							tipo_persona_codigo = '$tipo_persona_codigo',
							tipo_persona_nombre = '$tipo_persona_nombre'
						WHERE
							activo = 1
						AND
							id = $id_empresa";

		$query = $mysql->query($sql,$mysql->link);

		if($query){
			echo "<script>
							MyLoading2('off');
							Win_Panel_Global.close();
						</script>";
		}
		else{
			echo "<script>
							MyLoading2('off',{icono:'fail',texto:'Error al actualizar la informacion'});
						</script>";
		}
	}
 ?>
