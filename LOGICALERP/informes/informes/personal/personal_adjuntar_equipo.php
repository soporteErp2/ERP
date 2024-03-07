<?php
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	//include("../../../../configuracion/define_variables_debug.php");
	
	$texto='<link rel="stylesheet" type="text/css" href="../../../../temas/clasico/informes.css"/>';		

	
	/*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/
	
	$SQL = "SELECT * FROM empleados WHERE activo=1 AND id=". $id_empleado_inventario;
	//$texto.= $SQL;		
	$consul =mysql_query($SQL,$link);
	while($row = mysql_fetch_array($consul)){
			
	$texto.='
			<div style="float:inherit; width:100%; font-size:12px">
				<div style="float:left; width:780px">
			        <div style="float:left; width:100%; margin:0 0 30px 0">
			            <div style="font-weight:bold; font-size:16px; text-align:center;">ASIGNACION DE INVENTARIOS</div>
			        </div>
			        <div style="float:left;width:100%">
			            <div class="my_informe_Contenedor_Titulo_informe_label">Nombre</div>
			            <div class="my_informe_Contenedor_Titulo_informe_detalle">'.$row['nombre'].'</div>    
			        </div>
			        <div style="float:left;width:100%">
			            <div class="my_informe_Contenedor_Titulo_informe_label">Empresa</div>
			            <div class="my_informe_Contenedor_Titulo_informe_detalle">'.$row['empresa'].'</div>    
			        </div>    
			        <div style="float:left;width:100%">
			            <div class="my_informe_Contenedor_Titulo_informe_label">Sucursal</div>
			            <div class="my_informe_Contenedor_Titulo_informe_detalle">'.$row['sucursal'].'</div>    
			        </div>
			        <div style="float:left;width:100%">
			            <div class="my_informe_Contenedor_Titulo_informe_label">Fecha</div>
			            <div class="my_informe_Contenedor_Titulo_informe_detalle">'.fecha_larga_hora_m(date('Y-m-d H:s:i')).'</div>    
			        </div>
				</div>
			</div><br><br>
		';
	}
	/*--------------------------------------------------CUERPO DEL INFORME---------------------------------------------------*/

	$inventariado_empleado='<div style=" width:100%; width:100%; font-size:12px">
								<div style=" width:100%;">
									<div style=" width:30%; float:left; font-weight:bold;">Fecha</div>
									<div style=" width:16%; float:left; font-weight:bold;">Hora</div>
									<div style=" width:16%; float:left; font-weight:bold;">Codigo</div>
									<div style=" width:35%; float:left; font-weight:bold;">Nombre del Equipo</div>
								</div><br>
							';
						

	$SQL = "SELECT * FROM inventarios WHERE id_usuario_encargado=". $id_empleado_inventario;
	//$texto.= $SQL;		
	$i=0;
	$consul =mysql_query($SQL,$link);

	while($row = mysql_fetch_array($consul)){
		$i++;	
		$inventariado_empleado.='
								<div style=" width:100%;">
									<div style=" width:30%; float:left;">
										'.fecha_larga($row['fecha_asignacion_usuario']).'
									</div>
									<div style=" width:16%; float:left;">
										'.hora($row['hora_asignacion_usuario']).'
									</div>
									<div style=" width:16%; float:left;">
										'.$row['codigo'].'
									</div>
									<div style=" width:35%; float:left;">
										'.$row['nombre_equipo'].'
									</div>
								</div>
							';
	}

	if($i==0){

		$inventariado_empleado.='<div style=" width:100%;">No tiene inventarios asignados</div>';

	}	

	$inventariado_empleado.='</div><br>';
	
	$texto .= $inventariado_empleado;

	
	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){
		list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
	}else{
		$MS = 18;$MD = 10;$MI = 15;$ML = 10;
	}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
	
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
		$mpdf->SetHeader("");
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