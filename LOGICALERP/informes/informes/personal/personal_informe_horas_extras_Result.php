<?php 
    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');
	ob_start();
	echo '<link rel="stylesheet" type="text/css" href="../../../../temas/clasico/informes.css"/>';
	
	//echo $MyInformeFiltroFechaInicio.'?????';
	
?>

<?php
	$nombre_empresa	 = mysql_result(mysql_query("SELECT * FROM empresas WHERE id = $MyInformeFiltroEmpresa",$link),0,"nombre");
	$nombre_sucursal = mysql_result(mysql_query("SELECT * FROM empresas_sucursales WHERE id = $MyInformeFiltroSucursal",$link),0,"nombre");	
?>
<div class="my_informe_Contenedor_Titulo_informe">
	<div style="float:left; width:540px">
        <div style="float:left;width:100%">
            <div class="my_informe_Contenedor_Titulo_informe_label">Informe</div>
            <div class="my_informe_Contenedor_Titulo_informe_detalle" style="font-weight:bold"><?php echo $nombre_informe ?></div>
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


<div id="listadoHorasExtras" style="float:left; margin:0; width:100%; font-size:11px; font-family:Arial, Helvetica, sans-serif;" >
	<div style="float:left;width:100%; font-weight:bold; border-bottom:1px solid #AAA">
        <div style="float:left;width:90px">Documento</div>
        <div style="float:left;width:155px">Funcionario</div>
        <div style="float:left;width:55px">M.E.D.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">M.E.N.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">M.E.N.F.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">M.E.N.F.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">Total</div>
   </div> 
		<?php		
			cargaHorasExtras($MyInformeFiltroSucursal,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal);
		?>
</div>

<script language="JavaScript" type="text/JavaScript">

</script>

<?php 
   
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
///////////////////////////////////////// FUNCIONES HORAS EXTRAS DE PERSONAL /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
	function cargaHorasExtras($sucursal,$fechai,$fechaf){	
		
		////	EXTRACCION DE REGISTROS DE LLEGADAS Y SALIDAS
		$sql2="SELECT
					documento,
					nombre1,
					apellido1,
					min(empleados_horas_extras.fechai) as fechai,
					max(empleados_horas_extras.fechas) as fechas,
					sum(empleados_horas_extras.med) as med,
					sum(empleados_horas_extras.med_valor) as med_valor,
					sum(empleados_horas_extras.men) as men,
					sum(empleados_horas_extras.men_valor) as men_valor,
					sum(empleados_horas_extras.medf) as medf,
					sum(empleados_horas_extras.medf_valor) as medf_valor,
					sum(empleados_horas_extras.menf) as menf,
					sum(empleados_horas_extras.menf_valor) as menf_valor,
					sum(empleados_horas_extras.total) as total
				FROM 
					 empleados_horas_extras
				INNER JOIN 
					empleados on empleados_horas_extras.cedula=empleados.documento
				WHERE 
					empleados_horas_extras.total!=0 AND
					empleados.id_sucursal=$sucursal AND
					fechai BETWEEN '$fechai' AND '$fechaf'
				GROUP BY documento";
		//echo $sql2;
		$resultado = mysql_query($sql2);		
		
		while($row = mysql_fetch_array($resultado)){
			echo 	'	<div style="float:left;width:100%; border-bottom:1px solid #AAA">
							<div style="float:left;width:90px; ">'.$row['documento'].'</div>
							<div style="float:left;width:155px;  white-space:nowrap; text-overflow:ellipsis; overflow:hidden">'.$row['nombre1']." ".$row['apellido1'].'</div>
							<div style="float:left;width:55px; background-color:#EEE">'.$row['med'].'</div>
							<div style="float:left;width:55px; background-color:#EEE">$'.number_format ($row['med_valor']).'</div>
							<div style="float:left;width:55px; ">'.$row['men'].'</div>
							<div style="float:left;width:55px; ">$'.number_format ($row['men_valor']).'</div>
							<div style="float:left;width:55px; background-color:#EEE">'.$row['medf'].'</div>
							<div style="float:left;width:55px; background-color:#EEE">$'.number_format ($row['medf_valor']).'</div>
							<div style="float:left;width:55px; ">'.$row['menf'].'</div>
							<div style="float:left;width:55px; ">$'.number_format ($row['menf_valor']).'</div>
							<div style="float:left;width:55px; background-color:#EEE">$'.number_format($row['total']).'</div>
					   </div> ';		
		}		
	}
	
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