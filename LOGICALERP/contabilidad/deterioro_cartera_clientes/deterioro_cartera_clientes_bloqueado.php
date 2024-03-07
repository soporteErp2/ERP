<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_usuario  = $_SESSION['IDUSUARIO'];



		$sql="SELECT
				fecha,
				tasa_descuento,
				rotacion,
				nombre_usuario,
				sucursal,
				estado,
				consecutivo
			FROM deterioro_cartera_clientes
			WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_deterioro_cliente";
		$query=$mysql->query($sql,$mysql->link);

		$consecutivo    = $mysql->result($query,0,'consecutivo');
		$fecha          = $mysql->result($query,0,'fecha');
		$tasa_descuento = $mysql->result($query,0,'tasa_descuento');
		$rotacion       = $mysql->result($query,0,'rotacion');
		$nombre_usuario = $mysql->result($query,0,'nombre_usuario');
		$sucursal       = $mysql->result($query,0,'sucursal');
		$estado         = $mysql->result($query,0,'estado');

		if ($estado==1) {
			$script = "
						document.getElementById('btn_restaurar_deterioro').style.display = 'none';
						document.getElementById('btn_editar_deterioro').style.display    = 'inline';
						document.getElementById('btn_cancelar_deterioro').style.display  = 'inline';
						document.getElementById('btn_excel_deterioro').style.display  = 'inline';
						";
		}
		else if ($estado==3) {
			$script = "
						document.getElementById('btn_restaurar_deterioro').style.display = 'inline';
						document.getElementById('btn_editar_deterioro').style.display    = 'none';
						document.getElementById('btn_excel_deterioro').style.display    = 'none';
						document.getElementById('btn_cancelar_deterioro').style.display  = 'none';
						";
		}

		// CONSULTAR TODAS LAS FACTURAS
		$sql="SELECT
					documento_tercero,
					tercero,
					id_factura,
					fecha_factura,
					numero_factura,
					valor_factura,
					tiempo_estimado_pago,
					porcentaje_recaudo,
					estado,
					deteriorable
				FROM deterioro_cartera_clientes_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente = $id_deterioro_cliente";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$porcentaje_baja    = '';
			$porcentaje_recaudo = '';

			$whereIdFacturasPeriodos .= ($whereIdFacturasPeriodos=='')? " id = ".$row['id_factura'] : " OR id = ".$row['id_factura'] ;
			$arrayFacturas[ $row['id_factura'] ] =  array(
														'valor_factura'        => $row['valor_factura'],
														'tiempo_estimado_pago' => $row['tiempo_estimado_pago'],
														);

			$dias = $row['tiempo_estimado_pago'] * 30;

			// if ($dias>$rotacion){ $cartera_no_corriente = $row['valor_factura']; }
			// else{ $cartera_corriente = $row['valor_factura']; }

			$incobrable     = ($row['estado']=='Incobrable')? $row['valor_factura'] : '' ;
			$cobro_juridico = ($row['estado']=='Juridico')? $row['valor_factura'] : '' ;

			if ($row['estado']=='Juridico') {
				$porcentaje_baja = (100 - $row['porcentaje_recaudo'])/100;
				$porcentaje_baja = $row['valor_factura'] * $porcentaje_baja;

				$porcentaje_recaudo = $row['valor_factura'] * ($row['porcentaje_recaudo']/100);
			}

			$icon_deteriorable = ($row['deteriorable']=='true')? 'src="img/checked.png"' : 'src="img/un_checked.png"' ;


			$diferencia_dias      = diferencia_dias($row['fecha_factura'],$fecha);
			$cartera_corriente    = '';
			$cartera_no_corriente = '';

			if ($diferencia_dias<0 && (abs($diferencia_dias)>$rotacion)) {
				$diferencia_dias = abs($diferencia_dias);
				$cartera_no_corriente = number_format($row['valor_factura'],$_SESSION['DECIMALESMONEDA']);
			}
			else{
				$cartera_corriente    = number_format($row['valor_factura'],$_SESSION['DECIMALESMONEDA']);
			}

			$bodyTable .= '
							<tr>
								<td><div>'.$row['documento_tercero'].'</div></td>
								<td><div class="deterioro_cliente_label" title="'.$row['tercero'].'">'.$row['tercero'].'</div></td>
								<td><div class="fecha_label">'.$row['fecha_factura'].'</div></td>
								<td><div>'.$row['numero_factura'].'</div></td>
								<td><div>'.number_format($row['valor_factura'],$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div>'.$row['estado'].'</div></td>
								<td><div>'.$row['tiempo_estimado_pago'].'</div></td>
								<td><div>'.$row['porcentaje_recaudo'].'</div></td>
								<td><div>'.number_format($cartera_corriente,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div>'.number_format($cartera_no_corriente,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td ><div id="deterioro_'.$row['id_factura'].'"></div></td>
								<td><div>'.number_format($incobrable,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div>'.number_format($jurídico,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div>'.number_format($porcentaje_baja,$_SESSION['DECIMALESMONEDA']) .'</div></td>
								<td><div>'.number_format($porcentaje_recaudo,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td>
									<div  style="text-align:center;">
										<img id="deteriorar_'.$row['id_factura'].'" '.$icon_deteriorable.' >
									</div>
								</td>
							</tr>
						';
		}

		// CONSULTAR LOS PERIODOS ANTERIORES DE LAS FACTURAS, PARA EL CALCULO DEL DETERIORO
		$sql="SELECT COUNT(id) AS cont,id_factura FROM deterioro_cartera_clientes_facturas WHERE activo=1 AND id_empresa=$id_empresa AND $whereIdFacturasPeriodos GROUP BY id_factura";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$arrayPeriodosAnterioresFacturas [$row['id_factura']] = $row['cont'];
		}

		// RECORRER LAS FACTURAS PARA HALLAR SU DETERIORO
		foreach ($arrayFacturas as $id_factura => $arrayResul) {
			$t  = $arrayPeriodosAnterioresFacturas[$id_factura];
			$VP = $arrayResul['valor_factura'];
			$i  = 1*($tasa_descuento/100);
			$n  = ($t>0)? $arrayResul['tiempo_estimado_pago']*$t : $arrayResul['tiempo_estimado_pago'];
			$VA = $arrayResul['valor_factura']-($VP / ( pow(( 1 + $i ), $n) ));
			$script .= 'document.getElementById("deterioro_'.$id_factura.'").innerHTML="'.number_format($VA,$_SESSION['DECIMALESMONEDA']).'";
						console.log("$t : '.$t .' $VP : '.$VP.' $i : '.$i .' $n : '.$n .'");';
		}

	function diferencia_dias($fecha_i,$fecha_f){
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		// $dias 	= abs($dias);
		$dias = floor($dias);
		return $dias;
	}

