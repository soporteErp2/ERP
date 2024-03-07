<?php
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	ob_start();

    /*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/

	$nombre_empresa	 = mysql_result(mysql_query("SELECT * FROM empresas WHERE id = $MyInformeFiltroEmpresa",$link),0,"nombre");
	$nombre_sucursal = mysql_result(mysql_query("SELECT * FROM empresas_sucursales WHERE id = $MyInformeFiltroSucursal",$link),0,"nombre");
	$nombre_bodega   = mysql_result(mysql_query("SELECT * FROM empresas_sucursales_bodegas WHERE id = $MyInformeFiltroBodega",$link),0,"nombre");

    //CABECERA DEL DOCUMENTO

    $calidad = '&nbsp;&nbsp;Codigo : CO-PR-02-F05<br/>&nbsp;&nbsp;Version : 1<br/>&nbsp;&nbsp;Vigencia : 2015-05-26';

    $titulo  = 'FORMATO DE VISITAS <BR>COMERCIALES/OPERATIVAS';

    $contenido = '';


    //CONSULTO EL NOMBRE DEL FUNCIONARIO DE LA SESION
    //$id_usuario = 4;

    $id_usuario = $_SESSION['IDUSUARIO'];

    $sqlUsuario   = "SELECT nombre FROM empleados WHERE id= '$id_usuario'";
    $queryUsuario = mysql_query($sqlUsuario,$link);
    $nombre       = mysql_result($queryUsuario, 0, 'nombre');



    //CONSULTA DE LAS ACTIVIDADES

    $sqlVisitas = "SELECT A.id_cliente,
                          A.tipo_nombre,
                          A.horai
                   FROM crm_objetivos_actividades AS A
                   LEFT JOIN crm_objetivos_actividades_personas AS P ON (A.id = P.id_actividad),
                        crm_configuracion_actividades AS C
                   WHERE (A.id_asignado = '$id_usuario' OR P.id_asignado = '$id_usuario')
                   AND A.tipo = C.id
                   AND A.activo = 1
                   AND C.activo = 1
                   AND C.genera_visita = 'true'
                   AND fechai = '$MyInformeFiltroFechaInicio'";

    $queryVisitas = mysql_query($sqlVisitas,$link);
    //TRAEMOS EL NOMBRE DEL TERCERO  Y SU CONTACTO

    //echo mysql_num_rows($queryVisitas);

    if($IMPRIME_PDF =='true'){$ImgPath = '../../';}else{$ImgPath = '';}

     $head = '<div style="display:none;float:left; width:100%; font-size:11px; border:1px solid #000; margin:0 0 8px 0;">
                    <div style="width:30%; height:48px; float:left; border-right:1px solid #000; text-align:center;padding-top:2px"><img src = "" alt="logo corporativo" style="width:70%; height:30px;"></div>
             <div style="width:40%; height:48px; float:left; border-right:1px solid #000; font-size:14px; font-weight:bold;">

                    <div style="width:100%;height:31px;text-align:center;padding-top:8px;font-size:13px;font-weight:bold;vertical-align:middle">'.$titulo.'</div>
             </div>
             <div style="width:28%;padding-left:5px; float:left;font-weight:bold">'.$calidad.'</div>
             </div>';

    while($row = mysql_fetch_array($queryVisitas)){

          $sqlCliente   = "SELECT nombre,nombre_comercial,direccion FROM terceros WHERE id= '$row[id_cliente]' AND activo = 1 LIMIT 0,1";
          $queryCliente = mysql_query($sqlCliente,$link);
          
          $nombreC   = mysql_result($queryCliente,0,'nombre_comercial');
          $direccion = mysql_result($queryCliente,0,'direccion');          
          
          $contenido .= '<table style="float:left;width:100%;padding-top:17px;font-size: 11px;">
                            <tr>
                               <td style="width:7%;vertical-align: top;font-weight: bold;">Cliente:</td>
                               <td style="width:46%;padding-right:5px;">'.$nombreC.'</td>
                               <td style="width:14%;font-weight: bold;">Tipo de Visita:</td>
                               <td style="width:30%">'.$row['tipo_nombre'].'</td>
                            </tr>
                           <tr>
                               <td style="width:7%;vertical-align: top;font-weight: bold;">Direcci&oacute;n:</td>
                               <td style="width:46%;padding-right:5px;">'.$direccion.'</td>
                               <td style="width:14%;font-weight: bold;">Hora de entrada:</td>
                               <td style="width:30%">'.$row['horai'].'</td>
                           </tr>
                           <tr>
                               <td style="width:7%;vertical-align: top;font-weight: bold;">Contacto:</td>
                               <td style="width:46%;height:18px;padding-right:5px;">'.$contacto.'</td>
                               <td style="width:14%;font-weight: bold;">Hora de salida:</td>
                               <td style="width:30%">&nbsp;</td>
                           </tr>
                        </table>
                        <table style="float:left;width:100%;padding-top:17px;">
                            <tr>
                               <td class="my_informe_Contenedor_Titulo_informe_label" style="padding-left:5px;width:47%;height:70px;font-weight:normal;border:1px solid;vertical-align: top;">Temas tratados:</td>
                               <td class="my_informe_Contenedor_Titulo_informe_label" style="width:6%;height:70px;font-weight:normal;">&nbsp;</td>
                               <td class="my_informe_Contenedor_Titulo_informe_label" style="padding-left:5px;width:45%;height:70px;font-weight:normal;border:1px solid;vertical-align: top;">Nombre legible del funcionario y sello de la empresa</td>
                            </tr>
                       </table>';


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
        font-size           :   11px;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
		float				:	left;
		width				:	210px;
		padding				:	0 0 0 5px;
	    /*white-space             : nowrap;*/
        overflow                : hidden;
        font-size           :   11px;
       /* text-overflow           : ellipsis;*/
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
		float				:	left;
		width				:	370px;
		font-size			:	16px;
	}

