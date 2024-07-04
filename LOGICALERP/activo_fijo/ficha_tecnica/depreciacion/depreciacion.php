<?php
	$id_empresa=$_SESSION['EMPRESA'];

	// CONSULTAR LA INFORMACION
	$sql="SELECT
				vida_util,
				depreciacion_acumulada,
				depreciacion_acumulada_niif,
				costo
			FROM activos_fijos WHERE activo=1 AND id=$id_activo";
	$query                       = $mysql->query($sql,$mysql->link);
	$vida_util                   = $mysql->result($query,0,'vida_util');
	$depreciacion_acumulada      = $mysql->result($query,0,'depreciacion_acumulada');
	$depreciacion_acumulada_niif = $mysql->result($query,0,'depreciacion_acumulada_niif');
	$costo                       = $mysql->result($query,0,'costo');

	// CUENTA PERIODOS DEPRECIADOS COLGAAP
	// CONSULTAMOS LOS DOCUMENTOS DEL ACTIVO
	$sql="SELECT id_depreciacion,valor,depreciacion_acumulada FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_empresa=$id_empresa AND id_activo_fijo=$id_activo";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$whereId.=($whereId=='')? 'id='.$row['id_depreciacion'] : ' OR id='.$row['id_depreciacion'] ;
		$arrayValor[$row['id_depreciacion']]=array('valor'=>$row['valor'],'depreciacion_acumulada'=>$row['depreciacion_acumulada']);
	}

	$sql1="SELECT COUNT(*) AS cuenta_colgaap  FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId) AND estado=1 AND sinc_nota='colgaap' ";
	$query1=mysql_query($sql1,$link);
	$cuenta_colgaap = mysql_result($query1,0, 'cuenta_colgaap');
	//:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
	// CUENTA PERIODOS DEPRECIADOS NIIF
	// CONSULTAMOS LOS DOCUMENTOS DEL ACTIVO
	$sql="SELECT id_depreciacion,valor,depreciacion_acumulada FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_empresa=$id_empresa AND id_activo_fijo=$id_activo";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$whereId.=($whereId=='')? 'id='.$row['id_depreciacion'] : ' OR id='.$row['id_depreciacion'] ;
		$arrayValor[$row['id_depreciacion']]=array('valor'=>$row['valor'],'depreciacion_acumulada'=>$row['depreciacion_acumulada']);
	}

	$sql="SELECT COUNT(*) AS cuenta_niif  FROM activos_fijos_depreciaciones WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId) AND estado=1 AND sinc_nota='niif' ";
	$query=mysql_query($sql,$link);
	$cuenta_niif = mysql_result($query,0, 'cuenta_niif');
?>

<style>
	.content-personal-info{
		padding-left : 15px;
		width        : calc(100% - 15px);
	}
</style>

<div class="content-personal-info">
<!-- 	<div class="buttom-content">
		<button class="button" data-value="save" onclick="guardar_datos_personales()">Guadar</button>
	</div> -->
	<div class="separator-body" >COLGAAP</div>
	<table class="table-form">
		<!-- <tr class="thead">
			<td colspan="2">INFORMACIÓN BASICA</td>
		</tr> -->
		<tr>
			<td>Vida util</td>
			<td><input type="text" readonly="readonly" value="<?php echo $vida_util ?>"></td>
		</tr>
		<tr>
			<td>Periodos Depreciados</td>
			<td><input type="text" readonly="readonly" value="<?php echo $cuenta_colgaap ?>"></td>
		</tr>
		<tr>
			<td>Costo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $costo ?>"></td>
		</tr>
		<tr>
			<td>Depreciacion Acumulada</td>
			<td><input type="text" readonly="readonly" value="<?php echo $depreciacion_acumulada ?>"></td>
		</tr>
		<tr>
			<td>Valor actual</td>
			<td><input type="text" readonly="readonly" value="<?php echo $costo-$depreciacion_acumulada ?>"></td>
		</tr>
		<!--<tr>
			<td>Codigo del Activo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $code_bar ?>"></td>
		</tr>
		<tr>
			<td>Nombre del Activo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $nombre_equipo ?>"></select></td>
		</tr>
		<tr>
			<td>Tipo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $tipo ?>"></td>
		</tr>
		<tr>
			<td>Fecha de Compra</td>
			<td><input type="text" readonly="readonly" value="<?php echo $fecha_compra ?>"></td>
		</tr>
		<tr>
			<td>Documento de Ingreso</td>
			<td><input type="text" readonly="readonly" style="width:50px;border-right:none;" value="<?php echo $documento_referencia ?>"><input type="text" readonly="readonly" style="width:190px;" value="<?php echo $documento_referencia_consecutivo ?>"></td>
		</tr>
		<tr>
			<td>Costo de compra</td>
			<td><input type="text" readonly="readonly" value="<?php echo $costo ?>"></td>
		</tr>
		<tr>
			<td>Fecha de Vencimiento Garantia</td>
			<td><input type="text" readonly="readonly" value="<?php echo $fecha_vencimiento_garantia ?>"></td>
		</tr>
		<tr>
			<td>Vida Util</td>
			<td><input type="text" readonly="readonly" value="<?php echo $vida_util ?>"></td>
		</tr>
		<tr>
			<td>Marca</td>
			<td><input type="text" readonly="readonly" value="<?php echo $marca ?>"></td>
		</tr>
		<tr>
			<td>Modelo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $modelo ?>"></td>
		</tr>
		<tr>
			<td>Unidad de Medida</td>
			<td><input type="text" readonly="readonly" value="<?php echo $unidad ?>"></td>
		</tr>
		<tr>
			<td>Numero de Piezas</td>
			<td><input type="text" readonly="readonly" value="<?php echo $numero_piezas ?>"></td>
		</tr>
		<tr>
			<td>Color</td>
			<td><input type="text" readonly="readonly" value="<?php echo $color ?>"></td>
		</tr>
		<tr>
			<td>Descripcion 1</td>
			<td><textarea readonly><?php echo $descripcion1 ?></textarea></td>
		</tr>
		<tr>
			<td>Descripcion 2</td>
			<td><textarea readonly><?php echo $descripcion2 ?></textarea></td>
		</tr>-->
	</table>
	<div class="separator-body" >NIIF</div>
	<table class="table-form">
		<tr>
			<td>Vida util</td>
			<td><input type="text" readonly="readonly" value="<?php echo $vida_util ?>"></td>
		</tr>
		<tr>
			<td>Periodos Depreciados</td>
			<td><input type="text" readonly="readonly" value="<?php echo $cuenta_niif ?>"></td>
		</tr>
		<tr>
			<td>Costo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $costo ?>"></td>
		</tr>
		<tr>
			<td>Depreciacion Acumulada</td>
			<td><input type="text" readonly="readonly" value="<?php echo $depreciacion_acumulada_niif ?>"></td>
		</tr>
		<tr>
			<td>Valor actual</td>
			<td><input type="text" readonly="readonly" value="<?php echo $costo-$depreciacion_acumulada_niif ?>"></td>
		</tr>
	</table>
</div>
