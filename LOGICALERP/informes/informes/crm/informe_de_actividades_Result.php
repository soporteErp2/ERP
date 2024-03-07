<?php

    ini_set('max_execution_time', 10000);
	include('../../../../configuracion/conectar.php');
	include("../../../../configuracion/define_variables.php");
	if($IMPRIME_XLS=='true'){

	   header('Content-Type: text/html; charset=UTF-8');
       header('Content-type: application/vnd.ms-excel;');
       header("Content-Disposition: attachment; filename=Informe_tareas_pendientes.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
   	}
	ob_start();

	if($MyInformeFiltro_Clientes == undefined){
		   $MyInformeFiltro_Clientes = '';
	}

	if($MyInformeFiltro_Funcionarios == undefined){
		   $MyInformeFiltro_Funcionarios = '';
	}

	$MyInformeFiltroEmpresa  = $_SESSION['EMPRESA'];
	$MyInformeFiltroSucursal = $_SESSION['SUCURSAL'];

	$nombre_empresa = $mysql->result($mysql->query("SELECT * FROM empresas WHERE id = $MyInformeFiltroEmpresa",$link),0,"nombre");
	$WhereEmpresa = 'AND E.id_empresa = '.$MyInformeFiltroEmpresa;

	$nombre_sucursal = $mysql->result($mysql->query("SELECT * FROM empresas_sucursales WHERE id = $MyInformeFiltroSucursal",$link),0,"nombre");
	$WhereSucursal = 'AND E.id_sucursal = '.$MyInformeFiltroSucursal;

	$EstadoAct           = $MyInformeFiltro_2;
	$EstadoActividadName = array("PENDIENTES","FINALIZADAS");
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
		       		<tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Fecha Inicial</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaInicio)?></td>
		            </tr>
		            <tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Fecha Final</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo fecha_larga($MyInformeFiltroFechaFinal)?></td>
		            </tr>
		            <tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Vendedor</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreFuncionario; ?></td>
		            </tr>
		             <tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Cliente</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $nombreCliente; ?></td>
		            </tr>
		            <tr style="float:left;width:100%">
		                <td class="my_informe_Contenedor_Titulo_informe_label">Estado</td>
		                <td class="my_informe_Contenedor_Titulo_informe_detalle"><?php echo $EstadoActividad; ?></td>
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
	//Thead::::::::::::::::::::::::::::
	echo'<br>
	     <table  style="width:1010px;float:left;border-spacing:0px;font-size:12px; ">

		 </table>';


	//==========================================================================================================================
	//===================================================ZONA DE PRUEBAS=======================================================

	//OBTENGO LOS EMPLEADOS Y LOS GUARDO EN UN ARRAY
	$SQL1_1 = "SELECT
				P.id_asignado,
			 	P.asignado,
			    E.email_empresa
			 FROM
			 	crm_objetivos_actividades_personas AS P
			 INNER JOIN crm_objetivos_actividades AS A ON (P.id_actividad = A.id)
			 INNER JOIN empleados AS E ON (P.id_asignado = E.id)
			 WHERE
			 	A.activo = 1
			 AND P.id_asignado $whereFuncionario
			 AND A.estado $whereEstado
			 AND A.id_cliente $whereCliente
			 AND A.fecha_actividad BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
			 $WhereEmpresa
			 $WhereSucursal
             GROUP BY P.id_asignado";

    $consulta1_1 = mysql_query($SQL1_1,$link);

    $body = '';

    $whereIdUsuarios1 = '';

    //ARRAY CON LOS FUNCIONARIOS ADICIONALES
    while($row = mysql_fetch_array($consulta1_1)){

    	 $whereIdUsuarios1=($whereIdUsuarios1!='')? $whereIdUsuarios1.' OR P.id_asignado='.$row['id_asignado'] : 'P.id_asignado='.$row['id_asignado'] ;
    	 $arrayEmpleados[$row['id_asignado']]=array('asignado'      => $row['asignado'],
                                              	    'email_empresa' => $row['email_empresa'],
                                             	  );
   	}

   	//OBTENGO LOS DATOS DE LAS ACTIVIDADES POR EMPLEADO Y LAS GUARDO EN OTRO ARRAY

    $SQL1_2 = "SELECT
    			P.id_asignado,
			 	P.asignado,
			    E.email_empresa,
			    A.id,
			 	A.id_cliente,
			 	A.cliente,
			 	A.id_objetivo,
			 	A.objetivo,
			 	A.tema,
			 	A.estado,
			 	A.usuario,
			 	A.tipo_nombre,
			 	A.fechai,
			 	A.horai,
			 	A.fechaf,
			 	A.horaf,
			 	A.observacion
			 FROM
			 	crm_objetivos_actividades_personas AS P
			 INNER JOIN crm_objetivos_actividades AS A ON (P.id_actividad = A.id)
			 INNER JOIN empleados AS E ON (P.id_asignado = E.id)
			 WHERE
			 	 A.activo = 1
			 AND P.id_asignado $whereFuncionario
			 AND A.estado $whereEstado
			 AND A.fecha_actividad BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
			 AND A.id_cliente $whereCliente
			 $WhereEmpresa
			 $WhereSucursal
             AND ($whereIdUsuarios1)";

    $consulta1_2 = mysql_query($SQL1_2,$link);

    while($row = mysql_fetch_array($consulta1_2)){
    	 $arrayActividadesEmpleados[$row['id_asignado']][$row['id']]  =  array('id_cliente'  => $row['id_cliente'],
    	 																	   'cliente'     => $row['cliente'],
    	 																	   'id_objetivo' => $row['id_objetivo'],
                                                                  			   'objetivo'    => $row['objetivo'],
                                                                  			   'tema'        => $row['tema'],
                                                                  			   'estado'      => $row['estado'],
                                                                  			   'usuario'     => $row['usuario'],
                                                                  			   'fechai'      => $row['fechai'],
                                                                  			   'horai'       => $row['horai'],
                                                                  			   'fechaf'      => $row['fechaf'],
                                                                  			   'horaf'       => $row['horaf'],
                                                                  			   'tipo_nombre' => $row['tipo_nombre'],
                                                                  			   'observacion' => $row['observacion'],
                                                                  			   'asignado'    => $row['asignado'],
                                                                  			  );

    }

    //manda_correo($consulta1_1);

    //CORREO A TODOS LOS FUNCIONARIOS ASIGNADOS EN LAS ACTIVIDADES
	$SQL2_1 = "SELECT
				A.id_asignado,
		 		A.asignado,
		 		E.email_empresa
			 FROM
			  	crm_objetivos_actividades AS A
			 INNER JOIN empleados AS E ON (A.id_asignado = E.id)
			 WHERE
			 	A.activo = 1
			 AND A.id_asignado $whereFuncionario
			 AND A.estado $whereEstado
			 AND A.fecha_actividad BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
			 AND A.id_cliente $whereCliente
			 $WhereEmpresa
			 $WhereSucursal
             GROUP BY A.id_asignado";

    $consulta2_1 = mysql_query($SQL2_1,$link);

    $whereIdUsuarios2 = '';

    //AL ARRAY YA EXISTENTE LE AGREGO LOS FUNCIONARIOS ASIGNADOS
    while($row = mysql_fetch_array($consulta2_1)){

    	 $whereIdUsuarios2=($whereIdUsuarios2!='')? $whereIdUsuarios2.' OR A.id_asignado='.$row['id_asignado'] : 'A.id_asignado='.$row['id_asignado'] ;
    	 $arrayEmpleados[$row['id_asignado']] = array('asignado'      => $row['asignado'],
                                              	      'email_empresa' => $row['email_empresa'],
                                             	    );
   	}

   	$SQL2_2 = "SELECT
				A.id_asignado,
		 		A.asignado,
		 		E.email_empresa,
			    A.id,
			 	A.id_cliente,
			 	A.cliente,
			 	A.id_objetivo,
			 	A.objetivo,
			 	A.tema,
			 	A.estado,
			 	A.usuario,
			 	A.tipo_nombre,
			 	A.fechai,
			 	A.horai,
			 	A.fechaf,
			 	A.horaf,
			 	A.observacion
			 FROM
			  	crm_objetivos_actividades AS A
			 INNER JOIN empleados AS E ON (A.id_asignado = E.id)
			 WHERE
			 	 A.activo = 1
			 AND A.id_asignado $whereFuncionario
			 AND A.estado $whereEstado
			 AND A.fecha_actividad BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
			 AND A.id_cliente $whereCliente
			 $WhereEmpresa
			 $WhereSucursal
             AND ($whereIdUsuarios2)";

    $consulta2_2 = mysql_query($SQL2_2,$link);

    //AL ARRAY YA CREADO LE AGREGO LAS ACTIVIDADES EN LAS QUE SON ASIGNADOS
    while($row = mysql_fetch_array($consulta2_2)){
    	 $arrayActividadesEmpleados[$row['id_asignado']][$row['id']]  =  array('id_cliente'  => $row['id_cliente'],
    	 																	   'cliente'     => $row['cliente'],
    	 																	   'id_objetivo' => $row['id_objetivo'],
                                                                  			   'objetivo'    => $row['objetivo'],
                                                                  			   'tema'        => $row['tema'],
                                                                  			   'estado'      => $row['estado'],
                                                                  			   'usuario'     => $row['usuario'],
                                                                  			   'fechai'      => $row['fechai'],
                                                                  			   'horai'       => $row['horai'],
                                                                  			   'fechaf'      => $row['fechaf'],
                                                                  			   'horaf'       => $row['horaf'],
                                                                  			   'tipo_nombre' => $row['tipo_nombre'],
                                                                  			   'observacion' => $row['observacion'],
                                                                  			   'asignado'    => $row['asignado'],
                                                                  			  );

    }

    // foreach ($arrayEmpleados as $id_usuario => $arrayResul) {


    // }

	//==========================================================================================================================
	//maketbody:::::::::::::::::::::::::::::::
	 foreach ($arrayEmpleados as $id_usuario => $arrayResul) {

	 	//$bodytable .= $arrayResul['asignado'].'<br>';

	 	//$i=0;

	 	foreach ($arrayActividadesEmpleados[$id_usuario] as $id_actividad => $row) {



	 		//echo $i++;
	 		if($usuario != $id_usuario){

	 			$bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border-top:1px solid #999;">
								<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
									<td colspan="5">&nbsp;</td>
								</tr>
							 </table>';

	 			$bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border:1px solid #999;border-bottom:none;background:#FF9999">
								<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
									<td colspan="5" style="padding-left:5px">Asignado: '.$arrayResul['asignado'].'</td>
								</tr>
							 </table>';
				$usuario = $id_usuario;
	 		}

			if($id_cliente != $row['id_cliente'] && $row['id_cliente'] > 0){
        	    $id_cliente = $row['id_cliente'];

        	    $bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border-top:1px solid #999;">
								<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
									<td colspan="5">&nbsp;</td>
								</tr>
							 </table>';

        	    $bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;background: #b2b2b2;border:1px solid #999;border-bottom:none; ">
								<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
									<td colspan="5" style="padding-left:25px">Cliente: '.$row['cliente'].'</td>
								</tr>
							 </table>';

			    if($row['id_objetivo'] < 1 || $row['id_objetivo'] == ''){
			    	$bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border:1px solid #999;border-top:none;border-bottom:none;">
        						<tr style="font-size:11px;">
									<td style =" width:250px; padding-bottom:5px;float:left;font-weight:bold;padding-left:75px">ACTIVIDAD</td>
									<td style =" width:178px; padding-bottom:5px;float:left;font-weight:bold">TIPO</td>
									<td style =" width:128px; padding-bottom:5px;float:left;font-weight:bold">FECHA INICIO</td>
									<td style =" width:128px; padding-bottom:5px;float:left;font-weight:bold">FECHA FINAL</td>
									<td style =" width:130px; padding-bottom:5px;float:left;font-weight:bold"></td>
									<td style =" width:121px; padding-bottom:5px;float:left;font-weight:bold"></td>
								</tr>
							 </table>';
			    }

        	}

        	if($id_proyecto != $row['id_objetivo'] && $row['id_objetivo'] > 0){
        	    $id_proyecto = $row['id_objetivo'];

        	    $bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border:1px solid #999;border-top:none;border-bottom:none;">
								<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px;background:#EEE;">
									<td colspan="6" style="padding-left:50px">Proyecto: '.$row['objetivo'].'</td>
								</tr>
								<tr style="font-size:11px;">
									<td style =" width:250px; padding-bottom:5px;float:left;font-weight:bold;padding-left:75px">ACTIVIDAD</td>
									<td style =" width:178px; padding-bottom:5px;float:left;font-weight:bold">TIPO</td>
									<td style =" width:128px; padding-bottom:5px;float:left;font-weight:bold">FECHA INICIO</td>
									<td style =" width:128px; padding-bottom:5px;float:left;font-weight:bold">FECHA FINAL</td>
									<td style =" width:130px; padding-bottom:5px;float:left;font-weight:bold">ESTADO</td>
									<td style =" width:121px; padding-bottom:5px;float:left;font-weight:bold"></td>
								</tr>
							 </table>';


        	}
        	if($id_proyecto1 != $row['id_objetivo'] && ($row['id_objetivo'] < 1 || $row['id_objetivo'] == '')){

        		$bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border:1px solid #999;border-top:none;border-bottom:none;">
        						<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px;background:#EEE;">
									<td colspan="6" style="padding-left:50px">Actividades Independientes</td>
								</tr>
								<tr style="font-size:11px;">
									<td style =" width:250px; padding-bottom:5px;float:left;font-weight:bold;padding-left:75px">ACTIVIDAD</td>
									<td style =" width:178px; padding-bottom:5px;float:left;font-weight:bold">TIPO</td>
									<td style =" width:128px; padding-bottom:5px;float:left;font-weight:bold">FECHA INICIO</td>
									<td style =" width:128px; padding-bottom:5px;float:left;font-weight:bold">FECHA FINAL</td>
									<td style =" width:130px; padding-bottom:5px;float:left;font-weight:bold">ESTADO</td>
									<td style =" width:121px; padding-bottom:5px;float:left;font-weight:bold"></td>
								</tr>
							 </table>';

        		$id_proyecto1 = $row['id_objetivo'];
        	}


        	if($row['estado'] == 0){
        		$estado = 'PENDIENTE';
        	}
        	else if($row['estado'] == 1){
        		$estado = 'FINALIZADA';
        	}

			$bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border:1px solid #999;border-bottom:none;border-top:none">
							<tr style="font-size:11px; overflow: hidden; margin-bottom:0px; font-weight:normal; border-spacing:0px">
								<td style =" width:250px;padding-left:75px">'.$row['tema'].'</td>
								<td style =" width:178px; ">'.$row['tipo_nombre'].'</td>
								<td style =" width:128px; ">'.$row['fechai'].' - '.$row['horai'].'</td>
								<td style =" width:128px; ">'.$row['fechaf'].' - '.$row['horaf'].'</td>
								<td style =" width:130px; ">'.$estado.'</td>
								<td style =" width:121px; ">&nbsp;</td>

							</tr>
						 </table>';
		}

	}

	$bodytable.='<table  style="width:1010px; float:left;border-spacing:0px;font-size:12px;border-top:1px solid #999;">
					<tr style="font-size:12px; overflow: hidden; margin-bottom:0px; font-weight:bold; border-spacing:0px">
						<td colspan="5">&nbsp;</td>
					</tr>
				 </table>';
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