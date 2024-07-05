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

	$filtro_empresa  = $_SESSION['EMPRESA'];
	$whereSaldo = ($_SESSION['NITEMPRESA']==900474556)? 'AND  total_factura_sin_abono>0' : 'AND  total_factura_sin_abono>1';

	if ($tipo_documento_cruce == 'FV') {
		$tablaBuscar = 'ventas_facturas';
		$sql="SELECT id_documento_cruce FROM recibo_caja_cuentas WHERE activo=1 AND id_recibo_caja=$id_recibo_caja";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=mysql_fetch_array($query)) {
			$whereIdFV .= ' AND id<>'.$row['id_documento_cruce'] ;
		}
	}

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaBuscar;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 $whereSaldo AND estado=1 AND id_empresa='$filtro_empresa' AND id_sucursal= '$filtro_sucursal' $whereTercero $whereIdFV ";
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	    = $CualAncho;	//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= $CualAlto;	//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 190;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nit,cliente,fecha_inicio,prefijo,numero_factura';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
	 		$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
	 		$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstadoFacturaCompra_[id]" /></center><div id="id_tercero_[id]" style="display:none;">[id_cliente]</div><div id="id_cuenta_[id]" style="display:none;">[id_cuenta_pago]</div><div id="cuenta_pago_[id]" style="display:none;">[cuenta_pago]</div>','40');
			$grilla->AddRow('Prefijo','prefijo',80);
			$grilla->AddRow('N. Factura','numero_factura',150);
			$grilla->AddRow('Nit','nit',100);
			$grilla->AddRow('Cliente','cliente',200);
			$grilla->AddRow('Saldo','total_factura_sin_abono',100);
			$grilla->AddRow('Fecha','fecha_inicio',250,'fecha');

			$grilla->AddColStyle('numero_factura','text-align:right; width:145px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
			$grilla->AddColStyle('nit','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
			$grilla->AddColStyle('total_factura_sin_abono','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

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

		function Editar_<?php echo $opcGrillaContable; ?>(id){
			// console.log('cont = '+cont+' - contArticulos= '+contArticulos<?php echo $opcGrillaContable; ?>);

			if(!document.getElementById('idInsertCuentaReciboCaja_'+cont)){ Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close(); return; }

			if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
        	    document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
        	    document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
        	}

			id_cuenta = document.getElementById('id_cuenta_'+id).innerHTML;
			cuenta    = document.getElementById('cuenta_pago_'+id).innerHTML;

			document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value = id_cuenta;
			document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value   = cuenta;

			document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value=id;
			document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value='<?php echo $tipo_documento_cruce; ?>';
			document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value = document.getElementById('div_<?php echo $opcGrillaContable; ?>_prefijo_'+id).innerHTML;
			document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value  = document.getElementById('div_<?php echo $opcGrillaContable; ?>_numero_factura_'+id).innerHTML;
			document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value= document.getElementById('div_<?php echo $opcGrillaContable; ?>_total_factura_sin_abono_'+id).innerHTML;
			document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).focus();

			if (document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value != '' ) {
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('src','img/eliminar.png');
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('title','Eliminar Documento Cruce');
        		document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('onclick'," eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>("+cont+")");
			}

			id_tercero=document.getElementById('id_tercero_'+id).innerHTML;

			if (id_cliente_<?php echo $opcGrillaContable; ?>!=id_tercero) {

				document.getElementById('tercero<?php echo $opcGrillaContable; ?>_'+cont).value   = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cliente_'+id).innerHTML;
				document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+cont).value = id_tercero;
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/eliminar.png');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Eliminar Tercero');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
			}
			else{
				document.getElementById('tercero<?php echo $opcGrillaContable; ?>_'+cont).value='';
				document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+cont).value='';
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/buscar20.png');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Buscar Tercero');
        		document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
			}

			guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont);
			// Elimina_Div_nomina_planillas_empleados_contabilizacion(id);
			// actualiza_fila_ventana_busqueda_doc_cruce(id);
			cont=contArticulos<?php echo $opcGrillaContable; ?>;

			// Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>

