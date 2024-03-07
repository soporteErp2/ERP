<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $observacion = str_replace("\n", "<br>", $observacion);
	if(!isset($fechai)){$fechai = $fechaf;}
	if(!isset($horai)){$horai = $horaf;}
	$fecha_actividad = $fechai.' '.$horai;

    if($opcion == 'insert'){
		if($tipo_crm=='objetivo'){
			$objetivo = $id_objetivo;
		}
		if($tipo_crm=='cliente'){
			$objetivo = 0;
		}

    	$mysql->query("INSERT INTO crm_objetivos_actividades
    								(
    									id_objetivo,
    									tipo,
    									tema,
    									id_asignado,
    									id_usuario,
    									fecha_actividad,
										fechai,
    									horai,
										fechaf,
										horaf,
    									observacion,
										id_cliente
    								)
    								VALUES
    								(
    									'$objetivo',
    									'$tipo',
    									'$tema',
    									'$id_asignado',
    									'$_SESSION[IDUSUARIO]',
    									'$fecha_actividad',
    									'$fechai',
    									'$horai',
										'$fechaf',
										'$horaf',
    									'$observacion',
										'$id_cliente'
    								)",$link);
		$id = $mysql->insert_id();
		echo $id.'{.}';

		//INSERTA LAS PERSONAS ADICIONALES QUE ESTAN ASIGNADAS
		if(isset($personas) && $personas != 'false'){
			$funcionarios = explode('{.}',$personas);
			for($i=0;$i<count($funcionarios);$i++){
				$mysql->query("INSERT INTO crm_objetivos_actividades_personas
													(
														id_actividad,
														id_asignado
													)VALUES(
														$id,
														$funcionarios[$i]
													)",$link);

			}
		}

		//ENVIARLE NOTIFICACION A CADA FUNCIONARIO ASIGNADO EN LA ACTIVIDAD
		include("../notificaciones/mail_actividad_funcionarios.php");
	}


   if($opcion == 'update'){

    	$mysql->query("UPDATE crm_objetivos_actividades SET
							tipo 			='$tipo',
							tema 			='$tema',
							id_asignado 	='$id_asignado',
							id_usuario 		='$_SESSION[IDUSUARIO]',
							fecha_actividad ='$fecha_actividad',
							fechai 			='$fechai',
							horai 			='$horai',
							fechaf 			='$fechaf',
							horaf 			='$horaf',
							observacion 	='$observacion'
					 WHERE id = $id",$link);

		$mysql->query("DELETE FROM crm_objetivos_actividades_personas WHERE id_actividad = $id ",$link);

		//INSERTA LAS PERSONAS ADICIONALES QUE ESTAN ASIGNADAS
		if(isset($personas) && $personas != 'false'){
			$funcionarios = explode('{.}',$personas);
			for($i=0;$i<count($funcionarios);$i++){
				$mysql->query("INSERT INTO crm_objetivos_actividades_personas
													(
														id_actividad,
														id_asignado
													)VALUES(
														$id,
														$funcionarios[$i]
													)",$link);

			}
		}

		//ENVIARLE NOTIFICACION A CADA FUNCIONARIO ASIGNADO EN LA ACTIVIDAD
		if($finaliza != 'true'){
			include("../notificaciones/mail_actividad_funcionarios.php");
		}

   }

   if($opcion == 'delete'){

		$mysql->query("UPDATE crm_objetivos_actividades SET activo = 0 WHERE id = $id",$link);//eliminar actividad
		$mysql->query("DELETE FROM crm_objetivos_actividades_personas WHERE id_actividad = $id ",$link);//eliminar funcionarios
		
		if($id_calendario != 0){
			$mysql->query("DELETE FROM calendario WHERE id = $id_calendario",$link);
            $mysql->query("DELETE FROM calendario_personas WHERE id_calendario = $id_calendario",$link);
            $mysql->query("DELETE FROM calendario_notificaciones WHERE id_calendario = $id_calendario",$link);
			$mysql->query("DELETE FROM calendario_notificaciones_personas WHERE id_calendario = $id_calendario",$link);
		}		

   }


?>