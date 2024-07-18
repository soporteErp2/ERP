<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php_debug");
	include("../../misc/MyGrilla/class.MyGrilla.php");
	
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/	
	
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'empresas_formatos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empresas_formatos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'id_empresa ='.$_SESSION['EMPRESA'];		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA	
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 940;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 450;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 75;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre_formato,id';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA    
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Nombre Documento','nombre_formato',450);
			$grilla->AddRow('Extension','ext_formato',150);
			$grilla->AddRowImage('Documento Formato','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../../temas/clasico/images/BotonesTabs/informe0.png?" style="cursor:pointer" width="16" height="16" onclick="Editar_empresas_formatos([id]);"></div></center>',150);
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL  
			$grilla->TituloVentana		= 'Formato de empresa'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Formato'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 350;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 160;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'false';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION	
			$grilla->AddTextField('Formato','nombre_formato',150,'true','false');
			$grilla->AddTextField('id_empresa','id_empresa',200,'true','true',$_SESSION['EMPRESA']);
	
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
function Editar_empresas_formatos(id){
		window.open("descarga_doc.php?id="+id,"","");
		/*Ext.Ajax.request(
				{
					url		: 'bd/bd.php',
					failure	: function(){alert('Error Recuperando el Formato!');},
					params	: {
								op	: "descargaFormato",
								id 	: id
					},
					success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						var ext = resultado[1];
						if(respuesta == 'true'){
							
							ventana=window.open("descarga_doc.php?file="+ext,"","");
							ventana.focus();
						}else{
							alert("Error, No existe o Corrupto");
						
						}
					}
				}
		);*/
	
}

</script>