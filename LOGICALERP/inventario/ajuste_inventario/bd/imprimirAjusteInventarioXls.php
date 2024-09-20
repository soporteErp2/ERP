<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    if (!isset($_SESSION['EMPRESA'])) { exit; }

    $id_empresa     = $_SESSION['EMPRESA'];
    $nombre_empresa = mysql_result(mysql_query("SELECT nombre FROM empresas WHERE id = $id_empresa",$link),0,"nombre");
    
    //DEBITO CREDITO FECHA SELECCIONADA
    $sqlAjusteInventario   = "SELECT consecutivo,
                                    fecha_documento
                            FROM inventario_ajuste 
                            WHERE id=$id
                            AND activo=1
                            AND id_empresa=$id_empresa";
    $queryComprobante  = mysql_query($sqlAjusteInventario,$link);
    if(!$queryComprobante){echo mysql_error();}
    $consecutivo       = mysql_result($queryComprobante,0,'consecutivo');
    $fecha_documento   = mysql_result($queryComprobante,0,'fecha_documento');



    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Ajuste_inventario_$fecha_documento.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ob_start();


    $sqlDetalle = "SELECT
                        codigo,
                        nombre_unidad_medida,
                        nombre,
                        cantidad_inventario,
                        cantidad,
                        costo_unitario
                    FROM inventario_ajuste_detalle
                    WHERE activo = 1
                        AND id_ajuste_inventario = $id";

    $queryDetalles = mysql_query($sqlDetalle,$link);

    $bodyTable    = '';
    while($row = mysql_fetch_array($queryDetalles)){
        $ajuste = $row['cantidad']-$row['cantidad_inventario'];

        $bodyTable .= '<tr>
                            <td width="235">'.$row['codigo'].'</td>
                            <td width="235">'.$row['nombre'].'</td>
                            <td width="100">'.$row['nombre_unidad_medida'].'</td>
                            <td width="100">'.number_format($row['cantidad_inventario'],2,$separador_decimales,$separador_miles).'</td>
                            <td width="90">'.number_format($row['cantidad'],2,$separador_decimales,$separador_miles).'</td>
                            <td width="90">'.number_format($row['costo_unitario'],2,$separador_decimales,$separador_miles).'</td>
                            <td width="90">'.number_format($ajuste,2,$separador_decimales,$separador_miles).'</td>
                            <td width="90">'.number_format($ajuste*$row['costo_unitario'],2,$separador_decimales,$separador_miles).'</td>
                        </tr>';
    }

    //$bodyTable = '<tr>
    //                    <td width="235">'.$tercero.'</td>
    //                    <td width="100">&nbsp;</td>
    //                    <td width="235"><b>'.$codigoCuenta.'</b> '.$detalleCuenta.'</td>
    //                    <td width="90" style="text-align:right;">0</td>
    //                    <td width="90" style="text-align:right;">'.number_format($saldoCuentaCruce,$_SESSION['DECIMALESMONEDA']).'</td>
    //                </tr>'.$bodyTable;
?>
<style>
	.my_informe_Contenedor_Titulo_informe{
        float       : left;
        width       : 100%;
        margin      : 0 0 10px 0;
        font-size   : 11px;
        font-family : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
	}
	.my_informe_Contenedor_Titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
        float         :	left;
        width         :	210px;
        padding       :	0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
        float     :	left;
        width     :	100%;
        font-size : 16px;
	}
    .defaultFont{ font-size : 11px; }
    .my_informe_Contenedor_Titulo_informe td{ padding-left : 2px; }
    .tablaPiePagina td{ padding-left : 2px; }

    td{
        font-size   : 11px;
        font-family :"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
    }
</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table  style="text-align:center;margin-left: auto; margin-right: auto; font-size:12px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $nombre_empresa; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
        </table>
        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:40%;">
            <table>
                <tr>
                    <td style="font-size: 15px;font-weight: bold;">AJUSTE DE INVENTARIO No.</td>
                    <td style="font-size: 15px;"><?php echo $consecutivo; ?></td>
                </tr>
                <?php echo $numero_cheque; ?>
                <tr>
                    <td>FECHA: <?php echo fecha_larga($fecha_documento); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <br>

    </div>

    <div class="my_informe_Contenedor_Titulo_informe">
        <table class="defaultFont" style="border-collapse: collapse;">
            <tr style="background-color:#000;">
                <td width="235" style="color:#fff;">CODIGO</td>
                <td width="235" style="color:#fff;">NOMBRE</td>
                <td width="100" style="color:#fff;">UNIDAD MEDIDA</td>
                <td width="90" style="text-align:center; color:#fff;">CANT. INV</td>
				<td width="90" style="text-align:center; color:#fff;">CANT. REAL</td>
				<td width="90" style="text-align:center; color:#fff;">COSTO</td>
				<td width="90" style="text-align:center; color:#fff;">AJUSTE</td>
				<td width="90" style="text-align:center; color:#fff;">COSTO AJUSTE</td>
            </tr>
            <?php echo $bodyTable; ?>
        </table>
    </div>

</body>

<?php

	$texto = ob_get_contents(); ob_end_clean();

    echo $texto;

?>