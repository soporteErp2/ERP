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

	$informe->InformeName			=	'contabilidad_estado_de_resultado_niif';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Estado de Resultado'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	// CHANGE CSS
	$informe->DefaultCls               = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar            = 	80; 		//HEIGHT TOOLBAR

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principalNiif("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principalNiif("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInformeNiif()','Btn_configurar_cartera');

	$array= '["Resumido","Resumido"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"]';

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

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

	contCentroCostosNiif = 1;

	//==========================// PDF Y EXCEL PRINCIPAL //==========================//
	//*******************************************************************************//

	function generarPDF_Excel_principalNiif(tipo_documento){

		var id_centro_costos = '';

		//RECORRER LOS CHECKBOX PARA IDENTIFICAR SI SE SELECCIONARON LOS CENTROS DE COSTOS O NO
		if (arrayCentroCostosNiif.length>0) {
			for(i=0;i<arrayCentroCostosNiif.length;i++){
				if (typeof(arrayCentroCostosNiif[i])!="undefined") {
					id_centro_costos+=arrayCentroCostosNiif[i]+',';
				}
			}
		}

		if (checkBoxSelectAllNiif=='true') {id_centro_costos='todos';}
		var MyInformeFiltroFechaFinal    = (typeof(localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif)!='undefined')? localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif : '' ;
		var MyInformeFiltroFechaInicio   = (typeof(localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif)!='undefined')? localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif : '' ;
		var tipo_balance_EstadoResultado = (typeof(localStorage.tipo_balance_EstadoResultadoNiif)!='undefined')? localStorage.tipo_balance_EstadoResultadoNiif : 'mensual' ;
		var nivel_cuenta                 = (typeof(localStorage.nivel_cuentas_EstadoResultadoNiif)!='undefined')? localStorage.nivel_cuentas_EstadoResultadoNiif : 'Grupos' ;
		var estado_resultado             = (typeof(localStorage.estado_resultado_niif)!='undefined')? localStorage.estado_resultado_niif : '' ;
		var mostrar_cuentas_niif         = (typeof(localStorage.mostrar_cuentas_niif)!='undefined')? localStorage.mostrar_cuentas_niif : 'false' ;
		var sucursal                     = (typeof(localStorage.sucursales_estado_resultado_niif)!='undefined')? localStorage.sucursales_estado_resultado_niif : '' ;

		window.open("../informes/informes/contabilidad_niif/contabilidad_estado_de_resultado_Result.php?"+tipo_documento+"=true&nombre_informe=Estado de Resultados&tipo_balance_EstadoResultado="+tipo_balance_EstadoResultado+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&generar="+nivel_cuenta+"&id_centro_costos="+id_centro_costos+"&estado_resultado="+estado_resultado+"&mostrar_cuentas_niif="+mostrar_cuentas_niif+"&sucursal="+sucursal);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInformeNiif(){

		Win_Ventana_configurar_cartera_edades = new Ext.Window({
		    width       : 550,
		    height      : 530,
		    id          : 'Win_Ventana_configurar_cartera_edades',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad_niif/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaEstadoResultado',
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
                                params  : { opc  : 'sucursales_estado_resultado' }
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
                    handler     : function(){ generarHtmlNiif() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelNiif('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelNiif('IMPRIME_XLS') }
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
                    handler     : function(){ Win_Ventana_configurar_cartera_edades.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){


		localStorage.nivel_cuentas_EstadoResultadoNiif             = "";
		localStorage.tipo_balance_EstadoResultadoNiif              = "";
		localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif  = "";
		localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif = "";
		localStorage.estado_resultado_niif                         = "";
		localStorage.mostrar_cuentas_niif                          = "";
		localStorage.sucursales_estado_resultado_niif              = "";
		arrayCentroCostosNiif.length                               =0;
		Win_Ventana_configurar_cartera_edades.close();
		ventanaConfigurarInformeNiif();
	}


	function generarHtmlNiif(){
		var sucursal             = document.getElementById('filtro_sucursal_sucursales_estado_resultado').value;
		var elementos            = document.getElementsByName("tipo_balance");
		var nivel_cuenta         = document.getElementById('nivel_cuenta').value;
		var estado_resultado     = document.getElementById('estado_resultado').value;
		var mostrar_cuentas_niif = (document.getElementById('mostrar_cuentas').checked)? 'true' : '' ;

		var id_centro_costos             = '';
		var tipo_balance_EstadoResultado = '';

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_balance_EstadoResultado=elementos[i].value;}
		}

		var MyInformeFiltroFechaInicio = '';
		var MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance_EstadoResultado!='rango_fechas') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance_EstadoResultado=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{ return; }

		//RECORRER LOS CHECKBOX PARA IDENTIFICAR SI SE SELECCIONARON LOS CENTROS DE COSTOS O NO
		if (arrayCentroCostosNiif.length>0) {
			for(i=0;i<arrayCentroCostosNiif.length;i++){
				if (typeof(arrayCentroCostosNiif[i])!="undefined") {
					id_centro_costos+=arrayCentroCostosNiif[i]+',';
				}
			}
		}

		if (checkBoxSelectAllNiif=='true') {id_centro_costos='todos';}

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.nivel_cuentas_EstadoResultadoNiif             = nivel_cuenta;
		localStorage.tipo_balance_EstadoResultadoNiif              = tipo_balance_EstadoResultado;
		localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif = MyInformeFiltroFechaInicio;
		localStorage.estado_resultado_niif                         = estado_resultado;
		localStorage.mostrar_cuentas_niif                          = mostrar_cuentas_niif;
		localStorage.sucursales_estado_resultado_niif              = sucursal;

		Ext.get('RecibidorInforme_contabilidad_estado_de_resultado_niif').load({
			url     : '../informes/informes/contabilidad_niif/contabilidad_estado_de_resultado_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe               : 'Estado de Resultados',
				tipo_balance_EstadoResultado : tipo_balance_EstadoResultado,
				MyInformeFiltroFechaFinal    : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio   : MyInformeFiltroFechaInicio,
				generar                      : nivel_cuenta,
				id_centro_costos             : id_centro_costos,
				estado_resultado             : estado_resultado,
				mostrar_cuentas_niif         : mostrar_cuentas_niif,
				sucursal                     : sucursal,
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_estado_de_resultado_niif").style.padding = 20;
	}

	function generarPDF_ExcelNiif(tipo_documento){
		var nivel_cuenta = document.getElementById('nivel_cuenta').value;
		var elementos = document.getElementsByName("tipo_balance");
		var id_centro_costos='';
		var estado_resultado = document.getElementById('estado_resultado').value;
		var mostrar_cuentas_niif = (document.getElementById('mostrar_cuentas').checked)? 'true' : '' ;
		var sucursal=document.getElementById('filtro_sucursal_sucursales_estado_resultado').value;

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_balance_EstadoResultado=elementos[i].value;}
		}

		var MyInformeFiltroFechaInicio = '';
		var MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance_EstadoResultado!='rango_fechas') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance_EstadoResultado=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{
			return;
		}

		//RECORRER LOS CHECKBOX PARA IDENTIFICAR SI SE SELECCIONARON LOS CENTROS DE COSTOS O NO
		if (arrayCentroCostosNiif.length>0) {
			for(i=0;i<arrayCentroCostosNiif.length;i++){
				if (typeof(arrayCentroCostosNiif[i])!="undefined") {
					id_centro_costos+=arrayCentroCostosNiif[i]+',';
				}
			}
		}
		if (checkBoxSelectAllNiif=='true') {id_centro_costos='todos';}
		window.open("../informes/informes/contabilidad_niif/contabilidad_estado_de_resultado_Result.php?"+tipo_documento+"=true&nombre_informe=Estado de Resultados&tipo_balance_EstadoResultado="+tipo_balance_EstadoResultado+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&generar="+nivel_cuenta+"&id_centro_costos="+id_centro_costos+"&estado_resultado="+estado_resultado+"&mostrar_cuentas_niif="+mostrar_cuentas_niif+"&sucursal="+sucursal);
	}

	//=============== BUSCAR CENTRO DE COSTOS =================================//
	function buscarCentroCostosNiif(event,input) {
		tecla   = (input) ? event.keyCode : event.which;
        numero  = input.value;

        if (tecla==13 && numero!="") {
        	Ext.Ajax.request({
        	    url     : '../informes/informes/contabilidad_niif/bd.php',
        	    params  :
        	    {
        			opc  : 'buscarCentroCostosNiif',
        			codigo : numero,
        	    },
        	    success :function (result, request){
        	    			if(result.responseText=='false'){ alert('Error\nNo existe el centro de costos');  return;}
        	                else if(result.responseText != 'true'){
    	                				var arrayBD=result.responseText;
                                      	var obj=JSON.parse(arrayBD);

        	                			console.log('id: '+obj.id+' nombre: '+obj.nombre);

        	                			renderizaResultadoVentanaCentroCostoNiif(obj.id,numero,obj.nombre);
        	            			}
        	            },
        	    failure : function(){ console.log("fail"); }
        	});
        }
	}

	//=================== VENTANA PARA BUSCAR LOS CENTROS DE COSTOS ======================================//
	function ventanaBuscarCentroCostosNiif(){

		Win_Ventana_buscar_centro_cotosNiif = new Ext.Window({
		    width       : 400,
		    height      : 450,
		    id          : 'Win_Ventana_buscar_centro_cotosNiif',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad_niif/grilla_buscar_centro_costos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opcGrillaContable : 'grillaCentroCostosNiif',
					funcion           : 'renderizaResultadoVentanaCentroCostoNiif(id,codigo,nombre)',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_buscar_centro_cotosNiif.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();

	}

	//================== FUNCION PARA RENDERIZAR LOS RESULTADOS DE LA VENTANA DE CENTROS DE COSTOS =======================//
	function renderizaResultadoVentanaCentroCostoNiif(id,codigo,nombre) {
		if (id!='' && codigo!='' && nombre!='') {
			//VALIDAR QUE LAS CUENTAS NO ESTEN YA AGREGADAS
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			var cadenaBuscar='';
			for ( i = 0; i < arrayCodigosCentroCostosNiif.length; i++) {
				if (typeof(arrayCodigosCentroCostosNiif[i])!="undefined" && arrayCodigosCentroCostosNiif[i]!="") {

					if (codigo.indexOf(arrayCodigosCentroCostosNiif[i])==0) {

					  alert("Ya se agrego el Centro de Costos, o el padre del centro de costos");
					  return;
					}
				}
			}

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            var div   = document.createElement('div');
            div.setAttribute('id','fila_centro_costos_'+id);
            div.setAttribute('class','filaBoleta');
            document.getElementById('bodyTablaConfiguracion').appendChild(div);

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            centroCostosConfiguradosNiif[id]='<div class="campo0">'+contCentroCostosNiif+'</div><div class="campo1" id="codigo_'+id+'">'+codigo+'</div><div class="campo2" style="width:143px;" id="nombre_'+id+'" title="'+nombre+'">'+nombre+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;" onclick="eliminaCentroCostos('+id+')" title="Eliminar Centro Costos"></div>';
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('fila_centro_costos_'+id).innerHTML=centroCostosConfiguradosNiif[id];
            contCentroCostosNiif++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosNiif[id]=codigo;
            arrayCodigosCentroCostosNiif[id]=codigo;

		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCentroCostos(cont){

		delete arrayCentroCostosNiif[cont];
		delete arrayCodigosCentroCostosNiif[cont];

		delete centroCostosConfiguradosNiif[cont];
		(document.getElementById("fila_centro_costos_"+cont)).parentNode.removeChild(document.getElementById("fila_centro_costos_"+cont));
	}

</script>