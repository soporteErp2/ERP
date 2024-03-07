<head>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
</head>
<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");

  // CONSULTAR ID DE EMPRESAS
  $sql = "SELECT id,nombre FROM empresas WHERE activo = 1 AND tipo_documento_nombre IS NOT NULL";
  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
    $optionEmpresas .= "<option value='$row[id]'>$row[nombre]</option>";
  }

  // CONSULTAR ID DE FACTURAS
  if(isset($id_empresa)){
    $sql = "SELECT
            	VF.id,
            	VF.numero_factura_completo
            FROM
            	ventas_facturas AS VF
            LEFT JOIN
            	ventas_facturas_configuracion AS VFC
            ON VF.id_configuracion_resolucion = VFC.id
            WHERE
            	VF.activo = 1
            AND VF.id_empresa = $id_empresa
            AND (VF.estado != 0 OR VF.estado != 3)
            AND VFC.tipo = 'FE'
            AND VF.numero_factura_completo != ''
            ORDER BY VF.id DESC
            LIMIT 0,1000";
    $query = $mysql->query($sql,$mysql->link);

    while($row = $mysql->fetch_array($query)){
      $optionFacturas .= "<option value='$row[id]'>$row[numero_factura_completo]</option>";
    }
  }
?>
<form method="post" action="">
  Empresa &nbsp;
  <select id="empresa" name="empresa" onchange="cargar_facturas(this.value)" required>
    <option value="">Seleccione...</option>
    <?php echo $optionEmpresas; ?>
  </select>
  Factura &nbsp;
  <select id="factura" name="factura" required>
    <option value="">Seleccione...</option>
    <?php echo $optionFacturas; ?>
  </select>
  Version Json &nbsp;
  <select id="version" name="version" required>
    <option value="">Seleccione...</option>
    <option value="v1">Version 1</option>
    <option value="v2">Version 2</option>
    <option value="v3">Version 3</option>
  </select>
  Pais &nbsp;
  <select id="pais" name="pais">
    <option value="colombia">Colombia</option>
    <option value="ecuador">Ecuador</option>
    <option value="peru">Peru</option>
  </select>
  Enviar &nbsp;
  <select id="enviar" name="enviar">
    <option value="si">Si</option>
    <option value="no">No</option>
  </select>
  <input type="submit">
</form>
<?php
  // CONSULTAR ID DE FACTURAS
  if(isset($id_empresa)){
    echo "<script> document.getElementById('empresa').value = $id_empresa; </script>";
  }
?>
<script type="text/javascript">
  $('#empresa').select2();
  $('#factura').select2();
  $('#version').select2();
  $('#pais').select2();
  $('#enviar').select2();

  if(localStorage.id_factura_debug_json != "" && document.getElementById('empresa').value != ""){
    $("#factura").val(localStorage.id_factura_debug_json).trigger('change');
  }

  if(localStorage.version_debug_json != ""){
    $("#version").val(localStorage.version_debug_json).trigger('change');
  }

  if(localStorage.pais_debug_json != ""){
    $("#pais").val(localStorage.pais_debug_json).trigger('change');
  }

  if(localStorage.enviar_debug_json != ""){
    $("#enviar").val(localStorage.enviar_debug_json).trigger('change');
  }

  function cargar_facturas(value){
    if(value == ""){
      return;
    }
    else{
      window.location = 'obtener_json.php?id_empresa=' + value;
    }
  }
</script>
<?php
  if($empresa != "" && $factura != "" && $version != "" && $pais != "" && $enviar != ""){
    if($pais == "colombia"){
      if($version == "v1"){
        enviarFacturaDIAN_V1($factura,$empresa,$enviar,$mysql);
      }
      else if($version == "v2"){
        enviarFacturaDIAN_V2($factura,$empresa,$enviar,$mysql);
      }
      else if($version == "v3"){
        enviarFacturaDIAN_V3($factura,$empresa,$enviar,$mysql);
      }
    }
    else if($pais == "ecuador"){
      if($version == "v1"){
        enviarFacturaSRI_V1($factura,$empresa,$enviar,$mysql);
      }
      else{
        echo "no existe";
        exit;
      }
    }
    else if($pais == "peru"){
      echo "no existe";
      exit;
    }

    echo "<script>
            localStorage.id_factura_debug_json = '$factura';
            localStorage.version_debug_json    = '$version';
            localStorage.pais_debug_json       = '$pais';
            localStorage.enviar_debug_json     = '$enviar';

            if('$factura' != '' && document.getElementById('empresa').value != ''){
              $('#factura').val('$factura').trigger('change');
            }

            if('$version' != ''){
              $('#version').val('$version').trigger('change');
            }

            if('$pais' != ''){
              $('#pais').val('$pais').trigger('change');
            }

            if('$enviar' != ''){
              $('#enviar').val('$enviar').trigger('change');
            }
          </script>";
  }

  function enviarFacturaDIAN_V1($factura,$empresa,$enviar,$mysql){
    include("ClassFacturaJSON.php");

		$facturaJSON = new ClassFacturaJSON($mysql);
	  $facturaJSON->obtenerDatos($factura,$empresa);
	  $facturaJSON->construirJSON();
		$result 		 = $facturaJSON->imprimirJSON();
    echo "<textarea readonly style='width: 1000px; height: 700px;'>$result</textarea>";
  }

  function enviarFacturaDIAN_V2($factura,$empresa,$enviar,$mysql){
    include("ClassFacturaJSON_V2.php");

		$facturaJSON = new ClassFacturaJSON_V2($mysql);
	  $facturaJSON->obtenerDatos($factura,$empresa);
    $facturaJSON->construirJSON();
    $resultPrint = $facturaJSON->imprimirJSON();

    if($enviar == "si"){
      $resultFinal = $facturaJSON->enviarJSON();
    }

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

  function enviarFacturaDIAN_V3($factura,$empresa,$enviar,$mysql){
    include("ClassFacturaJSON_V3.php");

		$facturaJSON = new ClassFacturaJSON_V3($mysql);
	  $facturaJSON->obtenerDatos($factura,$empresa);
    $facturaJSON->construirJSON();
    $resultPrint = $facturaJSON->imprimirJSON();

    if($enviar == "si"){
      $resultFinal = $facturaJSON->enviarJSON();
    }

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

  function enviarFacturaSRI_V1($factura,$empresa,$enviar,$mysql){
    include("ClassFacturaJSON_V1_EC.php");

		$facturaJSON = new ClassFacturaJSON_V1_EC($mysql);
	  $facturaJSON->obtenerDatos($factura,$empresa);
    $facturaJSON->construirJSON();
    $resultPrint = $facturaJSON->imprimirJSON();

    if($enviar == "si"){
      $resultFinal = $facturaJSON->enviarJSON();
    }

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
