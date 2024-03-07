<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	// CONSULTAR LAS SECCIONES
	$sql="SELECT
				id,
				id_formato,
				codigo_seccion,
				codigo_seccion_padre,
				orden,
				nombre,
				tipo,
				descripcion_tipo,
				formula,
				totalizado,
				label_totalizado,
				formula_totalizado
			FROM informes_niif_formatos_secciones
			WHERE activo=1 AND id_empresa=$id_empresa AND id_formato = $id_formato";
	$query=$mysql->query($sql,$mysql->link);
	while ($row= $mysql->fetch_array($query)) {
		$codSeccion      = $row['codigo_seccion'];
		$codSeccionPadre = $row['codigo_seccion_padre'];
		$nomSeccion      = $row['nombre'];
		$optionSeccionPadre .= "<option value='$codSeccion'>$codSeccion - $nomSeccion</option>";
		$arraySeccion[$row['codigo_seccion']] = array(
											'id'                   => $row['id'],
											'id_formato'           => $row['id_formato'],
											'codigo_seccion'       => $row['codigo_seccion'],
											'codigo_seccion_padre' => $row['codigo_seccion_padre'],
											'orden'                => $row['orden'],
											'nombre'               => $row['nombre'],
											'tipo'                 => $row['tipo'],
											'formula'              => $row['formula'],
											'descripcion_tipo'     => $row['descripcion_tipo'],
											'totalizado'           => $row['totalizado'],
											'label_totalizado'     => $row['label_totalizado'],
											'formula_totalizado'   => $row['formula_totalizado'],
											);
		$arrayOrden[$codSeccionPadre] = ($row['orden'] > $arrayOrden[$codSeccionPadre])? $row['orden'] : $arrayOrden[$codSeccionPadre] ;
	}

	// print_r($arraySeccion);

	if ($codigo_seccion>0) {

		$acumscript ="
						document.getElementById('codigo').value             = '".$arraySeccion[$codigo_seccion]['codigo_seccion']."';
						document.getElementById('seccion_padre').value      = '".$arraySeccion[$codigo_seccion]['codigo_seccion_padre']."';
						document.getElementById('orden').value              = '".$arraySeccion[$codigo_seccion]['orden']."';
						document.getElementById('nombre').value             = '".$arraySeccion[$codigo_seccion]['nombre']."';
						document.getElementById('formula').value            = '".$arraySeccion[$codigo_seccion]['formula']."';
						document.getElementById('tipo').value               = '".$arraySeccion[$codigo_seccion]['tipo']."';
						document.getElementById('descripcion_tipo').value   = '".$arraySeccion[$codigo_seccion]['descripcion_tipo']."';
						document.getElementById('totalizado').value         = '".$arraySeccion[$codigo_seccion]['totalizado']."';
						document.getElementById('label_totalizado').value   = '".$arraySeccion[$codigo_seccion]['label_totalizado']."';
						document.getElementById('formula_totalizado').value = '".$arraySeccion[$codigo_seccion]['formula_totalizado']."';

						//document.getElementById('tipo_documento').disabled = true;
						//document.getElementById('id_sucursal').disabled    = true;
						//document.getElementById('documento').style.width   = '190px';
						//document.getElementById('valor').readOnly          = true;

					";
	}
	else{
		$btn_buscar_doc = '<img onclick="buscar_documento_cruce()" src="img/buscar.png" id="img_buscar_doc">';
		$acumscript .= '
						Ext.getCmp("btn_eliminar").hide();
						Ext.getCmp("conf_cuentas").hide();
						';
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
			<td colspan="2">INFORMACION SECCION</td>
		</tr>
		<tr>
			<td>Codigo</td>
			<td><input type="text" style="width:190px;" data-requiere="true" id="codigo" data-value="" ></td>
		</tr>
		<tr>
			<td>Seccion Padre</td>
			<td>
				<select style="width:190px;" id="seccion_padre" onchange="setOrden(this.value)">
					<option value="">Seleccione...</option>
					<?php echo $optionSeccionPadre; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Orden</td>
			<td ><input type="text"  style="width:190px;"  id="orden" readonly></td>
		</tr>
		<tr>
			<td>Nombre</td>
			<td ><input type="text"  style="width:190px;"  id="nombre" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Tipo</td>
			<td ><input type="text" style="width:190px;" id="tipo" ></td>
		</tr>
		<tr>
			<td>Descripcion Tipo</td>
			<td ><input type="text" style="width:190px;" id="descripcion_tipo"  ></td>
		</tr>
		<tr>
			<td>Formula</td>
			<td ><input type="text"  style="width:190px;"  id="formula" ></td>
		</tr>
		<tr>
			<td>Totalizado</td>
			<td>
				<select style="width:190px;" data-requiere="true" id="totalizado" >
					<option value="false">No</option>
					<option value="true">Si</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Titulo Totalizado</td>
			<td ><input type="text" style="width:190px;" id="label_totalizado"  ></td>
		</tr>
		<tr>
			<td>Formula Totalizado</td>
			<td ><input type="text" style="width:190px;" id="formula_totalizado"  ></td>
		</tr>
	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	<?php echo $acumscript; ?>

	function setOrden(codigo_seccion) {
		var arrayOrden = JSON.parse('<?php echo json_encode($arrayOrden); ?>')
		,	orden = (arrayOrden[codigo_seccion]*1)+1

		document.getElementById('orden').value = isNaN(orden) ? 1 : orden ;
	}

	function guardarActualizarSeccion() {
		var opc = '<?php echo $codigo_seccion ?>' > 0 ? 'actualizarSeccion' : 'agregarSeccion';
		var codigo           = document.getElementById('codigo').value
		,	seccion_padre    = document.getElementById('seccion_padre').value
		,	orden            = document.getElementById('orden').value
		,	nombre           = document.getElementById('nombre').value
		,	tipo             = document.getElementById('tipo').value
		,	descripcion_tipo = document.getElementById('descripcion_tipo').value
		,	formula          = document.getElementById('formula').value
		,	totalizado       = document.getElementById('totalizado').value
		,	label_totalizado      = document.getElementById('label_totalizado').value
		,	formula_totalizado    = document.getElementById('formula_totalizado').value

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'configuracion_informes_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                : opc,
				id_seccion         : '<?php echo $arraySeccion[$codigo_seccion]['id']; ?>',
				id_formato         : '<?php echo $id_formato; ?>',
				codigo             : codigo,
				seccion_padre      : seccion_padre,
				orden              : orden,
				nombre             : nombre,
				tipo               : tipo,
				descripcion_tipo   : descripcion_tipo,
				formula            : formula,
				totalizado         : totalizado,
				label_totalizado   : label_totalizado,
				formula_totalizado : formula_totalizado,
			}
		});
	}

	function cuentasSeccion() {
		Win_Ventana_cuentas_seccion = new Ext.Window({
		    width       : 650,
		    height      : 600,
		    id          : 'Win_Ventana_cuentas_seccion',
		    title       : 'Cuentas de la seccion',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_informes_niif/cuentas_secciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            codigo_seccion    : '<?php echo $codigo_seccion; ?>',
					id_formato    : '<?php echo $id_formato; ?>',
		        }
		    },
		}).show();
	}

	function eliminarSeccion() {
		if ('<?php echo $id_seccion ?>' <= 0)  return
		if (!confirm("Eliminar Seccion con Subsecciones y filas? ")) return

		MyLoading2('on',{texto:'Eliminando Seccion'});
		Ext.get('loadForm').load({
			url     : 'configuracion_informes_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc              : 'eliminarSeccion',
				id_seccion       : '<?php echo $id_seccion; ?>',
				id_formato       : '<?php echo $id_formato; ?>',
			}
		});

	}

</script>