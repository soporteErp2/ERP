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

	$informe->InformeName			=	'cotizaciones_venta';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Cotizaciones'; //TITULO DEL INFORME
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
	contTercero    = 1;
	contVendedores = 1;

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var sucursal   = '';
		var idTerceros = '';
		var idVendedores = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosCV.length; i++) {
			if (typeof(arraytercerosCV[i])!="undefined" && arraytercerosCV[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosCV[i] : idTerceros+','+arraytercerosCV[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS VENDEDORES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayvendedoresCV.length; i++) {
			if (typeof(arrayvendedoresCV[i])!="undefined" && arrayvendedoresCV[i]!="") {
				idVendedores=(idVendedores=='')? arrayvendedoresCV[i] : idVendedores+','+arrayvendedoresCV[i] ;
			}

		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioCotizacionesVenta)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalCotizacionesVenta)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioCotizacionesVenta!='' && localStorage.MyInformeFiltroFechaFinalCotizacionesVenta) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalCotizacionesVenta;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioCotizacionesVenta;
			}
		}

		if (typeof(localStorage.sucursal_cotizaciones_venta)!="undefined") {
			if (localStorage.sucursal_cotizaciones_venta) {
				sucursal=localStorage.sucursal_cotizaciones_venta;
			}
		}

		window.open("../informes/informes/informes_ventas/cotizaciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 550,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionCotizacionesVenta',

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
                                params  : { opc  : 'cotizaciones_venta' }
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
		            handler     : function(){ Win_Ventana_configurar_informe_facturas.close() }
		        }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalCotizacionesVenta  = "";
		localStorage.MyInformeFiltroFechaInicioCotizacionesVenta = "";
		localStorage.sucursal_cotizaciones_venta                 = "";
		arraytercerosCV.length                                   = 0;
		arrayvendedoresCV.length                                 = 0;
		Win_Ventana_configurar_informe_facturas.close();
        ventanaConfigurarInforme();

	}

	function generarHtml(){

		var MyInformeFiltroFechaFinal  =document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal   = document.getElementById('filtro_sucursal_cotizaciones_venta').value;
		var idTerceros = '';
		var idVendedores = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosCV.length; i++) {
			if (typeof(arraytercerosCV[i])!="undefined" && arraytercerosCV[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosCV[i] : idTerceros+','+arraytercerosCV[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS VENDEDORES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayvendedoresCV.length; i++) {
			if (typeof(arrayvendedoresCV[i])!="undefined" && arrayvendedoresCV[i]!="") {
				idVendedores=(idVendedores=='')? arrayvendedoresCV[i] : idVendedores+','+arrayvendedoresCV[i] ;
			}

		}

		Ext.get('RecibidorInforme_cotizaciones_venta').load({
			url     : '../informes/informes/informes_ventas/cotizaciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Cotizaciones',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				idTerceros                 : idTerceros,
				idVendedores               : idVendedores,
			}
		});

		document.getElementById("RecibidorInforme_cotizaciones_venta").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalCotizacionesVenta  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioCotizacionesVenta = MyInformeFiltroFechaInicio;
		localStorage.sucursal_cotizaciones_venta                  = sucursal;

	}

	function generarPDF_Excel(tipo_documento){

		var MyInformeFiltroFechaFinal  =document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal   = document.getElementById('filtro_sucursal_cotizaciones_venta').value;
		var idTerceros = '';
		var idVendedores = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosCV.length; i++) {
			if (typeof(arraytercerosCV[i])!="undefined" && arraytercerosCV[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosCV[i] : idTerceros+','+arraytercerosCV[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS VENDEDORES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayvendedoresCV.length; i++) {
			if (typeof(arrayvendedoresCV[i])!="undefined" && arrayvendedoresCV[i]!="") {
				idVendedores=(idVendedores=='')? arrayvendedoresCV[i] : idVendedores+','+arrayvendedoresCV[i] ;
			}

		}

		window.open("../informes/informes/informes_ventas/cotizaciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores);

	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTerceroFV(opc){
		if (opc=='vendedores') {
			tabla='empleados';
			tercero='nombre';
			titulo_ventana='Empleados';
		}
		else{
			tabla='terceros';
			tercero='nombre_comercial';
			titulo_ventana='Clientes';
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
					opcGrillaContable 	 : 'cotizaciones_venta',
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

	//FUNCION DE LA VENTANA DE BUSQUDA DE CLIENTES Y VENDEDORES
	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR

            if (tabla=='empleados') {
            	var div   = document.createElement('div');
            	div.setAttribute('id','fila_empleado_'+cont);
            	div.setAttribute('class','filaBoleta');
            	document.getElementById('bodyTablaConfiguracionVendedores').appendChild(div);

            	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            	var nit=document.getElementById('nit_'+cont).innerHTML;
            	var tercero=document.getElementById('tercero_'+cont).innerHTML;
            	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            	var fila='<div class="campo0">'+contVendedores+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteFV('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	vendedoresConfiguradosCV[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_empleado_'+cont).innerHTML=fila;
            	contVendedores++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arrayvendedoresCV[cont]=checkbox.value;
            }
            else{
            	var div   = document.createElement('div');
            	div.setAttribute('id','fila_cartera_tercero_'+cont);
            	div.setAttribute('class','filaBoleta');
            	document.getElementById('bodyTablaConfiguracion').appendChild(div);

            	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            	var nit=document.getElementById('nit_'+cont).innerHTML;
            	var tercero=document.getElementById('tercero_'+cont).innerHTML;
            	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteFV('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	tercerosConfiguradosCV[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_cartera_tercero_'+cont).innerHTML=fila;
            	contTercero++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arraytercerosCV[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {
			if (tabla=='empleados') {
				delete arrayvendedoresCV[cont];
				delete vendedoresConfiguradosCV[cont];
				(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
			}
			else{
				delete arraytercerosCV[cont];
				delete tercerosConfiguradosCV[cont];
				(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
			}


		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaClienteFV(cont,tabla){
		if (tabla=='empleados') {
			delete arrayvendedoresCV[cont];
			delete vendedoresConfiguradosCV[cont];
			(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
		}
		else{
			delete arraytercerosCV[cont];
			delete tercerosConfiguradosCV[cont];
			(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
		}
		// console.log("fila_cartera_tercero_"+cont);

	}

</script>