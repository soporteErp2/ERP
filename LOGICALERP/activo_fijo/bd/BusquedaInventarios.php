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

	// VALIDAR QUE NO MUESTRE LOS ACTIVOS CARGADOS AL DOCUMENTO
	if ($option=='deterioro') {
		$sql1="SELECT id_activo_fijo FROM activos_fijos_deterioro_inventario WHERE activo=1 AND id_deterioro=$id_deterioro";
		$query=mysql_query($sql1,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereAF .=  ' AND id<>'.$row['id_activo_fijo'] ;
		}
		// $whereAF = ($whereAF<>'')? ' AND ('.$whereAF.')' : '' ;
	}
	else{
		$sql1="SELECT id_activo_fijo FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_depreciacion=$id_depreciacion";
		$query=mysql_query($sql1,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereAF .= ($whereAF=='')? 'id<>'.$row['id_activo_fijo'] : ' AND id<>'.$row['id_activo_fijo'] ;
		}
		$whereAF = ($whereAF<>'')? " AND ($whereAF) AND depreciable='Si'" : " AND depreciable='Si' " ;
	}





	$QuitarAncho = ($QuitarAncho>0)? $QuitarAncho : 150;
	$QuitarAlto	 = ($QuitarAlto>0)? $QuitarAlto	 : 170;

	$id_empresa = $_SESSION['EMPRESA'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $nombre_grilla;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $nombreTabla;		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND estado='1'  $whereAF $sql"; //.$condicional;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
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
			$grilla->AddRow('Codigo','codigo_activo',80);
			$grilla->AddRow('Codigo de Barras','code_bar',100);
			$grilla->AddRow('Nombre del Activo','nombre_equipo',250);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRow('Subgrupo','subgrupo',200);
			$grilla->AddRow('Costo','costo',100);
			if ($option=='deterioro') {
				$grilla->AddRow('Deterioro','deterioro_acumulado',100);
			}
			else{
				$grilla->AddRow('Depreciacion','depreciacion_acumulada',100);
			}

			//$grilla->AddRow('Departamento','departamento',200);
			$grilla->AddRowImage('Unidad de Medida','<div id="unidad_medida_activo_[id]">[unidad] x [numero_piezas]</div><div id="id_activo_[id]" style="display:none;" >[id_item]</div><div id="id_impuesto_[id]" style="display:none;">[id_impuesto]</div>','120');

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