?>
<div class="content" id="content_<?php echo $opcGrillaContable; ?>">
	<div class="separator">DETERIORO CARTERA CLIENTES <div class="close" onclick="Win_Ventana_nuevo_deterioro.close();"></div></div>
	<div class="buttom-content">
		<button class="button" id="btn_editar_deterioro"    data-value="edit"    onclick="editar_<?php echo $opcGrillaContable; ?>()">Editar</button>
		<button class="button" id="btn_cancelar_deterioro"  data-value="cancel"  onclick="cancelar_<?php echo $opcGrillaContable; ?>()">Cancelar</button>
		<button class="button" id="btn_excel_deterioro"     data-value="excel"   onclick="exportar_<?php echo $opcGrillaContable; ?>()">Exportar</button>
		<button class="button" id="btn_restaurar_deterioro" data-value="restore" onclick="restaurar_<?php echo $opcGrillaContable; ?>()">Restaurar</button>
		<div id="titleDocumento<?php echo $opcGrillaContable; ?>" class="infoDocumento">Consecutivo <br>No. <?php echo $consecutivo; ?></div>
	</div>
	<table class="table-form">
		<tr class="thead">
			<td colspan="4">VARIABLES INICIALES <div></div> </td>
		</tr>
		<tr>
			<td>Sucursal</td>
			<td><input type="text" value="<?php echo $sucursal; ?>" readonly></td>
			<td>Fecha</td>
			<td><input type="text" value="<?php echo $fecha ?>" readonly></td>
			<td rowspan="2"></td>
		</tr>
		<tr>
			<td>Tasa de Descuento</td>
			<td><input type="text" value="<?php echo $tasa_descuento ?>" title="<?php echo $tasa_descuento ?>" readonly> </td>
			<td>Rotación</td>
			<td><input type="text" value="<?php echo $rotacion ?>" title="<?php echo $rotacion ?>" readonly></td>
		</tr>
	</table>

	<div class="content-documentos" id="content-documentos">
		<table class="table-grilla">
			<thead>
				<tr>
					<td >NIT</td>
					<td >CLIENTE</td>
					<td >FECHA</td>
					<td >No <br>DOCUMENTO</td>
					<td >VALOR</td>
					<td >ESTADO</td>
					<td >TIEMPO ESTIMADO DE <br>PAGO(MESES)</td>
					<td >PORCENTAJE DE RECAUDO <br>PROBABLE EN PROCESO <br>JURÍDICO</td>
					<td >CARTERA CORRIENTE</td>
					<td >CARTERA NO CORRIENTE</td>
					<td >DETERIORO</td>
					<td >INCOBRABLE</td>
					<td >COBRO JURÍDICO</td>
					<td >CARTERA EN PROCESO JURÍDICO <br>A DESRECONOCER (DAR DE BAJA)</td>
					<td >CARTERA EN PROCESO <br>JURÍDICO A RECUPERAR</td>
					<td >DETERIORAR</td>
				</tr>
			</thead>
			<tbody id="bodyTable_<?php echo $opcGrillaContable; ?>">
				<?php echo $bodyTable; ?>
			</tbody>
		</table>
	</div>

</div>
<div id="loadForm" style="display:none;"></div>

<script>

	<?php echo $script; ?>

	function editar_<?php echo $opcGrillaContable; ?>() {
		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'editar_documento',
				opcGrillaContable    : '<?php echo $opcGrillaContable ?>',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
			}
		});
	}

	function cancelar_<?php echo $opcGrillaContable; ?>() {
		if (!confirm("Aviso\nRealmente desea cancelar el documento ")) {};
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'cancelar_documento',
				opcGrillaContable    : '<?php echo $opcGrillaContable ?>',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
			}
		});

	}

	function restaurar_<?php echo $opcGrillaContable; ?>() {
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'restaurar_documento',
				opcGrillaContable    : '<?php echo $opcGrillaContable ?>',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
			}
		});
	}

	function exportar_<?php echo $opcGrillaContable; ?>() {
		window.open('deterioro_cartera_clientes/exportar_deterioro_cartera_clientes.php?id_deterioro_cliente=<?php echo $id_deterioro_cliente ?>');
	}

</script>