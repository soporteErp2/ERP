<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=deterioro_cartera_clientes_".(date('Y-m-d')).".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_usuario  = $_SESSION['IDUSUARIO'];
	// echo $id_deterioro_cliente.'--';
	$sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selectSucursal .= '<option value="'.$row['id'].'" >'.$row['nombre'].'</option>';
	}

	// SI NO EXISTE LA NOTA DE DETERIORO SE CREA UNA NUEVA
	if(!isset($id_deterioro_cliente) || $id_deterioro_cliente=='' || $id_deterioro_cliente==0){
		$random_documento = responseUnicoRanomico();
		$fecha            = date('Y').'-12-31';

		$sql="INSERT INTO deterioro_cartera_clientes (random,fecha,id_usuario,id_sucursal,id_empresa)
				VALUES ('$random_documento','$fecha',$id_usuario,$id_sucursal,$id_empresa) ";
		$query=$mysql->query($sql,$mysql->link);

		// CONSULTAR EL ID DEL DOCUMENTO
		$sql   = "SELECT id FROM deterioro_cartera_clientes WHERE activo=1 AND id_empresa=$id_empresa AND random='$random_documento' ";
		$query = $mysql->query($sql,$mysql->link);
		$id_deterioro_cliente = $mysql->result($query,0,'id');

		// CONSULTAR EL NOMBRE DEL USUARIO
		$sql="SELECT nombre FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_usuario";
		$query=$mysql->query($sql,$mysql->link);
		$nombre_usuario = $mysql->result($query,0,'nombre');


		$script = "
					document.getElementById('titleDocumento$opcGrillaContable').value    = 'Consecutivo <br> No. $consecutivo';
					document.getElementById('sucursal_$opcGrillaContable').value = '$id_sucursal';
					Inserta_Div_deterioros($id_deterioro_cliente);
				";
	}
	// SI EXISTE LA NOTA DE DETERIORO
	else{

		$sql="SELECT
				fecha,
				tasa_descuento,
				rotacion,
				nombre_usuario,
				sucursal,
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

		// CONSULTAR TODAS LAS FACTURAS
		$sql="SELECT
					documento_tercero,
					tercero,
					id_factura,
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
														'estado'               => $row['estado'],
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

			// $porcentaje_baja = (100 - $row['porcentaje_recaudo'])/100;
			// $porcentaje_baja = $row['valor_factura'] * $porcentaje_baja;

			// $porcentaje_recaudo = $row['valor_factura'] * ($row['porcentaje_recaudo']/100);
			$icon_deteriorable = ($row['deteriorable']=='true')? 'src="img/checked.png"' : 'src="img/un_checked.png"' ;
			$script .= "document.getElementById('estado_$row[id_factura]').value='$row[estado]'; ";

			$diferencia_dias      = diferencia_dias($row['fecha_factura'],$fecha);
			$cartera_corriente    = '';
			$cartera_no_corriente = '';

			if ($diferencia_dias<0 && (abs($diferencia_dias)>$rotacion)) {
				$diferencia_dias = abs($diferencia_dias);
				$cartera_no_corriente = round($row['valor_factura'],$_SESSION['DECIMALESMONEDA']);
			}
			else{
				$cartera_corriente    = round($row['valor_factura'],$_SESSION['DECIMALESMONEDA']);
			}

			$bodyTable .= '
							<tr>
								<td>'.$row['documento_tercero'].'</td>
								<td>'.$row['tercero'].'</td>
								<td>'.$row['numero_factura'].'</td>
								<td>'.$row['valor_factura'].'</td>
								<td>'.$row['estado'].'</td>
								<td>'.$row['tiempo_estimado_pago'].'</td>
								<td>'.$row['porcentaje_recaudo'].'</td>
								<td>'.$cartera_corriente.'</td>
								<td>'.$cartera_no_corriente.'</td>
								<td>deterioro_'.$row['id_factura'].'</td>
								<td>'.$incobrable.'</td>
								<td>'.$cobro_juridico.'</td>
								<td>'.$porcentaje_baja .'</td>
								<td>'.$porcentaje_recaudo.'</td>
								<td>'.(($row['deteriorable']=='true')? 'Si' : 'No' ).'</td>
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
			if ($arrayResul['estado']<>'Comprimiso de Pago') {
				$bodyTable = str_replace('deterioro_'.$id_factura, '', $bodyTable);
				continue;
			}
			$t  = $arrayPeriodosAnterioresFacturas[$id_factura];
			$VP = $arrayResul['valor_factura'];
			$i  = 1*($tasa_descuento/100);
			$n  = ($t>0)? $arrayResul['tiempo_estimado_pago']*$t : $arrayResul['tiempo_estimado_pago'];
			$VA = $arrayResul['valor_factura']-($VP / ( pow(( 1 + $i ), $n) ));
			// echo 'deterioro_'.$id_factura." - ".$VA." <br>";
			$bodyTable = str_replace('deterioro_'.$id_factura, round($VA,$_SESSION['DECIMALESMONEDA']), $bodyTable);

			$script .= 'document.getElementById("deterioro_'.$id_factura.'").innerHTML="'.number_format($VA,$_SESSION['DECIMALESMONEDA']).'";
						console.log("$t : '.$t .' $VP : '.$VP.' $i : '.$i .' $n : '.$n .'");';
		}

	}

	function diferencia_dias($fecha_i,$fecha_f){
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		// $dias 	= abs($dias);
		$dias = floor($dias);
		return $dias;
	}

