<?php

    include_once('../../../misc/excel/Classes/PHPExcel.php');
    $id_empresa     = $_SESSION['EMPRESA'];
    $id_sucursal    = $_SESSION['SUCURSAL'];
    $id_usuario     = $_SESSION['IDUSUARIO'];
    $cc_usuario     = $_SESSION['CEDULAFUNCIONARIO'];
    $nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

    $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    $contArray  = COUNT($arrayExcel);
    $contCol    = COUNT($arrayExcel[0]);
    $debug      = "cuentas";

    // return $mysql;
    // ARRAY COLUMNAS
    $arrayLabelCol = array(
                            0  => 'Columna <b>A</b>',
                            1  => 'Columna <b>B</b>',
                            2  => 'Columna <b>C</b>',
                            3  => 'Columna <b>D</b>',
                            4  => 'Columna <b>E</b>',
                            5  => 'Columna <b>F</b>',
                            6  => 'Columna <b>G</b>',
                            7  => 'Columna <b>H</b>',
                            8  => 'Columna <b>I</b>',
                            9  => 'Columna <b>J</b>',
                            10 => 'Columna <b>K</b>',
                            );

    // return  "string";
    // CONSULTAR LOS TERCEROS
    $sql="SELECT id,numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayTerceros[$row['numero_identificacion']] = array('id' => $row['id'],'nombre'=>$row['nombre'] );
    }

    // CONSULTAR EL PUC
    $sql="SELECT id,cuenta,descripcion FROM puc WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayPuc[$row['cuenta']] = array('id' => $row['id'], 'descripcion' => $row['descripcion'] );
    }

    // CONSULTAR PUC NIIF
    $sql="SELECT id,cuenta,descripcion FROM puc_niif WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayPucNiif[$row['cuenta']] = array('id' => $row['id'], 'descripcion' => $row['descripcion'] );
    }

     // CONSULTAR LOS CENTROS DE COSTOS
    $sql="SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayCentroCostos[$row['codigo']] = array('id' => $row['id'],'nombre'=>$row['nombre'] );
    }

    $contFilas = 0;
    foreach ($arrayExcel as $filas => $arrayExcelCol) {
                // $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[0]</div><div class='cell' data-col='3'> La cuenta (Local) no existe </div></div>";
        if ($contFilas<=0) { $contFilas++; continue; }
        // VALIDAR PUC
        if ($typeNota = 'colgaap') {
            if ($arrayExcelCol[0]<>'' && !array_key_exists("$arrayExcelCol[0]",$arrayPuc)) {
                $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[0]</div><div class='cell' data-col='3'> La cuenta (Local ".$arrayExcelCol[0].") no existe </div></div>";
            }
            $idCuenta = $arrayPuc[$arrayExcelCol[0]]['id'];
        }
        else{
            if ($arrayExcelCol[0]<>'' && !array_key_exists("$arrayExcelCol[0]",$arrayPucNiif)) {
                $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[0]</div><div class='cell' data-col='3'> La cuenta (Niif ".$arrayExcelCol[0].") no existe </div></div>";
            }
            $idCuenta = $arrayPucNiif[$arrayExcelCol[0]]['id'];
        }

        // VALIDAR QUE EL DEBITO Y CREDITO SEAN VALORES NUMERICOS
        if(!is_numeric($arrayExcelCol[2])) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[0]</div><div class='cell' data-col='3'>El campo Debito debe ser numerico (<i>Verificar formato</i>)</div></div>";
        }
        if(!is_numeric($arrayExcelCol[3])) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[0]</div><div class='cell' data-col='3'>El campo Credito debe ser numerico (<i>Verificar formato</i>)</div></div>";
        }
        if ( ($arrayExcelCol[2]==0 && $arrayExcelCol[3]==0) || ($arrayExcelCol[2]=='' && $arrayExcelCol[3]=='') ) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[0]</div><div class='cell' data-col='3'>Los campos Debito y Credito no pueden estar vacios o en cero los dos</div></div>";
        }

        // VALIDAR EL TERCERO
        if ($arrayExcelCol[4]<>'' && !array_key_exists("$arrayExcelCol[4]",$arrayTerceros)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[4]</div><div class='cell' data-col='3'> No existe el tercero ".$arrayExcelCol[4]."</div></div>";
        }

        // VALIDAR EL CENTRO DE COSTOS
        if (!array_key_exists("$arrayExcelCol[5]",$arrayCentroCostos) && $arrayExcelCol[5]<>'') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[5]</div><div class='cell' data-col='3'> No existe el centro de costos</div></div>";
        }

        $acumDebito  += $arrayExcelCol[2];
        $acumCredito += $arrayExcelCol[3];

        // CREAR EL INSERT DE LAS CUENTAS
        $valueInsert .= "('id_nota_replace','$idCuenta', '$arrayExcelCol[2]', '$arrayExcelCol[3]', '$id_empresa', '".$arrayTerceros[$arrayExcelCol[4]]['id']."','".$arrayCentroCostos[$arrayExcelCol[5]]['id']."'),";

    }

    // SI NO HAY ERRORES DE VALIDACION ENTONCES INSERTAR LOS ITEMS EN LA BASE DE DATOS
    if (empty($arrayError)) {
        if ($acumDebito <> $acumCredito) {
            $debug      = "bd";
            $arrayError[1] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Saldo en Debito y Credito no son iguales </div><div class='cell' data-col='3' >Verifique el saldo de las cuentas</div></div>";
        }

        // INSERTAR LA CABECERA DE LA NOTA
        $sql   = "INSERT INTO nota_contable_general(sinc_nota, id_empresa, id_sucursal, fecha_registro, fecha_nota, fecha_finalizacion, estado, id_tipo_nota, id_usuario, cedula_usuario, usuario)
                    VALUES ('$sinc_nota', '$id_empresa', '$id_sucursal', NOW(), NOW(), NOW(), 0, '$idFiltroNota', '$id_usuario', '$cc_usuario', '$nombre_usuario')";
        $query=$mysql->query($sql,$mysql->link);
        if (!$query) {
            $debug      = "bd";
            $arrayError[2] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar la nota contable </div><div class='cell' data-col='3' title='".$mysql->error()."'><b>".$mysql->errno()."</b>:".$mysql->error()."</div></div>";
            return;
        }
        $id_nota = $mysql->insert_id();

        $valueInsert = str_replace("id_nota_replace", $id_nota, $valueInsert);
        $valueInsert = substr($valueInsert,0,-1);

        // INSERTAR LAS CUENTAS DE LA NOTA
        $sql   = "INSERT INTO nota_contable_general_cuentas(id_nota_general,id_puc,debe,haber,id_empresa,id_tercero,id_centro_costos) VALUES $valueInsert";
        $query = $mysql->query($sql,$mysql->link);
        if (!$query) {
            $debug      = "bd";
            $arrayError[3] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar las cuentas de la nota contable </div><div class='cell' data-col='3' title='".$mysql->error()."'><b>".$mysql->errno()."</b>:".$mysql->error()."</div></div>";
            return;
        }
    }

?>
