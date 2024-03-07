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

	$informe->InformeName           =	'certificado_ingresos_retenciones';  //NOMBRE DEL INFORME
	$informe->InformeTitle          =	'Certificado Ingresos y Retenciones Empleados'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode  =	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu      =	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin =	'false';	 //FILTRO FECHA

	// EDIT CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->InformeExportarPDF    = 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS    = 	"false";	//SI EXPORTA A XLS
	$informe->BtnGenera             = 'false';

	$informe->AreaInformeQuitaAncho = 	0;
	$informe->AreaInformeQuitaAlto  = 	170;

	$informe->InformeTamano 		= 	"CARTA-HORIZONTAL";


	$informe->AddBotton('Configurar','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_balance_prueba');


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


	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_medios_magneticos = new Ext.Window({
		    width       : 600,
			height      : 280,
		    id          : 'Win_Ventana_configurar_medios_magneticos',
		    title       : 'Configurar Informe',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/tributario/wizard_certificado_ingreso_retenciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventanaBalanceComprobacion',
		        }
		    },

		}).show();
	}

	function generarHtml(){
		var fecha_inicio       = document.getElementById('fecha_inicial').value
		,	fecha_final        = document.getElementById('fecha_final').value
		,	id_empleado        = document.getElementById('id_empleado').value
		,	documento_empleado = document.getElementById('documento_empleado').innerHTML
		,	nombre_empleado    = document.getElementById('nombre_empleado').innerHTML;

		if (id_empleado==0 || id_empleado=='') { alert('Debe seleccionar el empleado!'); return; }

		Ext.get('RecibidorInforme_certificado_ingresos_retenciones').load({
			url     : '../informes/informes/tributario/certificado_ingresos_retenciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				fecha_inicio : fecha_inicio,
				fecha_final  : fecha_final,
				id_empleado  : id_empleado,
			}
		});

		document.getElementById("RecibidorInforme_certificado_ingresos_retenciones").style.padding = 20;
		localStorage.fecha_inicio_CIRE       = fecha_inicio;
		localStorage.fecha_final_CIRE        = fecha_final;
		localStorage.id_empleado_CIRE        = id_empleado;
		localStorage.documento_empleado_CIRE = documento_empleado;
		localStorage.nombre_empleado_CIRE    = nombre_empleado;

	}

	function generar_Excel(){
		var fecha_inicio       = document.getElementById('fecha_inicial').value
		,	fecha_final        = document.getElementById('fecha_final').value
		,	id_empleado        = document.getElementById('id_empleado').value;

		if (id_empleado==0 || id_empleado=='') { alert('Debe seleccionar el empleado!'); return; }

		var bodyVar = 	'&fecha_inicio='+fecha_inicio+
						'&fecha_final='+fecha_final+
						'&id_empleado='+id_empleado;


		window.open("../informes/informes/tributario/certificado_ingresos_retenciones_Result.php?IMPRIME_XLS=true"+bodyVar);

	}

</script>