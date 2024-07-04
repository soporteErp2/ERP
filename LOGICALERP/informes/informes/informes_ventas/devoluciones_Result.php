<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel;');
       header("Content-Disposition: attachment; filename=informe__devolucion_factura_venta_".date("Y_m_d").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa = $_SESSION['EMPRESA'];
    $desde            = $MyInformeFiltroFechaInicio;
    $hasta            = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
    // $generar          = $MyInformeFiltro_0;
    $divTitleSucursal   = '';
    $whereSucursal      = '';
    $subtitulo_cabecera = '';
    $whereVendedores    = '';

    // echo$MyInformeFiltroFechaFinal=(isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;

    if (isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal!='') {
        $whereFechas=" AND VF.fecha_registro BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
    }
    else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");

        $script = 'localStorage.MyInformeFiltroFechaInicioDevolucionVentas  = "";
                    localStorage.MyInformeFiltroFechaFinalDevolucionVentas = "";
                    localStorage.sucursal_DevolucionVentas                 = "";
                    localStorage.documento_venta                           = "Todos";
                    arraytercerosNDV.length                                = 0;
                    tercerosConfiguradosNDV.length                         = 0;
                    arrayvendedoresNDV.length                              = 0;
                    vendedoresConfiguradosNDV.length                       = 0;';
    }

    if ($documento_venta!="") {
        $whereDocumento=($documento_venta=='Todos')? "" : " AND documento_venta='".$documento_venta."'" ;
    }

    if ($idTerceros!='' ) {

        if ($idTerceros!='todos') {
            $idTercerosQuery=explode(",",$idTerceros);
            //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
            foreach ($idTercerosQuery as $indice => $valor) {
                $whereidTerceros=($whereidTerceros=='')? ' VF.id_cliente='.$valor : $whereidTerceros.' OR VF.id_cliente='.$valor ;
                $whereClientes=($whereidTerceros!='')? "AND (".$whereidTerceros.")" : "" ;
            }
        }

        $groupBy = ',VF.id_cliente';
    }

    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = ' AND VF.id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $query = mysql_query($sql,$link);
        $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }
    $sql="SELECT
                VF.id,
                VF.consecutivo,
                VF.fecha_registro,
                VF.id_documento_venta,
                VF.documento_venta,
                VF.numero_documento_venta,
                VF.nit,
                VF.cliente,
                VF.sucursal,
                VF.bodega,
                VF.estado,
                SUM(
                    VFI.cantidad * VFI.costo_unitario
                ) AS subtotal,
                #SUM(
                #    (
                #        (
                #            VFI.cantidad * VFI.costo_unitario
                #        ) * VFI.valor_impuesto
                #    ) / 100
                #) AS iva
                IF (
                    VFI.tipo_descuento = 'porcentaje',
                    (
                        SUM(
                            VFI.cantidad * VFI.costo_unitario
                        ) * VFI.descuento
                    ) / 100,
                    VFI.descuento
                ) AS descuento_pesos,
                (
                    (
                        VFI.cantidad * VFI.costo_unitario - (

                            IF (
                                VFI.tipo_descuento = 'porcentaje',
                                (
                                    SUM(
                                        VFI.cantidad * VFI.costo_unitario
                                    ) * VFI.descuento
                                ) / 100,
                                VFI.descuento
                            )
                        )
                    ) * VFI.valor_impuesto
                ) / 100 AS iva
            FROM
                devoluciones_venta AS VF
            LEFT JOIN devoluciones_venta_inventario AS VFI ON (
                VFI.activo = 1
                AND VFI.id_devolucion_venta = VF.id
            )
            WHERE
                VF.activo = 1
            AND VF.id_empresa = $id_empresa
            AND (VF.estado = 1 OR VF.estado = 3)
            $whereSucursal $whereClientes $whereFechas $whereDocumento
            GROUP BY
                VFI.id";

    $query = mysql_query($sql,$link);

    $acumuladoSubtotal = 0;
    $acumuladoIva      = 0;
    $acumuladoTotal    = 0;

    $whereId='';
    while ($row=mysql_fetch_array($query)) {
        if ($row['documento_venta']=='Factura') {
            $whereId.=($whereId=='')? 'id_factura_venta='.$row['id_documento_venta'] : ' OR id_factura_venta='.$row['id_documento_venta'] ;
        }

        if ($row['estado']==3) {
            $row['subtotal'] = 0;
            $row['iva']      = 0;
        }

        $numero_factura=($row['prefijo']!="")? $row['prefijo'].' '.$row['numero_factura'] : $row['numero_factura'] ;
        if (!isset($arrayFacturas [$row['id']])) {
            $arrayFacturas [$row['id']]= array('fecha_registro'      => $row['fecha_registro'],
                                                'id_documento_venta'     => $row['id_documento_venta'],
                                                'documento_venta'        => $row['documento_venta'],
                                                'numero_documento_venta' => $row['numero_documento_venta'],
                                                'consecutivo'            => $row['consecutivo'],
                                                'nit'                    => $row['nit'],
                                                'cliente'                => $row['cliente'],
                                                'bodega'                 => $row['bodega'],
                                                'centro_costo'           => $row['centro_costo'],
                                                'sucursal'               => $row['sucursal'],
                                                'sucursal_cliente'       => $row['sucursal_cliente'],
                                                'subtotal'               => $row['subtotal']-$row['descuento_pesos'],
                                                // 'descuento'              => $row['descuento'],
                                                'iva'                    => $row['iva'],
                                                'ReteFuente'             => '',
                                                'ReteIva'                => '',
                                                'ReteIca'                => '',
                                                'estado'                 => $row['estado'],
                                                );
        }
        else{
           $arrayFacturas [$row['id']]['subtotal'] += $row['subtotal']-$row['descuento_pesos'];
           $arrayFacturas [$row['id']]['iva']  += $row['iva'];
        }

        $acumuladoSubtotal += $row['subtotal'];
        $acumuladoIva      += $row['iva'];
        // echo $acumuladoIva.'<br>';

    }

    if ($whereId!='') {
        $sql="SELECT id_factura_venta,tipo_retencion,retencion,valor,base FROM ventas_facturas_retenciones WHERE activo=1 AND ($whereId)";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {

            // AutoRetencion
            if ($row['tipo_retencion']=='ReteFuente') {
                $arrayImpuestos[$row['id_factura_venta']]['ReteFuente'][] = array(
                                                                                    'retencion'      => $row['retencion'],
                                                                                    'valor'          => $row['valor'],
                                                                                    'base'           => $row['base'],
                                                                                );
            }
            else if ($row['tipo_retencion']=='ReteIva') {
                $arrayImpuestos[$row['id_factura_venta']]['ReteIva'][] = array(
                                                                                'retencion'      => $row['retencion'],
                                                                                'valor'          => $row['valor'],
                                                                                'base'           => $row['base'],
                                                                            );

            }
            else if ($row['tipo_retencion']=='ReteIca') {
                $arrayImpuestos[$row['id_factura_venta']]['ReteIca'][] = array(
                                                                                'retencion'      => $row['retencion'],
                                                                                'valor'          => $row['valor'],
                                                                                'base'           => $row['base'],
                                                                            );
            }

        }//FIN WHILE

        $whereId=str_replace('id_factura_venta', 'id', $whereId);
        // CONSULTAR LA INFORMACION DE LAS FACTURAS
        $sql="SELECT * FROM ventas_facturas WHERE activo=1 AND id_empresa=$id_empresa AND ($whereId)";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $arrayInfoFacturas[$row['id']] = array(
                                                    'documento_vendedor'  => $row['documento_vendedor'],
                                                    'nombre_vendedor'     => $row['nombre_vendedor'],
                                                    'nit'                 => $row['nit'],
                                                    'cliente'             => $row['cliente'],
                                                    'sucursal_cliente'    => $row['sucursal_cliente'],
                                                    'sucursal'            => $row['sucursal'],
                                                    'bodega'              => $row['bodega'],
                                                    'codigo_centro_costo' => $row['codigo_centro_costo'],
                                                    'centro_costo'        => $row['centro_costo'],
                                                );
        }

    }//FIN IF


    // RECORRER EL ARRAY ARA ARMAR EL CUERPO DEL INFORME
    foreach ($arrayFacturas as $id_nota_devolucion => $arrayResul) {
        // RECORRER LAS RETENCIONES PARA GENERAR EL CALCULO
        $ReteFuente = 0;
        $ReteIva    = 0;
        $ReteIca    = 0;

        foreach ($arrayImpuestos[$arrayResul['id_documento_venta']]['ReteFuente'] as $key => $arrayResulRetencion) {
            if ($arrayResulRetencion['base']<$arrayResul['subtotal']) {
                $ReteFuente += ($arrayResul['subtotal']*$arrayResulRetencion['valor'])/100;
            }
        }
        foreach ($arrayImpuestos[$arrayResul['id_documento_venta']]['ReteIva'] as $key => $arrayResulRetencion) {
            if ($arrayResulRetencion['base']<$arrayResul['iva']) {
                $ReteIva += ($arrayResul['iva']*$arrayResulRetencion['valor'])/100;
            }
        }
        foreach ($arrayImpuestos[$arrayResul['id_documento_venta']]['ReteIca'] as $key => $arrayResulRetencion) {
            if ($arrayResulRetencion['base']<$arrayResul['subtotal']) {
                $ReteIca += ($arrayResul['subtotal']*$arrayResulRetencion['valor'])/100;
            }
        }

        $ivaTotal       = $arrayResul['iva'] - $ReteIva;
        $total          = ($arrayResul['subtotal']+$ivaTotal)-$ReteFuente-$ReteIca;

        $acumuladoReteFuente += $ReteFuente;
        $acumuladoReteIva    += $ReteIva;
        $acumuladoReteIca    += $ReteIca;
        $acumuladoTotal      += $total;
        // $total    = $subtotal+$iva;
        $style=($style!='')? '' : 'background:#f7f7f7;' ;
        $styleDocCancelado=($arrayResul['estado']==3)? 'color:#F00A0A;font-style: italic;font-weight:bold;' : '' ;

        $bodyTable.=($IMPRIME_XLS=='true')?'<tr style="'.$style.'" >
                                                <td style="width:100px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['sucursal'].'</td>
                                                <td style="width:70px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['fecha_registro'].'</td>
                                                <td style="width:70px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['fecha_registro'].'</td>
                                                <td style="width:70px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['numero_documento_venta'].'</td>
                                                <td style="width:200px; padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['nit'].'</td>
                                                <td style="width:200px; padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                                                <td style="width:200px; padding-left: 10px;'.$styleDocCancelado.'">'.$arrayInfoFacturas[$arrayResul['id_documento_venta']]['nombre_vendedor'].'</td>
                                                <td style="width:100px; padding-left: 10px;'.$styleDocCancelado.'">'.$arrayInfoFacturas[$arrayResul['id_documento_venta']]['sucursal_cliente'].'</td>
                                                <td style="width:100px; padding-left: 10px;'.$styleDocCancelado.'">'.$arrayInfoFacturas[$arrayResul['id_documento_venta']]['codigo_centro_costo'].'</td>
                                                <td style="width:100px; padding-left: 10px;'.$styleDocCancelado.'">'.$arrayInfoFacturas[$arrayResul['id_documento_venta']]['centro_costo'].'</td>
                                                <td style="width:80px; text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['subtotal'],$IMPRIME_XLS).'</td>
                                                <td style="width:80px; text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['iva'],$IMPRIME_XLS).'</td>
                                                <td style="width:80px; text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($ReteFuente,$IMPRIME_XLS).'</td>
                                                <td style="width:80px; text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($ReteIca,$IMPRIME_XLS).'</td>
                                                <td style="width:80px; text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($ReteIva,$IMPRIME_XLS).'</td>
                                                <td style="width:80px; text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($total,$IMPRIME_XLS).'</td>
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['documento_venta'].'</td>
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['consecutivo'].'</td>
                                            </tr>'
                                            :
                                            '<tr style="'.$style.'" >
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['sucursal'].' </td>
                                                <!--<td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['bodega'].' </td>-->
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_registro'].'</td>
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['numero_documento_venta'].'</td>

                                                <td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>

                                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['subtotal'],$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['iva'],$IMPRIME_XLS).'</td>

                                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($ReteFuente,$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($ReteIca,$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($ReteIva,$IMPRIME_XLS).'</td>

                                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($total,$IMPRIME_XLS).'</td>
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['documento_venta'].'</td>
                                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['consecutivo'].'</td>
                                            </tr>'
                                            ;
    }

    $titulosTipoDoc=($IMPRIME_XLS=='true')? '<td style="width:200px; padding-left: 10px;"><b>NIT</b></td>
                                            <td style="width:200px; padding-left: 10px;"><b>CLIENTE</b></td>
                                            <td style="width:200px; padding-left: 10px;"><b>VENDEDOR</b></td>
                                            <td style="width:100px; padding-left: 10px;"><b>SUCURSAL CLIENTE</b></td>
                                            <td style="width:100px; padding-left: 10px;"><b>CODIGO CENTRO COSTOS</b></td>'
                                            :
                                            '<td style="width:200px; padding-left: 10px;"><b>CLIENTE</b></td>
                                            <td style="width:100px; padding-left: 10px;"><b>SUCURSAL CLIENTE</b></td>' ;

    $colspanTotal=($IMPRIME_XLS=='true')? 'colspan="6"' : 'colspan="6"' ;

    $headTable=($IMPRIME_XLS=='true')? '<td style="width:100px; text-align:center;"><b>SUCURSAL</b></td>
                                        <td style="width:70px; text-align:center;"><b>FECHA</b></td>
                                        <td style="width:70px; text-align:center;"><b>FECHA VENCIMIENTO</b></td>
                                        <td style="width:70px; text-align:center;"><b>N. DOC</b></td>
                                        <td style="width:200px; padding-left: 10px;"><b>NIT</b></td>
                                        <td style="width:200px; padding-left: 10px;"><b>CLIENTE</b></td>
                                        <td style="width:200px; padding-left: 10px;"><b>VENDEDOR</b></td>
                                        <td style="width:100px; padding-left: 10px;"><b>SUCURSAL CLIENTE</b></td>
                                        <td style="width:100px; padding-left: 10px;"><b>CODIGO CENTRO COSTOS</b></td>
                                        <td style="width:100px; padding-left: 10px;"><b>CENTRO COSTOS</b></td>
                                        <td style="width:80px; text-align:right;"><b>SUBTOTAL</b></td>
                                        <td style="width:80px; text-align:right;"><b>IVA</b></td>
                                        <td style="width:80px; text-align:right;"><b>RETE. FUENTE</b></td>
                                        <td style="width:80px; text-align:right;"><b>RETE. ICA</b></td>
                                        <td style="width:80px; text-align:right;"><b>RETE. IVA</b></td>
                                        <td style="width:80px; text-align:right;"><b>TOTAL</b></td>
                                        <td style="width:70px; text-align:center;"><b>DOC.</b></td>
                                        <td style="width:70px; text-align:center;"><b>CONS. NOTA</b></td>'
                                        :
                                        '<td style="width:100px; text-align:center;"><b>SUCURSAL</b></td>
                                        <!--<td style="width:100px; text-align:center;"><b>BODEGA</b></td>-->
                                        <td style="width:70px; text-align:center;"><b>FECHA</b></td>
                                        <td style="width:70px; text-align:center;"><b>N. DOC</b></td>
                                        <td style="width:200px; padding-left: 10px;"><b>CLIENTE</b></td>
                                        <td style="width:80px; text-align:right;"><b>SUBTOTAL</b></td>
                                        <td style="width:80px; text-align:right;"><b>IVA</b></td>
                                        <td style="width:80px; text-align:right;"><b>RETE. FUENTE</b></td>
                                        <td style="width:80px; text-align:right;"><b>RETE. ICA</b></td>
                                        <td style="width:80px; text-align:right;"><b>RETE. IVA</b></td>
                                        <td style="width:80px; text-align:right;"><b>TOTAL</b></td>
                                        <td style="width:70px; text-align:center;"><b>DOC.</b></td>
                                        <td style="width:70px; text-align:center;"><b>CONS. NOTA</b></td>'
                                        ;
?>
<style>
	.my_informe_Contenedor_Titulo_informe{
        float         :	left;
        width         :	100%;
        /*border-bottom :	1px solid #CCC;*/
        margin        :	0 0 10px 0;
        font-size     :	11px;
        font-family   :	Verdana, Geneva, sans-serif
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
        float     : left;
        width     : 100%;
        font-size : 16px;
        font-weight:bold;
	}
    .defaultFont{
        font-size : 11px;
        border-collapse : collapse;
        border: none;
    }
    .labelResult{ font-weight:bold;font-size: 14px; }
    .labelResult2{ font-weight:bold;font-size: 12px;  width: 20%;}
    .labelResult3{ font-weight:bold;font-size: 12px; text-align: right;}



    .titulos{
        background   : #999;
        padding-left : 10px;
        /*font-size    : 11px;*/
    }

    .titulos td{
        height : 35px;
        color  :#FFF;
    }

    .total{
        background  : #EEE;
        font-weight : bold;
        color: #8E8E8E;
    }
    .total td{
        border-top    : 1px solid #999;
        border-bottom : 1px solid #999;
        background    : #EEE;
        padding-left  : 10px;
        height        : 25px;
        font-weight   : bold;
    }

</style>


<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
                <table align="center" style="text-align:center;" >
                    <tr ><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr  ><td  style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="font-size:13px;"><b>Informe Devoluciones de Venta</b><br> <?php echo $subtitulo_cabecera; ?><br>&nbsp;</td></tr>
                    <?php echo $datos_informe; ?>
                </table>
               <!--  <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
                <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
                <div style="margin-bottom:8px;" >A <?php echo $arrayMeses[$mes].' '.$dia.' de '.$anio;?></div> -->
                <table class="defaultFont" style="width:99%" >
                    <tr class="titulos">
                        <!--<td style="width:100px; text-align:center;"><b>SUCURSAL</b></td>
                         <td style="width:100px; text-align:center;"><b>BODEGA</b></td>
                        <td style="width:70px; text-align:center;"><b>FECHA</b></td>
                        <td style="width:70px; text-align:center;"><b>N. DOC</b></td>
                        <?php echo $titulosTipoDoc; ?>
                        <td style="width:100px; padding-left: 10px;"><b>CENTRO COSTOS</b></td>
                        <td style="width:80px; text-align:right;"><b>SUBTOTAL</b></td>
                        <td style="width:80px; text-align:right;"><b>IVA</b></td>
                        <td style="width:80px; text-align:right;"><b>RETE. FUENTE</b></td>
                        <td style="width:80px; text-align:right;"><b>RETE. ICA</b></td>
                        <td style="width:80px; text-align:right;"><b>RETE. IVA</b></td>
                        <td style="width:80px; text-align:right;"><b>TOTAL</b></td>
                        <td style="width:70px; text-align:center;"><b>DOC.</b></td>
                        <td style="width:70px; text-align:center;"><b>CONS. NOTA</b></td>-->

                        <?php echo $headTable; ?>

                    </tr>
                    <?php echo $bodyTable; ?>
                    <tr><td>&nbsp;</td></tr>
                    <tr class="total"  >
                        <td style="text-align:center;" <?php echo  $colspanTotal; ?>>TOTAL DEVOLUCIONES</td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoSubtotal,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoIva,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoReteFuente,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoReteIca,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoReteIva,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;" > <?php echo validar_numero_formato($acumuladoTotal,$IMPRIME_XLS); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <br>
    <?php echo $cuerpoInforme.'<script>'.$script.'</script>'; ?>
</body>
<?php
    $footer='<div style="text-align:right;font-weight:bold;font-size:12px;">Pagina {PAGENO}/{nb}</div>';
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
    // if(!isset($ORIENTACION)){$ORIENTACION = 'L';}
	$ORIENTACION = 'p';
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}

	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}
    else{$MS=10;$MD=10;$MI=10;$ML=10;}

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
					10,				// margin header
					10,				// margin footer
					'L'	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHtmlFooter($footer);
        // $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{	$mpdf->Output($documento.".pdf",'I');}
		exit;
	}
    else{ echo $texto; }
?>