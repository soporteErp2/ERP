<?php
//Id de las facturas a imprimir
$ids_facturas = []; 
$ruta_base = "imprimir_factura_compra.php"; // Ruta del script que genera el PDF
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar Facturas</title>
</head>
<body>
    <h2>Descargar Facturas en Serie</h2>
    <button onclick="descargarFacturas()">Descargar Facturas</button>

    <script>
        let ids = <?php echo json_encode($ids_facturas); ?>;
        let rutaBase = "<?php echo $ruta_base; ?>";

        async function descargarFacturas() {
            for (let id of ids) {
                let url = `${rutaBase}?id=${id}&PDF_GUARDA=TRUE`;
                window.open(url, "_blank");

                // Esperar 2s antes de la siguiente descarga 
                await new Promise(resolve => setTimeout(resolve, 2000));
            }
        }
    </script>
</body>
</html>
