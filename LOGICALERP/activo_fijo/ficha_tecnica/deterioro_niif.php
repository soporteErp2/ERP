<?php
	$id_empresa = $_SESSION['EMPRESA'];

	// CUENTA PERIODOS DEPRECIADOS COLGAAP
	// CONSULTAMOS LOS DOCUMENTOS DEL ACTIVO
	$sql = "SELECT
						AFD.id,
						AFD.consecutivo,
						AFD.fecha_inicio,
						ADFI.id_deterioro,
						ADFI.costo,
						ADFI.valor,
						ADFI.deterioro_acumulado
					FROM
						activos_fijos_deterioro AS AFD
					INNER JOIN
						activos_fijos_deterioro_inventario AS ADFI ON ADFI.id_deterioro = AFD.id
					WHERE
						AFD.activo = 1
					AND
						AFD.id_empresa = $id_empresa
					AND
						AFD.estado = 1
					AND
						ADFI.id_activo_fijo = $id_activo
					ORDER BY
						fecha_inicio DESC,consecutivo DESC";
	$query = $mysql->query($sql,$mysql->link);
	$periodos  = 0;
	$bodyTable = '';
	while($row = $mysql->fetch_array($query)){
		$valor = $row['costo']-$row['valor'];
		$bodyTable .=  "<tr>
											<td>".round($row['fecha_inicio'],$_SESSION['DECIMALESMONEDA'])."</td>
											<td>".round($row['consecutivo'],$_SESSION['DECIMALESMONEDA'])."</td>
											<td>".round($row['deterioro_acumulado'],$_SESSION['DECIMALESMONEDA'])."</td>
											<td>".round($valor,$_SESSION['DECIMALESMONEDA'])."</td>
										</tr>";
		$periodos++;
	}
?>
<style>
	.content-personal-info{
		padding-left : 15px;
		width        : calc(100% - 15px);
	}

	.content-detail{
		width     : 559px;
		height    : 416px;
		font-size : 12px;
		overflow  : auto;
	}
</style>
<div class="content-personal-info">
	<div class="separator-body" >DETERIORO NIIF</div>
	<table class="table-form">
		<tr>
			<td>Periodos Depreciados</td>
			<td><input type="text" readonly="readonly" value="<?php echo $periodos ?>"></td>
		</tr>
		<tr>
			<td>Costo</td>
			<td><input type="text" readonly="readonly" value="<?php echo round($costo,$_SESSION['DECIMALESMONEDA']); ?>"></td>
		</tr>
		<tr>
			<td>Depreciacion Acumulada</td>
			<td><input type="text" readonly="readonly" value="<?php echo round($deterioro_acumulado,$_SESSION['DECIMALESMONEDA']); ?>"></td>
		</tr>
		<tr>
			<td>Valor actual</td>
			<td><input type="text" readonly="readonly" value="<?php echo round($costo - $deterioro_acumulado,$_SESSION['DECIMALESMONEDA']); ?>"></td>
		</tr>
	</table>
	<div class="content-detail">
		<table class="table-form">
			<tr class="thead">
				<td>Fecha</td>
				<td>Consecutivo</td>
				<td>Deter. Acumulado</td>
				<td>Deterioro</td>
			</tr>
			<?php echo $bodyTable; ?>
		</table>
	</div>
</div>