?>

<style>

	.table-grilla{
		font-family     : arial,sans-serif;
		margin-top      : 20px;
		font-size       : 12px;
		float           : left;
		margin-left     : 10px;
		width           : 95%;
		border-collapse : collapse;
	}

	.table-grilla td{
		border  : 1px solid #2A80B9;
	}

	.table-grilla thead,.table-grilla .thead{
		background-color : #2A80B9;
		color            : #fff;
	}

	.table-grilla .thead img{
		float: left;
		width: 24px;
		padding: 3px;
	}

	.table-grilla thead td{
		padding     : 5px;
		font-size   : 14px;
		white-space : nowrap;
	}


	.table-grilla tbody td{
		padding : 0px;
		cursor  : pointer;
	}

	.table-grilla tbody td > div{
		padding: 5px;
	}

	.table-form{
		font-family : arial,sans-serif;
		margin-top  : 20px;
		font-size   : 12px;
		float       : left;
		margin-left : 10px;
		/*width       : 400px;*/
	}

	.table-form .thead{
		background-color : #2A80B9;
		color            : #fff;
	}

	.table-form .thead td{
		padding   : 5px;
		font-size : 14px;
	}

	.table-form td{
		padding: 2px 2px 2px 15px;
	}

	.table-form input, .table-form textarea, .table-form select{
		line-height      : 1.42857143;
		color            : #555;
		background-color : #fff;
		border           : 1px solid #ccc;
		height           : 30px;
		width            : 200px;
		padding-left     : 5px;
	}

	.table-form textarea{
		height: 50px;
	}

	.table-form input[data-requiere="true"], select[data-requiere="true"]{
		background-color:#FFE0E0;
	}
</style>

	<table class="table-form">
		<tr class="thead">
			<td colspan="4">VARIABLES INICIALES  </td>
		</tr>
		<tr>
			<td>Sucursal</td>
			<td><b><?php echo $sucursal; ?></b></td>
			<td>Fecha</td>
			<td><b><?php echo $fecha; ?></b></td>
			<td rowspan="2"></td>
		</tr>
		<tr>
			<td>Tasa de Descuento</td>
			<td><b><?php echo $tasa_descuento ?></b></td>
			<td>rotacion</td>
			<td><b><?php echo $rotacion ?></b></td>
		</tr>
	</table>

	<table class="table-grilla">
			<tr class="thead">
				<td >NIT</td>
				<td >CLIENTE</td>
				<td >No <br>DOCUMENTO</td>
				<td >VALOR</td>
				<td >ESTADO</td>
				<td >TIEMPO ESTIMADO DE <br>PAGO(MESES)</td>
				<td >PORCENTAJE DE RECAUDO <br>PROBABLE EN PROCESO <br>JURIDICO</td>
				<td >CARTERA CORRIENTE</td>
				<td >CARTERA NO CORRIENTE</td>
				<td >DETERIORO</td>
				<td >INCOBRABLE</td>
				<td >COBRO JURIDICO</td>
				<td >CARTERA EN PROCESO JURIDICO <br>A DESRECONOCER (DAR DE BAJA)</td>
				<td >CARTERA EN PROCESO <br>JURIDICO A RECUPERAR</td>
				<td >DETERIORAR</td>
			</tr>
		<tbody>
			<?php echo $bodyTable; ?>
		</tbody>
	</table>
