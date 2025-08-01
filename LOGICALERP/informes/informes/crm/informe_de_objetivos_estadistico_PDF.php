<?php
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	include('../../../../misc/libchart1.3/libchart/classes/libchart.php');	

	ob_start();	

	/*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/
	$fechai = $MyInformeFiltroFechaInicio;
	$fechaf = $MyInformeFiltroFechaFinal;	

	$nombre_informe = 'Informe estadistico de Objetivos';
	$nombre_empresa = $_SESSION['NOMBREEMPRESA'];

	if($IMPRIME_PDF == 'true'){
    	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		$stringURL = '';
		$PathImg   = '../../../';
    }
    else{
		$stringURL = '?'.rand();
		$PathImg   = '../';
    } 
	
?>

<style>
	.my_informe_Contenedor_Titulo_informe{
		float				:	left;
		width				:	100%;
		border-bottom		:	1px solid #CCC;
		margin				:	0 0 10px 0;
		font-size			:	11px;
		font-family			:	Verdana, Geneva, sans-serif
	}
	.my_informe_Contenedor_Titulo_informe_label{
		float				:	left;
		width				:	130px;
		font-weight			:	bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
		float				:	left;
		width				:	210px;
		padding				:	0 0 0 5px;
	    white-space         : 	nowrap;
        overflow            : 	hidden;
        text-overflow       : 	ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
		float				:	left;
		width				:	370px;
		font-size			:	16px;
	}
	.InfCajaRedondeada{
		border				: 	1px solid #CCC;
		border-radius		:	5px;
	}
	.InfTituloBloque{
		float				:	left;
		width				:	100%;
		font-size			:	12px;
		font-weight			:	bold;
		margin				: 	10px 0 0 0;
		background			: 	#333;
		color				: 	#fff;
		padding				:	0 0 0 3px;
	}
	.InfListadoTitulo{
		float				:	left;
		font-weight			:	bold;
	}
	.InfListado{
		float				:	left;
		font-weight			:	normal;

	}
	.CortaLargoTexto	{
		white-space         : 	nowrap;
        overflow            : 	hidden;
        text-overflow       : 	ellipsis;
	}	
	
	.amcharts-export-menu-top-right {
	  top: 10px;
	  right: 0;
	}

	.tabla td{
        border : 1px solid #c6c6c6;
        padding: 0px 5px;
    }

    .tabla > table{
        border-collapse: collapse;
        font-size: 14px;
        font-weight:bold;
    }
</style>

<!-- --------------------------   DESARROLLO DEL INFORME  ------------------------------------ -->
<!-- ----------------------------------------------------------------------------------------- -->

<body style="font-size:11px; font-family:Verdana, Geneva, sans-serif;" class="tabla">

    <htmlpageheader  class="SoloPDF" name="MyHeaderInforme">
        <div style="text-align:right;font-size:8px">
            <?php echo $nombre_informe.'  |  '.$nombre_empresa.'  |  '.$_SESSION["NOMBREFUNCIONARIO"].'  |  '.fecha_larga_hora_m(date('Y-m-d H:s:i')); ?>  |   Paginas({PAGENO} de {nb})
        </div>
    </htmlpageheader>
    <sethtmlpageheader name="MyHeaderInforme" show-this-page="1" value="on"></sethtmlpageheader>

    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left;width:100%">
            <div style="width:100%; border-bottom:1px solid #CCC; font-weight:bold; font-size:18px; text-align:left;"><?php echo $nombre_informe ?></div>
        </div>
        <div style="float:left; width:370px">
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Inicial</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaInicio)?></div>
            </div>
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Final</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaFinal)?></div>
            </div>            
      	</div>
        <div style="float:left; width:370px;">
            <div style="float:left;width:100%; text-align:center">
                <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
            </div>
        </div>
    </div>

<?php
	
	/*--------------------------------------------------ESTADO DE PROYECTO---------------------------------------------------*/

	$consul = $mysql->query("SELECT
							 	count(*) AS cantidad,
							 	estado_proyecto,
							 	sum(valor) AS total
							 FROM
							 	crm_objetivos
							 WHERE
							    activo = 1
							 AND id_empresa = '$_SESSION[EMPRESA]'
							 AND fecha_creacion BETWEEN '$fechai 00-00-00'
							 AND '$fechaf 23:59:59'	
							 GROUP BY
							 	id_estado",$link);	

	$objetivos  = $mysql->num_rows($consul);

	$rowTable1 .= '<tr style="background: #aaccf6;">
    				  <td  style="">Estado</td><td  style="">Proyectos</td><td  style="">Valor</td>
    			  </tr>';

	$chart   = new VerticalBarChart(700, 350);
	$chart->getConfig()->setUseMultipleColor(true);
	$chart->getPlot()->getPalette()->setBarColor(array(
		new Color(38,142,189),
		new Color(190,86,161),
		new Color(245,92,7),
		new Color(94,189,20),
		new Color(233,44,28)		

	));
	$dataSet = new XYDataSet();	
	
	while($row1 = $mysql->fetch_array($consul)){

		if($row1['estado_proyecto'] == ''){
			$row1['estado_proyecto'] = 'Ninguno';
		}

		$dataSet->addPoint(new Point($row1['estado_proyecto'],$row1['cantidad']));

	    $rowTable1 .= '<tr style="background: #dfe8f6;">
	    				  <td style="">'.$row1['estado_proyecto'].'</td><td style="">'.$row1['cantidad'].'</td><td style="">$ '.$row1['total'].'</td>
	    			  </tr>';
		
	}

	$chart->setDataSet($dataSet);
	$chart->setTitle("");
	$chart->render("graficos/objetivos_grafico_estados.png");
	
?>
		<div class="InfTituloBloque">Estado del Proyecto</div>
        <div class="" style="float:left; width:100%; margin: 10px 0 0 0;">            
            <?php if($objetivos>0){ ?>             
                <img src="<?php echo $PathImg ?>/informes/informes/crm/graficos/objetivos_grafico_estados.png<?php echo $stringURL; ?>">
                <div id="" class="tabla">
                	<table style="">
                		<?php echo $rowTable1 ?>
                	</table>
                </div>
            <?php } ?>
        </div>

<?php        

	/*--------------------------------------------------LINEAS DE NEGOCIO---------------------------------------------------*/

	$consul = $mysql->query("SELECT
							 	count(*) AS cantidad,
							 	linea_negocio,
							 	sum(valor) AS total
							 FROM
							 	crm_objetivos
							 WHERE
							 	activo = 1
							 AND id_empresa = '$_SESSION[EMPRESA]'
							 AND fecha_creacion BETWEEN '$fechai 00-00-00'
							 AND '$fechaf 23:59:59'	
							 GROUP BY
							 	id_linea",$link);	

	$lineas  = $mysql->num_rows($consul);	

	$rowTable2 .= '<tr style="background: #aaccf6;">
    				  <td  style="">Linea</td><td  style="">Proyectos</td><td  style="">Valor</td>
    			  </tr>';

    $chart   = new VerticalBarChart(700, 350);
    $chart->getConfig()->setUseMultipleColor(true);
	$chart->getPlot()->getPalette()->setBarColor(array(
		new Color(38,142,189),
		new Color(190,86,161),
		new Color(245,92,7),
		new Color(94,189,20),
		new Color(233,44,28)		
				
	));
	$dataSet = new XYDataSet();	
	
	while($row2 = $mysql->fetch_array($consul)){

		if($row2['linea_negocio'] == ''){
			$row2['linea_negocio'] = 'Ninguno';
		}

		$dataSet->addPoint(new Point($row2['linea_negocio'],$row2['cantidad']));

		$rowTable2 .= '<tr style="background: #dfe8f6;">
	    				  <td style="">'.$row2['linea_negocio'].'</td><td style="">'.$row2['cantidad'].'</td><td style="">$ '.$row2['total'].'</td>
	    			  </tr>';	
		
	}

	$chart->setDataSet($dataSet);
	$chart->setTitle("");
	$chart->render("graficos/objetivos_grafico_lineas.png");
	
?>
		<div class="InfTituloBloque">Lineas de Negocio</div>
        <div class="" style="float:left; width:100%; margin: 10px 0 0 0;">            
            <?php if($lineas>0){ ?>                
                <img src="<?php echo $PathImg ?>/informes/informes/crm/graficos/objetivos_grafico_lineas.png<?php echo $stringURL; ?>">
                <div id="" class="tabla">
                	<table style="">
                		<?php echo $rowTable2 ?>
                	</table>
                </div>
            <?php } ?>
        </div>   


<?php
	/*--------------------------------------------------PROBABILIDAD DE EXITO---------------------------------------------------*/

	$consul = $mysql->query("SELECT
							 	count(*) AS cantidad,
							 	probabilidad_exito,
							 	sum(valor) AS total
							 FROM
							 	crm_objetivos
							 WHERE
							 	activo = 1
							 AND id_empresa = '$_SESSION[EMPRESA]'
							 AND fecha_creacion BETWEEN '$fechai 00-00-00'
							 AND '$fechaf 23:59:59'							 
							 GROUP BY
							 	probabilidad_exito",$link);	

	$probabilidades  = $mysql->num_rows($consul);		

	$rowTable3 .= '<tr style="background: #aaccf6;">
    				  <td  style="">Probabilidad</td><td  style="">Proyectos</td><td  style="">Valor</td>
    			  </tr>';

	$chart   = new PieChart(700, 400);
	$chart->getConfig()->setUseMultipleColor(true);
	$chart->getPlot()->getPalette()->setPieColor(array(
		new Color(38,142,189),
		new Color(190,86,161),
		new Color(245,92,7),
		new Color(94,189,20),
		new Color(233,44,28)		
				
	));
	$dataSet = new XYDataSet();
	
	$acumCant  = 0;
	$acumValor = 0;
	while($row3 = $mysql->fetch_array($consul)){

		if($row3['probabilidad_exito'] == ''){			
			$acumCant  += $row3['cantidad'];
			$acumValor += $row3['total'];		

			continue;			
		}

		$dataSet->addPoint(new Point($row3['probabilidad_exito'].' ('.$row3['cantidad'].')', $row3['cantidad']));

	    $rowTable3 .= '<tr style="background: #dfe8f6;">
	    				  <td style="">'.$row3['probabilidad_exito'].'</td><td style="">'.$row3['cantidad'].'</td><td style="">$ '.$row3['total'].'</td>
	    			  </tr>';
		
	}

	if($acumCant > 0){
		
		$rowTable3 .= '<tr style="background: #dfe8f6;">
	    				  <td style="">Ninguna</td><td style="">'.$acumCant.'</td><td style="">$ '.$acumValor.'</td>
	    			  </tr>';
	}

	$chart->setDataSet($dataSet);
	$chart->setTitle("");
	$chart->render("graficos/objetivos_grafico_probabilidades.png");
	
?>			
		<div style="padding-top: 20px">&nbsp;</div>
		<div class="InfTituloBloque">Probabilidad de Exito</div>
        <div class="" style="float:left; width:100%; margin: 10px 0 0 0;">            
            <?php if($probabilidades > 0){ ?>                
                <img src="<?php echo $PathImg ?>/informes/informes/crm/graficos/objetivos_grafico_probabilidades.png<?php echo $stringURL; ?>">
                <div id="" class="tabla">
                	<table style="">
                		<?php echo $rowTable3 ?>
                	</table>
                </div>
            <?php } ?>
        </div>   


<?php	
	/*-----------------------------------------------------------------------------------------------------------------------*/
?>
</body>

<!-- ---------------------------------  FIN DEL INFORME  ------------------------------------- -->
<!-- ----------------------------------------------------------------------------------------- -->
<?php
	$texto = $revision_actual =  ob_get_contents(); ob_end_clean();

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}
	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
	if($IMPRIME_PDF == 'true'){
		$documento = 'informe estadistico de objetivos';
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
					'utf-8',  		// mode - default ''
					$HOJA,			// format - A4, for example, default ''
					12,				// font size - default 0
					'',				// default font family
					$MI,			// margin_left
					$MD,			// margin right
					$MS,			// margin top
					$ML,			// margin bottom
					3,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ('Informe SIIP' );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{	$mpdf->Output($documento.".pdf",'I');}
		exit;
	}else{
		echo $texto;
	}
?>