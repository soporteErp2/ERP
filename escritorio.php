<?php

    // VALIDAR SI SE RECIBEN DATOS PARA ASIGNAR LA SUCURSAL
    if (@$_POST['cloud']=='true') {
        session_start();
        $_SESSION['SUCURSAL']               = $_POST['id_sucursal'];
        $_SESSION['NOMBRESUCURSAL']         = $_POST['sucursal'];
    }
    // echo "<script>console.log('$_POST[cloud] - $_SESSION[CEDULAFUNCIONARIO] - $_SESSION[NOMBREFUNCIONARIO] ');</script>";
    include("configuracion/session.php"); //EN ESTE ARCHVO SE DEFINEN LAS VARIABLES DE SESSION QE SE TILIZARAN EN LA APLICACION  Y REALIZA LAS CONSULTAS PROPIAS DE LA APLICACION QUE SE EJECUTARA EN ESTE ESCRITORIO
    include("temas/clasico/configuracion_pantalla.php");// EN ESTE ARCHIVO SE DEFINE LA CONFIGURACION DE LOS ELEMENTOS DE LA PANTALLA SEGUN EL "TEMA"
    
	$dat_person = mysql_query("SELECT * FROM empleados WHERE id = $_SESSION[IDUSUARIO]",$link); //OBTIENE LOS DATOS PARA LA VENTANA DE OPCIONES PERSONALES
	$DatPersonales = mysql_fetch_array($dat_person);
	if(!isset($_SESSION['IDUSUARIO'])){header ("Location: login.php");}

    if ($_SESSION['SUPPORT'] =='true') {
        $DatPersonales['cargo']    = 'Soporte Tecnico';
        $DatPersonales['empresa']  = 'LogicalSoft S.A.S';
        $DatPersonales['sucursal'] = 'Cali';
    }

  //================ FUNCION PARA CALCULAR EL COLOR DE DEGRADE ===============//
  function ColorDegrade($color){
    $col = explode(',',$color);
    $col[0] = $col[0]+50; if($col[0] > 255){$col[0] = 255;}
    $col[1] = $col[1]+50; if($col[1] > 255){$col[1] = 255;}
    $col[2] = $col[2]+50; if($col[2] > 255){$col[2] = 255;}
    return $col[0].','.$col[1].','.$col[2];
  }
  $NewColorDegrade = ColorDegrade($_SESSION['COLOR_ESCRITORIO']);

	//================== CALCULAR VENCIMIENTO DE LA RESOLUCION =================//
	$sql = "SELECT id,DATE_ADD(fecha_resolucion,INTERVAL 2 YEAR) AS fecha_resolucion_fin,numero_final_resolucion
					FROM ventas_facturas_configuracion
			 		WHERE activo = 1
	 				AND id_empresa  = '$_SESSION[EMPRESA]'
          AND id_sucursal = '$_SESSION[SUCURSAL]'
          AND tipo = 'FE'
		 			ORDER BY id DESC";

    $query                          = mysql_query($sql,$link);
    $id_resolucion                  = mysql_result($query,0,'id');
    $fecha_resolucion_fin           = mysql_result($query,0,'fecha_resolucion_fin');
    $numero_final_resolucion        = mysql_result($query,0,'numero_final_resolucion');
    $fecha_diferencia               = date_diff(date_create(date('Y-m-d')),date_create($fecha_resolucion_fin));
    $fecha_diferencia               = $fecha_diferencia->format("%r%a");
    $texto_numero                   = abs($fecha_diferencia);
    ($texto_numero > 1)? $texto_dia = "dias" : $texto_dia = "dia";

  //AVERIGUAMOS SI LA CONSULTA OBTUVO DATOS
  if(mysql_num_rows($query) != 0){
    $resolucion_activa = "si";
  }
  else{
    $resolucion_activa = "no";
  }

	//============ CALCULAR CONSECUTIVOS RESTANTES DE LA RESOLUCION ============//
	$sql = "SELECT numero_factura
  				FROM ventas_facturas
  				WHERE activo = 1
  				AND id_empresa = '$_SESSION[EMPRESA]'
          AND id_sucursal = '$_SESSION[SUCURSAL]'
          AND id_configuracion_resolucion = '$id_resolucion'
  				ORDER BY numero_factura DESC";

	$query      					 = mysql_query($sql,$link);
	$numero_factura_actual = mysql_result($query,0,'numero_factura');
	$resolucion_diferencia = $numero_final_resolucion - $numero_factura_actual;
	$numero_consecutivo 	 = abs($resolucion_diferencia);
	($numero_consecutivo > 1)? $texto_consecutivo = "consecutivos" : $texto_consecutivo = "consecutivo";
