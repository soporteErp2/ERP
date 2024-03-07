<?php
    ini_set('max_execution_time', 10000);
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=Informe_terceros_contactos.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
   	}

	ob_start();

	/*--------------------------------------------------CABECERA INFORME---------------------------------------------------*/

	$id_empresa = $_SESSION['EMPRESA'];

	//FECHA DE CREACION DEL TERCERO

	$whereFechas      ="";
	$cabecera_informe = '';
	if (isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal!='') {
	    $whereFechas   =" AND T.fecha_creacion BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";    
	    $cabecera_informe  = '<tr style="float:left;width:100%">
	                              <td class="my_informe_Contenedor_Titulo_informe_label">Fecha Inicial</td>
	                              <td class="my_informe_Contenedor_Titulo_informe_detalle">'.fecha_larga($MyInformeFiltroFechaInicio).'</td>
	                           </tr>
	                           <tr style="float:left;width:100%">
	                              <td class="my_informe_Contenedor_Titulo_informe_label">Fecha Final</td>
	                              <td class="my_informe_Contenedor_Titulo_informe_detalle">'.fecha_larga($MyInformeFiltroFechaFinal).'</td>
	                           </tr>';
	}

	//FILTROS DE UBICACION
	$wherePais= '';
	
	if ($id_pais != '' && $id_pais != 'todos') {
	    $wherePais   = "AND T.id_pais = ".$id_pais;   
	    $nombre_pais = $mysql->result($mysql->query("SELECT * FROM ubicacion_pais WHERE id = $id_pais",$link),0,"pais");    
	}
	else{
	    $nombre_pais = 'Todos';
	}
	
	
	$whereDepartamento= '';
	
	if ($id_departamento != ''  && $id_departamento != 'todos') {
	    $whereDepartamento   = "AND T.id_departamento = ".$id_departamento;   
	    $nombre_departamento = $mysql->result($mysql->query("SELECT * FROM ubicacion_departamento WHERE id = $id_departamento",$link),0,"departamento");  
	}
	else{
	    $nombre_departamento = 'Todos';    
	}
	
	$whereCiudad= '';
	
	if ($id_ciudad != '' && $id_ciudad != 'todos') {
	    $whereCiudad   = "AND T.id_ciudad = ".$id_ciudad;  
	    $nombre_ciudad = $mysql->result($mysql->query("SELECT * FROM ubicacion_ciudad WHERE id = $id_ciudad",$link),0,"ciudad");  
	}
	else{
	    $nombre_ciudad = 'Todos'; 
	}
	
	$andTipoTercero = '';
	
	if($tipo_tercero == ''){
	    $tipo_tercero = 'todos';
	}
	
	//TIPO DE TERCERO
	if($tipo_tercero == 'todos'){
	    $andTipoTercero = "";
	}else if($tipo_tercero == 'clientes'){
	    $andTipoTercero = "AND T.tipo_cliente = 'Si'";  
	}else if($tipo_tercero == 'proveedores'){
	    $andTipoTercero = "AND T.tipo_proveedor = 'Si'";  
	}

	$andContactos   = 'GROUP BY TC.id';
	//SI TIENE O NO CONTACTOS
	if($con_contactos == 'true'){
	    $join_contactos = 'INNER';	   
	    $cabecera       = '<tr style="float:left;width:100%">
	                           <td class="my_informe_Contenedor_Titulo_informe_label">Contactos</td>
	                           <td class="my_informe_Contenedor_Titulo_informe_detalle">Si</td>
	                       </tr>';        
	}
	if($sin_contactos == 'true'){
	    $join_contactos = 'LEFT';   
	    $andContactos   = 'AND TC.id is null';
	    $cabecera       = '<tr style="float:left;width:100%">
	                           <td class="my_informe_Contenedor_Titulo_informe_label">Contactos</td>
	                           <td class="my_informe_Contenedor_Titulo_informe_detalle">No</td>
	                        </tr>';
        $nombre_informe = "INFORME DE TERCEROS SIN CONTACTOS";
	}
	if($con_contactos == 'true' && $sin_contactos == 'true'){
	    $join_contactos = 'LEFT';
		$andContactos   = 'GROUP BY TC.id';	    
	    $cabecera       = '';
        $nombre_informe = "INFORME DE CONTACTOS POR TERCERO";

	}
	if ($idProveedores!='' ) {
	    if ($idProveedores!='todos') {
	        $idProveedoresQuery=explode(",",$idProveedores);
	         //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
	         foreach ($idProveedoresQuery as $indice => $valor) {
	             $whereidProveedores=($whereidProveedores=='')? ' T.id ='.$valor : $whereidProveedores.' OR T.id ='.$valor ;
	             $whereProveedores=($whereidProveedores!='')? "AND (".$whereidProveedores.")" : "" ;
	        }
	    }	    
	}

	if (!isset($idProveedores) && !isset($id_pais)){    
	    $script='arrayproveedoresTC.length        = 0;
				 proveedoresConfiguradosTC.length = 0;
				 localStorage.tipo_tercero        = "";
        		 checkboxConContactos             = "";
        		 checkboxSinContactos             = "";             
	             ';
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
		        	<tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Pais</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo ucfirst($nombre_pais); ?></td>
                    </tr><tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Departamento</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo ucfirst($nombre_departamento); ?></td>
                    </tr><tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Ciudad</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo ucfirst($nombre_ciudad); ?></td>
                    </tr>
                    <?php echo $cabecera_informe.$cabecera ?>
                     <tr style="float:left;width:100%">
                        <td class="my_informe_Contenedor_Titulo_informe_label">Tipo de Tercero</td>
                        <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo ucfirst($tipo_tercero); ?></td>
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

	$SQL = "SELECT					
				T.id AS id_tercero,
				TC.id,
				TCE.id,   
				T.tipo_identificacion AS nit,         
				T.numero_identificacion,
				T.nombre AS nombre_tercero,
				T.nombre_comercial,
				T.telefono1 AS telefono_tercero,
				T.celular1 AS celular_tercero,
				T.fecha_creacion,
				T.direccion AS direccion_tercero,
				TC.tipo_identificacion,
				TC.identificacion,
				TC.nombre,
				TC.cargo,
				TC.direccion,
				TC.telefono1,
				TC.telefono2,
				TC.celular1,
				TC.celular2,
				TCE.email				
			FROM
				terceros T
			$join_contactos JOIN terceros_contactos TC ON (T.id = TC.id_tercero AND TC.activo = 1)
			LEFT JOIN terceros_contactos_email TCE ON (TC.id = TCE.id_contacto AND TCE.activo = 1)			
			WHERE
				T.activo = 1
				AND T.id_empresa='$id_empresa'
				AND T.tercero_empleado = 'false'						
				AND T.tercero = 1				
				$whereFechas
				$andTipoTercero               
				$wherePais
                $whereDepartamento
                $whereCiudad
                $whereProveedores
				$andContactos							
			ORDER BY T.id ASC,TC.id ASC,TCE.id ASC";//LOGICA DE LOS PEDIDOS ADICIONALES
			//AND tipo_evento = 0
	//echo $SQL;
	$consul = $mysql->query($SQL,$link);

	$acumulador = 0;

	while($row=$mysql->fetch_array($consul)){

		if($sin_contactos != 'true'){

			if($id_tercero != $row['id_tercero'] && $row['id_tercero'] > 0){
	    		
	    		$id_tercero = $row['id_tercero'];
	
	    		$bodytable.='<table  style="width:1010px; border-spacing:0px;font-size:12px;border-top:1px solid #999;">
							 	<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
									<td colspan="6">&nbsp;</td>
								</tr>
						 	</table>';
	
    		   	$bodytable.='<table  style="width:1010px; border-spacing:0px;font-size:12px;background: #b2b2b2;border:1px solid #999;border-bottom:none;border-collapse:collapse; ">
								<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
									<td colspan="5" style="padding-left:25px;width:760px">'.$row['nit'].' '.$row['numero_identificacion'].' '.$row['nombre_tercero'].'</td>
									<td style="width:230px">Fecha Creacion: '.$row['fecha_creacion'].'</td>
								</tr>
							 </table>
							 ';	
				
				$bodytable.='<table style="width:1010px;border-collapse:collapse;font-size:10px;border:1px solid #999;border-top:none;border-bottom:none;">
								<tr style=" border-bottom:none;height:12px;font-size:10px;font-weight:bold">	          					  
	    	      				   <td style="width:190px;font-weight:bold;padding-left:5px;text-align:center">NOMBRE</td>
	    	      				   <td style="width:190px;font-weight:bold;padding-left:5px;text-align:center">CARGO</td>
	    	      				   <td style="width:150px;font-weight:bold;padding-left:5px;text-align:center">DIRECCION</td>
	    	      				   <td style="width:110px;font-weight:bold;padding-left:5px;text-align:center;">TELEFONO</td>
	    	      				   <td style="width:110px;font-weight:bold;padding-left:5px;text-align:center;">CELULAR</td>
	    	      				   <td style="width:210px;font-weight:bold;padding-left:5px;text-align:center;">EMAIL</td>
	    	      	 		    </tr>
	    	      	    	 </table>';
					   		 
	   		}  
	
	   		$bodytable.='<table style="width:1010px;border-collapse:collapse;font-size:10px;border:1px solid #999;border-top:none;border-bottom:none;">
							<tr style="overflow: hidden; margin-bottom:0px; border-spacing:0px">	          			   
	    	      			   <td style="width:190px;font-weight:bold;padding-left:5px">'.$row['nombre'].'</td>
	    	      			   <td style="width:190px;font-weight:bold;padding-left:5px">'.$row['cargo'].'</td>
	    	      			   <td style="width:150px;font-weight:bold;padding-left:5px;">'.$row['direccion'].'</td>
	    	      			   <td style="width:110px;font-weight:bold;padding-left:5px;">'.$row['telefono1'].'</td>
	    	      			   <td style="width:110px;font-weight:bold;padding-left:5px;">'.$row['celular1'].'</td>
	    	      			   <td style="text-align:right;width:210px;font-weight:bold;padding-right:5px;">'.$row['email'].'</td>
	    	      		 	</tr>
	    	      		 </table>';
  		}
  		else{

  			if($acumulador == 0){
  				$bodytable.='<table style="width:1010px;border-collapse:collapse;font-size:10px;">
								<tr style=" border-bottom:none;height:12px;font-size:10px;font-weight:bold">	          					  
	    	      				   <td style="width:120px;font-weight:bold;padding-left:5px;text-align:center">NIT</td>
	    	      				   <td style="width:210px;font-weight:bold;padding-left:5px;text-align:center">TERCERO</td>	    	      				   
	    	      				   <td style="width:90px;font-weight:bold;padding-left:5px;text-align:center">CREADO EL</td>
	    	      				   <td style="width:210px;font-weight:bold;padding-left:5px;text-align:center">USUARIO</td>
	    	      				   <td style="width:190px;font-weight:bold;padding-left:5px;text-align:center;">DIRECCION</td>
	    	      				   <td style="width:110px;font-weight:bold;padding-left:5px;text-align:center;">TELEFONO</td>	    	      				   
	    	      	 		    </tr>
	    	      	    	 </table>';
  		    }
  		    $bodytable.='<table style="width:1010px;border-collapse:collapse;font-size:10px;">
								<tr style="overflow: hidden; margin-bottom:0px; border-spacing:0px">	          			   
	    		      			   <td style="width:120px;font-weight:bold;padding-left:5px">'.$row['numero_identificacion'].'</td>
	    		      			   <td style="width:210px;font-weight:bold;padding-left:5px">'.$row['nombre_tercero'].'</td>
	    		      			   <td style="width:90px;font-weight:bold;padding-left:5px;text-align:center">'.$row['fecha_creacion'].'</td>
	    		      			   <td style="width:210px;font-weight:bold;padding-left:5px;">'.$row['usuario_creacion'].'</td>
	    		      			   <td style="width:190px;font-weight:bold;padding-left:5px;">'.$row['direccion_tercero'].'</td>
	    		      			   <td style="width:110px;font-weight:bold;padding-left:5px;">'.$row['telefono_tercero'].'</td>	    		      			   
	    		      		 	</tr>
	    		      		 </table>';
  		}	

  		$acumulador++;   
	}			

	echo $bodytable;

?>
</body>
<script>
    <?php echo $script; ?>
</script>

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