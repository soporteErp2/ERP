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
	
			$informe->InformeName				=	'informe_contratos';  //NOMBRE DEL INFORME
			$informe->InformeTitle				=	'Informe de Contratos'; //TITULO DEL INFORME
			$informe->InformeEmpreSucuBode		=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu			=	'true';	 //FILTRO EMPRESA, SUCURSAL
			$informe->InformeFechaInicioFin		=	'true';	 //FILTRO FECHA
			
			$informe->InformeExportarPDF		= 	"true";	//SI EXPORTA A PDF
			$informe->InformeExportarXLS		= 	"false";	//SI EXPORTA A XLS
			
			//$informe->FuncionGenerarCustom		=	"alert('si');";

			$informe->InformeTamano = "CARTA-HORIZONTAL";
			
			//$informe->AreaInforme				= 	"false";
			//$informe->AreaInformeAncho		= 	500;
			//$informe->AreaInformeAlto			= 	300;
			$informe->AreaInformeQuitaAncho		= 	0;
			$informe->AreaInformeQuitaAlto		= 	190;
			if($modulo=='personal'){$informe->AreaInformeQuitaAlto = 274;}	

			$informe->AddFiltro('Estado','Seleccione ...','[0,"Activo"],[1,"Bloqueado"]',0);					
	
	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/	
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/		

	
?>