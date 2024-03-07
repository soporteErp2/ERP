<?php
	$id_empresa = $_SESSION['EMPRESA'];

	// CUENTA PERIODOS DEPRECIADOS COLGAAP
	// CONSULTAMOS LOS DOCUMENTOS DEL ACTIVO
	$sql = "SELECT
						AFD.id,
						AFD.consecutivo_niif,
						AFD.fecha_inicio,
						ADFI.id_depreciacion,
						ADFI.valor,
						ADFI.depreciacion_acumulada_niif
					FROM
						activos_fijos_depreciaciones AS AFD
					INNER JOIN
						activos_fijos_depreciaciones_inventario AS ADFI ON ADFI.id_depreciacion = AFD.id
					WHERE
						AFD.activo = 1
					AND
						AFD.id_empresa = $id_empresa
					AND
						AFD.estado = 1
					AND
						AFD.sinc_nota = 'niif'
					AND
						ADFI.id_activo_fijo = $id_activo
					ORDER BY
						fecha_inicio DESC,consecutivo_niif DESC";
	$query = $mysql->query($sql,$mysql->link);
	$periodos  = 0;
	$bodyTable = '';
	while($row = $mysql->fetch_array($query)){
		$bodyTable .=  "<tr>
											<td>".round($row['fecha_inicio'],$_SESSION['DECIMALESMONEDA'])."</td>
											<td>".round($row['consecutivo_niif'],$_SESSION['DECIMALESMONEDA'])."</td>
											<td>".round($row['depreciacion_acumulada_niif'],$_SESSION['DECIMALESMONEDA'])."</td>
											<td>".round($row['valor'],$_SESSION['DECIMALESMONEDA'])."</td>
										</tr>";
		$periodos ++;
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
	<div class="separator-body" >DEPRECIACION NIIF</div>
	<table class="table-form">
		<tr>
			<td>Vida util</td>
			<td><input type="text" readonly="readonly" value="<?php echo $vida_util_niif ?>"></td>
		</tr>
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
			<td><input type="text" readonly="readonly" value="<?php echo round($depreciacion_acumulada_niif,$_SESSION['DECIMALESMONEDA']); ?>"></td>
		</tr>
		<tr>
			<td>Valor actual</td>
			<td><input type="text" readonly="readonly" value="<?php echo round($costo - $depreciacion_acumulada_niif,$_SESSION['DECIMALESMONEDA']); ?>"></td>
		</tr>
	</table>
	<div class="content-detail">
		<table class="table-form">
			<tr class="thead">
				<td>Fecha</td>
				<td>Consecutivo</td>
				<td>Deprec. Acumulada</td>
				<td>Depreciacion</td>
			</tr>
			<?php echo $bodyTable; ?>
		</table>
	</div>
</div>
