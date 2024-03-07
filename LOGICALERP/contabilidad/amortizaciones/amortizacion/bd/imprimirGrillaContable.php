<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	ob_start();

	$id_empresa = $_SESSION['EMPRESA'];

	// CABECERA DEL DOCUMENTO
	$sql="SELECT consecutivo,fecha_documento,fecha_diferidos,nombre_usuario,sucursal,observacion FROM amortizaciones WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
	$query=$mysql->query($sql,$mysql->link);
	$consecutivo     = $mysql->result($query,0,'consecutivo');
	$fecha_documento = $mysql->result($query,0,'fecha_documento');
	$fecha_diferidos  = $mysql->result($query,0,'fecha_diferidos');
	$nombre_usuario  = $mysql->result($query,0,'nombre_usuario');
	$observacion        = $mysql->result($query,0,'observacion');
	$sucursal        = $mysql->result($query,0,'sucursal');

	$arrayReplaceString = array("\n", "\r");
	$observacion = str_replace($arrayReplaceString, "<br/>", $observacion );

	// CUERPO DEL DOCUMENTO
	$sql="SELECT tipo_documento,consecutivo_documento,fecha_inicio,documento_tercero,tercero,valor FROM amortizaciones_diferidos WHERE activo=1 AND id_empresa=$id_empresa ANd id_amortizacion=$id_documento";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$tbody .= "<tr>
						<td>$row[tipo_documento]</td>
	        			<td>$row[consecutivo_documento]</td>
	        			<td>$row[fecha_inicio]</td>
	        			<td>$row[documento_tercero]</td>
	        			<td>$row[tercero]</td>
	        			<td>$row[valor]</td>
					</tr>";
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
    .my_informe_Contenedor_Titulo_informe td{ padding-left : 2px; }
    .tablaPiePagina td{ padding-left : 2px; }

    td{
        font-size   : 11px;
        font-family :"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
    }

</style>

<body>
	<div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table style="text-align:center; margin-left:auto; margin-right:auto; font-size:12px; float:left; width:100%; margin-bottom:20px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $_SESSION['NOMBREEMPRESA']; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
            <!-- <tr><td style="font-size: 13px;font-weight: bold;">Amortizacion N. <?php echo $consecutivo; ?></td></tr> -->
        </table>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left;width:50%;">
            <table style="font-size: 12px;">
                <tr>
                    <td>Fecha Documento:</td>
                    <td><?php echo $fecha_documento; ?></td>
                </tr>
                <tr>
                    <td>Fecha Diferidos:</td>
                    <td><?php echo $fecha_diferidos; ?></td>
                </tr>
            </table>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:40%;">
            <table>
                <tr>
                    <td style="font-size: 15px;font-weight: bold;">AMORTIZACION No.</td>
                    <td style="font-size: 15px;"><?php echo $consecutivo; ?></td>
                </tr>
                <tr>
                    <td>Elaborado Por: <?php echo $nombre_usuario ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <br>
	</div>
	<div class="my_informe_Contenedor_Titulo_informe">

        <!-- CUERPO DEL INFORME -->
        <table style="border-collapse: collapse; width:100%;">
        	<thead>
        		<tr style="background-color:#000;">
        			<td style="color:#fff;width:100px;" >DOCUMENTO</td>
        			<td style="color:#fff;width:100px;" >CONSECUTIVO</td>
        			<td style="color:#fff;width:100px;" >FECHA</td>
        			<td style="color:#fff;width:100px;" >NIT</td>
        			<td style="color:#fff;" >TERCERO</td>
        			<td style="color:#fff;width:100px;" >VALOR</td>
        		</tr>
        	</thead>
			<tbody><?php echo $tbody ?></tbody>
		</table>
		<div style=" float:left">
			<br>
			<div style="overflow: hidden; width:100%; margin:5px 5px 20px 0px; padding:0px 7px 0px 0px; font-size:12px;">
		        <div style="border:1px solid #000; margin-top:20px; width:740px; padding:5px;">
		            <b>Observaciones: </b><?php echo $observacion; ?>
		        </div>
	    	</div>
	    </div>
    </div>

</body>

<?php

	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	// if($IMPRIME_PDF){
		include("../../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);
		$mpdf->SetProtection(array('print'));
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	// OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		// OUTPUT A VISTA

		exit;
	// }
	// else{ echo $texto; }

?>