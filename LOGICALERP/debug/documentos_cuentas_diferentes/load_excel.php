<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../../misc/excel/Classes/PHPExcel.php");
    echo "comentado por seguridad";
    exit;
    // $id_empresa     = $_SESSION['EMPRESA'];
    // $id_sucursal    = $_SESSION['SUCURSAL'];
    // $id_usuario     = $_SESSION['IDUSUARIO'];
    // $cc_usuario     = $_SESSION['CEDULAFUNCIONARIO'];
    // $nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

    // $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $objPHPExcel = PHPExcel_IOFactory::load("documentos.xls");
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    $contArray  = COUNT($arrayExcel);
    $contCol    = COUNT($arrayExcel[0]);

    // CONSULTA DE LAS SUCURSALES
    $sql="SELECT * FROM empresas_sucursales WHERE activo=1 AND (id_empresa=1 OR id_empresa=47)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $id_empresa  = $row['id_empresa'];
        $id_sucursal = $row['id'];
        $nombre      = $row['nombre'];
        $arraySucursales[$id_empresa][$nombre] = $id_sucursal;
    }

    // CUENTAS COLGAAP
    $sql="SELECT cuenta,cuenta_niif,id_empresa FROM puc WHERE activo=1 AND (id_empresa=1 OR id_empresa=47)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $cuenta     = $row['cuenta'];
        $id_empresa = $row['id_empresa'];
        $arrayPuc[$id_empresa][$cuenta]=$row['cuenta_niif'];
    }

    // CUENTAS NIIF
    $sql="SELECT id,cuenta,id_empresa FROM puc_niif WHERE activo=1 AND (id_empresa=1 OR id_empresa=47)";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $cuenta     = $row['cuenta'];
        $id_empresa = $row['id_empresa'];
        $arrayNiif[$id_empresa][$cuenta] = $row['id'];
    }

    foreach ($arrayExcel as $key => $documentos) {
        if($key==0 || $documentos[0]==''){ continue; }
        $id_empresa  = ($documentos[3]=='COLOMBIA')? 1 : 47 ;
        $id_sucursal = $arraySucursales[$id_empresa][$documentos[2]];
        $whereSql   .= ($whereSql=='')? " (consecutivo_documento='$documentos[0]' AND tipo_documento='$documentos[1]' AND id_sucursal=$id_sucursal) " :
                                     " OR (consecutivo_documento='$documentos[0]' AND tipo_documento='$documentos[1]' AND id_sucursal=$id_sucursal) ";
    }

    // CONSULTA DE LOS ASIENTOS
    echo$sql="SELECT * FROM asientos_colgaap WHERE activo=1 AND (id_empresa=1 OR id_empresa=47) AND ($whereSql); ";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $id_documento   = $row['id_documento'];
        $tipo_documento = $row['tipo_documento'];
        $arrayAsientos[$$id_documento][$tipo_documento] = array(
                                                                'id_documento'             => $row['id_documento'],
                                                                'consecutivo_documento'    => $row['consecutivo_documento'],
                                                                'tipo_documento'           => $row['tipo_documento'],
                                                                'tipo_documento_extendido' => $row['tipo_documento_extendido'],
                                                                'id_documento_cruce'       => $row['id_documento_cruce'],
                                                                'tipo_documento_cruce'     => $row['tipo_documento_cruce'],
                                                                'numero_documento_cruce'   => $row['numero_documento_cruce'],
                                                                'fecha'                    => $row['fecha'],
                                                                'debe'                     => $row['debe'],
                                                                'haber'                    => $row['haber'],
                                                                'id_cuenta'                => $row['id_cuenta'],
                                                                'codigo_cuenta'            => $row['codigo_cuenta'],
                                                                'cuenta'                   => $row['cuenta'],
                                                                'id_tercero'               => $row['id_tercero'],
                                                                'nit_tercero'              => $row['nit_tercero'],
                                                                'tercero'                  => $row['tercero'],
                                                                'id_sucursal'              => $row['id_sucursal'],
                                                                'sucursal'                 => $row['sucursal'],
                                                                'permiso_sucursal'         => $row['permiso_sucursal'],
                                                                'id_empresa'               => $row['id_empresa'],
                                                                'id_centro_costos'         => $row['id_centro_costos'],
                                                                'codigo_centro_costos'     => $row['codigo_centro_costos'],
                                                                'centro_costos'            => $row['centro_costos'],
                                                                'id_flujo_efectivo'        => $row['id_flujo_efectivo'],
                                                                'flujo_efectivo'           => $row['flujo_efectivo'],
                                                                'id_sucursal_cruce'        => $row['id_sucursal_cruce'],
                                                                'sucursal_cruce'           => $row['sucursal_cruce'],
                                                                'observacion'              => $row['observacion'],
                                                                );
        $insertAsientos .= "(
                                '$row[id_documento]',
                                '$row[consecutivo_documento]',
                                '$row[tipo_documento]',
                                '$row[tipo_documento_extendido]',
                                '$row[id_documento_cruce]',
                                '$row[tipo_documento_cruce]',
                                '$row[numero_documento_cruce]',
                                '$row[fecha]',
                                '$row[debe]',
                                '$row[haber]',
                                '$row[id_cuenta]',
                                '$row[codigo_cuenta]',
                                '$row[cuenta]',
                                '$row[id_tercero]',
                                '$row[nit_tercero]',
                                '$row[tercero]',
                                '$row[id_sucursal]',
                                '$row[sucursal]',
                                '$row[permiso_sucursal]',
                                '$row[id_empresa]',
                                '$row[id_centro_costos]',
                                '$row[codigo_centro_costos]',
                                '$row[centro_costos]',
                                '$row[id_flujo_efectivo]',
                                '$row[flujo_efectivo]',
                                '$row[id_sucursal_cruce]',
                                '$row[sucursal_cruce]',
                                'Cuentas Diferentes Insert'
                            ),";

    }

    // ACTUALIZAR LOS ASIENTOS NIIF
    echo$sql="UPDATE asientos_niif SET activo=0,observacion='Cuentas Diferentes Drop' WHERE activo=1 AND ($whereSql);";
    $query=$mysql->query($sql,$mysql->link);

    // INSERTAR LOS NUEVOS ASIENTOS
    $insertAsientos = substr($insertAsientos, 0, -1);
    echo$sql = "INSERT INTO asientos_niif
                (
                    id_documento,
                    consecutivo_documento,
                    tipo_documento,
                    tipo_documento_extendido,
                    id_documento_cruce,
                    tipo_documento_cruce,
                    numero_documento_cruce,
                    fecha,
                    debe,
                    haber,
                    id_cuenta,
                    codigo_cuenta,
                    cuenta,
                    id_tercero,
                    nit_tercero,
                    tercero,
                    id_sucursal,
                    sucursal,
                    permiso_sucursal,
                    id_empresa,
                    id_centro_costos,
                    codigo_centro_costos,
                    centro_costos,
                    id_flujo_efectivo,
                    flujo_efectivo,
                    id_sucursal_cruce,
                    sucursal_cruce,
                    observacion
                ) VALUES $insertAsientos";
    $query=$mysql->query($sql,$mysql->link);

    //$sql="SELECT * FROM asientos_niif WHERE activo=1 AND (id_empresa=1 OR id_empresa=47) AND ($whereSql); ";
    // $query=$mysql->query($sql,$mysql->link);

?>
