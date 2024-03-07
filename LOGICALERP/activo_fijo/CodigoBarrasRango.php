<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	if(isset($desde)&&isset($hasta)&&isset($bodega)){
				
				$cont=0;
				$texto=" ";
				
				$SQL = "SELECT * FROM activos_fijos 
						WHERE 
						 ".$campo." between '".$desde."' AND '".$hasta."' AND id_empresa=".$_SESSION['EMPRESA']." AND activo=1 AND estado=1";
				
				$consul=mysql_query($SQL,$link);
				
				while($row = mysql_fetch_array($consul)){ 

						$largoCodigo = strlen($row['code_bar']);
						$CodeType = 'C128C';
						if($largoCodigo == 10){$CodeType = 'C128C';}
						if($largoCodigo == 9){$CodeType = 'C128A';}
				
					$cont++;
					if($cont>1){$texto .= '<pagebreak />';}
					$texto .= '
								<div style="width:190px; text-align:center; font-size:9px; font-family:Arial; margin: 0 0 4px 0"><b>'.$row['empresa'].'</b></div>			
								<div style="width:190px; text-align:center; margin: 0 0 0 0">
									<barcode code="'.$row['code_bar'].'" type="'.$CodeType.'" size="0.9" height="0.7" class="barcode"/>
								</div>
								<div style="width:190px; text-align:center; font-size:9px; font-family:Arial; margin: 0 0 4px 0">'.$row['code_bar'].'</div>
								<div style="width:190px; text-align:center; font-size:9px; font-family:Arial">'.$row['nombre_equipo'].'</div>	
							';
				}
								
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
				$mpdf->WriteHTML(utf8_encode($texto));
				$mpdf->Output($documento.".pdf",'I');   	///OUTPUT A VISTA
	
		
	}else{
?>

	<iframe style="" src="CodigoBarrasRango.php?desde=<?php echo $limite_inferior ?>&hasta=<?php echo $limite_superior ?>&bodega=<?php echo $filtro_ubicacion_origen ?>&campo=<?php echo $campo ?>" width="385" height="268" frameborder="0" scrolling="no"></iframe>

<?php		
	}
?>