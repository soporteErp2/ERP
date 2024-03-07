<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];

	// CONSULTAR LA INFORMACION DEL LIBRO DE VACACIONES
	$sql="SELECT
			id,
			fecha_inicio_contrato,
			fecha_inicio_periodo_vacaciones,
			fecha_final_periodo_vacaciones,
			fecha_inicio_vacaciones_disfrutadas,
			fecha_fin_vacaciones_disfrutadas,
			id_concepto_vacaciones,
			concepto_vacaciones,
			tipo_base,
			dias_vacaciones_disfrutadas,
			base,
			valor_vacaciones_disfrutadas,
			fecha_inicio_labores,
			dias_vacaciones_compensadas,
			valor_vacaciones_compensadas,
			tipo_pago_vacaciones
		FROM
			nomina_vacaciones_empleados
		WHERE
			activo          = 1
			AND id_empresa  = $id_empresa
			AND id_planilla = $id_planilla
			AND id_empleado = $id_empleado";
	$query=$mysql->query($sql,$mysql->link);

	$id_libro                            = $mysql->result($query,0,'id');
	$fecha_inicio_contrato               = $mysql->result($query,0,'fecha_inicio_contrato');
	$fecha_inicio_periodo_vacaciones     = $mysql->result($query,0,'fecha_inicio_periodo_vacaciones');
	$fecha_final_periodo_vacaciones      = $mysql->result($query,0,'fecha_final_periodo_vacaciones');
	$fecha_inicio_vacaciones_disfrutadas = $mysql->result($query,0,'fecha_inicio_vacaciones_disfrutadas');
	$fecha_final_vacaciones_disfrutadas  = $mysql->result($query,0,'fecha_fin_vacaciones_disfrutadas');
	$id_concepto_vacaciones              = $mysql->result($query,0,'id_concepto_vacaciones');
	$concepto_vacaciones                 = $mysql->result($query,0,'concepto_vacaciones');
	$tipo_base                           = $mysql->result($query,0,'tipo_base');
	$dias_vacaciones_disfrutadas         = $mysql->result($query,0,'dias_vacaciones_disfrutadas');
	$valor_base_vacaciones               = $mysql->result($query,0,'base');
	$valor_vacaciones_disfrutadas        = $mysql->result($query,0,'valor_vacaciones_disfrutadas');
	$fecha_inicio_labores                = $mysql->result($query,0,'fecha_inicio_labores');
	$dias_vacaciones_compensadas         = $mysql->result($query,0,'dias_vacaciones_compensadas');
	$valor_vacaciones_compensadas        = $mysql->result($query,0,'valor_vacaciones_compensadas');
	$tipo_pago_vacaciones                = $mysql->result($query,0,'tipo_pago_vacaciones');

	// CONSULTAR LA INFORMACION DE LA PLANILLA DE NOMINA
	$sql="SELECT fecha_inicio,fecha_final,estado FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
	$query=$mysql->query($sql,$mysql->link);
	$fecha_inicio    = $mysql->result($query,0,'fecha_inicio');
	$fecha_final     = $mysql->result($query,0,'fecha_final');
	$estado_planilla = $mysql->result($query,0,'estado');

	// SI NO TIENE EL LIBRO DE VACACIONES AUN CREADO EN ESTA PLANILLA
	if ($id_libro==0 || $id_libro=='') {

		// CONSULTAR LOS DATOS DEL CONTRATO DEL EMPLEADO
		$sql="SELECT
					fecha_inicio_contrato,
					fecha_fin_contrato,
					(SELECT dias FROM nomina_tipo_contrato WHERE id=id_tipo_contrato) AS dias_tipo_contrato
				FROM empleados_contratos
				WHERE activo=1
				AND id_empresa=$id_empresa
				AND id_empleado=$id_empleado
				AND id=$id_contrato";
		$query=$mysql->query($sql,$mysql->link);

		$fecha_inicio_contrato = $mysql->result($query,0,'fecha_inicio_contrato');
		$fecha_fin_contrato    = $mysql->result($query,0,'fecha_fin_contrato');
		$dias_tipo_contrato    = $mysql->result($query,0,'dias_tipo_contrato');

		$fecha_final_contrato  = ($dias_tipo_contrato==0)? date("Y-m-d", strtotime("$fecha_inicio_contrato +12 month")) : $fecha_fin_contrato;

		// CONSULTAR EL CONCEPTO DE VACACIONES DE LA PLANILLA
		$sql="SELECT id,id_concepto,concepto FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato AND codigo_concepto='VC' ";
		$query=$mysql->query($sql,$mysql->link);
		$id_concepto_vacaciones = $mysql->result($query,0,'id_concepto');
		$concepto_vacaciones    = $mysql->result($query,0,'concepto');

		// CONSULTAR SI TIENE OTROS LIBROS DE VACACIONES EN OTRAS PLANILLAS
		$sql="SELECT
					COUNT(id) AS cont_vacaciones,
					fecha_final_periodo_vacaciones
				FROM nomina_vacaciones_empleados
				WHERE activo=1
				AND id_empresa=$id_empresa
				AND id_planilla<>$id_planilla
				AND id_empleado = $id_empleado
				AND id_contrato=$id_contrato
				AND tipo_pago_vacaciones='completas'
				GROUP BY fecha_final_periodo_vacaciones
				ORDER BY fecha_final_periodo_vacaciones DESC
				";
		$query=$mysql->query($sql,$mysql->link);
		$cont_vacaciones   = $mysql->result($query,0,'cont_vacaciones');
		$fecha_final_libro = $mysql->result($query,0,'fecha_final_periodo_vacaciones');

		// SI YA SE CERRO UN PERIODO DE VACACIONES
		if ($cont_vacaciones>0) {
			// $fecha_inicio_periodo_vacaciones = $fecha_final_libro;
			// $fecha_final_periodo_vacaciones  = date("Y-m-d", strtotime("$fecha_inicio_periodo_vacaciones +12 month"));

			$fecha_inicio_periodo_vacaciones = $fecha_inicio;
			$fecha_final_periodo_vacaciones  = $fecha_final;
		}
		// SI AUN ESE PERIODO TIENE VACACIONES GENERAR
		else{
			$fecha_inicio_periodo_vacaciones = $fecha_inicio_contrato;
			$fecha_final_periodo_vacaciones  = $fecha_final_contrato;
		}

	}
	else{
		$acumscript .= "
						document.getElementById('tipo_pago_vacaciones').value = '$tipo_pago_vacaciones' ;
						document.getElementById('base_vacaciones').value      = '$tipo_base' ;
						";
	}



	

	if ($estado_planilla==0) {

		$btn_load_concepto = "<img onclick='cargarVacaciones($id_empleado,$id_contrato)' src='img/load_vacations.png' title='Cargar Vacaciones de ese periodo'>";
		$btn_buscar_concepto = "<img onclick='ventanaBuscarConceptosVacaciones($id_empleado,$id_contrato)' src='img/buscar2.png' title='Buscar el concepto de vacaciones'>";

		$acumscript .= '
						new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 190,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_inicio_vacaciones_disfrutadas",
					    editable   : false,                 //EDITABLE
					    value : "'.$fecha_inicio_vacaciones_disfrutadas.'",
					    listeners  : { select: function() { restaFechas()  } }
					});
					new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 190,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_final_vacaciones_disfrutadas",
					    editable   : false,                 //EDITABLE
					    value : "'.$fecha_final_vacaciones_disfrutadas.'",
					    listeners  : { select: function() { restaFechas()  } }
					});
					new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 190,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_inicio_labores",
					    editable   : false,                 //EDITABLE
					    value : "'.$fecha_inicio_labores.'",
					    listeners  : { select: function() {   } }
					});
						';
	}
	else{
		$acumscript .= "
						document.getElementById('tipo_pago_vacaciones').disabled                = true;
						document.getElementById('base_vacaciones').disabled                     = true;
						document.getElementById('fecha_inicio_vacaciones_disfrutadas').readOnly = true;
						document.getElementById('fecha_final_vacaciones_disfrutadas').readOnly  = true;
						document.getElementById('fecha_inicio_labores').readOnly                = true;
						document.getElementById('dias_vacaciones_compensadas').readOnly         = true;
						";
	}


