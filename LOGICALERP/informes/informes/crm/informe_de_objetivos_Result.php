<?php
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	ob_start();

    /*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/

	if($MyInformeFiltro_Clientes == undefined){
           $MyInformeFiltro_Clientes = '';
    }

    if($MyInformeFiltro_Funcionarios == undefined){
           $MyInformeFiltro_Funcionarios = '';
    }

    if($MyInformeFiltro_2 == undefined){
           $MyInformeFiltro_2 = '';
    }    

    $MyInformeFiltroEmpresa  = $_SESSION['EMPRESA'];
    $MyInformeFiltroSucursal = $_SESSION['SUCURSAL'];

    $nombre_empresa = $mysql->result($mysql->query("SELECT * FROM empresas WHERE id = $MyInformeFiltroEmpresa",$link),0,"nombre");
    $WhereEmpresa   = 'AND CO.id_empresa = '.$MyInformeFiltroEmpresa;

    $nombre_sucursal = $mysql->result($mysql->query("SELECT * FROM empresas_sucursales WHERE id = $MyInformeFiltroSucursal",$link),0,"nombre");
    $WhereSucursal   = 'AND CO.id_sucursal = '.$MyInformeFiltroSucursal;

    $EstadoAct           = $MyInformeFiltro_2;
    $EstadoActividadName = array("SIN FINALIZAR","FINALIZADOS");
    if($EstadoAct == ''){
        $EstadoActividad = 'TODOS';
        $whereEstado     = "LIKE '%'";
    }else{
        $EstadoActividad = $EstadoActividadName[$EstadoAct];
        $whereEstado     = '= '.$EstadoAct;
    }

    //EXTRAIGO EL ID QUE ESTA ENTRE PARENTESIS

    $array = explode(")",$MyInformeFiltro_Clientes);

    $cliente = str_replace("(","",$array[0]);

    if($cliente == ''){
        $nombreCliente = 'TODOS';
        $whereCliente  = "LIKE '%'";
    }else{
        $nombreCliente = $mysql->result($mysql->query("SELECT nombre FROM terceros WHERE id = $cliente"),0,"nombre");
        if($nombreCliente == ''){
            $nombreCliente = $mysql->result($mysql->query("SELECT nombre_comercial FROM terceros WHERE id = $cliente"),0,"nombre_comercial");
        }
        $whereCliente  = ' = '.$cliente;
    }


    $array = explode(")",$MyInformeFiltro_Funcionarios);

    $funcionario = str_replace("(","",$array[0]);

    if($funcionario == ''){
        $nombreFuncionario = 'TODOS';
        $whereFuncionario  = "LIKE '%'";
    }else{
        $nombreFuncionario = $mysql->result($mysql->query("SELECT nombre FROM empleados WHERE id = $funcionario"),0,"nombre");
        $whereFuncionario  = ' = '.$funcionario;
    }



?>
<style>
	.my_informe_Contenedor_Titulo_informe{
		float				:	left;
		width				:	100%;
		border-bottom		:	1px solid #CCC;
		margin				:	0 0 10px 0;
		font-size			:	11px;
		font-family			:	Verdana, Geneva, sans-serif
	}
	.my_informe_Contenedor_Titulo_informe_label{
		float				:	left;
		width				:	130px;
		font-weight			:	bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
		float				:	left;
		width				:	210px;
		padding				:	0 0 0 5px;
	    white-space             : nowrap;
        overflow                : hidden;
        text-overflow           : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
		float				:	left;
		width				:	370px;
		font-size			:	16px;
	}

    .INproyecto16{float:left; background-image: url('/LOGICALERP/crm/images/proyecto16.png'); background-repeat:no-repeat;}
    .estado1{float:left; background-image: url('/LOGICALERP/crm/images/1.png'); background-repeat:no-repeat; background-position: top right}
    .estado0{float:left; background-image: url('/LOGICALERP/crm/images/0.png'); background-repeat:no-repeat; background-position: top right}
    .t1{float:left; background-image: url('/LOGICALERP/crm/images/t1.png'); background-repeat:no-repeat;}
    .t2{float:left; background-image: url('/LOGICALERP/crm/images/t2.png'); background-repeat:no-repeat;}
    .t3{float:left; background-image: url('/LOGICALERP/crm/images/t3.png'); background-repeat:no-repeat;}
    .INuser0{float:left; background-image: url('/LOGICALERP/crm/images/user0.png'); background-repeat:no-repeat;}
    .INuser1{float:left; background-image: url('/LOGICALERP/crm/images/user1.png'); background-repeat:no-repeat;}
    .estadoP{float:left; background-image: url('/LOGICALERP/crm/images/saved.png'); background-repeat:no-repeat;}

    .acciones{
        border-top: 1px solid #CCC
    }
    .finalizado{
        border-top      : 1px solid #CCC;
        border-bottom   : 1px solid #CCC;
        border-right    : 1px solid #CCC;
        border-left     : 3px solid #333;
    }

    .Color0A{color:#333; font-weight: bold; background: #FF9999;}.Color0B{color:#333;}.Color0C{color:#333; font-weight: bold;}  
    .Color1A{color:#333; font-weight: bold; background: #99FF99;}.Color1B{color:#333;}.Color1C{color:#333;} 

</style>

<!----------------------------   DESARROLLO DEL INFORME  -------------------------------------->
<!--------------------------------------------------------------------------------------------->

    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left;width:100%">
            <div style="width:100%; border-bottom:1px solid #CCC; font-weight:bold; font-size:18px; text-align:left;"><?php echo $nombre_informe ?></div>
        </div>
        <div style="float:left; width:370px">
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Inicial</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaInicio)?></div>
            </div>
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Final</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaFinal)?></div>
            </div>
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Sucursal</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombre_sucursal; ?></div>
            </div>
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Vendedor</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreFuncionario; ?></div>
            </div>
             <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Cliente</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreCliente; ?></div>
            </div>
            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Estado</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $EstadoActividad; ?></div>
            </div>
            <!-- <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label">Fecha Generacion</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga_hora_m(date('Y-m-d H:s:i'))?></div>
            </div> -->
        </div>
        <div style="float:left; width:370px">
            <div style="float:left;width:100%; text-align:center">
                <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
            </div>
        </div>
    </div>

<?php
    
    /*--------------------------------------------------CUERPO DEL INFORME---------------------------------------------------*/
    $sqlCRM = "SELECT 
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
                             LEFT(CO.vencimiento,10) BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
                             AND CO.id_usuario $whereFuncionario
                             AND CO.activo = 1                             
                             AND CO.estado $whereEstado
                             $WhereEmpresa
                             $WhereSucursal
                             ORDER BY CO.id";

    $consul = $mysql->query($sqlCRM,$link);

    while($row = $mysql->fetch_array($consul)){

        $fecha_actualizacion = fecha_larga_hora_m($row['fecha_actualizacion']);
        $fecha_creacion      = fecha_larga_hora_m($row['fecha_creacion']);

        if($row['fecha_actualizacion'] == ''){
            $fecha_actualizacion = '';
        }
        if($row['fecha_creacion'] == ''){
            $fecha_creacion      = '';
        }
        
?>
        <!-- OBJETIVOS O PROYECTOS -->

        <div class="RedondeadoSombraIN Color<?php echo $row['estado'] ?>A" style="float:left; width:740px; height:20px; padding: 0 0 0 0; margin: 20px 0 0 0; font-size:12px;">
            <div style="float:left; width:16px; height:16px; margin: 2px 0 0 0">
                <img src="../crm/images/prioridades/prioridad_<?php echo $row['prioridad'] ?>.png" style="" width="16" height="16" onclick="">
            </div>
            <div class="INproyecto16" style="float:left; width:16px; height:16px; margin: 2px 0 0 5"></div>         
            <div class="estado<?php echo $row['estado'] ?>" style="float:left; width:695px; margin: 2px 0 0 4px">
                <span style="font-weight: bold; font-size: 12px">Proyecto: </span><?php echo $row['objetivo'] ?>
            </div>
        </div>
        <div class="Color<?php echo $row['estado'] ?>B" style="float:left; width:740px; padding: 0 0 0 0; margin: 0 0 0 0; font-size:11px;">
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Cliente: <b><span style="color:#333"><?php echo $row['cliente'] ?></span></b></div>
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Linea de Negocio: <b><span style="color:#333"><?php echo $row['linea_negocio'] ?></span></b></div>            
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Probabilidad de Exito: <b><span style="color:#333"><?php echo $row['probabilidad_exito'] ?></span></b></div> 
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Estado: <b><span style="color:#333"><?php echo $row['estado_proyecto'] ?></span></b></div>
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Creaci&oacute;n: <b><span style="color:#333"><?php echo $fecha_creacion ?></span></b></div> 
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Usuario: <b><span style="color:#333"><?php echo $row['usuario'] ?></span></b></div>    
            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Ultim. Actualizaci&oacute;n: <b><span style="color:#333"><?php echo $fecha_actualizacion; ?></span></b></div>    

            <div style="float:left; width:400; margin: 2px 0 0 0; color:#777 ">Valor: <b><span style="color:#333"><?php echo $row['valor'] ?></span></b></div>        
            <div class="RedondeadoSombra" style="float:right; width:300; margin: 2px 2px 0 0; padding: 0 3px 0 0; text-align:right;"><span style="color:#777">Vencimiento.</span> <b><?php echo fecha_larga_hora_m($row['vencimiento']) ?></b></div>
            <div style="float:left; width:740px; margin: 2px 0 0 0px"><?php echo $row['observacion'] ?></div>
        </div>

        <?php
            //CONSULTA EL HISTORICO DE ESTADOS DEL OBJETIVO
            $consulE = $mysql->query("SELECT * FROM crm_objetivos_log WHERE id_objetivo = $row[id] ORDER BY id ",$link);
            while($rowE = $mysql->fetch_array($consulE)){
        ?>        

                <div  style="float:left; width:700px; height:20px; background:#95D8F8; padding: 0 0 0 0; margin: 5px 0 0 0px; font-size:12px;">
                    <div class ="estadoP" style="float:left; width:16px; height:16px; margin: 2px 0 0 1px;" ></div>
                    <div style="float:left;font-weight: bold; width:677px; margin: 2px 0 0 4px"><?php echo $rowE['estado'] ?></div>
                </div>
                <div style="float:left; width:700px; padding: 0 0 0 0; margin: 0 0 0 10px; font-size:11px;">
                    <div style="float:left; width:320px; margin: 2px 0 0 0; padding: 0 0 0 10px;">
                        <span style="color:#777">Fecha: </span>
                        <b><?php echo fecha_larga_hora_m($rowE['fecha'].' '.$rowE['hora']) ?>
                        </b>
                    </div>
                    <div style="float:left; width:320px; margin: 2px 0 0 0; padding: 0 0 0 20px;">
                        <span style="color:#777">Usuario: </span>
                        <b><?php echo $rowE['nombre'] ?>
                        </b>
                    </div>
                </div>

        <?php
            }
            echo '<div  style="float:left; width:700px; height:20px; background:#888; padding: 0 0 0 0; margin: 5px 0 0 0px; font-size:12px;color:#FFF">
                    <div style="float:left; width:16px; height:16px; margin: 2px 0 0 1px;" ></div>
                    <div style="float:left;font-weight: bold; width:677px; margin: 2px 0 0 4px">Actividades</div>
                  </div>';
            $consul2 = $mysql->query("SELECT * FROM crm_objetivos_actividades WHERE id_objetivo = $row[id] AND activo = 1",$link);
            while($row2 = $mysql->fetch_array($consul2)){
        ?>
            <!-- ACTIVIDADES -->

            <div class="RedondeadoSombraIN Color<?php echo $row2['estado'] ?>C" style="float:left; width:700px; height:20px; background:#DDD; padding: 0 0 0 0; margin: 5px 0 0 20px; font-size:12px;">
                <div class="t<?php echo $row2['tipo'] ?>" style="float:left; width:16px; height:16px; margin: 2px 0 0 1px;" ></div>
                <div class="estado<?php echo $row2['estado'] ?>" style="float:left; width:677px; margin: 2px 0 0 4px"><?php echo $row2['tema'] ?></div>
            </div>

            <div class="Color<?php echo $row2['estado'] ?>B" style="float:left; width:700px; padding: 0 0 0 0; margin: 0 0 0 20px; font-size:11px;">
                <div class="INuser<?php echo $row['estado'] ?>" style="float:left; width:320px; margin: 2px 0 0 0; padding: 0 0 0 20px;">
                    <span style="color:#777">Asignado a: </span>
                    <b><?php echo $row2['asignado'] ?>
        <?php
                $consul2_1  = $mysql->query("SELECT * FROM crm_objetivos_actividades_personas WHERE id_actividad = $row2[id] AND id_asignado > 0",$link);
                while($row2_1 = $mysql->fetch_array($consul2_1)){
        ?>
                    <?php echo ','.$row2_1['asignado'] ?>
        <?php
                }
        ?>
                    </b>
                </div>                
                 <div style="float:left; width:700px; margin: 2px 0 0 20px">
                     <div style="float:left; width:300; margin: 2px 2px 0 0; padding: 0 3px 0 0; text-align:left;">
                        <span style="color:#777">Creaci&oacute;n.</span><b>
                        <?php echo fecha_larga_hora_m($row2['fecha']) ?></b>
                    </div>
                    <div style="float:left; width:300; margin: 2px 2px 0 0; padding: 0 3px 0 0; text-align:right;">
                        <span style="color:#777">Usuario.</span><b>
                        <?php echo $row2['usuario'] ?></b>
                    </div>               
                 </div>
                 <div style="float:left; width:700px; margin: 2px 0 0 20px">
                    <div style="float:left; width:300; margin: 2px 2px 0 0; padding: 0 3px 0 0; text-align:left;">
                          <span style="color:#777">Vencimiento.</span><b>
                          <?php echo fecha_larga_hora_m($row2['fechaf'].' '.$row2['horaf']) ?></b>
                    </div>
                </div>
                <div style="float:left; width:700px; margin: 2px 0 0 20px"><?php echo $row2['observacion'] ?></div>                
            </div>


            <?php if($row2['estado']==1){ ?>
                <div class="finalizado" style="float:left; width:698px; padding: 0 0 0 0; margin: 0 0 0 20px; font-size:11px;">
                    <div class="INuser0" style="float:left; width:300px; margin: 2px 0 0 10px; padding: 0 0 0 20px;">
                        <span style="color:#777">Finalizo: </span><b>
                        <?php echo $row2['usuario_finaliza'] ?></b>
                    </div>
                    <div style="float:right; width:350; margin: 2px 2px 0 0; padding: 0 3px 0 0; text-align:right;">
                        <span style="color:#777">Fecha de Finalizacion: </span> <b>
                        <?php echo fecha_larga_hora_m($row2['fecha_finaliza']) ?></b>
                    </div>
                    <div style="float:left; width:690px; margin: 2px 0 0 10px"><?php echo $row2['observacion_finaliza'] ?></div>
                </div>
            <?php } ?>

            <?php
                $consul3 = $mysql->query("SELECT * FROM crm_objetivos_actividades_acciones WHERE id_actividad = $row2[id] AND activo = 1 ",$link);
                while($row3 = $mysql->fetch_array($consul3)){


            ?>
                <!-- ACCIONES -->

                <div class="acciones Color<?php echo $row['estado'] ?>B" style="float:left; width:637; padding: 0 0 0 3px; margin: 4px 0 0 60px; font-size:11px;">
                    <div class="INuser<?php echo $row['estado'] ?>" style="float:left; width:300; margin: 2px 0 0 0; padding: 0 0 0 20px;">
                        <span style="color:#777">Usuario: </span> <b>
                        <b><?php echo $row3['usuario'] ?></b>
                    </div>
                    <div style="float:right; width:300; margin: 2px 2px 0 0; padding: 0 3px 0 0; text-align:right;">
                        <span style="color:#777">Fecha: </span> <b>
                        <b><?php echo fecha_larga_hora_m($row3['fecha']) ?></b>
                    </div>
                    <div style="float:left; width:680; margin: 2px 0 0 0"><?php echo $row3['accion'] ?></div>
                </div>

<?php
                }
            }

    }
?>
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
                    10,             // margin header
                    10,             // margin footer
                    $ORIENTACION    // L - landscape, P - portrait
                );
        $mpdf->SetAutoPageBreak(TRUE, 15);
        //$mpdf->SetTitle ( $documento );
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