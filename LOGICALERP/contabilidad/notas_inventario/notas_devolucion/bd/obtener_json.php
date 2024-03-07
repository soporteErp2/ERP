<head>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
</head>
<?php
  include("../../../../../configuracion/conectar.php");
  include("../../../../../configuracion/define_variables.php");

  // CONSULTAR ID DE EMPRESAS
  $sql = "SELECT id,nombre FROM empresas WHERE activo = 1 AND tipo_documento_nombre IS NOT NULL";
  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
    $optionEmpresas .= "<option value='$row[id]'>$row[nombre]</option>";
  }

  // CONSULTAR ID DE FACTURAS
  if(isset($id_empresa)){
    $sql = "SELECT
            	DV.id,
            	DV.consecutivo,
              DV.sucursal
            FROM
            	devoluciones_venta AS DV
            LEFT JOIN
              ventas_facturas AS VF
            ON
              DV.id_documento_venta = VF.id
            LEFT JOIN
            	ventas_facturas_configuracion AS VFC
            ON VF.id_configuracion_resolucion = VFC.id
            WHERE
            	DV.activo = 1
            AND DV.id_empresa = $id_empresa
            AND (DV.estado != 0 OR DV.estado != 3)
            AND VFC.tipo = 'FE'
            ORDER BY DV.id DESC";
    $query = $mysql->query($sql,$mysql->link);

    while($row = $mysql->fetch_array($query)){
      $optionDevoluciones .= "<option value='$row[id]'>$row[sucursal] - $row[consecutivo]</option>";
    }
  }
?>
<form method="post" action="">
  Empresa &nbsp;
  <select id="empresa" name="empresa" onchange="cargar_devoluciones(this.value)" required>
    <option value="">Seleccione...</option>
    <?php echo $optionEmpresas; ?>
  </select>
  Devolucion &nbsp;
  <select id="devolucion" name="devolucion" required>
    <option value="">Seleccione...</option>
    <?php echo $optionDevoluciones; ?>
  </select>
  Version Json &nbsp;
  <select id="version" name="version" required>
    <option value="">Seleccione...</option>
    <option value="v1">Version 1</option>
    <option value="v2">Version 2</option>
  </select>
  <input type="submit">
</form>
<?php
  // CONSULTAR ID DE DEVOLUCIONES
  if(isset($id_empresa)){
    echo "<script> document.getElementById('empresa').value = $id_empresa; </script>";
  }
?>
<script type="text/javascript">
  $('#empresa').select2();
  $('#devolucion').select2();
  $('#version').select2();

  if(localStorage.id_devolucion_debug_json != "" && document.getElementById('empresa').value != ""){
    $("#devolucion").val(localStorage.id_devolucion_debug_json).trigger('change');
  }

  if(localStorage.version_debug_json != ""){
    $("#version").val(localStorage.version_debug_json).trigger('change');
  }

  function cargar_devoluciones(value){
    if(value == ""){
      return;
    }
    else{
      window.location = 'obtener_json.php?id_empresa=' + value;
    }
  }
</script>
<?php
  if($empresa != "" && $devolucion != "" && $version != ""){
    if($version == "v1"){
      enviarDevolucionDIAN($devolucion,$empresa,$mysql);
    }
    else if($version == "v2"){
      enviarDevolucionDIAN_V2($devolucion,$empresa,$mysql);
    }

    echo "<script>
            localStorage.id_devolucion_debug_json = '$devolucion';
            localStorage.version_debug_json    = '$version';

            if('$devolucion' != '' && document.getElementById('empresa').value != ''){
              $('#devolucion').val('$devolucion').trigger('change');
            }

            if('$version' != ''){
              $('#version').val('$version').trigger('change');
            }
          </script>";
  }

  function enviarDevolucionDIAN($devolucion,$empresa,$sucursal,$mysql){
    include("ClassDevolucionJSON.php");

		$devolucionJSON 	= new ClassDevolucionJSON($mysql);
	  $devolucionJSON->obtenerDatos($devolucion,$empresa,$sucursal);
	  $devolucionJSON->construirJSON();
		$result 			    = $devolucionJSON->imprimirJSON();
    echo "<textarea readonly style='width: 1000px; height: 700px;'>$result</textarea>";
  }

  function enviarDevolucionDIAN_V2($devolucion,$empresa,$mysql){
    include("ClassDevolucionJSON_V2.php");

		$devolucionJSON = new ClassDevolucionJSON_V2($mysql);
	  $devolucionJSON->obtenerDatos($devolucion,$empresa);
    $devolucionJSON->construirJSON();
    $resultPrint = $devolucionJSON->imprimirJSON();
	  // $resultFinal = $devolucionJSON->enviarJSON();
    echo "<table>
            <tr>
              <td>
                <textarea readonly style='width: 1000px; height: 650px;'>$resultPrint</textarea>
              </td>
              <td>
                <textarea readonly style='width: 490px; height: 325px;'>$resultFinal[validar]</textarea>
                <textarea readonly style='width: 490px; height: 325px;'>$resultFinal[comprobante]</textarea>
              </td>
            <tr>
          </table>";
  }

?>
