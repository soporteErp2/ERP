<?php

    include_once('../../../configuracion/conectar.php');
    include_once('../../../configuracion/define_variables.php');
    if (!isset($_SESSION['EMPRESA'])) { exit; }

    $id_empresa     = $_SESSION['EMPRESA'];
    $nombre_empresa = mysql_result(mysql_query("SELECT nombre FROM empresas WHERE id = $id_empresa",$link),0,"nombre");
    
    //DEBITO CREDITO FECHA SELECCIONADA
    $sqlAjusteInventario   = "SELECT consecutivo,
                                    fecha_inicio
                            FROM ventas_remisiones 
                            WHERE id=$id
                            AND activo=1
                            AND id_empresa=$id_empresa";
    $queryComprobante  = mysql_query($sqlAjusteInventario,$link);
    if(!$queryComprobante){echo mysql_error();}
    $consecutivo       = mysql_result($queryComprobante,0,'consecutivo');
    $fecha_documento   = mysql_result($queryComprobante,0,'fecha_inicio');



    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Ajuste_inventario_$fecha_documento.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ob_start();


    $sqlDetalle = "SELECT
                        codigo,
                        nombre_unidad_medida,
                        nombre,
                        cantidad,
                        costo_unitario
                    FROM ventas_remisiones_inventario
                    WHERE activo = 1
                        AND id_remision_venta = $id";

    $queryDetalles = mysql_query($sqlDetalle,$link);

    $bodyTable    = '';
    while($row = mysql_fetch_array($queryDetalles)){

        $bodyTable .= '<tr>
                            <td width="235">'.$row['codigo'].'</td>
                            <td width="235">'.$row['nombre'].'</td>
                            <td width="100">'.$row['nombre_unidad_medida'].'</td>
                            <td width="90">'.number_format($row['cantidad'],2,$separador_decimales,$separador_miles).'</td>
                            <td width="90">'.number_format($row['costo_unitario'],2,$separador_decimales,$separador_miles).'</td>
                            <td width="90">'.number_format(1*$row['cantidad']*$row['costo_unitario'],2,$separador_decimales,$separador_miles).'</td>
                        </tr>';
    }

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
                    <td style="font-size: 15px;font-weight: bold;">REMISION DE VENTA No.</td>
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
				<td width="90" style="text-align:center; color:#fff;">CANTIDAD</td>
				<td width="90" style="text-align:center; color:#fff;">COSTO</td>
				<td width="90" style="text-align:center; color:#fff;">COSTO TOTAL</td>
            </tr>
            <?php echo $bodyTable; ?>
        </table>
    </div>

</body>

<?php

	$texto = ob_get_contents(); ob_end_clean();

    echo $texto;

?>