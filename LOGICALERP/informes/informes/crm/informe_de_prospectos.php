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

			$informe->InformeName			=	'informe_de_prospectos'; //NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Informe de Prospectos';//TITULO DEL INFORME
			//$informe->AddFiltroEmpresa('true','true','true','false','true');
			//$informe->FiltroClientes        = 'true';
			/*$informe->InformeEmpreSucuBode	=	'false';  //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'true'; //FILTRO EMPRESA, SUCURSAL
			$informe->FiltroEmpreTodos      =   'false'; //OPCION TODOS EN EL FILTRO DE EMPRESA
			$informe->FiltroSucuTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE SUCURSAL
			$informe->FiltroBodeTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE BODEGA*/
			//$informe->InformeDebug  = 'true';
			//$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			$informe->InformeExportarPDF    = 	"true";	 //SI EXPORTA A PDF
			$informe->InformeExportarXLS    = 	"true"; //SI EXPORTA A XLS
			$informe->AreaInformeQuitaAncho = 0;
			$informe->AreaInformeQuitaAlto  = 215;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 276;}

			$informe->InformeTamano      = "OFICIO-HORIZONTAL";			
			$informe->FiltroFuncionarios = 'true';			


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>