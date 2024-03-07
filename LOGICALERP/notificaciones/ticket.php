<?php

$sqlItems="SELECT * FROM ventas_pos_inventario WHERE id_pos=10 AND activo=1";
$queryItems=mysql_query($sqlItems,$link);
$bodyTicket='';
while ($rowItems=mysql_fetch_array($queryItems)) {
	$bodyTicket.='<tr width="200">
					<td style="width:33px;">'.$rowItems['codigo_barras'].'</td>
					<td width="100">'.$rowItems['nombre'].'</td>
					<td width="33">'.$rowItems['cantidad'].'</td>
					<td width="33">'.($rowItems['cantidad']*$rowItems['precio_venta']).'</td>
				</tr>';
}

?>
<html>
<head>
	<title></title>
</head>
<body >
	<table border="0" align="center" width="260" style="text-align:center;font-size:10px;" >
		<tr><td><?php //echo $_SESSION['EMPRESA']; ?>LogicalSoft S.A.S</td></tr>
		<tr><td>NIT: 900467785 Regimen Comun</td></tr>
		<tr><td>Avenida norte</td></tr>
	</table>
	<table border="1" align="center" width="260" style="font-size:20px;system">
		<tr><td colspan="4"> consecutivo: 2587410325</td></tr>
		<tr><td colspan="4"> fecha: 01/01/2014  Hora: 01:00 PM</td></tr>
		<tr><td colspan="4"> vendedor: xxxxxx xxxxxxx</td></tr>
		<tr>
			<td >Codigo</td>
			<td >Item</td>
			<td >Cant.</td>
			<td >Total</td>
		</tr>
		<?php echo $bodyTicket; ?>
	</table>
	<br>
	<table  border="1" align="center" width="260" style="font-size:10px;">
		<tr><td colspan="2" style="text-align:center;">TOTALES</td></tr>
		<tr><td style="width:100px;">Subtotal</td>	<td style="width:200px;text-align:right;">1</td></tr>
		<tr><td style="width:100px;">Iva</td>		<td style="width:200px;text-align:right;">1</td></tr>
		<tr><td style="width:100px;">Total</td>		<td style="width:200px;text-align:right;">2</td></tr>
	</table>
	<br>
	<table  border="1" align="center" width="260" style="font-size:10px;">
		<tr><td >Resolucion: 5417885652411</td></tr>
		<tr><td >de 1 al 1000</td>	</tr>
	</table>

</body>
</html> 

<!-- <html>
                <body>
                    <table border="0" align="center" width="200" style="text-align:center;font-size:5px;font-family: Arial, Helvetica, sans-serif;">
                        <tr><td>'.$_SESSION['NOMBREEMPRESA'].'</td></tr>
                        <tr><td>NIT: '.$_SESSION['NITEMPRESA'].' </td></tr>
                        <tr><td>Avenida norte</td></tr>
                    </table>
                    <table  align="center" style="font-size:5px;font-family: Arial, Helvetica, sans-serif;">
                        <tr><td colspan="4"> consecutivo: '.$consecutivoFactura.'</td></tr>
                        <tr><td colspan="4"> fecha: '.date("Y-m-d").'  Hora: '.date("H-m-s").'</td></tr>
                        <tr><td colspan="4"> vendedor: '.$_SESSION['NOMBREFUNCIONARIO'].'</td></tr>
                        <tr style="border-top:1px solid;border-bottom:1px solid;">
                            <td >Codigo</td>
                            <td >Item</td>
                            <td >Cant.</td>
                            <td >Total</td>
                        </tr>
                        <?php echo $bodyTicket; ?>
                    </table>                    
                    <table   align="center" width="200" style="font-size:5px;font-family: Arial, Helvetica, sans-serif;" >
                        <tr><td colspan="2" style="text-align:center;border-top:1px solid;border-bottom:1px solid;">TOTALES</td></tr>
                        <tr><td width="50" >Subtotal</td>  <td style="width:50px;text-align:right;">$ '.$acumSubtotal.'</td></tr>
                        <tr><td width="50" >Iva</td>       <td style="width:50px;text-align:right;">$ '.$acumIva.'</td></tr>
                        <tr><td width="50" >Total</td>     <td style="width:50px;text-align:right;">$ '.$totalPos.'</td></tr>
                    </table>
                    <br>
                    <table   align="center"  width="200" style="font-size:5px;font-family: Arial, Helvetica, sans-serif;">
                    <tr><td style="text-align:center;border-top:1px solid;border-bottom:1px solid;" >INFORMACION TRIBUTARIA</td></tr>
                        <tr><td >Resolucion: '.$resolucion.' de '.$fecha_resolucion_dian.'</td></tr>
                        <tr><td >'.$numerosDian.'</td>  </tr>
                    </table>  
                    <br>              
                </body>
                </html> -->
