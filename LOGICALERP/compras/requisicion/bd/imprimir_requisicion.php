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
                fecha_inicio,
                documento_solicitante,
                nombre_solicitante,
                observacion,
                estado,
                codigo_centro_costo,
                centro_costo,
                area_solicitante
            FROM compras_requisicion
            WHERE id=$id
                AND activo=1
                AND id_empresa=$id_empresa";
    $query = mysql_query($sql,$link);


    $sucursal              = mysql_result($query,0,'sucursal');
    $bodega                = mysql_result($query,0,'bodega');
    $consecutivo           = mysql_result($query,0,'consecutivo');
    $fecha_registro        = mysql_result($query,0,'fecha_registro');
    $fecha_inicio          = mysql_result($query,0,'fecha_inicio');
    $documento_solicitante = mysql_result($query,0,'documento_solicitante');
    $nombre_solicitante    = mysql_result($query,0,'nombre_solicitante');
    $observacion           = mysql_result($query,0,'observacion');
    $estado                = mysql_result($query,0,'estado');
    $area_solicitante      = mysql_result($query,0,'area_solicitante');
    $labelCcos             = mysql_result($query,0,'codigo_centro_costo').' - '.mysql_result($query,0,'centro_costo');

    if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
    $marcaAgua = 'false';
    if($estado==3){
        $marcaAgua = 'true';
    }

    $sqlArticulos   = "SELECT *, SUM(cantidad) AS cantidad_total
                        FROM compras_requisicion_inventario
                        WHERE id_requisicion_compra='$id'
                        GROUP BY id_inventario, id_impuesto, costo_unitario, observaciones, tipo_descuento, descuento";
    $queryArticulos = mysql_query($sqlArticulos,$link);

    $estilo = 'background-color: #EEE;';
    while ($array= mysql_fetch_array($queryArticulos)) {
        $estilo = ($estilo!='')? '': 'background-color: #EEE;';


        // consultamos la unidad del articulo
        $sqlUnidad     = 'SELECT inventario_unidades.nombre  FROM inventario_unidades INNER JOIN items ON inventario_unidades.id=items.id_unidad_medida WHERE items.id="'.$array["id_inventario"].'"';
        $queryUnidad   = mysql_query($sqlUnidad,$link);
        $unidad_nombre = mysql_result($queryUnidad,0,'nombre');

        if ($array["descuento"]>0) {
            if ($array["tipo_descuento"]=='porcentaje') {
                $temp      = ($array["cantidad_total"]*$array["costo_unitario"]);
                $descuento = ($temp*$array["descuento"])/100;
                $temp     -= $descuento;
            }
            else{ $temp = ($array["cantidad_total"]*$array["costo_unitario"])-$array["descuento"]; }
        }
        else{ $temp = ($array["cantidad_total"]*$array["costo_unitario"]); }

        $tipodesart = ($array["tipo_descuento"]=='porcentaje')? '%': $tipodesart='$';
        if ($array["descuento"]==0 || $array["descuento"]==0.00) { $tipodesart='&nbsp;&nbsp;'; }

        $bodyTable.='<tr>
                        <td style="'.$estilo.' width: 55px;">'.$array["codigo"].'</td>
                        <td style="'.$estilo.' width: 230px; text-align:left; padding-left:5px;">'.$array["nombre"].'</td>
                        <td style="'.$estilo.' width: 80px; text-align:left; padding-left:5px;">'.$array["nombre_unidad_medida"].' x '.$array["cantidad_unidad_medida"].'</td>
                        <td style="'.$estilo.' width: 60px; text-align:right;">'.$array["cantidad_total"].'</td>
                    </tr>';

        if($array["observaciones"] != ''){
            $bodyTable.='<tr style=" border-bottom:1px solid #000; border-top:1px solid;">
                            <td style="'.$estilo.' width:100%;" colspan="7">
                                <b>Observaciones</b>:<br>
                                '.str_replace("\n",'<br>',$array["observaciones"]).'
                            </td>
                         </tr>';
        }


        //========================= INICIO DEL CALCULO FACTURA DE COMPRA ==========================//

        $cantidad  = $array["cantidad_total"];
        $descuento = $array["descuento"];
        $costo     = $array["costo_unitario"];
        $tipoDesc  = $array["tipo_descuento"];
        $iva       = $array["valor_impuesto"];

        $subtotal         = 0;
        $descuentoTotal   = 0;
        $descuentoMostrar = 0;

        $subtotal=($cantidad*$costo);

        //verificar el tipo de descuento del articulo, si es en porcentaje se hace el sgt calculoa para convertir en pesos el descuento
        $descuento = ($tipoDesc=='porcentaje')? ($subtotal*$descuento)/100 : $descuento;
        $subtotal  = $subtotal- $descuento;
        $iva       = ($iva*$subtotal)/100;

        $subtotalFactura = $subtotal+$subtotalFactura;  //ACUMULADO SUBTOTAL
        $ivaFactura      = $ivaFactura+$iva;            //ACUMULADO IVA

        $arrayIvaDocumento[$array['id_impuesto']]+=$iva;
        $arrayDatosIva[$array['id_impuesto']]= array('nombre' => $array['impuesto'],'valor_impuesto'=> $array['valor_impuesto']*1, );

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
<title>Entrada de Almacen</title>
<body >
    <div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table  style="text-align:center;margin-left: auto; margin-right: auto; font-size:12px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $nombre_empresa; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
            <tr><td>Bodega: <?php echo $bodega; ?></td></tr>
        </table>
        <br>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left;width:50%;">
            <table style="font-size: 12px;">
                <tr>
                    <td>SOLICITANTE:</td>
                    <td><b><?php echo $nombre_solicitante; ?></b></td>
                </tr>
                <tr>
                    <td>DOCUMENTO:</td>
                    <td><?php echo $documento_solicitante; ?></td>
                </tr>
                <tr>
                    <td>AREA DE DONDE SE SOLICITA:</td>
                    <td><?php echo $area_solicitante; ?></td>
                </tr>
            </table>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:40%;">
            <table>
                <tr>
                    <td style="font-size: 15px;font-weight: bold;">REQUISICION DE COMPRA No.</td>
                    <td style="font-size: 15px;"><?php echo $consecutivo; ?></td>
                </tr>
                <tr>
                    <td>FECHA: <?php echo fecha_larga($fecha_inicio); ?></td>
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
                    <td >CODIGO</td>
                    <td >ITEM</td>
                    <td >UNIDAD</td>
                    <td >CANTIDAD</td>
                </tr>
            </thead>
            <tbody>
                <?php echo $bodyTable; ?>
            </tbody>

        </table>

        <br>
        <div style="overflow: hidden; width:100%; font-size:12px;">
            <div style="float:left; width:90%; margin:5px 5px 0px 0px;">Observaciones</div>
            <div style="float:left; width:100%; margin:3px 200px 5px 0px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;"><?php echo $observacion ?></div>
        </div>

        <!-- <div style="border:1px solid #000; margin-top:20px; width:740px; padding:5px;">
            <b>Observaciones: </b><?php echo $observacion; ?>
        </div> -->

    </div>

    <!-- PIE DE PAGINA -->
    <div style="margin-top:150px; overflow:hidden; width:100%;">
        <div style="width:48%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Elaboro</b></div>
        <div style="width:48%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Recibio</b></div>
    </div>
</body>

<?php

    $texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
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
        if($marcaAgua=='true'){
            $mpdf->SetWatermarkText('ANULADO');
            $mpdf->showWatermarkText = true;
        }
        // $mpdf-> debug = true;
        // $mpdf->useSubstitutions = true;
        // $mpdf->simpleTables = true;
        // $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode ('fullpage');
		$mpdf->SetHeader("");
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));

		// if( $PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        // else{
            $mpdf->Output("requisicion_compra.pdf",'I');
        // }

        exit;
    // }
    // else{ echo $texto; }

?>