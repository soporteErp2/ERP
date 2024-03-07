<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	//id_fila
	//id_seccion
	//id_formato

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
				totalizado,
				label_totalizado,
				formula_totalizado
			FROM informes_niif_formatos_secciones
			WHERE activo=1 AND id_empresa=$id_empresa AND id_formato = $id_formato";
	$query=$mysql->query($sql,$mysql->link);
	while ( $row = $mysql->fetch_array($query) ) {
		$idSeccion  = $row['id'];
		$codSeccion = $row['codigo_seccion'];
		$nomSeccion = $row['nombre'];
		$optionSeccionPadre .= "<option value='$codSeccion'>$codSeccion - $nomSeccion</option>";
	}

	// CONSULTAR LAS FILAS
	$sql="SELECT
				id,
				id_formato,
				id_seccion,
				codigo,
				orden,
				nombre,
				naturaleza,
				formula
			FROM informes_niif_formatos_secciones_filas
			WHERE activo=1 AND id_empresa=$id_empresa AND id_formato = $id_formato";
	$query=$mysql->query($sql,$mysql->link);
	while ($row= $mysql->fetch_array($query)) {
		$id_seccion      = $row['id_seccion'];
		$codSeccionPadre = $row['codigo_seccion_padre'];
		$nomSeccion      = $row['nombre'];
		// $optionSeccionPadre .= "<option value='$codSeccion'>$codSeccion - $nomSeccion</option>";
		$arrayFilas[$row['id']] = array(
										'id_formato' => $row['id_formato'],
										'id_seccion' => $row['id_seccion'],
										'codigo'     => $row['codigo'],
										'orden'      => $row['orden'],
										'nombre'     => $row['nombre'],
										'naturaleza' => $row['naturaleza'],
										'formula'    => $row['formula'],
										);
		$arrayOrden[$id_seccion] = ($row['orden'] > $arrayOrden[$id_seccion])? $row['orden'] : $arrayOrden[$id_seccion] ;
	}
	// print_r($arrayFilas);

	if ($id_fila>0) {

		$acumscript ="
						document.getElementById('seccion_padre').value = '".$arrayFilas[$id_fila]['id_seccion']."';
						document.getElementById('codigo').value        = '".$arrayFilas[$id_fila]['codigo']."';
						document.getElementById('orden').value         = '".$arrayFilas[$id_fila]['orden']."';
						document.getElementById('nombre').value        = '".$arrayFilas[$id_fila]['nombre']."';
						document.getElementById('naturaleza').value    = '".$arrayFilas[$id_fila]['naturaleza']."';
						document.getElementById('formula').value       = '".$arrayFilas[$id_fila]['formula']."';
					";
	}
	else{
		$acumscript .= 'Ext.getCmp("btn_eliminar").hide();
						Ext.getCmp("conf_cuentas").hide();';
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
			<td colspan="2">INFORMACION FILA</td>
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
			<td>Codigo</td>
			<td ><input type="text"  style="width:190px;"  id="codigo" ></td>
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
			<td>Naturaleza</td>
			<td>
				<select style="width:190px;" data-requiere="true" id="naturaleza" >
					<option value="debito">Debito</option>
					<option value="credito">Credito</option>
					<option value="deudor">Deudor</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Formula</td>
			<td ><input type="text"  style="width:190px;"  id="formula" ></td>
		</tr>
	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	<?php echo $acumscript; ?>

	function setOrden(id_seccion) {
		var arrayOrden = JSON.parse('<?php echo json_encode($arrayOrden); ?>')
		,	orden = (arrayOrden[id_seccion]*1)+1

		document.getElementById('orden').value = isNaN(orden) ? 1 : orden ;
	}

	function guardarActualizarFila() {
		var opc = '<?php echo $id_fila ?>' > 0 ? 'actualizarFila' : 'agregarFila';
		var seccion_padre = document.getElementById('seccion_padre').value
		,	codigo        = document.getElementById('codigo').value
		,	orden         = document.getElementById('orden').value
		,	nombre        = document.getElementById('nombre').value
		,	naturaleza    = document.getElementById('naturaleza').value
		,	formula       = document.getElementById('formula').value

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'configuracion_informes_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc           : opc,
				id_seccion    : '<?php echo $id_seccion; ?>',
				id_formato    : '<?php echo $id_formato; ?>',
				id_fila       : '<?php echo $id_fila; ?>',
				seccion_padre : seccion_padre,
				codigo        : codigo,
				orden         : orden,
				nombre        : nombre,
				naturaleza    : naturaleza,
				formula       : formula,
			}
		});
	}

	function eliminarSeccion() {
		if ('<?php echo $id_seccion ?>' <= 0)  return
		if (!confirm("Eliminar la Fila?")) return

		MyLoading2('on',{texto:'Eliminando Seccion'});
		Ext.get('loadForm').load({
			url     : 'configuracion_informes_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc          : 'eliminarSeccion',
				id_seccion   : '<?php echo $id_seccion; ?>',
				eliminarFila : '<?php echo $eliminarFila; ?>',
			}
		});
	}

	function cuentasFila() {
		Win_Ventana_cuentas_fila = new Ext.Window({
		    width       : 650,
		    height      : 600,
		    id          : 'Win_Ventana_cuentas_fila',
		    title       : 'Cuentas de la fila',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_informes_niif/cuentas_filas.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            id_seccion    : '<?php echo $id_seccion; ?>',
					id_formato    : '<?php echo $id_formato; ?>',
					id_fila       : '<?php echo $id_fila; ?>',
		        }
		    },
		}).show();
	}

</script>