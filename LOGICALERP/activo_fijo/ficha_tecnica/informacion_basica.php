<?php
	// SI ES PARA ACTUALIZAR EL ACTIVO
	if($id_activo > 0){
		if($id_documento_referencia_inventario > 0){
			$campoDocRef = "<td>Documento de Ingreso</td>
											<td>
												<input type='text' readonly style='width:50px;' id='tipo_documento_referencia' value='$documento_referencia'>
												<input type='text' readonly style='width:127px;' id='documento_referencia' value='$documento_referencia_consecutivo'>
											</td>
											<td style='padding:0px;'><img src='images/block.png' title='Cruzado en documento' style='width:24px;height:24px;' ></td>";
			if(user_permisos(237,'false') == 'true'){
				$acumScript .= "new Ext.form.DateField({
													format 			:	'Y-m-d',
													width				:	197,
													allowBlank	:	false,
													showToday		:	false,
													applyTo			:	'fecha_compra',
													editable		:	false
												});

												new Ext.form.DateField({
													format 			:	'Y-m-d',
													width				:	197,
													allowBlank	:	false,
													showToday		:	false,
													applyTo			:	'fecha_vencimiento_garantia',
													editable		:	false
												});

												new Ext.form.DateField({
													format 			:	'Y-m-d',
													width				:	197,
													allowBlank	:	false,
													showToday		:	false,
													applyTo			:	'fecha_vencimiento_tenencia',
													editable		:	false
												});";
			}
		}
		else{
			$campoDocRef = "<td>Documento de Ingreso</td>
											<td>
												<select style='width:50px;'  id='tipo_documento_referencia' data-requiere='true' onchange='limpiarDocIngreso()' >
													<option value='FC' title='Factura de compra'>FC</option>
													<option value='NCG' title='Nota contable general'>NCG</option>
												</select>
												<input type='text' readonly style='width:127px;' id='documento_referencia' value='$documento_referencia_consecutivo' data-requiere='true'>
											</td>
											<td style='padding:0px;' onclick='ventanaBuscarDocumentoIngreso()' title='Buscar Documento'><img src='images/buscar.png'></td>";
			$campoProveedor = "<td style='padding:0px;' onclick='ventanaBuscarProveedor()' title='Buscar Proveedor'><img src='images/buscar.png'></td>";
			$acumScript .= "document.getElementById('costo').readOnly = false;";

			if(user_permisos(237,'false') == 'true'){
				$acumScript .= "new Ext.form.DateField({
													format 			:	'Y-m-d',
													width				:	197,
													allowBlank	:	false,
													showToday		:	false,
													applyTo			:	'fecha_compra',
													editable		:	false
												});

												new Ext.form.DateField({
													format 			:	'Y-m-d',
													width				:	197,
													allowBlank	:	false,
													showToday		:	false,
													applyTo			:	'fecha_vencimiento_garantia',
													editable		:	false
												});

												new Ext.form.DateField({
													format 			:	'Y-m-d',
													width				:	197,
													allowBlank	:	false,
													showToday		:	false,
													applyTo			:	'fecha_vencimiento_tenencia',
													editable		:	false
												});";
			}
		}

		if(user_permisos(237,'false') != 'true'){
			$acumScript .= "var inputs = document.getElementsByTagName('input');
									    for(var i = 0; i < inputs.length; i++){
									      inputs[i].disabled = true;
									    }

											var selects = document.getElementsByTagName('select');
									    for(var i = 0; i < selects.length; i++){
									      selects[i].disabled = true;
									    }

											var textareas = document.getElementsByTagName('textarea');
									    for(var i = 0; i < textareas.length; i++){
									      textareas[i].disabled = true;
									    }

											var buttomContent = document.getElementsByClassName('buttom-content');
									    for(var i = 0; i < buttomContent.length; i++){
									      buttomContent[i].style.display = 'none';
									    }

											var imgSearch = document.getElementsByTagName('img');
									    for(var i = 0; i < imgSearch.length; i++){
												if(imgSearch[i].getAttribute('src') == 'images/buscar.png'){
													imgSearch[i].style.display = 'none';
												}
									    }

											var tableForm = document.getElementsByClassName('table-form');
									    for(var i = 0; i < tableForm.length; i++){
												tableForm[i].style.pointerEvents = 'none';
									    }
											";
		}

		$acumScript .= "document.getElementById('familia').value													= '$id_familia';
										buscarGrupo('$id_familia','$id_grupo');
										buscaSubgrupo('$id_grupo','$id_subgrupo');
										document.getElementById('centro_costos').value                    = '$codigo_centro_costos - $centro_costos';
										document.getElementById('centro_costos').title                    = '$codigo_centro_costos - $centro_costos';
										document.getElementById('centro_costos').dataset.id               = '$id_centro_costos';
										document.getElementById('centro_costos').dataset.codigo           = '$codigo_centro_costos';
										document.getElementById('centro_costos').dataset.nombre           = '$centro_costos';
										document.getElementById('funcionario_asignado').dataset.id        = '$id_funcionario_asignado';
										document.getElementById('funcionario_asignado').dataset.documento = '$documento_funcionario_asignado';
										document.getElementById('funcionario_asignado').dataset.nombre    = '$funcionario_asignado';
										document.getElementById('funcionario_asignado').value             = '$documento_funcionario_asignado - $funcionario_asignado';
										document.getElementById('funcionario_asignado').title             = '$documento_funcionario_asignado - $funcionario_asignado';
										document.getElementById('proveedor').dataset.id                   = '$id_proveedor';
										document.getElementById('proveedor').dataset.documento            = '$nit_proveedor';
										document.getElementById('proveedor').dataset.nombre               = '$proveedor';
										document.getElementById('proveedor').value                        = '$nit_proveedor - $proveedor';
										document.getElementById('proveedor').title                        = '$nit_proveedor - $proveedor';
										document.getElementById('unidad').value                           = '$id_unidad';
										document.getElementById('tipo').value															= '$tipo';
										document.getElementById('tenencia').value													= '$tenencia';
										document.getElementById('sincronizar_siip').value                 = '$sincronizar_siip';
										document.getElementById('codigo_automatico').value                = '$codigo_automatico';";
	}
	// SI ES UN NUEVO ACTIVO
	else{
		$campoDocRef = "<td>Documento de Ingreso</td>
										<td>
											<select style='width:50px;'  id='tipo_documento_referencia' data-requiere='true' onchange='limpiarDocIngreso()' >
												<option value='FC' title='Factura de compra'>FC</option>
												<option value='NCG' title='Nota contable general'>NCG</option>
											</select>
											<input type='text' readonly style='width:127px;' id='documento_referencia' value='$documento_referencia_consecutivo' data-requiere='true'>
										</td>
										<td style='padding:0px;' onclick='ventanaBuscarDocumentoIngreso()' title='Buscar Documento'><img src='images/buscar.png'></td>";
		$campoProveedor = "<td style='padding:0px;' onclick='ventanaBuscarProveedor()' title='Buscar Proveedor'><img src='images/buscar.png'></td>" ;
		$acumScript .= "document.getElementById('costo').readOnly = false;
										new Ext.form.DateField({
											format 			:	'Y-m-d',
											width				:	197,
											allowBlank	:	false,
											showToday		:	false,
											applyTo			:	'fecha_compra',
											editable		:	false
										});

										new Ext.form.DateField({
											format 			:	'Y-m-d',
											width				:	197,
											allowBlank	:	false,
											showToday		:	false,
											applyTo			:	'fecha_vencimiento_garantia',
											editable		:	false
										});

										new Ext.form.DateField({
											format 			:	'Y-m-d',
											width				:	197,
											allowBlank	:	false,
											showToday		:	false,
											applyTo			:	'fecha_vencimiento_tenencia',
											editable		:	false
										});";
	}

	// CONSULTAR LAS FAMILIAS
	$sql = "SELECT id,codigo,nombre FROM items_familia WHERE activo = 1 AND id_empresa = $id_empresa";
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)){
		$optionFamilia .="<option value='$row[id]' data-id='$row[id]' data-nombre='$row[nombre]' data-codigo='$row[codigo]' >$row[nombre]</option>";
	}

	// CONSULTAR LAS UNIDADES DE MEDIDA
	$sql = "SELECT id,nombre,unidades FROM inventario_unidades WHERE activo = 1 AND id_empresa = $id_empresa";
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)){
		$optionUnidades .="<option value='$row[id]' data-id='$row[id]' data-nombre='$row[nombre]' data-codigo='$row[codigo]' >$row[nombre]</option>";
	}
