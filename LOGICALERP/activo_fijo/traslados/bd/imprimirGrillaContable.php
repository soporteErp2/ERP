<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	if(!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA'] == ''){
		exit;
	}

	$idEmpresa = $_SESSION['EMPRESA'];

	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa  = "SELECT nombre,tipo_documento_nombre,documento,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular
									FROM empresas
									WHERE id = '$idEmpresa'
									LIMIT 0,1";
	$queryEmpresa 				 = mysql_query($sqlEmpresa,$link);
	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ubicacion_empresa     = mysql_result($queryEmpresa,0,'ciudad').' - '.mysql_result($queryEmpresa,0,'pais');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos 			   		 = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	$actividad_economica   = mysql_result($queryEmpresa,0,'actividad_economica');

	$subTotalDocumento  = 0.00;
	$totalDocumento     = 0.00;

	$sql = "SELECT * FROM $tablaPrincipal WHERE id = '$id' AND activo = 1 AND id_empresa = '$idEmpresa'";
	$consul = mysql_query($sql,$link);

	if(!$consul){
		die('Infrme no valido ' . mysql_error());
	}

	while($row = mysql_fetch_array($consul)){
		if($row['estado'] == 0){
			echo '<center><h2><i>Documento no Generado</i></h2></center>';
			exit;
		}
		else if($row['estado'] == 3){
			echo "<center><h2><i>Documento Cancelado</i></h2></center>";
			exit;
		}

		$labelConsecutivo = 'Consecutivo No.';
		$documento   			= 'Traslado';
		$titulo      			= 'TRASLADO ACTIVOS FIJOS';
		$consecutivo 			= $row['consecutivo'];
		$estilo 					= 'background-color: #DFDFDF;';

		$sqlArticulos   = "SELECT * FROM $tablaInventario WHERE $idTablaPrincipal = '$id'";
		$queryArticulos = mysql_query($sqlArticulos,$link);

		while($array = mysql_fetch_array($queryArticulos)){
			$estilo 		 = ($estilo != '') ? '': 'background-color: #DFDFDF;';
			$costo 			 = $array['costo'];
			$valor 			 = $array['valor'];
			$styleBorder = ($array["observaciones"] == '')? 'border-bottom:1px solid #000;': 'border-bottom:1px dotted #000;';
			$subTotal    = ((($costo - $valor) < 0 || $valor == 0)? 0 : $costo - $valor);

			$articulos .=  '<tr style="'.$estilo.' '.$styleBorder.'">
												<td style="width: 55px;">'.$array["codigo_activo"].'</td>
												<td style="width: 230px; text-align:left; padding-left:5px;">'.$array["nombre"].'</td>
												<td style="width: 80px; text-align:left; padding-left:5px;">'.$array["unidad"].' x '.$array["numero_piezas"].'</td>
												<td style="width: 80px; text-align:right;">'.$array["costo"].'</td>
												<td style="width: 60px; text-align:right;">'.$array["depreciacion_acumulada"].'</td>
												<td style="width: 60px; text-align:right;">'.$array["depreciacion_acumulada_niif"].'</td>
											 	<td style="width: 80px; text-align:right;">'.$array["deterioro_acumulado"].'</td>
											</tr>';

			$subTotalDocumento = $subTotal + $subTotalDocumento;		//SUBTOTAL FACTURA
		}

		$totalDocumento = $subTotalDocumento;

		$arrayReplaceString = array("\n", "\r");
		$row['observacion'] = str_replace($arrayReplaceString, "<br/>", $row['observacion'] );

		//=================== CONSULTAMOS LOS DATOS DEL TERCERO ==================//
		$sqlCliente  = "SELECT direccion,telefono1,ciudad
										FROM terceros
										WHERE id_empresa = '$idEmpresa'
										AND tercero = 1
										AND id = ".$row["id_cliente"];
		$queryCliente      = mysql_query($sqlCliente,$link);
		$direccion_cliente = mysql_result($queryCliente,0,'direccion');
		$telefono_cliente  = mysql_result($queryCliente,0,'telefono1');
		$ciudad_cliente    = mysql_result($queryCliente,0,'ciudad');

		//========================= ARMAMOS EL DOCUMENTO =========================//
		$header =  '<div id="body_pdf" style="width:100%; font-style:normal; font-size:11px;">
									<div style="float:left; width:445px; margin-left:10px;">
										<b>'.$razon_social.'</b>
										<br>'.$tipo_regimen.'&nbsp;&nbsp;&nbsp;&nbsp; '.$tipo_documento_nombre.': <b>'.$documento_empresa.'</b>
										<br>'.$direccion_empresa.'&nbsp;&nbsp;<b>Tels:</b>'.$telefonos.'
										<br>'.$row["sucursal"].' '.$row["bodega"].'
										<br>'.$ubicacion_empresa.'
									</div>
									<div style="float:left;width:30%;text-align:center;">
										<b>'.$titulo.'<br>'.$labelConsecutivo.' '.$consecutivo.'</b><br>
										ACTIVIDAD ECONOMICA '.$actividad_economica.' <br>
									</div>
									<br>
									<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;">
										<div style="float:left; width:90%; margin:0px 5px 0px 10px">
											<div style="float:left; width:15%;"><b>Sucursal</b></div>
											<div style="float:left; width:50%;">'.$row["sucursal"].'</div>
										</div>
										<div style="float:left; width:90%; margin:0px 5px 0px 10px">
											<div style="float:left; width:15%;"><b>Fecha</b></div>
											<div style="float:left; width:50%;">'.$row["fecha_inicio"].'</div>
										</div>
										<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
											<div style="float:left; width:15%;"><b>Usuario</b></div>
											<div style="float:left; width:50%;">'.$row["usuario"].'</div>
										</div>
									</div>
								</div>';

		$contenido = '<table class="articlesTable">
										<thead>
											<tr>
												<td style="width:55px;">CODIGO</td>
												<td style="width:230px;">ACTIVO</td>
												<td style="width:80px;">UNIDAD</td>
												<td style="width:80px;">COSTO</td>
												<td style="width:80px; border-right: 1px solid;">DEP. LOCAL</td>
												<td style="width:80px; border-right: 1px solid;">DEP. NIIF</td>
												<td style="width:60px;">DET. ACUM.</td>
											</tr>
										</thead>
										<tbody>
											'.$articulos.'
										</tbody>
									</table>
									<table style="overflow: hidden; width:100%; margin:50px 5px 100px 0px; padding:0px 7px 0px 0px; font-size:12px;">
										<tr style="width:100%;">
											<td style="width:100%;">
												<table>
													<tr>
														<td>Observaciones</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td style="border-bottom:1px solid #000;" colspan="3">'.$row['observacion'].'</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td style="40%;border-top: 1px solid;"><br><br><br>_______________________________________________________</td>
														<td style="10%;border-top: 1px solid;"><br><br><br>&nbsp;</td>
														<td style="40%;border-top: 1px solid;"><br><br><br>_______________________________________________________</td>
													</tr>
													<tr>
														<td style="40%;border-top: 1px solid;">Elaborado por</td>
														<td style="10%;border-top: 1px solid;">&nbsp;</td>
														<td style="40%;border-top: 1px solid;">Revisado</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<style>
										.articlesTable{
											font-size       : 12px;
											border          : 1px solid #000;
											border-collapse : collapse;
											margin-left     : 10px;
											width           : 100%;
										}
										.articlesTable td{
											padding         : 2px;
											border          : 1px solid #000;
											border-collapse : collapse;
										}
										.articlesTable thead td{
											font-size   		: 10px;
											font-weight 		: bold;
											text-align  		: center;
										}
										.articlesTable tbody tr{
											border 					: none;
										}
									</style>';
	}

	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS = 50 ; $MD = 10;$MI = 15;$ML = 10; }
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}

	if($IMPRIME_PDF){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   			// mode - default ''
			$HOJA,					// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION		// L - landscape, P - portrait
		);

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle($documento);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHTMLHeader(utf8_encode($header));
		$mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($contenido));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA

		exit;
	}
	else{
		echo $contenido;
	}

	//FUNCIONES DE CONVERSION DE NUMEROS A LETRAS.
	//@function num2letras ()
	//@abstract Dado un n?mero lo devuelve escrito.
	//@param $num number - N?mero a convertir.
	//@param $fem bool - Forma femenina (true) o no (false).
	//@param $dec bool - Con decimales (true) o no (false).
	//@result string - Devuelve el n?mero escrito en letra.

	function num2letras($num, $fem = false, $dec = true){
  $matuni[2]  = "dos";
  $matuni[3]  = "tres";
  $matuni[4]  = "cuatro";
  $matuni[5]  = "cinco";
  $matuni[6]  = "seis";
  $matuni[7]  = "siete";
  $matuni[8]  = "ocho";
  $matuni[9]  = "nueve";
  $matuni[10] = "diez";
  $matuni[11] = "once";
  $matuni[12] = "doce";
  $matuni[13] = "trece";
  $matuni[14] = "catorce";
  $matuni[15] = "quince";
  $matuni[16] = "dieciseis";
  $matuni[17] = "diecisiete";
  $matuni[18] = "dieciocho";
  $matuni[19] = "diecinueve";
  $matuni[20] = "veinte";

	$matunisub[2] = "dos";
  $matunisub[3] = "tres";
  $matunisub[4] = "cuatro";
  $matunisub[5] = "quin";
  $matunisub[6] = "seis";
  $matunisub[7] = "sete";
  $matunisub[8] = "ocho";
  $matunisub[9] = "nove";

  $matdec[2] = "veint";
  $matdec[3] = "treinta";
  $matdec[4] = "cuarenta";
  $matdec[5] = "cincuenta";
  $matdec[6] = "sesenta";
  $matdec[7] = "setenta";
  $matdec[8] = "ochenta";
  $matdec[9] = "noventa";

	$matsub[3]  = 'mill';
  $matsub[5]  = 'bill';
  $matsub[7]  = 'mill';
  $matsub[9]  = 'trill';
  $matsub[11] = 'mill';
  $matsub[13] = 'bill';
  $matsub[15] = 'mill';

  $matmil[4]  = 'millones';
  $matmil[6]  = 'billones';
  $matmil[7]  = 'de billones';
  $matmil[8]  = 'millones de billones';
  $matmil[10] = 'trillones';
  $matmil[11] = 'de trillones';
  $matmil[12] = 'millones de trillones';
  $matmil[13] = 'de trillones';
  $matmil[14] = 'billones de trillones';
  $matmil[15] = 'de billones de trillones';
  $matmil[16] = 'millones de billones de trillones';

  $float = explode('.',$num);
  $num	 = $float[0];
  $num 	 = trim((string)@$num);

  if($num[0] == '-'){
    $neg = 'menos ';
    $num = substr($num, 1);
  } else
    $neg = '';
    while($num[0] == '0') $num = substr($num, 1);
	  	if($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
		   	$zeros = true;
		   	$punt = false;
		   	$ent = '';
		   	$fra = '';
   	for($c = 0; $c < strlen($num); $c++){
      $n = $num[$c];
      if(!(strpos(".,'''", $n) === false)){
        if($punt){
					break;
				}
        else{
          $punt = true;
          continue;
        }
      } elseif(!(strpos('0123456789', $n) === false)){
        if($punt){
          if($n != '0'){
						$zeros = false;
            $fra .= $n;
					}
        } else{
          $ent .= $n;
				}
      } else

         break;

   }
   $ent = '     ' . $ent;
   if ($dec and $fra and ! $zeros) {
      $fin = ' coma';
      for ($n = 0; $n < strlen($fra); $n++) {
         if (($s = $fra[$n]) == '0')
            $fin .= ' cero';
         elseif ($s == '1')
            $fin .= $fem ? ' una' : ' un';
         else
            $fin .= ' ' . $matuni[$s];
      }
   }else
      $fin = '';
   if ((int)$ent === 0) return 'Cero ' . $fin;
   $tex = '';
   $sub = 0;
   $mils = 0;
   $neutro = false;
   while ( ($num = substr($ent, -3)) != '   ') {
      $ent = substr($ent, 0, -3);
      if (++$sub < 3 and $fem) {
         $matuni[1] = 'una';
         $subcent = 'as';
      }else{
         $matuni[1] = $neutro ? 'un' : 'uno';
         $subcent = 'os';
      }
      $t = '';
      $n2 = substr($num, 1);
      if ($n2 == '00') {
      }elseif ($n2 < 21)
         $t = ' ' . $matuni[(int)$n2];
      elseif ($n2 < 30) {
         $n3 = $num[2];
         if ($n3 != 0) $t = 'i' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }else{
         $n3 = $num[2];
         if ($n3 != 0) $t = ' y ' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }
      $n = $num[0];
      if ($n == 1) {
         $t = ' ciento' . $t;
      }elseif ($n == 5){
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
      }elseif ($n != 0){
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
      }
      if ($sub == 1) {
      }elseif (! isset($matsub[$sub])) {
         if ($num == 1) {
            $t = ' mil';
         }elseif ($num > 1){
            $t .= ' mil';
         }
      }elseif ($num == 1) {
         $t .= ' ' . $matsub[$sub] . '?n';
      }elseif ($num > 1){
         $t .= ' ' . $matsub[$sub] . 'ones';
      }
      if ($num == '000') $mils ++;
      elseif ($mils != 0) {
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
         $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
   }
   $tex = $neg . substr($tex, 1) . $fin;

   $end_num=ucfirst($tex).' pesos ';
   return $end_num;
}
?>
