<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];

	error_reporting(1);

	include('../../../../misc/MyInforme/class.MyInforme.php');
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

			$informe->InformeName			=	'informe_de_objetivos_listado';  						//NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Informe General de Objetivos';	//TITULO DEL INFORME
			//$informe->BtnGenera             =   'false';
			//$informe->AddFiltroEmpresa('true','true','true','false','true');
			$informe->FiltroClientesProspectos = 'true';
			/*$informe->InformeEmpreSucuBode	=	'false';  //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'true'; //FILTRO EMPRESA, SUCURSAL
			$informe->FiltroEmpreTodos      =   'false'; //OPCION TODOS EN EL FILTRO DE EMPRESA
			$informe->FiltroSucuTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE SUCURSAL
			$informe->FiltroBodeTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE BODEGA*/
			//$informe->InformeDebug  = 'true';
			$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			//$informe->InformeExportarPDF    = 	"true";	 //SI EXPORTA A PDF
			$informe->InformeExportarXLS    = 	"true"; //SI EXPORTA A XLS			
			$informe->AreaInformeQuitaAncho = 0;
			$informe->AreaInformeQuitaAlto  = 215;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 276;}
			$informe->AddFiltro('Probabilidad','Seleccione la Probabilidad','[0,"Alta"],[1,"Media"],[2,"Baja"]',0);
			//$informe->AddFiltro('Estado','Seleccione el Estado','[0,"Prospecto"],[1,"Oportunidad"],[2,"Propuesta"],[3,"Cierre"],[4,"Retirado"]',0);
			//$informe->AddFiltro('Estado','Seleccione el Estado','[1,"Prospecto"],[2,"Oportunidad"],[3,"Propuesta"],[4,"Cierre"],[5,"Retirado"]',0);

			$consul1 = $mysql->query("SELECT id,nombre FROM configuracion_estados_proyectos WHERE activo = 1 AND id_empresa = '$id_empresa'",$link);
			$array1 = '';
			while($row1 = $mysql->fetch_array($consul1)){
				$array1 .= '["'.$row1['id'].'","'.$row1['nombre'].'"],';
			}
			$informe->AddFiltro('Estado','Seleccione el Estado',trim($array1,','),0);	

			$consul2 = $mysql->query("SELECT id,nombre FROM configuracion_lineas_negocio WHERE activo = 1 AND id_empresa = '$id_empresa'",$link);
			$array2 = '';
			while($row2 = $mysql->fetch_array($consul2)){
				$array2 .= '["'.$row2['id'].'","'.$row2['nombre'].'"],';
			}
			$informe->AddFiltro('Linea','Seleccione la linea',trim($array2,','),0);			


			$informe->InformeTamano         = "OFICIO-HORIZONTAL";
			$informe->FiltroFuncionarios    = 'true';
			

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>