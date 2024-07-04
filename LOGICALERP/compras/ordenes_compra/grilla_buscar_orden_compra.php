<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
	/**//////////////////////////////////////////////**/
	/**/// 	  	 INICIALIZACION DE LA CLASE  	    ///**/
	/**/																						/**/
	/**/				  $grilla = new MyGrilla();				  /**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'ordenesCompra';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'compras_ordenes';	//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere				= "activo = 1 AND proveedor <> '' AND id_sucursal = $id_sucursal AND id_bodega = $filtro_bodega AND id_empresa='$id_empresa' AND (id IN(SELECT id_orden_compra FROM compras_ordenes_inventario WHERE activo=1) OR consecutivo>0)";
			$grilla->OrderBy				= 'id DESC';					//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';						//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			$grilla->QuitarAncho		= 145;			//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 170;			//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar						= 'true';		//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda			= 'consecutivo,nit,proveedor,fecha_registro,consecutivo_siip';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '';				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoOrdenCompra_[id]" /></center>','50');
			$grilla->AddRow(utf8_decode('Doc N°'),'consecutivo',50);
			$grilla->AddRow(utf8_decode('N° SIIP'),'consecutivo_siip',50);
			$grilla->AddRow('Unidades','pendientes_facturar',80);
			$grilla->AddRow('Nit','nit',120);
			$grilla->AddRow('Proveedor','proveedor',200);
			$grilla->AddRow('Fecha','fecha_registro',200,'fecha');
			$grilla->AddRowImage('Autorizada','<center><image src="../personal/images/[autorizado].png" alt ="[autorizado]" id="imgValidaOrdenCompra_[id]" /></center>',70);
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho			= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		  = 'false';					//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	  = 'Ventana Reuniones Coope'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones	  = 'false';					//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		  = 'false';					//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		  = 'Nueva Reunion'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		  = 'addcontactos';		//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		  = 'true';						//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 			  = 400;							//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 			  = 200;							//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		  = 70;								//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		  = 160;							//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'true';						//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';						//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';						//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**//////////////////////////////////////////////////////////////**/
		/**///								 INICIALIZACION DE LA GRILLA	  			  ///**/
		/**/																														/**/
		/**/		 $grilla->Link = $link;  			//Conexion a la BD			  /**/
		/**/		 $grilla->inicializa($_POST);	//Variables POST				  /**/
		/**/		 $grilla->GeneraGrilla(); 		//Inicializa la Grilla	  /**/
		/**/																														/**/
		/**//////////////////////////////////////////////////////////////**/

	if(!isset($opcion)){  ?>
		<script>
			function Editar_ordenesCompra(id){
				var direccionRender = 'ordenes_compra/ordenes_compra_bloqueada.php'
				,	estado          = document.getElementById('imgEstadoOrdenCompra_'+id).getAttribute('src')
				,	consecutivoDoc  = document.getElementById('div_ordenesCompra_consecutivo_'+id).innerHTML
				,	validada        = document.getElementById('imgValidaOrdenCompra_'+id).getAttribute('src');

				if(estado == 'img/estado_doc/0.png'){ direccionRender = 'ordenes_compra/ordenes_compra.php' }
				if((estado == 'img/estado_doc/0.png')){ Ext.getCmp('Btn_guardar_orden_compra').enable(); }
				else{ Ext.getCmp('Btn_guardar_orden_compra').disable(); }
				if(consecutivoDoc != '' && estado != 'img/estado_doc/3.png'){ document.getElementById('titleDocuementoOrdenCompra').innerHTML='Orden de compra<br>N. '+consecutivoDoc; }

				else if(consecutivoDoc != '' && estado == 'img/estado_doc/3.png'){  document.getElementById('titleDocuementoOrdenCompra').innerHTML='<span style="color:red;font-size:18px;font-weight: bold;}">Orden de compra<br>N. '+consecutivoDoc+'</span>';}
				else{ document.getElementById('titleDocuementoOrdenCompra').innerHTML=''; }

			  var validado = '';

			  //====================== CONTROL DEL BOTON VALIDAR =====================//

			  if(validada == '../personal/images/false.png' && estado != 'img/estado_doc/3.png'){Ext.getCmp('Btn_validar_orden_compra').enable();}
	      else {Ext.getCmp('Btn_validar_orden_compra').disable();}

				//====================== CONTROL DEL BOTON ANEXAR ======================//

	      if(estado == 'img/estado_doc/3.png'){  Ext.getCmp('Btn_upload_orden_compra').disable(); }
	      else{ Ext.getCmp('Btn_upload_orden_compra').enable(); }

	      Ext.get("contenedor_ordenes_compra").load({
					url     : direccionRender,
					scripts : true,
					nocache : true,
					params  :	{
											id_orden_compra : id,
											filtro_bodega   : '<?php echo $filtro_bodega; ?>'
										}
				});

	    	// Ext.Ajax.request({
	      //   url    	: 'ordenes_compra/bd/bd.php',
	      //   method  : 'GET',
	      //   params  : {
				// 		        	opc         : 'btnValidarOrdenCompra',
				// 		    			consecutivo : consecutivoDoc
				// 		        },
	      //   success : function(response){
				// 																var responseJson = response.responseText
				// 																,	arrayJson    = JSON.parse(responseJson)
				// 																,	validate     = arrayJson.validate
				// 																,	estado       = arrayJson.estado;
				// 																botonValidar(validate,estado);
	      //           										},
	      //   failure : function(){ console.log("fail"); }
	    	// });

			  Win_Ventana_buscar_orden_compra.close();
			}

			function botonValidar(valor1,valor2){
		    // if(valor1 == "false" && valor2 != "3"){Ext.getCmp('Btn_validar_orden_compra').enable();}
		    // else {Ext.getCmp('Btn_validar_orden_compra').disable();}
				//
		    // if(valor2 == "3"){
		    //  	Ext.getCmp('Btn_upload_orden_compra').disable();
		    // }
		    // else{
				// 	Ext.getCmp('Btn_upload_orden_compra').enable();
				// }
				//
		    // Win_Ventana_buscar_orden_compra.close();
			}
		</script>
	<?php
	} ?>
