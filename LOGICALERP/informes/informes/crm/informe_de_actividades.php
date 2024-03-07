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

			$informe->InformeName              =	'informe_de_actividades'; //NOMBRE DEL INFORME
			$informe->InformeTitle             =	'Informe de Actividades';//TITULO DEL INFORME

			$informe->FiltroClientesProspectos =   'true';
			$informe->FiltroFuncionarios       =   'true';

			//$informe->AddFiltroField('HOLAAA','Escriba algo','','true');
			//$informe->AddFiltroEmpresa('true','false','true','false','true');
			//$informe->FiltroClientes        = 'true';
			/*$informe->InformeEmpreSucuBode	=	'false';  //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'true'; //FILTRO EMPRESA, SUCURSAL
			$informe->FiltroEmpreTodos      =   'false'; //OPCION TODOS EN EL FILTRO DE EMPRESA
			$informe->FiltroSucuTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE SUCURSAL
			$informe->FiltroBodeTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE BODEGA*/
			//$informe->InformeDebug  = 'true';
			$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			$informe->InformeExportarPDF	= 	"true";	 //SI EXPORTA A PDF
			$informe->InformeExportarXLS	= 	"true"; //SI EXPORTA A XLS
			$informe->AreaInformeQuitaAncho		= 0;
			$informe->AreaInformeQuitaAlto		= 215;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 276;}

			$informe->InformeTamano         = "CARTA-HORIZONTAL";

			/* COMBOX PERSONALIZADO*/
			// $consul1 = $mysql->query("SELECT id,CONCAT(apellido1,' ',apellido2,' ',nombre1)AS nombre FROM empleados WHERE activo = 1  AND vendedor='true' ORDER BY apellido1 ",$link);
			// $array1 = '';
			// while($row1 = $mysql->fetch_array($consul1)){
			// 	$array1 .= '["'.$row1['id'].'","'.$row1['nombre'].'"],';
			// }
			// $informe->AddFiltro('Vendedor','Seleccione el Vendedor',trim($array1,','),0);

			/* COMBOX PERSONALIZADO*/

			$informe->AddFiltro('Estado','Seleccione el estado de las actividades','[0,"Pendientes"],[1,"Finalizadas"]',0);

			// /* COMBOX PERSONALIZADO*/
			// $consul2 = $mysql->query("SELECT id,CONCAT(codigo,' - ',nombre)AS nombre FROM configuracion_proyectos WHERE activo = 1",$link);
			// $array2 = '';
			// while($row2 = $mysql->fetch_array($consul2)){
			// 	$array2 .= '["'.$row2['id'].'","'.$row2['nombre'].'"],';
			// }
			// $informe->AddFiltro('Proyecto','Seleccione el Proyecto',trim($array2,','),0);

			//  COMBOX PERSONALIZADO
			// $consul3 = $mysql->query("SELECT id,CONCAT(codigo_proyecto,codigo,' - ',nombre)AS nombre FROM configuracion_proyectos_actividades WHERE activo = 1",$link);
			// $array3 = '';
			// while($row3 = $mysql->fetch_array($consul3)){
			// 	$array3 .= '["'.$row3['id'].'","'.$row3['nombre'].'"],';
			// }
			// $informe->AddFiltro('Actividad','Seleccione la Actividad',trim($array3,','),0);


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>