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

	$informe->InformeName			=	'clientes';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Informe Clientes'; //TITULO DEL INFORME
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

	function generarPDF_Excel_principal(tipo_documento){
		window.open("../informes/informes/contabilidad/contabilidad_balance_general_Result.php?"+tipo_documento+"=true&nombre_informe=Balance General&$tipo_balance==comprobacion");
	}

	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//

	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_balance_general = new Ext.Window({
		    width       : 400,
		    height      : 350,
		    id          : 'Win_Ventana_configurar_balance_general',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/bd.php',
		        scripts : true,
		        // text    : 'cargando...	',
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionBalanceGeneral',

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
		                    handler     : function(){ Win_Ventana_configurar_balance_general.close() }
		                }
		            ]
		        },
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Generacion de Informe',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Generar Informe',
		                    scale       : 'large',
		                    iconCls     : 'genera_informe',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarHtml() }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Exportar a PDF',
		                    scale       : 'large',
		                    iconCls     : 'genera_pdf',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Exportar a Excel',
		                    scale       : 'large',
		                    iconCls     : 'excel32',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarPDF_Excel('IMPRIME_XLS') }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function generarHtml(){

		var elementos = document.getElementsByName("tipo_balance");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_balance=elementos[i].value;}
		}

		generar=document.getElementById('nivel_cuenta').value;
		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance=='comprobacion') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance=='comparativo') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{
			return;
		}

		Ext.get('RecibidorInforme_contabilidad_balance_general').load({
			url     : '../informes/informes/contabilidad/contabilidad_balance_general_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Balance General',
				tipo_balance               : tipo_balance,
				generar                    : generar,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_balance_general").style.padding = 20;

		localStorage.tipo_balance               = tipo_balance;
		localStorage.generar                    = generar;
		localStorage.MyInformeFiltroFechaFinal  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicio = MyInformeFiltroFechaInicio;

	}

	function generarPDF_Excel(tipo_documento){

		var elementos = document.getElementsByName("tipo_balance");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_balance=elementos[i].value;}
		}

		generar=document.getElementById('nivel_cuenta').value;
		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance=='comprobacion') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance=='comparativo') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{
			return;
		}

		window.open("../informes/informes/contabilidad/contabilidad_balance_general_Result.php?"+tipo_documento+"=true&nombre_informe=Balance General&generar="+generar+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&tipo_balance=comprobacion&tipo_balance="+tipo_balance);

	}


</script>