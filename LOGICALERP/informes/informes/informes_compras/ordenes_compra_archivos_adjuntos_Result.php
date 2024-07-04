<?php

include_once('../../../../configuracion/conectar.php');
include_once('../../../../configuracion/define_variables.php');
ob_start();

if($IMPRIME_XLS=='true'){
  header('Content-type: application/vnd.ms-excel');
  header("Content-Disposition: attachment; filename=ordenes_compra_archivos_adjuntos_".date("Y_m_d").".xls");
  header("Pragma: no-cache");
  header("Expires: 0");
}

$arraytercerosJSON = json_decode($arraytercerosJSON);

$id_empresa         = $_SESSION['EMPRESA'];
$id_sucursal        = $MyInformeFiltro_0;
$nombreSucursal     = $_SESSION['NOMBRESUCURSAL'];
$subtitulo_cabecera = '';
$bodyTable          = '';

//---------------------------FILTRO POR SUCURSAL------------------------------//

if ($sucursal!='' && $sucursal!='global') {

    $whereSucursal = ' AND id_sucursal='.$sucursal;

    //CONSULTAR EL NOMBRE DE LA SUCURSAL
    $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
    $query = mysql_query($sql,$link);
    $subtitulo_cabecera .= '<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
}

//---------------------------FILTRO POR TERCEROS------------------------------//

$whereTerceros = '';
$id_tercero = '';

if (!empty($arraytercerosJSON)) {
    foreach ($arraytercerosJSON as $indice => $id_proveedor) {
        $whereTerceros .= ($whereTerceros=='')? " id_proveedor=$id_proveedor" : " OR id_proveedor=$id_proveedor";
    }
    $whereTerceros   = " AND (".$whereTerceros.")";
}

//---------------------------FILTRO POR FECHAS--------------------------------//

if (!isset($MyInformeFiltroFechaInicio) && !isset($MyInformeFiltroFechaFinal)) {
    $script  = 'localStorage.MyInformeFiltroFechaFinal                  = "";
                localStorage.MyInformeFiltroFechaInicio                 = "";
                localStorage.sucursal_ordenes_compra_archivos_adjuntos  = "";
                arraytercerosOC.length                                  = 0;
                tercerosConfiguradosOC.length                           = 0;';
}else if(isset($MyInformeFiltroFechaInicio) && isset($MyInformeFiltroFechaFinal)){
  $whereFechas = " fecha_inicio BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'";
}

$campoAdd     = ($IMPRIME_XLS=='true')? '<td><b>NIT EMPRESA</b></td>' : '';
$campoAddFila = ($IMPRIME_XLS=='true')? '<td>'.$_SESSION['NITEMPRESA'].'</td>' : '';

//--------------------------FILTRO POR CONTENIDO------------------------------//

if($contenido == "conArchivos"){
  $whereContenido = " AND compras_ordenes_documentos.nombre IS NOT NULL";
}elseif ($contenido == "sinArchivos") {
  $whereContenido = " AND compras_ordenes_documentos.nombre IS NULL";
}

//---------------------------CONSULTA PRINCIPAL-------------------------------//

$sqlCompras =  "SELECT
                  compras_ordenes.sucursal,
                  compras_ordenes.consecutivo,
                  compras_ordenes.fecha_inicio,
                  compras_ordenes.bodega,
                  compras_ordenes.nit,
                  compras_ordenes.proveedor,
                  CONCAT(compras_ordenes_documentos.nombre,'.',compras_ordenes_documentos.ext) AS archivo,
                  compras_ordenes_documentos.id_orden_compra,
                  compras_ordenes.tipo_nombre
                FROM
                  compras_ordenes
                LEFT JOIN
                  compras_ordenes_documentos ON compras_ordenes.id=compras_ordenes_documentos.id_orden_compra
                WHERE
                  compras_ordenes.activo=1
                AND
                  compras_ordenes.estado!=0
                AND
                  compras_ordenes.id_empresa=$id_empresa
                  $whereTerceros
                  $whereContenido
                  $whereSucursal
                AND
                  $whereFechas";

//-------------------------CONSTRUCCION DE LA TABLA---------------------------//
//echo $sqlCompras;

$queryCompras = mysql_query($sqlCompras,$link);

