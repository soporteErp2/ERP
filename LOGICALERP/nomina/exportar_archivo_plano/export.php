<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("arraysResolucion.php");
    foreach ($arrayEncabezado as $campo => $arrayResul) {
        $filecontent.=str_pad($arrayResul['value'],$arrayResul['long'], $arrayResul['rellena_espacios'] ,STR_PAD_LEFT);
    }
    $fileName="archivo_plano_pila.txt";
    header("Content-disposition: attachment; filename=$fileName");
    header("Content-Type: application/force-download");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".strlen($filecontent));
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $filecontent;
?>