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

	$informe->InformeName			=	'contabilidad_libro_auxiliar';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Libro Auxiliar'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	// EDIT CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->InformeExportarPDF	= "false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= "false";	//SI EXPORTA A XLS
	$informe->InformeTamano 		= "CARTA-HORIZONTAL";
	$informe->BtnGenera             = 'false';

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
	contTercero = 1;

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_libro_auxiliar = new Ext.Window({
		    width       : 750,
		    height      : 550,
		    id          : 'Win_Ventana_configurar_libro_auxiliar',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/wizard_libro_auxiliar.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionLibroAuxiliar',
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
                                params  : { opc  : 'sucursales_libro_auxiliar' }
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
                    handler     : function(){ Win_Ventana_configurar_libro_auxiliar.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalLibroAuxiliar  = "";
		localStorage.MyInformeFiltroFechaInicioLibroAuxiliar = "";
		localStorage.cuenta_finalLA                          = "";
		localStorage.cuenta_inicialLA                        = "";
		localStorage.sucursal_libro_auxiliar                 = "";
		localStorage.totalizado_libro_auxiliar               ="";
		localStorage.order_libro_auxiliar                    ="";
		localStorage.by_libro_auxiliar                       ="";
		arraytercerosLA.length                               =0;
		Win_Ventana_configurar_libro_auxiliar.close();
		ventanaConfigurarInforme();
	}

	function generarPDF_Excel_principal(tipo_documento){
		var fecha               = new Date()
		,	dia                   = fecha.getDate()
		,	mes                   = fecha.getMonth()+1
		,	anio                  = fecha.getFullYear()
		,	sucursal              = ''
		,	cuentaInicial         = ''
		,	cuentaFinal           = ''
		,	totalizado            = ""
		,	order                 = ""
		,	by                    = ""
		,	arraytercerosJSON     = Array()
		,	arrayCentroCostosJSON = Array()
		,	mostrar_observacion   = ""

		if (typeof(localStorage.cuenta_inicialLA) != "undefined" && typeof(localStorage.cuenta_finalLA) != "undefined" ) {
			if (localStorage.cuenta_inicialLA != "" && localStorage.cuenta_finalLA != "") {
				cuentaInicial = localStorage.cuenta_inicialLA;
				cuentaFinal   = localStorage.cuenta_finalLA;
			}
		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioLibroAuxiliar) != "undefined" && typeof(localStorage.MyInformeFiltroFechaFinalLibroAuxiliar) != "undefined") {
			if (localStorage.MyInformeFiltroFechaInicioLibroAuxiliar != "" && localStorage.MyInformeFiltroFechaFinalLibroAuxiliar != "") {
				var MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioLibroAuxiliar;
				var MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalLibroAuxiliar;
			} else{
				//VARIABLES CON EL RANGO DE FECHAS
				var MyInformeFiltroFechaInicio = anio + "-01-01";
				var MyInformeFiltroFechaFinal  = anio + "-" + mes + "-" + dia;
			}
		} else{
			//VARIABLES CON EL RANGO DE FECHAS
			var MyInformeFiltroFechaInicio = anio + "-01-01";
			var MyInformeFiltroFechaFinal  = anio + "-" + mes + "-" + dia;
		}

		if (typeof(localStorage.sucursal_libro_auxiliar) != "undefined" ) {
			if (localStorage.sucursal_libro_auxiliar != '') {
				sucursal = localStorage.sucursal_libro_auxiliar;
			}
		}

    if (typeof(localStorage.totalizado_libro_auxiliar) != "undefined") {
      if (localStorage.totalizado_libro_auxiliar != "") {
				totalizado = localStorage.totalizado_libro_auxiliar;
			}
		}

    if (typeof(localStorage.order_libro_auxiliar) != "undefined") {
      if (localStorage.order_libro_auxiliar != "") {
				order = localStorage.order_libro_auxiliar;
			}
    }

    if (typeof(localStorage.by_libro_auxiliar) != "undefined") {
      if (localStorage.by_libro_auxiliar != "") {
				by = localStorage.by_libro_auxiliar;
			}
		}

   	if (typeof(localStorage.mostrar_observacion) != "undefined") {
      if (localStorage.mostrar_observacion != "") {
				mostrar_observacion = localStorage.mostrar_observacion;
			}
		}

		i = 0
		arraytercerosLA.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++;  });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		i = 0
    arrayCentroCostosLA.forEach(function(id_centro_costo) {  arrayCentroCostosJSON[i] = id_centro_costo; i++; });
    arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

		var data =  tipo_documento+"=true"
								+"&nombre_informe=Libro_Auxiliar"
								+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
								+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
								+"&sucursal="+sucursal
								+"&cuentaInicial="+cuentaInicial
								+"&cuentaFinal="+cuentaFinal
								+"&arraytercerosJSON="+arraytercerosJSON
								+"&arrayCentroCostosJSON="+arrayCentroCostosJSON
								+"&totalizado="+totalizado
								+"&order_by="+order+" "+by
								+"&mostrar_observacion="+mostrar_observacion
		window.open("../informes/informes/contabilidad/contabilidad_libro_auxiliar_Result.php?" + data);
	}

	//================ VENTANA PARA BUSCAR LA CUENTA DEL PUC PARA EL RANGO DE CUENTAS ===========//
	function ventanaBuscarCuentaPuc(campo){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_cuenta_puc_balance_prueba = new Ext.Window({
		    width       : 680,
		    height      : 500,
		    id          : 'Win_Ventana_buscar_cuenta_puc_balance_prueba',
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
		            opc : 'puc',

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
                    handler     : function(){ Win_Ventana_buscar_cuenta_puc_balance_prueba.close() }
                }
		    ]
		}).show();
	}

	//================== RENDERIZAR LOS RESULTADOS DE LA BUSQUEDA DE LA CUENTA =============================//
	function renderizaResultadoVentanaPuc(id,campo){
		input=document.getElementById(campo);
		input.value=document.getElementById('div_buscarCuentaBalancePrueba_cuenta_'+id).innerHTML;
		input.setAttribute("title",document.getElementById('div_buscarCuentaBalancePrueba_descripcion_'+id).innerHTML);
		Win_Ventana_buscar_cuenta_puc_balance_prueba.close();

		input.focus();
	}

	//============== EVENTO KEY UP DE LOS CAMPOS CUENTA =================================//
	function validaCuentaPuc(event,input) {
		tecla  = input ? event.keyCode : event.which;
		patron = /[^\d]/g;
        if(patron.test(input.value)){ input.value = (input.value).replace(/[^0-9]/g,''); }
	}

	function generarHtml(){

		// CAPTURAR VARIABLES
		var cuentaInicial              = document.getElementById('cuenta_inicial').value
		,	cuentaFinal                = document.getElementById('cuenta_final').value
		,	sucursal                   = document.getElementById('filtro_sucursal_sucursales_libro_auxiliar').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,   arraytercerosJSON          = Array()
		,   arrayCentroCostosJSON      = Array()
		,	totalizado                 = document.getElementById('totalizado').value
		,	order                      = document.getElementById('order').value
		,	by                         = document.getElementById('by').value
		,	mostrar_observacion        = document.getElementById('mostrar_observacion').value
		,	i                          = 0

		if (cuentaInicial!="" && cuentaFinal==""|| cuentaInicial=="" && cuentaFinal!="") {
			alert("Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
			return;
		}

		arraytercerosLA.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        arrayCentroCostosLA.forEach(function(id_centro_costo) {  arrayCentroCostosJSON[i] = id_centro_costo; i++; });
        arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

		Ext.get('RecibidorInforme_contabilidad_libro_auxiliar').load({
			url     : '../informes/informes/contabilidad/contabilidad_libro_auxiliar_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Libro auxiliar',
				cuentaInicial              : cuentaInicial,
				cuentaFinal                : cuentaFinal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				sucursal                   : sucursal,
				arraytercerosJSON          : arraytercerosJSON,
				arrayCentroCostosJSON      : arrayCentroCostosJSON,
				totalizado                 : totalizado,
				order_by                   : order+" "+by,
				mostrar_observacion        : mostrar_observacion,
			}
		});

		localStorage.cuenta_inicialLA                        = cuentaInicial;
		localStorage.cuenta_finalLA                          = cuentaFinal;
		localStorage.MyInformeFiltroFechaFinalLibroAuxiliar  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioLibroAuxiliar = MyInformeFiltroFechaInicio;
		localStorage.sucursal_libro_auxiliar                 = sucursal;
		localStorage.totalizado_libro_auxiliar               = totalizado;
		localStorage.order_libro_auxiliar                    = order;
		localStorage.by_libro_auxiliar                       = by;
		localStorage.mostrar_observacion                     = mostrar_observacion;

		document.getElementById("RecibidorInforme_contabilidad_libro_auxiliar").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){

		// CAPTURAR VARIABLES
		var cuentaInicial              = document.getElementById('cuenta_inicial').value
		,	cuentaFinal                = document.getElementById('cuenta_final').value
		,	sucursal                   = document.getElementById('filtro_sucursal_sucursales_libro_auxiliar').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,   arraytercerosJSON          = Array()
		,	arrayCentroCostosJSON      = Array()
		,	totalizado                 = document.getElementById('totalizado').value
		,	order                      = document.getElementById('order').value
		,	by                         = document.getElementById('by').value
		,	mostrar_observacion        = document.getElementById('mostrar_observacion').value
		,	i                          = 0

		if (cuentaInicial!="" && cuentaFinal==""|| cuentaInicial=="" && cuentaFinal!="") {
			alert("Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
			return;
		}

		arraytercerosLA.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++;  });
    	arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		arrayCentroCostosLA.forEach(function(id_centro_costo) {  arrayCentroCostosJSON[i] = id_centro_costo; i++; });
		arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

		var data = tipo_documento+"=true"
									+"&nombre_informe=Libro_Auxiliar"
									+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
									+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
									+"&sucursal="+sucursal
									+"&cuentaInicial="+cuentaInicial
									+"&cuentaFinal="+cuentaFinal
									+"&arraytercerosJSON="+arraytercerosJSON
									+"&arrayCentroCostosJSON="+arrayCentroCostosJSON
									+"&totalizado="+totalizado
									+"&order_by="+order+" "+by
									+"&mostrar_observacion="+mostrar_observacion

		window.open("../informes/informes/contabilidad/contabilidad_libro_auxiliar_Result.php?"+data);
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaCliente_ = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_',
            title       : 'Terceros',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
					tabla                : 'terceros',
					id_tercero           : 'id',
					tercero              : 'nombre_comercial',
					opcGrillaContable 	 : 'libro_auxiliar',
					cargaFuncion         : '',
					nombre_grilla        : '',
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_VentanaCliente_.close(id) }
                }
            ]
        }).show();
	}

	function checkGrilla(checkbox,cont){

		if (checkbox.checked ==true) {

			var div   = document.createElement('div');
            div.setAttribute('id','row_tercero_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var nit     = document.getElementById('nit_'+cont).innerHTML
            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

            var fila = `<div class="row" id="row_tercero_${cont}">
                           <div class="cell" data-col="1">${contTercero}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Cliente"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            tercerosConfiguradosLA[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosLA[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraytercerosLA[cont];
			delete tercerosConfiguradosLA[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont){

		delete arraytercerosLA[cont];

		delete tercerosConfiguradosLA[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}


	//=====================// VENTANA CENTROS DE COSTOS //=====================//
	//*************************************************************************//
	function ventanaBusquedaCcos() {

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
		if (id!='' && codigo!='' && nombre!='') {
			//VALIDAR QUE LAS CUENTAS NO ESTEN YA AGREGADAS
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			var cadenaBuscar='';
			for ( i = 0; i < arrayCentroCostosLA.length; i++) {
				if (typeof(arrayCentroCostosLA[i])!="undefined" && arrayCentroCostosLA[i]!="") {
					// console.log(codigo.indexOf(arrayCentroCostosLA[i])+' - '+arrayCentroCostosLA[i]+' - '+id);

					if (id.indexOf(arrayCentroCostosLA[i])==0) {

					  alert("Ya se agrego el Centro de Costos, o el padre del centro de costos");
					  return;
					}
				}
			}

            var div   = document.createElement('div');
            div.setAttribute('id','row_centro_costo_'+id);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro_ccos').appendChild(div);

            var fila = `<div class="row" id="row_centro_costo_${id}">
                           <div class="cell" data-col="1"></div>
                           <div class="cell" data-col="2">${codigo}</div>
                           <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${id})" title="Eliminar Centro Costos"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            centroCostosConfiguradosLA[id]=fila;

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_centro_costo_'+id).innerHTML=fila;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosLA[id]=id;
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCentroCostos(id){

		delete arrayCentroCostosLA[id];
		delete centroCostosConfiguradosLA[id];
		(document.getElementById("row_centro_costo_"+id)).parentNode.removeChild(document.getElementById("row_centro_costo_"+id));
	}

</script>
