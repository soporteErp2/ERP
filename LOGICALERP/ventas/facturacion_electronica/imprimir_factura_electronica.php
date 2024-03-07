<?php
  include("../../../configuracion/conectar.php");
  include("../../../configuracion/define_variables.php");
  include("bd/bd.php");

  $pdf = imprimirFacturaElectronica($numeroDocumento,$dominio,$_SESSION['EMPRESA'],$mysql);
  
  if($pdf['PdfBase64'] == null){
    echo "<script>
            alert('$pdf[MensajeRespuesta]');
            console.log('$pdf[endpoint_facse]')
            // window.close();
          </script>";
    exit;
  }
  else{
    // CREAR EL PDF Y DESCARGARLO
    file_put_contents($numeroDocumento.".pdf", base64_decode($pdf['PdfBase64'],true));
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=\"$numeroDocumento.pdf\"");
    readfile($numeroDocumento.".pdf");

    // ELIMINAR EL PDF GENERADO
    unlink($numeroDocumento.".pdf");
  }
?>
