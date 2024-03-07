<?php
	if($id_activo > 0){
		$acumScript .=  "document.getElementById('depreciable').value                = '$depreciable';
										document.getElementById('metodo_depreciacion_colgaap').value = '$metodo_depreciacion_colgaap';
										document.getElementById('depreciable_niif').value            = '$depreciable_niif';
										document.getElementById('metodo_depreciacion_niif').value    = '$metodo_depreciacion_niif';
										document.getElementById('deteriorable').value                = '$deteriorable';";
	}

	if(user_permisos(237,'false') == 'true'){
		$acumScript .= "new Ext.form.DateField({
											format 			:	'Y-m-d',
											width				:	197,
											allowBlank  :	false,
											showToday		:	false,
											applyTo			:	'fecha_inicio_depreciacion',
											editable		:	false
										});

										new Ext.form.DateField({
											format 			:	'Y-m-d',
											width				:	197,
											allowBlank	:	false,
											showToday		:	false,
											applyTo			:	'fecha_inicio_depreciacion_niif',
											editable		:	false
										});";
	}

	// CONSULTAR LAS CUENTAS DEL ACTIVO
	$sql = "SELECT
						descripcion,
						estado,
						id_cuenta,
						cuenta,
						descripcion_cuenta,
						contabilidad
					FROM
						activos_fijos_cuentas
					WHERE
						activo = 1
					AND
						id_activo = $id_activo
					AND
						id_empresa = $id_empresa";
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)){
		$descripcion  = $row['descripcion'];
		$estado       = $row['estado'];
		$contabilidad = $row['contabilidad'];

		$arrayCuentas[$contabilidad][$estado][$descripcion] = array(
																	'id_cuenta'          => $row['id_cuenta'],
																	'cuenta'             => $row['cuenta'],
																	'descripcion'        => " - ".$row['descripcion_cuenta'],
																	'descripcion_cuenta' => $row['descripcion_cuenta'],
																	);
		$acumScript .= "
						document.getElementById('".$descripcion."_".$estado."_$contabilidad').dataset.id_cuenta   ='$row[id_cuenta]';
						document.getElementById('".$descripcion."_".$estado."_$contabilidad').dataset.cuenta      ='$row[cuenta]';
						document.getElementById('".$descripcion."_".$estado."_$contabilidad').dataset.descripcion ='$row[descripcion_cuenta]';
						";
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
		<button class="button" data-value="save" onclick="validarCamposContabilidad()">Guadar</button>
		<button class="button" data-value="default_acount" onclick="cargarCuentasDefaultGrupos()">Cargar Cuentas Default Grupos</button>
	</div>
	<div class="separator-body">DEPRECIACIÓN COLGAAP</div>
	<table class="table-form">
		<tr>
			<td>Depreciable</td>
			<td>
				<select id="depreciable" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Fecha Inicio depreciacion</td>
			<td><input type="text" id="fecha_inicio_depreciacion" value="<?php echo $fecha_inicio_depreciacion ?>" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Valor Salvamento</td>
			<td><input type="text" id="valor_salvamento" value="<?php echo $valor_salvamento ?>" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Metodo Depreciacion</td>
			<td>
				<select id="metodo_depreciacion_colgaap" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="linea_recta">Linea Recta</option>
					<option value="reduccion_saldos">Reduccion de Saldos</option>
					<option value="suma_digitos_year">Suma de los digitos del año</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Cuenta Debito (Gasto)</td>
			<td><input type="text" readonly="readonly" id="depreciacion_debito_colgaap" data-requiere="true" value="<?php echo $arrayCuentas['colgaap']['debito']['depreciacion']['cuenta'].$arrayCuentas['colgaap']['debito']['depreciacion']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('depreciacion_debito_colgaap')" title="Buscar cuenta Debito"><img src="images/buscar.png"></td>
		</tr>
		<tr>
			<td>Cuenta Credito (Activo)</td>
			<td><input type="text" readonly="readonly" id="depreciacion_credito_colgaap" data-requiere="true" value="<?php echo $arrayCuentas['colgaap']['credito']['depreciacion']['cuenta'].$arrayCuentas['colgaap']['credito']['depreciacion']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('depreciacion_credito_colgaap')" title="Buscar cuenta Gasto"><img src="images/buscar.png"></td>
		</tr>
	</table>
	<div class="separator-body">DEPRECIACIÓN NIIF</div>
	<table class="table-form">
		<tr>
			<td>Depreciable</td>
			<td>
				<select id="depreciable_niif" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Fecha Inicio depreciacion</td>
			<td><input type="text" id="fecha_inicio_depreciacion_niif" value="<?php echo $fecha_inicio_depreciacion_niif ?>" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Valor Salvamento</td>
			<td><input type="text" id="valor_salvamento_niif" value="<?php echo $valor_salvamento_niif ?>" data-requiere="true"></td>
		</tr>
		<tr>
			<td>Metodo Depreciacion</td>
			<td>
				<select id="metodo_depreciacion_niif" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="linea_recta">Linea Recta</option>
					<option value="reduccion_saldos">Reduccion de Saldos</option>
					<option value="suma_digitos_year">Suma de los digitos del año</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Cuenta Debito (Gasto)</td>
			<td><input type="text" readonly="readonly" id="depreciacion_debito_niif" data-requiere="true" value="<?php echo $arrayCuentas['niif']['debito']['depreciacion']['cuenta'].$arrayCuentas['niif']['debito']['depreciacion']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('depreciacion_debito_niif')" title="Buscar cuenta Debito"><img src="images/buscar.png"></td>
		</tr>
		<tr>
			<td>Cuenta Credito (Activo)</td>
			<td><input type="text" readonly="readonly" id="depreciacion_credito_niif" data-requiere="true" value="<?php echo $arrayCuentas['niif']['credito']['depreciacion']['cuenta'].$arrayCuentas['niif']['credito']['depreciacion']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('depreciacion_credito_niif')" title="Buscar cuenta Gasto"><img src="images/buscar.png"></td>
		</tr>
	</table>
	<div class="separator-body">DETERIORO NIIF</div>
	<table class="table-form">
		<tr>
			<td>Deteriorable</td>
			<td>
				<select id="deteriorable" data-requiere="true">
					<option value="">Seleccione...</option>
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Cuenta Debito</td>
			<td><input type="text" readonly="readonly" id="deterioro_debito_niif" data-requiere="true" value="<?php echo $arrayCuentas['niif']['debito']['deterioro']['cuenta'].$arrayCuentas['niif']['debito']['deterioro']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('deterioro_debito_niif')" title="Buscar cuenta Debito"><img src="images/buscar.png"></td>
		</tr>
		<tr>
			<td>Cuenta Credito</td>
			<td><input type="text" readonly="readonly" id="deterioro_credito_niif" data-requiere="true" value="<?php echo $arrayCuentas['niif']['credito']['deterioro']['cuenta'].$arrayCuentas['niif']['credito']['deterioro']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('deterioro_credito_niif')" title="Buscar cuenta Credito"><img src="images/buscar.png"></td>
		</tr>
	</table>
	<div class="separator-body">DAR DE BAJA</div>
	<table class="table-form">
		<tr>
			<td>Cuenta Colgaap</td>
			<td style="padding-left:65px;"><input type="text" readonly="readonly" id="baja_debito_colgaap" data-requiere="true" value="<?php echo $arrayCuentas['colgaap']['debito']['baja']['cuenta'].$arrayCuentas['colgaap']['debito']['baja']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('baja_debito_colgaap')" title="Buscar Cuenta Debito Colgaap"><img src="images/buscar.png"></td>
		</tr>
		<tr>
			<td>Cuenta Niif</td>
			<td style="padding-left:65px;"><input type="text" readonly="readonly" id="baja_debito_niif" data-requiere="true" value="<?php echo $arrayCuentas['niif']['debito']['baja']['cuenta'].$arrayCuentas['niif']['debito']['baja']['descripcion'] ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCuentas('baja_debito_niif')" title="Buscar Cuenta Debito Niif"><img src="images/buscar.png"></td>
		</tr>
	</table>
</div>
<script>
	<?php echo $acumScript; ?>

	//CARGAR CUENTAS POR DEFECTO
	function cargarCuentasDefaultGrupos(){
		if (!confirm("Realmente desea cargar las cuentas por defecto de los grupos?")) {return;}

		var id_grupo = document.getElementById('grupo').value;

		if (id_grupo=='') { alert("Aviso\nEl activo no tiene grupo seleccionado!"); return; }

		MyLoading2('on');

		Ext.get('loadForm').load({
			url     : 'ficha_tecnica/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc       : 'cargarCuentasDefaultGrupos',
				id_activo : '<?php echo $id_activo ?>',
				id_grupo  : id_grupo,
			}
		});
	}

	//VENTANA PARA BUSCAR EL CENTRO DE COSTOS
	function ventanaBuscarCuentas(idInput){
		var contabilidad = idInput.split("_")[2];
		Win_VentanaBuscarCuentas = new Ext.Window({
		    width       : 678,
		    height      : 520,
		    id          : 'Win_VentanaBuscarCuentas',
		    title       : 'Buscar Cuentas',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombreGrilla  : "puc_"+contabilidad,
					opc           : contabilidad,
					cargaFuncion : 'renderCuentas(id,"'+idInput+'");',
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
		                    handler     : function(){ Win_VentanaBuscarCuentas.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	//EVENTO AL SELECCIONAR EL CENTRO DE COSTOS
	function renderCuentas(id,idInput){
		var contabilidad = idInput.split("_")[2]
		,	cuenta       = document.getElementById('div_puc_'+contabilidad+'_cuenta_'+id).innerHTML
		,	nombre       = document.getElementById('div_puc_'+contabilidad+'_descripcion_'+id).innerHTML;

		document.getElementById(idInput).dataset.id_cuenta   = id;
		document.getElementById(idInput).dataset.cuenta      = cuenta;
		document.getElementById(idInput).dataset.descripcion = nombre;

		document.getElementById(idInput).value               = cuenta+' - '+nombre;
		document.getElementById(idInput).title               = cuenta+' - '+nombre;

		Win_VentanaBuscarCuentas.close();
	}

	//VALIDAR LOS CAMPOS ANTES DE ENVIARLOS
	function validarCamposContabilidad(){
		var values                                  = new Object()
		values.cuentas                              = new Object()
		values.cuentas.colgaap                      = new Object()
		values.cuentas.colgaap.debito               = new Object()
		values.cuentas.colgaap.credito              = new Object()
		values.cuentas.colgaap.debito.depreciacion  = new Object()
		values.cuentas.colgaap.credito.depreciacion = new Object()
		values.cuentas.colgaap.debito.baja 				  = new Object()
		values.cuentas.niif                         = new Object()
		values.cuentas.niif.debito                  = new Object()
		values.cuentas.niif.credito                 = new Object()
		values.cuentas.niif.debito.depreciacion     = new Object()
		values.cuentas.niif.credito.depreciacion    = new Object()
		values.cuentas.niif.debito.deterioro        = new Object()
		values.cuentas.niif.credito.deterioro       = new Object()
		values.cuentas.niif.debito.baja 		        = new Object()

		values.depreciable                                             = document.getElementById('depreciable').value;
		values.fecha_inicio_depreciacion                               = document.getElementById('fecha_inicio_depreciacion').value;
		values.valor_salvamento                                        = document.getElementById('valor_salvamento').value;
		values.metodo_depreciacion_colgaap                             = document.getElementById('metodo_depreciacion_colgaap').value;

		// values.depreciacion_debito_colgaap                          = document.getElementById('depreciacion_debito_colgaap').value;
		values.cuentas.colgaap.debito.depreciacion.id_cuenta           = document.getElementById('depreciacion_debito_colgaap').dataset.id_cuenta;
		values.cuentas.colgaap.debito.depreciacion.cuenta              = document.getElementById('depreciacion_debito_colgaap').dataset.cuenta;
		values.cuentas.colgaap.debito.depreciacion.descripcion_cuenta  = document.getElementById('depreciacion_debito_colgaap').dataset.descripcion;

		// values.depreciacion_credito_colgaap                         = document.getElementById('depreciacion_credito_colgaap').value;
		values.cuentas.colgaap.credito.depreciacion.id_cuenta          = document.getElementById('depreciacion_credito_colgaap').dataset.id_cuenta;
		values.cuentas.colgaap.credito.depreciacion.cuenta             = document.getElementById('depreciacion_credito_colgaap').dataset.cuenta;
		values.cuentas.colgaap.credito.depreciacion.descripcion_cuenta = document.getElementById('depreciacion_credito_colgaap').dataset.descripcion;

		values.cuentas.colgaap.debito.baja.id_cuenta           				 = document.getElementById('baja_debito_colgaap').dataset.id_cuenta;
		values.cuentas.colgaap.debito.baja.cuenta              				 = document.getElementById('baja_debito_colgaap').dataset.cuenta;
		values.cuentas.colgaap.debito.baja.descripcion_cuenta  				 = document.getElementById('baja_debito_colgaap').dataset.descripcion;

		values.depreciable_niif                                        = document.getElementById('depreciable_niif').value;
		values.fecha_inicio_depreciacion_niif                          = document.getElementById('fecha_inicio_depreciacion_niif').value;
		values.valor_salvamento_niif                                   = document.getElementById('valor_salvamento_niif').value;
		values.metodo_depreciacion_niif                                = document.getElementById('metodo_depreciacion_niif').value;

		// values.depreciacion_debito_niif                             = document.getElementById('depreciacion_debito_niif').value;
		values.cuentas.niif.debito.depreciacion.id_cuenta              = document.getElementById('depreciacion_debito_niif').dataset.id_cuenta;
		values.cuentas.niif.debito.depreciacion.cuenta                 = document.getElementById('depreciacion_debito_niif').dataset.cuenta;
		values.cuentas.niif.debito.depreciacion.descripcion_cuenta     = document.getElementById('depreciacion_debito_niif').dataset.descripcion;

		// values.depreciacion_credito_niif                            = document.getElementById('depreciacion_credito_niif').value;
		values.cuentas.niif.credito.depreciacion.id_cuenta             = document.getElementById('depreciacion_credito_niif').dataset.id_cuenta;
		values.cuentas.niif.credito.depreciacion.cuenta                = document.getElementById('depreciacion_credito_niif').dataset.cuenta;
		values.cuentas.niif.credito.depreciacion.descripcion_cuenta    = document.getElementById('depreciacion_credito_niif').dataset.descripcion;

		values.deteriorable                                            = document.getElementById('deteriorable').value;

		// values.deterioro_debito_niif                                = document.getElementById('deterioro_debito_niif').value;
		values.cuentas.niif.debito.deterioro.id_cuenta                 = document.getElementById('deterioro_debito_niif').dataset.id_cuenta;
		values.cuentas.niif.debito.deterioro.cuenta                    = document.getElementById('deterioro_debito_niif').dataset.cuenta;
		values.cuentas.niif.debito.deterioro.descripcion_cuenta        = document.getElementById('deterioro_debito_niif').dataset.descripcion;

		// values.deterioro_credito_niif                               = document.getElementById('deterioro_credito_niif').value;
		values.cuentas.niif.credito.deterioro.id_cuenta                = document.getElementById('deterioro_credito_niif').dataset.id_cuenta;
		values.cuentas.niif.credito.deterioro.cuenta                   = document.getElementById('deterioro_credito_niif').dataset.cuenta;
		values.cuentas.niif.credito.deterioro.descripcion_cuenta       = document.getElementById('deterioro_credito_niif').dataset.descripcion;

		values.cuentas.niif.debito.baja.id_cuenta           					 = document.getElementById('baja_debito_niif').dataset.id_cuenta;
		values.cuentas.niif.debito.baja.cuenta              					 = document.getElementById('baja_debito_niif').dataset.cuenta;
		values.cuentas.niif.debito.baja.descripcion_cuenta  					 = document.getElementById('baja_debito_niif').dataset.descripcion;

		if (values.depreciable == "") 																							{ alert("Aviso\nCampo Depreciable (colgaap) obligatorio"); return; }
		if (values.fecha_inicio_depreciacion == "") 																{ alert("Aviso\nCampo Fecha inicio depreciacion (colgaap) obligatorio"); return; }
		if (values.valor_salvamento == "" ) 																				{ alert("Aviso\nCampo Valor salvamento (colgaap) obligatorio"); return; }
		if (values.metodo_depreciacion_colgaap == "" ) 															{ alert("Aviso\nCampo metodo depreciacion (colgaap) obligatorio"); return; }
		if (values.cuentas.colgaap.debito.depreciacion.id_cuenta == undefined ) 		{ alert("Aviso\nCampo Cuenta depreciacion debito (colgaap) obligatorio"); return; }
		if (values.cuentas.colgaap.credito.depreciacion.id_cuenta == undefined ) 		{ alert("Aviso\nCampo Cuenta depreciacion credito (colgaap) obligatorio"); return; }
		if (values.depreciable_niif == "") 																					{ alert("Aviso\nCampo Depreciable (niif) obligatorio"); return; }
		if (values.fecha_inicio_depreciacion_niif == "" ) 													{ alert("Aviso\nCampo Fecha inicio depreciacion (niif) obligatorio"); return; }
		if (values.valor_salvamento_niif == "" ) 																		{ alert("Aviso\nCampo Valor salvamento (niif) obligatorio"); return; }
		if (values.metodo_depreciacion_niif == "" ) 																{ alert("Aviso\nCampo metodo depreciacion (niif) obligatorio"); return; }
		if (values.cuentas.niif.debito.depreciacion.id_cuenta == undefined ) 				{ alert("Aviso\nCampo Cuenta depreciacion debito (niif) obligatorio"); return; }
		if (values.cuentas.niif.credito.depreciacion.id_cuenta == undefined ) 			{ alert("Aviso\nCampo Cuenta depreciacion credito (niif) obligatorio"); return; }
		if (values.cuentas.niif.debito.deterioro.id_cuenta == undefined ) 					{ alert("Aviso\nCampo Cuenta deterioro debito (niif) obligatorio"); return; }
		if (values.cuentas.niif.credito.deterioro.id_cuenta == undefined ) 					{ alert("Aviso\nCampo Cuenta deterioro credito (niif) obligatorio"); return; }
		if (values.cuentas.colgaap.debito.baja.id_cuenta == undefined )  						{ alert("Aviso\nCampo Cuenta dar de baja debito (colgaap) obligatorio"); return; }
		if (values.cuentas.niif.debito.baja.id_cuenta == undefined )  							{ alert("Aviso\nCampo Cuenta dar de baja debito (niif) obligatorio"); return; }

		// console.log(JSON.stringify(values.cuentas));
		guardaActualizaContabilidad(values);
	}

	//GUARDAR O ACTUALIZAR INFORMACION CONTABLE
	function guardaActualizaContabilidad(values){
		MyLoading2('on');
		Ext.get('loadForm').load({
			url     : 'ficha_tecnica/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                            : 'guardaActualizaContabilidad',
				id_activo                      : '<?php echo $id_activo ?>',
				depreciable                    : values.depreciable,
				fecha_inicio_depreciacion      : values.fecha_inicio_depreciacion,
				valor_salvamento               : values.valor_salvamento,
				metodo_depreciacion_colgaap    : values.metodo_depreciacion_colgaap,
				depreciable_niif               : values.depreciable_niif,
				fecha_inicio_depreciacion_niif : values.fecha_inicio_depreciacion_niif,
				valor_salvamento_niif          : values.valor_salvamento_niif,
				metodo_depreciacion_niif       : values.metodo_depreciacion_niif,
				deteriorable                   : values.deteriorable,
				cuentas                        : JSON.stringify(values.cuentas),
				id_sucursal                    : '<?php echo $id_sucursal; ?>',
				id_bodega                      : '<?php echo $id_bodega; ?>',
			}
		});
	}
</script>
