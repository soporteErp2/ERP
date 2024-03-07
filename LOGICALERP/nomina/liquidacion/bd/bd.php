<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	include_once("../../../funciones_globales/funciones_php/contabilizacion_simultanea.php");
	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id_planilla)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if ($opc<>'guardarFechaPlanilla') {
			verificaCierre($id_planilla,'nomina_planillas_liquidacion',$id_empresa,$link);
		}
	}

	switch ($opc) {
		case 'guardarFechaPlanilla':
			guardarFechaPlanilla($id_planilla,$opcGrillaContable,$fecha,$campo,$id_empresa,$link);
			break;

		case 'guardarTipoLiquidacion':
			guardarTipoLiquidacion($id_planilla,$id_tipo_liquidacion,$id_empresa,$link);
			break;
		case 'agregarEmpleado':
			agregarEmpleado($id_planilla,$id_contrato,$cont,$id_empresa,$link);
			break;
		case 'eliminarEmpleado':
			eliminarEmpleado($cont,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link);
			break;
		case 'cargarConceptosEmpleado':
			cargarConceptosEmpleado($id_contrato,$id_empleado,$id_planilla,$id_empresa,$link);
			break;
		case 'cargarTodosEmpleados':
			cargarTodosEmpleados($id_planilla,$id_empresa,$sucursal,$link);
			break;
		case 'eliminarConcepto':
			eliminarConcepto($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$naturaleza,$valor_concepto,$id_empresa,$link);
			break;
		case 'guardarConcepto':
			guardarConcepto($input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$id_empresa,$link);
			break;
		case 'actualizarconcepto':
			actualizarconcepto($id_insert,$input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$dias_adicionales,$id_empresa,$link);
			break;
		case 'updateFinalizaContrato':
			updateFinalizaContrato($terminar_contrato,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link);
			break;
		case 'updateMotivoFinContrato':
			updateMotivoFinContrato($id_motivo_finalizacion,$motivo_finalizacion,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link);
			break;
		case 'updateDiasLaborados':
			updateDiasLaborados($dias,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link);
			break;
		case 'verificaEmpleado':
			verificaEmpleado($check,$id_contrato,$id_empleado,$id_planilla,$cont,$id_empresa,$link);
			break;
		case 'ventanaConfigurarCuentasConcepto':
			ventanaConfigurarCuentasConcepto($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link);
			break;
		case 'sincronizarCuentaNiif':
			sincronizarCuentaNiif($id,$campoId,$campoText,$id_empresa,$link);
			break;
		case 'updateCuentasConcepto':
			updateCuentasConcepto($id_cuenta_colgaap,$id_cuenta_niif,$id_cuenta_contrapartida_colgaap,$id_cuenta_contrapartida_niif,$id_concepto,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link);
			break;
		case 'calculaValorConceptoFormulaInput':
			calculaValorConceptoFormulaInput($id_insert_concepto,$id_planilla,$id_concepto,$id_contrato,$id_empleado,$cont,$variable,$id_empresa,$link);
			break;
		case 'calculaValorConceptoBuscado':
			calculaValorConceptoBuscado($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$id_empresa,$link);
			break;
		case 'actualizarSucursal':
			actualizarSucursal($id_planilla,$sucursal,$id_empresa,$link);
			break;
		case 'ventanaConfigurarConceptoDeduccion':
			ventanaConfigurarConceptoDeduccion($cont,$id_planilla,$id_concepto,$id_prestamo,$id_empleado,$id_contrato,$id_empresa,$link);
			break;
		case 'guardarConceptoDeducir':
			guardarConceptoDeducir($id_planilla,$cont_deducir,$cont,$id_empleado,$id_contrato,$id_concepto,$id_prestamo,$id_concepto_deducir,$valor_deducir,$id_empresa,$link);
			break;
		case 'eliminarConceptoDeducir':
			eliminarConceptoDeducir($cont_deducir,$id_empleado,$id_concepto,$id_prestamo,$id_concepto_deducir,$id_planilla,$id_contrato,$id_empresa,$link);
			break;
		case 'buscarEmpleadoCargado':
			buscarEmpleadoCargado($id_planilla,$filtro,$estado,$id_empresa,$link);
			break;
		case 'updateVacaciones':
			updateVacaciones($vacaciones,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$mysql);
			break;
		case 'ventana_libro_vacaciones':
			ventana_libro_vacaciones($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link);
			break;
		case 'guardarInfoVacaciones':
			guardarInfoVacaciones($fecha_inicio_contrato,$fecha_inicio_periodo_vacaciones,$fecha_final_periodo_vacaciones,$fecha_inicio_vacaciones_disfrutadas,$fecha_final_vacaciones_disfrutadas,$id_concepto_vacaciones,$concepto_vacaciones,$base_vacaciones,$dias_vacaciones_disfrutadas,$valor_base_vacaciones,$valor_vacaciones_disfrutadas,$fecha_inicio_labores,$dias_vacaciones_compensadas,$valor_vacaciones_compensadas,$id_empleado,$id_contrato,$id_planilla,$tipo_pago_vacaciones,$id_empresa,$mysql);
			break;
		case 'guardarObservacionEmpleado':
			guardarObservacionEmpleado($observacion,$id_planilla,$id,$id_empresa,$link);
			break;
		case 'calcularBaseVacaciones':
			calcularBaseVacaciones($id_concepto_vacaciones,$fecha_inicio,$fecha_final,$base_vacaciones,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$mysql);
			break;
		case 'calculaValorVacacionesDisfrutadas':
			calculaValorVacacionesDisfrutadas($fecha_inicio,$fecha_final,$id_input,$id_concepto_vacaciones,$base_vacaciones,$dias,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$mysql);
			break;
		case 'UpdateFechaFinContrato':
			UpdateFechaFinContrato($fecha,$id_planilla,$id_empleado,$id_empresa,$link);
			break;
		case 'check_provision':
			check_provision($accion,$id_planilla,$id_empleado,$id_contrato,$id_empresa,$mysql);
			break;
		case 'cargarVacaciones':
			cargarVacaciones($id_planilla,$id_empleado,$id_contrato,$fecha_inicio,$fecha_final,$id_empresa,$mysql);
			break;
		case 'helpTipoPago':
			helpTipoPago();
			break;


	//////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////
		case 'terminarGenerar':
			terminarGenerar($id_planilla,$id_empresa,$id_sucursal,$link);
			break;
		case 'modificarDocumentoPlanillaNomina':
			modificarDocumentoPlanillaNomina($id_planilla,$id_empresa,$link);
			break;
		case 'cancelarPlanillaNomina':
			cancelarPlanillaNomina($id_planilla,$id_empresa,$link);
			break;
		case 'restaurarPlanillaNomina':
			restaurarPlanillaNomina($id_planilla,$id_empresa,$link);
			break;
		case 'calcularValoresPlanilla':
			calcularValoresPlanilla($id_planilla,$id_empresa,$link);
			break;
		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$id_empresa,$link);
			break;
		case 'enviarPlanillaNomina':
			enviarPlanillaNomina($id_planilla,$id_empresa,$link);
			break;
		case 'enviarVolanteUnicoEmpleado':
			enviarVolanteUnicoEmpleado($id_planilla,$id_contrato,$id_empleado,$id_empresa,$link);
			break;
		case 'saveDataNE':
			saveDataNE($id_planilla,$id_empleado,$id_concepto,$id_estructura,$data,$link);
			break;
		case 'deleteDataNE': 
			deleteDataNE($id,$link);
			break;

	}

	function guardarFechaPlanilla($id_planilla,$opcGrillaContable,$fecha,$campo,$id_empresa,$link){
		$script='';
		if ($campo!='fecha_documento') {

			//ELIMINAR LOS CONCEPTOS QUE ESTAN EN LA PLANILLA
			$sql="DELETE FROM nomina_planillas_liquidacion_empleados_conceptos WHERE id_planilla=$id_planilla";
			$query=mysql_query($sql,$link);

			//ELIMINAR LOS EMPLEADOS QUE ESTAN EN LA PLANILLA
			$sql="DELETE FROM nomina_planillas_liquidacion_empleados WHERE id_planilla=$id_planilla";
			$query=mysql_query($sql,$link);

			// ACTUALIZAR LA COLUMNA ID PLANILLA LIQUIDACION DE LAS FILAS DE LOS CONCEPTOS DE LAS PLANILLAS EN ESE PERIODO DE TIEMPO
			$sql="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=0, tipo_planilla_cruce=''
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla_cruce=$id_planilla ";
			$query=mysql_query($sql,$link);

			$script='<script>
						document.getElementById("contenedorPrincipalConceptos").innerHTML = "";
						document.getElementById("contenedorEmpleados").innerHTML          = "";
					</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);;
		}

		// ACTUALIZAR LAS FECHAS DEL DOCUMENTO
		$sql="UPDATE nomina_planillas_liquidacion SET $campo='$fecha' WHERE activo=1 AND id=$id_planilla AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se guardo la fecha, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
		else{
			echo $script;
		}
	}

	function guardarTipoLiquidacion($id_planilla,$id_tipo_liquidacion,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_liquidacion SET id_tipo_liquidacion='$id_tipo_liquidacion' WHERE activo=1 AND id=$id_planilla AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se guardo la fecha, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
		else{
			echo '<script>
					document.getElementById("contenedorPrincipalConceptos").innerHTML = "";
					document.getElementById("contenedorEmpleados").innerHTML          = "";
				</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);
		}
	}

	function agregarEmpleado($id_planilla,$id_contrato,$cont,$id_empresa,$link){
		$fecha=date("Y-m-d");

		//CONSULTAR EL RANGO DE FECHA DE LA PLANILLA
		$sql="SELECT fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$fecha_inicio = mysql_result($query,0,'fecha_inicio');
		$fecha_final  = mysql_result($query,0,'fecha_final');
		$id_sucursal  = mysql_result($query,0,'id_sucursal');

		//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
		$sql="SELECT id_empleado,
					tipo_documento_empleado,
					documento_empleado,
					nombre_empleado,
					numero_contrato,
					salario_basico,
					fecha_inicio_nomina,
					fecha_fin_contrato,
					IF(fecha_fin_contrato <= '$fecha', 'Si', 'No') AS terminar_contrato,
					id_grupo_trabajo,
					grupo_trabajo,
					valor_nivel_riesgo_laboral,
					id_tipo_contrato
				FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id=$id_contrato";
		$query=mysql_query($sql,$link);

		$id_empleado                = mysql_result($query,0,'id_empleado');
		$tipo_documento_empleado    = mysql_result($query,0,'tipo_documento_empleado');
		$documento_empleado         = mysql_result($query,0,'documento_empleado');
		$nombre_empleado            = mysql_result($query,0,'nombre_empleado');
		$numero_contrato            = mysql_result($query,0,'numero_contrato');
		$salario_basico             = mysql_result($query,0,'salario_basico');
		$fecha_inicio_nomina        = mysql_result($query,0,'fecha_inicio_nomina');
		$fecha_fin_contrato         = mysql_result($query,0,'fecha_fin_contrato');
		$id_grupo_trabajo           = mysql_result($query,0,'id_grupo_trabajo');
		$grupo_trabajo              = mysql_result($query,0,'grupo_trabajo');
		$terminar_contrato          = mysql_result($query,0,'terminar_contrato');
		$valor_nivel_riesgo_laboral = mysql_result($query,0,'valor_nivel_riesgo_laboral');
		$id_tipo_contrato           = mysql_result($query,0,'id_tipo_contrato');

		// CONSULTAR LOS DIAS DEL TIPO DE CONTRATO
		$sql="SELECT dias FROM nomina_tipo_contrato WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_tipo_contrato";
		$query=mysql_query($sql,$link);
		$dias_contrato=mysql_result($query,0,'dias');

		if ($dias_contrato==0) {
			$terminar_contrato='No';
		}
		else{
			$terminar_contrato = (compararFechas($fecha_fin_contrato,$fecha)<0)? 'Si' : 'No' ;
		}

		// CONSULTAR SI EL EMPLEADO TIENE PLANILLAS DE LQUIDACION CREADAS EN ESE PERIODO DE TIEMPO PARA EXCLUIR ESAS FECHAS DE LA BASE DEL CONCEPTO
		echo$sql="SELECT
					NP.id,
					NP.consecutivo,
					NPE.dias_laborados,
					NPE.id_empleado,
					NPE.id_concepto,
					NPE.concepto,
					NPE.valor_concepto,
					NPE.valor_concepto_ajustado,
					NPE.naturaleza,
					NPE.base
				FROM
					nomina_planillas_liquidacion AS NP,
					nomina_planillas_liquidacion_empleados_conceptos AS NPE
				WHERE
					NP.activo 		= 1
				AND NP.estado       = 1
				AND NP.id_empresa   = $id_empresa
				AND NP.fecha_final BETWEEN '$fecha_inicio'
				AND  '$fecha_final'
				AND NPE.id_planilla = NP.id
				AND NPE.id_empleado = '$id_empleado'
				AND NPE.id_contrato = $id_contrato
				GROUP BY NP.id,NPE.id_concepto,NPE.id_empleado";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereIdLiquidacionPlanillas.= ($whereIdLiquidacionPlanillas=='')? ' id_planilla='.$row['id'] : ' OR id_planilla='.$row['id'] ;
			if ($arrayConceptosLiquidados[$row['id_concepto']]['valor_concepto']>0) {
				$arrayConceptosLiquidados[$row['id_concepto']]['valor_concepto']+=($row['naturaleza']=='Provision')? $row['valor_concepto_ajustado'] : $row['valor_concepto'] ;
				$arrayConceptosLiquidados[$row['id_concepto']]['base']+= $row['base'];
			}
			else{
				$arrayConceptosLiquidados[$row['id_concepto']] = array(
																		'saldo_dias_laborados' => $row['dias_laborados'],
																		'valor_concepto'       => ( ($row['naturaleza']=='Provision')? $row['valor_concepto_ajustado'] : $row['valor_concepto']),
																		'base'                 => $row['base'],
																		 );
			}
		}


		//
		// $sql="";
		// $query=$mysql->query($sql,$mysql->link);
		// echo $arrayConceptosAcumulados[547]['base']." ---- ";
		// print_r($arrayConceptosLiquidados);

		//CONSULTAR LAS PLANILLAS DE NOMINA QUE ESTAN DENTRO DEL RANGO DE FECHAS DE LA LIQUIDACION PARA TRAER TODO LOS PROVISIONADO
		echo$sql="SELECT
					NP.id,
					NP.fecha_final,
					NPE.dias_laborados,
					NPE.id_empleado
				FROM
					nomina_planillas AS NP,
					nomina_planillas_empleados AS NPE
				WHERE
					NP.activo       = 1
				AND (NP.estado= 1 OR NP.estado=2)
				AND NP.id_empresa   = $id_empresa
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final  <= '$fecha_final'
				AND NPE.id_planilla = NP.id
				AND NPE.id_empleado = '$id_empleado'
				AND NPE.id_contrato = $id_contrato
				GROUP BY NP.id,NPE.id_empleado
				ORDER BY fecha_final ASC";
		$query=mysql_query($sql,$link);
		$whereIdPlanillas='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			$dias_laborados+=$row['dias_laborados'];
			$fecha_finalizacion_contrato = $row['fecha_final'];
			// echo '<script>console.log("'.$row['fecha_final'].'");</script>';
		}

		if ($fecha_finalizacion_contrato<>'') {
			$fecha_finalizacion_contrato = ($terminar_contrato=='Si')? $fecha_finalizacion_contrato : '0000-00-00' ;
		}
		else{
			$fecha_finalizacion_contrato = $fecha_final;
		}

		// ACTUALIZAR LA COLUMNA ID PLANILLA LIQUIDACION DE LAS FILAS DE LOS CONCEPTOS DE LAS PLANILLAS EN ESE PERIODO DE TIEMPO
		$sql="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=$id_planilla, tipo_planilla_cruce='LE'
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla_cruce=0  AND ($whereIdPlanillas) ";
		$query=mysql_query($sql,$link);

		// CONSULTAR EL LIBRO DE VACACIONES PARA AGREGAR EL VALOR DE LAS VACACIONES PAGADAS A LA BASE
		$sql   = "SELECT
						SUM(valor_vacaciones_disfrutadas) AS saldo_vacaciones
					FROM nomina_vacaciones_empleados
					WHERE
						activo=1
					AND id_empresa=$id_empresa
					AND id_empleado=$id_empleado
					AND fecha_inicio_vacaciones_disfrutadas >= '$fecha_inicio'
					AND fecha_inicio_vacaciones_disfrutadas  <= '$fecha_final' ";
		$query = mysql_query($sql,$link);
		$saldo_vacaciones = mysql_result($query,0,'saldo_vacaciones');

		//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE ESE EMPLEADO
		$sql="SELECT
				id_concepto,
				codigo_concepto,
				concepto,
				SUM(valor_concepto) AS valor_provisionado,
				SUM(saldo_dias_laborados) AS saldo_dias_laborados,
				naturaleza,
				id_planilla_cruce,
				tipo_planilla_cruce
				FROM
					nomina_planillas_empleados_conceptos
				WHERE
					activo = 1
				AND id_empleado = $id_empleado
				AND id_empresa = $id_empresa
				AND ($whereIdPlanillas)
				AND saldo_dias_laborados>0
				GROUP BY id_concepto";
		$query=mysql_query($sql,$link);
		$whereIdConceptos='';
		while ($row=mysql_fetch_array($query)){

			// SI EL CONCEPTO ES PROVISION Y ESTA ASIGNADO A OTRA PLANILLA DE LIQUIDACION ENTONCES NO SE PONE
			if ($row['id_planilla_cruce']<>$id_planilla && $row['naturaleza']=='Provision') { continue; }

			if ($row['naturaleza']=='Provision'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_concepto']] = $row['valor_provisionado'];
				$arrayDiasConceptos[$row['id_concepto']]    = $row['saldo_dias_laborados'];
			}

			$arrayConceptosAcumulados[$row['id_concepto']] = array(
																	'codigo'               => $row['codigo_concepto'],
																	'concepto'             => $row['concepto'],
																	'valor_concepto'       => $row['valor_provisionado'],
																	'saldo_dias_laborados' => $row['saldo_dias_laborados'],
																	'naturaleza'           => $row['naturaleza'],
																	'base'                 => $saldo_vacaciones,
																	);
		}

		// CONSULTAR LOS CONCEPTOS PARA LA BASE DE LA LIQUIDACION
		$whereIdConceptosLiquidacion = str_replace("id", "id_concepto", $whereIdConceptos);
		$sql="SELECT * FROM nomina_conceptos_base_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdConceptosLiquidacion)";
		$query = mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			if ($row['naturaleza_base']=='Devengo'){
				$arrayConceptosAcumulados[$row['id_concepto']]['base'] += $arrayConceptosAcumulados[$row['id_concepto_base']]['valor_concepto'];
			}
			else if ($row['naturaleza_base']=='Deduccion') {
				$arrayConceptosAcumulados[$row['id_concepto']]['base'] -= $arrayConceptosAcumulados[$row['id_concepto_base']]['valor_concepto'];
			}
		}

		// print_r($arrayConceptosAcumulados);
		// CONSULTAR LA SUCURSAL DEL EMPLEADO
		$sql="SELECT id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		$query=mysql_query($sql,$link);
		$id_sucursal=mysql_result($query,0,'id_sucursal');

		//INSERTAR EL EMPLEADO EN LA PLANILLA
		$sql="INSERT INTO nomina_planillas_liquidacion_empleados(id_planilla,id_empleado,tipo_documento,documento_empleado,nombre_empleado,id_contrato,dias_laborados,terminar_contrato,fecha_fin_contrato,id_sucursal,id_empresa)
				VALUES ('$id_planilla','$id_empleado','$tipo_documento_empleado','$documento_empleado','$nombre_empleado','$id_contrato','$dias_laborados','$terminar_contrato','$fecha_finalizacion_contrato','$id_sucursal','$id_empresa') ";
		$query=mysql_query($sql,$link);
		if ($query) {

			//CONSULTAR TODOS LOS CONCEPTOS CON CARGA AUTOMATICA DE LA BASE DE DATOS
			$sql="SELECT id,
						codigo,
						descripcion,
						formula_liquidacion,
						nivel_formula_liquidacion,
						tipo_concepto,
						id_cuenta_colgaap,
						cuenta_colgaap,
						descripcion_cuenta_colgaap,
						id_cuenta_niif,
						cuenta_niif,
						descripcion_cuenta_niif,
						caracter,
						centro_costos,
						id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap,
						descripcion_cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif,
						descripcion_cuenta_contrapartida_niif,
						caracter_contrapartida,
						id_cuenta_colgaap_liquidacion,
						cuenta_colgaap_liquidacion,
						descripcion_cuenta_colgaap_liquidacion,
						id_cuenta_niif_liquidacion,
						cuenta_niif_liquidacion,
						descripcion_cuenta_niif_liquidacion,
						centro_costos_contrapartida,
						naturaleza,
						imprimir_volante
					FROM nomina_conceptos
					WHERE
						activo=1
					AND id_empresa=$id_empresa
					AND naturaleza='Provision'
					AND ($whereIdConceptos)
					ORDER BY nivel_formula_liquidacion ASC";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id = $row['id'];
				$tipo_concepto = $row['tipo_concepto'];
				$nivel_formula = $row['nivel_formula_liquidacion'];
				$row['formula'] = str_replace(" ","",$row['formula_liquidacion']);
				// echo "formula:".$row['formula'].'-';
				$arrayConceptos[$nivel_formula][$id] = array(
															'codigo'           					   	   => $row['codigo'],
															'concepto'                                 => $row['descripcion'],
															'formula'                                  => $row['formula_liquidacion'],
															'formula_original'                         => $row['formula_liquidacion'],
															'nivel_formula'                            => $row['nivel_formula_liquidacion'],
															'valor_concepto'                           => 0,
															'valor_provisionado'                       => $arrayConceptosAcumulados[$row['id']]['valor_concepto'],
															'base'                                     => $arrayConceptosAcumulados[$row['id']]['base'],
															'insert'                                   => 'false',
															'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
															'cuenta_colgaap'                           => $row['cuenta_colgaap'],
															'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
															'id_cuenta_niif'                           => $row['id_cuenta_niif'],
															'cuenta_niif'                              => $row['cuenta_niif'],
															'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
															'caracter'                                 => $row['caracter'],
															'centro_costos'                            => $row['centro_costos'],
															'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
															'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
															'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
															'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
															'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
															'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
															'caracter_contrapartida'                   => $row['caracter_contrapartida'],
															'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
															'naturaleza'                               => $row['naturaleza'],
															'imprimir_volante'                         => $row['imprimir_volante'],
															'id_cuenta_colgaap_liquidacion'            => $row['id_cuenta_colgaap_liquidacion'],
															'cuenta_colgaap_liquidacion'               => $row['cuenta_colgaap_liquidacion'],
															'descripcion_cuenta_colgaap_liquidacion'   => $row['descripcion_cuenta_colgaap_liquidacion'],
															'id_cuenta_niif_liquidacion'               => $row['id_cuenta_niif_liquidacion'],
															'cuenta_niif_liquidacion'                  => $row['cuenta_niif_liquidacion'],
															'descripcion_cuenta_niif_liquidacion'      => $row['descripcion_cuenta_niif_liquidacion'],
															'saldo_dias_laborados'                     => $arrayConceptosAcumulados[$row['id']]['saldo_dias_laborados'],
															);
			}

			// print_r($arrayConceptos);
			// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
			$sql="SELECT id_concepto,
						nivel_formula,
						nivel_formula_liquidacion,
						formula_liquidacion,
						id_cuenta_colgaap,
						cuenta_colgaap,
						descripcion_cuenta_colgaap,
						id_cuenta_niif,
						cuenta_niif,
						descripcion_cuenta_niif,
						caracter,
						centro_costos,
						id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap,
						descripcion_cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif,
						descripcion_cuenta_contrapartida_niif,
						caracter_contrapartida,
						centro_costos_contrapartida,
						id_cuenta_colgaap_liquidacion,
						cuenta_colgaap_liquidacion,
						descripcion_cuenta_colgaap_liquidacion,
						id_cuenta_niif_liquidacion,
						cuenta_niif_liquidacion,
						descripcion_cuenta_niif_liquidacion
						FROM nomina_conceptos_grupos_trabajo
						WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id            = $row['id_concepto'];
				$nivel_formula = $row['nivel_formula_liquidacion'];
				echo $arrayConceptos[$nivel_formula][$id]['codigo'].':'.$arrayConceptos[$nivel_formula][$id]['cuenta_colgaap_liquidacion'].'-'.$row['cuenta_colgaap_liquidacion'].'<br>';
				// VALIDAR QUE EL CONCEPTO EXISTA EN EL ARRAY DE LOS CONCEPTOS
				if ($arrayConceptos[$nivel_formula][$id]['codigo']=='') {
					continue;
				}
				// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
				$arrayConceptos[$nivel_formula][$id]['formula']                                  = ($row['formula_liquidacion']=='')? $arrayConceptos[$nivel_formula][$id]['formula'] : $row['formula_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['formula_original']                         = ($row['formula_liquidacion']=='')? $arrayConceptos[$nivel_formula][$id]['formula_original'] : $row['formula_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['id_cuenta_colgaap']                        = $row['id_cuenta_colgaap'];
				$arrayConceptos[$nivel_formula][$id]['cuenta_colgaap']                           = $row['cuenta_colgaap'];
				$arrayConceptos[$nivel_formula][$id]['descripcion_cuenta_colgaap']               = $row['descripcion_cuenta_colgaap'];
				$arrayConceptos[$nivel_formula][$id]['id_cuenta_niif']                           = $row['id_cuenta_niif'];
				$arrayConceptos[$nivel_formula][$id]['cuenta_niif']                              = $row['cuenta_niif'];
				$arrayConceptos[$nivel_formula][$id]['descripcion_cuenta_niif']                  = $row['descripcion_cuenta_niif'];
				$arrayConceptos[$nivel_formula][$id]['caracter']                                 = $row['caracter'];
				$arrayConceptos[$nivel_formula][$id]['centro_costos']                            = $row['centro_costos'];
				$arrayConceptos[$nivel_formula][$id]['id_cuenta_contrapartida_colgaap']          = $row['id_cuenta_contrapartida_colgaap'];
				$arrayConceptos[$nivel_formula][$id]['cuenta_contrapartida_colgaap']             = $row['cuenta_contrapartida_colgaap'];
				$arrayConceptos[$nivel_formula][$id]['descripcion_cuenta_contrapartida_colgaap'] = $row['descripcion_cuenta_contrapartida_colgaap'];
				$arrayConceptos[$nivel_formula][$id]['id_cuenta_contrapartida_niif']             = $row['id_cuenta_contrapartida_niif'];
				$arrayConceptos[$nivel_formula][$id]['cuenta_contrapartida_niif']                = $row['cuenta_contrapartida_niif'];
				$arrayConceptos[$nivel_formula][$id]['descripcion_cuenta_contrapartida_niif']    = $row['descripcion_cuenta_contrapartida_niif'];
				$arrayConceptos[$nivel_formula][$id]['caracter_contrapartida']                   = $row['caracter_contrapartida'];
				$arrayConceptos[$nivel_formula][$id]['centro_costos_contrapartida']              = $row['centro_costos_contrapartida'];
				$arrayConceptos[$nivel_formula][$id]['id_cuenta_colgaap_liquidacion']            = $row['id_cuenta_colgaap_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['cuenta_colgaap_liquidacion']               = $row['cuenta_colgaap_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['descripcion_cuenta_colgaap_liquidacion']   = $row['descripcion_cuenta_colgaap_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['id_cuenta_niif_liquidacion']               = $row['id_cuenta_niif_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['cuenta_niif_liquidacion']                  = $row['cuenta_niif_liquidacion'];
				$arrayConceptos[$nivel_formula][$id]['descripcion_cuenta_niif_liquidacion']      = $row['descripcion_cuenta_niif_liquidacion'];

			}

			// --------------------------------------------------------- //
			// RECORRER EL ARRAY PARA CALCULAR EL VALOR CON LAS FORMULAS //
			// --------------------------------------------------------- //
			$valueInsert='';
			$nivel=0;

			// PRIMER CAPA, NIVEL DE LA FORMULA
			foreach ($arrayConceptos as $nivel_formula => $arrayConceptosArray) {
				// RECORRER LOS CONCEPTOS DE CADA NIVEL
				foreach ($arrayConceptosArray as $id_concepto => $arrayConceptosResul) {
					// -----------------------------------//
					// REEMPLAZAR LAS VARIABLES GENERALES //
					// -----------------------------------//

					// BASE DEL VALOR GANADO
					$base_concepto=($arrayConceptosAcumulados[$id_concepto]['base']/$arrayConceptosAcumulados[$id_concepto]['saldo_dias_laborados'])*30;
					$arrayConceptosResul['formula']=str_replace('{BL}', $base_concepto, $arrayConceptosResul['formula']);
					// DIAS LABORADOS
					$arrayConceptosResul['formula']=str_replace('{DL}', $arrayConceptosAcumulados[$id_concepto]['saldo_dias_laborados'], $arrayConceptosResul['formula']);

					// RECORRER ARRAY PARA REEMPLAZAR LOS CONCEPTOS DE LA FORMULA
					// NIVEL DE LA FORMULA DEL CONCEPTO
					foreach ($arrayConceptos as $nivel_formula_search => $arrayConceptosArray_search) {
						foreach ($arrayConceptosArray_search as $id_concepto_search => $arrayConceptosResul_search) {

							// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
							if($arrayConceptosResul_search['valor_concepto']<0 ){ continue; }

							// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR
							if($nivel_formula<$nivel_formula_search){ continue; }
							// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
							$arrayConceptosResul['formula']=str_replace('['.$arrayConceptosResul_search['codigo'].']', $arrayConceptos[$nivel_formula_search][$id_concepto_search]['valor_concepto'], $arrayConceptosResul['formula']);

						}
					}//->FIN FOR EACH PARA BUCAR LOS VALORES DE LOS CONCEPTOS DE LA FORMULA

					//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
					$search_var_concepto=strpos($arrayConceptosResul['formula'], '[');
					if ($search_var_concepto===false) {
						// CALCULAR LA FORMULA
						$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
						// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
						if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
							echo '<script>
									alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: ('.$arrayConceptosResul['formula'].')");
									console.log("'.$arrayConceptosResul['formula'].'");
								</script>';
							// continue;
						}

						$valor_concepto = $valor_concepto-$arrayConceptosLiquidados[$id_concepto]['valor_concepto'];
						$base = $arrayConceptosAcumulados[$id_concepto]['base']-$arrayConceptosLiquidados[$id_concepto]['base'];

						$arrayConceptos[$nivel_formula][$id_concepto]['valor_concepto']=$valor_concepto;
						$valueInsert.="('$id_planilla',
										'$id_empleado',
										'$id_contrato',
										'$id_concepto',
										'".$arrayConceptosResul['codigo']."',
										'".$arrayConceptosResul['concepto']."',
										'".$arrayConceptosAcumulados[$id_concepto]['valor_concepto']."',
										'".$valor_concepto."',
										'".$valor_concepto."',
										'".$base."',
										'".$arrayConceptosResul['formula']."',
										'".$arrayConceptosResul['formula_original']."',
										'".$nivel_formula."',
										'$id_sucursal',
										'$id_empresa',
										'".$arrayConceptosResul['id_cuenta_colgaap']."',
										'".$arrayConceptosResul['cuenta_colgaap']."',
										'".$arrayConceptosResul['descripcion_cuenta_colgaap']."',
										'".$arrayConceptosResul['id_cuenta_niif']."',
										'".$arrayConceptosResul['cuenta_niif']."',
										'".$arrayConceptosResul['descripcion_cuenta_niif']."',
										'".$arrayConceptosResul['caracter']."',
										'".$arrayConceptosResul['centro_costos']."',
										'".$arrayConceptosResul['id_cuenta_contrapartida_colgaap']."',
										'".$arrayConceptosResul['cuenta_contrapartida_colgaap']."',
										'".$arrayConceptosResul['descripcion_cuenta_contrapartida_colgaap']."',
										'".$arrayConceptosResul['id_cuenta_contrapartida_niif']."',
										'".$arrayConceptosResul['cuenta_contrapartida_niif']."',
										'".$arrayConceptosResul['descripcion_cuenta_contrapartida_niif']."',
										'".$arrayConceptosResul['caracter_contrapartida']."',
										'".$arrayConceptosResul['centro_costos_contrapartida']."',
										'".$arrayConceptosResul['id_cuenta_colgaap_liquidacion']."',
										'".$arrayConceptosResul['cuenta_colgaap_liquidacion']."',
										'".$arrayConceptosResul['descripcion_cuenta_colgaap_liquidacion']."',
										'".$arrayConceptosResul['id_cuenta_niif_liquidacion']."',
										'".$arrayConceptosResul['cuenta_niif_liquidacion']."',
										'".$arrayConceptosResul['descripcion_cuenta_niif_liquidacion']."',
										'".$arrayConceptosResul['naturaleza']."',
										'".$arrayConceptosResul['imprimir_volante']."',
										'".$arrayConceptosResul['saldo_dias_laborados']."'
										),";
					}

				}// FIN SEGUNDO FOR EACH
			} // FIN PRIMER FOR EACH

			$valueInsert = substr($valueInsert, 0, -1);
			$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos
					(id_planilla,
					id_empleado,
					id_contrato,
					id_concepto,
					codigo_concepto,
					concepto,
					valor_concepto,
					valor_concepto_ajustado,
					saldo_restante,
					base,
					formula,
					formula_original,
					nivel_formula,
					id_sucursal,
					id_empresa,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion,
					naturaleza,
					imprimir_volante,
					dias_laborados)
					VALUES $valueInsert";
			$query=mysql_query($sql,$link);

			// CARGAR PRESTAMOS DEL EMPLEADO
			cargaPrestamosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_sucursal,$id_empresa,$link);

			echo '<script>
					//AGREGAR EL EMPLEADO A LA PLANILLA DE NOMINA
					var div=document.createElement("div");
					div.setAttribute("class","bodyDivNominaPlanilla  claseBuscar");
					div.innerHTML="<div class=\"campo\" id=\"divLoadEmpleado_'.$id_contrato.'\">"+contEmpleados+"</div>"+
									"<div class=\"campo\" style=\"margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;\" id=\"fila_selected_'.$id_contrato.'\"><img src=\"img/fila_selected.png\"></div>"+
                    				"<div class=\"campo1\" onclick=\"cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');\" style=\"width:100px;text-indent:5px;\">'.$documento_empleado.'</div>"+
                    				"<div class=\"campo1\" onclick=\"cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');\" style=\"width:calc(100% - 100px - 49px - 20px);text-indent:5px;\">'.$nombre_empleado.'</div>"+
                    				"<div onclick=\"verificaEmpleado('.$id_contrato.','.$id_empleado.','.$id_contrato.')\"  title=\"Verificar Empleado\" class=\"iconBuscar\" style=\"margin-left: -1px;\" >"+
                            		    "<img class=\"capturaImgCheck\" src=\"img/checkbox_false.png\" value=\"false\" id=\"verifica_empleado_'.$id_contrato.'\">"+
                            		"</div>"+
                    				"<div onclick=\"eliminarEmpleado('.$id_contrato.','.$id_empleado.')\" title=\"Eliminar Empleado\" class=\"iconBuscar\" style=\"margin-left: -1px;\">"+
                    				    "<img src=\"img/delete.png\">"+
                    				"</div>";
                    document.getElementById("contenedorEmpleados").appendChild(div);
                    contEmpleados++;
                    //ELIMINAR EL EMPLEADO DE LA GRILLA DE BUSQUEDA
					document.getElementById("item_buscarEmpleadosPlanilla_'.$id_contrato.'").parentNode.removeChild(document.getElementById("item_buscarEmpleadosPlanilla_'.$id_contrato.'"));
					calcularValoresPlanilla();
				</script>
				';
		}
		else{
			echo '<script>
					//QUITAR EL TEXT-IDENT DEL NUMERO DE LA FILA DE LA GRILLA Y MOSTRAR EL NUMERO
					document.getElementById("MuestraToltip_buscarEmpleadosPlanilla_'.$id_contrato.'").style.textIndent="0px";
					document.getElementById("MuestraToltip_buscarEmpleadosPlanilla_'.$id_contrato.'").innerHTML="'.$id_contrato.'";
					alert("Error\nNo se agrego el empleado a la planilla de nomina, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>';
		}
	}

	function eliminarEmpleado($cont,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){
		//ELIMINAR LA RELACION DE LOS CONCEPTOS DEDUCCION
		$sql="DELETE FROM nomina_planillas_liquidacion_conceptos_deducir WHERE
				activo=1 AND
				id_planilla='$id_planilla' AND
				id_empleado='$id_empleado' AND
				id_empresa='$id_empresa'";
		$query=mysql_query($sql,$link);

		//ELIMINAR LOS CONCEPTOS DEL EMPLEADO
		$sql="DELETE FROM nomina_planillas_liquidacion_empleados_conceptos WHERE
				activo=1 AND
				id_planilla='$id_planilla' AND
				id_empleado='$id_empleado' AND
				id_contrato='$id_contrato' AND
				id_empresa='$id_empresa'";
		$query=mysql_query($sql,$link);
		if ($query) {
			//ELIMINAR EL EMPLEADO DE LA PLANILLA
			$sql="DELETE FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND
					id_planilla = '$id_planilla' AND
					id_empleado = '$id_empleado' AND
					id_contrato = '$id_contrato' AND
					id_empresa = '$id_empresa'";
			$query=mysql_query($sql,$link);
			if ($query) {

				// ACTUALIZAR LA COLUMNA ID PLANILLA LIQUIDACION DE LAS FILAS DE LOS CONCEPTOS DE LAS PLANILLAS EN ESE PERIODO DE TIEMPO
				$sql="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=0, tipo_planilla_cruce=''
						WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla_cruce=$id_planilla ";
				$query=mysql_query($sql,$link);

				// ELIMINAR LIBRO DE VACACIONES SI FUE CREADO
				$sql="DELETE FROM nomina_vacaciones_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla";
				$query=mysql_query($sql,$link);

				//ELIMINAR EL NODO DEL DOM
				echo '<script>
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
						// console.log(document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode);
						// console.log("document.getElementById(\"divLoadEmpleado_'.$id_contrato.'\").parentNode.removeChild(document.getElementById(\"divLoadEmpleado_'.$id_contrato.'\"))");
						document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode.parentNode.removeChild(document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode);
						calcularValoresPlanilla();
					</script>';
			}
			else{
				echo '<script>
					alert("Error\nSe eliminaron los conceptos pero no el empleado, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>'.$cont;
			}
		}
		else{
			echo '<script>
					alert("Error\nNo se eliminaron los conceptos del empleado, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>'.$cont;
		}
	}

	function cargarTodosEmpleados($id_planilla,$id_empresa,$sucursal,$link){
		$fecha = date("Y-m-d");

		// $tiempo_inicio = microtime_float();


		//CONSULTAR LOS DIAS DE LIQUIDACION DE LA PLANILLA
		$sql="SELECT dias_liquidacion,fecha_inicio,fecha_final,id_sucursal  FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$dias_liquidacion = mysql_result($query,0,'dias_liquidacion');
		$fecha_inicio     = mysql_result($query,0,'fecha_inicio');
		$fecha_final      = mysql_result($query,0,'fecha_final');
		$id_sucursal      = mysql_result($query,0,'id_sucursal');

		$bodyEmpleados = '';
		//SELECCIONAR LOS EMPLEADOS QUE ESTAN EN LA PLANILLA
		$sql="SELECT id_contrato,id_empleado,nombre_empleado,documento_empleado FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query=mysql_query($sql,$link);
		$whereIdEmpleados='';
		$whereIdEmpleadosSucursal='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdEmpleados.=' AND id_empleado<>'.$row['id_empleado'].' ';
			$whereIdEmpleadosSucursal.=' AND id<>'.$row['id_empleado'].' ';
		}

		// CONSULTAR LA SUCURSAL DEL EMPLEADO
		$sqlEmpleadoSucursal="SELECT id,id_sucursal,documento,nombre FROM empleados WHERE activo=1 AND id_empresa=$id_empresa  $whereIdEmpleadosSucursal ";
		$queryEmpleadoSucursal=mysql_query($sqlEmpleadoSucursal,$link);
		while ($row=mysql_fetch_array($queryEmpleadoSucursal)) {
			$arraySucursalEmpleado[$row['id']]=$row['id_sucursal'];
			$arrayDatosEmpleados[$row['id']]=array('documento'=>$row['documento'],'nombre'=>$row['nombre']);
		}

		// CONSULTAR TODOS LOS TIPOS DE CONTRATO
		$sql="SELECT id,dias FROM nomina_tipo_contrato WHERE activo=1 AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$arrayTiposContrato[$row['id']]=$row['dias'];
		}

		//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
		$sql="SELECT id,
					id_empleado,
					tipo_documento_empleado,
					documento_empleado,
					nombre_empleado,
					numero_contrato,
					salario_basico,
					fecha_inicio_nomina,
					id_grupo_trabajo,
					valor_nivel_riesgo_laboral,
					fecha_fin_contrato,
					IF(fecha_fin_contrato <= '$fecha', 'Si', 'No') AS terminar_contrato,
					id_sucursal,
					id_tipo_contrato
				FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_sucursal=$sucursal AND fecha_inicio_nomina<='$fecha_final' AND documento_empleado<>'' $whereIdEmpleados";
		$query=mysql_query($sql,$link);

		$valueInsertEmpleados  = '';
		$whereInsertEmpleados  = '';
		$whereDeleteEmpleados  = '';
		$whereId_grupo_trabajo = '';

		while ($row=mysql_fetch_array($query)) {
			$whereInsertEmpleados  .= ($whereInsertEmpleados=='')? ' E.id='.$row['id_empleado'] : ' OR E.id='.$row['id_empleado'] ;
			$whereDeleteEmpleados  .= ($whereDeleteEmpleados=='')? ' id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
			$whereId_grupo_trabajo .= ($whereId_grupo_trabajo=='')? ' id_grupo_trabajo='.$row['id_grupo_trabajo'] : ' OR id_grupo_trabajo='.$row['id_grupo_trabajo'] ;
			// $whereIdEmpleadosProvision.=($whereIdEmpleadosProvision=='')? ' NPE.id_empleado='.$row['id_empleado']  : ' OR NPE.id_empleado='.$row['id_empleado'];
			$arrayIdEmpleados[$row['id_empleado']]=$row['id_empleado'];

			$arrayEmpleados[$row['id_empleado']]         = $row['id_grupo_trabajo'];
			$arrayEmpleadosValores[$row['id_empleado']]  = array(	'salario_basico' 			 => $row['salario_basico'],
																	'id_contrato'                => $row['id'],
																	'id_sucursal'                => $row['id_sucursal'],
																	'valor_nivel_riesgo_laboral' => $row['valor_nivel_riesgo_laboral']);

			// echo $row['nombre_empleado'].' - '.$arrayTiposContrato[$row['id_tipo_contrato']].' - '.$row['id_tipo_contrato'].'<br>';
			if ($arrayTiposContrato[$row['id_tipo_contrato']]==0) {
				$terminar_contrato='No';
			}
			else{
				$terminar_contrato=(compararFechas($row['fecha_fin_contrato'],$fecha)<0)? 'Si' : 'No' ;
			}

			$fecha_finalizacion_contrato = ($terminar_contrato=='Si')? "{fecha_finalizacion_contrato_$row[id_empleado]}" : '0' ;

			$valueInsertEmpleados.="('$id_planilla',
									 '$row[id_empleado]',
									 '$row[tipo_documento_empleado]',
									 '$row[documento_empleado]',
									 '$row[nombre_empleado]',
									 '$row[id]',
									 '".$arrayInfoNomina[$row['id_empleado']]['dias_laborados']."',
									 '$terminar_contrato',
									 '".$fecha_finalizacion_contrato."',
									 '".$arraySucursalEmpleado[$row['id_empleado']]."',
									 '$id_empresa'
									 ),";
		}

		foreach ($arrayIdEmpleados as $id_empleado => $value) {
			$whereIdEmpleadosProvision.=($whereIdEmpleadosProvision=='')? ' NPE.id_empleado='.$id_empleado  : ' OR NPE.id_empleado='.$id_empleado;
		}

		// CONSULTAR SI EL EMPLEADO TIENE PLANILLAS DE LQUIDACION CREADAS EN ESE PERIODO DE TIEMPO PARA EXCLUIR ESAS FECHAS DE LA BASE DEL CONCEPTO
		$sql="SELECT
					NP.id,
					NP.consecutivo,
					NPE.dias_laborados,
					NPE.id_empleado,
					NPE.id_concepto,
					NPE.concepto,
					NPE.valor_concepto,
					NPE.valor_concepto_ajustado,
					NPE.naturaleza,
					NPE.base
				FROM
					nomina_planillas_liquidacion AS NP,
					nomina_planillas_liquidacion_empleados_conceptos AS NPE
				WHERE
					NP.activo = 1
				AND NP.estado       = 1
				AND NP.id_empresa   = $id_empresa
				AND NP.fecha_final BETWEEN '$fecha_inicio'
				AND  '$fecha_final'
				AND NPE.id_planilla = NP.id
				AND ($whereIdEmpleadosProvision)
				GROUP BY NP.id,NPE.id_concepto,NPE.id_empleado";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			// $whereIdLiquidacionPlanillas.= ($whereIdLiquidacionPlanillas=='')? ' id='.$row['id'] : ' OR id='.$row['id'] ;
			if ($arrayConceptosLiquidados[$row['id_empleado']][$row['id_concepto']]['valor_concepto']>0){
				$arrayConceptosLiquidados[$row['id_empleado']][$row['id_concepto']]['valor_concepto'] +=($row['naturaleza']=='Provision')? $row['valor_concepto_ajustado'] : $row['valor_concepto'] ;
				$arrayConceptosLiquidados[$row['id_empleado']][$row['id_concepto']]['base']           +=$row['base'] ;
			}
			else{
				$arrayConceptosLiquidados[$row['id_empleado']][$row['id_concepto']] = array('saldo_dias_laborados' => $row['dias_laborados'],
																							'base' => $row['base'],
																							'valor_concepto' => ( ($row['naturaleza']=='Provision')? $row['valor_concepto_ajustado'] : $row['valor_concepto']) );
			}

		}


		// CONSULTAR LAS PLANILLAS DE LAS PROVISIONES DE LOS EMPLEADOS
		$sql="SELECT
					NP.id,
					NP.fecha_final,
					NPE.dias_laborados,
					NPE.id_empleado
				FROM
					nomina_planillas AS NP,
					nomina_planillas_empleados AS NPE
				WHERE
					NP.activo = 1
				AND (NP.estado=1 OR NP.estado=2)
				AND NP.id_empresa = $id_empresa
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final <= '$fecha_final'
				AND NPE.id_planilla = NP.id
				AND ($whereIdEmpleadosProvision)
				GROUP BY NP.id,NPE.id_empleado
				ORDER BY fecha_final DESC";
		$query=mysql_query($sql,$link);
		$whereIdPlanillas='';
		while ($row=mysql_fetch_array($query)) {
			// $whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			$arrayInfoNomina[$row['id_empleado']] += $row['dias_laborados'];
			$arrayIdPlanillas[$row['id']]         =  $row['id'];

			$valueInsertEmpleados = str_replace("{fecha_finalizacion_contrato_$row[id_empleado]}", "$row[fecha_final]", $valueInsertEmpleados);

		}

		$id_planilla_load='';
		foreach ($arrayIdPlanillas as $id_planilla_load => $value) {
			$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$id_planilla_load : ' OR id_planilla='.$id_planilla_load  ;
		}

		$tiempo_inicio = microtime_float();

		// REEMPLAZAR LA VARIABLE DE LOS ID QUITANDO NPE. POR ''
		$whereIdEmpleadosProvision=str_replace('NPE.', '', $whereIdEmpleadosProvision);

		// ACTUALIZAR EL ID DE LA PLANILLA DE LIQUIDACION
		$sql="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=$id_planilla, tipo_planilla_cruce='LE'
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla_cruce=0  AND ($whereIdPlanillas) AND ($whereIdEmpleadosProvision)";
		$query=mysql_query($sql,$link);

		$tiempo_fin    = microtime_float();

		// CONSULTAR EL LIBRO DE VACACIONES PARA AGREGAR EL VALOR DE LAS VACACIONES PAGADAS A LA BASE
		$sql   = "SELECT
						id_empleado,
						SUM(valor_vacaciones_disfrutadas) AS saldo_vacaciones
					FROM nomina_vacaciones_empleados
					WHERE
						activo=1
					AND id_empresa=$id_empresa
					AND ($whereIdEmpleadosProvision)
					AND fecha_inicio_vacaciones_disfrutadas >= '$fecha_inicio'
					AND fecha_inicio_vacaciones_disfrutadas  <= '$fecha_final'
					GROUP BY id_empleado";
		$query = mysql_query($sql,$link);
		while ($row = mysql_fetch_array($query) ) {
			$arraySaldoVacaciones[$row['id_empleado']] = $row['saldo_vacaciones'];
		}

		// $saldo_vacaciones = mysql_result($query,0,'saldo_vacaciones');

		//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE LOS EMPLEADOS
		$sql="SELECT
				id_concepto,
				codigo_concepto,
				concepto,
				id_empleado,
				SUM(valor_concepto) AS valor_provisionado,
				SUM(saldo_dias_laborados) AS saldo_dias_laborados,
				naturaleza,
				id_planilla_cruce
				FROM
					nomina_planillas_empleados_conceptos
				WHERE
					activo = 1
				AND ($whereIdEmpleadosProvision)
				AND id_empresa = $id_empresa
				AND ($whereIdPlanillas)
				AND saldo_dias_laborados>0
				GROUP BY
				id_tercero,
				id_concepto";
		$query=mysql_query($sql,$link);
		$whereIdConceptos='';
		while ($row=mysql_fetch_array($query)) {

			// SI EL CONCEPTO ES PROVISION Y ESTA ASIGNADO A OTRA PLANILLA DE LIQUIDACION ENTONCES NO SE PONE
			if ($row['id_planilla_cruce']<>$id_planilla && $row['naturaleza']=='Provision') { continue; }

			if ($row['naturaleza']=='Provision'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_empleado']][$row['id_concepto']] = $row['valor_provisionado'];
				$arrayDiasConceptos[$row['id_empleado']][$row['id_concepto']]    = $row['saldo_dias_laborados'];
			}

			$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']] = array(
																						'codigo'               => $row['codigo_concepto'],
																						'concepto'             => $row['concepto'],
																						'valor_concepto'       => $row['valor_provisionado'],
																						'saldo_dias_laborados' => $row['saldo_dias_laborados'],
																						'naturaleza'           => $row['naturaleza'],
																						'base'                 => $arraySaldoVacaciones[$row['id_empleado']],
																						);

		}


		$valueInsertEmpleados = substr($valueInsertEmpleados, 0, -1);
		// VALIDAR SI HAY EMPLEADOS A INSERTAR
		if ($valueInsertEmpleados=='') {
			echo '<script>
						alert("Aviso!\nNo hay empleados ha cargar");
						Win_Ventana_buscar_empleados.close();
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
				</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);
			return;
		}
		//INSERTAR EL EMPLEADO EN LA PLANILLA
		$sql="INSERT INTO nomina_planillas_liquidacion_empleados (id_planilla,id_empleado,tipo_documento,documento_empleado,nombre_empleado,id_contrato,dias_laborados,terminar_contrato,fecha_fin_contrato,id_sucursal,id_empresa)
				VALUES $valueInsertEmpleados ";
		$query=mysql_query($sql,$link);
		if ($query) {

			// CONSULTAR LOS CONCEPTOS PARA LA BASE DE LA LIQUIDACION
			$whereIdConceptosLiquidacion = str_replace("id", "id_concepto", $whereIdConceptos);
			$sql="SELECT * FROM nomina_conceptos_base_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdConceptosLiquidacion)";
			$query = mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				if ($row['naturaleza_base']=='Devengo'){
					foreach ($arrayConceptosAcumulados as $id_empleado => $arrayConceptosAcumuladosArray) {
						// foreach ($arrayConceptosAcumuladosArray as $id_concepto => $arrayConceptosAcumuladosResul) {
							if (isset($arrayConceptosAcumulados[$id_empleado][$row['id_concepto']])) {
								$arrayConceptosAcumulados[$id_empleado][$row['id_concepto']]['base'] += $arrayConceptosAcumulados[$id_empleado][$row['id_concepto_base']]['valor_concepto'];
								// echo '<br>'.$arrayConceptosAcumulados[$id_empleado][$row['id_concepto_base']]['concepto'].':'.$arrayConceptosAcumulados[$id_empleado][$row['id_concepto']]['base'].' - '.$arrayConceptosAcumulados[$id_empleado][$row['id_concepto_base']]['valor_concepto'];
							}

						// }
					}

				}
				else if ($row['naturaleza_base']=='Deduccion') {
					foreach ($arrayConceptosAcumulados as $id_empleado => $arrayConceptosAcumuladosArray) {
						// foreach ($arrayConceptosAcumuladosArray as $id_concepto => $arrayConceptosAcumuladosResul) {
							if (isset($arrayConceptosAcumulados[$id_empleado][$row['id_concepto']])) {
								$arrayConceptosAcumulados[$id_empleado][$row['id_concepto']]['base'] -= $arrayConceptosAcumulados[$id_empleado][$row['id_concepto_base']]['valor_concepto'];
							}

						// }
					}

				}

			}

			// print_r($arrayConceptosAcumulados);

			//CONSULTAR TODOS LOS CONCEPTOS CON CARGA AUTOMATICA DE LA BASE DE DATOS
			$sql="SELECT id,
						codigo,
						descripcion,
						formula_liquidacion,
						nivel_formula_liquidacion,
						tipo_concepto,
						id_cuenta_colgaap,
						cuenta_colgaap,
						descripcion_cuenta_colgaap,
						id_cuenta_niif,
						cuenta_niif,
						descripcion_cuenta_niif,
						caracter,
						centro_costos,
						id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap,
						descripcion_cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif,
						descripcion_cuenta_contrapartida_niif,
						caracter_contrapartida,
						centro_costos_contrapartida,
						naturaleza,
						imprimir_volante,
						id_cuenta_colgaap_liquidacion,
						cuenta_colgaap_liquidacion,
						descripcion_cuenta_colgaap_liquidacion,
						id_cuenta_niif_liquidacion,
						cuenta_niif_liquidacion,
						descripcion_cuenta_niif_liquidacion
					FROM nomina_conceptos
					WHERE
						activo=1
						AND id_empresa=$id_empresa
						AND naturaleza='Provision'
						AND ($whereIdConceptos)
						ORDER BY nivel_formula ASC";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id=$row['id'];
				$nivel_formula=$row['nivel_formula_liquidacion'];
				$arrayConceptos[$nivel_formula][$id] = array(
															'codigo'                                   => $row['codigo'],
															'concepto'                                 => $row['descripcion'],
															'formula'                                  => $row['formula_liquidacion'],
															'formula_original'                         => $row['formula_liquidacion'],
															'nivel_formula'                            => $row['nivel_formula_liquidacion'],
															'valor_concepto'                           => 0,
															'insert'                                   => 'false',
															'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
															'cuenta_colgaap'                           => $row['cuenta_colgaap'],
															'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
															'id_cuenta_niif'                           => $row['id_cuenta_niif'],
															'cuenta_niif'                              => $row['cuenta_niif'],
															'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
															'caracter'                                 => $row['caracter'],
															'centro_costos'                            => $row['centro_costos'],
															'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
															'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
															'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
															'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
															'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
															'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
															'caracter_contrapartida'                   => $row['caracter_contrapartida'],
															'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
															'naturaleza'                               => $row['naturaleza'],
															'imprimir_volante'                         => $row['imprimir_volante'],
															'id_cuenta_colgaap_liquidacion'            => $row['id_cuenta_colgaap_liquidacion'],
															'cuenta_colgaap_liquidacion'               => $row['cuenta_colgaap_liquidacion'],
															'descripcion_cuenta_colgaap_liquidacion'   => $row['descripcion_cuenta_colgaap_liquidacion'],
															'id_cuenta_niif_liquidacion'               => $row['id_cuenta_niif_liquidacion'],
															'cuenta_niif_liquidacion'                  => $row['cuenta_niif_liquidacion'],
															'descripcion_cuenta_niif_liquidacion'      => $row['descripcion_cuenta_niif_liquidacion'],
															'saldo_dias_laborados'                     => '',
														);
			}


			// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
			$sql="SELECT id_concepto,
						nivel_formula_liquidacion,
						formula_liquidacion,
						id_cuenta_colgaap,
						cuenta_colgaap,
						descripcion_cuenta_colgaap,
						id_cuenta_niif,
						cuenta_niif,
						descripcion_cuenta_niif,
						caracter,
						centro_costos,
						id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap,
						descripcion_cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif,
						descripcion_cuenta_contrapartida_niif,
						caracter_contrapartida,
						centro_costos_contrapartida,
						id_grupo_trabajo,
						id_cuenta_colgaap_liquidacion,
						cuenta_colgaap_liquidacion,
						descripcion_cuenta_colgaap_liquidacion,
						id_cuenta_niif_liquidacion,
						cuenta_niif_liquidacion,
						descripcion_cuenta_niif_liquidacion
						FROM nomina_conceptos_grupos_trabajo
						WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId_grupo_trabajo)";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id               = $row['id_concepto'];
				$id_grupo_trabajo = $row['id_grupo_trabajo'];
				$arrayGruposTrabajo[$id_grupo_trabajo][$id]=array(
																'nivel_formula_liquidacion'                => $row['nivel_formula_liquidacion'],
																'formula'                                  => $row['formula_liquidacion'],
																'formula_original'                         => $row['formula_liquidacion'],
																'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
																'cuenta_colgaap'                           => $row['cuenta_colgaap'],
																'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
																'id_cuenta_niif'                           => $row['id_cuenta_niif'],
																'cuenta_niif'                              => $row['cuenta_niif'],
																'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
																'caracter'                                 => $row['caracter'],
																'centro_costos'                            => $row['centro_costos'],
																'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
																'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
																'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
																'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
																'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
																'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
																'caracter_contrapartida'                   => $row['caracter_contrapartida'],
																'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
																'id_cuenta_colgaap_liquidacion'            => $row['id_cuenta_colgaap_liquidacion'],
																'cuenta_colgaap_liquidacion'               => $row['cuenta_colgaap_liquidacion'],
																'descripcion_cuenta_colgaap_liquidacion'   => $row['descripcion_cuenta_colgaap_liquidacion'],
																'id_cuenta_niif_liquidacion'               => $row['id_cuenta_niif_liquidacion'],
																'cuenta_niif_liquidacion'                  => $row['cuenta_niif_liquidacion'],
																'descripcion_cuenta_niif_liquidacion'      => $row['descripcion_cuenta_niif_liquidacion'],
																);
			}

			// RECORER EL ARRAY PARA ORGANIZAR LOS CONCEPTOS DE CADA EMPLEADO
			foreach ($arrayEmpleados as $id_empleado => $id_grupo_trabajo) {
				//CREAR ARRAY TEMPORAL PARA GUARDAR TODOS LOS CONCEPTOS DE CARGA AUTOMATICA
				$arrayTempConceptos=$arrayConceptos;
				// RECORRER EL ARRAY TEMPORAL PARA REEMPLAZAR LOS VALORES DE CUENTAS Y FORMULAS POR EL QUE CORRESPONDE AL GRUPO DE TRABAJO, SI EL GRUPO DE TRABAJO NO TIENE NADA CONFIGURADO, ENTONCES SE PONE LA CONFIGURACION INICIAL DEL CONCEPTO
				foreach ($arrayTempConceptos as $nivel_formula => $arrayTempConceptosArray) {
					foreach ($arrayTempConceptosArray as $id_concepto => $arrayTempConceptosResul) {
						// SI LOS VALORES DEL ARRAY EN LOS INDICES NO EXISTEN, ENTONCES SE HACE CONTINUE PARA QUE NO ELIMINE LOS VALORES ANTERIORES DEL ARRAY
						if (!isset($arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto])) {
							continue;
						}

						$formula=$arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['formula'];

						// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
						$arrayTempConceptos[$nivel_formula][$id_concepto]['formula']                                  = ($formula=='')? $arrayTempConceptos[$nivel_formula][$id_concepto]['formula'] : $formula;
						$arrayTempConceptos[$nivel_formula][$id_concepto]['formula_original']                         = ($formula=='')? $arrayTempConceptos[$nivel_formula][$id_concepto]['formula_original'] : $formula;
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_colgaap']                        = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_colgaap']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap']               = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_niif']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_niif']                              = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_niif']                  = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['caracter']                                 = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['caracter'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['centro_costos']                            = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap']          = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap']             = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'] = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif']             = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_contrapartida_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_niif']                = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_contrapartida_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif']    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_contrapartida_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['caracter_contrapartida']                   = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['caracter_contrapartida'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['centro_costos_contrapartida']              = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos_contrapartida'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_colgaap_liquidacion']            = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_colgaap_liquidacion'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_colgaap_liquidacion']               = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_colgaap_liquidacion'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap_liquidacion']   = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_colgaap_liquidacion'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_niif_liquidacion']               = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_niif_liquidacion'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_niif_liquidacion']                  = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_niif_liquidacion'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_niif_liquidacion']      = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_niif_liquidacion'];
					}
				}
				// ASIGNAR EL VALOR DEL ARRAY TEMPORAL AL ARRAY FINAL DEL EMPLEADO
				$arrayEmpleadosConceptos[$id_empleado]=$arrayTempConceptos;
			}
			// print_r($arrayEmpleadosConceptos);

			// --------------------------------------------------------- //
			// RECORRER EL ARRAY PARA CALCULAR EL VALOR CON LAS FORMULAS //
			// --------------------------------------------------------- //
			$valueInsert='';
			$nivel=0;

			// PRIMER CAPA, EMPLEADOS
			foreach ($arrayEmpleadosConceptos as $id_empleado => $arrayEmpleadosConceptosArray) {
				// CARGAR PRESTAMOS DEL EMPLEADO
				cargaPrestamosEmpleado($id_planilla,$id_empleado,$arrayEmpleadosValores[$id_empleado]['id_contrato'],$id_sucursal,$id_empresa,$link);

				// RECORRER LOS NIVELES
				foreach ($arrayEmpleadosConceptosArray as $nivel_formula => $arrayEmpleadosConceptosArrayResul) {

					// RECORRER LOS CONCEPTOS
					foreach ($arrayEmpleadosConceptosArrayResul as $id_concepto => $arrayConceptosResul) {

						// -----------------------------------//
						// REEMPLAZAR LAS VARIABLES GENERALES //
						// -----------------------------------//

						// BASE DEL VALOR GANADO
						$base_concepto=($arrayConceptosAcumulados[$id_empleado][$id_concepto]['base']/$arrayConceptosAcumulados[$id_empleado][$id_concepto]['saldo_dias_laborados'])*30;
						$arrayConceptosResul['formula']=str_replace('{BL}', $base_concepto, $arrayConceptosResul['formula']);
						// DIAS LABORADOS
						$arrayConceptosResul['formula']=str_replace('{DL}', $arrayConceptosAcumulados[$id_empleado][$id_concepto]['saldo_dias_laborados'], $arrayConceptosResul['formula']);

						// RECORRER ARRAY PARA REEMPLAZAR LOS CONCEPTOS DE LA FORMULA
						// NIVEL DE LA FORMULA DEL CONCEPTO
						foreach ($arrayEmpleadosConceptos[$id_empleado] as $nivel_formula_search => $arrayConceptosArray_search) {
							foreach ($arrayConceptosArray_search as $id_concepto_search => $arrayConceptosResul_search) {
								// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
								if($arrayConceptosResul_search['valor_concepto']<0 ){ continue; }

								// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR
								if($nivel_formula<$nivel_formula_search){ continue; }
								// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
								$arrayConceptosResul['formula']=str_replace('['.$arrayConceptosResul_search['codigo'].']', $arrayEmpleadosConceptos[$id_empleado][$nivel_formula_search][$id_concepto_search]['valor_concepto'], $arrayConceptosResul['formula']);
								// echo '<br>'.$arrayConceptosResul['concepto'].' - '.$arrayConceptosResul_search['concepto'];

							}
						}//->FIN FOR EACH PARA BUCAR LOS VALORES DE LOS CONCEPTOS DE LA FORMULA
						//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
						$search_var_concepto=strpos($arrayConceptosResul['formula'], '[');
						if ($search_var_concepto===false) {
							// CALCULAR LA FORMULA
							$valor_concepto=calcula_formula($arrayConceptosResul['formula']);

							// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
							if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
								echo '<script>
										alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: ('.$arrayConceptosResul['formula'].')\nDel empleado:\n'.$arrayDatosEmpleados[$id_empleado]['documento'].' - '.$arrayDatosEmpleados[$id_empleado]['nombre'].'");
										console.log("'.$arrayConceptosResul['formula'].'");
									</script>';
								continue;
							}

							$valor_concepto=$valor_concepto-$arrayConceptosLiquidados[$id_empleado][$id_concepto]['valor_concepto'];
							$base=$arrayConceptosAcumulados[$id_empleado][$id_concepto]['base']-$arrayConceptosLiquidados[$id_empleado][$id_concepto]['base'];
							$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['valor_concepto']=$valor_concepto;

							$valueInsert.="('$id_planilla',
											'$id_empleado',
											'".$arrayEmpleadosValores[$id_empleado]['id_contrato']."',
											'$id_concepto',
											'".$arrayConceptosResul['codigo']."',
											'".$arrayConceptosResul['concepto']."',
											'".$arrayConceptosAcumulados[$id_empleado][$id_concepto]['valor_concepto']."',
											'".$valor_concepto."',
											'".$valor_concepto."',
											'".$base."',
											'".$arrayConceptosResul['formula']."',
											'".$arrayConceptosResul['formula_original']."',
											'".$nivel_formula."',
											'$id_sucursal',
											'$id_empresa',
											'".$arrayConceptosResul['id_cuenta_colgaap']."',
											'".$arrayConceptosResul['cuenta_colgaap']."',
											'".$arrayConceptosResul['descripcion_cuenta_colgaap']."',
											'".$arrayConceptosResul['id_cuenta_niif']."',
											'".$arrayConceptosResul['cuenta_niif']."',
											'".$arrayConceptosResul['descripcion_cuenta_niif']."',
											'".$arrayConceptosResul['caracter']."',
											'".$arrayConceptosResul['centro_costos']."',
											'".$arrayConceptosResul['id_cuenta_contrapartida_colgaap']."',
											'".$arrayConceptosResul['cuenta_contrapartida_colgaap']."',
											'".$arrayConceptosResul['descripcion_cuenta_contrapartida_colgaap']."',
											'".$arrayConceptosResul['id_cuenta_contrapartida_niif']."',
											'".$arrayConceptosResul['cuenta_contrapartida_niif']."',
											'".$arrayConceptosResul['descripcion_cuenta_contrapartida_niif']."',
											'".$arrayConceptosResul['caracter_contrapartida']."',
											'".$arrayConceptosResul['centro_costos_contrapartida']."',
											'".$arrayConceptosResul['id_cuenta_colgaap_liquidacion']."',
											'".$arrayConceptosResul['cuenta_colgaap_liquidacion']."',
											'".$arrayConceptosResul['descripcion_cuenta_colgaap_liquidacion']."',
											'".$arrayConceptosResul['id_cuenta_niif_liquidacion']."',
											'".$arrayConceptosResul['cuenta_niif_liquidacion']."',
											'".$arrayConceptosResul['descripcion_cuenta_niif_liquidacion']."',
											'".$arrayConceptosResul['naturaleza']."',
											'".$arrayConceptosResul['imprimir_volante']."',
											'".$arrayConceptosAcumulados[$id_empleado][$id_concepto]['saldo_dias_laborados']."'
											),";
						}
					}
				}

			}

			// print_r($arrayEmpleadosConceptos);


			$valueInsert = substr($valueInsert, 0, -1);
			$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos
					(id_planilla,
					id_empleado,
					id_contrato,
					id_concepto,
					codigo_concepto,
					concepto,
					valor_concepto,
					valor_concepto_ajustado,
					saldo_restante,
					base,
					formula,
					formula_original,
					nivel_formula,
					id_sucursal,
					id_empresa,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion,
					naturaleza,
					imprimir_volante,
					dias_laborados)
					VALUES $valueInsert";
			$query=mysql_query($sql,$link);

			$tiempo        = $tiempo_fin - $tiempo_inicio;
  			echo "<script>console.log('Tiempo empleado: ".($tiempo_fin - $tiempo_inicio)." ');</script>  ";

			echo '<script>
						Win_Ventana_buscar_empleados.close();
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
						calcularValoresPlanilla();
					</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);



		}
		else{
			echo '<script>
						alert("Error\nNo se agregaron los empleados a la planilla, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
						Win_Ventana_buscar_empleados.close();
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
				</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);
		}
	}

	function renderizarEmpleados($id_planilla,$id_empresa,$link){
		$bodyEmpleados = '';

		$sql="SELECT id_contrato,id_empleado,nombre_empleado,documento_empleado,verificado FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query=mysql_query($sql,$link);
		$whereIdEmpleados='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdEmpleados.=' AND id_empleado<>'.$row['id_empleado'].' ';
			$bodyEmpleados.='<script>
							 	//AGREGAR EL EMPLEADO A LA PLANILLA DE NOMINA
							 	var div=document.createElement("div");
							 	div.setAttribute("class","bodyDivNominaPlanilla claseBuscar");
							 	div.innerHTML="<div class=\"campo\" id=\"divLoadEmpleado_'.$row['id_contrato'].'\">"+contEmpleados+"</div>"+
							 				"<div class=\"campo\" style=\"margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;\" id=\"fila_selected_'.$row['id_contrato'].'\"><img src=\"img/fila_selected.png\"></div>"+
                    		     				"<div class=\"campo1\" onclick=\"cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].');\" style=\"width:100px;\">'.$row['documento_empleado'].'</div>"+
                    		     				"<div class=\"campo1\" onclick=\"cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].');\" style=\"width:calc(100% - 100px - 49px - 20px);\">'.$row['nombre_empleado'].'</div>"+
                    		     				"<div onclick=\"verificaEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$row['id_contrato'].')\"  title=\"Verificar Empleado\" class=\"iconBuscar\" style=\"margin-left: -1px;\" >"+
                                				    "<img class=\"capturaImgCheck\" src=\"img/checkbox_'.$row['verificado'].'.png\" value=\"'.$row['verificado'].'\" id=\"verifica_empleado_'.$row['id_contrato'].'\">"+
                                				"</div>"+
                    		     				"<div onclick=\"eliminarEmpleado('.$row['id_contrato'].','.$row['id_empleado'].')\" title=\"Eliminar Empleado\" class=\"iconBuscar\" style=\"margin-left: -1px;\">"+
                    		     				    "<img src=\"img/delete.png\">"+
                    		     				"</div>";
                    		     document.getElementById("contenedorEmpleados").appendChild(div);
                    		     contEmpleados++;

							 </script>';
		}
		return $bodyEmpleados;
	}

	function cargaPrestamosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_sucursal,$id_empresa,$link){
		$fecha=date("Y-m-d");

		$sql="SELECT fecha_inicio,fecha_final FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$fecha_inicio = mysql_result($query,0,'fecha_inicio');
		$fecha_final  = mysql_result($query,0,'fecha_final');

		$sql="SELECT id,id_concepto,valor_prestamo_restante,id_centro_costos
				FROM nomina_prestamos_empleados
				WHERE activo=1
				AND id_empresa=$id_empresa
				AND id_empleado=$id_empleado
				AND valor_prestamo_restante>0
				AND fecha_inicio_pago BETWEEN '$fecha_inicio' AND '$fecha_final' ";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereIdConceptos.=' AND id='.$row['id_concepto'];
			$arrayPrestamos[$row['id']][$row['id_concepto']]=array('id_prestamo'=>$row['id'],
																	'id_concepto'=>$row['id_concepto'],
																	'valor_cuota'=>$row['valor_prestamo_restante'],
																	'id_centro_costos'=>$row['id_centro_costos']);
		}

		// SI TIENE PRESTAMOS
		if ($whereIdConceptos!='') {
			// CONSULTAR LOS CONCEPTOS DE LOS PRESTAMOS
			$sql="SELECT id,
					codigo,
					descripcion,
					formula,
					nivel_formula,
					tipo_concepto,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					naturaleza,
					imprimir_volante
				FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa $whereIdConceptos  ORDER BY nivel_formula ASC";
			$query=mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$id=$row['id'];
				$arrayConceptos[$id] = array('codigo'           					   => $row['codigo'],
											'concepto'                                 => $row['descripcion'],
											'formula'                                  => $row['formula'],
											'formula_original'                         => $row['formula'],
											'nivel_formula'                            => $row['nivel_formula'],
											'valor_concepto'                           => 0,
											'insert'                                   => 'false',
											'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
											'cuenta_colgaap'                           => $row['cuenta_colgaap'],
											'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
											'id_cuenta_niif'                           => $row['id_cuenta_niif'],
											'cuenta_niif'                              => $row['cuenta_niif'],
											'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
											'caracter'                                 => $row['caracter'],
											'centro_costos'                            => $row['centro_costos'],
											'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
											'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
											'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
											'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
											'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
											'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
											'caracter_contrapartida'                   => $row['caracter_contrapartida'],
											'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
											'naturaleza'                               => $row['naturaleza'],
											'imprimir_volante'                         => $row['imprimir_volante'],
											);
			}

			//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
			$sql   = "SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id=$id_contrato";
			$query = mysql_query($sql,$link);
			$id_grupo_trabajo = mysql_result($query,0,'id_grupo_trabajo');

			// CONSULTAR EL GRUPO DE TRABAJO
			$sql   = "SELECT id_concepto,
							nivel_formula,
							formula,
							id_cuenta_colgaap,
							cuenta_colgaap,
							descripcion_cuenta_colgaap,
							id_cuenta_niif,
							cuenta_niif,
							descripcion_cuenta_niif,
							caracter,
							centro_costos,
							id_cuenta_contrapartida_colgaap,
							cuenta_contrapartida_colgaap,
							descripcion_cuenta_contrapartida_colgaap,
							id_cuenta_contrapartida_niif,
							cuenta_contrapartida_niif,
							descripcion_cuenta_contrapartida_niif,
							caracter_contrapartida,
							centro_costos_contrapartida
						FROM nomina_conceptos_grupos_trabajo
						WHERE activo=1
							AND id_empresa=$id_empresa
							AND id_grupo_trabajo=$id_grupo_trabajo";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$id            = $row['id_concepto'];
				// VALIDAR QUE EL CONCEPTO EXISTA EN EL ARRAY DE LOS CONCEPTOS
				if ($arrayConceptos[$id]['codigo']=='') { continue; }
				// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
				$arrayConceptos[$id]['formula']                                  = ($row['formula']=='')? $arrayConceptos[$id]['formula'] : $row['formula'];
				$arrayConceptos[$id]['formula_original']                         = ($row['formula']=='')? $arrayConceptos[$id]['formula_original'] : $row['formula'];
				$arrayConceptos[$id]['id_cuenta_colgaap']                        = $row['id_cuenta_colgaap'];
				$arrayConceptos[$id]['cuenta_colgaap']                           = $row['cuenta_colgaap'];
				$arrayConceptos[$id]['descripcion_cuenta_colgaap']               = $row['descripcion_cuenta_colgaap'];
				$arrayConceptos[$id]['id_cuenta_niif']                           = $row['id_cuenta_niif'];
				$arrayConceptos[$id]['cuenta_niif']                              = $row['cuenta_niif'];
				$arrayConceptos[$id]['descripcion_cuenta_niif']                  = $row['descripcion_cuenta_niif'];
				$arrayConceptos[$id]['caracter']                                 = $row['caracter'];
				$arrayConceptos[$id]['centro_costos']                            = $row['centro_costos'];
				$arrayConceptos[$id]['id_cuenta_contrapartida_colgaap']          = $row['id_cuenta_contrapartida_colgaap'];
				$arrayConceptos[$id]['cuenta_contrapartida_colgaap']             = $row['cuenta_contrapartida_colgaap'];
				$arrayConceptos[$id]['descripcion_cuenta_contrapartida_colgaap'] = $row['descripcion_cuenta_contrapartida_colgaap'];
				$arrayConceptos[$id]['id_cuenta_contrapartida_niif']             = $row['id_cuenta_contrapartida_niif'];
				$arrayConceptos[$id]['cuenta_contrapartida_niif']                = $row['cuenta_contrapartida_niif'];
				$arrayConceptos[$id]['descripcion_cuenta_contrapartida_niif']    = $row['descripcion_cuenta_contrapartida_niif'];
				$arrayConceptos[$id]['caracter_contrapartida']                   = $row['caracter_contrapartida'];
				$arrayConceptos[$id]['centro_costos_contrapartida']              = $row['centro_costos_contrapartida'];
			}

			$valueInsert='';
			// RECORRER EL ARRAY DE LOS PRESTAMOS PARA ARMAR EL STRING A INSERTAR
			foreach ($arrayPrestamos as $id_prestamo => $arrayPrestamosArray) {
				foreach ($arrayPrestamosArray as $id_concepto => $arrayResul) {
					$valueInsert.="('$id_planilla',
									'$id_empleado',
									'$id_contrato',
									'$id_concepto',
									'".$arrayConceptos[$id_concepto]['codigo']."',
									'".$arrayConceptos[$id_concepto]['concepto']."',
									'".$arrayResul['valor_cuota']."',
									'".$arrayResul['valor_cuota']."',
									'".$arrayConceptos[$id_concepto]['formula']."',
									'".$arrayConceptos[$id_concepto]['formula_original']."',
									'".$nivel_formula."',
									'$id_sucursal',
									'$id_empresa',
									'".$arrayConceptos[$id_concepto]['id_cuenta_colgaap']."',
									'".$arrayConceptos[$id_concepto]['cuenta_colgaap']."',
									'".$arrayConceptos[$id_concepto]['descripcion_cuenta_colgaap']."',
									'".$arrayConceptos[$id_concepto]['id_cuenta_niif']."',
									'".$arrayConceptos[$id_concepto]['cuenta_niif']."',
									'".$arrayConceptos[$id_concepto]['descripcion_cuenta_niif']."',
									'".$arrayConceptos[$id_concepto]['caracter']."',
									'".$arrayConceptos[$id_concepto]['centro_costos']."',
									'".$arrayConceptos[$id_concepto]['id_cuenta_contrapartida_colgaap']."',
									'".$arrayConceptos[$id_concepto]['cuenta_contrapartida_colgaap']."',
									'".$arrayConceptos[$id_concepto]['descripcion_cuenta_contrapartida_colgaap']."',
									'".$arrayConceptos[$id_concepto]['id_cuenta_contrapartida_niif']."',
									'".$arrayConceptos[$id_concepto]['cuenta_contrapartida_niif']."',
									'".$arrayConceptos[$id_concepto]['descripcion_cuenta_contrapartida_niif']."',
									'".$arrayConceptos[$id_concepto]['caracter_contrapartida']."',
									'".$arrayConceptos[$id_concepto]['centro_costos_contrapartida']."',
									'".$arrayConceptos[$id_concepto]['naturaleza']."',
									'".$arrayConceptos[$id_concepto]['imprimir_volante']."',
									'$id_prestamo'
									),";
				}
			}


			$valueInsert = substr($valueInsert, 0, -1);
			if ($valueInsert!='') {
				$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos
								(id_planilla,
								id_empleado,
								id_contrato,
								id_concepto,
								codigo_concepto,
								concepto,
								valor_concepto,
								saldo_restante,
								formula,
								formula_original,
								nivel_formula,
								id_sucursal,
								id_empresa,
								id_cuenta_colgaap,
								cuenta_colgaap,
								descripcion_cuenta_colgaap,
								id_cuenta_niif,
								cuenta_niif,
								descripcion_cuenta_niif,
								caracter,
								centro_costos,
								id_cuenta_contrapartida_colgaap,
								cuenta_contrapartida_colgaap,
								descripcion_cuenta_contrapartida_colgaap,
								id_cuenta_contrapartida_niif,
								cuenta_contrapartida_niif,
								descripcion_cuenta_contrapartida_niif,
								caracter_contrapartida,
								centro_costos_contrapartida,
								naturaleza,
								imprimir_volante,
								id_prestamo)
						VALUES $valueInsert";
				$query=mysql_query($sql,$link);
				if (!$query) {
					echo '<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>';
				}
				else{
					$valueInsert='';
				}
			}
		}
	}

	function cargarConceptosEmpleado($id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){
		//CONSULTAR EL ESTADO DE LA PLANILLA, SI ESTA GENERADA MOSTRAR SOLO COMO INFORMACION
		$sql="SELECT estado,fecha_final FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$estado      =mysql_result($query,0,'estado');
		$fecha_final =mysql_result($query,0,'fecha_final');

		$readonly=($estado==1 || $estado==3 || user_permisos(169)=='false')? 'readonly' : '' ;

		//CONSULTAR LOS DIAS LABORADOS Y SI ES FINALIZACION DEL CONTRATO DEL EMPLEADO
		$sql="SELECT id,dias_laborados, terminar_contrato,id_motivo_fin_contrato,motivo_fin_contrato,nombre_empleado,vacaciones,observaciones,fecha_fin_contrato,provision_vacaciones
				FROM nomina_planillas_liquidacion_empleados
				WHERE
				activo=1
				AND id_empresa=$id_empresa
				AND id_planilla=$id_planilla
				AND id_empleado='$id_empleado'
				AND id_contrato='$id_contrato'";
		$query=mysql_query($sql,$link);

		$dias_laborados         = mysql_result($query,0,'dias_laborados');
		$terminar_contrato      = mysql_result($query,0,'terminar_contrato');
		$vacaciones             = mysql_result($query,0,'vacaciones');
		$id_motivo_fin_contrato = mysql_result($query,0,'id_motivo_fin_contrato');
		$motivo_fin_contrato    = mysql_result($query,0,'motivo_fin_contrato');
		$nombre_empleado        = mysql_result($query,0,'nombre_empleado');
		$observaciones          = mysql_result($query,0,'observaciones');
		$id_registro            = mysql_result($query,0,'id');
		$fecha_fin_contrato     = mysql_result($query,0,'fecha_fin_contrato');
		$provision_vacaciones   = mysql_result($query,0,'provision_vacaciones');

		// CONSULTAR LOS MOTIVOS DE FIN DEL CONTRATO
		$sql="SELECT id,descripcion FROM nomina_motivo_fin_contrato WHERE activo=1 AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		$select_motivo_fin_contrato='<select id="motivo_fin_contrato" onchange="updateMotivoFinContrato(this.value,\''.$id_empleado.'\',\''.$id_contrato.'\')"><option value="">Seleccione...</option>';
		while ($row=mysql_fetch_array($query)) {
			$selected=($id_motivo_fin_contrato==$row['id'])? ' selected ' : '' ;
			$select_motivo_fin_contrato.='<option value="'.$row['id'].'" '.$selected.'>'.$row['descripcion'].'</option>';
		}
		$select_motivo_fin_contrato.='</select>';

		$select_motivo_fin_contrato=($estado==1 || $estado==3)? '<input type="text" readonly value="'.$motivo_fin_contrato.'" id="motivo_fin_contrato">' : $select_motivo_fin_contrato ;

		//CONSULTAR LOS CONCEPTOS DEL EMPLEADO DE LA PLANILLA
		$sql="SELECT NC.*, C.clasificacion 
				FROM nomina_planillas_liquidacion_empleados_conceptos AS NC
				INNER JOIN nomina_conceptos AS C ON C.id = NC.id_concepto
				WHERE NC.activo=1
				AND	NC.id_contrato='$id_contrato'
				AND NC.id_empleado='$id_empleado'
				AND NC.id_planilla='$id_planilla'
				AND NC.id_empresa ='$id_empresa'
				ORDER BY NC.naturaleza ASC";
		$query=mysql_query($sql,$link);

		if ($estado==1 || $estado==3) {
			$script.='document.getElementById("fecha_fin_contrato").value="'.$fecha_fin_contrato.'";';
			$select_contrato = '<input type="text" readonly value="'.$terminar_contrato.'">';
			$select_vacaciones = '<input type="text" readonly value="'.$vacaciones.'">';
			$observacion_empleado = $observaciones;
			$options_dias_laborados = 'readonly';
		}
		else{
			$scriptField='
						new Ext.form.DateField({
						    fieldLabel : "Date from today",     //SI TIENE LABEL
						    format     : "Y-m-d",               //FORMATO
						    width      : 122,                   //ANCHO
						    allowBlank : false,
						    showToday  : false,
						    applyTo    : "fecha_fin_contrato",
						    editable   : false,                 //EDITABLE
						    value      : "'.$fecha_fin_contrato.'",
						    listeners  : { select: function() {  UpdateFechaFinContrato(this.value,'.$id_empleado.') } }
						});
					';
			$script                                     .='document.getElementById("terminar_contrato").value="'.$terminar_contrato.'";document.getElementById("vacaciones").value ="'.$vacaciones.'";';
			$select_contrato                            = '<select id="terminar_contrato" onchange="updateFinalizaContrato(this.value,\''.$id_empleado.'\',\''.$id_contrato.'\')">
                	        																							    <option value="No">No</option>
                	        																							    <option value="Si">Si</option>
                	        																							</select>';
			$select_vacaciones                          = '<select id="vacaciones" onchange="updateVacaciones(this.value,\''.$id_empleado.'\',\''.$id_contrato.'\')">
                	        																							    <option value="No">No</option>
                	        																							    <option value="Si">Si</option>
                	        																							</select>';
			$observacion_empleado                       = '<textarea id="observacionEmpleado" style="width: 100%;height: 100%;padding:0px;" onkeydown="inputObservacionEmpleadoPlanillaLiquidacion(event,this,'.$id_registro.')">'.$observaciones.'</textarea>';
			$options_dias_laborados                     = 'onkeyup="updateDiasLaborados(event,this,\''.$id_empleado.'\',\''.$id_contrato.'\')"';
		}

		$img_provisionamiento = ($provision_vacaciones=='true')? 'checkin' : 'checkout' ;

		$bodyConceptos='<script>
							//LIMPIAR VARIABLES DE CALCULOS
							totalDevengoEmpleado     = 0
							totalDeduccionEmpleado   = 0
							totalApropiacionEmpleado = 0
							totalProvisionEmpleado   = 0
							totalNetoPagarEmpleado   = 0
						</script>

						<style>
							.content-vacation-book{
								background-color : #EDEDED;
								border           : 2px solid #666666;
								padding          : 7px;
								// position      : absolute;
								// margin-top    : -80px;
								text-align       : center;
								width            : 105px;
								box-shadow       : 0 0 5px #888888;
								margin-top       : 35px;
							}
							.chat-bubble-arrow-border{
								border-color : #666666 transparent transparent transparent;
								border-style : solid;
								border-width : 10px;
								height       : 0;
								width        : 0;
								position     : absolute;
							    -webkit-transform: rotate(-180deg);
							    margin-top : -41px;
								// bottom       : -22px;
								// left         : 30px;
							}
							.chat-bubble-arrow{
								border-color : #EDEDED transparent transparent transparent;
								border-style : solid;
								border-width : 7px;
								height       : 0;
								width        : 0;
								position     : absolute;
							    -webkit-transform: rotate(-180deg);
							    transform: rotate(-180deg);
						        margin-top: -34px;
								margin-left: 3px;
								// bottom       : -19px;
								// left         : 30px;
							}
						</style>

						<div style="width:100%; height:35px;text-transform: uppercase;font-weight:bold;font-size:18px;color:#999;text-indent: 10px;line-height:1.5;">
							'.$nombre_empleado .'
						</div>

						<div style="float:left;  width: calc(100% - 50% - 10px);">
	                    	<div class="renglonTop" style="margin-left:10px;float:none;width: 95%;margin-top:5px;min-height:0px;">
	                    	    <div class="labelTop" style="width:45%;float:left;height:20px;border-bottom:0px;border-right:1px solid #d4d4d4;">
	                    	    	Finalizar Contrato

	                    	    </div>
	                    	    <div class="campoTop" style="width:calc(55% - 1px);">
	                    	    	'.$select_contrato.'
	                    	    </div>
	                    	    <div id="divLoadFinalizaContrato" style="width: 20px;height: 18px;position: absolute;margin-left: 250;overflow: hidden;"></div>

	                    	    <div id="div_contenedor_fecha_fin_contrato_label" class="labelTop" style="width:45%;float:left;height:20px;border-bottom:0px;border-right:1px solid #d4d4d4;border-top:1px solid #d4d4d4;">
	                    	    	Fecha
	                    	    </div>
	                    	    <div id="div_contenedor_fecha_fin_contrato_campo" class="campoTop" style="width:calc(55% - 1px);border-top:1px solid #d4d4d4;">
	                    	    	<input type="text" id="fecha_fin_contrato" readonly>
	                    	    </div>
	                    	    <div id="divLoadFechaFinContrato" style="width: 20px;height: 18px;position: absolute;margin-left: 250;margin-top: 25px;overflow: hidden;"></div>

	                    	    <div id="div_contenedor_motivo_fin_contrato_label" class="labelTop" style="width:45%;float:left;height:20px;border-bottom:0px;border-right:1px solid #d4d4d4;border-top:1px solid #d4d4d4;">
	                    	    	Motivo
	                    	    </div>
	                    	    <div id="div_contenedor_motivo_fin_contrato_campo" class="campoTop" style="width:calc(55% - 1px);border-top:1px solid #d4d4d4;">
	                    	    	'.$select_motivo_fin_contrato.'
	                    	    </div>
	                    	    <div id="divLoadMotivoFinContrato" style="width: 20px;height: 18px;position: absolute;margin-left: 250;margin-top: 25px;overflow: hidden;"></div>

	                    	</div>
                    	</div>

                    	<div style="float:left;  width: calc(100% - 50% - 10px);">
	                    	<div class="renglonTop" style="margin-left:10px;float:none;width: 95%;margin-top:5px;min-height:0px;border:none;">
	                    	    <div class="labelTop" style="width:45%;float:left;height:20px;border-bottom:0px;border:1px solid #d4d4d4;">
	                    	    	Vacaciones
	                    	    </div>
	                    	    <div class="campoTop" style="width:calc(55% - 4px);border:1px solid #d4d4d4;border-left:none;">
	                    	    	'.$select_vacaciones.'
	                    	    </div>
	                    	    <div id="divLoadVacaciones" style="width: 20px;height: 18px;position: absolute;margin-left: 250;overflow: hidden;"></div>

	                    	    <div class="labelTop" id="div_contenedor_libro_vacaciones_campo" style="border:1px solid #d4d4d4;width:auto;float:left;height:23px;border-top:none;border-right:none;cursor:hand;" onclick="ventana_libro_vacaciones('.$id_empleado.','.$id_contrato.')">
	                    	    	<div style="float:left;"><img src="img/libro_vacaciones.png"></div><div style="float:left;line-height: 2;padding: 0px 8px 0px 8px;">Libro Vacaciones</div>
	                    	    </div>

	                    	    <div class="labelTop" id="div_contenedor_provision_vacaciones" style="border:1px solid #d4d4d4;width:auto;float:left;height:23px;border-top:none;cursor:hand;" onclick="check_provision('.$id_empleado.','.$id_contrato.')">
	                    	    	<div style="float:left;"><img id="img_provisionamiento" src="img/'.$img_provisionamiento.'.png"></div><div style="float:left;line-height: 2;padding: 0px 7px 0px 7px;"> Provision</div>
	                    	    </div>
	                    	    <div id="divLoadVacaciones" style="width: 20px;height: 18px;position: absolute;margin-left: 250;margin-top: 25px;overflow: hidden;"></div>

	                    	</div>
                    	</div>




                    	<div class="headConceptos" >
	                    	<div class="bodyDivNominaPlanilla" style="border-bottom:none;" id="headConceptos">
	                            <div class="campo" style=""></div>
	                            <div class="campoHeadConceptos" style="width:calc(100% - 50% - 70px - 18px );">Concepto  </div>
	                            <div class="campoHeadConceptos" style="width:30px;">Dias</div>
	                            <div class="campoHeadConceptos" style="width:50px;" title="Dias Adicionales">Dias + </div>
	                            <div class="campoHeadConceptos" style="width:calc(100% - 50% - 30px - 18px - 84px);" title="Valor - Ajuste">Valor - Ajuste</div>
	                            <div class="campoHeadConceptos" style="width:30px;text-align:center;" title="Naturaleza del Concepto">Nat.</div>
	                        </div>
                        </div>
                        <div class="contenedorConceptos" id="contenedorConceptos" onscroll="resizeHeadMyGrilla(this,\'headConceptos\')">
                        ';
        $cont=1;
		while ($row=mysql_fetch_array($query)) {
			$valor_concepto=($row['valor_concepto_ajustado']>0)? $row['valor_concepto_ajustado'] : $row['valor_concepto'] ;
			$divStyleFunction = 'display:none;';
			$divImgFunction   = 'funcion';
			$divTitleFunction   = 'Concepto calculado con formula';

			$btnNEData = "";
			//Configuracion datos de concepto para nomina electronica
			$configTypes = array(
									"cesantias",
									"hora_extra_diurna",
									"hora_extra_nocturna",
									"hora_recargo_nocturno",
									"hora_recargo_diario_dominicales_y_festivas",
									"hora_extra_nocturna_dominicales_y_festivas",
									"hora_recargo_nocturno_dominicales_y_festivas",
									"incapacidad",
									"licencia_maternidad_paternidad",
									"licencia_remunerada",
									"licencia_no_remunerada",
									"licencia_maternidad_paternidad",
									"licencia_remunerada",
									"licencia_no_remunerada",
									"fondo_solidaridad_pensional",
								);
			if (in_array($row['clasificacion'],$configTypes)) {
				$btnNEData = "<div onclick='ventanaConfigurarDatosNELE($cont,\"$row[clasificacion]\")' id='divImageConfiConcepto_$cont' title='Configurar datos nomina electronica' style='width:20px; float:left; margin-top:3px;cursor:pointer;'><img src='../../temas/clasico/images/BotonesTabs/book_open.png'></div>";
			}

			$botones=($estado==1 || $estado==3 )? '<div style="float:left;margin-left:10px; min-width:60px;">
													'.$btnNEData.'
													<div onclick="ventanaConfigurarCuentasConcepto('.$cont.')" id="divImageConfiConcepto_'.$cont.'" title="Ver configuracion" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
													</div>'
													: '<div style="float:left;margin-left:10px; min-width:60px;">
							                        	     						    <div onclick="guardarConcepto('.$cont.',\'actualizarconcepto\')" id="divImageSaveConcepto_'.$cont.'" title="Actualizar Concepto" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/reload.png" id="ImageSaveConcepto_'.$cont.'"></div>
							                        	     						    <div onclick="ventanaConfigurarCuentasConcepto('.$cont.')" id="divImageConfiConcepto_'.$cont.'" title="Configurar Cuentas" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
                        	     						    							'.$btnNEData.'							                        	     						    
							                        	     						    <div onclick="eliminarConcepto('.$cont.')" id="deleteConcepto_'.$cont.'" title="Eliminar Concepto" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
							                        	     						</div>' ;

			$evento_input=($estado==1 || $estado==3  || user_permisos(169)=='false' )? '' : 'onkeyup="validaNumero(event,this)"' ;
			$script.=($row['naturaleza']=='Deduccion' && $estado==0)?
					'document.getElementById("divImageConfiConcepto_'.$cont.'").setAttribute("onclick","ventanaConfigurarConceptoDeduccion('.$cont.','.$row['id_prestamo'].')");
					document.getElementById("divImageConfiConcepto_'.$cont.'").setAttribute("title","Configuracion del concepto");' : '' ;
            // VERIFICAR SI TIENE CONCEPTOS CON FORMULA
            $campoValorConcepto='readonly';
 			if ($row['id_prestamo']>0) {
 				$divStyleFunction = '';
				$divImgFunction   = 'ventas16';
				$divTitleFunction   = 'Prestamo del empleado';
 			}

 			

     		$titlePrint=($row['imprimir_volante']=='true')? 'Imprimible' : 'No Imprimible' ;
     		$inputPS = ($row['codigo_concepto']=='PS')? '<input type="text" id="dias_adicionales_'.$cont.'" '.$evento_input.' '.$readonly.' value="'.$row['dias_adicionales'].'">' : '' ;
     		$camposBody=($row['naturaleza']=='Provision')?
     					'<div style="float:left;width:25px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px">
							'.$row['dias_laborados'].'
						</div>
						<div style="float:left;width:50px;height:100%;border-right:1px solid #d4d4d4;text-align:right;">
							'.$inputPS.'
						</div>
						<input type="text" style="width:calc(100% - 30px - 50px);padding-left: 0px;/*margin-left: -3px;*/" name="valor_concepto_'.$row['id_concepto'].'"  id="valor_concepto_'.$cont.'" value="'.$row['valor_concepto_ajustado'].'" '.$evento_input.' '.$readonly.'>'
     					:
     					'<div style="float:left;width:25px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px">
							'.$row['dias_laborados'].'
						</div>
						<div style="float:left;width:50px;height:100%;border-right:1px solid #d4d4d4;text-align:right;"></div>
						<input type="text" style="width:calc(100% - 30px - 50px);padding-left: 0px;/*margin-left: -3px;*/" name="valor_concepto_'.$row['id_concepto'].'"  id="valor_concepto_'.$cont.'" value="'.$row['valor_concepto'].'" '.$evento_input.' '.$readonly.'>';


			$bodyConceptos.='<div class="bodyDivNominaPlanilla">
                        	     <div class="campo" id="divLoadConcepto_'.$cont.'">'.$cont.'</div>

                        	     <div class="campo1" id="concepto_'.$cont.'" style="width:calc(100% - 50% - 70px - 18px );text-indent:5px;">'.$row['concepto'].'</div>
                        	     <div class="campo" style="'.$divStyleFunction.'border: none;height:21px;width: 13px;margin-top: 1px;margin-left: -15px;background-image:url(img/'.$divImgFunction.'.png);background-repeat:no-repeat;background-color:#FFF;" title="'.$divTitleFunction.'"></div>
                        	     <div class="campo1" style="width:calc(100% - 50% - 50px );text-indent:0;">

                        	     	<input type="text" style="width:50px;border-right:1px solid #d4d4d4;padding-right: 0px;display:none;"  id="input_calculo_'.$cont.'" value="'.$row['dias_laborados'].'" readonly>

                        	     	'.$camposBody.'

                        	     </div>

                        	     <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;" title="'.$row['naturaleza'].'"><img src="img/'.$row['naturaleza'].'.png" ></div>
                        	     <!--<div class="campo1" id="imprimir_volante_'.$cont.'" style="width:30px;text-align:center;text-indent:0px;" title="'.$titlePrint.'"><img src="img/'.$row['imprimir_volante'].'.png"></div>-->
                        	     '.$botones.'
                        	     <input type="hidden" id="id_insert_concepto_'.$cont.'" value="'.$row['id'].'">
                        	     <input type="hidden" id="id_concepto_'.$cont.'" value="'.$row['id_concepto'].'">
                        	     <input type="hidden" id="id_contrato_concepto_'.$cont.'" value="'.$id_contrato.'">
                        	     <input type="hidden" id="id_empleado_concepto_'.$cont.'" value="'.$id_empleado.'">
                        	     <input type="hidden" id="formula_concepto_'.$cont.'" value="'.$row['formula_original'].'">
                        	     <input type="hidden" id="nivel_formula_concepto_'.$cont.'" value="'.$row['nivel_formula'].'">
                        	 </div>

                        	 <script>
                        	 	calculaValoresEmpleado('.$valor_concepto.',"agregar","'.$row['naturaleza'].'");
                        	 	'.$script.'
                        	 </script>
                        	 ';
             $cont++;
		}

		if ($estado==0) {
			$bodyConceptos.='<div class="bodyDivNominaPlanilla">
                        	     <div class="campo" id="divLoadConcepto_'.$cont.'">'.$cont.'</div>
                        	     <div class="campo1" id="concepto_'.$cont.'" style="width:calc(100% - 50% - 70px - 18px );"></div>
                        	     <div class="campo" id="divImgFunction_'.$cont.'" style="display:none;border: none;height:21px;width: 13px;margin-top: 1px;margin-left: -15px;background-image:url(img/funcion.png);background-repeat:no-repeat;background-color:#FFF;" title="Concepto calculado con formula"></div>
                        	     <div id="btnBuscarConcepto_'.$cont.'" onclick="ventanaBuscarConceptos('.$cont.')" title="Buscar Concepto" class="iconBuscar">
            					     <img src="img/buscar20.png">
            					 </div>
                        	     <div class="campo1" style="width:calc(100% - 50% - 50px);text-indent:0;">

                        	     	<div style="float:left;width:25px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px">
									</div>

									<div style="float:left;width:50px;height:100%;border-right:1px solid #d4d4d4;text-align:right;">
									</div>

                        	     	<input type="text" style="width:50px;border-right:1px solid #d4d4d4;padding-right: 0px;display:none;" readonly id="input_calculo_'.$cont.'" >
									<input type="text" style="width:calc(100% - 30px - 50px);padding-left: 0px;/*margin-left: -3px;*/" '.$evento_input.' '.$readonly.' id="valor_concepto_'.$cont.'">
                    	    	</div>
                        	     <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;"></div>
                        	     <!--<div class="campo1" id="imprimir_volante_'.$cont.'" style="width:30px;text-align:center;text-indent:0px;" title=""></div>-->
                        	     <div style="float:left;margin-left:10px; min-width:60px;">
                	     		     <div onclick=" guardarConcepto('.$cont.',\'\')" id="divImageSaveConcepto_'.$cont.'" title="Guardar Concepto" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="ImageSaveConcepto_'.$cont.'"></div>
                	     		     <div onclick="ventanaConfigurarCuentasConcepto('.$cont.')" id="divImageConfiConcepto_'.$cont.'" title="Configurar Cuentas" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/config16.png"></div>
                	     		     <div onclick="eliminarConcepto('.$cont.')" id="deleteConcepto_'.$cont.'" title="Eliminar Concepto" style="width:20px; float:left;display:none; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
                	     		 </div>
                        	     <input type="hidden" id="id_insert_concepto_'.$cont.'" value="">
                	     		 <input type="hidden" id="id_concepto_'.$cont.'" value="">
                	     		 <input type="hidden" id="id_contrato_concepto_'.$cont.'" value="'.$id_contrato.'">
                        	     <input type="hidden" id="id_empleado_concepto_'.$cont.'" value="'.$id_empleado.'">
                        	     <input type="hidden" id="formula_concepto_'.$cont.'" value="">
                        	     <input type="hidden" id="nivel_formula_concepto_'.$cont.'" value="">
                        	 </div>';
		}

		$bodyConceptos.='</div>

							<div class="contenedorConceptos" style="height:auto;margin-top:10px;width: 50%;">
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:50%;">
									<div class="campoHeadConceptos" style="width:22px;border-right:none;"><p style="float:left;" title="Devengo"><img src="img/Devengo.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDevengo"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:50%;" >
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Deduccion"><img src="img/Deduccion.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDeduccion"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);display: none;">
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Apropiacion"><img src="img/Apropiacion.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalApropiacion"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);display: none;">
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Provision"><img src="img/Provision.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalProvision"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;width:100%;float: left;">
									<div class="campoHeadConceptos" style="padding-right:5px;padding-left:5px;text-align: center;background-color : #F3F3F3;">Neto  Empleado</div>
									<div class="campo1" style="width:18px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
									<div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalNetoPagar"></div>
								</div>
							</div>

							<div style="float:left;width:calc(100% - 50% - 34px);">
								<div class="renglonTop" style="margin-left:10px;float:none;width: 100%;margin-top:10px;min-height:0px;">
		                    	    <div class="labelTop" style="width:100px;height:43px !important;float:left;height:20px;border-right:1px solid #d4d4d4;text-align:left;text-indent:7px;border-bottom:none;line-height: 2.5;" id="label_observacion">
		                    	    	Observaciones
		                    	    </div>
		                    	    <div class="campoTop" style="width:calc(100% - 102px);height:43px;">
		                    	    	'.$observacion_empleado.'
		                    	    </div>

		                    	</div>
							</div>

							<script>
									'.$scriptField.'

									if ("'.$terminar_contrato.'"=="Si") {
										document.getElementById("div_contenedor_motivo_fin_contrato_label").style.display="block";
										document.getElementById("div_contenedor_motivo_fin_contrato_campo").style.display="block";
										document.getElementById("div_contenedor_fecha_fin_contrato_label").style.display="block";
										document.getElementById("div_contenedor_fecha_fin_contrato_campo").style.display="block";

									}
									else{
										document.getElementById("div_contenedor_motivo_fin_contrato_label").style.display="none";
										document.getElementById("div_contenedor_motivo_fin_contrato_campo").style.display="none";
										document.getElementById("motivo_fin_contrato").value="";
										document.getElementById("div_contenedor_fecha_fin_contrato_label").style.display="none";
										document.getElementById("div_contenedor_fecha_fin_contrato_campo").style.display="none";
									}

									if ("'.$vacaciones.'"=="Si") {
										// document.getElementById("div_contenedor_libro_vacaciones_label").style.display="block";
										document.getElementById("div_contenedor_libro_vacaciones_campo").style.display="block";
										document.getElementById("div_contenedor_provision_vacaciones").style.display="block";
									}
									else{
										// document.getElementById("div_contenedor_libro_vacaciones_label").style.display="none";
										document.getElementById("div_contenedor_libro_vacaciones_campo").style.display="none";
										document.getElementById("div_contenedor_provision_vacaciones").style.display="none";
										// document.getElementById("motivo_fin_contrato").value="";
									}

									contConceptos='.$cont.';
									'.$script.'
									//RESIZE HEAD
									resizeHeadMyGrilla(document.getElementById("contenedorConceptos"), "headConceptos");
								</script>';

		echo $bodyConceptos;
	}

	function eliminarConcepto($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$naturaleza,$valor_concepto,$id_empresa,$link){
		// VALIDAR QUE OTROS CONCEPTOS NO DEPENDAN DEL CONCEPTO A ELIMINAR
		$sql="SELECT codigo_concepto FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo= 1 AND
																					id_concepto = '$id_concepto' AND
																					id_contrato = '$id_contrato' AND
																					id_empleado = '$id_empleado' AND
																					id_planilla = '$id_planilla' AND
																					id_empresa  = '$id_empresa'";
		$query=mysql_query($sql,$link);
		$codigo_concepto=mysql_result($query,0,'codigo_concepto');

		$sql="SELECT concepto FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo= 1 AND
																					formula_original LIKE '%[$codigo_concepto]%' AND
																					id_contrato = '$id_contrato' AND
																					id_empleado = '$id_empleado' AND
																					id_planilla = '$id_planilla' AND
																					id_empresa  = '$id_empresa'";
		$query=mysql_query($sql,$link);
		$conceptos_resul='';
		while ($row=mysql_fetch_array($query)) {
			$conceptos_resul.='\n -> '.$row['concepto'];
		}
		// exit;
		if ($conceptos_resul!='') {
			echo '<script>alert("Aviso!\nElimine los siguientes conceptos para continuar: '.$conceptos_resul.'");</script>'.$cont;
			exit;
		}

		$sql="DELETE FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1 AND
																id_concepto = '$id_concepto' AND
																id_contrato = '$id_contrato' AND
																id_empleado = '$id_empleado' AND
																id_planilla = '$id_planilla' AND
																id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if ($query) {
			$sql="DELETE FROM nomina_planillas_liquidacion_conceptos_deducir
					WHERE
					activo=1
					AND id_concepto = '$id_concepto'
					AND id_empleado = '$id_empleado'
					AND id_planilla = '$id_planilla'
					AND id_empresa  = '$id_empresa'";
			$query=mysql_query($sql,$link);

			//CONSULTAR EL RANGO DE FECHA DE LA PLANILLA
			// $sql="SELECT fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			// $query=mysql_query($sql,$link);
			// $fecha_inicio = mysql_result($query,0,'fecha_inicio');
			// $fecha_final  = mysql_result($query,0,'fecha_final');
			// $id_sucursal  = mysql_result($query,0,'id_sucursal');

			// //CONSULTAR LAS PLANILLAS DE NOMINA QUE ESTAN DENTRO DEL RANGO DE FECHAS DE LA LIQUIDACION PARA ACTUALIZAR EL CAMPO ID_PLANILLA_cruce
			// $sql="SELECT
			// 			NP.id,
			// 			NPE.dias_laborados,
			// 			NPE.id_empleado
			// 		FROM
			// 			nomina_planillas AS NP,
			// 			nomina_planillas_empleados AS NPE
			// 		WHERE
			// 			NP.activo       = 1
			// 		AND (NP.estado = 1 OR NP.estado=2)
			// 		AND NP.id_empresa   = $id_empresa
			// 		AND NP.fecha_inicio >= '$fecha_inicio'
			// 		AND NP.fecha_final  <= '$fecha_final'
			// 		AND NPE.id_planilla = NP.id
			// 		AND NPE.id_empleado = '$id_empleado'
			// 		GROUP BY NP.id,NPE.id_empleado";
			// $query=mysql_query($sql,$link);
			// $whereIdPlanillas='';
			// while ($row=mysql_fetch_array($query)) {
			// 	$whereIdPlanillas.=($whereIdPlanillas=='')? ' id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			// 	$dias_laborados+=$row['dias_laborados'];
			// }

			// ACTUALIZAR LA COLUMNA ID PLANILLA LIQUIDACION DE LAS FILAS DE LOS CONCEPTOS DE LAS PLANILLAS EN ESE PERIODO DE TIEMPO
			$sql="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=0, tipo_planilla_cruce=''
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_concepto=$id_concepto AND id_planilla_cruce=$id_planilla ";
			$query=mysql_query($sql,$link);

			echo '<script>
					//CALCULAR EL VALOR DEL EMPLEADO
					calculaValoresEmpleado('.$valor_concepto.',"eliminar","'.$naturaleza.'");
					//CALCULAR LOS VALORES DE LA PLANILLA
					calcularValoresPlanilla();
					document.getElementById("divLoadConcepto_'.$cont.'").parentNode.parentNode.removeChild(document.getElementById("divLoadConcepto_'.$cont.'").parentNode);
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se elimino el concepto intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'.$cont;
		}
	}

	function guardarConcepto($input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$id_empresa,$link){
		$valueInsert='';
		// CONSULTAR TODA LA INFORMACION DEL CONCEPTO
		$sql="SELECT
				id AS id_concepto,
				codigo,
				descripcion,
				formula,
				id_cuenta_colgaap,
				cuenta_colgaap,
				descripcion_cuenta_colgaap,
				id_cuenta_niif,
				cuenta_niif,
				descripcion_cuenta_niif,
				caracter,
				centro_costos,
				id_cuenta_contrapartida_colgaap,
				cuenta_contrapartida_colgaap,
				descripcion_cuenta_contrapartida_colgaap,
				id_cuenta_contrapartida_niif,
				cuenta_contrapartida_niif,
				descripcion_cuenta_contrapartida_niif,
				caracter_contrapartida,
				centro_costos_contrapartida,
				naturaleza,
				imprimir_volante,
				nivel_formula,
				id_cuenta_colgaap_liquidacion,
				cuenta_colgaap_liquidacion,
				descripcion_cuenta_colgaap_liquidacion,
				id_cuenta_niif_liquidacion,
				cuenta_niif_liquidacion,
				descripcion_cuenta_niif_liquidacion
			  FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_concepto";
		$query=mysql_query($sql,$link);
		// CREAR ARRAY CON LOS VALORES DEL CONCEPTO
		$arrayConcepto = array(
								'id_concepto'                              => mysql_result($query,0,'id_concepto'),
								'codigo'                                   => mysql_result($query,0,'codigo'),
								'descripcion'                              => mysql_result($query,0,'descripcion'),
								'formula'                                  => mysql_result($query,0,'formula'),
								'id_cuenta_colgaap'                        => mysql_result($query,0,'id_cuenta_colgaap'),
								'cuenta_colgaap'                           => mysql_result($query,0,'cuenta_colgaap'),
								'descripcion_cuenta_colgaap'               => mysql_result($query,0,'descripcion_cuenta_colgaap'),
								'id_cuenta_niif'                           => mysql_result($query,0,'id_cuenta_niif'),
								'cuenta_niif'                              => mysql_result($query,0,'cuenta_niif'),
								'descripcion_cuenta_niif'                  => mysql_result($query,0,'descripcion_cuenta_niif'),
								'caracter'                                 => mysql_result($query,0,'caracter'),
								'centro_costos'                            => mysql_result($query,0,'centro_costos'),
								'id_cuenta_contrapartida_colgaap'          => mysql_result($query,0,'id_cuenta_contrapartida_colgaap'),
								'cuenta_contrapartida_colgaap'             => mysql_result($query,0,'cuenta_contrapartida_colgaap'),
								'descripcion_cuenta_contrapartida_colgaap' => mysql_result($query,0,'descripcion_cuenta_contrapartida_colgaap'),
								'id_cuenta_contrapartida_niif'             => mysql_result($query,0,'id_cuenta_contrapartida_niif'),
								'cuenta_contrapartida_niif'                => mysql_result($query,0,'cuenta_contrapartida_niif'),
								'descripcion_cuenta_contrapartida_niif'    => mysql_result($query,0,'descripcion_cuenta_contrapartida_niif'),
								'caracter_contrapartida'                   => mysql_result($query,0,'caracter_contrapartida'),
								'centro_costos_contrapartida'              => mysql_result($query,0,'centro_costos_contrapartida'),
								'naturaleza'                               => mysql_result($query,0,'naturaleza'),
								'imprimir_volante'                         => mysql_result($query,0,'imprimir_volante'),
								'nivel_formula'                            => mysql_result($query,0,'nivel_formula'),
								'id_cuenta_colgaap_liquidacion'            => mysql_result($query,0,'id_cuenta_colgaap_liquidacion'),
								'cuenta_colgaap_liquidacion'               => mysql_result($query,0,'cuenta_colgaap_liquidacion'),
								'descripcion_cuenta_colgaap_liquidacion'   => mysql_result($query,0,'descripcion_cuenta_colgaap_liquidacion'),
								'id_cuenta_niif_liquidacion'               => mysql_result($query,0,'id_cuenta_niif_liquidacion'),
								'cuenta_niif_liquidacion'                  => mysql_result($query,0,'cuenta_niif_liquidacion'),
								'descripcion_cuenta_niif_liquidacion'      => mysql_result($query,0,'descripcion_cuenta_niif_liquidacion'),
				 			);

		// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA CONFIGURACION DIFERENTE A LA GENERAL
		$sql="SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id=$id_contrato AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		$id_grupo_trabajo=mysql_result($query,0,'id_grupo_trabajo');

		$sql="SELECT
				id_concepto,
				nivel_formula,
				formula,
				id_cuenta_colgaap,
				cuenta_colgaap,
				descripcion_cuenta_colgaap,
				id_cuenta_niif,
				cuenta_niif,
				descripcion_cuenta_niif,
				caracter,
				centro_costos,
				id_cuenta_contrapartida_colgaap,
				cuenta_contrapartida_colgaap,
				descripcion_cuenta_contrapartida_colgaap,
				id_cuenta_contrapartida_niif,
				cuenta_contrapartida_niif,
				descripcion_cuenta_contrapartida_niif,
				caracter_contrapartida,
				centro_costos_contrapartida,
				id_cuenta_colgaap_liquidacion,
				cuenta_colgaap_liquidacion,
				descripcion_cuenta_colgaap_liquidacion,
				id_cuenta_niif_liquidacion,
				cuenta_niif_liquidacion,
				descripcion_cuenta_niif_liquidacion
				FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto AND id_grupo_trabajo=$id_grupo_trabajo";
		$query=mysql_query($sql,$link);

		// SI TIENE CONFIGURACION POR GRUPO DE TRABAJO
		if (mysql_result($query,0,'id_concepto')>0) {
			$formula_query                                             = mysql_result($query,0,'formula');
			$arrayConcepto['id_concepto']                              = mysql_result($query,0,'id_concepto');
			$arrayConcepto['nivel_formula']                            = mysql_result($query,0,'nivel_formula');
			$arrayConcepto['formula']                                  = ($formula_query=='')? $arrayConcepto['formula']  : $formula_query ;
			$arrayConcepto['id_cuenta_colgaap']                        = mysql_result($query,0,'id_cuenta_colgaap');
			$arrayConcepto['cuenta_colgaap']                           = mysql_result($query,0,'cuenta_colgaap');
			$arrayConcepto['descripcion_cuenta_colgaap']               = mysql_result($query,0,'descripcion_cuenta_colgaap');
			$arrayConcepto['id_cuenta_niif']                           = mysql_result($query,0,'id_cuenta_niif');
			$arrayConcepto['cuenta_niif']                              = mysql_result($query,0,'cuenta_niif');
			$arrayConcepto['descripcion_cuenta_niif']                  = mysql_result($query,0,'descripcion_cuenta_niif');
			$arrayConcepto['caracter']                                 = mysql_result($query,0,'caracter');
			$arrayConcepto['centro_costos']                            = mysql_result($query,0,'centro_costos');
			$arrayConcepto['id_cuenta_contrapartida_colgaap']          = mysql_result($query,0,'id_cuenta_contrapartida_colgaap');
			$arrayConcepto['cuenta_contrapartida_colgaap']             = mysql_result($query,0,'cuenta_contrapartida_colgaap');
			$arrayConcepto['descripcion_cuenta_contrapartida_colgaap'] = mysql_result($query,0,'descripcion_cuenta_contrapartida_colgaap');
			$arrayConcepto['id_cuenta_contrapartida_niif']             = mysql_result($query,0,'id_cuenta_contrapartida_niif');
			$arrayConcepto['cuenta_contrapartida_niif']                = mysql_result($query,0,'cuenta_contrapartida_niif');
			$arrayConcepto['descripcion_cuenta_contrapartida_niif']    = mysql_result($query,0,'descripcion_cuenta_contrapartida_niif');
			$arrayConcepto['caracter_contrapartida']                   = mysql_result($query,0,'caracter_contrapartida');
			$arrayConcepto['centro_costos_contrapartida']              = mysql_result($query,0,'centro_costos_contrapartida');
			$arrayConcepto['id_cuenta_colgaap_liquidacion']            = mysql_result($query,0,'id_cuenta_colgaap_liquidacion');
			$arrayConcepto['cuenta_colgaap_liquidacion']               = mysql_result($query,0,'cuenta_colgaap_liquidacion');
			$arrayConcepto['descripcion_cuenta_colgaap_liquidacion']   = mysql_result($query,0,'descripcion_cuenta_colgaap_liquidacion');
			$arrayConcepto['id_cuenta_niif_liquidacion']               = mysql_result($query,0,'id_cuenta_niif_liquidacion');
			$arrayConcepto['cuenta_niif_liquidacion']                  = mysql_result($query,0,'cuenta_niif_liquidacion');
			$arrayConcepto['descripcion_cuenta_niif_liquidacion']      = mysql_result($query,0,'descripcion_cuenta_niif_liquidacion');
		}

		// CREAR LA CADENA DEL INSERT DEL CONCEPTO
		$valueInsert.="('$id_planilla',
						'$id_empleado',
						'$id_contrato',
						'$id_concepto',
						'".$arrayConcepto['codigo']."',
						'".$arrayConcepto['descripcion']."',
						'$valor_concepto',
						'$valor_concepto',
						'".$arrayConcepto['formula']."',
						'".$formula."',
						'$id_empresa',
						'".$arrayConcepto['id_cuenta_colgaap']."',
						'".$arrayConcepto['cuenta_colgaap']."',
						'".$arrayConcepto['descripcion_cuenta_colgaap']."',
						'".$arrayConcepto['id_cuenta_niif']."',
						'".$arrayConcepto['cuenta_niif']."',
						'".$arrayConcepto['descripcion_cuenta_niif']."',
						'".$arrayConcepto['caracter']."',
						'".$arrayConcepto['centro_costos']."',
						'".$arrayConcepto['id_cuenta_contrapartida_colgaap']."',
						'".$arrayConcepto['cuenta_contrapartida_colgaap']."',
						'".$arrayConcepto['descripcion_cuenta_contrapartida_colgaap']."',
						'".$arrayConcepto['id_cuenta_contrapartida_niif']."',
						'".$arrayConcepto['cuenta_contrapartida_niif']."',
						'".$arrayConcepto['descripcion_cuenta_contrapartida_niif']."',
						'".$arrayConcepto['caracter_contrapartida']."',
						'".$arrayConcepto['centro_costos_contrapartida']."',
						'".$arrayConcepto['id_cuenta_colgaap_liquidacion']."',
						'".$arrayConcepto['cuenta_colgaap_liquidacion']."',
						'".$arrayConcepto['descripcion_cuenta_colgaap_liquidacion']."',
						'".$arrayConcepto['id_cuenta_niif_liquidacion']."',
						'".$arrayConcepto['cuenta_niif_liquidacion']."',
						'".$arrayConcepto['descripcion_cuenta_niif_liquidacion']."',
						'".$arrayConcepto['naturaleza']."',
						'".$arrayConcepto['imprimir_volante']."',
						'$input_calculo'
						)";

		$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos
					(id_planilla,
					id_empleado,
					id_contrato,
					id_concepto,
					codigo_concepto,
					concepto,
					valor_concepto,
					saldo_restante,
					formula,
					formula_original,
					id_empresa,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion,
					naturaleza,
					imprimir_volante,
					valor_campo_texto)
					VALUES $valueInsert";
		// print_r($arrayConcepto);
		//$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos (id_planilla,id_empleado,id_contrato,id_concepto,valor_concepto,id_empresa) VALUES ('$id_planilla','$id_empleado','$id_contrato','$id_concepto','$valor_concepto','$id_empresa')";
		$query=mysql_query($sql,$link);
		if ($query) {
			$sql="SELECT LAST_INSERT_ID() AS id";
			$query=mysql_query($sql,$link);
			$lastId= mysql_result($query,0,'id');

			$contOld=$cont;
			$cont++;

			// $styleImgFunction=($arrayConcepto['formula'] !='')? 'document.getElementById("divImgFunction_'.$contOld.'").style.display = "block";' : '' ;
			$evento_input=(user_permisos(169)=='false')? 'readonly' : 'onkeyup=\"validaNumero(event,this)\"';

			$script=($arrayConcepto['naturaleza']=='Deduccion')?
				'document.getElementById("divImageConfiConcepto_'.$contOld.'").setAttribute("onclick","ventanaConfigurarConceptoDeduccion('.$contOld.')");
				document.getElementById("divImageConfiConcepto_'.$contOld.'").setAttribute("title","Configuracion del concepto");' : '' ;

			echo '<script>
					contConceptos='.$cont.';
					//AGREGAR EL CONCEPTO A LA PLANILLA DE NOMINA
				 	var div=document.createElement("div");
				 	div.setAttribute("class","bodyDivNominaPlanilla");
				 	div.innerHTML="<div class=\"campo\" id=\"divLoadConcepto_'.$cont.'\">"+contConceptos+"</div>"+
        		     				"<div class=\"campo1\" id=\"concepto_'.$cont.'\" style=\"width:calc(100% - 50% - 70px - 18px );\"></div>"+
                        	     		"<div class=\"campo\" id=\"divImgFunction_'.$cont.'\" style=\"display:none;border: none;height:21px;width: 13px;margin-top: 1px;margin-left: -15px;background-image:url(img/funcion.png);background-repeat:no-repeat;background-color:#FFF;\" title=\"Concepto calculado con formula\"></div>"+
                        	     		"<div id=\"btnBuscarConcepto_'.$cont.'\" onclick=\"ventanaBuscarConceptos('.$cont.')\" title=\"Buscar Concepto\" class=\"iconBuscar\">"+
            					 		    "<img src=\"img/buscar20.png\">"+
            					 		"</div>"+
                        	     		"<div class=\"campo1\" style=\"width:calc(100% - 50% - 50px );text-indent:0;\">"+
	                        	     		"<div style=\"float:left;width:25px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px\">"+
											"</div>"+

											"<div style=\"float:left;width:50px;height:100%;border-right:1px solid #d4d4d4;text-align:right;\">"+
											"</div>"+

                        	     			"<input onkeyup=\"validaNumero(event,this)\" id=\"input_calculo_'.$cont.'\" style=\"width:50px;border-right:1px solid #d4d4d4;display:none;\" type=\"text\" readonly>"+
                        	     			"<input '.$evento_input.' id=\"valor_concepto_'.$cont.'\" style=\"width:calc(100% - 30px - 50px);\" type=\"text\">"+
                        	     		"</div>"+
                        	     		"<div class=\"campo1\" id=\"naturaleza_'.$cont.'\" style=\"width:30px;text-align:center;\"></div>"+
                        	     		"<div class=\"campo1\" id=\"imprimir_volante_'.$cont.'\" style=\"width:30px;text-align:center;text-indent:0px;display:none;\" title=\"\"></div>"+
                        	     		"<div style=\"float:left; min-width:60px;margin-left:10px;\">"+
                	     		 		    "<div onclick=\" guardarConcepto('.$cont.',\'\')\" id=\"divImageSaveConcepto_'.$cont.'\"  title=\"Guardar Concepto\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;\"><img src=\"img/save_true.png\" id=\"ImageSaveConcepto_'.$cont.'\"></div>"+
                	     		 		    "<div onclick=\"\" id=\"divImageDeshacerConcepto_'.$cont.'\" title=\"Deshacer Cambios\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;display:none\"><img src=\"img/deshacer.png\" id=\"imgDeshacerArticuloFacturaVenta_1\"></div>"+
                	     		 		    "<div onclick=\"ventanaConfigurarCuentasConcepto('.$cont.')\" id=\"divImageConfiConcepto_'.$cont.'\" title=\"Configurar Cuentas\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;display:none;\"><img src=\"img/config16.png\"></div>"+
                	     		 		    "<div onclick=\"eliminarConcepto('.$cont.')\" id=\"deleteConcepto_'.$cont.'\" title=\"Eliminar Concepto\" style=\"width:20px; float:left;display:none; margin-top:3px; cursor:pointer;\"><img src=\"img/delete.png\"></div>"+
                	     		 		"</div>"+
                	     		 		"<input type=\"hidden\" id=\"id_insert_concepto_'.$cont.'\" value=\"\">"+
                	     		 		"<input type=\"hidden\" id=\"id_concepto_'.$cont.'\" value=\"\">"+
                	     		 		"<input type=\"hidden\" id=\"id_contrato_concepto_'.$cont.'\" value=\"'.$id_contrato.'\">"+
                        	     		"<input type=\"hidden\" id=\"id_empleado_concepto_'.$cont.'\" value=\"'.$id_empleado.'\">"+
                        	     		"<input type=\"hidden\" id=\"formula_concepto_'.$cont.'\" value=\"\">"+
                        	     		"<input type=\"hidden\" id=\"nivel_formula_concepto_'.$cont.'\" value=\"\">"+
        		     				"</div>";

					document.getElementById("contenedorConceptos").appendChild(div);
					document.getElementById("divImageSaveConcepto_'.$contOld.'").style.display  = "none";
					document.getElementById("divImageSaveConcepto_'.$contOld.'").setAttribute("onclick","guardarConcepto('.$contOld.',\'actualizarconcepto\')");
					document.getElementById("ImageSaveConcepto_'.$contOld.'").setAttribute("src","img/reload.png");
					document.getElementById("divImageConfiConcepto_'.$contOld.'").style.display = "block";
					document.getElementById("deleteConcepto_'.$contOld.'").style.display        = "block";
					document.getElementById("btnBuscarConcepto_'.$contOld.'").style.display     = "none";
					document.getElementById("id_insert_concepto_'.$contOld.'").value            ="'.$lastId.'";
					document.getElementById("valor_concepto_'.$cont.'").focus();

					'.$script.'

					console.log("'.$arrayConcepto['formula'].'");

					'.$styleImgFunction.'

					//CALCULAR EL VALOR DEL EMPLEADO
					calculaValoresEmpleado('.$valor_concepto.',"agregar","'.$naturaleza.'");
					//CALCULAR LOS VALORES DE LA PLANILLA
					calcularValoresPlanilla();

				</script>'.$contOld;
		}
		else{
			echo '<script>alert("Error\nNo se guardo el concepto intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'.$contOld;
		}
	}

	function actualizarconcepto($id_insert,$input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$dias_adicionales,$id_empresa,$link){
		//CONSULTAR EL VALOR ANTERIOR PARA CARCULAR EN LA PLANILLA
		$sql="SELECT codigo_concepto,valor_concepto,valor_concepto_ajustado,id_prestamo,id_empleado,dias_laborados,base,formula_original
				FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE
					id_concepto = '$id_concepto' AND
					id_contrato = '$id_contrato' AND
					id_empleado = '$id_empleado' AND
					id_planilla = '$id_planilla' AND
					id_empresa  = '$id_empresa' AND
					id          = $id_insert";
		$query=mysql_query($sql,$link);

		$codigo_concepto         = mysql_result($query,0,'codigo_concepto');
		$valor_concepto_anterior = (mysql_result($query,0,'valor_concepto_ajustado')>0)? mysql_result($query,0,'valor_concepto_ajustado') : mysql_result($query,0,'valor_concepto');
		$id_empleado_prestamo    = mysql_result($query,0,'id_empleado');
		$id_prestamo             = mysql_result($query,0,'id_prestamo');
		$dias_laborados          = mysql_result($query,0,'dias_laborados');
		$base                    = mysql_result($query,0,'base');
		$formula_original        = mysql_result($query,0,'formula_original');

		// SI TIENE UN ID DE PRESTAMO, VALIDAR QUE EL VALOR INGRESADO NO EXEDA AL VALOR RESTANTE DEL PRESTAMO
		if ($id_prestamo>0) {
			$sql="SELECT valor_prestamo_restante FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_prestamo";
			$query=mysql_query($sql,$link);
			$valor_prestamo_restante=mysql_result($query,0,'valor_prestamo_restante');
			if ($valor_concepto>$valor_prestamo_restante) {
				echo '<script>
						alert("Error!\nEste concepto pertenece a un Prestamo y el valor ingresado supera el valor restante del prestamo");
						document.getElementById("valor_concepto_'.$cont.'").value="'.$valor_prestamo_restante.'";
					</script>'	;
				exit;
			}
		}

		if ($naturaleza=='Provision') {
			if ($codigo_concepto=='PS' && $valor_concepto_anterior == $valor_concepto) {
				$DL               = $dias_laborados+$dias_adicionales;
				// ($arrayConceptosAcumulados[$id_concepto]['base']/$arrayConceptosAcumulados[$id_concepto]['saldo_dias_laborados'])*30
				$base_concepto    = ($base/$dias_laborados)*30;
				$formula_original = str_replace('{BL}', $base_concepto, $formula_original);
				$formula_original = str_replace('{DL}', $DL, $formula_original);
				$valor_concepto   =  calcula_formula($formula_original);
				$campoUpdate      = "dias_adicionales='$dias_adicionales',formula='$formula_original',";
				$script           = 'cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');';
			}
			$campoUpdate.="valor_concepto_ajustado='$valor_concepto',";
		}
		else{
			$campoUpdate="valor_concepto='$valor_concepto',";
		}

		//ACTUALIZAR EL CAMPO CON EL NUEVO VALOR
		$sql="UPDATE nomina_planillas_liquidacion_empleados_conceptos SET $campoUpdate saldo_restante='$valor_concepto', valor_campo_texto='$input_calculo'
				WHERE id_concepto = '$id_concepto' AND
						id_contrato = '$id_contrato' AND
						id_empleado = '$id_empleado' AND
						id_planilla = '$id_planilla' AND
						id_empresa  = '$id_empresa' AND
						id          = $id_insert";
		$query=mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					//CALCULAR EL VALOR DEL EMPLEADO
					calculaValoresEmpleado('.$valor_concepto_anterior.',"eliminar","'.$naturaleza.'");
					//CALCULAR EL VALOR DEL EMPLEADO
					calculaValoresEmpleado('.$valor_concepto.',"agregar","'.$naturaleza.'");
					document.getElementById(\'divImageSaveConcepto_'.$cont.'\').style.display=\'none\';
					//CALCULAR LOS VALORES DE LA PLANILLA
					calcularValoresPlanilla();
					'.$script.'
				</script>'.$cont;
			// recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link);
		}
		else{
			echo '<script>alert("Error\nNo se actualizo el valor intentelo de nuevo");</script>'.$cont;
		}
	}

	function updateFinalizaContrato($terminar_contrato,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link){
		$campo_update="";
		if ($terminar_contrato=='Si') {
			echo '<script>
					document.getElementById("div_contenedor_motivo_fin_contrato_label").style.display="block";
					document.getElementById("div_contenedor_motivo_fin_contrato_campo").style.display="block";
					document.getElementById("div_contenedor_fecha_fin_contrato_label").style.display="block";
					document.getElementById("div_contenedor_fecha_fin_contrato_campo").style.display="block";
				</script>';
		}
		else{
			echo '<script>
					document.getElementById("div_contenedor_motivo_fin_contrato_label").style.display="none";
					document.getElementById("div_contenedor_motivo_fin_contrato_campo").style.display="none";
					document.getElementById("motivo_fin_contrato").value="";
					document.getElementById("div_contenedor_fecha_fin_contrato_label").style.display="none";
					document.getElementById("div_contenedor_fecha_fin_contrato_campo").style.display="none";
				</script>';
			$campo_update=',id_motivo_fin_contrato="0",motivo_fin_contrato="" ';
		}

		$sql="UPDATE nomina_planillas_liquidacion_empleados SET terminar_contrato='$terminar_contrato' $campo_update WHERE
				id_empleado = '$id_empleado' AND
				id_contrato = '$id_contrato' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo el campo, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function updateMotivoFinContrato($id_motivo_finalizacion,$motivo_finalizacion,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_liquidacion_empleados SET id_motivo_fin_contrato=$id_motivo_finalizacion,motivo_fin_contrato='$motivo_finalizacion' WHERE
				id_empleado = '$id_empleado' AND
				id_contrato = '$id_contrato' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo el campo, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function updateDiasLaborados($dias,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link){
		// VALIDAR QUE LOS DIAS LABORADOS NO SEAN MAYORES A LOS DIAS DE LA PLANILLA
		$sql="SELECT dias_liquidacion FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);

		$dias_liquidacion=mysql_result($query,0,'dias_liquidacion');

		if ($dias>$dias_liquidacion) {
			echo '<script>
						alert("Aviso!\nLos dias no pueden ser superiores a '.$dias_liquidacion.' dias ");
						document.getElementById("dias_laborados").value="'.$dias_liquidacion.'";
				</script>';
			exit;
		}

		$sql="UPDATE nomina_planillas_liquidacion_empleados SET dias_laborados='$dias' WHERE
				id_empleado = '$id_empleado' AND
				id_contrato = '$id_contrato' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo el campo, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
		recalcularValoresConceptos($id_planilla,'DL',$id_contrato,$id_empleado,$id_empresa,$link);
	}

	function verificaEmpleado($check,$id_contrato,$id_empleado,$id_planilla,$cont,$id_empresa,$link){
		$valor = ($check=='true')? 'false' : 'true' ;
		$sql="UPDATE nomina_planillas_liquidacion_empleados SET verificado='$valor' WHERE
				id_empleado = '$id_empleado' AND
				id_contrato = '$id_contrato' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					document.getElementById("verifica_empleado_'.$id_contrato.'").setAttribute("src","img/checkbox_'.$valor.'.png");
				</script>'.$cont;
		}
		else{
			echo '<script>alert("Se produjo un error intentelo de nuevo");</script>'.$cont;
		}
	}

	function ventanaConfigurarCuentasConcepto($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link){
		// CONSULTAR EL ESTADO DE LA PLANILLA
		$sql    = "SELECT estado FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query  = mysql_query($sql,$link);
		$estado = mysql_result($query,0,'estado');

		//CONSULTAR LAS CUENTAS DEL CONCEPTO
		$sql="SELECT concepto,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion,
					naturaleza,
					base,
					dias_laborados,
					valor_concepto
					FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND
					id_planilla = '$id_planilla' AND
					id_concepto = '$id_concepto' AND
					id_contrato = '$id_contrato' AND
					id_empleado = '$id_empleado' AND
					id_empresa  = '$id_empresa'";
		$query=mysql_query($sql,$link);

		$concepto                                 = mysql_result($query,0,'concepto');
		$id_cuenta_colgaap                        = mysql_result($query,0,'id_cuenta_colgaap');
		$cuenta_colgaap                           = mysql_result($query,0,'cuenta_colgaap');
		$descripcion_cuenta_colgaap               = mysql_result($query,0,'descripcion_cuenta_colgaap');
		$id_cuenta_niif                           = mysql_result($query,0,'id_cuenta_niif');
		$cuenta_niif                              = mysql_result($query,0,'cuenta_niif');
		$descripcion_cuenta_niif                  = mysql_result($query,0,'descripcion_cuenta_niif');
		$caracter                                 = mysql_result($query,0,'caracter');
		$id_cuenta_contrapartida_colgaap          = mysql_result($query,0,'id_cuenta_contrapartida_colgaap');
		$cuenta_contrapartida_colgaap             = mysql_result($query,0,'cuenta_contrapartida_colgaap');
		$descripcion_cuenta_contrapartida_colgaap = mysql_result($query,0,'descripcion_cuenta_contrapartida_colgaap');
		$id_cuenta_contrapartida_niif             = mysql_result($query,0,'id_cuenta_contrapartida_niif');
		$cuenta_contrapartida_niif                = mysql_result($query,0,'cuenta_contrapartida_niif');
		$descripcion_cuenta_contrapartida_niif    = mysql_result($query,0,'descripcion_cuenta_contrapartida_niif');
		$caracter_contrapartida                   = mysql_result($query,0,'caracter_contrapartida');
		$id_cuenta_colgaap_liquidacion            = mysql_result($query,0,'id_cuenta_colgaap_liquidacion');
		$cuenta_colgaap_liquidacion               = mysql_result($query,0,'cuenta_colgaap_liquidacion');
		$descripcion_cuenta_colgaap_liquidacion   = mysql_result($query,0,'descripcion_cuenta_colgaap_liquidacion');
		$id_cuenta_niif_liquidacion               = mysql_result($query,0,'id_cuenta_niif_liquidacion');
		$cuenta_niif_liquidacion                  = mysql_result($query,0,'cuenta_niif_liquidacion');
		$descripcion_cuenta_niif_liquidacion      = mysql_result($query,0,'descripcion_cuenta_niif_liquidacion');
		$naturaleza                               = mysql_result($query,0,'naturaleza');
		$base                                     = mysql_result($query,0,'base');
		$dias_laborados                           = mysql_result($query,0,'dias_laborados');
		$valor_concepto                           = mysql_result($query,0,'valor_concepto');


		$cuenta_debito_colgaap  ='';
		$cuenta_debito_niif     ='';
		$cuenta_credito_colgaap ='';
		$cuenta_credito_niif    ='';

		if ($naturaleza=='Provision') {
			// SELECCIONAMOS LA CUENTA EN CREDITO PARA MOSTRARLA COMO DEBITO Y ASI REALIZAR EL MOVIMIENTO CONTABLE REVERSADO
			if ($caracter=='credito') {
				$cuenta_debito=$cuenta_colgaap;
				$cuenta_debito_niif=$cuenta_niif;
			}
			else{
				$cuenta_debito=$cuenta_contrapartida_colgaap;
				$cuenta_debito_niif=$cuenta_contrapartida_niif;
			}

			$btnColgaap = ($estado==1 || $estado==3)? '' : '<div class="divIcono" style="border-right  :1px solid #D4D4D4;"  onclick="ventanaBuscarCuentasConcepto(\'colgaap\',\'id_cuenta_contrapartida_colgaap\',\'cuenta_contrapartida_colgaap\')">
																<img src="img/buscar20.png" title="Buscar Cuenta">
															</div>
															<div class="divIcono" id="id_cuenta_contrapartida_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'id_cuenta_contrapartida_colgaap\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')">
																<img src="img/refresh.png" title="Homologar Cuenta en Niif">
															</div>';
			$btnNiif    = ($estado==1 || $estado==3)? '' : '<div class="divIcono"  style="border-top:1px solid #D4D4D4;border-right:none;" onclick="ventanaBuscarCuentasConcepto(\'niif\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')">
																<img src="img/buscar20.png" title="Buscar Cuenta">
															</div>';
			$styleColgaap = ($estado==1 || $estado==3)? 'style="width:calc(100% - 110px - 92px);border-right:none;"' : 'style="width:calc(100% - 110px - 94px - 49px);"';
			$styleNiif    = ($estado==1 || $estado==3)? 'style="width:calc(100% - 110px - 92px);border-right:none;"' : 'style="width:calc(100% - 110px - 94px - 23px);border-top:1px solid #D4D4D4;"';

			echo '	<style>

						.titulos_ventana{
							color       : #15428B;
							font-weight : bold;
							font-size   : 13px;
							font-family : tahoma,arial,verdana,sans-serif;
							text-align  : center;
							margin-top  : 15px;
							float       : left;
							width       : 100%;
						}

						.contenedor_tablas_cuentas{
							float            : left;
							width            : 90%;
							background-color : #FFF;
							margin-top       : 10px;
							margin-left      : 20px;
							border           : 1px solid #D4D4D4;
						}

						.headDivs{
							float            : left;
							background-color : #F3F3F3;
							padding          : 5 0 5 3;
							font-size        : 11px;
							font-weight      : bold;
							border-right     : 1px solid #D4D4D4;
							border-bottom    : 1px solid #D4D4D4;
						}

						.filaDivs{
							float         : left;
							border-right  : 1px solid #D4D4D4;
							padding       :  5 0 5 3;
							overflow      : hidden;
							white-space   : nowrap;
							text-overflow : ellipsis;
						}

						.divIcono{
							float            : left;
							width            : 20px;
							height           : 16px;
							padding          : 3 0 4 5;
							background-color : #F3F3F3;
							overflow         : hidden;
						}

						.divIcono>img{
							cursor : pointer;
							width  : 16px;
							height : 16px;
						}
					</style>
					<div style="width:100%;height:100%;background-color:#dfe8f6;">

						<div class="titulos_ventana">CONFIGURACION E INFORMACION</div>

						<div class="contenedor_tablas_cuentas">
							<div class="headDivs" style="width:90px;">CONTABILIDAD</div>
							<div class="headDivs" style="width:100px;">DEBITO</div>
							<div class="headDivs" style="width:calc(100% - 107px - 94px);border-right:none;">CREDITO</div>

							<div class="filaDivs" style="width:90px;background-color: #F3F3F3;font-weight:bold;">COLGAAP</div>
							<div class="filaDivs" id="cuenta_colgaap" style="width:100px;">&nbsp;'.$cuenta_debito.'</div>
							<div class="filaDivs" id="cuenta_contrapartida_colgaap" '.$styleColgaap.'>&nbsp;'.$cuenta_colgaap_liquidacion.'</div>
							'.$btnColgaap.'

							<div class="filaDivs" style="width:90px;border-top:1px solid #D4D4D4;background-color: #F3F3F3;font-weight:bold;">NIIF</div>
							<div class="filaDivs" id="cuenta_niif" style="width:100px;border-top:1px solid #D4D4D4;">&nbsp;'.$cuenta_debito_niif.'</div>
							<div class="filaDivs" id="cuenta_contrapartida_niif" '.$styleNiif.'>&nbsp;'.$cuenta_niif_liquidacion.'</div>
							'.$btnNiif.'

							<div class="headDivs" style="width:99%;border-right:none;border-top:1px solid #D4D4D4;">&nbsp;</div>
							<div class="headDivs" style="width:40%;border-right:none;border-bottom:none;border-right:1px solid #D4D4D4;">BASE DEL CONCEPTO</div>
							<div class="filaDivs" style="width:calc(59% - 5px);float:left;border-bottom:none;border-right:none;">&nbsp;'.(number_format(($base/$dias_laborados)*30,$_SESSION['DECIMALESMONEDA'])).'</div>
							<div class="headDivs" style="width:40%;border-right:none;border-bottom:none;border-right:1px solid #D4D4D4;border-top:1px solid #D4D4D4;">VALOR PROVISIONADO</div>
							<div class="filaDivs" style="width:calc(59% - 5px);float:left;border-bottom:none;border-right:none;border-top:1px solid #D4D4D4;">&nbsp;'.(number_format($valor_concepto,$_SESSION['DECIMALESMONEDA'])).'</div>

						</div>

						<div id="divLoadConfigCuentas" style="display:none;"></div>

						<input type="hidden" id="id_cuenta_colgaap" value="0">
						<input type="hidden" id="id_cuenta_niif" value="0">
						<input type="hidden" id="id_cuenta_contrapartida_colgaap" value="'.$id_cuenta_colgaap_liquidacion.'">
						<input type="hidden" id="id_cuenta_contrapartida_niif" value="'.$id_cuenta_niif_liquidacion.'">

					</div>
				';
		}
		else{

			$btnColgaap       = ($estado==1 || $estado==3)? '' : '<div id="id_cuenta_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'id_cuenta_colgaap\',\'id_cuenta_niif\',\'cuenta_niif\')" title="Sincronizar Niif" class="iconBuscar" style="overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
											                		    <img src="img/refresh.png">
											                		</div>
											                		<div onclick="ventanaBuscarCuentasConcepto(\'colgaap\',\'id_cuenta_colgaap\',\'cuenta_colgaap\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
											                		    <img src="img/buscar20.png">
											                		</div>';
			$btnContraColgaap = ($estado==1 || $estado==3)? '' : '<div id="id_cuenta_contrapartida_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'id_cuenta_contrapartida_colgaap\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Sincronizar Niif" class="iconBuscar" style="overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
											                		    <img src="img/refresh.png">
											                		</div>
																	<div onclick="ventanaBuscarCuentasConcepto(\'colgaap\',\'id_cuenta_contrapartida_colgaap\',\'cuenta_contrapartida_colgaap\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
											                		    <img src="img/buscar20.png">
											                		</div>';
			$btnNiif          = ($estado==1 || $estado==3)? '' : '<div onclick="ventanaBuscarCuentasConcepto(\'niif\',\'id_cuenta_niif\',\'cuenta_niif\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
											                		    <img src="img/buscar20.png">
											                		</div>';
			$btnContraNiif    = ($estado==1 || $estado==3)? '' : '<div onclick="ventanaBuscarCuentasConcepto(\'niif\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
										                		    <img src="img/buscar20.png">
										                		</div>';

			echo '	<style>
					.campoConfigLabel{
						width:30%;
						float:left;
						margin-top:10px;
						text-align:left;
						margin-left: 65px;
						height:20px;
						padding-top: 2px;
					}
					.campoConfig{
						width:120px;
						float:left;
						margin-top:10px;
						background-color:#FFF;
						height:20px;
						padding-top: 2px;
					}
				</style>
				<div style="width:100%;height:100%;background-color:#dfe8f6;">
					<div style="margin:auto;width:350px;text-align:center;padding-top: 20px;">
						<div id="divLoadConfigCuentas" style="width: 50px;"></div>
						<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;">'.$caracter.'</div>
						<div class="campoConfigLabel">Cuenta Colgaap</div><div class="campoConfig" id="cuenta_colgaap">'.$cuenta_colgaap.'</div>
						'.$btnColgaap.'
						<div class="campoConfigLabel">Cuenta Niif</div><div class="campoConfig" id="cuenta_niif">'.$cuenta_niif.'</div>
						'.$btnNiif.'

						<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;float:left;">'.$caracter_contrapartida.'</div>
						<div class="campoConfigLabel">Cuenta Colgaap</div><div class="campoConfig" id="cuenta_contrapartida_colgaap">'.$cuenta_contrapartida_colgaap.'</div>
						'.$btnContraColgaap.'
						<div class="campoConfigLabel">Cuenta Niif</div><div class="campoConfig" id="cuenta_contrapartida_niif">'.$cuenta_contrapartida_niif.'</div>
						'.$btnContraNiif.'
                		<input type="hidden" id="id_cuenta_colgaap" value="'.$id_cuenta_colgaap.'">
						<input type="hidden" id="id_cuenta_niif" value="'.$id_cuenta_niif.'">
						<input type="hidden" id="id_cuenta_contrapartida_colgaap" value="'.$id_cuenta_contrapartida_colgaap.'">
						<input type="hidden" id="id_cuenta_contrapartida_niif" value="'.$id_cuenta_contrapartida_niif.'">
					</div>

				</div>
			';

		}
	}

	function sincronizarCuentaNiif($id,$campoId,$campoText,$id_empresa,$link){
		$sqlNiif   = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif,PN.id
						FROM puc AS P, puc_niif AS PN
						WHERE P.activo=1
							AND P.id='$id'
							AND P.id_empresa='$id_empresa'
							AND PN.activo=1
							AND PN.id_empresa=P.id_empresa
							AND PN.cuenta=P.cuenta_niif
							LIMIT 0,1";
		$queryNiif = mysql_query($sqlNiif,$link);

		$contNiif        = mysql_result($queryNiif,0,'cont_niif');
		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');
		$descripcionNiif = mysql_result($queryNiif,0,'descripcion');
		$id_niif = mysql_result($queryNiif,0,'id');

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo '<script>
						document.getElementById("'.$campoText.'").innerHTML = "'.$cuentaNiif.'";
						document.getElementById("'.$campoId.'").value    = "'.$id_niif.'";
					</script>'; }

		echo'<img src="img/refresh.png" />';
	}

	function updateCuentasConcepto($id_cuenta_colgaap,$id_cuenta_niif,$id_cuenta_contrapartida_colgaap,$id_cuenta_contrapartida_niif,$id_concepto,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){
		// CONSULTAR LA NATURALEZA DEL CONCEPTO PARA REALIZAR EL UPDATE
		$sql="SELECT naturaleza FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE activo=1 AND
				id_concepto = '$id_concepto' AND
				id_contrato = '$id_contrato' AND
				id_empleado = '$id_empleado' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		$naturaleza=mysql_result($query,0,'naturaleza');

		if ($naturaleza=='Provision') {
			$sql  ="UPDATE nomina_planillas_liquidacion_empleados_conceptos
						SET
							id_cuenta_colgaap_liquidacion = '$id_cuenta_contrapartida_colgaap',
							id_cuenta_niif_liquidacion    = '$id_cuenta_contrapartida_niif'
						WHERE
							activo=1 AND
							id_concepto = '$id_concepto' AND
							id_contrato = '$id_contrato' AND
							id_empleado = '$id_empleado' AND
							id_planilla = '$id_planilla' AND
							id_empresa  = '$id_empresa'";
			$query=mysql_query($sql,$link);
		}
		else{
			$sql  ="UPDATE nomina_planillas_liquidacion_empleados_conceptos
						SET
							id_cuenta_colgaap 				= '$id_cuenta_colgaap',
							id_cuenta_niif                  = '$id_cuenta_niif',
							id_cuenta_contrapartida_colgaap = '$id_cuenta_contrapartida_colgaap',
							id_cuenta_contrapartida_niif    = '$id_cuenta_contrapartida_niif'
						WHERE
							activo=1 AND
							id_concepto = '$id_concepto' AND
							id_contrato = '$id_contrato' AND
							id_empleado = '$id_empleado' AND
							id_planilla = '$id_planilla' AND
							id_empresa  = '$id_empresa'";
			$query=mysql_query($sql,$link);
		}


		if ($query) {
			echo '<script>
						MyLoading2("off")
						Win_Ventana_configurar_cuentas_conceptos.close();
				</script>';
		}
		else{
			echo '<script>
					// alert("Error\nNo se actualizaron las cuentas, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					MyLoading2("off",{icono:"fail",texto:"Se produjo un error Intentelo de nuevo"});
				</script>';
		}
	}

	function calculaValorConceptoFormulaInput($id_insert_concepto,$id_planilla,$id_concepto,$id_contrato,$id_empleado,$cont,$variable,$id_empresa,$link){
		// SI EL CONCEPTO YA ESTA INSERTADO EN LA BASE DE DATOS
		if ($id_insert_concepto>0) {
			$sql="SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query=mysql_query($sql,$link);
			$salario_basico = mysql_result($query,0,'salario_basico');

			// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
			$sql="SELECT dias_laborados FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
			$query=mysql_query($sql,$link);
			$dias_laborados=mysql_result($query,0,'dias_laborados');

			// CONSULTAR LA FORMULA DEL CONCEPTO
			$sql="SELECT formula_original FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_concepto=$id_concepto AND id_contrato=$id_contrato AND id_empleado=$id_empleado" ;
			$query=mysql_query($sql,$link);
			$formula_original=mysql_result($query,0,'formula_original');
			$formula=mysql_result($query,0,'formula_original');

			// REEMPLAZAR PRIMERO EL VALOR DE LA FORMULA DEL INPUT
			$formula=str_replace('{CT}', $variable, $formula);
			$formula=str_replace('{SC}', $salario_basico, $formula);
			$formula=str_replace('{DL}', $dias_laborados, $formula);

			//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
			$search_var_concepto=strpos($formula, '[');
			if ($search_var_concepto===false) {
				// CALCULAR LA FORMULA
				$valor_concepto=calcula_formula($formula);
				// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
				if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
					echo '<script>alert("Error en el calculo de la fomula del concepto\nFormula: '.$formula.'");</script>';
					continue;
				}
				// ACTUALIZAR EL VALOR DEL CONCEPTO CON EL VALOR DE LA FORMULA
				$sql="UPDATE nomina_planillas_liquidacion_empleados_conceptos SET formula='$formula',valor_concepto=$valor_concepto,valor_campo_texto=$variable
						WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_concepto=$id_concepto AND id_contrato=$id_contrato AND id_empleado=$id_empleado";
				$query=mysql_query($sql,$link);
				if (!$query) {
					echo '<script>alert("Error!\nSe calculo el valor de la formula pero no se logro actualizar en el valor del concepto\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>';
				}
				echo "<script>
						document.getElementById('valor_concepto_".$cont."').value='".$valor_concepto."';
						document.getElementById('divImageSaveConcepto_".$cont."').style.display='none';
						console.log('".$formula_original."');
						console.log('".$formula."');
					</script>";

			}
			// SI EXISTEN VARIABLES DE OTROS CONCEPTOS, CONSULTAR EN LA BASE DE DATOS Y REEMPLAZAR
			else{
				$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1
										AND id_planilla = '$id_planilla'
										AND id_empleado = '$id_empleado'
										AND id_contrato = '$id_contrato'
										AND id_empresa  = '$id_empresa'";
								$query=mysql_query($sql,$link);
				while ($row=mysql_fetch_array($query)) {
					// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
					if($row['valor_concepto']<0){ continue; }

					// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
					$formula=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $formula);
				}
				$formula=reemplazarValoresFaltantes($formula);
				//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
				$search_var_concepto=strpos($formula, '[');
				if ($search_var_concepto===false) {
					// CALCULAR LA FORMULA
					$valor_concepto=calcula_formula($formula);
					// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
					if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
						echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: '.$formula.'");</script>';
						continue;
					}

					// ACTUALIZAR EL VALOR DEL CONCEPTO CON EL VALOR DE LA FORMULA
					$sql="UPDATE nomina_planillas_liquidacion_empleados_conceptos SET formula='$formula',valor_concepto=$valor_concepto,valor_campo_texto=$variable
							WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_concepto=$id_concepto AND id_contrato=$id_contrato AND id_empleado=$id_empleado";
					$query=mysql_query($sql,$link);
					if (!$query) {
						echo '<script>alert("Error!\nSe calculo el valor de la formula pero no se logro actualizar en el valor del concepto\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>';
					}
						echo "<script>
								document.getElementById('valor_concepto_".$cont."').value='".$valor_concepto."';
								document.getElementById('divImageSaveConcepto_".$cont."').style.display='none';
							</script>";
				}
				// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES MOSTRAR MENSAJE DE ERROR
				else{
					$conceptos=EncuentraVariablesCadena($formula_original);
					foreach ($conceptos as $key => $codigo) {
						$mensaje.='\n* '.$codigo;
					}
					echo '<script>alert("No se puede calcular el valor del concepto por que necesitan los coneptos con codigos'.$conceptos.'");</script>';
				}

			}
		}
		// SI EL CONCEPTO AUN NO ESTA INSERTADO EN LA BASE DE DATOS
		else{
			// PRIMERO CONSULTAR LA FORMULA DEL CONCEPTO
			$sql="SELECT formula FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_concepto";
			$query=mysql_query($sql,$link);
			$formula=mysql_result($query,0,'formula');
			$formula_original=mysql_result($query,0,'formula');

			// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA FORMULA DIFERENTE A LA GENERAL
			$sql="SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query=mysql_query($sql,$link);
			$id_grupo_trabajo=mysql_result($query,0,'id_grupo_trabajo');
			if ($id_grupo_trabajo>0) {
				$sql="SELECT formula FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo AND id_concepto=$id_concepto";
				$query=mysql_query($sql,$link);
				$formula_grupo_trabajo=mysql_result($query,0,'formula');
				if ($formula_grupo_trabajo!='') {
					$formula=$formula_grupo_trabajo;
				}
			}

			// CONSULTAR EL SALARIO DEL CONTRATO
		$sql="SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";

		$query=mysql_query($sql,$link);
		$salario_basico = mysql_result($query,0,'salario_basico');

		// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
		$sql="SELECT dias_laborados FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
		$query=mysql_query($sql,$link);
		$dias_laborados=mysql_result($query,0,'dias_laborados');

		// REEMPLAZAR LOS VALORES PRINCIPALES DEL LA FORMULA
		$formula=str_replace('{SC}', $salario_basico, $formula);
		$formula=str_replace('{DL}', $dias_laborados, $formula);
		$formula=str_replace('{CT}', $variable, $formula);

		//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
		// $search_var_concepto=strpos($formula, '{CT}');
		// if ($search_var_concepto!==false) {
		// 	echo '<script>
		// 			document.getElementById("input_calculo_'.$cont.'").readOnly=false;
		// 			document.getElementById("input_calculo_'.$cont.'").setAttribute("onkeyup","calculaValorConceptoFormulaInput('.$id_empleado.','.$id_contrato.','.$id_concepto.','.$cont.',event,this)");
		// 			document.getElementById("formula_concepto_'.$cont.'").value="'.$formula_original.'";
		// 			document.getElementById("nivel_formula_concepto_'.$cont.'").value="'.$nivel_formula.'";

		// 			document.getElementById("valor_concepto_'.$cont.'").value = "0";

		// 			Win_Ventana_bucar_concepto.close();
		// 		</script>';
		// }
		// SI EXISTEN VARIABLES DE OTROS CONCEPTOS, CONSULTAR EN LA BASE DE DATOS Y REEMPLAZAR
		// else{
			$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1
					AND id_planilla = '$id_planilla'
					AND id_empleado = '$id_empleado'
					AND id_contrato = '$id_contrato'
					AND id_empresa  = '$id_empresa'";
							$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
				if($row['valor_concepto']<0){ continue; }
				// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
				$formula=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $formula);
			}

			//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
			$search_var_concepto=strpos($formula, '[');
			if ($search_var_concepto===false) {
				// CALCULAR LA FORMULA
				$valor_concepto=calcula_formula($formula);
				// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
				if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
					echo '<script>alert("Error en el calculo de la fomula del concepto\nFormula: '.$formula.'");</script>';
				}

				echo "<script>
						document.getElementById('valor_concepto_$cont').value         = '$valor_concepto';
						document.getElementById('formula_concepto_$cont').value       = '$formula_original';

						Win_Ventana_bucar_concepto.close();
					</script>";
			}
			// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES MOSTRAR MENSAJE DE ERROR
			else{
				$conceptos=EncuentraVariablesCadena($formula_original);
				foreach ($conceptos as $key => $codigo) {
					$mensaje.='\n* '.$codigo;
				}
				echo '<script>alert("No se puede calcular el valor del concepto por que necesitan los siguientes conceptos con codigo: '.$mensaje.'");</script>';
			}

		// }

		}

		echo $cont;
	}

	function calculaValorConceptoBuscado($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$id_empresa,$link){

		// CONSULTAR LA FORMULA DEL CONCEPTO
		//$sql="SELECT formula,nivel_formula,tipo_concepto FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id='$id_concepto' " ;
		//
		//$query            = mysql_query($sql,$link);
		//$formula_original = mysql_result($query,0,'formula');
		//$nivel_formula    = mysql_result($query,0,'nivel_formula');
		//$formula          = mysql_result($query,0,'formula');
		//$tipo_concepto    = mysql_result($query,0,'tipo_concepto');
		//
		//// echo '<script>console.log("formula: '.$formula.'");</script>';
		//// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA FORMULA DIFERENTE A LA GENERAL
		//$sql="SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
		//$query=mysql_query($sql,$link);
		//$id_grupo_trabajo=mysql_result($query,0,'id_grupo_trabajo');
		//if ($id_grupo_trabajo>0) {
		//	$sql="SELECT formula FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo AND id_concepto=$id_concepto";
		//	$query=mysql_query($sql,$link);
		//	$formula_grupo_trabajo=mysql_result($query,0,'formula');
		//	if ($formula_grupo_trabajo!='') {
		//		$formula=$formula_grupo_trabajo;
		//		$formula_original=$formula_grupo_trabajo;
		//	}
		//}
		//// echo '<script>console.log("formula: '.$formula.'");</script>';

		//// SI EL CONCEPTO NO TIENE FORMULA, TERMINAMOS EL PROCESO
		//if ($formula=='') {
		//	// CONSULTAR SI EL CONCEPTO TIENE VALORES PRECONFIGURADOS POR DEFECTO
		//	if ($tipo_concepto=='Personal') {
		//		// SELECCIONAR EL VALOR DEL CONCEPTO PERSONA
		//		$sql="SELECT valor_concepto FROM nomina_conceptos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto AND id_empleado=$id_empleado";
		//	}
		//	else{
		//		// CONSULTAR EL CARGO DEL EMPLEADO
		//		$sql="SELECT id_cargo FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		//		$query    = mysql_query($sql,$link);
		//		$id_cargo = mysql_result($query,0,'id_cargo');

		//		$sql="SELECT valor_concepto FROM nomina_conceptos_cargo WHERE activo=1 AND id_empresa=$id_empresa AND id_cargo=$id_cargo AND id_concepto=$id_concepto";
		//	}

		//	$query=mysql_query($sql,$link);
		//	$valor_concepto=mysql_result($query,0,'valor_concepto');

		//	echo '<script>
		//			document.getElementById("valor_concepto_'.$cont.'").value = "'.$valor_concepto.'";
		//			Win_Ventana_bucar_concepto.close();
		//		</script>';
		//	exit;
		//}

		//// CONSULTAR EL SALARIO DEL CONTRATO
		//$sql="SELECT salario_basico,valor_nivel_riesgo_laboral FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";

		//$query=mysql_query($sql,$link);
		//$salario_basico = mysql_result($query,0,'salario_basico');
		//$valor_nivel_riesgo_laboral = mysql_result($query,0,'valor_nivel_riesgo_laboral');

		//// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
		//$sql="SELECT dias_laborados FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
		//$query=mysql_query($sql,$link);
		//$dias_laborados=mysql_result($query,0,'dias_laborados');

		//// REEMPLAZAR LOS VALORES PRINCIPALES DEL LA FORMULA
		//$formula=str_replace('{SC}', $salario_basico, $formula);
		//$formula=str_replace('{DL}', $dias_laborados, $formula);
		//$formula=str_replace('{NRL}', $dias_laborados, $formula);

		////SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
		//$search_var_concepto=strpos($formula, '{CT}');
		//if ($search_var_concepto!==false) {
		//	echo '<script>
		//			document.getElementById("input_calculo_'.$cont.'").readOnly=false;
		//			document.getElementById("input_calculo_'.$cont.'").setAttribute("onkeyup","calculaValorConceptoFormulaInput('.$id_empleado.','.$id_contrato.','.$id_concepto.','.$cont.',event,this)");
		//			document.getElementById("formula_concepto_'.$cont.'").value="'.$formula_original.'";
		//			document.getElementById("nivel_formula_concepto_'.$cont.'").value="'.$nivel_formula.'";

		//			document.getElementById("valor_concepto_'.$cont.'").value = "0";

		//			Win_Ventana_bucar_concepto.close();
		//		</script>';
		//}
		//// SI EXISTEN VARIABLES DE OTROS CONCEPTOS, CONSULTAR EN LA BASE DE DATOS Y REEMPLAZAR
		//else{
		//	$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1
		//							AND id_planilla = '$id_planilla'
		//							AND id_empleado = '$id_empleado'
		//							AND id_contrato = '$id_contrato'
		//							AND id_empresa  = '$id_empresa'";
		//					$query=mysql_query($sql,$link);
		//	while ($row=mysql_fetch_array($query)) {
		//		// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
		//		if($row['valor_concepto']<0){ continue; }
		//		// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
		//		$formula=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $formula);
		//	}
		//	$formula=reemplazarValoresFaltantes($formula);
		//	//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
		//	$search_var_concepto=strpos($formula, '[');
		//	if ($search_var_concepto===false) {
		//		// CALCULAR LA FORMULA
		//		$valor_concepto=calcula_formula($formula);
		//		// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
		//		if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
		//			echo '<script>alert("Error en el calculo de la fomula del concepto\nFormula: '.$formula.'");</script>';
		//		}

		//		echo "<script>
		//				document.getElementById('valor_concepto_$cont').value         = '$valor_concepto';
		//				document.getElementById('formula_concepto_$cont').value       = '$formula_original';
		//				document.getElementById('nivel_formula_concepto_$cont').value = '$nivel_formula';

		//				Win_Ventana_bucar_concepto.close();
		//			</script>";
		//	}
		//	// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES MOSTRAR MENSAJE DE ERROR
		//	else{
		//		$conceptos=EncuentraVariablesCadena($formula_original);
		//		foreach ($conceptos as $key => $codigo) {
		//			$mensaje.='\n* '.$codigo;
		//		}
		//		echo '<script>alert("No se puede calcular el valor del concepto por que necesitan los siguientes conceptos con codigo: '.$mensaje.'");</script>';
		//	}

		//}

		echo '<script>Win_Ventana_bucar_concepto.close();</script>'.$cont;
	}

	function UpdateFechaFinContrato($fecha,$id_planilla,$id_empleado,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_liquidacion_empleados SET fecha_fin_contrato='$fecha'  WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla ANd id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se guardo la fecha de finalizacion del contrato\nIntentelo de nuevo");</script>';
		}
	}

	///////////////////////////////////////////////////////////////
	// FUNCION PARA RECALCULAR LOS CONCEPTOS CUANDO SE ACTUALIZA //
	///////////////////////////////////////////////////////////////
	function recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link){
		// VALIDAR QUE SEA DIFERENTE AL CAMPO DIAS LABORADOS DEL EMPLEADO
		if ($id_concepto=='DL') {
			// SI ES UN CONCEPTO, VALIDAR QUE SEA REQUERIDO POR OTROS CONCEPTOS Y SUS CALCULOS, SI NO ES NECESARIO PARA ACTUALIZAR EL VALOR DEL LA FORMULA DE OTROS CONCEPTOS TERMINAR FUNCION
			$sql="SELECT id FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND formula_original LIKE '%{DL}%' ";
			$query=mysql_query($sql,$link);
			$id_resul=mysql_result($query,0,'id');

			if ($id_resul==0) {
				return;
			}

		}
		else{
			// CONSULTAR EL CODIGO DEL CONCEPTO
			$sql="SELECT codigo_concepto,nivel_formula FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_concepto=$id_concepto";
			$query=mysql_query($sql,$link);
			$codigo_concepto = mysql_result($query,0,'codigo_concepto');
			$nivel_formula   = mysql_result($query,0,'nivel_formula');
			// SI ES UN CONCEPTO, VALIDAR QUE SEA REQUERIDO POR OTROS CONCEPTOS Y SUS CALCULOS, SI NO ES NECESARIO PARA ACTUALIZAR EL VALOR DEL LA FORMULA DE OTROS CONCEPTOS TERMINAR FUNCION
			$sql="SELECT id FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND formula_original LIKE '%$codigo_concepto%' ";
			$query=mysql_query($sql,$link);
			$id_resul=mysql_result($query,0,'id');

			if ($id_resul==0) {
				return;
			}
		}

		// CONSULTAR NUEVAMENTE LOS DATOS DEL SALARIO DE CONTRATO Y LOS DIAS LABORADOS
		//CONSULTAR LOS DIAS DE LABORADOS DEL EMPLEADO
		$sql="SELECT dias_laborados FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		$dias_laborados=mysql_result($query,0,'dias_laborados');


		// CONSULTAR EL SALRAIO DEL CONTRATO DEL EMPLEADO
		$sql="SELECT salario_basico,valor_nivel_riesgo_laboral	FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id=$id_contrato AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		$salario_basico = mysql_result($query,0,'salario_basico');
		$valor_nivel_riesgo_laboral=mysql_result($query,0,'valor_nivel_riesgo_laboral');

		// CONSULTAR TODOS LOS CONCEPTOS Y ORGANIZARLOS EN UN ARRAY
		$sql="SELECT id_concepto,codigo_concepto,concepto,nivel_formula,formula_original,valor_concepto,valor_campo_texto,id_cuenta_colgaap,id_cuenta_niif,id_cuenta_contrapartida_colgaap,id_cuenta_contrapartida_niif
				FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ORDER BY nivel_formula ASC";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id            = $row['id_concepto'];
			$nivel_formula = $row['nivel_formula'];
			$arrayConceptos[$nivel_formula][$id] = array(
														'id_concepto'                     => $row['id_concepto'],
														'codigo_concepto'                 => $row['codigo_concepto'],
														'concepto'                        => $row['concepto'],
														'nivel_formula'                   => $row['nivel_formula'],
														'formula'                         => $row['formula_original'],
														'valor_concepto'                  => $row['valor_concepto'],
														'valor_campo_texto'               => $row['valor_campo_texto'],
														'id_cuenta_colgaap'               => $row['id_cuenta_colgaap'],
														'id_cuenta_niif'                  => $row['id_cuenta_niif'],
														'id_cuenta_contrapartida_colgaap' => $row['id_cuenta_contrapartida_colgaap'],
														'id_cuenta_contrapartida_niif'    => $row['id_cuenta_contrapartida_niif'],
														);
		}
		// print_r($arrayConceptos);
		// echo json_encode($arrayConceptos);
		// RECORRER EL ARRAY CON LOS CONCEPTOS Y RECALCULAR EL VALOR DE LAS FORMULAS
		$nivel=0;
		// PRIMER CAPA DEL ARRAY CON LOS NIVELES DE LOS CONCEPTOS
		foreach ($arrayConceptos as $nivel_formula => $arrayConceptosArray) {
			// SEGUNDA CAPA DEL  ARRAY CON LOS ID DE LOS CONCEPTOS
			foreach ($arrayConceptosArray as $id_concepto_array => $arrayConceptosResul) {
				// SI EL CONCEPTO DEL ARRAY ES IGUAL AL ACTUALIZADO ENTONCES LO SALTAMOS
				if ($id_concepto_array==$id_concepto) { continue; }
				// SI NO TIENEN FORMULA, LOS SALTAMOS
				if ($arrayConceptosResul['formula']=='') { continue; }
				// echo 'Concepto principal: '.$arrayConceptosResul['concepto'].' formula: '.$arrayConceptosResul['formula'].' level: '.$nivel_formula.' <br>';
				// REEMPLAZAR LOS VALORES PRINCIPALES DE LA FORMULA
				$arrayConceptosResul['formula']=str_replace('{DL}', $dias_laborados, $arrayConceptosResul['formula']);
				$arrayConceptosResul['formula']=str_replace('{SC}', $salario_basico, $arrayConceptosResul['formula']);
				$arrayConceptosResul['formula']=str_replace('{CT}', $arrayConceptosResul['valor_campo_texto'], $arrayConceptosResul['formula']);
				$arrayConceptosResul['formula']=str_replace('{NRL}', $valor_nivel_riesgo_laboral, $arrayConceptosResul['formula']);

				// CONSULTAR EL ARRAY Y REEMPLAZAR LOS VALORES DE LA FORMULA PARA REALIZAR EL CALULO DE NUEVO
				foreach ($arrayConceptos as $nivel_formula_search => $arrayConceptosArray_search) {
					foreach ($arrayConceptosArray_search as $id_concepto_search => $arrayConceptosResul_search) {
						// echo '-> concepto search: '.$arrayConceptosResul_search['codigo_concepto'].' valor: '.$arrayConceptosResul_search['valor_concepto'].' level: '.$nivel_formula_search.' <br>';
						// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
						// if($arrayConceptosResul_search['valor_concepto']<0){ continue; }
						// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR
						if($nivel_formula<$nivel_formula_search){ /*echo "main: ".$nivel_formula." <-> search ".$nivel_formula_search." ---- ".$arrayConceptosResul_search['codigo_concepto']." ------ <br>";*/continue; }
						// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
						$arrayConceptosResul['formula']=str_replace('['.$arrayConceptosResul_search['codigo_concepto'].']', $arrayConceptosResul_search['valor_concepto'], $arrayConceptosResul['formula']);
					}
				}
				$arrayConceptosResul['formula']=reemplazarValoresFaltantes($arrayConceptosResul['formula']);
				//SI EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE NO ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA
				$search_var_concepto=strpos($arrayConceptosResul['formula'],'[');
				//SI ESTAN TODOS LOS CONEPTOS PARA LA FORMULA
				if ($search_var_concepto===false) {
					// CALCULAR LA FORMULA
					$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
					// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
					if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
						echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: '.$arrayConceptosResul['formula'].'");</script>';
						continue;
					}

					// SI LA FORMULA ES CORRECTA, REALIZAR EL UPDATE DEL CON LOS VALORES DE LA FORMULA
					$sql="UPDATE nomina_planillas_liquidacion_empleados_conceptos SET
							valor_concepto='$valor_concepto' ,
							formula='$arrayConceptosResul[formula]' ,
							id_cuenta_colgaap = '$arrayConceptosResul[id_cuenta_colgaap]',
							id_cuenta_niif = '$arrayConceptosResul[id_cuenta_niif]',
							id_cuenta_contrapartida_colgaap = '$arrayConceptosResul[id_cuenta_contrapartida_colgaap]',
							id_cuenta_contrapartida_niif = '$arrayConceptosResul[id_cuenta_contrapartida_niif]'
						WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_concepto=$id_concepto_array AND id_planilla=$id_planilla";

					$query=mysql_query($sql,$link);
					if (!$query) {
						// echo $sql;
						// echo "<br>";
						echo '<script>alert("Error\nNo se actualizo el valor del concepto '.$arrayConceptosResul['concepto'].'");</script>';
					}
					// CAMBIAR EL VALOR DEL CONCEPTO EN ESE ARRAY PARA LAS PROXIMAS FORMULAS
					$arrayConceptos[$nivel_formula][$id_concepto_array]['valor_concepto']=$valor_concepto;

				}
				// SINO MOSTRAR ERROR
				else{
					$mensaje='';
					$conceptos=EncuentraVariablesCadena($arrayConceptosResul['formula']);
					foreach ($conceptos as $key => $codigo) {
						$mensaje.='\n* '.$codigo;
					}
					// echo 'Error: '.$mensaje.'<br>';
					echo '<script>alert("No se puede calcular el valor del concepto:'.$arrayConceptosResul['concepto'].' por que necesitan los siguientes conceptos con codigo: '.$mensaje.'");</script>';
				}

			}
		}

		echo '<script>cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.',0)</script>';
	}

	///////////////////////////////////////////////////////////////
	function reemplazarValoresFaltantes($formula){
		// echo '<script>console.log(" -> '.$formula.'");</script>';
		$conceptos=EncuentraVariablesCadena($formula);
		foreach ($conceptos as $key => $codigo) {
			$formula=str_replace('['.$codigo.']', 0, $formula);
			$formula=str_replace('|>'.$codigo.'<|', 0, $formula);
		}
		// echo '<script>console.log(" => '.$formula.'");</script>';
		return $formula;
	}

	function EncuentraVariablesCadena($mensaje){
		$resultado1 = array();
		$resultado2 = array();
		$esta = stripos($mensaje,"[");
		if($esta !== false){
			$primera = explode("[",$mensaje);
			for($i=0;$i<count($primera);$i++){
				$esta2 = stripos($primera[$i],"]");
				if($esta2 !== false){
					$r = count($resultado1);
					$segunda = explode("]",$primera[$i]);
					$resultado1[$r] = $segunda[0];
				}
			}
		}

		$esta = stripos($mensaje,"|>");
		if($esta !== false){
			$primera = explode("|>",$mensaje);
			for($i=0;$i<count($primera);$i++){
				$esta2 = stripos($primera[$i],"<|");
				if($esta2 !== false){
					$r = count($resultado2);
					$segunda = explode("<|",$primera[$i]);

					$resultado2[$r] = $segunda[0];
				}
			}
		}
	 	$resultado = array_merge($resultado1,$resultado2);
		return $resultado;
	}

	/*function reemplazarValoresFaltantes($formula){
		$conceptos=EncuentraVariablesCadena($formula);
		foreach ($conceptos as $key => $codigo) {
			$formula=str_replace('['.$codigo.']', 0, $formula);
		}

		return $formula;
	}

	function EncuentraVariablesCadena($mensaje){
		$resultado = array();
		$esta = stripos($mensaje,"[");
		if($esta !== false){
			$primera = explode("[",$mensaje);
			for($i=0;$i<count($primera);$i++){
				$esta2 = stripos($primera[$i],"]");
				if($esta2 !== false){
					$r = count($resultado);
					$segunda = explode("]",$primera[$i]);
					$resultado[$r] = $segunda[0];
				}
			}
		}
		return $resultado;
	}*/

	function actualizarSucursal($id_planilla,$sucursal,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_liquidacion SET id_sucursal=$sucursal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo la sucursal de la planilla\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function ventanaConfigurarConceptoDeduccion($cont,$id_planilla,$id_concepto,$id_prestamo,$id_empleado,$id_contrato,$id_empresa,$link){
		$cont_deducir=1;
		// $where_prestamo=($id_prestamo>0)? ' AND id_prestamo='.$id_prestamo : '' ;
		$sql="SELECT * FROM nomina_planillas_liquidacion_conceptos_deducir
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_concepto=$id_concepto AND id_prestamo=$id_prestamo";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$body.='<div class="filaBoleta_concepto_deduccion" id="fila_concepto_deducir_'.$cont_deducir.'">
						<div class="campo0_concepto_deduccion" id="loadFila_'.$cont_deducir.'">'.$cont_deducir.'</div>
						<div class="campo1_concepto_deduccion" id="concepto_deducir_'.$cont_deducir.'">'.$row['concepto_deducir'].'</div>
						<div class="campoImg_concepto_deduccion" id="divImageBuscarConcepto_'.$cont_deducir.'" title="Buscar Entidad"><img src="img/buscar20.png" onclick="ventanaBuscarConceptosConceptoDeduccion('.$cont_deducir.','.$id_empleado.','.$id_contrato.')"></div>
						<div class="campo2_concepto_deduccion" ><input type="text" id="valor_deducir_'.$cont_deducir.'" value="'.$row['valor_deducir'].'"></div>
						<div style="float:right; min-width:55px;padding-top: 0px !important;">
							<div onclick="eliminarConceptoDeducir('.$cont_deducir.','.$id_empleado.','.$id_contrato.','.$id_concepto.','.$id_prestamo.')" id="delete_'.$cont_deducir.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
						</div>
						<input type="hidden" id="id_concepto_deducir_'.$cont_deducir.'" value="'.$row['id_concepto_deducir'].'">
					</div>';
			$cont_deducir++;
		}

		$body.='<div class="filaBoleta_concepto_deduccion" id="fila_concepto_deducir_'.$cont_deducir.'">
						<div class="campo0_concepto_deduccion" id="loadFila_'.$cont_deducir.'">'.$cont_deducir.'</div>
						<div class="campo1_concepto_deduccion" id="concepto_deducir_'.$cont_deducir.'"></div>
						<div class="campoImg_concepto_deduccion" id="divImageBuscarConcepto_'.$cont_deducir.'" title="Buscar Entidad"><img src="img/buscar20.png" onclick="ventanaBuscarConceptosConceptoDeduccion('.$cont_deducir.','.$id_empleado.','.$id_contrato.')"></div>
						<div class="campo2_concepto_deduccion" ><input type="text" id="valor_deducir_'.$cont_deducir.'"></div>
						<div style="float:right; min-width:55px;padding-top: 0px !important;">
							<div onclick="guardarConceptoDeducir('.$cont_deducir.','.$cont.','.$id_empleado.','.$id_contrato.','.$id_concepto.','.$id_prestamo.')" id="divImageSave_'.$cont_deducir.'" title="Guardar" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" ></div>
							<div onclick="eliminarConceptoDeducir('.$cont_deducir.','.$id_empleado.','.$id_contrato.','.$id_concepto.','.$id_prestamo.')" id="delete_'.$cont_deducir.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"></div>
						</div>
						<input type="hidden" id="id_concepto_deducir_'.$cont_deducir.'" value="">
					</div>';

		echo '
			<style>
				#contenedor_formulario_concepto_deduccion{
					overflow   : hidden;
					width      : calc(100% - 30px);
					height     : calc(100% - 10px);
					margin     : 15px;
					margin-top : 0px;
				}
				#contenedor_tabla_boletas_concepto_deduccion{
					overflow              : hidden;
					width                 : calc(100% - 2px);
					height                : calc(100% - 5px);
					/*border              : 1px solid #d4d4d4;*/
					border                : 1px solid #D4D4D4 	;
					border-radius         : 4px;
					-webkit-border-radius : 4px;
					-webkit-box-shadow    : 1px 1px 1px #d4d4d4;
					-moz-box-shadow       : 1px 1px 1px #d4d4d4;
					box-shadow            : 1px 1px 1px #d4d4d4;
					background-color      : #F3F3F3;
				}
				.campoImg_concepto_deduccion{
					float            : left;
					width            : 22px;
					border-right     : 1px solid #d4d4d4;
					background-color : #F3F3F3;
					padding-top      : 1px !important;
					height           : 22px !important;
					cursor           : hand;
				}
				.campo0_concepto_deduccion{
					float            : left;
					width            : 26px;
					text-indent      : 5px;
					border-right     : 1px solid #d4d4d4;
					background-color : #F3F3F3;
					padding-top      : 0px !important;
					height           : 22px !important;
				}

				.campo1_concepto_deduccion{
					float            : left;
					width            : 152px;
					text-indent      : 5px;
					background-color : #FFF;
					border-right     : 1px solid #d4d4d4;
					white-space      : nowrap;
					text-overflow    : ellipsis;
					overflow         : hidden;
					height           : 100%;
				}

				.campo2_concepto_deduccion{
					float            : left;
					width            : 175px;
					height           : 100%;
					text-indent      : 5px;
					background-color : #FFF;
					border-right     : 1px solid #d4d4d4;
					white-space      : nowrap;
					text-overflow    : ellipsis;
					overflow         : hidden;
				}

				.filaBoleta_concepto_deduccion{ background-color:#F3F3F3; }

				.filaBoleta_concepto_deduccion input[type=text]{
					border : 0px;
					width  : 90%;
					height : 100%;
				}

				.filaBoleta_concepto_deduccion input[type=text]:focus { background: #FFF; }

				#bodyTablaConceptos_concepto_deduccion{
					overflow-x       : hidden;
					overflow-y       : auto;
					width            : 100%;
					height           : calc(100% - 30px);
					background-color : #FFF;
					border-bottom    : 1px solid #d4d4d4;
				}

				#bodyTablaConceptos_concepto_deduccion > div{
					overflow      : hidden;
					height        : 22px;
					border-bottom : 1px solid #d4d4d4;
				}

				#bodyTablaConceptos_concepto_deduccion > div > div { /*height: 18px; background-color : #FFF; padding-top: 4px;*/ }

				.headTablaBoletas{
					overflow      : hidden;
					font-weight   : bold;
					width         : 100%;
					border-bottom : 1px solid #d4d4d4;
					height        : 22px;
				}

				.headTablaBoletas div{
					background-color : #F3F3F3;
					height           : 22px;
					padding-top      : 3;
				}
			</style>

			<div id="contenedor_formulario_concepto_deduccion">

				<div id="contenedor_tabla_boletas_concepto_deduccion">
					<div class="headTablaBoletas">
						<div class="campo0_concepto_deduccion">&nbsp;</div>
						<div class="campo2_concepto_deduccion">Concepto</div>
						<div class="campo2_concepto_deduccion">Valor a descontar</div>
					</div>
					<div id="bodyTablaConceptos_concepto_deduccion">
						'.$body.'
					</div>

				</div>
			</div>
			<script>


			</script>
		';
	}

	function guardarConceptoDeducir($id_planilla,$cont_deducir,$cont,$id_empleado,$id_contrato,$id_concepto,$id_prestamo,$id_concepto_deducir,$valor_deducir,$id_empresa,$link){
		$id_prestamo=($id_prestamo>0)? $id_prestamo : 0 ;
		//CONSULTAR EL VALOR DEL CONCEPTO A DEDUCIR
		$sql="SELECT
					valor_concepto,
					valor_concepto_ajustado,
					concepto,
					cuenta_colgaap_liquidacion,
					cuenta_niif_liquidacion,
					naturaleza,
  					IF(caracter ='credito',cuenta_colgaap,cuenta_contrapartida_colgaap) AS cuenta_colgaap,
					IF(caracter='credito',cuenta_niif ,cuenta_contrapartida_niif) AS cuenta_niif
				FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE
					activo=1
					AND id_empresa  = $id_empresa
					AND id_planilla = $id_planilla
					AND id_empleado = $id_empleado
					AND id_concepto = $id_concepto_deducir
					";
		$query=mysql_query($sql,$link);

		$concepto                   = mysql_result($query,0,'concepto');
		// $valor_concepto             = mysql_result($query,0,'valor_concepto');
		// $valor_concepto_ajustado    = mysql_result($query,0,'valor_concepto_ajustado');
		$valor_concepto = (mysql_result($query,0,'valor_concepto_ajustado')>0)? mysql_result($query,0,'valor_concepto_ajustado') : mysql_result($query,0,'valor_concepto') ;
		$naturaleza    = mysql_result($query,0,'naturaleza');
		if ($naturaleza=='Provision') {
			$cuenta_colgaap = mysql_result($query,0,'cuenta_colgaap_liquidacion');
			$cuenta_niif    = mysql_result($query,0,'cuenta_niif_liquidacion');
		}
		else{
			$cuenta_colgaap = mysql_result($query,0,'cuenta_colgaap');
			$cuenta_niif    = mysql_result($query,0,'cuenta_niif');
		}

		//CONSULTAR EL ACUMULADO DE LOS CONCEPTOS DEDUCIDOS
		$sql="SELECT SUM(valor_deducir) AS saldo
				FROM nomina_planillas_liquidacion_conceptos_deducir
				WHERE
					activo=1
					AND id_empresa          = $id_empresa
					AND id_planilla         = $id_planilla
					AND id_empleado         = $id_empleado
					AND id_concepto_deducir = $id_concepto_deducir";
		$query=mysql_query($sql,$link);
		$saldo = mysql_result($query,0,'saldo');

		// EL ACUMULADO DEL CONCEPTO DEDUCTOR
		$sql="SELECT SUM(valor_deducir) AS saldo
				FROM nomina_planillas_liquidacion_conceptos_deducir
				WHERE
					activo          =1
					AND id_empresa  = $id_empresa
					AND id_planilla = $id_planilla
					AND id_empleado = $id_empleado
					AND id_concepto = $id_concepto
					AND id_prestamo = $id_prestamo;";
		$query=mysql_query($sql,$link);
		$saldo_deductor = mysql_result($query,0,'saldo');


		// CONSULTAR EL CONCEPTO QUE SE ESTA DEDUCIENDO
		$sql="SELECT valor_concepto FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE activo=1
					AND id_empresa  = $id_empresa
					AND id_planilla = $id_planilla
					AND id_empleado = $id_empleado
					AND id_concepto = $id_concepto
					AND id_prestamo = $id_prestamo;";
		$query=mysql_query($sql,$link);
		$valor_concepto_deductor = mysql_result($query,0,'valor_concepto');

		if ($valor_deducir>$valor_concepto_deductor || ($saldo_deductor+$valor_deducir)>$valor_concepto_deductor) {
			echo '<script>alert("Error\nEl valor a descontar es mayor al valor del concepto de deduccion ");console.log("saldo: '.$saldo.'- valor_deducir: '.$valor_deducir.'- valor_concepto: '.$valor_concepto.'");</script>'.$cont_deducir;
			exit;
		}

		if ((($valor_concepto-$valor_deducir)-$saldo)<0) {
			echo '<script>alert("Error\nEl valor a descontar es mayor al valor restante del concepto\nSaldo restante: '.($valor_concepto-$saldo).' ");console.log("'.$saldo.'-'.$valor_deducir.'-'.$valor_concepto.'");</script>'.$cont_deducir;
			exit;
		}

		if ($valor_concepto>0 && ($valor_deducir>$valor_concepto /*|| ($valor_deducir+$saldo)>$valor_concepto*/) ) {
			echo '<script>alert("El valor a descontar es mayor que el valor del concepto ajustado");console.log("'.$saldo.'-'.$valor_deducir.'-'.$valor_concepto.'");</script>'.$cont_deducir;
			exit;
		}

		$sql="INSERT INTO nomina_planillas_liquidacion_conceptos_deducir(
				id_planilla,id_empleado,id_concepto,id_prestamo,id_concepto_deducir,valor_deducir,cuenta_colgaap,cuenta_niif,id_empresa)
				VALUES
				($id_planilla,$id_empleado,$id_concepto,$id_prestamo,$id_concepto_deducir,$valor_deducir,$cuenta_colgaap,$cuenta_niif,$id_empresa) ";
		$query=mysql_query($sql,$link);

		if ($query){
			$cont_deducir_new=$cont_deducir+1;
			echo '<script>
					document.getElementById("delete_'.$cont_deducir.'").style.display="block";
					document.getElementById("divImageSave_'.$cont_deducir.'").style.display="none";

					//AGREGAR EL CONCEPTO A LA PLANILLA DE NOMINA
				 	var div=document.createElement("div");
				 	div.setAttribute("class","filaBoleta_concepto_deduccion");
				 	div.setAttribute("id","fila_concepto_deducir_'.$cont_deducir_new.'");
				 	div.innerHTML="<div class=\"campo0_concepto_deduccion\" id=\"loadFila_'.$cont_deducir_new.'\">'.$cont_deducir_new.'</div>"+
									"<div class=\"campo1_concepto_deduccion\" id=\"concepto_deducir_'.$cont_deducir_new.'\"></div>"+
									"<div class=\"campoImg_concepto_deduccion\" id=\"divImageBuscarConcepto_'.$cont_deducir_new.'\" title=\"Buscar Entidad\"><img src=\"img/buscar20.png\" onclick=\"ventanaBuscarConceptosConceptoDeduccion('.$cont_deducir_new.','.$id_empleado.','.$id_contrato.')\"></div>"+
									"<div class=\"campo2_concepto_deduccion\" ><input type=\"text\" id=\"valor_deducir_'.$cont_deducir_new.'\"></div>"+
									"<div style=\"float:right; min-width:55px;padding-top: 0px !important;\">"+
										"<div onclick=\"guardarConceptoDeducir('.$cont_deducir_new.','.$cont.','.$id_empleado.','.$id_contrato.','.$id_concepto.','.$id_prestamo.')\" id=\"divImageSave_'.$cont_deducir_new.'\" title=\"Guardar\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;\"><img src=\"img/save_true.png\" ></div>"+
										"<div onclick=\"eliminarConceptoDeducir('.$cont_deducir_new.','.$id_empleado.','.$id_contrato.','.$id_concepto.','.$id_prestamo.')\" id=\"delete_'.$cont_deducir_new.'\" title=\"Eliminar Registro\" style=\"width:20px; float:left; margin-top:3px; display:none;cursor:pointer;\"><img src=\"img/delete.png\"></div>"+
									"</div>"+
									"<input type=\"hidden\" id=\"id_concepto_deducir_'.$cont_deducir_new.'\" value=\"\">";
									// guardarConceptoDeducir(cont_deducir,cont,id_empleado,id_contrato,id_concepto)
					document.getElementById("bodyTablaConceptos_concepto_deduccion").appendChild(div);

				</script>'.$cont_deducir;
		}
		else{
			echo '<script>alert("Error!\nNo se guardo el concepto a deducir!\nIntentelo de nuevo");</script>';
		}
	}

	function eliminarConceptoDeducir($cont_deducir,$id_empleado,$id_concepto,$id_prestamo,$id_concepto_deducir,$id_planilla,$id_contrato,$id_empresa,$link){
		$sql="DELETE FROM nomina_planillas_liquidacion_conceptos_deducir
				WHERE activo=1
				AND id_empleado         = '$id_empleado'
				AND id_concepto         = '$id_concepto'
				AND id_prestamo         = '$id_prestamo'
				AND id_concepto_deducir = '$id_concepto_deducir'
				AND id_planilla         = '$id_planilla'
				AND id_empresa          = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					document.getElementById("fila_concepto_deducir_'.$cont_deducir.'").parentNode.removeChild(document.getElementById("fila_concepto_deducir_'.$cont_deducir.'"));
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se elimino el registro, intentelo de nuevo");</script>'.$cont_deducir;
		}
	}

	function microtime_float(){
		list($useg, $seg) = explode(" ", microtime());
		return ((float)$useg + (float)$seg);
	}

	// ============== BUSCAR EMPLEADO EN LA INTERFAZ DE LA PLANILLA ==================//
	function buscarEmpleadoCargado($id_planilla,$filtro,$estado,$id_empresa,$link){
		//======================= CONSULTAR LOS EMPLEADOS CARGADOS EN LA PLANILLA ==================================//
	    $sql='SELECT id_empleado,documento_empleado,nombre_empleado,id_contrato,verificado,email_enviado
	    		FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa='.$id_empresa.' AND id_planilla='.$id_planilla.' '.$filtro;
	    $query=mysql_query($sql,$link);
	    $bodyEmpleados='';
	    $cont=1;
	    while ($row=mysql_fetch_array($query)) {

	    	$titleImg=($row['email_enviado']=='true')? 'Reenviar Volante por Email' : 'Enviar Volante por Email' ;

	    	if ($estado==1) {
            $btnEmail='<div  class="iconBuscar" style="margin-left: -1px;" onclick="enviarVolanteUnicoEmpleado(\''.$row['id_contrato'].'\',\''.$row['id_empleado'].'\',\''.$row['nombre_empleado'].'\')">
                                <img class="capturaImgCheck" id="imgEmail_'.$row['id_contrato'].'" src="img/enviaremail_'.$row['email_enviado'].'.png" title="'.$titleImg.'">
                            </div>';
            $btnPrint='<div  class="iconBuscar" style="margin-left: -1px;" onclick="imprimirVolanteUnicoEmpleado(\''.$row['id_empleado'].'\')">
                                <img class="capturaImgCheck" id="imgPrint_'.$row['id_contrato'].'" src="img/printer.png" title="Imprimir Volante de nomina">
                            </div>';
        	}
        	if ($estado==0) {
        		$bodyEmpleados.='<div class="bodyDivNominaPlanilla claseBuscar" >
	                            <div class="campo" id="divLoadEmpleado_'.$row['id_contrato'].'">'.$cont.'</div>
	                            <div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_'.$row['id_contrato'].'"><img src="img/fila_selected.png"></div>
	                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:100px;text-indent:5px;">'.$row['documento_empleado'].'</div>
	                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:calc(100% - 100px - 49px - 20px);text-indent:5px;">'.$row['nombre_empleado'].'</div>
	                            <div onclick="verificaEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')"  title="Verificar Empleado" class="iconBuscar" style="margin-left: -1px;" >
	                                <img class="capturaImgCheck" src="img/checkbox_'.$row['verificado'].'.png" value="'.$row['verificado'].'" id="verifica_empleado_'.$row['id_contrato'].'">
	                            </div>
	                            <div onclick="eliminarEmpleado('.$row['id_contrato'].','.$row['id_empleado'].')" title="Eliminar Empleado" class="iconBuscar" style="margin-left: -1px;">
	                                <img src="img/delete.png">
	                            </div>
	                        </div>';
        	}
        	else{
        		$bodyEmpleados.='<div class="bodyDivNominaPlanilla claseBuscar" >
                            <div class="campo" id="divLoadEmpleado_'.$row['id_contrato'].'">'.$cont.'</div>
                            <div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_'.$row['id_contrato'].'"><img src="img/fila_selected.png"></div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:100px;text-indent:5px;">'.$row['documento_empleado'].'</div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:calc(100% - 100px - 49px - 40px);text-indent:5px;">'.$row['nombre_empleado'].'</div>
                            <div title="'.$titleImg.'" class="iconBuscar" style="margin-left: -1px;" >
                                <img class="capturaImgCheck" src="img/checkbox_'.$row['verificado'].'.png" value="'.$row['verificado'].'" id="verifica_empleado_'.$row['id_contrato'].'">
                            </div>
                            '.$btnEmail.'
                            '.$btnPrint.'
                        </div>';
        	}

	        $cont++;
	    }
	    if ($bodyEmpleados=='') {
	    	echo '<span style="font-weight:bold;font-size:11px;font-style:italic;color:#999;line-height:3;padding-left:15px;">No hay resultados de la busqueda...</span>';
	    }
	    else{
	    	echo $bodyEmpleados;
	    }
	}

	function updateVacaciones($vacaciones,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$mysql){
		$campo_update="";
		if ($vacaciones=='Si') {
			echo '<script>
					// document.getElementById("div_contenedor_libro_vacaciones_label").style.display="block";
					document.getElementById("div_contenedor_libro_vacaciones_campo").style.display="block";
					document.getElementById("div_contenedor_provision_vacaciones").style.display="block";
				</script>';
		}
		else{
			echo '<script>
					// document.getElementById("div_contenedor_libro_vacaciones_label").style.display="none";
					document.getElementById("div_contenedor_libro_vacaciones_campo").style.display="none";
					document.getElementById("div_contenedor_provision_vacaciones").style.display="none";
					document.getElementById("motivo_fin_contrato").value="";
				</script>';

			// ELIMINAR EL LIBRO DE VACACIONES
			$sql="DELETE FROM nomina_vacaciones_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND id_empleado=$id_empleado";
			$query=$mysql->query($sql,$mysql->link);

			// ELIMINAR LOS CONCEPTOS DE PROVISION
			$sql="DELETE FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_contrato=$id_contrato AND (codigo_concepto='EPST' OR codigo_concepto='PT')";
			$query=$mysql->query($sql,$mysql->link);

			// ACTUALIZAR EL EMPLEADO COMO NO PROVISIONANTE
			$sql="UPDATE nomina_planillas_liquidacion_empleados
					SET provision_vacaciones='false'
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla ANd id_contrato=$id_contrato ";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');</script>';
			}

		}

		$sql="UPDATE nomina_planillas_liquidacion_empleados SET vacaciones='$vacaciones' $campo_update WHERE
				id_empleado = '$id_empleado' AND
				id_contrato = '$id_contrato' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo el campo, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function ventana_libro_vacaciones($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link){

		// CONSULTAR LOS DATOS INICIALES DEL CONTRATO
		$sql="SELECT fecha_inicio_contrato FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
		$query=mysql_query($sql,$link);
		$fecha_contrato=mysql_result($query,0,'fecha_inicio_contrato');

		// CONSULTAR EL ULTIMO PERIODO DE VACACIONES DEL EMPLEADO
		$sql="SELECT
				NPL.id
				FROM
					nomina_planillas_liquidacion_empleados AS NPLE
				INNER JOIN nomina_planillas_liquidacion AS NPL ON NPL.id=NPLE.id_planilla
				WHERE
					NPLE.activo 	 = 1
				AND NPLE.id_empresa  = $id_empresa
				AND NPLE.id_empleado = $id_empleado
				AND NPLE.id_contrato = $id_contrato
				AND NPL.estado=1 AND NPL.activo=1 AND NPL.id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereIdPlanillas.=($whereIdPlanillas=='')? ' id_planilla='.$row['id'] : ' OR id_planilla='.$row['id'] ;
		}

		$sql="SELECT fecha_final_periodo_vacaciones
				FROM nomina_vacaciones_empleados
				WHERE
					activo=1
				$where
				AND id_empresa  = $id_empresa
				AND id_empleado = $id_empleado
				AND id_contrato = $id_contrato
				ORDER BY fecha_final_periodo_vacaciones DESC LIMIT 0,1";
		$query=mysql_query($sql,$link);
		$fecha_ultimas_vacaciones=mysql_result($query,0,'fecha_final_periodo_vacaciones');

		$fecha_inicio_vacaciones = ($fecha_ultimas_vacaciones<>'')? date("Y-m-d", strtotime("$fecha_ultimas_vacaciones +1 day")) : $fecha_contrato ;
		$fecha_final_vacaciones  = date("Y-m-d", strtotime("$fecha_inicio_vacaciones +12 month"));

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
					AND id_empleado = $id_empleado
					AND id_contrato = $id_contrato";
		$query=mysql_query($sql,$link);

		$id_row                              = mysql_result($query,0,'id');
		$fecha_inicio_contrato               = mysql_result($query,0,'fecha_inicio_contrato');
		$fecha_inicio_periodo_vacaciones     = mysql_result($query,0,'fecha_inicio_periodo_vacaciones');
		$fecha_final_periodo_vacaciones      = mysql_result($query,0,'fecha_final_periodo_vacaciones');
		$fecha_inicio_vacaciones_disfrutadas = mysql_result($query,0,'fecha_inicio_vacaciones_disfrutadas');
		$fecha_final_vacaciones_disfrutadas  = mysql_result($query,0,'fecha_fin_vacaciones_disfrutadas');
		$id_concepto_vacaciones              = mysql_result($query,0,'id_concepto_vacaciones');
		$concepto_vacaciones                 = mysql_result($query,0,'concepto_vacaciones');
		$tipo_base                           = mysql_result($query,0,'tipo_base');
		$dias_vacaciones_disfrutadas         = mysql_result($query,0,'dias_vacaciones_disfrutadas');
		$valor_base_vacaciones               = mysql_result($query,0,'base');
		$valor_vacaciones_disfrutadas        = mysql_result($query,0,'valor_vacaciones_disfrutadas');
		$fecha_inicio_labores                = mysql_result($query,0,'fecha_inicio_labores');
		$dias_vacaciones_compensadas         = mysql_result($query,0,'dias_vacaciones_compensadas');
		$valor_vacaciones_compensadas        = mysql_result($query,0,'valor_vacaciones_compensadas');
		$tipo_pago_vacaciones                = mysql_result($query,0,'tipo_pago_vacaciones');

		// SI LA PLANILLA AUN NO TIENE LIBRO DE VACACIONES CREADO
		if ($id_row==0 || $id_row=='') {
			// CONSULTAR SI TIENE PERIODOS PENDIENTES O SI ES UN NUEVO PERIODO DE VACACIONES A PAGAR
			$sql="SELECT COUNT(id) AS cont,fecha_inicio_periodo_vacaciones,fecha_final_periodo_vacaciones,id_contrato,tipo_pago_vacaciones FROM nomina_vacaciones_empleados
					WHERE activo=1 AND id_empresa=$id_empresa /*AND tipo_pago_vacaciones='parciales'*/ AND id_planilla<>$id_planilla AND id_empleado = $id_empleado
					GROUP BY fecha_inicio_periodo_vacaciones,fecha_final_periodo_vacaciones,tipo_pago_vacaciones ORDER BY fecha_final_periodo_vacaciones DESC ";
			$query=mysql_query($sql,$link);
			while ( $row = mysql_fetch_array($query) ){
				// CONSULTAR LOS DATOS INICIALES DEL CONTRATO
				$sql="SELECT fecha_inicio_contrato FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$row[id_contrato]";
				$query=mysql_query($sql,$link);
				$fecha_inicio_contrato=mysql_result($query,0,'fecha_inicio_contrato');

				if ($row['cont']==1 && $row['tipo_pago_vacaciones'] == 'parciales' ) {
					$fecha_inicio_periodo_vacaciones = $row['fecha_inicio_periodo_vacaciones'];
					$fecha_final_periodo_vacaciones  = $row['fecha_final_periodo_vacaciones'];
					$script                          .= 'document.getElementById("tipo_pago_vacaciones").disabled =true;' ;
					$tipo_pago_vacaciones            = 'parciales';
					break;

				}
				else if ( ($row['cont']==2 || $row['tipo_pago_vacaciones'] == 'parciales') ||  $row['tipo_pago_vacaciones'] == 'completas') {
					$fecha_inicio_periodo_vacaciones = date("Y-m-d", strtotime("$row[fecha_final_periodo_vacaciones] +1 day"));
					$fecha_final_periodo_vacaciones  = date("Y-m-d", strtotime("$fecha_inicio_periodo_vacaciones +12 month"));
					break;

				}

			}

			if($fecha_inicio_periodo_vacaciones=='0000-00-00' || $fecha_inicio_periodo_vacaciones==''){
				$fecha_inicio_contrato               = ($fecha_inicio_contrato=='0000-00-00' || $fecha_inicio_contrato=='')? $fecha_inicio_vacaciones : $fecha_inicio_contrato ;
				$fecha_inicio_vacaciones_disfrutadas = ($fecha_inicio_vacaciones_disfrutadas=='0000-00-00' || $fecha_inicio_vacaciones_disfrutadas=='')? '' : $fecha_inicio_vacaciones_disfrutadas ;
				$fecha_final_vacaciones_disfrutadas  = ($fecha_final_vacaciones_disfrutadas=='0000-00-00' || $fecha_final_vacaciones_disfrutadas=='')? '' : $fecha_final_vacaciones_disfrutadas ;
				$fecha_inicio_labores                = ($fecha_inicio_labores=='0000-00-00' || $fecha_inicio_labores=='')? '' : $fecha_inicio_labores ;
				$fecha_inicio_periodo_vacaciones     = $fecha_contrato;
				$fecha_final_periodo_vacaciones      = date("Y-m-d", strtotime("$fecha_inicio_periodo_vacaciones +12 month"));
			}
		}
		else{
			$fecha_inicio_contrato               = ($fecha_inicio_contrato=='0000-00-00' || $fecha_inicio_contrato=='')? $fecha_inicio_vacaciones : $fecha_inicio_contrato ;
			$fecha_inicio_vacaciones_disfrutadas = ($fecha_inicio_vacaciones_disfrutadas=='0000-00-00' || $fecha_inicio_vacaciones_disfrutadas=='')? '' : $fecha_inicio_vacaciones_disfrutadas ;
			$fecha_final_vacaciones_disfrutadas  = ($fecha_final_vacaciones_disfrutadas=='0000-00-00' || $fecha_final_vacaciones_disfrutadas=='')? '' : $fecha_final_vacaciones_disfrutadas ;
			$fecha_inicio_labores                = ($fecha_inicio_labores=='0000-00-00' || $fecha_inicio_labores=='')? '' : $fecha_inicio_labores ;
			$fecha_inicio_periodo_vacaciones     = ($fecha_inicio_periodo_vacaciones=='0000-00-00' || $fecha_inicio_periodo_vacaciones=='')? $fecha_contrato : $fecha_inicio_periodo_vacaciones ;
			$fecha_final_periodo_vacaciones      = ($fecha_final_periodo_vacaciones=='0000-00-00' || $fecha_final_periodo_vacaciones=='')? $fecha_final_vacaciones : $fecha_final_periodo_vacaciones ;
		}



		// CONSULTAR EL ESTADO DE LA PLANILLA
		$sql="SELECT estado FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$estado=mysql_result($query,0,'estado');

		$readonly=($estado==0)? '' : 'readonly' ;
		$tipo_base_vacaciones = ($tipo_base=='promedio_anio')? 'Promedio del A&ntilde;o' : 'Ultimo Salario' ;

		if ($estado==0) {
			$script.=($tipo_base=='promedio_anio')? 'document.getElementById("base_vacaciones").value="promedio_anio";' : 'document.getElementById("base_vacaciones").value="ultimo_salario";' ;
			$script.=($tipo_pago_vacaciones=='completas' || $tipo_pago_vacaciones=='')? 'document.getElementById("tipo_pago_vacaciones").value="completas";' : 'document.getElementById("tipo_pago_vacaciones").value="parciales";' ;
		}

		$select_tipo_pago = ($estado==0)? '<select class="myInput" style="width:174px;" id="tipo_pago_vacaciones" >
												<option value="completas" >Vacaciones Completas</option>
												<option value="parciales" >Vacaciones Parciales</option>
											</select>'
											: '<input type=""text readonly class="myInput" value="'.$tipo_pago_vacaciones.'">' ;

		$select_tipo_base = ($estado==0)? '<select class="myInput" style="width:174px;" id="base_vacaciones" onchange="restaFechas()">
												<option value="ultimo_salario" >Ultimo salario (Salario del contrato)</option>
												<option value="promedio_anio" >Promedio del a&ntilde;o</option>
											</select>'
											: '<input type=""text readonly class="myInput" value="'.$tipo_base_vacaciones.'">' ;

		$btn_buscar_concepto = ($estado==0)? '<div class="iconSearch" title="Buscar Concepto Vacaciones" onclick="ventanaBuscarConceptosVacaciones('.$id_empleado.','.$id_contrato.')">
												<img src="img/find.png">
											</div>' : '' ;

		$btn_valor_disfrute = ($estado==0)?'<div id="divLoadValorVacacionesDisfrutadas" class="iconSearch" onclick="restaFechas()" title="Actualizar Valor"  >
												<img src="img/update.png" style="margin: 1px 0px 0px 1px;">
											</div>' : '' ;

		$btn_valor_compensada = ($estado==0)? '<div id="divLoadValorVacacionesCompensadas" class="iconSearch" onclick="calculaValorClick()" title="Actualizar Valor"  >
													<img src="img/update.png" style="margin: 1px 0px 0px 1px;">
												</div>' : '' ;

		echo '
			<style>
				.contenedorLibroVacaciones{
					width  : 100%;
					height : 100%;
				}

				.tituloVacaciones{
					width       : 100%;
					height      : 25px;
					font-weight : bold;
					font-size   : 14px;
					text-align  : center;
					font-family : sans-serif;
					border-top : 1px solid #99BBE8;
					padding-top: 20px;
				}

				.renglonVacaciones{
					float : left;
					width : 100%;
				}
				.labelVacaciones{
					width      : 170px;
					float      : left;
					text-align : left;
				    margin: 10px 0 0 35px;
				}
				.campoVacaciones{
					width      : 50%;
					float      : left;
					text-align : left;
					margin-top : 10px;
				}

				hr{
					width : 300px;
					border-color : #EAEAEA;
				}

				.myInput, .myInputRequiere{
					border : none;
					height : 20px;
					padding: 0 0 0 5px;
					box-sizing: border-box;

				}
				.myInputRequiere{
					border : 1px solid #FF7373;
					background-color: rgb(255, 238, 238);
				}

				input[data-icon="buscar"] {
				    background-position: right center;
				    /*padding-right: 20px;*/
				    background-image: url("img/buscar20.png");
				    background-repeat: no-repeat;
				}

				/*input[data-icon="money"] {
				    background-position: left center;
				    text-indent: 15px;
				    background-image: url("img/money.png");
				    background-repeat: no-repeat;
				}*/

				.iconSearch{
					float            : left;
					height           : 20px;
					width            : 20px;
					margin-top       : 0px;
					border-left      : 1px solid #d4d4d4;
					overflow         : hidden;
					background-color : #FFF;
					cursor           : pointer;
				}

			</style>

			<div class="contenedorLibroVacaciones">
				<div id="divLoadLibroVacaciones"></div>
				<div class="tituloVacaciones">
					INFORMACI&Oacute;N DE LAS VACACIONES
					<hr>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Fecha Ingreso</div>
					<div class="campoVacaciones"> <input type="text" id="fecha_inicio_contrato" readonly class="myInput" value="'.$fecha_inicio_contrato.'"> </div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Periodo de Vacaciones</div>
					<div class="campoVacaciones">
						<input type="text" style="width:82px;" id="fecha_inicio_periodo_vacaciones" class="myInput" readonly value="'.$fecha_inicio_periodo_vacaciones.'"> -
						<input type="text" style="width:82px;" id="fecha_final_periodo_vacaciones" class="myInput" readonly value="'.$fecha_final_periodo_vacaciones.'">

						<div id="divLoadPeriodoVacaciones" class="iconSearch" onclick="cargarVacaciones('.$id_empleado.','.$id_contrato.')" title="Cargar Vacaciones de ese periodo"  style="float:right;height:20px; width:20px;margin-top:0px;border-left:1px solid #d4d4d4; overflow:hidden;margin-right:23px;">
							<img src="../nomina/img/load_vacations.png" style="margin: 1px 0px 0px 1px;">
						</div>
					</div>



				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Fecha Inicio<br>vacaciones Disfrutadas</div>
					<div class="campoVacaciones"> <input type="text" class="myInput" id="fecha_inicio_vacaciones_disfrutadas" value="'.$fecha_inicio_vacaciones_disfrutadas.'" readonly> </div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Fecha Final<br>vacaciones Disfrutadas</div>
					<div class="campoVacaciones"> <input type="text" class="myInput" id="fecha_final_vacaciones_disfrutadas" value="'.$fecha_final_vacaciones_disfrutadas.'" readonly> </div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Concepto de Vacaciones</div>
					<div class="campoVacaciones">
						<input type="hidden" id="id_concepto_vacaciones" value="'.$id_concepto_vacaciones.'">
						<input type="text" class="myInput" id="concepto_vacaciones" value="'.$concepto_vacaciones.'" readonly style="float:left;">
						'.$btn_buscar_concepto.'
					</div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Tipo de Pago de vacaciones</div>
					<div class="campoVacaciones">
						'.$select_tipo_pago.'

						<div class="iconSearch" onclick="helpTipoPago()" title="Acerca de este campo..."  style="float:right;height:20px; width:20px;margin-top:0px;border-left:1px solid #d4d4d4; overflow:hidden;margin-right:23px;">
							<img src="img/help.png" style="margin: 1px 0px 0px 1px;">
						</div>
					</div>

				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Tomar como base</div>
					<div class="campoVacaciones">
						'.$select_tipo_base.'
					</div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Base calculo Vacaciones</div>
					<div class="campoVacaciones"><input type="text" id="valor_base_vacaciones" readonly class="myInput" value="'.$valor_base_vacaciones.'" data-icon="money"> </div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Dias vacaciones Disfrutadas</div>
					<div class="campoVacaciones"><input type="text" id="dias_vacaciones_disfrutadas" readonly class="myInput" value="'.$dias_vacaciones_disfrutadas.'"> </div>
				</div>



				<div class="renglonVacaciones">
					<div class="labelVacaciones">Valor vacaciones Disfrutadas</div>
					<div class="campoVacaciones">
						<input type="text" id="valor_vacaciones_disfrutadas"  class="myInput" style="float:left;" readonly value="'.$valor_vacaciones_disfrutadas.'" data-icon="money">
						'.$btn_valor_disfrute.'
					</div>

				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Fecha inicio de labores</div>
					<div class="campoVacaciones"> <input type="text" id="fecha_inicio_labores" value="'.$fecha_inicio_labores.'" readonly class="myInputRequiere"> </div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Dias vacaciones Compensadas</div>
					<div class="campoVacaciones"> <input type="text" id="dias_vacaciones_compensadas" '.$readonly.' class="myInput" value="'.$dias_vacaciones_compensadas.'" onkeyup="validaNumeroDias(event,this);"> </div>
				</div>

				<div class="renglonVacaciones">
					<div class="labelVacaciones">Valor vacaciones Compensadas</div>
					<div class="campoVacaciones">
						<input type="text" id="valor_vacaciones_compensadas"  class="myInput" value="'.$valor_vacaciones_compensadas.'" readonly style="float:left;" data-icon="money">
						'.$btn_valor_compensada.'
					</div>
				</div>

			</div>

			<script>
				'.$script.'
				if ("'.$estado.'"==0) {
					new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 174,                   //ANCHO
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
					    width      : 174,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_final_vacaciones_disfrutadas",
					    editable   : false,                 //EDITABLE
					    value : "'.$fecha_final_vacaciones_disfrutadas.'",
					    listeners  : { select: function() { restaFechas()  } }
					});
					/*
					new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 174,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_inicio_promedio",
					    editable   : false,                 //EDITABLE
					    // value : "'.$fecha_inicio_promedio.'",
					    listeners  : { select: function() {   } }
					});
					new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 174,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_final_promedio",
					    editable   : false,                 //EDITABLE
					    // value : "'.$fecha_fin_promedio.'",
					    listeners  : { select: function() {   } }
					});
					*/
					new Ext.form.DateField({
					    emptyText  : "Seleccione...",    //PLACEHOLDER
					    fieldLabel : "Date from today",     //SI TIENE LABEL
					    format     : "Y-m-d",               //FORMATO
					    width      : 174,                   //ANCHO
					    allowBlank : false,
					    showToday  : false,
					    applyTo    : "fecha_inicio_labores",
					    editable   : false,                 //EDITABLE
					    value : "'.$fecha_inicio_labores.'",
					    listeners  : { select: function() {   } }
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
					calculaValorVacacionesDisfrutadas(dias,'.$id_empleado.','.$id_contrato.');
					calculaValorClick();
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
			 			calculaValorVacacionesDisfrutadas(numero,'.$id_empleado.','.$id_contrato.',"compensadas");
			 		}
			 	}

			 	function calculaValorClick(){
			 		var dias=document.getElementById("dias_vacaciones_compensadas").value;
			 		if (dias<=0) {
			 			return;
			 		}

			 		calculaValorVacacionesDisfrutadas(dias,'.$id_empleado.','.$id_contrato.',"compensadas");

			 	}

			</script>';
	}

	function guardarInfoVacaciones($fecha_inicio_contrato,$fecha_inicio_periodo_vacaciones,$fecha_final_periodo_vacaciones,$fecha_inicio_vacaciones_disfrutadas,$fecha_final_vacaciones_disfrutadas,$id_concepto_vacaciones,$concepto_vacaciones,$base_vacaciones,$dias_vacaciones_disfrutadas,$valor_base_vacaciones,$valor_vacaciones_disfrutadas,$fecha_inicio_labores,$dias_vacaciones_compensadas,$valor_vacaciones_compensadas,$id_empleado,$id_contrato,$id_planilla,$tipo_pago_vacaciones,$id_empresa,$mysql){

		$sql="SELECT COUNT(id) AS cont
				FROM nomina_vacaciones_empleados
				WHERE
					activo      = 1
				AND id_empresa  = $id_empresa
				AND id_planilla = $id_planilla
				AND id_contrato = $id_contrato
				AND id_empleado = $id_empleado";
		$query=$mysql->query($sql,$mysql->link);
		$cont=$mysql->result($query,0,'cont');

		if ($cont>0) {

			$sql="UPDATE nomina_vacaciones_empleados
					SET
						fecha_inicio_contrato               = '$fecha_inicio_contrato',
						fecha_inicio_periodo_vacaciones     = '$fecha_inicio_periodo_vacaciones',
						fecha_final_periodo_vacaciones      = '$fecha_final_periodo_vacaciones',
						fecha_inicio_vacaciones_disfrutadas = '$fecha_inicio_vacaciones_disfrutadas',
						fecha_fin_vacaciones_disfrutadas    = '$fecha_final_vacaciones_disfrutadas',
						id_concepto_vacaciones              = '$id_concepto_vacaciones',
						concepto_vacaciones                 = '$concepto_vacaciones',
						tipo_base                           = '$base_vacaciones',
						dias_vacaciones_disfrutadas         = '$dias_vacaciones_disfrutadas',
						base                                = '$valor_base_vacaciones',
						valor_vacaciones_disfrutadas        = '$valor_vacaciones_disfrutadas',
						fecha_inicio_labores                = '$fecha_inicio_labores',
						dias_vacaciones_compensadas         = '$dias_vacaciones_compensadas',
						valor_vacaciones_compensadas        = '$valor_vacaciones_compensadas',
						tipo_pago_vacaciones                = '$tipo_pago_vacaciones'
					WHERE
						activo      = 1
					AND id_empresa  = $id_empresa
					AND id_empleado = $id_empleado
					AND id_contrato = $id_contrato
					AND id_planilla = $id_planilla
					 ";
		}
		else{

			$sql="INSERT INTO nomina_vacaciones_empleados
					(
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
						tipo_pago_vacaciones,
						id_empleado,
						id_contrato,
						id_planilla,
					 	id_empresa
					 )
					VALUES
					(
						'$fecha_inicio_contrato',
						'$fecha_inicio_periodo_vacaciones',
						'$fecha_final_periodo_vacaciones',
						'$fecha_inicio_vacaciones_disfrutadas',
						'$fecha_final_vacaciones_disfrutadas',
						'$id_concepto_vacaciones',
						'$concepto_vacaciones',
						'$base_vacaciones',
						'$dias_vacaciones_disfrutadas',
						'$valor_base_vacaciones',
						'$valor_vacaciones_disfrutadas',
						'$fecha_inicio_labores',
						'$dias_vacaciones_compensadas',
						'$valor_vacaciones_compensadas',
						'$tipo_pago_vacaciones',
						'$id_empleado',
						'$id_contrato',
						'$id_planilla',
						'$id_empresa'
					)";
		}
		// echo $sql;
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$cierre_total = ($tipo_pago_vacaciones=='completas')? 'true' : 'false' ;
			$sql="UPDATE nomina_planillas_liquidacion_empleados_conceptos SET cierra_total_provision='$cierre_total'
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla AND id_concepto=$id_concepto_vacaciones ";
			$query=$mysql->query($sql,$mysql->link);

			echo '<script>
					Win_Ventana_libro_vacaciones.close();
					MyLoading2("off");
				</script>';
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error al guardar el libro de vacaciones",duracion:2500});
				</script>';
		}
	}

	//====================== FUNCION PARA GUARDAR LA OBSERVACION ==========================//
	function guardarObservacionEmpleado($observacion,$id_planilla,$id,$id_empresa,$link){

		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);

		$sql   = "UPDATE nomina_planillas_liquidacion_empleados SET  observaciones='$observacion' WHERE id_planilla='$id_planilla' AND id=$id AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		if($query){ echo 'true'; }
		else{ echo'false'; }
	}

	//============== CALCULAR EL VALOR DE LAS VACACIONES DISFRUTADAS ===================//
	function calcularBaseVacaciones($id_concepto_vacaciones,$fecha_inicio,$fecha_final,$base_vacaciones,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$mysql){
		// CONSULTAR LOS DIAS ACUMULADOS DEL CONCEPTO DE VACACIONES
		$sql="SELECT dias_laborados FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto_vacaciones AND id_empleado=$id_empleado AND id_planilla=$id_planilla";
		$query=$mysql->query($sql,$mysql->link);
		$dias_laborados = $mysql->result($query,0,'dias_laborados');

		// OBTENER LA BASE PARA EL CALCULO DE LAS VACACIONES
		if ($base_vacaciones=='ultimo_salario') {
			$sql="SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query=$mysql->query($sql,$mysql->link);
			$base=$mysql->result($query,0,'salario_basico');

			// $base_resul=$base/30;
			$base_resul=$base;
		}
		else if ($base_vacaciones=='promedio_anio') {
			// CONSULTAR EL PERIODO DE VACACIONES A PAGAR
			// $sql="SELECT fecha_inicio_contrato FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			// $query=mysql_query($sql,$link);
			// $fecha_inicio_contrato=mysql_result($query,0,'fecha_inicio_contrato');
			// $fecha_final= date("Y-m-d", strtotime("$fecha_inicio_contrato +12 month"));

			// CONSULTAR LOS CONCEPTOS QUE FORMAN LA BASE DEL CONCEPTO DE VACACIONES PARA CALULAR LA BASE PROMEDIADA
			$sql="SELECT id_concepto_base FROM nomina_conceptos_base_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto_vacaciones";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$whereIdConceptos.=($whereIdConceptos=='')? 'id_concepto='.$row['id_concepto_base'] : ' OR id_concepto='.$row['id_concepto_base'] ;
			}

			// CONSULTAR LAS PLANILLAS DE ESE PERIODO DE VACACIONES
			$sql="SELECT
						NP.id,
						NPE.dias_laborados,
						NPE.id_empleado
					FROM
						nomina_planillas AS NP,
						nomina_planillas_empleados AS NPE
					WHERE
						NP.activo = 1
					AND (NP.estado=1 OR NP.estado=2)
					AND NP.id_empresa = $id_empresa
					AND NP.fecha_inicio >= '$fecha_inicio'
					AND NP.fecha_final <= '$fecha_final'
					AND NPE.id_planilla=NP.id
					AND NPE.id_empleado='$id_empleado'
					GROUP BY NP.id,NPE.id_empleado";
			$query=$mysql->query($sql,$mysql->link);
			$whereIdPlanillas='';
			while ($row=$mysql->fetch_array($query)) {
				$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
				// $dias_laborados+=$row['dias_laborados'];
			}

			//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE ESE EMPLEADO
			echo$sql="SELECT
					id_concepto,
					codigo_concepto,
					concepto,
					SUM(valor_concepto) AS valor_acumulado,
					SUM(saldo_dias_laborados) AS saldo_dias_laborados,
					naturaleza
					FROM
						nomina_planillas_empleados_conceptos
					WHERE
						activo = 1
					AND id_empleado = $id_empleado
					AND id_empresa = $id_empresa
					AND ($whereIdPlanillas)
					AND ($whereIdConceptos)
					GROUP BY id_concepto";
			$query=$mysql->query($sql,$mysql->link);
			$whereIdConceptos='';
			while ($row=$mysql->fetch_array($query)) {
				$base += $row['valor_acumulado'];
			}
			$base_resul = ($base/$dias_laborados)*30;
			// $base_resul=$base;

		}

		$base_resul = round( $base_resul,$_SESSION['DECIMALESMONEDA']);
		// $valor=round($base_resul*$dias,$_SESSION['DECIMALESMONEDA']);
		$valor = $base_resul/30*$dias;
		// $valor = $base_resul*$dias_laborados/720;
		$valor = round($valor,$_SESSION['DECIMALESMONEDA']);
		// round($valor,$_SESSION['DECIMALESMONEDA']);
		echo "<script>
				document.getElementById('valor_base_vacaciones').value='$base_resul';
				console.log(' $base / $dias_laborados ');
			</script>";
		// echo '
		// 	<script>
		// 		// console.log("'.$base.' / '.$dias_laborados.' ");

		// 		// debugger;

		// 		// document.getElementById("'.$id_input.'").value="'.$valor.'";
		// 		// document.getElementById("valor_base_vacaciones").value="'.$base_resul.'";
		// 		// var valor_vacaciones_disfrutadas = document.getElementById("valor_vacaciones_disfrutadas").value;
		// 		// var valor_vacaciones_compensadas = document.getElementById("valor_vacaciones_compensadas").value;

		// 		// try {
		// 		//     document.getElementsByName("valor_concepto_'.$id_concepto_vacaciones.'")[0].value=((valor_vacaciones_disfrutadas*1)+(valor_vacaciones_compensadas*1)).toFixed('.$_SESSION['DECIMALESMONEDA'].');
		// 		// 	var contador_vacaciones = (document.getElementsByName("valor_concepto_'.$id_concepto_vacaciones.'")[0].id).split("_")[2];
		// 		// 	guardarConcepto(contador_vacaciones,"actualizar");
		// 		// }
		// 		// catch(err) {
		// 		//     console.log("error! no se ha seleccionado el concepto de vacaciones");
		// 		// }


		// 		// console.log(\'document.getElementsByName("valor_concepto_'.$id_concepto_vacaciones.'")\');
		// 		// console.log(valor_vacaciones_disfrutadas+" + "+valor_vacaciones_compensadas+" = "+(valor_vacaciones_disfrutadas*1)+(valor_vacaciones_compensadas*1));
		// 	</script>';
	}

	//============== CALCULAR EL VALOR DE LAS VACACIONES DISFRUTADAS ===================//
	function calculaValorVacacionesDisfrutadas($fecha_inicio,$fecha_final,$id_input,$id_concepto_vacaciones,$base_vacaciones,$dias,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$mysql){
		// CONSULTAR LOS DIAS ACUMULADOS DEL CONCEPTO DE VACACIONES
		$sql="SELECT dias_laborados FROM nomina_planillas_liquidacion_empleados_conceptos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto_vacaciones AND id_empleado=$id_empleado AND id_planilla=$id_planilla";
		$query=$mysql->query($sql,$mysql->link);
		$dias_laborados = $mysql->result($query,0,'dias_laborados');

		// OBTENER LA BASE PARA EL CALCULO DE LAS VACACIONES
		if ($base_vacaciones=='ultimo_salario') {
			$sql="SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query=$mysql->query($sql,$mysql->link);
			$base=$mysql->result($query,0,'salario_basico');

			// $base_resul=$base/30;
			$base_resul=$base;
		}
		else if ($base_vacaciones=='promedio_anio') {
			// CONSULTAR EL PERIODO DE VACACIONES A PAGAR
			// $sql="SELECT fecha_inicio_contrato FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			// $query=mysql_query($sql,$link);
			// $fecha_inicio_contrato=mysql_result($query,0,'fecha_inicio_contrato');
			// $fecha_final= date("Y-m-d", strtotime("$fecha_inicio_contrato +12 month"));

			// CONSULTAR LOS CONCEPTOS QUE FORMAN LA BASE DEL CONCEPTO DE VACACIONES PARA CALULAR LA BASE PROMEDIADA
			$sql="SELECT id_concepto_base FROM nomina_conceptos_base_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto_vacaciones";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$whereIdConceptos.=($whereIdConceptos=='')? 'id_concepto='.$row['id_concepto_base'] : ' OR id_concepto='.$row['id_concepto_base'] ;
			}

			// CONSULTAR LAS PLANILLAS DE ESE PERIODO DE VACACIONES
			$sql="SELECT
						NP.id,
						NPE.dias_laborados,
						NPE.id_empleado
					FROM
						nomina_planillas AS NP,
						nomina_planillas_empleados AS NPE
					WHERE
						NP.activo = 1
					AND (NP.estado=1 OR NP.estado=2)
					AND NP.id_empresa = $id_empresa
					AND NP.fecha_inicio >= '$fecha_inicio'
					AND NP.fecha_final <= '$fecha_final'
					AND NPE.id_planilla=NP.id
					AND NPE.id_empleado='$id_empleado'
					GROUP BY NP.id,NPE.id_empleado";
			$query=$mysql->query($sql,$mysql->link);
			$whereIdPlanillas='';
			while ($row=$mysql->fetch_array($query)) {
				$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
				// $dias_laborados+=$row['dias_laborados'];
			}

			//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE ESE EMPLEADO
			$sql="SELECT
					id_concepto,
					codigo_concepto,
					concepto,
					SUM(valor_concepto) AS valor_acumulado,
					SUM(saldo_dias_laborados) AS saldo_dias_laborados,
					naturaleza
					FROM
						nomina_planillas_empleados_conceptos
					WHERE
						activo = 1
					AND id_empleado = $id_empleado
					AND id_empresa = $id_empresa
					AND ($whereIdPlanillas)
					AND ($whereIdConceptos)
					GROUP BY id_concepto";
			$query=$mysql->query($sql,$mysql->link);
			$whereIdConceptos='';
			while ($row=$mysql->fetch_array($query)) {
				$base += $row['valor_acumulado'];
			}
			$base_resul = ($base/$dias_laborados)*30;
			// $base_resul=$base;

		}
		$base_resul = round( $base_resul,$_SESSION['DECIMALESMONEDA']);
		// $valor=round($base_resul*$dias,$_SESSION['DECIMALESMONEDA']);
		$valor = $base_resul/30*$dias;
		// $valor = $base_resul*$dias_laborados/720;
		$valor = round($valor,$_SESSION['DECIMALESMONEDA']);
		// round($valor,$_SESSION['DECIMALESMONEDA']);
		echo '
			<img src="img/update.png" style="margin: 1px 0px 0px 1px;">
			<script>
				console.log("'.$base.' / '.$dias_laborados.' ");

				// debugger;

				document.getElementById("'.$id_input.'").value="'.$valor.'";
				document.getElementById("valor_base_vacaciones").value="'.$base_resul.'";
				var valor_vacaciones_disfrutadas = document.getElementById("valor_vacaciones_disfrutadas").value;
				var valor_vacaciones_compensadas = document.getElementById("valor_vacaciones_compensadas").value;

				try {
				    document.getElementsByName("valor_concepto_'.$id_concepto_vacaciones.'")[0].value=((valor_vacaciones_disfrutadas*1)+(valor_vacaciones_compensadas*1)).toFixed('.$_SESSION['DECIMALESMONEDA'].');
					var contador_vacaciones = (document.getElementsByName("valor_concepto_'.$id_concepto_vacaciones.'")[0].id).split("_")[2];
					guardarConcepto(contador_vacaciones,"actualizar");
				}
				catch(err) {
				    console.log("error! no se ha seleccionado el concepto de vacaciones");
				}


				// console.log(\'document.getElementsByName("valor_concepto_'.$id_concepto_vacaciones.'")\');
				// console.log(valor_vacaciones_disfrutadas+" + "+valor_vacaciones_compensadas+" = "+(valor_vacaciones_disfrutadas*1)+(valor_vacaciones_compensadas*1));
			</script>';
	}

	// ============ GENERAR LAS DEDUCCIONES DE PROVISION Y GUARDAR CAMPO PARA LAS DEMAS PROVISIONES ==================//
	function check_provision($accion,$id_planilla,$id_empleado,$id_contrato,$id_empresa,$mysql){

		if ($accion=='checkin') {
			// CONSULTAR SI SE REALIZO EL LIBRO DE VACACIONES, SI NO, NO SE PUEDE REALIZAR
			$sql="SELECT
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
						id_empleado,
						id_contrato,
						id_planilla,
						id_empresa
					FROM nomina_vacaciones_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado";
			$query=$mysql->query($sql,$mysql->link);
			$dias_vacaciones_disfrutadas = mysql_result($query,0,'dias_vacaciones_disfrutadas');

			if ($dias_vacaciones_disfrutadas==0 || $dias_vacaciones_disfrutadas=='') {
				echo '<script>
						MyLoading2("off",{texto:"Debe generar primero el libro de vacaciones",icono:"fail",duracion:3000});
						document.getElementById("img_provisionamiento").setAttribute("src","img/checkout.png");
					</script>';
				exit;
			}

			// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA CONFIGURACION DIFERENTE A LA GENERAL
			$sql="SELECT id_grupo_trabajo,salario_basico FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id=$id_contrato AND id_empleado=$id_empleado";
			$query=$mysql->query($sql,$mysql->link);
			$id_grupo_trabajo =$mysql->result($query,0,'id_grupo_trabajo');
			$salario_basico   =$mysql->result($query,0,'salario_basico');

			///////////////////////////////////////////////////////////////////////////////////
			// INICIO DEL PROCESO DE CARGA DE LOS CONCEPTOS DE DEDUCCION DE SEGURIDAD SOCIAL //
			///////////////////////////////////////////////////////////////////////////////////

			// CONSULTAR LOS CONCEPTOS TIPO DEDUCCION DE LA PLANILLA DE SE EMPLEADO, PARA NO INSERTARLOS DE NUEVO
			$sql="SELECT id_concepto
					FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_contrato=$id_contrato AND id_planilla=$id_planilla";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=mysql_fetch_array($query)) {
				$whereIdConceptos.=' AND id<>'.$row['id_concepto'] ;
			}

			$valueInsert='';

			// CONSULTAR TODA LA INFORMACION DEL CONCEPTO
			$sql="SELECT
					id AS id_concepto,
					codigo,
					descripcion,
					formula,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					naturaleza,
					imprimir_volante,
					nivel_formula,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion
		  		FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND (codigo='EPST' OR codigo='PT') AND (id<>0 $whereIdConceptos) ";
			$query=$mysql->query($sql,$mysql->link);
			$cont_id=0;
			$whereIdConceptos = '';
			while ($row=mysql_fetch_array($query)) {

				$whereIdConceptos.=($whereIdConceptos=='')? ' id_concepto='.$row['id_concepto']  : ' OR id_concepto='.$row['id_concepto'] ;
				$arrayConcepto[$row['id_concepto']] = array(
										'id_concepto'                              => $row['id_concepto'],
										'codigo'                                   => $row['codigo'],
										'descripcion'                              => $row['descripcion'],
										'formula'                                  => $row['formula'],
										'formula_reemplazada'                      => $row['formula'],
										'valor_concepto'                           => $valor_concepto,
										'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
										'cuenta_colgaap'                           => $row['cuenta_colgaap'],
										'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
										'id_cuenta_niif'                           => $row['id_cuenta_niif'],
										'cuenta_niif'                              => $row['cuenta_niif'],
										'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
										'caracter'                                 => $row['caracter'],
										'centro_costos'                            => $row['centro_costos'],
										'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
										'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
										'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
										'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
										'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
										'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
										'caracter_contrapartida'                   => $row['caracter_contrapartida'],
										'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
										'naturaleza'                               => $row['naturaleza'],
										'imprimir_volante'                         => $row['imprimir_volante'],
										'nivel_formula'                            => $row['nivel_formula'],
										'id_cuenta_colgaap_liquidacion'            => $row['id_cuenta_colgaap_liquidacion'],
										'cuenta_colgaap_liquidacion'               => $row['cuenta_colgaap_liquidacion'],
										'descripcion_cuenta_colgaap_liquidacion'   => $row['descripcion_cuenta_colgaap_liquidacion'],
										'id_cuenta_niif_liquidacion'               => $row['id_cuenta_niif_liquidacion'],
										'cuenta_niif_liquidacion'                  => $row['cuenta_niif_liquidacion'],
										'descripcion_cuenta_niif_liquidacion'      => $row['descripcion_cuenta_niif_liquidacion'],
						 			);

				$cont_id++;
			}

			$sql="SELECT
					id_concepto,
					nivel_formula,
					formula,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion
					FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdConceptos) AND id_grupo_trabajo=$id_grupo_trabajo";
			$query = $mysql->query($sql,$mysql->link);

			while ($row = mysql_fetch_array($query)){
				$id_concepto                                                             = $row['id_concepto'];
				$formula_query                                                           = $row['formula'];

				$arrayConcepto[$id_concepto]['id_concepto']                              = $row['id_concepto'];
				$arrayConcepto[$id_concepto]['nivel_formula']                            = $row['nivel_formula'];
				$arrayConcepto[$id_concepto]['formula']                                  = ($formula_query=='')? $arrayConcepto[$id_concepto]['formula']  : $formula_query ;
				$arrayConcepto[$id_concepto]['id_cuenta_colgaap']                        = $row['id_cuenta_colgaap'];
				$arrayConcepto[$id_concepto]['cuenta_colgaap']                           = $row['cuenta_colgaap'];
				$arrayConcepto[$id_concepto]['descripcion_cuenta_colgaap']               = $row['descripcion_cuenta_colgaap'];
				$arrayConcepto[$id_concepto]['id_cuenta_niif']                           = $row['id_cuenta_niif'];
				$arrayConcepto[$id_concepto]['cuenta_niif']                              = $row['cuenta_niif'];
				$arrayConcepto[$id_concepto]['descripcion_cuenta_niif']                  = $row['descripcion_cuenta_niif'];
				$arrayConcepto[$id_concepto]['caracter']                                 = $row['caracter'];
				$arrayConcepto[$id_concepto]['centro_costos']                            = $row['centro_costos'];
				$arrayConcepto[$id_concepto]['id_cuenta_contrapartida_colgaap']          = $row['id_cuenta_contrapartida_colgaap'];
				$arrayConcepto[$id_concepto]['cuenta_contrapartida_colgaap']             = $row['cuenta_contrapartida_colgaap'];
				$arrayConcepto[$id_concepto]['descripcion_cuenta_contrapartida_colgaap'] = $row['descripcion_cuenta_contrapartida_colgaap'];
				$arrayConcepto[$id_concepto]['id_cuenta_contrapartida_niif']             = $row['id_cuenta_contrapartida_niif'];
				$arrayConcepto[$id_concepto]['cuenta_contrapartida_niif']                = $row['cuenta_contrapartida_niif'];
				$arrayConcepto[$id_concepto]['descripcion_cuenta_contrapartida_niif']    = $row['descripcion_cuenta_contrapartida_niif'];
				$arrayConcepto[$id_concepto]['caracter_contrapartida']                   = $row['caracter_contrapartida'];
				$arrayConcepto[$id_concepto]['centro_costos_contrapartida']              = $row['centro_costos_contrapartida'];
				$arrayConcepto[$id_concepto]['id_cuenta_colgaap_liquidacion']            = $row['id_cuenta_colgaap_liquidacion'];
				$arrayConcepto[$id_concepto]['cuenta_colgaap_liquidacion']               = $row['cuenta_colgaap_liquidacion'];
				$arrayConcepto[$id_concepto]['descripcion_cuenta_colgaap_liquidacion']   = $row['descripcion_cuenta_colgaap_liquidacion'];
				$arrayConcepto[$id_concepto]['id_cuenta_niif_liquidacion']               = $row['id_cuenta_niif_liquidacion'];
				$arrayConcepto[$id_concepto]['cuenta_niif_liquidacion']                  = $row['cuenta_niif_liquidacion'];
				$arrayConcepto[$id_concepto]['descripcion_cuenta_niif_liquidacion']      = $row['descripcion_cuenta_niif_liquidacion'];
			}


			if ($cont_id==0) {
				echo '<script>
						MyLoading2("off",{texto:"El empleado ya tiene los conceptos de aportes de seguridad social",icono:"warning",duracion:3000});
					</script>';
				exit;
			}



			// CREAR LA CADENA DEL INSERT DEL CONCEPTO
			foreach ($arrayConcepto as $id_concepto => $arrayResul) {
				$formula        = $arrayResul['formula'];
				$formula        = str_replace('{SC}', $salario_basico, $formula);
				$formula        = str_replace('{DL}', $dias_vacaciones_disfrutadas, $formula);
				$formula        = reemplazarValoresFaltantes($formula);
				$valor_concepto = calcula_formula($formula);

				$valueInsert.="('$id_planilla',
							'$id_empleado',
							'$id_contrato',
							'$id_concepto',
							'".$arrayResul['codigo']."',
							'".$arrayResul['descripcion']."',
							'".$valor_concepto."',
							'".$valor_concepto."',
							'".$formula."',
							'".$arrayResul['formula']."',
							'$id_empresa',
							'".$arrayResul['id_cuenta_colgaap']."',
							'".$arrayResul['cuenta_colgaap']."',
							'".$arrayResul['descripcion_cuenta_colgaap']."',
							'".$arrayResul['id_cuenta_niif']."',
							'".$arrayResul['cuenta_niif']."',
							'".$arrayResul['descripcion_cuenta_niif']."',
							'".$arrayResul['caracter']."',
							'".$arrayResul['centro_costos']."',
							'".$arrayResul['id_cuenta_contrapartida_colgaap']."',
							'".$arrayResul['cuenta_contrapartida_colgaap']."',
							'".$arrayResul['descripcion_cuenta_contrapartida_colgaap']."',
							'".$arrayResul['id_cuenta_contrapartida_niif']."',
							'".$arrayResul['cuenta_contrapartida_niif']."',
							'".$arrayResul['descripcion_cuenta_contrapartida_niif']."',
							'".$arrayResul['caracter_contrapartida']."',
							'".$arrayResul['centro_costos_contrapartida']."',
							'".$arrayResul['id_cuenta_colgaap_liquidacion']."',
							'".$arrayResul['cuenta_colgaap_liquidacion']."',
							'".$arrayResul['descripcion_cuenta_colgaap_liquidacion']."',
							'".$arrayResul['id_cuenta_niif_liquidacion']."',
							'".$arrayResul['cuenta_niif_liquidacion']."',
							'".$arrayResul['descripcion_cuenta_niif_liquidacion']."',
							'".$arrayResul['naturaleza']."',
							'".$arrayResul['imprimir_volante']."'
							),";
			}

			$valueInsert = substr($valueInsert, 0, -1);
			$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos
						(id_planilla,
						id_empleado,
						id_contrato,
						id_concepto,
						codigo_concepto,
						concepto,
						valor_concepto,
						saldo_restante,
						formula,
						formula_original,
						id_empresa,
						id_cuenta_colgaap,
						cuenta_colgaap,
						descripcion_cuenta_colgaap,
						id_cuenta_niif,
						cuenta_niif,
						descripcion_cuenta_niif,
						caracter,
						centro_costos,
						id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap,
						descripcion_cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif,
						descripcion_cuenta_contrapartida_niif,
						caracter_contrapartida,
						centro_costos_contrapartida,
						id_cuenta_colgaap_liquidacion,
						cuenta_colgaap_liquidacion,
						descripcion_cuenta_colgaap_liquidacion,
						id_cuenta_niif_liquidacion,
						cuenta_niif_liquidacion,
						descripcion_cuenta_niif_liquidacion,
						naturaleza,
						imprimir_volante
						)
						VALUES $valueInsert";
			$query = $mysql->query($sql,$mysql->link);

			$sql="UPDATE nomina_planillas_liquidacion_empleados
					SET provision_vacaciones='true'
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla ANd id_contrato=$id_contrato ";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				echo '<script>
						MyLoading2("off",{texto:"Se produjo un error intentelo de nuevo",icono:"fail"});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{texto:"Se cargaron los conceptos",icono:"success"});
						cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');
					</script>';
			}

		}
		else{
			// CONSULTAR LOS CONCEPTOS DE DEDUCCION PARA ELIMINAR EL REGITRO DE CONCEPTOS_DEDUCIR
			$sql="SELECT id_concepto FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla ANd id_empleado=$id_empleado AND id_contrato=$id_contrato";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=mysql_fetch_array($query)) {
				$whereIdConceptos.=($whereIdConceptos=='')? 'id_concepto='.$row['id_concepto'] : ' OR id_concepto='.$row['id_concepto'] ;
			}

			$sql="DELETE FROM nomina_planillas_liquidacion_conceptos_deducir
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_prestamo=0 AND ($whereIdConceptos)";
			$query=$mysql->query($sql,$mysql->link);

			// BORAR LOS CONCEPTOS QUE SON DEDUCIBLES DEL EMPLEADO QUE CORRESPONDEN A PAGOS DE APORTES DE SEGURIDAD SOCIAL
			$sql="DELETE FROM nomina_planillas_liquidacion_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND (codigo_concepto='EPST' OR codigo_concepto='PT')";
			$query=$mysql->query($sql,$mysql->link);

			$sql="UPDATE nomina_planillas_liquidacion_empleados
					SET provision_vacaciones='false'
					WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla ANd id_contrato=$id_contrato ";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				echo '<script>
						MyLoading2("off",{texto:"Se produjo un error intentelo de nuevo",icono:"fail"});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{texto:"Se eliminaron los conceptos",icono:"success"});
						cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');
					</script>';
			}

			// echo '<script>
			// 		MyLoading2("off",{texto:"realizado",icono:"success"});
			// 		document.getElementById("img_provisionamiento").setAttribute("src","img/checkout.png");
			// 	</script>';
		}
	}

	// ============ CARGAR LAS VACACIONES DE ESE PERIODO ============================= //
	function cargarVacaciones($id_planilla,$id_empleado,$id_contrato,$fecha_inicio,$fecha_final,$id_empresa,$mysql){

		// VALIDAR SI YA ESTA EL CONCEPTO VACACIONES
		$sql   = "SELECT id FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND codigo_concepto='VC' ";
		$query  = $mysql->query($sql,$mysql->link);
		$id_row = $mysql->result($query,0,'id');
		if ($id_row>0) {
			echo '<img src="../nomina/img/load_vacations.png" style="margin: 1px 0px 0px 2px;">';
			return;
		}

		// $sql   = "SELECT fecha_inicio,fecha_final FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla ";
		// $query = $mysql->query($sql,$mysql->link);
		// $fecha_inicio = $mysql->result($query,0,'fecha_inicio');
		// $fecha_final  = $mysql->result($query,0,'fecha_final');

		//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
		$sql="SELECT id,id_grupo_trabajo FROM empleados_contratos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
		$query=$mysql->query($sql,$mysql->link);
		$id_grupo_trabajo = $mysql->result($query,0,'id_grupo_trabajo');

		$sql="SELECT
					NP.id,
					NPE.dias_laborados,
					NPE.id_empleado
				FROM
					nomina_planillas AS NP,
					nomina_planillas_empleados AS NPE
				WHERE
					NP.activo       = 1
				AND ( NP.estado = 1 OR NP.estado = 2 )
				AND NP.id_empresa   = $id_empresa
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final  <= '$fecha_final'
				AND NPE.id_planilla = NP.id
				AND NPE.id_empleado = '$id_empleado'
				GROUP BY NP.id,NPE.id_empleado";
		$query=$mysql->query($sql,$mysql->link);
		$whereIdPlanillas = '';
		while ($row=$mysql->fetch_array($query)){
			$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			$dias_laborados+=$row['dias_laborados'];
		}

		//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE ESE EMPLEADO
		$sql="SELECT
				id_concepto,
				codigo_concepto,
				concepto,
				SUM(valor_concepto) AS valor_provisionado,
				SUM(dias_laborados) AS saldo_dias_laborados,
				naturaleza,
				id_planilla_cruce,
				tipo_planilla_cruce
				FROM
					nomina_planillas_empleados_conceptos
				WHERE
					activo = 1
				AND id_empleado = $id_empleado
				AND id_empresa = $id_empresa
				AND ($whereIdPlanillas)
				/*AND codigo_concepto = 'VC'*/
				/*AND saldo_dias_laborados>0*/
				GROUP BY id_concepto";
		$query=$mysql->query($sql,$mysql->link);
		$whereIdConceptos='';
		while ($row=$mysql->fetch_array($query)) {

			// SI EL CONCEPTO ES PROVISION Y ESTA ASIGNADO A OTRA PLANILLA DE LIQUIDACION ENTONCES NO SE PONE
			// if ($row['id_planilla_cruce']<>$id_planilla && $row['naturaleza']=='Provision') { continue; }

			if ($row['naturaleza']=='Provision'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_concepto']] = $row['valor_provisionado'];
				$arrayDiasConceptos[$row['id_concepto']]    = $row['saldo_dias_laborados'];
			}

			$arrayConceptosAcumulados[$row['id_concepto']] = array(
																	'codigo'               => $row['codigo_concepto'],
																	'concepto'             => $row['concepto'],
																	'valor_concepto'       => $row['valor_provisionado'],
																	'saldo_dias_laborados' => $row['saldo_dias_laborados'],
																	'naturaleza'           => $row['naturaleza'],
																	'base'                 => 0,
																	);

		}

		// CONSULTAR LOS CONCEPTOS PARA LA BASE DE LA LIQUIDACION
		$whereIdConceptosLiquidacion = str_replace("id", "id_concepto", $whereIdConceptos);
		$sql="SELECT * FROM nomina_conceptos_base_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdConceptosLiquidacion)";
		$query = $mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			if ($row['naturaleza_base']=='Devengo'){
				$arrayConceptosAcumulados[$row['id_concepto']]['base'] += $arrayConceptosAcumulados[$row['id_concepto_base']]['valor_concepto'];
			}
			else if ($row['naturaleza_base']=='Deduccion') {
				$arrayConceptosAcumulados[$row['id_concepto']]['base'] -= $arrayConceptosAcumulados[$row['id_concepto_base']]['valor_concepto'];
			}

		}

		// print_r($arrayConceptosAcumulados);

		//CONSULTAR TODOS LOS CONCEPTOS CON CARGA AUTOMATICA DE LA BASE DE DATOS
		$sql="SELECT id,
					codigo,
					descripcion,
					formula_liquidacion,
					nivel_formula_liquidacion,
					tipo_concepto,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion,
					centro_costos_contrapartida,
					naturaleza,
					imprimir_volante
				FROM nomina_conceptos
				WHERE
					activo=1
				AND id_empresa=$id_empresa
				AND naturaleza='Provision'
				AND ($whereIdConceptos)
				ORDER BY nivel_formula_liquidacion ASC";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$id = $row['id'];
			$tipo_concepto = $row['tipo_concepto'];
			$nivel_formula = $row['nivel_formula_liquidacion'];
			$row['formula'] = str_replace(" ","",$row['formula_liquidacion']);
			// echo "formula:".$row['formula'].'-';
			$arrayConceptos[$id] = array('codigo'           					   => $row['codigo'],
										'concepto'                                 => $row['descripcion'],
										'formula'                                  => $row['formula_liquidacion'],
										'formula_original'                         => $row['formula_liquidacion'],
										'nivel_formula'                            => $row['nivel_formula_liquidacion'],
										'valor_concepto'                           => 0,
										'valor_provisionado'                       => $arrayConceptosAcumulados[$row['id']]['valor_concepto'],
										'base'                                     => $arrayConceptosAcumulados[$row['id']]['base'],
										'insert'                                   => (($row['codigo']<>'VC')? 'true' : 'false' ) ,
										'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
										'cuenta_colgaap'                           => $row['cuenta_colgaap'],
										'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
										'id_cuenta_niif'                           => $row['id_cuenta_niif'],
										'cuenta_niif'                              => $row['cuenta_niif'],
										'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
										'caracter'                                 => $row['caracter'],
										'centro_costos'                            => $row['centro_costos'],
										'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
										'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
										'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
										'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
										'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
										'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
										'caracter_contrapartida'                   => $row['caracter_contrapartida'],
										'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
										'naturaleza'                               => $row['naturaleza'],
										'imprimir_volante'                         => $row['imprimir_volante'],
										'id_cuenta_colgaap_liquidacion'            => $row['id_cuenta_colgaap_liquidacion'],
										'cuenta_colgaap_liquidacion'               => $row['cuenta_colgaap_liquidacion'],
										'descripcion_cuenta_colgaap_liquidacion'   => $row['descripcion_cuenta_colgaap_liquidacion'],
										'id_cuenta_niif_liquidacion'               => $row['id_cuenta_niif_liquidacion'],
										'cuenta_niif_liquidacion'                  => $row['cuenta_niif_liquidacion'],
										'descripcion_cuenta_niif_liquidacion'      => $row['descripcion_cuenta_niif_liquidacion'],
										'saldo_dias_laborados'                     => $arrayConceptosAcumulados[$row['id']]['saldo_dias_laborados'],
										);
		}

		// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
		$sql="SELECT id_concepto,
					nivel_formula,
					nivel_formula_liquidacion,
					formula_liquidacion,
					id_cuenta_colgaap,
					cuenta_colgaap,
					descripcion_cuenta_colgaap,
					id_cuenta_niif,
					cuenta_niif,
					descripcion_cuenta_niif,
					caracter,
					centro_costos,
					id_cuenta_contrapartida_colgaap,
					cuenta_contrapartida_colgaap,
					descripcion_cuenta_contrapartida_colgaap,
					id_cuenta_contrapartida_niif,
					cuenta_contrapartida_niif,
					descripcion_cuenta_contrapartida_niif,
					caracter_contrapartida,
					centro_costos_contrapartida,
					id_cuenta_colgaap_liquidacion,
					cuenta_colgaap_liquidacion,
					descripcion_cuenta_colgaap_liquidacion,
					id_cuenta_niif_liquidacion,
					cuenta_niif_liquidacion,
					descripcion_cuenta_niif_liquidacion
					FROM nomina_conceptos_grupos_trabajo
					WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$id            = $row['id_concepto'];
			$nivel_formula = $row['nivel_formula_liquidacion'];
			// echo $arrayConceptos[$nivel_formula][$id]['codigo'].':'.$arrayConceptos[$nivel_formula][$id]['cuenta_colgaap_liquidacion'].'-'.$row['cuenta_colgaap_liquidacion'].'<br>';
			// VALIDAR QUE EL CONCEPTO EXISTA EN EL ARRAY DE LOS CONCEPTOS
			if ($arrayConceptos[$nivel_formula][$id]['codigo']=='') {
				continue;
			}
			// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
			$arrayConceptos[$id]['formula']                                  = ($row['formula_liquidacion']=='')? $arrayConceptos[$nivel_formula][$id]['formula'] : $row['formula_liquidacion'];
			$arrayConceptos[$id]['formula_original']                         = ($row['formula_liquidacion']=='')? $arrayConceptos[$nivel_formula][$id]['formula_original'] : $row['formula_liquidacion'];
			$arrayConceptos[$id]['id_cuenta_colgaap']                        = $row['id_cuenta_colgaap'];
			$arrayConceptos[$id]['cuenta_colgaap']                           = $row['cuenta_colgaap'];
			$arrayConceptos[$id]['descripcion_cuenta_colgaap']               = $row['descripcion_cuenta_colgaap'];
			$arrayConceptos[$id]['id_cuenta_niif']                           = $row['id_cuenta_niif'];
			$arrayConceptos[$id]['cuenta_niif']                              = $row['cuenta_niif'];
			$arrayConceptos[$id]['descripcion_cuenta_niif']                  = $row['descripcion_cuenta_niif'];
			$arrayConceptos[$id]['caracter']                                 = $row['caracter'];
			$arrayConceptos[$id]['centro_costos']                            = $row['centro_costos'];
			$arrayConceptos[$id]['id_cuenta_contrapartida_colgaap']          = $row['id_cuenta_contrapartida_colgaap'];
			$arrayConceptos[$id]['cuenta_contrapartida_colgaap']             = $row['cuenta_contrapartida_colgaap'];
			$arrayConceptos[$id]['descripcion_cuenta_contrapartida_colgaap'] = $row['descripcion_cuenta_contrapartida_colgaap'];
			$arrayConceptos[$id]['id_cuenta_contrapartida_niif']             = $row['id_cuenta_contrapartida_niif'];
			$arrayConceptos[$id]['cuenta_contrapartida_niif']                = $row['cuenta_contrapartida_niif'];
			$arrayConceptos[$id]['descripcion_cuenta_contrapartida_niif']    = $row['descripcion_cuenta_contrapartida_niif'];
			$arrayConceptos[$id]['caracter_contrapartida']                   = $row['caracter_contrapartida'];
			$arrayConceptos[$id]['centro_costos_contrapartida']              = $row['centro_costos_contrapartida'];
			$arrayConceptos[$id]['id_cuenta_colgaap_liquidacion']            = $row['id_cuenta_colgaap_liquidacion'];
			$arrayConceptos[$id]['cuenta_colgaap_liquidacion']               = $row['cuenta_colgaap_liquidacion'];
			$arrayConceptos[$id]['descripcion_cuenta_colgaap_liquidacion']   = $row['descripcion_cuenta_colgaap_liquidacion'];
			$arrayConceptos[$id]['id_cuenta_niif_liquidacion']               = $row['id_cuenta_niif_liquidacion'];
			$arrayConceptos[$id]['cuenta_niif_liquidacion']                  = $row['cuenta_niif_liquidacion'];
			$arrayConceptos[$id]['descripcion_cuenta_niif_liquidacion']      = $row['descripcion_cuenta_niif_liquidacion'];

		}

		// print_r($arrayConceptos);
		// print_r($arrayConceptosAcumulados);

		foreach ($arrayConceptos as $id_concepto => $arrayConceptosResul) {
			if($arrayConceptosResul['insert'] == 'true' ){ continue; }
			// -----------------------------------//
			// REEMPLAZAR LAS VARIABLES GENERALES //
			// -----------------------------------//

			// BASE DEL VALOR GANADO
			$base_concepto=($arrayConceptosAcumulados[$id_concepto]['base']/$arrayConceptosAcumulados[$id_concepto]['saldo_dias_laborados'])*30;
			$arrayConceptosResul['formula']=str_replace('{BL}', $base_concepto, $arrayConceptosResul['formula']);
			// DIAS LABORADOS
			$arrayConceptosResul['formula']=str_replace('{DL}', $arrayConceptosAcumulados[$id_concepto]['saldo_dias_laborados'], $arrayConceptosResul['formula']);

			// RECORRER ARRAY PARA REEMPLAZAR LOS CONCEPTOS DE LA FORMULA
			// NIVEL DE LA FORMULA DEL CONCEPTO
			foreach ($arrayConceptos as $nivel_formula_search => $arrayConceptosArray_search) {
				foreach ($arrayConceptosArray_search as $id_concepto_search => $arrayConceptosResul_search) {

					// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
					if($arrayConceptosResul_search['valor_concepto']<0 ){ continue; }

					// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR
					if($nivel_formula<$nivel_formula_search){ continue; }
					// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
					$arrayConceptosResul['formula']=str_replace('['.$arrayConceptosResul_search['codigo'].']', $arrayConceptos[$nivel_formula_search][$id_concepto_search]['valor_concepto'], $arrayConceptosResul['formula']);

				}
			}//->FIN FOR EACH PARA BUCAR LOS VALORES DE LOS CONCEPTOS DE LA FORMULA

			//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
			$search_var_concepto=strpos($arrayConceptosResul['formula'], '[');
			if ($search_var_concepto===false) {
				// CALCULAR LA FORMULA
				$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
				// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
				if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
					echo '<script>
							alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: ('.$arrayConceptosResul['formula'].')");
							console.log("'.$arrayConceptosResul['formula'].' ");
						</script>';
					// continue;
				}

				$valor_concepto = $valor_concepto-$arrayConceptosLiquidados[$id_concepto]['valor_concepto'];
				$base = $arrayConceptosAcumulados[$id_concepto]['base']-$arrayConceptosLiquidados[$id_concepto]['base'];

				$arrayConceptos[$nivel_formula][$id_concepto]['valor_concepto']=$valor_concepto;
				$concepto = $arrayConceptosResul['concepto'];
				$valueInsert.="('$id_planilla',
								'$id_empleado',
								'$id_contrato',
								'$id_concepto',
								'".$arrayConceptosResul['codigo']."',
								'".$arrayConceptosResul['concepto']."',
								'".$arrayConceptosAcumulados[$id_concepto]['valor_concepto']."',
								'".$valor_concepto."',
								'".$valor_concepto."',
								'".$base."',
								'".$arrayConceptosResul['formula']."',
								'".$arrayConceptosResul['formula_original']."',
								'".$nivel_formula."',
								'$id_sucursal',
								'$id_empresa',
								'".$arrayConceptosResul['id_cuenta_colgaap']."',
								'".$arrayConceptosResul['cuenta_colgaap']."',
								'".$arrayConceptosResul['descripcion_cuenta_colgaap']."',
								'".$arrayConceptosResul['id_cuenta_niif']."',
								'".$arrayConceptosResul['cuenta_niif']."',
								'".$arrayConceptosResul['descripcion_cuenta_niif']."',
								'".$arrayConceptosResul['caracter']."',
								'".$arrayConceptosResul['centro_costos']."',
								'".$arrayConceptosResul['id_cuenta_contrapartida_colgaap']."',
								'".$arrayConceptosResul['cuenta_contrapartida_colgaap']."',
								'".$arrayConceptosResul['descripcion_cuenta_contrapartida_colgaap']."',
								'".$arrayConceptosResul['id_cuenta_contrapartida_niif']."',
								'".$arrayConceptosResul['cuenta_contrapartida_niif']."',
								'".$arrayConceptosResul['descripcion_cuenta_contrapartida_niif']."',
								'".$arrayConceptosResul['caracter_contrapartida']."',
								'".$arrayConceptosResul['centro_costos_contrapartida']."',
								'".$arrayConceptosResul['id_cuenta_colgaap_liquidacion']."',
								'".$arrayConceptosResul['cuenta_colgaap_liquidacion']."',
								'".$arrayConceptosResul['descripcion_cuenta_colgaap_liquidacion']."',
								'".$arrayConceptosResul['id_cuenta_niif_liquidacion']."',
								'".$arrayConceptosResul['cuenta_niif_liquidacion']."',
								'".$arrayConceptosResul['descripcion_cuenta_niif_liquidacion']."',
								'".$arrayConceptosResul['naturaleza']."',
								'".$arrayConceptosResul['imprimir_volante']."',
								'".$arrayConceptosResul['saldo_dias_laborados']."',
								'false'
								)";
			}

		}// FIN SEGUNDO FOR EACH

		$sql="INSERT INTO nomina_planillas_liquidacion_empleados_conceptos
				(id_planilla,
				id_empleado,
				id_contrato,
				id_concepto,
				codigo_concepto,
				concepto,
				valor_concepto,
				valor_concepto_ajustado,
				saldo_restante,
				base,
				formula,
				formula_original,
				nivel_formula,
				id_sucursal,
				id_empresa,
				id_cuenta_colgaap,
				cuenta_colgaap,
				descripcion_cuenta_colgaap,
				id_cuenta_niif,
				cuenta_niif,
				descripcion_cuenta_niif,
				caracter,
				centro_costos,
				id_cuenta_contrapartida_colgaap,
				cuenta_contrapartida_colgaap,
				descripcion_cuenta_contrapartida_colgaap,
				id_cuenta_contrapartida_niif,
				cuenta_contrapartida_niif,
				descripcion_cuenta_contrapartida_niif,
				caracter_contrapartida,
				centro_costos_contrapartida,
				id_cuenta_colgaap_liquidacion,
				cuenta_colgaap_liquidacion,
				descripcion_cuenta_colgaap_liquidacion,
				id_cuenta_niif_liquidacion,
				cuenta_niif_liquidacion,
				descripcion_cuenta_niif_liquidacion,
				naturaleza,
				imprimir_volante,
				dias_laborados,
				cierra_total_provision)
				VALUES $valueInsert";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$id_row=$mysql->insert_id();
			echo "<script>
					document.getElementById('concepto_vacaciones').dataset.id = '$id_concepto';
					document.getElementById('concepto_vacaciones').value      = '$concepto';
				</script>";
		}
		else{
			echo "<script>alert('Error al cargar el concepto de vacaciones');</script>";
		}

		echo '<img src="../nomina/img/load_vacations.png" style="margin: 1px 0px 0px 2px;">
				<script>cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.');</script>';

		// echo $valor_concepto;
	}

	function helpTipoPago(){
		echo '
			<style>
				.container{
					width  : 100%;
					height : 100%;
				}
				.title{
					width      : 100%;
					text-align : center;
					font-weight : bold;
					padding : 10px 0 10px 0;
				}
				.desc{
					width: 95%;
					padding-left: 10px;
				}
			</style>

			<div class="container">
				<div class="title">VACACIONES COMPLETAS</div>
				<div class="desc">
					Al seleccionar la opcion de vacaciones completas, el sistema interpreta que el empleado tomara la totalidad de sus 15 dias habiles de vacaciones,
					de manera que el proximo libro de vacaciones tomara el periodo siguiente de vacaciones, en cuanto a la contabilidad, se cerrara de forma autormatica
					todas las provisiones que corresponden a las vacaciones, ademas de realizar de forma automatica el ajuste contable respectivo.
				</div>
				<div class="title">VACACIONES PARCIALES</div>
				<div class="desc">
					En las vacaciones parciales, se pueden dividir el periodo de vacaciones en dos secciones, es decir, pude disfrutar sus vacaciones de forma segmentada o pagada, por ejemplo
					si un empleado quiere, puede disfrutar 7 dias de vacaciones en un mes y al mes siguiente los dias restantes, o en el tiempo que desee, SE DEBE TENER EN CUENTA que
					para este tipo de pago de vacaciones, el sistema toma las provisiones pero no realiza ajuste, de manera
					que la persona encargada de la contabilidad ESTA OBLIGADA a realizar los ajustes contables respectivos en una nota general para ajustar la provision.
				</div>
			</div>
		';
	}

    //////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////

	//==================== FUNCION PARA GENERAR LA PLANILLA DE NOMINA ================//
    function terminarGenerar($id_planilla,$id_empresa,$id_sucursal,$link){

    	// CONSULTAR SI LA LIQUIDACION TIENE CONCEPTOS TIPO DEDUCCION Y SI ESTAN TOTALMENTE CONFIGURADOS
    	$sql="SELECT id_empleado,documento_empleado,nombre_empleado FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa ";
    	$query=mysql_query($sql,$link);
    	while ($row=mysql_fetch_array($query)) {
    		$whereIdEmpleados.=($whereIdEmpleados=='')? 'id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
    		$arrayInfoEmpleados[$row['id_empleado']] = array('documento_empleado' => $row['documento_empleado'],
    														 'nombre_empleado'	  => $row['nombre_empleado'] );
    	}

    	$sql="SELECT id_concepto,concepto,codigo_concepto,valor_concepto,id_empleado,id_prestamo
    			FROM nomina_planillas_liquidacion_empleados_conceptos
    			WHERE activo=1 AND naturaleza='Deduccion' AND id_planilla=$id_planilla AND id_empresa=$id_empresa AND ($whereIdEmpleados)";
    	$query=mysql_query($sql,$link);

    	while ($row=mysql_fetch_array($query)) {
    		$whereIdConceptos.=($whereIdConceptos=='')? 'id_concepto='.$row['id_concepto'] : ' OR id_concepto='.$row['id_concepto'] ;
    		$cont_conceptos++;
    		$arrayInfoConceptos[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] = array('concepto'       => $row['concepto'],
					    														 						'codigo_concepto' => $row['codigo_concepto'],
					    														 						'valor_concepto' => $row['valor_concepto'],
					    														 						'saldo'          => $row['valor_concepto'] );
    	}

    	$sql="SELECT id_empleado,id_concepto,id_concepto_deducir,valor_deducir,cuenta_colgaap,cuenta_niif,id_prestamo
    				FROM nomina_planillas_liquidacion_conceptos_deducir
    				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND ($whereIdEmpleados) AND ($whereIdConceptos)";
    	$query=mysql_query($sql,$link);
    	while ($row=mysql_fetch_array($query)) {
    		$arrayInfoConceptos[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']]['saldo']-=$row['valor_deducir'];


    		if($arrayDeduccionesColgaap[$row['id_empleado']][$row['id_concepto_deducir']][$row['cuenta_colgaap']]['valor_deducir'] > 0){
				$arrayDeduccionesColgaap[$row['id_empleado']][$row['id_concepto_deducir']][$row['cuenta_colgaap']]['valor_deducir'] += $row['valor_deducir'];
    		}
    		else{
    			$arrayDeduccionesColgaap[$row['id_empleado']][$row['id_concepto_deducir']][$row['cuenta_colgaap']] = array(
																														'valor_deducir'  => $row['valor_deducir'],
																														);
    		}

    		if ($arrayDeduccionesNiif[$row['id_empleado']][$row['id_concepto_deducir']][$row['cuenta_niif']]['valor_deducir'] > 0) {
    			$arrayDeduccionesNiif[$row['id_empleado']][$row['id_concepto_deducir']][$row['cuenta_niif']]['valor_deducir'] +=$row['valor_deducir'];
    		}
    		else{
    			$arrayDeduccionesNiif[$row['id_empleado']][$row['id_concepto_deducir']][$row['cuenta_niif']] = array(
																											'valor_deducir'  => $row['valor_deducir'],
																											);
    		}


    	}

    	foreach ($arrayInfoConceptos as $id_empleado => $arrayInfoConceptosArray) {
    		foreach ($arrayInfoConceptosArray as $id_concepto => $arrayInfoConceptosArray2) {
    			foreach ($arrayInfoConceptosArray2 as $id_prestamo => $arrayResul) {

	    			if ($arrayResul['saldo']>0) {
	    				echo '<script>
	    						alert("Aviso!\nEl concepto: '.$arrayResul['codigo_concepto'].' - '.$arrayResul['concepto'].'\ndel empleado: '.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].'\nNo se configurado totalmente le resta un saldo de '.$arrayResul['saldo'].'");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						exit;
	    			}
	    			else if ($arrayResul['saldo']<0) {
	    				echo '<script>
	    						alert("Aviso!\nEl concepto: '.$arrayResul['codigo_concepto'].' - '.$arrayResul['concepto'].'\ndel empleado: '.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].'\nSe configuro incorrectamente tiene saldo negativo '.$arrayResul['saldo'].'");
								document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
							</script>';
						exit;
	    			}

    			}
    		}
    	}


		// CONSULTAR LAS CUENTAS PERMITIDAS EN EL COMPROBANTE DE EGRESO
    	$sql="SELECT cuenta FROM configuracion_comprobante_egreso WHERE activo=1 AND id_empresa=$id_empresa";
    	$query=mysql_query($sql,$link);
    	$i=0;
    	while ($row=mysql_fetch_array($query)) {
    		$i++;
    		$arrayCuentaComprobante[$i]=$row['cuenta'];

    	}

    	//ARMAR EL QUERY DEL ASIENTO
    	$sql="SELECT
				NC.concepto_ajustable,
				NPLEC.codigo_concepto,
				NPLEC.concepto,
				NPLEC.valor_concepto,
				NPLEC.valor_concepto_ajustado,
				NPLEC.id_tercero,
				NPLEC.id_empleado_cruce,
				NPLEC.id_empleado_cruce_contrapartida,
				NPLEC.id_empleado,
				NPLEC.id_tercero_contrapartida,
				NPLEC.id_concepto,
				NPLEC.caracter,
				NPLEC.caracter_contrapartida,
				NPLEC.cuenta_colgaap,
				NPLEC.cuenta_niif,
				NPLEC.cuenta_contrapartida_colgaap,
				NPLEC.cuenta_contrapartida_niif,
				NPLEC.id_centro_costos,
				NPLEC.id_centro_costos_contrapartida,
				NPLEC.naturaleza,
				NPLEC.id_prestamo,
				NPLEC.id_tercero_cruce_liquidacion,
				NPLEC.id_empleado_cruce_liquidacion,
				NPLEC.cuenta_colgaap_liquidacion,
				NPLEC.cuenta_niif_liquidacion,
				NPLEC.cierra_total_provision,
				NC.tercero,
				NC.tercero_cruce,
				NC.tercero_cruce_liquidacion,
				NC.centro_costos,
				NC.centro_costos_contrapartida

			FROM
				nomina_planillas_liquidacion_empleados_conceptos AS NPLEC,
				nomina_conceptos AS NC
			WHERE
				NPLEC.id_planilla = $id_planilla
			AND NPLEC.id_empresa  = $id_empresa
			AND NC.id             = NPLEC.id_concepto
			AND NC.id_empresa     = $id_empresa";
    	$query=mysql_query($sql,$link);
    	$cont_conceptos=0;

    	while ($row=mysql_fetch_array($query)){
    		$cont_conceptos++;

    		// VARIABLES CON LOS RESULTADOS DE LA CONSULTA
			$id_empleado                     = $row['id_empleado'];
			$id_concepto                     = $row['id_concepto'];
			$id_tercero                      = $row['id_tercero'];
			$id_tercero_contrapartida        = $row['id_tercero_contrapartida'];
			$id_empleado_cruce               = $row['id_empleado_cruce'];
			$id_empleado_cruce_contrapartida = $row['id_empleado_cruce_contrapartida'];
			$cuenta_colgaap                  = $row['cuenta_colgaap'];
			$cuenta_contrapartida_colgaap    = $row['cuenta_contrapartida_colgaap'];
			$cuenta_niif                     = $row['cuenta_niif'];
			$cuenta_contrapartida_niif       = $row['cuenta_contrapartida_niif'];
			$id_prestamo                     = $row['id_prestamo'];
			$id_tercero_cruce_liquidacion    = $row['id_tercero_cruce_liquidacion'];
			$cuenta_colgaap_liquidacion      = $row['cuenta_colgaap_liquidacion'];
			$cuenta_niif_liquidacion         = $row['cuenta_niif_liquidacion'];
			$valor_concepto                  = $row['valor_concepto'];
			$valor_concepto_ajustado         = $row['valor_concepto_ajustado'];
			$caracter                        = $row['caracter'];
			$caracter_contrapartida          = $row['caracter_contrapartida'];
			$naturaleza                      = $row['naturaleza'];
			$tercero                         = $row['tercero'];
			$tercero_cruce                   = $row['tercero_cruce'];
			$tercero_cruce_liquidacion       = $row['tercero_cruce_liquidacion'];
			$id_empleado_cruce_liquidacion   = $row['id_empleado_cruce_liquidacion'];
			$id_empleado_nomina              = $row['id_empleado'];
			$codigo_concepto                 = $row['codigo_concepto'];
			$concepto                        = $row['concepto'];
			$centro_costos                   = $row['centro_costos'];
			$centro_costos_contrapartida     = $row['centro_costos_contrapartida'];
			$concepto_ajustable              = $row['concepto_ajustable'];
			$cierra_total_provision          = $row['cierra_total_provision'];

			// VALIDAR SI EL CONCEPTO SE CREO CON CRUCE CON UNA ENTIDAD ENTONCES QUE EL ID SEA DIFERENTE AL DEL CLIENTE
			if ($tercero=='Entidad' && $id_empleado_cruce==$id_tercero) {
				echo '<script>
							alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
				exit;
			}
			if ($tercero_cruce=='Entidad' && $id_empleado_cruce==$id_tercero_contrapartida) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			if ($tercero_cruce_liquidacion=='Entidad' && $id_empleado_cruce==$id_tercero_cruce_liquidacion) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// VALIDAR SI EL CONCEPTO NECESITA CENTRO DE COSTOS Y SI NO SE CREO EL CCOS EN EL CONTRATO DEL EMPLEADO
			if ($centro_costos=='true' && $row['id_centro_costos']=='') {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayInfoEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado_nomina]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			if ($centro_costos_contrapartida=='true' && $row['id_centro_costos_contrapartida']=='') {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayInfoEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado_nomina]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// VALIDAR QUE EXISTA COMO TERCERO EL EMPLEADO
			if ($id_empleado=='' || $id_tercero_contrapartida=='') {
				// echo '<script>alert("Error!\nLos siguientes empleados no estan como tercero:\n '.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].' \n'.$arrayInfoEmpleados[$id_empleado_contrapartida]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado_contrapartida]['nombre_empleado'].'");</script>';
				echo '<script>
							alert("'.$arrayInfoEmpleados[$id_empleado]['documento_empleado'].' - '.$arrayInfoEmpleados[$id_empleado]['nombre_empleado'].' \nNo esta creado como tercero!\nVerifiquelos dede el modulo de empleados");
							document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						</script>';
				exit;
			}


			echo "<script>console.log('$codigo_concepto  -> $cierra_total_provision');</script>";
			//////////////////////////////////////////////////////////////////////////////
    		// SI LAS VACIONES SON PAGADAS PARCIALES, ENTONCES SE CONTABILIZA DIFERENTE //
    		//////////////////////////////////////////////////////////////////////////////
			if ( $codigo_concepto == 'VC' && $naturaleza == 'Provision' && $cierra_total_provision == 'false' ) {

				if ($caracter=='credito') {
					$id_ccos_debito           = $row['id_centro_costos'];
					$id_empleado_debito       = $id_tercero;
					$cuenta_debito            = $cuenta_colgaap;

					$id_ccos_credito          = '';
					$id_empleado_credito      = $id_tercero_cruce_liquidacion;
					$cuenta_credito           = $cuenta_colgaap_liquidacion;

					$id_ccos_debito_niif      = $row['id_centro_costos'];
					$id_empleado_debito_niif  = $id_tercero;
					$cuenta_debito_niif       = $cuenta_niif;

					$id_ccos_credito_niif     = '';
					$id_empleado_credito_niif = $id_tercero_cruce_liquidacion;
					$cuenta_credito_niif      = $cuenta_niif_liquidacion;
    			}
    			else{
					$id_ccos_debito           = $row['id_centro_costos_contrapartida'];
					$id_empleado_debito       = $id_tercero_contrapartida;
					$cuenta_debito            = $cuenta_contrapartida_colgaap;

					$id_ccos_credito          = '';
					$id_empleado_credito      = $id_tercero_cruce_liquidacion;
					$cuenta_credito           = $cuenta_colgaap_liquidacion;

					$id_ccos_debito_niif      = $row['id_centro_costos_contrapartida'];
					$id_empleado_debito_niif  = $id_tercero_contrapartida;
					$cuenta_debito_niif       = $cuenta_contrapartida_niif;

					$id_ccos_credito_niif     = '';
					$id_empleado_credito_niif = $id_tercero_cruce_liquidacion;
					$cuenta_credito_niif      = $cuenta_niif_liquidacion;
    			}

    			echo '<script>
					var array = {};

					array.codigo_concepto       = {};
					array.naturaleza            = {};
					array.cuenta_debito         = {};
					array.cuenta_credito        = {};

					array.codigo_concepto.valor = "'.$codigo_concepto.'";
					array.naturaleza.valor      = "'.$naturaleza.'";
					array.cuenta_debito.valor   = "'.$cuenta_debito.'";
					array.cuenta_credito.valor  = "'.$cuenta_credito.'";

					// console.table(array);

				</script>';

				// ARRAY CON LOS VALORES PARA LOS ASIENTOS COLGAAP
				$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['ccos']                           = $id_ccos_debito;
				$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['debito']                         += $valor_concepto_ajustado;
				$arrayAsientosColgaap[$id_tercero_cruce_liquidacion][$cuenta_credito]['credito'] += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_credito]['valor_deducir']);

				// ARRAY CON LOS VALORES PARA LOS ASIENTOPS NIIF
				$arrayAsientosNiif[$id_empleado_debito_niif][$cuenta_debito_niif]['ccos']                    = $id_ccos_debito;
				$arrayAsientosNiif[$id_empleado_debito_niif][$cuenta_debito_niif]['debito']                  += $valor_concepto_ajustado;
				$arrayAsientosNiif[$id_tercero_cruce_liquidacion][$cuenta_credito_niif]['credito']       += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_credito_niif]['valor_deducir']);

				// ARRAY CON LAS CUENTAS DE LA TABLA CONTABILIZACION
				// $arrayNominaContabilizacion[$id_empleado_debito][$cuenta_debito]['debito']                                           += $valor_concepto_ajustado;
				$arrayNominaContabilizacion[$id_tercero_cruce_liquidacion][$cuenta_credito]['credito'] += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_credito]['valor_deducir']);

				// NATURALEZA DE LA CUENTA (DEVENGO,DEDUCCION,APROPIACION,PROVISION), INDICE EL TERCERO Y LA CUENTA COLGAAP
				// $arrayNaturaleza[$id_empleado_debito][$cuenta_debito]                             = $row['naturaleza'];
				$arrayNaturaleza[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion] = $row['naturaleza'];

				// ARRAY CON LOS DEMAS DATOS DEL INSERT
				// $arrayDatosInsert[$cuenta_debito]               = array('cuenta_niif'=>$cuenta_debito_niif,'id_concepto'=>$row['id_concepto'] );
				$arrayDatosInsert[$cuenta_colgaap_liquidacion] = array('cuenta_niif'=> $cuenta_niif_liquidacion,'id_concepto'=>$row['id_concepto'] );

				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// $arrayAsientosColgaap[$id_tercero][$cuenta_colgaap]['ccos']                                              = $row['id_centro_costos'];
				// $arrayAsientosColgaap[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap]['ccos']                  = $row['id_centro_costos_contrapartida'];
				// $arrayAsientosColgaap[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
				// $arrayAsientosColgaap[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);


				// // ARRAY CON LOS VALORES PARA LOS ASIENTOPS NIIF
				// $arrayAsientosNiif[$id_tercero][$cuenta_niif]['ccos']                                           = $row['id_centro_costos'];
				// $arrayAsientosNiif[$id_tercero_contrapartida][$cuenta_contrapartida_niif]['ccos']               = $row['id_centro_costos_contrapartida'];
				// $arrayAsientosNiif[$id_tercero][$cuenta_niif][$caracter]                                           += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_niif]['valor_deducir']);
				// $arrayAsientosNiif[$id_tercero_contrapartida][$cuenta_contrapartida_niif][$caracter_contrapartida] += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_contrapartida_niif]['valor_deducir']);

				// $arrayNominaContabilizacion[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
				// $arrayNominaContabilizacion[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);

				// // NATURALEZA DE LA CUENTA (DEVENGO,DEDUCCION,APROPIACION,PROVISION), INDICE EL TERCERO Y LA CUENTA COLGAAP
				// $arrayNaturaleza[$id_tercero][$cuenta_colgaap]                             = $row['naturaleza'];
				// $arrayNaturaleza[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap] = $row['naturaleza'];

				// // ARRAY CON LOS DEMAS DATOS DEL INSERT
				// $arrayDatosInsert[$cuenta_colgaap]               = array('cuenta_niif'=>$cuenta_niif,'id_concepto'=>$row['id_concepto'] );
				// $arrayDatosInsert[$cuenta_contrapartida_colgaap] = array('cuenta_niif'=> $cuenta_contrapartida_niif,'id_concepto'=>$row['id_concepto'] );
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			}
			////////////////////////////////////////////////////////////////////
    		// SEPARAR LAS PROVISIONES POR QUE SU CONTABILIZACION ES ESPECIAL //
    		////////////////////////////////////////////////////////////////////
    		else if ($naturaleza=='Provision') {
    			//==============================================================================================//
    			// LA CUENTA CONFIGURADA COMO LIQUIDACION VA A SER CREDITO, Y LA CUENTA CREDITO VA A SER DEBITO //
    			//==============================================================================================//

    			if ($caracter=='credito') {
					$id_ccos_debito           = $row['id_centro_costos'];
					$id_empleado_debito       = $id_tercero;
					$cuenta_debito            = $cuenta_colgaap;

					$id_ccos_credito          = $row['id_centro_costos_contrapartida'];
					$id_empleado_credito      = $id_tercero_contrapartida;
					$cuenta_credito           = $cuenta_contrapartida_colgaap;

					$id_ccos_debito_niif      = $row['id_centro_costos'];
					$id_empleado_debito_niif  = $id_tercero;
					$cuenta_debito_niif       = $cuenta_niif;

					$id_ccos_credito_niif     = $row['id_centro_costos_contrapartida'];
					$id_empleado_credito_niif = $id_tercero_contrapartida;
					$cuenta_credito_niif      = $cuenta_contrapartida_niif;
    			}
    			else{
					$id_ccos_debito           = $row['id_centro_costos_contrapartida'];
					$id_empleado_debito       = $id_tercero_contrapartida;
					$cuenta_debito            = $cuenta_contrapartida_colgaap;

					$id_ccos_credito          = $row['id_centro_costos'];
					$id_empleado_credito      = $id_tercero;
					$cuenta_credito           = $cuenta_colgaap;

					$id_ccos_debito_niif      = $row['id_centro_costos_contrapartida'];
					$id_empleado_debito_niif  = $id_tercero_contrapartida;
					$cuenta_debito_niif       = $cuenta_contrapartida_niif;

					$id_ccos_credito_niif     = $row['id_centro_costos'];
					$id_empleado_credito_niif = $id_tercero;
					$cuenta_credito_niif      = $cuenta_niif;
    			}

    			//=============================================//
				// VALIDAR SI SE DEBE HACER UN AJUSTE CONTABLE //
				//=============================================//
				if ($valor_concepto==$valor_concepto_ajustado) {

					// ARRAY CON LOS VALORES PARA LOS ASIENTOS COLGAAP
					$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['ccos']                           = $id_ccos_debito;
					$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['debito']                         += $valor_concepto;
					$arrayAsientosColgaap[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion]['credito'] += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap_liquidacion]['valor_deducir']);

					// ARRAY CON LOS VALORES PARA LOS ASIENTOPS NIIF
					$arrayAsientosNiif[$id_empleado_debito_niif][$cuenta_debito_niif]['ccos']                    = $id_ccos_debito;
					$arrayAsientosNiif[$id_empleado_debito_niif][$cuenta_debito_niif]['debito']                  += $valor_concepto;
					$arrayAsientosNiif[$id_tercero_cruce_liquidacion][$cuenta_niif_liquidacion]['credito']       += ($valor_concepto-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_niif_liquidacion]['valor_deducir']);

					// ARRAY CON LAS CUENTAS DE LA TABLA CONTABILIZACION
					// $arrayNominaContabilizacion[$id_empleado_debito][$cuenta_debito]['debito']                                           += $valor_concepto;
					$arrayNominaContabilizacion[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion]['credito'] += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_colgaap_liquidacion]['valor_deducir']);

					// NATURALEZA DE LA CUENTA (DEVENGO,DEDUCCION,APROPIACION,PROVISION), INDICE EL TERCERO Y LA CUENTA COLGAAP
					// $arrayNaturaleza[$id_empleado_debito][$cuenta_debito]                             = $row['naturaleza'];
					$arrayNaturaleza[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion] = $row['naturaleza'];

					// ARRAY CON LOS DEMAS DATOS DEL INSERT
					// $arrayDatosInsert[$cuenta_debito]               = array('cuenta_niif'=>$cuenta_debito_niif,'id_concepto'=>$row['id_concepto'] );
					$arrayDatosInsert[$cuenta_colgaap_liquidacion] = array('cuenta_niif'=> $cuenta_niif_liquidacion,'id_concepto'=>$row['id_concepto'] );

				}
				// SI EL VALOR EL CONCEPTO ES DIFERENTE AL VALOR AJUSTADO, SE DEBE REALIZR UN AJUSTE CONTABLE
				else{
					$diferencia      = $valor_concepto_ajustado-$valor_concepto;

					$ajuste          = $valor_concepto_ajustado-$valor_concepto;
					$caracter_ajuste = ($ajuste<0)? 'credito' : 'debito' ;
					$ajuste          = abs($ajuste);

					$arrayAsientosColgaap[$id_empleado_credito][$cuenta_credito][$caracter_ajuste]        += $ajuste;
					$arrayAsientosNiif[$id_empleado_credito_niif][$cuenta_credito_niif][$caracter_ajuste] += $ajuste;
					// echo '<script>console.log("in '.$ajuste.' '.$cuenta_credito.' '.$row['concepto'].'");</script>';

					$arrayAsientosColgaap[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion]['credito'] += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap_liquidacion]['valor_deducir']);
					$arrayAsientosNiif[$id_tercero_cruce_liquidacion][$cuenta_niif_liquidacion]['credito']       += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_niif_liquidacion]['valor_deducir']);

					// ARRAY CON LOS VALORES PARA LOS ASIENTOS COLGAAP
					$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['ccos']          = $id_ccos_debito;
					$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['naturaleza']    = $naturaleza;
					$arrayAsientosColgaap[$id_empleado_debito][$cuenta_debito]['debito']        += $valor_concepto;

					// CUENTA CON EL AJUSTE
					$arrayAsientosColgaap[$id_empleado_credito][$cuenta_credito]['ccos']        = $id_ccos_credito;


					// ARRAY CON LOS VALORES PARA LOS ASIENTOS NIIF
					$arrayAsientosNiif[$id_empleado_debito_niif][$cuenta_debito_niif]['ccos']   = $id_ccos_debito;
					$arrayAsientosNiif[$id_empleado_debito_niif][$cuenta_debito_niif]['debito'] += $valor_concepto;

					// CUENTA CON EL AJUSTE
					$arrayAsientosNiif[$id_empleado_credito_niif][$cuenta_credito_niif]['ccos'] = $id_ccos_credito;



					// ARRAY CON LAS CUENTAS DE LA TABLA CONTABILIZACION
					// $arrayNominaContabilizacion[$id_empleado_debito][$cuenta_debito]['debito']                                           += $valor_concepto;
					$arrayNominaContabilizacion[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion]['credito'] += ($valor_concepto_ajustado-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_colgaap_liquidacion]['valor_deducir']);

					// NATURALEZA DE LA CUENTA (DEVENGO,DEDUCCION,APROPIACION,PROVISION), INDICE EL TERCERO Y LA CUENTA COLGAAP
					// $arrayNaturaleza[$id_empleado_debito][$cuenta_debito]                             = $row['naturaleza'];
					$arrayNaturaleza[$id_tercero_cruce_liquidacion][$cuenta_colgaap_liquidacion] = $row['naturaleza'];

					// ARRAY CON LOS DEMAS DATOS DEL INSERT
					// $arrayDatosInsert[$cuenta_debito]  = array('cuenta_niif'=>$cuenta_debito_niif,'id_concepto'=>$row['id_concepto'] );
					$arrayDatosInsert[$cuenta_colgaap_liquidacion] = array('cuenta_niif'=> $cuenta_niif_liquidacion,'id_concepto'=>$row['id_concepto'] );

				}
    		}

    		//////////////////////////////////////////////////////////////
    		// SI NO ES UNA PROVISION, TIENE UNA CONTABILIZACION NORMAL //
    		//////////////////////////////////////////////////////////////
    		else{

    			if ($naturaleza=='Deduccion') {

    				if ($caracter=='debito'){
						$arrayAsientosColgaap[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap]['ccos']                        = $row['id_centro_costos_contrapartida'];
						$arrayAsientosColgaap[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida]       += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);
						$arrayAsientosNiif[$id_tercero_contrapartida][$cuenta_contrapartida_niif]['ccos']                              = $row['id_centro_costos_contrapartida'];
						$arrayAsientosNiif[$id_tercero_contrapartida][$cuenta_contrapartida_niif][$caracter_contrapartida]             += ($valor_concepto-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_contrapartida_niif]['valor_deducir']);
						// $arrayNominaContabilizacion[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
						// $arrayNominaContabilizacion[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);
						$arrayNaturaleza[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap]                                     = $row['naturaleza'];

    				}
    				else{
						$arrayAsientosColgaap[$id_tercero][$cuenta_colgaap]['ccos']                                                    = $row['id_centro_costos'];
						$arrayAsientosColgaap[$id_tercero][$cuenta_colgaap][$caracter]                                                 += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
						$arrayAsientosNiif[$id_tercero][$cuenta_niif]['ccos']                                                          = $row['id_centro_costos'];
						$arrayAsientosNiif[$id_tercero][$cuenta_niif][$caracter]                                                       += ($valor_concepto-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_niif]['valor_deducir']);
						// $arrayNominaContabilizacion[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
						// $arrayNominaContabilizacion[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto_ajustado-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);
						$arrayNaturaleza[$id_tercero][$cuenta_colgaap]                                                                 = $row['naturaleza'];

    				}

						$arrayNominaContabilizacion[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
						$arrayNominaContabilizacion[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);


					// ARRAY CON LOS DEMAS DATOS DEL INSERT
					$arrayDatosInsert[$cuenta_colgaap]               = array('cuenta_niif'=>$cuenta_niif,'id_concepto'=>$row['id_concepto'] );
					$arrayDatosInsert[$cuenta_contrapartida_colgaap] = array('cuenta_niif'=> $cuenta_contrapartida_niif,'id_concepto'=>$row['id_concepto'] );
    			}
    			else{
    				// ARRAY CON LOS VALORES PARA LOS ASIENTOS COLGAAP
					$arrayAsientosColgaap[$id_tercero][$cuenta_colgaap]['ccos']                                              = $row['id_centro_costos'];
					$arrayAsientosColgaap[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap]['ccos']                  = $row['id_centro_costos_contrapartida'];
					$arrayAsientosColgaap[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
					$arrayAsientosColgaap[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);


					// ARRAY CON LOS VALORES PARA LOS ASIENTOPS NIIF
					$arrayAsientosNiif[$id_tercero][$cuenta_niif]['ccos']                                           = $row['id_centro_costos'];
					$arrayAsientosNiif[$id_tercero_contrapartida][$cuenta_contrapartida_niif]['ccos']               = $row['id_centro_costos_contrapartida'];
					$arrayAsientosNiif[$id_tercero][$cuenta_niif][$caracter]                                           += ($valor_concepto-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_niif]['valor_deducir']);
					$arrayAsientosNiif[$id_tercero_contrapartida][$cuenta_contrapartida_niif][$caracter_contrapartida] += ($valor_concepto-$arrayDeduccionesNiif[$id_empleado][$id_concepto][$cuenta_contrapartida_niif]['valor_deducir']);

					// ARRAY CON LOS VALORES ACUMULADOS DE LOS CONCEPTOS, CON 4 CAPAS, ID_TERCERO, EMPLEADO QUE CRUZA,CUENTA Y SI ES DEBITO O CREDITO
					// $arrayNominaContabilizacion[$id_tercero][$id_empleado_cruce][$cuenta_colgaap][$caracter]                                           += $valor_concepto;
					// $arrayNominaContabilizacion[$id_tercero_contrapartida][$id_empleado_cruce][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += $valor_concepto;

					$arrayNominaContabilizacion[$id_tercero][$cuenta_colgaap][$caracter]                                           += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir']);
					$arrayNominaContabilizacion[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += ($valor_concepto-$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir']);

					// NATURALEZA DE LA CUENTA (DEVENGO,DEDUCCION,APROPIACION,PROVISION), INDICE EL TERCERO Y LA CUENTA COLGAAP
					$arrayNaturaleza[$id_tercero][$cuenta_colgaap]                             = $row['naturaleza'];
					$arrayNaturaleza[$id_tercero_contrapartida][$cuenta_contrapartida_colgaap] = $row['naturaleza'];

					// ARRAY CON LOS DEMAS DATOS DEL INSERT
					$arrayDatosInsert[$cuenta_colgaap]               = array('cuenta_niif'=>$cuenta_niif,'id_concepto'=>$row['id_concepto'] );
					$arrayDatosInsert[$cuenta_contrapartida_colgaap] = array('cuenta_niif'=> $cuenta_contrapartida_niif,'id_concepto'=>$row['id_concepto'] );
    			}

    			// echo $id_tercero.' - '.$cuenta_colgaap.' - '.$caracter.' - '.$valor_concepto.' - '.$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_colgaap]['valor_deducir'].' <br>';
    			// echo $id_tercero_contrapartida.' - '.$cuenta_contrapartida_colgaap.' - '.$caracter_contrapartida.' - '.$valor_concepto.' - '.$arrayDeduccionesColgaap[$id_empleado][$id_concepto][$cuenta_contrapartida_colgaap]['valor_deducir'].' <br>';

    		}
    	}

    	// SI NO HAY NINGUN CONCEPTO DE NINGUN EMPLEADO EN LA PLANILLA
    	if ($cont_conceptos==0) {
    		echo '<script>
					alert("Aviso!\nNo hay conceptos en la planilla de liquidacion!");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
    	}

    	//ACTUALIZAR EL DOCUMENTO
    	$fecha_generacion=date("Y-m-d");
    	$sql="UPDATE nomina_planillas_liquidacion SET estado=1,fecha_generacion='$fecha_generacion' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>
					alert("Error\nNo se actualizo la planilla, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//CONSULTAR EL CONSECUTIVO DEL DOCUMENTO
		$sql   = "SELECT consecutivo,fecha_documento,fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query = mysql_query($sql,$link);
		$fecha_documento = mysql_result($query,0,'fecha_documento');
		$consecutivo     = mysql_result($query,0,'consecutivo');
		$fecha_inicio    = mysql_result($query,0,'fecha_inicio');
		$fecha_final     = mysql_result($query,0,'fecha_final');
		$id_sucursal     = mysql_result($query,0,'id_sucursal');

		// if (!$queryColgaap) {
		// print_r($arrayAsientosColgaap);
		// echo '<script>
		// 		alert("debuf");
		// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
		// 	</script>';
		// exit;
		// }

    	//RECORRER EL ARRAY PARA CREAR EL INSERT COLGAAP
    	foreach ($arrayAsientosColgaap as $id_tercero => $arrayResul) {

    		foreach ($arrayResul as $cuenta => $arrayResul1) {
    			$id_centro_costos=$arrayResul1['ccos'];
    			$acumDebito+=$arrayResul1['debito'];
    			$acumCredito+=$arrayResul1['credito'];
    			if ($arrayResul1['debito']==0 && $arrayResul1['credito']==0) {
    				continue;
    			}

    			if ($arrayResul1['debito']>0) {
    				$valueInsertAsientos .= "('$id_planilla',
										'$consecutivo',
										'LE',
										'$id_planilla',
										'$consecutivo',
										'LE',
										'Liquidacion Empleado',
										'".$fecha_documento."',
										'".$arrayResul1['debito']."',
										'0',
										'".$cuenta."',
										'".$id_tercero."',
										'$id_centro_costos',
										'$id_sucursal',
										'$id_empresa'),";
    			}
    			if ($arrayResul1['credito']>0) {
    				$valueInsertAsientos .= "('$id_planilla',
										'$consecutivo',
										'LE',
										'$id_planilla',
										'$consecutivo',
										'LE',
										'Liquidacion Empleado',
										'".$fecha_documento."',
										'0',
										'".$arrayResul1['credito']."',
										'".$cuenta."',
										'".$id_tercero."',
										'$id_centro_costos',
										'$id_sucursal',
										'$id_empresa'),";
    			}

    		}
    	}

		$acumDebito  = round($acumDebito,$_SESSION['DECIMALESMONEDA']);
		$acumCredito = round($acumCredito,$_SESSION['DECIMALESMONEDA']);

    	if (($acumDebito-$acumCredito)!=0) {
    		$sql="UPDATE nomina_planillas_liquidacion SET estado=0  WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
    		echo '<script>
    					alert("Los saldos contables tienen una diferencia de '.($acumDebito-$acumCredito).' ");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    					console.log(" '.$acumDebito.' - '.$acumCredito.' = '.($acumDebito-$acumCredito).' ");
    				</script>';
    		exit;
    	}



    	// echo json_encode($arrayNominaContabilizacion);
    	// exit;

    	//RECORRER EL ARRAY PARA CREAR EL INSERT NIIF
    	foreach ($arrayAsientosNiif as $id_empleado => $arrayResul) {
    		foreach ($arrayResul as $cuenta => $arrayResul1) {
    			$id_centro_costos=$arrayResul1['ccos'];

    			if ($arrayResul1['debito']==0 && $arrayResul1['credito']==0) {
    				continue;
    			}

    			if ($arrayResul1['debito']>0) {
    				$valueInsertAsientosNiif .= "('$id_planilla',
													'$consecutivo',
													'LE',
													'$id_planilla',
													'$consecutivo',
													'LE',
													'Liquidacion Empleado',
													'".$fecha_documento."',
													'".$arrayResul1['debito']."',
													'0',
													'".$cuenta."',
													'".$id_empleado."',
													'$id_centro_costos',
													'$id_sucursal',
													'$id_empresa'),";
    			}
    			if ($arrayResul1['credito']>0) {
    				$valueInsertAsientosNiif .= "('$id_planilla',
													'$consecutivo',
													'LE',
													'$id_planilla',
													'$consecutivo',
													'LE',
													'Liquidacion Empleado',
													'".$fecha_documento."',
													'0',
													'".$arrayResul1['credito']."',
													'".$cuenta."',
													'".$id_empleado."',
													'$id_centro_costos',
													'$id_sucursal',
													'$id_empresa'),";
    			}

    		}
    	}


    	// RECORRER ARRAY PARA INSERTAR LA TABLA NOMINA EMPLEDOS CONTABILIZACION
    	$valueInsertConfiguracion='';
    	// PRIMERA CAPA ID DEL TERCERO
    	foreach ($arrayNominaContabilizacion as $id_tercero => $arrayNominaContabilizacionArray) {
			// TERCERA CAPA LA CUENTA CONTABLE
			foreach ($arrayNominaContabilizacionArray as $cuenta => $arrayResul) {
				// echo $cuenta.': '.$arrayResul['debito'].' - '.$arrayResul['credito'].'<br>';
				// $debito  = $arrayResul['debito'];
				$credito = $arrayResul['credito'];


    			if ($debito==0 && $credito==0) {
    				continue;
    			}

    			// RECORRER LA CONFIGURACION DEL COMPROBANTE
				for ($j=1; $j <= $i; $j++) {
					$total_sin_abono= abs($debito-$credito);
					$total_sin_abono_provision=0;
					// $total_sin_abono_provision=($arrayNaturaleza[$id_tercero][$cuenta]=='Provision')? abs($debito-$credito) : 0 ;
    				if (strpos($cuenta, $arrayCuentaComprobante[$j])===0) {
    				    $valueInsertConfiguracion.="('$id_tercero',
    				    								'LE',
    				    								'$id_planilla',
    				    								'$consecutivo',
    				    								'".$arrayDatosInsert[$cuenta]['id_concepto']."',
    				    								'$cuenta',
    				    								".$arrayDatosInsert[$cuenta]['cuenta_niif'].",
    				    								'".$debito."',
														'".$credito."',
														'$total_sin_abono',
														'$total_sin_abono_provision',
														'$fecha_inicio',
														'$fecha_final',
														'$id_sucursal',
														'$id_empresa'),";
    				}

				}
			}

    	}

    	if ($valueInsertConfiguracion!='' ) {
    		// INSERTAR LA CONFIGURACION PARA EL COMPROBANTE
	    	$valueInsertConfiguracion     = substr($valueInsertConfiguracion, 0, -1);
			$sql="INSERT INTO nomina_planillas_empleados_contabilizacion
					(id_tercero,
					tipo_planilla,
					id_planilla,
					consecutivo_planilla,
					id_concepto,
					cuenta_colgaap,
					cuenta_niif,
					debito,
					credito,
					total_sin_abono,
					total_sin_abono_provision,
					fecha_inicio_planilla,
					fecha_final_planilla,
					id_sucursal,id_empresa)
					VALUES $valueInsertConfiguracion ";
			$query=mysql_query($sql,$link);
			if (!$query) {
				$sql="UPDATE nomina_planillas_liquidacion SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
				$query=mysql_query($sql,$link);
				echo '<script>
						alert("Error\nNo se inserto la configuracion contable");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
    	}



		// $tiempo_fin = microtime_float();
		// $tiempo = $tiempo_fin - $tiempo_inicio;
  		//   	echo "<script>console.log('Tiempo empleado: ".($tiempo_fin - $tiempo_inicio)." ');</script>  ";


		$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
		$valueInsertAsientosNiif = substr($valueInsertAsientosNiif, 0, -1);
		// $tiempo_inicio = microtime_float();
		//INSERT COLGAAP
    	$sqlColgaap="INSERT INTO asientos_colgaap(
										id_documento,
										consecutivo_documento,
										tipo_documento,
										id_documento_cruce,
										numero_documento_cruce,
										tipo_documento_cruce,
										tipo_documento_extendido,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_centro_costos,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
		$queryColgaap=mysql_query($sqlColgaap,$link);

		if (!$queryColgaap) {
			moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
			$sql="UPDATE nomina_planillas_liquidacion SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
			echo '<script>
					alert("Error\nNo se insertaron los asientos Colgaap");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
		contabilizacionSimultanea($id_planilla,'LE',$id_sucursal,$id_empresa,$link);

		// $tiempo_fin = microtime_float();
		// $tiempo = $tiempo_fin - $tiempo_inicio;
    	// echo "<script>console.log('Tiempo empleado colgaap: ".($tiempo_fin - $tiempo_inicio)." ');</script>  ";
		// $tiempo_inicio = microtime_float();
		// INSERT NIIF
		$sqlNiif="INSERT INTO asientos_niif(
										id_documento,
										consecutivo_documento,
										tipo_documento,
										id_documento_cruce,
										numero_documento_cruce,
										tipo_documento_cruce,
										tipo_documento_extendido,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_centro_costos,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientosNiif";
		$queryNiif=mysql_query($sqlNiif,$link);

		if (!$queryNiif) {
			$sql="UPDATE nomina_planillas_liquidacion SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
			moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
			echo '<script>
					alert("Error\nNo se insertaron los asientos Niif");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
		// $tiempo_fin = microtime_float();
		// $tiempo = $tiempo_fin - $tiempo_inicio;
  		//   	echo "<script>console.log('Tiempo empleado niif: ".($tiempo_fin - $tiempo_inicio)." ');</script>  ";

  		// MOVER LOS SALDOS DE LAS PLANILLAS
		moverSaldoDiasPlanillas('eliminar',$id_planilla,$id_empresa,$link);
		//FINALIZAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR
		administrarContratos('terminar',$id_planilla,$fecha_final,$id_empresa,$link);
    	// GENERAR EL MOVIMIENTO DE LOS PRESTAMOS
    moverSaldoPrestamos('eliminar',$id_planilla,$id_empresa,$link);

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','LE','Planilla de Liquidacion',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

		planillaNominaProvision('crearPlanilla',$id_planilla,$id_empresa,$link);

		echo '<script>
				Ext.get("contenedor_PlanillaLiquidacion").load({
					url     : "liquidacion/bd/grillaContableBloqueada.php",
					scripts : true,
					nocache : true,
					params  :
					{
						id_planilla       : "'.$id_planilla.'",
						opcGrillaContable : "PlanillaLiquidacion",
					}
				});
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
    }

    //======== FUNCION PARA MOVER LOS SALDOS DE DIAS DE LAS PLANILLAS GENERADAS ============//
    function moverSaldoDiasPlanillas($accion,$id_planilla,$id_empresa,$link){
    	// CONSULTAR LOS EMPLEADOS QUE SE MOVERAN EN LAS PLANILLA
    	$sql="SELECT id_empleado,id_concepto,concepto
    			FROM nomina_planillas_liquidacion_empleados_conceptos
    			WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa AND naturaleza='Provision' GROUP BY id_empleado";
    	$query=mysql_query($sql,$link);
    	$whereIdEmpleados='';
    	while ($row=mysql_fetch_array($query)) {
    		$whereIdEmpleados.=($whereIdEmpleados=='')? ' id_empleado='.$row['id_empleado']  : ' OR id_empleado='.$row['id_empleado'];
    		$arrayIdConceptos[$row['id_empleado']].=($arrayIdConceptos[$row['id_empleado']]=='')? ' id_concepto='.$row['id_concepto'] : ' OR id_concepto='.$row['id_concepto'] ;
    	}

  	  	// CONSULTAR LAS PLANILLAS DE LAS PROVISIONES DE LOS EMPLEADOS
		// $sql="SELECT
		// 			NP.id,
		// 			NPE.dias_laborados,
		// 			NPE.id_empleado
		// 		FROM
		// 			nomina_planillas AS NP,
		// 			nomina_planillas_empleados AS NPE
		// 		WHERE
		// 			NP.activo       = 1
		// 		AND NP.estado       = 1
		// 		AND NP.id_empresa   = $id_empresa
		// 		AND NP.fecha_inicio >= '$fecha_inicio'
		// 		AND NP.fecha_final  <= '$fecha_final'
		// 		AND ($whereIdEmpleados)
		// 		GROUP BY NP.id,NPE.id_empleado";
		// $query=mysql_query($sql,$link);
		// $whereIdPlanillas='';
		// while ($row=mysql_fetch_array($query)) {
		// 	$where.=($where=='')? '(id_planilla='.$row['id'].' AND id_empleado='.$row['id_empleado'].' AND ('.$arrayIdConceptos[$row['id_empleado']].') )'
		// 						: ' OR (id_planilla='.$row['id'].' AND id_empleado='.$row['id_empleado'].' AND ('.$arrayIdConceptos[$row['id_empleado']].') )' ;
		// 	// $whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
		// }



		if ($accion=='agregar'){
			// ACTUALIZAR EL SALDO DE LOS DIAS DE LAS PLANILLAS DE NOMINA
			$sql="UPDATE nomina_planillas_empleados_conceptos SET saldo_dias_laborados = dias_laborados
					WHERE activo = 1
					AND id_empresa=$id_empresa
					AND id_planilla_cruce = $id_planilla
					AND tipo_planilla_cruce = 'LE'
					AND ($whereIdEmpleados)
					AND naturaleza='Provision'";
			$query=mysql_query($sql,$link);

			// CONSULTAR LAS PLANILLAS DE NOMINA RELACIONADAS PARA ACTUALIZAR SU ESTADO A 1 (GENERADA SIN CRUZAR) VALIDANDO QUE NO ESTE CRUZADA EN OTROS DOCUMENTO
			$sql="SELECT id_planilla FROM nomina_planillas_empleados_conceptos
					WHERE activo = 1
					AND id_empresa=$id_empresa
					AND id_planilla_cruce = $id_planilla
					AND tipo_planilla_cruce = 'LE'
					AND ($whereIdEmpleados)
					AND naturaleza='Provision'
					GROUP BY id_planilla";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				// $whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
				// CONSULTAR LOS CONCEPTOS DE ESTA PLANILLA PARA VERIFICAR LOS DOCUMENTOS CRUCE, AGRUPADO POR ID DE CRUCE Y TIPO DOC. CRUCE
				$sql2="SELECT id_planilla_cruce,tipo_planilla_cruce FROM nomina_planillas_empleados_conceptos
						WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$row[id_planilla] GROUP BY id_planilla_cruce,tipo_planilla_cruce ";
				$query2=mysql_query($sql2,$link);
				$contDocs=0;
				while ($row2=mysql_fetch_array($query2)) {
					if ($row2['tipo_planilla_cruce']=='LE') {
						if ($id_planilla==$row2['id_planilla_cruce']) { continue; }
						$sql3="SELECT estado FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$row2[id_planilla_cruce]";
						$query3=mysql_query($sql3,$link);
						$estado_doc = mysql_result($query3,0,'estado');
						if ($estado_doc=='1') { $contDocs++; }
					}
					else if ($row2['tipo_planilla_cruce']=='PCP') {
						$sql3="SELECT estado FROM nomina_consolidacion_provision WHERE activo=1 AND id_empresa=$id_empresa AND id=$row2[id_planilla_cruce]";
						$query3=mysql_query($sql3,$link);
						$estado_doc = mysql_result($query3,0,'estado');
						if ($estado_doc=='1') { $contDocs++; }
					}
				}

				// SI NO TIENE MAS PLANILLAS RELACIONADAS O LAS TIENE PERO ESTAN EDITADAS, ACTUALIZAR SU ESTADO DE 2 A 1
				if ($contDocs==0) {
					$sqlUpdate="UPDATE nomina_planillas SET estado=IF(estado=0,0,IF(estado=3,3,1) ) WHERE activo=1 AND id_empresa=$id_empresa AND id=$row[id_planilla]";
					$queryUpdate=mysql_query($sqlUpdate,$link);
				}
			}


		}
		else if($accion=='eliminar'){

			$sql="UPDATE nomina_planillas_empleados_conceptos SET saldo_dias_laborados = 0
					WHERE activo = 1
					AND id_empresa=$id_empresa
					AND id_planilla_cruce = $id_planilla
					AND tipo_planilla_cruce = 'LE'
					AND ($whereIdEmpleados)
					AND naturaleza='Provision'";
			$query=mysql_query($sql,$link);

			// CONSULTAR LAS PLANILLAS DE NOMINA RELACIONADAS PARA ACTUALIZAR SU ESTADO A 2 (CRUZADA - BLOQUEADA)
			$sql="SELECT id_planilla FROM nomina_planillas_empleados_conceptos
					WHERE activo = 1
					AND id_empresa=$id_empresa
					AND id_planilla_cruce = $id_planilla
					AND tipo_planilla_cruce = 'LE'
					AND ($whereIdEmpleados)
					AND naturaleza='Provision'
					GROUP BY id_planilla";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$whereIdPlanillas.=($whereIdPlanillas=='')? 'id='.$row['id_planilla'] : ' OR id='.$row['id_planilla']  ;
			}

			// ACTUALIZAR LAS PLANILLAS A ESTADO 2 PARA BLOQUEARLAS POR ESTAR CRUZADAS
			$sql="UPDATE nomina_planillas SET estado=2 WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPlanillas)";
			$query=mysql_query($sql,$link);

			// ACTUALIZAR LAS PLANILLAS A ESTADO 2 PARA BLOQUEARLAS POR ESTAR CRUZADAS
			//$sql="UPDATE nomina_planillas SET estado=2 WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPlanillas)";
			//$query=mysql_query($sql,$link);


		}

		echo '<script>
				if(document.getElementById("PanelNomina")){ MyBusquedanomina_planillas(); }
				if(document.getElementById("PanelLiquidacion")){ MyBusquedanomina_planillas_liquidacion(); }
			</script>';
    }

    //==================== FUNCION PAR ADMINISTRAR LOS PRESTAMOS ==========================//
    function moverSaldoPrestamos($accion,$id_planilla,$id_empresa,$link){

    	if ($accion=='eliminar') {
    		$sql="UPDATE nomina_prestamos_empleados,
					(
						SELECT valor_concepto,id_prestamo,id_empleado AS id_empleado_cruce
						FROM nomina_planillas_liquidacion_empleados_conceptos
						WHERE activo = 1
						AND id_empresa = $id_empresa
						AND id_planilla = $id_planilla
						AND id_prestamo > 0
					) AS NPEC
					SET valor_prestamo_restante=IF((valor_prestamo_restante-NPEC.valor_concepto)<=0,0,valor_prestamo_restante-NPEC.valor_concepto),
					cuotas_restantes=IF((cuotas_restantes-1)<0,0,cuotas_restantes-1)
					WHERE activo=1
					AND id_empresa=$id_empresa
					AND id_empleado=NPEC.id_empleado_cruce
					AND id=NPEC.id_prestamo";
				// exit;
    	}
    	else if ($accion=='agregar') {
    		// cuotas_restantes=IF((cuotas_restantes+1)>cuotas,cuotas,cuotas_restantes+1)
    		$sql="UPDATE nomina_prestamos_empleados,
					(
						SELECT valor_concepto,id_prestamo,id_empleado AS id_empleado_cruce
						FROM nomina_planillas_liquidacion_empleados_conceptos
						WHERE activo = 1
						AND id_empresa = $id_empresa
						AND id_planilla = $id_planilla
						AND id_prestamo > 0
					) AS NPEC
					SET valor_prestamo_restante=IF((valor_prestamo_restante+NPEC.valor_concepto)>valor_prestamo,valor_prestamo,valor_prestamo_restante+NPEC.valor_concepto),
					cuotas_restantes=IF((cuotas_restantes+1)>cuotas,cuotas,cuotas_restantes+1)
					WHERE activo=1
					AND id_empresa=$id_empresa
					AND id_empleado=NPEC.id_empleado_cruce
					AND id=NPEC.id_prestamo";
				// exit;
    	}
    	// echo $sql;
    	$query=mysql_query($sql,$link);
    	if (!$query) {
			echo '<script>
					alert("Error\nNo se actualizaron los saldo de los prestamos!\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
    }

    //==================== FUNCION PAR ADMINISTRAR LOS CONTRATOS ==========================//
    function administrarContratos($accion,$id_planilla,$fecha_final,$id_empresa,$link){


		if ($accion=='terminar') {
			// ACTUALIZAR LOS REGISTROS DE LAS VACACIONES DE LA PLANILLA
			$sql="UPDATE nomina_vacaciones_empleados SET estado=1 WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
			$query=mysql_query($sql,$link);

			$sql="UPDATE empleados_contratos AS EC,
					(
					    SELECT
							id_empleado,
							id_contrato,
							terminar_contrato,
							id_motivo_fin_contrato,
							motivo_fin_contrato,
							fecha_fin_contrato,
							IF(terminar_contrato='Si' ,1,0) AS estado_contrato
						FROM
							nomina_planillas_liquidacion_empleados
						WHERE
							activo      = 1 AND
							id_planilla = $id_planilla
							AND id_empresa  = $id_empresa
							AND (terminar_contrato='Si' OR vacaciones='Si')
					) AS NPE
					SET EC.estado=NPE.estado_contrato,EC.fecha_cancelacion=NPE.fecha_fin_contrato,EC.id_motivo_cancelacion=NPE.id_motivo_fin_contrato,EC.motivo_cancelacion=NPE.motivo_fin_contrato
					WHERE EC.activo=1
					AND EC.id_empresa=$id_empresa
					AND EC.id_empleado=NPE.id_empleado
					AND EC.id=NPE.id_contrato";
			$query=mysql_query($sql,$link);
			if (!$query) {
				echo '<script>
						alert("Error!\nNo se actualizaron los Contrato para terminarlos\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");
						// document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			}
		}
		else if ($accion=='renovar') {
			// ACTUALIZAR LOS REGISTROS DE LAS VACACIONES DE LA PLANILLA
			$sql="UPDATE nomina_vacaciones_empleados SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
			$query=mysql_query($sql,$link);

			$sql="UPDATE empleados_contratos AS EC,
					(
					    SELECT
							id_empleado,
							id_contrato,
							terminar_contrato,
							id_motivo_fin_contrato,
							motivo_fin_contrato
						FROM
							nomina_planillas_liquidacion_empleados
						WHERE
							activo      = 1
							AND id_planilla = $id_planilla
							AND id_empresa  = $id_empresa
							AND (terminar_contrato='Si' OR vacaciones='Si')
					) AS NPE
					SET EC.estado=0,EC.fecha_cancelacion='0000-00-00',EC.id_motivo_cancelacion='0',EC.motivo_cancelacion=''
					WHERE EC.activo=1
					AND EC.id_empresa=$id_empresa
					AND EC.id_empleado=NPE.id_empleado
					AND EC.id=NPE.id_contrato";
			$query=mysql_query($sql,$link);
			if (!$query) {
				echo '<script>
						alert("Error!\nNo se actualizaron los Contrato para terminarlos\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");
						// document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
			}

		}
    }

    //==================== MOVER LAS CUENTAS DE LA PLANILLA ==============================//
    function moverCuentasPlanilla($accion,$id_planilla,$id_empresa,$link){
    	if ($accion=='eliminar') {
    		//ASIENTOS COLGAAP
    		$sql="DELETE FROM nomina_planillas_empleados_contabilizacion WHERE id_empresa='$id_empresa' AND id_planilla='$id_planilla' AND tipo_planilla='LE' ";
    		$queryColgaap=mysql_query($sql,$link);
    		//ASIENTOS COLGAAP
    		$sql="DELETE FROM asientos_colgaap WHERE id_empresa='$id_empresa' AND id_documento='$id_planilla' AND tipo_documento='LE'";
    		$queryColgaap=mysql_query($sql,$link);
    		//ASIENTOS NIIF
    		$sql="DELETE FROM asientos_niif WHERE id_empresa='$id_empresa' AND id_documento='$id_planilla' AND tipo_documento='LE'";
    		$queryNiif=mysql_query($sql,$link);

    		if (!$queryColgaap) {
    			echo '<script>
    					alert("Error\nNo se Eliminaron los asientos Colgaap, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
    			exit;
    		}
    		if (!$queryNiif) {
    			echo '<script>
						alert("Error\nNo se Eliminaron los asientos Colgaap, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
    			exit;
    		}

    	}
    }

    //======================= MODIFICAR DOCUMENTO GENERADO ==============================//
    function modificarDocumentoPlanillaNomina($id_planilla,$id_empresa,$link){
    	global $id_sucursal;
    	//VALIDAR QUE NO TENFGA DOCUMENTO CRUCE REALCIONADOS
    	validaDocumentoCruce($id_planilla,$id_empresa,$link);
    	// RETORNAR EL VALOR DE LOS PRESTAMOS
    	moverSaldoPrestamos('agregar',$id_planilla,$id_empresa,$link);
    	//MOVER LAS CUENTAS DEL DOCUMENTO
    	moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
    	//RENOVAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR
		administrarContratos('renovar',$id_planilla,'',$id_empresa,$link);
		// ACTUALIZAR EL SALDO DE LAS PLANILLAS
		moverSaldoDiasPlanillas('agregar',$id_planilla,$id_empresa,$link);
    	$sql="UPDATE nomina_planillas_liquidacion SET estado=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
    	$query=mysql_query($sql,$link);
    	if ($query) {
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

				//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','LE','Planilla de Liquidacion',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);
				planillaNominaProvision('editarPlanilla',$id_planilla,$id_empresa,$link);
    		echo '<script>
					Ext.get("contenedor_PlanillaLiquidacion").load({
						url     : "liquidacion/grillaPlanilla.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_planilla       : "'.$id_planilla.'",
							opcGrillaContable : "PlanillaLiquidacion",
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    	else{
    		echo '<script>
    				alert("Error\nSe eliminaron los asientos pero no se actualizo el documento\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
    				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    }

    //======================= CANCELAR LA PLANILLA GENERADA ===============================//
    function cancelarPlanillaNomina($id_planilla,$id_empresa,$link){
    	global $id_sucursal;
    	//VALIDAR QUE NO TENFGA DOCUMENTO CRUCE REALCIONADOS
    	validaDocumentoCruce($id_planilla,$id_empresa,$link);

    	$sql="SELECT estado,consecutivo FROM nomina_planillas_liquidacion WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa'";
   		$query=mysql_query($sql,$link);
		$estado      = mysql_result($query,0,'estado');
		$consecutivo = mysql_result($query,0,'consecutivo');

    	$sql="UPDATE nomina_planillas_liquidacion SET estado=3 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
    	$opc_funcion = '';

   		if ($estado==3) {
   			echo '<script>
   					alert("La planilla ya esta cancelada");
   					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
   			exit;
   		}
   		else if ($estado==0 && ($consecutivo==0 || $consecutivo=='') ) {
			$sql="UPDATE nomina_planillas_liquidacion SET activo=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa'";
			$opc_funcion = 'delete';
		}
		else if ($estado<>0) {
	   		// RETORNAR EL VALOR DE LOS PRESTAMOS
	    	moverSaldoPrestamos('agregar',$id_planilla,$id_empresa,$link);
	   		//RENOVAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR
			administrarContratos('renovar',$id_planilla,'',$id_empresa,$link);
	    	moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
	    	// ACTUALIZAR EL SALDO DE LAS PLANILLAS
			moverSaldoDiasPlanillas('agregar',$id_planilla,$id_empresa,$link);
		}

    	$query=mysql_query($sql,$link);
    	if ($query) {
    		// ACTUALIZAR LA COLUMNA ID PLANILLA LIQUIDACION DE LAS FILAS DE LOS CONCEPTOS DE LAS PLANILLAS EN ESE PERIODO DE TIEMPO
			$sql="UPDATE nomina_planillas_empleados_conceptos SET id_planilla_cruce=0, tipo_planilla_cruce=''
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla_cruce=$id_planilla ";
			$query=mysql_query($sql,$link);

			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

    	//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','LE','Planilla de Liquidacion',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);
			planillaNominaProvision('cancelarPlanilla',$id_planilla,$id_empresa,$link);
    		echo '<script>
					// Ext.get("contenedor_PlanillaLiquidacion").load({
					// 	url     : "liquidacion/bd/grillaContableBloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_planilla       : "'.$id_planilla.'",
					// 		opcGrillaContable : "PlanillaLiquidacion",
					// 	}
					// });
					cerrarPlanilla("'.$opc_funcion.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    	else{
    		echo '<script>
    				alert("Error\nSe eliminaron los asientos pero no se actualizo el documento\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
    				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    }

   	//======================= RESTAURAR LA PLANILLA ELIMINADA =============================//
   	function restaurarPlanillaNomina($id_planilla,$id_empresa,$link){
   		global $id_sucursal;
   		$sql="UPDATE nomina_planillas_liquidacion SET estado=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
    	$query=mysql_query($sql,$link);
    	if ($query) {
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

    		//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','LE','Planilla de Liquidacion',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);
				planillaNominaProvision('restaurarPlanilla',$id_planilla,$id_empresa,$link);
    		echo '<script>
					Ext.get("contenedor_PlanillaLiquidacion").load({
						url     : "liquidacion/grillaPlanilla.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_planilla       : "'.$id_planilla.'",
							opcGrillaContable : "PlanillaLiquidacion",
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    	else{
    		echo '<script>
					alert("Error\nNo se actualizo el documento, Intentelo de nuevo \nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
   	}

   	//====================== CALCULAR LOS TOTALES DE LA PLANILLA ==========================//
   	function calcularValoresPlanilla($id_planilla,$id_empresa,$link){
   		$sql="SELECT valor_concepto,valor_concepto_ajustado,naturaleza FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
   		$query=mysql_query($sql,$link);
   		$acumDevengo=0;
   		$acumDeduce=0;
   		$acumApropiacion=0;
   		$acumProvision=0;
   		while ($row=mysql_fetch_array($query)) {
   			$valor=($row['valor_concepto_ajustado']>0)? $row['valor_concepto_ajustado'] : $row['valor_concepto'] ;
   			if ($row['naturaleza']=='Devengo' || $row['naturaleza']=='Provision') {
   				$acumDevengo += $valor;
   			}
   			else if ($row['naturaleza']=='Deduccion') {
   				$acumDeduce += $valor;
   			}
   			else if ($row['naturaleza']=='Apropiacion') {
   				$acumApropiacion += $valor;
   			}
   			// else if ($row['naturaleza']=='Provision') {
   			// 	$acumProvision += $row['valor_concepto'];
   			// }
   		}
   		echo '<script>
   				document.getElementById("totalDevengoNomina").innerHTML="'.number_format($acumDevengo,$_SESSION['DECIMALESMONEDA']).'";
   				document.getElementById("totalDeduccionNomina").innerHTML="'.number_format($acumDeduce,$_SESSION['DECIMALESMONEDA']).'";
   				document.getElementById("totalApropiacionNomina").innerHTML="'.number_format($acumApropiacion,$_SESSION['DECIMALESMONEDA']).'";
   				document.getElementById("totalProvisionNomina").innerHTML="'.number_format($acumProvision,$_SESSION['DECIMALESMONEDA']).'";
   			</script>';
   	}

   	//====================== FUNCION PARA GUARDAR LA OBSERVACION ==========================//
	function guardarObservacion($observacion,$id,$id_empresa,$link){

		$observacion=str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sql   = "UPDATE nomina_planillas_liquidacion SET  observacion='$observacion' WHERE id='$id' AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if($query){ echo 'true'; }
		else{ echo'false'; }
	}

	//===== FUNCION PARA ENVIAR LOS VOLANTES DE NOMINA A TODOS LOS EMPLEADOS ===============//
	function enviarPlanillaNomina($id_planilla,$id_empresa,$link){
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		include("../../../../misc/MPDF54/mpdf.php");
		include('enviar_volante_email.php');
		//================== ONFIGURACION ENVIO EMAIL ============================//
		$mail  = new PHPMailer();

		$sqlConexion   = "SELECT * FROM configuracion_global LIMIT 0,1";
		$queryConexion = mysql_query ($sqlConexion,$link);
		if($row_consulta= mysql_fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_SMTP'];
			// $pass          = $row_consulta['password'];
			$pass          = 'issaga$08';
			// $user          = $row_consulta['user_name'];
			$user          = 'soportetecnico@logicalsoft.co';
			$puerto        = $row_consulta['puerto_SMTP'];
			// $servidor      = $row_consulta['servidor_SMTP'];
			$servidor      = 'ssl://smtp.gmail.com';
			// $from          = $row_consulta['from'];
			$from          = 'soportetecnico@logicalsoft.co';
			$autenticacion = $row_consulta['autenticacion_SMTP'];
		}

		$mail->IsSMTP();
		// $mail->SMTPDebug = true;

		$mail->SMTPAuth   = true;                  				// enable SMTP authentication
		$mail->SMTPSecure = $seguridad;                         // sets the prefix to the servier
		$mail->Host       = $servidor;      				    // sets GMAIL as the SMTP server
		$mail->Port       = $puerto;                            // set the SMTP port

		$mail->Username   = $user; // GMAIL username
		$mail->Password   = $pass; // GMAIL password

		$mail->From       = $from;
		$mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
		$mail->Subject    = "Volante de Pago de Nomina";
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap

		//================== CONFIGURACION  DEL PDF ====================================//
		if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
		if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
		if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
		if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
		else{ $MS=($terminar_contrato=='Si')? 60 : 57 ; $MD = 10;$MI = 15;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
		if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}




		// echo "string";
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		//CONSULTAR TODOS LOS EMPLEADOS Y ENVIARLES EL EMAIL
		$sql="SELECT id_empleado,id_contrato,nombre_empleado FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$empleado=$row['nombre_empleado'];
			$id_empleado=$row['id_empleado'];
			$id_contrato=$row['id_contrato'];

			for ($i=0; $i <50 ; $i++) {
				//====================== LLAMAR LA FUNCION PARA EL ENVIO =============================//
				$fun=imprimirEnviaVolante($i,$id_planilla,$id_empleado,$id_contrato,$mail,$mpdf,$link);
			}


		}

		echo '<script>
				document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
			</script>';
	}

	//=========  FUNCION PARA ENVIAR EL VOLANTE A UN SOLO EMPLEADO ========================//
	function enviarVolanteUnicoEmpleado($id_planilla,$id_contrato,$id_empleado,$id_empresa,$link){
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		include("../../../../misc/MPDF54/mpdf.php");
		include('enviar_volante_email.php');

		$mail  = new PHPMailer();

		$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
		$queryConexion = mysql_query ($sqlConexion,$link);
		if($row_consulta= mysql_fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_smtp'];
			// $pass          = $row_consulta['password'];
			$pass          = $row_consulta['password'];
			// $user          = $row_consulta['user_name'];
			$user          = $row_consulta['correo'];
			$puerto        = $row_consulta['puerto'];
			// $servidor      = $row_consulta['servidor_SMTP'];
			$servidor      = $row_consulta['servidor'];
			// $from          = $row_consulta['from'];
			$from          = $row_consulta['correo'];
			$autenticacion = $row_consulta['autenticacion'];
		}

		if ($user=='') {
			echo '<script>
					alert("No exite ninguna configuracion de correo SMTP!\nConfigure el correo desde el panel de control en el boton configuracion SMTP");
					document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
				</script>';
			exit;
		}

		$mail->IsSMTP();
		// $mail->SMTPDebug = true;

		$mail->SMTPAuth   = true;                  				// enable SMTP authentication
		$mail->SMTPSecure = $seguridad;                         // sets the prefix to the servier
		$mail->Host       = $servidor;      				    // sets GMAIL as the SMTP server
		$mail->Port       = $puerto;                            // set the SMTP port

		$mail->Username   = $user; // GMAIL username
		$mail->Password   = $pass; // GMAIL password

		$mail->From       = $from;
		$mail->FromName   = "Sistema de Notificaciones Automaticas LOGICALSOFT ERP";
		$mail->Subject    = "Volante de Pago de Liquidacion";
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap

		if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
		if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
		if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
		if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
		else{ $MS=35 ; $MD = 10;$MI = 10;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
		if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}




		// echo "string";
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		$fun=imprimirEnviaVolante($id_planilla,$id_empleado,$id_contrato,$mail,$mpdf,$link);
		if ($fun['return']=='true') {
			$sql="UPDATE nomina_planillas_liquidacion_empleados SET email_enviado='true' WHERE activo=1 AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND id_empleado=$id_empleado AND id_empresa=$id_empresa";
			$query=mysql_query($sql,$link);
			unlink('volante_liquidacion.pdf');
			echo '<script>
				document.getElementById("imgEmail_'.$id_contrato.'").setAttribute("src","img/enviaremail_true.png");
				document.getElementById("imgEmail_'.$id_contrato.'").setAttribute("title","Reenviar Volante por email");
				document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
			</script>';
		}
		else{
			echo '	$seguridad = "'.$seguridad.'"
					$pass = "'.$pass.'"
					$user = "'.$user.'"
					$puerto = "'.$puerto.'"
					$servidor = "'.$servidor.'"
					$from = "'.$from.'"
					$autenticacion = "'.$autenticacion.'"
					fun = '.$fun.'
				<script>
					alert("Error\n'.$fun['mensaje'].'");
					document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
				</script>';
		}
	}

	//FUNCION PARA VALIDAR QUE NO TENGA UN DOCUMENTO CREUCE RELACIONADO
	function validaDocumentoCruce($idDocumento,$id_empresa,$link){
		$id_sucursal=$_SESSION['SUCURSAL'];
		$sqlNota    = "SELECT consecutivo_documento, tipo_documento FROM asientos_colgaap WHERE id_documento_cruce = '$idDocumento' AND tipo_documento_cruce='LE' AND tipo_documento<>'LE' AND activo=1 AND id_empresa = '$id_empresa' GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';
		while ($row=mysql_fetch_array($queryNota)) {
			$doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento'];
		}
		if ($doc_cruces != '') {
			echo '<script>
					alert("Error!\nEsta Planilla tienen relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nPor favor anule los documentos para poder modificar el documento");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
	}

	//FUNCION PARA CALCULAR LA FORMULA DEL CONCEPTO
	function calcula_formula($equation)
    {
    	if ($equation=='') {
    		return round(0,$_SESSION['DECIMALESMONEDA']);
    	}

        // Remove whitespaces
        $equation = preg_replace('/\s+/', '', $equation);
        // echo "$equation\n=";
        // echo 'alert("'.$equation.'"=)';

        $number = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi|π)'; // What is a number

        $functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg
    |sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
        $operators = '[\/*\^\+-,]'; // Allowed math operators
        $regexp = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

        if (preg_match($regexp, $equation))
        {
            $equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
            // echo "$equation\n";
            eval('$result = '.$equation.';');
        }
        else
        {
            $result = false;
        }
        // round(1.95583, 2)
        // return $result;
        return round($result,$_SESSION['DECIMALESMONEDA']);
    }

    // COMPARAR DOS FECHAS
    function compararFechas($primera, $segunda){
		$valoresPrimera = explode ("-", $primera);
		$valoresSegunda = explode ("-", $segunda);

		$diaPrimera  = $valoresPrimera[2];
		$mesPrimera  = $valoresPrimera[1];
		$anyoPrimera = $valoresPrimera[0];

		$diaSegunda  = $valoresSegunda[2];
		$mesSegunda  = $valoresSegunda[1];
		$anyoSegunda = $valoresSegunda[0];

		$diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
		$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);

		if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
		// "La fecha ".$primera." no es válida";
		return 0;
		}elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
		// "La fecha ".$segunda." no es válida";
		return 0;
		}else{
		return  $diasPrimeraJuliano - $diasSegundaJuliano;
		}
	}


	// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO
	function verificaCierre($id_documento,$tablaPrincipal,$id_empresa,$link){
		// CONSULTAR EL DOCUMENTO
		$sql="SELECT fecha_documento,fecha_inicio,fecha_final FROM $tablaPrincipal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
		$query=mysql_query($sql,$link);

		$fecha_documento = mysql_result($query,0,'fecha_documento');
		$fecha_inicio    = mysql_result($query,0,'fecha_inicio');
		$fecha_final     = mysql_result($query,0,'fecha_final');

		//FECHA A BUSCAR LAS NOTAS GENERADAS PARA VALIDAR
		$fecha_inicio_buscar_1 = date("Y", strtotime($fecha_documento)).'-01-01';
		$fecha_fin_buscar_1    = date("Y", strtotime($fecha_documento)).'-12-31';

		$fecha_inicio_buscar_2 = date("Y", strtotime($fecha_inicio)).'-01-01';
		$fecha_fin_buscar_2    = date("Y", strtotime($fecha_inicio)).'-12-31';

		$fecha_inicio_buscar_3 = date("Y", strtotime($fecha_final)).'-01-01';
		$fecha_fin_buscar_3    = date("Y", strtotime($fecha_final)).'-12-31';

		// VALIDAR QUE NO EXISTAN CIERRES POR PERIODO CREADOS EN ESE LAPSO
		$sql="SELECT COUNT(id) AS cont FROM cierre_por_periodo WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND '$fecha_documento' BETWEEN fecha_inicio AND fecha_final";
		$query=mysql_query($sql,$link);
		$cont1 = mysql_result($query,0,'cont');

		// VALIDAR QUE NO EXISTAN MAS NOTAS DE CIERRE CREADAS PARA ESE PERIODO
		$sql="SELECT COUNT(id) AS cont
				FROM nota_cierre
				WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND
				(
					(fecha_nota>='$fecha_inicio_buscar_1' AND fecha_nota<='$fecha_fin_buscar_1') OR
					(fecha_nota>='$fecha_inicio_buscar_2' AND fecha_nota<='$fecha_fin_buscar_2') OR
					(fecha_nota>='$fecha_inicio_buscar_3' AND fecha_nota<='$fecha_fin_buscar_3') ) ";
		$query=mysql_query($sql,$link);
		$cont2 = mysql_result($query,0,'cont');

		if ($cont>0) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}
	}

	// FUNCION PARA LA PLANILLA DE PROVISION
	function planillaNominaProvision($event,$id_planilla,$id_empresa,$link){
		$sql="SELECT COUNT(id) AS cont FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND provision_vacaciones='true' AND id_empresa=$id_empresa AND id_planilla=$id_planilla ";
		$query=mysql_query($sql,$link);
		$cont=mysql_result($query,0,'cont');
		if ($cont>0) {
			global $mysql;
			$sql="SELECT consecutivo FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=$mysql->query($sql,$mysql->link);
			$consecutivo = $mysql->result($query,0,'consecutivo');

			// CREAR OBJETO DE LA CLASE
			$ObjPlanillaProvision = new planillaProvision($id_planilla,$consecutivo,$id_empresa,$mysql);
			switch ($event) {
				case 'crearPlanilla':
					// CREAR PLANILLA DE NOMINA PARA LA PROVISION
		 			$ObjPlanillaProvision->crearPlanilla();
					break;
				case 'editarPlanilla':
					// CREAR PLANILLA DE NOMINA PARA LA PROVISION
		 			$ObjPlanillaProvision->editarPlanilla();
					break;
				case 'cancelarPlanilla':
					// CREAR PLANILLA DE NOMINA PARA LA PROVISION
		 			$ObjPlanillaProvision->cancelarPlanilla();
					break;
				case 'restaurarPlanilla':
					// CREAR PLANILLA DE NOMINA PARA LA PROVISION
		 			$ObjPlanillaProvision->restaurarPlanilla();
					break;
			}

		}
	}


	///////////////////////////////////////////////////////////////////////////////////////////////////
    /////////                               												 //////////
    /////////     CLASE PARA GENERAR LA PLANILLA DE PROVISION EN PERIODO DE VACACIONES       //////////
    /////////                              													 //////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////

    /**
    * @class planillaProvision
    * @param int id de la nueva planilla de nomina a provisionar
    * @param int id de la planilla de liquidacion
    * @param int consecutivo de la planilla de liquidacion
    * @param int id de la empresa
    * @param obj objeto de conexion a mysql
    * @param arr empleados a realizar la provision
    * @param str where con los id de los empleados a provisionar
    * @param str where con los id de los contratos de los empleados a provisionar
    * @param arr contratos de los empleados a provisionar
    * @param str where con los id de los grupos de trabajo de los empleados a provisionar
    * @param arr cuentas colgaap para los asientos
    * @param arr cuentas niif para los asientos
    * @param arr cuentas para el cruze de la planilla de provision
    * @param arr informacion de los conceptos con relacion a sus cuentas
    * @param arr conceptos que son de tipo ajuste
    * @param arr informacion extra para insertar los asientos
    */
    class planillaProvision
    {
		private $id_planilla                = '';
		private $id_planilla_liquidacion    = '';
		private $consecutivo_liquidacion    = '';
		private $id_empresa                 = '';
		private $mysql                      = '';
		private $arrayEmpleados             = '';
		private $whereIdEmpleados           = '';
		private $whereIdContratos           = '';
		private $arrayContratos             = '';
		private $whereIdGrupoTrabajo        = '';
		private $arrayAsientosColgaap       = '';
		private $arrayAsientosNiif          = '';
		private $arrayNominaContabilizacion = '';
		private $arrayInfoConcepto          = '';
		private $arrayConceptoAjuste        = '';
		private $arrayDatosInsert           = '';

		private $consecutivo                = '';
		private $fecha_documento            = '';
		private $fecha_inicio               = '';
		private $fecha_final                = '';
		private $id_sucursal                = '';

		/**
		* @method construct
		* @param int id de la planilla de liquidacion
		* @param int id de la empresa
		* @param obj objeto de conexion mysql
		*/
    	function __construct($id_planilla_liquidacion,$consecutivo_liquidacion,$id_empresa,$mysql)
    	{
			$this->id_planilla_liquidacion = $id_planilla_liquidacion;
			$this->consecutivo_liquidacion = $consecutivo_liquidacion;
			$this->id_empresa              = $id_empresa;
			$this->mysql                   = $mysql;
    	}

    	/**
		* @method error establecer empleados a cargar en la planilla a provisionar
		* @param str error generado
    	*/
    	private function error($function,$error)
    	{
    		echo '<script>
    				alert("'.$error.'");
    				console.log("Error en la funcion '.$function.' de la clase planillaProvision");
				</script>';
    		// exit;
    	}

    	/**
		* @method setEmpleados establecer empleados a cargar en la planilla a provisionar
    	*/
    	private function setEmpleados()
    	{
    		$sql="SELECT
    				id_empleado,
    				tipo_documento,
    				documento_empleado,
    				nombre_empleado,
    				id_contrato
				FROM nomina_planillas_liquidacion_empleados
				WHERE
					activo=1
				AND provision_vacaciones = 'true'
				AND id_empresa=$this->id_empresa
				AND id_planilla=$this->id_planilla_liquidacion
				";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayReturn[$row['id_empleado']]  = array(
															'tipo_documento'     => $row['tipo_documento'],
															'documento_empleado' => $row['documento_empleado'],
															'nombre_empleado'    => $row['nombre_empleado'],
															'id_contrato'        => $row['id_contrato'],
															);
    			$whereReturn  .= ($whereReturn=='')? 'id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
    			$whereReturn2 .= ($whereReturn2=='')? 'id='.$row['id_contrato'] : ' OR id='.$row['id_contrato'] ;
    		}

			$this->arrayEmpleados   = $arrayReturn;
			$this->whereIdEmpleados = $whereReturn;
			$this->whereIdContratos = $whereReturn2;

			$this->setDiasVacaciones();
    	}

    	/**
		* @method setDiasVacaciones establecer dias de liquidacion de aportes de cada empleados en el arrar de empleados
    	*/
		private function setDiasVacaciones()
    	{
    		$sql="SELECT
    				id_empleado,
    				dias_vacaciones_disfrutadas
				FROM nomina_vacaciones_empleados
				WHERE
					activo=1
				AND id_empresa=$this->id_empresa
				AND id_planilla=$this->id_planilla_liquidacion
				AND ($this->whereIdEmpleados)
				";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$this->arrayEmpleados[$row['id_empleado']]['dias_vacaciones_disfrutadas']=$row['dias_vacaciones_disfrutadas'];
    		}
    	}

    	/**
		* @method setContratos establecer array con el valor base de calculo de los conceptos y id de grupo de trabajo
    	*/
    	private function setContratos()
    	{
    		$sql="SELECT
    				id,
    				id_empleado,
					salario_basico,
					id_grupo_trabajo
				FROM empleados_contratos
				WHERE
					activo = 1
				AND (estado = 0 OR estado=2)
				AND id_empresa = $this->id_empresa
				AND ($this->whereIdEmpleados)
				";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		while ($row=$this->mysql->fetch_array($query)) {
    			$arrayReturn[$row['id_empleado']] = array(
															'id_contrato'      => $row['id'],
															'id_grupo_trabajo' => $row['id_grupo_trabajo'],
															'salario_basico'   => $row['salario_basico'],
														);
				$whereReturn .= ($whereReturn=='')? 'id_grupo_trabajo='.$row['id_grupo_trabajo'] : ' OR id_grupo_trabajo='.$row['id_grupo_trabajo'] ;
    		}

    		$this->arrayContratos = $arrayReturn;
    		$this->whereIdGrupoTrabajo = $whereReturn;
    	}

    	/**
		* @method getEmpleados crear string con los empleados a insertar en la planilla de nomina
		* @return str values de insert de empleados para la planilla de nomina a provisionar
    	*/
    	private function getEmpleados()
    	{
    		foreach ($this->arrayEmpleados as $id_empleado => $arrayResul) {
    			$valueInsertEmpleados.="('$this->id_planilla',
										'$id_empleado',
										'$arrayResul[tipo_documento]',
										'$arrayResul[documento_empleado]',
										'$arrayResul[nombre_empleado]',
										'$arrayResul[id_contrato]',
										'$arrayResul[dias_vacaciones_disfrutadas]',
										'$arrayResul[dias_vacaciones_disfrutadas]',
										'No',
										'',
										'$this->id_empresa'
									),";
    		}
    		$valueInsertEmpleados = substr($valueInsertEmpleados, 0, -1);
    		return $valueInsertEmpleados;
    	}
		/**
		* @method getConceptosProvisiones crear string con los conceptos de los empleados a insertar en la planilla de nomina
		* @return str values de insert de los conceptos de los empleados para la planilla de nomina a provisionar
    	*/
    	private function getConceptosProvisiones()
    	{
    		$sql="SELECT id,
						codigo,
						descripcion,
						formula,
						nivel_formula,
						tipo_concepto,
						id_cuenta_colgaap,
						cuenta_colgaap,
						descripcion_cuenta_colgaap,
						id_cuenta_niif,
						cuenta_niif,
						descripcion_cuenta_niif,
						caracter,
						centro_costos,
						id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap,
						descripcion_cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif,
						descripcion_cuenta_contrapartida_niif,
						caracter_contrapartida,
						centro_costos_contrapartida,
						naturaleza,
						imprimir_volante
					FROM nomina_conceptos
					WHERE activo=1
						AND id_empresa=$this->id_empresa
						AND (
							codigo    = 'EPSE'
							OR codigo = 'PE'
							OR codigo = 'SENA'
							OR codigo = 'ICBF'
							OR codigo = 'CDCF'
							OR codigo = 'CS'
							OR codigo = 'PS'
							OR codigo = 'ISC'
							OR codigo = 'VC'
							)
					ORDER BY nivel_formula ASC";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		while ( $row=$this->mysql->fetch_array($query) ){

				$id            = $row['id'];
				$tipo_concepto = $row['tipo_concepto'];
				$nivel_formula = $row['nivel_formula'];
				$arrayConceptos[$nivel_formula][$id] = array('codigo'           					   => $row['codigo'],
															'concepto'                                 => $row['descripcion'],
															'formula'                                  => $row['formula'],
															'formula_original'                         => $row['formula'],
															'nivel_formula'                            => $row['nivel_formula'],
															'valor_concepto'                           => 0,
															'insert'                                   => 'false',
															'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
															'cuenta_colgaap'                           => $row['cuenta_colgaap'],
															'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
															'id_cuenta_niif'                           => $row['id_cuenta_niif'],
															'cuenta_niif'                              => $row['cuenta_niif'],
															'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
															'caracter'                                 => $row['caracter'],
															'centro_costos'                            => $row['centro_costos'],
															'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
															'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
															'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
															'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
															'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
															'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
															'caracter_contrapartida'                   => $row['caracter_contrapartida'],
															'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
															'naturaleza'                               => $row['naturaleza'],
															'imprimir_volante'                         => $row['imprimir_volante'],
															);

			}

			$sql   = "SELECT id_concepto,
							nivel_formula,
							formula,
							id_cuenta_colgaap,
							cuenta_colgaap,
							descripcion_cuenta_colgaap,
							id_cuenta_niif,
							cuenta_niif,
							descripcion_cuenta_niif,
							caracter,
							centro_costos,
							id_cuenta_contrapartida_colgaap,
							cuenta_contrapartida_colgaap,
							descripcion_cuenta_contrapartida_colgaap,
							id_cuenta_contrapartida_niif,
							cuenta_contrapartida_niif,
							descripcion_cuenta_contrapartida_niif,
							caracter_contrapartida,
							centro_costos_contrapartida,
							id_grupo_trabajo
						FROM nomina_conceptos_grupos_trabajo
						WHERE activo=1
							AND id_empresa=$this->id_empresa
							AND ($this->whereIdGrupoTrabajo)";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id               = $row['id_concepto'];
				$nivel_formula    = $row['nivel_formula'];
				$id_grupo_trabajo = $row['id_grupo_trabajo'];
				$arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id]=array(
																					'formula'                                  => $row['formula'],
																					'id_cuenta_colgaap'                        => $row['id_cuenta_colgaap'],
																					'cuenta_colgaap'                           => $row['cuenta_colgaap'],
																					'descripcion_cuenta_colgaap'               => $row['descripcion_cuenta_colgaap'],
																					'id_cuenta_niif'                           => $row['id_cuenta_niif'],
																					'cuenta_niif'                              => $row['cuenta_niif'],
																					'descripcion_cuenta_niif'                  => $row['descripcion_cuenta_niif'],
																					'caracter'                                 => $row['caracter'],
																					'centro_costos'                            => $row['centro_costos'],
																					'id_cuenta_contrapartida_colgaap'          => $row['id_cuenta_contrapartida_colgaap'],
																					'cuenta_contrapartida_colgaap'             => $row['cuenta_contrapartida_colgaap'],
																					'descripcion_cuenta_contrapartida_colgaap' => $row['descripcion_cuenta_contrapartida_colgaap'],
																					'id_cuenta_contrapartida_niif'             => $row['id_cuenta_contrapartida_niif'],
																					'cuenta_contrapartida_niif'                => $row['cuenta_contrapartida_niif'],
																					'descripcion_cuenta_contrapartida_niif'    => $row['descripcion_cuenta_contrapartida_niif'],
																					'caracter_contrapartida'                   => $row['caracter_contrapartida'],
																					'centro_costos_contrapartida'              => $row['centro_costos_contrapartida'],
																					);
			}

			// print_r($arrayContratos);
			// print_r($this->arrayEmpleados);

			foreach ($this->arrayEmpleados as $id_empleado => $arrayResul) {
				$id_grupo_trabajo = $this->arrayContratos[$id_empleado]['id_grupo_trabajo'];
				// echo '<script>console.log("'.$this->arrayContratos[$id_empleado]['id_grupo_trabajo'].' - ");</script>';
				//CREAR ARRAY TEMPORAL PARA GUARDAR TODOS LOS CONCEPTOS DE CARGA AUTOMATICA
				$arrayTempConceptos=$arrayConceptos;
				// RECORRER EL ARRAY TEMPORAL PARA REEMPLAZAR LOS VALORES DE CUENTAS Y FORMULAS POR EL QUE CORRESPONDE AL GRUPO DE TRABAJO, SI EL GRUPO DE TRABAJO NO TIENE NADA CONFIGURADO, ENTONCES SE PONE LA CONFIGURACION INICIAL DEL CONCEPTO
				foreach ($arrayTempConceptos as $nivel_formula => $arrayTempConceptosArray) {
					foreach ($arrayTempConceptosArray as $id_concepto => $arrayTempConceptosResul) {

						// SI LOS VALORES DEL ARRAY EN LOS INDICES NO EXISTEN, ENTONCES SE HACE CONTINUE PARA QUE NO ELIMINE LOS VALORES ANTERIORES DEL ARRAY
						if (!isset($arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto])) {
							continue;
						}

						$formula=$arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['formula'];

						// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
						$arrayTempConceptos[$nivel_formula][$id_concepto]['formula']                                  = ($formula=='')? $arrayTempConceptos[$nivel_formula][$id_concepto]['formula'] : $formula;
						$arrayTempConceptos[$nivel_formula][$id_concepto]['formula_original']                         = ($formula=='')? $arrayTempConceptos[$nivel_formula][$id_concepto]['formula_original'] : $formula;
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_colgaap']                        = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_colgaap']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap']               = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_niif']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_niif']                              = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_niif']                  = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['caracter']                                 = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['caracter'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['centro_costos']                            = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['centro_costos'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap']          = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap']             = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'] = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif']             = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_niif']                = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['cuenta_contrapartida_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif']    = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['caracter_contrapartida']                   = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['caracter_contrapartida'];
						$arrayTempConceptos[$nivel_formula][$id_concepto]['centro_costos_contrapartida']              = $arrayGruposTrabajo[$id_grupo_trabajo][$nivel_formula][$id_concepto]['centro_costos_contrapartida'];
					}
				}
				// ASIGNAR EL VALOR DEL ARRAY TEMPORAL AL ARRAY FINAL DEL EMPLEADO
				$arrayEmpleadosConceptos[$id_empleado]=$arrayTempConceptos;
			}

			// RECORRER LOS CONCEPTOS E IR CALCULANDO SUS RESPECTIVOS VALORES
			foreach ($arrayEmpleadosConceptos as $id_empleado => $arrayConceptosEmp) {
				foreach ($arrayConceptosEmp as $nivel_formula => $arrayConceptosEmpArray) {
					foreach ($arrayConceptosEmpArray as $id_concepto => $arrayResul) {

						$formula = $arrayResul['formula'];
						$formula = str_replace('{SC}', $this->arrayContratos[$id_empleado]['salario_basico'], $formula);
						$formula = str_replace('{DL}', $this->arrayEmpleados[$id_empleado]['dias_vacaciones_disfrutadas'], $formula);

						foreach ($arrayEmpleadosConceptos[$id_empleado] as $nivel_formula_search => $arrayConceptosEmpArray_search) {
							foreach ($arrayConceptosEmpArray_search as $id_concepto_search => $arrayResul_search) {
								// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
								if($arrayResul_search['valor_concepto']<0 ){ continue; }


								// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR
								if($nivel_formula<$nivel_formula_search){ continue; }
								// if ($arrayResul['codigo']=='ISC') {
								// 	echo '<script>
								// 		console.log(" '.$arrayResul['codigo'].' - '.$nivel_formula.' ==> '.$arrayResul_search['codigo'].' - '.$nivel_formula_search.' = '.$arrayResul_search['valor_concepto'].'");
								// 	</script>';
								// }
								// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
								// $arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]
								$formula = str_replace('['.$arrayResul_search['codigo'].']', $arrayResul_search['valor_concepto'], $formula);

							}
						}

						$formula        = reemplazarValoresFaltantes($formula);
						$valor_concepto = calcula_formula($formula);
						$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['valor_concepto'] = $valor_concepto;
						if ($valor_concepto == 0) { continue;}
						// echo '<script>
						// 		console.log("'.$arrayResul['codigo'].' = '.$valor_concepto.' -- '.$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['codigo'].' = '.$arrayResul['valor_concepto'].' ");
						// 	</script>';

						$valueInsert .= "('$this->id_planilla',
											'$id_empleado',
											'".$this->arrayEmpleados[$id_empleado]['id_contrato']."',
											'$id_concepto',
											'".$arrayResul['codigo']."',
											'".$arrayResul['concepto']."',
											'$valor_concepto',
											'".$formula."',
											'".$arrayResul['formula_original']."',
											'".$nivel_formula."',
											'$this->id_empresa',
											'".$arrayResul['id_cuenta_colgaap']."',
											'".$arrayResul['cuenta_colgaap']."',
											'".$arrayResul['descripcion_cuenta_colgaap']."',
											'".$arrayResul['id_cuenta_niif']."',
											'".$arrayResul['cuenta_niif']."',
											'".$arrayResul['descripcion_cuenta_niif']."',
											'".$arrayResul['caracter']."',
											'".$arrayResul['centro_costos']."',
											'".$arrayResul['id_cuenta_contrapartida_colgaap']."',
											'".$arrayResul['cuenta_contrapartida_colgaap']."',
											'".$arrayResul['descripcion_cuenta_contrapartida_colgaap']."',
											'".$arrayResul['id_cuenta_contrapartida_niif']."',
											'".$arrayResul['cuenta_contrapartida_niif']."',
											'".$arrayResul['descripcion_cuenta_contrapartida_niif']."',
											'".$arrayResul['caracter_contrapartida']."',
											'".$arrayResul['centro_costos_contrapartida']."',
											'".$arrayResul['naturaleza']."',
											'".$arrayResul['imprimir_volante']."'
										),";
					}
				}
			}

			$valueInsert = substr($valueInsert, 0, -1);
			return $valueInsert;
    	}

    	/**
		* @method getPlanillaNomina crear la cabecera de la planilla de nomina a provisionar
    	*/
    	private function getPlanillaNomina()
    	{

			$sql   = "SELECT id FROM nomina_planillas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_planilla_liquidacion=$this->id_planilla_liquidacion";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$id_planilla = $this->mysql->result($query,0,'id');

			if ($id_planilla>0) {
				$this->id_planilla = $id_planilla;
			}
			else{
	    		$sql = "SELECT
	    					fecha_documento,
	    					id_tipo_liquidacion,
	    					tipo_liquidacion,
	    					id_usuario,
	    					usuario,
	    					id_sucursal
						FROM nomina_planillas_liquidacion
						WHERE
							activo = 1
						AND id_empresa = $this->id_empresa
						AND id = $this->id_planilla_liquidacion
	    				";
	    		$query=$this->mysql->query($sql,$this->mysql->link);

				$ramdon              = mktime();
				$fecha_documento     = $this->mysql->result($query,0,'fecha_documento');
				$id_tipo_liquidacion = $this->mysql->result($query,0,'id_tipo_liquidacion');
				$tipo_liquidacion    = $this->mysql->result($query,0,'tipo_liquidacion');
				$id_usuario          = $this->mysql->result($query,0,'id_usuario');
				$usuario             = $this->mysql->result($query,0,'usuario');
				$id_sucursal         = $this->mysql->result($query,0,'id_sucursal');

				$arrayFechas = $this->getRangosFechas();
				$fecha_inicio = $arrayFechas['fecha_inicio'];
				$fecha_final  = $arrayFechas['fecha_final'];

				$sql   ="INSERT INTO
									nomina_planillas (id_empresa,id_sucursal,random,fecha_documento,fecha_inicio,fecha_final,id_tipo_liquidacion,id_planilla_liquidacion,consecutivo_planilla_liquidacion,id_usuario,usuario,observacion)
	                        	VALUES(
	                        		'$this->id_empresa',
	                        		'$id_sucursal',
	                        		'$ramdon',
	                        		'$fecha_documento',
	                        		'$fecha_inicio',
	                        		'$fecha_final',
	                        		'$id_tipo_liquidacion',
	                        		'$this->id_planilla_liquidacion',
	                        		'$this->consecutivo_liquidacion',
	                        		'$id_usuario',
	                        		'$usuario',
	                        		'Planilla de Provision de la planilla de liquidacion N. $this->consecutivo_liquidacion'
	                        		)";
	        	$query=$this->mysql->query($sql,$this->mysql->link);

	    	 	$sql      = "SELECT id FROM nomina_planillas  WHERE random='$ramdon' LIMIT 0,1";
	    	 	$query=$this->mysql->query($sql,$this->mysql->link);
	        	$this->id_planilla = $this->mysql->result($query,0,'id');
	        }
    	}

    	/**
		* @method getRangosFechas retornar el rango de fechas para la planilla
		* @return arr fecha inicial y final de la planilla a provisionar
    	*/
    	private function getRangosFechas()
    	{
			$sql   = "SELECT
							MIN(fecha_inicio_vacaciones_disfrutadas) AS fecha_inicio,
							MAX(fecha_fin_vacaciones_disfrutadas) AS fecha_final
						FROM nomina_vacaciones_empleados
						WHERE
							activo=1
						AND id_empresa=$this->id_empresa
						AND id_planilla = $this->id_planilla_liquidacion
						AND ($this->whereIdEmpleados)";
			$query = $this->mysql->query($sql,$this->mysql->link);

			$arrayReturn['fecha_inicio'] = $this->mysql->result($query,0,'fecha_inicio');
			$arrayReturn['fecha_final']  = $this->mysql->result($query,0,'fecha_final');

			return $arrayReturn;
    	}

    	/**
		* @method setDatosPlanillaNomina insertar los datos de la planilla de nomina aprovisionar
    	*/
    	private function setDatosPlanillaNomina()
    	{
			$valueInsertEmpleados = $this->getEmpleados();
			$sql   = "INSERT INTO nomina_planillas_empleados(id_planilla,
						id_empleado,
						tipo_documento,
						documento_empleado,
						nombre_empleado,
						id_contrato,
						dias_laborados,
						dias_laborados_empleado,
						terminar_contrato,
						id_sucursal,
						id_empresa)
					VALUES $valueInsertEmpleados";
			$query = $this->mysql->query($sql,$this->mysql->link);

			$valueInsertConceptos= $this->getConceptosProvisiones();
			$sql   = "INSERT INTO nomina_planillas_empleados_conceptos(
								id_planilla,
								id_empleado,
								id_contrato,
								id_concepto,
								codigo_concepto,
								concepto,
								valor_concepto,
								formula,
								formula_original,
								nivel_formula,
								id_empresa,
								id_cuenta_colgaap,
								cuenta_colgaap,
								descripcion_cuenta_colgaap,
								id_cuenta_niif,
								cuenta_niif,
								descripcion_cuenta_niif,
								caracter,
								centro_costos,
								id_cuenta_contrapartida_colgaap,
								cuenta_contrapartida_colgaap,
								descripcion_cuenta_contrapartida_colgaap,
								id_cuenta_contrapartida_niif,
								cuenta_contrapartida_niif,
								descripcion_cuenta_contrapartida_niif,
								caracter_contrapartida,
								centro_costos_contrapartida,
								naturaleza,
								imprimir_volante)
						VALUES $valueInsertConceptos";
			$query = $this->mysql->query($sql,$this->mysql->link);
    	}

    	/**
		* @method setArraysContabilidad establecer los arrays necesarios para la contabilidad
    	*/
    	private function setArraysContabilidad()
    	{
    		//ARMAR EL QUERY DEL ASIENTO
	    	$sql="SELECT
					NC.concepto_ajustable,
					NPEC.codigo_concepto,
					NPEC.concepto,
					NPEC.valor_concepto,
					NPEC.id_tercero,
					NPEC.id_empleado_cruce,
					NPEC.id_empleado_cruce_contrapartida,
					NPEC.id_empleado,
					NPEC.id_tercero_contrapartida,
					NPEC.id_concepto,
					NPEC.caracter,
					NPEC.caracter_contrapartida,
					NPEC.cuenta_colgaap,
					NPEC.cuenta_niif,
					NPEC.cuenta_contrapartida_colgaap,
					NPEC.cuenta_contrapartida_niif,
					NPEC.id_centro_costos,
					NPEC.id_centro_costos_contrapartida,
					NPEC.naturaleza,
					NPEC.id_prestamo,
					NC.tercero,
					NC.tercero_cruce,
					NC.centro_costos,
					NC.centro_costos_contrapartida
				FROM
					nomina_planillas_empleados_conceptos AS NPEC,
					nomina_conceptos AS NC
				WHERE
					NPEC.id_planilla = $this->id_planilla
				AND NPEC.id_empresa = $this->id_empresa
				AND NC.id=NPEC.id_concepto
				AND NC.id_empresa=$this->id_empresa";
	    	$query = $this->mysql->query($sql,$this->mysql->link);
	    	$error = '0';
	    	while ($row=$this->mysql->fetch_array($query)){

	    		// VARIABLES CON LOS RESULTADOS DE LA CONSULTA
				$id_empleado                     = $row['id_tercero'];
				$id_empleado_contrapartida       = $row['id_tercero_contrapartida'];
				$id_empleado_cruce               = $row['id_empleado_cruce'];
				$id_empleado_cruce_contrapartida = $row['id_empleado_cruce_contrapartida'];
				$cuenta_colgaap                  = $row['cuenta_colgaap'];
				$cuenta_contrapartida_colgaap    = $row['cuenta_contrapartida_colgaap'];
				$cuenta_niif                     = $row['cuenta_niif'];
				$cuenta_contrapartida_niif       = $row['cuenta_contrapartida_niif'];
				$caracter                        = $row['caracter'];
				$caracter_contrapartida          = $row['caracter_contrapartida'];
				$id_prestamo                     = $row['id_prestamo'];
				$tercero                         = $row['tercero'];
				$tercero_cruce                   = $row['tercero_cruce'];
				$id_empleado_nomina              = $row['id_empleado'];
				$concepto                        = $row['concepto'];
				$centro_costos                   = $row['centro_costos'];
				$centro_costos_contrapartida     = $row['centro_costos_contrapartida'];

				// VALIDAR SI EL CONCEPTO SE CREO CON CRUCE CON UNA ENTIDAD ENTONCES QUE EL ID SEA DIFERENTE AL DEL CLIENTE
				if ($tercero=='Entidad' && $id_empleado_cruce==$id_empleado) {
					continue;
					$str_error_entidad.='\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleados[$id_empleado_nomina]['nombre_empleado'];
					$error++;
				}
				if ($tercero_cruce=='Entidad' && $id_empleado_cruce==$id_empleado_contrapartida) {
					continue;
					$str_error_entidad.= '\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleados[$id_empleado_nomina]['nombre_empleado'];
					$error++;
				}

				// VALIDAR SI EL CONCEPTO NECESITA CENTRO DE COSTOS Y SI NO SE CREO EL CCOS EN EL CONTRATO DEL EMPLEADO
				if ($centro_costos=='true' && $row['id_centro_costos']=='') {
					continue;
					$str_error_ccos.='\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleados[$id_empleado_nomina]['nombre_empleado'];
					$error++;
				}
				if ($centro_costos_contrapartida=='true' && $row['id_centro_costos_contrapartida']=='') {
					continue;
					$str_error_ccos.='\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleados[$id_empleado_nomina]['nombre_empleado'];
					$error++;
				}

				// VALIDAR QUE EXISTA COMO TERCERO EL EMPLEADO
				if ($id_empleado=='' || $id_empleado_contrapartida=='') {
					continue;
					$str_error_ccos.= $arrayEmpleados[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleados[$id_empleado_nomina]['nombre_empleado'].' \n';
					$error++;
				}

				// ARRAY CON LOS VALORES PARA LOS ASIENTOS COLGAAP
				$this->arrayAsientosColgaap[$id_empleado][$cuenta_colgaap]['ccos']                                                    = $row['id_centro_costos'];
				$this->arrayAsientosColgaap[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap]['ccos']                        = $row['id_centro_costos_contrapartida'];
				$this->arrayAsientosColgaap[$id_empleado][$cuenta_colgaap][$caracter]                                                 += $row['valor_concepto'];
				$this->arrayAsientosColgaap[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida]       += $row['valor_concepto'];

				// ARRAY CON LOS VALORES PARA LOS ASIENTOPS NIIF
				$this->arrayAsientosNiif[$id_empleado][$cuenta_niif]['ccos']                                                          = $row['id_centro_costos'];
				$this->arrayAsientosNiif[$id_empleado_contrapartida][$cuenta_contrapartida_niif]['ccos']                              = $row['id_centro_costos_contrapartida'];
				$this->arrayAsientosNiif[$id_empleado][$cuenta_niif][$caracter]                                                       += $row['valor_concepto'];
				$this->arrayAsientosNiif[$id_empleado_contrapartida][$cuenta_contrapartida_niif][$caracter_contrapartida]             += $row['valor_concepto'];

				// ARRAY CON LAS CUENTAS CRUCE
				$this->arrayNominaContabilizacion[$id_empleado][$cuenta_colgaap][$caracter]                                           += $row['valor_concepto'];
				$this->arrayNominaContabilizacion[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += $row['valor_concepto'];

				// ARRAY CON LA INFORMACION DEL CONCEPTO
				$this->arrayInfoConcepto[$id_empleado][$cuenta_colgaap]  = array(
																 					'concepto'           => $row['concepto'],
																 					'naturaleza'         => $row['naturaleza'],
																 					'concepto_ajustable' => $row['concepto_ajustable'],
																 				);

				$this->arrayInfoConcepto[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap] = array(
																												'concepto'           => $row['concepto'],
																												'naturaleza'         => $row['naturaleza'],
																												'concepto_ajustable' => $row['concepto_ajustable'],
																											);

				$this->arrayConceptoAjuste[$id_empleado][$cuenta_colgaap][$caracter]                                           = array('concepto_ajustable' => $row['concepto_ajustable'] );
				$this->arrayConceptoAjuste[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] = array('concepto_ajustable' => $row['concepto_ajustable'] );

				// ARRAY CON LOS DEMAS DATOS DEL INSERT
				$this->arrayDatosInsert[$cuenta_colgaap]               = array('cuenta_niif'=>$cuenta_niif,'id_concepto'=>$row['id_concepto'] );
				$this->arrayDatosInsert[$cuenta_contrapartida_colgaap] = array('cuenta_niif'=> $cuenta_contrapartida_niif,'id_concepto'=>$row['id_concepto'] );
	    	}

	    	if ($error>0) {

				$this->arrayAsientosColgaap       = '';
				$this->arrayAsientosNiif          = '';
				$this->arrayNominaContabilizacion = '';
				$this->arrayInfoConcepto          = '';
				$this->arrayConceptoAjuste        = '';
				$this->arrayDatosInsert           = '';

				$msj_error = 'Se Produjeron los siguientes errores:';
				if($str_error_entidad <> ''){ $msj_error.='\nEmpleados sin entidad relacionada en el contrato:'.$str_error_entidad; }
				if($str_error_ccos    <> ''){ $msj_error.='\nEstos conceptos no tienen centro de costos:'.$str_error_ccos; }
				if($str_error_ccos    <> ''){ $msj_error.='\nEstos empleados no estan configurados como terceros: '.$str_error_ccos; }
				$msj_error .= '\nDebe dirigirse a la planilla de nomina, editarla y generarla';

				$this->error('setArraysContabilidad',$msj_error);

	    	}

    	}

    	/**
		* @method setEstadoPlanillaNomina establecer el estado de una planilla de provision
		* @param int estado a actalizar de la planilla
    	*/
    	private function setEstadoPlanillaNomina($estado)
    	{
			if ($estado==1) {
				$fecha_generacion=date("Y-m-d");
				$camposUpdate = ",fecha_generacion='$fecha_generacion'";
			}

			$sql   = "UPDATE nomina_planillas SET estado=$estado $camposUpdate WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_planilla";
			$query = $this->mysql->query($sql,$this->mysql->link);
    	}

    	/**
		* @method setInfoPlanillaNomina consultar la informacion de la planilla de nomina a provisionar
		* @return arr informacion de la cabecera de la planilla de nomina
    	*/
    	private function setInfoPlanillaNomina()
    	{
			$sql   = "SELECT id,consecutivo,fecha_documento,fecha_inicio,fecha_final,id_sucursal
						FROM nomina_planillas
						WHERE activo=1
						AND id_empresa=$this->id_empresa
						AND id_planilla_liquidacion=$this->id_planilla_liquidacion";
			$query = $this->mysql->query($sql,$this->mysql->link);

			$this->id_planilla     = $this->mysql->result($query,0,'id');
			$this->consecutivo     = $this->mysql->result($query,0,'consecutivo');
			$this->fecha_documento = $this->mysql->result($query,0,'fecha_documento');
			$this->fecha_inicio    = $this->mysql->result($query,0,'fecha_inicio');
			$this->fecha_final     = $this->mysql->result($query,0,'fecha_final');
			$this->id_sucursal     = $this->mysql->result($query,0,'id_sucursal');

			return $arrayReturn;
    	}

    	/**
		* @method setLogPlanillaNomina generar el log de la planilla de nomina a provisionar
		* @param str evento de log del documento
    	*/
    	private function setLogPlanillaNomina($event)
    	{
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

    		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						       VALUES($this->id_planilla,$_SESSION[IDUSUARIO],'$_SESSION[NOMBREUSUARIO]','$event','LN','Planilla de Nomina',$this->id_sucursal,'$this->id_empresa','$_SERVER[REMOTE_ADDR]','$fecha_actual','$hora_actual')";
				$query = $this->mysql->query($sqlLog,$this->mysql->link);
    	}

    	/**
		* @method setContabilidadPlanillaNomina generar la contabilizacion de la planilla de nomina a provisionar
    	*/
    	private function setContabilidadPlanillaNomina()
    	{
    		$this->setArraysContabilidad();
	    	$this->setEstadoPlanillaNomina(1);
	    	$this->setInfoPlanillaNomina();

	    	//RECORRER EL ARRAY PARA CREAR EL INSERT COLGAAP
	    	foreach ($this->arrayAsientosColgaap as $id_tercero => $arrayResul) {

	    		foreach ($arrayResul as $cuenta => $arrayResul1) {
	    			$id_centro_costos=$arrayResul1['ccos'];

	    			if ($arrayResul1['debito']>$arrayResul1['credito']) {
						$debito  = $arrayResul1['debito']-$arrayResul1['credito'];
						$credito = 0;
	    			}
	    			else{
						$debito  = 0;
						$credito = $arrayResul1['credito']-$arrayResul1['debito'];
	    			}

	    			if ($debito==0 && $credito==0) { continue; }

					$id_documento_cruce     = ($arrayResul1['doc_cruce']['id_documento_cruce']<>'')? $arrayResul1['doc_cruce']['id_documento_cruce'] : $id_planilla ;
					$numero_documento_cruce = ($arrayResul1['doc_cruce']['numero_documento_cruce']<>'')? $arrayResul1['doc_cruce']['numero_documento_cruce'] : $this->consecutivo ;
					$tipo_documento_cruce   = ($arrayResul1['doc_cruce']['tipo_documento_cruce']<>'')? $arrayResul1['doc_cruce']['tipo_documento_cruce'] : 'LN' ;

					$valueInsertAsientos .= "('$this->id_planilla',
											'$this->consecutivo',
											'LN',
											'$id_documento_cruce',
											'$numero_documento_cruce',
											'$tipo_documento_cruce',
											'Liquidacion Nomina',
											'".$this->fecha_documento."',
											'".$debito."',
											'".$credito."',
											'".$cuenta."',
											'".$id_tercero."',
											'$id_centro_costos',
											'$this->id_sucursal',
											'$this->id_empresa'),";
	    		}
	    	}

	    	//RECORRER EL ARRAY PARA CREAR EL INSERT NIIF
	    	foreach ($this->arrayAsientosNiif as $id_empleado => $arrayResul) {
	    		foreach ($arrayResul as $cuenta => $arrayResul1) {
	    			$id_centro_costos=$arrayResul1['ccos'];
					if ($arrayResul1['debito']>$arrayResul1['credito']) {
						$debito  = $arrayResul1['debito']-$arrayResul1['credito'];
						$credito = 0;
	    			}
	    			else{
						$debito  = 0;
						$credito = $arrayResul1['credito']-$arrayResul1['debito'];
	    			}

	    			if ($debito==0 && $credito==0) { continue; }

	    			$id_documento_cruce     = ($arrayResul1['doc_cruce']['id_documento_cruce']<>'')? $arrayResul1['doc_cruce']['id_documento_cruce'] : $id_planilla ;
					$numero_documento_cruce = ($arrayResul1['doc_cruce']['numero_documento_cruce']<>'')? $arrayResul1['doc_cruce']['numero_documento_cruce'] : $this->consecutivo ;
					$tipo_documento_cruce   = ($arrayResul1['doc_cruce']['tipo_documento_cruce']<>'')? $arrayResul1['doc_cruce']['tipo_documento_cruce'] : 'LN' ;

					$valueInsertAsientosNiif .= "('$this->id_planilla',
													'$this->consecutivo',
													'LN',
													'$id_documento_cruce',
													'$numero_documento_cruce',
													'$tipo_documento_cruce',
													'Liquidacion Nomina',
													'".$this->fecha_documento."',
													'".$debito."',
													'".$credito."',
													'".$cuenta."',
													'".$id_empleado."',
													'$id_centro_costos',
													'$this->id_sucursal',
													'$this->id_empresa'),";
	    		}
	    	}

	    	// RECORRER ARRAY PARA INSERTAR LA TABLA NOMINA EMPLEDOS CONTABILIZACION
	    	$valueInsertConfiguracion='';
	    	// PRIMERA CAPA ID DEL TERCERO
	    	foreach ($this->arrayNominaContabilizacion as $id_tercero => $arrayNominaContabilizacionArray) {
				// TERCERA CAPA LA CUENTA CONTABLE
				foreach ($arrayNominaContabilizacionArray as $cuenta => $arrayResul) {
					if ($arrayResul['debito']>$arrayResul['credito']) {
						$debito  = $arrayResul['debito']-$arrayResul['credito'];
						$credito = 0;
	    			}
	    			else{
						$debito  = 0;
						$credito = $arrayResul['credito']-$arrayResul['debito'];
	    			}

	    			if ($debito==0 && $credito==0) { continue; }

	    			// RECORRER LA CONFIGURACION DEL COMPROBANTE
					for ($j=1; $j <= $i; $j++) {
						if ($arrayConceptoAjuste[$id_tercero][$cuenta]['credito']['concepto_ajustable']=='true'  ) {
							continue;
						}

						$total_sin_abono = ($arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza']=='Provision')? 0 : abs($debito-$credito);
						$total_sin_abono_provision = ($arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza']=='Provision')? abs($debito-$credito) : 0;

	    				if (strpos($cuenta, '2')===0) {
	    				    $valueInsertConfiguracion .= "('$id_tercero',
	    				    								'$this->id_planilla',
	    				    								'$this->consecutivo',
	    				    								'".$arrayDatosInsert[$cuenta]['id_concepto']."',
	    				    								'$cuenta',
	    				    								".$arrayDatosInsert[$cuenta]['cuenta_niif'].",
	    				    								'".$debito."',
															'".$credito."',
															'$total_sin_abono',
															'$total_sin_abono_provision',
															'$this->fecha_inicio',
															'$this->fecha_final',
															'$this->id_sucursal',
															'$this->id_empresa'),";
	    				}
					}
				}
	    	}

	    	// INSERTAR LA CONFIGURACION PARA EL COMPROBANTE
			$valueInsertConfiguracion = substr($valueInsertConfiguracion, 0, -1);

			$sql   = "INSERT INTO nomina_planillas_empleados_contabilizacion
						(id_tercero,id_planilla,consecutivo_planilla,id_concepto,cuenta_colgaap,cuenta_niif,debito,credito,total_sin_abono,total_sin_abono_provision,fecha_inicio_planilla,fecha_final_planilla,id_sucursal,id_empresa)
						VALUES $valueInsertConfiguracion";
			$query = $this->mysql->query($sql,$this->mysql->link);

			$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
			$valueInsertAsientosNiif = substr($valueInsertAsientosNiif, 0, -1);

			//INSERT COLGAAP
	    	$sql = "INSERT INTO asientos_colgaap(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								id_documento_cruce,
								numero_documento_cruce,
								tipo_documento_cruce,
								tipo_documento_extendido,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa)
							VALUES $valueInsertAsientos";
			$query = $this->mysql->query($sql,$this->mysql->link);

			//INSERT NIIF
			$sql = "INSERT INTO asientos_niif(
							id_documento,
							consecutivo_documento,
							tipo_documento,
							id_documento_cruce,
							numero_documento_cruce,
							tipo_documento_cruce,
							tipo_documento_extendido,
							fecha,
							debe,
							haber,
							codigo_cuenta,
							id_tercero,
							id_centro_costos,
							id_sucursal,
							id_empresa)
						VALUES $valueInsertAsientosNiif";
			$query = $this->mysql->query($sql,$this->mysql->link);

			//INSERTAR EL LOG DE EVENTOS
			$this->setLogPlanillaNomina('Generar',$this->id_sucursal);

    	}

    	/**
		* @method setDeleteDatosPlanillaNomina eliminar los datos generados de la planilla de nomina
    	*/
    	private function setDeleteDatosPlanillaNomina()
    	{
    		$sql = "DELETE FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND id_planilla=$this->id_planilla";
    		$query = $this->mysql->query($sql,$this->mysql->link);
    		$sql = "DELETE FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND id_empresa=$this->id_empresa AND id_planilla=$this->id_planilla";
    		$query = $this->mysql->query($sql,$this->mysql->link);
    		$sql = "DELETE FROM nomina_planillas_empleados_contabilizacion WHERE activo=1 AND id_empresa=$this->id_empresa AND id_planilla=$this->id_planilla";
    		$query = $this->mysql->query($sql,$this->mysql->link);
    		$sql = "DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$this->id_empresa AND id_documento=$this->id_planilla AND tipo_documento='LN' ";
    		$query = $this->mysql->query($sql,$this->mysql->link);
    		$sql = "DELETE FROM asientos_niif WHERE activo=1 AND id_empresa=$this->id_empresa AND id_documento=$this->id_planilla AND tipo_documento='LN' ";
    		$query = $this->mysql->query($sql,$this->mysql->link);
    	}

    	/**
		* @method crearPlanilla Crear la planilla de nomina para provision
    	*/
    	public function crearPlanilla()
    	{

			$this->setEmpleados();
			$this->setContratos();
			$this->getPlanillaNomina();
			$this->setDeleteDatosPlanillaNomina();
			$this->setDatosPlanillaNomina();
			$this->setContabilidadPlanillaNomina();

    	}

		/**
		* @method editarPlanilla editar la planilla de nomina para provision
    	*/
    	public function editarPlanilla()
    	{
    		$this->setInfoPlanillaNomina();
    		$this->setEstadoPlanillaNomina(0);
    		$this->setDeleteDatosPlanillaNomina();
			$this->setLogPlanillaNomina('Editar');
    	}

    	/**
		* @method cancelarPlanilla cancelar la planilla de nomina para provision
    	*/
    	public function cancelarPlanilla()
    	{
    		$this->setInfoPlanillaNomina();
    		$this->setEstadoPlanillaNomina(3);
    		$this->setDeleteDatosPlanillaNomina();
			$this->setLogPlanillaNomina('Cancelar');
    	}

    	/**
		* @method restaurarPlanillaNomina cancelar la planilla de nomina para provision
    	*/
    	public function restaurarPlanilla()
    	{
    		$this->setInfoPlanillaNomina();
    		$this->setEstadoPlanillaNomina(0);
			$this->setLogPlanillaNomina('Restaurar');
    	}
    }

    /**
	 * saveDataNE guardar el registro del detalle del concepto por empleado
	 * @param  [int] $id_planilla   		id de la planilla de nomina
	 * @param  [int] $id_empleado   		id del empleado de la planilla de nomina
	 * @param  [int] $id_concepto   		id del concepto del empleado de la planilla de nomina
	 * @param  [int] $id_estructura 		id de la estructura
	 * @param  [json] $data         		estructura con la informacion y los datos
	 * @param  [resource conection] $link  Recurso de conexion mysql
	 * @return [json]                		Respuesta json del proceso
	 */
	function saveDataNE($id_planilla,$id_empleado,$id_concepto,$id_estructura,$data,$link)
	{
		$sql = "INSERT INTO nomina_planillas_empleados_conceptos_datos_nomina_electronica 
				(tipo_planilla,id_estructura,id_planilla,id_empleado,id_concepto,data,id_empresa) 
				VALUES 
				('LE',$id_estructura,$id_planilla,$id_empleado,$id_concepto,'$data',$_SESSION[EMPRESA])";
		$query = mysql_query($sql,$link);
		if ($query) {
			echo '{"status":"sucess","lastId":"'.mysql_insert_id().'"}';
		}
		else{
			echo '{"status":"failed","error":"'.mysql_error().'"}';
		}
	}

	function deleteDataNE($id,$link)
	{
		$sql = "DELETE FROM nomina_planillas_empleados_conceptos_datos_nomina_electronica WHERE id=$id";
		$query = mysql_query($sql,$link);
		if ($query) {
			echo '{"status":"sucess"}';
		}
		else{
			echo '{"status":"failed","error":"'.mysql_error().'"}';
		}
	}


?>
