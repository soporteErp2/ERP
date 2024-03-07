<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../config_var_global.php");

  $id_empresa  = $_SESSION['EMPRESA'];
  $id_sucursal = $_SESSION['SUCURSAL'];

  switch($banco){
    case '1051':
      archivoPlanoDavivienda($regenera,$banco,$fecha_inicial,$fecha_final,$id_empresa,$id_sucursal,$mysql);
      break;

    case '1007':
      archivoPlanoBancolombia($regenera,$banco,$fecha_inicial,$fecha_final,$id_empresa,$id_sucursal,$mysql);
      break;

    default:
      echo "Banco No Encontrado";
      break;
  }

  function archivoPlanoDavivienda($regenera,$banco,$fecha_inicial,$fecha_final,$id_empresa,$id_sucursal,$mysql){

    if($regenera == "false"){
      $whereRegenera = "AND CE.archivo_plano_generado != 'Si'";
    }

    $sql = "SELECT
              CE.id,
              CE.nit_tercero,
              CE.tercero,
              T.tipo_cuenta_bancaria,
              T.numero_cuenta_bancaria,
              T.banco,
              T.tipo_identificacion,
              T.email,
            	CE.consecutivo,
            	CEC.debito,
            	CEC.credito
            FROM comprobante_egreso AS CE
            LEFT JOIN comprobante_egreso_cuentas AS CEC ON CE.id = CEC.id_comprobante_egreso
            LEFT JOIN terceros AS T ON T.id = CE.id_tercero
            LEFT JOIN configuracion_cuentas_pago AS CP ON CP.id = CE.id_configuracion_cuenta
            WHERE CE.activo = 1
            AND CE.id_empresa = $id_empresa
            AND CE.id_sucursal = $id_sucursal
            AND CE.fecha_comprobante BETWEEN '$fecha_inicial' AND '$fecha_final'
            AND CE.disponible_archivo_plano = 'Si'
            AND CP.banco = '1051'
            $whereRegenera";
    $query = $mysql->query($sql,$mysql->link);

    if($mysql->num_rows($query) == 0){
      echo "<script>
              window.close();
              alert('No existen documentos para generar el archivo plano.');
            </script>";
      exit;
    }

    while($row = $mysql->fetch_array($query)){
      if(!is_array($comprobantes[$row['id']])){
        $comprobantes[$row['id']] = array(
                                      "id" => $row['id'],
                                      "nit_tercero" => $row['nit_tercero'],
                                      "tercero" => $row['tercero'],
                                      "tipo_cuenta_bancaria" => $row['tipo_cuenta_bancaria'],
                                      "numero_cuenta_bancaria" => $row['numero_cuenta_bancaria'],
                                      "banco" => $row['banco'],
                                      "tipo_identificacion" => $row['tipo_identificacion'],
                                      "email" => $row['email'],
                                      "referencia" => $row['consecutivo'],
                                      "debito" => $row['debito'],
                                      "credito" => $row['credito']
                                    );
      } else{
        $comprobantes[$row['id']]['debito'] += $row['debito'];
        $comprobantes[$row['id']]['credito'] += $row['credito'];
      }
    }

    foreach($comprobantes as $key => $row){
      //CALCULAR PAGO TOTAL DEL ARCHIVO PLANO
      $total = $row['debito'] - $row['credito'];
      $totalPago += $total;
      $comprobantes[$key]['total'] = $total;
    }

    //=============================== CABECERA ===============================//
    $sql = "SELECT tipo_cuenta_bancaria,numero_cuenta_bancaria
            FROM nomina_archivos_planos
            WHERE activo = 1
            AND id_empresa = $id_empresa
            AND id = 2";
    $query = $mysql->query($sql,$mysql->link);
    $tipo_cuenta_bancaria   = $mysql->result($query,0,'tipo_cuenta_bancaria');
    $numero_cuenta_bancaria = $mysql->result($query,0,'numero_cuenta_bancaria');

    //TIPO REGISTRO
    $filaCabecera .= "RC";

    // NIT ENTIDAD ORIGINADORA
    $documentoEmpresa = $_SESSION['NITEMPRESA'];
    $documentoEmpresa = str_pad($documentoEmpresa,16,"0",STR_PAD_LEFT);
    $filaCabecera .= $documentoEmpresa;

    // TIPO DE PAGO
    $filaCabecera .= "PROVPROV";

    // NUMERO CUENTA BANCARIA
    $numero_cuenta_bancaria = str_pad($numero_cuenta_bancaria,16,"0",STR_PAD_LEFT);
    $filaCabecera .= $numero_cuenta_bancaria;

    //TIPO DE CUENTA
    if($tipo_cuenta_bancaria == "S"){
      $tipo_cuenta_bancaria = "CA";
    }
    else if($tipo_cuenta_bancaria == "D"){
      $tipo_cuenta_bancaria = "CC";
    }
    $filaCabecera .= $tipo_cuenta_bancaria;

    // CODIGO BANCO
    $filaCabecera .= "000051000000000000000000000004";

    // FECHA TRANSMISION DE LOTE
    $filaCabecera .= date('Ymd');

    // CAMPOS SIN IDENTIFICAR
    $filaCabecera .= "1000000000999900000000000000000100000000000000000000000000000000000000000000000000000000\n";

    //=============================== Terceros ===============================//
    foreach($comprobantes as $key){
      $filaTercero = "";

      // TIPO REGISTRO
      $filaTercero .= "TR";

      // NIT BENEFICIARIO
      if(strlen($key['nit_tercero']) > 16){
        $documentoTercero = substr($key['nit_tercero'],0,16);
      } else{
        $documentoTercero = str_pad($key['nit_tercero'],16,"0",STR_PAD_LEFT);
      }
      $filaTercero .= $documentoTercero;

      // NUMERO CUENTA BANCARIA
      $filaTercero .= str_pad($key['numero_cuenta_bancaria'],32,"0",STR_PAD_LEFT);

      // TIPO CUENTA BANCARIA
      $filaTercero .= $key['tipo_cuenta_bancaria'];

      // CODIGO BANCO
      $filaTercero .= "000051";

      // VALOR TRANSACCION
      $valorTransaccion = $key['total'];
      $valorTransaccion = number_format($valorTransaccion,2,"","");
      $filaTercero .= str_pad($valorTransaccion,17,"0",STR_PAD_LEFT);

      // ESPACIOS SIN UTILIZAR
      $filaTercero .= "00000000";

      // TIPO IDENTIFICACION
      if($key['tipo_identificacion'] == "CC" || $key['tipo_identificacion'] == "C.C" || $key['tipo_identificacion'] == "cc" || $key['tipo_identificacion'] == "c.c"){
        $tipoTransaccion = "21";
      }
      else{
        $tipoTransaccion = "11";
      }
      $filaTercero .= $tipoTransaccion;

      // ESPACIOS SIN UTILIZAR
      $filaTercero .= "9999000000000000000000000000000000000000000000000000000000000000000000000000000000000\n";

      $consolidadoFilaTercero .= $filaTercero;

      // ACTUALIZAR ESTADO DE GENERACION DE ARCHIVO PLANO
      if($regenera == "false"){
        $sql = "UPDATE comprobante_egreso SET archivo_plano_generado = 'Si' WHERE id = $key[id]";
        $query = $mysql->query($sql,$mysql->link);
      }
    }

    header("Pragma: public");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Description: File Transfer");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=Davivienda_pago_proveedores_".date(Y_m_d).".txt");

    echo $filaCabecera.$consolidadoFilaTercero;

  };

  function archivoPlanoBancolombia($regenera,$banco,$fecha_inicial,$fecha_final,$id_empresa,$id_sucursal,$mysql){

    if($regenera == "false"){
      $whereRegenera = "AND CE.archivo_plano_generado != 'Si'";
    }

    $sql = "SELECT
              CE.id,
              CE.nit_tercero,
              CE.tercero,
              T.numero_cuenta_bancaria,
              T.banco,
              T.tipo_identificacion,
              T.email,
            	CE.consecutivo,
            	CEC.debito,
            	CEC.credito
            FROM comprobante_egreso AS CE
            LEFT JOIN comprobante_egreso_cuentas AS CEC ON CE.id = CEC.id_comprobante_egreso
            LEFT JOIN terceros AS T ON T.id = CE.id_tercero
            LEFT JOIN configuracion_cuentas_pago AS CP ON CP.id = CE.id_configuracion_cuenta
            WHERE CE.activo = 1
            AND CE.id_empresa = $id_empresa
            AND CE.id_sucursal = $id_sucursal
            AND CE.fecha_comprobante BETWEEN '$fecha_inicial' AND '$fecha_final'
            AND CE.disponible_archivo_plano = 'Si'
            AND CP.banco = '1007'
            $whereRegenera";
    $query = $mysql->query($sql,$mysql->link);

    if($mysql->num_rows($query) == 0){
      echo "<script>
              window.close();
              alert('No existen documentos para generar el archivo plano.');
            </script>";
      exit;
    }

    while($row = $mysql->fetch_array($query)){
      if(!is_array($comprobantes[$row['id']])){
        $comprobantes[$row['id']] = array(
                                      "id" => $row['id'],
                                      "nit_tercero" => $row['nit_tercero'],
                                      "tercero" => $row['tercero'],
                                      "numero_cuenta_bancaria" => $row['numero_cuenta_bancaria'],
                                      "banco" => $row['banco'],
                                      "tipo_identificacion" => $row['tipo_identificacion'],
                                      "email" => $row['email'],
                                      "referencia" => $row['consecutivo'],
                                      "debito" => $row['debito'],
                                      "credito" => $row['credito']
                                    );
      } else{
        $comprobantes[$row['id']]['debito'] += $row['debito'];
        $comprobantes[$row['id']]['credito'] += $row['credito'];
      }
    }

    foreach($comprobantes as $key => $row){
      //CALCULAR PAGO TOTAL DEL ARCHIVO PLANO
      $total = $row['debito'] - $row['credito'];
      $totalPago += $total;
      $comprobantes[$key]['total'] = $total;
    }

    //=============================== CABECERA ===============================//

    //TIPO REGISTRO
    $filaCabecera .= "1";

    // NIT ENTIDAD ORIGINADORA
    $documentoEmpresa = $_SESSION['NITEMPRESA'];
    $documentoEmpresa = str_pad($documentoEmpresa,15,"0",STR_PAD_LEFT);
    $filaCabecera .= $documentoEmpresa;

    // APLICACION
    $filaCabecera .= "I";

    // FILLER
    $filaCabecera .= "               ";

    // CLASE DE TRANSACCION
    $filaCabecera .= "220";

    // DESCRIPCION PROPOSITO TRANSACCIONES
    $descripcionProposito = str_pad("PROV",10," ",STR_PAD_RIGHT);
    $filaCabecera .= $descripcionProposito;

    // FECHA TRANSMISION DE LOTE
    $filaCabecera .= date('Ymd');

    // SECUENCIA DE ENVIO DE LOTES
    $filaCabecera .= "A ";

    // FECHA DE APLICACION DEL LOTE
    $filaCabecera .= date('Ymd');

    // NUMERO DE REGISTROS
    $numero_comprobantes = count($comprobantes);
    $filaCabecera .= str_pad($numero_comprobantes,6,"0",STR_PAD_LEFT);

    // SUMATORIA DE DEBITOS
    $filaCabecera .= "00000000000000000";

    // SUMATORIA DE CREDITOS
    $totalPago = number_format($totalPago,2,"","");
    $filaCabecera .= str_pad($totalPago,17,"0",STR_PAD_LEFT);

    $sql = "SELECT tipo_cuenta_bancaria,numero_cuenta_bancaria
            FROM nomina_archivos_planos
            WHERE activo = 1
            AND id_empresa = $id_empresa
            AND id = 1";
    $query = $mysql->query($sql,$mysql->link);
    $tipo_cuenta_bancaria_cabecera   = $mysql->result($query,0,'tipo_cuenta_bancaria');
    $numero_cuenta_bancaria_cabecera = $mysql->result($query,0,'numero_cuenta_bancaria');

    // CUENTA CLIENTE A DEBITAR
    $filaCabecera .= str_pad($numero_cuenta_bancaria_cabecera,11,"0",STR_PAD_LEFT);

    // TIPO DE CUENTA CLIENTE A DEBITAR
    $filaCabecera .= $tipo_cuenta_bancaria_cabecera;

    // FILLER
    $filaCabecera .= "                                                                                                                                                     \n";

    //=============================== Terceros ===============================//
    foreach($comprobantes as $key){
      $filaTercero = "";

      // TIPO REGISTRO
      $filaTercero .= "6";

      // NIT BENEFICIARIO
      if(strlen($key['nit_tercero']) > 15){
        $documentoTercero = substr($key['nit_tercero'],0,15);
      } else{
        $documentoTercero = str_pad($key['nit_tercero'],15,"0",STR_PAD_LEFT);
      }
      $filaTercero .= $documentoTercero;

      // NOMBRE BENEFICIARIO
      $arrayOriginales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞß';
      $arrayReemplazos = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBS';
      $nombreTercero  = strtr($key['tercero'],utf8_decode($arrayOriginales),$arrayReemplazos);

      if(strlen($nombreTercero) > 30){
        $nombreTercero = substr($nombreTercero,0,30);
      } else{
        $nombreTercero = str_pad($nombreTercero,30," ",STR_PAD_RIGHT);
      }
      $filaTercero .= $nombreTercero;

      // BANCO CUENTA DEL BENEFICIARIO  9
      $filaTercero .= str_pad($key['banco'],9,"0",STR_PAD_LEFT);

      // NUMERO CUENTA DEL BENEFICIARIO
      $filaTercero .= str_pad($key['numero_cuenta_bancaria'],17," ",STR_PAD_RIGHT);

      // INDICADOR LUGAR DE PAGO
      $filaTercero .= "S";

      // TIPO DE IDENTIFICACION
      if($key['tipo_identificacion'] == "CC" || $key['tipo_identificacion'] == "C.C" || $key['tipo_identificacion'] == "cc" || $key['tipo_identificacion'] == "c.c"){
        $tipoTransaccion = "37";
      }
      else{
        $tipoTransaccion = "27";
      }
      $filaTercero .= $tipoTransaccion;

      // VALOR TRANSACCION
      $valorTransaccion = $key['total'];
      $valorTransaccion = number_format($valorTransaccion,2,"","");
      $filaTercero .= str_pad($valorTransaccion,17,"0",STR_PAD_LEFT);

      // FECHA APLICACION
      $filaTercero .= date('Ymd');

      // REFERENCIA DEL PAGO
      $filaTercero .= str_pad($key['referencia'],21," ",STR_PAD_LEFT);

      // TIPO DE DOCUMENTO DE IDENTIFICACION
      $filaTercero .= " ";

      // OFICINA DE ENTREGA
      $filaTercero .= "00000";

      // NUMERO DE FAX
      $filaTercero .= "               ";

      // EMAIL
      $filaTercero .= str_pad($key['email'],80," ",STR_PAD_RIGHT);

      // NUMERO IDENTIFICACION DEL AUTORIZADO
      $filaTercero .= "               ";

      // FILLER
      $filaTercero .= "                           \n";

      $consolidadoFilaTercero .= $filaTercero;

      // ACTUALIZAR ESTADO DE GENERACION DE ARCHIVO PLANO
      if($regenera == "false"){
        $sql = "UPDATE comprobante_egreso SET archivo_plano_generado = 'Si' WHERE id = $key[id]";
        $query = $mysql->query($sql,$mysql->link);
      }
    }

    header("Pragma: public");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Description: File Transfer");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=Bancolombia_pago_proveedores_".date(Y_m_d).".txt");

    echo $filaCabecera.$consolidadoFilaTercero;

  }
?>