?>
<!DOCTYPE HTML>
<html>
	<head>
        <!--<meta http-equiv="X-UA-Compatible" content="chrome=1">-->
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="SHORTCUT ICON" href="favicon.ico">
        <link rel="stylesheet" type="text/css" href="temas/clasico/estilo.php">
        <link rel="stylesheet" type="text/css" href="misc/extjs3/resources/css/ext-all.css"/>
        <link rel="stylesheet" href="misc/alertifyjs/css/alertify.min.css" />
        <link rel="stylesheet" href="misc/alertifyjs/css/themes/default.min.css" />

        <title><?php echo $TITULO_ESCRITORIO ?></title>

        <script type="text/javascript" src="misc/lib.js"></script>
        <script type="text/javascript" src="misc/extjs3/ext-base.js?v1.0.0.19-06-2013"></script>
        <script type="text/javascript" src="misc/extjs3/ext-all.js?v1.0.0.19-06-2013"></script>
        <script type="text/javascript" src="misc/jquery/2.0.3/jquery-2.0.3.min.js"></script>
        <script type="text/javascript" src="misc/chartjs/Chart.js"></script>
        <script type="text/javascript" src="misc/alertifyjs/alertify.min.js"></script>
        <script type="text/javascript" src="misc/MyFunctions.js?v4.2.27012014"></script>

        <!--##########################  PICKER HORAS ###############################-->
        <link rel="stylesheet" type="text/css" href="misc/clockpicker/clockpicker.css"/>
        <link rel="stylesheet" type="text/css" href="misc/clockpicker/standalone.css"/>
        <script src="misc/clockpicker/clockpicker.js" type="text/javascript"></script>
        <!--############################################################################-->

        <style>
            body {
                width               : 100%;
                height              : 100%;
                background          : rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1);
                background          : -moz-radial-gradient(center, ellipse cover, rgba(<?php echo $NewColorDegrade ?>,1) 0%, rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1) 100%);
                background          : -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(<?php echo $NewColorDegrade ?>,1)), color-stop(100%, rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1)));
                background          : -webkit-radial-gradient(center, ellipse cover, rgba(<?php echo $NewColorDegrade ?>,1) 0%, rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1) 100%);
                background          : -o-radial-gradient(center, ellipse cover, rgba(<?php echo $NewColorDegrade ?>,1) 0%, rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1) 100%);
                background          : -ms-radial-gradient(center, ellipse cover, rgba(<?php echo $NewColorDegrade ?>,1) 0%, rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1) 100%);
                background          : radial-gradient(ellipse at center, rgba(<?php echo $NewColorDegrade ?>,1) 0%, rgba(<?php echo $_SESSION['COLOR_ESCRITORIO']; ?>,1) 100%);
                color               : #333;
                font-family         : Verdana,sans-serif,Tahoma;
                font-size           : 11px;
                overflow            : hidden;
                background-repeat   : no-repeat;
            }
            #datos_usuario {
                position            : absolute;
                text-align          : center;
                color               : #FFF;
                width               : 325px;
                height              : 160px;
                top                 : 20px;
                right               : 20px;
                background          : rgba(<?php echo $_SESSION['COLOR_MENU']; ?>,.80);
                /*background-image    : url(temas/clasico/images/escritorio/fondo_transparente.png);*/
                -webkit-box-shadow  : 1px 1px 5px #333;
                -moz-box-shadow     : 1px 1px 4px #333;
                -moz-border-radius  : 5px;
                /*-webkit-border-radius: 5px;
                box-shadow          : 1px 1px 4px #333;
                border-radius       : 5px;*/
            }
            .ActividadesReglon{
                float: left;
                width: 420px;
                margin: 10px 0 0 10px;
            }
            .Actividadesfield{
                float: left;
                width: 100px;
            }
            .ActividadesControl{
                float: left;
                width: 300px;
            }
        </style>

	</head>
	<body onLoad="iniciar(); cierra_preloading()">

        <?php
            include('login/cargando.php');
            if ($_SESSION['SUPPORT']<>'true') {
                include 'terminos_y_condiciones.php';
            }

            // actualizacion_user_name
            // muestraTerminosCondiciones($mysql);
        ?>

        <div style="position:absolute; top:0; left:0; z-index:0;" id="DIV_FONDO_ESCRITORIO"></div>
        <div id="inicio" style="position:absolute; left:-1px;  top:1933px; 	width:<?php echo $CONF_BOTONINICIO_ANCHO ?>px; height:<?php echo $CONF_BOTONINICIO_ALTO ?>px; z-index:8000; cursor:pointer;" class="BOTON_INICIO"  onClick="menu()">
                <div id="BTIcube">
                        <div class="BTIface one">
                            <div id="BTIcuadro" class="el1"></div>
                            <div id="BTIcuadro" class="el2"></div>
                            <div id="BTIcuadro" class="el1"></div>
                            <div id="BTIcuadro" class="el1"></div>
                        </div>

                        <div class="BTIface two">
                            <div id="BTIcuadro" class="el1"></div>
                            <div id="BTIcuadro" class="el3"></div>
                            <div id="BTIcuadro" class="el3"></div>
                            <div id="BTIcuadro" class="el3"></div>
                        </div>

                        <div class="BTIface three">
                            <div id="BTIcuadro" class="el2"></div>
                            <div id="BTIcuadro" class="el2"></div>
                            <div id="BTIcuadro" class="el3"></div>
                            <div id="BTIcuadro" class="el2"></div>
                        </div>
                </div>
        </div>
        <div id="barra"  style="position:absolute; left:86px;  top:1962px; 	width:427px; 	height:<?php echo $CONF_BARRATAREAS_ALTO ?>px; z-index:8000;">
            <div style="float:right; width:<?php echo $CONF_SYSEMTRAY_ANCHO ?>;">
                <div style="float:right; width:<?php echo $CONF_SYSEMTRAY_RELOJ_ANCHO ?>; height:<?php echo $CONF_BARRATAREAS_ALTO ?>;">
                    <div style="margin-top:5px; font-size:12px; color:#FFF; font-weight:bold; text-align:center">
                        <div id="IconoMostarEscritorio" style="float:left; width:24px; height:24px; margin:-2px 0 0 5px; cursor:pointer; background-image:url(temas/clasico/images/iconos/mostrar_escritorio.png?v2.7.0.0501)" onClick="escritorio()"></div>
                	    <div id="IconoAlertasEscritorio" style="float:left; width:24px; height:24px; margin:-2px 0 0 5px; cursor:pointer; background-image:url(temas/clasico/images/formularios/Calendar_Alert24.png?v2.7.0.0501)" onClick="abre_radar_manual()"></div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
        <div id="verificaMenuInicio" style="position: absolute;top: 0px;width: 100%;height: 100%;z-index: 799999;display:none;" onclick="menu();"></div>
        <div id="menu">
				<?php include("menu_inicio.php"); ?>
        </div>

        <?php include('ventanas.php'); ?>

        <div id="datos_usuario">
            <div style="width:120px; height:160px; float:left; margin:0 10px 0 0">
                <img alt="." src="foto_generador.php?ID=<?php echo $_SESSION['IDUSUARIO'] ?>" width="120" height="160" />
            </div>
            <div style="float:left; width:180px; font-size:12px; margin:15px 0 0 0;"><b><?php echo $_SESSION['NOMBREFUNCIONARIO'] ?></b></div>
            <div style="float:left; width:180px; font-size:11px; margin:0 0 0 0;"><b><?php echo $DatPersonales['cargo'] ?></b></div>
            <div style="float:left; width:180px; font-size:10px; margin:0 0 0 0;"><?php echo $DatPersonales['empresa'] ?></div>
            <div style="float:left; width:180px; font-size:10px; border-bottom: 1px solid #808080; padding: 0 0 5px 0;"><?php echo $DatPersonales['sucursal'] ?></div>

            <?php  if($DatPersonales['id_empresa'] != $_SESSION['EMPRESA'] || $DatPersonales['id_sucursal'] != $_SESSION['SUCURSAL']){ ?>
                <div style="float:left; width:180px; font-size:11px; margin:5px 0 0 0;"><b>Validado en</b></div>
                <div style="float:left; width:180px; font-size:10px; margin:0px 0 0 0;"><?php echo $_SESSION['NOMBREEMPRESA'] ?></div>
                <div style="float:left; width:180px; font-size:10px"><?php echo $_SESSION['NOMBRESUCURSAL'] ?></div>
            <?php } ?>
       </div>

    </body>
