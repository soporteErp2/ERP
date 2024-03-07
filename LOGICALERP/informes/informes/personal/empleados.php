<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	       //**/
	/**/																					  /**/
	/**/			 $informe = new MyInforme();				  /**/
	/**/																					  /**/
	/**//////////////////////////////////////////////**/

	$id_empresa  										= $_SESSION['EMPRESA'];
	$id_sucursal 										= $_SESSION['SUCURSAL'];
	$informe->InformeName			    	=	'empleados';   				//NOMBRE DEL INFORME
	$informe->InformeTitle			  	=	'Empleados';   				//TITULO DEL INFORME
	$informe->BtnGenera							= 'false';							//BOTON PARA GENERAR INFORME
	$informe->InformeEmpreSucuBode	=	'false';     					//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false';		 					//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false';	   					//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false';	   					//SI EXPORTA A XLS
	$informe->InformeTamano         = 'CARTA-HORIZONTAL';   //TAMAÑO DEL INFORME
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	//CHANGE CSS
	$informe->DefaultCls            = ''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo == 'ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				     INICIALIZACION DE LA GRILLA	          	  ///**/
	/**/															                              /**/
	/**/		$informe->Link = $link;  	    //Conexion a la BD				/**/
	/**/		$informe->inicializa($_POST); //Variables POST					/**/
	/**/		$informe->GeneraInforme(); 	  //Inicializa la Grilla	  /**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var acceso 			= document.getElementById('acceso').value
		,	cargo 	 			= document.getElementById('cargo').value
		,	rol 		 			= document.getElementById('rol').value
		,	sucursal 			= document.getElementById('filtro_sucursal_empleados').value

		Ext.get('RecibidorInforme_empleados').load({
			url     : '../informes/informes/personal/empleados_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
									IMPRIME_HTML  : 'true',
									sucursal      : sucursal,
									acceso        : acceso,
									cargo 	      : cargo,
									rol        	  : rol
								}
		});

		document.getElementById("RecibidorInforme_empleados").style.padding = 20;

		localStorage.sucursalE       = sucursal;
		localStorage.accesoE   			 = acceso;
		localStorage.cargoE    			 = cargo;
		localStorage.rolE      			 = rol;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var acceso = document.getElementById('acceso').value
		,	cargo 	 = document.getElementById('cargo').value
		,	rol 		 = document.getElementById('rol').value
		,	sucursal = document.getElementById('filtro_sucursal_empleados').value

		var data =  tipo_documento + `=true
								&sucursal=${sucursal}
								&acceso=${acceso}
								&cargo=${cargo}
								&rol=${rol}`

		window.open("../informes/informes/personal/empleados_Result.php?" + data);

		localStorage.sucursalE = sucursal;
		localStorage.accesoE   = acceso;
		localStorage.cargoE    = cargo;
		localStorage.rolE      = rol;
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var sucursal = ''
		,	acceso 	 	 = ''
		,	cargo      = ''
		,	rol 	  	 = ''

		if(typeof(localStorage.sucursalE) != "undefined"){
			if(localStorage.sucursalE != ''){
				sucursal = localStorage.sucursalE;
			}
			else{
				alert('De generar el informe al menos una vez desde la ventana de configuracion.');
				return;
			}
		}

		if(typeof(localStorage.accesoE) != "undefined"){
			if(localStorage.accesoE != ''){
				acceso = localStorage.accesoE;
			}
			else{
				alert('De generar el informe al menos una vez desde la ventana de configuracion.');
				return;
			}
		}

		if(typeof(localStorage.cargoE) != "undefined"){
			if(localStorage.cargoE != ''){
				cargo = localStorage.cargoE;
			}
			else{
				alert('De generar el informe al menos una vez desde la ventana de configuracion.');
				return;
			}
		}

		if(typeof(localStorage.rolE) != "undefined"){
			if(localStorage.rolE != ''){
				rol = localStorage.rolE;
			}
			else{
				alert('De generar el informe al menos una vez desde la ventana de configuracion.');
				return;
			}
		}

		var data =  tipo_documento + `=true
								&sucursal=${sucursal}
								&acceso=${acceso}
								&cargo=${cargo}
								&rol=${rol}`

		window.open("../informes/informes/personal/empleados_Result.php?" + data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_informe_empleados = new Ext.Window({
	    width       : 560,
	    height      : 300,
	    id          : 'Win_Ventana_configurar_informe_empleados',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/personal/wizard_empleados.php',
							        scripts : true,
							        nocache : true,
							        params  : {
											            opc : 'cuerpoVentanaConfiguracionEmpleados',
												        }
								    },
	    tbar        : [
					      			{
					              xtype   : 'buttongroup',
					              columns : 3,
					              title   : 'Filtro Sucursal',
					              items   : [
					                        	{
					                            xtype       : 'panel',
					                            border      : false,
					                            width       : 160,
					                            height      : 45,
					                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
					                            autoLoad    : {
												                              url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
												                              scripts : true,
												                              nocache : true,
												                              params  : { opc  : 'empleados' }
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
						            handler     : function(){ Win_Ventana_configurar_informe_empleados.close() }
							        }
								    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.sucursalE = "global";
		localStorage.accesoE 	 = "";
		localStorage.cargoE    = "";
		localStorage.rolE      = "";
		Win_Ventana_configurar_informe_empleados.close();
		ventanaConfigurarInforme();
	}
</script>
