<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	// opcGrillaContable
	// id_documento
	
	// SI ES UNA ACTUALIZACION DEL GRUPO O ASIGNACION DE ITEMS
	if ($opcForm=='updateGroup') {
		$sql="SELECT * FROM ventas_facturas_grupos WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_venta=$id_documento AND id=$id_row";
		$query=$mysql->query($sql,$mysql->link);
		$codigo              = $mysql->result($query,0, 'codigo');
		$nombre              = $mysql->result($query,0, 'nombre');
		$cantidad            = $mysql->result($query,0, 'cantidad');
		$costo               = $mysql->result($query,0, 'costo_unitario');
		$descuento           = $mysql->result($query,0, 'descuento');
		$impuesto            = $mysql->result($query,0, 'valor_impuesto');
		$observaciones       = $mysql->result($query,0, 'observaciones');
		$id_impuesto         = $mysql->result($query,0, 'id_impuesto');
		$nombre_impuesto     = $mysql->result($query,0, 'nombre_impuesto');
		$codigo_impuesto     = $mysql->result($query,0, 'codigo_impuesto');
		$porcentaje_impuesto = $mysql->result($query,0, 'porcentaje_impuesto');
		echo "<script>console.log('$id_row');</script>";
		// $input_impuesto = "<input type='text' readonly style='width:190px;' value='$nombre_impuesto' id='info_impuesto_grupo' data-id='$id_impuesto' data-nombre='$nombre_impuesto' data-dianCode='$codigo_impuesto' data-valor='$porcentaje_impuesto' >";
	}

	$sql="SELECT id,impuesto,codigo_impuesto_dian,valor FROM impuestos WHERE activo=1 AND id_empresa=$id_empresa AND venta='Si' ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$input_impuesto .= "<option data-id='$row[id]' ".($id_impuesto==$row[id]? "selected" : "")." data-nombre='$row[impuesto]' data-dianCode='$row[codigo_impuesto_dian]' data-valor='$row[valor]' >
								$row[impuesto]
							</option>";
	}
?>

<style>
	img{
		cursor: pointer;
	}
