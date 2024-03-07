<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if ($_SESSION['EMPRESA']==''){ return; }
	else if ($filtro_empresa!=$_SESSION['EMPRESA']){ return; }

	 //verificamos si el acta parcial es total o es entre un periodo de tiemp

	if ($opc=="imprimirActaParcialBetween") {

	 	//si el acta es en un determinado tiempo se ejecuta este script	codigo,code_bar,unidad_medida,cantidad_unidades,nombre_equipo,vida_util,costos,id_impuesto -- (fecha_creacion_en_inventario BETWEEN '$fecha_ini' AND '$fecha_fin') AND
		 $SQL = "SELECT I.codigo,I.code_bar,I.unidad_medida,I.cantidad_unidades,I.nombre_equipo,I.vida_util,IT.costos,I.id_impuesto,I.precio_venta, IT.cantidad
					FROM items AS I
					INNER JOIN inventario_totales AS IT
						ON I.id=IT.id_item
					WHERE (I.fecha_creacion_en_inventario BETWEEN '$fecha_ini' AND '$fecha_fin')
						AND IT.id_sucursal='$filtro_sucursal'
						AND IT.id_ubicacion='$filtro_ubicacion'
						AND IT.inventariable = 'true'
						AND I.activo=1
						AND IT.id_empresa=".$_SESSION['EMPRESA']." ORDER BY codigo ASC";
	 	 $titulo=' ACTA DE INVENTARIO <br>Desde <b>'.$fecha_ini.'</b> Hasta <b>'.$fecha_fin.'</b>';
	}
	else{

	 	//si no es en determinado tiempo se ejecuta este, donde se muestran todo los articulos de todas las fechas, incluyendo si el usuario altera la url con los datos, se tomara este script
		$SQL = "SELECT I.codigo,I.code_bar,I.unidad_medida,I.cantidad_unidades,I.nombre_equipo,I.vida_util,IT.costos,I.id_impuesto,I.precio_venta, IT.cantidad
					FROM items AS I
					INNER JOIN inventario_totales AS IT
						ON I.id=IT.id_item
					WHERE IT.id_sucursal='$filtro_sucursal'
						AND IT.id_ubicacion='$filtro_ubicacion'
						AND IT.inventariable = 'true'
						AND I.activo=1
						AND IT.id_empresa=".$_SESSION['EMPRESA']."
						ORDER BY codigo ASC";
		$titulo=' ACTA DE INVENTARIO <br>'.date("d-m-Y").'';
	 }

	//consultar el nombre de la empresa
	$consulta_nombre=mysql_query("SELECT nombre FROM empresas WHERE id=".$_SESSION['EMPRESA'],$link);
	$nombre_empresa=mysql_fetch_array($consulta_nombre);

	//========================================================= CONSULTAMOS LOS ARTICULOS REGISTRADOS DE LA EMPRESA ===============================================================//



	$consul = mysql_query($SQL,$link);
	if (!$consul){die('no valido informe'.mysql_error());}
	$estilo='background-color: #DFDFDF;';


	while($row = mysql_fetch_array($consul)){
		if ($estilo!='') { $estilo=''; }
		else{ $estilo='background-color: #DFDFDF;'; }

		if ($row['code_bar']!=0 || $row['code_bar']!='') { $code_bar=$row['code_bar']; }
		else{ $code_bar="&nbsp;"; }

		$sqlIva   = "SELECT impuesto,valor FROM impuestos WHERE id=".$row["id_impuesto"];
		$queryIva = mysql_query($sqlIva,$link);
		$iva      = mysql_result($queryIva,0,'impuesto');

		$articulos.='<div style="'.$estilo.' text-align:right;">
						<div style="float:left; width:70px; padding-left:2px;">'.$row["codigo"].'</div>
						<div style="float:left; width:80px; padding-left:2px;">'.$code_bar.'</div>
						<div style="float:left; width:230px; padding-left:20px; padding-top:5px; text-align:left;" >'.$row["nombre_equipo"].'</div>
						<div style="float:left; width:100px; padding-left:2px; padding-top:5px;" >'.$row["costos"].'</div>
						<div style="float:left; width:100px; padding-left:2px;" >'.$row["precio_venta"].'</div>
						<div style="float:left; width:50px; padding-left:20px; padding-top:5px;" >'.$row["cantidad"].'</div>
					</div>';
	}

	$sqlLugar        = "SELECT sucursal,nombre FROM empresas_sucursales_bodegas WHERE id='$filtro_ubicacion' AND id_sucursal='$filtro_sucursal' AND id_empresa=".$_SESSION['EMPRESA'];
	$queryLugar      = mysql_query($sqlLugar,$link);
	$nombre_sucursal = mysql_result($queryLugar,0,'sucursal');
	$nombre_bodega   = mysql_result($queryLugar,0,'nombre');


	//============================================================= ARMAMOS EL DOCUMENTO ===================================================================//
		$documento = "Acta invententario ".date("d-m-Y");

		$header = '<div id="body_pdf" style="width:780px; font-style:normal; font-size:11px; height:10%;m" >
						<div style="float:left; width:90%; margin:0px 10px 20px 20px; text-align:center; font-weight:bold;">
							<div style="float:right; width:60px;"></div>
							'.$nombre_empresa["nombre"].'<br>'.$titulo.'<br>
						</div><br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;">

							<div style="float:left; width:90%; margin:0px 5px 0px 10px">
									<div style="float:left; width:21%;"><b>Usuario</b></div>
									<div style="float:left; width:60%;">'.$_SESSION['NOMBREFUNCIONARIO'].'</div>
							</div>
							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
									<div style="float:left; width:21%;"><b>Sucursal</b></div>
									<div style="float:left; width:60%;">'.$nombre_sucursal.'</div>
							</div>
							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
									<div style="float:left; width:21%;"><b>Bodega</b></div>
									<div style="float:left; width:60%;">'.$nombre_bodega.'</div>
							</div>

					</div>
						<div style="width:100%; font-style:normal; font-size:11px; margin:10px 5px 0px 10px; border-collapse:collapse; text-align:center; font-weight:bold;" >ARTICULOS REGISTRADOS A LA FECHA</div>
					</div>

						<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; background-color: #CDCDCD;">

							<div style="float:left; width:70px; padding-left:2px; text-align:center;" >Codigo<br>interno</div>
							<div style="float:left; width:80px; padding-left:2px; text-align:center;" >Codigo de<br>Barras</div>
							<div style="float:left; width:280px; padding-left:20px; padding-top:5px;" >Articulo</div>
							<div style="float:left; width:70px; padding-left:2px; padding-top:5px;" >Costo</div>
							<div style="float:left; width:90px; padding-left:2px; text-align:center" >Precio<br>Venta</div>
							<div style="float:left; width:50px; padding-left:20px; padding-top:5px;" >Stock</div>
						</div>';

			$contenido='<div style="width:100%; font-style:normal; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
							'.$articulos.'<br/>
						</div>';



	$texto= $contenido;


	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){
		list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
	}else{
		$MS = 56;$MD = 10;$MI = 15;$ML = 10;
	}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}

	if($IMPRIME_PDF){
		include("../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLHeader($header);
		$mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){
			$mpdf->Output($documento.".pdf",'D');   	///OUTPUT A ARCHIVO
		}else{
			$mpdf->Output($documento.".pdf",'I');		///OUTPUT A VISTA
		}
		exit;
	}else{
		echo $texto;
	}




?>