?>
<style>
	.content-personal-info{
		padding-left : 15px;
		width        : calc(100% - 15px);
	}
</style>
<div class="content-personal-info">
	<div class="buttom-content">
		<button class="button" data-value="save" onclick="validarCampos()">Guardar</button>
	</div>
	<div class="separator-body">GENERAL</div>
	<table class="table-form">
		<tr>
			<td>Familia</td>
			<td>
				<select id="familia" onchange="buscarGrupo(this.value)" data-requiere="true">
					<option value="">Seleccione...</option>
					<?php echo $optionFamilia; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Grupo</td>
			<td>
				<select id="grupo" onchange="buscaSubgrupo(this.value)" data-requiere="true">
				</select>
			</td>
		</tr>
		<tr>
			<td>Subgrupo</td>
			<td>
				<select id="subgrupo" data-requiere="true">
				</select>
			</td>
		</tr>
		<tr>
			<td>Codigo del Item</td>
			<td>
				<input type="text" id="code_bar" readonly value="<?php echo $code_bar; ?>">
			</td>
		</tr>
		<tr>
			<td>Codigo Automatico</td>
			<td>
				<select id="codigo_automatico" onchange="habilitarCodigoActivo(this.value)">
					<option value="">Seleccione...</option>
					<option value="si">Si</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Codigo del Activo</td>
			<td>
				<input type="text" id="codigo_activo" disabled data-requiere="true" value="<?php echo $codigo_activo ?>" data-id="<?php echo $id_item ?>">
			</td>
			<td style="padding:0px;" rowspan="2" onclick="ventanaBuscarItem()" title="Buscar Item">
				<img src="images/buscar.png">
			</td>
		</tr>
		<tr>
			<td>Nombre del Activo</td>
			<td>
				<input type="text" id="nombre_equipo" data-requiere="true" value="<?php echo $nombre_equipo ?>">
			</td>
		</tr>
		<tr>
			<td>Tipo</td>
			<td>
				<select id="tipo" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="terreno">Terreno</option>
					<option value="equipo_oficina">Equipo de oficina</option>
					<option value="maquinaria">Maquinaria y Equipo</option>
					<option value="equipo_computo_comunicacion">Equipo de Computo y Comunicacion</option>
					<option value="construcciones_edificaciones">Construcciones y edificaciones</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Fecha de Compra</td>
			<td>
				<input type="text" id="fecha_compra" value="<?php echo $fecha_compra ?>" data-requiere="true">
			</td>
		</tr>
		<tr>
			<?php echo $campoDocRef; ?>
		</tr>
		<tr>
			<td>Costo de compra</td>
			<td>
				<input type="text" readonly id="costo" value="<?php echo $costo ?>" data-requiere="true">
			</td>
		</tr>
		<tr>
			<td>Centro de Costos</td>
			<td>
				<input type="text" readonly id="centro_costos" value="">
			</td>
			<td style="padding:0px;" onclick="ventanaBuscarCentroCostos()" title="Buscar centro de costos">
				<img src="images/buscar.png">
			</td>
		</tr>
		<tr>
			<td>Fecha de Vencimiento Garantia</td>
			<td>
				<input type="text" id="fecha_vencimiento_garantia" value="<?php echo $fecha_vencimiento_garantia ?>">
			</td>
		</tr>
		<tr>
			<td>Proveedor</td>
			<td>
				<input type="text" id="proveedor" readonly value="<?php echo $proveedor; ?>" data-requiere="true">
			</td>
			<?php echo $campoProveedor; ?>
		</tr>
		<tr>
			<td>Funcionario Asignado</td>
			<td>
				<input type="text" id="funcionario_asignado" readonly value="<?php echo $funcionario_asignado; ?>">
			</td>
			<td style="padding:0px;" onclick="ventanaBuscarFuncionario()" title="Buscar funcionario">
				<img src="images/buscar.png">
			</td>
		</tr>
		<tr>
			<td>Tenencia</td>
			<td>
				<select id="tenencia" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="0">Propia</option>
					<option value="1">Leasing Financiero</option>
					<option value="2">Leasing Operativo</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Fecha de Vencimiento Tenencia</td>
			<td>
				<input type="text" id="fecha_vencimiento_tenencia" data-requiere="true" value="<?php echo $fecha_vencimiento_tenencia ?>">
			</td>
		</tr>
		<tr>
			<td>Vida util en meses(Colgaap)</td>
			<td>
				<input type="number" id="vida_util" data-requiere="true" value="<?php echo $vida_util ?>">
			</td>
		</tr>
		<tr>
			<td>Vida util en meses(Niif)</td>
			<td>
				<input type="number" id="vida_util_niif" data-requiere="true" value="<?php echo $vida_util_niif ?>">
			</td>
		</tr>
		<tr>
			<td>Sincronizar con SIIP</td>
			<td>
				<select id="sincronizar_siip" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="si">Si</option>
					<option value="no">No</option>
				</select>
			</td>
		</tr>
	</table>
	<div class="separator-body">ATRIBUTOS</div>
	<table class="table-form">
		<tr>
			<td>No. Serial</td>
			<td><input type="text" id="numero_serial" value="<?php echo $numero_serial ?>"></td>
		</tr>
		<tr>
			<td>No. Placa</td>
			<td><input type="text" id="numero_placa" value="<?php echo $numero_placa ?>"></td>
		</tr>
		<tr>
			<td>Marca</td>
			<td><input type="text" id="marca" value="<?php echo $marca ?>"></td>
		</tr>
		<tr>
			<td>Modelo</td>
			<td><input type="text" id="modelo" value="<?php echo $modelo ?>"></td>
		</tr>
		<tr>
			<td>Color</td>
			<td><input type="text" id="color" value="<?php echo $color ?>"></td>
		</tr>
		<tr>
			<td>Chasis</td>
			<td><input type="text" id="chasis" value="<?php echo $chasis ?>"></td>
		</tr>
		<tr>
			<td>Unidad de Medida</td>
			<td>
				<select id="unidad" data-requiere="true">
					<option value="">Seleccione...</option>
					<?php echo $optionUnidades; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Numero de Piezas</td>
			<td><input type="text" id="numero_piezas" value="<?php echo $numero_piezas ?>"></td>
		</tr>
		<tr>
			<td>Longitud</td>
			<td><input type="text" id="longitud" value="<?php echo $longitud ?>"></td>
		</tr>
		<tr>
			<td>Ancho</td>
			<td><input type="text" id="ancho" value="<?php echo $ancho ?>"></td>
		</tr>
		<tr>
			<td>Alto</td>
			<td><input type="text" id="alto" value="<?php echo $alto ?>"></td>
		</tr>
		<tr>
			<td>Volumen</td>
			<td><input type="text" id="volumen" value="<?php echo $volumen ?>"></td>
		</tr>
		<tr>
			<td>Peso</td>
			<td><input type="text" id="peso" value="<?php echo $peso ?>"></td>
		</tr>
		<tr>
			<td>Descripcion 1</td>
			<td><textarea id="descripcion1"><?php echo $descripcion1 ?></textarea></td>
		</tr>
		<tr>
			<td>Descripcion 2</td>
			<td><textarea id="descripcion2"><?php echo $descripcion2 ?></textarea></td>
		</tr>
	</table>
