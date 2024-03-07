<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa=$_SESSION['EMPRESA'];
	$where='';

	if ($nombreTabla=='activos_fijos') {
		// NO CARGAR LOS ACTIVOS QUE NO SE PUEDAN DEPRECIAR MAS Y LOS QUE YA ESTAN EN EL DOCUMENTO
		if ($contabilidad=='colgaap') {
			$campo_depreciacion_acumulada='depreciacion_acumulada';
			$campo_valor_salvamento='valor_salvamento';
		}
		else if ($contabilidad=='niif') {
			$campo_depreciacion_acumulada='depreciacion_acumulada_niif';
			$campo_valor_salvamento='valor_salvamento_niif';
		}
		$where=" AND (costo-$campo_valor_salvamento)>$campo_depreciacion_acumulada ";
		$sqlAF="SELECT id_activo_fijo FROM activos_fijos_depreciaciones_inventario WHERE activo=1 AND id_empresa=$id_empresa AND id_depreciacion=$id_depreciacion";
		$query=mysql_query($sqlAF,$link);
		while ($row=mysql_fetch_array($query)) {
			$where.= " AND id<>".$row['id_activo'];
		}
	}

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $nombre_grilla;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $nombreTabla;		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa=$id_empresa  $sql $where"; //.$condicional;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 755;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 355;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,code_bar,nombre_equipo,grupo,subgrupo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA

			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Codigo de Barras','code_bar',100);
			$grilla->AddRow('Nombre Item','nombre_equipo',350);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRowImage('Subgrupo','<div>subgrupo</div><div id="ventas_id_item_[id]" style="display:none;" >[id_item]</div><div id="depreciacion_acumulada_[id]" style="display:none;" >[depreciacion_acumulada]</div>',200);
			//$grilla->AddRow('Departamento','departamento',200);
			if ($nombreTabla!='activos_fijos') {
				$grilla->AddRowImage('Unidad de Medida','<div id="unidad_medida_grilla_[id]">[unidad_medida] x [cantidad_unidades]</div><div id="ventas_id_item_[id]" style="display:none;" >[id_item]</div><div id="div_'.$nombre_grilla.'_costos_[id]" style="display:none;" >[costos]</div>','120');
				$grilla->AddRow('Stock','cantidad',80);
				$grilla->AddRow('Precio','precio_venta',80);
				$grilla->AddRow('Costo','costos',80);
			}
			else{
				$grilla->AddRow('Unidad','unidad',150);
				$grilla->AddRow('Costo','costo',200);
			}


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

		function Editar_<?php echo $nombre_grilla; ?>(id){

			<?php
			echo $cargaFuncion; ?>
		}

	</script>


<?php
} ?>
