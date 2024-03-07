<?php
	include('../../configuracion/conectar.php');

$sql = "SELECT CF.nit, 
               CONCAT('!', CFI.codigo) as codigo, 
               CFC.puc as cuenta
        FROM compras_facturas as CF

        INNER JOIN compras_facturas_inventario as CFI 
        ON CFI.id_factura_compra = CF.id

        INNER JOIN items_cuentas as CFC
        ON CFI.codigo = CFC.codigo_items

        WHERE
        CF.activo = 1
        AND
        (CF.estado = 1 OR CF.estado = 2)
        AND CF.id_empresa = 47
        AND CFC.descripcion = 'impuesto'";
        
$query=$mysql->query($sql,$mysql->link);
while ($row=$mysql->fetch_array($query)) {

        $body .= "<tr>
                    <td>$row[nit]</td>
                    <td>$row[codigo]</td>  
                    <td>$row[cuenta]</td>  
                </tr>";
}
    
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Informe items por proveedor".date("Y_m_d").".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>

<html>
<head>
	<title></title>
</head>
<body>
	<table>
		<tr>
			<td>Nit</td>
			<td>Codigo</td>
			<td>Cuenta</td>
		</tr>
		<?php echo $body; ?>
	</table>
</body>
</html>