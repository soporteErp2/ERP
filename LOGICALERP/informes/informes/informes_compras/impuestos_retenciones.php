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

	$informe->InformeName			=	'impuestos_retenciones';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Impuestos y Retenciones Facturas de Compras'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal_FCIR("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal_FCIR("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme_FCIR()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";


	// CHANGE CSS
	$informe->DefaultCls    = 	''; //RESET STYLE CSS
	$informe->HeightToolbar = 	80; //HEIGHT TOOLBAR

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

	function generarPDF_Excel_principal_FCIR(tipo_documento){

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var sucursal       = '';
		var idTerceros     = '';
		var idVendedores   = '';
		var idCentroCostos = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_FCIR.length; i++) {
			if (typeof(arrayTerceros_FCIR[i])!="undefined" && arrayTerceros_FCIR[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_FCIR[i] : idTerceros+','+arrayTerceros_FCIR[i] ;
			}
		}

		//RECORREMOS EL ARRAY DE LOS CENTROS DE COSTO PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayCentroCostos_FCIR.length; i++) {
			if (typeof(arrayCentroCostos_FCIR[i])!="undefined" && arrayCentroCostos_FCIR[i]!="") {
				idCentroCostos=(idCentroCostos=='')? arrayCentroCostos_FCIR[i] : idCentroCostos+','+arrayCentroCostos_FCIR[i] ;
			}
		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalFacturas)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioFacturas!='' && localStorage.MyInformeFiltroFechaFinalFacturas) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalFacturas;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioFacturas;
			}
		}

		if (typeof(localStorage.sucursal_facturas)!="undefined") {
			if (localStorage.sucursal_facturas) {
				sucursal=localStorage.sucursal_facturas;
			}
		}

		window.open("../informes/informes/informes_compras/impuestos_retenciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores+"&idCentroCostos="+idCentroCostos);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme_FCIR(){

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 670,
		    height      : 340,
		    id          : 'Win_Ventana_configurar_informe_facturas',
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
		            opc : 'ventanaConfiguracion_FCIR',
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
                    handler     : function(){ generarHtml_FCIR() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_FCIR('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_FCIR('IMPRIME_XLS') }
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

		localStorage.MyInformeFiltroFechaFinalFacturas        = "";
		localStorage.MyInformeFiltroFechaFinalFacturasCompra  = "";
		localStorage.MyInformeFiltroFechaInicioFacturas       = "";
		localStorage.MyInformeFiltroFechaInicioFacturasCompra = "";
		localStorage.sucursal_facturas                        = "";
		localStorage.sucursal_facturas_compra                 = "";
		arrayTerceros_FCIR.length                             = 0;
		arrayCentroCostos_FCIR.length                         = 0;

		Win_Ventana_configurar_informe_facturas.close();
        ventanaConfigurarInforme_FCIR();

	}


	function generarHtml_FCIR(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal                   = document.getElementById('filtro_sucursal_facturas').value;
		var idTerceros                 = '';
		var idVendedores               = '';
		var idCentroCostos             = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_FCIR.length; i++) {
			if (typeof(arrayTerceros_FCIR[i])!="undefined" && arrayTerceros_FCIR[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_FCIR[i] : idTerceros+','+arrayTerceros_FCIR[i] ;
			}
		}
		//RECORREMOS EL ARRAY DE LOS CENTROS DE COSTO PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayCentroCostos_FCIR.length; i++) {
			if (typeof(arrayCentroCostos_FCIR[i])!="undefined" && arrayCentroCostos_FCIR[i]!="") {
				idCentroCostos=(idCentroCostos=='')? arrayCentroCostos_FCIR[i] : idCentroCostos+','+arrayCentroCostos_FCIR[i] ;
			}
		}

		Ext.get('RecibidorInforme_impuestos_retenciones').load({
			url     : '../informes/informes/informes_compras/impuestos_retenciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'impuestos_retenciones',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				idTerceros                 : idTerceros,
				idVendedores               : idVendedores,
				idCentroCostos             : idCentroCostos,
			}
		});

		document.getElementById("RecibidorInforme_impuestos_retenciones").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalFacturas  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFacturas = MyInformeFiltroFechaInicio;
		localStorage.sucursal_facturas                  = sucursal;

	}

	function generarPDF_Excel_FCIR(tipo_documento){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal       = document.getElementById('filtro_sucursal_facturas').value;
		var idTerceros     = '';
		var idVendedores   = '';
		var idCentroCostos = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_FCIR.length; i++) {
			if (typeof(arrayTerceros_FCIR[i])!="undefined" && arrayTerceros_FCIR[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_FCIR[i] : idTerceros+','+arrayTerceros_FCIR[i] ;
			}
		}
		//RECORREMOS EL ARRAY DE LOS CENTROS DE COSTO PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayCentroCostos_FCIR.length; i++) {
			if (typeof(arrayCentroCostos_FCIR[i])!="undefined" && arrayCentroCostos_FCIR[i]!="") {
				idCentroCostos=(idCentroCostos=='')? arrayCentroCostos_FCIR[i] : idCentroCostos+','+arrayCentroCostos_FCIR[i] ;
			}
		}

		window.open("../informes/informes/informes_compras/impuestos_retenciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores+"&idCentroCostos="+idCentroCostos);
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero_FCIR(opc){
		if (opc=='vendedores') {
			tabla          ='empleados';
			tercero        ='nombre';
			titulo_ventana ='Empleados';
		}
		else{
			tabla          ='terceros';
			tercero        ='nombre_comercial';
			titulo_ventana ='Clientes';
		}

        Win_VentanaCliente_terceros_FCIR = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros_FCIR',
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
					opcGrillaContable 	 : 'facturas',
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
                    handler     : function(){ Win_VentanaCliente_terceros_FCIR.close(id) }
                }
            ]
        }).show();
	}

	// ======================== VENTANA PARA BUSCAR LOS CENTROS DE COSTOS ========================//
	function ventanaBusquedaCentroCostos_FCIR(){
		Win_Ventana_buscar_centro_costos = new Ext.Window({
            width       : 450,
            height      : 410,
            id          : 'Win_Ventana_buscar_centro_costos',
            title       : 'Buscar Centro de Costos',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/grillaBuscarCentroCostos.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opcGrillaContable : 'facturas',
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
                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
                }
            ]
        }).show();
	}

	function checkGrillaCentroCostos(checkbox,cont){
		if (checkbox.checked ==true) {
        	var div   = document.createElement('div');
        	div.setAttribute('id','fila_centro_costo_'+cont);
        	div.setAttribute('class','filaBoleta');
        	document.getElementById('bodyTablaConfiguracionCentroCostos').appendChild(div);

        	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
        	var nit=document.getElementById('codigo_'+cont).innerHTML;
        	var tercero=document.getElementById('nombre_'+cont).innerHTML;
        	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
        	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" style="width:70px" id="codigo_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="nombre_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCentroCostos_FCIR('+cont+')" title="Eliminar Cliente"></div>';
        	conceptosConfigurados_FCIR[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_centro_costo_'+cont).innerHTML=fila;
        	contTercero++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayCentroCostos_FCIR[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayCentroCostos_FCIR[cont];
			delete conceptosConfigurados_FCIR[cont];
			(document.getElementById("fila_centro_costo_"+cont)).parentNode.removeChild(document.getElementById("fila_centro_costo_"+cont));
		}
	}

	function eliminaCentroCostos_FCIR(cont,tabla){
			delete arrayCentroCostos_FCIR[cont];
			delete tercerosConfigurados_FCIR[cont];
			(document.getElementById("fila_centro_costo_"+cont)).parentNode.removeChild(document.getElementById("fila_centro_costo_"+cont));
	}

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
            	var fila='<div class="campo0">'+contVendedores+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente_FCIR('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	vendedoresConfigurados_FCIR[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_empleado_'+cont).innerHTML=fila;
            	contVendedores++;
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
            	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente_FCIR('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	tercerosConfigurados_FCIR[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_cartera_tercero_'+cont).innerHTML=fila;
            	contTercero++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arrayTerceros_FCIR[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {
			delete arrayTerceros_FCIR[cont];
			delete tercerosConfigurados_FCIR[cont];
			(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente_FCIR(cont,tabla){

		delete arrayTerceros_FCIR[cont];
		delete tercerosConfigurados_FCIR[cont];
		(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));

	}

</script>