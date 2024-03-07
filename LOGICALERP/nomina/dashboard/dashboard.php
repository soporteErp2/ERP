<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	//-------------------------------------SELECTOR DE PERIODOS A MOSTRAR EN EL DASHBOARD-------------------------------------------------
	// $hoy           = date("Y-m-d");
	// $diaMes        = date("j");
	// $diaSemana     = date("N");
	// $numeroDiasMes = date("t");
	// $year          = date("Y");

	// if(!isset($periodo)){
	// 	$periodo = 'dia';
	// }
 //    //echo $periodo;

 //    echo '<script>var periodo = "'.$periodo.'"</script>';

	// if($periodo == 'year'){
	// 	$Titulo = 'Indicadores del A&ntilde;o';
	// 	$fecha  = $year;
	// 	$fechai = $fecha.'-01-01';
	// 	$fechaf = $hoy;
	// 	$rango  = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);

	// }
	// if($periodo == 'mes'){
	// 	$Titulo = 'Indicadores del Mes';
	// 	$resta  = $diaMes - 1;
	// 	$fechai = date("Y-m-d",strtotime ( '-'.$resta.' day',strtotime($hoy)));
	// 	$suma   = $numeroDiasMes - $diaMes;
	// 	$fechaf = date("Y-m-d",strtotime ( '+'.$suma.' day',strtotime($hoy)));
	// 	$rango  = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);
	// }
	// if($periodo == 'semana'){
	// 	$Titulo = 'Indicadores de la Semana';
	// 	$resta  = $diaSemana - 1;
	// 	$fechai = date("Y-m-d",strtotime ( '-'.$resta.' day',strtotime($hoy)));
	// 	$suma   = 7 - $diaSemana;
	// 	$fechaf = date("Y-m-d",strtotime ( '+'.$suma.' day',strtotime($hoy)));
	// 	$rango  = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);
	// }
	// if($periodo == 'dia'){
	// 	$Titulo = 'Indicadores del D&iacute;a';
	// 	$fechai = $hoy;
	// 	$fechaf = $hoy;
	// 	$rango  = fecha_larga($fechai);
	// }

    //echo $fechai.' '.$fechaf;
	//echo '<script>alert("'.$periodo.'")</script>';

    // CONDICIONES DE LOS QUERY'S
	$filtro_sucursal = ($filtro_sucursal == 0)? "" : " AND id_sucursal = '$filtro_sucursal' " ;
    $where_fechas = " AND fecha_documento BETWEEN '$fechai' AND '$fechaf'";
	$whereGlobal = "(estado = 1 OR estado=2) AND activo = 1 AND id_empresa = $id_empresa ";
    $whereCanceladas = " estado = 3 AND consecutivo > 0 AND activo = 1 AND id_empresa = $id_empresa ";
    $whereEditadas = " estado = 0 AND consecutivo > 0 AND activo = 1 AND id_empresa = $id_empresa ";

	// INDICADORES DE LA PLANILLA DE NOMINA GENERADA
	$sql   = "SELECT * FROM nomina_planillas WHERE $whereGlobal $filtro_sucursal $where_fechas";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE NOMINA ANULADA
	$sql   = "SELECT * FROM nomina_planillas WHERE $whereCanceladas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_canceladas = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE NOMINA EDITADAS
	$sql   = "SELECT * FROM nomina_planillas WHERE $whereEditadas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_editadas = $mysql->num_rows($query);


	// INDICADORES DE LA PLANILLA DE LIQUIDACION GENERADA
	$sql   = "SELECT * FROM nomina_planillas_liquidacion WHERE $whereGlobal $filtro_sucursal $where_fechas";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_liquidacion = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE LIQUIDACION ANULADA
	$sql   = "SELECT * FROM nomina_planillas_liquidacion WHERE $whereCanceladas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_liquidacion_canceladas = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE LIQUIDACION EDITADAS
	$sql   = "SELECT * FROM nomina_planillas_liquidacion WHERE $whereEditadas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_liquidacion_editadas = $mysql->num_rows($query);


	// INDICADORES DE LA PLANILLA DE AJUSTE GENERADA
	$sql   = "SELECT * FROM nomina_planillas_ajuste WHERE $whereGlobal $filtro_sucursal $where_fechas";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_ajuste = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE AJUSTE ANULADA
	$sql   = "SELECT * FROM nomina_planillas_ajuste WHERE $whereCanceladas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_ajuste_canceladas = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE AJUSTE EDITADAS
	$sql   = "SELECT * FROM nomina_planillas_ajuste WHERE $whereEditadas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_ajuste_editadas = $mysql->num_rows($query);


	// INDICADORES DE LA PLANILLA DE CONSOLIDACION GENERADA
	$sql   = "SELECT * FROM nomina_planillas_consolidacion_provision WHERE $whereGlobal $filtro_sucursal $where_fechas";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_consolidacion_provision = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE CONSOLIDACION ANULADA
	$sql   = "SELECT * FROM nomina_planillas_consolidacion_provision WHERE $whereCanceladas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_consolidacion_provision_canceladas = $mysql->num_rows($query);

	// INDICADORES DE LA PLANILLA DE CONSOLIDACION EDITADAS
	$sql   = "SELECT * FROM nomina_planillas_consolidacion_provision WHERE $whereEditadas $filtro_sucursal $where_fechas ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_nomina_planillas_consolidacion_provision_editadas = $mysql->num_rows($query);

	$fechaVencimiento = date('Y-m-d', strtotime('+1 month'));
	$hoy = date('Y-m-d');

	// INDICADORES DE LOS CONTRATOS DE EMPLEADOS
	$sql   = "SELECT * FROM empleados_contratos WHERE estado=0 AND id_empresa = $id_empresa $filtro_sucursal ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_contratos = $mysql->num_rows($query);

	// INDICADORES DE LOS CONTRATOS DE EMPLEADOS VENCIDOS
	$sql   = "SELECT * FROM empleados_contratos WHERE (estado=0 OR estado=2) AND fecha_fin_contrato <= '$hoy' AND id_empresa = $id_empresa AND tipo_contrato<>'TERMINO INDEFINIDO '  $filtro_sucursal ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_contratos_vencidos = $mysql->num_rows($query);

	// INDICADORES DE LOS CONTRATOS DE EMPLEADOS POR VENCER
	$sql   = "SELECT * FROM empleados_contratos WHERE (estado=0 OR estado=2) AND fecha_fin_contrato BETWEEN '$hoy' AND '$fechaVencimiento' AND id_empresa = $id_empresa  $filtro_sucursal ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_contratos_por_vencer = $mysql->num_rows($query);

	// INDICADORES DE LOS CONTRATOS DE EMPLEADOS EN VACACIONES
	$sql   = "SELECT * FROM empleados_contratos WHERE estado=2  AND id_empresa = $id_empresa $filtro_sucursal ";
	$query = $mysql->query($sql,$mysql->link);
	$indicador_contratos_vacaciones = $mysql->num_rows($query);

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
		font-size:11px;
		width: calc(100% - 40px);
		height:15px;
		padding:0 20px 0 20px;
		float:left;
		color:green;
		/*border-radius:0 0 10px 10px;*/
	}

	.DashIndicador1 .label3{
		font-size:11px;
		width: calc(100% - 40px);
		height:15px;
		padding:0 20px 0 20px;
		float:left;
		color:orange;
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

	#DashContenedor .deal{
		background          : url(img/contrato.png);
		background-position : center;
		background-repeat   : no-repeat;
		background-size     : contain;
	}

	/* .fondo_modal{
	    z-index          : 100;
	    top              : 0px;
	    width            : 100%;
	    height           : 100%;
	    display          : table;
	    left             : 0px;
	    position         : fixed !important;
	    background-color : rgba(0,0,0,0.6);       Color de fondo
	} */

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

    <div class="DashIndicador1">
        <div class="indicador"><?php echo $indicador_nomina_planillas ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Planilla de Nomina</div>
		<div class="label1"><div style="float:left" id="cancela1" ><?php echo $indicador_nomina_planillas_canceladas ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('cancela1','cancel','nomina_planillas','Planillas canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left" id="edicion1" ><?php echo $indicador_nomina_planillas_editadas ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('edicion1','edit','nomina_planillas','Planillas en edicion')">En edicion</div></div>

    </div>
    <div class="DashIndicador1">
        <div class="indicador"><?php echo $indicador_nomina_planillas_liquidacion ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Planilla de liquidacion</div>
        <div class="label1"><div style="float:left" id="cancela2" ><?php echo $indicador_nomina_planillas_liquidacion_canceladas ?></div><div style="float:left;margin-left:5px;cursor:hand;" onclick="mostrarInf('cancela2','cancel','nomina_planillas_liquidacion','Liquidacion canceladas')">Cancelados</div></div>
	    <div class="label2"><div style="float:left" id="edicion2" ><?php echo $indicador_nomina_planillas_liquidacion_editadas ?></div><div style="float:left;margin-left:5px;cursor:hand;" onclick="mostrarInf('edicion2','edit','nomina_planillas_liquidacion','Liquidacion en edicion')">En edicion</div></div>
    </div>
     <div class="DashIndicador1">
        <div class="indicador"><?php echo $indicador_nomina_planillas_ajuste ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Planilla de ajuste</div>
        <div class="label1"><div style="float:left" id="cancela3" ><?php echo $indicador_nomina_planillas_ajuste_canceladas ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('cancela3','cancel','nomina_planillas_ajuste','Ajustes cancelados')">Canceladas</div></div>
	    <div class="label2"><div style="float:left" id="edicion3" ><?php echo $indicador_nomina_planillas_ajuste_editadas ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('edicion3','edit','nomina_planillas_ajuste','Ajustes en edicion')">En edicion</div></div>
    </div>
 	<div class="DashIndicador1">
        <div class="indicador"><?php echo $indicador_nomina_planillas_consolidacion_provision ?></div>
        <div class="imagen <?php echo $IMG3 ?>"></div>
        <div class="label">Planilla de consolidacion</div>
        <div class="label1"><div style="float:left" id="cancela4"><?php echo $indicador_nomina_planillas_consolidacion_provision_canceladas ?></div><div style="float:left;margin-left:5px;cursor: hand" onclick="mostrarInf('cancela4','cancel','nomina_planillas_consolidacion_provision','Consolidacion canceladas')">Canceladas</div></div>
	    <div class="label2"><div style="float:left" id="edicion4"><?php echo $indicador_nomina_planillas_consolidacion_provision_editadas ?></div><div style="float:left;margin-left:5px;cursor: hand" onclick="mostrarInf('edicion4','edit','nomina_planillas_consolidacion_provision','Consolidacion en edicion')">En edicion</div></div>
    </div>

	<div class="DashIndicador1">
        <div class="indicador"><?php echo $indicador_contratos ?></div>
        <div class="imagen deal"></div>
        <div class="label">Contratos Empleados</div>
        <div class="label1"><div style="float:left" id="cancela4" ><?php echo $indicador_contratos_vencidos ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('cancela4','vencidos','empleados_contratos','Contratos Vencidos')">Vencidos</div></div>
        <div class="label3"><div style="float:left" id="cancela4" ><?php echo $indicador_contratos_por_vencer ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('cancela4','por_vencer','empleados_contratos','Contratos por vencer')">Proximos a Vencer</div></div>
	    <div class="label2"><div style="float:left" id="edicion4" ><?php echo $indicador_contratos_vacaciones ?></div><div style="float:left;margin-left:5px;cursor: hand;" onclick="mostrarInf('edicion4','en_vacaciones','empleados_contratos','Empleados en Vacaciones')">En Vacaciones</div></div>
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

   // var id_bodega = document.getElementById('filtro_ubicacion_DashboardVenta').value,
   var id_sucursal = document.getElementById('filtro_sucursal_DashboardNomina').value;

   function mostrarInf(estado,opc,tabla,titulo){

			var cantidad = document.getElementById(estado).innerHTML;
   			var cantidad = document.getElementById(estado).innerHTML;

   			// if(cantidad != '0'){

   				   //alert("holaaaa"+cantidad+opc);

   				   var myalto  = Ext.getBody().getHeight();
   				   var myancho = Ext.getBody().getWidth();

   				   Win_Ventana_DashVentas = new Ext.Window({
   				       width       : 680,
   				       height      : 450,
   				       id          : 'Win_Ventana_Info',
   				       title       : '<span style="text-transform: uppercase;">'+titulo+'</span>',
   				       modal       : true,
   				       autoScroll  : true,
   				       closable    : true,
   				       autoDestroy : true,
   				       bodyStyle 	: 'background-color:#fff;',
   				       autoLoad    :
   				       {
   				           url     : 'bd/mostrar_info_dashboard_nomina.php',
   				           scripts : true,
   				           nocache : true,
   				           params  :
   				           {
   				               opc    	: opc,
   				               tabla  	: tabla,
   				               sucursal : id_sucursal,
   				               titulo   : titulo,
   				               fechai   : '<?php echo $fechai ?>',
							   fechaf   : '<?php echo $fechaf ?>'
   				           }
   				       }

   				   }).show();


   			// }
   }

   function close_ventana_dashboard(){

   			document.getElementById('divVisualizador').setAttribute('style','display:none;');
   }

	function cambiaPeriodoDashboard(){
		var	filtro_sucursal = document.getElementById('filtro_sucursal_DashboardNomina').value
		,	fechai          = document.getElementById('fechai').value
		,	fechaf          = document.getElementById('fechaf').value

		Ext.get('contenedor_DashboardNomina').load({
			url     : 'dashboard/dashboard.php',
			scripts : true,
			nocache : true,
			params  : {
				// periodo         : Newperiodo,
				// filtro_bodega   : filtro_bodega,
				filtro_sucursal : filtro_sucursal,
				fechai          : fechai,
				fechaf          : fechaf,

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
