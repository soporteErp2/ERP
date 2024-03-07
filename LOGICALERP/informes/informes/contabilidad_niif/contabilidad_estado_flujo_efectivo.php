<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	error_reporting(1);
	$id_empresa          = $_SESSION['EMPRESA'];
	$id_sucursal_default = $_SESSION['SUCURSAL'];

	include('../../../../misc/MyInforme/class.MyInforme.php');
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$informe->InformeName			=	'contabilidad_estado_flujo_efectivo';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Estado de Flujo de Efectivo'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	// CHANGE CSS
	$informe->DefaultCls               = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar            = 	80; 		//HEIGHT TOOLBAR

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principalNiif("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principalNiif("IMPRIME_XLS")','Btn_exportar_excel');

	//CONFIGURAR INFORME
	// $informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInformeNiif()','Btn_configurar_cartera');

	// $informe->AddBotton('exportar prueba','add','prueba();','btn_prueba');
	// $informe->AddBotton('prueba','add','prueba();');

	/* COMBOX PERSONALIZADO SUCURSALES*/
	// $querySucursal   = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE activo = 1 AND id_empresa='$id_empresa' ORDER BY nombre",$link);
	// $arraySucursales = '["","Por Empresa"],';
	// while($rowSucursales = mysql_fetch_array($querySucursal)){
	// 	$arraySucursales .= '["'.$rowSucursales['id'].'","'.$rowSucursales['nombre'].'"],';
	// }
	$array= '["Resumido","Resumido"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"]';
	// $informe->AddFiltro('Generar','Seleccione...',trim($array,','),'Resumido');

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

	contCliente=1;

	//================================== GENERAR EL PDF Y EXCEL DESDE LA PAGINA PRINCIPAL ========================================//

	function generarPDF_Excel_principalNiif(tipo_documento){
		var id_centro_costos='';
		//RECORRER LOS CHECKBOX PARA IDENTIFICAR SI SE SELECCIONARON LOS CENTROS DE COSTOS O NO
		if (arrayCentroCostosNiif.length>0) {
			for(i=0;i<arrayCentroCostosNiif.length;i++){
				if (typeof(arrayCentroCostosNiif[i])!="undefined") {
					id_centro_costos+=arrayCentroCostosNiif[i]+',';
				}
			}
		}
		var MyInformeFiltroFechaFinal    = (typeof(localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif)!='undefined')? localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif : '' ;
		var MyInformeFiltroFechaInicio   = (typeof(localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif)!='undefined')? localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif : '' ;
		var tipo_balance_EstadoResultado = (typeof(localStorage.tipo_balance_EstadoResultadoNiif)!='undefined')? localStorage.tipo_balance_EstadoResultadoNiif : 'mensual' ;
		var nivel_cuenta                 = (typeof(localStorage.nivel_cuentas_EstadoResultadoNiif)!='undefined')? localStorage.nivel_cuentas_EstadoResultadoNiif : 'Grupos' ;
		var estado_resultado             = (typeof(localStorage.estado_resultado_niif)!='undefined')? localStorage.estado_resultado_niif : '' ;
		var mostrar_cuentas_niif         = (typeof(localStorage.mostrar_cuentas_niif)!='undefined')? localStorage.mostrar_cuentas_niif : 'false' ;

		window.open("../informes/informes/contabilidad_niif/contabilidad_estado_de_resultado_Result.php?"+tipo_documento+"=true&nombre_informe=Estado de Resultados&tipo_balance_EstadoResultado="+tipo_balance_EstadoResultado+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&generar="+nivel_cuenta+"&id_centro_costos="+id_centro_costos+"&estado_resultado="+estado_resultado+"&mostrar_cuentas_niif="+mostrar_cuentas_niif);

	}


	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//
	function ventanaConfigurarInformeNiif(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_cartera_edades = new Ext.Window({
		    width       : 500,
		    height      : 450,
		    id          : 'Win_Ventana_configurar_cartera_edades',
		    title       : 'Configurar Informe Estado de Resultados',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad_niif/bd.php',
		        scripts : true,
		        // text    : 'cargando...	',
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaEstadoResultado',

		        }
		    },
		    tbar        :
		    [

		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [

		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_configurar_cartera_edades.close() }
		                }
		            ]
		        },
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Generacion de Informe',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Generar Informe',
		                    scale       : 'large',
		                    iconCls     : 'genera_informe',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarHtmlNiif() }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Exportar a PDF',
		                    scale       : 'large',
		                    iconCls     : 'genera_pdf',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarPDF_ExcelNiif('IMPRIME_PDF') }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Exportar a Excel',
		                    scale       : 'large',
		                    iconCls     : 'excel32',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarPDF_ExcelNiif('IMPRIME_XLS') }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function generarHtmlNiif(){
		var nivel_cuenta = document.getElementById('nivel_cuenta').value;
		var elementos = document.getElementsByName("tipo_balance");
		var id_centro_costos='';
		var estado_resultado = document.getElementById('estado_resultado').value;
		var mostrar_cuentas_niif = (document.getElementById('mostrar_cuentas').checked)? 'true' : '' ;

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_balance_EstadoResultado=elementos[i].value;}
		}

		var MyInformeFiltroFechaInicio = '';
		var MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance_EstadoResultado!='rango_fechas') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance_EstadoResultado=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{
			return;
		}

		//RECORRER LOS CHECKBOX PARA IDENTIFICAR SI SE SELECCIONARON LOS CENTROS DE COSTOS O NO
		if (arrayCentroCostosNiif.length>0) {
			for(i=0;i<arrayCentroCostosNiif.length;i++){
				if (typeof(arrayCentroCostosNiif[i])!="undefined") {
					id_centro_costos+=arrayCentroCostosNiif[i]+',';
				}
			}
		}

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.nivel_cuentas_EstadoResultadoNiif             = nivel_cuenta;
		localStorage.tipo_balance_EstadoResultadoNiif              = tipo_balance_EstadoResultado;
		localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif = MyInformeFiltroFechaInicio;
		localStorage.estado_resultado_niif                         = estado_resultado;
		localStorage.mostrar_cuentas_niif                          = mostrar_cuentas_niif;

		Ext.get('RecibidorInforme_contabilidad_estado_de_resultado_niif').load({
			url     : '../informes/informes/contabilidad_niif/contabilidad_estado_de_resultado_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe               : 'Estado de Resultados',
				tipo_balance_EstadoResultado : tipo_balance_EstadoResultado,
				MyInformeFiltroFechaFinal    : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio   : MyInformeFiltroFechaInicio,
				generar                      : nivel_cuenta,
				id_centro_costos             : id_centro_costos,
				estado_resultado             : estado_resultado,
				mostrar_cuentas_niif         : mostrar_cuentas_niif,
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_estado_de_resultado_niif").style.padding = 20;
	}

	function generarPDF_ExcelNiif(tipo_documento){
		var nivel_cuenta = document.getElementById('nivel_cuenta').value;
		var elementos = document.getElementsByName("tipo_balance");
		var id_centro_costos='';
		var estado_resultado = document.getElementById('estado_resultado').value;
		var mostrar_cuentas_niif = (document.getElementById('mostrar_cuentas').checked)? 'true' : '' ;

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_balance_EstadoResultado=elementos[i].value;}
		}

		var MyInformeFiltroFechaInicio = '';
		var MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance_EstadoResultado!='rango_fechas') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance_EstadoResultado=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{
			return;
		}

		//RECORRER LOS CHECKBOX PARA IDENTIFICAR SI SE SELECCIONARON LOS CENTROS DE COSTOS O NO
		if (arrayCentroCostosNiif.length>0) {
			for(i=0;i<arrayCentroCostosNiif.length;i++){
				if (typeof(arrayCentroCostosNiif[i])!="undefined") {
					id_centro_costos+=arrayCentroCostosNiif[i]+',';
				}
			}
		}

		window.open("../informes/informes/contabilidad_niif/contabilidad_estado_de_resultado_Result.php?"+tipo_documento+"=true&nombre_informe=Estado de Resultados&tipo_balance_EstadoResultado="+tipo_balance_EstadoResultado+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&generar="+nivel_cuenta+"&id_centro_costos="+id_centro_costos+"&estado_resultado="+estado_resultado+"&mostrar_cuentas_niif="+mostrar_cuentas_niif);
	}

</script>