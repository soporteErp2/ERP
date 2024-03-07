<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	//CONSULTAR LAS CUENTAS Q ESTAMN GUERDADAS
	$sql="SELECT id_tabla_referencia FROM comprobante_egreso_cuentas WHERE activo=1 AND id_comprobante_egreso=$id_comprobante_egreso";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$whereId.=' AND id<>'.$row['id_tabla_referencia'];
	}

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nomina_planillas_empleados_contabilizacion';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_planillas_empleados_contabilizacion';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS

			$grilla->MyWhere			= "activo = 1 AND id_planilla=$id_planilla AND total_sin_abono>0 AND id_empresa=$id_empresa AND tipo_planilla='LN' $whereId";

			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 190;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
	 		$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA

			$grilla->CamposBusqueda		= 'cuenta_colgaap,cuenta_niif,tercero,debito,credito,total_sin_abono,documento_tercero,descripcion_cuenta_colgaap';

			$grilla->AddRow('Cuenta Colgaap','cuenta_colgaap',90);
			$grilla->AddRow('Descripcion','descripcion_cuenta_colgaap',200);
			$grilla->AddRowImage('Tercero','<label id="tercero_[id]">[tercero]</label><input type="hidden" id="id_cuenta_[id]" value="[id_cuenta_colgaap]"><input type="hidden" id="id_tercero_[id]" value="[id_tercero]">',200);
			// $grilla->AddRow('Empleado','empleado_cruce',200);
			$grilla->AddRow('Debito','debito',80);
			$grilla->AddRow('Credito','credito',80);
			$grilla->AddRow('Saldo','total_sin_abono',80);
			// $grilla->AddRow('Saldo','total_factura_sin_abono',100);
			// $grilla->AddRow('Fecha','fecha_inicio',250,'fecha');


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Reuniones Coope'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Reunion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';	//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 200;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

 		//CONFIGURACION DEL MENU CONTEXTUAL
 			// $grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		// $grilla->MenuContextEliminar= 'true';

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**//////////////////////////////////////////////////////////////**/
		/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
		/**/															/**/
		/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
		/**/	$grilla->inicializa($_POST);//variables POST			/**/
		/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
		/**/															/**/
		/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>
		// SI YA SE INERTO EL DOCUMENTO EN LA FILA, SE ASGINA CONT A LA ULTIMA FILA DE LA GRILLA
		if (document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont;?>').value>0) {cont=contArticulos<?php echo $opcGrillaContable; ?>;}
		// SINO SE HA INSERTADO AUN UN REGISTRO, ENTONCES CORRESPONDE A LA FILA DONDE SE HIZO CLICK
		else{cont = <?php echo $cont; ?>;}

		function Editar_nomina_planillas_empleados_contabilizacion(id){
			if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
        	    document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
        	    document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
        	}

			var id_cuenta = document.getElementById('id_cuenta_'+id).value;
			var cuenta    = document.getElementById('div_nomina_planillas_empleados_contabilizacion_cuenta_colgaap_'+id).innerHTML;
			var saldo     = document.getElementById('div_nomina_planillas_empleados_contabilizacion_total_sin_abono_'+id).innerHTML
			var debito    = document.getElementById('div_nomina_planillas_empleados_contabilizacion_debito_'+id).innerHTML
			var credito   = document.getElementById('div_nomina_planillas_empleados_contabilizacion_credito_'+id).innerHTML

			var debitoT  = (credito>0)? saldo : 0 ;
			var creditoT = (debito>0)? saldo : 0 ;

			document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value = id_cuenta;
			document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value   = cuenta;

			document.getElementById('idTablaReferencia<?php echo $opcGrillaContable; ?>_'+cont).value    = id;
			document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value='<?php echo $id_planilla; ?>';
			document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value='LN';
			document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value  = '<?php echo $consecutivo; ?>';
			document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value= debitoT;
			document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value= creditoT;
			document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).focus();

			if (document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value != '' ) {
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('src','img/eliminar.png');
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('title','Eliminar Documento Cruce');
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('onclick'," eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>('"+cont+"')");
			}

			id_tercero=document.getElementById('id_tercero_'+id).value;

			// if (id_cliente_<?php echo $opcGrillaContable; ?>!=id_tercero) {
			document.getElementById('tercero<?php echo $opcGrillaContable; ?>_'+cont).value   = document.getElementById('tercero_'+id).innerHTML;
			document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+cont).value = id_tercero;
			//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
    		document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/eliminar.png');
    		document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Eliminar Tercero');
    		document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
			// }
			// else{
			// 	document.getElementById('tercero<?php echo $opcGrillaContable; ?>_'+cont).value="";
			// 	document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+cont).value="";
			// 	//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
   //      		document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/buscar20.png');
   //      		document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Buscar Tercero');
   //      		document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
			// }

			guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont);
			// Elimina_Div_nomina_planillas_empleados_contabilizacion(id);
			actualiza_fila_ventana_busqueda_doc_cruce(id);
			cont=contArticulos<?php echo $opcGrillaContable; ?>;
			// Win_Ventana_cuentas_planilla.close();
			// Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>

