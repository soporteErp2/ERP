<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	error_reporting(1);

	include('../../../../misc/MyInforme/class.MyInforme.php');
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

			$informe->InformeName			=	'informe_de_objetivos_estadistico';  	//NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Informe estadistico de Objetivos';	//TITULO DEL INFORME
			
			// $informe->InformeEmpreSucuBode	=	'false';  //FILTRO EMPRESA, SUCURSAL, BODEGA
			// $informe->InformeEmpreSucu		=	'true'; //FILTRO EMPRESA, SUCURSAL
			// $informe->FiltroEmpreTodos      =   'false'; //OPCION TODOS EN EL FILTRO DE EMPRESA
			// $informe->FiltroSucuTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE SUCURSAL
			// $informe->FiltroBodeTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE BODEGA
			$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal()','Btn_exportar_pdf');

			$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			//$informe->InformeExportarPDF	= 	"true";	 //SI EXPORTA A PDF			

			$informe->InformeExportarXLS	= 	"false"; //SI EXPORTA A XLS
			$informe->AreaInformeQuitaAncho		= 0;
			$informe->AreaInformeQuitaAlto		= 215;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 275;}		
			

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

	function generarPDF_Excel_principal(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal_informe_de_objetivos_estadistico').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio_informe_de_objetivos_estadistico').value;
		window.open("../informes/informes/crm/informe_de_objetivos_estadistico_PDF.php?IMPRIME_PDF=true&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio);
	}

</script>