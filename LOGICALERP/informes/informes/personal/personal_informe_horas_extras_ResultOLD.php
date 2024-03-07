<?php 
    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');
	ob_start();
	echo '<link rel="stylesheet" type="text/css" href="../../../../temas/clasico/informes.css"/>';
	
	//echo $MyInformeFiltroFechaInicio.'?????';
	
	$desde			=	$MyInformeFiltroFechaInicio;
    $hasta			=	$MyInformeFiltroFechaFinal;
	$id_sucursal	=	$MyInformeFiltroSucursal;
    
    $fec1 = $desde;
    $fec2 = $hasta;
    
    $split1 = explode('-', $desde);
    $split2 = explode('-', $hasta);
    
    $anio1 = $split1[0];
    $anio2 = $split2[0];
    $mes1 = $split1[1];
    $mes2 = $split2[1];
    $dia1 = $split1[2];
    $dia2 = $split2[2];    
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
        <div style="float:left;width:55px">H.E.D.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">H.E.D.F.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">H.E.N.F.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">H.E.N.F.</div>
        <div style="float:left;width:55px">$</div>
        <div style="float:left;width:55px">Total</div>
   </div> 
		<?php 
				
			////	REVISA LA TABLA SACA SALARIO MINIMO
				$sql3 		= "SELECT salario_minimo FROM configuracion_global";
				//echo $sql3."<br />";
				$sal 		= mysql_query($sql3);
				$row1 		= mysql_fetch_array($sal);
				$valor_min 	= $row1['salario_minimo']/30/8/60; ///SACA VALOR DEL MIN.
				
			////	REVISA LA TABLA SACA FACTOR DE VALORIZACION DE HORAS EXTRAS NOCTURNAS Y DOMINICALES
				$sql4 		= "SELECT * FROM configuracion_factor_horas_extras";
				//echo $sql4."<br />";
				$config		= mysql_query($sql4);
				while ($row2 = mysql_fetch_array($config)){
					switch ($row2['tipo']) {
					
							case "H.E.DIURNA": 
								$factor_diurna 			= $row2['valor'];
								$valor_diurna			= $factor_diurna*$valor_min;
								break;
								
							case "H.E.NOCTURNA": 
								$factor_nocturna 		= $row2['valor'];
								$valor_nocturna			= $factor_nocturna*$valor_min;
								break;
								
							case "H.E.DIURNA FEST": 
								$factor_diurna_fest 	= $row2['valor'];
								$valor_diurna_fest		= $factor_diurna_fest*$valor_min;
								break;
								
							case "H.E.NOCTURNA FE": 
								$factor_nocturna_fest 	= $row2['valor'];
								$valor_nocturna_fest	= $factor_nocturna_fest*$valor_min;
								break;							
						}
				}
			
			$sql = "SELECT documento,apellido1,nombre1 FROM empleados WHERE activo = '1' AND id_sucursal = '$id_sucursal' GROUP BY documento ORDER BY apellido1";
			//echo $sql;
			$emp_act = mysql_query($sql); 
			while($row=mysql_fetch_array($emp_act)){
				cargaHorasExtras($row['documento'],$row['apellido1'].' '.$row['nombre1'],$fec1,$fec2,$valor_diurna,$valor_nocturna,$valor_diurna_fest,$valor_nocturna_fest);
			}

		?>
</div>

<script language="JavaScript" type="text/JavaScript">

</script>

