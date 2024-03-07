<?php

    ini_set('max_execution_time', 10000);
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	if($IMPRIME_XLS=='true'){

	   header('Content-Type: text/html; charset=UTF-8');
       header('Content-type: application/vnd.ms-excel;');
       header("Content-Disposition: attachment; filename=Informe_prospectos.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
   	}

   	if($MyInformeFiltro_Funcionarios == undefined){
           $MyInformeFiltro_Funcionarios = '';
    }

   	$id_empresa = $_SESSION['EMPRESA'];

	ob_start();

	$array = explode(")",$MyInformeFiltro_Funcionarios);

    $funcionario = str_replace("(","",$array[0]);

    if($funcionario == ''){
        $nombreFuncionario = 'TODOS';
        $whereFuncionario  = "";
    }else{
        $nombreFuncionario = $mysql->result($mysql->query("SELECT nombre FROM empleados WHERE id = $funcionario"),0,"nombre");
        $whereFuncionario  = 'AND TA.id_asignado = '.$funcionario;
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
	     <table  style="width:1196px;float:left;border-spacing:0px;font-size:12px; ">
			<tr>
				<td style =" width:187px; padding-bottom:5px;float:left;font-weight:bold;">NOMBRE COMERCIAL</td>
				<td style =" width:148px; padding-bottom:5px;float:left;font-weight:bold">TELEFONO</td>
				<td style =" width:208px; padding-bottom:5px;float:left;font-weight:bold">EMAIL</td>
				<td style =" width:168px; padding-bottom:5px;float:left;font-weight:bold">CIUDAD</td>
				<td style =" width:168px; padding-bottom:5px;float:left;font-weight:bold">DEPARTAMENTO</td>
				<td style =" width:115px; padding-bottom:5px;float:left;font-weight:bold">PAIS</td>
				<td style =" width:202px; padding-bottom:5px;float:left;font-weight:bold">ASIGNADO A</td>
			</tr>
		 </table>';
	$SQL = "SELECT
					TA.asignado,
					T.ciudad,
					T.email,
					T.nombre_comercial,
					T.pais,
					T.telefono1,
					T.departamento
				FROM
					terceros T
				LEFT JOIN terceros_asignados TA ON T.id = TA.id_tercero
				WHERE
					T.activo = 1
				AND T.id_empresa='$id_empresa'
				AND T.tercero = 0
				$whereVendedor
				$whereFuncionario 
				ORDER BY
					nombre ASC";
	$consul = $mysql->query($SQL,$link);
	//maketbody:::::::::::::::::::::::::::::::
	while($row=$mysql->fetch_array($consul)){

		$bodytable.='<table  style="width:1196px; float:left;border-spacing:0px;font-size:11px; ">
				<tr style="font-size:11px; overflow: hidden; margin-bottom:0px; font-weight:normal; border-spacing:0px">
					<td style =" width:187px; ">'.$row['nombre_comercial'].'</td>
					<td style =" width:148px; ">'.$row['telefono1'].'</td>
					<td style =" width:208px; ">'.$row['email'].'</td>
					<td style =" width:168px; ">'.$row['ciudad'].'</td>
					<td style =" width:168px; ">'.$row['departamento'].'</td>
					<td style =" width:115px; ">' .$row['pais'].'</td>
					<td style =" width:202px; ">'.$row['asignado'].'</td>
				</tr>
			 </table>';
	}
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

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
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