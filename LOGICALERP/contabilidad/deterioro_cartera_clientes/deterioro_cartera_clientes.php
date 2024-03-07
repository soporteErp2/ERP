<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../funciones_globales/funciones_php/randomico.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_usuario  = $_SESSION['IDUSUARIO'];
	// echo '<div class="separator">DETERIORO CARTERA CLIENTES <div class="close" onclick="Win_Ventana_nuevo_deterioro.close();"></div></div>';
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
				id_sucursal,
				consecutivo
			FROM deterioro_cartera_clientes
			WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_deterioro_cliente";
		$query=$mysql->query($sql,$mysql->link);

		$consecutivo    = $mysql->result($query,0,'consecutivo');
		$fecha          = $mysql->result($query,0,'fecha');
		$tasa_descuento = $mysql->result($query,0,'tasa_descuento');
		$rotacion       = $mysql->result($query,0,'rotacion');
		$nombre_usuario = $mysql->result($query,0,'nombre_usuario');
		$id_sucursal    = $mysql->result($query,0,'id_sucursal');

		$script = "
					document.getElementById('fecha_".$opcGrillaContable."').value    = '".$fecha."';
					document.getElementById('fecha_".$opcGrillaContable."').value    = '".$fecha."';
					document.getElementById('sucursal_".$opcGrillaContable."').value = '".$id_sucursal."';
					document.getElementById('titleDocumento".$opcGrillaContable."').innerHTML='".(($consecutivo>0)? 'Consecutivo <br>No. '.$consecutivo : '' )."';
				";

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
					deteriorable,
					fecha_factura
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

			$icon_deteriorable = ($row['deteriorable']=='true')? 'src="img/checked.png"' : 'src="img/un_checked.png"' ;
			$script .= "document.getElementById('estado_$row[id_factura]').value='$row[estado]'; ";


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
								<td>
									<select style="width: 140px;" id="estado_'.$row['id_factura'].'" onchange="cambia_estado_'.$opcGrillaContable.'('.$row['id_factura'].',this.value,\''.$row['valor_factura'].'\')" >
										<option>Seleccione</option>
										<option value="Comprimiso de Pago" >Comprimiso de Pago</option>
										<option value="Incobrable">Incobrable</option>
										<option value="Juridico">Juridico</option>
									</select>
								</td>
								<td><input type="text" id="tiempo_estimado_'.$row['id_factura'].'" value="'.$row['tiempo_estimado_pago'].'"  onkeyup="tiempo_estimado_'.$opcGrillaContable.'(this.value,'.$row['id_factura'].','.$row['valor_factura'].')"></td>
								<td><input type="text" id="porcentaje_recaudo_'.$row['id_factura'].'" value="'.$row['porcentaje_recaudo'].'" onkeyup="dar_de_baja_'.$opcGrillaContable.'('.$row['id_factura'].',this.value,'.$row['valor_factura'].')"></td>
								<td><div id="deterioro_acumulado_'.$row['id_factura'].'" >'.$cartera_corriente.'</div></td>
								<td><div id="cartera_no_corriente_'.$row['id_factura'].'" >'.$cartera_no_corriente.'</div></td>
								<td><div id="deterioro_'.$row['id_factura'].'"  	  ></div></td>
								<td><div id="incobrable_'.$row['id_factura'].'"  	 data-value="'.$incobrable.'" >'.number_format($incobrable,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div id="cobro_juridico_'.$row['id_factura'].'"  data-value="'.$cobro_juridico.'" >'.number_format($cobro_juridico,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td><div id="dar_de_baja_'.$row['id_factura'].'" data-value="'.$porcentaje_baja.'">'.number_format($porcentaje_baja,$_SESSION['DECIMALESMONEDA']) .'</div></td>
								<td><div id="recuperar_'.$row['id_factura'].'" >'.number_format($porcentaje_recaudo,$_SESSION['DECIMALESMONEDA']).'</div></td>
								<td>
									<div  style="text-align:center;">
										<img id="deteriorar_'.$row['id_factura'].'" '.$icon_deteriorable.'  onclick="deteriorable_'.$opcGrillaContable.'('.$row['id_factura'].',this);" data-value="'.$row['deteriorable'].'">
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
			if ($arrayResul['estado']<>'Comprimiso de Pago') { continue; }
			$t  = $arrayPeriodosAnterioresFacturas[$id_factura];
			$VP = $arrayResul['valor_factura'];
			$i  = 1*($tasa_descuento/100);
			$n  = ($t>0)? $arrayResul['tiempo_estimado_pago']*$t : $arrayResul['tiempo_estimado_pago'];
			$VA = $arrayResul['valor_factura']-($VP / ( pow(( 1 + $i ), $n) ));
			$script .= 'document.getElementById("deterioro_'.$id_factura.'").innerHTML="'.number_format($VA,$_SESSION['DECIMALESMONEDA']).'";
						document.getElementById("deterioro_'.$id_factura.'").dataset.value="'.$VA.'";
						//console.log("$t : '.$t .' $VP : '.$VP.' $i : '.$i .' $n : '.$n .'");';
		}

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
		<button class="button" data-value="save" onclick="generar_<?php echo $opcGrillaContable; ?>()">Generar</button>
		<button class="button" data-value="load" onclick="cargar_facturas_<?php echo $opcGrillaContable; ?>()" style="width:100px;">Cargar Facturas</button>
		<button class="button" id="btn_cancelar_deterioro"  data-value="cancel"  onclick="cancelar_<?php echo $opcGrillaContable; ?>()">Cancelar</button>
		<div id="titleDocumento<?php echo $opcGrillaContable; ?>" class="infoDocumento"></div>
	</div>
	<table class="table-form">
		<tr class="thead">
			<td colspan="4">VARIABLES INICIALES <div></div> </td>
		</tr>
		<tr>
			<td>Sucursal</td>
			<td>
				<select id="sucursal_<?php echo $opcGrillaContable ?>" onchange="cambia_sucursal_<?php echo $opcGrillaContable; ?>(this.value)">
					<optgroup label="Sucursales">
						<?php echo $selectSucursal; ?>
					</optgroup>
				</select>
			</td>
			<td>Fecha</td>
			<td><input type="text" id="fecha_<?php echo $opcGrillaContable ?>"></td>
			<td rowspan="2"></td>
		</tr>
		<tr>
			<td>Tasa de Descuento</td>
			<td><input type="text" value="<?php echo $tasa_descuento ?>" title="<?php echo $tasa_descuento ?>" id="tasa_descuento_<?php echo $opcGrillaContable ?>" onkeyup="evtInput(this)"> </td>
			<td>Rotación</td>
			<td><input type="text" value="<?php echo $rotacion ?>" title="<?php echo $rotacion ?>" id="rotacion_<?php echo $opcGrillaContable ?>" onkeyup="evtInput(this)"></td>
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
					<td >VALOR FACTURA</td>
					<td >ESTADO</td>
					<td >TIEMPO ESTIMADO DE <br>PAGO(MESES)</td>
					<td >PORCENTAJE DE RECAUDO <br>PROBABLE EN PROCESO <br>JURÍDICO</td>
					<td >DETERIORO ACUMULADO</td>
					<td >CARTERA NO CORRIENTE</td>
					<td >DETERIORO</td>
					<td >INCOBRABLE</td>
					<td >COBRO JURÍDICO</td>
					<td >CARTERA EN PROCESO JURÍDICO <br>A DESRECONOCER (DAR DE BAJA)</td>
					<td >CARTERA EN PROCESO <br>JURÍDICO A RECUPERAR</td>
					<td >DETERIORAR</td>
					<td >&nbsp;</td>
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

	new Ext.form.DateField({
	    format     : 'Y',               //FORMATO
	    width      : 200,               //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_<?php echo $opcGrillaContable; ?>',
	    editable   : false,                 //EDITABLE
	    value      : new Date(),             //VALOR POR DEFECTO
	    listeners  : { select: function() {  cambia_fecha_<?php echo $opcGrillaContable ?>();  } }
	});

	function cambia_sucursal_<?php echo $opcGrillaContable; ?>(id_sucursal) {
		console.log(id_sucursal);
		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'cambia_sucursal',
				id_sucursal_head     : id_sucursal,
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
			}
		});
	}

	function cambia_fecha_<?php echo $opcGrillaContable; ?>() {
		var fecha = document.getElementById('fecha_<?php echo $opcGrillaContable; ?>').value
		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'cambia_fecha',
				fecha                : fecha,
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
			}
		});
	}

	function evtInput(input) {
		var valor    = input.value
		,	id_input = input.id

		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'guardar_valor_input',
				opcGrillaContable    : '<?php echo $opcGrillaContable ?>',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
				valor_input          : valor,
				id_input             : id_input,
			}
		});
	}

	// CARGAR LAS FACTURAS DE LA SUCURSAL O LAS SUCURSALES
	function cargar_facturas_<?php echo $opcGrillaContable; ?>() {
		var sucursal       = document.getElementById('sucursal_<?php echo $opcGrillaContable ?>').value
		,	tasa_descuento = document.getElementById('tasa_descuento_<?php echo $opcGrillaContable ?>').value
		,	rotacion       = document.getElementById('rotacion_<?php echo $opcGrillaContable ?>').value
		,	fecha          = document.getElementById('fecha_<?php echo $opcGrillaContable; ?>').value

		if (tasa_descuento == '' ||	rotacion=='') {
			alert("Debe digitar la tasa de descuento y la rotacion");
			return;
		}

		Ext.get('bodyTable_<?php echo $opcGrillaContable; ?>').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opcGrillaContable    : '<?php echo $opcGrillaContable ?>',
				opc                  : 'cargar_facturas',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
				tasa_descuento       : tasa_descuento,
				rotacion             : rotacion,
				fecha                : fecha,
			}
		});
	}

	function cambia_estado_<?php echo $opcGrillaContable; ?>(id_factura,estado,valor_factura) {
		document.getElementById('incobrable_'+id_factura).innerHTML         = '';
		document.getElementById('incobrable_'+id_factura).dataset.value     = '';
		document.getElementById('cobro_juridico_'+id_factura).innerHTML     = '';
		document.getElementById('cobro_juridico_'+id_factura).dataset.value = '';
		document.getElementById('dar_de_baja_'+id_factura).innerHTML        = '';
		document.getElementById('dar_de_baja_'+id_factura).dataset.value    = '';
		document.getElementById('recuperar_'+id_factura).innerHTML          = '';
		document.getElementById('deterioro_'+id_factura).innerHTML          = '';
		document.getElementById('deterioro_'+id_factura).dataset.value      = '';

		if(estado == 'Comprimiso de Pago'){
			// document.getElementById('').innerHTML = "";
		}
		else if(estado == 'Incobrable'){
			document.getElementById('incobrable_'+id_factura).innerHTML     = formato_numero(valor_factura, <?php echo $_SESSION['DECIMALESMONEDA'] ?>, '.', ',');
			document.getElementById('incobrable_'+id_factura).dataset.value = valor_factura;
		}
		else if(estado == 'Juridico'){
			document.getElementById('cobro_juridico_'+id_factura).innerHTML     = formato_numero(valor_factura, <?php echo $_SESSION['DECIMALESMONEDA'] ?>, '.', ',');
			document.getElementById('cobro_juridico_'+id_factura).dataset.value = valor_factura;
			var porcentaje = document.getElementById('porcentaje_recaudo_'+id_factura).value;
			dar_de_baja_<?php echo $opcGrillaContable; ?>(id_factura,porcentaje,valor_factura);
		}
	}

	function tiempo_estimado_<?php echo $opcGrillaContable; ?>(valor,id_factura,valor_factura) {
		// var rotacion = document.getElementById('rotacion_<?php echo $opcGrillaContable; ?>').value;
		// var dias = valor *	30;
		// // console.log("rotacion="+rotacion+" valor= "+valor+" dias="+dias);
		// if (dias>rotacion){
		// 	document.getElementById('cartera_no_corriente_'+id_factura).innerHTML     = formato_numero(valor_factura,<?php echo $_SESSION['DECIMALESMONEDA']; ?>,'.',',');
		// 	document.getElementById('cartera_no_corriente_'+id_factura).dataset.value = formato_numero(valor_factura,<?php echo $_SESSION['DECIMALESMONEDA']; ?>,'.',',');
		// 	document.getElementById('deterioro_acumulado_'+id_factura).innerHTML        = "";
		// 	document.getElementById('deterioro_acumulado_'+id_factura).dataset.value    = "";
		// }
		// else{
		// 	document.getElementById('deterioro_acumulado_'+id_factura).innerHTML        = formato_numero(valor_factura,<?php echo $_SESSION['DECIMALESMONEDA']; ?>,'.',',');
		// 	document.getElementById('deterioro_acumulado_'+id_factura).dataset.value    = formato_numero(valor_factura,<?php echo $_SESSION['DECIMALESMONEDA']; ?>,'.',',');
		// 	document.getElementById('cartera_no_corriente_'+id_factura).innerHTML     = "";
		// 	document.getElementById('cartera_no_corriente_'+id_factura).dataset.value = "";
		// }

		// CALCULAR EL VALOR DEL DETERIORO
		calcular_deterioro_<?php echo $opcGrillaContable; ?>(id_factura,valor,valor_factura);
	}

	function dar_de_baja_<?php echo $opcGrillaContable; ?>(id_factura,porcentaje,valor_factura) {
		var porcentaje_baja = (100 - porcentaje)/100;
		var resultado = valor_factura * porcentaje_baja
		var resultado_sin_formato =resultado;
		resultado = formato_numero(resultado, <?php echo $_SESSION['DECIMALESMONEDA']; ?>, '.', ',');
		console.log(id_factura);

		document.getElementById('dar_de_baja_'+id_factura).innerHTML     = resultado ;
		document.getElementById('dar_de_baja_'+id_factura).dataset.value = resultado_sin_formato ;

		// EL VALOR DE LA CARTERA A RECUPERAR
		recuperar_<?php echo $opcGrillaContable; ?>(id_factura,porcentaje,valor_factura);
	}

	function recuperar_<?php echo $opcGrillaContable; ?>(id_factura,porcentaje,valor_factura) {
		var resultado             = valor_factura * (porcentaje/100);
		var resultado_sin_formato = resultado;
		resultado                 = resultado.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>);
		// console.log("baja: \nporcentaje="+porcentaje+" valor_factura="+valor_factura);
		document.getElementById('recuperar_'+id_factura).innerHTML     = formato_numero(resultado,<?php echo $_SESSION['DECIMALESMONEDA']; ?>,'.',',');
		document.getElementById('recuperar_'+id_factura).dataset.value = resultado_sin_formato ;
	}

	function guardar_factura_<?php echo $opcGrillaContable; ?>(id_factura) {
		var tiempo_estimado    = document.getElementById('tiempo_estimado_'+id_factura).value
		,	porcentaje_recaudo = document.getElementById('porcentaje_recaudo_'+id_factura).value
		,	estado             = document.getElementById('estado_'+id_factura).value
		,	deteriorar         = document.getElementById('deteriorar_'+id_factura).dataset.value

		if (estado=='') { alert('Aviso\nSeleccione el estado para la factura'); return;}


		var deterioro = 0;
		if (estado=='Comprimiso de Pago') {
			deterioro         = document.getElementById('deterioro_'+id_factura).dataset.value;
		}
		if (estado=='Incobrable') {
			deterioro         = document.getElementById('incobrable_'+id_factura).dataset.value;
		}
		if (estado=='Juridico') {
			deterioro         = document.getElementById('dar_de_baja_'+id_factura).dataset.value;
		}

		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'guardar_factura',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
				id_factura           : id_factura,
				tiempo_estimado      : tiempo_estimado,
				porcentaje_recaudo   : porcentaje_recaudo,
				estado               : estado,
				deteriorar           : deteriorar,
				deterioro            : deterioro,
			}
		});
	}

	function eliminar_factura_<?php echo $opcGrillaContable; ?>(id_factura) {
		if (confirm('Eliminar factura?')){
			Ext.get('loadForm').load({
				url     : 'deterioro_cartera_clientes/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc                  : 'eliminar_factura',
					id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
					id_factura           : id_factura,
				}
			});
		}
	}

	function deteriorable_<?php echo $opcGrillaContable; ?>(id_factura,img) {
		var deteriorable = ( img.dataset.value=='true' )? 'false' : 'true' ;
		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'deteriorable',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
				id_factura           : id_factura,
				deteriorable         : deteriorable,
			}
		});
	}

	function calcular_deterioro_<?php echo $opcGrillaContable; ?>(id_factura,periodos,valor_factura) {
		var tasa_descuento = document.getElementById('tasa_descuento_<?php echo $opcGrillaContable ?>').value
		,	rotacion       = document.getElementById('rotacion_<?php echo $opcGrillaContable ?>').value

		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'calcular_deterioro',
				opcGrillaContable    : '<?php echo $opcGrillaContable ?>',
				id_deterioro_cliente : '<?php echo $id_deterioro_cliente ?>',
				id_factura           : id_factura,
				periodos             : periodos,
				tasa_descuento       : tasa_descuento,
				rotacion             : rotacion,
				valor_factura        : valor_factura,
			}
		});
	}

	function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero=parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }  // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }

        return numero;
    }

    //
    function generar_<?php echo $opcGrillaContable; ?>() {
		var sucursal       = document.getElementById('sucursal_<?php echo $opcGrillaContable; ?>').value
		,	fecha          = document.getElementById('fecha_<?php echo $opcGrillaContable; ?>').value
		,	tasa_descuento = document.getElementById('tasa_descuento_<?php echo $opcGrillaContable; ?>').value
		,	rotacion       = document.getElementById('rotacion_<?php echo $opcGrillaContable; ?>').value

		if (tasa_descuento=='' || rotacion=='') {
			alert("Validacion\nDebe digitar las tasa y la rotacion");
			return;
		}

		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'deterioro_cartera_clientes/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                  : 'generar_deterioro',
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

</script>