<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if (isset($id_planilla)) {
		// FUNCION PARA VERIFICAR SI EXISTE ALGUN CIERRE EN ESE PERIODO ANTES DE PROCESAR EL DOCUMENTO, EXEPTO EN LA FUNCION DE CAMBIAR LA FECHA DE LA NOTA
		if ($opc<>'guardarFechaPlanilla') {
			verificaCierre($id_planilla,'nomina_planillas_ajuste',$id_empresa,$link);
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
			agregarEmpleado($id_planilla,$id_contrato,$cont,$id_empresa,$opc_contrato,$link);
			break;
		case 'eliminarEmpleado':
			eliminarEmpleado($cont,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link);
			break;
		case 'cargarConceptosEmpleado':
			cargarConceptosEmpleado($id_contrato,$id_empleado,$id_planilla,$id_empresa,$link);
			break;
		case 'cargarTodosEmpleados':
			cargarTodosEmpleados($id_planilla,$id_empresa,$sucursal,$opc_contrato,$link);
			break;
		case 'eliminarConcepto':
			eliminarConcepto($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$naturaleza,$valor_concepto,$id_empresa,$link);
			break;
		case 'guardarConcepto':
			guardarConcepto($input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$id_empresa,$link);
			break;
		case 'actualizarconcepto':
			actualizarconcepto($id_insert,$input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$id_empresa,$link);
			break;
		case 'updateFinalizaContrato':
			updateFinalizaContrato($terminar_contrato,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link);
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
			ventanaConfigurarConceptoDeduccion($cont,$id_planilla,$id_concepto,$id_empleado,$id_contrato,$id_empresa,$link);
			break;
		case 'guardarConceptoDeducir':
			guardarConceptoDeducir($id_planilla,$cont_deducir,$cont,$id_empleado,$id_contrato,$id_concepto,$id_concepto_deducir,$valor_deducir,$id_empresa,$link);
			break;
		case 'eliminarConceptoDeducir':
			eliminarConceptoDeducir($cont_deducir,$id_empleado,$id_concepto,$id_concepto_deducir,$id_planilla,$id_contrato,$id_empresa,$link);
			break;
		case 'buscarEmpleadoCargado':
			buscarEmpleadoCargado($id_planilla,$filtro,$estado,$id_empresa,$link);
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

	}

	function guardarFechaPlanilla($id_planilla,$opcGrillaContable,$fecha,$campo,$id_empresa,$link){
		$script='';
		if ($campo!='fecha_documento') {

			//ELIMINAR LOS CONCEPTOS QUE ESTAN EN LA PLANILLA
			$sql="DELETE FROM nomina_planillas_ajuste_empleados_conceptos WHERE id_planilla=$id_planilla";
			$query=mysql_query($sql,$link);

			//ELIMINAR LOS EMPLEADOS QUE ESTAN EN LA PLANILLA
			$sql="DELETE FROM nomina_planillas_ajuste_empleados WHERE id_planilla=$id_planilla";
			$query=mysql_query($sql,$link);

			$script='<script>
						document.getElementById("contenedorPrincipalConceptos").innerHTML = "";
						document.getElementById("contenedorEmpleados").innerHTML          = "";
					</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);;
		}

		// ACTUALIZAR LAS FECHAS DEL DOCUMENTO
		$sql="UPDATE nomina_planillas_ajuste SET $campo='$fecha' WHERE activo=1 AND id=$id_planilla AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se guardo la fecha, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
		else{
			echo $script;
		}
	}

	function guardarTipoLiquidacion($id_planilla,$id_tipo_liquidacion,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_ajuste SET id_tipo_liquidacion='$id_tipo_liquidacion' WHERE activo=1 AND id=$id_planilla AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se guardo la fecha, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
		else{
			echo '<script>
					document.getElementById("contenedorPrincipalConceptos").innerHTML ="";
					document.getElementById("contenedorEmpleados").innerHTML          ="";
				</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);
		}
	}

	function agregarEmpleado($id_planilla,$id_contrato,$cont,$id_empresa,$opc_contrato,$link){
		$fecha=date("Y-m-d");
		$whereEstadoContrato=($opc_contrato=='sin_contrato')? 'estado=1' : 'estado=0' ;

		//CONSULTAR EL RANGO DE FECHA DE LA PLANILLA
		$sql="SELECT fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
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
					id_grupo_trabajo,
					grupo_trabajo,
					valor_nivel_riesgo_laboral
				FROM empleados_contratos WHERE activo=1 AND $whereEstadoContrato AND id_empresa=$id_empresa AND id=$id_contrato";
		$query=mysql_query($sql,$link);

		$id_empleado                = mysql_result($query,0,'id_empleado');
		$tipo_documento_empleado    = mysql_result($query,0,'tipo_documento_empleado');
		$documento_empleado         = mysql_result($query,0,'documento_empleado');
		$nombre_empleado            = mysql_result($query,0,'nombre_empleado');
		$numero_contrato            = mysql_result($query,0,'numero_contrato');
		$salario_basico             = mysql_result($query,0,'salario_basico');
		$fecha_inicio_nomina        = mysql_result($query,0,'fecha_inicio_nomina');
		$id_grupo_trabajo           = mysql_result($query,0,'id_grupo_trabajo');
		$grupo_trabajo              = mysql_result($query,0,'grupo_trabajo');
		$valor_nivel_riesgo_laboral = mysql_result($query,0,'valor_nivel_riesgo_laboral');

		//CONSULTAR LAS PLANILLAS DE NOMINA QUE ESTAN DENTRO DEL RANGO DE FECHAS DE LA LIQUIDACION PARA TRAER TODO LOS PROVISIONADO
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
		$query=mysql_query($sql,$link);
		$whereIdPlanillas='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			$dias_laborados+=$row['dias_laborados'];
		}

		//CONSULTAR LAS PLANILLAS DE LIQUIDACION QUE ESTAN DENTRO DEL RANGO DE FECHAS DE LA LIQUIDACION PARA TRAER TODO LOS PROVISIONADO
		$sql="SELECT
					NP.id,
					NPE.dias_laborados,
					NPE.id_empleado
				FROM
					nomina_planillas_liquidacion AS NP,
					nomina_planillas_liquidacion_empleados AS NPE
				WHERE
					NP.activo 		= 1
				AND (NP.estado=1 OR NP.estado=2)
				AND NP.id_empresa   = $id_empresa
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final  <= '$fecha_final'
				AND NPE.id_planilla = NP.id
				AND NPE.id_empleado = '$id_empleado'
				GROUP BY NP.id,NPE.id_empleado";
		$query=mysql_query($sql,$link);
		$whereIdPlanillasLiquidacion='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdPlanillasLiquidacion.=($whereIdPlanillasLiquidacion=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
			$dias_laborados+=$row['dias_laborados'];
		}

		//CONSULTAR LOS CONCEPTOS ACUMULADOS DE ESE EMPLEADO
		$sql="SELECT
					NPEC.id_concepto,
					NPEC.codigo_concepto,
					NPEC.concepto,
					SUM(NPEC.valor_concepto) AS valor_provisionado,
					NPEC.naturaleza,
					NPEC.id_prestamo,
					NC.concepto_ajustable,
					NC.cuenta_colgaap,
					NC.caracter,
					NC.cuenta_contrapartida_colgaap,
					NC.caracter_contrapartida

				FROM
					nomina_planillas_empleados_conceptos AS NPEC,
					nomina_conceptos AS NC
				WHERE
					NPEC.activo = 1
				AND NPEC.id_empleado = $id_empleado
				AND NPEC.id_empresa = $id_empresa
				AND NC.concepto_ajustable = 'true'
				AND NC.id=NPEC.id_concepto
				AND ($whereIdPlanillas)
				GROUP BY
					NPEC.id_concepto,NPEC.id_prestamo";
		$query=mysql_query($sql,$link);
		$whereIdConceptos='';
		while ($row=mysql_fetch_array($query)) {
			if ($row['naturaleza']!='Provision' && $row['naturaleza']!='Devengo'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_concepto']] = $row['valor_provisionado'];
			}

			if ( empty( $arrayConceptosAcumulados[$row['id_concepto']][$row['id_prestamo']] ) ) {
				$arrayConceptosAcumulados[$row['id_concepto']][$row['id_prestamo']] = array(
																							'codigo'         => $row['codigo_concepto'],
																							'concepto'       => $row['concepto'],
																							'valor_concepto' => $row['valor_provisionado'],
																							'naturaleza'     => $row['naturaleza'],
																							'base'           => 0,
																							);
			}
			else{
				$arrayConceptosAcumulados[$row['id_concepto']][$row['id_prestamo']]['valor_concepto']+=$row['valor_provisionado'];
			}

		}

		// print_r($arrayValores);

		//CONSULTAR LOS CONCEPTOS ACUMULADOS DE ESE EMPLEADO EN LAS PLANILLAS DE LIQUIDACION
		$sql="SELECT
					NPEC.id_concepto,
					NPEC.codigo_concepto,
					NPEC.concepto,
					SUM(NPEC.valor_concepto) AS valor_provisionado,
					NPEC.naturaleza,
					NPEC.id_prestamo,
					NC.concepto_ajustable
				FROM
					nomina_planillas_liquidacion_empleados_conceptos AS NPEC,
					nomina_conceptos AS NC
				WHERE
					NPEC.activo = 1
				AND NPEC.id_empleado = $id_empleado
				AND NPEC.id_empresa = $id_empresa
				AND NC.concepto_ajustable = 'true'
				AND NC.id=NPEC.id_concepto
				AND ($whereIdPlanillasLiquidacion)
				GROUP BY
					NPEC.id_concepto,NPEC.id_prestamo";
		$query=mysql_query($sql,$link);
		// $whereIdConceptos='';
		while ($row=mysql_fetch_array($query)) {
			if ($row['naturaleza']!='Provision' && $row['naturaleza']!='Devengo'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_concepto']] = $row['valor_provisionado'];
			}

			if ( empty( $arrayConceptosAcumulados[$row['id_concepto']][$row['id_prestamo']] ) ) {
				$arrayConceptosAcumulados[$row['id_concepto']][$row['id_prestamo']] = array(
																							'codigo'         => $row['codigo_concepto'],
																							'concepto'       => $row['concepto'],
																							'valor_concepto' => $row['valor_provisionado'],
																							'naturaleza'     => $row['naturaleza'],
																							'base'           => 0,
																							);
			}
			else{
				$arrayConceptosAcumulados[$row['id_concepto']][$row['id_prestamo']]['valor_concepto']+=$row['valor_provisionado'];
			}

		}

		// print_r($arrayConceptosAcumulados);

		// CONSULTAR LA SUCURSAL DEL EMPLEADO
		$sql="SELECT id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		$query=mysql_query($sql,$link);
		$id_sucursal=mysql_result($query,0,'id_sucursal');

		//INSERTAR EL EMPLEADO EN LA PLANILLA
		$sql="INSERT INTO nomina_planillas_ajuste_empleados(id_planilla,id_empleado,tipo_documento,documento_empleado,nombre_empleado,id_contrato,dias_laborados,terminar_contrato,id_sucursal,id_empresa)
				VALUES ('$id_planilla','$id_empleado','$tipo_documento_empleado','$documento_empleado','$nombre_empleado','$id_contrato','$dias_laborados','No','$id_sucursal','$id_empresa') ";
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
						id_cuenta_colgaap_ajuste,
						cuenta_colgaap_ajuste,
						descripcion_cuenta_colgaap_ajuste,
						id_cuenta_niif_ajuste,
						cuenta_niif_ajuste,
						descripcion_cuenta_niif_ajuste,
						centro_costos_ajuste,
						centro_costos_contrapartida,
						naturaleza,
						imprimir_volante
					FROM nomina_conceptos
					WHERE
						activo=1
					AND id_empresa=$id_empresa
					AND ($whereIdConceptos)
					ORDER BY nivel_formula_liquidacion ASC";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {

				$id=$row['id'];
				$tipo_concepto=$row['tipo_concepto'];
				$nivel_formula=$row['nivel_formula_liquidacion'];
				$row['formula'] = str_replace(" ","",$row['formula_liquidacion']);
				// echo "formula:".$row['formula'].'-';

				// RECORRER EL ARRAY DE LOS CONCEPTOS ACUMULADOS
				foreach ($arrayConceptosAcumulados[$id] as $id_prestamo => $arrayConceptosAcumuladosResul) {

					$arrayConceptos[$id][$id_prestamo] = array('codigo'           					   	   => $row['codigo'],
																'concepto'                                 => $row['descripcion'],
																'formula'                                  => $row['formula_liquidacion'],
																'formula_original'                         => $row['formula_liquidacion'],
																'nivel_formula'                            => $row['nivel_formula_liquidacion'],
																'valor_concepto'                           => $arrayConceptosAcumuladosResul['valor_concepto'],
																'valor_provisionado'                       => $arrayConceptosAcumuladosResul['valor_concepto'],
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
																'id_cuenta_colgaap_ajuste'                 => $row['id_cuenta_colgaap_ajuste'],
																'cuenta_colgaap_ajuste'                    => $row['cuenta_colgaap_ajuste'],
																'descripcion_cuenta_colgaap_ajuste'        => $row['descripcion_cuenta_colgaap_ajuste'],
																'id_cuenta_niif_ajuste'                    => $row['id_cuenta_niif_ajuste'],
																'cuenta_niif_ajuste'                       => $row['cuenta_niif_ajuste'],
																'centro_costos_ajuste'                     => $row['centro_costos_ajuste'],
																'descripcion_cuenta_niif_ajuste'           => $row['descripcion_cuenta_niif_ajuste'],
																'saldo_dias_laborados'                     => $arrayConceptosAcumuladosResul['saldo_dias_laborados'],
																);

				}


			}
			// print_r($arrayConceptos);
			// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
			$sql="SELECT id_concepto,
						nivel_formula,
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
						id_cuenta_colgaap_ajuste,
						cuenta_colgaap_ajuste,
						descripcion_cuenta_colgaap_ajuste,
						id_cuenta_niif_ajuste,
						cuenta_niif_ajuste,
						descripcion_cuenta_niif_ajuste,
						centro_costos_ajuste
						FROM nomina_conceptos_grupos_trabajo
						WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id            = $row['id_concepto'];
				$nivel_formula = $row['nivel_formula'];
				// echo $arrayConceptos[$id]['codigo'].':'.$arrayConceptos[$id]['cuenta_colgaap_ajuste'].'-'.$row['cuenta_colgaap_ajuste'].'<br>';
				// VALIDAR QUE EL CONCEPTO EXISTA EN EL ARRAY DE LOS CONCEPTOS


				// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
				foreach ($arrayConceptos[$id] as $id_prestamo => $arrayConceptosResul) {
					if ($arrayConceptos[$id][$id_prestamo]['codigo']=='') {
						continue;
					}
					$arrayConceptos[$id][$id_prestamo]['formula']                                  = ($row['formula_liquidacion']=='')? $arrayConceptos[$id][$id_prestamo]['formula'] : $row['formula_liquidacion'];
					$arrayConceptos[$id][$id_prestamo]['formula_original']                         = ($row['formula_liquidacion']=='')? $arrayConceptos[$id][$id_prestamo]['formula_original'] : $row['formula_liquidacion'];
					$arrayConceptos[$id][$id_prestamo]['id_cuenta_colgaap']                        = $row['id_cuenta_colgaap'];
					$arrayConceptos[$id][$id_prestamo]['cuenta_colgaap']                           = $row['cuenta_colgaap'];
					$arrayConceptos[$id][$id_prestamo]['descripcion_cuenta_colgaap']               = $row['descripcion_cuenta_colgaap'];
					$arrayConceptos[$id][$id_prestamo]['id_cuenta_niif']                           = $row['id_cuenta_niif'];
					$arrayConceptos[$id][$id_prestamo]['cuenta_niif']                              = $row['cuenta_niif'];
					$arrayConceptos[$id][$id_prestamo]['descripcion_cuenta_niif']                  = $row['descripcion_cuenta_niif'];
					$arrayConceptos[$id][$id_prestamo]['caracter']                                 = $row['caracter'];
					$arrayConceptos[$id][$id_prestamo]['centro_costos']                            = $row['centro_costos'];
					$arrayConceptos[$id][$id_prestamo]['id_cuenta_contrapartida_colgaap']          = $row['id_cuenta_contrapartida_colgaap'];
					$arrayConceptos[$id][$id_prestamo]['cuenta_contrapartida_colgaap']             = $row['cuenta_contrapartida_colgaap'];
					$arrayConceptos[$id][$id_prestamo]['descripcion_cuenta_contrapartida_colgaap'] = $row['descripcion_cuenta_contrapartida_colgaap'];
					$arrayConceptos[$id][$id_prestamo]['id_cuenta_contrapartida_niif']             = $row['id_cuenta_contrapartida_niif'];
					$arrayConceptos[$id][$id_prestamo]['cuenta_contrapartida_niif']                = $row['cuenta_contrapartida_niif'];
					$arrayConceptos[$id][$id_prestamo]['descripcion_cuenta_contrapartida_niif']    = $row['descripcion_cuenta_contrapartida_niif'];
					$arrayConceptos[$id][$id_prestamo]['caracter_contrapartida']                   = $row['caracter_contrapartida'];
					$arrayConceptos[$id][$id_prestamo]['centro_costos_contrapartida']              = $row['centro_costos_contrapartida'];
					$arrayConceptos[$id][$id_prestamo]['id_cuenta_colgaap_ajuste']                 = $row['id_cuenta_colgaap_ajuste'];
					$arrayConceptos[$id][$id_prestamo]['cuenta_colgaap_ajuste']                    = $row['cuenta_colgaap_ajuste'];
					$arrayConceptos[$id][$id_prestamo]['descripcion_cuenta_colgaap_ajuste']        = $row['descripcion_cuenta_colgaap_ajuste'];
					$arrayConceptos[$id][$id_prestamo]['id_cuenta_niif_ajuste']                    = $row['id_cuenta_niif_ajuste'];
					$arrayConceptos[$id][$id_prestamo]['cuenta_niif_ajuste']                       = $row['cuenta_niif_ajuste'];
					$arrayConceptos[$id][$id_prestamo]['descripcion_cuenta_niif_ajuste']           = $row['descripcion_cuenta_niif_ajuste'];
					$arrayConceptos[$id][$id_prestamo]['centro_costos_ajuste']                     = $row['centro_costos_ajuste'];
				}

			}

			// --------------------------------------------------------- //
			// RECORRER EL ARRAY PARA CALCULAR EL VALOR CON LAS FORMULAS //
			// --------------------------------------------------------- //
			$valueInsert='';
			$nivel=0;
			// print_r($arrayConceptos);
			// PRIMER CAPA, NIVEL DE LA FORMULA
			// foreach ($arrayConceptos as $nivel_formula => $arrayConceptosArray) {
				// RECORRER LOS CONCEPTOS DE CADA NIVEL
				foreach ($arrayConceptos as $id_concepto => $arrayConceptosArray) {
					foreach ($arrayConceptosArray as $id_prestamo => $arrayConceptosResul) {

						$valueInsert.="('$id_planilla',
										'$id_empleado',
										'$id_contrato',
										'$id_concepto',
										'".$arrayConceptosResul['codigo']."',
										'".$arrayConceptosResul['concepto']."',
										'".$arrayConceptosAcumulados[$id_concepto][$id_prestamo]['valor_concepto']."',
										'".$arrayConceptosAcumulados[$id_concepto][$id_prestamo]['valor_concepto']."',
										'".$arrayConceptosAcumulados[$id_concepto][$id_prestamo]['valor_concepto']."',
										'".$arrayConceptosResul['formula']."',
										'".$arrayConceptosResul['formula_original']."',
										'',
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
										'".$arrayConceptosResul['id_cuenta_colgaap_ajuste']."',
										'".$arrayConceptosResul['cuenta_colgaap_ajuste']."',
										'".$arrayConceptosResul['descripcion_cuenta_colgaap_ajuste']."',
										'".$arrayConceptosResul['id_cuenta_niif_ajuste']."',
										'".$arrayConceptosResul['cuenta_niif_ajuste']."',
										'".$arrayConceptosResul['descripcion_cuenta_niif_ajuste']."',
										'".$arrayConceptosResul['centro_costos_ajuste']."',
										'".$arrayConceptosResul['naturaleza']."',
										'".$arrayConceptosResul['imprimir_volante']."',
										'".$arrayConceptosResul['saldo_dias_laborados']."',
										'$id_prestamo'
										),";

					}// FIN SEGUNDO FOR EACH
				} // FIN PRIMER FOR EACH

			$valueInsert = substr($valueInsert, 0, -1);
			$sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos
					(id_planilla,
					id_empleado,
					id_contrato,
					id_concepto,
					codigo_concepto,
					concepto,
					valor_concepto,
					valor_concepto_ajustado,
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
					id_cuenta_colgaap_ajuste,
					cuenta_colgaap_ajuste,
					descripcion_cuenta_colgaap_ajuste,
					id_cuenta_niif_ajuste,
					cuenta_niif_ajuste,
					descripcion_cuenta_niif_ajuste,
					centro_costos_ajuste,
					naturaleza,
					imprimir_volante,
					dias_laborados,
					id_prestamo)
					VALUES $valueInsert";
			$query=mysql_query($sql,$link);

			// CARGAR PRESTAMOS DEL EMPLEADO
			// cargaPrestamosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_sucursal,$id_empresa,$link);

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
		//ELIMINAR LOS CONCEPTOS DEL EMPLEADO
		$sql="DELETE FROM nomina_planillas_ajuste_empleados_conceptos WHERE
				activo=1 AND
				id_planilla='$id_planilla' AND
				id_empleado='$id_empleado' AND
				id_contrato='$id_contrato' AND
				id_empresa='$id_empresa'";
		$query=mysql_query($sql,$link);
		if ($query) {
			//ELIMINAR EL EMPLEADO DE LA PLANILLA
			$sql="DELETE FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND
					id_planilla = '$id_planilla' AND
					id_empleado = '$id_empleado' AND
					id_contrato = '$id_contrato' AND
					id_empresa = '$id_empresa'";
			$query=mysql_query($sql,$link);
			if ($query) {
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
					alert("Erro\nSe eliminaron los conceptos pero no el empleado, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>'.$cont;
			}
		}
		else{
			echo '<script>
					alert("Erro\nNo se eliminaron los conceptos del empleado, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>'.$cont;
		}
	}

	function cargarTodosEmpleados($id_planilla,$id_empresa,$sucursal,$opc_contrato,$link){
		$fecha = date("Y-m-d");
		$whereEstadoContrato=($opc_contrato=='sin_contrato')? 'estado=1' : 'estado=0' ;

		//CONSULTAR LOS DIAS DE LIQUIDACION DE LA PLANILLA
		$sql="SELECT dias_liquidacion,fecha_inicio,fecha_final,id_sucursal  FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$dias_liquidacion = mysql_result($query,0,'dias_liquidacion');
		$fecha_inicio     = mysql_result($query,0,'fecha_inicio');
		$fecha_final      = mysql_result($query,0,'fecha_final');
		$id_sucursal      = mysql_result($query,0,'id_sucursal');

		$bodyEmpleados = '';
		//SELECCIONAR LOS EMPLEADOS QUE ESTAN EN LA PLANILLA
		$sql="SELECT id_contrato,id_empleado,nombre_empleado,documento_empleado FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query=mysql_query($sql,$link);
		$whereIdEmpleados='';
		$whereIdEmpleadosSucursal='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdEmpleados.=' AND id_empleado<>'.$row['id_empleado'].' ';
			$whereIdEmpleadosSucursal.=' AND id<>'.$row['id_empleado'].' ';

		}

		// CONSULTAR LA SUCURSAL DEL EMPLEADO
		$sqlEmpleadoSucursal="SELECT id,id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa  $whereIdEmpleadosSucursal ";
		$queryEmpleadoSucursal=mysql_query($sqlEmpleadoSucursal,$link);
		while ($row=mysql_fetch_array($queryEmpleadoSucursal)) {
			$arraySucursalEmpleado[$row['id']]=$row['id_sucursal'];
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
					id_sucursal
				FROM empleados_contratos
				WHERE activo=1
				AND $whereEstadoContrato
				AND id_empresa=$id_empresa
				AND id_sucursal=$sucursal
				AND fecha_inicio_nomina<='$fecha_final'
				AND documento_empleado<>''
				$whereIdEmpleados
				";
		$query=mysql_query($sql,$link);

		$valueInsertEmpleados='';
		$whereInsertEmpleados='';
		$whereDeleteEmpleados='';
		$whereId_grupo_trabajo='';

		while ($row=mysql_fetch_array($query)) {
			$whereInsertEmpleados  .= ($whereInsertEmpleados=='')? ' E.id='.$row['id_empleado'] : ' OR E.id='.$row['id_empleado'] ;
			$whereDeleteEmpleados  .= ($whereDeleteEmpleados=='')? ' id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
			$whereId_grupo_trabajo .= ($whereId_grupo_trabajo=='')? ' id_grupo_trabajo='.$row['id_grupo_trabajo'] : ' OR id_grupo_trabajo='.$row['id_grupo_trabajo'] ;
			$whereIdEmpleadosProvision.=($whereIdEmpleadosProvision=='')? ' NPE.id_empleado='.$row['id_empleado']  : ' OR NPE.id_empleado='.$row['id_empleado'];

			$arrayEmpleados[$row['id_empleado']]         = $row['id_grupo_trabajo'];
			$arrayEmpleadosValores[$row['id_empleado']]  = array(	'salario_basico' 			 => $row['salario_basico'],
																	'id_contrato'                => $row['id'],
																	'id_sucursal'                => $row['id_sucursal'],
																	'valor_nivel_riesgo_laboral' => $row['valor_nivel_riesgo_laboral']);

			$valueInsertEmpleados.="('$id_planilla',
									 '$row[id_empleado]',
									 '$row[tipo_documento_empleado]',
									 '$row[documento_empleado]',
									 '$row[nombre_empleado]',
									 '$row[id]',
									 '".$ArrayDiasLaborados[$row['id_empleado']]."',
									 'No',
									 '".$arraySucursalEmpleado[$row['id_empleado']]."',
									 '$id_empresa'
									 ),";
		}

		// CONSULTAR LAS PLANILLAS DE LAS PROVISIONES DE LOS EMPLEADOS
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
				AND ($whereIdEmpleadosProvision)
				GROUP BY NP.id,NPE.id_empleado";
		$query=mysql_query($sql,$link);
		$whereIdPlanillas='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdPlanillas.=($whereIdPlanillas=='')? 'NPEC.id_planilla='.$row['id'] : ' OR NPEC.id_planilla='.$row['id']  ;
			$ArrayDiasLaborados[$row['id_empleado']]+=$row['dias_laborados'];
		}

		// CONSULTAR LAS PLANILLAS DE LAS PROVISIONES DE LOS EMPLEADOS
		$sql="SELECT
					NP.id,
					NPE.dias_laborados,
					NPE.id_empleado
				FROM
					nomina_planillas_liquidacion AS NP,
					nomina_planillas_liquidacion_empleados AS NPE
				WHERE
					NP.activo = 1
				AND (NP.estado=1 OR NP.estado=2)
				AND NP.id_empresa = $id_empresa
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final <= '$fecha_final'
				AND ($whereIdEmpleadosProvision)
				GROUP BY NP.id,NPE.id_empleado";
		$query=mysql_query($sql,$link);
		$whereIdPlanillasLiquidacion='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdPlanillasLiquidacion.=($whereIdPlanillasLiquidacion=='')? 'NPEC.id_planilla='.$row['id'] : ' OR NPEC.id_planilla='.$row['id']  ;
			$ArrayDiasLaborados[$row['id_empleado']]+=$row['dias_laborados'];
		}

		// REEMPLAZAR LA VARIABLE DE LOS ID QUITANDO NPE. POR ''
		$whereIdEmpleadosProvision=str_replace('NPE.', 'NPEC.', $whereIdEmpleadosProvision);


		// SELECT
		// id_concepto,
		// codigo_concepto,
		// concepto,
		// id_empleado,
		// SUM(valor_concepto) AS valor_provisionado,
		// naturaleza
		// FROM
		// 	nomina_planillas_empleados_conceptos
		// WHERE
		// 	activo = 1
		// AND ($whereIdEmpleadosProvision)
		// AND id_empresa = $id_empresa
		// AND ($whereIdPlanillas)
		// AND concepto_ajustable = 'true'
		// GROUP BY
		// id_tercero,
		// id_concepto

		//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE LOS EMPLEADOS
		$sql="SELECT
					NPEC.id_concepto,
					NPEC.codigo_concepto,
					NPEC.concepto,
					NPEC.id_empleado,
					SUM(NPEC.valor_concepto) AS valor_provisionado,
					NPEC.naturaleza,
					NPEC.id_prestamo,
					NC.concepto_ajustable,
					NC.cuenta_colgaap,
					NC.caracter,
					NC.cuenta_contrapartida_colgaap,
					NC.caracter_contrapartida
				FROM
					nomina_planillas_empleados_conceptos AS NPEC,
					nomina_conceptos AS NC
				WHERE
					NPEC.activo = 1
				AND NPEC.id_empresa = $id_empresa
				AND NC.concepto_ajustable = 'true'
				AND NC.id=NPEC.id_concepto
				AND ($whereIdPlanillas)
				AND ($whereIdEmpleadosProvision)
				GROUP BY
					NPEC.id_empleado,
					NPEC.id_concepto,
					NPEC.id_prestamo";
		$query=mysql_query($sql,$link);
		$whereIdConceptos='';
		while ($row=mysql_fetch_array($query)) {

			// if ($row['naturaleza']=='Apropiacion' || $row['naturaleza']=='Deduccion'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_empleado']][$row['id_concepto']] = $row['valor_provisionado'];
				$arrayDiasConceptos[$row['id_empleado']][$row['id_concepto']]    = $row['saldo_dias_laborados'];
			// }

			if ( empty( $arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] ) ) {
				$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] = array(
																							'codigo'         => $row['codigo_concepto'],
																							'concepto'       => $row['concepto'],
																							'valor_concepto' => $row['valor_provisionado'],
																							'naturaleza'     => $row['naturaleza'],
																							'base'           => 0,
																							);
			}
			else{
				$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']]['valor_concepto']+=$row['valor_provisionado'];
			}

			// ARRAY CON LOS VALORES DEL EMPLEADO PARA QUE EL SISTEMA AJUSTE DE FORMA AUTOAMTICA AL 100
			if ($row['caracter']=='credito') {
				if (empty($arrayValores[$row['id_empleado']] ) ){
					$arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]=array( 'valor' => $row['valor_provisionado'], 'cont' =>1 , 'concepto' => $row['concepto']);
				}
				else{
					$arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]['valor']+=$row['valor_provisionado'];
					$arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]['cont']++;
					$arrayValores[$row['id_empleado']][$row['cuenta_colgaap']]['concepto'].=' - '.$row['concepto'];
				}
			}
			else if ($row['caracter_contrapartida']=='credito') {
				if (empty($arrayValores[$row['id_empleado']] ) ){
					$arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]=array( 'valor' => $row['valor_provisionado'], 'cont' =>1, 'concepto' => $row['concepto'] );
				}
				else{
					$arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]['valor']+=$row['valor_provisionado'];
					$arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]['cont']++;
					$arrayValores[$row['id_empleado']][$row['cuenta_contrapartida_colgaap']]['concepto'].=' - '.$row['concepto'];
				}
			}

			//$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] = array(
			//																								'codigo'               => $row['codigo_concepto'],
			//																								'concepto'             => $row['concepto'],
			//																								'valor_concepto'       => $row['valor_provisionado'],
			//																								'saldo_dias_laborados' => $row['saldo_dias_laborados'],
			//																								'naturaleza'           => $row['naturaleza'],
			//																								'base'                 => 0,
			//																								);

		}

		// print_r($arrayValores);

		//CONSULTAR LOS CONCEPTOS PROVISIONADOS DE LOS EMPLEADOS
		$sql="SELECT
					NPEC.id_concepto,
					NPEC.codigo_concepto,
					NPEC.concepto,
					NPEC.id_empleado,
					SUM(NPEC.valor_concepto) AS valor_provisionado,
					NPEC.naturaleza,
					NPEC.id_prestamo
				FROM
					nomina_planillas_liquidacion_empleados_conceptos AS NPEC,
					nomina_conceptos AS NC
				WHERE
					NPEC.activo = 1
				AND NPEC.id_empresa = $id_empresa
				AND NC.concepto_ajustable = 'true'
				AND NC.id=NPEC.id_concepto
				AND ($whereIdPlanillasLiquidacion)
				AND ($whereIdEmpleadosProvision)
				GROUP BY
					NPEC.id_empleado,
					NPEC.id_concepto,
					NPEC.id_prestamo";
		$query=mysql_query($sql,$link);
		// $whereIdConceptos='';
		while ($row=mysql_fetch_array($query)) {

			// if ($row['naturaleza']=='Apropiacion' || $row['naturaleza']=='Deduccion'){
				$whereIdConceptos.=($whereIdConceptos=='')? ' id ='.$row['id_concepto'] : ' OR id='.$row['id_concepto'] ;
				$arrayValoresConceptos[$row['id_empleado']][$row['id_concepto']] = $row['valor_provisionado'];
				$arrayDiasConceptos[$row['id_empleado']][$row['id_concepto']]    = $row['saldo_dias_laborados'];
			// }

			if ( empty( $arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] ) ) {
				$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] = array(
																							'codigo'         => $row['codigo_concepto'],
																							'concepto'       => $row['concepto'],
																							'valor_concepto' => $row['valor_provisionado'],
																							'naturaleza'     => $row['naturaleza'],
																							'base'           => 0,
																							);
			}
			else{
				$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']]['valor_concepto']+=$row['valor_provisionado'];
			}

			//$arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']] = array(
			//																								'codigo'               => $row['codigo_concepto'],
			//																								'concepto'             => $row['concepto'],
			//																								'valor_concepto'       => $row['valor_provisionado'],
			//																								'saldo_dias_laborados' => $row['saldo_dias_laborados'],
			//																								'naturaleza'           => $row['naturaleza'],
			//																								'base'                 => 0,
			//																								);

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
		$sql="INSERT INTO nomina_planillas_ajuste_empleados (id_planilla,id_empleado,tipo_documento,documento_empleado,nombre_empleado,id_contrato,dias_laborados,terminar_contrato,id_sucursal,id_empresa)
				VALUES $valueInsertEmpleados ";
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
						centro_costos_contrapartida,
						naturaleza,
						imprimir_volante,
						id_cuenta_colgaap_ajuste,
						cuenta_colgaap_ajuste,
						descripcion_cuenta_colgaap_ajuste,
						id_cuenta_niif_ajuste,
						cuenta_niif_ajuste,
						centro_costos_ajuste,
						descripcion_cuenta_niif_ajuste
					FROM nomina_conceptos
					WHERE
						activo=1
						AND id_empresa=$id_empresa
						AND ($whereIdConceptos)
						ORDER BY nivel_formula ASC";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id=$row['id'];
				$nivel_formula=$row['nivel_formula_liquidacion'];
				$arrayConceptos[$id] = array(
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
															'id_cuenta_colgaap_ajuste'                 => $row['id_cuenta_colgaap_ajuste'],
															'cuenta_colgaap_ajuste'                    => $row['cuenta_colgaap_ajuste'],
															'descripcion_cuenta_colgaap_ajuste'        => $row['descripcion_cuenta_colgaap_ajuste'],
															'id_cuenta_niif_ajuste'                    => $row['id_cuenta_niif_ajuste'],
															'cuenta_niif_ajuste'                       => $row['cuenta_niif_ajuste'],
															'descripcion_cuenta_niif_ajuste'           => $row['descripcion_cuenta_niif_ajuste'],
															'centro_costos_ajuste'                     => $row['centro_costos_ajuste'],
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
						id_cuenta_colgaap_ajuste,
						cuenta_colgaap_ajuste,
						descripcion_cuenta_colgaap_ajuste,
						id_cuenta_niif_ajuste,
						cuenta_niif_ajuste,
						descripcion_cuenta_niif_ajuste,
						centro_costos_ajuste
						FROM nomina_conceptos_grupos_trabajo
						WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId_grupo_trabajo)";
			$query=mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
				$id               = $row['id_concepto'];
				$id_grupo_trabajo = $row['id_grupo_trabajo'];
				$arrayGruposTrabajo[$id_grupo_trabajo][$id]=array(
																'formula'                                  => $row['formula_liquidacion'],
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
																'id_cuenta_colgaap_ajuste'                 => $row['id_cuenta_colgaap_ajuste'],
																'cuenta_colgaap_ajuste'                    => $row['cuenta_colgaap_ajuste'],
																'descripcion_cuenta_colgaap_ajuste'        => $row['descripcion_cuenta_colgaap_ajuste'],
																'id_cuenta_niif_ajuste'                    => $row['id_cuenta_niif_ajuste'],
																'cuenta_niif_ajuste'                       => $row['cuenta_niif_ajuste'],
																'descripcion_cuenta_niif_ajuste'           => $row['descripcion_cuenta_niif_ajuste'],
																'centro_costos_ajuste'                     => $row['centro_costos_ajuste'],
																);
			}

			// RECORER EL ARRAY PARA ORGANIZAR LOS CONCEPTOS DE CADA EMPLEADO
			foreach ($arrayEmpleados as $id_empleado => $id_grupo_trabajo) {
				//CREAR ARRAY TEMPORAL PARA GUARDAR TODOS LOS CONCEPTOS DE CARGA AUTOMATICA
				$arrayTempConceptos=$arrayConceptos;
				// RECORRER EL ARRAY TEMPORAL PARA REEMPLAZAR LOS VALORES DE CUENTAS Y FORMULAS POR EL QUE CORRESPONDE AL GRUPO DE TRABAJO, SI EL GRUPO DE TRABAJO NO TIENE NADA CONFIGURADO, ENTONCES SE PONE LA CONFIGURACION INICIAL DEL CONCEPTO
				foreach ($arrayTempConceptos as $id_concepto => $arrayTempConceptosResul) {
						// SI LOS VALORES DEL ARRAY EN LOS INDICES NO EXISTEN, ENTONCES SE HACE CONTINUE PARA QUE NO ELIMINE LOS VALORES ANTERIORES DEL ARRAY
						if (!isset($arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto])) {
							continue;
						}

						$formula=$arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['formula'];

						// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
						$arrayTempConceptos[$id_concepto]['formula']                                  = ($formula=='')? $arrayTempConceptos[$id_concepto]['formula'] : $formula;
						$arrayTempConceptos[$id_concepto]['formula_original']                         = ($formula=='')? $arrayTempConceptos[$id_concepto]['formula_original'] : $formula;
						$arrayTempConceptos[$id_concepto]['id_cuenta_colgaap']                        = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_colgaap'];
						$arrayTempConceptos[$id_concepto]['cuenta_colgaap']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_colgaap'];
						$arrayTempConceptos[$id_concepto]['descripcion_cuenta_colgaap']               = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_colgaap'];
						$arrayTempConceptos[$id_concepto]['id_cuenta_niif']                           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_niif'];
						$arrayTempConceptos[$id_concepto]['cuenta_niif']                              = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_niif'];
						$arrayTempConceptos[$id_concepto]['descripcion_cuenta_niif']                  = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_niif'];
						$arrayTempConceptos[$id_concepto]['caracter']                                 = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['caracter'];
						$arrayTempConceptos[$id_concepto]['centro_costos']                            = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos'];
						$arrayTempConceptos[$id_concepto]['id_cuenta_contrapartida_colgaap']          = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$id_concepto]['cuenta_contrapartida_colgaap']             = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$id_concepto]['descripcion_cuenta_contrapartida_colgaap'] = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_contrapartida_colgaap'];
						$arrayTempConceptos[$id_concepto]['id_cuenta_contrapartida_niif']             = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_contrapartida_niif'];
						$arrayTempConceptos[$id_concepto]['cuenta_contrapartida_niif']                = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_contrapartida_niif'];
						$arrayTempConceptos[$id_concepto]['descripcion_cuenta_contrapartida_niif']    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_contrapartida_niif'];
						$arrayTempConceptos[$id_concepto]['caracter_contrapartida']                   = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['caracter_contrapartida'];
						$arrayTempConceptos[$id_concepto]['centro_costos_contrapartida']              = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos_contrapartida'];
						$arrayTempConceptos[$id_concepto]['id_cuenta_colgaap_ajuste']                 = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_colgaap_ajuste'];
						$arrayTempConceptos[$id_concepto]['cuenta_colgaap_ajuste']                    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_colgaap_ajuste'];
						$arrayTempConceptos[$id_concepto]['descripcion_cuenta_colgaap_ajuste']        = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_colgaap_ajuste'];
						$arrayTempConceptos[$id_concepto]['id_cuenta_niif_ajuste']                    = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['id_cuenta_niif_ajuste'];
						$arrayTempConceptos[$id_concepto]['cuenta_niif_ajuste']                       = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['cuenta_niif_ajuste'];
						$arrayTempConceptos[$id_concepto]['descripcion_cuenta_niif_ajuste']           = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['descripcion_cuenta_niif_ajuste'];
						$arrayTempConceptos[$id_concepto]['centro_costos_ajuste']                     = $arrayGruposTrabajo[$id_grupo_trabajo][$id_concepto]['centro_costos_ajuste'];
				}

				// }
				// ASIGNAR EL VALOR DEL ARRAY TEMPORAL AL ARRAY FINAL DEL EMPLEADO
				$arrayEmpleadosConceptos[$id_empleado]=$arrayTempConceptos;
			}


			// --------------------------------------------------------- //
			// RECORRER EL ARRAY PARA CALCULAR EL VALOR CON LAS FORMULAS //
			// --------------------------------------------------------- //
			$valueInsert='';
			$nivel=0;

			// print_r($arrayEmpleadosConceptos);
			// echo "<br>";
			// print_r($arrayConceptosAcumulados);

			// $arrayConceptosAcumulados[$row['id_empleado']][$row['id_concepto']][$row['id_prestamo']]

			// PRIMER CAPA, EMPLEADOS
			foreach ($arrayEmpleadosConceptos as $id_empleado => $arrayEmpleadosConceptosArray) {
				// RECORRER LOS CONCEPTOS
				foreach ($arrayEmpleadosConceptosArray as $id_concepto => $arrayConceptosResul) {
					// RECORRER EL ARRAY CON LOS PRESTAMOS
					foreach ($arrayConceptosAcumulados[$id_empleado][$id_concepto] as $id_prestamo => $arrayConceptosAcumuladosResul) {

						// if ($arrayConceptosResul['caracter']=='credito') {
						// 	$valor_aproximado = aproximar($arrayValores[$id_empleado][$arrayConceptosResul['cuenta_colgaap']]['valor'],3);
						// 	$valor_concepto_ajustado = $valor_aproximado/$arrayValores[$id_empleado][$arrayConceptosResul['cuenta_colgaap']]['cont'];
						// }
						// else if ($arrayConceptosResul['caracter_contrapartida']=='credito') {
						// 	$valor_aproximado = aproximar($arrayValores[$id_empleado][$arrayConceptosResul['cuenta_contrapartida_colgaap']]['valor'],3);
						// 	$valor_concepto_ajustado = $valor_aproximado/$arrayValores[$id_empleado][$arrayConceptosResul['cuenta_contrapartida_colgaap']]['cont'];
						// }

						$valueInsert.="('$id_planilla',
										'$id_empleado',
										'".$arrayEmpleadosValores[$id_empleado]['id_contrato']."',
										'$id_concepto',
										'".$arrayConceptosResul['codigo']."',
										'".$arrayConceptosResul['concepto']."',
										'".$arrayConceptosAcumuladosResul['valor_concepto']."',
										'".$arrayConceptosAcumuladosResul['valor_concepto']."',
										'".$arrayConceptosAcumuladosResul['valor_concepto']."',
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
										'".$arrayConceptosResul['id_cuenta_colgaap_ajuste']."',
										'".$arrayConceptosResul['cuenta_colgaap_ajuste']."',
										'".$arrayConceptosResul['descripcion_cuenta_colgaap_ajuste']."',
										'".$arrayConceptosResul['id_cuenta_niif_ajuste']."',
										'".$arrayConceptosResul['cuenta_niif_ajuste']."',
										'".$arrayConceptosResul['descripcion_cuenta_niif_ajuste']."',
										'".$arrayConceptosResul['centro_costos_ajuste']."',
										'".$arrayConceptosResul['naturaleza']."',
										'".$arrayConceptosResul['imprimir_volante']."',
										'".$arrayConceptosAcumuladosResul['saldo_dias_laborados']."',
										'$id_prestamo'
										),";
					}
				}
			}

			// print_r($arrayEmpleadosConceptos);

			$valueInsert = substr($valueInsert, 0, -1);
			$sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos
					(id_planilla,
					id_empleado,
					id_contrato,
					id_concepto,
					codigo_concepto,
					concepto,
					valor_concepto,
					valor_concepto_ajustado,
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
					id_cuenta_colgaap_ajuste,
					cuenta_colgaap_ajuste,
					descripcion_cuenta_colgaap_ajuste,
					id_cuenta_niif_ajuste,
					cuenta_niif_ajuste,
					descripcion_cuenta_niif_ajuste,
					centro_costos_ajuste,
					naturaleza,
					imprimir_volante,
					dias_laborados,
					id_prestamo)
					VALUES $valueInsert";
			$query=mysql_query($sql,$link);


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

		$sql="SELECT id_contrato,id_empleado,nombre_empleado,documento_empleado,verificado FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
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
		$sql="SELECT id,id_concepto,valor_prestamo_restante,id_centro_costos
				FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND valor_prestamo_restante>0 AND fecha_inicio_pago<='$fecha' ";
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
				$sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos
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
		$sql="SELECT estado FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$estado=mysql_result($query,0,'estado');

		$readonly=($estado==1 || $estado==3 || user_permisos(169)=='false')? 'readonly' : '' ;

		//CONSULTAR LOS DIAS LABORADOS Y SI ES FINALIZACION DEL CONTRATO DEL EMPLEADO
		$sql="SELECT dias_laborados, terminar_contrato, nombre_empleado FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado='$id_empleado' AND id_contrato='$id_contrato'";
		$query=mysql_query($sql,$link);
		$dias_laborados    = mysql_result($query,0,'dias_laborados');
		$terminar_contrato = mysql_result($query,0,'terminar_contrato');
		$label_empleado    = mysql_result($query,0,'nombre_empleado');

		// CONSULTAR EL CONTRATO DEL EMPLEADO
		$sql="SELECT estado FROM empleados_contratos WHERE activo=1 ANd id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato ";
		$query=mysql_query($sql,$link);
		$estado_contrato = mysql_result($query,0,'estado');
		if ($estado_contrato>0) {
			$label_empleado.='<br><span style="font-size:13px;">&nbsp;&nbsp;( Contratro Finalizado )</span>';
		}

		//CONSULTAR LOS CONCEPTOS DEL EMPLEADO DE LA PLANILLA
		$sql="SELECT * FROM nomina_planillas_ajuste_empleados_conceptos WHERE
				activo=1 AND
				id_contrato='$id_contrato' AND
				id_empleado='$id_empleado' AND
				id_planilla='$id_planilla' AND
				id_empresa ='$id_empresa' ORDER BY naturaleza ASC";
		$query=mysql_query($sql,$link);
		$select_contrato=($estado==1 || $estado==3)? '<input type="text" readonly value="'.$terminar_contrato.'">' : '<select id="terminar_contrato" onchange="updateFinalizaContrato(this.value,\''.$id_empleado.'\',\''.$id_contrato.'\')">
                	        																							    <option value="No">No</option>
                	        																							    <option value="Si">Si</option>
                	        																							</select>' ;
		$options_dias_laborados = ($estado==1 || $estado==3)? 'readonly' : 'onkeyup="updateDiasLaborados(event,this,\''.$id_empleado.'\',\''.$id_contrato.'\')"' ;
		// $script=($estado==1 || $estado==3)? '' : 'document.getElementById("terminar_contrato").value="'.$terminar_contrato.'";' ;
		$bodyConceptos='<script>
							//LIMPIAR VARIABLES DE CALCULOS
							totalDevengoEmpleado     = 0
							totalDeduccionEmpleado   = 0
							totalApropiacionEmpleado = 0
							totalProvisionEmpleado = 0
							totalNetoPagarEmpleado   = 0
						</script>

						<div style="width:100%; height:45px;text-transform: uppercase;font-weight:bold;font-size:18px;color:#999;text-indent: 10px;line-height:1.5;">
							'.$label_empleado .'
						</div>

                    	<div class="headConceptos" >
	                    	<div class="bodyDivNominaPlanilla" style="border-bottom:none;" id="headConceptos">
	                            <div class="campo" style=""></div>
	                            <div class="campoHeadConceptos" style="width:calc(100% - 50% - 70px - 18px );">Concepto</div>
	                            <div class="campoHeadConceptos" style="width:100px;padding-right:5px;">Valor Acumulado </div>
	                            <div class="campoHeadConceptos" style="width:calc(100% - 50% - 10px - 18px - 51px - 55px);" title="Valor Ajustado">Valor Ajustado</div>
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
			$botones=($estado==1 || $estado==3 )? '' : '<div style="float:left;margin-left:10px; min-width:40px;">
                        	     						    <div onclick="guardarConcepto('.$cont.',\'actualizarconcepto\')" id="divImageSaveConcepto_'.$cont.'" title="Actualizar Concepto" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/reload.png" id="ImageSaveConcepto_'.$cont.'"></div>
                        	     						    <div onclick="eliminarConcepto('.$cont.')" id="deleteConcepto_'.$cont.'" title="Eliminar Concepto" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
                        	     						</div>' ;

			$evento_input=($estado==1 || $estado==3  || user_permisos(169)=='false')? '' : 'onkeyup="validaNumero(event,this)"' ;
			// $script=($row['naturaleza']=='Deduccion' && $estado==0)?
				// 'document.getElementById("divImageConfiConcepto_'.$cont.'").setAttribute("onclick","ventanaConfigurarConceptoDeduccion('.$cont.')");
				// document.getElementById("divImageConfiConcepto_'.$cont.'").setAttribute("title","Configuracion del concepto");' : '' ;
            // VERIFICAR SI TIENE CONCEPTOS CON FORMULA
            $campoValorConcepto='readonly';
 			if ($row['id_prestamo']>0) {
				$divStyleFunction = '';
				$divImgFunction   = 'ventas16';
				$divTitleFunction = 'Prestamo del empleado';
 			}

			$bodyConceptos.='<div class="bodyDivNominaPlanilla">
	                    	     <div class="campo" id="divLoadConcepto_'.$cont.'">'.$cont.'</div>

	                    	     <div class="campo1" id="concepto_'.$cont.'" style="width:calc(100% - 50% - 70px - 18px );text-indent:5px;">'.$row['concepto'].'</div>
	                    	     <div class="campo" style="'.$divStyleFunction.'border: none;height:21px;width: 13px;margin-top: 1px;margin-left: -15px;background-image:url(img/'.$divImgFunction.'.png);background-repeat:no-repeat;background-color:#FFF;" title="'.$divTitleFunction.'"></div>
	                    	     <div class="campo1" style="width:calc(100% - 50% - 10px - 18px );text-indent:0;">

                    	     		<div style="float:left;width:100px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px">
										'.$row['valor_concepto'].'
									</div>
									<input type="text" style="width:calc(100% - 16px - 90px);padding-left: 0px;/*margin-left: -3px;*/"  id="valor_concepto_'.$cont.'" value="'.$row['valor_concepto_ajustado'].'" '.$evento_input.' '.$readonly.'>

                        	     </div>

                        	     <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;" title="'.$row['naturaleza'].'"><img src="img/'.$row['naturaleza'].'.png" ></div>
                        	     '.$botones.'
                        	     <input type="hidden" id="id_insert_concepto_'.$cont.'" value="'.$row['id'].'">
                        	     <input type="hidden" id="id_concepto_'.$cont.'" value="'.$row['id_concepto'].'">
                        	     <input type="hidden" id="id_contrato_concepto_'.$cont.'" value="'.$id_contrato.'">
                        	     <input type="hidden" id="id_empleado_concepto_'.$cont.'" value="'.$id_empleado.'">
                        	     <input type="hidden" id="formula_concepto_'.$cont.'" value="'.$row['formula_original'].'">
                        	     <input type="hidden" id="nivel_formula_concepto_'.$cont.'" value="'.$row['nivel_formula'].'">
                        	 </div>

                        	 <script>
                        	 	// calculaValoresEmpleado('.$valor_concepto.',"agregar","'.$row['naturaleza'].'");
                        	 	calculaValoresEmpleado('.$row['valor_concepto_ajustado'].',"agregar","'.$row['naturaleza'].'");
                        	 	'.$script.'
                        	 </script>
                        	 ';
             $cont++;
		}

		if ($estado==0) {
			$bodyConceptos .= '<div class="bodyDivNominaPlanilla">
	                        	    <div class="campo" id="divLoadConcepto_'.$cont.'">'.$cont.'</div>
	                        	    <div class="campo1" id="concepto_'.$cont.'" style="width:calc(100% - 50% - 70px - 18px );"></div>
	                        	    <div class="campo" id="divImgFunction_'.$cont.'" style="display:none;border: none;height:21px;width: 13px;margin-top: 1px;margin-left: -15px;background-image:url(img/funcion.png);background-repeat:no-repeat;background-color:#FFF;" title="Concepto calculado con formula"></div>
	                        	    <div id="btnBuscarConcepto_'.$cont.'" onclick="ventanaBuscarConceptos('.$cont.')" title="Buscar Concepto" class="iconBuscar">
	            					     <img src="img/buscar20.png">
	            					</div>

                        	    	<div class="campo1" style="width:calc(100% - 50% - 10px - 18px );text-indent:0;">

	                    	     		<div style="float:left;width:100px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px">
											0
										</div>
										<input type="text" style="width:calc(100% - 16px - 90px);padding-left: 0px;/*margin-left: -3px;*/"  id="valor_concepto_'.$cont.'" value="'.$row['valor_concepto_ajustado'].'" '.$evento_input.' '.$readonly.'>

                        	     	</div>

	                        	    <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;"></div>
	                        	    <div style="float:left; min-width:40px;margin-left:5px;">
	                	     		  	<div onclick=" guardarConcepto('.$cont.',\'\')" id="divImageSaveConcepto_'.$cont.'" title="Guardar Concepto" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" id="ImageSaveConcepto_'.$cont.'"></div>
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
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:100%;">
									<div class="campoHeadConceptos" style="background-color : #F3F3F3;border:none;width:calc(100% - 20px);">TOTALES EMPLEADO</div>
									<div class="button_options_nomina" title="Ver en detalle" onclick="ventana_ver_detalle('.$id_planilla.','.$id_empleado.','.$id_contrato.',\'personal\');"></div>
								</div>

								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:50%;display: none;">
									<div class="campoHeadConceptos" style="width:22px;border-right:none;"><p style="float:left;" title="Devengo"><img src="img/Devengo.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDevengo"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:50%;border-bottom:none;" >
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;border-left:none;"><p style="float:left;" title="Deduccion"><img src="img/Deduccion.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDeduccion"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:50%;border-bottom:none;">
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Apropiacion"><img src="img/Apropiacion.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalApropiacion"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);display: none;">
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Provision"><img src="img/Provision.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalProvision"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;width:100%;float: left;display: none;">
									<div class="campoHeadConceptos" style="padding-right:5px;padding-left:5px;text-align: center;background-color : #F3F3F3;">Neto  Empleado</div>
									<div class="campo1" style="width:18px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
									<div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalNetoPagar"></div>
								</div>
							</div>

							<!-- LA FUNCION DE ESTE BOTON SE ENCUENTRA EN EL ARCHIVO planilla.php YA QUE SE USA EN LA GRILLA NORMAL Y BLOQUEADA ASI QUE PARA NO DECLARARLA EN AMBOS ARCHIVOS SE DECLARO EN EL MENCIONADO
							<div class="button-options" title="Ver en detalle" onclick="ventana_ver_detalle('.$id_planilla.','.$id_empleado.','.$id_contrato.',\'personal\');">
								Valores detallados
							</div>-->

							<script>
									contConceptos='.$cont.';
									'.$script.'
									//RESIZE HEAD
									resizeHeadMyGrilla(document.getElementById("contenedorConceptos"), "headConceptos");
								</script>';

		echo $bodyConceptos;
	}

	function eliminarConcepto($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$naturaleza,$valor_concepto,$id_empresa,$link){
		// VALIDAR QUE OTROS CONCEPTOS NO DEPENDAN DEL CONCEPTO A ELIMINAR
		// $sql="SELECT codigo_concepto FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo= 1 AND
		// 																			id_concepto = '$id_concepto' AND
		// 																			id_contrato = '$id_contrato' AND
		// 																			id_empleado = '$id_empleado' AND
		// 																			id_planilla = '$id_planilla' AND
		// 																			id_empresa  = '$id_empresa'";
		// $query=mysql_query($sql,$link);
		// $codigo_concepto=mysql_result($query,0,'codigo_concepto');

		// $sql="SELECT concepto FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo= 1 AND
		// 																			formula_original LIKE '%$codigo_concepto%' AND
		// 																			id_contrato = '$id_contrato' AND
		// 																			id_empleado = '$id_empleado' AND
		// 																			id_planilla = '$id_planilla' AND
		// 																			id_empresa  = '$id_empresa'";
		// $query=mysql_query($sql,$link);
		// $conceptos_resul='';
		// while ($row=mysql_fetch_array($query)) {
		// 	$conceptos_resul.='\n -> '.$row['concepto'];
		// }
		// // exit;
		// if ($conceptos_resul!='') {
		// 	echo '<script>alert("Aviso!\nElimine los siguientes conceptos para continuar: '.$conceptos_resul.'");</script>'.$cont;
		// 	exit;
		// }



		$sql="DELETE FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo=1 AND
																id_concepto = '$id_concepto' AND
																id_contrato = '$id_contrato' AND
																id_empleado = '$id_empleado' AND
																id_planilla = '$id_planilla' AND
																id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		if ($query) {
			$sql="DELETE FROM nomina_planillas_ajuste_conceptos_deducir
					WHERE
					activo=1
					AND id_concepto = '$id_concepto'
					AND id_empleado = '$id_empleado'
					AND id_planilla = '$id_planilla'
					AND id_empresa  = '$id_empresa'";
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
				nivel_formula
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
				centro_costos_contrapartida
				FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto AND id_grupo_trabajo=$id_grupo_trabajo";
		$query=mysql_query($sql,$link);

		// SI TIENE CONFIGURACION POR GRUPO DE TRABAJO
		if (mysql_result($query,0,'id_concepto')>0) {
			$formula_query = mysql_result($query,0,'formula');
			$arrayConcepto['id_concepto']                              =mysql_result($query,0,'id_concepto');
			$arrayConcepto['nivel_formula']                            =mysql_result($query,0,'nivel_formula');
			$arrayConcepto['formula']                                  =($formula_query=='')? $arrayConcepto['formula']  : $formula_query ;
			$arrayConcepto['id_cuenta_colgaap']                        =mysql_result($query,0,'id_cuenta_colgaap');
			$arrayConcepto['cuenta_colgaap']                           =mysql_result($query,0,'cuenta_colgaap');
			$arrayConcepto['descripcion_cuenta_colgaap']               =mysql_result($query,0,'descripcion_cuenta_colgaap');
			$arrayConcepto['id_cuenta_niif']                           =mysql_result($query,0,'id_cuenta_niif');
			$arrayConcepto['cuenta_niif']                              =mysql_result($query,0,'cuenta_niif');
			$arrayConcepto['descripcion_cuenta_niif']                  =mysql_result($query,0,'descripcion_cuenta_niif');
			$arrayConcepto['caracter']                                 =mysql_result($query,0,'caracter');
			$arrayConcepto['centro_costos']                            =mysql_result($query,0,'centro_costos');
			$arrayConcepto['id_cuenta_contrapartida_colgaap']          =mysql_result($query,0,'id_cuenta_contrapartida_colgaap');
			$arrayConcepto['cuenta_contrapartida_colgaap']             =mysql_result($query,0,'cuenta_contrapartida_colgaap');
			$arrayConcepto['descripcion_cuenta_contrapartida_colgaap'] =mysql_result($query,0,'descripcion_cuenta_contrapartida_colgaap');
			$arrayConcepto['id_cuenta_contrapartida_niif']             =mysql_result($query,0,'id_cuenta_contrapartida_niif');
			$arrayConcepto['cuenta_contrapartida_niif']                =mysql_result($query,0,'cuenta_contrapartida_niif');
			$arrayConcepto['descripcion_cuenta_contrapartida_niif']    =mysql_result($query,0,'descripcion_cuenta_contrapartida_niif');
			$arrayConcepto['caracter_contrapartida']                   =mysql_result($query,0,'caracter_contrapartida');
			$arrayConcepto['centro_costos_contrapartida']              =mysql_result($query,0,'centro_costos_contrapartida');
		}

		// CREAR LA CADENA DEL INSERT DEL CONCEPTO
		$valueInsert.="('$id_planilla',
						'$id_empleado',
						'$id_contrato',
						'$id_concepto',
						'".$arrayConcepto['codigo']."',
						'".$arrayConcepto['descripcion']."',
						'0',
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
						'".$arrayConcepto['naturaleza']."',
						'".$arrayConcepto['imprimir_volante']."',
						'$input_calculo'
						)";

		$sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos
					(id_planilla,
					id_empleado,
					id_contrato,
					id_concepto,
					codigo_concepto,
					concepto,
					valor_concepto,
					valor_concepto_ajustado,
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
					naturaleza,
					imprimir_volante,
					valor_campo_texto)
					VALUES $valueInsert";
		// print_r($arrayConcepto);
		//$sql="INSERT INTO nomina_planillas_ajuste_empleados_conceptos (id_planilla,id_empleado,id_contrato,id_concepto,valor_concepto,id_empresa) VALUES ('$id_planilla','$id_empleado','$id_contrato','$id_concepto','$valor_concepto','$id_empresa')";
		$query=mysql_query($sql,$link);
		if ($query) {
			$sql="SELECT LAST_INSERT_ID() AS id";
			$query=mysql_query($sql,$link);
			$lastId= mysql_result($query,0,'id');

			$contOld=$cont;
			$cont++;

			// $styleImgFunction=($arrayConcepto['formula'] !='')? 'document.getElementById("divImgFunction_'.$contOld.'").style.display = "block";' : '' ;
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
                        	     		"<div class=\"campo1\" style=\"width:calc(100% - 50% - 70px - 18px );text-indent:0;\">"+

	                    	     		"<div style=\"float:left;width:100px;height:100%;border-right:1px solid #d4d4d4;text-align:right;padding-right:5px\">0</div>"+
											"<input type=\"text\" style=\"width:calc(100% - 16px - 90px);padding-left: 0px;\"  id=\"valor_concepto_'.$cont.'\" >"+
                        	     		"</div>"+
                        	     		"<div class=\"campo1\" id=\"naturaleza_'.$cont.'\" style=\"width:30px;text-align:center;\"></div>"+
                        	     		"<div class=\"campo1\" id=\"imprimir_volante_'.$cont.'\" style=\"width:30px;text-align:center;text-indent:0px;display:none;\" title=\"\"></div>"+
                        	     		"<div style=\"float:left; min-width:80px;margin-left:10px;\">"+
                	     		 		    "<div onclick=\" guardarConcepto('.$cont.',\'\')\" id=\"divImageSaveConcepto_'.$cont.'\"  title=\"Guardar Concepto\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;\"><img src=\"img/save_true.png\" id=\"ImageSaveConcepto_'.$cont.'\"></div>"+
                	     		 		    "<div onclick=\"\" id=\"divImageDeshacerConcepto_'.$cont.'\" title=\"Deshacer Cambios\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;display:none\"><img src=\"img/deshacer.png\" id=\"imgDeshacerArticuloFacturaVenta_1\"></div>"+
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

	function actualizarconcepto($id_insert,$input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$id_empresa,$link){
		//CONSULTAR EL VALOR ANTERIOR PARA CARCULAR EN LA PLANILLA
		$sql="SELECT valor_concepto,valor_concepto_ajustado,id_prestamo,id_empleado FROM nomina_planillas_ajuste_empleados_conceptos WHERE id_concepto = '$id_concepto' AND
						id_contrato = '$id_contrato' AND
						id_empleado = '$id_empleado' AND
						id_planilla = '$id_planilla' AND
						id_empresa  = '$id_empresa' AND
						id          = $id_insert";
		$query=mysql_query($sql,$link);
		$valor_concepto_anterior = (mysql_result($query,0,'valor_concepto_ajustado')>0)? mysql_result($query,0,'valor_concepto_ajustado') : mysql_result($query,0,'valor_concepto');
		$id_empleado_prestamo    = mysql_result($query,0,'id_empleado');
		$id_prestamo             = mysql_result($query,0,'id_prestamo');

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


		//ACTUALIZAR EL CAMPO CON EL NUEVO VALOR
		$sql="UPDATE nomina_planillas_ajuste_empleados_conceptos SET valor_concepto_ajustado='$valor_concepto',saldo_restante='$valor_concepto', valor_campo_texto='$input_calculo'
				WHERE id_concepto   = '$id_concepto' AND
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
				</script>'.$cont;
			// recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link);
		}
		else{
			echo '<script>alert("Error\nNo se actualizo el valor intentelo de nuevo");</script>'.$cont;
		}
	}

	function updateFinalizaContrato($terminar_contrato,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_ajuste_empleados SET terminar_contrato='$terminar_contrato' WHERE
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
		$sql="SELECT dias_liquidacion FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);

		$dias_liquidacion=mysql_result($query,0,'dias_liquidacion');

		if ($dias>$dias_liquidacion) {
			echo '<script>
						alert("Aviso!\nLos dias no pueden ser superiores a '.$dias_liquidacion.' dias ");
						document.getElementById("dias_laborados").value="'.$dias_liquidacion.'";
				</script>';
			exit;
		}

		$sql="UPDATE nomina_planillas_ajuste_empleados SET dias_laborados='$dias' WHERE
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
		$sql="UPDATE nomina_planillas_ajuste_empleados SET verificado='$valor' WHERE
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
					id_cuenta_colgaap_ajuste,
					cuenta_colgaap_ajuste,
					descripcion_cuenta_colgaap_ajuste,
					id_cuenta_niif_ajuste,
					cuenta_niif_ajuste,
					descripcion_cuenta_niif_ajuste,
					naturaleza
					FROM nomina_planillas_ajuste_empleados_conceptos
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
		$id_cuenta_colgaap_ajuste            = mysql_result($query,0,'id_cuenta_colgaap_ajuste');
		$cuenta_colgaap_ajuste               = mysql_result($query,0,'cuenta_colgaap_ajuste');
		$descripcion_cuenta_colgaap_ajuste   = mysql_result($query,0,'descripcion_cuenta_colgaap_ajuste');
		$id_cuenta_niif_ajuste               = mysql_result($query,0,'id_cuenta_niif_ajuste');
		$cuenta_niif_ajuste                  = mysql_result($query,0,'cuenta_niif_ajuste');
		$descripcion_cuenta_niif_ajuste      = mysql_result($query,0,'descripcion_cuenta_niif_ajuste');
		$naturaleza                               = mysql_result($query,0,'naturaleza');


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
							<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;">DEBITO</div>
							<div class="campoConfigLabel">Cuenta Colgaap</div><div class="campoConfig" id="cuenta_colgaap">'.$cuenta_debito.'</div>

							<div class="campoConfigLabel">Cuenta Niif</div><div class="campoConfig" id="cuenta_niif">'.$cuenta_debito_niif.'</div>


							<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;float:left;">CREDITO</div>
							<div class="campoConfigLabel">Cuenta Colgaap</div><div class="campoConfig" id="cuenta_contrapartida_colgaap">'.$cuenta_colgaap_ajuste.'</div>

							<div onclick="ventanaBuscarCuentasConcepto(\'colgaap\',\'id_cuenta_contrapartida_colgaap\',\'cuenta_contrapartida_colgaap\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
	                		    <img src="img/buscar20.png">
	                		</div>
	                		<div id="id_cuenta_contrapartida_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'id_cuenta_contrapartida_colgaap\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Sincronizar Niif" class="iconBuscar" style="overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
	                		    <img src="img/refresh.png">
	                		</div>
							<div class="campoConfigLabel">Cuenta Niif</div><div class="campoConfig" id="cuenta_contrapartida_niif">'.$cuenta_niif_ajuste.'</div>
							<div onclick="ventanaBuscarCuentasConcepto(\'niif\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
	                		    <img src="img/buscar20.png">
	                		</div>

	                		<input type="hidden" id="id_cuenta_colgaap" value="0">
							<input type="hidden" id="id_cuenta_niif" value="0">
							<input type="hidden" id="id_cuenta_contrapartida_colgaap" value="'.$id_cuenta_colgaap_ajuste.'">
							<input type="hidden" id="id_cuenta_contrapartida_niif" value="'.$id_cuenta_niif_ajuste.'">
						</div>

					</div>
				';
		}
		else{
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
						<div id="id_cuenta_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'id_cuenta_colgaap\',\'id_cuenta_niif\',\'cuenta_niif\')" title="Sincronizar Niif" class="iconBuscar" style="overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
                		    <img src="img/refresh.png">
                		</div>
                		<div onclick="ventanaBuscarCuentasConcepto(\'colgaap\',\'id_cuenta_colgaap\',\'cuenta_colgaap\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="img/buscar20.png">
                		</div>
						<div class="campoConfigLabel">Cuenta Niif</div><div class="campoConfig" id="cuenta_niif">'.$cuenta_niif.'</div>
						<div onclick="ventanaBuscarCuentasConcepto(\'niif\',\'id_cuenta_niif\',\'cuenta_niif\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="img/buscar20.png">
                		</div>

						<div style="width:100%;margin-top:10px;text-transform: uppercase;font-weight:bold;float:left;">'.$caracter_contrapartida.'</div>
						<div class="campoConfigLabel">Cuenta Colgaap</div><div class="campoConfig" id="cuenta_contrapartida_colgaap">'.$cuenta_contrapartida_colgaap.'</div>
						<div id="id_cuenta_contrapartida_colgaap_sincLoad" onclick="sincronizarCuentaNiif(\'id_cuenta_contrapartida_colgaap\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Sincronizar Niif" class="iconBuscar" style="overflow:hidden;margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;border-right:none;">
                		    <img src="img/refresh.png">
                		</div>
						<div onclick="ventanaBuscarCuentasConcepto(\'colgaap\',\'id_cuenta_contrapartida_colgaap\',\'cuenta_contrapartida_colgaap\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="img/buscar20.png">
                		</div>
						<div class="campoConfigLabel">Cuenta Niif</div><div class="campoConfig" id="cuenta_contrapartida_niif">'.$cuenta_contrapartida_niif.'</div>
						<div onclick="ventanaBuscarCuentasConcepto(\'niif\',\'id_cuenta_contrapartida_niif\',\'cuenta_contrapartida_niif\')" title="Buscar" class="iconBuscar" style="margin-top:9px;margin-left:0px;border: 1px solid #d4d4d4;">
                		    <img src="img/buscar20.png">
                		</div>
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
		$sql="SELECT naturaleza FROM nomina_planillas_ajuste_empleados_conceptos
				WHERE activo=1 AND
				id_concepto = '$id_concepto' AND
				id_contrato = '$id_contrato' AND
				id_empleado = '$id_empleado' AND
				id_planilla = '$id_planilla' AND
				id_empresa  = '$id_empresa' ";
		$query=mysql_query($sql,$link);
		$naturaleza=mysql_result($query,0,'naturaleza');

		if ($naturaleza=='Provision') {
			$sql  ="UPDATE nomina_planillas_ajuste_empleados_conceptos
						SET
							id_cuenta_colgaap_ajuste = '$id_cuenta_contrapartida_colgaap',
							id_cuenta_niif_ajuste    = '$id_cuenta_contrapartida_niif'
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
			$sql  ="UPDATE nomina_planillas_ajuste_empleados_conceptos
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
						Win_Ventana_configurar_cuentas_conceptos.close();
				</script>';
		}
		else{
			echo '<script>alert("Error\nNo se actualizaron las cuentas, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function calculaValorConceptoFormulaInput($id_insert_concepto,$id_planilla,$id_concepto,$id_contrato,$id_empleado,$cont,$variable,$id_empresa,$link){
		// SI EL CONCEPTO YA ESTA INSERTADO EN LA BASE DE DATOS
		if ($id_insert_concepto>0) {
			$sql="SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query=mysql_query($sql,$link);
			$salario_basico = mysql_result($query,0,'salario_basico');

			// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
			$sql="SELECT dias_laborados FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
			$query=mysql_query($sql,$link);
			$dias_laborados=mysql_result($query,0,'dias_laborados');

			// CONSULTAR LA FORMULA DEL CONCEPTO
			$sql="SELECT formula_original FROM nomina_planillas_ajuste_empleados_conceptos
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
				$sql="UPDATE nomina_planillas_ajuste_empleados_conceptos SET formula='$formula',valor_concepto=$valor_concepto,valor_campo_texto=$variable
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
				$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo=1
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
					$sql="UPDATE nomina_planillas_ajuste_empleados_conceptos SET formula='$formula',valor_concepto=$valor_concepto,valor_campo_texto=$variable
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
		$sql="SELECT dias_laborados FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
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
			$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo=1
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
		//$sql="SELECT dias_laborados FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
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
		//	$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo=1
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

	///////////////////////////////////////////////////////////////
	// FUNCION PARA RECALCULAR LOS CONCEPTOS CUANDO SE ACTUALIZA //
	///////////////////////////////////////////////////////////////
	function recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link){
		// VALIDAR QUE SEA DIFERENTE AL CAMPO DIAS LABORADOS DEL EMPLEADO
		if ($id_concepto=='DL') {
			// SI ES UN CONCEPTO, VALIDAR QUE SEA REQUERIDO POR OTROS CONCEPTOS Y SUS CALCULOS, SI NO ES NECESARIO PARA ACTUALIZAR EL VALOR DEL LA FORMULA DE OTROS CONCEPTOS TERMINAR FUNCION
			$sql="SELECT id FROM nomina_planillas_ajuste_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND formula_original LIKE '%{DL}%' ";
			$query=mysql_query($sql,$link);
			$id_resul=mysql_result($query,0,'id');

			if ($id_resul==0) {
				return;
			}

		}
		else{
			// CONSULTAR EL CODIGO DEL CONCEPTO
			$sql="SELECT codigo_concepto,nivel_formula FROM nomina_planillas_ajuste_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_concepto=$id_concepto";
			$query=mysql_query($sql,$link);
			$codigo_concepto = mysql_result($query,0,'codigo_concepto');
			$nivel_formula   = mysql_result($query,0,'nivel_formula');
			// SI ES UN CONCEPTO, VALIDAR QUE SEA REQUERIDO POR OTROS CONCEPTOS Y SUS CALCULOS, SI NO ES NECESARIO PARA ACTUALIZAR EL VALOR DEL LA FORMULA DE OTROS CONCEPTOS TERMINAR FUNCION
			$sql="SELECT id FROM nomina_planillas_ajuste_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND formula_original LIKE '%$codigo_concepto%' ";
			$query=mysql_query($sql,$link);
			$id_resul=mysql_result($query,0,'id');

			if ($id_resul==0) {
				return;
			}
		}

		// CONSULTAR NUEVAMENTE LOS DATOS DEL SALARIO DE CONTRATO Y LOS DIAS LABORADOS
		//CONSULTAR LOS DIAS DE LABORADOS DEL EMPLEADO
		$sql="SELECT dias_laborados FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		$dias_laborados=mysql_result($query,0,'dias_laborados');


		// CONSULTAR EL SALRAIO DEL CONTRATO DEL EMPLEADO
		$sql="SELECT salario_basico,valor_nivel_riesgo_laboral	FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$id_empresa AND id=$id_contrato AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		$salario_basico = mysql_result($query,0,'salario_basico');
		$valor_nivel_riesgo_laboral=mysql_result($query,0,'valor_nivel_riesgo_laboral');

		// CONSULTAR TODOS LOS CONCEPTOS Y ORGANIZARLOS EN UN ARRAY
		$sql="SELECT id_concepto,codigo_concepto,concepto,nivel_formula,formula_original,valor_concepto,valor_campo_texto,id_cuenta_colgaap,id_cuenta_niif,id_cuenta_contrapartida_colgaap,id_cuenta_contrapartida_niif
				FROM nomina_planillas_ajuste_empleados_conceptos
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
					$sql="UPDATE nomina_planillas_ajuste_empleados_conceptos SET
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
	}

	function actualizarSucursal($id_planilla,$sucursal,$id_empresa,$link){
		$sql="UPDATE nomina_planillas_ajuste SET id_sucursal=$sucursal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo la sucursal de la planilla\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function ventanaConfigurarConceptoDeduccion($cont,$id_planilla,$id_concepto,$id_empleado,$id_contrato,$id_empresa,$link){
		$cont_deducir=1;

		$sql="SELECT * FROM nomina_planillas_ajuste_conceptos_deducir
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_concepto=$id_concepto";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$body.='<div class="filaBoleta" id="fila_concepto_deducir_'.$cont_deducir.'">
						<div class="campo0" id="loadFila_'.$cont_deducir.'">'.$cont_deducir.'</div>
						<div class="campo1" id="concepto_deducir_'.$cont_deducir.'">'.$row['concepto_deducir'].'</div>
						<div class="campoImg" id="divImageBuscarConcepto_'.$cont_deducir.'" title="Buscar Entidad"><img src="img/buscar20.png" onclick="ventanaBuscarConceptosConceptoDeduccion('.$cont_deducir.','.$id_empleado.','.$id_contrato.')"></div>
						<div class="campo2" ><input type="text" id="valor_deducir_'.$cont_deducir.'" value="'.$row['valor_deducir'].'"></div>
						<div style="float:right; min-width:55px;padding-top: 0px !important;">
							<div onclick="eliminarConceptoDeducir('.$cont_deducir.','.$id_empleado.','.$id_contrato.','.$id_concepto.')" id="delete_'.$cont_deducir.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
						</div>
						<input type="hidden" id="id_concepto_deducir_'.$cont_deducir.'" value="'.$row['id_concepto_deducir'].'">
					</div>';
			$cont_deducir++;
		}

		$body.='<div class="filaBoleta" id="fila_concepto_deducir_'.$cont_deducir.'">
						<div class="campo0" id="loadFila_'.$cont_deducir.'">'.$cont_deducir.'</div>
						<div class="campo1" id="concepto_deducir_'.$cont_deducir.'"></div>
						<div class="campoImg" id="divImageBuscarConcepto_'.$cont_deducir.'" title="Buscar Entidad"><img src="img/buscar20.png" onclick="ventanaBuscarConceptosConceptoDeduccion('.$cont_deducir.','.$id_empleado.','.$id_contrato.')"></div>
						<div class="campo2" ><input type="text" id="valor_deducir_'.$cont_deducir.'"></div>
						<div style="float:right; min-width:55px;padding-top: 0px !important;">
							<div onclick="guardarConceptoDeducir('.$cont_deducir.','.$cont.','.$id_empleado.','.$id_contrato.','.$id_concepto.')" id="divImageSave_'.$cont_deducir.'" title="Guardar" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/save_true.png" ></div>
							<div onclick="eliminarConceptoDeducir('.$cont_deducir.','.$id_empleado.','.$id_contrato.','.$id_concepto.')" id="delete_'.$cont_deducir.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="img/delete.png"></div>
						</div>
						<input type="hidden" id="id_concepto_deducir_'.$cont_deducir.'" value="">
					</div>';

		echo '
			<style>
				#contenedor_formulario{
					overflow   : hidden;
					width      : calc(100% - 30px);
					height     : calc(100% - 10px);
					margin     : 15px;
					margin-top : 0px;
				}
				#contenedor_tabla_boletas{
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
				.campoImg{
					float            : left;
					width            : 22px;
					border-right     : 1px solid #d4d4d4;
					background-color : #F3F3F3;
					padding-top      : 1px !important;
					height           : 22px !important;
					cursor           : hand;
				}
				.campo0{
					float            : left;
					width            : 26px;
					text-indent      : 5px;
					border-right     : 1px solid #d4d4d4;
					background-color : #F3F3F3;
					padding-top      : 0px !important;
					height           : 22px !important;
				}

				.campo1{
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

				.campo2{
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

				.filaBoleta{ background-color:#F3F3F3; }

				.filaBoleta input[type=text]{
					border : 0px;
					width  : 90%;
					height : 100%;
				}

				.filaBoleta input[type=text]:focus { background: #FFF; }

				#bodyTablaConceptos{
					overflow-x       : hidden;
					overflow-y       : auto;
					width            : 100%;
					height           : calc(100% - 30px);
					background-color : #FFF;
					border-bottom    : 1px solid #d4d4d4;
				}

				#bodyTablaConceptos > div{
					overflow      : hidden;
					height        : 22px;
					border-bottom : 1px solid #d4d4d4;
				}

				#bodyTablaConceptos > div > div { /*height: 18px; background-color : #FFF; padding-top: 4px;*/ }

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

			<div id="contenedor_formulario">

				<div id="contenedor_tabla_boletas">
					<div class="headTablaBoletas">
						<div class="campo0">&nbsp;</div>
						<div class="campo2">Concepto</div>
						<div class="campo2">Valor a descontar</div>
					</div>
					<div id="bodyTablaConceptos">
						'.$body.'
					</div>

				</div>
			</div>
			<script>


			</script>
		';
	}

	function guardarConceptoDeducir($id_planilla,$cont_deducir,$cont,$id_empleado,$id_contrato,$id_concepto,$id_concepto_deducir,$valor_deducir,$id_empresa,$link){
		//CONSULTAR EL VALOR DEL CONCEPTO A DEDUCIR
		$sql="SELECT
					valor_concepto,
					valor_concepto_ajustado,
					concepto,
					cuenta_colgaap_ajuste,
					cuenta_niif_ajuste,
					naturaleza,
  					IF(caracter ='credito',cuenta_colgaap,cuenta_contrapartida_colgaap) AS cuenta_colgaap,
					IF(caracter='credito',cuenta_niif ,cuenta_contrapartida_niif) AS cuenta_niif
				FROM nomina_planillas_ajuste_empleados_conceptos
				WHERE
					activo=1
					AND id_empresa  = $id_empresa
					AND id_planilla = $id_planilla
					AND id_empleado = $id_empleado
					AND id_concepto = $id_concepto_deducir";
		$query=mysql_query($sql,$link);

		$concepto                   = mysql_result($query,0,'concepto');
		// $valor_concepto             = mysql_result($query,0,'valor_concepto');
		// $valor_concepto_ajustado    = mysql_result($query,0,'valor_concepto_ajustado');
		$valor_concepto = (mysql_result($query,0,'valor_concepto_ajustado')>0)? mysql_result($query,0,'valor_concepto_ajustado') : mysql_result($query,0,'valor_concepto') ;
		$naturaleza    = mysql_result($query,0,'naturaleza');
		if ($naturaleza=='Provision') {
			$cuenta_colgaap = mysql_result($query,0,'cuenta_colgaap_ajuste');
			$cuenta_niif    = mysql_result($query,0,'cuenta_niif_ajuste');
		}
		else{
			$cuenta_colgaap = mysql_result($query,0,'cuenta_colgaap');
			$cuenta_niif    = mysql_result($query,0,'cuenta_niif');
		}

		//CONSULTAR EL ACUMULADO DE DEDUCCIONES
		$sql="SELECT SUM(valor_deducir) AS saldo
				FROM nomina_planillas_ajuste_conceptos_deducir
				WHERE
					activo=1
					AND id_empresa          = $id_empresa
					AND id_planilla         = $id_planilla
					AND id_empleado         = $id_empleado
					AND id_concepto_deducir = $id_concepto_deducir";
		$query=mysql_query($sql,$link);
		$saldo = mysql_result($query,0,'saldo');

		// CONSULTAR EL CONCEPTO QUE SE ESTA DEDUCIENDO
		$sql="SELECT valor_concepto FROM nomina_planillas_ajuste_empleados_conceptos
				WHERE activo=1
					AND id_empresa=$id_empresa
					AND id_planilla = $id_planilla
					AND id_empleado = $id_empleado
					AND id_concepto = $id_concepto";
		$query=mysql_query($sql,$link);
		$valor_concepto_deductor = mysql_result($query,0,'valor_concepto');

		// echo '<script>
		// 		console.log("valor_deducir: '.$valor_deducir.'\n valor_concepto: '.$valor_concepto.'\n valor_concepto_ajustado: '.$valor_concepto_ajustado.'\n saldo:'.$saldo.'\n concepto:'.$concepto.'");
		// 	</script>';

		if ($valor_deducir>$valor_concepto_deductor) {
			echo '<script>alert("Error\nEl valor a descontar es mayor al valor del concepto de deduccion ");</script>'.$cont_deducir;
			exit;
		}

		if (($saldo+$valor_deducir)>$valor_concepto) {
			echo '<script>alert("Error\nEl valor a descontar es mayor al valor restante del concepto\nSaldo restante: '.($valor_concepto-$saldo).' ");</script>'.$cont_deducir;
			exit;
		}

		if ($valor_concepto>0 && ($valor_deducir>$valor_concepto || ($valor_deducir+$saldo)>$valor_concepto) ) {
			echo '<script>alert("El valor a descontar es mayor que el valor del concepto ajustado");</script>'.$cont_deducir;
			exit;
		}
		// if($valor_concepto_ajustado==0 && $valor_concepto>0 && ($valor_deducir>$valor_concepto || ($valor_deducir+$saldo)>$valor_concepto)){
		// 	echo '<script>alert("El valor a deducir es mayor que el valor del concepto");</script>'.$cont_deducir;
		// 	exit;

		// }



		$sql="INSERT INTO nomina_planillas_ajuste_conceptos_deducir(
				id_planilla,id_empleado,id_concepto,id_concepto_deducir,valor_deducir,cuenta_colgaap,cuenta_niif,id_empresa)
				VALUES
				($id_planilla,$id_empleado,$id_concepto,$id_concepto_deducir,$valor_deducir,$cuenta_colgaap,$cuenta_niif,$id_empresa) ";
		$query=mysql_query($sql,$link);

		if ($query){
			$cont_deducir_new=$cont_deducir+1;
			echo '<script>
					document.getElementById("delete_'.$cont_deducir.'").style.display="block";
					document.getElementById("divImageSave_'.$cont_deducir.'").style.display="none";

					//AGREGAR EL CONCEPTO A LA PLANILLA DE NOMINA
				 	var div=document.createElement("div");
				 	div.setAttribute("class","filaBoleta");
				 	div.setAttribute("id","fila_concepto_deducir_'.$cont_deducir_new.'");
				 	div.innerHTML="<div class=\"campo0\" id=\"loadFila_'.$cont_deducir_new.'\">'.$cont_deducir_new.'</div>"+
									"<div class=\"campo1\" id=\"concepto_deducir_'.$cont_deducir_new.'\"></div>"+
									"<div class=\"campoImg\" id=\"divImageBuscarConcepto_'.$cont_deducir_new.'\" title=\"Buscar Entidad\"><img src=\"img/buscar20.png\" onclick=\"ventanaBuscarConceptosConceptoDeduccion('.$cont_deducir_new.','.$id_empleado.','.$id_contrato.')\"></div>"+
									"<div class=\"campo2\" ><input type=\"text\" id=\"valor_deducir_'.$cont_deducir_new.'\"></div>"+
									"<div style=\"float:right; min-width:55px;padding-top: 0px !important;\">"+
										"<div onclick=\"guardarConceptoDeducir('.$cont_deducir_new.','.$cont.','.$id_empleado.','.$id_contrato.','.$id_concepto.')\" id=\"divImageSave_'.$cont_deducir_new.'\" title=\"Guardar\" style=\"width:20px; float:left; margin-top:3px;cursor:pointer;\"><img src=\"img/save_true.png\" ></div>"+
										"<div onclick=\"eliminarConceptoDeducir('.$cont_deducir_new.','.$id_empleado.','.$id_contrato.','.$id_concepto.')\" id=\"delete_'.$cont_deducir_new.'\" title=\"Eliminar Registro\" style=\"width:20px; float:left; margin-top:3px; display:none;cursor:pointer;\"><img src=\"img/delete.png\"></div>"+
									"</div>"+
									"<input type=\"hidden\" id=\"id_concepto_deducir_'.$cont_deducir_new.'\" value=\"\">";
									// guardarConceptoDeducir(cont_deducir,cont,id_empleado,id_contrato,id_concepto)
					document.getElementById("bodyTablaConceptos").appendChild(div);

				</script>'.$cont_deducir;
		}
		else{
			echo '<script>alert("Error!\nNo se guardo el concepto a deducir!\nIntentelo de nuevo");</script>';
		}
	}

	function eliminarConceptoDeducir($cont_deducir,$id_empleado,$id_concepto,$id_concepto_deducir,$id_planilla,$id_contrato,$id_empresa,$link){
		$sql="DELETE FROM nomina_planillas_ajuste_conceptos_deducir
				WHERE activo=1
				AND id_empleado         ='$id_empleado'
				AND id_concepto         ='$id_concepto'
				AND id_concepto_deducir ='$id_concepto_deducir'
				AND id_planilla         ='$id_planilla'
				AND id_empresa          ='$id_empresa' ";
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

	// ============== BUSCAR EMPLEADO EN LA INTERFAZ DE LA PLANILLA ==================//
	function buscarEmpleadoCargado($id_planilla,$filtro,$estado,$id_empresa,$link){
		//======================= CONSULTAR LOS EMPLEADOS CARGADOS EN LA PLANILLA ==================================//
	    $sql='SELECT id_empleado,documento_empleado,nombre_empleado,id_contrato,verificado,email_enviado
	    		FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa='.$id_empresa.' AND id_planilla='.$id_planilla.' '.$filtro;
	    $query=mysql_query($sql,$link);
	    $bodyEmpleados='';
	    $cont=1;
	    while ($row=mysql_fetch_array($query)) {

	    	$titleImg=($row['email_enviado']=='true')? 'Reenviar Volante por Email' : 'Enviar Volante por Email' ;


        	if ($estado==0) {
        		$bodyEmpleados.='<div class="bodyDivNominaPlanilla" >
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
        		$bodyEmpleados.='<div class="bodyDivNominaPlanilla" >
                            <div class="campo" id="divLoadEmpleado_'.$row['id_contrato'].'">'.$cont.'</div>
                            <div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_'.$row['id_contrato'].'"><img src="img/fila_selected.png"></div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:100px;text-indent:5px;">'.$row['documento_empleado'].'</div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:calc(100% - 100px - 49px - 40px);text-indent:5px;">'.$row['nombre_empleado'].'</div>
                            <div title="'.$titleImg.'" class="iconBuscar" style="margin-left: -1px;" >
                                <img class="capturaImgCheck" src="img/checkbox_'.$row['verificado'].'.png" value="'.$row['verificado'].'" id="verifica_empleado_'.$row['id_contrato'].'">
                            </div>
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

    //////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////

	//==================== FUNCION PARA GENERAR LA PLANILLA DE NOMINA ================//
    function terminarGenerar($id_planilla,$id_empresa,$id_sucursal,$link){

		// CONSULTAR LAS CUENTAS PERMITIDAS EN EL COMPROBANTE DE EGRESO
    	$sql="SELECT cuenta FROM configuracion_comprobante_egreso WHERE activo=1 AND id_empresa=$id_empresa";
    	$query=mysql_query($sql,$link);
    	$i=0;
    	while ($row=mysql_fetch_array($query)) {
    		$i++;
    		$arrayCuentaComprobante[$i]=$row['cuenta'];
    	}

    	$sql   = "SELECT dias_laborados_empleado,id_empleado,documento_empleado,nombre_empleado
					 FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query = mysql_query($sql,$link);

		while ($row=mysql_fetch_array($query)) {
			$arrayEmpleadosDatos[$row['id_empleado']] = array(
															'documento_empleado' => $row['documento_empleado'],
															'nombre_empleado' => $row['nombre_empleado'],
															);
		}

    	//ARMAR EL QUERY DEL ASIENTO
    	$sql="SELECT
				NC.concepto_ajustable,
				NPEC.codigo_concepto,
				NPEC.concepto,
				NPEC.valor_concepto,
				NPEC.valor_concepto_ajustado,
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
				NC.centro_costos_contrapartida,
				NPEC.id_tercero_ajuste,
				NPEC.cuenta_colgaap_ajuste,
				NPEC.cuenta_niif_ajuste,
				NPEC.id_centro_costos_ajuste,
				NPEC.centro_costos_ajuste
			FROM
				nomina_planillas_ajuste_empleados_conceptos AS NPEC,
				nomina_conceptos AS NC
			WHERE
				NPEC.activo=1
			AND NPEC.id_planilla = $id_planilla
			AND NPEC.id_empresa = $id_empresa
			AND NC.id=NPEC.id_concepto
			AND NC.id_empresa=$id_empresa";
    	$query=mysql_query($sql,$link);

    	while ($row=mysql_fetch_array($query)){
    		// VARIABLES CON LOS RESULTADOS DE LA CONSULTA
			$id_empleado                    = $row['id_empleado'];
			$id_concepto                    = $row['id_concepto'];
			$id_tercero                     = $row['id_tercero'];
			$id_tercero_contrapartida       = $row['id_tercero_contrapartida'];
			$id_empleado_cruce              = $row['id_empleado_cruce'];
			$cuenta_colgaap                 = $row['cuenta_colgaap'];
			$cuenta_contrapartida_colgaap   = $row['cuenta_contrapartida_colgaap'];
			$cuenta_niif                    = $row['cuenta_niif'];
			$cuenta_contrapartida_niif      = $row['cuenta_contrapartida_niif'];

			$concepto_ajustable             = $row['concepto_ajustable'];
			$id_tercero_ajuste              = $row['id_tercero_ajuste'];
			$cuenta_colgaap_ajuste          = $row['cuenta_colgaap_ajuste'];
			$cuenta_niif_ajuste             = $row['cuenta_niif_ajuste'];
			$valor_concepto                 = $row['valor_concepto'];
			$valor_concepto_ajustado        = $row['valor_concepto_ajustado'];

			$id_centro_costos               = $row['id_centro_costos'];
			$centro_costos_contrapartida    = $row['id_centro_costos_contrapartida'];
			$centro_costos_ajuste           = $row['centro_costos_ajuste'];

			$caracter                       = $row['caracter'];
			$caracter_contrapartida         = $row['caracter_contrapartida'];
			$naturaleza                     = $row['naturaleza'];

			$id_prestamo                    = $row['id_prestamo'];
			$tercero                        = $row['tercero'];
			$tercero_cruce                  = $row['tercero_cruce'];
			$concepto                       = $row['concepto'];
			$centro_costos                  = $row['centro_costos'];
			$id_centro_costos_contrapartida = $row['id_centro_costos_contrapartida'];
			$centro_costos_contrapartida    = $row['centro_costos_contrapartida'];

			// SI ES CONCEPTO AJUSTE VALIDAR QUE TENGA LA CUENTA DE AJUSTE
			if ($concepto_ajustable=='true' && $naturaleza=='Deduccion' && ($cuenta_colgaap_ajuste=='' || $cuenta_niif_ajuste=='' || $cuenta_colgaap_ajuste=='0' || $cuenta_niif_ajuste=='0') ) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].'\nEsta configurado como de ajuste, pero en su configuracion no tiene establecida una cuenta contable!\nVerifiquelo desde el panel de control e intentelo de nuevo");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// VALIDAR SI EL CONCEPTO SE CREO CON CRUCE CON UNA ENTIDAD ENTONCES QUE EL ID SEA DIFERENTE AL DEL CLIENTE
			if ($tercero=='Entidad' && $id_empleado_cruce==$id_empleado) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			if ($tercero_cruce=='Entidad' && $id_empleado_cruce==$id_tercero_contrapartida) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// VALIDAR SI EL CONCEPTO NECESITA CENTRO DE COSTOS Y SI NO SE CREO EL CCOS EN EL CONTRATO DEL EMPLEADO
			if ($centro_costos=='true' && $row['id_centro_costos']=='') {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			if ($centro_costos_contrapartida=='true' && $row['id_centro_costos_contrapartida']=='') {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			if ($centro_costos_ajuste=='true' && $row['id_centro_costos_ajuste']=='') {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// VALIDAR QUE EXISTA COMO TERCERO EL EMPLEADO
			if ($id_empleado=='' || $id_tercero_contrapartida=='') {

				echo '<script>
						alert("'.$arrayEmpleadosDatos[$id_empleado]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado]['nombre_empleado'].' \nNo esta creado como tercero!\nVerifiquelos dede el modulo de empleados");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// SI EL CONCEPTO ES UNA APROPIACION
			if ($naturaleza=='Apropiacion') {
				$ccos_debito            = ($caracter=='debito')? $id_centro_costos : $id_centro_costos_contrapartida ;
				$tercero_debito         = ($caracter=='debito')? $id_tercero : $id_tercero_contrapartida ;
				$cuenta_colgaap_debito  = ($caracter=='debito')? $cuenta_colgaap : $cuenta_contrapartida_colgaap ;
				$cuenta_niif_debito     = ($caracter=='debito')? $cuenta_niif : $cuenta_contrapartida_niif ;

				$ccos_credito           = ($caracter=='credito')? $id_centro_costos : $id_centro_costos_contrapartida ;
				$tercero_credito        = ($caracter=='credito')? $id_tercero : $id_tercero_contrapartida ;
				$cuenta_colgaap_credito = ($caracter=='credito')? $cuenta_colgaap : $cuenta_contrapartida_colgaap ;
				$cuenta_niif_credito    = ($caracter=='credito')? $cuenta_niif : $cuenta_contrapartida_niif ;

			}
			// SI EL CONCEPTO NO ES UNA APROPIACION TOMAR LA CUENTA DE AJUSTE CONFIGURADA EN EL CONCEPTO
			else{
				$ccos_debito           = $row['id_centro_costos_ajuste'];
				$tercero_debito        = $id_tercero_ajuste;
				$cuenta_colgaap_debito = $cuenta_colgaap_ajuste;
				$cuenta_niif_debito    = $cuenta_niif_ajuste;

				$ccos_credito           = ($caracter=='credito')? $id_centro_costos : $id_centro_costos_contrapartida;
				$tercero_credito        = ($caracter=='credito')? $id_tercero : $id_tercero_contrapartida;
				$cuenta_colgaap_credito = ($caracter=='credito')? $cuenta_colgaap : $cuenta_contrapartida_colgaap;
				$cuenta_niif_credito    = ($caracter=='credito')? $cuenta_niif : $cuenta_contrapartida_niif;

			}

			$diferencia=$valor_concepto-$valor_concepto_ajustado;
			$ajuste=abs($diferencia);

			// SI LA DIFERENCIA ES MENOR, SACAR DE LA CXP Y DEVOLVER AL GASTO
			if ($diferencia<0) {
				$arrayAsientosColgaap[$tercero_debito][$cuenta_colgaap_debito]['ccos']      =  $ccos_debito;
				$arrayAsientosColgaap[$tercero_credito][$cuenta_colgaap_credito]['ccos']    =  $ccos_credito;
				$arrayAsientosColgaap[$tercero_debito][$cuenta_colgaap_debito]['debito']    += $ajuste;
				$arrayAsientosColgaap[$tercero_credito][$cuenta_colgaap_credito]['credito'] += $ajuste;

				$arrayAsientosNiif[$tercero_debito][$cuenta_niif_debito]['ccos']            =  $ccos_debito;
				$arrayAsientosNiif[$tercero_credito][$cuenta_niif_credito]['ccos']          =  $ccos_credito;
				$arrayAsientosNiif[$tercero_debito][$cuenta_niif_debito]['debito']          += $ajuste;
				$arrayAsientosNiif[$tercero_credito][$cuenta_niif_credito]['credito']       += $ajuste;
			}
			// SI LA DIFERENCIA ES MAYOR, SACAR DE GASTO A CXP
			else if($diferencia>0){
				$arrayAsientosColgaap[$tercero_debito][$cuenta_colgaap_debito]['ccos']     =  $ccos_debito;
				$arrayAsientosColgaap[$tercero_credito][$cuenta_colgaap_credito]['ccos']   =  $ccos_credito;
				$arrayAsientosColgaap[$tercero_debito][$cuenta_colgaap_debito]['credito']  += $ajuste;
				$arrayAsientosColgaap[$tercero_credito][$cuenta_colgaap_credito]['debito'] += $ajuste;

				$arrayAsientosNiif[$tercero_debito][$cuenta_niif_debito]['ccos']           =  $ccos_debito;
				$arrayAsientosNiif[$tercero_credito][$cuenta_niif_credito]['ccos']         =  $ccos_credito;
				$arrayAsientosNiif[$tercero_debito][$cuenta_niif_debito]['credito']        += $ajuste;
				$arrayAsientosNiif[$tercero_credito][$cuenta_niif_credito]['debito']       += $ajuste;
			}

			// $arrayNominaContabilizacion[$id_empleado][$cuenta_colgaap][$caracter]                                           += $row['valor_concepto'];
			// $arrayNominaContabilizacion[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += $row['valor_concepto'];

			$arrayNominaContabilizacion[$tercero_debito][$cuenta_colgaap_debito]['debito']    += $valor_concepto_ajustado;
			$arrayNominaContabilizacion[$tercero_credito][$cuenta_colgaap_credito]['credito'] += $valor_concepto_ajustado;

			// ARRAY CON LA INFORMACION DEL CONCEPTO
			$arrayInfoConcepto[$tercero_debito][$cuenta_colgaap_debito]= array(
																				'concepto'           => $row['concepto'],
																				'naturaleza'         => $row['naturaleza'],
																				'concepto_ajustable' => $row['concepto_ajustable'],
																				'cuenta_niif'        => $cuenta_niif_credito,
																				'id_concepto'        => $row['id_concepto']
																				);

			$arrayInfoConcepto[$tercero_credito][$cuenta_colgaap_credito]= array(
																				'concepto'           => $row['concepto'],
																				'naturaleza'         => $row['naturaleza'],
																				'concepto_ajustable' => $row['concepto_ajustable'],
																				'cuenta_niif'        => $cuenta_niif_credito,
																				'id_concepto'        => $row['id_concepto']
																				);


    	}

    	$tablaDebug='<table style="font-size:11px;">
    				<tr>
	    				<td >CUENTA</td>
	    				<td >DEBITO</td>
	    				<td >CREDITO</td>
	    				<td >CENTRO COSTOS</td>
    				</tr>';
    	foreach ($arrayAsientosColgaap as $id_tercero => $arrayAsientosColgaapArray) {
    		foreach ($arrayAsientosColgaapArray as $cuenta => $arrayResul) {
    			$tablaDebug.='<tr>
    							<td >'.$cuenta.'</td>
    							<td >'.$arrayResul['debito'].'</td>
    							<td >'.$arrayResul['credito'].'</td>
    							<td >'.$arrayResul['ccos'].'</td>
    						</tr>';
    		}
    	}
    	$tablaDebug.='</table>';

    	// print_r($arrayAsientosColgaap);
    	$tablaDebug.='<table style="font-size:11px;">
	    				<tr>
		    				<td >CUENTA NIIF</td>
		    				<td >DEBITO</td>
		    				<td >CREDITO</td>
		    				<td >CENTRO COSTOS</td>
	    				</tr>';
    	foreach ($arrayAsientosNiif as $id_tercero => $arrayAsientosNiifArray) {
    		foreach ($arrayAsientosNiifArray as $cuenta => $arrayResul) {
    			$tablaDebug.='<tr>
    							<td >'.$cuenta.'</td>
    							<td >'.$arrayResul['debito'].'</td>
    							<td >'.$arrayResul['credito'].'</td>
    							<td >'.$arrayResul['ccos'].'</td>
    						</tr>';
    		}
    	}
    	$tablaDebug.='</table>';
    	// echo $tablaDebug;
    	// exit;



    	//ACTUALIZAR EL DOCUMENTO
    	$fecha_generacion=date("Y-m-d");
    	$sql="UPDATE nomina_planillas_ajuste SET estado=1,fecha_generacion='$fecha_generacion' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		if (!$query) {
			echo '<script>
					alert("Error\nNo se actualizo la planilla, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//CONSULTAR EL CONSECUTIVO DEL DOCUMENTO
		$sql   = "SELECT consecutivo,fecha_documento,fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query = mysql_query($sql,$link);
		$fecha_documento = mysql_result($query,0,'fecha_documento');
		$consecutivo     = mysql_result($query,0,'consecutivo');
		$fecha_inicio    = mysql_result($query,0,'fecha_inicio');
		$fecha_final     = mysql_result($query,0,'fecha_final');
		$id_sucursal     = mysql_result($query,0,'id_sucursal');

    	foreach ($arrayAsientosColgaap as $id_tercero => $arrayResul) {
    		foreach ($arrayResul as $cuenta => $arrayResul1) {
    			$id_centro_costos=$arrayResul1['ccos'];
				$acumDebito  += $arrayResul1['debito'];
				$acumCredito += $arrayResul1['credito'];

    			if ($arrayResul1['debito']>$arrayResul1['credito']) {
					$debito  = $arrayResul1['debito']-$arrayResul1['credito'];
					$credito = 0;
    			}
    			else{
					$debito  = 0;
					$credito = $arrayResul1['credito']-$arrayResul1['debito'];
    			}

    			if ($debito==0 && $credito==0) { continue; }

				$valueInsertAsientos .= "('$id_planilla',
										'$consecutivo',
										'PA',
										'$id_planilla',
										'$consecutivo',
										'PA',
										'Planilla Ajuste Nomina',
										'".$fecha_documento."',
										'".$debito."',
										'".$credito."',
										'".$cuenta."',
										'".$id_tercero."',
										'$id_centro_costos',
										'$id_sucursal',
										'$id_empresa'),";


    		}
    	}

	  	foreach ($arrayAsientosNiif as $id_empleado => $arrayResul) {
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

				$valueInsertAsientosNiif .= "('$id_planilla',
												'$consecutivo',
												'PA',
												'$id_planilla',
												'$consecutivo',
												'PA',
												'Planilla Ajuste Nomina',
												'".$fecha_documento."',
												'".$debito."',
												'".$credito."',
												'".$cuenta."',
												'".$id_empleado."',
												'$id_centro_costos',
												'$id_sucursal',
												'$id_empresa'),";
    		}
    	}

    	$acumDebito = round($acumDebito,$_SESSION['DECIMALESMONEDA']);
		$acumCredito = round($acumCredito,$_SESSION['DECIMALESMONEDA']);
    	if ($acumDebito!=$acumCredito) {
    		$sql="UPDATE nomina_planillas_ajuste SET estado=0  WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
    		echo '<script>
    				alert("Los saldos contables tienen una diferencia de '.($acumDebito-$acumCredito).' ");
					console.log("'.$acumDebito.' - '.$acumCredito.' = '.($acumDebito - $acumCredito).' ");
    				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    			</script>';
    		exit;
    	}
    	// print_r($arrayNominaContabilizacion);
    	// RECORRER ARRAY PARA INSERTAR LA TABLA NOMINA EMPLEDOS CONTABILIZACION
    	$valueInsertConfiguracion='';
    	// PRIMERA CAPA ID DEL TERCERO
    	foreach ($arrayNominaContabilizacion as $id_tercero => $arrayNominaContabilizacionArray) {
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
    			if ($debito>0) { continue; }

    			// RECORRER LA CONFIGURACION DEL COMPROBANTE
				for ($j=1; $j <= $i; $j++) {
					// if ($arrayInfoConcepto[$id_tercero][$cuenta]['concepto_ajustable']=='true') {
					// 	continue;
					// }
					// $total_sin_abono=($arrayNaturaleza[$id_tercero][$cuenta]=='Provision')? 0 : abs($debito-$credito);
					// $total_sin_abono_provision=($arrayNaturaleza[$id_tercero][$cuenta]=='Provision')? abs($debito-$credito) : 0;

					$total_sin_abono=($arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza']=='Provision')? 0 : abs($debito-$credito);
					$total_sin_abono_provision=($arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza']=='Provision')? abs($debito-$credito) : 0;

					// echo '<script>console.log("'.$arrayInfoConcepto[$id_tercero][$cuenta]['concepto'].' '.$arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza'].' '.$total_sin_abono.' - '.$total_sin_abono_provision.' ");</script>';

    				if (strpos($cuenta, $arrayCuentaComprobante[$j])===0) {
    				    $valueInsertConfiguracion .= "('$id_tercero',
				    									'PA',
    				    								'$id_planilla',
    				    								'$consecutivo',
    				    								'".$arrayInfoConcepto[$id_tercero][$cuenta]['id_concepto']."',
    				    								'$cuenta',
    				    								".$arrayInfoConcepto[$id_tercero][$cuenta]['cuenta_niif'].",
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
				id_sucursal,
				id_empresa)
				VALUES $valueInsertConfiguracion ";
		$query=mysql_query($sql,$link);
		if (!$query) {
			$sql="UPDATE nomina_planillas_ajuste SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
			echo '<script>
					alert("Error\nNo se insertaron las cuentas para cruzar el comprobante de egreso");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
		$valueInsertAsientosNiif = substr($valueInsertAsientosNiif, 0, -1);

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
			$sql="UPDATE nomina_planillas_ajuste SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
			echo '<script>
						alert("Error\nNo se insertaron los asientos Colgaap");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//INSERT NIIF
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
			$sql="UPDATE nomina_planillas_ajuste SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query=mysql_query($sql,$link);
			moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
			echo '<script>
					alert("Error\nNo se insertaron los asientos Niif");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		// moverSaldoDiasPlanillas('eliminar',$id_planilla,$id_empresa,$link);

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
					VALUES ($id_planilla,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Generar','Planilla Ajuste de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','PA')";
		$queryLog = mysql_query($sqlLog,$link);

		echo '<script>
				Ext.get("contenedor_PlanillaAjusteNomina").load({
					url     : "ajuste_nomina/bd/grillaContableBloqueada.php",
					scripts : true,
					nocache : true,
					params  :
					{
						id_planilla       : "'.$id_planilla.'",
						opcGrillaContable : "PlanillaAjusteNomina",
					}
				});
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
    }


    //======== FUNCION PARA MOVER LOS SALDOS DE DIAS DE LAS PLANILLAS GENERADAS ============//
    function moverSaldoDiasPlanillas($accion,$id_planilla,$id_empresa,$link){
    	// CONSULTAR LAS FECHAS DE LA PLANILLA DE LIQUIDACION
    	$sql="SELECT fecha_inicio,fecha_final FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
    	$query=mysql_query($sql,$link);

		$fecha_inicio = mysql_result($query,0,'fecha_inicio');
		$fecha_final  = mysql_result($query,0,'fecha_final');

    	// CONSULTAR LOS EMPLEADOS DE LA PLANILLA DE LIQUIDACION
    	// $sql="SELECT id_empleado FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
    	$sql="SELECT id_empleado,id_concepto,concepto
    			FROM nomina_planillas_ajuste_empleados_conceptos
    			WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa AND naturaleza='Provision'";
    	$query=mysql_query($sql,$link);
    	$whereIdEmpleadosProvision='';
    	while ($row=mysql_fetch_array($query)) {
    		$whereIdEmpleadosProvision.=($whereIdEmpleadosProvision=='')? ' NPE.id_empleado='.$row['id_empleado']  : ' OR NPE.id_empleado='.$row['id_empleado'];
    		$arrayIdConceptos[$row['id_empleado']].=($arrayIdConceptos[$row['id_empleado']]=='')? ' id_concepto='.$row['id_concepto'] : ' OR id_concepto='.$row['id_concepto'] ;
    	}

    	// CONSULTAR LAS PLANILLAS DE LAS PROVISIONES DE LOS EMPLEADOS
		$sql="SELECT
					NP.id,
					NPE.dias_laborados,
					NPE.id_empleado
				FROM
					nomina_planillas AS NP,
					nomina_planillas_empleados AS NPE
				WHERE
					NP.activo       = 1
				AND NP.estado       = 1
				AND NP.id_empresa   = $id_empresa
				AND NP.fecha_inicio >= '$fecha_inicio'
				AND NP.fecha_final  <= '$fecha_final'
				AND ($whereIdEmpleadosProvision)
				GROUP BY NP.id,NPE.id_empleado";
		$query=mysql_query($sql,$link);
		$whereIdPlanillas='';
		while ($row=mysql_fetch_array($query)) {
			$where.=($where=='')? '(id_planilla='.$row['id'].' AND id_empleado='.$row['id_empleado'].' AND ('.$arrayIdConceptos[$row['id_empleado']].') )'
								: ' OR (id_planilla='.$row['id'].' AND id_empleado='.$row['id_empleado'].' AND ('.$arrayIdConceptos[$row['id_empleado']].') )' ;
			// $whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id']  ;
		}

		if ($accion=='agregar') {

			$sql="UPDATE nomina_planillas_empleados_conceptos SET saldo_dias_laborados = dias_laborados
					WHERE activo = 1
					AND ($where)
					AND naturaleza='Provision'";
			$query=mysql_query($sql,$link);

		}
		else if($accion=='eliminar'){

			$sql="UPDATE nomina_planillas_empleados_conceptos SET saldo_dias_laborados = 0
					WHERE activo = 1
					AND ($where)
					AND naturaleza='Provision'";
			$query=mysql_query($sql,$link);

		}
    }

    //==================== MOVER LAS CUENTAS DE LA PLANILLA ==============================//
    function moverCuentasPlanilla($accion,$id_planilla,$id_empresa,$link){
    	if ($accion=='eliminar') {
    		//ASIENTOS COLGAAP
    		$sql="DELETE FROM nomina_planillas_empleados_contabilizacion WHERE id_empresa='$id_empresa' AND id_planilla='$id_planilla' AND tipo_planilla='PA' ";
    		$queryColgaap=mysql_query($sql,$link);
    		//ASIENTOS COLGAAP
    		$sql="DELETE FROM asientos_colgaap WHERE id_empresa='$id_empresa' AND id_documento='$id_planilla' AND tipo_documento='PA'";
    		$queryColgaap=mysql_query($sql,$link);
    		//ASIENTOS NIIF
    		$sql="DELETE FROM asientos_niif WHERE id_empresa='$id_empresa' AND id_documento='$id_planilla' AND tipo_documento='PA'";
    		$queryNiif=mysql_query($sql,$link);

    		if (!$queryColgaap) {
    			echo '<script>alert("Error\nNo se Eliminaron los asientos Colgaap, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
    			exit;
    		}
    		if (!$queryNiif) {
    			echo '<script>alert("Error\nNo se Eliminaron los asientos Colgaap, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
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

    	//MOVER LAS CUENTAS DEL DOCUMENTO
    	moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
    	//RENOVAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR

		// ACTUALIZAR EL SALDO DE LAS PLANILLAS
		moverSaldoDiasPlanillas('agregar',$id_planilla,$id_empresa,$link);
    	$sql="UPDATE nomina_planillas_ajuste SET estado=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
    	$query=mysql_query($sql,$link);
    	if ($query) {
    		//INSERTAR EL LOG DE EVENTOS
			$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id_planilla,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Editar','Planilla Ajuste de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','PA')";
			$queryLog = mysql_query($sqlLog,$link);
    		echo '<script>
					Ext.get("contenedor_PlanillaAjusteNomina").load({
						url     : "ajuste_nomina/grillaPlanilla.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_planilla       : "'.$id_planilla.'",
							opcGrillaContable : "PlanillaAjusteNomina",
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    	else{
    		echo '<script>alert("Error\nSe eliminaron los asientos pero no se actualizo el documento\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>';
    	}
    }

    //======================= CANCELAR LA PLANILLA GENERADA ===============================//
    function cancelarPlanillaNomina($id_planilla,$id_empresa,$link){
    	global $id_sucursal;
    	//VALIDAR QUE NO TENFGA DOCUMENTO CRUCE REALCIONADOS
    	validaDocumentoCruce($id_planilla,$id_empresa,$link);

    	$sql="SELECT estado,consecutivo FROM nomina_planillas_ajuste WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa'";
   		$query=mysql_query($sql,$link);
		$estado      = mysql_result($query,0,'estado');
		$consecutivo = mysql_result($query,0,'consecutivo');
		$opc_funcion = '';
    	$sql="UPDATE nomina_planillas_ajuste SET estado=3 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";

   		if ($estado==3) {
   			echo '<script>alert("La planilla ya esta cancelada");</script>';
   			exit;
   		}
   		else if ($estado==0 && ($consecutivo==0 || $consecutivo=='') ) {
			$sql="UPDATE nomina_planillas_ajuste SET activo=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa'";
			$opc_funcion = 'delete';
		}
		else if ($estado<>0) {
			moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
		}

    	$query=mysql_query($sql,$link);
    	if ($query) {
    		//INSERTAR EL LOG DE EVENTOS
			$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id_planilla,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Cancelar','Planilla Ajuste de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','PA')";
			$queryLog = mysql_query($sqlLog,$link);
    		echo '<script>
					// Ext.get("contenedor_PlanillaAjusteNomina").load({
					// 	url     : "ajuste_nomina/bd/grillaContableBloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_planilla       : "'.$id_planilla.'",
					// 		opcGrillaContable : "PlanillaAjusteNomina",
					// 	}
					// });
    				cerrarPlanilla("'.$opc_funcion.'");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    	else{
    		echo '<script>alert("Error\nSe eliminaron los asientos pero no se actualizo el documento\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>';
    	}
    }

   	//======================= RESTAURAR LA PLANILLA ELIMINADA =============================//
   	function restaurarPlanillaNomina($id_planilla,$id_empresa,$link){
   		global $id_sucursal;
   		$sql="UPDATE nomina_planillas_ajuste SET estado=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
    	$query=mysql_query($sql,$link);
    	if ($query) {
    		//INSERTAR EL LOG DE EVENTOS
			$sqlLog   = "INSERT INTO log_documentos_contables (id_documento,id_usuario,usuario,actividad,descripcion,id_sucursal,id_empresa,ip,tipo_documento)
						VALUES ($id_planilla,".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREUSUARIO']."','Restaurar','Planilla Ajuste de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','PA')";
			$queryLog = mysql_query($sqlLog,$link);
    		echo '<script>
					Ext.get("contenedor_PlanillaAjusteNomina").load({
						url     : "ajuste_nomina/grillaPlanilla.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_planilla       : "'.$id_planilla.'",
							opcGrillaContable : "PlanillaAjusteNomina",
						}
					});
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
    	}
    	else{
    		echo '<script>alert("Error\nNo se actualizo el documento, Intentelo de nuevo \nSi el problema continua comuniquese con el administrador del sistema");</script>';
    	}
   	}

   	//====================== CALCULAR LOS TOTALES DE LA PLANILLA ==========================//
   	function calcularValoresPlanilla($id_planilla,$id_empresa,$link){
   		$sql="SELECT valor_concepto,valor_concepto_ajustado,naturaleza FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
   		$query=mysql_query($sql,$link);
		$acumDevengo     =0;
		$acumDeduce      =0;
		$acumApropiacion =0;
		$acumProvision   =0;
   		while ($row=mysql_fetch_array($query)) {
   			// $valor=($row['valor_concepto_ajustado']>0)? $row['valor_concepto_ajustado'] : $row['valor_concepto'] ;
   			$valor=$row['valor_concepto_ajustado'] ;
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
		$sql   = "UPDATE nomina_planillas_ajuste SET  observacion='$observacion' WHERE id='$id' AND id_empresa=$id_empresa";
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
		$sql="SELECT id_empleado,id_contrato,nombre_empleado FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
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
		else{ $MS=35 ; $MD = 10;$MI = 15;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
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
		if ($fun=='true') {
			$sql="UPDATE nomina_planillas_ajuste_empleados SET email_enviado='true' WHERE activo=1 AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND id_empleado=$id_empleado AND id_empresa=$id_empresa";
			$query=mysql_query($sql,$link);
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
				<script>
					alert("Error\nNo se envio el email, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
				</script>';
		}
	}

	//FUNCION PARA VALIDAR QUE NO TENGA UN DOCUMENTO CREUCE RELACIONADO
	function validaDocumentoCruce($idDocumento,$id_empresa,$link){
		$id_sucursal=$_SESSION['SUCURSAL'];
		$sqlNota    = "SELECT consecutivo_documento, tipo_documento FROM asientos_colgaap WHERE id_documento_cruce = '$idDocumento' AND tipo_documento_cruce='PA' AND tipo_documento<>'PA' AND activo=1 AND id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';
		while ($row=mysql_fetch_array($queryNota)) {
			$doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento'];
		}
		if ($doc_cruces != '') { echo '<script>
										alert("Error!\nEsta Planilla tienen relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nPor favor anule los documentos para poder modificar el documento")
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

		if ($cont1 > 0 || $cont2 > 0) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
		}

	}

	// FUNCION PARA APROXIMAR LOS CONCEPTOS
	function aproximar($numero=0,$aproximacion=0){
		// REDONDEAR CIFRA
		$numero = round($numero);
		$tamano1 = count($numero);
		$numero1 = substr($numero,0,($tamano1-$aproximacion));
		// TOMAR LAS CIFRAS A APROXIMAR
		$numero2 = substr($numero, -$aproximacion);
		$tamano2 = count($numero2);
		// CIFRA A AUMENTAR O DISMINUIR
		$numero_principal = substr($numero2, 0,1);
		$numero_aproximar = substr($numero2, 1,1);
		// return $numero_aproximar;

		if ($numero_aproximar>=5) {
			$numero1++;
			for ($i=0; $i <=$tamano2 ; $i++) {
				$cifra_0.='0';
			}
			return $numero1.$cifra_0;
		}
		else{
			// $numero1--;
			for ($i=0; $i <=$tamano2 ; $i++) {
				$cifra_0.='0';
			}
			return $numero1.$cifra_0;
		}
	}

?>