$bodyTable .=   '<table class="table" style="border-collapse: collapse;">
                  <tr class="titulos_conceptos_empleados">
                    <td><b>SUCURSAL</b></td>
                    <td><b>CONSECUTIVO</b></td>
                    <td style="width:8%;"><b>FECHA</b></td>
                    <td><b>BODEGA</b></td>
                    <td><b>NIT</b></td>
                    <td><b>PROVEEDOR</b></td>
                    <td><b>ARCHIVO</b></td>
                    <td><b>FACTURA</b></td>
                    <td><b>TIPO</b></td>
                  </tr>';

while($row=mysql_fetch_array($queryCompras)) {
    $style = ($style != '')? '' : 'style="background:#f7f7f7;"';
    $bodyTable .= '  <tr class="filaConcepto " '.$style.'>
                       <td>'.$row['sucursal'].'</td>
                       <td>'.$row['consecutivo'].'</td>
                       <td>'.$row['fecha_inicio'].'</td>
                       <td>'.$row['bodega'].'</td>
                       <td>'.$row['nit'].'</td>
                       <td>'.$row['proveedor'].'</td>
                       <td>'.$row['archivo'].'</td>
                       <td>'.$row['id_orden_compra'].'</td>
                       <td>'.$row['tipo_nombre'].'</td>';
}

$bodyTable .= "</tr></table><br>";

?>
<style>
    .contenedor_informe, .contenedor_titulo_informe{
        width         : 100%;
        border-bottom : 1px solid #CCC;
        margin        : 0 0 10px 0;
        font-size     : 11px;
        font-family   : Verdana, Geneva, sans-serif
    }

    .titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
    }

    .titulo_informe_detalle{
        float         : left;
        width         : 210px;
        padding       : 0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
    }

    .titulo_informe_empresa{
        float       : left;
        width       : 100%;
        font-size   : 16px;
        font-weight : bold;
    }

    .table{ font-size : 11px; width: 100%; margin-top: 20px; }
    .table thead td { border-bottom: 1px solid; border-top: 1px solid; }
    .table td{ padding-right: 10px; }

    .empleado_nomina{
        height       : 25px;
        background   : #999;
        padding-left : 10px;
        height       : 25px;
        font-size    : 12px;
    }

    .empleado_nomina td{
        padding-left : 10px;
        color        : #FFF;
        height       : 25px;
    }

    .titulos_conceptos_empleados{
        height      : 25px;
        background  : #EEE;
        font-weight : bold;
    }

    .titulos_conceptos_empleados td{
        color  : #8E8E8E;
        height : 25px;
    }

    .titulos_conceptos_empleados td,.titulos_totales_empleados td{
        border-top    : 1px solid #999;
        border-bottom : 1px solid #999;
        background    : #EEE;
        padding-left  : 10px;
    }

    .titulos_totales_empleados{
        height      : 25px;
        font-weight : bold;
        font-size   : 12px;
        color       : #8E8E8E;
        font-weight : bold;
    }

    .titulos_totales_empleados td{
        color       : #8E8E8E;
        height      : 25px;
        font-weight : bold;
    }

    .filaConcepto{ height: 25px; }
    .filaConcepto td{ padding-left: 10px; }
    .table{ font-size : 11px; width: 100%; margin-top: 20px; }
</style>

<!-- *************************** DESARROLLO DEL INFORME ****************************************** -->
<!-- ********************************************************************************************* -->

<body>
  <div class="contenedor_titulo_informe">
    <div style=" width:100%">
      <div style="width:100%; text-align:center">
        <table align="center" style="text-align:center;" >
          <tr><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
          <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
          <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;">ORDENES DE COMPRA - ARCHIVOS ADJUNTOS</td></tr>
          <tr><td style="font-size:11px;text-align:center;"><?php echo $subtitulo_cabecera; ?> Periodo del <?php echo $MyInformeFiltroFechaInicio; ?> al <?php echo $MyInformeFiltroFechaFinal; ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <?php echo $bodyTable; ?>
</body>

<script>
    <?php echo $script; ?>
</script>

<?php

	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER-L'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
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
					10,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
    $mpdf->SetProtection(array('print'));
    $mpdf->useSubstitutions = true;
    $mpdf->simpleTables     = true;
    $mpdf->packTableData    = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
    $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output(ordenes_compra_archivos_adjuntos_.date("Y_m_d").".pdf","F"); }
        else{ $mpdf->Output("ordenes_compra_archivos_adjuntos_".date("Y_m_d").".pdf","I"); }
		exit;
	}
    else{ echo $texto; }

?>
