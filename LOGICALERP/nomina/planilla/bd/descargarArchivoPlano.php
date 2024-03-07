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
  header("Content-Disposition: attachment; filename=bancolombia_nomina_".date(Y_m_d).".txt");

  //================================ CABECERA ================================//

  $sql = "SELECT fecha_final
          FROM nomina_planillas
          WHERE activo = 1
          AND id = $id_planilla
          AND id_empresa = $id_empresa";
  $query = mysql_query($sql,$link);
  $fechaFinal = mysql_result($query,0,'fecha_final');

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

  // CLASE DE TRNASACCION
  $filaCabecera .= "225";

  // DESCRIPCION PROPOSITO TRANSACCIONES
  $descripcionProposito = str_pad("PAGONOMINA",10," ",STR_PAD_RIGHT);
  $filaCabecera .= $descripcionProposito;

  // FECHA TRANSMISION DE LOTE
  $filaCabecera .= date('Ymd',strtotime($fechaFinal));

  // SECUENCIA DE ENVIO DE LOTES
  $filaCabecera .= "AA";

  // FECHA DE APLICACION DEL LOTE
  $filaCabecera .= date('Ymd',strtotime($fechaFinal));

  // NUMERO DE REGISTROS
  $sql = "SELECT COUNT(id) AS id
          FROM nomina_planillas_empleados
          WHERE activo = 1
          AND id_empresa = $id_empresa
          AND id_planilla = $id_planilla";
  $query = mysql_query($sql,$link);
  $id = mysql_result($query,0,'id');
  $filaCabecera .= str_pad($id,6,"0",STR_PAD_LEFT);

  // SUMATORIA DE DEBITOS
  $filaCabecera .= "00000000000000000";

  // SUMATORIA DE CREDITOS
  $sql = "SELECT naturaleza,valor_concepto
          FROM nomina_planillas_empleados_conceptos
          WHERE activo = 1
          AND id_empresa = $id_empresa
          AND id_planilla = $id_planilla
          AND (naturaleza != 'Apropiacion' && naturaleza != 'Provision')";
  $query = mysql_query($sql,$link);

  while($row = mysql_fetch_array($query)){
    if($row['naturaleza'] == "Devengo"){
      $suma += $row['valor_concepto'];
    } else{
      $resta += $row['valor_concepto'];
    }
  }
  $totalNomina = number_format(($suma - $resta),2,"","");
  $filaCabecera .= str_pad($totalNomina,17,"0",STR_PAD_LEFT);

  $sql = "SELECT tipo_cuenta_bancaria,numero_cuenta_bancaria
          FROM nomina_archivos_planos
          WHERE activo = 1
          AND id_empresa = $id_empresa
          AND id = 1";
  $query = mysql_query($sql,$link);

  // CUENTA CLIENTE A DEBITAR
  $numero_cuenta_bancaria = mysql_result($query,0,'numero_cuenta_bancaria');
  $filaCabecera .= str_pad($numero_cuenta_bancaria,11,"0",STR_PAD_LEFT);

  // TIPO DE CUENTA CLIENTE A DEBITAR
  $tipo_cuenta_bancaria   = mysql_result($query,0,'tipo_cuenta_bancaria');
  $filaCabecera .= $tipo_cuenta_bancaria;

  // FILLER
  $filaCabecera .= "                                                                                                                                                     \n";

  //================================ EMPLEADOS ===============================//

  $sql = "SELECT E.id,E.documento,E.nombre,E.email_empresa,EC.numero_cuenta_bancaria,NPEC.naturaleza,NPEC.valor_concepto
          FROM nomina_planillas_empleados_conceptos AS NPEC
          LEFT JOIN empleados AS E
          ON NPEC.id_empleado = E.id
          LEFT JOIN empleados_contratos AS EC
          ON NPEC.id_contrato = EC.id
          WHERE NPEC.activo = 1
          AND NPEC.id_empresa = $id_empresa
          AND NPEC.id_planilla = $id_planilla
          AND (NPEC.naturaleza != 'Apropiacion' && NPEC.naturaleza != 'Provision')";
  $query = mysql_query($sql,$link);

  while($row = mysql_fetch_array($query)){
    if(is_array($arrayEmpleados[$row['id']])){
      if($row['naturaleza'] == "Devengo"){
        $arrayEmpleados[$row['id']]['devengo'] += $row['valor_concepto'];
      }
      if($row['naturaleza'] == "Deduccion"){
        $arrayEmpleados[$row['id']]['deduccion'] += $row['valor_concepto'];
      }
    }
    else{
      $arrayEmpleados[$row['id']] = array(
                                            "documento" => $row['documento'],
                                            "nombre" => $row['nombre'],
                                            "email" => $row['email_empresa'],
                                            "numero_cuenta_bancaria" => $row['numero_cuenta_bancaria'],
                                            'devengo' => (($row['naturaleza'] == "Devengo")? $row['valor_concepto'] : 0 ),
                                            'deduccion' => (($row['naturaleza'] == "Deduccion")? $row['valor_concepto'] : 0 ),
                                          );
    }
  }

  foreach($arrayEmpleados as $key){
    $filaEmpleado = "";

    // TIPO REGISTRO
    $filaEmpleado .= "6";

    // NIT BENEFICIARIO
    if(strlen($key['documento']) > 15){
      $documentoEmpleado = substr($key['documento'],0,15);
    } else{
      $documentoEmpleado = str_pad($key['documento'],15," ",STR_PAD_RIGHT);
    }
    $filaEmpleado .= $documentoEmpleado;

    // NOMBRE BENEFICIARIO
    $arrayOriginales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞß';
    $arrayReemplazos = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBS';
    $nombreEmpleado  = strtr($key['nombre'],utf8_decode($arrayOriginales),$arrayReemplazos);

    if(strlen($key['nombre']) > 30){
      $nombreEmpleado = substr($nombreEmpleado,0,30);
    } else{
      $nombreEmpleado = str_pad($nombreEmpleado,30," ",STR_PAD_RIGHT);
    }
    $filaEmpleado .= $nombreEmpleado;

    // BANCO CUENTA DEL BENEFICIARIO
    $filaEmpleado .= "005600078";

    // NUMERO CUENTA DEL BENEFICIARIO
    $filaEmpleado .= str_pad($key['numero_cuenta_bancaria'],17," ",STR_PAD_RIGHT);

    // INDICADOR LUGAR DE PAGO
    $filaEmpleado .= " ";

    // TIPO DE TRANSACCION
    $filaEmpleado .= "37";

    // VALOR TRANSACCION
    $valorTransaccion = $key['devengo'] - $key['deduccion'];
    $valorTransaccion = number_format($valorTransaccion,2,"","");
    $filaEmpleado .= str_pad($valorTransaccion,17,"0",STR_PAD_LEFT);

    // FECHA APLICACION
    $filaEmpleado .= date('Ymd',strtotime($fechaFinal));

    // REFERENCIA
    $filaEmpleado .= "                     ";

    // TIPO DE DOCUMENTO DE IDENTIFICACION
    $filaEmpleado .= " ";

    // OFICINA DE ENTREGA
    $filaEmpleado .= "00000";

    // NUMERO DE FAX
    $filaEmpleado .= "               ";

    // EMAIL
    $filaEmpleado .= str_pad($key['email'],80," ",STR_PAD_RIGHT);

    // NUMERO IDENTIFICACION DEL AUTORIZADO
    $filaEmpleado .= "               ";

    // FILLER
    $filaEmpleado .= "                           \n";

    $consolidadoFilaEmpleado .= $filaEmpleado;
  }

  echo $filaCabecera.$consolidadoFilaEmpleado;
?>
