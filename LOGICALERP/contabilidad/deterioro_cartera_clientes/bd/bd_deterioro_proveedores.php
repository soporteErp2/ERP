<?php

	include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");

	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_empresa  = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'cambia_sucursal':
			cambia_sucursal($id_sucursal_head,$id_deterioro_cliente,$id_empresa,$mysql);
			break;
		case 'cambia_fecha':
			cambia_fecha($fecha,$id_deterioro_cliente,$id_empresa,$mysql);
			break;
		case 'guardar_valor_input':
			guardar_valor_input($id_deterioro_cliente,$opcGrillaContable,$valor_input,$id_input,$id_empresa,$mysql);
			break;
		case 'cargar_facturas':
			cargar_facturas($id_deterioro_cliente,$opcGrillaContable,$tasa_descuento,$rotacion,$fecha,$id_empresa,$mysql);
			break;
		case 'guardar_factura':
			guardar_factura($id_deterioro_cliente,$id_factura,$tiempo_estimado,$porcentaje_recaudo,$estado,$deteriorar,$id_empresa,$mysql);
			break;
		case 'eliminar_factura':
			eliminar_factura($id_deterioro_cliente,$id_factura,$id_empresa,$mysql);
			break;
		case 'deteriorable':
			deteriorable($id_deterioro_cliente,$id_factura,$deteriorable,$id_empresa,$mysql);
			break;
		case 'calcular_deterioro':
			calcular_deterioro($opcGrillaContable,$id_deterioro_cliente,$id_factura,$periodos,$tasa_descuento,$rotacion,$valor_factura,$id_empresa,$mysql);
			break;
		case 'generar_deterioro':
			generar_deterioro($id_deterioro_cliente,$id_empresa,$mysql);
			break;
	}

	// CAMBIAR LA SUCURSAL DEL DOCUMENTO DE DETERIORO
	function cambia_sucursal($id_sucursal,$id_deterioro_cliente,$id_empresa,$mysql){
		echo$sql="UPDATE deterioro_cartera_clientes SET id_sucursal='$id_sucursal' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_deterioro_cliente ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se actualizo la sucursal intentelo de nuevo");</script>';
		}
	}

	// CAMBIAR LA FECHA DEL DOCUMENTO DE DETERIORO
	function cambia_fecha($fecha,$id_deterioro_cliente,$id_empresa,$mysql){
		$sql="UPDATE deterioro_cartera_clientes SET fecha='$fecha-12-31' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_deterioro_cliente ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se actualizo la fecha intentelo de nuevo");</script>';
		}
	}

	// GUARDAR EL VALOR DE LOS INPUTS DE CABECERA
	function guardar_valor_input($id_deterioro_cliente,$opcGrillaContable,$valor,$id_input,$id_empresa,$mysql){
		if ($id_input=='tasa_descuento_'.$opcGrillaContable) {
			$campoUpdate = "tasa_descuento = ".$valor;
		}
		else{
			$campoUpdate = "rotacion = ".$valor;
		}

		echo$sql="UPDATE deterioro_cartera_clientes SET $campoUpdate WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_deterioro_cliente ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se actualizo el campo");</script>';
		}
	}

	// CARGAR TODAS LAS FACTURAS
	function cargar_facturas($id_deterioro_cliente,$opcGrillaContable,$tasa_descuento,$rotacion,$fecha,$id_empresa,$mysql){
		// CONSULTAR LAS FACTURAS INSERTADAS EN ESE DETERIORO
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
				FROM deterioro_cartera_clientes_facturas
				WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente=$id_deterioro_cliente";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$whereIdFacturas .= ($whereIdFacturas=='')? "id <> ".$row['id_factura'] : " OR id <> ".$row['id_factura'] ;
			$whereIdFacturasPeriodos .= ($whereIdFacturasPeriodos=='')? "id = ".$row['id_factura'] : " OR id = ".$row['id_factura'] ;
			$arrayFacturas[ $row['id_factura'] ] = array(
														'valor_factura'        => $row['valor_factura'],
														'tiempo_estimado_pago' => $row['tiempo_estimado_pago'],
														);

			$dias = $row['tiempo_estimado_pago'] * 30;

			if ($dias>$rotacion){ $cartera_no_corriente = $row['valor_factura']; }
			else{ $cartera_corriente = $row['valor_factura']; }

			$incobrable     = ($row['estado']=='Incobrable')? $row['valor_factura'] : '' ;
			$cobro_juridico = ($row['estado']=='Juridico')? $row['valor_factura'] : '' ;

			$porcentaje_baja = (100 - $row['porcentaje_recaudo'])/100;
			$porcentaje_baja = $row['valor_factura'] * $porcentaje_baja;

			$porcentaje_recaudo = $row['valor_factura'] * ($row['porcentaje_recaudo']/100);
			$icon_deteriorable = ($row['deteriorable']=='true')? 'src="img/checked.png"' : 'src="img/un_checked.png"' ;
			$script .= "document.getElementById('estado_$row[id_factura]').value='$row[estado]'; ";

			$bodyTable .= '
							<tr>
								<td><div>'.$row['documento_tercero'].'</div></td>
								<td><div class="deterioro_cliente_label" title="'.$row['tercero'].'">'.$row['tercero'].'</div></td>
								<td><div>'.$row['numero_factura'].'</div></td>
								<td><div>'.number_format($row['valor_factura'],$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td>
									<select style="width: 140px;height: 100px;" id="estado_'.$row['id_factura'].'" onchange="cambia_estado_'.$opcGrillaContable.'('.$row['id_factura'].',this.value,\''.$row['valor_factura'].'\')" >
										<option>Seleccione</option>
										<option value="Comprimiso de Pago" >Comprimiso de Pago</option>
										<option value="Incobrable">Incobrable</option>
										<option value="Juridico">Juridico</option>
									</select>
								</td>
								<td><input type="text" id="tiempo_estimado_'.$row['id_factura'].'"  value="'.$row['tiempo_estimado_pago'].'" onkeyup="tiempo_estimado_'.$opcGrillaContable.'(this.value,'.$row['id_factura'].','.$row['valor_factura'].')"></td>
								<td><input type="text" id="porcentaje_recaudo_'.$row['id_factura'].'"  value="'.$row['porcentaje_recaudo'].'" onkeyup="dar_de_baja_'.$opcGrillaContable.'('.$row['id_factura'].',this.value,'.$row['valor_factura'].')"></td>
								<td><div id="cartera_corriente_'.$row['id_factura'].'" >'.number_format($cartera_corriente,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div id="cartera_no_corriente_'.$row['id_factura'].'" >'.number_format($cartera_no_corriente,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div id="deterioro_'.$row['id_factura'].'" ></div></td>
								<td><div id="incobrable_'.$row['id_factura'].'" >'.number_format($incobrable,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div id="cobro_juridico_'.$row['id_factura'].'" >'.number_format($jurídico,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div id="dar_de_baja_'.$row['id_factura'].'" >'.number_format($porcentaje_baja,$_SESSION['DECIMALESMONEDA']) .'</div></td>
								<td><div id="recuperar_'.$row['id_factura'].'" >'.number_format($porcentaje_recaudo,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td>
									<div  style="text-align:center;">
										<img id="deteriorar_'.$row['id_factura'].'" '.$icon_deteriorable.'  onclick="deteriorable('.$row['id_factura'].',this);" data-value="'.$row['deteriorable'].'">
									</div>
								</td>
								<td class="thead">
									<div>
										<img id="btn_guardar_'.$row['id_factura'].'"  src="img/grilla_deterioro/save.png" title="Guardar" onclick="guardar_factura_'.$opcGrillaContable.'('.$row['id_factura'].')">
										<img id="btn_eliminar_'.$row['id_factura'].'" src="img/grilla_deterioro/delete.png" title="Eliminar" onclick="eliminar_factura_'.$opcGrillaContable.'('.$row['id_factura'].')">
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
			$script .= "document.getElementById('deterioro_$id_factura').innerHTML='".number_format($VA,$_SESSION['DECIMALESMONEDA'])."';
						console.log('t : $t VP : $VP i : $i n : $n ');";
		}

		$whereIdFacturas = ($whereIdFacturas<>'')? " AND (".$whereIdFacturas.") " : "";

		// CONSULTAR LAS FACTURAS
		$sql="SELECT
				id,
				nit,
				cliente,
				numero_factura_completo,
				total_factura,
				total_factura_sin_abono,
				IF( ABS( DATEDIFF(MAX(fecha_vencimiento), MIN('$fecha-12-31')) ) < $rotacion, total_factura_sin_abono , '' ) AS cartera_corriente,
				IF( ABS( DATEDIFF(MAX(fecha_vencimiento), MIN('$fecha-12-31')) ) > $rotacion, total_factura_sin_abono , '' ) AS cartera_no_corriente
			FROM ventas_facturas
			WHERE
				activo=1
			AND id_empresa=$id_empresa
			AND total_factura_sin_abono>0
			$whereIdFacturas
			GROUP BY id";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$bodyTable .= '
							<tr>
								<td><div>'.$row['nit'].'</div></td>
								<td><div class="deterioro_cliente_label" title="'.$row['cliente'].'">'.$row['cliente'].'</div></td>
								<td><div>'.$row['numero_factura_completo'].'</div></td>
								<td><div>'.number_format($row['total_factura_sin_abono'],$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td>
									<select style="width: 140px;height: 100px;" id="estado_'.$row['id'].'" onchange="cambia_estado_'.$opcGrillaContable.'('.$row['id'].',this.value,\''.$row['total_factura_sin_abono'].'\')" >
										<option>Seleccione</option>
										<option value="Comprimiso de Pago" >Comprimiso de Pago</option>
										<option value="Incobrable" >Incobrable</option>
										<option value="Juridico" >Juridico</option>
									</select>
								</td>
								<td><input type="text" id="tiempo_estimado_'.$row['id'].'" onkeyup="tiempo_estimado_'.$opcGrillaContable.'(this.value,'.$row['id'].','.$row['total_factura_sin_abono'].')"></td>
								<td><input type="text" id="porcentaje_recaudo_'.$row['id'].'" onkeyup="dar_de_baja_'.$opcGrillaContable.'('.$row['id'].',this.value,'.$row['total_factura_sin_abono'].')"></td>
								<td><div id="cartera_corriente_'.$row['id'].'"></div></td>
								<td><div id="cartera_no_corriente_'.$row['id'].'"></div></td>
								<td><div id="deterioro_'.$row['id'].'"></div></td>
								<td><div id="incobrable_'.$row['id'].'"></div></td>
								<td><div id="cobro_juridico_'.$row['id'].'"></div></td>
								<td><div id="dar_de_baja_'.$row['id'].'"></div></td>
								<td><div id="recuperar_'.$row['id'].'"></div></td>
								<td>
									<div  style="text-align:center;">
										<img id="deteriorar_'.$row['id'].'" src="img/checked.png" onclick="deteriorable('.$row['id'].',this);" data-value="true">
									</div>
								</td>
								<td class="thead">
									<div>
										<img id="btn_guardar_'.$row['id'].'" 	src="img/grilla_deterioro/save.png" title="Guardar" onclick="guardar_factura_'.$opcGrillaContable.'('.$row['id'].')">
										<img id="btn_eliminar_'.$row['id'].'" 	style="display:none;" src="img/grilla_deterioro/delete.png" title="Eliminar" onclick="eliminar_factura_'.$opcGrillaContable.'('.$row['id'].')">
									</div>
								</td>
							</tr>
						';
		}

		echo $bodyTable."<script>$script</script>";
	}

	// GUARDAR LA FACTURA EN EL DETERIORO
	function guardar_factura($id_deterioro_cliente,$id_factura,$tiempo_estimado,$porcentaje_recaudo,$estado,$deteriorar,$id_empresa,$mysql){
		$sql    = "SELECT id FROM deterioro_cartera_clientes_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente=$id_deterioro_cliente AND id_factura=$id_factura";
		$query  = $mysql->query($sql,$mysql->link);
		$id_row = $mysql->result($query,0,'id');

		if ($id_row>0) {
			$sql="UPDATE deterioro_cartera_clientes_facturas SET estado='$estado',tiempo_estimado_pago='$tiempo_estimado',porcentaje_recaudo='$porcentaje_recaudo',deteriorable='$deteriorar' WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente=$id_deterioro_cliente AND id_factura=$id_factura";
			$query=$mysql->query($sql,$mysql->link);
		}
		else{
			$sql="INSERT INTO deterioro_cartera_clientes_facturas
					(id_deterioro_cliente,id_factura,estado,tiempo_estimado_pago,porcentaje_recaudo,deteriorable,id_empresa)
					VALUES ('$id_deterioro_cliente','$id_factura','$estado','$tiempo_estimado','$porcentaje_recaudo','$deteriorar','$id_empresa') ";
			$query=$mysql->query($sql,$mysql->link);
		}

		echo '<script>
				document.getElementById("btn_eliminar_'.$id_factura.'").style.display = "block";
			</script>';
	}

	// ELIMINAR LA FACTURA GUARDADA EN EL DETERIORO
	function eliminar_factura($id_deterioro_cliente,$id_factura,$id_empresa,$mysql){
		$sql="DELETE FROM deterioro_cartera_clientes_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente=$id_deterioro_cliente AND id_factura=$id_factura";
		$query=$mysql->query($sql,$mysql->link);
		echo '<script>
				document.getElementById("btn_eliminar_'.$id_factura.'").style.display = "none";
			</script>';
	}

	// ESTABLECER SI UNA FACTURA SE LE CONTABILIZA DETERIORO NO
	function deteriorable($id_deterioro_cliente,$id_factura,$deteriorable,$id_empresa,$mysql){
		echo$sql="UPDATE deterioro_cartera_clientes_facturas SET deteriorable='$deteriorable' WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente=$id_deterioro_cliente AND id_factura=$id_factura";
		$query=$mysql->query($sql,$mysql->link);
		if ($query){
			$icon_deteriorable = ($deteriorable=='true')? 'img/checked.png' : 'img/un_checked.png' ;
			echo '<script>
					document.getElementById("deteriorar_'.$id_factura.'").src="'.$icon_deteriorable.'";
					document.getElementById("deteriorar_'.$id_factura.'").dataset.value="'.$deteriorable.'";
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se actualizo si es deteriorable o no\nIntentelo de nuevo");</script>';
		}
	}

	// CALCULAR EL DETERIORO DE UNA FACTURA
	function calcular_deterioro($opcGrillaContable,$id_deterioro_cliente,$id_factura,$periodos,$tasa_descuento,$rotacion,$valor_factura,$id_empresa,$mysql){
		if(($periodos*30)<=$rotacion) {
			echo '<script>
					document.getElementById("deterioro_'.$id_factura.'").innerHTML="";
				</script>';
			return;
		}

		// CONSULTAR SI ESA FACTURA TUVO OTRAS DEPRECIACIONES
		$sql="SELECT COUNT(id) AS deterioros_anteriores FROM deterioro_cartera_clientes_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id_factura=$id_factura AND deteriorable='true' ";
		$query=$mysql->query($sql,$mysql->link);
		$t  = $mysql->result($query,0,'deterioros_anteriores');
		$VP = $valor_factura;
		$i  = 1*($tasa_descuento/100);
		$n  = ($t>0)? $periodos*$t : $periodos;

		/**
			FORMULA DEL VALOR ACTUAL (VA)
			VA = VP / ( 1 + i )^(n*t)

			VARIABLES
			VP = VALOR PRESENTE -> Corresponde al valor de la factura
			i  = TASA 			-> Corresponde a la tasa de interes definida por el usuario para el deterioro
			n  = PERIODOS 		-> Corresponde al periodo de pago de la factura digitado por el usuario
			t  = Corresponde a la cantidad de deterioros anteriores

		*/

		$VA = $valor_factura-($VP / ( pow(( 1 + $i ), $n) ));
		echo '<script>
				console.log("$t : '.$t .' $VP : '.$VP.' VA : '.($VP / ( pow(( 1 + $i ), $n) )).' $i : '.$i .' $n : '.$n .'");
				document.getElementById("deterioro_'.$id_factura.'").innerHTML="'.number_format($VA,$_SESSION['DECIMALESMONEDA']).'";
			</script>';
	}

	// GENERAR EL DOCUMENTO DE DETERIORO
	function generar_deterioro($id_deterioro_cliente,$id_empresa,$mysql){
		// VALIDAR QUE TENGA FACTURAS ALMACENADAS
		$sql="SELECT COUNT(id) AS cont FROM deterioro_cartera_clientes_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id_deterioro_cliente=$id_deterioro_cliente";
		$query=$mysql->query($sql,$mysql->link);
		if ($mysql->result($query,0,'cont')<1) {
			echo '<script>
					// alert("El documento no tiene facturas guardadas para deteriorar");
					MyLoading2("off",{texto:"No hay facturas para guardar",icono:"fail",duracion:2500});
				</script>';
			exit;
		}
		echo '<script>MyLoading2("off");</script>';
	}

	// CONTABILIZACION DEL DOCUMENTO
	function contabilizar_documento($id_deterioro_cliente,$accion,$id_empresa,$mysql){
		if ($accion=='contabilizar') {
			// CONSULTAR LAS CUENTAS CONFIGURADAS DEL DETERIORO
			$sql="";
			$query=$mysql->query($sql,$mysql->link);
		}
		else if($accion=='descontabilizar'){
			//
		}
	}

 ?>