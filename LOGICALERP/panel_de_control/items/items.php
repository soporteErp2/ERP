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

	$id_empresa = $_SESSION["EMPRESA"];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'itemsGeneral';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'items';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa'";	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy 			= 'codigo ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre_equipo,familia,grupo,subgrupo';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

			$grilla->Gfilters			= 'true';
			$grilla->GfiltersAutoOpen	= 'false';
			$grilla->AddFilter('Disponible','estado_venta','estado_venta');
			$grilla->AddFilter('Familia','familia','familia');
			$grilla->AddFilter('Grupo','grupo','grupo');
			$grilla->AddFilter('Subgrupo','subgrupo','subgrupo');
			$grilla->AddFilter('Tipo de Documento','id_tipo_identificacion','tipo_identificacion');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Nombre','nombre_equipo',250);
			$grilla->AddRow('Familia','familia',200);
			$grilla->AddRow('Grupo','grupo',200);
			$grilla->AddRow('Subgrupo','subgrupo',200);
			$grilla->AddRowImage('Venta','<center><img src="img/[estado_venta].png"></center>','80');
			$grilla->AddRowImage('Compra','<center><img src="img/[estado_compra].png"></center>','80');

 		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
	 		$grilla->FContenedorAncho		= 500;
			$grilla->FColumnaGeneralAncho	= 530;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 280;
			$grilla->FColumnaFieldAncho		= 250;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana			= 'Ventana itemsGeneral'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->CerrarDespuesDeEditar  = 'false';
			$grilla->VBarraBotones			= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText			= 'Nuevo Item'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage			= 'addequipo';		//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize			= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 			= 100;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 			= 100;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 100;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto			= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar			= 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar		= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->AddBotton('Descargar Formato','excel32',"window.open('items/bd/formato_items.xls')");
			$grilla->AddBotton('Cargar Items','excel32','windows_upload_excel();');
			$grilla->AddBotton('Descargar Items','excel32','descargar_items_excel();');

		//BOTONES ADICIONALES EN EL TOOLBAR DE LA VENTANA DE INSERT DELETE Y UPDATE
 			$grilla->AddBottonVentana('Eliminar','eliminar','ventana_eliminar_campo_inventario()','false','true');

 		// //CONFIGURACION DEL MENU CONTEXTUAL
 		// 	$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 	// 	$grilla->MenuContextEliminar= 'false';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
			// $grilla->AddMenuContext('Imprimir Codigo de Barras','barcode16','ventana_codigo_barras([id])');
			// $grilla->AddMenuContext('Documento itemsGeneral','doc','inventario_documentos([id])');
			// $grilla->AddMenuContext('Historico itemsGeneral','doc','historico_inventario([id])');

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION

			//=======================// FAMILIA, GRUPO, SUBGRUPO //=======================//
			$grilla->AddSeparator('Clasificaci&oacute;n');

			$grilla->AddTextField('','id_empresa',240,'false','hidden', $id_empresa);
			$grilla->AddTextField('','id_usuario_creacion',240,'false','hidden', $_SESSION['IDUSUARIO']);

			$grilla->AddComboBox ('Familia','id_familia',240,'true','true','items_familia,id,nombre,true','activo = 1 AND id_empresa="'.$id_empresa.'" ORDER BY nombre ASC');
            $grilla->AddComboBox ('Grupo','id_grupo',240,'true','true','items_familia_grupo,id,nombre,true','activo = 2 ORDER BY nombre ASC');
			$grilla->AddComboBox ('Subgrupo','id_subgrupo',240,'true','true','items_familia_grupo_subgrupo,id,nombre,true','activo = 2 ORDER BY nombre ASC');
			$grilla->AddTextField('','id_empresa',180,'true','true', $id_empresa);

			//=======================// INFORMACION GENERAL //=======================//
			$grilla->AddSeparator('Informaci&oacute;n General');
			$grilla->AddTextField('Nombre Del Item','nombre_equipo',240,'true','false');
			$grilla->AddValidation('nombre_equipo','mayuscula');

			//CODIGO ITEM
			$grilla->AddComboBox('Codigo Automatico','codigo_auto',240,'true','false','true:Si,false:No');
			$grilla->AddTextField('Codigo','codigo',240,'true','false');
			$grilla->AddValidation('codigo','unico_global','id_empresa='.$id_empresa);

			$grilla->AddTextField('Codigo de Barras','code_bar',240,'false','false');

			//INVENTARIABLE BOLEANO
			$grilla->AddComboBox('Inventariable','inventariable',240,'true','false','true:Si,false:No');

			$grilla->AddComboBox('Item en Compra','estado_compra',240,'true','false','true:Si,false:No');
			//$grilla->AddComboBox('Opciones compra','opcional_compra',240,'false','false','activo_fijo:Activo fijo,gasto:Costo-Gasto');

			$grilla->AddComboBox('Item disponible en Venta','estado_venta',240,'true','false','true:Si,false:No');
			$grilla->AddComboBox('Disponible en punto de venta (POS)','modulo_pos',240,'true','false','true:Si,false:No');
			$grilla->AddComboBox('Disponible en minbar','minibar',240,'true','false','true:Si,false:No');
			$grilla->AddComboBox('Item de produccion','item_produccion',240,'true','false','true:Si,false:No');
			$grilla->AddComboBox('Item de Transformacion','item_transformacion',240,'true','false','true:Si,false:No');

			$grilla->AddTextField('','id_item_transformacion',180,'true','true');
			$grilla->AddTextField('Item a Transformar','nombre_item_transformacion',240,'true','false');
			$grilla->AddTextField('Cantidad de Transformacion','cantidad_transformacion',240,'true','false');

			$grilla->AddComboBox ('Unidad de medida','id_unidad_medida',240,'true','true','inventario_unidades,id,nombre','activo = 1 AND id_empresa='.$id_empresa);
			$grilla->AddTextField('Marca','marca',240,'false','false');
			$grilla->AddTextField('Modelo','modelo',240,'false','false');
			$grilla->AddTextField('Color','color',240,'false','false');
			$grilla->AddTextField('Cantidad de Piezas','numero_piezas',240,'false','false');
			$grilla->AddTextField('Cantidad minima en stock','cantidad_minima_stock',240,'false','false');
			$grilla->AddTextField('Cantidad maxima en stock','cantidad_maxima_stock',240,'false','false');
			$grilla->AddValidation('numero_piezas','numero');
			$grilla->AddTextArea('Descripci&oacute;n 1','descripcion1',240,50,'false');
			$grilla->AddTextArea('Descripci&oacute;n 2','descripcion2',240,50,'false');

			//=======================// INFORMACION DE COMPRAS //=======================//
			$grilla->AddSeparator('Configuraci&oacute;n Compra');
			$grilla->AddComboBox('Activo Fijo','opcion_activo_fijo',240,'false','false','true:Si,false:No');
			$grilla->AddComboBox('Costo','opcion_costo',240,'false','false','true:Si,false:No');
			$grilla->AddComboBox('Gasto de Venta','opcion_gasto',240,'false','false','true:Si,false:No');

			$grilla->AddTextField('','id_centro_costos',240,'false','hidden', '');
			$grilla->AddTextField('Centro de Costos','centro_costos',240,'false','false');
			// $grilla->AddComboBox ('Centro de Costos','id_centro_costos',240,'false','true','centro_costos,id,nombre,true','activo = 1 AND id_empresa='.$id_empresa);

			//=======================// INFORMACION DE POS //=======================//
			$grilla->AddSeparator('Configuracion POS');
			// $grilla->AddTextField('Centro de produccion','id_bodega_produccion',240,'false','false');
			$grilla->AddTextField('Codigo Transaccion','codigo_transaccion',240,'false','false');
			$grilla->AddComboBox ('Centro de produccion','id_bodega_produccion',240,'false','true','empresas_sucursales_bodegas,id,nombre','activo = 1 AND id_empresa='.$id_empresa);
			$grilla->AddComboBox('Activo en POS','activo_pos',240,'false','false','1:Si,2:No');
			$grilla->AddComboBox ('Termino','id_termino',240,'false','true','items_terminos,id,nombre','activo = 1 AND id_empresa='.$id_empresa);
			$grilla->AddTextField('Precion venta 1','precio_venta_1',240,'false','false');
			$grilla->AddTextField('Precion venta 2','precio_venta_2',240,'false','false');
			$grilla->AddTextField('Precion venta 3','precio_venta_3',240,'false','false');
			$grilla->AddTextField('Precion venta 4','precio_venta_4',240,'false','false');
			$grilla->AddTextField('Precion venta 5','precio_venta_5',240,'false','false');

			//=======================// INFORMACION DE ASISTE //=======================//
			$grilla->AddSeparator('Configuracion Asiste');
			$grilla->AddComboBox('Disponible Asiste','disponible_asiste',240,'false','false','true:Si,false:No');
			$grilla->AddComboBox('Categoria Asiste','id_categoria_asiste',240,'false','false','true:Si,false:No');

			//=======================// INFORMACION CONTABLE //=======================//
			$grilla->AddSeparator('Informaci&oacute;n Contable');
			$grilla->AddTextField('','id_impuesto',240,'false','hidden', '');
			$grilla->AddTextField('Impuesto','impuesto',240,'false','false');
			// $grilla->AddComboBox ('Impuesto','id_impuesto',240,'false','true','impuestos,id,impuesto','activo = 1 AND id_empresa='.$id_empresa.' ORDER BY impuesto DESC');

			// $grilla->AddTextField('id_proveedor','id_proveedor',240,'false','true');//oculto
			// $grilla->AddTextField('Proveedor','proveedor',240,'true','false');
			// $grilla->AddTextField('Codigo asignado por proveedor','codigo_asignado_por_proveedor',240,'false','false');
			$grilla->AddTextField('Costo en compra','costos',240,'true','false');
			$grilla->AddTextField('Precio de Venta (Sin iva)','precio_venta',240,'true','false');
			$grilla->AddTextField('Vida Util (en meses)','vida_util',240,'true','false');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


