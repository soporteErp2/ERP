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

	$informe->InformeName			=	'pos';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'POS'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_cartera');

	// CHANGE CSS
	$informe->DefaultCls               = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar            = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='pos'){ $informe->AreaInformeQuitaAlto = 230; }

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

	contConsecutivos=1;

	//================================== GENERAR EL PDF Y EXCEL DESDE LA PAGINA PRINCIPAL ========================================//

	function generarPDF_Excel_principal(tipo_documento){

		cajas='';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConsecutivos.length; i++) {
			if (typeof(arrayConsecutivos[i])!="undefined" && arrayConsecutivos[i]!="") {
				cajas=(cajas=='')? arrayConsecutivos[i] : cajas+','+arrayConsecutivos[i] ;
			}
		}

		MyInformeFiltroFechaFinal  =(typeof(localStorage.MyInformeFiltroFechaFinalPos)!='undefined')? localStorage.MyInformeFiltroFechaFinalPos : '' ;
		MyInformeFiltroFechaInicio =(typeof(localStorage.MyInformeFiltroFechaInicioPos)!='undefined')? localStorage.MyInformeFiltroFechaInicioPos : '' ;
		tipo_fecha_informe_pos     =(typeof(localStorage.tipo_fecha_informe_pos)!='undefined')? localStorage.tipo_fecha_informe_pos : '' ;

		window.open("../informes/informes/pos/pos_Result.php?"+tipo_documento+"=true&nombre_informe=POS&tipo_fecha_informe="+tipo_fecha_informe_pos+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&cajas="+cajas);
	}


	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_pos = new Ext.Window({
		    width       : 445,
		    height      : 450,
		    id          : 'Win_Ventana_configurar_pos',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/pos/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionPos',

		        }
		    },
		    tbar        :
		    [
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
                    handler     : function(){ Win_Ventana_configurar_pos.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalPos  = "";
		localStorage.MyInformeFiltroFechaInicioPos = "";
		localStorage.tipo_fecha_informe_pos        = "";
		arrayConsecutivos.length                   = 0;
		Win_Ventana_configurar_pos.close();
        ventanaConfigurarInforme();

	}
	function generarHtml(){

		var cajas ='';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConsecutivos.length; i++) {
			if (typeof(arrayConsecutivos[i])!="undefined" && arrayConsecutivos[i]!="") {
				cajas=(cajas=='')? arrayConsecutivos[i] : cajas+','+arrayConsecutivos[i] ;
			}
		}

		var elementos = document.getElementsByName("tipo_fecha_informe");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_fecha_informe=elementos[i].value;}
		}

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_fecha_informe=='corte') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_fecha_informe=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{ return; }

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.tipo_fecha_informe_pos        = tipo_fecha_informe;
		localStorage.MyInformeFiltroFechaFinalPos  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioPos = MyInformeFiltroFechaInicio;

		Ext.get('RecibidorInforme_pos').load({
			url     : '../informes/informes/pos/pos_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'POS',
				caja                       : cajas,
				tipo_fecha_informe         : tipo_fecha_informe,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio
			}
		});

		document.getElementById("RecibidorInforme_pos").style.padding = 20;

	}

	function generarPDF_Excel(tipo_documento){

		cajas = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConsecutivos.length; i++) {
			if (typeof(arrayConsecutivos[i])!="undefined" && arrayConsecutivos[i]!="") {
				cajas=(cajas=='')? arrayConsecutivos[i] : cajas+','+arrayConsecutivos[i] ;
			}
		}

		var elementos = document.getElementsByName("tipo_fecha_informe");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_fecha_informe=elementos[i].value;}
		}

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_fecha_informe=='corte') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_fecha_informe=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{ return; }

		window.open("../informes/informes/pos/pos_Result.php?"+tipo_documento+"=true&nombre_informe=POS&tipo_fecha_informe="+tipo_fecha_informe+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&caja="+cajas);

	}

	//========================== VENTANA PARA BUSCAR LAS CAJAS ===============================//
	function ventanaBusquedaCajas(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaBuscarCaja = new Ext.Window({
            width       : 270,
            height      : 400,
            id          : 'Win_VentanaBuscarCaja',
            title       : 'Seleccione las Cajas',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/informes/pos/BusquedaCajas.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFuncion  : '',
                    nombre_grilla : ''
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
                    handler     : function(){ Win_VentanaBuscarCaja.close(id) }
                }
            ]
        }).show();
	}

	//============================ FUNCION PARA ELIMINAR LAS CAJAS AGREGADAS =========================//
	function eliminaCaja(cont){
		delete arrayConsecutivos[cont];
		delete consecutivosConfigurados[cont];
		(document.getElementById("fila_consecutivo_caja_"+cont)).parentNode.removeChild(document.getElementById("fila_consecutivo_caja_"+cont));
	}

</script>