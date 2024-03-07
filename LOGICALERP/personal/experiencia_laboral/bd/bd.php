<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	switch ($opc) {
		case 'select_informacion_familiar':
			select_informacion_familiar($tipo_identificacion,$link);
			break;
		case 'select_empresa':
			select_empresa($empresa,$id_empleado,$link);
			break;

	}


	function select_informacion_familiar($tipo_identificacion,$link){

		$sql   = "SELECT id,detalle FROM tipo_documento WHERE activo=1";
		$query = mysql_query($sql,$link);
		$body  = '';
		while($row = mysql_fetch_assoc($query)){
			$body .= '<option value="'.$row['id'].'">'.$row['detalle'].'</option>';
		}

		echo '<select onchange="" style="width:160px" id="Informacion_familiar_id_tipo_identificacion" name="Informacion_familiar_id_tipo_identificacion" class="myfield">
				<option value="">Seleccione...</option>
				'.$body.'
			</select>';
		if($tipo_identificacion>0)echo "<script>document.getElementById('Informacion_familiar_id_tipo_identificacion').value=$tipo_identificacion</script>";
	}


	function select_empresa($empresa,$id_empleado,$link){//$id_empleado,$link

		echo '<select onchange="" style="width:160px" id="Experiencia_laboral_empresa" name="Experiencia_laboral_empresa" class="myfield">';

			if($id_empleado > 0){

				$sql   = "SELECT empresa FROM empleados_experiencia_laboral WHERE id_empleado=$id_empleado AND activo=1";
				$query = mysql_query($sql,$link);

				if(mysql_num_rows($query)>0){//si hay filas

					$tiene_actual  ="No";
					// $tiene_anterior1 ="No";
					// $tiene_anterior2 ="No";
					// $tiene_anterior3 ="No";
					// $tiene_anterior4 ="No";
					$body         = "";
					while($row = mysql_fetch_assoc($query)){

						if($row['empresa']=='Actual'){
							$tiene_actual="Si";
						}


						// if($row['empresa']=='Anterior 1'){
						// 	$tiene_anterior1="Si";
						// }

						// if($row['empresa']=='Anterior 2'){
						// 	$tiene_anterior2="Si";
						// }
						// if($row['empresa']=='Anterior 3'){
						// 	$tiene_anterior3="Si";
						// }
						// if($row['empresa']=='Anterior 4'){
						// 	$tiene_anterior4="Si";
						// }

					}//while

					if($tiene_actual=="No" || $empresa=='Actual'){
						$body .= '<option value="Actual">Actual</option>';
					}


					// if($tiene_anterior1=="No" || $empresa=='Anterior 1'){
					// 	$body .= '<option value="Anterior 1">Anterior 1</option>';
					// }
					// if($tiene_anterior2=="No" || $empresa=='Anterior 2'){
					// 	$body .= '<option value="Anterior 2">Anterior 2</option>';
					// }
					// if($tiene_anterior3=="No" || $empresa=='Anterior 3'){
					// 	$body .= '<option value="Anterior 3">Anterior 3</option>';
					// }
					// if($tiene_anterior4=="No" || $empresa=='Anterior 4'){
					// 	$body .= '<option value="Anterior 4">Anterior 4</option>';
					// }

					echo '<option value="">Seleccione...</option>'.$body.'<option value="Anterior">Anterior</option>';
					echo '<script>document.getElementById("'.Experiencia_laboral_empresa.'").value="'.$empresa.'";</script>';

				}else{
					//echo '<option value="">Seleccione...</option><option value="Actual">Actual</option><option value="Anterior 1">Anterior 1</option><option value="Anterior 2">Anterior 2</option><option value="Anterior 3">Anterior 3</option><option value="Anterior 4">Anterior 4</option></select>';
					echo '<option value="">Seleccione...</option><option value="Actual">Actual</option><option value="Anterior">Anterior</option></select>';
				}

			}else{

				//echo '<option value="">Seleccione...</option><option value="Actual">Actual</option><option value="Anterior 1">Anterior 1</option><option value="Anterior 2">Anterior 2</option><option value="Anterior 3">Anterior 3</option><option value="Anterior 4">Anterior 4</option></select>';
				echo '<option value="">Seleccione...</option><option value="Actual">Actual</option><option value="Anterior">Anterior</option></select>';
			}

		echo '</select>';

	}

?>