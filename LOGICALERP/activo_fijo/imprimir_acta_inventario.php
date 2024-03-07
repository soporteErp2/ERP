<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	//Validacion para que solo se visualizen registros de la empresa en la que el usuario le logeo
	if ($_SESSION['EMPRESA']=='') {
		return;
	}else if ($filtro_empresa!=$_SESSION['EMPRESA']) {
	 	return;
	 }

	 //verificamos si el acta parcial es total o es entre un periodo de tiemp

	 if ($opc=="imprimirActaParcialBetween") {

	 	//si el acta es en un determinado tiempo se ejecuta este script
	 	 $SQL = "SELECT code_bar,nombre_equipo,grupo,subgrupo,centro_costos,bodega FROM activos_fijos WHERE activo=1 AND nombre_equipo<>'' AND estado=1 AND (fecha_creacion_en_inventario BETWEEN '$fecha_ini' AND '$fecha_fin') AND id_empresa=".$_SESSION['EMPRESA']." ";
	 	 $titulo=' ACTA DE ACTIVOS FIJOS <br>Desde <b>'.$fecha_ini.'</b> Hasta <b>'.$fecha_fin.'</b><br>Imprime: <b>'.$_SESSION['NOMBREFUNCIONARIO'].'</b>';;
	 }else{

	 	//si no es en determinado tiempo se ejecuta este, donde se muestran todo los articulos de todas las fechas, incluyendo si el usuario altera la url con los datos, se tomara este script
	 	 $SQL = "SELECT code_bar,nombre_equipo,grupo,subgrupo,centro_costos,bodega FROM activos_fijos WHERE activo=1 AND nombre_equipo<>'' AND estado=1 AND id_empresa=".$_SESSION['EMPRESA']." ";
	 	 $titulo=' ACTA DE ACTIVOS FIJOS <br>'.date("d-m-Y").'<br>Imprime: <b>'.$_SESSION['NOMBREFUNCIONARIO'].'</b>';
	 }

	//consultar el nombre de la empresa
	$consulta_nombre=mysql_query("SELECT nombre FROM empresas WHERE id=".$_SESSION['EMPRESA'],$link);
	$nombre_empresa=mysql_fetch_array($consulta_nombre);




	//========================================================= CONSULTAMOS LOS ARTICULOS REGISTRADOS DE LA EMPRESA ===============================================================//



	$consul=mysql_query($SQL,$link);
	if (!$consul){die('no valido informe'.mysql_error());}
	$estilo='background-color: #DFDFDF;';
	while($row = mysql_fetch_array($consul)){


		//generamos el estilo de las filas, para darle fodo de color cada linea de por medio

		if ($estilo!='') {
			$estilo='';
		}else{
			$estilo='background-color: #DFDFDF;';
		}
	//verificamos si el codigo de barras esta en blanco para poner un espacio en blansco y conservar la estructura de la tabla

		if ($row['code_bar']!=0 || $row['code_bar']!='') {
			$code_bar=$row['code_bar'];
		}else{
			$code_bar="&nbsp;";
		}

	//Consultamos el valor del impuesto para cada articulo

		$sqlIva="SELECT impuesto,valor FROM impuestos WHERE id=".$row["id_impuesto"];
		$queryIva=mysql_query($sqlIva,$link);
		$iva=mysql_result($queryIva,0,'impuesto');
		$valorIva=mysql_result($queryIva,0,'valor');

		if ($valorIva!=0 || $valorIva!='') {
			$mostrarIva=''.$iva.'('.$valorIva.' % )';
		}else{
			$mostrarIva='&nbsp;';
		}


				$articulos.='<div style="'.$estilo.'">

								<div style="float:left; width:55px; padding-left:2px; text-align:center;" > '.$code_bar.' </div>
								<div style="float:left; width:200px; padding-left:20px; padding-top:5px;" > '.$row["nombre_equipo"].' </div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" > '.$row["grupo"].'</div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" > '.$row["subgrupo"].'</div>
								<div style="float:left; width:70px; padding-left:2px; text-align:center" > '.$row["departamento"].'</div>
								<div style="float:left; width:70px; padding-left:20px; padding-top:5px;" > '.$row["centro_costos"].' </div>
								<div style="float:left; width:70px; padding-left:20px; padding-top:5px;" > '.$row["bodega"].' </div>

							</div>';



		}

	//============================================================= ARMAMOS EL DOCUMENTO ===================================================================//
			$documento = "Acta Activos Fijos ".date("d-m-Y");

			$header = '<div id="body_pdf" style="width:780px; font-style:normal; font-size:11px; height:10%;m" >
							<div style="float:left; width:90%; margin:0px 10px 20px 20px; text-align:center; font-weight:bold;">
								<div style="float:right; width:60px;"></div>
								'.$nombre_empresa["nombre"].'<br> '.$titulo.'<br>

							</div><br>

							<div style="width:100%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse; text-align:center; font-weight:bold;" >ARTICULOS REGISTRADOS A LA FECHA</div>
						</div>

							<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; background-color: #CDCDCD;">

								<div style="float:left; width:55px; padding-left:2px; text-align:center;" >Codigo de<br>Barras</div>
								<div style="float:left; width:200px; padding-left:20px; padding-top:5px;" >Articulo</div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" >Grupo</div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" >Subgrupo</div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" >Depto </div>
								<div style="float:left; width:70px; padding-left:20px; padding-top:5px;" >Centro Costos</div>
								<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" >Bodega</div>

							</div>';

			$contenido='<div style="width:100%; font-style:normal; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
							'.$articulos.'
							<br/>
							</div>
						';



	$texto= $contenido;


	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){
		list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
	}else{
		$MS = 47;$MD = 10;$MI = 15;$ML = 10;
	}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}

	if($IMPRIME_PDF){
		include("../../misc/MPDF54/mpdf.php");
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
		$mpdf->SetHTMLHeader($header);
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