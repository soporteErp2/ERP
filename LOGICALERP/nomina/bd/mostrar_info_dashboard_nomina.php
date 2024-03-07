<?php
  	include("../../../configuracion/conectar.php");
  	include("../../../configuracion/define_variables.php");
  	include("../config_var_global.php");

    $id_empresa = $_SESSION['EMPRESA'];
    $sucursal   = ($filtro_sucursal == 0)? "" : " AND id_sucursal = '$filtro_sucursal' " ;

    if ($opc=='por_vencer' || $opc=='en_vacaciones' || $opc=='vencidos') {
      $fechaVencimiento = date('Y-m-d', strtotime('+1 month'));
      $hoy              = date('Y-m-d');
      switch ($opc) {
        case 'por_vencer':
          $whereSql = "(estado=0 OR estado=2) AND fecha_fin_contrato BETWEEN '$hoy' AND '$fechaVencimiento' AND id_empresa = $id_empresa  AND nombre_empleado<>'' $sucursal";
          break;
        case 'en_vacaciones':
          $whereSql = " estado=2  AND id_empresa = $id_empresa $sucursal AND nombre_empleado<>'' ";
          break;
        case 'vencidos':
          $whereSql = "(estado=0 OR estado=2) AND fecha_fin_contrato <= '$hoy' AND id_empresa = $id_empresa AND nombre_empleado<>'' AND tipo_contrato<>'TERMINO INDEFINIDO '  $sucursal";
          break;

      }

      $arrCampos = array(
                        'titulos' => array(
                                            '1' => 'DOCUMENTO',
                                            '2' => 'EMPLEADO',
                                            '3' => 'FECHA INICIO',
                                            '4' => 'FECHA FIN',
                                            '5' => 'TIPO CONTRATO',
                                          ),
                        'campos' => array(
                                            '1' => 'documento_empleado',
                                            '2' => 'nombre_empleado',
                                            '3' => 'fecha_inicio_contrato',
                                            '4' => 'fecha_fin_contrato',
                                            '5' => 'tipo_contrato',
                                          ),
                      );
    }
    else{
      $whereSql   = " AND fecha_documento BETWEEN '$fechai' AND '$fechaf' $sucursal ";
      $whereSql   = ($opc=='cancel')? " estado = 3 AND consecutivo > 0 AND activo = 1 AND id_empresa = $id_empresa $whereSql " : " estado = 0 AND consecutivo > 0 AND activo = 1 AND id_empresa = $id_empresa $whereSql" ;

      $arrCampos = array(
                        'titulos' => array(
                                            '1' => 'CONSECUTIVO',
                                            '2' => 'FECHA',
                                            '3' => 'SUCURSAL',
                                            '4' => 'USUARIO',
                                          ),
                        'campos' => array(
                                            '1' => 'consecutivo',
                                            '2' => 'fecha_documento',
                                            '3' => 'sucursal',
                                            '4' => 'usuario',
                                          ),
                      );
    }

    $sql="SELECT * FROM $tabla WHERE $whereSql";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
      $campos = "";
      foreach ($arrCampos['campos'] as $key => $value) {
        $campos .= "<td>$row[$value]</td>";
      }
      $bodyTable .="<tr>
                    $campos
                  </tr>";
    }


?>
<div>

  <table class="table-grilla">
    <thead>
      <tr>
        <?php
          foreach ($arrCampos['titulos'] as $key => $value) {
            echo "<td>$value</td>";
          }
        ?>
      </tr>
    </thead>
    <tbody>
      <?php echo $bodyTable; ?>
    </tbody>
  </table>

</div>