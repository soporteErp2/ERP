<?php
    $id_empresa     = $_SESSION['EMPRESA'];
    $id_sucursal    = $_SESSION['SUCURSAL'];
    $id_usuario     = $_SESSION['IDUSUARIO'];
    $cc_usuario     = $_SESSION['CEDULAFUNCIONARIO'];
    $nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

    $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    // $contArray  = COUNT($arrayExcel);
    // $contCol    = COUNT($arrayExcel[0]);

    // echo  $arrayExcel[1][0];
    // print_r($arrayExcel);
    // echo $uploadDirectory.$filename.'.'.$ext;

    // RECORRER LOS ITEMS DEL EXCEL PARA CREAR UN ARRAY
    foreach ($arrayExcel as $fila => $arrayCol) {
        // SI ES LA FILA DE LOS TITULOS, ENTONCES NO RECORRER
        if ($fila==0) { continue; }
        $codigo   = $arrayExcel[$fila][0];
        $nombre   = $arrayExcel[$fila][1];
        $cantidad = $arrayExcel[$fila][2];
        $costo    = $arrayExcel[$fila][3];

        if ($cantidad < 0 || $costo < 0) {
            $errorLoadFile = "Error\nItems con cantidad o costo negativo!\n$codigo - $nombre";
            return;
            break;
        }

        $arrayItems[$codigo] = array(
                                    'codigo'   => $codigo,
                                    'nombre'   => $nombre,
                                    'cantidad' => $cantidad,
                                    'costo'    => $costo,
                                    );

    }

    $whereItems = "codigo='".implode("' OR codigo='", array_keys($arrayItems))."'";
    $sql = '';
    if($AjusteMensual=='NO'){
        $sql="SELECT id_item,codigo,nombre_equipo,costos,cantidad FROM inventario_totales WHERE activo=1 AND id_empresa=$id_empresa AND id_ubicacion=$id_bodega AND ($whereItems) ";
    }else{
        $sqlFecha = "SELECT fecha_documento FROM inventario_ajuste WHERE id=$id_documento LIMIT 1";
        $fechaDoc = $mysql->result(($mysql->query($sqlFecha,$mysql->link)),0,'fecha_documento');
        //sumarle un dos a la fecha (la base de datos guarda la fecha el sergundo segundo día del mes)
        $fecha = new DateTime($fechaDoc);
        $fecha->modify('+2 day');
        $fechaconsul = $fecha->format('Y-m-d');
        $sql="SELECT id_item,codigo,nombre as nombre_equipo,costo as costos,cantidad FROM inventario_totales_log_mensual WHERE id_empresa=$id_empresa AND id_bodega=$id_bodega AND fecha = '$fechaconsul' AND ($whereItems) ";
    }
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $codigo = $row['codigo'];
        $arrayItemsBd[$codigo] = array(
                                        'id'       => $row['id_item'],
                                        'nombre'   => $row['nombre_equipo'],
                                        'costos'   => $row['costos'],
                                        'cantidad' => $row['cantidad'],
                                        );
    }

    if ( !isset($arrayItemsBd)) {
        $errorLoadFile = "Error\nNingun item del excel Existe en el sistema";
        return;
    }

    $diffItems = array_diff_key($arrayItems, $arrayItemsBd);

    // VALIDACION DE QUE TODOS LOS ITEMS CARGADOS EN EL EXCEL FUNCIONEN
    if(COUNT($diffItems)){
        $msj = implode("<br>*", array_keys($diffItems));
        $errorLoadFile = "Aviso, Los siguientes items no existen\n".$msj;
        return;
    }

    // ELIMINAR EL CUERPO DEL DOCUMENTO
    $sql="DELETE FROM inventario_ajuste_detalle WHERE activo=1 AND id_ajuste_inventario=$id_documento";
    $query=$mysql->query($sql,$mysql->link);

    // $sql = "INSERT INTO $tablaInventario(
    //                             $idTablaPrincipal,
    //                             id_inventario,
    //                             cantidad_inventario,
    //                             cantidad,
    //                             costo_unitario
    //                             )
    //                     VALUES( '$id',
    //                             '$idInventario',
    //                             '$cantInvArticulo',
    //                             '$cantidad',
    //                             '$costoArticulo')";

    // CREAR CADENA PARA INSERTAR DE NUEVO EL CUERPO DEL DOCUMENTO
    foreach ($arrayItems as $codigo => $arrayResul) {
        $idInventario    = $arrayItemsBd[$codigo]['id'];
        $cantInvArticulo = $arrayItemsBd[$codigo]['cantidad'];

        $valueInsert .= "( '$id_documento',
                            '$idInventario',
                            '$cantInvArticulo',
                            '$arrayResul[cantidad]',
                            '$arrayResul[costo]'
                            ),";
    }

    $valueInsert = substr($valueInsert,0,-1);

    $sql="INSERT INTO inventario_ajuste_detalle(
                                id_ajuste_inventario,
                                id_inventario,
                                cantidad_inventario,
                                cantidad,
                                costo_unitario
                                )
                        VALUES $valueInsert";
    $query=$mysql->query($sql,$mysql->link);

    // echo json_encode($arrayItemsBd);

    // echo $valueInsert;

    // print_r($arrayItems);
    // return array('error'=> 'Aviso, Los siguientes items no existen \n'.$msj, 'debug'=> "$debugError");
    // return;

?>
