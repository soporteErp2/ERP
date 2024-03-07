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

			$informe->InformeName			=	'personal_informe_de_asistencia';  //NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Informe de Asistencia de Personal'; //TITULO DEL INFORME
			$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'true';	 //FILTRO EMPRESA, SUCURSAL
			$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA

			$informe->InformeExportarPDF	= 	"true";	//SI EXPORTA A PDF
			$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

			//$informe->AreaInformeAncho		= 500;
			//$informe->AreaInformeAlto			= 300;
			$informe->AreaInformeQuitaAncho		= 0;
			$informe->AreaInformeQuitaAlto		= 190;
			if($modulo=='personal'){$informe->AreaInformeQuitaAlto = 274;}

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>