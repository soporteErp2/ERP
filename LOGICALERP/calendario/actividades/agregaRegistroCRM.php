<?php 
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $fecha_actividad = $fechai.' '.$horai;
    $observacion = str_replace("\n", "<br>", $observacion);
    
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
		$mysql->query("UPDATE calendario SET id_objetivo_crm = $objetivo, id_actividad_crm = $id  WHERE id = $id_calendario",$link);
	
		echo $id.'{.}';  

		if($id_asignado != $_SESSION['IDUSUARIO']){
			if(file_exists("../../crm/notificaciones/mail_actividad_funcionarios.php")){
				echo 'existe!!!';
			}
			else{
				echo 'no existe!!!';
			}
			include("../../crm/notificaciones/mail_actividad_funcionarios.php");
		}
	}

	if($opcion == 'update'){
	
		$mysql->query("UPDATE crm_objetivos_actividades SET 
						tipo			= 	'$tipo',
						tema 			= '$tema',
						id_asignado		= '$id_asignado',
						id_usuario		= '$_SESSION[IDUSUARIO]',
						fecha_actividad = '$fecha_actividad',
						fechai			= '$fechai',
						horai			= '$horai',
						fechaf			= '$fechaf',
						horaf			= '$horaf',	
						observacion 	= '$observacion'
					 WHERE id_objetivo = $id_objetivo AND id = $id_actividad_crm",$link);
		
	}

     
?>