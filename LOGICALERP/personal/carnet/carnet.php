<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	
	///EXTRAE LA IMAGEN Y LA PONE EN EL TEMP
	$result = mysql_query("SELECT fondo,fondo_ext FROM configuracion_carnet WHERE id = $id_carnet",$link);
	$consul2 = mysql_query("SELECT * FROM configuracion_carnet_datos WHERE id_carnet = $id_carnet",$link);
	$image  = mysql_result($result,0,'fondo');
	if($image != ''){
		$ext  = mysql_result($result,0,'fondo_ext');
		$ImgName = '../../../ARCHIVOS_PROPIOS/temp/CarnetTempPdf.'.$ext;
		$file = fopen($ImgName,"w+");
		fwrite($file,$image);
		fclose($ImgName);
	}
	///////////////////////////////////////	
	
	$consul3 = mysql_query("SELECT * FROM vista_carnet WHERE id = $id",$link);
	$row3 = mysql_fetch_array($consul3);
	$image2  = $row3['foto'];
	if($image2 != ''){
		$ext2  = 'jpg';
		$ImgName2 = '../../../ARCHIVOS_PROPIOS/temp/CarnetFotoTempPdf.'.$ext2;
		$file2 = fopen($ImgName2,"w+");
		fwrite($file2,$image2);
		fclose($ImgName2);
	}
	
	



	include('../../../misc/tcpdf/config/lang/spa.php');
	include('../../../misc/tcpdf/tcpdf.php');
	

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetMargins(0, 0, 0);
	$pdf->SetAutoPageBreak(TRUE, 0);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	$pdf->SetHeaderData('', '', '');
	$pdf->setPrintHeader(false);
	$pdf->SetHeaderMargin(0);
	
	//$pdf->setImageScale(0.47);
	$pdf->SetDisplayMode('real', 'SinglePage', 'UseOutlines');
	
	$ORIENTACION = 'P';
	$tamano_hoja = array(number_format(205/3.8,2),number_format(325/3.8,2));
	$pdf->AddPage($ORIENTACION,$tamano_hoja); 

	
	$pdf->Image('../../../ARCHIVOS_PROPIOS/temp/CarnetTempPdf.'.$ext, 0, 0, $tamano_hoja[0], $tamano_hoja[1], '', '', 'T', false, 300, '', false, false, 1, false, false, false);	
	//$pdf->MultiCell($width, $height, 'xxxxxxxx', 0, 'C', 0, 1, $left, $top);
	
	while($row = mysql_fetch_array($consul2)){
		
			$width	= substr($row['width'], 0, -2)/3.8;
			$height	= substr($row['height'], 0, -2)/3.8;
			$top	= substr($row['top'], 0, -2)/3.8;
			$left	= substr($row['left'], 0, -2)/3.8;	
			
			if($row['campo'] == 'Fotografia'){
				$pdf->Image('../../../ARCHIVOS_PROPIOS/temp/CarnetFotoTempPdf.jpg', $left, $top, $width, $height, '', '', 'T', false, 300, '', false, false, 1, false, false, false);	
			}else{
				if($row['codebar']=='true'){
					$style = array(
								'position' => 'C','border' => false,'padding' => 0,	'fgcolor' => array(0,0,0),
								'bgcolor' => false, 'text' => false,'font' => 'helvetica','fontsize' => 8,'stretchtext' => 0
							  );
					$pdf->write1DBarcode(utf8_encode($row3[CampoBd($row['campo'])]), 'C128A', $left, $top, $width, $height, 0.4, $style, 'N');					
				}else{
					$color = HexToRGB($row['color']);
					$pdf->SetFont('Helvetica','B',$row['font']);
					$pdf->SetTextColor($color[r],$color[g],$color[b]);
					$pdf->MultiCell($width, $height, utf8_encode($row3[CampoBd($row['campo'])]), 0, 'C', 0, 1, $left, $top);
				}
			}
	}
		
	//$pdf->AddPage();
	$pdf->Output('example_061.pdf', 'I');
	
	
	
	function CampoBd($value){
	  if($value == 'Nombre Completo'){return 'nombre';}
	  if($value == 'Primer Nombre'){return 'nombre1';}
	  if($value == 'Nombres'){return 'nombres';}
	  if($value == 'Primer Apellido'){return 'apellido1';}
	  if($value == 'Apellidos'){return 'apellidos';}
	  if($value == 'Cargo'){return 'cargo';}
	  if($value == 'Fotografia'){return 'foto';}
	  if($value == 'Identificacion'){return 'documento';}
	}

	function HexToRGB($hex) {
		$hex = ereg_replace("#", "", $hex);
		$color = array();
		$color['r'] = hexdec(substr($hex, 0, 2));
		$color['g'] = hexdec(substr($hex, 2, 2));
		$color['b'] = hexdec(substr($hex, 4, 2));
		return $color;
	}	
?>