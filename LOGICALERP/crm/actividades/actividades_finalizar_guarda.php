<?php 
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $accion = str_replace("\n", "<br>", $accion);
	$mysql->query("UPDATE crm_objetivos_actividades SET
					estado = 1,
					observacion_finaliza = '$accion',
					id_usuario_finaliza	= '$_SESSION[IDUSUARIO]',
					fecha_finaliza	= NOW()
				 WHERE id = $id_actividad
				",$link);

	$finalizo = 'true';

	include("../notificaciones/mail_actividad_funcionarios.php");


    echo $id_actividad.'{.}';   
?>