</html>
<script type="text/javascript">
    <?php
        if ($_SESSION['ROL']<>'1') {
            include('actualizacion_user_name.php');
        }
    ?>
	function cierra_preloading(){
		document.getElementById('preloading0').style.top  = '-5000'
		document.getElementById('preloading1').style.top  = '-5000'
		document.getElementById('preloading0').style.left = '-5000'
		document.getElementById('preloading1').style.left = '-5000'

		//========================= ALERTA VENCIMIENTO PLAN ========================//
	  if(localStorage.fecha_vencimiento_plan != "<?php echo $_SESSION['PLAN_FECHA_VENCIMIENTO'] ?>"){
	    localStorage.mostrar_recordatorio         = 'true';
	    localStorage.mostrar_recordatorio_vencido = 'true';
	    localStorage.fecha_vencimiento_plan       = "<?php echo $_SESSION['PLAN_FECHA_VENCIMIENTO'] ?>";
	  }

	  if(dias_plan_erp < 7 && dias_plan_erp > 0 && localStorage.mostrar_recordatorio == 'true'){
	    // alertify.message("<span style='font-size:13px;' id='mensaje_plan_erp'><b>Recordatorio</b><br>Su plan esta proximo a vencer Le restan "+dias_plan_erp+" dias de uso en su plan</span>",0);
	    alertify.message("<span style='font-size:13px;' id='mensaje_plan_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div><br>Su plan esta proximo a vencer<br>Fecha vencimiento: <?php echo $_SESSION['PLAN_FECHA_VENCIMIENTO'] ?> <br><img onClick='check_alert_plan(true,\"recordatorio\")' id='img_alert_plan' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
	    document.getElementById('mensaje_plan_erp').parentNode.setAttribute('style','margin-top:-130px;');
	  }
	  else if(dias_plan_erp == 0 && localStorage.mostrar_recordatorio == 'true'){
	    // alertify.warning("<span style='font-size:13px;' id='mensaje_plan_erp'><b>Recordatorio</b><br>Su plan esta proximo a vencer<br><i>No mostrar otra vez</i></span>",0);
	    alertify.message("<span style='font-size:13px;' id='mensaje_plan_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div><br>Su plan esta proximo a vencer<br>Fecha vencimiento: <?php echo $_SESSION['PLAN_FECHA_VENCIMIENTO'] ?> <br><img onClick='check_alert_plan(true,\"recordatorio\")' id='img_alert_plan' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
	    document.getElementById('mensaje_plan_erp').parentNode.setAttribute('style','margin-top:-130px;background-color: khaki;');
	  }
	  else if(dias_plan_erp > -10 && dias_plan_erp < 0 && localStorage.mostrar_recordatorio_vencido == 'true'){
	    alertify.error(" <span style='font-size:13px;' id='mensaje_plan_erp'><b>Plan Vencido</b> <div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div> <br>Su fecha de corte expiro hace "+(dias_plan_erp*-1)+" dia(s)<br><img onClick='check_alert_plan(true,\"vencido\")' id='img_alert_plan' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
	    document.getElementById('mensaje_plan_erp').parentNode.setAttribute('style','margin-top:-130px;');
	  }
	  else if(dias_plan_erp < 0 && dias_plan_erp < -10){
	    alertify.alert('<span style="font-size:13px;">Ha exedido el tiempo de uso de su plan</span>').setHeader('<span style="font-size:13px;">Plan vencido!</span>');
	  }

    //OBTENEMOS EL JSON GUARDADO
    var jsonSave = localStorage.getItem('localResolucionFecha');

    //PREGUNTAMOS SI EL JSON ESTA VACIO O NO
    if(jsonSave == null){
      var json = '{ "<?php echo $_SESSION['NITEMPRESA']; ?>" : { "<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" : "<?php echo $resolucion_activa; ?>" } }';
      var obj = JSON.parse(json);
      localStorage.setItem('localResolucionFecha', JSON.stringify(obj));
    }
    else{
        var jsonParse = JSON.parse(jsonSave);

        // // console.log(jsonParse);
        // for(var keyNit in jsonParse){
        //     var empresa =  jsonParse[keyNit];
        //     // empresa.cali="nonas2";
        //     for(var sucursal in empresa){
        //         // console.log(empresa[sucursal]);
        //         if(keyNit.indexOf('<?php echo $_SESSION['NITEMPRESA']; ?>') < 0){
        //             // console.log('empresa no existe');
        //             jsonParse['<?php echo $_SESSION['NITEMPRESA']; ?>'] = { "<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" : "<?php echo $resolucion_activa; ?>" };
        //             localStorage.setItem('localResolucionFecha', JSON.stringify(jsonParse));
        //             console.log(empresa);
        //             if(sucursal.indexOf('<?php echo $_SESSION['NOMBRESUCURSAL']; ?>') < 0){
        //                 console.log('sucursal no existe');
        //
        //                 //
        //                 //   console.log('se anade una nueva sucursal');
        //                 //
        //                 //   jsonParse['<?php echo $_SESSION['NITEMPRESA']; ?>'] = [{ "<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" : "<?php echo $resolucion_activa; ?>" }];
        //                 //   localStorage.setItem('localResolucionFecha', JSON.stringify(jsonParse));
        //                 //
        //             }
        //         }
        //         else{
        //             console.log('empresa existe');
        //         }
        //     }
        // }

        for(var keyNit in jsonParse){
            if(!jsonParse.hasOwnProperty('<?php echo $_SESSION['NITEMPRESA']; ?>')){
                console.log('Empresa No Existe');
                jsonParse['<?php echo $_SESSION['NITEMPRESA']; ?>'] = { "<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" : "<?php echo $resolucion_activa; ?>" };
                localStorage.setItem('localResolucionFecha', JSON.stringify(jsonParse));
            }
            else{
                console.log('Empresa Existe');
                console.log(keyNit);
            }
        }
    }

    // //========================= ALERTA FECHA RESOLUCION ========================//
		// if(localStorage.fecha_resolucion_fin != "<?php echo $fecha_resolucion_fin; ?>" && "<?php echo $resolucion_activa; ?>" == "si"){
		// 	localStorage.fecha_resolucion_fin = "<?php echo $fecha_resolucion_fin; ?>";
		// 	localStorage.mostrar_recordatorio_fecha = 'true';
		// }
    // else{
    //   localStorage.mostrar_recordatorio_fecha = 'false';
    // }
    //
		// if(<?php echo $fecha_diferencia; ?> <= 7 && <?php echo $fecha_diferencia; ?> >= 1 && localStorage.mostrar_recordatorio_fecha == 'true'){
		// 	alertify.message("<span style='font-size:13px;' id='mensaje_resolucion_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div><br>Su resolucion esta proxima a vencerse.<br>Fecha vencimiento: <?php echo $fecha_resolucion_fin ?><br><img onClick='check_alert_plan(true,\"fecha\")' id='img_alert_resolucion_fecha' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
		//   document.getElementById('mensaje_resolucion_erp').parentNode.setAttribute('style','margin-top:-130px;');
		// }
		// else if(<?php echo $fecha_diferencia; ?> == 0 && localStorage.mostrar_recordatorio_fecha == 'true'){
		// 	alertify.message("<span style='font-size:13px;' id='mensaje_resolucion_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div><br>Su resolucion se ha vencido.<br><img onClick='check_alert_plan(true,\"fecha\")' id='img_alert_resolucion_fecha' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
		//   document.getElementById('mensaje_resolucion_erp').parentNode.setAttribute('style','margin-top:-130px;');
		// }
		// else if(<?php echo $fecha_diferencia; ?> < 0 && localStorage.mostrar_recordatorio_fecha == 'true'){
		// 	alertify.message("<span style='font-size:13px;' id='mensaje_resolucion_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div><br>Su resolucion vencio hace <?php echo "$texto_numero $texto_dia" ?>.<br><img onClick='check_alert_plan(true,\"fecha\")' id='img_alert_resolucion_fecha' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
		//   document.getElementById('mensaje_resolucion_erp').parentNode.setAttribute('style','margin-top:-130px;');
		// }
    //
		// //===================== ALERTA CONSECUTIVOS RESOLUCION =====================//
		// if(localStorage.numero_final_resolucion != "<?php echo $numero_final_resolucion; ?>" && "<?php echo $resolucion_activa; ?>" == "si"){
		// 	localStorage.numero_final_resolucion = "<?php echo $numero_final_resolucion; ?>";
		// 	localStorage.mostrar_recordatorio_resolucion = 'true';
		// }
    // else{
    //   localStorage.mostrar_recordatorio_resolucion = 'false';
    // }
    //
		// if(<?php echo $resolucion_diferencia; ?> <= 50 && <?php echo $resolucion_diferencia; ?> >= 1 && localStorage.mostrar_recordatorio_resolucion == 'true'){
		// 	alertify.message("<span style='font-size:13px;' id='mensaje_numero_factura_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;'>X</div><br>Los consecutivos de la resolucion, estan proximos a acabarse.<br>Solo quedan <?php echo "$resolucion_diferencia $texto_consecutivo" ?><br><img onClick='check_alert_plan(true,\"resolucion\")' id='img_alert_resolucion_consecutivo' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
		//   document.getElementById('mensaje_numero_factura_erp').parentNode.setAttribute('style','margin-bottom:27px;');
		// }
		// else if(<?php echo $resolucion_diferencia; ?> == 0 && localStorage.mostrar_recordatorio_resolucion == 'true'){
		// 	alertify.message("<span style='font-size:13px;' id='mensaje_numero_factura_erp'><b>Recordatorio</b><div style='float:right;margin-top: -10;font-weight: bold;cursor:hand;' >X</div><br>Su resolucion se ha quedado sin consecutivos.<br><img onClick='check_alert_plan(true,\"resolucion\")' id='img_alert_resolucion_consecutivo' src='LOGICALERP/informes/img/checkox_false.png' style='cursor:hand;'><i>No mostrar otra vez</i></span>",0);
		//   document.getElementById('mensaje_numero_factura_erp').parentNode.setAttribute('style','margin-bottom:27px;');
		// }
	}

	function check_alert_plan(estado,opc){
    //SELECCIONAR EL CHECKBOX
	  var new_img = (estado == true)? 'LOGICALERP/informes/img/checkox_true.png' : 'LOGICALERP/informes/img/checkox_false.png';

	  if(opc == 'recordatorio'){
      var img_alert_plan = document.getElementById('img_alert_plan');
  	  img_alert_plan.src = new_img;
      localStorage.mostrar_recordatorio = 'false';
    }
	  if(opc == 'vencido'){
      var img_alert_plan = document.getElementById('img_alert_plan');
  	  img_alert_plan.src = new_img;
      localStorage.mostrar_recordatorio_vencido = 'false';
    }
		if(opc == 'fecha'){
      var img_alert_resolucion_fecha = document.getElementById('img_alert_resolucion_fecha');
  	  img_alert_resolucion_fecha.src = new_img;
      localStorage.mostrar_recordatorio_fecha = 'false';
    }
		if(opc == 'resolucion'){
      var img_alert_resolucion_consecutivo = document.getElementById('img_alert_resolucion_consecutivo');
  	  img_alert_resolucion_consecutivo.src = new_img;
      localStorage.mostrar_recordatorio_resolucion = 'false';
    }
	}

//CARGA TODAS LAS VARIABLES GLOBALES ---------------------------------------------------------
var ubicacion = '<?php echo $CONF_BARRATAREAS_UBICACION ?>'; //VARIABLE QUE DETERMINA SI LA BARRA DE TAREAS VA ABAJO O ARRIBA (ARCHIVO CONFIGURACION PANALLA)
var ventanas = 0;//VARIABLE QUE CONTROLA EL NUMERO DE VENTANAS ABIERTAS
var contexmenu = 0; //VARIABLE QUE CONTROLA SI EL CONTEXMENU ESTA ABIERTO O NO (PARA CERRARLOS CON EL CLIK NORMAL SOBRE EL ESCRITORIO)
var menu_inicio = 0; //VARIABLE QUE CONTROLA SI EL MENU DE INICIO ESTA ABIERTO O NO (PARA CERRARLOS CON EL CLIK NORMAL SOBRE EL ESCRITORIO)
var estado_menu = 'nada';//VARIABLE QUE CONTROLA EL ESTADO DEL MENU (TRUE = ABIERTO , FALSE = CERRADO)
var z_index = 100;//VARIABLE QUE CONTROLA EL Z_INDEX DE LAS CAPAS PARA SABER CUAL QUEDA ARRIBA
var num_icos = '<?php echo mysql_num_rows($consulescritorio)?>'; // MUESTRA EL NUMERO DE ICONOS DISTRIBUIR Y GENERAR EN EL ESCRITORIO
var num_icos2 = '<?php echo mysql_num_rows($consul1)?>'; // MUESTRA EL NUMERO DE VENTANAS A GENERAR

//CARGA EL PROGRAMA DE MANEJO DE TODAS LAS CAPAS ---------------------------------------------
page=new lib_doc_size()
obj_inicio=new lib_obj('inicio')//BOTON DE INICIO
obj_barra=new lib_obj('barra')//BARRA DE TAREAS
obj_menu=new lib_obj('menu')//VENTANA DEL COMERCIAL - MINIMIZADA
//obj_logo=new lib_obj('logo')//CAPA QUE CONTIENE EL LOGO
//obj_inicio_rapido=new lib_obj('inicio_rapido')//CAPA QUE CONTIENE LOS ICONOS DE INICIO RAPIDO

//--------------------------------------------------------------------------------------------
var v_obj = [<?php while(mysql_fetch_array($consul9)){echo'0,';}?>] ;// VARIABLE QUE CONTROLA CUALES VENTANAS ESTAN ABIERTAS
var v_obj2 = [<?php	while(mysql_fetch_array($consul13)){echo'0,';}?>] ;// VARIABLE QUE CONTROLA CUALES VENTANAS ESTAN ABIERTAS (PARA MINIMIZAR DESDE LOS BOTONES DE LA BARRA DE TAREAS)
var obj = [<?php $count5 = 0; while(mysql_fetch_array($consul5)){echo'obj_ventana=new lib_obj(\'ventana'.$count5.'\'),';$count5 ++;}?>]//VENTANAS
var obj_m = [<?php $count7 = 0; while(mysql_fetch_array($consul7)){echo'obj_ventana_m=new lib_obj(\'ventana_m'.$count7.'\'),';$count7 ++;}?>]//VENTANAS - BARRA TAREAS
var obj_iconos = [<?php while($row3 = mysql_fetch_array($consul3)){echo'obj_icono=new lib_obj(\'icono'.$row3['id'].'\'),';}?>]//CAPAS QUE MUESTRAN LOS ICONOS DE ESCRITORIO
var nombre_botones_btareas = new Array(<?php $cont1 = mysql_num_rows($consul14) - 1; $cont2 = 0; while($row14 = mysql_fetch_array($consul14)){ echo '"'.substr($row14['nombre'],0,18); if(strlen($row14['nombre'])>18){echo '...';} echo '"';  if($cont2 < $cont1){echo ",";}	$cont2++;}?>);//NOMBRE DE LOS BOTONES

//---------------------------------------------------------------------------------------------
//FUNCION QUE ORGANIZA Y DISTRIBUYE LOS ICONOS SEGUN EL TAMANO DE LA VENTANA
function organiza_iconos(){
    var n_iconos = num_icos;
    var Tam = TamVentana();
    var lineas = Math.floor((Tam[1])/125);
    var columnas = Math.round(n_iconos/columnas);
    var col = 0;
    if(ubicacion == 'abajo'){var lin = 0;}
    if(ubicacion == 'arriba'){var lin = <?php echo $CONF_BOTONINICIO_ALTO ?> + 5;}
    var contador = 0;
    var contador_col = lineas;

	for (a=0; a<n_iconos; a++){
		var contador = contador + 1;
		obj_iconos[a].moveIt(col,lin);
		lin = lin + 120
			if(contador == contador_col){
				if(ubicacion == 'abajo'){var lin = 0;}
				if(ubicacion == 'arriba'){var lin = <?php echo $CONF_BOTONINICIO_ALTO ?> + 5;}
				col = col + 120;
				contador_col = contador_col + lineas;
			}
	}
}
//-----------------------------------------------------------------------------------------------
//FUNCION QUE SE EJECUA CADA QE HAY UN CAMBIO EN EL REDIMENSIONAMIENTO DE LA VENTANA PRINCIPAL
function comun(){
	 organiza_iconos();
	 if(document.getElementById('IFR_CONTENIDO0')){
	 	if(document.getElementById('IFR_CONTENIDO0').contentWindow.dimensionar_generador_de_dias){
		 	document.getElementById('IFR_CONTENIDO0').contentWindow.dimensionar_generador_de_dias();
		}
	 }
	 var Tam = TamVentana()

	 if(ubicacion == 'abajo'){
		 obj_inicio.moveIt(0,Tam[1]-<?php echo $CONF_BOTONINICIO_ALTO ?>);//UBICA EL BOTON DE INCIO
		 obj_barra.moveIt(<?php echo $CONF_BOTONINICIO_ANCHO ?>,Tam[1]-<?php echo $CONF_BARRATAREAS_ALTO ?>);//UBICA LA BARRA DE TAREAS
		// obj_inicio_rapido.moveIt(<?php echo $CONF_BOTONINICIO_ANCHO ?>+3,(Tam[1]-<?php echo $CONF_BARRATAREAS_ALTO ?>)+2);//UBICA EL ICONOS DE INICIO RAPIDO/
	 }
	 if(ubicacion == 'arriba'){
		 obj_inicio.moveIt(0,0);//UBICA EL BOTON DE INCIO
		 obj_barra.moveIt(<?php echo $CONF_BOTONINICIO_ANCHO ?>,0);//UBICA LA BARRA DE TAREAS
		 //obj_inicio_rapido.moveIt(<?php echo $CONF_BOTONINICIO_ANCHO ?>+3,2);//UBICA EL ICONOS DE INICIO RAPIDO
	 }
	 document.getElementById("barra").style.width=Tam[0]-<?php echo $CONF_BOTONINICIO_ANCHO ?>;//DETERMINA EL TAMANO DE LA BARRA DE TAREAS
	 //obj_logo.moveIt(Tam[0]-300,20);//UBICA EL LOGO
	 //document.getElementById('FONDO_ESCRITORIO').style.width = Tam[0];
	 //document.getElementById('FONDO_ESCRITORIO').style.height = Tam[1];

	 <?php
	 //WHILE QUE CONFIGURA LAS DIMENSIONES DE LA PANTALLA Y DEL FRAME DE CONTENIDOS
	 $count8 = 0;
	 while($row8 = mysql_fetch_array($consul8)){
		 if($row8['ancho'] == 0){//DEFINE EL ANCHO AUTOMATICO DE LAS VENTANAS SI NO TIENEN VALOR EN LA BASE DE DATOS
			 echo'document.getElementById("ventana'.$count8.'").style.width=Tam[0]-('.$margen_ventana.'*2);
				  document.getElementById("barra_arriba'.$count8.'").style.width=Tam[0]-('.$margen_ventana.'*2)-('.$CONF_BARRASUPERIOR_ANCHO.'*2);
				  document.getElementById("barra_abajo'.$count8.'").style.width= Tam[0]-('.$margen_ventana.'*2)-('.$CONF_BARRAINFERIOR_ANCHO.'*2);

				  document.getElementById("contenido'.$count8.'").style.width=Tam[0]-('.$margen_ventana.'*2)-('.$CONF_LATERAL_ANCHO.'*2);
				  document.getElementById("IFR_CONTENIDO'.$count8.'").style.width=Tam[0]-('.$margen_ventana.'*2)-('.$CONF_LATERAL_ANCHO.'*2);
				  ';
		}else{//DEFINE EL ANCHO DE LAS VENTANAS SI TIENEN VALOR EN LA BASE DE DATOS
			echo'document.getElementById("ventana'.$count8.'").style.width='.$row8['ancho'].'-('.$margen_ventana.'*2);
				 document.getElementById("barra_arriba'.$count8.'").style.width='.$row8['ancho'].'-('.$margen_ventana.'*2)-('.$CONF_BARRASUPERIOR_ANCHO.'*2);
				 document.getElementById("barra_abajo'.$count8.'").style.width= '.$row8['ancho'].'-('.$margen_ventana.'*2)-('.$CONF_BARRAINFERIOR_ANCHO.'*2);

				 document.getElementById("contenido'.$count8.'").style.width='.$row8['ancho'].'-('.$margen_ventana.'*2)-('.$CONF_LATERAL_ANCHO.'*2);
				 document.getElementById("IFR_CONTENIDO'.$count8.'").style.width='.$row8['ancho'].'-('.$margen_ventana.'*2)-('.$CONF_LATERAL_ANCHO.'*2);
				';
		}
		 if($row8['alto'] == 0){//DEFINE EL ALTO AUTOMATICO DE LAS VENTANAS SI NO TIENEN VALOR EN LA BASE DE DATOS
			 echo'document.getElementById("ventana'.$count8.'").style.height=Tam[1]-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2);

				  document.getElementById("barra_abajo'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';
				  document.getElementById("esquina_izquierda_abajo'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';
				  document.getElementById("esquina_derecha_abajo'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';
				  //document.getElementById("barra_abajo_content'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';

				  document.getElementById("contenido'.$count8.'").style.height=Tam[1]-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				  document.getElementById("lateral_derecho'.$count8.'").style.height=Tam[1]-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				  document.getElementById("lateral_izqierdo'.$count8.'").style.height=Tam[1]-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				  document.getElementById("IFR_CONTENIDO'.$count8.'").style.height=Tam[1]-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				  ';
		}else{//DEFINE EL ALTO DE LAS VENTANAS SI TIENEN VALOR EN LA BASE DE DATOS
			echo'document.getElementById("ventana'.$count8.'").style.height='.$row8['alto'].'-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2);

				 document.getElementById("barra_abajo'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';
				 document.getElementById("esquina_izquierda_abajo'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';
				 document.getElementById("esquina_derecha_abajo'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';
				 //document.getElementById("barra_abajo_content'.$count8.'").style.height= '.$CONF_BARRAINFERIOR_ALTO.';

				 document.getElementById("contenido'.$count8.'").style.height='.$row8['alto'].'-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				 document.getElementById("lateral_derecho'.$count8.'").style.height='.$row8['alto'].'-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				 document.getElementById("lateral_izqierdo'.$count8.'").style.height='.$row8['alto'].'-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				 document.getElementById("IFR_CONTENIDO'.$count8.'").style.height='.$row8['alto'].'-'.$CONF_BARRATAREAS_ALTO.'-('.$margen_ventana.'*2)-'.$CONF_BARRASUPERIOR_ALTO.'-'.$CONF_BARRAINFERIOR_ALTO.';
				';
		}
		$count8 ++;
	 } ?>
}
//-----------------------------------------------------------------------------------------------------------------------------
//MODIFICA EL REDIMENSIONAMIENTO EN CASO DE CAMBIAR EL TAMA�O DE LA VENTANA
window.onresize = function(){
	comun();
	tareas();
}
//-----------------------------------------------------------------------------------------------------------------------------
//ESTA FUNCION INICIA TODAS LAS CAPAS EN SU POSICION INICIAL
function iniciar(){
	comun();
	obj_menu.moveIt(-2000,-2000);//PONE EL MENU FUERA DEL AREA
	for(b=0; b<num_icos2; b++){obj[b].moveIt(-2000,-2000);obj_m[b].moveIt(-2000,-2000);}//FOR QUE MUEVE TODAS LAS VENTANA Y SU RESPECTIVA BARRA DE TAREAS FUERA DEL AREA
}
//-----------------------------------------------------------------------------------------------------------------------------
// FUNCION DE CERRAR VENTANA ---------------------------------------------------------------------
function cierra(cualv){
	obj[cualv].moveIt(-2000,-2000);
	obj_m[cualv].moveIt(-2000,-2000);
	ventanas = ventanas - 1;
	v_obj[cualv] = 0;
	v_obj2[cualv] = 0;
	tareas();
}
//-----------------------------------------------------------------------------------------------------------------------------
// FUNCION MINIMIZAR VENTANA ---------------------------------------------------------------------
function minimiza(cualv){
	obj[cualv].moveIt(-2000,-2000); //MINIMIZA SACANDO LA VENTANA DEL AREA DE VISTA
	v_obj2[cualv] = 0; //VARIABLE DEL CONTROLADOR DE ESTADO DE LA VENTANA PARA MINIMIZAR DESDE EL BOTON DE BARRA DE TAREAS
	tareas();
}
//-----------------------------------------------------------------------------------------------------------------------------
// FUNCION QUE LLEVA EL CALCULO DE LAS VENTANAS ABIERTAS
function calculos(multi){
	if(multi == 'true'){ventanas = ventanas + 1}
	if(multi == 'false'){ventanas = ventanas - 1}
	if(multi == 'llamar'){return ventanas;}
}
//-----------------------------------------------------------------------------------------------------------------------------
//FUNCION QUE CALCULA SI EL MENU DE INCIO ESTA ABIERTO O NO Y LO ABRE O CIERRA SEGUN EL CASO -----
function vuelve(){menu_inicio = 1;}
function menu(){
var Tam = TamVentana();
if (estado_menu == 'nada'){estado_menu = 'false';}
	if(estado_menu=='false'){
		if(ubicacion == 'abajo' ){obj_menu.moveIt(10,10)};
		 if(ubicacion == 'arriba'){obj_menu.moveIt(0,28)};

        estado_menu = 'true';

        setTimeout('vuelve()',800);
        document.getElementById('verificaMenuInicio').style.display='block';

	}else if(estado_menu =='true'){
		obj_menu.moveIt(-2000,-2000);
		estado_menu ='false';
		menu_inicio = 0;
        document.getElementById('verificaMenuInicio').style.display='none';
	}
}
//-----------------------------------------------------------------------------------------------------------------------------
// FUNCION DE ABRIR VENTANA --------------------------------------------------------------
var VentanaAbierta = new Array();
function abre(cualv,e_menu,url,permiso){
	if(e_menu == 'true'){//IF e_menu ES VERDADERO EJECUTA LA FUNCION DE CERRAR BARRA DE TAREAS
		menu();
	}

	if(ubicacion == 'arriba'){obj[cualv].moveIt(<?php echo $margen_ventana ?>,<?php echo $margen_ventana + $CONF_BARRATAREAS_ALTO ?>);} //BICACION DE LA VENTANA SI LA BARRADE TAREAS ESTA ARRIBA
	if(ubicacion == 'abajo' ){obj[cualv].moveIt(<?php echo $margen_ventana ?>,<?php echo $margen_ventana ?>);} //BICACION DE LA VENTANA SI LA BARRADE TAREAS ESTA ABAJO
	var z_v = 'ventana'+cualv; //CADENA CON EL NOMBRE DE LA VENATANA A MOVER
	document.getElementById(z_v).style.zIndex = z_index + 1;//LE AGREGA UNA UNIDAD AL Z-INDEX DE LA CAPA PARA QUEDE ARRIBA
	z_index = z_index + 1;//LE AGREGA UNA UNIDAD A LA VARIAVLE Z-INDEX QUE CONTROLA LA CAPA QUE QUEDA ARRIBA
		if(v_obj[cualv] == 0){//ESTA FUNCION CONTROLA CON LA VARIABLE VENTANAS Y V_OBJ CUL VENTANA YA ESTA ABIERTA Y CUANTAS VENTANAS ESTAN ABIERTAS
			ventanas = ventanas + 1;//VARIABLE QUE LLEVA EL CALCULO DE VENTANAS ABIERTAS
			v_obj[cualv] = 1;// VARIABLE QUE INDICA SI LA VENTANA ESTA ABIERTA O CERRADA
		}
	v_obj2[cualv] = 1;//VARIABLE QUE INDICA SI LA VENTANA ESTA MINIMIZADA O NO

	if(permiso == 'true'){
		if(VentanaAbierta[cualv] != 'true'){
			document.getElementById('IFR_CONTENIDO'+cualv).src = url+"?autorizado=true";
			$('#IFR_CONTENIDO'+cualv).load(
				function(){
					setTimeout("EliminaDivCarga("+cualv+")",1200);
				}
			);
			VentanaAbierta[cualv] = 'true';
		}
	}

	tareas();

}

function EliminaDivCarga(cual){
	if(document.getElementById('LaVentanaLoading'+cual)){
		document.getElementById('LaVentanaIframe'+cual).style.visibility = "visible";
		var este = document.getElementById('LaVentanaLoading'+cual);
		este.parentNode.removeChild(este);
	}
}
//-----------------------------------------------------------------------------------------------------------------------------
//FUNCION QUE CALCULA LAS TAREAS ABIERTAS Y LAS DISTRIBUYE EN LA BARRA DE TAREAS.
function tareas(){
    var num_tareas = 0;
	for (i=0; i<num_icos2; i++){
	        if(v_obj[i]==1){
			num_tareas = num_tareas + 1;
		}
	}
    var Tam = TamVentana();
    var largo_tareas = Tam[0] - <?php echo $CONF_BOTONINICIO_ANCHO ?> - <?php echo $CONF_INICIORAPIDO_ANCHO ?> - <?php echo $CONF_SYSEMTRAY_ANCHO ?>;
    var division = largo_tareas / num_tareas - 4;
    var num_ventanas1 = '<?php echo mysql_num_rows($consul14)?>'; //CALCULA EL NUMERO DE VENTANAS PARA EL FOR DE LOS NOMBRES DE LOS BOTONES DE LA BARRA DE TAREAS
    if(division >= 150){
	    var tamano_tareas = 150;
    }else{
        var tamano_tareas = division;
    }
	for(i=0;i<num_ventanas1;i++){
		document.getElementById('NOMBRE_VENTANAS_TAREAS'+i).innerHTML = nombre_botones_btareas[i];
        document.getElementById('NOMBRE_VENTANAS_TAREAS'+i).style.width = tamano_tareas - 30;
		//document.getElementById('NOMBRE_VENTANAS_TAREAS'+i).style.visibility = "visible";
	}

    var inicia_en = <?php echo $CONF_BOTONINICIO_ANCHO ?> + <?php echo $CONF_INICIORAPIDO_ANCHO ?> + 4;
	for (a=0; a<num_icos2; a++){
	   	if(v_obj[a]==1){
		    var c_v = 'ventana_m'+a;
			document.getElementById(c_v).style.width = tamano_tareas;
			//if(division <= 150){document.getElementById(c_v).style.fontSize = "9px";}
			//if(division > 150){document.getElementById(c_v).style.fontSize = "12px";}
			if(ubicacion == 'abajo'){obj_m[a].moveIt(inicia_en,Tam[1]-<?php echo $CONF_BARRATAREAS_ALTO ?>+1);}
			if(ubicacion == 'arriba'){obj_m[a].moveIt(inicia_en,1);}
			inicia_en = inicia_en + tamano_tareas + 3;
		}
	}
}
//------------------------------------------------------------------------------------------------------------------------
//FUNCION QUE MUESTRA EL ESCRITORIO
function escritorio(){
	for (h=0; h<num_icos2; h++){
		minimiza(h);
	}
}
//-------------------------------------------------------------------------------------------------------------------------
tareas();
//--------------------------------------------------------------------------------------------------------------------------
//FUNCION QUE DETECTA LA POSICION DEL MOUSE Y DESACTIVA EL CLICK DERECHO, LLAMA LA SIGUIENTE FUNCION QUE MUESTRA UN MENU DE CLICK DERECHO PERSONALIZADO
function disableRightClick(e){
		  if(!document.rightClickDisabled){
				if(document.layers){
					document.captureEvents(Event.MOUSEDOWN);
					document.onmousedown = disableRightClick;
				}else{
					document.oncontextmenu = disableRightClick;
				}
				return document.rightClickDisabled = true;
		  }
 	var posx = 0;
	var posy = 0;

	if(!e){ var e = window.event } //PARA DESACTIVAR ESTA FUNCION COMENTARIAR ESTA LINEA
	posx = (e.pageX) ? e.pageX : window.event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	posy = (e.pageY) ? e.pageY : window.event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	var posicion = {y: posy, x:posx}
	if(document.layers || (document.getElementById && !document.all)){
		if (e.which==2||e.which==3){
			click_derecho(posicion.x,posicion.y)
			return false;
		}
	}else{
		click_derecho(posicion.x,posicion.y)
		return false;
	}
}

//FUNCION QUE MUESTRA UN MENU DE CLICK DERECHO PERSONALIZADO
function click_derecho(X,Y){
	document.getElementById('CLICK_DERECHO').style.top = Y+"px";
	document.getElementById('CLICK_DERECHO').style.left = X+"px";
	contexmenu = 1;
}
//FUNCION QUE DESATIVA EL CONTEXMENU Y EL MENU DE INICIO
function desactiva_contexmenu(e){
	if(contexmenu == 1){ //OCULTA EL CONEXMENU
		document.getElementById('CLICK_DERECHO').style.top = "-2000px";
		document.getElementById('CLICK_DERECHO').style.left = "-2000px";
		contexmenu = 0;
	}
	/*if(menu_inicio == 1){ //OCULTA EL MENU DE INICIO
		menu()
		//estado_menu ='false';
	}*/
}
document.onclick = desactiva_contexmenu;
//-----------------------------------------------------------------------------------------------------------------------------
//FUNCION QUE REINICIA EL ESCRITORIO
// function reiniciar(){

// 	var confir1 = Ext.MessageBox.confirm('Reiniciar escritorio', 'Seguro que desea reiniciar el escritorio?<br /><br />todo trabajo no guardado se perdera!<br />', reinicio);
// 	function reinicio(btn){
// 		if(btn == 'yes'){document.location.reload();}
// 	}
// }

function reiniciar(opcion){

    if(opcion == 'ventana'){
        new Ext.Window({
            title       : 'Reiniciar en otra sucursal',
            id          : 'win_opciones_generales',
            border      : false,
            plain       : true,
            width       : 390,
            height      : 130,
            autoDestroy : true,
            modal       : true,
            autoLoad    :
            {
                url     : 'reiniciar.php',
                scripts : true,
                nocache : true,
                params  : { opcion:'GeneraCombo' }
            }
        }).show();
    }
    else if(opcion == 'reiniciar'){
        var sucursal = document.getElementById('FieldReiniciarEmpresa').value;

        Ext.Ajax.request({
            url     : 'reiniciar.php',
            method  : 'POST',
            params  :
            {
                opcion   :'Reiniciar',
                sucursal : sucursal
            },
            success : function ( result, request ){ document.location.reload(); }
        });
    }
}
//--------------------------------------------------------------------------------------------------------------------------------

//FUNCION PROPIETARIA DEL SIIP QUE REALIZA CAMBIO DE SUCURSAL
function logout(){
	var confir1 = Ext.MessageBox.confirm('Salir de LogicalSoft-ERP', 'Seguro que desea salir de LogicalSoft-ERP ?<br>Todo trabajo no guardado se perdera!', salga);
	function salga(btn){
		if(btn == 'yes'){document.location = "logout.php"}
	}
}
//--------------------------------------------------------------------------------------------------------------------------------


function CambiaClave(){
		CambioDeClave = new Ext.Window
		(
			{
				width		:	300,
				height		:	230,
				title		:	"Cambio de Clave",
				modal		: 	true,
				autoScroll	: 	false,
				autoDestroy : 	true,
				closable	:	false,
				bodyStyle 	: 	'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				autoLoad	:
					{
						url		: 	'CambioDeClave.php',
						scripts	:	true,
						nocache	:	true
					}
			}
		).show();
}

//////////////////////////////////////////// VENTANA DE OPCIONES PERSONALES ////////////////////////////////////////////////////

<?php
	//SCRIPT QUE VERIFICA SI HA ENTRADO CON LA CLAVE PREDETERMINADA -- VALOR TRAIDO DESDE validar.php

	if(isset($_SESSION["ACTUALIZA_PASS"]) && $_SESSION['ACTUALIZA_PASS'] == 'true'){

		echo '
			alert("Problema de Seguridad!\n\n\nUsted se esta validando con el Password asignada por el sistema.\n\nPor favor cambie el Password para poder continuar!.");
 			CambiaClave();
		';
	}
?>

var radar_habilitado = 'true';

function radar(){

    if(!document.getElementById('Ventana_de_pendientes')){
        Ext.Ajax.request({
           url: 'radar.php',
            success : function (result, request)
            {
                if(result.responseText == 'true')
                {
                    if(radar_habilitado == 'true')
                    {
                        win_pendientes = new Ext.Window
                        (
                            {
                                id          : 'Ventana_de_pendientes',
                                width       : 750,
                                height      : 370,
                                closable    : false,
                                //modal     : true,
                                autoDestroy : true,
                                autoLoad    :
                                {
                                    url     :'radar.php',
                                    scripts :true,
                                    nocache :true
                                }
                            }
                        ).show();
                    }
                }else if(result.responseText == 'logged_out')
                {
                    /*SI LA RESPUESTA ES logged_out, SIGNIFICA QUE EL USUARIO DEBE LOGUEARSE Y LO REDIRECCIONA AL FORMULARIO DE INICIO DE SESION*/
                    alert("Su sesi\u00f3n ha finalizado, para continuar utilizando el\nLogicalSoftERP, inicie sesi\u00f3n de nuevo.");
                    ReLogin();
                }
            },
            params: { consulta: 'true' }
        });
    }

    setTimeout('radar()',300000);

}

function abre_radar_manual(){
    if(!document.getElementById('Ventana_de_pendientes')){
        Ext.Ajax.request({
           url: 'radar.php',
            success : function (result, request)
            {
                if(result.responseText == 'true'){
                    win_pendientes = new Ext.Window
                    (
                        {
                            id          : 'Ventana_de_pendientes',
                            width       : 750,
                            height      : 370,
                            closable    : false,
                            //modal     : true,
                            autoDestroy : true,
                            autoLoad    :
                            {
                                url     :'radar.php?check_no_mas=false',
                                scripts :true,
                                nocache :true
                            }
                        }
                    ).show();
                }else{
                    alert('No hay pendientes!');
                }
            },
            params: {
                        consulta     : 'manual',
                    }
        });
    }
}

//VARIABLE GLOBAL QUE DETERMINA SI ESTA O NO HABILITADO EL MICROMODULO DE SEGUIMIENTOS
setTimeout('radar()',300000);
//cambia_color('<?php echo $_SESSION["COLOR_ESCRITORIO"]; ?>','<?php echo $_SESSION["COLOR_MENU"]; ?>');
function cambia_color(arrayStyle){
    if(typeof(arrayStyle)=='undefined' ){
        Ext.Ajax.request({
            url: 'configuracion/cambiar_color_escritorio.php',
            success : function (result, request)
            {
                resultado = result.responseText.split('{.}');

                document.getElementById('inicio').style.backgroundColor   = 'rgba('+resultado[1]+',.80)';
                document.getElementById('barra').style.background         = 'rgba('+resultado[1]+',.80)';
                document.getElementById('datos_usuario').style.background = 'rgba('+resultado[1]+',.80)';
                document.getElementById('menu').style.background = 'rgba('+resultado[1]+',.80)';

                ventana = document.getElementsByClassName('ClassVentana');
                for(i=0;i<ventana.length;i++){
                    if(ventana[i].id !=''){ document.getElementById(ventana[i].id).style.background = 'rgba('+resultado[1]+',.80)'; }
                }
                document.getElementsByTagName('body')[0].style.background = 'rgba('+resultado[0]+',1)';
                //document.getElementsByTagName('body')[0].style.background = 'rgba('+resultado[2]+',1) 0%, rgba('+resultado[0]+',1) 100%)';
            }
        });
    }
    else if(typeof(arrayStyle)=='object'){
        var colorDesktop = arrayStyle.colorFondo;
        var colorMenu = arrayStyle.colorMenu;

        var col = colorDesktop.split(',');
        col[0] = col[0]+50; if(col[0]  > 255){col[0] = 255;}
        col[1] = col[1]+50; if(col[1]  > 255){col[1] = 255;}
        col[2] = col[2]+50; if(col[2]  > 255){col[2] = 255;}

        var degradeDesktop=col[0]+','+col[1]+','+col[2];

        document.getElementById('inicio').style.backgroundColor   = 'rgba('+colorMenu+',.80)';
        document.getElementById('barra').style.background         = 'rgba('+colorMenu+',.80)';
        document.getElementById('datos_usuario').style.background = 'rgba('+colorMenu+',.80)';
        document.getElementById('menu').style.background = 'rgba('+colorMenu+',.80)';

        ventana = document.getElementsByClassName('ClassVentana');
        for(i=0;i<ventana.length;i++){
            if(ventana[i].id !=''){ document.getElementById(ventana[i].id).style.background = 'rgba('+colorMenu+',.80)'; }
        }
        document.getElementsByTagName('body')[0].style.background = 'rgba('+colorDesktop+',1)';
    }
}

/*function radar2(){// RADAR DE ACTUALIZACIONES //
	Ext.Ajax.request({
	   url: 'radar2.php',
		success	: function (result, request)
		{
			if(result.responseText == 'true'){
				alert('El sistema acaba de ser actualizado!\n\nPor favor reinicie el sistema lo mas pronto posible para que los cambios realizados surtan efecto\nPor favor evite realizar transacciones hasta que complete el reinicio del sistema.')
			}
		}
	});

	setTimeout('radar2()',300000);
}

setTimeout('radar2()',300000);
*/

function ReLogin(){
	location.href="/index.php";
}

</script>
<input id="myempresa" name="myempresa" type="hidden" value="<?php echo $_SESSION['EMPRESA']?>">
