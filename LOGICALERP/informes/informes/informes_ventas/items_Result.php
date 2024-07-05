<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

	ob_start();

    if($IMPRIME_XLS=='true'){
        header('Content-type: application/vnd.ms-excel; ');
        header("Content-Disposition: attachment; filename=informe_items_venta_".date("Y_m_d").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    //======================// CONFIG DE ARRAY //======================//
    $_tercero    = array("sql"=>"F.cliente", "width"=>"80", "style"=>"", "title"=>"TERCERO", "type"=>"string");
    $_nit        = array("sql"=>"F.nit", "width"=>"80", "style"=>"padding-right:5px; text-align:right;", "title"=>"NIT", "type"=>"string");
    $_vendedor   = array("sql"=>"F.nombre_vendedor", "width"=>"80", "style"=>"", "title"=>"VENDEDOR", "type"=>"string");
    $_cedula     = array("sql"=>"F.documento_vendedor", "width"=>"80", "style"=>"padding-right:5px; text-align:right;", "title"=>"CEDULA", "type"=>"string");
    $_sucursal   = array("sql"=>"F.sucursal", "width"=>"80", "style"=>"", "title"=>"SUCURSAL", "type"=>"string");
    $_bodega     = array("sql"=>"F.bodega", "width"=>"80", "style"=>"", "title"=>"BODEGA", "type"=>"string");
    $_codigoCcos = array("sql"=>"F.codigo_centro_costo", "width"=>"80", "style"=>"", "title"=>"", "type"=>"string");
    $_cCos       = array("sql"=>"F.centro_costo", "width"=>"80", "style"=>"", "title"=>"CENTRO DE COSTO", "type"=>"string");

    $_familia  = array("sql"=>"T.familia", "width"=>"80", "style"=>"", "title"=>"FAMILIA", "type"=>"string");
    $_grupo    = array("sql"=>"T.grupo", "width"=>"80", "style"=>"", "title"=>"GRUPO", "type"=>"string");
    $_subgrupo = array("sql"=>"T.subgrupo", "width"=>"80", "style"=>"", "title"=>"SUBGRUPO", "type"=>"string");

    $_codigo   = array("sql"=>"I.codigo", "width"=>"80", "style"=>"padding-right:5px; text-align:right;", "title"=>"CODIGO", "type"=>"string");
    $_nombre   = array("sql"=>"I.nombre", "width"=>"80", "style"=>"", "title"=>"ITEM", "type"=>"string");
    $_medida   = array("sql"=>"I.nombre_unidad_medida", "width"=>"80", "style"=>"", "title"=>"MEDIDA", "type"=>"string");
    $_cantidad = array("sql"=>"SUM(I.cantidad)", "width"=>"80", "style"=>"padding-right:5px;", "title"=>"CANTIDAD", "type"=>"number");
    $_precio   = array("sql"=>"SUM(I.costo_unitario * I.cantidad)", "width"=>"80", "style"=>"padding-right:5px;", "title"=>"PRECIO", "type"=>"moneda");
    $_costo    = array("sql"=>"SUM(I.costo_inventario * I.cantidad)", "width"=>"80", "style"=>"padding-right:5px;", "title"=>"COSTO", "type"=>"moneda");


    $id_empresa  = $_SESSION['EMPRESA'];
    $arrayCampos = array();
    $desde       = $MyInformeFiltroFechaInicio;
    $hasta       = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d");

    $whereSucursal = '';
    $subtitulo     = 'INFORME ITEMS DE VENTA';

    $groupBy = "I.id_inventario";

    if (isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal!='') {
        $whereFechas   = " AND F.fecha_inicio BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
        $datos_informe = 'Desde '.$MyInformeFiltroFechaInicio.' Hasta '.$MyInformeFiltroFechaFinal;
    }
    else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");
        $datos_informe='Corte ha '.$MyInformeFiltroFechaFinal;
        $script = 'localStorage.MyInformeFiltroFechaInicioRemisionesVenta = "";
                    localStorage.MyInformeFiltroFechaFinalRemisionesVenta = "";

                    localStorage.sucursal_items    = "";
                    localStorage.filtroOrden_items = "";
                    localStorage.limiteRows_items  = "";
                    array_terceros_items.length    = 0;
                    terceros_config_items.length   = 0;
                    array_vendedores_items.length  = 0;
                    vendedores_config_items.length = 0;
                    array_ccos_items.length        = 0;
                    ccos_config_Items.length       = 0;
                    array_categorias_items.length  = 0;
                    categorias_config_Items.length = 0;

                    allCategoriasItems = "false";
                    allCcosItems       = "false";
                    allVendedoresItems = "false";
                    allClientesItems   = "false";';
    }

    $arrayCampos[] = $_sucursal;
    $arrayCampos[] = $_bodega;
    $arrayCampos[] = $_codigo;
    $arrayCampos[] = $_nombre;
    $arrayCampos[] = $_medida;

    // CLIENTES
    if ($idTerceros!='') {

        $arrayCampos[] = $_nit;
        $arrayCampos[] = $_tercero;

        $groupBy .= ",id_cliente";
        if ($idTerceros!='todos') { $whereClientes = "AND(F.id_cliente = ".str_replace(',', ' OR F.id_cliente=', $idTerceros).")"; }
    }

    // VENDEDORES
    if ($idVendedores!='') {

        $arrayCampos[] = $_cedula;
        $arrayCampos[] = $_vendedor;

        $groupBy .= ',F.id_vendedor';
        if ($idVendedores!='todos') { $whereVendedores = "AND(F.id_vendedor = ".str_replace(',', ' OR F.id_vendedor=', $idVendedores).")"; }
    }

    // CENTRO DE COSTO
    if ($idCcos!='') {

        $arrayCampos[] = $_codigoCcos;
        $arrayCampos[] = $_cCos;

        $groupBy .= ",F.id_centro_costo";
        if ($idCcos!='todos') { $whereCcos = "AND(F.id_centro_costo = ".str_replace(',', ' OR F.id_centro_costo=', $idCcos).")"; }
    }

    // CATEGORIAS
    if ($idCategorias!='') {

        if($nivelCategoria == 'familia'){

            $arrayCampos[] = $_familia;

            $groupBy .= ",T.id_familia";
            if ($idCategorias!='todos') { $whereCategoria = "AND(T.id_familia = ".str_replace(',', ' OR T.id_familia=', $idCategorias).")"; }
        }
        else if($nivelCategoria == 'grupo'){

            $arrayCampos[] = $_grupo;

            $groupBy .= ",T.id_grupo";
            if ($idCategorias!='todos') { $whereCategoria = "AND(T.id_grupo = ".str_replace(',', ' OR T.id_grupo=', $idCategorias).")"; }
        }
        else if($nivelCategoria == 'subGrupo'){

            $arrayCampos[] = $_subgrupo;

            $groupBy .= ",T.id_subgrupo";
            if ($idCategorias!='todos') { $whereCategoria = "AND(T.id_subgrupo = ".str_replace(',', ' OR T.id_subgrupo=', $idCategorias).")"; }
        }
    }

    if($totalizado != "" AND $totalizado != 'ninguno'){
        $arrayCampos = array();

        $arrayCampos[] = $_sucursal;
        $arrayCampos[] = $_bodega;

        if($totalizado == 'clientes'){
            $subtitulo .= "<br>Totalizado por Clientes";
            $arrayCampos[] = $_nit;
            $arrayCampos[] = $_tercero;
            $groupBy = "F.id_cliente";
        }
        else if($totalizado == 'vendedoes'){
            $subtitulo .= "<br>Totalizado por Vendedores";
            $arrayCampos[] = $_cedula;
            $arrayCampos[] = $_vendedor;
            $groupBy = "F.id_vendedor";
        }
        else if($totalizado == 'ccos'){
            $subtitulo .= "<br>Totalizado por Centros de Costo";
            $arrayCampos[] = $_codigoCcos;
            $arrayCampos[] = $_cCos;
            $groupBy = "F.id_centro_costo";
        }

        else if($totalizado == 'categorias'){

            if($nivelCategoria == 'familia'){
                $subtitulo .= "<br>Totalizado por Familias de Categorias";
                $arrayCampos[] = $_familia;
                $groupBy = "T.id_familia";
            }
            else if($nivelCategoria == 'grupo'){
                $subtitulo .= "<br>Totalizado por Grupos de Categorias";
                $arrayCampos[] = $_grupo;
                $groupBy = "T.id_grupo";
            }
            else {
                $subtitulo .= "<br>Totalizado por SubGrupos de Categorias";
                $arrayCampos[] = $_subgrupo;
                $groupBy = "T.id_subgrupo";
            }
        }
    }

    //ORDENAMIENTO POR CAMPO ASCENDENTE/DESCENDENTE

    if($ordenaPor != "" AND $ordenaPor != 'ninguno'){

        if($ordenaPor == 'codigo_item'){
            $subtitulo .= "<br>Ordenado por Codigo";
            $campoSQL   = "I.codigo";
        }
        else if($ordenaPor == 'nombre_item'){
            $subtitulo .= "<br>Ordenado por Nombre";
            $campoSQL   = "I.nombre";
        }
        else if($ordenaPor == 'cantidad'){
            $subtitulo .= "<br>Ordenado por Cantidad";
            $campoSQL   = "SUM(I.cantidad)";
        }
        else if($ordenaPor == 'precio'){
            $subtitulo .= "<br>Ordenado por Precio";
            $campoSQL   = "SUM(I.costo_unitario * I.cantidad)";
        }
        else if($ordenaPor == 'cliente'){
            $subtitulo .= "<br>Ordenado por Cliente";
            $campoSQL   = "F.cliente";
        }
        else if($ordenaPor == 'vendedor'){
            $subtitulo .= "<br>Ordenado por Vendedor";
            $campoSQL   = "F.nombre_vendedor";
        }
        else if($ordenaPor == 'centro_costos'){
            $subtitulo .= "<br>Ordenado por Centro de Costos";
            $campoSQL   = "F.centro_costo";
        }
        else if($ordenaPor == 'categoria'){
            $subtitulo .= "<br>Ordenado por ".ucwords($nivelCategoria);
            $campoSQL   = "T.".strtolower($nivelCategoria);
        }
    }

    $arrayCampos[] = $_cantidad;
    $arrayCampos[] = $_precio;
    $arrayCampos[] = $_costo;

    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = ' AND F.id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sqlSucursal   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $querySucursal = mysql_query($sqlSucursal,$link);
        $subtitulo    .= '<br><b>Sucursal</b> '.mysql_result($querySucursal,0,'nombre').'';
    }

    $sqlTitle  = "";
    $sqlCampos = "";
    foreach ($arrayCampos as $i => $campo) {
        $sqlCampos .= "$campo[sql] AS campo_".$i.",";
        $bodyTitle .= '<td width="'.$campo['width'].'" style="text-align:center;">'.$campo['title'].'</td>';
        if($campo['sql'] == $campoSQL){
            //REALIZO EL ORDER BY SEGUN EL ALIAS QUE SE LE HA ASIGNADO AL CAMPO EN EL SQL
            $orderBy    = "ORDER BY campo_".$i." ".$ordenamiento;
        }
    }

    //LIMITE DE LA CONSULTA
    if($limite != "todos" && $limite != ""){
        $limit = 'LIMIT 0,'.$limite;
    }
    else{
        $limit = '';
    }

    $sqlCampos = substr($sqlCampos, 0, -1);

    $sqlVentas = "SELECT $sqlCampos
                    FROM ventas_facturas AS F
                    INNER JOIN ventas_facturas_inventario AS I ON(
                        I.activo=1
                        AND I.cantidad > 0
                        AND I.id_factura_venta=F.id
                    )
                    INNER JOIN items AS T ON(
                        T.activo=1
                        AND T.id = I.id_inventario
                    )
                    WHERE F.activo=1
                        AND F.estado=1
                        AND F.id_empresa='$id_empresa'
                        $whereSucursal
                        $whereClientes
                        $whereVendedores
                        $whereCcos
                        $whereCategoria
                        $whereFechas
                    GROUP BY $groupBy
                    $orderBy
                    $limit";
    $queryVentas = mysql_query($sqlVentas,$link);

    $remision = 0;

    //VARIABLES ACUMULADAS POR CADA ITEM
    $acumuladoCantidad  = 0;
    $acumuladoPendiente = 0;
    $acumuladoCosto     = 0;
    $acumuladoDescuento = 0;
    $acumuladoIva       = 0;
    $acumuladoTotal     = 0;

    //ARMAR EL CUERPO DEL INFORME
    // if($IMPRIME_XLS=='true'){
    //     while ($rowFactura = mysql_fetch_assoc($queryVentas)) {
    //         # code...
    //     }
    // }

    $contNumber = 0;
    $arrayTotal["total_1"] = 0;
    $arrayTotal["total_2"] = 0;
    $arrayTotal["total_3"] = 0;
    while ($rowFactura = mysql_fetch_assoc($queryVentas)) {
        $contNumber = 0;
        $bodyTable .= '<tr>';

        foreach ($arrayCampos as $i => $fila) {
            if($fila['type'] === "number"){
                $contNumber++;
                $arrayTotal["total_".$contNumber] += $rowFactura['campo_'.$i];
                $bodyTable .= '<td style="text-align:right; '.$fila["style"].'">'.($rowFactura['campo_'.$i] * 1).'</td>';
            }
            else if($fila['type'] === "moneda"){
                $contNumber++;
                $arrayTotal["total_".$contNumber] += $rowFactura['campo_'.$i];
                $bodyTable .= '<td style="text-align:right; '.$fila["style"].'">'.validar_numero_formato($rowFactura['campo_'.$i],$IMPRIME_XLS).'</td>';
            }
            else{ $bodyTable .= '<td style="'.$fila["style"].'">'.$rowFactura['campo_'.$i].'</td>'; }
        }

        $bodyTable .= '</tr>';
    }

    $colSpan = $i-2;
    $bodyTable .= '<tr style="background-color:#000;">
                        <td style="text-align:right; padding-right:5px; color:#FFF; font-weight:bold;" colspan="'.$colSpan.'">TOTAL</td>
                        <td style="text-align:right; padding-right:5px; color:#FFF; font-weight:bold;">'.validar_numero_formato($arrayTotal["total_1"],$IMPRIME_XLS).'</td>
                        <td style="text-align:right; padding-right:5px; color:#FFF; font-weight:bold;">'.validar_numero_formato($arrayTotal["total_2"],$IMPRIME_XLS).'</td>
                        <td style="text-align:right; padding-right:5px; color:#FFF; font-weight:bold;">'.validar_numero_formato($arrayTotal["total_3"],$IMPRIME_XLS).'</td>
                    </tr>';
