<?php

include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");
include("../config_var_global.php");
include("ClassFacturaJSON.php");

$prueba = new ClassFacturaJSON($mysql);
$prueba->prueba();
?>
