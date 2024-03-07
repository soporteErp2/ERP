<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../config_var_global.php");

  $id_empresa = $_SESSION['EMPRESA'];

  header("Pragma: public");
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private",false);
  header("Content-Description: File Transfer");
  header("Content-Type: application/force-download");
  header("Content-Disposition: attachment; filename=bancolombia_comprobante_egreso_".date(Y_m_d).".txt");

  //================================ CABECERA ================================//

  //TIPO REGISTRO
  $filaCabecera .= "1";

  // NIT ENTIDAD ORIGINADORA
  $sql = "SELECT documento FROM empresas WHERE activo = 1 AND id = $id_empresa";
  $query = $query = mysql_query($sql,$link);
  $documentoEmpresa = mysql_result($query,0,'documento');
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
  $filaCabecera .= str_pad("A",2," ",STR_PAD_RIGHT); //se debe generar un codigo que aumente las letras para que no haya repeticiones de los comprobantes

  // FECHA DE APLICACION DEL LOTE
  $filaCabecera .= date('Ymd');

  // NUMERO DE REGISTROS
  $sql = "SELECT COUNT(id) AS id
          FROM comprobante_egreso_cuentas
          WHERE activo = 1
          AND id_comprobante_egreso = $id_comprobante_egreso";
  $query = mysql_query($sql,$link);
  $id = mysql_result($query,0,'id');
  $filaCabecera .= str_pad($id,6,"0",STR_PAD_LEFT);

  // SUMATORIA DE DEBITOS
  $filaCabecera .= "00000000000000000";

  // SUMATORIA DE CREDITOS
  $sql = "SELECT debito,nit_tercero,tercero
          FROM comprobante_egreso_cuentas
          WHERE activo = 1
          AND id_comprobante_egreso = $id_comprobante_egreso";
  $query = mysql_query($sql,$link);

  while($row = mysql_fetch_array($query)){
    $totalComprobante += $row["debito"];
  }
  $totalComprobante = number_format($totalComprobante,2,"","");
  $filaCabecera .= str_pad($totalComprobante,17,"0",STR_PAD_LEFT);

  $sql = "SELECT CE.nit_tercero,CE.tercero,CCP.tipo_cuenta_bancaria,CCP.numero_cuenta_bancaria
          FROM comprobante_egreso AS CE
          LEFT JOIN configuracion_cuentas_pago AS CCP
          ON CE.id_configuracion_cuenta = CCP.id
          WHERE CE.activo = 1
          AND CE.estado = 1
          AND CE.id_empresa = $id_empresa
          AND CE.id = $id_comprobante_egreso";
  $query = mysql_query($sql,$link);
  $nit_tercero_cabecera            = mysql_result($query,0,'nit_tercero');
  $tercero_cabecera                = mysql_result($query,0,'tercero');
  $tipo_cuenta_bancaria_cabecera   = mysql_result($query,0,'tipo_cuenta_bancaria');
  $numero_cuenta_bancaria_cabecera = mysql_result($query,0,'numero_cuenta_bancaria');

  // CUENTA CLIENTE A DEBITAR
  $filaCabecera .= str_pad($numero_cuenta_bancaria_cabecera,11,"0",STR_PAD_LEFT);

  // TIPO DE CUENTA CLIENTE A DEBITAR
  $filaCabecera .= $tipo_cuenta_bancaria_cabecera;

  // FILLER
  $filaCabecera .= "                                                                                                                                                     \n";

  //================================ TerceroS ===============================//
  $sql = "SELECT CE.debito,CE.nit_tercero,CE.tercero,T.email,T.numero_cuenta_bancaria
          FROM comprobante_egreso_cuentas AS CE
          LEFT JOIN terceros AS T
          ON T.id = CE.id_tercero
          WHERE CE.activo = 1
          AND CE.id_comprobante_egreso = $id_comprobante_egreso";
  $query = mysql_query($sql,$link);

  while($row = mysql_fetch_array($query)){
    $arrayTerceros[] = array(
                                          "documento" => ($row['nit_tercero'] == "")? $nit_tercero_cabecera : $row['nit_tercero'],
                                          "nombre" => ($row['tercero'] == "")? $tercero_cabecera : $row['tercero'],
                                          "email" => $row['email_empresa'],
                                          "numero_cuenta_bancaria" => $row['numero_cuenta_bancaria'],
                                          'valor_transaccion' => $row['debito']
                                        );
  }

  foreach($arrayTerceros as $key){
    $filaTercero = "";

    // TIPO REGISTRO
    $filaTercero .= "6";

    // NIT BENEFICIARIO
    if(strlen($key['documento']) > 15){
      $documentoTercero = substr($key['documento'],0,15);
    } else{
      $documentoTercero = str_pad($key['documento'],15,"0",STR_PAD_LEFT);
    }
    $filaTercero .= $documentoTercero;

    // NOMBRE BENEFICIARIO
    $arrayOriginales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞß';
    $arrayReemplazos = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBS';
    $nombreTercero  = strtr($key['nombre'],utf8_decode($arrayOriginales),$arrayReemplazos);

    if(strlen($key['nombre']) > 30){
      $nombreTercero = substr($nombreTercero,0,30);
    } else{
      $nombreTercero = str_pad($nombreTercero,30," ",STR_PAD_RIGHT);
    }
    $filaTercero .= $nombreTercero;

    // BANCO CUENTA DEL BENEFICIARIO
    $filaTercero .= "000001007";

    // NUMERO CUENTA DEL BENEFICIARIO
    $filaTercero .= str_pad($key['numero_cuenta_bancaria'],17," ",STR_PAD_RIGHT);

    // INDICADOR LUGAR DE PAGO
    $filaTercero .= "S";

    // TIPO DE TRANSACCION
    $filaTercero .= "37";

    // VALOR TRANSACCION
    $valorTransaccion = $key['valor_transaccion'];
    $valorTransaccion = number_format($valorTransaccion,2,"","");
    $filaTercero .= str_pad($valorTransaccion,17,"0",STR_PAD_LEFT);

    // FECHA APLICACION
    $filaTercero .= date('Ymd');

    // REFERENCIA
    $filaTercero .= "                     ";

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
  }

  echo $filaCabecera.$consolidadoFilaTercero;
?>
