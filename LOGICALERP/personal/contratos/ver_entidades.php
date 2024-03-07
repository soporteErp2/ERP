<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa=$_SESSION['EMPRESA'];

    $sql="SELECT
                id_entidad,
                documento_entidad,
                nombre_entidad,
                fecha_inicio,
                fecha_final,
                id_concepto,
                concepto
            FROM empleados_contratos_entidades_traslados
            WHERE activo=1
            AND id_empresa=$id_empresa
            AND id_empleado=$id_empleado
            AND id_contrato=$id_contrato
            ";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $id_entidad   = $row['id_entidad'];
        $fecha_inicio = $row['fecha_inicio'];
        $fecha_final  = $row['fecha_final'];
        $id_concepto  = $row['id_concepto'];

        $mainArray [$id_entidad] [$fecha_inicio] [$fecha_final] [$id_concepto] = $row['concepto'];
        $arrayEntidad[$id_entidad] = array('documento_entidad' => $row['documento_entidad'],
                                            'nombre_entidad' => $row['nombre_entidad'] );

    }

    $contFilas   = 0;
    $firstTd     = '';
    $tdConceptos = '';
    $bodyTable        = '';

    // CREAR EL CUERPO DE LA TABLA
    foreach ($mainArray as $id_entidad => $mainArray1) {
        foreach ($mainArray1 as $fecha_inicio => $mainArray2) {
            foreach ($mainArray2 as $fecha_final => $mainArray2) {
                foreach ($mainArray2 as $id_concepto => $resul) {
                    $contFilas ++;
                    if ($firstTd=='') {
                        $firstTd = '<td class="filaDivs" title="'.$resul.'">'.$resul.'</td>';
                        continue;
                    }
                    $tdConceptos.='<tr><td class="filaDivs" title="'.$resul.'">'.$resul.'</td></tr>';
                }
                $bodyTable.='<tr>
                                <td class="filaDivs" rowspan="'.$contFilas.'" >'.$fecha_inicio.'</td>
                                <td class="filaDivs" rowspan="'.$contFilas.'" >'.$fecha_final.'</td>
                                <td class="filaDivs" rowspan="'.$contFilas.'" title="'.$arrayEntidad[$id_entidad]['nombre_entidad'].'">'.$arrayEntidad[$id_entidad]['nombre_entidad'].'</td>
                                '.$firstTd.'
                            </tr>'.$tdConceptos;
                $contFilas   = 0;
                $firstTd     = '';
                $tdConceptos = '';
            }
        }
    }

    if ($bodyTable=='') {
        $bodyTable='<tr><td colspan="4" style="font-style:italic;color:#999;font-weight:bold;font-size:12px; text-align:center;padding:10px;">No se han realizado traslados</td></tr>';
    }

?>

<style>
    .titulos_ventana{
        color         : #15428B;
        font-weight   : bold;
        font-size     : 13px;
        font-family   : tahoma,arial,verdana,sans-serif;
        text-align    : center;
        margin-top    : 15px;
        margin-bottom : 15px;
        float         : left;
        width         : 100%;
    }

    .contenedor_tablas_cuentas{
        float            : left;
        width            : 90%;
        height           : 85%;
        background-color : #FFF;
        margin-top       : 10px;
        margin-left      : 20px;
        /*border           : 1px solid #D4D4D4;*/
    }

    .headDivs{
        /*float            : left;*/
        background-color : #F3F3F3;
        padding          : 6px;
        font-size        : 11px;
        font-weight      : bold;
        /*border-right     : 1px solid #D4D4D4;*/
        /*border-bottom    : 1px solid #D4D4D4;*/
    }

    .content-tabla{
        height: 83%;
        width: 100%;
        overflow: auto;
    }

    .table{
        border-collapse : collapse;
        width           : 482px;
        table-layout    : fixed;

    }

    .table td{
        /*border-top: 1px solid #D4D4D4;*/
        /*border-left: 1px solid #D4D4D4;*/
        border: 1px solid #D4D4D4;
    }


    .table tbody{
        background-color : #FFF;
        height           : 100px;
    }

    .filaDivs{
        font-size        : 11px;
        /*float         : left;*/
        /*border-right  : 1px solid #D4D4D4;*/
        padding       :  5px;
        overflow      : hidden;
        white-space   : nowrap;
        text-overflow : ellipsis;
    }

</style>

<div style="width:100%;border-top:1px solid #8DB2E3;">
    <div class="titulos_ventana">TRASLADOS DE ENTIDADES DEL EMPLEADO EN EL CONTRATO</div>

    <div class="content-tabla">
        <table align="center" cellspacing="0" class="table" width="482">

            <thead>
                <tr>
                    <td class="headDivs" colspan="2" style="width: 130px;">PERIODO</td>
                    <td class="headDivs">ENTIDAD</td>
                    <td class="headDivs">CONCEPTOS</td>
                </tr>
            </thead>

            <tbody>
                <?php echo $bodyTable; ?>
                <!-- <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr>

                <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr>

                <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr>

                <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr>

                <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr>

                <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr>

                <tr>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >00-00-0000</td>
                    <td class="filaDivs" rowspan="3" >ENTIDAD</td>
                    <td class="filaDivs">EPS</td>
                </tr>
                <tr>
                    <td class="filaDivs">EPS</td>
                </tr>
                 <tr>
                    <td class="filaDivs">EPS</td>
                </tr> -->

            </tbody>

        </table>
    </div>

</div>
