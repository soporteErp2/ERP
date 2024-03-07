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
              CF.id,
              CONCAT(CF.prefijo_factura,' ', CF.consecutivo)  as numero_factura_completo
            FROM
              compras_facturas AS CF
            INNER JOIN resolucion_documento_soporte AS RDS ON CF.id_resolucion = RDS.id 
            WHERE
              CF.activo = 1 
            AND CF.id_empresa = $id_empresa
            AND ( CF.estado != 0 OR CF.estado != 3 ) 
            AND CF.prefijo_factura != '' 
            ORDER BY
              CF.id DESC 
            LIMIT 0, 10000";
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

  if(localStorage.id_factura_debug_json != "" && document.getElementById('empresa').value != ""){
    $("#factura").val(localStorage.id_factura_debug_json).trigger('change');
  }

  if(localStorage.version_debug_json != ""){
    $("#version").val(localStorage.version_debug_json).trigger('change');
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
  if($empresa != "" && $factura != "" && $version != ""){
    if($version == "v1"){
      enviar_documento_soporte_DIAN($factura,$empresa,$mysql);
    }

    echo "<script>
            localStorage.id_factura_debug_json = '$factura';
            localStorage.version_debug_json    = '$version';

            if('$factura' != '' && document.getElementById('empresa').value != ''){
              $('#factura').val('$factura').trigger('change');
            }

            if('$version' != ''){
              $('#version').val('$version').trigger('change');
            }
          </script>";
  }

  function enviar_documento_soporte_DIAN($factura,$empresa,$mysql){
    include("ClassSupportDocument.php");

		$facturaJSON = new ClassSupportDocument($factura,$mysql);
    $result = $facturaJSON->sendInvoice(true);

    echo "<textarea readonly style='width: 1000px; height: 700px;'>$result</textarea>";
  }

?>
