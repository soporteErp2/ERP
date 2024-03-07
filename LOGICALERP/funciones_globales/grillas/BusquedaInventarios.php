<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$QuitarAncho		=($QuitarAncho>0)? $QuitarAncho : 150;
	$QuitarAlto			=($QuitarAlto>0)? $QuitarAlto	 : 170;

	$id_empresa = $_SESSION['EMPRESA'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $nombre_grilla;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $nombreTabla;		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' ".$sql; //.$condicional;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 755;			//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 355;			//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= $QuitarAncho;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= $QuitarAlto;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,code_bar,nombre_equipo,grupo,subgrupo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA

			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Codigo de Barras','code_bar',100);
			$grilla->AddRow('Nombre del Activo','nombre_equipo',250);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRow('Subgrupo','subgrupo',200);
			$grilla->AddRow('Costo','costos',100);
			//$grilla->AddRow('Departamento','departamento',200);
			$grilla->AddRowImage('Unidad de Medida','<div id="unidad_medida_grilla_[id]">[unidad_medida] x [cantidad_unidades]</div><div id="id_impuesto_[id]" style="display:none;">[id_impuesto]</div><input type="hidden" id="clasificacion_[id]" data-familia="[id_familia]" data-grupo="[id_grupo]" data-subgrupo="[id_subgrupo]"> ','120');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 150;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana			= 'Clientes'; 		//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar	= 'false';
			$grilla->VBarraBotones			= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

   // echo '<script>alert("'.$grilla->MyWhere.'")</script>';

if(!isset($opcion)) {?>

	<script>

		function Editar_<?php echo $nombre_grilla; ?>(id){ <?php echo $cargaFuncion; ?> }

	</script>


<?php
} ?>