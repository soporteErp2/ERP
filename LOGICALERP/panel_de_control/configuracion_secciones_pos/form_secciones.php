<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];

	// CONSULTAR LAS SUCURSALES Y LAS BODEGAS
	$sql="SELECT id,nombre,id_sucursal,sucursal FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arraySucursales[$row['id_sucursal']]=$row['sucursal'];
		$arrayBodegas[$row['id_sucursal']][$row['id']]=$row['nombre'];
	}

	foreach ($arraySucursales as $id_sucursal => $sucursal) {
		// $selectSucursal .= "<option value='$id_sucursal' >$sucursal</option>";
		// $acumscript .= "arrayBodegasConfig[$id_sucursal]=[];";
		foreach ($arrayBodegas[$id_sucursal] as $id_bodega => $bodega) {
			// $acumscript .= "arrayBodegasConfig[$id_sucursal][$id_bodega] = '$bodega' ;";
			$selectBodega .= "<option value='$id_bodega' >$bodega</option>";
		}
	}

	// CONSULTAR LAS SECCIONES
	$sql="SELECT
				id,
				nombre,
				id_padre,
				restaurante,
				id_sucursal,
				id_bodega,
				id_centro_costos,
				codigo_centro_costos,
				eventos_asiste,
				codigo_transaccion,
				centro_costos,
				cuenta_ingreso_colgaap,
				cuenta_ingreso_niif,
				cambia_precio_items,
				cuenta_pago,
				metodo_pago
			FROM ventas_pos_secciones
			WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row= $mysql->fetch_array($query)){
		$codSeccion      = $row['codigo_seccion'];
		$codSeccionPadre = $row['codigo_seccion_padre'];
		$nomSeccion      = $row['nombre'];
		$optionSeccionPadre .= "<option value='$row[id]'>$row[nombre]</option>";
		$arraySeccion[$row['id']] = array(
										'nombre'                 => $row['nombre'],
										'id_padre'               => $row['id_padre'],
										'restaurante'            => $row['restaurante'],
										'id_sucursal'            => $row['id_sucursal'],
										'id_bodega'              => $row['id_bodega'],
										'id_centro_costos'       => $row['id_centro_costos'],
										'codigo_centro_costos'   => $row['codigo_centro_costos'],
										'centro_costos'          => $row['centro_costos'],
										'cuenta_ingreso_colgaap' => $row['cuenta_ingreso_colgaap'],
										'cuenta_ingreso_niif'    => $row['cuenta_ingreso_niif'],
										'eventos_asiste'         => $row['eventos_asiste'],
										'codigo_transaccion'     => $row['codigo_transaccion'],
										'cambia_precio_items'    => $row['cambia_precio_items'],
										'cuenta_pago' 			 => $row['cuenta_pago'],
										'metodo_pago' 			 => $row['metodo_pago']
										);
		// $arrayOrden[$codSeccionPadre] = ($row['orden'] > $arrayOrden[$codSeccionPadre])? $row['orden'] : $arrayOrden[$codSeccionPadre] ;
	}

	if ($id_seccion>0) {

		$acumscript .="
						document.getElementById('seccion_padre').value          = '".$arraySeccion[$id_seccion]['id_padre']."';
						document.getElementById('restaurante').value            = '".$arraySeccion[$id_seccion]['restaurante']."';
						document.getElementById('centro_costos').value          = '".$arraySeccion[$id_seccion]['codigo_centro_costos']." - ".$arraySeccion[$id_seccion]['centro_costos']."';
						document.getElementById('centro_costos').title          = '".$arraySeccion[$id_seccion]['codigo_centro_costos']." - ".$arraySeccion[$id_seccion]['centro_costos']."';
						document.getElementById('centro_costos').dataset.id     = '".$arraySeccion[$id_seccion]['id_centro_costos']."';
						document.getElementById('centro_costos').dataset.codigo = '".$arraySeccion[$id_seccion]['codigo_centro_costos']."';
						document.getElementById('centro_costos').dataset.nombre = '".$arraySeccion[$id_seccion]['centro_costos']."';
						document.getElementById('cuenta_ingreso_colgaap').value = '".$arraySeccion[$id_seccion]['cuenta_ingreso_colgaap']."';
						document.getElementById('cuenta_ingreso_niif').value    = '".$arraySeccion[$id_seccion]['cuenta_ingreso_niif']."';
						document.getElementById('eventos_asiste').value         = '".$arraySeccion[$id_seccion]['eventos_asiste']."';
						document.getElementById('cod_tx').value                 = '".$arraySeccion[$id_seccion]['codigo_transaccion']."';
						document.getElementById('cambia_precio_items').value    = '".$arraySeccion[$id_seccion]['cambia_precio_items']."';
						document.getElementById('cuenta_pago').value    		= '".$arraySeccion[$id_seccion]['cuenta_pago']."';
						document.getElementById('metodo_pago').value    		= '".$arraySeccion[$id_seccion]['metodo_pago']."';
						var options                                             = document.getElementById('seccion_padre').options
						for (const prop in options) {
						  if(options[prop].value==$id_seccion){
						  	document.getElementById('seccion_padre').options[prop].disabled = true;
						  }
						}
						document.getElementById('nombre').value = '".$arraySeccion[$id_seccion]['nombre']."';
					";
		if ($arraySeccion[$id_seccion]['id_sucursal']>0) {
			$acumscript .= "document.getElementById('bodega').value = ".$arraySeccion[$id_seccion]['id_bodega'].";
							//document.getElementById('sucursal').value = ".$arraySeccion[$id_seccion]['id_sucursal'].";
							//cambiaSucursal(".$arraySeccion[$id_seccion]['id_sucursal'].",".$arraySeccion[$id_seccion]['id_bodega'].");
							";
		}
	}
	else{
		$acumscript .= 'Ext.getCmp("btn_eliminar").hide();
						//Ext.getCmp("conf_cuentas").hide();
						';
	}

	// consultar los metodos de pago
	$sql = "SELECT id,nombre FROM configuracion_metodos_pago WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row = $mysql->fetch_array($query)) {
		$select_metodo_pago .= "<option value='$row[id]'>$row[nombre]</option>";
	}
	

	//consultar las cuentas de pago
	$sql = "SELECT id,nombre,cuenta FROM configuracion_cuentas_pago WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row = $mysql->fetch_array($query)) {
		$select_cuentas_pago .= "<option value='$row[id]'>$row[cuenta] - $row[nombre] </option>";
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
			<td colspan="3">INFORMACION SECCION</td>
		</tr>
		<tr>
			<td>Seccion Padre</td>
			<td>
				<select style="width:190px;" id="seccion_padre">
					<option value="">Sin agrupar</option>
					<?php echo $optionSeccionPadre; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nombre</td>
			<td ><input type="text"  style="width:190px;"  id="nombre" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Restaurante</td>
			<td>
				<select id="restaurante" data-requiere="true" style="width:190px;" onchange="cambiaRestaurante()">
					<option value="No">No</option>
					<option value="Si">Si</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Bodega</td>
			<td>
				<select id="bodega" data-requiere="true" style="width:190px;">
					<option value="">Seleccione</option>
					<?php echo $selectBodega; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Centro de Costos</td>
			<td ><input type="text"  style="width:190px;"  id="centro_costos" readonly="readonly"></td>
			<td><img src="img/search.png" alt="" title="Buscar centro de costos" onclick="ventanaCentroCostos()"></td>
		</tr>
		<tr>
			<td>Cuenta de ingreso (colgaap)</td>
			<td ><input type="text"  style="width:190px;"  id="cuenta_ingreso_colgaap" ></td>
		</tr>
		<tr>
			<td>Cuenta de ingreso (Niif)</td>
			<td ><input type="text"  style="width:190px;"  id="cuenta_ingreso_niif" ></td>
		</tr>
		<tr>
			<td>Eventos Asiste</td>
			<td>
				<select id="eventos_asiste" data-requiere="true" style="width:190px;" onchange="" >
					<option value="No">No</option>
					<option value="Si">Si</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Cod. Tx</td>
			<td ><input type="text"  style="width:190px;"  id="cod_tx"></td>
		</tr>
		<tr>
			<td>Permitir modificar precio de items al vender</td>
			<td>
				<select id="cambia_precio_items" data-requiere="true" style="width:190px;" onchange="" >
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select>
			</td>
		</tr>
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="3">INFORMACION FACTURACION ELECTRONICA</td>
		</tr>
		<tr>
			<td>Cuenta de pago</td>
			<td >
				<select id="cuenta_pago" data-requiere="true" style="width:190px;">
					<option value="">Seleccione</option>
					<?php echo $select_cuentas_pago; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Metodo de pago</td>
			<td >
				<select id="metodo_pago" data-requiere="true" style="width:190px;">
					<option value="">Seleccione</option>
					<?php echo $select_metodo_pago; ?>
				</select>
			</td>
		</tr>
	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>
	var arrayBodegasConfig = [];
	<?php echo $acumscript; ?>
	function setOrden(codigo_seccion) {
		var arrayOrden = JSON.parse('<?php echo json_encode($arrayOrden); ?>')
		,	orden = (arrayOrden[codigo_seccion]*1)+1

		document.getElementById('orden').value = isNaN(orden) ? 1 : orden ;
	}

	function cambiaRestaurante(){
		// var parentSucursal = document.getElementById('sucursal').parentNode.parentNode
		var	parentBodega   = document.getElementById('bodega').parentNode.parentNode
		,	restaurante    = document.getElementById('restaurante').value
		if (restaurante=='No'){
			// parentBodega.style.visibility = "hidden";
			parentBodega.style.display = "none";
		}
		else{
			// parentBodega.style.visibility = "visible";
			parentBodega.style.display = "contents";
		}
	}

	function cambiaSucursal(id_sucursal,id_bodega=0) {
		var selectBodega = `<option value="" >Seleccione</option>`;
		if (id_sucursal==0 || id_sucursal=='') {
			document.getElementById('bodega').innerHTML = selectBodega;
			return
		}
		var selectBodega = `<option value="" >Seleccione</option>`;
		arrayBodegasConfig[id_sucursal].forEach((value,index) => {
			selectBodega += `<option value="${index}" >${value}</option>`;
			// console.log(value,index);
      	});
		document.getElementById('bodega').innerHTML = selectBodega;
		if (id_bodega>0){
			document.getElementById('bodega').value = id_bodega;
		}
	}

	function guardarActualizarSeccion() {
		var opc            = '<?php echo $id_seccion ?>' > 0 ? 'actualizarSeccion' : 'agregarSeccion';
		let nombre                 = document.getElementById('nombre').value
		,	id_padre               = document.getElementById('seccion_padre').value
		,	restaurante            = document.getElementById('restaurante').value
		,	bodega                 = document.getElementById('bodega').value
		,	inputCcos              = document.getElementById('centro_costos')
		,	idCcos                 = inputCcos.dataset.id
		,	codigoCcos             = inputCcos.dataset.codigo
		,	nombreCcos             = inputCcos.dataset.nombre
		,	cuenta_ingreso_colgaap = document.getElementById('cuenta_ingreso_colgaap').value
		,	cuenta_ingreso_niif    = document.getElementById('cuenta_ingreso_niif').value
		,	eventos_asiste         = document.getElementById('eventos_asiste').value
		,	cod_tx                 = document.getElementById('cod_tx').value
		,	cambia_precio_items    = document.getElementById('cambia_precio_items').value
		,	cuenta_pago    		   = document.getElementById('cuenta_pago').value
		,	metodo_pago   		   = document.getElementById('metodo_pago').value
		


		// VALIDACION
		if (nombre==''){ alert("el campo nombre no puede estar vacio!"); return; }
		if (restaurante=='Si' && (/*sucursal=='' ||*/ bodega=='') ){
			alert("Si es un restaurante debe seleccionar la bodega y sucursal para tomar de alli el inventario");
			return;
		}

		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'configuracion_secciones_pos/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc,
				id_seccion : '<?php echo $id_seccion; ?>',
				nombre,                 
				id_padre,               
				restaurante,            
				bodega,                 
				idCcos,                 
				codigoCcos,             
				nombreCcos,             
				cuenta_ingreso_colgaap, 
				cuenta_ingreso_niif,    
				eventos_asiste,         
				cod_tx,                 
				cambia_precio_items,    
				cuenta_pago,
				metodo_pago,
			}
		});
	}

	function eliminarSeccion(){
		if ('<?php echo $id_seccion ?>' <= 0)  return
		if (!confirm("Eliminar Seccion del sistema? ")) return

		MyLoading2('on',{texto:'Eliminando Seccion'});
		Ext.get('loadForm').load({
			url     : 'configuracion_secciones_pos/bd/bd.php',
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

	cambiaRestaurante();

	var ventanaCentroCostos = ()=>{

		Win_Ventana_ventanaCcos = new Ext.Window({
		    width       : 560,
		    height      : 460,
		    id          : 'Win_Ventana_ventanaCcos',
		    title       : 'Buscar el centro de costos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            cargaFunction : 'renderCentroCostos(id);return;',
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
		                    handler     : function(){ BloqBtn(this); Win_Ventana_ventanaCcos.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	var renderCentroCostos = (id) => {
		let inputCcos = document.getElementById('centro_costos')
		,	codigo = document.getElementById(`div_CentroCostos_codigo_${id}`).innerHTML
		,	nombre = document.getElementById(`div_CentroCostos_nombre_${id}`).innerHTML
		inputCcos.dataset.id     = id;
		inputCcos.dataset.codigo = codigo;
		inputCcos.dataset.nombre = nombre;
		inputCcos.value = `${codigo} - ${nombre}`
		inputCcos.title = `${codigo} - ${nombre}`
		Win_Ventana_ventanaCcos.close();
	}

</script>