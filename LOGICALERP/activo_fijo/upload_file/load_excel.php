<?php

    $id_empresa     = $_SESSION['EMPRESA'];
    $id_usuario     = $_SESSION['IDUSUARIO'];
    $cc_usuario     = $_SESSION['CEDULAFUNCIONARIO'];
    $nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

    $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    $contArray  = COUNT($arrayExcel);
    $contCol    = COUNT($arrayExcel[0]);

    // $errorLoadFile = 'true';
    // $resulProcess = "error pass";
    // exit;

    // return array('success'=>true, 'resulProcess' => "$resulProcess - $body" );

    // CREAR ARRAY CON LOS VALORES DE LOS CONCEPTOS RELACIONADOS A LAS CUENTAS
    // RECORRER LAS FILAS
    for ($i=2; $i <=$contArray; $i++) {
        // RECORRER LAS COLUMNAS
        for ($j=0; $j <= $contCol; $j++) {
            $valor_celda = $arrayExcel[$i][$j];
            if ($valor_celda=='') { continue; }
            // CODIGOS DE GRUPOS
            if ($j ==0) { $arrayGrupos[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }
            // // CODIGOS DE SUBGRUPO
            if ($j ==1) { $arraySubGrupos[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }
            // // CODIGO CENTRO COSTOS
            if ($j ==2) { $arrayCentroCostos[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }

            // CODIGO ACTIVOS FIJOS
            if ($j ==4) { $arrayActivosFijos[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }
            if ($j ==5) { $arrayActivosFijosRepeat[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }

            // CUENTAS CONTABLES COLGAAP
            if ($j ==15 || $j==16) { $arrayCuentasColgaap[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }
            // CUENTAS CONTABLES COLGAAP
            if ($j==20 || $j==21 || $j==22 || $j==23) { $arrayCuentasNiif[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }
            // NIT DE LOS PROVEEDORES
            if ($j ==24) { $arrayNitTercero[$valor_celda] = array('fila' => $i, 'columna' => $j, 'validate' => 'false' ); }

            $body .= $arrayExcel[$i][$j].'  ';
        }
        $body .= '<br>';
    }

    $random = $this->randomico_maestro(); // ID UNICO

    $sqlTercerosUpload   = "INSERT INTO activos_fijos_upload(random,id_usuario,usuario,fecha,hora,nombre_archivo,id_empresa)
                            VALUES('$random','$id_usuario','$nombre_usuario',NOW(),NOW(),'".$filename.".".$ext."','$id_empresa')";
    $queryTercerosUpload = mysql_query($sqlTercerosUpload);

    $sqlUpload   = "SELECT id FROM activos_fijos_upload WHERE random='$random' AND id_empresa='$id_empresa' LIMIT 0,1";
    $queryUpload = mysql_query($sqlUpload);
    $idUpload    = mysql_result($queryUpload, 0, 'id');

    // $arrayNombreColumnas[13] = 'CUENTA COLGAAP DEPRECIACION (ACTIVO)';
    // $arrayNombreColumnas[14] = 'CONTRAPARTIDA CUENTA COLGAAP DEPRECIACION (GASTO)';
    // $arrayNombreColumnas[19] = 'CUENTA NIIF DEPRECIACION (ACTIVO)';
    // $arrayNombreColumnas[20] = 'CONTRAPARTIDA NIIF DEPRECIACION (GASTO)';
    // $arrayNombreColumnas[21] = 'CUENTA NIIF DETERIORO (DEBITO)';
    // $arrayNombreColumnas[22] = 'CONTRAPARTIDA NIIF DETERIORO (CREDITO)';

    // RECORRER EL ARRAY DE LOS GRUPOS
    foreach ($arrayGrupos as $codigo_grupo => $arrayResul) {
        $whereGrupos .=( $whereGrupos == '' )? " codigo_grupo='$codigo_grupo'" : " OR codigo_grupo='$codigo_grupo'" ;
    }

    // RECORRER EL ARRAY DE LOS SUBGRUPOS
    foreach ($arraySubGrupos as $codigo_subgrupo => $arrayResul) {
        $whereSubgrupos .=( $whereSubgrupos == '' )? " codigo_subgrupo='$codigo_subgrupo'" : " OR codigo_subgrupo='$codigo_subgrupo'";
    }

    // RECORRER EL ARRAY DE LOS CENTROS DE COSTO
    foreach ($arrayCentroCostos as $codigo => $arrayResul) {
        $whereCentroCostos .=( $whereCentroCostos == '' )? " codigo='$codigo'" : " OR codigo='$codigo'" ;
    }

    // RECORRER EL ARRAY DE LOS ACTIVOS FIJOS
    foreach ($arrayActivosFijos as $codigo_activo => $arrayResul) {
        $whereActivosFijos .=( $whereActivosFijos == '' )? " codigo='$codigo_activo'" : " OR codigo='$codigo_activo'" ;
    }

    // RECORRER EL ARRAY DE LOS ACTIVOS FIJOS
    foreach ($arrayActivosFijosRepeat as $codigo_activo => $arrayResul) {
        $whereRepeatActivosFijos .=( $whereRepeatActivosFijos == '' )? " codigo_activo='$codigo_activo'" : " OR codigo_activo='$codigo_activo'" ;
    }

    // RECORRER EL ARRAY DE LAS CUENTAS COLGAAP
    foreach ($arrayCuentasColgaap as $cuenta => $arrayResul) {
        $whereCuentasColgaap .=( $whereCuentasColgaap == '' )? " cuenta='$cuenta'" : " OR cuenta='$cuenta'" ;
    }

    // RECORRER EL ARRAY DE LAS CUENTAS NIIF
    foreach ($arrayCuentasNiif as $cuenta => $arrayResul) {
        $whereCuentasNiif .=( $whereCuentasNiif == '' )? " cuenta='$cuenta'" : " OR cuenta='$cuenta'" ;
    }

    // RECORRER EL ARRAY DE LOS NIT DE LOS PROVEEDORES
    foreach ($arrayNitTercero as $numero_identificacion => $arrayResul) {
        $whereNitTercero .=( $whereNitTercero == '' )? " numero_identificacion='$numero_identificacion'" : " OR numero_identificacion='$numero_identificacion'" ;
    }

    // CONSULTAR LOS GRUPOS PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, codigo_grupo,nombre_grupo FROM inventario_grupo WHERE activo=1 AND id_empresa=$id_empresa AND ($whereGrupos)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $codigo_grupo                           = $row['codigo_grupo'];
        $arrayGrupos[$codigo_grupo]['validate'] = 'true';
        $arrayGrupos[$codigo_grupo]['id']       = $row['id'];
        $arrayGrupos[$codigo_grupo]['nombre']   = $row['nombre_grupo'];
        $arrayGrupos[$row['id']]                = [$codigo_grupo];
    }

    // CONSULTAR LOS SUBGRUPOS PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, codigo_subgrupo,id_grupo,nombre_subgrupo FROM inventario_grupo_subgrupo WHERE activo=1 AND ($whereSubgrupos)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $codigo_subgrupo = $row['codigo_subgrupo'];
        if ($arraySubGrupos[$codigo_subgrupo]<>'' && $arrayGrupos[$row['id_grupo']]<>'') {

            $arraySubGrupos[$codigo_subgrupo]['validate'] = 'true';
            $arraySubGrupos[$codigo_subgrupo]['id_grupo'] = $row['id_grupo'];
            $arraySubGrupos[$codigo_subgrupo]['nombre']   = $row['nombre_subgrupo'];
            $arraySubGrupos[$codigo_subgrupo]['id']       = $row['id'];
        }

    }

    // CONSULTAR LOS CENTRO DE COSTOS PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCentroCostos)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $codigo                                 = $row['codigo'];
        $arrayCentroCostos[$codigo]['validate'] = 'true';
        $arrayCentroCostos[$codigo]['id']       = $row['id'];
        $arrayCentroCostos[$codigo]['nombre']   = $row['nombre'];
    }

    // CONSULTAR LOS ACTIVOS PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, codigo,nombre_equipo FROM items WHERE activo=1 AND id_empresa=$id_empresa AND ($whereActivosFijos)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $codigo                                 = $row['codigo'];
        $arrayActivosFijos[$codigo]['validate'] = 'true';
        $arrayActivosFijos[$codigo]['id']       = $row['id'];
        $arrayActivosFijos[$codigo]['nombre']   = $row['nombre_equipo'];
    }

    // CONSULTAR LOS ACTIVOS PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, code_bar, codigo_activo, nombre_equipo FROM activos_fijos WHERE activo=1 AND id_empresa=$id_empresa AND ($whereRepeatActivosFijos)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $codigo_activo                                 = $row['codigo_activo'];
        $code_bar                                      = $row['code_bar'];
        $arrayActivosFijosRepeat[$codigo_activo]['validate'] = 'repeat';
    }
    // print_r($arrayActivosFijos);

    // CONSULTAR LOS CUENTAS COLGAAP PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, cuenta,descripcion FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCuentasColgaap)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $cuenta                                      = $row['cuenta'];
        $arrayCuentasColgaap[$cuenta]['validate']    = 'true';
        $arrayCuentasColgaap[$cuenta]['id']          = $row['id'];
        $arrayCuentasColgaap[$cuenta]['descripcion'] = $row['descripcion'];
    }

    // CONSULTAR LOS CUENTAS COLGAAP PARA VALIDAR QUE EXISTAN
    $sql="SELECT id, cuenta,descripcion FROM puc_niif WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCuentasNiif)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $cuenta                                   = $row['cuenta'];
        $arrayCuentasNiif[$cuenta]['validate']    = 'true';
        $arrayCuentasNiif[$cuenta]['id']          = $row['id'];
        $arrayCuentasNiif[$cuenta]['descripcion'] = $row['descripcion'];
    }

    // CONSULTAR LOS PROVEEDORES PARA VALIDAR QUE EXISTAN
    $sql="SELECT id,numero_identificacion, nombre_comercial FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND ($whereNitTercero)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $numero_identificacion                                        = $row['numero_identificacion'];
        $arrayNitTercero[$numero_identificacion]['validate']         = 'true';
        $arrayNitTercero[$numero_identificacion]['id']               = $row['id'];
        $arrayNitTercero[$numero_identificacion]['nombre_comercial'] = $row['nombre_comercial'];
    }

    // VALIDAR LOS GRUPOS
    // foreach ($arrayGrupos as $codigo_grupo => $arrayResul) {
    //     if ($arrayResul['validate']=='false') {
    //         $errorLoadFile = 'true';
    //         $resulProcess = "El grupo $codigo_grupo de la fila ".($arrayResul['fila']+1)." no existe en base de datos";
    //         // return;
    //     }
    // }

    // // VALIDAR LOS SUBGRUPOS
    // foreach ($arraySubGrupos as $codigo_subgrupo => $arrayResul) {
    //     if ($arrayResul['validate']=='false') {
    //         $errorLoadFile = 'true';
    //         $resulProcess = "El subgrupo $codigo_subgrupo de la fila ".($arrayResul['fila']+1)." no existe en base de datos";
    //         // return;
    //     }
    // }

    // // VALIDAR LOS CENTROS DE COSTO
    // foreach ($arrayCentroCostos as $codigo => $arrayResul) {
    //     if ($arrayResul['validate']=='false') {
    //         $errorLoadFile = 'true';
    //         $resulProcess = "El Centro de costos $codigo de la fila ".($arrayResul['fila']+1)." no existe en base de datos";
    //         // return;
    //     }
    // }

    // // VALIDAR LOS ACTIVOS FIJOS
    // foreach ($arrayCuentasColgaap as $cuenta => $arrayResul) {
    //     if ($arrayResul['validate']=='false') {
    //         // $errorLoadFile = 'true';
    //         // $resulProcess = "la cuenta $cuenta de la fila ".($arrayResul['fila']+1)." de la columna ".$arrayNombreColumnas[$arrayResul['columna']]." no existe en base de datos";
    //         // return;
    //     }
    // }

    // // VALIDAR LAS CUENTAS COLGAAP
    // foreach ($arrayCuentasNiif as $cuenta => $arrayResul) {
    //     if ($arrayResul['validate']=='false') {
    //         // $errorLoadFile = 'true';
    //         // $resulProcess = "la cuenta $cuenta de la fila ".($arrayResul['fila']+1)." de la columna ".$arrayNombreColumnas[$arrayResul['columna']]." no existe en base de datos";
    //         // return;
    //     }
    // }

    // RECORRER LAS FILAS
    for ($i=2; $i <=$contArray; $i++) {

        $valor_celda = $arrayExcel[$i][0];
        if ($valor_celda=='') { continue; }

        $msjError        = '';
        $estado          = 'si';
        $contRepetido    = 0;
        $contActivosTrue = 0;
        $contActivosFail = 0;
        if($arrayGrupos[$arrayExcel[$i][0]]['validate']          == 'false' || $arrayExcel[$i][0]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO DE GRUPO NO EXISTE';}
        if($arraySubGrupos[$arrayExcel[$i][1]]['validate']       == 'false' || $arrayExcel[$i][1]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO SUBGRUPO NO EXISTE';}
        if($arrayCentroCostos[$arrayExcel[$i][2]]['validate']    == 'false' || $arrayExcel[$i][2]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CENTRO COSTOS NO EXISTE';}
        if($arrayExcel[$i][3] <> 'No' && $arrayExcel[$i][3] <> 'Si' ){ $estado = 'no'; $msjError .= '<br/>* VALOR EN CAMPO DETERIORABLE INCORRECTO';}
        if($arrayActivosFijos[$arrayExcel[$i][4]]['validate']    == 'false' ){ $estado = 'no'; $msjError .= '<br/>* ACTIVO NO EXISTE COMO ITEM';}
        if($arrayExcel[$i][5] == '' ){ $estado = 'no'; $msjError .= '<br/>* VALOR EN CAMPO CODIGO DEL ACTIVO NO PUEDE ESTAR VACIO';}
        if( $arrayExcel[$i][6] <> 'terreno'
            && $arrayExcel[$i][6] <> 'equipo_oficina'
            && $arrayExcel[$i][6] <> 'maquinaria'
            && $arrayExcel[$i][6] <> 'equipo_computo_comunicacion'
            && $arrayExcel[$i][6] <> 'construcciones_edificaciones'){ $estado = 'no'; $msjError .= '<br/>* VALOR EN CAMPO TIPO INCORRECTO';}
        if( $arrayExcel[$i][11] <> 'linea_recta'
            && $arrayExcel[$i][11] <> 'reduccion_saldos'
            && $arrayExcel[$i][11] <> 'suma_digitos_year'){ $estado = 'no'; $msjError .= '<br/>* VALOR EN CAMPO METODO DEPRECIACION COLGAAP INCORRECTO'; }
        if($arrayCuentasColgaap[$arrayExcel[$i][14]]['validate'] == 'false' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CUENTA COLGAAP NO EXISTE';}
        if($arrayCuentasColgaap[$arrayExcel[$i][15]]['validate'] == 'false' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CUENTA CONTRAPARTIDA COLGAAP NO EXISTE';}
        if( $arrayExcel[$i][16] <> 'linea_recta'
            && $arrayExcel[$i][16] <> 'reduccion_saldos'
            && $arrayExcel[$i][16] <> 'suma_digitos_year'){ $estado = 'no'; $msjError .= '<br/>* VALOR EN CAMPO METODO DEPRECIACION NIIF INCORRECTO'; }
        if($arrayCuentasNiif[$arrayExcel[$i][20]]['validate'] == 'false'  || $arrayExcel[$i][20]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CUENTA NIIF DEPRECIACION NO EXISTE';}
        if($arrayCuentasNiif[$arrayExcel[$i][21]]['validate'] == 'false'  || $arrayExcel[$i][21]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CUENTA NIIF CONTRAPARTIDA DEPRECIACION NO EXISTE';}
        if($arrayCuentasNiif[$arrayExcel[$i][22]]['validate'] == 'false'  || $arrayExcel[$i][22]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CUENTA NIIF DETERIORO (DEBITO) NO EXISTE';}
        if($arrayCuentasNiif[$arrayExcel[$i][23]]['validate'] == 'false'  || $arrayExcel[$i][23]=='' ){ $estado = 'no'; $msjError .= '<br/>* CODIGO CUENTA NIIF DETERIORO (CREDITO) NO EXISTE';}
        if($arrayNitTercero [$arrayExcel[$i][24]]['validate']  == 'false' || $arrayExcel[$i][24]=='' ){ $estado = 'no'; $msjError .= '<br/>* TERCERO NO EXISTE';}

        if($arrayActivosFijosRepeat[$arrayExcel[$i][5]]['validate'] == "repeat"){ $estado = 'repetido'; $msjError = '<br/>* ACTIVO REPETIDO'; $contRepetido++; }
        else if($estado == 'si'){ $contActivosTrue++; }
        else{ $contActivosFail++; }

        $tieneError = "false";
        if($msjError != ""){ $tieneError = "true"; }

        $campos = "'".$arrayExcel[$i][0]."',
                    '".$arrayExcel[$i][1]."',
                    '".$arrayExcel[$i][2]."',
                    '".$arrayExcel[$i][3]."',
                    '".$arrayExcel[$i][4]."',
                    '".$arrayExcel[$i][5]."',
                    '".$arrayExcel[$i][6]."',
                    '".$arrayExcel[$i][11]."',
                    '".$arrayExcel[$i][9]."',
                    '".$arrayExcel[$i][12]."',
                    '".$arrayExcel[$i][13]."',
                    '".$arrayExcel[$i][14]."',
                    '".$arrayExcel[$i][15]."',
                    '".$arrayExcel[$i][16]."',
                    '".$arrayExcel[$i][18]."',
                    '".$arrayExcel[$i][19]."',
                    '".$arrayExcel[$i][20]."',
                    '".$arrayExcel[$i][21]."',
                    '".$arrayExcel[$i][22]."',
                    '".$arrayExcel[$i][23]."',
                    '".$arrayExcel[$i][24]."'
                    ";

            $tieneError = "false";
            if($msjError != ""){ $tieneError = "true"; }
            $value_upload .= "('$idUpload',$campos,'$estado','$msjError','$tieneError','".($arrayExcel[$i][0]['fila']+3)."',$id_empresa,$id_sucursal,$id_bodega),";


            if ($estado=='si') {
                $valueInsert .= "(
                                    '".$arrayActivosFijos[ $arrayExcel[$i][4] ]['id']."',
                                    '".$arrayExcel[$i][4]."',
                                    '".$arrayExcel[$i][5]."',
                                    '".$arrayActivosFijos[ $arrayExcel[$i][4] ]['nombre']."',
                                    '".$arrayGrupos[ $arrayExcel[$i][0] ]['id']."',
                                    '".$arrayExcel[$i][0]."',
                                    '".$arrayGrupos[ $arrayExcel[$i][0]  ]['nombre']."',
                                    '".$arraySubGrupos[ $arrayExcel[$i][1]  ]['id']."',
                                    '".$arrayExcel[$i][1]."',
                                    '".$arraySubGrupos[ $arrayExcel[$i][1]  ]['nombre']."',
                                    '".$arrayCentroCostos[ $arrayExcel[$i][2]  ]['id']."',
                                    '".$arrayExcel[$i][2]."',
                                    '".$arrayCentroCostos[ $arrayExcel[$i][2]  ]['nombre']."',
                                    '".$arrayExcel[$i][3]."',
                                    '".$arrayExcel[$i][6]."',
                                    '".$arrayExcel[$i][7]."',
                                    '".$arrayExcel[$i][8]."',
                                    'NCG',
                                    '".$arrayExcel[$i][9]."',
                                    '".$arrayExcel[$i][10]."',
                                    '".$arrayExcel[$i][11]."',
                                    '".$arrayExcel[$i][12]."',
                                    '".$arrayExcel[$i][13]."',
                                    '".$arrayCuentasColgaap[ $arrayExcel[$i][14] ]['id']."',
                                    '".$arrayExcel[$i][14]."',
                                    '".$arrayCuentasColgaap[ $arrayExcel[$i][15] ]['id']."',
                                    '".$arrayExcel[$i][15]."',
                                    '".$arrayExcel[$i][16]."',
                                    '".$arrayExcel[$i][17]."',
                                    '".$arrayExcel[$i][18]."',
                                    '".$arrayExcel[$i][19]."',
                                    '".$arrayCuentasNiif[ $arrayExcel[$i][20] ]['id']."',
                                    '".$arrayExcel[$i][20]."',
                                    '".$arrayCuentasNiif[ $arrayExcel[$i][21] ]['id']."',
                                    '".$arrayExcel[$i][21]."',
                                    '".$arrayCuentasNiif[ $arrayExcel[$i][22] ]['id']."',
                                    '".$arrayExcel[$i][22]."',
                                    '".$arrayCuentasNiif[ $arrayExcel[$i][23] ]['id']."',
                                    '".$arrayExcel[$i][23]."',
                                    '1',
                                    '".$arrayNitTercero[$arrayExcel[$i][24]]['id']."',
                                    '".$arrayExcel[$i][24]."',
                                    '".$arrayNitTercero[$arrayExcel[$i][24]]['nombre_comercial']."',
                                    '$idUpload',
                                    $id_bodega,
                                    $id_sucursal,
                                    $id_empresa
                                ),";
            }

    }

    $value_upload = substr($value_upload,0,-1);
    $valueInsert = substr($valueInsert,0,-1);

    $campos ="
                codigo_grupo,
                codigo_subgrupo,
                centro_costos,
                deteriorable,
                code_bar,
                codigo_activo,
                tipo,
                metodo_depreciacion_colgaap,
                consecutivo_nota,
                vida_util,
                valor_salvamento,
                cuenta_colgaap_depreciacion,
                contrapartida_cuenta_colgaap_depreciacion,
                metodo_depreciacion_niif,
                vida_util_niif,
                valor_salvamento_niif,
                cuenta_niif_depreciacion,
                contrapartida_niif_depreciacion,
                cuenta_niif_deterioro,
                contrapartida_cuenta_niif_deterioro,
                nit_tercero
                ";


    $sql   = "INSERT INTO activos_fijos_upload_registro(id_upload,$campos,estado,mensaje_error,tiene_error,fila_excel,id_empresa,id_sucursal,id_bodega) VALUES $value_upload";
    $query = $mysql->query($sql,$mysql->link);
    if(!$query){
        $errorLoadFile = 'true';
        $resulProcess = "No se guardo el registro del documento en la base de datos";
        return;
    }

    $sql   = "UPDATE activos_fijos_upload SET ok='$contActivosTrue', fail='$contActivosFail', repetido='$contRepetido' WHERE id='$idUpload'";
    $query = $mysql->query($sql,$mysql->link);
    if(!$query){
        $errorLoadFile = 'true';
        $resulProcess = "No se guardo el registro los activos fijos en la base de datos";
        return;
    }


    $sql="INSERT INTO activos_fijos
            (
                id_item,
                code_bar,
                codigo_activo,
                nombre_equipo,
                id_grupo,
                cod_grupo,
                grupo,
                id_subgrupo,
                cod_subgrupo,
                subgrupo,
                id_centro_costos,
                codigo_centro_costos,
                centro_costos,
                depreciable,
                tipo,
                fecha_compra,
                costo,
                documento_referencia,
                documento_referencia_consecutivo,
                fecha_inicio_depreciacion,
                metodo_depreciacion_colgaap,
                vida_util,
                valor_salvamento,
                id_cuenta_depreciacion,
                cuenta_depreciacion,
                id_contrapartida_depreciacion,
                contrapartida_depreciacion,
                metodo_depreciacion_niif,
                fecha_inicio_depreciacion_niif,
                vida_util_niif,
                valor_salvamento_niif,
                id_cuenta_depreciacion_niif,
                cuenta_depreciacion_niif,
                id_contrapartida_depreciacion_niif,
                contrapartida_depreciacion_niif,
                id_cuenta_deterioro_niif_debito,
                cuenta_deterioro_niif_debito,
                id_cuenta_deterioro_niif_credito,
                cuenta_deterioro_niif_credito,
                estado,
                id_proveedor,
                nit_proveedor,
                proveedor,
                id_saldo_inicial,
                id_bodega,
                id_sucursal,
                id_empresa
            )
            VALUES  $valueInsert
            ";
    $query=$mysql->query($sql,$mysql->link);
    if (!$query) {
        $errorLoadFile = 'true';
        $resulProcess = "No se guardaron los activos fijos en la base de datos";
        return;
    }



    // return array('success'=>true, 'idInsert'=>$idUpload);

    // CODIGO GRUPO
    // CODIGO SUBGRUPO
    // CENTRO COSTOS
    // DETERIORABLE
    // NOMBRE
    // TIPO
    // FECHA COMPRA
    // COSTO
    // CONSECUTIVO NOTA
    // CODIGO BARRAS
    // FECHA DEPRECIACION
    // METODO DEPRECIACION (COLGAAP)
    // VIDA UTIL
    // VALOR SALVAMENTO
    // CUENTA COLGAAP DEPRECIACION (ACTIVO)
    // CONTRAPARTIDA CUENTA COLGAAP DEPRECIACION (GASTO)
    // METODO DEPRECIACION (NIIF)
    // FECHA INICIO DEPRECIACION (NIIF)
    // VIDA UTIL (NIIF)
    // VALOR SALVAMENTO (NIIF)
    // CUENTA NIIF DEPRECIACION (ACTIVO)
    // CONTRAPARTIDA NIIF DEPRECIACION (GASTO)
    // CUENTA NIIF DETERIORO (DEBITO)
    // CONTRAPARTIDA NIIF DETERIORO (CREDITO)



    // code_bar
    // id_empresa
    // id_sucursal
    // id_bodega
    // id_grupo
    // grupo
    // cod_grupo
    // id_subgrupo
    // subgrupo
    // cod_subgrupo

    // tipo
    // vida_util
    // fecha_inicio_depreciacion
    // costo
    // valor_salvamento
    // valor_salvamento_niif
    // documento_contable
    // numero_documento
    // metodo_depreciacion_colgaap
    // id_cuenta_depreciacion
    // cuenta_depreciacion
    // id_contrapartida_depreciacion
    // contrapartida_depreciacion
    // id_cuenta_depreciacion_niif
    // cuenta_depreciacion_niif
    // id_contrapartida_depreciacion_niif
    // contrapartida_depreciacion_niif

    // fecha_inicio_depreciacion_niif
    // vida_util_niif
    // metodo_depreciacion_niif
    // id_cuenta_deterioro_niif_debito
    // cuenta_deterioro_niif_debito
    // id_cuenta_deterioro_niif_credito
    // cuenta_deterioro_niif_credito
    // depreciable

?>
