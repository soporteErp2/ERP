
<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');	

	if (!isset($tipo_tercero_reporte) && $IMPRIME_XLS != 'true') {
		?>
		<script>
			// localStorage.clear();// BORRAR EL LOCAL STORANGE
			localStorage.tercero_tributario_reporte_t = "";
			localStorage.pais_reporte_t               = "";
			localStorage.departamento_reporte_t       = "";
			localStorage.tipo_tercero_reporte         = "";
			localStorage.clase_tercero_reporte        = "";
			localStorage.celular2_reporte_t           = "";
			localStorage.telefono2_reporte_t          = "";
			localStorage.apellido2_reporte_t          = "";
			localStorage.nombre2_reporte_t            = "";
			localStorage.direccion_reporte_t          = "";
			localStorage.cuidad_reporte_t             = "";
			localStorage.nombre_comercial_reporte_t   = "";
			localStorage.telefono1_reporte_t          = "";
			localStorage.apellido1_reporte_t          = "";
			localStorage.celular1_reporte_t           = "";
			localStorage.nombre1_reporte_t            = "";
			localStorage.funcionario_asignado         = "";
			localStorage.email1                       = "";
			localStorage.email2                       = "";			
		</script>
		<?php
	}

	ob_start();

	if($IMPRIME_XLS=='true'){


       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_terceros_".date("Y_m_d").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

	$fecha      = date("Y-m-d");
	$id_empresa = $_SESSION["EMPRESA"];

	$tipo_proveedores    = "T.id_empresa='$id_empresa'";
	$tipo_terceros_todos = "TERCEROS";

	if ($tipo_tercero_reporte==2) {
		$tipo_terceros_todos = "PROSPECTOS";
		$tipo_proveedores   .= " AND tercero=0";
		$displayHead       	 = '';

    }
    if ($tipo_tercero_reporte==1 || !isset($tipo_tercero_reporte)) {
		$tipo_terceros_todos  = "TERCEROS";
		$tipo_proveedores    .= " AND tercero=1";
		$displayHead          = '<td>CODIGO</td>
								 <td>TIPO</td>
						         <td>RAZON SOCIAL</td>
						         <td style="text-align:left;" >NIT</td>';
	}

	if ($clase_tercero_reporte==3) {
		$tipo_proveedores_todos = "PROVEEDORES";
		$tipo_proveedores   .= " AND tipo_proveedor='Si'";
    }
    if ($clase_tercero_reporte==2) {
		$tipo_proveedores_todos  = "CLIENTES";
		$tipo_proveedores    .= " AND tipo_cliente='Si'";
	}

	$whereFuncionarios = '';
	$join              = 'LEFT JOIN';
	// FUNCIONARIOS
	if ($idFuncionarios!='') {
		//$groupBy .= ',F.id_vendedor';
		 $whereFuncionarios = "AND(TA.id_asignado = ".str_replace(',', ' OR TA.id_asignado=', $idFuncionarios).")"; 
		 $join = 'INNER JOIN';
	}

	// if($tipo_tercero==3) {
	// 	$tipo_terceros_todos = "PROVEEDORES";
	// 	$tipo_proveedores   .= " AND tipo_proveedor='Si'";
 //    }
 //    if ($tipo_tercero==2) {
	// 	$tipo_terceros_todos  = "CLIENTES";
	// 	$tipo_proveedores    .= " AND tipo_cliente='Si'";
	// }

	if($IMPRIME_CSV!='true' ){
?>

		<style>
			.reporte_terceros, .reporte_terceros td{
				font-size   : 12px;
				font-family : Arial, Geneva, sans-serif;
			}

			.reporte_terceros_title td{ font-size :	13px; }

			.titulos{
				background   : #000;
				padding-left : 10px;
		    }

		    .titulos td{
				color      : #FFF;
				text-align : center;
		    }

		    .filassty{ padding-right : 5px; }
		    .numeros{ text-align : right; }
		</style>

		<body>
			<div class="reporte_terceros">
				<?php
	if($IMPRIME_XLS!='true'){
			?>
			    <htmlpageheader  class="SoloPDF" name="MyHeaderInforme">
			        <div style="text-align:right;font-size:8px">
			            <?php echo $nombre_informe.'  |  '.$nombre_empresa.'  |  '.$_SESSION["NOMBREFUNCIONARIO"].'  |  '.fecha_larga_hora_m(date('Y-m-d H:s:i')); ?>  |   Paginas({PAGENO} de {nb})
			        </div>
			    </htmlpageheader>
			    <sethtmlpageheader name="MyHeaderInforme" show-this-page="1" value="on"></sethtmlpageheader>
			<?php
			     }
			?>

				<table style="float:left;width:100%">
					<tr>
						<td style="width:100%; border-bottom:1px solid #CCC; font-weight:bold; font-size:18px; text-align:left;">INFORME <?php echo $tipo_terceros_todos; ?> <?php echo $tipo_proveedores_todos; ?></td>
					</tr>
				</table>
				<table class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
					<tr>
						<td>
							<table style="float:left; width:370px; border-spacing:0px" class="my_informe_Contenedor_Titulo_informe1">
							</table>
						</td>
						<td>
							<table style="float:left; width:370px;">
								<tr style="float:left;width:100%; text-align:center">
									<td class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $_SESSION['NOMBREEMPRESA']?>  <?php echo $_SESSION['NITEMPRESA']; ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			<?php
				
				$query_terceros = query_terceros($tipo_proveedores,$join,$whereFuncionarios,$mysql);
				$cont_columnas  = 0;

				if ($row = mysql_fetch_array($query_terceros)){

				  	echo'<table class="defaultFont" align="center" style="text-align:left; width:1015px; border-collapse:collapse;">
				  			<thead>
								<tr class="titulos">									
									'.$displayHead;
		  					   		if ($nombre_comercial_reporte_t==="true") { $cont_columnas++; echo '<td>NOMBRE COMERCIAL</td>'; }
							  	 	if ($direccion_reporte_t==="true") { $cont_columnas++; echo '<td>DIRECCION</td>'; }
							  	 	if ($telefono1_reporte_t==="true") { $cont_columnas++; echo '<td>TELEFONO1</td>'; }
							  	 	if ($telefono2_reporte_t==="true") { $cont_columnas++; echo '<td>TELEFONO2</td>'; }
							  	 	if ($celular1_reporte_t==="true") { $cont_columnas++; echo '<td>CELULAR1</td>'; }
							  	 	if ($celular2_reporte_t==="true") { $cont_columnas++; echo '<td>CELULAR2</td>'; }
							  	 	if ($cuidad_reporte_t==="true") { $cont_columnas++; echo '<td>CIUDAD</td>'; }
							  	 	if ($departamento_reporte_t==="true") { $cont_columnas++; echo '<td>DEPARTAMENTO</td>'; }
							  	 	if ($pais_reporte_t==="true") { $cont_columnas++; echo '<td>PAIS</td>'; }
							  	 	if ($tercero_tributario_reporte_t==="true") { $cont_columnas++; echo '<td>REGIMEN TRIBUTARIO</td>'; }
							  	 	if ($nombre1_reporte_t==="true") { $cont_columnas++; echo '<td>NOMBRE1</td>'; }
							  	 	if ($nombre2_reporte_t==="true") { $cont_columnas++; echo '<td>NOMBRE2</td>'; }
							  	 	if ($apellido1_reporte_t==="true") { $cont_columnas++; echo '<td>APELLIDO1</td>'; }
							  	 	if ($apellido2_reporte_t==="true") { $cont_columnas++; echo '<td>APELLIDO2</td>'; }
							  	 	if ($funcionario_asignado==="true") { $cont_columnas++; echo '<td>ASIGNADO</td>'; }
							  	 	if ($email1==="true") { $cont_columnas++; echo '<td>EMAIL 1</td>'; }
							  	 	if ($email2==="true") { $cont_columnas++; echo '<td>EMAIL 2</td>'; }
						echo 	'</tr>
							</thead>';

					do {
						if($tipo_tercero_reporte==1 || !isset($tipo_tercero_reporte)){
							$displayCampos = '<td style="text-align:right;">'.$row['codigo'].'</td>
											  <td class="filassty">'.$row['tipo'].'</td>
					      					  <td class="filassty">'.$row['nombre'].'</td>
					      					  <td class="numeros" style="padding-right: 20px;">'.$row['numero_identificacion'].'</td>';
						}
						else{
							/*$displayCampos = '<td class="filassty">'.$row['nombre_comercial'].'</td>
											  <td class="filassty">'.$row['direccion'].'</td>
											  <td class="filassty">'.$row['telefono1'].'</td>
					      					  <td class="filassty">'.$row['ciudad'].'</td>
					      					  <td class="filassty">'.$row['departamento'].'</td>';*/
						}

						$tResult.= 	'<tr>						   				
						   				'.$displayCampos.'					      				
					      			';

				      	if ($nombre_comercial_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['nombre_comercial'].'</td>'; }
					   	if ($direccion_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['direccion'].'</td>'; }
					   	if ($telefono1_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['telefono1'].'</td>'; }
				  	 	if ($telefono2_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['telefono2'].'</td>'; }
				  	 	if ($celular1_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['celular1'].'</td>'; }
				  	 	if ($celular2_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['celular2'].'</td>'; }
				  	 	if ($cuidad_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['ciudad'].'</td>'; }
				  	 	if ($departamento_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['departamento'].'</td>'; }
				  	 	if ($pais_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['pais'].'</td>'; }
				  	 	if ($tercero_tributario_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['tercero_tributario'].'</td>'; }
				  	 	if ($nombre1_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['nombre1'].'</td>'; }
				  	 	if ($nombre2_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['nombre2'].'</td>'; }
				  	 	if ($apellido1_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['apellido1'].'</td>'; }
				  	 	if ($apellido2_reporte_t==="true") { $tResult.= '<td class="filassty">'.$row['apellido2'].'</td>'; }
				  	 	if ($funcionario_asignado==="true") { $tResult.= '<td class="filassty">'.$row['asignado'].'</td>'; }
				  	 	if ($email1==="true") { $tResult.= '<td class="filassty">'.$row['email1'].'</td>'; }
				  	 	if ($email2==="true") { $tResult.= '<td class="filassty">'.$row['email2'].'</td>'; }

						$tResult.= '</tr>';

					} while ($row = $mysql->fetch_array($query_terceros));
					$tResult.= '</table>';
				}
				else { $tResult.= 'No se ha encontrado ningun registro'; }

				if($IMPRIME_XLS=='true'){
					echo utf8_encode($tResult);
				}else{
					echo $tResult;}
			?>
	                </tbody>
	            </table>
	        </div>
		</body>
<?php
	}
	else{				//IMPRIME CSV
		header("Content-type: text/csv; charset=utf-8");
		header("Content-Disposition: attachment; filename=".(date("Y_m_d")).".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo "CODIGO;NIT;TIPO;RAZON SOCIAL;NOMBRE COMERCIAL";

		if ($nombre_comercial_reporte_t==="true") { echo ';NOMBRE COMERCIAL'; }
		if ($direccion_reporte_t==="true") { echo ';DIRECCION'; }
		if ($telefono1_reporte_t==="true") { echo ';TELEFONO1'; }
		if ($telefono2_reporte_t==="true") { echo ';TELEFONO2'; }
		if ($celular1_reporte_t==="true") { echo ';CELULAR1'; }
		if ($celular2_reporte_t==="true") { echo ';CELULAR2'; }
		if ($cuidad_reporte_t==="true") { echo ';CIUDAD'; }
		if ($departamento_reporte_t==="true") { echo ';DEPARTAMENTO'; }
		if ($pais_reporte_t==="true") { echo ';PAIS'; }
		if ($tercero_tributario_reporte_t==="true") { echo ';REGIMEN TRIBUTARIO'; }
		if ($nombre1_reporte_t==="true") { echo ';NOMBRE1'; }
		if ($nombre2_reporte_t==="true") { echo ';NOMBRE2'; }
		if ($apellido1_reporte_t==="true") { echo ';APELLIDO1'; }
		if ($apellido2_reporte_t==="true") { echo ';APELLIDO2'; }
		if ($funcionario_asignado==="true") {  echo ';ASIGNADO'; }
  	 	if ($email1==="true") {  echo ';EMAIL 1'; }
  	 	if ($email2==="true") {  echo ';EMAIL 2'; }

		echo "\n";

		$query_terceros = query_terceros($tipo_proveedores,$join,$whereFuncionarios,$mysql);
		while ($row=$mysql->fetch_array($query_terceros)) {

			echo "$row[codigo];$row[numero_identificacion];$row[tipo];$row[nombre]";

			if ($nombre_comercial_reporte_t==="true") { echo ';'.str_replace(";","",$row['nombre_comercial']); }
		   	if ($direccion_reporte_t==="true") { echo ';'.str_replace(";","",$row['direccion']); }
		   	if ($telefono1_reporte_t==="true") { echo ';'.str_replace(";","",$row['telefono1']); }
	  	 	if ($telefono2_reporte_t==="true") { echo ';'.str_replace(";","",$row['telefono2']); }
	  	 	if ($celular1_reporte_t==="true") { echo ';'.str_replace(";","",$row['celular1']); }
	  	 	if ($celular2_reporte_t==="true") { echo ';'.str_replace(";","",$row['celular2']); }
	  	 	if ($cuidad_reporte_t==="true") { echo ';'.str_replace(";","",$row['ciudad']); }
	  	 	if ($departamento_reporte_t==="true") { echo ';'.str_replace(";","",$row['departamento']); }
	  	 	if ($pais_reporte_t==="true") { echo ';'.str_replace(";","",$row['pais']); }
	  	 	if ($tercero_tributario_reporte_t==="true") { echo ';'.str_replace(";","",$row['tercero_tributario']); }
	  	 	if ($nombre1_reporte_t==="true") { echo ';'.str_replace(";","",$row['nombre1']); }
	  	 	if ($nombre2_reporte_t==="true") { echo ';'.str_replace(";","",$row['nombre2']); }
	  	 	if ($apellido1_reporte_t==="true") { echo ';'.str_replace(";","",$row['apellido1']); }
	  	 	if ($apellido2_reporte_t==="true") { echo ';'.str_replace(";","",$row['apellido2']); }
	  	 	if ($funcionario_asignado==="true") {  echo ';'.str_replace(";","",$row['asignado']); }
	  	 	if ($email1==="true") {  echo ';'.str_replace(";","",$row['email1']); }
	  	 	if ($email2==="true") {  echo ';'.str_replace(";","",$row['email2']); }

			echo "\n";

		}
	}

	$documento = "informe_terceros_".$fecha;
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }

	// ORIENTACION PDF
    if($cont_columnas>2){ $HOJA = 'LETTER-L'; }
    else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'L'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
	if($IMPRIME_PDF == 'true'){
		include("../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
					'utf-8',  		// mode - default ''
					$HOJA,			// format - A4, for example, default ''
					12,				// font size - default 0
					'',				// default font family
					$MI,			// margin_left
					$MD,			// margin right
					$MS,			// margin top
					$ML,			// margin bottom
					10,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables     = true;
        $mpdf->packTableData    = true;
		$mpdf->SetAutoPageBreak(true, 15);

		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
    else{ echo $texto; }    

    function query_terceros($tipo_proveedores,$join,$whereFuncionarios,$mysql){
    	$sql = "SELECT
					T.codigo,
					T.numero_identificacion,
					T.empresa,
					T.tipo,
					T.nombre,
					T.nombre_comercial,
					T.direccion,
					T.telefono1,
					T.telefono2,
					T.celular1,
					T.celular2,
					T.ciudad,
					T.departamento,
					T.pais,
					T.nombre_establecimiento,
					T.tercero_tributario,
					T.nombre1,
					T.nombre2,
					T.apellido1,
					T.apellido2,
					TA.asignado,
					TE.email AS email1,
					T.email AS email2					
				FROM
					terceros AS T
				$join terceros_asignados AS TA ON(T.id = TA.id_tercero $whereFuncionarios)
				LEFT JOIN terceros_emails AS TE ON(T.id = TE.id_tercero)
				WHERE
					T.activo=1
				AND $tipo_proveedores";
		// return $GLOBALS['mysql']->query($sql,$link);
		return $mysql->query($sql,$mysql->link);
		// echo $mysql->error.$mysql->errno;
		// return $query;
    }
?>