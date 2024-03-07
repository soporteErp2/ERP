<?php
include("../../../configuracion/conectar.php");
include("../../../configuracion/define_variables.php");

$id_usuario = $_SESSION['IDUSUARIO'];
$id_empresa = $_SESSION['EMPRESA'];

echo"<style>
		.EmpSeparador
		{
			float	:left; width:100%;
			color	:#333;
			padding	:2px 0 3px 5px;
			margin	:4px 0 8px -10px;
			font-weight	: bold;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
			-webkit-box-shadow: 1px 1px 3px #666;
			-moz-box-shadow: 1px 1px 2px #666;
			background: -webkit-linear-gradient(#DFE8F6, #CDDBF0);
			background: -moz-linear-gradient(#DFE8F6, #CDDBF0);
			background: -o-linear-gradient(#DFE8F6, #CDDBF0);
			background: linear-gradient(#DFE8F6, #CDDBF0);
		}
	</style>";



if($opc=="empresa"){

	$consulta_cantidad           = "SELECT cantidad FROM inventario_totales WHERE id=$elid";
	$slq_consulta_cantidad       = mysql_query($consulta_cantidad,$link);
	$array_sql_consulta_cantidad = mysql_fetch_array($slq_consulta_cantidad);

	$cantidad_inventario = $array_sql_consulta_cantidad["cantidad"];

	/*----------------------------------------------Nombres De las ubicaciones-------------------------------------------*/

	$SQL_Origen    = "SELECT * FROM inventario_totales WHERE activo=1 AND id=".$elid;
	$consul_origen = mysql_query($SQL_Origen,$link);
	while($row = mysql_fetch_array($consul_origen)){
		$nombre_empresa_origen  = $row['empresa'];
		$nombre_sucursal_origen = $row['sucursal'];
		$nombre_bodega_origen   = $row['ubicacion'];
		$id_empresa_origen      = $row['id_empresa'];
		$id_sucursal_origen     = $row['id_sucursal'];
		$id_bodega_origen       = $row['id_ubicacion'];
	}

	/*-------------------------------------------------FORMULARIO PRESTAMO------------------------------------------------*/

	// $SQL_Empresa = "SELECT id,nombre FROM empresas WHERE activo=1";
	// $consul_empresa  =mysql_query($SQL_Empresa,$link);
	$SQL = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
	$consul  =mysql_query($SQL,$link);

	echo'
			<input type="hidden" id="id_sucursal_origen" value='.$id_sucursal_origen.'>
			<input type="hidden" id="id_ubicacion_origen" value='.$id_bodega_origen.'>
			<div id="guardar" style="margin:10px; width:90%;">
				<div style="float:left; width:200px; margin-right:50px">
					<fieldset style="padding:10px; height:150px;">
					<legend>Ubicacion Origen</legend>

						<div style="height:33%;">
							Sucursal:	<input id="sucursal_origen" type="text" value="'.$nombre_sucursal_origen.'" style="width:200px" disabled>
						</div>
						<div style="height:33%;">
							Bodega:	<input id="bodega_origen" type="text" value="'.$nombre_bodega_origen.'" style="width:200px" disabled>
						</div>
						<div style="height:33%;">
							Cantidad Existente:	<input id="cantidad_existente" type="text" value="'.$array_sql_consulta_cantidad["cantidad"].'" style="width:200px" disabled>
						</div>
					</fieldset>
				</div>
				<div div style="float:left; width:200px;" onload="sucursal()">
					<fieldset style="padding:10px; height:150px;">
						<legend>Detalles Traslado:</legend>
						<div id="sucursal" style="height:33%;">Sucursal:
							<select class="myfield" style="width:200px" id="Inventario_sucursal" onchange="bodega()" >
								<option value="">Seleccione...</option>';

								while($row = mysql_fetch_array($consul)){
									echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
								}

	echo 					'</select>
						</div>
						<div id="bodega" style="height:33%;">Bodega: &nbsp;
							<select class="myfield" style="width:200px" id="Inventario_bodega"  >
								<option value="">Seleccione...</option>
							</select>
						</div>
						<div id="cantidad" style="height:33%;">Cantidad a Trasladar: &nbsp;<br>
							<input type="text" id="cantidad_trasladar" name="cantidad">
						</div>
					</fieldset>
				</div>
				<div style="float:left; width:90%; margin-top:20px">Observaciones:<br>
					<textarea rows="4" cols="56" id="observaciones"></textarea>
				</div>
			</div>';
}



if($opc=="bodega"){
	$SQL    = "SELECT id,nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id_sucursal=".$id_sucursal;
	$consul = mysql_query($SQL,$link);

	echo'Bodega: &nbsp;
		<select class="myfield" name="Inventario_bodega" id="Inventario_bodega" style="width:200px">
			<option value="">Seleccione...</option>';
	while($row = mysql_fetch_array($consul)){
		echo '<option value="'.$row["id"].'">'.$row['nombre'].'</option>';
	}
	echo '</select>';
}

if($opc=='reset'){
	echo'Bodega: &nbsp;
		<select class="myfield" style="width:200px" id="'.$id_reset.'" >
			<option value="">Seleccione...</option>
		</select>';
	exit;
}

if($opc=='guardar_traslado'){
	//capturo el id del item
	$sqlItem   = "SELECT IT.id_item AS id, IT.cantidad, IT.costos
					FROM items AS I,
						inventario_totales AS IT
					WHERE IT.id='$elid'
						AND IT.id_item=I.id
						AND IT.id_empresa='$id_empresa'
						AND IT.id_sucursal=$sucursal_origen
						AND IT.id_ubicacion=$bodega_origen
						AND IT.activo=1
					GROUP BY I.id
					LIMIT 0,1";
	$queryItem = mysql_query($sqlItem,$link);
	$ArrayItem = mysql_fetch_assoc($queryItem);

	$id_equipo = $ArrayItem['id'];
	$saldoItem = $ArrayItem['cantidad'];
	$costoItem = $ArrayItem['costos'];

	//verificamos por segunda vez que los valores que trane cantidad  sean los correctos, es decir que cantidad a trasladar no sea mayor que la existente
	if ($cantidad>$saldoItem) {
		echo'<div style="float:left; margin: 5px 0 0 10px">
			    <div style="float:left; width:90px; padding:3px 0 0 0">Error</div>
			    <div style="float:left; width:120px">La cantidad a trasladar es mayor a la existente.</div>
			</div>';
	}
	else{
		$sql1 = "INSERT INTO inventario_totales_traslados
				(id_equipo,fecha,id_sucursal_origen,id_bodega_origen,id_empresa,id_sucursal_destino,id_bodega_destino,id_usuario,cantidad,costo,observaciones)
				VALUES
				('$id_equipo',now(),'$sucursal_origen','$bodega_origen',".$_SESSION['EMPRESA'].",'$sucursal_destino','$bodega_destino','$id_usuario','$cantidad','$costoItem','$observaciones')";
		$connectid1 = mysql_query($sql1,$link);
		$id_nuevo   = mysql_insert_id();

		if (!$connectid1){ die('no valido'.mysql_error()); exit; }

		//antes de imngresar el articulo en la nueva bodega y/o sucursal, verificamos si el item ya existe en el inventario de esa bodega y/o sucursal, si existe se actualiza la cantidad si no se inserta todo el registro
		$consulta_si_existe_item      = "SELECT COUNT(id) AS valor FROM inventario_totales WHERE id_item=$id_equipo AND id_sucursal=$sucursal_destino AND id_ubicacion=$bodega_destino AND  activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
		$sql_consulta_si_existe       = mysql_query($consulta_si_existe_item,$link);
		$array_sql_consulta_si_existe = mysql_fetch_array($sql_consulta_si_existe);

		$contador_consulta = $array_sql_consulta_si_existe["valor"];
		//inicio del if para verificar existencia del item
		if ($contador_consulta>0) {
		//en este punto que el contador vale mas q cero vamos a actualizar el item de esa bodega por que ya existe
			//cadena para actualizar la cantidad de ese item en el inventario de esa bodega y/o sucursal
			$actualiza_cantidad = "UPDATE inventario_totales
									SET costos=((costos*cantidad)+($costoItem*$cantidad))/(cantidad+$cantidad),
										cantidad=(cantidad+$cantidad),
										debug=CONCAT('cantidad: ',cantidad,' costo: ',costos,'+',$costoItem),
										id_documento_update          = '$id_nota',
										tipo_documento_update        = 'Nota Contable'
									WHERE id_item=$id_equipo AND id_sucursal=$sucursal_destino AND id_ubicacion=$bodega_destino AND  activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
			$actualiza_sql      = mysql_query($actualiza_cantidad,$link);
			if (!$actualiza_sql){die('no Actualizo'.mysql_error());}
			// echo '<script>console.log("'.$cantidad.' - '.$costoItem.'");</script>';
		}
		else{
		//si es cero quiere decir que no se existe en el inventario de esa bodega y/o sucursal por lo que proseguimos a crear el registro
			$inserta_nuevo_item = "INSERT INTO inventario_totales (id_item,id_sucursal,id_ubicacion,cantidad,costos) VALUES($id_equipo,$sucursal_destino,$bodega_destino,$cantidad,$costoItem)";
			$inserta_sql        = mysql_query($inserta_nuevo_item,$link);
			if (!$inserta_sql){die('no Actualizo'.mysql_error());}
		}

		//descontamos la cantidad trasladada al item
		//$actualiza_cantidad_trasladada = "UPDATE inventario_totales SET cantidad=(cantidad-$cantidad),costos=(costos/(cantidad+$cantidad))*cantidad WHERE id_item=$id_equipo AND id_sucursal=$sucursal_origen AND id_ubicacion=$bodega_origen AND activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
		$actualiza_cantidad_trasladada = "UPDATE inventario_totales
											SET cantidad=(cantidad-$cantidad) ,
												id_documento_update          = '$id_nota',
												tipo_documento_update        = 'Nota Contable'
											WHERE id_item=$id_equipo
											AND id_sucursal=$sucursal_origen
											AND id_ubicacion=$bodega_origen
											AND activo=1
											AND id_empresa=".$_SESSION['EMPRESA'];
		$slq_actualiza                 = mysql_query($actualiza_cantidad_trasladada,$link);

		echo"<script>
				Win_Nuevo_Traslado.close();
				Inserta_Div_Inventario_totales_traslados(".$id_nuevo.");
				Actualiza_Div_InventarioTotales(".$elid.")
			</script>";

	}
	exit;
}



if($opc=='ver_informe'){

	$titulo="INFORME TRASLADO DE ITEM";


	$SQL = "SELECT * FROM inventario_totales_traslados WHERE activo=1 AND id=".$id;
	$consul=mysql_query($SQL,$link);
	if (!$consul){die('no valido'.mysql_error());}
	while($row = mysql_fetch_array($consul)){


		echo'<div style="float:left; width:90%; margin:10px 10px 30px 10px; text-align:center;">
					'.$titulo.'<br>
					Traslado de Inventario No."'.str_pad($id, 12, '0', STR_PAD_LEFT).'"
				</div>

				<div style="float:left; width:90%; margin:10px 5px 5px 10px;">
					<div style="clear:both; float:left; width:40%;">Usuario que realizo el '.$funcion.'</div>
					<div style="float:right; width:60%;">'.$row['nombre_usuario'].'</div>
				</div>
				<div style="float:left; width:90%; margin:5px 10px 5px 10px">
					<div style="clear:both; float:left; width:40%;">Nombre Inventario</div>
					<div style="float:right; width:60%;">'.$row['nombre_equipo'].'</div>
				</div>
				<div style="float:left; width:90%; margin:10px 5px 5px 10px;">
					<div style="clear:both; float:left; width:40%;">Codigo inventario</div>
					<div style="float:right; width:60%;">'.$row['codigo'].'</div>
				</div>
				<div style="float:left; width:90%; margin:10px 5px 5px 10px">
					<div style="float:left; width:40%;">Fecha y Hora de '.$funcion.'</div>
					<div style="float:left; width:60%;">'.$row['fecha'].'</div>
				</div>
				<div style="float:left; width:90%; margin:5px 5px 20px 10px">
					<div style="float:left; width:40%;">Cantidad</div>
					<div style="float:left; width:60%;">'.$row['cantidad'].'</div>
				</div>

				<div style="float:left; width:90%; margin:5px 5px 5px 10px" class="EmpSeparador">
					<div style="float:left; width:80%;">Informacion Ubicacion De Origen</div>
				</div>

				<div style="float:left; width:90%; margin:5px 10px 5px 10px">
					<div style="clear:both; float:left; width:40%;">Sucursal</div>
					<div style="float:right; width:60%;">'.$row['nombre_sucursal_origen'].'</div>
				</div>
				<div style="float:left; width:90%; margin:5px 5px 20px 10px">
					<div style="float:left; width:40%;">Bodega</div>
					<div style="float:left; width:60%;">'.$row['nombre_bodega_origen'].'</div>
				</div>

				<div style="float:left; width:90%; margin:5px 5px 5px 10px" class="EmpSeparador">
					<div style="float:left; width:80%;">Informacion Ubicacion De Destino</div>
				</div>

				<div style="float:left; width:90%; margin:5px 10px 5px 10px">
					<div style="clear:both; float:left; width:40%;">Sucursal</div>
					<div style="float:right; width:60%;">'.$row['nombre_sucursal_destino'].'</div>
				</div>
				<div style="float:left; width:90%; margin:5px 5px 20px 10px">
					<div style="float:left; width:40%;">Bodega</div>
					<div style="float:left; width:60%;">'.$row['nombre_bodega_destino'].'</div>
				</div>

				<div style="float:left; width:90%; margin:5px 5px 5px 10px" class="EmpSeparador">
					<div style="float:left; width:80%;">Observaciones del '.$funcion.'</div>
				</div>
				<div style="float:left; width:90%; margin:5px 5px 20px 10px">
					'.$row['observaciones'].'
				</div>';
	}
	exit;
}

?>

<script>
	function sucursal(){
		var id_empresa = "<?php echo $_SESSION['EMPRESA']?>";
		Ext.get("sucursal").load({
			url		: "inventario_unidades/nuevo_movimiento_inventario.php",
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc        : "sucursal",
				id_empresa : id_empresa
			}
		});
		reset ('Inventario_bodega');
	}

	function bodega(){
		var id_sucursal = document.getElementById("Inventario_sucursal").value;
		Ext.get("bodega").load({
			url     : "inventario_unidades/nuevo_movimiento_inventario.php",
			scripts : true,
			nocache : true,
			params	:
			{
				opc			: "bodega",
				id_sucursal	: id_sucursal
			}
		});
	}

	function reset(id_reset){
		Ext.get(id_reset).load({
			url     : "nuevo_movimiento_inventario.php",
			scripts : true,
			nocache : true,
			params	:
			{
				id_reset : id_reset,
				opc      : 'reset',
			}
		});
	}


</script>



