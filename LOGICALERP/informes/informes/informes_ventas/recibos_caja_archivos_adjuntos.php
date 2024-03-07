<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		   INICIALIZACION DE LA CLASE  	    ///**/
	/**/											                      /**/
	/**/	       $informe = new MyInforme();				/**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$id_empresa                     = $_SESSION['EMPRESA'];
	$id_sucursal_default            = $_SESSION['SUCURSAL'];
	$informe->InformeName			      =	'recibos_caja_archivos_adjuntos';   //NOMBRE DEL INFORME
	$informe->InformeTitle			    =	'Recibo De Caja Archivos Adjuntos'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false';                            //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		  =	'false';                            //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	                          //FILTRO FECHA
	$informe->DefaultCls            = ''; 		                            //RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		                            //HEIGHT TOOLBAR
	$informe->InformeExportarPDF	  = "false";	                          //SI EXPORTA A PDF
	$informe->InformeExportarXLS	  = "false";	                          //SI EXPORTA A XLS
	$informe->InformeTamano 		    = "CARTA-HORIZONTAL";
	$informe->BtnGenera             = 'false';
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;

	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_balance_prueba');

	/**//////////////////////////////////////////////////////////////**/
	/**///				        INICIALIZACION DE LA GRILLA	  			   ///**/
	/**/															                              /**/
	/**/	 $informe->Link = $link;  	    //Conexion a la BD			  /**/
	/**/	 $informe->inicializa($_POST);  //Variables POST			    /**/
	/**/	 $informe->GeneraInforme(); 	  //Inicializa la Grilla		/**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero = 1;

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_recibos_caja_archivos_adjuntos = new Ext.Window({
				width       : myancho - (myancho * 35 / 100),
				height      : myalto - (myalto * 30 / 100),
		    id          : 'Win_Ventana_configurar_recibos_caja_archivos_adjuntos',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/wizard_recibos_caja_archivos_adjuntos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionRecibosCajaArchivosAdjuntos',
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
                                params  : { opc  : 'sucursales_recibos_caja_archivos_adjuntos' }
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
                    handler     : function(){ Win_Ventana_configurar_recibos_caja_archivos_adjuntos.close() }
                }
		    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalRecibosCajaArchivosAdjuntos   = "";
		localStorage.MyInformeFiltroFechaInicioRecibosCajaArchivosAdjuntos  = "";
		localStorage.sucursal_recibos_caja_archivos_adjuntos                = "";
		localStorage.contenido_recibos_caja_archivos_adjuntos               = "";
		arraytercerosRCAA.length                                						=  0;
		Win_Ventana_configurar_recibos_caja_archivos_adjuntos.close();
		ventanaConfigurarInforme();
	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){

		// CAPTURAR VARIABLES
		var	sucursal                 = document.getElementById('filtro_sucursal_sucursales_recibos_caja_archivos_adjuntos').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		, arraytercerosJSON          = Array()
		,	contenido                  = document.getElementById('contenido').value
		,	i                          = 0

		arraytercerosRCAA.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		Ext.get('RecibidorInforme_recibos_caja_archivos_adjuntos').load({
			url     : '../informes/informes/informes_ventas/recibos_caja_archivos_adjuntos_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Recibos De Caja Archivos adjuntos',
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				sucursal                   : sucursal,
				arraytercerosJSON          : arraytercerosJSON,
				contenido                  : contenido,
			}
		});

		localStorage.MyInformeFiltroFechaFinalRecibosCajaArchivosAdjuntos  	 = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioRecibosCajaArchivosAdjuntos 	 = MyInformeFiltroFechaInicio;
		localStorage.sucursal_recibos_caja_archivos_adjuntos                 = sucursal;
		localStorage.contenido_recibos_caja_archivos_adjuntos                = contenido;

		document.getElementById("RecibidorInforme_recibos_caja_archivos_adjuntos").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){

		// CAPTURAR VARIABLES
		var sucursal                 = document.getElementById('filtro_sucursal_sucursales_recibos_caja_archivos_adjuntos').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		, arraytercerosJSON          = Array()
		,	contenido                  = document.getElementById('contenido').value
		,	i = 0

		arraytercerosRCAA.map(function(id_tercero) { arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		var data = tipo_documento+"=true"
									+"&nombre_informe=Recibos_Caja_Archivos_Adjuntos"
									+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
									+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
									+"&sucursal="+sucursal
									+"&arraytercerosJSON="+arraytercerosJSON
									+"&contenido="+contenido

		window.open("../informes/informes/informes_ventas/recibos_caja_archivos_adjuntos_Result.php?"+data);
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
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
					opcGrillaContable 	 : 'recibos_caja_archivos_adjuntos',
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

	//============================ MOSTRAR TERCEROS ============================//
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
            tercerosConfiguradosRCAA[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosRCAA[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraytercerosRCAA[cont];
			delete tercerosConfiguradosRCAA[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//============================ ELIMINAR TERCEROS ===========================//
	function eliminaCliente(cont){

		delete arraytercerosRCAA[cont];

		delete tercerosConfiguradosRCAA[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}
</script>
