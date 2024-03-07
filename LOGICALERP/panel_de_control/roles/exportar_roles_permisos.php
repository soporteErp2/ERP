<?php
include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");

  if(!isset($_SESSION['EMPRESA']) && !isset($_SESSION['SUCURSAL'])){
    echo "Error de conexion";
    exit;
  }

  $id_empresa = $_SESSION['EMPRESA'];

  $sqlRP = "SELECT
              ER.nombre AS nombre_rol,
              EP.nombre AS nombre_permiso,
              EP.nivel
            FROM
              empleados_roles AS ER
            INNER JOIN empleados_roles_permisos AS ERP ON ER.id = ERP.id_rol
            INNER JOIN empleados_permisos AS EP ON ERP.id_permiso = EP.id
            WHERE
              ER.activo = 1
            AND
              ER.id_empresa = $id_empresa
            ORDER BY
              ER.id, EP.orden ASC";
  $queryRP = $mysql->query($sqlRP,$mysql->link);
  while($row = $mysql->fetch_array($queryRP)){
    $tbody .=  "<tr>
                  <td>$row[nombre_rol]</td>
                  <td>$row[nombre_permiso]</td>
                  <td>$row[nivel]</td>
                </tr>";
  }

  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=Informe_Roles_Permisos_" . date("Y_m_d") . ".xls");
  header("Pragma: no-cache");
  header("Expires: 0");
?>
<table>
  <thead>
    <tr>
      <td colspan="3" style="text-align:center;"><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:center;"><b>INFORME ROLES Y PERMISOS</b></td>
    </tr>
    <tr>
      <td colspan="3" style="text-align:center;"><b>FECHA: <?php echo date('Y-m-d'); ?></b></td>
    </tr>
  </thead>
</table>
<table>
  <thead>
    <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
      <td style='text-align:center;'><b>ROL</b></td>
      <td style='text-align:center;'><b>PERMISO</b></td>
      <td style='text-align:center;'><b>NIVEL</b></td>
    </tr>
  </thead>
  <tbody>
    <?php echo $tbody; ?>
  </tbody>
</table>
