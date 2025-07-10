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

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName			    	=	'facturas_radicadas';  //NOMBRE DEL INFORME
	$informe->InformeTitle			  	=	'Facturas Radicadas';  //TITULO DEL INFORME
	$informe->BtnGenera							= 'false';							 //BOTON PARA GENERAR INFORME
	$informe->InformeEmpreSucuBode	=	'false';     					 //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false';		 					 //FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false';	   					 //SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false';	   					 //SI EXPORTA A XLS
	$informe->InformeTamano         = 'CARTA-HORIZONTAL';    //TAMAÑO DEL INFORME
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Enviar Informe','enviar','enviarInforme()','Btn_enviar');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	//CHANGE CSS
	$informe->DefaultCls            = ''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo == 'ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  		          	  ///**/
	/**/															                              /**/
	/**/		$informe->Link = $link;  	    //Conexion a la BD				/**/
	/**/		$informe->inicializa($_POST); //variables POST					/**/
	/**/		$informe->GeneraInforme(); 	  // Inicializa la Grilla	  /**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero  = 1;
	contEmpleado = 1;

	//============================ GENERAR INFORME =============================//
	function generarHtml(){

		var MyInformeFiltroFechaFinal   = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio 	= document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeIncluirAnuladasNC 	= document.getElementById('MyInformeIncluirAnuladasNC').value
		,	sucursal                   	= document.getElementById('filtro_sucursal_facturas').value
		, 	cliente						= document.getElementById('id_cliente').value
		, 	documento_cliente			= document.getElementById('documento_cliente').innerHTML
		, 	nombre_cliente				= document.getElementById('nombre_cliente').innerHTML
		, 	arrayccosJSON				= Array()
		, 	i							= 0;

		arrayCentroCostosFR.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

		if(MyInformeFiltroFechaFinal == "" || MyInformeFiltroFechaInicio == "" || cliente == "" || MyInformeIncluirAnuladasNC == ""){
			alert("Faltan filtros por completar");
			return;
		}

		Ext.get('RecibidorInforme_facturas_radicadas').load({
			url     : '../informes/informes/informes_ventas/facturas_radicadas_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
									IMPRIME_HTML				: 'true',
									MyInformeFiltroFechaInicio 	: MyInformeFiltroFechaInicio,
									MyInformeFiltroFechaFinal  	: MyInformeFiltroFechaFinal,
									sucursal                   	: sucursal,
									cliente          			: cliente,
									arrayccosJSON				: arrayccosJSON,
									MyInformeIncluirAnuladasNC  : MyInformeIncluirAnuladasNC
								}
		});

		document.getElementById("RecibidorInforme_facturas_radicadas").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalFacturas  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFacturas	= MyInformeFiltroFechaInicio;
		localStorage.sucursal_facturas                  = sucursal;
		localStorage.cliente     						= cliente;
		localStorage.documento_cliente     				= documento_cliente;
		localStorage.nombre_cliente     				= nombre_cliente;
		localStorage.MyInformeIncluirAnuladasNC         = MyInformeIncluirAnuladasNC;

	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){

		var MyInformeFiltroFechaFinal = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio 	= document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeIncluirAnuladasNC 	= document.getElementById('MyInformeIncluirAnuladasNC').value
		,	sucursal                   	= document.getElementById('filtro_sucursal_facturas').value
		, cliente											= document.getElementById('id_cliente').value
		, documento_cliente						= document.getElementById('documento_cliente').innerHTML
		, nombre_cliente							= document.getElementById('nombre_cliente').innerHTML
		, arrayccosJSON								= Array()
		, i													  = 0;

		arrayCentroCostosFR.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

		if(MyInformeFiltroFechaFinal == "" || MyInformeFiltroFechaInicio == "" || cliente == "" || MyInformeIncluirAnuladasNC == ""){
			alert("Faltan filtros por completar");
			return;
		}

		var data =  tipo_documento+`=true
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&sucursal=${sucursal}
								&cliente=${cliente}
								&documento_cliente=${documento_cliente}
								&nombre_cliente=${nombre_cliente}
								&arrayccosJSON=${arrayccosJSON}
								&MyInformeIncluirAnuladasNC=${MyInformeIncluirAnuladasNC}`

		window.open("../informes/informes/informes_ventas/facturas_radicadas_Result.php?"+data);
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var MyInformeFiltroFechaFinal = localStorage.MyInformeFiltroFechaFinalFacturas
		,	MyInformeFiltroFechaInicio 	= localStorage.MyInformeFiltroFechaInicioFacturas
		,	sucursal                   	= localStorage.sucursal_facturas
		, cliente											= localStorage.cliente
		, MyInformeIncluirAnuladasNC											= localStorage.MyInformeIncluirAnuladasNC
		, documento_cliente						= localStorage.documento_cliente
		, nombre_cliente							= localStorage.nombre_cliente
		, arrayccosJSON								= Array()
		, i													  = 0;

		arrayCentroCostosFR.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

		if(MyInformeFiltroFechaFinal == "" || MyInformeFiltroFechaInicio == "" || cliente == ""){
			alert("Debe generar el informe al menos una vez");
			return;
		}

		var data =  tipo_documento+`=true
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&sucursal=${sucursal}
								&cliente=${cliente}
								&documento_cliente=${documento_cliente}
								&nombre_cliente=${nombre_cliente}
								&arrayccosJSON=${arrayccosJSON}
								&MyInformeIncluirAnuladasNC=${MyInformeIncluirAnuladasNC}`

		window.open("../informes/informes/informes_ventas/facturas_radicadas_Result.php?"+data);
	}

  //======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_informe_facturas_radicadas = new Ext.Window({
		    width       : 661,
		    height      : 360,
		    id          : 'Win_Ventana_configurar_informe_facturas_radicadas',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    : {
								        url     : '../informes/informes/informes_ventas/wizard_facturas_radicadas.php',
								        scripts : true,
								        nocache : true,
								        params  : {
							            opc : 'cuerpoVentanaConfiguracionFacturasRadicadas',
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
                                params  : { opc  : 'facturas' }
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
                },'-',
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
                },'-',
								{
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Enviar<br>Informe',
                    scale       : 'large',
                    iconCls     : 'enviar',
                    iconAlign   : 'top',
                    handler     : function(){ enviarInforme() }
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
				            handler     : function(){ Win_Ventana_configurar_informe_facturas_radicadas.close()
								}
		        }
		    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalFacturas  				= "";
		localStorage.MyInformeFiltroFechaInicioFacturas 				= "";
		localStorage.sucursal_facturas                  				= "global";
		localStorage.cliente     						           					= "";
		localStorage.documento_cliente     						  				= "";
		localStorage.nombre_cliente     						    				= "";
		document.getElementById('id_cliente').value 						= "";
		document.getElementById('documento_cliente').innerHTML 	= "";
		document.getElementById('nombre_cliente').innerHTML 		= "";
		centroCostosConfiguradosFR.length										 		= 0;
		Win_Ventana_configurar_informe_facturas_radicadas.close();
		ventanaConfigurarInforme();
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTercero(opc){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		if(opc == 'empleados'){
			tabla = 'empleados';
			tercero = 'nombre';
			titulo_ventana = 'Empleados';
		}	else{
			tabla='terceros';
			tercero='nombre_comercial';
			titulo_ventana='Clientes';
		}

    Win_VentanaCliente_terceros = new Ext.Window({
      width       : myancho-100,
      height      : myalto-50,
      id          : 'Win_VentanaCliente_terceros',
      title       : titulo_ventana,
      modal       : true,
      autoScroll  : false,
      closable    : false,
      autoDestroy : true,
      autoLoad    : {
				              url     : '../funciones_globales/grillas/BusquedaTerceros.php',
				              scripts : true,
				              nocache : true,
				              params  : {
																	sql						: '',
																	cargaFuncion  : 'agregarCliente(id)',
																	nombre_grilla : 'facturas_radicadas'
									              }
				            },
      tbar        : [
			                {
		                    xtype       : 'button',
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_VentanaCliente_terceros.close(id) }
			                }
				            ]
    }).show();
	}

	//============================= MOSTRAR CLIENTE ============================//
	function agregarCliente(id){
		var documento 	= document.getElementById('div_facturas_radicadas_numero_identificacion_' + id).innerHTML;
		var nombre 			= document.getElementById('div_facturas_radicadas_nombre_comercial_' + id).innerHTML;

		document.getElementById('id_cliente').value = id;
		document.getElementById('documento_cliente').innerHTML = documento;
		document.getElementById('nombre_cliente').innerHTML = nombre;
		Win_VentanaCliente_terceros.close();
		return;
	}

	//============================= ENVIAR INFORME =============================//
	function enviarInforme(){
		var myalto  										= Ext.getBody().getHeight()
		,   myancho 										= Ext.getBody().getWidth()
		, 	arrayccosJSON								= Array()
		, 	i													  = 0;

		arrayCentroCostosFR.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

		if(localStorage.MyInformeFiltroFechaFinal == "" || localStorage.MyInformeFiltroFechaInicio == "" || localStorage.cliente == ""){
			alert("Debe generar el informe al menos una vez");
			return;
		}

    data = JSON.stringify({
                            'IMPRIME_PDF'                 : 'true',
                            'MyInformeFiltroFechaFinal'   : localStorage.MyInformeFiltroFechaFinalFacturas,
                            'MyInformeFiltroFechaInicio'  : localStorage.MyInformeFiltroFechaInicioFacturas,
                            'MyInformeIncluirAnuladasNC'  : localStorage.MyInformeIncluirAnuladasNC,
                            'sucursal'                    : localStorage.sucursal_facturas,
                            'cliente'                     : localStorage.cliente,
                            'documento_cliente'           : localStorage.documento_cliente,
                            'nombre_cliente'              : localStorage.nombre_cliente,
                            'arrayccosJSON'               : arrayccosJSON,
                            'GUARDAR_PDF'                 : 'true'
                          }, null);

		Win_Ventana_enviar_informe = new Ext.Window({
			id          : 'Win_Ventana_enviar_informe',
			title       : 'Enviar Informe Facturas Radicadas',
			iconCls     : 'pie2',
			width       : 920,
			height      : myalto - 150,
			modal       : true,
			autoDestroy : true,
			draggable   : false,
			resizable   : true,
			bodyStyle   : 'background-color:#DFE8F6;',
			autoLoad    : {
											url     : "../../LOGICALERP/informes/EnviarInforme.php",
											scripts : true,
											nocache : true,
											params  :	{
																	id_cliente  		: localStorage.cliente,
																	nombre_cliente 	: localStorage.nombre_cliente,
																	nombre_informe	: '<?php echo $informe->InformeName; ?>',
																	url_result      : 'informes/informes_ventas/facturas_radicadas_Result.php',
                                  data            : data
																}
										}
		}).show();
	}

	//================ VENTANA PARA BUSCAR LOS CENTROS DE COSTO ================//
	function ventanaBusquedaCcos(){
		Win_Ventana_buscar_centros_costos = new Ext.Window({
	    width       : 400,
	    height      : 450,
	    id          : 'Win_Ventana_buscar_centros_costos',
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
					opcGrillaContable : 'facturas_radicadas',
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
          handler     : function(){ Win_Ventana_buscar_centros_costos.close(id) }
        }
	    ]
		}).show();
	}

	//================== VENTANA BUSCAR LOS CENTROS DE COSTOS ==================//
	function renderizaResultadoVentanaCentroCosto(id,codigo,nombre){
		if(id != '' && codigo != '' && nombre != ''){
			var cadenaBuscar = '';
			for(i = 0; i < arrayCentroCostosFR.length; i++){
				if(typeof(arrayCentroCostosFR[i]) != "undefined" && arrayCentroCostosFR[i] != ""){
					if(id.indexOf(arrayCentroCostosFR[i]) == 0){
					  alert("Ya se agrego el Centro de Costos, o el padre del centro de costos");
					  return;
					}
				}
			}

      var div = document.createElement('div');
      div.setAttribute('id','row_centro_costo_' + id);
      div.setAttribute('class','row');
      document.getElementById('body_grilla_filtro_ccos').appendChild(div);

      var fila = `<div class="row" id="row_centro_costo_${id}">
                    <div class="cell" data-col="2">${codigo}</div>
                    <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${id})" title="Eliminar Centro Costos"></div>
                  </div>`;

      //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
      centroCostosConfiguradosFR[id] = fila;

      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_centro_costo_' + id).innerHTML = fila;

      //LLENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayCentroCostosFR[id] = id;
		}
	}

	//======================== ELIMINAR CENTRO DE COSTO ========================//
	function eliminaCentroCostos(id){
		delete arrayCentroCostosFR[id];
		delete centroCostosConfiguradosFR[id];
		(document.getElementById("row_centro_costo_" + id)).parentNode.removeChild(document.getElementById("row_centro_costo_" + id));
	}
</script>
