<?php
	include('../../../../../../configuracion/conectar.php');
	include('../../../../../../configuracion/define_variables.php');
	$habilitaBoton = true;

	// VALIDAR QUE HALLA PRECIERRE
	$fechaCierre = date("Y-m-d");
	$sql   = "SELECT id,fecha FROM ventas_pos_auditoria_cierre WHERE activo=1 AND fecha='$fechaCierre'";
	$query = $mysql->query($sql);
	$idCierre    = $mysql->result($query,0,'id');
	if ($idCierre>0) {
		$bodyMessages .= '<div class="form_separador" style="background-color: #db5957 !important;color: #FFF !important; ">INCONSISTENCIAS CIERRE</div>
							<table class="simpleTable">
								<tbody>
									<tr>
										<td><b>Ya se realizo el cierre para ese dia</b></td>
									</tr>
								</tbody>
							</table>
							';
		$habilitaBoton = false;
	}

	// VALIDAR QUE HALLA PRECIERRE
	$sql   = "SELECT id,fecha FROM ventas_pos_auditoria_precierre WHERE activo=1 AND estado=1 ";
	$query = $mysql->query($sql);
	$idPrecierre    = $mysql->result($query,0,'id');
	$fechaPrecierre = $mysql->result($query,0,'fecha');

	if ($idPrecierre=='') {
		$bodyMessages .= '<div class="form_separador" style="background-color: #db5957 !important;color: #FFF !important; ">INCONSISTENCIAS PRECIERRE</div>
							<table class="simpleTable">
								<tbody>
									<tr>
										<td><b>No se ha realizado el precierre</b></td>
									</tr>
								</tbody>
							</table>
							';
		$habilitaBoton = false;
	}

	// CONSULTAR LAS MESAS ABIERTAS
	$sql   = "SELECT
				MC.id,
				MC.nombre_mesa,
				MC.fecha_apertura,
				MC.nombre_usuario_apertura,
				M.seccion
			FROM ventas_pos_mesas_cuenta AS MC INNER JOIN ventas_pos_mesas AS M ON M.id=MC.id_mesa
			WHERE MC.activo=1
			AND MC.id_empresa=$_SESSION[EMPRESA]
			AND MC.estado<>'Cerrada'
			";
	$query = $mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$bodyMesasAbiertas .= "<tr>
									<td>$row[id]</td>
									<td>$row[nombre_mesa]</td>
									<td>$row[fecha_apertura]</td>
									<td>$row[seccion]</td>
									<td>$row[nombre_usuario_apertura]</td>
								</tr>";
		$habilitaBoton = false;
	}

	// CONSULTAR LAS CAJAS ABIERTAS
	$sql="SELECT
				nombre_caja,
				id_usuario,
				documento_usuario,
				nombre_usuario,
				estado,
				fecha_apertura,
				provision
			FROM ventas_pos_cajas_movimientos WHERE activo=1 AND estado<>'Cerrada' ";
	$query=$mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$bodyCajasAbiertas .= "<tr>
									<td>$row[nombre_usuario]</td>
									<td>$row[nombre_caja] $row[estado]</td>
									<td>$row[fecha_apertura]</td>
									<td style='padding:3px;' >
										<img src='../../../images/desktop_access_disabled.png'style='width: 22px;color:#37474f;cursor:pointer;' onClick='cerrarCaja($row[id])' title='Cerrar Caja' >
									</td>
								</tr>";
		$habilitaBoton = false;
	}

	// CONSULTAR INCONSISTENCIAS DE FACTURAS
	$sql="SELECT id,consecutivo,cliente,usuario,detalle_estado FROM ventas_pos WHERE activo=1 AND estado='500' ";
	$query=$mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$bodyErrorFacturas .= "<tr>
									<td>$row[consecutivo]</td>
									<td>$row[cliente]</td>
									<td>$row[usuario]</td>
									<td>$row[detalle_estado]</td>
									<td >
										<i class='material-icons' style='cursor:pointer;' title='Reprocesar' onClick='sincFacturaCierre($row[id])' >autorenew</i>
									</td>
								</tr>";
		// $habilitaBoton = false;
	}

	$bodyMesasAbiertas = ( $bodyMesasAbiertas =='')? "<tr><td colspan='5' >No hay mesas abiertas</td></tr>" : $bodyMesasAbiertas ;
	$bodyCajasAbiertas = ( $bodyCajasAbiertas =='')? "<tr><td colspan='5' >No hay cajas abiertas</td></tr>" : $bodyCajasAbiertas ;
	$bodyErrorFacturas = ( $bodyErrorFacturas =='')? "<tr><td colspan='5' >No hay errores en facturas</td></tr>" : $bodyErrorFacturas ;

	// AUDITORIA DE MOVIMIENTOS

	// CONSULTAR LAS FACTURAS
	$sql   = "SELECT
					VP.id,
					VP.consecutivo,
					VP.usuario,
					VPP.forma_pago,
					FP.tipo,
					VPP.valor
				FROM
					ventas_pos AS VP
				INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
				INNER JOIN configuracion_cuentas_pago_pos AS FP ON FP.id = VPP.id_forma_pago
				WHERE
					VP.activo = 1
				AND VP.estado <> 2
				AND VPP.activo = 1 ";
	$query = $mysql->query($sql);
	while ( $row=$mysql->fetch_array($query) ){
		$arrayFormasPago[$row['tipo']][] = array(
												"usuario"     => $row['usuario'],
												"forma_pago"  => $row['forma_pago'],
												"consecutivo" => $row['consecutivo'],
												"valor"       => $row['valor'],
											);
		$bodyAudCajeros .= "<tr>
								<td style='text-align:left;'>$row[usuario]</td>
								<td style='text-align:left;'>$row[forma_pago]</td>
								<td style='text-align:left;'>$row[tipo]</td>
								<td style='text-align:right;padding-right:15px;' >".number_format($row['valor'],0,",",".")."</td>
							</tr>";

		// FORMA DE PAGO HUESPED
		if ($row['tipo']=='Cheque Cuenta') {
			$bodyHuesped .= "<tr>
								<td style='text-align:left;'>$row[usuario]</td>
								<td style='text-align:left;'>$row[forma_pago]</td>
								<td style='text-align:left;'>$row[consecutivo]</td>
								<td style='text-align:right;padding-right:15px;'>".number_format($row['valor'],0,",",".")."</td>
							</tr>";
		}
		// FORMA DE PAGO CORTESIAS
		else if ($row['tipo']=='Cortesia') {
			$bodyCortesia .= "<tr>
								<td style='text-align:left;'>$row[usuario]</td>
								<td style='text-align:left;'>$row[forma_pago]</td>
								<td style='text-align:left;'>$row[consecutivo]</td>
								<td style='text-align:right;padding-right:15px;'>".number_format($row['valor'],0,",",".")."</td>
							</tr>";
		}
		// FORMA DE PAGO A PARTICULARES
		else{
			$bodyParticulares .= "<tr>
									<td style='text-align:left;'>$row[usuario]</td>
									<td style='text-align:left;'>$row[forma_pago]</td>
									<td style='text-align:left;'>$row[consecutivo]</td>
									<td style='text-align:right;padding-right:15px;'>".number_format($row['valor'],0,",",".")."</td>
								</tr>";
		}
	}