?>

<style>
	img{
		cursor: pointer;
	}
</style>
<div class="content" style="overflow: auto;float: left;height: 435px;">
	<table class="table-form" style="width:90%;">
		<tr class="thead" style="background-color: #A2A2A2;">
			<td colspan="3">INFORMACIÓN DE LAS VACACIONES</td>
		</tr>
		<tr>
			<td>Fecha de Ingreso</td>
			<td><input type="text" value="<?php echo $fecha_inicio_contrato; ?>" style="width:190px;" readonly id="fecha_inicio_contrato"  ></td>
		</tr>
		<tr>
			<td>Periodo de Vacaciones</td>
			<td>
				<input type="text" value="<?php echo $fecha_inicio_periodo_vacaciones; ?>" style="width:89.5px;" readonly  id="fecha_inicio_periodo_vacaciones"  > -
				<input type="text" value="<?php echo $fecha_final_periodo_vacaciones; ?>" style="width:89.5px;"  readonly id="fecha_final_periodo_vacaciones"  >
				<td style="padding:0px;"><?php echo $btn_load_concepto; ?></td>
			</td>
		</tr>
		<tr>
			<td>Concepto de Vacaciones</td>
			<td><input type="text" value="<?php echo $concepto_vacaciones; ?>" style="width:190px;" readonly data-requiere="true" id="concepto_vacaciones" data-id="<?php echo $id_concepto_vacaciones ?>"  ></td>
			<td style="padding:0px;"><?php echo $btn_buscar_concepto; ?></td>
		</tr>
		<tr>
			<td>Tipo de pago vacaciones</td>
			<td>
				<select style="width:190px;" id="tipo_pago_vacaciones" >
					<option value="parciales" >Vacaciones Parciales</option>
					<option value="completas" >Vacaciones Completas</option>
				</select>
			</td>
			<td style="padding:0px;"><img onclick='ventanaAyuda()' src='img/help.png' title='Informacion de este campo'></td>
		</tr>
		<tr>
			<td>Base de Vacaciones</td>
			<td>
				<select class="myInput" style="width:190px;" id="base_vacaciones" onchange="calcularBaseVacaciones()">
					<option value="" data-requiere="true" >Seleccione...</option>
					<option value="ultimo_salario" >Ultimo salario (Salario del contrato)</option>
					<option value="promedio_anio" >Promedio del a&ntilde;o</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Valor de la base</td>
			<td><input type="text" value="<?php echo $valor_base_vacaciones; ?>" style="width:190px;" readonly id="valor_base_vacaciones"></td>
		</tr>

		<tr class="thead" style="background-color: #A2A2A2;" >
			<td colspan="3">INFORMACIÓN VACACIONES DISFRUTADAS</td>
		</tr>
		<tr>
			<td>Fecha Inicio vacaciones difrutadas</td>
			<td><input type="text" value="<?php echo $fecha_inicio_vacaciones_disfrutadas; ?>" style="width:190px;" data-requiere="true" id="fecha_inicio_vacaciones_disfrutadas"  ></td>
		</tr>
		<tr>
			<td>Fecha final vacaciones difrutadas</td>
			<td><input type="text" value="<?php echo $fecha_final_vacaciones_disfrutadas; ?>" style="width:190px;" data-requiere="true" id="fecha_final_vacaciones_disfrutadas"  ></td>
		</tr>
		<tr>
			<td>Fecha donde inicia labores</td>
			<td><input type="text" value="<?php echo $fecha_inicio_labores; ?>" style="width:190px;" data-requiere="true" id="fecha_inicio_labores"  ></td>
		</tr>
		<tr>
			<td>Dias a difrutar</td>
			<td><input type="text" value="<?php echo $dias_vacaciones_disfrutadas; ?>" style="width:190px;" readonly id="dias_vacaciones_disfrutadas"  ></td>
		</tr>
		<tr>
			<td>Valor vacaciones disfrutadas</td>
			<td><input type="text" value="<?php echo $valor_vacaciones_disfrutadas; ?>" style="width:190px;" readonly id="valor_vacaciones_disfrutadas"  ></td>
		</tr>

		<tr class="thead" style="background-color: #A2A2A2;">
			<td colspan="3">INFORMACIÓN VACACIONES COMPENSADAS</td>
		</tr>
		<tr>
			<td>Dias vacaciones compensadas</td>
			<td><input type="text" value="<?php echo $dias_vacaciones_compensadas; ?>" style="width:190px;"  id="dias_vacaciones_compensadas" onkeyup="validaNumeroDias(event,this)" ></td>
		</tr>
		<tr>
			<td>Valor vacaciones compensadas</td>
			<td><input type="text" value="<?php echo $valor_vacaciones_compensadas; ?>" style="width:190px;" readonly id="valor_vacaciones_compensadas"  ></td>
		</tr>
	</table>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	<?php echo $acumscript; ?>

	function ventanaAyuda() {

		var html = `<div class="content" style="overflow: auto;float: left;background-color:#FFF;">
						<table class="table-form" style="width:90%;">
							<tr class="thead" style="background-color: #A2A2A2;">
								<td>VACACIONES COMPLETAS</td>
							</tr>
							<tr>
								<td>
									Al seleccionar la opcion de vacaciones completas, el sistema interpreta que el empleado tomara la totalidad de sus 15 dias habiles de vacaciones,
									de manera que el proximo libro de vacaciones tomara el periodo siguiente de vacaciones, en cuanto a la contabilidad, se cerrara de forma autormatica
									todas las provisiones que corresponden a las vacaciones, ademas de realizar de forma automatica el ajuste contable respectivo.
								</td>
							</tr>
							<tr class="thead" style="background-color: #A2A2A2;">
								<td>VACACIONES PARCIALES</td>
							</tr>
							<tr>
								<td>
									En las vacaciones parciales, se pueden dividir el periodo de vacaciones en varias secciones, es decir, pude disfrutar sus vacaciones de forma segmentada o pagada, por ejemplo
									si un empleado quiere, puede disfrutar 7 dias de vacaciones en un mes y al mes siguiente los dias restantes, o en el tiempo que desee, SE DEBE TENER EN CUENTA que
									para este tipo de pago de vacaciones, el sistema toma las provisiones pero no realiza ajuste, de manera
									que la persona encargada de la contabilidad ESTA OBLIGADA a realizar los ajustes contables respectivos en una nota general para ajustar la provision.

								</td>
							</tr>

						</table>
					</div>`;
		Win_Ventana_ventnaAyuda = new Ext.Window({
			width       : 400,
			height      : 400,
			id          : 'Win_Ventana_ventnaAyuda',
			title       : 'Informacion del campo tipo de vacaciones',
			modal       : true,
			autoScroll  : false,
			closable    : true,
			autoDestroy : true,
			html        : html
		}).show();
	}

	// EVENTO DEL BOTON CARGAR VACACIONES, SI NO SE CARGARON EN LA PLANILLA
	function cargarVacaciones(id_empleado,id_contrato) {
		var fecha_inicio           = document.getElementById('fecha_inicio_periodo_vacaciones').value
		,	fecha_final            = document.getElementById('fecha_final_periodo_vacaciones').value

        Ext.get('loadForm').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc          : 'cargarVacaciones',
				id_empleado  : id_empleado,
				id_contrato  : id_contrato,
				id_planilla  : '<?php echo $id_planilla; ?>',
				fecha_inicio : fecha_inicio,
				fecha_final  : fecha_final,
            }
        });
    }

	function calcularBaseVacaciones() {
		var fecha_inicio           = document.getElementById('fecha_inicio_periodo_vacaciones').value
		,	fecha_final            = document.getElementById('fecha_final_periodo_vacaciones').value
		,	base_vacaciones        = document.getElementById('base_vacaciones').value
		,	id_concepto_vacaciones = document.getElementById('concepto_vacaciones').dataset.id

		// VALIDACIONES
		if (base_vacaciones=="") { document.getElementById('valor_base_vacaciones').value=""; return; }
		if (base_vacaciones=='promedio_anio' && id_concepto_vacaciones<=0) { alert('Debe seleccionar el concepto de vacaciones!'); }

		Ext.get('loadForm').load({
			url     : 'liquidacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                    : 'calcularBaseVacaciones',
				id_concepto_vacaciones : id_concepto_vacaciones,
				base_vacaciones        : base_vacaciones,
				fecha_inicio           : fecha_inicio,
				fecha_final            : fecha_final,
				id_empleado            : "<?php echo $id_empleado; ?>",
				id_contrato            : "<?php echo $id_contrato; ?>",
				id_planilla            : "<?php echo $id_planilla; ?>",
			}
		});
	}

	// FUNCIÓN PARA CALCULAR LOS DÍAS TRANSCURRIDOS ENTRE DOS FECHAS
	function restaFechas(){
		var f1=document.getElementById("fecha_inicio_vacaciones_disfrutadas").value;
		var f2=document.getElementById("fecha_final_vacaciones_disfrutadas").value;

		if (f1=="Seleccione..." || f1=="" || f2=="Seleccione..." || f2=="") {
			document.getElementById("dias_vacaciones_disfrutadas").value="";
			return;
		}

		var aFecha1 = f1.split("-");
		var aFecha2 = f2.split("-");
		var fFecha1 = Date.UTC(aFecha1[0],aFecha1[1]-1,aFecha1[2]);
		var fFecha2 = Date.UTC(aFecha2[0],aFecha2[1]-1,aFecha2[2]);
		var dif = fFecha2 - fFecha1;
		var dias = Math.floor(dif / (1000 * 60 * 60 * 24));
		// return dias;
		// console.log(dias);
		document.getElementById("dias_vacaciones_disfrutadas").value=dias;
		// LLAMAR LA FUNCION PARA CALCULAR EL VALOR DE LAS VACAIONES DISFRUTADAS
		calculaValorVacacionesDisfrutadas(dias);
		calculaValorClick();
 	}

 	// =========== FUNCION PARA CALCULAR EL VALOR DE LAS VACACIONES DISFRUTADAS ===================//
    function calculaValorVacacionesDisfrutadas(dias,op){

		var id_input               = (op=='compensadas')? 'valor_vacaciones_compensadas' : 'valor_vacaciones_disfrutadas'
		,	base_vacaciones        = document.getElementById('base_vacaciones').value
		,	id_concepto_vacaciones = document.getElementById('concepto_vacaciones').dataset.id
		,	fecha_inicio           = document.getElementById('fecha_inicio_periodo_vacaciones').value
		,	fecha_final            = document.getElementById('fecha_final_periodo_vacaciones').value

        Ext.get('loadForm').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                    : 'calculaValorVacacionesDisfrutadas',
                dias                   : dias,
                id_empleado            : "<?php echo $id_empleado; ?>",
				id_contrato            : "<?php echo $id_contrato; ?>",
				id_planilla            : "<?php echo $id_planilla; ?>",
                base_vacaciones        : base_vacaciones,
                id_concepto_vacaciones : id_concepto_vacaciones,
                id_input               : id_input,
                fecha_inicio           : fecha_inicio,
                fecha_final            : fecha_final,
            }
        });
    }

 	function validaNumeroDias(event,input){
 		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
 		var numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        // if(tecla == 13 || tecla == 9){ guardarNumeroCheque(input.value); return; }
        // else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,"");
            input.value = numero;
        }

 		if (keyCode=="97"
			|| keyCode=="98"
			|| keyCode=="99"
			|| keyCode=="100"
			|| keyCode=="101"
			|| keyCode=="102"
			|| keyCode=="103"
			|| keyCode=="104"
			|| keyCode=="105"
			|| keyCode=="96"
			|| keyCode=="49"
			|| keyCode=="50"
			|| keyCode=="51"
			|| keyCode=="52"
			|| keyCode=="53"
			|| keyCode=="54"
			|| keyCode=="55"
			|| keyCode=="56"
			|| keyCode=="57"
			|| keyCode=="48"
			|| keyCode=="46"
			|| keyCode=="8") {
 			calculaValorVacacionesDisfrutadas(numero,"compensadas");
 		}
 	}

 	function calculaValorClick(){
 		var dias=document.getElementById("dias_vacaciones_compensadas").value;
 		if (dias<=0) {
 			return;
 		}

 		calculaValorVacacionesDisfrutadas(dias,"compensadas");
 	}

 	function ventanaBuscarConceptosVacaciones() {
        Win_Ventana_ventana_buscar_concepto = new Ext.Window({
                width       : 500,
                height      : 400,
                id          : 'Win_Ventana_ventana_buscar_concepto',
                title       : 'Conceptos',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'liquidacion/bd/buscar_concepto.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
						cargaFuncion : 'responseVentanaBuscarConceptoVacaciones(id)',
						id_empleado  : "<?php echo $id_empleado; ?>",
						id_contrato  : "<?php echo $id_contrato; ?>",
						id_planilla  : "<?php echo $id_planilla; ?>",
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
                                handler     : function(){ Win_Ventana_ventana_buscar_concepto.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
    }

    function responseVentanaBuscarConceptoVacaciones(id){
		var codigo      = document.getElementById('detalles_concepto_'+id).dataset.codigo
		,	concepto    = document.getElementById('div_grilla_buscar_conceptos_concepto_'+id).innerHTML
		,	id_concepto = document.getElementById('detalles_concepto_'+id).dataset.id;

        document.getElementById('concepto_vacaciones').dataset.id = id_concepto;
        document.getElementById('concepto_vacaciones').dataset.codigo = codigo;
        document.getElementById('concepto_vacaciones').value = concepto;

        Win_Ventana_ventana_buscar_concepto.close();
        restaFechas();
    }

    function guardarInfoVacaciones() {

        var fecha_inicio_contrato               = document.getElementById('fecha_inicio_contrato').value
        ,   fecha_inicio_periodo_vacaciones     = document.getElementById('fecha_inicio_periodo_vacaciones').value
        ,   fecha_final_periodo_vacaciones      = document.getElementById('fecha_final_periodo_vacaciones').value
        ,   fecha_inicio_vacaciones_disfrutadas = document.getElementById('fecha_inicio_vacaciones_disfrutadas').value
        ,   fecha_final_vacaciones_disfrutadas  = document.getElementById('fecha_final_vacaciones_disfrutadas').value
        ,   id_concepto_vacaciones              = document.getElementById('concepto_vacaciones').dataset.id
        ,   concepto_vacaciones                 = document.getElementById('concepto_vacaciones').value
        ,   base_vacaciones                     = document.getElementById('base_vacaciones').value
        ,   dias_vacaciones_disfrutadas         = document.getElementById('dias_vacaciones_disfrutadas').value
        ,   valor_base_vacaciones               = document.getElementById('valor_base_vacaciones').value
        ,   valor_vacaciones_disfrutadas        = document.getElementById('valor_vacaciones_disfrutadas').value
        ,   fecha_inicio_labores                = document.getElementById('fecha_inicio_labores').value
        ,   dias_vacaciones_compensadas         = document.getElementById('dias_vacaciones_compensadas').value
        ,   valor_vacaciones_compensadas        = document.getElementById('valor_vacaciones_compensadas').value
        ,   tipo_pago_vacaciones                = document.getElementById('tipo_pago_vacaciones').value;

        if (base_vacaciones=='') { alert("Debe seleccionar la base de las vacaciones"); return; }
        if (valor_vacaciones_disfrutadas=='') { alert("El campo valor vacaciones disfrutadas debe tener un valor, puede ser 0"); return; }
       	if (valor_vacaciones_compensadas=='') { alert("El campo valor vacaciones compensadas debe tener un valor, puede ser 0"); return;  }

       	MyLoading2('on');

        Ext.get('loadForm').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc                                 : 'guardarInfoVacaciones',
				fecha_inicio_contrato               : fecha_inicio_contrato,
				fecha_inicio_periodo_vacaciones     : fecha_inicio_periodo_vacaciones,
				fecha_final_periodo_vacaciones      : fecha_final_periodo_vacaciones,
				fecha_inicio_vacaciones_disfrutadas : fecha_inicio_vacaciones_disfrutadas,
				fecha_final_vacaciones_disfrutadas  : fecha_final_vacaciones_disfrutadas,
				id_concepto_vacaciones              : id_concepto_vacaciones,
				concepto_vacaciones                 : concepto_vacaciones,
				base_vacaciones                     : base_vacaciones,
				dias_vacaciones_disfrutadas         : dias_vacaciones_disfrutadas,
				valor_base_vacaciones               : valor_base_vacaciones,
				valor_vacaciones_disfrutadas        : valor_vacaciones_disfrutadas,
				fecha_inicio_labores                : fecha_inicio_labores,
				dias_vacaciones_compensadas         : dias_vacaciones_compensadas,
				valor_vacaciones_compensadas        : valor_vacaciones_compensadas,
				id_empleado                         : "<?php echo $id_empleado; ?>",
				id_contrato                         : "<?php echo $id_contrato; ?>",
				id_planilla                         : "<?php echo $id_planilla; ?>",
				tipo_pago_vacaciones                : tipo_pago_vacaciones,
            }
        });
    }

</script>