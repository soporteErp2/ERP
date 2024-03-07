<?php 
    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');
	ob_start();
	echo '<link rel="stylesheet" type="text/css" href="../../../../temas/clasico/informes.css"/>';
	
	//echo $MyInformeFiltroFechaInicio.'?????';

	$id_empresa = $_SESSION['EMPRESA'];

	$whereVenc = " AND EC.estado = '$MyInformeFiltro_0'";
	
?>

<?php
	$nombre_empresa	 = mysql_result(mysql_query("SELECT * FROM empresas WHERE id = $MyInformeFiltroEmpresa",$link),0,"nombre");
	$nombre_sucursal = mysql_result(mysql_query("SELECT * FROM empresas_sucursales WHERE id = $MyInformeFiltroSucursal",$link),0,"nombre");	
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
	    white-space             : nowrap;
        overflow                : hidden;
        text-overflow           : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
		float				:	left;
		width				:	370px;
		font-size			:	16px;
	}
</style>
<div class="my_informe_Contenedor_Titulo_informe">
	<div style="float:left; width:1015px">
        <div style="float:left;width:100%">
            <div style="width:100%; border-bottom:1px solid #CCC; font-weight:bold; font-size:18px; text-align:left;"><?php echo $nombre_informe ?></div>
        </div>
        <div style="float:left;width:100%">
            <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Inicial</div>
            <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaInicio)?></div>    
        </div>
        <div style="float:left;width:100%">
            <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Final</div>
            <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaFinal)?></div>    
        </div>
        <div style="float:left;width:100%">
            <div class="my_informe_Contenedor_Titulo_informe_label">Empresa</div>
            <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombre_empresa?></div>    
        </div>    
        <div style="float:left;width:100%">
            <div class="my_informe_Contenedor_Titulo_informe_label">Sucursal</div>
            <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombre_sucursal?></div>    
        </div>
            <div style="float:left;width:100%">
            <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Generacion</div>
            <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga_hora_m(date('Y-m-d H:s:i'))?></div>    
        </div>
	</div>
    <div style="float:right; width:200px">
    </div>
</div>


<div id="listadoHorasExtras" style="float:left; margin:0; width:100%; font-size:12px; font-family:Arial, Helvetica, sans-serif;" >
	<br>
	<table width="1015" border="0" cellspacing="0" cellpadding="0" valign="top" style="font-size:11px;">
        <tr>
            <td style="font-weight:bold;" width="85">DOCUMENTO</td>
            <td style="font-weight:bold;" width="255">FUNCIONARIO</td>
            <td style="font-weight:bold;" colspan="235">CARGO</td>
            <td style="font-weight:bold;" width="75">F. INICIO</td>
            <td style="font-weight:bold;" width="75">F. FINALIZA</td>
            <td style="font-weight:bold;" width="105">CONTRATO</td>
            <td style="font-weight:bold;" width="185">CENTRO COSTOS</td>            
        </tr>
    </table>

<?php
	$sql2="SELECT
				EC.documento_empleado,
				EC.nombre_empleado,
				EC.fecha_inicio_contrato,
				EC.fecha_fin_contrato,
				EC.tipo_contrato,
				EC.cargo,
				EC.nombre_centro_costos
			FROM 
				 empleados_contratos AS EC						
			WHERE
				EC.activo = 1				
				AND EC.fecha_inicio_contrato >= '$MyInformeFiltroFechaInicio' 
				AND EC.fecha_fin_contrato <= '$MyInformeFiltroFechaFinal'
				AND EC.id_empresa = '$id_empresa'
				$whereVenc
			ORDER BY EC.nombre_empleado";
	//echo $sql2;
	$resultado = mysql_query($sql2);		
	
	while($row = mysql_fetch_array($resultado)){
		echo '  <table width="1015" border="0" cellspacing="0" cellpadding="0" valign="top" style="font-size:11px;">	
					<tr>
						<td width="85">'.$row['documento_empleado'].'</td>
						<td width="255">'.$row['nombre_empleado'].'</td>		
						<td width="235">'.$row['cargo'].'</td>						
						<td width="75" >'.$row['fecha_inicio_contrato'].'</td>
						<td width="75" >'.$row['fecha_fin_contrato'].'</td>
						<td width="105" >'.$row['tipo_contrato'].'</td>	
						<td width="185" >'.$row['nombre_centro_costos'].'</td>			
					</tr>
				</table>';		
	}	
?>
		
</div>

<script language="JavaScript" type="text/JavaScript">

</script>

<?php 
   
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
///////////////////////////////////////// FUNCIONES HORAS EXTRAS DE PERSONAL /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
	
	$texto = $revision_actual =  ob_get_contents(); ob_end_clean();

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}
	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
	if($IMPRIME_PDF == 'true'){
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
					10,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);				
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
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