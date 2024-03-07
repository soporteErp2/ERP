<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    $sql      = "SELECT dinamico FROM informes_formatos WHERE activo=1 AND id=$id_formato ";
    $query    = $mysql->query($sql);
    $dinamico = $mysql->result($query,0,'dinamico');

    switch ($dinamico) {
        case 'Si':
            include 'ClassDinamicReport.php';
            break;
        default:
            include 'report_Result.php';
            break;
    }

?>