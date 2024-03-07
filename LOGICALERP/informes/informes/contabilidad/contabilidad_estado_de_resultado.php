<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE  	  ///**/
	/**/											                      /**/
	/**/	       $informe = new MyInforme();				/**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$id_empresa                     = $_SESSION['EMPRESA'];
	$id_sucursal_default            = $_SESSION['SUCURSAL'];
	$informe->InformeName			      = 'contabilidad_estado_de_resultado';  //NOMBRE DEL INFORME
	$informe->InformeTitle			    = 'Estado de Resultado'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	= 'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		  = 'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	= 'false';	 //FILTRO FECHA
	$informe->InformeExportarPDF	  = "false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	  = "false";	//SI EXPORTA A XLS
	$informe->BtnGenera             = 'false';
	$informe->DefaultCls            =	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         =	80; 		//HEIGHT TOOLBAR
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_cartera');

	$array = '["Resumido","Resumido"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"]';

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				        INICIALIZACION DE LA GRILLA	    			  ///**/
	/**/															                              /**/
	/**/	   $informe->Link = $link;  	   //Conexion a la BD			  /**/
	/**/	   $informe->inicializa($_POST); //Variables POST			    /**/
	/**/	   $informe->GeneraInforme(); 	 //Inicializa la Grilla		/**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contCentroCostos = 1;

	id_pais = "<?php echo $_SESSION[PAIS]; ?>";
	if(id_pais != 49){
		url_variable = '../informes/informes/contabilidad/contabilidad_estado_de_resultado_paises_Result.php';
	}
	else{
		url_variable = '../informes/informes/contabilidad/contabilidad_estado_de_resultado_Result.php';
	}

	//==========================// PDF Y EXCEL PRINCIPAL //==========================//
	function generarPDF_Excel_principal(tipo_documento){

		var nivel_cuenta                 = ''
		,	tipo_balance_EstadoResultado = ''
		,	sucursal                     = ''
		,	MyInformeFiltroFechaFinal    = ''
		,	separador_miles              = ''
		,	separador_decimales          = ''
		,	MyInformeFiltroFechaInicio   = ''
		,	arrayCcosJSON                = ''
		,	i                            = 0

		arrayCentroCostos.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
    	arrayCcosJSON=JSON.stringify(arrayCcosJSON);
		if (checkBoxSelectAllCcosER=='true') { arrayCcosJSON='todos'; }

		if (typeof(localStorage.nivel_cuentas_EstadoResultado)!="undefined" )
			if (localStorage.nivel_cuentas_EstadoResultado!='')
				nivel_cuenta=localStorage.nivel_cuentas_EstadoResultado;

		if (typeof(localStorage.tipo_balance_EstadoResultado)!="undefined" )
			if (localStorage.tipo_balance_EstadoResultado!='')
				tipo_balance_EstadoResultado=localStorage.tipo_balance_EstadoResultado;

		if (typeof(localStorage.sucursales_estado_resultado)!="undefined" )
			if (localStorage.sucursales_estado_resultado!='')
				sucursal=localStorage.sucursales_estado_resultado;

		if (typeof(localStorage.MyInformeFiltroFechaInicioEstadoResultado)!="undefined" )
			if (localStorage.MyInformeFiltroFechaInicioEstadoResultado!='')
				MyInformeFiltroFechaInicio=localStorage.MyInformeFiltroFechaInicioEstadoResultado;

		if (typeof(localStorage.MyInformeFiltroFechaFinalEstadoResultado)!="undefined" )
			if (localStorage.MyInformeFiltroFechaFinalEstadoResultado!='')
				MyInformeFiltroFechaFinal=localStorage.MyInformeFiltroFechaFinalEstadoResultado;

		var data = tipo_documento+`=true
					&nombre_informe=Estado de Resultados
					&tipo_balance_EstadoResultado=${tipo_balance_EstadoResultado}
					&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
					&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
					&separador_miles=${separador_miles}
					&separador_decimales=${separador_decimales}
					&nivel_cuentas=${nivel_cuenta}
					&arrayCcosJSON=${arrayCcosJSON}
					&sucursal=${sucursal}
					`;

		window.open(url_variable + "?" + data);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_cartera_edades = new Ext.Window({
		    width       : 750,
		    height      : 450,
		    id          : 'Win_Ventana_configurar_cartera_edades',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/wizard_estado_resultado.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventana_configuracion_PyG',
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
                                params  : { opc  : 'sucursales_estado_resultado' }
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
                    handler     : function(){ generarPDF_ExcelNiif('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelNiif('IMPRIME_XLS') }
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
                    handler     : function(){ Win_Ventana_configurar_cartera_edades.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){
		localStorage.nivel_cuentas_EstadoResultado             = "";
		localStorage.tipo_balance_EstadoResultado              = "";
		localStorage.MyInformeFiltroFechaFinalEstadoResultado  = "";
		localStorage.MyInformeFiltroFechaInicioEstadoResultado = "";
		localStorage.sucursales_estado_resultado               = "";
		arrayCentroCostos.length                               = 0;
		Win_Ventana_configurar_cartera_edades.close();
		ventanaConfigurarInforme();
	}

	function generarHtml(){
		var nivel_cuenta               = document.getElementById('nivel_cuenta').value
		,	tipo_balance_EstadoResultado = document.getElementById("tipo_balance").value
		,	sucursal                     = document.getElementById('filtro_sucursal_sucursales_estado_resultado').value
		,	MyInformeFiltroFechaFinal    = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio   = document.getElementById('MyInformeFiltroFechaInicio').value
		,	separador_miles              = document.getElementById('separador_miles').value
		,	separador_decimales          = document.getElementById('separador_decimales').value
		,	arrayCcosJSON                = Array()
		,	i                            = 0

  	arrayCentroCostos.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
  	arrayCcosJSON = JSON.stringify(arrayCcosJSON);

		if(checkBoxSelectAllCcosER == 'true'){ arrayCcosJSON = 'todos'; }

		Ext.get('RecibidorInforme_contabilidad_estado_de_resultado').load({
			url     : url_variable,
			text	  : 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe               : 'Estado de Resultados',
				tipo_balance_EstadoResultado : tipo_balance_EstadoResultado,
				MyInformeFiltroFechaFinal    : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio   : MyInformeFiltroFechaInicio,
				nivel_cuentas                : nivel_cuenta,
				arrayCcosJSON                : arrayCcosJSON,
				sucursal                     : sucursal,
				separador_miles              : separador_miles,
				separador_decimales          : separador_decimales,
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_estado_de_resultado").style.padding = 20;

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.nivel_cuentas_EstadoResultado             = nivel_cuenta;
		localStorage.tipo_balance_EstadoResultado              = tipo_balance_EstadoResultado;
		localStorage.MyInformeFiltroFechaFinalEstadoResultado  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioEstadoResultado = MyInformeFiltroFechaInicio;
		localStorage.sucursales_estado_resultado               = sucursal;
	}

	function generarPDF_ExcelNiif(tipo_documento){
		var nivel_cuenta               = document.getElementById('nivel_cuenta').value
		,	tipo_balance_EstadoResultado = document.getElementById("tipo_balance").value
		,	sucursal                     = document.getElementById('filtro_sucursal_sucursales_estado_resultado').value
		,	MyInformeFiltroFechaFinal    = document.getElementById('MyInformeFiltroFechaFinal').value
		,	separador_miles              = document.getElementById('separador_miles').value
		,	separador_decimales          = document.getElementById('separador_decimales').value
		,	MyInformeFiltroFechaInicio   = document.getElementById('MyInformeFiltroFechaInicio').value
		,	arrayCcosJSON                = Array()
		,	i                            = 0

    	arrayCentroCostos.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
    	arrayCcosJSON=JSON.stringify(arrayCcosJSON);

		if (checkBoxSelectAllCcosER=='true') { arrayCcosJSON='todos'; }

		var data = tipo_documento+`=true
					&nombre_informe=Estado de Resultados
					&tipo_balance_EstadoResultado=${tipo_balance_EstadoResultado}
					&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
					&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
					&separador_miles=${separador_miles}
					&separador_decimales=${separador_decimales}
					&nivel_cuentas=${nivel_cuenta}
					&arrayCcosJSON=${arrayCcosJSON}
					&sucursal=${sucursal}`;

		window.open(url_variable + "?" + data);
	}

	function buscarCentroCostos(event,input) {
		tecla   = (input) ? event.keyCode : event.which;
        numero  = input.value;

        if (tecla==13 && numero!="") {
        	Ext.Ajax.request({
        	    url     : '../informes/informes/contabilidad/bd.php',
        	    params  :
        	    {
        			opc  : 'buscarCentroCostos',
        			codigo : numero,
        	    },
        	    success :function (result, request){
        	    			if(result.responseText=='false'){ alert('Error\nNo existe el centro de costos');  return;}
        	                else if(result.responseText != 'true'){
    	                				var arrayBD=result.responseText;
                                      	var obj=JSON.parse(arrayBD);

        	                			console.log('id: '+obj.id+' nombre: '+obj.nombre);

        	                			renderizaResultadoVentanaCentroCosto(obj.id,numero,obj.nombre);
        	            			}
        	            },
        	    failure : function(){ console.log("fail"); }
        	});
        }
	}

	//=====================// VENTANA CENTROS DE COSTOS //=====================//
	function ventanaBuscarCentroCostos() {

		Win_Ventana_buscar_centro_cotos = new Ext.Window({
		    width       : 400,
		    height      : 450,
		    id          : 'Win_Ventana_buscar_centro_cotos',
		    title       : 'Seleccione un Centro de costo',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/grilla_buscar_centro_costos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opcGrillaContable : 'grillaCentroCostos',
					funcion           : 'renderizaResultadoVentanaCentroCosto(id,codigo,nombre)',
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
                    handler     : function(){ Win_Ventana_buscar_centro_cotos.close(id) }
                }
		    ]
		}).show();
	}

	//================== FUNCION PARA RENDERIZAR LOS RESULTADOS DE LA VENTANA DE CENTROS DE COSTOS =======================//
	function renderizaResultadoVentanaCentroCosto(id,codigo,nombre) {
    	var div   = document.createElement('div');
        div.setAttribute('id','row_ccos_'+id);
        div.setAttribute('class','row');
        document.getElementById('body_grilla_filtro').appendChild(div);

        var fila = `<div class="row" id="row_ccos_${id}">
    					<div class="cell" data-col="1"></div>
    					<div class="cell" data-col="2">${codigo}</div>
						<div class="cell" data-col="3">${nombre}</div>
                   		<div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${id})" title="Eliminar Centro Costos"></div>
                    </div>`;

        //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
        centroCostosConfigurados[id]=fila;

        //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        document.getElementById('row_ccos_'+id).innerHTML=fila;

        //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        arrayCentroCostos[id]=id;

	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCentroCostos(id){
		delete arrayCentroCostos[id];
		delete centroCostosConfigurados[id];
		(document.getElementById("row_ccos_"+id)).parentNode.removeChild(document.getElementById("row_ccos_"+id));
	}

</script>
