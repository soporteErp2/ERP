<?php

    ini_set('max_execution_time', 10000);
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	if($IMPRIME_XLS=='true'){

	   header('Content-Type: text/html; charset=UTF-8');
       header('Content-type: application/vnd.ms-excel;');
       header("Content-Disposition: attachment; filename=Informe_tareas_pendientes.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
   	}
	ob_start();

	if($MyInformeFiltro_1 == undefined){
		   $MyInformeFiltro_1 = '';
	}

	if($MyInformeFiltro_Clientes == undefined){
		   $MyInformeFiltro_Clientes = '';
	}

	$vendedor = $MyInformeFiltro_0;
	if($vendedor == ''){
		$nombreVendedor = 'TODOS';
		$whereVendedor  = '';
	}else{
		$nombreVendedor = $mysql->result($mysql->query("SELECT nombre FROM empleados WHERE id = $vendedor"),0,"nombre");
		$whereVendedor  = 'AND TL.id_usuario = '.$vendedor;
	}

	/*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/
	?>
<style>
	.my_informe_Contenedor_Titulo_informe{
		float         :	left;
		width         :	100%;
		border-bottom :	1px solid #CCC;
		margin        :	0 0 10px 0;
		font-size     :	11px;
		font-family   :	Verdana, Geneva, sans-serif
	}
	.my_informe_Contenedor_Titulo_informe_label{
		float         :	left;
		width         :	130px;
		font-weight   :	bold;
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
		float         :	left;
		width         :	370px;
		font-size     :	16px;
	}
	.my_informe_Contenedor_Titulo_informe1{
		float         :	left;
		width         :	100%;
		margin        :	0 0 10px 0;
		font-size     :	11px;
		font-family   :	Verdana, Geneva, sans-serif
	}
	.total{
        background  : #EEE;
        font-weight : bold;
    }
    .total td{
        background    : #EEE;
        /*padding-left  : 10px;*/
        height        : 25px;
        font-weight   : bold;
    }
</style>

<!-- --------------------------   DESARROLLO DEL INFORME  ------------------------------------ -->
<!-- ----------------------------------------------------------------------------------------- -->
<body style="font-size:12px; font-family:Verdana, Geneva, arial;">

<?php
	if($IMPRIME_XLS!='true'){
?>
    <htmlpageheader  class="SoloPDF" name="MyHeaderInforme">
        <div style="text-align:right;font-size:8px">
            <?php echo $nombre_informe.'  |  '.$nombre_empresa.'  |  '.$_SESSION["NOMBREFUNCIONARIO"].'  |  '.fecha_larga_hora_m(date('Y-m-d H:s:i')); ?>  |   Paginas({PAGENO} de {nb})
        </div>
    </htmlpageheader>
    <sethtmlpageheader name="MyHeaderInforme" show-this-page="1" value="on"></sethtmlpageheader>
<?php
     }
?>
     <table style="float:left;width:100%">
        <tr>
        	<td style="width:100%; border-bottom:1px solid #CCC; font-weight:bold; font-size:18px; text-align:left;"><?php echo $nombre_informe ?></td>
        </tr>
    </table>
    <table class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <tr>
            <td>
		        <table style="float:left; width:370px; border-spacing:0px" class="my_informe_Contenedor_Titulo_informe1">
		       		<tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Fecha Inicial</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaInicio)?></td>
		            </tr>
		            <tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Fecha Final</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaFinal)?></td>
		            </tr>
		            <tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Vendedor</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreVendedor; ?></td>
		            </tr>
		        </table>
		    </td>
		    <td>
		        <table style="float:left; width:370px;">
		            <tr style="float:left;width:100%; text-align:center">
		                <td class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $_SESSION['NOMBREEMPRESA']?></td>
		            </tr>
		        </table>
		    </td>
    	</tr>
    </table>
