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

	if($filtro_bodega == 0){ $filtro_bodega = ''; }
	else{ $filtro_bodega = " AND id_bodega = '$filtro_bodega'"; }

	if($filtro_sucursal == 0){ $filtro_sucursal = ''; }
	else{ $filtro_sucursal = " AND id_sucursal = '$filtro_sucursal'"; }

	$and_factura_cuentas = " AND factura_por_cuentas = 'true' AND id_saldo_inicial = 0";
	$and_factura_normal  = " AND factura_por_cuentas = 'false' AND id_saldo_inicial = 0";


    $whereComprobante_fecha = " AND fecha_inicial BETWEEN '$fechai' AND '$fechaf'";
    $where_fechas = " AND fecha_inicio BETWEEN '$fechai' AND '$fechaf'";

	//----------------------------------------------------CONSULTAS GLOBALES--------------------------------------------------------
	$whereGlobal1 =  " estado = 1 AND activo = 1 AND id_empresa = '$id_empresa'";

	$SQL1A = mysql_query("SELECT * FROM compras_requisicion WHERE".$whereGlobal1.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1A = mysql_num_rows($SQL1A);

	$SQL1 = mysql_query("SELECT * FROM compras_ordenes WHERE".$whereGlobal1.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1 = mysql_num_rows($SQL1);

	$SQL1B = mysql_query("SELECT * FROM compras_entrada_almacen WHERE".$whereGlobal1.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1B = mysql_num_rows($SQL1B);

	$SQL2 = mysql_query("SELECT * FROM compras_facturas  WHERE".$whereGlobal1.$filtro_bodega." ".$filtro_sucursal.$and_factura_normal.$where_fechas,$link);
	//echo "SELECT * FROM compras_facturas  WHERE".$whereGlobal1.$filtro_bodega." ".$filtro_sucursal.$and_factura_normal.$where_fechas;
	$IND2 = mysql_num_rows($SQL2);

	//FACTURAS POR CUENTAS
	$SQL3 = mysql_query("SELECT * FROM compras_facturas  WHERE".$whereGlobal1.$filtro_sucursal.$and_factura_cuentas.$where_fechas,$link);
	$IND3 = mysql_num_rows($SQL3);


	$SQL4 = mysql_query("SELECT * FROM comprobante_egreso  WHERE".$whereGlobal1.$filtro_sucursal.$whereComprobante_fecha,$link);
	$IND4 = mysql_num_rows($SQL4);

    //echo "SELECT * FROM compras_facturas  WHERE".$whereGlobal1.$filtro_bodega." ".$filtro_sucursal.$and_factura_normal;
	/*$SQL5 = mysql_query("SELECT * FROM recibo_caja  WHERE ".$whereGlobal.$filtro_bodega." ".$filtro_sucursal,$link);
	$IND5 = mysql_num_rows($SQL5);*/

	//--------------------------------------------------CONSULTAS DE LAS CANCELADAS-----------------------------------------------

    $whereGlobal2 = " estado = 3 AND consecutivo > 0 AND activo = 1 AND id_empresa = '$id_empresa'";

    $SQL1_1A = mysql_query("SELECT * FROM compras_requisicion WHERE".$whereGlobal2.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	//echo "\nSELECT * FROM ventas_cotizaciones WHERE ".$filtro_bodega." ".$filtro_sucursal;
	$IND1_1A = mysql_num_rows($SQL1_1A);

	$SQL1_1 = mysql_query("SELECT * FROM compras_ordenes WHERE".$whereGlobal2.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	//echo "\nSELECT * FROM ventas_cotizaciones WHERE ".$filtro_bodega." ".$filtro_sucursal;
	$IND1_1 = mysql_num_rows($SQL1_1);

	$SQL1_1B = mysql_query("SELECT * FROM compras_entrada_almacen WHERE".$whereGlobal2.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	//echo "\nSELECT * FROM ventas_cotizaciones WHERE ".$filtro_bodega." ".$filtro_sucursal;
	$IND1_1B = mysql_num_rows($SQL1_1B);

	$SQL2_1 = mysql_query("SELECT * FROM compras_facturas  WHERE".$whereGlobal2.$filtro_bodega." ".$filtro_sucursal.$and_factura_normal.$where_fechas,$link);
	//echo "SELECT * FROM compras_facturas  WHERE ".$filtro_bodega." ".$filtro_sucursal." AND factura_por_cuentas = false";
	$IND2_1 = mysql_num_rows($SQL2_1);

	//FACTURAS POR CUENTAS
	$SQL3_1 = mysql_query("SELECT * FROM compras_facturas  WHERE".$whereGlobal2.$filtro_sucursal.$and_factura_cuentas.$where_fechas,$link);
	$IND3_1 = mysql_num_rows($SQL3_1);


	$SQL4_1 = mysql_query("SELECT * FROM comprobante_egreso  WHERE".$whereGlobal2.$filtro_sucursal.$whereComprobante_fecha,$link);
	//echo "\nSELECT * FROM comprobante_egreso  WHERE ".$whereCanceladas.$filtro_bodega." ".$filtro_sucursal;
	$IND4_1 = mysql_num_rows($SQL4_1);


	/*$SQL5_1 = mysql_query("SELECT * FROM recibo_caja  WHERE ".$whereCanceladas.$filtro_bodega." ".$filtro_sucursal,$link);
	$IND5_1 = mysql_num_rows($SQL5_1);*/

	//--------------------------------------------------CONSULTAS DE LAS EDITADAS-----------------------------------------------

	$whereGlobal3 = " estado = 0 AND consecutivo > 0 AND activo = 1 AND id_empresa = '$id_empresa'";

	$SQL1_2A = mysql_query("SELECT * FROM compras_requisicion WHERE".$whereGlobal3.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1_2A = mysql_num_rows($SQL1_2A);

	$SQL1_2 = mysql_query("SELECT * FROM compras_ordenes WHERE".$whereGlobal3.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1_2 = mysql_num_rows($SQL1_2);

	$SQL1_2B = mysql_query("SELECT * FROM compras_entrada_almacen WHERE".$whereGlobal3.$filtro_bodega." ".$filtro_sucursal.$where_fechas,$link);
	$IND1_2B = mysql_num_rows($SQL1_2B);

	$SQL2_2 = mysql_query("SELECT * FROM compras_facturas  WHERE".$whereGlobal3.$filtro_bodega." ".$filtro_sucursal.$and_factura_normal.$where_fechas,$link);
	//echo "SELECT * FROM compras_facturas  WHERE".$filtro_bodega." ".$filtro_sucursal." AND factura_por_cuentas = 'false'";
	$IND2_2 = mysql_num_rows($SQL2_2);

	//FACTURAS POR CUENTAS
	$SQL3_2 = mysql_query("SELECT * FROM compras_facturas  WHERE".$whereGlobal3.$filtro_sucursal.$and_factura_cuentas.$where_fechas,$link);
	$IND3_2 = mysql_num_rows($SQL3_2);

	//COMPROBANTES DE EGRESO
	$SQL4_2 = mysql_query("SELECT * FROM comprobante_egreso  WHERE".$whereGlobal3.$filtro_sucursal.$whereComprobante_fecha,$link);
	$IND4_2 = mysql_num_rows($SQL4_2);


	/*$SQL5_2 = mysql_query("SELECT * FROM recibo_caja  WHERE".$filtro_bodega." ".$filtro_sucursal,$link);
	$IND5_2 = mysql_num_rows($SQL5_2);*/





	$IMG3 = 'ok';
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
		font-size :11px;
		width     : calc(100% - 40px);
		height    :15px;
		padding   :0 20px 0 20px;
		float     :left;
		color     :green;
		/*border-radius:0 0 10px 10px;*/
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

	/* MODAL UPLOAD FILE */

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
	.dashIndicador1 > .label {
		border-right: none;
	}

	#div_upload_file > div {  margin: 0px !important;  }

</style>
<div id="DashContenedor">

<?php
    $mouseEvents = 'onmouseover="cambiarPunteroDiv()" onmouseout="devolverPunteroDiv()"';
?>

	<div class="DashIndicador1">
        <div class="indicador"><?php echo $IND1A ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Requisiciones</div>
		<div class="label1"><div style="float:left"id="cancela1A"><?php echo $IND1_1A ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela1A','cancel','compras_requisicion','requisiciones canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left"id="edicion1A"><?php echo $IND1_2A ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion1A','edit','compras_requisicion','requisiciones en edicion')">En edicion</div></div>

    </div>

    <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND1 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Ordenes de compra</div>
		<div class="label1"><div style="float:left"id="cancela1"><?php echo $IND1_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela1','cancel','compras_ordenes','ordenes de compras canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left"id="edicion1"><?php echo $IND1_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion1','edit','compras_ordenes','ordenes de compras en edicion')">En edicion</div></div>

    </div>

    <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND1B ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Entrada de Almacen</div>
		<div class="label1"><div style="float:left"id="cancela1B"><?php echo $IND1_1B ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela1B','cancel','compras_entrada_almacen','entradas de almacen canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left"id="edicion1B"><?php echo $IND1_2B ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion1B','edit','compras_entrada_almacen','entradas de almacen en edicion')">En edicion</div></div>

    </div>

    <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND2 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Facturas de compra</div>
        <div class="label1"><div style="float:left" id="cancela2"><?php echo $IND2_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela2','cancel','compras_facturas1','facturas de compras canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left" id="edicion2"><?php echo $IND2_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion2','edit','compras_facturas1','facturas de compras en edicion')">En edicion</div></div>
    </div>
     <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND3 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Facturas por cuentas</div>
        <div class="label1"><div style="float:left" id="cancela3"><?php echo $IND3_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela3','cancel','compras_facturas2','facturas por cuentas canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left" id="edicion3"><?php echo $IND3_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion3','edit','compras_facturas2','facturas por cuentas en edicion')">En edicion</div></div>
    </div>
     <div class="DashIndicador1">
        <div class="indicador"><?php echo $IND4 ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Comprobantes de egreso</div>
        <div class="label1"><div style="float:left" id="cancela4"><?php echo $IND4_1 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('cancela4','cancel','comprobante_egreso','comprobantes de egreso cancelados')">Cancelados</div></div>
	    <div class="label2"><div style="float:left" id="edicion4"><?php echo $IND4_2 ?></div><div style="float:left;margin-left:5px" <?php echo $mouseEvents; ?> onclick="mostrarInf('edicion4','edit','comprobante_egreso','comprobantes de egreso en edicion')">En edicion</div></div>
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
		margin : 5px;
		border : 1px solid #999;
		background-color: #e8e8e8;
	}

	.btn_close_dashboard{
		width  : 20px;
		height : 20px;
		margin : 10px 7px;
		float  : right;
	}

	.modal_dashboard{
		display  : none;
		position : fixed;
		height   : 100%;
		width    : 100%;
		top      : 0;
		left     : 0;
		background-color : rgba(0,0,0,0.6);
	}

	.modal_dashboard > div{
		display: table-cell;
		vertical-align : middle;
		width  : 100%;

	}

	.modal_dashboard > div > div{
		height : 700px;
		width  : 800px;
		margin : auto;
		border : 3px dashed #bcbcbc;
		background-color : #FFF;
	}

	.modal_dashboard > div > div:hover{ border : 3px dashed #9e9e9e; }



</style>
<script type="text/javascript">

	var id_bodega   = document.getElementById('filtro_ubicacion_DashboardCompra').value
	,	id_sucursal = document.getElementById('filtro_sucursal_DashboardCompra').value;


    //document.body.style.cursor = 'auto';

   	function cambiarPuntero(){
   	    //document.body.style.cursor = 'hand';
   	}


	function devolverPuntero(){
		document.body.style.cursor = 'auto';
	}

       //----------------------------PUNTEROS DE CANCELADOS Y EN EDICION---------------------------------------------

    function cambiarPunteroDiv(){ document.body.style.cursor = 'hand'; }
    function devolverPunteroDiv(){ document.body.style.cursor = 'auto'; }

   	function mostrarModal(valor){
       /*var tabla = valor;
	   document.getElementById('divVisualizador').setAttribute('style','display:table;');
	   document.getElementById('tabla_dashboard').innerHTML = tabla;*/
  	}

   	function mostrarInf(valor1,valor2,valor3,valor4){

   		//alert(valor1+' '+valor2+' '+valor3+' '+valor4);

		var cantidad = document.getElementById(valor1).innerHTML;
		var opc      = valor2;
		var cantidad = document.getElementById(valor1).innerHTML;
		var tabla    = valor3;

			if(cantidad != '0'){

			   var myalto  = Ext.getBody().getHeight();
			   var myancho = Ext.getBody().getWidth();

			Win_Ventana_Info = new Ext.Window({
				width       : 650,
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
					url     : 'bd/mostrar_info_dashboard_compras.php',
					scripts : true,
					nocache : true,
					params  :
					{
						opc      : opc,
						tabla    : tabla,
						bodega   : id_bodega,
						sucursal : id_sucursal,
						titulo   : valor4,
						fechai   : '<?php echo $fechai ?>',
						fechaf   : '<?php echo $fechaf ?>'
					}
				}
			}).show();
		}
	}

	document.getElementById("id_periodo").innerHTML = '<?php echo $Titulo ?>';
	document.getElementById("id_rango").innerHTML = '<?php echo $rango ?>';
	//document.getElementById("id_periodo").innerHTML = '<? echo $titulo ?>';


    function close_ventana_dashboard(){ document.getElementById('divVisualizador').setAttribute('style','display:none;'); }

    function cambiarPeriodoAtras(){
    	var filtro_bodega   = document.getElementById('filtro_ubicacion_DashboardCompra').value
		,	filtro_sucursal = document.getElementById('filtro_sucursal_DashboardCompra').value

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

		Ext.get('contenedor_DashboardCompra').load({
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
	function cambiarPeriodoAdelante(){
		var filtro_bodega   = document.getElementById('filtro_ubicacion_DashboardCompra').value
		,	filtro_sucursal = document.getElementById('filtro_sucursal_DashboardCompra').value

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

		Ext.get('contenedor_DashboardCompra').load({
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

</script>