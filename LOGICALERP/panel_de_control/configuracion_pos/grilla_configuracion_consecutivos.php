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

	if ($estado=='true') {
		$texto='Deshabilitar Caja';
		$iconoBtn='deshabilita_pos';
		$eventoBtn='deshabilita_pos('.$id.')';
	}
	else if ($estado=='disabled') {
		$texto='Habilitar Caja';
		$iconoBtn='habilita_pos';
		$eventoBtn='habilita_pos('.$id.')';
	}

	if ($estado=='block') {
		$texto2='Desbloquear<br>consecutivos';
		$iconoBtn2='desbloquear_pos';
		$eventoBtn2='desbloquear_pos('.$id.')';
	}else{
		$texto2='Bloquear <br>consecutivos';
		$iconoBtn2='bloquear_pos';
		$eventoBtn2='bloquear_pos('.$id.')';
	}

	$id_empresa = $_SESSION['EMPRESA'];
	$id_sucursal = $filtro_sucursal;
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'configuracionConsecutivosPos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ventas_pos_consecutivos_caja';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa' AND consecutivo_caja='$caja'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			// $grilla->OrderBy			= 'digitos ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 	= 'true';		//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		= 360;			//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		= 250;			//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho = 330;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto  = 580;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';							//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,consecutivo_caja';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Nombre','<left><div>[nombre]</div><div style="display:none;" id="id_resolucion_[id]">[id_resolucion]</div></left>','70');
			// $grilla->AddRow('Nombre','nombre',100);
			$grilla->AddRow('Numero Caja','consecutivo_caja',100);
			$grilla->AddRow('Cons. Inicial','consecutivo_inicial',100);
			$grilla->AddRow('Cons. Final','consecutivo_final',100);
			$grilla->AddRowImage('Usados','<center><div><img src="configuracion_pos/img/[estado].png" style="cursor:pointer;"></div><div style="display:none;" id="estado_[id]">[estado]</div></center>','55');
			$grilla->AddRow('Fecha','fecha',200,'fecha');
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 60;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Configuracion'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= ''; 				//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= '';				//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','cerrarVentana('.$id.')');

			if ($estado=='true') {
				$grilla->AddBotton($texto,$iconoBtn,$eventoBtn);
				$grilla->AddBotton($texto2,$iconoBtn2,$eventoBtn2);
				$grilla->AddBotton('Asignar Caja a<br>Otro equipo','actualiza_pos','cambiar_caja_de_equipo('.$id.')');
			}
			if ($estado=='disabled') {
				$grilla->AddBotton($texto,$iconoBtn,$eventoBtn);
				$grilla->AddBotton('Asignar Caja a<br>Otro equipo','actualiza_pos','cambiar_caja_de_equipo('.$id.')');
			}
			if($estado=='block'){
				$grilla->AddBotton($texto2,$iconoBtn2,$eventoBtn2);
			}


			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Nombre','nombre',200,'true','false');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			// $grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		// $grilla->MenuContextEliminar= 'false';
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
			// $grilla->AddMenuContext('Liberar estos consecutivos','alert','liberarConsecutivos([id])');

		// $grilla->AddMenuContext('Imprimir Codigo de Barras','barcode16','ventana_codigo_barras([id])');
		// $grilla->AddMenuContext('Configurar Cantidades Stock','config16','ventanaConfigurarInventario([id])');
		// $grilla->AddMenuContext('Agregar al Inventario','auto_back16','ventana_entrada_salida([id],"entrada",[id_item],"[nombre_equipo]",[cantidad])');
		// $grilla->AddMenuContext('Sacar del inventario','auto_go16','ventana_entrada_salida([id],"salida",[id_item],"[nombre_equipo]",[cantidad])');


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
		document.getElementById('ContenedorPrincipal_configuracionConsecutivosPos').style.marginTop='15px';
		function Editar_configuracionConsecutivosPos(id){
		}

		//FUNCION PARA LIBERAR LOS CONSECUTIVOS
		function liberarConsecutivos(id){

			//PREGUNTAR AL USUARIO SI REALMENTE DESEA LIBERAR LOS CONSECUTIVOS
			if (confirm("Advertencia!\nDesea liberar estos consecutivos?\nRecuerde liberarlos del equipo donde se utilizo el POS")) {

				//CAPTURAR EL RANGO DE LOS CONSECUTIVOS
				var consecutivo_inicial = document.getElementById('div_configuracionConsecutivosPos_consecutivo_inicial_'+id).innerHTML;
				var consecutivo_final = document.getElementById('div_configuracionConsecutivosPos_consecutivo_final_'+id).innerHTML;
				var id_resolucion = document.getElementById('id_resolucion_'+id).innerHTML;

				Ext.Ajax.request({
				    url     : 'configuracion_pos/bd/bd.php',
				    params  :
				    {
						opc                 : 'liberarConsecutivos',
						id                  : id,
						consecutivo_inicial : consecutivo_inicial,
						consecutivo_final   : consecutivo_final,
						id_resolucion       : id_resolucion,
						filtro_sucursal     : '<?php echo $filtro_sucursal; ?>'
				    },
				    success :function (result, request){
				    		// console.log(result.responseText);
				                if(result.responseText == 'false'){
				                	alert("Se produjo un error, intentelo de nuevo");
				                }
				                else if(result.responseText == 'true'){
				                	Elimina_Div_configuracionConsecutivosPos(id);
				                }else{
				                	alert(result.responseText);
				                	Actualiza_Div_configuracionConsecutivosPos(id);
				                }
				            },
				    failure : function(){
				    	alert("Se produjo un error, intentelo de nuevo");
				    }
				});

			}
		}

		function cerrarVentana(id) {
			Actualiza_Div_configuracionCajasPos(id)
			Win_Ventana_consecutivos.close(id);
		}

		//FUNCION PARA DESHABILITAR LA CAJA
		function deshabilita_pos(id) {
			if (confirm("Advertencia!\nSi deshabilita la caja, se liberaran los consecutivos asignados\ny se perderan los tiquecks que no se hayan subido al sistema\nRealmente desea continuar?")) {
				Ext.Ajax.request({
			    url     : 'configuracion_pos/bd/bd.php',
			    params  :
			    {
					opc : 'deshabilita_pos',
					id  : id,
					caja : '<?php echo $caja ?>',
					filtro_sucursal     : '<?php echo $filtro_sucursal; ?>'
			    },
			    success :function (result, request){
		    		// console.log(result.responseText);
	                if(result.responseText == 'true'){
	                	Actualiza_Div_configuracionCajasPos(id)
						Win_Ventana_consecutivos.close(id);
	                }
	                else{
	                	console.log(result.responseText);
	                	alert("Se produjo un Error, intentelo de nuevo");
	                }
	            },
			    failure : function(){ alert("Error\nNo hay Conexion, intentelo de nuevo"); }
				});
			}

		}
		//FUNCION PARA HABILITAR LA CAJA
		function habilita_pos(id) {
			Ext.Ajax.request({
			    url     : 'configuracion_pos/bd/bd.php',
			    params  :
			    {
					opc : 'habilita_pos',
					id  : id,
					caja : '<?php echo $caja ?>',
					filtro_sucursal     : '<?php echo $filtro_sucursal; ?>'
			    },
			    success :function (result, request){
			                if(result.responseText == 'true'){
			                	Actualiza_Div_configuracionCajasPos(id)
								Win_Ventana_consecutivos.close(id);
			                }
			                else{
			                	alert("Se produjo un Error, intentelo de nuevo");
			                }
			            },
			    failure : function(){ alert("Error\nNo hay Conexion, intentelo de nuevo"); }
				});
		}
		//FUNCION PARA BLOQUEAR LA CAJA, PARA QUE NO PUEDA DESCARGAR MAS CONSECUTIVOS
		function bloquear_pos(id) {
			Ext.Ajax.request({
			    url     : 'configuracion_pos/bd/bd.php',
			    params  :
			    {
					opc : 'bloquear_pos',
					id  : id,
					caja : '<?php echo $caja ?>',
					filtro_sucursal     : '<?php echo $filtro_sucursal; ?>'
			    },
			    success :function (result, request){
			                if(result.responseText == 'true'){
			                	Actualiza_Div_configuracionCajasPos(id)
								Win_Ventana_consecutivos.close(id);
			                }
			                else{
			                	alert("Se produjo un Error, intentelo de nuevo");
			                }
			            },
			    failure : function(){ alert("Error\nNo hay Conexion, intentelo de nuevo"); }
				});
		}
		//FUNCION PARA DESBLOQUEAR LA CAJA, PARA QUE PUEDA DESCARGAR MAS CONSECUTIVOS
		function desbloquear_pos(id) {
			Ext.Ajax.request({
			    url     : 'configuracion_pos/bd/bd.php',
			    params  :
			    {
					opc : 'desbloquear_pos',
					id  : id,
					caja : '<?php echo $caja ?>',
					filtro_sucursal     : '<?php echo $filtro_sucursal; ?>'
			    },
			    success :function (result, request){
			                if(result.responseText == 'true'){
			                	Actualiza_Div_configuracionCajasPos(id)
								Win_Ventana_consecutivos.close(id);
			                }
			                else{
			                	alert("Se produjo un Error, intentelo de nuevo");
			                	console.log(result.responseText);
			                }
			            },
			    failure : function(){ alert("Error\nNo hay Conexion, intentelo de nuevo"); }
				});
		}

		//FUNCION PARA CAMBIAR LA CAJA DE EQUIPO
		function cambiar_caja_de_equipo(id) {
			if (confirm("Esta caja se reasignara al proximo equipo que se conecte al POS\nRealmente desea continuar y liberar la caja?")) {
				Ext.Ajax.request({
			    url     : 'configuracion_pos/bd/bd.php',
			    params  :
			    {
					opc : 'reasignar_caja',
					id  : id,
					filtro_sucursal     : '<?php echo $filtro_sucursal; ?>',
					caja : '<?php echo $caja ?>',
			    },
			    success :function (result, request){
			                if(result.responseText == 'true'){
			                	Actualiza_Div_configuracionCajasPos(id)
								Win_Ventana_consecutivos.close(id);
			                }
			                else{
			                	alert("Se produjo un Error, intentelo de nuevo");
			                }
			            },
			    failure : function(){ alert("Error\nNo hay Conexion, intentelo de nuevo"); }
				});
			}
		}


    </script>

<?php } ?>