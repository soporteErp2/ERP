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

	$informe->InformeName			=	'devoluciones';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Devoluciones de Comrpa'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principalNDC("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principalNDC("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInformeNDC()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";


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

	function generarPDF_Excel_principalNDC(tipo_documento){

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var sucursal   = '';
		var idTerceros = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosNDC.length; i++) {
			if (typeof(arraytercerosNDC[i])!="undefined" && arraytercerosNDC[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosNDC[i] : idTerceros+','+arraytercerosNDC[i] ;
			}
		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioDevolucionCompras)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalDevolucionCompras)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioDevolucionCompras!='' && localStorage.MyInformeFiltroFechaFinalDevolucionCompras) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalDevolucionCompras;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioDevolucionCompras;
			}
		}

		if (typeof(localStorage.sucursal_DevolucionCompras)!="undefined") {
			if (localStorage.sucursal_DevolucionCompras) {
				sucursal=localStorage.sucursal_DevolucionCompras;
			}
		}

		window.open("../informes/informes/informes_compras/devoluciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInformeNDC(){

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 550,
		    height      : 350,
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
		            opc : 'cuerpoVentanaConfiguracionDevolucionCompras',
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
                                params  : { opc  : 'DevolucionCompras' }
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
                    handler     : function(){ generarHtmlNDC() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelNDC('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelNDC('IMPRIME_XLS') }
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
		localStorage.MyInformeFiltroFechaFinalDevolucionCompras  =  "";
		localStorage.MyInformeFiltroFechaInicioDevolucionCompras =	"";
		localStorage.sucursal_DevolucionCompras                  =	"";
		arraytercerosNDC.length                                   = 0;
		Win_Ventana_configurar_informe_facturas.close();
        ventanaConfigurarInformeNDC();

	}

	function generarHtmlNDC(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal                   = document.getElementById('filtro_sucursal_DevolucionCompras').value;
		var idTerceros                 = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosNDC.length; i++) {
			if (typeof(arraytercerosNDC[i])!="undefined" && arraytercerosNDC[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosNDC[i] : idTerceros+','+arraytercerosNDC[i] ;
			}

		}

		Ext.get('RecibidorInforme_devoluciones').load({
			url     : '../informes/informes/informes_compras/devoluciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Devoluciones de Venta',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				idTerceros                 : idTerceros,
			}
		});

		document.getElementById("RecibidorInforme_devoluciones").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalDevolucionCompras  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioDevolucionCompras = MyInformeFiltroFechaInicio;
		localStorage.sucursal_DevolucionCompras                  = sucursal;

	}

	function generarPDF_ExcelNDC(tipo_documento){

		var MyInformeFiltroFechaFinal  =document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal   = document.getElementById('filtro_sucursal_DevolucionCompras').value;
		var idTerceros = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arraytercerosNDC.length; i++) {
			if (typeof(arraytercerosNDC[i])!="undefined" && arraytercerosNDC[i]!="") {
				idTerceros=(idTerceros=='')? arraytercerosNDC[i] : idTerceros+','+arraytercerosNDC[i] ;
			}

		}

		window.open("../informes/informes/informes_compras/devoluciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros);

	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTerceroNDC(opc){
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

        Win_VentanaCliente_tercerosPF = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_tercerosPF',
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
					opcGrillaContable 	 : 'DevolucionCompras',
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
                    handler     : function(){ Win_VentanaCliente_tercerosPF.close(id) }
                }
            ]
        }).show();
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
            	var fila='<div class="campo0">'+contVendedores+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteNDC('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	vendedoresConfiguradosNDC[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_empleado_'+cont).innerHTML=fila;
            	contVendedores++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arrayvendedoresNDC[cont]=checkbox.value;
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
            	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteNDC('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	tercerosConfiguradosNDC[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_cartera_tercero_'+cont).innerHTML=fila;
            	contTercero++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arraytercerosNDC[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {
			if (tabla=='empleados') {
				delete arrayvendedoresNDC[cont];
				delete vendedoresConfiguradosNDC[cont];
				(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
			}
			else{
				delete arraytercerosNDC[cont];
				delete tercerosConfiguradosNDC[cont];
				(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
			}


		}

	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaClienteNDC(cont,tabla){
		delete arraytercerosNDC[cont];
		delete tercerosConfiguradosNDC[cont];
		(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));

	}

</script>