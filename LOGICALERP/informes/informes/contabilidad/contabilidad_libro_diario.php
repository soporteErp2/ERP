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

	$id_empresa          = $_SESSION['EMPRESA'];
	$id_sucursal_default = $_SESSION['SUCURSAL'];

	$informe->InformeName			=	'contabilidad_libro_diario';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Libro Diario'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	// EDIT CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS
	$informe->InformeTamano 		= 	"CARTA-HORIZONTAL";

	$informe->AreaInformeQuitaAncho = 	0;
	$informe->AreaInformeQuitaAlto  = 	190;

	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_balance_prueba');


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
	contTercero     = 1;
	arraytercerosBC = new Array();

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_balance_prueba = new Ext.Window({
		    width       : 500,
		    height      : 400,
		    id          : 'Win_Ventana_configurar_balance_prueba',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventanaLibroDiario',
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
                                params  : { opc  : 'libro_diario' }
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

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalLibroDiario  = "";
		localStorage.MyInformeFiltroFechaInicioLibroDiario = "";
		localStorage.sucursal_libro_diario                 = "";
		localStorage.clase_cuenta_libro_diario             = "";
		localStorage.nivel_cuentas_libro_diario            = "";
		localStorage.cuenta_inicial_libro_diario           = "";
		localStorage.cuenta_final_libro_diario             = "";
		Win_Ventana_configurar_balance_prueba.close();
		ventanaConfigurarInforme();

	}

	//==========================// PDF Y EXCEL PRINCIPAL //==========================//
	//*******************************************************************************//

	function generarPDF_Excel_principal(tipo_documento){

		var fecha         = new Date();
		var dia           = fecha.getDate();
		var mes           = fecha.getMonth()+1;
		var anio          = fecha.getFullYear();
		var sucursal      = '';
		var clase_cuenta  = '';
		var nivel_cuentas = '';

		if (typeof(localStorage.MyInformeFiltroFechaInicioLibroDiario)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalLibroDiario)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioLibroDiario!="" && localStorage.MyInformeFiltroFechaFinalLibroDiario!="") {
				//VARIABLES CON EL RANGO DE FECHAS
				var MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioLibroDiario;
				var MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalLibroDiario;

			}else{
				//VARIABLES CON EL RANGO DE FECHAS
				var MyInformeFiltroFechaInicio = anio+"-01-01";
				var MyInformeFiltroFechaFinal  = anio+"-"+mes+"-"+dia;
			}
		}
		else{
			//VARIABLES CON EL RANGO DE FECHAS
			var MyInformeFiltroFechaInicio = anio+"-01-01";
			var MyInformeFiltroFechaFinal  = anio+"-"+mes+"-"+dia;
		}

		if (typeof(localStorage.sucursal_libro_diario)!="undefined" ) {
			if (localStorage.sucursal_libro_diario!='') {
				sucursal=localStorage.sucursal_libro_diario;
			}
		}

		if (typeof(localStorage.clase_cuenta_libro_diario)!="undefined" ) {
			if (localStorage.clase_cuenta_libro_diario!='') {
				clase_cuenta=localStorage.clase_cuenta_libro_diario;
			}
		}

		if (typeof(localStorage.nivel_cuentas_libro_diario)!="undefined" ) {
			if (localStorage.nivel_cuentas_libro_diario!='') {
				nivel_cuentas=localStorage.nivel_cuentas_libro_diario;
			}
		}

		var whereRangoCuentas = '';
		if (typeof(localStorage.cuenta_inicial_libro_diario)!="undefined" && typeof(localStorage.cuenta_final_libro_diario)!="undefined" ) {
			if (localStorage.cuenta_inicial_libro_diario!="" && localStorage.cuenta_final_libro_diario!="") {
				whereRangoCuentas= ' AND (codigo_cuenta >= "'+localStorage.cuenta_inicial_libro_diario +'" AND codigo_cuenta <= "'+localStorage.cuenta_final_libro_diario +'" OR codigo_cuenta LIKE "'+localStorage.cuenta_inicial_libro_diario +'{.}" OR codigo_cuenta LIKE "'+localStorage.cuenta_final_libro_diario +'{.}")';
			}
		}

		var data = tipo_documento+"=true"
					+"&nombre_informe=Libro Diario"
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&sucursal="+sucursal
					+"&clase_cuenta="+clase_cuenta
					+"&nivel_cuentas="+nivel_cuentas
					+"&whereRangoCuentas="+whereRangoCuentas

		window.open("../informes/informes/contabilidad/contabilidad_libro_diario_Result.php?"+data);
	}

	function generarHtml(){

		var sucursal                   = document.getElementById('filtro_sucursal_libro_diario').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var clase_cuenta               = document.getElementById('clase_cuenta').value;
		var nivel_cuentas              = document.getElementById('nivel_cuentas_libro_diario').value;
		var whereRangoCuentas          = '';
		var cuenta_inicial             = document.getElementById('cuenta_inicial');
		var cuenta_final               = document.getElementById('cuenta_final');

		if (cuenta_inicial.value!="" && cuenta_final.value!="") {
			whereRangoCuentas=' AND (codigo_cuenta >= "'+cuenta_inicial.value+'" AND codigo_cuenta <= "'+cuenta_final.value+'" OR codigo_cuenta LIKE "'+cuenta_inicial.value+'{.}" OR codigo_cuenta LIKE "'+cuenta_final.value+'{.}")';
		}
		else if (cuenta_inicial.value!="" || cuenta_final.value!="") {
			alert("Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
			return;
		}

		Ext.get('RecibidorInforme_contabilidad_libro_diario').load({
			url     : '../informes/informes/contabilidad/contabilidad_libro_diario_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Libro Diario',
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				clase_cuenta               : clase_cuenta,
				nivel_cuentas              : nivel_cuentas,
				whereRangoCuentas          : whereRangoCuentas,
				sucursal                   : sucursal,
			}
		});

		localStorage.MyInformeFiltroFechaFinalLibroDiario  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioLibroDiario = MyInformeFiltroFechaInicio;
		localStorage.sucursal_libro_diario                 = sucursal;
		localStorage.clase_cuenta_libro_diario             = clase_cuenta;
		localStorage.nivel_cuentas_libro_diario            = nivel_cuentas;
		localStorage.cuenta_inicial_libro_diario           = cuenta_inicial.value;
		localStorage.cuenta_final_libro_diario             = cuenta_final.value;

		document.getElementById("RecibidorInforme_contabilidad_libro_diario").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){

		var sucursal                   = document.getElementById('filtro_sucursal_libro_diario').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var clase_cuenta               = document.getElementById('clase_cuenta').value;
		var nivel_cuentas              = document.getElementById('nivel_cuentas_libro_diario').value;
		var whereRangoCuentas          = '';
		var cuenta_inicial             = document.getElementById('cuenta_inicial');
		var cuenta_final               = document.getElementById('cuenta_final');

		if (cuenta_inicial.value!="" && cuenta_final.value!="") {
			whereRangoCuentas=' AND (codigo_cuenta >= "'+cuenta_inicial.value+'" AND codigo_cuenta <= "'+cuenta_final.value+'" OR codigo_cuenta LIKE "'+cuenta_inicial.value+'{.}" OR codigo_cuenta LIKE "'+cuenta_final.value+'{.}")';
		}
		else if (cuenta_inicial.value!="" || cuenta_final.value!="") {
			alert("Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
			return;
		}

		var data = tipo_documento+"=true"
								+"&nombre_informe=Libro Diario"
								+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
								+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
								+"&sucursal="+sucursal
								+"&clase_cuenta="+clase_cuenta
								+"&nivel_cuentas="+nivel_cuentas
								+"&whereRangoCuentas="+whereRangoCuentas


		window.open("../informes/informes/contabilidad/contabilidad_libro_diario_Result.php?"+data);
	}

	//================ VENTANA PARA BUSCAR LA CUENTA DEL PUC PARA EL RANGO DE CUENTAS ===========//
	function ventanaBuscarCuentaPucLibroDiario(campo){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_cuenta_puc_libro_diario = new Ext.Window({
		    width       : 680,
		    height      : 500,
		    id          : 'Win_Ventana_buscar_cuenta_puc_libro_diario',
		    title       : 'Consultar la cuenta del Puc',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombreGrilla : 'buscarCuentaBalancePrueba',
					cargaFuncion : 'renderizaResultadoVentanaPuc(id,"'+campo+'")',
					tabla_puc    : 'puc',
		        }
		    },
		    tbar        :
		    [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_cuenta_puc_libro_diario.close() }
                }
		    ]
		}).show();
	}

	//================== RENDERIZAR LOS RESULTADOS DE LA BUSQUEDA DE LA CUENTA =============================//
	function renderizaResultadoVentanaPuc(id,campo){
		input       = document.getElementById(campo);
		input.value = document.getElementById('div_buscarCuentaBalancePrueba_cuenta_'+id).innerHTML;

		input.setAttribute("title",document.getElementById('div_buscarCuentaBalancePrueba_descripcion_'+id).innerHTML);
		Win_Ventana_buscar_cuenta_puc_libro_diario.close();

		input.focus();

	}

	//============== EVENTO KEY UP DE LOS CAMPOS CUENTA =================================//
	function validaCuentaPucLibroDiario (event,input) {
		tecla  = input ? event.keyCode : event.which;
		patron = /[^\d]/g;
        if(patron.test(input.value)){ input.value = (input.value).replace(/[^0-9]/g,''); }

	}

</script>