<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 		INICIALIZACION DE LA CLASE  	  ///**/
	/**/											                      /**/
	/**/					$informe = new MyInforme();				/**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$id_empresa                     = $_SESSION['EMPRESA'];
	$id_sucursal                    = $_SESSION['SUCURSAL'];
	$informe->InformeName			      =	'contabilidad_balance_general';  //NOMBRE DEL INFORME
	$informe->InformeTitle			    =	'Balance General'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		  =	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->BtnGenera             = 'false';
	$informe->InformeExportarPDF	  = "false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	  = "false";	//SI EXPORTA A XLS
	$informe->DefaultCls            = ''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		//HEIGHT TOOLBAR
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_balance_general');

	$array= '["Grupos","Grupos"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"],["Auxiliares","Auxiliares"]';

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				         INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															                              /**/
	/**/	   $informe->Link = $link;  	    //Conexion a la BD			/**/
	/**/	   $informe->inicializa($_POST);  //Variables POST			  /**/
	/**/	   $informe->GeneraInforme(); 	  //Inicializa la Grilla	/**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	id_pais = "<?php echo $_SESSION[PAIS]; ?>";
	if(id_pais != 49){
		url_variable = '../informes/informes/contabilidad/contabilidad_balance_general_paises_Result.php';
	}
	else{
		url_variable = '../informes/informes/contabilidad/contabilidad_balance_general_Result.php';
	}

	function generarPDF_Excel_principal(tipo_documento){
		var data  = "";

		if(typeof(localStorage.tipo_balance) != "undefined"){

			data = tipo_documento+"=true"	+ "&tipo_balance=clasificado"

			if(localStorage.tipo_balance != ""){
				generar = localStorage.generar;
				MyInformeFiltroFechaFinal = localStorage.MyInformeFiltroFechaFinal;

				if(localStorage.tipo_balance == 'clasificado') { MyInformeFiltroFechaInicio = ''; }
				else if(localStorage.tipo_balance == 'comparativo') { MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicio; }

				data = tipo_documento+"=true"
						+"&generar="+generar
						+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
						+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
						+"&tipo_balance="+localStorage.tipo_balance

				window.open(url_variable + "?" + data);
			}
			else{
				window.open(url_variable + "?" + data);
			}
		}
		else{
			window.open(url_variable + "?" + data);
		}
	}

	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_balance_general = new Ext.Window({
		    width       : 500,
		    height      : 330,
		    id          : 'Win_Ventana_configurar_balance_general',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/wizard_balance_general.php',
		        scripts : true,
		        nocache : true,
		        params  : { opc : 'ventana_configuracion_BC' }
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
                    height      : 45,
                    text        : 'Regresar<br>',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_configurar_balance_general.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){
		localStorage.tipo_balance               = "";
		localStorage.generar                    = "";
		localStorage.MyInformeFiltroFechaFinal  = "";
		localStorage.MyInformeFiltroFechaInicio = "";
		Win_Ventana_configurar_balance_general.close();
		ventanaConfigurarInforme();
	}

	function generarHtml(){
		var tipo_balance             = document.getElementById("tipo_balance").value
		,	generar                    = document.getElementById('nivel_cuenta').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	separador_miles            = document.getElementById('separador_miles').value
		,	separador_decimales        = document.getElementById('separador_decimales').value

		Ext.get('RecibidorInforme_contabilidad_balance_general').load({
			url     : url_variable,
			text	  : 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Balance General',
				tipo_balance               : tipo_balance,
				generar                    : generar,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				separador_miles 		       : separador_miles,
				separador_decimales 	     : separador_decimales,
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_balance_general").style.padding = 20;

		localStorage.tipo_balance               = tipo_balance;
		localStorage.generar                    = generar;
		localStorage.MyInformeFiltroFechaFinal  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicio = MyInformeFiltroFechaInicio;
	}

	function generarPDF_Excel(tipo_documento){
		var tipo_balance             = document.getElementById("tipo_balance").value
		,	generar                    = document.getElementById('nivel_cuenta').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	separador_miles            = document.getElementById('separador_miles').value
		,	separador_decimales        = document.getElementById('separador_decimales').value

		var data = `${tipo_documento}=true
					&tipo_balance=${tipo_balance}
					&generar=${generar}
					&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
					&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
					&separador_miles=${separador_miles}
					&separador_decimales=${separador_decimales}`;

		window.open(url_variable + "?" + data);
	}
</script>
