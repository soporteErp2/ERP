<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];

	switch ($opc) {
		case 'buscar_provincia':
			buscar_provincia($id_pais,$id_region,$id_provincia,$id_empresa,$mysql);
			break;

		case 'buscar_comuna':
			buscar_comuna($id_pais,$id_provincia,$id_comuna,$id_empresa,$mysql);
			break;

		case 'actualiza_info_empresa':
			actualiza_info_empresa($nombre_empresa,$razon_social,$regimen,$id_region,$id_provincia,$id_comuna,$direccion,$telefono,$celular,$email,$id_empresa,$mysql);
			break;

	}

	function buscar_provincia($id_pais,$id_region,$id_provincia,$id_empresa,$mysql){
		$sql = "SELECT id,ciudad FROM ubicacion_ciudad WHERE activo = 1 AND id_departamento = $id_region AND id_pais=$id_pais";
		$query = $mysql->query($sql,$mysql->link);

		while ($row = $mysql->fetch_array($query)) {
			$selected = ($id_provincia == $row['id'])? 'selected' : '' ;
			$provincias .= '<option value="'.$row['id'].'" '.$selected.' >'.$row['ciudad'].'</option>';
		}

		echo "<select style='width:190px;' data-requiere='true' id='id_provincia' onchange='buscar_comuna(this.value)' >
				<option value=''>Seleccione</option>
				$provincias
			</select>";
	}

	function buscar_comuna($id_pais,$id_provincia,$id_comuna,$id_empresa,$mysql){
		$sql = "SELECT id,comuna FROM ubicacion_comuna WHERE activo = 1 AND id_ciudad=$id_provincia AND id_pais=$id_pais";
		$query = $mysql->query($sql,$mysql->link);

		while ($row = $mysql->fetch_array($query)) {
			$selected = ($id_comuna == $row['id'])? 'selected' : '' ;
			$comunas .= '<option value="'.$row['id'].'" '.$selected.' >'.$row['comuna'].'</option>';
		}
		echo "<select style='width:190px;' data-requiere='true' id='id_comuna' >
				<option value=''>Seleccione</option>
				$comunas
			</select>";
	}

	function actualiza_info_empresa($nombre_empresa,$razon_social,$regimen,$id_region,$id_provincia,$id_comuna,$direccion,$telefono,$celular,$email,$id_empresa,$mysql){

		$sql = "UPDATE
							empresas
						SET
							nombre          = '$nombre_empresa',
							razon_social    = '$razon_social',
							tipo_regimen    = '$regimen',
							id_departamento = '$id_region',
							id_ciudad       = '$id_provincia',
							id_comuna       = '$id_comuna',
							direccion       = '$direccion',
							telefono        = '$telefono',
							celular         = '$celular',
							email           = '$email'
						WHERE
							activo = 1
						AND
							id = $id_empresa";

		$query=$mysql->query($sql,$mysql->link);

		if ($query) {
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
