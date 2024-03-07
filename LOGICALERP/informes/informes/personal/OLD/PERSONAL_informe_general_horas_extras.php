<?php 
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    
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


<div id="listadoHorasExtras" style="float:left; margin: 10,10,10,10;" >
	
	<br />
	<table border="0" cellspacing="3" >
		<tr>
			<td width="10"> </td>
			<td colspan="11" align="center" bgcolor="#CDDBF0"><h2>INFORME DE GENERAL DE HORAS EXTRAS DESDE  <?php echo $fec1 ?>  HASTA  <?php echo  $fec2  ?></h2></td>
		</tr>
		<tr >
			<td width="5"> </td>
			<td width="100" bgcolor="#CDDBF0">Cedula</td>
			<td width="400" bgcolor="#CDDBF0">Nombres y Apellidos</td>
			<td width="100" bgcolor="#CDDBF0">No. H.E.D.</td>
			<td width="100" bgcolor="#CDDBF0">Valor H.E.D.</td>
			<td width="100" bgcolor="#CDDBF0">No. H.E.N.</td>
			<td width="100" bgcolor="#CDDBF0">Valor H.E.N.</td>
			<td width="100" bgcolor="#CDDBF0">No. H.E.D.F.</td>
			<td width="100" bgcolor="#CDDBF0">Valor H.E.D.F</td>
			<td width="100" bgcolor="#CDDBF0">No. H.E.N.F.</td>
			<td width="100" bgcolor="#CDDBF0">Valor H.E.N.F</td>
			<td width="100" bgcolor="#CDDBF0">Total</td>
		</tr>
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
			
			$sql = "SELECT documento,nombre FROM empleados WHERE activo = '1' AND id_sucursal = '$id_sucursal' GROUP BY documento ORDER BY apellido1";
			//echo $sql;
			$emp_act = mysql_query($sql); 
			while($row=mysql_fetch_array($emp_act)){
				cargaHorasExtras($row[documento],$row[nombre],$fec1,$fec2,$valor_diurna,$valor_nocturna,$valor_diurna_fest,$valor_nocturna_fest);
			}

		?>
	</table>
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
			
			echo 	'<tr>
						<td> </td>
						<td bgcolor="#CCC">'.$id.'</td>
						<td bgcolor="#CCC">'.$nombres.'</td>
						<td bgcolor="#EEE">'.$no.'</td>
						<td bgcolor="#DDD">$'.number_format ($no * $valor_diurna).'</td>
						<td bgcolor="#EEE">'.$to.'</td>
						<td bgcolor="#DDD">$'.number_format ($to * $valor_nocturna).'</td>
						<td bgcolor="#EEE">'.$fc.'</td>
						<td bgcolor="#DDD">$'.number_format ($fc * $valor_diurna_fest).'</td>
						<td bgcolor="#EEE">'.$fn.'</td>
						<td bgcolor="#DDD">$'.number_format ($fn * $factor_nocturna_fest).'</td>
						<td bgcolor="#EEE">$'.number_format( ($fn * $factor_nocturna_fest)+($fc * $valor_diurna_fest)+($to * $valor_nocturna)+($no * $valor_diurna)).'</td>
					</tr>';	
		}
	}
?>