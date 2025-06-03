<?php
    ini_set('max_execution_time', 10000);
    include('../../../../configuracion/conectar.php');
    include("../../../../configuracion/define_variables.php");
    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=Informe_objetivos.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];

    ob_start();

    /*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/

    if($MyInformeFiltro_Funcionarios == undefined){
           $MyInformeFiltro_Funcionarios = '';
    } 
    if($MyInformeFiltro_Clientes == undefined){
           $MyInformeFiltro_Clientes = '';
    }

    //FILTRO DE FUNCIONARIOS  

    $array = explode(")",$MyInformeFiltro_Funcionarios);

    $funcionario = str_replace("(","",$array[0]);

    if($funcionario == ''){
        $nombreFuncionario = 'TODOS';
        $whereFuncionario  = "";
    }else{
        $nombreFuncionario = $mysql->result($mysql->query("SELECT nombre FROM empleados WHERE id = $funcionario"),0,"nombre");
        $whereFuncionario  = ' = '.$funcionario;
    }

    //FILTRO DE CLIENTES

    $array = explode(")",$MyInformeFiltro_Clientes);

    $cliente = str_replace("(","",$array[0]);

    if($cliente == ''){
        $nombreCliente = 'TODOS';
        $whereCliente  = "LIKE '%'";
    }else{
        $nombreCliente = $mysql->result($mysql->query("SELECT nombre FROM terceros WHERE id = $cliente"),0,"nombre");
        $whereCliente  = ' = '.$cliente;
    }

    if($MyInformeFiltro_2 == undefined){
           $MyInformeFiltro_2 = '';
    }
    if($MyInformeFiltro_3 == undefined){
           $MyInformeFiltro_3 = '';
    }
    if($MyInformeFiltro_4 == undefined){
           $MyInformeFiltro_4 = '';
    }

    $andProbabilidad = '';
    $probabilidad    = "TODAS";
    if($MyInformeFiltro_2 == '0'){
        $andProbabilidad = "AND CO.probabilidad_exito = 'alta'";
        $probabilidad    = "Alta";
    }
    if($MyInformeFiltro_2 == '1'){
        $andProbabilidad = "AND CO.probabilidad_exito = 'media'";
        $probabilidad    = "Media";        
    }
    if($MyInformeFiltro_2 == '2'){
        $andProbabilidad = "AND CO.probabilidad_exito = 'baja'";
        $probabilidad    = "Baja";        
    }


    $andEstado = '';   
    if($MyInformeFiltro_3 == ''){
        $andEstado = '';
        $estado    = 'TODOS';
    }
    else{
        $andEstado = "AND CO.id_estado = '$MyInformeFiltro_3'";
        $estado    = $mysql->result($mysql->query("SELECT nombre FROM configuracion_estados_proyectos WHERE activo = 1 AND id='$MyInformeFiltro_3'",$link),0,'nombre');
    }

    $andLinea = '';   
    if($MyInformeFiltro_4 == ''){
        $andLinea = '';
        $linea    = 'TODAS';
    }
    else{
        $andLinea = "AND CO.id_linea = '$MyInformeFiltro_4'";
        $linea    = $mysql->result($mysql->query("SELECT nombre FROM configuracion_lineas_negocio WHERE activo = 1 AND id='$MyInformeFiltro_4'",$link),0,'nombre');
    }
    





?>

<style>
    .my_informe_Contenedor_Titulo_informe{
        float               :   left;
        width               :   100%;
        border-bottom       :   1px solid #CCC;
        margin              :   0 0 10px 0;
        font-size           :   11px;
        font-family         :   Verdana, Geneva, sans-serif
    }
    .my_informe_Contenedor_Titulo_informe_label{
        float               :   left;
        width               :   130px;
        font-weight         :   bold;
    }
    .my_informe_Contenedor_Titulo_informe_detalle{
        float               :   left;
        width               :   210px;
        padding             :   0 0 0 5px;
        white-space             : nowrap;
        overflow                : hidden;
        text-overflow           : ellipsis;
    }
    .my_informe_Contenedor_Titulo_informe_Empresa{
        float               :   left;
        width               :   370px;
        font-size           :   16px;
    }
    .my_informe_Contenedor_Titulo_informe1{
        float               :   left;
        width               :   100%;
        margin              :   0 0 10px 0;
        font-size           :   11px;
        font-family         :   Verdana, Geneva, sans-serif
    }
</style>

<!-- --------------------------   DESARROLLO DEL INFORME  ------------------------------------ -->
<!-- ----------------------------------------------------------------------------------------- -->
<body style="font-size:11px; font-family:Verdana, Geneva, sans-serif;">
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
                        <td class="my_informe_Contenedor_Titulo_informe_label">Funcionario</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreFuncionario; ?></td>
                    </tr>
                    <tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Cliente</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreCliente; ?></td>
                    </tr>
                    <tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Linea de Negocio</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $linea; ?></td>
                    </tr>
                    <tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Estado</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $estado; ?></td>
                    </tr>
                    <tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Probabilidad</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $probabilidad; ?></td>
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

            echo'<br>
                 <table style="width:1200px; float:left;border-spacing:0px;font-size:12px;">
                    <tr>
                        <td style="width:125px;padding-bottom:5px;font-weight:bold">Funcionario</td>
                        <td style="width:125px;padding-bottom:5px;padding-left:5px;font-weight:bold;">CLIENTE</td>
                        <td style="width:115px;padding-bottom:5px;padding-left:5px;font-weight:bold;">LINEA NEGOCIO</td>
                        <td style="width:115px;padding-bottom:5px;padding-left:5px;font-weight:bold;">PROYECTO</td>
                        <td style="width:95px;padding-bottom:5px;padding-left:5px;font-weight:bold;">N. PROYECTO</td>
                        <td style="width:105px;padding-bottom:5px;padding-left:5px;font-weight:bold;">COTIZACION</td>
                        <td style="width:95px;padding-bottom:5px;padding-left:5px;font-weight:bold;">VALOR</td>
                        <td style="width:105px;padding-bottom:5px;padding-left:5px;font-weight:bold;">PROBABILIDAD</td>
                        <td style="width:105px;padding-bottom:5px;padding-left:5px;font-weight:bold;">ESTADO</td>
                        <td style="width:78px;padding-bottom:5px;padding-left:5px;font-weight:bold;">F. CREACION</td>
                        <td style="width:85px;padding-bottom:5px;padding-left:5px;font-weight:bold;">F. ACTUALIZA</td>
                        <td style="width:67px;padding-bottom:5px;padding-left:5px;font-weight:bold;">F. VENCE</td>
                        <td style="width:130px;padding-bottom:5px;padding-left:5px;font-weight:bold;">CONTACTO</td>                
                    </tr>
                 </table>';

            $SQL = "SELECT 
                                CO.id,
                                CO.estado,
                                CO.prioridad,
                                CO.objetivo,
                                CO.cliente,
                                CO.linea_negocio,
                                CO.probabilidad_exito,
                                CO.estado_proyecto,
                                CO.fecha_creacion,
                                CO.usuario,
                                CO.fecha_actualizacion,
                                CO.valor,
                                CO.vencimiento,
                                CO.observacion                                
                             FROM crm_objetivos AS CO
                             INNER JOIN terceros AS T ON (T.id = CO.id_cliente AND T.activo = 1 AND T.id $whereCliente)
                             WHERE
                             CO.vencimiento BETWEEN '$MyInformeFiltroFechaInicio 00:00:00' AND '$MyInformeFiltroFechaFinal 23:59:59'
                             AND CO.id_usuario $whereFuncionario
                             AND CO.activo = 1
                             $andEstado         
                             AND CO.id_empresa = $id_empresa
                             AND CO.id_sucursal = $id_sucursal
                             ORDER BY CO.id";//LOGICA DE LOS PEDIDOS ADICIONALES
                    //AND tipo_evento = 0
            //echo $SQL;
            $consul = $mysql->query($SQL,$link);

            $acumulador = 0;



            while($row=$mysql->fetch_array($consul)){

                echo'<table style="width:1200px; float:left;border-spacing:0px;font-size:12px; ">
                        <tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:normal; border-spacing:0px">
                            <td style="width:125px;">'.$row['usuario'].'<span style="font-size:9px; font-weight:bold">'.$TipEve.'</span></td>
                            <td style="width:125px;padding-left:5px">'.$row['cliente'].'</td>
                            <td style="width:125px;padding-left:5px">'.$row['linea_negocio'].'</td>                           
                            <td style="width:115px;padding-left:5px">'.$row['objetivo'].'</td>
                            <td style="width:95px;padding-bottom:5px;padding-left:5px;">'.$row['id'].'</td>
                            <td style="width:105px;padding-left:5px">&nbsp;</td>
                            <td style="width:95px;padding-bottom:5px;padding-left:5px;">'.$row['valor'].'</td>
                            <td style="width:105px;padding-bottom:5px;padding-left:5px;">'.$row['probabilidad_exito'].'</td>
                            <td style="width:105px;">'.$row['estado_proyecto'].'</td>
                            <td style="width:78px;padding-left:5px">'.str_replace(' ',' - ',$row['fecha_creacion']).'</td>
                            <td style="width:85px;padding-left:5px">'.str_replace(' ',' - ',$row['fecha_actualizacion']).'</td>
                            <td style="width:67px;padding-bottom:5px;padding-left:5px;">'.$row['vencimiento'].'</td>
                            <td style="width:130px;padding-bottom:5px;padding-left:5px;">'.$row['ggg'].'</td>
                        </tr>
                     </table>';
            }


            /*echo '<table style=" width:100%; font-size:12px;border-spacing:0px; overflow: hidden; margin-top:5px; font-weight:bold;">
                       <tr >
                            <td colspan="3" style=" width:470px; float:left; font-size:10px;">'.$acumulador.' </td>
                       </tr>
                 </table>';*/


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
                    'utf-8',        // mode - default ''
                    $HOJA,          // format - A4, for example, default ''
                    12,             // font size - default 0
                    '',             // default font family
                    $MI,            // margin_left
                    $MD,            // margin right
                    $MS,            // margin top
                    $ML,            // margin bottom
                    3,              // margin header
                    10,             // margin footer
                    $ORIENTACION    // L - landscape, P - portrait
                );

        $mpdf->simpleTables = true;
        $mpdf->packTableData= true;
        $mpdf->SetAutoPageBreak(TRUE, 15);
        $mpdf->SetTitle ('Informe SIIP' );
        $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
        $mpdf->SetDisplayMode ( 'fullpage' );
        $mpdf->SetHeader("");
        $mpdf->WriteHTML(utf8_encode($texto));
        if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{ $mpdf->Output($documento.".pdf",'I');}
        exit;
    }else{
        echo $texto;
    }
?>