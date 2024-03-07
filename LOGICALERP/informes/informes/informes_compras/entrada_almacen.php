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

	$informe->InformeName			=	'entrada_almacen';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Entrada Almacen'; //TITULO DEL INFORME
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

		Win_Ventana_configurar_entrada_almacen = new Ext.Window({
		    width       : 750,
		    height      : 550,
		    id          : 'Win_Ventana_configurar_entrada_almacen',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_compras/wizard_entrada_almacen.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionEntradaAlmacen',
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
                                params  : { opc  : 'sucursales_entrada_almacen' }
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
                    handler     : function(){ Win_Ventana_configurar_entrada_almacen.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalEntradaAlmacen  = "";
		localStorage.MyInformeFiltroFechaInicioEntradaAlmacen = "";
		localStorage.sucursal_entrada_almacen                 = "";
		localStorage.tipo_entrada_almacen                     = "";
		localStorage.item_entrada_almacen                     = "";
		arraytercerosEA.length                                =  0;
		Win_Ventana_configurar_entrada_almacen.close();
		ventanaConfigurarInforme();
	}

	function generarHtml(){

		// CAPTURAR VARIABLES
		var	sucursal                 = document.getElementById('filtro_sucursal_sucursales_entrada_almacen').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		, arraytercerosJSON          = Array()
		,	tipo                       = document.getElementById('tipo').value
		,	item                       = document.getElementById('item').value
		,	i                          = 0

		arraytercerosEA.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		Ext.get('RecibidorInforme_entrada_almacen').load({
			url     : '../informes/informes/informes_compras/entrada_almacen_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Archivos adjuntos',
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				sucursal                   : sucursal,
				arraytercerosJSON          : arraytercerosJSON,
				tipo                       : tipo,
				item                       : item,
			}
		});

		localStorage.MyInformeFiltroFechaFinalEntradaAlmacen  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioEntradaAlmacen = MyInformeFiltroFechaInicio;
		localStorage.sucursal_entrada_almacen                 = sucursal;
		localStorage.tipo_entrada_almacen                     = tipo;
		localStorage.item_entrada_almacen                     = item;

		document.getElementById("RecibidorInforme_entrada_almacen").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){

		// CAPTURAR VARIABLES
		var sucursal                 = document.getElementById('filtro_sucursal_sucursales_entrada_almacen').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		, arraytercerosJSON          = Array()
		,	tipo                       = document.getElementById('tipo').value
		,	item                       = document.getElementById('item').value
		,	i = 0

		arraytercerosEA.map(function(id_tercero) { arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		var data = tipo_documento+"=true"
									+"&nombre_informe=Archivos_Adjuntos"
									+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
									+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
									+"&sucursal="+sucursal
									+"&arraytercerosJSON="+arraytercerosJSON
									+"&tipo="+tipo
									+"&item="+item

		window.open("../informes/informes/informes_compras/entrada_almacen_Result.php?"+data);
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
					opcGrillaContable 	 : 'archivos_adjuntos',
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
            tercerosConfiguradosEA[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosEA[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraytercerosEA[cont];
			delete tercerosConfiguradosEA[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//==================== FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont){

		delete arraytercerosEA[cont];

		delete tercerosConfiguradosEA[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}

</script>
