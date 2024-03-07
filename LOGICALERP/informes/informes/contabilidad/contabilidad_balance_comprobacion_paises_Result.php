<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();

  if($IMPRIME_XLS == 'true'){
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=balance_de_comprobacion.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
  }

  $id_empresa         = $_SESSION['EMPRESA'];
  $desde              = $MyInformeFiltroFechaInicio;
  $hasta              = $MyInformeFiltroFechaFinal;
  $whereSucursal      = '';
  $groupBy            = '';
  $subtitulo_cabecera = '';
  $td_body            = '';
  $td_head            = '';
  $bodyTable          = '';
  $idTercero          = 0;

  $sql = "SELECT digitos FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA] AND nombre = '$nivel_cuentas'";
  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
    $arrayCuentas[] = $row['digitos'];
  }

  if($sucursal != '' && $sucursal != 'global'){
    $whereSucursal = ' AND id_sucursal = ' . $sucursal;

    //CONSULTAR EL NOMBRE DE LA SUCURSAL
    $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa = $id_empresa AND id = " . $sucursal;
    $query = mysql_query($sql,$link);
    $subtitulo_cabecera .= '<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
  }

  foreach($arrayCuentas as $rowx){
    $nivel_cuentas = $rowx;
    //NIVEL DE CUENTAS
    $groupBy     = ($nivel_cuentas > 0)? "LEFT(codigo_cuenta,$nivel_cuentas)": "codigo_cuenta";
    $campoCuenta = ($nivel_cuentas > 0)? "LEFT(codigo_cuenta,$nivel_cuentas) AS codigo_cuenta": "codigo_cuenta";

    if(!isset($MyInformeFiltroFechaInicio) && !isset($MyInformeFiltroFechaFinal)){
      $MyInformeFiltroFechaInicio = date('Y').'-01-01';
      $MyInformeFiltroFechaFinal  = date('Y-m-d');
      $script  = 'localStorage.cuenta_inicialBC                              = "";
                  localStorage.cuenta_finalBC                                = "";
                  localStorage.MyInformeFiltroFechaFinalBalanceComprobacion  = "";
                  localStorage.MyInformeFiltroFechaInicioBalanceComprobacion = "";
                  localStorage.sucursal_balance_comprobacion                 = "";
                  arraytercerosBC.length                                     = 0;
                  tercerosConfiguradosBC.length                              = 0;
                  checkBoxSelectAllTercerosBC                                = 0;';
    }

    //FILTRO POR TERCERO
    if(isset($arraytercerosJSON) && $arraytercerosJSON <> '[]'){
        if ($arraytercerosJSON == 'todos') {
          $whereClientes = " ";
        }
        else{
          $arraytercerosJSON = json_decode($arraytercerosJSON);
          foreach($arraytercerosJSON as $indice => $id_tercero){
            $whereClientes .= ($whereClientes=='')? ' id_tercero='.$id_tercero : ' OR id_tercero='.$id_tercero;
          }
          $whereClientes = " AND (".$whereClientes.")";
        }

      $groupBy  .= ',id_tercero';
      $width_td  = '170';
      $td_head   = '<td width="170">NIT</td><td width="170">TERCERO</td>';
      $td_body   = '<td width="170"></td><td width="170"></td>';
    }

    //SALDOS
    $totalDebe          = 0;
    $totalHaber         = 0;
    $totalSaldoAnterior = 0;
    $totalSaldoActual   = 0;

    $fec1 = $desde;
    $fec2 = $hasta;

    $split1 = explode('-', $desde);
    $split2 = explode('-', $hasta);

    $year1 = $split1[0];
    $year2 = $split2[0];
    $mes1  = $split1[1];
    $mes2  = $split2[1];
    $dia1  = $split1[2];
    $dia2  = $split2[2];

  	$nombre_empresa	= $_SESSION['NOMBREEMPRESA'];
    $whereRangoCuentas=str_replace('{.}', "%", $whereRangoCuentas);
    //=======================================// SALDO ANTERIOR //=======================================//
    //**************************************************************************************************//
    $sqlAsientosAnterior = "SELECT
                              SUM(debe-haber) AS saldo,
                              cuenta,
                              $campoCuenta,
                              id_tercero,
                              nit_tercero,
                              tercero
                            FROM
                              asientos_colgaap
                            WHERE
                              activo = 1
                            AND
                              id_empresa = '$id_empresa'
                              $whereCuentasCierre
                              $whereSucursal
                              $whereClientes
                              $whereRangoCuentas
                            AND
                              fecha < '$MyInformeFiltroFechaInicio'
                            GROUP BY
                              $groupBy
                            ORDER BY
                              CAST(codigo_cuenta as CHAR) ASC, nit_tercero DESC;";
    $queryAsientosAnterior = mysql_query($sqlAsientosAnterior,$link);

    $sql_niveles_cuentas = "SELECT digitos FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA]";
    $query_niveles_cuentas = mysql_query($sql_niveles_cuentas,$link);
    while($row = mysql_fetch_array($query_niveles_cuentas)){
      $array_niveles_cuentas[$row['digitos']] = $row['digitos'];
    }

    while($rowAsientosAnterior = mysql_fetch_array($queryAsientosAnterior)){

      //SALDO
      $totalSaldoAnterior += $rowAsientosAnterior['saldo'];

      $cuenta    = $rowAsientosAnterior['codigo_cuenta'];
      if ($whereClientes<>'' ) { $idTercero = $rowAsientosAnterior['id_tercero']; }

      $arrayAsiento["$cuenta"][$idTercero] = array(
                                                      'debe_actual'   => 0,
                                                      'haber_actual'  => 0,
                                                      'descripcion'   => $rowAsientosAnterior['cuenta'],
                                                      'nit_tercero'   => $rowAsientosAnterior['nit_tercero'],
                                                      'tercero'       => $rowAsientosAnterior['tercero'],
                                                      'saldoAnterior' => $rowAsientosAnterior['saldo']
                                                  );

      foreach($array_niveles_cuentas AS $row){
        $cuenta_nivel = substr($cuenta,0,$row);
        $arrayTotalesClase[$cuenta_nivel]['saldoAnterior'] += $rowAsientosAnterior['saldo'];
        $arrayWherePuc[$cuenta_nivel] = $cuenta_nivel;
      }
    }

    //=======================================// SALDO ACTUAL //=======================================//
    //************************************************************************************************//
    $sqlAsientos = "SELECT
                      SUM(debe) AS debe,
                      SUM(haber) AS haber,
                      cuenta,
                      $campoCuenta,
                      id_tercero,
                      nit_tercero,
                      tercero
                    FROM
                      asientos_colgaap
                    WHERE
                      activo = 1
                    AND
                      id_empresa = '$id_empresa'
                      $whereCuentasCierre
                      $whereSucursal
                      $whereClientes
                      $whereRangoCuentas
                    AND
                      fecha BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
                    GROUP BY
                      $groupBy
                    ORDER BY
                      CAST(codigo_cuenta as CHAR) ASC, nit_tercero DESC;";
    $queryAsientos = mysql_query($sqlAsientos, $link);

    while($rowAsientos = mysql_fetch_array($queryAsientos)){
      //SALDO
      $totalDebe  += $rowAsientos['debe'];
      $totalHaber += $rowAsientos['haber'];
      $cuenta      = $rowAsientos['codigo_cuenta'];
      if ($whereClientes<>'' ) { $idTercero = $rowAsientos['id_tercero']; }

      $arrayAsiento["$cuenta"][$idTercero]['debe_actual']  = $rowAsientos['debe'];
      $arrayAsiento["$cuenta"][$idTercero]['haber_actual'] = $rowAsientos['haber'];
      $arrayAsiento["$cuenta"][$idTercero]['descripcion']  = $rowAsientos['cuenta'];
      $arrayAsiento["$cuenta"][$idTercero]['nit_tercero']  = $rowAsientos['nit_tercero'];
      $arrayAsiento["$cuenta"][$idTercero]['tercero']      = $rowAsientos['tercero'];

      foreach($array_niveles_cuentas AS $row){
        $cuenta_nivel = substr($cuenta,0,$row);
        $arrayTotalesClase[$cuenta_nivel]['debe']  += $rowAsientos['debe'];
        $arrayTotalesClase[$cuenta_nivel]['haber'] += $rowAsientos['haber'];
        $arrayWherePuc[$cuenta_nivel] = $cuenta_nivel;
      }
    }

    $totalClase      = '';
    $totalGrupo      = '';
    $totalCuenta     = '';
    $totalSubcuenta  = '';
    $totalAuxiliares = '';
    $totalesCuenta   = '';

    $wherePuc = "cuenta='".implode("' OR cuenta='", array_keys($arrayWherePuc))."'";

    //CONSULTAR LOS NOMBRES DE LAS CUENTAS SUPERIORES
    $sql = "SELECT cuenta,descripcion
            FROM puc
            WHERE id_empresa = '$id_empresa' AND ($wherePuc)
            GROUP BY cuenta ORDER BY CAST(cuenta as CHAR) ASC";
    $query = $mysql->query($sql,$mysql->link);
    while($row = $mysql->fetch_array($query)){
      $arrayPuc[$row['cuenta']] = $row['descripcion'];
    }

    foreach($arrayPuc as $cuenta => $descripcion){
      if($nivel_cuentas <> strlen($cuenta) && $totalizadoBC == 'Por Cuentas'){
        if($rowx === end($arrayCuentas)){
          $bodyTable .=  "<tr>
                            <td>$cuenta</td>
                            <td>".$arrayPuc[$cuenta]."</td>
                            ".(($whereClientes<>'')? "<td>&nbsp;</td><td>&nbsp;</td>" : '')."
                            <td style='text-align:right;'>".number_format($arrayTotalesClase[$cuenta]['saldoAnterior'],2,$separador_decimales,$separador_miles)."</td>
                            <td style='text-align:right;'>".number_format($arrayTotalesClase[$cuenta]['debe'],2,$separador_decimales,$separador_miles)."</td>
                            <td style='text-align:right;'>".number_format($arrayTotalesClase[$cuenta]['haber'],2,$separador_decimales,$separador_miles)."</td>
                            <td style='text-align:right;'>".number_format( ($arrayTotalesClase[$cuenta]['saldoAnterior'] + $arrayTotalesClase[$cuenta]['debe'] - $arrayTotalesClase[$cuenta]['haber'] ),2,$separador_decimales,$separador_miles)."</td>
                          </tr>";
        }
      }

      foreach($arrayAsiento[$cuenta] as $idTercero => $arrayDatos){
        $saldoAnterior = ($arrayDatos['saldoAnterior'] != '')? $arrayDatos['saldoAnterior'] : 0;
        $debe_actual   = $arrayDatos['debe_actual'];
        $haber_actual  = $arrayDatos['haber_actual'];
        $newSaldo      = $saldoAnterior+$debe_actual-$haber_actual;
        $tercero       = $arrayDatos['tercero'];
        $nit_tercero   = $arrayDatos['nit_tercero'];
        $descripcion   = $arrayDatos['descripcion'];

        if (($saldoAnterior*1)==0 && (($debe_actual*1)==0 && ($haber_actual*1)==0 && ($newSaldo*1)==0) ) { continue; }

        if($whereClientes <> ''){
          //RECORRER EL ARRAY DE LOS ID DE LOS TERCEROS Y ARMAR EL CUERPO DEL INFORME
          if($IMPRIME_XLS == 'true'){
            $tercero = utf8_encode($tercero);
          }

          if($rowx === end($arrayCuentas)){
            $bodyTable .=  "<tr>
                              <td>$cuenta</td>
                              <td>$descripcion</td>
                              <td>$nit_tercero</td>
                              <td>$tercero</td>
                              <td style='text-align:right;'>".number_format($saldoAnterior,2,$separador_decimales,$separador_miles)."</td>
                              <td style='text-align:right;'>".number_format($debe_actual,2,$separador_decimales,$separador_miles)."</td>
                              <td style='text-align:right;'>".number_format($haber_actual,2,$separador_decimales,$separador_miles)."</td>
                              <td style='text-align:right;'>".number_format($newSaldo,2,$separador_decimales,$separador_miles)."</td>
                            </tr>";
          }
        }
        else{
          if($rowx === end($arrayCuentas)){
            $bodyTable .=  "<tr>
                              <td>$cuenta</td>
                              <td>$descripcion</td>
                              <td style='text-align:right;'>".number_format($saldoAnterior,2,$separador_decimales,$separador_miles)."</td>
                              <td style='text-align:right;'>".number_format($debe_actual,2,$separador_decimales,$separador_miles)."</td>
                              <td style='text-align:right;'>".number_format($haber_actual,2,$separador_decimales,$separador_miles)."</td>
                              <td style='text-align:right;'>".number_format($newSaldo,2,$separador_decimales,$separador_miles)."</td>
                            </tr>";
          }
        }
      }
    }

    //SALDO
    $totalSaldoActual   = $totalSaldoAnterior + $totalDebe - $totalHaber;
    $totalSaldoAnterior = $totalSaldoAnterior;
    $totalDebe          = $totalDebe;
    $totalHaber         = $totalHaber;
    $totalSaldoActual   = $totalSaldoActual;

  }
