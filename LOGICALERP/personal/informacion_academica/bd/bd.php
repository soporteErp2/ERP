<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	switch ($opc) {
		case 'select_informacion_tipo_estudio':
			select_informacion_tipo_estudio($tipo,$id_empleado,$mysql,$link);
			break;
	}


	function select_informacion_tipo_estudio($tipo,$id_empleado,$mysql,$link){//$id_empleado,$link

		echo '<select class="myfieldObligatorio" name="Informacion_academica_tipo_estudio" id="Informacion_academica_tipo_estudio" style="width:160px" onchange="cambia()">';

			if($id_empleado > 0){

				$script = '';
				$sql    = "SELECT tipo_estudio FROM empleados_estudios WHERE id_empleado=$id_empleado AND activo=1";
				$query  = $mysql->query($sql,$link);

				//$tipo   = $mysql->result($query);

				if($mysql->num_rows($query)>0){

					echo'<option value="">Seleccione...</option>
						<option value="Primaria">Primaria</option>
						<option value="Secundaria">Secundaria</option>
						<option value="Universitario Pregrado">Universitario Pregrado</option>
						<option value="Universitario Diplomado">Universitario Diplomado</option>
						<option value="Universitario Especializacion">Universitario Especializacion</option>
						<option value="Universitario Maestrias">Universitario Maestrias</option>
						<option value="Otro">Otro</option>
						<script>document.getElementById("Informacion_academica_tipo_estudio").value="'.$tipo.'";'.$script.'</script>';
				}
				else{
					echo'<option value="">Seleccione...</option>
						<option value="Primaria">Primaria</option>
						<option value="Secundaria">Secundaria</option>
						<option value="Universitario Pregrado">Universitario Pregrado</option>
						<option value="Universitario Diplomado">Universitario Diplomado</option>
						<option value="Universitario Especializacion">Universitario Especializacion</option>
						<option value="Universitario Maestrias">Universitario Maestrias</option>
						<option value="Otro">Otro</option>';
				}
			}
		echo '</select>';
	}

?>