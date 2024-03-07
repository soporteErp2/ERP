<?php 
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if ($_SESSION['EMPRESA']=='') { return; }

	header("Content-Type: application/vnd.ms-excel");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=reporte_ingreso_orden.xls");
	
	//consultar el nombre de la empresa
	$consulta_nombre=mysql_query("SELECT nombre FROM empresas WHERE id=".$_SESSION['EMPRESA'],$link);
	$nombre_empresa=mysql_fetch_array($consulta_nombre);
	
	$titulo=' <b> REPORTE INGRESO FACTURA </b>';
	
	$subtotalOrdenCompra                   = 0.00;
	$acumuladodescuentoArticuloOrdenCompra = 0.00;
	$ivaOrdenCompra                        = 0.00;
	$ivaOrdenCompra1                       = 0.00;
	$valorRetencion                        = 0.00;
	$totalOrdenCompra                      = 0.00;

	$SQL = "SELECT * FROM compras_ordenes WHERE id='$id' AND activo=1 LIMIT 0,1";
	$consul=mysql_query($SQL,$link);
	if (!$consul){die('no valido informe'.mysql_error());}
	while($row = mysql_fetch_array($consul)){

		if ($row['tipo_descuento']=='porcentaje') {
			$tipod='%';
			$td="porcentaje";
		}
		else{
			$tipod='$';
			$td="pesos";
		}

		$estilo='background-color: #DFDFDF;';
		//consultamos los articulos de esta orden
		$sqlArticulos="SELECT * FROM compras_ordenes_inventario WHERE id_orden_compra='$id'";
		$queryArticulos=mysql_query($sqlArticulos,$link);
		//descuento general de la orden
				
		$color=0;
		while ($array= mysql_fetch_array($queryArticulos)) {
			if ($estilo!='') {
				$estilo='';			
			}else{
				$estilo='background-color: #DFDFDF;';			
			}

			// consultamos la unidad del articulo
			$sqlUnidad       = 'SELECT inventario_unidades.nombre  FROM inventario_unidades INNER JOIN items ON inventario_unidades.id=items.id_unidad_medida WHERE items.id="'.$array["id_inventario"].'"';
			$queryUnidad     = mysql_query($sqlUnidad,$link);
			$unidad_nombre   = mysql_result($queryUnidad,0,'nombre');
			$temp            = ($array["cantidad"]*$array["costo_unitario"])-$array["descuento"];
			$unidad_unidades = 


			$tipodesart='';
			if ($array["tipo_descuento"]=='porcentaje') {
				$tipodesart='%';
			}else{
				$tipodesart='$';
			}

			if ($array["descuento"]==0 || $array["descuento"]==0.00) {
				$tipodesart='';
			}		

			$articulos.='<tr>
							<td>'.$array["codigo"].'</td>
							<td>'.$array["nombre"].'</td>
							<td>'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
							<td>'.$array["cantidad"].'</div>
							<td>'.$array["descuento"].' '.$signoDescuento.'</td>
							<td>'.$array["costo_unitario"].'</td>
						 	<td>'.$temp.'</td> 
							
						</tr>';
			
			//========================= INICIO DEL CALCULO DE LA ORDEN DE COMPRA ============================================================//


			//asignamos los valores del articulo en variables

			$cantidad  =$array["cantidad"];
			$descuento =$array["descuento"];
			$costo     =$array["costo_unitario"];
			$tipoDesc  =$array["tipo_descuento"];
			$iva       =$array["valor_impuesto"];

			//variables para los calculos
			$descuento1       = 0;
			$subtotal         = 0;
			$subtotal1        = 0;
			$iva1             = 0;
			$descuentoTotal   = 0;
			$descuentoMostrar = 0;
			
			//calculamos el subtotal por articulo
			
			$subtotal=($cantidad*$costo);
			
			//verificar el tipo de descuento del articulo, si es en porcentaje se hace el sgt calculoa para convertir en pesos el descuento
			if ($tipoDesc=='porcentaje') {
			
				$descuento1=((($subtotal)*$descuento)/100);
				}else if($tipoDesc=='pesos'){
				//si el descuento no es en porcentaje, solo lo asignamos en la variable
				$descuento1=$descuento;
				}
			
			//generamos el subtotal del articulo
			$subtotal1= $subtotal- $descuento1;
			//generamos el costo del iva para ese articulo
			$iva1=($iva*$subtotal1)/100;
						
			//======================== INICIO DE LOS CALCULOS DE LOS TOTALES DE LA FACTURA  ===============================
			//se utiliza el parsefloat, por q presento confilcto, no estaba sumando los numeros sino concatenando las vairables
			//establecemos la variable descuentoOrdenCompra = al descuento del articulo (convertido en pesos) mas lo que tiene la misma variable
			
			// calculamos el subtotal de la orden donde subtotalOrdenCompra= subtotal del articulo mas la misma variable
			$subtotalOrdenCompra=$subtotal1+$subtotalOrdenCompra;

			//iva acumulado de cada articulo
			$ivaOrdenCompra = $ivaOrdenCompra+$iva1;

			// descuento de de la orden de compra, le incrementamos el descento en base el descuento del articulo
			// =parseFloat()+parseFloat(descuento1);
			
			
			//generamos un acumulado de los descuentos de los articulo                     
			$acumuladodescuentoArticuloOrdenCompra=$acumuladodescuentoArticuloOrdenCompra+$descuento1;			
		
					

		}

		//------------------------------- CALCULAMOS EL VALOR DE LA RETENCION  SOBRE LA FACTURA -------------------------------
		
		$valorRetencion = 0;
		$listadoRetenciones= '';
		$simboloRetencion = '';
		$valoresRetenciones = '';

		$sqlRetenciones= "SELECT retenciones.retencion, retenciones.valor FROM retenciones INNER JOIN compras_ordenes_retenciones ON retenciones.id=compras_ordenes_retenciones.id_retencion WHERE id_orden_compra='$id'";
		$queryRetenciones=mysql_query($sqlRetenciones,$link);

		while ($arrayRetenciones=mysql_fetch_array($queryRetenciones)) {
			
			if ($arrayRetenciones["retencion"]=='reteiva') {
				$valorRetencion +=($ivaOrdenCompra * $arrayRetenciones["valor"])/100;					

				$mostrarRetenciones.='<tr>
							<td style="border: 1px solid; ">'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;"> '.number_format ((($ivaOrdenCompra * $arrayRetenciones["valor"])/100),2).'</td>
							</tr>';	

			}
			else{
				$valorRetencion +=($subtotalOrdenCompra * $arrayRetenciones["valor"])/100;

				$mostrarRetenciones.='<tr>
							<td style="border: 1px solid; ">'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;"> '.number_format ((($subtotalOrdenCompra * $arrayRetenciones["valor"])/100),2).'</td>
							</tr>';	
			}

		
		}		
		
		$totalOrdenCompra=($subtotalOrdenCompra - $valorRetencion)+$ivaOrdenCompra;

		/*---------------------------------------------------------------formulario original------------------------------------------------------------------*/
		$contenido='	
						<p>
								<td><b>'.$nombre_empresa["nombre"].'</b><br> '.$titulo.'</td>
						</p>	
						<br>
						<table>						
							<tr>
									<td><b>Proveedor:</b></td>
									<td></td>
									<td>'.$row["proveedor"].'</td>
							</tr>
							<tr>
									<td><b>NIT</b></td>
									<td></td>
									<td>'.$row["nit"].'</td>
							</tr>
							<tr>
									<td><b>Sucursal</b></td>
									<td></td>
									<td>'.$row["sucursal"].' </td>
							</tr>

							<tr>
									<td><b>Bodega</b></td>
									<td></td>
									<td>'.$row["bodega"].' </td>
							</tr>

							<tr>
									<td><b>Fecha</b></td>
									<td></td>
									<td>'.$row["fecha"].'</td>
							</tr>
							<tr>
									<td><b>No. Orden Proveedor</b></td>
									<td></td>
									<td>'.$row["numero_orden_proveedor"].'</td>
							</tr>
							<tr>
									<td><b>No. Orden</b></div>
									<td></td>
									<td>'.$row["consecutivo_orden_compra"].'</td>
							</tr>

						</table>
						<br>
						<table border="1"  >
							<tr border="1">
								<td bgcolor="#B9BABF" >Codigo interno</td>						
								<td bgcolor="#B9BABF" >Articulo</td>						
								<td bgcolor="#B9BABF" >Unidad</td>						
								<td bgcolor="#B9BABF" >Cantidad<br>Pedida</td>					
								<td bgcolor="#B9BABF" >Descuento</td>										
								<td bgcolor="#B9BABF" >Valor<br>Unidad</td>
								<td bgcolor="#B9BABF" >Total</td>
							</tr>

						'.$articulos.'
						</table>

						<br/>					

						
						<table align="right" style="width:40%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" border="1" >
							<tr>
								<td style="border: 1px solid;" >Subtotal </td><td style="border: 1px solid;" >$</td> <td style="border: 1px solid;text-align:right;"> '.number_format ($subtotalOrdenCompra,2).'</td>
							</tr>
							'.$mostrarRetenciones.'
							<tr>
								<td style="border: 1px solid;">Iva </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;">'.number_format ($ivaOrdenCompra,2).' </td>
							</tr>
							<tr>
								<td style="border: 1px solid;text-align:center" bgcolor="#B9BABF">TOTAL </td><td style="border: 1px solid;"bgcolor="#B9BABF" >$</td><td style="border: 1px solid;text-align:right;font-weight:bold;bgcolor="#B9BABF">'. number_format ($totalOrdenCompra,2).'</td>
							</tr>

						</table>

						<br>
						<table align="right" style="width:400px; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" border="1" >
							<tr bgcolor="#B9BABF">
								<td>Observaciones</td>
							</tr>
							<tr>
								<td>'.$row['observaciones'].'</td>
							</tr>
						</table>';

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<?php echo $contenido; ?>
</body>
</html>
