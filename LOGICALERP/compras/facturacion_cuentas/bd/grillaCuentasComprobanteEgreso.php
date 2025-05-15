<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	      $grilla = new MyGrilla();			/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$filtro_empresa  = $_SESSION['EMPRESA'];
	if($tipo_documento_cruce == 'FC'){

		$tabla_buscar = "asientos_colgaap";
		$sqlCuentasPago = "SELECT id_cuenta FROM configuracion_cuentas_pago WHERE activo = 1 AND tipo ='compra'";
		$queryCuentasPago = mysql_query($sqlCuentasPago,$link);
	 	while ($row=mysql_fetch_array($queryCuentasPago)) {
	 		$cuentasPagoId[] = $row['id_cuenta'] ;
	 	}
		$idsString = implode(',', $cuentasPagoId);
		$where = "activo = 1 AND id_documento = $id_documento AND id_sucursal = $filtro_sucursal AND tipo_documento = 'FC' AND id_cuenta IN($idsString)";

		$debe_campo = 'debe';
		$haber_campo = 'haber';

	}
	else{
		$tabla_buscar = "comprobante_egreso_cuentas";
		$where = "activo = 1 AND saldo_pendiente > 0 AND id_comprobante_egreso = $id_documento AND id_documento_cruce = 0";
		$debe_campo = 'debito';
		$haber_campo = 'credito';

	}
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $tabla_buscar;  		//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_buscar;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= $where;
			$grilla->OrderBy			= 'id DESC';							//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';								//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			$grilla->QuitarAncho		= 150;									//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 190;									//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';								//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->DivActualiBusqueda = '';									//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			$grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
	 		$grilla->AddFilter('Estado de la Factura','estado','estado');
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			if($tipo_documento_cruce == 'FC'){
	 		$grilla->AddRowImage('Tercero','[tercero]<input type="hidden" id="id_tercero_[id]" value="[id_tercero]"><input type="hidden" id="tercero_[id]" value="[tercero]"><input type="hidden" id="id_cuenta_[id]" value="[id_puc]"> <input type="hidden" id="nombre_cuenta_[id]" value="[cuenta]"> ','250');
			$grilla->AddRow('Cuenta','codigo_cuenta',100);
			$grilla->AddRow('Descripcion','cuenta',200);
			$grilla->AddRow('Debito','debe',80);
			$grilla->AddRow('Credito','haber',80);
			}
			else{
			$grilla->CamposBusqueda = 'cuenta_colgaap,cuenta_niif,tercero,empleado_cruce,debito,credito,total_sin_abono_provision,documento_tercero,documento_empleado_cruce';
	 		$grilla->AddRowImage('Tercero','[tercero]<input type="hidden" id="id_tercero_[id]" value="[id_tercero]"><input type="hidden" id="tercero_[id]" value="[tercero]"><input type="hidden" id="id_cuenta_[id]" value="[id_puc]"> <input type="hidden" id="nombre_cuenta_[id]" value="[descripcion]"> ','250');
			$grilla->AddRow('Cuenta','cuenta',80);
			$grilla->AddRow('Descripcion','descripcion',200);
			$grilla->AddRow('Cuenta Niif','cuenta_niif',80);
			$grilla->AddRow('Debito','debito',80);
			$grilla->AddRow('Credito','credito',80);
			$grilla->AddRow('Saldo Pendiente','saldo_pendiente',100);
			}
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
			$grilla->VBotonNText		= 'Nueva Reunion';  //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';	//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 200;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**//////////////////////////////////////////////////////////////**/
		/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
		/**/															/**/
		/**/	 $grilla->Link = $link;  	  //Conexion a la BD		/**/
		/**/	 $grilla->inicializa($_POST); //Variables POST			/**/
		/**/	 $grilla->GeneraGrilla(); 	  //Inicializa la Grilla	/**/
		/**/															/**/
		/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>
		// SI YA SE INERTO EL DOCUMENTO EN LA FILA, SE ASGINA CONT A LA ULTIMA FILA DE LA GRILLA
		if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont;?>').value > 0){
			cont = contArticulos<?php echo $opcGrillaContable; ?>;
		}
		// SINO SE HA INSERTADO AUN UN REGISTRO, ENTONCES CORRESPONDE A LA FILA DONDE SE HIZO CLICK
		else{
			cont = <?php echo $cont; ?>;
		}

		function Editar_<?php echo $tabla_buscar; ?>(id){
			let tablaBuscar = '<?php echo $tabla_buscar; ?>';
			(cuenta_pago = document.getElementById('cuenta_pago_<?php echo $id_documento; ?>').innerHTML) * 1;
			debito  = document.getElementById('div_'+tablaBuscar+'_<?php echo $debe_campo; ?>_'+id).innerHTML;
			credito = document.getElementById('div_'+tablaBuscar+'_<?php echo $haber_campo; ?>_'+id).innerHTML;
			
			if('<?php echo $tipo_documento_cruce; ?>' == 'CE'){
			saldo_pendiente = document.getElementById('div_comprobante_egreso_cuentas_saldo_pendiente_'+id).innerHTML;
			}else{
			saldo_pendiente = credito-debito;
			}

			total_factura_sin_abono = saldo_pendiente;
			arrayCuentaPago[<?php echo $cont; ?>] = cuenta_pago;
			arrayTemp = new Array();
			arrayTemp[cuenta_pago] = total_factura_sin_abono;
			arraySaldoCuentaPago[<?php echo $cont; ?>] = arrayTemp[cuenta_pago];

			if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value > 0){
        	    document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').style.display = 'block';
        	    document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>").style.display     = 'inline';
        	}

			numero_factura  = '';
			tercero         = '';

        	if ('<?php echo $tipo_documento_cruce; ?>'=='CE') {
			// consecutivo = document.getElementById('div_<?php echo $opcGrillaContable; ?>_consecutivo_'+id).innerHTML
        	}

			document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value='<?php echo $id_documento; ?>';
			document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value='<?php echo $tipo_documento_cruce; ?>';
			document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value = '<?php echo $consecutivo; ?>';
			document.getElementById('credito<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').focus();

    		// DATOS DE LA CUENTA PARA INSERTAR
			var id_cuenta          = document.getElementById('id_cuenta_'+id).value;
			var cuenta             = document.getElementById('div_'+tablaBuscar+'__'+id).innerHTML;
			var descripcion_cuenta = document.getElementById('nombre_cuenta_'+id).value;

			var tercero    = (document.getElementById('tercero_'+id).value==0)? '<?php echo $tercero ?>' : document.getElementById('tercero_'+id).value ;
			var id_tercero = (document.getElementById('id_tercero_'+id).value=='')? '<?php echo $tercero ?>' : document.getElementById('id_tercero_'+id).value ;

			document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value          = id_cuenta;
			document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value            = cuenta;
			document.getElementById('descripcion<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value       = descripcion_cuenta;
			document.getElementById('idTablaReferencia<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value = id;

			if (document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value != '' ) {
				//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        		document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('src','img/eliminar.png');
        		document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('title','Eliminar Documento Cruce');
        		document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('onclick'," eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>('<?php echo $cont; ?>')");
			}

			document.getElementById('tercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value   = tercero;
			document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value = id_tercero;
			document.getElementById('credito<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value   = (debito>0)? total_factura_sin_abono : '' ;
			document.getElementById('debito<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').value   = (credito>0)? total_factura_sin_abono : '' ;
			//CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
    		document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('src','img/eliminar.png');
    		document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('title','Eliminar Tercero');
    		document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_<?php echo $cont; ?>').setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>(<?php echo $cont; ?>)");

			Win_Ventana_cuentas_documento_cruce.close();
    		Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close();

		}
	</script>
<?php
} ?>
