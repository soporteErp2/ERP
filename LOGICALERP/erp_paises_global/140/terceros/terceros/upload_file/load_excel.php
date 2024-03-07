<?php
    include_once('../../../../../misc/excel/Classes/PHPExcel.php');

    $id_empresa     = $_SESSION['EMPRESA'];
    $id_sucursal    = $_SESSION['SUCURSAL'];
    $id_usuario     = $_SESSION['IDUSUARIO'];
    $cc_usuario     = $_SESSION['CEDULAFUNCIONARIO'];
    $nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

    $objPHPExcel = PHPExcel_IOFactory::load($uploadDirectory.$filename.'.'.$ext);
    $arrayExcel  = $objPHPExcel->getActiveSheet()->toArray(null,true,false,false);

    $contArray  = COUNT($arrayExcel);
    $contCol    = COUNT($arrayExcel[0]);

    if($contCol > 50){ return array("error"=> "El archivo excel solo puede tener contenido en las columnas A, B, C, y D", "debug"=> "$debugError"); }
    // else{ return array("error"=> "No se puede leer el archivo Excel", "debug"=> "$debugError"); }

    /*======================================= TABLA COLGAAP =======================================
        @$idPais => Campo Id de la tabla Pais
        @$idDepartamento => Campo Id de la tabla departamento
        @$idCiudad => Campo Id de la tabla Ciudad

        @$arrayTipoNit => Tipo documento, campo codigo tabla tipo_documento
        @$arrayRegimen => Tipo regimen, campo codigo tabla terceros_tributario
    */

    //===========================// QUERY UBICACION EMPRESA  //===========================//
    //*************************************************************************************/
    $sqlUbicacion    = "SELECT id_ciudad FROM empresas WHERE id='$id_empresa' LIMIT 0,1";
    $queryUbicacion  = mysql_query($sqlUbicacion);
    $idCiudadEmpresa = mysql_result($queryUbicacion, 0, 'id_ciudad');

    //===========================// QUERY TIPO DOCUMENTO  //===========================//
    //**********************************************************************************/
    $arrayTipoNit = array();
    $sqlTipoNit   = "SELECT id,codigo,tipo FROM tipo_documento WHERE id_empresa='$id_empresa' AND activo=1";
    $queryTipoNit = mysql_query($sqlTipoNit);
    while ($rowTipoNit = mysql_fetch_assoc($queryTipoNit)) {
        $id     = $rowTipoNit['id'];
        $codigo = $rowTipoNit['codigo'];
        $tipo   = $rowTipoNit['tipo'];

        $arrayTipoNit[$codigo]['id']   = $id;
        $arrayTipoNit[$codigo]['tipo'] = $tipo;
    }

    //===========================// QUERY TIPO REGIMEN  //===========================//
    //********************************************************************************/
    $arrayRegimen = array();
    $sqlTipoNit   = "SELECT id,codigo FROM terceros_tributario WHERE id_pais='$_SESSION[PAIS]' AND activo=1";
    $queryTipoNit = mysql_query($sqlTipoNit);
    while ($rowTipoNit = mysql_fetch_assoc($queryTipoNit)) {
        $codigo = $rowTipoNit['codigo'];

        $arrayRegimen[$codigo] = $rowTipoNit['id'];
    }

    //REPLACE EN UNA MATRIZ
    // $arrayExcel = str_replace_json($arrayExcel);

    // return array("error"=> json_encode($arrayTerceros), "debug"=> "$debugError");

    // function str_replace_json($subject){
    //     return json_decode(str_replace("pppppppp", "'", json_encode($subject)),true);
    // }
    // return array("error"=> json_encode($arrayTerceros), "debug"=> "$debugError");

    $contTercero = 0;
    for ($contFila=1; $contFila <= $contArray; $contFila++) {

        //TIPO NIT
        $tipoNit       = ($arrayExcel[$contFila][0]*1 > 0)? $arrayExcel[$contFila][0]: 0;
        $idTipoNit     = $arrayTipoNit[$tipoNit]['id'];
        $estadoTipoNit = $arrayTipoNit[$tipoNit]['tipo'];

        $idCiudadNit = ($arrayExcel[$contFila][1] > 0)? $arrayExcel[$contFila][1]: 0;
        $arrayUbicacion[$idCiudadNit]['estado'] = 'no';

        $nit = $arrayExcel[$contFila][2];
        $dv  = $arrayExcel[$contFila][3];

        $razonSocial     = addslashes(strtoupper($arrayExcel[$contFila][4]));
        $nombreComercial = addslashes(strtoupper($arrayExcel[$contFila][5]));

        if($nit == '' && $razonSocial='' && $nombreComercial='') continue;

        $nombre1   = addslashes(strtoupper($arrayExcel[$contFila][6]));
        $nombre2   = addslashes(strtoupper($arrayExcel[$contFila][7]));
        $apellido1 = addslashes(strtoupper($arrayExcel[$contFila][8]));
        $apellido2 = addslashes(strtoupper($arrayExcel[$contFila][9]));

        $cliente   = (ucfirst(strtolower($arrayExcel[$contFila][10])) != 'No')? 'Si': 'No';
        $proveedor = (ucfirst(strtolower($arrayExcel[$contFila][11])) != 'No')? 'Si': 'No';

        //REGIMEN
        $regimen   = $arrayExcel[$contFila][12];
        $idRegimen = $arrayRegimen[$regimen];

        $exento_iva = (ucfirst(strtolower($arrayExcel[$contFila][13])) != 'Si')? 'No': 'Si';

        //UBICACION
        $idCiudad = ($arrayExcel[$contFila][14] > 0)? $arrayExcel[$contFila][14]: 0;
        $arrayUbicacion[$idCiudad]['estado'] = 'no';

        $direccion  = addslashes($arrayExcel[$contFila][15]);
        $telefono1  = addslashes($arrayExcel[$contFila][16]);
        $telefono2  = addslashes($arrayExcel[$contFila][17]);
        $celular1   = addslashes($arrayExcel[$contFila][18]);
        $celular2   = addslashes($arrayExcel[$contFila][19]);
        $email      = addslashes($arrayExcel[$contFila][20]);
        $pagina_web = addslashes($arrayExcel[$contFila][21]);

        //EVITA NIT REPETIDO
        if(@$arrayNitRepetido["$nit"] == 'true') continue;
        $arrayNitRepetido["$nit"] = 'true';

        $contTercero++;
        $estado = 'si';

        //VALIDACIONES
        $msjError = '';
        if($nit == ''){ $estado = 'no'; $msjError .= '<br/>* # IDENTIFICACION DEL TERCERO'; }       //VALIDACION NIT
        if($idTipoNit == 0){ $estado = 'no'; $msjError .= '<br/>* TIPO DE IDENTIFICACION DEL TERCERO'; }        //VALIDACION TIPO NIT
        if($direccion == ''){ $estado = 'no'; $msjError .= '<br/>* DIRECCION'; }       //VALIDACION DIRECCION
        if($estadoTipoNit=='Persona' && $nombre1==''){ $estado = 'no'; $msjError .= '<br/>* NOMBRE1'; }         //VALIDACION PRIMER NOMBRE CUANDO ES PERSONA
        if($estadoTipoNit=='Persona' && $apellido1==''){ $estado = 'no'; $msjError .= '<br/>* APELLIDO1'; }     //VALIDACION PRIMER APELLIDO CUANDO ES PERSONA
        if($idRegimen == 0 || $idRegimen == ''){ $estado = 'no'; $msjError .= '<br/>* TIPO DE REGIMEN'; }       //VALIDACION PRIMER APELLIDO CUANDO ES PERSONA

        $arrayTerceros[$contTercero] = array(
                                            'idTipoNit'       => $idTipoNit,
                                            'estadoTipoNit'   => $estadoTipoNit,
                                            'nit'             => $nit,
                                            'dv'              => $dv,
                                            'idCiudadNit'     => $idCiudadNit,
                                            'razonSocial'     => $razonSocial,
                                            'nombreComercial' => $nombreComercial,
                                            'nombre1'         => $nombre1,
                                            'nombre2'         => $nombre2,
                                            'apellido1'       => $apellido1,
                                            'apellido2'       => $apellido2,
                                            'cliente'         => $cliente,
                                            'proveedor'       => $proveedor,
                                            'idRegimen'       => $idRegimen,
                                            'exentoIva'       => $exento_iva,
                                            'idCiudad'        => $idCiudad,
                                            'direccion'       => $direccion,
                                            'telefono1'       => $telefono1,
                                            'telefono2'       => $telefono2,
                                            'celular1'        => $celular1,
                                            'celular2'        => $celular2,
                                            'email'           => $email,
                                            'pagina_web'      => $pagina_web,
                                            'msjError'        => $msjError,
                                            'estado'          => $estado
                                            );

        $valueSelectTercero .= " OR numero_identificacion='$nit'";
        $valueSelectCiudad  .= " OR id='$idCiudad' OR id='$idCiudadNit'";
    }

    //===========================// QUERY UBICACION  //===========================//
    //*****************************************************************************/
    $valueSelectCiudad = substr($valueSelectCiudad, 3);

    $sqlCiudad   = "SELECT id AS id_ciudad,ciudad,id_pais,id_departamento FROM ubicacion_ciudad WHERE ($valueSelectCiudad)";
    $queryCiudad = mysql_query($sqlCiudad);
    while ($rowCiudad = mysql_fetch_assoc($queryCiudad)) {
        $idPais         = $rowCiudad['id_pais'];
        $idDepartamento = $rowCiudad['id_departamento'];
        $ciudad         = $rowCiudad['ciudad'];
        $idCiudad       = $rowCiudad['id_ciudad'];

        $arrayUbicacion[$idCiudad]['estado']  = 'si';
        $arrayUbicacion[$idCiudad]['ciudad']  = $ciudad;
        $arrayUbicacion[$idCiudad]['id_pais'] = $idPais;
        $arrayUbicacion[$idCiudad]['id_departamento'] = $idDepartamento;
    }

    //===========================// QUERY TERCEROS YA EN BASE DE DATOS  //===========================//
    //************************************************************************************************/
    $valueSelectTercero = substr($valueSelectTercero, 3);

    $sqlTerceros   = "SELECT numero_identificacion FROM terceros WHERE id_empresa='$id_empresa' AND activo=1 AND tercero = 1 AND ($valueSelectTercero) GROUP BY numero_identificacion";
    $queryTerceros = mysql_query($sqlTerceros);
    while ($rowTercero = mysql_fetch_assoc($queryTerceros)) {
        $nit = $rowTercero['numero_identificacion'];
        $arrayTercerosBd["$nit"] = "true";
    }

    if($contArray == 0){ return array("error"=> "No se encontraron filas con informacion en el archivo excel!", "debug"=> "$debugError"); }

    //===========================// QUERY INSERT UPLOAD  //===========================//
    /**********************************************************************************/
    $random = $this->randomico_maestro(); // ID UNICO

    $sqlTercerosUpload   = "INSERT INTO terceros_upload(random,id_usuario,usuario,fecha,hora,nombre_archivo,id_empresa)
                            VALUES('$random','$id_usuario','$nombre_usuario',NOW(),NOW(),'".$filename.".".$ext."','$id_empresa')";
    $queryTercerosUpload = mysql_query($sqlTercerosUpload);

    $sqlUpload   = "SELECT id FROM terceros_upload WHERE random='$random' AND id_empresa='$id_empresa' LIMIT 0,1";
    $queryUpload = mysql_query($sqlUpload);
    $idUpload    = mysql_result($queryUpload, 0, 'id');


    //=========================// TABLA TERCERO //=========================//
    /***********************************************************************/
    $contRepetido     = 0;
    $valueTercero     = "";
    $contTerceroFail  = 0;
    $contTerceroTrue  = 0;
    $valueTerceroTrue = "";

    $fila = 1;
    for ($i = 1; $i < $contTercero; $i++) {
        $fila++;
        $nit             = $arrayTerceros[$i]['nit'];
        $dv              = $arrayTerceros[$i]['dv'];
        $idCiudadNit     = $arrayTerceros[$i]['idCiudadNit'];
        $razonSocial     = utf8_decode($arrayTerceros[$i]['razonSocial']);
        $nombreComercial = utf8_decode($arrayTerceros[$i]['nombreComercial']);
        $nombre1         = utf8_decode($arrayTerceros[$i]['nombre1']);
        $nombre2         = utf8_decode($arrayTerceros[$i]['nombre2']);
        $apellido1       = utf8_decode($arrayTerceros[$i]['apellido1']);
        $apellido2       = utf8_decode($arrayTerceros[$i]['apellido2']);
        $idCiudad        = $arrayTerceros[$i]['idCiudad'];
        $cliente         = $arrayTerceros[$i]['cliente'];
        $proveedor       = $arrayTerceros[$i]['proveedor'];
        $telefono1       = $arrayTerceros[$i]['telefono1'];
        $telefono2       = $arrayTerceros[$i]['telefono2'];
        $celular1        = $arrayTerceros[$i]['celular1'];
        $celular2        = $arrayTerceros[$i]['celular2'];
        $direccion       = $arrayTerceros[$i]['direccion'];
        $idtipoNit       = $arrayTerceros[$i]['idtipoNit'];
        $idRegimen       = $arrayTerceros[$i]['idRegimen'];
        $exentoIva       = $arrayTerceros[$i]['exentoIva'];
        $msjError        = $arrayTerceros[$i]['msjError'];
        $idTipoNit       = $arrayTerceros[$i]['idTipoNit'];
        $estadoTipoNit   = $arrayTerceros[$i]['estadoTipoNit'];
        $estado          = $arrayTerceros[$i]['estado'];
        $pagina_web      = $arrayTerceros[$i]['pagina_web'];
        $email           = $arrayTerceros[$i]['email'];

        //SI EXISTE LA UBICACION POR EL CODIGO DE LA CIUDAD
        $idPais         = 0;
        $idDepartamento = 0;
        if($arrayUbicacion[$idCiudad]['estado'] == 'no'){ $estado = 'no'; $msjError .= '<br/>* CODIGO UBICACION DEL TERCERO NO DISPONIBLE'; }      //NO SE ENCONTRO LA UBICACION
        else{
            $idPais         = $arrayUbicacion[$idCiudad]['id_pais'];
            $idDepartamento = $arrayUbicacion[$idCiudad]['id_departamento'];
        }

        //UBICACION DE LA CIUDAD DEL DOCUMENTO
        $ciudadNit = '';
        if($arrayUbicacion[$idCiudadNit]['estado'] == 'no' && $estadoTipoNit == 'Persona'){ $estado = 'no'; $msjError .= '<br/>* CODIGO UBICACION DOCUMENTO DE IDENTIFICACION'; }      //NO SE ENCONTRO LA UBICACION
        else if($arrayUbicacion[$idCiudadNit]['estado'] == 'si' && $estadoTipoNit == 'Persona'){ $ciudadNit = $arrayUbicacion[$idCiudadNit]['ciudad']; }

        $campos = "'$razonSocial',
                    '$nombreComercial',
                    '$nombre1',
                    '$nombre2',
                    '$apellido1',
                    '$apellido2',
                    '$idPais',
                    '$idDepartamento',
                    '$idCiudad',
                    '$cliente',
                    '$proveedor',
                    '$idRegimen',
                    '$exentoIva',
                    '$idTipoNit',
                    '$nit',
                    '$dv',
                    '$ciudadNit',
                    '$direccion',
                    '$telefono1',
                    '$telefono2',
                    '$celular1',
                    '$celular2',
                    '$email',
                    '$pagina_web',
                    NOW(),
                    '$id_empresa'";

        if($arrayTercerosBd["$nit"] == "true"){ $estado = 'repetido'; $contRepetido++; }
        else if($estado == 'si'){ $contTerceroTrue++; $valueTerceroTrue .= "($campos),"; }
        else{ $contTerceroFail++; }

        $tieneError = "false";
        if($msjError != ""){ $tieneError = "true"; }

        $valueTercero .= "('$idUpload',$campos,'$estado','$msjError','$tieneError','$fila'),";
    }

    // return array('error'=> $valueTercero." - ".$contTerceroTrue, 'debug'=> "$debugError");
    $valueTercero     = substr($valueTercero, 0, -1);
    $valueTerceroTrue = substr($valueTerceroTrue, 0, -1);
    //====================================// INSERTAR TERCEROS //====================================//
    if($contTercero > 0){

        $campos = "nombre,
                    nombre_comercial,
                    nombre1,
                    nombre2,
                    apellido1,
                    apellido2,
                    id_pais,
                    id_departamento,
                    id_ciudad,
                    tipo_cliente,
                    tipo_proveedor,
                    id_tercero_tributario,
                    exento_iva,
                    id_tipo_identificacion,
                    numero_identificacion,
                    dv,
                    ciudad_identificacion,
                    direccion,
                    telefono1,
                    telefono2,
                    celular1,
                    celular2,
                    email,
                    pagina_web,
                    fecha_creacion,
                    id_empresa";

        if($contTerceroTrue > 0){
            $sqlInsertTercero   = "INSERT INTO terceros($campos) VALUES $valueTerceroTrue";
            $queryInsertTercero = mysql_query($sqlInsertTercero);
            if(!$queryInsertTercero){ return array('error'=> "No se almaceno los terceros en la base de datos! $sqlInsertTercero",'debug'=> "$debugError"); }
        }

        $sqlInsertTercero   = "INSERT INTO terceros_upload_registro(id_upload,$campos,estado,mensaje_error,tiene_error,fila_excel) VALUES $valueTercero";
        $queryInsertTercero = mysql_query($sqlInsertTercero);
        if(!$queryInsertTercero){ return array('error'=> "No se almaceno los terceros en la base de datos! $sqlInsertTercero", 'debug'=> "$debugError"); }

        $sqlUpdate   = "UPDATE terceros_upload SET ok='$contTerceroTrue', fail='$contTerceroFail', repetido='$contRepetido' WHERE id='$idUpload'";
        $queryUpdate = mysql_query($sqlUpdate);

        return array('success'=>true, 'idInsert'=>$idUpload);
    }
    else{ return array('error'=> "No se encontraron terceros a almacenar!", 'debug'=> "$debugError"); }

?>