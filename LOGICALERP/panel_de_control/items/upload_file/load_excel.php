<?php

    include_once('../../../../misc/excel/Classes/PHPExcel.php');
    $id_empresa  = $_SESSION['EMPRESA'];
    $debug       = "items";
    $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    $contArray  = COUNT($arrayExcel);
    $contCol    = COUNT($arrayExcel[0]);

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
                            11 => 'Columna <b>L</b>',
                            12 => 'Columna <b>M</b>',
                            13 => 'Columna <b>N</b>',
                            14 => 'Columna <b>O</b>',
                            15 => 'Columna <b>P</b>',
                            16 => 'Columna <b>Q</b>',
                            17 => 'Columna <b>R</b>',
                            18 => 'Columna <b>S</b>',
                            19 => 'Columna <b>T</b>',
                            20 => 'Columna <b>U</b>',
                            21 => 'Columna <b>V</b>',
                            22 => 'Columna <b>W</b>',
                            23 => 'Columna <b>X</b>',
                            24 => 'Columna <b>Y</b>',
                            25 => 'Columna <b>Z</b>',
                            26 => 'Columna <b>AA</b>',
                            27 => 'Columna <b>AB</b>',
                            28 => 'Columna <b>AC</b>',
                            29 => 'Columna <b>AD</b>',
                            30 => 'Columna <b>AE</b>',
                            31 => 'Columna <b>AF</b>',
                            32 => 'Columna <b>AG</b>',
                            33 => 'Columna <b>AH</b>',
                            34 => 'Columna <b>AI</b>',
                            35 => 'Columna <b>AJ</b>',
                            36 => 'Columna <b>AK</b>',
                            37 => 'Columna <b>AL</b>',
                            38 => 'Columna <b>AM</b>',
                            39 => 'Columna <b>AN</b>',
                            40 => 'Columna <b>AO</b>',
                            41 => 'Columna <b>AP</b>',
                            42 => 'Columna <b>AQ</b>',
                            43 => 'Columna <b>AR</b>',
                            44 => 'Columna <b>AS</b>',
                            );

    // CONSULTAR LAS UNIDADES DE MEDIDA
    $sql="SELECT id,nombre,unidades FROM inventario_unidades WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayUnidadMedida[$row['nombre']] = array('id' => $row['id'], 'unidades'=>$row['unidades']);
    }

    // CONSULTAR LAS FAMILIAS
    $sql="SELECT id,codigo,nombre FROM items_familia WHERE  activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayFamilias[$row['codigo']] = array('id' => $row['id'], 'nombre'=>$row['nombre'] );
    }

    // CONSULTAR LOS GRUPOS
    $sql="SELECT id,codigo,nombre,cod_familia,familia FROM items_familia_grupo WHERE  activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayGrupos[$row['cod_familia']][$row['codigo']] = array('id' => $row['id'], 'nombre'=>$row['nombre'] );
    }

    // CONSULTAR LOS SUBGRUPOS
    $sql="SELECT id,codigo,nombre,cod_familia,familia,cod_grupo,grupo FROM items_familia_grupo_subgrupo WHERE  activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)){
        $arraySubgrupos[$row['cod_familia']][$row['cod_grupo']][$row['codigo']] = array('id' => $row['id'], 'nombre'=>$row['nombre'] );
    }

    // CONSULTAR LOS CENTROS DE COSTOS
    $sql="SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayCentroCostos[$row['codigo']] = array('id' => $row['id'],'nombre'=>$row['nombre'] );
    }

    // CONSULTAR IMPUESTO
    $sql="SELECT id,impuesto,valor,compra,venta FROM impuestos WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayImpuesto[$row['valor']*1] = array(
                                        'id'       => $row['id'],
                                        'impuesto' => $row['impuesto'],
                                        'compra'   => $row['compra'],
                                        'venta'    => $row['venta'],
                                    );
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

    $contFilas = 0;
    $arrayRepeat = '';
    foreach ($arrayExcel as $filas => $arrayExcelCol) {
        if ($contFilas<=1) { $contFilas++; continue; }

        // VALIDAR QUE EL EXCEL NO TENGA ITEMS CODIGO REPETIDO
        if (array_key_exists($arrayExcelCol[0],$arrayRepeat)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>Fila <b>".($filas+1)."</b></div><div class='cell' data-col='3'><b><i> El codigo de este item esta repetido en el excel</i></b> </div></div>";
            continue;
        }

        $arrayRepeat[$arrayExcelCol[0]] = $arrayExcelCol[9];

        $whereCodigo  .= ($whereCodigo=='')? " codigo=$arrayExcelCol[0] " : " OR codigo=$arrayExcelCol[0] ";

        // VALIDAR LA UNIDAD DE MEDIDA
        if (!array_key_exists("$arrayExcelCol[2]",$arrayUnidadMedida)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[2]</div><div class='cell' data-col='3'> No existe la unidad de medida </div></div>";
        }

        // VALIDAR LA FAMILIA
        if (!array_key_exists("$arrayExcelCol[5]",$arrayFamilias)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[5]</div><div class='cell' data-col='3'> No existe el codigo de la familia </div></div>";
            // VALIDAR EL GRUPO
            if (!array_key_exists("$arrayExcelCol[6]",$arrayGrupos[$arrayExcelCol[5]])) {
                // VALIDAR LA SUBGRUPO
                $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[6]</div><div class='cell' data-col='3'> No existe el codigo del grupo </div></div>";
                if (!array_key_exists("$arrayExcelCol[7]",$arraySubgrupos[$arrayExcelCol[5]][$arrayExcelCol[6]])) {
                    $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[7]</div><div class='cell' data-col='3'> No existe el codigo del subgrupo </div></div>";
                }
            }
        }

        // VALIDAR EL CENTRO DE COSTOS
        if (!array_key_exists("$arrayExcelCol[8]",$arrayCentroCostos) && $arrayExcelCol[8]<>'') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[8]</div><div class='cell' data-col='3'> No existe el centro de costos</div></div>";
        }

        // VALIDAR EL QUE TENGA NOMBRE
        if ($arrayExcelCol[9]=='') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[9]</div><div class='cell' data-col='3'> El campo nombre item es obligatorio</div></div>";
        }

        // VALIDAR EL IVA
        if (array_key_exists("$arrayExcelCol[18]",$arrayImpuesto) ) {
            // if ($arrayImpuesto[$arrayExcelCol[18]]['venta'] == 'No' || $arrayImpuesto[$arrayExcelCol[18]]['compra'] == 'No' ) {
            //     $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[18]</div><div class='cell' data-col='3'> El iva relacionado no esta disponible en compra y venta </div></div>";
            // }
        }
        else{
            if ($arrayExcelCol[18]>0) {
                $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[18] </div><div class='cell' data-col='3'> No existe el iva relacionado ($arrayExcelCol[18]%)</div></div>";
            }
        }

        // VALIDAR TRUE O FALSE EN OPCIONES DE LOS ITEMS
        if ($arrayExcelCol[19]<>'true' && $arrayExcelCol[19]<>'false' /*&& $arrayExcelCol[19]<>''*/) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[19]</div><div class='cell' data-col='3'> El valor del campo inventariable no es correcto debe ser true o false </div></div>";
        }
        if ($arrayExcelCol[20]<>'true' && $arrayExcelCol[20]<>'false'/* && $arrayExcelCol[20]<>''*/) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[20]</div><div class='cell' data-col='3'> El valor del campo compra no es correcto debe ser true o false </div></div>";
        }
        if ($arrayExcelCol[21]<>'true' && $arrayExcelCol[21]<>'false' /*&& $arrayExcelCol[21]<>''*/) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[21]</div><div class='cell' data-col='3'> El valor del campo venta no es correcto debe ser true o false </div></div>";
        }
        if ($arrayExcelCol[22]<>'true' && $arrayExcelCol[22]<>'false' && $arrayExcelCol[22]<>'') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[22]</div><div class='cell' data-col='3'> El valor del campo Costo no es correcto debe ser true o false </div></div>";
        }
        if ($arrayExcelCol[23]<>'true' && $arrayExcelCol[23]<>'false' && $arrayExcelCol[23]<>'') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[23]</div><div class='cell' data-col='3'> El valor del campo Gasto no es correcto debe ser true o false </div></div>";
        }
        if ($arrayExcelCol[24]<>'true' && $arrayExcelCol[24]<>'false' && $arrayExcelCol[24]<>'') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[24]</div><div class='cell' data-col='3'> El valor del campo Activo Fijo no es correcto debe ser true o false </div></div>";
        }

        // VALIDAR QUE ESTE DISPONIBLE EN COMPRA O EN VENTA
        if ($arrayExcelCol[20]=='false' && $arrayExcelCol[21]=='false') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> El item debe estar disponible en compra o en venta </div></div>";
        }

        // VALIDAR QUE LAS OPCIONES SELECCIONADAS TENGAN CUENTA CONTABLE
        if ($arrayExcelCol[21]=='true' && $arrayExcelCol[33]=='' && $arrayExcelCol[34]=='' && $arrayExcelCol[37]=='' ) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> Los campos con las cuentas de venta no estan completamente llenas </div></div>";
        }
        if ($arrayExcelCol[20]=='true' && $arrayExcelCol[25]=='' && $arrayExcelCol[29]=='' ) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> Los campos con las cuentas de compra no estan completamente llenas </div></div>";
        }

        if ($arrayExcelCol[19]=='true' && $arrayExcelCol[35]=='' && $arrayExcelCol[36]=='' && $arrayExcelCol[38]=='' && $arrayExcelCol[39]=='') {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> El item es inventariable pero no tiene las cuentas de invetario </div></div>";
        }
        if ($arrayExcelCol[22]=='true' && $arrayExcelCol[28]=='' && $arrayExcelCol[32]=='' ) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> El item esta configurado como costo pero no tiene las cuentas de costo</div></div>";
        }
        if ($arrayExcelCol[23]=='true' && $arrayExcelCol[27]=='' && $arrayExcelCol[31]=='' ) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> El item esta configurado como gasto pero no tiene las cuentas </div></div>";
        }
        if ($arrayExcelCol[24]=='true' && $arrayExcelCol[26]=='' && $arrayExcelCol[30]=='' ) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> El item esta configurado como activo fijo pero no tiene las cuentas </div></div>";
        }

        // VALIDAR PUC
        if ($arrayExcelCol[25]<>'' && !array_key_exists("$arrayExcelCol[25]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[25]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Subtotal en compra no existe </div></div>";
        }
        if ($arrayExcelCol[26]<>'' && !array_key_exists("$arrayExcelCol[26]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[26]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Subtotal (Activo Fijo) en compra no existe </div></div>";
        }
        if ($arrayExcelCol[27]<>'' && !array_key_exists("$arrayExcelCol[27]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[27]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Subtotal (Gasto) en compra no existe </div></div>";
        }
        if ($arrayExcelCol[28]<>'' && !array_key_exists("$arrayExcelCol[28]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[28]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Subtotal (Costo) en compra no existe </div></div>";
        }
        if ($arrayExcelCol[33]<>'' && !array_key_exists("$arrayExcelCol[33]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[33]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Subtotal en venta no existe </div></div>";
        }
        if ($arrayExcelCol[34]<>'' && !array_key_exists("$arrayExcelCol[34]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[34]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Subtotal devolucion en venta no existe </div></div>";
        }
        if ($arrayExcelCol[35]<>'' && !array_key_exists("$arrayExcelCol[35]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[35]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Inventario en venta no existe </div></div>";
        }
        if ($arrayExcelCol[36]<>'' && !array_key_exists("$arrayExcelCol[36]",$arrayPuc)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[36]</div><div class='cell' data-col='3'> La cuenta (Local) del campo Costo en venta no existe </div></div>";
        }

        // VALIDAR PUC NIIF
        if ($arrayExcelCol[29]<>'' && !array_key_exists("$arrayExcelCol[29]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[29]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Subtotal en compra no existe </div></div>";
        }
        if ($arrayExcelCol[30]<>'' && !array_key_exists("$arrayExcelCol[30]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[30]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Subtotal (Activo Fijo) en compra no existe </div></div>";
        }
        if ($arrayExcelCol[31]<>'' && !array_key_exists("$arrayExcelCol[31]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[31]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Subtotal (Gasto) en compra no existe </div></div>";
        }
        if ($arrayExcelCol[32]<>'' && !array_key_exists("$arrayExcelCol[32]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[32]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Subtotal (Costo) en compra no existe </div></div>";
        }
        if ($arrayExcelCol[37]<>'' && !array_key_exists("$arrayExcelCol[37]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[37]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Subtotal en venta no existe </div></div>";
        }
        if ($arrayExcelCol[38]<>'' && !array_key_exists("$arrayExcelCol[38]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[38]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Inventario en venta no existe </div></div>";
        }
        if ($arrayExcelCol[39]<>'' && !array_key_exists("$arrayExcelCol[39]",$arrayPucNiif)) {
            $arrayError[$arrayExcelCol[0]] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'>$arrayLabelCol[39]</div><div class='cell' data-col='3'> La cuenta (Niif) del campo Costo en venta no existe </div></div>";
        }

        // CREAR INSERT DE LOS ITEMS POR CADA BODEGA
        $valueInsert .="(
                            'false',
                            '$arrayExcelCol[0]',
                            '$arrayExcelCol[1]',
                            '".$arrayUnidadMedida[$arrayExcelCol[2]]['id']."',
                            '".$arrayExcelCol[2]."',
                            '".$arrayUnidadMedida[$arrayExcelCol[2]]['unidades']."',
                            '$arrayExcelCol[3]',
                            '$arrayExcelCol[4]',
                            '$id_empresa',
                            '".$arrayFamilias[$arrayExcelCol[5]]['id']."',
                            '".$arrayFamilias[$arrayExcelCol[5]]['nombre']."',
                            '".$arrayGrupos[$arrayExcelCol[5]][$arrayExcelCol[6]]['id']."',
                            '".$arrayGrupos[$arrayExcelCol[5]][$arrayExcelCol[6]]['nombre']."',
                            '".$arraySubgrupos[$arrayExcelCol[5]][$arrayExcelCol[6]][$arrayExcelCol[7]]['id']."',
                            '".$arraySubgrupos[$arrayExcelCol[5]][$arrayExcelCol[6]][$arrayExcelCol[7]]['nombre']."',
                            '".$arrayCentroCostos[$arrayExcelCol[8]]['id']."',
                            '".$arrayCentroCostos[$arrayExcelCol[8]]['nombre']."',
                            '$arrayExcelCol[9]',
                            NOW(),
                            '$arrayExcelCol[10]',
                            '$arrayExcelCol[11]',
                            '$arrayExcelCol[12]',
                            '$arrayExcelCol[13]',
                            '$arrayExcelCol[14]',
                            '$arrayExcelCol[15]',
                            '$arrayExcelCol[16]',
                            '$arrayExcelCol[17]',
                            '$_SESSION[IDUSUARIO]',
                            '".$arrayImpuesto[$arrayExcelCol[18]]['id']."',
                            '".$arrayImpuesto[$arrayExcelCol[18]]['impuesto']."',
                            '$arrayExcelCol[19]',
                            '$arrayExcelCol[20]',
                            '$arrayExcelCol[21]',
                            '$arrayExcelCol[22]',
                            '$arrayExcelCol[23]',
                            '$arrayExcelCol[24]',
                            'false',
                            'false',
                            'false',
                            '$arrayExcelCol[40]',
                            '$arrayExcelCol[41]',
                            '$arrayExcelCol[42]',
                            '$arrayExcelCol[43]',
                            '$arrayExcelCol[44]'

                        ),";


        $whereItems .=($whereItems=="")? " codigo='$arrayExcelCol[0]' " : " OR codigo='$arrayExcelCol[0]' ";

        $arrayPuc[$row['cuenta']] = array('id' => $row['id'], 'descripcion' => $row['descripcion'] );

        $arrayPucNiif[$row['cuenta']] = array('id' => $row['id'], 'descripcion' => $row['descripcion'] );

        // compra gasto
        // compra precio
        // compra impuesto
        // compra activo_fijo
        // compra contraPartida_precio

        // venta costo
        // venta precio
        // venta impuesto
        // venta contraPartida_costo
        // venta contraPartida_precio

        $arrayCuentas[$arrayExcelCol[0]]['venta']['precio']                = array('alias'=>'Subtotal', 'id_puc'=> $arrayPuc[$arrayExcelCol[33]]['id'], 'puc'=>$arrayExcelCol[33],'cuenta'=>$arrayPuc[$arrayExcelCol[33]]['descripcion'],'tipo'=>'credito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['venta']['impuesto']              = array('alias'=>'Impuesto', 'id_puc'=> 0, 'puc'=>2222222,'cuenta'=>' ','tipo'=>'credito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['venta']['devprecio']             = array('alias'=>'SubTotal en Devolucion', 'id_puc'=> $arrayPuc[$arrayExcelCol[34]]['id'], 'puc'=>$arrayExcelCol[34],'cuenta'=>$arrayPuc[$arrayExcelCol[34]]['descripcion'],'tipo'=>'debito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['venta']['costo']                 = array('alias'=>'Inventario', 'id_puc'=> $arrayPuc[$arrayExcelCol[35]]['id'], 'puc'=>$arrayExcelCol[35],'cuenta'=>$arrayPuc[$arrayExcelCol[35]]['descripcion'],'tipo'=>'credito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['venta']['contraPartida_costo']   = array('alias'=>'Costo', 'id_puc'=> $arrayPuc[$arrayExcelCol[36]]['id'], 'puc'=>$arrayExcelCol[36],'cuenta'=>$arrayPuc[$arrayExcelCol[36]]['descripcion'],'tipo'=>'debito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['venta']['contraPartida_precio']  = array('alias'=>'Precio', 'id_puc'=> 0, 'puc'=>13050501,'cuenta'=>'','tipo'=>'debito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['costo']                = array('alias'=>'Subtotal (Opcional - Costo)', 'id_puc'=> $arrayPuc[$arrayExcelCol[28]]['id'], 'puc'=>$arrayExcelCol[28],'cuenta'=>$arrayPuc[$arrayExcelCol[28]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['impuesto']             = array('alias'=>'Impuesto', 'id_puc'=> 0, 'puc'=>2222222,'cuenta'=>' ','tipo'=>'debito','estado'=>'compra');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['gasto']                = array('alias'=>'Subtotal (Opcional - Gasto Venta)', 'id_puc'=> $arrayPuc[$arrayExcelCol[27]]['id'], 'puc'=>$arrayExcelCol[27],'cuenta'=>$arrayPuc[$arrayExcelCol[27]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['precio']               = array('alias'=>'SubTotal', 'id_puc'=> $arrayPuc[$arrayExcelCol[25]]['id'], 'puc'=>$arrayExcelCol[25],'cuenta'=>$arrayPuc[$arrayExcelCol[25]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['activo_fijo']          = array('alias'=>'SubTotal (Opcional - Activo Fijo)', 'id_puc'=> $arrayPuc[$arrayExcelCol[26]]['id'], 'puc'=>$arrayExcelCol[26],'cuenta'=>$arrayPuc[$arrayExcelCol[26]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['contraPartida_costo']  = array('alias'=>'Costo', 'id_puc'=> 0, 'puc'=>'','cuenta'=>'','tipo'=>'debito','estado'=>'venta');
        $arrayCuentas[$arrayExcelCol[0]]['compra']['contraPartida_precio'] = array('alias'=>'Precio', 'id_puc'=> 0, 'puc'=>'13050501','cuenta'=>'','tipo'=>'credito','estado'=>'venta');

        $arrayCuentasNiif[$arrayExcelCol[0]]['venta']['precio']                = array('alias'=>'SubTotal','id_puc'=>$arrayPucNiif[$arrayExcelCol[37]]['id'], 'puc'=>$arrayExcelCol[37],'cuenta'=>$arrayPucNiif[$arrayExcelCol[37]]['descripcion'],'tipo'=>'credito','estado'=>'venta');
        $arrayCuentasNiif[$arrayExcelCol[0]]['venta']['impuesto']              = array('alias'=>'Impuesto', 'id_puc'=> 0, 'puc'=>2222222,'cuenta'=>' ','tipo'=>'credito','estado'=>'venta');
        $arrayCuentasNiif[$arrayExcelCol[0]]['venta']['costo']                 = array('alias'=>'Inventario','id_puc'=>$arrayPucNiif[$arrayExcelCol[35]]['id'], 'puc'=>$arrayExcelCol[35],'cuenta'=>$arrayPucNiif[$arrayExcelCol[35]]['descripcion'],'tipo'=>'credito','estado'=>'venta');
        $arrayCuentasNiif[$arrayExcelCol[0]]['venta']['contraPartida_costo']   = array('alias'=>'Costo','id_puc'=>$arrayPucNiif[$arrayExcelCol[36]]['id'], 'puc'=>$arrayExcelCol[36],'cuenta'=>$arrayPucNiif[$arrayExcelCol[36]]['descripcion'],'tipo'=>'debito','estado'=>'venta');
        $arrayCuentasNiif[$arrayExcelCol[0]]['venta']['contraPartida_precio']  = array('alias'=>'Costo','id_puc'=>0, 'puc'=>13050501,'cuenta'=>'','tipo'=>'debito','estado'=>'venta');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['costo']                = array('alias'=>'Subtotal (Opcional - Costo)','id_puc'=>$arrayPucNiif[$arrayExcelCol[32]]['id'], 'puc'=>$arrayExcelCol[32],'cuenta'=>$arrayPucNiif[$arrayExcelCol[32]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['impuesto']             = array('alias'=>'Impuesto', 'id_puc'=> 0, 'puc'=>2222222,'cuenta'=>' ','tipo'=>'debito','estado'=>'compra');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['gasto']                = array('alias'=>'Subtotal (Opcional - Gasto Venta)','id_puc'=>$arrayPucNiif[$arrayExcelCol[31]]['id'], 'puc'=>$arrayExcelCol[31],'cuenta'=>$arrayPucNiif[$arrayExcelCol[31]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['precio']               = array('alias'=>'SubTotal','id_puc'=>$arrayPucNiif[$arrayExcelCol[29]]['id'], 'puc'=>$arrayExcelCol[29],'cuenta'=>$arrayPucNiif[$arrayExcelCol[29]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['activo_fijo']          = array('alias'=>'SubTotal (Opcional - Activo Fijo)','id_puc'=>$arrayPucNiif[$arrayExcelCol[30]]['id'], 'puc'=>$arrayExcelCol[30],'cuenta'=>$arrayPucNiif[$arrayExcelCol[30]]['descripcion'],'tipo'=>'debito','estado'=>'compra');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['contraPartida_costo']  = array('alias'=>'Costo','id_puc'=>0, 'puc'=>'','cuenta'=>'','tipo'=>'debito','estado'=>'compra');
        $arrayCuentasNiif[$arrayExcelCol[0]]['compra']['contraPartida_precio'] = array('alias'=>'Precio','id_puc'=>0, 'puc'=>13050501,'cuenta'=>'','tipo'=>'credito','estado'=>'compra');

        $arrayItems[$arrayExcelCol[0]] = array(
                                                'id_item'               => '',
                                                'code_bar'              => $arrayExcelCol[1],
                                                'nombre_equipo'         => $arrayExcelCol[9],
                                                'unidad_medida'         => $arrayExcelCol[2],
                                                'cantidad_unidades'     => $arrayUnidadMedida[$arrayExcelCol[2]]['unidades'],
                                                'cantidad_minima_stock' => $arrayExcelCol[3],
                                                'cantidad_maxima_stock' => $arrayExcelCol[4],
                                                'costos'                => $arrayExcelCol[16],
                                                'precio_venta'          => $arrayExcelCol[17],
                                                'id_familia'            => $arrayFamilias[$arrayExcelCol[5]]['id'],
                                                'familia'               => $arrayFamilias[$arrayExcelCol[5]]['nombre'],
                                                'id_grupo'              => $arrayGrupos[$arrayExcelCol[5]][$arrayExcelCol[6]]['id'],
                                                'grupo'                 => $arrayGrupos[$arrayExcelCol[5]][$arrayExcelCol[6]]['nombre'],
                                                'id_subgrupo'           => $arraySubgrupos[$arrayExcelCol[5]][$arrayExcelCol[6]][$arrayExcelCol[7]]['id'],
                                                'subgrupo'              => $arraySubgrupos[$arrayExcelCol[5]][$arrayExcelCol[6]][$arrayExcelCol[7]]['nombre'],
                                                'id_impuesto'           => $arrayImpuesto[$arrayExcelCol[18]]['id'],
                                                'inventariable'         => $arrayExcelCol[19],
                                                'estado_compra'         => $arrayExcelCol[20],
                                                'estado_venta'          => $arrayExcelCol[21],
                                                );

    }

    // VALIDAR CODIGOS DE ITEM REPETIDOS EN EL SISTEMA
    $sql="SELECT codigo FROM items WHERE activo=1 AND id_empresa=$id_empresa AND ($whereCodigo) ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $arrayError[$row['codigo']] .= "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'></div><div class='cell' data-col='3'> Ya existe un item en el sistema con ese codigo </div></div>";
    }

    // SI NO HAY ERRORES DE VALIDACION ENTONCES INSERTAR LOS ITEMS EN LA BASE DE DATOS
    if (empty($arrayError)) {
        //
        $valueInsert = substr($valueInsert, 0, -1);
        $sqlItems="INSERT INTO items
                (
                    codigo_auto,
                    codigo,
                    code_bar,
                    id_unidad_medida,
                    unidad_medida,
                    cantidad_unidades,
                    cantidad_minima_stock,
                    cantidad_maxima_stock,
                    id_empresa,
                    id_familia,
                    familia,
                    id_grupo,
                    grupo,
                    id_subgrupo,
                    subgrupo,
                    id_centro_costos,
                    centro_costos,
                    nombre_equipo,
                    fecha_creacion_en_inventario,
                    marca,
                    modelo,
                    color,
                    numero_piezas,
                    descripcion1,
                    descripcion2,
                    costos,
                    precio_venta,
                    id_usuario_creacion,
                    id_impuesto,
                    impuesto,
                    inventariable,
                    estado_compra,
                    estado_venta,
                    opcion_costo,
                    opcion_gasto,
                    opcion_activo_fijo,
                    modulo_pos,
                    item_produccion,
                    item_transformacion,
                    precio_venta_1,
                    precio_venta_2,
                    precio_venta_3,
                    precio_venta_4,
                    precio_venta_5

                )
                VALUES
                    $valueInsert
                ";
        $query=$mysql->query($sqlItems,$mysql->link);
        if (!$query) {
            $debug = 'bd';
            $arrayError[1] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar los items </div><div class='cell' data-col='3' title='".$mysql->error()."'><b>".$mysql->errno()."</b>:".$mysql->error()."</div></div>";
            rollback($arrayItems,$id_empresa,$mysql);
            return;
        }

        // CONSULTAR EL ID DE LOS ITEMS Y ARMAR INSERT DE LA CUENTAS
        $sql="SELECT id,codigo FROM items WHERE activo=1 AND id_empresa=$id_empresa AND ($whereItems)";
        $query=$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)) {
            $codigo = $row['codigo'];

            $arrayItems[$codigo]['id_item'] = $row['id'];

            // CUENTAS COLGAAP
            foreach ($arrayCuentas[$codigo] as $estado => $arrayCuentasResult) {
                foreach ($arrayCuentasResult as $descripcion => $arrayResult) {
                    $valueInsertCuentas .= "(
                                                '$descripcion',
                                                '$row[id]',
                                                '$codigo',
                                                '$arrayResult[id_puc]',
                                                '$arrayResult[puc]',
                                                '$arrayResult[cuenta]',
                                                '$arrayResult[tipo]',
                                                '$id_empresa',
                                                '$estado'
                                            ),";
                }
            }

            // CUENTAS NIIF
            foreach ($arrayCuentasNiif[$codigo] as $estado => $arrayCuentasResult) {
                foreach ($arrayCuentasResult as $descripcion => $arrayResult) {
                    $valueInsertCuentasNiif .= "(
                                                '$descripcion',
                                                '$row[id]',
                                                '$codigo',
                                                '$arrayResult[id_puc]',
                                                '$arrayResult[puc]',
                                                '$arrayResult[cuenta]',
                                                '$arrayResult[tipo]',
                                                '$id_empresa',
                                                '$estado'
                                            ),";
                }
            }
        }

        // INSERTAR LAS CUENTAS CONTABLES DE LOS ITEMS
        $valueInsertCuentas = substr($valueInsertCuentas, 0, -1);
        $sqlLocal="INSERT INTO items_cuentas
                (
                    descripcion,
                    id_items,
                    codigo_items,
                    id_puc,
                    puc,
                    cuenta,
                    tipo,
                    id_empresa,
                    estado
                )
                VALUES
                $valueInsertCuentas";
        $query=$mysql->query($sqlLocal,$mysql->link);

        if (!$query) {
            $debug = 'bd';
            $arrayError[2] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar las cuentas colgaap $sqlLocal </div><div class='cell' data-col='3' title='".$mysql->error()."'><b>".$mysql->errno()."</b>:".$mysql->error()." </div></div>";
            rollback($arrayItems,$id_empresa,$mysql);
            return;
        }

        $valueInsertCuentasNiif = substr($valueInsertCuentasNiif, 0, -1);
        $sql="INSERT INTO items_cuentas_niif
                (
                    descripcion,
                    id_items,
                    codigo_items,
                    id_puc,
                    puc,
                    cuenta,
                    tipo,
                    id_empresa,
                    estado
                )
                VALUES
                $valueInsertCuentasNiif";
        $query=$mysql->query($sql,$mysql->link);
        if (!$query) {
            $debug = 'bd';
            $arrayError[3] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar las cuentas niif </div><div class='cell' data-col='3' title='".$mysql->error()."'><b>".$mysql->errno()."</b>:".$mysql->error()."</div></div>";
            rollback($arrayItems,$id_empresa,$mysql);
            return;
        }

        // CONSULTAR LAS BODEGAS DE LA EMPRESA PARA INSERTARLOS EN CADA UNA
        $sql="SELECT id,nombre,id_sucursal,sucursal,empresa FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$id_empresa ";
        $query=$mysql->query($sql,$mysql->link);
        $valueInsert = '';
        while ($row=$mysql->fetch_array($query)) {

            // RECORRER LOS ITEMS, PARA INSERTARLOS EN LAS BODEGAS
            foreach ($arrayItems as $codigo => $arrayResult) {
                $valueInsert .= "(
                                '$arrayResult[id_item]',
                                '$codigo',
                                '$arrayResult[code_bar]',
                                '$arrayResult[nombre_equipo]',
                                '$arrayResult[unidad_medida]',
                                '$arrayResult[cantidad_unidades]',
                                '$arrayResult[cantidad_minima_stock]',
                                '$arrayResult[cantidad_maxima_stock]',
                                '$arrayResult[costos]',
                                '$arrayResult[precio_venta]',
                                '$id_empresa',
                                '$row[empresa]',
                                '$row[id_sucursal]',
                                '$row[sucursal]',
                                '$row[id]',
                                '$row[nombre]',
                                '$arrayResult[id_familia]',
                                '$arrayResult[familia]',
                                '$arrayResult[id_grupo]',
                                '$arrayResult[grupo]',
                                '$arrayResult[id_subgrupo]',
                                '$arrayResult[subgrupo]',
                                '$arrayResult[id_impuesto]',
                                '$arrayResult[cantidad]',
                                '$arrayResult[cantidad_pendiente]',
                                '$arrayResult[inventariable]',
                                '$arrayResult[estado_compra]',
                                '$arrayResult[estado_venta]'
                            ),";
            }

        }

        // INSERTAR EN CADA BODEGA
        $valueInsert = substr($valueInsert, 0, -1);
        $sql="INSERT INTO inventario_totales
                (
                    id_item,
                    codigo,
                    code_bar,
                    nombre_equipo,
                    unidad_medida,
                    cantidad_unidades,
                    cantidad_minima_stock,
                    cantidad_maxima_stock,
                    costos,
                    precio_venta,
                    id_empresa,
                    empresa,
                    id_sucursal,
                    sucursal,
                    id_ubicacion,
                    ubicacion,
                    id_familia,
                    familia,
                    id_grupo,
                    grupo,
                    id_subgrupo,
                    subgrupo,
                    id_impuesto,
                    cantidad,
                    cantidad_pendiente,
                    inventariable,
                    estado_compra,
                    estado_venta
                )
                VALUES
                $valueInsert";
        $query=$mysql->query($sql,$mysql->link);
        if (!$query) {
            $debug = 'bd';
            $arrayError[4] = "<div class='row'><div class='cell' data-col='1'></div><div class='cell' data-col='2'> Error al insertar los items en cada bodega </div><div class='cell' data-col='3' title='".$mysql->error()."' ><b>".$mysql->errno()."</b>:".$mysql->error()."</div></div>";
            rollback($arrayItems,$id_empresa,$mysql);
            return;
        }

    }

    function rollback($arrayItems,$id_empresa,$mysql){
        foreach ($arrayItems as $codigo => $arrayResult){
            $sqlItemsDrop   .=($sqlItemsDrop=='')? " codigo='$codigo' " : " OR codigo='$codigo' ";
            $sqlCuentasDrop .=($sqlCuentasDrop=='')? " id_items='$arrayResult[id_item]' " : " OR id_items='$arrayResult[id_item]' ";
            $sqlBodegasDrop .=($sqlBodegasDrop=='')? " codigo='$codigo' " : " OR codigo='$codigo' ";
        }

        // ELIMINAR LOS ITEMS INSERTADOS
        $sql = "DELETE FROM items WHERE activo=1 AND id_empresa=$id_empresa AND ($sqlItemsDrop) ";
        $query = $mysql->query($sql,$mysql->link);
        // ELIMINAR LAS CUENTAS COLGAAP
        $sql = "DELETE FROM items_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND ($sqlCuentasDrop) ";
        $query = $mysql->query($sql,$mysql->link);
        // ELIMINAR LAS CUENTAS NIIF
        $sql = "DELETE FROM items_cuentas_niif WHERE activo=1 AND id_empresa=$id_empresa AND ($sqlCuentasDrop) ";
        $query = $mysql->query($sql,$mysql->link);
        // ELIMINAR DE LAS BODEGAS
        $sql = "DELETE FROM inventario_totales WHERE activo=1 AND id_empresa=$id_empresa AND ($sqlBodegasDrop) ";
        $query = $mysql->query($sql,$mysql->link);
    }

?>
