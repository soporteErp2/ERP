<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	switch ($opc) {
		// FUNCIONES PARA SECCIONES
		case 'agregarSeccion':
			agregarSeccion($nombre,$id_padre,$id_empresa,$mysql);
			break;
		case 'actualizarSeccion':
			actualizarSeccion($id_seccion,$nombre,$id_padre,$id_empresa,$mysql);
			break;
		case 'eliminarSeccion':
			eliminarSeccion($id_seccion,$id_formato,$id_empresa,$mysql);
			break;

	}

	// FUNCIONES PARA LAS SECCIONES
	function agregarSeccion($nombre,$id_padre,$id_empresa,$mysql){
		$sql="INSERT INTO ventas_pos_secciones
				(
					nombre,
					id_padre,
					id_empresa
				)
				VALUES
				(
					'$nombre',
					'$id_padre',
					$id_empresa
				)
				";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_secciones_pos/secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			// $id_seccion = $mysql->insert_id($mysql->link);
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se inserto la Seccion' });
				</script>";
		}
	}

	function actualizarSeccion($id_seccion,$nombre,$id_padre,$id_empresa,$mysql){
		$sql="UPDATE ventas_pos_secciones
				SET
					nombre   = '$nombre',
					id_padre = '$id_padre'
				WHERE activo   = 1
				AND id_empresa = $id_empresa
				AND id         = $id_seccion";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_secciones_pos/secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se actualizo la Seccion' });
				</script>";
		}
	}

	function eliminarSeccion($id_seccion,$id_formato,$id_empresa,$mysql){
		$sql="UPDATE informes_niif_formatos_secciones SET activo=0 WHERE  id_empresa = $id_empresa AND id = $id_seccion AND id_formato = $id_formato";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
							codigo     : '$codigo',
							nombre     : '$nombre',
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se actualizo la Seccion' });
				</script>";
		}
	}

?>

