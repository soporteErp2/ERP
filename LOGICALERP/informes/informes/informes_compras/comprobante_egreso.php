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

	$informe->InformeName			=	'comprobante_egreso';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Informe Comprobante de Egreso'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInformeCE()','Btn_configurar_informe_clientes');

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

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var sucursal   = '';
		var idTerceros = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosCE.length; i++) {
			if (typeof(arraytercerosCE[i])!="undefined" && arraytercerosCE[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosCE[i] : idTerceros+','+arraytercerosCE[i] ;
			}
		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioComprobanteEgreso)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalComprobanteEgreso)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioComprobanteEgreso!='' && localStorage.MyInformeFiltroFechaFinalComprobanteEgreso) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalComprobanteEgreso;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioComprobanteEgreso;
			}
		}

		if (typeof(localStorage.sucursal_comprobante_egreso)!="undefined") {
			if (localStorage.sucursal_comprobante_egreso) {
				sucursal=localStorage.sucursal_comprobante_egreso;
			}
		}

		window.open("../informes/informes/informes_compras/comprobante_egreso_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInformeCE(){

		Win_Ventana_configurar_comprobante_egreso = new Ext.Window({
		    width       : 550,
		    height      : 400,
		    id          : 'Win_Ventana_configurar_comprobante_egreso',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_compras/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionComprobanteEgreso',
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
                                params  : { opc  : 'comprobante_egreso' }
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
                    handler     : function(){ generarHtmlCE() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelCE('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelCE('IMPRIME_XLS') }
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
		            handler     : function(){ Win_Ventana_configurar_comprobante_egreso.close() }
		        }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalComprobanteEgreso  = "";
		localStorage.MyInformeFiltroFechaInicioComprobanteEgreso = "";
		localStorage.sucursal_comprobante_egreso                 = "";
		arraytercerosCE.length                                   = 0;
		Win_Ventana_configurar_comprobante_egreso.close();
        ventanaConfigurarInformeCE();

	}

	function generarHtmlCE(){

		var MyInformeFiltroFechaFinal  =document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal   = document.getElementById('filtro_sucursal_comprobante_egreso').value;
		var idTerceros = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosCE.length; i++) {
			if (typeof(arraytercerosCE[i])!="undefined" && arraytercerosCE[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosCE[i] : idTerceros+','+arraytercerosCE[i] ;
			}

		}

		Ext.get('RecibidorInforme_comprobante_egreso').load({
			url     : '../informes/informes/informes_compras/comprobante_egreso_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				idTerceros                 : idTerceros,
			}
		});

		document.getElementById("RecibidorInforme_comprobante_egreso").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalComprobanteEgreso  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioComprobanteEgreso = MyInformeFiltroFechaInicio;
		localStorage.sucursal_comprobante_egreso                 = sucursal;

	}

	function generarPDF_ExcelCE(tipo_documento){

		var MyInformeFiltroFechaFinal  =document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal   = document.getElementById('filtro_sucursal_comprobante_egreso').value;
		var idTerceros = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosCE.length; i++) {
			if (typeof(arraytercerosCE[i])!="undefined" && arraytercerosCE[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosCE[i] : idTerceros+','+arraytercerosCE[i] ;
			}

		}

		window.open("../informes/informes/informes_compras/comprobante_egreso_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros);

	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTerceroCE(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaCliente_tercerosCE = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_tercerosCE',
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
					opcGrillaContable 	 : 'comprobante_egreso',
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
                    handler     : function(){ Win_VentanaCliente_tercerosCE.close(id) }
                }
            ]
        }).show();
	}

	function checkGrilla(checkbox,cont){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            var div   = document.createElement('div');
            div.setAttribute('id','fila_cartera_tercero_'+cont);
            div.setAttribute('class','filaBoleta');
            document.getElementById('bodyTablaConfiguracion').appendChild(div);

            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            nit=document.getElementById('nit_'+cont).innerHTML;
            tercero=document.getElementById('tercero_'+cont).innerHTML;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            tercerosConfiguradosCE[cont]='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteCE('+cont+')" title="Eliminar Cliente"></div>';
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('fila_cartera_tercero_'+cont).innerHTML=tercerosConfiguradosCE[cont];
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosCE[cont]=checkbox.value;

		}
		else if (checkbox.checked == false) {
			delete arraytercerosCE[cont];
			delete tercerosConfiguradosCE[cont];
			(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
		}

	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaClienteCE(cont){

		delete arraytercerosCE[cont];

		delete tercerosConfiguradosCE[cont];
		// console.log("fila_cartera_tercero_"+cont);
		(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
	}

</script>