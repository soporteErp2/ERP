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
				formula,
				totalizado,
				label_totalizado,
				formula_totalizado
			FROM informes_formatos_secciones
			WHERE activo=1 AND id_empresa=$id_empresa AND id_formato = $id_formato";
	$query=$mysql->query($sql,$mysql->link);
	while ($row= $mysql->fetch_array($query)) {
		$idSeccion          = $row['id'];
		$codSeccionPadre    = $row['codigo_seccion_padre'];
		$nomSeccion         = $row['nombre'];
		$optionSeccionPadre .= "<option value='$idSeccion'>$idSeccion - $nomSeccion</option>";
		$arraySeccion[$idSeccion] = array(
											'id'                   => $row['id'],
											'id_formato'           => $row['id_formato'],
											'codigo_seccion'       => $row['codigo_seccion'],
											'codigo_seccion_padre' => $row['codigo_seccion_padre'],
											'orden'                => $row['orden'],
											'nombre'               => $row['nombre'],
											'formula'              => $row['formula'],
											'totalizado'           => $row['totalizado'],
											'label_totalizado'     => $row['label_totalizado'],
											'formula_totalizado'   => $row['formula_totalizado'],
											);
		$arrayOrden[$codSeccionPadre] = ($row['orden'] > $arrayOrden[$codSeccionPadre])? $row['orden'] : $arrayOrden[$codSeccionPadre] ;
	}

	// print_r($arraySeccion);

	if ($id_seccion>0) {
		$seccion = $arraySeccion[$id_seccion]['nombre'];
		$acumscript ="
						//document.getElementById('codigo').value             = '".$arraySeccion[$id_seccion]['codigo_seccion']."';
						document.getElementById('seccion_padre').value      = '".$arraySeccion[$id_seccion]['codigo_seccion_padre']."';
						document.getElementById('orden').value              = '".$arraySeccion[$id_seccion]['orden']."';
						document.getElementById('nombre').value             = '".$arraySeccion[$id_seccion]['nombre']."';
						document.getElementById('formula').value            = '".$arraySeccion[$id_seccion]['formula']."';
						document.getElementById('totalizado').value         = '".$arraySeccion[$id_seccion]['totalizado']."';
						document.getElementById('label_totalizado').value   = '".$arraySeccion[$id_seccion]['label_totalizado']."';
						document.getElementById('formula_totalizado').value = '".$arraySeccion[$id_seccion]['formula_totalizado']."';

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
			<td>Orden</td>
			<td ><input type="text"  style="width:190px;"  id="orden" readonly></td>
		</tr>
		<tr>
			<td>Nombre</td>
			<td ><input type="text"  style="width:190px;"  id="nombre" data-requiere="true"></td>
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
		var opc = '<?php echo $id_seccion ?>' > 0 ? 'actualizarSeccion' : 'agregarSeccion';
		var seccion_padre    = document.getElementById('seccion_padre').value
		,	orden            = document.getElementById('orden').value
		,	nombre           = document.getElementById('nombre').value
		,	formula          = document.getElementById('formula').value
		,	totalizado       = document.getElementById('totalizado').value
		,	label_totalizado      = document.getElementById('label_totalizado').value
		,	formula_totalizado    = document.getElementById('formula_totalizado').value

		seccion_padre = (seccion_padre=='')? 0 : seccion_padre ;

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'informes_formatos/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                : opc,
				id_seccion         : '<?php echo $id_seccion; ?>',
				id_formato         : '<?php echo $id_formato; ?>',
				seccion_padre      : seccion_padre,
				orden              : orden,
				nombre             : nombre,
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
		    height      : 400,
		    id          : 'Win_Ventana_cuentas_seccion',
		    title       : 'Cuentas de la fila',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'informes_formatos/cuentas_secciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_seccion : '<?php echo $id_seccion; ?>',
					seccion    : '<?php echo $seccion; ?>',
					id_formato : '<?php echo $id_formato; ?>',
		        }
		    },
		}).show();
	}

	function eliminarSeccion() {
		if ('<?php echo $id_seccion ?>' <= 0)  return
		if (!confirm("Eliminar Seccion con Subsecciones y filas? ")) return

		MyLoading2('on',{texto:'Eliminando Seccion'});
		Ext.get('loadForm').load({
			url     : 'informes_formatos/bd/bd.php',
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

	function ventana_centro_costos() {
		Win_Ventana_centro_costo = new Ext.Window({
		    width       : 500,
		    height      : 500,
		    id          : 'Win_Ventana_centro_costo',
		    title       : 'filtrar las cuentas por centro de costos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'informes_formatos/formatos_secciones_centro_costos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_formato : '<?php echo $id_formato; ?>',
					id_seccion : '<?php echo $id_seccion; ?>',
					seccion    : '<?php echo $seccion; ?>',
		        }
		    },
		}).show();
	}

</script>