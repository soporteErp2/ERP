<?php 
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if ($_SESSION['EMPRESA']=='') { return; }

	header("Content-Type: application/vnd.ms-excel");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=reporte_ingreso_orden.xls");
	
	//consultar el nombre de la empresa
	$consulta_empresa=mysql_query("SELECT nombre, nit_completo, direccion, ciudad FROM empresas WHERE id=".$_SESSION['EMPRESA'],$link);
	$array_empresa=mysql_fetch_array($consulta_empresa);
	
	$sqlOrden = "SELECT consecutivo, fecha_inicio, nit, proveedor, observacion FROM compras_ordenes WHERE id='$id' AND activo=1 LIMIT 0,1";
	$consulOrden=mysql_query($sqlOrden,$link);
	if (!$consulOrden){die('no valido informe'.mysql_error());}
	$arrayOrden = mysql_fetch_array($consulOrden);

	//consultamos los articulos de esta orden
	$sqlArticulos="SELECT cantidad, 
						  costo_unitario, 
						  descuento, codigo, 
						  nombre, 
						  nombre_unidad_medida, 
						  cantidad_unidad_medida,
						  tipo_descuento,
						  valor_impuesto FROM compras_ordenes_inventario WHERE id_orden_compra='$id'";
	$queryArticulos=mysql_query($sqlArticulos,$link);
		//descuento general de la orden
	$subtotalOrdenCompra = 0;
	$ivaOrdenCompra = 0;
	$ordenCompraDescuento = 0;
	while ($arrayArticulos= mysql_fetch_array($queryArticulos)) {
		$descuento   = ($arrayArticulos['tipo_descuento'] == 'porcentaje')? 
						$arrayArticulos["descuento"] :
						$arrayArticulos["cantidad"]*$arrayArticulos["costo_unitario"]*$arrayArticulos["descuento"];
		
		$subtotal = $arrayArticulos["cantidad"]*$arrayArticulos["costo_unitario"] - $descuento;

		$articulos	.='<tr>
							<td>'.$arrayArticulos["codigo"].'</td>
							<td>'.$arrayArticulos["nombre"].'</td>
							<td>'.$arrayArticulos["nombre_unidad_medida"].' x '.$arrayArticulos["cantidad_unidad_medida"].'</td>
							<td>'.number_format($arrayArticulos["cantidad"],2,$separador_decimales,$separador_miles).'</div>
							<td>'.number_format($descuento,2,$separador_decimales,$separador_miles).'</td>
							<td>'.number_format($arrayArticulos["costo_unitario"],2,$separador_decimales,$separador_miles).'</td>
						 	<td>'.number_format($subtotal,2,$separador_decimales,$separador_miles).'</td> 
						</tr>';
			
		// calculamos el subtotal de la orden donde subtotalOrdenCompra= subtotal del articulo mas la misma variable
		$subtotalOrdenCompra += $subtotal;
		//iva acumulado de cada articulo
		$ivaOrdenCompra += $subtotal*$arrayArticulos['valor_impuesto']/100;
		//generamos un acumulado de los descuentos de los articulo                     
		$ordenCompraDescuento=$descuento;			
	}
		
		$totalOrdenCompra=($subtotalOrdenCompra - $ordenCompraDescuento)+$ivaOrdenCompra;

		/*---------------------------------------------------------------formulario original------------------------------------------------------------------*/
		$contenido='	
						<table>
							<tr>
									<td colspan="7" align="center"><b>'.$array_empresa["nombre"].'</b></td>
							</tr>
							<tr>
									<td colspan="7" align="center"><b> ORDEN DE COMPRA </b></td>
							</tr>
							<tr>
									<td colspan="7" align="center">'.$array_empresa["direccion"].'</td>
							</tr>
							<tr>
									<td colspan="7" align="center">'.$array_empresa["ciudad"].'</td>
							</tr>
							<tr></tr><tr></tr>
							<tr>
									<td ><b>Proveedor:</b></td>
									<td>'.$arrayOrden["proveedor"].'</td>
							</tr>
							<tr>
									<td><b>Nit: </b></td>
									<td>'.$arrayOrden["nit"].'</td>
							</tr>

							<tr>
									<td><b>Fecha</b></td>
									<td>'.$arrayOrden["fecha_inicio"].'</td>
							</tr>
							<tr>
									<td><b>No. Orden</b></div>
									<td>'.$arrayOrden["consecutivo"].'</td>
							</tr>

						</table>
						<br>
						<table border="1"  >
							<tr border="1">
								<td bgcolor="#B9BABF" >Codigo interno</td>						
								<td bgcolor="#B9BABF" >Articulo</td>						
								<td bgcolor="#B9BABF" >Unidad</td>						
								<td bgcolor="#B9BABF" >Cantidad</td>					
								<td bgcolor="#B9BABF" >Descuento</td>										
								<td bgcolor="#B9BABF" >Valor unitario</td>
								<td bgcolor="#B9BABF" >Subtotal</td>
							</tr>

						'.$articulos.'
						</table>

						<br/>					

						
						<table align="right" style="width:40%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" border="1" >
							<tr>
								<td style="border: 1px solid;" >Subtotal </td><td style="border: 1px solid;" >$</td> <td style="border: 1px solid;text-align:right;"> '.number_format ($subtotalOrdenCompra,2,$separador_decimales,$separador_miles).'</td>
							</tr>
							<tr>
								<td style="border: 1px solid;">Iva </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;">'.number_format ($ivaOrdenCompra,2,$separador_decimales,$separador_miles).' </td>
							</tr>
							<tr>
								<td style="border: 1px solid;text-align:center" bgcolor="#B9BABF">TOTAL </td><td style="border: 1px solid;"bgcolor="#B9BABF" >$</td><td style="border: 1px solid;text-align:right;font-weight:bold;bgcolor="#B9BABF">'. number_format ($totalOrdenCompra,2,$separador_decimales,$separador_miles).'</td>
							</tr>

						</table>

						<br>
						<table align="right" style="width:400px; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" border="1" >
							<tr bgcolor="#B9BABF">
								<td>Observaciones</td>
							</tr>
							<tr>
								<td>'.$arrayOrden['observacion'].'</td>
							</tr>
						</table>';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<?php echo $contenido; ?>
</body>
</html>
