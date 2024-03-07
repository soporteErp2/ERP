<?php
    include_once('../../../../misc/excel/Classes/PHPExcel.php');

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

    $contTercero = 0;
    for ($contFila=1; $contFila <= $contArray; $contFila++) {

        //TIPO NIT
        $razonSocial     = addslashes(strtoupper($arrayExcel[$contFila][0]));
        $nombreComercial = addslashes(strtoupper($arrayExcel[$contFila][1]));        

        $idCiudad = ($arrayExcel[$contFila][2] > 0)? $arrayExcel[$contFila][2]: 0;
        $arrayUbicacion[$idCiudad]['estado'] = 'no';     
       
        if($nombreComercial=='' || $idCiudad == 0) continue;       
        
        $direccion  = addslashes($arrayExcel[$contFila][3]);
        $telefono1  = addslashes($arrayExcel[$contFila][4]);
        $telefono2  = addslashes($arrayExcel[$contFila][5]);
        $celular1   = addslashes($arrayExcel[$contFila][6]);
        $celular2   = addslashes($arrayExcel[$contFila][7]);
        $email      = addslashes($arrayExcel[$contFila][8]);       

        $contTercero++;   
        $estado = 'si';          
        
        $arrayTerceros[$contTercero] = array('razonSocial'=> $razonSocial,
                                             'nombreComercial'=> $nombreComercial,
                                             'idCiudad'=> $idCiudad,                                             
                                             'direccion'=> $direccion,
                                             'telefono1'=> $telefono1,
                                             'telefono2'=> $telefono2,
                                             'celular1'=> $celular1,
                                             'celular2'=> $celular2,
                                             'email'=> $email,
                                             'estado'=> $estado);
        $valueSelectTercero .= " OR nombre_comercial='$nombreComercial'";
        $valueSelectCiudad  .= " OR id='$idCiudad'";
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

    $sqlTerceros   = "SELECT nombre_comercial FROM terceros WHERE id_empresa='$id_empresa' AND activo=1 AND ($valueSelectTercero)";
    $queryTerceros = mysql_query($sqlTerceros);
    while ($rowTercero = mysql_fetch_assoc($queryTerceros)) {
        $nombre_comercial = $rowTercero['nombre_comercial'];
        $arrayTercerosBd["$nombre_comercial"] = "true";
    }

    // if($contArray == 0){ return array("error"=> "No se encontraron filas con informacion en el archivo excel!", "debug"=> "$debugError"); }

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
    for ($i = 1; $i <= $contTercero; $i++) {
        $fila++;      
        $razonSocial     = utf8_decode($arrayTerceros[$i]['razonSocial']);
        $nombreComercial = utf8_decode($arrayTerceros[$i]['nombreComercial']);      
        $idCiudad        = $arrayTerceros[$i]['idCiudad'];        
        $telefono1       = $arrayTerceros[$i]['telefono1'];
        $telefono2       = $arrayTerceros[$i]['telefono2'];
        $celular1        = $arrayTerceros[$i]['celular1'];
        $celular2        = $arrayTerceros[$i]['celular2'];
        $direccion       = $arrayTerceros[$i]['direccion'];       
        $email           = $arrayTerceros[$i]['email'];
        $estado          = $arrayTerceros[$i]['estado'];

        //SI EXISTE LA UBICACION POR EL CODIGO DE LA CIUDAD
        $idPais         = 0;
        $idDepartamento = 0;
        if($arrayUbicacion[$idCiudad]['estado'] == 'no'){ $estado = 'no'; $msjError .= '<br/>* CODIGO UBICACION DEL TERCERO NO DISPONIBLE'; }      //NO SE ENCONTRO LA UBICACION
        else{
            $idPais         = $arrayUbicacion[$idCiudad]['id_pais'];
            $idDepartamento = $arrayUbicacion[$idCiudad]['id_departamento'];
        }
        
        $campos =  "'$razonSocial',
                    '$nombreComercial',                    
                    '$idPais',
                    '$idDepartamento',
                    '$idCiudad',                    
                    '$direccion',
                    '$telefono1',
                    '$telefono2',
                    '$celular1',
                    '$celular2',
                    '$email',                    
                     NOW(),
                    '$id_empresa',
                     0,
                    'prospecto'";

        if($arrayTercerosBd["$nombreComercial"] == "true"){ $estado = 'repetido'; $contRepetido++; }
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

        $campos = " nombre,
                    nombre_comercial,                   
                    id_pais,
                    id_departamento,
                    id_ciudad,                    
                    direccion,
                    telefono1,
                    telefono2,
                    celular1,
                    celular2,
                    email,                  
                    fecha_creacion,
                    id_empresa,
                    tercero,
                    tipo";

        if($contTerceroTrue > 0){
            $sqlInsertTercero   = "INSERT INTO terceros($campos) VALUES $valueTerceroTrue";
            $queryInsertTercero = mysql_query($sqlInsertTercero);
            if(!$queryInsertTercero){ return array('error'=> "No se almaceno los terceros en la base de datos! $sqlInsertTercero",'debug'=> "$debugError"); }
        }

        $sqlInsertTercero   = "INSERT INTO prospectos_upload_registro(id_upload,$campos,estado,mensaje_error,tiene_error,fila_excel) VALUES $valueTercero";
        $queryInsertTercero = mysql_query($sqlInsertTercero);
        if(!$queryInsertTercero){ return array('error'=> "No se almaceno los terceros en la base de datos! $sqlInsertTercero ", 'debug'=> "$debugError"); }

        $sqlUpdate   = "UPDATE terceros_upload SET ok='$contTerceroTrue', fail='$contTerceroFail', repetido='$contRepetido',tercero=0 WHERE id='$idUpload'";
        $queryUpdate = mysql_query($sqlUpdate);

        return array('success'=>true, 'idInsert'=>$idUpload);

        print_r($arrayTerceros);
    }
    else{ return array('error'=> "No se encontraron terceros a almacenar!", 'debug'=> "$debugError"); }

?>