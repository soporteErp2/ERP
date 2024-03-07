<?php
    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');

    $id_empresa = $_SESSION['EMPRESA'];
    // echo "$opc <br>$params";
    // var_dump($_POST['params']);

    switch ($opc) {
    	case 'filtro_informe':
    		filtro_informe($id_empresa,$mysql);
    		break;
        case 'valoresCuentasSeccion':
            valoresCuentasSeccion($params);
            break;
    }

    function filtro_informe($id_empresa,$mysql){
        $sql="SELECT id,codigo,nombre FROM informes_formatos WHERE activo=1 AND id_empresa=$id_empresa";
        $query=$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)) {
            $reports .= "<option value='$row[id]'>$row[codigo] - $row[nombre]</option>";
        }

        ?>
            <select id="id_formato" style="margin-top: 9px;" onchange="carga_wizard(this.value)">
                <option value="">Seleccione...</option>
                <?php echo $reports; ?>
            </select>

            <script>
                function carga_wizard(id_formato) {
                    Ext.get('content-wizard').load({
                        url     : 'informes/report/wizard_report.php',
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            id_formato : id_formato,
                        }
                    });
                }
            </script>
        <?php

    }

    function valoresCuentasSeccion($params){
        $params = json_decode($_POST['params'],true);
        $params['custonWhere'] = base64_encode( $params['custonWhere']);
        // var_dump($params);
        include '../../../funciones_globales/Clases/ClassCurl.php';
        $objCurl = new ClassCurl();

        $paramsCurl['request_url']    = "logicalerp.localhost/api/v1/contabilidad/?fecha_inicio=$params[fecha_inicio]&fecha_final=$params[fecha_final]&custonWhere=\"$params[custonWhere]\"&asientos=$params[asientos]";
        $paramsCurl['request_method'] = "GET";
        $paramsCurl['Authorization']  = "Authorization: Basic ".base64_encode('usuario.informes:$2y$10$Kye1ukwGdbtume0/QiIIB.igXWGn1flxaiHvzPaavJwwQWFCmo9Gi:'.$params['nit']);
        $response = $objCurl->curl($paramsCurl);

        $arrayResponse = (is_array($response))? $response : json_decode($response,true) ;
        if ($arrayResponse['status']=='failed'){
            echo "<b>Error</b><br>".$arrayResponse['detalle'];
            exit;
        }

        // var_dump($paramsCurl);
        // echo "<br>";
        // echo "<br>";
        // var_dump($response);
        foreach ($arrayResponse['data'] as $key => $arrayResult) {
            // $arrayCuentas['codigo_cuenta']['codigo_cuenta'] = $arrayResult['codigo_cuenta'];
            $arrayCuentas[$arrayResult['codigo_cuenta']]['cuenta']        = $arrayResult['cuenta'];
            $arrayCuentas[$arrayResult['codigo_cuenta']]['debito']        += $arrayResult['debito'];
            $arrayCuentas[$arrayResult['codigo_cuenta']]['credito']       += $arrayResult['credito'];
        }

        ?>
            <style>
                .table-form{
                    border-collapse: collapse;
                }
            </style>
            <div style="width: 100%;height: 100%;background-color: #FFF; overflow-y: auto;">
                <table class="table-form" style="width:calc(100% - 20px);margin-bottom: 20px;">
                    <tr class="thead">
                        <td>Cuenta</td>
                        <td style="width: 120px">Descripcion</td>
                        <td>Debito</td>
                        <td>Credito</td>
                        <td>Debito - Credito</td>
                    </tr>
                    <?php
                        foreach ($arrayCuentas as $cuenta => $arrayResult) {
                            $acumDebito  += $arrayResult['debito'];
                            $acumCredito += $arrayResult['credito'];
                            ?>
                                <tr>
                                    <td><?= $cuenta; ?></td>
                                    <td><?= $arrayResult['cuenta'] ?></td>
                                    <td><?= number_format($arrayResult['debito'],0,",",".") ?></td>
                                    <td><?= number_format($arrayResult['credito'],0,",",".") ?></td>
                                    <td><?= number_format($arrayResult['debito']-$arrayResult['credito'],0,",",".") ?></td>
                                </tr>
                            <?php
                        }
                    ?>
                    <tr class="thead">
                        <td>Totales</td>
                        <td></td>
                        <td><?= number_format($acumDebito,0,",","."); ?></td>
                        <td><?= number_format($acumCredito,0,",","."); ?></td>
                        <td><?= number_format($acumDebito-$acumCredito,0,",","."); ?></td>
                    </tr>
                </table>
            </div>
        <?php

    }

?>