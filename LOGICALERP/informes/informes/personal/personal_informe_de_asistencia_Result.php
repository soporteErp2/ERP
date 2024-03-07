<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();
	echo '<link rel="stylesheet" type="text/css" href="../../../../temas/clasico/informes.css"/>';

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

    $emp_act = mysql_query("SELECT * FROM empleados WHERE activo = '1' AND id_sucursal = '$id_sucursal' ORDER By apellido1 ",$link);
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
      if($dia1 < 10){$ldia1 = substr($dia1, 1);
      }else{$ldia1 = $dia1;}


      if($dia2 < 10){$ldia2 = substr($dia2, 1);
      }else{$ldia2 = $dia2;}

      ?>

    <table class="defaultFont" border="1" cellpadding="0" cellspacing="0" style="width:740px; white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
      <tr class="defaultFont">
        <th width="69">CEDULA</th>
        <th width="155">NOMBRE</th>
        <?php for($i=$ldia1;$i<=$ldia2;$i++){
        echo "<th width='30'>$i</th>\n";} //dibuja los titulos
        $z=1;
        ?>
      </tr>

      <?php while($row=mysql_fetch_array($emp_act)){
      echo '<tr class="defaultFont" style="font-size:10px;">
      <td>'.$row[documento].'</td>
      <td>'.$row[apellido1].' '.$row[nombre1].'</td>';
      $z++;
      $c_dia=mysql_query("SELECT fecha,DATE_FORMAT(hora,'%H:%i') as hora FROM empleados_registro WHERE cedula = '$row[documento]' AND fecha BETWEEN '$fec1' AND '$fec2' AND tipo = 'in' ORDER BY fecha ASC",$link);
      //echo "SELECT DATE_FORMAT(hora,'%H:%i') as hora FROM empleados_registro WHERE cedula = '$row[documento]' AND fecha BETWEEN '$fec1' AND '$fec2' AND tipo = 'in' ORDER BY fecha ASC";
      $p=0;$ct=0;
      $wfecha ='';$whora='';
      while($frow=mysql_fetch_array($c_dia)){
            $wfecha[$p] = $frow[fecha];
            $whora[$p] = $frow[hora];
            $extraregistro[$p] = $frow[extraregistro];
            $p++;
            $obs[$p] = $frow[observacion];
      }
      mysql_free_result($c_dia);
      for($t=$ldia1;$t<=$ldia2;$t++){
            $cm = $ct + 1;
            $tdia = ($t < 10) ? "0$t" : "$t";
            if($wfecha[$ct] == "$anio1-$mes1-$tdia") {
                if($whora[$ct]>'07:00'){$elcolor = '#F00';}else{$elcolor = '#000';}
                if ($whora[$ct]>"$row[entrada]" OR $extraregistro[$ct] == 1){echo '<td><div align="center" ><font color="'.$elcolor .'" >'.$whora[$ct].'</font></div></td>';
                }else{echo '<td><div align="center">'.$whora[$ct].'</div></td>';}
                $ct++;
            }else{echo '<td>&nbsp;</td>';}
        }
      echo '</tr>';
       }

       ?>
    </table>
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