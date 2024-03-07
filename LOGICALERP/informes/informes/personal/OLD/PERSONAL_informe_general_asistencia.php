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
    
    $emp_act = mysql_query("SELECT * FROM empleados WHERE activo = '1' AND id_sucursal = '$id_sucursal' ORDER By apellido1 ",$link); 
?>


INFORME DE ASISTENCIA DE PERSONAL<br />
DESDE  <?php echo $fec1 ?>  HASTA  <?php echo  $fec2  ?>
    
    
  <?php 
  if($dia1 < 10){$ldia1 = substr($dia1, 1);
  }else{$ldia1 = $dia1;}
  
  
  if($dia2 < 10){$ldia2 = substr($dia2, 1);
  }else{$ldia2 = $dia2;}
    
  ?>

<table class="defaultFont" border="1" cellpadding="0" cellspacing="0" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
  <tr class="defaultFont">
    <th width="69">CEDULA</th>
    <th width="280">NOMBRE</th>
    <?php for($i=$ldia1;$i<=$ldia2;$i++){
    echo "<th width='30'>$i</th>\n";} //dibuja los titulos
    $z=1;
    ?>
  </tr>
    
  <?php while($row=mysql_fetch_array($emp_act)){
  echo '<tr class="defaultFont" style="font-size:10px;">
  <td>'.$row[documento].'</td>
  <td>'.$row[apellido1].' '.$row[apellido2].' '.$row[nombre1].' '.$row[nombre2].' </td>';
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
            if ($whora[$ct]>"$row[entrada]" OR $extraregistro[$ct] == 1){echo '<td><div align="center" ><font color="#FF0000" >'.$whora[$ct].'</font></div></td>';
            }else{echo '<td><div align="center">'.$whora[$ct].'</div></td>';}
            $ct++;
        }else{echo '<td>&nbsp;</td>';}
    }
  echo '</tr>';
   }

   ?>
</table>

<script language="JavaScript" type="text/JavaScript">

</script>