<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    if (!isset($_SESSION['EMPRESA']) ) { exit; }

    // if (isset($IMPRIME_XLS)) {
    //     if($IMPRIME_XLS=='true'){
    //         header('Content-type: application/vnd.ms-excel');
    //         header("Content-Disposition: attachment; filename=Comprobante_de_egreso.xls");
    //         header("Pragma: no-cache");
    //         header("Expires: 0");
    //     }
    // }

    ob_start();

    $id_empresa     = $_SESSION['EMPRESA'];
    $nombre_empresa = mysql_result(mysql_query("SELECT nombre FROM empresas WHERE id = $id_empresa",$link),0,"nombre");

    //DEBITO CREDITO FECHA SELECCIONADA
    $sql = "SELECT
                sucursal,
                bodega,
                consecutivo,
                fecha_registro,
                fecha_documento,
                documento_usuario,
                usuario,
                cod_tercero,
                nit,
                tercero,
                observacion,
                codigo_centro_costo,
                centro_costo,
                consecutivo_remision_venta,
                consecutivo_entrada_almacen,
                estado
            FROM inventario_ajuste
            WHERE id=$id
                AND activo=1
                AND id_empresa=$id_empresa";
    $query = $mysql->query($sql,$mysql->link);

    $sucursal                    = $mysql->result($query,0,'sucursal');
    $bodega                      = $mysql->result($query,0,'bodega');
    $consecutivo                 = $mysql->result($query,0,'consecutivo');
    $fecha_registro              = $mysql->result($query,0,'fecha_registro');
    $fecha_documento             = $mysql->result($query,0,'fecha_documento');
    $documento_usuario           = $mysql->result($query,0,'documento_usuario');
    $usuario                     = $mysql->result($query,0,'usuario');
    $cod_tercero                 = $mysql->result($query,0,'cod_tercero');
    $nit                         = $mysql->result($query,0,'nit');
    $tercero                     = $mysql->result($query,0,'tercero');
    $observacion                 = $mysql->result($query,0,'observacion');
    $codigo_centro_costo         = $mysql->result($query,0,'codigo_centro_costo');
    $centro_costo                = $mysql->result($query,0,'centro_costo');
    $consecutivo_remision_venta  = $mysql->result($query,0,'consecutivo_remision_venta');
    $consecutivo_entrada_almacen = $mysql->result($query,0,'consecutivo_entrada_almacen');
    $estado                      = $mysql->result($query,0,'estado');
    $labelCcos                   = $codigo_centro_costo.' - '.$centro_costo;

    if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }

    $sqlArticulos   = "SELECT *
                        FROM inventario_ajuste_detalle
                        WHERE id_ajuste_inventario='$id'";
    $queryArticulos = mysql_query($sqlArticulos,$link);

    $estilo = 'background-color: #EEE;';
    while ($array= mysql_fetch_array($queryArticulos)) {
        $estilo = ($estilo!='')? '': 'background-color: #EEE;';
        $cantidad_ingreso    = 0;
        $cantidad_salida     = 0;
        $cantidad_inventario = $array["cantidad_inventario"];
        $cantidad            = $array["cantidad"];


        $cantInv = abs($cantidad_inventario);
        $ajuste = $cantInv-$cantidad;
        if ($ajuste<>0) {
            $cantidad_ingreso    = ($ajuste<0)? abs($ajuste) : 0;
            $cantidad_salida     = ($ajuste>0)? abs($ajuste) : 0;
            // $ajuste = ($ajuste>0)? '-'.abs($ajuste) : '+ '.abs($ajuste);
        }
        if ($ajuste>0) { continue; }
        $bodyTable.='<tr>
                        <td style="'.$estilo.' ">'.$array["codigo"].'</td>
                        <td style="'.$estilo.' text-align:left; padding-left:5px;">'.$array["nombre"].'</td>
                        <td style="'.$estilo.' text-align:left; padding-left:5px;">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
                        <td style="'.$estilo.' text-align:right;">'.number_format ($array["cantidad_inventario"],$_SESSION['DECIMALESMONEDA']).'</td>
                        <td style="'.$estilo.' text-align:right;">'.number_format ($array["cantidad"],$_SESSION['DECIMALESMONEDA']).' </td>
                        <td style="'.$estilo.' text-align:right;">'.number_format ($array["costo_unitario"],$_SESSION['DECIMALESMONEDA']).'</td>
                        <td style="'.$estilo.' text-align:right;">'.$cantidad_ingreso.'</td>
                        <td style="'.$estilo.' text-align:right;">'.number_format ( ( abs($ajuste) * $array["costo_unitario"]) ,$_SESSION['DECIMALESMONEDA']).'</td>
                    </tr>';

        if($array["observaciones"] != ''){
            $bodyTable.='<tr style=" border-bottom:1px solid #000; border-top:1px solid;">
                            <td style="'.$estilo.' width:100%;" colspan="7">
                                <b>Observaciones</b>:<br>
                                '.str_replace("\n",'<br>',$array["observaciones"]).'
                            </td>
                         </tr>';
        }

        $totalIngresos += ($ajuste<0)? ( abs($ajuste) * $array["costo_unitario"]) : 0 ;
        $totalSalidas  += ($ajuste>0)? ( abs($ajuste) * $array["costo_unitario"]) : 0 ;
        $total         += ( abs($ajuste) * $array["costo_unitario"]);

    }


