<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    if (!isset($_SESSION['EMPRESA']) ) { exit; }
    ob_start();

    $id_empresa     = $_SESSION['EMPRESA'];

    $sql   = "SELECT
                sucursal,
                bodega,
                consecutivo,
                fecha_documento,
                sucursal_traslado,
                bodega_traslado,
                usuario,
                observacion,
                estado
            FROM inventario_traslados
            WHERE activo=1
            AND id=$id_documento";
    $query = $mysql->query($sql,$mysql->link);
    $sucursal          = $mysql->result($query,0,'sucursal');
    $bodega            = $mysql->result($query,0,'bodega');
    $consecutivo       = $mysql->result($query,0,'consecutivo');
    $fecha_documento   = $mysql->result($query,0,'fecha_documento');
    $sucursal_traslado = $mysql->result($query,0,'sucursal_traslado');
    $bodega_traslado   = $mysql->result($query,0,'bodega_traslado');
    $usuario           = $mysql->result($query,0,'usuario');
    $observacion       = $mysql->result($query,0,'observacion');
    $estado            = $mysql->result($query,0,'estado');

    $sql = "SELECT
                codigo,
                id_unidad_medida,
                nombre_unidad_medida,
                cantidad_unidad_medida,
                nombre,
                cantidad,
                costo_unitario,
                observaciones
            FROM inventario_traslados_unidades WHERE activo=1 AND id_traslado=$id_documento ";
    $query = $mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_assoc($query)) {
        $obsItem = ($row['observaciones']<>'')? "<tr><td colspan='4'><i>$row[observaciones]</i></td></tr>" : "" ;
        $bodyTable .= "<tr>
                            <td>$row[codigo]</td>
                            <td>$row[nombre]</td>
                            <td style='text-align:center;'>$row[nombre_unidad_medida] x $row[cantidad_unidad_medida]</td>
                            <td style='text-align:right;'>$row[cantidad]</td>
                        </tr>
                        $obsItem
                        ";
    }

    if ($estado==0) {
        echo "<center><h1>El documento no se ha generado</h1></center>";
        exit;
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
<title>Traslado de inventario</title>
<body >
    <div style="float:left; width:100%">
        <!-- INFORMACION DE LA EMPRESA -->
        <table  style="text-align:center;margin-left: auto; margin-right: auto; font-size:12px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $_SESSION['NOMBREEMPRESA']; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
            <tr><td>Bodega: <?php echo $bodega; ?></td></tr>
        </table>
        <br>
        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left;width:50%;">
            <table style="font-size: 12px;">
                <tr>
                    <td><b>Sucursal destino</b></td>
                    <td><?php echo $sucursal_traslado ?></td>
                </tr>
                <tr>
                    <td><b>Bodega destino</b></td>
                    <td><?php echo $bodega_traslado ?></td>
                </tr>
                <tr>
                    <td><b>Usuario</b></td>
                    <td><b><?php echo $usuario; ?></b></td>
                </tr>
            </table>
        </div>
        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:40%;">
            <table>
                <tr>
                    <td style="font-size: 15px;font-weight: bold;">TRASLADO DE INVENTARIO No.</td>
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
                    <td style="text-align: left;">CODIGO</td>
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
        <div style="width:48%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Reviso</b></div>
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
        if($estado==3){
            $mpdf->SetWatermarkText('DOCUMENTO ANULADO');
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