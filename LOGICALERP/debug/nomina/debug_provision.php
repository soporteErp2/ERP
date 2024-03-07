<?php
	error_reporting(E_ALL);
	include_once("../../../configuracion/conectar.php");
	include_once("../../../configuracion/define_variables.php");
	exit;
	// header('Content-type: application/vnd.ms-excel');
	// header("Content-Disposition: attachment; filename=nomina.xls");
	// header("Pragma: no-cache");
	// header("Expires: 0");

	$id_empresa = '48';

	$sql="SELECT
				id,
				codigo,
				descripcion,
				naturaleza,
				nivel_formula,
				formula,
				id_cuenta_colgaap,
				cuenta_colgaap,
				id_cuenta_niif,
				cuenta_niif,
				caracter,
				id_cuenta_contrapartida_colgaap,
				cuenta_contrapartida_colgaap,
				id_cuenta_contrapartida_niif,
				cuenta_contrapartida_niif,
				caracter_contrapartida
			FROM nomina_conceptos
			WHERE
				activo=1
			AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayConceptos[$row['id']]=array(
										'codigo'                          => $row['codigo'],
										'descripcion'                     => $row['descripcion'],
										'naturaleza'                      => $row['naturaleza'],
										'nivel_formula'                   => $row['nivel_formula'],
										'formula'                         => $row['formula'],
										'id_cuenta_colgaap'               => $row['id_cuenta_colgaap'],
										'cuenta_colgaap'                  => $row['cuenta_colgaap'],
										'id_cuenta_niif'                  => $row['id_cuenta_niif'],
										'cuenta_niif'                     => $row['cuenta_niif'],
										'caracter'                        => $row['caracter'],
										'id_cuenta_contrapartida_colgaap' => $row['id_cuenta_contrapartida_colgaap'],
										'cuenta_contrapartida_colgaap'    => $row['cuenta_contrapartida_colgaap'],
										'id_cuenta_contrapartida_niif'    => $row['id_cuenta_contrapartida_niif'],
										'cuenta_contrapartida_niif'       => $row['cuenta_contrapartida_niif'],
										'caracter_contrapartida'          => $row['caracter_contrapartida'],
										);
	}

	$sql="SELECT id,consecutivo,fecha_inicio,fecha_final,sucursal FROM nomina_planillas WHERE activo=1 AND (estado=1 OR estado = 2) AND id<>209 AND id_empresa = $id_empresa;";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdPlanillas.=($whereIdPlanillas=='')? 'id_planilla='.$row['id'] : ' OR id_planilla='.$row['id'] ;
		$arrayPlanillas[$row['id']] = array(
											'consecutivo'  => $row['consecutivo'],
											'fecha_inicio' => $row['fecha_inicio'],
											'fecha_final'  => $row['fecha_final'],
											'sucursal'     => $row['sucursal'],
											);
		$whereIdDocumentos.=($whereIdDocumentos=='')? 'id_documento='.$row['id'] : ' OR id_documento='.$row['id'] ;

	}

	$sql="SELECT id_planilla,id_empleado,documento_empleado, nombre_empleado,dias_laborados,dias_laborados_empleado
			FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPlanillas) AND id_empleado<>99 AND id_empleado<>348 AND id_empleado<>406 AND id_empleado<>407; ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdEmpleados.=($whereIdEmpleados=='')? 'id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
		$arrayEmpleadosPlanillas[$row['id_planilla']][$row['id_empleado']]=array(
																				'documento_empleado'      => $row['documento_empleado'],
																				'nombre_empleado'         => $row['nombre_empleado'],
																				'dias_laborados'          => $row['dias_laborados'],
																				'dias_laborados_empleado' => $row['dias_laborados_empleado'],
																				);
	}

	$sql="SELECT id,
				id_planilla,
				id_empleado,
				id_concepto,
				codigo_concepto,
				concepto,
				valor_concepto,
				id_contrato,
				naturaleza,
				id_cuenta_colgaap,
				cuenta_colgaap,
				id_cuenta_niif,
				cuenta_niif,
				caracter,
				id_tercero,
				id_cuenta_contrapartida_colgaap,
				cuenta_contrapartida_colgaap,
				id_cuenta_contrapartida_niif,
				cuenta_contrapartida_niif,
				caracter_contrapartida,
				id_tercero_contrapartida
			FROM nomina_planillas_empleados_conceptos
			WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdPlanillas) AND ($whereIdEmpleados) ORDER BY nivel_formula ASC;";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayConceptosPlanillas[$row['id_planilla']][$row['id_empleado']][$row['id_concepto']] = array(
																										'id'                              => $row['id'],
																										'codigo_concepto'                 => $row['codigo_concepto'],
																										'concepto'                        => $row['concepto'],
																										'valor_concepto'                  => $row['valor_concepto'],
																										'id_contrato'                     => $row['id_contrato'],
																										'naturaleza'                      => $row['naturaleza'],
																										'id_cuenta_colgaap'               => $row['id_cuenta_colgaap'],
																										'cuenta_colgaap'                  => $row['cuenta_colgaap'],
																										'id_cuenta_niif'                  => $row['id_cuenta_niif'],
																										'cuenta_niif'                     => $row['cuenta_niif'],
																										'caracter'                        => $row['caracter'],
																										'id_tercero'                      => $row['id_tercero'],
																										'id_cuenta_contrapartida_colgaap' => $row['id_cuenta_contrapartida_colgaap'],
																										'cuenta_contrapartida_colgaap'    => $row['cuenta_contrapartida_colgaap'],
																										'id_cuenta_contrapartida_niif'    => $row['id_cuenta_contrapartida_niif'],
																										'cuenta_contrapartida_niif'       => $row['cuenta_contrapartida_niif'],
																										'caracter_contrapartida'          => $row['caracter_contrapartida'],
																										'id_tercero_contrapartida'        => $row['id_tercero_contrapartida'],
																										);
	}

	$sql="SELECT id,salario_basico,id_centro_costos,codigo_centro_costos,nombre_centro_costos FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayContratos[$row['id']] = array(
											'salario_basico'   => $row['salario_basico'],
											'id_centro_costos' => $row['id_centro_costos'],
											);
	}

	$sql   = "SELECT id,id_documento,consecutivo_documento,fecha,debe,haber,codigo_cuenta,cuenta,id_tercero,nit_tercero,tercero FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdDocumentos)";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayColgaap[$row['id_documento']][$row['id_tercero']][$row['codigo_cuenta']] = array(
																								'id'                    => $row['id'],
																								'consecutivo_documento' => $row['consecutivo_documento'],
																								'fecha'                 => $row['fecha'],
																								'debe'                  => $row['debe'],
																								'haber'                 => $row['haber'],
																								'codigo_cuenta'         => $row['codigo_cuenta'],
																								'cuenta'                => $row['cuenta'],
																								'nit_tercero'           => $row['nit_tercero'],
																								'tercero'               => $row['tercero'],
																							);
	}

	$sql   = "SELECT id,id_documento,consecutivo_documento,fecha,debe,haber,codigo_cuenta,cuenta,id_tercero,nit_tercero,tercero FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdDocumentos)";
	$query = $mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$arrayNiif[$row['id_documento']][$row['id_tercero']][$row['codigo_cuenta']] = array(
																								'id'                    => $row['id'],
																								'consecutivo_documento' => $row['consecutivo_documento'],
																								'fecha'                 => $row['fecha'],
																								'debe'                  => $row['debe'],
																								'haber'                 => $row['haber'],
																								'codigo_cuenta'         => $row['codigo_cuenta'],
																								'cuenta'                => $row['cuenta'],
																								'nit_tercero'           => $row['nit_tercero'],
																								'tercero'               => $row['tercero'],
																							);
	}
	// print_r($arrayColgaap);
	$style = '';
	foreach ($arrayConceptosPlanillas as $id_planilla => $arrayConceptosPlanillasResul) {
		$body.='<tr class="head_planilla" >
					<td style="border-bottom:1px solid #FFF;">ID</td>
					<td style="border-bottom:1px solid #FFF;">CONSECUTIVO</td>
					<td style="border-bottom:1px solid #FFF;">FECHA INICIO</td>
					<td style="border-bottom:1px solid #FFF;">FECHA FINAL</td>
				</tr>
				<tr class="head_planilla">
					<td>'.$id_planilla.'</td>
					<td>'.$arrayPlanillas[$id_planilla]['consecutivo'].'</td>
					<td>'.$arrayPlanillas[$id_planilla]['fecha_inicio'].'</td>
					<td>'.$arrayPlanillas[$id_planilla]['fecha_final'].'</td>
				</tr>
					';

		foreach ($arrayConceptosPlanillasResul as $id_empleado => $arrayConceptosPlanillasResul2) {
			$body.='<tr class="head_empleado">
						<td>'.$arrayEmpleadosPlanillas[$id_planilla][$id_empleado]['documento_empleado'].'</td>
						<td>'.$arrayEmpleadosPlanillas[$id_planilla][$id_empleado]['nombre_empleado'].'</td>
						<td>DIAS LABORADOS</td>
						<td>SALARIO BASICO</td>
						<td>VALOR CONCEPTO</td>
						<td>VALOR CONCEPTO NEW</td>
						<td>NATURALEZA</td>
						<td>CUENTA</td>
						<td>DEBE</td>
						<td>HABER</td>
						<td>CUENTA CONTRAPARTIDA</td>
						<td>DEBE</td>
						<td>HABER</td>
					</tr>';

			foreach ($arrayConceptosPlanillasResul2 as $id_concepto => $arrayResul) {
				if ($arrayResul['naturaleza']<>'Provision') { continue; }
				$formula = $arrayConceptos[$id_concepto]['formula'];
				$formula = str_replace('{SC}', $arrayContratos[ $arrayResul['id_contrato'] ]['salario_basico'], $formula);
				$formula = str_replace('{DL}', $arrayEmpleadosPlanillas[$id_planilla][$id_empleado]['dias_laborados_empleado'], $formula);

				foreach ($arrayConceptosPlanillas[$id_planilla][$id_empleado] as $id_concepto_search => $arrayResul_search) {
					$formula = str_replace('['.$arrayResul_search['codigo_concepto'].']', $arrayConceptosPlanillas[$id_planilla][$id_empleado][$id_concepto_search]['valor_concepto'], $formula);

				}

				$formula        = reemplazarValoresFaltantes($formula);
				$valor_concepto = calcula_formula($formula);
				$arrayConceptosPlanillas[$id_planilla][$id_empleado][$id_concepto]['valor_concepto'] = $valor_concepto;

				$style = ($style=='')? 'style="background-color:#E5E5E5;"' : '' ;
				$body.='<tr>
							<td '.$style.' >'.$arrayResul['codigo_concepto'].'</td>
							<td '.$style.' >'.$arrayResul['concepto'].'</td>
							<td '.$style.' >'.$arrayEmpleadosPlanillas[$id_planilla][$id_empleado]['dias_laborados_empleado'].'</td>
							<td '.$style.' >'.$arrayContratos[ $arrayResul['id_contrato'] ]['salario_basico'].'</td>
							<td '.$style.' >'.$arrayResul['valor_concepto'].'</td>
							<td '.$style.' >'.$valor_concepto.'</td>
							<td '.$style.' >'.$arrayResul['naturaleza'].'</td>

							<td '.$style.' >'.$arrayColgaap[$id_planilla][$arrayResul['id_tercero']][$arrayResul['cuenta_colgaap']]['codigo_cuenta'].'</td>
							<td '.$style.' >'.$arrayColgaap[$id_planilla][$arrayResul['id_tercero']][$arrayResul['cuenta_colgaap']]['debe'].'</td>
							<td '.$style.' >'.$arrayColgaap[$id_planilla][$arrayResul['id_tercero']][$arrayResul['cuenta_colgaap']]['haber'].'</td>

							<td '.$style.' >'.$arrayColgaap[$id_planilla][$arrayResul['id_tercero_contrapartida']][$arrayResul['cuenta_contrapartida_colgaap']]['codigo_cuenta'].'</td>
							<td '.$style.' >'.$arrayColgaap[$id_planilla][$arrayResul['id_tercero_contrapartida']][$arrayResul['cuenta_contrapartida_colgaap']]['debe'].'</td>
							<td '.$style.' >'.$arrayColgaap[$id_planilla][$arrayResul['id_tercero_contrapartida']][$arrayResul['cuenta_contrapartida_colgaap']]['haber'].'</td>
							<td '.$style.' ></td>
						</tr>';

				$sqlNominaConceptos.="UPDATE nomina_planillas_empleados_conceptos SET valor_concepto='$valor_concepto' WHERE id=".$arrayResul['id'].";";

				$debito                = ($arrayResul['caracter']=='debito')? $valor_concepto : 0 ;
				$credito               = ($arrayResul['caracter']=='credito')? $valor_concepto : 0 ;
				$debito_contrapartida  = ($arrayResul['caracter_contrapartida']=='debito')? $valor_concepto : 0 ;
				$credito_contrapartida = ($arrayResul['caracter_contrapartida']=='credito')? $valor_concepto : 0 ;

				$sqlAsientosColgaap.="UPDATE asientos_colgaap SET debe=$debito,haber=$credito WHERE id=".$arrayColgaap[$id_planilla][$arrayResul['id_tercero']][$arrayResul['cuenta_colgaap']]['id'].";" ;
				$sqlAsientosColgaap.="UPDATE asientos_colgaap SET debe=$debito_contrapartida,haber=$credito_contrapartida WHERE id=".$arrayColgaap[$id_planilla][$arrayResul['id_tercero_contrapartida']][$arrayResul['cuenta_contrapartida_colgaap']]['id'].";";

				$sqlAsientosNiif.="UPDATE asientos_niif SET debe=$debito,haber=$credito WHERE id=".$arrayColgaap[$id_planilla][$arrayResul['id_tercero']][$arrayResul['cuenta_niif']]['id'].";";
				$sqlAsientosNiif.="UPDATE asientos_niif SET debe=$debito_contrapartida,haber=$credito_contrapartida WHERE id=".$arrayColgaap[$id_planilla][$arrayResul['id_tercero_contrapartida']][$arrayResul['cuenta_contrapartida_niif']]['id'].";";

			}
		}
	}

	echo "<style>
			.head_planilla{
				background-color : #4F81BD;
				color            : #FFF;

			}
			.head_planilla td{
				padding : 5px;
			}
			.head_empleado{
				background-color: #9BBB59;
				color : #FFF;
			}
			.head_empleado td{
				padding : 5px;
			}
		</style>
			<table cellspacing='0'>
				".$body."
			</table>";

	echo $sqlNominaConceptos.$sqlAsientosColgaap.$sqlAsientosNiif;

	//FUNCION PARA CALCULAR LA FORMULA DEL CONCEPTO
	function calcula_formula($equation)
    {
    	if ($equation=='') {
    		return round(0,0);
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
        return round($result,0);
    }

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

?>