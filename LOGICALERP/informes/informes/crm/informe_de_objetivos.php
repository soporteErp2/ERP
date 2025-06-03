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

			$informe->InformeName			=	'informe_de_objetivos';  						//NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Informe de Objetivos o Proyectos';	//TITULO DEL INFORME
			$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
			$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			$informe->InformeExportarPDF	= 	"true";	//SI EXPORTA A PDF
			$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS
			//$informe->AddFiltroEmpresa('true','true','true','false','true');

			$informe->FiltroClientesProspectos = 'false';
			$informe->FiltroFuncionarios       = 'true';

			//$informe->AreaInformeAncho		= 500;
			//$informe->AreaInformeAlto			= 300;
			$informe->AreaInformeQuitaAncho		= 0;
			$informe->AreaInformeQuitaAlto		= 215;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 276;}

			$informe->AddFiltro('Estado','Seleccione el estado del objetivo','[0,"Sin Finalizar"],[1,"Finalizados"]',0);

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>