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

			$informe->InformeName			=	'formato_visitas';  						//NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Formato de Visitas';	//TITULO DEL INFORME
			$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
			$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			$informe->InformeFechaInicio	= 	'true';
			$informe->InformeFechaFin		= 	'false';

			$informe->InformeExportarPDF	= 	"true";	//SI EXPORTA A PDF
			$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS
			$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR
			$informe->AreaInformeQuitaAncho		= 0;
			$informe->AreaInformeQuitaAlto		= 215;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 276;}

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>