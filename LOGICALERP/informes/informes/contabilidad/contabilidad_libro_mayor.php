<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE  	  ///**/
	/**/											                      /**/
	/**/          $informe = new MyInforme();       /**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	$informe->InformeName			      =	'contabilidad_libro_mayor'; //NOMBRE DEL INFORME
	$informe->InformeTitle			    =	'Libro Mayor';              //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false';                    //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		  =	'false';                    //FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF	  = "false";	                  //SI EXPORTA A PDF
	$informe->InformeExportarXLS	  = "false";	                  //SI EXPORTA A XLS
	$informe->DefaultCls            = ''; 		                    //RESET STYLE CSS
	$informe->HeightToolbar         = 80;
	$informe->BtnGenera             = 'false'; 		                    //HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_libro_mayor');

	$array= '["Grupos","Grupos"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"],["Auxiliares","Auxiliares"]';

	if($modulo == 'contabilidad'){
		$informe->AreaInformeQuitaAlto = 230;
	}

	/**//////////////////////////////////////////////////////////////**/
	/**///               INICIALIZACION DE LA GRILLA              ///**/
	/**/															                              /**/
	/**/	  $informe->Link = $link;  	    //Conexion a la BD        /**/
	/**/	  $informe->inicializa($_POST); //Variables POST          /**/
	/**/	  $informe->GeneraInforme(); 	  //Inicializa la Grilla    /**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	id_pais = "<?php echo $_SESSION[PAIS]; ?>";
	if(id_pais != 49){
		url_variable = '../informes/informes/contabilidad/contabilidad_libro_mayor_paises_Result.php';
	}
	else{
		url_variable = '../informes/informes/contabilidad/contabilidad_libro_mayor_Result.php';
	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var nivel_cuentas            = document.getElementById('nivel_cuentas_LM').value
		,	sucursal                   = document.getElementById('filtro_sucursal_sucursales_libro_mayor').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	separador_miles            = document.getElementById('separador_milesLM').value
		,	separador_decimales        = document.getElementById('separador_decimalesLM').value
		,	cuentas_cierre             = document.getElementById('cuentas_cierreLM').value

		if(separador_decimales == separador_miles){
			alert("\u00A1Error!\nEl separador de miles y decimales no puede ser igual.");
			return;
		}

		Ext.get('RecibidorInforme_contabilidad_libro_mayor').load({
			url     : url_variable,
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			timeout : 120000,
			params  :	{
									nivel_cuentas              : nivel_cuentas,
									nombre_informe             : 'Libro Mayor',
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									sucursal                   : sucursal,
									separador_miles 		   		 : separador_miles,
									separador_decimales 	   	 : separador_decimales,
									cuentas_cierre       	   	 : cuentas_cierre
								}
		});

		localStorage.nivel_cuentasLM              = nivel_cuentas;
		localStorage.MyInformeFiltroFechaFinalLM  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioLM = MyInformeFiltroFechaInicio;
		localStorage.sucursalLM                   = sucursal;
		localStorage.separador_milesLM            = separador_miles;
		localStorage.separador_decimalesLM        = separador_decimales;
		localStorage.cuentas_cierreLM             = cuentas_cierre;

		document.getElementById("RecibidorInforme_contabilidad_libro_mayor").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var nivel_cuentas            = document.getElementById('nivel_cuentas_LM').value
		,	sucursal                   = document.getElementById('filtro_sucursal_sucursales_libro_mayor').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	separador_miles            = document.getElementById('separador_milesLM').value
		,	separador_decimales        = document.getElementById('separador_decimalesLM').value
		,	cuentas_cierreLM           = document.getElementById('cuentas_cierreLM').value

		if(separador_decimales == separador_miles){
			alert("\u00A1Error!\nEl separador de miles y decimales no puede ser igual.");
			return;
		}

		var data = tipo_documento+"=true"
							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
							+"&sucursal="+sucursal
							+"&nivel_cuentas="+nivel_cuentas
							+"&separador_miles="+separador_miles
							+"&separador_decimales="+separador_decimales
							+"&cuentas_cierre="+cuentas_cierre

		localStorage.nivel_cuentasLM              = nivel_cuentas;
		localStorage.MyInformeFiltroFechaFinalLM  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioLM = MyInformeFiltroFechaInicio;
		localStorage.sucursalLM                   = sucursal;
		localStorage.separador_milesLM            = separador_miles;
		localStorage.separador_decimalesLM        = separador_decimales;
		localStorage.cuentas_cierreLM             = cuentas_cierre;

		window.open(url_variable + "?" + data);
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var nivel_cuentas            = localStorage.nivel_cuentas_LM
		,	sucursal                   = localStorage.sucursalLM
		,	MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioLM
		,	MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalLM
		,	separador_miles            = localStorage.separador_milesLM
		,	separador_decimales        = localStorage.separador_decimalesLM
		,	cuentas_cierre             = localStorage.cuentas_cierreLM


		if(localStorage.MyInformeFiltroFechaInicio == ""){
			alert('Error, debe generar el informe, al menos una primera vez.');
			return;
		}

		var data = tipo_documento+"=true"
							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
							+"&sucursal="+sucursal
							+"&nivel_cuentas="+nivel_cuentas
							+"&separador_miles="+separador_miles
							+"&separador_decimales="+separador_decimales
							+"&cuentas_cierre="+cuentas_cierre

		window.open(url_variable + "?" + data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_balance_prueba = new Ext.Window({
		    width       : 550,
		    height      : 350,
		    id          : 'Win_Ventana_configurar_balance_prueba',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/wizard_libro_mayor.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventanaLM',
		        }
		    },
		    tbar        :
		    [

		        {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Sucursal',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 160,
                            height      : 45,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
                                scripts : true,
                                nocache : true,
                                params  : { opc  : 'sucursales_libro_mayor' }
                            }
                        }
                    ]
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Generar<br>Informe',
                    scale       : 'large',
                    iconCls     : 'genera_informe',
                    iconAlign   : 'top',
                    handler     : function(){ generarHtml() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_XLS') }
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
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_configurar_balance_prueba.close() }
                }
		    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.nivel_cuentasLM              = "";
		localStorage.MyInformeFiltroFechaFinalLM  = "";
		localStorage.MyInformeFiltroFechaInicioLM = "";
		localStorage.sucursalLM                   = "";
		localStorage.separador_milesLM            = "";
		localStorage.separador_decimalesLM        = "";
		localStorage.cuentas_cierreLM             = "";
		Win_Ventana_configurar_balance_prueba.close();
		ventanaConfigurarInforme();
	}
</script>
