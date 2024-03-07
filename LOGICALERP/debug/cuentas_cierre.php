<?php
	error_reporting(E_ALL);

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="cierre_'.(date('Y_m_d_H_i_s')).'.xls"');
	header('Cache-Control: max-age=0');

	$id_empresa=$_SESSION['EMPRESA'];

	// CONSULTAR SI TIENE CUENTA DE CIERRE
	$sql="SELECT COUNT(id) AS cont,id,cuenta,descripcion FROM puc WHERE activo=1 AND tipo='cuenta_cierre'  AND id_empresa=$id_empresa";
	$query=mysql_query($sql,$link);
	$cont             = mysql_result($query,0,'cont');
	$id_cuenta_cierre = mysql_result($query,0,'id');
	$cuenta_cierre    = mysql_result($query,0,'cuenta');
	$descripcion_cuenta_cierre    = mysql_result($query,0,'descripcion');

	$sql="SELECT
			codigo_cuenta,
			cuenta,
			id_tercero,
			nit_tercero,
			tercero,
			SUM(debe) AS debe,
            SUM(haber) AS haber,
            SUM(debe-haber) AS saldo
		FROM
			asientos_colgaap
		WHERE
			activo = 1
		AND id_empresa = $id_empresa
		AND fecha>='2014-01-01' AND fecha<='2014-12-31'
		AND (
			codigo_cuenta LIKE '4%'
			OR codigo_cuenta LIKE '5%'
			OR codigo_cuenta LIKE '6%'
			OR codigo_cuenta LIKE '7%'
			OR codigo_cuenta LIKE '8%'
			OR codigo_cuenta LIKE '9%'
			)
		GROUP BY
			codigo_cuenta,
			id_tercero
		ORDER BY codigo_cuenta ASC";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		if ($row['saldo']==0) { continue; }
		$debito  = ($row['saldo']<0)? abs($row['saldo']) : 0;
		$credito = ($row['saldo']>0)? abs($row['saldo']) : 0;

		$acumDebito  += $debito;
		$acumCredito += $credito;

		$body.='<tr>
					<td>'.$row['codigo_cuenta'].'</td>
					<td>'.$row['cuenta'].'</td>
					<td>'.$debito.'</td>
					<td>'.$credito.'</td>
					<td>'.$row['nit_tercero'].'</td>
				</tr>';

		if ($cuenta_cierre<>'') {
			$acumDebito  += $credito;
			$acumCredito += $debito;

			$body.='<tr>
					<td>'.$cuenta_cierre.'</td>
					<td>'.$descripcion_cuenta_cierre.'</td>
					<td>'.$credito.'</td>
					<td>'.$debito.'</td>
					<td>900715553</td>
				</tr>';
		}


	}

	echo '<table>
			<!--<tr>
				<td>Cuenta</td>
				<td>Descripcion</td>
				<td>Debito</td>
				<td>Credito</td>
				<td>Nit</td>
			</tr>-->
			'.$body.'
			<!--<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><b>'.$acumDebito.'</td>
				<td><b>'.$acumCredito.'</td>
				<td>&nbsp;</td>
			</tr>-->
		</table>';

?>