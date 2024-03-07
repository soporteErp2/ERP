<?php
    ini_set('max_execution_time', 10000);
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=Informe_clientes.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
   	}

   	$id_empresa = $_SESSION['EMPRESA'];

	ob_start();

	/*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/

	if($MyInformeFiltro_Funcionarios == undefined){
           $MyInformeFiltro_Funcionarios = '';
    }   

	$array = explode(")",$MyInformeFiltro_Funcionarios);

    $funcionario = str_replace("(","",$array[0]);

    if($funcionario == ''){
        $nombreFuncionario = 'TODOS';
        $whereFuncionario  = "";
    }else{
        $nombreFuncionario = $mysql->result($mysql->query("SELECT nombre FROM empleados WHERE id = $funcionario"),0,"nombre");
        $whereFuncionario  = 'AND TA.id_asignado = '.$funcionario;
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
	.my_informe_Contenedor_Titulo_informe1{
		float				:	left;
		width				:	100%;
		margin				:	0 0 10px 0;
		font-size			:	11px;
		font-family			:	Verdana, Geneva, sans-serif
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
			     <table style="width:1010px; float:left;border-spacing:0px;font-size:12px;table-layout:fixed ">
					<tr>
						<td style=" width:90px; padding-bottom:5px;float:left;font-weight:bold">NIT</td>
						<td style=" width:300px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">RAZON SOCIAL</td>
						<td style=" width:300px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">NOMBRE COMERCIAL</td>
						<td style=" width:135px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">DIRECCION</td>
						<td style=" width:125px; padding-bottom:5px;float:left;padding-left:5px;font-weight:bold; margin-left:10px">TELEFONO</td>
					</tr>
				 </table>';

			$SQL = "SELECT
						TA.asignado,
						T.numero_identificacion,
						T.nombre,
						T.nombre_comercial,
						T.telefono1,
						T.direccion
					FROM
						terceros T
					LEFT JOIN terceros_asignados TA ON T.id = TA.id_tercero
					WHERE
						T.activo = 1
						AND T.tipo_cliente = 'Si'
						AND T.id_empresa='$id_empresa'
						AND T.tercero = 1
						$whereVendedor
						$whereFuncionario
					ORDER BY T.nombre ASC";//LOGICA DE LOS PEDIDOS ADICIONALES
					//AND tipo_evento = 0
			//echo $SQL;
			$consul = $mysql->query($SQL,$link);

			$acumulador = 0;



			while($row=$mysql->fetch_array($consul)){

				echo'<table style="width:1010px; float:left;border-spacing:0px;font-size:12px; ">
						<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:normal; border-spacing:0px">
							<td style=" width:90px; float:left;">'.$row['numero_identificacion'].'<span style="font-size:9px; font-weight:bold">'.$TipEve.'</span></td>
							<td style=" width:300px; float:left; margin-left:10px;padding-left:5px">'.$row['nombre'].'</td>
							<td style=" width:300px; float:left; margin-left:10px;padding-left:5px">'.$row['nombre_comercial'].'</td>
							<td style=" width:135px; padding-bottom:5px;float:left;padding-left:5px; margin-left:10px">'.$row['direccion'].'</td>
						<td style=" width:125px; padding-bottom:5px;float:left;padding-left:5px; margin-left:10px">'.$row['telefono1'].'</td>
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