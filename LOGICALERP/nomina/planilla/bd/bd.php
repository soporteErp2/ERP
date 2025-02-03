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
		if ($opc<>'guardarFechaPlanilla' && $opc<>'enviarVolanteUnicoEmpleado' && $opc<>'enviarPlanillaNomina' && $opc<>'cargarConceptosEmpleado') {
			verificaCierre($id_planilla,'nomina_planillas',$id_empresa,$link);
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

		case 'ventanaConfigurarHorasExtras':
			ventanaConfigurarHorasExtras($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link);
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

		case 'buscarEmpleadoCargado':
			buscarEmpleadoCargado($id_planilla,$filtro,$estado,$id_empresa,$link);
			break;

		case 'guardarObservacionEmpleado':
			guardarObservacionEmpleado($observacion,$id_planilla,$id,$id_empresa,$link);
			break;

		case 'actualizaRecalcularFormula':
			actualizaRecalcularFormula($id_contrato,$id_empleado,$id_planilla,$recalcular_concepto,$id_empresa,$link);
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
		if ($campo=='fecha_final' || $campo=='fecha_inicio') {

			// if ($campo=='fecha_inicio') {
				$whereFecha   = " AND (fecha_inicio>='".$fecha."' AND fecha_final<='".$fecha."') ";
			// 	$mensajeError = 'despues de esa fecha';
			// }
			// else if($campo=='fecha_final'){
			// 	$whereFecha   = " AND (fecha_inicio>='".$fecha."' AND fecha_final<='".$fecha."')";
			// 	$mensajeError = 'antes de esa fecha';
			// }

			// VALIDAR QUE NO EXISTAN PLANILLAS DE AJUSTE DE ESE PERIODO, SI EXISTEN, ENTONCES NO SE ACTUALIZA LA FECHA
			$sql="SELECT COUNT(id) AS cont FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 $whereFecha";
			$query=mysql_query($sql,$link);
			$cont_planillas = mysql_result($query,0,'cont');
			if ($cont_planillas>0) {
				$sql="SELECT $campo FROM nomina_planillas WHERE activo=1 ANd id_empresa=$id_empresa AND id=$id_planilla";
				$query=mysql_query($sql,$link);
				$fecha_old = mysql_result($query,0,$campo);

				$inputFecha=($campo=='fecha_inicio')? 'document.getElementById("fechaNominaPlanilla").value="'.$fecha_old.'";' :
														'document.getElementById("fechaFinalNominaPlanilla").value="'.$fecha_old.'";' ;

				echo '<script>
						alert("Error!\nHay '.$cont_planillas.' planillas de ajuste creadas '.$mensajeError.', no se pueden crear mas documentos en esa fecha hasta que edite las planillas de ajuste de ese periodo");
						'.$inputFecha.'
					</script>';
				exit;
			}

		}


		$sql   = "UPDATE nomina_planillas SET $campo='$fecha' WHERE activo=1 AND id=$id_planilla AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if (!$query) { echo '<script>alert("Error!\nNo se guardo la fecha, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	function guardarTipoLiquidacion($id_planilla,$id_tipo_liquidacion,$id_empresa,$link){
		$sql   = "UPDATE nomina_planillas SET id_tipo_liquidacion='$id_tipo_liquidacion' WHERE activo=1 AND id=$id_planilla AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if (!$query) { echo '<script>alert("Error!\nNo se guardo la fecha, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	function agregarEmpleado($id_planilla,$id_contrato,$cont,$id_empresa,$link){
		$fecha = date("Y-m-d");

		//CONSULTAR LOS DIAS DE LIQUIDACION DE LA PLANILLA
		$sql   = "SELECT dias_liquidacion FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query = mysql_query($sql,$link);
		$dias_liquidacion = mysql_result($query,0,'dias_liquidacion');

		//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
		$sql   = "SELECT id_empleado,
						tipo_documento_empleado,
						documento_empleado,
						nombre_empleado,
						numero_contrato,
						salario_basico,
						fecha_inicio_nomina,
						IF(fecha_fin_contrato <= '$fecha', 'Si', 'No') AS terminar_contrato,
						id_grupo_trabajo,
						grupo_trabajo,
						valor_nivel_riesgo_laboral
					FROM empleados_contratos
					WHERE activo=1
						AND (estado=0 OR estado=2)
						AND id_empresa=$id_empresa
						AND id=$id_contrato";
		$query = mysql_query($sql,$link);

		$id_empleado                = mysql_result($query,0,'id_empleado');
		$tipo_documento_empleado    = mysql_result($query,0,'tipo_documento_empleado');
		$documento_empleado         = mysql_result($query,0,'documento_empleado');
		$nombre_empleado            = mysql_result($query,0,'nombre_empleado');
		$numero_contrato            = mysql_result($query,0,'numero_contrato');
		$salario_basico             = mysql_result($query,0,'salario_basico');
		$fecha_inicio_nomina        = mysql_result($query,0,'fecha_inicio_nomina');
		$id_grupo_trabajo           = mysql_result($query,0,'id_grupo_trabajo');
		$grupo_trabajo              = mysql_result($query,0,'grupo_trabajo');
		$terminar_contrato          = mysql_result($query,0,'terminar_contrato');
		$valor_nivel_riesgo_laboral = mysql_result($query,0,'valor_nivel_riesgo_laboral');

		// CONSULTAR LA SUCURSAL DEL EMPLEADO
		$sql         = "SELECT id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
		$query       = mysql_query($sql,$link);
		$id_sucursal = mysql_result($query,0,'id_sucursal');

		//INSERTAR EL EMPLEADO EN LA PLANILLA
		$sql   = "INSERT INTO nomina_planillas_empleados(id_planilla,id_empleado,tipo_documento,documento_empleado,nombre_empleado,id_contrato,dias_laborados,dias_laborados_empleado,terminar_contrato,id_sucursal,id_empresa)
					VALUES ('$id_planilla','$id_empleado','$tipo_documento_empleado','$documento_empleado','$nombre_empleado','$id_contrato','$dias_liquidacion','$dias_liquidacion','No','$id_sucursal','$id_empresa') ";
		$query = mysql_query($sql,$link);
		if ($query) {

			//CONSULTAR TODOS LOS CONCEPTOS CON CARGA AUTOMATICA DE LA BASE DE DATOS
			$sql   = "SELECT id,
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
							imprimir_volante,
							resta_dias
					FROM nomina_conceptos
					WHERE activo=1
						AND id_empresa=$id_empresa
						AND carga_automatica='true'
					ORDER BY nivel_formula ASC";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)){
				$id             = $row['id'];
				$tipo_concepto  = $row['tipo_concepto'];
				$nivel_formula  = $row['nivel_formula'];
				$row['formula'] = str_replace(" ","",$row['formula']);

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
															'resta_dias'                               => $row['resta_dias'],
															);
			}
			// print_r($arrayConceptos);
			// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
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
				$nivel_formula = $row['nivel_formula'];
				// VALIDAR QUE EL CONCEPTO EXISTA EN EL ARRAY DE LOS CONCEPTOS
				if ($arrayConceptos[$nivel_formula][$id]['codigo']=='') { continue; }
				// REEMPLAZAR LOS VALORES DEL CONCEPTO CON LOS VALORES DE CADA GRUPO DE TRABAJO CONFIGURADO
				$arrayConceptos[$nivel_formula][$id]['formula']                                  = ($row['formula']=='')? $arrayConceptos[$nivel_formula][$id]['formula'] : $row['formula'];
				$arrayConceptos[$nivel_formula][$id]['formula_original']                         = ($row['formula']=='')? $arrayConceptos[$nivel_formula][$id]['formula_original'] : $row['formula'];
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
			}
			// print_r($arrayConceptos);
			//SI SE INSERTO EL EMPLEADO, INSERTAR LOS CONCEPTOS DEL EMPLEADO QUE ESTAN CON GARGA AUTOMATICA
			//CONSULTAR LOS CONCEPTOS RELACIONADOS AL CARGO DEL EMPLEADO (CONCEPTOS GENERALES)
			$sql   = "SELECT E.id_cargo,
						NCC.id,
						NCC.id_concepto,
						NCC.concepto,
						NCC.valor_concepto,
						NC.carga_automatica,
						NC.formula,
						NC.codigo,
						NC.nivel_formula
					FROM empleados AS E,
						nomina_conceptos_cargo AS NCC,
						nomina_conceptos AS NC
					WHERE E.id = $id_empleado
						AND NCC.id_cargo = E.id_cargo
						AND NC.id = NCC.id_concepto
						AND NC.carga_automatica = 'true'
					ORDER BY NC.nivel_formula ASC";
			$query = mysql_query($sql,$link);

			$valueInsert = '';
			while ($row=mysql_fetch_array($query)) {

				$id_concepto   = $row['id_concepto'];
				$nivel_formula = $row['nivel_formula'];
				// $arrayConceptosGenerales[$nivel_formula][$id_concepto] = array('valor_concepto'=>$row['valor_concepto']);

				// INSERTAR CONCEPTOS QUE NO TIENEN CALCULO CON FORMULA
				if ($arrayConceptos[$nivel_formula][$id_concepto]['formula']=='') {
					$arrayConceptos[$nivel_formula][$id_concepto]['valor_concepto'] = $row['valor_concepto'];
					$arrayConceptos[$nivel_formula][$id_concepto]['insert']         = 'true';
					$valueInsert .= "('$id_planilla',
										'$id_empleado',
										'$id_contrato',
										'$row[id_concepto]',
										'$row[codigo]',
										'$row[concepto]',
										'$row[valor_concepto]',
										'',
										'1',
										'$id_sucursal',
										'$id_empresa',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_colgaap']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_colgaap']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_niif']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_niif']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_niif']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['caracter']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['centro_costos']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_niif']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['caracter_contrapartida']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['centro_costos_contrapartida']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['naturaleza']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['imprimir_volante']."',
										'".$arrayConceptos[$nivel_formula][$id_concepto]['resta_dias']."'
									),";
				}

			}
			//CONSULTAR LOS CONCEPTOS PERSONALES DEL EMPLEADO
			$sql   = "SELECT E.id_cargo,
						NCC.id,
						NCC.id_concepto,
						NCC.concepto,
						NCC.valor_concepto,
						NC.carga_automatica,
						NC.formula,
						NC.codigo,
						NC.nivel_formula
					FROM empleados AS E,
						nomina_conceptos_empleados AS NCC,
						nomina_conceptos AS NC
					WHERE E.id = $id_empleado
						AND NCC.id_empleado = E.id
						AND NC.id = NCC.id_concepto
						AND NC.carga_automatica = 'true'
					ORDER BY NC.nivel_formula ASC";
			$query = mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {

				$id_concepto   = $row['id_concepto'];
				$nivel_formula = $row['nivel_formula'];
				// $arrayConceptosPersonales[$nivel_formula][$id_concepto] = array('valor_concepto'=>$row['valor_concepto']);

				// INSERTAR LOS CONCEPTOS QUE NO TIENEN FORMULA DE CALCULO
				if ($arrayConceptos[$nivel_formula][$id_concepto]['formula']=='') {
					$arrayConceptos[$nivel_formula][$id_concepto]['valor_concepto']=$row['valor_concepto'];
					$arrayConceptos[$nivel_formula][$id_concepto]['insert']='true';
					$valueInsert.="('$id_planilla',
									'$id_empleado',
									'$id_contrato',
									'$row[id_concepto]',
									'$row[codigo]',
									'$row[concepto]',
									'$row[valor_concepto]',
									'',
									'1',
									'$id_sucursal',
									'$id_empresa',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_colgaap']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_colgaap']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_niif']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_niif']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_niif']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['caracter']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['centro_costos']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['cuenta_contrapartida_niif']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['caracter_contrapartida']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['centro_costos_contrapartida']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['naturaleza']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['imprimir_volante']."',
									'".$arrayConceptos[$nivel_formula][$id_concepto]['resta_dias']."'
									),";
				}
			}

			$valueInsert = substr($valueInsert, 0, -1);
			echo$sql   = "INSERT INTO nomina_planillas_empleados_conceptos(
							id_planilla,
							id_empleado,
							id_contrato,
							id_concepto,
							codigo_concepto,
							concepto,
							valor_concepto,
							formula,
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
							resta_dias)
						VALUES $valueInsert";
			$query = mysql_query($sql,$link);

			//RECORRER EL ARRAY Y ARMAR EL INSERT
			$valueInsert = '';
			$nivel       = 0;

			// NIVEL DE LA FORMULA DEL CONCEPTO
			foreach ($arrayConceptos as $nivel_formula => $arrayConceptosArray) {
				//SI SE CAMBIO EL NIVEL, INSERTAR EN LA BASE DE DATOS LA CADENA
				if ($nivel!=$nivel_formula) {
    				if ($nivel!=0) {
    					$valueInsert = substr($valueInsert, 0, -1);
						if ($valueInsert!='') {
							echo$sql   = "INSERT INTO nomina_planillas_empleados_conceptos(
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
											resta_dias)
										VALUES $valueInsert";
							$query = mysql_query($sql,$link);
							if (!$query) { echo '<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>'; }
							else{ $valueInsert=''; }
						}
    				}
    				$nivel=$nivel_formula;
    			}

				// RECORRER LOS CONCEPTOS DE CADA NIVEL
				foreach ($arrayConceptosArray as $id_concepto => $arrayConceptosResul) {

					if ($arrayConceptosResul['insert']=='true') { continue; }			// SI YA SE INSERTO EL CONCEPTO, ENTONCES LO SALTAMOS

					// -----------------------------------//
					// REEMPLAZAR LAS VARIABLES GENERALES //
					// -----------------------------------//

					$arrayConceptosResul['formula']=str_replace('{SC}', $salario_basico, $arrayConceptosResul['formula']);			// SALARIO DEL CONTRATO
					$arrayConceptosResul['formula']=str_replace('{DL}', $dias_liquidacion, $arrayConceptosResul['formula']);			// DIAS LABORADOS
					$arrayConceptosResul['formula']=str_replace('{NRL}', $valor_nivel_riesgo_laboral, $arrayConceptosResul['formula']);			// NIVEL DE RIESGO LABORAL

					//SI EXISTE LA VARIABLE DEL CAMPO DE TEXTO
					$search_var_input=strpos($arrayConceptosResul['formula'], '{CT}');
					if ($search_var_input!==false) {
						$valueInsert.="('$id_planilla',
										'$id_empleado',
										'$id_contrato',
										'$id_concepto',
										'".$arrayConceptosResul['codigo']."',
										'".$arrayConceptosResul['concepto']."',
										'0',
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
										'".$arrayConceptosResul['naturaleza']."',
										'".$arrayConceptosResul['imprimir_volante']."',
										'".$arrayConceptosResul['resta_dias']."'
										),";
					}
					// SI LA FORMULA NO CONTIENE EL CAMPO DE TEXTO, ENTONCES SE BUSCA EN EL ARRAY O EN LA BASE DE DATOS LOS VALORES DE LOS CONCEPTOS DE LA FORMULA
					else{

						// NIVEL DE LA FORMULA DEL CONCEPTO
						foreach ($arrayConceptos as $nivel_formula_search => $arrayConceptosArray_search) {
							foreach ($arrayConceptosArray_search as $id_concepto_search => $arrayConceptosResul_search) {

								if($arrayConceptosResul_search['valor_concepto']<0 ){ continue; }			// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
								if($nivel_formula>$nivel_formula_search){ continue; }			// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR

								// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
								$arrayConceptosResul['formula']=str_replace('['.$arrayConceptosResul_search['codigo'].']', $arrayConceptosResul_search['valor_concepto'], $arrayConceptosResul['formula']);
								$arrayConceptosResul['formula']=str_replace('|>'.$arrayConceptosResul_search['codigo'].'<|', $arrayConceptosResul_search['valor_campo_texto'], $arrayConceptosResul['formula']);
							}
						}//->FIN FOR EACH PARA BUCAR LOS VALORES DE LOS CONCEPTOS DE LA FORMULA

						// --------------------------------------------------------------------------------------------------------------------------------//
						// SI DESPUES DE RECORRER EL ARRAY AUN EXISTEN VARIABLES PENDIENTES A REEMPLAZAR, ENTONCES BUSCAMOS EN LA BASE DE DATOS SI EXISTEN //
						// --------------------------------------------------------------------------------------------------------------------------------//

						//SI EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE NO ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA
						$search_var_concepto1=strpos($arrayConceptosResul['formula'],'[');
						$search_var_concepto2=strpos($arrayConceptosResul['formula'],'|>');
						if ($search_var_concepto1!==false || $search_var_concepto2!==false) {
							// CONSULTAMOS EN LA BASE DE DATOS LOS CONCEPTOS DE ESE EMPLEADO PARA BUSCAR LOS CONCEPTOS QUE FALTAN EN LA FORMULA
							$sql   = "SELECT id_concepto,codigo_concepto,concepto,valor_concepto,valor_campo_texto
										FROM nomina_planillas_empleados_conceptos
										WHERE activo=1
											AND id_planilla = '$id_planilla'
											AND id_empleado = '$id_empleado'
											AND id_contrato = '$id_contrato'
											AND id_empresa  = '$id_empresa'";
							$query = mysql_query($sql,$link);

							while ($row=mysql_fetch_array($query)) {

								if($row['valor_concepto']<0){ continue; }			// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
								if($nivel_formula>$nivel_formula_search){ continue; }			// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR

								// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
								$arrayConceptosResul['formula']=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $arrayConceptosResul['formula']);
								$arrayConceptosResul['formula']=str_replace('|>'.$row['codigo_concepto'].'<|', $row['valor_campo_texto'], $arrayConceptosResul['formula']);
							}

							$arrayConceptosResul['formula']=reemplazarValoresFaltantes($arrayConceptosResul['formula']);

							//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
							$search_var_concepto1=strpos($arrayConceptosResul['formula'], '[');
							$search_var_concepto2=strpos($arrayConceptosResul['formula'], '|>');
							if ($search_var_concepto1===false || $search_var_concepto2===false) {
								// CALCULAR LA FORMULA
								$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
								// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
								if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
									echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: ('.$arrayConceptosResul['formula'].')");</script>';
									continue;
								}

								$valueInsert.="('$id_planilla',
												'$id_empleado',
												'$id_contrato',
												'$id_concepto',
												'".$arrayConceptosResul['codigo']."',
												'".$arrayConceptosResul['concepto']."',
												'$valor_concepto',
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
												'".$arrayConceptosResul['naturaleza']."',
												'".$arrayConceptosResul['imprimir_volante']."',
												'".$arrayConceptosResul['resta_dias']."'
												),";
							}
							// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES NO SE GUARDA EL CONCEPTO
						}
						// ----------------------------------------------------------------------------------------------------------------------//
						// SI DESPUES DE RECORRER EL ARRAY NO EXISTEN VARIABLES PENDIENTES A REEMPLAZAR, ENTONCES GENERAMOS LA CADENA DE INSERT  //
						// ----------------------------------------------------------------------------------------------------------------------//
						else{
							// CALCULAR LA FORMULA
							$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
							// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
							if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
								echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: ('.$arrayConceptosResul['formula'].')");</script>';
								continue;
							}

							$valueInsert.="('$id_planilla',
											'$id_empleado',
											'$id_contrato',
											'$id_concepto',
											'".$arrayConceptosResul['codigo']."',
											'".$arrayConceptosResul['concepto']."',
											'$valor_concepto',
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
											'".$arrayConceptosResul['naturaleza']."',
											'".$arrayConceptosResul['imprimir_volante']."',
											'".$arrayConceptosResul['resta_dias']."'
											),";
						}
					}
				}
			}

			//SI SE CAMBIO EL NIVEL INSERTAR EN LA BASE DE DATOS LA CADENA
			if ($nivel!=0) {
				$valueInsert = substr($valueInsert, 0, -1);
				if ($valueInsert!='') {
					echo$sql   = "INSERT INTO nomina_planillas_empleados_conceptos(
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
									resta_dias)
								VALUES $valueInsert";
					$query = mysql_query($sql,$link);
					if (!$query) { echo '<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>'; }
					else{ $valueInsert = ''; }
				}
			}

			// CARGAR PRESTAMOS DEL EMPLEADO
			cargaPrestamosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_sucursal,$id_empresa,$link);

			echo '<script>
					//AGREGAR EL EMPLEADO A LA PLANILLA DE NOMINA
					var div=document.createElement("div");
					div.setAttribute("class","bodyDivNominaPlanilla");
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
				</script>';
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

	function verificaEliminaEmpleado($cont,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){
		// CONSULTAR SI LOS CONCEPTOS QUE TIENE EL EMPLEADO ESTAN RELACIONADOS EN UNA PLANILLA DE LIQUIDACION
		$sql = "SELECT id_planilla_liquidacion FROM nomina_planillas_empleados_conceptos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla AND id_contrato=$id_contrato";
		$query = mysql_query($sql,$link);
		$id_planilla_liquidacion=0;
		while ($row=mysql_fetch_array($query)) {
			if ($row['id_planilla_liquidacion']>0) {
				$id_planilla_liquidacion++;
			}
		}

		if ($id_planilla_liquidacion>0) {
			$sql="SELECT consecutivo FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla_liquidacion";
			$query=mysql_query($sql,$link);
			$consecutivo=mysql_result($query,0,'consecutivo');
			echo '<script>
					if (confirm("Cuidado!\nEste empleado se encuentra relacionado en la planilla de liquidacion N. '.$consecutivo.'\nSi lo elimina, se perdera la referencia de la informacion en la planilla ")) {
						# code...
					}
				</script>';
		}
	}

	function eliminarEmpleado($cont,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){

		//ELIMINAR LOS CONCEPTOS DEL EMPLEADO
		$sql   = "DELETE FROM nomina_planillas_empleados_conceptos
					WHERE activo=1
						AND id_planilla='$id_planilla'
						AND id_empleado='$id_empleado'
						AND id_contrato='$id_contrato'
						AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);
		if ($query) {
			//ELIMINAR EL EMPLEADO DE LA PLANILLA
			$sql   = "DELETE FROM nomina_planillas_empleados
						WHERE activo=1
							AND id_planilla = '$id_planilla'
							AND id_empleado = '$id_empleado'
							AND id_contrato = '$id_contrato'
							AND id_empresa = '$id_empresa'";
			$query = mysql_query($sql,$link);
			if ($query) {
				//ELIMINAR EL NODO DEL DOM
				echo'<script>
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
						// console.log(document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode);
						// console.log("document.getElementById(\"divLoadEmpleado_'.$id_contrato.'\").parentNode.removeChild(document.getElementById(\"divLoadEmpleado_'.$id_contrato.'\"))");
						document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode.parentNode.removeChild(document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode);
						calcularValoresPlanilla();
					</script>';
			}
			else{
				echo'<script>
						alert("Erro\nSe eliminaron los conceptos pero no el empleado, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					</script>'.$cont;
			}
		}
		else{
			echo'<script>
					alert("Erro\nNo se eliminaron los conceptos del empleado, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
				</script>'.$cont;
		}
	}

	function cargarTodosEmpleados($id_planilla,$id_empresa,$sucursal,$link){
		$fecha = date("Y-m-d");

		//CONSULTAR LOS DIAS DE LIQUIDACION DE LA PLANILLA
		$sql   = "SELECT dias_liquidacion,fecha_inicio,fecha_final  FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query = mysql_query($sql,$link);

		$dias_liquidacion = mysql_result($query,0,'dias_liquidacion');
		$fecha_final      = mysql_result($query,0,'fecha_final');

		$bodyEmpleados = '';
		//SELECCIONAR LOS EMPLEADOS QUE ESTAN EN LA PLANILLA
		$sql   = "SELECT id_contrato,id_empleado,nombre_empleado,documento_empleado FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query = mysql_query($sql,$link);

		$whereIdEmpleados         ='';
		$whereIdEmpleadosSucursal ='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdEmpleados.=' AND id_empleado<>'.$row['id_empleado'].' ';
			$whereIdEmpleadosSucursal.=' AND id<>'.$row['id_empleado'].' ';
		}

		// CONSULTAR LA SUCURSAL DEL EMPLEADO
		$sqlEmpleadoSucursal   = "SELECT id,id_sucursal FROM empleados WHERE activo=1 AND id_empresa=$id_empresa  $whereIdEmpleadosSucursal ";
		$queryEmpleadoSucursal = mysql_query($sqlEmpleadoSucursal,$link);

		while ($row=mysql_fetch_array($queryEmpleadoSucursal)) {
			$arraySucursalEmpleado[$row['id']]=$row['id_sucursal'];
		}

		//CONSULTAMOS LOS DATOS DEL CONTRATO PARA INSERTALOS EN LA TABLA DE EMPLEADOS DE LA PLANILLA
		$sql   = "SELECT id,id_empleado,tipo_documento_empleado,documento_empleado,nombre_empleado,numero_contrato,salario_basico,fecha_inicio_nomina,id_grupo_trabajo,valor_nivel_riesgo_laboral,IF(fecha_fin_contrato <= '$fecha', 'Si', 'No') AS terminar_contrato,id_sucursal
					FROM empleados_contratos
					WHERE activo=1
						/*AND (estado=0 OR estado=2)*/
						AND estado=0
						AND id_empresa=$id_empresa
						AND id_sucursal=$sucursal
						AND fecha_inicio_nomina<='$fecha_final'
						AND nombre_empleado IS NOT NULL
						$whereIdEmpleados";

		$query = mysql_query($sql,$link);

		$valueInsertEmpleados  = '';
		$whereInsertEmpleados  = '';
		$whereDeleteEmpleados  = '';
		$whereId_grupo_trabajo = '';

		while ($row=mysql_fetch_array($query)) {
			$whereInsertEmpleados  .= ($whereInsertEmpleados=='')? ' E.id='.$row['id_empleado'] : ' OR E.id='.$row['id_empleado'] ;
			$whereDeleteEmpleados  .= ($whereDeleteEmpleados=='')? ' id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
			$whereId_grupo_trabajo .= ($whereId_grupo_trabajo=='')? ' id_grupo_trabajo='.$row['id_grupo_trabajo'] : ' OR id_grupo_trabajo='.$row['id_grupo_trabajo'] ;

			$arrayEmpleados[$row['id_empleado']]         = $row['id_grupo_trabajo'];
			$arrayEmpleadosValores[$row['id_empleado']]  = array('salario_basico' => $row['salario_basico'],
																	'id_contrato'                => $row['id'],
																	'id_sucursal'                => $row['id_sucursal'],
																	'valor_nivel_riesgo_laboral' => $row['valor_nivel_riesgo_laboral']);

			$valueInsertEmpleados.="('$id_planilla',
										'$row[id_empleado]',
										'$row[tipo_documento_empleado]',
										'$row[documento_empleado]',
										'$row[nombre_empleado]',
										'$row[id]',
										'$dias_liquidacion',
										'$dias_liquidacion',
										'No',
										'".$arraySucursalEmpleado[$row['id_empleado']]."',
										'$id_empresa'
									),";
		}
		$valueInsertEmpleados = substr($valueInsertEmpleados, 0, -1);
		// VALIDAR SI HAY EMPLEADOS A INSERTAR
		if ($valueInsertEmpleados=='') {
			echo'<script>
					alert("Aviso!\nNo hay empleados ha cargar");
					Win_Ventana_buscar_empleados.close();
					document.getElementById("contenedorPrincipalConceptos").innerHTML="";
				</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);
			return;
		}
		//INSERTAR EL EMPLEADO EN LA PLANILLA
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
		$query = mysql_query($sql,$link);


		if ($query) {

			//CONSULTAR TODOS LOS CONCEPTOS CON CARGA AUTOMATICA DE LA BASE DE DATOS
			$sql   = "SELECT id,
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
							AND id_empresa=$id_empresa
							AND carga_automatica='true'
						ORDER BY nivel_formula ASC";
			$query = mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)){

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

			// CONSULTAR SI EL GRUPO DE TRABAJO DEL EMPLEADO TIENE OTRAS CUENTAS CONFIGURADAS PARA REEEMPLAZARLAS DEL ARRAY INICIAL
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
							AND id_empresa=$id_empresa
							AND ($whereId_grupo_trabajo)";
			$query = mysql_query($sql,$link);
			while ($row=mysql_fetch_array($query)) {
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

			// RECORER EL ARRAY PARA ORGANIZAR LOS CONCEPTOS DE CADA EMPLEADO
			foreach ($arrayEmpleados as $id_empleado => $id_grupo_trabajo) {
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

			//SI SE INSERTO EL EMPLEADO, INSERTAR LOS CONCEPTOS DEL EMPLEADO QUE ESTAN CON GARGA AUTOMATICA
			//CONSULTAR LOS CONCEPTOS RELACIONADOS AL CARGO DEL EMPLEADO (CONCEPTOS GENERALES)
			$sql   = "SELECT E.id AS id_empleado,
							E.id_cargo,
							EC.id AS id_contrato,
							NCC.id,
							NCC.id_concepto,
							NCC.concepto,
							NCC.valor_concepto,
							NC.carga_automatica,
							NC.formula,
							NC.codigo,
							NC.nivel_formula
						FROM empleados AS E,
							nomina_conceptos_cargo AS NCC,
							nomina_conceptos AS NC,
							empleados_contratos AS EC
						WHERE ($whereInsertEmpleados)
							AND NCC.id_cargo = E.id_cargo
							AND NC.id = NCC.id_concepto
							AND NC.carga_automatica = 'true'
							AND EC.id_empleado=E.id
							AND (EC.estado=0 OR EC.estado=2)
						ORDER BY NC.nivel_formula ASC";
			$query = mysql_query($sql,$link);

			$valueInsert = '';
			while ($row=mysql_fetch_array($query)) {
				$id_empleado   = $row['id_empleado'];
				$id_concepto   = $row['id_concepto'];
				$nivel_formula = $row['nivel_formula'];

				// INSERTAR CONCEPTOS QUE NO TIENEN CALCULO CON FORMULA
				if ($arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['formula']=='') {
					$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['valor_concepto'] = $row['valor_concepto'];
					$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['insert']         = 'true';
					$valueInsert .= "('$id_planilla',
										'$id_empleado',
										'$row[id_contrato]',
										'$row[id_concepto]',
										'$row[codigo]',
										'$row[concepto]',
										'$row[valor_concepto]',
										'',
										'1',
										'$id_empresa',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['caracter']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['centro_costos']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_contrapartida_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['caracter_contrapartida']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['centro_costos_contrapartida']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['naturaleza']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['imprimir_volante']."'
									),";
				}
			}

			//CONSULTAR LOS CONCEPTOS PERSONALES DEL EMPLEADO
			$sql   = "SELECT
							E.id AS id_empleado,
							E.id_cargo,
							NCC.id,
							NCC.id_contrato,
							NCC.id_concepto,
							NCC.concepto,
							NCC.valor_concepto,
							NC.carga_automatica,
							NC.formula,
							NC.codigo,
							NC.nivel_formula
						FROM empleados AS E,
							nomina_conceptos_empleados AS NCC,
							nomina_conceptos AS NC
						WHERE ($whereInsertEmpleados)
							AND NCC.id_empleado = E.id
							AND NC.id = NCC.id_concepto
							AND NC.carga_automatica = 'true'
						ORDER BY NC.nivel_formula ASC";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$id_empleado   = $row['id_empleado'];
				$id_concepto   = $row['id_concepto'];
				$nivel_formula = $row['nivel_formula'];

				// INSERTAR CONCEPTOS QUE NO TIENEN CALCULO CON FORMULA
				if ($arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['formula']=='') {
					$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['valor_concepto'] = $row['valor_concepto'];
					$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['insert']         = 'true';
					$valueInsert.="('$id_planilla',
										'$id_empleado',
										'$row[id_contrato]',
										'$row[id_concepto]',
										'$row[codigo]',
										'$row[concepto]',
										'$row[valor_concepto]',
										'',
										'1',
										'$id_empresa',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['caracter']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['centro_costos']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_contrapartida_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_colgaap']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['id_cuenta_contrapartida_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['cuenta_contrapartida_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['descripcion_cuenta_contrapartida_niif']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['caracter_contrapartida']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['centro_costos_contrapartida']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['naturaleza']."',
										'".$arrayEmpleadosConceptos[$id_empleado][$nivel_formula][$id_concepto]['imprimir_volante']."'
									),";
				}
			}

			$valueInsert = substr($valueInsert, 0, -1);
			$sql   = "INSERT INTO nomina_planillas_empleados_conceptos(
							id_planilla,
							id_empleado,
							id_contrato,
							id_concepto,
							codigo_concepto,
							concepto,
							valor_concepto,
							formula,
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
						VALUES $valueInsert";
			$query = mysql_query($sql,$link);

			/////////////////////////////////////////////////
			// RECORRER EL ARRAY PARA CALCULAR LAs FORMULA //
			/////////////////////////////////////////////////
			$valueInsert = '';
			$nivel       = 0;
			$id_tercero  = 0;

			// PRIMER NIVEL CORRESPONDIENTE AL ID DEL EMPLEADO
			foreach ($arrayEmpleadosConceptos as $id_empleado => $arrayEmpleadosConceptosArray) {
				// INSERTAR LOS PRESTAMOS DEL EMPLEADO
				cargaPrestamosEmpleado($id_planilla,$id_empleado,$arrayEmpleadosValores[$id_empleado]['id_contrato'],$arrayEmpleadosValores[$id_empleado]['id_sucursal'],$id_empresa,$link);

				//SI SE CAMBIO EL NIVEL, INSERTAR EN LA BASE DE DATOS LA CADENA
					if ($id_tercero!=$id_empleado) {
	    				if ($id_tercero!=0) {
	    					$valueInsert = substr($valueInsert, 0, -1);
							if ($valueInsert!='') {
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
											VALUES $valueInsert";
								$query = mysql_query($sql,$link);

								if (!$query) { echo '<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>'; }
								else{ $valueInsert=''; }
							}
	    				}
	    				$id_tercero=$id_empleado;
	    			}

				// NIVEL DE LA FORMULA DEL CONCEPTO
				foreach ($arrayEmpleadosConceptosArray as $nivel_formula => $arrayConceptosArray) {
					//SI SE CAMBIO EL NIVEL, INSERTAR EN LA BASE DE DATOS LA CADENA
					if ($nivel!=$nivel_formula) {
	    				if ($nivel!=0) {
	    					$valueInsert = substr($valueInsert, 0, -1);
							if ($valueInsert!='') {

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
									VALUES $valueInsert";
								$query = mysql_query($sql,$link);

								if (!$query) { echo '<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>'; }
								else{ $valueInsert = ''; }
							}
	    				}
	    				$nivel=$nivel_formula;
	    			}

					// RECORRER LOS CONCEPTOS DE CADA NIVEL
					foreach ($arrayConceptosArray as $id_concepto => $arrayConceptosResul) {

						if ($arrayConceptosResul['insert']=='true') { continue; }			// SI YA SE INSERTO EL CONCEPTO, ENTONCES LO SALTAMOS

						// -----------------------------------//
						// REEMPLAZAR LAS VARIABLES GENERALES //
						// -----------------------------------//

						// SALARIO DEL CONTRATO
						$arrayConceptosResul['formula']=str_replace('{SC}', $arrayEmpleadosValores[$id_empleado]['salario_basico'], $arrayConceptosResul['formula']);
						// DIAS LABORADOS
						$arrayConceptosResul['formula']=str_replace('{DL}', $dias_liquidacion, $arrayConceptosResul['formula']);
						// PORCENTAJE DE NIVEL DE RIESGO LABORAL
						$arrayConceptosResul['formula']=str_replace('{NRL}', $arrayEmpleadosValores[$id_empleado]['valor_nivel_riesgo_laboral'], $arrayConceptosResul['formula']);

						//SI EXISTE LA VARIABLE DEL CAMPO DE TEXTO
						$search_var_input=strpos($arrayConceptosResul['formula'], '{CT}');
						if ($search_var_input!==false) {
							$valueInsert .= "('$id_planilla',
												'$id_empleado',
												'".$arrayEmpleadosValores[$id_empleado]['id_contrato']."',
												'$id_concepto',
												'".$arrayConceptosResul['codigo']."',
												'".$arrayConceptosResul['concepto']."',
												'0',
												'".$arrayConceptosResul['formula']."',
												'".$arrayConceptosResul['formula_original']."',
												'".$nivel_formula."',
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
												'".$arrayConceptosResul['naturaleza']."',
												'".$arrayConceptosResul['imprimir_volante']."'
											),";
						}
						// SI LA FORMULA NO CONTIENE EL CAMPO DE TEXTO, ENTONCES SE BUSCA EN EL ARRAY O EN LA BASE DE DATOS LOS VALORES DE LOS CONCEPTOS DE LA FORMULA
						else{
							// RECORRER EL MISMO ARRAY PARA CONSULTAR SI LOS CONCEPTOS DE LA FORMULA ESTAN EN EL
							// NIVEL DE LA FORMULA DEL CONCEPTO
							foreach ($arrayEmpleadosConceptos[$id_empleado] as $nivel_formula_search => $arrayConceptosArray_search) {
								foreach ($arrayConceptosArray_search as $id_concepto_search => $arrayConceptosResul_search) {
									// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
									if($arrayConceptosResul_search['valor_concepto']<0 ){ continue; }
									// VALIDAR QUE EL NIVEL DE LOS CONCEPTOS BUSCADOS SEA MENOR
									if($nivel_formula>$nivel_formula_search){ continue; }
									// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
									$arrayConceptosResul['formula']=str_replace('['.$arrayConceptosResul_search['codigo'].']', $arrayConceptosResul_search['valor_concepto'], $arrayConceptosResul['formula']);
									$arrayConceptosResul['formula']=str_replace('|>'.$arrayConceptosResul_search['codigo'].'<|', $arrayConceptosResul_search['valor_campo_texto'], $arrayConceptosResul['formula']);
								}
							}//->FIN FOR EACH PARA BUCAR LOS VALORES DE LOS CONCEPTOS DE LA FORMULA

							// --------------------------------------------------------------------------------------------------------------------------------//
							// SI DESPUES DE RECORRER EL ARRAY AUN EXISTEN VARIABLES PENDIENTES A REEMPLAZAR, ENTONCES BUSCAMOS EN LA BASE DE DATOS SI EXISTEN //
							// --------------------------------------------------------------------------------------------------------------------------------//

							//SI EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE NO ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA
							$search_var_concepto1=strpos($arrayConceptosResul['formula'],'[');
							$search_var_concepto2=strpos($arrayConceptosResul['formula'],'|>');
							if ($search_var_concepto1!==false || $search_var_concepto2!==false) {
								// $tiempo_inicio = microtime_float();
								// CONSULTAMOS EN LA BASE DE DATOS LOS CONCEPTOS DE ESE EMPLEADO PARA BUSCAR LOS CONCEPTOS QUE FALTAN EN LA FORMULA
								$sql   = "SELECT id_concepto,codigo_concepto,concepto,valor_concepto,naturaleza
											FROM nomina_planillas_empleados_conceptos
											WHERE activo=1
												AND id_empresa  = '$id_empresa'
												AND id_planilla = '$id_planilla'
												AND id_empleado = '$id_empleado'
												AND id_contrato = '".$arrayEmpleadosValores[$id_empleado]['id_contrato']."'";
								$query = mysql_query($sql,$link);
			// 					$tiempo_fin = microtime_float();
			// $tiempo = $tiempo_fin - $tiempo_inicio;
 		// echo "<script>console.log('Tiempo empleado: ".($tiempo_fin - $tiempo_inicio)." ');</script>  ";
								while ($row=mysql_fetch_array($query)) {
									// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE
									if($row['valor_concepto']<0){ continue; }
									// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
									$arrayConceptosResul['formula']=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $arrayConceptosResul['formula']);
									$arrayConceptosResul['formula']=str_replace('|>'.$row['codigo_concepto'].'<|', $row['valor_campo_texto'], $arrayConceptosResul['formula']);
								}

								$arrayConceptosResul['formula']=reemplazarValoresFaltantes($arrayConceptosResul['formula']);

								//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
								$search_var_concepto1=strpos($arrayConceptosResul['formula'], '[');
								$search_var_concepto2=strpos($arrayConceptosResul['formula'], '|>');
								if ($search_var_concepto1===false && $search_var_concepto2===false) {
									// CALCULAR LA FORMULA
									$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
									// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
									if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
										echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: '.$arrayConceptosResul['formula'].'");</script>';
										continue;
									}

									$valueInsert .= "('$id_planilla',
														'$id_empleado',
														'".$arrayEmpleadosValores[$id_empleado]['id_contrato']."',
														'$id_concepto',
														'".$arrayConceptosResul['codigo']."',
														'".$arrayConceptosResul['concepto']."',
														'$valor_concepto',
														'".$arrayConceptosResul['formula']."',
														'".$arrayConceptosResul['formula_original']."',
														'".$nivel_formula."',
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
														'".$arrayConceptosResul['naturaleza']."',
														'".$arrayConceptosResul['imprimir_volante']."'
													),";
								}
								// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES NO SE GUARDA EL CONCEPTO
							}

							// ----------------------------------------------------------------------------------------------------------------------//
							// SI DESPUES DE RECORRER EL ARRAY NO EXISTEN VARIABLES PENDIENTES A REEMPLAZAR, ENTONCES GENERAMOS LA CADENA DE INSERT  //
							// ----------------------------------------------------------------------------------------------------------------------//
							else{
								// CALCULAR LA FORMULA
								$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
								// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
								if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
									echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: '.$arrayConceptosResul['formula'].'");</script>';
									continue;
								}

								$valueInsert .= "('$id_planilla',
													'$id_empleado',
													'".$arrayEmpleadosValores[$id_empleado]['id_contrato']."',
													'$id_concepto',
													'".$arrayConceptosResul['codigo']."',
													'".$arrayConceptosResul['concepto']."',
													'$valor_concepto',
													'".$arrayConceptosResul['formula']."',
													'".$arrayConceptosResul['formula_original']."',
													'".$nivel_formula."',
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
													'".$arrayConceptosResul['naturaleza']."',
													'".$arrayConceptosResul['imprimir_volante']."'
												),";
							}
						}
					}//FOR EACH DE CADA CONCEPTO
				}//FOR EACH DE CADA NIVEL DE LAS FORMULAS

			}//FOR EACH DE ID EMPLEADO



			//SI SE CAMBIO EL NIVEL, INSERTAR EN LA BASE DE DATOS LA CADENA
			// if ($nivel!=$nivel_formula) {
			if ($nivel!=0) {
				$valueInsert = substr($valueInsert, 0, -1);
				if ($valueInsert!='') {
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
								VALUES $valueInsert";
					$query = mysql_query($sql,$link);
					if (!$query) { echo'<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>'; }
					else{ $valueInsert = ''; }
				}
			}

			echo'<script>
						Win_Ventana_buscar_empleados.close();
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
						calcularValoresPlanilla();
					</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);

		}
		else{
			echo'<script>
						alert("Error\nNo se agregaron los empleados a la planilla, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
						Win_Ventana_buscar_empleados.close();
						document.getElementById("contenedorPrincipalConceptos").innerHTML="";
				</script>'.renderizarEmpleados($id_planilla,$id_empresa,$link);
		}
	}

	function renderizarEmpleados($id_planilla,$id_empresa,$link){
		$bodyEmpleados = '';

		//SELECCIONAR LOS EMPLEADOS QUE ESTAN EN LA PLANILLA
		$sql   = "SELECT id_contrato,id_empleado,nombre_empleado,documento_empleado,verificado FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query = mysql_query($sql,$link);

		$whereIdEmpleados = '';
		while ($row=mysql_fetch_array($query)) {
			$whereIdEmpleados.=' AND id_empleado<>'.$row['id_empleado'].' ';
			$bodyEmpleados.='<script>
							 	//AGREGAR EL EMPLEADO A LA PLANILLA DE NOMINA
							 	var div=document.createElement("div");
							 	div.setAttribute("class","bodyDivNominaPlanilla");
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
		// $fecha = date("Y-m-d");

		$sql="SELECT fecha_inicio,fecha_final FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$fecha_inicio = mysql_result($query,0,'fecha_inicio');
		$fecha_final  = mysql_result($query,0,'fecha_final');

		$sql   ="SELECT id,id_concepto,valor_cuota,id_centro_costos
					FROM nomina_prestamos_empleados
					WHERE activo=1
						AND id_empresa=$id_empresa
						AND id_empleado=$id_empleado
						AND valor_prestamo_restante>0
						AND fecha_inicio_pago BETWEEN '$fecha_inicio' AND '$fecha_final' ";
		$query = mysql_query($sql,$link);

		while ($row=mysql_fetch_array($query)) {
			$whereIdConceptos.=' AND id='.$row['id_concepto'];
			$arrayPrestamos[$row['id']][$row['id_concepto']]=array('id_prestamo'=>$row['id'],
																	'id_concepto'=>$row['id_concepto'],
																	'valor_cuota'=>$row['valor_cuota'],
																	'id_centro_costos'=>$row['id_centro_costos']);
		}

		// SI TIENE PRESTAMOS
		if ($whereIdConceptos!='') {
			// CONSULTAR LOS CONCEPTOS DE LOS PRESTAMOS
			$sql   = "SELECT id,
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
							AND id_empresa=$id_empresa $whereIdConceptos
						ORDER BY nivel_formula ASC";
			$query = mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {
				$id = $row['id'];
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
			$sql   = "SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id=$id_contrato";
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
					$valueInsert .= "('$id_planilla',
										'$id_empleado',
										'$id_contrato',
										'$id_concepto',
										'".$arrayConceptos[$id_concepto]['codigo']."',
										'".$arrayConceptos[$id_concepto]['concepto']."',
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
				$query = mysql_query($sql,$link);

				if (!$query) { echo '<script>alert("Se produjo un error y no se insertaron los conceptos con formulas");</script>'; }
				else{ $valueInsert = ''; }
			}
		}
	}

	function cargarConceptosEmpleado($id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){
		//CONSULTAR EL ESTADO DE LA PLANILLA, SI ESTA GENERADA MOSTRAR SOLO COMO INFORMACION
		$sql    = "SELECT estado FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query  = mysql_query($sql,$link);
		$estado = mysql_result($query,0,'estado');

		$readonly = ($estado==1 || $estado==3 || user_permisos(169)=='false' || $estado==2)? 'readonly' : '' ;

		//CONSULTAR LOS DIAS LABORADOS Y SI ES FINALIZACION DEL CONTRATO DEL EMPLEADO
		$sql   = "SELECT id,dias_laborados,dias_laborados_empleado,terminar_contrato,observaciones,nombre_empleado,recalcular_concepto
					FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado='$id_empleado' AND id_contrato='$id_contrato'";
		$query = mysql_query($sql,$link);

		$id_registro             = mysql_result($query,0,'id');
		$dias_laborados          = mysql_result($query,0,'dias_laborados');
		$dias_laborados_empleado = mysql_result($query,0,'dias_laborados_empleado');
		$terminar_contrato       = mysql_result($query,0,'terminar_contrato');
		$observaciones           = mysql_result($query,0,'observaciones');
		$nombre_empleado         = mysql_result($query,0,'nombre_empleado');
		$recalcular_concepto     = mysql_result($query,0,'recalcular_concepto');

		//CONSULTAR LOS CONCEPTOS DEL EMPLEADO DE LA PLANILLA
		$sql   = "SELECT
							NC.*, C.clasificacion
						FROM
							nomina_planillas_empleados_conceptos AS NC
						INNER JOIN nomina_conceptos AS C ON C.id = NC.id_concepto
						WHERE
							NC.activo = 1
						AND NC.id_contrato = '$id_contrato' 
						AND NC.id_empleado = '$id_empleado' 
						AND NC.id_planilla = '$id_planilla' 
						AND NC.id_empresa =  '$id_empresa'
						ORDER BY
							NC.naturaleza ASC";
		$query = mysql_query($sql,$link);

		$select_contrato=($estado==1 || $estado==3 || $estado==2)? '<input type="text" readonly value="'.$terminar_contrato.'">' : '<select id="terminar_contrato" onchange="updateFinalizaContrato(this.value,\''.$id_empleado.'\',\''.$id_contrato.'\')">
                	        																							    <option value="No">No</option>
                	        																							    <option value="Si">Si</option>
                	        																							</select>' ;
		$evento_recalcular = ($estado==1 || $estado==3 || $estado==2)? '' : 'onclick="actualizaRecalcularFormula('.$id_empleado.','.$id_contrato.',\''.$recalcular_concepto.'\')"' ;

		$observacion_empleado=($estado==1 || $estado==3 || $estado==2)? $observaciones : '<textarea id="observacionEmpleado" style="width: 100%;height: 100%;padding:0px;" onkeydown="inputObservacionEmpleadoNominaPlanilla(event,this,'.$id_registro.')">'.$observaciones.'</textarea>' ;

		$options_dias_laborados = ($estado==1 || $estado==3 || $estado==2)? 'readonly' : 'onkeyup="updateDiasLaborados(event,this,\''.$id_empleado.'\',\''.$id_contrato.'\')"' ;
		$script=($estado==1 || $estado==3 || $estado==2)? '' : 'document.getElementById("terminar_contrato").value="'.$terminar_contrato.'";' ;

		$bodyConceptos = '<script>
							//LIMPIAR VARIABLES DE CALCULOS
							totalDevengoEmpleado     = 0
							totalDeduccionEmpleado   = 0
							totalApropiacionEmpleado = 0
							totalProvisionEmpleado   = 0
							totalNetoPagarEmpleado   = 0
						</script>

						<div style="width:100%; height:35px;text-transform: uppercase;font-weight:bold;font-size:18px;color:#999;text-indent: 10px;line-height:1.5;">
							'.$nombre_empleado .'
						</div>

						<div style="float:left;">

							<div class="renglonTop" style="margin-left:10px;float:none;width: 200px;min-height:0px;">
	                    	    <div class="labelTop" style="width:calc(60% - 1px);float:left;height:20px;border-bottom:0px;border-right:1px solid #d4d4d4;text-align:left;text-indent:7px;">
	                    	    	Dias Liquidados
	                    	    	<div id="divLoadDiasLaborados" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div>
	                    	    </div>
	                    	    <div class="campoTop" style="width:40%;">
	                    	    	<input type="text" id="dias_laborados" value="'.$dias_laborados.'" '.$options_dias_laborados.' />
	                    	    </div>
	                    	</div>

	                    	<div class="renglonTop" style="margin-left:10px;float:none;width: 200px;margin-top:0px;border-top:none;min-height:0px;" >
	                    	    <div class="labelTop" style="width:calc(60% - 1px);float:left;height:20px;border-bottom:0px;border-right:1px solid #d4d4d4;text-align:left;text-indent:7px;">
	                    	    	Dias Laborados
	                    	    </div>
	                    	    <div id="dias_laborados_empleado" class="campoTop" style="width:40%;text-indent: 5px;line-height: 20px;font-size: 13px;">
	                    	    	'.$dias_laborados_empleado.'
	                    	    </div>
	                    	</div>
                    	</div>

                    	<div class="renglonTop" style="float:left;width:calc(100% - 200px - 10px - 19px);">
                    		<div id="label_observacion" class="labelTop" style="width:100px;height:43px !important;float:left;height:20px;border-right:1px solid #d4d4d4;text-align:left;text-indent:7px;border-bottom:none;line-height: 2.5;">
                    	    	<span>Observaciones</span>
                    	    </div>
                    	     <div id="dias_laborados_empleado" class="campoTop" style="width:calc(100% - 100px - 4px);height:44px; text-indent: 5px;line-height: 20px;font-size: 13px;">
	                    	    	'.$observacion_empleado.'
                    	    </div>
                    	</div>


                    	<div class="renglonTop" style="margin-left:10px;float:none;width: 200px;margin-top:0px;border-top:none;min-height:0px;display:none;">
                    	    <div class="labelTop" style="width:calc(60% - 1px);float:left;height:20px;border-bottom:0px;border-right:1px solid #d4d4d4;text-align:left;text-indent:7px;">
                    	    	Finalizar Contrato
                    	    	<div id="divLoadFinalizaContrato" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div>
                    	    </div>
                    	    <div class="campoTop" style="width:40%;">
                    	    	'.$select_contrato.'
                    	    </div>
                    	</div>
                    	<div class="headConceptos" >
	                    	<div class="bodyDivNominaPlanilla" style="border-bottom:none;" id="headConceptos">
	                            <div class="campo" style="overflow:hidden;" id="divLoadHead"></div>

	                            <div class="campoHeadConceptos" style="width:calc(100% - 50% - 70px - 18px );padding-top:0px;height:100%;" >
	                            	Concepto
	                            	<div title="Recalcular Conceptos con sus formulas" style="float:right;margin-right:3px;padding-top:1px;border-left: 1px solid #d4d4d4;width:38px;overflow:hidden;" id="divContentFormula">
	                            		<img src="img/funcion.png"> <img src="img/checkbox_'.$recalcular_concepto.'.png"  style="width:18px;height:18px;" '.$evento_recalcular.' >
                            		</div>
                        		</div>

	                            <div class="campoHeadConceptos" style="width:50px;">Valor</div>
	                            <div class="campoHeadConceptos" style="width:calc(100% - 50% - 70px - 18px - 51px);">Total </div>
	                            <div class="campoHeadConceptos" style="width:30px;text-align:center;" title="Naturaleza del Concepto">Nat.</div>
	                            <div class="campoHeadConceptos" style="width:30px;text-align:center;text-indent:0px;" title="Imprimible"><img src="img/printer.png"></div>
	                        </div>
                        </div>
                        <div class="contenedorConceptos" id="contenedorConceptos" onscroll="resizeHeadMyGrilla(this,\'headConceptos\')">';

        $cont = 1;
		while ($row=mysql_fetch_array($query)) {

			$divStyleFunction = 'display:none;';
			$divImgFunction   = 'funcion';
			$divTitleFunction = 'Concepto calculado con formula';
			$btnNEData = "";

			//Configuracion datos de concepto para nomina electronica
			$configTypes = array("hora_extra_diurna",
									"hora_extra_nocturna",
									"hora_recargo_nocturno",
									"hora_recargo_diario_dominicales_y_festivas",
									"hora_extra_nocturna_dominicales_y_festivas",
									"hora_recargo_nocturno_dominicales_y_festivas",
									"incapacidad",
									"licencia_maternidad_paternidad",
									"licencia_remunerada",
									"licencia_no_remunerada",
									"fondo_solidaridad_pensional");
			if (in_array($row['clasificacion'],$configTypes)) {
				$btnNEData = "<div onclick='ventanaConfigurarDatosNE($cont,\"$row[clasificacion]\")' id='divImageConfiConcepto_$cont' title='Configurar datos nomina electronica' style='width:20px; float:left; margin-top:3px;cursor:pointer;'><img src='../../temas/clasico/images/BotonesTabs/book_open.png'></div>";
			}

			$botones=($estado==1 || $estado==3 || $estado==2 )? $btnNEData : '<div style="float:right; min-width:80px;margin-left:5px;">
                        	     						    <div onclick="guardarConcepto('.$cont.',\'actualizarconcepto\')" id="divImageSaveConcepto_'.$cont.'" title="Actualizar Concepto" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/reload.png" id="ImageSaveConcepto_'.$cont.'"></div>
                        	     						    <div onclick="ventanaConfigurarCuentasConcepto('.$cont.')" id="divImageConfiConcepto_'.$cont.'" title="Configurar Cuentas" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
                        	     						    '.$btnNEData.'
                        	     						    <div onclick="eliminarConcepto('.$cont.')" id="deleteConcepto_'.$cont.'" title="Eliminar Concepto" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
                        	     						</div>';

			$evento_input=($estado==1 || $estado==3 || user_permisos(169)=='false' || $estado==2)? '' : 'onkeyup="validaNumero(event,this)"';

            // VERIFICAR SI TIENE CONCEPTOS CON FORMULA
            $campoValorConcepto='readonly';
 			if ($row['formula']!='') {
 				$search_var_input=strpos($row['formula_original'], '{CT}');
 				if ($search_var_input!==false) {
 					$campoValorConcepto='onkeyup="calculaValorConceptoFormulaInput('.$id_empleado.','.$id_contrato.','.$row['id_concepto'].','.$cont.',event,this)" value="'.$row['valor_campo_texto'].'" '.$readonly.'';
 				}
 				$divStyleFunction = '';
 			}
 			else if ($row['id_prestamo']>0) {
				$divStyleFunction = '';
				$divImgFunction   = 'ventas16';
				$divTitleFunction = 'Prestamo del empleado';
 			}

     		$titlePrint = ($row['imprimir_volante']=='true')? 'Imprimible' : 'No Imprimible' ;
			$bodyConceptos .= '<div class="bodyDivNominaPlanilla">
	                        	    <div class="campo" id="divLoadConcepto_'.$cont.'">'.$cont.'</div>

	                        	    <div class="campo1" id="concepto_'.$cont.'" style="width:calc(100% - 50% - 70px - 18px );text-indent:5px;">'.$row['concepto'].'</div>
	                        	    <div class="campo" style="'.$divStyleFunction.'border: none;height:21px;width: 13px;margin-top: 1px;margin-left: -15px;background-image:url(img/'.$divImgFunction.'.png);background-repeat:no-repeat;background-color:#FFF;" title="'.$divTitleFunction.'"></div>
	                        	    <div class="campo1" style="width:calc(100% - 50% - 70px - 18px );text-indent:0;">
	                        	     	<input type="text" style="width:50px;border-right:1px solid #d4d4d4;padding-right: 0px;" '.$campoValorConcepto.' id="input_calculo_'.$cont.'" >
										<input type="text" style="width:calc(100% - 51px);padding-left: 0px;margin-left: -3px;" '.$evento_input.' id="valor_concepto_'.$cont.'" value="'.$row['valor_concepto'].'" '.$readonly.'>
	                        	    </div>

	                        	    <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;" title="'.$row['naturaleza'].'"><img src="img/'.$row['naturaleza'].'.png" ></div>
	                        	    <div class="campo1" id="imprimir_volante_'.$cont.'" style="width:30px;text-align:center;text-indent:0px;" title="'.$titlePrint.'"><img src="img/'.$row['imprimir_volante'].'.png"></div>
	                        	    '.$botones.'
	                        	    <input type="hidden" id="id_insert_concepto_'.$cont.'" value="'.$row['id'].'">
	                        	    <input type="hidden" id="id_concepto_'.$cont.'" value="'.$row['id_concepto'].'">
	                        	    <input type="hidden" id="id_contrato_concepto_'.$cont.'" value="'.$id_contrato.'">
	                        	    <input type="hidden" id="id_empleado_concepto_'.$cont.'" value="'.$id_empleado.'">
	                        	    <input type="hidden" id="formula_concepto_'.$cont.'" value="'.$row['formula_original'].'">
	                        	    <input type="hidden" id="nivel_formula_concepto_'.$cont.'" value="'.$row['nivel_formula'].'">
	                        	</div>
	                        	<script>
	                        	 	calculaValoresEmpleado('.$row['valor_concepto'].',"agregar","'.$row['naturaleza'].'");
	                        	 	// console.log("calculaValoresEmpleado('.$row['valor_concepto'].',\"agregar\",\"'.$row['naturaleza'].'\")");
	                        	</script>';
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
	                        	    <div class="campo1" style="width:calc(100% - 50% - 70px - 18px );text-indent:0;">
	                        	     	<input type="text" style="width:50px;border-right:1px solid #d4d4d4;padding-right: 0px;" readonly id="input_calculo_'.$cont.'" >
										<input type="text" style="width:calc(100% - 51px);padding-left: 0px;margin-left: -3px;" '.$evento_input.' '.$readonly.' id="valor_concepto_'.$cont.'">
	                    	    	</div>
	                        	    <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;"></div>
	                        	    <div class="campo1" id="imprimir_volante_'.$cont.'" style="width:30px;text-align:center;text-indent:0px;" title=""></div>
	                        	    <div style="float:right; min-width:80px;margin-left:5px;">
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

		$bodyConceptos .= '</div>
							<div class="contenedorConceptos" style="height:auto;margin-top:10px;">
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);">
									<div class="campoHeadConceptos" style="width:22px;border-right:none;"><p style="float:left;" title="Devengo"><img src="img/Devengo.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDevengo"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);" >
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Deduccion"><img src="img/Deduccion.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDeduccion"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);">
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Apropiacion"><img src="img/Apropiacion.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalApropiacion"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);">
									<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Provision"><img src="img/Provision.png"></p></div>
									<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalProvision"></div>
								</div>
								<div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;width:100%;">
									<div class="campoHeadConceptos" style="padding-right:5px;padding-left:5px;text-align: center;background-color : #F3F3F3;">Neto  Empleado</div>
									<div class="campo1" style="width:18px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
									<div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalNetoPagar"></div>
								</div>
							</div>

							<script>
								contConceptos='.$cont.';
								'.$script.'
								//RESIZE HEAD
								resizeHeadMyGrilla(document.getElementById("contenedorConceptos"), "headConceptos");
							</script>';

		echo $bodyConceptos;
	}

	function eliminarConcepto($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$naturaleza,$valor_concepto,$id_empresa,$link){
		// CONSULTAR SI TIENE LA OPCION DE RECALCULAR VALIDAR LA DEPENDENCIA DE CONCEPTOS
		$sql="SELECT recalcular_concepto FROM nomina_planillas_empleados
				WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_planilla=$id_planilla AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		$recalcular_concepto = mysql_result($query,0,'recalcular_concepto');

		if ($recalcular_concepto=='true') {
			// VALIDAR QUE OTROS CONCEPTOS NO DEPENDAN DEL CONCEPTO A ELIMINAR
			$sql   = "SELECT codigo_concepto
						FROM nomina_planillas_empleados_conceptos
						WHERE activo= 1
							AND id_concepto = '$id_concepto'
							AND id_contrato = '$id_contrato'
							AND id_empleado = '$id_empleado'
							AND id_planilla = '$id_planilla'
							AND id_empresa  = '$id_empresa'";
			$query = mysql_query($sql,$link);
			$codigo_concepto = mysql_result($query,0,'codigo_concepto');

			$sql   = "SELECT concepto
						FROM nomina_planillas_empleados_conceptos
						WHERE activo= 1
							AND formula_original LIKE '%[$codigo_concepto]%'
							AND id_contrato = '$id_contrato'
							AND id_empleado = '$id_empleado'
							AND id_planilla = '$id_planilla'
							AND id_empresa  = '$id_empresa'";
			$query = mysql_query($sql,$link);

			$conceptos_resul='';
			while ($row=mysql_fetch_array($query)) { $conceptos_resul.='\n -> '.$row['concepto']; }

			if ($conceptos_resul!='') { echo '<script>alert("Aviso!\nElimine los siguientes conceptos para continuar: '.$conceptos_resul.'");</script>'.$cont; exit; }
		}



		$sql   = "DELETE FROM nomina_planillas_empleados_conceptos
					WHERE activo=1
						AND id_concepto = '$id_concepto'
						AND id_contrato = '$id_contrato'
						AND id_empleado = '$id_empleado'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'";
		$query = mysql_query($sql,$link);
		if ($query) {
			actualizaDiasLaboradosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link);
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
		// CONSULTAR SI EL CONCEPTO YA SE GUARDO
		$sql="SELECT id FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_concepto=$id_concepto AND id_planilla=$id_planilla";
		$query=mysql_query($sql,$link);
		$id_registro=mysql_result($query,0,'id');
		if ($id_registro>0) { echo '<script>console.log("'.$sql.'");</script>'; return; }

		$valueInsert = '';
		// CONSULTAR TODA LA INFORMACION DEL CONCEPTO
		$sql   = "SELECT
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
						resta_dias
				  	FROM nomina_conceptos
				  	WHERE activo=1
				  		AND id_empresa=$id_empresa
				  		AND id=$id_concepto";
		$query = mysql_query($sql,$link);
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
								'resta_dias'                               => mysql_result($query,0,'resta_dias'),
				 			);

		// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA CONFIGURACION DIFERENTE A LA GENERAL
		$sql="SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id=$id_contrato AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		$id_grupo_trabajo=mysql_result($query,0,'id_grupo_trabajo');

		$sql   = "SELECT
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
					FROM nomina_conceptos_grupos_trabajo
					WHERE activo=1
						AND id_empresa=$id_empresa
						AND id_concepto=$id_concepto
						AND id_grupo_trabajo=$id_grupo_trabajo";
		$query = mysql_query($sql,$link);

		// SI TIENE CONFIGURACION POR GRUPO DE TRABAJO
		if (mysql_result($query,0,'id_concepto')>0) {
			$formula_query = mysql_result($query,0,'formula');
			// $arrayConcepto['id_concepto']                              =mysql_result($query,0,'id_concepto');
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
						'".$arrayConcepto['resta_dias']."',
						'$input_calculo'
						)";

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
						resta_dias,
						valor_campo_texto)
					VALUES $valueInsert";
		// print_r($arrayConcepto);
		//$sql="INSERT INTO nomina_planillas_empleados_conceptos (id_planilla,id_empleado,id_contrato,id_concepto,valor_concepto,id_empresa) VALUES ('$id_planilla','$id_empleado','$id_contrato','$id_concepto','$valor_concepto','$id_empresa')";
		$query = mysql_query($sql,$link);
		if ($query) {
			$sql    = "SELECT LAST_INSERT_ID() AS id";
			$query  = mysql_query($sql,$link);
			$lastId = mysql_result($query,0,'id');

			// SI EL CONCEPTO LE RESTA A LOS DIAS DE LA PLANILLA
			if ($arrayConcepto['resta_dias']=='true'){ actualizaDiasLaboradosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link); }
			$evento_input=(user_permisos(169)=='false')? 'readonly' : 'onkeyup=\"validaNumero(event,this)\"';
			$contOld = $cont;
			$cont++;
			$styleImgFunction=($arrayConcepto['formula'] !='')? 'document.getElementById("divImgFunction_'.$contOld.'").style.display = "block";' : '' ;
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
                        	     			"<input onkeyup=\"validaNumero(event,this)\" id=\"input_calculo_'.$cont.'\" style=\"width:50px;border-right:1px solid #d4d4d4;\" type=\"text\" readonly>"+
                        	     			"<input '.$evento_input.'  id=\"valor_concepto_'.$cont.'\" style=\"width:calc(100% - 50px);\" type=\"text\">"+
                        	     		"</div>"+
                        	     		"<div class=\"campo1\" id=\"naturaleza_'.$cont.'\" style=\"width:30px;text-align:center;\"></div>"+
                        	     		"<div class=\"campo1\" id=\"imprimir_volante_'.$cont.'\" style=\"width:30px;text-align:center;text-indent:0px;\" title=\"\"></div>"+
                        	     		"<div style=\"float:right; min-width:80px;margin-left:5px;\">"+
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
					document.getElementById("divImageSaveConcepto_'.$contOld.'").style.display = "none";
					document.getElementById("divImageSaveConcepto_'.$contOld.'").setAttribute("onclick","guardarConcepto('.$contOld.',\'actualizarconcepto\')");
					document.getElementById("ImageSaveConcepto_'.$contOld.'").setAttribute("src","img/reload.png");
					document.getElementById("divImageConfiConcepto_'.$contOld.'").style.display       = "block";
					document.getElementById("deleteConcepto_'.$contOld.'").style.display       = "block";
					document.getElementById("btnBuscarConcepto_'.$contOld.'").style.display    = "none";
					document.getElementById("id_insert_concepto_'.$contOld.'").value="'.$lastId.'";
					document.getElementById("valor_concepto_'.$cont.'").focus();

					console.log("'.$arrayConcepto['formula'].'");

					'.$styleImgFunction.'

					//CALCULAR EL VALOR DEL EMPLEADO
					calculaValoresEmpleado('.$valor_concepto.',"agregar","'.$naturaleza.'");
					//CALCULAR LOS VALORES DE LA PLANILLA
					calcularValoresPlanilla();

				</script>'.$contOld;

				// CONSULTAR SI OTROS CONCEPTOS DEPENDEN DE ESTE, SI ES ASI LLAMAR LA FUNCION RECALCULAR
				$sql   = "SELECT COUNT(id) AS cont
							FROM nomina_planillas_empleados_conceptos
							WHERE activo=1
								AND id_empresa=$id_empresa
								AND id_planilla=$id_planilla
								AND id_empleado=$id_empleado
								AND id_contrato=$id_contrato";
				$query = mysql_query($sql,$link);
				$dependencias=mysql_result($query,0,'cont');
				if ($dependencias>0) {
					recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link);
				}


		}
		else{
			echo '<script>alert("Error\nNo se guardo el concepto intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'.$contOld;
		}
	}

	function actualizarconcepto($id_insert,$input_calculo,$formula,$nivel_formula_concepto,$id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$valor_concepto,$naturaleza,$id_empresa,$link){
		//CONSULTAR EL VALOR ANTERIOR PARA CARCULAR EN LA PLANILLA
		$sql   = "SELECT valor_concepto,id_prestamo,id_empleado,resta_dias
					FROM nomina_planillas_empleados_conceptos
					WHERE id_concepto = '$id_concepto'
						AND id_contrato = '$id_contrato'
						AND id_empleado = '$id_empleado'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'
						AND id          = $id_insert";
		$query = mysql_query($sql,$link);

		$valor_concepto_anterior = mysql_result($query,0,'valor_concepto');
		$id_empleado_prestamo    = mysql_result($query,0,'id_empleado');
		$id_prestamo             = mysql_result($query,0,'id_prestamo');
		$resta_dias              = mysql_result($query,0,'resta_dias');

		// SI TIENE UN ID DE PRESTAMO, VALIDAR QUE EL VALOR INGRESADO NO EXEDA AL VALOR RESTANTE DEL PRESTAMO
		if ($id_prestamo>0) {
			$sql   = "SELECT valor_prestamo_restante FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_prestamo";
			$query = mysql_query($sql,$link);

			$valor_prestamo_restante = mysql_result($query,0,'valor_prestamo_restante');
			if ($valor_concepto>$valor_prestamo_restante) {
				echo '<script>
						alert("Error!\nEste concepto pertenece a un Prestamo y el valor ingresado supera el valor restante del prestamo");
						document.getElementById("valor_concepto_'.$cont.'").value="'.$valor_prestamo_restante.'";
					</script>'	;
				exit;
			}
		}

		//ACTUALIZAR EL CAMPO CON EL NUEVO VALOR
		$sql   = "UPDATE nomina_planillas_empleados_conceptos
					SET valor_concepto='$valor_concepto',valor_campo_texto='$input_calculo'
					WHERE id_concepto = '$id_concepto'
						AND id_contrato = '$id_contrato'
						AND id_empleado = '$id_empleado'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'
						AND id          = $id_insert";
		$query = mysql_query($sql,$link);
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
			actualizaDiasLaboradosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link);
			recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link);
		}
		else{
			echo '<script>alert("Error\nNo se actualizo el valor intentelo de nuevo");</script>'.$cont;
		}
	}

	function updateFinalizaContrato($terminar_contrato,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link){
		$sql   = "UPDATE nomina_planillas_empleados
					SET terminar_contrato='$terminar_contrato'
					WHERE id_empleado = '$id_empleado'
						AND id_contrato = '$id_contrato'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query) { echo '<script>alert("Error\nNo se actualizo el campo, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>'; }
	}

	function updateDiasLaborados($dias,$id_empleado,$id_contrato,$id_planilla,$id_empresa,$link){
		// VALIDAR QUE LOS DIAS LABORADOS NO SEAN MAYORES A LOS DIAS DE LA PLANILLA
		// $sql   = "SELECT dias_liquidacion FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		// $query = mysql_query($sql,$link);

		// $dias_liquidacion=mysql_result($query,0,'dias_liquidacion');

		// if ($dias>$dias_liquidacion) {
		// 	echo'<script>
		// 			alert("Aviso!\nLos dias no pueden ser superiores a '.$dias_liquidacion.' dias ");
		// 			document.getElementById("dias_laborados").value="'.$dias_liquidacion.'";
		// 		</script>';
		// 	exit;
		// }

		$sql   = "UPDATE nomina_planillas_empleados
					SET dias_laborados='$dias'
					WHERE id_empleado = '$id_empleado'
						AND id_contrato = '$id_contrato'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'";
		$query = mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo el campo, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
		actualizaDiasLaboradosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link);
		recalcularValoresConceptos($id_planilla,'DL',$id_contrato,$id_empleado,$id_empresa,$link);
	}

	function verificaEmpleado($check,$id_contrato,$id_empleado,$id_planilla,$cont,$id_empresa,$link){

		$valor = ($check=='true')? 'false' : 'true' ;
		$sql   = "UPDATE nomina_planillas_empleados
					SET verificado='$valor'
					WHERE id_empleado = '$id_empleado'
						AND id_contrato = '$id_contrato'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'";
		$query = mysql_query($sql,$link);

		if ($query) { echo '<script>document.getElementById("verifica_empleado_'.$id_contrato.'").setAttribute("src","img/checkbox_'.$valor.'.png");</script>'.$cont; }
		else{ echo '<script>alert("Se produjo un error intentelo de nuevo");</script>'.$cont; }
	}

	function ventanaConfigurarCuentasConcepto($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link){
		//CONSULTAR LAS CUENTAS DEL CONCEPTO
		$sql   = "SELECT concepto,
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
						caracter_contrapartida
					FROM nomina_planillas_empleados_conceptos
					WHERE activo=1
						AND id_planilla = '$id_planilla'
						AND id_concepto = '$id_concepto'
						AND id_contrato = '$id_contrato'
						AND id_empleado = '$id_empleado'
						AND id_empresa  = '$id_empresa'";
		$query = mysql_query($sql,$link);

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
				</div>';
	}

	function ventanaConfigurarHorasExtras($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link){
		echo "<div>Configurar horas extras</div>";
	}//end ventanaConfigurarHoraConcepto

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
		$id_niif         = mysql_result($queryNiif,0,'id');

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo '<script>
						document.getElementById("'.$campoText.'").innerHTML = "'.$cuentaNiif.'";
						document.getElementById("'.$campoId.'").value    = "'.$id_niif.'";
					</script>'; }

		echo'<img src="img/refresh.png" />';
	}

	function updateCuentasConcepto($id_cuenta_colgaap,$id_cuenta_niif,$id_cuenta_contrapartida_colgaap,$id_cuenta_contrapartida_niif,$id_concepto,$id_contrato,$id_empleado,$id_planilla,$id_empresa,$link){
		$sql  = "UPDATE nomina_planillas_empleados_conceptos
					SET id_cuenta_colgaap = '$id_cuenta_colgaap',
						id_cuenta_niif = '$id_cuenta_niif',
						id_cuenta_contrapartida_colgaap = '$id_cuenta_contrapartida_colgaap',
						id_cuenta_contrapartida_niif = '$id_cuenta_contrapartida_niif'
					WHERE activo=1
						AND id_concepto = '$id_concepto'
						AND id_contrato = '$id_contrato'
						AND id_empleado = '$id_empleado'
						AND id_planilla = '$id_planilla'
						AND id_empresa  = '$id_empresa'";
		$query = mysql_query($sql,$link);

		if ($query) { echo'<script>Win_Ventana_configurar_cuentas_conceptos.close();</script>'; }
		else{
			echo '<script>alert("Error\nNo se actualizaron las cuentas, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function calculaValorConceptoFormulaInput($id_insert_concepto,$id_planilla,$id_concepto,$id_contrato,$id_empleado,$cont,$variable,$id_empresa,$link){
		// SI EL CONCEPTO YA ESTA INSERTADO EN LA BASE DE DATOS
		if ($id_insert_concepto>0) {
			$sql   = "SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query = mysql_query($sql,$link);
			$salario_basico = mysql_result($query,0,'salario_basico');

			// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
			$sql   = "SELECT dias_laborados FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
			$query = mysql_query($sql,$link);
			$dias_laborados=mysql_result($query,0,'dias_laborados');

			// CONSULTAR LA FORMULA DEL CONCEPTO
			$sql   = "SELECT formula_original FROM nomina_planillas_empleados_conceptos
						WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_concepto=$id_concepto AND id_contrato=$id_contrato AND id_empleado=$id_empleado" ;
			$query = mysql_query($sql,$link);

			$formula_original = mysql_result($query,0,'formula_original');
			$formula          = mysql_result($query,0,'formula_original');

			// REEMPLAZAR PRIMERO EL VALOR DE LA FORMULA DEL INPUT
			$formula = str_replace('{CT}', $variable, $formula);
			$formula = str_replace('{SC}', $salario_basico, $formula);
			$formula = str_replace('{DL}', $dias_laborados, $formula);

			//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
			$search_var_concepto=strpos($formula, '[');
			if ($search_var_concepto===false) {
				// CALCULAR LA FORMULA
				$valor_concepto=calcula_formula($formula);
				// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
				if ($valor_concepto==false && $arrayConceptosResul['formula']!='') { echo '<script>alert("Error en el calculo de la fomula del concepto\nFormula: '.$formula.'");</script>'; continue; }
				// ACTUALIZAR EL VALOR DEL CONCEPTO CON EL VALOR DE LA FORMULA
				$sql   = "UPDATE nomina_planillas_empleados_conceptos SET formula='$formula',valor_concepto=$valor_concepto,valor_campo_texto=$variable
							WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_concepto=$id_concepto AND id_contrato=$id_contrato AND id_empleado=$id_empleado";
				$query = mysql_query($sql,$link);
				recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link,'exit');
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
				$sql   = "SELECT id_concepto,codigo_concepto,concepto,valor_concepto,valor_campo_texto
							FROM nomina_planillas_empleados_conceptos
							WHERE activo=1
								AND id_planilla = '$id_planilla'
								AND id_empleado = '$id_empleado'
								AND id_contrato = '$id_contrato'
								AND id_empresa  = '$id_empresa'";
				$query=mysql_query($sql,$link);

				while ($row=mysql_fetch_array($query)) {

					if($row['valor_concepto']<0){ continue; }		// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE

					$formula=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $formula);		// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
					$formula=str_replace('|>'.$row['codigo_concepto'].'<|', $row['valor_campo_texto'], $formula);		// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
				}

				$formula=reemplazarValoresFaltantes($formula);
				//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
				$search_var_concepto1=strpos($formula, '[');
				$search_var_concepto2=strpos($formula, '|>');
				if ($search_var_concepto1===false && $search_var_concepto2===false) {
					// CALCULAR LA FORMULA
					$valor_concepto=calcula_formula($formula);
					// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
					if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
						echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: '.$formula.'");</script>';
						continue;
					}

					// ACTUALIZAR EL VALOR DEL CONCEPTO CON EL VALOR DE LA FORMULA
					$sql="UPDATE nomina_planillas_empleados_conceptos SET formula='$formula',valor_concepto=$valor_concepto,valor_campo_texto=$variable
							WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_concepto=$id_concepto AND id_contrato=$id_contrato AND id_empleado=$id_empleado";
					$query=mysql_query($sql,$link);
					recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link,'exit');

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
					$conceptos = EncuentraVariablesCadena($formula_original);
					foreach ($conceptos as $key => $codigo) { $mensaje.='\n* '.$codigo; }
					echo '<script>alert("No se puede calcular el valor del concepto por que necesitan los coneptos con codigos'.$conceptos.'");</script>';
				}

			}
		}
		// SI EL CONCEPTO AUN NO ESTA INSERTADO EN LA BASE DE DATOS
		else{
			// PRIMERO CONSULTAR LA FORMULA DEL CONCEPTO
			$sql   = "SELECT formula FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_concepto";
			$query = mysql_query($sql,$link);
			$formula          = mysql_result($query,0,'formula');
			$formula_original = mysql_result($query,0,'formula');

			// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA FORMULA DIFERENTE A LA GENERAL
			$sql   = "SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
			$query = mysql_query($sql,$link);

			$id_grupo_trabajo = mysql_result($query,0,'id_grupo_trabajo');
			if ($id_grupo_trabajo>0) {
				$sql   = "SELECT formula FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo AND id_concepto=$id_concepto";
				$query = mysql_query($sql,$link);

				$formula_grupo_trabajo=mysql_result($query,0,'formula');

				if ($formula_grupo_trabajo!='') { $formula=$formula_grupo_trabajo; }
			}

		// CONSULTAR EL SALARIO DEL CONTRATO
		$sql   = "SELECT salario_basico FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
		$query = mysql_query($sql,$link);

		$salario_basico = mysql_result($query,0,'salario_basico');

		// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
		$sql   = "SELECT dias_laborados FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
		$query = mysql_query($sql,$link);
		$dias_laborados = mysql_result($query,0,'dias_laborados');

		// REEMPLAZAR LOS VALORES PRINCIPALES DEL LA FORMULA
		$formula = str_replace('{SC}', $salario_basico, $formula);
		$formula = str_replace('{DL}', $dias_laborados, $formula);
		$formula = str_replace('{CT}', $variable, $formula);

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
			$sql="SELECT id_concepto,codigo_concepto,concepto,valor_concepto,valor_campo_texto FROM nomina_planillas_empleados_conceptos WHERE activo=1
					AND id_planilla = '$id_planilla'
					AND id_empleado = '$id_empleado'
					AND id_contrato = '$id_contrato'
					AND id_empresa  = '$id_empresa'";
			$query=mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {

				if($row['valor_concepto']<0){ continue; }		// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE

				$formula=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $formula);	// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
				$formula=str_replace('|>'.$row['codigo_concepto'].'<|', $row['valor_campo_texto'], $formula);	// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
			}

			//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
			$search_var_concepto1=strpos($formula, '[');
			$search_var_concepto2=strpos($formula, '|>');
			if ($search_var_concepto1===false && $search_var_concepto2===false) {
				// CALCULAR LA FORMULA
				$valor_concepto = calcula_formula($formula);
				// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
				if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
					echo '<script>alert("Error en el calculo de la fomula del concepto\nFormula: '.$formula.'");</script>';
				}

				echo"<script>
						document.getElementById('valor_concepto_$cont').value         = '$valor_concepto';
						document.getElementById('formula_concepto_$cont').value       = '$formula_original';
						console.log('".$formula."');
						Win_Ventana_bucar_concepto.close();
					</script>";
			}
			// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES MOSTRAR MENSAJE DE ERROR
			else{
				$conceptos = EncuentraVariablesCadena($formula_original);

				foreach ($conceptos as $key => $codigo) { $mensaje.='\n* '.$codigo; }

				echo '<script>alert("No se puede calcular el valor del concepto por que necesitan los siguientes conceptos con codigo: '.$mensaje.'");</script>';
			}

		// }

		}

		echo $cont;
	}

	function calculaValorConceptoBuscado($id_concepto,$id_contrato,$id_empleado,$cont,$id_planilla,$id_empresa,$link){

		// CONSULTAR LA FORMULA DEL CONCEPTO
		$sql   = "SELECT formula,nivel_formula,tipo_concepto FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id='$id_concepto' " ;
		$query = mysql_query($sql,$link);

		$formula_original = mysql_result($query,0,'formula');
		$nivel_formula    = mysql_result($query,0,'nivel_formula');
		$formula          = mysql_result($query,0,'formula');
		$tipo_concepto    = mysql_result($query,0,'tipo_concepto');

		// echo '<script>console.log("formula: '.$formula.'");</script>';
		// CONSULTAR EL GRUPO DE TRABAJO PARA IDENTIFICAR SI TIENE UNA FORMULA DIFERENTE A LA GENERAL
		$sql   = "SELECT id_grupo_trabajo FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
		$query = mysql_query($sql,$link);

		$id_grupo_trabajo = mysql_result($query,0,'id_grupo_trabajo');
		if ($id_grupo_trabajo>0) {
			$sql   = "SELECT formula FROM nomina_conceptos_grupos_trabajo WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo_trabajo=$id_grupo_trabajo AND id_concepto=$id_concepto";
			$query = mysql_query($sql,$link);

			$formula_grupo_trabajo = mysql_result($query,0,'formula');
			if ($formula_grupo_trabajo!='') {
				$formula=$formula_grupo_trabajo;
				$formula_original=$formula_grupo_trabajo;
			}
		}
		// echo '<script>console.log("formula: '.$formula.'");</script>';

		// SI EL CONCEPTO NO TIENE FORMULA, TERMINAMOS EL PROCESO
		if ($formula=='') {
			// CONSULTAR SI EL CONCEPTO TIENE VALORES PRECONFIGURADOS POR DEFECTO
			if ($tipo_concepto=='Personal') {
				// SELECCIONAR EL VALOR DEL CONCEPTO PERSONA
				$sql="SELECT valor_concepto FROM nomina_conceptos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_concepto=$id_concepto AND id_empleado=$id_empleado";
			}
			else{
				// CONSULTAR EL CARGO DEL EMPLEADO
				$sql   ="SELECT id_cargo FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
				$query = mysql_query($sql,$link);
				$id_cargo = mysql_result($query,0,'id_cargo');

				$sql = "SELECT valor_concepto FROM nomina_conceptos_cargo WHERE activo=1 AND id_empresa=$id_empresa AND id_cargo=$id_cargo AND id_concepto=$id_concepto";
			}

			$query = mysql_query($sql,$link);
			$valor_concepto = mysql_result($query,0,'valor_concepto');

			echo '<script>
					document.getElementById("valor_concepto_'.$cont.'").value = "'.$valor_concepto.'";
					Win_Ventana_bucar_concepto.close();
				</script>';
			exit;
		}

		// CONSULTAR EL SALARIO DEL CONTRATO
		$sql   = "SELECT salario_basico,valor_nivel_riesgo_laboral FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
		$query = mysql_query($sql,$link);

		$salario_basico             = mysql_result($query,0,'salario_basico');
		$valor_nivel_riesgo_laboral = mysql_result($query,0,'valor_nivel_riesgo_laboral');

		// CONSULTAR LOS DIAS LABORADOS DEL EMPLEADO
		$sql   = "SELECT dias_laborados FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ";
		$query = mysql_query($sql,$link);
		$dias_laborados = mysql_result($query,0,'dias_laborados');

		// REEMPLAZAR LOS VALORES PRINCIPALES DEL LA FORMULA
		$formula = str_replace('{SC}', $salario_basico, $formula);
		$formula = str_replace('{DL}', $dias_laborados, $formula);
		$formula = str_replace('{NRL}', $valor_nivel_riesgo_laboral, $formula);

		//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
		$search_var_concepto=strpos($formula, '{CT}');
		if ($search_var_concepto!==false) {
			echo '<script>
					document.getElementById("input_calculo_'.$cont.'").readOnly=false;
					document.getElementById("input_calculo_'.$cont.'").setAttribute("onkeyup","calculaValorConceptoFormulaInput('.$id_empleado.','.$id_contrato.','.$id_concepto.','.$cont.',event,this)");
					document.getElementById("formula_concepto_'.$cont.'").value="'.$formula_original.'";
					document.getElementById("nivel_formula_concepto_'.$cont.'").value="'.$nivel_formula.'";

					document.getElementById("valor_concepto_'.$cont.'").value = "0";

					Win_Ventana_bucar_concepto.close();
				</script>';
		}
		// SI EXISTEN VARIABLES DE OTROS CONCEPTOS, CONSULTAR EN LA BASE DE DATOS Y REEMPLAZAR
		else{
			$sql   = "SELECT id_concepto,codigo_concepto,concepto,valor_concepto,valor_campo_texto FROM nomina_planillas_empleados_conceptos WHERE activo=1
						AND id_planilla = '$id_planilla'
						AND id_empleado = '$id_empleado'
						AND id_contrato = '$id_contrato'
						AND id_empresa  = '$id_empresa'";
			$query=mysql_query($sql,$link);

			while ($row=mysql_fetch_array($query)) {

				if($row['valor_concepto']<0){ continue; }			// VALIDAR QUE EL CONCEPTO TENGA UN VALOR SINO CONTINUE

				$formula=str_replace('['.$row['codigo_concepto'].']', $row['valor_concepto'], $formula);		// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
				$formula=str_replace('|>'.$row['codigo_concepto'].'<|', $row['valor_campo_texto'], $formula);		// REEMPLAZAR LAS VARIABLES DE LA FORMULA CON LOS VALORES DE CADA CONCEPTO
			}

			$formula=reemplazarValoresFaltantes($formula);
			//SI NO EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA SINO SALTAMOS ESTE CONCEPTO
			$search_var_concepto1=strpos($formula, '[');
			$search_var_concepto2=strpos($formula, '|>');
			if ($search_var_concepto1===false || $search_var_concepto2===false) {
				// CALCULAR LA FORMULA
				$valor_concepto=calcula_formula($formula);
				// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
				if ($valor_concepto==false && $arrayConceptosResul['formula']!='') {
					echo '<script>alert("Error en el calculo de la fomula del concepto\nFormula: '.$formula.'");</script>';
				}

				echo "<script>
						document.getElementById('valor_concepto_$cont').value         = '$valor_concepto';
						document.getElementById('formula_concepto_$cont').value       = '$formula_original';
						document.getElementById('nivel_formula_concepto_$cont').value = '$nivel_formula';
						console.log('".$formula."')
						Win_Ventana_bucar_concepto.close();
					</script>";
			}
			// SI AUN EXISTEN VARIABLES A REEMPLAZAR ENTONCES MOSTRAR MENSAJE DE ERROR
			else{
				$conceptos=EncuentraVariablesCadena($formula_original);
				foreach ($conceptos as $key => $codigo) { $mensaje.='\n* '.$codigo; }
				echo '<script>alert("No se puede calcular el valor del concepto por que necesitan los siguientes conceptos con codigo: '.$mensaje.'");</script>';
			}
		}
		echo $cont;
	}

	function actualizaDiasLaboradosEmpleado($id_planilla,$id_empleado,$id_contrato,$id_empresa,$link){
		// CONSULTAR TODOS LOS CONCEPTOS QUE LE DESCUENTAN A LOS DIAS LABORADOS
		$sql="SELECT valor_campo_texto FROM nomina_planillas_empleados_conceptos
				WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_contrato=$id_contrato AND resta_dias='true' AND id_planilla=$id_planilla";
		$query=mysql_query($sql,$link);
		$resta_dias=0;
		while ($row=mysql_fetch_array($query)) {
			$resta_dias+=$row['valor_campo_texto'];
		}

		// ACTUALIZAR EL VALOR DE LOS DIAS LABORADOS DEL EMPLEADO
		$sql="UPDATE nomina_planillas_empleados SET dias_laborados_empleado=dias_laborados-$resta_dias
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);

		// CONSULTAR LOS DIAS RESTANTES
		$sql="SELECT dias_laborados_empleado FROM nomina_planillas_empleados
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		$dias=mysql_result($query,0,'dias_laborados_empleado');

		echo '<script>document.getElementById("dias_laborados_empleado").innerHTML="'.$dias.'";</script>';
	}

	///////////////////////////////////////////////////////////////
	// FUNCION PARA RECALCULAR LOS CONCEPTOS CUANDO SE ACTUALIZA //
	///////////////////////////////////////////////////////////////
	function recalcularValoresConceptos($id_planilla,$id_concepto,$id_contrato,$id_empleado,$id_empresa,$link,$opc_exit=''){
		// VALIDAR QUE ESTE SELECCIONADA LA OPCION DE RECALCULAR LOS CONCEPTOS
		$sql="SELECT recalcular_concepto FROM nomina_planillas_empleados
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado ANd id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);

		$recalcular_concepto=mysql_result($query,0,'recalcular_concepto');
		if ($recalcular_concepto=='false') {
			return;
		}

		// VALIDAR QUE SEA DIFERENTE AL CAMPO DIAS LABORADOS DEL EMPLEADO
		if ($id_concepto=='DL') {
			// SI ES UN CONCEPTO, VALIDAR QUE SEA REQUERIDO POR OTROS CONCEPTOS Y SUS CALCULOS, SI NO ES NECESARIO PARA ACTUALIZAR EL VALOR DEL LA FORMULA DE OTROS CONCEPTOS TERMINAR FUNCION
			$sql="SELECT id FROM nomina_planillas_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND formula_original LIKE '%{DL}%' ";
			$query=mysql_query($sql,$link);
			$id_resul=mysql_result($query,0,'id');

			if ($id_resul==0) { return; }
		}
		else{
			// CONSULTAR EL CODIGO DEL CONCEPTO
			$sql="SELECT codigo_concepto,nivel_formula FROM nomina_planillas_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_concepto=$id_concepto";
			$query=mysql_query($sql,$link);
			$codigo_concepto =mysql_result($query,0,'codigo_concepto');
			$nivel_formula   =mysql_result($query,0,'nivel_formula');
			// SI ES UN CONCEPTO, VALIDAR QUE SEA REQUERIDO POR OTROS CONCEPTOS Y SUS CALCULOS, SI NO ES NECESARIO PARA ACTUALIZAR EL VALOR DEL LA FORMULA DE OTROS CONCEPTOS TERMINAR FUNCION
			$sql="SELECT id FROM nomina_planillas_empleados_conceptos
					WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND formula_original LIKE '%$codigo_concepto%' ";
			$query=mysql_query($sql,$link);
			$id_resul=mysql_result($query,0,'id');

			if ($id_resul==0) {
				return;
			}
		}

		// CONSULTAR NUEVAMENTE LOS DATOS DEL SALARIO DE CONTRATO Y LOS DIAS LABORADOS
		//CONSULTAR LOS DIAS DE LABORADOS DEL EMPLEADO
		$sql="SELECT dias_laborados FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		$dias_laborados=mysql_result($query,0,'dias_laborados');


		// CONSULTAR EL SALRAIO DEL CONTRATO DEL EMPLEADO
		$sql="SELECT salario_basico,valor_nivel_riesgo_laboral	FROM empleados_contratos WHERE activo=1 AND (estado=0 OR estado=2) AND id_empresa=$id_empresa AND id=$id_contrato AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		$salario_basico = mysql_result($query,0,'salario_basico');
		$valor_nivel_riesgo_laboral=mysql_result($query,0,'valor_nivel_riesgo_laboral');

		// CONSULTAR TODOS LOS CONCEPTOS Y ORGANIZARLOS EN UN ARRAY
		$sql="SELECT id_concepto,codigo_concepto,concepto,nivel_formula,formula_original,valor_concepto,valor_campo_texto,id_cuenta_colgaap,id_cuenta_niif,id_cuenta_contrapartida_colgaap,id_cuenta_contrapartida_niif
				FROM nomina_planillas_empleados_conceptos
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
						$arrayConceptosResul['formula']=str_replace('|>'.$arrayConceptosResul_search['codigo_concepto'].'<|', $arrayConceptosResul_search['valor_campo_texto'], $arrayConceptosResul['formula']);
					}
				}
				$arrayConceptosResul['formula']=reemplazarValoresFaltantes($arrayConceptosResul['formula']);
				//SI EXISTE EN LA FORMULA EL SIMBOLO [ QUIERE DECIR QUE NO ESTAN TODOS LOS CONCEPTOS NECESARIOS PARA LA FORMULA
				$search_var_concepto1=strpos($arrayConceptosResul['formula'],'[');
				$search_var_concepto2=strpos($arrayConceptosResul['formula'],'|>');
				//SI ESTAN TODOS LOS CONEPTOS PARA LA FORMULA
				if ($search_var_concepto1===false || $search_var_concepto2===false) {
					// CALCULAR LA FORMULA
					$valor_concepto=calcula_formula($arrayConceptosResul['formula']);
					// SI LA FORMULA RETORNA ERROR ENTONCES CONTINUE
					if ($valor_concepto===false && $arrayConceptosResul['formula']!='') {
						echo '<script>alert("Error en el calculo de la fomula del concepto '.$arrayConceptosResul['concepto'].' formula: '.$arrayConceptosResul['formula'].'");</script>';
						continue;
					}

					// SI LA FORMULA ES CORRECTA, REALIZAR EL UPDATE DEL CON LOS VALORES DE LA FORMULA
					$sql="UPDATE nomina_planillas_empleados_conceptos SET
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
					$mensaje   = '';
					$conceptos = EncuentraVariablesCadena($arrayConceptosResul['formula']);

					foreach ($conceptos as $key => $codigo) { $mensaje.='\n* '.$codigo; }
					// echo 'Error: '.$mensaje.'<br>';
					echo '<script>alert("No se puede calcular el valor del concepto:'.$arrayConceptosResul['concepto'].' por que necesitan los siguientes conceptos con codigo: '.$mensaje.'");</script>';
				}
			}
		}

		echo '<script>cargarConceptosEmpleado('.$id_contrato.','.$id_empleado.',0)</script>';
		if ($opc_exit=='exit') {
			exit;
		}
	}

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

	function actualizarSucursal($id_planilla,$sucursal,$id_empresa,$link){
		$sql   = "UPDATE nomina_planillas SET id_sucursal=$sucursal WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla ";
		$query = mysql_query($sql,$link);
		if (!$query) {
			echo '<script>alert("Error\nNo se actualizo la sucursal de la planilla\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function microtime_float(){
		list($useg, $seg) = explode(" ", microtime());
		return ((float)$useg + (float)$seg);
	}

	// ============== BUSCAR EMPLEADO EN LA INTERFAZ DE LA PLANILLA ==================//
	function buscarEmpleadoCargado($id_planilla,$filtro,$estado,$id_empresa,$link){
		// CONSULTAR EL ESTADO DEL DOCUMENTO
		$sql="SELECT estado FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query=mysql_query($sql,$link);
		$estado = mysql_result($query,0,'estado');

		//======================= CONSULTAR LOS EMPLEADOS CARGADOS EN LA PLANILLA ==================================//
	    $sql='SELECT id_empleado,documento_empleado,nombre_empleado,id_contrato,verificado,email_enviado
	    		FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa='.$id_empresa.' AND id_planilla='.$id_planilla.' '.$filtro;
	    $query=mysql_query($sql,$link);
	    $bodyEmpleados='';
	    $cont=1;
	    while ($row=mysql_fetch_array($query)) {

	    	$titleImg=($row['email_enviado']=='true')? 'Reenviar Volante por Email' : 'Enviar Volante por Email' ;

	    	if ($estado==1 || $estado==2) {
            $btnEmail='<div  class="iconBuscar" style="margin-left: -1px;" onclick="enviarVolanteUnicoEmpleado(\''.$row['id_contrato'].'\',\''.$row['id_empleado'].'\',\''.$row['nombre_empleado'].'\')">
                                <img class="capturaImgCheck" id="imgEmail_'.$row['id_contrato'].'" src="img/enviaremail_'.$row['email_enviado'].'.png" title="'.$titleImg.'">
                            </div>';
            $btnPrint='<div  class="iconBuscar" style="margin-left: -1px;" onclick="imprimirVolanteUnicoEmpleado(\''.$row['id_empleado'].'\')">
                                <img class="capturaImgCheck" id="imgPrint_'.$row['id_contrato'].'" src="img/printer.png" title="Imprimir Volante de nomina">
                            </div>';
        	}
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

	//====================== FUNCION PARA GUARDAR LA OBSERVACION ==========================//
	function guardarObservacionEmpleado($observacion,$id_planilla,$id,$id_empresa,$link){

		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);

		$sql   = "UPDATE nomina_planillas_empleados SET  observaciones='$observacion' WHERE id_planilla='$id_planilla' AND id=$id AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		if($query){ echo 'true'; }
		else{ echo'false'; }
	}

	//===================== FUNCION PARA ACTUALIZAR EL RECALCULO DE LOS CONCEPTOS =====================//
	function actualizaRecalcularFormula($id_contrato,$id_empleado,$id_planilla,$recalcular_concepto,$id_empresa,$link){
		$valor_campo = ($recalcular_concepto=='true')? 'false' : 'true' ;
		$sql="UPDATE nomina_planillas_empleados SET recalcular_concepto='$valor_campo'
				WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		if ($query) {
			echo '<img src="img/funcion.png">
				<img src="img/checkbox_'.$valor_campo.'.png"  style="width:18px;height:18px;" onclick="actualizaRecalcularFormula('.$id_empleado.','.$id_contrato.',\''.$valor_campo.'\')">';
		}
		else{
			echo '<script>alert("Error!\nNo se pudo actualizar! intentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>
				<img src="img/funcion.png">
				<img src="img/checkbox_'.$recalcular_concepto.'.png"  style="width:18px;height:18px;" onclick="actualizaRecalcularFormula('.$id_empleado.','.$id_contrato.',\''.$recalcular_concepto.'\')">';
		}

	}

    //////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////

	//==================== FUNCION PARA GENERAR LA PLANILLA DE NOMINA ================//
  function terminarGenerar($id_planilla,$id_empresa,$id_sucursal,$link){
    $tiempo_inicio = microtime_float();
    // ACTUALIZAR LOS DIAS DE LIQUIDACION EN LOS CONCEPTOS
		$sql   = "SELECT dias_laborados_empleado,id_empleado,documento_empleado,nombre_empleado
					 FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
		$query = mysql_query($sql,$link);
		$whereIdEmpleados='';
		while ($row=mysql_fetch_array($query)) {
			$arrayEmpleadosDatos[$row['id_empleado']] = array(
															'documento_empleado' => $row['documento_empleado'],
															'nombre_empleado' => $row['nombre_empleado'],
															);
			$whereIdEmpleados.=($whereIdEmpleados=='')? 'id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'];
			// $sql   = "UPDATE nomina_planillas_empleados_conceptos SET dias_laborados=$row[dias_laborados_empleado],saldo_dias_laborados=$row[dias_laborados_empleado]
			// WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla AND id_empleado=$row[id_empleado] ";
			// $query_update = mysql_query($sql,$link);
		}

		// ACTUALIZAR EL SALDO DE LOS DIAS LABORADOS DE LOS EMPLEADOS
		$sql="UPDATE nomina_planillas_empleados_conceptos AS NPEC,
					 (
						SELECT
							dias_laborados_empleado,
							id_empleado
						FROM
							nomina_planillas_empleados
						WHERE
							activo = 1
						AND id_empresa = $id_empresa
						AND id_planilla = $id_planilla
					) AS NPE
					SET dias_laborados = NPE.dias_laborados_empleado,
					 saldo_dias_laborados = NPE.dias_laborados_empleado
					WHERE
						NPEC.activo = 1
					AND NPEC.id_empresa = $id_empresa
					AND NPEC.id_planilla = $id_planilla
					AND NPEC.id_empleado = NPE.id_empleado";
		$query=mysql_query($sql,$link);

		$tiempo_fin = microtime_float();
		$tiempo = $tiempo_fin - $tiempo_inicio;
  		 echo "<script>console.log('Tiempo empleado: ".($tiempo_fin - $tiempo_inicio)." ');</script>  ";

		//FINALIZAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR
		// administrarContratos('terminar',$id_planilla,$id_empresa,$link);

		// CONSULTAR LAS CUENTAS PERMITIDAS EN EL COMPROBANTE DE EGRESO
		$sql   = "SELECT cuenta FROM configuracion_comprobante_egreso WHERE activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		$i     = 0;

    	while ($row=mysql_fetch_array($query)) {
    		$i++;
    		$arrayCuentaComprobante[$i]=$row['cuenta'];
    	}

    	// SELECT valor_concepto,
    	// 			id_tercero,
    	// 			id_empleado_cruce,
    	// 			id_tercero_contrapartida,
    	// 			id_concepto,caracter,
    	// 			caracter_contrapartida,
    	// 			cuenta_colgaap,
    	// 			cuenta_niif,
    	// 			cuenta_contrapartida_colgaap,
    	// 			cuenta_contrapartida_niif,
    	// 			id_centro_costos,
    	// 			id_centro_costos_contrapartida,
    	// 			naturaleza,
    	// 			id_prestamo
    	// 		FROM nomina_planillas_empleados_conceptos
    	// 		WHERE activo=1
    	// 			AND id_planilla=$id_planilla
    	// 			AND id_empresa=$id_empresa

    	// CONSULTAR LOS DOCUENTOS DE PRESTAMOS VIGENTES DE LOS EMPLEADOS
    	$sql="SELECT id,id_comprobante_egreso,consecutivo_comprobante_egreso,id_documento_cruce,numero_documento_cruce,tipo_documento_cruce
    			FROM nomina_prestamos_empleados
    			WHERE activo=1 AND id_empresa=$id_empresa AND valor_prestamo_restante>0 AND ($whereIdEmpleados)";
    	$query=mysql_query($sql,$link);
    	while ($row=mysql_fetch_array($query)) {
    		// $arrayPrestamos[$row['id']] = array(
				// 	              'id_prestamo' 			     => $row['id'],
    		// 								'id_documento_cruce' 	   => $row['id_documento_cruce'],
				// 								'numero_documento_cruce' => $row['numero_documento_cruce'],
				// 								'tipo_documento_cruce'   => $row['tipo_documento_cruce'] );

				$arrayPrestamos[$row['id']] = array(
					              'id_prestamo' 			     => $row['id'],
    										'id_documento_cruce' 	   => ($row['id_documento_cruce'] == "0")? "" : $row['id_documento_cruce'],
												'numero_documento_cruce' => ($row['numero_documento_cruce'] == " ")? "" : $row['numero_documento_cruce'],
												'tipo_documento_cruce'   => ($row['tipo_documento_cruce'] == "false")? "" : $row['tipo_documento_cruce']);
    	}

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
				NPEC.id_planilla = $id_planilla
			AND NPEC.id_empresa = $id_empresa
			AND NC.id=NPEC.id_concepto
			AND NC.id_empresa=$id_empresa";
    	$query=mysql_query($sql,$link);

    	while ($row=mysql_fetch_array($query)){

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
			$id_ccos                         = $row['id_centro_costos'];
			$id_ccos_contrapartida           = $row['id_centro_costos_contrapartida'];

			// echo "<script>console.log('".$arrayEmpleadosDatos[$id_empleado_nomina]['nombre_empleado']."');</script>";
			// echo "<script>console.log('centro_costos = $centro_costos - id_ccos = $id_ccos ');</script>";
			// echo "<script>console.log('$concepto');</script>";
			// echo "<script>console.log('centro_costos_contrapartida = $centro_costos_contrapartida - id_ccos_contrapartida = $id_ccos_contrapartida ');</script>";

			// VALIDAR SI EL CONCEPTO SE CREO CON CRUCE CON UNA ENTIDAD ENTONCES QUE EL ID SEA DIFERENTE AL DEL CLIENTE
			if ($tercero=='Entidad' && $id_empleado_cruce==$id_empleado) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado_nomina]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}
			if ($tercero_cruce=='Entidad' && $id_empleado_cruce==$id_empleado_contrapartida) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado_nomina]['nombre_empleado'].'\nEsta configurado para una entidad pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione una entidad al concepto desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}

			// VALIDAR SI EL CONCEPTO NECESITA CENTRO DE COSTOS Y SI NO SE CREO EL CCOS EN EL CONTRATO DEL EMPLEADO
			if ($centro_costos=='true' && ($id_ccos =='' || $id_ccos ==0) ) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado_nomina]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						console.log("'.$cuenta_colgaap.'");
					</script>';
				exit;
			}
			if ($centro_costos_contrapartida=='true' && ($id_ccos_contrapartida=='' || $id_ccos_contrapartida==0 ) ) {
				echo '<script>
						alert("Error!\nEl Concepto: '.$concepto.' del empleado:\n'.$arrayEmpleadosDatos[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado_nomina]['nombre_empleado'].'\nEsta configurado para un centro de costos pero el empleado no la relaciona!\nDirijase al modulo de empleados y relacione un centro de costos desde el contrato del empleado");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						console.log("'.$cuenta_contrapartida_colgaap.'");
					</script>';
				exit;
			}

			// VALIDAR QUE EXISTA COMO TERCERO EL EMPLEADO
			if ($id_empleado=='' || $id_empleado_contrapartida=='') {
				echo '<script>
						alert("'.$arrayEmpleadosDatos[$id_empleado_nomina]['documento_empleado'].' - '.$arrayEmpleadosDatos[$id_empleado_nomina]['nombre_empleado'].' \nNo esta creado como tercero!\nVerifiquelos dede el modulo de empleados");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
				exit;
			}



			// ARRAY CON LOS VALORES PARA LOS ASIENTOS COLGAAP
			// $arrayAsientosColgaap[$id_empleado][$cuenta_colgaap]['ccos']                                              = $row['id_centro_costos'];
			// $arrayAsientosColgaap[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap]['ccos']                  = $row['id_centro_costos_contrapartida'];

			// $arrayAsientosColgaap[$id_empleado][$cuenta_colgaap][$id_ccos]
			// $arrayAsientosColgaap[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$id_ccos_contrapartida]

			if ($arrayPrestamos[$id_prestamo]['id_prestamo']>0) {
				$arrayAsientosColgaap[$id_empleado][$cuenta_colgaap][$id_ccos]['doc_cruce'] = array(	'id_documento_cruce'     => $arrayPrestamos[$id_prestamo]['id_documento_cruce'],
																										'numero_documento_cruce' => $arrayPrestamos[$id_prestamo]['numero_documento_cruce'],
																										'tipo_documento_cruce'   => $arrayPrestamos[$id_prestamo]['tipo_documento_cruce'], );

				$arrayAsientosColgaap[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$id_ccos_contrapartida]['doc_cruce'] = array(	'id_documento_cruce'     => $arrayPrestamos[$id_prestamo]['id_documento_cruce'],
																																				'numero_documento_cruce' => $arrayPrestamos[$id_prestamo]['numero_documento_cruce'],
																																				'tipo_documento_cruce'   => $arrayPrestamos[$id_prestamo]['tipo_documento_cruce'], );

				$arrayAsientosNiif[$id_empleado][$cuenta_niif][$id_ccos]['doc_cruce'] = array(	'id_documento_cruce'     => $arrayPrestamos[$id_prestamo]['id_documento_cruce'],
																								'numero_documento_cruce' => $arrayPrestamos[$id_prestamo]['numero_documento_cruce'],
																								'tipo_documento_cruce'   => $arrayPrestamos[$id_prestamo]['tipo_documento_cruce'], );

				$arrayAsientosNiif[$id_empleado_contrapartida][$cuenta_contrapartida_niif][$id_ccos_contrapartida]['doc_cruce']  = array(	'id_documento_cruce'     => $arrayPrestamos[$id_prestamo]['id_documento_cruce'],
																																			'numero_documento_cruce' => $arrayPrestamos[$id_prestamo]['numero_documento_cruce'],
																																			'tipo_documento_cruce'   => $arrayPrestamos[$id_prestamo]['tipo_documento_cruce'], );

			}



			$arrayAsientosColgaap[$id_empleado][$cuenta_colgaap][$id_ccos][$caracter]                                           += $row['valor_concepto'];
			$arrayAsientosColgaap[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$id_ccos_contrapartida][$caracter_contrapartida] += $row['valor_concepto'];


			// ARRAY CON LOS VALORES PARA LOS ASIENTOPS NIIF
			// $arrayAsientosNiif[$id_empleado][$cuenta_niif]['ccos']                                              = $row['id_centro_costos'];
			// $arrayAsientosNiif[$id_empleado_contrapartida][$cuenta_contrapartida_niif]['ccos']                  = $row['id_centro_costos_contrapartida'];

			$arrayAsientosNiif[$id_empleado][$cuenta_niif][$id_ccos][$caracter]                                           += $row['valor_concepto'];
			$arrayAsientosNiif[$id_empleado_contrapartida][$cuenta_contrapartida_niif][$id_ccos_contrapartida][$caracter_contrapartida] += $row['valor_concepto'];

			// ARRAY CON LOS VALORES ACUMULADOS DE LOS CONCEPTOS, CON 4 CAPAS, ID_TERCERO, EMPLEADO QUE CRUZA,CUENTA Y SI ES DEBITO O CREDITO
			// $arrayNominaContabilizacion[$id_empleado][$id_empleado_cruce][$cuenta_colgaap][$caracter]                                           += $row['valor_concepto'];
			// $arrayNominaContabilizacion[$id_empleado_contrapartida][$id_empleado_cruce][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += $row['valor_concepto'];

			// if ($row['concepto_ajustable']<>'true') {
				$arrayNominaContabilizacion[$id_empleado][$cuenta_colgaap][$caracter]                                           += $row['valor_concepto'];
				$arrayNominaContabilizacion[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] += $row['valor_concepto'];
			// }

			// ARRAY CON LA INFORMACION DEL CONCEPTO
			$arrayInfoConcepto[$id_empleado][$cuenta_colgaap]= array(
																	'concepto'           => $row['concepto'],
																	'naturaleza'         => $row['naturaleza'],
																	'concepto_ajustable' => $row['concepto_ajustable'],
																	);

			$arrayInfoConcepto[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap]= array(
																								'concepto'           => $row['concepto'],
																								'naturaleza'         => $row['naturaleza'],
																								'concepto_ajustable' => $row['concepto_ajustable'],
																								);

			$arrayConceptoAjuste[$id_empleado][$cuenta_colgaap][$caracter]                                           = array('concepto_ajustable' => $row['concepto_ajustable'] );
			$arrayConceptoAjuste[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap][$caracter_contrapartida] = array('concepto_ajustable' => $row['concepto_ajustable'] );

			// NATURALEZA DE LA CUENTA (DEVENGO,DEDUCCION,APROPIACION,PROVISION), INDICE EL TERCERO Y LA CUENTA COLGAAP
			$arrayNaturaleza[$id_empleado][$cuenta_colgaap]                             = $row['naturaleza'];
			$arrayNaturaleza[$id_empleado_contrapartida][$cuenta_contrapartida_colgaap] = $row['naturaleza'];

			// ARRAY CON LOS DEMAS DATOS DEL INSERT
			$arrayDatosInsert[$cuenta_colgaap]               = array('cuenta_niif'=>$cuenta_niif,'id_concepto'=>$row['id_concepto'] );
			$arrayDatosInsert[$cuenta_contrapartida_colgaap] = array('cuenta_niif'=> $cuenta_contrapartida_niif,'id_concepto'=>$row['id_concepto'] );

    	}



    	//ACTUALIZAR EL DOCUMENTO
    	$fecha_generacion=date("Y-m-d");
		$sql   = "UPDATE nomina_planillas SET estado=1,fecha_generacion='$fecha_generacion' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query = mysql_query($sql,$link);

		if (!$query) {
			echo '<script>
					alert("Error\nNo se actualizo la planilla, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		//CONSULTAR EL CONSECUTIVO DEL DOCUMENTO
		$sql   = "SELECT consecutivo,fecha_documento,fecha_inicio,fecha_final,id_sucursal FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
		$query = mysql_query($sql,$link);
		$consecutivo     = mysql_result($query,0,'consecutivo');
		$fecha_documento = mysql_result($query,0,'fecha_documento');
		$fecha_inicio    = mysql_result($query,0,'fecha_inicio');
		$fecha_final     = mysql_result($query,0,'fecha_final');
		$id_sucursal     = mysql_result($query,0,'id_sucursal');


    	// foreach ($arrayPrestamos as $id_prestamo => $arrayResul) {
    	// 	moverSaldoPrestamos('eliminar',$id_planilla,$id_empresa,$link);
    	// }
		// print_r($arrayAsientosColgaap);exit;
   //  	echo json_encode($arrayAsientosColgaap);
   //  	echo '<script>
			// 		// alert("Error\nLos asientos contables tienen una diferencia de '.($acumDebito - $acumCredito).' ");
			// 		// console.log(" colgaap '.$acumDebito.' - '.$acumCredito.' = '.($acumDebito - $acumCredito).' ");
			// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			// 	</script>';
			// exit;

    	//RECORRER EL ARRAY PARA CREAR EL INSERT COLGAAP
    	foreach ($arrayAsientosColgaap as $id_tercero => $arrayResul) {
    		foreach ($arrayResul as $cuenta => $arrayResul1) {
    			foreach ($arrayResul1 as $id_centro_costos => $arrayResul2) {
	    			// $id_centro_costos=$arrayResul1['ccos'];

	    			if ($arrayResul2['debito']>$arrayResul2['credito']) {
						$debito  = $arrayResul2['debito']-$arrayResul2['credito'];
						$credito = 0;
	    			}
	    			else{
						$debito  = 0;
						$credito = $arrayResul2['credito']-$arrayResul2['debito'];
	    			}

	    			if ($debito==0 && $credito==0) { continue; }

					$id_documento_cruce     = ($arrayResul2['doc_cruce']['id_documento_cruce']<>'')? $arrayResul2['doc_cruce']['id_documento_cruce'] : $id_planilla ;
					$numero_documento_cruce = ($arrayResul2['doc_cruce']['numero_documento_cruce']<>'')? $arrayResul2['doc_cruce']['numero_documento_cruce'] : $consecutivo ;
					$tipo_documento_cruce   = ($arrayResul2['doc_cruce']['tipo_documento_cruce']<>'')? $arrayResul2['doc_cruce']['tipo_documento_cruce'] : 'LN' ;

					$valueInsertAsientos .= "('$id_planilla',
											'$consecutivo',
											'LN',
											'$id_documento_cruce',
											'$numero_documento_cruce',
											'$tipo_documento_cruce',
											'Liquidacion Nomina',
											'".$fecha_documento."',
											'".$debito."',
											'".$credito."',
											'".$cuenta."',
											'".$id_tercero."',
											'$id_centro_costos',
											'$id_sucursal',
											'$id_empresa'),";
					$acumDebito  += $debito;
					$acumCredito += $credito;
				}
    		}
    	}

		$acumDebito  = round($acumDebito,$_SESSION['DECIMALESMONEDA']);
		$acumCredito = round($acumCredito,$_SESSION['DECIMALESMONEDA']);

    	if ($acumDebito - $acumCredito) {
			echo '<script>
					alert("Error\nLos asientos contables tienen una diferencia de '.($acumDebito - $acumCredito).' ");
					console.log(" colgaap '.$acumDebito.' - '.$acumCredito.' = '.($acumDebito - $acumCredito).' ");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

    	// echo json_encode($arrayNominaContabilizacion);
   //  	echo '<script>
			// 		// alert("Error\nLos asientos contables tienen una diferencia de '.($acumDebito - $acumCredito).' ");
			// 		// console.log(" colgaap '.$acumDebito.' - '.$acumCredito.' = '.($acumDebito - $acumCredito).' ");
			// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			// 	</script>';
			// exit;
    	// exit;
		$acumDebito  = 0;
		$acumCredito = 0;
    	//RECORRER EL ARRAY PARA CREAR EL INSERT NIIF
    	foreach ($arrayAsientosNiif as $id_empleado => $arrayResul) {
    		foreach ($arrayResul as $cuenta => $arrayResul1) {
    			foreach ($arrayResul1 as $id_centro_costos => $arrayResul2) {
	    			// $id_centro_costos=$arrayResul1['ccos'];
					if ($arrayResul2['debito']>$arrayResul2['credito']) {
						$debito  = $arrayResul2['debito']-$arrayResul2['credito'];
						$credito = 0;
	    			}
	    			else{
						$debito  = 0;
						$credito = $arrayResul2['credito']-$arrayResul2['debito'];
	    			}

	    			if ($debito==0 && $credito==0) { continue; }

	    			$id_documento_cruce     = ($arrayResul2['doc_cruce']['id_documento_cruce']<>'')? $arrayResul2['doc_cruce']['id_documento_cruce'] : $id_planilla ;
					$numero_documento_cruce = ($arrayResul2['doc_cruce']['numero_documento_cruce']<>'')? $arrayResul2['doc_cruce']['numero_documento_cruce'] : $consecutivo ;
					$tipo_documento_cruce   = ($arrayResul2['doc_cruce']['tipo_documento_cruce']<>'')? $arrayResul2['doc_cruce']['tipo_documento_cruce'] : 'LN' ;

					$valueInsertAsientosNiif .= "('$id_planilla',
													'$consecutivo',
													'LN',
													'$id_documento_cruce',
													'$numero_documento_cruce',
													'$tipo_documento_cruce',
													'Liquidacion Nomina',
													'".$fecha_documento."',
													'".$debito."',
													'".$credito."',
													'".$cuenta."',
													'".$id_empleado."',
													'$id_centro_costos',
													'$id_sucursal',
													'$id_empresa'),";
					$acumDebito  += $debito;
					$acumCredito += $credito;
				}
    		}
    	}

    	$acumDebito  = round($acumDebito,$_SESSION['DECIMALESMONEDA']);
		$acumCredito = round($acumCredito,$_SESSION['DECIMALESMONEDA']);

    	if ($acumDebito - $acumCredito) {
			echo '<script>
					alert("Error\nLos asientos contables niif tienen una diferencia de '.($acumDebito - $acumCredito).' ");
					console.log(" niif '.$acumDebito.' - '.$acumCredito.' = '.($acumDebito - $acumCredito).' ");
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

    			// RECORRER LA CONFIGURACION DEL COMPROBANTE
				for ($j=1; $j <= $i; $j++) {
					// if ($arrayInfoConcepto[$id_tercero][$cuenta]['concepto_ajustable']=='true') {
					// 	continue;
					// }

					if ($arrayConceptoAjuste[$id_tercero][$cuenta]['credito']['concepto_ajustable']=='true'  ) {
						continue;
					}

					// $total_sin_abono=($arrayNaturaleza[$id_tercero][$cuenta]=='Provision')? 0 : abs($debito-$credito);
					// $total_sin_abono_provision=($arrayNaturaleza[$id_tercero][$cuenta]=='Provision')? abs($debito-$credito) : 0;

					$total_sin_abono = ($arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza']=='Provision')? 0 : abs($debito-$credito);
					$total_sin_abono_provision = ($arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza']=='Provision')? abs($debito-$credito) : 0;

					// echo '<script>console.log("'.$arrayInfoConcepto[$id_tercero][$cuenta]['concepto'].' '.$arrayInfoConcepto[$id_tercero][$cuenta]['naturaleza'].' '.$total_sin_abono.' - '.$total_sin_abono_provision.' ");</script>';

    				if (strpos($cuenta, $arrayCuentaComprobante[$j])==0) {
    				    $valueInsertConfiguracion .= "('$id_tercero',
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

    	// INSERTAR LA CONFIGURACION PARA EL COMPROBANTE
		$valueInsertConfiguracion = substr($valueInsertConfiguracion, 0, -1);

		$sql   = "INSERT INTO nomina_planillas_empleados_contabilizacion
					(id_tercero,id_planilla,consecutivo_planilla,id_concepto,cuenta_colgaap,cuenta_niif,debito,credito,total_sin_abono,total_sin_abono_provision,fecha_inicio_planilla,fecha_final_planilla,id_sucursal,id_empresa)
					VALUES $valueInsertConfiguracion";
		$query = mysql_query($sql,$link);

		if (!$query) {
			$sql   = "UPDATE nomina_planillas SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query = mysql_query($sql,$link);
			echo '<script>
					alert("Error\nNo se inserto la configuracion contable");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$valueInsertAsientos     = substr($valueInsertAsientos, 0, -1);
		$valueInsertAsientosNiif = substr($valueInsertAsientosNiif, 0, -1);

		//INSERT COLGAAP
    	$sqlColgaap = "INSERT INTO asientos_colgaap(
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
		$queryColgaap = mysql_query($sqlColgaap,$link);

		if (!$queryColgaap) {

			moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
			$sql   = "UPDATE nomina_planillas SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query = mysql_query($sql,$link);

			echo '<script>
					alert("Error\nNo se insertaron los asientos Colgaap");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		// CUENTAS SIMULTANEAS DE LAS CUENTAS DEL DOCUMENTO
		contabilizacionSimultanea($id_planilla,'LN',$id_sucursal,$id_empresa,$link);

		//INSERT NIIF
		$sqlNiif = "INSERT INTO asientos_niif(
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
		$queryNiif = mysql_query($sqlNiif,$link);

		if (!$queryNiif) {
			$sql   = "UPDATE nomina_planillas SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_planilla";
			$query = mysql_query($sql,$link);

			moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
			echo '<script>
					alert("Error\nNo se insertaron los asientos Niif");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','LN','Planilla de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

		// GENERAR EL MOVIMIENTO DE LOS PRESTAMOS
    	moverSaldoPrestamos('eliminar',$id_planilla,$id_empresa,$link);

		echo '<script>
				Ext.get("contenedor_NominaPlanilla").load({
					url     : "planilla/bd/grillaContableBloqueada.php",
					scripts : true,
					nocache : true,
					params  :
					{
						id_planilla       : "'.$id_planilla.'",
						opcGrillaContable : "NominaPlanilla",
					}
				});
				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
			</script>';
    }


    //==================== FUNCION PAR ADMINISTRAR LOS CONTRATOS ==========================//
    function moverSaldoPrestamos($accion,$id_planilla,$id_empresa,$link){

    	if ($accion=='eliminar') {
    		$sql = "UPDATE nomina_prestamos_empleados,
					(
						SELECT valor_concepto,id_prestamo,id_empleado AS id_empleado_cruce
						FROM nomina_planillas_empleados_conceptos
						WHERE activo = 1
							AND id_empresa = $id_empresa
							AND id_planilla = $id_planilla
							AND id_prestamo > 0
					) AS NPEC
					SET valor_prestamo_restante=IF((valor_prestamo_restante-NPEC.valor_concepto)<=0,0,valor_prestamo_restante-NPEC.valor_concepto),
						cuotas_restantes=cuotas_restantes-1
					WHERE activo=1
						AND id_empresa=$id_empresa
						AND id_empleado=NPEC.id_empleado_cruce
						AND id=NPEC.id_prestamo";
    	}
    	else if ($accion=='agregar') {
    		// cuotas_restantes=IF((cuotas_restantes+1)>cuotas,cuotas,cuotas_restantes+1)
    		$sql = "UPDATE nomina_prestamos_empleados,
					(
						SELECT valor_concepto,id_prestamo,id_empleado AS id_empleado_cruce
						FROM nomina_planillas_empleados_conceptos
						WHERE activo = 1
							AND id_empresa = $id_empresa
							AND id_planilla = $id_planilla
							AND id_prestamo > 0
					) AS NPEC
					SET valor_prestamo_restante=IF((valor_prestamo_restante+NPEC.valor_concepto)<=0,0,valor_prestamo_restante+NPEC.valor_concepto),
						cuotas_restantes=cuotas_restantes+1
					WHERE activo=1
						AND id_empresa=$id_empresa
						AND id_empleado=NPEC.id_empleado_cruce
						AND id=NPEC.id_prestamo";
    	}

    	$query = mysql_query($sql,$link);
    	if (!$query) {
			echo '<script>
					alert("Error\nNo se actualizaron los saldo de los prestamos!\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
			exit;
		}
    }

    //==================== FUNCION PAR ADMINISTRAR LOS CONTRATOS ==========================//
    function administrarContratos($accion,$id_planilla,$id_empresa,$link){
    	//FINALIZAR LOS CONTRATOS
		$sql   = "SELECT id_empleado, id_contrato,terminar_contrato FROM nomina_planillas_empleados WHERE activo=1 ANd id_planilla=$id_planilla AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		$where = '';
		while ($row=mysql_fetch_array($query)) {
			if ($row['terminar_contrato']=='Si') {
				$where.=($where=='')? ' (id='.$row['id_contrato'].' AND id_empleado='.$row['id_empleado'].') ' : ' OR (id='.$row['id_contrato'].' AND id_empleado='.$row['id_empleado'].') ' ;
			}
		}

		if ($accion=='terminar' && $where!='') {
			$sql   = "UPDATE empleados_contratos SET estado=1 WHERE activo=1 AND id_empresa=$id_empresa AND ($where)";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizaron los Contrato para terminarlos\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
			}
		}
		else if ($accion=='renovar' && $where!='') {
			$sql   = "UPDATE empleados_contratos SET estado=0 WHERE activo=1 AND id_empresa=$id_empresa AND ($where)";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizaron los Contrato para renovarlos\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
			}
		}
    }

    //==================== MOVER LAS CUENTAS DE LA PLANILLA ==============================//
    function moverCuentasPlanilla($accion,$id_planilla,$id_empresa,$link){
    	if ($accion=='eliminar') {
    		//ASIENTOS COLGAAP
			$sqlPlanilla   = "DELETE FROM nomina_planillas_empleados_contabilizacion WHERE id_empresa='$id_empresa' AND id_planilla='$id_planilla' AND tipo_planilla='LN' ";
			$queryPlanilla = mysql_query($sqlPlanilla,$link);
    		//ASIENTOS COLGAAP
			$sqlColgaap   = "DELETE FROM asientos_colgaap WHERE id_empresa='$id_empresa' AND id_documento='$id_planilla' AND tipo_documento='LN'";
			$queryColgaap = mysql_query($sqlColgaap,$link);
    		//ASIENTOS NIIF
			$sqlNiif   = "DELETE FROM asientos_niif WHERE id_empresa='$id_empresa' AND id_documento='$id_planilla' AND tipo_documento='LN'";
			$queryNiif = mysql_query($sqlNiif,$link);

    		if (!$queryPlanilla) {
    			echo '<script>
    					alert("Error\nNo se Eliminaron los registros de planillas empleados contabilizacion.");
    					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
    			exit;
    		}
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
    	//VALIDAR QUE NO TENGA DOCUMENTO CRUCE REALCIONADOS
    	validaDocumentoCruce($id_planilla,$id_empresa,$link);
    	// RETORNAR EL VALOR DE LOS PRESTAMOS
    	moverSaldoPrestamos('agregar',$id_planilla,$id_empresa,$link);
    	//MOVER LAS CUENTAS DEL DOCUMENTO
    	moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
    	//RENOVAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR
		// administrarContratos('renovar',$id_planilla,$id_empresa,$link);

		$sql   = "UPDATE nomina_planillas SET estado=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
		$query = mysql_query($sql,$link);

    	if ($query) {
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

    		//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','LN','Planilla de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);
    		echo '<script>
					Ext.get("contenedor_NominaPlanilla").load({
						url     : "planilla/grillaPlanilla.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_planilla       : "'.$id_planilla.'",
							opcGrillaContable : "NominaPlanilla",
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

		$sql    = "SELECT estado,consecutivo FROM nomina_planillas WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa'";
		$query  = mysql_query($sql,$link);
		$estado = mysql_result($query,0,'estado');
		$consecutivo = mysql_result($query,0,'consecutivo');

		// STRING PARA ACTUALIZAR A 0 EL ESTADO DE LA PLANILLA
		$sql = "UPDATE nomina_planillas SET estado=3 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
		$opc_funcion = '';

   		if ($estado==3) {
   			echo '<script>
					alert("La planilla ya esta cancelada");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
		else if ($estado==0 && ($consecutivo==0 || $consecutivo=='') ) {
			$sql="UPDATE nomina_planillas SET activo=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa'";
			$opc_funcion = 'delete';
		}
		// SI LA PLANILLA ESTA GENERADA O CRUZADA
		else if ($estado<>0) {
			// RETORNAR EL VALOR DE LOS PRESTAMOS
    		moverSaldoPrestamos('agregar',$id_planilla,$id_empresa,$link);
   			//RENOVAR LOS CONTRATOS QUE SE TIENEN PARA TERMINAR
			// administrarContratos('renovar',$id_planilla,$id_empresa,$link);
    		moverCuentasPlanilla('eliminar',$id_planilla,$id_empresa,$link);
    	}

		$query = mysql_query($sql,$link);
    	if ($query) {
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

    		//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						       VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','LN','Planilla de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);
    		echo '<script>
					// Ext.get("contenedor_NominaPlanilla").load({
					// 	url     : "planilla/bd/grillaContableBloqueada.php",
					// 	scripts : true,
					// 	nocache : true,
					// 	params  :
					// 	{
					// 		id_planilla       : "'.$id_planilla.'",
					// 		opcGrillaContable : "NominaPlanilla",
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

		$sql   = "UPDATE nomina_planillas SET estado=0 WHERE activo=1 AND id='$id_planilla' AND id_empresa='$id_empresa' ";
		$query = mysql_query($sql,$link);
    	if ($query) {
				$fecha_actual = date('Y-m-d');
				$hora_actual  = date('H:i:s');

    		//INSERTAR EL LOG DE EVENTOS
				$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
							     VALUES($id_planilla,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','LN','Planilla de Nomina',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
				$queryLog = mysql_query($sqlLog,$link);
    		echo '<script>
					Ext.get("contenedor_NominaPlanilla").load({
						url     : "planilla/grillaPlanilla.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_planilla       : "'.$id_planilla.'",
							opcGrillaContable : "NominaPlanilla",
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
		$sql             = "SELECT valor_concepto,naturaleza FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
		$query           = mysql_query($sql,$link);
		$acumDevengo     = 0;
		$acumDeduce      = 0;
		$acumApropiacion = 0;
		$acumProvision   = 0;
   		while ($row=mysql_fetch_array($query)) {

   			if ($row['naturaleza']=='Devengo') { $acumDevengo += $row['valor_concepto']; }
   			else if ($row['naturaleza']=='Deduccion') { $acumDeduce += $row['valor_concepto']; }
   			else if ($row['naturaleza']=='Apropiacion') { $acumApropiacion += $row['valor_concepto']; }
   			else if ($row['naturaleza']=='Provision') { $acumProvision += $row['valor_concepto']; }
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

		$observacion = str_replace("[\n|\r|\n\r]", '<br>', $observacion);

		$sql   = "UPDATE nomina_planillas SET  observacion='$observacion' WHERE id='$id' AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		if($query){ echo 'true'; }
		else{ echo'false'; }
	}

	//===================== FUNCION PARA ENVIAR LOS VOLANTES DE NOMINA A TODOS LOS EMPLEADOS =====================//
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
		if(isset($TAM)){ $HOJA = $TAM; }
		else{ $HOJA = 'LETTER'; }

		if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
		if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
		if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
		else{ $MS=($terminar_contrato=='Si')? 65 : 62 ; $MD = 10;$MI = 10;$ML = 10; }		//con imagen ms=86 sin imagen ms=71

		if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

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
		$sql   = "SELECT id_empleado,id_contrato,nombre_empleado FROM nomina_planillas_empleados WHERE activo=1 AND id_planilla=$id_planilla AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);

		while ($row=mysql_fetch_array($query)) {
			$empleado    = $row['nombre_empleado'];
			$id_empleado = $row['id_empleado'];
			$id_contrato = $row['id_contrato'];

			//====================== LLAMAR LA FUNCION PARA EL ENVIO =============================//
			for ($i=0; $i <50 ; $i++) { $fun=imprimirEnviaVolante($i,$id_planilla,$id_empleado,$id_contrato,$mail,$mpdf,$link); }
		}

		echo'<script>
				document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
			</script>';
	}

	//==========================  FUNCION PARA ENVIAR EL VOLANTE A UN SOLO EMPLEADO =================================//
	function enviarVolanteUnicoEmpleado($id_planilla,$id_contrato,$id_empleado,$id_empresa,$link){
		include_once('../../../../misc/phpmailer/PHPMailerAutoload.php');
		include("../../../../misc/MPDF54/mpdf.php");
		include('enviar_volante_email.php');

		$mail  = new PHPMailer();

		$sqlConexion   = "SELECT * FROM empresas_config_correo WHERE id_empresa=$id_empresa LIMIT 0,1";
		$queryConexion = mysql_query ($sqlConexion,$link);
		if($row_consulta= mysql_fetch_array($queryConexion)){
			$seguridad     = $row_consulta['seguridad_smtp'];
			// $pass       = $row_consulta['password'];
			$pass          = $row_consulta['password'];
			// $user       = $row_consulta['user_name'];
			$user          = $row_consulta['correo'];
			$puerto        = $row_consulta['puerto'];
			// $servidor   = $row_consulta['servidor_SMTP'];
			$servidor      = $row_consulta['servidor'];
			// $from       = $row_consulta['from'];
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
		$mail->Subject    = "Volante de Pago de Nomina";
		$mail->AltBody    = "This is the body when user views in plain text format"; //Text Body
		$mail->WordWrap   = 50; // set word wrap

		if(isset($TAM)){ $HOJA = $TAM; }
		else{ $HOJA = 'LETTER'; }

		if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
		if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
		if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

		if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
		else{ $MS=($terminar_contrato=='Si')? 65 : 62 ; $MD = 10; $MI = 10;$ML = 10; }		//con imagen ms=86 sin imagen ms=71

		if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

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
			$sql   = "UPDATE nomina_planillas_empleados SET email_enviado='true' WHERE activo=1 AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND id_empleado=$id_empleado AND id_empresa=$id_empresa";
			$query = mysql_query($sql,$link);
			unlink('volante_nomina.pdf');
			echo '<script>
				document.getElementById("imgEmail_'.$id_contrato.'").setAttribute("src","img/enviaremail_true.png");
				document.getElementById("imgEmail_'.$id_contrato.'").setAttribute("title","Reenviar Volante por email");
				document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
			</script>';
		}
		else if($fun == "false"){
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
		else{
			echo '<script>
							alert("Error\nEl empleado no tiene correo configurado.");
							document.getElementById("divPadreModal").parentNode.removeChild(document.getElementById("divPadreModal"));
						</script>';
		}
	}

	//FUNCION PARA VALIDAR QUE NO TENGA UN DOCUMENTO CREUCE RELACIONADO
	function validaDocumentoCruce($idDocumento,$id_empresa,$link){
		$id_sucursal = $_SESSION['SUCURSAL'];

		//CONSULTAR SI HAY PLANILLAS DE AJUSTE EN ESE PERIODO
		$sql = "SELECT fecha_inicio,fecha_final FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$idDocumento;";
		$query = mysql_query($sql,$link);
		$fecha_inicio = mysql_result($query,0,'fecha_inicio');
		$fecha_final  = mysql_result($query,0,'fecha_final');

		// $sql="SELECT COUNT(id) AS planillas FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND fecha_inicio>='$fecha_inicio' AND estado=1 ";
		// $query=mysql_query($sql,$link);
		// $planillas = mysql_result($query,0,'planillas');

		// if ($planillas >= 0) {
		// 	echo '<script>
		// 			alert("Error!\nExisten planilla(s) de ajuste creadas en ese periodo, editelas y asi podra modificar el documento");
		// 			document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
		// 		</script>';
		// 		exit;
		// }

		$sqlNota    = "SELECT consecutivo_documento, tipo_documento FROM asientos_colgaap WHERE id_documento_cruce = '$idDocumento' AND tipo_documento_cruce='LN' AND tipo_documento<>'LN' AND activo=1 AND id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' GROUP BY id_documento, tipo_documento";
		$queryNota  = mysql_query($sqlNota,$link);
		$doc_cruces = '';

		while($row=mysql_fetch_array($queryNota)) { $doc_cruces .= '\n* '.$row['tipo_documento'].' '.$row['consecutivo_documento']; }
		if ($doc_cruces != '') {
			echo '<script>
					alert("Error!\nEsta Planilla tienen relacionados los siguientes Documentos:\n'.$doc_cruces.'\n\nPor favor anule los documentos para poder modificar el documento");
					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				</script>';
				exit;
		}
	}

	//FUNCION PARA CALCULAR LA FORMULA DEL CONCEPTO
	function calcula_formula($equation){
    	if ($equation==''){ return round(0,$_SESSION['DECIMALESMONEDA']); }

        // Remove whitespaces
        $equation = preg_replace('/\s+/', '', $equation);
        // echo "$equation\n=";
        // echo 'alert("'.$equation.'"=)';

		$number    = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi|π)'; // What is a number
		$functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg|sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
		$operators = '[\/*\^\+-,]'; // Allowed math operators
		$regexp    = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

        if (preg_match($regexp, $equation)){
            $equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
            eval('$result = '.$equation.';');
        }
        else{ $result = false; }

        return round($result,$_SESSION['DECIMALESMONEDA']);
        // return $result;
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

		if ($cont1>0 || $cont2>0) {
			echo '<script>
					alert("Advertencia!\nEl documento toma un periodo que se encuentra cerrado, no podra realizar operacion alguna sobre ese periodo a no ser que edite el cierre");
					if (document.getElementById("modal")) {
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					}
				</script>';
			exit;
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
				('LN',$id_estructura,$id_planilla,$id_empleado,$id_concepto,'$data',$_SESSION[EMPRESA])";
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
