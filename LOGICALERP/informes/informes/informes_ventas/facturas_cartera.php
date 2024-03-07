<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	include('../../../../misc/MyInforme/class.MyInforme.php');
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$informe->InformeName			=	'facturas_cartera';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Cartera'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal_Cartera("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal_Cartera("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme_Cartera()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";


	// CHANGE CSS
	$informe->DefaultCls    = 	''; //RESET STYLE CSS
	$informe->HeightToolbar = 	80; //HEIGHT TOOLBAR

	// $informe->AddBotton('exportar prueba','add','prueba();','btn_prueba');
	// $informe->AddBotton('prueba','add','prueba();');

	/* COMBOX PERSONALIZADO SUCURSALES*/
	// $querySucursal   = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE activo = 1 AND id_empresa='$id_empresa' ORDER BY nombre",$link);
	// $arraySucursales = '["","Por Empresa"],';
	// while($rowSucursales = mysql_fetch_array($querySucursal)){
	// 	$arraySucursales .= '["'.$rowSucursales['id'].'","'.$rowSucursales['nombre'].'"],';
	// }
	// $array= '["Grupos","Grupos"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"],["Auxiliares","Auxiliares"]';
	// $informe->AddFiltro('Generar','Seleccione...',trim($array,','),'Grupos');

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
	contTercero=1;
	contVendedores=1;

	function generarPDF_Excel_principal_Cartera(tipo_documento){

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var sucursal                   = '';
		var idTerceros                 = '';
		var idVendedores               = '';
		var idCentroCostos             = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_Cartera.length; i++) {
			if (typeof(arrayTerceros_Cartera[i])!="undefined" && arrayTerceros_Cartera[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_Cartera[i] : idTerceros+','+arrayTerceros_Cartera[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS VENDEDORES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayVendedores_Cartera.length; i++) {
			if (typeof(arrayVendedores_Cartera[i])!="undefined" && arrayVendedores_Cartera[i]!="") {
				idVendedores=(idVendedores=='')? arrayVendedores_Cartera[i] : idVendedores+','+arrayVendedores_Cartera[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS CENTROS DE COSTO PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayCentroCostos_Cartera.length; i++) {
			if (typeof(arrayCentroCostos_Cartera[i])!="undefined" && arrayCentroCostos_Cartera[i]!="") {
				idCentroCostos=(idCentroCostos=='')? arrayCentroCostos_Cartera[i] : idCentroCostos+','+arrayCentroCostos_Cartera[i] ;
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

		window.open("../informes/informes/informes_ventas/facturas_cartera_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores+"&idCentroCostos="+idCentroCostos);
	}

	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//

	function ventanaConfigurarInforme_Cartera(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 670,
		    height      : 340,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/bd.php',
		        scripts : true,
		        // text    : 'cargando...	',
		        nocache : true,
		        params  :
		        {
		            opc : 'ventanaConfiguracion_Cartera',

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
                    text        : 'Generar Informe',
                    scale       : 'large',
                    iconCls     : 'genera_informe',
                    iconAlign   : 'top',
                    handler     : function(){ generarHtml_Cartera() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar a PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_Cartera('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar a Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_Cartera('IMPRIME_XLS') }
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

	function generarHtml_Cartera(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal                   = document.getElementById('filtro_sucursal_facturas').value;
		var idTerceros                 = '';
		var idVendedores               = '';
		var idCentroCostos             = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_Cartera.length; i++) {
			if (typeof(arrayTerceros_Cartera[i])!="undefined" && arrayTerceros_Cartera[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_Cartera[i] : idTerceros+','+arrayTerceros_Cartera[i] ;
			}
		}

		//RECORREMOS EL ARRAY DE LOS VENDEDORES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayVendedores_Cartera.length; i++) {
			if (typeof(arrayVendedores_Cartera[i])!="undefined" && arrayVendedores_Cartera[i]!="") {
				idVendedores=(idVendedores=='')? arrayVendedores_Cartera[i] : idVendedores+','+arrayVendedores_Cartera[i] ;
			}
		}

		//RECORREMOS EL ARRAY DE LOS CENTROS DE COSTO PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayCentroCostos_Cartera.length; i++) {
			if (typeof(arrayCentroCostos_Cartera[i])!="undefined" && arrayCentroCostos_Cartera[i]!="") {
				idCentroCostos=(idCentroCostos=='')? arrayCentroCostos_Cartera[i] : idCentroCostos+','+arrayCentroCostos_Cartera[i] ;
			}
		}

		Ext.get('RecibidorInforme_facturas_cartera').load({
			url     : '../informes/informes/informes_ventas/facturas_cartera_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'facturas_cartera',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				idTerceros                 : idTerceros,
				idVendedores               : idVendedores,
				idCentroCostos             : idCentroCostos,
			}
		});

		document.getElementById("RecibidorInforme_facturas_cartera").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalFacturas  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFacturas = MyInformeFiltroFechaInicio;
		localStorage.sucursal_facturas                  = sucursal;

	}

	function generarPDF_Excel_Cartera(tipo_documento){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal       = document.getElementById('filtro_sucursal_facturas').value;
		var idTerceros     = '';
		var idVendedores   = '';
		var idCentroCostos = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_Cartera.length; i++) {
			if (typeof(arrayTerceros_Cartera[i])!="undefined" && arrayTerceros_Cartera[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_Cartera[i] : idTerceros+','+arrayTerceros_Cartera[i] ;
			}
		}

		//RECORREMOS EL ARRAY DE LOS VENDEDORES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayVendedores_Cartera.length; i++) {
			if (typeof(arrayVendedores_Cartera[i])!="undefined" && arrayVendedores_Cartera[i]!="") {
				idVendedores=(idVendedores=='')? arrayVendedores_Cartera[i] : idVendedores+','+arrayVendedores_Cartera[i] ;
			}
		}

		//RECORREMOS EL ARRAY DE LOS CENTROS DE COSTO PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayCentroCostos_Cartera.length; i++) {
			if (typeof(arrayCentroCostos_Cartera[i])!="undefined" && arrayCentroCostos_Cartera[i]!="") {
				idCentroCostos=(idCentroCostos=='')? arrayCentroCostos_Cartera[i] : idCentroCostos+','+arrayCentroCostos_Cartera[i] ;
			}
		}

		window.open("../informes/informes/informes_ventas/facturas_cartera_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores+"&idCentroCostos="+idCentroCostos);
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero_Cartera(opc){
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

        Win_VentanaCliente_terceros_Cartera = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros_Cartera',
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
                    handler     : function(){ Win_VentanaCliente_terceros_Cartera.close(id) }
                }
            ]
        }).show();
	}

	// ======================== VENTANA PARA BUSCAR LOS CENTROS DE COSTOS ========================//
	function ventanaBusquedaCentroCostos_Cartera(){
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
        	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" style="width:70px" id="codigo_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="nombre_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCentroCostos_Cartera('+cont+')" title="Eliminar Cliente"></div>';
        	conceptosConfigurados_Cartera[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_centro_costo_'+cont).innerHTML=fila;
        	contTercero++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayCentroCostos_Cartera[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayCentroCostos_Cartera[cont];
			delete conceptosConfigurados_Cartera[cont];
			(document.getElementById("fila_centro_costo_"+cont)).parentNode.removeChild(document.getElementById("fila_centro_costo_"+cont));
		}
	}

	function eliminaCentroCostos_Cartera(cont,tabla){
			delete arrayCentroCostos_Cartera[cont];
			delete tercerosConfigurados_Cartera[cont];
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
            	var fila='<div class="campo0">'+contVendedores+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente_Cartera('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	vendedoresConfigurados_Cartera[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_empleado_'+cont).innerHTML=fila;
            	contVendedores++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arrayVendedores_Cartera[cont]=checkbox.value;
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
            	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente_Cartera('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	tercerosConfigurados_Cartera[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_cartera_tercero_'+cont).innerHTML=fila;
            	contTercero++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arrayTerceros_Cartera[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {
			if (tabla=='empleados') {
				delete arrayVendedores_Cartera[cont];
				delete vendedoresConfigurados_Cartera[cont];
				(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
			}
			else{
				delete arrayTerceros_Cartera[cont];
				delete tercerosConfigurados_Cartera[cont];
				(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
			}


		}

	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente_Cartera(cont,tabla){
		if (tabla=='empleados') {
			delete arrayVendedores_Cartera[cont];
			delete vendedoresConfigurados_Cartera[cont];
			(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
		}
		else{
			delete arrayTerceros_Cartera[cont];
			delete tercerosConfigurados_Cartera[cont];
			(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
		}
	}

</script>