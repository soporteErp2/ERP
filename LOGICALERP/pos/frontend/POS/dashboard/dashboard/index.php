<?php

  include '../../../../../../configuracion/conectar.php';
  include '../../../../../../configuracion/define_variables.php';
  $id_empresa= $_SESSION["EMPRESA"];
  $fecha = date("Y-m-d");
  // $fecha = "2020-03-01";

  $sql="SELECT
            VP.id_seccion,
            VP.seccion,
            VP.consecutivo,
            VP.id,
            VP.fecha_documento,
            VP.seccion,
            VP.mesa,
            VP.documento_cliente,
            VP.cliente,
            VP.usuario,
            IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,
            (SUM(VPP.valor)-VP.valor_propina) / 1.08 AS subtotal,
            ((SUM(VPP.valor)-VP.valor_propina) / 1.08) * 0.08 AS impuesto,
            SUM(VPP.valor) AS valor,
            VP.valor_propina AS valor_propina,
            SUM(VP.valor_descuento) AS valor_descuento,
            VP.estado
          FROM
            ventas_pos AS VP
          INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
          INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
          WHERE
            VP.activo = 1
          AND (VP.estado = 1 OR VP.estado=2)
          AND (CP.tipo <> 'Cortesia' )
          AND VP.fecha_documento >= '$fecha' AND VP.fecha_documento <= '$fecha'
          GROUP BY VP.id ";
          // AND VP.fecha_documento BETWEEN '$fecha' AND '$fecha'
      $query=$mysql->query($sql);
      while ($row=$mysql->fetch_assoc($query)) {
        // if ($data['group_by']=='ambiente') {
          $arrayTemp[$row['id_seccion']]['id_seccion']      = $row['id_seccion'];
          $arrayTemp[$row['id_seccion']]['seccion']         = $row['seccion'];
          $arrayTemp[$row['id_seccion']]['cod_tx']          = $row['cod_tx'];
          $arrayTemp[$row['id_seccion']]['subtotal']        += $row['subtotal'];
          $arrayTemp[$row['id_seccion']]['impuesto']        += $row['impuesto'];
          $arrayTemp[$row['id_seccion']]['valor']           += $row['valor'];
          $arrayTemp[$row['id_seccion']]['valor_propina']   += $row['valor_propina'];
          $arrayTemp[$row['id_seccion']]['valor_descuento'] += $row['valor_descuento'];

          $ventaTotalSinIva += $row['subtotal'];
        // }
        // else{
        //   $arrayTemp[$row['id']]['id']                = $row['id'];
        //   $arrayTemp[$row['id']]['consecutivo']       = $row['consecutivo'];
        //   $arrayTemp[$row['id']]['fecha_documento']   = $row['fecha_documento'];
        //   $arrayTemp[$row['id']]['seccion']           = $row['seccion'];
        //   $arrayTemp[$row['id']]['mesa']              = $row['mesa'];
        //   $arrayTemp[$row['id']]['documento_cliente'] = $row['documento_cliente'];
        //   $arrayTemp[$row['id']]['cliente']           = $row['cliente'];
        //   $arrayTemp[$row['id']]['usuario']           = $row['usuario'];
        //   $arrayTemp[$row['id']]['tipo']              = $row['tipo'];
        //   $arrayTemp[$row['id']]['subtotal']          += $row['subtotal'];
        //   $arrayTemp[$row['id']]['impuesto']          += $row['impuesto'];
        //   $arrayTemp[$row['id']]['valor']             += $row['valor'];
        //   $arrayTemp[$row['id']]['valor_propina']     += $row['valor_propina'];
        //   $arrayTemp[$row['id']]['valor_descuento']   += $row['valor_descuento'];
        // }
      }

      $sql="SELECT
              id,
              nombre
            FROM ventas_pos_secciones
            WHERE activo=1
            AND id_empresa=$id_empresa
            AND restaurante='Si'";
      $query=$mysql->query($sql);
      while ($row = $mysql->fetch_assoc($query)) {
        $restPercent = $arrayTemp[$row['id']]['subtotal']/$ventaTotalSinIva*100;
          $restPercentUnformat = $restPercent;
          $restPercent = number_format($restPercent,2,",",".");

          $typeProgressBar = "bg-danger";
          $typeCard        = "border-left-danger";
          if ($restPercentUnformat>=30 && $restPercentUnformat<=49) {
            $typeProgressBar = "bg-warning";
            $typeCard        = "border-left-warning";

          }
          else if ($restPercentUnformat>=50) {
            $typeProgressBar = "bg-success";
            $typeCard        = "border-left-success";
          }

          $percentCards .= "
                            <h4 class='small font-weight-bold'>$row[nombre] <span class='float-right'>$restPercent%</span></h4>
                            <div class='progress mb-4'>
                              <div class='progress-bar $typeProgressBar' role='progressbar' style='width: $restPercentUnformat%' aria-valuenow='$restPercentUnformat' aria-valuemin='0' aria-valuemax='$ventaTotalSinIva'></div>
                            </div>
                        ";


        $ventaDia = number_format($arrayTemp[$row['id']]['subtotal'],0,",",".");
        $cards .= "
                    <div class='col-4 mb-4'>
                      <div class='card $typeCard shadow py-2'>
                        <div class='card-body'>
                          <div class='row no-gutters align-items-center'>
                            <div class='col mr-2'>
                              <div class='text-xs font-weight-bold text-primary text-uppercase mb-1'>$row[nombre]</div>
                              <div class='h5 mb-0 font-weight-bold text-gray-800' id='tarifaPromedioDia'>$ $ventaDia</div>
                            </div>
                            <div class='col-auto'>
                              <i class='material-icons' style='color:#dddfeb; font-size: 35px;'>room_service</i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  ";
                // $this->arraySeccionesFormato['items'][$id_seccion]['percent_1']  = $arrayResul['valor1']/$this->arraySeccionesFormato['items'][$arrayResul['codigo_seccion_padre']]['valor1']*100;

      }

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Custom fonts for this template-->
  <link href="lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="lib/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4" style="margin-top: 10px;">
            <h1 class="h3 mb-0 text-gray-800">Indicadores de <?= $fecha; ?></h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="reloadDashboard()">
                <i class="fas fa-redo fa-sm text-white-50"></i> Actualizar
            </a>
          </div>

          <div class='col-4 mb-4'>
            <div class='card border-left-primary shadow py-2'>
              <div class='card-body'>
                <div class='row no-gutters align-items-center'>
                  <div class='col mr-2'>
                    <div class='text-xs font-weight-bold text-primary text-uppercase mb-1'>Venta total del dia (sin impuesto) </div>
                    <div class='h5 mb-0 font-weight-bold text-gray-800' id='tarifaPromedioDia'>$<?= number_format($ventaTotalSinIva,0,",",".") ?></div>
                  </div>
                  <div class='col-auto'>
                    <i class='material-icons' style='color:#dddfeb; font-size: 35px;'>business</i>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-sm-flex align-items-center justify-content-between mb-4" >
            <h1 class="h5 mb-0 text-gray-800">Ventas por secciones (sin impuestos) </h1>
          </div>

          <!-- Content Row -->
          <div class="row">
            <!-- VENTAS SIN IVA DEL DIA -->
            <?= $cards; ?>

          </div>

          <!-- Content Row -->
          <div class="row">

            <!-- Content Column -->
            <div class="col-lg-6 mb-4">

              <!-- Project Card Example -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Relacion porcentual secciones - venta total del dia</h6>
                </div>
                <div class="card-body">
                  <?= $percentCards; ?>
                  <!-- <h4 class="small font-weight-bold">Server Migration <span class="float-right">20%</span></h4>
                  <div class="progress mb-4">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <h4 class="small font-weight-bold">Sales Tracking <span class="float-right">40%</span></h4>
                  <div class="progress mb-4">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <h4 class="small font-weight-bold">Customer Database <span class="float-right">60%</span></h4>
                  <div class="progress mb-4">
                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <h4 class="small font-weight-bold">Payout Details <span class="float-right">80%</span></h4>
                  <div class="progress mb-4">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <h4 class="small font-weight-bold">Account Setup <span class="float-right">Complete!</span></h4>
                  <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                  </div> -->
                </div>
              </div>
            </div>

          </div>

    </div>
  <!-- Bootstrap core JavaScript-->
  <script src="lib/vendor/jquery/jquery.min.js"></script>
  <script src="lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="lib/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="lib/js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="lib/vendor/chart.js/Chart.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="lib/js/demo/chart-area-demo.js"></script>
  <script src="lib/js/demo/chart-pie-demo.js"></script>

</body>
<script>

    var reloadDashboard = ()=>{

        $W.Load({
            idApply : 'win-tab-body-dashboard',
            url     : 'dashboard/index.php',
            // params  : {
            //     param1 :,
            //     param2 :
            // }
        })
    }

</script>
</html>
