<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];


	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'deterioros';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'deterioro_cartera_clientes';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa' AND (estado<>3 OR consecutivo>0)";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 380;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 750;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 210;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png"  style="cursor:pointer" width="16" height="16" id="imgEstado'.$opcGrillaContable.'_[id]" data-value="[estado]" /></center>','50');
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('Fecha','fecha',80);
			$grilla->AddRow('Tasa de descuento','tasa_descuento',100);
			$grilla->AddRow('Rotacion','rotacion',70);
			$grilla->AddRow('Sucursal','sucursal',100);
			$grilla->AddRow('Usuario','nombre_usuario',250);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 75;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion Sucursal'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Nuevo','add_new','nuevo_deterioro();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 180;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

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
		function Editar_deterioros(id) {
			nuevo_deterioro(id);
		}

		function nuevo_deterioro(id){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			var estado = (document.getElementById('imgEstado<?php echo $opcGrillaContable ?>_'+id))? document.getElementById('imgEstado<?php echo $opcGrillaContable ?>_'+id).dataset.value : 0 ;
			var	url = (estado==0)? 'deterioro_cartera_clientes/deterioro_cartera_clientes.php' : 'deterioro_cartera_clientes/deterioro_cartera_clientes_bloqueado.php' ;

			Win_Ventana_nuevo_deterioro = new Ext.Window({
			    width       : myancho-50,
			    height      : myalto-50,
			    id          : 'Win_Ventana_nuevo_deterioro',
			    title       : '',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : url,
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opcGrillaContable    : 'DeterioroCarteraClientes',
						id_deterioro_cliente : id,
			        }
			    }
			}).show();
		}

	</script>
<?php
}
else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>
	<script>


    </script>

<?php } ?>