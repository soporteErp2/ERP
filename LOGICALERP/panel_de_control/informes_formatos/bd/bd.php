<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	switch ($opc) {
		case 'cargar_formatos_basicos':
			cargar_formatos_basicos($id_empresa,$mysql);
			break;
		// FUNCIONES PARA SECCIONES
		case 'agregarSeccion':
			agregarSeccion($seccion_padre,$orden,$nombre,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql);
			break;
		case 'actualizarSeccion':
			actualizarSeccion($id_seccion,$seccion_padre,$orden,$nombre,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql);
			break;
		case 'eliminarSeccion':
			eliminarSeccion($id_seccion,$id_formato,$id_empresa,$mysql);
			break;

	}

	// FUNCIONES PARA LAS SECCIONES
	function agregarSeccion($seccion_padre,$orden,$nombre,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql){
		$sql="INSERT INTO informes_formatos_secciones
				(
					id_formato,
					codigo_seccion,
					codigo_seccion_padre,
					orden,
					nombre,
					formula,
					totalizado,
					label_totalizado,
					formula_totalizado,
					id_empresa
				)
				VALUES
				(
					'$id_formato',
					'$codigo',
					'$seccion_padre',
					'$orden',
					'$nombre',
					'$formula',
					'$totalizado',
					'$label_totalizado',
					'$formula_totalizado',
					'$id_empresa'
				)
				";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'informes_formatos/formatos_secciones_dinamico.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
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

	function actualizarSeccion($id_seccion,$seccion_padre,$orden,$nombre,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql){
		$sql="UPDATE informes_formatos_secciones
				SET
					codigo_seccion_padre = '$seccion_padre',
					orden                = '$orden',
					nombre               = '$nombre',
					formula              = '$formula',
					totalizado           = '$totalizado',
					label_totalizado     = '$label_totalizado',
					formula_totalizado   = '$formula_totalizado '
				WHERE activo   = 1
				AND id_empresa = $id_empresa
				AND id         = $id_seccion
				AND id_formato = $id_formato";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'informes_formatos/formatos_secciones_dinamico.php',
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

	function eliminarSeccion($id_seccion,$id_formato,$id_empresa,$mysql){
		$sql="UPDATE informes_formatos_secciones SET activo=0 WHERE  id_empresa = $id_empresa AND id = $id_seccion AND id_formato = $id_formato";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'informes_formatos/formatos_secciones_dinamico.php',
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

