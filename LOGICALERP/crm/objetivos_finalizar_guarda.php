<?php 
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    $accion = str_replace("\n", " ", $accion);
	mysql_query("UPDATE crm_objetivos SET
					estado = 1,
					observacion_finaliza = '$accion',
					id_usuario_finaliza	= '$_SESSION[IDUSUARIO]',
					fecha_finaliza	= NOW()
				 WHERE id = $id
				",$link);


    echo $id.'{.}';   
?>