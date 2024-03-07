<?php 
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $accion = str_replace("\n", " ", $accion);
	mysql_query("INSERT INTO crm_objetivos_actividades_acciones 
								(
									id_objetivo,
									id_actividad,
									id_usuario,
									accion
								) 
								VALUES 
								(
									'$id_objetivo',
									'$id_actividad',
									'$_SESSION[IDUSUARIO]',
									'$accion'
								)",$link);

    $id = mysql_insert_id();

    echo $id.'{.}';   
?>