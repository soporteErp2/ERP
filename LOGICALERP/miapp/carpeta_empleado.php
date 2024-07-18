<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");
	
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/	
	
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'carpeta_empleado';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empleados_documentos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'id_empleado ='.$_SESSION['IDUSUARIO'];		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA	
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 940;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 450;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 25;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 85;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,id';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA    
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('id','id',30,'codigo');
			$grilla->AddRow('Tipo Documento','tipo_documento_nombre',250);
			$grilla->AddRow('Fecha de Creacion','fecha_creacion',200,'fecha');
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL  
			$grilla->TituloVentana		= 'Ventana de Prueba'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Registro'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Boton de Prueba','guardar','alert("si")');
			$grilla->AddBotton('Boton de Prueba 2','contrato','alert("si")');
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 350;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 280;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION	
			$grilla->AddTextField('TextField','nombre',150,'true','false');
			$grilla->AddComboBox('ComboBox BD','dato1',150,'true','true','configuracion_zonas,id,nombre,true,true,true');	
			$grilla->AddComboBox('Combobox Manual','dato2',150,'true','false','1:primera opcion,2:segunda opcion,3:tercera opcion');			
			$grilla->AddTextArea('TextArea','dato3',150,80,'true');		
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/	
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/
?>

<script>
function Editar_carpeta_empleado(id){
	window.open('../personal/ver_documento.php?id0='+id+'&id1=<?php echo $_SESSION['IDUSUARIO'];?>');		
}
</script>