<?php 
   
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
///////////////////////////////////////// FUNCIONES HORAS EXTRAS DE PERSONAL /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function fecha($valor){
		list($anio,$mes,$dia)=split('-', $valor); 
		$man = date("w",mktime(0,0,0,$mes,$dia,$anio));
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"); 
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
		$fec = $dias[$man].' '.$dia.' de '.$meses[$mes-1].' '.$anio;
		return $fec;
	} 

	function fich($valor){
		list($hor,$min,$seg) = split(":", $valor);
		$fer = date("h:i a",mktime($hor,$min,$seg,10,10,2005));
		return $fer;
	}

	function puntos($horae,$fechaE,$hora,$fechaS,$cl){
		list($ano,$mes,$dia) = split('-', $fechaS); 
		list($hor,$min,$sec) = split(':',$hora); 
		$extra = mktime($hor,$min,$seg,$mes,$dia,$ano); //unix salida
		list($eanio,$emes,$edia) = split('-', $fechaE); 
		switch($cl){
			case 1:
				$prin = mktime(19,00,00,$emes,$edia,$eanio); //unix parte
				$ti = round(($extra - $prin)/60);
				//echo $ti;
				if($ti >=180){
					$tot = 180;
				}else if($ti>0){
					$tot = $ti;
				}else{
					$tot = $ti;
				}
			break;
			case 2:
				$limite = mktime(22,00,00,$emes,$edia,$eanio); //unix parte
				$mi = round(($extra - $limite)/60);
				if($mi < 0){$tot = 0;}else{$tot = $mi;}
			break;
		}
		return $tot;
	}
	
	function cargaHorasExtras($id,$nombres,$fechai,$fechaf,$valor_diurna,$valor_nocturna,$valor_diurna_fest,$factor_nocturna_fest){	
		
		////	EXTRACCION DE REGISTROS DE LLEGADAS Y SALIDAS
		$sql2="SELECT * FROM vista_horas_extras WHERE cedula = $id AND fechaE BETWEEN '$fechai' AND '$fechaf'";
		//echo $sql2;
		$resultado = mysql_query($sql2);
		global $fc,$fn,$to,$no;
		$fc=0;$fn=0;$to=0;$no=0;
		
		$fes = "SELECT * FROM tfestivo ORDER BY dia ASC ";
		$fet = mysql_query($fes);
		$l = 1;
		while($fest=mysql_fetch_array($fet)){ //pone matriz de fecha festiva
			$festivo[$l] = $fest[dia];
			$l++;
		}
		
		if(mysql_num_rows($resultado)){
			while($row = mysql_fetch_array($resultado)){
				list($san,$sme,$sdi) = split('-',$row['fechaE']);
				$vari = array_search($row['fechaE'], $festivo);
				$do = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],1);
				$lo = puntos($row['horaE'],$row['fechaE'],$row['horaS'],$row['fechaS'],2);			
				if($vari){ //Festivo pue
					$fc = $fc + $do;
					$fn = $fn + $lo;
				}else{
					$dm = date("w",mktime(0,0,0,$sme,$sdi,$san));  //// IDENTIFICA LOS DOMINGOS 
					if($dm==0){
						$fc = $fc + $do;
						$fn = $fn + $lo;
					}else{
						$to = $to + $lo;
						$no = $no + $do;
					}
				}				
			}
			
			echo 	'	<div style="float:left;width:100%; border-bottom:1px solid #AAA">
							<div style="float:left;width:90px; ">'.$id.'</div>
							<div style="float:left;width:155px;  white-space:nowrap; text-overflow:ellipsis; overflow:hidden">'.$nombres.'</div>
							<div style="float:left;width:55px; background-color:#EEE">'.$no.'</div>
							<div style="float:left;width:55px; background-color:#EEE">$'.number_format ($no * $valor_diurna).'</div>
							<div style="float:left;width:55px; ">'.$to.'</div>
							<div style="float:left;width:55px; ">$'.number_format ($to * $valor_nocturna).'</div>
							<div style="float:left;width:55px; background-color:#EEE">'.$fc.'</div>
							<div style="float:left;width:55px; background-color:#EEE">$'.number_format ($fc * $valor_diurna_fest).'</div>
							<div style="float:left;width:55px; ">'.$fn.'</div>
							<div style="float:left;width:55px; ">$'.number_format ($fn * $factor_nocturna_fest).'</div>
							<div style="float:left;width:55px; background-color:#EEE">$'.number_format( ($fn * $factor_nocturna_fest)+($fc * $valor_diurna_fest)+($to * $valor_nocturna)+($no * $valor_diurna)).'</div>
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