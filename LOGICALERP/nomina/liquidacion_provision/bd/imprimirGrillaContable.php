<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }
	$id_empresa=$_SESSION['EMPRESA'];

	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa="SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular,tipo_regimen FROM empresas WHERE id=$id_empresa LIMIT 0,1";
	$queryEmpresa=mysql_query($sqlEmpresa,$link);

	//CONSULTAR EL LOGO O IMAGEN DE LA EMPRESA QUE SE CARGO DESDE EL PANEL DE CONTROL
	$sqlImagen   = "SELECT nombre,ext FROM configuracion_imagenes_documentos WHERE activo=1 AND id_empresa=$id_empresa";
	$queryImagen = mysql_query($sqlImagen,$link);
	$nombre      = mysql_result($queryImagen,0,'nombre');
	$ext         = mysql_result($queryImagen,0,'ext');

	$imagen=' &nbsp;';
	// else{ $imagen='<img src="../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$_SESSION['EMPRESA']."/logos/".$nombre.'.'.$ext.'" width="150px" height="100px" >'; }

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ubicacion_empresa     = mysql_result($queryEmpresa,0,'ciudad').' - '.mysql_result($queryEmpresa,0,'pais');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos 			   = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');

	$acumDebito  = 0;
	$acumCredito = 0;

	$SQL = "SELECT * FROM nomina_liquidacion_provision WHERE id='$id' AND activo=1 AND id_empresa=$id_empresa";
	$consul=mysql_query($SQL,$link);
	if (!$consul){die('no valido informe'.mysql_error());}
	while($row = mysql_fetch_array($consul)){
		if ($row['estado']==0) {
			echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit;
		}
		else if ($row['estado']==3) {
			echo "<center><h2><i>Documento Cancelado</i></h2></center>"; exit;
		}

		$labelConsecutivo='Consecutivo No.';
		$titulo='LIQUIDACION '.$row['concepto'];
		$consecutivo = $row['consecutivo'];

		$estilo='';

		$cuentasMostrar='<br>&nbsp;';
		//SI SE MUESTRAN LAS CUENTAS COLGAAP O NIIF
		// if ($cuentas=='niif') {
		// 	$camposBd='cuenta_niif AS cuenta_puc,descripcion_niif AS descripcion_puc,';
		// 	$cuentasMostrar='<br>CUENTAS NIIF';
		// }
		// else{
		// 	$camposBd='cuenta_puc,descripcion_puc,';
		// }

		//CONSULTAR SI LA NOTA TIENE CRUCE DE DOCUMENTOS
    	$sql="SELECT documento_cruce FROM tipo_nota_contable WHERE id_empresa='$id_empresa' AND id='".$row['id_tipo_nota']."'";
    	$query=mysql_query($sql,$link);
    	$documento_cruce=mysql_result($query,0,'documento_cruce');

		//consultamos los articulos de esta orden
		$sqlArticulos="SELECT tipo_documento_cruce,id_documento_cruce,numero_documento_cruce,debe,haber,tercero,cuenta_puc,descripcion_puc FROM nomina_liquidacion_provision_cuentas WHERE id_liquidacion_provision='$id'";
		$queryArticulos=mysql_query($sqlArticulos,$link);

		// $classBody = ($documento_cruce == 'Si' )? 'contenedorNotaContableCruce' : 'contenedorNotaContable' ;

		$color=0;


		// if ($row["tercero"]!='NOTA INTERNA') {
		// 	$tercero='<div style="float:left; width:90%; margin:0px 5px 0px 10px">
		// 						<div style="float:left; width:21%;"><b>Identificacion:</b></div>
		// 						<div style="float:left; width:60%;">'.$row["tipo_identificacion_tercero"].'  '.$row['numero_identificacion_tercero'].'</div>
		// 				</div>
		// 				<div style="float:left; width:90%; margin:0px 5px 0px 10px">
		// 						<div style="float:left; width:21%;"><b>Tercero:</b></div>
		// 						<div style="float:left; width:60%;">'.$row["tercero"].'</div>
		// 				</div>';

		// 	while ($array= mysql_fetch_array($queryArticulos)) {
		// 	if ($estilo!='') {
		// 		$estilo='';
		// 	}else{
		// 		$estilo='background-color: #DFDFDF;';
		// 	}
		// 		$articulos.='<div style="'.$estilo.'">
		// 						<div style="float: left;width: 70px;">'.$array["cuenta_puc"].'</div>
		// 						<div style="float: left;width: 450px; text-align:left; padding-left:5px;">'.$array["descripcion_puc"].'</div>
		// 						<div style="float: left;width: 70px; text-align:left; padding-left:5px;">'.$array["debe"].'</div>
		// 						<div style="float: left;width: 70px; text-align:left;">'.$array["haber"].'</div>
		// 					</div>';

		// 	$acumDebito  += $array["debe"];
		// 	$acumCredito += $array["haber"];

		// 	}

		// 	$headCuentas='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; background-color: #CDCDCD;">

		// 						<div style="float:left; width:70px; padding-left:2px; padding-top:5px; " >Cuenta</div>
		// 						<div style="float:left; width:450px; padding-left:2px; padding-top:5px;" >Descripcion</div>
		// 						<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" >Debito</div>
		// 						<div style="float:left; width:70px; padding-left:2px; padding-top:5px; " >Credito</div>

		// 					</div>';


		// }else{
			// $tercero='<div style="float:left; width:90%; margin:0px 5px 0px 10px">
			// 				<div style="float:left; width:60%;"><b>'.$row["tercero"].' &nbsp;</b></div>
			// 		</div>';

			// $array["tercero"]=($array["tercero"]=='' || is_null($array["tercero"]) )? '-' : $array["tercero"] ;



			while ($array= mysql_fetch_array($queryArticulos)) {
				$acumDebito  += $array["debe"];
				$acumCredito += $array["haber"];

				$array["debe"]=number_format($array["debe"],$_SESSION['DECIMALESMONEDA']);
				$array["haber"]=number_format($array["haber"],$_SESSION['DECIMALESMONEDA']);

				// if ($documento_cruce == 'Si') {
					$numero_documento_cruce=($array["prefijo_documento_cruce"]!="")? $array["prefijo_documento_cruce"].' '.$array["numero_documento_cruce"] : $array["numero_documento_cruce"] ;
					$tipo_documento_cruce=($array["tipo_documento_cruce"]=="")? '&nbsp;' : $array["tipo_documento_cruce"] ;
					$tercero=($array["tercero"]=='')? '&nbsp;' : $array["tercero"] ;
					$articulos.='<div style="'.$estilo.';border-bottom:1px solid;float:left;">
									<div style="float: left;width: 70px;">'.$array["cuenta_puc"].'</div>
									<div style="float: left;width: 168px; text-align:left; padding-left:5px;">'.$array["descripcion_puc"].'</div>
									<div style="float: left;width: 168px; text-align:left; padding-left:5px;">'.$tercero.'</div>
									<div style="float: left;width: 70px;  text-align:left; padding-left:5px;text-align:center;">'.$tipo_documento_cruce.'</div>
									<div style="float: left;width: 70px;  text-align:left; padding-left:5px;text-align:center;">'.$numero_documento_cruce.'</div>
									<div style="float: left;width: 70px;  text-align:right;padding-left:5px;">'.$array["debe"].'</div>
									<div style="float: left;width: 70px;  text-align:right;">'.$array["haber"].'</div>
								</div>';
				// }
				// else{
				// 	$articulos.='<div style="'.$estilo.'">
				// 					<div style="float: left;width: 70px;">'.$array["cuenta_puc"].'</div>
				// 					<div style="float: left;width: 240px; text-align:left; padding-left:5px;">'.$array["descripcion_puc"].'</div>
				// 					<div style="float: left;width: 240px; text-align:left; padding-left:5px;">'.$array["tercero"].'&nbsp;</div>
				// 					<div style="float: left;width: 70px; text-align:right; padding-left:5px;">'.$array["debe"].'</div>
				// 					<div style="float: left;width: 70px; text-align:right;">'.$array["haber"].'</div>
				// 				</div>';
				// }



				if ($estilo!='') {
					$estilo='';
				}else{
					$estilo='background-color: #DFDFDF;';
				}

			}



			$debitoCruce  = ($acumCredito>$acumDebito)? $row['total'] : 0 ;
			$creditoCruce = ($acumDebito>$acumCredito)? $row['total'] : 0 ;

			$articulos.='<div style="'.$estilo.'">
								<div style="float: left;width: 70px;">'.$row["cuenta_colgaap_cruce"].'</div>
								<div style="float: left;width: 168px; text-align:left; padding-left:5px;">'.$row["descripcion_cuenta_colgaap_cruce"].'</div>
								<div style="float: left;width: 168px; text-align:left; padding-left:5px;">'.$row["tercero"].'&nbsp;</div>
								<div style="float: left;width: 70px; text-align:left;  padding-left:5px;text-align:center;">&nbsp;</div>
								<div style="float: left;width: 70px; text-align:left;  padding-left:5px;">&nbsp;</div>
								<div style="float: left;width: 70px; text-align:right; padding-left:5px;">'.$debitoCruce.'</div>
								<div style="float: left;width: 70px; text-align:right;">'.$creditoCruce.'</div>
							</div>';

			if ($acumCredito>$acumDebito) {
				$acumDebito+=$row['total'];
			}
			else{
				$acumCredito+=$row['total'];

			}

			// if ($documento_cruce == 'Si') {
				$headCuentas='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; background-color: #CDCDCD;">

									<div style="float:left; width:70px; padding-left:2px; padding-top:5px; " >Cuenta</div>
									<div style="float:left; width:170px; padding-left:2px; padding-top:5px;" >Descripcion</div>
									<div style="float:left; width:170px; padding-left:2px; padding-top:5px;" >Tercero</div>
									<div style="float: left;width:70px;text-align:left; padding-left:5px;text-align:center;">Doc. Cruce</div>
									<div style="float: left;width:70px;text-align:left; padding-left:5px;text-align:center;">N. Doc. Cruce</div>
									<div style="float:left; width:70px; padding-left:2px; padding-top:5px;text-align:right;" >Debito</div>
									<div style="float:left; width:70px; padding-left:2px; padding-top:5px;text-align:right; " >Credito</div>

								</div>';
			// }
			// else{

			// 	$headCuentas='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; background-color: #CDCDCD;">

			// 						<div style="float:left; width:70px; padding-left:2px; padding-top:5px; " >Cuenta</div>
			// 						<div style="float:left; width:240px; padding-left:2px; padding-top:5px;" >Descripcion</div>
			// 						<div style="float:left; width:240px; padding-left:2px; padding-top:5px;" >Tercero</div>
			// 						<div style="float:left; width:70px; padding-left:2px; padding-top:5px;text-align:right;" >Debito</div>
			// 						<div style="float:left; width:70px; padding-left:2px; padding-top:5px;text-align:right; " >Credito</div>

			// 					</div>';
			// }

			// <table align="center" style="width:40%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" >
			// 						<tr style="border: 1px solid;text-align:center" bgcolor="#B9BABF">
			// 							<td style="border: 1px solid;">Fecha Gen</td><td style="border: 1px solid;">Fecha Mov</td>
			// 						</tr>
			// 						<tr>
			// 							<td style="border: 1px solid;">'.date("Y-m-d",strtotime($row['fecha_registro'])).'</td><td style="border: 1px solid;">'.date("Y-m-d",strtotime($row['fecha_nota'])).'</td>
			// 						</tr>
			// 					</table>

			$arrayReplaceString = array("\n", "\r");
			$row['observacion'] = str_replace($arrayReplaceString, "<br/>", $row['observacion'] );
			//======================================= ARMAMOS EL DOCUMENTO =============================================//
			$documento = "Nota Contable";

			$header = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:11px;" >
							<div style="float:left; width:35%; text-align:left;margin-left:10px;">
								<b>'.$razon_social.'</b><br>'.$tipo_documento_nombre.': <b>'.$documento_empresa.'</b><br>'.$direccion_empresa.'<br><b>Tels:</b>'.$telefonos.'<br>'.$ubicacion_empresa.'<br>'.$tipo_regimen.'<br>
							</div>
							<div style="float:right;width:60%;text-align:center;">
								<b>'.$titulo.'</b><br><div style="font-size:18px;font-weight:bold;">'.$labelConsecutivo.' '.$consecutivo.'</div>
								Fecha del Documento '.$row['fecha_nota'].'
								<b></b>
							</div>
							<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;">


								<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
										<div style="float:left; width:21%;"><b>Tercero:</b></div>
										<div style="float:left; width:60%;">'.$row["tercero"].' </div>
								</div>
								<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
										<div style="float:left; width:21%;"><b>Sucursal:</b></div>
										<div style="float:left; width:60%;">'.$row["sucursal"].' </div>
								</div>
								<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
										<div style="float:left; width:21%;"><b>Elaborado por:</b></div>
										<div style="float:left; width:60%;">'.$row["usuario"].' </div>
								</div>

							</div>

						</div>

							'.$headCuentas;

			$contenido='<div style="width:100%; font-style:normal; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
							'.$articulos.'
							</div>

							<div style="width:100%; font-style:normal; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000;">
								<div style="float:left; width:565px; padding-left:2px; padding-top:5px; " ><b>TOTALES</b></div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px; text-align:right;" >'.number_format($acumDebito,$_SESSION['DECIMALESMONEDA']).'</div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px; text-align:right;" >'.number_format($acumCredito,$_SESSION['DECIMALESMONEDA']).'</div>
							</div>

							<div style=" float:left">
							<!--
							<table align="right" style="width:40%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse;" >
								<tr >
									<td colspan="3" align="center" bgcolor="#B9BABF" style="border: 1px solid;"><b>SUMAS IGUALES</b></td>
								</tr >

								<tr >
									<td style="border: 1px solid;" ><b>Debito </b></td><td style="border: 1px solid;" >$</td> <td style="border: 1px solid;text-align:right;"> '.number_format ($acumDebito,$_SESSION['DECIMALESMONEDA']).'</td>
								</tr>
								<tr >
									<td style="border: 1px solid;"><b>Credito </b></td><td style="border: 1px solid;" >$</td><td style="border: 1px solid;text-align:right;">'.number_format ($acumCredito,$_SESSION['DECIMALESMONEDA']).' </td>
								</tr>

							</table>
							-->
							<br>

							<div style="overflow: hidden; width:100%; margin:5px 5px 20px 0px; padding:0px 7px 0px 0px; font-size:12px;">
								<div style="float:left; width:90%; margin:5px 5px 0px 10px;">
									Observaciones
								</div>
								<div style="float:left; width:100%; margin:3px 200px 5px 10px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;">
									'.$row['observacion'].'
								</div>
							</div>
						</div>
						';

	}
	// echo $contenido;exit;
	$texto= $contenido;

	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){
		list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
	}else{
		$MS = 64;$MD = 10;$MI = 15;$ML = 10;
	}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}

	if($IMPRIME_PDF){
		include("../../../../misc/MPDF54/mpdf.php");
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

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLHeader(utf8_encode($header));
		$mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){
			$mpdf->Output($documento.".pdf",'D');   	///OUTPUT A ARCHIVO
		}else{
			$mpdf->Output($documento.".pdf",'I');		///OUTPUT A VISTA
		}
		exit;
	}else{
		echo $texto;
	}




?>