<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Impuestos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'impuestos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 510;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 320;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 750;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 210;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Impuesto','impuesto',200);
			$grilla->AddRow('Valor','valor',100);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 75;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			// $grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			// $grilla->TituloVentana		= 'Administracion Sucursal'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			// $grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			// $grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			// $grilla->VBotonNText		= 'Nueva Sucursal'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			// $grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			// $grilla->AddBotton('Estructura','sucursal','VentanaEstructuraEmpresa();');
			// $grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VAlto		 		= 180;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			// $grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			// $grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('','id_empresa',200,'false','hidden', $id_empresa);
			$grilla->AddTextField('Sucursal','nombre',180,'true','false');
			$grilla->AddComboBox('Departamento','id_departamento',180,'false','true','ubicacion_departamento,id,departamento,true','activo=1 AND id_pais='.$id_pais);
			$grilla->AddComboBox('Ciudad','id_ciudad',180,'false','true','0:Problema al Cargar la base de datos');

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>

	<script>

		function Editar_Impuestos(id) {
			// console.log("id: "+id);
			document.getElementById('itemsGeneral_id_impuesto').value=id;
			var valor=document.getElementById('div_Impuestos_valor_'+id).innerHTML*1;
			var nombre=document.getElementById('div_Impuestos_impuesto_'+id).innerHTML;

			document.getElementById('itemsGeneral_impuesto').value=nombre+' ('+valor+'%)';

			if (!document.getElementById('imgEliminarImpuesto')) {
				var div=document.createElement("div");
				div.setAttribute('onclick','eliminaImpuestoItem("'+id+'","'+nombre+' ('+valor+'%)'+'")');
				div.setAttribute('title','Eliminar Impuesto');
				div.setAttribute('style','width:16px;height:16px;cursor:pointer;background-image: url(\'img/false.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;');
				div.setAttribute('id','imgEliminarImpuesto');

				document.getElementById('itemsGeneral_impuesto').parentNode.appendChild(div);
			}else{
				document.getElementById('imgEliminarImpuesto').setAttribute('onclick','eliminaImpuestoItem("'+id+'","'+nombre+' ('+valor+'%)'+'")');
				document.getElementById('imgEliminarImpuesto').style.backgroundImage="url('img/false.png')";
				document.getElementById('imgEliminarImpuesto').setAttribute('title','Eliminar Impuesto');

			}
			Win_Ventana_buscar_centro_costos.close();
		}
	</script>
<?php
}
 ?>