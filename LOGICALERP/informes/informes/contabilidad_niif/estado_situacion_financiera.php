<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName			=	'estado_situacion_financiera';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Estado Situacion Financiera'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principalEstadoSituacionFinancieraNiif("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principalEstadoSituacionFinancieraNiif("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInformeEstadoSituacionFinancieraNiif()','Btn_configurar_balance_general');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	// CHANGE CSS
	$informe->DefaultCls               = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar            = 	80; 		//HEIGHT TOOLBAR

	$array= '["Grupos","Grupos"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"],["Auxiliares","Auxiliares"]';

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

?>

<script>

	function generarPDF_Excel_principalEstadoSituacionFinancieraNiif(tipo_documento){

		if (typeof(localStorage.tipo_balance_estado_situacion_financiera)!="undefined") {
			if (localStorage.tipo_balance_estado_situacion_financiera!="") {
				generar=localStorage.generar_estado_situacion_financiera;
				MyInformeNiifFechaFinal=localStorage.MyInformeNiifFechaFinal;

				if (localStorage.tipo_balance_estado_situacion_financiera=='comprobacion') { var MyInformeNiifFechaInicio=''; }
				else if (localStorage.tipo_balance_estado_situacion_financiera=='comparativo') { var MyInformeNiifFechaInicio=localStorage.MyInformeNiifFechaInicio; }
				window.open("../informes/informes/contabilidad_niif/estado_situacion_financiera_Result.php?"+tipo_documento+"=true&nombre_informe=Balance General&generar="+generar+"&MyInformeNiifFechaFinal="+MyInformeNiifFechaFinal+"&MyInformeNiifFechaInicio="+MyInformeNiifFechaInicio+"&tipo_balance="+localStorage.tipo_balance_estado_situacion_financiera+"&mostrar_cuenta_niif="+localStorage.mostrar_cuentas_estado_situacion_financiera);
			}
			else{ window.open("../informes/informes/contabilidad_niif/estado_situacion_financiera_Result.php?"+tipo_documento+"=true&nombre_informe=Balance General&$tipo_balance==comprobacion&mostrar_cuenta_niif="+localStorage.mostrar_cuentas_estado_situacion_financiera); }
		}
		else{ window.open("../informes/informes/contabilidad_niif/estado_situacion_financiera_Result.php?"+tipo_documento+"=true&nombre_informe=Balance General&$tipo_balance==comprobacion&mostrar_cuenta_niif="+localStorage.mostrar_cuentas_estado_situacion_financiera); }

	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInformeEstadoSituacionFinancieraNiif(){

		Win_Ventana_configurar_estado_situacion_financiera = new Ext.Window({
		    width       : 400,
		    height      : 350,
		    id          : 'Win_Ventana_configurar_estado_situacion_financiera',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad_niif/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventanaConfiguracionSituacionFinanciera',
		        }
		    },
		    tbar        :
		    [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Generar<br>Informe',
                    scale       : 'large',
                    iconCls     : 'genera_informe',
                    iconAlign   : 'top',
                    handler     : function(){ generarHtmlEstadoSituacionFinancieraNiif() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelEstadoSituacionFinancieraNiif('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelEstadoSituacionFinancieraNiif('IMPRIME_XLS') }
                },'-',
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Reiniciar<br>Filtros',
                    scale       : 'large',
                    iconCls     : 'restaurar',
                    iconAlign   : 'top',
                    handler     : function(){ resetFiltros() }
                },'-',
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 45,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_configurar_estado_situacion_financiera.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.tipo_balance_estado_situacion_financiera    = "";
		localStorage.generar_estado_situacion_financiera         = "";
		localStorage.MyInformeNiifFechaFinal                     = "";
		localStorage.MyInformeNiifFechaInicio                    = "";
		localStorage.mostrar_cuentas_estado_situacion_financiera = "";
		Win_Ventana_configurar_estado_situacion_financiera.close();
		ventanaConfigurarInformeEstadoSituacionFinancieraNiif();
	}

	function generarHtmlEstadoSituacionFinancieraNiif(){

		var elementos = document.getElementsByName("tipo_balance");

		for(var i=0; i<elementos.length; i++) { if (elementos[i].checked) { var tipo_balance=elementos[i].value; } }

		var generar                 = document.getElementById('nivel_cuenta').value;
		var mostrar_cuentas_niif = (document.getElementById('mostrar_cuentas').checked)? 'true' : '' ;
		var MyInformeNiifFechaFinal = document.getElementById('MyInformeNiifFechaFinal').value;

		if (tipo_balance=='comprobacion') { var MyInformeNiifFechaInicio=''; }
		else if (tipo_balance=='comparativo') { var MyInformeNiifFechaInicio=document.getElementById('MyInformeNiifFechaInicio').value; }
		else{ return; }

		Ext.get('RecibidorInforme_estado_situacion_financiera').load({
			url     : '../informes/informes/contabilidad_niif/estado_situacion_financiera_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe           : 'Balance General',
				tipo_balance             : tipo_balance,
				generar                  : generar,
				MyInformeNiifFechaFinal  : MyInformeNiifFechaFinal,
				MyInformeNiifFechaInicio : MyInformeNiifFechaInicio,
				mostrar_cuenta_niif      : mostrar_cuentas_niif,
			}
		});

		document.getElementById("RecibidorInforme_estado_situacion_financiera").style.padding = 20;

		localStorage.tipo_balance_estado_situacion_financiera    = tipo_balance;
		localStorage.generar_estado_situacion_financiera         = generar;
		localStorage.MyInformeNiifFechaFinal                     = MyInformeNiifFechaFinal;
		localStorage.MyInformeNiifFechaInicio                    = MyInformeNiifFechaInicio;
		localStorage.mostrar_cuentas_estado_situacion_financiera = mostrar_cuentas_niif;


	}

	function generarPDF_ExcelEstadoSituacionFinancieraNiif(tipo_documento){

		var elementos = document.getElementsByName("tipo_balance");

		for(var i=0; i<elementos.length; i++) { if (elementos[i].checked) { tipo_balance=elementos[i].value; } }

		generar=document.getElementById('nivel_cuenta').value;
		MyInformeNiifFechaFinal=document.getElementById('MyInformeNiifFechaFinal').value;

		if (tipo_balance=='comprobacion') { MyInformeNiifFechaInicio=''; }
		else if (tipo_balance=='comparativo') { MyInformeNiifFechaInicio=document.getElementById('MyInformeNiifFechaInicio').value; }
		else{ return; }

		window.open("../informes/informes/contabilidad_niif/estado_situacion_financiera_Result.php?"+tipo_documento+"=true&nombre_informe=Balance General&generar="+generar+"&MyInformeNiifFechaFinal="+MyInformeNiifFechaFinal+"&MyInformeNiifFechaInicio="+MyInformeNiifFechaInicio+"&tipo_balance=comprobacion&tipo_balance="+tipo_balance+"&mostrar_cuenta_niif="+localStorage.mostrar_cuentas_estado_situacion_financiera);

	}


</script>