<?php
	/*--------------------------------------------------CUERPO DEL INFORME---------------------------------------------------*/
	//Thead::::::::::::::::::::::::::::
	echo'<br>
		     <table style="width:1196px; float:left;border-spacing:0px;font-size:12px; ">
				<tr>
					<td style=" width:120px; padding-bottom:5px;float:left;font-weight:bold">NIT</td>
					<td style=" width:268px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">RAZON SOCIAL</td>
					<td style=" width:268px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">NOMBRE COMERCIAL</td>
					<td style=" width:135px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">DIRECCION</td>
					<td style=" width:120px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">TELEFONO</td>
					<td style=" width:70px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">FECHA</td>
					<td style=" width:215px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">RESPONSABLE</td>
				</tr>
			 </table>';

	$SQL = "SELECT
				T.numero_identificacion,
				T.nombre,
				T.nombre_comercial,
				TL.usuario,
				TL.fecha,
				T.telefono1,
				T.direccion
			FROM
				terceros T
			INNER JOIN terceros_log TL ON (T.id = TL.id_tercero AND TL.accion = 'prospecto-tercero')
			WHERE
				T.activo = 1
				AND TL.fecha BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
				AND T.tipo_cliente = 'Si'
				$whereVendedor
			ORDER BY T.nombre ASC";//LOGICA DE LOS PEDIDOS ADICIONALES

	$consul = $mysql->query($SQL,$link);

	$total_prospectos_clientes = $mysql->num_rows($consul);

	$SQL1 = "SELECT	T.id FROM	terceros T
			INNER JOIN terceros_log TL ON (T.id = TL.id_tercero AND (TL.accion = 'prospecto' OR TL.accion = 'prospecto-tercero'))
			WHERE
				T.activo = 1
				AND TL.fecha BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
				$whereVendedor
			GROUP BY TL.id_tercero";//LOGICA DE LOS PEDIDOS ADICIONALES

	$consul1 = $mysql->query($SQL1,$link);

	$total_prospectos = $mysql->num_rows($consul1);
	//maketbody:::::::::::::::::::::::::::::::
	while($row=$mysql->fetch_array($consul)){

		$bodytable .= '<table style="width:1196px; float:left;border-spacing:0px;font-size:12px; ">
							<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:normal; border-spacing:0px">
								<td style=" width:120px; float:left;">'.$row['numero_identificacion'].'<span style="font-size:9px; font-weight:bold">'.$TipEve.'</span></td>
								<td style=" width:268px; float:left; margin-left:10px;padding-left:5px">'.$row['nombre'].'</td>
								<td style=" width:268px; float:left; margin-left:10px;padding-left:5px">'.$row['nombre_comercial'].'</td>
								<td style=" width:135px; padding-bottom:5px;float:left;padding-left:5px; margin-left:10px">'.$row['direccion'].'</td>
								<td style=" width:120px; padding-bottom:5px;float:left;padding-left:5px; margin-left:10px">'.$row['telefono1'].'</td>
								<td style=" width:70px; padding-bottom:5px;float:left;padding-left:5px; margin-left:10px">'.$row['fecha'].'</td>
								<td style=" width:215px; padding-bottom:5px;float:left;padding-left:5px; margin-left:10px">'.$row['usuario'].'</td>
							</tr>
						</table>';
	}

	$porcentajeProspectosClientes = ($total_prospectos_clientes * 100) / $total_prospectos;

	$bodytable .=  '<div style="float:left;width:100%;padding-top:20px;">
        			    <div style="float:left;width:45%;">
        			        <table class="defaultFont" style="width:100%;border-collapse:collapse;">
                        		<tr class="total" style=" border:1px solid #999;border-top:none;">
                                   <td style="border-top:1px dashed;"><b>&nbsp;&nbsp;TOTAL PROSPECTOS EN EL PERIODO</b></td>
                                   <td style="border-top:1px dashed;"><div style="width:75px;height:12px">'.$total_prospectos.'</div></td>
                                   <td style="border-top:1px dashed;"><div style="width:75px;height:12px">100%</div></td>
                                </tr>
                                <tr class="total" style=" border:1px solid #999;border-top:none;">
                                   <td style="border-top:1px dashed;"><b>&nbsp;&nbsp;PROSPECTOS CONVERTIDOS EN CLIENTE</b></td>
                                   <td style="border-top:1px dashed;"><div style="width:75px;height:12px">'.$total_prospectos_clientes.'</div></td>
                                   <td style="border-top:1px dashed;"><div style="width:75px;height:12px">'.round($porcentajeProspectosClientes,2).'%</div></td>
                                </tr>
                            </table>
        			    </div>
        			    <div style="float:left;width:10%;">&nbsp;
        			    </div>
        			    <div style="float:left;width:45%;">&nbsp;
        			    </div>
        			</div>';

	 /*$totalesTable ='<table class="defaultFont" style="width:100%;border-collapse:collapse;">
                        '.$displayTotalOrdenes.'
                        <tr><td colspan="3">&nbsp;</td><tr>
                        '.$displayTotalOrdenesTodas.'
                    </table>';*/

	//condicion requerida para imprimir caracteres especiales(ñó) en ecxel,utf8_encode  se rompe en PDF.
	if($IMPRIME_XLS=='true'){
		echo (utf8_encode($bodytable));
	}else{
		echo $bodytable;
	}
?>
</body>

<!-- ---------------------------------  FIN DEL INFORME  ------------------------------------- -->
<!-- ----------------------------------------------------------------------------------------- -->
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
					3,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);

		$mpdf->simpleTables = true;
        $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ('Informe SIIP' );
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