</div>
<div id="loadForm" style="display: none;"></div>
<script>
	<?php echo $acumScript; ?>
	// HABILITAR EDICION CODIGO ACTIVO FIJO
	function habilitarCodigoActivo(value){
		if(value == "si"){
			document.getElementById('codigo_activo').disabled = true;
		}
		else{
			document.getElementById('codigo_activo').disabled = false;
		}
	}

	// BUSCAR LOS GRUPOS DE LA FAMILIA SELECCIONADA
	function buscarGrupo(id_familia,id_grupo){
		Ext.get('grupo').load({
			url     : 'ficha_tecnica/bd.php',
			text    : 'Cargando Grupos...',
			scripts : true,
			nocache : true,
			params  :
			{
				opc        :'busca_grupo',
				id_familia : id_familia,
				id_grupo   : id_grupo,
			}
		});
	}

	// BUSCAR LOS SUB-GRUPOS DEL GRUPO SELECCIONADO
	function buscaSubgrupo(id_grupo,id_subgrupo){
		Ext.get('subgrupo').load({
			url     : 'ficha_tecnica/bd.php',
			text    : 'Cargando Subgrupos...',
			scripts : true,
			nocache : true,
			params  :
			{
				opc         :'buscaSubgrupo',
				id_familia  : document.getElementById('familia').value,
				id_grupo    : id_grupo,
				id_subgrupo : id_subgrupo,
			}
		});
	}

	function ventanaBuscarItem(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_VentanaBuscarItem = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_VentanaBuscarItem',
		    title       : 'Buscar Items',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BusquedaInventarios.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombre_grilla : 'items',
					nombreTabla   : 'items',
					sql           : ' AND opcion_activo_fijo="true" ',
					cargaFuncion  : 'renderItem(id);',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_VentanaBuscarItem.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	// EVENTO AL SELECCIONAR EL ITEM
	function renderItem(id){
		var codigo_item  = document.getElementById('div_items_code_bar_'+id).innerHTML
		,	id_familia  = document.getElementById('clasificacion_'+id).dataset.familia
		,	id_grupo    = document.getElementById('clasificacion_'+id).dataset.grupo
		,	id_subgrupo = document.getElementById('clasificacion_'+id).dataset.subgrupo
		,	codigo      = document.getElementById('div_items_codigo_'+id).innerHTML
		,	nombre      = document.getElementById('div_items_nombre_equipo_'+id).innerHTML


		document.getElementById('familia').value                     = id_familia;
		document.getElementById('code_bar').value                    = codigo_item;
		document.getElementById('codigo_activo').value               = codigo_item;
		document.getElementById('codigo_activo').dataset.codigo_item = codigo_item;
		document.getElementById('codigo_activo').dataset.id          = id;
		document.getElementById('nombre_equipo').value               = nombre;

		buscarGrupo(id_familia,id_grupo);
		buscaSubgrupo(id_grupo,id_subgrupo);

		Win_VentanaBuscarItem.close();
	}

	// LIMPIAR CAMPOS SI SE CAMBIA DE TIPO DE DOCUMENTO
	function limpiarDocIngreso(){
		document.getElementById('documento_referencia').value      = '';
		document.getElementById('documento_referencia').dataset.id = '';
	}

	// BUSCAR LOS DOCUMENTOS DE INGRESO
	function ventanaBuscarDocumentoIngreso(){
		var tipo_doc = document.getElementById('tipo_documento_referencia').value
		,	url_doc  = (tipo_doc=='FC')? "buscarFacturaCompra.php" : "buscarNotaContable.php"
		,	title    = (tipo_doc=='FC')? "Seleccione la Factura de Compra" : "Seleccione la Nota contable"

		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_documentoIngreso = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_documentoIngreso',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'ficha_tecnica/buscarDocumentoIngreso.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					tipo_doc      : tipo_doc,
					cargaFunction : 'renderDocumentoIngreso(id);',
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
		                    handler     : function(){ BloqBtn(this); Win_Ventana_documentoIngreso.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	// EVENTO AL SELECCIONAR EL CENTRO DE COSTOS
	function renderDocumentoIngreso(id){
		var consecutivo = document.getElementById('div_documentoIngreso_consecutivo_'+id).innerHTML

		document.getElementById('documento_referencia').value      = consecutivo;
		document.getElementById('documento_referencia').dataset.id = id;

		Win_Ventana_documentoIngreso.close();
	}

	// VENTANA PARA BUSCAR EL CENTRO DE COSTOS
	function ventanaBuscarCentroCostos(){

		Win_VentanaBuscarCentroCostos = new Ext.Window({
		    width       : 540,
		    height      : 450,
		    id          : 'Win_VentanaBuscarCentroCostos',
		    title       : 'Buscar Centro de Costos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opc           : 'ActivosFijos',
					cargaFunction : 'renderCentroCostos(id);return;',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_VentanaBuscarCentroCostos.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	// EVENTO AL SELECCIONAR EL CENTRO DE COSTOS
	function renderCentroCostos(id){
		var codigo=document.getElementById('div_CentroCostos_codigo_'+id).innerHTML
		,	nombre=document.getElementById('div_CentroCostos_nombre_'+id).innerHTML;

		document.getElementById('centro_costos').dataset.id        = id;
		document.getElementById('centro_costos').dataset.codigo    = codigo;
		document.getElementById('centro_costos').dataset.nombre    = nombre;

		document.getElementById('centro_costos').value   = codigo+' - '+nombre;
		document.getElementById('centro_costos').title   = codigo+' - '+nombre;

		Win_VentanaBuscarCentroCostos.close();
	}

	// VENTANA PARA BUSCAR EL FUNCIONARIO AL CUAL ESTA ASIGNADO
	function ventanaBuscarFuncionario(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_VentanaBuscarProveedor = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_VentanaBuscarProveedor',
		    title       : 'Buscar Empleados',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BusquedaEmpleados.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombre_grilla : 'empleados',
					cargaFuncion  : 'renderFuncionario(id);',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_VentanaBuscarProveedor.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	// EVENTO AL SELECCIONAR EL FUNCIONARIO
	function renderFuncionario(id){
		var documento =document.getElementById('div_empleados_documento_'+id).innerHTML
		,	nombre=document.getElementById('div_empleados_nombre_'+id).innerHTML;

		document.getElementById('funcionario_asignado').dataset.id        = id;
		document.getElementById('funcionario_asignado').dataset.documento = documento;
		document.getElementById('funcionario_asignado').dataset.nombre    = nombre;

		document.getElementById('funcionario_asignado').value   = documento+' - '+nombre;
		document.getElementById('funcionario_asignado').title   = documento+' - '+nombre;

		Win_VentanaBuscarProveedor.close();
	}

	// VENTANA PARA BUSCAR EL PROVEEDOR AL CUAL ESTA ASIGNADO
	function ventanaBuscarProveedor(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_VentanaBuscarFuncionarios = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_VentanaBuscarFuncionarios',
		    title       : 'Buscar Terceros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BusquedaTerceros.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombre_grilla : 'terceros',
					cargaFuncion  : 'renderProveedor(id);',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_VentanaBuscarFuncionarios.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	// EVENTO AL SELECCIONAR EL PROVEEDOR
	function renderProveedor(id){
		var documento = document.getElementById('div_terceros_numero_identificacion_' + id).innerHTML
		,	nombre			= document.getElementById('div_terceros_nombre_comercial_' + id).innerHTML;

		document.getElementById('proveedor').dataset.id        = id;
		document.getElementById('proveedor').dataset.documento = documento;
		document.getElementById('proveedor').dataset.nombre    = nombre;

		document.getElementById('proveedor').value   = documento + ' - ' + nombre;
		document.getElementById('proveedor').title   = documento + ' - ' + nombre;

		Win_VentanaBuscarFuncionarios.close();
	}

	// VALIDAR LOS CAMPOR ANTES DE ENVIARLOS
	function validarCampos(){
		var values         = new Object()
		,	selectFamilia  = document.getElementById('familia')
		,	selectGrupo    = document.getElementById('grupo')
		,	selectSubgrupo = document.getElementById('subgrupo')
		,	selectUnidad   = document.getElementById('unidad')

		values.id_familia                       = selectFamilia.selectedOptions[0].dataset.id;
		values.codigo_familia                   = selectFamilia.selectedOptions[0].dataset.codigo;
		values.familia                          = selectFamilia.selectedOptions[0].dataset.nombre;
		values.id_grupo                         = selectGrupo.selectedOptions[0].dataset.id;
		values.codigo_grupo                     = selectGrupo.selectedOptions[0].dataset.codigo;
		values.grupo                            = selectGrupo.selectedOptions[0].dataset.nombre;
		values.id_subgrupo                      = selectSubgrupo.selectedOptions[0].dataset.id;
		values.codigo_subgrupo                  = selectSubgrupo.selectedOptions[0].dataset.codigo;
		values.subgrupo                         = selectSubgrupo.selectedOptions[0].dataset.nombre;
		values.id_item                          = document.getElementById('codigo_activo').dataset.id;
		values.codigo_item                      = document.getElementById('code_bar').value;
		values.codigo_automatico                = document.getElementById('codigo_automatico').value;
		values.codigo_activo                    = document.getElementById('codigo_activo').value;
		values.nombre_equipo                    = document.getElementById('nombre_equipo').value;
		values.tipo                             = document.getElementById('tipo').value;
		values.fecha_compra                     = document.getElementById('fecha_compra').value;
		values.id_documento_referencia          = document.getElementById('documento_referencia').dataset.id;
		values.documento_referencia             = document.getElementById('tipo_documento_referencia').value;
		values.documento_referencia_consecutivo = document.getElementById('documento_referencia').value;
		values.costo                            = document.getElementById('costo').value;
		values.id_centro_costos                 = document.getElementById('centro_costos').dataset.id;
		values.codigo_centro_costos             = document.getElementById('centro_costos').dataset.codigo;
		values.centro_costos                    = document.getElementById('centro_costos').dataset.nombre;
		values.fecha_vencimiento_garantia       = document.getElementById('fecha_vencimiento_garantia').value;
		values.id_proveedor                     = document.getElementById('proveedor').dataset.id,
		values.nit_proveedor                    = document.getElementById('proveedor').dataset.documento,
		values.proveedor                        = document.getElementById('proveedor').dataset.nombre,
		values.id_funcionario_asignado          = document.getElementById('funcionario_asignado').dataset.id;
		values.documento_funcionario_asignado   = document.getElementById('funcionario_asignado').dataset.documento;
		values.funcionario_asignado             = document.getElementById('funcionario_asignado').dataset.nombre;
		values.numero_serial                    = document.getElementById('numero_serial').value;
		values.numero_placa                     = document.getElementById('numero_placa').value;
		values.marca                            = document.getElementById('marca').value;
		values.modelo                           = document.getElementById('modelo').value;
		values.color                            = document.getElementById('color').value;
		values.chasis                           = document.getElementById('chasis').value;
		values.id_unidad                        = selectUnidad.value;
		values.unidad                           = selectUnidad.options[selectUnidad.selectedIndex].text;
		values.numero_piezas                    = document.getElementById('numero_piezas').value;
		values.longitud                         = document.getElementById('longitud').value;
		values.ancho                            = document.getElementById('ancho').value;
		values.alto                             = document.getElementById('alto').value;
		values.volumen                          = document.getElementById('volumen').value;
		values.peso                             = document.getElementById('peso').value;
		values.descripcion1                     = document.getElementById('descripcion1').value;
		values.descripcion2                     = document.getElementById('descripcion2').value;
		values.tenencia                         = document.getElementById('tenencia').value;
		values.fecha_vencimiento_tenencia       = document.getElementById('fecha_vencimiento_tenencia').value;
		values.vida_util                        = document.getElementById('vida_util').value;
		values.vida_util_niif                   = document.getElementById('vida_util_niif').value;
		values.sincronizar_siip                 = document.getElementById('sincronizar_siip').value;

		// VALIDAR CAMPOS OBLIGATORIOS
		if (values.id_familia == "" || typeof(values.id_familia) == "undefined") 		{ alert("Aviso\nCampo Familia obligatorio"); return; }
		if (values.id_grupo == "" || typeof(values.id_grupo) == "undefined") 	  		{ alert("Aviso\nCampo Grupo obligatorio"); return; }
		if (values.id_subgrupo == "" || typeof(values.id_subgrupo) == "undefined") 	{ alert("Aviso\nCampo Subgrupo obligatorio"); return; }
		if (values.id_item == "" || typeof(values.id_item) == "undefined")   				{ alert("Aviso\nDebe Buscar y seleccionar un item"); return; }
		if (values.codigo_activo == "") 																						{ alert("Aviso\nCampo Codigo Activo obligatorio"); return; }
		if (values.nombre_equipo == "") 																						{ alert("Aviso\nCampo Nombre obligatorio"); return; }
		if (values.tipo == "") 																											{ alert("Aviso\nCampo Tipo obligatorio"); return; }
		if (values.fecha_compra == "") 																							{ alert("Aviso\nCampo Fecha compra obligatorio"); return; }
		if (values.id_documento_referencia == "") 																	{ alert("Aviso\nCampo Documento Referencia obligatorio"); return; }
		if (values.costo == "") 																										{ alert("Aviso\nCampo Costo obligatorio"); return; }
		if (values.id_proveedor == "") 																							{ alert("Aviso\nCampo Proveedor obligatorio"); return; }
		if (values.tenencia == "")  																								{ alert("Aviso\nCampo Tenencia obligatorio"); return; }
		if (values.fecha_vencimiento_tenencia == "")  															{ alert("Aviso\nCampo Fecha Vencimiento Tenencia obligatorio"); return; }
		if (values.sincronizar_siip == "")  																				{ alert("Aviso\nCampo Sincronizar SIIP obligatorio"); return; }
		if (values.id_unidad == "") 																								{ alert("Aviso\nCampo Unidad obligatorio"); return; }
		if (values.vida_util == "") 																								{ alert("Aviso\nCampo Vida Util(Colgaap) obligatorio"); return; }
		if (values.vida_util_niif == "") 							 		   												{ alert("Aviso\nCampo Vida Util(Niif) obligatorio"); return; }

		guardarActualizarActivo(values);
	}

	// GUARDAR UN NUE4VO ACTIVO O ACTUALIZARLO
	function guardarActualizarActivo(values){
		MyLoading2('on');
		var opc = (<?php echo $id_activo ?> > 0)? "actualizarActivo" : "guardarActivo" ;
		Ext.get('loadForm').load({
			url     : 'ficha_tecnica/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                                : opc,
				id_activo                          : '<?php echo $id_activo ?>',
				id_familia                         : values.id_familia,
				codigo_familia                     : values.codigo_familia,
				familia                            : values.familia,
				id_grupo                           : values.id_grupo,
				codigo_grupo                       : values.codigo_grupo,
				grupo                              : values.grupo,
				id_subgrupo                        : values.id_subgrupo,
				codigo_subgrupo                    : values.codigo_subgrupo,
				subgrupo                           : values.subgrupo,
				id_item                            : values.id_item,
				codigo_item                        : values.codigo_item,
				codigo_automatico                  : values.codigo_automatico,
				codigo_activo                      : values.codigo_activo,
				nombre_equipo                      : values.nombre_equipo,
				tipo                               : values.tipo,
				fecha_compra                       : values.fecha_compra,
				id_documento_referencia            : values.id_documento_referencia,
				documento_referencia               : values.documento_referencia,
				documento_referencia_consecutivo   : values.documento_referencia_consecutivo,
				id_documento_referencia_inventario : '<?php echo $id_documento_referencia_inventario; ?>',
				costo                              : values.costo,
				id_centro_costos                   : values.id_centro_costos,
				codigo_centro_costos               : values.codigo_centro_costos,
				centro_costos                      : values.centro_costos,
				fecha_vencimiento_garantia         : values.fecha_vencimiento_garantia,
				id_proveedor                       : values.id_proveedor,
				nit_proveedor                      : values.nit_proveedor,
				proveedor                          : values.proveedor,
				id_funcionario_asignado            : values.id_funcionario_asignado,
				documento_funcionario_asignado     : values.documento_funcionario_asignado,
				funcionario_asignado               : values.funcionario_asignado,
				numero_serial                      : values.numero_serial,
				numero_placa                       : values.numero_placa,
				marca                              : values.marca,
				modelo                             : values.modelo,
				color                              : values.color,
				chasis                             : values.chasis,
				id_unidad                          : values.id_unidad,
				unidad                             : values.unidad,
				numero_piezas                      : values.numero_piezas,
				longitud                           : values.longitud,
				ancho                              : values.ancho,
				alto                               : values.alto,
				volumen                            : values.volumen,
				peso                               : values.peso,
				descripcion1                       : values.descripcion1,
				descripcion2                       : values.descripcion2,
				tenencia                           : values.tenencia,
				fecha_vencimiento_tenencia         : values.fecha_vencimiento_tenencia,
				vida_util                          : values.vida_util,
				vida_util_niif                     : values.vida_util_niif,
				sincronizar_siip                   : values.sincronizar_siip,
				id_sucursal                        : '<?php echo $id_sucursal; ?>',
				id_bodega                          : '<?php echo $id_bodega; ?>',
			}
		});
	}
</script>
