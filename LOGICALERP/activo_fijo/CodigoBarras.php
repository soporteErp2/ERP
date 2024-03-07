<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	if(isset($codigo)){

				$consul = mysql_query("SELECT * FROM activos_fijos WHERE id = $codigo",$link);

				$elcodigo = mysql_result($consul,0,"code_bar");
				$empresa = mysql_result($consul,0,"empresa");
				$equipo = mysql_result($consul,0,"nombre_equipo");

				$largoCodigo = strlen($elcodigo);
				$CodeType = 'C128C';
				if($largoCodigo == 10){$CodeType = 'C128C';}
				if($largoCodigo == 9){$CodeType = 'C128A';}

				$texto = '
							<div style="width:190px; text-align:center; font-size:9px; font-family:Arial; margin: 0 0 4px 0"><b>'.$empresa.'</b></div>
							<div style="width:190px; text-align:center; margin: 0 0 0 0">
								<barcode code="'.$elcodigo.'" type="'.$CodeType.'" size="0.9" height="0.7" class="barcode"/>
							</div>
							<div style="width:190px; text-align:center; font-size:9px; font-family:Arial; margin: 0 0 4px 0">'.$elcodigo.'</div>
							<div style="width:190px; text-align:center; font-size:9px; font-family:Arial">'.$equipo.'</div>
						';
				$texto= utf8_encode($texto);

				include("../../misc/MPDF54/mpdf.php");
				$mpdf = new mPDF(
					'utf-8',   		// mode - default ''
					array(50,25),	// format - A4, for example, default ''
					12,				// font size - default 0
					'',				// default font family
					0,				// margin_left
					0,				// margin right
					1,				// margin top
					0,				// margin bottom
					0,				// margin header
					0,				// margin footer
					'P'				// L - landscape, P - portrait
				);

				//$mpdf->SetAutoPageBreak(TRUE, 15);
				$mpdf->SetTitle ( $documento );
				$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
				$mpdf->SetDisplayMode ( 'fullpage' );
				//$mpdf->SetHeader("");
				$mpdf->WriteHTML($texto);
				$mpdf->Output($documento.".pdf",'I');   	///OUTPUT A VISTA


	}else{
?>

	<iframe style="" src="CodigoBarras.php?codigo=<?php echo $elid ?>" width="385" height="268" frameborder="0" scrolling="no"></iframe>

<?php
	}
?>