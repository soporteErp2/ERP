<?php
    // include_once('../../../misc/excel/Classes/PHPExcel.php');
    $id_empresa     = $_SESSION['EMPRESA'];
    $id_usuario     = $_SESSION['IDUSUARIO'];
    $cc_usuario     = $_SESSION['CEDULAFUNCIONARIO'];
    $nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];


    // $objPHPExcel = PHPExcel_IOFactory::load('plantilla_ajuste_2015_10_19.xls');
    $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    $contArray  = COUNT($arrayExcel);
    $contCol    = COUNT($arrayExcel[0]);



    // CREAR ARRAY CON LOS VALORES DE LOS CONCEPTOS RELACIONADOS A LAS CUENTAS
    // RECORRER LAS FILAS
    for ($i=0; $i <=$contArray ; $i++) {
        // RECORRER LAS COLUMNAS
        for ($j=0; $j <= $contCol; $j++) {

            // SI ES LA PRIMERA FILA, CONTIENE LA INFORMACION DE LAS CUENTAS
            if ($i==0) {
                // OBVIAR LAS DOS PRIMERAS COLUMNAS
                if ($j>0) {
                    @$arrayCuentas[$j]=split('_', $arrayExcel[$i][$j])[1];
                }
                continue;
            }

            // ARMAR EL ARRAY CON LA ESTRUCTURA DE LOS DOCUMENTOS DE LOS USUARIOS Y VALORES
            @$arrayValoresExcel[ preg_replace('/[^0-9]+/', '',trim($arrayExcel[$i][0]) )  ][ $arrayCuentas[$j] ] = ($arrayExcel[$i][$j]=='' || is_null($arrayExcel[$i][$j]) )? 0 : preg_replace('/[^0-9]+/', '',trim($arrayExcel[$i][$j]) );
        }
    }

    // print_r($arrayExcel);
    // echo $contArray;
    // exit;


    // $resulProcess = $arrayExcel[1][0];
    // $resulProcess = $arrayValoresExcel;
    // return $resulProcess;

    // ELIMINAR LOS DATOS DE LA PLANILLA DE AJUSTE, POR SI SE CARGO INFORMACION PREVIAMENTE
    $sql="DELETE FROM nomina_planillas_ajuste_empleados_conceptos WHERE
                activo=1 AND
                id_planilla='$id_planilla' AND
                id_empresa='$id_empresa'";
    $query = $mysql->query($sql,$mysql->link);

    $sql="DELETE FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND
                    id_planilla = '$id_planilla' AND
                    id_empresa = '$id_empresa'";
    $query = $mysql->query($sql,$mysql->link);

    // CONSULTAR LAS FECHAS DE LA PLANILLA DE AJUSTE
    $sql   = "SELECT fecha_inicio,fecha_final,id_sucursal,estado FROM nomina_planillas_ajuste WHERE activo=1 ANd id_empresa=$id_empresa AND id=$id_planilla";
    $query = $mysql->query($sql,$mysql->link);

    $fecha_inicio = $mysql->result($query,0,'fecha_inicio');
    $fecha_final  = $mysql->result($query,0,'fecha_final');
    $id_sucursal  = $mysql->result($query,0,'id_sucursal');
    $estado       = $mysql->result($query,0,'estado');
    if ($estado<>0) {
        $errorLoadFile = "Aviso! el estado del documento no permite generar el proceso"; return array('error'=> $errorLoadFile, 'debug'=> "$debugError");
    }

    // CONSULTAR LAS PLANILLAS DE NOMINA DE ESE PERIODO CON SUS EMPLEADOS
    $sql   = "SELECT
                    NP.id,
                    NPE.documento_empleado,
                    NPE.nombre_empleado,
                    NPE.id_empleado,
                    NPE.id_contrato
                FROM
                    nomina_planillas AS NP,
                    nomina_planillas_empleados AS NPE
                WHERE
                NP.activo = 1
                AND (NP.estado=1 OR NP.estado=2)
                AND NP.id_empresa = $id_empresa
                AND NP.id_sucursal = $id_sucursal
                AND NP.fecha_inicio >= '$fecha_inicio'
                AND NP.fecha_final <= '$fecha_final'
                AND NPE.id_planilla = NP.id
                GROUP BY NP.id,NPE.id_empleado
                ORDER BY NPE.documento_empleado ASC";
    $query = $mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $array_id_planillas[$row['id']] = $row['id'];
        $whereIdEmpleados .= ($whereIdEmpleados=='')? 'id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
        $whereIdContratos .= ($whereIdContratos=='')? 'id='.$row['id_contrato'] : ' OR id='.$row['id_contrato'] ;
        $array_empleados[$row['id_empleado']] = array('documento_empleado' => $row['documento_empleado'], 'nombre_empleado'=>$row['nombre_empleado']);
    }

    //CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
    $sql="SELECT id,
                id_empleado,
                tipo_documento_empleado,
                documento_empleado,
                nombre_empleado,
                numero_contrato,
                salario_basico,
                fecha_inicio_nomina,
                id_grupo_trabajo,
                valor_nivel_riesgo_laboral,
                id_sucursal
            FROM empleados_contratos
            WHERE activo=1
            AND id_empresa=$id_empresa
            AND documento_empleado <> ''
            AND ($whereIdContratos)
            AND ($whereIdEmpleados)
            ";
    $query = $mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $whereId_grupo_trabajo .= ($whereId_grupo_trabajo=='')? ' id_grupo_trabajo='.$row['id_grupo_trabajo'] : ' OR id_grupo_trabajo='.$row['id_grupo_trabajo'] ;
        $arrayEmpleados[$row['id_empleado']]         = $row['id_grupo_trabajo'];
        $arrayEmpleadosValores[$row['id_empleado']]  = array(   'salario_basico'             => $row['salario_basico'],
                                                                'id_contrato'                => $row['id'],
                                                                'documento_empleado'         => $row['documento_empleado'],
                                                                'id_sucursal'                => $row['id_sucursal'],
                                                                'valor_nivel_riesgo_laboral' => $row['valor_nivel_riesgo_laboral']);
        $valueInsertEmpleados.="('$id_planilla',
                                 '$row[id_empleado]',
                                 '$row[tipo_documento_empleado]',
                                 '$row[documento_empleado]',
                                 '$row[nombre_empleado]',
                                 '$row[id]',
                                 '',
                                 'No',
                                 '',
                                 '$id_empresa'
                                 ),";
    }

    $valueInsertEmpleados = substr($valueInsertEmpleados, 0, -1);
    $sql="INSERT INTO nomina_planillas_ajuste_empleados (id_planilla,id_empleado,tipo_documento,documento_empleado,nombre_empleado,id_contrato,dias_laborados,terminar_contrato,id_sucursal,id_empresa)
                VALUES $valueInsertEmpleados ";
        $query=$mysql->query($sql,$mysql->link);

    $whereIdPlanillas='';
    // CREAR WHERE DEL ID PLANILLA
    foreach ($array_id_planillas as $id_planilla_id => $valor) {
        $whereIdPlanillas .= ($whereIdPlanillas=='')? 'id_planilla='.$id_planilla_id : ' OR id_planilla='.$id_planilla_id ;
    }

    $sql="SELECT
            NPEC.id_concepto,
            NPEC.codigo_concepto,
            NPEC.concepto,
            NPEC.id_empleado,
            SUM(NPEC.valor_concepto) AS valor_provisionado,
            NPEC.naturaleza,
            NPEC.id_prestamo,
            NC.concepto_ajustable,
            NC.id,
            NC.codigo,
            NC.descripcion,
            NC.formula_liquidacion,
            NC.nivel_formula_liquidacion,
            NC.tipo_concepto,
            NC.id_cuenta_colgaap,
            NC.cuenta_colgaap,
            NC.descripcion_cuenta_colgaap,
            NC.id_cuenta_niif,
            NC.cuenta_niif,
            NC.descripcion_cuenta_niif,
            NC.caracter,
            NC.centro_costos,
            NC.id_cuenta_contrapartida_colgaap,
            NC.cuenta_contrapartida_colgaap,
            NC.descripcion_cuenta_contrapartida_colgaap,
            NC.id_cuenta_contrapartida_niif,
            NC.cuenta_contrapartida_niif,
            NC.descripcion_cuenta_contrapartida_niif,
            NC.caracter_contrapartida,
            NC.centro_costos_contrapartida,
            NC.naturaleza,
            NC.imprimir_volante,
            NC.id_cuenta_colgaap_ajuste,
            NC.cuenta_colgaap_ajuste,
            NC.descripcion_cuenta_colgaap_ajuste,
            NC.id_cuenta_niif_ajuste,
            NC.cuenta_niif_ajuste,
            NC.centro_costos_ajuste,
            NC.descripcion_cuenta_niif_ajuste
        FROM
            nomina_planillas_empleados_conceptos AS NPEC,
            nomina_conceptos AS NC
        WHERE
            NPEC.activo = 1
        AND NPEC.id_empresa = $id_empresa
        AND NC.concepto_ajustable = 'true'
        AND NC.id=NPEC.id_concepto
        AND ($whereIdPlanillas)
        AND ($whereIdEmpleados)
        GROUP BY
            NPEC.id_concepto,NPEC.id_empleado";
    $query = $mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $id=$row['id_concepto'];
        $arrayConceptos[$id] = array(
                                        'codigo'                                   => $row['codigo'],
                                        'concepto'                                 => $row['descripcion'],
                                        'formula'                                  => $row['formula_liquidacion'],
                                        'formula_original'                         => $row['formula_liquidacion'],
                                        'nivel_formula'                            => $row['nivel_formula_liquidacion'],
                                        'valor_concepto'                           => $row['valor_provisionado'],
                                        'insert'                                   => 'false',
                                        'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
                                        'cuenta_colgaap'                           => $row['cuenta_colgaap'],
                                        'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
                                        'id_cuenta_niif'                           => $row['id_cuenta_niif'],
                                        'cuenta_niif'                              => $row['cuenta_niif'],
                                        'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
                                        'caracter'                                 => $row['caracter'],
                                        'centro_costos'                            => $row['centro_costos'],
                                        'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
                                        'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
                                        'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
                                        'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
                                        'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
                                        'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
                                        'caracter_contrapartida'                   => $row['caracter_contrapartida'],
                                        'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
                                        'naturaleza'                               => $row['naturaleza'],
                                        'imprimir_volante'                         => $row['imprimir_volante'],
                                        'id_cuenta_colgaap_ajuste'                 => $row['id_cuenta_colgaap_ajuste'],
                                        'cuenta_colgaap_ajuste'                    => $row['cuenta_colgaap_ajuste'],
                                        'descripcion_cuenta_colgaap_ajuste'        => $row['descripcion_cuenta_colgaap_ajuste'],
                                        'id_cuenta_niif_ajuste'                    => $row['id_cuenta_niif_ajuste'],
                                        'cuenta_niif_ajuste'                       => $row['cuenta_niif_ajuste'],
                                        'descripcion_cuenta_niif_ajuste'           => $row['descripcion_cuenta_niif_ajuste'],
                                        'centro_costos_ajuste'                     => $row['centro_costos_ajuste'],
                                        'saldo_dias_laborados'                     => '',
                                    );

        $arrayEmpleadosValores[$row['id_empleado']][$row['id_concepto']]['valor_provisionado']+=$row['valor_provisionado'];
        $arrayEmpleadosValores[$row['id_empleado']][$row['id_concepto']]['insert']='true';

        // ARRAY CON LOS VALORES DEL EMPLEADO PARA QUE EL SISTEMA AJUSTE DE FORMA AUTOAMTICA AL 100
        if ($row['caracter']=='credito') {
            if (empty($arrayValores[$row['id_empleado']] ) ){
                $arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]=array( 'valor' => $row['valor_provisionado'], 'cont' =>1 , 'concepto' => $row['concepto']);
            }
            else{
                $arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]['valor']+=$row['valor_provisionado'];
                $arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]['cont']++;
                $arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]['concepto'].=' - '.$row['concepto'];
            }
        }
        else if ($row['caracter_contrapartida']=='credito') {
            if (empty($arrayValores[$row['id_empleado']] ) ){
                $arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]=array( 'valor' => $row['valor_provisionado'], 'cont' =>1, 'concepto' => $row['concepto'] );
            }
            else{
                $arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]['valor']+=$row['valor_provisionado'];
                $arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]['cont']++;
                $arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]['concepto'].=' - '.$row['concepto'];
            }
        }

    }

    // $resulProcess = $arrayValores;
    // return $resulProcess;

    // CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
    $sql="SELECT id_concepto,
                nivel_formula_liquidacion,
                formula_liquidacion,
                id_cuenta_colgaap,
                cuenta_colgaap,
                descripcion_cuenta_colgaap,
                id_cuenta_niif,
                cuenta_niif,
                descripcion_cuenta_niif,
                caracter,
                centro_costos,
                id_cuenta_contrapartida_colgaap,
                cuenta_contrapartida_colgaap,
                descripcion_cuenta_contrapartida_colgaap,
                id_cuenta_contrapartida_niif,
                cuenta_contrapartida_niif,
                descripcion_cuenta_contrapartida_niif,
                caracter_contrapartida,
                centro_costos_contrapartida,
                id_grupo_trabajo,
                id_cuenta_colgaap_ajuste,
                cuenta_colgaap_ajuste,
                descripcion_cuenta_colgaap_ajuste,
                id_cuenta_niif_ajuste,
                cuenta_niif_ajuste,
                descripcion_cuenta_niif_ajuste,
                centro_costos_ajuste
                FROM nomina_conceptos_grupos_trabajo
                WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId_grupo_trabajo)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $id               = $row['id_concepto'];
        $id_grupo_trabajo = $row['id_grupo_trabajo'];
        $arrayGruposTrabajo[$id_grupo_trabajo][$id]=array(
                                                        'formula'                                  => $row['formula_liquidacion'],
                                                        'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
                                                        'cuenta_colgaap'                           => $row['cuenta_colgaap'],
                                                        'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
                                                        'id_cuenta_niif'                           => $row['id_cuenta_niif'],
                                                        'cuenta_niif'                              => $row['cuenta_niif'],
                                                        'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
                                                        'caracter'                                 => $row['caracter'],
                                                        'centro_costos'                            => $row['centro_costos'],
                                                        'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
                                                        'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
                                                        'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
                                                        'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
                                                        'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
                                                        'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
                                                        'caracter_contrapartida'                   => $row['caracter_contrapartida'],
                                                        'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
                                                        'id_cuenta_colgaap_ajuste'                 => $row['id_cuenta_colgaap_ajuste'],
                                                        'cuenta_colgaap_ajuste'                    => $row['cuenta_colgaap_ajuste'],
                                                        'descripcion_cuenta_colgaap_ajuste'        => $row['descripcion_cuenta_colgaap_ajuste'],
                                                        'id_cuenta_niif_ajuste'                    => $row['id_cuenta_niif_ajuste'],
                                                        'cuenta_niif_ajuste'                       => $row['cuenta_niif_ajuste'],
                                                        'descripcion_cuenta_niif_ajuste'           => $row['descripcion_cuenta_niif_ajuste'],
                                                        'centro_costos_ajuste'                     => $row['centro_costos_ajuste'],
                                                        );
    }

    foreach ($arrayEmpleados as $id_empleado => $id_grupo_trabajo) {
        //CREAR ARRAY TEMPORAL PARA GUARDAR TODOS LOS CONCEPTOS DE CARGA AUTOMATICA
        $arrayTempConceptos=$arrayConceptos;
        // RECORRER EL ARRAY TEMPORAL PARA REEMPLAZAR LOS VALORES DE CUENTAS Y FORMULAS POR EL QUE CORRESPONDE AL GRUPO DE TRABAJO, SI EL GRUPO DE TRABAJO NO TIENE NADA CONFIGURADO, ENTONCES SE PONE LA CONFIGURACION INICIAL DEL CONCEPTO
        foreach ($arrayTempConceptos as $id_concepto => $arrayTempConceptosResul) {
                // SI LOS VALORES DEL ARRAY EN LOS INDICES NO EXISTEN, ENTONCES SE HACE CONTINUE PARA QUE NO ELIMINE LOS VALORES ANTERIORES DEL ARRAY
                if (!isset($arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto])) {
                    continue;
                }

                $formula=$arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['formula'];

                // REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
                $arrayTempConceptos[$id_concepto]['formula']                                  = ($formula=='')? $arrayTempConceptos[$id_concepto]['formula'] : $formula;
                $arrayTempConceptos[$id_concepto]['formula_original']                         = ($formula=='')? $arrayTempConceptos[$id_concepto]['formula_original'] : $formula;
                $arrayTempConceptos[$id_concepto]['id_cuenta_colgaap']                        = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_colgaap'];
                $arrayTempConceptos[$id_concepto]['cuenta_colgaap']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_colgaap'];
                $arrayTempConceptos[$id_concepto]['descripcion_cuenta_colgaap']               = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_colgaap'];
                $arrayTempConceptos[$id_concepto]['id_cuenta_niif']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_niif'];
                $arrayTempConceptos[$id_concepto]['cuenta_niif']                              = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_niif'];
                $arrayTempConceptos[$id_concepto]['descripcion_cuenta_niif']                  = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_niif'];
                $arrayTempConceptos[$id_concepto]['caracter']                                 = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['caracter'];
                $arrayTempConceptos[$id_concepto]['centro_costos']                            = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos'];
                $arrayTempConceptos[$id_concepto]['id_cuenta_contrapartida_colgaap']          = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_contrapartida_colgaap'];
                $arrayTempConceptos[$id_concepto]['cuenta_contrapartida_colgaap']             = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_contrapartida_colgaap'];
                $arrayTempConceptos[$id_concepto]['descripcion_cuenta_contrapartida_colgaap'] = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'];
                $arrayTempConceptos[$id_concepto]['id_cuenta_contrapartida_niif']             = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_contrapartida_niif'];
                $arrayTempConceptos[$id_concepto]['cuenta_contrapartida_niif']                = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_contrapartida_niif'];
                $arrayTempConceptos[$id_concepto]['descripcion_cuenta_contrapartida_niif']    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_contrapartida_niif'];
                $arrayTempConceptos[$id_concepto]['caracter_contrapartida']                   = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['caracter_contrapartida'];
                $arrayTempConceptos[$id_concepto]['centro_costos_contrapartida']              = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos_contrapartida'];
                $arrayTempConceptos[$id_concepto]['id_cuenta_colgaap_ajuste']                 = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_colgaap_ajuste'];
                $arrayTempConceptos[$id_concepto]['cuenta_colgaap_ajuste']                    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_colgaap_ajuste'];
                $arrayTempConceptos[$id_concepto]['descripcion_cuenta_colgaap_ajuste']        = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_colgaap_ajuste'];
                $arrayTempConceptos[$id_concepto]['id_cuenta_niif_ajuste']                    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_niif_ajuste'];
                $arrayTempConceptos[$id_concepto]['cuenta_niif_ajuste']                       = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_niif_ajuste'];
                $arrayTempConceptos[$id_concepto]['descripcion_cuenta_niif_ajuste']           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_niif_ajuste'];
                $arrayTempConceptos[$id_concepto]['centro_costos_ajuste']                     = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos_ajuste'];
        }

        // }
        // ASIGNAR EL VALOR DEL ARRAY TEMPORAL AL ARRAY FINAL DEL EMPLEADO
        $arrayEmpleadosConceptos[$id_empleado]=$arrayTempConceptos;
    }

    $cont_registros = 0;

    // PRIMER CAPA, EMPLEADOS
    foreach ($arrayEmpleadosConceptos as $id_empleado => $arrayEmpleadosConceptosArray) {
        // RECORRER LOS CONCEPTOS
        foreach ($arrayEmpleadosConceptosArray as $id_concepto => $arrayConceptosResul) {
            // CONDICION PARA INSERTAR SOLO LOS CONCEPTOS QUE POSEE CADA EMPLEADO Y NO TODOS
            if($arrayEmpleadosValores[$id_empleado][$id_concepto]['insert']<>'true'){continue;}

            // SI LOS REGISTROS SON MUCHOS, FRACCIONAR EL INSERT
            if ($cont_registros>=1000) {
                $valueInsert = substr($valueInsert, 0, -1);
                $sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos
                        (id_planilla,
                        id_empleado,
                        id_contrato,
                        id_concepto,
                        codigo_concepto,
                        concepto,
                        valor_concepto,
                        valor_concepto_ajustado,
                        saldo_restante,
                        formula,
                        formula_original,
                        nivel_formula,
                        id_sucursal,
                        id_empresa,
                        id_cuenta_colgaap,
                        cuenta_colgaap,
                        descripcion_cuenta_colgaap,
                        id_cuenta_niif,
                        cuenta_niif,
                        descripcion_cuenta_niif,
                        caracter,
                        centro_costos,
                        id_cuenta_contrapartida_colgaap,
                        cuenta_contrapartida_colgaap,
                        descripcion_cuenta_contrapartida_colgaap,
                        id_cuenta_contrapartida_niif,
                        cuenta_contrapartida_niif,
                        descripcion_cuenta_contrapartida_niif,
                        caracter_contrapartida,
                        centro_costos_contrapartida,
                        id_cuenta_colgaap_ajuste,
                        cuenta_colgaap_ajuste,
                        descripcion_cuenta_colgaap_ajuste,
                        id_cuenta_niif_ajuste,
                        cuenta_niif_ajuste,
                        descripcion_cuenta_niif_ajuste,
                        centro_costos_ajuste,
                        naturaleza,
                        imprimir_volante,
                        dias_laborados,
                        id_prestamo)
                        VALUES $valueInsert";
                $query=$mysql->query($sql,$mysql->link);
                $valueInsert='';
            }

            if ($arrayConceptosResul['caracter']=='credito') {
             // $valor_aproximado = aproximar($arrayValores[$id_empleado][$arrayConceptosResul['cuenta_colgaap']]['valor'],3);
             // $valor_concepto_ajustado = $valor_aproximado/$arrayValores[$id_empleado][$arrayConceptosResul['cuenta_colgaap']]['cont'];
                $valor_concepto_ajustado = $arrayValoresExcel[$array_empleados[$id_empleado]['documento_empleado'] ] [$arrayConceptosResul['cuenta_colgaap']]/$arrayValores[$id_empleado][$arrayConceptosResul['cuenta_colgaap']]['cont'];
                // $valor_concepto_ajustado = ($valor_concepto_ajustado==0)? $arrayEmpleadosValores[$id_empleado][$id_concepto]['valor_provisionado'] : $valor_concepto_ajustado ;
            }
            else if ($arrayConceptosResul['caracter_contrapartida']=='credito') {
             // $valor_aproximado = aproximar($arrayValores[$id_empleado][$arrayConceptosResul['cuenta_contrapartida_colgaap']]['valor'],3);
             // $valor_concepto_ajustado = $valor_aproximado/$arrayValores[$id_empleado][$arrayConceptosResul['cuenta_contrapartida_colgaap']]['cont'];
                $valor_concepto_ajustado = $arrayValoresExcel[$array_empleados[$id_empleado]['documento_empleado'] ] [$arrayConceptosResul['cuenta_contrapartida_colgaap']]/$arrayValores[$id_empleado][$arrayConceptosResul['cuenta_contrapartida_colgaap']]['cont'];
                // $valor_concepto_ajustado = ($valor_concepto_ajustado==0)? $arrayEmpleadosValores[$id_empleado][$id_concepto]['valor_provisionado'] : $valor_concepto_ajustado ;
            }

            $valueInsert.="('$id_planilla',
                            '$id_empleado',
                            '".$arrayEmpleadosValores[$id_empleado]['id_contrato']."',
                            '$id_concepto',
                            '".$arrayConceptosResul['codigo']."',
                            '".$arrayConceptosResul['concepto']."',
                            '".$arrayEmpleadosValores[$id_empleado][$id_concepto]['valor_provisionado']."',
                            '".$valor_concepto_ajustado."',
                            '".$valor_concepto_ajustado."',
                            '".$arrayConceptosResul['formula']."',
                            '".$arrayConceptosResul['formula_original']."',
                            '".$nivel_formula."',
                            '$id_sucursal',
                            '$id_empresa',
                            '".$arrayConceptosResul['id_cuenta_colgaap']."',
                            '".$arrayConceptosResul['cuenta_colgaap']."',
                            '".$arrayConceptosResul['descripcion_cuenta_colgaap']."',
                            '".$arrayConceptosResul['id_cuenta_niif']."',
                            '".$arrayConceptosResul['cuenta_niif']."',
                            '".$arrayConceptosResul['descripcion_cuenta_niif']."',
                            '".$arrayConceptosResul['caracter']."',
                            '".$arrayConceptosResul['centro_costos']."',
                            '".$arrayConceptosResul['id_cuenta_contrapartida_colgaap']."',
                            '".$arrayConceptosResul['cuenta_contrapartida_colgaap']."',
                            '".$arrayConceptosResul['descripcion_cuenta_contrapartida_colgaap']."',
                            '".$arrayConceptosResul['id_cuenta_contrapartida_niif']."',
                            '".$arrayConceptosResul['cuenta_contrapartida_niif']."',
                            '".$arrayConceptosResul['descripcion_cuenta_contrapartida_niif']."',
                            '".$arrayConceptosResul['caracter_contrapartida']."',
                            '".$arrayConceptosResul['centro_costos_contrapartida']."',
                            '".$arrayConceptosResul['id_cuenta_colgaap_ajuste']."',
                            '".$arrayConceptosResul['cuenta_colgaap_ajuste']."',
                            '".$arrayConceptosResul['descripcion_cuenta_colgaap_ajuste']."',
                            '".$arrayConceptosResul['id_cuenta_niif_ajuste']."',
                            '".$arrayConceptosResul['cuenta_niif_ajuste']."',
                            '".$arrayConceptosResul['descripcion_cuenta_niif_ajuste']."',
                            '".$arrayConceptosResul['centro_costos_ajuste']."',
                            '".$arrayConceptosResul['naturaleza']."',
                            '".$arrayConceptosResul['imprimir_volante']."',
                            '".$arrayConceptosAcumuladosResul['saldo_dias_laborados']."',
                            '$id_prestamo'
                            ),";

            $cont_registros++;

        }
    }

    if ($valueInsert<>'') {

        $valueInsert = substr($valueInsert, 0, -1);
        $sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos
                (id_planilla,
                id_empleado,
                id_contrato,
                id_concepto,
                codigo_concepto,
                concepto,
                valor_concepto,
                valor_concepto_ajustado,
                saldo_restante,
                formula,
                formula_original,
                nivel_formula,
                id_sucursal,
                id_empresa,
                id_cuenta_colgaap,
                cuenta_colgaap,
                descripcion_cuenta_colgaap,
                id_cuenta_niif,
                cuenta_niif,
                descripcion_cuenta_niif,
                caracter,
                centro_costos,
                id_cuenta_contrapartida_colgaap,
                cuenta_contrapartida_colgaap,
                descripcion_cuenta_contrapartida_colgaap,
                id_cuenta_contrapartida_niif,
                cuenta_contrapartida_niif,
                descripcion_cuenta_contrapartida_niif,
                caracter_contrapartida,
                centro_costos_contrapartida,
                id_cuenta_colgaap_ajuste,
                cuenta_colgaap_ajuste,
                descripcion_cuenta_colgaap_ajuste,
                id_cuenta_niif_ajuste,
                cuenta_niif_ajuste,
                descripcion_cuenta_niif_ajuste,
                centro_costos_ajuste,
                naturaleza,
                imprimir_volante,
                dias_laborados,
                id_prestamo)
                VALUES $valueInsert";
        $query=$mysql->query($sql,$mysql->link);

    }

    $resulProcess = $arrayValoresExcel;

?>