?>
<style>
	.my_informe_Contenedor_Titulo_informe{
    float         :	left;
    width         :	100%;
    margin        :	0 0 10px 0;
    font-size     :	11px;
	}
	.my_informe_Contenedor_Titulo_informe_label{
    float       : left;
    width       : 130px;
    font-weight : bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
    float         :	left;
    width         :	210px;
    padding       :	0 0 0 5px;
    white-space   : nowrap;
    overflow      : hidden;
    text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
    float       : left;
    width       : 100%;
    font-size   : 16px;
    font-weight : bold;
	}
  .table{
    font-size       : 12px;
    width           : 100%;
    border-collapse : collapse;
  }
  .table thead{
    background : #999;
  }
  .table thead td{
    padding-left : 10px;
    height       : 30px;
    background   : #999;
    color        : #FFF;
  }
  .total{
    background  : #EEE;
    font-weight : bold;
  }
  .total td{
    border-top    : 1px solid #999;
    border-bottom : 1px solid #999;
    background    : #EEE;
    padding-left  : 10px;
    height        : 25px;
    font-weight   : bold;
    color         : #8E8E8E;
  }
</style>
<body>
  <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
    <div style="float:left; width:100%">
      <div style="float:left;width:100%; text-align:center">
        <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa;?></div>
        <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
        <div style="margin-bottom:8px;"><?php echo $subtitulo_cabecera; ?> Periodo del <?php echo $MyInformeFiltroFechaInicio; ?> al <?php echo $MyInformeFiltroFechaFinal; ?></div>
      </div>
    </div>
  </div>
  <div class="my_informe_Contenedor_Titulo_informe">
    <table class="table">
      <thead>
        <tr>
          <td width="65"><b>CUENTA</b></td>
          <td ><b>DESCRIPCION</b></td>
          <?php echo $td_head; ?>
          <td width="80"><b> SALDO ANTERIOR</b></td>
          <td width="80"><b> DEBITO</b></td>
          <td width="80"><b> CREDITO</b></td>
          <td width="80"><b> SALDO ACTUAL </b></td>
        </tr>
      </thead>
      <?php echo $bodyTable; ?>
      <tr class="total">
        <td align="center" colspan="2"><b>TOTAL</b></td>
        <?php echo $td_body; ?>
        <td style="text-align:right;"><b><?php echo number_format($totalSaldoAnterior,2,$separador_decimales,$separador_miles); ?></b></td>
        <td style="text-align:right;"><b><?php echo number_format($totalDebe,2,$separador_decimales,$separador_miles); ?></b></td>
        <td style="text-align:right;"><b><?php echo number_format($totalHaber,2,$separador_decimales,$separador_miles); ?></b></td>
        <td style="text-align:right;"><b><?php echo number_format($totalSaldoActual,2,$separador_decimales,$separador_miles); ?></b></td>
      </tr>
    </table>
  </div>
</body>
<script>
  <?php echo $script; ?>
</script>
<?php
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
  else{ $HOJA = 'LETTER-L'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
  else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
	if($IMPRIME_PDF == 'true'){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
            					'utf-8',      // mode - default ''
            					$HOJA,		    // format - A4, for example, default ''
            					12,				    // font size - default 0
            					'',				    // default font family
            					$MI,			    // margin_left
            					$MD,			    // margin right
            					$MS,			    // margin top
            					$ML,			    // margin bottom
            					10,				    // margin header
            					10,				    // margin footer
            					$ORIENTACION	// L - landscape, P - portrait
            				);
    $mpdf->SetProtection(array('print'));
    $mpdf->useSubstitutions = true;
    $mpdf->simpleTables     = true;
    $mpdf->packTableData    = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO'] . " // " . $_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetTitle("Balance De Comprobacion");
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA == 'true'){ $mpdf->Output("balance_de_comprobacion.pdf",'D'); }
    else{ $mpdf->Output("balance_de_comprobacion.pdf",'I'); }
		exit;
	}
  else{
    echo $texto;
  }
?>