if($opcion == 'Vupdate' || $opcion == 'Vagregar'){ ?>
	<script>

		if ('<?php echo $opcion; ?>'=='Vupdate') {
			//Ext.getCmp('pestana_receta').disable()
			if (document.getElementById('itemsGeneral_item_produccion').value=='false') {
				Ext.getCmp('pestana_receta').disable();
			}
			else{
				Ext.getCmp('pestana_receta').enable();
			}
		}

		divOcultarCentroCostos     = document.getElementById('itemsGeneral_centro_costos').parentNode.parentNode;
		inputCentroCostos          = document.getElementById('itemsGeneral_centro_costos');
		inputCentroCostos.readOnly = true;
		inputCentroCostos.setAttribute("onclick","ventanaBuscarCentroCostos()");

		inputIva          = document.getElementById('itemsGeneral_impuesto');
		inputIva.readOnly = true;
		inputIva.setAttribute("onclick","ventanaBuscarIva()");

		function ventanaBuscarCentroCostos() {

			Win_Ventana_buscar_centro_costos = new Ext.Window({
			    width       : 540,
			    height      : 450,
			    id          : 'Win_Ventana_buscar_centro_costos',
			    title       : 'Buscar Centro de Costos',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            opc : 'itemsGeneral',
			            carpeta_img : 'img',
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}


		function ventanaBuscarIva() {

			Win_Ventana_buscar_centro_costos = new Ext.Window({
			    width       : 540,
			    height      : 450,
			    id          : 'Win_Ventana_buscar_centro_costos',
			    title       : 'Buscar Impuesto',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'items/bd/grillaBuscarIva.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            var1 : 'var1',
			            var2 : 'var2',
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'left',
			                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		divOcultarActivoFijo = document.getElementById('itemsGeneral_opcion_activo_fijo').parentNode.parentNode;
		divOcultarCosto      = document.getElementById('itemsGeneral_opcion_costo').parentNode.parentNode;
		divOcultarGasto      = document.getElementById('itemsGeneral_opcion_gasto').parentNode.parentNode;

		//SIEMPRE DEBE TENER UN CENTRO DE COSTO
		// selectOpcionCosto = document.getElementById('itemsGeneral_opcion_costo');
		// if (selectOpcionCosto.value=='true') {
		// 	divOcultarCentroCostos.style.display = 'block';
		// 	// inputCentroCostos.querySelectorAll('option')[0].value='';
		// 	// inputCentroCostos.value='';
		// }
		// else{
		// 	divOcultarCentroCostos.style.display = 'none';
		// 	inputCentroCostos.querySelectorAll('option')[0].value=0;
		// 	inputCentroCostos.value='0';
		// }


		// <?php if($opcion == 'Vagregar'){ echo"//document.getElementById('itemsGeneral_opcional_compra').disabled = true; "; } ?>


		document.getElementById('itemsGeneral_codigo_auto').onchange = function(){ actionComboCodigoAuto(this.value); };
		document.getElementById('itemsGeneral_inventariable').onchange = function(){ actionComboInventariable(this.value); };
		document.getElementById('itemsGeneral_estado_compra').onchange = function(){ actionComboEstadoCompra(this.value); };
		// document.getElementById('itemsGeneral_opcion_costo').onchange = function(){ actionComboOpcionalCompra(this.value); };
		document.getElementById('itemsGeneral_estado_venta').onchange = function(){ actionComboEstadoVenta(this.value); };

		actionComboCodigoAuto(document.getElementById('itemsGeneral_codigo_auto').value);
		function actionComboCodigoAuto(codigoAuto){
			if(codigoAuto == 'true' || codigoAuto == ''){
				if('<?php echo $id; ?>' > 0){ document.getElementById('itemsGeneral_codigo').value = document.getElementById('div_itemsGeneral_codigo_<?php echo $id; ?>').innerHTML; }
				else{  document.getElementById('itemsGeneral_codigo').value = 0; }

				<?php if($opcion == 'Vagregar'){ echo "document.getElementById('EmpConte_itemsGeneral_codigo').style.display='none';"; } ?>
				document.getElementById('itemsGeneral_codigo').disabled = true;
			}
			else{
				<?php if($opcion == 'Vagregar'){ echo "document.getElementById('EmpConte_itemsGeneral_codigo').style.display='block';"; } ?>
				document.getElementById('itemsGeneral_codigo').disabled = false;
			}
		}

		//VALIDACION COMBO INVENTARIABLE
		actionComboInventariable(document.getElementById('itemsGeneral_inventariable').value);
		function actionComboInventariable(boleano){
			// EmpConte_itemsGeneral_cantidad_minima_stock
			// EmpConte_itemsGeneral_cantidad_maxima_stock
			if(boleano == 'false'){
				document.getElementById('EmpConte_itemsGeneral_cantidad_minima_stock').style.display = 'none';
				document.getElementById('EmpConte_itemsGeneral_cantidad_maxima_stock').style.display = 'none';

				//UNIDAD DE MEDIDA POR DEFAULT EN SERVICIO
				var arrayComboUnidad = document.getElementById('itemsGeneral_id_unidad_medida').querySelectorAll('option');
				for(indice in arrayComboUnidad){

					if(arrayComboUnidad[indice].innerHTML == 'Servicio'){
						document.getElementById('itemsGeneral_id_unidad_medida').value = arrayComboUnidad[indice].value;
						document.getElementById('itemsGeneral_id_unidad_medida').setAttribute('disabled','disabled');
					}
				}

				document.getElementById('itemsGeneral_cantidad_minima_stock').value      = 0;
				document.getElementById('itemsGeneral_cantidad_minima_stock').disabled   = true;

				document.getElementById('itemsGeneral_cantidad_maxima_stock').value      = 0;
				document.getElementById('itemsGeneral_cantidad_maxima_stock').disabled   = true;

				document.getElementById('itemsGeneral_vida_util').value                  = '0';
				document.getElementById('EmpConte_itemsGeneral_vida_util').style.display = 'none';

			}
			else{
				document.getElementById('EmpConte_itemsGeneral_cantidad_minima_stock').style.display = 'block';
				document.getElementById('EmpConte_itemsGeneral_cantidad_maxima_stock').style.display = 'block';

				//UNIDAD DE MEDIDA POR DEFAULT EN SERVICIO
				if(document.getElementById('itemsGeneral_id_unidad_medida').getAttribute('disabled')=='disabled'){
					document.getElementById('itemsGeneral_id_unidad_medida').disabled = false;
					document.getElementById('itemsGeneral_id_unidad_medida').value = "";
				}

				document.getElementById('itemsGeneral_cantidad_minima_stock').disabled = false;
				document.getElementById('itemsGeneral_cantidad_maxima_stock').disabled = false;

				if ("<?php echo $opcion ?>" == 'Vagregar') {
					document.getElementById('itemsGeneral_vida_util').value = '0';
					document.getElementById('EmpConte_itemsGeneral_vida_util').style.display = 'block';
				}
			}

			//if(boleano == 'false' &&  document.getElementById('itemsGeneral_opcional_compra').value == 'activo_fijo'){
			//	document.getElementById('itemsGeneral_opcional_compra').value = '';
			//}
		}

		//VALIDACION COMBO ESTADO DE COMPRA
		actionComboEstadoCompra(document.getElementById('itemsGeneral_estado_compra').value);
		function actionComboEstadoCompra(estadoCompra){
			if(estadoCompra == 'true'){
				// document.getElementById('itemsGeneral_opcional_compra').disabled = false;
				document.querySelectorAll('.EmpSeparador')[2].style.display = 'block';
				divOcultarActivoFijo.style.display   = 'block';
				divOcultarCosto.style.display        = 'block';
				divOcultarGasto.style.display        = 'block';
				divOcultarCentroCostos.style.display = 'block';

			}
			else{
				//OCULTAR LA INFORMACION DE COMPRA
				document.querySelectorAll('.EmpSeparador')[2].style.display = 'none';
				divOcultarCentroCostos.style.display                        = 'none';

				//inputCentroCostos.querySelectorAll('option')[0].value      = 0;

				//LLAMAR FUNCION PARA BORRAR LOS DATOS DEL CENTRO DE COSTOS
				if (document.getElementById('imgEliminarCcos')) {
					eliminaCcosItem ();
				}


				divOcultarActivoFijo.style.display = 'none';
				divOcultarCosto.style.display      = 'none';
				divOcultarGasto.style.display      = 'none';

				// document.getElementById('itemsGeneral_opcional_compra').value = '';
				// document.getElementById('itemsGeneral_opcional_compra').disabled = true;
			}
		}

		//VALIDACION COMBO OPCIONES_COMPRA
		// function actionComboOpcionalCompra(OpcionalCompra){

		// 	if(OpcionalCompra == 'true'){
		// 		divOcultarCentroCostos.style.display                   ='block';
		// 		//	inputCentroCostos.querySelectorAll('option')[0].value = '';
		// 		//	inputCentroCostos.value                               ='';
		// 		if ('<?php echo $opcion; ?>' == 'Vagregar') {
		// 			inputCentroCostos.querySelectorAll('option')[0].value = 0;
		// 			inputCentroCostos.value                               ='0';
		// 		}
		// 	}
		// 	else{
		// 		divOcultarCentroCostos.style.display                   ='none';
		// 		inputCentroCostos.querySelectorAll('option')[0].value = 0;
		// 		inputCentroCostos.value                               ='0';
		// 	}
		// }

		//VALIDACION COMBO ESTADO DE COMPRA
		actionComboEstadoVenta(document.getElementById('itemsGeneral_estado_venta').value);
		function actionComboEstadoVenta(estadoVenta){
			if(estadoVenta == 'true'){
				document.getElementById('EmpConte_itemsGeneral_modulo_pos').style.display='block';
				document.getElementById('EmpConte_itemsGeneral_precio_venta').style.display='block';
			}
			else{
				document.getElementById('itemsGeneral_modulo_pos').value = 'false';
				document.getElementById('EmpConte_itemsGeneral_modulo_pos').style.display='none';

				document.getElementById('itemsGeneral_precio_venta').value = 0;
				document.getElementById('EmpConte_itemsGeneral_precio_venta').style.display='none';
			}
		}

		//FAMILIA////////////////////////////////////////////////////////////////////////////////////////////////
        function ActualizaFamilia(){
			var myParentFamiliaItems = document.getElementById('itemsGeneral_id_familia').parentNode;
			Ext.get(myParentFamiliaItems).load({
			    url		: 'items/bd/bd.php',
			    timeout : 180000,
			    scripts	: true,
			    nocache	: true,
			    params	:
			    {
					op      : 'OptionSelectFamiliaItems',
					id_item : '<?php echo $id?>',
			    }
			});
		};

    	//GRUPO////////////////////////////////////////////////////////////////////////////////////////////////
		function ActualizaGrupoItems(idFamiliaItem){
			var myParentFamiliaItems = document.getElementById('itemsGeneral_id_grupo').parentNode;
			Ext.get(myParentFamiliaItems).load({
			    url		: 'items/bd/bd.php',
			    timeout : 180000,
			    scripts	: true,
			    nocache	: true,
			    params	:
			    {
					op              : 'OptionSelectGrupoItems',
					id_item         : '<?php echo $id?>',
					id_item_familia : idFamiliaItem
			    }
			});
		};

    	//SUBGRUPO//////////////////////////////////////////////////////////////////////////////////////////////
		function ActualizaSubgrupoItems(idGrupoItem){
			var myParentSubgrupoItems = document.getElementById('itemsGeneral_id_subgrupo').parentNode;
			Ext.get(myParentSubgrupoItems).load({
			    url		: 'items/bd/bd.php',
			    timeout : 180000,
			    scripts	: true,
			    nocache	: true,
			    params	:
			    {
				    op            :	'OptionSelectSubgrupoItems',
				    id_item       : '<?php echo $id?>',
				    id_item_grupo : idGrupoItem
			    }
			});
		};

        ActualizaFamilia();

        /*------------------------------------------------ Actualiza centros de costos----------------------------------------------*/
		ActualizaCentroCostosItem();
		function ActualizaCentroCostosItem(){
			var MyParent = document.getElementById('itemsGeneral_centro_costos').parentNode;
			Ext.get(MyParent).load({
				url		: 'items/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					idItem : '<?php echo $id?>',
					op     : 'OptionCentroCostos'
				}
			});
		}

		ActualizaIvaItem();
		function ActualizaIvaItem(){
			var MyParent = document.getElementById('itemsGeneral_impuesto').parentNode;
			Ext.get(MyParent).load({
				url		: 'items/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					idItem : '<?php echo $id?>',
					op     : 'OptionIvaItem'
				}
			});
		};

		//ELIMINAR EL IMPUESTO DE UN ITEM
		function eliminaImpuestoItem(idItem,impuesto) {
			document.getElementById("itemsGeneral_id_impuesto").value="";
			document.getElementById("itemsGeneral_impuesto").value="";

			// document.getElementById('itemsGeneral_impuesto').parentNode.removeChild(document.getElementById('imgEliminarImpuesto'));
			// console.log("in");
			document.getElementById('imgEliminarImpuesto').style.backgroundImage="url('img/buscar20.png')";
			document.getElementById('imgEliminarImpuesto').setAttribute('onclick','ventanaBuscarIva()');
			document.getElementById('imgEliminarImpuesto').setAttribute('title','Bucar Impuesto');
			// var MyParent = document.getElementById('itemsGeneral_impuesto').parentNode;
			// Ext.get(MyParent).load({
			// 	url		: 'items/bd/bd.php',
			// 	timeout : 180000,
			// 	scripts	: true,
			// 	nocache	: true,
			// 	params	:
			// 	{
			// 		idItem   : idItem,
			// 		impuesto : impuesto,
			// 		op       : 'eliminaImpuestoItem',
			// 	}
			// });
		}

		//ELIMINAR EL CENTRO DE COSTOS
		function eliminaCcosItem () {
			document.getElementById('imgEliminarCcos').setAttribute('onclick','ventanaBuscarCentroCostos()');
			document.getElementById('imgEliminarCcos').style.backgroundImage="url('img/buscar20.png')";
			document.getElementById('imgEliminarCcos').setAttribute('title','Buscar Centro de Costos');

			document.getElementById('itemsGeneral_id_centro_costos').value = '';
			document.getElementById('itemsGeneral_centro_costos').value    = '';
		}

		document.getElementById('itemsGeneral_item_produccion').setAttribute('onchange','verificaItemProduccion()');
		function verificaItemProduccion() {
			var item_produccion = document.getElementById('itemsGeneral_item_produccion').value;
			var item_transformacion = document.getElementById('itemsGeneral_item_transformacion').value;
			if (item_transformacion=='true' && item_produccion=='true'){
				alert("El item solo puede ser de produccion o de transformacion, no puede tener las dos opciones!");
				document.getElementById('itemsGeneral_item_produccion').value='false';
			}

		}


		document.getElementById('itemsGeneral_item_transformacion').setAttribute('onchange','verificaItemTransformacion()');
		verificaItemTransformacion();
		function verificaItemTransformacion() {
			var item_transformacion = document.getElementById('itemsGeneral_item_transformacion').value;

			if (document.getElementById('itemsGeneral_item_produccion').value=='true' && item_transformacion=='true') {
				alert("El item solo puede ser de produccion o de transformacion, no puede tener las dos opciones!");
				document.getElementById('itemsGeneral_item_produccion').value='false';
				document.getElementById('itemsGeneral_id_item_transformacion').value                      = '0';
				document.getElementById('itemsGeneral_cantidad_transformacion').value                     = '0';
				// return;
			}

			if (item_transformacion=='true'){
				document.getElementById('EmpConte_itemsGeneral_nombre_item_transformacion').style.display = 'block';
				document.getElementById('EmpConte_itemsGeneral_cantidad_transformacion').style.display    = 'block';
				if ('<?php echo $opcion; ?>'=='Vagregar') {
					document.getElementById('itemsGeneral_nombre_item_transformacion').value              = '';
					document.getElementById('itemsGeneral_id_item_transformacion').value                  = '';
					document.getElementById('itemsGeneral_cantidad_transformacion').value                 = '';
				}

			}
			else{
				document.getElementById('EmpConte_itemsGeneral_nombre_item_transformacion').style.display = 'none';
				document.getElementById('EmpConte_itemsGeneral_cantidad_transformacion').style.display    = 'none';
				document.getElementById('itemsGeneral_nombre_item_transformacion').value                  = '0';
				document.getElementById('itemsGeneral_id_item_transformacion').value                      = '0';
				document.getElementById('itemsGeneral_cantidad_transformacion').value                     = '0';
			}

		}

		item_a_transformar = document.getElementById('itemsGeneral_nombre_item_transformacion')
		item_a_transformar.readOnly=true;
		item_a_transformar.style.float='left';

		var divBtn = document.createElement("div");
		divBtn.setAttribute("class","btnBuscar");
		divBtn.setAttribute("onclick","buscarItemTransformacion()");
		divBtn.setAttribute('title','Buscar Cuenta Item a transformar');
		divBtn.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_itemsGeneral_nombre_item_transformacion").appendChild(divBtn);

		function buscarItemTransformacion() {

			Win_Ventana_buscarItemTransformacion = new Ext.Window({
			    width       : 650,
			    height      : 650,
			    id          : 'Win_Ventana_buscarItemTransformacion',
			    title       : 'Buscar Item a transformar',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'items/bd/grillaBuscaritems.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            var1 : 'var1',
			            var2 : 'var2',
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_buscarItemTransformacion.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}



	</script>
<?php
}


if(!isset($opcion)) { ?>
	<script >

		//CAMBIAR EL NOMBRE DE LOS FILTROS DEL GFILTER DE LA GRILLA
		if (document.getElementById('ElFilter_Disponible0_itemsGeneral') && document.getElementById('ElFilter_Disponible1_itemsGeneral')) {
			var elemento0=document.getElementById('ElFilter_Disponible0_itemsGeneral').innerHTML;
			var elemento1=document.getElementById('ElFilter_Disponible1_itemsGeneral').innerHTML;

			if (elemento0.split("&")[0]=="false") {document.getElementById('ElFilter_Disponible0_itemsGeneral').innerHTML="Compras&"+elemento0.split("&")[1];}
			else if(elemento0.split("&")[0]=="true"){document.getElementById('ElFilter_Disponible1_itemsGeneral').innerHTML="Compras"+elemento0.split("&")[1];}

			if (elemento1.split("&")[0]=="true") {document.getElementById('ElFilter_Disponible1_itemsGeneral').innerHTML="Ventas&"+elemento1.split("&")[1];}
			else if(elemento1.split("&")[0]=="true"){document.getElementById('ElFilter_Disponible0_itemsGeneral').innerHTML="Compras"+elemento1.split("&")[1];}
		}

		function Editar_itemsGeneral(id){ VentanaAgregarItemsGeneral(id); }
		function Agregar_itemsGeneral(){ VentanaAgregarItemsGeneral('false'); }

		function VentanaAgregarItemsGeneral(cual){
			var myalto  = Ext.getBody().getHeight()
			,	myancho = Ext.getBody().getWidth();


			if(cual == 'false'){
				Win_Agregar_itemsGeneral = new Ext.Window({
					width		: 600,
					id			: 'Win_Ventana_itemsGeneral',
					height		: myalto - 80,
					title		: 'Agregar Item',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'items/ventana_auto_items.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							cual           : cual,
							opcion         : 'Vagregar',
							filtro_empresa : '<?php echo $id_empresa ?>',
						}
					}
				}).show();
			}
			else{

				Win_Editar_itemsGeneral = new Ext.Window({
					width		: 600,
					id			: 'Win_Ventana_itemsGeneral',
					height		: myalto - 80,
					title		: 'Editar Item',
					modal		: true,
					autoScroll	: false,
					resizable 	: false,
					closable	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'items/ventana_auto_items.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							cual           : cual,
							opcion         : 'Vupdate',
							filtro_empresa : '<?php echo $id_empresa ?>',
						}
					}
				}).show();
			}
		}

	//====================================// UPLOAD EXCEL INVENTARIO //====================================//
    function createUploader(){
        // var tipo_nota = document.getElementById('filtro_tipo_contabilidad_NotaGeneral').value;

        var uploader = new qq.FileUploader({
            element : document.getElementById('div_upload_file'),
            action  : 'items/upload_file/upload_file.php',
            debug   : false,
            params  : { opcion: 'loadExcelNota' },
            button            : null,
            multiple          : false,
            maxConnections    : 3,
            allowedExtensions : ['xls', 'ods'],
            sizeLimit         : 10*1024*1024,
            minSizeLimit      : 0,
            onSubmit          : function(id, fileName){},
            onProgress        : function(id, fileName, loaded, total){},
            onComplete        : function(id, fileName, responseJSON){
                                    document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';

                                    var JsonText = JSON.stringify(responseJSON);
                                    console.log(responseJSON);
                                    console.log(responseJSON.success);
                                    console.log(responseJSON['success']);
                                    // console.log(JsonText);
                                    if(JsonText == '{}'){
                                    	alert("Aviso\nLo sentimos a ocurrido un problema con la carga del archivo, por favor verifique si se logro subir el excel en caso contrario intentelo nuevamente!");
                                    	Ext.Ajax.request({
                                    	    url     : 'items/bd/bd.php',
                                    	    params  :
                                    	    {
                                    			op : 'delteTemporalFile',
                                    			file : fileName
                                    	    },
                                    	    success :function (result, request){
                                    	                if(result.responseText == 'true'){ console.log("delete ok"); }
                                    	                else{ console.log("delete no"); }
                                    	            },
                                    	    failure : function(){ console.log("delete no"); }
                                    	});
                                    	return;
                                    }
                                    else if (responseJSON.success == true) {
                                        document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                       	MyBusquedaitemsGeneral();
                                        console.log(responseJSON.debug);
                                    }
                                    else{
                                    	if (responseJSON.debug=='items') {
	                                		var errorsDetail = '';
	                                		for (var i in responseJSON.detalle){
	                                			errorsDetail += `<div class='row'>
	                                								<div class='cell' data-col='1'></div>
	                                								<div class='cell' data-col='2' style='width:515px;font-weight: bold;'>${i}</div>
	                            								</div>`+responseJSON.detalle[i]
	                                		}

	                                    	var contentHtml = `<style>
																	.sub-content[data-position="right"]{width: 100%; height: 386px; }
																    .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
																    .content-grilla-filtro .cell[data-col="2"]{width: 85px;}
																    .content-grilla-filtro .cell[data-col="3"]{width: 419px;}
																    .content-grilla-filtro .cell[data-col="4"]{width: 211px;}
																    .sub-content [data-width="input"]{width: 120px;}
	                                    						</style>

	                                    						<div class="main-content" style="height: 409px;overflow-y: auto;overflow-x: hidden;">
	                                    							<div class="sub-content" data-position="right">
	        															<div class="title">DETALLE DE ERRORES POR CODIGO DE ITEM DEL EXCEL</div>
	        															<div class="content-grilla-filtro">
																            <div class="head">
																                <div class="cell" data-col="1"></div>
																                <div class="cell" data-col="2">Codigo</div>
																                <div class="cell" data-col="3">Detalle del error</div>
																            </div>
																            <div class="body" id="body_grilla_filtro">
																            	${errorsDetail}
	        																</div>
	    																</div>

	        														</div>
	                                    						</div>`;
                						}
                						else{
                							var errorsDetail = '';
	                                		for (var i in responseJSON.detalle){
	                                			errorsDetail += responseJSON.detalle[i]
	                                		}
                							var contentHtml = `<style>
																	.sub-content[data-position="right"]{width: 100%; height: 386px; }
																    .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
																    .content-grilla-filtro .cell[data-col="2"]{width: 220px;}
																    .content-grilla-filtro .cell[data-col="3"]{width: 268px;}
																    .content-grilla-filtro .cell[data-col="4"]{width: 211px;}
																    .sub-content [data-width="input"]{width: 120px;}
	                                    						</style>

	                                    						<div class="main-content" style="height: 409px;overflow-y: auto;overflow-x: hidden;">
	                                    							<div class="sub-content" data-position="right">
	        															<div class="title">DETALLE DE ERRORES</div>
	        															<div class="content-grilla-filtro">
																            <div class="head">
																                <div class="cell" data-col="1"></div>
																                <div class="cell" data-col="2">Error generado</div>
																                <div class="cell" data-col="3">Detalle del error</div>
																            </div>
																            <div class="body" id="body_grilla_filtro">
																            	${errorsDetail}
	        																</div>
	    																</div>

	        														</div>
	                                    						</div>`;
                						}

                                    	Win_Ventana_errors = new Ext.Window({
											width       : 600,
											height      : 400,
											id          : 'Win_Ventana_errors',
											title       : 'Detalle de errores',
											modal       : true,
											autoScroll  : false,
											closable    : true,
											autoDestroy : true,
											html        : contentHtml
                                    	}).show();

                                    }
                                },
            onCancel : function(fileName){},
            messages :
            {
                typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'ods'",
                sizeError    : "\"{file}\" Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError   : "{file} is empty, please select files again without it.",
                onLeave      : "Cargando Archivo."
            }
        });
    }
    createUploader();

	function descargar_items_excel(){
		window.open("../panel_de_control/items/bd/bd.php?op=descargar_items_excel");
	}

	function windows_upload_excel(){
		if(globalNameFileUpload != ''){ alert('Elimine el archivo anterior antes de subir uno nuevo!'); return; }
		document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
	}

	function close_ventana_upload_file(){
		document.getElementById('divPadreModalUploadFile').setAttribute('style','');
	}

	function cancelUploadFile(){
		var xhr     = new XMLHttpRequest()
		,   bodyXhr = 'bd.php?nameFileUpload='+globalNameFileUpload+'&opc=cancelUploadFile';

		xhr.open('POST',bodyXhr, true);
		xhr.onreadystatechange=function(){
			if(xhr.readyState==4){
				var responseError = xhr.responseText;
				if (responseError=='true') {
					globalNameFileUpload = '';
					document.getElementById('nombre_excel').value = '';
					document.getElementById('btn_cancel_doc_upload').style.display = 'none';
					return;
				}
				alert(responseError);
			}
			else return;
		}
		xhr.send(null);
	}

	</script>

	<style type="text/css">
		.contenedor_items_cuentas{
			margin   : 0 5px 5px 5px;
			width    : calc(100% - 10px);
			overflow : auto;
		}

		.contenedor_items_cuentas input{ width : 100%; }

		.item_cuenta_left{
			margin-right : 6px;
			padding-top  : 40px;
		}

		.item_cuenta_right{
			margin-left : 6px;
			padding-top : 40px;
		}

		.titleItemsCuenta{
			font-size     : 13px;
			margin-bottom : 35px;
			text-align    : center;
		}

		.btnItemsCuentas{
			margin           : 1px 0 0 4px;
			height           : 16px;
			width            : 18px;
			float            : left;
			cursor           : pointer;
			border-radius    : 3px;
			border           : 1px solid #999;
			background-color : #FFF;
			box-shadow       : 1px 1px 3px #999;
			text-align       : center;
			font-weight      : bold;
			color            : #0842a5;
		}

		.btnItemsCuentasEstado{
			margin           : 1px 0 0 -20px;
			height           : 18px;
			width            : 18px;
			float            : left;
			cursor           : pointer;
			border-left      : 1px solid #999;
			background-color : #FFF;
			box-shadow       : 1px 1px 3px #999;
			text-align       : center;
			font-weight      : bold;
			color            : #0842a5;
		}

		.contenedorBtns{ float : left; }

		.cuentaPuc{
			float         : left;
			width         : 255px;
			overflow      : hidden;
			margin-left   : 10px;
			text-overflow : ellipsis;
			white-space   : nowrap;
		}

		.filaCuentasItems{
			overflow   : visible;
			margin     : 15px 5px;
			min-height : 20px;
		}

		.filaCuentasItems input{ cursor : pointer; }

		.btnBuscar{
			width             : 19px;
			height            : 16px;
			/*background-color  : #FFF;*/
			cursor            : pointer;
			background-image  : url('img/buscar20.png');
			background-repeat : no-repeat;
			float             : left;
			margin-left       : -20;
			margin-top        : 2;
			border-left       : 1px solid #BDB4B4;
		}

		.btnBuscar2{
			height           : 18px;
			width            : 18px;
			float            : left;
			cursor           : pointer;
			margin-top       : 3px;
			margin-left      : 2px;
			margin           : 1px 0 0 2px;
			border           : 1px solid #d4d4d4;
			background-color : #F3F3F3;
		}

	</style>

<?php
} ?>
