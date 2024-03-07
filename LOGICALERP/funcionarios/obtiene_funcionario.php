<?php 
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");
    echo mysql_result(mysql_query("SELECT nombre FROM empleados WHERE id = $id",$link),0,'nombre');
?>