?>
<style>
	.my_informe_Contenedor_Titulo_informe{
        float       : left;
        width       : 100%;
        margin      : 0 0 10px 0;
        font-size   : 11px;
        font-family : Verdana, Geneva, sans-serif;
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
        float       : left;
        width       : 100%;
        font-size   : 16px;
        font-weight : bold;
	}
    .defaultFont{
        font-size       : 11px;
        border-collapse : collapse;
    }

    .labelResult{ font-weight:bold; font-size: 14px; }
    .labelResult2{ font-weight:bold; font-size: 12px;  width: 20%; }
    .labelResult3{ font-weight:bold; font-size: 12px; text-align: right; }

    .titulos{
        background   : #000;
        padding-left : 10px;
    }
    .titulos td{
        color : #FFF;
    }
    .total{
        background  : #EEE;
        font-weight : bold;
    }
    .total td{
        background  : #EEE;
        height      : 25px;
        font-weight : bold;
    }

</style>


<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body>
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center; margin-bottom:15px;">
                <table align="center" style="text-align:center;">
                    <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="font-size:13px;"><?php echo $subtitulo; ?></td></tr>
                    <tr><td style="font-size:11px;"><?php echo $datos_informe; ?><br>&nbsp;</td></tr>
                </table>
                <table class="defaultFont" style="width:1015px; border-collapse:collapse;">
                    <thead>
                        <tr class="titulos"><?php echo $bodyTitle; ?></tr>
                    </thead>
                    <tbody>
                        <?php echo $bodyTable; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br>
    <?php echo $cuerpoInforme; ?>
</body>
<script><?php echo $script; ?></script>

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
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
    else{ echo $texto; }
?>