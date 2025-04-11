<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$tabla_asientos = ($contabilidad=='colgaap')? 'asientos_colgaap' : 'asientos_niif';

	$whereSucursal = ($id_sucursal=='todos')? '' : " AND id_sucursal=$id_sucursal" ;
	$whereTypedoc  = ($tipo_documento=='todos')? '' : " AND tipo_documento='$tipo_documento'";

	$sql  = "SELECT
				id_documento,
				consecutivo_documento,
				tipo_documento,
				tipo_documento_extendido,
				tipo_documento_cruce,
				numero_documento_cruce,
				fecha,
				debe,
				haber,
				codigo_cuenta,
				cuenta,
				nit_tercero,
				tercero,
				sucursal,
				sucursal_cruce,
				codigo_centro_costos,
				centro_costos
			FROM $tabla_asientos
			WHERE activo=1
				AND id_empresa=$id_empresa
				AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
				$whereSucursal
				$whereTypedoc
			ORDER BY id ASC";
	$query = mysql_query($sql,$link);

	if($type == 'XLS'){

		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=".$tabla_asientos."_".(date("Y_m_d")).".xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo'<table>
				<tr>
					<td>CONSECUTIVO DOCUMENTO</td>
					<td>TIPO DOCUMENTO</td>
					<td>NOMBRE DOCUMENTO</td>
					<td>DESCRIPCION</td>
					<td>TIPO DOCUMENTO CRUCE</td>
					<td>NUMERO DOCUMENTO CRUCE</td>
					<td>FECHA</td>
					<td>DEBITO</td>
					<td>CREDITO</td>
					<td>CUENTA</td>
					<td>DESCRIPCION CUENTA</td>
					<td>NUMERO DE IDENTIFICACION TERCERO</td>
					<td>TERCERO</td>
					<td>SUCURSAL</td>
					<td>SUCURSAL CRUCE</td>
					<td>CODIGO CENTRO COSTOS</td>
					<td>CENTRO COSTOS</td>
				</tr>';

		while ($row=mysql_fetch_array($query)) {
			echo'<tr>
					<td>'.$row['consecutivo_documento'].'</td>
					<td>'.$row['tipo_documento'].'</td>
					<td>'.$row['tipo_documento_extendido'].'</td>
					<td>'.$row['descripcion'].'</td>
					<td>'.$row['tipo_documento_cruce'].'</td>
					<td>'.$row['numero_documento_cruce'].'</td>
					<td>'.$row['fecha'].'</td>
					<td>'.$row['debe'].'</td>
					<td>'.$row['haber'].'</td>
					<td>'.$row['codigo_cuenta'].'</td>
					<td>'.$row['cuenta'].'</td>
					<td>'.$row['nit_tercero'].'</td>
					<td>'.$row['tercero'].'</td>
					<td>'.$row['sucursal'].'</td>
					<td>'.$row['sucursal_cruce'].'</td>
					<td>'.$row['codigo_centro_costos'].'</td>
					<td>'.$row['centro_costos'].'</td>
				</tr>';
		}

		echo'</table>';
	}
	else if($type=='CSV'){

		header("Content-type: text/csv; charset=utf-8");
		header("Content-Disposition: attachment; filename=".$tabla_asientos."_".(date("Y_m_d")).".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo "CONSECUTIVO DOCUMENTO;TIPO DOCUMENTO;NOMBRE DOCUMENTO;DESCRIPCION;TIPO DOCUMENTO CRUCE;NUMERO DOCUMENTO CRUCE;FECHA;DEBITO;CREDITO;CUENTA;DESCRIPCION CUENTA;NUMERO DE IDENTIFICACION TERCERO;TERCERO;SUCURSAL;SUCURSAL CRUCE;CODIGO CENTRO COSTOS;CETRO COSTOS\n";

		while ($row = mysql_fetch_array($query)) {
			// Limpieza básica del campo 'tercero'
			$row['tercero'] = str_replace("&nbsp;", "", $row['tercero']);
		
			echo escapar_csv($row['consecutivo_documento']) . ';' .
				 escapar_csv($row['tipo_documento']) . ';' .
				 escapar_csv($row['tipo_documento_extendido']) . ';' .
				 escapar_csv($row['descripcion']) . ';' .
				 escapar_csv($row['tipo_documento_cruce']) . ';' .
				 escapar_csv($row['numero_documento_cruce']) . ';' .
				 escapar_csv($row['fecha']) . ';' .
				 escapar_csv($row['debe']) . ';' .
				 escapar_csv($row['haber']) . ';' .
				 escapar_csv($row['codigo_cuenta']) . ';' .
				 escapar_csv($row['cuenta']) . ';' .
				 escapar_csv($row['nit_tercero']) . ';' .
				 escapar_csv($row['tercero']) . ';' .
				 escapar_csv($row['sucursal']) . ';' .
				 escapar_csv($row['sucursal_cruce']) . ';' .
				 escapar_csv($row['codigo_centro_costos']) . ';' .
				 escapar_csv($row['centro_costos']) . "\n";
		}
		
	}

	// Función auxiliar para escapar campos
	function escapar_csv($valor) {
		// Reemplazar comillas dobles con dobles comillas
		$valor = str_replace('"', '""', $valor);
		// Envolver en comillas si contiene ; , salto de línea o comillas
		if (preg_match('/[;,\"\r\n]/', $valor)) {
			$valor = '"' . $valor . '"';
		}
		return $valor;
	}

?>