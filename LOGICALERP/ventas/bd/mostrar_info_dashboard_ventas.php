<?php
  	include("../../../configuracion/conectar.php");
  	include("../../../configuracion/define_variables.php");
  	include("../config_var_global.php");

      $id_empresa = $_SESSION['EMPRESA'];

      //echo $bodega.$sucursal;
    if ($opc='no_send') {
      facturasPendientesDian($id_empresa,$sucursal,$bodega,$mysql);
      exit;
    }

  	if($bodega == 0){

  		  $bodega = '';
  	}
  	else{
  		  $bodega = " AND id_bodega = '".$bodega."'";
  	}
  	if($sucursal == 0){

  		  $sucursal = '';
  	}
  	else{
  		  $sucursal = " AND id_sucursal = '".$sucursal."'";
  	}

    $where_fechas = " AND fecha_inicio BETWEEN '$fechai' AND '$fechaf'";
  	//echo $opc.'<br>'.$tabla;
    if($tabla == 'recibo_caja'){
        $andGlobal    = " activo = 1 AND id_empresa = $id_empresa".$sucursal;
        $where_fechas = " AND fecha_inicial BETWEEN '$fechai' AND '$fechaf'";
    }
    else{
        $andGlobal = " activo = 1 AND id_empresa = $id_empresa".$bodega.$sucursal;
    }

      //si son canceladas o en edicion

  	if($opc == 'cancel'){

             $andConsulta = " AND estado = 3";

  	}
  	else{
             $andConsulta = " AND estado = 0";
  	}

    $orderBy = " ORDER BY id_sucursal,id_bodega";

    if($tabla == 'ventas_facturas'){

           $campo1         = 'numero_factura_completo';

           $total          = 'total_factura';

           $andConsecutivo = ' AND numero_factura > 0 AND id_saldo_inicial = 0';
           //$total1 = 'TOTAL';


    }
    else{

           $campo1 = 'consecutivo';

           $andConsecutivo = ' AND consecutivo > 0';

           $total = '';
           //$total1 = '';

    }


    $sql1 = "SELECT * FROM ".$tabla." WHERE ".$andGlobal.$andConsecutivo.$andConsulta.$where_fechas.$orderBy;
    //echo $sql1.'<br>';
    $query = mysql_query($sql1,$link);

    if($tabla == 'ventas_facturas'){

           $campo1 = 'numero_factura_completo';
           $total  = 'total_factura';

    }
    else{

           $campo1 = 'consecutivo';
           $total  = '';
    }
    if($tabla == 'recibo_caja'){

        $tercero = 'tercero';
        $nit     = 'nit_tercero';
    	  //echo $tercero;
    }
    else{
          $tercero = 'cliente';
          $nit     = 'nit';
    }

    $id_bodega1   = '';
    $id_sucursal1 = '';

    if($tabla != 'ventas_facturas'){
           $campoConsec = '80px';
           echo '<script>
           				Win_Ventana_DashVentas.setWidth(500);
           		</script>';
           $text_align = 'text-align:left;';
    }
    else{
    	   $campoConsec = '100px';
         $text_align = 'text-align:left;';
         echo '<script>Win_Ventana_DashVentas.setWidth(680);</script>';
    }

    $body        = '';
    $margin_left = '30px;';
		while($row=mysql_fetch_array($query)){

        if($id_sucursal != $row['id_sucursal'] && $row['id_sucursal'] > 0){
            $id_sucursal = $row['id_sucursal'];
            $body .= '<br>
                    <div style="width:250px; overflow:hidden; margin-bottom:2px;">
                        <div style="float:left;margin-left:10px">
                            <img src="..\..\temas\clasico\images\BotonesTabs\empresa16.png">
                        </div>
                        <div style="width:180px;float:left;margin-left:1px;font-size:12;font-weight:bold">'.strtoupper($row['sucursal']).'</div>
                    </div>';
        }

        if($id_bodega != $row['id_bodega'] && $row['id_bodega'] > 0){
                $margin_left = '60px;';
                $id_bodega   = $row['id_bodega'];
                $body .= '<div style="width:250px; overflow:hidden; margin-top:5px;"><div style="float:left;margin-left:30px">
                            <img src="..\..\temas\clasico\images\BotonesTabs\sucursales16.png"></div>
                            <div style="width:150px;float:left; margin-left:1px;font-style: italic;font-weight:bold">'.strtoupper($row['bodega']).'</div>
                        </div>';
        }
        if($row[$total] > 0){ $row[$total] = $row[$total] * 1; }

  			$body .= '<div style="overflow:hidden;margin-left:'.$margin_left.'">
  			            <div style="width:'.$campoConsec.';float:left;'.$text_align.'">'.$row[$campo1].'</div>
                    <div style="float:left;width:100px;text-align:right;margin-left:5px;">'.$row[$nit].'</div>
  						<div style="float:left;width:220px;margin-left:5px;">'.$row[$tercero].'</div>
  						<div style="float:left;margin-left:5px;width:100px;text-align:right">'.$row[$total].'</div>
  	             </div>';
		}

    $body .= '<br>';
    $head  = '<div class="EmpSeparador" style="overflow:hidden;">';
    $width = '100px;';

    if ($tabla == 'ventas_facturas'){
        $head       .= '<div style="float:left; margin-left:'.$margin_left.'; width:120px;">NUMERO</div>';
        $margin_left = '5px;';
        $width       = '80px;';
    }
    else{
        $head       .= '<div style="float:left; margin-left:'.$margin_left.' width:'.$width.'">CONSECUTIVO</div>';
        $margin_left = '5px;';
    }
    $head .=   '<div style="float:left; margin-left:10px; width:80px; text-align:center;">NIT</div>
                    <div style="float:left; margin-left:5px; width:200px;">TERCERO</div>';

    if ($tabla == 'ventas_facturas'){
        $head .= '<div style="float:left; text-align:right; width:100px;">VALOR</div>';
    }

    $head .='</div>';
    echo '<div style="width:100%; overflow:hidden;" class="informacion_dash_board">'.$head.$body.'</div>';

    function facturasPendientesDian($id_empresa,$id_sucursal,$id_bodega,$mysql){
      $sql="SELECT * FROM ventas_facturas_configuracion WHERE activo=1 AND id_empresa=$id_empresa AND tipo='FE' AND consecutivo_factura<numero_final_resolucion ";
      $query=$mysql->query($sql,$mysql->link);
      while ($row=$mysql->fetch_array($query)) {
        $whereIdResolucion .= ($whereIdResolucion=='')? "id_configuracion_resolucion=$row[id]" : " OR id_configuracion_resolucion=$row[id] " ;
      }

      $sql="SELECT * FROM ventas_facturas
          WHERE activo=1
          $filtro_sucursal
          $filtro_bodega
          AND id_empresa=$id_empresa
          AND estado=1
          AND ($whereIdResolucion)
          AND (ISNULL(response_FE) OR response_FE='')
          ";
      $query=$mysql->query($sql,$mysql->link);
      while ($row=$mysql->fetch_array($query)) {
        $bodyGrilla .= "<div class='row'>
                          <div class='cell' data-col='1'></div>
                          <div class='cell' data-col='2' title='$row[sucursal]'>$row[sucursal]</div>
                          <div class='cell' data-col='2' title='$row[bodega]'>$row[bodega]</div>
                          <div class='cell' data-col='3'>$row[fecha_inicio]</div>
                          <div class='cell' data-col='3'>$row[numero_factura_completo] </div>
                          <div class='cell' data-col='3'>$row[nit]</div>
                          <div class='cell' data-col='4' title='$row[cliente]' >$row[cliente]</div>
                        </div>";
      }

      ?>
      <style>
          /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
          .sub-content[data-position="right"]{width: 100%; height: 100%; }
          .sub-content[data-position="left"]{width: 40%; overflow:auto;}
          /*.content-grilla-filtro { height: 235px;}*/
          .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
          .content-grilla-filtro .cell[data-col="2"]{width: 90px;}
          .content-grilla-filtro .cell[data-col="3"]{width: 60px;}
          .content-grilla-filtro .cell[data-col="4"]{width: 165px;}
          .content-grilla-filtro .cell[data-col="5"]{width: 80px;}
          .content-grilla-filtro .mainText{ font-weight: bold; text-align: center;}
          /*.content-grilla-filtro .cell[data-col="6"]{width: 80px;}*/
          .sub-content [data-width="input"]{width: 120px;}

      </style>

      <div class="main-content" style="overflow-y: auto;overflow-x: hidden;" >
          <div class="sub-content" data-position="right">
              <div class="title">FACTURAS PENDIENTES POR ENVIAR A LA DIAN</div>

              <div class="content-grilla-filtro">
                  <div class="head">
                      <div class="cell" data-col="1"></div>
                      <div class="cell" data-col="2">Sucursal</div>
                      <div class="cell" data-col="2">Bodega</div>
                      <div class="cell" data-col="3">Fecha</div>
                      <div class="cell" data-col="3">Numero </div>
                      <div class="cell" data-col="3">Nit</div>
                      <div class="cell" data-col="4">Tercero</div>
                  </div>
                  <div class="body" id="body_grilla_filtro">
                    <?php echo $bodyGrilla; ?>
                  </div>
              </div>

          </div>

      </div>
      <?php

    }

?>
<style type="text/css">
    .informacion_dash_board .EmpSeparador {
        float: left;
        width: 100%;
        color: #333;
        padding: 2px 0 3px 5px;
        margin: 4px 0 8px -10px;
        font-weight: bold;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        -webkit-box-shadow: 1px 1px 3px #666;
        -moz-box-shadow: 1px 1px 2px #666;
        background: -webkit-linear-gradient(#FFF, #CECECE);
        background: -moz-linear-gradient(#FFF, #CECECE);
        background: -o-linear-gradient(#FFF, #CECECE);
        background: linear-gradient(#FFF, #CECECE);
    }
</style>