?>
<style>
    body, body td{
       font-family : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
       font-size   : 11px;
    }
	.my_informe_Contenedor_Titulo_informe{
        float       : left;
        width       : 100%;
        margin      : 0 0 10px 0;
	}

    .my_informe_Contenedor_Titulo_informe td{ padding-left : 2px; }

    .articlesTable{
        border-collapse : collapse;
        border-bottom   : 1px solid #000;
        width           : 100%;
    }

    .articlesTable td{ border-collapse : collapse; }
    .articlesTable tbody tr{ border : none; }

    .articlesTable thead td{
        text-align       : center;
        background-color : #000;
        color            : #FFF;
        height: 25px;
    }

</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->
<title>Ajuste de Inventario</title>
<body >
    <div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table  style="text-align:center;margin-left: auto; margin-right: auto; font-size:12px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $nombre_empresa; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
        </table>
        <br>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left;width:50%;">
            <table style="font-size: 12px;">
                <tr>
                    <td>NIT:</td>
                    <td><?php echo $nit; ?></td>
                </tr>
                <tr>
                    <td>Tercero:</td>
                    <td><b><?php echo $tercero; ?></b></td>
                </tr>
                <tr>
                    <td>Bodega:</td>
                    <td><?php echo $bodega; ?></td>
                </tr>
                <tr>
                    <td>Centro de Costos:</td>
                    <td><?php echo $labelCcos; ?></td>
                </tr>
            </table>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:40%;">
            <table>
                <tr>
                    <td style="font-size: 15px;font-weight: bold;">ENTRADAS DEL AJUSTE DE INVENTARIO  No.</td>
                    <td style="font-size: 15px;"><?php echo $consecutivo; ?></td>
                </tr>
                <tr>
                    <td>FECHA: <?php echo fecha_larga($fecha_documento); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <br>

    </div>

    <div class="my_informe_Contenedor_Titulo_informe">

        <!-- CUERPO DEL INFORME -->
        <table class="articlesTable">
            <thead>
                <tr>
                    <td style="">CODIGO</td>
                    <td style="">ITEM</td>
                    <td style="">UNIDAD</td>
                    <td style="text-align:right;">CANTIDAD INV.</td>
                    <td style="text-align:right;">CANTIDAD REAL</td>
                    <td style="text-align:right;">COSTO UNI.</td>
                    <td style="text-align:right;">CANT. INGRESO</td>
                    <td style="text-align:right;">SUBTOTAL</td>
                    <!-- <td style="width:80px; border-right: 1px solid;">SUBTOTAL</td> -->
                </tr>
            </thead>
            <tbody>
                <?php echo $bodyTable; ?>
            </tbody>

        </table>

        <table align="right" style="width:25%; font-style:normal; font-size:11px; margin:20px 0px 0px 0px; border-collapse:collapse;" >
            <tr >
                <td style="font-weight:bold;width:50px;">TOTAL</td>
                <td style="font-weight:bold;width:10px;">$</td>
                <td style="font-weight:bold;width:50px;text-align:right;"><?php echo number_format($total,$_SESSION['DECIMALESMONEDA']); ?></td>
            </tr>
        </table>
        <br>
        <div style="overflow: hidden; width:100%; font-size:12px;">
            <div style="float:left; width:90%; margin:5px 5px 0px 0px;">Observaciones</div>
            <div style="float:left; width:100%; margin:3px 200px 5px 0px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;"><?php echo $observacion ?></div>
        </div>
    </div>

    <!-- PIE DE PAGINA -->
    <div style="margin-top:150px; overflow:hidden; width:100%;">
        <div style="width:48%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Elaboro: <?php echo $usuario ?></b></div>
        <div style="width:48%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Reviso</b></div>
    </div>
</body>

<?php
    // exit;
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER-L'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'L'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }

	// if ($IMPRIME_PDF=='true') {

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

        $mpdf->SetProtection(array('print'));
        // $mpdf->useSubstitutions = true;
        $mpdf->simpleTables = true;
        // $mpdf->packTableData = true;
		// $mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		// $mpdf->SetDisplayMode ('fullpage');
		// $mpdf->SetHeader("");
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));

		if( $PDF_GUARDA=='true'){ $mpdf->Output("Ajuste de Inventario.pdf",'D'); }
        else{ $mpdf->Output("Ajuste de Inventario.pdf",'I'); }

        exit;
    // }
    // else{ echo $texto; }

?>