</style>

<!-- --------------------------   DESARROLLO DEL INFORME  ------------------------------------ -->
<!-- ----------------------------------------------------------------------------------------- -->





    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <!-- <div style="float:left;width:100%">
            <div style="width:100%; border-bottom:1px solid #CCC; font-weight:bold; font-size:18px; text-align:left;"><?php echo $nombre_informe ?></div>
        </div> -->
        <div style="float:left; width:740px">

            <div style="float:left;width:100%">
                <div class="my_informe_Contenedor_Titulo_informe_label" style="padding-left:5px;width:14%">Nombre Ejecutivo:</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle" style="padding-left:5px;width:84%"><?php echo $nombre; ?></div>
            </div>
            <div style="float:left;width:100%;border-bottom:1px solid #CCC;padding-bottom:12px">
                <div class="my_informe_Contenedor_Titulo_informe_label" style="padding-left:5px;width:14%">Fecha:</div>
                <div class="my_informe_Contenedor_Titulo_informe_detalle" style="padding-left:5px;width:84%"><?php echo fecha_larga($MyInformeFiltroFechaInicio); ?></div>
            </div>
            <div style="width:100%"></div>
            <?php echo $contenido; ?>
            <div style="width:100%;padding-top:15px">
            </div>
        </div>
    </div>


<?php

     $footer = '<div style="width:100%;padding-top:15px;text-align:right;font-size:12px">Paginas ({PAGENO} de {nb})</div>';
    /*--------------------------------------------------CUERPO DEL INFORME---------------------------------------------------*/
    //$consul = mysql_query("SELECT * FROM crm_objetivos",$link);
    // while($row = mysql_fetch_array($consul)){
?>


<?php
    // }
?>

<!-- ---------------------------------  FIN DEL INFORME  ------------------------------------- -->
<!-- ----------------------------------------------------------------------------------------- -->
<?php
    $texto = $revision_actual =  ob_get_contents(); ob_end_clean();

    if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
    if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
    if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
    if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}
    if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=30;$MD=10;$MI=10;$ML=10;}
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
        $mpdf->SetHTMLHeader($head);
        $mpdf->SetHtmlFooter($footer);
        $mpdf->WriteHTML(utf8_encode($texto));
        if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{ $mpdf->Output($documento.".pdf",'I');}
        exit;
    }else{
        echo $head.$texto;
    }
?>