</style>
<div class="content" >
	<table class="table-form" style="width:90%;" >
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="4">INFORMACION GENERAL DEL GRUPO</td>
		</tr>
		<tr>
			<td>Codigo</td>
			<td><input type="text" value="<?php echo $codigo; ?>" style="width:190px;" data-requiere="true" id="codigo_grupo" data-value="" ></td>
			<td>Nombre</td>
			<td><input type="text" style="width:190px;" value="<?php echo $nombre; ?>" data-requiere="true" id="nombre_grupo" data-value=""></td>
		</tr>

		<tr>
			<td>Cantidad</td>
			<td ><input type="number" style="width:190px;" value="1" id="cantidad_grupo" readonly=""></td>
			<td>Costo</td>
			<td ><input type="text" style="width:190px;" value="<?php echo $costo; ?>" id="costo_grupo" ></td>
		</tr>
		<tr>
			<td>Descuento</td>
			<td ><input type="text" style="width:190px;" value="<?php echo $descuento; ?>" id="descuento_grupo" ></td>
			<td>Impuesto</td>
			<td >
				<select style='width:190px;' id='info_impuesto_grupo' >
					<option>Exento</option>
					<?= $input_impuesto ?>
							
				</select>
				<!-- <?php echo $input_impuesto; ?> -->
				<!-- <select style="width: 190px;" name="" id="info_impuesto_grupo">
					<option value="" data-value="c" data-icon="1">xxx</option>
				</select> -->
				<!-- <input type="text" readonly style="width:190px;" value="<?php echo $impuesto; ?>" id="info_impuesto_grupo" placeholder="Busque "> -->
			</td>
			<!-- <td><img onclick="ventanaBuscarImpuesto()" src="img/buscar.png" id="img_buscar_doc"></td> -->
		</tr>
		<tr>
			<td>Valor Impuesto</td>
			<td ><input type="text" style="width:190px;" value="<?php echo $impuesto; ?>" id="impuesto_grupo" ></td>
		</tr>
		<tr>
			<td>Observaciones</td>
			<td colspan="3" ><textarea name="" cols="25" rows="10" style="width: 529px !important;" id="observaciones_grupo"><?php echo $observaciones; ?></textarea></td>
		</tr>

	</table>
	<?php
	if ($opcForm=='updateGroup') {

		$sql="SELECT
					FI.id,
					FI.codigo,
					FI.nombre,
					FI.cantidad,
					FI.costo_unitario,
					FI.tipo_descuento,
					FI.descuento,
					FI.valor_impuesto
				FROM
					ventas_facturas_inventario_grupos AS FIG
				INNER JOIN ventas_facturas_inventario AS FI ON FIG.id_inventario_factura_venta = FI.id
				WHERE
					FIG.activo = 1
				AND FIG.id_factura_venta = $id_documento
				AND FIG.id_grupo_factura_venta = $id_row";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$id             = $row['id'];
			$codigo         = $row['codigo'];
			$nombre         = $row['nombre'];
			$cantidad       = $row['cantidad'];
			$costo_unitario = $row['costo_unitario'];
			$tipo_descuento = $row['tipo_descuento'];
			$descuento      = $row['descuento'];
			$valor_impuesto = $row['valor_impuesto'];

			$subtotal = $cantidad*$costo_unitario;
			if ($descuento>0 && $tipo_descuento=='porcentaje') {
				$descuento = $subtotal*$descuento/100;
			}
			$impuesto = (($subtotal-$descuento)*$valor_impuesto)/100;
			$total = $subtotal-$descuento+$impuesto;

			$bodyTable .= "<tr id='tr_items_$id'>
									<td>$codigo</td>
									<td>$nombre</td>
									<td>$cantidad</td>
									<td>$descuento</td>
									<td>$subtotal</td>
									<td>$total</td>
									<td ><img src='img/delete.png' title ='Eliminar Item' onclick='eliminarItemGrupo($id);'></td>
								</tr>";
		}
	?>
	<div>
		<table class="table-form" style="width:98%;border-collapse: collapse;" id="items_grupos">
			<tr class="thead" style="background-color: #a2a2a2;text-align: center;border-bottom: 1px solid #FFF;">
				<td colspan="6">ARTICULOS DEL GRUPO</td>
			</tr>
			<tr class="thead" style="background-color: #a2a2a2;">
				<td>CODIGO</td>
				<td>ARTICULO</td>
				<td>CANTIDAD</td>
				<td>DESCUENTO</td>
				<td>PRECIO</td>
				<td>TOTAL</td>
				<td style="background-color: #FFF;"><img src="img/addItem.png" title ="Agregar Item" onclick="ventanaBuscarItems();"></td>
			</tr>
			<?php echo $bodyTable; ?>
		</table>
	</div>
	<?php } ?>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	<?php echo $acumscript; ?>

	function ventanaBuscarImpuesto() {

		Win_Ventana_impuesto = new Ext.Window({
		    width       : 540,
		    height      : 450,
		    id          : 'Win_Ventana_impuesto',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'facturacion/bd/grillaBuscarIva.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            var1 : 'var1',
		            var2 : 'var2',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_impuesto.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	// GUARDAR LA INFORMACION PRINCIPAL DEL GRUPO
	function saveUpdateGroup() {
		var opc             = ("<?php echo $opcForm; ?>"=='newGroup')? 'saveGroup' : 'updateGroup'
		,	codigo          = document.getElementById('codigo_grupo').value
		,	nombre          = document.getElementById('nombre_grupo').value
		,	cantidad        = document.getElementById('cantidad_grupo').value
		,	observaciones   = document.getElementById('observaciones_grupo').value
		,	id_bodega       = document.getElementById('filtro_ubicacion_FacturaVenta').value
		,	select_impuesto = document.getElementById('info_impuesto_grupo')
		,	id_impuesto     = select_impuesto.dataset.id
		,	nombre_impuesto = select_impuesto.dataset.nombre
		,	valor_impuesto  = select_impuesto.dataset.valor
		,	codigo_dian     = select_impuesto.dataset.dianCode
		,	costo_grupo     = document.getElementById('costo_grupo').value
		,	descuento_grupo = document.getElementById('descuento_grupo').value
		,	impuesto_grupo  = document.getElementById('impuesto_grupo').value

		// QUITAR CARACTERES ESPECIALES
		observaciones = observaciones.replace(/[\#\<\>\'\"]/g, '');

		if (codigo=="" || nombre=="" || cantidad=="") {alert("Aviso\nHay campos obligatorios vacios!"); return; }

		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc               : opc,
				codigo            : codigo,
				nombre            : nombre,
				cantidad          : cantidad,
				observaciones     : observaciones,
				id_bodega         : id_bodega,
				id_documento      : '<?php echo $id_documento; ?>',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				id_row            : '<?php echo $id_row; ?>',
				id_impuesto       : id_impuesto,
				nombre_impuesto   : nombre_impuesto,
				valor_impuesto    : valor_impuesto,
				codigo_dian       : codigo_dian,
				costo_unitario    : costo_grupo,
				descuento         : descuento_grupo,
			}
		});
	}

	// BUSCAR LOS ITEMS A ASIGNAR AL GRUPO
	function ventanaBuscarItems(){

		Win_Ventana_buscar_item = new Ext.Window({
		    width       : 650,
		    height      : 600,
		    id          : 'Win_Ventana_buscar_item',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'facturacion/bd/buscarItemsGrupo.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
					id_documento      : '<?php echo $id_documento; ?>',
					id_grupo          : '<?php echo $id_row; ?>',
					id_impuesto       : document.getElementById('info_impuesto_grupo').dataset.id
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_buscar_item.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function eliminarItemGrupo(id) {
		if (!confirm("Aviso\nQuitar el item del grupo?")) { return; }

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc               : 'eliminarItemGrupo',
				id_documento      : '<?php echo $id_documento; ?>',
				opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				id_grupo          : '<?php echo $id_row; ?>',
				id_inventario     : id,
			}
		});
	}

</script>
