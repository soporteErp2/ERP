<?php 
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();
	echo '<link rel="stylesheet" type="text/css" href="../../../../temas/clasico/informes.css"/>';	
	
	$desde			=	$MyInformeFiltroFechaInicio;
    $hasta			=	$MyInformeFiltroFechaFinal;
	$id_sucursal	=	$MyInformeFiltroSucursal;
    	
    $sql1 = "	SELECT
					empleados.nombre as nombres,
					empleados.documento as documento,
					empleados_registro.fecha as fecha,
					DATE_FORMAT(hora,'%H:%i') as hora,
					empleados_registro.tipo as tipo,
					equipos_registro.nombre as equipo
				FROM 
					empleados_registro
				LEFT JOIN 
					equipos_registro on empleados_registro.id_equipo=equipos_registro.serial
				LEFT JOIN 
					empleados on empleados.documento=empleados_registro.cedula
				WHERE 
					empleados_registro.fecha BETWEEN '$desde' AND '$hasta' AND
					activo = '1' AND
					id_sucursal = '$id_sucursal'
				ORDER BY
					empleados.nombre,empleados_registro.fecha ,empleados_registro.hora ";
	//echo $sql1;
    $emp_act = mysql_query($sql1,$link); 
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

<div class="my_informe_Contenedor_Titulo_informe">

<?php 
	$id="";
	$tipo="";
	echo "<div class='defaultFont'style='border:1px solid #fff;float:left; width:740px;'>";
		while($row=mysql_fetch_array($emp_act)){			
			if($id!=$row['documento']){ // SI INICIA LOS DATOS DE OTRO EMPLEADO	
				if($tipo=="in"){
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'>-</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'>-</div>
							<div style='float:left; width:25%; text-align:center; background:#EEE;'>-</div>";
				}
				echo "	<div class='defaultFont' style='border:1px solid #fff;float:left; width:740px; height:10px;'></div>
						<div class='defaultFont' style='border:1px solid #fff;float:left; width:740px;'>					
							<div style='float:left; width:15%; background:#DDD;'>Identificacion:</div>
							<div style='float:left; width:35%; background:#DDD;'>".$row[documento]."</div>
							<div style='float:left; width:15%; background:#DDD;'>Nombre:</div>
							<div style='float:left; width:35%; background:#DDD;'>".$row[nombres]."</div>
						</div>
						<div class='defaultFont' style='border:1px solid #fff;float:left; width:740px;'>				
							<div style='float:left; text-align:center; width:50%; background:#DDD;'>ENTRADA</div>
							<div style='float:left; text-align:center; width:50%; background:#DDD;'>SALIDA</div>
						</div>";
				$id = $row['documento'];
				$tipo="";
			}
			if($row['tipo']=="in"){
				if($tipo=="in"){
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'>-</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'>-</div>
							<div style='float:left; width:25%; text-align:center; background:#EEE;'>-</div>";
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'> ".$row['fecha']."&nbsp;</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'> ".$row['hora']."&nbsp;</div>
							<div style='float:left; width:25%; background:#EEE;'> ".$row['equipo']."&nbsp;</div>";
				}else{
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'> ".$row['fecha']."&nbsp;</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'> ".$row['hora']."&nbsp;</div>
							<div style='float:left; width:25%; background:#EEE;'> ".$row['equipo']."&nbsp;</div>";
				}
				$tipo="in";
			}else
			if($row['tipo']=="out"){
				if($tipo=="in"){
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'> ".$row['fecha']."&nbsp;</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'> ".$row['hora']."&nbsp;</div>
							<div style='float:left; width:25%; background:#EEE;'> ".$row['equipo']."&nbsp;</div>";
				}else{
					
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'>-</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'>-</div>
							<div style='float:left; width:25%; text-align:center; background:#EEE;'>-</div>";
					echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'> ".$row['fecha']."&nbsp;</div>
							<div style='float:left; width:10%; text-align:center; background:#EEE;'> ".$row['hora']."&nbsp;</div>
							<div style='float:left; width:25%; background:#EEE;'> ".$row['equipo']."&nbsp;</div>";
				}
				$tipo="out";
			}
		}
	if($tipo=="in"){
		echo "	<div style='float:left; width:15%; text-align:center; background:#EEE;'>-</div>
				<div style='float:left; width:10%; text-align:center; background:#EEE;'>-</div>
				<div style='float:left; width:25%; text-align:center; background:#EEE;'>-</div>";
	}
	echo "</div>";
?>

</div>
<?php
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


