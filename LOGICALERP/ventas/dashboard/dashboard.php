<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	//-------------------------------------SELECTOR DE PERIODOS A MOSTRAR EN EL DASHBOARD-------------------------------------------------

	$hoy           = date("Y-m-d");
	$diaMes        = date("j");
	$diaSemana     = date("N");
	$numeroDiasMes = date("t");
	$year          = date("Y");

	if(!isset($periodo)){
		$periodo = 'dia';
	}
    //echo $periodo;

    echo '<script>var periodo = "'.$periodo.'"</script>';

	if($periodo == 'year'){
		$Titulo = 'Indicadores del A&ntilde;o';
		$fecha  = $year;
		$fechai = $fecha.'-01-01';
		$fechaf = $hoy;
		$rango  = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);

	}
	if($periodo == 'mes'){
		$Titulo = 'Indicadores del Mes';
		$resta  = $diaMes - 1;
		$fechai = date("Y-m-d",strtotime ( '-'.$resta.' day',strtotime($hoy)));
		$suma   = $numeroDiasMes - $diaMes;
		$fechaf = date("Y-m-d",strtotime ( '+'.$suma.' day',strtotime($hoy)));
		$rango  = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);
	}
	if($periodo == 'semana'){
		$Titulo = 'Indicadores de la Semana';
		$resta  = $diaSemana - 1;
		$fechai = date("Y-m-d",strtotime ( '-'.$resta.' day',strtotime($hoy)));
		$suma   = 7 - $diaSemana;
		$fechaf = date("Y-m-d",strtotime ( '+'.$suma.' day',strtotime($hoy)));
		$rango  = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);
	}
	if($periodo == 'dia'){
		$Titulo = 'Indicadores del D&iacute;a';
		$fechai = $hoy;
		$fechaf = $hoy;
		$rango  = fecha_larga($fechai);
	}

    //echo $fechai.' '.$fechaf;
	//echo '<script>alert("'.$periodo.'")</script>';

	//------------------------------------------------------------------------------------------------------------------------------------------


	if($filtro_bodega == 0){

		  $filtro_bodega = '';
	}
	else{
		  $filtro_bodega = " AND id_bodega = '".$filtro_bodega."'";
	}
	if($filtro_sucursal == 0){

		  $filtro_sucursal = '';
	}
	else{
		  $filtro_sucursal = "AND id_sucursal = '".$filtro_sucursal."'";
	}

    $whereRecibo_fecha = " AND fecha_inicial BETWEEN '$fechai' AND '$fechaf'";
    $where_fechas = " AND fecha_inicio BETWEEN '$fechai' AND '$fechaf'";

	//----------------------------------------------------CONSULTAS GLOBALES--------------------------------------------------------

	$whereGlobal = "estado = 1 AND activo = 1 AND id_empresa = $id_empresa ";

	$SQL1 = mysql_query("SELECT * FROM ventas_cotizaciones WHERE ".$whereGlobal.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1 = mysql_num_rows($SQL1);


	$SQL2 = mysql_query("SELECT * FROM ventas_pedidos  WHERE ".$whereGlobal.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND2 = mysql_num_rows($SQL2);

	$SQL3 = mysql_query("SELECT * FROM ventas_remisiones  WHERE ".$whereGlobal.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND3 = mysql_num_rows($SQL3);


	$SQL4 = mysql_query("SELECT * FROM ventas_facturas  WHERE ".$whereGlobal.$filtro_bodega." ".$filtro_sucursal." AND id_saldo_inicial = 0".$where_fechas,$link);
	$IND4 = mysql_num_rows($SQL4);


	$SQL5 = mysql_query("SELECT * FROM recibo_caja  WHERE ".$whereGlobal.$filtro_sucursal.$whereRecibo_fecha,$link);
	//echo "SELECT * FROM recibo_caja  WHERE ".$whereGlobal.$filtro_bodega." ".$filtro_sucursal;
	$IND5 = mysql_num_rows($SQL5);

	//--------------------------------------------------CONSULTAS DE LAS CANCELADAS-----------------------------------------------


    $whereCanceladas = " estado = 3 AND consecutivo > 0 AND activo = 1 AND id_empresa = $id_empresa ";


	$SQL1_1 = mysql_query("SELECT * FROM ventas_cotizaciones WHERE ".$whereCanceladas.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	//echo "\nSELECT * FROM ventas_cotizaciones WHERE ".$whereCanceladas.$filtro_bodega." ".$filtro_sucursal;
	$IND1_1 = mysql_num_rows($SQL1_1);


	$SQL2_1 = mysql_query("SELECT * FROM ventas_pedidos  WHERE ".$whereCanceladas.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND2_1 = mysql_num_rows($SQL2_1);


	$SQL3_1 = mysql_query("SELECT * FROM ventas_remisiones  WHERE ".$whereCanceladas.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND3_1 = mysql_num_rows($SQL3_1);


	$SQL4_1 = mysql_query("SELECT * FROM ventas_facturas  WHERE activo = 1".$filtro_bodega." ".$filtro_sucursal."AND estado = 3 AND numero_factura > 0 AND id_empresa = $id_empresa AND id_saldo_inicial = 0".$where_fechas,$link);
	//echo "\nSELECT * FROM ventas_facturas  WHERE activo = 1".$filtro_bodega." ".$filtro_sucursal."AND estado = 3 AND numero_factura > 0";
	$IND4_1 = mysql_num_rows($SQL4_1);


	$SQL5_1 = mysql_query("SELECT * FROM recibo_caja  WHERE ".$whereCanceladas." ".$filtro_sucursal.$whereRecibo_fecha,$link);
	$IND5_1 = mysql_num_rows($SQL5_1);

//--------------------------------------------------CONSULTAS DE LAS EDITADAS-----------------------------------------------


    $whereEditadas = " estado = 0 AND consecutivo > 0 AND activo = 1 AND id_empresa = $id_empresa ";


	$SQL1_2 = mysql_query("SELECT * FROM ventas_cotizaciones WHERE".$whereEditadas.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1_2 = mysql_num_rows($SQL1_2);


	$SQL2_2 = mysql_query("SELECT * FROM ventas_pedidos  WHERE".$whereEditadas.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND2_2 = mysql_num_rows($SQL2_2);


	$SQL3_2 = mysql_query("SELECT * FROM ventas_remisiones  WHERE".$whereEditadas.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND3_2 = mysql_num_rows($SQL3_2);


	$SQL4_2 = mysql_query("SELECT * FROM ventas_facturas  WHERE activo = 1 ".$filtro_bodega." ".$filtro_sucursal." AND estado = 0 AND id_empresa = $id_empresa AND numero_factura > 0 AND id_saldo_inicial = 0".$where_fechas,$link);
	$IND4_2 = mysql_num_rows($SQL4_2);


	$SQL5_2 = mysql_query("SELECT * FROM recibo_caja  WHERE".$whereEditadas." ".$filtro_sucursal.$whereRecibo_fecha,$link);
	$IND5_2 = mysql_num_rows($SQL5_2);

	$IMG3 = 'ok';

	// COSULTAR LAS FACTURAS PENDIENTES DE ENVIO
	$sql="SELECT * FROM ventas_facturas_configuracion WHERE activo=1 AND id_empresa=$id_empresa AND tipo='FE' AND consecutivo_factura<numero_final_resolucion ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$whereIdResolucion .= ($whereIdResolucion=='')? "id_configuracion_resolucion=$row[id]" : " OR id_configuracion_resolucion=$row[id] " ;
	}

	$sql="SELECT COUNT(id) AS cont FROM ventas_facturas
			WHERE activo=1
			$filtro_sucursal
			$filtro_bodega
			AND id_empresa=$id_empresa
			AND estado=1
			AND ($whereIdResolucion)
			AND (ISNULL(response_FE) OR response_FE='')
			";
	$query=$mysql->query($sql,$mysql->link);
	$facturasPendientesDian = $mysql->result($query,0,'cont');

	// CONSULTAR LAS FACTURAS QUE SE ENVIARON A FACSE PERO QUE NO PASARON LA VALIDACION
	// include_once("../../web_service/nuSoap/nusoap.php");
	// $objSoap = new nusoap_client("https://test.facse.net/conexion/comprobante.asmx?WSDL",true);
	// // $objSoap = new nusoap_client("https://test.facse.net/conexion/comprobante.asmx?wsdl",true);
	// $errorWs = $objSoap->getError();



	// if ($errorWs) { echo "<h2>Constructor error</h2><pre>".$errorWs."</pre>"; exit; }
	// $responseWs = $objSoap->call('ListaComprobantesPendientes', array('fechaInicial' => "2018-01-01","fechaFinal" => "2018-12-31", "emisor"=> "$_SESSION[NITEMPRESA]" ));
	// // $responseWs = $objSoap->call('ListaComprobantesPendientes', '<ListaComprobantesPendientes xmlns="http://tempuri.org/">
	// // 															      <fechaInicial>2018-01-01</fechaInicial>
	// // 															      <fechaFinal>2018-12-31</fechaFinal>
	// // 															      <emisor>'.$_SESSION['NITEMPRESA'].'</emisor>
	// // 															    </ListaComprobantesPendientes>' );

	// if ($objSoap->fault) {
	// 	echo "<h2>Fault</h2><pre>";
	// 	print_r($responseWs);
	// 	echo "</pre>";
	// }
	// else {
	// 	$errorWs = $objSoap->getError();
	// 	if ($errorWs) { echo "<h2>Error</h2><pre>".$errorWs."</pre>"; }
	// 	else {
	// 		// echo "<br>";
	// 		// $arrayResponse = json_decode($responseWs["ConsultarComprobanteResult"]);
	// 		print_r($responseWs);

	// 	}
	// }
	$nit = explode("-", $_SESSION['NITEMPRESA']);
	// echo "$nit[0]<br>";
	/*$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL            => "https://test.facse.net/conexion/comprobante.asmx",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_TIMEOUT        => 30,
		CURLOPT_CUSTOMREQUEST  => "POST",
		CURLOPT_POSTFIELDS     => "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\r\n  <soap:Body>\r\n    <ListaComprobantesPendientes xmlns=\"http://tempuri.org/\">\r\n      <fechaInicial>2000-01-01</fechaInicial>\r\n      <fechaFinal>3019-01-01</fechaFinal>\r\n      <emisor>$nit[0]</emisor>\r\n    </ListaComprobantesPendientes>\r\n  </soap:Body>\r\n</soap:Envelope>",
		CURLOPT_HTTPHEADER     => array( "Content-Type: text/xml", ),
	));

	$response = curl_exec($curl);
	$err      = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
  		echo $response;
  		$response = json_decode($response, true);
		var_dump($response);
	}*/

?>
<style>
	#DashContenedor{
		width: calc(100%-40px);
		margin : 20px;
		float: left;
	}
	.DashIndicador1{
		background:rgba(0,102,153,.1);
		font-family:Verdana, Geneva, sans-serif;
		float:left;
		width:250px;
		height:150px;
		/*border:1px solid #CCC;*/
		margin:10px;
		/*box-shadow:1px 1px 2px #BBB;*/
		border-radius:20px 0 20px 0;
		/*border:0 1px 1px 0 solid #BBB;*/
	}
	.DashIndicador1 .indicador{
		font-size:50px;
		width: calc(50% - 20px);
		padding:5px 0 0 0;
		margin:10px 0 -5px 20px;
		float:left;
		font-weight:bold;
		color: #333;
	}
	.DashIndicador1 .indicador2{
		font-size:20px;
		width: calc(50% - 20px);
		padding:5px 0 0 0;
		margin:10px 0 -5px 20px;
		float:left;
		font-weight:bold;
		color: #333;
	}
	.DashIndicador1 .imagen{
		font-size:50px;
		width: calc(50% - 20px);
		height:48px;
		padding:5px 0 0 0;
		margin:10px 0 -5px 20px;
		float:left;
		font-weight:bold;
		color: #333;
		background-repeat:no-repeat;
	}
	.DashIndicador1 .label{
		font-size:14px;
		width: calc(100% - 40px);
		height:20px;
		padding:0 20px 0 20px;
		float:left;
		color:#069;
		/*border-radius:0 0 10px 10px;*/
	}
	.DashIndicador1 .label1{
		font-size:11px;
		width: calc(100% - 40px);
		height:15px;
		margin-top:10px;
		padding:0 20px 0 20px;
		float:left;
		color:red;


		/*border-radius:0 0 10px 10px;*/
	}
	.DashIndicador1 .label2{
		font-size:11px;
		width: calc(100% - 40px);
		height:15px;
		padding:0 20px 0 20px;
		float:left;
		color:green;
		/*border-radius:0 0 10px 10px;*/
	}

	.DashIndicador1 .label3{
		font-size : 11px;
		width     : calc(100% - 40px);
		height    : 15px;
		padding   : 0 20px 0 20px;
		float     : left;
		color     : #ff9d00;
	}

	#DashContenedor .alert{
		background:url(images/alert.png);
		background-position:center;
		background-repeat:no-repeat;
	}

	#DashContenedor .ok{
		background:url(images/ok.png);
		background-position:center;
		background-repeat:no-repeat;
	}
	.fondo_modal{
	    z-index          : 100;
	    top              : 0px;
	    width            : 100%;
	    height           : 100%;
	    display          : table;
	    left             : 0px;
	    position         : fixed !important;
	    background-color : rgba(0,0,0,0.6);       /* Color de fondo */
	}

	.image_loading{
	    width    : 90px;
	    height   : 90px;
	    position : absolute;
	    left     : 60%;
	    top      : 50%;
	    margin   : -75px 0 0 -135px;
	}

	/*=======================// MODAL UPLOAD FILE //=======================*/

	.fondo_modal_upload_file{
		display          : none;
		top              : 0px;
		left             : 0px;
		width            : 100%;
		height           : 100%;
		z-index          : 10000;
		position         : fixed;
		background-color : rgba(0,0,0,0.6);
	}

	.fondo_modal_upload_file > div{
	  position : absolute;
	  display  : table;
	  height   : 100%;
	  width    : 100%;
	  top      : 0;
	  left     : 0;
	}

	.fondo_modal_upload_file > div > div{
	  display        : table-cell;
	  vertical-align : middle;
	  width          : 100%;
	}

	.fondo_modal_upload_file > div > div > div{
	  width  : 400px;
	  height : 300px;
	  margin : 0px auto;
	}


	#div_upload_file{
	  width         : 400px;
	  height        : 300px;
	  background    : #FFF;
	  overflow      : hidden;
	  position      : fixed;
	  border        : 3px dashed #bcbcbc;
	}

	#div_upload_file:hover{
		border : 3px dashed #9e9e9e;
	}

	#div_upload_file > div{
	  font-size: 23px;
	  margin-top: 130px;
	  text-align: center;
	  color: #bcbcbc;
	}

	.btn_div_upload_file1{
	  width            : 26px;
	  height           : 26px;
	  position         : fixed;
	  margin-top       : -10px;
	  margin-left      : 390px;
	  font-size        : 20px;
	  font-weight      : bold;
	  text-align       : center;
	  background-color : #bcbcbc;
	  border-radius    : 12px;
	  cursor           : pointer;
	}

	.btn_div_upload_file2{
	  color            : #9e9e9e;
	  width            : 22px;
	  height           : 22px;
	  position         : fixed;
	  margin-top       : 6px;
	  margin-left      : 375px;
	  font-size        : 20px;
	  font-weight      : bold;
	  text-align       : center;
	  background-color : #fff;
	  cursor           : pointer;
	}

	.btn_div_upload_file2:hover{
		color : #32B1D9;
		border: 1px solid #32B1D9;
	}

	#btn_cancel_doc_upload{
	  cursor           : pointer;
	  display          : none;
	  float            : left;
	  margin           : 7px -35px;
	  height           : 23px;
	  width            : 25px;
	  border           : 1px solid;
	  border-color     : #c4c4c4 #d1d1d1 #d4d4d4;
	  border-radius    : 2px;
	  padding-top      : 2px;
	  text-align       : center;
	  font-weight      : bold;
	  font-size        : 23px;
	  color            : #32B1D9;
	  background-color : #f3f3f3;
	}

	#div_upload_file > div {
		margin: 0px !important;
	}

	#div_upload_file:before {
	  content    : 'Arrastre el documento';
	  width      : 100%;
	  float      : left;
	  font-size  : 23px;
	  margin-top : 130px;
	  text-align : center;
	  color      : #bcbcbc;
	}
	.fondo_modal_upload_file .qq-uploader { position:relative; width: 100%; height:100%; }
	.fondo_modal_upload_file .qq-upload-button { display:block; position:fixed !important; width:100px; height:34px; margin:5px; background-image:url(img/uploading.png); background-size: 100px; }
	.fondo_modal_upload_file .qq-upload-button-hover { background-image:url(img/uploading_blue.png) }
	.fondo_modal_upload_file .qq-upload-button-focus { }
	.fondo_modal_upload_file .qq-upload-drop-area { position:absolute; top:40; left:0; width:100%; height:100%; min-height: 70px; background:none; text-align:center; display:none;  }
	.fondo_modal_upload_file .qq-upload-drop-area span {  display:block; position:absolute; top: 50%; width:100%; margin-top:-8px; font-size:16px; }
	.fondo_modal_upload_file .qq-upload-drop-area-active { background:#FF0000; opacity: 0.3; filter:alpha(opacity=30); -moz-opacity:0.3; -khtml-opacity: 0.3; }
	.fondo_modal_upload_file .qq-upload-list { height:100%; list-style:none; color:#333; text-align:center; }
	.fondo_modal_upload_file .qq-upload-list li {  margin:0; padding:0; line-height:15px; font-size:12px; }
	.fondo_modal_upload_file .qq-upload-file, .qq-upload-spinner, .qq-upload-size, .qq-upload-cancel, .qq-upload-failed-text {  margin-right: 7px; }
	.fondo_modal_upload_file .qq-upload-file {  }

	.fondo_modal_upload_file .qq-upload-spinner {
	  display             : inline-block;
	  background-image    : url("img/loading.gif");
	  width               : 400px;
	  height              : 400px;
	  vertical-align      : text-bottom;
	  position            : absolute;
	  top                 : 0;
	  left                : 0;
	  background-repeat   : no-repeat;
	  background-position : 100px 50px;
	  background-color    : #fff;
	}

	.fondo_modal_upload_file .qq-upload-size,.qq-upload-cancel { font-size:11px; }
	.fondo_modal_upload_file .qq-upload-failed-text { display:none; }
	.fondo_modal_upload_file .qq-upload-fail .qq-upload-failed-text { display:inline; }

</style>
<div id="DashContenedor">

<?php
	$mouseEvents = 'onmouseover="cambiarPunteroDiv()" onmouseout="devolverPunteroDiv()"';
?>

    <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND1 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Cotizaciones</div>
		<div class="label1"><div style="float:left"id="cancela1"><?php echo $IND1_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela1','cancel','ventas_cotizaciones','cotizaciones canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left"id="edicion1"><?php echo $IND1_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion1','edit','ventas_cotizaciones','cotizaciones en edicion')">En edicion</div></div>

    </div>
    <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND2 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Pedidos</div>
        <div class="label1"><div style="float:left"id="cancela2"><?php echo $IND2_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela2','cancel','ventas_pedidos','pedidos cancelados')">Cancelados</div></div>
	    <div class="label2"><div style="float:left"id="edicion2"><?php echo $IND2_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion2','edit','ventas_pedidos','pedidos en edicion')">En edicion</div></div>
    </div>
     <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND3 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Remisiones</div>
        <div class="label1"><div style="float:left"id="cancela3"><?php echo $IND3_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela3','cancel','ventas_remisiones','remisiones canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left"id="edicion3"><?php echo $IND3_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion3','edit','ventas_remisiones','remisiones en edicion')">En edicion</div></div>
    </div>
     <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND4 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Facturas de venta</div>
        <div class="label1"><div style="float:left"id="cancela4"><?php echo $IND4_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela4','cancel','ventas_facturas','facturas de venta canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left"id="edicion4"><?php echo $IND4_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion4','edit','ventas_facturas','facturas de venta en edicion')">En edicion</div></div>
	    <div class="label3"><div style="float:left"id="no_send4"><?php echo $facturasPendientesDian ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('no_send4','no_send','ventas_facturas','Pendientes')">Sin enviar a la Dian</div></div>
    </div>
     <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND5 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Recibos de caja</div>
        <div class="label1"><div style="float:left"id="cancela5"><?php echo $IND5_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela5','cancel','recibo_caja','recibos de caja cancelados')">Cancelados</div></div>
	    <div class="label2"><div style="float:left"id="edicion5"><?php echo $IND5_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion5','edit','recibo_caja','recibos de caja en edicion')">En edicion</div></div>
    </div>

     <!--<div id="divVisualizador" class="modal_dashboard">
       <div>
       		<div>
				<div style="overflow:hidden;" class="btnDashBoard">
					<div style="float:left;" onclick="agruparPor(this.innerHTML)" onmouseover="cambiarPuntero()" onmouseout="devolverPuntero()">Global</div>
					<div style="float:left;" onclick="agruparPor(this.innerHTML)" onmouseover="cambiarPuntero()" onmouseout="devolverPuntero()">Cliente</div>
					<div style="float:left;" onclick="agruparPor(this.innerHTML)" onmouseover="cambiarPuntero()" onmouseout="devolverPuntero()">Vendedor</div>
					<div style="float:left;" onclick="agruparPor(this.innerHTML)" onmouseover="cambiarPuntero()" onmouseout="devolverPuntero()">Centro costo</div>
					<div style="float:left;" onclick="agruparPor(this.innerHTML)" onmouseover="cambiarPuntero()" onmouseout="devolverPuntero()">Pendientes por facturar</div>
					<div style="float:left;" onclick="agruparPor(this.innerHTML)" onmouseover="cambiarPuntero()" onmouseout="devolverPuntero()">Facturadas</div>
				</div>
       			<div class="btn_close_dashboard" onclick="close_ventana_dashboard()" onmouseout="devolverPuntero()" onmouseover="cambiarPuntero()">X</div>
       			<div id="tabla_dashboard"></div>
	            <div id="texto_dashboard"></div>
	        </div>

        </div>

     </div>-->
</div>

<style type="text/css">

	.btnDashBoard > div{
		margin: 5px;
		border: 1px solid #999;
		background-color: #e8e8e8;
	}

	.btn_close_dashboard{
		width: 20px;
		height: 20px;
		margin: 10px 7px;
		float: right;
	}

	.modal_dashboard{
		background-color : rgba(0,0,0,0.6);
		display: none;
		position: fixed;
		height : 100%;
		width  : 100%;
		top    : 0;
		left   : 0;
	}

	.modal_dashboard > div{
		display: table-cell;
		vertical-align : middle;
		width  : 100%;

	}

	.modal_dashboard > div > div{
		height: 700px;
		width: 800px;
		background-color: #FFF;
		margin: auto;
		border        : 3px dashed #bcbcbc;
	}

	.modal_dashboard > div > div:hover{ border : 3px dashed #9e9e9e; }



</style>
<script type="text/javascript">

   var id_bodega = document.getElementById('filtro_ubicacion_DashboardVenta').value,
       id_sucursal = document.getElementById('filtro_sucursal_DashboardVenta').value;

   //document.body.style.cursor = 'auto';

   function cambiarPuntero(){


   	       //document.body.style.cursor = 'hand';
   }


   function devolverPuntero(){


   	       document.body.style.cursor = 'auto';
   }

   //----------------------------PUNTEROS DE CANCELADOS Y EN EDICION---------------------------------------------

    function cambiarPunteroDiv(){

    	   //if()


   	       document.body.style.cursor = 'hand';
   }

    function devolverPunteroDiv(){


   	       document.body.style.cursor = 'auto';
   }

   function mostrarModal(valor){

           /*var tabla = valor;
		   document.getElementById('divVisualizador').setAttribute('style','display:table;');
		   document.getElementById('tabla_dashboard').innerHTML = tabla;*/

   }


   function mostrarInf(valor1,valor2,valor3,valor4){

			var cantidad = document.getElementById(valor1).innerHTML;
   	        var opc = valor2;

   			var cantidad = document.getElementById(valor1).innerHTML;

   			var tabla = valor3;


   			if(cantidad != '0'){

   				   //alert("holaaaa"+cantidad+opc);

   				   var myalto  = Ext.getBody().getHeight();
   				   var myancho = Ext.getBody().getWidth();

   				   Win_Ventana_DashVentas = new Ext.Window({
   				       width       : 680,
   				       height      : 450,
   				       id          : 'Win_Ventana_Info',
   				       title       : '<span style="text-transform: uppercase;">'+valor4+'</span>',
   				       modal       : true,
   				       autoScroll  : true,
   				       closable    : true,
   				       autoDestroy : true,
   				       bodyStyle 	: 'background-color:#fff;',
   				       autoLoad    :
   				       {
   				           url     : 'bd/mostrar_info_dashboard_ventas.php',
   				           scripts : true,
   				           nocache : true,
   				           params  :
   				           {
   				               opc    	: opc,
   				               tabla  	: tabla,
   				               bodega 	: id_bodega,
   				               sucursal : id_sucursal,
   				               titulo   : valor4,
   				               fechai   : '<?php echo $fechai ?>',
							   fechaf   : '<?php echo $fechaf ?>'
   				           }
   				       }

   				   }).show();


   			}
   			//else{  alert("hola"); }
   }

   document.getElementById("id_periodo").innerHTML = '<?php echo $Titulo ?>';
   document.getElementById("id_rango").innerHTML = '<?php echo $rango ?>';


   function close_ventana_dashboard(){

   			document.getElementById('divVisualizador').setAttribute('style','display:none;');
   }

   function cambiarPeriodoAdelante(){

		var filtro_bodega   = document.getElementById('filtro_ubicacion_DashboardVenta').value
		,	filtro_sucursal = document.getElementById('filtro_sucursal_DashboardVenta').value

        periodo = '<?php echo $periodo ?>';

    	if(periodo == 'dia'){
			var Newperiodo = 'semana';
		}

		if(periodo == 'semana'){
			var Newperiodo = 'mes';
		}
		if(periodo == 'mes'){
			var Newperiodo = 'year';
		}
		if(periodo == 'year'){
			var Newperiodo = 'dia';
		}

		Ext.get('contenedor_DashboardVenta').load({
			url     : 'dashboard/dashboard.php',
			scripts : true,
			nocache : true,
			params  : {
				periodo         : Newperiodo,
				filtro_bodega   : filtro_bodega,
				filtro_sucursal : filtro_sucursal,
			}
		});
	}


    function cambiarPeriodoAtras(){

    	var filtro_bodega   = document.getElementById('filtro_ubicacion_DashboardVenta').value
		,	filtro_sucursal = document.getElementById('filtro_sucursal_DashboardVenta').value

        periodo = '<?php echo $periodo ?>';

    	if(periodo == 'year'){
    		var Newperiodo = 'mes';
    	}

		if(periodo == 'mes'){
			var Newperiodo = 'semana';
		}
		if(periodo == 'semana'){
			var Newperiodo = 'dia';
		}
		if(periodo == 'dia'){
			var Newperiodo = 'year';
		}

		Ext.get('contenedor_DashboardVenta').load({
			url     : 'dashboard/dashboard.php',
			scripts : true,
			nocache : true,
			params  : {
				periodo         : Newperiodo,
				filtro_bodega   : filtro_bodega,
				filtro_sucursal : filtro_sucursal,

			}
		});

	}

	function RecargaPeriodo(){
		Ext.getCmp('contenedor_DashboardCompra').load(
			{
				url     : 'dashboard.php',
				scripts : true,
				nocache : true,
				params  : { periodo:periodo }
			}
		);
	}

	// CONSULTAR LAS FACTURAS DE FACSE PENDIENTES
	// Ext.Ajax.request({
	//     url     : 'https://test.facse.net/conexion/comprobante.asmx?wsdl',
	//     params  :
	//     {
	// 		op           : "ListaComprobantesPendientes",
	// 		fechaInicial : "2018-01-01",
	// 		fechaFinal   : "2019-01-01",
	// 		emisor       : "900467785",
	//     },
	//     success :function (result, request){
	//                 if(result.responseText == 'true'){ console.log("true"); }
	//                 else{ console.log("false"); }
	//             },
	//     failure : function(){ console.log("fail"); }
	// });

</script>
