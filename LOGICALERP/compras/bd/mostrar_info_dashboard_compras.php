<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../config_var_global.php");

    $id_empresa = $_SESSION['EMPRESA'];
    $and_sucursal   = '';
    $and_bodega     = '';

	if($bodega > 0 && $tabla != 'compras_facturas2' && $tabla != 'comprobante_egreso'){ $and_bodega = " AND id_bodega = '".$bodega."'"; }
	if($sucursal > 0){ $and_sucursal = " AND id_sucursal = '".$sucursal."'"; }

	$andGlobal = " consecutivo > 0 AND activo = 1 AND id_empresa = '$id_empresa'".$and_bodega.$and_sucursal;

    //si son canceladas o en edicion
	if($opc == 'cancel'){ $andConsulta = " AND estado = 3"; }
	else{ $andConsulta = " AND estado = 0"; }

    //si es factura de compra o por cuentas
    $factura_por_cuentas = '';

    if($tabla == 'compras_facturas1' || $tabla == 'compras_facturas2'){
    	if($tabla == 'compras_facturas1'){
    	   	$factura_por_cuentas = " AND factura_por_cuentas = 'false' AND id_saldo_inicial = 0 ORDER BY id_sucursal,id_bodega";
    	   	$tabla = 'compras_facturas';
    	}
    	else{
    	   	$factura_por_cuentas = " AND factura_por_cuentas = 'true' AND id_saldo_inicial = 0 ORDER BY id_sucursal";
    	   	$tabla = 'compras_facturas';
    	}
    }

    $where_fechas = " AND fecha_inicio BETWEEN '$fechai' AND '$fechaf'";


    $orderBy = '';
    if($tabla == 'comprobante_egreso'){
        $orderBy      = " ORDER BY id_sucursal";
        $where_fechas = " AND fecha_inicial BETWEEN '$fechai' AND '$fechaf'";
    }
    if($tabla == 'compras_ordenes'){ $orderBy = " ORDER BY id_sucursal,id_bodega"; }

    $sql   = "SELECT * FROM ".$tabla." WHERE ".$andGlobal.$andConsulta.$where_fechas.$factura_por_cuentas.$orderBy;
    //echo $sql;
    $query = mysql_query($sql,$link);

    if($tabla == 'compras_facturas'){
        $campo1 = 'prefijo_factura';
        $campo2 = 'consecutivo';
        $campo3 = 'numero_factura';
        $total  = 'total_factura';
        $total1 = 'TOTAL';
    }
    else{
        $campo1 = 'consecutivo';
        $campo2 = '';
        $campo3 = '';
        $total  = '';
        $total1 = '';
    }

    if($tabla == 'comprobante_egreso'){ $tercero = 'tercero'; $nit = 'nit_tercero'; }
    else{ $tercero = 'proveedor'; $nit = 'nit'; }

    //---------------------------------redimensionar ventana------------------------------------------

    if($tabla == 'compras_ordenes'||$tabla == 'comprobante_egreso' || $tabla == 'compras_entrada_almacen' || $tabla == 'compras_requisicion'){
        echo '<script>Win_Ventana_Info.setWidth(490);</script>';
        $text_align = 'text-align:left;';
    }
    else{ echo '<script>Win_Ventana_Info.setWidth(700);</script>'; }

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

		$body .= '<div style="width:600px; overflow:hidden; margin-left:'.$margin_left.'">
    	            <div style="width:100px; float:left;">'.$row[$campo1].$row[$campo3].'</div>
                    <div style="width:80px; float:left; margin-left:5px;">'.$row[$campo2].'</div>
                    <div style="float:left; width:100px; margin-left:5px; text-align:right;">'.$row[$nit].'</div>
    				<div style="float:left; width:200px; margin-left:5px;">'.$row[$tercero].'</div>';

        if($tabla == 'compras_facturas'){
			$body .= '<div style="float:left; margin-left:5px; width:100px; text-align:right;">$ '.$row[$total].'</div>';
        }

        $body .= '</div>';
	}

    $head = '<div class="EmpSeparador" style="overflow:hidden;">';

    $width = '100px;';
    if ($tabla == 'compras_facturas'){
        $head        .= '<div style="float:left; margin-left:'.$margin_left.'; width:100px;">NUMERO</div>';
        $margin_left = '5px;';
        $width       = '80px;';
    }

    $head .= '<div style="float:left; margin-left:'.$margin_left.' width:'.$width.'">CONSECUTIVO</div>
                <div style="float:left; margin-left:5px; width:100px; text-align:center;">NIT</div>
                <div style="float:left; margin-left:5px; width:200px;">TERCERO</div>';

    if ($tabla == 'compras_facturas'){
        $head .= '<div style="float:left; text-align:right; width:100px;">VALOR</div>';
    }

    $head .='</div>';


    echo '<div style="width:100%; overflow:hidden;" class="informacion_dash_board">'.$head.$body.'</div>';

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