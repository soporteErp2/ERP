<?php 
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");    

    $fecha_actividad = $fecha.' '.$hora;
    $observacion = str_replace("\n", " ", $observacion);

    if($alarma != 0){
        $fecha_al       = date("Y-m-d H:i:s",strtotime($fecha_actividad));
        $fe             = split(" ",$fecha_al);
        $fech           = split("-",$fe[0]);
        $hor            = split(":",$fe[1]);
        $fecha_alerta   = date('Y-m-d H:i:s', mktime(date($hor[0]),date($hor[1]),date($hor[2])-$alarma,date($fech[1]),date($fech[2]),date($fech[0])));
    }

    if($opcion == 'insert'){
    	mysql_query("INSERT INTO crm_objetivos
    								(
    									id_cliente,
    									tipo,
    									objetivo,
    									id_usuario,
    									vencimiento,
    									observacion,
                                        valor,
                                        prioridad,
                                        id_tipo,
                                        id_linea,
                                        id_estado,
                                        probabilidad_exito
    								) 
    								VALUES 
    								(
    									'$id_cliente',
    									2,
    									'$tema',
    									'$_SESSION[IDUSUARIO]',
    									'$fecha_actividad',
    									'$observacion',
                                        '$valorObj',
                                        '$prioridad',
                                        '$tipo_proyecto',
                                        '$linea_negocio',
                                        '$estado_proyecto',
                                        '$probabilidad'
    								)",$link);
        $id = mysql_insert_id();

    }

    

   if($opcion == 'update'){        

    	mysql_query("UPDATE crm_objetivos SET 
                        objetivo           = '$tema',						
                        id_usuario         = '$_SESSION[IDUSUARIO]',
                        vencimiento        = '$fecha_actividad',
                        observacion        = '$observacion',
                        valor              = '$valorObj',
                        prioridad          = '$prioridad',
                        id_linea           = '$linea_negocio',
                        id_tipo            = '$tipo_proyecto',
                        id_estado          = '$estado_proyecto',
                        probabilidad_exito = '$probabilidad'
					 WHERE id = $id_objetivo",$link);        

        $id = $id_objetivo;

    }

    echo $id.'{.}';   
?>