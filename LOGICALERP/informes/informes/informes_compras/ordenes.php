<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		   INICIALIZACION DE LA CLASE	  		///**/
	/**/																						/**/
	/**/			   $informe = new MyInforme();	  		/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  										= $_SESSION['EMPRESA'];
	$id_sucursal 										= $_SESSION['SUCURSAL'];
	$informe->BtnGenera             = 'false';
	$informe->InformeName						=	'ordenes_compra';  		//NOMBRE DEL INFORME
	$informe->InformeTitle					=	'Ordenes De Compra'; 	//TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; 							//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false'; 							//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= "false";							//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= "false";							//SI EXPORTA A XLS
	$informe->InformeTamano 				= "CARTA-HORIZONTAL";
	$informe->DefaultCls            = ''; 									//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 									//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	/**//////////////////////////////////////////////////////////////**/
	/**///				       INICIALIZACION DE LA GRILLA	  			 		///**/
	/**/																														/**/
	/**/	 $informe->Link = $link;  				//Conexion a la BD			/**/
	/**/	 $informe->inicializa($_POST);		//variables POST		    /**/
	/**/	 $informe->GeneraInforme(); 			// Inicializa la Grilla	/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero  = 1;
	contEmpleado = 1;

	//========================= GENERAR ARCHIVO EXCEL ==========================//
	function generarPDF_Excel_principal(tipo_documento){
		var arrayempleadosJSON         = Array()
		,   arraytercerosJSON 	       = Array()
		,   arrayccosJSON							 = Array()
		,   MyInformeFiltroFechaFinal  = ''
		,   MyInformeFiltroFechaInicio = ''

		if (typeof(localStorage.MyInformeFiltroFechaInicioOrdenesCompra) != "undefined" && typeof(localStorage.MyInformeFiltroFechaFinalOrdenesCompra) != "undefined") {
			if (localStorage.MyInformeFiltroFechaInicioOrdenesCompra != '' && localStorage.MyInformeFiltroFechaFinalOrdenesCompra != '') {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalOrdenesCompra;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioOrdenesCompra;
			}
		}

		if (typeof(localStorage.sucursal_ordenes_compra) != "undefined") {
			if (localStorage.sucursal_ordenes_compra) {
				sucursal = localStorage.sucursal_ordenes_compra;
			}
		}

		if (typeof(localStorage.bodega_ordenes_compra) != "undefined") {
			if (localStorage.bodega_ordenes_compra) {
				bodega = localStorage.bodega_ordenes_compra;
			}
		}

		if (typeof(localStorage.tipo_ordenes_compra) != "undefined") {
			if (localStorage.tipo_ordenes_compra) {
				tipo_orden_compra = localStorage.tipo_ordenes_compra;
			}
		}

		if (typeof(localStorage.estado_ordenes_compra) != "undefined") {
			if (localStorage.estado_ordenes_compra) {
				estado = localStorage.estado_ordenes_compra;
			}
		}

		if (typeof(localStorage.item_ordenes_compra) != "undefined") {
			if (localStorage.item_ordenes_compra) {
				item = localStorage.item_ordenes_compra;
			}
		}

		if (typeof(localStorage.autorizado_ordenes_compra) != "undefined") {
			if (localStorage.autorizado_ordenes_compra) {
				autorizado = localStorage.autorizado_ordenes_compra;
			}
		}

		i = 0
		arraytercerosOC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

    i = 0
    arrayempleadosOC.forEach(function(id_empleado) {  arrayempleadosJSON[i] = id_empleado; i++; });
    arrayempleadosJSON = JSON.stringify(arrayempleadosJSON);

		i = 0
		arrayCentroCostosOC.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

		var data = tipo_documento 								+ "=true"
		+					 "&MyInformeFiltroFechaFinal="	+	MyInformeFiltroFechaFinal
		+					 "&MyInformeFiltroFechaInicio="	+	MyInformeFiltroFechaInicio
		+					 "&sucursal="   								+	sucursal
		+					 "&bodega="											+ bodega
		+					 "&tipo_orden_compra="          + tipo_orden_compra
		+					 "&estado="										  +	estado
		+					 "&item="												+	item
		+					 "&autorizado="									+ autorizado
		+					 "&arraytercerosJSON="					+	arraytercerosJSON
		+					 "&arrayempleadosJSON="					+	arrayempleadosJSON
		+					 "&arrayccosJSON="       				+ arrayccosJSON

		window.open("../informes/informes/informes_compras/ordenes_Result.php?"+data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_informe_ordenes = new Ext.Window({
	    width       : 750,
	    height      : 580,
	    id          : 'Win_Ventana_configurar_informe_ordenes',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    tbar        : [
					        		{
			                  xtype   : 'buttongroup',
			                  columns : 3,
			                  title   : 'Filtros',
			                  items   : [
							                      {
							                        xtype       : 'panel',
							                        border      : false,
							                        width       : 205,
							                        height      : 65,
							                        bodyStyle   : 'background-color:rgba(255,255,255,0);',
							                        autoLoad    : {
												                              url     : '../funciones_globales/filtros/filtro_sucursal_bodega_informes.php',
												                              scripts : true,
												                              nocache : true,
												                              params  : {
																																	opc  	 : 'ordenes_compra'
																																}
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
						            handler     : function(){ Win_Ventana_configurar_informe_ordenes.close()
											}
	        }
	    ],
			autoLoad    : {
								      url     : '../informes/informes/informes_compras/wizard_ordenes.php',
								      scripts : true,
								      nocache : true,
								      params  :
								      {
								       	opc : 'cuerpoVentanaConfiguracionOrdenesCompra',
								      }
	    						  }
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalOrdenesCompra  = "";
		localStorage.MyInformeFiltroFechaInicioOrdenesCompra = "";
		localStorage.sucursal_ordenes_compra                 = "global";
		localStorage.bodega_ordenes_compra                   = "global";
		localStorage.tipo_ordenes_compra										 = "";
		localStorage.estado_ordenes_compra									 = "";
		localStorage.item_ordenes_compra      							 = "";
		localStorage.autorizado_ordenes_compra							 = "";
		tercerosConfiguradosOC.length                    		 = 0;
		empleadosConfiguradosOC.length                 			 = 0;
		centroCostosConfiguradosOC.length										 = 0;

		Win_Ventana_configurar_informe_ordenes.close();
    ventanaConfigurarInforme();
	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,		MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,		sucursal                   = document.getElementById('filtro_sucursal_ordenes_compra').value
		,		bodega                     = document.getElementById('filtro_bodega_ordenes_compra').value
		,   tipo_orden_compra					 = document.getElementById('tipo_orden_compra').value
		,   estado										 = document.getElementById('estado').value
		,		item      						     = document.getElementById('item').value
		, 	autorizado								 = document.getElementById('autorizado').value
		,		arraytercerosJSON          = Array()
		,		arrayempleadosJSON       	 = Array()
		,		arrayccosJSON							 = Array()
		,		i                          = 0;

		arraytercerosOC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

    i = 0
    arrayempleadosOC.forEach(function(id_empleado) {  arrayempleadosJSON[i] = id_empleado; i++; });
    arrayempleadosJSON=JSON.stringify(arrayempleadosJSON);

		i = 0
		arrayCentroCostosOC.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

		Ext.get('RecibidorInforme_ordenes_compra').load({
			url     : '../informes/informes/informes_compras/ordenes_Result.php',
			text	  : 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
									IMPRIME_HTML							 : 'true',
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									sucursal                   : sucursal,
									bodega										 : bodega,
									tipo_orden_compra					 : tipo_orden_compra,
									estado            				 : estado,
									item           						 : item,
									autorizado								 : autorizado,
									arraytercerosJSON          : arraytercerosJSON,
									arrayempleadosJSON         : arrayempleadosJSON,
									arrayccosJSON							 : arrayccosJSON
								}
		});

		document.getElementById("RecibidorInforme_ordenes_compra").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalOrdenesCompra  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioOrdenesCompra = MyInformeFiltroFechaInicio;
		localStorage.sucursal_ordenes_compra                 = sucursal;
		localStorage.bodega_ordenes_compra                   = bodega;
		localStorage.tipo_ordenes_compra										 = tipo_orden_compra;
		localStorage.estado_ordenes_compra									 = estado;
		localStorage.item_ordenes_compra      							 = item;
		localStorage.autorizado_ordenes_compra							 = autorizado;
	}

	//========================== GENERAR ARCHIVO PDF ===========================//
	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,		MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,		sucursal                   = document.getElementById('filtro_sucursal_ordenes_compra').value
		,		bodega                     = document.getElementById('filtro_bodega_ordenes_compra').value
		,   tipo_orden_compra					 = document.getElementById('tipo_orden_compra').value
		,   estado										 = document.getElementById('estado').value
		,		item      						     = document.getElementById('item').value
		, 	autorizado								 = document.getElementById('autorizado').value
		,		arraytercerosJSON          = Array()
		,		arrayempleadosJSON       	 = Array()
		,		arrayccosJSON							 = Array()
		,		i                          = 0;

		arraytercerosOC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

    i = 0
    arrayempleadosOC.forEach(function(id_empleado) {  arrayempleadosJSON[i] = id_empleado; i++; });
    arrayempleadosJSON=JSON.stringify(arrayempleadosJSON);

		i = 0
		arrayCentroCostosOC.forEach(function(id_centro_costo) { arrayccosJSON[i] = id_centro_costo; i++; });
		arrayccosJSON = JSON.stringify(arrayccosJSON);

    var data = tipo_documento 								+ "=true"
		+					 "&MyInformeFiltroFechaFinal="	+	MyInformeFiltroFechaFinal
		+					 "&MyInformeFiltroFechaInicio="	+	MyInformeFiltroFechaInicio
		+					 "&sucursal="   								+	sucursal
		+					 "&bodega="											+ bodega
		+					 "&tipo_orden_compra="          + tipo_orden_compra
		+					 "&estado="										  +	estado
		+					 "&item="												+	item
		+          "&autorizado="									+ autorizado
		+					 "&arraytercerosJSON="					+	arraytercerosJSON
		+					 "&arrayempleadosJSON="					+	arrayempleadosJSON
		+					 "&arrayccosJSON="       				+ arrayccosJSON

		window.open("../informes/informes/informes_compras/ordenes_Result.php?"+data);
	}

	//=================== VENTANA PARA BUSCAR LOS TERCEROS =====================//
	function ventanaBusquedaTerceroOC(opc){
		if(opc == 'empleados'){
			tabla          = 'empleados';
			tercero        = 'nombre';
			titulo_ventana = 'Empleados';
		}	else{
			tabla          = 'terceros';
			tercero        = 'nombre_comercial';
			titulo_ventana = 'Proveedores';
		}

        Win_VentanaCliente_terceros = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros',
            title       : titulo_ventana,
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
					tabla                : tabla,
					id_tercero           : 'id',
					tercero              : tercero,
					opcGrillaContable 	 : 'ordenes_compra',
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
                    handler     : function(){ Win_VentanaCliente_terceros.close(id) }
                }
            ]
        }).show();
	}

	//================ VENTANA PARA BUSCAR LOS CENTROS DE COSTO ================//
	function ventanaBusquedaCcos(){
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
					opcGrillaContable : 'ordenes_compra',
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

	//================== VERIFICAR LOS CAMPOS SELECCIONADOS ====================//
	function checkGrilla(checkbox,cont,tabla){
		if (checkbox.checked == true){
			if(tabla == 'terceros'){
				var div = document.createElement('div');
        div.setAttribute('id','row_tercero_'+cont);
        div.setAttribute('class','row');
        document.getElementById('body_grilla_filtro_tercero').appendChild(div);

        //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
        var nit     = document.getElementById('nit_'+cont).innerHTML
        var tercero = document.getElementById('tercero_'+cont).innerHTML;

        var fila = `<div class="row" id="row_tercero_${cont}">
                       <div class="cell" data-col="1">${contTercero}</div>
                       <div class="cell" data-col="2">${nit}</div>
                       <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                       <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont},'terceros')" title="Eliminar Cliente"></div>
                    </div>`;

        //LLENAMOS EL ARRAY CON EL CLIENTE CREADO
        tercerosConfiguradosOC[cont]=fila;
        //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        document.getElementById('row_tercero_'+cont).innerHTML=fila;
        contTercero++;
        //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        arraytercerosOC[cont]=checkbox.value;
			} else{
				var div = document.createElement('div');
				div.setAttribute('id','row_empleado_'+cont);
				div.setAttribute('class','row');
				document.getElementById('body_grilla_filtro_empleado').appendChild(div);

				//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
				var nit      = document.getElementById('nit_'+cont).innerHTML;
				var empleado = document.getElementById('tercero_'+cont).innerHTML;

				var fila = `<div class="row" id="row_empleado_${cont}">
											<div class="cell" data-col="1">${contEmpleado}</div>
											<div class="cell" data-col="2">${nit}</div>
											<div class="cell" data-col="3" title="${empleado}">${empleado}</div>
											<div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Usuario"></div>
										</div>`;

				//LLENAMOS EL ARRAY CON EL CLIENTE CREADO
				empleadosConfiguradosOC[cont]=fila;
				//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
				document.getElementById('row_empleado_'+cont).innerHTML = fila;
				contEmpleado++;
				//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
				arrayempleadosOC[cont] = checkbox.value;
			}
		}	else if(checkbox.checked == false){
			if(tabla == 'terceros'){
				delete arraytercerosOC[cont];
				delete tercerosConfiguradosOC[cont];
				(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
			}else{
				delete arrayempleadosOC[cont];
				delete empleadosConfiguradosOC[cont];
				(document.getElementById("row_empleado_"+cont)).parentNode.removeChild(document.getElementById("row_empleado_"+cont));
			}
		}
	}

	//=================== ELIMINAR LOS CLIENTES AGREGADOS ======================//
	function eliminaCliente(cont,tabla){
		if(tabla == 'terceros'){
			delete arraytercerosOC[cont];
			delete tercerosConfiguradosOC[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}else{
			delete arrayempleadosOC[cont];
			delete empleadosConfiguradosOC[cont];
			(document.getElementById("row_empleado_"+cont)).parentNode.removeChild(document.getElementById("row_empleado_"+cont));
		}
	}

	//================== VENTANA BUSCAR LOS CENTROS DE COSTOS ==================//
	function renderizaResultadoVentanaCentroCosto(id,codigo,nombre) {
		if(id != '' && codigo != '' && nombre != ''){
			var cadenaBuscar = '';
			for(i = 0; i < arrayCentroCostosOC.length; i++){
				if(typeof(arrayCentroCostosOC[i]) != "undefined" && arrayCentroCostosOC[i] != ""){
					if(id.indexOf(arrayCentroCostosOC[i]) == 0){
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
                    <div class="cell" data-col="1"></div>
                    <div class="cell" data-col="2">${codigo}</div>
                    <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${id})" title="Eliminar Centro Costos"></div>
                  </div>`;

      //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
      centroCostosConfiguradosOC[id] = fila;

      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_centro_costo_' + id).innerHTML = fila;

      //LLENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayCentroCostosOC[id] = id;
		}
	}

	//======================== ELIMINAR CENTRO DE COSTO ========================//
	function eliminaCentroCostos(id){
		delete arrayCentroCostosOC[id];
		delete centroCostosConfiguradosOC[id];
		(document.getElementById("row_centro_costo_"+id)).parentNode.removeChild(document.getElementById("row_centro_costo_"+id));
	}
</script>
