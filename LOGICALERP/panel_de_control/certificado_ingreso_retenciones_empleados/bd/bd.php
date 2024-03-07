<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	switch ($opc) {
		case 'agregar_concepto':
			agregar_concepto($id_concepto,$id_seccion,$id_fila,$id_empresa,$link);
			break;
		case 'eliminar_concepto':
			eliminar_concepto($id,$id_empresa,$link);
			break;
	}

	// AGREGAR LOS CONCEPTOS PARA EL CALCULO DE LA LIQUIDACION
	function agregar_concepto($id_concepto,$id_seccion,$id_fila,$id_empresa,$link){
		$sql="INSERT INTO certificado_ingreso_retenciones_empleados_conceptos (id_concepto,id_seccion,id_fila,id_empresa)
				VALUES ('$id_concepto','$id_seccion','$id_fila','$id_empresa')";
		$query=mysql_query($sql,$link);

		if ($query) {
			$id_insert = mysql_insert_id();
			echo '<script>
					actualiza_fila_ventana_busqueda('.$id_concepto.');
					Inserta_Div_certificado_ingreso_retenciones_empleados_conceptos('.$id_insert.');
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se agrego el Concepto, intentelo de nuevo");</script>';
		}
	}

	function eliminar_concepto($id,$id_empresa,$link){
		$sql="DELETE FROM certificado_ingreso_retenciones_empleados_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query=mysql_query($sql,$link);

		if ($query) {
			echo '<script>
					document.getElementById("item_certificado_ingreso_retenciones_empleados_conceptos_'.$id.'").parentNode.removeChild(document.getElementById("item_certificado_ingreso_retenciones_empleados_conceptos_'.$id.'"));
					//Elimina_Div_certificado_ingreso_retenciones_empleados_conceptos('.$id.');
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se elimino el registro, intentelo de nuevo");</script>';
		}
	}

?>

