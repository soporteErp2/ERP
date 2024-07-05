<?php



	// CONSULTAR LA INFORMACION DEL EMPLEADO
	$sql="SELECT
				grupo,
				cod_grupo,
				cod_subgrupo,
				subgrupo,
				codigo_centro_costos,
				centro_costos,
				depreciable,
				code_bar,
				nombre_equipo,
				tipo,
				fecha_compra,
				documento_referencia,
				documento_referencia_consecutivo,
				costo,
				fecha_vencimiento_garantia,
				vida_util,
				marca,
				modelo,
				unidad,
				numero_piezas,
				color,
				descripcion1,
				descripcion2
			FROM activos_fijos WHERE activo=1 AND id=$id_activo";
	$query=$mysql->query($sql,$mysql->link);
	$grupo                      = $mysql->result($query,0,'grupo');
	$cod_grupo                  = $mysql->result($query,0,'cod_grupo');
	$cod_subgrupo               = $mysql->result($query,0,'cod_subgrupo');
	$subgrupo                   = $mysql->result($query,0,'subgrupo');
	$codigo_centro_costos       = $mysql->result($query,0,'codigo_centro_costos');
	$centro_costos              = $mysql->result($query,0,'centro_costos');
	$depreciable                = $mysql->result($query,0,'depreciable');
	$code_bar                   = $mysql->result($query,0,'code_bar');
	$nombre_equipo              = $mysql->result($query,0,'nombre_equipo');
	$tipo                       = $mysql->result($query,0,'tipo');
	$fecha_compra               = $mysql->result($query,0,'fecha_compra');
	$documento_referencia         = $mysql->result($query,0,'documento_referencia');
	$documento_referencia_consecutivo           = $mysql->result($query,0,'documento_referencia_consecutivo');
	$costo                      = $mysql->result($query,0,'costo');
	$fecha_vencimiento_garantia = $mysql->result($query,0,'fecha_vencimiento_garantia');
	$vida_util                  = $mysql->result($query,0,'vida_util');
	$marca                      = $mysql->result($query,0,'marca');
	$modelo                     = $mysql->result($query,0,'modelo');
	$unidad                     = $mysql->result($query,0,'unidad');
	$numero_piezas              = $mysql->result($query,0,'numero_piezas');
	$color                      = $mysql->result($query,0,'color');
	$descripcion1               = $mysql->result($query,0,'descripcion1');
	$descripcion2               = $mysql->result($query,0,'descripcion2');

	if ($tipo=='terreno') 						{ $tipo = 'Terreno'; }
	if ($tipo=='equipo_oficina') 				{ $tipo = 'Equipo de oficina'; }
	if ($tipo=='maquinaria') 					{ $tipo = 'Maquinaria y Equipo'; }
	if ($tipo=='equipo_computo_comunicacion') 	{ $tipo = 'Equipo de Computo y Comunicacion'; }
	if ($tipo=='construcciones_edificaciones') 	{ $tipo = 'Construcciones y edificaciones'; }

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

	<table class="table-form">
		<!-- <tr class="thead">
			<td colspan="2">INFORMACIÓN BASICA</td>
		</tr> -->
		<tr>
			<td>Grupo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $grupo ?>"></td>
		</tr>
		<tr>
			<td>Subgrupo</td>
			<td><input type="text" readonly="readonly" value="<?php echo $subgrupo ?>"></td>
		</tr>
		<tr>
			<td>Centro de Costos</td>
			<td><input type="text" readonly="readonly" style="width:50px;border-right:none;" value="<?php echo $codigo_centro_costos ?>"><input type="text" readonly="readonly" style="width:190px;" value="<?php echo $centro_costos ?>"></td>
		</tr>
		<tr>
			<td>Depreciable</td>
			<td><input type="text" readonly="readonly" value="<?php echo $depreciable ?>"></td>
		</tr>
		<tr>
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
		</tr>

	</table>
</div>
