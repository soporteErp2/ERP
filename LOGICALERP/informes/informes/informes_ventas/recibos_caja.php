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

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName			=	'recibo_caja';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Recibos de Caja'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='ventas'){ $informe->AreaInformeQuitaAlto = 230; }

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

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal  = ''
		,	MyInformeFiltroFechaInicio = ''
		,	sucursal                   = ''
		,	software                   = ''
		,	i                          = 0
		,	arraytercerosJSON          = Array()

		arraytercerosRC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		if (typeof(localStorage.MyInformeFiltroFechaInicioReciboCaja)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalReciboCaja)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioReciboCaja!='' && localStorage.MyInformeFiltroFechaFinalReciboCaja) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalReciboCaja;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioReciboCaja;
			}
		}

		if (typeof(localStorage.sucursal_recibo_caja)!="undefined") {
			if (localStorage.sucursal_recibo_caja) {
				sucursal=localStorage.sucursal_recibo_caja;
			}
		}

		if (typeof(localStorage.softwareReciboCaja)!="undefined") {
			if (localStorage.softwareReciboCaja) {
				software=localStorage.softwareReciboCaja;
			}
		}

		var data = tipo_documento+"=true"
    							+"&sucursal="+sucursal
    							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
    							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
    							+"&arraytercerosJSON="+arraytercerosJSON
    							+"&software="+software

		window.open("../informes/informes/informes_ventas/recibos_caja_Result.php?"+data);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_recibo_caja = new Ext.Window({
		    width       : 670,
		    height      : 400,
		    id          : 'Win_Ventana_configurar_recibo_caja',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/wizard_recibos_caja.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionReciboCaja',

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
                                params  : { opc  : 'recibo_caja' }
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
		            handler     : function(){ Win_Ventana_configurar_recibo_caja.close() }
		        }
		    ]
		}).show();
	}


	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalFacturas    = "";
		localStorage.MyInformeFiltroFechaFinalReciboCaja  = "";
		localStorage.MyInformeFiltroFechaInicioReciboCaja = "";
		localStorage.sucursal_recibo_caja                 = "";
		arraytercerosRC.length                            = 0;
		Win_Ventana_configurar_recibo_caja.close();
		ventanaConfigurarInforme();
	}

	function generarHtml(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_recibo_caja').value
		,	software                   = document.getElementById('software').value
		,	i                          = 0
		,	arraytercerosJSON          = Array()

		arraytercerosRC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

		Ext.get('RecibidorInforme_recibo_caja').load({
			url     : '../informes/informes/informes_ventas/recibos_caja_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				arraytercerosJSON          : arraytercerosJSON,
				software                   : software
			}
		});

		document.getElementById("RecibidorInforme_recibo_caja").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalReciboCaja  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioReciboCaja = MyInformeFiltroFechaInicio;
		localStorage.sucursal_recibo_caja                 = sucursal;
		localStorage.softwareReciboCaja                   = software;
	}

	function generarPDF_Excel(tipo_documento){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_recibo_caja').value
		,	software                   = document.getElementById('software').value
		,	i                          = 0
		,	arraytercerosJSON          = Array()

		arraytercerosRC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        var data = tipo_documento+"=true"
    							+"&sucursal="+sucursal
    							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
    							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
    							+"&arraytercerosJSON="+arraytercerosJSON
    							+"&software="+software

		window.open("../informes/informes/informes_ventas/recibos_caja_Result.php?"+data);
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaTercero_RC = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaTercero_RC',
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
					opcGrillaContable 	 : 'recibo_caja',
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
                    handler     : function(){ Win_VentanaTercero_RC.close(id) }
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
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaTercero(${cont})" title="Eliminar Tercero"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            tercerosConfiguradosRC[cont]=fila;
	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_tercero_'+cont).innerHTML=fila;
	            contTercero++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arraytercerosRC[cont]=checkbox.value;

			}
			else if (checkbox.checked ==false) {
				delete arraytercerosRC[cont];
				delete tercerosConfiguradosRC[cont];
				(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
			}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaTercero(cont){

		delete arraytercerosRC[cont];

		delete tercerosConfiguradosRC[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}

</script>