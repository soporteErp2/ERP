<?php 
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	$SQL    = "SELECT * FROM $tablaPrincipal WHERE id='$id' AND activo=1";
	$consul = mysql_query($SQL,$link);

	if (!$consul){ die('no valido informe '.mysql_error()); }

	while($row = mysql_fetch_array($consul)){

	header("Content-Type: application/vnd.ms-excel");

	header("Expires: 0");

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

	header("content-disposition: attachment;filename=".$opcGrillaContable."_".$id.".xls");
	
	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa="SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular FROM empresas WHERE id=".$_SESSION['EMPRESA']." LIMIT 0,1";
	$queryEmpresa=mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ubicacion_empresa     = mysql_result($queryEmpresa,0,'ciudad').' - '.mysql_result($queryEmpresa,0,'pais');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$telefonos 			   = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	
	$labelConsecutivo='Consecutivo Nro';

	//ASIGNAMOS A UNA VARIABLE EL TITULO
	if ($opcGrillaContable=='FacturaVenta') {
		$titulo='FACTURA DE VENTA';
		$labelConsecutivo='Numero';

	}elseif ($opcGrillaContable=='RemisionesVenta') {
		$titulo='REMISION DE VENTA';
		$consecutivo = $row['consecutivo'];

	}elseif ($opcGrillaContable=='PedidoVenta') {
		$titulo='PEDIDO DE VENTA';
		$consecutivo = $row['consecutivo'];

	}else{
		$titulo='COTIZACION DE VENTA';
		$consecutivo = $row['consecutivo'];
	}

	$subtotalFacturaCompra                   = 0.00;
	$acumuladodescuentoArticuloFacturaCompra = 0.00;
	$valorFleteFacturaCompra                 = 0.00;
	$ivaFacturaCompra                        = 0.00;
	$ivaFacturaCompra1                       = 0.00;
	$valorRetencion                       	 = 0.00;
	$totalFacturaCompra                      = 0.00;

	
	

				if ($row['tipo_descuento']=='porcentaje') {
					$tipod='%';
					$td="porcentaje";
				}else{
					$tipod='$';
					$td="pesos";
				}
				$estilo='background-color: #DFDFDF;';
				//consultamos los articulos de esta orden
				$sqlArticulos="SELECT * FROM $tablaInventario WHERE $idTablaPrincipal='$id'";
				$queryArticulos=mysql_query($sqlArticulos,$link);
				//descuento general de la factura
					
					$valorFleteFacturaCompra = $row["flete"];
				$color=0;
				while ($array= mysql_fetch_array($queryArticulos)) {
						if ($estilo!='') {
							$estilo='';			
						}else{
							$estilo='background-color: #DFDFDF;';			
						}

						// consultamos la unidad del articulo
						$sqlUnidad   = 'SELECT inventario_unidades.nombre 
										FROM inventario_unidades 
										INNER JOIN items ON inventario_unidades.id=items.id_unidad_medida WHERE items.id="'.$array["id_inventario"].'"';
						$queryUnidad = mysql_query($sqlUnidad,$link);
						$unidad_nombre = mysql_result($queryUnidad,0,'nombre');
						$temp= ($array["cantidad"]*$array["costo_unitario"])-$array["descuento"];
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
						
						// calculamos el subtotal de la factura donde subtotalFacturaCompra= subtotal del articulo mas la misma variable
						$subtotalFacturaCompra=$subtotal1+$subtotalFacturaCompra;

						//iva acumulado de cada articulo
						$ivaFacturaCompra = $ivaFacturaCompra+$iva1;

						// descuento de de la orden de compra, le incrementamos el descento en base el descuento del articulo
						// =parseFloat()+parseFloat(descuento1);
						
						
						//generamos un acumulado de los descuentos de los articulo                     
						$acumuladodescuentoArticuloFacturaCompra=$acumuladodescuentoArticuloFacturaCompra+$descuento1;			
							
								

				}
				//=============================================== CALCULOS FINALES DE LA FACTURA =====================================//			

				//------------------------------- CALCULAMOS EL VALOR DE LA RETENCION  SOBRE LA FACTURA -------------------------------
				if ($opcGrillaContable=='FacturaVenta') {
					$valorRetencion     = 0;
					$listadoRetenciones = '';
					$simboloRetencion   = '';
					$valoresRetenciones = '';

					$sqlRetenciones= "SELECT retencion,valor,tipo_retencion
									FROM compras_facturas_retenciones
									WHERE id_factura_compra='$id' AND activo=1";
					$queryRetenciones=mysql_query($sqlRetenciones,$link);

					while ($arrayRetenciones=mysql_fetch_array($queryRetenciones)) {
						
						if ($arrayRetenciones["retencion"]=='reteiva') {
							$valorRetencion +=($ivaFacturaCompra * $arrayRetenciones["valor"])/100;					

							$mostrarRetenciones.='<tr >
										<td style="border: 1px solid; ">'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;"> '.number_format ((($ivaFacturaCompra * $arrayRetenciones["valor"])/100),2).'</td>
										</tr>';	

						}else{
							$valorRetencion +=($subtotalFacturaCompra * $arrayRetenciones["valor"])/100;

							$mostrarRetenciones.='<tr >
										<td style="border: 1px solid; ">'.$arrayRetenciones["retencion"].' ( '.$arrayRetenciones["valor"].' %) </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;"> '.number_format ((($subtotalFacturaCompra * $arrayRetenciones["valor"])/100),2).'</td>
										</tr>';	
						}				
					}
				}		
				
				$totalFacturaCompra=($subtotalFacturaCompra - $valorRetencion)+$ivaFacturaCompra;

				//======================================  CUERPO REPORTE INGRESO FACTURA ========================================//
				$contenido='	
							<table>
									<tr>
										<td><b>'.$nombre_empresa.'</b><br>'.$tipo_documento_nombre.': <b>'.$documento_empresa.'</b><br>'.$direccion_empresa.'<br><b>Tels:</b>'.$telefonos.'<br>'.$ubicacion_empresa.'<br>'.$razon_social.'<br></td>
									</tr>
									<tr>
										<td><b>'.$titulo.'</b><br>'.$labelConsecutivo.' '.$consecutivo.'</td>
									</tr>
									<tr style="border: 1px solid;text-align:center" bgcolor="#B9BABF">
										<td style="border: 1px solid;">Fecha Inicio</td><td style="border: 1px solid;">Vencimiento</td>
									</tr>							
									<tr>
										<td style="border: 1px solid;">'.date("Y-m-d",strtotime($row['fecha_inicio'])).'</td><td style="border: 1px solid;">'.date("Y-m-d",strtotime($row['fecha_finalizacion'])).'</td>
									</tr>
									
							</table>
								
							<br>
							<table  >						
								<tr>
										<td><b>Cliente:</b></td>
										<td></td>
										<td>'.$row["cliente"].'</td>
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

							</table>
							<br>
							<table border="1">
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
								<tr >
									<td style="border: 1px solid;" >Subtotal </td><td style="border: 1px solid;" >$</td> <td style="border: 1px solid;text-align:right;"> '.number_format ($subtotalFacturaCompra,2).'</td>
								</tr>
								'.$mostrarRetenciones.'
								<tr >
									<td style="border: 1px solid;">Iva </td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;">'.number_format ($ivaFacturaCompra,2).' </td>
								</tr>
								<tr >
									<td style="border: 1px solid;text-align:center" bgcolor="#B9BABF">TOTAL </td><td style="border: 1px solid;"bgcolor="#B9BABF" >$</td><td style="border: 1px solid;text-align:right;font-weight:bold;bgcolor="#B9BABF">'. number_format ($totalFacturaCompra,2).'</td>
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
							</table>
							

							
						';

	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
<?php 
	echo $contenido;
 ?>
</body>
</html>