?>

<!DOCTYPE html>
<html>
<head>

	<!-- Estilos globales de la app -->
	<link rel="stylesheet" type="text/css" href="../../temas/estilo.css">

	<style>
		.panelColorIcons{
			color:#37474f !important;
		}
	</style>

</head>
<body>

	<div class="desktopContainer">
		<?= $bodyMessages; ?>

		<div class="form_separador" style="background-color: #db5957 !important;color: #FFF !important; ">INCONSISTENCIAS CUENTAS</div>

		<table class="simpleTable">
			<thead>
				<tr>
					<td>No. cuenta</td>
					<td>Mesa</td>
					<td>Fecha Apertura</td>
					<td>Ambiente</td>
					<td>Usuario</td>
				</tr>
			</thead>
			<tbody><?= $bodyMesasAbiertas; ?></tbody>
		</table>

		<div class="form_separador" style="background-color: #db5957 !important;color: #FFF !important; ">INCONSISTENCIAS CAJEROS</div>
		<table class="simpleTable">
			<thead>
				<tr>
					<td>Usuario</td>
					<td>Descripcion</td>
					<td>Fecha Apertura</td>
				</tr>
			</thead>
			<tbody><?= $bodyCajasAbiertas;?></tbody>
		</table>

		<div class="form_separador" style="background-color: #db5957 !important;color: #FFF !important; ">INCONSISTENCIAS FACTURAS</div>
		<table class="simpleTable">
			<thead>
				<tr>
					<td>Numero</td>
					<td>Cliente</td>
					<td>Usuario</td>
					<td>Detalle</td>
					<td></td>
				</tr>
			</thead>
			<tbody><?= $bodyErrorFacturas;?></tbody>
		</table>

		<div class="form_separador">AUDITORIA CAJERO</div>
		<table class="simpleTable">
			<thead>
				<tr>
					<td>Usuario</td>
					<td>Tipo</td>
					<td>Forma Pago</td>
					<td>Valor</td>
				</tr>
			</thead>
			<tbody><?= $bodyAudCajeros;?></tbody>
		</table>

		<div class="form_separador">FORMA DE PAGO A PARTICULARES</div>
		<table class="simpleTable">
			<thead>
				<tr>
					<td>Usuario</td>
					<td>Forma Pago</td>
					<td>Numero</td>
					<td>Valor</td>
				</tr>
			</thead>
			<tbody><?= $bodyParticulares;?></tbody>
		</table>

		<div class="form_separador">FORMA DE PAGO HUESPED</div>
		<table class="simpleTable">
			<thead>
				<tr>
					<td>Usuario</td>
					<td>Forma Pago</td>
					<td>Numero</td>
					<td>Valor</td>
				</tr>
			</thead>
			<tbody><?= $bodyHuesped;?></tbody>
		</table>

		<div class="form_separador">FORMA DE PAGO CORTESIAS</div>
		<table class="simpleTable">
			<thead>
				<tr>
					<td>Usuario</td>
					<td>Forma Pago</td>
					<td>Numero</td>
					<td>Valor</td>
				</tr>
			</thead>
			<tbody><?= $bodyCortesia;?></tbody>
		</table>


	</div>

	<script type="text/javascript">
		<?php
			if ($habilitaBoton) {
				echo "\$W.Element('btnCierre').enable();";
			}
		?>

		var cerrarCajaCierre = (id_row) =>{
			if (confirm("Realmente desea cerrar la caja?")){
				$W.Loading();
				$W.Ajax({
					url    : "../../../backend/pos_admin/Controller.php",
					params :  {
						id_row : id_row,
						method : "cerrarCaja"
					},
					timeout : 2000,
					success : function(result,xhr){
						// console.log(result.responseText); //lee respuesta como texto
						console.log(JSON.parse(result.responseText)); //lee respuesta como json
						var response = JSON.parse(result.responseText);
						console.log(response);
						if (response.status=='success'){ validarDia(); }
						else{ alert(response.message); }
						$W.Loading();
					},
					failure : function(xhr){
						alert("Error de conexion");
						$W.Loading();
					}
				})
				//
			}
		}

		var sincFacturaCierre = (id_documento='')=>{
			$W.Loading();
			params       = { id : id_documento }
			fetch(`../../../backend/pos/Controller.php?method=generateTiquet`, {
				method  : 'POST', // or 'PUT'
				body    : JSON.stringify(params), // data can be `string` or {object}!
				mode    : 'cors',
				headers : {
			    	'Content-Type': 'application/json',
			  	}
			}).then(res => res.json())
			.then((responseJson)=>{
				console.log(responseJson);
				if (responseJson.status==true){ validarDia(); }
				else{ alert(responseJson.message); }
				$W.Loading();
			})
		}

	</script>
</body>
</html>